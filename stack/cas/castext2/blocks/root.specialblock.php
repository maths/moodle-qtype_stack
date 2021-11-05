<?php
// This file is part of Stateful
//
// Stateful is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Stateful is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Stateful.  If not, see <http://www.gnu.org/licenses/>.
defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../block.interface.php');
require_once(__DIR__ . '/../block.factory.php');
require_once(__DIR__ . '/../CTP_classes.php');
require_once(__DIR__ . '/../utils.php');
require_once(__DIR__ . '/../../../utils.class.php');


require_once(__DIR__ . '/ioblock.specialblock.php');
require_once(__DIR__ . '/raw.specialblock.php');
require_once(__DIR__ . '/stack_translate.specialblock.php');
require_once(__DIR__ . '/demarkdown.block.php');
require_once(__DIR__ . '/demoodle.block.php');

class stack_cas_castext2_special_root extends stack_cas_castext2_block {
    public function compile($format, $options):  ? string{
        $r = '';

        if ($this->is_flat()) {
            $r = 'sconcat(';
        } else {
            $r = '["%root",';
        }
        $items = [];

        foreach ($this->children as $item) {
            $c = $item->compile($format, $options);
            if ($c !== null) {
                $items[] = $c;
            }
        }
        $r .= implode(',', $items);

        if ($this->is_flat()) {
            $r .= ')';
        } else {
            $r .= ']';
        }

        // Essenttialy we now have an expression where anything could happen.
        // We do not want it to affect the surroundings. So.
        $ast    = maxima_parser_utils::parse($r);

        // At this point we want to simplify things, it is entirelly
        // possible that there are sconcats in play with overt number 
        // of arguments and we may need to turn them to reduce-calls
        // to deal with GCL-limits.
        $simplifier = function($node) use ($options) {
            if ($node instanceof MP_FunctionCall) {
                if ($node->name instanceof MP_Identifier && $node->name->value === 'sconcat') {
                    if (count($node->arguments) == 0) {
                        $node->parentnode->replace($node, new MP_String(""));
                        return false;
                    }

                    if (count($node->arguments) == 1 && $node->arguments[0] instanceof MP_String) {
                        $node->parentnode->replace($node, new MP_String($node->arguments[0]->value));
                        return false;
                    }

                    if (count($node->arguments) > 1) {
                        $newargs = castext2_parser_utils::string_list_reduce($node->arguments);
                        if (count($newargs) < count($node->arguments)) {
                            if (count($newargs) === 1 && $newargs[0] instanceof MP_String) {
                                $node->parentnode->replace($node, $newargs[0]);
                                return false;
                            }
                            $node->arguments = $newargs;
                            return false;
                        }

                    }
                    // The GCL thing. Old used lreduce/sconcat but simplode is probably faster.
                    if (count($node->arguments) > 40) {
                        $replacement = new MP_FunctionCall(new MP_Identifier('simplode'),[new MP_List($node->arguments)]);
                        $node->parentnode->replace($node, $replacement);
                        return false;
                    }
                } else if ($node->name instanceof MP_Identifier && $node->name->value === 'castext') {
                    // The very special case of seeing the castext-function inside castext.
                    if (count($node->arguments) !== 1 || !($node->arguments[0] instanceof MP_String)) {
                        throw new stateful_exception('Inline castext()-compiler, wrong argument. Only works with one direct raw string.');
                    }
                    $compiled = castext2_parser_utils::compile($node->arguments[0]->value, $format, $options);
                    $compiled = maxima_parser_utils::parse($compiled);
                    if ($compiled instanceof MP_Root) {
                        $compiled = $compiled->items[0];
                    }
                    if ($compiled instanceof MP_Statement) {
                        $compiled = $compiled->statement;
                    }
                    $node->parentnode->replace($node, $compiled);
                    return false;
                }
            }
            // We may also have simplified everything out.
            if ($node instanceof MP_List && count($node->items) > 0 && 
                $node->items[0] instanceof MP_String && 
                $node->items[0]->value === '%root') {
                // The root nodes represent simple string concateations
                // if the arguments are strings we can simply do
                // the concatenation in advance and if we end with
                // 1 argument we can unwrap the thing.
                $newitems = castext2_parser_utils::string_list_reduce($node->items, true);
                if (count($newitems) < count($node->items)) {
                    if (count($newitems) === 2) {
                        // The second term is something evaluating to
                        // string.
                        $node->parentnode->replace($node, $newitems[1]);
                        return false;
                    }

                    $node->items = $newitems;
                    return false;
                }
            }
            if ($node instanceof MP_List && count($node->items) == 2 && 
                $node->items[0] instanceof MP_String && 
                $node->items[1] instanceof MP_String && 
                $node->items[0]->value === 'demarkdown') {
                // If we have simplified it down to a string and we still have a markdown 
                // postprocessing step we can do that as well and see if we can simplify 
                // further, might lead to full simplify to string and thus allow skipping
                // the whole CAS-evaluation.
                // Unfortunately, this cannot be done if any of the parents does formatting.
                // Mixed formats are not liked and should be banned, but the [[include]]-logic
                // needs to be able to deal with them and PRTs and input2 trickery become simpler
                // if we support this.
                $good = true;
                $same = false;
                $p = $node->parentnode;
                while ($p !== null) {
                    if ($p instanceof MP_List && count($p->items) > 0 && $p->items[0] instanceof MP_String &&
                        ($p->items[0]->value === 'demoodle' || $p->items[0]->value === 'demarkdown' || $p->items[0]->value === 'htmlformat' || $p->items[0]->value === 'jsxgraph')) {
                        $good =false;
                        if ($p->items[0]->value === 'demarkdown') {
                            $same = true;
                        }
                        break;
                    }
                    $p = $p->parentnode;
                }
                if ($good) {
                    $params = [$node->items[0]->value, $node->items[1]->value];
                    $proc = new stack_cas_castext2_demarkdown([]);
                    $node->parentnode->replace($node, new MP_String($proc->postprocess($params)));
                    return false;
                }
                if ($same) {
                    // Same format nested can merge.
                    if ($node->parentnode instanceof MP_List) {
                        for ($i = 1; $i < count($node->items); $i++) {
                            $node->parentnode->insertChild($node->items[$i], $node);
                        }
                        $node->parentnode->removeChild($node);
                    }
                }
            }
            if ($node instanceof MP_List && count($node->items) == 2 && 
                $node->items[0] instanceof MP_String && 
                $node->items[1] instanceof MP_String && 
                $node->items[0]->value === 'demoodle') {
                // Same for Moodle auto-format
                $good = true;
                $same = false;
                $p = $node->parentnode;
                while ($p !== null) {
                    if ($p instanceof MP_List && count($p->items) > 0 && $p->items[0] instanceof MP_String &&
                        ($p->items[0]->value === 'demoodle' || $p->items[0]->value === 'demarkdown' || $p->items[0]->value === 'htmlformat' || $p->items[0]->value === 'jsxgraph')) {
                        $good =false;
                        if ($p->items[0]->value === 'demoodle') {
                            $same = true;
                        }
                        break;
                    }
                    $p = $p->parentnode;
                }
                if ($good) {
                    $params = [$node->items[0]->value, $node->items[1]->value];
                    $proc = new stack_cas_castext2_demoodle([]);
                    $node->parentnode->replace($node, new MP_String($proc->postprocess($params)));
                    return false;
                }
                if ($same) {
                    // Same format nested can merge.
                    if ($node->parentnode instanceof MP_List) {
                        for ($i = 1; $i < count($node->items); $i++) {
                            $node->parentnode->insertChild($node->items[$i], $node);
                        }
                        $node->parentnode->removeChild($node);
                    }
                }
            }
            if ($node instanceof MP_List && count($node->items) == 2 && 
                $node->items[0] instanceof MP_String && 
                $node->items[1] instanceof MP_String && 
                $node->items[0]->value === 'htmlformat') {
                // Same for html-format
                $good = true;
                $same = false;
                $p = $node->parentnode;
                while ($p !== null) {
                    if ($p instanceof MP_List && count($p->items) > 0 && $p->items[0] instanceof MP_String &&
                        ($p->items[0]->value === 'demoodle' || $p->items[0]->value === 'demarkdown' || $p->items[0]->value === 'htmlformat' || $p->items[0]->value === 'jsxgraph')) {
                        if ($p->items[0]->value === 'htmlformat' || $p->items[0]->value === 'jsxgraph') {
                            $same = true;
                        }
                        $good =false;
                        break;
                    }
                    $p = $p->parentnode;
                }
                if ($good) {
                    $node->parentnode->replace($node, $node->items[1]);
                    return false;
                }
                if ($same) {
                    // Same format nested can merge.
                    if ($node->parentnode instanceof MP_List) {
                        for ($i = 1; $i < count($node->items); $i++) {
                            $node->parentnode->insertChild($node->items[$i], $node);
                        }
                        $node->parentnode->removeChild($node);
                    }
                }
            }

            return true;
        };
        while ($ast->callbackRecurse($simplifier) !== true) {};
        $r = $ast->toString(['nosemicolon' => true]);


        $varref = maxima_parser_utils::variable_usage_finder($ast);

        // This may break with GCL as there is a limit for that local
        // call there.
        if (count($varref['write']) > 0) {
            $r = 'block(local(' . implode(',', array_keys($varref['write'])) .
                '),castext_simplify(' . $r . '))';
        }

        return $r;
    }

    public function is_flat() : bool{
        // Now then the problem here is that the flatness depends on the flatness of
        // the blocks contents. If they all generate strings then we are flat but if not...
        $flat = true;

        foreach ($this->children as $child) {
            $flat = $flat && $child->is_flat();
        }

        return $flat;
    }

    /**
     * If this is not a flat block this will be called with the response from CAS and
     * should execute whatever additional logic is needed. Register JavaScript and such
     * things it must then return the content that will take this blocks place.
     */
    public function postprocess(array $params, castext2_processor $processor): string {
        if (count($params) < 2) {
            // Nothing at all.
            return '';
        }
        $r = '';
        for ($i = 1; $i < count($params); $i++) {
            if (is_array($params[$i])) {
                $r .= $processor->process($params[$i][0], $params[$i]);
            } else {
                $r .= $params[$i];
            }
        }

        // This should be handled at a higher level, but as the structure that is postprocessed
        // Still comes through so many routes this has not been cleared.
        // TODO: once everything for this comes through the MaximaParser, make the conversion
        // from its structure to the array this function eats do this.
        $r = str_replace('QMCHAR', '?', $r);

        return $r;
    }

    public function validate_extract_attributes(): array{
        return [];
    }

    // Creates a block from a node.
    // TODO: pick another place for this function.
    public static function make(CTP_Node $node): stack_cas_castext2_block {
        if ($node instanceof CTP_IOBlock) {
            return new stack_cas_castext2_special_ioblock([], [], $node->
                mathmode, $node->channel, $node->variable);
        } else if ($node instanceof CTP_Raw) {
            return new stack_cas_castext2_special_raw([], [], $node->
                mathmode, $node->value);
        } else if ($node instanceof CTP_Root) {
            $children = [];
            foreach ($node->items as $item) {
                $children[] = self::make($item);
            }
            return new stack_cas_castext2_special_root([], $children, $node
                    ->mathmode);
            }
            // What remains are blocks.

            $children = [];
            foreach ($node->contents as $item) {
            $children[] = self::make($item);
        }

        $params = [];
        if ($node->name === 'define') {
            // Define is a very special block in the sense that it can actually have 
            // multiple values for the same key. This due to it being mostly 
            // compatible with keyvals.
            // The parser returns a differently encoded result that needs 
            // to be unpacked here and in the define block itself.
            foreach ($node->parameters as $param) {
                $params[] = ['key' => $param['key'], 'value' => $param['value']->value];
            }
            return castext2_block_factory::make($node->name, $params, $children,
                $node->mathmode);
        }

        // IF-blocks do also have special encoding in the params.
        foreach ($node->parameters as $key => $value) {
            if ($value instanceof CTP_String) {
                $params[$key] = $value->value;
            } else if (is_array($value) && $value[0] instanceof CTP_String) {
                // If conditions.
                $t = [];
                foreach ($value as $item) {
                    $t[] = $item->value;
                }
                $params[$key] = $t;
            } else {
                // If branches
                $params[$key] = $value;
            }
        }
        return castext2_block_factory::make($node->name, $params, $children,
            $node->mathmode);
    }
}
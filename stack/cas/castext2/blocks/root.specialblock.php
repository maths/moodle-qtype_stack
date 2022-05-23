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
    public function compile($format, $options):  ? string {
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

        // Essentially we now have an expression where anything could happen.
        // We do not want it to affect the surroundings. So parse and
        // find the used variables.
        $ast    = maxima_parser_utils::parse($r);

        // At this point we want to simplify things, it is entirely
        // possible that there are sconcats in play with overt number
        // of arguments and we may need to turn them to reduce-calls
        // to deal with GCL-limits.

        $filteroptions = ['601_castext' => $options];
        $pipeline = stack_parsing_rule_factory::get_filter_pipeline(['601_castext',
            '602_castext_simplifier', '680_gcl_sconcat'], $filteroptions, false);

        $errors = [];
        $answernotes = [];
        $ast = $pipeline->filter($ast, $errors, $answernotes, new stack_cas_security());

        if (count($errors) > 0) {
            throw new stack_exception(implode('; ', $errors));
        }

        $r = $ast->toString(['nosemicolon' => true]);

        $varref = maxima_parser_utils::variable_usage_finder($ast);

        // This may break with GCL as there is a limit for that local call there.
        if (count($varref['write']) > 0) {
            $r = 'castext_simplify(block(local(' . implode(',', array_keys($varref['write'])) .
                '),' . $r . '))';
        }

        return $r;
    }

    public function is_flat() : bool {
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

    public function validate_extract_attributes(): array {
        return [];
    }

    // Creates a block from a node.
    // TODO: pick another place for this function.
    public static function make(CTP_Node $node): stack_cas_castext2_block {
        if ($node instanceof CTP_IOBlock) {
            $r = new stack_cas_castext2_special_ioblock([], [], $node->
                mathmode, $node->channel, $node->variable);
            $r->position = $node->position;
            $r->paintformat = $node->paintformat;
            return $r;
        } else if ($node instanceof CTP_Raw) {
            $r = new stack_cas_castext2_special_raw([], [], $node->
                mathmode, $node->value);
            $r->position = $node->position;
            $r->paintformat = $node->paintformat;
            return $r;
        } else if ($node instanceof CTP_Root) {
            $children = [];
            foreach ($node->items as $item) {
                $children[] = self::make($item);
            }
            $r = new stack_cas_castext2_special_root([], $children, $node->mathmode);
            $r->position = $node->position;
            $r->paintformat = $node->paintformat;
            return $r;
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
            $r = castext2_block_factory::make($node->name, $params, $children,
                $node->mathmode);
            $r->position = $node->position;
            $r->paintformat = $node->paintformat;
            return $r;
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
                // If branches.
                $params[$key] = $value;
            }
        }
        $r = castext2_block_factory::make($node->name, $params, $children,
            $node->mathmode);
        $r->position = $node->position;
        $r->paintformat = $node->paintformat;
        return $r;
    }
}

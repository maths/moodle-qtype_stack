<?php
// This file is part of Stack - https://stack.maths.ed.ac.uk
//
// Stack is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Stack is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Stack.  If not, see <http://www.gnu.org/licenses/>.

defined('MOODLE_INTERNAL') || die();
require_once(__DIR__ . '/filter.interface.php');

/**
 * AST filter that extracts static strings from CASText.
 * To be run after the CASText has been simplified.
 */
class stack_ast_filter_610_castext_static_string_extractor implements stack_cas_astfilter_parametric {

    // A reference to the extractor.
    private $extractor = false;

    public function set_filter_parameters(array $parameters) {
        $this->extractor = isset($parameters['static string extractor']) ? $parameters['static string extractor'] : null;
    }

    public function filter(MP_Node $ast, array &$errors, array &$answernotes, stack_cas_security $identifierrules): MP_Node {

        // Simply nothing to do if we have nowhere to place those strings.
        if ($this->extractor === null) {
            return $ast;
        }

        $map = $this->extractor;
        $process = function($node) use(&$map) {
            if ($node instanceof MP_String && mb_strlen($node->value) > 10 &&
                    $node->parentnode instanceof MP_List &&
                    array_search($node, $node->parentnode->items) > 0 ) {
                // Ensure that the list is a CASText2 thing.
                if ($node->parentnode->items[0] instanceof MP_String && (
                    $node->parentnode->items[0]->value === '%root' ||
                    $node->parentnode->items[0]->value === '%cs' ||
                    $node->parentnode->items[0]->value === 'demarkdown' ||
                    $node->parentnode->items[0]->value === 'demoodle' ||
                    ($node->parentnode->items[0]->value === 'jsxgraph' &&
                            array_search($node, $node->parentnode->items) > 1)
                    )) {
                    $node->value = $map->add_to_map($node->value);
                }
            } else if ($node instanceof MP_String && mb_strlen($node->value) > 10 &&
                            isset($node->position['castext']) && $node->position['castext']) {
                if ($node->parentnode instanceof MP_FunctionCall &&
                    $node->parentnode->name instanceof MP_Atom &&
                    $node->parentnode->name->value === '_EC' &&
                    $node->parentnode->arguments[1] == $node) {
                    // Do not touch the error tracing bits, we want to keep those easily visible.
                    return true;
                } else {
                    $node->value = $map->add_to_map($node->value);
                }
            }
            return true;
        };

        // @codingStandardsIgnoreStart
        $ast->callbackRecurse($process);
        // @codingStandardsIgnoreEnd

        return $ast;
    }
}

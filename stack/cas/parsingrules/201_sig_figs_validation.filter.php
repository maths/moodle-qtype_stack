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
require_once(__DIR__ . '/../../utils.class.php');

/**
 * AST filter that examines the significant figures of the leftmost
 * integer or float.
 * Can be tuned to check for desired number of digits as well as
 * the so called strict form.
 */
class stack_ast_filter_201_sig_figs_validation implements stack_cas_astfilter_parametric {
    // Min and max are integer or null, null or values less than
    // 1 signify that there is no limit in the given direction.
    private $min = 3;
    private $max = 3;
    private $strict = false;

    public function set_filter_parameters(array $parameters) {
        $this->min = $parameters['min'];
        $this->max = $parameters['max'];
        $this->strict = $parameters['strict'];
    }

    public function filter(MP_Node $ast, array &$errors, array &$answernotes, stack_cas_security $identifierrules): MP_Node {
        $root = $ast;
        if ($root instanceof MP_Root) {
            $root = $root->items[0];
        }
        if ($root instanceof MP_Statement) {
            $root = $root->statement;
        }
        $node = self::get_leftmost_int_or_float($ast);
        if ($node === null) {
            $root->position['invalid'] = true;
            if ($this->min !== null && $this->min > 0) {
                $errors[] = stack_string('numericalinputminsf', $this->min);
            } else {
                $errors[] = stack_string('numericalinputmaxsf', $this->max);
            }
        } else {
            // Hmm. where did stack_utils::decimal_places go?
            // Well this is simpler to do like this.
            $raw = strtolower($node->toString());
            $raw = ltrim($raw, '-'); // Just in case.
            $raw = ltrim($raw, '+');
            $raw = explode('e', $raw)[0];

            $pre = explode('.', $raw)[0];
            $post = '';
            if (strpos($raw, '.') !== false) {
                $post = explode('.', $raw)[1];
            }
            $min = null;
            $max = null;

            if (ltrim($pre, '0') === '') {
                if (ltrim($post, '0') === '') {
                    // For example 0.000.
                    $min = 1;
                    $max = 1 + strlen($post);
                } else {
                    // For example 0.032.
                    $max = strlen(ltrim($post, '0'));
                    $min = $max;
                }
            } else if ($post !== '') {
                // For example  12.0230.
                $max = strlen(ltrim($pre, '0') . $post);
                $min = $max;
            } else {
                // For example 110.
                $max = strlen(ltrim($pre, '0'));
                $min = strlen(trim($pre, '0'));
            }

            if ($this->min !== null && $this->min > 0) {
                if ($max < $this->min) {
                    $node->position['invalid'] = true;
                    $errors[] = stack_string('numericalinputminsf', $this->min);
                } else if ($this->strict && $min < $this->min) {
                    $node->position['invalid'] = true;
                    $errors[] = stack_string('numericalinputminsf', $this->min);
                }
            }
            if ($this->max !== null && $this->max > 0) {
                if ($min > $this->max) {
                    $node->position['invalid'] = true;
                    $errors[] = stack_string('numericalinputmaxsf', $this->max);
                }
            }
        }
        return $ast;
    }

    public static function get_leftmost_int_or_float(MP_Node $tree) {
        $nodes = [];
        $search = function($node) use(&$nodes) {
            if ($node instanceof MP_Float || $node instanceof MP_Integer) {
                $nodes[] = $node;
            }
            return true;
        };
        $tree->callbackRecurse($search);

        if (count($nodes) === 1) {
            return $nodes[0];
        }
        if (count($nodes) < 1) {
            return null;
        }
        $leftmost = 9000;
        $filterednodes = [];
        foreach ($nodes as $node) {
            if (isset($node->position['start'])) {
                if ($node->position['start'] < $leftmost) {
                    $leftmost = $node->position['start'];
                    $filterednodes[] = $node;
                }
            } else {
                $filterednodes[] = $node;
            }
        }
        $nodes = $filterednodes;
        if (count($nodes) === 1) {
            return $nodes[0];
        }

        // We may still have more than one node, so lets filter again.
        $filterednodes = [];
        foreach ($nodes as $node) {
            if (isset($node->position['start'])) {
                if ($node->position['start'] < $leftmost) {
                    $leftmost = $node->position['start'];
                    $filterednodes[] = $node;
                }
            } else {
                $filterednodes[] = $node;
            }
        }
        $nodes = $filterednodes;
        if (count($nodes) === 1) {
            return $nodes[0];
        }

        // Ok we have acted on a tree that has no position data for
        // the relevant nodes, so we need to reverse engineer the
        // positions.
        $raw = $tree->toString();
        $leftmost = 9000;
        $thenode = null;
        foreach ($nodes as $node) {
            $i = strpos($raw, $node->toString());
            if ($i < $leftmost) {
                $thenode = $node;
                $leftmost = $i;
            }
        }
        return $thenode;
    }
}


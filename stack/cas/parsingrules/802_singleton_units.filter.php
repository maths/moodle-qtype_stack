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
require_once(__DIR__ . '/801_singleton_numeric.filter.php');

/**
 * AST filter that checks that the AST represents a singleton value
 * that consists of a numeric part and a part that describes a unit
 * or combination of units.
 *
 * Essenttialy, this filter ensures that the answer does not include
 * any of the following operations:
 *  - Non prefix versions of `+` and `-`.
 *  - Powers of numeric values other than integer powers of ten.
 *  - Sets, lists or any flow control operations
 *  - Function calls
 *
 * It may also be used to detect non 'unit' identifiers in
 * the expression and will automatically deal with the 1e-310 issue.
 *
 */
class stack_ast_filter_802_singleton_units implements stack_cas_astfilter_parametric {

    // Do we only accept the default units or do we allow additional
    // variables/constants? Use the forbidden words to limit more accurately.
    // That limitation will happen through the security-filter.
    private $allowvariables = false;

    // Allow the expression to contain constants like `pi`.
    private $allowconstants = false;

    // Convert all floats to powers.
    private $floattopower = false;

    // Invalid if no known unit found. This means that one can allow
    // this to function even if the "unit" is not one of the official ones
    // it will still require an identifier though.
    private $mandatoryunit = true;


    public function set_filter_parameters(array $parameters) {
        if (isset($parameters['allowvariables'])) {
            $this->allowvariables = $parameters['allowvariables'];
        }
        if (isset($parameters['allowconstants'])) {
            $this->allowconstants = $parameters['allowconstants'];
        }
        if (isset($parameters['floattopower'])) {
            $this->floattopower = $parameters['floattopower'];
        }
        if (isset($parameters['mandatoryunit'])) {
            $this->mandatoryunit = $parameters['mandatoryunit'];
        }
    }

    public function filter(MP_Node $ast, array &$errors, array &$answernotes, stack_cas_security $identifierrules): MP_Node {

        $ops = [];
        $ids = [];
        $misc = [];
        $floats = [];

        $collect = function($node) use (&$ids, &$ops, &$misc, &$floats) {
            if ($node instanceof MP_Operation) {
                $ops[] = $node;
            } else if ($node instanceof MP_Identifier && $node->is_variable_name()) {
                $ids[] = $node;
            } else if ($node instanceof MP_PrefixOp && ($node->op === '-' || $node->op === '+')) {
                // Ignore.
                $null = true;
            } else if ($node instanceof MP_Float) {
                $floats[] = $node;
            } else if ($node instanceof MP_Integer) {
                // Ignore.
                $null = true;
            } else {
                $misc[] = $node;
            }
            return true;
        };

        $ast->callbackRecurse($collect, false);

        // First check the identifiers.
        $formfail = false;
        $hasunits = false;
        $sec = $identifierrules;
        if (!$sec->get_units()) {
            $sec = clone $identifierrules;
            $sec->set_units(true);
        }
        $vars = [];
        $constants = [];
        foreach ($ids as $id) {
            if ($sec->has_feature($id->value, 'unit')) {
                $hasunits = true;
            } else if ($sec->has_feature($id->value, 'constant')) {
                $constants[$id->value] = $id->value;
                if (!$this->allowconstants) {
                    $id->position['invalid'] = true;
                }
            } else {
                $vars[$id->value] = $id->value;
                if (!$this->allowvariables) {
                    $id->position['invalid'] = true;
                }
            }
        }
        if (!$this->allowvariables || !$this->allowconstants) {
            $keys = [];
            if (!$this->allowvariables) {
                $keys = $keys + array_keys($vars);
            }
            if (!$this->allowconstants) {
                $keys = $keys + array_keys($constants);
            }
            sort($keys);
            if (count($keys) > 0) {
                $errors[] = stack_string('Illegal_identifiers_in_units', implode(', ', $keys));
            }
        }
        if ((!$hasunits && $this->mandatoryunit) ||
            (!$hasunits && !$this->mandatoryunit && (count($vars) + count($constants)) === 0)) {
            $node = $ast;
            if ($node instanceof MP_Root) {
                $node = $node->items[0];
            }
            if ($node instanceof MP_Statement) {
                $node = $node->statement;
            }
            $node->position['invalid'] = true;
            $formfail = true;
        }

        // Check the miscs.
        foreach ($misc as $node) {
            if ($node instanceof MP_Set || $node instanceof MP_List) {
                $node->position['invalid'] = true;
                $formfail = true;
            } else if ($node instanceof MP_If || $node instanceof MP_Loop || $node instanceof MP_LoopBit) {
                $node->position['invalid'] = true;
                $formfail = true;
            } else if ($node instanceof MP_PrefixOp || $node instanceof MP_PostfixOp) {
                $node->position['invalid'] = true;
                $errors[] = stack_string('Illegal_illegal_operation_in_units', $node->op);
            } else if ($node instanceof MP_FunctionCall) {
                $node->position['invalid'] = true;
                $formfail = true;
            } else if ($node instanceof MP_String) {
                $node->position['invalid'] = true;
                $formfail = true;
            }
        }

        // Check ops to ensure singleton value.
        $opfail = false;
        foreach ($ops as $op) {
            if ($op->op === '+' || $op->op === '-') {
                $op->position['invalid'] = true;
                $opfail = true;
            } else if ($op->op === '/' || $op->op === '*') {
                // Fine.
                $null = true;
            } else if ($op->op === '^' || $op->op === '**') {
                if ($op->lhs instanceof MP_Integer && $op->lhs->value === 10) {
                    $rhs = $op->rhs;
                    if ($rhs instanceof MP_PrefixOp) {
                        $rhs = $rhs->rhs;
                    }
                    if (!($rhs instanceof MP_Integer)) {
                        $op->position['invalid'] = true;
                        $errors[] = stack_string('Illegal_illegal_power_of_ten_in_units', $node->op);
                    }
                } else if ($op->lhs instanceof MP_Integer || $op->lhs instanceof MP_Float) {
                    $op->position['invalid'] = true;
                    $opfail = true;
                }
            } else {
                $op->position['invalid'] = true;
                $opfail = true;
            }
        }
        if ($opfail || $formfail) {
            $errors[] = stack_string('Illegal_input_form_units');
        }

        // Check floats and fix if need be.
        foreach ($floats as $node) {
            if ((strpos($node->toString(), 'E') !== false || $this->floattopower) && $node->raw !== null) {
                $p = 0;
                $parts = explode('E', $node->toString());
                $p = intval($parts[1]);
                if ($p < 0) {
                    $p = $p - strlen($parts[0]);
                } else {
                    $p = $p + strlen($parts[0]);
                }

                if (($p >= 303 || $p <= -303) || $this->floattopower) {
                    $other = new stack_ast_filter_801_singleton_numeric();
                    $replacement = $other->float_to_power($node);
                    if ($node->parentnode instanceof MP_PrefixOp && $replacement instanceof MP_Operation) {
                        // Move the prefix to its natural place.
                        $replacement->replace($replacement->lhs, new MP_PrefixOp($node->parentnode->op, $replacement->lhs));
                        $node->parentnode->parentnode->replace($node->parentnode, $replacement);
                    } else {
                        $node->parentnode->replace($node, $replacement);
                    }
                }
            }
        }

        return $ast;
    }
}
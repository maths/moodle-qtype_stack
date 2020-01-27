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
require_once(__DIR__ . '/../../maximaparser/utils.php');

/**
 * AST filter that checks that the AST represents a singleton value
 * that is purely numeric. It can also be used to turn that value
 * between certain representations.
 *
 * Note that conversion requires that you accept the form from which
 * the conversion happens.
 */
class stack_ast_filter_801_singleton_numeric implements stack_cas_astfilter_parametric {

    // These two control the aceptable raw data types as well as
    // the mantissa allowed in the third option.
    private $integer = true;
    private $float = true;

    // @codingStandardsIgnoreStart
    // Accepts 0.123*10^45 or 123*10^45.
    // Will not accept 0.123e4*10^5 as that is mixed usage.
    // @codingStandardsIgnoreEnd
    private $power = true;

    // Convert from power form to float or vice versa
    // Will not convert raw integers to floats.
    private $convert = 'none'; // Other options are 'to float', 'to power'.

    public function set_filter_parameters(array $parameters) {
        $this->integer = $parameters['integer'];
        $this->float = $parameters['float'];
        $this->power = $parameters['power'];
        $this->convert = $parameters['convert'];
    }

    public function filter(MP_Node $ast, array &$errors, array &$answernotes, stack_cas_security $identifierrules): MP_Node {

        // First unpack the $ast.
        $node = $ast;
        if ($node instanceof MP_Root) {
            $node = $node->items[0];
        }
        if ($node instanceof MP_Statement) {
            $node = $node->statement;
        }
        if ($node instanceof MP_PrefixOp && ($node->op === '-' || $node->op === '+')) {
            $node = $node->rhs;
        }

        // Make sure that we have the full parentnode linking.
        // Mainly relevant for test cases.
        $ast->callbackRecurse(null);

        // Trivial exits.
        if ($node instanceof MP_Float) {
            if ($this->float) {
                // Turn floats that are to small or large to powers of ten.
                $p = 0;
                if (strpos($node->toString(), 'E') !== false) {
                    $p = intval(explode('E', $node->toString())[1]);
                }

                if ($p < 0) {
                    $p = $p - strlen(explode('E', $node->toString())[0]);
                } else {
                    $p = $p + strlen(explode('E', $node->toString())[0]);
                }
                if ($this->convert !== 'to power' && ($p < 303 && $p > -303)) {
                    return $ast;
                } else {
                    $replacement = $this->float_to_power($node);
                    if ($node->parentnode instanceof MP_PrefixOp && $replacement instanceof MP_Operation) {
                        // Move the prefix to its natural place.
                        $replacement->replace($replacement->lhs, new MP_PrefixOp($node->parentnode->op, $replacement->lhs));
                        $node->parentnode->parentnode->replace($node->parentnode, $replacement);
                    } else {
                        $node->parentnode->replace($node, $replacement);
                    }
                    return $ast;
                }
            } else {
                $node->position['invalid'] = true;
                $answernotes[] = 'Illegal_floats';
                $errors[] = stack_string('Illegal_singleton_floats', ['forms' => $this->acceptable_forms()]);
                return $ast;
            }
        }
        if ($node instanceof MP_Integer) {
            if (!$this->integer) {
                $node->position['invalid'] = true;
                $answernotes[] = 'Illegal_integer';
                $errors[] = stack_string('Illegal_singleton_integer', ['forms' => $this->acceptable_forms()]);
            }
            return $ast;
        }

        $usage = maxima_parser_utils::variable_usage_finder($ast);
        if ((isset($usage['read']) && count($usage['read']) > 0) ||
            (isset($usage['write']) && count($usage['write']) > 0) ||
            (isset($usage['calls']) && count($usage['calls']) > 0)) {
            $node->position['invalid'] = true;
            $answernotes[] = 'Illegal_form';
            $errors[] = stack_string('Illegal_singleton_power', ['forms' => $this->acceptable_forms()]);
            return $ast;
        }

        // The non trivial bit of identifying a power form representation.
        // 10^1 is also one, i.e. matissa being 1 and omitted is to be noted.
        $m = null;
        $p = null;
        $sgn = false;
        if ($node instanceof MP_Operation && $node->op === '^') {
            if ($node->lhs instanceof MP_Integer && $node->lhs->value === 10) {
                $m = '1';
                if ($node->rhs instanceof MP_Integer) {
                    $p = $node->rhs->value;
                } else if (($node->rhs instanceof MP_PrefixOp) &&
                        ($node->rhs->op === '-' || $node->rhs->op === '+') &&
                        ($node->rhs->rhs instanceof MP_Integer)) {
                    $p = $node->rhs->rhs->value;
                    if ($node->rhs->op === '-') {
                        $p = -$p;
                    }
                } else {
                    $node->position['invalid'] = true;
                    $answernotes[] = 'Illegal_power';
                    $errors[] = stack_string('Illegal_singleton_power', ['forms' => $this->acceptable_forms()]);
                    return $ast;
                }
            } else {
                $node->position['invalid'] = true;
                $answernotes[] = 'Illegal_power';
                $errors[] = stack_string('Illegal_singleton_power', ['forms' => $this->acceptable_forms()]);
                return $ast;
            }
        }
        if ($node instanceof MP_Operation && $node->op === '*' && $node->rhs instanceof MP_Operation && $node->rhs->op === '^') {
            // The power first.
            if ($node->rhs->lhs instanceof MP_Integer && $node->rhs->lhs->value === 10) {
                if ($node->rhs->rhs instanceof MP_Integer) {
                    $p = $node->rhs->rhs->value;
                } else if ($node->rhs->rhs instanceof MP_PrefixOp &&
                    ($node->rhs->rhs->op === '-' || $node->rhs->rhs->op === '+') &&
                    $node->rhs->rhs->rhs instanceof MP_Integer) {
                    $p = $node->rhs->rhs->rhs->value;
                    if ($node->rhs->rhs->op === '-') {
                        $p = -$p;
                    }
                } else {
                    $node->position['invalid'] = true;
                    $answernotes[] = 'Illegal_power';
                    $errors[] = stack_string('Illegal_singleton_power', ['forms' => $this->acceptable_forms()]);
                    return $ast;
                }
            } else {
                $node->position['invalid'] = true;
                $answernotes[] = 'Illegal_power';
                $errors[] = stack_string('Illegal_singleton_power', ['forms' => $this->acceptable_forms()]);
                return $ast;
            }
            // Then the mantissa.
            if ($node->lhs instanceof MP_Integer) {
                $m = $node->lhs->raw;
                if (!$this->integer) {
                    $node->position['invalid'] = true;
                    $answernotes[] = 'Illegal_integer';
                    $errors[] = stack_string('Illegal_singleton_integer', ['forms' => $this->acceptable_forms()]);
                    return $ast;
                }
            } else if ($node->lhs instanceof MP_Float) {
                $m = $node->lhs->raw;
                if (!$this->float) {
                    $node->position['invalid'] = true;
                    $answernotes[] = 'Illegal_floats';
                    $errors[] = stack_string('Illegal_singleton_floats', ['forms' => $this->acceptable_forms()]);
                    return $ast;
                }
            } else if ($node->lhs instanceof MP_PrefixOp && ($node->lhs->op === '-' || $node->lhs->op === '+')) {
                if ($node->lhs->op === '-') {
                    $sgn = '-';
                }
                if ($node->lhs->rhs instanceof MP_Integer) {
                    $m = $node->lhs->rhs->raw;
                    if (!$this->integer) {
                        $node->position['invalid'] = true;
                        $answernotes[] = 'Illegal_integer';
                        $errors[] = stack_string('Illegal_singleton_integer', ['forms' => $this->acceptable_forms()]);
                        return $ast;
                    }
                } else if ($node->lhs->rhs instanceof MP_Float) {
                    $m = $node->lhs->rhs->raw;
                    if (!$this->float) {
                        $node->position['invalid'] = true;
                        $answernotes[] = 'Illegal_floats';
                        $errors[] = stack_string('Illegal_singleton_floats', ['forms' => $this->acceptable_forms()]);
                        return $ast;
                    }
                } else {
                    $node->position['invalid'] = true;
                    $answernotes[] = 'Illegal_power';
                    $errors[] = stack_string('Illegal_singleton_power', ['forms' => $this->acceptable_forms()]);
                    return $ast;
                }
            }
        }
        // Ok so if we did not match before this or fail before this
        // then we should have the $m and $p and even $sgn if needed.
        // If conversion toward floats is needed we can do that
        // and we can check the bad form 1e23*10^45.
        if (stripos($m, 'e') !== false || !$this->power) {
            // Could have a separate error.
            $node->position['invalid'] = true;
            $answernotes[] = 'Illegal_power';
            $errors[] = stack_string('Illegal_singleton_power', ['forms' => $this->acceptable_forms()]);
            return $ast;
        }
        // Only convert to float if safe to do so.
        if ($this->convert === 'to float' && (($p - strlen($m)) > -303 && ($p + strlen($m)) < 303)) {
            $replacement = new MP_Float(floatval($m . 'e' . $p), $m . 'e' . $p);
            if ($sgn === '-') {
                $replacement = new MP_PrefixOp('-', $replacement);
            }
            $node->parentnode->replace($node, $replacement);
            return $ast;
        }

        // If we are still here and either part is null we failed.
        if ($m === null || $p === null) {
            $node->position['invalid'] = true;
            $answernotes[] = 'Illegal_power';
            $errors[] = stack_string('Illegal_singleton_power', ['forms' => $this->acceptable_forms()]);
        }

        return $ast;
    }

    private function acceptable_forms(): string {
        $r = [];
        if ($this->integer) {
            $r[] = '12345';
            $r[] = '-12345';
        }
        if ($this->float) {
            $r[] = '-1.2345';
            $r[] = '1.2E45';
            $r[] = '1.2e45';
        }
        if ($this->power) {
            if ($this->integer) {
                $r[] = '123*10^45';
            }
            if ($this->float) {
                $r[] = '1.23*10^-45';
            }
        }
        return implode(', ', $r);
    }

    public function float_to_power(MP_Float $float): MP_Node {
        $raw = strtolower($float->raw);
        $p = 0;
        if (strpos($raw, 'e') !== false) {
            $parts = explode('e', $raw);
            $raw = $parts[0];
            $p = intval($parts[1]);
        }
        if (strpos($raw, '.') !== false) {
            $parts = explode('.', $raw);
            $raw = $parts[0] . $parts[1];
            $p = $p - strlen($parts[1]);
        }
        $raw = ltrim($raw, '0');
        if ($raw === '') {
            $p = 0;
        }
        $replacement = new MP_Integer(intval($raw), $raw);
        if ($p > 0) {
            $ten = new MP_Integer(10, '10');
            $p = new MP_Integer($p, '' . $p);
            $replacement = new MP_Operation('*', $replacement, new MP_Operation('^', $ten, $p));
        } else if ($p < 0) {
            $ten = new MP_Integer(10, '10');
            $p = new MP_Integer(-$p, '' . (-$p));
            $replacement = new MP_Operation('*', $replacement, new MP_Operation('^', $ten, new MP_PrefixOp('-', $p)));
        }
        return $replacement;
    }

}
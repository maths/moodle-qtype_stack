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
 * AST filter that spots one inconvenient parser missconception dealing
 * with floats and the matrix multiplication operator.
 */
class stack_ast_filter_003_no_dot_dot implements stack_cas_astfilter {

    public function filter(MP_Node $ast, array &$errors, array &$answernotes, stack_cas_security $identifierrules): MP_Node {

        $process = function($node) use (&$valid, &$errors, &$answernotes) {
            if ($node instanceof MP_Operation && $node->op === '.' && (
                $node->lhs instanceof MP_Float ||
                $node->rhs instanceof MP_Float)) {
                // In general one can write '0.1 . 0.1' but one
                // cannot write '0.1.0.1' while we could check
                // for the original representation and ensure that
                // there are spaces correctly there it's a special
                // case that we do not want to deal with as
                // the students will not be able to deal with it
                // anyway. So lets forbid all matrix
                // multiplications that have floats as scalars.
                // also deal with the extra special '1..1'.
                $node->position['invalid'] = true;
                if (($node->rhs instanceof MP_Float &&
                    $node->rhs->raw !== null &&
                    substr($node->rhs->raw, 0, 1) === '.') ||
                    ($node->lhs instanceof MP_Float &&
                    $node->lhs->raw !== null &&
                    substr($node->lhs->raw, -1) === '.')) {
                    $a = array();
                    $a['cmd']  = stack_maxima_format_casstring('..');
                    if (array_search(stack_string('stackCas_spuriousop', $a), $errors) === false) {
                        $errors[] = stack_string('stackCas_spuriousop', $a);
                    }
                    if (array_search('spuriousop', $answernotes) === false) {
                        $answernotes[] = 'spuriousop';
                    }
                } else if (($node->rhs instanceof MP_Operation &&
                            $node->rhs->lhs instanceof MP_Float &&
                            $node->rhs->lhs->raw !== null &&
                            substr($node->rhs->lhs->raw, 0, 1) === '.') ||
                            ($node->lhs instanceof MP_Operation &&
                            $node->lhs->rhs instanceof MP_Float &&
                            $node->lhs->rhs->raw !== null &&
                            substr($node->lhs->rhs->raw, -1) === '.')) {
                    $a = array();
                    $a['cmd']  = stack_maxima_format_casstring('..');
                    if (array_search(stack_string('stackCas_spuriousop', $a), $errors) === false) {
                        $errors[] = stack_string('stackCas_spuriousop', $a);
                    }
                    if (array_search('spuriousop', $answernotes) === false) {
                        $answernotes[] = 'spuriousop';
                    }
                } else {
                    if (!$node->is_invalid()) {
                        // No need to warn about this if we are already invalid due to whatever reason.
                        $answernotes[] = 'MatrixMultWithFloat';
                        $errors[] = 'Due to syntactical reasons matrix multiplication "." with scalar floats is ' .
                                'forbidden, use normal multiplication "*" instead for the same result.';
                    }
                }
            }

            return true;
        };

        $ast->callbackRecurse($process, true);
        return $ast;
    }
}
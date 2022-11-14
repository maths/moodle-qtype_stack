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
require_once(__DIR__ . '/../../../utils.class.php');

/**
 * The commonstring block is used to ouput string templates that
 * come from the localised strings. It requires that one defined
 * the key of the template and allows one to declare named parameters
 * to be placed into it. When declaring named parameters one can prefix
 * the parameter name with one of these strings:
 *
 *   "" or no prefix will just act like {@...@} but will follow
 *      the local simplification setting
 *   "nosimp_" will do normal {@...@} rendering but without
 *      simplification.
 *
 *   "raw_" will cause the value to be pushed through `string()`
 *      like in the use of {#...#} with the local simplification.
 *
 *   "nosimp_raw_" will cause the value to be pushed through `string()`
 *      like in the use of {#...#} without simplification.
 *
 */
class stack_cas_castext2_commonstring extends stack_cas_castext2_block {

    public function compile($format, $options): ?string {
        // The user should use this block's full name "commonstring" but
        // as this is a common block and chars take room we tend to use a shorter
        // one internally "%cs", note that the processors need to know of this.
        if (count($this->params) == 1) {
            return '["%cs",' . stack_utils::php_string_to_maxima_string($this->params['key']) . ']';
        } else {
            $r = ['["%cs"', stack_utils::php_string_to_maxima_string($this->params['key'])];

            $epos = $options['context'] . '/' . $this->position['start'] . '-' . $this->position['end'];
            $epos = stack_utils::php_string_to_maxima_string($epos);

            foreach ($this->params as $key => $value) {
                if ($key !== 'key') {
                    $ev = stack_ast_container::make_from_teacher_source($value);
                    $ev = $ev->get_evaluationform();
                    $ev = "_EC(errcatch(_ct2_tmp:$ev),$epos)";

                    if (strpos($key, 'nosimp_raw_') === 0) {
                        $r[] = stack_utils::php_string_to_maxima_string(mb_substr($key, 11));
                        $r[] = 'block([_ct2_tmp,_ct2_simp],_ct2_simp:simp,simp:false,' . $ev .
                             ',_ct2_tmp:string(_ct2_tmp),simp:_ct2_simp,_ct2_tmp)';
                    } else if (strpos($key, 'nosimp_') === 0) {
                        $r[] = stack_utils::php_string_to_maxima_string(mb_substr($key, 7));
                        $r[] = 'block([_ct2_tmp,_ct2_simp],_ct2_simp:simp,simp:false,' . $ev .
                            ',simp:false,_ct2_tmp:ct2_latex(_ct2_tmp,"i",false),simp:_ct2_simp,_ct2_tmp)';
                    } else if (strpos($key, 'raw_') === 0) {
                        $r[] = stack_utils::php_string_to_maxima_string(mb_substr($key, 4));
                        // If prefixed by raw output as {#...#} would do.
                        $r[] = 'block([_ct2_tmp],' . $ev . ',string(_ct2_tmp))';
                    } else {
                        // By default assume the value is to be handled like {@...@} would handle it.
                        $r[] = stack_utils::php_string_to_maxima_string($key);
                        $r[] = 'block([_ct2_tmp,_ct2_simp],_ct2_simp:simp,' . $ev .
                            ',simp:false,_ct2_tmp:ct2_latex(_ct2_tmp,"i",_ct2_simp),simp:_ct2_simp,_ct2_tmp)';
                    }
                }
            }

            $r[count($r) - 1] .= ']';
            return implode(',', $r);
        }
    }

    public function is_flat(): bool {
        return false;
    }

    public function validate_extract_attributes(): array {
        $r = array();
        foreach ($this->params as $key => $value) {
            $r[] = stack_ast_container_silent::make_from_teacher_source(
                $value, 'ct2:commonstring', new stack_cas_security());
        }
        return $r;

    }

    public function postprocess(array $params, castext2_processor $processor): string {
        if (count($params) === 2) {
            return stack_string($params[1]);
        }
        $args = [];
        for ($i = 2; $i < count($params); $i += 2) {
            $val = '';
            if (is_array($params[$i + 1])) {
                $val = $processor->process($params[$i + 1][0], $params[$i + 1]);
            } else {
                $val = $params[$i + 1];
            }

            $args[$params[$i]] = $val;
        }

        return stack_string($params[1], $args);
    }


    public function validate(&$errors=[], $options=[]): bool {
        if (!array_key_exists('key', $this->params)) {
            $errors[] = new $options['errclass']('The commonstring block must always have a key for the string template.',
                $options['context'] . '/' . $this->position['start'] . '-' . $this->position['end']);
            return false;
        }

        return true;
    }
}

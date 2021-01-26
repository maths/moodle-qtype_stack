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


require_once(__DIR__ . '/raw.block.php');
require_once(__DIR__ . '/../../ast.container.class.php');

class stack_cas_castext2_latex extends stack_cas_castext2_raw {

    public function compile($format, $options): ?string {
        // Convert possible simplification flags.
        $ev = stack_ast_container::make_from_teacher_source($this->content);
        $ev = $ev->get_evaluationform();

        // If the author enforces simplification on the content we need 
        // to not simplify when we reuse that content.
        // Also we only evaluate it only once.

        // So we need to know if there is simplification in play
        // from the given expression.

        $forcesimp = mb_strpos($ev, ',simp=true') !== false;
        $disablesimp = mb_strpos($ev, ',simp=false') !== false;

        $r = 'block([_ct2_tmp],_ct2_tmp:' . $ev . ',';
        if ($forcesimp) {
            $r = 'block([_ct2_tmp,_ct2_simp],_ct2_simp:simp,simp:true,_ct2_tmp:' . $ev . ',';
        } else if ($disablesimp) {
            $r = 'block([_ct2_tmp,_ct2_simp],_ct2_simp:simp,simp:false,_ct2_tmp:' . $ev . ',';
        }
        $mdsuffix = ''; // Special parameter for "smlt" to do Markdown escaping.
        // So if it is a string then we print it out as is. But in math-mode add the
        // customary {wrapping}, just in case. If markdown is in play we escape it.
        if ($format === castext2_parser_utils::MDFORMAT) {
            $mdsuffix = ',"1"';
            if ($this->mathmode) {
                $r .= 'if stringp(_ct2_tmp) then sconcat("\\{",str_to_md(_ct2_tmp),"\\}") else ';
            } else {
                $r .= 'if stringp(_ct2_tmp) then str_to_md(_ct2_tmp) else ';
            }
        } else {
            if ($this->mathmode) {
                $r .= 'if stringp(_ct2_tmp) then sconcat("{",_ct2_tmp,"}") else ';
            } else {
                $r .= 'if stringp(_ct2_tmp) then _ct2_tmp else ';
            }
        }

        // Otherwise we output different things depending on whether we are 
        // in mathmode or not.
        if ($this->mathmode) {
            if ($forcesimp || $disablesimp) {
                $r .= '(simp:false,_ct2_tmp:stack_disp(_ct2_tmp, "{"),simp:_ct2_simp,["smlt",_ct2_tmp'. $mdsuffix .'])';
            } else {
                $r .= '["smlt",stack_disp(_ct2_tmp, "{")'. $mdsuffix .']';
            }
        } else {
            if ($forcesimp || $disablesimp) {
                $r .= '(simp:false,_ct2_tmp:stack_disp(_ct2_tmp, "i{"),simp:_ct2_simp,["smlt",_ct2_tmp'. $mdsuffix .'])';
            } else {
                $r .= '["smlt",stack_disp(_ct2_tmp, "i{")'. $mdsuffix .']';
            }
        }

        $r .= ')';
        return $r;
    }

    public function is_flat(): bool {
        return false;
    }
}
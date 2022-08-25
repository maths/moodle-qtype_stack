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
        $forcesimp = (mb_strpos($ev, ',simp=true') !== false) || (mb_strpos($ev, ',simp = true') !== false);
        $disablesimp = (mb_strpos($ev, ',simp=false') !== false) || (mb_strpos($ev, ',simp = false') !== false);

        $mode = '""';
        if ($format === castext2_parser_utils::MDFORMAT) {
            if ($this->mathmode) {
                $mode = '"m"';
            } else {
                $mode = '"im"';
            }
        } else {
            if ($this->mathmode) {
                $mode = '""';
            } else {
                $mode = '"i"';
            }
        }

        $r = 'block([_ct2_tmp,_ct2_simp],_ct2_simp:simp,';
        $r .= '_ct2_tmp:' . stack_utils::php_string_to_maxima_string($this->content) . ',';
        $simp = ',_ct2_simp';
        if ($forcesimp) {
            $r .= 'simp:true,';
            $simp = ',true';
        } else if ($disablesimp) {
            $r .= 'simp:false,';
            $simp = ',false';
        }
        $epos = $options['context'] . '/' . $this->position['start'] . '-' . $this->position['end'];
        $epos = stack_utils::php_string_to_maxima_string($epos);
        $r .= '_EC(errcatch(_ct2_tmp:' . $ev . '),' . $epos . '),';

        $r .= 'simp:false,_ct2_tmp:ct2_latex(_ct2_tmp,'. $mode . $simp .'),simp:_ct2_simp,_ct2_tmp)';

        return $r;
    }

    public function is_flat(): bool {
        return false;
    }
}

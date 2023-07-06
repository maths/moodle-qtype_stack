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

    public function compile($format, $options): ?MP_Node {
        // Convert possible simplification flags.
        $ev = stack_ast_container::make_from_teacher_source($this->content);
        $ast = $ev->get_commentles_primary_statement();
        $simps = $ev->identify_simplification_modifications();

        // If the author enforces simplification on the content we need
        // to not simplify when we reuse that content.
        // Also we only evaluate it only once.

        // So we need to know if there is simplification in play
        // from the given expression.
        $forcesimp = $simps['last-seen'] === true;
        $disablesimp = $simps['last-seen'] === false;

        $simp = new MP_Identifier('_ct2_simp');
        if ($forcesimp) {
            $simp = new MP_Boolean(true);
        } else if ($disablesimp) {
            $simp = new MP_Boolean(false);
        }

        $mode = '';
        if ($format === castext2_parser_utils::MDFORMAT) {
            if ($this->mathmode) {
                $mode = 'm';
            } else {
                $mode = 'im';
            }
        } else {
            if ($this->mathmode) {
                $mode = '';
            } else {
                $mode = 'i';
            }
        }
        $mode = new MP_String($mode);

        $r = new MP_FunctionCall(new MP_Identifier('block'), [
            new MP_List([new MP_Identifier('_ct2_tmp'), new MP_Identifier('_ct2_simp')]),
            new MP_Operation(':', new MP_Identifier('_ct2_simp'), new MP_Identifier('simp')),
            new MP_Operation(':', new MP_Identifier('_ct2_tmp'), new MP_String($this->content))
        ]);

        if ($forcesimp) {
            $r->arguments[] = new MP_Operation(':', new MP_Identifier('simp'), new MP_Boolean(true));
        } else if ($disablesimp) {
            $r->arguments[] = new MP_Operation(':', new MP_Identifier('simp'), new MP_Boolean(false));
        }

        $epos = $options['context'] . '/' . $this->position['start'] . '-' . $this->position['end'];

        $r->arguments[] = new MP_FunctionCall(new MP_Identifier('_EC'),
            [
                new MP_FunctionCall(new MP_Identifier('errcatch'), [
                    new MP_Operation(':', new MP_Identifier('_ct2_tmp'), $ast)
                ]),
                new MP_String($epos)
            ]);

        // If there is a possibility of the simp value leaking to global context we need to identify it.
        if ($simps['out-of-ev-write']) {
            $simp = new MP_Identifier('_ct2_simp');
            $r->arguments[] = new MP_Operation(':', new MP_Identifier('_ct2_simp'), new MP_Identifier('simp'));
        }
        $r->arguments[] = new MP_Operation(':', new MP_Identifier('simp'), new MP_Boolean(false));
        $r->arguments[] = new MP_Operation(':', new MP_Identifier('_ct2_tmp'),
            new MP_FunctionCall(new MP_Identifier('ct2_latex'), [new MP_Identifier('_ct2_tmp'), $mode, $simp]));
        $r->arguments[] = new MP_Operation(':', new MP_Identifier('simp'), new MP_Identifier('_ct2_simp'));
        $r->arguments[] = new MP_Identifier('_ct2_tmp');

        return $r;
    }

    public function is_flat(): bool {
        return false;
    }
}

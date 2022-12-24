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
require_once(__DIR__ . '/../../ast.container.class.php');


class stack_cas_castext2_raw extends stack_cas_castext2_block {

    public $content;

    public function __construct($params, $children=array(), $mathmode=false) {
        parent::__construct($params, $children, $mathmode);
        $this->content = $children[0]->content; // The child is a different type of RAW.
        $this->children = array(); // We want to modify the iteration here a bit.
    }

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
        // Unlike with the LaTeX version we do not try to catch global
        // state affecting things. After all we tend to have more of this
        // type of injection and expanding the logic to allow that would not
        // be nice.
        $forcesimp = $simps['last-seen'] === true;
        $disablesimp = $simps['last-seen'] === false;

        $epos = $options['context'] . '/' . $this->position['start'] . '-' . $this->position['end'];
        $ec = new MP_FunctionCall(new MP_Identifier('_EC'), [
            new MP_FunctionCall(new MP_Identifier('errcatch'), [
                new MP_Operation(':', new MP_Identifier('_ct2_tmp'), $ast)
            ]),
            new MP_String($epos)
        ]);

        if ($forcesimp) {
            // We need the temp to hold the value while we return simp to what it was.
            $r = new MP_FunctionCall(new MP_Identifier('block'), [
                new MP_List([new MP_Identifier('_ct2_tmp'), new MP_Identifier('_ct2_simp')]),
                new MP_Operation(':', new MP_Identifier('_ct2_simp'), new MP_Identifier('simp')),
                new MP_Operation(':', new MP_Identifier('simp'), new MP_Boolean(true)),
                $ec,
                new MP_Operation(':', new MP_Identifier('_ct2_tmp'), new MP_FunctionCall(new MP_Identifier('string'),
                    [new MP_Identifier('_ct2_tmp')])),
                new MP_Operation(':', new MP_Identifier('simp'), new MP_Identifier('_ct2_simp')),
                new MP_Identifier('_ct2_tmp')
            ]);
        } else if ($disablesimp) {
            $r = new MP_FunctionCall(new MP_Identifier('block'), [
                new MP_List([new MP_Identifier('_ct2_tmp'), new MP_Identifier('_ct2_simp')]),
                new MP_Operation(':', new MP_Identifier('_ct2_simp'), new MP_Identifier('simp')),
                new MP_Operation(':', new MP_Identifier('simp'), new MP_Boolean(false)),
                $ec,
                new MP_Operation(':', new MP_Identifier('_ct2_tmp'),
                    new MP_FunctionCall(new MP_Identifier('string'), [new MP_Identifier('_ct2_tmp')])),
                new MP_Operation(':', new MP_Identifier('simp'), new MP_Identifier('_ct2_simp')),
                new MP_Identifier('_ct2_tmp')
            ]);
        } else {
            $r = new MP_FunctionCall(new MP_Identifier('block'), [
                new MP_List([new MP_Identifier('_ct2_tmp')]),
                $ec,
                new MP_FunctionCall(new MP_Identifier('string'), [new MP_Identifier('_ct2_tmp')])
            ]);
        }
        return $r;
    }

    public function is_flat(): bool {
        return true;
    }

    public function validate_extract_attributes(): array {
        return array(stack_ast_container::make_from_teacher_source($this->content, 'ct2:raw', new stack_cas_security()));
    }
}

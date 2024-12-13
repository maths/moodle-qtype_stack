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

/**
 * Add description here!
 * @package    qtype_stack
 * @copyright  2017 Matti Harjula.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../block.interface.php');
require_once(__DIR__ . '/../../ast.container.class.php');

// phpcs:ignore moodle.Commenting.MissingDocblock.Class
class stack_cas_castext2_define extends stack_cas_castext2_block {

    // phpcs:ignore moodle.Commenting.MissingDocblock.Function
    public function compile($format, $options): ?MP_Node {
        $epos = $options['context'] . '/' . $this->position['start'] . '-' . $this->position['end'];
        $r = new MP_Group([]);
        foreach ($this->params as $param) {
            $ev = stack_ast_container::make_from_teacher_source($param['value']);
            $ast = $ev->get_commentles_primary_statement();
            $r->items[] = new MP_FunctionCall(new MP_Identifier('_EC'),
                [
                    new MP_FunctionCall(new MP_Identifier('errcatch'), [
                        new MP_Operation(':', new MP_Identifier($param['key']), $ast),
                    ]),
                    new MP_String($epos),
                ]);
        }

        // In the end we need to return something. Note that this will break all
        // sort of simplifications and you may see some wacky logic working with this.
        // The recommended simplification rule is to move this before any static seen
        // before this, and jsut ignore what happens inside this.
        $r->items[] = new MP_String('');

        // TO-DO: consider a define that would define something for only its contents?
        // For now however define is assumed to be an empty block.
        // block(local(foo,bar),foo:1,bar:3,contents).
        return $r;
    }

    // phpcs:ignore moodle.Commenting.MissingDocblock.Function
    public function is_flat(): bool {
        return true;
    }

    // phpcs:ignore moodle.Commenting.MissingDocblock.Function
    public function validate_extract_attributes(): array {
        $r = [];
        foreach ($this->params as $param) {
            $r[] = stack_ast_container_silent::make_from_teacher_source($param['key'] . ':' .
                $param['value'], 'ct2:define', new stack_cas_security());
        }
        return $r;
    }
}

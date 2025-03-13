<?php
// This file is part of Stack - http://stack.maths.ed.ac.uk/
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

/**
 * This script handles the various deploy/undeploy actions from questiontestrun.php.
 *
 * @package    qtype_stack
 * @copyright  2023 RWTH Aachen
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

// phpcs:ignore moodle.Commenting.MissingDocblock.Class, Generic.Classes.DuplicateClassName.Found
class qtype_stack_renderer {
    // phpcs:ignore moodle.Commenting.MissingDocblock.Function
    public function fact_sheet($name, $fact) {
        $name = html_writer::tag('h5', $name);
        return html_writer::tag('div', $name.$fact, ['class' => 'factsheet']);
    }
}

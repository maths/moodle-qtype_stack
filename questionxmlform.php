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

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

/**
 * This file defines the form for editing question XML.
 *
 * @package    qtype_stack
 * @copyright 2025 University of Edinburgh
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_stack_question_xml_form extends moodleform {
    // phpcs:ignore moodle.Commenting.MissingDocblock.Function
    protected function definition() {

        $mform = $this->_form;

        $mform->addElement('textarea', 'questionxml',
                stack_string('editxmlquestion'), ['rows' => $this->_customdata['numberrows'], 'cols' => 100]);
        $mform->setType('questionxml', PARAM_RAW);
        $mform->setDefault('questionxml', $this->_customdata['xmlstring']);

        // Submit buttons.
        $this->add_sticky_action_buttons(true, $this->_customdata['submitlabel']);
    }
}

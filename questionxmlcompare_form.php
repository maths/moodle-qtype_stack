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

require_once(__DIR__ . '/locallib.php');
require_once($CFG->libdir . '/formslib.php');

/**
 * File upload form for comparing Moodle question XML files.
 *
 * @package    qtype_stack
 * @copyright  2026 University of Edinburgh
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_stack_question_xml_compare_form extends moodleform {
    /**
     * Define the filepicker controls.
     */
    protected function definition() {
        $this->set_display_vertical();
        $mform = $this->_form;
        $mform->disable_form_change_checker();
        $filepickeroptions = [
            'accepted_types' => '.xml',
            'maxfiles' => 1,
        ];

        $mform->addElement('html', '<div class="stack-xml-compare-filepickers">');
        $mform->addElement(
            'filepicker',
            'currentfile',
            stack_string('comparexmlcurrentfile'),
            null,
            $filepickeroptions
        );
        $mform->addElement(
            'filepicker',
            'comparefile',
            stack_string('comparexmlcomparefile'),
            null,
            $filepickeroptions
        );
        $mform->addElement('html', '</div>');
    }
}

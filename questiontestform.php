<?php
// This file is part of Stack - http://stack.bham.ac.uk/
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
 * This file defines the editing form for editing question tests.
 *
 * @copyright 2012 the Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');


/**
 * The editing form for editing question tests.
 *
 * @copyright 2012 the Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_stack_question_test_form extends moodleform {
    protected function definition() {

        $mform = $this->_form;
        $question = $this->_customdata['question'];

        // Inputs
        $mform->addElement('header', 'inputsheader', get_string('testinputs', 'qtype_stack'));

        foreach ($question->inputs as $inputname => $input) {
            // We do not require these to be filled in, (or contain valid input), as the teacher may want to test such cases.
            $input->add_to_moodleform_testinput($mform);
        }

        // TODO I would really like to add a button here:
        // "Fill in the rest of the form to make a passing test"
        // but I am not quite sure how to do that easily.
	//
	// It seems $mform->createElement() does not print any labels for its interaction elements
	// whereas $mform->addElement() does. This is a temporary workaround added here.

        // Expected outcome.
        $mform->addElement('header', 'prtsheader', get_string('expectedoutcomes', 'qtype_stack') 
		. ' ' . get_string('qtesthelp', 'qtype_stack'));

        foreach ($question->prts as $prtname => $prt) {
            $elements = array(
                $mform->createElement('text', $prtname . 'score',
                    get_string('score', 'qtype_stack'), array('size' => 2)),
                $mform->createElement('text', $prtname . 'penalty',
                    get_string('penalty', 'qtype_stack'), array('size' => 2)),
                $mform->createElement('select', $prtname . 'answernote',
                    get_string('answernote', 'qtype_stack'), $prt->get_all_answer_notes())
            );
            $mform->addGroup($elements, null, $prtname, ' ', false);
        }

        // Submit buttons.
        $this->add_action_buttons(true, $this->_customdata['submitlabel']);
    }

    public function validation($data, $files) {
        //print_r($data);
        $errors = parent::validation($data, $files);

        return $errors;
    }
}

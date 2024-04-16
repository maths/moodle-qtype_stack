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
 * This file defines the editing form for editing question tests.
 *
 * @copyright 2012 the Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');


/**
 * The editing form for editing STACK question tests.
 *
 * @copyright 2012 the Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_stack_question_test_form extends moodleform {
    protected function definition() {

        $mform = $this->_form;
        $question = $this->_customdata['question'];

        $mform->addElement('text', 'description', stack_string('description'), ['size' => 64]);
        $mform->setType('description', PARAM_RAW);

        // Inputs.
        $mform->addElement('header', 'inputsheader', stack_string('testinputs'));

        foreach ($question->inputs as $input) {
            // We do not require these to be filled in, (or contain valid input), as the teacher may want to test such cases.
            $input->add_to_moodleform_testinput($mform);
        }

        $mform->addElement('submit', 'teacher', stack_string('teacheranswercase'));
        $mform->registerNoSubmitButton('teacher');
        $mform->addElement('submit', 'complete', stack_string('completetestcase'));
        $mform->registerNoSubmitButton('complete');

        // Expected outcome.
        $mform->addElement('header', 'prtsheader', stack_string('expectedoutcomes'));

        $allinputs = array_keys($question->inputs);
        foreach ($question->prts as $prtname => $prt) {
            $inputsused = array_keys($question->get_cached('required')[$prtname]);
            $inputsused = ': [' . implode(', ' , $inputsused) . ']';

            $elements = [
                $mform->createElement('text', $prtname . 'score',
                    stack_string('score'), ['size' => 2]),
                $mform->createElement('text', $prtname . 'penalty',
                    stack_string('penalty'), ['size' => 2]),
                $mform->createElement('select', $prtname . 'answernote',
                    stack_string('answernote'), $prt->get_all_answer_notes()),
            ];
            $mform->addGroup($elements, $prtname . 'group', $prtname . $inputsused, ' ', false);
            $mform->setType($prtname . 'score', PARAM_RAW);
            $mform->setType($prtname . 'penalty', PARAM_RAW);
            $mform->setType($prtname . 'answernote', PARAM_RAW);
        }

        // Submit buttons.
        $this->add_action_buttons(true, $this->_customdata['submitlabel']);
    }

    public function definition_after_data() {
        if ($this->_form->exportValue('complete')) {
            $this->complete_passing_testcase();
        }
        if ($this->_form->exportValue('teacher')) {
            $this->complete_teacher_testcase();
        }
    }

    protected function complete_passing_testcase() {

        $mform = $this->_form;
        $question = $this->_customdata['question'];

        $inputs = [];
        foreach ($question->inputs as $inputname => $input) {
            $inputs[$inputname] = $mform->exportValue($inputname);
        }

        $response = stack_question_test::compute_response($question, $inputs);

        foreach ($question->prts as $prtname => $prt) {
            $result = $question->get_prt_result($prtname, $response, false);
            $answernotes = $result->get_answernotes();
            // In automatic test case generation set penalties as the default unless they differ.
            // If they are the same as the detault, and you want this, you can change it later.
            $prtpenalty = $result->get_penalty();
            if ($prtpenalty == $question->penalty) {
                $prtpenalty = '';
            }
            $mform->getElement($prtname . 'group')->setValue([
                    $prtname . 'score'      => $result->get_score(),
                    $prtname . 'penalty'    => $prtpenalty,
                    $prtname . 'answernote' => end($answernotes)]);
        }
    }

    protected function complete_teacher_testcase() {

        $mform = $this->_form;
        $question = $this->_customdata['question'];

        $inputs = [];
        foreach ($question->inputs as $inputname => $input) {
            $ta = $input->get_teacher_answer_testcase();
            $mform->getElement($inputname)->setValue($ta);
        }
    }

    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        return $errors;
    }
}

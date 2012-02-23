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
 * Stack question renderer class.
 *
 * @package   qtype_stack
 * @copyright 2012 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Generates the output for Stack questions.
 *
 * @copyright 2012 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_stack_renderer extends qtype_renderer {

    public function formulation_and_controls(question_attempt $qa, question_display_options $options) {
        $question = $qa->get_question();
        if (empty($question->inputs)) {
            throw new coding_exception('This question does not have any inputs.');
        }

        $response = $qa->get_last_qt_data();

        $questiontext = $qa->get_last_qt_var('_questiontext');

        // Replace inputs.
        foreach ($question->inputs as $name => $input) {

            if (array_key_exists($name, $response)) {
                $currentvalue = $response[$name];
            } else {
                $currentvalue = '';
            }
            $questiontext = str_replace("#{$name}#",
                    $input->get_xhtml($currentvalue, $qa->get_qt_field_name($name), $options->readonly),
                    $questiontext);

            $feedback = $this->input_feedback($question->get_input_feedback($name, $response));
            $questiontext = str_replace("<IEfeedback>{$name}</IEfeedback>", $feedback, $questiontext);
        }

        foreach ($question->prts as $index => $prt) {
            if ($options->feedback) {
                $result = $question->get_prt_result($index, $response);
                $feedback = $this->prt_feedback($result['feedback']);
            } else {
                $feedback = '';
            }
            $questiontext = str_replace("<PRTfeedback>{$index}</PRTfeedback>", $feedback, $questiontext);
        }

        return $question->format_text($questiontext, $question->questiontextformat,
                $qa, 'question', 'questiontext', $question->id);
    }

    /**
     * @param string $feedback the raw feedback message from the intput element.
     * @return string Nicely formatted feedback, for display.
     */
    protected function input_feedback($feedback) {
        return html_writer::nonempty_tag('div', $feedback, array('class' => 'IEFeedback'));
    }

    /**
     * @param string $feedback the raw feedback message from the PRT.
     * @return string Nicely formatted feedback, for display.
     */
    protected function prt_feedback($feedback) {
        return html_writer::nonempty_tag('div', $feedback, array('class' => 'PRTFeedback'));
    }

    /**
     * Tests whether the input element exists inside a math region.
     */
    protected function is_inside_maths($input, $string) {
        // Remove all delimited regions and see if $input remains in $string
        // TODO move this to stack_utils? NOT CURRENTLY USED
        $patterns = array('/\\$\\$(.+?)\\$\\$/', '/\\$(.+?)\\$/', '/\\\\\[(.+?)\\\\\]/', '/\\\\\((.+?)\\\\\)/');
        $string = preg_replace($patterns, '', $string);
        return strpos($string, $input) === true;
    }

}

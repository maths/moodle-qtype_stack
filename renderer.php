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
        $questiontext = $question->format_text(
                stack_maths::process_display_castext($questiontext),
                $question->questiontextformat,
                $qa, 'question', 'questiontext', $question->id);

        // Replace inputs.
        $inputstovaldiate = array();
        foreach ($question->inputs as $name => $input) {
            $fieldname = $qa->get_qt_field_name($name);
            $state = $question->get_input_state($name, $response);

            $questiontext = str_replace("[[input:{$name}]]",
                    $input->render($state, $fieldname, $options->readonly),
                    $questiontext);

            $feedback = $this->input_validation($fieldname . '_val', $input->render_validation($state, $fieldname));
            $questiontext = str_replace("[[validation:{$name}]]", $feedback, $questiontext);

            $qaid = $qa->get_database_id();
            if ($input->requires_validation()) {
                $inputstovaldiate[] = $name;
            }
        }

        // Initialise automatic validation, if enabled.
        if ($qaid && stack_utils::get_config()->ajaxvalidation) {
            $this->page->requires->yui_module('moodle-qtype_stack-input',
                    'M.qtype_stack.init_inputs', array($inputstovaldiate, $qaid, $qa->get_field_prefix()));
        }

        // Replace PRTs.
        foreach ($question->prts as $index => $prt) {
            $feedback = '';
            if ($options->feedback) {
                $feedback = $this->prt_feedback($index, $response, $qa, $options);

            } else if (in_array($qa->get_behaviour_name(), array('interactivecountback', 'adaptivemulipart'))) {
                // The behaviour name test here is a hack. The trouble is that interactive
                // behaviour or adaptivemulipart does not show feedback if the input
                // is invalid, but we want to show the CAS errors from the PRT.
                $result = $question->get_prt_result($index, $response, $qa->get_state()->is_finished());
                $feedback = html_writer::nonempty_tag('div', $result->errors,
                        array('class' => 'stackprtfeedback stackprtfeedback-' . $name));
            }
            $questiontext = str_replace("[[feedback:{$index}]]", $feedback, $questiontext);
        }

        $result = '';
        $result .= $this->question_tests_link($question, $options) . $questiontext;

        if ($qa->get_state() == question_state::$invalid) {
            $result .= html_writer::nonempty_tag('div',
                    $question->get_validation_error($response),
                    array('class' => 'validationerror'));
        }

        return $result;
    }

    /**
     * Displays a link to run the question tests, if applicable.
     * @param qtype_stack_question $question
     * @param question_display_options $options
     * @return string HTML fragment.
     */
    protected function question_tests_link(qtype_stack_question $question, question_display_options $options) {
        if (!empty($options->suppressruntestslink)) {
            return '';
        }
        if (!$question->user_can_view()) {
            return '';
        }

        $urlparams = array('questionid' => $question->id, 'seed' => $question->seed);

        // This is a bit of a hack to find the right thing to put in the URL.
        $context = $question->get_context();
        if (!empty($options->editquestionparams['cmid'])) {
            $urlparams['cmid'] = $options->editquestionparams['cmid'];

        } else if (!empty($options->editquestionparams['courseid'])) {
            $urlparams['courseid'] = $options->editquestionparams['courseid'];

        } else if ($cmid = optional_param('cmid', null, PARAM_INT)) {
            $urlparams['cmid'] = $cmid;

        } else if ($courseid = optional_param('courseid', null, PARAM_INT)) {
            $urlparams['courseid'] = $courseid;

        } else if ($context->contextlevel == CONTEXT_MODULE) {
            $urlparams['cmid'] = $context->instanceid;

        } else if ($context->contextlevel == CONTEXT_COURSE) {
            $urlparams['courseid'] = $context->instanceid;

        } else {
            $urlparams['courseid'] = get_site()->id;
        }

        return html_writer::tag('div',
                html_writer::link(new moodle_url('/question/type/stack/questiontestrun.php', $urlparams),
                        stack_string('runquestiontests')),
                array('class' => 'questiontestslink'));
    }

    public function feedback(question_attempt $qa, question_display_options $options) {
        $output = '';
        if ($options->feedback) {
            $output .= $this->stack_specific_feedback($qa, $options);
        } else if ($qa->get_behaviour_name() == 'interactivecountback') {
            // The behaviour name test here is a hack. The trouble is that interactive
            // behaviour does not show feedback if the input is invalid, but we want
            // to show the CAS errors from the PRT.
            $output .= $this->stack_specific_feedback_errors_only($qa);
        }

        $output .= parent::feedback($qa, $options);

        return $output;
    }

    protected function stack_specific_feedback_errors_only(question_attempt $qa) {
        $question = $qa->get_question();
        $response = $qa->get_last_qt_data();
        $feedbacktext = $qa->get_last_qt_var('_feedback');
        if (!$feedbacktext) {
            return '';
        }

        $feedbacktext = stack_maths::process_display_castext($feedbacktext);
        $feedbacktext = $question->format_text($feedbacktext, $question->specificfeedbackformat,
                $qa, 'qtype_stack', 'specificfeedback', $question->id);

        // Replace any PRT feedback.
        $allempty = true;
        foreach ($question->prts as $name => $prt) {
            $feedback = '';
            $result = $question->get_prt_result($name, $response, $qa->get_state()->is_finished());
            if ($result->errors) {
                $feedback = html_writer::nonempty_tag('div', $result->errors,
                        array('class' => 'stackprtfeedback stackprtfeedback-' . $name));
            }
            $allempty = $allempty && !$feedback;
            $feedbacktext = str_replace("[[feedback:{$name}]]", $feedback, $feedbacktext);
        }

        if ($allempty) {
            return '';
        }

        return $feedbacktext;
    }

    /**
     * Generate the specific feedback. This has to be a stack-specific method
     * since the standard specific_feedback method does not get given $options.
     * @param question_attempt $qa the question attempt to display.
     * @param question_display_options $options controls what should and should not be displayed.
     * @return string HTML fragment.
     */
    protected function stack_specific_feedback(question_attempt $qa, question_display_options $options) {

        $question = $qa->get_question();
        $response = $qa->get_last_qt_data();
        $feedbacktext = $qa->get_last_qt_var('_feedback');
        if (!$feedbacktext) {
            return '';
        }

        $feedbacktext = stack_maths::process_display_castext($feedbacktext);
        $feedbacktext = $question->format_text($feedbacktext, $question->specificfeedbackformat,
                $qa, 'qtype_stack', 'specificfeedback', $question->id);

        // Replace any PRT feedback.
        $allempty = true;
        foreach ($question->prts as $index => $prt) {
            $feedback = $this->prt_feedback($index, $response, $qa, $options);
            $allempty = $allempty && !$feedback;
            $feedbacktext = str_replace("[[feedback:{$index}]]",
                    stack_maths::process_display_castext($feedback), $feedbacktext);
        }

        if ($allempty) {
            return '';
        }

        return $feedbacktext;
    }

    /**
     * @param string $feedback the raw feedback message from the intput element.
     * @return string Nicely formatted feedback, for display.
     */
    protected function input_validation($id, $feedback) {
        $class = "stackinputfeedback";
        if (!$feedback) {
            $class .= ' empty';
        }
        return html_writer::tag('div', $feedback, array('class' => $class, 'id' => $id));
    }

    /**
     * Slighly complex rules for what feedback to display.
     * @param string $name the PRT name.
     * @param array $response the most recent student response.
     * @param question_attempt $qa the question attempt to display.
     * @param question_display_options $options controls what should and should not be displayed.
     * @return string nicely formatted feedback, for display.
     */
    protected function prt_feedback($name, $response, question_attempt $qa, question_display_options $options) {
        $question = $qa->get_question();

        if ($qa->get_behaviour_name() == 'adaptivemultipart') {
            // The behaviour name test here is a hack. The trouble is that
            // for adaptive behaviour, exactly what feedback to display is
            // is complex, so we need to ask the behaviour.
            $step = $qa->get_behaviour()->get_last_graded_response_step_for_part($name);

            if (!$step) {
                return '';
            }

            $relevantresponse = $step->get_qt_data();
            if (!$question->is_same_response_for_part($name, $relevantresponse, $response)) {
                return '';
            }
        } else {
            $relevantresponse = $response;
        }

        $result = $question->get_prt_result($name, $response, $qa->get_state()->is_finished());
        if (is_null($result->valid)) {
            return '';
        }
        return $this->prt_feedback_display($name, $qa, $question, $result, $options);
    }

    /**
     * Actually generate the display of the PRT feedback.
     * @param string $name the PRT name.
     * @param question_attempt $qa the question attempt to display.
     * @param question_definition $question the question being displayed.
     * @param stack_potentialresponse_tree_state $result the results to display.
     * @param question_display_options $options controls what should and should not be displayed.
     * @return string nicely formatted feedback, for display.
     */
    protected function prt_feedback_display($name, question_attempt $qa,
            question_definition $question, stack_potentialresponse_tree_state $result,
            question_display_options $options) {
        $err = '';
        if ($result->errors) {
            $err = $result->errors;
        }

        $feedback = '';
        $feedbackbits = $result->get_feedback();
        if ($feedbackbits) {
            $feedback = array();
            $format = null;
            foreach ($feedbackbits as $bit) {
                $feedback[] = $qa->rewrite_pluginfile_urls(
                        $bit->feedback, 'qtype_stack', $bit->filearea, $bit->itemid);
                if (!is_null($bit->format)) {
                    if (is_null($format)) {
                        $format = $bit->format;
                    }
                    if ($bit->format != $format) {
                        throw new coding_exception('Inconsistent feedback formats found in PRT ' . $name);
                    }
                }
            }
            if (is_null($format)) {
                $format = FORMAT_HTML;
            }

            $feedback = $result->substitue_variables_in_feedback(implode(' ', $feedback));
            $feedback = format_text(stack_maths::process_display_castext($feedback),
                    $format, array('noclean' => true, 'para' => false));
        }

        $gradingdetails = '';
        if (!$result->errors && $qa->get_behaviour_name() == 'adaptivemultipart') {
            // This is rather a hack, but it will probably work.
            $renderer = $this->page->get_renderer('qbehaviour_adaptivemultipart');
            $gradingdetails = $renderer->render_adaptive_marks(
                    $qa->get_behaviour()->get_part_mark_details($name), $options);
        }

        return html_writer::nonempty_tag('div',
                $this->standard_prt_feedback($qa, $question, $result) .
                $err . $feedback . $gradingdetails,
                array('class' => 'stackprtfeedback stackprtfeedback-' . $name));
    }

    /**
     * Generate the standard PRT feedback for a pearticular score.
     * @param question_attempt $qa the question attempt to display.
     * @param question_definition $question the question being displayed.
     * @param stack_potentialresponse_tree_state $result the results to display.
     * @return string nicely standard feedback, for display.
     */
    protected function standard_prt_feedback($qa, $question, $result) {
        if ($result->errors) {
            return '';
        }

        $state = question_state::graded_state_for_fraction($result->score);

        $class = $state->get_feedback_class($qa);
        $field = 'prt' . $class;
        $format = 'prt' . $class . 'format';
        if ($question->$field) {
            return html_writer::tag('div', $question->format_text(
                    stack_maths::process_display_castext($question->$field),
                    $question->$format, $qa, 'qtype_stack', $field, $question->id), array('class' => $class));
        }
        return '';
    }
}

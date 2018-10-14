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
 * Stack question renderer class.
 *
 * @package   qtype_stack
 * @copyright 2012 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use qtype_stack\output\debug_renderer;

defined('MOODLE_INTERNAL') || die();

/**
 * Generates the output for Stack questions.
 *
 * @copyright 2012 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_stack_renderer extends qtype_renderer {

    public function formulation_and_controls(question_attempt $qa, question_display_options $options) {
        global $CFG;
        $debug = $CFG->debugdeveloper;

        $question = $qa->get_question();

        $response = $qa->get_last_qt_data();

        $questiontext = $question->questiontextinstantiated;


        $prefix = $qa->get_field_prefix();

        $inputids = [];
        $latexinputids = [];
        $latexresponses = [];


        // Replace inputs.
        $inputstovaldiate = array();
        $qaid = null;
        foreach ($question->inputs as $name => $input) {
            // Get the actual value of the teacher's answer at this point.
            $tavalue = $question->get_session_variable($name);

            $fieldname = $qa->get_qt_field_name($name);
            $state = $question->get_input_state($name, $response);

            $questiontext = str_replace("[[input:{$name}]]",
                    $input->render($state, $fieldname, $options->readonly, $tavalue),
                    $questiontext);

            $questiontext = $input->replace_validation_tags($state, $fieldname, $questiontext);

            $qaid = $qa->get_database_id();
            if ($input->requires_validation()) {
                $inputstovaldiate[] = $name;
            }
        }

        // Replace PRTs.
        foreach ($question->prts as $index => $prt) {
            $feedback = '';
            if ($options->feedback) {
                $feedback = $this->prt_feedback($index, $response, $qa, $options, true);

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

        // Now format the questiontext.  This should be done after the subsitutions of inputs and PRTs.
        $questiontext = $question->format_text(
                stack_maths::process_display_castext($questiontext, $this),
                $question->questiontextformat,
                $qa, 'question', 'questiontext', $question->id);

        // Initialise automatic validation, if enabled.
        if ($qaid && stack_utils::get_config()->ajaxvalidation) {
            $this->page->requires->yui_module('moodle-qtype_stack-input',
                    'M.qtype_stack.init_inputs', array($inputstovaldiate, $qaid, $qa->get_field_prefix()));
        }

        $result = '';
        $result .= html_writer::div('', '', ['id' => 'controls_wrapper']);

        $result .= $this->question_tests_link($question, $options) . $questiontext;

        if ($qa->get_state() == question_state::$invalid) {
            $result .= html_writer::nonempty_tag('div',
                    $question->get_validation_error($response),
                    array('class' => 'validationerror'));
        }


        foreach ($question->inputs as $name => $input) {
            // Collect all the STACK input field ids.
            $inputids[] = $prefix . $name;

            // Create new hidden input fields for string the raw LaTeX input.
            $latexinputname = $prefix . $name . '_latex';
            $latexinputids[] = $latexinputname;

            // Set initial question value to "" if the question_attempt has no responses.
            if (isset($response[$name . '_latex'])) {
                $value = $response[$name . '_latex'];
            } else {
                $value = "";
            }

            $latexresponses[] = $value;

            $attributes = array(
                'type' => 'hidden',
                'name' => $latexinputname,
                'value' => $value,
                'id' => $latexinputname,
            );
            $result .= html_writer::empty_tag('input', $attributes);
        }

        // Initialise visualmathinput functionality
        if ($CFG->debugdeveloper) {
            $result .= debug_renderer::render_debug_view($inputids, "", $latexinputids, $latexresponses);
        }

        $configParams = $this->getAMDConfigParams($question);
        $amdParams = array($debug, $prefix, $inputids, $latexinputids, $latexresponses, $configParams);
        $this->page->requires->js_call_amd('qtype_stack/input', 'initialize', $amdParams);

        return $result;
    }


    public function head_code(question_attempt $qa) {
        global $PAGE;

        parent::head_code($qa);

        $PAGE->requires->css('/question/type/stack/visualmathinput/mathquill.css');
        $PAGE->requires->css('/question/type/stack/visualmathinput/visual-math-input.css');
    }


    /**
     * @param $question
     * @return mixed
     * @throws stack_exception
     */
    private function getAMDConfigParams($question) {
        $result = [];
        //if (!isset($question->singlevars)) throw new stack_exception('renderer: singlevars is not set');
        //if (!isset($question->addtimessign)) throw new stack_exception('renderer: addtimessign is not set');
        //if (!isset($question->mathinputmode)) throw new stack_exception('renderer: mathinputmode is not set');

        //$result['singlevars'] = $question->singlevars;
        //$result['addtimessign'] = $question->addtimessign;
        //esult['mathinputmode'] = $question->mathinputmode;

        $result['singlevars'] = "1";
        $result['addtimessign'] = "1";
        $result['mathinputmode'] = "experimental";
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

        $urlparams = array('questionid' => $question->id);

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

        $links = array();
        if ($question->user_can_edit()) {
            $links[] = html_writer::link(new moodle_url(
                    '/question/type/stack/tidyquestion.php', $urlparams),
                    stack_string('tidyquestion'));
        }

        $urlparams['seed'] = $question->seed;
        $links[] = html_writer::link(new moodle_url(
                '/question/type/stack/questiontestrun.php', $urlparams),
                stack_string('runquestiontests'));

        return html_writer::tag('div', implode(' | ', $links), array('class' => 'questiontestslink'));
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
        $feedbacktext = $question->specificfeedbackinstantiated;
        if (!$feedbacktext) {
            return '';
        }

        $feedbacktext = stack_maths::process_display_castext($feedbacktext, $this);
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
        $feedbacktext = $question->specificfeedbackinstantiated;
        if (!$feedbacktext) {
            return '';
        }

        $feedbacktext = stack_maths::process_display_castext($feedbacktext, $this);
        $feedbacktext = $question->format_text($feedbacktext, $question->specificfeedbackformat,
                $qa, 'qtype_stack', 'specificfeedback', $question->id);

        $individualfeedback = count($question->prts) == 1;
        if ($individualfeedback) {
            $overallfeedback = '';
        } else {
            $overallfeedback = $this->overall_standard_prt_feedback($qa, $question, $response);
        }

        // Replace any PRT feedback.
        $allempty = true;
        foreach ($question->prts as $index => $prt) {
            $feedback = $this->prt_feedback($index, $response, $qa, $options, $individualfeedback);
            $allempty = $allempty && !$feedback;
            $feedbacktext = str_replace("[[feedback:{$index}]]",
                    stack_maths::process_display_castext($feedback, $this), $feedbacktext);
        }

        if ($allempty && !$overallfeedback) {
            return '';
        }

        return $overallfeedback . $feedbacktext;
    }

    /**
     * Get the appropriate response to use for generating the feedback to a PRT.
     * @param string $name PRT name
     * @param array $response the current response.
     * @param question_attempt $qa the question_attempt we are displaying.
     */
    protected function get_applicable_response_for_prt($name, $response, question_attempt $qa) {
        if ($qa->get_behaviour_name() != 'adaptivemultipart') {
            return $response;
        }

        // The behaviour name test above is a hack. The trouble is that
        // for adaptive behaviour, exactly what feedback to display is
        // is complex, so we need to ask the behaviour.
        $step = $qa->get_behaviour()->get_last_graded_response_step_for_part($name);

        if (!$step) {
            return null;
        }

        $lastgradedresponse = $step->get_qt_data();
        if (!$qa->get_question()->is_same_response_for_part($name, $lastgradedresponse, $response)) {
            return null;
        }

        return $response;
    }

    /**
     * Slighly complex rules for what feedback to display.
     * @param string $name the PRT name.
     * @param array $response the most recent student response.
     * @param question_attempt $qa the question attempt to display.
     * @param question_display_options $options controls what should and should not be displayed.
     * @param bool $includestandardfeedback whether to include the standard
     *      'Your answer is partially correct' bit at the start of the feedback.
     * @return string nicely formatted feedback, for display.
     */
    protected function prt_feedback($name, $response, question_attempt $qa,
            question_display_options $options, $includestandardfeedback) {
        $question = $qa->get_question();

        $relevantresponse = $this->get_applicable_response_for_prt($name, $response, $qa);
        if (is_null($relevantresponse)) {
            return '';
        }

        $result = $question->get_prt_result($name, $relevantresponse, $qa->get_state()->is_finished());
        if (is_null($result->valid)) {
            return '';
        }
        return $this->prt_feedback_display($name, $qa, $question, $result, $options, $includestandardfeedback);
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
            question_display_options $options, $includestandardfeedback) {
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
            $feedback = format_text(stack_maths::process_display_castext($feedback, $this),
                    $format, array('noclean' => true, 'para' => false));
        }

        $gradingdetails = '';
        if (!$result->errors && $qa->get_behaviour_name() == 'adaptivemultipart'
                && $options->marks >= question_display_options::MARK_AND_MAX) {
            $renderer = $this->page->get_renderer('qbehaviour_adaptivemultipart');
            $gradingdetails = $renderer->render_adaptive_marks(
                $qa->get_behaviour()->get_part_mark_details($name), $options);
        }

        if ($includestandardfeedback) {
            $standardfeedback = $this->standard_prt_feedback($qa, $question, $result);
        } else {
            $standardfeedback = '';
        }

        return html_writer::nonempty_tag('div', $standardfeedback . $err . $feedback . $gradingdetails,
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

        $class = $state->get_feedback_class();
        $field = 'prt' . $class . 'instantiated';
        $format = 'prt' . $class . 'format';
        if ($question->$field) {
            return html_writer::tag('div', $question->format_text(
                    stack_maths::process_display_castext($question->$field, $this),
                    $question->$format, $qa, 'qtype_stack', $field, $question->id), array('class' => $class));
        }
        return '';
    }

    /**
     * Display and appropriate piece of standard PRT feedback given the overall
     * state of the question.
     * @param question_attempt $qa
     * @param qtype_stack_question $question the question being displayed.
     * @param array $response the current response.
     * @return string HTML fragment.
     */
    protected function overall_standard_prt_feedback(question_attempt $qa,
            qtype_stack_question $question, $response) {

        $fraction = null;
        foreach ($question->prts as $name => $prt) {
            $relevantresponse = $this->get_applicable_response_for_prt($name, $response, $qa);
            if (is_null($relevantresponse)) {
                continue;
            }

            $result = $question->get_prt_result($name, $relevantresponse, $qa->get_state()->is_finished());
            if (is_null($result->valid)) {
                continue;
            }

            $fraction += $result->fraction;
        }

        if (is_null($fraction)) {
            return '';
        }

        $result = new stack_potentialresponse_tree_state(1, true, $fraction);
        return $this->standard_prt_feedback($qa, $qa->get_question(), $result);
    }

    protected function hint(question_attempt $qa, question_hint $hint) {
        if (empty($hint->hint)) {
            return '';
        }

        $hinttext = $qa->get_question()->get_hint_castext($hint);

        $newhint = new question_hint($hint->id,
                stack_maths::process_display_castext($hinttext->get_display_castext(), $this),
                $hint->hintformat);

        return html_writer::nonempty_tag('div',
                $qa->get_question()->format_hint($newhint, $qa), array('class' => 'hint'));
    }

    public function correct_response(question_attempt $qa) {
        $question = $qa->get_question();
        return '<hr />'.$question->format_correct_response($qa);
    }

    public function general_feedback(question_attempt $qa) {
        $question = $qa->get_question();
        if (empty($question->generalfeedback)) {
            return '';
        }

        return $qa->get_question()->format_text(stack_maths::process_display_castext(
                $question->get_generalfeedback_castext()->get_display_castext(), $this),
                $question->generalfeedbackformat, $qa, 'question', 'generalfeedback', $question->id);
    }

    /**
     * Render a fact sheet.
     * @param string $name the title of the fact sheet.
     * @param string $fact the contents of the fact sheet.
     */
    public function fact_sheet($name, $fact) {
        $name = html_writer::tag('h5', $name);
        return html_writer::tag('div', $name.$fact, array('class' => 'factsheet'));
    }
}

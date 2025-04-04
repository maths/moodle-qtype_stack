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

/**
 * Stack question renderer class.
 *
 * @package   qtype_stack
 * @copyright 2012 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/vle_specific.php');

/**
 * Generates the output for Stack questions.
 *
 * @package    qtype_stack
 * @copyright 2012 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
// @codingStandardsIgnoreStart
// There's a matching class name in the API.
class qtype_stack_renderer extends qtype_renderer {
// @codingStandardsIgnoreEnd

    // phpcs:ignore moodle.Commenting.MissingDocblock.Function
    public function formulation_and_controls(question_attempt $qa, question_display_options $options) {
        /* Return type should be @var qtype_stack_question $question. */
        $question = $qa->get_question();

        $response = $qa->get_last_qt_data();

        // We need to provide a processor for the CASText2 post-processing,
        // basically for targetting pluginfiles.
        $question->castextprocessor = new castext2_qa_processor($qa);

        if (is_string($question->questiontextinstantiated)) {
            // The question has not been instantiated successfully, at this level it is likely
            // a failure at compilation and that means invalid teacher code.
            return $question->questiontextinstantiated;
        }

        $questiontext = $question->questiontextinstantiated->get_rendered($question->castextprocessor);

        // Replace inputs.
        $inputstovaldiate = [];

        // Get the list of placeholders before format_text.
        $originalinputplaceholders = array_unique(stack_utils::extract_placeholders($questiontext, 'input'));
        sort($originalinputplaceholders);
        $originalfeedbackplaceholders = array_unique(stack_utils::extract_placeholders($questiontext, 'feedback'));
        sort($originalfeedbackplaceholders);

        // Now format the questiontext.
        $questiontext = $question->format_text(
                stack_maths::process_display_castext($questiontext, $this),
                FORMAT_HTML, // All CASText2 processed content has already been formatted to HTML.
                $qa, 'question', 'questiontext', $question->id);
        // Replace the secured bits.
        $questiontext = $question->questiontextinstantiated->apply_placeholder_holder($questiontext);

        // Get the list of placeholders after format_text.
        $formatedinputplaceholders = stack_utils::extract_placeholders($questiontext, 'input');
        sort($formatedinputplaceholders);
        $formatedfeedbackplaceholders = stack_utils::extract_placeholders($questiontext, 'feedback');
        sort($formatedfeedbackplaceholders);

        // We need to check that if the list has changed.
        // Have we lost some of the placeholders entirely?
        // Duplicates may have been removed by multi-lang,
        // No duplicates should remain.
        if ($formatedinputplaceholders !== $originalinputplaceholders ||
                $formatedfeedbackplaceholders !== $originalfeedbackplaceholders) {
            throw new coding_exception('Inconsistent placeholders. Possibly due to multi-lang filtter not being active.');
        }

        foreach ($question->inputs as $name => $input) {
            // Get the actual value of the teacher's answer at this point.
            $tavalue = $question->get_ta_for_input($name);

            $fieldname = $qa->get_qt_field_name($name);
            $state = $question->get_input_state($name, $response);

            $questiontext = str_replace("[[input:{$name}]]",
                    $input->render($state, $fieldname, $options->readonly, $tavalue),
                    $questiontext);

            $questiontext = $input->replace_validation_tags($state, $fieldname, $questiontext);

            if ($input->requires_validation()) {
                $inputstovaldiate[] = $name;
            }
        }

        // Replace PRTs.
        foreach ($question->prts as $index => $prt) {
            $feedback = '';
            if ($options->feedback) {
                $feedback = $this->prt_feedback($index, $response, $qa, $options, $prt->get_feedbackstyle());

            } else if (in_array($qa->get_behaviour_name(), ['interactivecountback', 'adaptivemulipart'])) {
                // The behaviour name test here is a hack. The trouble is that interactive
                // behaviour or adaptivemulipart does not show feedback if the input
                // is invalid, but we want to show the CAS errors from the PRT.
                $result = $question->get_prt_result($index, $response, $qa->get_state()->is_finished());
                $errors = implode(' ', $result->get_errors());
                $feedback = html_writer::nonempty_tag('span', $errors,
                        ['class' => 'stackprtfeedback stackprtfeedback-' . $name]);
            }
            $questiontext = str_replace("[[feedback:{$index}]]", $feedback, $questiontext);
        }

        // Initialise automatic validation, if enabled.
        if (stack_utils::get_config()->ajaxvalidation) {
            // Once we cen rely on everyone being on a Moodle version that includes the fix for
            // MDL-65029 (3.5.6+, 3.6.4+, 3.7+) we can remove this if and just call the method.
            if (method_exists($qa, 'get_outer_question_div_unique_id')) {
                $questiondivid = $qa->get_outer_question_div_unique_id();
            } else {
                $questiondivid = 'q' . $qa->get_slot();
            }
            $this->page->requires->js_call_amd('qtype_stack/input', 'initInputs',
                    [
                        $questiondivid, $qa->get_field_prefix(),
                        $qa->get_database_id(), $inputstovaldiate,
                    ]);
        }

        $result = '';
        $result .= $this->question_tests_link($question, $options) . $questiontext;

        if ($qa->get_state() == question_state::$invalid) {
            $result .= html_writer::nonempty_tag('span',
                    $question->get_validation_error($response),
                    ['class' => 'validationerror']);
        }

        // ISS879 - Output current language
        // to be returned by validation request and also stored in
        // question step data on submission.
        $fieldname = $qa->get_qt_field_name('step_lang');
        $currentlang = stack_get_system_language();
        $result .= html_writer::empty_tag('input', ['type' => 'hidden',
                    'name' => $fieldname, 'value' => $currentlang,
        ]);

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
        if (!stack_user_can_view_question($question)) {
            return '';
        }

        $urlparams = ['questionid' => $question->id];
        $urlparams['seed'] = $question->seed;

        // Quite honestly fellow developers I'm getting fed up of fixing live questions written by colleagues!
        // Especially when the questions do not have tests or deployed variants which would have revealed the problem.
        // Make these problems more obvious to authors, who don't yet understand what tests/variants are for.
        // Alert a teacher to questions without tests or deployed variants.
        $testscases = question_bank::get_qtype('stack')->load_question_tests($question->id);
        $links = [];
        if (($question->has_random_variants() && empty($question->deployedseeds)) ||
            count($testscases) == 0) {
            $links[] = html_writer::link(
                $question->qtype->get_question_test_url($question),
                stack_string_error('runquestiontests_alert'));
        } else {
            $links[] = html_writer::link(
                    $question->qtype->get_question_test_url($question),
                    stack_string('runquestiontests'));
        }

        return html_writer::tag('div', implode(' | ', $links), ['class' => 'questiontestslink']);
    }

    // phpcs:ignore moodle.Commenting.MissingDocblock.Function
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

    // phpcs:ignore moodle.Commenting.MissingDocblock.Function
    protected function stack_specific_feedback_errors_only(question_attempt $qa) {
        $question = $qa->get_question();
        $response = $qa->get_last_qt_data();
        // If called out of order.
        if ($question->castextprocessor === null) {
            $question->castextprocessor = new castext2_qa_processor($qa);
        }

        if ($question->specificfeedbackinstantiated === null) {
            // Invalid question, otherwise this would be here.
            return '';
        }
        $feedbacktext = $question->specificfeedbackinstantiated->get_rendered($question->castextprocessor);
        if (!$feedbacktext) {
            return '';
        }

        $feedbacktext = stack_maths::process_display_castext($feedbacktext, $this);
        $feedbacktext = $question->format_text($feedbacktext,
                FORMAT_HTML, // All CASText2 processed content has already been formatted to HTML.
                $qa, 'qtype_stack', 'specificfeedback', $question->id);
        // Replace the secured bits.
        $feedbacktext = $question->specificfeedbackinstantiated->apply_placeholder_holder($feedbacktext);

        // Replace any PRT feedback.
        $allempty = true;
        foreach ($question->prts as $name => $prt) {
            $feedback = '';
            $result = $question->get_prt_result($name, $response, $qa->get_state()->is_finished());
            if ($result->get_errors() != []) {
                $errors = implode(' ', $result->get_errors());
                $feedback = html_writer::nonempty_tag('span', $errors,
                        ['class' => 'stackprtfeedback stackprtfeedback-' . $name]);
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
        // If called out of order.
        if ($question->castextprocessor === null) {
            $question->castextprocessor = new castext2_qa_processor($qa);
        }
        if ($question->specificfeedbackinstantiated === null) {
            // Invalid question, otherwise this would be here.
            return '';
        }
        $feedbacktext = $question->specificfeedbackinstantiated->get_rendered($question->castextprocessor);
        if (!$feedbacktext) {
            return '';
        }

        $feedbacktext = stack_maths::process_display_castext($feedbacktext, $this);
        $feedbacktext = $question->format_text($feedbacktext,
                FORMAT_HTML, // All CASText2 processed content has already been formatted to HTML.
                $qa, 'qtype_stack', 'specificfeedback', $question->id);
        // Replace the secured bits.
        $feedbacktext = $question->specificfeedbackinstantiated->apply_placeholder_holder($feedbacktext);

        $individualfeedback = count($question->prts) == 1;
        if ($individualfeedback) {
            $overallfeedback = '';
        } else {
            $overallfeedback = $this->overall_standard_prt_feedback($qa, $question, $response);
        }

        // Replace any PRT feedback.
        $allempty = true;
        foreach ($question->prts as $index => $prt) {
            $feedback = $this->prt_feedback($index, $response, $qa, $options, $prt->get_feedbackstyle());
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
     * Slightly complex rules for what feedback to display.
     * @param string $name the PRT name.
     * @param array $response the most recent student response.
     * @param question_attempt $qa the question attempt to display.
     * @param question_display_options $options controls what should and should not be displayed.
     * @param int feedbackstyle whether and how to include the standard.
     *      'Your answer is partially correct' bit at the start of the feedback.
     * @return string nicely formatted feedback, for display.
     */
    protected function prt_feedback($name, $response, question_attempt $qa,
            question_display_options $options, int $feedbackstyle) {
        $question = $qa->get_question();

        $relevantresponse = $this->get_applicable_response_for_prt($name, $response, $qa);
        if (is_null($relevantresponse)) {
            return '';
        }
        $result = $question->get_prt_result($name, $relevantresponse, $qa->get_state()->is_finished());
        return $this->prt_feedback_display($name, $qa, $question, $result, $options, $feedbackstyle);
    }

    /**
     * Actually generate the display of the PRT feedback.
     * @param string $name the PRT name.
     * @param question_attempt $qa the question attempt to display.
     * @param question_definition $question the question being displayed.
     * @param prt_evaluatable $result the results to display.
     * @param question_display_options $options controls what should and should not be displayed.
     * @param feedbackstyle styles the type of feedback.
     * @return string nicely formatted feedback, for display.
     */
    protected function prt_feedback_display($name, question_attempt $qa,
            question_definition $question, prt_evaluatable $result,
            question_display_options $options, $feedbackstyle) {
        $err = '';
        if ($result->get_errors()) {
            $err = stack_string('prtruntimeerror',
                ['prt' => $name, 'error' => implode(' ', $result->get_errors())]);
        }

        $feedback = $result->get_feedback(new castext2_qa_processor($qa));
        // The feedback does not come as bits anymore the whole thing is concatenated in CAS
        // and CASText converts any formats to HTML already, pluginfiles as well.
        $feedback = format_text(stack_maths::process_display_castext($feedback, $this),
            FORMAT_HTML, ['noclean' => true, 'para' => false, 'allowid' => true]);
        // Replace the secured bits.
        $feedback = $result->apply_placeholder_holder($feedback);

        $gradingdetails = '';
        if ($result->get_valid() && $qa->get_behaviour_name() == 'adaptivemultipart'
                && $options->marks >= question_display_options::MARK_AND_MAX) {
            $renderer = $this->page->get_renderer('qbehaviour_adaptivemultipart');
            $gradingdetails = $renderer->render_adaptive_marks(
                $qa->get_behaviour()->get_part_mark_details($name), $options);
        }

        $standardfeedback = $this->standard_prt_feedback($qa, $question, $result, $feedbackstyle);

        $tag = 'div';
        switch ($feedbackstyle) {
            case 0:
                // Formative PRT.
                $fb = $err . $feedback;
                break;
            case 1:
                $fb = $standardfeedback . $err . $feedback . $gradingdetails;
                break;
            case 2:
                // Compact.
                $fb = $standardfeedback . $err . $feedback;
                $tag = 'span';
                break;
            case 3:
                // Symbolic.
                $fb = $standardfeedback . $err;
                $tag = 'span';
                break;
            default:
                echo "i is not equal to 0, 1 or 2";
        }

        return html_writer::nonempty_tag($tag, $fb, ['class' => 'stackprtfeedback stackprtfeedback-' . $name]);
    }

    /**
     * Generate the standard PRT feedback for a particular score.
     * @param question_attempt $qa the question attempt to display.
     * @param question_definition $question the question being displayed.
     * @param prt_evaluatable $result the results to display.
     * @param feedbackstyle styles the type of feedback.
     * @return string nicely standard feedback, for display.
     */
    protected function standard_prt_feedback(question_attempt $qa, question_definition $question,
            prt_evaluatable $result, $feedbackstyle) {
        if (!$result->is_evaluated()) {
            return '';
        }
        // Don't give standard feedback when we have errors.
        if (count($result->get_errors()) != 0) {
            return '';
        }

        $state = question_state::graded_state_for_fraction($result->get_score());
        $class = $state->get_feedback_class();

        // Compact and symbolic only.
        if ($feedbackstyle === 2 || $feedbackstyle === 3) {
            $s = get_string('symbolicprt' . $class . 'feedback', 'qtype_stack');
            return html_writer::tag('span', $s, ['class' => $class]);
        }

        // If called out of order.
        if ($question->castextprocessor === null) {
            $question->castextprocessor = new castext2_qa_processor($qa);
        }

        $field = 'prt' . $class . 'instantiated';
        if ($question->$field) {
            // NOTE: assume that we have no holder related content here.
            return html_writer::tag('div', $question->format_text(
                    stack_maths::process_display_castext($question->$field->get_rendered($question->castextprocessor), $this),
                    FORMAT_HTML, // All CASText2 processed content has already been formatted to HTML.
                    $qa, 'qtype_stack', $field, $question->id), ['class' => $class]);
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
            if (is_null($result->get_valid())) {
                continue;
            }

            $fraction += $result->get_fraction();
        }

        if (is_null($fraction)) {
            return '';
        }

        $result = new prt_evaluatable('', $fraction, new castext2_static_replacer([]), []);
        // This is overall, so we fix the PRT feedbackstyle style = 1 to get the default type of feedback.
        return $this->standard_prt_feedback($qa, $qa->get_question(), $result, 1);
    }

    // phpcs:ignore moodle.Commenting.MissingDocblock.Function
    protected function hint(question_attempt $qa, question_hint $hint) {
        if (empty($hint->hint)) {
            return '';
        }
        $question = $qa->get_question();
        $hinttext = $question->get_hint_castext($hint);

        // If called out of order.
        if ($question->castextprocessor === null) {
            $question->castextprocessor = new castext2_qa_processor($qa);
        }

        $newhint = new question_hint($hint->id,
                stack_maths::process_display_castext($hinttext->get_rendered($question->castextprocessor), $this),
                FORMAT_HTML // All CASText2 processed content has already been formatted to HTML.
            );

        return html_writer::nonempty_tag('div',
            $question->format_hint($newhint, $qa), ['class' => 'hint']);
    }

    // phpcs:ignore moodle.Commenting.MissingDocblock.Function
    public function correct_response(question_attempt $qa) {
        $question = $qa->get_question();
        return '<hr />'.$question->format_correct_response($qa);
    }

    // phpcs:ignore moodle.Commenting.MissingDocblock.Function
    public function general_feedback(question_attempt $qa) {
        $question = $qa->get_question();
        if (empty($question->generalfeedback)) {
            return '';
        }

        // If called out of order.
        if ($question->castextprocessor === null) {
            $question->castextprocessor = new castext2_qa_processor($qa);
        }

        $gf = $qa->get_question()->format_text(stack_maths::process_display_castext(
                $question->get_generalfeedback_castext()->get_rendered($question->castextprocessor), $this),
                FORMAT_HTML, // All CASText2 processed content has already been formatted to HTML.
                $qa, 'question', 'generalfeedback', $question->id);
        // Replace the secured bits.
        return $question->get_generalfeedback_castext()->apply_placeholder_holder($gf);
    }

    // phpcs:ignore moodle.Commenting.MissingDocblock.Function
    public function question_description(question_attempt $qa) {
        $question = $qa->get_question();
        if (empty($question->questiondescription)) {
            return '';
        }

        // If called out of order.
        if ($question->castextprocessor === null) {
            $question->castextprocessor = new castext2_qa_processor($qa);
        }

        $qd = $qa->get_question()->format_text(stack_maths::process_display_castext(
            $question->get_questiondescription_castext()->get_rendered($question->castextprocessor), $this),
            FORMAT_HTML, // All CASText2 processed content has already been formatted to HTML.
            $qa, 'qtype_stack', 'questiondescription', $question->id);
        // Replace the secured bits.
        return $question->get_questiondescription_castext()->apply_placeholder_holder($qd);
    }

    /**
     * Render a fact sheet.
     * @param string $name the title of the fact sheet.
     * @param string $fact the contents of the fact sheet.
     */
    public function fact_sheet($name, $fact) {
        $name = html_writer::tag('h5', $name);
        return html_writer::tag('div', $name.$fact, ['class' => 'factsheet']);
    }
}

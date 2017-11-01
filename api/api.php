<?php

/*
 * Minimal functionality needed to display and grade a question in a stateless way.
 */

require_once("apilib.php");
require_once(__DIR__ . '/../question.php');

/*
 * Dummy classes to allow STACK's question.php to extend it.
 */
class question_graded_automatically_with_countback {

}

interface question_automatically_gradable_with_multiple_parts {

}

/*
 *
 */
class qtype_stack_api {

    /*
     * This is based closely on the render.php formulation_and_controls.
     *
     */
    public function formulation_and_controls($question, $attempt, $options, $fieldprefix) {

        /*****************************************************************************/
        $response = new stdClass();
        // The "questiontext" is what is shown to the student.
        // Note, we need a lot of options to formulate this correctly.
        // For example, are inputs still editable?  Do we display feedback/partial marks in multi-part questions?
        $response->questiontext = null;
        // The overall score for this attempt.  Must be a float between 0 and 1.
        $response->score = null;
        // The number of marks available for the question, as specified in the question itself.
        $response->defaultmark = $question->defaultmark;
        // The "generalfeedback" is a Moodle term for a "worked solution".
        $response->generalfeedback = null;
        // This is feedback to students of the form "A correct answer is ...".
        // This is a text field, not an array of correct answers in Maxima syntax.
        $response->formatcorrectresponse = null;

        // TODO: should the JSON format be used, or would an array be better here?
        // One option for the API is that all fields in this class return a flat text object or number. In that case JSON.
        // This affects the next two fields.

        // This gives a JSON encoded summary of the status of the attempt: "valid", "invalid" etc.
        $response->summariseresponse = null;
        // This gives a JSON encoded summary of the status of the potential response trees.
        $response->answernotes = null;

        /*****************************************************************************/

        $questiontext = $question->questiontextinstantiated;
        // For the minimal API we concatinate the two.
        $questiontext .= $question->specificfeedback;

        // Replace inputs.
        $inputstovaldiate = array();
        $qaid = null;
        foreach ($question->inputs as $name => $input) {

            $state = $question->get_input_state($name, $attempt);
            if (property_exists($options, 'validate')) {
                $skip_validation = stack_input::BLANK == $state->status || stack_input::INVALID == $state->status;

                if (!$skip_validation && $input->requires_validation() && '' !== $state->contents) {
                    $attempt[$name.'_val'] = $input->contents_to_maxima($state->contents);
                }
            }

            $fieldname = $fieldprefix.$name;
            $tavalue = $question->get_session_variable($name);
            $state = $question->get_input_state($name, $attempt);

            $questiontext = str_replace("[[input:{$name}]]",
            $input->render($state, $fieldname, $options->readonly, $tavalue),
            $questiontext);

            $questiontext = $input->replace_validation_tags($state, $fieldname, $questiontext);

            if ($input->requires_validation()) {
                $inputstovaldiate[] = $name;
            }
        }

        $weights = $question->get_parts_and_weights();
        $scores = array();
        $notes = array();

        // Replace PRTs.
        foreach ($question->prts as $index => $prt) {
            $feedback = '';
            $result = $question->get_prt_result($index, $attempt, false);
//            echo "<pre>"; var_dump($result->get_feedback()); echo "</pre>";
            $resultfeedback = $result->get_feedback();
            $scores[$index] = $result->score;
            $notes[$index] = implode(' | ', $result->answernotes);
            foreach ($resultfeedback as $fb) {
                $feedback .= $fb->feedback;
            }
            $fbct = new stack_cas_text($feedback, $result->cascontext);
            if ($options->feedback) {
                $feedback = html_writer::nonempty_tag('div', $fbct->get_display_castext(),
                        array('class' => 'stackprtfeedback stackprtfeedback-' . $name));
            } else {
                // We want to show the CAS errors from the PRT.
                $feedback = html_writer::nonempty_tag('div', $result->errors,
                        array('class' => 'stackprtfeedback stackprtfeedback-' . $name));
            }
            if ($options->score) {
                if (null !== $result->score) {
                    // TODO: language support etc.
                    $feedback .= "<p class='stackpartmark'>Your mark for this part is ".$result->score.".</p>";
                }
            }
            $target = "[[feedback:{$index}]]";
            $questiontext = str_replace($target, $feedback, $questiontext);
        }
        $response->questiontext = stack_maths::process_display_castext($questiontext);
        $response->answernotes = json_encode($notes);

        // Sort out the "marks", called a "score".
        // TODO: STACK penalty scheme is not applied.
        $score = 0;
        foreach ($weights as $prt => $weight) {
            $score += $weights[$prt] * $scores[$prt];
        }
        $response->score = $score;
        // You will probably want to do the following somewhere.
        // $score = $score * $question->defaultmark;

        // Add in general feedback.
        $generalfeedback = $question->get_generalfeedback_castext();
        $response->generalfeedback = stack_maths::process_display_castext($generalfeedback->get_display_castext());
        $response->formatcorrectresponse = stack_maths::process_display_castext($question->format_correct_response(null));
        $response->summariseresponse = $question->summarise_response_json($attempt);

        /*
        // Initialise automatic validation, if enabled.
        if ($qaid && stack_utils::get_config()->ajaxvalidation) {
            $this->page->requires->yui_module('moodle-qtype_stack-input',
                    'M.qtype_stack.init_inputs', array($inputstovaldiate, $qaid, $qa->get_field_prefix()));
        }
        */

        return $response;
    }

    public function format_correct_response($qa) {
        $feedback = '';
        $inputs = stack_utils::extract_placeholders($this->questiontextinstantiated, 'input');
        foreach ($inputs as $name) {
            $input = $this->inputs[$name];
            $feedback .= html_writer::tag('p', $input->get_teacher_answer_display($this->session->get_value_key($name, true),
                    $this->session->get_display_key($name)));
        }
        return stack_ouput_castext($feedback);
    }

    public function initialise_question(array $q) {

        $question = new qtype_stack_question();
        $question->type = 'stack';
        $question->defaultmark               = (float) $q['default_mark'];
        $question->penalty                   = (float) $q['penalty'];

        $question->name                      = (string) $q['name'];
        $question->questiontext              = (string) $q['question_html'];
        $question->questiontextformat        = 'html';
        $question->generalfeedback           = (string) $q['worked_solution_html'];
        $question->generalfeedbackformat     = 'html';
        $question->questionvariables         = (string) $q['variables'];
        $question->questionnote              = (string) $q['note'];
        $question->specificfeedback          = (string) $q['specific_feedback_html'];
        $question->specificfeedbackformat    = 'html';
        $question->prtcorrect                = (string) $q['prt_correct_html'];
        $question->prtcorrectformat          = 'html';
        $question->prtpartiallycorrect       = (string) $q['prt_partially_correct_html'];
        $question->prtpartiallycorrectformat = 'html';
        $question->prtincorrect              = (string) $q['prt_incorrect_html'];
        $question->prtincorrectformat        = '';
        $question->variantsselectionseed     = "";

        $question->options = new stack_options();
        $stringoptions = array(
            'multiplicationsign' => 'multiplication_sign',
            'complexno' => 'complex_no',
            'inversetrig' => 'inverse_trig',
            'matrixparens' => 'matrix_parens'
            );
        foreach ($stringoptions as $key => $value) {
            $opt = trim((string) $q['options'][$value]);
            if ('' != $opt) {
                $question->options->set_option($key, $opt);
            }
        }
        $booloptions = array(
            'sqrtsign' => 'sqrt_sign',
            'assumepos' => 'assume_positive',
            'assumereal' => 'assume_real',
            'simplify' => 'simplify'
        );
        foreach ($booloptions as $key => $value) {
            $opt = (bool) $q['options'][$value];
            $question->options->set_option($key, $opt);
        }

        $requiredparams = stack_input_factory::get_parameters_used();
        // Note, we need to increment over this variable to get at the SimpleXMLElement array elements.
        $k=-1;
        foreach ($q['inputs'] as $key => $inputdata) {
            $name = (string) $key;
            $type = (string) $inputdata['type'];
            $allparameters = array(
                'boxWidth'        => (int) $inputdata['box_size'],
                'strictSyntax'    => (bool) $inputdata['strict_syntax'],
                'insertStars'     => (int) $inputdata['insert_stars'],
                'syntaxHint'      => (string) $inputdata['syntax_hint'],
                'syntaxAttribute' => (string) $inputdata['syntax_attribute'],
                'forbidWords'     => (string) $inputdata['forbid_words'],
                'allowWords'      => (string) $inputdata['allow_words'],
                'forbidFloats'    => (bool) $inputdata['forbid_float'],
                'lowestTerms'     => (bool) $inputdata['require_lowest_terms'],
                'sameType'        => (bool) $inputdata['check_answer_type'],
                'mustVerify'      => (bool) $inputdata['must_verify'],
                'showValidation'  => (string) $inputdata['show_validations'],
                'options'         => (string) $inputdata['options'],
            );
            $parameters = array();
            foreach ($requiredparams[$type] as $paramname) {
                if ($paramname == 'inputType') {
                continue;
                }
                $parameters[$paramname] = $allparameters[$paramname];
            }
            $question->inputs[$name] = stack_input_factory::make(
                $inputdata['type'], $name, (string) $inputdata['model_answer'], $question->options, $parameters);
        }

        $totalvalue = 0;
        foreach ($q['response_trees'] as $key => $prtdata) {

            $totalvalue += (float) $prtdata['value'];
        }

        foreach ($q['response_trees'] as $key => $prtdata) {
            $name = (string) $key;
            $nodes = array();

            foreach ($prtdata['nodes'] as $nodedata) {
                $sans = new stack_cas_casstring((string) $nodedata['answer']);
                $sans->get_valid('t');
                $tans = new stack_cas_casstring((string) $nodedata['model_answer']);
                $tans->get_valid('t');

                if (is_null($nodedata['F']['penalty']) || $nodedata['F']['penalty'] === '') {
                    $falsepenalty = $question->penalty;
                } else {
                    $falsepenalty = (float) $nodedata['F']['penalty'];
                }
                if (is_null($nodedata['T']['penalty']) || $nodedata['T']['penalty'] === '') {
                    $truepenalty = $question->penalty;
                } else {
                    $truepenalty = (float) $nodedata['T']['penalty'];
                }

                $nodeid = (int) $nodedata['name'];
                $quiet = $nodedata['quiet'];
                $node = new stack_potentialresponse_node($sans, $tans,
                        (string) $nodedata['answer_test'], (string) $nodedata['test_options'],
                        $quiet, '', $nodeid);
                $node->add_branch(0, (string) $nodedata['F']['score_mode'], (float) $nodedata['F']['score'],
                        $falsepenalty, (int) $nodedata['F']['next_node'],
                        (string) $nodedata['F']['feedback_html'],
                        'html',
                        $nodedata['F']['answer_note']);
                $node->add_branch(1, (string) $nodedata['T']['score_mode'], (float) $nodedata['T']['score'],
                        $falsepenalty, (int) $nodedata['T']['next_node'],
                        (string) $nodedata['T']['feedback_html'],
                        'html',
                        $nodedata['T']['answer_note']);
                $nodes[$nodeid] = $node;
            }
            if ($prtdata['feedback_variables']) {
                $feedbackvariables = new stack_cas_keyval((string) $prtdata['feedback_variables'], $question->options, null, 't');
                $feedbackvariables = $feedbackvariables->get_session();
            } else {
                $feedbackvariables = null;
            }

            $question->prts[$name] = new stack_potentialresponse_tree($name, '',
                (bool) $prtdata['auto_simplify'], (float) $prtdata['value'] / $totalvalue,
                $feedbackvariables, $nodes, (int) $prtdata['first_node']);
         }
         return $question;
    }

    public function initialise_question_from_xml($questionxml) {

        $xml = simplexml_load_string($questionxml, null, LIBXML_NOCDATA);
        $json = json_encode($xml);
        $array = json_decode($json, true);
        $qob = new SimpleXMLElement($questionxml);
        $questionob = $qob->question;

        $question = new qtype_stack_question();
        $question->type = 'stack';
        $question->defaultmark               = (float) $questionob->defaultgrade;
        $question->penalty                   = (float) $questionob->penalty;

        $question->name                      = (string) $questionob->name->text;
        $question->questiontext              = (string) $questionob->questiontext->text;
        $question->questiontextformat        = 'html';
        $question->generalfeedback           = (string) $questionob->generalfeedback->text;
        $question->generalfeedbackformat     = 'html';
        $question->questionvariables         = (string) $questionob->questionvariables->text;
        $question->questionnote              = (string) $questionob->questionnote->text;
        $question->specificfeedback          = (string) $questionob->specificfeedback->text;
        $question->specificfeedbackformat    = 'html';
        $question->prtcorrect                = (string) $questionob->prtcorrect->text;
        $question->prtcorrectformat          = 'html';
        $question->prtpartiallycorrect       = (string) $questionob->prtpartiallycorrect->text;
        $question->prtpartiallycorrectformat = 'html';
        $question->prtincorrect              = (string) $questionob->prtincorrect->text;
        $question->prtincorrectformat        = (string) $questionob->prtincorrectformat->text;
        $question->variantsselectionseed     = (string) $questionob->variantsselectionseed->text;

        $question->options = new stack_options();
        $stringoptions = array('multiplicationsign', 'complexno', 'inversetrig', 'matrixparens');
        foreach ($stringoptions as $optionname) {
            $opt = trim((string) $questionob->$optionname);
            if ('' != $opt) {
                $question->options->set_option($optionname, $opt);
            }
        }
        $booloptions = array('sqrtsign', 'assumepos', 'assumereal');
        foreach ($booloptions as $optionname) {
            $opt = (bool) $questionob->$optionname;
            $question->options->set_option($optionname, $opt);
        }
        // One exceptional case.
        $opt = (bool) $questionob->questionsimplify;
        $question->options->set_option('simplify', $opt);

        $requiredparams = stack_input_factory::get_parameters_used();
        // Note, we need to increment over this variable to get at the SimpleXMLElement array elements.
        $k=-1;
        foreach ($questionob->input as $key => $input) {
            $k++;
            $inputdata=$questionob->input[$k];
            $name = (string) $inputdata->name;
            $type = (string) $inputdata->type;
            $allparameters = array(
                'boxWidth'        => (int) $inputdata->boxsize,
                'strictSyntax'    => (bool) $inputdata->strictsyntax,
                'insertStars'     => (int) $inputdata->insertstars,
                'syntaxHint'      => (string) $inputdata->syntaxhint,
                'syntaxAttribute' => (string) $inputdata->syntaxattribute,
                'forbidWords'     => (string) $inputdata->forbidwords,
                'allowWords'      => (string) $inputdata->allowwords,
                'forbidFloats'    => (bool) $inputdata->forbidfloat,
                'lowestTerms'     => (bool) $inputdata->requirelowestterms,
                'sameType'        => (bool) $inputdata->checkanswertype,
                'mustVerify'      => (bool) $inputdata->mustverify,
                'showValidation'  => (string) $inputdata->showvalidation,
                'options'         => (string) $inputdata->options,
            );
            $parameters = array();
            foreach ($requiredparams[$type] as $paramname) {
                if ($paramname == 'inputType') {
                continue;
                }
                $parameters[$paramname] = $allparameters[$paramname];
            }
            $question->inputs[$name] = stack_input_factory::make(
                $inputdata->type, $name, (string) $inputdata->tans, $question->options, $parameters);
        }

        $totalvalue = 0;
        $k = -1;
        foreach ($questionob->prt as $key => $prt) {
            $k++;
            $prtdata = $questionob->prt[$k];
            $totalvalue += (float) $prtdata->value;
        }

        $k = -1;
        foreach ($questionob->prt as $key => $prt) {
            $k++;
            $prtdata = $questionob->prt[$k];
            $name = (string) $prtdata->name;
            $nodes = array();

            $n = -1;
            foreach ($prtdata->node as $dummynode) {
                $n++;
                $nodedata = $prtdata->node[$n];
                $sans = new stack_cas_casstring((string) $nodedata->sans);
                $sans->get_valid('t');
                $tans = new stack_cas_casstring((string) $nodedata->tans);
                $tans->get_valid('t');

                if (is_null($nodedata->falsepenalty) || $nodedata->falsepenalty === '') {
                    $falsepenalty = $question->penalty;
                } else {
                    $falsepenalty = (float) $nodedata->falsepenalty;
                }
                if (is_null($nodedata->truepenalty) || $nodedata->truepenalty === '') {
                    $truepenalty = $question->penalty;
                } else {
                    $truepenalty = (float) $nodedata->truepenalty;
                }

                $nodeid = (int) $nodedata->name;
                $quiet = true;
                if ('0' == $nodedata->quiet) {
                    $quiet = false;
                }

                $node = new stack_potentialresponse_node($sans, $tans,
                        (string) $nodedata->answertest, (string) $nodedata->testoptions,
                        $quiet, '', $nodeid);
                $node->add_branch(0, (string) $nodedata->falsescoremode, (float) $nodedata->falsescore,
                        $falsepenalty, (int) $nodedata->falsenextnode,
                        (string) $nodedata->falsefeedback->text,
                        $nodedata->falsefeedbackformat,
                        $nodedata->falseanswernote);
                $node->add_branch(1, (string) $nodedata->truescoremode, (float) $nodedata->truescore,
                        $truepenalty, (int) $nodedata->truenextnode,
                        (string) $nodedata->truefeedback->text,
                        $nodedata->truefeedbackformat,
                        $nodedata->trueanswernote);
                $nodes[$nodeid] = $node;
            }
            if ($prtdata->feedbackvariables) {
                $feedbackvariables = new stack_cas_keyval((string) $prtdata->feedbackvariables->text, $question->options, null, 't');
                $feedbackvariables = $feedbackvariables->get_session();
            } else {
                $feedbackvariables = null;
            }

            $question->prts[$name] = new stack_potentialresponse_tree($name, '',
                (bool) $prtdata->autosimplify, (float) $prtdata->value / $totalvalue,
                $feedbackvariables, $nodes, (int) $prtdata->firstnodename);
    }

    return($question);
    }


    /*
     * This function writes the maximalocal.mac file into the data directory,
     * communicating the local setting to maxima.
     */
    public function install() {
        global $CFG;

        $helper = new stack_cas_configuration;
        $helper->create_maximalocal();
        $helper->create_auto_maxima_image();

        echo "You must now update the setting <tt>maximacommand</tt> in <tt>config.php</tt> to be <pre>". $CFG->maximacommand . "</pre>";
        die();
    }
}

<?php

/*
 * Minimal functionality needed to display and grade a question in a stateless way.
 */

require_once("apilib.php");

/*
 * Dummy classes to allow STACK's question.php to extend it.
 */
class question_graded_automatically_with_countback {

}

interface question_automatically_gradable_with_multiple_parts{

}

/*
 * 
 */
class qtype_stack_api {

    /* 
     * This is based closely on the render.php version.
     * 
     */
    public function formulation_and_controls($question, $response, $options, $fieldprefix) {

        $questiontext = $question->questiontextinstantiated;
        // For the minimal API we concatinate the two.
        $questiontext .= $question->specificfeedback;

        // Replace inputs.
        $inputstovaldiate = array();
        $qaid = null;
        foreach ($question->inputs as $name => $input) {
            // Get the actual value of the teacher's answer at this point.
            $tavalue = $question->get_session_variable($name);

            $fieldname = $fieldprefix.$name;
            $state = $question->get_input_state($name, $response);

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

        // Replace PRTs.
        foreach ($question->prts as $index => $prt) {
            $feedback = '';
            $result = $question->get_prt_result($index, $response, false);
            //echo "<pre>"; print_r($result); echo "</pre>";
            $resultfeedback = $result->get_feedback();
            $scores[$index] = $result->score;
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
                    $feedback .= "<p>Your mark for this part is ".$result->score.".</p>";
                }
            }

            $target = "[[feedback:{$index}]]";
            $questiontext = str_replace($target, $feedback, $questiontext);
        }

        if ($options->score) {
            $score = 0;
            foreach ($weights as $prt => $weight) {
                $score += $weights[$prt] * $scores[$prt];
            }
            $score = $score * $question->defaultmark;
            // TODO: language support etc.
            $questiontext .= "<p>Your mark for this attempt is ".$score.".</p>";
        }

        // Add "generalfeedback" (worked solution), if it exists, and correct answer.
        if ($options->generalfeedback) {
            $questiontext .=  "<hr />";
            $generalfeedback = $question->get_generalfeedback_castext();
            $questiontext .= $generalfeedback->get_display_castext();

            $questiontext .= $question->format_correct_response(null);
        }
        // Now format the questiontext.  This should be done after the subsitutions of inputs and PRTs.
        $questiontext = stack_maths::process_display_castext($questiontext);

        /*
        // Initialise automatic validation, if enabled.
        if ($qaid && stack_utils::get_config()->ajaxvalidation) {
            $this->page->requires->yui_module('moodle-qtype_stack-input',
                    'M.qtype_stack.init_inputs', array($inputstovaldiate, $qaid, $qa->get_field_prefix()));
        }
        */

        return $questiontext;
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

        echo "You must now update your maximacommand to be <pre>". $CFG->maximacommand . "</pre>";
    }
}

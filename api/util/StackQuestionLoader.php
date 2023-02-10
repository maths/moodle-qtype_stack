<?php

namespace api\util;
use SimpleXMLElement;

require_once(__DIR__ . '/../../question.php');

/**
 * TODO: Rework, dont use legacy classes
 * Converts question xml into usable format
 */
class StackQuestionLoader
{
    static public function loadXML($xml) {
        //TODO: Consider defaults
        $xmlData = new SimpleXMLElement($xml);
        $question = new \qtype_stack_question();

        //Collect included files
        $files = array();
        if($xmlData->question->questiontext) {
            $files = array_merge($files, StackQuestionLoader::handleFiles($xmlData->question->questiontext->file));
        }
        if($xmlData->question->generalfeedback) {
            $files = array_merge($files, StackQuestionLoader::handleFiles($xmlData->question->generalfeedback->file));
        }
        if($xmlData->question->specificfeedback) {
            $files = array_merge($files, StackQuestionLoader::handleFiles($xmlData->question->specificfeedback->file));
        }
        $question->pluginfiles = $files;

        //Based on moodles base question type
        $question->name = (string) $xmlData->question->name->text;
        $question->questiontext = (string) $xmlData->question->questiontext->text;
        $question->questiontextformat = (string) $xmlData->question->questiontext['format'];
        $question->generalfeedback = (string) $xmlData->question->generalfeedback->text;
        $question->generalfeedbackformat = (string) $xmlData->question->generalfeedback['format'];
        $question->defaultmark = isset($xmlData->question->defaultgrade) ? (float) $xmlData->question->defaultgrade : 1.0;
        $question->penalty = isset($xmlData->question->penalty) ? (float) $xmlData->question->penalty : 0.1;


        //Based on initialise_question_instance from questiontype.php
        $question->stackversion              = (string) $xmlData->question->stackversion->text;
        $question->questionvariables         = (string) $xmlData->question->questionvariables->text;
        $question->questionnote              = (string) $xmlData->question->questionnote->text;
        $question->specificfeedback          = (string) $xmlData->question->specificfeedback->text;
        $question->specificfeedbackformat    = (string) $xmlData->question->specificfeedback['format'];
        if(isset($xmlData->question->prtcorrect->text)) {
            $question->prtcorrect                = (string) $xmlData->question->prtcorrect->text;
            $question->prtcorrectformat          = (string) $xmlData->question->prtcorrect['format'];
        } else {
            $question->prtcorrect = get_string('defaultprtcorrectfeedback', null, null);
            $question->prtcorrectformat = 'html';
        }
        if(isset($xmlData->question->prtpartiallycorrect->text)) {
            $question->prtpartiallycorrect = (string)$xmlData->question->prtpartiallycorrect->text;
            $question->prtpartiallycorrectformat = (string)$xmlData->question->prtpartiallycorrect['format'];
        } else {
            $question->prtpartiallycorrect = get_string('defaultprtpartiallycorrectfeedback', null, null);
            $question->prtpartiallycorrectformat = 'html';
        }
        if(isset($xmlData->question->prtincorrect->text)) {
            $question->prtincorrect = (string)$xmlData->question->prtincorrect->text;
            $question->prtincorrectformat = (string)$xmlData->question->prtincorrect['format'];
        } else {
            $question->prtincorrect = get_string('defaultprtincorrectfeedback', null, null);
            $question->prtincorrectformat = 'html';
        }
        $question->variantsselectionseed     = (string) $xmlData->question->variantsselectionseed;
        $question->compiledcache             = [];

        $question->options = new \stack_options();
        $question->options->set_option('multiplicationsign', isset($xmlData->question->multiplicationsign) ? (string) $xmlData->question->multiplicationsign : 'dot');
        $question->options->set_option('complexno',          isset($xmlData->question->complexno) ? (string) $xmlData->question->complexno : 'i');
        $question->options->set_option('inversetrig',        isset($xmlData->question->inversetrig) ? (string) $xmlData->question->inversetrig : 'cos-1');
        $question->options->set_option('logicsymbol',        isset($xmlData->question->logicsymbol) ? (string) $xmlData->question->logicsymbol : 'lang');
        $question->options->set_option('matrixparens',       isset($xmlData->question->matrixparens) ? (string) $xmlData->question->matrixparens : '[');
        $question->options->set_option('sqrtsign',    isset($xmlData->question->sqrtsign) ? StackQuestionLoader::parseBoolean($xmlData->question->sqrtsign) : true);
        $question->options->set_option('simplify',    isset($xmlData->question->questionsimplify) ? StackQuestionLoader::parseBoolean($xmlData->question->questionsimplify) : true);
        $question->options->set_option('assumepos',   isset($xmlData->question->assumepositive) ? StackQuestionLoader::parseBoolean($xmlData->question->assumepositive) : false);
        $question->options->set_option('assumereal',  isset($xmlData->question->assumereal) ? StackQuestionLoader::parseBoolean($xmlData->question->assumereal) : false);

        $inputMap = array();
        foreach ($xmlData->question->input as $input) {
            $inputMap[(string) $input->name] = $input;
        }

        $requiredparams = \stack_input_factory::get_parameters_used();
        foreach ($inputMap as $name => $inputdata) {
            $allparameters = array(
                'boxWidth'        => isset($inputdata->boxsize) ? (int) $inputdata->boxsize : 30,
                'strictSyntax'    => isset($inputdata->strictsyntax) ? StackQuestionLoader::parseBoolean($inputdata->strictsyntax) : true,
                'insertStars'     => isset($inputdata->insertstars) ? (int) $inputdata->insertstars : 1,
                'syntaxHint'      => isset($inputdata->syntaxhint) ? (string) $inputdata->syntaxhint : '',
                'syntaxAttribute' => isset($inputdata->syntaxattribute) ? (int) $inputdata->syntaxattribute : 0,
                'forbidWords'     => isset($inputdata->forbidwords) ? (string) $inputdata->forbidwords : '',
                'allowWords'      => isset($inputdata->allowwords) ? (string) $inputdata->allowwords : '',
                'forbidFloats'    => isset($inputdata->forbidfloat) ? StackQuestionLoader::parseBoolean($inputdata->forbidfloat) : true,
                'lowestTerms'     => isset($inputdata->requirelowestterms) ? StackQuestionLoader::parseBoolean($inputdata->requirelowestterms) : false,
                'sameType'        => isset($inputdata->checkanswertype) ? StackQuestionLoader::parseBoolean($inputdata->checkanswertype) : false,
                'mustVerify'      => isset($inputdata->mustverify) ? StackQuestionLoader::parseBoolean($inputdata->mustverify) : true,
                'showValidation'  => isset($inputdata->showvalidation) ? (int) $inputdata->showvalidation : 1,
                'options'         => isset($inputdata->options) ? (string) $inputdata->options : '',
            );
            $parameters = array();
            foreach ($requiredparams[(string) $inputdata->type] as $paramname) {
                if ($paramname == 'inputType') {
                    continue;
                }
                $parameters[$paramname] = $allparameters[$paramname];
            }
            $question->inputs[$name] = \stack_input_factory::make(
                (string) $inputdata->type, (string) $inputdata->name, (string) $inputdata->tans, $question->options, $parameters);
        }

        $totalvalue = 0;
        $allformative = true;
        foreach ($xmlData->question->prt as $prtdata) {
            // At this point we do not have the PRT method is_formative() available to us.
            if (((int) $prtdata->feedbackstyle) > 0) {
                $totalvalue += (float) $prtdata->value;
                $allformative = false;
            }
        }
        if (count($xmlData->question->prt) > 0 && !$allformative && $totalvalue < 0.0000001) {
            throw new \stack_exception('There is an error authoring your question. ' .
                'The $totalvalue, the marks available for the question, must be positive in question ' .
                $question->name);
        }

        foreach ($xmlData->question->prt as $prtdata) {
            $prtvalue = 0;
            if (!$allformative) {
                $prtvalue = ((float)$prtdata->value) / $totalvalue;
            }

            $data = new \stdClass();
            $data->name = (string) $prtdata->name;
            $data->autosimplify = isset($prtdata->autosimplify) ? StackQuestionLoader::parseBoolean($prtdata->autosimplify) : true;
            $data->feedbackstyle = isset($prtdata->feedbackstyle) ? (int) $prtdata->feedbackstyle : 1;
            $data->value = isset($prtdata->value) ? (float) $prtdata->value : 1.0;

            $data->feedbackvariables = (string) $prtdata->feedbackvariables->text;

            $data->nodes = array();
            foreach ($prtdata->node as $node) {
                $newNode = new \stdClass();

                $newNode->nodename = (string) $node->name;
                $newNode->answertest = isset($node->answertest) ? (string) $node->answertest : 'AlgEquiv';
                $newNode->sans = (string) $node->sans;
                $newNode->tans = (string) $node->tans;
                $newNode->testoptions = (string) $node->testoptions;
                $newNode->quiet = StackQuestionLoader::parseBoolean($node->quiet);

                $newNode->truescoremode = isset($node->truescoremode) ? (string) $node->truescoremode : 'add';
                $newNode->truescore = isset($node->truescore) ? (float) $node->truescore : 1.0;
                $newNode->truepenalty = isset($node->truepenalty) ? (float) $node->truepenalty : 0.0;
                $newNode->truenextnode = isset($node->truenextnode) ? (string) $node->truenextnode : '-1';
                $newNode->trueanswernote = (string) $node->trueanswernote;
                $newNode->truefeedback = (string) $node->truefeedback->text;

                $newNode->falsescoremode = isset($node->falsescoremode) ? (string) $node->falsescoremode : 'equals';
                $newNode->falsescore = isset($node->falsescore) ? (float) $node->falsescore : 0.0;
                $newNode->falsepenalty = isset($node->falsepenalty) ? (float) $node->falsepenalty : 0.0;
                $newNode->falsenextnode = isset($node->falsenextnode) ? (string) $node->falsenextnode : '-1';
                $newNode->falseanswernote = (string) $node->falseanswernote;
                $newNode->falsefeedback = (string) $node->falsefeedback->text;

                $data->nodes[(int) $node->name] = $newNode;
            }

            $question->prts[(string) $prtdata->name] = new \stack_potentialresponse_tree_lite($data,
                $prtvalue, $question);
        }

        $deployedSeeds = [];
        foreach ($xmlData->question->deployedseed as $seed) {
            $deployedSeeds[] = (int) $seed;
        }

        $question->deployedseeds = $deployedSeeds;

        return $question;
    }

    private static function handleFiles(\SimpleXMLElement $files) {
        $data = [];

        foreach ($files as $file) {
            $data[(string) $file['name']] = (string) $file;
        }

        return $data;
    }

    private static function parseBoolean(\SimpleXMLElement $element) {
        $v = (string) $element;
        if($v === "0") return false;
        if($v === "1") return true;

        throw new \stack_exception('invalid bool value');
    }
}

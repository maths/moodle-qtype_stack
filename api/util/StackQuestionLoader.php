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

// This script handles the various deploy/undeploy actions from questiontestrun.php.
//
// @copyright  2023 RWTH Aachen
// @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.

namespace api\util;
use SimpleXMLElement;
defined('MOODLE_INTERNAL') || die();
require_once(__DIR__ . '/../../question.php');
require_once(__DIR__ . '/../../stack/questiontest.php');
require_once(__DIR__ . '/../../stack/potentialresponsetreestate.class.php');

/**
 * TO-DO: Rework, dont use legacy classes
 * Converts question xml into usable format
 */
class StackQuestionLoader {
    public static function loadxml($xml, $includetests=false) {
        // TO-DO: Consider defaults.
        try {
            $xmldata = new SimpleXMLElement($xml);
        } catch (\Exception $e) {
            throw new \stack_exception("The provided file does not contain valid XML");
        }
        $question = new \qtype_stack_question();

        // Throw error if more then one question element is contained in the xml.
        if (count($xmldata->question) != 1) {
            throw new \stack_exception("The provided XML file does not contain exactly one question element");
        }

        if (((string) $xmldata->question->attributes()->type) !== "stack") {
            throw new \stack_exception("The provided question is not of type STACK");
        }

        // Collect included files.
        $files = [];
        if ($xmldata->question->questiontext) {
            $files = array_merge($files, self::handlefiles($xmldata->question->questiontext->file));
        }
        if ($xmldata->question->generalfeedback) {
            $files = array_merge($files, self::handlefiles($xmldata->question->generalfeedback->file));
        }
        if ($xmldata->question->specificfeedback) {
            $files = array_merge($files, self::handlefiles($xmldata->question->specificfeedback->file));
        }
        $question->pluginfiles = $files;

        // Based on moodles base question type.
        $question->name = (string) $xmldata->question->name->text;
        $question->questiontext = (string) $xmldata->question->questiontext->text;
        $question->questiontextformat = (string) $xmldata->question->questiontext['format'];
        $question->generalfeedback = (string) $xmldata->question->generalfeedback->text;
        $question->generalfeedbackformat = (string) $xmldata->question->generalfeedback['format'];
        $question->defaultmark = (array) $xmldata->question->defaultgrade ? (float) $xmldata->question->defaultgrade : 1.0;
        $question->penalty = (array) $xmldata->question->penalty ? (float) $xmldata->question->penalty : 0.1;

        // Based on initialise_question_instance from questiontype.php.
        $question->stackversion              = (string) $xmldata->question->stackversion->text;
        $question->questionvariables         = (string) $xmldata->question->questionvariables->text;
        $question->questionnote              = (string) $xmldata->question->questionnote->text;
        $question->specificfeedback          = (string) $xmldata->question->specificfeedback->text;
        $question->specificfeedbackformat    = (string) $xmldata->question->specificfeedback['format'];
        $question->questiondescription       = (string) $xmldata->question->questiondescription->text;
        $question->questiondescriptionformat = (string) $xmldata->question->questiondescription['format'];
        if (isset($xmldata->question->prtcorrect->text)) {
            $question->prtcorrect                = (string) $xmldata->question->prtcorrect->text;
            $question->prtcorrectformat          = (string) $xmldata->question->prtcorrect['format'];
        } else {
            $question->prtcorrect = get_string('defaultprtcorrectfeedback', null, null);
            $question->prtcorrectformat = 'html';
        }
        if (isset($xmldata->question->prtpartiallycorrect->text)) {
            $question->prtpartiallycorrect = (string)$xmldata->question->prtpartiallycorrect->text;
            $question->prtpartiallycorrectformat = (string)$xmldata->question->prtpartiallycorrect['format'];
        } else {
            $question->prtpartiallycorrect = get_string('defaultprtpartiallycorrectfeedback', null, null);
            $question->prtpartiallycorrectformat = 'html';
        }
        if (isset($xmldata->question->prtincorrect->text)) {
            $question->prtincorrect = (string)$xmldata->question->prtincorrect->text;
            $question->prtincorrectformat = (string)$xmldata->question->prtincorrect['format'];
        } else {
            $question->prtincorrect = get_string('defaultprtincorrectfeedback', null, null);
            $question->prtincorrectformat = 'html';
        }
        $question->variantsselectionseed     = (string) $xmldata->question->variantsselectionseed;
        $question->compiledcache             = [];

        $question->options = new \stack_options();
        $question->options->set_option(
            'multiplicationsign',
            (array) $xmldata->question->multiplicationsign ?
                (string) $xmldata->question->multiplicationsign : get_config('qtype_stack', 'multiplicationsign')
        );
        $question->options->set_option(
            'complexno',
            (array) $xmldata->question->complexno ?
                (string) $xmldata->question->complexno : get_config('qtype_stack', 'complexno')
        );
        $question->options->set_option(
            'inversetrig',
            (array) $xmldata->question->inversetrig ?
                (string) $xmldata->question->inversetrig : get_config('qtype_stack', 'inversetrig')
        );
        $question->options->set_option(
            'logicsymbol',
            (array) $xmldata->question->logicsymbol ?
                (string) $xmldata->question->logicsymbol : get_config('qtype_stack', 'logicsymbol')
        );
        $question->options->set_option(
            'matrixparens',
            (array) $xmldata->question->matrixparens ?
                (string) $xmldata->question->matrixparens : get_config('qtype_stack', 'matrixparens')
        );
        $question->options->set_option(
            'sqrtsign',
            (array) $xmldata->question->sqrtsign ?
                self::parseboolean($xmldata->question->sqrtsign) : (bool) get_config('qtype_stack', 'sqrtsign')
        );
        $question->options->set_option(
            'simplify',
            (array) $xmldata->question->questionsimplify ?
                self::parseboolean($xmldata->question->questionsimplify) : (bool) get_config('qtype_stack', 'questionsimplify')
        );
        $question->options->set_option(
            'assumepos',
            (array) $xmldata->question->assumepositive ?
                self::parseboolean($xmldata->question->assumepositive) : (bool) get_config('qtype_stack', 'assumepositive')
        );
        $question->options->set_option(
            'assumereal',
            (array) $xmldata->question->assumereal ?
                self::parseboolean($xmldata->question->assumereal) : (bool) get_config('qtype_stack', 'assumereal')
        );
        $question->options->set_option(
            'decimals',
            (array) $xmldata->question->decimals ?
                (string) $xmldata->question->decimals : get_config('qtype_stack', 'decimals')
        );
        $question->options->set_option(
            'scientificnotation',
            (array) $xmldata->question->scientificnotation ?
                                (string) $xmldata->question->scientificnotation : get_config('qtype_stack', 'scientificnotation')
        );

        $inputmap = [];
        foreach ($xmldata->question->input as $input) {
            $inputmap[(string) $input->name] = $input;
        }

        $requiredparams = \stack_input_factory::get_parameters_used();
        foreach ($inputmap as $name => $inputdata) {
            $allparameters = [
                'boxWidth'        => (array) $inputdata->boxsize ?
                    (int) $inputdata->boxsize : get_config('qtype_stack', 'inputboxsize'),
                'insertStars'     => (array) $inputdata->insertstars ?
                    (int) $inputdata->insertstars : get_config('qtype_stack', 'inputinsertstars'),
                'syntaxHint'      => isset($inputdata->syntaxhint) ? (string) $inputdata->syntaxhint : '',
                'syntaxAttribute' => (array) $inputdata->syntaxattribute ? (int) $inputdata->syntaxattribute : 0,
                'forbidWords'     => isset($inputdata->forbidwords) ?
                    (string) $inputdata->forbidwords : get_config('qtype_stack', 'inputforbidwords'),
                'allowWords'      => isset($inputdata->allowwords) ? (string) $inputdata->allowwords : '',
                'forbidFloats'    => (array) $inputdata->forbidfloat ?
                    self::parseboolean($inputdata->forbidfloat) : (bool) get_config('qtype_stack', 'inputforbidfloat'),
                'lowestTerms'     => (array) $inputdata->requirelowestterms ?
                    self::parseboolean($inputdata->requirelowestterms) :
                    (bool) get_config('qtype_stack', 'inputrequirelowestterms'),
                'sameType'        => (array) $inputdata->checkanswertype ?
                    self::parseboolean($inputdata->checkanswertype) : (bool) get_config('qtype_stack', 'inputcheckanswertype'),
                'mustVerify'      => (array) $inputdata->mustverify ?
                    self::parseboolean($inputdata->mustverify) : (bool) get_config('qtype_stack', 'inputmustverify'),
                'showValidation'  => (array) $inputdata->showvalidation ?
                    (int) $inputdata->showvalidation : get_config('qtype_stack', 'inputshowvalidation'),
                'options'         => isset($inputdata->options) ? (string) $inputdata->options : '',
            ];
            $parameters = [];
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
        foreach ($xmldata->question->prt as $prtdata) {
            // At this point we do not have the PRT method is_formative() available to us.
            if (((int) $prtdata->feedbackstyle) > 0) {
                $totalvalue += (float) $prtdata->value;
                $allformative = false;
            }
        }
        if (count($xmldata->question->prt) > 0 && !$allformative && $totalvalue < 0.0000001) {
            throw new \stack_exception('There is an error authoring your question. ' .
                'The $totalvalue, the marks available for the question, must be positive in question ' .
                $question->name);
        }

        foreach ($xmldata->question->prt as $prtdata) {
            $prtvalue = 0;
            if (!$allformative) {
                $prtvalue = ((float)$prtdata->value) / $totalvalue;
            }

            $data = new \stdClass();
            $data->name = (string) $prtdata->name;
            $data->autosimplify = (array) $prtdata->autosimplify ? self::parseboolean($prtdata->autosimplify) : true;
            $data->feedbackstyle = (array) $prtdata->feedbackstyle ? (int) $prtdata->feedbackstyle : 1;
            $data->value = (array) $prtdata->value ? (float) $prtdata->value : 1.0;
            $data->firstnodename = null;

            $data->feedbackvariables = (string) $prtdata->feedbackvariables->text;

            $data->nodes = [];
            foreach ($prtdata->node as $node) {
                $newnode = new \stdClass();

                $newnode->nodename = (string) $node->name;
                $newnode->description = isset($node->description) ? (string) $node->description : '';
                $newnode->answertest = isset($node->answertest) ? (string) $node->answertest : 'AlgEquiv';
                $newnode->sans = (string) $node->sans;
                $newnode->tans = (string) $node->tans;
                $newnode->testoptions = (string) $node->testoptions;
                $newnode->quiet = self::parseboolean($node->quiet);

                $newnode->truescoremode = (array) $node->truescoremode ? (string) $node->truescoremode : '+';
                $newnode->truescore = (array) $node->truescore ? (string) $node->truescore : 1.0;
                $newnode->truepenalty = (array) $node->truepenalty ? (string) $node->truepenalty : null;
                $newnode->truenextnode = (array) $node->truenextnode ? (string) $node->truenextnode : '-1';
                $newnode->trueanswernote = (string) $node->trueanswernote;
                $newnode->truefeedback = (string) $node->truefeedback->text;
                $newnode->truefeedbackformat = (string) $node->truefeedback['format'];

                $newnode->falsescoremode = (array) $node->falsescoremode ? (string) $node->falsescoremode : '=';
                $newnode->falsescore = (array) $node->falsescore ? (string) $node->falsescore : 0.0;
                $newnode->falsepenalty = (array) $node->falsepenalty ? (string) $node->falsepenalty : null;
                $newnode->falsenextnode = (array) $node->falsenextnode ? (string) $node->falsenextnode : '-1';
                $newnode->falseanswernote = (string) $node->falseanswernote;
                $newnode->falsefeedback = (string) $node->falsefeedback->text;
                $newnode->falsefeedbackformat = (string) $node->falsefeedback['format'];

                $data->nodes[(int) $node->name] = $newnode;
            }

            $question->prts[(string) $prtdata->name] = new \stack_potentialresponse_tree_lite($data,
                $prtvalue, $question);
        }

        $deployedseeds = [];
        foreach ($xmldata->question->deployedseed as $seed) {
            $deployedseeds[] = (int) $seed;
        }

        $question->deployedseeds = $deployedseeds;
        $testcases = [];
        if ($includetests) {
            foreach ($xmldata->question->qtest as $test) {
                $testinputs = [];
                foreach ($test->testinput as $testinput) {
                    $testinputs[(string) $testinput->name] = (string) $testinput->value;
                }
                $testcase = new \stack_question_test((string) $test->description, $testinputs, (string) $test->testcase);
                foreach ($test->expected as $expected) {
                    $testcase->add_expected_result((string) $expected->name,
                            new \stack_potentialresponse_tree_state(1, true,
                                (array) $expected->expectedscore ?
                                    (string) $expected->expectedscore : null,
                                (array) $expected->expectedpenalty ?
                                    (string) $expected->expectedpenalty : null,
                                    '', [(string) $expected->expectedanswernote]));
                }
                $testcases[] = $testcase;
            }
        }

        return ['question' => $question, 'testcases' => $testcases];
    }

    private static function handlefiles(\SimpleXMLElement $files) {
        $data = [];

        foreach ($files as $file) {
            $data[(string) $file['name']] = (string) $file;
        }

        return $data;
    }

    private static function parseboolean(\SimpleXMLElement $element) {
        $v = (string) $element;
        if ($v === "0") {
            return false;
        }
        if ($v === "1") {
            return true;
        }

        throw new \stack_exception('invalid bool value');
    }
}

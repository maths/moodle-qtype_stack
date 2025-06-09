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
 * This script handles the various deploy/undeploy actions from questiontestrun.php.
 *
 * @package    qtype_stack
 * @copyright  2023 RWTH Aachen
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

namespace api\util;
use SimpleXMLElement;
use Symfony\Component\Yaml\Yaml;
defined('MOODLE_INTERNAL') || die();
require_once(__DIR__ . '/../../question.php');
require_once(__DIR__ . '/../../stack/questiontest.php');
require_once(__DIR__ . '/../../stack/potentialresponsetreestate.class.php');

/**
 * TO-DO: Rework, dont use legacy classes
 * Converts question xml into usable format
 */
class StackQuestionLoader {
    public static $defaults = null;
    public const TEXTFIELDS = [
        'name', 'questiontext', 'generalfeedback', 'stackversion', 'questionvariables',
        'specificfeedback', 'questionnote',
        'questiondescription', 'prtcorrect', 'prtpartiallycorrect', 'prtincorrect',
        'feedbackvariables', 'truefeedback', 'falsefeedback'
    ];
    public const ARRAYFIELDS = [
        'input', 'prt', 'node', 'deployedseed', 'qtest', 'testinput', 'expected'
    ];
            
    // phpcs:ignore moodle.Commenting.MissingDocblock.Function
    public static function loadxml($xml, $includetests=false) {
        try {
            if (strpos($xml, '<question type="stack">') !== false) {
                $xmldata = new SimpleXMLElement($xml);
            } else {
                $xmldata = StackQuestionLoader::yaml_to_xml($xml);
            }
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
        $question->name = (string) $xmldata->question->name->text ?
            (string) $xmldata->question->name->text : StackQuestionLoader::get_default('question', 'name', 'Default');
        $question->questiontext = (string) $xmldata->question->questiontext->text ?
            (string) $xmldata->question->questiontext->text :
            StackQuestionLoader::get_default(
                'question', 'questiontext', '<p>Default question</p><p>[[input:ans1]] [[validation:ans1]]</p>'
            );
        $question->questiontextformat =
            (string) $xmldata->question->questiontext['format'] ? (string) $xmldata->question->questiontext['format'] :
            StackQuestionLoader::get_default('question', 'questiontextformat', 'html');
        $question->generalfeedback =
            (string) $xmldata->question->generalfeedback->text ? (string) $xmldata->question->generalfeedback->text :
            StackQuestionLoader::get_default('question', 'generalfeedback', '');
        $question->generalfeedbackformat =
            (string) $xmldata->question->generalfeedback['format'] ? (string) $xmldata->question->generalfeedback['format'] :
            StackQuestionLoader::get_default('question', 'generalfeedbackformat', 'html');
        $question->defaultmark = (array) $xmldata->question->defaultgrade ? (float) $xmldata->question->defaultgrade :
            StackQuestionLoader::get_default('question', 'defaultgrade', 1.0);
        $question->penalty = (array) $xmldata->question->penalty ? (float) $xmldata->question->penalty :
            StackQuestionLoader::get_default('question', 'penalty', 0.1);

        // Based on initialise_question_instance from questiontype.php.
        $question->stackversion              =
            (string) $xmldata->question->stackversion->text ? (string) $xmldata->question->stackversion->text :
            StackQuestionLoader::get_default('question', 'stackversion', '');
        $question->questionvariables         =
            (string) $xmldata->question->questionvariables->text ? (string) $xmldata->question->questionvariables->text :
            StackQuestionLoader::get_default('question', 'questionvariables', 'ta1:1;');
        $question->questionnote              =
            (string) $xmldata->question->questionnote->text ? (string) $xmldata->question->questionnote->text :
            StackQuestionLoader::get_default('question', 'questionnote', '{@ta1@}');
        $question->questionnoteformat        =
            (string) $xmldata->question->questionnote['format'] ? (string) $xmldata->question->questionnote['format'] :
            StackQuestionLoader::get_default('question', 'questionnoteformat', 'html');
        $question->specificfeedback          =
                (string) $xmldata->question->specificfeedback->text ?
                (string) $xmldata->question->specificfeedback->text :
                StackQuestionLoader::get_default('question', 'specificfeedback', '[[feedback:prt1]]');
        $question->specificfeedbackformat    =
                (string) $xmldata->question->specificfeedback['format'] ?
                (string) $xmldata->question->specificfeedback['format'] :
                StackQuestionLoader::get_default('question', 'specificfeedbackformat', 'html');
        $question->questiondescription       =
            (string) $xmldata->question->questiondescription->text ? (string) $xmldata->question->questiondescription->text :
            StackQuestionLoader::get_default('question', 'questiondescription', '');
        $question->questiondescriptionformat =
                (string) $xmldata->question->questiondescription['format'] ?
                (string) $xmldata->question->questiondescription['format'] :
                StackQuestionLoader::get_default('question', 'questiondescriptionformat', 'html');
        if (isset($xmldata->question->prtcorrect->text)) {
            $question->prtcorrect                = (string) $xmldata->question->prtcorrect->text;
            $question->prtcorrectformat          = (string) $xmldata->question->prtcorrect['format'];
        } else {
            $question->prtcorrect =
                StackQuestionLoader::get_default(
                    'question', 'prtcorrect', get_string('defaultprtcorrectfeedback', 'qtype_stack', null)
                );
            $question->prtcorrectformat = StackQuestionLoader::get_default('question', 'prtcorrectformat', 'html');
        }
        if (isset($xmldata->question->prtpartiallycorrect->text)) {
            $question->prtpartiallycorrect = (string)$xmldata->question->prtpartiallycorrect->text;
            $question->prtpartiallycorrectformat = (string)$xmldata->question->prtpartiallycorrect['format'];
        } else {
            $question->prtpartiallycorrect =
                StackQuestionLoader::get_default(
                    'question', 'prtpartiallycorrect', get_string('defaultprtpartiallycorrectfeedback', 'qtype_stack', null)
                );
            $question->prtpartiallycorrectformat =
                StackQuestionLoader::get_default('question', 'prtpartiallycorrectformat', 'html');
        }
        if (isset($xmldata->question->prtincorrect->text)) {
            $question->prtincorrect = (string)$xmldata->question->prtincorrect->text;
            $question->prtincorrectformat = (string)$xmldata->question->prtincorrect['format'];
        } else {
            $question->prtincorrect =
                StackQuestionLoader::get_default(
                    'question', 'prtincorrect', get_string('defaultprtincorrectfeedback', 'qtype_stack', null)
                );
            $question->prtincorrectformat =
                StackQuestionLoader::get_default('question', 'prtincorrectformat', 'html');
        }
        $question->variantsselectionseed     =
            (string) $xmldata->question->variantsselectionseed ? (string) $xmldata->question->variantsselectionseed :
            StackQuestionLoader::get_default('question', 'variantsselectionseed', '');
        $question->compiledcache             = [];
        $question->isbroken = (array) $xmldata->question->isbroken ? self::parseboolean($xmldata->question->isbroken) :
            StackQuestionLoader::get_default('question', 'isbroken', 0);
        $question->options = new \stack_options();
        $question->options->set_option(
            'multiplicationsign',
            (array) $xmldata->question->multiplicationsign ?
                (string) $xmldata->question->multiplicationsign :
                StackQuestionLoader::get_default('question', 'multiplicationsign', get_config('qtype_stack', 'multiplicationsign'))
        );
        $question->options->set_option(
            'complexno',
            (array) $xmldata->question->complexno ?
                (string) $xmldata->question->complexno :
                StackQuestionLoader::get_default('question', 'complexno', get_config('qtype_stack', 'complexno'))
        );
        $question->options->set_option(
            'inversetrig',
            (array) $xmldata->question->inversetrig ?
                (string) $xmldata->question->inversetrig :
                StackQuestionLoader::get_default('question', 'inversetrig', get_config('qtype_stack', 'inversetrig'))
        );
        $question->options->set_option(
            'logicsymbol',
            (array) $xmldata->question->logicsymbol ?
                (string) $xmldata->question->logicsymbol :
                StackQuestionLoader::get_default('question', 'logicsymbol', get_config('qtype_stack', 'logicsymbol'))
        );
        $question->options->set_option(
            'matrixparens',
            (array) $xmldata->question->matrixparens ?
                (string) $xmldata->question->matrixparens :
                StackQuestionLoader::get_default('question', 'matrixparens', get_config('qtype_stack', 'matrixparens'))
        );
        $question->options->set_option(
            'sqrtsign',
            (array) $xmldata->question->sqrtsign ?
                self::parseboolean($xmldata->question->sqrtsign) :
                (bool) StackQuestionLoader::get_default('question', 'sqrtsign', get_config('qtype_stack', 'sqrtsign'))
        );
        $question->options->set_option(
            'simplify',
            (array) $xmldata->question->questionsimplify ?
                self::parseboolean($xmldata->question->questionsimplify) :
                (bool) StackQuestionLoader::get_default(
                    'question', 'questionsimplify', get_config('qtype_stack', 'questionsimplify'))
        );
        $question->options->set_option(
            'assumepos',
            (array) $xmldata->question->assumepositive ?
                self::parseboolean($xmldata->question->assumepositive) :
                (bool) StackQuestionLoader::get_default(
                    'question', 'assumepositive', get_config('qtype_stack', 'assumepositive')
                )
        );
        $question->options->set_option(
            'assumereal',
            (array) $xmldata->question->assumereal ?
                self::parseboolean($xmldata->question->assumereal) :
                (bool) StackQuestionLoader::get_default(
                    'question', 'assumereal', get_config('qtype_stack', 'assumereal')
                )
        );
        $question->options->set_option(
            'decimals',
            (array) $xmldata->question->decimals ?
                (string) $xmldata->question->decimals :
                StackQuestionLoader::get_default('question', 'decimals', get_config('qtype_stack', 'decimals'))
        );
        $question->options->set_option(
            'scientificnotation',
            (array) $xmldata->question->scientificnotation ?
                (string) $xmldata->question->scientificnotation :
                StackQuestionLoader::get_default(
                    'question', 'scientificnotation', get_config('qtype_stack', 'scientificnotation')
                )
        );

        $inputmap = [];
        foreach ($xmldata->question->input as $input) {
            $inputmap[(string) $input->name] = $input;
        }

        if (empty($inputmap) && $question->defaultmark) {
            $defaultinput = new \SimpleXMLElement('<input></input>');
            $defaultinput->addChild('name', StackQuestionLoader::get_default('input', 'name', 'ans1'));
            $defaultinput->addChild('tans', StackQuestionLoader::get_default('input', 'tans', 'ta1'));
            $inputmap[StackQuestionLoader::get_default('input', 'name', 'ans1')] = $defaultinput;
        }

        $requiredparams = \stack_input_factory::get_parameters_used();
        foreach ($inputmap as $name => $inputdata) {
            $allparameters = [
                'boxWidth'        => (array) $inputdata->boxsize ?
                    (int) $inputdata->boxsize :
                    StackQuestionLoader::get_default('input', 'boxsize', get_config('qtype_stack', 'inputboxsize')),
                'insertStars'     => (array) $inputdata->insertstars ?
                    (int) $inputdata->insertstars :
                    StackQuestionLoader::get_default('input', 'insertstars', get_config('qtype_stack', 'inputinsertstars')),
                'syntaxHint'      => isset($inputdata->syntaxhint) ?
                    (string) $inputdata->syntaxhint :
                    StackQuestionLoader::get_default('input', 'syntaxhint', ''),
                'syntaxAttribute' => (array) $inputdata->syntaxattribute ?
                    (int) $inputdata->syntaxattribute : StackQuestionLoader::get_default('input', 'syntaxattribute', 0),
                'forbidWords'     => isset($inputdata->forbidwords) ?
                    (string) $inputdata->forbidwords :
                    StackQuestionLoader::get_default('input', 'forbidwords', get_config('qtype_stack', 'inputforbidwords')),
                'allowWords'      => isset($inputdata->allowwords) ?
                    (string) $inputdata->allowwords : StackQuestionLoader::get_default('input', 'allowwords', ''),
                'forbidFloats'    => (array) $inputdata->forbidfloat ?
                    self::parseboolean($inputdata->forbidfloat) :
                    (bool) StackQuestionLoader::get_default('input', 'forbidfloat', get_config('qtype_stack', 'inputforbidfloat')),
                'lowestTerms'     => (array) $inputdata->requirelowestterms ?
                    self::parseboolean($inputdata->requirelowestterms) :
                    (bool) StackQuestionLoader::get_default(
                        'input', 'requirelowestterms', get_config('qtype_stack', 'inputrequirelowestterms')
                    ),
                'sameType'        => (array) $inputdata->checkanswertype ?
                    self::parseboolean($inputdata->checkanswertype) :
                    (bool) StackQuestionLoader::get_default(
                        'input', 'checkanswertype', get_config('qtype_stack', 'inputcheckanswertype')
                    ),
                'mustVerify'      => (array) $inputdata->mustverify ?
                    self::parseboolean($inputdata->mustverify) :
                    (bool) StackQuestionLoader::get_default('input', 'mustverify', get_config('qtype_stack', 'inputmustverify')),
                'showValidation'  => (array) $inputdata->showvalidation ?
                    (int) $inputdata->showvalidation :
                    StackQuestionLoader::get_default('input', 'showvalidation', get_config('qtype_stack', 'inputshowvalidation')),
                'options'         => isset($inputdata->options) ? (string) $inputdata->options :
                    StackQuestionLoader::get_default('input', 'options', ''),
            ];
            $parameters = [];
            $inputtype = (string) $inputdata->type ? (string) $inputdata->type :
                StackQuestionLoader::get_default('input', 'type', 'algebraic');
            foreach ($requiredparams[$inputtype] as $paramname) {
                if ($paramname == 'inputType') {
                    continue;
                }
                $parameters[$paramname] = $allparameters[$paramname];
            }
            $question->inputs[$name] = \stack_input_factory::make(
                $inputtype, (string) $inputdata->name, (string) $inputdata->tans, $question->options, $parameters);
        }

        $totalvalue = 0;
        $allformative = true;
        $prtmap = [];
        foreach ($xmldata->question->prt as $prt) {
            $prtmap[(string) $prt->name] = $prt;
        }

        if (empty($prtmap) && $question->defaultmark) {
            $defaultprt = new \SimpleXMLElement('<prt></prt>');
            $defaultprt->addChild('name', StackQuestionLoader::get_default('prt', 'name', 'prt1'));
            $defaultnode = $defaultprt->addChild('node');
            $defaultnode->addChild('name', StackQuestionLoader::get_default('node', 'name', '0'));
            $defaultnode->addChild('sans', StackQuestionLoader::get_default('node', 'sans', 'ans1'));
            $defaultnode->addChild('tans', StackQuestionLoader::get_default('node', 'tans', 'ta1'));
            $defaultnode->addChild('trueanswernote', StackQuestionLoader::get_default('node', 'trueanswernote', 'prt1-1-T'));
            $defaultnode->addChild('falseanswernote', StackQuestionLoader::get_default('node', 'falseanswernote', 'prt1-1-F'));
            $prtmap[StackQuestionLoader::get_default('prt', 'name', 'prt1')] = $defaultprt;
        }

        foreach ($prtmap as $prtdata) {
            // At this point we do not have the PRT method is_formative() available to us.
            if (!isset($prtdata->feedbackstyle) || ((int) $prtdata->feedbackstyle) > 0) {
                $totalvalue += isset($prtdata->value) ? (float) $prtdata->value : StackQuestionLoader::get_default('prt', 'value', 1);
                $allformative = false;
            }
        }
        if (count($prtmap) > 0 && !$allformative && $totalvalue < 0.0000001) {
            throw new \stack_exception('There is an error authoring your question. ' .
                'The $totalvalue, the marks available for the question, must be positive in question ' .
                $question->name);
        }

        foreach ($prtmap as $prtdata) {
            $prtvalue = 0;
            if (!$allformative) {
                $value = $prtdata->value ? (float) $prtdata->value : StackQuestionLoader::get_default('prt', 'value', 1);
                $prtvalue = $value / $totalvalue;
            }

            $data = new \stdClass();
            $data->name = (string) $prtdata->name;
            $data->autosimplify = (array) $prtdata->autosimplify ? self::parseboolean($prtdata->autosimplify) :
                StackQuestionLoader::get_default('prt', 'autosimplify', true);
            $data->feedbackstyle = (array) $prtdata->feedbackstyle ? (int) $prtdata->feedbackstyle :
                StackQuestionLoader::get_default('prt', 'feedbackstyle', 1);
            $data->value = (array) $prtdata->value ? (float) $prtdata->value :
                StackQuestionLoader::get_default('prt', 'value', 1.0);
            $data->firstnodename = null;

            $data->feedbackvariables = (string) $prtdata->feedbackvariables->text ? (string) $prtdata->feedbackvariables->text :
                StackQuestionLoader::get_default('prt', 'feedbackvariables', '');

            $data->nodes = [];
            foreach ($prtdata->node as $node) {
                $newnode = new \stdClass();

                $newnode->nodename = (string) $node->name;
                $newnode->description = isset($node->description) ? (string) $node->description : '';
                $newnode->answertest = isset($node->answertest) ? (string) $node->answertest :
                    StackQuestionLoader::get_default('node', 'answertest', 'AlgEquiv');
                $newnode->sans = (string) $node->sans;
                $newnode->tans = (string) $node->tans;
                $newnode->testoptions = (string) $node->testoptions ? (string) $node->testoptions :
                    StackQuestionLoader::get_default('node', 'testoptions', '');
                $newnode->quiet = isset($node->quiet) ? self::parseboolean($node->quiet) :
                    StackQuestionLoader::get_default('node', 'quiet', false);

                $newnode->truescoremode = (array) $node->truescoremode ?
                    (string) $node->truescoremode : StackQuestionLoader::get_default('node', 'truescoremode', '=');
                $newnode->truescore = (array) $node->truescore ?
                (string) $node->truescore : StackQuestionLoader::get_default('node', 'truescore', '1.0');
                $newnode->truepenalty = (array) $node->truepenalty ?
                    (string) $node->truepenalty : StackQuestionLoader::get_default('node', 'truepenalty', null);
                $newnode->truenextnode = (array) $node->truenextnode ?
                    (string) $node->truenextnode : StackQuestionLoader::get_default('node', 'truenextnode', '-1');
                $newnode->trueanswernote = (string) $node->trueanswernote ?
                    (string) $node->trueanswernote : StackQuestionLoader::get_default('node', 'trueanswernote', '');
                $newnode->truefeedback = (string) $node->truefeedback->text ?
                    (string) $node->truefeedback->text : StackQuestionLoader::get_default('node', 'truefeedback', '');
                $newnode->truefeedbackformat =
                    (string) $node->truefeedback['format'] ?
                    (string) $node->truefeedback['format'] : StackQuestionLoader::get_default('node', 'truefeedbackformat', 'html');

                $newnode->falsescoremode = (array) $node->falsescoremode ?
                    (string) $node->falsescoremode : StackQuestionLoader::get_default('node', 'falsescoremode', '=');
                $newnode->falsescore = (array) $node->falsescore ? (string) $node->falsescore :
                    StackQuestionLoader::get_default('node', 'falsescore', '0.0');
                $newnode->falsepenalty = (array) $node->falsepenalty ?
                    (string) $node->falsepenalty : StackQuestionLoader::get_default('node', 'falsepenalty', null);
                $newnode->falsenextnode = (array) $node->falsenextnode ?
                    (string) $node->falsenextnode : StackQuestionLoader::get_default('node', 'falsenextnode', '-1');
                $newnode->falseanswernote = (string) $node->falseanswernote ?
                    (string) $node->falseanswernote : StackQuestionLoader::get_default('node', 'falseanswernote', '');
                $newnode->falsefeedback = (string) $node->falsefeedback->text ?
                    (string) $node->falsefeedback->text : StackQuestionLoader::get_default('node', 'falsefeedback', '');
                $newnode->falsefeedbackformat =
                    (string) $node->falsefeedback['format'] ?
                    (string) $node->falsefeedback['format'] :
                    StackQuestionLoader::get_default('node', 'falsefeedbackformat', 'html');

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
            if (empty($qtest) && $question->defaultmark) {
                $defaulttest = new \SimpleXMLElement('<qtest></qtest>');
                $defaulttest->addChild('testcase', StackQuestionLoader::get_default('qtest', 'testcase', '1'));
                $defaulttest->addChild('description', StackQuestionLoader::get_default('qtest', 'description', ''));
                $defaulttinput = $defaulttest->addChild('testinput');
                $defaulttinput->addChild('name', StackQuestionLoader::get_default('testinput', 'name', 'ans1'));
                $defaulttinput->addChild('value', StackQuestionLoader::get_default('testinput', 'value', 'ta1'));
                $defaulttexpected = $defaulttest->addChild('expected');
                $defaulttexpected->addChild('name', StackQuestionLoader::get_default('expected', 'name', 'prt1'));
                $defaulttexpected->addChild('expectedscore', StackQuestionLoader::get_default('expected', 'expectedscore', '1.0000000'));
                $defaulttexpected->addChild('expectedpenalty', StackQuestionLoader::get_default('expected', 'expectedpenalty', '0.0000000'));
                $defaulttexpected->addChild('expectedanswernote', StackQuestionLoader::get_default('expected', 'expectedanswernote', '1-0-T'));
                $testcases[] = $defaulttest;
            }
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
                                    (string) $expected->expectedscore : StackQuestionLoader::get_default('expected', 'expectedscore', '1.0000000'),
                                (array) $expected->expectedpenalty ?
                                    (string) $expected->expectedpenalty : StackQuestionLoader::get_default('expected', 'expectedpenalty', '0.0000000'),
                                    '', [(string) $expected->expectedanswernote]));
                }
                $testcases[] = $testcase;
            }
        }

        return ['question' => $question, 'testcases' => $testcases];
    }

    // phpcs:ignore moodle.Commenting.MissingDocblock.Function
    private static function handlefiles(\SimpleXMLElement $files) {
        $data = [];

        foreach ($files as $file) {
            $data[(string) $file['name']] = (string) $file;
        }

        return $data;
    }

    // phpcs:ignore moodle.Commenting.MissingDocblock.Function
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

    public static function get_default($defaultcategory, $defaultname, $default) {
        if (!get_config('qtype_stack', 'stackapi')) {
            return $default;
        }
        if (!StackQuestionLoader::$defaults) {
                StackQuestionLoader::$defaults = yaml_parse_file(__DIR__ . '/../questiondefaults.yml');
        }

        if (isset(StackQuestionLoader::$defaults[$defaultcategory][$defaultname])) {
            return StackQuestionLoader::$defaults[$defaultcategory][$defaultname];
        }

        return null;
    }

    /**
     * Converts a YAML string to a SimpleXMLElement object.
     *
     * @param string $yamlstring The YAML string to convert.
     * @return SimpleXMLElement The resulting XML object.
     * @throws \stack_exception If the YAML string is invalid.
     */
    public static function yaml_to_xml($yamlstring) {
        $yaml = yaml_parse($yamlstring);
        if (!$yaml) {
            throw new \stack_exception("The provided file does not contain valid YAML or XML.");
        }
        $xml = new SimpleXMLElement("<quiz></quiz>");
        $question = $xml->addChild('question');
        $question->addAttribute('type', 'stack');
        
        StackQuestionLoader::array_to_xml($yaml, $question);
        // Name is a special case. Has text tag but no format.
        $name = (string) $xml->question->name ? (string) $xml->question->name : StackQuestionLoader::get_default('question', 'name', 'Default');
        $xml->question->name = new SimpleXMLElement('<root></root>');
        $xml->question->name->addChild('text', $name);
        return $xml;  
    }

    /**
     * Recursively converts an associative array to XML.
     */
    public static function array_to_xml($data, &$xml) {
        foreach($data as $key => $value) {
            if (strpos($key, 'format') !== false && in_array(str_replace('format', '', $key), StackQuestionLoader::TEXTFIELDS)) {
                // Skip format attributes for text fields - they are handled with the text field below.
                continue;
            } else if (in_array($key, StackQuestionLoader::TEXTFIELDS)) {
                // Convert basic YAML field to node with text and format fields.
                if ($key !== 'name') {
                    // Name is used in multiple places and sometimes has text property and sometimes not.
                    // Handled in yaml_to_xml().
                    $subnode = $xml->addChild($key);
                    /* if (!empty($value) && htmlspecialchars($value, ENT_COMPAT) != $value) {
                        $subvalue = ['text' => '<![CDATA[' . $value . ']]>'];
                    } else {
                        $subvalue = ['text' => $value];
                    } */
                    $subvalue = ['text' => $value];
                    if (isset($data[$key . 'format'])) {
                        $subvalue['format'] = $data[$key . 'format'];
                    }
                    StackQuestionLoader::array_to_xml($subvalue, $subnode);
                } else {
                    $xml->addChild($key, $value);
                }
            } else if (in_array($key, StackQuestionLoader::ARRAYFIELDS)) {
                // Certain fields need special handling to strip out
                // numeric keys.
                foreach($value as $element) {
                    if (is_array($element)) {
                        $subnode = $xml->addChild($key);
                        StackQuestionLoader::array_to_xml($element, $subnode);
                    } else {
                        $xml->addChild($key, $element);
                    }
                }
            } else if (is_array($value)) {
                $subnode = $xml->addChild($key);
                StackQuestionLoader::array_to_xml($value, $subnode);
            } else {
                $xml->addChild($key, $value);
            } 
        }
    }

    /**
     * Converts a SimpleXMLElement object to an array for conversion to YAML.
     *
     * @param SimpleXMLElement The resulting XML object.
     * @return array The resulting array.
     */
    public static function xml_to_array($xmldata, &$output = []) {
        foreach($xmldata as $key => $value) {
            if ($key === 'deployedseed') {
                // Convert deployedseed to an array of integers.
                $x= (int) $value;
            }
            if (in_array($key, StackQuestionLoader::TEXTFIELDS)) {
                if (isset($value->text)) {
                    $output[$key] = (string) $value->text;
                } else {
                    $output[$key] = (string) $value;
                }
                if (isset($value->format)) {
                    $output[$key . 'format'] = (string) $value->format;
                }
            } else if ($value instanceof SimpleXMLElement && $value->count()) {
                if (in_array($key, StackQuestionLoader::ARRAYFIELDS)) {
                    $output[$key][] = StackQuestionLoader::xml_to_array($value);
                } else {
                    $output[$key] = [];
                    StackQuestionLoader::xml_to_array($value, $output[$key]);
                }
            } else {
                if (in_array($key, StackQuestionLoader::ARRAYFIELDS)) {
                    $output[$key][] = (string) $value;
                } else {
                    $output[$key] = (string) $value;
                }
            } 
        }
        return $output;  
    }

    public static function detect_differences($xml) {      
        if (!StackQuestionLoader::$defaults) {
                StackQuestionLoader::$defaults = yaml_parse_file(__DIR__ . '/../questiondefaults.yml');
        }
        if (strpos($xml, '<question type="stack">') !== false) {
            $xmldata = new SimpleXMLElement($xml);
        } else {
            $xmldata = StackQuestionLoader::yaml_to_xml($xml);
        }
        $plaindata = StackQuestionLoader::xml_to_array($xmldata);
        $diff = StackQuestionLoader::obj_diff(StackQuestionLoader::$defaults['question'], $plaindata['question']);
        $diffinputs = [];
        foreach ($plaindata['question']['input'] as $input) {
            $diffinput = [];
            $diffinput['name'] = $input['name'];
            $diffinput['tans'] = $input['tans'];
            $diffinput = array_merge($diffinput, StackQuestionLoader::obj_diff(StackQuestionLoader::$defaults['input'], $input));
            $diffinputs[] = $diffinput;
        }
        $diff['input'] = $diffinputs;
        $diffprts = [];
        foreach ($plaindata['question']['prt'] as $prt) {
            $diffprt = [];
            $diffprt['name'] = $prt['name'];
            $diffprt = array_merge($diffprt, StackQuestionLoader::obj_diff(StackQuestionLoader::$defaults['prt'], $prt));
            foreach ($prt['node'] as $node) {
                $diffnode = [];
                $diffnode['name'] = $node['name'];
                $diffnode['sans'] = $node['sans'];
                $diffnode['tans'] = $node['tans'];
                $diffnode = array_merge($diffnode, StackQuestionLoader::obj_diff(StackQuestionLoader::$defaults['node'], $node));
                $diffprt['node'][] = $diffnode;
            }
            $diffprts[] = $diffprt;
        }
        $diff['prt'] = $diffprts;
        $deployedseeds = [];
        foreach ($plaindata['question']['deployedseed'] as $seed) {
            $deployedseeds[] = (string) $seed;
        }
        if (count($deployedseeds)) {
            $diff['deployedseed'] = $deployedseeds;
        }
        $difftests = [];
        foreach ($plaindata['question']['qtest'] as $test) {
            $difftest = [];
            $difftest['testcase'] = $test['testcase'];
            $difftest = array_merge($difftest, StackQuestionLoader::obj_diff(StackQuestionLoader::$defaults['qtest'], $test));
            foreach ($test['testinput'] as $tinput) {
                $difftinput = [];
                $difftinput['name'] = $tinput['name'];
                $difftinput = array_merge($difftinput, StackQuestionLoader::obj_diff(StackQuestionLoader::$defaults['testinput'], $tinput));
                $difftest['testinput'][] = $difftinput;
            }
            foreach ($test['expected'] as $texpected) {
                $difftexpected = [];
                $difftexpected['name'] = $texpected['name'];
                $difftexpected = array_merge($difftexpected, StackQuestionLoader::obj_diff(StackQuestionLoader::$defaults['expected'], $texpected));
                $difftest['expected'][] = $difftexpected;
            }
            $difftests[] = $difftest;
        }
        $diff['qtest'] = $difftests;
        $yaml = Yaml::dump($diff, 10, 2, Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK);
        $yaml = ltrim($yaml, "---\n");
        $yaml = rtrim($yaml, "...\n");
        return $yaml;
    }

    public static function obj_diff($obj1, $obj2):array { 
        $a1 = (array)$obj1;
        $a2 = (array)$obj2;
        return StackQuestionLoader::arr_diff($a1, $a2);
    }

    public static function arr_diff($a1, $a2):array {
        $r = [];
        foreach ($a1 as $k => $v) {
            if (array_key_exists($k, $a2)) { 
                if (is_array($v)){
                    $rad = StackQuestionLoader::arr_diff($v, (array) $a2[$k]);  
                    if (count($rad)) { $r[$k] = $rad; } 
                // required to avoid rounding errors due to the 
                // conversion from string representation to double
                } else if (is_double($v)){ 
                    if (abs($v - $a2[$k]) > 0.000000000001) { 
                        $r[$k] = $a2[$k]; 
                    }
                } else { 
                    if ($v != $a2[$k]) { 
                        $r[$k] = $a2[$k]; 
                    }
                }
            }
        } 
        return $r;
    }    
} 

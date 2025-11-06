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
    /**
     * @var array|null Default values for the question.
     */
    public static $defaults = null;
    /**
     * @var array Question properties that have <text> elements in the xml.
     */
    public const TEXTFIELDS = [
        'name', 'questiontext', 'generalfeedback', 'stackversion', 'questionvariables',
        'specificfeedback', 'questionnote',
        'questiondescription', 'prtcorrect', 'prtpartiallycorrect', 'prtincorrect',
        'feedbackvariables', 'truefeedback', 'falsefeedback',
    ];
    /**
     * @var array Question properties that can have multiple elements in the xml.
     */
    public const ARRAYFIELDS = [
        'input', 'prt', 'node', 'deployedseed', 'qtest', 'testinput', 'expected',
    ];

    /**
     * @var array Question properties are always shown in difference file even if they match the default.
     */
    public const ALWAYS_SHOWN = [
        'questionsimplify', 'type', 'tans', 'forbidfloat', 'requirelowestterms', 'checkanswertype',
        'mustverify', 'showvalidation', 'autosimplify', 'feedbackstyle', 'answertest', 'sans',
        'quiet', 'name',
    ];

    // phpcs:ignore moodle.Commenting.MissingDocblock.Function
    public static function loadxml($xml, $includetests=false) {
        try {
            if (strpos($xml, '<question type=') !== false) {
                $xmldata = new SimpleXMLElement($xml);
            } else {
                $xmldata = self::yaml_to_xml($xml);
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
            (string) $xmldata->question->name->text : self::get_default('question', 'name', 'Default');
        $question->questiontext = isset($xmldata->question->questiontext->text) ?
            (string) $xmldata->question->questiontext->text :
            self::get_default(
                'question', 'questiontext', '<p>Default question</p><p>[[input:ans1]] [[validation:ans1]]</p>'
            );
        $question->questiontextformat =
            isset($xmldata->question->questiontext['format']) ? (string) $xmldata->question->questiontext['format'] :
            self::get_default('question', 'questiontextformat', 'html');
        $question->generalfeedback =
            isset($xmldata->question->generalfeedback->text) ? (string) $xmldata->question->generalfeedback->text :
            self::get_default('question', 'generalfeedback', '');
        $question->generalfeedbackformat =
            isset($xmldata->question->generalfeedback['format']) ? (string) $xmldata->question->generalfeedback['format'] :
            self::get_default('question', 'generalfeedbackformat', 'html');
        // Use (array) because isset($xmldata->question->defaultgrade) returns true if the element is empty and
        // empty() returns true if element is 0. Casting to array returns [] and [0] which return false and true respectively.
        $question->defaultmark = (array) $xmldata->question->defaultgrade ? (float) $xmldata->question->defaultgrade :
            self::get_default('question', 'defaultgrade', 1.0);
        $question->penalty = (array) $xmldata->question->penalty ? (float) $xmldata->question->penalty :
            self::get_default('question', 'penalty', 0.1);

        // Based on initialise_question_instance from questiontype.php.
        $question->stackversion              =
            isset($xmldata->question->stackversion->text) ? (string) $xmldata->question->stackversion->text :
            self::get_default('question', 'stackversion', '');
        $question->questionvariables         =
            isset($xmldata->question->questionvariables->text) ? (string) $xmldata->question->questionvariables->text :
            self::get_default('question', 'questionvariables', 'ta1:1;');
        $question->questionnote              =
            isset($xmldata->question->questionnote->text) ? (string) $xmldata->question->questionnote->text :
            self::get_default('question', 'questionnote', '{@ta1@}');
        $question->questionnoteformat        =
            isset($xmldata->question->questionnote['format']) ? (string) $xmldata->question->questionnote['format'] :
            self::get_default('question', 'questionnoteformat', 'html');
        $question->specificfeedback          =
                isset($xmldata->question->specificfeedback->text) ?
                (string) $xmldata->question->specificfeedback->text :
                self::get_default('question', 'specificfeedback', '[[feedback:prt1]]');
        $question->specificfeedbackformat    =
                isset($xmldata->question->specificfeedback['format']) ?
                (string) $xmldata->question->specificfeedback['format'] :
                self::get_default('question', 'specificfeedbackformat', 'html');
        $question->questiondescription       =
            isset($xmldata->question->questiondescription->text) ? (string) $xmldata->question->questiondescription->text :
            self::get_default('question', 'questiondescription', '');
        $question->questiondescriptionformat =
                isset($xmldata->question->questiondescription['format']) ?
                (string) $xmldata->question->questiondescription['format'] :
                self::get_default('question', 'questiondescriptionformat', 'html');
        if (isset($xmldata->question->prtcorrect->text)) {
            $question->prtcorrect                = (string) $xmldata->question->prtcorrect->text;
            $question->prtcorrectformat          =
                isset($xmldata->question->prtcorrect['format']) ?
                (string) $xmldata->question->prtcorrect['format'] :
                self::get_default('question', 'prtcorrectformat', 'html');
        } else {
            $question->prtcorrect =
                self::get_default(
                    'question', 'prtcorrect', get_string('defaultprtcorrectfeedback', 'qtype_stack', null)
                );
            $question->prtcorrectformat = self::get_default('question', 'prtcorrectformat', 'html');
        }
        if (isset($xmldata->question->prtpartiallycorrect->text)) {
            $question->prtpartiallycorrect = (string)$xmldata->question->prtpartiallycorrect->text;
            $question->prtpartiallycorrectformat =
                isset($xmldata->question->prtpartiallycorrect['format']) ?
                (string) $xmldata->question->prtpartiallycorrect['format'] :
                self::get_default('question', 'prtpartiallycorrectformat', 'html');
        } else {
            $question->prtpartiallycorrect =
                self::get_default(
                    'question', 'prtpartiallycorrect', get_string('defaultprtpartiallycorrectfeedback', 'qtype_stack', null)
                );
            $question->prtpartiallycorrectformat =
                self::get_default('question', 'prtpartiallycorrectformat', 'html');
        }
        if (isset($xmldata->question->prtincorrect->text)) {
            $question->prtincorrect = (string)$xmldata->question->prtincorrect->text;
            $question->prtincorrectformat =
                isset($xmldata->question->prtincorrect['format']) ?
                (string) $xmldata->question->prtincorrect['format'] :
                self::get_default('question', 'prtincorrectformat', 'html');
        } else {
            $question->prtincorrect =
                self::get_default(
                    'question', 'prtincorrect', get_string('defaultprtincorrectfeedback', 'qtype_stack', null)
                );
            $question->prtincorrectformat =
                self::get_default('question', 'prtincorrectformat', 'html');
        }
        $question->variantsselectionseed     =
            isset($xmldata->question->variantsselectionseed) ? (string) $xmldata->question->variantsselectionseed :
            self::get_default('question', 'variantsselectionseed', '');
        $question->compiledcache             = [];
        $question->isbroken = (array) $xmldata->question->isbroken ? self::parseboolean($xmldata->question->isbroken) :
            self::get_default('question', 'isbroken', 0);
        $question->options = new \stack_options();
        $question->options->set_option(
            'multiplicationsign',
            (array) $xmldata->question->multiplicationsign ?
                (string) $xmldata->question->multiplicationsign :
                self::get_default('question', 'multiplicationsign', get_config('qtype_stack', 'multiplicationsign'))
        );
        $question->options->set_option(
            'complexno',
            (array) $xmldata->question->complexno ?
                (string) $xmldata->question->complexno :
                self::get_default('question', 'complexno', get_config('qtype_stack', 'complexno'))
        );
        $question->options->set_option(
            'inversetrig',
            (array) $xmldata->question->inversetrig ?
                (string) $xmldata->question->inversetrig :
                self::get_default('question', 'inversetrig', get_config('qtype_stack', 'inversetrig'))
        );
        $question->options->set_option(
            'logicsymbol',
            (array) $xmldata->question->logicsymbol ?
                (string) $xmldata->question->logicsymbol :
                self::get_default('question', 'logicsymbol', get_config('qtype_stack', 'logicsymbol'))
        );
        $question->options->set_option(
            'matrixparens',
            (array) $xmldata->question->matrixparens ?
                (string) $xmldata->question->matrixparens :
                self::get_default('question', 'matrixparens', get_config('qtype_stack', 'matrixparens'))
        );
        $question->options->set_option(
            'sqrtsign',
            (array) $xmldata->question->sqrtsign ?
                self::parseboolean($xmldata->question->sqrtsign) :
                (bool) self::get_default('question', 'sqrtsign', get_config('qtype_stack', 'sqrtsign'))
        );
        $question->options->set_option(
            'simplify',
            (array) $xmldata->question->questionsimplify ?
                self::parseboolean($xmldata->question->questionsimplify) :
                (bool) self::get_default(
                    'question', 'questionsimplify', get_config('qtype_stack', 'questionsimplify'))
        );
        $question->options->set_option(
            'assumepos',
            (array) $xmldata->question->assumepositive ?
                self::parseboolean($xmldata->question->assumepositive) :
                (bool) self::get_default(
                    'question', 'assumepositive', get_config('qtype_stack', 'assumepositive')
                )
        );
        $question->options->set_option(
            'assumereal',
            (array) $xmldata->question->assumereal ?
                self::parseboolean($xmldata->question->assumereal) :
                (bool) self::get_default(
                    'question', 'assumereal', get_config('qtype_stack', 'assumereal')
                )
        );
        $question->options->set_option(
            'decimals',
            (array) $xmldata->question->decimals ?
                (string) $xmldata->question->decimals :
                self::get_default('question', 'decimals', get_config('qtype_stack', 'decimals'))
        );
        $question->options->set_option(
            'scientificnotation',
            (array) $xmldata->question->scientificnotation ?
                (string) $xmldata->question->scientificnotation :
                self::get_default(
                    'question', 'scientificnotation', get_config('qtype_stack', 'scientificnotation')
                )
        );

        $inputmap = [];
        foreach ($xmldata->question->input as $input) {
            $inputmap[(string) $input->name] = $input;
        }

        if (empty($inputmap) && $question->defaultmark) {
            $defaultinput = new \SimpleXMLElement('<input></input>');
            $defaultinput->addChild('name', self::get_default('input', 'name', 'ans1'));
            $defaultinput->addChild('tans', self::get_default('input', 'tans', 'ta1'));
            $inputmap[self::get_default('input', 'name', 'ans1')] = $defaultinput;
        }

        $requiredparams = \stack_input_factory::get_parameters_used();
        foreach ($inputmap as $name => $inputdata) {
            $allparameters = [
                'boxWidth'        => (array) $inputdata->boxsize ?
                    (int) $inputdata->boxsize :
                    self::get_default('input', 'boxsize', get_config('qtype_stack', 'inputboxsize')),
                'insertStars'     => (array) $inputdata->insertstars ?
                    (int) $inputdata->insertstars :
                    self::get_default('input', 'insertstars', get_config('qtype_stack', 'inputinsertstars')),
                'syntaxHint'      => isset($inputdata->syntaxhint) ?
                    (string) $inputdata->syntaxhint :
                    self::get_default('input', 'syntaxhint', ''),
                'syntaxAttribute' => (array) $inputdata->syntaxattribute ?
                    (int) $inputdata->syntaxattribute : self::get_default('input', 'syntaxattribute', 0),
                'forbidWords'     => isset($inputdata->forbidwords) ?
                    (string) $inputdata->forbidwords :
                    self::get_default('input', 'forbidwords', get_config('qtype_stack', 'inputforbidwords')),
                'allowWords'      => isset($inputdata->allowwords) ?
                    (string) $inputdata->allowwords : self::get_default('input', 'allowwords', ''),
                'forbidFloats'    => (array) $inputdata->forbidfloat ?
                    self::parseboolean($inputdata->forbidfloat) :
                    (bool) self::get_default('input', 'forbidfloat', get_config('qtype_stack', 'inputforbidfloat')),
                'lowestTerms'     => (array) $inputdata->requirelowestterms ?
                    self::parseboolean($inputdata->requirelowestterms) :
                    (bool) self::get_default(
                        'input', 'requirelowestterms', get_config('qtype_stack', 'inputrequirelowestterms')
                    ),
                'sameType'        => (array) $inputdata->checkanswertype ?
                    self::parseboolean($inputdata->checkanswertype) :
                    (bool) self::get_default(
                        'input', 'checkanswertype', get_config('qtype_stack', 'inputcheckanswertype')
                    ),
                'mustVerify'      => (array) $inputdata->mustverify ?
                    self::parseboolean($inputdata->mustverify) :
                    (bool) self::get_default('input', 'mustverify', get_config('qtype_stack', 'inputmustverify')),
                'showValidation'  => (array) $inputdata->showvalidation ?
                    (int) $inputdata->showvalidation :
                    self::get_default('input', 'showvalidation', get_config('qtype_stack', 'inputshowvalidation')),
                'options'         => isset($inputdata->options) ? (string) $inputdata->options :
                    self::get_default('input', 'options', ''),
            ];
            $parameters = [];
            $inputtype = (string) $inputdata->type ? (string) $inputdata->type :
                self::get_default('input', 'type', 'algebraic');
            foreach ($requiredparams[$inputtype] as $paramname) {
                if ($paramname == 'inputType') {
                    continue;
                }
                $parameters[$paramname] = $allparameters[$paramname];
            }
            $question->inputs[$name] = \stack_input_factory::make(
                $inputtype, (string) $name, (string) $inputdata->tans, $question->options, $parameters);
        }

        $totalvalue = 0;
        $allformative = true;
        $prtmap = [];
        foreach ($xmldata->question->prt as $prt) {
            $prtmap[(string) $prt->name] = $prt;
        }

        if (empty($prtmap) && $question->defaultmark) {
            $defaultprt = new \SimpleXMLElement('<prt></prt>');
            $defaultprt->addChild('name', self::get_default('prt', 'name', 'prt1'));
            $defaultnode = $defaultprt->addChild('node');
            $defaultnode->addChild('name', self::get_default('node', 'name', '0'));
            $defaultnode->addChild('sans', self::get_default('node', 'sans', 'ans1'));
            $defaultnode->addChild('tans', self::get_default('node', 'tans', 'ta1'));
            $defaultnode->addChild('trueanswernote', self::get_default('node', 'trueanswernote', 'prt1-1-T'));
            $defaultnode->addChild('falseanswernote', self::get_default('node', 'falseanswernote', 'prt1-1-F'));
            $prtmap[self::get_default('prt', 'name', 'prt1')] = $defaultprt;
        }

        foreach ($prtmap as $prtdata) {
            // At this point we do not have the PRT method is_formative() available to us.
            if (!isset($prtdata->feedbackstyle) || ((int) $prtdata->feedbackstyle) > 0) {
                $totalvalue += isset($prtdata->value) ? (float) $prtdata->value : self::get_default('prt', 'value', 1);
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
                $value = $prtdata->value ? (float) $prtdata->value : self::get_default('prt', 'value', 1);
                $prtvalue = $value / $totalvalue;
            }

            $data = new \stdClass();
            $data->name = (string) $prtdata->name;
            $data->autosimplify = (array) $prtdata->autosimplify ? self::parseboolean($prtdata->autosimplify) :
                self::get_default('prt', 'autosimplify', true);
            $data->feedbackstyle = (array) $prtdata->feedbackstyle ? (int) $prtdata->feedbackstyle :
                self::get_default('prt', 'feedbackstyle', 1);
            $data->value = (array) $prtdata->value ? (float) $prtdata->value :
                self::get_default('prt', 'value', 1.0);
            $data->firstnodename = null;

            $data->feedbackvariables = isset($prtdata->feedbackvariables->text) ? (string) $prtdata->feedbackvariables->text :
                self::get_default('prt', 'feedbackvariables', '');

            $data->nodes = [];
            foreach ($prtdata->node as $node) {
                $newnode = new \stdClass();

                $newnode->nodename = (string) $node->name;
                $newnode->description = isset($node->description) ? (string) $node->description :
                    self::get_default('node', 'description', '');
                self::parse_answertest($node);
                $newnode->answertest = isset($node->answertest) ? (string) $node->answertest :
                    self::get_default('node', 'answertest', 'AlgEquiv');
                self::parse_answertest($newnode);
                $newnode->sans = isset($node->sans) ? (string) $node->sans :
                    self::get_default('node', 'sans', 'ans1');
                $newnode->tans = isset($node->tans) ? (string) $node->tans :
                    self::get_default('node', 'tans', 'ta1');
                $newnode->testoptions = isset($node->testoptions) ? (string) $node->testoptions :
                    self::get_default('node', 'testoptions', '');
                $newnode->quiet = isset($node->quiet) ? self::parseboolean($node->quiet) :
                    self::get_default('node', 'quiet', false);

                $newnode->truescoremode = (array) $node->truescoremode ?
                    (string) $node->truescoremode : self::get_default('node', 'truescoremode', '=');
                $newnode->truescore = (array) $node->truescore ?
                (string) $node->truescore : self::get_default('node', 'truescore', '1.0');
                $newnode->truepenalty = (array) $node->truepenalty ?
                    (string) $node->truepenalty : self::get_default('node', 'truepenalty', null);
                $newnode->truenextnode = (array) $node->truenextnode ?
                    (string) $node->truenextnode : self::get_default('node', 'truenextnode', '-1');
                $newnode->trueanswernote = isset($node->trueanswernote) ?
                    (string) $node->trueanswernote : self::get_default('node', 'trueanswernote', '');
                $newnode->truefeedback = isset($node->truefeedback->text) ?
                    (string) $node->truefeedback->text : self::get_default('node', 'truefeedback', '');
                $newnode->truefeedbackformat =
                    (string) $node->truefeedback['format'] ?
                    (string) $node->truefeedback['format'] : self::get_default('node', 'truefeedbackformat', 'html');

                $newnode->falsescoremode = (array) $node->falsescoremode ?
                    (string) $node->falsescoremode : self::get_default('node', 'falsescoremode', '=');
                $newnode->falsescore = (array) $node->falsescore ? (string) $node->falsescore :
                    self::get_default('node', 'falsescore', '0.0');
                $newnode->falsepenalty = (array) $node->falsepenalty ?
                    (string) $node->falsepenalty : self::get_default('node', 'falsepenalty', null);
                $newnode->falsenextnode = (array) $node->falsenextnode ?
                    (string) $node->falsenextnode : self::get_default('node', 'falsenextnode', '-1');
                $newnode->falseanswernote = isset($node->falseanswernote) ?
                    (string) $node->falseanswernote : self::get_default('node', 'falseanswernote', '');
                $newnode->falsefeedback = isset($node->falsefeedback->text) ?
                    (string) $node->falsefeedback->text : self::get_default('node', 'falsefeedback', '');
                $newnode->falsefeedbackformat =
                    (string) $node->falsefeedback['format'] ?
                    (string) $node->falsefeedback['format'] :
                    self::get_default('node', 'falsefeedbackformat', 'html');

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
                    $testiname = isset($testinput->name) ? (string) $testinput->name :
                        self::get_default('testinput', 'name', 'ans1');
                    $testivalue = (array) $testinput->value ? (string) $testinput->value :
                        self::get_default('testinput', 'value', 'ta1');
                    $testinputs[$testiname] = $testivalue;
                }
                $testdescription = isset($test->description) ? (string) $test->description :
                    self::get_default('qtest', 'description', '');
                $testtestcase = (array) $test->testcase ? (string) $test->testcase :
                    self::get_default('qtest', 'testcase', '1');
                $testcase = new \stack_question_test($testdescription, $testinputs, $testtestcase);
                foreach ($test->expected as $expected) {
                    $testename = isset($expected->name) ? (string) $expected->name :
                        self::get_default('expected', 'name', 'prt1');
                    $testcase->add_expected_result($testename,
                        new \stack_potentialresponse_tree_state(1, true,
                            (array) $expected->expectedscore ?
                                (string) $expected->expectedscore :
                                self::get_default('expected', 'expectedscore', null),
                            (array) $expected->expectedpenalty ?
                                (string) $expected->expectedpenalty :
                                self::get_default('expected', 'expectedpenalty', null),
                            '', [
                                (array) $expected->expectedanswernote ?
                                (string) $expected->expectedanswernote :
                                self::get_default('expected', 'expectedanswernote', '1-0-T'),
                            ]
                        )
                    );
                }
                $testcases[] = $testcase;
            }
        }

        return ['question' => $question, 'testcases' => $testcases];
    }

    /**
     * Splits an answertest string into its components and adds the fields to the node.
     * @param mixed $node
     * @return void
     */
    public static function parse_answertest(&$node) {
        if (substr($node->answertest, 0, 2) === 'AT') {
            [$answertest, $sans, $tans, $testoptions] = self::split_answertest($node->answertest);
            $node->answertest = substr($answertest, 2);
            $node->sans = $sans;
            $node->tans = $tans;
            $node->testoptions = $testoptions;
        }
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

    /**
     * Returns the default value for a question property.
     *
     * @param string $defaultcategory The category of the property required - question, input, prt, node, qtest.
     * @param string $defaultname The name of the property.
     * @param mixed $default The default value to return if not using the API.
     * @return mixed The default value.
     */
    public static function get_default($defaultcategory, $defaultname, $default) {
        // If we're in the STACK library in Moodle we can't load YML.
        if (!get_config('qtype_stack', 'stackapi')) {
            return $default;
        }

        if (!self::$defaults) {
            self::$defaults = Yaml::parseFile(__DIR__ . '/../questiondefaults.yml');
        }

        if (isset(self::$defaults[$defaultcategory][$defaultname])) {
            return self::$defaults[$defaultcategory][$defaultname];
        }
        if ($defaultcategory === 'node'
                && in_array($defaultname, ['sans', 'tans', 'testoptions'])) {
            $answertest = self::get_default('node', 'answertest', '');
            if (substr($answertest, 0, 2) === 'AT') {
                [$answertest, $sans, $tans, $testoptions] = self::split_answertest($answertest);
                if ($defaultname === 'sans') {
                    return $sans;
                } else if ($defaultname === 'tans') {
                    return $tans;
                } else if ($defaultname === 'testoptions') {
                    return $testoptions;
                }
            }
        }
        // We could return $default here but we'd rather the default file was fixed.
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
        $yaml = Yaml::parse($yamlstring);
        if (!$yaml) {
            throw new \stack_exception("The provided file does not contain valid YAML or XML.");
        }
        $xml = new SimpleXMLElement("<?xml version='1.0' encoding='UTF-8'?><quiz></quiz>");
        $question = $xml->addChild('question');
        $question->addAttribute('type', 'stack');

        self::array_to_xml($yaml, $question);
        // Name is a special case. Has text tag but no format.
        $name = (string) $xml->question->name ? (string) $xml->question->name : self::get_default('question', 'name', 'Default');
        $xml->question->name = new SimpleXMLElement('<root></root>');
        $xml->question->name->addChild('text', $name);
        return $xml;
    }

    /**
     * Recursively converts an associative array to XML.
     */
    public static function array_to_xml($data, &$xml) {
        foreach ($data as $key => $value) {
            if (strpos($key, 'format') !== false && in_array(str_replace('format', '', $key), self::TEXTFIELDS)) {
                $nodekey = str_replace('format', '', $key);
                if (!isset($xml->$nodekey)) {
                    $xml->addChild($nodekey);
                    $xml->{$nodekey}['format'] = $value;
                } else {
                    continue;
                }
            } else if (in_array($key, self::TEXTFIELDS)) {
                // Convert basic YAML field to node with text and format fields.
                if ($key !== 'name') {
                    // Name is used in multiple places and sometimes has text property and sometimes not.
                    // Handled in yaml_to_xml().
                    $subnode = $xml->addChild($key);
                    $subvalue = ['text' => $value];
                    if (isset($data[$key . 'format'])) {
                        $subnode['format'] = $data[$key . 'format'];
                    }
                    self::array_to_xml($subvalue, $subnode);
                } else {
                    $xml->$key = $value;
                }
            } else if (in_array($key, self::ARRAYFIELDS)) {
                // Certain fields need special handling to strip out
                // numeric keys.
                foreach ($value as $element) {
                    if (is_array($element)) {
                        $subnode = $xml->addChild($key);
                        self::array_to_xml($element, $subnode);
                    } else {
                        $xml->addChild($key, $element);
                    }
                }
            } else if (is_array($value)) {
                $subnode = $xml->addChild($key);
                self::array_to_xml($value, $subnode);
            } else {
                if ($key === 'text') {
                    $textnode = $xml->addChild('text');
                    self::add_cdata($textnode, $value);
                } else {
                    $xml->$key = $value;
                }
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
        foreach ($xmldata as $key => $value) {
            if (in_array($key, self::TEXTFIELDS)) {
                if (isset($value->text)) {
                    $output[$key] = (string) $value->text;
                } else {
                    $output[$key] = (string) $value;
                }
                if (isset($xmldata->{$key}['format'])) {
                    $output[$key . 'format'] = (string) $xmldata->{$key}['format'];
                }
            } else if ($value instanceof SimpleXMLElement && $value->count()) {
                if (in_array($key, self::ARRAYFIELDS)) {
                    $output[$key][] = self::xml_to_array($value);
                } else {
                    $output[$key] = [];
                    self::xml_to_array($value, $output[$key]);
                }
            } else {
                if (in_array($key, self::ARRAYFIELDS)) {
                    $output[$key][] = (string) $value;
                } else {
                    $output[$key] = (string) $value;
                }
            }
        }
        return $output;
    }

    /**
     * Detects differences between the provided XML or YAML and the default question structure.
     *
     * @param string $xml The XML or YAML string to compare.
     * @return string The differences in YAML format.
     */
    public static function detect_differences($xml) {
        if (!self::$defaults) {
                self::$defaults = Yaml::parseFile(__DIR__ . '/../questiondefaults.yml');
        }
        if (strpos($xml, '<question type=') !== false) {
            $xmldata = new SimpleXMLElement($xml);
        } else {
            $xmldata = self::yaml_to_xml($xml);
        }

        if (count($xmldata->question) != 1) {
            throw new \stack_exception("The provided XML file does not contain exactly one question element");
        }

        if (((string) $xmldata->question->attributes()->type) !== "stack") {
            throw new \stack_exception("The provided question is not of type STACK");
        }
        $plaindata = self::xml_to_array($xmldata);
        $diff = self::obj_diff(self::$defaults['question'], $plaindata['question']);
        if (!empty($plaindata['question']['input'])) {
            $diffinputs = [];
            foreach ($plaindata['question']['input'] as $input) {
                $diffinput = self::obj_diff(self::$defaults['input'], $input);
                $diffinputs[] = $diffinput;
            }
            $diff['input'] = $diffinputs;
        } else if (!isset($plaindata['question']['defaultgrade']) || $plaindata['question']['defaultgrade']) {
            $diff['input'] = [['name' => self::get_default('input', 'name', 'ans1'),
                'type' => self::get_default('input', 'type', 'algebraic'),
                'tans' => self::get_default('input', 'tans', 'ta1'),
                'forbidfloat' => self::get_default('input', 'forbidfloat', '1'),
                'requirelowestterms' => self::get_default('input', 'requirelowestterms', '0'),
                'checkanswertype' => self::get_default('input', 'checkanswertype', '0'),
                'mustverify' => self::get_default('input', 'mustverify', '1'),
                'showvalidation' => self::get_default('input', 'showvalidation', '1')]];
        } else {
            $diff['input'] = [];
        }
        if (!empty($plaindata['question']['prt'])) {
            $diffprts = [];
            foreach ($plaindata['question']['prt'] as $prt) {
                $diffprt = self::obj_diff(self::$defaults['prt'], $prt);
                foreach ($prt['node'] as $node) {
                    $diffnode = self::obj_diff(self::$defaults['node'], $node);
                    if (substr($diffnode['answertest'], 0, 2) === 'AT') {
                        unset($diffnode['sans']);
                        unset($diffnode['tans']);
                        unset($diffnode['testoptions']);
                    }
                    if (substr(self::get_default('node', 'answertest', 'AlgEquiv'), 0, 2) === 'AT' &&
                            substr($diffnode['answertest'], 0, 2) !== 'AT') {
                        // This occurs if answertest set in XML but summary in defaults.
                        // We need to build a summary from supplied XML fields and default summary.
                        $diffanswertest = isset($node['answertest']) ?
                            'AT' . $node['answertest'] : self::split_answertest(self::get_default('node', 'answertest', 'AlgEquiv'))[0];
                        $diffsans = isset($node['sans']) ? $node['sans'] : self::get_default('node', 'sans', 'ans1');
                        $difftans = isset($node['tans']) ? $node['tans'] : self::get_default('node', 'tans', 'ta1');
                        $difftestoptions = isset($node['testoptions']) ?
                            $node['testoptions'] : self::get_default('node', 'testoptions', '');
                        $diffnode['answertest'] =
                            "{$diffanswertest}({$diffsans},{$difftans}" .
                            ($difftestoptions !== '' ? ",{$difftestoptions}" : '') . ')';
                        unset($diffnode['sans']);
                        unset($diffnode['tans']);
                        unset($diffnode['testoptions']);
                    }

                    $diffprt['node'][] = $diffnode;
                }
                $diffprts[] = $diffprt;
            }
            $diff['prt'] = $diffprts;
        } else if (!isset($plaindata['question']['defaultgrade']) || $plaindata['question']['defaultgrade']) {
            $prtnode = ['name' => self::get_default('node', 'name', '0'),
                    'answertest' => self::get_default('node', 'answertest', 'AlgEquiv'),];
            if (substr($prtnode['answertest'], 0, 2) !== 'AT') {
                $prtnode['sans'] = self::get_default('node', 'sans', 'sans');
                $prtnode['tans'] = self::get_default('node', 'tans', 'tans');
            }
            $prtnode['quiet'] = self::get_default('node', 'quiet', '0');
            $diff['prt'] = [['name' => self::get_default('prt', 'name', 'prt1'),
                'autosimplify' => self::get_default('prt', 'autosimplify', '1'),
                'feedbackstyle' => self::get_default('prt', 'feedbackstyle', '1'),
                'node' => [$prtnode]]];
        } else {
            $diff['prt'] = [];
        }
        if (!empty($plaindata['question']['deployedseed'])) {
            $deployedseed = [];
            foreach ($plaindata['question']['deployedseed'] as $seed) {
                $deployedseed[] = (string) $seed;
            }
            if (count($deployedseed)) {
                $diff['deployedseed'] = $deployedseed;
            }
        }
        if (!empty($plaindata['question']['qtest'])) {
            $difftests = [];
            foreach ($plaindata['question']['qtest'] as $test) {
                $difftest = [];
                $difftest['testcase'] = $test['testcase'];
                $difftest = array_merge($difftest, self::obj_diff(self::$defaults['qtest'], $test));
                foreach ($test['testinput'] as $tinput) {
                    $difftinput = [];
                    $difftinput['name'] = $tinput['name'];
                    $difftinput = array_merge($difftinput, self::obj_diff(self::$defaults['testinput'], $tinput));
                    $difftest['testinput'][] = $difftinput;
                }
                foreach ($test['expected'] as $texpected) {
                    $difftexpected = [];
                    $difftexpected['name'] = $texpected['name'];
                    $difftexpected = array_merge($difftexpected, self::obj_diff(self::$defaults['expected'], $texpected));
                    $difftest['expected'][] = $difftexpected;
                }
                $difftests[] = $difftest;
            }
            $diff['qtest'] = $difftests;
        }
        $yaml = Yaml::dump($diff, 10, 2, Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK | Yaml::DUMP_COMPACT_NESTED_MAPPING);
        return $yaml;
    }

    /**
     * Compares two objects and returns the differences as an array.
     *
     * @param object $obj1 The first object to compare.
     * @param object $obj2 The second object to compare.
     * @return array An associative array of differences.
     */
    public static function obj_diff($obj1, $obj2): array {
        $a1 = (array) $obj1;
        $a2 = (array) $obj2;
        return self::arr_diff($a1, $a2);
    }

    /**
     * Compares two arrays and returns the differences as an associative array.
     *
     * @param array $a1 The first array to compare.
     * @param array $a2 The second array to compare.
     * @return array An associative array of differences.
     */
    public static function arr_diff($a1, $a2): array {
        $r = [];
        foreach ($a1 as $k => $v) {
            if (in_array($k, self::ALWAYS_SHOWN)) {
                if (array_key_exists($k, $a2)) {
                    $r[$k] = $a2[$k];
                } else {
                    $r[$k] = $v;
                }
                continue;
            }
            if (array_key_exists($k, $a2)) {
                if (is_array($v)) {
                    $rad = self::arr_diff($v, (array) $a2[$k]);
                    if (count($rad)) {
                        $r[$k] = $rad;
                    }
                    // Required to avoid rounding errors due to the
                    // conversion from string representation to double.
                } else if (is_double($v)) {
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

    /**
     * Adds a CDATA section to an XML node if the value contains special characters.
     *
     * @param SimpleXMLElement $xml The XML node to add the CDATA to.
     * @param string $value The value to add as CDATA.
     */
    public static function add_cdata(&$xml, $value) {
        if (!empty($value) && htmlspecialchars($value, ENT_COMPAT) != $value) {
            $node = dom_import_simplexml($xml);
            $no = $node->ownerDocument;
            $node->appendChild($no->createCDATASection($value));
        } else {
            $xml[0] = $value;
        }
    }

        /**
     * Split a string into a 4-item array such that:
     * 'AAAA(X(X,X)XX, YYY, ZZZ, WWW)'
     * becomes:
     * [0] => 'AAAA'
     * [1] => 'X(X,X)XX'
     * [2] => 'YYY'
     * [3] => 'ZZZ, WWW'
     * @param string $answertest
     * @return array
     */
    public static function split_answertest($answertest) {
        $result = [];
        $firstbracketpos = strpos($answertest, '(');
        $result[] = substr($answertest, 0, $firstbracketpos);
        $testprops = substr($answertest, $firstbracketpos + 1, strrpos($answertest, ')') - $firstbracketpos - 1);
        $bracketlevel = 0;
        $current = '';
        $count = 0;
        $len = strlen($testprops);
        for ($i = 0; $i < $len; $i++) {
            $char = $testprops[$i];
            if ($char === '(') {
                $bracketlevel++;
                $current .= $char;
            } else if ($char === ')') {
                $bracketlevel--;
                $current .= $char;
            } else if ($char === ',' && $bracketlevel === 0 && $count < 2) {
                $result[] = trim($current);
                $current = '';
                $count++;
            } else {
                $current .= $char;
            }
        }
        $result[] = trim($current);
        // Ensure always 4 items.
        while (count($result) < 4) {
            $result[] = '';
        }
        return $result;
    }
}

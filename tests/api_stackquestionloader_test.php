<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Unit tests for the Stack question type API.
 *
 * @package    qtype_stack
 * @copyright 2023 University of Edinburgh.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

namespace qtype_stack;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/fixtures/apifixtures.class.php');
require_once(__DIR__ . '/fixtures/test_base.php');
require_once(__DIR__ . '../../api/util/StackQuestionLoader.php');
use api\util\StackQuestionLoader;
use stack_api_test_data;
use qtype_stack_testcase;
use Symfony\Component\Yaml\Yaml;

/**
 * Add description here.
 * @group qtype_stack
 * @covers \qtype_stack
 */
final class api_stackquestionloader_test extends qtype_stack_testcase {

    public function test_question_loader(): void {

        $xml = stack_api_test_data::get_question_string('matrices');
        $ql = new StackQuestionLoader();
        $question = $ql->loadXML($xml)['question'];

        // Testing a representative selection of fields.
        $this->assertEquals('test_3_matrix', $question->name);
        $this->assertEquals('<p><span class="correct">Correct answer, well done.</span></p>', $question->prtcorrect);
        $this->assertEquals('html', $question->prtcorrectformat);
        // $x = $question->prts['prt1']->get_nodes_summary()[0];
        $this->assertEquals('-1', $question->prts['prt1']->get_nodes_summary()[0]->truenextnode);
        $this->assertEquals('1-0-T ', $question->prts['prt1']->get_nodes_summary()[0]->trueanswernote);
        $this->assertEquals(10, $question->prts['prt1']->get_nodes_summary()[0]->truescore);
        $this->assertEquals('=', $question->prts['prt1']->get_nodes_summary()[0]->truescoremode);
        $this->assertEquals('1', $question->prts['prt1']->get_nodes_summary()[0]->falsenextnode);
        $this->assertEquals('1-0-F', $question->prts['prt1']->get_nodes_summary()[0]->falseanswernote);
        $this->assertEquals(0, $question->prts['prt1']->get_nodes_summary()[0]->falsescore);
        $this->assertEquals('=', $question->prts['prt1']->get_nodes_summary()[0]->falsescoremode);
        $this->assertEquals(true, $question->prts['prt1']->get_nodes_summary()[0]->quiet);
        $this->assertEquals('ATAlgEquiv(ans1,TA)', $question->prts['prt1']->get_nodes_summary()[0]->answertest);
        $this->assertContains(86, $question->deployedseeds);
        $this->assertContains(219862533, $question->deployedseeds);
        $this->assertContains(1167893775, $question->deployedseeds);
        $this->assertEquals(3, count($question->deployedseeds));
    }

    public function test_question_loader_use_defaults(): void {

        global $CFG;
        $xml = stack_api_test_data::get_question_string('usedefaults');
        $ql = new StackQuestionLoader();
        $question = $ql->loadXML($xml)['question'];
        $this->assertEquals($question->options->get_option('decimals'), get_config('qtype_stack', 'decimals'));
        $this->assertEquals($question->options->get_option('scientificnotation'),
                get_config('qtype_stack', 'scientificnotation'));
        $this->assertEquals($question->options->get_option('assumepos'), get_config('qtype_stack', 'assumepositive'));
        $this->assertEquals($question->options->get_option('assumereal'), get_config('qtype_stack', 'assumereal'));
        $this->assertEquals($question->options->get_option('multiplicationsign'), get_config('qtype_stack', 'multiplicationsign'));
        $this->assertEquals($question->options->get_option('sqrtsign'), get_config('qtype_stack', 'sqrtsign'));
        $this->assertEquals($question->options->get_option('complexno'), get_config('qtype_stack', 'complexno'));
        $this->assertEquals($question->options->get_option('logicsymbol'), get_config('qtype_stack', 'logicsymbol'));
        $this->assertEquals($question->options->get_option('inversetrig'), get_config('qtype_stack', 'inversetrig'));
        $this->assertEquals($question->options->get_option('matrixparens'), get_config('qtype_stack', 'matrixparens'));
        $this->assertEquals($question->options->get_option('simplify'), get_config('qtype_stack', 'questionsimplify'));
        $this->assertEquals($question->inputs['ans1']->get_parameter('mustVerify'), get_config('qtype_stack', 'inputmustverify'));
        $this->assertEquals($question->inputs['ans1']->get_parameter('showValidation'),
                get_config('qtype_stack', 'inputshowvalidation'));
        $this->assertEquals($question->inputs['ans1']->get_parameter('insertStars'), get_config('qtype_stack', 'inputinsertstars'));
        $this->assertEquals($question->inputs['ans1']->get_parameter('forbidFloats'),
                get_config('qtype_stack', 'inputforbidfloat'));
        $this->assertEquals($question->inputs['ans1']->get_parameter('lowestTerms'),
                get_config('qtype_stack', 'inputrequirelowestterms'));
        $this->assertEquals($question->inputs['ans1']->get_parameter('sameType'),
                get_config('qtype_stack', 'inputcheckanswertype'));
        $this->assertEquals($question->inputs['ans1']->get_parameter('forbidWords'), get_config('qtype_stack', 'inputforbidwords'));
        $this->assertEquals($question->inputs['ans1']->get_parameter('boxWidth'), get_config('qtype_stack', 'inputboxsize'));
    }

    public function test_question_loader_do_not_use_defaults(): void {

        global $CFG;
        $xml = stack_api_test_data::get_question_string('optionset');
        $ql = new StackQuestionLoader();
        $question = $ql->loadXML($xml)['question'];
        $this->assertEquals($question->options->get_option('decimals'), ',');
        $this->assertEquals($question->options->get_option('scientificnotation'), '*10');
        $this->assertEquals($question->options->get_option('assumepos'), true);
        $this->assertEquals($question->options->get_option('assumereal'), true);
        $this->assertEquals($question->options->get_option('multiplicationsign'), 'cross');
        $this->assertEquals($question->options->get_option('sqrtsign'), false);
        $this->assertEquals($question->options->get_option('complexno'), 'j');
        $this->assertEquals($question->options->get_option('logicsymbol'), 'symbol');
        $this->assertEquals($question->options->get_option('inversetrig'), 'acos');
        $this->assertEquals($question->options->get_option('matrixparens'), '(');
        $this->assertEquals($question->options->get_option('simplify'), false);
        $this->assertEquals($question->inputs['ans1']->get_parameter('mustVerify'), false);
        $this->assertEquals($question->inputs['ans1']->get_parameter('showValidation'), false);
        $this->assertEquals($question->inputs['ans1']->get_parameter('insertStars'), true);
        $this->assertEquals($question->inputs['ans1']->get_parameter('forbidFloats'), false);
        $this->assertEquals($question->inputs['ans1']->get_parameter('lowestTerms'), true);
        $this->assertEquals($question->inputs['ans1']->get_parameter('sameType'), true);
        $this->assertEquals($question->inputs['ans1']->get_parameter('forbidWords'), 'test');
        $this->assertEquals($question->inputs['ans1']->get_parameter('boxWidth'), 30);
    }

    public function test_question_loader_base_question(): void {
        global $CFG;
        $xml = stack_api_test_data::get_question_string('empty');
        $question = StackQuestionLoader::loadXML($xml)['question'];
        $this->assertEquals('Default', $question->name);
        $this->assertEquals('Correct answer, well done.', $question->prtcorrect);
        $this->assertEquals('html', $question->prtcorrectformat);
        $this->assertEquals('-1', $question->prts['prt1']->get_nodes_summary()[0]->truenextnode);
        $this->assertEquals('prt1-1-T', $question->prts['prt1']->get_nodes_summary()[0]->trueanswernote);
        $this->assertEquals(1, $question->prts['prt1']->get_nodes_summary()[0]->truescore);
        $this->assertEquals('=', $question->prts['prt1']->get_nodes_summary()[0]->truescoremode);
        $this->assertEquals('-1', $question->prts['prt1']->get_nodes_summary()[0]->falsenextnode);
        $this->assertEquals('prt1-1-F', $question->prts['prt1']->get_nodes_summary()[0]->falseanswernote);
        $this->assertEquals(0, $question->prts['prt1']->get_nodes_summary()[0]->falsescore);
        $this->assertEquals('=', $question->prts['prt1']->get_nodes_summary()[0]->falsescoremode);
        $this->assertEquals(false, $question->prts['prt1']->get_nodes_summary()[0]->quiet);
        $this->assertEquals('ATAlgEquiv(ans1,ta1)', $question->prts['prt1']->get_nodes_summary()[0]->answertest);
        $this->assertEquals($question->inputs['ans1']->get_parameter('mustVerify'), get_config('qtype_stack', 'inputmustverify'));
        $this->assertEquals($question->inputs['ans1']->get_parameter('showValidation'),
                get_config('qtype_stack', 'inputshowvalidation'));
        $this->assertEquals($question->inputs['ans1']->get_parameter('insertStars'), get_config('qtype_stack', 'inputinsertstars'));
        $this->assertEquals($question->inputs['ans1']->get_parameter('forbidFloats'),
                get_config('qtype_stack', 'inputforbidfloat'));
        $this->assertEquals($question->inputs['ans1']->get_parameter('lowestTerms'),
                get_config('qtype_stack', 'inputrequirelowestterms'));
        $this->assertEquals($question->inputs['ans1']->get_parameter('sameType'),
                get_config('qtype_stack', 'inputcheckanswertype'));
        $this->assertEquals($question->inputs['ans1']->get_parameter('forbidWords'), get_config('qtype_stack', 'inputforbidwords'));
        $this->assertEquals($question->inputs['ans1']->get_parameter('boxWidth'), get_config('qtype_stack', 'inputboxsize'));
    }

        public function test_loadxml_summary_default(): void {
        if (!defined('Symfony\Component\Yaml\Yaml::DUMP_COMPACT_NESTED_MAPPING')) {
            $this->markTestSkipped('Symfony YAML extension is not available.');
            return;
        }
        set_config('stackapi', true, 'qtype_stack');
        StackQuestionLoader::$defaults = Yaml::parseFile(__DIR__ . '/fixtures/questiondefaultssugar.yml');
        $questionyaml = file_get_contents(__DIR__ . '/fixtures/questionymlpartial.yml');
        $question = StackQuestionLoader::loadxml($questionyaml)['question'];

        // Check prt fields.
        $this->assertCount(2, $question->prts);
        $prt1 = $question->prts['prt1'];
        $prt2 = $question->prts['prt2'];
        $this->assertCount(1, $prt1->get_nodes_summary());
        $this->assertEquals('0', (string) $prt1->get_nodes_summary()[0]->nodename);
        $this->assertEquals('', (string) $prt1->get_nodes_summary()[0]->description);
        $this->assertEquals('ATDiff(ans1,ta1,ev(1,simp))', (string) $prt1->get_nodes_summary()[0]->answertest);
        $this->assertEquals(true, $prt1->get_nodes_summary()[0]->quiet);
        $this->assertEquals('=', (string) $prt1->get_nodes_summary()[0]->truescoremode);

        $this->assertCount(1, $prt2->get_nodes_summary());
        $this->assertEquals('0', (string) $prt2->get_nodes_summary()[0]->nodename);
        $this->assertEquals('ATAlgEquiv(ans1,ta2)', (string) $prt2->get_nodes_summary()[0]->answertest);
        $this->assertEquals(false, (string) $prt2->get_nodes_summary()[0]->quiet);
        $this->assertEquals('1', (string) $prt2->get_nodes_summary()[0]->falsescore);
        set_config('stackapi', false, 'qtype_stack');
    }


    public function test_loadxml_summary(): void {
        if (!defined('Symfony\Component\Yaml\Yaml::DUMP_COMPACT_NESTED_MAPPING')) {
            $this->markTestSkipped('Symfony YAML extension is not available.');
            return;
        }

        $questionyaml = file_get_contents(__DIR__ . '/fixtures/questionymlpartial.yml');
        $question = StackQuestionLoader::loadxml($questionyaml)['question'];

        // Check prt fields.
        $this->assertCount(2, $question->prts);
        $prt1 = $question->prts['prt1'];
        $prt2 = $question->prts['prt2'];
        $this->assertCount(1, $prt1->get_nodes_summary());
        $this->assertEquals('0', (string) $prt1->get_nodes_summary()[0]->nodename);
        $this->assertEquals('', (string) $prt1->get_nodes_summary()[0]->description);
        $this->assertEquals('ATDiff(ans1,ta1,ev(1,simp))', (string) $prt1->get_nodes_summary()[0]->answertest);
        $this->assertEquals(true, $prt1->get_nodes_summary()[0]->quiet);
        $this->assertEquals('=', (string) $prt1->get_nodes_summary()[0]->truescoremode);

        $this->assertCount(1, $prt2->get_nodes_summary());
        $this->assertEquals('0', (string) $prt2->get_nodes_summary()[0]->nodename);
        $this->assertEquals('ATAlgEquiv(ans1,ta2)', (string) $prt2->get_nodes_summary()[0]->answertest);
        $this->assertEquals(false, (string) $prt2->get_nodes_summary()[0]->quiet);
        $this->assertEquals('1', (string) $prt2->get_nodes_summary()[0]->falsescore);
    }

    public function test_yaml_to_xml()
    {
        if (!defined('Symfony\Component\Yaml\Yaml::DUMP_COMPACT_NESTED_MAPPING')) {
            $this->markTestSkipped('Symfony YAML extension is not available.');
            return;
        }
        $yaml = file_get_contents(__DIR__ . '/fixtures/questionyml.yml');
        $xml = StackQuestionLoader::yaml_to_xml($yaml);
        $this->assertEquals('Test question', (string)$xml->question->name->text);
        $this->assertEquals(1,
            preg_match('/<p>Question<\/p><p>\[\[input:ans1\]\] \[\[validation:ans1\]\]<\/p>\n    <p>' .
                '\[\[input:ans2\]\] \[\[validation:ans2\]\]<\/p>/s', (string) $xml->question->questiontext->text));
       $this->assertEquals('html', (string)$xml->question->questiontext['format']);
       $this->assertEquals(false, isset($xml->question->questiontext->format));
    }

    public function test_array_to_xml_inverse()
    {
        $data = [
            'name' => 'Test',
            'questiontext' => 'What is 2+2?',
            'questiontextformat' => 'moodle',
            'input' => [
                [
                    'name' => 'ans1',
                    'tans' => '1'
                ],
                [
                    'name' => 'ans1',
                    'tans' => '2'
                ]
            ],
            'prt' => [
                [
                    'name' => 'prt1',
                    'value' => '23',
                    'node' => [
                        [
                            'name' => '0',
                            'sans' => '011',
                            'tans' => '022'
                        ],
                        [
                            'name' => '1',
                            'sans' => '033',
                            'tans' => '044'
                        ]
                    ]
                ]
            ]
        ];
        $xml = new \SimpleXMLElement('<question></question>');
        StackQuestionLoader::array_to_xml($data, $xml);
        $this->assertEquals('Test', $xml->name);
        $this->assertEquals('What is 2+2?', $xml->questiontext->text);
        $this->assertEquals('moodle', $xml->questiontext['format']);
        $this->assertEquals(2, count($xml->input));
        $this->assertEquals(1, count($xml->prt));
        $this->assertEquals('prt1', $xml->prt->name);
        $this->assertEquals(2, count($xml->prt[0]->node));
        $this->assertEquals('1', $xml->prt[0]->node[1]->name);
        $this->assertEquals('033', $xml->prt[0]->node[1]->sans);
        $this->assertEquals('044', $xml->prt[0]->node[1]->tans);
        $array = StackQuestionLoader::xml_to_array($xml);
        $this->assertEqualsCanonicalizing($data, $array);
    }

    public function test_obj_diff()
    {
        $a = (object) ['a' => 1, 'b' => 2];
        $b = (object) ['a' => 1, 'b' => 3];
        $diff = StackQuestionLoader::obj_diff($a, $b);
        $this->assertArrayHasKey('b', $diff);
        $this->assertEquals(3, $diff['b']);
    }

    public function test_arr_diff()
    {
        $a = ['x' => 5, 'y' => 6, 'z' => (0.1+0.7)*10, 'a' => [1 => 'x', 2 => 'y']];
        $b = ['x' => 5, 'y' => 7, 'z' => 8, 'a' => [1 => 'x', 2 => 'z']];
        $diff = StackQuestionLoader::arr_diff($a, $b);
        $this->assertEquals(2, count($diff));
        $this->assertArrayHasKey('y', $diff);
        $this->assertEquals(7, $diff['y']);
        $this->assertEquals(1, count($diff['a']));
        $this->assertEquals('z', $diff['a'][2]);
    }

    public function test_get_default()
    {
        set_config('stackapi', true, 'qtype_stack');
        $default = StackQuestionLoader::get_default('question', 'name', 'Fallback');
        $this->assertEquals('Default', $default);
        set_config('stackapi', false, 'qtype_stack');
        $default = StackQuestionLoader::get_default('question', 'name', 'Fallback');
        $this->assertEquals('Fallback', $default);
    }

    public function test_detect_difference()
    {
        if (!defined('Symfony\Component\Yaml\Yaml::DUMP_COMPACT_NESTED_MAPPING')) {
            $this->markTestSkipped('Symfony YAML extension is not available.');
            return;
        }
        $xml = '<quiz><question type="stack"><name><text>Test</text></name></question></quiz>';
        $yaml = StackQuestionLoader::detect_differences($xml);
        $this->assertStringContainsString('name: Test', $yaml);
    }

    public function test_detect_difference_yml()
    {
        if (!defined('Symfony\Component\Yaml\Yaml::DUMP_COMPACT_NESTED_MAPPING')) {
            $this->markTestSkipped('Symfony YAML extension is not available.');
            return;
        }
        // Test the difference detection with a full question.
        $yaml = file_get_contents(__DIR__ . '/fixtures/questionyml.yml');
        $diff = StackQuestionLoader::detect_differences($yaml);
        $diffarray = Yaml::parse($diff);
        $this->assertEquals(10, count($diffarray));
        $expected = [
            'name' => 'Test question',
            'questiontext' => "<p>Question</p><p>[[input:ans1]] [[validation:ans1]]</p>\n    <p>[[input:ans2]] [[validation:ans2]]</p>\n",
            'questionvariables' => 'ta1:1;ta2:2;',
            'questionsimplify' => '1',
            'prtcorrect' => '<p><i class="fa fa-check"></i> Correct answer*, well done.</p>',
            'multiplicationsign' => 'cross',
            'input' => [
                [
                    'name' => 'ans1',
                    'type' => 'algebraic',
                    'tans' => 'ta1',
                    'boxsize' => 25,
                    'forbidfloat' => '1',
                    'requirelowestterms' => '0',
                    'checkanswertype' => '0',
                    'mustverify' => '1',
                    'showvalidation' => '1'
                ],
                [
                    'name' => 'ans2',
                    'type' => 'algebraic',
                    'tans' => 'ta2',
                    'forbidfloat' => '1',
                    'requirelowestterms' => '0',
                    'checkanswertype' => '0',
                    'mustverify' => '1',
                    'showvalidation' => '1'
                ]
            ],
            'prt' => [
                [
                    'name' => 'prt1',
                    'value' => '2',
                    'autosimplify' => '1',
                    'feedbackstyle' => '1',
                    'node' => [
                        [
                            'name' => '0',
                            'answertest' => 'AlgEquiv',
                            'sans' => 'ans1',
                            'tans' => 'ta1',
                            'quiet' => '1'
                        ]
                    ]
                ],
                [
                    'name' => 'prt2',
                    'value' => '1.0000001',
                    'autosimplify' => '1',
                    'feedbackstyle' => '1',
                    'node' => [
                        [
                            'name' => '0',
                            'answertest' => 'AlgEquiv',
                            'sans' => 'ans2',
                            'tans' => 'ta2',
                            'quiet' => '0',
                            'falsescore' => '1'
                        ]
                    ]
                ]
            ],
            'deployedseed' => [
                1,
                2,
                3
            ],
            'qtest' => [
                [
                    'testcase' => '1',
                    'description' => 'A test',
                    'testinput' => [
                        [
                            'name' => 'ans1'
                        ],
                        [
                            'name' => 'ans2',
                            'value' => 'ta2'
                        ]
                    ],
                    'expected' => [
                        [
                            'name' => 'prt1',
                            'expectedscore' => '1.0000000',
                            'expectedpenalty' => '0.0000000'
                        ],
                        [
                            'name' => 'prt2',
                            'expectedscore' => '1.0000000',
                            'expectedpenalty' => '0.0000000',
                            'expectedanswernote' => '2-0-T'
                        ]
                    ]
                ]
            ]
        ];
        $expectedstring = "name: 'Test question'\nquestiontext: |\n  <p>Question</p><p>[[input:ans1]] [[validation:ans1]]</p>" .
            "\n      <p>[[input:ans2]] [[validation:ans2]]</p>\nquestionvariables: 'ta1:1;ta2:2;'\nquestionsimplify: '1'\nprtcorrect: '<p>" .
            "<i class=\"fa fa-check\"></i> Correct answer*, well done.</p>'\nmultiplicationsign: cross\ninput:\n  - " .
            "name: ans1\n    type: algebraic\n    tans: ta1\n    boxsize: '25'\n    forbidfloat: '1'\n    " .
            "requirelowestterms: '0'\n    checkanswertype: '0'\n    mustverify: '1'\n    showvalidation: '1'\n  - name: " .
            "ans2\n    type: algebraic\n    tans: ta2\n    forbidfloat: '1'\n    requirelowestterms: '0'\n    " .
            "checkanswertype: '0'\n    mustverify: '1'\n    showvalidation: '1'\nprt:\n  - name: prt1\n    value: '2'\n    autosimplify: '1'\n    feedbackstyle: '1'\n    " .
            "node:\n      - name: '0'\n        answertest: AlgEquiv\n        sans: ans1\n        tans: ta1\n        quiet: '1'\n  - name: prt2\n    " .
            "value: '1.0000001'\n    autosimplify: '1'\n    feedbackstyle: '1'\n    node:\n      - name: '0'\n        answertest: AlgEquiv\n        sans: ans2\n        tans: ta2\n        quiet: '0'\n        falsescore: '1'\n" .
            "deployedseed:\n  - '1'\n  - '2'\n  - '3'\nqtest:\n  - testcase: '1'\n    description: 'A test'\n    " .
            "testinput:\n      - name: ans1\n      - name: ans2\n        value: ta2\n    expected:\n      - name: prt1" .
            "\n        expectedscore: '1.0000000'\n        expectedpenalty: '0.0000000'\n      " .
            "- name: prt2\n        expectedscore: '1.0000000'\n        expectedpenalty:" .
            " '0.0000000'\n        expectedanswernote: 2-0-T\n";
        $this->assertStringContainsString($expectedstring, $diff);
        $this->assertEqualsCanonicalizing($expected, $diffarray);

        // Check results when using answertest summary in defaults.
        set_config('stackapi', true, 'qtype_stack');
        StackQuestionLoader::$defaults = Yaml::parseFile(__DIR__ . '/fixtures/questiondefaultssugar.yml');
        $diff = StackQuestionLoader::detect_differences($yaml);
        $diffarray = Yaml::parse($diff);
        $this->assertEquals(10, count($diffarray));
        $expected['prt'][0]['node'][0] = [
                            'name' => '0',
                            'answertest' => 'ATAlgEquiv(ans1,ta1)',
                            'quiet' => '1',
        ];
        $expected['prt'][1]['node'][0] = [
                            'name' => '0',
                            'answertest' => 'ATAlgEquiv(ans2,ta2)',
                            'quiet' => '0',
                            'falsescore' => '1',
        ];
        $this->assertEqualsCanonicalizing($expected, $diffarray);
        set_config('stackapi', true, 'qtype_stack');

        // Test the difference detection with a completely default XML question.
        StackQuestionLoader::$defaults = null;
        $blankxml = '<quiz><question type="stack"></question></quiz>';
        $expected = [
            'name' => 'Default',
            'questionsimplify' => '1',
            'input' => [
                [
                    'name' => 'ans1',
                    'type' => 'algebraic',
                    'tans' => 'ta1',
                    'forbidfloat' => '1',
                    'requirelowestterms' => '0',
                    'checkanswertype' => '0',
                    'mustverify' => '1',
                    'showvalidation' => '1'
                ]
            ],
            'prt' => [
                        [
                            'name' => 'prt1',
                            'autosimplify' => '1',
                            'feedbackstyle' => '1',
                            'node' => [
                                [
                                    'name' => '0',
                                    'answertest' => 'AlgEquiv',
                                    'sans' => 'ans1',
                                    'tans' => 'ta1',
                                    'quiet' => '0'
                                ]
                            ]
                        ]
                ],
        ];
        $diff = StackQuestionLoader::detect_differences($blankxml, null);
        $diffarray = Yaml::parse($diff);
        $this->assertEquals(4, count($diffarray));
        $this->assertEqualsCanonicalizing($expected, $diffarray);

        // Check results when using answertest summary in defaults.
        StackQuestionLoader::$defaults = Yaml::parseFile(__DIR__ . '/fixtures/questiondefaultssugar.yml');
        set_config('stackapi', true, 'qtype_stack');
        $diff = StackQuestionLoader::detect_differences($blankxml);
        $diffarray = Yaml::parse($diff);
        $this->assertEquals(4, count($diffarray));
        $expected['prt'][0]['node'][0] = [
                            'name' => '0',
                            'answertest' => 'ATAlgEquiv(ans1,ta1)',
                            'quiet' => '0',
        ];
        $this->assertEqualsCanonicalizing($expected, $diffarray);
        set_config('stackapi', false, 'qtype_stack');

        // Test the difference detection with an info XML question.
        $infoxml = '<quiz><question type="stack"><defaultgrade>0</defaultgrade></question></quiz>';
        $expected = [
            'name' => 'Default',
            'questionsimplify' => '1',
            'defaultgrade' => '0',
            'input' => [],
            'prt' => []
        ];
        $diff = StackQuestionLoader::detect_differences($infoxml, null);
        $diffarray = Yaml::parse($diff);
        $this->assertEquals(5, count($diffarray));

        $this->assertEqualsCanonicalizing($expected, $diffarray);

        // Test the difference detection with an info XML question.
        $infoxml = '<quiz><question type="stack"><defaultgrade>0</defaultgrade></question></quiz>';
        $expected = [
            'name' => 'Default',
            'defaultgrade' => '0',
            'questionsimplify' => '1',
            'input' => [],
            'prt' => []
        ];
        $diff = StackQuestionLoader::detect_differences($infoxml);
        $diffarray = Yaml::parse($diff);
        $this->assertEquals(5, count($diffarray));
        $this->assertEqualsCanonicalizing($expected, $diffarray);

        // Check results when using answertest summary in defaults.
        set_config('stackapi', true, 'qtype_stack');
        StackQuestionLoader::$defaults = Yaml::parseFile(__DIR__ . '/fixtures/questiondefaultssugar.yml');
        $diff = StackQuestionLoader::detect_differences($infoxml);
        $diffarray = Yaml::parse($diff);
        $this->assertEquals(5, count($diffarray));
        $this->assertEqualsCanonicalizing($expected, $diffarray);
        set_config('stackapi', false, 'qtype_stack');
    }

        public function test_split_answertest_basic(): void {
        $input = 'ATAlgEquiv(x^2+2x+1, (x+1)^2, 1, ignoreorder)';
        $expected = [
            'ATAlgEquiv',
            'x^2+2x+1',
            '(x+1)^2',
            '1, ignoreorder',
        ];
        $this->assertEquals($expected, StackQuestionLoader::split_answertest($input));
    }

    public function test_split_answertest_nested_parentheses(): void {
        $input = 'ATTest(foo(bar, baz), qux, quux, corge)';
        $expected = [
            'ATTest',
            'foo(bar, baz)',
            'qux',
            'quux, corge',
        ];
        $this->assertEquals($expected, StackQuestionLoader::split_answertest($input));
    }

    public function test_split_answertest_missing_items(): void {
        $input = 'ATTest(foo)';
        $expected = [
            'ATTest',
            'foo',
            '',
            '',
        ];
        $this->assertEquals($expected, StackQuestionLoader::split_answertest($input));
    }

    public function test_split_answertest_extra_commas(): void {
        $input = 'ATTest(foo, bar, baz, qux, quux)';
        $expected = [
            'ATTest',
            'foo',
            'bar',
            'baz, qux, quux',
        ];
        $this->assertEquals($expected, StackQuestionLoader::split_answertest($input));
    }

    public function test_split_answertest_spaces(): void {
        $input = 'ATTest( foo ( bar ), baz , qux )';
        $expected = [
            'ATTest',
            'foo ( bar )',
            'baz',
            'qux',
        ];
        $this->assertEquals($expected, StackQuestionLoader::split_answertest($input));
    }
}

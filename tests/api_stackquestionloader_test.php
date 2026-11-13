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
    /**
     * Some tests replace the cached defaults with a fixture. Clear the cache after
     * every test, so that no test inherits another test's defaults.
     */
    public function tearDown(): void {
        StackQuestionLoader::$defaults = null;
        parent::tearDown();
    }

    public function test_question_loader(): void {

        $xml = stack_api_test_data::get_question_string('matrices');
        $ql = new StackQuestionLoader();
        $question = $ql->loadXML($xml)['question'];

        // Testing a representative selection of fields.
        $this->assertEquals('test_3_matrix', $question->name);
        $this->assertEquals('<p>[[commonstring key="symbolicprtcorrectfeedback"/]] ' .
            '[[commonstring key="defaultprtcorrectfeedback"/]]</p>', $question->prtcorrect);
        $this->assertEquals('html', $question->prtcorrectformat);
        $nodesummary = $question->prts['prt1']->get_nodes_summary()[0];
        $this->assertEquals('-1', $nodesummary->truenextnode);
        $this->assertEquals('1-0-T ', $nodesummary->trueanswernote);
        $this->assertEquals(10, $nodesummary->truescore);
        $this->assertEquals('=', $nodesummary->truescoremode);
        $this->assertEquals('1', $nodesummary->falsenextnode);
        $this->assertEquals('1-0-F', $nodesummary->falseanswernote);
        $this->assertEquals(0, $nodesummary->falsescore);
        $this->assertEquals('=', $nodesummary->falsescoremode);
        $this->assertEquals(true, $nodesummary->quiet);
        $this->assertEquals('ATAlgEquiv(ans1,TA)', $nodesummary->answertest);
        $this->assertContains(86, $question->deployedseeds);
        $this->assertContains(219862533, $question->deployedseeds);
        $this->assertContains(1167893775, $question->deployedseeds);
        $this->assertEquals(3, count($question->deployedseeds));
    }

    public function test_question_loader_use_defaults(): void {
        $xml = stack_api_test_data::get_question_string('usedefaults');
        $ql = new StackQuestionLoader();
        $question = $ql->loadXML($xml)['question'];

        $this->assertEquals($question->options->get_option('decimals'), get_config('qtype_stack', 'decimals'));
        $this->assertEquals(
            $question->options->get_option('scientificnotation'),
            get_config('qtype_stack', 'scientificnotation')
        );
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
        $this->assertEquals(
            $question->inputs['ans1']->get_parameter('showValidation'),
            get_config('qtype_stack', 'inputshowvalidation')
        );
        $this->assertEquals($question->inputs['ans1']->get_parameter('insertStars'), get_config('qtype_stack', 'inputinsertstars'));
        $this->assertEquals(
            $question->inputs['ans1']->get_parameter('forbidFloats'),
            get_config('qtype_stack', 'inputforbidfloat')
        );
        $this->assertEquals(
            $question->inputs['ans1']->get_parameter('lowestTerms'),
            get_config('qtype_stack', 'inputrequirelowestterms')
        );
        $this->assertEquals(
            $question->inputs['ans1']->get_parameter('sameType'),
            get_config('qtype_stack', 'inputcheckanswertype')
        );
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
        $this->assertEquals(
            '<p>Default question</p><p>[[input:ans1]] [[validation:ans1]]</p>',
            $question->questiontext
        );
        $this->assertEquals('html', $question->questiontextformat);
        $this->assertEquals(
            '',
            $question->generalfeedback
        );
        $this->assertEquals('html', $question->generalfeedbackformat);
        $this->assertEquals(
            '[[commonstring key="symbolicprtcorrectfeedback"/]] ' .
            '[[commonstring key="defaultprtcorrectfeedback"/]]',
            $question->prtcorrect
        );
        $this->assertEquals('html', $question->prtcorrectformat);
        $this->assertEquals(
            '[[commonstring key="symbolicprtpartiallycorrectfeedback"/]] ' .
            '[[commonstring key="defaultprtpartiallycorrectfeedback"/]]',
            $question->prtpartiallycorrect
        );
        $this->assertEquals('html', $question->prtpartiallycorrectformat);
        $this->assertEquals(
            '[[commonstring key="symbolicprtincorrectfeedback"/]] ' .
            '[[commonstring key="defaultprtincorrectfeedback"/]]',
            $question->prtincorrect
        );
        $this->assertEquals('html', $question->prtincorrectformat);
        $this->assertEquals(1, $question->defaultmark);
        $this->assertEquals(0.1, $question->penalty);
        if (isset($question->hidden)) {
            // Moodle > 4.1.
            $this->assertEquals(0, $question->hidden);
        }
        $this->assertEquals(
            \get_config('qtype_stack', 'stackversion'),
            $question->stackversion
        );
        $this->assertEquals(
            'ta1:1;',
            $question->questionvariables
        );
        $this->assertEquals(
            '[[feedback:prt1]]',
            $question->specificfeedback
        );
        $this->assertEquals('html', $question->specificfeedbackformat);
        $this->assertEquals(
            '{@ta1@}',
            $question->questionnote
        );
        $this->assertEquals('html', $question->questionnoteformat);
        $this->assertEquals(
            '',
            $question->questiondescription
        );
        $this->assertEquals('html', $question->questiondescriptionformat);

        $this->assertEquals(\get_config('qtype_stack', 'decimals'), $question->options->get_option('decimals'));
        $this->assertEquals(\get_config('qtype_stack', 'scientificnotation'), $question->options->get_option('scientificnotation'));
        $this->assertEquals(\get_config('qtype_stack', 'assumepositive'), $question->options->get_option('assumepos'));
        $this->assertEquals(\get_config('qtype_stack', 'assumereal'), $question->options->get_option('assumereal'));
        $this->assertEquals(\get_config('qtype_stack', 'multiplicationsign'), $question->options->get_option('multiplicationsign'));
        $this->assertEquals(\get_config('qtype_stack', 'sqrtsign'), $question->options->get_option('sqrtsign'));
        $this->assertEquals(\get_config('qtype_stack', 'complexno'), $question->options->get_option('complexno'));
        $this->assertEquals(\get_config('qtype_stack', 'logicsymbol'), $question->options->get_option('logicsymbol'));
        $this->assertEquals(\get_config('qtype_stack', 'inversetrig'), $question->options->get_option('inversetrig'));
        $this->assertEquals(\get_config('qtype_stack', 'matrixparens'), $question->options->get_option('matrixparens'));
        $this->assertEquals(\get_config('qtype_stack', 'questionsimplify'), $question->options->get_option('simplify'));
        $this->assertEquals(0, $question->isbroken);

        $this->assertEquals(1, $question->prts['prt1']->get_value());
        $this->assertEquals(1, $question->prts['prt1']->get_feedbackstyle());
        $this->assertEquals('', $question->prts['prt1']->get_feedbackvariables_keyvals());

        $nodesummary = $question->prts['prt1']->get_nodes_summary()[0];
        $this->assertEquals('', $nodesummary->description);
        $this->assertEquals('ATAlgEquiv(ans1,ta1)', $nodesummary->answertest);
        $this->assertEquals(0, $nodesummary->quiet);
        $this->assertEquals('-1', $nodesummary->truenextnode);
        $this->assertEquals('prt1-1-T', $nodesummary->trueanswernote);
        $this->assertEquals(1, $nodesummary->truescore);
        $this->assertEquals('=', $nodesummary->truescoremode);
        $this->assertEquals('-1', $nodesummary->falsenextnode);
        $this->assertEquals('prt1-1-F', $nodesummary->falseanswernote);
        $this->assertEquals(0, $nodesummary->falsescore);
        $this->assertEquals('=', $nodesummary->falsescoremode);
        $this->assertEquals(0, $nodesummary->quiet);
        $this->assertEquals($question->inputs['ans1']->get_parameter('mustVerify'), get_config('qtype_stack', 'inputmustverify'));
        $this->assertEquals(
            $question->inputs['ans1']->get_parameter('showValidation'),
            get_config('qtype_stack', 'inputshowvalidation')
        );
        $this->assertEquals($question->inputs['ans1']->get_parameter('insertStars'), get_config('qtype_stack', 'inputinsertstars'));
        $this->assertEquals(
            $question->inputs['ans1']->get_parameter('forbidFloats'),
            get_config('qtype_stack', 'inputforbidfloat')
        );
        $this->assertEquals(
            $question->inputs['ans1']->get_parameter('lowestTerms'),
            get_config('qtype_stack', 'inputrequirelowestterms')
        );
        $this->assertEquals(
            $question->inputs['ans1']->get_parameter('sameType'),
            get_config('qtype_stack', 'inputcheckanswertype')
        );
        $this->assertEquals($question->inputs['ans1']->get_parameter('forbidWords'), get_config('qtype_stack', 'inputforbidwords'));
        $this->assertEquals($question->inputs['ans1']->get_parameter('boxWidth'), get_config('qtype_stack', 'inputboxsize'));
    }

    public function test_question_loader_collects_files_from_all_text_fields(): void {
        $xml = $this->get_minimal_question_xml(
            '<questiontext><text>[[input:ans1]] [[validation:ans1]]</text><file name="questiontext.txt">cXRleHQ=</file></questiontext>' .
            '<generalfeedback><text></text><file name="generalfeedback.txt">Z2ZlZWRiYWNr</file></generalfeedback>' .
            '<specificfeedback><text></text><file name="specificfeedback.txt">c2ZlZWRiYWNr</file></specificfeedback>' .
            '<questionnote><text></text><file name="questionnote.txt">bm90ZQ==</file></questionnote>' .
            '<questiondescription><text></text><file name="questiondescription.txt">ZGVzY3JpcHRpb24=</file></questiondescription>' .
            '<prtcorrect><text></text><file name="prtcorrect.txt">Y29ycmVjdA==</file></prtcorrect>' .
            '<prtpartiallycorrect><text></text><file name="prtpartiallycorrect.txt">cGFydGlhbA==</file></prtpartiallycorrect>' .
            '<prtincorrect><text></text><file name="prtincorrect.txt">aW5jb3JyZWN0</file></prtincorrect>' .
            '<prt><name>prt1</name><node><name>0</name><trueanswernote>T</trueanswernote>' .
            '<truefeedback><text></text><file name="truefeedback.txt">dHJ1ZQ==</file></truefeedback>' .
            '<falseanswernote>F</falseanswernote>' .
            '<falsefeedback><text></text><file name="falsefeedback.txt">ZmFsc2U=</file></falsefeedback>' .
            '</node></prt>' .
            '<hint><text></text><file name="hint.txt">aGludA==</file></hint>' .
            '<hint><hintcontent><text></text><file name="legacyhint.txt">bGVnYWN5</file></hintcontent></hint>'
        );

        $question = StackQuestionLoader::loadXML($xml)['question'];

        $this->assertEquals([
            'questiontext.txt' => 'cXRleHQ=',
            'generalfeedback.txt' => 'Z2ZlZWRiYWNr',
            'specificfeedback.txt' => 'c2ZlZWRiYWNr',
            'questionnote.txt' => 'bm90ZQ==',
            'questiondescription.txt' => 'ZGVzY3JpcHRpb24=',
            'prtcorrect.txt' => 'Y29ycmVjdA==',
            'prtpartiallycorrect.txt' => 'cGFydGlhbA==',
            'prtincorrect.txt' => 'aW5jb3JyZWN0',
            'truefeedback.txt' => 'dHJ1ZQ==',
            'falsefeedback.txt' => 'ZmFsc2U=',
            'hint.txt' => 'aGludA==',
            'legacyhint.txt' => 'bGVnYWN5',
        ], $question->pluginfiles);
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

    public function test_yaml_to_xml(): void {
        if (!defined('Symfony\Component\Yaml\Yaml::DUMP_COMPACT_NESTED_MAPPING')) {
            $this->markTestSkipped('Symfony YAML extension is not available.');
            return;
        }
        $yaml = file_get_contents(__DIR__ . '/fixtures/questionyml.yml');
        $xml = StackQuestionLoader::yaml_to_xml($yaml);
        $this->assertEquals('Test question', (string)$xml->question->name->text);
        $this->assertEquals(
            1,
            preg_match(
                '/<p>Question<\/p><p>\[\[input:ans1\]\] \[\[validation:ans1\]\]<\/p>\n    <p>' .
                '\[\[input:ans2\]\] \[\[validation:ans2\]\]<\/p>/s',
                (string) $xml->question->questiontext->text
            )
        );
        $this->assertEquals('html', (string)$xml->question->questiontext['format']);
        $this->assertEquals(false, isset($xml->question->questiontext->format));
    }

    public function test_array_to_xml_inverse(): void {
        $data = [
            'name' => 'Test',
            'questiontext' => 'What is 2+2?',
            'questiontextformat' => 'moodle',
            'input' => [
                [
                    'name' => 'ans1',
                    'tans' => '1',
                ],
                [
                    'name' => 'ans1',
                    'tans' => '2',
                ],
            ],
            'prt' => [
                [
                    'name' => 'prt1',
                    'value' => '23',
                    'node' => [
                        [
                            'name' => '0',
                            'sans' => '011',
                            'tans' => '022',
                        ],
                        [
                            'name' => '1',
                            'sans' => '033',
                            'tans' => '044',
                        ],
                    ],
                ],
            ],
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

    public function test_obj_diff(): void {
        $a = (object) ['a' => 1, 'b' => 2];
        $b = (object) ['a' => 1, 'b' => 3];
        $diff = StackQuestionLoader::obj_diff($a, $b);
        $this->assertArrayHasKey('b', $diff);
        $this->assertEquals(3, $diff['b']);
    }

    public function test_arr_diff(): void {
        $a = ['x' => 5, 'y' => 6, 'z' => (0.1 + 0.7) * 10, 'a' => [1 => 'x', 2 => 'y']];
        $b = ['x' => 5, 'y' => 7, 'z' => 8, 'a' => [1 => 'x', 2 => 'z']];
        $diff = StackQuestionLoader::arr_diff($a, $b);
        $this->assertEquals(2, count($diff));
        $this->assertArrayHasKey('y', $diff);
        $this->assertEquals(7, $diff['y']);
        $this->assertEquals(1, count($diff['a']));
        $this->assertEquals('z', $diff['a'][2]);
    }

    public function test_get_default(): void {
        set_config('stackapi', true, 'qtype_stack');
        $default = StackQuestionLoader::get_default('question', 'name', 'Fallback');
        $this->assertEquals('Default', $default);
        set_config('stackapi', false, 'qtype_stack');
        $default = StackQuestionLoader::get_default('question', 'name', 'Fallback');
        $this->assertEquals('Fallback', $default);
    }

    public function test_question_loader_default_expectedanswernote(): void {
        // An omitted expected answer note is empty, which the test reports as a construction error.
        $xml = '<quiz><question type="stack">
                    <qtest><testcase>1</testcase>
                        <testinput><name>ans1</name><value>ta1</value></testinput>
                        <expected><name>prt1</name></expected>
                    </qtest>
                    <qtest><testcase>2</testcase>
                        <testinput><name>ans1</name><value>ta1</value></testinput>
                        <expected><name>prt1</name><expectedanswernote>prt1-1-T</expectedanswernote></expected>
                    </qtest>
                </question></quiz>';
        foreach ([true, false] as $stackapi) {
            set_config('stackapi', $stackapi, 'qtype_stack');
            $testcases = StackQuestionLoader::loadXML($xml, true)['testcases'];
            $this->assertEquals([''], $testcases[0]->expectedresults['prt1']->answernotes);
            $this->assertEquals(['prt1-1-T'], $testcases[1]->expectedresults['prt1']->answernotes);
        }
    }

    public function test_detect_difference(): void {
        if (!defined('Symfony\Component\Yaml\Yaml::DUMP_COMPACT_NESTED_MAPPING')) {
            $this->markTestSkipped('Symfony YAML extension is not available.');
            return;
        }
        $xml = '<quiz><question type="stack"><name><text>Test</text></name></question></quiz>';
        $yaml = StackQuestionLoader::detect_differences($xml);
        $this->assertStringContainsString('name: Test', $yaml);
    }

    public function test_detect_difference_yml(): void {
        if (!defined('Symfony\Component\Yaml\Yaml::DUMP_COMPACT_NESTED_MAPPING')) {
            $this->markTestSkipped('Symfony YAML extension is not available.');
            return;
        }
        // Test the difference detection with a full question.
        $yaml = file_get_contents(__DIR__ . '/fixtures/questionyml.yml');
        $diff = StackQuestionLoader::detect_differences($yaml);
        $diffarray = Yaml::parse($diff);
        $this->assertEquals(11, count($diffarray));
        $expected = [
            'name' => 'Test question',
            'questiontext' => "<p>Question</p><p>[[input:ans1]] [[validation:ans1]]</p>\n    " .
            "<p>[[input:ans2]] [[validation:ans2]]</p>\n",
            'stackversion' => '2025042500',
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
                    'showvalidation' => '1',
                ],
                [
                    'name' => 'ans2',
                    'type' => 'algebraic',
                    'tans' => 'ta2',
                    'forbidfloat' => '1',
                    'requirelowestterms' => '0',
                    'checkanswertype' => '0',
                    'mustverify' => '1',
                    'showvalidation' => '1',
                ],
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
                            'quiet' => '1',
                        ],
                    ],
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
                            'falsescore' => '1',
                        ],
                    ],
                ],
            ],
            'deployedseed' => [
                1,
                2,
                3,
            ],
            'qtest' => [
                [
                    'testcase' => '1',
                    'description' => 'A test',
                    'testinput' => [
                        [
                            'name' => 'ans1',
                            'value' => 'ta1',
                        ],
                        [
                            'name' => 'ans2',
                            'value' => 'ta2',
                        ],
                    ],
                    'expected' => [
                        [
                            'name' => 'prt1',
                            'expectedscore' => '1.0000000',
                            'expectedpenalty' => '0.0000000',
                            'expectedanswernote' => '1-0-T',
                        ],
                        [
                            'name' => 'prt2',
                            'expectedscore' => '1.0000000',
                            'expectedpenalty' => '0.0000000',
                            'expectedanswernote' => '2-0-T',
                        ],
                    ],
                ],
            ],
        ];
        $expectedstring = "name: 'Test question'\nquestiontext: |\n  <p>Question</p><p>[[input:ans1]] [[validation:ans1]]</p>" .
            "\n      <p>[[input:ans2]] [[validation:ans2]]</p>\nstackversion: '2025042500'\nquestionvariables: 'ta1:1;ta2:2;'" .
            "\nquestionsimplify: '1'\nprtcorrect: '<p>" .
            "<i class=\"fa fa-check\"></i> Correct answer*, well done.</p>'\nmultiplicationsign: cross\ninput:\n  - " .
            "name: ans1\n    type: algebraic\n    tans: ta1\n    boxsize: '25'\n    forbidfloat: '1'\n    " .
            "requirelowestterms: '0'\n    checkanswertype: '0'\n    mustverify: '1'\n    showvalidation: '1'\n  - name: " .
            "ans2\n    type: algebraic\n    tans: ta2\n    forbidfloat: '1'\n    requirelowestterms: '0'\n    " .
            "checkanswertype: '0'\n    mustverify: '1'\n    showvalidation: '1'\nprt:\n  - name: prt1\n    " .
            "value: '2'\n    autosimplify: '1'\n    feedbackstyle: '1'\n    " .
            "node:\n      - name: '0'\n        answertest: AlgEquiv\n        " .
            "sans: ans1\n        tans: ta1\n        quiet: '1'\n  - name: prt2\n    " .
            "value: '1.0000001'\n    autosimplify: '1'\n    feedbackstyle: '1'\n    node:\n      - name: '0'\n        " .
            "answertest: AlgEquiv\n        sans: ans2\n        tans: ta2\n        quiet: '0'\n        falsescore: '1'\n" .
            "deployedseed:\n  - '1'\n  - '2'\n  - '3'\nqtest:\n  - testcase: '1'\n    description: 'A test'\n    " .
            "testinput:\n      - name: ans1\n        value: ta1\n      - name: ans2\n" .
            "        value: ta2\n    expected:\n      - name: prt1" .
            "\n        expectedscore: '1.0000000'\n        expectedpenalty: '0.0000000'\n" .
            "        expectedanswernote: 1-0-T\n      " .
            "- name: prt2\n        expectedscore: '1.0000000'\n        expectedpenalty:" .
            " '0.0000000'\n        expectedanswernote: 2-0-T\n";
        $this->assertStringContainsString($expectedstring, $diff);
        $this->assertEqualsCanonicalizing($expected, $diffarray);

        // Check results when using answertest summary in defaults.
        set_config('stackapi', true, 'qtype_stack');
        StackQuestionLoader::$defaults = Yaml::parseFile(__DIR__ . '/fixtures/questiondefaultssugar.yml');
        $diff = StackQuestionLoader::detect_differences($yaml);
        $diffarray = Yaml::parse($diff);
        $this->assertEquals(11, count($diffarray));
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
                    'showvalidation' => '1',
                ],
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
                                    'quiet' => '0',
                                ],
                            ],
                        ],
                ],
        ];
        $diff = StackQuestionLoader::detect_differences($blankxml);
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

        // Empty question with default grade.
        $xml = stack_api_test_data::get_question_string('emptygrade');
        $expected = [
            'name' => 'Default',
            'defaultgrade' => '2',
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
                    'showvalidation' => '1',
                ],
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
                                    'quiet' => '0',
                                ],
                            ],
                        ],
                ],
        ];
        $diff = StackQuestionLoader::detect_differences($xml);
        $diffarray = Yaml::parse($diff);
        $this->assertEquals(5, count($diffarray));
        $this->assertEqualsCanonicalizing($expected, $diffarray);

        // Check results when using answertest summary in defaults.
        set_config('stackapi', true, 'qtype_stack');
        StackQuestionLoader::$defaults = Yaml::parseFile(__DIR__ . '/fixtures/questiondefaultssugar.yml');
        $diff = StackQuestionLoader::detect_differences($xml);
        $diffarray = Yaml::parse($diff);
        $this->assertEquals(5, count($diffarray));
        $expected['prt'][0]['node'][0] = [
                    'name' => '0',
                    'answertest' => 'ATAlgEquiv(ans1,ta1)',
                    'quiet' => '0',
        ];
        $this->assertEqualsCanonicalizing($expected, $diffarray);

        // No inputs, blank specific feedback.
        $xml = stack_api_test_data::get_question_string('noinputblankspecific');
        $expected = [
            'name' => 'Default',
            'questiontext' => 'Question wording',
            'defaultgrade' => '0',
            'specificfeedback' => '',
            'questionsimplify' => '1',
            'input' => [],
            'prt' => [],
        ];
        $diff = StackQuestionLoader::detect_differences($xml);
        $diffarray = Yaml::parse($diff);
        $this->assertEquals(7, count($diffarray));
        $this->assertEqualsCanonicalizing($expected, $diffarray);
        set_config('stackapi', false, 'qtype_stack');

        // Input, blank specific feedback.
        $xml = stack_api_test_data::get_question_string('inputblankspecific');
        $expected = [
            'name' => 'Default',
            'questiontext' => 'Question wording [[input:ans1]] [[validation:ans1]]',
            'defaultgrade' => '2',
            'specificfeedback' => '',
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
                    'showvalidation' => '1',
                ],
            ],
            'prt' => [],
        ];
        $diff = StackQuestionLoader::detect_differences($xml);
        $diffarray = Yaml::parse($diff);
        $this->assertEquals(7, count($diffarray));
        $this->assertEqualsCanonicalizing($expected, $diffarray);

        // No input, no specific feedback.
        $xml = stack_api_test_data::get_question_string('noinputnospecific');
        $expected = [
            'name' => 'Default',
            'questiontext' => 'Question wording',
            'defaultgrade' => '0',
            'questionsimplify' => '1',
            'input' => [],
            'prt' => [],
        ];
        $diff = StackQuestionLoader::detect_differences($xml);
        $diffarray = Yaml::parse($diff);
        $this->assertEquals(6, count($diffarray));
        $this->assertEqualsCanonicalizing($expected, $diffarray);
        set_config('stackapi', false, 'qtype_stack');
    }

    public function test_question_loader_rejects_xml_doctype(): void {
        $xml = <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE quiz [
  <!ENTITY xxe SYSTEM "http://example.com/evil.dtd">
]>
<quiz>
  <question type="stack">
    <name><text>Unsafe</text></name>
  </question>
</quiz>
XML;

        $this->expectException(\stack_exception::class);
        $this->expectExceptionMessage('forbidden DOCTYPE');
        StackQuestionLoader::loadXML($xml);
    }

    public function test_question_loader_accepts_html_in_text_field_without_cdata_outside_test_route(): void {
        $xml = $this->get_minimal_question_xml(
            '    <prt><name>prt1</name><node><name>0</name>' .
            '<truefeedback><text><p>Correct.</p></text></truefeedback>' .
            '</node></prt>'
        );

        $question = StackQuestionLoader::loadXML($xml)['question'];

        $this->assertCount(1, $question->prts['prt1']->get_nodes_summary());
    }

    public function test_question_loader_test_route_rejects_html_in_text_field_without_cdata(): void {
        $xml = $this->get_minimal_question_xml(
            '    <prt><name>prt1</name><node><name>0</name>' .
            '<truefeedback><text><p>Correct.</p></text></truefeedback>' .
            '</node></prt>'
        );

        $this->expectException(\stack_exception::class);
        $this->expectExceptionMessage('Escape HTML or wrap it in CDATA');
        StackQuestionLoader::loadXML($xml, true, true);
    }

    public function test_question_loader_ignores_strict_moodle_compatibility_without_tests(): void {
        $xml = $this->get_minimal_question_xml(
            '<prt><name>prt1</name><node><name>0</name>' .
            '<truefeedback><text><p>Correct.</p></text></truefeedback>' .
            '</node></prt>'
        );

        $question = StackQuestionLoader::loadXML($xml, false, true)['question'];

        $this->assertCount(1, $question->prts['prt1']->get_nodes_summary());
    }

    public function test_question_loader_test_route_rejects_html_in_hint_without_cdata(): void {
        $xml = $this->get_minimal_question_xml('<hint><text><p>Try again.</p></text></hint>');

        $this->expectException(\stack_exception::class);
        $this->expectExceptionMessage('Escape HTML or wrap it in CDATA');
        StackQuestionLoader::loadXML($xml, true, true);
    }

    public function test_question_loader_test_route_rejects_html_in_legacy_hint_without_cdata(): void {
        $xml = $this->get_minimal_question_xml(
            '<hint><hintcontent><text><p>Try again.</p></text></hintcontent></hint>'
        );

        $this->expectException(\stack_exception::class);
        $this->expectExceptionMessage('Escape HTML or wrap it in CDATA');
        StackQuestionLoader::loadXML($xml, true, true);
    }

    public function test_question_loader_test_route_rejects_unknown_text_format(): void {
        $xml = $this->get_minimal_question_xml(
            '<questiontext format="unknown"><text>[[input:ans1]] [[validation:ans1]]</text></questiontext>'
        );

        $this->expectException(\stack_exception::class);
        $this->expectExceptionMessage("Unknown XML text format 'unknown'");
        StackQuestionLoader::loadXML($xml, true, true);
    }

    public function test_question_loader_trims_xml_text_fields(): void {
        $xml = $this->get_minimal_question_xml(
            '<questiontext><text>  [[input:ans1]] [[validation:ans1]]  </text></questiontext>'
        );

        $question = StackQuestionLoader::loadXML($xml)['question'];

        $this->assertEquals('[[input:ans1]] [[validation:ans1]]', $question->questiontext);
    }

    public function test_question_loader_accepts_cdata_html_in_text_field(): void {
        $xml = $this->get_minimal_question_xml(
            '    <prt><name>prt1</name><node><name>0</name>' .
            '<truefeedback><text><![CDATA[<p>Correct.</p>]]></text></truefeedback>' .
            '</node></prt>'
        );

        $question = StackQuestionLoader::loadXML($xml, true)['question'];

        $this->assertCount(1, $question->prts['prt1']->get_nodes_summary());
    }

    public static function invalid_input_placeholder_provider(): array {
        return [
            'missing validation placeholder' => ['[[input:ans1]]', '[[validation:ans1]]'],
            'duplicate input placeholder' => ['[[input:ans1]] [[input:ans1]] [[validation:ans1]]', '[[input:ans1]]'],
            'duplicate validation placeholder' => ['[[input:ans1]] [[validation:ans1]] [[validation:ans1]]', '[[validation:ans1]]'],
            'orphaned validation placeholder' => ['[[validation:ans1]]', '[[input:ans1]]'],
        ];
    }

    /**
     * @dataProvider invalid_input_placeholder_provider
     */
    public function test_question_loader_validates_input_placeholders(string $placeholders, string $message): void {
        $xml = $this->get_minimal_question_xml("<questiontext><text>{$placeholders}</text></questiontext>");

        $this->expectException(\stack_exception::class);
        $this->expectExceptionMessage($message);
        StackQuestionLoader::loadXML($xml, true);
    }

    public function test_question_loader_leaves_authoring_checks_to_the_test_route(): void {
        $xml = $this->get_minimal_question_xml(
            '<questiontext><text>[[input:ans1]]</text></questiontext><penalty>1.1</penalty>' .
            '<input><name>ans1</name><tans>ta1</tans><showvalidation>0</showvalidation><syntaxhint>' .
            str_repeat('x', 256) . '</syntaxhint></input>' .
            '<prt><name>prt1</name><node><name>0</name><answertest>Int</answertest><description>' .
            str_repeat('x', 256) . '</description><truescore>1.1</truescore>' .
            '<trueanswernote></trueanswernote><falseanswernote>note|with-reserved-character</falseanswernote>' .
            '</node></prt>'
        );

        $question = StackQuestionLoader::loadXML($xml)['question'];

        $this->assertSame(1.1, $question->penalty);
        $this->assertCount(1, $question->prts['prt1']->get_nodes_summary());
    }

    public function test_question_loader_test_route_rejects_unreferenced_input(): void {
        $xml = $this->get_minimal_question_xml(
            '<questiontext><text>[[input:ans1]] [[validation:ans1]]</text></questiontext>' .
            '<input><name>ans2</name><tans>ta2</tans></input>'
        );

        $this->expectException(\stack_exception::class);
        $this->expectExceptionMessage('Input is no longer referred to in the question text');
        StackQuestionLoader::loadXML($xml, true, true);
    }

    public function test_question_loader_test_route_rejects_unconfigured_input_placeholder(): void {
        $xml = $this->get_minimal_question_xml(
            '<questiontext><text>[[input:ans2]] [[validation:ans2]]</text></questiontext>'
        );

        $this->expectException(\stack_exception::class);
        $this->expectExceptionMessage("input 'ans2' must be set up");
        StackQuestionLoader::loadXML($xml, true, true);
    }

    public function test_question_loader_test_route_requires_visible_validation_when_verification_is_required(): void {
        $xml = $this->get_minimal_question_xml(
            '    <questiontext><text>[[input:ans1]] [[validation:ans1]]</text></questiontext>' .
            '<input><name>ans1</name><tans>ta1</tans><showvalidation>0</showvalidation></input>'
        );

        $this->expectException(\stack_exception::class);
        $this->expectExceptionMessage('cannot choose to require two step validation');
        StackQuestionLoader::loadXML($xml, true);
    }

    public function test_question_loader_allows_hidden_validation_when_verification_is_disabled(): void {
        $xml = $this->get_minimal_question_xml(
            '    <questiontext><text>[[input:ans1]] [[validation:ans1]]</text></questiontext>' .
            '<input><name>ans1</name><tans>ta1</tans><mustverify>0</mustverify>' .
            '<showvalidation>0</showvalidation></input>'
        );

        $question = StackQuestionLoader::loadXML($xml)['question'];

        $this->assertFalse($question->inputs['ans1']->get_parameter('mustVerify'));
        $this->assertSame(0, $question->inputs['ans1']->get_parameter('showValidation'));
    }

    public function test_question_loader_test_route_requires_required_answer_test_options(): void {
        $xml = $this->get_minimal_question_xml(
            '<prt><name>prt1</name><node><name>0</name><answertest>Int</answertest></node></prt>'
        );

        $this->expectException(\stack_exception::class);
        $this->expectExceptionMessage('Test options are required');
        StackQuestionLoader::loadXML($xml, true);
    }

    public function test_question_loader_test_route_rejects_long_answer_test_options(): void {
        $xml = $this->get_minimal_question_xml(
            '<prt><name>prt1</name><node><name>0</name><answertest>Int</answertest>' .
            '<testoptions>' . str_repeat('x', 256) . '</testoptions></node></prt>'
        );

        $this->expectException(\stack_exception::class);
        $this->expectExceptionMessage('may not exceed 255 characters');
        StackQuestionLoader::loadXML($xml, true);
    }

    public function test_question_loader_rejects_invalid_answer_test_options(): void {
        $xml = $this->get_minimal_question_xml(
            '<prt><name>prt1</name><node><name>0</name><answertest>Int</answertest>' .
            '<testoptions>)</testoptions></node></prt>'
        );

        $this->expectException(\stack_exception::class);
        $this->expectExceptionMessage('The test options are invalid');
        StackQuestionLoader::loadXML($xml, true);
    }


    public function test_question_loader_test_route_requires_answer_notes(): void {
        $xml = $this->get_minimal_question_xml(
            '<prt><name>prt1</name><node><name>0</name>' .
            '<trueanswernote></trueanswernote><falseanswernote>false-note</falseanswernote>' .
            '</node></prt>'
        );

        $this->expectException(\stack_exception::class);
        $this->expectExceptionMessage('Answer note must not be empty');
        StackQuestionLoader::loadXML($xml, true);
    }

    public function test_question_loader_test_route_rejects_reserved_answer_note_characters(): void {
        $xml = $this->get_minimal_question_xml(
            '<prt><name>prt1</name><node><name>0</name>' .
            '<trueanswernote>true|note</trueanswernote><falseanswernote>false-note</falseanswernote>' .
            '</node></prt>'
        );

        $this->expectException(\stack_exception::class);
        $this->expectExceptionMessage('Answer notes may not contain the character |');
        StackQuestionLoader::loadXML($xml, true);
    }

    public function test_question_loader_test_route_rejects_answer_note_separators(): void {
        $xml = $this->get_minimal_question_xml(
            '<prt><name>prt1</name><node><name>0</name>' .
            '<trueanswernote>true;note</trueanswernote><falseanswernote>false-note</falseanswernote>' .
            '</node></prt>'
        );

        $this->expectException(\stack_exception::class);
        $this->expectExceptionMessage('Answer notes may not contain ; or : characters');
        StackQuestionLoader::loadXML($xml, true);
    }

    public function test_question_loader_test_route_requires_unique_answer_notes(): void {
        $xml = $this->get_minimal_question_xml(
            '<prt><name>prt1</name><node><name>0</name>' .
            '<trueanswernote>same-note</trueanswernote><falseanswernote>same-note</falseanswernote>' .
            '</node></prt>'
        );

        $this->expectException(\stack_exception::class);
        $this->expectExceptionMessage('Duplicate answer notes detected');
        StackQuestionLoader::loadXML($xml, true);
    }

    public function test_question_loader_test_route_requires_a_penalty_between_zero_and_one(): void {
        $xml = $this->get_minimal_question_xml('<penalty>1.1</penalty>');

        $this->expectException(\stack_exception::class);
        $this->expectExceptionMessage('The penalty must be a numeric value between 0 and 1');
        StackQuestionLoader::loadXML($xml, true);
    }

    public function test_question_loader_test_route_requires_non_negative_prt_values(): void {
        $xml = $this->get_minimal_question_xml(
            '<prt><name>prt1</name><value>-1</value><node><name>0</name></node></prt>'
        );

        $this->expectException(\stack_exception::class);
        $this->expectExceptionMessage('Question value must be non-negative');
        StackQuestionLoader::loadXML($xml, true);
    }

    public function test_question_loader_test_route_requires_scores_between_zero_and_one(): void {
        $xml = $this->get_minimal_question_xml(
            '<prt><name>prt1</name><node><name>0</name><truescore>1.1</truescore></node></prt>'
        );

        $this->expectException(\stack_exception::class);
        $this->expectExceptionMessage('The score must be a numeric value between 0 and 1');
        StackQuestionLoader::loadXML($xml, true);
    }

    public function test_question_loader_test_route_requires_penalties_between_zero_and_one(): void {
        $xml = $this->get_minimal_question_xml(
            '<prt><name>prt1</name><node><name>0</name><falsepenalty>-0.1</falsepenalty></node></prt>'
        );

        $this->expectException(\stack_exception::class);
        $this->expectExceptionMessage('The penalty must empty, or be a numeric value between 0 and 1');
        StackQuestionLoader::loadXML($xml, true);
    }

    public function test_question_loader_test_route_rejects_missing_prt_node(): void {
        $xml = $this->get_minimal_question_xml(
            '<prt><name>prt1</name><node><name>0</name><truenextnode>1</truenextnode></node></prt>'
        );

        $this->expectException(\stack_exception::class);
        $this->expectExceptionMessage('True next node for PRT prt1 node 0 is invalid');
        StackQuestionLoader::loadXML($xml, true);
    }

    public function test_question_loader_test_route_requires_prt_node_name(): void {
        $xml = $this->get_minimal_question_xml(
            '<specificfeedback><text>[[feedback:prt1]]</text></specificfeedback>' .
            '<prt><name>prt1</name><node><sans>ans1</sans><tans>ta1</tans></node></prt>'
        );

        $this->expectException(\stack_exception::class);
        $this->expectExceptionMessage('Missing PRT node name in the XML');
        StackQuestionLoader::loadXML($xml, true, true);
    }

    public function test_question_loader_test_route_requires_integer_prt_node_names(): void {
        $xml = $this->get_minimal_question_xml(
            '<specificfeedback><text>[[feedback:prt1]]</text></specificfeedback>' .
            '<prt><name>prt1</name><node><name>root</name></node></prt>'
        );

        $this->expectException(\stack_exception::class);
        $this->expectExceptionMessage("PRT node name 'root' must be an integer");
        StackQuestionLoader::loadXML($xml, true, true);
    }

    public function test_question_loader_test_route_rejects_prt_cycle(): void {
        $xml = $this->get_minimal_question_xml(
            '<prt><name>prt1</name>' .
            '<node><name>0</name><truenextnode>1</truenextnode></node>' .
            '<node><name>1</name><truenextnode>1</truenextnode></node>' .
            '</prt>'
        );

        $this->expectException(\stack_exception::class);
        $this->expectExceptionMessage('This link creates a cycle in this PRT');
        StackQuestionLoader::loadXML($xml, true);
    }

    public function test_question_loader_test_route_rejects_disconnected_prt_node(): void {
        $xml = $this->get_minimal_question_xml(
            '<prt><name>prt1</name><node><name>0</name></node><node><name>1</name></node></prt>'
        );

        $this->expectException(\stack_exception::class);
        $this->expectExceptionMessage('No other nodes in the PRT link to this node');
        StackQuestionLoader::loadXML($xml, true);
    }

    public function test_question_loader_test_route_rejects_prt_without_nodes(): void {
        $xml = $this->get_minimal_question_xml('<prt><name>prt1</name></prt>');

        $this->expectException(\stack_exception::class);
        $this->expectExceptionMessage('graph of this PRT has no roots');
        StackQuestionLoader::loadXML($xml, true);
    }


    public function test_question_loader_test_route_rejects_long_syntax_hints(): void {
        $xml = $this->get_minimal_question_xml(
            '<questiontext><text>[[input:ans1]] [[validation:ans1]]</text></questiontext>' .
            '<input><name>ans1</name><tans>ta1</tans><syntaxhint>' . str_repeat('x', 256) .
            '</syntaxhint></input>'
        );

        $this->expectException(\stack_exception::class);
        $this->expectExceptionMessage('syntax hint field is limited to 255');
        StackQuestionLoader::loadXML($xml, true);
    }

    public function test_question_loader_rejects_invalid_input_parameters(): void {
        $xml = $this->get_minimal_question_xml(
            '<questiontext><text>[[input:ans1]] [[validation:ans1]]</text></questiontext>' .
            '<input><name>ans1</name><tans>ta1</tans><showvalidation>4</showvalidation></input>'
        );

        $this->expectException(\stack_exception::class);
        $this->expectExceptionMessage('Invalid parameter');
        StackQuestionLoader::loadXML($xml, true);
    }

    public function test_question_loader_test_route_rejects_invalid_input_options(): void {
        $xml = $this->get_minimal_question_xml(
            '<questiontext><text>[[input:ans1]] [[validation:ans1]]</text></questiontext>' .
            '<input><name>ans1</name><tans>ta1</tans><options>unknown</options></input>'
        );

        $this->expectException(\stack_exception::class);
        $this->expectExceptionMessage('does not support the option');
        StackQuestionLoader::loadXML($xml, true);
    }

    public function test_question_loader_test_route_rejects_long_prt_node_descriptions(): void {
        $xml = $this->get_minimal_question_xml(
            '<prt><name>prt1</name><node><name>0</name><description>' . str_repeat('x', 256) .
            '</description></node></prt>'
        );

        $this->expectException(\stack_exception::class);
        $this->expectExceptionMessage('node description is longer than 255 characters');
        StackQuestionLoader::loadXML($xml, true);
    }

    public function test_question_loader_rejects_invalid_metadata_json(): void {
        $xml = $this->get_minimal_question_xml('<metadata>{invalid}</metadata>');

        $this->expectException(\stack_exception::class);
        $this->expectExceptionMessage('Metadata is invalid JSON');
        StackQuestionLoader::loadXML($xml, true, true);
    }

    public function test_question_loader_rejects_oversized_metadata(): void {
        $xml = $this->get_minimal_question_xml('<metadata>{"value":"' . str_repeat('x', 32000) . '"}</metadata>');

        $this->expectException(\stack_exception::class);
        $this->expectExceptionMessage('There is too much metadata');
        StackQuestionLoader::loadXML($xml, true, true);
    }

    public function test_question_loader_test_route_rejects_legacy_syntax_hint_delimiters(): void {
        $xml = $this->get_minimal_question_xml(
            '<questiontext><text>[[input:ans1]] [[validation:ans1]]</text></questiontext>' .
            '<input><name>ans1</name><tans>ta1</tans><syntaxhint>$$x$$</syntaxhint></input>'
        );

        $this->expectException(\stack_exception::class);
        $this->expectExceptionMessage('are not permitted');
        StackQuestionLoader::loadXML($xml, true);
    }

    public function test_question_loader_test_route_rejects_unknown_syntax_hint_facts_tags(): void {
        $xml = $this->get_minimal_question_xml(
            '<questiontext><text>[[input:ans1]] [[validation:ans1]]</text></questiontext>' .
            '<input><name>ans1</name><tans>ta1</tans><syntaxhint>[[facts:not_real]]</syntaxhint></input>'
        );

        $this->expectException(\stack_exception::class);
        $this->expectExceptionMessage('facts tag(s) are not recognized: not_real');
        StackQuestionLoader::loadXML($xml, true);
    }

    public function test_question_loader_test_route_rejects_duplicate_prt_feedback_placeholders(): void {
        $xml = $this->get_minimal_question_xml(
            '<questiontext><text>[[feedback:prt1]] [[feedback:prt1]]</text></questiontext>' .
            '<specificfeedback><text></text></specificfeedback>' .
            '<prt><name>prt1</name><node><name>0</name></node></prt>'
        );

        $this->expectException(\stack_exception::class);
        $this->expectExceptionMessage('should only contain the token \'[[feedback:prt1]]\' once');
        StackQuestionLoader::loadXML($xml, true, true);
    }

    public function test_question_loader_test_route_rejects_feedback_placeholder_without_prt(): void {
        $xml = $this->get_minimal_question_xml(
            '<questiontext><text>[[feedback:prt2]]</text></questiontext>' .
            '<specificfeedback><text></text></specificfeedback>'
        );

        $this->expectException(\stack_exception::class);
        $this->expectExceptionMessage('This PRT must be set up before the question can be saved');
        StackQuestionLoader::loadXML($xml, true, true);
    }

    public function test_question_loader_test_route_rejects_prt_without_feedback_placeholder(): void {
        $xml = $this->get_minimal_question_xml(
            '<questiontext><text>Question wording</text></questiontext>' .
            '<specificfeedback><text></text></specificfeedback>' .
            '<prt><name>prt1</name><node><name>0</name></node></prt>'
        );

        $this->expectException(\stack_exception::class);
        $this->expectExceptionMessage('no longer referred to in the question text or specific feedback');
        StackQuestionLoader::loadXML($xml, true, true);
    }

    public function test_question_loader_test_route_rejects_long_input_names(): void {
        $inputname = str_repeat('a', 19);
        $xml = $this->get_minimal_question_xml(
            "<questiontext><text>[[input:{$inputname}]] [[validation:{$inputname}]]</text></questiontext>" .
            "<input><name>{$inputname}</name><tans>ta1</tans></input>"
        );

        $this->expectException(\stack_exception::class);
        $this->expectExceptionMessage('Input names cannot be longer than 18 characters');
        StackQuestionLoader::loadXML($xml, true, true);
    }

    public function test_question_loader_test_route_rejects_invalid_input_name_form(): void {
        $xml = $this->get_minimal_question_xml(
            '<questiontext><text>[[input:ans_]] [[validation:ans_]]</text></questiontext>' .
            '<input><name>ans_</name><tans>ta1</tans></input>'
        );

        $this->expectException(\stack_exception::class);
        $this->expectExceptionMessage("Input names must only be letters followed (optionally) by numbers. 'ans_' is illegal");
        StackQuestionLoader::loadXML($xml, true, true);
    }

    public function test_question_loader_test_route_rejects_long_prt_names(): void {
        $prtname = str_repeat('p', 19);
        $xml = $this->get_minimal_question_xml(
            "<questiontext><text>[[feedback:{$prtname}]]</text></questiontext>" .
            '<specificfeedback><text></text></specificfeedback>' .
            "<prt><name>{$prtname}</name><node><name>0</name></node></prt>"
        );

        $this->expectException(\stack_exception::class);
        $this->expectExceptionMessage('PRT names cannot be longer than 18 characters');
        StackQuestionLoader::loadXML($xml, true, true);
    }

    public function test_question_loader_test_route_rejects_whitespace_in_input_placeholder(): void {
        $xml = $this->get_minimal_question_xml('<questiontext><text>[[input: ans1]]</text></questiontext>');

        $this->expectException(\stack_exception::class);
        $this->expectExceptionMessage('Placeholders may not contain whitespace');
        StackQuestionLoader::loadXML($xml, true, true);
    }

    public function test_question_loader_test_route_rejects_whitespace_in_validation_placeholder(): void {
        $xml = $this->get_minimal_question_xml('<questiontext><text>[[ validation :ans1]]</text></questiontext>');

        $this->expectException(\stack_exception::class);
        $this->expectExceptionMessage('Placeholders may not contain whitespace');
        StackQuestionLoader::loadXML($xml, true, true);
    }

    public function test_question_loader_test_route_rejects_whitespace_in_prt_placeholder(): void {
        $xml = $this->get_minimal_question_xml('<questiontext><text>[[prt: prt1]]</text></questiontext>');

        $this->expectException(\stack_exception::class);
        $this->expectExceptionMessage('Placeholders may not contain whitespace');
        StackQuestionLoader::loadXML($xml, true, true);
    }


    public function test_question_loader_test_route_rejects_multilang_input_placeholder_mismatches(): void {
        $questiontext = '<span lang="en" class="multilang">[[input:ans1]] [[validation:ans1]]</span>' .
            '<span lang="fi" class="multilang">[[input:ans2]] [[validation:ans2]]</span>';
        $xml = $this->get_minimal_question_xml(
            '<questiontext><text><![CDATA[' . $questiontext . ']]></text></questiontext>' .
            '<input><name>ans1</name><tans>ta1</tans></input>' .
            '<input><name>ans2</name><tans>ta2</tans></input>'
        );

        $this->expectException(\stack_exception::class);
        $this->expectExceptionMessage('inconsistencies in the input and validation tags between languages');
        StackQuestionLoader::loadXML($xml, true, true);
    }

    public function test_question_loader_test_route_rejects_multilang_feedback_placeholder_mismatches(): void {
        $questiontext = '<span lang="en" class="multilang">[[feedback:prt1]]</span>' .
            '<span lang="fi" class="multilang">[[feedback:prt2]]</span>';
        $xml = $this->get_minimal_question_xml(
            '<questiontext><text><![CDATA[' . $questiontext . ']]></text></questiontext>' .
            '<specificfeedback><text></text></specificfeedback>' .
            '<prt><name>prt1</name><node><name>0</name></node></prt>' .
            '<prt><name>prt2</name><node><name>0</name></node></prt>'
        );

        $this->expectException(\stack_exception::class);
        $this->expectExceptionMessage('inconsistencies in the feedback tags between languages');
        StackQuestionLoader::loadXML($xml, true, true);
    }

    public function test_question_loader_test_route_rejects_prts_without_inputs(): void {
        $xml = $this->get_minimal_question_xml(
            '<questiontext><text>[[feedback:prt1]]</text></questiontext>' .
            '<specificfeedback><text></text></specificfeedback>' .
            '<prt><name>prt1</name><node><name>0</name></node></prt>'
        );

        $this->expectException(\stack_exception::class);
        $this->expectExceptionMessage('A question with no inputs cannot have any PRTs');
        StackQuestionLoader::loadXML($xml, true, true);
    }

    public function test_question_loader_test_route_rejects_invalid_question_variables(): void {
        $xml = $this->get_minimal_question_xml('<questionvariables><text>x:(</text></questionvariables>');

        $this->expectException(\stack_exception::class);
        StackQuestionLoader::loadXML($xml, true);
    }

    public function test_question_loader_test_route_rejects_question_variables_that_define_an_input(): void {
        $xml = $this->get_minimal_question_xml(
            '<questiontext><text>[[input:ans1]] [[validation:ans1]]</text></questiontext>' .
            '<questionvariables><text>ans1:1;</text></questionvariables>' .
            '<input><name>ans1</name><tans>ta1</tans></input>'
        );

        $this->expectException(\stack_exception::class);
        $this->expectExceptionMessage('You may not use input names as variables');
        StackQuestionLoader::loadXML($xml, true);
    }

    public static function invalid_question_text_field_provider(): array {
        return [
            'specific feedback CASText' => ['specificfeedback', '$$x$$', 'are not permitted'],
            'general feedback CASText' => ['generalfeedback', '$$x$$', 'are not permitted'],
            'question note CASText' => ['questionnote', '$$x$$', 'are not permitted'],
            'question description CASText' => ['questiondescription', '$$x$$', 'are not permitted'],
            'specific feedback input' => [
                'specificfeedback', '[[input:ans1]]', 'Specific feedback should not contain any [[input:...]] placeholders',
            ],
            'specific feedback validation' => [
                'specificfeedback',
                '[[validation:ans1]]',
                'Specific feedback should not contain any [[validation:...]] placeholders',
            ],
            'general feedback input' => [
                'generalfeedback', '[[input:ans1]]', 'should not contain any [[input:...]] placeholders',
            ],
            'general feedback validation' => [
                'generalfeedback', '[[validation:ans1]]', 'should not contain any [[validation:...]] placeholders',
            ],
            'general feedback feedback' => [
                'generalfeedback', '[[feedback:prt1]]', 'should not contain any [[feedback:...]] placeholders',
            ],
            'question note input' => [
                'questionnote', '[[input:ans1]]', 'Question note should not contain any [[input:...]] placeholders',
            ],
            'question note validation' => [
                'questionnote', '[[validation:ans1]]', 'Question note should not contain any [[validation:...]] placeholders',
            ],
            'question note feedback' => [
                'questionnote', '[[feedback:prt1]]', 'Question note should not contain any [[feedback:...]] placeholders',
            ],
            'question description feedback' => [
                'questiondescription',
                '[[feedback:prt1]]',
                'Question description should not contain any [[feedback:...]] placeholders',
            ],
        ];
    }

    /**
     * @dataProvider invalid_question_text_field_provider
     */
    public function test_question_loader_validates_question_text_fields(
        string $field,
        string $text,
        string $message
    ): void {
        $xml = $this->get_minimal_question_xml("<{$field}><text>{$text}</text></{$field}>");

        $this->expectException(\stack_exception::class);
        $this->expectExceptionMessage($message);
        StackQuestionLoader::loadXML($xml, true);
    }

    public function test_question_loader_test_route_requires_question_note_for_random_variables(): void {
        $xml = $this->get_minimal_question_xml(
            '<questionvariables><text>x:rand(1);</text></questionvariables>' .
            '<questionnote><text></text></questionnote>' .
            '<input><name>ans1</name><tans>ta1</tans></input>'
        );

        $this->expectException(\stack_exception::class);
        $this->expectExceptionMessage('question note cannot be empty when rand() appears');
        StackQuestionLoader::loadXML($xml, true, true);
    }


    public function test_question_loader_test_route_requires_input_teacher_answers(): void {
        $xml = $this->get_minimal_question_xml(
            '<questiontext><text>[[input:ans1]] [[validation:ans1]]</text></questiontext>' .
            '<input><name>ans1</name><tans></tans></input>'
        );

        $this->expectException(\stack_exception::class);
        $this->expectExceptionMessage('This must not be empty');
        StackQuestionLoader::loadXML($xml, true);
    }

    public function test_question_loader_test_route_accepts_missing_input_teacher_answers_without_strict_moodle_compatibility(): void {
        $xml = $this->get_minimal_question_xml(
            '<questiontext><text>[[input:ans1]] [[validation:ans1]]</text></questiontext>' .
            '<input><name>ans1</name></input>'
        );

        $question = StackQuestionLoader::loadXML($xml, true, false)['question'];

        $this->assertArrayHasKey('ans1', $question->inputs);
        $this->assertSame('ta1', $question->inputs['ans1']->get_teacher_answer());
    }

    public function test_question_loader_strict_moodle_compatibility_requires_input_teacher_answers(): void {
        $xml = $this->get_minimal_question_xml(
            '<questiontext><text>[[input:ans1]] [[validation:ans1]]</text></questiontext>' .
            '<input><name>ans1</name></input>'
        );

        $this->expectException(\stack_exception::class);
        $this->expectExceptionMessage('This must not be empty');
        StackQuestionLoader::loadXML($xml, true, true);
    }

    public function test_question_loader_uses_the_default_for_missing_input_teacher_answers(): void {
        $xml = $this->get_minimal_question_xml(
            '<questiontext><text>[[input:ans1]] [[validation:ans1]]</text></questiontext>' .
            '<input><name>ans1</name></input>'
        );

        $question = StackQuestionLoader::loadXML($xml)['question'];

        $this->assertSame('ta1', $question->inputs['ans1']->get_teacher_answer());
    }

    public function test_question_loader_test_route_rejects_long_input_teacher_answers(): void {
        $xml = $this->get_minimal_question_xml(
            '<questiontext><text>[[input:ans1]] [[validation:ans1]]</text></questiontext>' .
            '<input><name>ans1</name><tans>' . str_repeat('x', 256) . '</tans></input>'
        );

        $this->expectException(\stack_exception::class);
        $this->expectExceptionMessage('may not exceed 255 characters');
        StackQuestionLoader::loadXML($xml, true);
    }

    public function test_question_loader_test_route_rejects_invalid_input_teacher_answers(): void {
        $xml = $this->get_minimal_question_xml(
            '<questiontext><text>[[input:ans1]] [[validation:ans1]]</text></questiontext>' .
            '<input><name>ans1</name><tans>x:(</tans></input>'
        );

        $this->expectException(\stack_exception::class);
        StackQuestionLoader::loadXML($xml, true);
    }

    public function test_question_loader_test_route_rejects_input_teacher_answers_ending_in_semicolons(): void {
        $xml = $this->get_minimal_question_xml(
            '<questiontext><text>[[input:ans1]] [[validation:ans1]]</text></questiontext>' .
            '<input><name>ans1</name><tans>ta1;</tans></input>'
        );

        $this->expectException(\stack_exception::class);
        $this->expectExceptionMessage('must not end Maxima expressions with a semicolon');
        StackQuestionLoader::loadXML($xml, true);
    }

    public function test_question_loader_test_route_rejects_invalid_prt_feedback_variables(): void {
        $xml = $this->get_minimal_question_xml(
            '<questiontext><text>[[input:ans1]] [[validation:ans1]] [[feedback:prt1]]</text></questiontext>' .
            '<specificfeedback><text></text></specificfeedback>' .
            '<input><name>ans1</name><tans>ta1</tans></input>' .
            '<prt><name>prt1</name><feedbackvariables><text>x:(</text></feedbackvariables>' .
            '<node><name>0</name></node></prt>'
        );

        $this->expectException(\stack_exception::class);
        StackQuestionLoader::loadXML($xml, true);
    }

    public function test_question_loader_test_route_rejects_prt_feedback_variables_that_define_an_input(): void {
        $xml = $this->get_minimal_question_xml(
            '<questiontext><text>[[input:ans1]] [[validation:ans1]] [[feedback:prt1]]</text></questiontext>' .
            '<specificfeedback><text></text></specificfeedback>' .
            '<input><name>ans1</name><tans>ta1</tans></input>' .
            '<prt><name>prt1</name><feedbackvariables><text>ans1:1;</text></feedbackvariables>' .
            '<node><name>0</name></node></prt>'
        );

        $this->expectException(\stack_exception::class);
        $this->expectExceptionMessage('You may not use input names as variables');
        StackQuestionLoader::loadXML($xml, true);
    }

    public function test_question_loader_test_route_requires_prt_node_student_answers(): void {
        $xml = $this->get_minimal_question_xml(
            '<questiontext><text>[[input:ans1]] [[validation:ans1]] [[feedback:prt1]]</text></questiontext>' .
            '<specificfeedback><text></text></specificfeedback><input><name>ans1</name><tans>ta1</tans></input>' .
            '<prt><name>prt1</name><node><name>0</name><sans></sans></node></prt>'
        );

        $this->expectException(\stack_exception::class);
        $this->expectExceptionMessage('SAns must not be empty');
        StackQuestionLoader::loadXML($xml, true);
    }

    public function test_question_loader_test_route_requires_prt_node_teacher_answers(): void {
        $xml = $this->get_minimal_question_xml(
            '<questiontext><text>[[input:ans1]] [[validation:ans1]] [[feedback:prt1]]</text></questiontext>' .
            '<specificfeedback><text></text></specificfeedback><input><name>ans1</name><tans>ta1</tans></input>' .
            '<prt><name>prt1</name><node><name>0</name><tans></tans></node></prt>'
        );

        $this->expectException(\stack_exception::class);
        $this->expectExceptionMessage('TAns must not be empty');
        StackQuestionLoader::loadXML($xml, true);
    }

    public function test_question_loader_test_route_rejects_long_prt_node_answers(): void {
        $xml = $this->get_minimal_question_xml(
            '<questiontext><text>[[input:ans1]] [[validation:ans1]] [[feedback:prt1]]</text></questiontext>' .
            '<specificfeedback><text></text></specificfeedback><input><name>ans1</name><tans>ta1</tans></input>' .
            '<prt><name>prt1</name><node><name>0</name><sans>' . str_repeat('x', 256) . '</sans></node></prt>'
        );

        $this->expectException(\stack_exception::class);
        $this->expectExceptionMessage('may not exceed 255 characters');
        StackQuestionLoader::loadXML($xml, true);
    }

    public function test_question_loader_test_route_rejects_invalid_prt_node_answers(): void {
        $xml = $this->get_minimal_question_xml(
            '<questiontext><text>[[input:ans1]] [[validation:ans1]] [[feedback:prt1]]</text></questiontext>' .
            '<specificfeedback><text></text></specificfeedback><input><name>ans1</name><tans>ta1</tans></input>' .
            '<prt><name>prt1</name><node><name>0</name><tans>x:(</tans></node></prt>'
        );

        $this->expectException(\stack_exception::class);
        StackQuestionLoader::loadXML($xml, true);
    }

    public function test_question_loader_test_route_rejects_prt_node_answers_ending_in_semicolons(): void {
        $xml = $this->get_minimal_question_xml(
            '<questiontext><text>[[input:ans1]] [[validation:ans1]] [[feedback:prt1]]</text></questiontext>' .
            '<specificfeedback><text></text></specificfeedback><input><name>ans1</name><tans>ta1</tans></input>' .
            '<prt><name>prt1</name><node><name>0</name><sans>ans1;</sans></node></prt>'
        );

        $this->expectException(\stack_exception::class);
        $this->expectExceptionMessage('must not end Maxima expressions with a semicolon');
        StackQuestionLoader::loadXML($xml, true);
    }

    public function test_question_loader_test_route_rejects_invalid_true_feedback_castext(): void {
        $xml = $this->get_minimal_question_xml(
            '<questiontext><text>[[input:ans1]] [[validation:ans1]] [[feedback:prt1]]</text></questiontext>' .
            '<specificfeedback><text></text></specificfeedback><input><name>ans1</name><tans>ta1</tans></input>' .
            '<prt><name>prt1</name><node><name>0</name><truefeedback><text>$$x$$</text></truefeedback></node></prt>'
        );

        $this->expectException(\stack_exception::class);
        $this->expectExceptionMessage('are not permitted');
        StackQuestionLoader::loadXML($xml, true);
    }

    public function test_question_loader_test_route_rejects_invalid_false_feedback_castext(): void {
        $xml = $this->get_minimal_question_xml(
            '<questiontext><text>[[input:ans1]] [[validation:ans1]] [[feedback:prt1]]</text></questiontext>' .
            '<specificfeedback><text></text></specificfeedback><input><name>ans1</name><tans>ta1</tans></input>' .
            '<prt><name>prt1</name><node><name>0</name><falsefeedback><text>$$x$$</text></falsefeedback></node></prt>'
        );

        $this->expectException(\stack_exception::class);
        $this->expectExceptionMessage('are not permitted');
        StackQuestionLoader::loadXML($xml, true);
    }

    public function test_question_loader_test_route_requires_consistent_true_feedback_formats(): void {
        $xml = $this->get_minimal_question_xml(
            '<questiontext><text>[[input:ans1]] [[validation:ans1]] [[feedback:prt1]]</text></questiontext>' .
            '<specificfeedback><text></text></specificfeedback><input><name>ans1</name><tans>ta1</tans></input>' .
            '<prt><name>prt1</name>' .
            '<node><name>0</name><sans>ans1</sans><tans>ta1</tans><trueanswernote>T0</trueanswernote>' .
            '<falseanswernote>F0</falseanswernote><truenextnode>1</truenextnode>' .
            '<truefeedback format="html"><text>Correct.</text></truefeedback></node>' .
            '<node><name>1</name><sans>ans1</sans><tans>ta1</tans><trueanswernote>T1</trueanswernote>' .
            '<falseanswernote>F1</falseanswernote><truefeedback format="plain_text"><text>Correct.</text></truefeedback></node>' .
            '</prt>'
        );

        $this->expectException(\stack_exception::class);
        $this->expectExceptionMessage('All the feedback for all the nodes in a PRT must use the same text format');
        StackQuestionLoader::loadXML($xml, true, true);
    }

    public function test_question_loader_test_route_rejects_legacy_question_text_delimiters(): void {
        $xml = $this->get_minimal_question_xml('<questiontext><text>$$x$$</text></questiontext>');

        $this->expectException(\stack_exception::class);
        $this->expectExceptionMessage('are not permitted');
        StackQuestionLoader::loadXML($xml, true);
    }

    public function test_question_loader_test_route_rejects_unknown_question_text_facts_tags(): void {
        $xml = $this->get_minimal_question_xml('<questiontext><text>[[facts:not_real]]</text></questiontext>');

        $this->expectException(\stack_exception::class);
        $this->expectExceptionMessage('facts tag(s) are not recognized: not_real');
        StackQuestionLoader::loadXML($xml, true);
    }

    public function test_question_loader_test_route_rejects_invalid_hint_castext(): void {
        $xml = $this->get_minimal_question_xml('<hint><text>$$x$$</text></hint>');

        $this->expectException(\stack_exception::class);
        $this->expectExceptionMessage('are not permitted');
        StackQuestionLoader::loadXML($xml, true);
    }

    public function test_question_loader_test_route_rejects_duplicate_testcase_numbers(): void {
        $xml = $this->get_minimal_question_xml(
            '<qtest><testcase>1</testcase></qtest>' .
            '<qtest><testcase>1</testcase></qtest>'
        );

        $this->expectException(\stack_exception::class);
        $this->expectExceptionMessage('Multiple question tests have the number 1');
        StackQuestionLoader::loadXML($xml, true);
    }

    public function test_question_loader_test_route_requires_testcase_number(): void {
        $xml = $this->get_minimal_question_xml('<qtest><description>A test</description></qtest>');

        $this->expectException(\stack_exception::class);
        $this->expectExceptionMessage('Missing testcase number in the XML');
        StackQuestionLoader::loadXML($xml, true, true);
    }

    public function test_question_loader_test_route_rejects_invalid_legacy_hint_castext(): void {
        $xml = $this->get_minimal_question_xml('<hint><hintcontent><text>$$x$$</text></hintcontent></hint>');

        $this->expectException(\stack_exception::class);
        $this->expectExceptionMessage('are not permitted');
        StackQuestionLoader::loadXML($xml, true);
    }

    public function test_question_loader_test_route_rejects_duplicate_input_definitions(): void {
        $xml = $this->get_minimal_question_xml(
            '<questiontext><text>[[input:ans1]] [[validation:ans1]]</text></questiontext>' .
            '<input><name>ans1</name><tans>ta1</tans></input>' .
            '<input><name>ans1</name><tans>ta2</tans></input>'
        );

        $this->expectException(\stack_exception::class);
        $this->expectExceptionMessage('Multiple inputs have the same name: ans1');
        StackQuestionLoader::loadXML($xml, true);
    }

    public function test_question_loader_test_route_rejects_duplicate_prt_definitions(): void {
        $xml = $this->get_minimal_question_xml(
            '<prt><name>prt1</name><node><name>0</name></node></prt>' .
            '<prt><name>prt1</name><node><name>0</name></node></prt>'
        );

        $this->expectException(\stack_exception::class);
        $this->expectExceptionMessage('Multiple prts have the same name: prt1');
        StackQuestionLoader::loadXML($xml, true);
    }

    public function test_question_loader_test_route_rejects_duplicate_node_definitions(): void {
        $xml = $this->get_minimal_question_xml(
            '<prt><name>prt1</name>' .
            '<node><name>0</name></node><node><name>0</name></node>' .
            '</prt>'
        );

        $this->expectException(\stack_exception::class);
        $this->expectExceptionMessage('Multiple nodes have the name: 0 in PRT: prt1');
        StackQuestionLoader::loadXML($xml, true);
    }

    public function test_question_loader_rejects_oversized_yaml(): void {
        $yaml = "name: Oversized\nquestiontext: |\n  " . str_repeat('a', 5242881);

        $this->expectException(\stack_exception::class);
        $this->expectExceptionMessage('maximum allowed size');
        StackQuestionLoader::loadXML($yaml);
    }

    public function test_question_loader_rejects_excessive_node_count(): void {
        $xml = $this->get_minimal_question_xml(str_repeat("<deployedseed>1</deployedseed>\n", 50001));

        $this->expectException(\stack_exception::class);
        $this->expectExceptionMessage('maximum allowed node count');
        StackQuestionLoader::loadXML($xml);
    }

    public function test_question_loader_marks_non_stack_question_exception(): void {
        $xml = stack_api_test_data::get_question_string('nonstack');

        try {
            StackQuestionLoader::loadXML($xml);
        } catch (\stack_exception $e) {
            $this->assertEquals(get_string('api_not_stack_question', 'qtype_stack'), $e->getMessage());
            $this->assertEquals(StackQuestionLoader::NON_STACK_Q, $e->debuginfo);
            return;
        }

        $this->fail('Expected a stack_exception for a non-STACK question.');
    }

    public function test_question_loader_marks_multiple_question_exceptions(): void {
        $xml = stack_api_test_data::get_question_string('multiplequestions');

        try {
            StackQuestionLoader::loadXML($xml);
        } catch (\stack_exception $e) {
            $this->assertEquals(get_string('api_invalid_question_count', 'qtype_stack'), $e->getMessage());
            $this->assertEquals(StackQuestionLoader::MULTIPLE_Q, $e->debuginfo);
            return;
        }

        $this->fail('Expected a stack_exception for multiple questions.');
    }

    public function test_question_loader_marks_multiple_question_exception_with_category(): void {
        $xml = stack_api_test_data::get_question_string('multiplequestionscategory');

        try {
            StackQuestionLoader::loadXML($xml);
        } catch (\stack_exception $e) {
            $this->assertEquals(get_string('api_invalid_question_count_category', 'qtype_stack'), $e->getMessage());
            $this->assertEquals(StackQuestionLoader::MULTIPLE_Q_CATEGORY, $e->debuginfo);
            return;
        }

        $this->fail('Expected a stack_exception for multiple questions with category data.');
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

    public function test_question_loader_default_emptygrade(): void {
        $xml = stack_api_test_data::get_question_string('emptygrade');
        $question = StackQuestionLoader::loadXML($xml)['question'];
        $this->assertEquals(
            '<p>Default question</p><p>[[input:ans1]] [[validation:ans1]]</p>',
            $question->questiontext
        );
        $this->assertEquals(2, $question->defaultmark);
        $this->assertEquals(
            '[[feedback:prt1]]',
            $question->specificfeedback
        );

        $this->assertEquals(1, count($question->prts));
        $nodesummary = $question->prts['prt1']->get_nodes_summary()[0];
        $this->assertEquals('ATAlgEquiv(ans1,ta1)', $nodesummary->answertest);
        $this->assertEquals(1, count($question->inputs));
        $this->assertEquals(
            $question->inputs['ans1']->get_parameter('showValidation'),
            get_config('qtype_stack', 'inputshowvalidation')
        );
    }

    public function test_question_loader_default_noinputblankspecific(): void {
        $xml = stack_api_test_data::get_question_string('noinputblankspecific');
        $question = StackQuestionLoader::loadXML($xml)['question'];
        $this->assertEquals(
            'Question wording',
            $question->questiontext
        );
        $this->assertEquals(0, $question->defaultmark);
        $this->assertEquals(
            '',
            $question->specificfeedback
        );

        $this->assertEquals(0, count($question->prts));
        $this->assertEquals(0, count($question->inputs));
    }

    public function test_question_loader_default_inputblankspecific(): void {
        $xml = stack_api_test_data::get_question_string('inputblankspecific');
        $question = StackQuestionLoader::loadXML($xml)['question'];
        $this->assertEquals(
            'Question wording [[input:ans1]] [[validation:ans1]]',
            $question->questiontext
        );
        $this->assertEquals(2, $question->defaultmark);
        $this->assertEquals(
            '',
            $question->specificfeedback
        );

        $this->assertEquals(0, count($question->prts));
        $this->assertEquals(1, count($question->inputs));
        $this->assertEquals(
            $question->inputs['ans1']->get_parameter('showValidation'),
            get_config('qtype_stack', 'inputshowvalidation')
        );
    }

    public function test_question_loader_default_noinputnospecific(): void {
        $xml = stack_api_test_data::get_question_string('noinputnospecific');
        $question = StackQuestionLoader::loadXML($xml)['question'];
        $this->assertEquals(
            'Question wording',
            $question->questiontext
        );
        $this->assertEquals(0, $question->defaultmark);
        $this->assertEquals(
            '',
            $question->specificfeedback
        );

        $this->assertEquals(0, count($question->prts));
        $this->assertEquals(0, count($question->inputs));
    }

    /**
     * A helper function returning minimal STACK XML.
     *
     * @param string $extra XML to insert inside the question element.
     * @return string
     */
    private function get_minimal_question_xml(string $extra = ''): string {
        return "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n" .
            "<quiz>\n" .
            "  <question type=\"stack\">\n" .
            "    <name><text>Test</text></name>\n" .
            $extra .
            "  </question>\n" .
            "</quiz>\n";
    }
}

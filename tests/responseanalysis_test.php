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

namespace qtype_stack;
use qtype_stack_testcase;
use stack_question_report;
use test_question_maker;

defined('MOODLE_INTERNAL') || die();
require_once(__DIR__ . '/../stack/questionreport.class.php');
require_once(__DIR__ . '/fixtures/test_base.php');
global $CFG;
require_once($CFG->dirroot . '/mod/quiz/tests/quiz_question_helper_test_trait.php');

define ('RESPONSETS', '# = 0 | thing1_true | prt1-1-T');
define ('RESPONSEFS1', '# = 0 | thing1_yuck | prt1-1-F');
define ('RESPONSETFS', '# = 0 | thing1_true | prt1-1-T | thing2_yuck. | prt1-2-F');
define ('RESPONSETTS', '# = 1 | thing1_true | prt1-1-T | thing2_true. | prt1-2-T');
define ('RESPONSEFS2', '# = 0 | thing1_ew | prt1-1-F');
define ('RESPONSE3F', 'Seed: 333333333; ans1: 22 [score]; PotResTree_1: ' . RESPONSEFS1);
define ('RESPONSE3TF', 'Seed: 333333333; ans1: 103 [score]; PotResTree_1: ' . RESPONSETFS);
define ('RESPONSE3TT', 'Seed: 333333333; ans1: x+3 [score]; PotResTree_1: ' . RESPONSETTS);
define ('RESPONSE1F', 'Seed: 123456789; ans1: 45 [score]; PotResTree_1: ' . RESPONSEFS2);
define ('RESPONSE5F', 'Seed: 555555555; ans1: 78 [score]; PotResTree_1: ' . RESPONSEFS2);

define ('MULTRESPONSE3TTFT', 'Seed: 333333333; ans1: 11 [score]; ans2: 22 [score]; ans3: 33 [score]; ans4: 44 [score];' .
        ' odd: ' . RESPONSETS . '; even: ' . RESPONSETS . '; oddeven: ' . RESPONSETS . '; unique: ' . RESPONSETS);
define ('MULTRESPONSE1TNNN', 'Seed: 123456789; ans1: 11 [score]; ans2: vv [valid]; ans3: ii [invalid]; ans4: zz;' .
        ' odd: ' . RESPONSETS . '; even: !; oddeven: !; unique: !');
/**
 * Unit tests for the response analysis report.
 *
 * @package    qtype_stack
 * @copyright 2023 The University of Edinburgh.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 * @group qtype_stack
 * @covers \stack_question_report
 */
final class responseanalysis_test extends qtype_stack_testcase {
    use \quiz_question_helper_test_trait;
    // phpcs:ignore moodle.Commenting.VariableComment.Missing
    public $report;
    // phpcs:ignore moodle.Commenting.VariableComment.Missing
    public $sqlsummary = '{
        "974": {
            "id": "974",
            "variant": "3",
            "responsesummary": "' . RESPONSE3F . '"
        },
        "975": {
            "id": "975",
            "variant": "1",
            "responsesummary": "' . RESPONSE1F . '"
        },
        "988": {
            "id": "988",
            "variant": "5",
            "responsesummary": "' . RESPONSE5F . '"
        },
        "989": {
            "id": "989",
            "variant": "3",
            "responsesummary": "' . RESPONSE3TF . '"
        },
        "990": {
            "id": "990",
            "variant": "3",
            "responsesummary": "' . RESPONSE3TT . '"
        },
        "995": {
            "id": "990",
            "variant": "3",
            "responsesummary": "' . RESPONSE3TT . '"
        }
    }';
    // phpcs:ignore moodle.Commenting.VariableComment.Missing
    public $notes = [
        1 => 'Variant One',
        3 => 'Variant Three',
        5 => 'Variant Five',
    ];
    // phpcs:ignore moodle.Commenting.VariableComment.Missing
    public $seeds = [
        1 => 123456789,
        3 => 333333333,
        5 => 555555555,
    ];
    // phpcs:ignore moodle.Commenting.VariableComment.Missing
    public $summary = [
        3 => [RESPONSE3F => 1, RESPONSE3TF => 1, RESPONSE3TT => 2],
        1 => [RESPONSE1F => 1],
        5 => [RESPONSE5F => 1],
    ];
    // phpcs:ignore moodle.Commenting.VariableComment.Missing
    public $inputreport = [
        "3" => [
            "ans1" => [
                "score" => [
                    "22" => 1,
                    "103" => 1,
                    "x+3" => 2,
                ],
                "valid" => [],
                "invalid" => [],
                "other" => [],
            ],
        ],
        "1" => [
            "ans1" => [
                "score" => [
                    "45" => 1,
                ],
                "valid" => [],
                "invalid" => [],
                "other" => [],
            ],
        ],
        "5" => [
            "ans1" => [
                "score" => [
                    "78" => 1,
                ],
                "valid" => [],
                "invalid" => [],
                "other" => [],
            ],
        ],
    ];
    // phpcs:ignore moodle.Commenting.VariableComment.Missing
    public $inputreportsummary = [
        "ans1" => [
            "score" => [
                "x+3" => 2,
                "22" => 1,
                "103" => 1,
                "45" => 1,
                "78" => 1,
            ],
            "valid" => [],
            "invalid" => [],
            "other" => [],
        ],
    ];
    // phpcs:ignore moodle.Commenting.VariableComment.Missing
    public $prtreport = [
        "3" => [
            "PotResTree_1" => [
                RESPONSEFS1 => 1,
                RESPONSETFS => 1,
                RESPONSETTS => 2,
            ],
        ],
        "1" => [
            "PotResTree_1" => [
                RESPONSEFS2 => 1,
            ],
        ],
        "5" => [
            "PotResTree_1" => [
                RESPONSEFS2 => 1,
            ],
        ],
    ];
    // phpcs:ignore moodle.Commenting.VariableComment.Missing
    public $prtreportinputs = [
        "3" => [
            "PotResTree_1" => [
                RESPONSEFS1 => [
                    "ans1:22; " => 1,
                ],
                RESPONSETFS => [
                    "ans1:103; " => 1,
                ],
                RESPONSETTS => [
                    "ans1:x+3; " => 2,
                ],
            ],
        ],
        "1" => [
            "PotResTree_1" => [
                RESPONSEFS2 => [
                    "ans1:45; " => 1,
                ],
            ],
        ],
        "5" => [
            "PotResTree_1" => [
                RESPONSEFS2 => [
                    "ans1:78; " => 1,
                ],
            ],
        ],
    ];
    // phpcs:ignore moodle.Commenting.VariableComment.Missing
    public $prtreportsummary = [
        "PotResTree_1" => [
            RESPONSEFS1 => 1,
            RESPONSETFS => 1,
            RESPONSETTS => 2,
            RESPONSEFS2 => 2,
        ],
    ];

    // phpcs:ignore moodle.Commenting.VariableComment.Missing
    public $sqlsummarymult = '{
        "974": {
            "id": "974",
            "variant": "3",
            "responsesummary": "' . MULTRESPONSE3TTFT . '"
        },
        "975": {
            "id": "975",
            "variant": "1",
            "responsesummary": "' . MULTRESPONSE1TNNN . '"
        }
    }';

    // phpcs:ignore moodle.Commenting.VariableComment.Missing
    public $summarymult = [
        3 => [MULTRESPONSE3TTFT => 1],
        1 => [MULTRESPONSE1TNNN => 1],
    ];

    // phpcs:ignore moodle.Commenting.VariableComment.Missing
    public $inputreportmult = [
        "3" => [
            "ans1" => [
                "score" => [
                    "11" => 1,
                ],
                "valid" => [],
                "invalid" => [],
                "other" => [],
            ],
            "ans2" => [
                "score" => [
                    "22" => 1,
                ],
                "valid" => [],
                "invalid" => [],
                "other" => [],
            ],
            "ans3" => [
                "score" => [
                    "33" => 1,
                ],
                "valid" => [],
                "invalid" => [],
                "other" => [],
            ],
            "ans4" => [
                "score" => [
                    "44" => 1,
                ],
                "valid" => [],
                "invalid" => [],
                "other" => [],
            ],
        ],
        "1" => [
            "ans1" => [
                "score" => [
                    "11" => 1,
                ],
                "valid" => [],
                "invalid" => [],
                "other" => [],
            ],
            "ans2" => [
                "score" => [],
                "valid" => [
                    "vv" => 1,
                ],
                "invalid" => [],
                "other" => [],
            ],
            "ans3" => [
                "score" => [],
                "valid" => [],
                "invalid" => [
                    "ii" => 1,
                ],
                "other" => [],
            ],
            "ans4" => [
                "score" => [],
                "valid" => [],
                "invalid" => [],
                "other" => [
                    "zz" => 1,
                ],
            ],
        ],
    ];
    // phpcs:ignore moodle.Commenting.VariableComment.Missing
    public $inputreportsummarymult = [
        "ans1" => [
            "score" => [
                "11" => 2,
            ],
            "valid" => [],
            "invalid" => [],
            "other" => [],
        ],
        "ans2" => [
            "score" => [
                "22" => 1,
            ],
            "valid" => [
                "vv" => 1,
            ],
            "invalid" => [],
            "other" => [],
        ],
        "ans3" => [
            "score" => [
                "33" => 1,
            ],
            "valid" => [],
            "invalid" => [
                "ii" => 1,
            ],
            "other" => [],
        ],
        "ans4" => [
            "score" => [
                "44" => 1,
            ],
            "valid" => [],
            "invalid" => [],
            "other" => [
                "zz" => 1,
            ],
        ],
    ];
    // phpcs:ignore moodle.Commenting.VariableComment.Missing
    public $prtreportmult = [
        "3" => [
            "odd" => [
                RESPONSETS => 1,
            ],
            "even" => [
                RESPONSETS => 1,
            ],
            "oddeven" => [
                RESPONSETS => 1,
            ],
            "unique" => [
                RESPONSETS => 1,
            ],
        ],
        "1" => [
            "odd" => [
                RESPONSETS => 1,
            ],
            "even" => [
                "!" => 1,
            ],
            "oddeven" => [
                "!" => 1,
            ],
            "unique" => [
                "!" => 1,
            ],
        ],
    ];
    // phpcs:ignore moodle.Commenting.VariableComment.Missing
    public $prtreportinputsmult = [
        "3" => [
            "odd" => [
                RESPONSETS => [
                    "ans1:11; " => 1,
                ],
            ],
            "even" => [
                RESPONSETS => [
                    "ans2:22; " => 1,
                ],
            ],
            "oddeven" => [
                RESPONSETS => [
                    "ans3:33; " => 1,
                ],
            ],
            "unique" => [
                RESPONSETS => [
                    "ans4:44; " => 1,
                ],
            ],
        ],
        "1" => [
            "odd" => [
                RESPONSETS => [
                    "ans1:11; " => 1,
                ],
            ],
            "even" => [
                "!" => [
                    "ans2:vv; " => 1,
                ],
            ],
            "oddeven" => [
                "!" => [
                    "ans3:ii; " => 1,
                ],
            ],
            "unique" => [
                "!" => [
                    "ans4:zz; " => 1,
                ],
            ],
        ],
    ];
    // phpcs:ignore moodle.Commenting.VariableComment.Missing
    public $prtreportsummarymult = [
        "odd" => [
            RESPONSETS => 2,
        ],
        "even" => [
            RESPONSETS => 1,
            "!" => 1,
        ],
        "oddeven" => [
            RESPONSETS => 1,
            "!" => 1,
        ],
        "unique" => [
            RESPONSETS => 1,
            "!" => 1,
        ],
    ];
    // phpcs:ignore moodle.Commenting.VariableComment.Missing
    public static $question;
    // phpcs:ignore moodle.Commenting.VariableComment.Missing
    public static $question2;

    // phpcs:ignore moodle.Commenting.MissingDocblock.Function
    public static function setUpBeforeClass(): void {
        parent::setUpBeforeClass();
        self::$question = test_question_maker::make_question('stack', 'test1');
        self::$question2 = test_question_maker::make_question('stack', 'test3');
    }

    // phpcs:ignore moodle.Commenting.MissingDocblock.MissingTestcaseMethodDescription
    public function set_question(): void {
        $this->report = $this->getMockBuilder(stack_question_report::class)
            ->onlyMethods(['run_report'])
            ->setConstructorArgs([self::$question, 2, 1])->getMock();
        $this->report->summary = $this->summary;
        $this->report->questionnotes = $this->notes;
        $this->report->questionseeds = $this->seeds;
        $this->report->collate();
        $this->report->reports_sort();
    }

    // phpcs:ignore moodle.Commenting.MissingDocblock.MissingTestcaseMethodDescription
    public function set_question_mult(): void {
        $this->report = $this->getMockBuilder(stack_question_report::class)
            ->onlyMethods(['run_report'])
            ->setConstructorArgs([self::$question2, 2, 1])->getMock();
        $this->report->summary = $this->summarymult;
        $this->report->questionnotes = $this->notes;
        $this->report->questionseeds = $this->seeds;
        $this->report->collate();
        $this->report->reports_sort();
    }

    public function test_create_summary(): void {

        $this->report = $this->getMockBuilder(stack_question_report::class)
            ->onlyMethods(['load_summary_data', 'run_report'])
            ->setConstructorArgs([self::$question, 2, 1])->getMock();
        $this->report->expects($this->any())
            ->method("load_summary_data")
            ->willReturn((array)json_decode($this->sqlsummary));
        $this->report->create_summary();
        $this->assertEquals($this->summary, $this->report->summary);
    }

    public function test_collate(): void {

        $this->report = $this->getMockBuilder(stack_question_report::class)
            ->onlyMethods(['run_report'])
            ->setConstructorArgs([self::$question, 2, 1])->getMock();
        $this->report->summary = $this->summary;
        $this->report->questionnotes = $this->notes;
        $this->report->questionseeds = $this->seeds;
        $this->report->collate();
        $this->assertEquals($this->inputreport, $this->report->inputreport);
        $this->assertEquals($this->inputreportsummary, $this->report->inputreportsummary);
        $this->assertEquals($this->prtreport, $this->report->prtreport);
        $this->assertEquals($this->prtreportinputs, $this->report->prtreportinputs);
        $this->assertEquals($this->prtreportsummary, $this->report->prtreportsummary);
    }

    public function test_format_summary(): void {

        $this->set_question();
        $summary = $this->report->format_summary();
        $expected = "## PotResTree_1 (6)\n2 ( 33.33%); " . RESPONSEFS2 . "\n1 ( 16.67%); " . RESPONSETFS .
            "\n1 ( 16.67%); " . RESPONSEFS1 . "\n2 ( 33.33%); " . RESPONSETTS;
        $this->assertEquals($expected, $summary->prts[0]->sumout);
        $this->assertEquals(6, $summary->tot['PotResTree_1']);
    }

    public function test_note_summary(): void {

        $this->set_question();
        $summary = $this->report->format_summary();
        $notesummary = $this->report->format_notesummary($summary->tot);
        $expected = "## PotResTree_1 (6)\n4 ( 66.67%); # = 0\n2 ( 33.33%); # = 1\n3 ( 50.00%); prt1-1-F\n3 ( 50.00%); " .
            "prt1-1-T\n1 ( 16.67%); prt1-2-F\n2 ( 33.33%); prt1-2-T\n2 ( 33.33%); thing1_ew\n3 ( 50.00%); " .
            "thing1_true\n1 ( 16.67%); thing1_yuck\n2 ( 33.33%); thing2_true.\n1 ( 16.67%); thing2_yuck.\n\n";
        $this->assertEquals('PotResTree_1', $notesummary->prts[0]->prtname);
        $this->assertEquals($expected, $notesummary->prts[0]->sumout);
    }

    public function test_variants_summary(): void {

        $this->set_question();
        $variants = $this->report->format_variants()->variants;
        $expectedsum3 = "## PotResTree_1 (4)\n2 ( 50.00%); " . RESPONSETTS . "\n2 ( 50.00%); ans1:x+3; \n\n1 ( 25.00%); " .
            RESPONSEFS1 . "\n1 ( 25.00%); ans1:22; \n\n1 ( 25.00%); " . RESPONSETFS . "\n1 ( 25.00%); ans1:103; \n\n";
        $expectedsum1 = "## PotResTree_1 (1)\n1 (100.00%); " . RESPONSEFS2 . "\n1 (100.00%); ans1:45; \n\n";
        $expectedsum5 = "## PotResTree_1 (1)\n1 (100.00%); " . RESPONSEFS2 . "\n1 (100.00%); ans1:78; \n\n";
        $expectedans3 = "## ans1 (4)\n### score\n2 ( 50.00%); x+3\n1 ( 25.00%); 22\n1 ( 25.00%); 103\n\n";
        $expectedans1 = "## ans1 (1)\n### score\n1 (100.00%); 45\n\n";
        $expectedans5 = "## ans1 (1)\n### score\n1 (100.00%); 78\n\n";
        $this->assertEquals(333333333, $variants[0]->seed);
        $this->assertEquals($expectedsum3, $variants[0]->notessumout->sumout);
        $this->assertEquals($expectedans3, $variants[0]->anssumout);
        $this->assertEquals(123456789, $variants[1]->seed);
        $this->assertEquals($expectedsum1, $variants[1]->notessumout->sumout);
        $this->assertEquals($expectedans1, $variants[1]->anssumout);
        $this->assertEquals(555555555, $variants[2]->seed);
        $this->assertEquals($expectedsum5, $variants[2]->notessumout->sumout);
        $this->assertEquals($expectedans5, $variants[2]->anssumout);
    }

    public function test_inputs_summary(): void {

        $this->set_question();
        $inputs = $this->report->format_inputs()->inputs;
        $expected = "## ans1 (6)\n### score\n2 ( 33.33%); x+3\n1 ( 16.67%); " .
            "22\n1 ( 16.67%); 103\n1 ( 16.67%); 45\n1 ( 16.67%); 78\n\n";
        $this->assertEquals($expected, $inputs);
    }

    public function test_raw_data(): void {

        $this->set_question();
        $rawdata = $this->report->format_raw_data()->rawdata;
        $expected = "\n# 3 (4)\n1 ( 25.00%); Seed: 333333333; ans1: 22 [score]; PotResTree_1: " .
            RESPONSEFS1 . "\n1 ( 25.00%); Seed: 333333333; ans1: 103 [score]; PotResTree_1: " . RESPONSETFS .
            "\n2 ( 50.00%); Seed: 333333333; ans1: x+3 [score]; PotResTree_1: " . RESPONSETTS . "\n\n# 1 (1)\n1 (100.00%); " .
            "Seed: 123456789; ans1: 45 [score]; PotResTree_1: " . RESPONSEFS2 . "\n\n# 5 (1)\n1 (100.00%); " .
            "Seed: 555555555; ans1: 78 [score]; PotResTree_1: " . RESPONSEFS2 . "\n";
        $this->assertEquals($expected, $rawdata);
    }

    public function test_create_summary_multiple(): void {

        $this->report = $this->getMockBuilder(stack_question_report::class)
            ->onlyMethods(['load_summary_data', 'run_report'])
            ->setConstructorArgs([self::$question2, 2, 1])->getMock();
        $this->report->expects($this->any())
            ->method("load_summary_data")
            ->willReturn((array)json_decode($this->sqlsummarymult));
        $this->report->create_summary();
        $this->assertEquals($this->summarymult, $this->report->summary);
    }

    public function test_collate_multiple(): void {

        $this->report = $this->getMockBuilder(stack_question_report::class)
            ->onlyMethods(['run_report'])
            ->setConstructorArgs([self::$question2, 2, 1])->getMock();
        $this->report->summary = $this->summarymult;
        $this->report->questionnotes = $this->notes;
        $this->report->questionseeds = $this->seeds;
        $this->report->collate();
        $this->assertEquals($this->inputreportmult, $this->report->inputreport);
        $this->assertEquals($this->inputreportsummarymult, $this->report->inputreportsummary);
        $this->assertEquals($this->prtreportmult, $this->report->prtreport);
        $this->assertEquals($this->prtreportinputsmult, $this->report->prtreportinputs);
        $this->assertEquals($this->prtreportsummarymult, $this->report->prtreportsummary);
    }

    public function test_format_summary_multiple(): void {

        $this->set_question_mult();
        $summary = $this->report->format_summary();
        $expected1 = "## odd (2)\n2 (100.00%); " . RESPONSETS;
        $expected2 = "## even (2)\n1 ( 50.00%); !\n1 ( 50.00%); " . RESPONSETS;
        $expected3 = "## oddeven (2)\n1 ( 50.00%); !\n1 ( 50.00%); " . RESPONSETS;
        $expected4 = "## unique (2)\n1 ( 50.00%); !\n1 ( 50.00%); " . RESPONSETS;
        $this->assertEquals('odd', $summary->prts[0]->prtname);
        $this->assertEquals($expected1, $summary->prts[0]->sumout);
        $this->assertEquals('even', $summary->prts[1]->prtname);
        $this->assertEquals($expected2, $summary->prts[1]->sumout);
        $this->assertEquals('oddeven', $summary->prts[2]->prtname);
        $this->assertEquals($expected3, $summary->prts[2]->sumout);
        $this->assertEquals('unique', $summary->prts[3]->prtname);
        $this->assertEquals($expected4, $summary->prts[3]->sumout);
        $this->assertEquals(2, $summary->tot['odd']);
        $this->assertEquals(2, $summary->tot['even']);
        $this->assertEquals(2, $summary->tot['oddeven']);
        $this->assertEquals(2, $summary->tot['unique']);
    }

    public function test_note_summary_multiple(): void {

        $this->set_question_mult();
        $summary = $this->report->format_summary();
        $notesummary = $this->report->format_notesummary($summary->tot);
        $expected1 = "## odd (2)\n2 (100.00%); # = 0\n2 (100.00%); prt1-1-T\n2 (100.00%); thing1_true\n\n";
        $expected2 = "## even (2)\n1 ( 50.00%); !\n1 ( 50.00%); # = 0\n1 ( 50.00%); prt1-1-T\n1 ( 50.00%); thing1_true\n\n";
        $expected3 = "## oddeven (2)\n1 ( 50.00%); !\n1 ( 50.00%); # = 0\n1 ( 50.00%); prt1-1-T\n1 ( 50.00%); thing1_true\n\n";
        $expected4 = "## unique (2)\n1 ( 50.00%); !\n1 ( 50.00%); # = 0\n1 ( 50.00%); prt1-1-T\n1 ( 50.00%); thing1_true\n\n";
        $this->assertEquals('odd', $summary->prts[0]->prtname);
        $this->assertEquals($expected1, $notesummary->prts[0]->sumout);
        $this->assertEquals('even', $notesummary->prts[1]->prtname);
        $this->assertEquals($expected2, $notesummary->prts[1]->sumout);
        $this->assertEquals('oddeven', $notesummary->prts[2]->prtname);
        $this->assertEquals($expected3, $notesummary->prts[2]->sumout);
        $this->assertEquals('unique', $notesummary->prts[3]->prtname);
        $this->assertEquals($expected4, $notesummary->prts[3]->sumout);
    }

    public function test_variants_summary_multiple(): void {

        $this->set_question_mult();
        $variants = $this->report->format_variants()->variants;
        $expectedsum3 = "## odd (1)\n1 (100.00%); " . RESPONSETS . "\n1 (100.00%); ans1:11; \n\n## even (1)\n1 (100.00%); " .
            RESPONSETS . "\n1 (100.00%); ans2:22; \n\n## oddeven (1)\n1 (100.00%); " . RESPONSETS .
            "\n1 (100.00%); ans3:33; \n\n## unique (1)\n1 (100.00%); " . RESPONSETS . "\n1 (100.00%); ans4:44; \n\n";
        $expectedsum1 = "## odd (1)\n1 (100.00%); " . RESPONSETS . "\n1 (100.00%); ans1:11; \n\n## even (1)\n1 (100.00%); " .
            "!\n1 (100.00%); ans2:vv; \n\n## oddeven (1)\n1 (100.00%); !\n1 (100.00%); ans3:ii; \n\n## unique (1)\n1 (100.00%); " .
            "!\n1 (100.00%); ans4:zz; \n\n";
        $expectedans3 = "## ans1 (1)\n### score\n1 (100.00%); 11\n\n## ans2 (1)\n### score\n1 (100.00%); " .
            "22\n\n## ans3 (1)\n### score\n1 (100.00%); 33\n\n## ans4 (1)\n### score\n1 (100.00%); 44\n\n";
        $expectedans1 = "## ans1 (1)\n### score\n1 (100.00%); 11\n\n## ans2 (1)\n### valid\n1 (100.00%); " .
            "vv\n\n## ans3 (1)\n### invalid\n1 (100.00%); ii\n\n## ans4 (1)\n### other\n1 (100.00%); zz\n\n";
        $this->assertEquals(333333333, $variants[0]->seed);
        $this->assertEquals($expectedsum3, $variants[0]->notessumout->sumout);
        $this->assertEquals($expectedans3, $variants[0]->anssumout);
        $this->assertEquals(123456789, $variants[1]->seed);
        $this->assertEquals($expectedsum1, $variants[1]->notessumout->sumout);
        $this->assertEquals($expectedans1, $variants[1]->anssumout);
    }

    public function test_inputs_summary_multiple(): void {

        $this->set_question_mult();
        $inputs = $this->report->format_inputs()->inputs;
        $expected = "## ans1 (2)\n### score\n2 (100.00%); 11\n\n## ans2 (2)\n### score\n1 ( 50.00%); " .
            "22\n\n### valid\n1 ( 50.00%); vv\n\n## ans3 (2)\n### score\n1 ( 50.00%); 33\n\n### invalid\n1 " .
            "( 50.00%); ii\n\n## ans4 (2)\n### score\n1 ( 50.00%); 44\n\n### other\n1 ( 50.00%); zz\n\n";
        $this->assertEquals($expected, $inputs);
    }

    public function test_raw_data_multiple(): void {

        $this->set_question_mult();
        $rawdata = $this->report->format_raw_data()->rawdata;
        $expected = "\n# 3 (1)\n1 (100.00%); Seed: 333333333; ans1: 11 [score]; ans2: 22 [score]; " .
            "ans3: 33 [score]; ans4: 44 [score]; odd: " . RESPONSETS . "; even: " . RESPONSETS . "; oddeven: " . RESPONSETS .
            "; unique: " . RESPONSETS . "\n\n# 1 (1)\n1 (100.00%); Seed: 123456789; ans1: 11 [score]; ans2: vv [valid]; " .
            "ans3: ii [invalid]; ans4: zz; odd: # = 0 | thing1_true | prt1-1-T; even: !; oddeven: !; unique: !\n";
        $this->assertEquals($expected, $rawdata);
    }

    public function test_quiz_selection(): void {
        global $DB;
        $this->resetAfterTest();
        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $course = $this->getDataGenerator()->create_course();
        $contextid = \context_course::instance($course->id)->id;
        $qcategory = $generator->create_question_category(
            ['contextid' => $contextid]);
        $user = $this->getDataGenerator()->create_user();
        $managerroleid = $DB->get_field('role', 'id', ['shortname' => 'manager']);
        role_assign($managerroleid, $user->id, $contextid);
        $this->setUser($user);
        $q = $generator->create_question('shortanswer', null,
                        ['name' => 'QNAME1', 'category' => $qcategory->id]);
        $q2 = $generator->create_question('shortanswer', null,
                        ['name' => 'QNAME2', 'category' => $qcategory->id]);

        $quizgenerator = new \testing_data_generator();
        $quizgenerator = $quizgenerator->get_plugin_generator('mod_quiz');

        $quiz1 = $quizgenerator->create_instance(['course' => $course->id,
            'name' => 'QUIZNAME1', 'questionsperpage' => 0,
            'grade' => 100.0, 'sumgrades' => 2, 'preferredbehaviour' => 'immediatefeedback']);
        $quiz2 = $quizgenerator->create_instance(['course' => $course->id,
            'name' => 'QUIZNAME2', 'questionsperpage' => 0,
            'grade' => 100.0, 'sumgrades' => 2, 'preferredbehaviour' => 'immediatefeedback']);
        $quiz3 = $quizgenerator->create_instance(['course' => $course->id,
            'name' => 'QUIZNAME3', 'questionsperpage' => 0,
            'grade' => 100.0, 'sumgrades' => 2, 'preferredbehaviour' => 'immediatefeedback']);

        $quiz1contextid = \context_module::instance($quiz1->cmid)->id;
        $quiz1qcategory = $generator->create_question_category(
            ['contextid' => $quiz1contextid]);
        $q3 = $generator->create_question('shortanswer', null,
            ['name' => 'QNAME2', 'category' => $quiz1qcategory->id]);

        // No questions added to quizzes.
        $quizzes = stack_question_report::get_relevant_quizzes($q->id, $contextid);
        $this->assertEquals(0, count($quizzes));
        $quizzes = stack_question_report::get_relevant_quizzes($q2->id, $contextid);
        $this->assertEquals(0, count($quizzes));
        $quizzes = stack_question_report::get_relevant_quizzes($q3->id, $contextid);
        $this->assertEquals(0, count($quizzes));

        // Quiz 1: Add q1. Add q3 as part of random selection
        \quiz_add_quiz_question($q->id, $quiz1);
        $this->add_random_questions($quiz1->id, 0, $quiz1qcategory->id, 1);
        // Quiz 2: Add q1 and q2.
        \quiz_add_quiz_question($q->id, $quiz2);
        \quiz_add_quiz_question($q2->id, $quiz2);
        // Quiz 3: Add q1 and q2 as part of random selection.
        global $CFG;
        if ($CFG->version > 2022041900) {
            $this->add_random_questions($quiz3->id, 0, $qcategory->id, 1);
        } else {
            \quiz_add_random_questions($quiz3, 0, $qcategory->id, 1, false);
        }

        if (class_exists('\mod_quiz\quiz_settings')) {
            $quizobj1 = \mod_quiz\quiz_settings::create($quiz1->id);
            $quizobj2 = \mod_quiz\quiz_settings::create($quiz2->id);
            $quizobj3 = \mod_quiz\quiz_settings::create($quiz3->id);
        } else {
            $quizobj1 = \quiz::create($quiz1->id);
            $quizobj2 = \quiz::create($quiz2->id);
            $quizobj3 = \quiz::create($quiz3->id);
        }
        \mod_quiz\structure::create_for_quiz($quizobj1);
        \mod_quiz\structure::create_for_quiz($quizobj2);
        \mod_quiz\structure::create_for_quiz($quizobj3);
        $quizzes = stack_question_report::get_relevant_quizzes($q->id, $contextid);
        $quiznames = array_column($quizzes, 'name');
        $this->assertEquals(3, count($quizzes));
        $this->assertEquals(true, in_array('QUIZNAME1', $quiznames));
        $this->assertEquals(true, in_array('QUIZNAME2', $quiznames));
        $this->assertEquals(true, in_array('QUIZNAME3', $quiznames));
        $quizzes = stack_question_report::get_relevant_quizzes($q2->id, $contextid);
        $quiznames = array_column($quizzes, 'name');
        $this->assertEquals(2, count($quizzes));
        $this->assertEquals(true, in_array('QUIZNAME2', $quiznames));
        $this->assertEquals(true, in_array('QUIZNAME3', $quiznames));
        $quizzes = stack_question_report::get_relevant_quizzes($q3->id, $quiz1contextid);
        $this->assertEquals(1, count($quizzes));
        $quiznames = array_column($quizzes, 'name');
        $this->assertEquals(true, in_array('QUIZNAME1', $quiznames));
    }
}

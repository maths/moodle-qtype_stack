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

define ('RESPONSEFS1', '# = 0 | thing1_yuck | prt1-1-F');
define ('RESPONSETFS', '# = 0 | thing1_true | prt1-1-T | thing2_yuck. | prt1-2-F');
define ('RESPONSETTS', '# = 1 | thing1_true | prt1-1-T | thing2_true. | prt1-2-T');
define ('RESPONSEFS2', '# = 0 | thing1_ew | prt1-1-F');
define ('RESPONSE3F', 'Seed: 333333333; ans1: 22 [score]; PotResTree_1: ' . RESPONSEFS1);
define ('RESPONSE3TF', 'Seed: 333333333; ans1: 103 [score]; PotResTree_1: ' . RESPONSETFS);
define ('RESPONSE3TT', 'Seed: 333333333; ans1: x+3 [score]; PotResTree_1: ' . RESPONSETTS);
define ('RESPONSE1F', 'Seed: 123456789; ans1: 45 [score]; PotResTree_1: ' . RESPONSEFS2);
define ('RESPONSE5F', 'Seed: 555555555; ans1: 78 [score]; PotResTree_1: ' . RESPONSEFS2);

// Unit tests for the response analysis report.
//
// @copyright 2023 The University of Edinburgh.
// @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.

/**
 * @group qtype_stack
 * @covers \stack_question_report
 */
class responseanalysis_test extends qtype_stack_testcase {
    public $report;
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
    public $notes = [
        1 => 'Variant One',
        3 => 'Variant Three',
        5 => 'Variant Five',
    ];
    public $seeds = [
        1 => 123456789,
        3 => 333333333,
        5 => 555555555,
    ];
    public $summary = [
        3 => [RESPONSE3F => 1, RESPONSE3TF => 1, RESPONSE3TT => 2],
        1 => [RESPONSE1F => 1],
        5 => [RESPONSE5F => 1],
    ];
    public $inputreport = [
        "3" => [
            "ans1" => [
                "score" => [
                    "22" => 1,
                    "103" => 1,
                    "x+3" => 2
                ],
                "valid" => [],
                "invalid" => [],
                "other" => []
            ]
        ],
        "1" => [
            "ans1" => [
                "score" => [
                    "45" => 1
                ],
                "valid" => [],
                "invalid" => [],
                "other" => []
            ]
        ],
        "5" => [
            "ans1" => [
                "score" => [
                    "78" => 1
                ],
                "valid" => [],
                "invalid" => [],
                "other" => []
            ]
        ]
    ];
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
            "other" => []
        ]
    ];
    public $prtreport = [
        "3" => [
            "PotResTree_1" => [
                RESPONSEFS1 => 1,
                RESPONSETFS => 1,
                RESPONSETTS => 2
            ]
        ],
        "1" => [
            "PotResTree_1" => [
                RESPONSEFS2 => 1
            ]
        ],
        "5" => [
            "PotResTree_1" => [
                RESPONSEFS2 => 1
            ]
        ]
    ];
    public $prtreportinputs = [
        "3" => [
            "PotResTree_1" => [
                RESPONSEFS1 => [
                    "ans1:22; " => 1
                ],
                RESPONSETFS => [
                    "ans1:103; " => 1
                ],
                RESPONSETTS => [
                    "ans1:x+3; " => 2
                ]
            ]
        ],
        "1" => [
            "PotResTree_1" => [
                RESPONSEFS2 => [
                    "ans1:45; " => 1
                ]
            ]
        ],
        "5" => [
            "PotResTree_1" => [
                RESPONSEFS2 => [
                    "ans1:78; " => 1
                ]
            ]
        ]
    ];
    public $prtreportsummary = [
        "PotResTree_1" => [
            RESPONSEFS1 => 1,
            RESPONSETFS => 1,
            RESPONSETTS => 2,
            RESPONSEFS2 => 2
        ]
    ];

    public static $question;

    public static function setUpBeforeClass(): void {
        self::$question = test_question_maker::make_question('stack', 'test1');
    }

    public function x_test_create_summary() {
        $this->report = $this->getMockBuilder(stack_question_report::class)
            ->setMethods(['load_summary_data', 'run_report'])
            ->setConstructorArgs([self::$question, 2, 1])->getMock();
        $this->report->expects($this->any())
            ->method("load_summary_data")
            ->willReturn((array)json_decode($this->sqlsummary));
        $this->report->create_summary();
        $this->assertEquals($this->summary, $this->report->summary);
    }

    public function x_test_collate() {
        $this->report = $this->getMockBuilder(stack_question_report::class)
            ->setMethods(['run_report'])
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

    public function x_test_format_summary() {
        $this->report = $this->getMockBuilder(stack_question_report::class)
            ->setMethods(['run_report'])
            ->setConstructorArgs([self::$question, 2, 1])->getMock();
        $this->report->summary = $this->summary;
        $this->report->questionnotes = $this->notes;
        $this->report->questionseeds = $this->seeds;
        $this->report->collate();
        $this->report->reports_sort();
        $summary = $this->report->format_summary();
        $expected = "## PotResTree_1 (6)\n2 ( 33.33%); " . RESPONSEFS2 . "\n1 ( 16.67%); " . RESPONSETFS .
            "\n1 ( 16.67%); " . RESPONSEFS1 . "\n2 ( 33.33%); " . RESPONSETTS;
        $this->assertEquals($expected, $summary->prts[0]->sumout);
        $this->assertEquals(6, $summary->tot['PotResTree_1']);
    }

    public function x_test_note_summary() {
        $this->report = $this->getMockBuilder(stack_question_report::class)
            ->setMethods(['run_report'])
            ->setConstructorArgs([self::$question, 2, 1])->getMock();
        $this->report->summary = $this->summary;
        $this->report->questionnotes = $this->notes;
        $this->report->questionseeds = $this->seeds;
        $this->report->collate();
        $this->report->reports_sort();
        $summary = $this->report->format_summary();
        $notesummary = $this->report->format_notesummary($summary->tot);
        $expected = "## PotResTree_1 (6)\n4 ( 66.67%); # = 0\n2 ( 33.33%); # = 1\n3 ( 50.00%); prt1-1-F\n3 ( 50.00%); " .
            "prt1-1-T\n1 ( 16.67%); prt1-2-F\n2 ( 33.33%); prt1-2-T\n2 ( 33.33%); thing1_ew\n3 ( 50.00%); " .
            "thing1_true\n1 ( 16.67%); thing1_yuck\n2 ( 33.33%); thing2_true.\n1 ( 16.67%); thing2_yuck.\n\n";
        $this->assertEquals('PotResTree_1', $notesummary->prts[0]->prtname);
        $this->assertEquals($expected, $notesummary->prts[0]->sumout);
    }

    public function x_test_variants_summary() {
        $this->report = $this->getMockBuilder(stack_question_report::class)
            ->setMethods(['run_report'])
            ->setConstructorArgs([self::$question, 2, 1])->getMock();
        $this->report->summary = $this->summary;
        $this->report->questionnotes = $this->notes;
        $this->report->questionseeds = $this->seeds;
        $this->report->collate();
        $this->report->reports_sort();
        $variants = $this->report->format_variants()->variants;
        $expectedsum3 = "## PotResTree_1 (4)\n2 ( 50.00%); " . RESPONSETTS . "\n2 ( 50.00%); ans1:x+3; \n\n1 ( 25.00%); " . RESPONSEFS1 . "\n1 ( 25.00%); ans1:22; \n\n1 ( 25.00%); " . RESPONSETFS . "\n1 ( 25.00%); ans1:103; \n\n";
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

    public function x_test_inputs_summary() {
        $this->report = $this->getMockBuilder(stack_question_report::class)
            ->setMethods(['run_report'])
            ->setConstructorArgs([self::$question, 2, 1])->getMock();
        $this->report->summary = $this->summary;
        $this->report->questionnotes = $this->notes;
        $this->report->questionseeds = $this->seeds;
        $this->report->collate();
        $this->report->reports_sort();
        $inputs = $this->report->format_inputs()->inputs;
        $expected = "## ans1 (6)\n### score\n2 ( 33.33%); x+3\n1 ( 16.67%); 22\n1 ( 16.67%); 103\n1 ( 16.67%); 45\n1 ( 16.67%); 78\n\n";
        $this->assertEquals($expected, $inputs);
    }

    public function test_raw_data() {
        $this->report = $this->getMockBuilder(stack_question_report::class)
            ->setMethods(['run_report'])
            ->setConstructorArgs([self::$question, 2, 1])->getMock();
        $this->report->summary = $this->summary;
        $this->report->questionnotes = $this->notes;
        $this->report->questionseeds = $this->seeds;
        $this->report->collate();
        $this->report->reports_sort();
        $rawdata = $this->report->format_raw_data()->rawdata;
        $expected = "\n# 3 (4)\n1 ( 25.00%); Seed: 333333333; ans1: 22 [score]; PotResTree_1: " .
            RESPONSEFS1 . "\n1 ( 25.00%); Seed: 333333333; ans1: 103 [score]; PotResTree_1: " . RESPONSETFS .
            "\n2 ( 50.00%); Seed: 333333333; ans1: x+3 [score]; PotResTree_1: " . RESPONSETTS . "\n\n# 1 (1)\n1 (100.00%); " .
            "Seed: 123456789; ans1: 45 [score]; PotResTree_1: " . RESPONSEFS2 . "\n\n# 5 (1)\n1 (100.00%); " .
            "Seed: 555555555; ans1: 78 [score]; PotResTree_1: " . RESPONSEFS2 . "\n";
        $this->assertEquals($expected, $rawdata);
        //echo json_encode($this->report->format_raw_data());
    }
}

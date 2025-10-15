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
require_once($CFG->dirroot . '/mod/quiz/lib.php');
require_once($CFG->dirroot . '/mod/quiz/tests/quiz_question_helper_test_trait.php');

define ('RESPONSET', '# = 1 | prt1-1-T');
define ('RESPONSEFF', '# = 0 | prt1-1-F | prt1-2-F');
define ('RESPONSEFT', '# = 1 | prt1-1-F | prt1-2-T');
define ('RESPONSE3FF', 'Seed: 333333333; ans1: "thing1_yuck" [score]; PotResTree_1: ' . RESPONSEFF);
define ('RESPONSE3FF2', 'Seed: 333333333; ans1: "thing2_yuck" [score]; PotResTree_1: ' . RESPONSEFF);
define ('RESPONSE3T', 'Seed: 333333333; ans1: "thing1_true" [score]; PotResTree_1: ' . RESPONSET);
define ('RESPONSE3FT', 'Seed: 333333333; ans1: "thing2_true" [score]; PotResTree_1: ' . RESPONSEFT);
define ('RESPONSE1FF', 'Seed: 123456789; ans1: "thing1_ew" [score]; PotResTree_1: ' . RESPONSEFF);
define ('RESPONSE5FF', 'Seed: 555555555; ans1: "thing1_ew" [score]; PotResTree_1: ' . RESPONSEFF);

define ('MULTRESPONSE3TTFT', 'Seed: 333333333; ans1: x^3 [score]; ans2: x^4 [score]; ans3: 0 [score]; ans4: true [score];' .
        ' odd: # = 1 | odd-1-T; even: # = 1 | even-1-T;' .
        ' oddeven: # = 1 | oddeven-1-T | oddeven-2-T; unique: # = 1 | ATLogic_True. | unique-1-T');
define ('MULTRESPONSE1TNNN', 'Seed: 123456789; ans1: x^5 [score]; ans2: vvv [invalid]; ans3: iii [invalid]; ans4: zz [invalid];' .
        ' odd: # = 1 | odd-1-T; even: !; oddeven: !; unique: !');

define ('MULTRESPONSE1TVIO', 'Seed: 123456789; ans1: x^3 [score]; ans2: vv [valid]; ans3: iii [invalid]; ans4: zzz;' .
        ' odd: # = 1 | odd-1-T; even: !; oddeven: !; unique: !');
define ('RESPONSEOT', '# = 1 | odd-1-T');
define ('RESPONSEET', '# = 1 | even-1-T');
define ('RESPONSEOET', '# = 1 | oddeven-1-T | oddeven-2-T');
define ('RESPONSEUT', '# = 1 | ATLogic_True. | unique-1-T');

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
        3 => [RESPONSE3FF => 1, RESPONSE3FF2 => 1, RESPONSE3T => 1, RESPONSE3FT => 2],
        1 => [RESPONSE1FF => 1],
        5 => [RESPONSE5FF => 1],
    ];
    // phpcs:ignore moodle.Commenting.VariableComment.Missing
    public $inputreport = [
        "3" => [
            "ans1" => [
                "score" => [
                    '"thing1_yuck"' => 1,
                    '"thing2_yuck"' => 1,
                    '"thing2_true"' => 2,
                    '"thing1_true"' => 1,
                ],
                "valid" => [],
                "invalid" => [],
                "other" => [],
            ],
        ],
        "1" => [
            "ans1" => [
                "score" => [
                    '"thing1_ew"' => 1,
                ],
                "valid" => [],
                "invalid" => [],
                "other" => [],
            ],
        ],
        "5" => [
            "ans1" => [
                "score" => [
                    '"thing1_ew"' => 1,
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
                '"thing1_ew"' => 2,
                '"thing1_true"' => 1,
                '"thing1_yuck"' => 1,
                '"thing2_true"' => 2,
                '"thing2_yuck"' => 1,
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
                RESPONSET => 1,
                RESPONSEFF => 2,
                RESPONSEFT => 2,
            ],
        ],
        "1" => [
            "PotResTree_1" => [
                RESPONSEFF => 1,
            ],
        ],
        "5" => [
            "PotResTree_1" => [
                RESPONSEFF => 1,
            ],
        ],
    ];
    // phpcs:ignore moodle.Commenting.VariableComment.Missing
    public $prtreportinputs = [
        "3" => [
            "PotResTree_1" => [
                RESPONSET => [
                    'ans1:"thing1_true"; ' => 1,
                ],
                RESPONSEFF => [
                    'ans1:"thing1_yuck"; ' => 1,
                    'ans1:"thing2_yuck"; ' => 1,
                ],
                RESPONSEFT => [
                    'ans1:"thing2_true"; ' => 2,
                ],
            ],
        ],
        "1" => [
            "PotResTree_1" => [
                RESPONSEFF => [
                    'ans1:"thing1_ew"; ' => 1,
                ],
            ],
        ],
        "5" => [
            "PotResTree_1" => [
                RESPONSEFF => [
                    'ans1:"thing1_ew"; ' => 1,
                ],
            ],
        ],
    ];
    // phpcs:ignore moodle.Commenting.VariableComment.Missing
    public $prtreportsummary = [
        "PotResTree_1" => [
            RESPONSET => 1,
            RESPONSEFT => 2,
            RESPONSEFF => 4,
        ],
    ];
    // phpcs:ignore moodle.Commenting.VariableComment.Missing
    public $summarymult = [
        3 => [MULTRESPONSE3TTFT => 1],
        1 => [MULTRESPONSE1TNNN => 1],
    ];
    // phpcs:ignore moodle.Commenting.VariableComment.Missing
    public $summarymult2 = [
        3 => [MULTRESPONSE3TTFT => 1],
        1 => [MULTRESPONSE1TVIO => 1],
    ];

    // phpcs:ignore moodle.Commenting.VariableComment.Missing
    public $inputreportmult = [
        "3" => [
            "ans1" => [
                "score" => [
                    "x^3" => 1,
                ],
                "valid" => [],
                "invalid" => [],
                "other" => [],
            ],
            "ans2" => [
                "score" => [
                    "x^4" => 1,
                ],
                "valid" => [],
                "invalid" => [],
                "other" => [],
            ],
            "ans3" => [
                "score" => [
                    "0" => 1,
                ],
                "valid" => [],
                "invalid" => [],
                "other" => [],
            ],
            "ans4" => [
                "score" => [
                    "true" => 1,
                ],
                "valid" => [],
                "invalid" => [],
                "other" => [],
            ],
        ],
        "1" => [
            "ans1" => [
                "score" => [
                    "x^3" => 1,
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
                    "iii" => 1,
                ],
                "other" => [],
            ],
            "ans4" => [
                "score" => [],
                "valid" => [],
                "invalid" => [],
                "other" => [
                    "zzz" => 1,
                ],
            ],
        ],
    ];
    // phpcs:ignore moodle.Commenting.VariableComment.Missing
    public $inputreportsummarymult = [
        "ans1" => [
            "score" => [
                "x^3" => 2,
            ],
            "valid" => [],
            "invalid" => [],
            "other" => [],
        ],
        "ans2" => [
            "score" => [
                "x^4" => 1,
            ],
            "valid" => [
                "vv" => 1,
            ],
            "invalid" => [],
            "other" => [],
        ],
        "ans3" => [
            "score" => [
                "0" => 1,
            ],
            "valid" => [],
            "invalid" => [
                "iii" => 1,
            ],
            "other" => [],
        ],
        "ans4" => [
            "score" => [
                "true" => 1,
            ],
            "valid" => [],
            "invalid" => [],
            "other" => [
                "zzz" => 1,
            ],
        ],
    ];
    // phpcs:ignore moodle.Commenting.VariableComment.Missing
    public $prtreportmult = [
        "3" => [
            "odd" => [
                RESPONSEOT => 1,
            ],
            "even" => [
                RESPONSEET => 1,
            ],
            "oddeven" => [
                RESPONSEOET => 1,
            ],
            "unique" => [
                RESPONSEUT => 1,
            ],
        ],
        "1" => [
            "odd" => [
                RESPONSEOT => 1,
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
                RESPONSEOT => [
                    "ans1:x^3; " => 1,
                ],
            ],
            "even" => [
                RESPONSEET => [
                    "ans2:x^4; " => 1,
                ],
            ],
            "oddeven" => [
                RESPONSEOET => [
                    "ans3:0; " => 1,
                ],
            ],
            "unique" => [
                RESPONSEUT => [
                    "ans4:true; " => 1,
                ],
            ],
        ],
        "1" => [
            "odd" => [
                RESPONSEOT => [
                    "ans1:x^3; " => 1,
                ],
            ],
            "even" => [
                "!" => [
                    "ans2:vv; " => 1,
                ],
            ],
            "oddeven" => [
                "!" => [
                    "ans3:iii; " => 1,
                ],
            ],
            "unique" => [
                "!" => [
                    "ans4:zzz; " => 1,
                ],
            ],
        ],
    ];
    // phpcs:ignore moodle.Commenting.VariableComment.Missing
    public $prtreportsummarymult = [
        "odd" => [
            RESPONSEOT => 2,
        ],
        "even" => [
            RESPONSEET => 1,
            "!" => 1,
        ],
        "oddeven" => [
            RESPONSEOET => 1,
            "!" => 1,
        ],
        "unique" => [
            RESPONSEUT => 1,
            "!" => 1,
        ],
    ];
    // phpcs:ignore moodle.Commenting.VariableComment.Missing
    public static $question;
    // phpcs:ignore moodle.Commenting.VariableComment.Missing
    public static $question2;
    // phpcs:ignore moodle.Commenting.VariableComment.Missing
    public $coursecontextid;
    // phpcs:ignore moodle.Commenting.VariableComment.Missing
    public $quizcontextid;
    // phpcs:ignore moodle.Commenting.VariableComment.Missing
    public $quizquestion;

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

    // phpcs:ignore moodle.Commenting.MissingDocblock.MissingTestcaseMethodDescription
    public function set_question_mult2(): void {
        $this->report = $this->getMockBuilder(stack_question_report::class)
            ->onlyMethods(['run_report'])
            ->setConstructorArgs([self::$question2, 2, 1])->getMock();
        $this->report->summary = $this->summarymult2;
        $this->report->questionnotes = $this->notes;
        $this->report->questionseeds = $this->seeds;
        $this->report->collate();
        $this->report->reports_sort();
    }

    /**
     * Add question attempt steps to the DB.
     * @param string $type The test being done.
     * @return array
     */
    public function create_steps($type): array {
        $this->resetAfterTest();
        global $DB;

        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $course = $this->getDataGenerator()->create_course();
        $contextid = \context_course::instance($course->id)->id;
        // For Moodle 5 this will be in a question bank module.
        $qcategory = $generator->create_question_category(
            ['contextid' => $contextid]);
        $user = $this->getDataGenerator()->create_user();
        $managerroleid = $DB->get_field('role', 'id', ['shortname' => 'manager']);
        role_assign($managerroleid, $user->id, $contextid);
        $this->setUser($user);
        switch ($type) {
            case 'singleinput':
                $q = $generator->create_question('stack', 'response_test',
                                ['name' => 'QNAME1', 'category' => $qcategory->id]);
                $this->quizquestion = $q;
                $steps = [
                    [0, 'John', 'Jones', 3,
                        [1 => ['ans1' => 'thing1_yuck', 'ans1_val' => '"thing1_yuck"', 'step_lang' => 'en']], 1],
                    [1, 'John', 'Jones', 3,
                        [1 => ['ans1' => 'thing2_yuck', 'ans1_val' => '"thing2_yuck"', 'step_lang' => 'en']], 1],
                    [2, 'John', 'Jones', 3,
                        [1 => ['ans1' => 'thing2_true', 'ans1_val' => '"thing2_true"', 'step_lang' => 'en']], 1],
                    [3, 'John', 'Jones', 1,
                        [1 => ['ans1' => 'thing1_ew', 'ans1_val' => '"thing1_ew"', 'step_lang' => 'en']], 1],
                    [4, 'John', 'Jones', 5,
                        [1 => ['ans1' => 'thing1_ew', 'ans1_val' => '"thing1_ew"', 'step_lang' => 'en']], 1],
                    [5, 'John', 'Jones', 3,
                        [1 => ['ans1' => 'thing2_true', 'ans1_val' => '"thing2_true"', 'step_lang' => 'en']], 1],
                    [6, 'John', 'Jones', 3,
                        [1 => ['ans1' => 'thing1_true', 'ans1_val' => '"thing1_true"', 'step_lang' => 'en']], 1],
                ];
                $behaviour = 'immediatefeedback';
                break;
            case 'multiinput':
                $q = $generator->create_question('stack', 'response_test_2',
                                ['name' => 'QNAME1', 'category' => $qcategory->id]);
                $this->quizquestion = $q;

                $steps = [
                    [0, 'John', 'Jones', 3, [1 => [
                        'ans1' => 'x^3', 'ans1_val' => 'x^3',
                        'ans2' => 'x^4', 'ans2_val' => 'x^4',
                        'ans3' => '0', 'ans3_val' => '0',
                        'ans4' => 'true', 'ans4_val' => 'true',
                        'step_lang' => 'en']], 1],
                    [1, 'John', 'Jones', 1, [1 => [
                        'ans1' => 'x^5', 'ans1_val' => 'x^5',
                        'ans2' => "vvv", 'ans2_val' => "vvv",
                        'ans3' => "iii", 'ans3_val' => "ii",
                        'ans4' => "zz", 'ans4_val' => "zz",
                        'step_lang' => 'en']], 1],
                ];
                $behaviour = 'immediatefeedback';
                break;
            case 'interactive1':
                // Two wrong answers and then correct.
                $q = $generator->create_question('stack', 'response_test',
                                ['name' => 'QNAME1', 'category' => $qcategory->id]);
                $this->quizquestion = $q;
                $steps = [
                    [0, 'John', 'Jones', 3,
                        [1 => ['ans1' => 'thing1_yuck', 'ans1_val' => '"thing1_yuck"', 'step_lang' => 'en', '-submit' => 1]], 1,
                        [1 => ['ans1' => 'thing2_yuck', 'ans1_val' => '"thing2_yuck"', 'step_lang' => 'en', '-submit' => 1]],
                        [1 => ['ans1' => 'thing1_true', 'ans1_val' => '"thing1_true"', 'step_lang' => 'en', '-submit' => 1]]],
                ];
                $behaviour = 'interactive';
                break;
            case 'interactive2':
                // Three wrong answers.
                $q = $generator->create_question('stack', 'response_test',
                                ['name' => 'QNAME1', 'category' => $qcategory->id]);
                $this->quizquestion = $q;
                $steps = [
                    [0, 'John', 'Jones', 3,
                        [1 => ['ans1' => 'thing1_yuck', 'ans1_val' => '"thing1_yuck"', 'step_lang' => 'en', '-submit' => 1]], 1,
                        [1 => ['ans1' => 'thing2_yuck', 'ans1_val' => '"thing2_yuck"', 'step_lang' => 'en', '-submit' => 1]],
                        [1 => ['ans1' => 'thing1_yuck', 'ans1_val' => '"thing1_yuck"', 'step_lang' => 'en', '-submit' => 1]]],
                ];
                $behaviour = 'interactive';
                break;
            case 'interactive3':
                // Three wrong answers. No final submit.
                $q = $generator->create_question('stack', 'response_test',
                                ['name' => 'QNAME1', 'category' => $qcategory->id]);
                $this->quizquestion = $q;
                $steps = [
                    [0, 'John', 'Jones', 3,
                        [1 => ['ans1' => 'thing1_yuck', 'ans1_val' => '"thing1_yuck"', 'step_lang' => 'en', '-submit' => 1]], 0,
                        [1 => ['ans1' => 'thing2_yuck', 'ans1_val' => '"thing2_yuck"', 'step_lang' => 'en', '-submit' => 1]],
                        [1 => ['ans1' => 'thing1_yuck', 'ans1_val' => '"thing1_yuck"', 'step_lang' => 'en', '-submit' => 1]]],
                ];
                $behaviour = 'interactive';
                break;
            case 'interactive4':
                // One wrong answer and then correct. No final submit.
                $q = $generator->create_question('stack', 'response_test',
                                ['name' => 'QNAME1', 'category' => $qcategory->id]);
                $this->quizquestion = $q;
                $steps = [
                    [0, 'John', 'Jones', 3,
                        [1 => ['ans1' => 'thing1_yuck', 'ans1_val' => '"thing1_yuck"', 'step_lang' => 'en', '-submit' => 1]], 0,
                        [1 => ['ans1' => 'thing1_true', 'ans1_val' => '"thing1_true"', 'step_lang' => 'en', '-submit' => 1]]],
                ];
                $behaviour = 'interactive';
                break;
        }
        $qtype = new \qtype_stack();
        $qtype->deploy_variant($q->id, 123456789);
        $qtype->deploy_variant($q->id, 222222222);
        $qtype->deploy_variant($q->id, 333333333);
        $qtype->deploy_variant($q->id, 444444444);
        $qtype->deploy_variant($q->id, 555555555);
        $quizgenerator = new \testing_data_generator();
        $quizgenerator = $quizgenerator->get_plugin_generator('mod_quiz');

        $quiz1 = $quizgenerator->create_instance(['course' => $course->id,
            'name' => 'QUIZNAME1', 'questionsperpage' => 0,
            'grade' => 100.0, 'sumgrades' => 2, 'preferredbehaviour' => $behaviour]);

        \quiz_add_quiz_question($q->id, $quiz1, 0);

        $attemptids = [];
        // Attempt id, user first name, user last name, variant number, answer data, finished.

        foreach ($steps as $step) {
            // Find existing user or make a new user to do the quiz.
            $username = ['firstname' => $step[1], 'lastname'  => $step[2]];

            if (!$user = $DB->get_record('user', $username)) {
                $user = $this->getDataGenerator()->create_user($username);
                $studentid = $DB->get_field('role', 'id', ['shortname' => 'student']);
                role_assign($studentid, $user->id, $contextid);

            }

            if (!isset($attemptids[$step[0]])) {
                // Start the attempt.
                if (class_exists('\mod_quiz\quiz_settings')) {
                    $quizobj = \mod_quiz\quiz_settings::create($quiz1->id, $user->id);
                } else {
                    $quizobj = \quiz::create($quiz1->id, $user->id);
                }
                $quba = \question_engine::make_questions_usage_by_activity('mod_quiz', $quizobj->get_context());
                $quba->set_preferred_behaviour($quizobj->get_quiz()->preferredbehaviour);

                $prevattempts = \quiz_get_user_attempts($quiz1->id, $user->id, 'all', true);
                $attemptnumber = count($prevattempts) + 1;
                $timenow = time();
                $attempt = \quiz_create_attempt($quizobj, $attemptnumber, null, $timenow, false, $user->id);

                \quiz_start_new_attempt($quizobj, $quba, $attempt, $attemptnumber, $timenow, [], [1 => $step[3]]);
                \quiz_attempt_save_started($quizobj, $quba, $attempt);
                $attemptid = $attemptids[$step[0]] = $attempt->id;
            } else {
                $attemptid = $attemptids[$step[0]];
            }

            // Process some responses from the student.
            if (class_exists('\mod_quiz\quiz_attempt')) {
                $attemptobj = \mod_quiz\quiz_attempt::create($attemptid);
            } else {
                $attemptobj = \quiz_attempt::create($attemptid);
            }
            $attemptobj->process_submitted_actions($timenow, false, $step[4]);
            if (isset($step[6])) {
                $attemptobj->process_submitted_actions($timenow, false, [1 => ['-tryagain' => 1]]);
                $attemptobj->process_submitted_actions($timenow, false, $step[6]);
            }
            if (isset($step[7])) {
                $attemptobj->process_submitted_actions($timenow, false, [1 => ['-tryagain' => 1]]);
                $attemptobj->process_submitted_actions($timenow, false, $step[7]);
            }

            // Finish the attempt.
            if ($step[5] == 1) {
                $attemptobj->process_finish($timenow, false);
            }
        }
        $this->quizcontextid = $quizobj->get_context()->id;
        $this->coursecontextid = $contextid;
        return $attemptids;
    }

    public function test_create_summary(): void {
        $this->create_steps('singleinput');
        $this->report = $this->getMockBuilder(stack_question_report::class)
            ->onlyMethods(['run_report'])
            ->setConstructorArgs([$this->quizquestion, $this->quizcontextid, $this->coursecontextid])->getMock();
        $this->report->create_summary();
        $this->assertEquals($this->summary, $this->report->summary);
        $this->assertEquals(7, count($this->report->jsonsummary));
    }

    public function test_create_summary_interactive(): void {
        $summary = [
            3 => [RESPONSE3FF => 1, RESPONSE3FF2 => 1, RESPONSE3T => 1],
        ];
        $this->create_steps('interactive1');
        $this->report = $this->getMockBuilder(stack_question_report::class)
            ->onlyMethods(['run_report'])
            ->setConstructorArgs([$this->quizquestion, $this->quizcontextid, $this->coursecontextid])->getMock();
        $this->report->create_summary();
        $this->assertEquals($summary, $this->report->summary);
        $this->assertEquals(3, count($this->report->jsonsummary));
        $json0 = json_decode($this->report->jsonsummary[0]);
        $this->assertEquals('"thing1_yuck"', $json0->inputs->ans1->value);
        $this->assertEquals(0, $json0->prts->PotResTree_1->score);
        $this->assertEquals(0.4, $json0->prts->PotResTree_1->penalty);
        $this->assertEquals(['prt1-1-F', 'prt1-2-F'], $json0->prts->PotResTree_1->note);
        $this->assertEquals(333333333, $json0->seed);
        $json1 = json_decode($this->report->jsonsummary[1]);
        $this->assertEquals('"thing2_yuck"', $json1->inputs->ans1->value);
        $this->assertEquals(0, $json1->prts->PotResTree_1->score);
        $this->assertEquals(0.4, $json1->prts->PotResTree_1->penalty);
        $this->assertEquals(['prt1-1-F', 'prt1-2-F'], $json1->prts->PotResTree_1->note);
        $this->assertEquals(333333333, $json1->seed);
        $json2 = json_decode($this->report->jsonsummary[2]);
        $this->assertEquals('"thing1_true"', $json2->inputs->ans1->value);
        $this->assertEquals(1, $json2->prts->PotResTree_1->score);
        $this->assertEquals(0, $json2->prts->PotResTree_1->penalty);
        $this->assertEquals(['prt1-1-T'], $json2->prts->PotResTree_1->note);
        $this->assertEquals(333333333, $json2->seed);
    }

    public function test_create_summary_interactive2(): void {
        $summary = [
            3 => [RESPONSE3FF => 2, RESPONSE3FF2 => 1],
        ];
        $this->create_steps('interactive2');
        $this->report = $this->getMockBuilder(stack_question_report::class)
            ->onlyMethods(['run_report'])
            ->setConstructorArgs([$this->quizquestion, $this->quizcontextid, $this->coursecontextid])->getMock();
        $this->report->create_summary();
        $this->assertEquals($summary, $this->report->summary);
        $this->assertEquals(3, count($this->report->jsonsummary));
        $json0 = json_decode($this->report->jsonsummary[0]);
        $this->assertEquals('"thing1_yuck"', $json0->inputs->ans1->value);
        $this->assertEquals(0, $json0->prts->PotResTree_1->score);
        $this->assertEquals(0.4, $json0->prts->PotResTree_1->penalty);
        $this->assertEquals(['prt1-1-F', 'prt1-2-F'], $json0->prts->PotResTree_1->note);
        $this->assertEquals(333333333, $json0->seed);
        $json1 = json_decode($this->report->jsonsummary[1]);
        $this->assertEquals('"thing2_yuck"', $json1->inputs->ans1->value);
        $this->assertEquals(0, $json1->prts->PotResTree_1->score);
        $this->assertEquals(0.4, $json1->prts->PotResTree_1->penalty);
        $this->assertEquals(['prt1-1-F', 'prt1-2-F'], $json1->prts->PotResTree_1->note);
        $this->assertEquals(333333333, $json1->seed);
        $json2 = json_decode($this->report->jsonsummary[2]);
        $this->assertEquals('"thing1_yuck"', $json2->inputs->ans1->value);
        $this->assertEquals(0, $json2->prts->PotResTree_1->score);
        $this->assertEquals(0.4, $json2->prts->PotResTree_1->penalty);
        $this->assertEquals(['prt1-1-F', 'prt1-2-F'], $json2->prts->PotResTree_1->note);
        $this->assertEquals(333333333, $json2->seed);
    }

    public function test_create_summary_interactive3(): void {
        $summary = [
            3 => [RESPONSE3FF => 2, RESPONSE3FF2 => 1],
        ];
        $this->create_steps('interactive3');
        $this->report = $this->getMockBuilder(stack_question_report::class)
            ->onlyMethods(['run_report'])
            ->setConstructorArgs([$this->quizquestion, $this->quizcontextid, $this->coursecontextid])->getMock();
        $this->report->create_summary();
        $this->assertEquals($summary, $this->report->summary);
        $this->assertEquals(3, count($this->report->jsonsummary));
        $json0 = json_decode($this->report->jsonsummary[0]);
        $this->assertEquals('"thing1_yuck"', $json0->inputs->ans1->value);
        $this->assertEquals(0, $json0->prts->PotResTree_1->score);
        $this->assertEquals(0.4, $json0->prts->PotResTree_1->penalty);
        $this->assertEquals(['prt1-1-F', 'prt1-2-F'], $json0->prts->PotResTree_1->note);
        $this->assertEquals(333333333, $json0->seed);
        $json1 = json_decode($this->report->jsonsummary[1]);
        $this->assertEquals('"thing2_yuck"', $json1->inputs->ans1->value);
        $this->assertEquals(0, $json1->prts->PotResTree_1->score);
        $this->assertEquals(0.4, $json1->prts->PotResTree_1->penalty);
        $this->assertEquals(['prt1-1-F', 'prt1-2-F'], $json1->prts->PotResTree_1->note);
        $this->assertEquals(333333333, $json1->seed);
        $json2 = json_decode($this->report->jsonsummary[2]);
        $this->assertEquals('"thing1_yuck"', $json2->inputs->ans1->value);
        $this->assertEquals(0, $json2->prts->PotResTree_1->score);
        $this->assertEquals(0.4, $json2->prts->PotResTree_1->penalty);
        $this->assertEquals(['prt1-1-F', 'prt1-2-F'], $json2->prts->PotResTree_1->note);
        $this->assertEquals(333333333, $json2->seed);
    }

    public function test_create_summary_interactive4(): void {
        $summary = [
            3 => [RESPONSE3FF => 1, RESPONSE3T => 1],
        ];
        $this->create_steps('interactive4');
        $this->report = $this->getMockBuilder(stack_question_report::class)
            ->onlyMethods(['run_report'])
            ->setConstructorArgs([$this->quizquestion, $this->quizcontextid, $this->coursecontextid])->getMock();
        $this->report->create_summary();
        $this->assertEquals($summary, $this->report->summary);
        $this->assertEquals(2, count($this->report->jsonsummary));
        $json0 = json_decode($this->report->jsonsummary[0]);
        $this->assertEquals('"thing1_yuck"', $json0->inputs->ans1->value);
        $this->assertEquals(0, $json0->prts->PotResTree_1->score);
        $this->assertEquals(0.4, $json0->prts->PotResTree_1->penalty);
        $this->assertEquals(['prt1-1-F', 'prt1-2-F'], $json0->prts->PotResTree_1->note);
        $this->assertEquals(333333333, $json0->seed);
        $json1 = json_decode($this->report->jsonsummary[1]);
        $this->assertEquals('"thing1_true"', $json1->inputs->ans1->value);
        $this->assertEquals(1, $json1->prts->PotResTree_1->score);
        $this->assertEquals(0, $json1->prts->PotResTree_1->penalty);
        $this->assertEquals(['prt1-1-T'], $json1->prts->PotResTree_1->note);
        $this->assertEquals(333333333, $json1->seed);
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
        $expected = "## PotResTree_1 (7)\n4 ( 57.14%); " . RESPONSEFF . "\n2 ( 28.57%); " . RESPONSEFT .
            "\n1 ( 14.29%); " . RESPONSET;
        $this->assertEquals($expected, $summary->prts[0]->sumout);
        $this->assertEquals(7, $summary->tot['PotResTree_1']);
    }

    public function test_note_summary(): void {

        $this->set_question();
        $summary = $this->report->format_summary();
        $notesummary = $this->report->format_notesummary($summary->tot);
        $expected = "## PotResTree_1 (7)\n4 ( 57.14%); # = 0\n3 ( 42.86%); # = 1\n6 ( 85.71%); prt1-1-F\n1 ( 14.29%); " .
            "prt1-1-T\n4 ( 57.14%); prt1-2-F\n2 ( 28.57%); prt1-2-T\n\n";
        $this->assertEquals('PotResTree_1', $notesummary->prts[0]->prtname);
        $this->assertEquals($expected, $notesummary->prts[0]->sumout);
    }

    public function test_variants_summary(): void {

        $this->set_question();
        $variants = $this->report->format_variants()->variants;
        $expectedsum3 = "## PotResTree_1 (5)" .
            "\n2 ( 40.00%); " . RESPONSEFF .
            "\n1 ( 20.00%); ans1:&quot;thing1_yuck&quot;; \n1 ( 20.00%); ans1:&quot;thing2_yuck&quot;; " .
            "\n\n2 ( 40.00%); " . RESPONSEFT .
            "\n2 ( 40.00%); ans1:&quot;thing2_true&quot;; " .
            "\n\n1 ( 20.00%); " . RESPONSET .
            "\n1 ( 20.00%); ans1:&quot;thing1_true&quot;; \n\n";
        $expectedsum1 = "## PotResTree_1 (1)" .
            "\n1 (100.00%); " . RESPONSEFF .
            "\n1 (100.00%); ans1:&quot;thing1_ew&quot;; \n\n";
        $expectedsum5 = "## PotResTree_1 (1)" .
            "\n1 (100.00%); " . RESPONSEFF .
            "\n1 (100.00%); ans1:&quot;thing1_ew&quot;; \n\n";
        $expectedans3 = "## ans1 (5)" .
            "\n### score\n2 ( 40.00%); &quot;thing2_true&quot;" .
            "\n1 ( 20.00%); &quot;thing1_yuck&quot;" .
            "\n1 ( 20.00%); &quot;thing2_yuck&quot;" .
            "\n1 ( 20.00%); &quot;thing1_true&quot;\n\n";
        $expectedans1 = "## ans1 (1)\n### score\n1 (100.00%); &quot;thing1_ew&quot;\n\n";
        $expectedans5 = "## ans1 (1)\n### score\n1 (100.00%); &quot;thing1_ew&quot;\n\n";
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
        $expected = "## ans1 (7)\n### score\n" .
            "2 ( 28.57%); &quot;thing2_true&quot;\n" .
            "2 ( 28.57%); &quot;thing1_ew&quot;\n" .
            "1 ( 14.29%); &quot;thing1_yuck&quot;\n" .
            "1 ( 14.29%); &quot;thing2_yuck&quot;\n" .
            "1 ( 14.29%); &quot;thing1_true&quot;\n\n";
        $this->assertEquals($expected, $inputs);
    }

    public function test_raw_data(): void {

        $this->set_question();
        $rawdata = $this->report->format_raw_data()->rawdata;
        $expected = "\n# 3 (5)" .
            "\n1 ( 20.00%); " . htmlspecialchars(RESPONSE3FF) .
            "\n1 ( 20.00%); " . htmlspecialchars(RESPONSE3FF2) .
            "\n1 ( 20.00%); " . htmlspecialchars(RESPONSE3T) .
            "\n2 ( 40.00%); " . htmlspecialchars(RESPONSE3FT) .
            "\n\n# 1 (1)" .
            "\n1 (100.00%); " . htmlspecialchars(RESPONSE1FF) .
            "\n\n# 5 (1)" .
            "\n1 (100.00%); " . htmlspecialchars(RESPONSE5FF) ."\n";
        $this->assertEquals($expected, $rawdata);
    }

    public function test_create_summary_multiple(): void {
        $this->create_steps('multiinput');
        $this->report = $this->getMockBuilder(stack_question_report::class)
            ->onlyMethods(['run_report'])
            ->setConstructorArgs([$this->quizquestion, $this->quizcontextid, $this->coursecontextid])->getMock();
        $this->report->create_summary();
        $this->assertEquals($this->summarymult, $this->report->summary);
        $this->assertEquals(2, count($this->report->jsonsummary));
        $json0 = json_decode($this->report->jsonsummary[0]);
        $this->assertEquals(4, count((array)$json0->inputs));
        $this->assertEquals('x^3', $json0->inputs->ans1->value);
        $this->assertEquals('x^4', $json0->inputs->ans2->value);
        $this->assertEquals('0', $json0->inputs->ans3->value);
        $this->assertEquals('true', $json0->inputs->ans4->value);
        $this->assertEquals(1, $json0->prts->odd->score);
        $this->assertEquals(0, $json0->prts->odd->penalty);
        $this->assertEquals(['odd-1-T'], $json0->prts->odd->note);
        $this->assertEquals(1, $json0->prts->even->score);
        $this->assertEquals(0, $json0->prts->even->penalty);
        $this->assertEquals(['even-1-T'], $json0->prts->even->note);
        $this->assertEquals(1, $json0->prts->oddeven->score);
        $this->assertEquals(0, $json0->prts->oddeven->penalty);
        $this->assertEquals(['oddeven-1-T', 'oddeven-2-T'], $json0->prts->oddeven->note);
        $this->assertEquals(1, $json0->prts->unique->score);
        $this->assertEquals(0, $json0->prts->unique->penalty);
        $this->assertEquals(['ATLogic_True.', 'unique-1-T'], $json0->prts->unique->note);
        $this->assertEquals(333333333, $json0->seed);
        $json1 = json_decode($this->report->jsonsummary[1]);
        $this->assertEquals(4, count((array)$json1->inputs));
        $this->assertEquals('x^5', $json1->inputs->ans1->value);
        $this->assertEquals('vvv', $json1->inputs->ans2->value);
        $this->assertEquals('iii', $json1->inputs->ans3->value);
        $this->assertEquals('zz', $json1->inputs->ans4->value);
        $this->assertEquals('score', $json1->inputs->ans1->status);
        $this->assertEquals('invalid', $json1->inputs->ans2->status);
        $this->assertEquals('invalid', $json1->inputs->ans3->status);
        $this->assertEquals('invalid', $json1->inputs->ans4->status);
        $this->assertEquals(1, $json1->prts->odd->score);
        $this->assertEquals(0, $json1->prts->odd->penalty);
        $this->assertEquals(['odd-1-T'], $json1->prts->odd->note);
        $this->assertEquals(null, $json1->prts->even->score);
        $this->assertEquals(null, $json1->prts->even->penalty);
        $this->assertEquals([], $json1->prts->even->note);
        $this->assertEquals(null, $json1->prts->oddeven->score);
        $this->assertEquals(null, $json1->prts->oddeven->penalty);
        $this->assertEquals([], $json1->prts->oddeven->note);
        $this->assertEquals(null, $json1->prts->unique->score);
        $this->assertEquals(null, $json1->prts->unique->penalty);
        $this->assertEquals([], $json1->prts->unique->note);
        $this->assertEquals(123456789, $json1->seed);
    }

    public function test_collate_multiple(): void {

        $this->report = $this->getMockBuilder(stack_question_report::class)
            ->onlyMethods(['run_report'])
            ->setConstructorArgs([self::$question2, 2, 1])->getMock();
        $this->report->summary = $this->summarymult2;
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
        $expected1 = "## odd (2)\n2 (100.00%); " . RESPONSEOT;
        $expected2 = "## even (2)\n1 ( 50.00%); !\n1 ( 50.00%); " . RESPONSEET;
        $expected3 = "## oddeven (2)\n1 ( 50.00%); !\n1 ( 50.00%); " . RESPONSEOET;
        $expected4 = "## unique (2)\n1 ( 50.00%); !\n1 ( 50.00%); " . RESPONSEUT;
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
        $expected1 = "## odd (2)\n2 (100.00%); # = 1\n2 (100.00%); odd-1-T\n\n";
        $expected2 = "## even (2)\n1 ( 50.00%); !\n1 ( 50.00%); # = 1\n1 ( 50.00%); even-1-T\n\n";
        $expected3 = "## oddeven (2)\n1 ( 50.00%); !\n1 ( 50.00%); # = 1\n1 ( 50.00%); oddeven-1-T\n1 ( 50.00%); oddeven-2-T\n\n";
        $expected4 = "## unique (2)\n1 ( 50.00%); !\n1 ( 50.00%); # = 1\n1 ( 50.00%); ATLogic_True.\n1 ( 50.00%); unique-1-T\n\n";
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

        $this->set_question_mult2();
        $variants = $this->report->format_variants()->variants;
        $expectedsum3 = "## odd (1)\n1 (100.00%); " . RESPONSEOT . "\n1 (100.00%); ans1:x^3; \n\n## even (1)\n1 (100.00%); " .
            RESPONSEET . "\n1 (100.00%); ans2:x^4; \n\n## oddeven (1)\n1 (100.00%); " . RESPONSEOET .
            "\n1 (100.00%); ans3:0; \n\n## unique (1)\n1 (100.00%); " . RESPONSEUT . "\n1 (100.00%); ans4:true; \n\n";
        $expectedsum1 = "## odd (1)\n1 (100.00%); " . RESPONSEOT . "\n1 (100.00%); ans1:x^3; \n\n## even (1)\n1 (100.00%); " .
            "!\n1 (100.00%); ans2:vv; \n\n## oddeven (1)\n1 (100.00%); !\n1 (100.00%); ans3:iii; \n\n## unique (1)\n1 (100.00%); " .
            "!\n1 (100.00%); ans4:zzz; \n\n";
        $expectedans3 = "## ans1 (1)\n### score\n1 (100.00%); x^3\n\n## ans2 (1)\n### score\n1 (100.00%); " .
            "x^4\n\n## ans3 (1)\n### score\n1 (100.00%); 0\n\n## ans4 (1)\n### score\n1 (100.00%); true\n\n";
        $expectedans1 = "## ans1 (1)\n### score\n1 (100.00%); x^3\n\n## ans2 (1)\n### valid\n1 (100.00%); " .
            "vv\n\n## ans3 (1)\n### invalid\n1 (100.00%); iii\n\n## ans4 (1)\n### other\n1 (100.00%); zzz\n\n";
        $this->assertEquals(333333333, $variants[0]->seed);
        $this->assertEquals($expectedsum3, $variants[0]->notessumout->sumout);
        $this->assertEquals($expectedans3, $variants[0]->anssumout);
        $this->assertEquals(123456789, $variants[1]->seed);
        $this->assertEquals($expectedsum1, $variants[1]->notessumout->sumout);
        $this->assertEquals($expectedans1, $variants[1]->anssumout);
    }

    public function test_inputs_summary_multiple(): void {

        $this->set_question_mult2();
        $inputs = $this->report->format_inputs()->inputs;
        $expected = "## ans1 (2)\n### score\n2 (100.00%); x^3\n\n## ans2 (2)\n### score\n1 ( 50.00%); " .
            "x^4\n\n### valid\n1 ( 50.00%); vv\n\n## ans3 (2)\n### score\n1 ( 50.00%); 0\n\n### invalid\n1 " .
            "( 50.00%); iii\n\n## ans4 (2)\n### score\n1 ( 50.00%); true\n\n### other\n1 ( 50.00%); zzz\n\n";
        $this->assertEquals($expected, $inputs);
    }

    public function test_raw_data_multiple(): void {

        $this->set_question_mult2();
        $rawdata = $this->report->format_raw_data()->rawdata;
        $expected = "\n# 3 (1)\n1 (100.00%); Seed: 333333333; ans1: x^3 [score]; ans2: x^4 [score]; " .
            "ans3: 0 [score]; ans4: true [score]; odd: " . RESPONSEOT . "; even: " . RESPONSEET . "; oddeven: " . RESPONSEOET .
            "; unique: " . RESPONSEUT . "\n\n# 1 (1)\n1 (100.00%); Seed: 123456789; ans1: x^3 [score]; ans2: vv [valid]; " .
            "ans3: iii [invalid]; ans4: zzz; odd: " . RESPONSEOT . "; even: !; oddeven: !; unique: !\n";
        $this->assertEquals($expected, $rawdata);
    }

    public function test_quiz_selection(): void {
        global $DB;
        $this->resetAfterTest();
        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $course = $this->getDataGenerator()->create_course();
        $contextid = \context_course::instance($course->id)->id;
        // For Moodle 5 this will be in a question bank module.
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
        $quizzes = stack_question_report::get_relevant_quizzes($q->id, $qcategory->contextid);
        $this->assertEquals(0, count($quizzes));
        $quizzes = stack_question_report::get_relevant_quizzes($q2->id, $qcategory->contextid);
        $this->assertEquals(0, count($quizzes));
        $quizzes = stack_question_report::get_relevant_quizzes($q3->id, $quiz1contextid);
        $this->assertEquals(0, count($quizzes));

        // Quiz 1: Add q1. Add q3 as part of random selection.
        \quiz_add_quiz_question($q->id, $quiz1);
        global $CFG;
        if ($CFG->version > 2023042411) {
            $this->add_random_questions($quiz1->id, 0, $quiz1qcategory->id, 1);
        } else {
            \quiz_add_random_questions($quiz1, 0, $quiz1qcategory->id, 1, false);
        }
        // Quiz 2: Add q1 and q2.
        \quiz_add_quiz_question($q->id, $quiz2);
        \quiz_add_quiz_question($q2->id, $quiz2);
        // Quiz 3: Add q1 and q2 as part of random selection.
        if ($CFG->version > 2023042411) {
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
        $quizzes = stack_question_report::get_relevant_quizzes($q->id, $qcategory->contextid);
        $quiznames = array_column($quizzes, 'name');
        $this->assertEquals(3, count($quizzes));
        $this->assertEquals(true, in_array('QUIZNAME1', $quiznames));
        $this->assertEquals(true, in_array('QUIZNAME2', $quiznames));
        $this->assertEquals(true, in_array('QUIZNAME3', $quiznames));
        $quizzes = stack_question_report::get_relevant_quizzes($q2->id, $qcategory->contextid);
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

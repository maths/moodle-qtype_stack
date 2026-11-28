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
 * Tests for bulk testing a library of question files, and for the bulk test reports.
 *
 * @package    qtype_stack
 * @copyright  2026 University of Edinburgh
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace qtype_stack;

use qtype_stack_testcase;
use stack_bulk_tester;
use stack_bulk_tester_formatter;

defined('MOODLE_INTERNAL') || die();
global $CFG;
require_once(__DIR__ . '/fixtures/test_base.php');
require_once($CFG->dirroot . '/question/type/stack/stack/bulktester.class.php');

/**
 * Tests for bulk testing a library of question files, which need the CAS.
 * @group qtype_stack
 *
 * @covers \stack_bulk_tester::class
 * @covers \stack_bulk_tester_formatter::class
 */
final class bulktester_library_test extends qtype_stack_testcase {
    /** @var string the folder used as a library in these tests. */
    private const LIBRARY = '/question/type/stack/samplequestions/importtest';

    /** @var string a folder of 18 questions in that library. */
    private const FOLDER = 'Course1/top/CR_Diff_01';

    /**
     * The absolute path of the test library.
     * @return string
     */
    private function library(): string {
        global $CFG;
        return $CFG->dirroot . self::LIBRARY;
    }

    public function test_library_validation_only(): void {
        global $DB;
        $this->setAdminUser();
        $questions = $DB->count_records('question');
        $categories = $DB->count_records('question_categories');

        $bulktester = new stack_bulk_tester();
        [$allpassed, $failing] = $bulktester->run_all_tests_for_library($this->library(), self::FOLDER, 'json', false);
        $reports = $bulktester->get_reports();

        $this->assertCount(18, $reports);
        foreach ($reports as $report) {
            $this->assertEquals('library', $report['source']);
            $this->assertNull($report['questionid']);
            $this->assertStringStartsWith(self::FOLDER . '/', $report['filename']);
            $this->assertNotEquals('', $report['name']);
            $this->assertEquals([], $report['importerrors']);
            $this->assertEquals('', $report['upgradeerrors']);
            $this->assertFalse($report['testsrun']);
            $this->assertEquals([], $report['seeds']);
        }
        $this->assertEquals([], $failing['failingimport']);
        $this->assertEquals([], $failing['failingtests']);
        $this->assertTrue($allpassed);
        // The JSON report names each question by its file in the library.
        $json = json_decode(stack_bulk_tester_formatter::to_json($allpassed, $reports), true);
        $this->assertTrue($json['allpassed']);
        $this->assertEquals(
            array_column($reports, 'filename'),
            array_column($json['questions'], 'filename')
        );

        // The temporary questions and category have gone. The default bank and its categories may remain.
        $this->assertEquals($questions, $DB->count_records('question'));
        $this->assertEquals(0, $DB->count_records_select(
            'question_categories',
            $DB->sql_like('name', ':name'),
            ['name' => 'STACK library bulk test %']
        ));
        $this->assertLessThanOrEqual($categories + 2, $DB->count_records('question_categories'));
    }

    public function test_library_run_tests(): void {
        $this->setAdminUser();
        $file = self::FOLDER . '/CR-Diff-01-basic-1-b.xml';

        $bulktester = new stack_bulk_tester();
        [$allpassed, $failing] = $bulktester->run_all_tests_for_library($this->library(), $file, 'json', true);
        $reports = $bulktester->get_reports();

        $this->assertCount(1, $reports);
        $report = $reports[0];
        $this->assertEquals($file, $report['filename']);
        $this->assertEquals($file, $report['key']);
        $this->assertTrue($report['testsrun']);
        $this->assertCount(6, $report['seeds']);
        foreach ($report['seeds'] as $seedresult) {
            $this->assertIsInt($seedresult['seed']);
            $this->assertEquals(3, $seedresult['passes'] + $seedresult['fails']);
            $this->assertTrue($seedresult['passed']);

            // Every test case is reported in full, passed or not.
            $this->assertCount(3, $seedresult['testcases']);
            foreach ($seedresult['testcases'] as $details) {
                $this->assertEquals(
                    ['testcase', 'description', 'passed', 'reason', 'inputs', 'outcomes'],
                    array_keys($details)
                );
                $this->assertIsInt($details['testcase']);
                $this->assertNotEmpty($details['inputs']);
                $this->assertNotEmpty($details['outcomes']);
                foreach ($details['outcomes'] as $outcome) {
                    foreach (['score', 'expectedscore', 'penalty', 'expectedpenalty', 'answernote', 'expectedanswernote'] as $f) {
                        $this->assertArrayHasKey($f, $outcome);
                    }
                }
            }
        }
        // The details survive the JSON.
        $json = json_decode(stack_bulk_tester_formatter::to_json($allpassed, $reports), true);
        $this->assertEquals(
            array_keys($report['seeds'][0]['testcases'][0]['outcomes']),
            array_keys($json['questions'][0]['seeds'][0]['testcases'][0]['outcomes'])
        );
        $this->assertEquals([], $failing['failingimport']);
        $this->assertEquals($report['passed'], $allpassed);
    }

    public function test_library_other_folder_and_keys(): void {
        $this->setAdminUser();
        // A folder outside STACK, as a project would use, holding one file with two questions.
        $dir = make_request_directory();
        $xml = file_get_contents($this->library() . '/' . self::FOLDER . '/CR-Diff-01-basic-1-b.xml');
        $this->assertEquals(1, preg_match('~<question type="stack">.*</question>~s', $xml, $matches));
        mkdir($dir . '/sub');
        file_put_contents(
            $dir . '/sub/two.xml',
            '<?xml version="1.0" encoding="UTF-8"?>' . "\n<quiz>\n" . $matches[0] . "\n" . $matches[0] . "\n</quiz>\n"
        );
        copy($this->library() . '/' . self::FOLDER . '/CR-Diff-01-basic-1-e.xml', $dir . '/one.xml');

        $bulktester = new stack_bulk_tester();
        // A trailing slash on the folder makes no difference.
        [$allpassed, $failing] = $bulktester->run_all_tests_for_library($dir . '/', '', 'json', false);
        $reports = $bulktester->get_reports();

        $this->assertEquals(['one.xml', 'sub/two.xml', 'sub/two.xml'], array_column($reports, 'filename'));
        $this->assertEquals(['one.xml', 'sub/two.xml#1', 'sub/two.xml#2'], array_column($reports, 'key'));
        $this->assertEquals([], $failing['failingimport']);
    }

    public function test_library_import_error(): void {
        $this->setAdminUser();
        $dir = make_request_directory();
        file_put_contents($dir . '/broken.xml', '<?xml version="1.0" encoding="UTF-8"?><quiz><question type="stack">');

        $bulktester = new stack_bulk_tester();
        [$allpassed, $failing] = $bulktester->run_all_tests_for_library($dir, '', 'json', false);
        $reports = $bulktester->get_reports();

        $this->assertFalse($allpassed);
        $this->assertCount(1, $reports);
        $this->assertEquals('broken.xml', $reports[0]['filename']);
        $this->assertFalse($reports[0]['passed']);
        $this->assertNotEmpty($reports[0]['importerrors']);
        $this->assertCount(1, $failing['failingimport']);
    }

    public function test_question_report_bank_question(): void {
        $this->setAdminUser();
        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $cat = $generator->create_question_category();
        $question = $generator->create_question('stack', 'test3', ['category' => $cat->id]);

        $bulktester = new stack_bulk_tester();
        $context = \context::instance_by_id($cat->contextid);
        $report = $bulktester->test_question_report($question->id, $question->name, $context, false);

        $this->assertEquals('bank', $report['source']);
        $this->assertEquals($question->id, $report['questionid']);
        $this->assertNull($report['filename']);
        $this->assertEquals($context->id, $report['contextid']);
        $this->assertFalse($report['testsrun']);
        $this->assertEquals([], $report['seeds']);
        $this->assertEquals([], $report['importerrors']);
    }
}

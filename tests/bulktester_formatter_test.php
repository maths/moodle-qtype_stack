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
 * Tests for the bulk test reports, their JSON and their formatting. None needs the CAS.
 *
 * @package    qtype_stack
 * @copyright  2026 University of Edinburgh
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace qtype_stack;

use stack_bulk_tester;
use stack_bulk_tester_formatter;

defined('MOODLE_INTERNAL') || die();
global $CFG;
require_once($CFG->dirroot . '/question/type/stack/stack/bulktester.class.php');

/**
 * Tests for the bulk test reports, their JSON and their formatting.
 * @group qtype_stack
 *
 * @covers \stack_bulk_tester::class
 * @covers \stack_bulk_tester_formatter::class
 */
final class bulktester_formatter_test extends \advanced_testcase {
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

    /**
     * The result of testing one seed.
     * @param int|null $seed
     * @param bool $passed
     * @param array $errors runtime errors.
     * @return array
     */
    private function seed(?int $seed, bool $passed, array $errors = []): array {
        return [
            'seed' => $seed,
            'passed' => $passed,
            'passes' => $passed ? 2 : 1,
            'fails' => $passed ? 0 : 1,
            'message' => $passed ? '2 passes and 0 failures.' : '1 passes and 1 failures.',
            'errors' => $errors,
            'failingtests' => $passed ? [] : [2 => 'prt1'],
            'testcases' => [[
                'testcase' => 2,
                'description' => 'Model answer',
                'passed' => $passed,
                'reason' => '',
                'inputs' => ['ans1' => ['inputexpression' => 'ans1', 'inputentered' => 'x^2', 'errors' => '']],
                'outcomes' => ['prt1' => [
                    'outcome' => $passed, 'score' => $passed ? 1 : 0, 'expectedscore' => 1,
                    'penalty' => 0, 'expectedpenalty' => 0,
                    'answernote' => $passed ? 'prt1-1-T' : 'prt1-1-F', 'expectedanswernote' => 'prt1-1-T',
                ]],
            ]],
        ];
    }

    public function test_new_report(): void {
        $bank = stack_bulk_tester::new_report('Q1', 42);
        $this->assertSame('42', $bank['key']);
        $this->assertEquals('bank', $bank['source']);
        $this->assertSame(42, $bank['questionid']);
        $this->assertNull($bank['filename']);
        $this->assertTrue($bank['passed']);
        $this->assertFalse($bank['testsrun']);
        $this->assertSame([], $bank['seeds']);

        $library = stack_bulk_tester::new_report('Q1', null, 'a/b.xml');
        $this->assertEquals('a/b.xml', $library['key']);
        $this->assertEquals('library', $library['source']);
        $this->assertNull($library['questionid']);
        $this->assertEquals('a/b.xml', $library['filename']);

        // Every report has the same fields, whatever its source.
        $this->assertEquals(array_keys($bank), array_keys($library));
    }

    public function test_to_json(): void {
        $report1 = stack_bulk_tester::new_report('Différentiation ∫', 42);
        $report1['testsrun'] = true;
        $report1['seeds'] = [$this->seed(null, true)];
        $report2 = stack_bulk_tester::new_report('Q2', null, 'Calculus/Differentiation/q2.xml');
        $report2['passed'] = false;
        $report2['hastodo'] = true;
        $report2['todotags'] = ['broken', 'review'];
        $report2['testsrun'] = true;
        $report2['seeds'] = [$this->seed(123, true), $this->seed(456, false, ['Division by zero.'])];

        $json = stack_bulk_tester_formatter::to_json(false, [5 => $report1, 9 => $report2], 1.5);

        // Readable as it stands: no escaped slashes or Unicode.
        $this->assertStringContainsString('"filename": "Calculus/Differentiation/q2.xml"', $json);
        $this->assertStringContainsString('Différentiation ∫', $json);

        // It decodes back to the reports, as a list.
        $decoded = json_decode($json, true);
        $this->assertSame(['allpassed', 'timetaken', 'questions'], array_keys($decoded));
        $this->assertFalse($decoded['allpassed']);
        $this->assertEquals(1.5, $decoded['timetaken']);
        $this->assertEquals([$report1, $report2], $decoded['questions']);
        $this->assertSame(42, $decoded['questions'][0]['questionid']);
        $this->assertNull($decoded['questions'][0]['seeds'][0]['seed']);
        $this->assertSame(456, $decoded['questions'][1]['seeds'][1]['seed']);
        $this->assertEquals(['prt1'], array_values($decoded['questions'][1]['seeds'][1]['failingtests']));
        $this->assertSame(['42', 'Calculus/Differentiation/q2.xml'], array_column($decoded['questions'], 'key'));

        // The full test case details come through, for a failing test as for a passing one.
        $details = $decoded['questions'][1]['seeds'][1]['testcases'][0];
        $this->assertFalse($details['passed']);
        $this->assertSame(0, $details['outcomes']['prt1']['score']);
        $this->assertSame(1, $details['outcomes']['prt1']['expectedscore']);
        $this->assertEquals('prt1-1-F', $details['outcomes']['prt1']['answernote']);
        $this->assertEquals('x^2', $details['inputs']['ans1']['inputentered']);
    }

    public function test_to_json_empty_and_invalid_utf8(): void {
        $decoded = json_decode(stack_bulk_tester_formatter::to_json(true, []), true);
        $this->assertEquals(['allpassed' => true, 'timetaken' => null, 'questions' => []], $decoded);

        // A message with invalid UTF-8 (from a broken file, say) must not lose the whole report.
        $report = stack_bulk_tester::new_report('Q', null, 'bad.xml');
        $report['importerrors'] = ["Bad byte \xB1 here."];
        $decoded = json_decode(stack_bulk_tester_formatter::to_json(false, [$report]), true);
        $this->assertCount(1, $decoded['questions']);
        $this->assertStringStartsWith('Bad byte ', $decoded['questions'][0]['importerrors'][0]);
    }

    public function test_seed_message(): void {
        $errorstring = stack_string('stackInstall_testsuite_errors');
        $this->assertEquals('2 passes and 0 failures.', stack_bulk_tester_formatter::seed_message($this->seed(1, true), 'cli'));
        $this->assertEquals(
            '1 passes and 1 failures. ' . $errorstring . ' E1 ' . $errorstring . ' E2',
            stack_bulk_tester_formatter::seed_message($this->seed(1, false, ['E1', 'E2']), 'cli')
        );
        $this->assertEquals(
            '1 passes and 1 failures.<br />' . $errorstring . ' E1',
            stack_bulk_tester_formatter::seed_message($this->seed(1, false, ['E1']), 'web')
        );
    }

    public function test_add_to_failing(): void {
        $formatter = new stack_bulk_tester_formatter('cli');
        $failing = stack_bulk_tester_formatter::empty_failing();

        $report = stack_bulk_tester::new_report('Q1', null, 'a/b.xml');
        $report['passed'] = false;
        $report['validation'] = 'Bad.';
        $report['notests'] = true;
        $report['seeds'][] = $this->seed(5, false, ['Oops']);
        $formatter->add_to_failing($failing, $report);

        $this->assertEquals(['a/b.xml: Q1 Bad.'], $failing['failingvalidation']);
        $this->assertEquals(['a/b.xml: Q1'], $failing['notests']);
        $this->assertEquals(
            ['a/b.xml: Q1 ' . stack_string('seedx', 5) . ': 1 passes and 1 failures. ' .
                stack_string('stackInstall_testsuite_errors') . ' Oops'],
            $failing['failingtests']
        );
        $this->assertEquals([], $failing['failingimport']);

        $report = stack_bulk_tester::new_report('', null, 'c.xml');
        $report['passed'] = false;
        $report['importerrors'] = ['Broken.'];
        $formatter->add_to_failing($failing, $report);
        $this->assertEquals(['c.xml: Broken.'], $failing['failingimport']);

        // A question in the bank is named by its id. Load and upgrade errors are listed together.
        $report = stack_bulk_tester::new_report('Q2', 42);
        $report['nogeneralfeedback'] = true;
        $report['nodeployedseeds'] = true;
        $report['seeds'][] = $this->seed(null, false);
        $formatter->add_to_failing($failing, $report);
        $this->assertEquals(['42: Q2'], $failing['nogeneralfeedback']);
        $this->assertEquals(['42: Q2'], $failing['nodeployedseeds']);
        $this->assertEquals('42: Q2: 1 passes and 1 failures.', end($failing['failingtests']));

        $report = stack_bulk_tester::new_report('Q3', 43);
        $report['loaderror'] = 'Cannot load.';
        $formatter->add_to_failing($failing, $report);
        $report = stack_bulk_tester::new_report('Q4', 44);
        $report['upgradeerrors'] = 'Old syntax.';
        $formatter->add_to_failing($failing, $report);
        $this->assertEquals(['43: Q3: Cannot load.', '44: Q4 Old syntax.'], $failing['failingupgrades']);

        // The keys are those the summary prints, each with a language string.
        $this->assertEquals(array_keys(stack_bulk_tester_formatter::empty_failing()), array_keys($failing));
        foreach (array_keys($failing) as $key) {
            $this->assertTrue(get_string_manager()->string_exists('stackInstall_testsuite_' . $key, 'qtype_stack'));
        }
    }

    public function test_print_question_cli(): void {
        $formatter = new stack_bulk_tester_formatter('cli');
        $report = stack_bulk_tester::new_report('Q2', 42);
        $report['seeds'] = [$this->seed(1, true), $this->seed(2, true), $this->seed(3, false)];

        $this->expectOutputString("\n42: Q2 :...");
        $formatter->print_question_start($report);
        $formatter->print_question($report);
    }

    public function test_print_question_cli_library_start(): void {
        $formatter = new stack_bulk_tester_formatter('cli');
        // Before a library file is imported, only its name is known.
        $this->expectOutputString("\na/b.xml :");
        $formatter->print_question_start(stack_bulk_tester::new_report('', null, 'a/b.xml'));
    }

    public function test_print_question_json_is_silent(): void {
        $formatter = new stack_bulk_tester_formatter('json');
        $report = stack_bulk_tester::new_report('Q2', 42);
        $report['seeds'] = [$this->seed(1, false)];

        $this->expectOutputString('');
        $formatter->print_question_start($report);
        $formatter->print_question($report);
    }

    public function test_print_question_web_escapes_library_text(): void {
        $this->resetAfterTest();
        $formatter = new stack_bulk_tester_formatter('web');
        $report = stack_bulk_tester::new_report('', null, 'a/<b>.xml');
        $report['passed'] = false;
        $report['importerrors'] = ['Tag <script> not closed.'];

        ob_start();
        $formatter->print_question($report);
        $output = ob_get_clean();
        $this->assertStringContainsString('a/&lt;b&gt;.xml', $output);
        $this->assertStringContainsString('Tag &lt;script&gt; not closed.', $output);
        $this->assertStringNotContainsString('<script>', $output);
    }

    public function test_print_cli_result(): void {
        $formatter = new stack_bulk_tester_formatter('cli');
        $failing = stack_bulk_tester_formatter::empty_failing();
        $failing['notests'] = ['42: Q2', '43: Q3'];

        ob_start();
        $formatter->print_cli_result(false, $failing);
        $output = ob_get_clean();
        $this->assertStringContainsString('** ' . stack_string('stackInstall_testsuite_fail'), $output);
        $this->assertStringContainsString(
            '* ' . stack_string('stackInstall_testsuite_notests') . "\n42: Q2\n43: Q3",
            $output
        );
        // Empty lists are not printed.
        $this->assertStringNotContainsString(stack_string('stackInstall_testsuite_failingimport'), $output);
    }

    public function test_get_library_question_files(): void {
        $files = stack_bulk_tester::get_library_question_files($this->library(), self::FOLDER);
        $this->assertCount(18, $files);
        $this->assertEquals(self::FOLDER . '/CR-Diff-01-basic-1-b.xml', $files[0]);
        $this->assertEquals(self::FOLDER . '/CR-Diff-01-basic-4-c.xml', $files[17]);
        foreach ($files as $file) {
            $this->assertStringNotContainsString('gitsync_category', $file);
        }

        // The whole library includes the quiz folder's questions, but no .json files.
        $all = stack_bulk_tester::get_library_question_files($this->library());
        $this->assertGreaterThan(18, count($all));
        $this->assertEquals($all, array_values(array_filter($all, fn($f) => substr($f, -4) === '.xml')));

        // A single file, and a trailing slash.
        $one = self::FOLDER . '/CR-Diff-01-basic-1-b.xml';
        $this->assertEquals([$one], stack_bulk_tester::get_library_question_files($this->library(), $one));
        $this->assertEquals($files, stack_bulk_tester::get_library_question_files($this->library(), self::FOLDER . '/'));
    }

    public function test_get_library_question_files_rejects_dotdot(): void {
        $this->expectException(\coding_exception::class);
        stack_bulk_tester::get_library_question_files($this->library(), self::FOLDER . '/../../..');
    }

    public function test_get_library_question_files_rejects_missing(): void {
        $this->expectException(\coding_exception::class);
        stack_bulk_tester::get_library_question_files($this->library(), 'no-such-folder');
    }
}

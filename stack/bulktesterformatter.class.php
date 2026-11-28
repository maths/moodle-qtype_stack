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
 * Formats the reports made by the bulk tester for people to read.
 *
 * The bulk tester makes one report per question ({@see stack_bulk_tester::test_question_report()})
 * and prints nothing itself. This class prints those reports, on the web or the command line,
 * and collects the lists of failing questions for the summary at the end. In 'json' mode it
 * prints nothing: the caller serialises the reports instead.
 *
 * @package    qtype_stack
 * @copyright  2026 The University of Edinburgh.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */
class stack_bulk_tester_formatter {
    /** @var string 'web', 'cli' or 'json'. */
    protected $outputmode;

    /** @var moodle_url|null the base URL of the question test page, for links on the web. */
    protected $questiontestsurl;

    /** @var context|null the context being tested, if it is in the question bank. */
    protected $context;

    /** @var int the number of progress dots printed on the current line. */
    protected $dots = 0;

    /**
     * Constructor.
     *
     * @param string $outputmode 'web', 'cli' or 'json'.
     * @param moodle_url|null $questiontestsurl the base URL of the question test page.
     * @param context|null $context the context being tested, if it is in the question bank.
     */
    public function __construct(string $outputmode, ?moodle_url $questiontestsurl = null, ?context $context = null) {
        $this->outputmode = $outputmode;
        $this->questiontestsurl = $questiontestsurl;
        $this->context = $context;
    }

    /**
     * The lists of failing questions, all empty, in the order the summary prints them.
     * Each key names the language string 'stackInstall_testsuite_' . $key.
     *
     * @return array key => list of messages.
     */
    public static function empty_failing(): array {
        return [
            'failingimport'     => [],
            'failingtests'      => [],
            'notests'           => [],
            'nogeneralfeedback' => [],
            'nodeployedseeds'   => [],
            'failingupgrades'   => [],
            'failingvalidation' => [],
        ];
    }

    /**
     * Serialise the reports on a bulk test as JSON, for machines to read.
     *
     * @param bool $allpassed whether everything passed.
     * @param array $reports as returned by {@see stack_bulk_tester::get_reports()}.
     * @param float|null $timetaken seconds the bulk test took, if known.
     * @return string JSON object with fields allpassed, timetaken and questions (the reports).
     */
    public static function to_json(bool $allpassed, array $reports, ?float $timetaken = null): string {
        return json_encode(
            ['allpassed' => $allpassed, 'timetaken' => $timetaken, 'questions' => array_values($reports)],
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE |
                JSON_THROW_ON_ERROR
        );
    }

    /**
     * The message for the result of testing one seed: the summary of passes and fails,
     * followed by any runtime errors.
     *
     * @param array $seedresult one entry of the 'seeds' field of a report.
     * @param string $outputmode 'web', 'cli' or 'json'.
     * @return string the message.
     */
    public static function seed_message(array $seedresult, string $outputmode): string {
        $message = $seedresult['message'];
        foreach ($seedresult['errors'] as $error) {
            $s = stack_string('stackInstall_testsuite_errors') . ' ' . $error;
            if ($outputmode == 'web') {
                $s = html_writer::empty_tag('br') . $s;
            } else {
                $s = ' ' . $s;
            }
            $message .= $s;
        }
        return $message;
    }

    /**
     * Print what is known about a question before it is tested: on the command line,
     * its name, so that a question which hangs can be identified.
     *
     * @param array $report a report holding at least the name, question id or file name.
     */
    public function print_question_start(array $report): void {
        if ($this->outputmode == 'cli') {
            echo "\n" . $this->label($report) . " :";
            $this->dots = 0;
        }
    }

    /**
     * Print the report on one question.
     *
     * @param array $report as returned by {@see stack_bulk_tester::test_question_report()}.
     */
    public function print_question(array $report): void {
        global $OUTPUT;

        if ($this->outputmode == 'cli') {
            foreach ($report['seeds'] as $unused) {
                echo '.';
                $this->dots += 1;
                if ($this->dots > 50) {
                    echo "\n";
                    $this->dots = 0;
                }
            }
            flush();
            return;
        }
        if ($this->outputmode != 'web') {
            return;
        }

        $label = $this->label($report);

        if ($report['importerrors']) {
            echo $OUTPUT->heading($label, 4);
            echo html_writer::tag('p', s(implode(' ', $report['importerrors'])), ['class' => 'fail']);
            return;
        }
        if ($report['loaderror'] !== '') {
            return;
        }
        if ($report['upgradeerrors'] !== '') {
            echo $OUTPUT->heading($label, 4);
            echo html_writer::tag('p', $report['upgradeerrors'], ['class' => 'fail']);
            return;
        }

        $problems = [];
        if ($report['validation'] !== '') {
            $problems[] = html_writer::tag('li', $report['validation']);
        }
        foreach (['nogeneralfeedback', 'nodeployedseeds', 'notests'] as $flag) {
            if ($report[$flag]) {
                $problems[] = html_writer::tag('li', stack_string('bulktest' . $flag));
            }
        }
        if ($problems) {
            echo $OUTPUT->heading($label, 4);
            echo html_writer::tag('ul', implode("\n", $problems));
        }

        foreach ($report['seeds'] as $i => $seedresult) {
            if ($seedresult['seed'] === null) {
                if (!$problems) {
                    echo $OUTPUT->heading($label, 4);
                }
            } else {
                if (!$problems && $i === 0) {
                    echo $OUTPUT->heading(format_string($report['name']), 4);
                }
                echo $OUTPUT->heading($this->seed_label($report, $seedresult['seed']), 4);
            }
            $flag = $seedresult['passed'] ? '* ' : '';
            echo html_writer::tag(
                'p',
                $flag . self::seed_message($seedresult, 'web'),
                ['class' => $seedresult['passed'] ? 'pass' : 'fail']
            );
        }
        flush();
    }

    /**
     * Add the problems in one report to the lists of failing questions.
     *
     * @param array $failing the lists, as made by {@see self::empty_failing()}. Updated.
     * @param array $report as returned by {@see stack_bulk_tester::test_question_report()}.
     */
    public function add_to_failing(array &$failing, array $report): void {
        $label = $this->label($report);

        if ($report['importerrors']) {
            $failing['failingimport'][] = $label . ': ' . implode(' ', $report['importerrors']);
        }
        if ($report['loaderror'] !== '') {
            $failing['failingupgrades'][] = $label . ': ' . $report['loaderror'];
        }
        if ($report['upgradeerrors'] !== '') {
            $failing['failingupgrades'][] = $label . ' ' . $report['upgradeerrors'];
        }
        if ($report['validation'] !== '') {
            $failing['failingvalidation'][] = $label . ' ' . $report['validation'];
        }
        foreach (['nogeneralfeedback', 'nodeployedseeds', 'notests'] as $flag) {
            if ($report[$flag]) {
                $failing[$flag][] = $label;
            }
        }

        foreach ($report['seeds'] as $seedresult) {
            if ($seedresult['passed']) {
                continue;
            }
            $message = self::seed_message($seedresult, $this->outputmode);
            if ($seedresult['seed'] === null) {
                $failing['failingtests'][] = $label . ': ' . $message;
            } else {
                $prefix = '';
                if ($report['source'] === 'bank' && $this->context) {
                    $prefix = $this->context->get_context_name(false, true) . ' ';
                }
                $name = ($this->outputmode == 'web') ? format_string($report['name']) : $this->plain_label($report);
                $failing['failingtests'][] = $prefix . $name . ' ' .
                    $this->seed_label($report, $seedresult['seed']) . ': ' . $message;
            }
        }
    }

    /**
     * Print the result of testing one context, or a library, on the command line.
     *
     * @param bool $passed whether everything passed.
     * @param array $failing the lists of failing questions.
     */
    public function print_cli_result(bool $passed, array $failing): void {
        echo "\n";
        echo "** " . stack_string($passed ? 'stackInstall_testsuite_pass' : 'stackInstall_testsuite_fail');
        echo "\n\n";
        foreach ($failing as $key => $messages) {
            if ($messages !== []) {
                echo "\n* " . stack_string('stackInstall_testsuite_' . $key) . "\n";
                echo implode("\n", $messages);
            }
        }
    }

    /**
     * Print an overall summary on the web, with a link back to the bulk test index.
     *
     * @param bool $allpassed whether all the tests passed.
     * @param array $failing the lists of failing questions.
     */
    public function print_overall_result($allpassed, $failing): void {
        global $OUTPUT;
        echo $OUTPUT->heading(stack_string('overallresult'), 2);
        if ($allpassed) {
            echo html_writer::tag('p', stack_string('stackInstall_testsuite_pass'), ['class' => 'overallresult pass']);
        } else {
            echo html_writer::tag('p', stack_string('stackInstall_testsuite_fail'), ['class' => 'overallresult fail']);
        }

        foreach ($failing as $key => $failarray) {
            if (!empty($failarray)) {
                echo $OUTPUT->heading(stack_string('stackInstall_testsuite_' . $key), 3);
                echo html_writer::start_tag('ul');
                foreach ($failarray as $message) {
                    echo html_writer::tag('li', $message);
                }
                echo html_writer::end_tag('ul');
            }
        }

        echo html_writer::tag('p', html_writer::link(
            new moodle_url('/question/type/stack/adminui/bulktestindex.php'),
            get_string('back')
        ));
    }

    /**
     * How a question is named in the output: a link to its test page on the web.
     *
     * @param array $report the report on the question.
     * @return string the label.
     */
    protected function label(array $report): string {
        if ($this->outputmode == 'web' && $report['source'] === 'bank' && $this->questiontestsurl) {
            return html_writer::link(
                new moodle_url($this->questiontestsurl, ['questionid' => $report['questionid']]),
                format_string($report['name'])
            );
        }
        $label = $this->plain_label($report);
        return ($this->outputmode == 'web') ? s($label) : $label;
    }

    /**
     * How a question is named in plain text: its id or file, then its name.
     *
     * @param array $report the report on the question.
     * @return string the label.
     */
    protected function plain_label(array $report): string {
        $where = ($report['source'] === 'library') ? $report['filename'] : $report['questionid'];
        if ($report['name'] === '') {
            return (string) $where;
        }
        return $where . ': ' . format_string($report['name']);
    }

    /**
     * How a seed is named in the output: a link to the question test page for that seed on the web.
     *
     * @param array $report the report on the question.
     * @param int $seed the seed.
     * @return string the label.
     */
    protected function seed_label(array $report, int $seed): string {
        $text = stack_string('seedx', $seed);
        if ($this->outputmode == 'web' && $report['source'] === 'bank' && $this->questiontestsurl) {
            return html_writer::link(
                new moodle_url($this->questiontestsurl, ['questionid' => $report['questionid'], 'seed' => $seed]),
                $text
            );
        }
        return $text;
    }
}

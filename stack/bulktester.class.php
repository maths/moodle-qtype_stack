<?php
// This file is part of Stack - http://stack.bham.ac.uk/
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
 * Helper class for running the question tests in bulk.
 *
 * @copyright  2015 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


/**
 * Class for running the question tests in bulk.
 *
 * @copyright  2015 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class stack_bulk_tester  {

    /**
     * Get all the contexts that contain at least one STACK question, with a
     * count of the number of those questions.
     *
     * @return array context id => number of STACK questions.
     */
    public function get_stack_questions_by_context() {
        global $DB;

        return $DB->get_records_sql_menu("
            SELECT ctx.id, COUNT(q.id) AS numstackquestions
              FROM {context} ctx
              JOIN {question_categories} qc ON qc.contextid = ctx.id
              JOIN {question} q ON q.category = qc.id
             WHERE q.qtype = 'stack'
          GROUP BY ctx.id, ctx.path
          ORDER BY ctx.path
        ");
    }

    /**
     * Run all the question tests for all variants of all questions belonging to
     * a given context.
     *
     * Does output as we go along.
     *
     * @param context $context the context to run the tests for.
     * @return array with two elements:
     *              bool true if all the tests passed, else false.
     *              array of messages relating to the questions with failures.
     */
    public function run_all_tests_for_context(context $context) {
        global $DB, $OUTPUT;

        // Load the necessary data.
        $categories = question_category_options(array($context));
        $categories = reset($categories);
        $questiontestsurl = new moodle_url('/question/type/stack/questiontestrun.php');
        if ($context->contextlevel == CONTEXT_COURSE) {
            $questiontestsurl->param('courseid', $context->instanceid);
        } else if ($context->contextlevel == CONTEXT_MODULE) {
            $questiontestsurl->param('cmid', $context->instanceid);
        }
        $allpassed = true;
        $failingtests = array();

        foreach ($categories as $key => $category) {
            list($categoryid) = explode(',', $key);
            echo $OUTPUT->heading($category, 3);

            $questionids = $DB->get_records_menu('question',
                    array('category' => $categoryid, 'qtype' => 'stack'), 'name', 'id,name');
            if (!$questionids) {
                continue;
            }

            echo html_writer::tag('p', stack_string('replacedollarscount', count($questionids)));

            foreach ($questionids as $questionid => $name) {
                $tests = question_bank::get_qtype('stack')->load_question_tests($questionid);
                if (!$tests) {
                    echo $OUTPUT->heading(html_writer::link(new moodle_url($questiontestsurl,
                            array('questionid' => $questionid)), format_string($name)), 4);
                    echo html_writer::tag('p', stack_string('bulktestnotests'));
                    continue;
                }

                $question = question_bank::load_question($questionid);
                $questionname = format_string($name);
                $previewurl = new moodle_url($questiontestsurl, array('questionid' => $questionid));
                if (empty($question->deployedseeds)) {
                    $questionnamelink = html_writer::link($previewurl, $questionname);
                    echo $OUTPUT->heading($questionnamelink, 4);
                    list($ok, $message) = $this->qtype_stack_test_question($question, $tests);
                    if (!$ok) {
                        $allpassed = false;
                        $failingtests[] = $questionnamelink . ': ' . $message;
                    }

                } else {
                    echo $OUTPUT->heading(format_string($name), 4);
                    foreach ($question->deployedseeds as $seed) {
                        $previewurl->param('seed', $seed);
                        $questionnamelink = html_writer::link($previewurl, stack_string('seedx', $seed));
                        echo $OUTPUT->heading($questionnamelink, 4);
                        list($ok, $message) = $this->qtype_stack_test_question($question, $tests, $seed);
                        if (!$ok) {
                            $allpassed = false;
                            $failingtests[] = $context->get_context_name(false, true) .
                                    ' ' . $questionname . ' ' . $questionnamelink . ': ' . $message;
                        }
                    }
                }
            }
        }

        return array($allpassed, $failingtests);
    }

    /**
     * Run the tests for one variant of one question and display the results.
     *
     * @param qtype_stack_question $question the question to test.
     * @param array $tests tests to run.
     * @param int|null $seed if we want to force a particular version.
     * @return array with two elements:
     *              bool true if the tests passed, else false.
     *              sring message summarising the number of passes and fails.
     */
    public function qtype_stack_test_question($question, $tests, $seed = null) {
        flush(); // Force output to prevent timeouts and to make progress clear.
        core_php_time_limit::raise(60); // Prevent PHP timeouts.
        gc_collect_cycles(); // Because PHP's default memory management is rubbish.

        // Prepare the question and a usage.
        $question = clone($question);
        $question->seed = (int) $seed;
        $quba = question_engine::make_questions_usage_by_activity('qtype_stack', context_system::instance());
        $quba->set_preferred_behaviour('adaptive');

        // Execute the tests.
        $passes = 0;
        $fails = 0;
        foreach ($tests as $key => $testcase) {
            $testresults[$key] = $testcase->test_question($quba, $question, $seed);
            if ($testresults[$key]->passed()) {
                $passes += 1;
            } else {
                $fails += 1;
            }
        }

        $flag = '';
        $ok = $fails == 0;
        if ($ok) {
            $class = 'pass';
        } else {
            $class = 'fail';
            $flag = '* ';
        }

        $message = stack_string('testpassesandfails', array('passes' => $passes, 'fails' => $fails));
        echo html_writer::tag('p', $flag.$message, array('class' => $class));

        flush(); // Force output to prevent timeouts and to make progress clear.

        return array($ok, $message);
    }

    /**
     * Print an overall summary, with a link back to the bulk test index.
     *
     * @param bool $allpassed whether all the tests passed.
     * @param array $failingtests list of the ones that failed.
     */
    public function print_overall_result($allpassed, $failingtests) {
        global $OUTPUT;
        echo $OUTPUT->heading(stack_string('overallresult'), 2);
        if ($allpassed) {
            echo html_writer::tag('p', stack_string('stackInstall_testsuite_pass'),
                    array('class' => 'overallresult pass'));
        } else {
            echo html_writer::tag('p', stack_string('stackInstall_testsuite_fail'),
                    array('class' => 'overallresult fail'));
        }

        echo $OUTPUT->heading(stack_string('stackInstall_testsuite_failures'), 3);
        echo html_writer::start_tag('ul');
        foreach ($failingtests as $message) {
            echo html_writer::tag('li', $message);
        }
        echo html_writer::end_tag('ul');

        echo html_writer::tag('p', html_writer::link(new moodle_url('/question/type/stack/bulktestindex.php'),
                get_string('back')));
    }
}

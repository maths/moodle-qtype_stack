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

// Class for running the question tests in bulk.
//
// @copyright  2015 The Open University.
// @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.

defined('MOODLE_INTERNAL') || die();

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
     * Get all the STACK questions in a particular context.
     *
     * @return array id of STACK questions.
     */
    public function get_stack_questions($categoryid) {
        global $DB;

        return $DB->get_records_menu('question',
                ['category' => $categoryid, 'qtype' => 'stack'], 'name', 'id, name');
    }

    /**
     * Run all the question tests for all variants of all questions belonging to
     * a given context.
     *
     * Does output as we go along.
     *
     * @param context $context the context to run the tests for.
     * @param string $outputmode 'web' or 'cli'. How to display results.
     * @param bool $skippreviouspasses if true, don't re-run tests where the previous
     *      result recorded in qtype_stack_qtest_results was a pass.
     * @return array with two elements:
     *              bool true if all the tests passed, else false.
     *              array of messages relating to the questions with failures.
     */
    public function run_all_tests_for_context(context $context, $outputmode = 'web', $qidstart = null,
            $skippreviouspasses = false) {
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
        $notests = array();
        $nogeneralfeedback = array();
        $nodeployedseeds = array();
        $failingupgrade = array();

        $readytostart = true;
        if ($qidstart) {
            $readytostart = false;
        }

        foreach ($categories as $key => $category) {
            $qdotoutput = 0;
            list($categoryid) = explode(',', $key);
            if ($outputmode == 'web') {
                echo $OUTPUT->heading($category, 3);
            }

            if ($skippreviouspasses) {
                // I think this is not strictly right. It will miss questions where
                // you have run tests for some deployed seeds where all tests passed,
                // but not yet run tests for some failing seeds. However, this
                // is good enough for my needs now.
                $questionids = $DB->get_records_sql_menu("
                        SELECT q.id, q.name
                          FROM {question} q
                          LEFT JOIN {qtype_stack_qtest_results} res ON res.questionid = q.id
                         WHERE q.category = ? AND q.qtype = ?
                         GROUP BY q.id, q.name
                        HAVING SUM(res.result) < COUNT(res.result) OR SUM(res.result) IS NULL
                        ", [$categoryid, 'stack']);
            } else {
                $questionids = $this->get_stack_questions($categoryid);
            }
            if (!$questionids) {
                continue;
            }

            // Do we start from a particular question id?
            if ($qidstart && array_key_exists($qidstart, $questionids)) {
                $readytostart = true;
                $qids = array_keys($questionids);
                $offset = array_search($qidstart, $qids) + 0;
                $questionids = array_slice ($questionids, $offset, null, true);
            }
            if (!$readytostart) {
                continue;
            }

            if ($outputmode == 'web') {
                echo html_writer::tag('p', stack_string('replacedollarscount', count($questionids)));
            } else {
                echo "\n\n## " . $category . "\n## ";
                echo stack_string('replacedollarscount', count($questionids)) . ' ' . "\n";
            }

            foreach ($questionids as $questionid => $name) {
                $question = question_bank::load_question($questionid);
                if ($outputmode == 'web') {
                    $questionname = format_string($name);
                    $questionnamelink = html_writer::link(new moodle_url($questiontestsurl,
                        array('questionid' => $questionid)), $name);
                } else {
                    $questionname = $questionid . ': ' . format_string($name);
                    $questionnamelink = $questionname;
                    echo "\n" . $questionnamelink . " :";
                    $qdotoutput = 0;
                }

                $upgradeerrors = $question->validate_against_stackversion();
                if ($upgradeerrors != '') {
                    if ($outputmode == 'web') {
                        echo $OUTPUT->heading($questionnamelink, 4);
                        echo html_writer::tag('p', $upgradeerrors, array('class' => 'fail'));
                    }
                    $failingupgrade[] = $questionnamelink . ' ' . $upgradeerrors;
                    $allpassed = false;
                    continue;
                }

                $questionproblems = array();
                if (trim($question->generalfeedback) === '') {
                    $nogeneralfeedback[] = $questionnamelink;
                    if ($outputmode == 'web') {
                        $questionproblems[] = html_writer::tag('li', stack_string('bulktestnogeneralfeedback'));
                    } else {
                        $questionproblems[] = stack_string('bulktestnogeneralfeedback');
                    }
                }

                if (empty($question->deployedseeds)) {
                    if ($question->has_random_variants()) {
                        $nodeployedseeds[] = $questionnamelink;;
                        if ($outputmode == 'web') {
                            $questionproblems[] = html_writer::tag('li', stack_string('bulktestnodeployedseeds'));
                        } else {
                            $questionproblems[] = stack_string('bulktestnodeployedseeds');
                        }
                    }
                }

                $tests = question_bank::get_qtype('stack')->load_question_tests($questionid);
                if (!$tests) {
                    $notests[] = $questionnamelink;
                    if ($outputmode == 'web') {
                        $questionproblems[] = html_writer::tag('li', stack_string('bulktestnotests'));
                    } else {
                        $questionproblems[] = stack_string('bulktestnotests');
                    }
                }

                if ($questionproblems !== array()) {
                    if ($outputmode == 'web') {
                        echo $OUTPUT->heading($questionnamelink, 4);
                        echo html_writer::tag('ul', implode("\n", $questionproblems));
                    }
                }

                $previewurl = new moodle_url($questiontestsurl, array('questionid' => $questionid));
                if (empty($question->deployedseeds)) {
                    if ($outputmode == 'cli') {
                        echo '.';
                        $qdotoutput += 1;
                        if ($qdotoutput > 50) {
                            echo "\n";
                            $qdotoutput = 0;
                        }
                    }
                    $this->qtype_stack_seed_cache($question, 0);
                    $questionnamelink = $questionname;
                    if ($outputmode == 'web') {
                        $questionnamelink = html_writer::link($previewurl, $questionname);
                        echo $OUTPUT->heading($questionnamelink, 4);
                    }
                    // Make sure the bulk tester is able to continue.
                    try {
                        list($ok, $message) = $this->qtype_stack_test_question($context, $questionid, $tests, $outputmode);
                    } catch (stack_exception $e) {
                        $ok = false;
                        $message = stack_string('errors') . ' : ' . $e;
                    }
                    if (!$ok) {
                        $allpassed = false;
                        $failingtests[] = $questionnamelink . ': ' . $message;
                    }
                } else {
                    if ($outputmode == 'web') {
                        echo $OUTPUT->heading(format_string($name), 4);
                    }
                    foreach ($question->deployedseeds as $seed) {
                        if ($outputmode == 'cli') {
                            echo '.';
                            $qdotoutput += 1;
                            if ($qdotoutput > 50) {
                                echo "\n";
                                $qdotoutput = 0;
                            }
                        }
                        $this->qtype_stack_seed_cache($question, $seed);
                        $previewurl->param('seed', $seed);
                        if ($outputmode == 'web') {
                            $questionnamelink = html_writer::link($previewurl, stack_string('seedx', $seed));
                        } else {
                            $questionnamelink = stack_string('seedx', $seed);
                        }
                        if ($outputmode == 'web') {
                            echo $OUTPUT->heading($questionnamelink, 4);
                        }
                        // Make sure the bulk tester is able to continue.
                        try {
                            list($ok, $message) = $this->qtype_stack_test_question($context, $questionid, $tests,
                                    $outputmode, $seed);
                        } catch (stack_exception $e) {
                            $ok = false;
                            $message = stack_string('errors') . ' : ' . $e;
                        }
                        if (!$ok) {
                            $allpassed = false;
                            $failingtests[] = $context->get_context_name(false, true) .
                                    ' ' . $questionname . ' ' . $questionnamelink . ': ' . $message;
                        }
                    }
                }
            }
        }
        $failing = array(
            'failingtests'      => $failingtests,
            'notests'           => $notests,
            'nogeneralfeedback' => $nogeneralfeedback,
            'nodeployedseeds'   => $nodeployedseeds,
            'failingupgrades'   => $failingupgrade);
        return array($allpassed, $failing);
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
    public function qtype_stack_test_question($context, $qid, $tests, $outputmode, $seed = null, $quiet = false) {
        flush(); // Force output to prevent timeouts and to make progress clear.
        core_php_time_limit::raise(60); // Prevent PHP timeouts.
        gc_collect_cycles(); // Because PHP's default memory management is rubbish.

        $question = question_bank::load_question($qid);
        if (!is_null($seed)) {
            $question->seed = (int) $seed;
        }

        $quba = question_engine::make_questions_usage_by_activity('qtype_stack', $context);
        $quba->set_preferred_behaviour('adaptive');

        // Execute the tests.
        $passes = 0;
        $fails = 0;

        foreach ($tests as $key => $testcase) {
            $testresults[$key] = $testcase->test_question($qid, $seed, $context);
            if ($testresults[$key]->passed()) {
                $passes += 1;
            } else {
                $fails += 1;
            }
        }

        $message = stack_string('testpassesandfails', array('passes' => $passes, 'fails' => $fails));
        $ok = ($fails === 0);

        // These lines are to seed the cache and to generate any runtime errors.
        $notused = $question->get_question_summary();
        $generalfeedback = $question->get_generalfeedback_castext();
        $notused = $generalfeedback->get_display_castext();

        if (!empty($question->runtimeerrors)) {
            $ok = false;
            $s = stack_string('stackInstall_testsuite_errors') . implode(' ', array_keys($question->runtimeerrors));
            if ($outputmode == 'web') {
                $s = html_writer::tag('br', $s);
            }
            $message .= $s;
        }

        $flag = '';
        if ($ok === false) {
            $class = 'fail';
        } else {
            $class = 'pass';
            $flag = '* ';
        }
        if (!$quiet && $outputmode == 'web') {
            echo html_writer::tag('p', $flag.$message, array('class' => $class));
        }

        flush(); // Force output to prevent timeouts and to make progress clear.

        return array($ok, $message);
    }

    /**
     * Instantiate the question to seed the cache.
     *
     * @param qtype_stack_question $question the question to test.
     * @param int|null $seed if we want to force a particular version.
     * @return array with two elements:
     *              bool true if the tests passed, else false.
     *              sring message summarising the number of passes and fails.
     */
    public function qtype_stack_seed_cache($question, $seed = null, $quiet = false) {
        flush(); // Force output to prevent timeouts and to make progress clear.
        core_php_time_limit::raise(60); // Prevent PHP timeouts.
        gc_collect_cycles(); // Because PHP's default memory management is rubbish.

        // Prepare the question and a usage.
        $qu = clone($question);

        // Create the question usage we will use.
        $quba = question_engine::make_questions_usage_by_activity('qtype_stack', context_system::instance());
        $quba->set_preferred_behaviour('adaptive');
        if (!is_null($seed)) {
            // This is a bit of a hack to force the question to use a particular seed,
            // even if it is not one of the deployed seeds.
            $qu->seed = (int) $seed;
        }

        $slot = $quba->add_question($qu, $qu->defaultmark);
        $quba->start_question($slot);

        // Prepare the display options.
        $options = new question_display_options();
        $options->readonly = true;
        $options->flags = question_display_options::HIDDEN;
        $options->suppressruntestslink = true;

        // Create the question text, question note and worked solutions.
        // This involves instantiation, which seeds the CAS cache in the cases when we have no tests.
        $renderquestion = $quba->render_question($slot, $options);
        $workedsolution = $qu->get_generalfeedback_castext();
        $workedsolution->get_display_castext();
        $questionote = $qu->get_question_summary();
    }

    /**
     * Print an overall summary, with a link back to the bulk test index.
     *
     * @param bool $allpassed whether all the tests passed.
     * @param array $failingtests list of the ones that failed.
     */
    public function print_overall_result($allpassed, $failing) {
        global $OUTPUT;
        echo $OUTPUT->heading(stack_string('overallresult'), 2);
        if ($allpassed) {
            echo html_writer::tag('p', stack_string('stackInstall_testsuite_pass'),
                    array('class' => 'overallresult pass'));
        } else {
            echo html_writer::tag('p', stack_string('stackInstall_testsuite_fail'),
                    array('class' => 'overallresult fail'));
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

        echo html_writer::tag('p', html_writer::link(new moodle_url('/question/type/stack/bulktestindex.php'),
                get_string('back')));
    }
}

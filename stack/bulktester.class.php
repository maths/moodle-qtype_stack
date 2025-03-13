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

defined('MOODLE_INTERNAL') || die();

/**
 * Class for running the question tests in bulk.
 *
 * @copyright  2015 The Open University.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

require_once(__DIR__ . '/../vle_specific.php');
require_once(__DIR__ . '/../../../engine/bank.php');

class stack_bulk_tester {

    /**
     * Get all the courses and their contexts from the database.
     *
     * @return array of course objects with id, contextid and name (short),
     * indexed by id
     */
    public function get_all_courses() {
        global $DB;

        return $DB->get_records_sql("
            SELECT crs.id, ctx.id as contextid, crs.shortname as name
              FROM {course} crs
              JOIN {context} ctx ON ctx.instanceid = crs.id
            WHERE ctx.contextlevel = 50
            ORDER BY name");
    }

    /**
     * Get all the contexts that contain at least one stack question, with a
     * count of the number of those questions. Only the latest version of each
     * question is counted.
     *
     * @return array context id => number of stack questions.
     */
    public function get_num_stack_questions_by_context() {
        global $DB;

        return $DB->get_records_sql_menu("
            SELECT ctx.id, COUNT(q.id) AS numstackquestions
            FROM {context} ctx
            JOIN {question_categories} qc ON qc.contextid = ctx.id
            JOIN {question_bank_entries} qbe ON qbe.questioncategoryid = qc.id
            JOIN {question_versions} qv ON qv.questionbankentryid = qbe.id
            JOIN {question} q ON qv.questionid = q.id
            WHERE q.qtype = 'stack'
            AND (qv.version = (SELECT MAX(v.version)
                                FROM {question_versions} v
                                JOIN {question_bank_entries} be ON be.id = v.questionbankentryid
                                WHERE be.id = qbe.id)
                              )
            GROUP BY ctx.id, ctx.path
            ORDER BY ctx.path
        ");
    }

    /**
     * Find all stack questions in a given category, returning only
     * the latest version of each question.
     * @param type $categoryid the id of a question category of interest
     * @return all stack question ids in any state and any version in the given
     * category. Each row in the returned list of rows has an id, name and version number.
     */
    public function stack_questions_in_category($categoryid) {
        global $DB;

        // See question/engine/bank.php around line 500, but this does not return the last version.
        $qcparams['readystatus'] = \core_question\local\bank\question_version_status::QUESTION_STATUS_READY;
        return $DB->get_records_sql_menu("
                SELECT q.id, q.name AS id2
                FROM {question} q
                JOIN {question_versions} qv ON qv.questionid = q.id
                JOIN {question_bank_entries} qbe ON qbe.id = qv.questionbankentryid
                WHERE qbe.questioncategoryid = {$categoryid}
                       AND q.parent = 0
                       AND qv.status = :readystatus
                       AND q.qtype = 'stack'
                       AND qv.version = (SELECT MAX(v.version)
                                         FROM {question_versions} v
                                         JOIN {question_bank_entries} be
                                         ON be.id = v.questionbankentryid
                                         WHERE be.id = qbe.id)", $qcparams);
    }

    /**
     * Find all stack questions in a given category with a todo block, returning only
     * the latest version of each question.
     * @param type $categoryid the id of a question category of interest
     * @return all stack question ids in any state and any version in the given
     * category. Each row in the returned list of rows has an id, name and version number.
     */
    public function stack_questions_in_category_with_todo($categoryid) {
        global $DB;

        // See question/engine/bank.php around line 500, but this does not return the last version.
        $qcparams['readystatus'] = \core_question\local\bank\question_version_status::QUESTION_STATUS_READY;
        return $DB->get_records_sql_menu("
                SELECT q.id, q.name AS id2
                FROM {question} q
                JOIN {question_versions} qv ON qv.questionid = q.id
                JOIN {question_bank_entries} qbe ON qbe.id = qv.questionbankentryid
                JOIN {qtype_stack_options} qso ON qso.questionid = q.id
                WHERE qbe.questioncategoryid = {$categoryid}
                       AND q.parent = 0
                       AND qv.status = :readystatus
                       AND q.qtype = 'stack'
                       AND qv.version = (SELECT MAX(v.version)
                                         FROM {question_versions} v
                                         JOIN {question_bank_entries} be
                                         ON be.id = v.questionbankentryid
                                         WHERE be.id = qbe.id)
                       AND (
                            q.questiontext REGEXP '[[][[]todo'
                            OR q.generalfeedback REGEXP '[[][[]todo'
                            OR qso.questionnote REGEXP '[[][[]todo'
                            OR qso.specificfeedback REGEXP '[[][[]todo'
                            OR qso.questiondescription REGEXP '[[][[]todo'
                        )", $qcparams);
    }

    /**
     * Get a list of all the categories within the supplied contextid that
     * contain stack questions in any state and any version.
     * @return an associative array mapping from category id to an object
     * with name and count fields for all question categories in the given context
     * that contain one or more stack questions.
     * The 'count' field is the number of stack questions in the given
     * category.
     */
    public function get_categories_for_context($contextid) {
        global $DB;

        return $DB->get_records_sql("
                SELECT qc.id, qc.parent, qc.name as name,
                       (SELECT count(1)
                        FROM {question} q
                        JOIN {question_versions} qv ON qv.questionid = q.id
                        JOIN {question_bank_entries} qbe ON qv.questionbankentryid = qbe.id
                        WHERE qc.id = qbe.questioncategoryid and q.qtype='stack') AS count
                FROM {question_categories} qc
                WHERE qc.contextid = :contextid
                ORDER BY qc.name",
            ['contextid' => $contextid]);
    }

    /**
     * Get all the stack questions in the given context.
     *
     * @param courseid The id of the course of interest.
     * @param includeprototypes true to include prototypes in the returned list.
     * @return array qid => question
     */
    public function get_all_stack_questions_in_context($contextid) {
        global $DB;

        return $DB->get_records_sql("
            SELECT q.id, ctx.id as contextid, qc.id as category, qc.name as categoryname, q.*, opts.*
              FROM {context} ctx
              JOIN {question_categories} qc ON qc.contextid = ctx.id
              JOIN {question_bank_entries} qbe ON qbe.questioncategoryid = qc.id
              JOIN {question_versions} qv ON qv.questionbankentryid = qbe.id
              JOIN {question} q ON q.id = qv.questionid
              WHERE (qv.version = (SELECT MAX(v.version)
                                FROM {question_versions} v
                                JOIN {question_bank_entries} be ON be.id = v.questionbankentryid
                                WHERE be.id = qbe.id and q.qtype='stack')
                              )
              AND ctx.id = :contextid
              ORDER BY name", ['contextid' => $contextid]);
    }

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
                  JOIN {question_bank_entries} qb ON qb.questioncategoryid = qc.id
                  JOIN {question_versions} qv ON qv.questionbankentryid = qb.id
                  JOIN {question} q ON q.id = qv.questionid
                WHERE q.qtype = 'stack'
                AND qv.version = (SELECT MAX(v.version)
                                  FROM {question_versions} v
                                  JOIN {question_bank_entries} be
                                    ON be.id = v.questionbankentryid
                                 WHERE be.id = qb.id)
                GROUP BY ctx.id, ctx.path
                ORDER BY ctx.path");
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
    public function run_all_tests_for_context(context $context, $categoryid = null, $outputmode = 'web', $qidstart = null,
            $skippreviouspasses = false) {
        global $DB, $OUTPUT;

        // Load the necessary data.
        $categories = $this->get_categories_for_context($context->id);
        $questiontestsurl = new moodle_url('/question/type/stack/questiontestrun.php');
        if ($context->contextlevel == CONTEXT_COURSE) {
            $questiontestsurl->param('courseid', $context->instanceid);
        } else if ($context->contextlevel == CONTEXT_MODULE) {
            $questiontestsurl->param('cmid', $context->instanceid);
        } else {
            $questiontestsurl->param('courseid', SITEID);
        }
        $numpasses = 0;
        $allpassed = true;
        $failingtests = [];
        $missinganswers = [];
        $notests = [];
        $nogeneralfeedback = [];
        $nodeployedseeds = [];
        $failingupgrade = [];

        foreach ($categories as $currentcategoryid => $nameandcount) {
            if ($categoryid !== null && $currentcategoryid != $categoryid) {
                continue;
            }
            $questions = $this->stack_questions_in_category($currentcategoryid);
            if (!$questions) {
                continue;
            }

            $readytostart = true;
            if ($qidstart) {
                $readytostart = false;
            }

            $qdotoutput = 0;
            if ($outputmode == 'web') {
                echo $OUTPUT->heading($nameandcount->name . ' (' . $nameandcount->count . ')', 3);
            }
            $questionids = $this->stack_questions_in_category($currentcategoryid);
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

            foreach ($questionids as $questionid => $name) {
                try {
                    $question = question_bank::load_question($questionid);
                } catch (Exception $e) {
                    $message = $questionid . ', ' . format_string($name) .
                        ': ' . stack_string('errors') . ' : ' . $e;
                    $allpassed = false;
                    $failingupgrade[] = $message;
                    continue;
                }

                if ($outputmode == 'web') {
                    $questionname = format_string($name);
                    $questionnamelink = html_writer::link(new moodle_url($questiontestsurl,
                        ['questionid' => $questionid]), $name);
                } else {
                    $questionname = $questionid . ': ' . format_string($name);
                    $questionnamelink = $questionname;
                    echo "\n" . $questionnamelink . " :";
                    $qdotoutput = 0;
                }

                // At this point we have no question context and so we can't possibly correctly evaluate URLs.
                $question->castextprocessor = new castext2_qa_processor(new stack_outofcontext_process());
                $upgradeerrors = $question->validate_against_stackversion($context);

                if ($upgradeerrors != '') {
                    if ($outputmode == 'web') {
                        echo $OUTPUT->heading($questionnamelink, 4);
                        echo html_writer::tag('p', $upgradeerrors, ['class' => 'fail']);
                    }
                    $failingupgrade[] = $questionnamelink . ' ' . $upgradeerrors;
                    $allpassed = false;
                    continue;
                }

                $questionproblems = [];
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
                if (!$tests && $question->inputs !== []) {
                    $notests[] = $questionnamelink;
                    if ($outputmode == 'web') {
                        $questionproblems[] = html_writer::tag('li', stack_string('bulktestnotests'));
                    } else {
                        $questionproblems[] = stack_string('bulktestnotests');
                    }
                }

                if ($questionproblems !== []) {
                    if ($outputmode == 'web') {
                        echo $OUTPUT->heading($questionnamelink, 4);
                        echo html_writer::tag('ul', implode("\n", $questionproblems));
                    }
                }

                $previewurl = new moodle_url($questiontestsurl, ['questionid' => $questionid]);
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
                        try {
                            $this->qtype_stack_seed_cache($question, $seed);
                        } catch (stack_exception $e) {
                            $ok = false;
                            $message = stack_string('errors') . ' : ' . $e;
                        }
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
        $failing = [
            'failingtests'      => $failingtests,
            'notests'           => $notests,
            'nogeneralfeedback' => $nogeneralfeedback,
            'nodeployedseeds'   => $nodeployedseeds,
            'failingupgrades'   => $failingupgrade,
        ];
        return [$allpassed, $failing];
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

        $message = stack_string('testpassesandfails', ['passes' => $passes, 'fails' => $fails]);
        $ok = ($fails === 0);

        // These lines are to seed the cache and to generate any runtime errors.
        $slot = $quba->add_question($question, $question->defaultmark);
        try {
            $quba->start_question($slot);
        } catch (stack_exception $e) {
            return [false, "Attempting to start the question threw an exception!"];
        }

        if (!$tests && $question->inputs !== []) {
            $defaulttest = self::create_default_test($question);
            $defaulttestresult = $defaulttest->test_question($qid, $seed, $context);
            if ($defaulttestresult->passed()) {
                $ok = true;
                $message = stack_string('defaulttestpass');
            } else {
                $ok = false;
                $message = stack_string('defaulttestfail');
            }
        }

        // Prepare the display options.
        $options = new question_display_options();
        $options->readonly = true;
        $options->flags = question_display_options::HIDDEN;
        $question->castextprocessor = new castext2_qa_processor($quba->get_question_attempt($slot));

        // Create the question text, question note and worked solutions.
        // This involves instantiation, which seeds the CAS cache in the cases when we have no tests.
        $renderquestion = $quba->render_question($slot, $options);
        $questionote = $question->get_question_summary();
        $generalfeedback = $question->get_generalfeedback_castext();

        $generalfeedback->get_rendered($question->castextprocessor);
        if ($generalfeedback->get_errors() != '') {
            $ok = false;
            $s = stack_string('stackInstall_testsuite_errors') . '  ' .
                stack_string('generalfeedback') . ': ' . $generalfeedback->get_errors();
            if ($outputmode == 'web') {
                $s = html_writer::tag('br', $s);
            }
            $message .= $s;
        }

        if (!empty($question->runtimeerrors)) {
            $ok = false;
            $s = stack_string('stackInstall_testsuite_errors') . ' ' .
                implode(' ', array_keys($question->runtimeerrors));
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
            echo html_writer::tag('p', $flag.$message, ['class' => $class]);
        }

        flush(); // Force output to prevent timeouts and to make progress clear.

        return [$ok, $message];
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
        try {
            $quba->start_question($slot);
        } catch (stack_exception $e) {
            return false;
        }

        // Prepare the display options.
        $options = new question_display_options();
        $options->readonly = true;
        $options->flags = question_display_options::HIDDEN;

        // Create the question text, question note and worked solutions.
        // This involves instantiation, which seeds the CAS cache in the cases when we have no tests.
        $renderquestion = $quba->render_question($slot, $options);
        $workedsolution = $qu->get_generalfeedback_castext();
        $workedsolution->get_rendered();
        $questionote = $qu->get_question_summary();

        // As we cloned the question any and all updates to the cache will not sync.
        // So let's do that ourselves.
        if ($qu->compiledcache !== $question->compiledcache) {
            $question->compiledcache = $qu->compiledcache;
        }
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
                    ['class' => 'overallresult pass']);
        } else {
            echo html_writer::tag('p', stack_string('stackInstall_testsuite_fail'),
                    ['class' => 'overallresult fail']);
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

        echo html_writer::tag('p', html_writer::link(new moodle_url('/question/type/stack/adminui/bulktestindex.php'),
                get_string('back')));
    }

    /**
     * Create a default test for a question.
     *
     * Expected score of 1 and penalty of 0 using the model answers.
     * The answer note will be set to whatever answer note the model
     * answer returns.
     *
     * Question usage by attempt needs to have already been set up
     * and the question started.
     *
     * @param object $question
     * @return stack_question_test
     */
    public static function create_default_test($question) {
        $inputs = [];
        foreach ($question->inputs as $inputname => $input) {
            $inputs[$inputname] = $input->get_teacher_answer_testcase();
        }
        $qtest = new stack_question_test(stack_string('autotestcase'), $inputs);
        $response = stack_question_test::compute_response($question, $inputs);

        foreach ($question->prts as $prtname => $prt) {
            $result = $question->get_prt_result($prtname, $response, false);
            // For testing purposes we just take the last note.
            $answernotes = $result->get_answernotes();
            $answernote = [end($answernotes)];
            // Here we hard-wire 1 mark and 0 penalty.  This is what we normally want for the
            // teacher's answer.  If the question does not give full marks to the teacher's answer then
            // the test case will fail, and the user can confirm the failing behaviour if they really intended this.
            // Normally we'd want a failing test case with the teacher's answer not getting full marks!
            $qtest->add_expected_result($prtname, new stack_potentialresponse_tree_state(
                1, true, 1, 0, '', $answernote));
        }

        return $qtest;
    }
}

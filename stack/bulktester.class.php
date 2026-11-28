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
 * @package    qtype_stack
 * @copyright  2015 The Open University.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

require_once(__DIR__ . '/../vle_specific.php');
require_once(__DIR__ . '/../../../engine/bank.php');
require_once(__DIR__ . '/bulktesterformatter.class.php');

// phpcs:ignore moodle.Commenting.MissingDocblock.Class
class stack_bulk_tester {
    /** @var array the reports of every question tested so far. */
    protected $reports = [];

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
     * @param string $categoryid the id of a question category of interest
     * @return array all stack question ids in any state and any version in the given
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
     * Find all stack questions in a quiz, returning only
     * the latest version of each question.
     * @param string $contextid the id of the quiz context
     * @return array all stack question ids in any state in the given
     * quiz. Each row in the returned list of rows has an id, name and version number.
     */
    public function stack_questions_in_quiz($contextid) {
        require_once(__DIR__ . '/questionreport.class.php');
        global $DB;
        $qcparams['contextid'] = $contextid;
        $questions = $DB->get_records_sql_menu(
            "SELECT q.id, q.name AS id2
            FROM {quiz_slots} qs
            JOIN {question_references} qr ON qr.itemid = qs.id
            JOIN {question_versions} qv ON qv.questionbankentryid = qr.questionbankentryid
            JOIN {question} q ON q.id = qv.questionid
            WHERE qr.usingcontextid = :contextid
            AND q.qtype = 'stack'
            AND qr.questionarea = 'slot'
            AND qv.version = (SELECT MAX(version)
                                FROM {question_versions}
                                WHERE questionbankentryid = qr.questionbankentryid)",
            $qcparams
        );

        // Find all the random questions for the quiz's context.
        $randomreferences = $DB->get_records_sql(
            "SELECT qr.filtercondition
            FROM {question_set_references} qr
            WHERE qr.usingcontextid = :contextid",
            $qcparams
        );

        $ids = [];
        $randomloader = new stack_random_question_loader(new qubaid_list([]));

        // Get the question id selections for each random question.
        foreach ($randomreferences as $randomref) {
            $currentids = [];
            $filter = json_decode($randomref->filtercondition, true);
            if (isset($filter['filter'])) {
                $filter = $filter['filter'];
                $currentids = $randomloader->get_filtered_question_ids($filter);
            } else if ($filter['questioncategoryid']) {
                // This is for Moodle 4.0, 4.1.
                $currentids = $randomloader->get_question_ids(
                    $filter['questioncategoryid'],
                    $filter['includingsubcategories'],
                    (isset($filter['tags'])) ? $filter['tags'] : []
                );
            }
            foreach ($currentids as $id) {
                // Avoid repeats from other random questions or the non-random questions.
                if (!in_array($id, $ids) && !isset($questions[$id])) {
                    $ids[] = $id;
                }
            }
        }

        if ($ids) {
            // We only have ids for random selection. Need to fetch names as well.
            [$dsql, $dparam] = $DB->get_in_or_equal($ids, SQL_PARAMS_NAMED);
            $randomquestions = $DB->get_records_sql_menu(
                "SELECT q.id, q.name AS id2
                FROM {question} q
                WHERE q.qtype = 'stack'
                AND q.id {$dsql}",
                $dparam
            );

            $questions = $questions + $randomquestions;
        }

        return $questions;
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
        // ISS1419 - REGEX failing in Postgres DBs. Change to LIKE as query is simple and let Moodle generate fragments.
        $qtextlike = $DB->sql_like('q.questiontext', ':search1');
        $feedbacklike = $DB->sql_like('q.generalfeedback', ':search2');
        $qnotelike = $DB->sql_like('qso.questionnote', ':search3');
        $specificfeedbacklike = $DB->sql_like('qso.specificfeedback', ':search4');
        $qdescriptionlike = $DB->sql_like('qso.questiondescription', ':search5');
        for ($i = 1; $i < 6; $i++) {
            $qcparams['search' . $i] = '%\\[\\[todo%';
        }
        return $DB->get_records_sql_menu("
                SELECT q.id, q.name AS id2, qso.isbroken
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
                            {$qtextlike}
                            OR {$feedbacklike}
                            OR {$qnotelike}
                            OR {$specificfeedbacklike}
                            OR {$qdescriptionlike}
                            OR qso.isbroken = 1
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

        return $DB->get_records_sql(
            "SELECT qc.id, qc.parent, qc.name as name,
                       (SELECT count(1)
                        FROM {question} q
                        JOIN {question_versions} qv ON qv.questionid = q.id
                        JOIN {question_bank_entries} qbe ON qv.questionbankentryid = qbe.id
                        WHERE qc.id = qbe.questioncategoryid and q.qtype='stack') AS count
                FROM {question_categories} qc
                WHERE qc.contextid = :contextid
                ORDER BY qc.name",
            ['contextid' => $contextid]
        );
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
     * Each question is tested by {@see self::test_question_report()}, which returns a
     * report and prints nothing. The formatter for the output mode then prints it.
     * The reports are also kept: see {@see self::get_reports()}.
     *
     * @param context $context the context to run the tests for.
     * @param int|null $categoryid if set, only test the questions in this category.
     * @param string $outputmode 'web', 'cli' or 'json'. How to display results ('json' displays nothing).
     * @param int|null $qidstart if set, start from the question with this id.
     * @param bool $skippreviouspasses if true, don't re-run tests where the previous
     *      result recorded in qtype_stack_qtest_results was a pass.
     * @param bool $addtodo will add [[todo]] blocks to the description.
     * @param bool $isquiz will test questions that are in the quiz.
     * @return array with two elements:
     *              bool true if all the tests passed, else false.
     *              array of messages relating to the questions with failures.
     */
    public function run_all_tests_for_context(
        context $context,
        $categoryid = null,
        $outputmode = 'web',
        $qidstart = null,
        $skippreviouspasses = false,
        $addtodo = false,
        $isquiz = false
    ) {
        global $OUTPUT;

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
        $formatter = new stack_bulk_tester_formatter($outputmode, $questiontestsurl, $context);
        $timenow = date("Ymd");
        $allpassed = true;
        $failing = stack_bulk_tester_formatter::empty_failing();

        $readytostart = true;
        if ($qidstart) {
            $readytostart = false;
        }

        // Make a list of the questions to test, category by category.
        $questionlists = [];
        if ($isquiz) {
            $questionlists[] = [null, $this->stack_questions_in_quiz($context->id)];
        } else {
            foreach ($categories as $currentcategoryid => $nameandcount) {
                if ($categoryid !== null && $currentcategoryid != $categoryid) {
                    continue;
                }
                $questionlists[] = [$nameandcount, $this->stack_questions_in_category($currentcategoryid)];
            }
        }

        foreach ($questionlists as [$nameandcount, $questionids]) {
            if (!$questionids) {
                continue;
            }
            if ($nameandcount !== null && $outputmode == 'web') {
                echo $OUTPUT->heading($nameandcount->name . ' (' . $nameandcount->count . ')', 3);
            }

            // Do we start from a particular question id?
            if ($qidstart && array_key_exists($qidstart, $questionids)) {
                $readytostart = true;
                $qids = array_keys($questionids);
                $offset = array_search($qidstart, $qids) + 0;
                $questionids = array_slice($questionids, $offset, null, true);
            }
            if (!$readytostart) {
                continue;
            }

            foreach ($questionids as $questionid => $name) {
                $formatter->print_question_start(self::new_report($name, $questionid));
                $report = $this->test_question_report($questionid, $name, $context, true, $addtodo, $timenow);
                $this->reports[] = $report;
                $formatter->print_question($report);
                $formatter->add_to_failing($failing, $report);
                $allpassed = $allpassed && $report['passed'];
            }
        }

        return [$allpassed, $failing];
    }

    /**
     * Import every question file in (part of) a library of question files, and bulk test
     * each question exactly as {@see self::run_all_tests_for_context()} tests a question
     * in the question bank.
     *
     * Each file is imported into a temporary question category, tested, and deleted again
     * before the next file is imported. The temporary category is deleted at the end.
     *
     * @param string $librarydir absolute path of the library, e.g. samplequestions/stacklibrary.
     * @param string $subpath only test files below this path within the library ('' for all).
     * @param string $outputmode 'web', 'cli' or 'json'. How to display results ('json' displays nothing).
     * @param bool $runtests if false, only import and validate (PHP validation and upgrade checks),
     *      without running any question tests.
     * @return array with two elements:
     *              bool true if all the tests passed, else false.
     *              array of messages relating to the questions with failures.
     */
    public function run_all_tests_for_library(
        string $librarydir,
        string $subpath = '',
        string $outputmode = 'cli',
        bool $runtests = true
    ): array {
        global $DB;

        $librarydir = rtrim(str_replace('\\', '/', $librarydir), '/');
        $files = self::get_library_question_files($librarydir, $subpath);
        $formatter = new stack_bulk_tester_formatter($outputmode);
        $allpassed = true;
        $failing = stack_bulk_tester_formatter::empty_failing();

        $category = self::create_temporary_category();
        $context = context::instance_by_id($category->contextid);
        try {
            foreach ($files as $file) {
                core_php_time_limit::raise(60);
                $formatter->print_question_start(self::new_report('', null, $file));
                [$questionids, $importerrors, $importwarnings] = self::import_question_file(
                    $librarydir . '/' . $file,
                    $category
                );

                $reports = [];
                if ($importerrors || !$questionids) {
                    $report = self::new_report('', null, $file);
                    $report['passed'] = false;
                    $report['importerrors'] = $importerrors ?: [stack_string('bulktestlibrarynoquestions')];
                    $report['importwarnings'] = $importwarnings;
                    $reports[] = $report;
                }
                foreach (array_values($questionids) as $position => $questionid) {
                    $question = $DB->get_record('question', ['id' => $questionid], 'id, name, qtype', MUST_EXIST);
                    if ($question->qtype !== 'stack') {
                        $report = self::new_report($question->name, null, $file);
                        $report['importwarnings'] = array_merge(
                            $importwarnings,
                            [stack_string('bulktestlibrarynotstack', $question->qtype)]
                        );
                    } else {
                        $report = $this->test_question_report($questionid, $question->name, $context, $runtests);
                        $report['importwarnings'] = $importwarnings;
                    }
                    // The question is deleted next: report where it came from instead.
                    $report['source'] = 'library';
                    $report['questionid'] = null;
                    $report['filename'] = $file;
                    $report['key'] = (count($questionids) > 1) ? $file . '#' . ($position + 1) : $file;
                    $reports[] = $report;
                }
                self::delete_questions($questionids);

                foreach ($reports as $report) {
                    $this->reports[] = $report;
                    $formatter->print_question($report);
                    $formatter->add_to_failing($failing, $report);
                    $allpassed = $allpassed && $report['passed'];
                }
            }
        } finally {
            self::delete_temporary_category($category);
        }

        return [$allpassed, $failing];
    }

    /**
     * The reports made by every question tested so far by this bulk tester.
     *
     * @return array of reports, as returned by {@see self::test_question_report()}.
     */
    public function get_reports(): array {
        return $this->reports;
    }

    /**
     * An empty report on one question, which has passed everything.
     *
     * @param string $name the name of the question.
     * @param int|null $questionid the Moodle id of a question in the question bank.
     * @param string|null $filename the path, within the library, of a question imported temporarily.
     * @return array the report. See {@see self::test_question_report()} for its fields.
     */
    public static function new_report(string $name, ?int $questionid = null, ?string $filename = null): array {
        return [
            'key'               => ($filename === null) ? (string) $questionid : $filename,
            'source'            => ($filename === null) ? 'bank' : 'library',
            'questionid'        => $questionid,
            'filename'          => $filename,
            'name'              => $name,
            'contextid'         => null,
            'passed'            => true,
            'importerrors'      => [],
            'importwarnings'    => [],
            'loaderror'         => '',
            'upgradeerrors'     => '',
            'validation'        => '',
            'isbroken'          => false,
            'nogeneralfeedback' => false,
            'nodeployedseeds'   => false,
            'notests'           => false,
            'hastodo'           => false,
            'todotags'          => [],
            'testsrun'          => false,
            'seeds'             => [],
        ];
    }

    /**
     * Test one question, and report the results. This prints nothing: the report is
     * formatted for people by {@see stack_bulk_tester_formatter}, or serialised as JSON.
     *
     * The report is an array with these fields.
     *  - key: unique within a run. For a question in the bank, its id. For a library file, its path
     *    within the library, followed by '#' and the question's position in the file (from 1) if the
     *    file holds more than one question.
     *  - source: 'bank' for a question in the question bank, 'library' for a library file
     *    imported temporarily.
     *  - questionid: the Moodle id of a question in the bank (null for a library question).
     *  - filename: the path of the file within the library (null for a question in the bank).
     *  - name: the name of the question.
     *  - contextid: the context the question was tested in.
     *  - passed: false if the question failed to import, load, upgrade or pass its tests.
     *    Validation warnings, and missing tests, feedback or seeds, do not make it fail.
     *  - importerrors, importwarnings: messages from importing a library file.
     *  - loaderror: why the question could not be loaded ('' if it was).
     *  - upgradeerrors: errors from {@see qtype_stack_question::validate_against_stackversion()}.
     *    If there are any, nothing else is checked.
     *  - validation: warnings from the PHP validation for bulk testing.
     *  - isbroken, nogeneralfeedback, nodeployedseeds, notests: flags.
     *  - hastodo, todotags: whether the question has [[todo]] blocks, and their tags.
     *  - testsrun: whether the question tests were run.
     *  - seeds: one entry per seed tested (a single entry with seed null if the question has
     *    no deployed seeds), each with fields seed, passed, passes, fails, message (the
     *    summary of passes and fails), errors (runtime errors), failingtests
     *    (test case number => reason), and testcases (the full details of every test case run,
     *    passed or not, including the default test when there are no test cases).
     *
     * @param int $questionid the id of the question to test.
     * @param string $name the name of the question.
     * @param context $context the context to run the tests for.
     * @param bool $runtests if false, only validate the question, without running its tests.
     * @param bool $addtodo will add [[todo]] blocks to the description.
     * @param string|null $timenow the current date in Ymd format, used in [[todo]] tags.
     * @return array the report.
     */
    public function test_question_report(
        $questionid,
        $name,
        context $context,
        bool $runtests = true,
        bool $addtodo = false,
        ?string $timenow = null
    ): array {
        $timenow = $timenow ?? date("Ymd");
        $report = self::new_report($name, (int) $questionid);
        $report['contextid'] = $context->id;

        try {
            $question = question_bank::load_question($questionid);
        } catch (Exception $e) {
            $report['passed'] = false;
            $report['loaderror'] = stack_string('errors') . ' : ' . $e;
            if ($addtodo) {
                $this->update_descriptions_todo(
                    $questionid,
                    'error_loading',
                    $questionid . ', ' . format_string($name) . ': ' . $report['loaderror']
                );
            }
            return $report;
        }

        // At this point we have no question context and so we can't possibly correctly evaluate URLs.
        $question->castextprocessor = new castext2_qa_processor(new stack_outofcontext_process());
        $report['upgradeerrors'] = $question->validate_against_stackversion($context);
        if ($report['upgradeerrors'] != '') {
            if ($addtodo) {
                $this->update_descriptions_todo($questionid, $timenow . ',upgrade_error', $report['upgradeerrors']);
                // The TODO system only works on cached values, which the above method just cleared.
                $question->get_cached('questiondescription');
            }
            $report['passed'] = false;
            return $report;
        }

        $report['validation'] = trim($question->validate_for_bulk($context) . ' ' .
            $question->validate_stackversion_newer());
        if ($addtodo && $report['validation'] !== '') {
            $this->update_descriptions_todo($questionid, $timenow . ',validation', $report['validation']);
            $question->get_cached('questiondescription');
        }

        [$report['hastodo'], $report['todotags']] = $question->get_question_todos();
        $report['isbroken'] = !empty($question->isbroken);
        $report['nogeneralfeedback'] = (trim($question->generalfeedback) === '');

        if (empty($question->deployedseeds) && $question->has_random_variants()) {
            $report['nodeployedseeds'] = true;
            if ($addtodo) {
                $this->update_descriptions_todo($questionid, $timenow . ',no_seeds', '');
                $question->get_cached('questiondescription');
            }
        }

        $tests = question_bank::get_qtype('stack')->load_question_tests($questionid);
        if (!$tests && $question->inputs !== []) {
            $report['notests'] = true;
            if ($addtodo) {
                $this->update_descriptions_todo($questionid, $timenow . ',no_tests', '');
                $question->get_cached('questiondescription');
            }
        }

        if (!$runtests || $question->isbroken) {
            return $report;
        }

        $report['testsrun'] = true;
        if (empty($question->deployedseeds)) {
            $seeds = [null];
        } else {
            $seeds = $question->deployedseeds;
        }
        foreach ($seeds as $seed) {
            try {
                $this->qtype_stack_seed_cache($question, $seed ?? 0);
                // Make sure the bulk tester is able to continue.
                $seedresult = $this->run_question_tests($context, $questionid, $tests, $seed);
            } catch (stack_exception $e) {
                $seedresult = [
                    'seed' => is_null($seed) ? null : (int) $seed, 'passed' => false, 'passes' => 0, 'fails' => 0,
                    'message' => stack_string('errors') . ' : ' . $e, 'errors' => [], 'failingtests' => [], 'testcases' => [],
                ];
            }
            $report['seeds'][] = $seedresult;
            if (!$seedresult['passed']) {
                if ($addtodo) {
                    $this->update_descriptions_todo(
                        $questionid,
                        $timenow . ',question_tests',
                        stack_bulk_tester_formatter::seed_message($seedresult, 'cli')
                    );
                    $question->get_cached('questiondescription');
                }
                $report['passed'] = false;
            }
        }

        return $report;
    }

    /**
     * Run the tests for one variant of one question and display the results.
     *
     * @param context $context the context to run the tests in.
     * @param int $qid the id of the question to test.
     * @param array $tests tests to run.
     * @param string $outputmode 'web' or 'cli'.
     * @param int|null $seed if we want to force a particular version.
     * @param bool $quiet if true, do not echo the result in web mode.
     * @return array with two elements:
     *              bool true if the tests passed, else false.
     *              sring message summarising the number of passes and fails.
     */
    public function qtype_stack_test_question($context, $qid, $tests, $outputmode, $seed = null, $quiet = false) {
        $result = $this->run_question_tests($context, $qid, $tests, $seed);
        $ok = $result['passed'];
        $message = stack_bulk_tester_formatter::seed_message($result, $outputmode);

        if (!$quiet && $outputmode == 'web') {
            $flag = $ok ? '* ' : '';
            echo html_writer::tag('p', $flag . $message, ['class' => $ok ? 'pass' : 'fail']);
        }

        flush(); // Force output to prevent timeouts and to make progress clear.

        return [$ok, $message];
    }

    /**
     * Run the tests for one variant of one question, printing nothing.
     *
     * @param context $context the context to run the tests in.
     * @param int $qid the id of the question to test.
     * @param array $tests tests to run.
     * @param int|null $seed if we want to force a particular version.
     * @return array with fields seed, passed, passes, fails, message (the summary of passes and fails,
     *      or of the default test), errors (runtime errors), failingtests (test case => reason) and
     *      testcases (the full details of every test case run, passed or not: {@see self::test_case_details()}).
     */
    public function run_question_tests($context, $qid, $tests, $seed = null): array {
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
        $result = [
            'seed' => is_null($seed) ? null : (int) $seed,
            'passed' => true,
            'passes' => 0,
            'fails' => 0,
            'message' => '',
            'errors' => [],
            'failingtests' => [],
            'testcases' => [],
        ];

        foreach ($tests as $key => $testcase) {
            $testresult = $testcase->test_question($qid, $seed, $context);
            $reasons = $testresult->passed_with_reasons();
            $result['testcases'][] = self::test_case_details($key, $testcase, $testresult->passed(), $reasons);
            if ($testresult->passed()) {
                $result['passes'] += 1;
            } else {
                $result['fails'] += 1;
                $result['failingtests'][$key] = self::failure_reason($reasons);
            }
        }

        $result['message'] = stack_string(
            'testpassesandfails',
            ['passes' => $result['passes'], 'fails' => $result['fails']]
        );
        $result['passed'] = ($result['fails'] === 0);

        // These lines are to seed the cache and to generate any runtime errors.
        $slot = $quba->add_question($question, $question->defaultmark);
        try {
            $quba->start_question($slot);
        } catch (stack_exception $e) {
            $result['passed'] = false;
            $result['message'] = "Attempting to start the question threw an exception!";
            return $result;
        }

        if (!$tests && $question->inputs !== []) {
            $defaulttest = self::create_default_test($question);
            $defaulttestresult = $defaulttest->test_question($qid, $seed, $context);
            $reasons = $defaulttestresult->passed_with_reasons();
            $result['testcases'][] = self::test_case_details(null, $defaulttest, $defaulttestresult->passed(), $reasons);
            if ($defaulttestresult->passed()) {
                $result['passed'] = true;
                $result['message'] = stack_string('defaulttestpass');
            } else {
                $result['passed'] = false;
                $result['message'] = stack_string('defaulttestfail');
                $result['failingtests'][stack_string('autotestcase')] = self::failure_reason($reasons);
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
            $result['passed'] = false;
            $result['errors'][] = stack_string('generalfeedback') . ': ' . $generalfeedback->get_errors();
        }

        if (!empty($question->runtimeerrors)) {
            $result['passed'] = false;
            $result['errors'][] = implode(' ', array_keys($question->runtimeerrors));
        }

        return $result;
    }

    /**
     * The full details of the result of one test case, for the report.
     *
     * @param int|null $testcase the number of the test case, or null for the default test made from the model answers.
     * @param stack_question_test $test the test case.
     * @param bool $passed whether it passed.
     * @param array $reasons as returned by {@see stack_question_test_result::passed_with_reasons()}.
     * @return array with fields testcase, description, passed, reason (why it failed, if it did),
     *      inputs (input name => what was entered, how it was displayed, its status and errors)
     *      and outcomes (PRT name => the actual and expected score, penalty and answer note, and more).
     */
    private static function test_case_details(?int $testcase, stack_question_test $test, bool $passed, array $reasons): array {
        return [
            'testcase' => $testcase,
            'description' => (string) $test->description,
            'passed' => $passed,
            'reason' => $reasons['reason'],
            'inputs' => $reasons['inputs'],
            'outcomes' => $reasons['outcomes'],
        ];
    }

    /**
     * Summarise why a test case failed.
     *
     * @param array $reasons as returned by {@see stack_question_test_result::passed_with_reasons()}.
     * @return string the reason, or the names of the PRTs whose outcome was wrong.
     */
    private static function failure_reason(array $reasons): string {
        if ($reasons['reason'] !== '') {
            return $reasons['reason'];
        }
        $prts = [];
        foreach ($reasons['outcomes'] as $prtname => $outcome) {
            if (!$outcome['outcome']) {
                $prts[] = $prtname . ($outcome['reason'] ? ' (' . $outcome['reason'] . ')' : '');
            }
        }
        return implode(', ', $prts);
    }

    /**
     * Instantiate the question to seed the cache.
     *
     * @param qtype_stack_question $question the question to test.
     * @param int|null $seed if we want to force a particular version.
     * @param bool $quiet not used.
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
     * @param array $failing list of the ones that failed.
     */
    public function print_overall_result($allpassed, $failing) {
        $formatter = new stack_bulk_tester_formatter('web');
        $formatter->print_overall_result($allpassed, $failing);
    }

    /**
     * List the question files in a library of question files: every .xml file except
     * the gitsync category files, sorted by path.
     *
     * @param string $librarydir absolute path of the library.
     * @param string $subpath only list files below this path within the library ('' for all).
     * @return string[] paths of the files, relative to $librarydir.
     */
    public static function get_library_question_files(string $librarydir, string $subpath = ''): array {
        $librarydir = rtrim(str_replace('\\', '/', $librarydir), '/');
        $subpath = trim(str_replace('\\', '/', $subpath), '/');
        if (in_array('..', explode('/', $subpath))) {
            throw new coding_exception('The path within the library may not contain "..".');
        }
        $start = ($subpath === '') ? $librarydir : $librarydir . '/' . $subpath;
        if (is_file($start)) {
            return [$subpath];
        }
        if (!is_dir($start)) {
            throw new coding_exception('Not a file or directory in the library: ' . $start);
        }

        $files = [];
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($start, FilesystemIterator::SKIP_DOTS)
        );
        foreach ($iterator as $fileinfo) {
            $filename = $fileinfo->getFilename();
            if (
                !$fileinfo->isFile() || strtolower($fileinfo->getExtension()) !== 'xml' ||
                    strpos($filename, 'gitsync_category') !== false || substr($filename, 0, 1) === '.'
            ) {
                continue;
            }
            $path = str_replace('\\', '/', $fileinfo->getPathname());
            $files[] = substr($path, strlen($librarydir) + 1);
        }
        sort($files, SORT_STRING);
        return $files;
    }

    /**
     * Create a temporary question category to import library questions into.
     *
     * On Moodle 5.0 and later it is in the site's default question bank, created if need be.
     * Before Moodle 5.0 it is in the site course's question bank.
     *
     * @return stdClass the new category.
     */
    public static function create_temporary_category(): stdClass {
        global $CFG, $DB;
        require_once($CFG->libdir . '/questionlib.php');

        $helper = '\core_question\local\bank\question_bank_helper';
        if (class_exists($helper) && method_exists($helper, 'get_default_open_instance_system_type')) {
            $cm = $helper::get_default_open_instance_system_type(get_site(), true);
            $context = context_module::instance($cm->id);
            $parent = question_get_default_category($context->id, true);
        } else {
            $context = context_course::instance(SITEID);
            $parent = question_make_default_categories([$context]);
        }

        $category = new stdClass();
        $category->name = stack_string('bulktestlibrarycategory', date('Y-m-d H:i:s'));
        $category->info = '';
        $category->infoformat = FORMAT_HTML;
        $category->contextid = $context->id;
        $category->parent = $parent->id;
        $category->sortorder = 999;
        $category->stamp = make_unique_id_code();
        $category->id = $DB->insert_record('question_categories', $category);
        return $category;
    }

    /**
     * Delete a temporary question category, with any questions still in it.
     *
     * @param stdClass $category the category made by {@see self::create_temporary_category()}.
     */
    public static function delete_temporary_category(stdClass $category): void {
        global $DB;
        $questionids = $DB->get_fieldset_sql(
            "SELECT qv.questionid
               FROM {question_versions} qv
               JOIN {question_bank_entries} qbe ON qbe.id = qv.questionbankentryid
              WHERE qbe.questioncategoryid = ?",
            [$category->id]
        );
        self::delete_questions($questionids);
        $DB->delete_records('question_categories', ['id' => $category->id]);
    }

    /**
     * Delete questions.
     *
     * @param int[] $questionids the ids of the questions.
     */
    private static function delete_questions(array $questionids): void {
        global $CFG;
        require_once($CFG->libdir . '/questionlib.php');
        foreach ($questionids as $questionid) {
            question_delete_question($questionid);
        }
    }

    /**
     * Import a Moodle XML file into a question category, as the question library does.
     *
     * Anything the import prints is captured: as errors if the import failed, otherwise as warnings.
     *
     * @param string $filepath absolute path of the file.
     * @param stdClass $category the category to import into.
     * @return array with three elements: the ids of the questions imported, the import errors,
     *      and the import warnings.
     */
    public static function import_question_file(string $filepath, stdClass $category): array {
        global $CFG;
        require_once($CFG->dirroot . '/question/format.php');
        require_once($CFG->dirroot . '/question/format/xml/format.php');

        $qformat = new qformat_xml();
        $qformat->set_display_progress(false);
        $qformat->setCourse(get_site());
        $qformat->setCategory($category);
        // Every question goes into the temporary category, whatever the file says.
        $qformat->setCatfromfile(false);
        $qformat->setContextfromfile(false);
        $qformat->setStoponerror(true);
        $qformat->setContexts([context::instance_by_id($category->contextid)]);
        $qformat->setFilename($filepath);

        $ok = false;
        $errors = [];
        ob_start();
        try {
            $ok = $qformat->importpreprocess() && $qformat->importprocess() && $qformat->importpostprocess();
        } catch (Throwable $e) {
            $errors[] = get_class($e) . ': ' . $e->getMessage();
        }
        $output = trim(html_to_text(ob_get_clean(), 0, false));

        $questionids = $ok ? array_map('intval', $qformat->questionids) : [];
        $warnings = [];
        if ($output !== '') {
            if ($ok) {
                $warnings[] = $output;
            } else {
                $errors[] = $output;
            }
        }
        if (!$ok && !$errors) {
            $errors[] = stack_string('bulktestlibraryimportfailed');
        }
        return [$questionids, $errors, $warnings];
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
            // Here we hard-wire 1 mark and 0 penalty.  This is what we normally want for the
            // teacher's answer.  If the question does not give full marks to the teacher's answer then
            // the test case will fail, and the user can confirm the failing behaviour if they really intended this.
            // Normally we'd want a failing test case with the teacher's answer not getting full marks!
            $qtest->add_expected_result($prtname, new stack_potentialresponse_tree_state(
                1,
                true,
                1,
                0,
                '',
                $result->get_answernotes_testcase()
            ));
        }

        return $qtest;
    }

    /**
     * Update the question descriptions with "todo" blocks.
     */
    private function update_descriptions_todo($questionid, $tags, $message) {
        global $DB;
        $description = $DB->get_field(
            'qtype_stack_options',
            'questiondescription',
            ['questionid' => $questionid]
        );
        // Do not update the description if we already have a todo with an bulktest tag unresolved.
        // This prevents build-up with multiple runs.
        if (substr_count($description, '[[todo tags="bulktest') === 0) {
            $message = '[[todo tags="bulktest-' . $tags . '"]]' . $message . '[[/todo]]';
            $DB->set_field(
                'qtype_stack_options',
                'questiondescription',
                $description . $message,
                ['questionid' => $questionid]
            );
            $DB->set_field('qtype_stack_options', 'compiledcache', null, ['questionid' => $questionid]);
            // Invalidate the question definition cache.
            stack_clear_vle_question_cache($questionid);
        }
    }
}

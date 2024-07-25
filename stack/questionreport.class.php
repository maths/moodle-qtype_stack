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

// Loads and manipulates data for display on the response analysis page.
//
// @copyright 2024 University of Edinburgh.
// @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.

class stack_question_report {
    /**
     * @var object Question being analysed.
     */
    public $question;

    /**
     * @var int Context of quiz being analysed in this report.
     */
    public $quizcontextid;

    /**
     * @var int Context of course containing the quiz being analysed in this report.
     */
    public $coursecontextid;
    public $summary;
    public $questionseeds;
    public $questionnotes;

    public $inputreport = [];
    // The inputreportsummary is used to store inputs, regardless of variant.
    // Multi-part questions may have inputs which are not subject to randomisation.
    public $inputreportsummary = [];
    public $prtreport = [];
    public $prtreportinputs = [];
    // Create a summary of the data without different variants.
    public $prtreportsummary = [];
    public $notesummary = [];

    /**
     * Constructor
     */
    public function __construct(object $question, int $quizcontextid, int $coursecontextid) {
        $this->question = $question;
        $this->quizcontextid = $quizcontextid;
        $this->coursecontextid = $coursecontextid;
        $this->create_summary();
        $this->match_variants_and_notes();
        $this->collate();
        $this->reports_sort();
    }

    public function create_summary():void {
        $result = $this->load_summary_data();
        $summary = [];
        foreach ($result as $qattempt) {
            if (!array_key_exists($qattempt->variant, $summary)) {
                $summary[$qattempt->variant] = [];
            }
            $rsummary = trim($qattempt->responsesummary ?? '');
            if ($rsummary !== '') {
                if (array_key_exists($rsummary, $summary[$qattempt->variant])) {
                    $summary[$qattempt->variant][$rsummary] += 1;
                } else {
                    $summary[$qattempt->variant][$rsummary] = 1;
                }
            }
        }

        foreach ($summary as $vkey => $variant) {
            arsort($variant);
            $summary[$vkey] = $variant;
        }

        $this->summary = $summary;
    }

    public function load_summary_data():array {
        global $DB;
        $params = ['coursecontextid' => $this->coursecontextid, 'quizcontextid' => $this->quizcontextid, 'questionid' => (int) $this->question->id];
        $query = "SELECT qa.*, qas_last.*
                    FROM {question_attempts} qa
                    LEFT JOIN {question_attempt_steps} qas_last ON qas_last.questionattemptid = qa.id
                    /* attach another copy of qas to those rows with the most recent timecreated,
                    using method from https://stackoverflow.com/a/28090544 */
                    LEFT JOIN {question_attempt_steps} qas_prev
                                    ON qas_last.questionattemptid = qas_prev.questionattemptid
                                        AND (qas_last.sequencenumber < qas_prev.sequencenumber
                                            OR (qas_last.sequencenumber = qas_prev.sequencenumber
                                                AND qas_last.id < qas_prev.id))
                    LEFT JOIN {user} u ON qas_last.userid = u.id
                    LEFT JOIN {question_usages} qu ON qa.questionusageid = qu.id
                    INNER JOIN {role_assignments} ra ON ra.userid = u.id
                WHERE qas_prev.timecreated IS NULL
                    /* Check responses are the correc quiz and made by students */
                    AND qu.component = 'mod_quiz'
                    AND qu.contextid = :quizcontextid
                    AND ra.roleid = 5
                    AND ra.contextid = :coursecontextid
                    /* In moodle 4 we look at all attempts at all versions.
                    Otherwise an edit, regrade and re-analysis becomes impossible. */
                    AND qa.questionid IN
                        (
                            SELECT qv.questionid
                                FROM {question_versions} qv_original
                                JOIN {question_versions} qv ON
                                        qv.questionbankentryid = qv_original.questionbankentryid
                            WHERE qv_original.questionid = :questionid
                        )
                ORDER BY u.username, qas_last.timecreated";

        $result = $DB->get_records_sql($query, $params);

        return $result;
    }

    public function match_variants_and_notes(): void {
        $questionnotes = [];
        $questionseeds = [];
        foreach (array_keys($this->summary) as $variant) {
            $questionnotes[$variant] = $variant;

            $question = question_bank::load_question((int) $this->question->id);
            $question->start_attempt(new question_attempt_step(), $variant);
            $questionseeds[$variant] = $question->seed;
            $qnotesummary = $question->get_question_summary();
            // TO-DO check for duplicate notes.
            $questionnotes[$variant] = stack_ouput_castext($qnotesummary);
        }

        $this->questionnotes = $questionnotes;
        $this->questionseeds = $questionseeds;
    }

    public function collate():void {
        $inputtotals = [];
        $qinputs = array_flip(array_keys($this->question->inputs));
        foreach ($qinputs as $key => $val) {
            $qinputs[$key] = ['score' => [], 'valid' => [], 'invalid' => [], 'other' => []];
        }
        $qprts = array_flip(array_keys($this->question->prts));
        foreach ($qprts as $key => $notused) {
            $qprts[$key] = [];
        }
        // We only display inputs relevant to a particular PTR.
        $inputsbyprt = $this->question->get_cached('required');
        foreach ($this->summary as $variant => $vdata) {
            $this->inputreport[$variant] = $qinputs;
            $this->prtreport[$variant] = $qprts;
            $this->prtreportinputs[$variant] = $qprts;

            foreach ($vdata as $attemptsummary => $num) {
                $inputvals = [];
                $rawdat = explode(';', $attemptsummary);
                foreach ($rawdat as $data) {
                    $data = trim($data);
                    foreach ($qinputs as $input => $notused) {
                        if (substr($data, 0, strlen($input . ':')) === $input . ':') {
                            // Tidy up inputs by (i) trimming status and whitespace, and (2) removing input name.
                            $datas = trim(substr($data, strlen($input . ':')));
                            $status = 'other';
                            if (strpos($datas, '[score]') !== false) {
                                $status = 'score';
                                $datas = trim(substr($datas, 0, -7));
                            } else if (strpos($datas, '[valid]') !== false) {
                                $status = 'valid';
                                $datas = trim(substr($datas, 0, -7));
                            } else if (strpos($datas, '[invalid]') !== false) {
                                $status = 'invalid';
                                $datas = trim(substr($datas, 0, -9));
                            }
                            // Reconstruct input string but whitespace is trimmed.
                            $inputvals[$input] = $input . ':' . $datas;
                            // Add data.
                            if (array_key_exists($datas, $this->inputreport[$variant][$input][$status])) {
                                $this->inputreport[$variant][$input][$status][$datas] += (int) $num;
                            } else {
                                $this->inputreport[$variant][$input][$status][$datas] = $num;
                            }
                            if (array_key_exists($datas, $this->inputreportsummary[$input][$status])) {
                                $this->inputreportsummary[$input][$status][$datas] += (int) $num;
                            } else {
                                $this->inputreportsummary[$input][$status][$datas] = $num;
                            }
                            // Count the total numbers in this array.
                            if (array_key_exists($input, $inputtotals)) {
                                $inputtotals[$input] += (int) $num;
                            } else {
                                $inputtotals[$input] = $num;
                            }
                        }
                    }
                    foreach ($qprts as $prt => $notused) {
                        // Only create an input summary of the inputs required for this PRT.
                        $inputsummary = '';
                        foreach ($inputsbyprt[$prt] as $input => $alsonotused) {
                            if (array_key_exists($input, $inputvals)) {
                                $inputsummary .= $inputvals[$input] . '; ';
                            }
                        }
                        if (substr($data, 0, strlen($prt . ':')) === $prt . ':') {
                            $datas = trim(substr($data, strlen($prt . ':')));
                            if (array_key_exists($datas, $this->prtreport[$variant][$prt])) {
                                $this->prtreport[$variant][$prt][$datas] += (int) $num;
                                if (array_key_exists($inputsummary, $this->prtreportinputs[$variant][$prt][$datas])) {
                                    $this->prtreportinputs[$variant][$prt][$datas][$inputsummary] += (int) $num;
                                } else {
                                    $this->prtreportinputs[$variant][$prt][$datas][$inputsummary] = (int) $num;
                                }
                            } else {
                                $this->prtreport[$variant][$prt][$datas] = $num;
                                $this->prtreportinputs[$variant][$prt][$datas] = [$inputsummary => (int) $num];
                            }
                            if (!array_key_exists($prt, $this->prtreportsummary)) {
                                $this->prtreportsummary[$prt] = [];
                            }
                            if (array_key_exists($datas, $this->prtreportsummary[$prt])) {
                                $this->prtreportsummary[$prt][$datas] += (int) $num;
                            } else {
                                $this->prtreportsummary[$prt][$datas] = $num;
                            }
                        }
                    }
                }
            }
        }
    }

    public function reports_sort(): void {
        foreach ($this->inputreport as $variant => $vdata) {
            foreach ($vdata as $input => $idata) {
                foreach ($idata as $key => $value) {
                    arsort($value);
                    $this->inputreport[$variant][$input][$key] = $value;
                }
            }
        }

        foreach ($this->inputreportsummary as $input => $idata) {
            foreach ($idata as $key => $value) {
                arsort($value);
                $this->inputreportsummary[$input][$key] = $value;
            }
        }

        foreach ($this->prtreport as $variant => $vdata) {
            foreach ($vdata as $prt => $tdata) {
                arsort($tdata);
                $this->prtreport[$variant][$prt] = $tdata;
            }
        }

        foreach ($this->prtreportsummary as $prt => $tdata) {
            ksort($tdata);
            $this->prtreportsummary[$prt] = $tdata;
            if (!array_key_exists($prt, $this->notesummary)) {
                $this->notesummary[$prt] = [];
            }
            foreach ($tdata as $rawnote => $num) {
                $notes = explode('|', $rawnote);
                foreach ($notes as $note) {
                    $note = trim($note);
                    if (array_key_exists($note, $this->notesummary[$prt])) {
                        $this->notesummary[$prt][$note] += (int) $num;
                    } else {
                        $this->notesummary[$prt][$note] = $num;
                    }
                }
            }

            foreach ($this->prtreportinputs[$variant][$prt] as $note => $ipts) {
                arsort($ipts);
                $this->prtreportinputs[$variant][$prt][$note] = $ipts;
            }
        }

        foreach ($this->notesummary as $prt => $tdata) {
            ksort($tdata);
            $this->notesummary[$prt] = $tdata;
        }
    }

    public static function get_relevant_quizzes(int $questionid):array {
        global $DB;
        $quizzesquery = "SELECT qr.usingcontextid, q.name, cc.id, co.fullname 
                    FROM {question_versions} qv 
                    LEFT JOIN {question_references} qr ON qv.questionbankentryid = qr.questionbankentryid
                    LEFT JOIN {context} c ON c.id = qr.usingcontextid
                    LEFT JOIN {course_modules} cm ON cm.id = c.instanceid
                    LEFT JOIN {quiz} q ON cm.instance = q.id
                    LEFT JOIN {course} co ON q.course = co.id
                    LEFT JOIN {context} cc ON cc.instanceid = co.id 
                    WHERE qv.questionid = :questionid
                        AND cc.contextlevel = 50";

        $quizzes = $DB->get_records_sql($quizzesquery, ['questionid' => $questionid]);
        return $quizzes;
    }
}
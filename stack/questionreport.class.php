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
 * Loads and manipulates data for display on the response analysis page.
 *
 * @copyright 2024 University of Edinburgh.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

/**
 * Retrieves and formats the response data for a particular question in a particular quiz.
 */
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
    /**
     * @var array Summary of response counts by variant.
     * [
     *   variantid => [ response => count, response => count, response => count ]
     *   variantid => [ response => count, response => count ]
     * ]
     */
    public $summary;
    /**
     * @var array key: variant id, value: seed.
     */
    public $questionseeds;
    /**
     * @var array key: variant id, value: notes.
     */
    public $questionnotes;
    /**
     * @var array count of input values by type, inputname and variant.
     * [ variantid => [
     *      inputname => [
     *          'score' => [ inputvalue => count, inputvalue => count],
     *          'valid' => [],
     *          'invalid' => [],
     *          'other' => []
     * ]]]
     */
    public $inputreport = [];
    /**
     * @var array Stores inputs, regardless of variant.
     * Multi-part questions may have inputs which are not subject to randomisation.
     *    [
     *      inputname => [
     *          'score' => [ inputvalue => count, inputvalue => count],
     *          'valid' => [],
     *          'invalid' => [],
     *          'other' => []
     *    ]]
     */
    public $inputreportsummary = [];
    /**
     * @var array PRT responses by variant.
     *  [
     *      variantid => [
     *         prtname => [
     *            response => count,
     *            response => count,
     *         ],
     *      ],
     *  ]
     */
    public $prtreport = [];
    /**
     * @var array input values by response, PRT and variant
     *  [
     *      variantid => [
     *         prtname => [
     *            response => [
     *                  inputname:inputvalue => count,
     *              ],
     *         ],
     *      ],
     *  ]
     */
    public $prtreportinputs = [];
    /**
     * @var array summary of the data without different variants.
     *      [
     *         prtname => [
     *            response => count,
     *            response => count,
     *         ],
     *      ]
     */
    public $prtreportsummary = [];
    /**
     * @var array note count by PRT.
     *  [
     *      prtname => [
     *          note => count,
     *          note => count,
     *      ],
     *  ]
     */
    public $notesummary = [];
    /**
     * @var object StdClass Data formatted for questionreport.mustache.
     */
    public $outputdata;

    /**
     * Constructor
     * @param object $question - a version of the question being analysed
     * @param int $quizcontextid - the id of the quizzes' context
     * @param int $coursecontextid - the id of the course's context
     */
    public function __construct(object $question, int $quizcontextid, int $coursecontextid) {
        $this->question = $question;
        $this->quizcontextid = $quizcontextid;
        $this->coursecontextid = $coursecontextid;
        $this->outputdata = new StdClass();
        $this->run_report();
    }

    /**
     * Creates the report
     * @return void
     */
    public function run_report(): void {
        $this->create_summary();
        $this->match_variants_and_notes();
        $this->collate();
        $this->reports_sort();
        $this->create_output_data();
    }

    /**
     * Create a summary of response counts by variant
     * [
     *   variantid => [ response => count, response => count, response => count ]
     *   variantid => [ response => count, response => count ]
     * ]
     *
     * Response example:
     * Seed: 333333333; ans1: 11 [score]; ans2: 22 [score];
     * PotResTree_1: # = 1 | thing1_true | prt1-1-T; PotResTree_2: # = 0 | thing2_bad | prt2-2-F
     * @return void
     */
    public function create_summary(): void {
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

    /**
     * Load relevant reponse summaries from the database.
     * Restrict to a single quiz, actual quiz answers and users who are students.
     * @return array
     */
    public function load_summary_data(): array {
        global $DB;
        $params = [
            'coursecontextid' => $this->coursecontextid,
            'quizcontextid' => $this->quizcontextid,
            'questionid' => (int) $this->question->id,
        ];
        $query = "SELECT qa.id, qa.variant, qa.responsesummary
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
                    INNER JOIN {role} r ON r.id = ra.roleid
                WHERE qas_prev.timecreated IS NULL
                    /* Check responses are the correct quiz and made by students */
                    AND qu.component = 'mod_quiz'
                    AND qu.contextid = :quizcontextid
                    AND r.archetype = 'student'
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

    /**
     * Create arrays indexed by variant id.
     * One for notes and one for seeds.
     * @return void
     */
    public function match_variants_and_notes(): void {
        $questionnotes = [];
        $questionseeds = [];
        foreach (array_keys($this->summary) as $variant) {
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

    /**
     * Create the data for each page of the report
     * @return void
     */
    public function collate(): void {
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
        $this->inputreportsummary = $qinputs;
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

    /**
     * Sort the entries within the differetn summaries.
     * @return void
     */
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

    /**
     * Pad and format the summary data for easy comparison and
     * then arrange in a suitable object to feed to the mustache template.
     * @return void
     */
    public function create_output_data(): void {
        $this->outputdata->question = $this->format_question_data($this->question);
        $this->outputdata->summary = $this->format_summary();
        $this->outputdata->notesummary = $this->format_notesummary($this->outputdata->summary->tot);
        $this->outputdata->variants = $this->format_variants();
        $this->outputdata->inputs = $this->format_inputs();
        $this->outputdata->rawdata = $this->format_raw_data();
    }

    /**
     * Question name, text and variables
     * @param mixed $question
     * @return object
     */
    public static function format_question_data($question): object {
        $qdata = new StdClass();
        $qdata->name = $question->name;
        $qdata->text = $question->questiontext;
        $qdata->deployedseeds = empty($question->deployedseeds) ? false : true;
        $qdata->hasvariants = $question->has_random_variants();
        $vars = $question->questionvariables;
        if ($vars != '') {
            $vars = trim($vars) . "\n\n";
        }
        foreach ($question->inputs as $inputname => $input) {
            $vars .= $inputname . ':' . $input->get_teacher_answer() . ";\n";
        }
        $qdata->vars = s(trim($vars));
        return $qdata;
    }

    /**
     * Frequency of answer notes, for each PRT, regardless of which variant was used
     * @return object
     */
    public function format_summary(): object {
        $output = new StdClass();
        $output->prts = [];
        $sumout = [];
        $tot = [];
        foreach ($this->prtreportsummary as $prt => $data) {
            $sumouti = '';
            $tot[$prt] = 0;
            $pad = max($data);
            foreach ($data as $key => $val) {
                $tot[$prt] += $val;
            }
            if ($data !== []) {
                foreach ($data as $dat => $num) {
                    $sumouti .= str_pad($num, strlen((string) $pad) + 1) . '(' .
                        str_pad(number_format((float) 100 * $num / $tot[$prt], 2, '.', ''), 6, ' ', STR_PAD_LEFT) .
                        '%); ' . $dat . "\n";
                }
            }
            if (trim($sumouti) !== '') {
                $sumout[$prt] = '## ' . $prt . ' ('. $tot[$prt] . ")\n" . $sumouti . "\n";;
            }
        }

        foreach ($this->question->prts as $prtname => $prt) {
            $prtdata = new StdClass();
            $prtdata->prtname = $prtname;
            $graph = $prt->get_prt_graph();
            $prtdata->graph_svg = stack_abstract_graph_svg_renderer::render($graph, $prtname . 'graphsvg');
            $prtdata->graph_text = stack_prt_graph_text_renderer::render($graph);
            $prtdata->maxima = s($prt->get_maxima_representation());
            $prtdata->sumout = (array_key_exists($prtname, $sumout)) ? trim($sumout[$prtname]) : null;
            $output->prts[] = $prtdata;
        }
        $output->tot = $tot;
        return $output;
    }

    /**
     * Frequency of answer notes, for each PRT, split by |, regardless of which variant was used
     * @param array $tot Totals from format_summary())
     * @return object
     */
    public function format_notesummary(array $tot): object {
        $output = new StdClass();
        $output->prts = [];
        $sumout = [];
        $prtlabels = [];
        foreach ($this->notesummary as $prt => $data) {
            $sumouti = '';
            $pad = max($data);
            if ($data !== []) {
                foreach ($data as $dat => $num) {
                    // Use the old $tot, to give meaningful percentages of which individual notes occur overall.
                    $prtlabels[$prt][$dat] = $num;
                    $sumouti .= str_pad($num, strlen((string) $pad) + 1) . '(' .
                        str_pad(number_format((float) 100 * $num / $tot[$prt], 2, '.', ''), 6, ' ', STR_PAD_LEFT) . '%); '.
                        $dat . "\n";
                }
            }
            if (trim($sumouti) !== '') {
                $sumout[$prt] = '## ' . $prt . ' ('. $tot[$prt] . ")\n" . $sumouti . "\n";;
            }
        }

        foreach ($this->question->prts as $prtname => $prt) {
            if (array_key_exists($prtname, $prtlabels)) {
                $prtdata = new StdClass();
                $prtdata->prtname = $prtname;
                $graph = $prt->get_prt_graph();
                $prtdata->graph_svg = stack_abstract_graph_svg_renderer::render($graph, $prtname . 'graphsvg');
                $prtdata->graph_text = stack_prt_graph_text_renderer::render($graph);
                $prtdata->sumout = s($sumout[$prtname]);
                $output->prts[] = $prtdata;
            }
        }

        return $output;
    }

    /**
     * Raw inputs and PRT answer notes by variant
     * @return object
     */
    public function format_variants(): object {
        $output = new StdClass();
        $output->variants = [];
        foreach (array_keys($this->summary) as $variant) {
            $variantdata = new StdClass();
            $variantdata->seed = $this->questionseeds[$variant];
            $variantdata->notes = $this->questionnotes[$variant];
            $variantdata->notessumout = $this->format_variant_answer_notes($variant);
            $variantdata->anssumout = $this->format_variant_inputs($variant);
            $output->variants[] = $variantdata;
        }
        return $output;
    }

    /**
     * PRT answer notes by variant
     * @param int $variant
     * @return object
     */
    public function format_variant_answer_notes(int $variant): object {
        $sumout = '';
        $sumheadline = '';
        foreach ($this->prtreport[$variant] as $prt => $idata) {
            $pad = 0;
            $tot = 0;
            foreach ($idata as $dat => $num) {
                $tot += $num;
            }
            if ($idata !== []) {
                $sumout .= '## ' . $prt . ' ('. $tot . ")\n";
                $pad = max($idata);
            }
            $sumprtheadline = '';
            foreach ($idata as $dat => $num) {
                $dataline = str_pad($num, strlen((string) $pad) + 1) . '(' .
                    str_pad(number_format((float) 100 * $num / $tot, 2, '.', ''), 6, ' ', STR_PAD_LEFT) .
                    '%); ' . $dat . "\n";
                $sumout .= $dataline;
                // Use most frequent (first) result for each part as variant headline.
                $sumprtheadline = ($sumprtheadline) ? $sumprtheadline : $dataline;
                foreach ($this->prtreportinputs[$variant][$prt][$dat] as $inputsummary => $inum) {
                    $sumout .= str_pad($inum, strlen((string) $pad) + 1) . '(' .
                        str_pad(number_format((float) 100 * $inum / $tot, 2, '.', ''), 6, ' ', STR_PAD_LEFT) .
                        '%); ' . htmlentities($inputsummary, ENT_COMPAT) . "\n";
                }
                $sumout .= "\n";
            }
            $sumheadline .= '## ' . $prt . ': ' . $sumprtheadline;
        }
        $result = new StdClass();
        $result->sumout = $sumout;
        $result->sumheadline = $sumheadline;
        return $result;
    }

    /**
     * Raw inputs by variant
     * @param int $variant
     * @return object
     */
    public function format_variant_inputs(int $variant): string {
        $sumout = '';
        foreach ($this->inputreport[$variant] as $input => $idata) {
            $sumouti = '';
            $tot = 0;
            foreach ($idata as $key => $data) {
                foreach ($data as $dat => $num) {
                    $tot += $num;
                }
            }
            foreach ($idata as $key => $data) {
                if ($data !== []) {
                    $sumouti .= '### ' . $key . "\n";
                    $pad = max($data);
                    foreach ($data as $dat => $num) {
                        $sumouti .= str_pad($num, strlen((string) $pad) + 1) . '(' .
                            str_pad(number_format((float) 100 * $num / $tot, 2, '.', ''), 6, ' ', STR_PAD_LEFT) .
                            '%); ' . htmlentities($dat, ENT_COMPAT) . "\n";
                    }
                    $sumouti .= "\n";
                }
            }
            if (trim($sumouti) !== '') {
                $sumout .= '## ' . $input . ' ('. $tot . ")\n" . $sumouti;
            }
        }
        return $sumout;
    }

    /**
     * Raw inputs, regardless of which variant was used
     * @return object
     */
    public function format_inputs(): object {
        $output = new StdClass();
        $sumout = '';
        foreach ($this->inputreportsummary as $input => $idata) {
            $sumouti = '';
            $tot = 0;
            foreach ($idata as $key => $data) {
                foreach ($data as $dat => $num) {
                    $tot += $num;
                }
            }
            foreach ($idata as $key => $data) {
                if ($data !== []) {
                    $sumouti .= '### ' . $key . "\n";
                    $pad = max($data);
                    foreach ($data as $dat => $num) {
                        $sumouti .= str_pad($num, strlen((string) $pad) + 1) . '(' .
                                str_pad(number_format((float) 100 * $num / $tot, 2, '.', ''), 6, ' ', STR_PAD_LEFT) .
                                '%); ' . htmlentities($dat, ENT_COMPAT) . "\n";
                    }
                    $sumouti .= "\n";
                }
            }
            if (trim($sumouti) !== '') {
                $sumout .= '## ' . $input . ' ('. $tot . ")\n" . $sumouti;
            }
        }
        $output->inputs = $sumout;
        return $output;
    }

    /**
     * Raw data
     * @return object
     */
    public function format_raw_data(): object {
        $output = new StdClass();
        $sumout = '';
        foreach ($this->summary as $variant => $vdata) {
            if ($vdata !== []) {
                $tot = 0;
                foreach ($vdata as $dat => $num) {
                    $tot += $num;
                }
                $pad = max($vdata);
                $sumout .= "\n# " . $variant . ' ('. $tot . ")\n";
                foreach ($vdata as $dat => $num) {
                    $sumout .= str_pad($num, strlen((string) $pad) + 1) . '(' .
                            str_pad(number_format((float) 100 * $num / $tot, 2, '.', ''), 6, ' ', STR_PAD_LEFT) .
                            '%); ' . htmlentities($dat, ENT_COMPAT) . "\n";
                }
            }
        }
        $output->rawdata = $sumout;
        return $output;
    }

    /**
     * Get inofmration on all quizzes containing a version of a given question
     * @param int $questionid
     * @return array
     */
    public static function get_relevant_quizzes(int $questionid): array {
        global $DB;
        $quizzesquery = "SELECT qr.usingcontextid as quizcontextid, q.name, cc.id as coursecontextid, co.fullname as coursename
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

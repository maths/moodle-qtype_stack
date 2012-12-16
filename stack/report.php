<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * This file defines the report class for STACK questions.
 *
 * @copyright  2012 the University of Birmingham
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/quiz/report/attemptsreport.php');
require_once($CFG->dirroot . '/mod/quiz/report/statistics/report.php');


/**
 * Report subclass for the responses report to individual stack questions.
 *
 *
 * @copyright  2012 the University of Birmingham
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class quiz_stack_report extends quiz_attempts_report {

    /** @var int the relevant id of the question to be analysed.*/
    public $questionid;

    /** @array The names of all inputs for this question.*/
    private $inputs;

    /** @array The names of all prts for this question.*/
    private $prts;

    /** @array The deployed answernotes for this question.*/
    private $notes;

    /** @array The attempts at this question.*/
    private $attempts;

    /*
     * Set the relevant id of the question to be analysed.
     */
    public function add_questionid($questionid) {
        $this->questionid = $questionid;
    }

    public function display($quiz, $cm, $course) {
        global $CFG, $DB, $OUTPUT;

        $this->context = context_module::instance($cm->id);

        // Find out current groups mode. [Copied from .../statistics/report.php lines 58 onwards]
        $currentgroup = $this->get_current_group($cm, $course, $this->context);
        $nostudentsingroup = false; // True if a group is selected and there is no one in it.
        if (empty($currentgroup)) {
            $currentgroup = 0;
            $groupstudents = array();

        } else if ($currentgroup == self::NO_GROUPS_ALLOWED) {
            $groupstudents = array();
            $nostudentsingroup = true;

        } else {
            // All users who can attempt quizzes and who are in the currently selected group.
            $groupstudents = get_users_by_capability($this->context,
                    array('mod/quiz:reviewmyattempts', 'mod/quiz:attempt'),
                    '', '', '', '', $currentgroup, '', false);
            if (!$groupstudents) {
                $nostudentsingroup = true;
            }
        }

        $qubaids = quiz_statistics_qubaids_condition($quiz->id, $currentgroup, $groupstudents, true);
        $dm = new question_engine_data_mapper();
        $this->attempts = $dm->load_attempts_at_question($this->questionid, $qubaids);

        // Setup useful internal arrays for report generation
        $question = question_bank::load_question($this->questionid);
        $this->inputs = array_keys($question->inputs);
        $this->prts = array_keys($question->prts);
        
        // TODO: change this to be a list of all *deployed* notes, not just those *used*.
        $notes = array();
        foreach($this->attempts as $qattempt) {
            $q = $qattempt->get_question();
            $notes[$q->get_question_summary()] = true;
        }
        $this->notes = array_keys($notes);

        // Compute results
        $results = $this->input_report();
        list ($results_valid, $results_invalid) = $this->input_report_separate();
        // Display the results
        foreach($this->notes as $note) {
            echo html_writer::tag('h2', $note);

            $inputstable = new html_table();
            $inputstable->attributes['class'] = 'generaltable stacktestsuite';
            $inputstable->head = array('', '', '');
            foreach($results[$note] as $dsummary => $summary) {
                foreach($summary as $key => $res) {
                    $inputstable->data[] = array($dsummary, $key, $res);
                }
            }
            echo html_writer::table($inputstable);

            // Separate out inputs and look at validity.
            foreach($this->inputs as $input) {
                $inputstable = new html_table();
                $inputstable->attributes['class'] = 'generaltable stacktestsuite';
                $inputstable->head = array($input, '', '');
                foreach($results_valid[$note][$input] as $key => $res) {
                    $inputstable->data[] = array($key, $res, get_string('inputstatusnamevalid', 'qtype_stack'));
                    $inputstable->rowclasses[] = 'pass';
                }
                foreach($results_invalid[$note][$input] as $key => $res) {
                    $inputstable->data[] = array($key, $res, get_string('inputstatusnameinvalid', 'qtype_stack'));
                    $inputstable->rowclasses[] = 'fail';
                }
                echo html_writer::table($inputstable);
            }

        }

    }

    /* 
     * This function counts the number of response summaries per question note.
     */
    private function input_report() {

        $results = array();
        foreach($this->notes as $note) {
            $results[$note] = array();
        }

        foreach ($this->attempts as $qattempt) {
            $question = $qattempt->get_question();
            $note = $question->get_question_summary();

            for ($i = 0; $i < $qattempt->get_num_steps(); $i++) {
                $step = $qattempt->get_step($i);
                if($data = $this->nontrivial_response_step($qattempt, $i)) {
                    $fraction = (string) $step->get_fraction();
                    $summary = $question->summarise_response($data);
                    if (array_key_exists($summary, $results[$note])) {
                        if (array_key_exists($fraction, $results[$note][$summary])) {
                            $results[$note][$summary][$fraction] += 1;
                        } else {
                            $results[$note][$summary][$fraction] = 1;
                        }
                    } else {
                        $results[$note][$summary][$fraction] = 1;
                    }
                }
            }
        }

        return $results;
    }

    /* 
     * Counts the number of response to each input and records their validity.
     */
    private function input_report_separate() {

        $results = array();
        $validity = array();
        foreach($this->notes as $note) {
            foreach($this->inputs as $input) {
                $results[$note][$input] = array();
            }
        }

        foreach ($this->attempts as $qattempt) {
            $question = $qattempt->get_question();
            $note = $question->get_question_summary();

            for ($i = 0; $i < $qattempt->get_num_steps(); $i++) {
                if($data = $this->nontrivial_response_step($qattempt, $i)) {
                    $summary = $question->summarise_response_data($data);
                    foreach($this->inputs as $input) {
                        if (array_key_exists($input, $summary)) {
                            if ('' != $data[$input]) {
                                if (array_key_exists($data[$input],  $results[$note][$input])) {
                                    $results[$note][$input][$data[$input]] += 1;
                                } else {
                                    $results[$note][$input][$data[$input]] = 1;
                                }
                            }
                        $validity[$note][$input][$data[$input]] = $summary[$input];
                        }
                    }
                }
            }
        }

        foreach($this->notes as $note) {
            foreach($this->inputs as $input) {
                arsort($results[$note][$input]);
            }
        }

        // Split into valid and invalid responses.
        $results_valid = array();
        $results_invalid = array();
        foreach($this->notes as $note) {
            foreach($this->inputs as $input) {
                $results_valid[$note][$input] = array();
                $results_invalid[$note][$input] = array();
                foreach($results[$note][$input] as $key => $res) {
                    if ('valid' == $validity[$note][$input][$key]) {
                        $results_valid[$note][$input][$key] = $res;
                    } else {
                        $results_invalid[$note][$input][$key] = $res;
                    }
                }
            }
        }


        return array($results_valid, $results_invalid);
    }

    /*
     * From an individual attempt, we need to establish that step $i for this attempt is non-trivial, and return the non-trivial responses.
     * Otherwise we return boolean false
     */
    private function nontrivial_response_step($qattempt, $i) {
        $any_data = false;
        $rdata = array();
        $step = $qattempt->get_step($i);
        $data = $step->get_submitted_data();
        foreach ($this->inputs as $input) {
            if (array_key_exists($input, $data)) {
                $any_data = true;
                $rdata[$input] = $data[$input];
            }
        }
        if ($any_data) {
            return $rdata;
        }
        return false;
    }

}
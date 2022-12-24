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
// along with STACK.  If not, see <http://www.gnu.org/licenses/>.

/**
 * This script lets the user see what attempts have been made at this question.
 *
 * The script loops over summarise_response data from the database, and does not
 * re-generate reports.  The script is designed to let a question author improve feedback
 * and assessment by looking at what students type, easily and without going through a quiz report.
 *
 * @copyright  2020 the University of Edinburgh
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__.'/../../../config.php');
require_once($CFG->libdir . '/questionlib.php');
require_once(__DIR__ . '/vle_specific.php');

// Get the parameters from the URL.
$questionid = required_param('questionid', PARAM_INT);
// Load the necessary data.
$questiondata = question_bank::load_question_data($questionid);
if (!$questiondata) {
    throw new stack_exception('questiondoesnotexist');
}
$question = question_bank::load_question($questionid);

// Process any other URL parameters, and do require_login.
list($context, $seed, $urlparams) = qtype_stack_setup_question_test_page($question);

// Check permissions.
question_require_capability_on($questiondata, 'view');
$canedit = question_has_capability_on($questiondata, 'edit');

// Initialise $PAGE.
$PAGE->set_url('/question/type/stack/questiontestreport.php', $urlparams);
$title = stack_string('basicquestionreport');
$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->set_pagelayout('popup');

$testquestionlink = new moodle_url('/question/type/stack/questiontestrun.php', $urlparams);

require_login();

// Start output.
echo $OUTPUT->header();
$renderer = $PAGE->get_renderer('qtype_stack');
echo $OUTPUT->heading($question->name, 2);


// Link back to question tests.

$out = html_writer::link($testquestionlink, stack_string('runquestiontests'), array('target' => '_blank'));

// If question has no random variants.
if (empty($question->deployedseeds)) {
    if ($question->has_random_variants()) {
        $out .= ' ' . stack_string('questionnotdeployedyet');
    }
}

if (stack_determine_moodle_version() < 400) {
    $qurl = question_preview_url($questionid, null, null, null, null, $context);
} else {
    $qurl = qbank_previewquestion\helper::question_preview_url($questionid, null, null, null, null, $context);
}

echo html_writer::tag('p', $out . ' ' .
    $OUTPUT->action_icon($qurl, new pix_icon('t/preview', get_string('preview'))));

// Display a representation of the question, variables and PRTs for easy reference.
echo $OUTPUT->heading(stack_string('questiontext'), 3);
echo html_writer::tag('pre', $question->questiontext, array('class' => 'questiontext'));

$vars = $question->questionvariables;
if ($vars != '') {
    $vars = trim($vars) . "\n\n";
}
$inputdisplay = array($vars);
foreach ($question->inputs as $inputname => $input) {
    $vars .= $inputname . ':' . $input->get_teacher_answer() . ";\n";
}
$maxima = html_writer::start_tag('div', array('class' => 'questionvariables'));
$maxima .= html_writer::tag('pre', s(trim($vars)));
$maxima .= html_writer::end_tag('div');
echo $maxima;

$offlinemaxima = array();
$nodesummary1 = array();
$nodesummary2 = array();
$graphrepresentation = array();
foreach ($question->prts as $prtname => $prt) {
    $nodes = $prt->get_nodes_summary();
    $nodesummary1[$prtname] = '';
    $nodesummary2[$prtname] = '';
    $nodesummary3[$prtname] = '';
    $offlinemaxima[$prtname] = $prt->get_maxima_representation();

    foreach ($nodes as $key => $node) {
        $nodesummary1[$prtname] .= ($key + 1). ': ' . $node->answertest . "\n";
        $nodesummary2[$prtname] .= $node->trueanswernote . "\n";
        $nodesummary3[$prtname] .= $node->falseanswernote . "\n";
    }

    $graph = $prt->get_prt_graph();
    $graphrepresentation[$prtname] = stack_abstract_graph_svg_renderer::render($graph, $prtname . 'graphsvg');
}

flush();

// Later we only display inputs relevant to a particular PTR, so we sort out prt input requirements here.
$inputsbyprt = $question->get_cached('required');

$params = [$questionid];
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
          WHERE qas_prev.timecreated IS NULL";

if (stack_determine_moodle_version() < 400) {
    $query .= " AND qa.questionid = ?";
} else {
    // In moodle 4 we look at all attempts at all versions.
    // Otherwise an edit, regrade and re-analysis becomes impossible.
    $query .= " AND qa.questionid IN (
                    SELECT qv.questionid
                      FROM {question_versions} qv_original
                      JOIN {question_versions} qv ON
                                qv.questionbankentryid = qv_original.questionbankentryid
                    WHERE qv_original.questionid = ?)";
}
$query .= " ORDER BY u.username, qas_last.timecreated";

global $DB;

$result = $DB->get_records_sql($query, $params);
$summary = array();
foreach ($result as $qattempt) {
    if (!array_key_exists($qattempt->variant, $summary)) {
        $summary[$qattempt->variant] = array();
    }
    $rsummary = trim($qattempt->responsesummary);
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

// Match up variants to answer notes.
$questionnotes = array();
$questionseeds = array();
foreach (array_keys($summary) as $variant) {
    $questionnotes[$variant] = $variant;

    $question = question_bank::load_question($questionid);
    $question->start_attempt(new question_attempt_step(), $variant);
    $questionseeds[$variant] = $question->seed;
    $notesummary = $question->get_question_summary();
    // TODO check for duplicate notes.
    $questionnotes[$variant] = stack_ouput_castext($notesummary);
}

// Create blank arrays in which to store data.
$qinputs = array_flip(array_keys($question->inputs));
foreach ($qinputs as $key => $val) {
    $qinputs[$key] = array('score' => array(), 'valid' => array(), 'invalid' => array(), 'other' => array());
}

$inputreport = array();
// The inputreportsummary is used to store inputs, regardless of variant.
// Multi-part questions may have inputs which are not subject to randomisation.
$inputreportsummary = $qinputs;
$inputtotals = array();

$qprts = array_flip(array_keys($question->prts));
foreach ($qprts as $key => $notused) {
    $qprts[$key] = array();
}
$prtreport = array();
$prtreportinputs = array();

// Create a summary of the data without different variants.
$prtreportsummary = array();

foreach ($summary as $variant => $vdata) {
    $inputreport[$variant] = $qinputs;
    $prtreport[$variant] = $qprts;
    $prtreportinputs[$variant] = $qprts;

    foreach ($vdata as $attemptsummary => $num) {
        $inputvals = array();
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
                    if (array_key_exists($datas, $inputreport[$variant][$input][$status])) {
                        $inputreport[$variant][$input][$status][$datas] += (int) $num;
                    } else {
                        $inputreport[$variant][$input][$status][$datas] = $num;
                    }
                    if (array_key_exists($datas, $inputreportsummary[$input][$status])) {
                        $inputreportsummary[$input][$status][$datas] += (int) $num;
                    } else {
                        $inputreportsummary[$input][$status][$datas] = $num;
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
                    if (array_key_exists($datas, $prtreport[$variant][$prt])) {
                        $prtreport[$variant][$prt][$datas] += (int) $num;
                        if (array_key_exists($inputsummary, $prtreportinputs[$variant][$prt][$datas])) {
                            $prtreportinputs[$variant][$prt][$datas][$inputsummary] += (int) $num;
                        } else {
                            $prtreportinputs[$variant][$prt][$datas][$inputsummary] = (int) $num;
                        }
                    } else {
                        $prtreport[$variant][$prt][$datas] = $num;
                        $prtreportinputs[$variant][$prt][$datas] = array($inputsummary => (int) $num);
                    }
                    if (!array_key_exists($prt, $prtreportsummary)) {
                        $prtreportsummary[$prt] = array();
                    }
                    if (array_key_exists($datas, $prtreportsummary[$prt])) {
                        $prtreportsummary[$prt][$datas] += (int) $num;
                    } else {
                        $prtreportsummary[$prt][$datas] = $num;
                    }
                }
            }
        }
    }
}

// Sort the values.
foreach ($inputreport as $variant => $vdata) {
    foreach ($vdata as $input => $idata) {
        foreach ($idata as $key => $value) {
            arsort($value);
            $inputreport[$variant][$input][$key] = $value;
        }
    }
}

foreach ($inputreportsummary as $input => $idata) {
    foreach ($idata as $key => $value) {
        arsort($value);
        $inputreportsummary[$input][$key] = $value;
    }
}

foreach ($prtreport as $variant => $vdata) {
    foreach ($vdata as $prt => $tdata) {
        arsort($tdata);
        $prtreport[$variant][$prt] = $tdata;
    }
}

$notesummary = array();
foreach ($prtreportsummary as $prt => $tdata) {
    ksort($tdata);
    $prtreportsummary[$prt] = $tdata;
    if (!array_key_exists($prt, $notesummary)) {
        $notesummary[$prt] = array();
    }
    foreach ($tdata as $rawnote => $num) {
        $notes = explode('|', $rawnote);
        foreach ($notes as $note) {
            $note = trim($note);
            if (array_key_exists($note, $notesummary[$prt])) {
                $notesummary[$prt][$note] += (int) $num;
            } else {
                $notesummary[$prt][$note] = $num;
            }
        }
    }

    foreach ($prtreportinputs[$variant][$prt] as $note => $ipts) {
        arsort($ipts);
        $prtreportinputs[$variant][$prt][$note] = $ipts;
    }
}

foreach ($notesummary as $prt => $tdata) {
    ksort($tdata);
    $notesummary[$prt] = $tdata;
}

// Frequency of answer notes, for each PRT, split by |, regardless of which variant was used.
echo html_writer::tag('h3', stack_string('basicreportnotes'));

$sumout = array();
foreach ($prtreportsummary as $prt => $data) {
    $sumouti = '';
    $tot = 0;
    $pad = max($data);
    foreach ($data as $key => $val) {
        $tot += $val;
    }
    if ($data !== array()) {
        foreach ($data as $dat => $num) {
            $sumouti .= str_pad($num, strlen((string) $pad) + 1) . '(' .
                str_pad(number_format((float) 100 * $num / $tot, 2, '.', ''), 6, ' ', STR_PAD_LEFT) .
                '%); ' . $dat . "\n";
        }
    }
    if (trim($sumouti) !== '') {
        $sumout[$prt] = '## ' . $prt . ' ('. $tot . ")\n" . $sumouti . "\n";;
    }
}

foreach ($question->prts as $prtname => $prt) {
    echo html_writer::start_tag('table');
    echo html_writer::start_tag('tr');

    echo html_writer::tag('td', $graphrepresentation[$prtname]);

    $node = html_writer::start_tag('div', array('class' => 'questionvariables'));
    $node .= html_writer::tag('pre', s($nodesummary1[$prtname]));
    $node .= html_writer::end_tag('div');
    echo html_writer::tag('td', $node);

    $node = html_writer::start_tag('div', array('class' => 'questionvariables'));
    $node .= html_writer::tag('pre', s($nodesummary2[$prtname]));
    $node .= html_writer::end_tag('div');
    echo html_writer::tag('td', $node);

    $node = html_writer::start_tag('div', array('class' => 'questionvariables'));
    $node .= html_writer::tag('pre', s($nodesummary3[$prtname]));
    $node .= html_writer::end_tag('div');
    echo html_writer::tag('td', $node);

    $maxima = html_writer::start_tag('div', array('class' => 'questionvariables'));
    $out = html_writer::tag('summary', $prtname);
    $out .= html_writer::tag('pre', s($offlinemaxima[$prtname]));
    $maxima .= html_writer::tag('details', $out);
    $maxima .= html_writer::end_tag('div');
    echo html_writer::tag('td', $maxima);

    echo html_writer::end_tag('tr');
    echo html_writer::end_tag('table');

    if (array_key_exists($prtname, $sumout)) {
        echo html_writer::tag('pre', trim($sumout[$prtname]));
    }
}

$sumout = array();
$prtlabels = array();
foreach ($notesummary as $prt => $data) {
    $sumouti = '';
    if ($data !== array()) {
        foreach ($data as $dat => $num) {
            // Use the old $tot, to give meaningful percentages of which individual notes occur overall.
            $prtlabels[$prt][$dat] = $num;
            $sumouti .= str_pad($num, strlen((string) $pad) + 1) . '(' .
                str_pad(number_format((float) 100 * $num / $tot, 2, '.', ''), 6, ' ', STR_PAD_LEFT) . '%); '.
                $dat . "\n";
        }
    }
    if (trim($sumouti) !== '') {
        $sumout[$prt] = '## ' . $prt . ' ('. $tot . ")\n" . $sumouti . "\n";;
    }
}
if (trim(implode($sumout)) !== '') {
    echo html_writer::tag('h3', stack_string('basicreportnotessplit'));
}

echo html_writer::start_tag('table');
foreach ($question->prts as $prtname => $prt) {
    if (array_key_exists($prtname, $prtlabels)) {
        echo html_writer::start_tag('tr');

        echo html_writer::tag('td', $prtname);
        echo html_writer::tag('td', $graphrepresentation[$prtname]);

        $graph = $prt->get_prt_graph($prtlabels[$prtname]);
        echo html_writer::tag('td', stack_abstract_graph_svg_renderer::render($graph, $prtname . 'graphsvg'));

        $node = html_writer::start_tag('div', array('class' => 'questionvariables'));
        $node .= html_writer::tag('pre', s($nodesummary1[$prtname]));
        $node .= html_writer::end_tag('div');
        echo html_writer::tag('td', $node);

        $node = html_writer::start_tag('div', array('class' => 'questionvariables'));
        $node .= html_writer::tag('pre', s($nodesummary2[$prtname]));
        $node .= html_writer::end_tag('div');
        echo html_writer::tag('td', $node);

        $node = html_writer::start_tag('div', array('class' => 'questionvariables'));
        $node .= html_writer::tag('pre', s($nodesummary3[$prtname]));
        $node .= html_writer::end_tag('div');
        echo html_writer::tag('td', $node);

        $maxima = html_writer::start_tag('div', array('class' => 'questionvariables'));
        $maxima .= html_writer::tag('pre', s($sumout[$prtname]));
        $maxima .= html_writer::end_tag('div');
        echo html_writer::tag('td', $maxima);

        echo html_writer::end_tag('tr');
    }
}
echo html_writer::end_tag('table');

// Raw inputs and PRT answer notes by variant.
if (array_keys($summary) !== array()) {
    echo html_writer::tag('h3', stack_string('basicreportvariants'));
}
foreach (array_keys($summary) as $variant) {
    $sumout = '';
    foreach ($prtreport[$variant] as $prt => $idata) {
        $pad = 0;
        $tot = 0;
        foreach ($idata as $dat => $num) {
            $tot += $num;
        }
        if ($idata !== array()) {
            $sumout .= '## ' . $prt . ' ('. $tot . ")\n";
            $pad = max($idata);
        }
        foreach ($idata as $dat => $num) {
            $sumout .= str_pad($num, strlen((string) $pad) + 1) . '(' .
                str_pad(number_format((float) 100 * $num / $tot, 2, '.', ''), 6, ' ', STR_PAD_LEFT) .
                '%); ' . $dat . "\n";
            foreach ($prtreportinputs[$variant][$prt][$dat] as $inputsummary => $inum) {
                $sumout .= str_pad($inum, strlen((string) $pad) + 1) . '(' .
                    str_pad(number_format((float) 100 * $inum / $tot, 2, '.', ''), 6, ' ', STR_PAD_LEFT) .
                    '%); ' . $inputsummary . "\n";
            }
            $sumout .= "\n";
        }
        $sumout .= "\n";
    }
    if (trim($sumout) !== '') {
        echo html_writer::tag('h3', $questionseeds[$variant] . ': ' . $questionnotes[$variant]);
        echo html_writer::tag('pre', $sumout);
    }
}


foreach (array_keys($summary) as $variant) {
    $sumout = '';
    foreach ($inputreport[$variant] as $input => $idata) {
        $sumouti = '';
        $tot = 0;
        foreach ($idata as $key => $data) {
            foreach ($data as $dat => $num) {
                $tot += $num;
            }
        }
        foreach ($idata as $key => $data) {
            if ($data !== array()) {
                $sumouti .= '### ' . $key . "\n";
                $pad = max($data);
                foreach ($data as $dat => $num) {
                    $sumouti .= str_pad($num, strlen((string) $pad) + 1) . '(' .
                        str_pad(number_format((float) 100 * $num / $tot, 2, '.', ''), 6, ' ', STR_PAD_LEFT) .
                        '%); ' . $dat . "\n";
                }
                $sumouti .= "\n";
            }
        }
        if (trim($sumouti) !== '') {
            $sumout .= '## ' . $input . ' ('. $tot . ")\n" . $sumouti;
        }
    }
    if (trim($sumout) !== '') {
        echo html_writer::tag('h3', $questionseeds[$variant] . ': ' . $questionnotes[$variant]);
        echo html_writer::tag('pre', $sumout);
    }
}

// Summary of just the inputs.
$sumout = '';
foreach ($inputreportsummary as $input => $idata) {
    $sumouti = '';
    $tot = 0;
    foreach ($idata as $key => $data) {
        foreach ($data as $dat => $num) {
            $tot += $num;
        }
    }
    foreach ($idata as $key => $data) {
        if ($data !== array()) {
            $sumouti .= '### ' . $key . "\n";
            $pad = max($data);
            foreach ($data as $dat => $num) {
                $sumouti .= str_pad($num, strlen((string) $pad) + 1) . '(' .
                        str_pad(number_format((float) 100 * $num / $tot, 2, '.', ''), 6, ' ', STR_PAD_LEFT) .
                        '%); ' . $dat . "\n";
            }
            $sumouti .= "\n";
        }
    }
    if (trim($sumouti) !== '') {
        $sumout .= '## ' . $input . ' ('. $tot . ")\n" . $sumouti;
    }
}
if (trim($sumout) !== '') {
    echo html_writer::tag('h3', stack_string('basicreportinputsummary'));
    echo html_writer::tag('pre', $sumout);
}

// Output the raw data.
echo html_writer::tag('h3', stack_string('basicreportraw'));
$sumout = '';
foreach ($summary as $variant => $vdata) {
    if ($vdata !== array()) {
        $tot = 0;
        foreach ($vdata as $dat => $num) {
            $tot += $num;
        }
        $pad = max($vdata);
        $sumout .= "\n# " . $variant . ' ('. $tot . ")\n";
        foreach ($vdata as $dat => $num) {
            $sumout .= str_pad($num, strlen((string) $pad) + 1) . '(' .
                    str_pad(number_format((float) 100 * $num / $tot, 2, '.', ''), 6, ' ', STR_PAD_LEFT) .
                    '%); ' . $dat . "\n";
        }
    }
}
echo html_writer::tag('pre', $sumout);

// Finish output.
echo $OUTPUT->footer();

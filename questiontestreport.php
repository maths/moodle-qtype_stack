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
require_once(__DIR__ . '/stack/questionreport.class.php');

// Get the parameters from the URL.
$questionid = required_param('questionid', PARAM_INT);
$quizcontext = optional_param('context', null, PARAM_INT);
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
$out = html_writer::link($testquestionlink, stack_string('runquestiontests'), ['target' => '_blank']);

// If question has no random variants.
if (empty($question->deployedseeds)) {
    if ($question->has_random_variants()) {
        $out .= ' ' . stack_string('questionnotdeployedyet');
    }
}

$qurl = qbank_previewquestion\helper::question_preview_url($questionid, null, null, null, null, $context);

echo html_writer::tag('p', $out . ' ' .
    $OUTPUT->action_icon($qurl, new pix_icon('t/preview', get_string('preview'))));

// Display a representation of the question, variables and PRTs for easy reference.
echo $OUTPUT->heading(stack_string('questiontext'), 3);
echo html_writer::tag('pre', $question->questiontext, ['class' => 'questiontext']);

$vars = $question->questionvariables;
if ($vars != '') {
    $vars = trim($vars) . "\n\n";
}
$inputdisplay = [$vars];
foreach ($question->inputs as $inputname => $input) {
    $vars .= $inputname . ':' . $input->get_teacher_answer() . ";\n";
}
$maxima = html_writer::start_tag('div', ['class' => 'questionvariables']);
$maxima .= html_writer::tag('pre', s(trim($vars)));
$maxima .= html_writer::end_tag('div');
echo $maxima;

flush();

$quizzes = stack_question_report::get_relevant_quizzes($questionid);
foreach ($quizzes as $quiz) {
    $button = $OUTPUT->single_button(new moodle_url('/question/type/stack/questiontestreport.php',
            $urlparams + ['context' => $quiz->usingcontextid]),
            $quiz->name);
    echo html_writer::tag('div', $button,
            []);
    echo "\n";
}
if ($quizcontext !== null) {
    $coursecontextid = $quizzes[$quizcontext]->id;
} else {
    echo $OUTPUT->footer();
    return;
}

$report = new stack_question_report($question, $quizcontext, $coursecontextid);
$summary = $report->summary;
$questionnotes = $report->questionnotes;
$questionseeds = $report->questionseeds;
$inputreport = $report->inputreport;
$inputreportsummary = $report->inputreportsummary;
$prtreport = $report->prtreport;
$prtreportinputs = $report->prtreportinputs;
// Create a summary of the data without different variants.
$prtreportsummary = $report->prtreportsummary;
$notesummary = $report->notesummary;

// Frequency of answer notes, for each PRT, split by |, regardless of which variant was used.
echo html_writer::tag('h3', stack_string('basicreportnotes'));

$sumout = [];
foreach ($prtreportsummary as $prt => $data) {
    $sumouti = [];
    $tot = 0;
    $pad = max($data);
    foreach ($data as $key => $val) {
        $tot += $val;
    }
    if ($data !== []) {
        foreach ($data as $dat => $num) {
            $sumouti[] = str_pad($num, strlen((string) $pad) + 1) . '(' .
                str_pad(number_format((float) 100 * $num / $tot, 2, '.', ''), 6, ' ', STR_PAD_LEFT) .
                '%); ' . $dat;
        }
    }
    if (count($sumouti) > 0) {
        $sumout[$prt] = new StdClass();
        $sumout[$prt]->prt = $prt;
        $sumout[$prt]->tot = $tot;
        $sumout[$prt]->sumouti = $sumouti;
    }
}

// Produce a text-based summary of a PRT.
foreach ($question->prts as $prtname => $prt) {
    // Here we render each PRT as a separate single-row table.
    $prtdata = new StdClass();
    $prtdata->prtname = $prtname;
    $graph = $prt->get_prt_graph();
    $prtdata->graph_svg = stack_abstract_graph_svg_renderer::render($graph, $prtname . 'graphsvg');
    $prtdata->graph_text = stack_prt_graph_text_renderer::render($graph);
    $prtdata->maxima = s($prt->get_maxima_representation());
    $prtdata->sumout = (array_key_exists($prtname, $sumout)) ? $sumout[$prtname] : null;
    echo $OUTPUT->render_from_template('qtype_stack/questionreportprt', $prtdata);
}

$sumout = [];
$prtlabels = [];
foreach ($notesummary as $prt => $data) {
    $sumouti = '';
    if ($data !== []) {
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

$tablerows = [];
foreach ($question->prts as $prtname => $prt) {
    if (array_key_exists($prtname, $prtlabels)) {
        $prtdata = new StdClass();
        $prtdata->prtname = $prtname;
        $graph = $prt->get_prt_graph();
        $prtdata->graph_svg = stack_abstract_graph_svg_renderer::render($graph, $prtname . 'graphsvg');
        $prtdata->graph_text = stack_prt_graph_text_renderer::render($graph);
        $prtdata->sumout = s($sumout[$prtname]);
        echo $OUTPUT->render_from_template('qtype_stack/questionreportprtsplit', $prtdata);
    }
}

// Raw inputs and PRT answer notes by variant.
if (array_keys($summary) !== []) {
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
        if ($idata !== []) {
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
                    '%); ' . htmlentities($inputsummary, ENT_COMPAT) . "\n";
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
if (trim($sumout) !== '') {
    echo html_writer::tag('h3', stack_string('basicreportinputsummary'));
    echo html_writer::tag('pre', $sumout);
}

// Output the raw data.
echo html_writer::tag('h3', stack_string('basicreportraw'));
$sumout = '';
foreach ($summary as $variant => $vdata) {
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
echo html_writer::tag('pre', $sumout);

// Finish output.
echo $OUTPUT->footer();

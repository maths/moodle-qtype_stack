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
 * This script seeds every question for seed 0. Which will cause
 * the questions to be initialised and checked for common errors.
 *
 * For extra testing this will also try to render the model solutions
 * and tries to push in the teachers answers to the questions.
 *
 * This does not run the question-tests, use the other bulk script
 * for that. Not running because the time use there depends on deployed
 * variants and some systems have much too many variants deployed.
 *
 * @package    qtype_stack
 * @subpackage cli
 * @copyright  2019 Aalto University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


define('CLI_SCRIPT', true);

require(__DIR__ . '/../../../../config.php');
require_once($CFG->libdir.'/clilib.php');
require_once($CFG->libdir . '/questionlib.php');
require_once(__DIR__ . '/../vle_specific.php');
require_once(__DIR__ . '/../locallib.php');
require_once(__DIR__ . '/../stack/utils.class.php');
require_once(__DIR__ . '/../stack/bulktester.class.php');

// Get cli options.
list($options, $unrecognized) = cli_get_params(['help' => false], ['h' => 'help']);

if ($unrecognized) {
    $unrecognized = implode("\n  ", $unrecognized);
    cli_error(get_string('cliunknowoption', 'admin', $unrecognized));
}

if ($options['help']) {
    echo "This script will try to lightly poke questions to see if they are still stable.";
    exit(0);
}

$context = context_system::instance();


// We process every single question, even those that wait for cron to clean up things.
$questions = $DB->get_records('question', ['qtype' => 'stack'], 'id', 'id');

cli_heading('Processing ' . count($questions) . ' questions');

$c = 1;

function cat_to_course($catid) {
    global $DB;
    // Why are the contexts so hard, where are the utility functions to map them...
    static $map = [];
    if (isset($map[$catid])) {
        return $map[$catid];
    }
    $cat = $DB->get_record('question_categories', ['id' => $catid], 'contextid, parent');
    if ($cat->contextid == 0) {
        return cat_to_course($cat->parent);
    }

    $context = $DB->get_record('context', ['id' => $cat->contextid], '*');
    if ($context->contextlevel == 50) {
        $map[$catid] = $context->instanceid;
        return $context->instanceid;
    }
    if ($context->contextlevel == 10) {
        $map[$catid] = 1;
        return 1;
    }
    if ($context->contextlevel == 40) {
        // Could probably pick a course...
        $map[$catid] = 1;
        return 1;
    }

    // Try going up on the context.
    while ($context->contextlevel != 50) {
        $path = explode('/', $context->path);
        array_pop($path);
        $path = implode('/', $path);
        if ($path === '') {
            break;
        }
        $context = $DB->get_record('context', ['path' => $path], '*');
    }
    if ($context->contextlevel == 50) {
        $map[$catid] = $context->instanceid;
        return $context->instanceid;
    }
    $map[$catid] = 1;
    return 1;
}

foreach ($questions as $id) {
    if ($c % 50 === 0) {
        cli_writeln(' Questions processed: ' . $c);
    }
    $c++;
    $questiondata = question_bank::load_question_data($id->id);
    $urlparams = ['qperpage' => 1000,
        'category' => $questiondata->category,
        'lastchanged' => $id->id,
        'courseid' => cat_to_course($questiondata->category)];
    if (property_exists($questiondata, 'hidden') && $questiondata->hidden) {
        $urlparams['showhidden'] = 1;
    }

    try {
        $fails = false;

        $question = question_bank::load_question($id->id);
        $urlparams['category'] .= ',' . $question->contextid;
        $questionbanklink = (new moodle_url('/question/edit.php', $urlparams))->out(false);
        $question->seed = 0;
        if ($question->validate_against_stackversion($context) !== '') {
            cli_writeln(' Upgrade issues in ' . $id->id . ': ' . $question->name);
            $fails = true;
        }

        $quba = question_engine::make_questions_usage_by_activity('qtype_stack', context_system::instance());
        $quba->set_preferred_behaviour('adaptive');

        $slot = $quba->add_question($question, $question->defaultmark);
        try {
            $quba->start_question($slot);
        } catch (\Exception $estart) {
            cli_writeln(' Start issues in ' . $id->id . ': ' . $question->name);
            $fails = true;
        } catch (\Throwable $tstart) {
            cli_writeln(' Start issues in ' . $id->id . ': ' . $question->name);
            $fails = true;
        }

        if ($fails) {
            cli_writeln($questionbanklink);
            continue;
        }

        // Prepare the display options.
        $options = question_display_options();
        $question->castextprocessor = new castext2_qa_processor($quba->get_question_attempt($slot));

        // Create the question text, question note and worked solutions.
        // This involves instantiation, which may fail.
        try {
            $renderquestion = $quba->render_question($slot, $options);
        } catch (Exception $erender) {
            cli_writeln(' Question render issues in ' . $id->id . ': ' . $question->name);
            $fails = true;
        }
        try {
            if (trim($question->generalfeedback) !== '') {
                $workedsolution = $question->get_generalfeedback_castext();
                $workedsolution->get_rendered($question->castextprocessor);
            }
        } catch (Exception $erendersolution) {
            cli_writeln(' Solution render issues in ' . $id->id . ': ' . $question->name);
            $fails = true;
        }
        try {
            $questionote = $question->get_question_summary();
        } catch (Exception $erendernote) {
            cli_writeln(' Note render issues in ' . $id->id . ': ' . $question->name);
            $fails = true;
        }

        try {
            $response = $quba->get_correct_response($slot);
            $quba->process_action($slot, $response);
            $d = implode(' ', $question->summarise_response_data($response));
            if (mb_strpos($d, 'STACKERROR') !== false) {
                cli_writeln(' Potenttial teachers answer issues in ' . $id->id . ': ' . $question->name);
                $fails = true;
            }
        } catch (Exception $etestta) {
            cli_writeln(' Teachers answer issues in ' . $id->id . ': ' . $question->name);
            $fails = true;
        }
        if ($fails) {
            cli_writeln($questionbanklink);
        }
    } catch (Exception $eload) {
        // We do not have the context-id...
        $cat = $DB->get_record('question_categories', ['id' => $questiondata->category], 'contextid');
        $urlparams['category'] .= ',' . $cat->contextid;
        $questionbanklink = (new moodle_url('/question/edit.php', $urlparams))->out(false);

        cli_writeln(' Failure to even load question id = ' . $id->id);
        cli_writeln($questionbanklink);
    }
}

echo "\n\n";
exit(0);

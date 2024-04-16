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

// This script handles the various deploy/undeploy actions from questiontestrun.php.
//
// @copyright  2012 the Open University
// @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.

define('NO_OUTPUT_BUFFERING', true);

require_once(__DIR__.'/../../../config.php');

require_once($CFG->libdir . '/questionlib.php');
require_once(__DIR__ . '/locallib.php');


// Get the parameters from the URL.
$questionid = required_param('questionid', PARAM_INT);

// Load the necessary data.
$questiondata = $DB->get_record('question', ['id' => $questionid], '*', MUST_EXIST);
$question = question_bank::load_question($questionid);

// Process any other URL parameters, and do require_login.
list($context, $seed, $urlparams) = qtype_stack_setup_question_test_page($question);
$PAGE->set_context($context);

// Check permissions.
question_require_capability_on($questiondata, 'edit');
require_sesskey();

// Initialise $PAGE.
$nexturl = new moodle_url('/question/type/stack/questiontestrun.php', $urlparams);
$PAGE->set_url($nexturl); // Since this script always ends in a redirect.
$PAGE->set_heading($COURSE->fullname);
$PAGE->set_pagelayout('popup');

require_login();

// Process deploy if applicable.
$deploy = optional_param('deploy', null, PARAM_INT);
if (!is_null($deploy)) {
    $question->deploy_variant($deploy);
    redirect($nexturl);
}

// Process undeploy if applicable.
$undeploy = optional_param('undeploy', null, PARAM_INT);
if (!is_null($undeploy)) {
    $question->undeploy_variant($undeploy);

    // As we redirect, switch to the undeployed variant, so it easy to re-deploy
    // if you just made a mistake.
    $nexturl->param('seed', $undeploy);
    redirect($nexturl);
}

// Process undeployall if applicable.
$undeploy = optional_param('undeployall', null, PARAM_INT);
if (!is_null($undeploy) && $question->deployedseeds) {
    foreach ($question->deployedseeds as $seed) {
        $question->undeploy_variant($seed);
    }
    $nexturl->param('seed', $seed);
    redirect($nexturl);
}

// Process undeployall if applicable.
$deployfromlist = optional_param('deployfromlist', null, PARAM_INT);
$deploysystematic = optional_param('deploysystematic', null, PARAM_INT);
if (!is_null($deployfromlist) || !is_null($deploysystematic)) {

    // Check data integrity.
    $dataproblem = false;

    if (!is_null($deployfromlist)) {
        $deploytxt = optional_param('deployfromlist', null, PARAM_TEXT);
        $baseseeds = explode("\n", trim($deploytxt));
    } else {
        $baseseeds = range(1, $deploysystematic);
    }
    $newseeds = [];
    foreach ($baseseeds as $newseed) {
        // Now also explode over commas.
        $newseed = explode(",", trim($newseed));
        foreach ($newseed as $seed) {
            // Clean up whitespace.
            // Force the entry to be a positive integer.
            if (trim($seed) !== '') {
                $seed = (int) (trim($seed));
                if ($seed <= 0) {
                    $dataproblem = true;
                } else {
                    $newseeds[] = (string) ($seed);
                }
            }
        }
    }

    // No action to take?
    if ($newseeds === $question->deployedseeds) {
        redirect($nexturl);
    }

    if (count($newseeds) > 100) {
        $nexturl->param('deployfeedbackerr', stack_string('deploymanyerror', ['err' => count($newseeds)]));
        redirect($nexturl);
    }

    // Check the entries are all different.
    if (count($newseeds) !== count(array_flip($newseeds))) {
        // TODO: specific feedback for each error.
        $dataproblem = true;
    }

    if ($dataproblem) {
        $nexturl->param('deployfeedbackerr', stack_string('deployfromlisterror'));
        redirect($nexturl);
    }

    // Undeploy all existing variants.
    if ($question->deployedseeds) {
        foreach ($question->deployedseeds as $seed) {
            $question->undeploy_variant($seed);
        }
    }
    // Deploy all new variants.
    foreach ($newseeds as $seed) {
        $question->deploy_variant($seed);
    }
    redirect($nexturl);
}

$deploy = optional_param('deploymany', null, PARAM_INT);
$deploytxt = optional_param('deploymany', null, PARAM_TEXT);
$starttime = time();
// The number of seconds we devote to deploying before moving on.  Prevents system hangging.
// Note, in "safe mode" the set time limit function has no effect.
$maxtime = 180;
// Deploying lots of variants is time consuming, and we need a progress bar in some cases.
$numforprogressbar = 10;
core_php_time_limit::raise($maxtime); // Prevent PHP timeouts.
gc_collect_cycles(); // Because PHP's default memory management is rubbish.

if (!is_null($deploy)) {

    if (0 == $deploy) {
        $nexturl->param('deployfeedbackerr', stack_string('deploymanyerror', ['err' => $deploytxt]));
        redirect($nexturl);
    }

    if ($deploy > 100) {
        $nexturl->param('deployfeedbackerr', stack_string('deploytoomanyerror'));
        redirect($nexturl);
    }

    $maxfailedattempts = 10;
    $failedattempts = 0;
    $numberdeployed = 0;

    // Output something if we need a progress bar.
    if ($deploy >= $numforprogressbar) {
        echo $OUTPUT->header();
        flush();
        $a = ['total' => $deploy, 'done' => 0];
        $progressevery = (int) min(max(1, $deploy / 500), 100);
        $pbar = new progress_bar('deployedprogress', 500, true);
    }

    while ($failedattempts < $maxfailedattempts && $numberdeployed < $deploy && time() - $starttime < $maxtime) {
        // Genrate a new seed.
        $seed = mt_rand();
        $variantdeployed = false;

        // Reload the question to ensure any new deployed version is included.
        $question = question_bank::load_question($questionid);
        $question->seed = (int) $seed;
        $quba = question_engine::make_questions_usage_by_activity('qtype_stack', $context);
        $quba->set_preferred_behaviour('adaptive');
        $slot = $quba->add_question($question, $question->defaultmark);
        $quba->start_question($slot);

        foreach ($question->deployedseeds as $key => $deployedseed) {
            $qn = question_bank::load_question($questionid);
            $qn->seed = (int) $deployedseed;
            $cn = $qn->get_context();
            $qunote = question_engine::make_questions_usage_by_activity('qtype_stack', $cn);
            $qunote->set_preferred_behaviour('adaptive');
            $slotnote = $qunote->add_question($qn, $qn->defaultmark);
            $qunote->start_question($slotnote);

            // Check if the question note has already been deployed.
            if ($qn->get_question_summary() == $question->get_question_summary()) {
                $variantdeployed = true;
                $failedattempts++;
            }
        }

        if (!$variantdeployed) {
            // Load the list of test cases.
            $testscases = question_bank::get_qtype('stack')->load_question_tests($question->id);
            // Exectue the tests.
            $testresults = [];
            $allpassed = true;
            foreach ($testscases as $key => $testcase) {
                $testresults[$key] = $testcase->test_question($questionid, $seed, $context);
                if (!$testresults[$key]->passed()) {
                    $nexturl->param('seed', $seed);
                    $nexturl->param('deployfeedback', stack_string('deploymanysuccess', ['no' => $numberdeployed]));
                    $nexturl->param('deployfeedbackerr', stack_string('stackInstall_testsuite_fail'));
                    redirect($nexturl);
                }
            }

            // Actually deploy the question.
            $question->deploy_variant($seed);
            $numberdeployed++;
            flush();
        }
        if ($deploy >= $numforprogressbar) {
            $a['done'] += 1;
            if ($a['done'] % $progressevery == 0 || $a['done'] == $a['total']) {
                core_php_time_limit::raise(60);
                $pbar->update($a['done'], $a['total'], get_string('deployedprogress', 'qtype_stack', $a));
            }
        }
    }

    // If we quit the while loop early we should set progress to 100%.
    if ($deploy >= $numforprogressbar) {
        $pbar->update($a['total'], $a['total'], get_string('deployedprogress', 'qtype_stack', $a));
    }

    $message = stack_string('deploymanysuccess', ['no' => $numberdeployed]);
    $nexturl->param('deployfeedback', $message);
    $nexturl->param('seed', $seed);

    $allok = true;
    if ($failedattempts >= $maxfailedattempts) {
        $allok = false;
        $errmessage = stack_string('deploymanynonew');
        $nexturl->param('deployfeedbackerr', $errmessage);
        if ($deploy < $numforprogressbar) {
            redirect($nexturl);
        }
    }
    if (time() - $starttime >= $maxtime) {
        $allok = false;
        $errmessage = stack_string('deployoutoftime', ['time' => time() - $starttime]);
        $nexturl->param('deployfeedbackerr', $errmessage);
        if ($deploy < $numforprogressbar) {
            redirect($nexturl);
        }
    }

    if ($deploy >= $numforprogressbar) {
        \core\notification::success(stack_string('deploymanysuccess', ['no' => $numberdeployed]));
        if (!$allok) {
            echo html_writer::tag('p', $errmessage, ['class' => 'overallresult fail']);
        }
        echo $OUTPUT->continue_button($nexturl);
        echo $OUTPUT->footer();
        exit;
    }
    redirect($nexturl);
}

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


require_once(__DIR__.'/../../../config.php');

require_once($CFG->libdir . '/questionlib.php');
require_once(__DIR__ . '/locallib.php');


// Get the parameters from the URL.
$questionid = required_param('questionid', PARAM_INT);

// Load the necessary data.
$questiondata = $DB->get_record('question', array('id' => $questionid), '*', MUST_EXIST);
$question = question_bank::load_question($questionid);

// Process any other URL parameters, and do require_login.
list($context, $seed, $urlparams) = qtype_stack_setup_question_test_page($question);

// Check permissions.
question_require_capability_on($questiondata, 'edit');
require_sesskey();

// Initialise $PAGE.
$nexturl = new moodle_url('/question/type/stack/questiontestrun.php', $urlparams);
$PAGE->set_url($nexturl); // Since this script always ends in a redirect.
$PAGE->set_heading($COURSE->fullname);
$PAGE->set_pagelayout('admin');

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

$deploy = optional_param('deploymany', null, PARAM_INT);
$deploytxt = optional_param('deploymany', null, PARAM_TEXT);
$starttime = time();
// The number of seconds we devote to deploying before moving on.  Prevents system hangging.
// Note, in "safe mode" the set time limit function has no effect.
$maxtime = 180;
flush(); // Force output to prevent timeouts and to make progress clear.
core_php_time_limit::raise($maxtime); // Prevent PHP timeouts.
gc_collect_cycles(); // Because PHP's default memory management is rubbish.

if (!is_null($deploy)) {

    if (0 == $deploy) {
        $nexturl->param('deployfeedbackerr', stack_string('deploymanyerror', array('err' => $deploytxt)));
        redirect($nexturl);
    }

    if ($deploy > 100) {
        $nexturl->param('deployfeedbackerr', stack_string('deploytoomanyerror'));
        redirect($nexturl);
    }

    $maxfailedattempts = 10;
    $failedattempts = 0;
    $numberdeployed = 0;

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
            $testresults = array();
            $allpassed = true;
            foreach ($testscases as $key => $testcase) {
                $testresults[$key] = $testcase->test_question($quba, $question, $seed);
                if (!$testresults[$key]->passed()) {
                    $nexturl->param('seed', $seed);
                    $nexturl->param('deployfeedback', stack_string('deploymanysuccess', array('no' => $numberdeployed)));
                    $nexturl->param('deployfeedbackerr', stack_string('stackInstall_testsuite_fail'));
                    redirect($nexturl);
                }
            }

            // Actually deploy the question.
            $question->deploy_variant($seed);
            $numberdeployed++;
            flush();
        }
    }

    $nexturl->param('deployfeedback', stack_string('deploymanysuccess', array('no' => $numberdeployed)));
    $nexturl->param('seed', $seed);
    if (time() - $starttime >= $maxtime) {
        $nexturl->param('deployfeedbackerr', stack_string('deployoutoftime', array('time' => time() - $starttime)));
        redirect($nexturl);
    }
    if ($failedattempts >= $maxfailedattempts) {
        $nexturl->param('deployfeedbackerr', stack_string('deploymanynonew'));
    }
    redirect($nexturl);
}

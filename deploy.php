<?php
// This file is part of Stack - http://stack.bham.ac.uk/
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
 * This script handles the various deploy/undeploy actions from questiontestrun.php.
 *
 * @copyright  2012 the Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__).'/../../../config.php');

require_once($CFG->libdir . '/questionlib.php');
require_once(dirname(__FILE__) . '/locallib.php');


// Get the parameters from the URL.
$questionid = required_param('questionid', PARAM_INT);

// Load the necessary data.
$questiondata = $DB->get_record('question', array('id' => $questionid), '*', MUST_EXIST);
$question = question_bank::load_question($questionid);
$context = $question->get_context();

// Check permissions.
require_login();
question_require_capability_on($questiondata, 'edit');
require_sesskey();

// Initialise $PAGE.
$PAGE->set_url('/question/type/stack/questiontestrun.php', array('questionid' => $question->id));
$PAGE->set_context($context);

// Process deploy if applicable.
$deploy = optional_param('deploy', null, PARAM_INT);
if (!is_null($deploy)) {
    $record = new stdClass();
    $record->questionid = $question->id;
    $record->seed = $deploy;
    $DB->insert_record('qtype_stack_deployed_seeds', $record);

    redirect(new moodle_url('/question/type/stack/questiontestrun.php',
            array('questionid' => $question->id, 'seed' => $deploy)));
}

// Process undeploy if applicable.
$undeploy = optional_param('undeploy', null, PARAM_INT);
if (!is_null($undeploy)) {
    $DB->delete_records('qtype_stack_deployed_seeds',
            array('questionid' => $question->id, 'seed' => $undeploy));

    redirect(new moodle_url('/question/type/stack/questiontestrun.php',
            array('questionid' => $question->id, 'seed' => $undeploy)));
}

redirect(new moodle_url('/question/type/stack/questiontestrun.php', array('questionid' => $question->id)));

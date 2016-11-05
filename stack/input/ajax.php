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
 * Handles ajax requests to validate the input.
 */

define('AJAX_SCRIPT', true);

require_once(__DIR__ . '/../../../../../config.php');
require_once($CFG->libdir . '/questionlib.php');
require_once(__DIR__ . '/../options.class.php');
require_once(__DIR__ . '/inputbase.class.php');

$qaid  = required_param('qaid', PARAM_INT);
$inputname = required_param('name', PARAM_ALPHANUMEXT);
$inputvalue = required_param('input', PARAM_RAW);

if (!isloggedin()) {
    die;
}

// This should not be necessary, but the TeX filter requires it, because it uses $OUTPUT.
$PAGE->set_context(context_system::instance());

$dm = new question_engine_data_mapper();
$qa = $dm->load_question_attempt($qaid);
$question = $qa->get_question();

$input = $question->inputs[$inputname];
$state = $question->get_input_state($inputname, $inputvalue, true);

$result = array(
    'input'   => $inputvalue,
    'status'  => $state->status,
    'message' => $input->render_validation($state, $qa->get_qt_field_name($inputname)),
);

header('Content-type: application/json; charset=utf-8');
echo json_encode($result);

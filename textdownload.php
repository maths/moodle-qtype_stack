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
 * This script serves text files generated on demand by rendering CASText
 * of a given question with a given seed. For generated data transfer needs.
 *
 * @copyright  2021 Aalto University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');
require_once(__DIR__ . '/vle_specific.php');

global $CFG;
require_once($CFG->libdir . '/questionlib.php');

require_login();

// Start by checking that we have what we need.
if (!(isset($_GET['qaid']) && isset($_GET['id']) && isset($_GET['name']))) {
    header('HTTP/1.0 404 Not Found');
    header('Content-Type: text/plain;charset=UTF-8');
    echo 'Incomplete request';
    die();
}

// Extract the details we need for this action.
$qaid = $_GET['qaid'];
$tdid = $_GET['id'];
$name = $_GET['name'];

// Check that they are of the correct type.
if (!is_numeric($qaid) || !is_numeric($tdid)) {
    header('HTTP/1.0 404 Not Found');
    header('Content-Type: text/plain;charset=UTF-8');
    echo 'Incomplete request';
    die();
}

// So what we are doing is that we need to instanttiate the question
// of that attempt to have correct seed and then we need to render
// that specific td-file and serve it out with a specific name.
$dm = new question_engine_data_mapper();
$qa = $dm->load_question_attempt($qaid);
$question = $qa->get_question();
$question->apply_attempt_state($qa->get_step(0));

if (!stack_user_can_view_question($question)) {
    header('HTTP/1.0 403 Forbidden');
    header('Content-Type: text/plain;charset=UTF-8');
    echo 'This question is not accessible for the active user';
    die();
}
// Unlock session during instantiation.
\core\session\manager::write_close();

// Make sure that the cache is good, as this is one of those places where
// the identifier for the cached item comes from outside we cannot
// cannot directly ask for it as that would allow people to force the cache
// to be regenerated.

// This will generate the cache if it is missing, which is highly unlikely.
$question->get_cached('units');

if (!isset($question->compiledcache['castext-td-' . $tdid])) {
    header('HTTP/1.0 404 Not Found');
    header('Content-Type: text/plain;charset=UTF-8');
    echo 'No such textdownload object in this question';
    die();
}

require_once(__DIR__ . '/stack/cas/castext2/castext2_evaluatable.class.php');
$ct = castext2_evaluatable::make_from_compiled($question->compiledcache['castext-td-' .
    $tdid], $name, new castext2_static_replacer($question->get_cached('static-castext-strings')));

// Get the context from the question.
$ses = new stack_cas_session2([], $question->options, $question->seed);
$question->add_question_vars_to_session($ses);

$ses->add_statement($ct);

// Is it valid?
if (!$ses->get_valid()) {
    header('HTTP/1.0 500 Internal Server Error');
    header('Content-Type: text/plain;charset=UTF-8');
    echo 'Unknown issue related to the generation of this data';
    die();
}

// Render it.
$ses->instantiate();
$content = $ct->get_rendered();

// Now pick some sensible headers.
header('HTTP/1.0 200 OK');
header("Content-Disposition: attachment; filename=\"$name\"");
if (strripos($name, '.csv') === strlen($name) - 4) {
    header('Content-Type: text/csv;charset=UTF-8');
} else {
    header('Content-Type: text/plain;charset=UTF-8');
}
echo($content);

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
 * Page which simply redirects to edit page for latest version of supplied question.
 *
 * @package    qtype_stack
 * @copyright  2025 The University of Edinburgh.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__.'/../../../config.php');
require_once(__DIR__ . '/locallib.php');
require_once($CFG->libdir . '/questionlib.php');
require_login();
// Get the parameters from the URL.
$questionid = required_param('id', PARAM_INT);
list($qversion, $questionid) = get_latest_question_version($questionid);
$question = question_bank::load_question($questionid);

list($context, $seed, $urlparams) = qtype_stack_setup_question_test_page($question);
$urlparams['id'] = $question->id;
redirect(new moodle_url('/question/bank/editquestion/question.php', $urlparams));

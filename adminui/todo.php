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
 * This page lets the user find all questions in a course in which they are a techer
 * which have a [[todo]] block.  The questions are sorted by any tags they might have.
 *
 * @copyright  2024 University of Edinburgh
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__.'/../../../../config.php');

require_once($CFG->libdir . '/questionlib.php');
require_once(__DIR__ . '/../locallib.php');
require_once(__DIR__ . '/../stack/utils.class.php');
require_once(__DIR__ . '/../stack/bulktester.class.php');

// Get the parameters from the URL.
$context = context_system::instance();
$contextid = optional_param('contextid', $context->__get('id'), PARAM_INT);
$context = context::instance_by_id($contextid);

// Login and check permissions.
require_login();
$PAGE->set_url('/question/type/stack/todo.php');
$PAGE->set_context($context);
$title = stack_string('seetodolist');
$PAGE->set_title($title);

echo $OUTPUT->header();
echo $OUTPUT->heading($title);
echo html_writer::tag('p', stack_string('seetodolist_desc'));
echo html_writer::tag('p', stack_string('seetodolist_help'));

$bulktester = new stack_bulk_tester();
$tagsummary = [];

// Loop over all questions which this teache can edit.
foreach ($bulktester->get_num_stack_questions_by_context() as $contextid => $numstackquestions) {
    $context = context::instance_by_id($contextid);
    if (has_capability('moodle/question:editall', $context)) {
        $categories = $bulktester->get_categories_for_context($context->id);

        $questiontestsurl = new moodle_url('/question/type/stack/questiontestrun.php');
        if ($context->contextlevel == CONTEXT_COURSE) {
            $questiontestsurl->param('courseid', $context->instanceid);
        } else if ($context->contextlevel == CONTEXT_MODULE) {
            $questiontestsurl->param('cmid', $context->instanceid);
        } else {
            $questiontestsurl->param('courseid', SITEID);
        }

        foreach ($categories as $currentcategoryid => $nameandcount) {
            core_php_time_limit::raise(60); // Prevent PHP timeouts.

            $qtodos = [];
            $questions = $bulktester->stack_questions_in_category_with_todo($currentcategoryid);
            if (!$questions) {
                continue;
            }
            foreach ($questions as $qid => $qname) {
                $q = question_bank::load_question($qid);
                list($hastodos, $tags) = $q->get_question_todos();
                if ($hastodos) {
                    $preurl = qbank_previewquestion\helper::question_preview_url($qid,
                        null, null, null, null, $context);
                    $dashurl = html_writer::link(new moodle_url($questiontestsurl,
                        ['questionid' => $qid]), $qname). ' ' .
                        $OUTPUT->action_icon($preurl, new pix_icon('t/preview', get_string('preview')));
                    // TODO: add in a direct edit URL.
                    $qtodos[] = ['qid' => $qid,
                                 'qname' => $qname,
                                 'tags' => $tags,
                                 'dashurl' => $dashurl,
                                ];
                    if ($tags !== []) {
                        foreach ($tags as $tag) {
                            if (array_key_exists($tag, $tagsummary)) {
                                $tagsummary[$tag][] = $dashurl;
                            } else {
                                $tagsummary[$tag] = [$dashurl];
                            }
                        }
                    }
                }
            }
            if ($qtodos !== []) {
                echo $OUTPUT->heading($context->get_context_name());
                echo '<table><thead><tr><th>Question</th><th>Tags</th></thead><tbody>';
                // Load the whole question, simpler to get the contexts correct that way.
                foreach ($qtodos as $item) {
                    echo "<tr><td>" . $item['dashurl'] .
                        '</td><td>' . implode(', ', $item['tags']). '<td></tr>';
                }
                echo '</tbody></table>';

                flush(); // Force output to prevent timeouts and to make progress clear.
            }
        }
    }
}

if ($tagsummary !== []) {
    echo "\n\n<hr />\n\n";
    foreach ($tagsummary as $tagname => $tagurls) {
        echo $OUTPUT->heading($tagname);
        foreach ($tagurls as $dashurl) {
            echo $dashurl . "\n<br >\n";
        }
    }
}

echo $OUTPUT->footer();

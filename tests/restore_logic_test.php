<?php
// This file is part of Stack - https://stack.maths.ed.ac.uk
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
 * Unit tests for the restore logic.
 *
 * @package    qtype_stack
 * @copyright  2017 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace qtype_stack;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');
require_once($CFG->dirroot . '/question/type/stack/backup/moodle2/restore_qtype_stack_plugin.class.php');

// phpcs:ignore moodle.Commenting.MissingDocblock.Class
final class restore_logic_test_class extends \restore_qtype_stack_plugin {
    // phpcs:ignore moodle.Commenting.VariableComment.Missing
    private $log = '';

    // phpcs:ignore moodle.Commenting.MissingDocblock.MissingTestcaseMethodDescription
    public function __construct() {

        // Passing $this as $step, since we only need some log method, which we implemente below.
        parent::__construct('qtype', 'stack', $this);
    }

    /**
     * Fake log method.
     * @param $message the message.
     * @param $level the level.
     */
    public function log($message, $level) {
        $this->log .= $message . "\n";
    }

    /**
     * Fake log method.
     * @param $message the message.
     * @param $level the level.
     */
    public function get_task() {

        return  null;
    }

    /**
     * Get everything that was logged.
     * @return string the log messages, separated by newline.
     */
    public function get_log() {

        return $this->log;
    }

    // @codingStandardsIgnoreLine
    public function after_execute_question(): void {

        // Make method public.
        parent::after_execute_question();
    }
}


/**
 *
 * @package    qtype_stack
 * @copyright  2012 The University of Birmingham
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @group qtype_stack
 * @covers \qtype_stack
 */
final class restore_logic_test extends \advanced_testcase {

    public function test_fix_prt_roots(): void {

        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();

        // Create a test question.
        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $cat = $generator->create_question_category();
        $question = $generator->create_question('stack', 'test3', ['category' => $cat->id]);

        // Break one of its PRTs.
        $DB->set_field('qtype_stack_prts', 'firstnodename', -1,
                ['questionid' => $question->id, 'name' => 'oddeven']);
        $DB->set_field('qtype_stack_prt_nodes', 'truenextnode', 7,
                ['questionid' => $question->id, 'prtname' => 'oddeven', 'nodename' => 0]);

        $restoreplugin = new restore_logic_test_class();
        $restoreplugin->after_execute_question();

        $this->assertStringContainsString('The PRT named "oddeven" is malformed', $restoreplugin->get_log());
        $this->assertStringContainsString('Question name "test-3"', $restoreplugin->get_log());
        $this->assertStringContainsString('Node 8 is not in the graph', $restoreplugin->get_log());
    }
}

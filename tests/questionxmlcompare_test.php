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
 * Unit tests for the question XML comparison class.
 *
 * @package    qtype_stack
 * @copyright  2026 The University of Edinburgh
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace qtype_stack;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/stack/stack/questionxmlcompare.class.php');

/**
 * Unit tests for the question XML comparison class.
 */
#[\PHPUnit\Framework\Attributes\CoversClass(\stack_question_xml_compare::class)]
#[\PHPUnit\Framework\Attributes\Group('qtype_stack')]
final class questionxmlcompare_test extends \advanced_testcase {
    public function test_compare_rows_uses_current_version_perspective(): void {
        $current = "same\nadded in current\nsame again\nchanged new\nsame last";
        $compared = "same\nsame again\nchanged old\nsame last\ndeleted from current";

        $rows = \stack_question_xml_compare::compare_rows($current, $compared);

        $this->assertEquals('same', $rows[0]->type);
        $this->assertEquals('added', $rows[1]->type);
        $this->assertEquals('added in current', $rows[1]->currenttext);
        $this->assertEquals('changed', $rows[3]->type);
        $this->assertEquals('changed new', $rows[3]->currenttext);
        $this->assertEquals('changed old', $rows[3]->comparetext);
        $this->assertEquals('deleted', $rows[5]->type);
        $this->assertEquals('deleted from current', $rows[5]->comparetext);
    }

    public function test_diff_rows_highlights_inline_edits_and_deletions(): void {
        $rows = \stack_question_xml_compare::diff_rows('value new tail', 'value old tail');

        $this->assertCount(1, $rows);
        $this->assertEquals('changed', $rows[0]->type);
        $this->assertStringContainsString('stack-xml-compare-inline-added', $rows[0]->currenthtml);
        $this->assertStringContainsString('stack-xml-compare-inline-deleted', $rows[0]->currenthtml);
        $this->assertStringContainsString('stack-xml-compare-inline-missing', $rows[0]->currenthtml);
        $this->assertStringContainsString('stack-xml-compare-inline-deleted', $rows[0]->comparehtml);
    }

    public function test_diff_rows_shows_deleted_only_content_in_current_column(): void {
        $rows = \stack_question_xml_compare::diff_rows("same\nlast", "same\ndeleted\nlast");

        $this->assertEquals('deleted', $rows[1]->type);
        $this->assertStringContainsString('deleted', $rows[1]->currenthtml);
        $this->assertStringContainsString('stack-xml-compare-inline-missing', $rows[1]->currenthtml);
        $this->assertEquals('deleted', $rows[1]->comparehtml);
    }

    public function test_form_params_preserve_scalar_context_parameters(): void {
        $params = \stack_question_xml_compare::form_params([
            'id' => 2,
            'cmid' => 3,
            'nested' => ['ignored'],
        ]);

        $this->assertCount(2, $params);
        $this->assertEquals('id', $params[0]->name);
        $this->assertEquals(2, $params[0]->value);
        $this->assertEquals('cmid', $params[1]->name);
        $this->assertEquals(3, $params[1]->value);
    }
}

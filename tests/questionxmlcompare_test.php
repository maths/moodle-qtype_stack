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
require_once($CFG->dirroot . '/question/type/stack/stack/utils.class.php');
require_once($CFG->dirroot . '/question/type/stack/locallib.php');
require_once($CFG->dirroot . '/question/type/stack/stack/questionxmlcompare.class.php');

/**
 * Unit tests for the question XML comparison class.
 */
#[\PHPUnit\Framework\Attributes\CoversClass(\stack_question_xml_compare::class)]
#[\PHPUnit\Framework\Attributes\Group('qtype_stack')]
final class questionxmlcompare_test extends \advanced_testcase {
    public function test_get_compare_version_defaults_to_previous_version(): void {
        $versions = [3 => 103, 2 => 102, 1 => 101];

        $this->assertSame(2, \stack_question_xml_compare::get_compare_version(null, $versions, 3));
    }

    public function test_get_compare_version_defaults_to_current_version_when_no_previous_exists(): void {
        $versions = [1 => 101];

        $this->assertSame(1, \stack_question_xml_compare::get_compare_version(null, $versions, 1));
    }

    public function test_get_compare_version_accepts_requested_version(): void {
        $versions = [3 => 103, 2 => 102, 1 => 101];

        $this->assertSame(1, \stack_question_xml_compare::get_compare_version(1, $versions, 3));
    }

    public function test_get_compare_version_rejects_unknown_version(): void {
        $versions = [3 => 103, 2 => 102, 1 => 101];

        $this->expectException(\invalid_parameter_exception::class);
        \stack_question_xml_compare::get_compare_version(4, $versions, 3);
    }

    public function test_get_display_mode_defaults_to_unified(): void {
        $this->assertSame(
            \stack_question_xml_compare::DISPLAY_UNIFIED,
            \stack_question_xml_compare::get_display_mode(null)
        );
        $this->assertSame(
            \stack_question_xml_compare::DISPLAY_UNIFIED,
            \stack_question_xml_compare::get_display_mode('')
        );
    }

    public function test_get_display_mode_accepts_split_and_unified(): void {
        $this->assertSame(
            \stack_question_xml_compare::DISPLAY_SPLIT,
            \stack_question_xml_compare::get_display_mode('split')
        );
        $this->assertSame(
            \stack_question_xml_compare::DISPLAY_UNIFIED,
            \stack_question_xml_compare::get_display_mode('unified')
        );
    }

    public function test_get_display_mode_rejects_unknown_mode(): void {
        $this->expectException(\invalid_parameter_exception::class);
        \stack_question_xml_compare::get_display_mode('sideways');
    }

    public function test_toggle_display_mode_returns_the_other_mode(): void {
        $this->assertSame(
            \stack_question_xml_compare::DISPLAY_SPLIT,
            \stack_question_xml_compare::toggle_display_mode(\stack_question_xml_compare::DISPLAY_UNIFIED)
        );
        $this->assertSame(
            \stack_question_xml_compare::DISPLAY_UNIFIED,
            \stack_question_xml_compare::toggle_display_mode(\stack_question_xml_compare::DISPLAY_SPLIT)
        );
    }

    public function test_version_options_mark_selected_version(): void {
        $options = \stack_question_xml_compare::version_options([3 => 103, 2 => 102, 1 => 101], 2);

        $this->assertCount(3, $options);
        $this->assertSame(3, $options[0]->version);
        $this->assertFalse($options[0]->selected);
        $this->assertSame(2, $options[1]->version);
        $this->assertTrue($options[1]->selected);
        $this->assertSame(1, $options[2]->version);
        $this->assertFalse($options[2]->selected);
    }

    public function test_lines_normalises_newline_styles_and_preserves_trailing_empty_line(): void {
        $lines = \stack_question_xml_compare::lines("one\r\ntwo\rthree\n");

        $this->assertSame(['one', 'two', 'three', ''], $lines);
    }

    public function test_compare_rows_identical_xml_keeps_line_numbers_on_both_sides(): void {
        $rows = \stack_question_xml_compare::compare_rows("one\ntwo", "one\ntwo");

        $this->assertCount(2, $rows);
        $this->assertSame('same', $rows[0]->type);
        $this->assertSame(1, $rows[0]->currentline);
        $this->assertSame(1, $rows[0]->compareline);
        $this->assertSame('same', $rows[1]->type);
        $this->assertSame(2, $rows[1]->currentline);
        $this->assertSame(2, $rows[1]->compareline);
    }

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

    public function test_compare_rows_pairs_change_blocks_and_marks_surplus_current_lines_as_added(): void {
        $current = "before\nnew first\nnew second\nafter";
        $compared = "before\nold first\nafter";

        $rows = \stack_question_xml_compare::compare_rows($current, $compared);

        $this->assertSame('changed', $rows[1]->type);
        $this->assertSame('new first', $rows[1]->currenttext);
        $this->assertSame('old first', $rows[1]->comparetext);
        $this->assertSame('added', $rows[2]->type);
        $this->assertSame('new second', $rows[2]->currenttext);
        $this->assertNull($rows[2]->comparetext);
    }

    public function test_compare_rows_pairs_change_blocks_and_marks_surplus_compared_lines_as_deleted(): void {
        $current = "before\nnew first\nafter";
        $compared = "before\nold first\nold second\nafter";

        $rows = \stack_question_xml_compare::compare_rows($current, $compared);

        $this->assertSame('changed', $rows[1]->type);
        $this->assertSame('new first', $rows[1]->currenttext);
        $this->assertSame('old first', $rows[1]->comparetext);
        $this->assertSame('deleted', $rows[2]->type);
        $this->assertNull($rows[2]->currenttext);
        $this->assertSame('old second', $rows[2]->comparetext);
    }

    public function test_compare_rows_handles_added_and_deleted_lines_at_document_edges(): void {
        $current = "current first\nsame\ncurrent last";
        $compared = "same\ncompared last";

        $rows = \stack_question_xml_compare::compare_rows($current, $compared);

        $this->assertSame('added', $rows[0]->type);
        $this->assertSame('current first', $rows[0]->currenttext);
        $this->assertSame('same', $rows[1]->type);
        $this->assertSame('changed', $rows[2]->type);
        $this->assertSame('current last', $rows[2]->currenttext);
        $this->assertSame('compared last', $rows[2]->comparetext);
    }

    public function test_diff_rows_escapes_unchanged_and_added_xml_text(): void {
        $rows = \stack_question_xml_compare::diff_rows("<tag>& value</tag>\n<new>", "<tag>& value</tag>");

        $this->assertSame('&lt;tag&gt;&amp; value&lt;/tag&gt;', $rows[0]->currenthtml);
        $this->assertSame('&lt;tag&gt;&amp; value&lt;/tag&gt;', $rows[0]->comparehtml);
        $this->assertSame('added', $rows[1]->type);
        $this->assertSame('&lt;new&gt;', $rows[1]->currenthtml);
        $this->assertSame('', $rows[1]->comparehtml);
        $this->assertStringContainsString('stack-xml-compare-empty', $rows[1]->compareclass);
    }

    public function test_diff_rows_highlights_inline_edits_and_deletions(): void {
        $rows = \stack_question_xml_compare::diff_rows('value new tail', 'value old tail');

        $this->assertCount(2, $rows);
        $this->assertEquals('deleted', $rows[0]->type);
        $this->assertStringContainsString('stack-xml-compare-inline-deleted', $rows[0]->currenthtml);
        $this->assertStringContainsString('old', strip_tags($rows[0]->currenthtml));
        $this->assertSame('', $rows[0]->comparehtml);
        $this->assertEquals('added', $rows[1]->type);
        $this->assertStringContainsString('stack-xml-compare-inline-added', $rows[1]->currenthtml);
        $this->assertStringContainsString('new', strip_tags($rows[1]->currenthtml));
        $this->assertSame('', $rows[1]->comparehtml);
    }

    public function test_diff_rows_split_mode_keeps_changed_lines_side_by_side(): void {
        $rows = \stack_question_xml_compare::diff_rows(
            'value new tail',
            'value old tail',
            \stack_question_xml_compare::DISPLAY_SPLIT
        );

        $this->assertCount(1, $rows);
        $this->assertEquals('changed', $rows[0]->type);
        $this->assertStringContainsString('stack-xml-compare-inline-added', $rows[0]->currenthtml);
        $this->assertStringContainsString('new', strip_tags($rows[0]->currenthtml));
        $this->assertStringContainsString('stack-xml-compare-inline-deleted', $rows[0]->comparehtml);
        $this->assertStringContainsString('old', strip_tags($rows[0]->comparehtml));
    }

    public function test_diff_rows_split_mode_keeps_unchanged_compared_content(): void {
        $rows = \stack_question_xml_compare::diff_rows(
            '<quiz>',
            '<quiz>',
            \stack_question_xml_compare::DISPLAY_SPLIT
        );

        $this->assertSame('&lt;quiz&gt;', $rows[0]->currenthtml);
        $this->assertSame('&lt;quiz&gt;', $rows[0]->comparehtml);
        $this->assertStringNotContainsString('stack-xml-compare-empty', $rows[0]->compareclass);
    }

    public function test_diff_rows_split_mode_shows_deleted_only_content_in_compared_column(): void {
        $rows = \stack_question_xml_compare::diff_rows(
            "same\nlast",
            "same\ndeleted\nlast",
            \stack_question_xml_compare::DISPLAY_SPLIT
        );

        $this->assertEquals('deleted', $rows[1]->type);
        $this->assertSame('', $rows[1]->currenthtml);
        $this->assertStringContainsString('stack-xml-compare-empty', $rows[1]->currentclass);
        $this->assertStringContainsString('deleted', $rows[1]->comparehtml);
        $this->assertStringContainsString('stack-xml-compare-inline-deleted', $rows[1]->comparehtml);
    }

    public function test_diff_rows_escapes_changed_xml_text_inside_inline_highlights(): void {
        $rows = \stack_question_xml_compare::diff_rows('<tag attr="new">', '<tag attr="old">');

        $this->assertSame('deleted', $rows[0]->type);
        $this->assertSame('&lt;tag attr=&quot;old&quot;&gt;', strip_tags($rows[0]->currenthtml));
        $this->assertStringNotContainsString('<tag attr=', $rows[0]->currenthtml);
        $this->assertSame('added', $rows[1]->type);
        $this->assertStringContainsString('&lt;tag attr=&quot;', $rows[1]->currenthtml);
        $this->assertSame('&lt;tag attr=&quot;new&quot;&gt;', strip_tags($rows[1]->currenthtml));
        $this->assertStringNotContainsString('<tag attr=', $rows[1]->currenthtml);
    }

    public function test_diff_rows_shows_deleted_only_content_in_current_code_column(): void {
        $rows = \stack_question_xml_compare::diff_rows("same\nlast", "same\ndeleted\nlast");

        $this->assertEquals('deleted', $rows[1]->type);
        $this->assertStringContainsString('deleted', $rows[1]->currenthtml);
        $this->assertStringContainsString('stack-xml-compare-inline-deleted', $rows[1]->currenthtml);
        $this->assertSame('', $rows[1]->comparehtml);
        $this->assertStringContainsString('stack-xml-compare-empty', $rows[1]->compareclass);
    }

    public function test_inline_changed_separate_marks_additions_and_deletions_on_separate_lines(): void {
        [$currenthtml, $comparehtml] = \stack_question_xml_compare::inline_changed_separate(
            'value new tail',
            'value old tail'
        );

        $this->assertStringContainsString('stack-xml-compare-inline-added', $currenthtml);
        $this->assertStringNotContainsString('stack-xml-compare-inline-deleted', $currenthtml);
        $this->assertSame('value new tail', strip_tags($currenthtml));
        $this->assertStringContainsString('stack-xml-compare-inline-deleted', $comparehtml);
        $this->assertStringNotContainsString('stack-xml-compare-inline-added', $comparehtml);
        $this->assertSame('value old tail', strip_tags($comparehtml));
    }

    public function test_inline_changed_marks_insertions_only_on_current_side(): void {
        [$currenthtml, $comparehtml] = \stack_question_xml_compare::inline_changed('abcXYZdef', 'abcdef');

        $this->assertStringContainsString('stack-xml-compare-inline-added', $currenthtml);
        $this->assertSame('abcXYZdef', strip_tags($currenthtml));
        $this->assertStringNotContainsString('stack-xml-compare-inline-deleted', $currenthtml);
        $this->assertSame('abcdef', $comparehtml);
    }

    public function test_inline_changed_marks_deletions_on_both_sides(): void {
        [$currenthtml, $comparehtml] = \stack_question_xml_compare::inline_changed('abcdef', 'abcXYZdef');

        $this->assertStringContainsString('stack-xml-compare-inline-missing', $currenthtml);
        $this->assertSame('abcXYZdef', strip_tags($currenthtml));
        $this->assertStringContainsString('stack-xml-compare-inline-deleted', $comparehtml);
        $this->assertSame('abcXYZdef', strip_tags($comparehtml));
    }

    public function test_inline_changed_region_marks_middle_changes_for_long_lines(): void {
        [$currenthtml, $comparehtml] = \stack_question_xml_compare::inline_changed_region(
            str_split('prefix-CURRENT-suffix'),
            str_split('prefix-COMPARED-suffix')
        );

        $this->assertStringStartsWith('prefix-', $currenthtml);
        $this->assertStringEndsWith('-suffix', $currenthtml);
        $this->assertStringContainsString('stack-xml-compare-inline-added', $currenthtml);
        $this->assertStringContainsString('URRENT', $currenthtml);
        $this->assertStringContainsString('stack-xml-compare-inline-missing', $currenthtml);
        $this->assertStringContainsString('OMPARED', $currenthtml);
        $this->assertStringContainsString('stack-xml-compare-inline-deleted', $comparehtml);
    }

    public function test_inline_changed_separate_region_marks_middle_changes_for_long_lines(): void {
        [$currenthtml, $comparehtml] = \stack_question_xml_compare::inline_changed_separate_region(
            str_split('prefix-CURRENT-suffix'),
            str_split('prefix-COMPARED-suffix')
        );

        $this->assertStringStartsWith('prefix-', $currenthtml);
        $this->assertStringEndsWith('-suffix', $currenthtml);
        $this->assertStringContainsString('stack-xml-compare-inline-added', $currenthtml);
        $this->assertStringContainsString('URRENT', $currenthtml);
        $this->assertStringNotContainsString('stack-xml-compare-inline-deleted', $currenthtml);
        $this->assertStringStartsWith('prefix-', $comparehtml);
        $this->assertStringEndsWith('-suffix', $comparehtml);
        $this->assertStringContainsString('stack-xml-compare-inline-deleted', $comparehtml);
        $this->assertStringContainsString('OMPARED', $comparehtml);
        $this->assertStringNotContainsString('stack-xml-compare-inline-added', $comparehtml);
    }

    public function test_inline_changed_uses_long_line_fallback(): void {
        $current = str_repeat('a', 201) . 'current';
        $compared = str_repeat('a', 201) . 'compared';

        [$currenthtml, $comparehtml] = \stack_question_xml_compare::inline_changed($current, $compared);

        $this->assertStringContainsString('urrent', $currenthtml);
        $this->assertStringContainsString('ompared', $currenthtml);
        $this->assertStringContainsString('stack-xml-compare-inline-added', $currenthtml);
        $this->assertStringContainsString('stack-xml-compare-inline-deleted', $comparehtml);
    }

    public function test_chars_handles_multibyte_characters(): void {
        $this->assertSame(['a', '£', 'b'], \stack_question_xml_compare::chars('a£b'));
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

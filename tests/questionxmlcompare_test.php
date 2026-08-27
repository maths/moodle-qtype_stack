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

    public function test_get_compare_version_defaults_to_previous_selected_version(): void {
        $versions = [3 => 103, 2 => 102, 1 => 101];

        $this->assertSame(1, \stack_question_xml_compare::get_compare_version(null, $versions, 2));
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

    public function test_get_current_version_defaults_to_latest_version(): void {
        $versions = [3 => 103, 2 => 102, 1 => 101];

        $this->assertSame(3, \stack_question_xml_compare::get_current_version(null, $versions, 3));
    }

    public function test_get_current_version_accepts_requested_version(): void {
        $versions = [3 => 103, 2 => 102, 1 => 101];

        $this->assertSame(1, \stack_question_xml_compare::get_current_version(1, $versions, 3));
    }

    public function test_get_current_version_rejects_unknown_version(): void {
        $versions = [3 => 103, 2 => 102, 1 => 101];

        $this->expectException(\invalid_parameter_exception::class);
        \stack_question_xml_compare::get_current_version(4, $versions, 3);
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

    public function test_get_display_mode_defaults_to_unified_for_unknown_mode(): void {
        $this->assertSame(
            \stack_question_xml_compare::DISPLAY_UNIFIED,
            \stack_question_xml_compare::get_display_mode('sideways')
        );
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

    public function test_get_diff_only_reads_requested_filter(): void {
        $this->assertFalse(\stack_question_xml_compare::get_diff_only(null));
        $this->assertFalse(\stack_question_xml_compare::get_diff_only(0));
        $this->assertTrue(\stack_question_xml_compare::get_diff_only(1));
    }

    public function test_toggle_diff_only_returns_the_other_url_value(): void {
        $this->assertSame(
            \stack_question_xml_compare::FILTER_DIFFERENCES,
            \stack_question_xml_compare::toggle_diff_only(false)
        );
        $this->assertSame(
            \stack_question_xml_compare::FILTER_ALL,
            \stack_question_xml_compare::toggle_diff_only(true)
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

    public function test_version_options_adds_disabled_selected_file_option_when_requested(): void {
        $options = \stack_question_xml_compare::version_options([3 => 103, 2 => 102, 1 => 101], 2, true);

        $this->assertCount(4, $options);
        $this->assertSame('Selected file', $options[0]->label);
        $this->assertSame('', $options[0]->version);
        $this->assertTrue($options[0]->selected);
        $this->assertTrue($options[0]->disabled);
        $this->assertSame(3, $options[1]->version);
        $this->assertFalse($options[1]->selected);
        $this->assertSame(2, $options[2]->version);
        $this->assertFalse($options[2]->selected);
        $this->assertSame(1, $options[3]->version);
    }

    public function test_question_options_mark_selected_question(): void {
        $question1 = (object) [
            'id' => 10,
            'name' => 'Stack question',
            'qtype' => 'stack',
            'version' => 2,
            'questionbankentryid' => 100,
        ];
        $question2 = (object) [
            'id' => 20,
            'name' => 'True false question',
            'qtype' => 'truefalse',
            'version' => 1,
            'questionbankentryid' => 200,
        ];

        $options = \stack_question_xml_compare::question_options([$question1, $question2], 200);

        $this->assertCount(2, $options);
        $this->assertSame(10, $options[0]->id);
        $this->assertSame('Stack question', $options[0]->label);
        $this->assertFalse($options[0]->selected);
        $this->assertSame(20, $options[1]->id);
        $this->assertSame('True false question', $options[1]->label);
        $this->assertTrue($options[1]->selected);
    }

    public function test_questions_in_category_returns_latest_questions_for_all_question_types(): void {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $category = $generator->create_question_category();
        $stackquestion = $generator->create_question(
            'stack',
            'test3',
            ['category' => $category->id, 'name' => 'Stack question']
        );
        $truefalsequestion = $generator->create_question(
            'truefalse',
            null,
            ['category' => $category->id, 'name' => 'True false question']
        );

        $selectedquestionbankentryid = $DB->get_field(
            'question_versions',
            'questionbankentryid',
            ['questionid' => $truefalsequestion->id],
            MUST_EXIST
        );

        $options = \stack_question_xml_compare::questions_in_category($category->id, $selectedquestionbankentryid);

        $this->assertCount(2, $options);
        $this->assertEqualsCanonicalizing([$stackquestion->id, $truefalsequestion->id], array_column($options, 'id'));
        $selected = array_values(array_filter($options, fn($option) => $option->selected));
        $this->assertCount(1, $selected);
        $this->assertSame($truefalsequestion->id, $selected[0]->id);
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

    public function test_diff_rows_can_show_only_changed_blocks_with_context_in_unified_mode(): void {
        $rows = \stack_question_xml_compare::diff_rows("same\nnew\nsame", "same\nold\nsame", 'unified', true);

        $this->assertCount(4, $rows);
        $this->assertSame('same', $rows[0]->type);
        $this->assertSame('deleted', $rows[1]->type);
        $this->assertSame('added', $rows[2]->type);
        $this->assertSame('same', $rows[3]->type);
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

    public function test_diff_rows_can_show_only_changed_blocks_with_context_in_split_mode(): void {
        $rows = \stack_question_xml_compare::diff_rows(
            "same\nnew\nsame",
            "same\nold\nsame",
            \stack_question_xml_compare::DISPLAY_SPLIT,
            true
        );

        $this->assertCount(3, $rows);
        $this->assertSame('same', $rows[0]->type);
        $this->assertSame('changed', $rows[1]->type);
        $this->assertSame(2, $rows[1]->currentline);
        $this->assertSame(2, $rows[1]->compareline);
        $this->assertSame('same', $rows[2]->type);
    }

    public function test_diff_rows_adds_separator_between_compacted_changed_blocks(): void {
        $rows = \stack_question_xml_compare::diff_rows(
            "a\nnew 1\nb\nc\nnew 2\nd",
            "a\nold 1\nb\nc\nold 2\nd",
            \stack_question_xml_compare::DISPLAY_SPLIT,
            true
        );

        $this->assertSame('same', $rows[0]->type);
        $this->assertSame('changed', $rows[1]->type);
        $this->assertSame('same', $rows[2]->type);
        $this->assertSame('separator', $rows[3]->type);
        $this->assertSame('...', $rows[3]->currenthtml);
        $this->assertSame('...', $rows[3]->comparehtml);
        $this->assertSame('same', $rows[4]->type);
        $this->assertSame('changed', $rows[5]->type);
        $this->assertSame('same', $rows[6]->type);
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

    public function test_inline_changed_separate_keeps_cdata_sections_atomic(): void {
        $current = '<text><![CDATA[<p>Remember that \(x^0=1\) for all \(x \neq 0\). Therefore \({@e@}^0=1\).</p>]]></text>';
        $compare = '<text></text>';

        [$currenthtml, $comparehtml] = \stack_question_xml_compare::inline_changed_separate($current, $compare);

        $this->assertSame($current, html_entity_decode(strip_tags($currenthtml)));
        $this->assertSame($compare, html_entity_decode(strip_tags($comparehtml)));
        $this->assertStringContainsString(
            '<span class="stack-xml-compare-inline-added">&lt;![CDATA[&lt;p&gt;Remember that \(x^0=1\) for all \(x \neq 0\). ' .
            'Therefore \({@e@}^0=1\).&lt;/p&gt;]]&gt;</span>',
            $currenthtml
        );
        $this->assertSame(0, substr_count($comparehtml, 'stack-xml-compare-inline-deleted'));
    }

    public function test_inline_changed_separate_prefers_word_insertions_over_character_fragments(): void {
        [$currenthtml, $comparehtml] = \stack_question_xml_compare::inline_changed_separate(
            'corresponding eigenvectors',
            'corresponding hello eigenvectors'
        );

        $this->assertSame('corresponding eigenvectors', html_entity_decode(strip_tags($currenthtml)));
        $this->assertSame('corresponding hello eigenvectors', html_entity_decode(strip_tags($comparehtml)));
        $this->assertSame(0, substr_count($currenthtml, 'stack-xml-compare-inline-added'));
        $this->assertSame(0, substr_count($currenthtml, 'stack-xml-compare-inline-deleted'));
        $this->assertStringContainsString(
            '<span class="stack-xml-compare-inline-deleted">hello </span>',
            $comparehtml
        );
        $this->assertSame(0, substr_count($comparehtml, 'stack-xml-compare-inline-added'));
    }

    public function test_inline_changed_separate_highlights_comment_body_changes(): void {
        $current = '<text><!-- question: 445  --></text>';
        $compare = '<text><!-- question: 411  --></text>';

        [$currenthtml, $comparehtml] = \stack_question_xml_compare::inline_changed_separate($current, $compare);

        $this->assertSame($current, html_entity_decode(strip_tags($currenthtml)));
        $this->assertSame($compare, html_entity_decode(strip_tags($comparehtml)));
        $this->assertStringContainsString('&lt;!-- question: ', $currenthtml);
        $this->assertStringContainsString('stack-xml-compare-inline-added">445</span>', $currenthtml);
        $this->assertStringContainsString('stack-xml-compare-inline-deleted">411</span>', $comparehtml);
        $this->assertSame(1, substr_count($currenthtml, 'stack-xml-compare-inline-added'));
        $this->assertSame(1, substr_count($comparehtml, 'stack-xml-compare-inline-deleted'));
    }

    public function test_inline_changed_separate_keeps_comment_delimiters_atomic(): void {
        $current = '<text><!-- note --></text>';
        $compare = '<text></text>';

        [$currenthtml, $comparehtml] = \stack_question_xml_compare::inline_changed_separate($current, $compare);

        $this->assertSame($current, html_entity_decode(strip_tags($currenthtml)));
        $this->assertSame($compare, html_entity_decode(strip_tags($comparehtml)));
        $this->assertStringContainsString(
            '<span class="stack-xml-compare-inline-added">&lt;!-- note --&gt;</span>',
            $currenthtml
        );
        $this->assertSame(0, substr_count($comparehtml, 'stack-xml-compare-inline-deleted'));
    }

    public function test_inline_changed_separate_keeps_ascii_wrappers_atomic(): void {
        $current = '<text>[[ascii]]{@ta1@}[[/ascii]]</text>';
        $compare = '<text></text>';

        [$currenthtml, $comparehtml] = \stack_question_xml_compare::inline_changed_separate($current, $compare);

        $this->assertSame($current, html_entity_decode(strip_tags($currenthtml)));
        $this->assertSame($compare, html_entity_decode(strip_tags($comparehtml)));
        $this->assertStringContainsString(
            '<span class="stack-xml-compare-inline-added">[[ascii]]{@ta1@}[[/ascii]]</span>',
            $currenthtml
        );
        $this->assertSame(0, substr_count($comparehtml, 'stack-xml-compare-inline-deleted'));
    }

    public function test_inline_changed_separate_keeps_processing_instructions_atomic(): void {
        $current = '<text><?stack one?></text>';
        $compare = '<text><?stack two?></text>';

        [$currenthtml, $comparehtml] = \stack_question_xml_compare::inline_changed_separate($current, $compare);

        $this->assertSame($current, html_entity_decode(strip_tags($currenthtml)));
        $this->assertSame($compare, html_entity_decode(strip_tags($comparehtml)));
        $this->assertStringContainsString(
            '<span class="stack-xml-compare-inline-added">&lt;?stack one?&gt;</span>',
            $currenthtml
        );
        $this->assertStringContainsString(
            '<span class="stack-xml-compare-inline-deleted">&lt;?stack two?&gt;</span>',
            $comparehtml
        );
        $this->assertSame(1, substr_count($currenthtml, 'stack-xml-compare-inline-added'));
        $this->assertSame(1, substr_count($comparehtml, 'stack-xml-compare-inline-deleted'));
    }

    public function test_inline_changed_separate_keeps_entity_references_atomic(): void {
        $current = '<text>&alpha; value</text>';
        $compare = '<text>&beta; value</text>';

        [$currenthtml, $comparehtml] = \stack_question_xml_compare::inline_changed_separate($current, $compare);

        $this->assertSame($current, html_entity_decode(strip_tags($currenthtml)));
        $this->assertSame($compare, html_entity_decode(strip_tags($comparehtml)));
        $this->assertStringContainsString(
            '<span class="stack-xml-compare-inline-added">&amp;alpha;</span>',
            $currenthtml
        );
        $this->assertStringContainsString(
            '<span class="stack-xml-compare-inline-deleted">&amp;beta;</span>',
            $comparehtml
        );
        $this->assertSame(1, substr_count($currenthtml, 'stack-xml-compare-inline-added'));
        $this->assertSame(1, substr_count($comparehtml, 'stack-xml-compare-inline-deleted'));
    }

    public function test_inline_changed_separate_joins_changes_across_short_unchanged_gaps(): void {
        [$currenthtml, $comparehtml] = \stack_question_xml_compare::inline_changed_separate(
            'abcXXabcdeYYghi',
            'abc11abcde22ghi'
        );

        $this->assertSame(1, substr_count($currenthtml, 'stack-xml-compare-inline-added'));
        $this->assertSame(1, substr_count($comparehtml, 'stack-xml-compare-inline-deleted'));
        $this->assertStringContainsString('XXabcdeYY', strip_tags($currenthtml));
        $this->assertStringContainsString('11abcde22', strip_tags($comparehtml));
    }

    public function test_inline_changed_separate_keeps_changes_separate_across_six_character_gaps(): void {
        [$currenthtml, $comparehtml] = \stack_question_xml_compare::inline_changed_separate(
            'a!!!!!!b',
            'c!!!!!!d'
        );

        $this->assertSame(2, substr_count($currenthtml, 'stack-xml-compare-inline-added'));
        $this->assertSame(2, substr_count($comparehtml, 'stack-xml-compare-inline-deleted'));
        $this->assertSame('a!!!!!!b', strip_tags($currenthtml));
        $this->assertSame('c!!!!!!d', strip_tags($comparehtml));
    }

    public function test_diff_rows_flags_edited_lines_shorter_than_five_characters(): void {
        $rows = \stack_question_xml_compare::diff_rows(
            'abcd',
            'abxd',
            \stack_question_xml_compare::DISPLAY_SPLIT
        );

        $this->assertSame('changed', $rows[0]->type);
        $this->assertStringContainsString('stack-xml-compare-inline-added', $rows[0]->currenthtml);
        $this->assertStringContainsString('stack-xml-compare-inline-deleted', $rows[0]->comparehtml);
        $this->assertSame('abcd', strip_tags($rows[0]->currenthtml));
        $this->assertSame('abxd', strip_tags($rows[0]->comparehtml));
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

    public function test_question_form_params_strip_uploaded_file_drafts(): void {
        $params = \stack_question_xml_compare::question_form_params([
            'id' => 2,
            'currentfile' => 111,
            'comparefile' => 222,
            'display' => 'split',
        ]);

        $this->assertCount(2, $params);
        $this->assertEquals('id', $params[0]->name);
        $this->assertEquals('display', $params[1]->name);
    }

    public function test_export_question_version_xml_wraps_a_real_question_export(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $category = $generator->create_question_category();
        $question = $generator->create_question(
            'stack',
            'test3',
            ['category' => $category->id, 'name' => 'Stack question']
        );

        $question = \question_bank::load_question_data($question->id);

        $xml = \stack_question_xml_compare::export_question_version_xml($question);

        $this->assertIsString($xml);
        $this->assertStringStartsWith('<?xml version="1.0" encoding="UTF-8"?>' . "\n<quiz>\n", $xml);
        $this->assertStringEndsWith('</quiz>', $xml);
        $this->assertStringContainsString('<question type="stack">', $xml);
        $this->assertStringContainsString('<!-- question:', $xml);
    }

    public function test_draft_file_content_reads_current_users_filepicker_file(): void {
        global $USER;

        $this->resetAfterTest();
        $this->setAdminUser();

        $draftitemid = file_get_unused_draft_itemid();
        $fs = get_file_storage();
        $usercontext = \context_user::instance($USER->id);
        $fs->create_file_from_string([
            'contextid' => $usercontext->id,
            'component' => 'user',
            'filearea' => 'draft',
            'itemid' => $draftitemid,
            'filepath' => '/',
            'filename' => 'question.xml',
        ], '<quiz />');

        $this->assertSame('<quiz />', \stack_question_xml_compare::draft_file_content($draftitemid));
        $this->assertSame('question.xml', \stack_question_xml_compare::draft_file_name($draftitemid));
    }

    public function test_draft_file_content_reads_current_users_filepicker_file_for_compare_side(): void {
        global $USER;

        $this->resetAfterTest();
        $this->setAdminUser();

        $draftitemid = file_get_unused_draft_itemid();
        $fs = get_file_storage();
        $usercontext = \context_user::instance($USER->id);
        $fs->create_file_from_string([
            'contextid' => $usercontext->id,
            'component' => 'user',
            'filearea' => 'draft',
            'itemid' => $draftitemid,
            'filepath' => '/',
            'filename' => 'compared.xml',
        ], '<quiz><note>compare</note></quiz>');

        $this->assertSame('<quiz><note>compare</note></quiz>', \stack_question_xml_compare::draft_file_content($draftitemid));
        $this->assertSame('compared.xml', \stack_question_xml_compare::draft_file_name($draftitemid));
    }

    public function test_draft_file_content_returns_null_for_missing_file(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $this->assertNull(\stack_question_xml_compare::draft_file_content(123456789));
        $this->assertNull(\stack_question_xml_compare::draft_file_name(123456789));
    }

    public function test_draft_file_returns_null_for_empty_item_id(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $this->assertNull(\stack_question_xml_compare::draft_file(null));
        $this->assertNull(\stack_question_xml_compare::draft_file(0));
    }
}

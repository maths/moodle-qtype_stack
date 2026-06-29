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
 * PHPUnit tests for the [[ascii]] castext block.
 * @package    qtype_stack
 * @copyright  2026 University of Edinburgh.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

namespace qtype_stack;

use castext2_evaluatable;
use qtype_stack_testcase;
use stack_cas_session2;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../locallib.php');
require_once(__DIR__ . '/fixtures/test_base.php');
require_once(__DIR__ . '/../stack/cas/castext2/castext2_evaluatable.class.php');
require_once(__DIR__ . '/../stack/cas/cassession2.class.php');
require_once(__DIR__ . '/../stack/cas/castext2/blocks/iframe.block.php');
require_once(__DIR__ . '/../stack/cas/castext2/blocks/filter.block.php');
require_once(__DIR__ . '/../stack/cas/castext2/blocks/extractor.block.php');

use stack_cas_castext2_iframe;

/**
 * Tests for {@link stack_cas_castext2_ascii}.
 * @group qtype_stack
 * @covers \qtype_stack\stack_cas_castext2_ascii
 */
final class ascii_block_test extends qtype_stack_testcase {
    /**
     * Extract all MP_String values from a compiled MP_List.
     * @param \MP_List $compiled
     * @return array
     */
    private function get_string_items(\MP_List $compiled): array {
        $strings = [];
        foreach ($compiled->items as $item) {
            if ($item instanceof \MP_String) {
                $strings[] = $item->value;
            }
        }
        return $strings;
    }

    public function test_basic_ascii_block(): void {
        stack_cas_castext2_iframe::register_counter('///IFRAME_COUNT///');

        $raw = '[[ascii input="ans1"]][[/ascii]]';
        $expected = '<div style="width:100%;height:400px;" id="stack-iframe-holder-1"></div>';

        $at1 = castext2_evaluatable::make_from_source($raw, 'test-case');
        $session = new stack_cas_session2([$at1]);
        $session->instantiate();

        $this->assertEquals($expected, $at1->apply_placeholder_holder($at1->get_rendered()));
    }

    public function test_ascii_block_with_filter_and_extractor_children(): void {
        stack_cas_castext2_iframe::register_counter('///IFRAME_COUNT///');

        $raw = '[[ascii input="ans1"]]'
            . '[[filter type="markdown" transforms="aligneq,boldfilter"]][[/filter]]'
            . '[[extractor targetinput="ans2" type="lastexpr"]][[/extractor]]'
            . '[[/ascii]]';
        $expected = '<div style="width:100%;height:400px;" id="stack-iframe-holder-1"></div>';

        $at1 = castext2_evaluatable::make_from_source($raw, 'test-case');
        $session = new stack_cas_session2([$at1]);
        $session->instantiate();

        $this->assertTrue($at1->get_valid());
        $this->assertEquals($expected, $at1->apply_placeholder_holder($at1->get_rendered()));
    }

    public function test_ascii_compile_adds_default_filter_and_input_request(): void {
        $block = new \stack_cas_castext2_ascii(['input' => 'ans1'], []);
        $compiled = $block->compile(null, []);

        $this->assertInstanceOf(\MP_List::class, $compiled);
        $this->assertInstanceOf(\MP_String::class, $compiled->items[0]);
        $this->assertEquals('iframe', $compiled->items[0]->value);

        $this->assertInstanceOf(\MP_String::class, $compiled->items[1]);
        $xpars = json_decode($compiled->items[1]->value, true);
        $this->assertEquals('100%', $xpars['width']);
        $this->assertEquals('400px', $xpars['height']);
        $this->assertStringContainsString('STACK ASCII', $xpars['title']);

        $strings = $this->get_string_items($compiled);
        $joined = implode("\n", $strings);
        $this->assertStringContainsString('stack_js.request_access_to_input("ans1",true)', $joined);
        $expectedlinkcode = '{init(inputIds,[{"operation":"filter","type":"markdown","transforms":"asciimath,aligneq,minwrap"}]);}';
        $this->assertStringContainsString($expectedlinkcode, $joined);
        $this->assertStringContainsString(
            'id="asciiContainerRow" style="width:calc(100% - 20px);height:calc(100vh - 30px);min-height:calc(400px - 30px);"',
            $joined
        );
    }

    public function test_ascii_compile_without_input_parameter_uses_empty_input_requests(): void {
        $block = new \stack_cas_castext2_ascii([], []);
        $compiled = $block->compile(null, []);

        $this->assertInstanceOf(\MP_List::class, $compiled);

        $strings = $this->get_string_items($compiled);
        $joined = implode("\n", $strings);
        $this->assertStringContainsString('Promise.all([])', $joined);
        $this->assertStringNotContainsString('stack_js.request_access_to_input(', $joined);
        $this->assertStringContainsString('<textarea id="asciiSuppliedText" style="display:none;">', $joined);
        $this->assertStringContainsString('</textarea>', $joined);
        $expectedlinkcode = '{init(inputIds,[{"operation":"filter","type":"markdown","transforms":"asciimath,aligneq,minwrap"}]);}';
        $this->assertStringContainsString($expectedlinkcode, $joined);
    }

    public function test_ascii_compile_uses_child_filter_and_extractor_operations(): void {
        $filter = new \stack_cas_castext2_filter([
            'type' => 'markdown',
            'transforms' => 'aligneq',
            'display' => 'true',
        ]);
        $extractor = new \stack_cas_castext2_extractor([
            'type' => 'lastexpr',
            'targetinput' => 'ans2',
        ]);

        $block = new \stack_cas_castext2_ascii(
            ['input' => 'ans1', 'width' => '80%', 'height' => '300px'],
            [$filter, $extractor]
        );
        $compiled = $block->compile(null, []);

        $this->assertInstanceOf(\MP_List::class, $compiled);
        $xpars = json_decode($compiled->items[1]->value, true);
        $this->assertEquals('80%', $xpars['width']);
        $this->assertEquals('300px', $xpars['height']);

        $strings = $this->get_string_items($compiled);
        $joined = implode("\n", $strings);
        $this->assertStringContainsString('stack_js.request_access_to_input("ans1",true)', $joined);
        $this->assertStringContainsString('stack_js.request_access_to_input("ans2")', $joined);
        $expectedlinkcode = '{init(inputIds,[{"type":"markdown","transforms":"aligneq","display":"true","operation":"filter"}' .
            ',{"type":"lastexpr","targetinput":"ans2","operation":"extractor"}]);}';
        $this->assertStringContainsString($expectedlinkcode, $joined);
        $this->assertStringContainsString(
            'id="asciiContainerRow" style="width:calc(80% - 20px);height:calc(100vh - 30px);min-height:calc(300px - 30px);"',
            $joined
        );
        $this->assertStringNotContainsString('"transforms":"aligneq,boldfilter"', $joined);
    }

    public function test_ascii_compile_uses_child_filter_markdown_maths(): void {
        $filter = new \stack_cas_castext2_filter([
            'type' => 'markdown-math',
            'transforms' => 'aligneq',
            'display' => 'true',
        ]);
        $extractor = new \stack_cas_castext2_extractor([
            'type' => 'lastexpr',
            'targetinput' => 'ans2',
        ]);

        $block = new \stack_cas_castext2_ascii(
            ['input' => 'ans1', 'width' => '80%', 'height' => '300px'],
            [$filter, $extractor]
        );
        $compiled = $block->compile(null, []);

        $this->assertInstanceOf(\MP_List::class, $compiled);
        $xpars = json_decode($compiled->items[1]->value, true);
        $this->assertEquals('80%', $xpars['width']);
        $this->assertEquals('300px', $xpars['height']);

        $strings = $this->get_string_items($compiled);
        $joined = implode("\n", $strings);
        $this->assertStringContainsString('stack_js.request_access_to_input("ans1",true)', $joined);
        $this->assertStringContainsString('stack_js.request_access_to_input("ans2")', $joined);
        $expectedlinkcode = '{init(inputIds,[{"type":"markdown","transforms":"asciimath,aligneq,minwrap",' .
            '"display":"true","operation":"filter"}' .
            ',{"type":"lastexpr","targetinput":"ans2","operation":"extractor"}]);}';
        $this->assertStringContainsString($expectedlinkcode, $joined);
        $this->assertStringContainsString(
            'id="asciiContainerRow" style="width:calc(80% - 20px);height:calc(100vh - 30px);min-height:calc(300px - 30px);"',
            $joined
        );
        $this->assertStringNotContainsString('"transforms":"aligneq,boldfilter"', $joined);
    }

    public function test_ascii_validate_width_unit_and_number(): void {
        $valid = '[[ascii input="ans1" width="500px"]][[/ascii]]';
        $invalidunit = '[[ascii input="ans1" width="500bad"]][[/ascii]]';
        $invalidnum = '[[ascii input="ans1" width="-5px"]][[/ascii]]';

        $atvalid = castext2_evaluatable::make_from_source($valid, 'test-case');
        $this->assertTrue($atvalid->get_valid());

        $atunit = castext2_evaluatable::make_from_source($invalidunit, 'test-case');
        $this->assertFalse($atunit->get_valid());
        $this->assertEquals(stack_string('stackBlock_ascii_width'), $atunit->get_errors());

        $atnum = castext2_evaluatable::make_from_source($invalidnum, 'test-case');
        $session = new stack_cas_session2([$atnum]);
        $this->assertFalse($atnum->get_valid());
        $this->assertEquals(stack_string('stackBlock_ascii_width_num'), $atnum->get_errors());
    }

    public function test_ascii_validate_height_unit_and_number(): void {
        $valid = '[[ascii input="ans1" height="500px"]][[/ascii]]';
        $invalidunit = '[[ascii input="ans1" height="500bad"]][[/ascii]]';
        $invalidnum = '[[ascii input="ans1" height="-5px"]][[/ascii]]';

        $atvalid = castext2_evaluatable::make_from_source($valid, 'test-case');
        $this->assertTrue($atvalid->get_valid());

        $atunit = castext2_evaluatable::make_from_source($invalidunit, 'test-case');
        $session = new stack_cas_session2([$atunit]);
        $this->assertFalse($atunit->get_valid());
        $this->assertEquals(stack_string('stackBlock_ascii_height'), $atunit->get_errors());

        $atnum = castext2_evaluatable::make_from_source($invalidnum, 'test-case');
        $session = new stack_cas_session2([$atnum]);
        $this->assertFalse($atnum->get_valid());
        $this->assertEquals(stack_string('stackBlock_ascii_height_num'), $atnum->get_errors());
    }

    public function test_ascii_aspect_ratio_dimension_rules(): void {
        $overdefined = '[[ascii input="ans1" width="100%" height="400px" aspect-ratio="1"]][[/ascii]]';
        $underdefined = '[[ascii input="ans1" aspect-ratio="1"]][[/ascii]]';

        $atover = castext2_evaluatable::make_from_source($overdefined, 'test-case');
        $this->assertFalse($atover->get_valid());
        $this->assertEquals(stack_string('stackBlock_ascii_overdefined_dimension'), $atover->get_errors());

        $atunder = castext2_evaluatable::make_from_source($underdefined, 'test-case');
        $this->assertFalse($atunder->get_valid());
        $this->assertEquals(stack_string('stackBlock_ascii_underdefined_dimension'), $atunder->get_errors());
    }

    public function test_ascii_unknown_param_rejected(): void {
        $raw = '[[ascii input="ans1" bad_param="x"]][[/ascii]]';

        $at1 = castext2_evaluatable::make_from_source($raw, 'test-case');

        $this->assertFalse($at1->get_valid());
        $this->assertStringContainsString(stack_string('stackBlock_ascii_unknown_param', 'bad_param'), $at1->get_errors());
        $this->assertStringContainsString(stack_string('stackBlock_ascii_param', [
            'param' => 'width, height, aspect-ratio, input, hidden',
        ]), $at1->get_errors());
    }
}

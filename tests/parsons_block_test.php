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

namespace qtype_stack;

use castext2_evaluatable;
use castext2_parser_utils;
use qtype_stack_testcase;
use stack_ast_container;
use stack_cas_keyval;
use stack_cas_security;
use stack_cas_session2;
use stack_maths;
use stack_options;
use stack_secure_loader;
use stack_multilang;
use function stack_ast_container_silent\is_int;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../locallib.php');
require_once(__DIR__ . '/fixtures/test_base.php');
require_once(__DIR__ . '/../stack/cas/castext2/castext2_evaluatable.class.php');
require_once(__DIR__ . '/../stack/cas/castext2/blocks/iframe.block.php');
require_once(__DIR__ . '/../stack/cas/castext2/utils.php');
require_once(__DIR__ . '/../stack/cas/keyval.class.php');
require_once(__DIR__ . '/../stack/cas/cassession2.class.php');
require_once(__DIR__ . '/../stack/cas/secure_loader.class.php');
require_once(__DIR__ . '/../lang/multilang.php');

use stack_cas_castext2_iframe;

// Unit tests for {@link stack_cas_castext2_parsons}.

/**
 * @group qtype_stack
 * @group qtype_stack_castext_module
 */
class parsons_block_test extends qtype_stack_testcase {

    /**
     * @covers \qtype_stack\stack_cas_castext2_parsons
     */
    public function test_basic_parsons_block() {
        // This needs reset as the class variable must be being upped in a different
        // test and the value is bleeding through.
        stack_cas_castext2_iframe::register_counter('///IFRAME_COUNT///');
        $raw = '[[parsons]]{' .
            '"1":"Assume that \\(n\\) is odd.",' .
            '"2":"Then there exists an \\(m\\in\\mathbb{Z}\\) such that \\(n=2m+1\\).", ' .
            '"3":"\\[ n^2 = (2m+1)^2 = 2(2m^2+2m)+1.\\]", ' .
            '"4":"Define \\(M=2m^2+2m\\in\\mathbb{Z}\\) then \\(n^2=2M+1\\).", ' .
            '} [[/parsons]]';
        $expected = '<div style="width:100%;height:400px;" id="stack-iframe-holder-1"></div>';

        $at1 = castext2_evaluatable::make_from_source($raw, 'test-case');
        $session = new stack_cas_session2([$at1]);
        $session->instantiate();
        $this->assertEquals($expected, $at1->apply_placeholder_holder($at1->get_rendered()));
    }

    /**
     * @covers \qtype_stack\stack_cas_castext2_parsons
     */
    public function test_parsons_validate_height_unit() {
        $validunits = [
            'vmin', 'vmax', 'rem', 'em', 'ex', 'px', 'cm', 'mm',
            'in', 'pt', 'pc', 'ch', 'vh', 'vw', '%',
        ];
        $invalidunits = ['VMIN', 'gjd', '50', 'Px', 'pct', ''];
        foreach ($validunits as $unit) {
            $raw = '[[parsons height="500' . $unit . '"]]{' .
                '"1":"Assume that \\(n\\) is odd.",' .
                '"2":"Then there exists an \\(m\\in\\mathbb{Z}\\) such that \\(n=2m+1\\).", ' .
                '"3":"\\[ n^2 = (2m+1)^2 = 2(2m^2+2m)+1.\\]", ' .
                '"4":"Define \\(M=2m^2+2m\\in\\mathbb{Z}\\) then \\(n^2=2M+1\\).", ' .
                '} [[/parsons]]';

            $at1 = castext2_evaluatable::make_from_source($raw, 'test-case');
            $session = new stack_cas_session2([$at1]);
            $this->assertTrue($at1->get_valid());
        }
        foreach ($invalidunits as $unit) {
            $raw = '[[parsons height="500' . $unit . '"]]{' .
                '"1":"Assume that \\(n\\) is odd.",' .
                '"2":"Then there exists an \\(m\\in\\mathbb{Z}\\) such that \\(n=2m+1\\).", ' .
                '"3":"\\[ n^2 = (2m+1)^2 = 2(2m^2+2m)+1.\\]", ' .
                '"4":"Define \\(M=2m^2+2m\\in\\mathbb{Z}\\) then \\(n^2=2M+1\\).", ' .
                '} [[/parsons]]';

            $at1 = castext2_evaluatable::make_from_source($raw, 'test-case');
            $session = new stack_cas_session2([$at1]);
            $this->assertFalse($at1->get_valid());
            $this->assertEquals(stack_string('stackBlock_parsons_height'), $at1->get_errors());
        }
    }

    /**
     * @covers \qtype_stack\stack_cas_castext2_parsons
     */
    public function test_parsons_validate_width_unit() {
        $validunits = [
            'vmin', 'vmax', 'rem', 'em', 'ex', 'px', 'cm', 'mm',
            'in', 'pt', 'pc', 'ch', 'vh', 'vw', '%',
        ];
        $invalidunits = ['VMIN', 'gjd', '50', 'Px', 'pct', ''];
        foreach ($validunits as $unit) {
            $raw = '[[parsons width="500' . $unit . '"]]{' .
                '"1":"Assume that \\(n\\) is odd.",' .
                '"2":"Then there exists an \\(m\\in\\mathbb{Z}\\) such that \\(n=2m+1\\).", ' .
                '"3":"\\[ n^2 = (2m+1)^2 = 2(2m^2+2m)+1.\\]", ' .
                '"4":"Define \\(M=2m^2+2m\\in\\mathbb{Z}\\) then \\(n^2=2M+1\\).", ' .
                '} [[/parsons]]';

            $at1 = castext2_evaluatable::make_from_source($raw, 'test-case');
            $session = new stack_cas_session2([$at1]);
            $this->assertTrue($at1->get_valid());
        }
        foreach ($invalidunits as $unit) {
            $raw = '[[parsons width="500' . $unit . '"]]{' .
                '"1":"Assume that \\(n\\) is odd.",' .
                '"2":"Then there exists an \\(m\\in\\mathbb{Z}\\) such that \\(n=2m+1\\).", ' .
                '"3":"\\[ n^2 = (2m+1)^2 = 2(2m^2+2m)+1.\\]", ' .
                '"4":"Define \\(M=2m^2+2m\\in\\mathbb{Z}\\) then \\(n^2=2M+1\\).", ' .
                '} [[/parsons]]';

            $at1 = castext2_evaluatable::make_from_source($raw, 'test-case');
            $session = new stack_cas_session2([$at1]);
            $this->assertFalse($at1->get_valid());
            $this->assertEquals(stack_string('stackBlock_parsons_width'), $at1->get_errors());
        }
    }

    /**
     * @covers \qtype_stack\stack_cas_castext2_parsons
     */
    public function test_parsons_validate_height_num() {
        $validheights = ['500', '4', '432.5'];
        $invalidheights = ['-5', 'ghjd', ''];

        foreach ($validheights as $ht) {
            $raw = '[[parsons height="' . $ht . 'px"]]{' .
                '"1":"Assume that \\(n\\) is odd.",' .
                '"2":"Then there exists an \\(m\\in\\mathbb{Z}\\) such that \\(n=2m+1\\).", ' .
                '"3":"\\[ n^2 = (2m+1)^2 = 2(2m^2+2m)+1.\\]", ' .
                '"4":"Define \\(M=2m^2+2m\\in\\mathbb{Z}\\) then \\(n^2=2M+1\\).", ' .
                '} [[/parsons]]';

            $at1 = castext2_evaluatable::make_from_source($raw, 'test-case');
            $session = new stack_cas_session2([$at1]);
            $this->assertTrue($at1->get_valid());
        }
        foreach ($invalidheights as $ht) {
            $raw = '[[parsons height="' . $ht . 'px"]]{' .
                '"1":"Assume that \\(n\\) is odd.",' .
                '"2":"Then there exists an \\(m\\in\\mathbb{Z}\\) such that \\(n=2m+1\\).", ' .
                '"3":"\\[ n^2 = (2m+1)^2 = 2(2m^2+2m)+1.\\]", ' .
                '"4":"Define \\(M=2m^2+2m\\in\\mathbb{Z}\\) then \\(n^2=2M+1\\).", ' .
                '} [[/parsons]]';

            $at1 = castext2_evaluatable::make_from_source($raw, 'test-case');
            $session = new stack_cas_session2([$at1]);
            $this->assertFalse($at1->get_valid());
            $this->assertEquals(stack_string('stackBlock_parsons_height_num'), $at1->get_errors());
        }
    }

    /**
     * @covers \qtype_stack\stack_cas_castext2_parsons
     */
    public function test_parsons_validate_width_num() {
        $validversions = ['500', '4', '432.5'];
        $invalidversions = ['-5', 'ghjd', ''];

        foreach ($validversions as $vs) {
            $raw = '[[parsons width="' . $vs . 'px"]]{' .
                '"1":"Assume that \\(n\\) is odd.",' .
                '"2":"Then there exists an \\(m\\in\\mathbb{Z}\\) such that \\(n=2m+1\\).", ' .
                '"3":"\\[ n^2 = (2m+1)^2 = 2(2m^2+2m)+1.\\]", ' .
                '"4":"Define \\(M=2m^2+2m\\in\\mathbb{Z}\\) then \\(n^2=2M+1\\).", ' .
                '} [[/parsons]]';

            $at1 = castext2_evaluatable::make_from_source($raw, 'test-case');
            $session = new stack_cas_session2([$at1]);
            $this->assertTrue($at1->get_valid());
        }
        foreach ($invalidversions as $vs) {
            $raw = '[[parsons width="' . $vs . 'px"]]{' .
                '"1":"Assume that \\(n\\) is odd.",' .
                '"2":"Then there exists an \\(m\\in\\mathbb{Z}\\) such that \\(n=2m+1\\).", ' .
                '"3":"\\[ n^2 = (2m+1)^2 = 2(2m^2+2m)+1.\\]", ' .
                '"4":"Define \\(M=2m^2+2m\\in\\mathbb{Z}\\) then \\(n^2=2M+1\\).", ' .
                '} [[/parsons]]';

            $at1 = castext2_evaluatable::make_from_source($raw, 'test-case');
            $session = new stack_cas_session2([$at1]);
            $this->assertFalse($at1->get_valid());
            $this->assertEquals(stack_string('stackBlock_parsons_width_num'), $at1->get_errors());
        }
    }

    /**
     * @covers \qtype_stack\stack_cas_castext2_parsons
     */
    public function test_parsons_overdefined_dimensions_1() {
        $raw = '[[parsons height="500px" width="100%" aspect-ratio="1"]]{' .
            '"1":"Assume that \\(n\\) is odd.",' .
            '"2":"Then there exists an \\(m\\in\\mathbb{Z}\\) such that \\(n=2m+1\\).", ' .
            '"3":"\\[ n^2 = (2m+1)^2 = 2(2m^2+2m)+1.\\]", ' .
            '"4":"Define \\(M=2m^2+2m\\in\\mathbb{Z}\\) then \\(n^2=2M+1\\).", ' .
            '} [[/parsons]]';

        $at1 = castext2_evaluatable::make_from_source($raw, 'test-case');
        $session = new stack_cas_session2([$at1]);
        $this->assertFalse($at1->get_valid());
        $this->assertEquals(stack_string('stackBlock_parsons_overdefined_dimension'), $at1->get_errors());
    }

    /**
     * @covers \qtype_stack\stack_cas_castext2_parsons
     */
    public function test_parsons_underdefined_dimensions() {
        $raw = '[[parsons aspect-ratio="1"]]{' .
            '"1":"Assume that \\(n\\) is odd.",' .
            '"2":"Then there exists an \\(m\\in\\mathbb{Z}\\) such that \\(n=2m+1\\).", ' .
            '"3":"\\[ n^2 = (2m+1)^2 = 2(2m^2+2m)+1.\\]", ' .
            '"4":"Define \\(M=2m^2+2m\\in\\mathbb{Z}\\) then \\(n^2=2M+1\\).", ' .
            '} [[/parsons]]';

        $at1 = castext2_evaluatable::make_from_source($raw, 'test-case');
        $session = new stack_cas_session2([$at1]);
        $this->assertFalse($at1->get_valid());
        $this->assertEquals(stack_string('stackBlock_parsons_underdefined_dimension'), $at1->get_errors());
    }

    /**
     * @covers \qtype_stack\stack_cas_castext2_parsons
     */
    public function test_parsons_validate_version() {
        $validversions = ['cdn', 'local'];
        $invalidversions = ['-5', 'ghjd', ''];

        foreach ($validversions as $vs) {
            $raw = '[[parsons version="' . $vs . '"]]{' .
                '"1":"Assume that \\(n\\) is odd.",' .
                '"2":"Then there exists an \\(m\\in\\mathbb{Z}\\) such that \\(n=2m+1\\).", ' .
                '"3":"\\[ n^2 = (2m+1)^2 = 2(2m^2+2m)+1.\\]", ' .
                '"4":"Define \\(M=2m^2+2m\\in\\mathbb{Z}\\) then \\(n^2=2M+1\\).", ' .
                '} [[/parsons]]';

            $at1 = castext2_evaluatable::make_from_source($raw, 'test-case');
            $session = new stack_cas_session2([$at1]);
            $this->assertTrue($at1->get_valid());
        }
        foreach ($invalidversions as $vs) {
            $raw = '[[parsons version="' . $vs . '"]]{' .
                '"1":"Assume that \\(n\\) is odd.",' .
                '"2":"Then there exists an \\(m\\in\\mathbb{Z}\\) such that \\(n=2m+1\\).", ' .
                '"3":"\\[ n^2 = (2m+1)^2 = 2(2m^2+2m)+1.\\]", ' .
                '"4":"Define \\(M=2m^2+2m\\in\\mathbb{Z}\\) then \\(n^2=2M+1\\).", ' .
                '} [[/parsons]]';

            $at1 = castext2_evaluatable::make_from_source($raw, 'test-case');
            $session = new stack_cas_session2([$at1]);
            $this->assertFalse($at1->get_valid());
            $this->assertEquals(
                stack_string('stackBlock_parsons_unknown_named_version', ['version' => implode(', ', $validversions)]),
                $at1->get_errors());
        }
    }

    /**
     * @covers \qtype_stack\stack_cas_castext2_parsons
     */
    public function test_parsons_validate_params() {
        $invalidparameters = ['bad_param', 'HEIGHT', 'Height', 'override-css'];
        $validparameters = [
            'width', 'height', 'aspect-ratio', 'version', 'overridecss',
            'overridejs', 'input', 'clone', 'columns', 'rows', 'transpose', 'item-height', 'item-width', 'log',
        ];

        foreach ($invalidparameters as $param) {
            $raw = '[[parsons ' . $param . '="500"]]{' .
                '"1":"Assume that \\(n\\) is odd.",' .
                '"2":"Then there exists an \\(m\\in\\mathbb{Z}\\) such that \\(n=2m+1\\).", ' .
                '"3":"\\[ n^2 = (2m+1)^2 = 2(2m^2+2m)+1.\\]", ' .
                '"4":"Define \\(M=2m^2+2m\\in\\mathbb{Z}\\) then \\(n^2=2M+1\\).", ' .
                '} [[/parsons]]';

            $err = "Unknown parameter '$param' for Parson's block.";
            $at1 = castext2_evaluatable::make_from_source($raw, 'test-case');
            $session = new stack_cas_session2([$at1]);
            $this->assertFalse($at1->get_valid());
            $this->assertEquals(
                $err . ', ' . stack_string('stackBlock_parsons_param', ['param' => implode(', ', $validparameters)]),
                $at1->get_errors());
        }
    }
}

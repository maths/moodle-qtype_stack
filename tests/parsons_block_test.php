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
require_once(__DIR__ . '/../stack/cas/castext2/utils.php');
require_once(__DIR__ . '/../stack/cas/keyval.class.php');
require_once(__DIR__ . '/../stack/cas/cassession2.class.php');
require_once(__DIR__ . '/../stack/cas/secure_loader.class.php');
require_once(__DIR__ . '/../lang/multilang.php');


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
        $raw = '[[parsons]]{' .
            '"1":"Assume that \\(n\\) is odd.",' .
            '"2":"Then there exists an \\(m\\in\\mathbb{Z}\\) such that \\(n=2m+1\\).", ' .
            '"3":"\\[ n^2 = (2m+1)^2 = 2(2m^2+2m)+1.\\]", ' .
            '"4":"Define \\(M=2m^2+2m\\in\\mathbb{Z}\\) then \\(n^2=2M+1\\).", ' .
            '} [[/parsons]]';
        $expected = '<div style="width:100%;height:480px;" id="stack-iframe-holder-1"></div>';

        $at1 = castext2_evaluatable::make_from_source($raw, 'test-case');
        $session = new stack_cas_session2([$at1]);
        $session->instantiate();
        $this->assertEquals($expected, $at1->get_rendered());
    }

    /**
     * @covers \qtype_stack\stack_cas_castext2_parsons
     */
    public function test_pasrons_validate_length() {
        $raw = '[[parsons length="a"]]{' .
            '"1":"Assume that \\(n\\) is odd.",' .
            '"2":"Then there exists an \\(m\\in\\mathbb{Z}\\) such that \\(n=2m+1\\).", ' .
            '"3":"\\[ n^2 = (2m+1)^2 = 2(2m^2+2m)+1.\\]", ' .
            '"4":"Define \\(M=2m^2+2m\\in\\mathbb{Z}\\) then \\(n^2=2M+1\\).", ' .
            '} [[/parsons]]';
        $expected = '<div style="width:100%;height:480px;" id="stack-iframe-holder-1"></div>';

        $at1 = castext2_evaluatable::make_from_source($raw, 'test-case');
        $session = new stack_cas_session2([$at1]);
        $this->assertFalse($at1->get_valid());
        $this->assertEquals(stack_string('stackBlock_parsons_length_num'), $at1->get_errors());
    }

}

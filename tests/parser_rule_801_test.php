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

namespace qtype_stack;

use maxima_parser_utils;
use qtype_stack_testcase;
use stack_ast_filter_801_singleton_numeric;
use stack_cas_security;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../locallib.php');
require_once(__DIR__ . '/fixtures/test_base.php');
require_once(__DIR__ . '/../stack/cas/parsingrules/801_singleton_numeric.filter.php');

/**
 * Unit tests for {@link stack_ast_filter_801_singleton_numeric}.
 * @group qtype_stack
 * @group qtype_stack_ast_filters
 * @covers \ast_filter_801_singleton_numeric_auto_generated_test
 */
class parser_rule_801_test extends qtype_stack_testcase {

    public function filter(string $input, bool $floats = true, bool $ints = true, bool $power = true,
            string $convert = 'none'): array {
        $ast = maxima_parser_utils::parse($input);
        $filter = new stack_ast_filter_801_singleton_numeric();
        $filter->set_filter_parameters([
            'integer' => $ints,
            'float' => $floats,
            'power' => $power,
            'convert' => $convert
        ]);
        $errs = [];
        $note = [];
        $security = new stack_cas_security();

        $ast = $filter->filter($ast, $errs, $note, $security);

        $hasinvalid = false;
        $findinvalid = function($node) use(&$hasinvalid) {
            if (isset($node->position['invalid']) && $node->position['invalid'] === true) {
                $hasinvalid = true;
                return false;
            }
            return true;
        };
        $ast->callbackRecurse($findinvalid, false);

        $r = [
            'output' => $ast->toString(['nosemicolon' => true]),
            'notes' => $note,
            'errors' => $errs,
            'valid' => !$hasinvalid
        ];
        return $r;
    }

    public function test_normal_no_convert() {
        $test = '1+2';
        $result = $this->filter($test);
        $this->assertFalse($result['valid']);

        $test = '"1+2"';
        $result = $this->filter($test);
        $this->assertFalse($result['valid']);

        $test = '(1+2)';
        $result = $this->filter($test);
        $this->assertFalse($result['valid']);

        $test = '1/2';
        $result = $this->filter($test);
        $this->assertFalse($result['valid']);

        $test = '1*4^23';
        $result = $this->filter($test);
        $this->assertFalse($result['valid']);

        $test = '1*10^23.3';
        $result = $this->filter($test);
        $this->assertFalse($result['valid']);

        $test = '1.23e3*10^3';
        $result = $this->filter($test);
        $this->assertFalse($result['valid']);

        $test = '2.3';
        $result = $this->filter($test);
        $this->assertTrue($result['valid']);

        $test = '-2.3';
        $result = $this->filter($test);
        $this->assertTrue($result['valid']);

        $test = '23';
        $result = $this->filter($test);
        $this->assertTrue($result['valid']);

        $test = '+23';
        $result = $this->filter($test);
        $this->assertTrue($result['valid']);

        $test = '-23';
        $result = $this->filter($test);
        $this->assertTrue($result['valid']);

        $test = '2.3e3';
        $result = $this->filter($test);
        $this->assertTrue($result['valid']);

        $test = '-2.3E-3';
        $result = $this->filter($test);
        $this->assertTrue($result['valid']);

        $test = '2.3*10^3';
        $result = $this->filter($test);
        $this->assertTrue($result['valid']);

        $test = '-23*10^-4';
        $result = $this->filter($test);
        $this->assertTrue($result['valid']);

        $test = '2.*10^-4';
        $result = $this->filter($test);
        $this->assertTrue($result['valid']);
    }

    public function test_no_floats() {
        $test = '1+2';
        $result = $this->filter($test, false);
        $this->assertFalse($result['valid']);

        $test = '"1+2"';
        $result = $this->filter($test, false);
        $this->assertFalse($result['valid']);

        $test = '(1+2)';
        $result = $this->filter($test, false);
        $this->assertFalse($result['valid']);

        $test = '1/2';
        $result = $this->filter($test, false);
        $this->assertFalse($result['valid']);

        $test = '1*4^23';
        $result = $this->filter($test, false);
        $this->assertFalse($result['valid']);

        $test = '1*10^23.3';
        $result = $this->filter($test, false);
        $this->assertFalse($result['valid']);

        $test = '1.23e3*10^3';
        $result = $this->filter($test, false);
        $this->assertFalse($result['valid']);

        $test = '2.3';
        $result = $this->filter($test, false);
        $this->assertFalse($result['valid']);

        $test = '-2.3';
        $result = $this->filter($test, false);
        $this->assertFalse($result['valid']);

        $test = '23';
        $result = $this->filter($test, false);
        $this->assertTrue($result['valid']);

        $test = '+23';
        $result = $this->filter($test, false);
        $this->assertTrue($result['valid']);

        $test = '-23';
        $result = $this->filter($test, false);
        $this->assertTrue($result['valid']);

        $test = '2.3e3';
        $result = $this->filter($test, false);
        $this->assertFalse($result['valid']);

        $test = '-2.3E-3';
        $result = $this->filter($test, false);
        $this->assertFalse($result['valid']);

        $test = '2.3*10^3';
        $result = $this->filter($test, false);
        $this->assertFalse($result['valid']);

        $test = '-23*10^-4';
        $result = $this->filter($test, false);
        $this->assertTrue($result['valid']);
    }

    public function test_no_integers() {
        $test = '1+2';
        $result = $this->filter($test, true, false);
        $this->assertFalse($result['valid']);

        $test = '"1+2"';
        $result = $this->filter($test, true, false);
        $this->assertFalse($result['valid']);

        $test = '(1+2)';
        $result = $this->filter($test, true, false);
        $this->assertFalse($result['valid']);

        $test = '1/2';
        $result = $this->filter($test, true, false);
        $this->assertFalse($result['valid']);

        $test = '1*4^23';
        $result = $this->filter($test, true, false);
        $this->assertFalse($result['valid']);

        $test = '1*10^23.3';
        $result = $this->filter($test, true, false);
        $this->assertFalse($result['valid']);

        $test = '1.23e3*10^3';
        $result = $this->filter($test, true, false);
        $this->assertFalse($result['valid']);

        $test = '2.3';
        $result = $this->filter($test, true, false);
        $this->assertTrue($result['valid']);

        $test = '-2.3';
        $result = $this->filter($test, true, false);
        $this->assertTrue($result['valid']);

        $test = '23';
        $result = $this->filter($test, true, false);
        $this->assertFalse($result['valid']);

        $test = '+23';
        $result = $this->filter($test, true, false);
        $this->assertFalse($result['valid']);

        $test = '-23';
        $result = $this->filter($test, true, false);
        $this->assertFalse($result['valid']);

        $test = '2.3e3';
        $result = $this->filter($test, true, false);
        $this->assertTrue($result['valid']);

        $test = '-2.3E-3';
        $result = $this->filter($test, true, false);
        $this->assertTrue($result['valid']);

        $test = '2.3*10^3';
        $result = $this->filter($test, true, false);
        $this->assertTrue($result['valid']);

        $test = '-23*10^-4';
        $result = $this->filter($test, true, false);
        $this->assertFalse($result['valid']);
    }

    public function test_no_powers() {
        $test = '1+2';
        $result = $this->filter($test, true, true, false);
        $this->assertFalse($result['valid']);

        $test = '"1+2"';
        $result = $this->filter($test, true, true, false);
        $this->assertFalse($result['valid']);

        $test = '(1+2)';
        $result = $this->filter($test, true, true, false);
        $this->assertFalse($result['valid']);

        $test = '1/2';
        $result = $this->filter($test, true, true, false);
        $this->assertFalse($result['valid']);

        $test = '1*4^23';
        $result = $this->filter($test, true, true, false);
        $this->assertFalse($result['valid']);

        $test = '1*10^23.3';
        $result = $this->filter($test, true, true, false);
        $this->assertFalse($result['valid']);

        $test = '1.23e3*10^3';
        $result = $this->filter($test, true, true, false);
        $this->assertFalse($result['valid']);

        $test = '2.3';
        $result = $this->filter($test, true, true, false);
        $this->assertTrue($result['valid']);

        $test = '-2.3';
        $result = $this->filter($test, true, true, false);
        $this->assertTrue($result['valid']);

        $test = '23';
        $result = $this->filter($test, true, true, false);
        $this->assertTrue($result['valid']);

        $test = '+23';
        $result = $this->filter($test, true, true, false);
        $this->assertTrue($result['valid']);

        $test = '-23';
        $result = $this->filter($test, true, true, false);
        $this->assertTrue($result['valid']);

        $test = '2.3e3';
        $result = $this->filter($test, true, true, false);
        $this->assertTrue($result['valid']);

        $test = '-2.3E-3';
        $result = $this->filter($test, true, true, false);
        $this->assertTrue($result['valid']);

        $test = '2.3*10^3';
        $result = $this->filter($test, true, true, false);
        $this->assertFalse($result['valid']);

        $test = '-23*10^-4';
        $result = $this->filter($test, true, true, false);
        $this->assertFalse($result['valid']);
    }

    public function test_no_convert() {
        $test = ['123', '123'];
        $result = $this->filter($test[0], true, true, true, 'none');
        $this->assertEquals($test[1], $result['output']);

        $test = ['123.45', '123.45'];
        $result = $this->filter($test[0], true, true, true, 'none');
        $this->assertEquals($test[1], $result['output']);

        $test = ['123.45e6', '123.45E6'];
        $result = $this->filter($test[0], true, true, true, 'none');
        $this->assertEquals($test[1], $result['output']);

        $test = ['123.45*10^6', '123.45*10^6'];
        $result = $this->filter($test[0], true, true, true, 'none');
        $this->assertEquals($test[1], $result['output']);

        $test = ['-123', '-123'];
        $result = $this->filter($test[0], true, true, true, 'none');
        $this->assertEquals($test[1], $result['output']);

        $test = ['-123.45', '-123.45'];
        $result = $this->filter($test[0], true, true, true, 'none');
        $this->assertEquals($test[1], $result['output']);

        $test = ['-123.45*10^6', '-123.45*10^6'];
        $result = $this->filter($test[0], true, true, true, 'none');
        $this->assertEquals($test[1], $result['output']);

        $test = ['-123.45*10^-6', '-123.45*10^-6'];
        $result = $this->filter($test[0], true, true, true, 'none');
        $this->assertEquals($test[1], $result['output']);

        // With sufficient exponents will alway use the exact form.
        $test = ['-123.45*10^-333', '-123.45*10^-333'];
        $result = $this->filter($test[0], true, true, true, 'none');
        $this->assertEquals($test[1], $result['output']);

        $test = ['-123.45e-333', '-12345*10^-335'];
        $result = $this->filter($test[0], true, true, true, 'none');
        $this->assertEquals($test[1], $result['output']);
    }

    public function test_convert_to_float() {
        $test = ['123', '123'];
        $result = $this->filter($test[0], true, true, true, 'to float');
        $this->assert_equals_ignore_spaces_and_e($test[1], $result['output']);

        $test = ['123.45', '123.45'];
        $result = $this->filter($test[0], true, true, true, 'to float');
        $this->assertEquals($test[1], $result['output']);

        $test = ['123.45e6', '123.45E6'];
        $result = $this->filter($test[0], true, true, true, 'to float');
        $this->assertEquals($test[1], $result['output']);

        $test = ['123.45*10^6', '123.45e6'];
        $result = $this->filter($test[0], true, true, true, 'to float');
        // Ignore e/E differences.
        $this->assertEquals($test[1], strtolower($result['output']));

        $test = ['-123', '-123'];
        $result = $this->filter($test[0], true, true, true, 'to float');
        $this->assertEquals($test[1], $result['output']);

        $test = ['-123.45', '-123.45'];
        $result = $this->filter($test[0], true, true, true, 'to float');
        $this->assertEquals($test[1], $result['output']);

        $test = ['-123.45*10^6', '-123.45e6'];
        $result = $this->filter($test[0], true, true, true, 'to float');
        $this->assertEquals($test[1], strtolower($result['output']));

        $test = ['-123.45*10^-6', '-123.45e-6'];
        $result = $this->filter($test[0], true, true, true, 'to float');
        $this->assertEquals($test[1], strtolower($result['output']));

        // With sufficient exponents will alway use the exact form.
        $test = ['-123.45*10^-333', '-123.45*10^-333'];
        $result = $this->filter($test[0], true, true, true, 'to float');
        $this->assertEquals($test[1], $result['output']);

        $test = ['-123.45e-333', '-12345*10^-335'];
        $result = $this->filter($test[0], true, true, true, 'to float');
        $this->assertEquals($test[1], $result['output']);
    }

    public function test_convert_to_power() {
        $test = ['123', '123'];
        $result = $this->filter($test[0], true, true, true, 'to power');
        $this->assertEquals($test[1], $result['output']);

        $test = ['123.45', '12345*10^-2'];
        $result = $this->filter($test[0], true, true, true, 'to power');
        $this->assertEquals($test[1], $result['output']);

        $test = ['123.45e6', '12345*10^4'];
        $result = $this->filter($test[0], true, true, true, 'to power');
        $this->assertEquals($test[1], $result['output']);

        $test = ['123.45*10^6', '123.45*10^6'];
        $result = $this->filter($test[0], true, true, true, 'to power');
        $this->assertEquals($test[1], $result['output']);

        $test = ['-123', '-123'];
        $result = $this->filter($test[0], true, true, true, 'to power');
        $this->assertEquals($test[1], $result['output']);

        $test = ['-123.45', '-12345*10^-2'];
        $result = $this->filter($test[0], true, true, true, 'to power');
        $this->assertEquals($test[1], $result['output']);

        $test = ['-123.45*10^6', '-123.45*10^6'];
        $result = $this->filter($test[0], true, true, true, 'to power');
        $this->assertEquals($test[1], $result['output']);

        $test = ['-123.45*10^-6', '-123.45*10^-6'];
        $result = $this->filter($test[0], true, true, true, 'to power');
        $this->assertEquals($test[1], $result['output']);
    }
}

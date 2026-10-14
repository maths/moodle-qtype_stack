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
 * Unit tests for the Maxima function stackjson_parse() in stack/maxima/stackstrings.mac.
 * @package    qtype_stack
 * @copyright  2026 Martin Kraska, Technische Hochschule Brandenburg.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

namespace qtype_stack;

use qtype_stack_testcase;
use stack_ast_container;
use stack_cas_security;
use stack_cas_session2;
use stack_options;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../locallib.php');
require_once(__DIR__ . '/fixtures/test_base.php');
require_once(__DIR__ . '/../stack/cas/cassession2.class.php');
require_once(__DIR__ . '/../stack/cas/ast.container.class.php');

/**
 * Unit tests for stackjson_parse(), see issue #1898.
 * @group qtype_stack
 * @covers \stack_cas_session2
 */
final class stackjson_test extends qtype_stack_testcase {

    /**
     * Evaluate Maxima statements in one CAS session and return their values.
     * @param string[] $statements Maxima code, one statement each.
     * @param bool $simp the simplify option of the session.
     * @return string[] the values as returned by get_value().
     */
    private function evaluate(array $statements, bool $simp = true): array {
        $cs = [];
        foreach ($statements as $s) {
            $cs[] = stack_ast_container::make_from_teacher_source($s, '', new stack_cas_security(), []);
        }
        $options = new stack_options();
        $options->set_option('simplify', $simp);
        $session = new stack_cas_session2($cs, $options, 0);
        $this->assertTrue($session->get_valid());
        $session->instantiate();
        $values = [];
        foreach ($cs as $c) {
            $this->assertEquals('', $c->get_errors());
            $values[] = $c->get_value();
        }
        return $values;
    }

    public function test_literals_and_empty(): void {
        $values = $this->evaluate([
            'stackjson_parse("true")',
            'stackjson_parse("false")',
            'stackjson_parse("null")',
            'stackjson_parse("")',
            'stackjson_parse("   ")',
            'stackjson_parse("[]")',
            'stackjson_parse("{}")',
            'stackjson_parse(" [ 1 , 2 ] ")',
            'stackjson_parse("\"x\"")',
        ]);
        $this->assertEquals(['true', 'false', 'und', 'und', 'und', '[]', '["stack_map"]', '[1,2]', '"x"'], $values);
    }

    public function test_numbers(): void {
        $values = $this->evaluate([
            // As before, stack_string_parse_number() gives an integer if the value is one (e.g. -2.5e2).
            'is(stackjson_parse("[1, -2.5e2, 3.25E-1, 0, -7]") = [1, -250, 0.325, 0, -7])',
            // A number at the top level (failed before #1898).
            'stackjson_parse("42")',
            'stackjson_parse("-3")',
        ]);
        $this->assertEquals(['true', '42', '-3'], $values);
    }

    public function test_nested_lists_and_maps(): void {
        $values = $this->evaluate([
            'is(stackjson_parse("[true, false, null, [], [[3]], 7]") = [true, false, und, [], [[3]], 7])',
            'is(stackjson_parse("{\"a\": 1, \"b\": [1, {\"c\": null}], \"d\": {}}") = ' .
                '["stack_map", ["a", 1], ["b", [1, ["stack_map", ["c", und]]]], ["d", ["stack_map"]]])',
            'is(stackjson_parse("[{\"x\": 1.5, \"y\": -2}, {\"x\": 0, \"y\": 3}]") = ' .
                '[["stack_map", ["x", 1.5], ["y", -2]], ["stack_map", ["x", 0], ["y", 3]]])',
            'stackmap_get(stackjson_parse("{\"k\": \"v\", \"n\": 2}"), "n")',
        ]);
        $this->assertEquals(['true', 'true', 'true', '2'], $values);
    }

    public function test_string_escapes(): void {
        $values = $this->evaluate([
            'p:stackjson_parse("[\"a\\\\\"b\\\\\\\\c\\\\nd\\\\te\\\\/f\", \"\\\\u00e4x\", \"\"]")',
            'is(p[1] = sconcat("a", "\"", "b", "\\\\", "c", ascii(10), "d", ascii(9), "e/f"))',
            'is(p[2] = sconcat(unicode(228), "x"))',
            'p[3]',
        ]);
        $this->assertEquals(['true', 'true', '""'], array_slice($values, 1));
    }

    public function test_malformed(): void {
        $values = $this->evaluate([
            'stackjson_parse("[1")',
            'stackjson_parse("[1,2]]")',
            'stackjson_parse("[1}")',
            'stackjson_parse("{\"a\": 1]")',
            'stackjson_parse("\"abc")',
            'stackjson_parse("[tru]")',
            'stackjson_parse(42)',
        ]);
        $this->assertEquals(array_fill(0, 7, 'und'), $values);
    }

    public function test_simp_false(): void {
        // The result does not depend on the simp setting of the caller (failed before #1898).
        $values = $this->evaluate([
            'stackjson_parse("[-4, 2, {\"a\": -1}]")',
            'stackjson_parse("-7")',
        ], false);
        $this->assertEquals(['[-4,2,["stack_map",["a",-1]]]', '-7'], $values);
    }

    public function test_long_input(): void {
        // The state of a JSXGraph sketch with 200 objects, about 9 kB of JSON. The parser is linear in
        // the length of the input; the version before #1898 needed more than two minutes for this.
        $values = $this->evaluate([
            'o:"[\"force\",\"N_A\",[3.25,-1.2],[3,0],10,\"active\"]"',
            's:sconcat("[", simplode(makelist(o, i, 1, 200), ","), "]")',
            'p:stackjson_parse(s)',
            'length(p)',
            'is(p[200] = ["force", "N_A", [3.25, -1.2], [3, 0], 10, "active"])',
        ]);
        $this->assertEquals(['200', 'true'], array_slice($values, 3));
    }
}

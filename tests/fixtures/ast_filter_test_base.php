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

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../../locallib.php');
require_once(__DIR__ . '/../../stack/maximaparser/corrective_parser.php');
require_once(__DIR__ . '/../../stack/cas/parsingrules/parsingrule.factory.php');
require_once(__DIR__ . '/../../stack/cas/cassecurity.class.php');


/**
 * This is a base-class for automatically generated AST-filter tests
 *
 * There is a CLI-script to generate and update those test. You must
 * not modify those tests by hand and they exist just so that you can
 * see what happened when they were created and that things still work
 * the same.
 *
 * @copyright  2019 Aalto University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class qtype_stack_ast_testcase extends basic_testcase {

    /**
     * @var stack_cas_astfilter The filter being tested.
     */
    public $filter = null;

    /**
     * @var stack_cas_security The security being used.
     */
    public $security = null;

    /**
     * The generic declaration of what is expected.
     *
     * @param string $input
     * @param string $result
     * @param array $notes
     * @param bool $valid
     * @param bool $errors
     */
    public function expect(string $input, string $result, $notes=array(),
                           $valid=true, $errors=false) {
        // We currently ignore these but lets collect them.
        $parsererrors = array();
        $parsernotes = array();

        // Parse it, remember that these tests only act on parseable strings.
        $ast = maxima_corrective_parser::parse($input, $parsererrors, $parsernotes,
                                               array('startRule' => 'Root',
                                               'letToken' => stack_string('equiv_LET')));

        $filtererrors = array();
        $filternotes = array();

        $filtered = $this->filter->filter($ast, $filtererrors,
                                          $filternotes, $this->security);

        // What notes we expect there to be.
        $this->assertArraySubset($notes, $filternotes);

        // If it is supposed to become invalid.
        if ($valid === true) {
            $this->assert_not_marked_invalid($filtered);
        } else {
            $this->assert_marked_invalid($filtered);
        }

        // If we expect errors to be generated.
        if ($errors === true) {
            $this->assertNotEmpty($filtererrors);
        } else {
            $this->assertEquals([], $filtererrors);
        }

        // Finally, check that the result string is equivalent.
        $this->assertEquals($result, $filtered->toString(array('nosemicolon' => true)));
    }

    /**
     * @param MP_Node $ast
     */
    public function assert_marked_invalid($ast) {
        $hasinvalid = false;
        $findinvalid = function($node) use(&$hasinvalid) {
            if (isset($node->position['invalid']) && $node->position['invalid'] === true) {
                $hasinvalid = true;
                return false;
            }
            return true;
        };
        $ast->callbackRecurse($findinvalid);

        $this->assertTrue($hasinvalid);
    }

    /**
     * @param MP_Node $ast
     */
    public function assert_not_marked_invalid($ast) {
        $hasinvalid = false;
        $findinvalid = function($node) use(&$hasinvalid) {
            if (isset($node->position['invalid']) && $node->position['invalid'] === true) {
                $hasinvalid = true;
                return false;
            }
            return true;
        };
        $ast->callbackRecurse($findinvalid);

        $this->assertFalse($hasinvalid);
    }
}

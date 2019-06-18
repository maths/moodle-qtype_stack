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
     * The filter being tested (stack_cas_astfilter).
     */
    public $filter = null;

    /**
     * The security being used (stack_cas_security).
     */
    public $security = null;

    /**
     * The generic declaration of what is expected.
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

        // If we expect errors to be generated.
        if ($errors === true) {
            $this->assertFalse(empty($filtererrors));
        } else {
            $this->assertTrue(empty($filtererrors));
        }

        // What notes we expect there to be.
        $this->assertArraySubset($notes, $filternotes);

        // If it is supposed to become invalid.
        if ($valid === true) {
            $this->assertNotMarkkedInvalid($filtered);
        } else {
            $this->assertMarkkedInvalid($filtered);
        }

        // Finally, check that the result string is equivalent.
        $this->assertEquals($result, $filtered->toString(array('nosemicolon' => true)));
    }

    public function assertMarkkedInvalid($ast) {
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

    public function assertNotMarkkedInvalid($ast) {
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

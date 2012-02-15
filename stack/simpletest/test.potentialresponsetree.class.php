<?php
// This file is part of Stack - http://stack.bham.ac.uk/
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
 * Unit tests for stack_potentialresponse_tree.
 *
 * @copyright  2012 The University of Birmingham
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once(dirname(__FILE__) . '/../potentialresponsetree.class.php');
require_once(dirname(__FILE__) . '/../cas/castext.class.php');
require_once(dirname(__FILE__) . '/../../locallib.php');


/**
 * Unit tests for stack_potentialresponse_tree.
 *
 * @copyright  2012 The University of Birmingham
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class stack_potentialresponsetree_test extends UnitTestCase {

    public function test_do_test_pass() {

        $sans = new stack_cas_casstring('sans', 's');
        $tans = new stack_cas_casstring('(x+1)^2/3+c', 't');
        $pr = new stack_potentialresponse($sans, $tans, 'Int', 'x', false);
        $pr->add_branch(0, '=', 0, '', -1, 'Boo!', '1-0-0');
        $pr->add_branch(1, '=', 2, '', -1, 'Yeah!', '1-0-1');
    
        $potentialresponses[] = $pr;

        $tree = new stack_potentialresponse_tree('', '', true, 5, null, $potentialresponses);

        $questionvars = null;
        $options = new stack_options();
        $anwers[] = array('sans'=>'(x+1)^2/3+c');
        $seed = 12345;
        $result = $tree->traverse_tree($questionvars, $options, $answers, $seed);
        
        echo "<pre>";
        print_r($result);
        echo "</pre>";
    }

}
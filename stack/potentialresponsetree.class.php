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
 * Deals with whole potential response trees.
 *
 * @copyright  2012 University of Birmingham
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . 'potentialresponse.class.php');

/**
 * Deals with whole potential response trees.
 *
 * @copyright  2012 University of Birmingham
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class stack_potentialresponse_tree {

    /*
    * @var string Name of the PRT.
    */
    private $name;

    /*
    * @var string Description of the PRT.
    */
    private $description;

    /*
    * @var boolean Should this PRT simplify when its arguments are evaluated?
    */
    private $simp;

    /*
    * @var float Number of marks available from this PRT.
    */
    private $value;

    /*
    * @var stack_cas_cassession Feeback variables. 
    */
    private $feedbackvars;
    
    /*
    * @var array of stack_potentialresponse.  
    */
    private $potentialresponses;
    

    /*
     * This function actually traverses the tree and generates outcomes.
     */
    public function traverse_tree($questionvars, $options, $answers) {

        $options->set_option('simplify', $this->simp);

        // (1) Concatinate the question_variables and $feedbackvars
        
        // (2) Traverse the $potentialresponses and pull out all sans, tans and options.
        
        // (3) Instantiate these background variables
        
        // (4) Traverse the tree.

        // (5) Instantiate the feedback castext.

    }
}
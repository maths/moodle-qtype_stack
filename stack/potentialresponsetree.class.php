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

    function __construct() {
    }
    /*
     * This function actually traverses the tree and generates outcomes.
     */
    public function traverse_tree($questionvars, $options, $answers, $seed) {

        if(empty($this->potentialresponses)) {
            throw new Exception ('stack_potentialresponse_tree: traverse_tree attempting to traverse an empty tree.  Something is wrong here.');
        }

        $options->set_option('simplify', $this->simp);

        // Set up the outcomes for this travserse of the tree
        $feedback    = '';
        $answernote  = '';
        $errors      = '';
        $valid       = true;
        $mark        = 0;
        $penalty     = 0;

        // (1) Concatinate the question_variables and $feedbackvars
        $cascontext = clone $questionvars;
        // Add in students' answers....
        $cascontext->merge_session($this->feedbackvars);
        
        // (2) Traverse the $potentialresponses and pull out all sans, tans and options.
        $answervars = array();
        foreach ($this->potentialresponses as $key => $pr) {
            $sans = $pr->get_sans();
            $sans->set_key('PRSANS'.$key);
            $answervars[]=$sans;

            $tans = $pr->get_tans();
            $tans->set_key('PRTANS'.$key);
            $answervars[]=$tans;

            if ($pr->process_atoptions()) {
                $atopts = new stack_cas_casstring($pr->get_atoptions(),'t',false,false);
                $atopts->set_key('PRATOPT'.$key);
                $answervars[]=$atopts;
            }
        }
        $cascontext->add_vars($answervars);
        
        // (3) Instantiate these background variables
        $cascontext->instantiate();
        //TODO error trapping at this stage....?
        // (4) Traverse the tree.
        $nextpr = 0;
        while($nextpr != -1) {

            $pr = $this->potentialresponses[$nextpr];

            if ($pr->visited_before()) {
                $nextpr = -1;
            } else {
                //TODO check for errors here
                $sans = $cascontext->get_value_key('PRSANS'.$nextpr);
                $tans = $cascontext->get_value_key('PRTANS'.$nextpr);
                $atopts =  $cascontext->get_value_key('PRATOPT'.$nextpr);
                // If we can't find atopts then they were not processed by the CAS.  They might still be some in the potential response which do not need to be processed.
                if (false === $atopts) {
                    $atopts = null;
                }
                $results = $pr->do_test($sans, $tans, $atopts, $options);

                $valid = $valid && $result['valid'];
                $mark  = $pr->update_mark($mark);
                $feedback .= $result['feedback'];
                $answernote .= $result['answernote'];

                if (''!=$result['penalty']) {
                    $penalty = $result['penalty'];
                }

                if ('' != $result['errors']) {
                    $errors .= $result['errors'];
                    $nextpr = -1;
                } else {
                    $nextpr = $result['nextpr'];
                }
            }
        }
        // (5) Instantiate the feedback castext.

        $feedbackct = new stack_cas_text($feedback,$cascontext,$seed,'t',false,false);
        $feedback = $feedbackct->get_display_castext();
        $errors .= $feedbackct->get_errors();
        // (6) Return the results and clean up.

        $result['valid']      = $valid;
        $result['errors']     = $errors;
        $result['mark']       = $mark;
        $result['penalty']    = $penalty;
        $result['answernote'] = $answernote;
        $result['feedback']   = $feedback;

        return $result;
    }

}
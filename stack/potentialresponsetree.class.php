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

require_once(dirname(__FILE__) . '/potentialresponse.class.php');

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

    public function __construct($name, $description, $simp, $value, $feedbackvars, $potentialresponses) {

        $this->name               = $name;
        $this->description        = $description;

        if (!is_bool($simp)) {
            throw new Exception('stack_potentialresponse_tree: __construct: simp must be a boolean.');
        } else {
            $this->simp               = $simp;
        }

        $this->value              = $value;

        if (is_a($feedbackvars, 'stack_cas_session') || null===$feedbackvars) {
            $this->feedbackvars = $feedbackvars;
        } else {
            throw new Exception('stack_potentialresponse_tree: __construct: expects $feedbackvars to be null or a stack_cas_session.');
        }

        if (is_array($potentialresponses)) {
            foreach ($potentialresponses as $pr) {
                if (!is_a($pr, 'stack_potentialresponse')) {
                    throw new Exception ('stack_potentialresponse_tree: __construct: attempting to construct a potential response tree with potential responses which are not stack_potentialresponse');
                }
            }
        } else if (!(null===$potentialresponses)) {
            throw new Exception ('stack_potentialresponse_tree: __construct: attempting to construct a potential response tree with potential responses which are not an array of stack_potentialresponse');
        }
        $this->potentialresponses = $potentialresponses;

    }
    /*
     * This function actually traverses the tree and generates outcomes.
     */
    public function traverse_tree($questionvars, $options, $answers, $seed) {

        if (empty($this->potentialresponses)) {
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
        $cascontext = new stack_cas_session(null, $options, $seed, 't', true, false);
        // (1.1) Start with the question variables.
        $cascontext->merge_session($questionvars);
        // (1.2) Add in student's answers.
        $answervars = array();
        foreach ($answers as $key=>$val) {
            $cs = new stack_cas_casstring($val);
            $cs->set_key($key);
            $answervars[]=$cs;
        }
        $cascontext->add_vars($answervars);
        // (1.2) Add in feedback variables.
        $cascontext->merge_session($this->feedbackvars);

        // (1.3) Traverse the $potentialresponses and pull out all sans, tans and options.
        $answervars = array();
        foreach ($this->potentialresponses as $key => $pr) {
            $sans = $pr->sans;
            $sans->set_key('PRSANS'.$key);
            $answervars[]=$sans;

            $tans = $pr->tans;
            $tans->set_key('PRTANS'.$key);
            $answervars[]=$tans;

            if ($pr->process_atoptions()) {
                $atopts = new stack_cas_casstring($pr->atoptions, 't', false, false);
                $atopts->set_key('PRATOPT'.$key);
                $answervars[]=$atopts;
            }
        }
        $cascontext->add_vars($answervars);

        // (2) Instantiate these background variables
        $cascontext->instantiate();

        //echo "<pre>";
        //print_r($cascontext->get_session());
        //echo "</pre>";        

        //TODO error trapping at this stage....?
        // (3) Traverse the tree.
        $nextpr = 0;
        while ($nextpr != -1) {

            if (!array_key_exists($nextpr, $this->potentialresponses)) {
                throw new Exception('stack_potentialresponse_tree: traverse_tree: attempted to jump to a potential response which does not exist in this question.  This is a question authoring/validation problem.');
            }
            $pr = $this->potentialresponses[$nextpr];

            if ($pr->visited_before()) {
                $answernote .= ' | [PRT-CIRCULARITY]='.$nextpr;
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
                $result = $pr->do_test($sans, $tans, $atopts, $options);

                $valid = $valid && $result['valid'];
                $mark  = $pr->update_mark($mark);
                $feedback = trim($feedback).' '.trim($result['feedback']);
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

        // (4) Sort out feedback and answernotes
        // (4.1) Instantiate the feedback castext.
        $feedbackct = new stack_cas_text($feedback, $cascontext, $seed, 't', false, false);
        $feedback = trim($feedbackct->get_display_castext());
        $errors .= $feedbackct->get_errors();
        // (4.2) Tidy up the answernote
        if ('|'==substr(trim($answernote), 0, 1)) {
            $answernote = substr(trim($answernote), 1);
        }
        $answernote = trim($answernote);
        // (4.3) Reset all the potential responses for the next attempt
        foreach ($this->potentialresponses as $pr) {
            $pr->reset();
        }
        
        // (5) Return the results and clean up.
        $result = array();
        $result['valid']      = $valid;
        $result['errors']     = $errors;
        $result['mark']       = $mark;
        $result['penalty']    = $penalty;
        $result['answernote'] = $answernote;
        $result['feedback']   = $feedback;

        return $result;
    }

    /*
     * Takes an array of interaction element keys, e.g. sans1, a, and returns those needed for the
     * potential response tree to be executed.
     * TODO: Since this is a time consuming operation, it needs to be done once and cached within the object.
     *
	 * @ var array of interaction element names.
     */
    public function get_ie_requirements($ies) {
        $rawcasstrings = array();
        if (null !== $this->feedbackvars) {
            $rawcasstrings = $this->feedbackvars->get_all_raw_casstrings();
        }
        foreach ($this->potentialresponses as $pr) {
            $rawcasstrings = array_merge($rawcasstrings, $pr->get_ie_requirements_data());
        }

        $neededies = array();
        foreach ($ies as $ie) {
            // Don't just "foreach ($rawcastrings as..." here.  Very wasteful.
            $i=0;
            $found = false;
            while ($i<count($rawcasstrings) && !$found) {
                $found = $this->find_string_whole($ie, $rawcasstrings[$i]);
                $i++;
            }
            if ($found) {
                $neededies[]=$ie;
            }
        }
        return $neededies;
    }

    /*
     * Looks for occurances of $needle in $haystack as whole words only.
     */
    private function find_string_whole($needle, $haystack) {
        $needle = strtolower($needle);
        $haystack = strtolower($haystack);
        $charArray = str_split($haystack);

        $characters = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
        //characters that are invalid, if the needle is surrounded by these then the find returns false

        $noToSearch = substr_count($haystack, $needle);
        $pos = 0;//position (index) in the string;
        $i = 0;

        $toReturn = false;
        while ($i < $noToSearch) {
            $toReturn = true;

            if ($needle == '' || $haystack == '') {
                $toReturn = false;
            } else {
                $pos = stripos($haystack, $needle);

            if ($pos === false) {
                $toReturn = false;
            } else {
                if (($pos -1) >= 0) {
                    //check preceeding character
                    if (in_array($charArray[($pos -1)], $characters)) {
                        $toReturn = false;
                    }
                }
                $endChar = $pos + strlen($needle);

                if (($endChar +1) <= count($charArray)) {
                    //check end character + 1
                    if (in_array($charArray[($endChar)], $characters)) {
                        $toReturn = false;
                    }
                }
                if ($toReturn == true) {
                    return true;
                }
            }
        }
        $i++;
        $end = ($pos + strlen($needle) -1);
        $haystack = substr($haystack, $end); //stripos only matches first occurence. We
        $charArray = str_split($haystack);
        }
        return $toReturn;
    }

}
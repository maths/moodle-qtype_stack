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

require_once(dirname(__FILE__) . '/potentialresponsenode.class.php');

/**
 * Deals with whole potential response trees.
 *
 * @copyright  2012 University of Birmingham
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class stack_potentialresponse_tree {

    /** @var string Name of the PRT. */
    private $name;

    /** @var string Description of the PRT. */
    private $description;

    /** @var boolean Should this PRT simplify when its arguments are evaluated? */
    private $simplify;

    /** @var float Number of marks available from this PRT. */
    private $value;

    /** @var stack_cas_cassession Feeback variables. */
    private $feedbackvariables;

    /** @var array of stack_potentialresponse_node. */
    private $nodes;

    public function __construct($name, $description, $simplify, $value, $feedbackvariables, $nodes) {

        $this->name        = $name;
        $this->description = $description;

        if (!is_bool($simplify)) {
            throw new Exception('stack_potentialresponse_tree: __construct: simplify must be a boolean.');
        } else {
            $this->simplify = $simplify;
        }

        $this->value = $value;

        if (is_a($feedbackvariables, 'stack_cas_session') || null===$feedbackvariables) {
            $this->feedbackvariables = $feedbackvariables;
        } else {
            throw new Exception('stack_potentialresponse_tree: __construct: expects $feedbackvariables to be null or a stack_cas_session.');
        }

        if ($nodes === null) {
            $nodes = array();
        }
        if (!is_array($nodes)) {
            throw new Exception ('stack_potentialresponse_tree: __construct: attempting to construct a potential response tree with potential responses which are not an array of stack_potentialresponse');
        }
        foreach ($nodes as $pr) {
            if (!is_a($pr, 'stack_potentialresponse_node')) {
                throw new Exception ('stack_potentialresponse_tree: __construct: attempting to construct a potential response tree with potential responses which are not stack_potentialresponse');
            }
        }

        $this->nodes = $nodes;
    }

    /**
     * This function actually traverses the tree and generates outcomes.
     */
    public function evaluate_response($questionvars, $options, $answers, $seed) {

        if (empty($this->nodes)) {
            throw new Exception ('stack_potentialresponse_tree: evaluate_response attempting to traverse an empty tree.  Something is wrong here.');
        }

        $options->set_option('simplify', $this->simplify);

        // Set up the outcomes for this travserse of the tree
        $feedback    = '';
        $answernote  = '';
        $errors      = '';
        $valid       = true;
        $mark        = 0;
        $penalty     = 0;

        // (1) Concatinate the question_variables and $feedbackvariables
        $cascontext = new stack_cas_session(null, $options, $seed, 't', true, false);
        // (1.1) Start with the question variables.
        $cascontext->merge_session($questionvars);
        // (1.2) Add in student's answers.
        $answervars = array();
        foreach ($answers as $key => $val) {
            $cs = new stack_cas_casstring($val);
            $cs->set_key($key);
            $answervars[]=$cs;
        }
        $cascontext->add_vars($answervars);
        // (1.2) Add in feedback variables.
        $cascontext->merge_session($this->feedbackvariables);

        // (1.3) Traverse the $nodes and pull out all sans, tans and options.
        $answervars = array();
        foreach ($this->nodes as $key => $pr) {
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

        //TODO error trapping at this stage....?
        // (3) Traverse the tree.
        $nextpr = 0;
        while ($nextpr != -1) {

            if (!array_key_exists($nextpr, $this->nodes)) {
                throw new Exception('stack_potentialresponse_tree: evaluate_response: attempted to jump to a potential response which does not exist in this question.  This is a question authoring/validation problem.');
            }
            $pr = $this->nodes[$nextpr];

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
        foreach ($this->nodes as $pr) {
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

    /**
     * Take an array of interaction element names, or equivalently response
     * varaibles, (for example sans1, a) and return those that are used by this
     * potential response tree.
     *
     * TODO: Since this is a time consuming operation, it needs to be done once
     * and cached within the object.
     *
     * @param array of string variable names.
     * @return array filter list of variable names. Only those variable names
     * referred to be this PRT are returned.
     */
    public function get_required_variables($variablenames) {

        $rawcasstrings = array();
        if ($this->feedbackvariables !== null) {
            $rawcasstrings = $this->feedbackvariables->get_all_raw_casstrings();
        }
        foreach ($this->nodes as $node) {
            $rawcasstrings = array_merge($rawcasstrings, $node->get_required_cas_strings());
        }

        $requirednames = array();
        foreach ($variablenames as $name) {
            foreach ($rawcasstrings as $string) {
                if ($this->string_contains_variable($name, $string)) {
                    $requirednames[] = $name;
                    break;
                }
            }
        }
        return $requirednames;
    }

    /**
     * Looks for occurances of $variable in $string as whole words only.
     * @param string $variable a variable name.
     * @param string $string a cas string.
     * @return bool whether the string refers to the variable.
     */
    private function string_contains_variable($variable, $string) {
        $regex = '~\b' . preg_quote(strtolower($variable)) . '\b~';
        return preg_match($regex, strtolower($string));
    }
}

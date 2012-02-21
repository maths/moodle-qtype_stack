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
        foreach ($nodes as $node) {
            if (!is_a($node, 'stack_potentialresponse_node')) {
                throw new Exception ('stack_potentialresponse_tree: __construct: attempting to construct a potential response tree with potential responses which are not stack_potentialresponse');
            }
        }

        $this->nodes = $nodes;
    }

    /**
     * Create the CAS context in which we will evaluate this PRT. This contains
     * all the question variables, student responses, feedback variables, and all
     * the sans, tans and atoptions expressions from all the nodes.
     *
     * @param stack_cas_session $questionvars the question varaibles.
     * @param stack_options $options
     * @param array $answers name => value the student response.
     * @param int $seed the random number seed.
     * @return stack_cas_session initialised with all the expressions this PRT will need.
     */
    protected function create_cas_context_for_evaluation($questionvars, $options, $answers, $seed) {

        // Start with the quetsion variables (note that order matters here).
        $cascontext = new stack_cas_session(null, $options, $seed, 't', true, false);
        $cascontext->merge_session($questionvars);

        // Add the student's responses.
        $answervars = array();
        foreach ($answers as $key => $val) {
            $cs = new stack_cas_casstring($val);
            $cs->set_key($key);
            $answervars[] = $cs;
        }
        $cascontext->add_vars($answervars);

        // Add the feedback variables.
        $cascontext->merge_session($this->feedbackvariables);

        // Add all the expressions from all the nodes.
        foreach ($this->nodes as $key => $node) {
            $cascontext->add_vars($node->get_context_variables($key));
        }

        $cascontext->instantiate();
        //TODO error trapping at this stage....?

        return $cascontext;
    }

    /**
     * This function actually traverses the tree and generates outcomes.
     *
     * @param stack_cas_session $questionvars the question varaibles.
     * @param stack_options $options
     * @param array $answers name => value the student response.
     * @param int $seed the random number seed.
     */
    public function evaluate_response($questionvars, $options, $answers, $seed) {

        if (empty($this->nodes)) {
            throw new Exception ('stack_potentialresponse_tree: evaluate_response attempting to traverse an empty tree.  Something is wrong here.');
        }

        $options->set_option('simplify', $this->simplify);

        $cascontext = $this->create_cas_context_for_evaluation($questionvars, $options, $answers, $seed);

        // Set up the outcomes for this travsersal of the tree
        $results = array(
            'feedback'    => array(),
            'answernote'  => array(),
            'errors'      => '',
            'valid'       => true,
            'mark'        => 0,
            'penalty'     => 0,
        );

        // Traverse the tree.
        $nodekey = 0;
        $visitednodes = array();
        while ($nodekey != -1) {

            if (!array_key_exists($nodekey, $this->nodes)) {
                throw new Exception('stack_potentialresponse_tree: evaluate_response: attempted to jump to a potential response which does not exist in this question.  This is a question authoring/validation problem.');
            }

            if (array_key_exists($nodekey, $visitednodes)) {
                $results['answernote'][] = '[PRT-CIRCULARITY]=' . $nodekey;
                break;
            }

            $visitednodes[$nodekey] = true;
            list($results, $nodekey) = $this->nodes[$nodekey]->traverse($results, $nodekey, $cascontext, $options);

            if ($results['errors']) {
                break;
            }
        }

        // Tidy up the results.
        $feedbackct = new stack_cas_text(implode(' ', $results['feedback']), $cascontext, $seed, 't', false, false);
        $results['feedback'] =  html_writer::tag('div', $feedbackct->get_display_castext(), array('class' => 'PRTFeedback'));
        $results['errors'] .= $feedbackct->get_errors();
        $results['answernote'] = implode(' | ', $results['answernote']);

        return $results;
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

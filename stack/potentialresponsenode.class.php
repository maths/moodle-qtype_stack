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
 * Node in a potential response tree.
 *
 * @copyright  2012 University of Birmingham
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/answertest/controller.class.php');

/**
 * A node in a potential response tree.
 *
 * @copyright  2012 University of Birmingham
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class stack_potentialresponse_node {

    /*
     * @var stack_cas_casstring Hold's nominal "student's answer".
     */
    public $sans;

    /*
     * @var stack_cas_casstring Hold's nominal "teacher's answer".
     */
    public $tans;

    /*
     * @var string Name of answer test to be used here.
     */
    private $answertest;

    /*
     * @var string Any options for the answer test
     */
    public $atoptions;

    /*
    * @var boolean Suppress any feedback from the answer test itself?
     */
    private $quiet;

    /*
     * @var string Private notes/memos about this potential response.
     */
    private $notes;

    /*
     * @var boolean Has this node been visited before?  Used for lazy evaluation, and also to ensure the node isn't visited twice.
     */
    private $instantiated;

    /*
     * @var array Holds the result of doing the answer test.
     */
    private $result;

    /*
     * @var array Holds the information for each branch.
     */
    private $branches;

    public function __construct($sans, $tans, $answertest, $atoptions = null, $quiet=false, $notes='') {
        if (is_a($sans, 'stack_cas_casstring')) {
            $this->sans        = $sans;
        } else {
            throw new Exception('stack_potentialresponse_node: sans must be a stack_cas_casstring');
        }
        if (is_a($tans, 'stack_cas_casstring')) {
            $this->tans        = $tans;
        } else {
            throw new Exception('stack_potentialresponse_node: tans must be a stack_cas_casstring');
        }
        $this->answertest  = $answertest;
        if (!is_bool($quiet)) {
            throw new Exception('stack_potentialresponse_node: quiet must be a boolean.');
        } else {
            $this->quiet        = $quiet;
        }

        $this->atoptions = $atoptions;// This is not a stack_options class, but a string.
        $this->notes = $notes;

        $this->instantiated = false;
        $this->branches     = array();
    }

    /*
     * Add information into each branch
     */
    public function add_branch($tf, $mod, $mark, $penalty, $nextpr, $feedback, $answernote) {
        if (1===$tf or 0===$tf) {
            $branch = $tf;
        } else {
            throw new Exception('stack_potentialresponse_node: branches can only be 0 or 1.');
        }
        $this->branches[$branch]['markmodification']     = $mod;
        $this->branches[$branch]['mark']                 = $mark;
        $this->branches[$branch]['penalty']              = $penalty;
        $this->branches[$branch]['nextpr']               = $nextpr;
        $this->branches[$branch]['feedback']             = $feedback;
        $this->branches[$branch]['answernote']           = $answernote;
    }

    /*
     * Has this node been visited before?  Uses instantiation infomation.
     */
    public function do_test($nsans, $ntans, $ncasopts, $options) {

        if (false === $ncasopts) {
            $ncasopts = $this->atoptions;
        }
        $at = new stack_ans_test_controller($this->answertest, $nsans, $ntans, $options, $ncasopts);
        $at->do_test();
        $result['result'] = $at->get_at_mark();
        $this->instantiated = true;

        if ($result['result']) {
            $branch = 1;
        } else {
            $branch = 0;
        }

        $result['valid']  = $at->get_at_valid();
        $result['errors'] = $at->get_at_errors();

        $result['markmodification'] = $this->branches[$branch]['markmodification'];
        $result['mark']             = $this->branches[$branch]['mark'];
        $result['penalty']          = $this->branches[$branch]['penalty'];
        $result['nextpr']           = $this->branches[$branch]['nextpr'];

        if (''!=trim($at->get_at_answernote()) and ''!=trim($this->branches[$branch]['answernote'])) {
            $result['answernote']       = ' | '.$at->get_at_answernote().' | '.$this->branches[$branch]['answernote'];
        } else {
            $result['answernote']       = ' | '.trim($at->get_at_answernote().' '.$this->branches[$branch]['answernote']);
        }

        // If the answer test is running in "quiet mode" we suppress any automatically generated feedback from the answertest itself.
        if ($this->quiet) {
            $result['feedback']     = $this->branches[$branch]['feedback'];
        } else {
            $result['feedback']     = trim($at->get_at_feedback()).' '.trim($this->branches[$branch]['feedback']);
        }
        $result['feedback']     = trim($result['feedback']);

        $this->result = $result;
        return $result;
    }

    /*
    * Has this node been visited before?  Uses instantiation infomation.
    */
    public function visited_before() {
        return $this->instantiated;
    }

    /*
    * Clean out information from previous traverses.
    */
    public function reset() {
        $this->instantiated = false;
        $this->results      = array();
    }

    /*
     * Does this answer test actually require options,?
     */
    public function process_atoptions() {
        $at = new stack_ans_test_controller($this->answertest, '', '', null, '');
        return $at->process_atoptions();
    }

    public function update_mark($oldmark){
        if (!$this->instantiated) {
            throw new Exception('stack_potentialresponse_node: potential response must be instantiated before marks can be updated.');
        }

        switch($this->result['markmodification']) {
            case '=':
                $newmark = $this->result['mark'];
                break;

            case '+':
                $newmark = $oldmark + $this->result['mark'];
                break;

            case '-':
                $newmark = $oldmark - $this->result['mark'];
                break;

            case '=AT':
                $newmark = $this->result['mark'];
                break;

            default:
                throw new Exception('stack_potentialresponse_node: update_mark called with invalid mark modificiation method: '.$result['markmodification']);
        }//switch
        return $newmark;
    }

    /**
     * @return array of CAS strings. These cas strings include the names of all
     * the input variables that are required by this node.
     */
    public function get_required_cas_strings() {

        $ct = new stack_cas_text($this->branches[0]['feedback'] . $this->branches[1]['feedback']);
        $requiredcasstrings = $ct->get_all_raw_casstrings();

        $requiredcasstrings[] = $this->sans->get_raw_casstring();
        $requiredcasstrings[] = $this->tans->get_raw_casstring();

        if ($this->process_atoptions() && trim($this->atoptions) != '') {
            $requiredcasstrings[] = $this->atoptions;
        }

        return $requiredcasstrings;
    }

    /**
     * Get the context variables that this node uses, so that they can be
     * pre-evaluated prior to transversing the tree.
     * @param string $key used to make the variable names unique to this node.
     * @return array of stack_cas_casstring
     */
    public function get_context_variables($key) {
        $variables = array();

        $this->sans->set_key('PRSANS' . $key);
        $variables[] = $this->sans;

        $this->tans->set_key('PRTANS' . $key);
        $variables[] = $this->tans;

        if ($this->process_atoptions()) {
            $atopts = new stack_cas_casstring($this->atoptions, 't', false, false);
            $atopts->set_key('PRATOPT' . $key);
            $variables[] = $atopts;
        }

        return $variables;
    }
}

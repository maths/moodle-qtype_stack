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
 * Individual "potential responses".
 *
 * @copyright  2012 University of Birmingham
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . 'answertest/controller.class.php');

/**
 * Individual "potential responses".
 *
 * @copyright  2012 University of Birmingham
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class stack_potentialresponse {

    /*
     * @var stack_cas_casstring Hold's nominal "student's answer".
     */
    private $sans;

    /*
     * @var stack_cas_casstring Hold's nominal "teacher's answer".
     */
    private $tans;

    /*
     * @var string Name of answer test to be used here.
     */
    private $answertest;

    /*
     * @var string Any options for the answer test
     */
    private $atoptions;

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
    
    public function __construct($sans, $tans, $answertest, $atoption = null, $quiet=false) {
        $this->sans        = $sans;
        $this->tans        = $tans;
        $this->answertest  = $answertest;
        $this->atoption    = $atoption;

        $this->instantiated = false;
        $this->branches     = array();
    }

    /*
     * Add information into each branch
     */
    public function add_branch($tf, $mod, $mark, $penalty, $nextpr, $feedback, $answernote) {
        if ($tf) {
            $branch = 1;
        } else {
            $branch = 0;
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

        if ($this->result) {
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

        //TODO remove unesessary '|'s.
        $result['answernote']       = $at->get_at_answernote().' | '.$this->branches[$branch]['answernote'];

        // If the answer test is running in "quiet mode" we suppress any automatically generated feedback from the answertest itself.
        if ($this->quiet) {
            $result['feedback']     = $this->branches[$branch]['feedback'];
        } else {
            $result['feedback']     = $at->get_at_feedback().' | '.$this->branches[$branch]['feedback'];
        }

        $this->result = $result;
        return $result;
    }

    /*
    * Has this node been visited before?  Uses instantiation infomation.
    */
    public function visited_before() {
        return $this->instantiated;
    }

    public function get_sans() {
        return $this->sans;
    }

    public function get_tans() {
        return $this->tans;
    }

    public function get_atoptions() {
        return $this->atoptions;
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
            throw new Exception('stack_potentialresponse: potential response must be instantiated before marks can be updated.');
        }

        switch($result['markmodification']) {
            case '=':
                $newmark = $result['mark'];
                break;

            case '+':
                $newmark = $oldmark + $result['mark'];
                break;

            case '-':
                $newmark = $oldmark - $result['mark'];
                break;

            case '=AT':
                $newmark = $result['mark'];
                break;
        }//switch
        return $newmark;
    }

}
<?php
// This file is part of Stack - http://stack.maths.ed.ac.uk/
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

// Node in a potential response tree.
//
// @copyright  2012 University of Birmingham.
// @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.

require_once(__DIR__ . '/answertest/controller.class.php');
require_once(__DIR__ . '/maximaparser/utils.php');

/**
 * A node in a potential response tree.
 *
 * @copyright  2012 University of Birmingham
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class stack_potentialresponse_node {

    /**
     * @var int the node id.
     */
    public $nodeid;

    /**
     * @var stack_ast_container Holds nominal "student's answer".
     */
    public $sans;

    /**
     * @var stack_ast_container Holds nominal "teacher's answer".
     */
    public $tans;

    /**
     * @var string Name of answer test to be used here.
     */
    private $answertest;

    /**
     * @var array Options taken by the answer test.
     */
    private $atoptions;

    /**
     * @var bool Suppress any feedback from the answer test itself?
     */
    private $quiet;

    /**
     * @var string Private notes/memos about this potential response.
     */
    private $notes;

    /**
     * @var array Holds the information for each branch.
     */
    private $branches;

    public function __construct($sans, $tans, $answertest, $atoptions = null, $quiet = false, $notes = '', $nodeid = 0) {
        if (is_a($sans, 'stack_ast_container')) {
            $this->sans        = $sans;
        } else {
            throw new stack_exception('stack_potentialresponse_node: sans must be a stack_ast_container');
        }
        if (is_a($tans, 'stack_ast_container')) {
            $this->tans        = $tans;
        } else {
            throw new stack_exception('stack_potentialresponse_node: tans must be a stack_ast_container');
        }
        $this->answertest  = $answertest;
        if (!is_bool($quiet)) {
            throw new stack_exception('stack_potentialresponse_node: quiet must be a boolean.');
        } else {
            $this->quiet        = $quiet;
        }

        /*
         * For some tests there is an option assume_pos. This will be evaluated by maxima (since this is also the name
         * of a maxima variable).  So, we need to protect the name from being evaluated.
         */
        $op = $atoptions;
        $reps = array('assume_pos' => 'assumepos', 'assume_real' => 'assumereal');
        foreach ($reps as $key => $val) {
            $op = str_replace($key, $val, $op);
        }
        $this->atoptions = $op;

        $this->notes = $notes;
        $this->nodeid = $nodeid;

        $this->branches = array();
    }

    /**
     * Add information into each branch
     *
     * @param int $trueorfalse 0 or 1, which branch to set.
     * @param string $mod score modification method. One of the values recognised by {@link update_score()}
     * @param float $score score value used by update_score.
     * @param float $penalty penalty for this branch.
     * @param int $nextnode index of the node to process next on this branch.
     * @param string $feedback feedback for this branch.
     * @param int $feedbackformat one of Moodle's FORMAT_... constants.
     * @param string $answernote answer note for this branch.
     */
    public function add_branch($trueorfalse, $mod, $score, $penalty, $nextnode, $feedback, $feedbackformat, $answernote) {
        if ($trueorfalse !== 0 && $trueorfalse !== 1) {
            throw new stack_exception('stack_potentialresponse_node: branches can only be 0 or 1.');
        }

        $this->branches[$trueorfalse] = array(
            'scoremodification' => $mod,
            'score'             => $score,
            'penalty'           => $penalty,
            'nextnode'          => $nextnode,
            'feedback'          => trim($feedback),
            'feedbackformat'    => trim($feedbackformat),
            'answernote'        => trim($answernote),
        );
    }

    /**
     * Actually execute the test for this node.
     *
     * @param $nsans
     * @param $ntans
     * @param $ncasopts
     * @param $options
     * @param stack_potentialresponse_tree_state $results
     * @return int the next node to evaluate (or -1 to stop).
     */
    public function do_test($nsans, $ntans, $ncasopts, $options, $contextsession,
            stack_potentialresponse_tree_state $results) {

        // If an option is required by the answer test, but not processed by the CAS then take the raw value.
        if ($this->required_atoptions() && !$this->process_atoptions()) {
            $ncasopts = $this->atoptions;
        }
        $at = new stack_ans_test_controller($this->answertest, $nsans, $ntans, $ncasopts, $options, $contextsession);
        $at->do_test();

        $testpassed = $at->get_at_mark();
        if ($testpassed) {
            $resultbranch = $this->branches[1];
            $branchname = 'prtnodetruefeedback';
        } else {
            $resultbranch = $this->branches[0];
            $branchname = 'prtnodefalsefeedback';
        }

        if ($at->get_at_answernote()) {
            $results->add_answernote($at->get_at_answernote());
            $trace['atanswernote'] = $at->get_at_answernote();
        }
        if ($resultbranch['answernote']) {
            $results->add_answernote($resultbranch['answernote']);
        }

        // If the answer test is running in quiet mode we suppress any
        // automatically generated feedback from the answertest itself.
        if (!$this->quiet && $at->get_at_feedback()) {
            $results->add_feedback($at->get_at_feedback());
        }
        if ($resultbranch['feedback']) {
            $results->add_feedback($resultbranch['feedback'], $resultbranch['feedbackformat'],
                    $branchname, $this->nodeid);
        }

        $results->_valid = $results->_valid && $at->get_at_valid();
        $results->_score = $this->update_score($results->_score, $resultbranch);

        if ($resultbranch['penalty'] !== '') {
            $results->_penalty = $resultbranch['penalty'];
        }

        if ($at->get_at_errors()) {
            $results->_errors .= $at->get_at_errors();
            // This builds a basic representation of the CAS command used.
            $cascommand = '<pre>AT'.$this->answertest . '(' . $nsans . ', ' . $ntans;
            if ($ncasopts != '') {
                $cascommand .= ', ' . $ncasopts;
            }
            $cascommand .= ')</pre>';
            $results->_debuginfo .= $cascommand;
            $results->_debuginfo .= $at->get_debuginfo();
        }

        $results->add_trace($at->get_trace());
        return $resultbranch['nextnode'];
    }

    /**
     * Traverse this node, updating the results array that is used by
     * {@link stack_potentialresponse_tree::evaluate_response()}.
     *
     * @param stack_potentialresponse_tree_state $results to be updated.
     * @param int $key the index of this node.
     * @param stack_cas_session2 $cascontext the CAS context that holds all the relevant variables.
     * @param array $answers
     * @param stack_options $options
     * @param cas_evaluatable[] $cascontext
     * @return int the next node to evaluate, or -1 to stop.
     */
    public function traverse($results, $key, $cascontext, $answers, $options, $contextsession) {

        $errorfree = true;
        if ($cascontext->get_by_key('PRSANS' . $key)->get_errors() !== '') {
            $results->_errors .= $cascontext->get_by_key('PRSANS' . $key)->get_errors();
            $results->add_feedback(' '.stack_string('prtruntimeerror',
                    array('node' => 'PRSANS'.($key + 1), 'error' => $cascontext->get_by_key('PRSANS' . $key)->get_errors())));
            $errorfree = false;
        }
        if ($cascontext->get_by_key('PRTANS' . $key) !== null && $cascontext->get_by_key('PRTANS' . $key)->get_errors() !== '') {
            $results->_errors .= $cascontext->get_by_key('PRTANS' . $key)->get_errors();
            $results->add_feedback(' '.stack_string('prtruntimeerror',
                    array('node' => 'PRTANS'.($key + 1), 'error' => $cascontext->get_by_key('PRTANS' . $key)->get_errors())));
            $errorfree = false;
        }
        if ($cascontext->get_by_key('PRATOPT' . $key) !== null && $cascontext->get_by_key('PRATOPT' . $key)->get_errors() !== '') {
            $results->_errors .= $cascontext->get_by_key('PRATOPT' . $key)->get_errors();
            $results->add_feedback(' '.stack_string('prtruntimeerror',
                    array('node' => 'PRATOPT'.($key + 1), 'error' => $cascontext->get_by_key('PRATOPT' . $key)->get_errors())));
            $errorfree = false;
        }
        if (!($errorfree)) {
            return -1;
        }
        // At this point we need to subvert the CAS.  If the sans or tans is *exactly* the name of one of the
        // inputs, then we should use the casstring (not the rawcasstring).  Running the value through the CAS strips
        // off trailing zeros, making it effectively impossible to run the numerical sigfigs tests.

        // TODO: refactor this to pass the ast, then we won't need to "subvert the CAS".....
        $sans   = $cascontext->get_by_key('PRSANS' . $key);
        $tans   = $cascontext->get_by_key('PRTANS' . $key);
        foreach ($answers as $cskey => $val) {
            // Check whether the raw input to the node exactly matches one of the answer names.
            $cs = $this->sans;
            if ($cs->get_valid() && trim($cs->get_inputform(true)) == trim($cskey)) {
                $sans = $cascontext->get_by_key($cskey);
            }
            $cs = $this->tans;
            if ($cs->get_valid() && trim($cs->get_inputform(true)) == trim($cskey)) {
                $tans = $cascontext->get_by_key($cskey);
            }
        }
        $atopts = $cascontext->get_by_key('PRATOPT' . $key);
        // If we can't find atopts then they were not processed by the CAS.
        // They might still be some in the potential response which do not
        // need to be processed.
        if (false === $atopts) {
            $atopts = null;
        }

        return $this->do_test($sans, $tans, $atopts, $options, $contextsession, $results);
    }

    /*
     * Does this answer test actually require options to be processed by the CAS?
     */
    public function process_atoptions() {
        return stack_ans_test_controller::process_atoptions($this->answertest);
    }

    /*
     * Does this answer test actually require options?
     */
    public function required_atoptions() {
        return stack_ans_test_controller::required_atoptions($this->answertest);
    }

    protected function update_score($oldscore, $resultbranch) {
        switch($resultbranch['scoremodification']) {
            case '=':
                return $resultbranch['score'];

            case '+':
                return $oldscore + $resultbranch['score'];

            case '-':
                return $oldscore - $resultbranch['score'];

            default:
                throw new stack_exception('stack_potentialresponse_node: update_score called ' .
                        'with invalid score modificiation method: ' . $resultbranch['scoremodification']);
        }
    }

    public function get_variable_usage(array $updatearray = array()): array {
        $ct = new stack_cas_text($this->branches[0]['feedback'] . $this->branches[1]['feedback']);
        $updatearray = $ct->get_variable_usage($updatearray);
        $updatearray = $this->sans->get_variable_usage($updatearray);
        $updatearray = $this->tans->get_variable_usage($updatearray);

        if ($this->process_atoptions() && trim($this->atoptions) != '') {
            // Eventtually at-options will be an ast_container, not yet though
            // so if it is not an empty string then it is parseable.
            $ast = maxima_parser_utils::parse($this->atoptions);
            $updatearray = maxima_parser_utils::variable_usage_finder($ast, $updatearray);
        }

        return $updatearray;
    }

    /**
     * Get the context variables that this node uses, so that they can be
     * pre-evaluated prior to traversing the tree.
     * @param string $key used to make the variable names unique to this node.
     * @return array of stack_ast_container
     */
    public function get_context_variables($key) {
        $variables = array();

        // Do we simplify the expressions in the context variables?
        $simp = 'false';
        if (stack_ans_test_controller::simp($this->answertest)) {
            $simp = 'true';
        }
        $sf = stack_ast_container::make_from_teacher_source('simp:' . $simp, '', new stack_cas_security());
        $variables[0] = $sf;

        // We need to clone these, so we can set the key for evaluation and the simplification context.
        $variables[1] = clone $this->sans;
        $variables[1]->set_key('PRSANS' . $key);
        $variables[2] = clone $this->tans;
        $variables[2]->set_key('PRTANS' . $key);

        if (stack_ans_test_controller::process_atoptions($this->answertest) && trim($this->atoptions) != '') {
            // We always simplify the options field.
            $nodeoptions = 'ev(' . $this->atoptions . ',simp)';
            $cs = stack_ast_container::make_from_teacher_source('PRATOPT' . $key . ':' . $nodeoptions,
                    '', new stack_cas_security());
            $variables[] = $cs;
        }
        return $variables;
    }

    /**
     * Returns answer notes, used for question testing.
     * @return array string Of all the answer notes this tree might produce.
     */
    public function get_answer_notes() {
        return array($this->branches[true]['answernote'], $this->branches[false]['answernote']);
    }

    /**
     * @return object with fields falsenote, falsescore, truenote, truescore.
     */
    public function summarise_branches() {
        $summary = new stdClass();
        $summary->falsenextnode  = $this->branches[false]['nextnode'];
        $summary->falsenote      = $this->branches[false]['answernote'];
        $summary->falsescore     = $this->branches[false]['score'];
        $summary->falsescoremode = $this->branches[false]['scoremodification'];
        $summary->truenextnode   = $this->branches[true]['nextnode'];
        $summary->truenote       = $this->branches[true]['answernote'];
        $summary->truescore      = $this->branches[true]['score'];
        $summary->truescoremode  = $this->branches[true]['scoremodification'];
        return $summary;
    }

    public function get_maxima_representation() {
        $ncasoptions = null;
        if ($this->required_atoptions()) {
            $ncasoptions = stack_ast_container::make_from_teacher_source($this->atoptions);
        }
        $at = new stack_ans_test_controller($this->answertest, $this->sans, $this->tans, $ncasoptions, null);
        return $at->get_trace(false);
    }

    /**
     * @return string just the name of the test.
     */
    public function get_test(): string {
        return $this->answertest;
    }
}

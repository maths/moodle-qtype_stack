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

require_once(__DIR__ . '/cas/castext2/castext2_evaluatable.class.php');
require_once(__DIR__ . '/cas/keyval.class.php');
require_once(__DIR__ . '/cas/ast.container.class.php');


// Deals with whole potential response trees. 
// A rewrite dropping everything not needed for compiled PRTs.
// Works as the compiler for the matching evaluatable.
// Otherwise used as a store for meta-data related to the question-model.
class stack_potentialresponse_tree_lite {

    /** @var string Name of the PRT. */
    private $name;

    /** @var bool Should this PRT simplify when its arguments are evaluated? */
    private $simplify;

    /** @var float total amount of fraction available from this PRT. Zero is possible for formative PRT questions. */
    private $value;

    /** @var stack_cas_session2 Feeback variables. */
    private $feedbackvariables;

    /** @var string index of the first node. */
    private $firstnode;

    /** @var object the nodes of the tree. Just raw DB-objects. */
    private $nodes;

    /** @var int The feedback style of this PRT.
     *  0. Formative PRT: Errors and PRT feedback only.
     *     Does not contribute to the attempt grade, no grade displayed ever, no standard feedback.
     *  1. Standard PRT.
     *  Making this an integer now, and not a Boolean, will allow future options (such as "compact" or "symbol only")
     *  without further DB upgrades.
     **/
    private $feedbackstyle;

    public function __construct($prtdata) {
        $this->name          = $prtdata->name;
		$this->simplify      = (bool) $prtdata->autosimplify;
		$this->feedbackstyle = (int) $prtdata->feedbackstyle;
 
		// Note lets leave the scaling to other levels.
        $this->value         = $prtdata->value;

        $this->feedbackvariables = $prtdata->feedbackvariables;

        $this->nodes = $prtdata->nodes;
        $this->firstnode = (string) $prtdata->firstnodename;
        // Do nothing else, this is just a holder of data that will fetch things on demand
        // and even then just to be cached.
    }

    public function get_value() {
        return $this->value;
    }

    public function get_name() {
        return $this->name;
    }

    /**
     * A "formative" PRT is a PRT which does not contribute marks to the question.
     * This affected whether a response is "complete", and how marks are shown for feedback.
     * @return boolean
     */
    public function is_formative() {
        // Note, some of this logic is duplicated in renderer.php before we have instantiated this class.
        if ($this->feedbackstyle === 0) {
            return true;
        }
        return false;
    }

    /**
     * @return int.
     */
    public function get_feedbackstyle() {
        return $this->feedbackstyle;
    }

    /**
     * @return string The keyval-bit for some version changes.
     */
    public function get_feedbackvariables_keyvals() {
        if (null === $this->feedbackvariables) {
            return '';
        }
        return $this->feedbackvariables;
    }

    /**
     * @return array Returns the answer tests used by this PRT for version changes.
     */
    public function get_answertests(): array {
        $tests = array();
        foreach ($this->nodes as $node) {
            $tests[$node->answertest] = true;
        }
        return $tests;
    }

    /**
     * @return string Representation of the PRT for Maxima offline use.
     */
    public function get_maxima_representation() {
        // What? The full compiled thing or something else?
        // Probably needs pretty printting if the full compiled thing.
        // Also probably should drop the feedback bits from it if doing output.
        // Probably parse the compiled one and filter out items dealing with feedback.
        return 'TODO MAXIMA REPRESENTATION';
    }

    /**
     * @return array All the "sans" strings used in the nodes with test requiring a raw input.
     */
    public function get_raw_sans_used() {
        $sans = array();
        foreach ($this->nodes as $key => $node) {
            if (stack_ans_test_controller::required_raw($node->answertest)) {
                $name = (string) $this->get_name() . '-' . ($key + 1);
                $sans[$name] = $node->sans;
            }
        }
        return $sans;
    }

    /**
     * This lists all possible answer notes, used for question testing.
     * @return array string Of all the answer notes this tree might produce.
     */
    public function get_all_answer_notes() {
        $nodenotes = array();
        foreach ($this->nodes as $node) {
            $nodenotes = array_merge($nodenotes, [$node->trueanswernote, $node->falseanswernote]);
        }
        $notes = array('NULL' => 'NULL');
        foreach ($nodenotes as $note) {
            $notes[$note] = $note;
        }
        return $notes;
    }

    private function get_reverse_post_order_nodes(): array {
    	// i.e. list the nodes in the order they are last visited to allow simple
        // guard clauses... nice feature of acyclic graphs... drops the orphans too.
        $order   = [];
        $visited = [];
        $this->po_recurse($this->nodes[$this->firstnode], $order, $visited);
        return array_reverse($order);
    }

    private function get_node($name) {
    	// Simple getter that handles the cases where the key is bad or null.
    	if (isset($this->nodes[$name])) {
    		return $this->nodes[$name];
    	}
    	return null;
    }

    private function po_recurse($node, array &$postorder, array &$visited): array {
        $truenode                 = $this->get_node($node->truenextnode);
        $falsenode                = $this->get_node($node->falsenextnode);
        $visited[$node->nodename] = $node;
        if ($truenode != null && !array_key_exists($truenode->nodename, $visited)) {
            $this->po_recurse($truenode, $postorder, $visited);
        }
        if ($falsenode != null && !array_key_exists($falsenode->nodename, $visited)) {
            $this->po_recurse($falsenode, $postorder, $visited);
        }

        $postorder[] = $node;
        return $postorder;
    }

    // Builds a single function to evaluate the PRT, uses the known inputs and 
    // bound vars to decide what to pass into the function as arguments and
    // what to use as local variables.
    // The returned array contains the function declaration, its call signature,
    // and any necessary additional preamble, i.e. textput rules and the like.
    public function compile(array $inputs, array $boundvars, $defaultpenalty, $security): array {
    	$R = ['sig' => '', 'def' => '', 'cv' => null, 'be' => null, 'required' => [], 'units' => false];
    	// Note these variables are initialised before the feedback-vars and if not forbidden
    	// could be directly set in the vars. The logic does not actually require any PRT-nodes.
    	$body = '(%PRT_FEEDBACK:"",' . // The variable holding the feedback CASText2
    			'%PRT_SCORE:0,' .
    			'%PRT_PENALTY:0,' .
    			'%PRT_PATH:[],' . // The nodes visited and their answertest notes.
                '%PRT_EXIT_NOTE: [],' . // The notes for nodes not the answertests.
    			'%_EXITS:{},'; // This tracks the exits from nodes so that we can decide the next node.

    	$fv = new stack_cas_keyval($this->feedbackvariables);
        $fv->set_security($security);
        $fv->get_valid();
    	$fv = $fv->compile('PRT:' . $this->name . ': feedback-variables');
    	$R['be'] = $fv['blockexternal'];
    	$R['cv'] = $fv['contextvariables'];
    	$usage = $fv['references']; // We need to track the usage of vars over the whole thing.
    	$usage['write']['%PRT_FEEDBACK'] = true;
    	$usage['write']['%PRT_SCORE'] = true;
    	$usage['write']['%PRT_PENALTY'] = true;
    	$usage['write']['%PRT_PATH'] = true;
        $usage['write']['%PRT_EXIT_NOTE'] = true;
    	$usage['write']['%_EXITS'] = true;

    	if ($fv['statement'] !== null) {
	    	// The simplification status for feedback vars. If we have any.
	    	if ($this->simplify) {
	    		$body .= 'simp:true,';
	    	} else {
	    		$body .= 'simp:false,';
	    	}
	    	$body .= $fv['statement'] . ',';
	    }

    	// Lets build the node precedence map, i.e. through which edges are nodes reachable.
    	$precedence = [];
    	$nodes = $this->get_reverse_post_order_nodes();

    	foreach ($nodes as $node) {
    		$truenode  = $this->get_node($node->truenextnode);
        	$falsenode = $this->get_node($node->falsenextnode);
        	if ($truenode !== null) {
        		if (!isset($precedence[$truenode->nodename])) {
        			$precedence[$truenode->nodename] = [];
        		}
        		$precedence[$truenode->nodename][] = '[' . stack_utils::php_string_to_maxima_string($node->nodename) . ',true]';
        	}
        	if ($falsenode !== null) {
        		if (!isset($precedence[$falsenode->nodename])) {
        			$precedence[$falsenode->nodename] = [];
        		}
        		$precedence[$falsenode->nodename][] = '[' . stack_utils::php_string_to_maxima_string($node->nodename) . ',false]';
        	}
    	}

    	// Then we need to iterate the nodes and generate the matching logic for them all.
    	$first = true;
    	foreach ($nodes as $node) {
            if (strpos($node->answertest, 'Units') === 0) {
                $R['units'] = true;
            }
    		if ($first) {
    			// The first node is not conditional.
    			$first = false;
    			$body .= '(';
    		} else {
    			$body .= 'if not emptyp(intersection(%_EXITS,{' . implode(',', $precedence[$node->nodename]) .'})) then (';
    		}
            [$nc, $usage] = $this->compile_node($node, $usage, $defaultpenalty, $security);
    		$body .= $nc . '),';
    	}

    	// Finally round the score and return the relevant details.
    	$body .= '%PRT_SCORE:ev(float(floor(max(min(%PRT_SCORE,1.0),0.0)*1000)/1000),simp),';
        $body .= '%PRT_PENALTY:ev(float(floor(max(min(%PRT_PENALTY,1.0),0.0)*1000)/1000),simp),';
        $body .= '[%PRT_PATH,%PRT_SCORE,%PRT_PENALTY,%PRT_FEEDBACK,%PRT_EXIT_NOTE]';
        $body .= ')'; // The first char.
    
        // Now we have that long command and will need to figure out the full variable usage
        // and what to protect. Anythin being written here needs to be either scoped as local
        // or brought in as function-arguments to avoid leakage.
        $asarg = [];
        $aslocal = [];
        $forcelocals = [ // We do not accept values for these from the outside.
            '%PRT_FEEDBACK' => true,
            '%PRT_SCORE' => true,
            '%PRT_PENALTY' => true,
            '%PRT_PATH' => true,
            '%PRT_EXIT_NOTE' => true,
            '%_EXITS' => true,
            'simp'
        ];
        // We want to make sure that any writing inside this logic does not affect 
        // the outside. However, some of the vars that could be written come from 
        // the outside and need to be pased in as arguments.
        foreach ($usage['write'] as $key => $ignore) {
            if (isset($forcelocals[$key])) {
                $aslocal[$key] = true;
            } else if (isset($boundvars[$key]) || isset($inputs[$key])) {
                $asarg[$key] = true;
            } else {
                $aslocal[$key] = true;
            }
        }
        // We could also have anything we read as an argument, but as we assume
        // that everythign else in the code also avoids leakage then that is not 
        // necessary. We still colelct the input requirements though.
        foreach ($usage['read'] as $key => $ignore) {
            if (isset($inputs[$key])) {
                $R['required'][$key] = true;
                $asarg[$key] = true;
            } 
            if ($key === '_INPUT_STRING') {
                $asarg[$key] = true;
            }
        }

        // Deal with GCL... and the limited function args on it.
        if (count($asarg) > 40) {
            // Just cut down to less and hope no spooky interactions arise.
            // In any case it is an overly complex PRT if it takes that many
            // parameters or the parameters are used at a too low a level.
            // Start by dropping inputs people should understand that one does 
            // not write on them.
            $R['GCL-warn'] = true;
            foreach ($inputs as $key => $ignore) {
                if (isset($asarg[$key])) {
                    unset($asarg[$key]);
                }
            }
            if (count($asarg) > 40 && isset($asarg['_INPUT_STRING'])) {
                unset($asarg['_INPUT_STRING']);
            }
            if (count($asarg) > 40) {
                // If that was not enough drop from the tail.
                $asarg = array_slice($asarg, 0, 39, true);
            }
        }

        // Then the definition of the function.
        $R['sig'] = 'prt_' . $this->name . '(' . implode(',', array_keys($asarg)) . ')';

        $R['def'] = $R['sig'] . ':=block([' . implode(',', array_keys($aslocal)) . '],' . $body . ')';

        return $R;
    }

    private function compile_node($node, $usage, $defaultpenalty, $security): array {
    	// Start by turning simplification off for the call of the answer-test.
    	// Or on...
    	// Really all tests should be so that one calls them without simplification
    	// of anything and if they need to simplify inside the test then the test 
    	// does it and if the author wants to simplify outside the test they do so.
    	$body = 'simp:false,';
    	if (stack_ans_test_controller::simp($node->answertest)) {
    		$body = 'simp:true,';
    	}

    	// TODO: make this saner, the way Stateful lets the tests do their own
    	// call construction might duplicate things but it does not require this 
    	// much knowledge about the shape of things.
    	// We have no validation for these requirements.
    	// TODO: choose whether we error catch sans/tans/options separately or
    	// at the whole test level. Now at test level.
    	$AT = '%_TMP:AT' . $node->answertest . '(' . $node->sans;

    	if ($node->tans === null || trim($node->tans) === '') {
    		$AT .= ',""';
    	} else {
    		$AT .= ',' . $node->tans;
    	}

		if (stack_ans_test_controller::required_atoptions($node->answertest)) {
			// Simplify these. Mainly the sigfigs as the test has a history of not doing it.
			$AT .= ',ev(' . $node->testoptions . ',simp)';	
		}

		if (stack_ans_test_controller::required_raw($node->answertest)) {
			// The sans better be just a raw input ref... If not things break.
			$AT .= ',stackmap_get(_INPUT_STRING,' . stack_utils::php_string_to_maxima_string(trim($node->sans)) . ')';
		}
		$AT .= ')';

		// Do a parse at this point to normalise the statement. And to collect usages.
		$context = 'PRT:' . $this->name . ' NODE:';
        // The context needs to note that in STACK the nodes are numbered and not named.
        // And those numbers are displayed from 1 while actually are from 0...
        if (is_numeric($node->nodename)) {
            $context .= (1+$node->nodename) . ' ';
        } else {
            $context .= $node->nodename . ' ';
        }
		$cs = stack_ast_container::make_from_teacher_source($AT, $context . 'answertest');
        $cs->set_securitymodel($security);
        if (!$cs->get_valid()) {
            throw new stack_exception('Error in ' . $context . ' answertest parameters.');
        }
		$usage = $cs->get_variable_usage($usage); // Update the references.

		// Add the error catching wrapping:
		$body .= '_EC(errcatch(' . $cs->get_evaluationform() . '),' . stack_utils::php_string_to_maxima_string($context . 'answertest') . '),';

		// Now based on the results we update things. For the updates we need simp:true.
		$body .= 'simp:true,'; // Hold until score math done.
		$body .= '%PRT_PATH:append(%PRT_PATH,[%_TMP]),'; // Add the raw result to the path.
		$body .= '%_EXITS:union(%_EXITS, {['. stack_utils::php_string_to_maxima_string($node->nodename) . ',%_TMP[2]]}),'; // Which exit we took.

		if ($node->quiet==0) {
			// We need to connect any possible test feedback to the feedback. 
			// Note that as long as the CAS side tests rely on the old translation 
			// logic we need to wrap them to the special `%strans` block.
			$body .= 'if length(%_TMP) > 3 and slength(%_TMP[4]) > 0 then %PRT_FEEDBACK:castext_concat(%PRT_FEEDBACK,["%strans",%_TMP[4]]),';
		}

		// Those were the branch neutral parts, now the branches.
		$body .= 'if %_TMP[2] then (';
        $body .= '%PRT_EXIT_NOTE:append(%PRT_EXIT_NOTE, [' . stack_utils::php_string_to_maxima_string($node->trueanswernote) . ']),';
		// The true branch.
        if (!$this->is_formative()) { // No need for formative. Or do we calculate for analysis?
    		$s = $node->truescore;
            if ($s === null || trim($s) == '') {
                $s = '0';
            }
            $p = $node->truepenalty;
            if ($p === null || trim($p) == '') {
                $p = $defaultpenalty;
            }
            // To save code we only generate error catching evaluation for values that are not numbers.
            if (!is_numeric($s)) {
                $s = stack_ast_container::make_from_teacher_source($s, $context . 'truescore');
                $s->set_securitymodel($security);
                if (!$s->get_valid()) {
                   throw new stack_exception('Error in ' . $context . ' true-score.');
                }
                $s = '_EC(errcatch(%_TMP:' . $s->get_evaluationform() . '),' . stack_utils::php_string_to_maxima_string($context . 'truescore') . ')';
            } else {
                $s = '%_TMP:' . $s;
            }
            if (!is_numeric($p)) {
                $p = stack_ast_container::make_from_teacher_source($p, $context . 'truepenalty');
                $p->set_securitymodel($security);
                if (!$p->get_valid()) {
                   throw new stack_exception('Error in ' . $context . ' true-penalty.');
                }
                $p = '_EC(errcatch(%PRT_PENALTY:' . $p->get_evaluationform() . '),' . stack_utils::php_string_to_maxima_string($context . 'truepenalty') . ')';
            } else {
                $p = '%PRT_PENALTY:' . $p;
            }
            // Now the score mode based logic, I wonder why both score and penalty use the same.
            // TODO: trace the original logic and check how these are tied to each other.
            switch ($node->truescoremode) {
                case '+':
                    $body .= $s . ',%PRT_SCORE:%PRT_SCORE+%_TMP,' . $p;
                    break;
                case '-':
                    $body .= $s . ',%PRT_SCORE:%PRT_SCORE-%_TMP,' . $p;
                    break;
                case '*':
                    $body .= $s . ',%PRT_SCORE:%PRT_SCORE*%_TMP,' . $p;
                    break;
                default: # '='
                    $body .= $s . ',%PRT_SCORE:%_TMP,' . $p;
                    break;
            }
        }
		
    	if ($node->truefeedback !== null && trim($node->truefeedback) !== '') {
            $feedback = $node->truefeedback;
            if (strpos($feedback, '@@PLUGINFILE@@') !== false) {
                $feedback = '[[pfs component="qtype_stack" filearea="prtnodetruefeedback" itemid="' . $node->id . '"]]' .
                                $feedback . '[[/pfs]]';
            }
            if (substr($body, -1) !== '(') { // Depends on whether the score math was done.
                $body .= ','; 
            }
    		// The feedback will be rendered using the simplify setting of the PRT.
	    	if ($this->simplify) {
	    		$body .= 'simp:true,';
	    	} else {
	    		$body .= 'simp:false,';
	    	}
	    	$ct = castext2_evaluatable::make_from_source($feedback, $context . 'truefeedback');
            if(!$ct->get_valid($node->truefeedbackformat, [], $security)) {
               throw new stack_exception('Error in ' . $context . ' true-feedback.');
            }
	    	$cs = stack_ast_container::make_from_teacher_source($ct->get_evaluationform(), $context . 'truefeedback');
			$usage = $cs->get_variable_usage($usage); // Update the references.
	    	$body .= '_EC(errcatch(%PRT_FEEDBACK:castext_concat(%PRT_FEEDBACK,' . $ct->get_evaluationform() . ')),' . stack_utils::php_string_to_maxima_string($context . 'truefeedback') . ')';
		}

		$body .= ') else (';
        $body .= '%PRT_EXIT_NOTE:append(%PRT_EXIT_NOTE, [' . stack_utils::php_string_to_maxima_string($node->falseanswernote) . ']),';
		// The false branch.
        if (!$this->is_formative()) { // No need for formative.
            $s = $node->falsescore;
            if ($s === null || trim($s) == '') {
                $s = '0';
            }
            $p = $node->falsepenalty;
            if ($p === null || trim($p) == '') {
                $p = $defaultpenalty;
            }
            // To save code we only generate error catching evaluation for values that are not numbers.
            if (!is_numeric($s)) {
                $s = stack_ast_container::make_from_teacher_source($s, $context . 'falsescore');
                $s->set_securitymodel($security);
                if (!$s->get_valid()) {
                   throw new stack_exception('Error in ' . $context . ' false-score.');
                }
                $s = '_EC(errcatch(%_TMP:' . $s->get_evaluationform() . '),' . stack_utils::php_string_to_maxima_string($context . 'falsescore') . ')';
            } else {
                $s = '%_TMP:' . $s;
            }
            if (!is_numeric($p)) {
                $p = stack_ast_container::make_from_teacher_source($p, $context . 'falsepenalty');
                $p->set_securitymodel($security);
                if (!$p->get_valid()) {
                   throw new stack_exception('Error in ' . $context . ' false-penalty.');
                }
                $p = '_EC(errcatch(%PRT_PENALTY:' . $p->get_evaluationform() . '),' . stack_utils::php_string_to_maxima_string($context . 'falsepenalty') . ')';
            } else {
                $p = '%PRT_PENALTY:' . $p;
            }
            // Now the score mode based logic, I wonder why both score and penalty use the same.
            // TODO: trace the original logic and check how these are tied to each other.
            switch ($node->falsescoremode) {
                case '+':
                    $body .= $s . ',%PRT_SCORE:%PRT_SCORE+%_TMP,' . $p;
                    break;
                case '-':
                    $body .= $s . ',%PRT_SCORE:%PRT_SCORE-%_TMP,' . $p;
                    break;
                case '*':
                    $body .= $s . ',%PRT_SCORE:%PRT_SCORE*%_TMP,' . $p;
                    break;
                default: # '='
                    $body .= $s . ',%PRT_SCORE:%_TMP,' . $p;
                    break;
            }
		}

    	if ($node->falsefeedback !== null && trim($node->falsefeedback) !== '') {
            $feedback = $node->falsefeedback;
            if (strpos($feedback, '@@PLUGINFILE@@') !== false) {
                $feedback = '[[pfs component="qtype_stack" filearea="prtnodefalsefeedback" itemid="' . $node->id . '"]]' .
                                $feedback . '[[/pfs]]';
            }
            if (substr($body, -1) !== '(') { // Depends on whether the score math was done.
                $body .= ','; 
            }
    		// The feedback will be rendered using the simplify setting of the PRT.
	    	if ($this->simplify) {
	    		$body .= 'simp:true,';
	    	} else {
	    		$body .= 'simp:false,';
	    	}
	    	// TODO: consider the format to be used here.
            $ct = castext2_evaluatable::make_from_source($feedback, $context . 'falsefeedback');
            if (!$ct->get_valid($node->falsefeedbackformat, [], $security)) {
                throw new stack_exception('Error in ' . $context . ' false-feedback.');
            }
            $cs = stack_ast_container::make_from_teacher_source($ct->get_evaluationform(), $context . 'falsefeedback');
			$usage = $cs->get_variable_usage($usage); // Update the references.
	    	$body .= '_EC(errcatch(%PRT_FEEDBACK:castext_concat(%PRT_FEEDBACK,' . $ct->get_evaluationform() . ')),' . stack_utils::php_string_to_maxima_string($context . 'falsefeedback') . ')';
		}

		$body .= ')';
		return [$body, $usage];
    }
}
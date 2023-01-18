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

require_once(__DIR__ . '/cas/ast.container.class.php');
require_once(__DIR__ . '/cas/keyval.class.php');
require_once(__DIR__ . '/cas/castext2/castext2_evaluatable.class.php');
require_once(__DIR__ . '/answertest/controller.class.php');
require_once(__DIR__ . '/../vle_specific.php');

// Deals with whole potential response trees.
// A rewrite dropping everything not needed for compiled PRTs.
// Works as the compiler for the matching evaluatable.
// Otherwise used as a store for meta-data related to the question-model.
class stack_potentialresponse_tree_lite {

    /** @var string Name of the PRT. */
    private $name;

    /** @var id Identifier of the PRT. */
    private $id;

    /** @var bool Should this PRT simplify when its arguments are evaluated? */
    private $simplify;

    /** @var float Total amount of fraction available from this PRT. Zero is possible for formative PRT questions. */
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

    /**
     * A reference to the question to be used for constructing debug messages.
     * DO NOT USE FOR ANYTHING ELSE. Necessary, as we need to know about inputs
     * and other details when building those messages but do not need about those
     * details otherewise.
     */
    private $question = null;

    /**
     * Stores the trace array while being compiled.
     * @var array
     */
    private $trace = [];

    public function __construct($prtdata, $value, $question = null) {
        $this->name          = $prtdata->name;
        $this->simplify      = (bool) $prtdata->autosimplify;
        $this->feedbackstyle = (int) $prtdata->feedbackstyle;

        // TODO move the scaling to other levels.
        $this->value         = $value;

        $this->feedbackvariables = $prtdata->feedbackvariables;

        if (property_exists($prtdata, 'id')) {
            $this->id        = $prtdata->id;
        }

        $this->nodes = $prtdata->nodes;
        foreach ($this->nodes as $node) {
            if (!property_exists($node, 'id')) {
                // Fill in missing values if we have a system
                // that does not have these values.
                $node->id = null;
            }
        }
        $this->firstnode = (string) $prtdata->firstnodename;
        // Do nothing else, this is just a holder of data that will fetch things on demand
        // and even then just to be cached.

        $this->question = $question; // DO NOT USE!
        // Only for get_maxima_representation() and other debug details.
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
        // Get the compiled one and work on it.
        $code = $this->question->get_cached('prt-definition')[$this->name];

        // The bulk tester will get called on questions which no longer work.
        // In this case we want to bail here and not try to parse null in the line below which
        // throws an exception and halts the bulk tester.
        if ($code === null) {
            return stack_string('errors');
        }
        // Parse that and remove some less relevant parts.
        $ast = maxima_parser_utils::parse($code);
        // Remove the feedback rendering parts, no need to see that CASText2.
        $clean = function ($node) {
            if ($node instanceof MP_Operation && $node->op === ':'
                && $node->lhs instanceof MP_Atom && $node->lhs->value === '%PRT_FEEDBACK') {
                if ($node->parentnode instanceof MP_Group) {
                    $i = array_search($node, $node->parentnode->items);
                    unset($node->parentnode->items[$i]);
                    return false;
                } else if ($node->parentnode instanceof MP_FunctionCall
                           && $node->parentnode->name instanceof MP_Atom
                           && $node->parentnode->name->value === 'errcatch'
                           && $node->parentnode->parentnode->parentnode instanceof MP_Group) {
                    // Using array_search here caused an infinite recursion.  No idea why!
                    foreach ($node->parentnode->parentnode->parentnode->items as $i => $item) {
                        if ($item === $node->parentnode->parentnode) {
                            unset($node->parentnode->parentnode->parentnode->items[$i]);
                        }
                    }
                    return false;
                } else if ($node->parentnode instanceof MP_If
                           && $node->parentnode->parentnode instanceof MP_Group) {
                    $i = array_search($node->parentnode, $node->parentnode->parentnode->items);
                    unset($node->parentnode->parentnode->items[$i]);
                    return false;
                }
            }
            return true;
        };
        // @codingStandardsIgnoreStart
        while ($ast->callbackRecurse($clean) !== true) {}
        // @codingStandardsIgnoreEnd

        return $ast->toString(['pretty' => true, 'checkinggroup' => true]);
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
     * @return array All the non-trivial strings used in the node arguments.
     */
    public function get_raw_arguments_used() {
        $ans = array();
        foreach ($this->nodes as $key => $node) {
            $name = (string) $this->get_name() . '-' . ($key + 1);
            if (trim($node->sans) != '') {
                $ans[$name . '-sans'] = $node->sans;
            }
            if (trim($node->tans) != '') {
                $ans[$name . '-tans'] = $node->tans;
            }
        }
        return $ans;
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
        // That is to say, list the nodes in the order they are last visited to allow simple
        // guard clauses... nice feature of acyclic graphs... drops the orphans too.
        $order   = [];
        $visited = [];

        // Due to the old system we need to guess the firstnode if it is not defined.
        if ($this->firstnode === null || $this->firstnode === '') {
            $this->firstnode = array_keys($this->nodes)[0];
        }

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

    // Summary of the nodes, for use in various logics that track answernotes and scores.
    public function get_nodes_summary(): array {
        $summary = [];
        foreach ($this->nodes as $node) {
            $n = new stdClass();
            $n->truenextnode    = $node->truenextnode;
            $n->trueanswernote  = $node->trueanswernote;
            $n->truescore       = $node->truescore;
            $n->truescoremode   = $node->truescoremode;
            $n->falsenextnode   = $node->falsenextnode;
            $n->falseanswernote = $node->falseanswernote;
            $n->falsescore      = $node->falsescore;
            $n->falsescoremode  = $node->falsescoremode;
            $n->answertest      = $this->compile_node_answertest($node);
            $summary[$node->nodename] = $n;
        }
        return $summary;
    }

    /**
     * @return array Languages used in the feedback.
     */
    public function get_feedback_languages() {
        $langs = array();
        $ml = new stack_multilang();
        foreach ($this->nodes as $key => $node) {
            $langs[$key] = [];
            if ($node->truefeedback !== null && $node->truefeedback !== '') {
                $langs[$key][$node->trueanswernote] = $ml->languages_used($node->truefeedback);
            }
            if ($node->falsefeedback !== null && $node->falsefeedback !== '') {
                $langs[$key][$node->falseanswernote] = $ml->languages_used($node->falsefeedback);
            }
        }
        return $langs;
    }

    /**
     * @return array of choices for the show validation select menu.
     */
    public static function get_feedbackstyle_options() {
        return array(
            '0' => get_string('feedbackstyle0', 'qtype_stack'),
            '1' => get_string('feedbackstyle1', 'qtype_stack'),
            '2' => get_string('feedbackstyle2', 'qtype_stack'),
            '3' => get_string('feedbackstyle3', 'qtype_stack'),
        );
    }

    /**
     * This is only for testing, you need to do more to check the actual text.
     *
     * @return string Raw feedback text as a single blob for checking.
     */
    public function get_feedback_test() {
        $text = '';
        foreach ($this->nodes as $node) {
            if ($node->truefeedback !== null) {
                $text .= $node->truefeedback;
            }
            if ($node->falsefeedback !== null) {
                $text .= $node->falsefeedback;
            }
        }
        return $text;
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
    public function compile(array $inputs, array $boundvars, $defaultpenalty, $security, $pathprefix, $map): array {
        $r = ['sig' => '', 'def' => '', 'cv' => null, 'be' => null, 'required' => [], 'units' => false];
        // Note these variables are initialised before the feedback-vars and if not forbidden
        // could be directly set in the vars. The logic does not actually require any PRT-nodes.
        $body = '(%PRT_FEEDBACK:"",' . // The variable holding the feedback CASText2.
                '%PRT_SCORE:0,' .
                '%PRT_PENALTY:0,' .
                '%PRT_PATH:[],' . // The nodes visited and their answertest notes.
                '%PRT_EXIT_NOTE: [],' . // The notes for nodes not the answertests.
                '%_EXITS:{},'; // This tracks the exits from nodes so that we can decide the next node.

        // We build a trace here to help question authors understand and debug questions.
        if ($this->feedbackvariables === null) {
            $this->feedbackvariables = '';
        }

        // Start a fresh trace with each compile.
        $this->trace = array();
        if ($this->feedbackvariables != '') {
            $this->trace[] = $this->feedbackvariables;
            $this->trace[] = '/* ------------------- */';
        }

        $fv = new stack_cas_keyval($this->feedbackvariables);
        $fv->set_security($security);
        $fv->get_valid();
        $fv = $fv->compile($pathprefix . '/fv', $map);
        $r['be'] = $fv['blockexternal'];
        $r['cv'] = $fv['contextvariables'];
        if (isset($fv['includes'])) {
            $r['includes'] = [];
            $r['includes']['keyval'] = $fv['includes'];
        }
        $usage = $fv['references']; // We need to track the usage of vars over the whole thing.
        $usage['write']['%PRT_FEEDBACK'] = true;
        $usage['write']['%PRT_SCORE'] = true;
        $usage['write']['%PRT_PENALTY'] = true;
        $usage['write']['%PRT_PATH'] = true;
        $usage['write']['%PRT_EXIT_NOTE'] = true;
        $usage['write']['%_EXITS'] = true;

        // For the feedback we might want to provide extra information related to
        // feedback vars. Basically, for the debug-block we tell that these are
        // the bound ones.
        $ct2options = ['bound-vars' => $fv['references']['write'], 'static string extractor' => $map];

        if ($fv['statement'] !== null) {
            // The simplification status for feedback vars. If we have any.
            if ($this->simplify) {
                $body .= 'simp:true,';
            } else {
                $body .= 'simp:false,';
            }
            $body .= $fv['statement'] . ',';
        }

        // Let's build the node precedence map, i.e. through which edges are nodes reachable.
        $precedence = [];
        $nodes = $this->get_reverse_post_order_nodes();

        foreach ($nodes as $node) {
            $truenode  = $this->get_node($node->truenextnode);
            $falsenode = $this->get_node($node->falsenextnode);
            if ($truenode !== null) {
                if (!isset($precedence[$truenode->nodename])) {
                    $precedence[$truenode->nodename] = [];
                }
                $precedence[$truenode->nodename][] = '[' .
                    stack_utils::php_string_to_maxima_string($node->nodename) . ',true]';
            }
            if ($falsenode !== null) {
                if (!isset($precedence[$falsenode->nodename])) {
                    $precedence[$falsenode->nodename] = [];
                }
                $precedence[$falsenode->nodename][] = '[' .
                    stack_utils::php_string_to_maxima_string($node->nodename) . ',false]';
            }
        }

        // Then we need to iterate the nodes and generate the matching logic for them all.
        $first = true;
        foreach ($nodes as $node) {
            // The error path needs indexing that differs from execution order.
            $path = $pathprefix . '/n/' . array_search($node, $this->nodes);
            if (strpos($node->answertest, 'Units') === 0) {
                $r['units'] = true;
            }
            if ($first) {
                // The first node is not conditional.
                $first = false;
                $body .= '(';
            } else {
                $body .= 'if not emptyp(intersection(%_EXITS,{' . implode(',', $precedence[$node->nodename]) .'})) then (';
            }
            [$nc, $usage, $ctincludes] = $this->compile_node($node, $usage, $defaultpenalty, $security, $path, $ct2options);
            if (count($ctincludes) > 0) {
                if (!isset($r['includes'])) {
                    $r['includes'] = ['castext' => $ctincludes];
                } else if (!isset($r['includes']['castext'])) {
                    $r['includes']['castext'] = $ctincludes;
                } else {
                    foreach ($ctincludes as $url) {
                        if (array_search($url, $r['includes']['castext']) === false) {
                            $r['includes']['castext'][] = $url;
                        }
                    }
                }
            }
            $body .= $nc . '),';
        }

        // Finally round the score and return the relevant details.
        $body .= '%PRT_SCORE:ev(float(round(max(min(%PRT_SCORE,1.0),0.0)*1000)/1000),simp),';
        $body .= '%PRT_PENALTY:ev(float(round(max(min(%PRT_PENALTY,1.0),0.0)*1000)/1000),simp),';
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
                $r['required'][$key] = true;
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
            $r['GCL-warn'] = true;
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
        $r['sig'] = 'prt_' . $this->name . '(' . implode(',', array_keys($asarg)) . ')';

        $this->trace[] = '/* ------------------- */';
        $this->trace[] = $r['sig'] . ';';
        $r['trace'] = $this->trace;

        $r['def'] = $r['sig'] . ':=block([' . implode(',', array_keys($aslocal)) . '],' . $body . ')';
        return $r;
    }

    /*
     * Generate the complete maxima command for a single answertest in a specific node.
     */
    private function compile_node_answertest($node) {
        // TODO: make this saner, the way Stateful lets the tests do their own
        // call construction might duplicate things but it does not require this
        // much knowledge about the shape of things.
        // We have no validation for these requirements.
        // TODO: choose whether we error catch sans/tans/options separately or
        // at the whole test level. Now at test level.
        $at = 'AT' . $node->answertest . '(' . $node->sans;

        if ($node->tans === null || trim($node->tans) === '') {
            $at .= ',""';
        } else {
            $at .= ',' . $node->tans;
        }

        if (stack_ans_test_controller::required_atoptions($node->answertest) === true ||
                (stack_ans_test_controller::required_atoptions($node->answertest) === 'optional' &&
                trim($node->testoptions) !== '')) {
            // Simplify these. Mainly the sigfigs as the test has a history of not doing it.
            $at .= ',ev(' . $node->testoptions . ',simp)';
        }

        if (stack_ans_test_controller::required_raw($node->answertest)) {
            // The sans better be just a raw input ref.
            // If not then just use the expression.
            $at .= ',stackmap_get_ifexists(_INPUT_STRING,' .
            stack_utils::php_string_to_maxima_string(trim($node->sans)) . ')';
        }
        $at .= ')';

        return $at;
    }

    private function compile_node($node, $usage, $defaultpenalty, $security, $path, $ct2options): array {
        /* In the old system there is a hack that covers some options let's repeat that here.
         * For some tests there is an option assume_pos. This will be evaluated by maxima (since this is also the name
         * of a maxima variable).  So, we need to protect the name from being evaluated.
         */
        $op = $node->testoptions;
        $reps = array('assume_pos' => 'assumepos', 'assume_real' => 'assumereal');
        foreach ($reps as $key => $val) {
            $op = str_replace($key, $val, $op);
        }
        $node->testoptions = $op;

        // Track inclusions inside CASText.
        $ctincludes = [];

        // Start by turning simplification off for the call of the answer-test.
        // Or on...
        // Really all tests should be so that one calls them without simplification
        // of anything and if they need to simplify inside the test then the test
        // does it and if the author wants to simplify outside the test they do so.
        $body = 'simp:false,';
        if (stack_ans_test_controller::simp($node->answertest)) {
            $body = 'simp:true,';
        }

        $at = $this->compile_node_answertest($node);
        $this->trace[] = $at . ';';
        $at = '%_TMP:' . $at;

        // Do a parse at this point to normalise the statement. And to collect usages.
        $context = $path;
        $cs = stack_ast_container::make_from_teacher_source($at, $context . '/at');
        $cs->set_securitymodel($security);
        if (!$cs->get_valid()) {
            throw new stack_exception('Error in ' . $context . ' answertest parameters.');
        }
        $usage = $cs->get_variable_usage($usage); // Update the references.

        // Add the error catching wrapping.
        $body .= '_EC(errcatch(' . $cs->get_evaluationform() . '),' .
            stack_utils::php_string_to_maxima_string($context . '/at') . '),';

        // Now based on the results we update things. For the updates we need simp:true.
        $body .= 'simp:true,'; // Hold until score math done.
        $body .= '%PRT_PATH:append(%PRT_PATH,[%_TMP]),'; // Add the raw result to the path.
        $body .= '%_EXITS:union(%_EXITS, {['. stack_utils::php_string_to_maxima_string($node->nodename) .
            ',%_TMP[2]]}),'; // Which exit we took.

        if ($node->quiet == 0) {
            // We need to connect any possible test feedback to the feedback.
            // Note that as long as the CAS side tests rely on the old translation
            // logic we need to wrap them to the special `%strans` block.
            $body .= 'if not(atom(%_TMP)) and length(%_TMP) > 3 and slength(%_TMP[4]) > 0 then ' .
                '%PRT_FEEDBACK:castext_concat(%PRT_FEEDBACK,["%strans",%_TMP[4]]),';
        }

        // Those were the branch neutral parts, now the branches.
        $body .= 'if %_TMP[2] then (';
        $body .= '%PRT_EXIT_NOTE:append(%PRT_EXIT_NOTE, [' .
            stack_utils::php_string_to_maxima_string($node->trueanswernote) . '])';
        // The true branch.
        // Even if the branch is formative we do calculate score for analysis.
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
            $s = stack_ast_container::make_from_teacher_source($s, $context . '/st');
            $s->set_securitymodel($security);
            if (!$s->get_valid()) {
                throw new stack_exception('Error in ' . $context . ' true-score.');
            }
            $s = '_EC(errcatch(%_TMP:' . $s->get_evaluationform() . '),' .
                stack_utils::php_string_to_maxima_string($context . '/st') . ')';
        } else {
            $s = '%_TMP:' . $s;
        }
        if (!is_numeric($p)) {
            $p = stack_ast_container::make_from_teacher_source($p, $context . '/pt');
            $p->set_securitymodel($security);
            if (!$p->get_valid()) {
                throw new stack_exception('Error in ' . $context . ' true-penalty.');
            }
            $p = '_EC(errcatch(%PRT_PENALTY:' . $p->get_evaluationform() . '),' .
                stack_utils::php_string_to_maxima_string($context . '/pt') . ')';
        } else {
            $p = '%PRT_PENALTY:' . $p;
        }
        // Now the score mode based logic, I wonder why both score and penalty use the same.
        // TODO: trace the original logic and check how these are tied to each other.
        switch ($node->truescoremode) {
            case '+':
                $body .= ',' . $s . ',%PRT_SCORE:%PRT_SCORE+%_TMP,' . $p;
                break;
            case '-':
                $body .= ',' . $s . ',%PRT_SCORE:%PRT_SCORE-%_TMP,' . $p;
                break;
            case '*':
                $body .= ',' . $s . ',%PRT_SCORE:%PRT_SCORE*%_TMP,' . $p;
                break;
            default: // Which is '='.
                $body .= ',' . $s . ',%PRT_SCORE:%_TMP,' . $p;
                break;
        }

        if ($node->truefeedback !== null && trim($node->truefeedback) !== '') {
            // Note the space separates any feedback from that generated by the prt node.
            $feedback = ' ' . stack_castext_file_filter($node->truefeedback,
                ['field' => 'prtnodetruefeedback',
                 'prtnodeid' => $node->id,
                 'prtid' => $this->id, // For completeness sake.
                 'questionid' =>
                    $this->question !== null && property_exists($this->question, 'id') ? $this->question->id : null
                ]);
            if (substr($body, -1) !== '(') {
                // Depends on whether the score math was done.
                $body .= ',';
            }
            // The feedback will be rendered using the simplify setting of the PRT.
            if ($this->simplify) {
                $body .= 'simp:true,';
            } else {
                $body .= 'simp:false,';
            }
            $ct = castext2_evaluatable::make_from_source($feedback, $context . '/ft');
            if (!$ct->get_valid($node->truefeedbackformat, $ct2options, $security)) {
                throw new stack_exception('Error in ' . $context . ' true-feedback.');
            }
            if (isset($ct->get_special_content()['castext-includes'])) {
                foreach ($ct->get_special_content()['castext-includes'] as $url) {
                    if (array_search($url, $ctincludes) === false) {
                        $ctincludes[] = $url;
                    }
                }
            }
            $cts = $ct->get_evaluationform();
            // If it is a pure static string it is too simple for latter processing to detect static content.
            // So we will add some wrapping to make it obvious.
            if (mb_substr($cts, 0, 1) === '"') {
                $cts = '["%root",' . $cts . ']';
            }

            $cs = stack_ast_container::make_from_teacher_source($cts, $context . '/ft');
            $usage = $cs->get_variable_usage($usage); // Update the references.
            $body .= '_EC(errcatch(%PRT_FEEDBACK:castext_concat(%PRT_FEEDBACK,' . $cts . ')),' .
                stack_utils::php_string_to_maxima_string($context . '/ft') . ')';
        }

        $body .= ') else (';
        $body .= '%PRT_EXIT_NOTE:append(%PRT_EXIT_NOTE, [' .
            stack_utils::php_string_to_maxima_string($node->falseanswernote) . '])';
        // The false branch.
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
            $s = stack_ast_container::make_from_teacher_source($s, $context . '/sf');
            $s->set_securitymodel($security);
            if (!$s->get_valid()) {
                throw new stack_exception('Error in ' . $context . ' false-score.');
            }
            $s = '_EC(errcatch(%_TMP:' . $s->get_evaluationform() . '),' .
                stack_utils::php_string_to_maxima_string($context . '/sf') . ')';
        } else {
            $s = '%_TMP:' . $s;
        }
        if (!is_numeric($p)) {
            $p = stack_ast_container::make_from_teacher_source($p, $context . '/pf');
            $p->set_securitymodel($security);
            if (!$p->get_valid()) {
                throw new stack_exception('Error in ' . $context . ' false-penalty.');
            }
            $p = '_EC(errcatch(%PRT_PENALTY:' . $p->get_evaluationform() . '),' .
                stack_utils::php_string_to_maxima_string($context . '/pf') . ')';
        } else {
            $p = '%PRT_PENALTY:' . $p;
        }
        // Now the score mode based logic, I wonder why both score and penalty use the same.
        // TODO: trace the original logic and check how these are tied to each other.
        switch ($node->falsescoremode) {
            case '+':
                $body .= ',' . $s . ',%PRT_SCORE:%PRT_SCORE+%_TMP,' . $p;
                break;
            case '-':
                $body .= ',' . $s . ',%PRT_SCORE:%PRT_SCORE-%_TMP,' . $p;
                break;
            case '*':
                $body .= ',' . $s . ',%PRT_SCORE:%PRT_SCORE*%_TMP,' . $p;
                break;
            default: // Which is '='.
                $body .= ',' . $s . ',%PRT_SCORE:%_TMP,' . $p;
                break;
        }

        if ($node->falsefeedback !== null && trim($node->falsefeedback) !== '') {
            // Note the space separates any feedback from that generated by the prt node.
            $feedback = ' ' . stack_castext_file_filter($node->falsefeedback,
                ['field' => 'prtnodefalsefeedback',
                 'prtnodeid' => $node->id,
                 'prtid' => $this->id, // For completeness sake.
                 'questionid' => $this->question !==
                    null && property_exists($this->question, 'id') ? $this->question->id : null
                ]);
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
            $ct = castext2_evaluatable::make_from_source($feedback, $context . '/ff');
            if (!$ct->get_valid($node->falsefeedbackformat, $ct2options, $security)) {
                throw new stack_exception('Error in ' . $context . ' false-feedback.');
            }
            if (isset($ct->get_special_content()['castext-includes'])) {
                foreach ($ct->get_special_content()['castext-includes'] as $url) {
                    if (array_search($url, $ctincludes) === false) {
                        $ctincludes[] = $url;
                    }
                }
            }

            $cts = $ct->get_evaluationform();
            // If it is a pure static string it is too simple for latter processing to detect static content.
            // So we will add some wrapping to make it obvious.
            if (mb_substr($cts, 0, 1) === '"') {
                $cts = '["%root",' . $cts . ']';
            }

            $cs = stack_ast_container::make_from_teacher_source($cts, $context . '/ff');
            $usage = $cs->get_variable_usage($usage); // Update the references.
            $body .= '_EC(errcatch(%PRT_FEEDBACK:castext_concat(%PRT_FEEDBACK,' . $cts . ')),' .
                stack_utils::php_string_to_maxima_string($context . '/ff') . ')';
        }

        $body .= ')';
        return [$body, $usage, $ctincludes];
    }

    /*
     * @param array $labels an array of labels for the branches.
     */
    public function get_prt_graph($labels = false) {
        $graph = new stack_abstract_graph();
        foreach ($this->nodes as $key => $node) {

            if ($node->truenextnode == -1) {
                $left = null;
            } else {
                $left = $node->truenextnode + 1;
            }
            if ($node->falsenextnode == -1) {
                $right = null;
            } else {
                $right = $node->falsenextnode + 1;
            }
            $llabel = $node->truescoremode . round($node->truescore, 2);
            if ($labels && array_key_exists($node->trueanswernote, $labels)) {
                $llabel = $labels[$node->trueanswernote];
            }
            $rlabel = $node->falsescoremode . round($node->falsescore, 2);
            if ($labels && array_key_exists($node->falseanswernote, $labels)) {
                $rlabel = $labels[$node->falseanswernote];
            }

            $graph->add_node($key + 1, $left, $right, $llabel, $rlabel,
                '#fgroup_id_' . $this->name . 'node_' . $key);
        }

        $graph->layout();
        return $graph;
    }

    /*
     * Returns the trace of the PRT.
     */
    public function get_trace() {
        return $this->trace;
    }

}

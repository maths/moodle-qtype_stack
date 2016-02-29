<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Defines the editing form for the Stack question type.
 *
 * @package    qtype_stack
 * @copyright  2012 The Open University.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/type/edit_question_form.php');
require_once($CFG->dirroot . '/question/type/stack/question.php');
require_once($CFG->dirroot . '/question/type/stack/stack/input/factory.class.php');
require_once($CFG->dirroot . '/question/type/stack/stack/answertest/controller.class.php');

require_once($CFG->dirroot . '/question/type/stack/stack/cas/keyval.class.php');
require_once($CFG->dirroot . '/question/type/stack/stack/cas/castext.class.php');
require_once($CFG->dirroot . '/question/type/stack/stack/graphlayout/graph.php');


/**
 * Stack question editing form definition.
 *
 * @copyright  2012 The Open University.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_stack_edit_form extends question_edit_form {
    /** @var string the default question text for a new question. */
    const DEFAULT_QUESTION_TEXT = '<p></p><p>[[input:ans1]] [[validation:ans1]]</p>';
    /** @var string the default specific feedback for a new question. */
    const DEFAULT_SPECIFIC_FEEDBACK = '[[feedback:prt1]]';

    /** @var int array key into the results of get_input_names_from_question_text for the count of input placeholders. */
    const INPUTS = 0;
    /** @var int array key into the results of get_input_names_from_question_text for the count of validation placeholders. */
    const VALIDATIONS = 1;

    /** @var options the STACK configuration settings. */
    protected $stackconfig = null;

    /** @var string caches the result of {@link get_current_question_text()}. */
    protected $questiontext = null;

    /** @var string caches the result of {@link get_current_specific_feedback()}. */
    protected $specificfeedback = null;

    /**
     * @var array prt name => stack_abstract_graph caches the result of
     * {@link get_prt_graph()}.
     */
    protected $prtgraph = array();

    /** @var array the set of choices used for the type of all inputs. */
    protected $typechoices;

    /** @var array the set of choices used for the type of all answer tests. */
    protected $answertestchoices;

    /** @var array the set of choices used for the score mode of all PRT branches. */
    protected $scoremodechoices;

    /** @var int the CAS seed using during validation. */
    protected $seed = 1;

    /** @var stack_options the CAS options using during validation. */
    protected $options;


    /** Patch up data from the database before a user edits it in the form. */
    public function set_data($question) {
        if (!empty($question->questiontext)) {
            $question->questiontext = $this->convert_legacy_fact_sheets($question->questiontext);
        }
        if (!empty($question->generalfeedback)) {
            $question->generalfeedback = $this->convert_legacy_fact_sheets($question->generalfeedback);
        }
        if (!empty($question->specificfeedback)) {
            $question->specificfeedback = $this->convert_legacy_fact_sheets($question->specificfeedback);
        }

        if (!empty($question->prts)) {
            foreach ($question->prts as $prtname => $prt) {
                if (!empty($prt->nodes)) {
                    foreach ($prt->nodes as $nodename => $node) {
                        $node->truefeedback  = $this->convert_legacy_fact_sheets($node->truefeedback);
                        $node->falsefeedback = $this->convert_legacy_fact_sheets($node->falsefeedback);
                    }
                }
            }
        }

        parent::set_data($question);
    }

    /**
     * Replace any <hint> delimiters in the given text from the
     * form with the recommended delimiters.
     * @param string $text input to convert.
     */
    protected function convert_legacy_fact_sheets($text) {
        return stack_fact_sheets::convert_legacy_tags($text);
    }

    /**
     * @return string the current value of the question text, given the state the form is in.
     */
    protected function get_current_question_text() {
        if (!is_null($this->questiontext)) {
            return $this->questiontext;
        }

        $submitted = optional_param_array('questiontext', array(), PARAM_RAW);

        if (array_key_exists('text', $submitted)) {
            $this->questiontext = $submitted['text'];
        } else if (!empty($this->question->questiontext)) {
            $this->questiontext = $this->question->questiontext;
        } else {
            $this->questiontext = self::DEFAULT_QUESTION_TEXT;
        }

        return $this->questiontext;
    }

    /**
     * @return string the current value of the specific feedback, given the state the form is in.
     */
    protected function get_current_specific_feedback() {
        if (!is_null($this->specificfeedback)) {
            return $this->specificfeedback;
        }

        $submitted = optional_param_array('specificfeedback', array(), PARAM_RAW);
        if (array_key_exists('text', $submitted)) {
            $this->specificfeedback = $submitted['text'];
        } else if (isset($this->question->options->specificfeedback)) {
            $this->specificfeedback = $this->question->options->specificfeedback;
        } else {
            $this->specificfeedback = self::DEFAULT_SPECIFIC_FEEDBACK;
        }

        return $this->specificfeedback;
    }

    /**
     * Get a list of the PRT notes that should be present for a given PRT.
     * @param string $prtname the name of a PRT.
     * @return array list of nodes that should be present in the form definitino for this PRT.
     */
    protected function get_prt_graph($prtname) {
        if (array_key_exists($prtname, $this->prtgraph)) {
            return $this->prtgraph[$prtname];
        }

        // If the form has been submitted and is being redisplayed, and this is
        // an existing PRT, base things on the submitted data.
        $submitted = optional_param_array($prtname . 'truenextnode', null, PARAM_RAW);
        if ($submitted) {
            $truescoremode  = optional_param_array($prtname . 'truescoremode',  null, PARAM_RAW);
            $truescore      = optional_param_array($prtname . 'truescore',      null, PARAM_RAW);
            $falsenextnode  = optional_param_array($prtname . 'falsenextnode',  null, PARAM_RAW);
            $falsescoremode = optional_param_array($prtname . 'falsescoremode', null, PARAM_RAW);
            $falsescore     = optional_param_array($prtname . 'falsescore',     null, PARAM_RAW);
            $graph = new stack_abstract_graph();

            $deletednode = null;
            $lastkey = -1;
            foreach ($submitted as $key => $truenextnode) {
                if (optional_param($prtname . 'nodedelete' . $key, false, PARAM_BOOL)) {

                    // Slightly odd to register the button here, especially since
                    // now this node has been deleted, this button will not exist,
                    // but anyway this works, and in necessary to stop the form
                    // from being submitted.
                    $this->_form->registerNoSubmitButton($prtname . 'nodedelete' . $key);

                    // For deleted nodes, we add them to the tree anyway, and
                    // then remove them again below. We have to do it that way
                    // because we also need to delete links that point to the
                    // deleted node.
                    $deletednode = $key;
                }

                if ($truenextnode == -1 || !array_key_exists($truenextnode, $submitted)) {
                    $left = null;
                } else {
                    $left = $truenextnode + 1;
                }
                if ($falsenextnode[$key] == -1 || !array_key_exists($falsenextnode[$key], $submitted)) {
                    $right = null;
                } else {
                    $right = $falsenextnode[$key] + 1;
                }
                $graph->add_node($key + 1, $left, $right,
                        $truescoremode[$key] . round($truescore[$key], 2),
                        $falsescoremode[$key] . round($falsescore[$key], 2),
                        '#fgroup_id_' . $prtname . 'node_' . $key);

                $lastkey = max($lastkey, $key);
            }

            if (optional_param($prtname . 'nodeadd', false, PARAM_BOOL)) {
                $graph->add_node($lastkey + 2, null, null, '+0', '-0',
                        '#fgroup_id_' . $prtname . 'node_' . $lastkey + 1);
            }

            if (!is_null($deletednode)) {
                $graph->remove_node($deletednode + 1);
            }

            $graph->layout();
            $this->prtgraph[$prtname] = $graph;
            return $graph;
        }

        // Otherwise, if an existing question is being edited, and this is an
        // existing PRT, base things on the existing question definition.
        if (!empty($this->question->prts[$prtname]->nodes)) {
            $graph = new stack_abstract_graph();
            foreach ($this->question->prts[$prtname]->nodes as $node) {
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
                $graph->add_node($node->nodename + 1, $left, $right,
                        $node->truescoremode . round($node->truescore, 2),
                        $node->falsescoremode . round($node->falsescore, 2),
                        '#fgroup_id_' . $prtname . 'node_' . $node->nodename);
            }
            $graph->layout();
            $this->prtgraph[$prtname] = $graph;
            return $graph;
        }

        // Otherwise, it is a new PRT. Just one node.
        $graph = new stack_abstract_graph();
        $graph->add_node('1', null, null, '=1', '=0', '#fgroup_id_' . $prtname . 'node_0');
        $graph->layout();
        $this->prtgraph[$prtname] = $graph;
        return $graph;
    }

    /**
     * @return array of the input names that currently appear in the question text.
     */
    protected function get_input_names_from_question_text() {
        $questiontext = $this->get_current_question_text();

        $inputs = stack_utils::extract_placeholders($questiontext, 'input');
        $validations = stack_utils::extract_placeholders($questiontext, 'validation');
        $inputnames = array();

        $data = data_submitted();
        if ($data) {
            foreach (get_object_vars($data) as $name => $value) {
                if (preg_match('~(' . stack_utils::VALID_NAME_REGEX . ')modelans~', $name, $matches)) {
                    $inputnames[$matches[1]] = array(0, 0);
                }
            }
        }

        foreach ($inputs as $inputname) {
            if (!array_key_exists($inputname, $inputnames)) {
                $inputnames[$inputname] = array(0, 0);
            }
            $inputnames[$inputname][self::INPUTS] += 1;
        }

        foreach ($validations as $inputname) {
            if (!array_key_exists($inputname, $inputnames)) {
                $inputnames[$inputname] = array(0, 0);
            }
            $inputnames[$inputname][self::VALIDATIONS] += 1;
        }

        return $inputnames;
    }

    /**
     * @return array of the PRT names that currently appear in the question
     *      text and specific feedback.
     */
    protected function get_prt_names_from_question() {
        $questiontext = $this->get_current_question_text();
        $specificfeedback = $this->get_current_specific_feedback();
        $prts = stack_utils::extract_placeholders($questiontext . $specificfeedback, 'feedback');
        $prtnames = array();

        $data = data_submitted();
        if ($data) {
            foreach (get_object_vars($data) as $name => $value) {
                if (preg_match('~(' . stack_utils::VALID_NAME_REGEX . ')feedbackvariables~', $name, $matches)) {
                    $prtnames[$matches[1]] = 0;
                }
            }
        }

        foreach ($prts as $name) {
            if (!array_key_exists($name, $prtnames)) {
                $prtnames[$name] = 0;
            }
            $prtnames[$name] += 1;
        }
        return $prtnames;
    }

    /**
     * Tags which have extra whitespace within them. E.g. [[input: ans1]] are forbidden.
     * @return array of tags.
     */
    protected function get_sloppy_tags($text) {

        $sloppytags = stack_utils::extract_placeholders_sloppy($text, 'input');
        $sloppytags = array_merge(stack_utils::extract_placeholders_sloppy($text, 'validation'), $sloppytags);
        $sloppytags = array_merge(stack_utils::extract_placeholders_sloppy($text, 'prt'), $sloppytags);

        return $sloppytags;
    }

    /**
     * Helper method to get the list of inputs required by a PRT, given the current
     * state of the form.
     * @param string $prtname the name of a PRT.
     * @return array list of inputs used by this PRT.
     */
    protected function get_inputs_used_by_prt($prtname) {
        // Needed for questions with no inputs, (in particular blank starting questions).
        if (!property_exists($this->question, 'inputs')) {
            return array();
        }
        if (is_null($this->question->inputs)) {
            return array();
        }
        $inputs = $this->question->inputs;
        $inputkeys = array();
        if (is_array($inputs)) {
            foreach ($inputs as $input) {
                $inputkeys[] = $input->name;
            }
        } else {
            return array();
        }

        // TODO fix this. At the moment it only considers the data from the unedited
        // question. We should take into account any changes made since the
        // form was first shown, for example adding or removing nodes, or changing
        // the things they compare. However, it is not critical.

        // If we are creating a new question, or if we add a new prt in the
        // question stem, then the PRT will not yet exist, so return an empty array.
        if (is_null($this->question->prts) || !array_key_exists($prtname, $this->question->prts)) {
            return array();
        }
        $prt = $this->question->prts[$prtname];

        $prtnodes = array();
        foreach ($prt->nodes as $name => $node) {
            $sans = new stack_cas_casstring($node->sans);
            $tans = new stack_cas_casstring($node->tans);
            $prtnode = new stack_potentialresponse_node($sans, $tans, $node->answertest, $node->testoptions);
            $prtnode->add_branch(1, '+', 0, '', -1, $node->truefeedback, $node->truefeedbackformat, '');
            $prtnode->add_branch(0, '+', 0, '', -1, $node->falsefeedback, $node->falsefeedbackformat, '');
            $prtnodes[$name] = $prtnode;
        }
        $feedbackvariables = new stack_cas_keyval($prt->feedbackvariables, null, 0, 't');
        $potentialresponsetree = new stack_potentialresponse_tree(
                '', '', false, 0, $feedbackvariables->get_session(), $prtnodes, $prt->firstnodename);
        return $potentialresponsetree->get_required_variables($inputkeys);
    }

    protected function definition() {
        parent::definition();
        $mform = $this->_form;

        $fixdollars = $mform->createElement('checkbox', 'fixdollars',
                stack_string('fixdollars'), stack_string('fixdollarslabel'));
        $mform->insertElementBefore($fixdollars, 'buttonar');
        $mform->addHelpButton('fixdollars', 'fixdollars', 'qtype_stack');
        $mform->closeHeaderBefore('fixdollars');

        // There is no un-closeHeaderBefore, so fake it.
        $closebeforebuttonarr = array_search('buttonar', $mform->defaultRenderer()->_stopFieldsetElements);
        if ($closebeforebuttonarr !== false) {
            unset($mform->defaultRenderer()->_stopFieldsetElements[$closebeforebuttonarr]);
        }
    }

    protected function definition_inner(/* MoodleQuickForm */ $mform) {

        // Load the configuration.
        $this->stackconfig = stack_utils::get_config();

        // Prepare input types.
        $this->typechoices = stack_input_factory::get_available_type_choices();

        // Prepare answer test types.
        $answertests = stack_ans_test_controller::get_available_ans_tests();
        // Algebraic Equivalence should be the default test, and first on the list.
        // This does not come first in the alphabet of all languages.
        $default    = 'AlgEquiv';
        $defaultstr = stack_string($answertests[$default]);
        unset($answertests[$default]);

        $this->answertestchoices = array();
        foreach ($answertests as $test => $string) {
            $this->answertestchoices[$test] = stack_string($string);
        }
        stack_utils::sort_array($this->answertestchoices);
        $this->answertestchoices = array_merge(array($default => $defaultstr),
                $this->answertestchoices);

        // Prepare score mode choices.
        $this->scoremodechoices = array(
                    '=' => '=',
                    '+' => '+',
                    '-' => '-',
        );

        $inputnames = $this->get_input_names_from_question_text();
        $prtnames = $this->get_prt_names_from_question();

        // Note that for the editor elements, we are using $mform->getElement('prtincorrect')->setValue(...); instead
        // of setDefault, because setDefault does not work for editors.

        $mform->addHelpButton('questiontext', 'questiontext', 'qtype_stack');
        $mform->addRule('questiontext', stack_string('questiontextnonempty'), 'required', '', 'client');

        $qvars = $mform->createElement('textarea', 'questionvariables',
                stack_string('questionvariables'), array('rows' => 5, 'cols' => 80));
        $mform->insertElementBefore($qvars, 'questiontext');
        $mform->addHelpButton('questionvariables', 'questionvariables', 'qtype_stack');

        $seed = $mform->createElement('text', 'variantsselectionseed',
                stack_string('variantsselectionseed'), array('size' => 50));
        $mform->insertElementBefore($seed, 'questiontext');
        $mform->setType('variantsselectionseed', PARAM_RAW);
        $mform->addHelpButton('variantsselectionseed', 'variantsselectionseed', 'qtype_stack');

        $sf = $mform->createElement('editor', 'specificfeedback',
                get_string('specificfeedback', 'question'), array('rows' => 10), $this->editoroptions);
        $mform->insertElementBefore($sf, 'generalfeedback');

        $mform->getElement('specificfeedback')->setValue(array('text' => self::DEFAULT_SPECIFIC_FEEDBACK));
        $mform->addHelpButton('specificfeedback', 'specificfeedback', 'qtype_stack');

        $mform->addHelpButton('generalfeedback', 'generalfeedback', 'qtype_stack');

        $mform->addElement('textarea', 'questionnote',
                stack_string('questionnote'), array('rows' => 2, 'cols' => 80));
        $mform->addHelpButton('questionnote', 'questionnote', 'qtype_stack');

        $mform->addElement('submit', 'verify', stack_string('verifyquestionandupdate'));
        $mform->registerNoSubmitButton('verify');

        // Inputs.
        foreach ($inputnames as $inputname => $counts) {
            $this->definition_input($inputname, $mform, $counts);
        }

        // PRTs.
        foreach ($prtnames as $prtname => $count) {
            $this->definition_prt($prtname, $mform, $count);
        }

        // Options.
        $mform->addElement('header', 'optionsheader', stack_string('options'));

        $mform->addElement('selectyesno', 'questionsimplify',
                stack_string('questionsimplify'));
        $mform->setDefault('questionsimplify', $this->stackconfig->questionsimplify);
        $mform->addHelpButton('questionsimplify', 'autosimplify', 'qtype_stack');

        $mform->addElement('selectyesno', 'assumepositive',
                stack_string('assumepositive'));
        $mform->setDefault('assumepositive', $this->stackconfig->assumepositive);
        $mform->addHelpButton('assumepositive', 'assumepositive', 'qtype_stack');

        $mform->addElement('editor', 'prtcorrect',
                stack_string('prtcorrectfeedback'),
                array('rows' => 1), $this->editoroptions);
        $mform->getElement('prtcorrect')->setValue(array(
                'text' => $this->stackconfig->prtcorrect));

        $mform->addElement('editor', 'prtpartiallycorrect',
                stack_string('prtpartiallycorrectfeedback'),
                array('rows' => 1), $this->editoroptions);
        $mform->getElement('prtpartiallycorrect')->setValue(array(
                        'text' => $this->stackconfig->prtpartiallycorrect));

        $mform->addElement('editor', 'prtincorrect',
                stack_string('prtincorrectfeedback'),
                array('rows' => 1), $this->editoroptions);
        $mform->getElement('prtincorrect')->setValue(array(
                        'text' => $this->stackconfig->prtincorrect));

        $mform->addElement('select', 'multiplicationsign',
                stack_string('multiplicationsign'), stack_options::get_multiplication_sign_options());
        $mform->setDefault('multiplicationsign', $this->stackconfig->multiplicationsign);
        $mform->addHelpButton('multiplicationsign', 'multiplicationsign', 'qtype_stack');

        $mform->addElement('selectyesno', 'sqrtsign',
                stack_string('sqrtsign'));
        $mform->setDefault('sqrtsign', $this->stackconfig->sqrtsign);
        $mform->addHelpButton('sqrtsign', 'sqrtsign', 'qtype_stack');

        $mform->addElement('select', 'complexno',
                stack_string('complexno'), stack_options::get_complex_no_options());
        $mform->setDefault('complexno', $this->stackconfig->complexno);
        $mform->addHelpButton('complexno', 'complexno', 'qtype_stack');

        $mform->addElement('select', 'inversetrig',
                stack_string('inversetrig'), stack_options::get_inverse_trig_options());
        $mform->setDefault('inversetrig', $this->stackconfig->inversetrig);
        $mform->addHelpButton('inversetrig', 'inversetrig', 'qtype_stack');

        $mform->addElement('select', 'matrixparens',
                stack_string('matrixparens'), stack_options::get_matrix_parens_options());
        $mform->setDefault('matrixparens', $this->stackconfig->matrixparens);
        $mform->addHelpButton('matrixparens', 'matrixparens', 'qtype_stack');

        // Hints.
        $this->add_interactive_settings();

        // Replace standard penalty input at the bottom with the one we want.
        $mform->removeElement('multitriesheader');
        $mform->removeElement('penalty');

        $pen = $mform->createElement('text', 'penalty', stack_string('penalty'), array('size' => 5));
        $mform->insertElementBefore($pen, 'generalfeedback');
        $mform->setType('penalty', PARAM_FLOAT);
        $mform->addHelpButton('penalty', 'penalty', 'qtype_stack');
        $mform->setDefault('penalty', 0.1000000);
        $mform->addRule('penalty', null, 'required', null, 'client');
    }

    /**
     * Add the form fields for a given input element to the form.
     * @param string $inputname the input name.
     * @param MoodleQuickForm $mform the form being assembled.
     * @param int $counts the number of times this input and its validation appears in the questiontext.
     */
    protected function definition_input($inputname, MoodleQuickForm $mform, $counts) {

        $mform->addElement('header', $inputname . 'header', stack_string('inputheading', $inputname));

        if ($counts[self::INPUTS] == 0 && $counts[self::VALIDATIONS] == 0) {
            $mform->addElement('static', $inputname . 'warning', '', stack_string('inputwillberemoved', $inputname));
            $mform->addElement('advcheckbox', $inputname . 'deleteconfirm', '', stack_string('inputremovedconfirm'));
            $mform->setDefault($inputname . 'deleteconfirm', 0);
            $mform->setExpanded($inputname . 'header');
        }

        $mform->addElement('select', $inputname . 'type', stack_string('inputtype'), $this->typechoices);
        $mform->setDefault($inputname . 'type', $this->stackconfig->inputtype);
        $mform->addHelpButton($inputname . 'type', 'inputtype', 'qtype_stack');

        $mform->addElement('text', $inputname . 'modelans', stack_string('teachersanswer'), array('size' => 20));
        $mform->setType($inputname . 'modelans', PARAM_RAW);
        $mform->addHelpButton($inputname . 'modelans', 'teachersanswer', 'qtype_stack');
        // We don't make modelans a required field in the formslib sense, because
        // That stops the input sections collapsing by default. Instead, we enforce
        // that it is non-blank in the server-side validation.

        $mform->addElement('text', $inputname . 'boxsize', stack_string('boxsize'), array('size' => 3));
        $mform->setDefault($inputname . 'boxsize', $this->stackconfig->inputboxsize);
        $mform->setType($inputname . 'boxsize', PARAM_INT);
        $mform->addHelpButton($inputname . 'boxsize', 'boxsize', 'qtype_stack');

        $mform->addElement('selectyesno', $inputname . 'strictsyntax',
                stack_string('strictsyntax'));
        $mform->setDefault($inputname . 'strictsyntax', $this->stackconfig->inputstrictsyntax);
        $mform->addHelpButton($inputname . 'strictsyntax', 'strictsyntax', 'qtype_stack');

        $mform->addElement('select', $inputname . 'insertstars',
                stack_string('insertstars'), stack_options::get_insert_star_options());
        $mform->setDefault($inputname . 'insertstars', $this->stackconfig->inputinsertstars);
        $mform->addHelpButton($inputname . 'insertstars', 'insertstars', 'qtype_stack');

        $mform->addElement('text', $inputname . 'syntaxhint', stack_string('syntaxhint'), array('size' => 20));
        $mform->setType($inputname . 'syntaxhint', PARAM_RAW);
        $mform->addHelpButton($inputname . 'syntaxhint', 'syntaxhint', 'qtype_stack');

        $mform->addElement('text', $inputname . 'forbidwords', stack_string('forbidwords'), array('size' => 20));
        $mform->setType($inputname . 'forbidwords', PARAM_RAW);
        $mform->setDefault($inputname . 'forbidwords', $this->stackconfig->inputforbidwords);
        $mform->addHelpButton($inputname . 'forbidwords', 'forbidwords', 'qtype_stack');

        $mform->addElement('text', $inputname . 'allowwords', stack_string('allowwords'), array('size' => 20));
        $mform->setType($inputname . 'allowwords', PARAM_RAW);
        $mform->setDefault($inputname . 'allowwords', '');
        $mform->addHelpButton($inputname . 'allowwords', 'allowwords', 'qtype_stack');

        $mform->addElement('selectyesno', $inputname . 'forbidfloat',
                stack_string('forbidfloat'));
        $mform->setDefault($inputname . 'forbidfloat', $this->stackconfig->inputforbidfloat);
        $mform->addHelpButton($inputname . 'forbidfloat', 'forbidfloat', 'qtype_stack');

        $mform->addElement('selectyesno', $inputname . 'requirelowestterms',
                stack_string('requirelowestterms'));
        $mform->setDefault($inputname . 'requirelowestterms', $this->stackconfig->inputrequirelowestterms);
        $mform->addHelpButton($inputname . 'requirelowestterms', 'requirelowestterms', 'qtype_stack');

        $mform->addElement('selectyesno', $inputname . 'checkanswertype',
                stack_string('checkanswertype'));
        $mform->setDefault($inputname . 'checkanswertype', $this->stackconfig->inputcheckanswertype);
        $mform->addHelpButton($inputname . 'checkanswertype', 'checkanswertype', 'qtype_stack');

        $mform->addElement('selectyesno', $inputname . 'mustverify',
                stack_string('mustverify'));
        $mform->setDefault($inputname . 'mustverify', $this->stackconfig->inputmustverify);
        $mform->addHelpButton($inputname . 'mustverify', 'mustverify', 'qtype_stack');

        $mform->addElement('select', $inputname . 'showvalidation',
                stack_string('showvalidation'), stack_options::get_showvalidation_options());
        $mform->setDefault($inputname . 'showvalidation', $this->stackconfig->inputshowvalidation);
        $mform->addHelpButton($inputname . 'showvalidation', 'showvalidation', 'qtype_stack');

        $mform->addElement('text', $inputname . 'options', stack_string('inputextraoptions'), array('size' => 20));
        $mform->setType($inputname . 'options', PARAM_RAW);
        $mform->addHelpButton($inputname . 'options', 'inputextraoptions', 'qtype_stack');
    }

    /**
     * Add the form elements defining one PRT.
     * @param string $prtname the name of the PRT.
     * @param MoodleQuickForm $mform the form being assembled.
     * @param int $count the number of times this PRT appears in the text of the question.
     */
    protected function definition_prt($prtname, MoodleQuickForm $mform, $count) {

        $mform->addElement('header', $prtname . 'header', stack_string('prtheading', $prtname));

        if ($count == 0) {
            $mform->addElement('static', $prtname . 'prtwarning', '', stack_string('prtwillberemoved', $prtname));
            $mform->addElement('advcheckbox', $prtname . 'prtdeleteconfirm', '', stack_string('prtremovedconfirm'));
            $mform->setDefault($prtname . 'prtdeleteconfirm', 0);
            $mform->setExpanded($prtname . 'header');
        }

        $mform->addElement('text', $prtname . 'value', stack_string('questionvalue'), array('size' => 3));
        $mform->setType($prtname . 'value', PARAM_FLOAT);
        $mform->setDefault($prtname . 'value', 1);

        $mform->addElement('selectyesno', $prtname . 'autosimplify',
                stack_string('autosimplify'));
        $mform->setDefault($prtname . 'autosimplify', true);
        $mform->addHelpButton($prtname . 'autosimplify', 'autosimplifyprt', 'qtype_stack');

        $mform->addElement('textarea', $prtname . 'feedbackvariables',
                stack_string('feedbackvariables'), array('rows' => 3, 'cols' => 80));
        $mform->addHelpButton($prtname . 'feedbackvariables', 'feedbackvariables', 'qtype_stack');

        $inputnames = implode(', ', $this->get_inputs_used_by_prt($prtname));
        $mform->addElement('static', $prtname . 'inputsnote', '',
                stack_string('prtwillbecomeactivewhen', html_writer::tag('b', $inputnames)));

        // Create the section of the form for each node - general bits.
        $graph = $this->get_prt_graph($prtname);

        $mform->addElement('static', $prtname . 'graph', '',
                stack_abstract_graph_svg_renderer::render($graph, $prtname . 'graphsvg'));

        $nextnodechoices = array('-1' => stack_string('stop'));
        foreach ($graph->get_nodes() as $node) {
            $nextnodechoices[$node->name - 1] = stack_string('nodex', $node->name);
        }

        $deletable = count($graph->get_nodes()) > 1;

        foreach ($graph->get_nodes() as $node) {
            $this->definition_prt_node($prtname, $node->name, $nextnodechoices, $deletable, $mform);
        }

        $mform->addElement('submit', $prtname . 'nodeadd', stack_string('addanothernode'));
        $mform->registerNoSubmitButton($prtname . 'nodeadd');
    }

    /**
     * Add the form elements defining one PRT node.
     * @param string $prtname the name of the PRT.
     * @param string $name the name of the node.
     * @param array $nextnodechoices the available choices for the next node.
     * @param bool $deletable whether the user is allowed to delete this node.
     * @param MoodleQuickForm $mform the form being assembled.
     */
    protected function definition_prt_node($prtname, $name, $nextnodechoices, $deletable, MoodleQuickForm $mform) {
        $nodekey = $name - 1;

        unset($nextnodechoices[$nodekey]);

        $nodegroup = array();
        $nodegroup[] = $mform->createElement('select', $prtname . 'answertest[' . $nodekey . ']',
                stack_string('answertest'), $this->answertestchoices);

        $nodegroup[] = $mform->createElement('text', $prtname . 'sans[' . $nodekey . ']',
                stack_string('sans'), array('size' => 15));

        $nodegroup[] = $mform->createElement('text', $prtname . 'tans[' . $nodekey . ']',
                stack_string('tans'), array('size' => 15));

        $nodegroup[] = $mform->createElement('text', $prtname . 'testoptions[' . $nodekey . ']',
                stack_string('testoptions'), array('size' => 5));

        $nodegroup[] = $mform->createElement('selectyesno', $prtname . 'quiet[' . $nodekey . ']',
                stack_string('quiet'));

        $mform->addGroup($nodegroup, $prtname . 'node[' . $nodekey . ']',
                html_writer::tag('b', stack_string('nodex', $name)),
                null, false);
        $mform->addHelpButton($prtname . 'node[' . $nodekey . ']', 'nodehelp', 'qtype_stack');
        $mform->setType($prtname . 'sans[' . $nodekey . ']', PARAM_RAW);
        $mform->setType($prtname . 'tans[' . $nodekey . ']', PARAM_RAW);
        $mform->setType($prtname . 'testoptions[' . $nodekey . ']', PARAM_RAW);

        // Create the section of the form for each node - the branches.
        foreach (array('true', 'false') as $branch) {
            $branchgroup = array();

            $branchgroup[] = $mform->createElement('select', $prtname . $branch . 'scoremode[' . $nodekey . ']',
                    stack_string('scoremode'), $this->scoremodechoices);
            if ($nodekey > 0) {
                if ($branch === 'true') {
                    $mform->setDefault($prtname . $branch . 'scoremode[' . $nodekey . ']', '+');
                } else {
                    $mform->setDefault($prtname . $branch . 'scoremode[' . $nodekey . ']', '-');
                }
            }

            $branchgroup[] = $mform->createElement('text', $prtname . $branch . 'score[' . $nodekey . ']',
                    stack_string('score'), array('size' => 2));
            $mform->setDefault($prtname . $branch . 'score[' . $nodekey . ']', (int) ($branch === 'true' && $nodekey == 0));

            $branchgroup[] = $mform->createElement('text', $prtname . $branch . 'penalty[' . $nodekey . ']',
                    stack_string('penalty'), array('size' => 2));

            $branchgroup[] = $mform->createElement('select', $prtname . $branch . 'nextnode[' . $nodekey . ']',
                    stack_string('next'), $nextnodechoices);

            $branchgroup[] = $mform->createElement('text', $prtname . $branch . 'answernote[' . $nodekey . ']',
                    stack_string('answernote'), array('size' => 10));
            $mform->setDefault($prtname . $branch . 'answernote[' . $nodekey . ']',
                    stack_string('answernotedefault' . $branch, array('prtname' => $prtname, 'nodename' => $name)));

            $mform->addGroup($branchgroup, $prtname . 'nodewhen' . $branch . '[' . $nodekey . ']',
                    stack_string('nodexwhen' . $branch, $name), null, false);
            $mform->addHelpButton($prtname . 'nodewhen' . $branch . '[' . $nodekey . ']', $branch . 'branch', 'qtype_stack');
            $mform->setType($prtname . $branch . 'score[' . $nodekey . ']', PARAM_RAW);
            $mform->setType($prtname . $branch . 'penalty[' . $nodekey . ']', PARAM_RAW);
            $mform->setType($prtname . $branch . 'answernote[' . $nodekey . ']', PARAM_RAW);

            $mform->addElement('editor', $prtname . $branch . 'feedback[' . $nodekey . ']',
                    stack_string('nodex' . $branch . 'feedback', $name), array('rows' => 1), $this->editoroptions);
            $mform->addHelpButton($prtname . $branch . 'feedback[' . $nodekey . ']', 'branchfeedback', 'qtype_stack');
        }

        if ($deletable) {
            $mform->addElement('submit', $prtname . 'nodedelete' . $nodekey, stack_string('nodexdelete', $name));
            $mform->registerNoSubmitButton($prtname . 'nodedelete' . $nodekey);
        }
    }

    public function data_preprocessing($question) {
        $question = parent::data_preprocessing($question);
        $question = $this->data_preprocessing_options($question);
        $question = $this->data_preprocessing_inputs($question);
        $question = $this->data_preprocessing_prts($question);
        $question = $this->data_preprocessing_hints($question);

        if (empty($question->questiontext['text'])) {
            // Nasty hack to override what the base class does. The way it
            // prepares the questiontext field overwrites the default.
            $question->questiontext['text'] = self::DEFAULT_QUESTION_TEXT;
        }

        return $question;
    }

    /**
     * Do the bit of {@link data_preprocessing()} for the data in the qtype_stack_options table.
     * @param object $question the raw data.
     * @return object the updated $question updated object closer to being ready to send to the form.
     */
    protected function data_preprocessing_options($question) {
        if (!isset($question->options)) {
            return $question;
        }
        $opt = $question->options;

        $question->questionvariables     = $opt->questionvariables;
        $question->variantsselectionseed = $opt->variantsselectionseed;
        $question->questionnote          = $opt->questionnote;
        $question->specificfeedback      = $this->prepare_text_field('specificfeedback',
                                            $opt->specificfeedback, $opt->specificfeedbackformat, $question->id);
        $question->prtcorrect            = $this->prepare_text_field('prtcorrect',
                                            $opt->prtcorrect, $opt->prtcorrectformat, $question->id);
        $question->prtpartiallycorrect   = $this->prepare_text_field('prtpartiallycorrect',
                                            $opt->prtpartiallycorrect, $opt->prtpartiallycorrectformat, $question->id);
        $question->prtincorrect          = $this->prepare_text_field('prtincorrect',
                                            $opt->prtincorrect, $opt->prtincorrectformat, $question->id);
        $question->multiplicationsign    = $opt->multiplicationsign;
        $question->complexno             = $opt->complexno;
        $question->inversetrig           = $opt->inversetrig;
        $question->matrixparens          = $opt->matrixparens;
        $question->sqrtsign              = $opt->sqrtsign;
        $question->questionsimplify      = $opt->questionsimplify;
        $question->assumepositive        = $opt->assumepositive;

        return $question;
    }

    /**
     * Do the bit of {@link data_preprocessing()} for the data in the qtype_stack_inputs table.
     * @param object $question the raw data.
     * @return object the updated $question updated object closer to being ready to send to the form.
     */
    protected function data_preprocessing_inputs($question) {
        if (!isset($question->inputs)) {
            return $question;
        }

        foreach ($question->inputs as $inputname => $input) {
            $question->{$inputname . 'type'}               = $input->type;
            $question->{$inputname . 'modelans'}           = $input->tans;
            $question->{$inputname . 'boxsize'}            = $input->boxsize;
            $question->{$inputname . 'strictsyntax'}       = $input->strictsyntax;
            $question->{$inputname . 'insertstars'}        = $input->insertstars;
            $question->{$inputname . 'syntaxhint'}         = $input->syntaxhint;
            $question->{$inputname . 'forbidwords'}        = $input->forbidwords;
            $question->{$inputname . 'allowwords'}         = $input->allowwords;
            $question->{$inputname . 'forbidfloat'}        = $input->forbidfloat;
            $question->{$inputname . 'requirelowestterms'} = $input->requirelowestterms;
            $question->{$inputname . 'checkanswertype'}    = $input->checkanswertype;
            $question->{$inputname . 'mustverify'}         = $input->mustverify;
            $question->{$inputname . 'showvalidation'}     = $input->showvalidation;
            $question->{$inputname . 'options'}            = $input->options;
        }

        return $question;
    }

    /**
     * Do the bit of {@link data_preprocessing()} for the data in the qtype_stack_prts table.
     * @param object $question the raw data.
     * @return object the updated $question updated object closer to being ready to send to the form.
     */
    protected function data_preprocessing_prts($question) {
        if (!isset($question->prts)) {
            return $question;
        }

        foreach ($question->prts as $prtname => $prt) {
            $question->{$prtname . 'value'}             = 0 + $prt->value; // Remove excess decimals.
            $question->{$prtname . 'autosimplify'}      = $prt->autosimplify;
            $question->{$prtname . 'feedbackvariables'} = $prt->feedbackvariables;

            foreach ($prt->nodes as $node) {
                $question = $this->data_preprocessing_node($question, $prtname, $node);
            }
        }

        return $question;
    }

    /**
     * Do the bit of {@link data_preprocessing()} for one PRT node.
     * @param object $question the raw question data.
     * @param string $prtname the name of this PRT.
     * @param object $node the raw data about this node.
     * @return object the updated $question updated object closer to being ready to send to the form.
     */
    protected function data_preprocessing_node($question, $prtname, $node) {
        $nodename = $node->nodename;

        $question->{$prtname . 'answertest' }[$nodename] = $node->answertest;
        $question->{$prtname . 'sans'       }[$nodename] = $node->sans;
        $question->{$prtname . 'tans'       }[$nodename] = $node->tans;
        $question->{$prtname . 'testoptions'}[$nodename] = $node->testoptions;
        $question->{$prtname . 'quiet'      }[$nodename] = $node->quiet;

        // 0 + bit is to eliminate excessive decimal places from the DB.
        $question->{$prtname . 'truescoremode' }[$nodename] = $node->truescoremode;
        $question->{$prtname . 'truescore'     }[$nodename] = 0 + $node->truescore;
        $penalty = $node->truepenalty;
        if ('' != trim($penalty)) {
            $penalty = 0 + $penalty;
        }
        $question->{$prtname . 'truepenalty'   }[$nodename] = $penalty;
        $question->{$prtname . 'truenextnode'  }[$nodename] = $node->truenextnode;
        $question->{$prtname . 'trueanswernote'}[$nodename] = $node->trueanswernote;
        $question->{$prtname . 'truefeedback'  }[$nodename] = $this->prepare_text_field(
                $prtname . 'truefeedback[' . $nodename . ']', $node->truefeedback,
                $node->truefeedbackformat, $node->id, 'prtnodetruefeedback');

        $question->{$prtname . 'falsescoremode' }[$nodename] = $node->falsescoremode;
        $question->{$prtname . 'falsescore'     }[$nodename] = 0 + $node->falsescore;
        $penalty = $node->falsepenalty;
        if ('' != trim($penalty)) {
            $penalty = 0 + $penalty;
        }
        $question->{$prtname . 'falsepenalty'   }[$nodename] = $penalty;
        $question->{$prtname . 'falsenextnode'  }[$nodename] = $node->falsenextnode;
        $question->{$prtname . 'falseanswernote'}[$nodename] = $node->falseanswernote;
        $question->{$prtname . 'falsefeedback'  }[$nodename] = $this->prepare_text_field(
                $prtname . 'falsefeedback[' . $nodename . ']', $node->falsefeedback,
                $node->falsefeedbackformat, $node->id, 'prtnodefalsefeedback');

        // See comment in the parent method about this hack.
        unset($this->_form->_defaultValues["{$prtname}truescoremode[$nodename]"]);
        unset($this->_form->_defaultValues["{$prtname}falsescoremode[$nodename]"]);
        unset($this->_form->_defaultValues["{$prtname}truescore[$nodename]"]);
        unset($this->_form->_defaultValues["{$prtname}falsescore[$nodename]"]);
        unset($this->_form->_defaultValues["{$prtname}trueanswernote[$nodename]"]);
        unset($this->_form->_defaultValues["{$prtname}falseanswernote[$nodename]"]);

        return $question;
    }

    /**
     * Do the necessary data_preprocessing work for one text field.
     * @param string $field the field / file-area name. (These are assumed to be the same.)
     * @param string $text the raw text contents of this field.
     * @param int $format the text format (one of the FORMAT_... constants.)
     * @param int $itemid file area itemid.
     * @param string $filearea the file area name. Defaults to $field.
     * @return array in the format needed by the form.
     */
    protected function prepare_text_field($field, $text, $format, $itemid, $filearea = '') {
        if ($filearea === '') {
            $filearea = $field;
        }

        $data = array();
        $data['itemid'] = file_get_submitted_draft_itemid($field);
        $data['text'] = file_prepare_draft_area($data['itemid'], $this->context->id,
                'qtype_stack', $filearea, $itemid, $this->fileoptions, $text);
        $data['format'] = $format;
        return $data;
    }

    public function validation($fromform, $files) {
        $errors = parent::validation($fromform, $files);

        $inputs = $this->get_input_names_from_question_text();
        $prts = $this->get_prt_names_from_question();
        $fixingdollars = array_key_exists('fixdollars', $fromform);

        $this->options = new stack_options();
        $this->options->set_option('multiplicationsign', $fromform['multiplicationsign']);
        $this->options->set_option('complexno',          $fromform['complexno']);
        $this->options->set_option('inversetrig',        $fromform['inversetrig']);
        $this->options->set_option('matrixparens',       $fromform['matrixparens']);
        $this->options->set_option('sqrtsign',    (bool) $fromform['sqrtsign']);
        $this->options->set_option('simplify',    (bool) $fromform['questionsimplify']);
        $this->options->set_option('assumepos',   (bool) $fromform['assumepositive']);

        // We slightly break the usual conventions of validation, in that rather
        // than building up $errors as an array of strings, we initially build it
        // up as an array of arrays, then at the end remove any empty arrays,
        // and implod (' ', ...) any arrays that are non-empty. This makes our
        // rather complex validation easier to implement.

        // 1) Validate all the fixed question fields.
        // Question variables.
        $errors = $this->validate_cas_keyval($errors, $fromform['questionvariables'], 'questionvariables');

        // Question text.
        $errors['questiontext'] = array();
        $errors = $this->validate_cas_text($errors, $fromform['questiontext']['text'], 'questiontext', $fixingdollars);

        // Check for whitespace following placeholders.
        $sloppytags = $this->get_sloppy_tags($fromform['questiontext']['text']);
        foreach ($sloppytags as $sloppytag) {
            $errors['questiontext'][] = stack_string(
                        'questiontextplaceholderswhitespace', $sloppytag);
        }

        foreach ($inputs as $inputname => $counts) {
            list($numinputs, $numvalidations) = $counts;

            if ($numinputs == 0 && $numvalidations == 0) {
                if (!$fromform[$inputname . 'deleteconfirm']) {
                    $errors['questiontext'][] = stack_string('inputremovedconfirmbelow', $inputname);
                }
                continue;
            }

            if ($numinputs == 0) {
                $errors['questiontext'][] = stack_string(
                        'questiontextmustcontain', '[[input:' . $inputname . ']]');
            } else if ($numinputs > 1) {
                $errors['questiontext'][] = stack_string(
                        'questiontextonlycontain', '[[input:' . $inputname . ']]');
            }

            if ($numvalidations == 0) {
                $errors['questiontext'][] = stack_string(
                        'questiontextmustcontain', '[[validation:' . $inputname . ']]');
            } else if ($numvalidations > 1) {
                $errors['questiontext'][] = stack_string(
                        'questiontextonlycontain', '[[validation:' . $inputname . ']]');
            }
        }

        if (empty($inputs) && !empty($prts)) {
            $errors['questiontext'][] = stack_string('noprtsifnoinputs');
        }

        // Default mark.
        if (empty($inputs) && $fromform['defaultmark'] != 0) {
            $errors['defaultmark'][] = stack_string('defaultmarkzeroifnoprts');
        }

        // Penalty.
        $penalty = $fromform['penalty'];
        if (!is_numeric($penalty) || $penalty < 0 || $penalty > 1) {
            $errors['penalty'][] = stack_string('penaltyerror');
        }

        // Specific feedback.
        $errors['specificfeedback'] = array();
        $errors = $this->validate_cas_text($errors, $fromform['specificfeedback']['text'], 'specificfeedback', $fixingdollars);

        $errors['specificfeedback'] += $this->check_no_placeholders(
                    stack_string('specificfeedback'), $fromform['specificfeedback']['text'],
                    array('input', 'validation'));

        // General feedback.
        $errors['generalfeedback'] = array();
        $errors = $this->validate_cas_text($errors, $fromform['generalfeedback']['text'], 'generalfeedback', $fixingdollars);
        $errors['generalfeedback'] += $this->check_no_placeholders(
                    get_string('generalfeedback', 'question'), $fromform['generalfeedback']['text']);

        // Question note.
        $errors['questionnote'] = array();
        if ('' == $fromform['questionnote']) {
            if (!(false === strpos($fromform['questionvariables'], 'rand'))) {
                $errors['questionnote'][] = stack_string('questionnotempty');
            }
        } else {
            // Note, the 'questionnote' does not have an editor field and hence no 'text' sub-clause.
            $errors = $this->validate_cas_text($errors, $fromform['questionnote'], 'questionnote', $fixingdollars);
        }

        $errors['questionnote'] += $this->check_no_placeholders(
                    stack_string('questionnote'), $fromform['questionnote']);

        // 2) Validate all inputs.
        foreach ($inputs as $inputname => $counts) {
            list($numinputs, $numvalidations) = $counts;

            if ($numinputs == 0 && $numvalidations == 0 && !$fromform[$inputname . 'deleteconfirm']) {
                $errors[$inputname . 'deleteconfirm'][] = stack_string('youmustconfirm');
            }

            if (strlen($inputname) > 18 && !isset($fromform[$inputname . 'deleteconfirm'])) {
                $errors['questiontext'][] = stack_string('inputnamelength', $inputname);
            }

            if ($fromform[$inputname . 'mustverify'] and $fromform[$inputname . 'showvalidation'] == 0) {
                $errors[$inputname . 'mustverify'][] = stack_string('mustverifyshowvalidation');
            }

            if (array_key_exists($inputname . 'modelans', $fromform)) {
                $errors = $this->validate_cas_string($errors,
                        $fromform[$inputname . 'modelans'], $inputname . 'modelans', $inputname . 'modelans');
            }
        }

        // 3) Validate all prts.
        foreach ($prts as $prtname => $count) {
            if ($count == 0) {
                if (!$fromform[$prtname . 'prtdeleteconfirm']) {
                    $errors['specificfeedback'][] = stack_string('prtremovedconfirmbelow', $prtname);
                    $errors[$prtname . 'prtdeleteconfirm'][] = stack_string('youmustconfirm');
                }
            } else if ($count > 1) {
                $errors['specificfeedback'][] = stack_string(
                        'questiontextfeedbackonlycontain', '[[feedback:' . $prtname . ']]');
            }

            $errors = $this->validate_prt($errors, $fromform, $prtname, $fixingdollars);

        }

        // 4) Validate all hints.
        foreach ($fromform['hint'] as $index => $hint) {
            $errors = $this->validate_cas_text($errors, $hint['text'], 'hint[' . $index . ']', $fixingdollars);
        }

        // Clear out any empty $errors elements, ready for the next check.
        foreach ($errors as $field => $messages) {
            if (empty($messages)) {
                unset($errors[$field]);
            }
        }

        // If everything else is OK, try executing the CAS code to check for errors.
        if (empty($errors)) {
            $errors = $this->validate_question_cas_code($errors, $fromform, $fixingdollars);
        }

        // Convert the $errors array from our array of arrays format to the
        // standard array of strings format.
        foreach ($errors as $field => $messages) {
            if ($messages) {
                $errors[$field] = implode(' ', $messages);
            } else {
                unset($errors[$field]);
            }
        }

        return $errors;
    }

    /**
     * Validate the fields for a given PRT
     * @param array $errors the error so far. This array is added to and returned.
     * @param array $fromform the submitted data to validate.
     * @param string $prtname the name of the PRT to validate.
     * @return array the update $errors array.
     */
    protected function validate_prt($errors, $fromform, $prtname, $fixingdollars) {

        if (strlen($prtname) > 18 && !isset($fromform[$prtname . 'prtdeleteconfirm'])) {
            $errors['specificfeedback'][] = stack_string('prtnamelength', $prtname);
        }

        if (!array_key_exists($prtname . 'feedbackvariables', $fromform)) {
            // This happens when you edit the question text to add more PRTs.
            // The user added a new PRT and did not click "Verify the question
            // text and update the form". We need to fail validation, so the
            // form is re-displayed so that this PRT can be configured.
            $errors[$prtname . 'value'][] = stack_string('prtmustbesetup');
            return $errors;
        }

        // Check the fields that belong to the PRT as a whole.
        $errors = $this->validate_cas_keyval($errors, $fromform[$prtname . 'feedbackvariables'],
                $prtname . 'feedbackvariables');

        if ($fromform[$prtname . 'value'] < 0) {
            $errors[$prtname . 'value'][] = stack_string('questionvaluepostive');
        }

        // Check that answernotes are not duplicated.
        $answernotes = array_merge($fromform[$prtname . 'trueanswernote'], $fromform[$prtname . 'falseanswernote']);
        if (count(array_unique($answernotes)) < count($answernotes)) {
            // Strictly speaking this should not be in the feedback variables.  But there is no general place to put this error.
            $errors[$prtname . 'feedbackvariables'][] = stack_string('answernoteunique');
        }

        // Check the nodes.
        $graph = $this->get_prt_graph($prtname);
        $textformat = null;
        foreach ($graph->get_nodes() as $node) {
            $nodekey = $node->name - 1;

            // Check the fields the belong to this node individually.
            $errors = $this->validate_prt_node($errors, $fromform, $prtname, $nodekey, $fixingdollars);

            if (is_null($textformat)) {
                $textformat = $fromform[$prtname . 'truefeedback'][$nodekey]['format'];
            }
            if ($textformat != $fromform[$prtname . 'truefeedback'][$nodekey]['format']) {
                $errors[$prtname . 'truefeedback[' . $nodekey . ']'][] =
                        stack_string('allnodefeedbackmustusethesameformat');
            }
        }

        // Check that the nodes form a directed acyclic graph.
        $roots = $graph->get_roots();

        // There should only be a single root. If there is more than one, then we
        // assume that the first one is the intended root, and flat the others as unused.
        array_shift($roots);
        foreach ($roots as $node) {
            $errors[$prtname . 'node[' . ($node->name - 1) . ']'][] = stack_string('nodenotused');
        }
        foreach ($graph->get_broken_cycles() as $backlink => $notused) {
            list($nodename, $direction) = explode('|', $backlink);
            if ($direction == stack_abstract_graph::LEFT) {
                $field = 'nodewhentrue';
            } else {
                $field = 'nodewhenfalse';
            }
            $errors[$prtname.$field.'['.($nodename - 1).']'][] = stack_string('nodeloopdetected');
        }

        return $errors;
    }

    /**
     * Validate the fields for a given PRT node.
     * @param array $errors the error so far. This array is added to and returned.
     * @param array $fromform the submitted data to validate.
     * @param string $prtname the name of the PRT to validate.
     * @param string $nodekey the name of the node to validate.
     * @return array the update $errors array.
     */
    protected function validate_prt_node($errors, $fromform, $prtname, $nodekey, $fixingdollars) {
        $nodegroup = $prtname . 'node[' . $nodekey . ']';

        $errors = $this->validate_cas_string($errors, $fromform[$prtname . 'sans'][$nodekey],
                $nodegroup, $prtname . 'sans' . $nodekey, 'sansrequired');

        $errors = $this->validate_cas_string($errors, $fromform[$prtname . 'tans'][$nodekey],
                $nodegroup, $prtname . 'tans' . $nodekey, 'tansrequired');

        $answertest = new stack_ans_test_controller($fromform[$prtname . 'answertest'][$nodekey]);
        if ($answertest->required_atoptions()) {
            $opt = trim($fromform[$prtname . 'testoptions'][$nodekey]);

            if ('' === trim($opt)) {
                $errors[$nodegroup][] = stack_string('testoptionsrequired');

            } else if (strlen($opt > 255)) {
                $errors[$nodegroup][] = stack_string('testoptionsinvalid',
                        stack_string('strlengtherror'));

            } else {
                // TODO capture this for later execution.
                list($valid, $message) = $answertest->validate_atoptions($opt);
                if (!$valid) {
                    $errors[$nodegroup][] = stack_string('testoptionsinvalid', $message);
                }
            }
        }

        foreach (array('true', 'false') as $branch) {
            $branchgroup = $prtname . 'nodewhen' . $branch . '[' . $nodekey . ']';

            $score = $fromform[$prtname . $branch . 'score'][$nodekey];
            if (!is_numeric($score) || $score < 0 || $score > 1) {
                 $errors[$branchgroup][] = stack_string('scoreerror');
            }

            $penalty = $fromform[$prtname . $branch . 'penalty'][$nodekey];
            if ('' != $penalty && (!is_numeric($penalty) || $penalty < 0 || $penalty > 1)) {
                $errors[$branchgroup][] = stack_string('penaltyerror2');
            }

            $answernote = $fromform[$prtname . $branch . 'answernote'][$nodekey];
            if ('' == $answernote) {
                $errors[$branchgroup][] = stack_string('answernoterequired');
            } else if (strstr($answernote, '|') !== false) {
                $errors[$branchgroup][] = stack_string('answernote_err');
                foreach ($fromform[$prtname.$branch.'answernote'] as $key => $strin) {
                    if ('' == trim($strin)) {
                        $interror[$prtname.'nodewhen'.$branch.'['.$key.']'][] = stack_string('answernoterequired');
                    } else if (strstr($strin, '|') !== false) {
                        $nodename = $key + 1;
                        $interror[$prtname.'nodewhen'.$branch.'['.$key.']'][] = stack_string('answernote_err');
                    }
                }
            }

            $errors = $this->validate_cas_text($errors, $fromform[$prtname . $branch . 'feedback'][$nodekey]['text'],
                    $prtname . $branch . 'feedback[' . $nodekey . ']', $fixingdollars);
        }

        return $errors;
    }

    /**
     * Validate a CAS string field to make sure that: 1. it fits in the DB, and
     * 2. that it is syntactically valid.
     * @param array $errors the errors array that validation is assembling.
     * @param string $value the submitted value validate.
     * @param string $fieldname the name of the field add any errors to.
     * @param string $savesession the array key to save the string to in $this->validationcasstrings.
     * @param bool|string $notblank false means do nothing (default). A string
     *      will validate that the field is not blank, and if it is, display that error.
     * @param int $maxlength the maximum allowable length. Defaults to 255.
     * @return array updated $errors array.
     */
    protected function validate_cas_string($errors, $value, $fieldname, $savesession, $notblank = true, $maxlength = 255) {

        if ($notblank && '' === trim($value)) {
            $errors[$fieldname][] = stack_string('nonempty');

        } else if (strlen($value) > $maxlength) {
            $errors[$fieldname][] = stack_string('strlengtherror');

        } else {
            $casstring = new stack_cas_casstring($value);
            if (!$casstring->get_valid('t')) {
                $errors[$fieldname][] = $casstring->get_errors();
            }
        }

        return $errors;
    }

    /**
     * Validate a CAS text field.
     * @param array $errors the errors array that validation is assembling.
     * @param string $value the submitted value validate.
     * @param string $fieldname the name of the field add any errors to.
     * @param string $savesession the array key to save the session to in $this->validationcasstrings.
     * @return array updated $errors array.
     */
    protected function validate_cas_text($errors, $value, $fieldname, $fixingdollars, $session = null) {
        if (!$fixingdollars && strpos($value, '$$') !== false) {
            $errors[$fieldname][] = stack_string('forbiddendoubledollars');
        }

        $castext = new stack_cas_text($value, $session, $this->seed, 't');
        if (!$castext->get_valid()) {
            $errors[$fieldname][] = $castext->get_errors();
            return $errors;
        }

        // Validate any [[facts:...]] tags.
        $unrecognisedtags = stack_fact_sheets::get_unrecognised_tags($value);
        if ($unrecognisedtags) {
            $errors[$fieldname][] = stack_string('unrecognisedfactstags',
                    array('tags' => implode(', ', $unrecognisedtags)));
            return $errors;
        }

        if ($session) {
            $display = $castext->get_display_castext();
            if ($castext->get_errors()) {
                $errors[$fieldname][] = $castext->get_errors();
                return $errors;
            }
        }

        return $errors;
    }

    /**
     * Validate a CAS string field to make sure that: 1. it fits in the DB, and
     * 2. that it is syntactically valid.
     * @param array $errors the errors array that validation is assembling.
     * @param string $value the submitted value validate.
     * @param string $fieldname the name of the field add any errors to.
     * @return array updated $errors array.
     */
    protected function validate_cas_keyval($errors, $value, $fieldname) {
        if ('' == trim($value)) {
            return $errors;
        }

        $keyval = new stack_cas_keyval($value, $this->options, $this->seed, 't');
        if (!$keyval->get_valid()) {
            $errors[$fieldname][] = $keyval->get_errors();
        }

        return $errors;
    }

    /**
     * Validate all the maxima code in the question.
     *
     * This is done last, and separate from the other validation for two reasons:
     * 1. The rest of the validation is organised to validate the form in order,
     *    to match the way the form is defined. Here we need to validate in the
     *    order that the CAS is evaluated at run-time.
     * 2. This is the slowest part of validation, so we only do it at the end if
     *    everything else is OK.
     *
     * @param array $errors the errors array that validation is assembling.
     * @param array $fromform the submitted data to validate.
     * @return array updated $errors array.
     */
    protected function validate_question_cas_code($errors, $fromform, $fixingdollars) {

        $keyval = new stack_cas_keyval($fromform['questionvariables'], $this->options, $this->seed, 't');
        $keyval->instantiate();
        $session = $keyval->get_session();
        if ($session->get_errors()) {
            $errors['questionvariables'][] = $session->get_errors();
            return $errors;
        }

        // Instantiate all text fields and look for errors.
        $castextfields = array('questiontext', 'specificfeedback', 'generalfeedback');
        foreach ($castextfields as $field) {
            $errors = $this->validate_cas_text($errors, $fromform[$field]['text'], $field, $fixingdollars, clone $session);
        }
        $errors = $this->validate_cas_text($errors, $fromform['questionnote'], 'questionnote', $fixingdollars, clone $session);

        // Make a list of all inputs, instantiate it and then look for errors.
        $inputs = $this->get_input_names_from_question_text();
        $inputvalues = array();
        foreach ($inputs as $inputname => $notused) {
            $cs = new stack_cas_casstring($inputname.':'.$fromform[$inputname . 'modelans']);
            $cs->get_valid('t');
            $inputvalues[] = $cs;
        }
        $inputsession = clone $session;
        $inputsession->add_vars($inputvalues);
        $inputsession->instantiate();

        $getdebuginfo = false;
        foreach ($inputs as $inputname => $notused) {
            if ($inputsession->get_errors_key($inputname)) {
                $errors[$inputname . 'modelans'][] = $inputsession->get_errors_key($inputname);
                if ('' == $inputsession->get_value_key($inputname)) {
                    $getdebuginfo = true;
                }
                // TODO: Send the acutal value to the input, and ask it to validate it.
                // For example, the matrix input type could check that the model answer is a matrix.
            }

            if ($fromform[$inputname . 'options'] && $inputsession->get_errors_key('optionsfor' . $inputname)) {
                $errors[$inputname . 'options'][] = $inputsession->get_errors_key('optionsfor' . $inputname);
            }
        }

        if ($getdebuginfo) {
            $errors['questionvariables'][] = $inputsession->get_debuginfo();
        }

        // At this point if we have errors, especially with inputs, there is no point in executing any of the PRTs.
        if (!empty($errors)) {
            return $errors;
        }

        // TODO: loop over all the PRTs in a similar manner....
        // Remember, to use
        // clone $inputsession
        // as the base session to have all the teacher's answers instantiated.
        // Otherwise we are likley to do illigitimate things to the various inputs.

        return $errors;

    }


    /**
     * Check a form field to ensure it does not contain any placeholders of given types.
     * @param string $fieldname the name of this field. Used in the error messages.
     * @param value $value the value to check.
     * @param array $placeholders types to check for. By default 'input', 'validation' and 'feedback'.
     * @return array of problems (so an empty array means all is well).
     */
    protected function check_no_placeholders($fieldname, $value, $placeholders = array('input', 'validation', 'feedback')) {
        $problems = array();
        foreach ($placeholders as $placeholder) {
            if (stack_utils::extract_placeholders($value, 'input')) {
                $problems[] = stack_string('fieldshouldnotcontainplaceholder',
                        array('field' => $fieldname, 'type' => $placeholder));
            }
        }
        return $problems;
    }

    public function qtype() {
        return 'stack';
    }
}
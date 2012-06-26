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
require_once($CFG->dirroot . '/question/type/stack/stack/acyclicchecker.class.php');


/**
 * Stack question editing form definition.
 *
 * @copyright  2012 The Open University.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_stack_edit_form extends question_edit_form {
    /** @var string the default question text for a new question. */
    const DEFAULT_QUESTION_TEXT = '<p>[[input:ans1]]</p><div>[[validation:ans1]]</div>';
    /** @var string the default specific feedback for a new question. */
    const DEFAULT_SPECIFIC_FEEDBACK = '[[feedback:prt1]]';

    /** @var int array key into the results of get_input_names_from_question_text for the count of input placeholders. */
    const INPUTS = 0;
    /** @var int array key into the results of get_input_names_from_question_text for the count of validation placeholders. */
    const VALIDATAIONS = 1;

    /** @var string caches the result of {@link get_current_question_text()}. */
    protected $questiontext = null;

    /** @var string caches the result of {@link get_current_specific_feedback()}. */
    protected $specificfeedback = null;

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
    protected function get_required_nodes_for_prt($prtname) {
        // If the form has been submitted and is being redisplayed, and this is
        // an existing PRT, base things on the submitted data.
        $submitted = optional_param_array($prtname . 'answertest', null, PARAM_RAW);
        if ($submitted) {
            foreach ($submitted as $key => $notused) {
                if (optional_param($prtname . 'nodedelete' . $key, false, PARAM_BOOL)) {
                    unset($submitted[$key]);

                    // Slightly odd to register the button here, especially since
                    // now this node has been deleted, this button will not exist,
                    // but anyway this works, and in necessary to stop the form
                    // from being submitted.
                    $this->_form->registerNoSubmitButton($prtname . 'nodedelete' . $key);
                }
            }

            if (optional_param($prtname . 'nodeadd', false, PARAM_BOOL)) {
                $submitted[] = true;
            }

            return array_keys($submitted);
        }

        // Otherwise, if an existing question is being edited, and this is an
        // existing PRT, base things on the existing question definition.
        if (!empty($this->question->prts[$prtname]->nodes)) {
            return array_keys($this->question->prts[$prtname]->nodes);
        }

        // Otherwise, it is a new PRT. Just one node.
        return array(0);
    }

    /**
     * @return array of the input names that currently appear in the question text.
     */
    protected function get_input_names_from_question_text() {
        $questiontext = $this->get_current_question_text();

        $inputs = stack_utils::extract_placeholders($questiontext, 'input');
        $validations = stack_utils::extract_placeholders($questiontext, 'validation');

        $inputnames = array();
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
            $inputnames[$inputname][self::VALIDATAIONS] += 1;
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
        foreach ($prts as $name) {
            if (!array_key_exists($name, $prtnames)) {
                $prtnames[$name] = 0;
            }
            $prtnames[$name] += 1;
        }
        return $prtnames;
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
        $input_keys = array();
        if (is_array($inputs)) {
            foreach ($inputs as $input) {
                $input_keys[] = $input->name;
            }
        } else {
            return array();
        }

        // If we are creating a new question, or if we add a new prt in the
        // question stem, then the PRT will not yet exist, so return an empty array.
        if (is_null($this->question->prts) || !array_key_exists($prtname, $this->question->prts)) {
            return array();
        }
        $prt = $this->question->prts[$prtname];

        $prt_nodes = array();
        foreach ($prt->nodes as $node) {
            $sans = new stack_cas_casstring($node->sans);
            $tans = new stack_cas_casstring($node->tans);
            $prt_node = new stack_potentialresponse_node($sans, $tans, $node->answertest, $node->testoptions);
            $prt_node->add_branch(1, '+', 0, '', -1, $node->truefeedback, '');
            $prt_node->add_branch(0, '+', 0, '', -1, $node->falsefeedback, '');
            $prt_nodes[] = $prt_node;
        }
        $feedbackvariables = new stack_cas_keyval($prt->feedbackvariables, null, 0, 't');
        $potential_response_tree = new stack_potentialresponse_tree(
                '', '', false, 0, $feedbackvariables->get_session(), $prt_nodes);
        return $potential_response_tree->get_required_variables($input_keys);
    }

    protected function definition_inner(/* MoodleQuickForm */ $mform) {

        // Prepare input types.
        $types = stack_input_factory::get_available_types();
        $this->typechoices = array();
        foreach ($types as $type => $notused) {
            $this->typechoices[$type] = get_string('inputtype' . $type, 'qtype_stack');
        }
        collatorlib::asort($this->typechoices);

        // Prepare answer test types.
        $answertests = stack_ans_test_controller::get_available_ans_tests();
        $this->answertestchoices = array();
        foreach ($answertests as $test => $string) {
            $this->answertestchoices[$test] = get_string($string, 'qtype_stack');
        }
        collatorlib::asort($this->answertestchoices);

        // Prepare score mode choices.
        $this->scoremodechoices = array(
                    '=' => '=',
                    '+' => '+',
                    '-' => '-',
        );

        $inputnames = $this->get_input_names_from_question_text();
        $prtnames = $this->get_prt_names_from_question();

        // Note that for the editor elements, we are using
        // $mform->getElement('prtincorrect')->setValue(...);
        // instead of setDefault, because setDefault does not work for editors.

        $mform->addHelpButton('questiontext', 'questiontext', 'qtype_stack');
        $mform->addRule('questiontext', get_string('questiontextnonempty', 'qtype_stack'), 'required', '', 'client');

        $qvars = $mform->createElement('textarea', 'questionvariables',
                get_string('questionvariables', 'qtype_stack'), array('rows' => 5, 'cols' => 80));
        $mform->insertElementBefore($qvars, 'questiontext');
        $mform->addHelpButton('questionvariables', 'questionvariables', 'qtype_stack');

        $seed = $mform->createElement('text', 'variantsselectionseed',
                get_string('variantsselectionseed', 'qtype_stack'), array('size' => 50));
        $mform->insertElementBefore($seed, 'questiontext');
        $mform->addHelpButton('variantsselectionseed', 'variantsselectionseed', 'qtype_stack');

        $pen = $mform->createElement('text', 'penalty', get_string('penalty', 'qtype_stack'), array('size' => 5));
        $mform->insertElementBefore($pen, 'generalfeedback');
        $mform->addHelpButton('penalty', 'penalty', 'qtype_stack');
        $mform->setDefault('penalty', 0.1000000);
        $mform->addRule('penalty', null, 'required', null, 'client');

        $sf = $mform->createElement('editor', 'specificfeedback',
                get_string('specificfeedback', 'question'), array('rows' => 10), $this->editoroptions);
        $mform->insertElementBefore($sf, 'generalfeedback');

        $mform->getElement('specificfeedback')->setValue(array('text' => self::DEFAULT_SPECIFIC_FEEDBACK));
        $mform->addHelpButton('specificfeedback', 'specificfeedback', 'qtype_stack');

        $mform->addHelpButton('generalfeedback', 'generalfeedback', 'qtype_stack');

        $mform->addElement('textarea', 'questionnote',
                get_string('questionnote', 'qtype_stack'), array('rows' => 2, 'cols' => 80));
        $mform->addHelpButton('questionnote', 'questionnote', 'qtype_stack');

        $mform->addElement('submit', 'verify', get_string('verifyquestionandupdate', 'qtype_stack'));
        $mform->registerNoSubmitButton('verify');

        // Inputs.
        foreach ($inputnames as $inputname => $notused) {
            $this->definition_input($inputname, $mform);
        }

        // PRTs.
        foreach ($prtnames as $prtname => $notused) {
            $this->definition_prt($prtname, $mform);
        }

        // Options.
        $mform->addElement('header', 'optionsheader', get_string('options', 'qtype_stack'));

        $mform->addElement('selectyesno', 'questionsimplify',
                get_string('questionsimplify', 'qtype_stack'));
        $mform->setDefault('questionsimplify', true);
        $mform->addHelpButton('questionsimplify', 'autosimplify', 'qtype_stack');

        $mform->addElement('selectyesno', 'assumepositive',
                get_string('assumepositive', 'qtype_stack'));
        $mform->addHelpButton('assumepositive', 'assumepositive', 'qtype_stack');

        $mform->addElement('editor', 'prtcorrect',
                get_string('prtcorrectfeedback', 'qtype_stack'),
                array('rows' => 1), $this->editoroptions);
        $mform->getElement('prtcorrect')->setValue(array(
                'text' => get_string('defaultprtcorrectfeedback', 'qtype_stack')));

        $mform->addElement('editor', 'prtpartiallycorrect',
                get_string('prtpartiallycorrectfeedback', 'qtype_stack'),
                array('rows' => 1), $this->editoroptions);
        $mform->getElement('prtpartiallycorrect')->setValue(array(
                        'text' => get_string('defaultprtpartiallycorrectfeedback', 'qtype_stack')));

        $mform->addElement('editor', 'prtincorrect',
                get_string('prtincorrectfeedback', 'qtype_stack'),
                array('rows' => 1), $this->editoroptions);
        $mform->getElement('prtincorrect')->setValue(array(
                        'text' => get_string('defaultprtincorrectfeedback', 'qtype_stack')));

        $mform->addElement('select', 'multiplicationsign',
                get_string('multiplicationsign', 'qtype_stack'), array(
                    'dot'   => get_string('multdot', 'qtype_stack'),
                    'cross' => get_string('multcross', 'qtype_stack'),
                    'none'  => get_string('none')));
        $mform->addHelpButton('multiplicationsign', 'multiplicationsign', 'qtype_stack');

        $mform->addElement('selectyesno', 'sqrtsign',
                get_string('sqrtsign', 'qtype_stack'));
        $mform->setDefault('sqrtsign', true);
        $mform->addHelpButton('sqrtsign', 'sqrtsign', 'qtype_stack');

        $mform->addElement('select', 'complexno',
                get_string('complexno', 'qtype_stack'), array(
                    'i' => 'i', 'j' => 'j', 'symi' => 'symi', 'symj' => 'symj'));
        $mform->addHelpButton('complexno', 'complexno', 'qtype_stack');
    }

    /**
     * Add the form fields for a given input element to the form.
     * @param string $inputname the input name.
     * @param MoodleQuickForm $mform the form being assembled.
     */
    protected function definition_input($inputname, MoodleQuickForm $mform) {

        $mform->addElement('header', $inputname . 'header', get_string('inputheading', 'qtype_stack', $inputname));

        $mform->addElement('select', $inputname . 'type', get_string('inputtype', 'qtype_stack'), $this->typechoices);
        $mform->addHelpButton($inputname . 'type', 'inputtype', 'qtype_stack');

        $mform->addElement('text', $inputname . 'modelans', get_string('teachersanswer', 'qtype_stack'), array('size' => 20));
        $mform->addRule($inputname . 'modelans', get_string('teachersanswer', 'qtype_stack'), 'required', '', 'client', false, false);
        $mform->addHelpButton($inputname . 'modelans', 'teachersanswer', 'qtype_stack');

        $mform->addElement('text', $inputname . 'boxsize', get_string('boxsize', 'qtype_stack'), array('size' => 3));
        $mform->setDefault($inputname . 'boxsize', 15);
        $mform->setType($inputname . 'boxsize', PARAM_INT);
        $mform->addHelpButton($inputname . 'boxsize', 'boxsize', 'qtype_stack');

        $mform->addElement('selectyesno', $inputname . 'strictsyntax',
                get_string('strictsyntax', 'qtype_stack'));
        $mform->setDefault($inputname . 'strictsyntax', true);
        $mform->addHelpButton($inputname . 'strictsyntax', 'strictsyntax', 'qtype_stack');

        $mform->addElement('selectyesno', $inputname . 'insertstars',
                get_string('insertstars', 'qtype_stack'));
        $mform->setDefault($inputname . 'insertstars', false);
        $mform->addHelpButton($inputname . 'insertstars', 'insertstars', 'qtype_stack');

        $mform->addElement('text', $inputname . 'syntaxhint', get_string('syntaxhint', 'qtype_stack'), array('size' => 20));
        $mform->addHelpButton($inputname . 'syntaxhint', 'syntaxhint', 'qtype_stack');

        $mform->addElement('text', $inputname . 'forbidwords', get_string('forbidwords', 'qtype_stack'), array('size' => 20));
        $mform->addHelpButton($inputname . 'forbidwords', 'forbidwords', 'qtype_stack');

        $mform->addElement('selectyesno', $inputname . 'forbidfloat',
                get_string('forbidfloat', 'qtype_stack'));
        $mform->setDefault($inputname . 'forbidfloat', true);
        $mform->addHelpButton($inputname . 'forbidfloat', 'forbidfloat', 'qtype_stack');

        $mform->addElement('selectyesno', $inputname . 'requirelowestterms',
                get_string('requirelowestterms', 'qtype_stack'));
        $mform->setDefault($inputname . 'requirelowestterms', false);
        $mform->addHelpButton($inputname . 'requirelowestterms', 'requirelowestterms', 'qtype_stack');

        $mform->addElement('selectyesno', $inputname . 'checkanswertype',
                get_string('checkanswertype', 'qtype_stack'));
        $mform->setDefault($inputname . 'checkanswertype', false);
        $mform->addHelpButton($inputname . 'checkanswertype', 'checkanswertype', 'qtype_stack');

        $mform->addElement('selectyesno', $inputname . 'mustverify',
                get_string('mustverify', 'qtype_stack'));
        $mform->setDefault($inputname . 'mustverify', true);
        $mform->addHelpButton($inputname . 'mustverify', 'mustverify', 'qtype_stack');

        $mform->addElement('selectyesno', $inputname . 'showvalidation',
                get_string('showvalidation', 'qtype_stack'));
        $mform->setDefault($inputname . 'showvalidation', true);
        $mform->addHelpButton($inputname . 'showvalidation', 'showvalidation', 'qtype_stack');
    }

    /**
     * Add the form elements defining one PRT.
     * @param string $prtname the name of the PRT.
     * @param MoodleQuickForm $mform the form being assembled.
     */
    protected function definition_prt($prtname, MoodleQuickForm $mform) {

        $mform->addElement('header', $prtname . 'header', get_string('prtheading', 'qtype_stack', $prtname));

        $mform->addElement('text', $prtname . 'value', get_string('questionvalue', 'qtype_stack'), array('size' => 3));
        $mform->setDefault($prtname . 'value', 1);

        $mform->addElement('selectyesno', $prtname . 'autosimplify',
                get_string('autosimplify', 'qtype_stack'));
        $mform->setDefault($prtname . 'autosimplify', true);
        $mform->addHelpButton($prtname . 'autosimplify', 'autosimplify', 'qtype_stack');

        $mform->addElement('textarea', $prtname . 'feedbackvariables',
                get_string('feedbackvariables', 'qtype_stack'), array('rows' => 3, 'cols' => 80));
        $mform->addHelpButton($prtname . 'feedbackvariables', 'feedbackvariables', 'qtype_stack');

        $inputnames = implode(', ', $this->get_inputs_used_by_prt($prtname));
        $mform->addElement('static', $prtname . 'inputsnote', '',
                get_string('prtwillbecomeactivewhen', 'qtype_stack', html_writer::tag('b', $inputnames)));

        // Create the section of the form for each node - general bits.
        $nodes = $this->get_required_nodes_for_prt($prtname);

        $nextnodechoices = array('-1' => get_string('stop', 'qtype_stack'));
        foreach ($nodes as $nodekey) {
            $nextnodechoices[$nodekey] = get_string('nodex', 'qtype_stack', $nodekey + 1);
        }

        $deletable = count($nodes) > 1;

        foreach ($nodes as $nodekey) {
            $this->definition_prt_node($prtname, $nodekey, $nextnodechoices, $deletable, $mform);
        }

        $mform->addElement('submit', $prtname . 'nodeadd', get_string('addanothernode', 'qtype_stack'));
        $mform->registerNoSubmitButton($prtname . 'nodeadd');
    }

    /**
     * Add the form elements defining one PRT node.
     * @param string $prtname the name of the PRT.
     * @param string $nodekey the name of the node.
     * @param array $nextnodechoices the available choices for the next node.
     * @param bool $deletable whether the user is allowed to delete this node.
     * @param MoodleQuickForm $mform the form being assembled.
     */
    protected function definition_prt_node($prtname, $nodekey, $nextnodechoices, $deletable, MoodleQuickForm $mform) {
        $name = $nodekey + 1;

        unset($nextnodechoices[$nodekey]);

        $nodegroup = array();
        $nodegroup[] = $mform->createElement('select', $prtname . 'answertest[' . $nodekey . ']',
                get_string('answertest', 'qtype_stack'), $this->answertestchoices);

        $nodegroup[] = $mform->createElement('text', $prtname . 'sans[' . $nodekey . ']',
                get_string('sans', 'qtype_stack'), array('size' => 5));

        $nodegroup[] = $mform->createElement('text', $prtname . 'tans[' . $nodekey . ']',
                get_string('tans', 'qtype_stack'), array('size' => 5));

        $nodegroup[] = $mform->createElement('text', $prtname . 'testoptions[' . $nodekey . ']',
                get_string('testoptions', 'qtype_stack'), array('size' => 5));

        $nodegroup[] = $mform->createElement('selectyesno', $prtname . 'quiet[' . $nodekey . ']',
                get_string('quiet', 'qtype_stack'));

        $mform->addGroup($nodegroup, $prtname . 'node[' . $nodekey . ']',
                html_writer::tag('b', get_string('nodex', 'qtype_stack', $name)),
                null, false);
        $mform->addHelpButton($prtname . 'node[' . $nodekey . ']', 'nodehelp', 'qtype_stack');

        // Create the section of the form for each node - the branches.
        foreach (array('true', 'false') as $branch) {
            $branchgroup = array();

            $branchgroup[] = $mform->createElement('select', $prtname . $branch . 'scoremode[' . $nodekey . ']',
                    get_string('scoremode', 'qtype_stack'), $this->scoremodechoices);

            $branchgroup[] = $mform->createElement('text', $prtname . $branch . 'score[' . $nodekey . ']',
                    get_string('score', 'qtype_stack'), array('size' => 2));
            $mform->setDefault($prtname . $branch . 'score[' . $nodekey . ']', (float) $branch);

            $branchgroup[] = $mform->createElement('text', $prtname . $branch . 'penalty[' . $nodekey . ']',
                    get_string('penalty', 'qtype_stack'), array('size' => 2));

            $branchgroup[] = $mform->createElement('select', $prtname . $branch . 'nextnode[' . $nodekey . ']',
                    get_string('next', 'qtype_stack'), $nextnodechoices);

            $branchgroup[] = $mform->createElement('text', $prtname . $branch . 'answernote[' . $nodekey . ']',
                    get_string('answernote', 'qtype_stack'), array('size' => 10));
            $mform->setDefault($prtname . $branch . 'answernote[' . $nodekey . ']',
                    get_string('answernotedefault' . $branch, 'qtype_stack', array('prtname' => $prtname, 'nodename' => $name)));

            $mform->addGroup($branchgroup, $prtname . 'nodewhen' . $branch . '[' . $nodekey . ']',
                    get_string('nodexwhen' . $branch, 'qtype_stack', $name), null, false);
            $mform->addHelpButton($prtname . 'nodewhen' . $branch . '[' . $nodekey . ']', $branch . 'branch', 'qtype_stack');

            $mform->addElement('editor', $prtname . $branch . 'feedback[' . $nodekey . ']',
                    get_string('nodex' . $branch . 'feedback', 'qtype_stack', $name), array('rows' => 1), $this->editoroptions);
            $mform->addHelpButton($prtname . $branch . 'feedback[' . $nodekey . ']', 'branchfeedback', 'qtype_stack');
        }

        if ($deletable) {
            $mform->addElement('submit', $prtname . 'nodedelete' . $nodekey, get_string('nodexdelete', 'qtype_stack', $name));
            $mform->registerNoSubmitButton($prtname . 'nodedelete' . $nodekey);
        }
    }

    public function data_preprocessing($question) {
        $question = parent::data_preprocessing($question);
        $question = $this->data_preprocessing_options($question);
        $question = $this->data_preprocessing_inputs($question);
        $question = $this->data_preprocessing_prts($question);

        if (empty($question->questiontext['text'])) {
            // Nasty hack to override what the base class does. The way it
            // prepares the questiontext field overwrites the default.
            $question->questiontext['text'] = self::DEFAULT_QUESTION_TEXT;
        }

        return $question;
    }

    /**
     * Do the bit of {@link data_preprocessing()} for the data in the qtype_stack table.
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
            $question->{$inputname . 'forbidfloat'}        = $input->forbidfloat;
            $question->{$inputname . 'requirelowestterms'} = $input->requirelowestterms;
            $question->{$inputname . 'checkanswertype'}    = $input->checkanswertype;
            $question->{$inputname . 'mustverify'}         = $input->mustverify;
            $question->{$inputname . 'showvalidation'}     = $input->showvalidation;
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

        // 1) Validate all the fixed question fields.
        $questionvars = new stack_cas_keyval($fromform['questionvariables'], null, null, 't');
        if (!$questionvars->get_valid()) {
            $errors['questionvariables'] = $questionvars->get_errors();
        }

        // Question text.
        $errors['questiontext'] = array();

        $questiontext = new stack_cas_text($fromform['questiontext']['text'], null, null, 't');
        if (!$questiontext->get_valid()) {
            $errors['questiontext'][] = $questiontext->get_errors();
        }

        foreach ($inputs as $inputname => $counts) {
            list($numinputs, $numvalidations) = $counts;

            if ($numinputs == 0) {
                $errors['questiontext'][] = get_string('questiontextmustcontain', 'qtype_stack', '[[input:' . $inputname . ']]');
            } else if ($numinputs > 1) {
                $errors['questiontext'][] = get_string('questiontextonlycontain', 'qtype_stack', '[[input:' . $inputname . ']]');
            }

            if ($numvalidations == 0) {
                $errors['questiontext'][] = get_string('questiontextmustcontain', 'qtype_stack', '[[validation:' . $inputname . ']]');
            } else if ($numvalidations > 1) {
                $errors['questiontext'][] = get_string('questiontextonlycontain', 'qtype_stack', '[[validation:' . $inputname . ']]');
            }
        }

        if ($errors['questiontext']) {
            $errors['questiontext'] = implode(' ', $errors['questiontext']);
        } else {
            unset($errors['questiontext']);
        }

        // Penalty.
        $penalty = $fromform['penalty'];
        if (!is_numeric($penalty) || $penalty < 0 || $penalty > 1) {
            $errors['penalty'] = get_string('penaltyerror', 'qtype_stack');
        }

        // Specific feedback.
        $errors['specificfeedback'] = array();

        $specificfeedback = new stack_cas_text($fromform['specificfeedback']['text'], null, null, 't');
        if (!$specificfeedback->get_valid()) {
            $errors['specificfeedback'][] = $specificfeedback->get_errors();
        }

        $errors['specificfeedback'] += $this->check_no_placeholders(
                    get_string('specificfeedback', 'qtype_stack'), $fromform['specificfeedback']['text'],
                    array('input', 'validation'));

        foreach ($prts as $prtname => $count) {
            if ($count > 1) {
                $errors['specificfeedback'][] = get_string('questiontextfeedbackonlycontain', 'qtype_stack', '[[feedback:' . $prtname . ']]');
            }
        }

        if ($errors['specificfeedback']) {
            $errors['specificfeedback'] = implode(' ', $errors['specificfeedback']);
        } else {
            unset($errors['specificfeedback']);
        }

        // General feedback.
        $errors['generalfeedback'] = array();

        $generalfeedback = new stack_cas_text($fromform['generalfeedback']['text'], null, null, 't');
        if (!$generalfeedback->get_valid()) {
            $errors['generalfeedback'][] = $generalfeedback->get_errors();
        }

        $errors['generalfeedback'] += $this->check_no_placeholders(
                    get_string('generalfeedback', 'question'), $fromform['generalfeedback']['text']);

        if ($errors['generalfeedback']) {
            $errors['generalfeedback'] = implode(' ', $errors['generalfeedback']);
        } else {
            unset($errors['generalfeedback']);
        }

        // Question note.
        $errors['questionnote'] = array();

        if ('' == $fromform['questionnote']) {
            if (!(false === strpos($fromform['questionvariables'], 'rand'))) {
                $errors['questionnote'] = get_string('questionnotempty', 'qtype_stack');
            }
        } else {
            $questionnote = new stack_cas_text($fromform['questionnote'], null, null, 't');
            if (!$questionnote->get_valid()) {
                $errors['questionnote'] = $questionnote->get_errors();
            }
        }

        $errors['questionnote'] += $this->check_no_placeholders(
                    get_string('questionnote', 'qtype_stack'), $fromform['questionnote']);

        if ($errors['questionnote']) {
            $errors['questionnote'] = implode(' ', $errors['questionnote']);
        } else {
            unset($errors['questionnote']);
        }

        // 2) Validate all inputs.
        foreach ($inputs as $inputname => $notused) {
            if (strlen($fromform[$inputname . 'modelans']) > 255) {
                $errors[$inputname . 'modelans'] = get_string('strlengtherror', 'qtype_stack');
            } else {
                $teacheranswer = new stack_cas_casstring($fromform[$inputname . 'modelans']);
                if (!$teacheranswer->get_valid('t')) {
                    $errors[$inputname . 'modelans'] = $teacheranswer->get_errors();
                }
            }
        }

        // 3) Validate all prts.
        foreach ($prts as $prtname => $notused) {
            $errors = $this->validation_prt($errors, $fromform, $files, $prtname);
        }

        return $errors;
    }

    /**
     * Validate the fields for a given PRT
     * @param array $errors the error so far. This array is added to and returned.
     * @param array $fromform the submitted data to validate.
     * @param array $files the submitted files to validate.
     * @param string $prtname the name of the PRT to validate.
     * @return array the update $errors array.
     */
    protected function validation_prt($errors, $fromform, $files, $prtname) {

        if (!array_key_exists($prtname.'feedbackvariables', $fromform)) {
            // This happens when you edit the question text to add more PRTs.
            // There is nothing to validate for the new PRTs, so stop now.
            return $errors;
        }

        $interror = array();

        $feedbackvars = new stack_cas_keyval($fromform[$prtname.'feedbackvariables'], null, null, 't');

        if (!$feedbackvars->get_valid()) {
            $interror[$prtname.'feedbackvariables'][] = $feedbackvars->get_errors();
        }

        if ($fromform[$prtname.'value'] <= 0) {
            $interror[$prtname.'value'][] = get_string('questionvaluepostive', 'qtype_stack');
        }

        foreach ($fromform[$prtname.'sans'] as $key => $sans) {
            if ('' == $sans) {
                    $interror[$prtname . 'node[' . $key . ']'][] = get_string('sansrequired', 'qtype_stack');
            } else {
                if (strlen($sans > 255)) {
                    $interror[$prtname . 'node[' . $key . ']'][] = get_string('sansinvalid', 'qtype_stack', get_string('strlengtherror', 'qtype_stack'));
                } else {
                    $cs= new stack_cas_casstring($sans);
                    if (!$cs->get_valid('t')) {
                        $interror[$prtname . 'node[' . $key . ']'][] =
                                get_string('sansinvalid', 'qtype_stack', $cs->get_errors());
                    }
                }
            }
        }

        foreach ($fromform[$prtname.'tans'] as $key => $tans) {
            if ('' == $tans) {
                    $interror[$prtname . 'node[' . $key . ']'][] = get_string('tansrequired', 'qtype_stack');
            } else {
                if (strlen($tans > 255)) {
                    $interror[$prtname . 'node[' . $key . ']'][] = get_string('tansinvalid', 'qtype_stack', get_string('strlengtherror', 'qtype_stack'));
                } else {
                    $cs= new stack_cas_casstring($tans);
                    if (!$cs->get_valid('t')) {
                        $interror[$prtname . 'node[' . $key . ']'][] =
                                get_string('tansinvalid', 'qtype_stack', $cs->get_errors());
                    }
                }
            }
        }

        foreach ($fromform[$prtname.'testoptions'] as $key => $opt) {
            $answertest = new stack_ans_test_controller($fromform[$prtname . 'answertest'][$key]);
            if ($answertest->required_atoptions()) {
                if ('' === trim($opt)) {
                    $interror[$prtname . 'node[' . $key . ']'][] = get_string('testoptionsrequired', 'qtype_stack');
                } else {
                    if (strlen($opt > 255)) {
                        $interror[$prtname . 'node[' . $key . ']'][] = get_string('testoptionsinvalid', 'qtype_stack', get_string('strlengtherror', 'qtype_stack'));
                    } else {
                        list($validity, $errs) = $answertest->validate_atoptions($opt);
                        if (!$validity) {
                            $interror[$prtname . 'node[' . $key . ']'][] =
                                get_string('testoptionsinvalid', 'qtype_stack', $errs);
                        }
                    }
                }
            }
        }

        $nextnodes = array();
        foreach (array('true', 'false') as $branch) {
            foreach ($fromform[$prtname.$branch.'score'] as $key => $score) {
                if (!is_numeric($score) || $score<0 || $score>1) {
                     $interror[$prtname.'nodewhen'.$branch.'['.$key.']'][] = get_string('scoreerror', 'qtype_stack');
                }
            }
            foreach ($fromform[$prtname.$branch.'penalty'] as $key => $penalty) {
                if ('' != $penalty) {
                    if (!is_numeric($penalty) || $penalty<0 || $penalty>1) {
                        $interror[$prtname.'nodewhen'.$branch.'['.$key.']'][] = get_string('penaltyerror2', 'qtype_stack');
                    }
                }
            }
            foreach ($fromform[$prtname.$branch.'answernote'] as $key => $strin) {
                if ('' == $strin) {
                    $interror[$prtname.'nodewhen'.$branch.'['.$key.']'][] = get_string('answernoterequired', 'qtype_stack');
                } else if (strstr($strin, '|') !== false) {
                    $nodename = $key+1;
                    $interror[$prtname.'nodewhen'.$branch.'['.$key.']'][] = get_string('answernote_err', 'qtype_stack');
                }
            }
            foreach ($fromform[$prtname.$branch.'feedback'] as $key => $strin) {
                $feedback = new stack_cas_text($strin['text'], null, null, 't');
                if (!$feedback->get_valid()) {
                    $nodename = $key+1;
                    $interror[$prtname . $branch . 'feedback['.$key.']'][] = $feedback->get_errors();
                }
            }

            foreach ($fromform[$prtname.$branch.'nextnode'] as $key => $next) {
                if (!array_key_exists($key, $nextnodes)) {
                    $nextnodes[$key] = array();
                }
                if ($next == -1) {
                    continue;
                }
                if ($next == $key) {
                    $interror[$prtname.'nodewhen'.$branch.'['.$key.']'][] = get_string('nextcannotbeself', 'qtype_stack');
                    continue;
                }
                $nextnodes[$key][] = $next;
            }
        }

        $nodes = $this->get_required_nodes_for_prt($prtname);
        $firstnode = reset($nodes);

        list($problem, $details) = stack_acyclic_graph_checker::check_graph($nextnodes, $firstnode);
        switch ($problem) {
            case 'disconnected':
                foreach ($details as $unusednode) {
                    $interror[$prtname . 'node[' . $key . ']'][] = get_string('nodenotused', 'qtype_stack');
                }
                break;

            case 'backlink':
                list($from, $to) = $details;
                if ($fromform[$prtname.'truenextnode'][$from] == $to) {
                    $interror[$prtname.'nodewhentrue['.$from.']'][] = get_string('nodeloopdetected', 'qtype_stack', $to + 1);
                } else {
                    $interror[$prtname.'nodewhenfalse['.$from.']'][] = get_string('nodeloopdetected', 'qtype_stack', $to + 1);
                }
                break;
        }

        foreach ($interror as $field => $messages) {
            $errors[$field] = implode(' ', $messages);
        }

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
                $problems[] = get_string('fieldshouldnotcontainplaceholder', 'qtype_stack', array('field' => $fieldname, 'type' => $placeholder));
            }
        }
        return $problems;
    }

    public function qtype() {
        return 'stack';
    }
}

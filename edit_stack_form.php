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
require_once($CFG->dirroot . '/question/type/stack/questiontype.php');


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

    /** @var options the STACK configuration settings. */
    protected $stackconfig = null;

    /** @var string caches the result of {@link get_current_question_text()}. */
    protected $questiontext = null;

    /** @var string caches the result of {@link get_current_specific_feedback()}. */
    protected $specificfeedback = null;

    /** @var array the set of choices used for the type of all inputs. */
    protected $typechoices;

    /** @var array the set of choices used for the type of all answer tests. */
    protected $answertestchoices;

    /** @var array the set of choices used for the score mode of all PRT branches. */
    protected $scoremodechoices;

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
            foreach ($question->prts as $prt) {
                if (!empty($prt->nodes)) {
                    foreach ($prt->nodes as $node) {
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
        $default     = 'AlgEquiv';
        $defaultstr  = stack_string($answertests[$default]);
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

        $qtype = new qtype_stack();
        // Note, the following methods have side-effects in $qtype stack, setting up internal data.
        $inputnames = $qtype->get_input_names_from_question_text($this->get_current_question_text());
        $prtnames = $qtype->get_prt_names_from_question($this->get_current_question_text(),
                $this->get_current_specific_feedback());

        // Note that for the editor elements, we are using $mform->getElement('prtincorrect')->setValue(...); instead
        // of setDefault, because setDefault does not work for editors.

        $mform->addHelpButton('questiontext', 'questiontext', 'qtype_stack');
        $mform->addRule('questiontext', stack_string('questiontextnonempty'), 'required', '', 'client');

        $sv = $mform->createElement('hidden', 'stackversion', get_config('qtype_stack', 'version'));
        $mform->insertElementBefore($sv, 'questiontext');
        $mform->setType('stackversion', PARAM_RAW);

        $qvars = $mform->createElement('textarea', 'questionvariables',
                stack_string('questionvariables'), array('rows' => 5, 'cols' => 80));
        $mform->insertElementBefore($qvars, 'questiontext');
        $mform->addHelpButton('questionvariables', 'questionvariables', 'qtype_stack');

        if (isset($this->question->id)) {
            $out = stack_string('runquestiontests');
            if (empty($this->question->deployedseeds) &&
                    qtype_stack_question::random_variants_check($this->question->options->questionvariables)) {
                $out = stack_string('questionnotdeployedyet');
            }
            $qtestlink = html_writer::link($qtype->get_question_test_url($this->question),
                    $out, array('target' => '_blank'));
            $qtlink = $mform->createElement('static', 'runquestiontests', '', $qtestlink);
            $mform->insertElementBefore($qtlink, 'questionvariables');
            $mform->addHelpButton('runquestiontests', 'runquestiontests', 'qtype_stack');
        }

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
            // Create the section of the form for each node - general bits.
            $inputnames = $qtype->get_inputs_used_by_prt($prtname, $this->question);
            $graph = $qtype->get_prt_graph($prtname, $this->question);
            $this->definition_prt($prtname, $mform, $count, $graph, $inputnames);
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

        $mform->addElement('selectyesno', 'assumereal',
                stack_string('assumereal'));
        $mform->setDefault('assumereal', $this->stackconfig->assumereal);
        $mform->addHelpButton('assumereal', 'assumereal', 'qtype_stack');

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

        $mform->addElement('select', 'logicsymbol',
                stack_string('logicsymbol'), stack_options::get_logic_options());
        $mform->setDefault('logicsymbol', $this->stackconfig->logicsymbol);
        $mform->addHelpButton('logicsymbol', 'logicsymbol', 'qtype_stack');

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

        $qtype = new qtype_stack();
        if ($counts[$qtype::INPUTS] == 0 && $counts[$qtype::VALIDATIONS] == 0) {
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

        $mform->addElement('select', $inputname . 'insertstars',
                stack_string('insertstars'), stack_options::get_insert_star_options());
        $mform->setDefault($inputname . 'insertstars', $this->stackconfig->inputinsertstars);
        $mform->addHelpButton($inputname . 'insertstars', 'insertstars', 'qtype_stack');

        $mform->addElement('text', $inputname . 'syntaxhint', stack_string('syntaxhint'), array('size' => 20));
        $mform->setType($inputname . 'syntaxhint', PARAM_RAW);
        $mform->addHelpButton($inputname . 'syntaxhint', 'syntaxhint', 'qtype_stack');

        $mform->addElement('select', $inputname . 'syntaxattribute',
                stack_string('syntaxattribute'), stack_options::get_syntax_attribute_options());
        $mform->setDefault($inputname . 'syntaxattribute', '0');
        $mform->addHelpButton($inputname . 'syntaxattribute', 'syntaxattribute', 'qtype_stack');

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
    protected function definition_prt($prtname, MoodleQuickForm $mform, $count, $graph, $inputnames) {

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

        $mform->addElement('select', $prtname . 'feedbackstyle',
                stack_string('feedbackstyle'), stack_potentialresponse_tree::get_feedbackstyle_options());
        $mform->setDefault($prtname . 'feedbackstyle', $this->stackconfig->feedbackstyle);
        $mform->addHelpButton($prtname . 'feedbackstyle', 'feedbackstyle', 'qtype_stack');

        $mform->addElement('textarea', $prtname . 'feedbackvariables',
                stack_string('feedbackvariables'), array('rows' => 3, 'cols' => 80));
        $mform->addHelpButton($prtname . 'feedbackvariables', 'feedbackvariables', 'qtype_stack');

        $inputnames = implode(', ', $inputnames);
        $mform->addElement('static', $prtname . 'inputsnote', '',
                stack_string('prtwillbecomeactivewhen', html_writer::tag('b', $inputnames)));

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
        $question->logicsymbol           = $opt->logicsymbol;
        $question->matrixparens          = $opt->matrixparens;
        $question->sqrtsign              = $opt->sqrtsign;
        $question->questionsimplify      = $opt->questionsimplify;
        $question->assumepositive        = $opt->assumepositive;
        $question->assumereal            = $opt->assumereal;

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
            // TODO: remove this when we delete it from the DB.
            $question->{$inputname . 'strictsyntax'}       = true;
            $question->{$inputname . 'insertstars'}        = $input->insertstars;
            $question->{$inputname . 'syntaxhint'}         = $input->syntaxhint;
            $question->{$inputname . 'syntaxattribute'}    = $input->syntaxattribute;
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
            $question->{$prtname . 'feedbackstyle'}     = (int) $prt->feedbackstyle;
            $question->{$prtname . 'feedbackvariables'} = $prt->feedbackvariables;

            foreach ($prt->nodes as $node) {
                $question = $this->data_preprocessing_node($question, $prtname, $node);
            }

            // Sort out deleting nodes via the Moodle form.

            // If the form has been submitted and is being redisplayed, and this is
            // an existing PRT, base things on the submitted data.
            $submitted = optional_param_array($prtname . 'truenextnode', null, PARAM_RAW);
            if ($submitted) {
                foreach ($submitted as $key => $truenextnode) {
                    if (optional_param($prtname . 'nodedelete' . $key, false, PARAM_BOOL)) {
                        $this->_form->registerNoSubmitButton($prtname . 'nodedelete' . $key);
                    }
                }
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

        $qtype = new qtype_stack();
        list($errors, $warnings) = $qtype->validate_fromform($fromform, $errors);

        return $errors;
    }

    public function qtype() {
        return 'stack';
    }
}

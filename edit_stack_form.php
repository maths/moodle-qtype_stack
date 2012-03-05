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


/**
 * Stack question editing form definition.
 *
 * @copyright  2012 The Open University.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_stack_edit_form extends question_edit_form {

    protected function definition_inner(MoodleQuickForm $mform) {

        // Note that for the editor elements, we are using
        // $mform->getElement('prtincorrect')->setValue(...);
        // instead of setDefault, because setDefault does not work for editors.
        $mform->getElement('questiontext')->setValue(array('text' => '<p>[[input:ans1]]</p><div>[[validation:ans1]]</div>'));

        $qvars = $mform->createElement('textarea', 'questionvariables',
                get_string('questionvariables', 'qtype_stack'), array('rows' => 5, 'cols' => 80));
        $mform->insertElementBefore($qvars, 'questiontext');

        $sf = $mform->createElement('editor', 'specificfeedback',
                get_string('specificfeedback', 'question'), array('rows' => 10), $this->editoroptions);
        $mform->insertElementBefore($sf, 'generalfeedback');
        $mform->getElement('specificfeedback')->setValue(array('text' => '[[feedback:prt1]]'));

        $mform->addElement('textarea', 'questionnote',
                get_string('questionnote', 'qtype_stack'), array('rows' => 2, 'cols' => 80));

        // Inputs - for now, hard-code it to one input.
        $types = stack_input_factory::get_available_types();
        $typechoices = array();
        foreach ($types as $type => $notused) {
            $typechoices[$type] = get_string('inputtype' . $type, 'qtype_stack');
        }

        $inputname = 'ans1';
        $mform->addElement('header', 'answerhdr' . $inputname, get_string('inputheading', 'qtype_stack', $inputname));

        $mform->addElement('select', $inputname . 'type', get_string('inputtype', 'qtype_stack'), $typechoices);

        $mform->addElement('text', $inputname . 'tans', get_string('teachersanswer', 'qtype_stack'), array('size' => 20));

        $mform->addElement('text', $inputname . 'boxsize', get_string('boxsize', 'qtype_stack'), array('size' => 3));
        $mform->setDefault($inputname . 'boxsize', 15);
        $mform->setType($inputname . 'boxsize', PARAM_INT);

        $mform->addElement('selectyesno', $inputname . 'strictsyntax',
                get_string('strictsyntax', 'qtype_stack'));
        $mform->setDefault($inputname . 'strictsyntax', true);

        $mform->addElement('selectyesno', $inputname . 'insertstars',
                get_string('insertstars', 'qtype_stack'));
        $mform->setDefault($inputname . 'insertstars', false);

        $mform->addElement('text', $inputname . 'syntaxhint', get_string('syntaxhint', 'qtype_stack'), array('size' => 20));

        $mform->addElement('selectyesno', $inputname . 'forbidfloat',
                get_string('forbidfloat', 'qtype_stack'));
        $mform->setDefault($inputname . 'forbidfloat', true);

        $mform->addElement('selectyesno', $inputname . 'requirelowestterms',
                get_string('requirelowestterms', 'qtype_stack'));
        $mform->setDefault($inputname . 'requirelowestterms', false);

        $mform->addElement('selectyesno', $inputname . 'checkanswertype',
                get_string('checkanswertype', 'qtype_stack'));
        $mform->setDefault($inputname . 'checkanswertype', false);

        $mform->addElement('selectyesno', $inputname . 'showvalidation',
                get_string('showvalidation', 'qtype_stack'));
        $mform->setDefault($inputname . 'showvalidation', true);

        // PRTs
        $prtname = 'prt1';

        $answertests = stack_ans_test_controller::get_available_ans_tests();
        $answertestchoices = array();
        foreach ($answertests as $test => $string) {
            $answertestchoices[$test] = get_string($string, 'qtype_stack');
        }

        $scoremodechoices = array(
            '=' => '=',
            '+' => '+',
            '-' => '-',
            '=AT' => '=AT',
        );

        $numnodes = optional_param($prtname . 'numnodes', 1, PARAM_INT) +
                optional_param($prtname . 'addnode', 0, PARAM_BOOL);
        $nextnodechoices = array('-1' => get_string('stop', 'qtype_stack'));
        for ($i = 0; $i < $numnodes; $i += 1) {
            $nextnodechoices[$i] = get_string('nodex', 'qtype_stack', $i + 1);
        }

        $mform->addElement('header', 'answerhdr' . $prtname, get_string('prtheading', 'qtype_stack', $prtname));

        $mform->addElement('text', $prtname . 'value', get_string('questionvalue', 'qtype_stack'), array('size' => 3));

        $mform->addElement('selectyesno', $prtname . 'autosimplify',
                get_string('autosimplify', 'qtype_stack'));
        $mform->setDefault($prtname . 'autosimplify', true);

        $mform->addElement('textarea', $prtname . 'feedbackvariables',
                get_string('feedbackvariables', 'qtype_stack'), array('rows' => 3, 'cols' => 80));

        $mform->addElement('static', $prtname . 'inputsnote', '',
                get_string('prtwillbecomeactivewhen', 'qtype_stack', html_writer::tag('b', $inputname)));

        // Create the section of the form for each node - general bits.
        $repeatoptions = array();

        $elements = array();

        $nodegroup = array();
        $nodegroup[] = $mform->createElement('select', $prtname . 'answertest',
                get_string('answertest', 'qtype_stack'), $answertestchoices);
        $nodegroup[] = $mform->createElement('text', $prtname . 'sans',
                get_string('sans', 'qtype_stack'), array('size' => 5));
        $nodegroup[] = $mform->createElement('text', $prtname . 'tans',
                get_string('tans', 'qtype_stack'), array('size' => 5));
        $nodegroup[] = $mform->createElement('text', $prtname . 'testoptions',
                get_string('testoptions', 'qtype_stack'), array('size' => 5));
        $nodegroup[] = $mform->createElement('selectyesno', $prtname . 'quiet',
                get_string('quiet', 'qtype_stack'));

        $elements[] = $mform->createElement('group', $prtname . 'node',
                html_writer::tag('b', get_string('nodex', 'qtype_stack', '{no}')),
                $nodegroup, null, false);

        // Create the section of the form for each node - the branches.
        foreach (array('true', 'false') as $branch) {
            $branchgroup = array();
            $branchgroup[] = $mform->createElement('select', $prtname . $branch . 'scoremode',
                    get_string('scoremode', 'qtype_stack'), $scoremodechoices);
            $branchgroup[] = $mform->createElement('text', $prtname . $branch . 'score',
                    get_string('score', 'qtype_stack'), array('size' => 2));
            $branchgroup[] = $mform->createElement('text', $prtname . $branch . 'penalty',
                    get_string('penalty', 'qtype_stack'), array('size' => 2));
            $branchgroup[] = $mform->createElement('select', $prtname . $branch . 'next',
                    get_string('next', 'qtype_stack'), $nextnodechoices);
            $branchgroup[] = $mform->createElement('text', $prtname . $branch . 'answernote',
                    get_string('answernote', 'qtype_stack'), array('size' => 10));

            $elements[] = $mform->createElement('group', $prtname . 'nodewhen' . $branch,
                    get_string('nodexwhen' . $branch, 'qtype_stack'), $branchgroup, null, false);

            $elements[] = $mform->createElement('editor', $prtname . $branch . 'feedback',
                    get_string('nodex' . $branch . 'feedback', 'qtype_stack'), array('rows' => 1), $this->editoroptions);
        }

        $repeatoptions[$prtname . 'truescore']['default'] = 1;
        $repeatoptions[$prtname . 'falsescore']['default'] = 0;
        $repeatoptions[$prtname . 'trueanswernote']['default'] = $prtname . '-{no}-T';
        $repeatoptions[$prtname . 'falseanswernote']['default'] = $prtname . '-{no}-F';

        $this->repeat_elements($elements, 1, $repeatoptions, $prtname . 'numnodes',
                $prtname . 'addnode', 1, get_string('addanothernode', 'qtype_stack'), true);

        // Options
        $mform->addElement('header', 'optionsheader', get_string('options', 'qtype_stack'));

        $mform->addElement('selectyesno', 'simplify',
                get_string('questionsimplify', 'qtype_stack'));

        $mform->addElement('selectyesno', 'assumepos',
                get_string('assumepos', 'qtype_stack'));

        $mform->addElement('select', 'multiplicationsign',
                get_string('multiplicationsign', 'qtype_stack'), array(
                    qtype_stack_question::MARK_MODE_PENALTY => get_string('markmodepenalty', 'qtype_stack'),
                    qtype_stack_question::MARK_MODE_FIRST   => get_string('markmodefirst', 'qtype_stack'),
                    qtype_stack_question::MARK_MODE_LAST    => get_string('markmodelast', 'qtype_stack')));

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

        $mform->addElement('selectyesno', 'sqrtsign',
                get_string('sqrtsign', 'qtype_stack'));

        $mform->addElement('select', 'complexno',
                get_string('complexno', 'qtype_stack'), array(
                    'i' => 'i', 'j' => 'j', 'symi' => 'symi', 'symj' => 'symj'));

        // Question tests.

        // To stop Moodle compaining.
        $mform->addElement('hidden', 'penalty', 0);
    }

    public function data_preprocessing($question) {
        $question = parent::data_preprocessing($question);

        return $question;
    }

    public function validation($fromform, $files) {
        $errors = parent::validation($fromform, $files);

        $inputplaceholder = '[[input:ans1]]';
        if (false === strpos($fromform['questiontext']['text'], $inputplaceholder)) {
            $errors['questiontext'] = get_string('questiontextmustcontain', 'qtype_stack', $inputplaceholder);
        }
        return $errors;
    }

    public function qtype() {
        return 'stack';
    }
}

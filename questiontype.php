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
 * Question type class for the Stack question type.
 *
 * @package    qtype_stack
 * @copyright  2012 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/questionlib.php');


/**
 * Stack question type class.
 *
 * @copyright  2012 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_stack extends question_type {

    public function save_question_options($fromform) {
        global $DB;
        $context = $fromform->context;

        parent::save_question_options($fromform);

        $options = $DB->get_record('qtype_stack', array('questionid' => $fromform->id));
        if (!$options) {
            $options = new stdClass();
            $options->questionid = $fromform->id;
            $options->questionvariables = '';
            $options->specificfeedback = '';
            $options->prtcorrect = '';
            $options->prtpartiallycorrect = '';
            $options->prtincorrect = '';
            $options->id = $DB->insert_record('qtype_stack', $options);
        }

        $options->questionvariables         = $fromform->questionvariables;
        $options->specificfeedback          = $this->import_or_save_files($fromform->specificfeedback,
                    $context, 'qtype_stack', 'specificfeedback', $fromform->id);
        $options->specificfeedbackformat    = $fromform->specificfeedback['format'];
        $options->questionnote              = $fromform->questionnote;
        $options->questionsimplify          = $fromform->questionsimplify;
        $options->assumepositive            = $fromform->assumepositive;
        $options->markmode                  = $fromform->markmode;
        $options->prtcorrect                = $this->import_or_save_files($fromform->prtcorrect,
                    $context, 'qtype_stack', 'prtcorrect', $fromform->id);
        $options->prtcorrectformat          = $fromform->prtcorrect['format'];
        $options->prtpartiallycorrect       = $this->import_or_save_files($fromform->prtpartiallycorrect,
                    $context, 'qtype_stack', 'prtpartiallycorrect', $fromform->id);
        $options->prtpartiallycorrectformat = $fromform->prtpartiallycorrect['format'];
        $options->prtincorrect              = $this->import_or_save_files($fromform->prtincorrect,
                    $context, 'qtype_stack', 'prtincorrect', $fromform->id);
        $options->prtincorrectformat        = $fromform->prtincorrect['format'];
        $options->multiplicationsign        = $fromform->multiplicationsign;
        $options->sqrtsign                  = $fromform->sqrtsign;
        $options->complexno                 = $fromform->complexno;
        $DB->update_record('qtype_stack', $options);

        $inputnames = array('ans1'); // TODO generalise this.
        $inputs = $DB->get_records('qtype_stack_inputs',
                array('questionid' => $fromform->id), '', 'name, id, questionid');
        foreach ($inputnames as $inputname) {
            if (array_key_exists($inputname, $inputs)) {
                $input = $inputs[$inputname];
                unset($inputs[$inputname]);
            } else {
                $input = new stdClass();
                $input->questionid = $fromform->id;
                $input->name       = $inputname;
                $input->id = $DB->insert_record('qtype_stack_inputs', $input);
            }

            $input->type               = $fromform->{$inputname . 'type'};
            $input->tans               = $fromform->{$inputname . 'tans'};
            $input->boxsize            = $fromform->{$inputname . 'boxsize'};
            $input->strictsyntax       = $fromform->{$inputname . 'strictsyntax'};
            $input->insertstars        = $fromform->{$inputname . 'insertstars'};
            $input->syntaxhint         = $fromform->{$inputname . 'syntaxhint'};
            $input->forbidfloat        = $fromform->{$inputname . 'forbidfloat'};
            $input->requirelowestterms = $fromform->{$inputname . 'requirelowestterms'};
            $input->checkanswertype    = $fromform->{$inputname . 'checkanswertype'};
            $input->showvalidation     = $fromform->{$inputname . 'showvalidation'};

            $DB->update_record('qtype_stack_inputs', $input);
        }

        if ($inputs) {
            list($test, $params) = $DB->get_in_or_equal(array_keys($inputs));
            $params[] = $fromform->id;
            $DB->delete_records_select('qtype_stack_inputs',
                    'name ' . $test . ' AND questionid = ?', $params);
        }

        $prtnames = array('prt1'); // TODO generalise this.
        $prts = $DB->get_records('qtype_stack_prts',
                array('questionid' => $fromform->id), '', 'name, id, questionid');
        foreach ($prtnames as $prtname) {
            if (array_key_exists($prtname, $prts)) {
                $prt = $prts[$prtname];
                unset($prts[$prtname]);
            } else {
                $prt = new stdClass();
                $prt->questionid        = $fromform->id;
                $prt->name              = $prtname;
                $prt->feedbackvariables = '';
                $prt->id = $DB->insert_record('qtype_stack_prts', $prt);
            }

            $prt->value             = $fromform->{$prtname . 'value'};
            $prt->autosimplify      = $fromform->{$prtname . 'autosimplify'};
            $prt->feedbackvariables = $fromform->{$prtname . 'feedbackvariables'};
            $DB->update_record('qtype_stack_prts', $prt);

            $nodes = $DB->get_records('qtype_stack_prt_nodes',
                    array('questionid' => $fromform->id, 'prtname' => $prtname),
                    '', 'nodename, id, questionid, prtname');

            foreach ($fromform->{$prtname . 'answertest'} as $nodename => $notused) {
                if (array_key_exists($nodename, $nodes)) {
                    $node = $nodes[$nodename];
                    unset($nodes[$nodename]);
                } else {
                    $node = new stdClass();
                    $node->questionid    = $fromform->id;
                    $node->prtname       = $prtname;
                    $node->nodename      = $nodename;
                    $node->truefeedback  = '';
                    $node->falsefeedback = '';
                    $node->id = $DB->insert_record('qtype_stack_prt_nodes', $node);
                }

                $node->answertest          = $fromform->{$prtname . 'answertest'}[$nodename];
                $node->sans                = $fromform->{$prtname . 'sans'}[$nodename];
                $node->tans                = $fromform->{$prtname . 'tans'}[$nodename];
                $node->testoptions         = $fromform->{$prtname . 'testoptions'}[$nodename];
                $node->quiet               = $fromform->{$prtname . 'quiet'}[$nodename];
                $node->truescoremode       = $fromform->{$prtname . 'truescoremode'}[$nodename];
                $node->truescore           = $fromform->{$prtname . 'truescore'}[$nodename];
                $node->truepenalty         = $fromform->{$prtname . 'truepenalty'}[$nodename];
                $node->truenextnode        = $fromform->{$prtname . 'truenextnode'}[$nodename];
                $node->trueanswernote      = $fromform->{$prtname . 'trueanswernote'}[$nodename];
                $node->truefeedback        = $this->import_or_save_files(
                                $fromform->{$prtname . 'truefeedback'}[$nodename],
                                $context, 'qtype_stack', 'prtnodetruefeedback', $node->id);
                $node->truefeedbackformat  = $fromform->{$prtname . 'truefeedback'}[$nodename]['format'];
                $node->falsescoremode      = $fromform->{$prtname . 'falsescoremode'}[$nodename];
                $node->falsescore          = $fromform->{$prtname . 'falsescore'}[$nodename];
                $node->falsepenalty        = $fromform->{$prtname . 'falsepenalty'}[$nodename];
                $node->falsenextnode       = $fromform->{$prtname . 'falsenextnode'}[$nodename];
                $node->falseanswernote     = $fromform->{$prtname . 'falseanswernote'}[$nodename];
                $node->falsefeedback        = $this->import_or_save_files(
                                $fromform->{$prtname . 'falsefeedback'}[$nodename],
                                $context, 'qtype_stack', 'prtnodefalsefeedback', $node->id);
                $node->falsefeedbackformat  = $fromform->{$prtname . 'falsefeedback'}[$nodename]['format'];

                $DB->update_record('qtype_stack_prt_nodes', $node);
            }

            if ($nodes) {
                list($test, $params) = $DB->get_in_or_equal(array_keys($nodes));
                $params[] = $fromform->id;
                $params[] = $prt->name;
                $DB->delete_records_select('qtype_stack_prt_nodes',
                        'nodename ' . $test . ' AND questionid = ? AND prtname = ?', $params);
            }
        }

        if ($prts) {
            list($test, $params) = $DB->get_in_or_equal(array_keys($prts));
            $params[] = $fromform->id;
            $DB->delete_records_select('qtype_stack_prts',
                    'name ' . $test . ' AND questionid = ?', $params);
        }

    }

    public function get_question_options($question) {
        global $DB;

        parent::get_question_options($question);

        $question->options = $DB->get_record('qtype_stack',
                array('questionid' => $question->id), '*', MUST_EXIST);

        $question->inputs = $DB->get_records('qtype_stack_inputs',
                array('questionid' => $question->id), 'name',
                'name, id, questionid, type, tans, boxsize, strictsyntax, insertstars, ' .
                'syntaxhint, forbidfloat, requirelowestterms, checkanswertype, showvalidation');

        $question->prts = $DB->get_records('qtype_stack_prts',
                array('questionid' => $question->id), 'name',
                'name, id, questionid, value, autosimplify, feedbackvariables');

        $noders = $DB->get_recordset('qtype_stack_prt_nodes',
                array('questionid' => $question->id), 'prtname, nodename');
        foreach ($noders as $node) {
            $question->prts[$node->prtname]->nodes[$node->nodename] = $node;
        }
        $noders->close();

        return true;
    }

    protected function initialise_question_instance(question_definition $question, $questiondata) {
        parent::initialise_question_instance($question, $questiondata);

        $question->questionvariables         = $questiondata->options->questionvariables;
        $question->questionnote              = $questiondata->options->questionnote;
        $question->specificfeedback          = $questiondata->options->specificfeedback;
        $question->specificfeedbackformat    = $questiondata->options->specificfeedbackformat;
        $question->prtcorrect                = $questiondata->options->prtcorrect;
        $question->prtcorrectformat          = $questiondata->options->prtcorrectformat;
        $question->prtpartiallycorrect       = $questiondata->options->prtpartiallycorrect;
        $question->prtpartiallycorrectformat = $questiondata->options->prtpartiallycorrectformat;
        $question->prtincorrect              = $questiondata->options->prtincorrect;
        $question->prtincorrectformat        = $questiondata->options->prtincorrectformat;
        $question->markmode                  = $questiondata->options->markmode;

        $question->options = new stack_options();
        $question->options->set_option('multiplicationsign', $questiondata->options->multiplicationsign);
        $question->options->set_option('complexno',          $questiondata->options->complexno);
        $question->options->set_option('sqrtsign',    (bool) $questiondata->options->sqrtsign);
        $question->options->set_option('simplify',    (bool) $questiondata->options->questionsimplify);
        $question->options->set_option('assumepos',   (bool) $questiondata->options->assumepositive);

        foreach ($questiondata->inputs as $name => $inputdata) {
            $parameters = array(
                'boxWidth'     =>        $inputdata->boxsize,
                'strictSyntax' => (bool) $inputdata->strictsyntax,
                'insertStars'  => (bool) $inputdata->insertstars,
                'syntaxHint'   =>        $inputdata->syntaxhint,
                'forbidFloats' => (bool) $inputdata->forbidfloat,
                'lowestTerms'  => (bool) $inputdata->requirelowestterms,
                'sameType'     => (bool) $inputdata->checkanswertype,
                'hideFeedback' =>       !$inputdata->showvalidation,
            );
            $question->inputs[$name] = stack_input_factory::make(
                    $inputdata->type, $inputdata->name, $inputdata->tans, $parameters);
        }

        foreach ($questiondata->prts as $name => $prtdata) {
            $nodes = array();
            foreach ($prtdata->nodes as $nodedata) {
                $sans = new stack_cas_casstring($nodedata->sans);
                $sans->get_valid('t');
                $tans = new stack_cas_casstring($nodedata->tans);
                $tans->get_valid('t');

                $node = new stack_potentialresponse_node($sans, $tans,
                        $nodedata->answertest, $nodedata->testoptions, (bool) $nodedata->quiet);
                $node->add_branch(0, $nodedata->falsescoremode, $nodedata->falsescore,
                        $nodedata->falsepenalty, $nodedata->falsenextnode,
                        $nodedata->falsefeedback, $nodedata->falseanswernote);
                $node->add_branch(1, $nodedata->truescoremode, $nodedata->truescore,
                        $nodedata->truepenalty, $nodedata->truenextnode,
                        $nodedata->truefeedback, $nodedata->trueanswernote);
                // TODO true/false feedbackformat.
                $nodes[$nodedata->nodename] = $node;
            }

            // TODO $feedbackvariables, and $sans, $tans, should probably still be strings
            // here, and should be converted to CAS stuff later, only if needed.
            $feedbackvariables = new stack_cas_keyval($prtdata->feedbackvariables, null, null, 't');
            $question->prts[$name] = new stack_potentialresponse_tree($name, '',
                    (bool) $prtdata->autosimplify, $prtdata->value,
                    $feedbackvariables->get_session(), $nodes);
        }
    }

    public function delete_question($questionid, $contextid) {
        global $DB;
        $DB->delete_records('qtype_stack_prt_nodes', array('questionid' => $questionid));
        $DB->delete_records('qtype_stack_prts',      array('questionid' => $questionid));
        $DB->delete_records('qtype_stack_inputs',    array('questionid' => $questionid));
        $DB->delete_records('qtype_stack',           array('questionid' => $questionid));
        parent::delete_question($questionid, $contextid);
    }

    public function move_files($questionid, $oldcontextid, $newcontextid) {
        global $DB;
        $fs = get_file_storage();

        parent::move_files($questionid, $oldcontextid, $newcontextid);

        $fs->move_area_files_to_new_context($oldcontextid, $newcontextid,
                                            'qtype_stack', 'specificfeedback',    $questionid);
        $fs->move_area_files_to_new_context($oldcontextid, $newcontextid,
                                            'qtype_stack', 'prtcorrect',          $questionid);
        $fs->move_area_files_to_new_context($oldcontextid, $newcontextid,
                                            'qtype_stack', 'prtpartiallycorrect', $questionid);
        $fs->move_area_files_to_new_context($oldcontextid, $newcontextid,
                                            'qtype_stack', 'prtincorrect',        $questionid);

        $nodeids = $DB->get_records_menu('qtype_stack_prt_nodes', array('questionid' => $questionid), 'id', 'id,1');
        foreach ($nodeids as $nodeid => $notused) {
            $fs->move_area_files_to_new_context($oldcontextid, $newcontextid,
                                                'qtype_stack', 'prtnodetruefeedback', $nodeid);
            $fs->move_area_files_to_new_context($oldcontextid, $newcontextid,
                                                'qtype_stack', 'prtnodefalsefeedback', $nodeid);
        }
    }

    protected function delete_files($questionid, $contextid) {
        global $DB;
        $fs = get_file_storage();

        parent::delete_files($questionid, $contextid);

        $fs->delete_area_files($contextid, 'qtype_stack', 'specificfeedback',    $questionid);
        $fs->delete_area_files($contextid, 'qtype_stack', 'prtcorrect',          $questionid);
        $fs->delete_area_files($contextid, 'qtype_stack', 'prtpartiallycorrect', $questionid);
        $fs->delete_area_files($contextid, 'qtype_stack', 'prtincorrect',        $questionid);

        $nodeids = $DB->get_records_menu('qtype_stack_prt_nodes', array('questionid' => $questionid), 'id', 'id,1');
        foreach ($nodeids as $nodeid => $notused) {
            $fs->delete_area_files($oldcontextid, $newcontextid,
                                                'qtype_stack', 'prtnodetruefeedback', $nodeid);
            $fs->delete_area_files($oldcontextid, $newcontextid,
                                                'qtype_stack', 'prtnodefalsefeedback', $nodeid);
        }
    }
}

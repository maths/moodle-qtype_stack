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

/**
 * Question type class for the Stack question type.
 *
 * @package    qtype_stack
 * @copyright  2012 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/questionlib.php');
require_once(__DIR__ . '/stack/input/factory.class.php');
require_once(__DIR__ . '/stack/answertest/controller.class.php');
require_once(__DIR__ . '/stack/cas/keyval.class.php');
require_once(__DIR__ . '/stack/cas/castext.class.php');
require_once(__DIR__ . '/stack/questiontest.php');
require_once(__DIR__ . '/stack/graphlayout/graph.php');
require_once(__DIR__ . '/lang/multilang.php');

/**
 * Stack question type class.
 *
 * @copyright  2012 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_stack extends question_type {

    /** @var int array key into the results of get_input_names_from_question_text for the count of input placeholders. */
    const INPUTS = 0;
    /** @var int array key into the results of get_input_names_from_question_text for the count of validation placeholders. */
    const VALIDATIONS = 1;

    /** @var int the CAS seed using during validation. */
    protected $seed = 1;

    /** @var stack_options the CAS options using during validation. */
    protected $options;

    /**
     * @var array prt name => stack_abstract_graph caches the result of
     * {@link get_prt_graph()}.
     */
    protected $prtgraph = array();

    public function save_question($question, $fromform) {

        if (!empty($fromform->fixdollars)) {
            $this->fix_dollars_in_form_data($fromform);
        }

        $fromform->penalty = stack_utils::fix_approximate_thirds($fromform->penalty);

        return parent::save_question($question, $fromform);
    }

    /**
     * Replace any $...$ and $$...$$ delimiters in the question text from the
     * form with the recommended delimiters.
     * @param object $fromform the data from the form.
     */
    protected function fix_dollars_in_form_data($fromform) {
        $questionfields = array('questiontext', 'generalfeedback', 'specificfeedback',
                'prtcorrect', 'prtpartiallycorrect', 'prtincorrect');
        foreach ($questionfields as $field) {
            $fromform->{$field}['text'] = stack_maths::replace_dollars($fromform->{$field}['text']);
        }
        $fromform->questionnote = stack_maths::replace_dollars($fromform->questionnote);

        $prtnames = array_keys($this->get_prt_names_from_question($fromform->questiontext['text'],
                $fromform->specificfeedback['text']));
        foreach ($prtnames as $prt) {
            foreach ($fromform->{$prt . 'truefeedback'} as &$feedback) {
                $feedback['text'] = stack_maths::replace_dollars($feedback['text']);
            }

            foreach ($fromform->{$prt . 'falsefeedback'} as &$feedback) {
                $feedback['text'] = stack_maths::replace_dollars($feedback['text']);
            }
        }

        foreach ($fromform->hint as &$hint) {
            $hint['text'] = stack_maths::replace_dollars($hint['text']);
        }
    }

    public function save_question_options($fromform) {
        global $DB;
        $context = $fromform->context;

        parent::save_question_options($fromform);

        $options = $DB->get_record('qtype_stack_options', array('questionid' => $fromform->id));
        if (!$options) {
            $options = new stdClass();
            $options->questionid = $fromform->id;
            $options->stackversion = '';
            $options->questionvariables = '';
            $options->questionnote = '';
            $options->specificfeedback = '';
            $options->prtcorrect = '';
            $options->prtpartiallycorrect = '';
            $options->prtincorrect = '';
            $options->stackversion = get_config('qtype_stack', 'version');
            $options->id = $DB->insert_record('qtype_stack_options', $options);
        }

        $options->stackversion              = $fromform->stackversion;
        $options->questionvariables         = $fromform->questionvariables;
        $options->specificfeedback          = $this->import_or_save_files($fromform->specificfeedback,
                    $context, 'qtype_stack', 'specificfeedback', $fromform->id);
        $options->specificfeedbackformat    = $fromform->specificfeedback['format'];
        $options->questionnote              = $fromform->questionnote;
        $options->questionsimplify          = $fromform->questionsimplify;
        $options->assumepositive            = $fromform->assumepositive;
        $options->assumereal                = $fromform->assumereal;
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
        $options->inversetrig               = $fromform->inversetrig;
        $options->logicsymbol               = $fromform->logicsymbol;
        $options->matrixparens              = $fromform->matrixparens;
        $options->variantsselectionseed     = $fromform->variantsselectionseed;
        $DB->update_record('qtype_stack_options', $options);

        $inputnames = array_keys($this->get_input_names_from_question_text_lang($fromform->questiontext));
        $inputs = $DB->get_records('qtype_stack_inputs',
                array('questionid' => $fromform->id), '', 'name, id, questionid');
        $questionhasinputs = false;
        foreach ($inputnames as $inputname) {
            if (array_key_exists($inputname, $inputs)) {
                $input = $inputs[$inputname];
                unset($inputs[$inputname]);
            } else {
                $input = new stdClass();
                $input->questionid = $fromform->id;
                $input->name       = $inputname;
                $input->options    = '';
                $input->id = $DB->insert_record('qtype_stack_inputs', $input);
            }

            $input->type               = $fromform->{$inputname . 'type'};
            $input->tans               = $fromform->{$inputname . 'modelans'};
            $input->boxsize            = $fromform->{$inputname . 'boxsize'};
            // TODO: remove this when we remove strictsyntax from the DB.
            $input->strictsyntax       = true;
            $input->insertstars        = $fromform->{$inputname . 'insertstars'};
            $input->syntaxhint         = $fromform->{$inputname . 'syntaxhint'};
            $input->syntaxattribute    = $fromform->{$inputname . 'syntaxattribute'};
            $input->forbidwords        = $fromform->{$inputname . 'forbidwords'};
            $input->allowwords         = $fromform->{$inputname . 'allowwords'};
            $input->forbidfloat        = $fromform->{$inputname . 'forbidfloat'};
            $input->requirelowestterms = $fromform->{$inputname . 'requirelowestterms'};
            $input->checkanswertype    = $fromform->{$inputname . 'checkanswertype'};
            $input->mustverify         = $fromform->{$inputname . 'mustverify'};
            $input->showvalidation     = $fromform->{$inputname . 'showvalidation'};
            $input->options            = $fromform->{$inputname . 'options'};

            $questionhasinputs = true;
            $DB->update_record('qtype_stack_inputs', $input);
        }

        if ($inputs) {
            list($test, $params) = $DB->get_in_or_equal(array_keys($inputs));
            $params[] = $fromform->id;
            $DB->delete_records_select('qtype_stack_inputs',
                    'name ' . $test . ' AND questionid = ?', $params);
        }

        if (!$questionhasinputs) {
            // A question with no inputs is an information item.
            $DB->set_field('question', 'length', 0, array('id' => $fromform->id));
        }

        $prtnames = array_keys($this->get_prt_names_from_question($fromform->questiontext, $options->specificfeedback));

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
                $prt->firstnodename     = 0;
                $prt->id = $DB->insert_record('qtype_stack_prts', $prt);
            }

            // Find the root node of the PRT.
            // Otherwise, if an existing question is being edited, and this is an
            // existing PRT, base things on the existing question definition.
            $graph = new stack_abstract_graph();
            foreach ($fromform->{$prtname . 'answertest'} as $nodename => $notused) {
                $truenextnode  = $fromform->{$prtname . 'truenextnode'}[$nodename];
                $falsenextnode = $fromform->{$prtname . 'falsenextnode'}[$nodename];

                if ($truenextnode == -1) {
                    $left = null;
                } else {
                    $left = $truenextnode + 1;
                }
                if ($falsenextnode == -1) {
                    $right = null;
                } else {
                    $right = $falsenextnode + 1;
                }

                $graph->add_node($nodename + 1, $left, $right);
            }
            $graph->layout();
            $roots = $graph->get_roots();
            if (count($roots) != 1 || $graph->get_broken_cycles()) {
                throw new coding_exception('The PRT ' . $prtname . ' is malformed.');
            }
            reset($roots);
            $firstnode = key($roots) - 1;

            $prt->value             = $fromform->{$prtname . 'value'};
            $prt->autosimplify      = $fromform->{$prtname . 'autosimplify'};
            $prt->feedbackstyle     = $fromform->{$prtname . 'feedbackstyle'};
            $prt->feedbackvariables = $fromform->{$prtname . 'feedbackvariables'};
            $prt->firstnodename     = $firstnode;
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
                $node->truepenalty         = stack_utils::fix_approximate_thirds(
                                $fromform->{$prtname . 'truepenalty'}[$nodename]);
                $node->truenextnode        = $fromform->{$prtname . 'truenextnode'}[$nodename];
                $node->trueanswernote      = $fromform->{$prtname . 'trueanswernote'}[$nodename];
                $node->truefeedback        = $this->import_or_save_files(
                                $fromform->{$prtname . 'truefeedback'}[$nodename],
                                $context, 'qtype_stack', 'prtnodetruefeedback', $node->id);
                $node->truefeedbackformat  = $fromform->{$prtname . 'truefeedback'}[$nodename]['format'];
                $node->falsescoremode      = $fromform->{$prtname . 'falsescoremode'}[$nodename];
                $node->falsescore          = $fromform->{$prtname . 'falsescore'}[$nodename];
                $node->falsepenalty        = stack_utils::fix_approximate_thirds(
                                $fromform->{$prtname . 'falsepenalty'}[$nodename]);
                $node->falsenextnode       = $fromform->{$prtname . 'falsenextnode'}[$nodename];
                $node->falseanswernote     = $fromform->{$prtname . 'falseanswernote'}[$nodename];
                $node->falsefeedback        = $this->import_or_save_files(
                                $fromform->{$prtname . 'falsefeedback'}[$nodename],
                                $context, 'qtype_stack', 'prtnodefalsefeedback', $node->id);
                $node->falsefeedbackformat  = $fromform->{$prtname . 'falsefeedback'}[$nodename]['format'];

                if ('' === $node->truepenalty) {
                    $node->truepenalty = null;
                }
                if ('' === $node->falsepenalty) {
                    $node->falsepenalty = null;
                }

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
            $DB->delete_records_select('qtype_stack_prt_nodes',
                    'prtname ' . $test . ' AND questionid = ?', $params);
            $DB->delete_records_select('qtype_stack_prts',
                    'name ' . $test . ' AND questionid = ?', $params);
        }

        $this->save_hints($fromform);

        // This is a bit of a hack. If doing 'Duplicate' in the question bank
        // then when saving the editing form, then detect that here, and try to
        // copy the deployed variants from the original question.
        if (!isset($fromform->deployedseeds) && !empty($fromform->makecopy)) {
            $oldquestionid = optional_param('id', 0, PARAM_INT);
            if ($oldquestionid) {
                $fromform->deployedseeds = $DB->get_fieldset_sql('
                        SELECT seed
                          FROM {qtype_stack_deployed_seeds}
                         WHERE questionid = ?
                      ORDER BY id', [$oldquestionid]);;
            }
        }

        if (isset($fromform->deployedseeds)) {
            $DB->delete_records('qtype_stack_deployed_seeds', array('questionid' => $fromform->id));
            foreach ($fromform->deployedseeds as $deployedseed) {
                $record = new stdClass();
                $record->questionid = $fromform->id;
                $record->seed = $deployedseed;
                $DB->insert_record('qtype_stack_deployed_seeds', $record, false);
            }
        }

        // This is a bit of a hack. If doing 'Duplicate' in the question bank
        // then when saving the editing form, then detect that here, and try to
        // copy the question tests from the original question.
        if (!isset($fromform->testcases) && !empty($fromform->makecopy)) {
            $oldquestionid = optional_param('id', 0, PARAM_INT);
            if ($oldquestionid) {
                $fromform->testcases = $this->load_question_tests($oldquestionid);
            }
        }

        if (isset($fromform->testcases)) {
            // If the data includes the defintion of the question tests that there
            // should be (i.e. when doing import) then replace the existing set
            // of tests with the new one.
            $this->save_question_tests($fromform->id, $fromform->testcases);
        }

        // Irrespective of what else has happened, ensure there is no garbage
        // in the database, for example if we delete a PRT, remove the expected
        // values for that PRT while leaving the rest of the testcases alone.
        list($nametest, $params) = $DB->get_in_or_equal($inputnames, SQL_PARAMS_NAMED, 'input', false, null);
        $params['questionid'] = $fromform->id;
        $DB->delete_records_select('qtype_stack_qtest_inputs',
                'questionid = :questionid AND inputname ' . $nametest, $params);

        list($nametest, $params) = $DB->get_in_or_equal($prtnames, SQL_PARAMS_NAMED, 'prt', false, null);
        $params['questionid'] = $fromform->id;
        $DB->delete_records_select('qtype_stack_qtest_expected',
                'questionid = :questionid AND prtname ' . $nametest, $params);
    }

    public function get_question_options($question) {
        global $DB;

        parent::get_question_options($question);

        $question->options = $DB->get_record('qtype_stack_options',
                array('questionid' => $question->id), '*', MUST_EXIST);

        $question->inputs = $DB->get_records('qtype_stack_inputs',
                array('questionid' => $question->id), 'name',
                'name, id, questionid, type, tans, boxsize, strictsyntax, insertstars, ' .
                'syntaxhint, syntaxattribute, forbidwords, allowwords, forbidfloat, requirelowestterms, ' .
                'checkanswertype, mustverify, showvalidation, options');

        $question->prts = $DB->get_records('qtype_stack_prts',
                array('questionid' => $question->id), 'name',
                'name, id, questionid, value, autosimplify, feedbackstyle, feedbackvariables, firstnodename');

        $noders = $DB->get_recordset('qtype_stack_prt_nodes',
                array('questionid' => $question->id),
                'prtname, ' . $DB->sql_cast_char2int('nodename'));
        foreach ($noders as $node) {
            $question->prts[$node->prtname]->nodes[$node->nodename] = $node;
        }
        $noders->close();

        $question->deployedseeds = $DB->get_fieldset_sql('
                SELECT seed
                  FROM {qtype_stack_deployed_seeds}
                 WHERE questionid = ?
              ORDER BY id', array($question->id));

        return true;
    }

    protected function initialise_question_instance(question_definition $question, $questiondata) {
        parent::initialise_question_instance($question, $questiondata);

        $question->stackversion              = $questiondata->options->stackversion;
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
        $question->variantsselectionseed     = $questiondata->options->variantsselectionseed;

        $question->options = new stack_options();
        $question->options->set_option('multiplicationsign', $questiondata->options->multiplicationsign);
        $question->options->set_option('complexno',          $questiondata->options->complexno);
        $question->options->set_option('inversetrig',        $questiondata->options->inversetrig);
        $question->options->set_option('logicsymbol',        $questiondata->options->logicsymbol);
        $question->options->set_option('matrixparens',       $questiondata->options->matrixparens);
        $question->options->set_option('sqrtsign',    (bool) $questiondata->options->sqrtsign);
        $question->options->set_option('simplify',    (bool) $questiondata->options->questionsimplify);
        $question->options->set_option('assumepos',   (bool) $questiondata->options->assumepositive);
        $question->options->set_option('assumereal',  (bool) $questiondata->options->assumereal);

        $requiredparams = stack_input_factory::get_parameters_used();
        foreach (stack_utils::extract_placeholders($question->questiontext, 'input') as $name) {
            $inputdata = $questiondata->inputs[$name];
            $allparameters = array(
                'boxWidth'        => $inputdata->boxsize,
                'strictSyntax'    => true,
                'insertStars'     => (int) $inputdata->insertstars,
                'syntaxHint'      => $inputdata->syntaxhint,
                'syntaxAttribute' => $inputdata->syntaxattribute,
                'forbidWords'     => $inputdata->forbidwords,
                'allowWords'      => $inputdata->allowwords,
                'forbidFloats'    => (bool) $inputdata->forbidfloat,
                'lowestTerms'     => (bool) $inputdata->requirelowestterms,
                'sameType'        => (bool) $inputdata->checkanswertype,
                'mustVerify'      => (bool) $inputdata->mustverify,
                'showValidation'  => $inputdata->showvalidation,
                'options'         => $inputdata->options,
            );
            $parameters = array();
            foreach ($requiredparams[$inputdata->type] as $paramname) {
                if ($paramname == 'inputType') {
                    continue;
                }
                $parameters[$paramname] = $allparameters[$paramname];
            }
            $question->inputs[$name] = stack_input_factory::make(
                    $inputdata->type, $inputdata->name, $inputdata->tans, $question->options, $parameters);
        }

        $totalvalue = 0;
        $allformative = true;
        foreach ($questiondata->prts as $name => $prtdata) {
            // At this point we do not have the PRT method is_formative() available to us.
            if ($prtdata->feedbackstyle > 0) {
                $totalvalue += $prtdata->value;
                $allformative = false;
            }
        }
        if ($questiondata->prts && !$allformative && $totalvalue < 0.0000001) {
            throw new coding_exception('There is an error authoring your question. ' .
                    'The $totalvalue, the marks available for the question, must be positive in question ' .
                    $question->name);
        }

        $prtnames = array_keys($this->get_prt_names_from_question($question->questiontext, $question->specificfeedback));
        foreach ($prtnames as $name) {
            $prtdata = $questiondata->prts[$name];
            $nodes = array();
            foreach ($prtdata->nodes as $key => $nodedata) {
                $sans = stack_ast_container::make_from_teacher_source('PRSANS' . $key . ':' . $nodedata->sans,
                        '', new stack_cas_security());
                $tans = stack_ast_container::make_from_teacher_source('PRTANS' . $key . ':' . $nodedata->tans,
                        '', new stack_cas_security());

                if (is_null($nodedata->falsepenalty) || $nodedata->falsepenalty === '') {
                    $falsepenalty = $questiondata->penalty;
                } else {
                    $falsepenalty = $nodedata->falsepenalty;
                }
                if (is_null($nodedata->truepenalty) || $nodedata->truepenalty === '') {
                    $truepenalty = $questiondata->penalty;
                } else {
                    $truepenalty = $nodedata->truepenalty;
                }

                $node = new stack_potentialresponse_node($sans, $tans,
                        $nodedata->answertest, $nodedata->testoptions, (bool) $nodedata->quiet, '', $nodedata->id);
                $node->add_branch(0, $nodedata->falsescoremode, $nodedata->falsescore,
                        $falsepenalty, $nodedata->falsenextnode,
                        $nodedata->falsefeedback, $nodedata->falsefeedbackformat, $nodedata->falseanswernote);
                $node->add_branch(1, $nodedata->truescoremode, $nodedata->truescore,
                        $truepenalty, $nodedata->truenextnode,
                        $nodedata->truefeedback, $nodedata->truefeedbackformat, $nodedata->trueanswernote);
                $nodes[$nodedata->nodename] = $node;
            }

            if ($prtdata->feedbackvariables) {
                $feedbackvariables = new stack_cas_keyval($prtdata->feedbackvariables);
                $feedbackvariables = $feedbackvariables->get_session();
            } else {
                $feedbackvariables = null;
            }

            $prtvalue = 0;
            if (!$allformative) {
                $prtvalue = $prtdata->value / $totalvalue;
            }
            $question->prts[$name] = new stack_potentialresponse_tree($name, '',
                    (bool) $prtdata->autosimplify, $prtvalue,
                    $feedbackvariables, $nodes, (string) $prtdata->firstnodename, (int) $prtdata->feedbackstyle);
        }

        $question->deployedseeds = array_values($questiondata->deployedseeds);
    }

    /**
     * Get the URL params required for linking to associated scripts like
     * questiontestrun.php.
     *
     * @param stdClass|qtype_stack_question $question question data, as from question_bank::load_question
     *      or question_bank::load_question_data.
     * @return array of URL params. Can be passed to moodle_url.
     */
    protected function get_question_url_params($question) {
        $urlparams = array('questionid' => $question->id);
        if (property_exists($question, 'seed')) {
            $urlparams['seed'] = $question->seed;
        }

        // This is a bit of a hack to find the right thing to put in the URL.
        // If we are already on a URL that gives us a clue what to do, use that.
        $context = context::instance_by_id($question->contextid);
        if ($cmid = optional_param('cmid', null, PARAM_INT)) {
            $urlparams['cmid'] = $cmid;

        } else if ($courseid = optional_param('courseid', null, PARAM_INT)) {
            $urlparams['courseid'] = $courseid;

        } else if ($context->contextlevel == CONTEXT_MODULE) {
            $urlparams['cmid'] = $context->instanceid;

        } else if ($context->contextlevel == CONTEXT_COURSE) {
            $urlparams['courseid'] = $context->instanceid;

        } else {
            $urlparams['courseid'] = get_site()->id;
        }

        return $urlparams;
    }

    /**
     * Get the URL for questiontestrun.php for a question.
     *
     * @param stdClass|qtype_stack_question $question question data, as from question_bank::load_question
     *      or question_bank::load_question_data.
     * @return moodle_url the URL.
     */
    public function get_question_test_url($question) {
        $linkparams = $this->get_question_url_params($question);
        return new moodle_url('/question/type/stack/questiontestrun.php', $linkparams);
    }

    /**
     * Get the URL for tidyquestion.php for a question.
     *
     * @param stdClass|qtype_stack_question $question question data, as from question_bank::load_question
     *      or question_bank::load_question_data.
     * @return moodle_url the URL.
     */
    public function get_tidy_question_url($question) {
        $linkparams = $this->get_question_url_params($question);
        return new moodle_url('/question/type/stack/tidyquestion.php', $linkparams);
    }

    public function get_extra_question_bank_actions(stdClass $question): array {
        $actions = parent::get_extra_question_bank_actions($question);

        $linkparams = $this->get_question_url_params($question);

        // Directly link to question tests and deployed variants.
        if (question_has_capability_on($question, 'view')) {
            $actions[] = new \action_menu_link_secondary(
                    new moodle_url('/question/type/stack/questiontestrun.php', $linkparams),
                    new \pix_icon('t/approve', ''),
                    get_string('runquestiontests', 'qtype_stack'));
        }

        // Directly link to tidy question script.
        if (question_has_capability_on($question, 'view')) {
            $actions[] = new \action_menu_link_secondary(
                    new moodle_url('/question/type/stack/tidyquestion.php', $linkparams),
                    new \pix_icon('t/edit', ''),
                    get_string('tidyquestion', 'qtype_stack'));
        }

        return $actions;
    }

    public function delete_question($questionid, $contextid) {
        global $DB;
        $this->delete_question_tests($questionid);
        $DB->delete_records('qtype_stack_deployed_seeds', array('questionid' => $questionid));
        $DB->delete_records('qtype_stack_prt_nodes',      array('questionid' => $questionid));
        $DB->delete_records('qtype_stack_prts',           array('questionid' => $questionid));
        $DB->delete_records('qtype_stack_inputs',         array('questionid' => $questionid));
        $DB->delete_records('qtype_stack_options',        array('questionid' => $questionid));
        parent::delete_question($questionid, $contextid);
    }

    public function move_files($questionid, $oldcontextid, $newcontextid) {
        global $DB;
        $fs = get_file_storage();

        parent::move_files($questionid, $oldcontextid, $newcontextid);
        $this->move_files_in_hints($questionid, $oldcontextid, $newcontextid);

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
        $this->delete_files_in_hints($questionid, $contextid);

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

    /**
     * Save a set of question tests for a question, replacing any existing tests.
     * @param int $questionid the question id of the question we are manipulating the tests for.
     * @param array $testcases testcase number => stack_question_test
     */
    public function save_question_tests($questionid, $testcases) {
        global $DB;
        $transaction = $DB->start_delegated_transaction();
        $this->delete_question_tests($questionid);
        foreach ($testcases as $number => $testcase) {
            $this->save_question_test($questionid, $testcase, $number);
        }
        $transaction->allow_commit();
    }

    /**
     * Save a question tests for a question, either replacing the test at a given
     * number, or adding a new test, either with a given number, or taking the
     * first unused number.
     * @param int $questionid the question id of the question we are manipulating the tests for.
     * @param stack_question_test $qtest
     * @param int $testcases testcase number to replace/add. If not given, the first unused number is found.
     */
    public function save_question_test($questionid, stack_question_test $qtest, $testcase = null) {
        global $DB;
        $transaction = $DB->start_delegated_transaction();

        if (!$testcase || !$DB->record_exists('qtype_stack_qtests',
                array('questionid' => $questionid, 'testcase' => $testcase))) {
            // Find the first unused testcase number.
            $testcase = $DB->get_field_sql('
                        SELECT MIN(qt.testcase) + 1
                        FROM (
                            SELECT testcase FROM {qtype_stack_qtests} WHERE questionid = ?
                            UNION
                            SELECT 0
                        ) qt
                        LEFT JOIN {qtype_stack_qtests} qt2 ON qt2.questionid = ? AND
                                                              qt2.testcase = qt.testcase + 1
                        WHERE qt2.id IS NULL
                        ', array($questionid, $questionid));
            $testcasedata = new stdClass();
            $testcasedata->questionid = $questionid;
            $testcasedata->testcase = $testcase;
            $testcasedata->timemodified = time();
            $DB->insert_record('qtype_stack_qtests', $testcasedata);
        } else {
            $DB->set_field('qtype_stack_qtests', 'timemodified', time(),
                    array('questionid' => $questionid, 'testcase' => $testcase));
        }

        // Save the input data.
        $DB->delete_records('qtype_stack_qtest_inputs', array('questionid' => $questionid, 'testcase' => $testcase));
        foreach ($qtest->inputs as $name => $value) {
            $testinput = new stdClass();
            $testinput->questionid = $questionid;
            $testinput->testcase   = $testcase;
            $testinput->inputname  = $name;
            $testinput->value      = $value;
            $DB->insert_record('qtype_stack_qtest_inputs', $testinput);
        }

        // Save the expected outcome data.
        $DB->delete_records('qtype_stack_qtest_expected', array('questionid' => $questionid, 'testcase' => $testcase));
        foreach ($qtest->expectedresults as $prtname => $expectedresults) {
            $expected = new stdClass();
            $expected->questionid         = $questionid;
            $expected->testcase           = $testcase;
            $expected->prtname            = $prtname;
            if ($expectedresults->score === '' || $expectedresults->score === null) {
                $expected->expectedscore = null;
            } else {
                $expected->expectedscore = (float) $expectedresults->score;
            }
            if ($expectedresults->penalty === '' || $expectedresults->penalty === null) {
                $expected->expectedpenalty = null;
            } else {
                $expected->expectedpenalty = stack_utils::fix_approximate_thirds(
                        (float) $expectedresults->penalty);
            }
            $expected->expectedanswernote = $expectedresults->answernotes[0];
            $DB->insert_record('qtype_stack_qtest_expected', $expected);
        }

        $transaction->allow_commit();
    }

    /**
     * Deploy a variant of a question.
     * @param int $questionid the question id.
     * @param int $seed the seed to deploy.
     */
    public function deploy_variant($questionid, $seed) {
        global $DB;

        $record = new stdClass();
        $record->questionid = $questionid;
        $record->seed = $seed;
        $DB->insert_record('qtype_stack_deployed_seeds', $record);

        $this->notify_question_edited($questionid);
    }

    /**
     * Un-deploy a variant of a question.
     * @param int $questionid the question id.
     * @param int $seed the seed to un-deploy.
     */
    public function undeploy_variant($questionid, $seed) {
        global $DB;

        $DB->delete_records('qtype_stack_deployed_seeds',
                array('questionid' => $questionid, 'seed' => $seed));

        $this->notify_question_edited($questionid);
    }

    /**
     * Rename an input in the question data. It is the caller's responsibility
     * to ensure that the $to name will not violate any unique constraints.
     * @param int $questionid the question id.
     * @param string $from the input to rename.
     * @param string $to the new name to give it.
     */
    public function rename_input($questionid, $from, $to) {
        global $DB;
        $transaction = $DB->start_delegated_transaction();

        // Place-holders in the question text.
        $questiontext = $DB->get_field('question', 'questiontext', array('id' => $questionid));
        $questiontext = str_replace(array("[[input:{$from}]]", "[[validation:{$from}]]"),
                array("[[input:{$to}]]", "[[validation:{$to}]]"), $questiontext);
        $DB->set_field('question', 'questiontext', $questiontext, array('id' => $questionid));

        // Input names in question test data.
        $DB->set_field('qtype_stack_qtest_inputs', 'inputname', $to,
                array('questionid' => $questionid, 'inputname' => $from));

        // The input itself.
        $DB->set_field('qtype_stack_inputs', 'name', $to,
                array('questionid' => $questionid, 'name' => $from));

        $regex = '~\b' . preg_quote($from, '~') . '\b~';
        // Where the input name appears in expressions in PRTs.
        $prts = $DB->get_records('qtype_stack_prts', array('questionid' => $questionid),
                    'id, feedbackvariables');
        foreach ($prts as $prt) {
            $prt->feedbackvariables = preg_replace($regex, $to, $prt->feedbackvariables, -1, $changes);
            if ($changes) {
                $DB->update_record('qtype_stack_prts', $prt);
            }
        }

        // Where the input name appears in expressions in PRT node.
        $nodes = $DB->get_records('qtype_stack_prt_nodes', array('questionid' => $questionid),
                        'id, sans, tans, testoptions, truefeedback, falsefeedback');
        foreach ($nodes as $node) {
            $changes = false;
            $node->sans = preg_replace($regex, $to, $node->sans, -1, $count);
            $changes = $changes || $count;
            $node->tans = preg_replace($regex, $to, $node->tans, -1, $count);
            $changes = $changes || $count;
            $node->testoptions = preg_replace($regex, $to, $node->testoptions, -1, $count);
            $changes = $changes || $count;
            $node->truefeedback = preg_replace($regex, $to, $node->truefeedback, -1, $count);
            $changes = $changes || $count;
            $node->falsefeedback = preg_replace($regex, $to, $node->falsefeedback, -1, $count);
            $changes = $changes || $count;
            if ($changes) {
                $DB->update_record('qtype_stack_prt_nodes', $node);
            }
        }

        $transaction->allow_commit();
        $this->notify_question_edited($questionid);
    }

    /**
     * Rename a PRT in the question data. It is the caller's responsibility
     * to ensure that the $to name will not violate any unique constraints.
     * @param int $questionid the question id.
     * @param string $from the PRT to rename.
     * @param string $to the new name to give it.
     */
    public function rename_prt($questionid, $from, $to) {
        global $DB;
        $transaction = $DB->start_delegated_transaction();

        // Place-holders in the question text.
        $questiontext = $DB->get_field('question', 'questiontext', array('id' => $questionid));
        $questiontext = str_replace("[[feedback:{$from}]]", "[[feedback:{$to}]]", $questiontext);
        $DB->set_field('question', 'questiontext', $questiontext, array('id' => $questionid));

        // Place-holders in the specific feedback.
        $specificfeedback = $DB->get_field('qtype_stack_options', 'specificfeedback',
                array('questionid' => $questionid));
        $specificfeedback = str_replace("[[feedback:{$from}]]", "[[feedback:{$to}]]", $specificfeedback);
        $DB->set_field('qtype_stack_options', 'specificfeedback', $specificfeedback,
                array('questionid' => $questionid));

        // PRT names in question test data.
        $DB->set_field('qtype_stack_qtest_expected', 'prtname', $to,
                array('questionid' => $questionid, 'prtname' => $from));

        // The PRT name in its nodes.
        $DB->set_field('qtype_stack_prt_nodes', 'prtname', $to,
                array('questionid' => $questionid, 'prtname' => $from));

        // The PRT itself.
        $DB->set_field('qtype_stack_prts', 'name', $to,
                array('questionid' => $questionid, 'name' => $from));

        $transaction->allow_commit();
        $this->notify_question_edited($questionid);
    }

    /**
     * Rename a PRT node in the question data. It is the caller's responsibility
     * to ensure that the $to name will not violate any unique constraints.
     * @param int $questionid the question id.
     * @param string $prtname the PRT that the node belongs to.
     * @param string $from the input to rename.
     * @param string $to the new name to give it.
     */
    public function rename_prt_node($questionid, $prtname, $from, $to) {
        global $DB;
        $transaction = $DB->start_delegated_transaction();

        // The PRT node itself.
        $DB->set_field('qtype_stack_prt_nodes', 'nodename', $to,
                array('questionid' => $questionid, 'prtname' => $prtname, 'nodename' => $from));

        // True next node links.
        $DB->set_field('qtype_stack_prt_nodes', 'truenextnode', $to,
                array('questionid' => $questionid, 'prtname' => $prtname, 'truenextnode' => $from));

        // False next node links.
        $DB->set_field('qtype_stack_prt_nodes', 'falsenextnode', $to,
                array('questionid' => $questionid, 'prtname' => $prtname, 'falsenextnode' => $from));

        // PRT first node link.
        $DB->set_field('qtype_stack_prts', 'firstnodename', $to,
                array('questionid' => $questionid, 'name' => $prtname, 'firstnodename' => $from));

        $transaction->allow_commit();
        $this->notify_question_edited($questionid);
    }

    /**
     * From Moodle 2.4 onwards, we need to clear the entry from the question
     * cache if a question definition changes. This method deals with doing
     * that without causing errors on earlier versions of Moodle.
     * @param int $questionid the question id to clear from the cache.
     */
    protected function notify_question_edited($questionid) {
        if (method_exists('question_bank', 'notify_question_edited')) {
            call_user_func(array('question_bank', 'notify_question_edited'), $questionid);
        }
    }

    /**
     * Load all the question tests for a question.
     * @param int $questionid the id of the question to load the tests for.
     * @return array testcase number => stack_question_test
     */
    public function load_question_tests($questionid) {
        global $DB;

        $testinputdata = $DB->get_records('qtype_stack_qtest_inputs',
                array('questionid' => $questionid), 'testcase, inputname');
        $testinputs = array();
        foreach ($testinputdata as $data) {
            $testinputs[$data->testcase][$data->inputname] = $data->value;
        }

        $testcasenumbers = $DB->get_records_menu('qtype_stack_qtests',
                array('questionid' => $questionid), 'testcase', 'testcase, 1');
        $testcases = array();
        foreach ($testcasenumbers as $number => $notused) {
            if (!array_key_exists($number, $testinputs)) {
                $testinputs[$number] = array();
            }
            $testcase = new stack_question_test($testinputs[$number], $number);
            $testcases[$number] = $testcase;
        }

        $expecteddata = $DB->get_records('qtype_stack_qtest_expected',
                array('questionid' => $questionid), 'testcase, prtname');
        foreach ($expecteddata as $data) {
            $testcases[$data->testcase]->add_expected_result($data->prtname,
                    new stack_potentialresponse_tree_state(1, true,
                            $data->expectedscore, $data->expectedpenalty,
                            '', array($data->expectedanswernote)));
        }

        return $testcases;
    }

    /**
     * Load one particular question tests for a question.
     * @param int $questionid the id of the question to load the tests for.
     * @param int $testcase the testcase nubmer to load.
     * @return stack_question_test the test-case
     */
    public function load_question_test($questionid, $testcase) {
        global $DB;

        // Verify that this testcase exists.
        $DB->get_record('qtype_stack_qtests',
                array('questionid' => $questionid, 'testcase' => $testcase), '*', MUST_EXIST);

        // Load the inputs.
        $inputs = $DB->get_records_menu('qtype_stack_qtest_inputs',
                array('questionid' => $questionid, 'testcase' => $testcase),
                'inputname', 'inputname, value');
        $qtest = new stack_question_test($inputs, $testcase);

        // Load the expectations.
        $expectations = $DB->get_records('qtype_stack_qtest_expected',
                array('questionid' => $questionid, 'testcase' => $testcase), 'prtname',
                'prtname, expectedscore, expectedpenalty, expectedanswernote');
        foreach ($expectations as $prtname => $expected) {
            $qtest->add_expected_result($prtname, new stack_potentialresponse_tree_state(
                    1, true, $expected->expectedscore, $expected->expectedpenalty,
                    '', array($expected->expectedanswernote)));
        }

        return $qtest;
    }

    /**
     * Delete all the question tests for a question.
     * @param int $questionid the id of the question to load the tests for.
     */
    protected function delete_question_tests($questionid) {
        global $DB;
        $transaction = $DB->start_delegated_transaction();
        $DB->delete_records('qtype_stack_qtest_expected', array('questionid' => $questionid));
        $DB->delete_records('qtype_stack_qtest_inputs',   array('questionid' => $questionid));
        $DB->delete_records('qtype_stack_qtests',         array('questionid' => $questionid));
        $DB->delete_records('qtype_stack_qtest_results',  array('questionid' => $questionid));
        $transaction->allow_commit();
    }

    /**
     * Delete one particular question test for a question.
     * @param int $questionid the id of the question to load the tests for.
     * @param int $testcase the testcase nubmer to load.
     */
    public function delete_question_test($questionid, $testcase) {
        global $DB;
        $transaction = $DB->start_delegated_transaction();
        $DB->delete_records('qtype_stack_qtest_expected',
                array('questionid' => $questionid, 'testcase' => $testcase));
        $DB->delete_records('qtype_stack_qtest_inputs',
                array('questionid' => $questionid, 'testcase' => $testcase));
        $DB->delete_records('qtype_stack_qtests',
                array('questionid' => $questionid, 'testcase' => $testcase));
        $DB->delete_records('qtype_stack_qtest_results',
                array('questionid' => $questionid, 'testcase' => $testcase));
        $transaction->allow_commit();
    }

    public function get_possible_responses($questiondata) {
        $parts = array();

        $q = $this->make_question($questiondata);

        foreach ($q->prts as $index => $prt) {
            foreach ($prt->get_nodes_summary() as $nodeid => $choices) {
                $parts[$index . '-' . $nodeid] = array(
                    $choices->falsenote => new question_possible_response(
                            $choices->falsenote, $choices->falsescore * $prt->get_value()),
                    $choices->truenote => new question_possible_response(
                            $choices->truenote, $choices->truescore * $prt->get_value()),
                    null              => question_possible_response::no_response(),
                );
            }
        }

        return $parts;
    }

    /**
     * Helper method used by {@link export_to_xml()}.
     * @param qformat_xml $format the importer/exporter object.
     * @param string $tag the XML tag to use.
     * @param string $text the text to output.
     * @param int $textformat the text's format.
     * @param int $itemid the itemid for any files.
     * @param int $contextid the context id that the text belongs to.
     * @param string $indent the amount of indent to add at the start of the line.
     * @return string XML fragment.
     */
    protected function export_xml_text(qformat_xml $format, $tag, $text, $textformat,
            $contextid, $filearea, $itemid, $indent = '    ') {
        $fs = get_file_storage();
        $files = $fs->get_area_files($contextid, 'qtype_stack', $filearea, $itemid);

        $output = '';
        $output .= $indent . "<{$tag} {$format->format($textformat)}>\n";
        $output .= $indent . '  ' . $format->writetext($text);
        $output .= $format->write_files($files);
        $output .= $indent . "</{$tag}>\n";

        return $output;
    }

    public function export_to_xml($questiondata, qformat_xml $format, $notused = null) {
        $contextid = $questiondata->contextid;

        if (!isset($questiondata->testcases)) {
            // The method get_question_options does not load the testcases, because
            // they are not normally needed, so we have to load them manually here.
            // However, we only do it conditionally, so that the unit tests can
            // just pass the data in.
            $questiondata->testcases = $this->load_question_tests($questiondata->id);
        }

        $output = '';

        $options = $questiondata->options;
        $output .= "    <stackversion>\n";
        $output .= "      " . $format->writetext($options->stackversion, 0);
        $output .= "    </stackversion>\n";
        $output .= "    <questionvariables>\n";
        $output .= "      " . $format->writetext($options->questionvariables, 0);
        $output .= "    </questionvariables>\n";
        $output .= $this->export_xml_text($format, 'specificfeedback', $options->specificfeedback,
                        $options->specificfeedbackformat, $contextid, 'specificfeedback', $questiondata->id);
        $output .= "    <questionnote>\n";
        $output .= "      " . $format->writetext($options->questionnote, 0);
        $output .= "    </questionnote>\n";
        $output .= "    <questionsimplify>{$options->questionsimplify}</questionsimplify>\n";
        $output .= "    <assumepositive>{$options->assumepositive}</assumepositive>\n";
        $output .= "    <assumereal>{$options->assumereal}</assumereal>\n";
        $output .= $this->export_xml_text($format, 'prtcorrect', $options->prtcorrect,
                        $options->prtcorrectformat, $contextid, 'prtcorrect', $questiondata->id);
        $output .= $this->export_xml_text($format, 'prtpartiallycorrect', $options->prtpartiallycorrect,
                        $options->prtpartiallycorrectformat, $contextid, 'prtpartiallycorrect', $questiondata->id);
        $output .= $this->export_xml_text($format, 'prtincorrect', $options->prtincorrect,
                        $options->prtincorrectformat, $contextid, 'prtincorrect', $questiondata->id);
        $output .= "    <multiplicationsign>{$options->multiplicationsign}</multiplicationsign>\n";
        $output .= "    <sqrtsign>{$options->sqrtsign}</sqrtsign>\n";
        $output .= "    <complexno>{$options->complexno}</complexno>\n";
        $output .= "    <inversetrig>{$options->inversetrig}</inversetrig>\n";
        $output .= "    <logicsymbol>{$options->logicsymbol}</logicsymbol>\n";
        $output .= "    <matrixparens>{$options->matrixparens}</matrixparens>\n";
        $output .= "    <variantsselectionseed>{$format->xml_escape($options->variantsselectionseed)}</variantsselectionseed>\n";

        foreach ($questiondata->inputs as $input) {
            $output .= "    <input>\n";
            $output .= "      <name>{$input->name}</name>\n";
            $output .= "      <type>{$input->type}</type>\n";
            $output .= "      <tans>{$format->xml_escape($input->tans)}</tans>\n";
            $output .= "      <boxsize>{$input->boxsize}</boxsize>\n";
            $output .= "      <strictsyntax>{$input->strictsyntax}</strictsyntax>\n";
            $output .= "      <insertstars>{$input->insertstars}</insertstars>\n";
            $output .= "      <syntaxhint>{$format->xml_escape($input->syntaxhint)}</syntaxhint>\n";
            $output .= "      <syntaxattribute>{$format->xml_escape($input->syntaxattribute)}</syntaxattribute>\n";
            $output .= "      <forbidwords>{$format->xml_escape($input->forbidwords)}</forbidwords>\n";
            $output .= "      <allowwords>{$format->xml_escape($input->allowwords)}</allowwords>\n";
            $output .= "      <forbidfloat>{$input->forbidfloat}</forbidfloat>\n";
            $output .= "      <requirelowestterms>{$input->requirelowestterms}</requirelowestterms>\n";
            $output .= "      <checkanswertype>{$input->checkanswertype}</checkanswertype>\n";
            $output .= "      <mustverify>{$input->mustverify}</mustverify>\n";
            $output .= "      <showvalidation>{$input->showvalidation}</showvalidation>\n";
            $output .= "      <options>{$input->options}</options>\n";
            $output .= "    </input>\n";
        }

        foreach ($questiondata->prts as $prt) {
            $output .= "    <prt>\n";
            $output .= "      <name>{$prt->name}</name>\n";
            $output .= "      <value>{$prt->value}</value>\n";
            $output .= "      <autosimplify>{$prt->autosimplify}</autosimplify>\n";
            $output .= "      <feedbackstyle>{$prt->feedbackstyle}</feedbackstyle>\n";
            $output .= "      <feedbackvariables>\n";
            $output .= "        " . $format->writetext($prt->feedbackvariables, 0);
            $output .= "      </feedbackvariables>\n";

            foreach ($prt->nodes as $node) {
                $output .= "      <node>\n";
                $output .= "        <name>{$node->nodename}</name>\n";
                $output .= "        <answertest>{$node->answertest}</answertest>\n";
                $output .= "        <sans>{$format->xml_escape($node->sans)}</sans>\n";
                $output .= "        <tans>{$format->xml_escape($node->tans)}</tans>\n";
                $output .= "        <testoptions>{$format->xml_escape($node->testoptions)}</testoptions>\n";
                $output .= "        <quiet>{$node->quiet}</quiet>\n";
                $output .= "        <truescoremode>{$node->truescoremode}</truescoremode>\n";
                $output .= "        <truescore>{$node->truescore}</truescore>\n";
                $output .= "        <truepenalty>{$node->truepenalty}</truepenalty>\n";
                $output .= "        <truenextnode>{$node->truenextnode}</truenextnode>\n";
                $output .= "        <trueanswernote>{$format->xml_escape($node->trueanswernote)}</trueanswernote>\n";
                $output .= $this->export_xml_text($format, 'truefeedback', $node->truefeedback, $node->truefeedbackformat,
                                $contextid, 'prtnodetruefeedback', $node->id, '        ');
                $output .= "        <falsescoremode>{$node->falsescoremode}</falsescoremode>\n";
                $output .= "        <falsescore>{$node->falsescore}</falsescore>\n";
                $output .= "        <falsepenalty>{$node->falsepenalty}</falsepenalty>\n";
                $output .= "        <falsenextnode>{$node->falsenextnode}</falsenextnode>\n";
                $output .= "        <falseanswernote>{$format->xml_escape($node->falseanswernote)}</falseanswernote>\n";
                $output .= $this->export_xml_text($format, 'falsefeedback', $node->falsefeedback, $node->falsefeedbackformat,
                                $contextid, 'prtnodefalsefeedback', $node->id, '        ');
                $output .= "      </node>\n";
            }

            $output .= "    </prt>\n";
        }

        foreach ($questiondata->deployedseeds as $deployedseed) {
            $output .= "    <deployedseed>{$deployedseed}</deployedseed>\n";
        }

        foreach ($questiondata->testcases as $testcase => $qtest) {
            $output .= "    <qtest>\n";
            $output .= "      <testcase>{$testcase}</testcase>\n";

            foreach ($qtest->inputs as $name => $value) {
                $output .= "      <testinput>\n";
                $output .= "        <name>{$name}</name>\n";
                $output .= "        <value>{$format->xml_escape($value)}</value>\n";
                $output .= "      </testinput>\n";
            }

            foreach ($qtest->expectedresults as $name => $expected) {
                $output .= "      <expected>\n";
                $output .= "        <name>{$name}</name>\n";
                $output .= "        <expectedscore>{$format->xml_escape($expected->score)}</expectedscore>\n";
                $output .= "        <expectedpenalty>{$format->xml_escape($expected->penalty)}</expectedpenalty>\n";
                $output .= "        <expectedanswernote>{$format->xml_escape($expected->answernotes[0])}</expectedanswernote>\n";
                $output .= "      </expected>\n";
            }

            $output .= "    </qtest>\n";
        }

        return $output;
    }

    public function import_from_xml($xml, $fromform, qformat_xml $format, $notused = null) {
        if (!isset($xml['@']['type']) || $xml['@']['type'] != $this->name()) {
            return false;
        }

        $fromform = $format->import_headers($xml);
        $fromform->qtype = $this->name();

        $fromform->stackversion          = $format->getpath($xml, array('#', 'stackversion', 0, '#', 'text', 0, '#'), '', true);
        $fromform->questionvariables     = $format->getpath($xml, array('#', 'questionvariables',
                                                            0, '#', 'text', 0, '#'), '', true);
        $fromform->specificfeedback      = $this->import_xml_text($xml, 'specificfeedback', $format, $fromform->questiontextformat);
        $fromform->questionnote          = $format->getpath($xml, array('#', 'questionnote', 0, '#', 'text', 0, '#'), '', true);
        $fromform->questionsimplify      = $format->getpath($xml, array('#', 'questionsimplify', 0, '#'), 1);
        $fromform->assumepositive        = $format->getpath($xml, array('#', 'assumepositive', 0, '#'), 0);
        $fromform->assumereal            = $format->getpath($xml, array('#', 'assumereal', 0, '#'), 0);
        $fromform->prtcorrect            = $this->import_xml_text($xml, 'prtcorrect', $format, $fromform->questiontextformat);
        $fromform->prtpartiallycorrect   = $this->import_xml_text($xml, 'prtpartiallycorrect',
                                                                  $format, $fromform->questiontextformat);
        $fromform->prtincorrect          = $this->import_xml_text($xml, 'prtincorrect', $format, $fromform->questiontextformat);
        $fromform->penalty               = $format->getpath($xml, array('#', 'penalty', 0, '#'), 0.1);
        $fromform->multiplicationsign    = $format->getpath($xml, array('#', 'multiplicationsign', 0, '#'), 'dot');
        $fromform->sqrtsign              = $format->getpath($xml, array('#', 'sqrtsign', 0, '#'), 1);
        $fromform->complexno             = $format->getpath($xml, array('#', 'complexno', 0, '#'), 'i');
        $fromform->inversetrig           = $format->getpath($xml, array('#', 'inversetrig', 0, '#'), 'cos-1');
        $fromform->logicsymbol           = $format->getpath($xml, array('#', 'logicsymbol', 0, '#'), 'lang');
        $fromform->matrixparens          = $format->getpath($xml, array('#', 'matrixparens', 0, '#'), '[');
        $fromform->variantsselectionseed = $format->getpath($xml, array('#', 'variantsselectionseed', 0, '#'), 'i');

        if (isset($xml['#']['input'])) {
            foreach ($xml['#']['input'] as $inputxml) {
                $this->import_xml_input($inputxml, $fromform, $format);
            }
        }

        if (isset($xml['#']['input'])) {
            foreach ($xml['#']['input'] as $inputxml) {
                $this->import_xml_input($inputxml, $fromform, $format);
            }
        }

        if (isset($xml['#']['prt'])) {
            foreach ($xml['#']['prt'] as $prtxml) {
                $this->import_xml_prt($prtxml, $fromform, $format);
            }
        }

        $format->import_hints($fromform, $xml, false, false,
                $format->get_format($fromform->questiontextformat));

        if (isset($xml['#']['deployedseed'])) {
            $fromform->deployedseeds = array();
            foreach ($xml['#']['deployedseed'] as $seedxml) {
                $fromform->deployedseeds[] = $format->getpath($seedxml, array('#'), null);
            }
        }

        if (isset($xml['#']['qtest'])) {
            $fromform->testcases = array();
            foreach ($xml['#']['qtest'] as $qtestxml) {
                list($no, $testcase) = $this->import_xml_qtest($qtestxml, $format);
                $fromform->testcases[$no] = $testcase;
            }
        }

        return $fromform;
    }

    /**
     * Helper method used by {@link export_to_xml()}.
     * @param array $xml the XML to extract the data from.
     * @param string $field the name of the sub-tag in the XML to load the data from.
     * @param qformat_xml $format the importer/exporter object.
     * @param int $defaultformat Dfeault text format, if it is not given in the file.
     * @return array with fields text, format and files.
     */
    protected function import_xml_text($xml, $field, qformat_xml $format, $defaultformat) {
        $text = array();
        $text['text']   = $format->getpath($xml, array('#', $field, 0, '#', 'text', 0, '#'), '', true);
        $text['format'] = $format->trans_format($format->getpath($xml, array('#', $field, 0, '@', 'format'),
                                                $format->get_format($defaultformat)));
        $text['files']  = $format->import_files($format->getpath($xml, array('#', $field, 0, '#', 'file'), array(), false));

        return $text;
    }

    /**
     * Helper method used by {@link export_to_xml()}. Handle the data for one input.
     * @param array $xml the bit of the XML representing one input.
     * @param object $fromform the data structure we are building from the XML.
     * @param qformat_xml $format the importer/exporter object.
     */
    protected function import_xml_input($xml, $fromform, qformat_xml $format) {
        $name = $format->getpath($xml, array('#', 'name', 0, '#'), null, false, 'Missing input name in the XML.');

        $fromform->{$name . 'type'}               = $format->getpath($xml, array('#', 'type', 0, '#'), '');
        $fromform->{$name . 'modelans'}           = $format->getpath($xml, array('#', 'tans', 0, '#'), '');
        $fromform->{$name . 'boxsize'}            = $format->getpath($xml, array('#', 'boxsize', 0, '#'), 15);
        $fromform->{$name . 'strictsyntax'}       = $format->getpath($xml, array('#', 'strictsyntax', 0, '#'), 1);
        $fromform->{$name . 'insertstars'}        = $format->getpath($xml, array('#', 'insertstars', 0, '#'), 0);
        $fromform->{$name . 'syntaxhint'}         = $format->getpath($xml, array('#', 'syntaxhint', 0, '#'), '');
        $fromform->{$name . 'syntaxattribute'}    = $format->getpath($xml, array('#', 'syntaxattribute', 0, '#'), 0);
        $fromform->{$name . 'forbidwords'}        = $format->getpath($xml, array('#', 'forbidwords', 0, '#'), '');
        $fromform->{$name . 'allowwords'}         = $format->getpath($xml, array('#', 'allowwords', 0, '#'), '');
        $fromform->{$name . 'forbidfloat'}        = $format->getpath($xml, array('#', 'forbidfloat', 0, '#'), 1);
        $fromform->{$name . 'requirelowestterms'} = $format->getpath($xml, array('#', 'requirelowestterms', 0, '#'), 0);
        $fromform->{$name . 'checkanswertype'}    = $format->getpath($xml, array('#', 'checkanswertype', 0, '#'), 0);
        $fromform->{$name . 'mustverify'}         = $format->getpath($xml, array('#', 'mustverify', 0, '#'), 1);
        $fromform->{$name . 'showvalidation'}     = $format->getpath($xml, array('#', 'showvalidation', 0, '#'), 1);
        $fromform->{$name . 'options'}            = $format->getpath($xml, array('#', 'options', 0, '#'), '');
    }

    /**
     * Helper method used by {@link export_to_xml()}. Handle the data for one PRT.
     * @param array $xml the bit of the XML representing one PRT.
     * @param object $fromform the data structure we are building from the XML.
     * @param qformat_xml $format the importer/exporter object.
     */
    protected function import_xml_prt($xml, $fromform, qformat_xml $format) {
        $name = $format->getpath($xml, array('#', 'name', 0, '#'), null, false, 'Missing PRT name in the XML.');

        $fromform->{$name . 'value'}             = $format->getpath($xml, array('#', 'value', 0, '#'), 1);
        $fromform->{$name . 'autosimplify'}      = $format->getpath($xml, array('#', 'autosimplify', 0, '#'), 1);
        $fromform->{$name . 'feedbackstyle'}     = $format->getpath($xml, array('#', 'feedbackstyle', 0, '#'), 1);
        $fromform->{$name . 'feedbackvariables'} = $format->getpath($xml,
                            array('#', 'feedbackvariables', 0, '#', 'text', 0, '#'), '', true);

        if (isset($xml['#']['node'])) {
            foreach ($xml['#']['node'] as $nodexml) {
                $this->import_xml_prt_node($nodexml, $name, $fromform, $format);
            }
        }
    }

    /**
     * Helper method used by {@link import_xml_prt()}. Handle the data for one PRT node.
     * @param array $xml the bit of the XML representing one PRT.
     * @param string $prtname the name of the PRT this node belongs to.
     * @param object $fromform the data structure we are building from the XML.
     * @param qformat_xml $format the importer/exporter object.
     */
    protected function import_xml_prt_node($xml, $prtname, $fromform, qformat_xml $format) {
        $name = $format->getpath($xml, array('#', 'name', 0, '#'), null, false, 'Missing PRT name in the XML.');

        $fromform->{$prtname . 'answertest'}[$name]      = $format->getpath($xml, array('#', 'answertest', 0, '#'), '');
        $fromform->{$prtname . 'sans'}[$name]            = $format->getpath($xml, array('#', 'sans', 0, '#'), '');
        $fromform->{$prtname . 'tans'}[$name]            = $format->getpath($xml, array('#', 'tans', 0, '#'), '');
        $fromform->{$prtname . 'testoptions'}[$name]     = $format->getpath($xml, array('#', 'testoptions', 0, '#'), '');
        $fromform->{$prtname . 'quiet'}[$name]           = $format->getpath($xml, array('#', 'quiet', 0, '#'), 0);
        $fromform->{$prtname . 'truescoremode'}[$name]   = $format->getpath($xml, array('#', 'truescoremode', 0, '#'), '=');
        $fromform->{$prtname . 'truescore'}[$name]       = $format->getpath($xml, array('#', 'truescore', 0, '#'), 1);
        $fromform->{$prtname . 'truepenalty'}[$name]     = $format->getpath($xml, array('#', 'truepenalty', 0, '#'), '');
        $fromform->{$prtname . 'truenextnode'}[$name]    = $format->getpath($xml, array('#', 'truenextnode', 0, '#'), -1);
        $fromform->{$prtname . 'trueanswernote'}[$name]  = $format->getpath($xml,
                array('#', 'trueanswernote', 0, '#'), 1, '');
        $fromform->{$prtname . 'truefeedback'}[$name]    = $this->import_xml_text($xml,
                'truefeedback', $format, $fromform->questiontextformat);
        $fromform->{$prtname . 'falsescoremode'}[$name]  = $format->getpath($xml, array('#', 'falsescoremode', 0, '#'), '=');
        $fromform->{$prtname . 'falsescore'}[$name]      = $format->getpath($xml, array('#', 'falsescore', 0, '#'), 1);
        $fromform->{$prtname . 'falsepenalty'}[$name]    = $format->getpath($xml, array('#', 'falsepenalty', 0, '#'), '');
        $fromform->{$prtname . 'falsenextnode'}[$name]   = $format->getpath($xml, array('#', 'falsenextnode', 0, '#'), -1);
        $fromform->{$prtname . 'falseanswernote'}[$name] = $format->getpath($xml, array('#', 'falseanswernote', 0, '#'), '');
        $fromform->{$prtname . 'falsefeedback'}[$name]   = $this->import_xml_text($xml,
                'falsefeedback', $format, $fromform->questiontextformat);
    }

    /**
     * Helper method used by {@link export_to_xml()}. Handle the data for one question text.
     * @param array $xml the bit of the XML representing one question text.
     * @param qformat_xml $format the importer/exporter object.
     * @return stack_question_test the question test.
     */
    protected function import_xml_qtest($xml, qformat_xml $format) {
        $number = $format->getpath($xml, array('#', 'testcase', 0, '#'), null, false, 'Missing testcase number in the XML.');

        $inputs = array();
        if (isset($xml['#']['testinput'])) {
            foreach ($xml['#']['testinput'] as $inputxml) {
                $name  = $format->getpath($inputxml, array('#', 'name', 0, '#'), '');
                $value = $format->getpath($inputxml, array('#', 'value', 0, '#'), '');
                $inputs[$name] = $value;
            }
        }

        $testcase = new stack_question_test($inputs, $number);

        if (isset($xml['#']['expected'])) {
            foreach ($xml['#']['expected'] as $expectedxml) {
                $name  = $format->getpath($expectedxml, array('#', 'name', 0, '#'), '');
                $expectedscore = $format->getpath($expectedxml, array('#', 'expectedscore', 0, '#'), '');
                $expectedpenalty = $format->getpath($expectedxml, array('#', 'expectedpenalty', 0, '#'), '');
                $expectedanswernote = $format->getpath($expectedxml, array('#', 'expectedanswernote', 0, '#'), '');

                $testcase->add_expected_result($name, new stack_potentialresponse_tree_state(
                        1, true, $expectedscore, $expectedpenalty, '', array($expectedanswernote)));
            }
        }

        return array($number, $testcase);
    }

    /*
     * This method takes Moodle's "fromform" data type and validates the question.  All question level validation and warnings
     * should be in this method.
     * Much of this code was in edit_stack_form.php (until Jan 2018).
     * See https://docs.moodle.org/dev/Question_data_structures for why we chose the "fromform" data structure,
     * not "question" objects.
     *
     * @param array $fromform Moodle's "fromform" data type.
     * @param array $errors Existing partial error array.
     * @return array($errors, $warnings).
     */
    public function validate_fromform($fromform, $errors) {
        $warnings = array();

        $fixingdollars = array_key_exists('fixdollars', $fromform);

        $this->options = new stack_options();
        $this->options->set_option('multiplicationsign', $fromform['multiplicationsign']);
        $this->options->set_option('complexno',          $fromform['complexno']);
        $this->options->set_option('inversetrig',        $fromform['inversetrig']);
        $this->options->set_option('logicsymbol',        $fromform['logicsymbol']);
        $this->options->set_option('matrixparens',       $fromform['matrixparens']);
        $this->options->set_option('sqrtsign',    (bool) $fromform['sqrtsign']);
        $this->options->set_option('simplify',    (bool) $fromform['questionsimplify']);
        $this->options->set_option('assumepos',   (bool) $fromform['assumepositive']);
        $this->options->set_option('assumereal',  (bool) $fromform['assumereal']);

        // We slightly break the usual conventions of validation, in that rather
        // than building up $errors as an array of strings, we initially build it
        // up as an array of arrays, then at the end remove any empty arrays,
        // and implode (' ', ...) any arrays that are non-empty. This makes our
        // rather complex validation easier to implement.

        // Question text.
        $errors['questiontext'] = array();
        $errors = $this->validate_cas_text($errors, $fromform['questiontext']['text'], 'questiontext', $fixingdollars);

        // Check multi-language versions all have the same feedback tags.
        $ml = new stack_multilang();
        $combinedtext = $fromform['questiontext']['text'] . $fromform['specificfeedback']['text'];
        $langs = $ml->languages_used($combinedtext);
        if ($langs == array()) {
            $prts = $this->get_prt_names_from_question($fromform['questiontext']['text'], $fromform['specificfeedback']['text']);
        } else {
            $prtsbylang = array();
            foreach ($langs as $lang) {
                $prtsbylang[$lang] = $this->get_prt_names_from_question_lang($ml->filter($combinedtext, $lang));
            }
            // Check they are all equal, but don't fuss about exact differences as feedback.
            $prts = reset($prtsbylang);
            $failed = false;
            foreach ($langs as $lang) {
                if ($prtsbylang[$lang] != $prts) {
                    $failed = true;
                }
            }
            if ($failed) {
                $errors['questiontext'][] = stack_string('questiontextfeedbacklanguageproblems');
            }
        }

        // Check for whitespace following placeholders.
        $sloppytags = $this->validation_get_sloppy_tags($fromform['questiontext']['text']);
        foreach ($sloppytags as $sloppytag) {
            $errors['questiontext'][] = stack_string(
                    'questiontextplaceholderswhitespace', $sloppytag);
        }

        // Check multi-language versions all have the same inputs and validation tags.
        $ml = new stack_multilang();
        $langs = $ml->languages_used($fromform['questiontext']['text']);
        if ($langs == array()) {
            $inputs = $this->get_input_names_from_question_text_lang($fromform['questiontext']['text']);
        } else {
            $inputsbylang = array();
            foreach ($langs as $lang) {
                $inputsbylang[$lang] = $this->get_input_names_from_question_text_lang(
                        $ml->filter($fromform['questiontext']['text'], $lang));
            }
            // Check they are all equal, but don't fuss about exact differences as feedback.
            $inputs = reset($inputsbylang);
            $failed = false;
            foreach ($langs as $lang) {
                if ($inputsbylang[$lang] != $inputs) {
                    $failed = true;
                }
            }
            if ($failed) {
                $errors['questiontext'][] = stack_string('inputlanguageproblems');
            }
        }

        // Check input placholders appear with the correct number of times in the question text.
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

        // Question variables.
        $errors = $this->validate_cas_keyval($errors, $fromform['questionvariables'], 'questionvariables',
                array_keys($inputs));

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

        $errors['specificfeedback'] += $this->validation_check_no_placeholders(
                stack_string('specificfeedback'), $fromform['specificfeedback']['text'],
                array('input', 'validation'));

        // General feedback.
        $errors['generalfeedback'] = array();
        $errors = $this->validate_cas_text($errors, $fromform['generalfeedback']['text'], 'generalfeedback', $fixingdollars);
        $errors['generalfeedback'] += $this->validation_check_no_placeholders(
                get_string('generalfeedback', 'question'), $fromform['generalfeedback']['text']);

        // Question note.
        $errors['questionnote'] = array();
        if ('' == $fromform['questionnote']) {
            $foundrandom = false;
            foreach (stack_cas_security::get_all_with_feature('random') as $rndid) {
                if (!(false === strpos($fromform['questionvariables'], $rndid))) {
                    $foundrandom = true;
                    break;
                }
            }
            if ($foundrandom) {
                $errors['questionnote'][] = stack_string('questionnotempty');
            }
        } else {
            // Note, the 'questionnote' does not have an editor field and hence no 'text' sub-clause.
            $errors = $this->validate_cas_text($errors, $fromform['questionnote'], 'questionnote', $fixingdollars);
        }

        $errors['questionnote'] += $this->validation_check_no_placeholders(
                stack_string('questionnote'), $fromform['questionnote']);

        // 2) Validate all inputs.
        $stackinputfactory = new stack_input_factory();
        foreach ($inputs as $inputname => $counts) {
            list($numinputs, $numvalidations) = $counts;

            if ($numinputs == 0 && $numvalidations == 0 && !$fromform[$inputname . 'deleteconfirm']) {
                $errors[$inputname . 'deleteconfirm'][] = stack_string('youmustconfirm');
            }

            if ($numinputs == 0 && $numvalidations == 0) {
                // Input is being deleted. Don't show validation errors.
                continue;
            }

            if (strlen($inputname) > 18 && !isset($fromform[$inputname . 'deleteconfirm'])) {
                $errors['questiontext'][] = stack_string('inputnamelength', $inputname);
            }

            if (!preg_match('/^([a-zA-Z]+|[a-zA-Z]+[0-9a-zA-Z_]*[0-9a-zA-Z]+)$/', $inputname) &&
                    !isset($fromform[$inputname . 'deleteconfirm'])) {
                $errors['questiontext'][] = stack_string('inputnameform', $inputname);
            }

            if ($fromform[$inputname . 'mustverify'] and $fromform[$inputname . 'showvalidation'] == 0) {
                $errors[$inputname . 'mustverify'][] = stack_string('mustverifyshowvalidation');
            }

            if (array_key_exists($inputname . 'modelans', $fromform)) {
                $errors = $this->validate_cas_string($errors,
                        $fromform[$inputname . 'modelans'], $inputname . 'modelans', $inputname . 'modelans');
            }

            $inputtype = $fromform[$inputname . 'type'];
            $modelans = '';
            if (array_key_exists($inputname . 'modelans', $fromform)) {
                $modelans = $fromform[$inputname . 'modelans'];
            }
            $stackinput = $stackinputfactory->make($inputtype, $inputname, $modelans, null, null, false);
            $parameters = array();
            foreach ($stackinputfactory->get_parameters_fromform_mapping($inputtype) as $key => $param) {
                $paramvalue = $stackinputfactory->convert_parameter_fromform($key, $fromform[$inputname .$param]);
                $parameters[$key] = $paramvalue;
                if ('options' !== $key) {
                    $validityresult = $stackinput->validate_parameter($key, $paramvalue);
                    if (!($validityresult === true)) {
                        $errors[$inputname . $param][] = stack_string('inputinvalidparamater');
                    }
                }
            }
            // Create an input with these parameters, in particular the 'options', and validate that.
            $stackinput = $stackinputfactory->make($inputtype, $inputname,
                    $fromform[$inputname . 'modelans'], null, $parameters, false);
            $stackinput->validate_extra_options();
            $errors[$inputname . 'options'] = $stackinput->get_errors();
        }

        // 3) Validate all prts.
        foreach ($prts as $prtname => $count) {
            if ($count == 0) {
                if (!$fromform[$prtname . 'prtdeleteconfirm']) {
                    $errors['specificfeedback'][] = stack_string('prtremovedconfirmbelow', $prtname);
                    $errors[$prtname . 'prtdeleteconfirm'][] = stack_string('youmustconfirm');
                }
                // Don't show validation errors relating to a PRT that is to be deleted.
                continue;

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
                foreach ($messages as $key => $val) {
                    if (is_array($val)) {
                        $messages[$key] = implode(' ', $val);
                    }
                }
                $errors[$field] = implode(' ', $messages);
            } else {
                unset($errors[$field]);
            }
        }

        return array($errors, $warnings);
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
            $casstring = stack_ast_container::make_from_teacher_source($value, '', new stack_cas_security());
            if (!$casstring->get_valid()) {
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

        $castext = new stack_cas_text($value, $session, $this->seed);
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

        if ($castext->get_errors()) {
            $errors[$fieldname][] = $castext->get_errors();
            return $errors;
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
    protected function validate_cas_keyval($errors, $value, $fieldname, $inputs = null) {
        if ('' == trim($value)) {
            return $errors;
        }

        $keyval = new stack_cas_keyval($value, $this->options, $this->seed);
        if (!$keyval->get_valid($inputs)) {
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

        $keyval = new stack_cas_keyval($fromform['questionvariables'], $this->options, $this->seed);
        if ($keyval->get_valid()) {
            $runtimeerrors = $keyval->instantiate();
        }
        if ($runtimeerrors) {
            $errors['questionvariables'][] = $runtimeerrors;
        }
        $session = $keyval->get_session();
        if ($session->get_errors()) {
            $errors['questionvariables'][] = $session->get_errors(true);
            $errors['questionvariables'] = array_unique($errors['questionvariables']);
            return $errors;
        }

        // Instantiate all text fields and look for errors.
        $castextfields = array('questiontext', 'specificfeedback', 'generalfeedback');
        foreach ($castextfields as $field) {
            $errors = $this->validate_cas_text($errors, $fromform[$field]['text'], $field, $fixingdollars, clone $session);
        }
        $errors = $this->validate_cas_text($errors, $fromform['questionnote'], 'questionnote', $fixingdollars, clone $session);

        // Make a list of all inputs, instantiate it and then look for errors.
        $inputs = array_keys($this->get_input_names_from_question_text($fromform['questiontext']['text']));
        $inputvalues = array();
        foreach ($inputs as $inputname) {
            if (array_key_exists($inputname . 'modelans', $fromform)) {
                $value = $inputname.':'.$fromform[$inputname . 'modelans'];
                $cs = stack_ast_container::make_from_teacher_source($value, '', new stack_cas_security());
                $inputvalues[] = $cs;
            }
        }
        // TODO: why clone when we never reuse the original...
        $inputsession = clone $session;
        $inputsession->add_statements($inputvalues);
        if ($inputsession->get_valid()) {
            $inputsession->instantiate();
        }

        $getdebuginfo = false;
        foreach ($inputs as $inputname) {
            if ($inputsession->get_by_key($inputname) !== null &&
                    $inputsession->get_by_key($inputname)->get_errors() !== '') {
                $errors[$inputname . 'modelans'][] = $inputsession->get_by_key($inputname)->get_errors();
                $in = $inputsession->get_by_key($inputname);
                if (!$in->is_correctly_evaluated()) {
                    $getdebuginfo = true;
                }
                // TODO: Send the actual value to the input, and ask it to validate it.
                // For example, the matrix input type could check that the model answer is a matrix.
            }

            if ($fromform[$inputname . 'options'] && $inputsession->get_by_key('optionsfor' . $inputname)
                    && $inputsession->get_by_key('optionsfor' . $inputname)->get_errors() !== '') {
                $errors[$inputname . 'options'][] = $inputsession->get_by_key('optionsfor' . $inputname)->get_errors();
            }
            // ... else TODO: Send the actual value to the input, and ask it to validate it.
        }

        if ($getdebuginfo) {
            $errors['questionvariables'][] = $inputsession->get_debuginfo();
        }

        // At this point if we have errors, especially with inputs, there is no point in executing any of the PRTs.
        if (!empty($errors)) {
            return $errors;
        }

        // TODO: loop over all the PRTs in a similar manner....
        // Remember, to clone the inputsession as the base session for each PRT.
        // This will have all the teacher's answers instantiated.
        // Otherwise we are likley to do illigitimate things to the various inputs.

        return $errors;
    }

    /**
     * Tags which have extra whitespace within them. E.g. [[input: ans1]] are forbidden.
     * @return array of tags.
     */
    public function validation_get_sloppy_tags($text) {

        $sloppytags = stack_utils::extract_placeholders_sloppy($text, 'input');
        $sloppytags = array_merge(stack_utils::extract_placeholders_sloppy($text, 'validation'), $sloppytags);
        $sloppytags = array_merge(stack_utils::extract_placeholders_sloppy($text, 'prt'), $sloppytags);

        return $sloppytags;
    }

    /**
     * Check a form field to ensure it does not contain any placeholders of given types.
     * @param string $fieldname the name of this field. Used in the error messages.
     * @param value $value the value to check.
     * @param array $placeholders types to check for. By default 'input', 'validation' and 'feedback'.
     * @return array of problems (so an empty array means all is well).
     */
    protected function validation_check_no_placeholders($fieldname, $value,
            $placeholders = array('input', 'validation', 'feedback')) {
        $problems = array();
        foreach ($placeholders as $placeholder) {
            if (stack_utils::extract_placeholders($value, 'input')) {
                $problems[] = stack_string('fieldshouldnotcontainplaceholder',
                        array('field' => $fieldname, 'type' => $placeholder));
            }
        }
        return $problems;
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
        $inputs = array_keys($this->get_input_names_from_question_text($fromform['questiontext']['text']));
        $errors = $this->validate_cas_keyval($errors, $fromform[$prtname . 'feedbackvariables'],
                $prtname . 'feedbackvariables', $inputs);

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
        $question = null;
        if (property_exists($this, 'question')) {
            $question = $this->question;
        }
        $graph = $this->get_prt_graph($prtname, $question);
        $textformat = null;
        foreach ($graph->get_nodes() as $node) {
            $nodekey = $node->name - 1;

            // Check the fields the belong to this node individually.
            $errors = $this->validate_prt_node($errors, $fromform, $prtname, $nodekey, $fixingdollars);

            if (is_null($textformat)) {
                $textformat = $fromform[$prtname . 'truefeedback'][$nodekey]['format'];
            }
            if ($textformat != $fromform[$prtname . 'truefeedback'][$nodekey]['format']) {
                $errors[$prtname . 'truefeedback[' . $nodekey . ']'][]
                    = stack_string('allnodefeedbackmustusethesameformat');
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

        $atname = $fromform[$prtname . 'answertest'][$nodekey];
        if (stack_ans_test_controller::required_atoptions($atname)) {
            $opt = trim($fromform[$prtname . 'testoptions'][$nodekey]);

            if ('' === trim($opt)) {
                $errors[$nodegroup][] = stack_string('testoptionsrequired');

            } else if (strlen($opt) > 255) {
                $errors[$nodegroup][] = stack_string('testoptionsinvalid',
                        stack_string('strlengtherror'));

            } else {
                $cs = stack_ast_container::make_from_teacher_source('null', '', new stack_cas_security());
                $answertest = new stack_ans_test_controller($atname, $cs, $cs);
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
            } else if (strstr($answernote, ';') !== false || strstr($answernote, ':') !== false) {
                $errors[$branchgroup][] = stack_string('answernote_err2');
            }

            $errors = $this->validate_cas_text($errors, $fromform[$prtname . $branch . 'feedback'][$nodekey]['text'],
                    $prtname . $branch . 'feedback[' . $nodekey . ']', $fixingdollars);
        }

        return $errors;
    }

    /**
     * This method is needed for validation, and to construct the editing form.
     * @return array of the input names that currently appear in the question text.
     */
    public function get_input_names_from_question_text($questiontext) {
        $ml = new stack_multilang();
        $langs = $ml->languages_used($questiontext);
        if ($langs == array()) {
            return $this->get_input_names_from_question_text_lang($questiontext);
        }

        // At this point, all languages are assumed to have the same inputs.
        $lang = reset($langs);
        return($this->get_input_names_from_question_text_lang($ml->filter($questiontext, $lang)));
    }

    private function get_input_names_from_question_text_lang($questiontext) {
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
     * This method is needed for validation, and to construct the editing form.
     * @return array of the PRT names that currently appear in the question
     *      text and specific feedback.
     */
    public function get_prt_names_from_question($questiontext, $specificfeedback) {
        $ml = new stack_multilang();
        $langs = $ml->languages_used($questiontext.$specificfeedback);
        if ($langs == array()) {
            return $this->get_prt_names_from_question_lang($questiontext.$specificfeedback);
        }

        // At this point, all languages are assumed to have the same prts.
        $lang = reset($langs);
        return($this->get_prt_names_from_question_lang($ml->filter($questiontext.$specificfeedback, $lang)));
    }

    private function get_prt_names_from_question_lang($text) {
        $prts = stack_utils::extract_placeholders($text, 'feedback');
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
     * Get a list of the PRT notes that should be present for a given PRT.
     * @param string $prtname the name of a PRT.
     * @param $question the question itself.
     * @return array list of nodes that should be present in the form definitino for this PRT.
     */
    public function get_prt_graph($prtname, $question) {
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
                        '#fgroup_id_' . $prtname . 'node_' . ($lastkey + 1));
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
        if (!empty($question->prts[$prtname]->nodes)) {
            $graph = new stack_abstract_graph();
            foreach ($question->prts[$prtname]->nodes as $node) {
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
     * Helper method to get the list of inputs required by a PRT, given the current
     * state of the form.
     * @param string $prtname the name of a PRT.
     * @param qtype_stack $question
     * @return array list of inputs used by this PRT.
     */
    public function get_inputs_used_by_prt($prtname, $question) {
        // Needed for questions with no inputs, (in particular blank starting questions).
        if (!property_exists($question, 'inputs')) {
            return array();
        }
        if (is_null($question->inputs)) {
            return array();
        }
        $inputs = $question->inputs;
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
        if (is_null($question->prts) || !array_key_exists($prtname, $question->prts)) {
            return array();
        }
        $prt = $question->prts[$prtname];

        $prtnodes = array();
        foreach ($prt->nodes as $name => $node) {
            $sans = stack_ast_container::make_from_teacher_source($node->sans, '', new stack_cas_security());
            $tans = stack_ast_container::make_from_teacher_source($node->tans, '', new stack_cas_security());
            $prtnode = new stack_potentialresponse_node($sans, $tans, $node->answertest, $node->testoptions);
            $prtnode->add_branch(1, '+', 0, '', -1, $node->truefeedback, $node->truefeedbackformat, '');
            $prtnode->add_branch(0, '+', 0, '', -1, $node->falsefeedback, $node->falsefeedbackformat, '');
            $prtnodes[$name] = $prtnode;
        }
        $feedbackvariables = new stack_cas_keyval($prt->feedbackvariables);
        $potentialresponsetree = new stack_potentialresponse_tree(
                '', '', false, 0, $feedbackvariables->get_session(), $prtnodes, (string) $prt->firstnodename, 1);
        return $potentialresponsetree->get_required_variables($inputkeys);
    }
}

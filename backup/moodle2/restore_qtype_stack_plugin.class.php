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
 * STACK question type restore code.
 * @package   qtype_stack
 * @copyright 2012 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/type/stack/stack/graphlayout/graph.php');
require_once($CFG->dirroot . '/question/type/stack/stack/utils.class.php');


/**
 * Provides the information to restore STACK questions
 *
 * @copyright 2012 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_qtype_stack_plugin extends restore_qtype_plugin {

    /** @var string the name of the PRT we are currently restoring. */
    protected $currentprtname = null;

    /** @var int the name of the PRT we are currently restoring. */
    protected $currenttestcase = null;

    /**
     * Returns the paths to be handled by the plugin at question level
     */
    protected function define_question_plugin_structure() {

        $paths = array();

        // List the relevant paths in the XML.
        $elements = array(
            'qtype_stack_options'        => '/stackoptions',
            'qtype_stack_input'          => '/stackinputs/stackinput',
            'qtype_stack_prt'            => '/stackprts/stackprt',
            'qtype_stack_prt_node'       => '/stackprts/stackprt/stackprtnodes/stackprtnode',
            'qtype_stack_qtest'          => '/stackqtests/stackqtest',
            'qtype_stack_qtest_input'    => '/stackqtests/stackqtest/stackqtestinputs/stackqtestinput',
            'qtype_stack_qtest_expected' => '/stackqtests/stackqtest/stackqtestexpecteds/stackqtestexpected',
            'qtype_stack_deployed_seed'  => '/stackdeployedseeds/stackdeployedseed',
        );
        foreach ($elements as $elename => $path) {
            $paths[] = new restore_path_element($elename, $this->get_pathfor($path));
        }

        return $paths;
    }

    /**
     * Process the STACK options.
     * @param array/object $data the data from the backup file.
     */
    public function process_qtype_stack_options($data) {
        global $DB;

        $data = (object)$data;

        if (!property_exists($data, 'stackversion')) {
            $data->stackversion = '';
        }

        if (!property_exists($data, 'inversetrig')) {
            $data->inversetrig = 'cos-1';
        }

        if (!property_exists($data, 'logicsymbol')) {
            $data->logicsymbol = 'lang';
        }

        if (!property_exists($data, 'matrixparens')) {
            $data->matrixparens = '[';
        }

        // Detect if the question is created or mapped.
        $questioncreated = (bool) $this->get_mappingid('question_created', $this->get_old_parentid('question'));

        // If the question is being created by restore, save the stack options.
        if ($questioncreated) {
            $oldid = $data->id;
            $data->questionid = $this->get_new_parentid('question');
            $newitemid = $DB->insert_record('qtype_stack_options', $data);
            $this->set_mapping('qtype_stack_options', $oldid, $newitemid);
        }
    }

    /**
     * Process the STACK inputs.
     * @param array/object $data the data from the backup file.
     */
    public function process_qtype_stack_input($data) {
        global $DB;

        $data = (object)$data;

        if (!property_exists($data, 'options')) {
            $data->options = '';
        }

        // Detect if the question is created or mapped.
        $questioncreated = (bool) $this->get_mappingid('question_created', $this->get_old_parentid('question'));

        // If the question is being created, save this input.
        if ($questioncreated) {
            $data->questionid = $this->get_new_parentid('question');
            $DB->insert_record('qtype_stack_inputs', $data, false);
        }
    }

    /**
     * Process the STACK PRTs.
     * @param array/object $data the data from the backup file.
     */
    public function process_qtype_stack_prt($data) {
        global $DB;

        $data = (object)$data;

        $this->currentprtname = $data->name;

        if (!property_exists($data, 'firstnodename')) {
            $data->firstnodename = -1; // This will be fixed later.
        }

        // Detect if the question is created or mapped.
        $questioncreated = (bool) $this->get_mappingid('question_created', $this->get_old_parentid('question'));

        // If the question is being created, save this PRT.
        if ($questioncreated) {
            $data->questionid = $this->get_new_parentid('question');
            $DB->insert_record('qtype_stack_prts', $data, false);
        }
    }

    /**
     * Process the STACK PRT nodes.
     * @param array/object $data the data from the backup file.
     */
    public function process_qtype_stack_prt_node($data) {
        global $DB;

        $data = (object)$data;

        // Detect if the question is created or mapped.
        $questioncreated = (bool) $this->get_mappingid('question_created', $this->get_old_parentid('question'));

        // If the question is being created, save this input.
        if ($questioncreated) {
            $oldid = $data->id;

            $data->questionid = $this->get_new_parentid('question');
            $data->prtname = $this->currentprtname;
            $data->truepenalty = stack_utils::fix_approximate_thirds($data->truepenalty);
            $data->falsepenalty = stack_utils::fix_approximate_thirds($data->falsepenalty);

            $newitemid = $DB->insert_record('qtype_stack_prt_nodes', $data);
            $this->set_mapping('qtype_stack_prt_nodes', $oldid, $newitemid);
        }
    }

    /**
     * Process the STACK question tests.
     * @param array/object $data the data from the backup file.
     */
    public function process_qtype_stack_qtest($data) {
        global $DB;

        $data = (object)$data;

        $this->currenttestcase = $data->testcase;

        // Detect if the question is created or mapped.
        $questioncreated = (bool) $this->get_mappingid('question_created', $this->get_old_parentid('question'));

        // If the question is being created, save this input.
        if ($questioncreated) {
            $data->questionid = $this->get_new_parentid('question');
            $DB->insert_record('qtype_stack_qtests', $data, false);
        }
    }

    /**
     * Process the STACK question test input data.
     * @param array/object $data the data from the backup file.
     */
    public function process_qtype_stack_qtest_input($data) {
        global $DB;

        $data = (object)$data;

        // Detect if the question is created or mapped.
        $questioncreated = (bool) $this->get_mappingid('question_created', $this->get_old_parentid('question'));

        // If the question is being created, save this input.
        if ($questioncreated) {
            $data->questionid = $this->get_new_parentid('question');
            $data->testcase = $this->currenttestcase;
            $DB->insert_record('qtype_stack_qtest_inputs', $data, false);
        }
    }

    /**
     * Process the STACK question test expected results.
     * @param array/object $data the data from the backup file.
     */
    public function process_qtype_stack_qtest_expected($data) {
        global $DB;

        $data = (object)$data;

        // Detect if the question is created or mapped.
        $questioncreated = (bool) $this->get_mappingid('question_created', $this->get_old_parentid('question'));

        // If the question is being created, save this input.
        if ($questioncreated) {
            $data->questionid = $this->get_new_parentid('question');
            $data->testcase = $this->currenttestcase;
            $data->expectedpenalty = stack_utils::fix_approximate_thirds($data->expectedpenalty);
            $DB->insert_record('qtype_stack_qtest_expected', $data, false);
        }
    }

    /**
     * Process the STACK question tests.
     * @param array/object $data the data from the backup file.
     */
    public function process_qtype_stack_deployed_seed($data) {
        global $DB;

        $data = (object)$data;

        // Detect if the question is created or mapped.
        $questioncreated = (bool) $this->get_mappingid('question_created', $this->get_old_parentid('question'));

        // If the question is being created, save this input.
        if ($questioncreated) {
            $data->questionid = $this->get_new_parentid('question');
            $DB->insert_record('qtype_stack_deployed_seeds', $data, false);
        }
    }

    /**
     * Return the contents of this qtype to be processed by the links decoder
     */
    public static function define_decode_contents() {
        return array(
            new restore_decode_content('qtype_stack_options',
                    array('specificfeedback', 'prtcorrect', 'prtpartiallycorrect', 'prtincorrect'),
                    'qtype_stack_options'),
            new restore_decode_content('qtype_stack_prt_nodes', array('truefeedback', 'falsefeedback'),
                    'qtype_stack_prt_nodes'),
        );
    }

    /**
     * When restoring old data, that does not have the essay options information
     * in the XML, supply defaults.
     */
    protected function after_execute_question() {
        global $DB;

        $prtswithoutfirstnode = $DB->get_records('qtype_stack_prts',
                array('firstnodename' => -1), '', 'id, questionid, name');

        foreach ($prtswithoutfirstnode as $prt) {
            $nodes = $DB->get_records('qtype_stack_prt_nodes',
                    array('questionid' => $prt->questionid, 'prtname' => $prt->name), '',
                    'nodename, truenextnode, falsenextnode');

            // Find the root node of the PRT.
            // Otherwise, if an existing question is being edited, and this is an
            // existing PRT, base things on the existing question definition.
            $graph = new stack_abstract_graph();
            foreach ($nodes as $node) {

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

                $graph->add_node($node->nodename + 1, $left, $right);
            }
            try {
                $graph->layout();
            } catch (coding_exception $ce) {
                $question = $DB->get_record('question', array('id' => $prt->questionid));
                $this->step->log('The PRT named "' . $prt->name .
                        '" is malformed in question id ' . $prt->questionid .
                        ' and cannot be laid out, (Question name "' . $question->name .
                        '").  Error reported: ' . $ce->getMessage(), backup::LOG_WARNING);
                continue;
            }

            $roots = $graph->get_roots();
            if (count($roots) != 1 || $graph->get_broken_cycles()) {
                $question = $DB->get_record('question', array('id' => $prt->questionid));
                if (count($roots) != 1) {
                    $err = 'abnormal root count: '.count($roots).'(<>1)';
                } else {
                    $err = 'broken cycles: '.implode('.', $graph->get_broken_cycles());
                }
                $this->step->log('The PRT named "' . $prt->name .
                        '" is malformed in question id ' . $prt->questionid .
                        ' and does not have a single root, (Question name "' . $question->name .
                        '").  Error reported: ' . $err, backup::LOG_WARNING);
            }
            reset($roots);
            $firstnode = key($roots) - 1;

            $DB->set_field('qtype_stack_prts', 'firstnodename', $firstnode,
                    array('id' => $prt->id));
        }
    }
}

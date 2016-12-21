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
 * This file defines the editing form used by the tidy question script.
 *
 * @copyright 2013 the Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');
require_once($CFG->dirroot . '/question/type/stack/stack/graphlayout/graph.php');


/**
 * The editing form used by the tidy question script.
 *
 * @copyright 2013 the Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_stack_tidy_question_form extends moodleform {
    protected function definition() {

        $mform = $this->_form;
        $question = $this->_customdata;

        // Inputs.
        $mform->addElement('header', 'inputsheader', stack_string('inputs'));

        foreach ($question->inputs as $inputname => $input) {
            $mform->addElement('text', 'inputname_' . $inputname,
                    stack_string('newnameforx', $inputname), array('size' => 20));
            $mform->setDefault('inputname_' . $inputname, $inputname);
            $mform->setType('inputname_' . $inputname, PARAM_RAW); // Validated in the validation method.
        }

        // PRTs.
        $mform->addElement('header', 'prtsheader', stack_string('prts'));

        foreach ($question->prts as $prtname => $prt) {
            $mform->addElement('text', 'prtname_' . $prtname,
                    stack_string('newnameforx', $prtname), array('size' => 20));
            $mform->setDefault('prtname_' . $prtname, $prtname);
            $mform->setType('prtname_' . $prtname, PARAM_RAW); // Validated in the validation method.
        }

        // PRT nodes.
        foreach ($question->prts as $prtname => $prt) {
            $mform->addElement('header', 'prtnodesheader' . $prtname,
                    stack_string('prtnodesheading', $prtname));

            $graph = $graph = $this->get_prt_graph($prt);
            $newnames = $graph->get_suggested_node_names();
            $mform->addElement('static', $prtname . 'graph', '',
                    stack_abstract_graph_svg_renderer::render($graph, $prtname . 'graphsvg'));

            foreach ($prt->get_nodes_summary() as $nodekey => $notused) {
                $mform->addElement('text', 'nodename_' . $prtname . '_' . $nodekey,
                        stack_string('newnameforx', $nodekey + 1), array('size' => 20));
                $mform->setDefault('nodename_' . $prtname . '_' . $nodekey, $newnames[$nodekey + 1]);
                $mform->setType('nodename_' . $prtname . '_' . $nodekey, PARAM_INT);
            }
        }

        // Submit buttons.
        $this->add_action_buttons(true, stack_string('renamequestionparts'));
    }

    /**
     * Get a stack_abstract_graph represemtatopm of a PRT.
     * @return stack_abstract_graph.
     */
    protected function get_prt_graph($prt) {
        $graph = new stack_abstract_graph();
        foreach ($prt->get_nodes_summary() as $nodekey => $summary) {
            if ($summary->truenextnode == -1) {
                $left = null;
            } else {
                $left = $summary->truenextnode + 1;
            }
            if ($summary->falsenextnode == -1) {
                $right = null;
            } else {
                $right = $summary->falsenextnode + 1;
            }
            $graph->add_node($nodekey + 1, $left, $right,
                    $summary->truescoremode . round($summary->truescore, 2),
                    $summary->falsescoremode . round($summary->falsescore, 2));
        }
        $graph->layout();
        return $graph;
    }

    public function validation($data, $files) {
        $errors = parent::validation($data, $files);
        $question = $this->_customdata;

        // Inputs.
        $inputnames = array();
        foreach ($question->inputs as $inputname => $notused) {
            $field = 'inputname_' . $inputname;
            $proposedname = $data[$field];

            if (!stack_utils::is_valid_name($proposedname)) {
                $errors[$field] = stack_string('notavalidname');

            } else if (array_key_exists($proposedname, $inputnames)) {
                $errors[$field] = stack_string('namealreadyused');

            } else {
                $inputnames[$proposedname] = $inputname;
            }
        }

        // PRTs.
        $prtnames = array();
        foreach ($question->prts as $prtname => $notused) {
            $field = 'prtname_' . $prtname;
            $proposedname = $data[$field];

            if (!stack_utils::is_valid_name($proposedname)) {
                $errors[$field] = stack_string('notavalidname');

            } else if (array_key_exists($proposedname, $prtnames)) {
                $errors[$field] = stack_string('namealreadyused');

            } else {
                $prtnames[$proposedname] = $prtname;
            }
        }

        foreach ($question->prts as $prtname => $prt) {
            $nodenames = array();
            $nodes = $prt->get_nodes_summary();
            foreach ($nodes as $nodekey => $notused) {
                $field = 'nodename_' . $prtname . '_' . $nodekey;
                $proposedname = $data[$field];

                if ($proposedname < 1) {
                    $errors[$field] = stack_string('notavalidname');

                } else if (array_key_exists($proposedname, $nodenames)) {
                    $errors[$field] = stack_string('namealreadyused');

                } else {
                    $nodenames[$proposedname] = $nodekey;
                }
            }
        }

        return $errors;
    }
}
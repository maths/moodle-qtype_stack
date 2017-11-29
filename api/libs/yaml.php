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

require_once('input_values.php');

class qtype_stack_api_yaml {

    private $defaults;
    private $question;

    private function checkkey(&$question, $key, $default) {
        if (!array_key_exists($key, $question)) {
            $question[$key] = $default;
        }
    }

    public function __construct(string $yaml, $defaults) {
        $question = yaml_parse($yaml);
        if ($question === false || !is_array($question)) {
            throw new Exception("can't parse yaml");
        }
        $this->checkkey($question, 'options', array());
        $this->checkkey($question, 'inputs', array());
        $this->checkkey($question, 'response_trees', array());
        $this->defaults = $defaults;
        $this->question = $question;
    }

    /**
     * Apply default values to question
     * @param mixed $question yaml question
     * @param qtype_stack_api_yaml_defaults $defaults
     * @return mixed
     */
    private function apply_defaults($question, $defaults) {
        $defaults->apply($question, 'main');

        $defaults->apply($question['options'], 'options');

        foreach ($question['inputs'] as &$value) {
            $defaults->apply($value, 'input');
        }

        foreach ($question['response_trees'] as &$tree) {
            $defaults->apply($tree, 'tree');
            foreach ($tree['nodes'] as $key => &$value) {
                $defaults->apply($value, 'node');
            }
        }
        return $question;
    }

    /**
     * Convert yaml question values to stack values
     * @param mixed $question yaml question
     */
    private function convert_values(&$question) {
        foreach ($question as $key => &$value) {
            if (is_array($value)) {
                $this->convert_values($value);
            } else {
                $question[$key] = qtype_stack_api_input_values::get_stack_value($question, $key, $value);
            }
        }
    }

    /**
     * Num nodes instead user friendly node names
     * @param mixed $question yaml question
     */
    private function num_nodes(&$question) {
        foreach ($question['response_trees'] as $treename => &$tree) {
            $newnodes = array();
            $nodesmap = array();
            $i = 0;
            // Collect.
            foreach ($tree['nodes'] as $nodename => $node) {
                $nodesmap[$nodename] = $i;

                if (!array_key_exists('answer_note', $node['T'])) {
                    $node['T']['answer_note'] = $treename . '-' . $nodename . '-T';
                }
                if (!array_key_exists('answer_note', $node['F'])) {
                    $node['F']['answer_note'] = $treename . '-' . $nodename . '-F';
                }

                $newnodes[$i] = $node;
                $i++;
            }
            // Replace.
            foreach ($newnodes as $nodename => &$nodenew) {
                if (array_key_exists('next_node', $nodenew['T']) && $nodenew['T']['next_node'] != -1) {
                    $nodenew['T']['next_node'] = $nodesmap[$nodenew['T']['next_node']];
                }
                if (array_key_exists('next_node', $nodenew['F']) && $nodenew['F']['next_node'] != -1) {
                    $nodenew['F']['next_node'] = $nodesmap[$nodenew['F']['next_node']];
                }
            }
            $tree['nodes'] = $newnodes;
            if (array_key_exists('first_node', $tree)) {
                $tree['first_node'] = $nodesmap[$tree['first_node']];
            } else {
                $tree['first_node'] = 0;
            }
        }
    }

    /**
     * Returns question
     * @return mixed
     */
    public function get_question() {
        $question = $this->apply_defaults($this->question, $this->defaults);
        $this->convert_values($question);
        $this->num_nodes($question);
        return $question;
    }
}
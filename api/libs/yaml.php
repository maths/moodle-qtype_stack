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
            throw new Exception("Can't parse yaml.");
        }
        $this->checkkey($question, 'options', array());
        $this->checkkey($question, 'inputs', array());
        $this->checkkey($question, 'response_trees', array());
        $this->checkkey($question, 'worked_solution_html', '');
        $this->checkkey($question, 'variables', '');
        $this->checkkey($question, 'note', '');
        $this->checkkey($question, 'specific_feedback_html', '');
        $this->defaults = $defaults;
        $this->question = $question;
    }

    /**
     * Apply default values to question.
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
     * Convert yaml question values to stack values.  This includes merging separate language versions.
     * @param mixed $question yaml question
     */
    private function convert_values(&$question, $lang) {
        $castextfields = qtype_stack_api_input_values::CONTENT_FIELDS;

        // First consolidate langauges.
        // Note, we don't delete the language versions from the question array.
        foreach ($castextfields as $field) {
            $fieldhtml = $field . '_html_';
            foreach ($question as $key => $value) {
                // Note the 0 is not false here.
                if (stripos($key, $fieldhtml) === 0) {
                    $langfound = str_replace($fieldhtml, '', $key);
                    if ($lang == '') {
                        // We are not looking for a specific language, so consolidate them all.
                        $langwrap = '<span lang="' . $langfound .'" class="multilang">' . $value . "</span>\n";
                        if (array_key_exists($fieldhtml, $question)) {
                            $question[$fieldhtml] .= $langwrap;
                        } else {
                            $question[$fieldhtml] = $langwrap;
                        }
                    }
                    // If we are looking for a specific language and have found it, then just use this value.
                    if ($lang == $langfound) {
                        $question[$fieldhtml] = $value;
                    }
                    // Note, there is no graceful degredation to a default language at this point yet...
                    // However, if you included the $field, this will be used as the default.
                }
            }
        }
        foreach ($question as $key => &$value) {
            if (is_array($value)) {
                $this->convert_values($value, $lang);
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
     * @param string $lang The language code for the specified version.
     * If no string is given, we wrap any langauges in multilang span tags.
     * @return mixed
     */
    public function get_question($lang = '') {
        $question = $this->apply_defaults($this->question, $this->defaults);
        $this->convert_values($question, $lang);
        $this->num_nodes($question);
        return $question;
    }
}

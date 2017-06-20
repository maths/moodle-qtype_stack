<?php

require_once('input_values.php');

class qtype_stack_api_yaml {

    private $defaults;
    private $question;

    function checkKey(&$question, $key, $default) {
        if (!array_key_exists($key, $question)) {
            $question[$key] = $default;
        }
    }

    function qtype_stack_api_yaml(string $yaml, $defaults) {
        $question = yaml_parse($yaml);
        if ($question === FALSE || !is_array($question)) {
            throw new Exception("can't parse yaml");
        }
        $this->checkKey($question, 'options', array());
        $this->checkKey($question, 'inputs', array());
        $this->checkKey($question, 'response_trees', array());
        $this->defaults = $defaults;
        $this->question = $question;
    }

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

    private function convert_values(&$question) {
        foreach ($question as $key => &$value) {
            if (is_array($value)) {
                $this->convert_values($value);
            }
            else {
                $question[$key] = qtype_stack_api_input_values::get_stack_value($question, $key, $value);
            }
        }
    }

    private function num_nodes(&$question) {
        $i = 0;
        $nodes_map = array();
        $new_nodes = array();
        foreach ($question['response_trees'] as $tree_name => &$tree) {
            # collect
            foreach ($tree['nodes'] as $node_name => $node) {
                $nodes_map[$node_name] = $i;

                if (!array_key_exists('answer_note', $node['T'])) {
                    $node['T']['answer_note'] = $tree_name . '-' . $node_name . '-T';
                }
                if (!array_key_exists('answer_note', $node['F'])) {
                    $node['F']['answer_note'] = $tree_name . '-' . $node_name . '-F';
                }

                $new_nodes[$i] = $node;
                $i++;
            }

            # replace
            foreach ($new_nodes as $node_name => &$node) {
                if (array_key_exists('next_node', $node['T']) && $node['T']['next_node'] != -1) {
                    $node['T']['next_node'] = $nodes_map[$node['T']['next_node']];
                }
                if (array_key_exists('next_node', $node['F']) && $node['F']['next_node'] != -1) {
                    $node['F']['next_node'] = $nodes_map[$node['F']['next_node']];
                }
            }
            $tree['nodes'] = $new_nodes;
            if (array_key_exists('first_node', $tree)) {
                $tree['first_node'] = $nodes_map[$tree['first_node']];
            }
            else {
                $tree['first_node'] = 0;
            }
        }
    }

    public function get_question() {
        $question = $this->apply_defaults($this->question, $this->defaults);
        $this->convert_values($question);
        $this->num_nodes($question);
        return $question;
    }
}
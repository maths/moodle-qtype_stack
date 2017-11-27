<?php

class qtype_stack_api_yaml_defaults {

    private $defaults = array();

    public function __construct($defaults) {

        if ($defaults == null) {
            $defaults = yaml_parse_file(__DIR__ . '/defaults/main.yaml');
        } else {
            $defaults = yaml_parse($defaults);
        }
        $this->defaults['branch-T'] = $defaults['response_trees']['tree']['node']['T'];
        $this->defaults['branch-F'] = $defaults['response_trees']['tree']['node']['F'];
        $this->defaults['node'] = $defaults['response_trees']['tree']['node'];
        unset($this->defaults['node']['T']);
        unset($this->defaults['node']['F']);
        $this->defaults['tree'] = $defaults['response_trees']['tree'];
        unset($this->defaults['tree']['node']);
        $this->defaults['input'] = $defaults['input'];
        $this->defaults['options'] = $defaults['options'];
        $this->defaults['main'] = $defaults;
        unset($this->defaults['main']['response_trees']);
        unset($this->defaults['main']['input']);
        unset($this->defaults['main']['options']);

    }

    private function get_root($section) {
        $root = $this->defaults[$section];

        if (!is_array($root)) {
            return array();
        }
        return $root;
    }

    /**
     * Apply default values to node
     * @param $node
     * @param string $section
     */
    public function apply(&$node, $section) {
        $root = $this->get_root($section);
        foreach ($root as $key => $value) {
            if (!array_key_exists($key, $node)) {
                $node[$key] = $value;
            }
            if ($section == 'node') {
                foreach ($this->get_root('branch-T') as $key => $value) {
                    if (!array_key_exists($key, $node['T'])) {
                        $node['T'][$key] = $value;
                    }
                }
                foreach ($this->get_root('branch-F') as $key => $value) {
                    if (!array_key_exists($key, $node['F'])) {
                        $node['F'][$key] = $value;
                    }
                }
            }
        }
    }

    public function isdefault($section, $path, $value) {
        $root = $this->get_root($section);
        if (!array_key_exists($path, $root)) {
            return false;
        }

        return ($root[$path] == $value);
    }
}
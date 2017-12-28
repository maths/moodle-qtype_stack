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

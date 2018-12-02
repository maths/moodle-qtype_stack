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

    /*
     * This function converts the settings from a Moodle site into API defaults.
     * This enables users of Moodle to convert existing STACK questions to yaml without
     * exporting all the default settings.
     */
    public function moodle_settings_to_yaml_defaults($settings) {

        $iv = new qtype_stack_api_input_values();

        $this->defaults['options']['simplify'] = $settings->questionsimplify;
        $this->defaults['options']['assume_positive'] = $settings->assumepositive;
        $this->defaults['options']['assume_real'] = $settings->assumereal;
        $this->defaults['options']['multiplication_sign'] = $settings->multiplicationsign;
        $this->defaults['options']['sqrt_sign'] = $settings->sqrtsign;
        $this->defaults['options']['complex_no'] = $settings->complexno;
        $this->defaults['options']['inverse_trig'] = $settings->inversetrig;
        $this->defaults['options']['matrix_parens'] = $iv->get_yaml_value('matrix_parens', $settings->matrixparens);

        // TODO: 'syntax_attribute' is set in main.yaml, but I can't find it in $settings.
        $this->defaults['input']['box_size'] = $settings->inputboxsize;
        $this->defaults['input']['strict_syntax'] = $settings->inputstrictsyntax;
        $this->defaults['input']['insert_stars'] = $iv->get_yaml_value('insert_stars', $settings->inputinsertstars);
        $this->defaults['input']['forbid_words'] = $settings->inputforbidwords;
        $this->defaults['input']['forbid_float'] = $settings->inputforbidfloat;
        $this->defaults['input']['require_lowest_terms'] = $settings->inputrequirelowestterms;
        $this->defaults['input']['check_answer_type'] = $settings->inputcheckanswertype;
        $this->defaults['input']['must_verify'] = $settings->inputmustverify;
        $this->defaults['input']['show_validations'] = $settings->inputshowvalidation;

        $this->defaults['prt_correct_html'] = $settings->prtcorrect;
        $this->defaults['prt_partially_correct_html'] = $settings->prtincorrect;
        $this->defaults['prt_incorrect_html'] = $settings->prtpartiallycorrect;

        $this->defaults['tests'] = '';
    }

    private function get_root($section) {
        if (!array_key_exists($section, $this->defaults)) {
            return array();
        }

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

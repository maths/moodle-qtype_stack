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
 * @copyright  2013 Aalto University
 * @copyright  2012 University of Birmingham
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(dirname(__FILE__) . '/../conditionalcasstring.class.php');
require_once(dirname(__FILE__) . '/../casstring.class.php');
require_once('external/latex.class.php');

class stack_cas_castext_external extends stack_cas_castext_block {

    private $handler = NULL;

    public function extract_attributes(&$tobeevaluatedcassession,$conditionstack = NULL) {
        $type = $this->get_node()->get_parameter('type',NULL);
        switch ($type) {
            case 'latex':
                $this->handler = new stack_cas_castext_external_latex();
                break;
        }
        if ($this->handler !== NULL) {
            $this->handler->extract_attributes($this->get_node(),$tobeevaluatedcassession,$conditionstack);
        }
    }

    public function content_evaluation_context($conditionstack = array()) {
        return $conditionstack;
    }

    public function process_content($evaluatedcassession,$conditionstack = NULL) {
        global $CFG;

        // Check if the contents have been fully evaluated
        $this->get_node()->normalize();
        if ($this->get_node()->first_child->next_sibling !== NULL) {
            return true;
        }

        $full_content = $this->get_node()->first_child->to_string();
        $name = md5($full_content);

        if ($this->handler === NULL) {
            $this->get_node()->convert_to_text("<pre>UNKNOWN EXTERNAL-TYPE\n".$this->handler->get_replacement_text()."</pre>");
            return false;
        }

        $this->handler->set_name($name);
        $this->handler->set_attributes($evaluatedcassession);

        $generated = $this->handler->get_generated_files();
        $got_all_ready = true;

        foreach ($generated as $file) {
            if (!file_exists($file)) {
                $got_all_ready = false;
            }
        }
        if ($got_all_ready) {
            $this->get_node()->convert_to_text($this->handler->get_replacement_text());
            return false;
        }

        $files = array();
        foreach (explode('### FILE: ',$full_content) as $split) {
            if (count($files) == 0) {
                $files['__SOURCE_CODE__'] = $split;
            } else {
                $key = trim(substr($split,0,strpos($split,'###')));
                $files[$key] = substr($split,strpos($split,'###')+3);
            }
        }

        $label_map = array();
        $i=0;
        $temp_dir = sys_get_temp_dir() . "/$name";
        mkdir($temp_dir);
        foreach (array_keys($files) as $key) {
            $i++;
            if ($key == '__SOURCE_CODE__') {
                $label = $name.$this->handler->get_source_file_extension();
            } else {
                $label = $name.'-'.$i;
                if (strpos($key,'.') !== FALSE) {
                    $possible_file_extension = substr($key,strpos($key,'.'));
                    $ok_extension = true;
                    for ($j = 0; $j<strlen($possible_file_extension); $j++) {
                        $c = substr($possible_file_extension,$j,1);
                        if ($c == '.' || ctype_alnum($c)) {

                        } else {
                            $ok_extension = false;
                        }
                    }
                    if ($ok_extension) {
                        $label .= $possible_file_extension;
                    }
                }
            }
            $label = "$temp_dir/". $label;
            $label_map[$key] = $label;
            foreach (array_keys($files) as $k) {
                $files[$k] = str_replace($key,$label,$files[$k]);
            }
        }

        foreach (array_keys($files) as $key) {
            // so apparently some versions of the editor add mac-line changes... and some software fails with them
            $files[$key] = str_replace("\r","\n",$files[$key]);
            file_put_contents($label_map[$key],$files[$key]);
        }

        $this->handler->process($label_map);

        foreach ($label_map as $key => $file) {
            unlink($file);
        }
        rmdir($temp_dir);


        $this->get_node()->convert_to_text($this->handler->get_replacement_text());
        return false;
    }

    public function validate_extract_attributes() {
        //// TODO this must do something as well as the validation step checking all parameters, expand handler...
        return array();
    }
}

abstract class stack_cas_castext_external_handler {
    public $name;

    public abstract function extract_attributes($node,&$tobeevaluatedcassession,$conditionstack);

    public function set_name($name) {
        $this->name = $name;
    }

    public abstract function set_attributes($evaluatedcassession);

    public abstract function get_generated_files();

    public function get_source_file_extension() {
        return ".txt";
    }

    public abstract function process($label_map);

    public abstract function get_replacement_text();
}


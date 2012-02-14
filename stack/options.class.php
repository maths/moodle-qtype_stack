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
 * Options enable a context to be set for each question, and information
 * made generally available to other classes.
 *
 * @copyright  2012 The University of Birmingham
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
 class stack_options {

    private $options;   // Exactly the CASText entered.

    public function __construct($settings = array()) {

        //i		OptionType can be: boolean, string, html, list
        $this->options  = array( // Array of public class settings for this class.
            'display'   =>  array(
                'type'       =>  'list',
                'value'      =>  'LaTeX',
                'strict'     =>  true,
                'values'     =>  array('LaTeX', 'MathML', 'String'),
                'caskey'     =>  'OPT_OUTPUT',
                'castype'    =>  'string',
             ),
            'multiplicationsign'   =>  array(
                'type'       =>  'list',
                'value'      =>  'dot',
                'strict'     =>  true,
                'values'     =>  array('dot', 'cross', 'none'),
                'caskey'     =>  'make_multsgn',
                'castype'    =>  'fun',
            ),
            'complexno'   =>  array(
                'type'       =>  'list',
                'value'      =>  'i',
                'strict'     =>  true,
                'values'     =>  array('i', 'j', 'symi', 'symj'),
                'caskey'     =>  'make_complexJ',
                'castype'    =>  'fun',
            ),
            'floats'   =>  array(
                'type'       =>  'boolean',
                'value'      =>  1,
                'strict'     =>  true,
                'values'     =>  array(),
                'caskey'     =>  'OPT_NoFloats',
                'castype'    =>  'ex',
            ),
            'sqrtsign'   =>  array(
                'type'       =>  'boolean',
                'value'      =>  1,
                'strict'     =>  true,
                'values'     =>  array(),
                'caskey'     =>  'sqrtdispflag',
                'castype'    =>  'ex',
            ),
            'simplify'   =>  array(
                'type'       =>  'boolean',
                'value'      =>  1,
                'strict'     =>  true,
                'values'     =>  array(),
                'caskey'     =>  'simp',
                'castype'    =>  'ex',
            ),
            'markmodmethod'   =>  array(
                'type'       =>  'list',
                'value'      =>  'penalty',
                'strict'     =>  true,
                'values'     =>  array('penalty', 'firstanswer', 'lastanswer'),
                'caskey'     =>  null,
                'castype'    =>  null,
             ),
            'assumepos'   =>  array(
                'type'       =>  'boolean',
                'value'      =>  0,
                'strict'     =>  true,
                'values'     =>  array(),
                'caskey'     =>  'assume_pos',
                'castype'    =>  'ex',
            ),
            'feedback'   =>  array(
                'type'       =>  'list',
                'value'      =>  'TGS',
                'strict'     =>  true,
                'values'     =>  array('TGS', 'TG', 'GS', 'T', 'G', 'S', 'none'),
                'caskey'     =>  null,
                'castype'    =>  null,
            ),
            'feedbackgenericcorrect'   =>  array(
                'type'       =>  'html',
                'value'      =>  '',
                'strict'     =>  false,
                'values'     =>  array(),
                'caskey'     =>  null,
                'castype'    =>  null,
            ),
            'feedbackgenericincorrect'   =>  array(
                'type'       =>  'html',
                'value'      =>  '',
                'strict'     =>  false,
                'values'     =>  array(),
                'caskey'     =>  null,
                'castype'    =>  null,
            ),
            'feedbackgenericpcorrect'   =>  array(
                'type'       =>  'html',
                'value'      =>  '',
                'strict'     =>  false,
                'values'     =>  array(),
                'caskey'     =>  null,
                'castype'    =>  null,
            ),
        );

        if (!is_array($settings)) {
            throw new Exception('stack_options: $settings must be an array.');
        }

        // Overright them from any input
        foreach ($settings as $key => $val) {
            if (!array_key_exists($key, $this->settings)) {
                throw new Exception('stack_options construct: $key '.$key.' is not a valid option name.');
            } else {
                $this->options[$key] = $val;
            }
        }

}

    /*
     * This function validates the information.
     * TODO: this will need to be refactored to return messages to users who enter data in forms, not just throw exceptions.
     */
    private function validate_key($key,$val) {
        if (!array_key_exists($key, $this->options)) {
            throw new Exception('stack_options set_option: $key '.$key.' is not a valid option name.');
        } 
        $optiontype = $this->options[$key]['type'];
        switch($optiontype) {
            case 'boolean':
                if (!(0===$val || 1===$val)) {
                    throw new Exception('stack_options set boolean option: options store booleans as 1 or 0, not true or false.  Recieved '.$val);
                }
                break;

            case 'string':
                //TODO
                break;

            case 'html':
                //TODO
                break;

            case 'list':
                if (!in_array($val, $this->options[$key]['values'])) {
                    throw new Exception('stack_options set option '.$val.' for '.$key.' is invalid');
                }
                break;
        }
        return true;
    }

    public function get_option($key) {
        if (!array_key_exists($key, $this->options)) {
            throw new Exception('stack_options get_option: $key '.$key.' is not a valid option name.');
        } else {
            return $this->options[$key]['value'];
        }
    }

    public function set_option($key, $val) {
        if ($this->validate_key($key, $val)) {
            $this->options[$key]['value']=$val;
        } // Else an exception will have been thrown.  
          // TODO: return useful errors to users who enter data....
    }

    public function get_cas_commands() {

        $names = '';
        $commands = '';

        foreach ($this->options as $key => $opt) {
            if (null!=$opt['castype']) {
                if ('boolean'===$opt['type']) {
                    if ($opt['value']) {
                        $value = 'true';
                    } else {
                        $value = 'false';
                    }
                } else {
                    $value = $opt['value'];
                }

                if ('ex' == $opt['castype']) {
                    $names      .= ', '.$opt['caskey'];
                    $commands   .= ', '.$opt['caskey'].':'.$value;
                } else if ('fun' == $opt['castype']) {
                    $commands   .= ', '.$opt['caskey'].'('.$value.')';
                }
            }
        }
        $ret = array('names'=>$names, 'commands'=>$commands);
        return $ret;
    }
}

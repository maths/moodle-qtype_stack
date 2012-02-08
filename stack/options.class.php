<?php
// This file is part of Stack - http://stack.bham.ac.uk//
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

class	STACK_options {

    private $options;   // Exactly the CASText entered.
    
    public function __construct($settings = array()) {

    //i		OptionType can be: boolean, string, html, email, url, number, list
    $this->options  = array( // Array of public class settings for this class.
        'display'   =>  array(
            'type'       =>  'list',
            'value'      =>  'LaTeX',
            'strict'     =>  true,
            'values'     =>  array('LaTeX','MathML','String'),
            'caskey'     =>  'OPT_OUTPUT',
            'castype'    =>  'string',
         ),
        'multiplicationsign'   =>  array(
            'type'       =>  'list',
            'value'      =>  'dot',
            'strict'     =>  true,
            'values'     =>  array('dot','cross','none'),
            'caskey'     =>  'make_multsgn',
            'castype'    =>  'fun',
        ),
        'complexno'   =>  array(
            'type'       =>  'list',
            'value'      =>  'i',
            'strict'     =>  true,
            'values'     =>  array('i','j','symi','symj'),
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
            'values'     =>  array('penalty','firstanswer','lastanswer'),
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
            'values'     =>  array('TGS','TG','GS','T','G','S','none'),
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

        if(!is_array($settings))
        {
            throw new Exception('STACK_options: $settings must be an array.');
        }

        // Set up default values
        $this->settings = $this->_settings;

        // Overright them from any input
        foreach ($settings as $key => $val) {
            if (!array_key_exists($key, $this->settings)) {
                throw new Exception('STACK_options construct: $key '.$key.' is not a valid option name.');
            } else {
                $this->options[$key] = $val;
            }
        }

}

    public function getoption($key) {
        if (!array_key_exists($key, $_settings)) {
            throw new Exception('STACK_options getoption: $key '.$key.' is not a valid option name.');
        } else {
            return $this->options[$key];
        }
    }

    public function setoption($key,$val) {
        if (!array_key_exists($key, $_settings)) {
            throw new Exception('STACK_options getoption: $key '.$key.' is not a valid option name.');
        } else {
            $this->options[$key]=$val;
        }
    }

    public function getcascommands(){

        $names = '';
        $commands = '';
        
        foreach ($this->options as $key => $opt){
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
                
                if ('ex'===$opt['castype']) {
                    $names      .= ', '.$opt['caskey'];
                    $commands   .= ', '.$opt['caskey'].':'.$value;
                } else if ('fun' === $opt['castype']) {
                    $commands   .= ', '.$opt['caskey'].'('.$value.')';
                }
            }
        }
        $ret = array('names'=>$names,'commands'=>$commands);
        return $ret;
    }
}

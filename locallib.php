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
 * Base class for all the types of exception we throw.
 */
class stack_exception extends moodle_exception {
    public function __construct($error) {
        parent::__construct('exceptionmessage', 'qtype_stack', '', $error);
    }
}

function stack_string($key, $a = null) {
    return get_string($key, 'qtype_stack', $a);
}

 /**
  * Translates a string taken as output from Maxima.
  *
  * This function takes a variable number of arguments, the first of which is assumed to be the identifier
  * of the string to be translated.
  */
function stack_trans() {
    $nargs = func_num_args();

    if ($nargs>0) {
        $arg_list = func_get_args();
        $identifier = func_get_arg(0);
        $a = array();
        if ($nargs>1) {
            for ($i=1; $i<$nargs; $i++) {
                $index=$i-1;
                $a["m{$index}"] = func_get_arg($i);
            }
        }
        $return = get_string($identifier, 'qtype_stack', $a);
        echo $return;
    }
}

function stack_maxima_translate($rawfeedback) {

    if (strpos($rawfeedback, 'stack_trans') === false) {
        return trim($rawfeedback);
    } else {
        $rawfeedback = str_replace('[[', '', $rawfeedback);
        $rawfeedback = str_replace(']]', '', $rawfeedback);
        $rawfeedback = str_replace('\n', '', $rawfeedback);
        $rawfeedback = str_replace('\\', '\\\\', $rawfeedback);
        $rawfeedback = str_replace('$', '\$', $rawfeedback);
        $rawfeedback = str_replace('!quot!', '"', $rawfeedback);

        ob_start();
        eval($rawfeedback);
        $translated = ob_get_contents();
        ob_end_clean();

        return trim($translated);
    }
}

function stack_maxima_format_casstring($str) {
    return html_writer::tag('span', $str, array('class' => 'stacksyntaxexample'));
}

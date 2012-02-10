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

class STACK_Legacy {

    /**
     * Gets the string between two specified characters.
     *
     * @param int $start The position of the string to start searching at.
     * @param char $leftc Finds the first occurence of this character
     * @param char $rightc Finds the first occurence of this character after $leftc
     * @return array Field [0] contains the string between the two characters, Field [1] contains the start position of -1 if it does not exist and Field [2] contains the end position of -1 if there was no match.
     */
    public static function util_grabbetween($strin, $leftc, $rightc, $start) {
        // Starting at $start, (a number)
        // Find the first occurance of $leftc, match with an occurance of
        // $rightc and grab everything in between.
        // Returns an array:
        // ret[0] = stuff grabbed
        // ret[1] = start position, or -1 if it does not exist
        // ret[2] = end position, or -1 if there is no match
        $strin_len = strlen($strin);
        $start = strpos($strin, $leftc, $start);
        if ($start === false) {
            $ret[0]='';
            $ret[1]=-1;
            $ret[2]=0;
            return $ret;
        }
        if ($leftc == $rightc) { // Left and right are the same
            $end = strpos($strin, $rightc, $start+1); // Just go for the next one
            if ($end === false) {
                $ret[0]='';
                $ret[1]=$start;
                $ret[2]=-1;
                return $ret;
            }
            $end++;
        } else {
            $left_bracket=0;
            $right_bracket=0;
            $end=$start;

            do {
                if ($strin[$end] == $leftc) {
                    $left_bracket++;
                } else if ($strin[$end] == $rightc) {
                    $right_bracket++;
                }

                $end++;
            } while ($left_bracket != $right_bracket and $end<$strin_len);

        }

        $ret[0]= substr($strin, $start, $end-$start);
        $ret[1]= $start;
        $ret[2]= $end-1;

        return $ret;
    }

    /**
     * Translates a string taken as output from Maxima.
     *
     * This function takes a variable number of arguments, the first of which is assumed to be the identifier
     * of the string to be translated.
     */
    public static function trans() {
        $nargs = func_num_args();

        if ($nargs>0) {
            $arg_list = func_get_args();
            $identifier = func_get_arg(0);
            $a = array();
            if ($nargs>1) {
                for ($i=1; $i<$nargs; $i++) {
                    $a[] = func_get_arg($i);
                }
            }
            $return = STACK_Translator::get_string($identifier, 'stack', $a);
            echo $return;
        }
    }

}

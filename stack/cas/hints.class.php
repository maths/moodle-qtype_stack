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
 * The hints class for STACK.
 *
 * @copyright  2014 Loughborough University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class stack_hints {

    private $text;

    /* This is the list of allowable hint tags.  Each of these needs to have
     * two corresponding lines in the language file.
     * E.g. greek_alphabet_name and greek_alphabet_fact
     */
    private static $hints = array('greek_alphabet', 'alg_inequalities',
                    'alg_indices', 'alg_logarithms', 'alg_quadratic_formula',
                    'alg_partial_fractions', 'trig_degrees_radians', 'trig_standard_values',
                    'trig_standard_identities', 'hyp_functions', 'hyp_identities',
                    'hyp_inverse_functions', 'calc_diff_standard_derivatives',
                    'calc_diff_linearity_rule', 'calc_product_rule', 'calc_quotient_rule',
                    'calc_chain_rule', 'calc_rules', 'calc_int_standard_integrals',
                    'calc_int_linearity_rule', 'calc_int_methods_substitution',
                    'calc_int_methods_parts');

    public function __construct($text) {

        if (!is_string($text)) {
            throw new stack_exception('stack_hints: text, must be a string.');
        }

        $this->text = $text;
    }

    /* Check each hint tag actually corresponds to a valid hint. */
    public function validate() {
        $strin = $this->text;
        $html_match = $this->get_hint_tags();
        if ($html_match) {
            $errors = array();
            foreach ($html_match as $val) {
                if (false === array_search($val, self::$hints)) {
                    $errors[] = $val;
                }
            }
            if (!empty($errors)) {
                return $errors;
            }
        }
        return true;
    }

    private function get_hint_tags() {
        if (preg_match_all('|\[\[hint:(.*)\]\]|U', $this->text, $html_match)) {
            return $html_match[1];
        }
        return false;
    }

    /* This function repaces tags with they HTML value.
     * Note, that at this point we assume we have already validated the text.
     */
    public function display() {

        $strin = $this->text;
        $html_match = $this->get_hint_tags();
        if ($html_match) {
            global $CFG;
            $stackurl = $CFG->wwwroot . '/question/type/stack/';

            //$modal_script= "<script type='text/javascript' src='http://ajax.googleapis.com/ajax/libs/jquery/1.4.3/jquery.min.js?ver=1.4.3'></script><script type='text/javascript' src='".$stackurl."jquery.simplemodal.1.4.3.min.js'></script>";
            //$strin = $modal_script.$strin; // prepend script
            foreach ($html_match as $val) {
                if (false !== array_search($val, self::$hints)) {
                    $sr = '[[hint:'.$val.']]';
                    /*
                    //This uses a popup button to display the hint. 
                    //Currently not working.
                    $rep = $this->modal_popup(stack_string($val.'_name'),
                            stack_string($val.'_fact'),//body
                            'Hint' //label on button
                           );
                    */
                    $rep = '<div class="secondaryFeedback"><h3 class="secondaryFeedback">' .
                       stack_string($val.'_name') . '</h3>' . stack_string($val . '_fact') . '</div>';
                    $strin = str_replace($sr, $rep, $strin);
                } else {
                    throw new stack_exception('stack_hints: the following hint tag does not exist: '.$val);
                }
            }
        }
        return $strin;
    }

    // Generate a random string (letters and numbers) of length $length
    private function genRandomString($length) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
        $string = '';
        for ($p = 0; $p < $length; $p++) {
            $string .= $characters[mt_rand(0, strlen($characters)-1)];
        }
        return $string;
    }

    private function modal_popup($header, $body, $buttonlabel) {
        global $CFG;
        $stackurl = $CFG->wwwroot . '/question/type/stack/';

        $hint = $this->genRandomString(10);
        //   <!--<img align="middle" border="0" alt="Hint?" src="'.$stackurl.'pix/help.png" />-->
        return
        '<span id="'.$hint.'">
   <input type="button"  value="'.$buttonlabel.'" class="modal-button"/>
</span>
<div id="'.$hint.'2" style="display: none">
  <div class="secondaryFeedback">
    <h3 class="secondaryFeedback">'.$header.'</h3>'.
        $body.'
  </div>
</div>
<div style="display:none">
   <img src="'.$stackurl.'pix/x.png" alt="X" />
</div>
<script>$("#'.$hint.'").click(function(e) {$("#'.$hint.'2").modal(); return false;});</script>';
    }

    /* This function converts the old style html tags to the new hint
     * system using square brackets.
     */
    public function legacy_convert() {
        preg_match_all('|<hint>(.*)</hint>|U', $this->text, $html_match);
        foreach($html_match[1] as $key => $val) {
            $old = $html_match[0][$key];
            $new = '[[hint:'.trim($val).']]';
            $this->text = str_replace($old, $new, $this->text);
        }
        return $this->text;
    }

    /* This function returns the html to insert into the documentaion. 
     * It ensures that all/only the current tags are included in the docs.
     * Note, docs are usually in markdown, but we have html here because 
     * hints are part of castext.
     */
    public function gen_docs() {
        $doc = '';
        foreach (self::$hints as $tag) {
            $doc .= '<h4>'.stack_string($tag.'_name').'</h4> [[hint:'.$tag."]]";
            $doc .= '<p>'.stack_string($tag.'_fact').'</p>';
        }
        return $doc;
    }
}

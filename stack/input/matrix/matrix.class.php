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
 * A basic text-field input.
 *
 * @copyright  2012 University of Birmingham
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class stack_matrix_input extends stack_input {
    protected $errors = null;
    protected $width;
    protected $height;

    public function adapt_to_model_answer($teacheranswer) {

        // Work out how big the matrix should be from the INSTANTIATED VALUE of the teacher's answer.
        $cs =  new stack_cas_casstring('ta:matrix_size(' . $teacheranswer . ')');
        $cs->validate('t');
        $at1 = new stack_cas_session(array($cs), null, 0);
        $at1->instantiate();

        if ('' != $at1->get_errors()) {
            $this->errors = $at1->get_errors();
            return;
        }

        $size = $at1->get_value_key('ta');
        $dimensions = explode(',', $size);

        $this->height = trim($dimensions[0], '[]');
        $this->width = trim($dimensions[1], '[]');
    }

    public function get_expected_data() {
        $expected = array();

        // All the matrix elements.
        for ($i = 0; $i < $this->height; $i++)  {
            for ($j = 0; $j < $this->width; $j++) {
                $expected[$this->name . '_sub_' . $i . '_' . $j] = PARAM_RAW;
            }
        }

        // I am assuming that the valdiation will write one CAS string in a
        // hidden input, that is the combination of all the separate inputs.
        if ($this->requires_validation()) {
            $expected[$this->name . '_val'] = PARAM_RAW;
        }
        return $expected;
    }

    public function render(stack_input_state $state, $fieldname, $readonly) {
        $attributes = array(
            'type' => 'text',
            'name' => $fieldname,
        );

        if ($this->errors) {
            // If there are errors, don't try to display anything else.
            return html_writer::tag('p', $this->errors, array('id' => 'error', 'class' => 'p'));
        }

        // Build an empty array.
        $firstrow = array_fill(0, $this->width, '');
        $tc       = array_fill(0, $this->height, $firstrow);

        // Turn the student's answer into a PHP array.
        if ('' != $state->contents) {
            $t = trim($state->contents);
            $rows = $this->modinput_tokenizer(substr($t, 7, -1));  // array("[a,b]","[c,d]");
            for($i=0; $i < count($rows); $i++) {
                $row = $this->modinput_tokenizer(substr($rows[$i], 1, -1));
                $tc[$i] = $row;
            }
        }

        // Build the html table to contain these values.
        $xhtml = '<table class="matrixtable" style="display:inline; vertical-align: middle;" border="0" cellpadding="1" cellspacing="0"><tbody>';
        for ($i=0; $i < $this->height; $i++)  {
            $xhtml .= '<tr>';
            if($i == 0) {
                $xhtml .= '<td style="border-width: 2px 0px 0px 2px; padding-top: 0.5em">&nbsp;</td>';
            } elseif ($i == ($this->height - 1)) {
                $xhtml .= '<td style="border-width: 0px 0px 2px 2px;">&nbsp;</td>';
            } else {
                $xhtml .= '<td style="border-width: 0px 0px 0px 2px;">&nbsp;</td>';
            }

            for ($j=0; $j < $this->width; $j++) {
                $name = $fieldname.'_sub_'.$i.'_'.$j;
                $xhtml .= '<td><input type="text" name="'.$name.'" value="'.$tc[$i][$j].'" size="'.$this->parameters['boxWidth'].'" ></td>';
            }

            if ($i == 0) {
                $xhtml .= '<td style="border-width: 2px 2px 0px 0px; padding-top: 0.5em">&nbsp;</td>';
            } elseif ($i == ($this->height - 1)) {
                $xhtml .= '<td style="border-width: 0px 2px 2px 0px; padding-bottom: 0.5em">&nbsp;</td>';
            } else {
                $xhtml .= '<td style="border-width: 0px 2px 0px 0px;">&nbsp;</td>';
            }
            $xhtml .= '</tr>';
        }
        $xhtml .= '</tbody></table>';

        return $xhtml;
    }

    public function add_to_moodleform_testinput(MoodleQuickForm $mform) {
        $mform->addElement('text', $this->name, $this->name, array('size' => $this->parameters['boxWidth']));
        $mform->setDefault($this->name, $this->parameters['syntaxHint']);
    }

    /**
     * Return the default values for the parameters.
     * @return array parameters` => default value.
     */
    public static function get_parameters_defaults() {
        return array(
            'mustVerify'     => true,
            'hideFeedback'   => false,
            'boxWidth'       => 5,
            'strictSyntax'   => false,
            'insertStars'    => false,
            'syntaxHint'     => '',
            'forbidWords'    => '',
            'forbidFloats'   => true,
            'lowestTerms'    => true,
            'sameType'       => true);
    }

    /**
     * Each actual extension of this base class must decide what parameter values are valid
     * @return array of parameters names.
     */
    public function internal_validate_parameter($parameter, $value) {
        $valid = true;
        switch($parameter) {
            case 'boxWidth':
                $valid = is_int($value) && $value>0;
                break;
        }
        return $valid;
    }

    /**
    * Takes comma separated list of elements and returns them as an array
    * while at the same time making sure that the braces stay balanced
    *
    * _tokenizer("[1,2]") => array("[1,2]")
    * _tokenizer("1,2") = > array("1","2")
    * _tokenizer("1,1/sum([1,3]),matrix([1],[2])") => array("1","1/sum([1,3])","matrix([1],[2])")
    *
    * $t = trim("matrix([a,b],[c,d])");
    * $rows = _tokenizer(substr($t, 7, -1));  // array("[a,b]","[c,d]");
    * $firstRow = _tokenizer(substr($rows[0],1,-1)); // array("a","b");
    *
    * @author Matti Harjula
    *
    * @param string $in
    * @access private
    * @return array with the parsed elements, if no elements then array
    *         contains only the input string
    */
    private function modinput_tokenizer($in) {
        $bracecount = 0;
        $parenthesiscount = 0;
        $bracketcount = 0;

        $out = array ();

        $current = '';
        $unplaced = 0;
        for ($i = 0; $i < strlen($in); $i++) {
            $unplaced++;
            $char = $in[$i];
            switch ($char) {
                case '{':
                    $bracecount++;
                    $current .= $char;
                    break;
                case '}':
                    $bracecount--;
                    $current .= $char;
                    break;
                case '(':
                    $parenthesiscount++;
                    $current .= $char;
                    break;
                case ')':
                    $parenthesiscount--;
                    $current .= $char;
                    break;
                case '[':
                    $bracketcount++;
                    $current .= $char;
                    break;
                case ']':
                    $bracketcount--;
                    $current .= $char;
                    break;
                case ',':
                    if ($bracketcount == 0 && $parenthesiscount == 0 && $bracecount == 0) {
                        $out[] = $current;
                        $current = '';
                        $unplaced = 0;
                    } else {
                        $current .= $char;
                    }
                    break;
                default;
                    $current .= $char;
            }
        }

        if ($unplaced > 0 && $bracketcount == 0 && $parenthesiscount == 0 && $bracecount == 0) {
            $out[] = $current;
        }

        return $out;
    }

}
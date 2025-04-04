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

/**
 * A basic text-field input.
 *
 * @package    qtype_stack
 * @copyright  2012 University of Birmingham
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class stack_matrix_input extends stack_input {
    // phpcs:ignore moodle.Commenting.VariableComment.Missing
    protected $width;
    // phpcs:ignore moodle.Commenting.VariableComment.Missing
    protected $height;

    // phpcs:ignore moodle.Commenting.VariableComment.Missing
    protected $extraoptions = [
        'hideanswer' => false,
        'allowempty' => false,
        'nounits' => false,
        'simp' => false,
        'consolidatesubscripts' => false,
        'checkvars' => 0,
        'validator' => false,
        'feedback' => false,
    ];

    // phpcs:ignore moodle.Commenting.MissingDocblock.Function
    public function adapt_to_model_answer($teacheranswer) {

        // Work out how big the matrix should be from the INSTANTIATED VALUE of the teacher's answer.
        $cs = stack_ast_container::make_from_teacher_source('matrix_size(' . $teacheranswer . ')');
        $cs->get_valid();
        $at1 = new stack_cas_session2([$cs], null, 0);
        $at1->instantiate();

        if ('' != $at1->get_errors()) {
            $this->errors[] = $at1->get_errors();
            return;
        }

        // These are ints...
        $this->height = $cs->get_list_element(0, true)->value;
        $this->width = $cs->get_list_element(1, true)->value;
    }

    // phpcs:ignore moodle.Commenting.MissingDocblock.Function
    public function get_expected_data() {
        $expected = [];

        // All the matrix elements.
        for ($i = 0; $i < $this->height; $i++) {
            for ($j = 0; $j < $this->width; $j++) {
                $expected[$this->name . '_sub_' . $i . '_' . $j] = PARAM_RAW;
            }
        }

        // The valdiation will write one CAS string in a
        // hidden input, that is the combination of all the separate inputs.
        if ($this->requires_validation()) {
            $expected[$this->name . '_val'] = PARAM_RAW;
        }
        return $expected;
    }

    /**
     * Decide if the contents of this attempt is blank.
     *
     * @param array $contents a non-empty array of the student's input as a split array of raw strings.
     * @return string any error messages describing validation failures. An empty
     *      string if the input is valid - at least according to this test.
     */
    protected function is_blank_response($contents) {
        $allblank = true;
        foreach ($contents as $row) {
            foreach ($row as $val) {
                if (!('' == trim($val) || '?' == $val || 'null' == $val)) {
                    $allblank = false;
                }
            }
        }
        return $allblank;
    }

    /**
     * Converts the input passed in via many input elements into a raw Maxima matrix
     *
     * @param string $in
     * @return string
     */
    public function response_to_contents($response) {
        // At the start of an attempt we will have a completely blank matrix.
        // This must be spotted and a blank attempt returned.
        $allblank = true;

        $matrix = [];
        for ($i = 0; $i < $this->height; $i++) {
            $row = [];
            for ($j = 0; $j < $this->width; $j++) {
                $element = '';
                if (array_key_exists($this->name . '_sub_' . $i . '_' . $j, $response)) {
                    $element = trim($response[$this->name . '_sub_' . $i . '_' . $j]);
                }
                if ('' == $element) {
                    $element = '?';  // Ensures all matrix elements have something non-empty.
                } else {
                    $allblank = false;
                }
                $row[] = $element;
            }
            $matrix[] = $row;
        }

        // We need to build a special definitely blank matrix of the correct shape.
        if ($allblank && $this->get_extra_option('allowempty')) {
            $matrix = [];
            for ($i = 0; $i < $this->height; $i++) {
                $row = [];
                for ($j = 0; $j < $this->width; $j++) {
                    $row[] = 'null';
                }
                $matrix[] = $row;
            }
        }
        return $matrix;
    }

    // phpcs:ignore moodle.Commenting.MissingDocblock.Function
    public function contents_to_maxima($contents) {
        $matrix = [];
        foreach ($contents as $row) {
            $matrix[] = '['.implode(',', $row).']';
        }
        return 'matrix('.implode(',', $matrix).')';
    }

    /**
     * Takes a Maxima matrix object and returns an array of values.
     * @return array
     */
    private function maxima_to_array($in) {

        // Build an empty array.
        $firstrow = array_fill(0, $this->width ?? 0, '');
        $tc       = array_fill(0, $this->height ?? 0, $firstrow);

        // Turn the student's answer, syntax hint, etc., into a PHP array.
        $t = trim($in);
        if ('matrix(' == substr($t, 0, 7)) {
            // @codingStandardsIgnoreStart
            // E.g. array("[a,b]","[c,d]").
            // @codingStandardsIgnoreEnd
            $rows = $this->modinput_tokenizer(substr($t, 7, -1));
            for ($i = 0; $i < count($rows); $i++) {
                $row = $this->modinput_tokenizer(substr(trim($rows[$i]), 1, -1));
                $tc[$i] = $row;
            }
        }

        return $tc;
    }

    /**
     * This is the basic validation of the student's "answer".
     * This method is only called in the input is not blank.
     *
     * Only a few input methods need to modify this method.
     * For example, Matrix types have two dimensional arrays to loop over.
     *
     * @param array $contents the content array of the student's input.
     * @return array of the validity, errors strings and modified contents.
     */
    protected function validate_contents($contents, $basesecurity, $localoptions) {

        $errors = [];
        $notes = [];
        $valid = true;

        list ($secrules, $filterstoapply) = $this->validate_contents_filters($basesecurity);
        // Separate rules for inert display logic, which wraps floats with certain functions.
        $secrulesd = clone $secrules;
        $secrulesd->add_allowedwords('dispdp,displaysci');

        // Now validate the input as CAS code.
        $modifiedcontents = [];
        foreach ($contents as $row) {
            $modifiedrow = [];
            foreach ($row as $val) {
                // Any student input which is too long is not even parsed.
                if (strlen($val) > $this->maxinputlength) {
                    $valid = false;
                    $errors[] = stack_string('studentinputtoolong');
                    $notes['too_long'] = true;
                    $val = '';
                }

                $answer = stack_ast_container::make_from_student_source($val, '', $secrules, $filterstoapply,
                    [], 'Root', $this->options->get_option('decimals'));
                if ($answer->get_valid()) {
                    $modifiedrow[] = $answer->get_inputform();
                } else {
                    $modifiedrow[] = 'EMPTYANSWER';
                }
                $valid = $valid && $answer->get_valid();
                $errors[] = $answer->get_errors();
                $note = $answer->get_answernote(true);
                if ($note) {
                    foreach ($note as $n) {
                        $notes[$n] = true;
                    }
                }
            }
            $modifiedcontents[] = $modifiedrow;
        }
        // Construct one final "answer" as a single maxima object.
        // In the case of matrices (where $caslines are empty) create the object directly here.
        // As this will create a matrix we need to check that 'matrix' is not a forbidden word.
        // Should it be a forbidden word it gets still applied to the cells.
        if (isset(stack_cas_security::list_to_map($this->get_parameter('forbidWords', ''))['matrix'])) {
            $modifiedforbid = str_replace('\,', 'COMMA_TAG', $this->get_parameter('forbidWords', ''));
            $modifiedforbid = explode(',', $modifiedforbid);
            array_map('trim', $modifiedforbid);
            unset($modifiedforbid[array_search('matrix', $modifiedforbid)]);
            $modifiedforbid = implode(',', $modifiedforbid);
            $modifiedforbid = str_replace('COMMA_TAG', '\,', $modifiedforbid);
            $secrules->set_forbiddenwords(trim($modifiedforbid));
            // Cumbersome, and cannot deal with matrix being within an alias...
            // But first iteration and so on.
        }
        $value = $this->contents_to_maxima($modifiedcontents);
        // Sanitised above.
        $answer = stack_ast_container::make_from_teacher_source($value, '', $secrules);
        $answer->get_valid();

        $inertform = stack_ast_container::make_from_student_source($value, '', $secrules,
            array_merge($filterstoapply, ['910_inert_float_for_display', '912_inert_string_for_display']),
            [], 'Root', '.');
        $inertform->get_valid();

        $caslines = [];
        return [$valid, $errors, $notes, $answer, $caslines, $inertform, $caslines];
    }

    // phpcs:ignore moodle.Commenting.MissingDocblock.Function
    public function render(stack_input_state $state, $fieldname, $readonly, $tavalue) {

        if ($this->errors) {
            return $this->render_error($this->errors);
        }

        $tc = $state->contents;
        $blank = $this->is_blank_response($state->contents);
        $useplaceholder = false;
        if ($blank) {
            $syntaxhint = $this->parameters['syntaxHint'];
            if (trim($syntaxhint) != '') {
                $tc = $this->maxima_to_array($syntaxhint);
                if ($this->parameters['syntaxAttribute'] == '1') {
                    $useplaceholder = true;
                }
                $blank = false;
            }
        }
        $attr = [
            'size'  => $this->parameters['boxWidth'],
            'autocapitalize' => 'none',
            'spellcheck'     => 'false',
        ];
        if ($readonly) {
            $attr['readonly'] = 'readonly';
        }

        // Metadata for JS users.
        $attr['data-stack-input-type'] = 'matrix';
        if ($this->options->get_option('decimals') === ',') {
            $attr['data-stack-input-decimal-separator'] = ",";
            $attr['data-stack-input-list-separator'] = ";";
        } else {
            $attr['data-stack-input-decimal-separator'] = ".";
            $attr['data-stack-input-list-separator'] = ",";
        }

        // Read matrix bracket style from options.
        $matrixbrackets = 'matrixsquarebrackets';
        $matrixparens = $this->options->get_option('matrixparens');
        if ($matrixparens == '(') {
            $matrixbrackets = 'matrixroundbrackets';
        } else if ($matrixparens == '|') {
            $matrixbrackets = 'matrixbarbrackets';
        } else if ($matrixparens == '') {
            $matrixbrackets = 'matrixnobrackets';
        }
        // Build the html table to contain these values.
        $xhtml = '<div class="' . $matrixbrackets . '"><table class="matrixtable" id="' . $fieldname .
                '_container" style="display:inline; vertical-align: middle;" ' .
                'cellpadding="1" cellspacing="0"><tbody>';
        for ($i = 0; $i < $this->height; $i++) {
            $xhtml .= '<tr>';
            if ($i == 0) {
                $xhtml .= '<td style="padding-top: 0.5em">&nbsp;</td>';
            } else if ($i == ($this->height - 1)) {
                $xhtml .= '<td>&nbsp;</td>';
            } else {
                $xhtml .= '<td>&nbsp;</td>';
            }

            for ($j = 0; $j < $this->width; $j++) {
                $val = '';
                if (!$blank) {
                    $val = trim($tc[$i][$j]);
                }
                if ($val === 'null' || $val === 'EMPTYANSWER') {
                    $val = '';
                }
                $field = 'value';
                if ($useplaceholder) {
                    $field = 'placeholder';
                }
                $name = $fieldname.'_sub_'.$i.'_'.$j;
                $html   = html_writer::empty_tag('input',
                    array_merge(['type' => 'text', 'id'  => $name, 'name'  => $name, $field => $val], $attr));
                $xhtml .= html_writer::tag('td', $html);
            }

            if ($i == 0) {
                $xhtml .= '<td style="padding-top: 0.5em">&nbsp;</td>';
            } else if ($i == ($this->height - 1)) {
                $xhtml .= '<td style="padding-bottom: 0.5em">&nbsp;</td>';
            } else {
                $xhtml .= '<td>&nbsp;</td>';
            }
            $xhtml .= '</tr>';
        }
        $xhtml .= '</tbody></table></div>';

        return $xhtml;
    }

    // phpcs:ignore moodle.Commenting.MissingDocblock.Function
    public function render_api_data($tavalue) {
        if ($this->errors) {
            throw new stack_exception("Error rendering input: " . implode(',', $this->errors));
        }

        $data = [];

        $data['type'] = 'matrix';

        $syntaxhint = $this->parameters['syntaxHint'];
        $data['syntaxHint'] = null;
        if (trim($syntaxhint) != '') {
            $data['syntaxHint'] = $this->maxima_to_array($syntaxhint);
        }

        // Read matrix bracket style from options.
        $matrixbrackets = 'matrixroundbrackets';
        $matrixparens = $this->options->get_option('matrixparens');
        if ($matrixparens == '[') {
            $matrixbrackets = 'matrixsquarebrackets';
        } else if ($matrixparens == '|') {
            $matrixbrackets = 'matrixbarbrackets';
        } else if ($matrixparens == '') {
            $matrixbrackets = 'matrixnobrackets';
        }

        $data['matrixbrackets'] = $matrixbrackets;
        $data['boxWidth'] = $this->parameters['boxWidth'];
        $data['width'] = $this->width;
        $data['height'] = $this->height;

        return $data;
    }

    /**
     * Transforms a Maxima expression into an array of raw inputs which are part of a response.
     * Most inputs are very simple, but textarea and matrix need more here.
     *
     * @param array|string $in
     * @return string
     */
    public function maxima_to_response_array($in) {
        $response = [];
        $tc = $this->maxima_to_array($in);

        for ($i = 0; $i < $this->height; $i++) {
            for ($j = 0; $j < $this->width; $j++) {
                $val = trim($tc[$i][$j]);
                if ('?' == $val) {
                    $val = '';
                }
                $response[$this->name.'_sub_'.$i.'_'.$j] = $val;
            }
        }

        if ($this->requires_validation()) {
            $response[$this->name . '_val'] = $in;
        }
        return $response;

    }

    // phpcs:ignore moodle.Commenting.MissingDocblock.Function
    public function add_to_moodleform_testinput(MoodleQuickForm $mform) {
        $mform->addElement('text', $this->name, $this->name, ['size' => $this->parameters['boxWidth']]);
        $mform->setDefault($this->name, $this->parameters['syntaxHint']);
        $mform->setType($this->name, PARAM_RAW);
    }

    /**
     * Return the default values for the parameters.
     * @return array parameters` => default value.
     */
    public static function get_parameters_defaults() {
        return [
            'mustVerify'         => true,
            'showValidation'     => 1,
            'boxWidth'           => 5,
            'insertStars'        => 0,
            'syntaxHint'         => '',
            'syntaxAttribute'    => 0,
            'forbidWords'        => '',
            'allowWords'         => '',
            'forbidFloats'       => true,
            'lowestTerms'        => true,
            'sameType'           => true,
            'options'            => '',
        ];
    }

    /**
     * Each actual extension of this base class must decide what parameter values are valid.
     * @return array of parameters names.
     */
    public function internal_validate_parameter($parameter, $value) {
        $valid = true;
        switch($parameter) {
            case 'boxWidth':
                $valid = is_int($value) && $value > 0;
                break;
        }
        return $valid;
    }

    // phpcs:ignore moodle.Commenting.MissingDocblock.Function
    public function get_correct_response($value) {

        if (trim($value) == 'EMPTYANSWER' || $value === null) {
            $value = '';
        }
        // TO-DO: refactor this ast creation away.
        $cs = stack_ast_container::make_from_teacher_source($value, '', new stack_cas_security(), []);
        $cs->set_nounify(0);

        // Hard-wire to strict Maxima syntax.
        $decimal = '.';
        $listsep = ',';
        $params = [
            'checkinggroup' => true,
            'qmchar' => false,
            'pmchar' => 1,
            'nosemicolon' => true,
            'keyless' => true,
            'dealias' => false, // This is needed to stop pi->%pi etc.
            'nounify' => 0,
            'nontuples' => false,
            'decimal' => $decimal,
            'listsep' => $listsep,
        ];
        if ($cs->get_valid()) {
            $value = $cs->ast_to_string(null, $params);
        }
        $response = $this->maxima_to_response_array($value);

        // Once we have the correct array, within the array, use the correct decimal separator.
        $decimal = '.';
        $listsep = ',';
        if ($this->options->get_option('decimals') === ',') {
            $decimal = ',';
            $listsep = ';';
        }
        $params = [
            'checkinggroup' => true,
            'qmchar' => false,
            'pmchar' => 1,
            'nosemicolon' => true,
            'keyless' => true,
            'dealias' => false, // This is needed to stop pi->%pi etc.
            'nounify' => 0,
            'nontuples' => false,
            'decimal' => $decimal,
            'listsep' => $listsep,
        ];
        foreach ($response as $ckey => $cell) {
            $cs = stack_ast_container::make_from_teacher_source($cell, '', new stack_cas_security(), []);
            $cs->set_nounify(0);
            if ($cs->get_valid()) {
                $response[$ckey] = $cs->ast_to_string(null, $params);
            }
        }
        return $response;
    }

    /**
     * The AJAX instant validation method mostly returns a Maxima expression.
     * Mostly, we need an array, labelled with the input name.
     *
     * The matrix type is different.  The javascript creates a JSON encoded object,
     * and we need to split this up into an array of individual elements.
     *
     * @param string $in
     * @return array
     */
    protected function ajax_to_response_array($in) {

        $tc = json_decode($in);
        for ($i = 0; $i < $this->height; $i++) {
            for ($j = 0; $j < $this->width; $j++) {
                $val = trim($tc[$i][$j]);
                if ('?' == $val) {
                    $val = '';
                }
                $response[$this->name.'_sub_'.$i.'_'.$j] = $val;
            }
        }

        if ($this->requires_validation()) {
            $response[$this->name . '_val'] = $in;
        }
        return $response;
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
     * @return array with the parsed elements, if no elements then array
     *         contains only the input string
     */
    public function modinput_tokenizer($in) {
        $bracecount = 0;
        $parenthesiscount = 0;
        $bracketcount = 0;

        $out = [];

        $current = '';
        for ($i = 0; $i < strlen($in); $i++) {
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
                    } else {
                        $current .= $char;
                    }
                    break;
                default;
                    $current .= $char;
            }
        }

        if ($bracketcount == 0 && $parenthesiscount == 0 && $bracecount == 0) {
            $out[] = $current;
        }

        return $out;
    }

    /**
     * Function added for API support
     */
    public function get_api_solution($ta) {
        // We dont want to include the inputname in the solution, therefore we clear the name,
        // and set it back later after saving the solution.
        $name = $this->name;
        $this->name = '';

        $solution = $this->maxima_to_response_array($ta);

        $this->name = $name;

        return $solution;
    }
}

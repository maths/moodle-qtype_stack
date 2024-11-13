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
 * An input which provides a matrix input of variable size.
 * Lots in common with the textarea class.
 *
 * @copyright  2019 Ruhr University Bochum
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class stack_varmatrix_input extends stack_input {

    protected $extraoptions = [
        'hideanswer' => false,
        'allowempty' => false,
        'simp' => false,
        'rationalized' => false,
        'consolidatesubscripts' => false,
        'checkvars' => 0,
        'validator' => false,
        'monospace' => false,
    ];

    protected function is_blank_response($contents) {
        if ($contents == ['EMPTYANSWER']) {
            return true;
        }
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

    public function render(stack_input_state $state, $fieldname, $readonly, $tavalue) {
        // Note that at the moment, $this->boxHeight and $this->boxWidth are only
        // used as minimums. If the current input is bigger, the box is expanded.

        if ($this->errors) {
            return $this->render_error($this->errors);
        }

        $size = $this->parameters['boxWidth'] * 0.9 + 0.1;
        $attributes = [
            'name'           => $fieldname,
            'id'             => $fieldname,
            'autocapitalize' => 'none',
            'spellcheck'     => 'false',
            'class'          => 'varmatrixinput',
            'size'           => $this->parameters['boxWidth'] * 1.1,
            'style'          => 'width: '.$size.'em',
        ];

        if ($this->extraoptions['monospace']) {
            $attributes['class'] .= ' input-monospace';
        }

        if ($this->is_blank_response($state->contents)) {
            $current = $this->maxima_to_raw_input($this->parameters['syntaxHint']);
            if ($this->parameters['syntaxAttribute'] == '1') {
                $attributes['placeholder'] = $current;
                $current = '';
            }
        } else {
            $current = [];
            foreach ($state->contents as $row) {
                $current[] = implode(" ", $row);
            }
            $current = implode("\n", $current);
        }

        // Sort out size of text area.
        $sizecontent = $current;
        if ($this->options && $this->options->get_option('decimals') == ',') {
            // The utility list_to_array expects commas to have meaning at this point.
            $sizecontent = str_replace(',', '.', $sizecontent);
        }
        if ($this->is_blank_response($state->contents) && $this->parameters['syntaxAttribute'] == '1') {
            $sizecontent = $attributes['placeholder'];
        }
        $rows = stack_utils::list_to_array($sizecontent, false);
        $attributes['rows'] = max(5, count($rows) + 1);

        $boxwidth = $this->parameters['boxWidth'];
        foreach ($rows as $row) {
            $boxwidth = max($boxwidth, strlen($row) + 5);
        }
        $attributes['cols'] = $boxwidth;

        if ($readonly) {
            $attributes['readonly'] = 'readonly';
        }

        // Read matrix bracket style from options.
        // The default brackets for matrices are square in options.
        $matrixbrackets = 'matrixsquarebrackets';
        if ($this->options) {
            $matrixparens = $this->options->get_option('matrixparens');
            if ($matrixparens == '(') {
                $matrixbrackets = 'matrixroundbrackets';
            } else if ($matrixparens == '|') {
                $matrixbrackets = 'matrixbarbrackets';
            } else if ($matrixparens == '') {
                $matrixbrackets = 'matrixnobrackets';
            }
        }

        // Metadata for JS users.
        $attributes['data-stack-input-type'] = 'varmatrix';
        if ($this->options->get_option('decimals') === ',') {
            $attributes['data-stack-input-decimal-separator']  = ',';
            $attributes['data-stack-input-list-separator'] = ';';
        } else {
            $attributes['data-stack-input-decimal-separator']  = '.';
            $attributes['data-stack-input-list-separator'] = ',';
        }

        $xhtml = html_writer::tag('textarea', htmlspecialchars($current, ENT_COMPAT), $attributes);
        return html_writer::tag('div', $xhtml, ['class' => $matrixbrackets]);
    }

    public function render_api_data($tavalue) {
        if ($this->errors) {
            throw new stack_exception("Error rendering input: " . implode(',', $this->errors));
        }

        $data = [];

        $data['type'] = 'varmatrix';
        $data['boxWidth'] = $this->parameters['boxWidth'];
        $data['syntaxHint'] = $this->maxima_to_raw_input($this->parameters['syntaxHint']);

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

        return $data;
    }

    public function add_to_moodleform_testinput(MoodleQuickForm $mform) {
        $mform->addElement('text', $this->name, $this->name, ['size' => $this->parameters['boxWidth']]);
        $mform->setDefault($this->name, $this->parameters['syntaxHint']);
        $mform->setType($this->name, PARAM_RAW);
    }

    /**
     * Transforms the student's response input into an array.
     * Most return the same as went in.
     *
     * @param array|string $in
     * @return array
     */
    protected function response_to_contents($response) {
        $contents = [];
        if (array_key_exists($this->name, $response)) {
            $sans = $response[$this->name];
            $rowsin = explode("\n", $sans);
            foreach ($rowsin as $key => $row) {
                $cleanrow = trim($row);
                if ($cleanrow !== '') {
                    $contents[] = $cleanrow;
                }
            }
        }
        // Transform into lists.
        $maxlen = 0;
        foreach ($contents as $key => $row) {
            $entries = preg_split('/\s+/', $row);
            $maxlen = max(count($entries), $maxlen);
            $contents[$key] = $entries;
        }

        foreach ($contents as $key => $row) {
            // Pad out short rows.
            $padrow = [];
            for ($i = 0; $i < ($maxlen - count($row)); $i++) {
                $padrow[] = '?';
            }
            $contents[$key] = array_merge($row, $padrow);
        }
        if ($contents == [] && $this->get_extra_option('allowempty')) {
            $contents = ['EMPTYANSWER'];
        }
        return $contents;
    }

    protected function caslines_to_answer($caslines, $secrules = false) {
        $vals = [];
        foreach ($caslines as $line) {
            if ($line->get_valid()) {
                $vals[] = $line->get_inputform();
            } else {
                // This is an empty place holder for an invalid expression.
                $vals[] = 'EMPTYCHAR';
            }
        }
        $s = 'matrix('.implode(',', $vals).')';
        if (!$secrules) {
            $secrules = $caslines[0]->get_securitymodel();
        }
        return stack_ast_container::make_from_student_source($s, '', $secrules);
    }

    /**
     * Transforms the contents array into a maxima expression.
     *
     * @param array|string $in
     * @return string
     */
    public function contents_to_maxima($contents) {
        if ($contents == ['EMPTYANSWER']) {
            return 'matrix(EMPTYCHAR)';
        }
        $matrix = [];
        foreach ($contents as $row) {
            $matrix[] = '['.implode(',', $row).']';
        }
        return 'matrix('.implode(',', $matrix).')';
    }

    /**
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
        if ($contents == ['EMPTYANSWER']) {
            $modifiedcontents = $contents;
        } else {
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
                        [], 'Root', $localoptions->get_option('decimals'));
                    if ($answer->get_valid()) {
                        $modifiedrow[] = $answer->get_inputform();
                    } else {
                        $modifiedrow[] = 'EMPTYCHAR';
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
            $secrules->set_forbiddenwords($modifiedforbid);
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

    /**
     * Transforms a Maxima list into raw input.
     *
     * @param string $in
     * @return string
     */
    private function maxima_to_raw_input($in) {
        $decimal = '.';
        $listsep = ',';
        if ($this->options->get_option('decimals') === ',') {
            $decimal = ',';
            $listsep = ';';
        }
        $tostringparams = [
            'inputform' => true,
            'qmchar' => true,
            'pmchar' => 0,
            'nosemicolon' => true,
            'dealias' => false, // This is needed to stop pi->%pi etc.
            'nounify' => true,
            'nontuples' => false,
            'varmatrix' => true,
            'decimal' => $decimal,
            'listsep' => $listsep,
        ];
        $cs = stack_ast_container::make_from_teacher_source($in);
        return $cs->ast_to_string(null, $tostringparams);
    }

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
        return  $this->maxima_to_response_array($value);
    }

    protected function ajax_to_response_array($in) {
        $in = explode('<br>', $in);
        $in = implode("\n", $in);
        return [$this->name => $in];
    }

    /**
     * Transforms a Maxima expression into an array of raw inputs which are part of a response.
     * Most inputs are very simple, but textarea and matrix need more here.
     *
     * @param string $in
     * @return string
     */
    public function maxima_to_response_array($in) {
        $response[$this->name] = $this->maxima_to_raw_input($in);
        if ($this->requires_validation()) {
            $response[$this->name . '_val'] = $in;
        }
        return $response;
    }

    /**
     * Return the default values for the parameters.
     * Parameters are options a teacher might set.
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
            // This looks odd, but the teacher's answer is a list and the student's a matrix.
            'sameType'           => false,
            'options'            => '',
        ];
    }

    /**
     * Each actual extension of this base class must decide what parameter values are valid
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

    public function get_api_solution($tavalue) {
        // We clear the name, and then restore its original value,
        // to not include the prefix in the api solution.
        $name = $this->name;
        $this->name = '';
        $sol = $this->maxima_to_response_array($tavalue);
        $this->name = $name;
        return $sol;
    }

}

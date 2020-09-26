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

defined('MOODLE_INTERNAL') || die();

/**
 * An input which provides a matrix input of variable size.
 * Lots in common with the textarea class.
 *
 * @copyright  2019 Ruhr University Bochum
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class stack_varmatrix_input extends stack_input {

    private static $tostringparams = array('inputform' => true,
        'qmchar' => true,
        'pmchar' => 0,
        'nosemicolon' => true,
        'dealias' => false, // This is needed to stop pi->%pi etc.
        'nounify' => true,
        'varmatrix' => true,
    );

    protected $extraoptions = array(
        'simp' => false,
        'rationalized' => false,
        'allowempty' => false
    );

    public function render(stack_input_state $state, $fieldname, $readonly, $tavalue) {
        // Note that at the moment, $this->boxHeight and $this->boxWidth are only
        // used as minimums. If the current input is bigger, the box is expanded.

        if ($this->errors) {
            return $this->render_error($this->errors);
        }

        $size = $this->parameters['boxWidth'] * 0.9 + 0.1;
        $attributes = array(
            'name'           => $fieldname,
            'id'             => $fieldname,
            'autocapitalize' => 'none',
            'spellcheck'     => 'false',
            'class'          => 'varmatrixinput',
            'size'           => $this->parameters['boxWidth'] * 1.1,
            'style'          => 'width: '.$size.'em',
        );

        if ($this->is_blank_response($state->contents)) {
            $current = $this->maxima_to_raw_input($this->parameters['syntaxHint']);
        } else {
            $current = array();
            foreach ($state->contents as $row) {
                $cs = stack_ast_container::make_from_teacher_source($row);
                if ($cs->get_valid()) {
                    $current[] = $cs->ast_to_string(null, self::$tostringparams);
                }
            }
            $current = implode("\n", $current);
        }

        // Sort out size of text area.
        $rows = stack_utils::list_to_array($current, false);
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
        $matrixbrackets = 'matrixroundbrackets';
        if ($this->options) {
            $matrixparens = $this->options->get_option('matrixparens');
            if ($matrixparens == '[') {
                $matrixbrackets = 'matrixsquarebrackets';
            } else if ($matrixparens == '|') {
                $matrixbrackets = 'matrixbarbrackets';
            } else if ($matrixparens == '') {
                $matrixbrackets = 'matrixnobrackets';
            }
        }

        $xhtml = html_writer::tag('textarea', htmlspecialchars($current), $attributes);
        return html_writer::tag('div', $xhtml, array('class' => $matrixbrackets));
    }

    public function add_to_moodleform_testinput(MoodleQuickForm $mform) {
        $mform->addElement('text', $this->name, $this->name, array('size' => $this->parameters['boxWidth']));
        $mform->setDefault($this->name, $this->parameters['syntaxHint']);
        $mform->setType($this->name, PARAM_RAW);
    }

    /**
     * Transforms the student's response input into an array.
     * Most return the same as went in.
     *
     * @param array|string $in
     * @return string
     */
    protected function response_to_contents($response) {
        $contents = array();
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
            for ($i = 0; $i < ($maxlen - count($row)); $i++) {
                $row[] = '?';
            }
            $contents[$key] = '[' . implode(',', $row) . ']';
        }
        if ($contents == array() && $this->get_extra_option('allowempty')) {
            $contents = array('EMPTYANSWER');
        }
        return $contents;
    }

    protected function caslines_to_answer($caslines, $secrules = false) {
        $vals = array();
        foreach ($caslines as $line) {
            if ($line->get_valid()) {
                $vals[] = $line->get_evaluationform();
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
        return 'matrix('.implode(',', $contents).')';
    }

    /**
     * Transforms a Maxima list into raw input.
     *
     * @param string $in
     * @return string
     */
    private function maxima_to_raw_input($in) {
        $cs = stack_ast_container::make_from_teacher_source($in);
        return $cs->ast_to_string(null, self::$tostringparams);
    }

    protected function ajax_to_response_array($in) {
        $in = explode('<br>', $in);
        $in = implode("\n", $in);
        return array($this->name => $in);
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
        return array(
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
            'options'            => '');
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

}

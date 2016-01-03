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
 * Input that is a dropdown list/multiple choice that the teacher
 * has specified.
 *
 * @copyright  2015 University of Edinburgh
 * @author     Chris Sangwin
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class stack_dropdown_input extends stack_input {

    /*
     * Catch and report runtime errors.
     */
    protected $ddlerrors = '';

    /*
     * ddlvalues is an array of the types used.
     */
    protected $ddlvalues = array();

    /*
     * ddltype must be one of 'select', 'checkbox' or 'radio'.
     */
    protected $ddltype = 'select';

    /*
     * ddlshuffle is a boolean which decides whether to shuffle the non-trivial options.
     */
    protected $ddlshuffle = false;

    /*
     * ddldisplay must be either 'LaTeX' or 'casstring' and it determines what is used for the displayed
     * string the student uses.  The default is LaTeX, but this doesn't always work in dropdowns.
     */
    protected $ddldisplay = 'casstring';

    /*
     * This holds the value of those
     * entries which the teacher has indicated are correct.
     */
    protected $teacheranswervalue = '';

    /*
     * This holds a displayed form of $this->teacheranswer. We need to generate this from those
     * entries which the teacher has indicated are correct.
     */
    protected $teacheranswerdisplay = '';


    protected function internal_contruct() {
        $options = $this->get_parameter('options');
        if (trim($options) != '') {
            $options = explode(',', $options);
            foreach ($options as $option) {
                $option = strtolower(trim($option));

                switch($option) {
                    // Should we shuffle values?
                    case 'shuffle':
                        $this->ddlshuffle = true;
                        break;

                    // Does a student see LaTeX or cassting values?
                    case 'latex':
                        $this->ddldisplay = 'LaTeX';
                        break;

                    case 'latexinline':
                        $this->ddldisplay = 'LaTeXinline';
                        break;

                    case 'casstring':
                        $this->ddldisplay = 'casstring';
                        break;

                    // Radio, checkboxes or dropdown?
                    case 'checkbox':
                        $this->ddltype = 'checkbox';
                        break;

                    case 'radio':
                        $this->ddltype = 'radio';
                        break;

                    case 'select':
                        $this->ddltype = 'select';
                        break;

                    default:
                        throw new stack_exception('stack_dropdown_input: did not recognize the input type option '.$option);
                }
            }
        }

        // Sort out the default ddlvalues etc.
        $this->adapt_to_model_answer($this->teacheranswer);
        return true;
    }

    /* For the dropdown, each expression must be a list of pairs:
     * [CAS expression, true/false].
     * The second Boolean value determines if this should be considered
     * correct.  If there is more than one correct answer then checkboxes
     * will always be used.
     */
    public function adapt_to_model_answer($teacheranswer) {

        // We need to reset the errors here, now we have a new teacher's answer.
        $this->ddlerrors = '';

        /* Sort out the $this->ddlvalues.
         * Each element must be an array with the keys
         * value - the CAS value.
         * display - the LaTeX displayed value.
         * correct - whether this is considered correct or not.  This is a PHP boolean.
        */
        $values = stack_utils::list_to_array($teacheranswer, false);
        if (empty($values)) {
            $this->ddlerrors = stack_string('ddl_badanswer', $teacheranswer);
            $this->teacheranswervalue = '[ERR]';
            $this->teacheranswerdisplay = '<code>'.'[ERR]'.'</code>';
            $this->ddlvalues = null;
            return false;
        }

        $numbercorrect = 0;
        $ddlvalues = array();
        $correctanswer = array();
        $correctanswerdisplay = array();
        foreach ($values as $distractor) {
            $value = stack_utils::list_to_array($distractor, false);
            $ddlvalue = array();
            if (is_array($value)) {
                if (count($value) >= 2) {
                    $ddlvalue['value'] = $value[0];
                    $ddlvalue['display'] = $value[0];
                    if (array_key_exists(2, $value)) {
                        $ddlvalue['display'] = $value[2];
                    }
                    if ($value[1] == 'true') {
                        $ddlvalue['correct'] = true;
                    } else {
                        $ddlvalue['correct'] = false;
                    }
                    if ($ddlvalue['correct']) {
                        $numbercorrect += 1;
                        $correctanswer[] = $ddlvalue['value'];
                        $correctanswerdisplay[] = $ddlvalue['display'];
                    }
                    $ddlvalues[] = $ddlvalue;
                } else {
                    $this->ddlerrors = stack_string('ddl_badanswer', $teacheranswer);
                }
            }
        }

        /*
         * The dropdown input is very unusual in that the "teacher's answer" contains a mix
         * of correct and incorrect responses.  The teacher may be happy with a subset
         * of the correct responses.  So, we create $this->teacheranswervalue to be a Maxima
         * list of the values of those things the teacher said are correct.
         */
        $this->teacheranswervalue = '['.implode(',', $correctanswer).']';
        $this->teacheranswerdisplay = '<code>'.'['.implode(',', $correctanswerdisplay).']'.'</code>';

        if ($this->ddldisplay === 'casstring') {
            // By default, we wrap displayed values in <code> tags.
            foreach ($ddlvalues as $key => $value) {
                $ddlvalues[$key]['display'] = '<code>'.$ddlvalues[$key]['display'].'</code>';
            }
            $this->ddlvalues = $ddlvalues;
            return;
        }

        // If we are displaying LaTeX we need to connect to the CAS to generate LaTeX from the displayed values.
        $csvs = array();
        // Create a displayed form of the teacher's answer.
        $csv = new stack_cas_casstring('teachans:'.$this->teacheranswervalue);
        $csv->get_valid('t');
        $csvs[] = $csv;
        foreach ($ddlvalues as $key => $value) {
            // We use the display term here because it might differ explicitly from the above "value".
            // So, we send the display form to LaTeX, and then replace it with the LaTeX below.
            $csv = new stack_cas_casstring('val'.$key.':'.$value['display']);
            $csv->get_valid('t');
            $csvs[] = $csv;
        }

        $at1 = new stack_cas_session($csvs, null, 0);
        $at1->instantiate();

        if ('' != $at1->get_errors()) {
            $this->ddlerrors .= $at1->get_errors();
            return;
        }

        // This sets display form in $this->ddlvalues.
        $this->teacheranswerdisplay = '\('.$at1->get_display_key('teachans').'\)';
        foreach ($ddlvalues as $key => $value) {
            // Note, we've chosen to add LaTeX maths environments here.
            $disp = $at1->get_display_key('val'.$key);
            if ($this->ddldisplay === 'LaTeX') {
                $ddlvalues[$key]['display'] = '\['.$disp.'\]';
            } else {
                $ddlvalues[$key]['display'] = '\('.$disp.'\)';
            }
        }
        $this->ddlvalues = $ddlvalues;

        return;
    }

    protected function extra_validation($contents) {
        if (!array_key_exists($contents[0], $this->get_choices())) {
            return stack_string('dropdowngotunrecognisedvalue');
        }
        return '';
    }

    /**
     * Transforms the contents array into a maxima list.
     *
     * @param array|string $in
     * @return string
     */
    public function contents_to_maxima($contents) {
        return '['.implode(',', $contents).']';
    }

    /**
     * Takes a Maxima list returns an array of values.
     * @return array
     */
    private function maxima_to_array($in) {
        return stack_utils::list_to_array($in, false);
    }

    /* This function always returns an array where the key is the CAS "value".
     * This is needed in various places, e.g. when we check the an answer received is actually
     * in the list of possible answers.
     */
    protected function get_choices() {
        if (empty($this->ddlvalues)) {
            return array();
        }

        $values = $this->ddlvalues;
        if (empty($values)) {
            $this->ddlerrors .= stack_string('ddl_empty');
            return array();
        }

        // We need to shuffle first becuase suffle changes the array keys.
        // We rely on the array keys to hold the value.
        if ($this->ddlshuffle) {
            shuffle($values);
        }

        $values = array_merge(array(0 => array('value' => '', 'display' => stack_string('notanswered'))), $values);

        // We need to do this step after array_merge.
        // If the 'value' is an integer, array_merge may renumber it.
        $choices = array();
        foreach ($values as $val) {
            if (!array_key_exists($val['value'], $choices)) {
                $choices[$val['value']] = $val['display'];
            } else {
                $this->ddlerrors .= stack_string('ddl_duplicates');
            }
        }

        return $choices;
    }

    public function render(stack_input_state $state, $fieldname, $readonly) {

        $values = $this->get_choices();

        $attributes = array();
        if ($readonly) {
            $attributes['disabled'] = 'disabled';
        }

        if ($this->ddltype != 'checkbox' && $this->teacheranswervalue == '[]') {
            $this->ddlerrors .= stack_string('ddl_nocorrectanswersupplied');
        }

        $ret = '';
        // Display runtime errors and bail out.
        if ('' != $this->ddlerrors) {
            $ret .= html_writer::tag('p', stack_string('ddl_runtime'));
            $ret .= html_writer::tag('p', $this->ddlerrors);
            return html_writer::tag('div', $ret, array('class' => 'error'));
        }

        // HACK: in preparation for questions with more than one potential input.
        // Need to loop over the numbers here for each input.
        $selected = $state->contents;
        $idx = 0;
        $select = '';
        if (array_key_exists($idx, $selected)) {
            $select = $selected[$idx];
        }
        //$fieldname = $fieldname.'_sub_'.$idx;
        $ret .= html_writer::select($values, $fieldname, $select,
            array('' => stack_string('notanswered')), $attributes);

        return $ret;
    }

    /*
     * We only call this method at the point we really intend to use
     * the displayed values in the render.
     */

    public function add_to_moodleform_testinput(MoodleQuickForm $mform) {
        $values = $this->get_choices();
        if (empty($values)) {
            $mform->addElement('static', $this->name, stack_string('ddl_empty'));
        } else {
            $mform->addElement('select', $this->name, $this->name, $values);
        }
    }

    /**
     * Return the default values for the parameters.
     * @return array parameters` => default value.
     */
    public static function get_parameters_defaults() {

        return array(
            'mustVerify'     => false,
            'showValidation' => 0,
            'options'        => '',
        );
    }

    /**
     * This is used by the question to get the teacher's correct response.
     * The dropdown type needs to intercept this to filter the correct answers.
     * @param unknown_type $in
     */
    public function get_correct_response($in) {
        $this->adapt_to_model_answer($in);
        return $this->maxima_to_response_array($this->teacheranswervalue);
    }

    /**
     * Transforms a Maxima expression into an array of raw inputs which are part of a response.
     * Most inputs are very simple, but textarea and matrix need more here.
     *
     * @param array|string $in
     * @return string
     */
    public function maxima_to_response_array($in) {

        $tc = $this->maxima_to_array($in);

        foreach ($tc as $key => $val) {
                $response[$this->name.'_sub_'.$key] = $val;
        }

        if ($this->requires_validation()) {
            $response[$this->name . '_val'] = $in;
        }
        return $response;

    }

    /**
     * @return string the teacher's answer, displayed to the student in the general feedback.
     */
    public function get_teacher_answer_display($value, $display) {
        // Can we really ignore the $value and $display inputs here and rely on the internal state?
        return stack_string('teacheranswershow_disp', array('display' => $this->teacheranswerdisplay));
    }

    /**
     * Converts the input passed in via many input elements into an array.
     *
     * @param string $in
     * @return string
     * @access public
     */
    public function response_to_contents($response) {
        // HACK, need to loop over all the possible values.
        $contents = array();
        if (array_key_exists($this->name . '_sub_0', $response)) {
            $contents[] = $response[$this->name . '_sub_0'];
        }
        // TODO Delete this!
        if (array_key_exists($this->name, $response)) {
            $contents[] = $response[$this->name];
        }

        return $contents;
    }

}

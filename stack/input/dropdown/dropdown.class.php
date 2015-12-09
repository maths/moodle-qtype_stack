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

/* TODO
 * (0) Enable the input to deal with multiple submissions, e.g. checkboxes.  See the Matrix type for ideas here.
 * (1) Reinstate the various input types
 *     (1.1) Radio buttons (use LaTeX display)
 *     (1.2) Check boxes
 * (2) Check if the display type is a string. If so, strip off the "s and don't sent it through the CAS.
 * (3) Get all this working with thorough unit tests.
 * 
 * RELEASE basic working version?
 * 
 * (1) Refactor shuffle to be seeded from the question usage?
 * (2) Add choose N (correct) from M feature (used at Aalto).
 * (3) Have a "none of these" which degrates to an algebraic input
 * (4) Enable better support for text-based strings in the display (e.g. CASTex?!)
 *  
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
    protected $ddlshuffle = true;

    /*
     * ddldisplay must be either 'LaTeX' or 'casstring' and it determines what is used for the displayed
     * string the student uses.  The default is LaTeX, but this doesn't always work in dropdowns.
     */
    protected $ddldisplay = 'casstring';

    /*
     * This holds a displayed form of $this->teacheranswer. We need to generate this from those 
     * entries which the teacher has indicated are correct.
     */
    protected $teacheranswerdisplay= '';
    
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
            return array();
        }

        // We need to shuffle first becuase suffle changes the array keys.
        // We rely on the array keys to hold the value.
        if ($this->ddlshuffle) {
            shuffle($values);
        }
        
        $choices = array();
        foreach ($values as $value) {
            $choices[$value['value']] = $value['display'];
        }

        $choices = array_merge(array('' => stack_string('notanswered')), $choices);

        // TODO.  In this method check all the key and values are unique.
        return $choices;
    }

    /* For the dropdown, each expression must be a list of pairs:
     * [CAS expression, true/false].
     * The second Boolean value determines if this should be considered 
     * correct.  If there is more than one correct answer then checkboxes 
     * will always be used.
     */
    public function adapt_to_model_answer($teacheranswer) {

        // (1) Register the options.
        $options = $this->get_parameter('options');
        if (trim($options) != '') {
            $options = explode(',', $options);
            foreach ($options as $option) {
                $option = strtolower(trim($option));

                // Should we shuffle values?
                if ($option === 'shuffle') {
                    $this->ddlshuffle = true;
                }

                // Does a student see LaTeX or cassting values?
                if ($option === 'latex') {
                    $this->ddldisplay = 'LaTeX';
                }
                if ($option === 'latexinline') {
                    $this->ddldisplay = 'LaTeXinline';
                }
                if ($option === 'casstring') {
                    $this->ddldisplay = 'casstring';
                }
                
                // Radio, checkboxes or dropdown?
                if ($option === 'checkbox') {
                    $this->ddltype = 'checkbox';
                }
                if ($option === 'radio') {
                    $this->ddltype = 'radio';
                }
                if ($options === 'select') {
                    $this->ddltype = 'select';
                }
                // TODO: throw an error for an unrecognised option.
            }
        }

        /* (2) Sort out the $this->ddlvalues.
         * Each element must be an array with the keys
         * value - the CAS value.
         * display - the LaTeX displayed value.
         * correct - whether this is considered correct or not.  This is a PHP boolean.
        */
        $values = stack_utils::list_to_array($teacheranswer, false);
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
                    // TODO: Add an error message here.
                }
            }
        }
        
        if ($numbercorrect === 0) {
            $this->ddlerrors .= stack_string('ddl_nocorrectanswersupplied');
            return;
        }
        /* 
         * This input is very unusual in that the "teacher's answer" contains a mix
         * of correct and incorrect responses.  The teacher may be happy with a subset
         * of the correct responses.  So, we update $this->teacheranswer to be a Maxima
         * list of the values of those things the teacher said are correct.
         */
        $this->teacheranswer = '['.implode(',', $correctanswer).']';
        $this->teacheranswerdisplay = '<code>'.'['.implode(',', $correctanswerdisplay).']'.'</code>';
        // If we are displaying casstrings then we need to wrap them in <code> tags.
        if ($this->ddldisplay === 'casstring') {
            foreach ($ddlvalues as $key => $value) {
                $ddlvalues[$key]['display'] = '<code>'.$ddlvalues[$key]['display'].'</code>';
            }
            $this->ddlvalues = $ddlvalues;
            
            return;
        }

        // (3) If we are displaying LaTeX we need to connect to the CAS to generate LaTeX from the displayed values.
        $csvs = array();
        // Create a displayed form of the teacher's answer.
        $csv = new stack_cas_casstring('teachans:'.'['.implode(',', $correctanswerdisplay).']');
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
    
    public function render(stack_input_state $state, $fieldname, $readonly) {
        $values = $this->get_choices();
        if (empty($values)) {
            return stack_string('ddl_empty');
        }
        
        $attributes = array();
        if ($readonly) {
            $attributes['disabled'] = 'disabled';
        }

        $ret = '';
        // Print runtime errors.
        if ('' != $this->ddlerrors) {
            $ret .= html_writer::tag('p', $this->ddlerrors);
        }
        
        $ret .= html_writer::select($values, $fieldname, $this->contents_to_maxima($state->contents),
            array('' => stack_string('notanswered')), $attributes);

        return $ret;
    }

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
     * @return string the teacher's answer, displayed to the student in the general feedback.
     */
    public function get_teacher_answer_display($value, $display) {
        // TODO: make sure we can really ignore the $value and $display inputs here.

        return stack_string('teacheranswershow_disp', array('display' => $this->teacheranswerdisplay));
    }
}

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
 * (1) Refactor code so the input type always returns a *LIST*.  This is
 * needed for the checkboxes, so we might as well always have a list, so the
 * PRTS know what they are up to.
 * (2) Reinstate the various input types
 *     (2.1) Radio buttons (use LaTeX display)
 *     (2.2) Check boxes
 * (3) Get all this working with thorough unit tests.
 * (4) Add in the optional display field.  [value,correct(,display)]
 *     If it exists then it should be sent to the CAS for evaluation.
 * 
 * RELEASE basic working version?
 * 
 * (1) Refactor shuffle to be seeded from the question usage?
 * (2) Add choose N (correct) from M feature (used at Aalto).
 * (3) Have a "none of these" which degrates to an algebraic input
 * (3) Enable better support for text-based strings in the display (e.g. CASTex?!)
 *  
 */

class stack_dropdown_input extends stack_input {

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

    protected function get_choices() {
        if (empty($this->ddlvalues)) {
            return array();
        }

        $values = $this->ddlvalues;
        if (empty($values)) {
            return array();
        }

        $choices = array();
        foreach ($values as $value) {
            $choices[$value['value']] = '\('.$value['display'].'\)';
            // For the "select" type we need to see the typed syntax, not the LaTeX.
            if ($this->ddltype == 'select') {
                $choices[$value['value']] = $value['value'];
            }
        }

        if ($this->ddlshuffle) {
            shuffle($choices);
        }

        $choices = array_merge(array('' => stack_string('notanswered')), $choices);

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

                if ($option === 'shuffle') {
                    $this->ddlshuffle = true;
                }

                if ($option === 'checkbox') {
                    $this->ddltype = 'checkbox';
                }
                if ($option === 'radio') {
                    $this->ddltype = 'radio';
                }
                if ($options === 'select') {
                    $this->ddltype = 'select';
                }
            }
        }

        // (2) Sort out the choices.
        $values = stack_utils::list_to_array($teacheranswer, false);

        $csvs = array();
        foreach ($values as $key => $value) {
            $csv = new stack_cas_casstring('val'.$key.':first(' . $value . ')');
            $csv->get_valid('t');
            $csvs[] = $csv;
            $csv = new stack_cas_casstring('cor'.$key.':second(' . $value . ')');
            $csv->get_valid('t');
            $csvs[] = $csv;
            // Stub for the "lable", but this now throws an error when it is optional.
            // TODO: refactor to either not throw an error, or ignore this error.
            $csv = new stack_cas_casstring('dis'.$key.':third(' . $value . ')');
            $csv->get_valid('t');
            $csvs[] = $csv;
        }

        $at1 = new stack_cas_session($csvs, null, 0);
        $at1->instantiate();

        if ('' != $at1->get_errors()) {
            $this->errors = $at1->get_errors();
            return;
        }

        /* This sets $this->ddlvalues.
         * Each element must be an array with the keys
         * value - the CAS value.
         * display - the LaTeX displayed value.
         * correct - whether this is considered correct or not.
        */
        $ddlvalues = array();
        $numbercorrect = 0;
        foreach ($values as $key => $value) {
            $ddlvalue = array();
            $ddlvalue['value'] = $at1->get_value_key('val'.$key);
            $ddlvalue['display'] = $at1->get_display_key('val'.$key);
            $ddlvalue['correct'] = $at1->get_value_key('cor'.$key);
            $ddlvalues[] = $ddlvalue;
            if ('true' == $ddlvalue['correct']) {
                $numbercorrect += 1;
            }
            // TODO: stub for display option.
            //if ($at1->get_errors_key('dis'.$key) === '') {
            //    $ddlvalue['display'] = $at1->get_display_key('dis'.$key);                
            //}
        }

        $this->ddlvalues = $ddlvalues;
        if ($numbercorrect === 0) {
            $this->errors = stack_string('ddl_nocorrectanswersupplied');
            return;
        }
        // If the teacher has created more than one correct answer then
        // we must use checkboxes.
        if ($numbercorrect > 1) {
            $this->ddltype = 'checkbox';
            return;
        }
    }

    protected function extra_validation($contents) {
        if (!array_key_exists($contents[0], $this->get_choices())) {
            return stack_string('dropdowngotunrecognisedvalue');
        }
        return '';
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

        return html_writer::select($values, $fieldname, $this->contents_to_maxima($state->contents),
            array('' => stack_string('notanswered')), $attributes);
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

}

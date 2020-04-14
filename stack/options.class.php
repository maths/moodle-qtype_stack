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
 * Options enable a context to be set for each question, and information
 * made generally available to other classes.
 *
 * @copyright  2012 The University of Birmingham
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class stack_options {

    private $options;

    public function __construct($settings = array()) {

        // OptionType can be: boolean, string, html, list.
        $this->options  = array( // Array of public class settings for this class.
            'display'   => array(
                'type'       => 'list',
                'value'      => 'LaTeX',
                'strict'     => true,
                'values'     => array('LaTeX', 'String'),
                'caskey'     => 'OPT_OUTPUT',
                'castype'    => 'string',
             ),
            'multiplicationsign'   => array(
                'type'       => 'list',
                'value'      => 'dot',
                'strict'     => true,
                'values'     => array('dot', 'cross', 'none'),
                'caskey'     => 'make_multsgn',
                'castype'    => 'fun',
            ),
            'complexno'   => array(
                'type'       => 'list',
                'value'      => 'i',
                'strict'     => true,
                'values'     => array('i', 'j', 'symi', 'symj'),
                'caskey'     => 'make_complexJ',
                'castype'    => 'fun',
            ),
            'inversetrig'   => array(
                'type'       => 'list',
                'value'      => 'cos-1',
                'strict'     => true,
                'values'     => array('cos-1', 'acos', 'arccos'),
                'caskey'     => 'make_arccos',
                'castype'    => 'fun',
            ),
            'logicsymbol'   => array(
                'type'       => 'list',
                'value'      => 'lang',
                'strict'     => true,
                'values'     => array('lang', 'symbol'),
                'caskey'     => 'make_logic',
                'castype'    => 'fun',
            ),
            'floats'   => array(
                'type'       => 'boolean',
                'value'      => 1,
                'strict'     => true,
                'values'     => array(),
                'caskey'     => 'OPT_NoFloats',
                'castype'    => 'ex',
            ),
            'sqrtsign'   => array(
                'type'       => 'boolean',
                'value'      => true,
                'strict'     => true,
                'values'     => array(),
                'caskey'     => 'sqrtdispflag',
                'castype'    => 'ex',
            ),
            'simplify'   => array(
                'type'       => 'boolean',
                'value'      => true,
                'strict'     => true,
                'values'     => array(),
                'caskey'     => 'simp',
                'castype'    => 'ex',
            ),
            'assumepos'   => array(
                'type'       => 'boolean',
                'value'      => false,
                'strict'     => true,
                'values'     => array(),
                'caskey'     => 'assume_pos',
                'castype'    => 'ex',
            ),
            'assumereal'   => array(
                'type'       => 'boolean',
                'value'      => false,
                'strict'     => true,
                'values'     => array(),
                'caskey'     => 'assume_real',
                'castype'    => 'ex',
            ),
            'matrixparens'   => array(
                'type'       => 'list',
                'value'      => '[',
                'strict'     => true,
                'values'     => array('[', '(', '', '{', '|'),
                'caskey'     => 'lmxchar',
                'castype'    => 'exs',
            ),
        );

        if (!is_array($settings)) {
            throw new stack_exception('stack_options: $settings must be an array.');
        }

        // Overright them from any input.
        foreach ($settings as $key => $val) {
            if (!array_key_exists($key, $this->options)) {
                throw new stack_exception('stack_options construct: $key '.$key.' is not a valid option name.');
            } else {
                $this->options[$key]['value'] = $val;
            }
        }
    }

    public function set_site_defaults() {
        $stackconfig = stack_utils::get_config();
        // Display option does not match up to $stackconfig->mathsdisplay).
        $this->set_option('multiplicationsign', $stackconfig->multiplicationsign);
        $this->set_option('complexno', $stackconfig->complexno);
        $this->set_option('inversetrig', $stackconfig->inversetrig);
        $this->set_option('logicsymbol', $stackconfig->logicsymbol);
        $this->set_option('matrixparens', $stackconfig->matrixparens);
        $this->set_option('floats', (bool) $stackconfig->inputforbidfloat);
        $this->set_option('sqrtsign', (bool) $stackconfig->sqrtsign);
        $this->set_option('simplify', (bool) $stackconfig->questionsimplify);
        $this->set_option('assumepos', (bool) $stackconfig->assumepositive);
        $this->set_option('assumereal', (bool) $stackconfig->assumereal);
        return true;
    }

    /*
     * This function validates the information.
     */
    private function validate_key($key, $val) {
        if (!array_key_exists($key, $this->options)) {
            throw new stack_exception('stack_options set_option: $key '.$key.' is not a valid option name.');
        }
        $optiontype = $this->options[$key]['type'];
        switch($optiontype) {
            case 'boolean':
                if (!is_bool($val)) {
                    throw new stack_exception('stack_options: set: boolean option '.$key.' Recieved non-boolean value '.$val);
                }
                break;

            case 'list':
                if (!in_array($val, $this->options[$key]['values'])) {
                    throw new stack_exception('stack_options set option '.$val.' for '.$key.' is invalid');
                }
                break;
        }
        return true;
    }

    public function get_option($key) {
        if (!array_key_exists($key, $this->options)) {
            throw new stack_exception('stack_options get_option: $key '.$key.' is not a valid option name.');
        } else {
            return $this->options[$key]['value'];
        }
    }

    public function set_option($key, $val) {
        $this->validate_key($key, $val); // Throws an exception on error.
        $this->options[$key]['value'] = $val;
    }

    public function get_cas_commands() {

        $names = '';
        $commands = '';

        foreach ($this->options as $key => $opt) {
            if (null != $opt['castype']) {
                if ('boolean' === $opt['type']) {
                    if ($opt['value']) {
                        $value = 'true';
                    } else {
                        $value = 'false';
                    }
                } else {
                    $value = $opt['value'];
                }

                if ('ex' == $opt['castype']) {
                    $names      .= ', '.$opt['caskey'];
                    $commands   .= stack_cas_session2::SEP . $opt['caskey'].':'.$value;
                } else if ('exs' == $opt['castype']) {
                    $names      .= ', '.$opt['caskey'];
                    $commands   .= stack_cas_session2::SEP . $opt['caskey'].':"'.$value.'"';
                } else if ('fun' == $opt['castype']) {
                    // Make sure these options are *strings*, otherwise they clash
                    // with Maxim names, particularly alias.
                    $commands   .= stack_cas_session2::SEP . $opt['caskey'].'("'.$value.'")';
                }
            }
        }
        $ret = array('names' => $names, 'commands' => $commands);
        return $ret;
    }

    /**
     * @return array of choices for a no/yes select menu.
     */
    public static function get_yes_no_options() {
        return array(
            '0' => get_string('no'),
            '1' => get_string('yes'),
        );
    }

    /**
     * @return array of choices for the insert stars select menu.
     */
    public static function get_insert_star_options() {
        return array(
            '0' => get_string('insertstarsno', 'qtype_stack'),
            '1' => get_string('insertstarsyes', 'qtype_stack'),
            '2' => get_string('insertstarsassumesinglechar', 'qtype_stack'),
            '3' => get_string('insertspaces', 'qtype_stack'),
            '4' => get_string('insertstarsspaces', 'qtype_stack'),
            '5' => get_string('insertstarsspacessinglechar', 'qtype_stack')
        );
    }

    /**
     * @return array of choices for the input syntax hint display attribute.
     */
    public static function get_syntax_attribute_options() {
        return array(
                '0' => get_string('syntaxattributevalue', 'qtype_stack'),
                '1' => get_string('syntaxattributeplaceholder', 'qtype_stack'),
        );
    }

    /**
     * @return array of choices for the multiplication sign select menu.
     */
    public static function get_multiplication_sign_options() {
        return array(
            'dot'   => get_string('multdot', 'qtype_stack'),
            'cross' => get_string('multcross', 'qtype_stack'),
            'none'  => get_string('none'),
        );
    }

    /**
     * @return array of choices for the complex number select menu.
     */
    public static function get_complex_no_options() {
        return array(
            'i'    => 'i',
            'j'    => 'j',
            'symi' => 'symi',
            'symj' => 'symj',
        );
    }

    /**
     * @return array of choices for the inverse trig select menu.
     */
    public static function get_inverse_trig_options() {
        return array(
            'cos-1'  => "cos\xe2\x81\xbb\xc2\xb9(x)",
            'acos'   => 'acos(x)',
            'arccos' => 'arccos(x)',
        );
    }

    /**
     * @return array of choices for the inverse trig select menu.
     */
    public static function get_logic_options() {
        return array(
            'lang'   => get_string('logicsymbollang', 'qtype_stack'),
            'symbol' => get_string('logicsymbolsymbol', 'qtype_stack'),
        );
    }

    /**
     * @return array of choices for the matrix prenthesis select menu.
     */
    public static function get_matrix_parens_options() {
        return array(
            '[' => '[',
            '(' => '(',
            ''  => '',
            '{' => '{',
            '|' => '|',
        );
    }

    /**
     * @return array of choices for the show validation select menu.
     */
    public static function get_showvalidation_options() {
        return array(
            '0' => get_string('showvalidationno', 'qtype_stack'),
            '1' => get_string('showvalidationyes', 'qtype_stack'),
            '2' => get_string('showvalidationyesnovars', 'qtype_stack'),
            '3' => get_string('showvalidationcompact', 'qtype_stack'),
        );
    }
}

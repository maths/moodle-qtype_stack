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
 * Options enable a context to be set for each question, and information
 * made generally available to other classes.
 *
 * @copyright  2012 The University of Birmingham
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class stack_options {

    private $options;

    public function __construct($settings = []) {

        // OptionType can be: boolean, string, html, list.
        $this->options  = [ // Array of public class settings for this class.
            'display'   => [
                'type'       => 'list',
                'value'      => 'LaTeX',
                'strict'     => true,
                'values'     => ['LaTeX', 'String'],
                'caskey'     => 'OPT_OUTPUT',
                'castype'    => 'string',
            ],
            'decimals'       => [
                'type'       => 'list',
                'value'      => '.',
                'strict'     => true,
                'values'     => ['.', ','],
                'caskey'     => 'texput_decimal',
                'castype'    => 'fun',
            ],
            'scientificnotation' => [
                'type'       => 'list',
                'value'      => '*10',
                'strict'     => true,
                'values'     => ['*10', 'E'],
                'caskey'     => 'texput_scientificnotation',
                'castype'    => 'fun',
            ],
            'multiplicationsign'   => [
                'type'       => 'list',
                'value'      => 'dot',
                'strict'     => true,
                'values'     => ['dot', 'cross', 'onum', 'none'],
                'caskey'     => 'make_multsgn',
                'castype'    => 'fun',
            ],
            'complexno'   => [
                'type'       => 'list',
                'value'      => 'i',
                'strict'     => true,
                'values'     => ['i', 'j', 'symi', 'symj'],
                'caskey'     => 'make_complexJ',
                'castype'    => 'fun',
            ],
            'inversetrig'   => [
                'type'       => 'list',
                'value'      => 'cos-1',
                'strict'     => true,
                'values'     => ['cos-1', 'acos', 'arccos', 'arccos-arcosh'],
                'caskey'     => 'make_arccos',
                'castype'    => 'fun',
            ],
            'logicsymbol'   => [
                'type'       => 'list',
                'value'      => 'lang',
                'strict'     => true,
                'values'     => ['lang', 'symbol'],
                'caskey'     => 'make_logic',
                'castype'    => 'fun',
            ],
            'sqrtsign'   => [
                'type'       => 'boolean',
                'value'      => true,
                'strict'     => true,
                'values'     => [],
                'caskey'     => 'sqrtdispflag',
                'castype'    => 'ex',
            ],
            'simplify'   => [
                'type'       => 'boolean',
                'value'      => true,
                'strict'     => true,
                'values'     => [],
                'caskey'     => 'simp',
                'castype'    => 'ex',
            ],
            'assumepos'   => [
                'type'       => 'boolean',
                'value'      => false,
                'strict'     => true,
                'values'     => [],
                'caskey'     => 'assume_pos',
                'castype'    => 'ex',
            ],
            'assumereal'   => [
                'type'       => 'boolean',
                'value'      => false,
                'strict'     => true,
                'values'     => [],
                'caskey'     => 'assume_real',
                'castype'    => 'ex',
            ],
            'matrixparens'   => [
                'type'       => 'list',
                'value'      => '[',
                'strict'     => true,
                'values'     => ['[', '(', '', '{', '|'],
                'caskey'     => 'lmxchar',
                'castype'    => 'exs',
            ],
        ];

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
        $this->set_option('decimals', $stackconfig->decimals);
        $this->set_option('scientificnotation', $stackconfig->scientificnotation);
        $this->set_option('multiplicationsign', $stackconfig->multiplicationsign);
        $this->set_option('complexno', $stackconfig->complexno);
        $this->set_option('inversetrig', $stackconfig->inversetrig);
        $this->set_option('logicsymbol', $stackconfig->logicsymbol);
        $this->set_option('matrixparens', $stackconfig->matrixparens);
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
        $ret = ['names' => $names, 'commands' => $commands];
        return $ret;
    }

    /**
     * @return array of choices for a no/yes select menu.
     */
    public static function get_yes_no_options() {
        return [
            '0' => get_string('no'),
            '1' => get_string('yes'),
        ];
    }

    /**
     * @return array of choices for the insert stars select menu.
     */
    public static function get_insert_star_options() {
        return [
            '0' => get_string('insertstarsno', 'qtype_stack'),
            '1' => get_string('insertstarsyes', 'qtype_stack'),
            '2' => get_string('insertstarsassumesinglechar', 'qtype_stack'),
            '3' => get_string('insertspaces', 'qtype_stack'),
            '4' => get_string('insertstarsspaces', 'qtype_stack'),
            '5' => get_string('insertstarsspacessinglechar', 'qtype_stack'),
        ];
    }

    /**
     * @return array of choices for the input syntax hint display attribute.
     */
    public static function get_syntax_attribute_options() {
        return [
            '0' => get_string('syntaxattributevalue', 'qtype_stack'),
            '1' => get_string('syntaxattributeplaceholder', 'qtype_stack'),
        ];
    }

    /**
     * @return array of choices for the decimal sign select menu.
     */
    public static function get_decimals_sign_options() {
        return [
            '.'    => '.',
            ','    => ',',
        ];
    }

    /**
     * @return array of choices for the scientific notation select menu.
     */
    public static function get_scientificnotation_options() {
        return [
            '*10'  => get_string('scientificnotation_10', 'qtype_stack'),
            'E'    => get_string('scientificnotation_E', 'qtype_stack'),
        ];
    }

    /**
     * @return array of choices for the multiplication sign select menu.
     */
    public static function get_multiplication_sign_options() {
        return [
            'dot'   => get_string('multdot', 'qtype_stack'),
            'cross' => get_string('multcross', 'qtype_stack'),
            'onum'  => get_string('multonlynumbers', 'qtype_stack'),
            'none'  => get_string('none'),
        ];
    }

    /**
     * @return array of choices for the complex number select menu.
     */
    public static function get_complex_no_options() {
        return [
            'i'    => 'i',
            'j'    => 'j',
            'symi' => 'symi',
            'symj' => 'symj',
        ];
    }

    /**
     * @return array of choices for the inverse trig select menu.
     */
    public static function get_inverse_trig_options() {
        return [
            'cos-1'         => "cos\xe2\x81\xbb\xc2\xb9(x)",
            'acos'          => 'acos(x)',
            'arccos'        => 'arccos(x)',
            'arccos-arcosh' => 'arccos(x)/arcosh(x)',
        ];
    }

    /**
     * @return array of choices for the inverse trig select menu.
     */
    public static function get_logic_options() {
        return [
            'lang'   => get_string('logicsymbollang', 'qtype_stack'),
            'symbol' => get_string('logicsymbolsymbol', 'qtype_stack'),
        ];
    }

    /**
     * @return array of choices for the matrix prenthesis select menu.
     */
    public static function get_matrix_parens_options() {
        return [
            '[' => '[',
            '(' => '(',
            ''  => '',
            '{' => '{',
            '|' => '|',
        ];
    }

    /**
     * @return array of choices for the show validation select menu.
     */
    public static function get_showvalidation_options() {
        return [
            '0' => get_string('showvalidationno', 'qtype_stack'),
            '1' => get_string('showvalidationyes', 'qtype_stack'),
            '2' => get_string('showvalidationyesnovars', 'qtype_stack'),
            '3' => get_string('showvalidationcompact', 'qtype_stack'),
        ];
    }

    /**
     * @return array of choices for the monospace input select menu.
     */
    public static function get_monospace_options() {
        return [
            // Options will appear in order listed, not key order.
            // Keys need to match is_monospace() below.
            '0' => get_string('inputtypealgebraic', 'qtype_stack'),
            '1' => get_string('inputtypenumerical', 'qtype_stack'),
            '2' => get_string('inputtypeunits', 'qtype_stack'),
            '3' => get_string('inputtypevarmatrix', 'qtype_stack'),
        ];
    }

    /**
     * Get the monospace default for supplied input class.
     * @return bool
     *
     * We have a class name in format 'stack_XXXX_input' where 'XXXX' is the input type.
     * The monospace default config setting is a string in format '0,2,4' where the integers are
     * the array keys from the option selection in get_monospace_options().
     * We have to convert the input type to an integer and then check if it's in the config string.
     */
    public static function is_monospace($class) {
        $options = [
            // These need to match get_monospace_options() above.
            '0' => 'algebraic',
            '1' => 'numerical',
            '2' => 'units',
            '3' => 'varmatrix',
        ];
        $optionkey = array_search(explode('_', $class)[1], $options);
        if ($optionkey === false) {
            // This type of input not allowed to be monospace.
            return false;
        }

        $monoinputkeys = explode(',', get_config('qtype_stack', 'inputmonospace'));

        $key = array_search(strval($optionkey), $monoinputkeys, true);

        if ($key === false) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * @return array of choices for the button display type select menu.
     */
    public static function get_displaytype_options() {
        return [
            '0' => get_string('displaytypedropdown', 'qtype_stack'),
            '1' => get_string('displaytypeclickbutton', 'qtype_stack'),
            '2' => get_string('displaytypetogglebutton', 'qtype_stack'),
        ];
    }

    /**
     * @return array of choices for the auswahl display type select menu.
     */
    public static function get_choicetype_options() {
        return [
            '0' => get_string('choicetypedropdown', 'qtype_stack'),
            '1' => get_string('choicetypecheckbox', 'qtype_stack'),
            '2' => get_string('choicetyperadiobuttons', 'qtype_stack'),
        ];
    }

    /**
     * @return array of choices for the auswahl display type select menu.
     */
    public static function get_matrixsize_options() {
        return [
            '0' => get_string('matrixsizevar', 'qtype_stack'),
            '1' => get_string('matrixsizefix', 'qtype_stack'),
        ];
    }
}
 
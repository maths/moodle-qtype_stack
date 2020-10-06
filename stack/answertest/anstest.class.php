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
 * Answer test base class.
 *
 * @copyright  2012 University of Birmingham
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class stack_anstest {

    /**
     * The name of the answer test.
     * @var string
     */
    protected $atname;

    /**
     * Every answer test must have something sensible here for the tracing.
     * @var string The name of the cas function this answer test uses.
     */
    protected $casfunction;

    /**
     * @var    stack_ast_container
     */
    protected $sanskey;

    /**
     * @var    stack_ast_container
     */
    protected $tanskey;

    /**
     * @var    stack_ast_container
     */
    protected $atoption = null;

    /**
     * @var    string
     */
    protected $options;

    /**
     * @var    float
     */
    protected $atmark;

    /**
     * @var    string
     */
    protected $aterror;

    /**
     * @var    bool
     */
    protected $atvalid;

    /**
     * @var    string
     */
    protected $atansnote;

    /**
     * @var    string
     */
    protected $atfeedback;

    /**
     * $var string.  Copies the debug info, e.g. from the CAS session.
     */
    protected $debuginfo;

    /**
     * Constructor
     *
     * @param  string $sanskey
     * @param  string $tanskey
     */
    public function __construct(stack_ast_container $sans, stack_ast_container $tans, $options = null, $atoption = null) {
        $this->sanskey = $sans;
        $this->tanskey = $tans;

        if (!(null === $options || is_a($options, 'stack_options'))) {
            throw new stack_exception('stack_anstest: options must be stack_options or null.');
        }

        if ($options != null) {
            $this->options  = clone $options;
        } else {
            $this->options = null;
        }

        if (is_a($atoption, 'stack_ast_container')) {
            $this->atoption = $atoption;
        }
    }

    /**
     * Acutally perform the test.
     *
     * @return bool
     */
    public function do_test() {
        return null;
    }

    /**
     *
     *
     * @return string
     */
    public function get_at_errors() {
        return $this->aterror;
    }

    /**
     *
     *
     * @return float
     */
    public function get_at_mark() {
        return $this->atmark;
    }

    /**
     *
     *
     * @return bool
     */
    public function get_at_valid() {
        return $this->atvalid;
    }

    /**
     *
     *
     * @return string
     */
    public function get_at_answernote() {
        return $this->atansnote;
    }

    /**
     *
     *
     * @return string
     */
    public function get_at_feedback() {
        return $this->atfeedback;
    }

    /**
     * Returns some sensible debug information for testing questions.
     *
     * @return string
     * @access public
     */
    public function get_debuginfo() {
        return $this->debuginfo;
    }

    /**
     * Returns some sensible debug information for testing questions.
     *
     * @return string
     * @access public
     */
    protected function get_casfunction() {
        return $this->casfunction;
    }

    /**
     * Returns an intelligible trace of an executed answer test.
     *
     * @return string
     * @access public
     */
    public function get_trace($includeresult) {

        if ($this->tanskey && $this->tanskey->get_valid()) {
            $ta = $this->tanskey->ast_to_string(null,
                array('logicnoun' => true, 'keyless' => true));
            if ($this->tanskey->is_correctly_evaluated()) {
                $ta = $this->tanskey->get_value();
            }
        } else {
            return '';
        }
        if ($this->sanskey && $this->sanskey->get_valid()) {
            $sa = $this->sanskey->ast_to_string(null,
                array('logicnoun' => true, 'keyless' => true));
            if ($this->sanskey->is_correctly_evaluated()) {
                $sa = $this->sanskey->get_value();
            }
        } else {
            return '';
        }
        $traceline = $this->get_casfunction() . '(' . $sa . ', ' . $ta . ')';
        $atopt = '';
        if ($this->atoption && $this->atoption->get_valid()) {
            $atopt = $this->atoption->ast_to_string(null,
                array('logicnoun' => true, 'keyless' => true));
        }
        if ($this->atoption && $this->atoption->is_correctly_evaluated()) {
            $atopt = $this->atoption->get_value();
        }
        if ($atopt != '') {
            $traceline = $this->get_casfunction() . '(' . $sa . ', ' . $ta . ', '. $atopt .')';
        }
        if ($includeresult) {
            $traceline .= ' = ['.$this->atmark. ', "' . $this->atansnote .'"];';
        } else {
            $traceline = trim($traceline) . ';';
        }

        return $traceline;
    }

}

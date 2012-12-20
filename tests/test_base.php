<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Base class for Stack unit tests.
 *
 * @package    qtype_stack
 * @copyright  2012 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/stack/stack/cas/installhelper.class.php');
require_once($CFG->dirroot . '/question/engine/tests/helpers.php');


/**
 * Base class for Stack unit tests. If you subclass this class, then code that
 * needs to call maxima will work (providing you have set up the neccessary
 * defines in phpunit.xml.
 *
 * @copyright  2012 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class qtype_stack_testcase extends advanced_testcase {
    public function setUp() {
        parent::setUp();

        stack_utils::clear_config_cache();
        self::setup_test_maxima_connection($this);
        $this->resetAfterTest();
    }

    /**
     * Helper that sets up the maxima configuration. This allows maxima to be used
     * from test classes that cannot subclass this one, for whatever reason.
     */
    public static function setup_test_maxima_connection($testcase) {
        global $CFG;

        if (!defined('QTYPE_STACK_TEST_CONFIG_PLATFORM')) {
            $testcase->markTestSkipped(
                    'To run the STACK unit tests, you must set up the Maxima configuration in phpunit.xml.');
        }

        if (!defined('QTYPE_STACK_EXPECTED_VERSION')) {
            if (!preg_match('~\[ STACK-Maxima started, library version (\d{10}) \]~',
                    file_get_contents($CFG->dirroot . '/question/type/stack/stack/maxima/stackmaxima.mac'), $matches)) {
                throw new coding_exception('Maxima libraries version number not found in stackmaxima.mac.');
            }
            define('QTYPE_STACK_EXPECTED_VERSION', $matches[1]);
        }

        set_config('platform',        QTYPE_STACK_TEST_CONFIG_PLATFORM,        'qtype_stack');
        set_config('maximaversion',   QTYPE_STACK_TEST_CONFIG_MAXIMAVERSION,   'qtype_stack');
        set_config('castimeout',      QTYPE_STACK_TEST_CONFIG_CASTIMEOUT,      'qtype_stack');
        set_config('casresultscache', QTYPE_STACK_TEST_CONFIG_CASRESULTSCACHE, 'qtype_stack');
        set_config('maximacommand',   QTYPE_STACK_TEST_CONFIG_MAXIMACOMMAND,   'qtype_stack');
        set_config('plotcommand',     QTYPE_STACK_TEST_CONFIG_PLOTCOMMAND,     'qtype_stack');
        set_config('casdebugging',    QTYPE_STACK_TEST_CONFIG_CASDEBUGGING,    'qtype_stack');
        set_config('mathsdisplay',    'mathjax',                               'qtype_stack');
        set_config('replacedollars',  0,                                       'qtype_stack');
        set_config('stackmaximaversion', QTYPE_STACK_EXPECTED_VERSION,         'qtype_stack');

        if (QTYPE_STACK_TEST_CONFIG_CASRESULTSCACHE == 'otherdb') {
            set_config('cascachedbtype',    QTYPE_STACK_TEST_CONFIG_CASCACHEDBTYPE,    'qtype_stack');
            set_config('cascachedblibrary', QTYPE_STACK_TEST_CONFIG_CASCACHEDBLIBRARY, 'qtype_stack');
            set_config('cascachedbhost',    QTYPE_STACK_TEST_CONFIG_CASCACHEDBHOST,    'qtype_stack');
            set_config('cascachedbname',    QTYPE_STACK_TEST_CONFIG_CASCACHEDBNAME,    'qtype_stack');
            set_config('cascachedbuser',    QTYPE_STACK_TEST_CONFIG_CASCACHEDBUSER,    'qtype_stack');
            set_config('cascachedbpass',    QTYPE_STACK_TEST_CONFIG_CASCACHEDBPASS,    'qtype_stack');
            set_config('cascachedbprefix',  QTYPE_STACK_TEST_CONFIG_CASCACHEDBPREFIX,  'qtype_stack');
            if (defined('QTYPE_STACK_TEST_CONFIG_CASCACHEDBSOCKET')) {
                set_config('cascachedbsocket',  QTYPE_STACK_TEST_CONFIG_CASCACHEDBSOCKET,  'qtype_stack');
            }
        }

        if (stack_cas_configuration::maxima_bat_is_missing()) {
            stack_cas_configuration::create_maximalocal();
        }
    }
}


/**
 * Base class for Stack walkthrough tests.
 *
 * Sets up the Maxima connection, and provides some additional asserts.
 *
 * @copyright 2012 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class qtype_stack_walkthrough_test_base extends qbehaviour_walkthrough_test_base {
    protected $currentoutput = null;

    public function setUp() {
        parent::setUp();
        qtype_stack_testcase::setup_test_maxima_connection($this);
        $this->resetAfterTest();
    }

    protected function render() {
        $this->currentoutput = $this->quba->render_question($this->slot, $this->displayoptions);
    }

    protected function get_tag_matcher($tag, $attributes) {
        return array(
            'tag' => $tag,
            'attributes' => $attributes,
        );
    }

    protected function check_prt_score($index, $score, $penalty, $finalsubmit = false) {
        $question = $this->quba->get_question($this->slot);
        $attempt  = $this->quba->get_question_attempt($this->slot);
        $qa       = $attempt->get_last_qt_data();
        $result   = $question->get_prt_result($index, $qa, $finalsubmit);

        if (is_null($score)) {
            $this->assertNull($result->score);
        } else {
            if ($score == 0) {
                // PHP will think a null and are equal, so explicity check not null.
                $this->assertNotNull($result->score);
            }
            $this->assertEquals($score, $result->score, 'Wrong score.  The PRT returned ' .
                    $result->score . ' but we expected ' . $score . '.');
        }

        if (is_null($penalty)) {
            $this->assertNull($result->penalty);
        } else {
            if ($penalty == 0) {
                // PHP will think a null and are equal, so explicity check not null.
                $this->assertNotNull($result->penalty);
            }
            $this->assertEquals($penalty, $result->penalty, 'Wrong penalty.  The PRT returned ' .
                    $result->penalty . ' but we expected ' . $penalty . '.');
        }
    }

    protected function check_output_contains_text_input($name, $value = null, $enabled = true) {
        $attributes = array(
            'type' => 'text',
            'name' => $this->quba->get_field_prefix($this->slot) . $name,
        );
        if (!is_null($value)) {
            $attributes['value'] = $value;
        }
        if (!$enabled) {
            $attributes['readonly'] = 'readonly';
        }
        $matcher = $this->get_tag_matcher('input', $attributes);
        $this->assertTag($matcher, $this->currentoutput,
                'Looking for an input with attributes ' . html_writer::attributes($attributes) . ' in ' . $this->currentoutput);

        if ($enabled) {
            $matcher['attributes']['readonly'] = 'readonly';
            $this->assertNotTag($matcher, $this->currentoutput,
                    'input with attributes ' . html_writer::attributes($attributes) .
                    ' should not be read-only in ' . $this->currentoutput);
        }
    }

    protected function check_output_contains_input_validation($name) {
        $id = $this->quba->get_question_attempt($this->slot)->get_qt_field_name($name . '_val');
        $this->assertRegExp('~<div (?=[^>]*\bclass="stackinputfeedback")(?=[^>]*\bid="' . $id . '")~', $this->currentoutput,
                'Input validation for ' . $name . ' not found in ' . $this->currentoutput);
    }

    protected function check_output_does_not_contain_any_input_validation() {
        $this->assertNotRegExp('~<div [^>]*\bclass="stackinputfeedback(?:(?! empty)[^"])*"~',
                $this->currentoutput, 'Input validation should not be present in ' . $this->currentoutput);
    }

    protected function check_output_does_not_contain_input_validation($name = null) {
        if (!$name) {
            $this->check_output_does_not_contain_any_input_validation();
            return;
        }
        $id = $this->quba->get_question_attempt($this->slot)->get_qt_field_name($name . '_val');
        $this->assertNotRegExp('~<div (?=[^>]*\bclass="stackinputfeedback")(?=[^>]*\bid="' . $id . '")~', $this->currentoutput,
                'Input validation for ' . $name . ' should not be present in ' . $this->currentoutput);
    }

    protected function check_output_contains_prt_feedback($name = null) {
        $class = 'stackprtfeedback';
        if ($name) {
            $class .= ' stackprtfeedback-' . $name;
        }
        $this->assertTag(array('tag' => 'div', 'attributes' => array('class' => $class)), $this->currentoutput,
                'PRT feedback for ' . $name . ' not found in ' . $this->currentoutput);
    }

    protected function check_output_does_not_contain_prt_feedback($name = null) {
        $class = 'stackprtfeedback';
        if ($name) {
            $class .= ' stackprtfeedback-' . $name;
        }
        $this->assertNotTag(array('tag' => 'div', 'attributes' => array('class' => $class)), $this->currentoutput,
                'PRT feedback for ' . $name . ' should not be present in ' . $this->currentoutput);
    }

    protected function check_output_does_not_contain_stray_placeholders() {
        $this->assertNotRegExp('~\[\[|\]\]~', $this->currentoutput, 'Not all placehoders were replaced.');
    }

    protected function check_output_contains_lang_string($identifier, $component = '', $a = null) {
        $string = get_string($identifier, $component, $a);
        $this->assertNotContains($string, $this->currentoutput,
                'Expected string ' . $string . ' not found in ' . $this->currentoutput);
    }

    protected function check_output_does_not_contain_lang_string($identifier, $component = '', $a = null) {
        $string = get_string($identifier, $component, $a);
        $this->assertContains($string, $this->currentoutput,
                'The string ' . $string . ' should not be present in ' . $this->currentoutput);
    }
}

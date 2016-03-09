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
require_once(__DIR__ . '/test_maxima_configuration.php');
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

        if (!qtype_stack_test_config::is_test_config_available()) {
            $testcase->markTestSkipped(
                    'To run the STACK unit tests, you must set up the Maxima configuration in phpunit.xml.');
        }

        qtype_stack_test_config::setup_test_maxima_connection();
    }

    /**
     * Verify that some content, containing maths, that is due to be output, is as expected.
     *
     * The purpose of this method is to hide the details of what the maths display system does.
     *
     * @param string $expected with plain maths delimiters. E.g. '<p>\(x + 1\)</p>'.
     * @param unknown $actual the actual output, as processed by the default Maths filter that STACK uses.
     */
    protected function assertContentWithMathsEquals($expected, $actual) {
        $this->assertEquals(self::prepare_expected_maths($expected), $actual);
    }

    /**
     * Verify that some content, containing maths, that is due to be output, is as expected.
     *
     * The purpose of this method is to hide the details of what the maths display system does.
     *
     * @param string $expected with plain maths delimiters. E.g. '<p>\(x + 1\)</p>'.
     * @param unknown $actual the actual output, as processed by the default Maths filter that STACK uses.
     */
    protected function assertContentWithMathsContains($expected, $actual) {
        $this->assertContains(self::prepare_expected_maths($expected), $actual);
    }

    /**
     * Prepare some content for comparison with rendered maths.
     * @param string $content Some content containing unprocessed maths like '<p>\(x + 1\)</p>'.
     * @return string The equivalent content, as it will look after maths processing.
     */
    public static function prepare_expected_maths($content) {
        $replacements = array(
                '\(' => '<span class="nolink"><span class="filter_mathjaxloader_equation">\(',
                '\)' => '\)</span></span>',
                '\[' => '<span class="nolink"><span class="filter_mathjaxloader_equation">\[',
                '\]' => '\]</span></span>',
        );

        return str_replace(array_keys($replacements), array_values($replacements), $content);
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
        $this->assertContains($string, $this->currentoutput,
                'Expected string ' . $string . ' not found in ' . $this->currentoutput);
    }

    protected function check_output_does_not_contain_lang_string($identifier, $component = '', $a = null) {
        $string = get_string($identifier, $component, $a);
        $this->assertNotContains($string, $this->currentoutput,
                'The string ' . $string . ' should not be present in ' . $this->currentoutput);
    }
}

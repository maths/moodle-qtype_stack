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

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once(__DIR__ . '/test_maxima_configuration.php');
require_once($CFG->dirroot . '/question/engine/tests/helpers.php');


/**
 * Base class for STACK unit tests. If you subclass this class, then code that
 * needs to call maxima will work (providing you have set up the neccessary
 * defines in phpunit.xml.
 *
 * @copyright  2012 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class qtype_stack_testcase extends advanced_testcase {

    /* Different underlying versions of LISP (behind Maxima) have different results,
     * especially with the floating point routines upon which Maxima relies.
     *
     * This must only be CLISP or SBCL.
     */
    protected $lisp = 'SBCL';

    public function setUp() :void {
        parent::setUp();

        stack_utils::clear_config_cache();
        self::setup_test_maxima_connection($this);

        $this->resetAfterTest();
    }

    /**
     * Helper that sets up the maxima configuration. This allows maxima to be used
     * from test classes that cannot subclass this one, for whatever reason.
     *
     * @param base_testcase $testcase the test-case we are doing the setup for.
     */
    public static function setup_test_maxima_connection($testcase) {
        if (!qtype_stack_test_config::is_test_config_available()) {
            $testcase->markTestSkipped(
                    'To run the STACK unit tests, you must set up the Maxima configuration in config.php.');
        }

        qtype_stack_test_config::setup_test_maxima_connection();
    }

    /**
     * Helper that skips a test if the version of Maxima used is at least as old as a given one.
     *
     * @param string $version e.g. '5.23.2'. Skip if the maxima version is <= this.
     */
    public function skip_if_old_maxima($version) {
        $versionused = get_config('qtype_stack', 'maximaversion');
        // The default version of Maxima is never "old".
        if ($versionused == 'default') {
            return;
        }
        if (version_compare($versionused, $version) <= 0) {
            $this->markTestSkipped(
                    'Skipping this test because it is known to fail on Maxima older than ' .
                    $version . ' and the tests are running with Maxima ' . $versionused . '.');
        }
    }

    /**
     * Helper that skips a test if the version of Maxima used is newer than a given one.
     *
     * @param string $version e.g. '5.23.2'. Skip if the maxima version is > this.
     */
    public function skip_if_new_maxima($version) {
        $versionused = get_config('qtype_stack', 'maximaversion');
        if ($versionused == 'default' || !(version_compare($versionused, $version) <= 0)) {
            $this->markTestSkipped(
                    'Skipping this test because it is known to fail on Maxima newer than ' .
                    $version . ' and the tests are running with Maxima ' . $versionused . '.');
        }
    }

    /**
     * Helper that returns true if we have default, or newer versions of Maxima.
     * Used to update the expected value of a test case.
     *
     * @param string $version e.g. '5.23.2'. True if the maxima version is default or > this.
     */
    public function adapt_to_new_maxima($version) {
        $versionused = get_config('qtype_stack', 'maximaversion');
        if ($versionused == 'default' || !(version_compare($versionused, $version) < 0)) {
            return true;
        }
        return false;
    }

    /**
     * Verify that some content, containing maths, that is due to be output, is as expected.
     *
     * The purpose of this method is to hide the details of what the maths display system does.
     *
     * @param string $expected with plain maths delimiters. E.g. '<p>\(x + 1\)</p>'.
     * @param string $actual the actual output, as processed by the default Maths filter that STACK uses.
     */
    protected function assert_content_with_maths_equals($expected, $actual) {
        $this->assertEquals($expected, self::prepare_actual_maths($actual));
    }

    /**
     * Verify that some content, containing maths, that is due to be output, is as expected.
     *
     * The purpose of this method is to hide the details of what the maths display system does.
     *
     * @param string $expected with plain maths delimiters. E.g. '<p>\(x + 1\)</p>'.
     * @param string $actual the actual output, as processed by the default Maths filter that STACK uses.
     */
    protected function assert_content_with_maths_contains($expected, $actual) {
        $this->assertStringContainsString($expected, self::prepare_actual_maths($actual));
    }

    /**
     * Prepare some content for comparison with expected maths but stripping out the
     * extra spans that the maths filder adds, so they don't get in the way of the comparison.
     * @param string $content Some content containing processed maths like
     * '<p><span class="nolink"><span class="filter_mathjaxloader_equation">\(x + 1\)</span></span></p>'.
     * @return string The equivalent content, without the extra spans.
     */
    public static function prepare_actual_maths($content) {
        // Eliminate differences caused just by how Moodle post-proceses equations for display,
        // which has changed between Moodle versions. We strip out the extra <span>s that
        // Moodle adds, so we can just compare the raw maths.
        $lastcontent = '';
        while ($lastcontent != $content) {
            $lastcontent = $content;
            $content = preg_replace(
                    '~(?:<span class="nolink">|<span class="filter_mathjaxloader_equation">)((?:(?!<span\b).)*?)</span>~s',
                    '$1', $content);
        }

        $content = self::prepare_actual_maths_floats($content);

        // Add .0 in 3E8 or 3.E8, to give 3.0E8.
        $content = preg_replace('~((?<!\.)\b-?\d+)\.?(E[-+]?\d+\b)~', '$1.0$2', $content);

        return $content;

    }

    /**
     * Different versions of Maxima output floats in slighly different ways.
     * Revert some of those irrelevant differences.
     * We always expect the E in 3.0E8 to be upper case.
     * Upper case is used to avoid a conflict with the base of the natural logarithms.
     */
    public static function prepare_actual_maths_floats($content) {

        // We don't add in a trailing zero at this point as that would break a decimal places display function test.
        $content = preg_replace('~(-?\b\d+(?:\.\d*)?)e([-+]?\d+\b)~', '$1E$2', $content);

        return $content;
    }

    /**
     * Compares two strings for equality. Ignoring multiplied whitespace e.g.
     * '\t\n ' ~ ' ' but '' != ' ' and E-surrounded by numeric characters is
     * assumed to be a float an thus case insensitive.
     */
    public function assert_equals_ignore_spaces_and_e(string $expected, string $actual) {

        // Data gathered on 26/06/2021.
        // Maxima 5.38.0 SBCL 1.2.4.debian.                1.0e-5.
        // Maxima 5.43.2 SBCL 1.4.14 (x86_64-w64-mingw32). 1.0e-5.
        // Maxima 5.42.1 GCL 2.6.12.                       1.0E-5.
        // Maxima 5.44.0 CLISP 2.49 (2010-07-07).          1.0E-5.

        $e = trim(preg_replace('/[\t\n\r\s]+/', ' ', $expected));
        $a = trim(preg_replace('/[\t\n\r\s]+/', ' ', $actual));
        $e = preg_replace('/([\d.])e([+\-\d])/', '$1E$2', $e);
        $a = preg_replace('/([\d.])e([+\-\d])/', '$1E$2', $a);
        // Make sure we have a trailing zero as well.  E.g. 1.0E2, not 1.E2.
        $e = preg_replace('~((?<!\.)\b-?\d+)\.?(E[-+]?\d+\b)~', '$1.0$2', $e);
        $a = preg_replace('~((?<!\.)\b-?\d+)\.?(E[-+]?\d+\b)~', '$1.0$2', $a);

        $this->assertEquals($e, $a);
    }

    // phpcs:ignore moodle.NamingConventions.ValidFunctionName.LowercaseMethod
    public static function assertMatchesRegularExpression(string $pattern, string $string, string $message = '') : void {
        // TODO remove this once Moodle 3.11 is the lowest supported version.
        if (method_exists('advanced_testcase', 'assertMatchesRegularExpression')) {
            parent::assertMatchesRegularExpression($pattern, $string, $message);
        } else {
            parent::assertRegExp($pattern, $string);
        }
    }
    // phpcs:enable

    // phpcs:ignore moodle.NamingConventions.ValidFunctionName.LowercaseMethod
    public static function assertDoesNotMatchRegularExpression(string $pattern, string $string, string $message = '') : void {
        // TODO remove this once Moodle 3.11 is the lowest supported version.
        if (method_exists('advanced_testcase', 'assertDoesNotMatchRegularExpression')) {
            parent::assertDoesNotMatchRegularExpression($pattern, $string, $message);
        } else {
            parent::assertNotRegExp($pattern, $string);
        }
    }
    // phpcs:enable
}


/**
 * Base class for STACK walkthrough tests.
 *
 * Sets up the Maxima connection, and provides some additional asserts.
 *
 * @copyright 2012 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class qtype_stack_walkthrough_test_base extends \qbehaviour_walkthrough_test_base {
    protected $currentoutput = null;

    public function setUp() :void {
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
        if (!array_key_exists($index, $question->prts)) {
            throw new Exception("The PRT $index does not exist in this question!");
        }
        $result   = $question->get_prt_result($index, $qa, $finalsubmit);

        if (is_null($score)) {
            $this->assertNull($result->get_score());
        } else {
            if ($score == 0) {
                // PHP will think a null and are equal, so explicity check not null.
                $this->assertNotNull($result->get_score());
            }
            $this->assertEquals($score, $result->get_score(), 'Wrong score.  The PRT returned ' .
                    $result->get_score() . ' but we expected ' . $score . '.');
        }

        if (is_null($penalty)) {
            $this->assertNull($result->get_penalty());
        } else {
            if ($penalty == 0) {
                // PHP will think a null and are equal, so explicity check not null.
                $this->assertNotNull($result->get_penalty());
            }
            $this->assertEquals($penalty, $result->get_penalty(), 'Wrong penalty.  The PRT returned ' .
                    $result->get_penalty() . ' but we expected ' . $penalty . '.');
        }
    }

    protected function check_answer_note($index, $note) {
        $question = $this->quba->get_question($this->slot);
        $attempt  = $this->quba->get_question_attempt($this->slot);
        $qa       = $attempt->get_last_qt_data();
        $result   = $question->get_prt_result($index, $qa, false);

        $this->assertEquals($note, implode(' | ', $result->get_answernotes()));
    }

    protected function check_response_summary($note) {
        $question = $this->quba->get_question($this->slot);
        $attempt  = $this->quba->get_question_attempt($this->slot);
        $qs = $attempt->get_last_step();
        $this->assertEquals($note, $qs->get_new_response_summary());
    }

    /*
     * This function is needed because Maxima versions generate different error messages.
     */
    protected function check_response_summary_contains($note) {
        $question = $this->quba->get_question($this->slot);
        $attempt  = $this->quba->get_question_attempt($this->slot);
        $qs = $attempt->get_last_step();
        $this->assertTrue((strpos($qs->get_new_response_summary(), $note) !== false));
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

    protected function check_output_contains_textarea_input($name, $content = null, $enabled = true) {
        $attributes = array(
                'name' => $this->quba->get_field_prefix($this->slot) . $name,
        );
        if (!$enabled) {
            $attributes['readonly'] = 'readonly';
        }
        $matcher = $this->get_tag_matcher('textarea', $attributes);
        $this->assertTag($matcher, $this->currentoutput,
                'Looking for a textarea with attributes ' . html_writer::attributes($attributes) . ' in ' .
                $this->currentoutput);

        if ($content) {
            $this->assertMatchesRegularExpression('/' . preg_quote(s($content), '/') . '/', $this->currentoutput);
        }

        if ($enabled) {
            $matcher['attributes']['readonly'] = 'readonly';
            $this->assertNotTag($matcher, $this->currentoutput,
                    'input with attributes ' . html_writer::attributes($attributes) .
                    ' should not be read-only in ' . $this->currentoutput);
        }
    }

    protected function check_output_contains_input_validation($name) {
        $id = $this->quba->get_question_attempt($this->slot)->get_qt_field_name($name . '_val');
        $this->assertMatchesRegularExpression('~<div (?=[^>]*\bclass="stackinputfeedback standard")(?=[^>]*\bid="' .
                $id . '")~',
                $this->currentoutput,
                'Input validation for ' . $name . ' not found in ' . $this->currentoutput);
    }

    protected function check_output_contains_input_validation_compact($name) {
        $id = $this->quba->get_question_attempt($this->slot)->get_qt_field_name($name . '_val');
        $this->assertMatchesRegularExpression('~<span (?=[^>]*\bclass="stackinputfeedback compact")(?=[^>]*\bid="' .
                $id . '")~',
                $this->currentoutput,
                'Input validation for ' . $name . ' not found in ' . $this->currentoutput);
    }

    protected function check_output_does_not_contain_any_input_validation() {
        $this->assertDoesNotMatchRegularExpression('~<div [^>]*\bclass="stackinputfeedback standard(?:(?! empty)[^"])*"~',
                $this->currentoutput, 'Input validation should not be present in ' . $this->currentoutput);
        $this->assertDoesNotMatchRegularExpression('~<div [^>]*\bclass="stackinputfeedback compact(?:(?! empty)[^"])*"~',
                $this->currentoutput, 'Input validation should not be present in ' . $this->currentoutput);
    }

    protected function check_output_does_not_contain_input_validation($name = null) {
        if (!$name) {
            $this->check_output_does_not_contain_any_input_validation();
            return;
        }
        $id = $this->quba->get_question_attempt($this->slot)->get_qt_field_name($name . '_val');
        $this->assertDoesNotMatchRegularExpression('~<div (?=[^>]*\bclass="stackinputfeedback standard")(?=[^>]*\bid="'
                . $id . '")~',
                $this->currentoutput,
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
        $this->assertDoesNotMatchRegularExpression('~\[\[|\]\]~', $this->currentoutput, 'Not all placehoders were replaced.');
    }

    protected function check_output_contains_lang_string($identifier, $component = '', $a = null) {
        $string = get_string($identifier, $component, $a);
        $this->assertStringContainsString($string, $this->currentoutput,
                'Expected string ' . $string . ' not found in ' . $this->currentoutput);
    }

    protected function check_output_does_not_contain_lang_string($identifier, $component = '', $a = null) {
        $string = get_string($identifier, $component, $a);
        $this->assertStringNotContainsString($string, $this->currentoutput,
                'The string ' . $string . ' should not be present in ' . $this->currentoutput);
    }

    /**
     * Verify that some content, containing maths, that is due to be output, is as expected.
     *
     * The purpose of this method is to hide the details of what the maths display system does.
     *
     * @param string $expected with plain maths delimiters. E.g. '<p>\(x + 1\)</p>'.
     * @param string $actual the actual output, as processed by the default Maths filter that STACK uses.
     */
    protected function assert_content_with_maths_equals($expected, $actual) {
        $this->assertEquals($expected, qtype_stack_testcase::prepare_actual_maths($actual));
    }

    /**
     * Verify that some content, containing maths, that is due to be output, is as expected.
     *
     * The purpose of this method is to hide the details of what the maths display system does.
     *
     * @param string $expected with plain maths delimiters. E.g. '<p>\(x + 1\)</p>'.
     * @param string $actual the actual output, as processed by the default Maths filter that STACK uses.
     */
    protected function assert_content_with_maths_contains($expected, $actual) {
        $this->assertStringContainsString($expected, qtype_stack_testcase::prepare_actual_maths($actual));
    }

    /**
     * Moodle has changed how the HTML for select menus is generated between versions. This method
     * compares expected and acutal HTML ignoring these differences.
     *
     * @param string $expected expected HTML
     * @param string $actual actual HTML
     */
    protected function assert_same_select_html($expected, $actual) {
        $actual = str_replace('class="select custom-select', 'class="select', $actual);
        $this->assertEquals($expected, $actual);
    }

    // phpcs:ignore moodle.NamingConventions.ValidFunctionName.LowercaseMethod
    public static function assertMatchesRegularExpression(string $pattern, string $string, string $message = '') : void {
        // TODO remove this once Moodle 3.11 is the lowest supported version.
        if (method_exists('advanced_testcase', 'assertMatchesRegularExpression')) {
            parent::assertMatchesRegularExpression($pattern, $string, $message);
        } else {
            parent::assertRegExp($pattern, $string);
        }
    }
    // phpcs:enable

    // phpcs:ignore moodle.NamingConventions.ValidFunctionName.LowercaseMethod
    public static function assertDoesNotMatchRegularExpression(string $pattern, string $string, string $message = '') : void {
        // TODO remove this once Moodle 3.11 is the lowest supported version.
        if (method_exists('advanced_testcase', 'assertDoesNotMatchRegularExpression')) {
            parent::assertDoesNotMatchRegularExpression($pattern, $string, $message);
        } else {
            parent::assertNotRegExp($pattern, $string);
        }
    }
    // phpcs:enable
}

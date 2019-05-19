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
 * A command-line script for generating unit tests to freeze the behaviour
 * of casstring parsing process for all possible combinations of settings.
 *
 * This tool generates a full test suite as its standard output, pipe that
 * where ever you want.
 *
 * When defining a test using this tool you must give arguments to this tool:
 *  1. the casstring e.g. as --string='foo 2/bar' is mandatory
 *  2. you may define allowed words as --allow='foo,[[basic-calculus]]'
 *  3. you may define forbidden words as --forbid='foo,[[basic-calculus]]'
 *  4. you may define forbidden keys as --keys='foo,bar'
 *
 * Currently, this tool generates the following tests:
 *  1. Teacher interetation of the input, i.e. no insertted stars.
 *  2. The insert stars 0-5 tests, the interpreted parse tree and validity.
 *  3. Same with allow words if defined.
 *  4. ... with forbid words if defined.
 *  5. ... with forbid keys if defined.
 *  6. ... with all those if more than one defined.
 *  7. 2-6 with units enabled.
 *
 * In the future this tool could be extended to include more combinations.
 *
 * @package   qtype_stack
 * @copyright 2019 Aalto University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);
require(__DIR__ . '/../../../../config.php');
require_once($CFG->libdir . '/clilib.php');
require_once(__DIR__ . '/../stack/cas/casstring.class.php');
require_once(__DIR__ . '/../stack/cas/cassecurity.class.php');

list($options, $unrecognized) = cli_get_params(array('help' => false,
                                                     'string' => '1+2x',
                                                     'allow' => '',
                                                     'forbid' => '',
                                                     'keys' => ''),
    array('h' => 'help'));
if ($unrecognized) {
    $unrecognized = implode("\n  ", $unrecognized);
    cli_error(get_string('cliunknowoption', 'admin', $unrecognized));
}
if ($options['help']) {
    $help =
        "Generates an unit test description of the current behaviour of casstring
parsing. The options are as follows:

          --string='1+2x'
          --allow='foo,[[basic-calculus]]'
          --forbid='foo,[[basic-calculus]]'
          --keys='ta,solution'
        ";
    echo $help;
    die;
}

// Some important strings.
$nl = "\n";
$indent = '    ';
$indent2 = $indent . $indent;
$out = '<?' . 'php' . $nl;

$teststring = '1+2x';
if (isset($options['string'])) {
    $teststring = $options['string'];
}
$allow = '';
if (isset($options['allow'])) {
    $allow = $options['allow'];
}
$forbid = '';
if (isset($options['forbid'])) {
    $forbid = $options['forbid'];
}
$keys = array();
if (isset($options['keys'])) {
    $keys = explode(',', $options['keys']);
    $keys = array_map('trim', $keys);
}

// We need to come up with a "unique" name for the test class.
$testidentifier = hash('crc32', $teststring);

// We assume that whatever is generated goes into the question/type/stack//tests/
// directory and no deepper, if and probably when otherwise modify the following bit.
$out .= <<<ESCAPE
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

require_once(__DIR__ . '/../locallib.php');
require_once(__DIR__ . '/../stack/cas/casstring.class.php');

// Auto-generated unit tests for {@link stack_cas_casstring}.

ESCAPE;

$out .= '// Using input \'' . $teststring . '\'' . $nl;
if ($allow !== '') {
    $out .= '// Using allow \'' . $allow . '\'' . $nl;
}
if ($forbid !== '') {
    $out .= '// Using forbid \'' . $forbid . '\'' . $nl;
}

$out .= <<<ESCAPE
// @copyright  2019 STACK project.
// @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
// @group qtype_stack.

/**
 * @group qtype_stack
 */

ESCAPE;

$out .= 'class stack_cas_casstring_' . $testidentifier . '_test extends basic_testcase {' . $nl;

// Lets store the common teststring. and other bits as static variables
// of the test class, note we use this string representation to overcome all
// possible escape issues, except of course ^ESCAPE... also works for
// parsing in these test files, for analysis...

$out .= $indent . 'static $teststring = <<<\'ESCAPESTRING\'' . $nl .
            $teststring . $nl . 'ESCAPESTRING;' . $nl;

if ($allow !== '') {
    $out .= $indent . 'static $allow = <<<\'ESCAPEALLOW\'' . $nl .
                $allow . $nl . 'ESCAPEALLOW;' . $nl;
}
if ($forbid !== '') {
    $out .= $indent . 'static $forbid = <<<\'ESCAPEFORBID\'' . $nl .
                $forbid . $nl . 'ESCAPEFORBID;' . $nl;
}
// Lets assume that the keys if defined are better defined.
if (count($keys) > 0) {
    $out .= $indent . 'static $keys = array(\'' . implode('\', \'', array_map('addslashes', $keys)) . '\');' . $nl;
}

// First the check against teacher rights.
$cs = new stack_cas_casstring($teststring);

$out .= $nl . $indent . 'public function test_teacher() {';
$out .= $nl . $indent2 . '$cs = new stack_cas_casstring(self::$teststring);';

if ($cs->get_valid('t')) {
    $out .= $nl . $indent2 . '$this->assertTrue($cs->get_valid(\'t\'));';
    $out .= $nl . $indent2 . '$presentation = <<<\'ESCAPEDEBUG\'' . $nl;
    $out .= $cs->ast->debugPrint($teststring);
    $out .= $nl . 'ESCAPEDEBUG;';
    $out .= $nl . $indent2 . '$this->assertEquals($presentation, $cs->ast->debugPrint(self::$teststring));';
    $out .= $nl . $indent2 . '$this->assertEquals(\'' . $cs->get_casstring() . '\', $cs->get_casstring());';
} else {
    $out .= $nl . $indent2 . '$this->assertFalse($cs->get_valid(\'t\'));';
    // In all cases if it is invalid there must be some error messages.
    $out .= $nl . $indent2 . '$this->assertTrue(count($cs->get_errors(true)) > 0);';
}

foreach ($cs->get_answernote(true) as $note) {
    $out .= $nl . $indent2 . '$this->assertContains(\'' . addslashes($note) . '\', $cs->get_answernote(true));';
}

$out .= $nl . $indent .'}';

// Then the insert stars with default settings.
for ($is = 0; $is <= 5; $is++) {
    $out .= $nl . $nl . $indent . 'public function test_insert_stars_' . $is . '_defaults() {';
    $out .= $nl . $indent2 . '$cs = new stack_cas_casstring(self::$teststring);';
    $cs = new stack_cas_casstring($teststring);

    if ($cs->get_valid('s', true, $is)) {
        $out .= $nl . $indent2 . '$this->assertTrue($cs->get_valid(\'s\', true, ' . $is . '));';
        $out .= $nl . $indent2 . '$presentation = <<<\'ESCAPEDEBUG\'' . $nl;
        $out .= $cs->ast->debugPrint($teststring);
        $out .= $nl . 'ESCAPEDEBUG;';
        $out .= $nl . $indent2 . '$this->assertEquals($presentation, $cs->ast->debugPrint(self::$teststring));';
        $out .= $nl . $indent2 . '$this->assertEquals(\'' . $cs->get_casstring() . '\', $cs->get_casstring());';
    } else {
        $out .= $nl . $indent2 . '$this->assertFalse($cs->get_valid(\'s\', true, ' . $is . '));';
        // In all cases if it is invalid there must be some error messages.
        $out .= $nl . $indent2 . '$this->assertTrue(count($cs->get_errors(true)) > 0);';
    }

    foreach ($cs->get_answernote(true) as $note) {
        $out .= $nl . $indent2 . '$this->assertContains(\'' . addslashes($note) . '\', $cs->get_answernote(true));';
    }

    $out .= $nl . $indent .'}';
}

// Then the insert stars with units.
for ($is = 0; $is <= 5; $is++) {
    $out .= $nl . $nl . $indent . 'public function test_insert_stars_' . $is . '_units() {';
    $out .= $nl . $indent2 . '$cs = new stack_cas_casstring(self::$teststring);';
    $out .= $nl . $indent2 . '$cs->set_context(\'units\', true);';
    $cs = new stack_cas_casstring($teststring);
    $cs->set_context('units', true);

    if ($cs->get_valid('s', true, $is)) {
        $out .= $nl . $indent2 . '$this->assertTrue($cs->get_valid(\'s\', true, ' . $is . '));';
        $out .= $nl . $indent2 . '$presentation = <<<\'ESCAPEDEBUG\'' . $nl;
        $out .= $cs->ast->debugPrint($teststring);
        $out .= $nl . 'ESCAPEDEBUG;';
        $out .= $nl . $indent2 . '$this->assertEquals($presentation, $cs->ast->debugPrint(self::$teststring));';
        $out .= $nl . $indent2 . '$this->assertEquals(\'' . $cs->get_casstring() . '\', $cs->get_casstring());';
    } else {
        $out .= $nl . $indent2 . '$this->assertFalse($cs->get_valid(\'s\', true, ' . $is . '));';
        // In all cases if it is invalid there must be some error messages.
        $out .= $nl . $indent2 . '$this->assertTrue(count($cs->get_errors(true)) > 0);';
    }

    foreach ($cs->get_answernote(true) as $note) {
        $out .= $nl . $indent2 . '$this->assertContains(\'' . addslashes($note) . '\', $cs->get_answernote(true));';
    }

    $out .= $nl . $indent .'}';
}

// Then the same for allow if defined.
if ($allow !== '') {
    // Insert stars with default settings.
    for ($is = 0; $is <= 5; $is++) {
        $out .= $nl . $nl . $indent . 'public function test_insert_stars_' . $is . '_defaults_allow() {';
        $out .= $nl . $indent2 . '$cs = new stack_cas_casstring(self::$teststring);';
        $out .= $nl . $indent2 . '$security = new stack_cas_security(false, self::$allow);';
        $security = new stack_cas_security(false, $allow);

        $cs = new stack_cas_casstring($teststring);

        if ($cs->get_valid('s', true, $is, $security)) {
            $out .= $nl . $indent2 . '$this->assertTrue($cs->get_valid(\'s\', true, ' . $is . ', $security));';
            $out .= $nl . $indent2 . '$presentation = <<<\'ESCAPEDEBUG\'' . $nl;
            $out .= $cs->ast->debugPrint($teststring);
            $out .= $nl . 'ESCAPEDEBUG;';
            $out .= $nl . $indent2 . '$this->assertEquals($presentation, $cs->ast->debugPrint(self::$teststring));';
            $out .= $nl . $indent2 . '$this->assertEquals(\'' . $cs->get_casstring() . '\', $cs->get_casstring());';
        } else {
            $out .= $nl . $indent2 . '$this->assertFalse($cs->get_valid(\'s\', true, ' . $is . ', $security));';
            // In all cases if it is invalid there must be some error messages.
            $out .= $nl . $indent2 . '$this->assertTrue(count($cs->get_errors(true)) > 0);';
        }

        foreach ($cs->get_answernote(true) as $note) {
            $out .= $nl . $indent2 . '$this->assertContains(\'' . addslashes($note) . '\', $cs->get_answernote(true));';
        }

        $out .= $nl . $indent .'}';
    }

    // Insert stars with units.
    for ($is = 0; $is <= 5; $is++) {
        $out .= $nl . $nl . $indent . 'public function test_insert_stars_' . $is . '_units_allow() {';
        $out .= $nl . $indent2 . '$cs = new stack_cas_casstring(self::$teststring);';
        $out .= $nl . $indent2 . '$security = new stack_cas_security(true, self::$allow);';
        $out .= $nl . $indent2 . '$cs->set_context(\'units\', true);';
        $security = new stack_cas_security(true, $allow);

        $cs = new stack_cas_casstring($teststring);
        $cs->set_context('units', true);

        if ($cs->get_valid('s', true, $is, $security)) {
            $out .= $nl . $indent2 . '$this->assertTrue($cs->get_valid(\'s\', true, ' . $is . ', $security));';
            $out .= $nl . $indent2 . '$presentation = <<<\'ESCAPEDEBUG\'' . $nl;
            $out .= $cs->ast->debugPrint($teststring);
            $out .= $nl . 'ESCAPEDEBUG;';
            $out .= $nl . $indent2 . '$this->assertEquals($presentation, $cs->ast->debugPrint(self::$teststring));';
            $out .= $nl . $indent2 . '$this->assertEquals(\'' . $cs->get_casstring() . '\', $cs->get_casstring());';
        } else {
            $out .= $nl . $indent2 . '$this->assertFalse($cs->get_valid(\'s\', true, ' . $is . ', $security));';
            // In all cases if it is invalid there must be some error messages.
            $out .= $nl . $indent2 . '$this->assertTrue(count($cs->get_errors(true)) > 0);';
        }

        foreach ($cs->get_answernote(true) as $note) {
            $out .= $nl . $indent2 . '$this->assertContains(\'' . addslashes($note) . '\', $cs->get_answernote(true));';
        }

        $out .= $nl . $indent .'}';
    }
}

// Then the same for forbid if defined.
if ($forbid !== '') {
    // Insert stars with default settings.
    for ($is = 0; $is <= 5; $is++) {
        $out .= $nl . $nl . $indent . 'public function test_insert_stars_' . $is . '_defaults_forbid() {';
        $out .= $nl . $indent2 . '$cs = new stack_cas_casstring(self::$teststring);';
        $out .= $nl . $indent2 . '$security = new stack_cas_security(false, \'\', self::$forbid);';
        $security = new stack_cas_security(false, '', $forbid);

        $cs = new stack_cas_casstring($teststring);

        if ($cs->get_valid('s', true, $is, $security)) {
            $out .= $nl . $indent2 . '$this->assertTrue($cs->get_valid(\'s\', true, ' . $is . ', $security));';
            $out .= $nl . $indent2 . '$presentation = <<<\'ESCAPEDEBUG\'' . $nl;
            $out .= $cs->ast->debugPrint($teststring);
            $out .= $nl . 'ESCAPEDEBUG;';
            $out .= $nl . $indent2 . '$this->assertEquals($presentation, $cs->ast->debugPrint(self::$teststring));';
            $out .= $nl . $indent2 . '$this->assertEquals(\'' . $cs->get_casstring() . '\', $cs->get_casstring());';
        } else {
            $out .= $nl . $indent2 . '$this->assertFalse($cs->get_valid(\'s\', true, ' . $is . ', $security));';
            // In all cases if it is invalid there must be some error messages.
            $out .= $nl . $indent2 . '$this->assertTrue(count($cs->get_errors(true)) > 0);';
        }

        foreach ($cs->get_answernote(true) as $note) {
            $out .= $nl . $indent2 . '$this->assertContains(\'' . addslashes($note) . '\', $cs->get_answernote(true));';
        }

        $out .= $nl . $indent .'}';
    }

    // Insert stars with units.
    for ($is = 0; $is <= 5; $is++) {
        $out .= $nl . $nl . $indent . 'public function test_insert_stars_' . $is . '_units_forbid() {';
        $out .= $nl . $indent2 . '$cs = new stack_cas_casstring(self::$teststring);';
        $out .= $nl . $indent2 . '$security = new stack_cas_security(true, \'\', self::$forbid);';
        $out .= $nl . $indent2 . '$cs->set_context(\'units\', true);';
        $security = new stack_cas_security(true, '', $forbid);

        $cs = new stack_cas_casstring($teststring);
        $cs->set_context('units', true);

        if ($cs->get_valid('s', true, $is, $security)) {
            $out .= $nl . $indent2 . '$this->assertTrue($cs->get_valid(\'s\', true, ' . $is . ', $security));';
            $out .= $nl . $indent2 . '$presentation = <<<\'ESCAPEDEBUG\'' . $nl;
            $out .= $cs->ast->debugPrint($teststring);
            $out .= $nl . 'ESCAPEDEBUG;';
            $out .= $nl . $indent2 . '$this->assertEquals($presentation, $cs->ast->debugPrint(self::$teststring));';
            $out .= $nl . $indent2 . '$this->assertEquals(\'' . $cs->get_casstring() . '\', $cs->get_casstring());';
        } else {
            $out .= $nl . $indent2 . '$this->assertFalse($cs->get_valid(\'s\', true, ' . $is . ', $security));';
            // In all cases if it is invalid there must be some error messages.
            $out .= $nl . $indent2 . '$this->assertTrue(count($cs->get_errors(true)) > 0);';
        }

        foreach ($cs->get_answernote(true) as $note) {
            $out .= $nl . $indent2 . '$this->assertContains(\'' . addslashes($note) . '\', $cs->get_answernote(true));';
        }

        $out .= $nl . $indent .'}';
    }
}

// Keys...
if (count($keys) > 0) {
    // Insert stars with default settings.
    for ($is = 0; $is <= 5; $is++) {
        $out .= $nl . $nl . $indent . 'public function test_insert_stars_' . $is . '_defaults_keys() {';
        $out .= $nl . $indent2 . '$cs = new stack_cas_casstring(self::$teststring);';
        $out .= $nl . $indent2 . '$security = new stack_cas_security(false, \'\', \'\', self::$keys);';
        $security = new stack_cas_security(false, '', '', $keys);

        $cs = new stack_cas_casstring($teststring);

        if ($cs->get_valid('s', true, $is, $security)) {
            $out .= $nl . $indent2 . '$this->assertTrue($cs->get_valid(\'s\', true, ' . $is . ', $security));';
            $out .= $nl . $indent2 . '$presentation = <<<\'ESCAPEDEBUG\'' . $nl;
            $out .= $cs->ast->debugPrint($teststring);
            $out .= $nl . 'ESCAPEDEBUG;';
            $out .= $nl . $indent2 . '$this->assertEquals($presentation, $cs->ast->debugPrint(self::$teststring));';
            $out .= $nl . $indent2 . '$this->assertEquals(\'' . $cs->get_casstring() . '\', $cs->get_casstring());';
        } else {
            $out .= $nl . $indent2 . '$this->assertFalse($cs->get_valid(\'s\', true, ' . $is . ', $security));';
            // In all cases if it is invalid there must be some error messages.
            $out .= $nl . $indent2 . '$this->assertTrue(count($cs->get_errors(true)) > 0);';
        }

        foreach ($cs->get_answernote(true) as $note) {
            $out .= $nl . $indent2 . '$this->assertContains(\'' . addslashes($note) . '\', $cs->get_answernote(true));';
        }

        $out .= $nl . $indent .'}';
    }

    // Insert stars with units.
    for ($is = 0; $is <= 5; $is++) {
        $out .= $nl . $nl . $indent . 'public function test_insert_stars_' . $is . '_units_keys() {';
        $out .= $nl . $indent2 . '$cs = new stack_cas_casstring(self::$teststring);';
        $out .= $nl . $indent2 . '$security = new stack_cas_security(true, \'\', \'\', self::$keys);';
        $out .= $nl . $indent2 . '$cs->set_context(\'units\', true);';
        $security = new stack_cas_security(true, '', '', $keys);

        $cs = new stack_cas_casstring($teststring);
        $cs->set_context('units', true);

        if ($cs->get_valid('s', true, $is, $security)) {
            $out .= $nl . $indent2 . '$this->assertTrue($cs->get_valid(\'s\', true, ' . $is . ', $security));';
            $out .= $nl . $indent2 . '$presentation = <<<\'ESCAPEDEBUG\'' . $nl;
            $out .= $cs->ast->debugPrint($teststring);
            $out .= $nl . 'ESCAPEDEBUG;';
            $out .= $nl . $indent2 . '$this->assertEquals($presentation, $cs->ast->debugPrint(self::$teststring));';
            $out .= $nl . $indent2 . '$this->assertEquals(\'' . $cs->get_casstring() . '\', $cs->get_casstring());';
        } else {
            $out .= $nl . $indent2 . '$this->assertFalse($cs->get_valid(\'s\', true, ' . $is . ', $security));';
            // In all cases if it is invalid there must be some error messages.
            $out .= $nl . $indent2 . '$this->assertTrue(count($cs->get_errors(true)) > 0);';
        }

        foreach ($cs->get_answernote(true) as $note) {
            $out .= $nl . $indent2 . '$this->assertContains(\'' . addslashes($note) . '\', $cs->get_answernote(true));';
        }

        $out .= $nl . $indent .'}';
    }
}

// If more than one security then combined.
if (($allow !== '' && $forbid !== '') ||
    ($allow !== '' && count($keys) > 0) ||
    ($forbid !== '' && count($keys) > 0)) {
    // Insert stars with default settings.
    for ($is = 0; $is <= 5; $is++) {
        $out .= $nl . $nl . $indent . 'public function test_insert_stars_' . $is . '_defaults_combined() {';
        $out .= $nl . $indent2 . '$cs = new stack_cas_casstring(self::$teststring);';
        $out .= $nl . $indent2 . '$security = new stack_cas_security(false);';
        $security = new stack_cas_security(false);
        if ($allow !== '') {
            $security->set_allowedwords($allow);
            $out .= $nl . $indent2 . '$security->set_allowedwords(self::$allow);';
        }
        if ($forbid !== '') {
            $security->set_forbiddenwords($forbid);
            $out .= $nl . $indent2 . '$security->set_forbiddenwords(self::$forbid);';
        }
        if (count($keys) > 0) {
            $security->set_forbiddenkeys($keys);
            $out .= $nl . $indent2 . '$security->set_forbiddenkeys(self::$keys);';
        }
        $cs = new stack_cas_casstring($teststring);

        if ($cs->get_valid('s', true, $is, $security)) {
            $out .= $nl . $indent2 . '$this->assertTrue($cs->get_valid(\'s\', true, ' . $is . ', $security));';
            $out .= $nl . $indent2 . '$presentation = <<<\'ESCAPEDEBUG\'' . $nl;
            $out .= $cs->ast->debugPrint($teststring);
            $out .= $nl . 'ESCAPEDEBUG;';
            $out .= $nl . $indent2 . '$this->assertEquals($presentation, $cs->ast->debugPrint(self::$teststring));';
            $out .= $nl . $indent2 . '$this->assertEquals(\'' . $cs->get_casstring() . '\', $cs->get_casstring());';
        } else {
            $out .= $nl . $indent2 . '$this->assertFalse($cs->get_valid(\'s\', true, ' . $is . ', $security));';
            // In all cases if it is invalid there must be some error messages.
            $out .= $nl . $indent2 . '$this->assertTrue(count($cs->get_errors(true)) > 0);';
        }

        foreach ($cs->get_answernote(true) as $note) {
            $out .= $nl . $indent2 . '$this->assertContains(\'' . addslashes($note) . '\', $cs->get_answernote(true));';
        }

        $out .= $nl . $indent .'}';
    }

    // Insert stars with units.
    for ($is = 0; $is <= 5; $is++) {
        $out .= $nl . $nl . $indent . 'public function test_insert_stars_' . $is . '_units_combined() {';
        $out .= $nl . $indent2 . '$cs = new stack_cas_casstring(self::$teststring);';
        $out .= $nl . $indent2 . '$security = new stack_cas_security(true);';
        $out .= $nl . $indent2 . '$cs->set_context(\'units\', true);';
        $security = new stack_cas_security(true);
        if ($allow !== '') {
            $security->set_allowedwords($allow);
            $out .= $nl . $indent2 . '$security->set_allowedwords(self::$allow);';
        }
        if ($forbid !== '') {
            $security->set_forbiddenwords($forbid);
            $out .= $nl . $indent2 . '$security->set_forbiddenwords(self::$forbid);';
        }
        if (count($keys) > 0) {
            $security->set_forbiddenkeys($keys);
            $out .= $nl . $indent2 . '$security->set_forbiddenkeys(self::$keys);';
        }
        $cs = new stack_cas_casstring($teststring);
        $cs->set_context('units', true);

        if ($cs->get_valid('s', true, $is, $security)) {
            $out .= $nl . $indent2 . '$this->assertTrue($cs->get_valid(\'s\', true, ' . $is . ', $security));';
            $out .= $nl . $indent2 . '$presentation = <<<\'ESCAPEDEBUG\'' . $nl;
            $out .= $cs->ast->debugPrint($teststring);
            $out .= $nl . 'ESCAPEDEBUG;';
            $out .= $nl . $indent2 . '$this->assertEquals($presentation, $cs->ast->debugPrint(self::$teststring));';
            $out .= $nl . $indent2 . '$this->assertEquals(\'' . $cs->get_casstring() . '\', $cs->get_casstring());';
        } else {
            $out .= $nl . $indent2 . '$this->assertFalse($cs->get_valid(\'s\', true, ' . $is . ', $security));';
            // In all cases if it is invalid there must be some error messages.
            $out .= $nl . $indent2 . '$this->assertTrue(count($cs->get_errors(true)) > 0);';
        }

        foreach ($cs->get_answernote(true) as $note) {
            $out .= $nl . $indent2 . '$this->assertContains(\'' . addslashes($note) . '\', $cs->get_answernote(true));';
        }

        $out .= $nl . $indent .'}';
    }
}

$out .= $nl . '}';

echo $out;
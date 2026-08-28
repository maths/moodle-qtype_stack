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
 * Unit tests for the example `stack_require` libraries in 
 * `/stack/maxima/contrib/stack_require_example/`
 *
 * As those libraries are not present in the normal library store, this
 * will contain some duplication and overrides of the general library 
 * unit testing setup.
 *
 * @package    qtype_stack
 * @copyright 2026 Aalto University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

namespace qtype_stack;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../stack/cas/contriblibrarytools.class.php');
require_once(__DIR__ . '/../stack/cas/keyval.class.php');
require_once(__DIR__ . '/../stack/cas/cassession2.class.php');
require_once(__DIR__ . '/../stack/cas/ast.container.class.php');

use qtype_stack_testcase;
use stack_cas_contrib_library_tools;
use stack_cas_keyval;
use stack_ast_container;
/**
 * Add description here.
 * @group qtype_stack
 * @group qtype_stack_require
 * @covers \qtype_stack
 */
final class stack_require_test extends qtype_stack_testcase {

	/**
	 * Loads the special lib1 and lib2 manifests from the special places.
	 */
	public function get_manifests(): array {
		static $cache = null;
		if ($cache !== null) {
			return $cache;
		}
		$cache = [
			'lib1' => json_decode(file_get_contents(__DIR__ . '/../stack/maxima/contrib/stack_require_example/lib1.stacklib'), true),
			'lib2' => json_decode(file_get_contents(__DIR__ . '/../stack/maxima/contrib/stack_require_example/lib2.stacklib'), true),
		];
		return $cache;
	}

    public function test_non_local_dependency(): void {
    	/*
    	 * In this case lib1/lib1a_fun1 has a dependency present in lib2 which again depends on lib1.
    	 * Will all relevant bits get loaded, when we only require in this style:
    	 *  `stack_require("lib1",["lib1a_fun1"])`
         *
         * Basically, if the item in the manifest states that it requires things from
         * another library then those will be fetched as long as the other library is
         * in the default place. In the test-case we use the override logic to provide
         * that library so that we do not need to look for it from that place.
    	 */ 
    	list($code, $preamble, $loads) = stack_cas_contrib_library_tools::fetch_requirements(
            'lib1', self::get_manifests(), ['lib1a_fun1']
        );

    	$this->assertTrue(isset($loads['lib2/lib2_fun2']), 'Must include the lib2 sourced item in the loads.');
    	$this->assertTrue(isset($loads['lib1/lib1a_fun2']), 'Must include the lib1 item that the lib2 item needs in the loads.');
    	$this->assertTrue(strpos($code, 'lib2_fun2(mode):=') !== false, 'Must include the definition for that lib2 item in the code.');

    	// Then the equivalent session.
    	$kv = new stack_cas_keyval($code . $preamble);
    	$ses = $kv->get_session();

    	// Add some test to it.
    	$test1 = stack_ast_container::make_from_teacher_source("lib1a_fun1(true);");
    	$ses->add_statement($test1);

    	$ses->instantiate();

    	$this->assertEquals('"1/fun1, 2/fun2, 1/fun2"', $test1->get_value(), 'Must have evaluated to the expected value.');
    }

    public function test_selective_preamble(): void {
    	/*
    	 * In this case lib1/lib1a_value1 has a preamble texput rule from the lib1a.mac file.
    	 * The interesting thing is that lib1/lib1a_value1 = lib1/lib1b_value1 and as long as
    	 * we do not include anything from lib1a then the value from lib1b does not get that 
    	 * texput rule.
		 *
    	 * Basically, a library preamble logic does not need to trigger for the whole library,
    	 * some parts may trigger different things, e.g., different cache population logic etc...
    	 */ 
    	// `stack_require("lib1",["lib1a_value1"])`
    	list($codea, $preamblea, $loadsa) = stack_cas_contrib_library_tools::fetch_requirements(
            'lib1', self::get_manifests(), ['lib1a_value1']
        );
        $this->assertTrue(isset($loadsa['lib1/lib1b_value1']), 'Must include the lib1b sourced item in the loads.');
    	$this->assertTrue(strpos($preamblea, 'texput(') !== false, 'Must include texput in the preamble.');

    	// Then the equivalent session.
    	$kva = new stack_cas_keyval($codea . $preamblea);
    	$sesa = $kva->get_session();

    	// Add some test to it.
    	$test1 = stack_ast_container::make_from_teacher_source("tex1(lib1a_value1);");
    	$sesa->add_statement($test1);

    	$sesa->instantiate();

		$this->assertEquals('"\\\\sqrt{2}"', $test1->get_value(), 'Must have evaluated to the expected value.');

		// `stack_require("lib1",["lib1b_value1"])`
    	list($codeb, $preambleb, $loadsb) = stack_cas_contrib_library_tools::fetch_requirements(
            'lib1', self::get_manifests(), ['lib1b_value1']
        );
		$this->assertFalse(isset($loadsb['lib1/lib1a_value1']), 'Must not include the lib1a sourced item in the loads.');
		$this->assertTrue(strpos($preambleb, 'texput(') === false, 'Must not include texput in the preamble.');

    	// Then the equivalent session.
    	$kvb = new stack_cas_keyval($codeb . $preambleb);
    	$sesb = $kvb->get_session();

    	// Add some test to it.
    	$test2 = stack_ast_container::make_from_teacher_source("tex1(lib1a_value1);");
    	$sesb->add_statement($test2);

    	$sesb->instantiate();

		$this->assertEquals('"{\\\\it lib1a\\\\_value}_{1}"', $test2->get_value(), 'Must have evaluated to the expected value.');
    }
}
<?php
// This file is part of Stateful.
//
// Stateful is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Stateful is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Stateful.  If not, see <http://www.gnu.org/licenses/>.

defined('MOODLE_INTERNAL') || die();

// Unit tests to check that the requirements of a related software
// are still present, and that the interface does not change unexpectedly.
//
// @copyright  2020 Aalto University
// @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.

// These are all the imports Stateful does, one test is to check that these exist.
// Even the test_base is one of them.
require_once(__DIR__ . '/fixtures/test_base.php');
require_once(__DIR__ . '/../locallib.php');
require_once(__DIR__ . '/../stack/options.class.php');
require_once(__DIR__ . '/../stack/utils.class.php');
require_once(__DIR__ . '/../stack/input/inputbase.class.php');
require_once(__DIR__ . '/../stack/input/factory.class.php');
require_once(__DIR__ . '/../stack/cas/casstring.units.class.php');
require_once(__DIR__ . '/../stack/mathsoutput/mathsoutput.class.php');
require_once(__DIR__ . '/../stack/maximaparser/utils.php');
require_once(__DIR__ . '/../stack/input/inputstate.class.php');
require_once(__DIR__ . '/../stack/cas/ast.container.class.php');
require_once(__DIR__ . '/../stack/cas/ast.container.silent.class.php');
require_once(__DIR__ . '/../stack/cas/secure_loader.class.php');
require_once(__DIR__ . '/../stack/cas/keyval.class.php');
require_once(__DIR__ . '/../stack/cas/cassession2.class.php');
require_once(__DIR__ . '/../renderer.php');
require_once(__DIR__ . '/../stack/cas/parsingrules/parsingrule.factory.php');
require_once(__DIR__ . '/../stack/cas/evaluatable_object.interfaces.php');


/**
 * @group qtype_stack
 * @group qtype_stateful
 * @group qtype_stack_compatibility
 */
class stack_api_compatibility_stateful_test extends qtype_stack_testcase {

    public function test_security_map_path() {
        $this->assertTrue(file_exists(__DIR__ . '/../stack/cas/security-map.json'));
        $map = file_get_contents(__DIR__ . '/../stack/cas/security-map.json');
        $map = json_decode($map);
        $this->assertEquals(JSON_ERROR_NONE, json_last_error());
    }

    public function test_names_of_interfaces_and_classes() {
        $this->assertTrue(class_exists("MP_Root"));
        $this->assertTrue(class_exists("MP_Statement"));
        $this->assertTrue(class_exists("MP_FunctionCall"));
        $this->assertTrue(class_exists("MP_Identifier"));
        $this->assertTrue(class_exists("MP_Integer"));
        $this->assertTrue(class_exists("MP_String"));
        $this->assertTrue(class_exists("MP_List"));
        $this->assertTrue(class_exists("MP_Set"));
        $this->assertTrue(class_exists("MP_Atom"));

        $this->assertTrue(class_exists("stack_ast_container"));
        $this->assertTrue(class_exists("stack_ast_container_silent"));
        $this->assertTrue(class_exists("stack_secure_loader"));
        $this->assertTrue(class_exists("stack_secure_loader_value"));
        $this->assertTrue(class_exists("stack_cas_keyval"));
        $this->assertTrue(class_exists("stack_cas_session2"));
        $this->assertTrue(class_exists("stack_cas_security"));
        $this->assertTrue(class_exists("stack_cas_keyval"));
        $this->assertTrue(class_exists("stack_utils"));
        $this->assertTrue(class_exists("stack_maths"));
        $this->assertTrue(class_exists("stack_options"));
        $this->assertTrue(class_exists("maxima_parser_utils"));

        $this->assertTrue(interface_exists("cas_raw_value_extractor"));
    }

    public function test_stack_utils() {
        $this->assertTrue(method_exists('stack_utils', 'get_config'));
        $this->assertTrue(method_exists('stack_utils', 'php_string_to_maxima_string'));
        $this->assertTrue(method_exists('stack_utils', 'maxima_string_to_php_string'));
        $this->assertTrue(method_exists('stack_utils', 'all_substring_strings'));
        $this->assertTrue(method_exists('stack_utils', 'eliminate_strings'));
        $this->assertTrue(method_exists('stack_utils', 'list_to_array'));

        $this->assertTrue(isset(stack_utils::get_config()->stackmaximaversion));
        $this->assertEquals('"\"foo"', stack_utils::php_string_to_maxima_string('"foo'));
        $this->assertEquals('"foo', stack_utils::maxima_string_to_php_string('"\"foo"'));
        $this->assertEquals('["","",1+""]', stack_utils::eliminate_strings('["foo","bar",1+"baz"]'));
        $this->assertEquals(["foo", "bar", "baz"], stack_utils::all_substring_strings('["foo","bar",1+"baz"]'));
    }

    public function test_maxima_parser_utils() {
        $this->assertTrue(method_exists('maxima_parser_utils', 'parse'));
        $this->assertTrue(method_exists('maxima_parser_utils', 'variable_usage_finder'));

        $ast = maxima_parser_utils::parse('x:y+sqrt(y)');
        $update = ['read' => ['z' => true]];
        $usage = maxima_parser_utils::variable_usage_finder($ast, $update);
        $this->assertEquals(['z' => true, 'y' => true], $usage['read']);
        $this->assertEquals(['x' => true], $usage['write']);
        $this->assertEquals(['sqrt' => true], $usage['calls']);
    }

    public function test_stack_ast_container() {
        $this->assertTrue(method_exists('stack_ast_container', 'get_evaluated'));
        $this->assertTrue(method_exists('stack_ast_container', 'get_evaluationform'));
    }

    public function test_stack_ast_container_silent() {
        $this->assertTrue(method_exists('stack_ast_container_silent', 'get_evaluationform'));
        $this->assertTrue(method_exists('stack_ast_container_silent', 'get_errors'));
        $this->assertTrue(method_exists('stack_ast_container_silent', 'get_inputform'));
        $this->assertTrue(method_exists('stack_ast_container_silent', 'get_list_element'));
        $this->assertTrue(method_exists('stack_ast_container_silent', 'get_valid'));
        $this->assertTrue(method_exists('stack_ast_container_silent', 'make_from_student_source'));
        $this->assertTrue(method_exists('stack_ast_container_silent', 'make_from_teacher_source'));
    }

    public function test_stack_cas_security() {
        $this->assertTrue(method_exists('stack_cas_security', 'set_allowedwords'));
        $this->assertTrue(method_exists('stack_cas_security', 'set_forbiddenwords'));
        $this->assertTrue(method_exists('stack_cas_security', 'get_units'));
    }

    public function test_stack_cas_session2() {
        $this->assertTrue(method_exists('stack_cas_session2', 'get_valid'));
        $this->assertTrue(method_exists('stack_cas_session2', 'get_errors'));
        $this->assertTrue(method_exists('stack_cas_session2', 'get_session'));
        $this->assertTrue(method_exists('stack_cas_session2', 'instantiate'));
    }

    public function test_stack_cas_keyval() {
        $this->assertTrue(method_exists('stack_cas_keyval', 'get_session'));
        $this->assertTrue(method_exists('stack_cas_keyval', 'get_variable_usage'));
    }


    public function test_stack_maths() {
        $this->assertTrue(method_exists('stack_maths', 'process_display_castext'));
    }

    public function test_stack_options() {
        $this->assertTrue(method_exists('stack_options', 'set_option'));
        $this->assertTrue(method_exists('stack_options', 'get_option'));
        $this->assertTrue(method_exists('stack_options', 'set_site_defaults'));
    }

    public function test_stack_secure_loader() {
        $this->assertTrue(method_exists('stack_secure_loader', 'get_valid'));
        $this->assertTrue(method_exists('stack_secure_loader', 'get_evaluationform'));
    }

    public function test_stack_secure_loader_value() {
        $this->assertTrue(method_exists('stack_secure_loader_value', 'get_value'));
    }

    public function test_globals() {
        $this->assertTrue(function_exists('stack_maxima_latex_tidy'));
    }
}


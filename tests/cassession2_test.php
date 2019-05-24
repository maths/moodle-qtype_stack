<?php


defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../locallib.php');
require_once(__DIR__ . '/fixtures/test_base.php');
require_once(__DIR__ . '/../stack/cas/cassession2.class.php');
require_once(__DIR__ . '/../stack/cas/ast.container.class.php');

class stack_cas_session2_test extends qtype_stack_testcase {


	public function test_get_valid() {
		$strings = array('foo', 'bar', 'sqrt(4)');

		$casstrings = array();

		foreach ($strings as $string) {
			$casstrings[] = stack_ast_container::make_from_teacher_source($string, 'test_get_valid()', new stack_cas_security());
		}

		$session = new stack_cas_session2($casstrings);

		$this->assertTrue($session->get_valid());
	}

	public function test_get_valid_false() {
		$strings = array('foo', 'bar', 'system(4)');

		$casstrings = array();

		foreach ($strings as $string) {
			$casstrings[] = stack_ast_container::make_from_teacher_source($string, 'test_get_valid_false()', new stack_cas_security());
		}

		$session = new stack_cas_session2($casstrings);

		$this->assertFalse($session->get_valid());
	}

	public function test_instantiation_and_return_values() {
		$strings = array('1+2' => '3', 
						 'sqrt(4)' => '2', 
						 'diff(x^2,x)' => '2*x');

		$casstrings = array();

		foreach ($strings as $string => $result) {
			$casstrings[] = stack_ast_container::make_from_teacher_source($string, 'test_instantiation_and_return_values()', new stack_cas_security());
		}

		$session = new stack_cas_session2($casstrings);

		$this->assertTrue($session->get_valid());
		$this->assertFalse($session->is_instantiated());
		$session->instantiate();
		$this->assertTrue($session->is_instantiated());

		$i = 0;
		foreach ($strings as $string => $result) {
			$this->assertEquals($result, $casstrings[$i]->get_evaluated()->toString());
			$i = $i + 1;
		}
	}	

	public function test_keys_or_not() {
		// Keys are optional in the new cassession, we can extract the values 
		// if need be even if keys do not exist, and if you do an assignement 
		// it wont be visible in the return values anyway.
		$strings = array('foo:1+2' => array('3', '3'), 
						 '1+2' => array('3', '3'),
						 'bar:diff(x^2,x)' => array('2*x', '2\\cdot x'),
						 'diff(x^2,x)' => array('2*x', '2\\cdot x'));
		$casstrings = array();

		foreach ($strings as $string => $result) {
			$casstrings[] = stack_ast_container::make_from_teacher_source($string, 'test_keys_or_not()', new stack_cas_security());
		}

		$session = new stack_cas_session2($casstrings);

		$this->assertTrue($session->get_valid());
		$this->assertFalse($session->is_instantiated());
		$session->instantiate();
		$this->assertTrue($session->is_instantiated());
		
		$i = 0;
		foreach ($strings as $string => $result) {
			$this->assertEquals($result[0], $casstrings[$i]->get_evaluated()->toString());
			$this->assertEquals($result[1], $casstrings[$i]->get_latex());
			$i = $i + 1;
		}
	}

	public function test_error() {
		$simpon = stack_ast_container::make_from_teacher_source('simp:true', 'test_error()', new stack_cas_security());
		$divzero = stack_ast_container::make_from_teacher_source('1/0', 'test_error()', new stack_cas_security());
		$foo = stack_ast_container::make_from_teacher_source('sconcat("f","o","o")', 'test_error()', new stack_cas_security());

		$session = new stack_cas_session2([$simpon, $divzero, $foo]);

		$this->assertTrue($session->get_valid());
		$this->assertFalse($session->is_instantiated());
		$session->instantiate();
		$this->assertTrue($session->is_instantiated());

		$this->assertEquals('', $simpon->get_errors());
		$this->assertEquals('', $foo->get_errors());
		$this->assertEquals('"foo"', $foo->get_value());
		$this->assertTrue(count($divzero->get_errors(true)) > 0);
		$this->assertContains('expt: undefined: 0 to a negative exponent.', $divzero->get_errors(true));
	}

	public function test_answernote() {
		$simpoff = stack_ast_container::make_from_teacher_source('simp:false', 'test_answernote()', new stack_cas_security());
		$validation = stack_ast_container::make_from_teacher_source('stack_validate_typeless([2/4], true, true,"~a")', 'test_answernote()', new stack_cas_security());

		$session = new stack_cas_session2([$simpoff, $validation]);
		$this->assertEquals('', $validation->get_answernote());

		$this->assertTrue($session->get_valid());
		$this->assertFalse($session->is_instantiated());
		$session->instantiate();
		$this->assertTrue($session->is_instantiated());

		$this->assertEquals('Lowest_Terms', $validation->get_answernote());
	}
}

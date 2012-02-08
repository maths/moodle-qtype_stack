<?php

require_once(dirname(__FILE__) . '/../../../locallib.php');
require_once(dirname(__FILE__) . '/../casstring.class.php');

class STACK_CAS_CasStringTest
extends UnitTestCase {

    function Get_valid($s, $st, $te) {
        $at1 = new STACK_CAS_CasString($s, 's');
        $this->assertEqual($st, $at1->Get_valid());

        $at2 = new STACK_CAS_CasString($s, 't');
        $this->assertEqual($te, $at2->Get_valid());
    }

    public function testGet_valid() {
        $cases = array(
            array('', false, false),
            array('1', true, true),
            array('a b', false, true),
            array('%pi', true, true), // Only %pi %e, %i, %gamma, %phi
            array('1+%e', true, true),
            array('e^%i*%pi', true, true),
            array('%gamma', true, true),
            array('%phi', true, true),
            array('%o1', false, false),
            array('(x+1', false, false),
            array('(y^2+1))', false, false),
            array('[sin(x)+1)', false, false),
            //array('([y^2+1)]', false, false), // TODO!
        );

        foreach ($cases as $case) {
            $this->Get_valid($case[0], $case[1], $case[2]);
        }
    }

    function Get_key($s, $key, $val) {
        $at1 = new STACK_CAS_CasString($s);
        $this->assertEqual($key, $at1->Get_key());
        $this->assertEqual($s, $at1->Get_rawCASString());    //Note the difference between the two!
        $this->assertEqual($val, $at1->Get_CASString());
    }

    public function testGet_key() {
        $cases = array(
            array('x=1', '', 'x=1'),
            array('a:1', 'a', '1'),
            array('a1:1', 'a1', '1'),
            array('f(x):=x^2', '', 'f(x):=x^2'),
            array('a:b:1', 'a', 'b:1')
        );

        foreach ($cases as $case) {
            $this->Get_key($case[0], $case[1], $case[2]);
        }
    }
}

class STACK_CAS_CasString_ExceptionTest
extends UnitTestCase {

    function Exception($a, $b, $c, $d) {
        $this->expectException();
        $at1 = new STACK_CAS_CasString($a, $b, $c, $d);
    }

    public function testException() {
        $cases = array(
            array(array(), false, false, false), 
            array("x=1", false, false, false), 
            array("x=1", 'z', false, false), 
            array("x=1", 't', 'a', false), 
            array("x=1", 't', true, 'a')
        );

        foreach ($cases as $case) {
            $this->Exception($case[0], $case[1], $case[2], $case[3]);
        }

    }
}
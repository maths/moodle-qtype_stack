<?php

require_once(dirname(__FILE__) . '/../../../locallib.php');
require_once(dirname(__FILE__) . '/../cassession.class.php');

class STACK_CAS_CasSessionTest
extends UnitTestCase {

    function Get_valid($s, $val) {
        $at1 = new STACK_CAS_CasSession($s);
        $this->assertEqual($val, $at1->Get_valid());
    }

    public function testGet_valid() {

        $a1=array('x^2', '(x+1)^2');
        $s1=array();
        foreach ($a1 as $s) {
            $s1[] = new STACK_CAS_CasString($s);
        }

        $a1=array('x^2', 'x+1)^2');
        $s2=array();
        foreach ($a1 as $s) {
            $s2[] = new STACK_CAS_CasString($s);
        }

        $cases =  array(
                array(null, true),
                array($s1, true),
                array($s2, false)
            );

        foreach ($cases as $case) {
            $this->Get_valid($case[0], $case[1]);
        }

    }

}


class STACK_CAS_CasSession_ExceptionTest 
extends UnitTestCase {
    public function Exception($a, $b, $c, $d, $e, $f) {
        $this->expectException();
        $at1 = new STACK_CAS_CasSession($a, $b, $c, $d, $e, $f);
        $at1->Get_valid();
    }

    public function testException() {
    // __construct($session, $options = null, $seed=null, $securityLevel='s', $syntax=true, $stars=false)
        $pref = new STACK_options();

        $cases =array(
            array("x=1", false, false, false, false, false),
            array(array(), null, false, false, false, false),
            array(array(1, 2, 3), null, false, false, false, false),
            array(null, 123, false, false, false, false),
            array(null, $pref, "abc", false, false, false),
            array(null, null, 123, false, false, false),
            array(null, null, 123, 'z', false, false),
            array(null, null, 123, 't', 1, false),
            array(null, null, 123, 't', false, 1),
        );

        foreach ($cases as $case) {
            $this->Exception($case[0], $case[1], $case[2], $case[3], $case[4], $case[5]);
        }

    }
}

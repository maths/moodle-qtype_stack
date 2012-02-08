<?php

require_once(dirname(__FILE__) . '/../../../locallib.php');
require_once(dirname(__FILE__) . '/../castext.class.php');

class STACK_CAS_CasTextTest
extends UnitTestCase{

    function Get_valid($s,$session,$val,$disp)  {
        $at1 = new STACK_CAS_CasText($s,$session);
        $this->assertEqual($val,$at1->Get_valid());
        $this->assertEqual($disp,$at1->Get_display_castext());
    }

    public function testGet_valid()  {

        $a1=array('a:x^2','b:(x+1)^2');
        $s1=array();
        foreach($a1 as $s) {
            $s1[] = new STACK_CAS_CasString($s);
        }
        $cs1 = new STACK_CAS_CasSession($s1);
        
        $a2=array('a:x^2)','b:(x+1)^2');
        $s2=array();
        foreach($a2 as $s) {
            $s2[] = new STACK_CAS_CasString($s);
        }
        $cs2 = new STACK_CAS_CasSession($s2);
        
        $cases = array(
                array('',NULL,true,''),  
                array('Hello world',NULL,true,'Hello world'),  
                array('$x^2$',NULL,true,'$x^2$'),
                array('@x^2@',NULL,true,'$x^2$'),
                array('\[@x^2@\]',NULL,true,'\[x^2\]'),
                array('\[@a@\]',$cs1,true,'\[x^2\]'),
                array('\[@a*b@\]',$cs1,true,'\[x^2\cdot \left(x+1\right)^2\]'),
                array('@',NULL,false,false), 
                array('@(x^2@',NULL,false,false),
                array('@1/0@',NULL,true,'$1/0$'),
                array('@x^2@',$cs2,false,false), 
        );

        foreach($cases as $case) {
            $this->Get_valid($case[0], $case[1], $case[2], $case[3]);
        }

        }

    public function checkExternalForbiddenWords($ct,$val,$words) {
        $a2=array('a:x^2)','b:(sin(x)+1)^2');
        $s2=array();
        foreach($a2 as $s) {
            $s2[] = new STACK_CAS_CasString($s);
        }
        $cs2 = new STACK_CAS_CasSession($s2);
        
        $at1 = new STACK_CAS_CasText($ct,$cs2);
        $this->assertEqual($val,$at1->checkExternalForbiddenWords($words));
        
    }

    public function testcheckExternalForbiddenWords() {
        $cases =  array(
            array('',false,array()),  
            array('$\sin(x)$',false,array()),  
            array('$\cos(x)$',false,array('cos')),  
            array('@cos(x)@',true,array('cos')),  
            array('$\cos(x)$',true,array('sin')), // sin(x) is in the session above!  
        );

        foreach($cases as $case) {
            $this->checkExternalForbiddenWords($case[0], $case[1], $case[2]);
        }
        
    }
}

class STACK_CAS_CasText_ExceptionTest 
extends UnitTestCase {    

    function Exception($a,$b,$c,$d,$e,$f) {        
        $this->expectException();
        $at1 = new STACK_CAS_CasSession($a,$b,$c,$d,$e,$f);
        $at1->Get_valid();
    }

    public function testException() {
        // __construct($rawCASText, $session=NULL, $seed=NULL, $securityLevel='s', $syntax=true, $stars=false)
        
        $session = new STACK_CAS_CasSession(NULL);

        $cases = array(
            array(array(),NULL,NULL,NULL,false,false),  
            array("Hello world",array(1),NULL,NULL,false,false),  
            array("Hello world",$session,NULL,NULL,false,false),  
            array("Hello world",$session,"abc",NULL,false,false),  
            array("Hello world",$session,123,123,false,false),  
            array("Hello world",$session,NULL,'z',false,false),  
            array("Hello world",$session,NULL,'t',1,false),  
            array("Hello world",$session,NULL,'t',false,1)  
            );

        foreach($cases as $case) {
            $this->Exception($case[0], $case[1], $case[2],$case[3],$case[4],$case[5]);
        }
        
    }
}

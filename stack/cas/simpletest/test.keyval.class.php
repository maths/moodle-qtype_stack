<?php
##																														
##																														
##																														

require_once(dirname(__FILE__) . '/../keyval.class.php');

#+																														
#+	Attempt																											 {	
#+(																														
    
    /**
     * @covers STACK_CAS_CasSession
     */
    class STACK_CAS_KeyValTest
    
    extends UnitTestCase
    
#+)		

{	#~	Code							

    #*														
    #*	test methods							{		
    #*														

    /**     
    * @dataProvider exampleCASKeyValValid    
    */
    public function testGet_valid($s,$val) {
        $at1 = new STACK_CAS_KeyVal($s);
        $this->assertEqual($val,$at1->Get_valid(),$s);
    }
	
    public function exampleCASKeyValValid() {

    $a1=array('a:x^2','b:(x+1)^2');
    $s1=array();
    foreach($a1 as $s) {
        $s1[] = new STACK_CAS_CasString($s);
    }
    $cs1 = new STACK_CAS_CasSession($s1);
    $cs1->instantiate();
    
    $a2=array('a:x^2)','b:(x+1)^2');
    $s2=array();
    foreach($a2 as $s) {
    	$s2[] = new STACK_CAS_CasString($s);
    }
    $cs2 = new STACK_CAS_CasSession($s2);
    $cs2->instantiate();
    
    $a3=array('a:1/0');
    $s3=array();
    foreach($a3 as $s) {
    	$s3[] = new STACK_CAS_CasString($s);
    }
    $cs3 = new STACK_CAS_CasSession($s3);
    $cs3->instantiate();
    
    return array(
            array('',true,NULL),  
            array("a=x^2 \n b=(x+1)^2",true,$cs1),  
            array("a:x^2 \n b:(x+1)^2",true,$cs1),  
            array("a=x^2; b=(x+1)^2",true,$cs1),  
            array('a=x^2); b=(x+1)^2',false,$cs2),  
            array('a=1/0',true,$cs3)  
        );
    }
	
    #*														
    #*												}		
    #*														
    
}

#+																														
#+																												 }		
#+	

class STACK_CAS_CasKeyVal_ExceptionTest 
extends UnitTestCase 
{    
      /**     
      * @dataProvider exceptionCASKeyVal   
      */
    public function testException($a,$b,$c,$d) {
        $this->expectException();
        $at1 = new STACK_CAS_KeyVal($a,$b,$c,$d);
    }

    public function exceptionCASKeyVal() 
    {  // __construct($raw, $securityLevel='s', $syntax=true, $stars=false)


    return array(
            array(array(),false,false,false),  
            array(1,false,false,false),  
            array('x=1',false,false,false),  
            array('x=1',NULL,false,false),  
            array('x=1','z',false,false),  
            array('x=1','t',1,false),  
            array('x=1','t',false,1),  
    );
    }
}

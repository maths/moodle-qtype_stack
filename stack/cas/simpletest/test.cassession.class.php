<?php
##																														
##																														
##																														

require_once(dirname(__FILE__) . '/../cassession.class.php');

#+																														
#+	Attempt																											 {	
#+(																														
	
	/**
	 * @covers STACK_CAS_CasSession
	 */
	class STACK_CAS_CasSessionTest
	
	extends UnitTestCase
	
#+)																														

{	#~	Code							

	#*														
	#*	test methods							{		
	#*														

    /**     
    * @dataProvider exampleCASSessionValid    
    */
    public function testGet_valid($s,$val) {
        $at1 = new STACK_CAS_CasSession($s);
        $this->assertEqual($val,$at1->Get_valid(),$s);
    }

    public function exampleCASSessionValid() 
    {

    $a1=array('x^2','(x+1)^2');
    $s1=array();
    foreach($a1 as $s) {
        $s1[] = new STACK_CAS_CasString($s);
    }
    
    $a1=array('x^2','x+1)^2');
    $s2=array();
    foreach($a1 as $s) {
        $s2[] = new STACK_CAS_CasString($s);
    }
    
    return array(
            array(NULL,true),  
            array($s1,true),  
            array($s2,false)  
    );
    
    }
	
	#*														
	#*												}		
	#*														
	
}

#+																														
#+																												 }		
#+	

class STACK_CAS_CasSession_ExceptionTest 
extends UnitTestCase 
{    
      /**     
      * @dataProvider exceptionCASSessions   
      */
    public function testException($a,$b,$c,$d,$e,$f) {        
        $this->expectException();
        $at1 = new STACK_CAS_CasSession($a,$b,$c,$d,$e,$f);
        $at1->Get_valid();
    }

    public function exceptionCASSessions() {
    // __construct($session, $options = NULL, $seed=NULL, $securityLevel='s', $syntax=true, $stars=false)
        
    $pref = new STACK_CAS_Maxima_Preferences(_::$General["DisplayMethod"]);
    
    return array(
            array("x=1",false,false,false,false,false),  
            array(array(),NULL,false,false,false,false),  
            array(array(1,2,3),NULL,false,false,false,false),  
            array(NULL,123,false,false,false,false),  
            array(NULL,$pref,"abc",false,false,false),  
            array(NULL,NULL,123,false,false,false),  
            array(NULL,NULL,123,'z',false,false),  
            array(NULL,NULL,123,'t',1,false),  
            array(NULL,NULL,123,'t',false,1),  
        );
    }
}

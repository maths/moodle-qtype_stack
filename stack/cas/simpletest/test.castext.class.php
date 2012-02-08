<?php
##																														
##																														
##																														

require_once(dirname(__FILE__) . '/../castext.class.php');

#+																														
#+	Attempt																											 {	
#+(																														
    
    /**
     * @covers STACK_CAS_CasSession
     */
    class STACK_CAS_CasTextTest
    
    extends UnitTestCase
    
#+)																														

{	#~	Code							

    #*														
    #*	test methods							{		
    #*														

    /**     
    * @dataProvider exampleCASTextValid    
    */
    public function testGet_valid($s,$session,$val,$disp) 
    {
        $at1 = new STACK_CAS_CasText($s,$session);
        $this->assertEquals($val,$at1->Get_valid(),$s);
        $this->assertEquals($disp,$at1->Get_display_castext(),$s);
    }
	
    public function exampleCASTextValid()  {

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
        
        return array(
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
    }
    /**     
    * @dataProvider examplecheckExternalForbiddenWords   
    */
    public function testcheckExternalForbiddenWords($ct,$val,$words) {
        $a2=array('a:x^2)','b:(sin(x)+1)^2');
        $s2=array();
        foreach($a2 as $s) {
            $s2[] = new STACK_CAS_CasString($s);
        }
        $cs2 = new STACK_CAS_CasSession($s2);
        
        $at1 = new STACK_CAS_CasText($ct,$cs2);
        $this->assertEquals($val,$at1->checkExternalForbiddenWords($words),$ct);
        
    }

    public function examplecheckExternalForbiddenWords() {
        return array(
            array('',false,array()),  
            array('$\sin(x)$',false,array()),  
            array('$\cos(x)$',false,array('cos')),  
            array('@cos(x)@',true,array('cos')),  
            array('$\cos(x)$',true,array('sin')), // sin(x) is in the session above!  
        );
    }
	
	#*														
	#*												}		
	#*														
	
}

#+																														
#+																												 }		
#+	

class STACK_CAS_CasText_ExceptionTest 
extends UnitTestCase 
{    
      /**     
      * @dataProvider exceptionCASText   
      */
    public function testException($a,$b,$c,$d,$e,$f) {        
        $this->expectException();
        $at1 = new STACK_CAS_CasSession($a,$b,$c,$d,$e,$f);
        $at1->Get_valid();
    }

    public function exceptionCASText() {
        // __construct($rawCASText, $session=NULL, $seed=NULL, $securityLevel='s', $syntax=true, $stars=false)
        
        $session = new STACK_CAS_CasSession(NULL);

        return array(
            array(array(),NULL,NULL,NULL,false,false),  
            array("Hello world",array(1),NULL,NULL,false,false),  
            array("Hello world",$session,NULL,NULL,false,false),  
            array("Hello world",$session,"abc",NULL,false,false),  
            array("Hello world",$session,123,123,false,false),  
            array("Hello world",$session,NULL,'z',false,false),  
            array("Hello world",$session,NULL,'t',1,false),  
            array("Hello world",$session,NULL,'t',false,1)  
        );
    }
}

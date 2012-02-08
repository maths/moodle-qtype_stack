<?php
##																														 
##																														 
##																														 

require_once(dirname(__FILE__) . '/../casstring.class.php');

#+																														 
#+	Attempt																											 {	
#+(																														 
    
    /**
     * @covers STACK_CAS_CasString
     */
    class STACK_CAS_CasStringTest
    
    extends UnitTestCase
    
#+)																														 

{	#~	Code							
    
    #*														
    #*	test methods							{		
    #*														
    /**     
    * @dataProvider exampleCASStringsValid    
    */
    public function testGet_valid($s,$st,$te) {
        $at1 = new STACK_CAS_CasString($s,'s');
        $this->assertEqual($st,$at1->Get_valid(),$s);
        
        $at2 = new STACK_CAS_CasString($s,'t');
        $this->assertEqual($te,$at2->Get_valid(),$s);
}

    public function exampleCASStringsValid() {
        return array(
            array('',false,false),  
            array('1',true,true),
            array('a b',false,true),
            array('%pi',true,true),  // Only %pi %e, %i, %gamma, %phi
            array('1+%e',true,true),
            array('e^%i*%pi',true,true),
            array('%gamma',true,true),
            array('%phi',true,true),
            array('%o1',false,false),
            array('(x+1',false,false),
            array('(y^2+1))',false,false),
            array('[sin(x)+1)',false,false),
            //array('([y^2+1)]',false,false),  // TODO!
        );
    }

    /**     
    * @dataProvider exampleCASStringsKey     
    */
    public function testGet_key($s,$key,$val) {
        $at1 = new STACK_CAS_CasString($s);
        $this->assertEqual($key,$at1->Get_key());
        $this->assertEqual($s,$at1->Get_rawCASString());    //Note the difference between the two!
        $this->assertEqual($val,$at1->Get_CASString(),$s);
    }

    public function exampleCASStringsKey()  {
        return array(
            array('x=1','','x=1'),  
            array('a:1','a','1'), 
            array('a1:1','a1','1'), 
            array('f(x):=x^2','','f(x):=x^2'), 
            array('a:b:1','a','b:1') 
        );
    }
    
    #*														
    #*												}		
    #*														
    
}

#+																														 
#+																												 }		
#+																														 

class STACK_CAS_CasString_ExceptionTest 
extends UnitTestCase {    
      /**     
      * @dataProvider exceptionCASStrings     
      */
    public function testException($a,$b,$c,$d) {        
        $this->expectException();
        $at1 = new STACK_CAS_CasString($a,$b,$c,$d);
    }

    public function exceptionCASStrings() {
        return array(
            array(array(),false,false,false),  
            array("x=1",false,false,false),  
            array("x=1",'z',false,false),  
            array("x=1",'t','a',false),  
            array("x=1",'t',true,'a')
    );
    }
}
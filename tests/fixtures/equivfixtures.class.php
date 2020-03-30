<?php
// This file is part of Stack - https://stack.maths.ed.ac.uk/demo/
//
// Stack is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Stack is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Stack.  If not, see <http://www.gnu.org/licenses/>.

defined('MOODLE_INTERNAL') || die();

/**
 * @copyright  2017 University of Edinburgh
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class stack_equiv_test_data {

    public $rawdata;

    public $answertestfixtures;

    public function __construct() {

        $samplearguments = array();

        $newarg = array();
        $newarg['section'] = 'Trivial and empty cases';
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Empty argument";
        $newarg['narrative'] = '';
        $newarg['casstring'] = "[]";
        $newarg['debuglist'] = "[EMPTYCHAR]";
        $newarg['outcome']   = true;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Single line argument";
        $newarg['narrative'] = '';
        $newarg['casstring'] = "[x^2=-1]";
        $newarg['debuglist'] = "[EMPTYCHAR]";
        $newarg['outcome']   = true;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "";
        $newarg['narrative'] = '';
        $newarg['casstring'] = "[x=x,all]";
        $newarg['debuglist'] = "[EMPTYCHAR,EQUIVCHAR]";
        $newarg['outcome']   = true;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "";
        $newarg['narrative'] = '';
        $newarg['casstring'] = "[x=x,true]";
        $newarg['debuglist'] = "[EMPTYCHAR,EQUIVCHAR]";
        $newarg['outcome']   = true;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "";
        $newarg['narrative'] = '';
        $newarg['casstring'] = "[x=x,false]";
        $newarg['debuglist'] = "[EMPTYCHAR,QMCHAR]";
        $newarg['outcome']   = false;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "";
        $newarg['narrative'] = '';
        $newarg['casstring'] = "[1=1,all]";
        $newarg['debuglist'] = "[EMPTYCHAR,EQUIVCHAR]";
        $newarg['outcome']   = true;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "";
        $newarg['narrative'] = '';
        $newarg['casstring'] = "[1=1,true]";
        $newarg['debuglist'] = "[EMPTYCHAR,EQUIVCHAR]";
        $newarg['outcome']   = true;
        $samplearguments[] = $newarg;

        // We need separate test cases for the 0=0 equation.
        $newarg = array();
        $newarg['title']     = "";
        $newarg['narrative'] = '';
        $newarg['casstring'] = "[0=0,all]";
        $newarg['debuglist'] = "[EMPTYCHAR,EQUIVCHAR]";
        $newarg['outcome']   = true;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "";
        $newarg['narrative'] = '';
        $newarg['casstring'] = "[0=0,true]";
        $newarg['debuglist'] = "[EMPTYCHAR,EQUIVCHAR]";
        $newarg['outcome']   = true;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "";
        $newarg['narrative'] = '';
        $newarg['casstring'] = "[1=2,false]";
        $newarg['debuglist'] = "[EMPTYCHAR,EQUIVCHAR]";
        $newarg['outcome']   = true;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "";
        $newarg['narrative'] = '';
        $newarg['casstring'] = "[1=2,none]";
        $newarg['debuglist'] = "[EMPTYCHAR,EQUIVCHAR]";
        $newarg['outcome']   = true;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "";
        $newarg['narrative'] = '';
        $newarg['casstring'] = "[1=2,{}]";
        $newarg['debuglist'] = "[EMPTYCHAR,EQUIVCHAR]";
        $newarg['outcome']   = true;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "";
        $newarg['narrative'] = '';
        $newarg['casstring'] = "[1=2,[]]";
        $newarg['debuglist'] = "[EMPTYCHAR,EQUIVCHAR]";
        $newarg['outcome']   = true;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "";
        $newarg['narrative'] = 'Change of variables.';
        $newarg['casstring'] = "[x=1,X=1]";
        $newarg['debuglist'] = "[EMPTYCHAR,QMCHAR]";
        $newarg['outcome']   = false;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "";
        $newarg['narrative'] = '';
        $newarg['casstring'] = "[1/(x^2+1)=1/((x+%i)*(x-%i)),true]";
        $newarg['debuglist'] = "[EMPTYCHAR,EQUIVCHAR]";
        $newarg['outcome']   = true;
        $samplearguments[] = $newarg;

        /* ....................................... */

        $newarg = array();
        $newarg['section'] = 'Numerical arguments.';
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "";
        $newarg['narrative'] = '';
        $newarg['casstring'] = "[2^2,stackeq(4)]";
        $newarg['debuglist'] = "[EMPTYCHAR,CHECKMARK]";
        $newarg['outcome']   = true;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "";
        $newarg['narrative'] = '';
        $newarg['casstring'] = "[2^2,stackeq(3)]";
        $newarg['debuglist'] = "[EMPTYCHAR,IMPLIESCHAR]";
        $newarg['outcome']   = false;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "";
        $newarg['narrative'] = '';
        $newarg['casstring'] = "[2^2,4]";
        $newarg['debuglist'] = "[EMPTYCHAR,EQUIVCHAR]";
        $newarg['outcome']   = true;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "";
        $newarg['narrative'] = '';
        $newarg['casstring'] = "[2^2,3]";
        $newarg['debuglist'] = "[EMPTYCHAR,IMPLIESCHAR]";
        $newarg['outcome']   = false;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "";
        $newarg['narrative'] = '';
        $newarg['casstring'] = "[lg(64,4),lg(4^3,4),3*lg(4,4),3]";
        $newarg['debuglist'] = "[EMPTYCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR]";
        $newarg['outcome']   = true;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "";
        $newarg['narrative'] = '';
        $newarg['casstring'] = "[lg(64,4),stackeq(lg(4^3,4)),stackeq(3*lg(4,4)),stackeq(3)]";
        $newarg['debuglist'] = "[EMPTYCHAR,CHECKMARK,CHECKMARK,CHECKMARK]";
        $newarg['outcome']   = true;
        $samplearguments[] = $newarg;

        /* ....................................... */

        $newarg = array();
        $newarg['section'] = 'Things students will get wrong.';
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "";
        $newarg['narrative'] = '';
        $newarg['casstring'] = "[x=1 or x=2,x=1 or 2]";
        $newarg['debuglist'] = "[EMPTYCHAR,MISSINGVAR]";
        $newarg['outcome']   = false;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "";
        $newarg['narrative'] = '';
        $newarg['casstring'] = "[x=1 or x=2,x=1 and x=2]";
        $newarg['debuglist'] = "[EMPTYCHAR,ANDOR]";
        $newarg['outcome']   = false;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "";
        $newarg['narrative'] = '';
        $newarg['casstring'] = "[x=1 and y=2,x=1 or y=2]";
        $newarg['debuglist'] = "[EMPTYCHAR,ANDOR]";
        $newarg['outcome']   = false;
        $samplearguments[] = $newarg;

        /* ....................................... */

        $newarg = array();
        $newarg['section'] = 'Roots, powers and absolute value';
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Squaring both sides";
        $newarg['narrative'] = 'Squaring both sides does not give equivalence.';
        $newarg['casstring'] = "[a=b,a^2=b^2]";
        $newarg['debuglist'] = "[EMPTYCHAR,IMPLIESCHAR]";
        $newarg['outcome']   = false;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Rooting both sides (1)";
        $newarg['narrative'] = 'Taking the square root of both sides does not give equivalence.';
        $newarg['casstring'] = "[a=b,sqrt(a)=sqrt(b)]";
        $newarg['debuglist'] = "[EMPTYCHAR,IMPLIEDCHAR]";
        $newarg['outcome']   = false;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Rooting both sides (2)";
        $newarg['narrative'] = 'Taking the square root of both sides does not give equivalence.';
        $newarg['casstring'] = "[a^2=b^2,a=b]";
        $newarg['debuglist'] = "[EMPTYCHAR,IMPLIEDCHAR]";
        $newarg['outcome']   = false;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Rooting both sides (2)";
        $newarg['narrative'] = 'Taking the square root of both sides does not give equivalence, we need two values.';
        $newarg['casstring'] = "[a^2=b^2,a=b or a=-b]";
        $newarg['debuglist'] = "[EMPTYCHAR,EQUIVCHAR]";
        $newarg['outcome']   = true;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Rooting both sides (+-)";
        $newarg['narrative'] = 'Taking the square root of both sides with the \(\pm\) operator.';
        $newarg['casstring'] = "[a^2=b^2,a= #pm#b,a= b or a=-b]";
        $newarg['debuglist'] = "[EMPTYCHAR,EQUIVCHAR,EQUIVCHAR]";
        $newarg['outcome']   = true;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Absolute value";
        $newarg['narrative'] = '';
        $newarg['casstring'] = "[a=b,abs(a)=abs(b),a=b]";
        $newarg['debuglist'] = "[EMPTYCHAR,IMPLIESCHAR,IMPLIEDCHAR]";
        $newarg['outcome']   = false;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Absolute value";
        $newarg['narrative'] = '';
        $newarg['casstring'] = "[abs(a)=abs(b),a=b or a=-b]";
        $newarg['debuglist'] = "[EMPTYCHAR,EQUIVCHAR]";
        $newarg['outcome']   = true;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Absolute value";
        $newarg['narrative'] = '';
        $newarg['casstring'] = "[abs(a)=abs(b),a^2=b^2]";
        $newarg['debuglist'] = "[EMPTYCHAR,EQUIVCHAR]";
        $newarg['outcome']   = true;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Odd powers";
        $newarg['narrative'] = '';
        $newarg['casstring'] = "[x^3=8,x=2]";
        $newarg['debuglist'] = "[EMPTYCHAR,IMPLIEDCHAR]";
        $newarg['outcome']   = false;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']      = "Odd powers, over the reals";
        $newarg['narrative']  = '';
        $newarg['casstring']  = "[x^3=8,x=2]";
        $newarg['debuglist']  = "[ASSUMEREALVARS,EQUIVCHARREAL]";
        $newarg['outcome']    = true;
        $newarg['assumereal'] = true;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Absolute value (Tricky)";
        $newarg['narrative'] = 'These *equations* are equivalent, but the expressions are not.  ';
        $newarg['casstring'] = "[abs(x-1/2)+abs(x+1/2)=2,abs(x)=1]";
        $newarg['debuglist'] = "[EMPTYCHAR,EQUIVCHAR]";
        $newarg['outcome']   = true;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Explicit assumptions";
        $newarg['narrative'] = '';
        $newarg['casstring'] = "[a^2=9 and a>0,a=3]";
        $newarg['debuglist'] = "[EMPTYCHAR,EQUIVCHAR]";
        $newarg['outcome']   = true;
        $samplearguments[] = $newarg;

        /* ....................................... */

        $newarg = array();
        $newarg['section'] = 'Assume positive values, to condone squaring.';
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Make g the subject";
        $newarg['narrative'] = 'In this example we need to assume all variables are positive.';
        $newarg['casstring'] = "[T=2*pi*sqrt(L/g),T^2=4*pi^2*L/g,g=4*pi^2*L/T^2]";
        $newarg['debuglist'] = "[ASSUMEPOSVARS,EQUIVCHAR,EQUIVCHAR]";
        $newarg['outcome']   = true;
        $newarg['assumepos'] = true;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Squaring both sides";
        $newarg['narrative'] = 'Assume positive values then squaring both sides does give equivalence.';
        $newarg['casstring'] = "[a=b,a^2=b^2]";
        $newarg['debuglist'] = "[ASSUMEPOSVARS,EQUIVCHAR]";
        $newarg['outcome']   = true;
        $newarg['assumepos'] = true;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Rooting both sides (1)";
        $newarg['narrative'] = 'Assume positive values then taking the square root of both sides does give equivalence.';
        $newarg['casstring'] = "[a=b,sqrt(a)=sqrt(b)]";
        $newarg['debuglist'] = "[ASSUMEPOSVARS,EQUIVCHAR]";
        $newarg['outcome']   = true;
        $newarg['assumepos'] = true;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Rooting both sides (2)";
        $newarg['narrative'] = 'Assume positive values then taking the square root of both sides does give equivalence.';
        $newarg['casstring'] = "[a^2=b^2,a=b]";
        $newarg['debuglist'] = "[ASSUMEPOSVARS,EQUIVCHAR]";
        $newarg['outcome']   = true;
        $newarg['assumepos'] = true;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Rooting both sides (2)";
        $newarg['narrative'] = 'Taking the square root of both sides does not give equivalence, we need two values.  '.
                'This is still ok when we assume positive values';
        $newarg['casstring'] = "[a^2=b^2,a=b or a=-b]";
        $newarg['debuglist'] = "[ASSUMEPOSVARS,EQUIVCHAR]";
        $newarg['outcome']   = true;
        $newarg['assumepos'] = true;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Absolute value";
        $newarg['narrative'] = '';
        $newarg['casstring'] = "[a=b,abs(a)=abs(b)]";
        $newarg['debuglist'] = "[ASSUMEPOSVARS,EQUIVCHAR]";
        $newarg['outcome']   = true;
        $newarg['assumepos'] = true;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Absolute value";
        $newarg['narrative'] = '';
        $newarg['casstring'] = "[abs(a)=abs(b),a=b]";
        $newarg['debuglist'] = "[ASSUMEPOSVARS,EQUIVCHAR]";
        $newarg['outcome']   = true;
        $newarg['assumepos'] = true;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Absolute value";
        $newarg['narrative'] = 'Who knows if a or b are positive?';
        $newarg['casstring'] = "[abs(a)=abs(b),a=-b]";
        $newarg['debuglist'] = "[ASSUMEPOSVARS,EQUIVCHAR]";
        $newarg['outcome']   = true;
        $newarg['assumepos'] = true;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Absolute value";
        $newarg['narrative'] = "Can we have it both ways?";
        $newarg['casstring'] = "[abs(a)=abs(b),a=b or a=-b]";
        $newarg['debuglist'] = "[ASSUMEPOSVARS,EQUIVCHAR]";
        $newarg['outcome']   = true;
        $newarg['assumepos'] = true;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Absolute value";
        $newarg['narrative'] = '';
        $newarg['casstring'] = "[x=abs(-2),x=2]";
        $newarg['debuglist'] = "[ASSUMEPOSVARS,EQUIVCHAR]";
        $newarg['outcome']   = true;
        $newarg['assumepos'] = true;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Absolute value";
        $newarg['narrative'] = '';
        $newarg['casstring'] = "[abs(a)=abs(b),a^2=b^2]";
        $newarg['debuglist'] = "[ASSUMEPOSVARS,EQUIVCHAR]";
        $newarg['outcome']   = true;
        $newarg['assumepos'] = true;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Solving quadratic equations (pm) and assume pos";
        $newarg['narrative'] = 'The assume_pos flay *ignores* negative solutions (if they exist), ' .
            'so this is still considered to be equivalent.';
        $newarg['casstring'] = "[x^2=9,x=#pm#3,x=3 or x=-3,x=3]";
        $newarg['debuglist'] = "[ASSUMEPOSVARS,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR]";
        $newarg['outcome']   = true;
        $newarg['assumepos'] = true;
        $samplearguments[]   = $newarg;

        $newarg = array();
        $newarg['title']     = "Solving quadratic equations with assume pos";
        $newarg['narrative'] = '';
        $newarg['casstring'] = "[x^2=9,x=3]";
        $newarg['debuglist'] = "[ASSUMEPOSVARS,EQUIVCHAR]";
        $newarg['outcome']   = true;
        $newarg['assumepos'] = true;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Solving quadratic equations (pm) and assume pos";
        $newarg['narrative'] = 'If we assume positive variables, then we just ignore the negtaive solution, ' .
            'so this is true.  If you don not want this as a final answer, ' .
            'then you have to check a separate property at the end of the argument.';
        $newarg['casstring'] = "[x^2=2,x=#pm#sqrt(2),x=sqrt(2) or x=-sqrt(2)]";
        $newarg['debuglist'] = "[ASSUMEPOSVARS,EQUIVCHAR,EQUIVCHAR]";
        $newarg['outcome']   = true;
        $newarg['assumepos'] = true;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Solving quadratic equations with assume pos";
        $newarg['narrative'] = '';
        $newarg['casstring'] = "[x^2=2,x=sqrt(2)]";
        $newarg['debuglist'] = "[ASSUMEPOSVARS,EQUIVCHAR]";
        $newarg['outcome']   = true;
        $newarg['assumepos'] = true;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Root both sides";
        $newarg['narrative'] = 'In this context, we should be able to "take the square root" of both sides.';
        $newarg['casstring'] = "[x^2 = a^2-b,x = sqrt(a^2-b)]";
        $newarg['debuglist'] = "[ASSUMEPOSVARS,EQUIVCHAR]";
        $newarg['outcome']   = true;
        $newarg['assumepos'] = true;
        $samplearguments[] = $newarg;

        /* ....................................... */

        $newarg = array();
        $newarg['section'] = 'Solving simple equations';
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Linear equation (1)";
        $newarg['narrative'] = '';
        $newarg['casstring'] = "[2*(x-3) = 4*x-3*(x+2),2*x-6=x-6,x=0]";
        $newarg['debuglist'] = "[EMPTYCHAR,EQUIVCHAR,EQUIVCHAR]";
        $newarg['outcome']   = true;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Vacuous linear equation (1)";
        $newarg['narrative'] = 'This equation is satisfied by any value of x.';
        $newarg['casstring'] = "[2*(x-3) = 5*x-3*(x+2),2*x-6=2*x-6,0=0,all]";
        $newarg['debuglist'] = "[EMPTYCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR]";
        $newarg['outcome']   = true;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Vacuous linear equation (2)";
        $newarg['narrative'] = 'This equation is satisfied by no value of x.';
        $newarg['casstring'] = "[2*(x-3) = 5*x-3*(x+1),2*x-6=2*x-3,0=3,{}]";
        $newarg['debuglist'] = "[EMPTYCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR]";
        $newarg['outcome']   = true;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Difference of two squares";
        $newarg['narrative'] = 'This argument is a basic step and should be considered true.';
        $newarg['casstring'] = "[a^2=b^2,a^2-b^2=0,(a-b)*(a+b)=0,a=b or a=-b]";
        $newarg['debuglist'] = "[EMPTYCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR]";
        $newarg['outcome']   = true;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Difference of two cubes";
        $newarg['narrative'] = 'Over the reals, this argument is true, but we have missed complex roots.';
        $newarg['casstring'] = "[a^3=b^3,a^3-b^3=0,(a-b)*(a^2+a*b+b^2)=0,(a-b)=0,a=b]";
        $newarg['debuglist'] = "[EMPTYCHAR,EQUIVCHAR,EQUIVCHAR,IMPLIEDCHAR,EQUIVCHAR]";
        $newarg['outcome']   = false;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Difference of two cubes: complex";
        $newarg['narrative'] = 'Work over the real numbers, but should not reject a correct argument over the complex numbers.';
        $newarg['casstring'] = "[a^3=b^3,a^3-b^3=0,(a-b)*(a^2+a*b+b^2)=0,(a-b)=0 or (a^2+a*b+b^2)=0, ".
                "a=b or (a+(1+%i*sqrt(3))/2*b)*(a+(1-%i*sqrt(3))/2*b)=0, ".
                "a=b or a=-(1+%i*sqrt(3))/2*b or a=-(1-%i*sqrt(3))/2*b]";
        $newarg['debuglist'] = "[EMPTYCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR]";
        $newarg['outcome']   = true;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Solving quadratic equations 1";
        $newarg['narrative'] = 'This is the basic method for solving quadratics via factoring over the reals.  It should be true.';
        $newarg['casstring'] = "[x^2-x=30,x^2-x-30=0,(x-6)*(x+5)=0,x-6=0 or x+5=0,x=6 or x=-5]";
        $newarg['debuglist'] = "[EMPTYCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR]";
        $newarg['outcome']   = true;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Solving quadratic equations 2";
        $newarg['narrative'] = '';
        $newarg['casstring'] = "[x^2=2,x^2-2=0,(x-sqrt(2))*(x+sqrt(2))=0,x=sqrt(2) or x=-sqrt(2)]";
        $newarg['debuglist'] = "[EMPTYCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR]";
        $newarg['outcome']   = true;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Solving quadratic equations 2 (pm)";
        $newarg['narrative'] = '';
        $newarg['casstring'] = "[x^2=2,x=#pm#sqrt(2),x=sqrt(2) or x=-sqrt(2)]";
        $newarg['debuglist'] = "[EMPTYCHAR,EQUIVCHAR,EQUIVCHAR]";
        $newarg['outcome']   = true;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Solving quadratic equations 3";
        $newarg['narrative'] = 'This argument avoids taking the square root of both sides by subtracting and taking the '.
                'difference of two squares.';
        $newarg['casstring'] = "[(2*x-7)^2=(x+1)^2,(2*x-7)^2 -(x+1)^2=0,(2*x-7+x+1)*(2*x-7-x-1)=0,(3*x-6)*(x-8)=0,x=2 or x=8]";
        $newarg['debuglist'] = "[EMPTYCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR]";
        $newarg['outcome']   = true;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Solving quadratic equations 4 (repeated root)";
        $newarg['narrative'] = 'This has a repeated root. There is no easy way to deal with multiplicity of roots.';
        $newarg['casstring'] = "[x^2-6*x=-9,(x-3)^2=0,x-3=0,x=3]";
        $newarg['debuglist'] = "[EMPTYCHAR,EQUIVCHAR,SAMEROOTS,EQUIVCHAR]";
        $newarg['outcome']   = true;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Solving quadratic equations 5 (missing root)";
        $newarg['narrative'] = 'This argument creates problems by taking the square root of both sides.';
        $newarg['casstring'] = "[(2*x-7)^2=(x+1)^2,sqrt((2*x-7)^2)=sqrt((x+1)^2),2*x-7=x+1,x=8]";
        $newarg['debuglist'] = "[EMPTYCHAR,EQUIVCHAR,IMPLIEDCHAR,EQUIVCHAR]";
        $newarg['outcome']   = false;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Solving quadratic equations 6 (specific with pm)";
        $newarg['narrative'] = 'Uses the \(\pm\) operator to capture both roots.';
        $newarg['casstring'] = "[x^2-10*x+9 = 0, (x-5)^2-16 = 0, (x-5)^2 =16, x-5 =#pm#4, x-5 =4 or x-5=-4, x = 1 or x = 9]";
        $newarg['debuglist'] = "[EMPTYCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR]";
        $newarg['outcome']   = true;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Solving quadratic equations 7 (general with pm)";
        $newarg['narrative'] = '';
        $newarg['casstring'] = "[x^2-2*p*x-q=0,x^2-2*p*x=q,x^2-2*p*x+p^2=q+p^2,(x-p)^2=q+p^2,x-p=#pm#sqrt(q+p^2),".
                "x-p=sqrt(q+p^2) or x-p=-sqrt(q+p^2),x=p+sqrt(q+p^2) or x=p-sqrt(q+p^2)]";
        $newarg['debuglist'] = "[EMPTYCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR]";
        $newarg['outcome']   = true;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Solving quadratic equations 8";
        $newarg['narrative'] = '';
        $newarg['casstring'] = "[x^2-10*x+7=0,(x-5)^2-18=0,(x-5)^2=sqrt(18)^2,(x-5)^2-sqrt(18)^2=0,".
                "(x-5-sqrt(18))*(x-5+sqrt(18))=0,x=5-sqrt(18) or x=5+sqrt(18)]";
        $newarg['debuglist'] = "[EMPTYCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR]";
        $newarg['outcome']   = true;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Completing the square";
        $newarg['narrative'] = 'A direct method for completing the square.';
        $newarg['casstring'] = "[x^2+2*a*x = 0, x*(x+2*a)=0, (x+a-a)*(x+a+a)=0, (x+a)^2-a^2=0]";
        $newarg['debuglist'] = "[EMPTYCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR]";
        $newarg['outcome']   = true;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Solving cubic equations 1 (missing complex roots)";
        $newarg['narrative'] = '';
        $newarg['casstring'] = "[x^3-1=0,(x-1)*(x^2+x+1)=0,x=1]";
        $newarg['debuglist'] = "[EMPTYCHAR,EQUIVCHAR,IMPLIEDCHAR]";
        $newarg['outcome']   = false;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Solving cubic equations 2 (complex roots)";
        $newarg['narrative'] = '';
        $newarg['casstring'] = "[x^3-1=0,(x-1)*(x^2+x+1)=0,x=1 or x^2+x+1=0,x=1 or x = -(sqrt(3)*%i+1)/2 or x=(sqrt(3)*%i-1)/2]";
        $newarg['debuglist'] = "[EMPTYCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR]";
        $newarg['outcome']   = true;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Solving quadratic equations 8 (Toby's method)";
        $newarg['narrative'] = 'In the last line of this argument we get a double root for a=0, which is slightly odd.';
        $newarg['casstring'] = "[a*x^2+b*x+c=0 or a=0,a^2*x^2+a*b*x+a*c=0,(a*x)^2+b*(a*x)+a*c=0, ".
                "(a*x)^2+b*(a*x)+b^2/4-b^2/4+a*c=0,(a*x+b/2)^2-b^2/4+a*c=0,(a*x+b/2)^2=b^2/4-a*c, ".
                "a*x+b/2= #pm#sqrt(b^2/4-a*c),a*x=-b/2+sqrt(b^2/4-a*c) or a*x=-b/2-sqrt(b^2/4-a*c), ".
                "(a=0 or x=(-b+sqrt(b^2-4*a*c))/(2*a)) or (a=0 or x=(-b-sqrt(b^2-4*a*c))/(2*a)), ".
                "a^2=0 or x=(-b+sqrt(b^2-4*a*c))/(2*a) or x=(-b-sqrt(b^2-4*a*c))/(2*a)]";
        $newarg['debuglist'] = "[EMPTYCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR," .
                "EQUIVCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR]";
        $newarg['outcome']   = true;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Solving cubic equations 9 (11th centuary Hindu method)";
        $newarg['narrative'] = 'This lacks auditing in the last step.';
        $newarg['casstring'] = "[a*x^2+b*x=-c,4*a^2*x^2+4*a*b*x+b^2=b^2-4*a*c,(2*a*x+b)^2=b^2-4*a*c,2*a*x+b=#pm#sqrt(b^2-4*a*c),".
                "2*a*x=-b#pm#sqrt(b^2-4*a*c),x=(-b#pm#sqrt(b^2-4*a*c))/(2*a)]";
        $newarg['debuglist'] = "[EMPTYCHAR,IMPLIESCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR,QMCHAR]";
        $newarg['outcome']   = false;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Solving cubic equations 9 (11th centuary Hindu method)";
        $newarg['narrative'] = 'With auditing, we decide this argument is correct.';
        $newarg['casstring'] = "[a*x^2+b*x=-c or a=0,4*a^2*x^2+4*a*b*x+b^2=b^2-4*a*c,(" .
                "2*a*x+b)^2=b^2-4*a*c,2*a*x+b=#pm#sqrt(b^2-4*a*c),".
                "2*a*x=-b#pm#sqrt(b^2-4*a*c),x=(-b#pm#sqrt(b^2-4*a*c))/(2*a) or a=0]";
        $newarg['debuglist'] = "[EMPTYCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR]";
        $newarg['outcome']   = true;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Solving equations with surds (erroneous argument 1)";
        $newarg['narrative'] = 'Squaring both sides of an equation leads to possible additional solutions.';
        $newarg['casstring'] = "[sqrt(3*x+4) = 2+sqrt(x+2), 3*x+4=4+4*sqrt(x+2)+(x+2),x-1=2*sqrt(x+2),".
                "x^2-2*x+1 = 4*x+8,x^2-6*x-7 = 0,(x-7)*(x+1) = 0,x=7 or x=-1]";
        $newarg['debuglist'] = "[EMPTYCHAR,IMPLIESCHAR,EQUIVCHAR,IMPLIESCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR]";
        $newarg['outcome']   = false;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Solving equations with surds (erroneous argument 1), assumepos condones this...";
        $newarg['narrative'] = 'In this situation, assumepos option means this argument is now considered correct...';
        $newarg['casstring'] = "[sqrt(3*x+4) = 2+sqrt(x+2), 3*x+4=4+4*sqrt(x+2)+(x+2),x-1=2*sqrt(x+2),".
                "x^2-2*x+1 = 4*x+8,x^2-6*x-7 = 0,(x-7)*(x+1) = 0,x=7 or x=-1,x=7]";
        $newarg['debuglist'] = "[ASSUMEPOSVARS,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR]";
        $newarg['outcome']   = true;
        $newarg['assumepos'] = true;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "";
        $newarg['narrative'] = 'Extra and missing solutions';
        $newarg['casstring'] = "[x*(x-1)*(x-2)=0,x*(x-1)=0,x*(x-1)*(x-2)=0,x*(x^2-2)=0]";
        $newarg['debuglist'] = "[EMPTYCHAR,IMPLIEDCHAR,IMPLIESCHAR,QMCHAR]";
        $newarg['outcome']   = false;
        $samplearguments[] = $newarg;

        /* ....................................... */

        $newarg = array();
        $newarg['section'] = 'Multiplicities of roots';
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Solving quadratic equations 4 (repeated root)";
        $newarg['narrative'] = 'This has a repeated root. There is no easy way to deal with multiplicity of roots.';
        $newarg['casstring'] = "[x^2-6*x=-9,x=3]";
        $newarg['debuglist'] = "[EMPTYCHAR,SAMEROOTS]";
        $newarg['outcome']   = true;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Cubic equation";
        $newarg['narrative'] = '';
        $newarg['casstring'] = "[x=1 nounor x=-2 nounor x=1,x^3-3*x=-2,x=1 nounor x=-2]";
        $newarg['debuglist'] = "[EMPTYCHAR,EQUIVCHAR,SAMEROOTS]";
        $newarg['outcome']   = true;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Rational roots";
        $newarg['narrative'] = '';
        $newarg['casstring'] = "[9*x^3-24*x^2+13*x=2,x=1/3 nounor x=2]";
        $newarg['debuglist'] = "[EMPTYCHAR,SAMEROOTS]";
        $newarg['outcome']   = true;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Larger powers";
        $newarg['narrative'] = '';
        $newarg['casstring'] = "[(x-2)^43*(x+1/3)^60=0,(3*x+1)^4*(x-2)^2=0,x=-1/3 nounor x=2]";
        $newarg['debuglist'] = "[EMPTYCHAR,SAMEROOTS,SAMEROOTS]";
        $newarg['outcome']   = true;
        $samplearguments[] = $newarg;

        /* ....................................... */

        $newarg = array();
        $newarg['section'] = 'Exponential and logarithmic equations';
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "";
        $newarg['narrative'] = '';
        $newarg['casstring'] = "[2^x=4,x*log(2)=log(4),x=log(2^2)/log(2),x=2*log(2)/log(2),x=2]";
        $newarg['debuglist'] = "[EMPTYCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR]";
        $newarg['outcome']   = true;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "";
        $newarg['narrative'] = '';
        $newarg['casstring'] = "[x^log(y),stackeq(e^(log(x)*log(y))),stackeq(e^(log(y)*log(x))),stackeq(y^log(x))]";
        $newarg['debuglist'] = "[EMPTYCHAR,CHECKMARK,CHECKMARK,CHECKMARK]";
        $newarg['outcome']   = true;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "";
        $newarg['narrative'] = 'This it needs the rule A=B <=> e^A=e^B.';
        $newarg['casstring'] = "[lg(x+17,3)-2=lg(2*x,3),lg(x+17,3)-lg(2*x,3)=2,lg((x+17)/(2*x),3)=2,(x+17)/(2*x)=3^2," .
               "(x+17)=18*x,17*x=17,x=1]";
        $newarg['debuglist'] = "[EMPTYCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVLOG,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR]";
        $newarg['outcome']   = true;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "";
        $newarg['narrative'] = 'Problematic case with nth roots.  (Needed for intrging integrals.)';
        $newarg['casstring'] = "[x=(1+y/n)^n,x^(1/n)=(1+y/n),y/n=x^(1/n)-1,y=n*(x^(1/n)-1)]";
        $newarg['debuglist'] = "[EMPTYCHAR,QMCHAR,EQUIVCHAR,EQUIVCHAR]";
        $newarg['outcome']   = 'unspported';
        $samplearguments[] = $newarg;

        /* ....................................... */

        $newarg = array();
        $newarg['section'] = 'Working over the real numbers';
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Difference of two cubes";
        $newarg['narrative'] = 'We have missed complex roots, and we don nott know what a and b are, so this is still false.';
        $newarg['casstring'] = "[a^3=b^3,a^3-b^3=0,(a-b)*(a^2+a*b+b^2)=0,(a-b)=0,a=b]";
        $newarg['debuglist'] = "[ASSUMEREALVARS,EQUIVCHAR,EQUIVCHAR,IMPLIEDCHAR,EQUIVCHAR]";
        $newarg['assumereal'] = true;
        $newarg['outcome']   = false;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Solving cubic equations 1 (missing complex roots)";
        $newarg['narrative'] = '';
        $newarg['casstring'] = "[x^3-1=0,(x-1)*(x^2+x+1)=0,x=1]";
        $newarg['debuglist'] = "[ASSUMEREALVARS,EQUIVCHAR,EQUIVCHARREAL]";
        $newarg['assumereal'] = true;
        $newarg['outcome']   = true;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Solving over the reals";
        $newarg['narrative'] = '';
        $newarg['casstring'] = "[x^4=2,x^4-2=0,(x^2-sqrt(2))*(x^2+sqrt(2))=0,x^2=sqrt(2),x=#pm# 2^(1/4)]";
        $newarg['debuglist'] = "[ASSUMEREALVARS,EQUIVCHAR,EQUIVCHAR,EQUIVCHARREAL,EQUIVCHAR]";
        $newarg['assumereal'] = true;
        $newarg['outcome']   = true;
        $samplearguments[] = $newarg;

        /* ....................................... */

        $newarg = array();
        $newarg['section'] = 'Difficult cases and nonsense arguments';
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Solving linear equations (nonsense)";
        $newarg['narrative'] = 'In this argument we "move over" one term, but end up at the right answer.'.
                'Actually, the term we "moved over" is zero, which is why we don\'t pick up the problem';
        $newarg['casstring'] = "[6*x-12=3*(x-2),6*x-12+3*(x-2)=0,9*x-18=0,x=2]";
        $newarg['debuglist'] = "[EMPTYCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR]";
        $newarg['outcome']   = true;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Solving quadratic equations (McCullum's nonsense)";
        $newarg['narrative'] = 'This argument is by Bill McCullum: matching up the coefficients.  '.
                'This argument is false in general, but in this special case leads to the correct solution.  '.
                'We can not spot this kind of thing.  Note, this argument also has a repeated root.';
        $newarg['casstring'] = "[x^2-6*x+9=0,x^2-6*x=-9,x*(x-6)=3*-3,x=3 or x-6=-3,x=3]";
        $newarg['debuglist'] = "[EMPTYCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR,SAMEROOTS]";
        $newarg['outcome']   = true;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Solving quadratic equations (Maxwell's nonsense)";
        $newarg['narrative'] = 'Maxwell pg 88.  The wrong method leads to a correct solution. ' .
                'So, this outcome is expected to be *true*!'.
                'This argument is false in general, but in this special case leads to the correct solution.  '.
                'We can not spot this kind of thing.';
        $newarg['casstring'] = "[(x+3)*(2-x)=4,x+3=4 or (2-x)=4,x=1 or x=-2]";
        $newarg['debuglist'] = "[EMPTYCHAR,EQUIVCHAR,EQUIVCHAR]";
        $newarg['outcome']   = true;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Solving quadratic equations (Maxwell's nonsense)";
        $newarg['narrative'] = 'Maxwell pg 89.  A general version of the previous argument. ' .
                'This outcome is expected to be *true*!';
        $newarg['casstring'] = "[(x-p)*(x-q)=0,x^2-p*x-q*x+p*q=0,1+q-x-p-p*q+p*x+x+q*x-x^2=1-p+q,(1+q-x)*(1-p+x)=1-p+q," .
                "(1+q-x)=1-p+q or (1-p+x)=1-p+q,x=p or x=q]";
        $newarg['debuglist'] = "[EMPTYCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR]";
        $newarg['outcome']   = true;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Classic nonsense 1.";
        $newarg['narrative'] = 'Here we create a problem by dividing by a term which is actually zero.';
        $newarg['casstring'] = "[a=b, a^2=a*b, a^2-b^2=a*b-b^2, (a-b)*(a+b)=b*(a-b), a+b=b, 2*a=a, 1=2]";
        $newarg['debuglist'] = "[EMPTYCHAR,IMPLIESCHAR,EQUIVCHAR,EQUIVCHAR,IMPLIEDCHAR,EQUIVCHAR,IMPLIEDCHAR]";
        $newarg['outcome']   = false;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Classic nonsense 1 (with auditing).";
        $newarg['narrative'] = 'Here we create a problem by dividing by a term which is actually zero.';
        $newarg['casstring'] = "[a=b or a=0, a^2=a*b, a^2-b^2=a*b-b^2, (a-b)*(a+b)=b*(a-b), ".
                "a+b=b or a-b=0, 2*a=a or a=b, 2=1 or a=0 or a=b, a=0 or a=b]";
        $newarg['debuglist'] = "[EMPTYCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR]";
        $newarg['outcome']   = true;
        $samplearguments[] = $newarg;

        /* ....................................... */

        $newarg = array();
        $newarg['section'] = 'Rational expressions';
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Rational expressions 1";
        $newarg['narrative'] = 'Cancelling factors here is fine.';
        $newarg['casstring'] = "[(x^2-4)/(x-2)=0,(x-2)*(x+2)/(x-2)=0,x+2=0,x=-2]";
        $newarg['debuglist'] = "[EMPTYCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR]";
        $newarg['outcome']   = true;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Rational expressions 2";
        $newarg['narrative'] = 'We should have cancelled the roots. '.
                'As a result of the failure to do this we have an extra root of this equation.';
        $newarg['casstring'] = "[(x^2-4)/(x-2)=0,(x^2-4)=0,(x-2)*(x+2)=0,x=-2 or x=2]";
        $newarg['debuglist'] = "[EMPTYCHAR,IMPLIESCHAR,EQUIVCHAR,EQUIVCHAR]";
        $newarg['outcome']   = false;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Rational expressions 3";
        $newarg['narrative'] = '';
        $newarg['casstring'] = "[5*x/(2*x+1)-3/(x+1) = 1,5*x*(x+1)-3*(2*x+1)=(x+1)*(2*x+1),5*x^2+5*x-6*x-3=2*x^2+3*x+1,".
            "3*x^2-4*x-4=0,(x-2)*(3*x+2)=0,x=2 or x=-2/3]";
        $newarg['debuglist'] = "[EMPTYCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR]";
        $newarg['outcome']   = true;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Solving rational expressions (erroneous argument 1)";
        $newarg['narrative'] = 'Here we create a problem by dividing by a term which is actually zero.  '.
                'The only solution is \(x=10\) which we have cancelled out.';
        $newarg['casstring'] = "[(x+10)/(x-6)-5= (4*x-40)/(13-x),(x+10-5*(x-6))/(x-6)= (4*x-40)/(13-x), ".
                "(4*x-40)/(6-x)= (4*x-40)/(13-x),6-x= 13-x,6= 13]";
        $newarg['debuglist'] = "[EMPTYCHAR,EQUIVCHAR,EQUIVCHAR,QMCHAR,EQUIVCHAR]";
        $newarg['outcome']   = false;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Solving rational expressions (erroneous argument 2)";
        $newarg['narrative'] = 'This is similar to the previous argument.';
        $newarg['casstring'] = "[(x+5)/(x-7)-5= (4*x-40)/(13-x),(x+5-5*(x-7))/(x-7)= (4*x-40)/(13-x), ".
                "(4*x-40)/(7-x)= (4*x-40)/(13-x),7-x= 13-x,7= 13]";
        $newarg['debuglist'] = "[EMPTYCHAR,EQUIVCHAR,EQUIVCHAR,IMPLIEDCHAR,EQUIVCHAR]";
        $newarg['outcome']   = false;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Solving rational expressions (correct argument)";
        $newarg['narrative'] = 'Here we create a problem by dividing by a term which is actually zero.  '.
                'The only solution is \(x=10\) which we have cancelled out.';
        $newarg['casstring'] = "[(x+5)/(x-7)-5= (4*x-40)/(13-x),(x+5-5*(x-7))/(x-7)= (4*x-40)/(13-x), ".
                "(4*x-40)/(7-x)= (4*x-40)/(13-x),7-x= 13-x or 4*x-40=0,7= 13 or 4*x=40,x=10]";
        $newarg['debuglist'] = "[EMPTYCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR]";
        $newarg['outcome']   = true;
        $samplearguments[] = $newarg;

        /* ....................................... */

        $newarg = array();
        $newarg['section'] = 'Equate coefficients';
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Simple equate coeffs";
        $newarg['narrative'] = '';
        $newarg['casstring'] = "[a*x^2+b*x+c=0,a=0 nounand b=0 nounand c=0,a*x^2+b*x+c=0]";
        $newarg['debuglist'] = "[EMPTYCHAR,EQUATECOEFFLOSS(x),EQUATECOEFFGAIN(x)]";
        $newarg['outcome']   = true;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Equate coeffs";
        $newarg['narrative'] = '';
        $newarg['casstring'] = "[a*x^2+b*x+c=A*x^2+B*x+C,a=A nounand b=B nounand c=C,a*x^2+b*x+c=A*x^2+B*x+C]";
        $newarg['debuglist'] = "[EMPTYCHAR,EQUATECOEFFLOSS(x),EQUATECOEFFGAIN(x)]";
        $newarg['outcome']   = true;
        $samplearguments[] = $newarg;

        /* ....................................... */

        $newarg = array();
        $newarg['section'] = 'Equational reasoning';
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Expand out the brackets (1)";
        $newarg['narrative'] = '';
        $newarg['casstring'] = "[(x-1)*(x+4), stackeq(x^2-x+4*x-4),stackeq(x^2+3*x-4)]";
        $newarg['debuglist'] = "[EMPTYCHAR,CHECKMARK,CHECKMARK]";
        $newarg['outcome']   = true;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Expand out the brackets (1)";
        $newarg['narrative'] = '';
        $newarg['casstring'] = "[(x-1)*(x+4), stackeq(x^2-x+4*x-4),stackeq(x^2+3*x-4)]";
        $newarg['debuglist'] = "[EMPTYCHAR,CHECKMARK,CHECKMARK]";
        $newarg['outcome']   = true;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Difference of two square 1";
        $newarg['narrative'] = '';
        $newarg['casstring'] = "[x^2-2,stackeq((x-sqrt(2))*(x+sqrt(2)))]";
        $newarg['debuglist'] = "[EMPTYCHAR,CHECKMARK]";
        $newarg['outcome']   = true;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Difference of two square 2";
        $newarg['narrative'] = '';
        $newarg['casstring'] = "[x^2+4,stackeq((x-2*i)*(x+2*i))]";
        $newarg['debuglist'] = "[EMPTYCHAR,CHECKMARK]";
        $newarg['outcome']   = true;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Completing the square (1)";
        $newarg['narrative'] = 'A simple matter of completing the square.';
        $newarg['casstring'] = "[x^2+2*a*x,x^2+2*a*x+a^2-a^2,(x+a)^2-a^2]";
        $newarg['debuglist'] = "[EMPTYCHAR,EQUIVCHAR,EQUIVCHAR]";
        $newarg['outcome']   = true;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Completing the square (2)";
        $newarg['narrative'] = 'Using "stackeq" as a prefix equation symbol.';
        $newarg['casstring'] = "[x^2+2*a*x,stackeq(x^2+2*a*x+a^2-a^2),stackeq((x+a)^2-a^2)]";
        $newarg['debuglist'] = "[EMPTYCHAR,CHECKMARK,CHECKMARK]";
        $newarg['outcome']   = true;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Adding fractions";
        $newarg['narrative'] = 'This contains an edge case of zero only in a line.';
        $newarg['casstring'] = "[(y-z)/(y*z)+(z-x)/(z*x)+(x-y)/(x*y),(x*(y-z)+y*(z-x)+z*(x-y))/(x*y*z),0]";
        $newarg['debuglist'] = "[EMPTYCHAR,EQUIVCHAR,EQUIVCHAR]";
        $newarg['outcome']   = true;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Adding fractions";
        $newarg['narrative'] = 'This contains an edge case of zero only in a line.';
        $newarg['casstring'] = "[(y-z)/(y*z)+(z-x)/(z*x)+(x-y)/(x*y),stackeq((x*(y-z)+y*(z-x)+z*(x-y))/(x*y*z)),stackeq(0)]";
        $newarg['debuglist'] = "[EMPTYCHAR,CHECKMARK,CHECKMARK]";
        $newarg['outcome']   = true;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Reasoning with many variables";
        $newarg['narrative'] = 'This example comes from proving Heron\'s formula. '.
                'See http://math.stackexchange.com/questions/255527/show-that-2a2-b2a2c2b2c2-a4b4c4-abc-abca-bcab';
        $newarg['casstring'] = "[2*(a^2*b^2+b^2*c^2+c^2*a^2)-(a^4+b^4+c^4),".
                "stackeq(4*a^2*b^2-(a^4+b^4+c^4+2*a^2*b^2-2*b^2*c^2-2*c^2*a^2)),".
                "stackeq((2*a*b)^2-(b^2+a^2-c^2)^2,(2*a*b+b^2+a^2-c^2)*(2*a*b-b^2-a^2+c^2)),".
                "stackeq(((a+b)^2-c^2)*(c^2-(a-b)^2)),stackeq((a+b+c)*(a+b-c)*(c+a-b)*(c-a+b))]";
        $newarg['debuglist'] = "[EMPTYCHAR,CHECKMARK,CHECKMARK,CHECKMARK,CHECKMARK]";
        $newarg['outcome']   = true;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Non-trivial difference between equations and expressions";
        $newarg['narrative'] = 'This contains an edge case of zero only in a line.';
        $newarg['casstring'] = "[abs(x-1/2)+abs(x+1/2)-2,stackeq(abs(x)-1)]";
        $newarg['debuglist'] = "[EMPTYCHAR,QMCHAR]";
        $newarg['outcome']   = false;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "";
        $newarg['narrative'] = 'Squaring both sides introduces an extra root into an interesting example.';
        $newarg['casstring'] = "[11*sqrt(abs(x)+1)=25-x,11^2*(abs(x)+1)=(25-x)^2," .
            "11^2*abs(x)=(25-x)^2-11^2,11^4*x^2=((25-x)^2-11^2)^2, " .
            "((25-x)^2-11^2)^2-11^4*x^2=0,((25-x)^2-11^2-11^2*x)*((25-x)^2-11^2+11^2*x)=0," .
            "(x^2-50*x+504-121*x)*(x^2-50*x+504+121*x)=0, " .
            "(x-168)*(x-3)*(x+8)*(x+63)=0]";
        $newarg['debuglist'] = "[EMPTYCHAR,QMCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR]";
        $newarg['outcome']   = false;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Non-trivial partial fractions";
        $newarg['narrative'] = '';
        $newarg['casstring'] = "[1/(x^2+1)=1/((x+%i)*(x-%i)), stackeq(1/(2*%i)*(1/(x-%i)-1/(x+%i)))]";
        $newarg['debuglist'] = "[CHECKMARK,CHECKMARK]";
        $newarg['outcome']   = true;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "The Van Schooten Example";
        $newarg['narrative'] = 'This example is based on the Van Schooten Example, which has appeared in many algebra ' .
            'books since Van Schooten\'s Principia Mathesos Universalis.  See Heller 1940.';
        $newarg['casstring'] = "[((a-b)/(a^2+a*b))/((a^2-2*a*b+b^2)/(a^4-b^4))," .
            "stackeq(((a-b)*(a-b)*(a+b)*(a^2+b^2))/(a*(a+b)*(a-b)^2)),stackeq((a^2+b^2)/a),stackeq(a+b^2/a)]";
        $newarg['debuglist'] = "[EMPTYCHAR,CHECKMARK,CHECKMARK,CHECKMARK]";
        $newarg['outcome']   = true;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Induction step";
        $newarg['narrative'] = '';
        $newarg['casstring'] = "[sum(k,k,1,n+1),stackeq(sum(k,k,1,n)+(n+1)),stackeq(n*(n+1)/2 +n+1),".
                "stackeq((n+1)*(n+1+1)/2),stackeq((n+1)*(n+2)/2)]";
        $newarg['debuglist'] = "[EMPTYCHAR,CHECKMARK,CHECKMARK,CHECKMARK,CHECKMARK]";
        $newarg['outcome']   = true;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Likelihood";
        $newarg['narrative'] = '';
        $newarg['casstring'] = "[log((a-1)^n*product(x_i^(-a),i,1,n)),stackeq(n*log(a-1)-a*sum(log(x_i),i,1,n))]";
        $newarg['debuglist'] = "[EMPTYCHAR,CHECKMARK]";
        $newarg['outcome']   = true;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Recurrance relation in binomial coefficients.";
        $newarg['narrative'] = '';
        $newarg['casstring'] = '[binomial(n,k)+binomial(n,k+1),stackeq(n!/(k!*(n-k)!)+n!/((k+1)!*(n-k-1)!)),' .
                'stackeq(n!/(k!*(n-k)*(n-k-1)!)+n!/((k+1)!*(n-k-1)!)),stackeq(n!/(k!*(n-k-1)!)*(1/(n-k)+1/(k+1))),' .
                'stackeq(n!/(k!*(n-k-1)!)*((n+1)/((n-k)*(k+1)))),stackeq((n+1)*n!/(k!*(n-k-1)!)*(1/((k+1)*(n-k)))),' .
                'stackeq((n+1)*n!/((k+1)*k!*(n-k)*(n-k-1)!)),stackeq(((n+1)!/((k+1)!)*(1/((n-k)*(n-k-1)!)))),' .
                'stackeq((n+1)!/((k+1)!*(n-k)!)),stackeq(binomial(n+1,k+1))]';
        $newarg['debuglist'] = '[EMPTYCHAR,CHECKMARK,CHECKMARK,CHECKMARK,CHECKMARK,CHECKMARK,CHECKMARK,CHECKMARK,' .
                'CHECKMARK,CHECKMARK]';
        $newarg['outcome']   = true;
        $samplearguments[]   = $newarg;

        /* ....................................... */

        $newarg = array();
        $newarg['section'] = 'Mix of equations and expressions';
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Correct";
        $newarg['narrative'] = '';
        $newarg['casstring'] = "[(x-1)^2=(x-1)*(x-1), stackeq(x^2-2*x+1)]";
        $newarg['debuglist'] = "[CHECKMARK,CHECKMARK]";
        $newarg['outcome']   = true;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Correct 1st line, incorrect next step";
        $newarg['narrative'] = '';
        $newarg['casstring'] = "[(x-1)^2=(x-1)*(x-1), stackeq(x^2-2*x+2)]";
        $newarg['debuglist'] = "[CHECKMARK,QMCHAR]";
        $newarg['outcome']   = false;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Incorrect 1st line, incorrect next step";
        $newarg['narrative'] = '';
        $newarg['casstring'] = "[(x-2)^2=(x-1)*(x-1), stackeq(x^2-2*x+1)]";
        $newarg['debuglist'] = "[QMCHAR,CHECKMARK]";
        $newarg['outcome']   = false;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Numerical example";
        $newarg['narrative'] = 'From an induction proof that 3 divides 4^(n+1)-1.';
        $newarg['casstring'] = "[4^((n+1)+1)-1= 4*4^(n+1)-1,stackeq(4*(4^(n+1)-1)+3)]";
        $newarg['debuglist'] = "[CHECKMARK,CHECKMARK]";
        $newarg['outcome']   = true;
        $samplearguments[] = $newarg;

        /* ....................................... */

        $newarg = array();
        $newarg['section'] = 'Simultaneous equations and substitution';
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Linear simultaneous equations";
        $newarg['narrative'] = 'With sumultaneous equations students must use "and" to join them.';
        $newarg['casstring'] = "[2*x+3*y=6 and 4*x+9*y=15,2*x+3*y=6 and -2*x=-3,".
            "3+3*y=6 and 2*x=3,y=1 and x=3/2]";
        $newarg['debuglist'] = "[EMPTYCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR]";
        $newarg['outcome']   = true;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Linear simultaneous equations";
        $newarg['narrative'] = 'With sumultaneous equations students must use "and" to join them.';
        $newarg['casstring'] = "[2*x+3*y=6 and 4*x+9*y=15,2*x+3*y=6 and -2*x=-3,".
                "3+3*y=6 and 2*x=3,y=1 and x=3]";
        $newarg['debuglist'] = "[EMPTYCHAR,EQUIVCHAR,EQUIVCHAR,QMCHAR]";
        $newarg['outcome']   = false;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Simultaneous equations";
        $newarg['narrative'] = '';
        $newarg['casstring'] = "[x^2+y^2=8 and x=y, 2*x^2=8 and y=x, x^2=4 and y=x, x= #pm#2 and y=x, ".
                "(x= 2 and y=x) or (x=-2 and y=x), (x=2 and y=2) or (x=-2 and y=-2)]";
        $newarg['debuglist'] = "[EMPTYCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR]";
        $newarg['outcome']   = true;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Simultaneous equations (without using square roots or substitution)";
        $newarg['narrative'] = '';
        $newarg['casstring'] = "[x^2+y^2=5 and x*y=2, x^2+y^2-5=0 and x*y-2=0, x^2-2*x*y+y^2-1=0 and x^2+2*x*y+y^2-9=0, ".
                "(x-y)^2-1=0 and (x+y)^2-3^2=0, ".
                "(x-y=1 and x+y=3) or (x-y=-1 and x+y=3) or (x-y=1 and x+y=-3) or (x-y=-1 and x+y=-3), ".
                "(x=1 and y=2) or (x=2 and y=1) or (x=-2 and y=-1) or (x=-1 and y=-2)]";
        $newarg['debuglist'] = "[EMPTYCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR]";
        $newarg['outcome']   = true;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Simultaneous equations (without using square roots or substitution) 2";
        $newarg['narrative'] = '';
        $newarg['casstring'] = "[4*x^2+7*x*y+4*y^2=4 and y=x-4, 4*x^2+7*x*(x-4)+4*(x-4)^2-4=0 and y=x-4, ".
                "15*x^2-60*x+60=0 and y=x-4, (x-2)^2=0 and y=x-4, x=2 and y=x-4, x=2 and y=-2]";
        $newarg['debuglist'] = "[EMPTYCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR]";
        $newarg['outcome']   = true;
        $samplearguments[]   = $newarg;

        $newarg = array();
        $newarg['title']     = "Substitution";
        $newarg['narrative'] = '';
        $newarg['casstring'] = "[a^2=b and a^2=1, b=a^2 and (a=1 or a=-1), (b=1 and a=1) or (b=1 and a=-1)]";
        $newarg['debuglist'] = "[EMPTYCHAR,EQUIVCHAR,EQUIVCHAR]";
        $newarg['outcome']   = true;
        $samplearguments[]   = $newarg;

        $newarg = array();
        $newarg['title']     = "Substitution";
        $newarg['narrative'] = '';
        $newarg['casstring'] = "[a^2=b and x=1, b=a^2 and x=1]";
        $newarg['debuglist'] = "[EMPTYCHAR,EQUIVCHAR]";
        $newarg['outcome']   = true;
        $samplearguments[]   = $newarg;

        $newarg = array();
        $newarg['title']      = "Find two distinct numbers which are the square of each other (needs substitution, not equals)";
        $newarg['narrative']  = '';
        $newarg['casstring']  = "[a^2=b and b^2=a, b=a^2 and a^4=a, b=a^2 and a^4-a=0, b=a^2 and a*(a-1)*(a^2+a+1)=0, ".
                "b=a^2 and (a=0 or a=1 or a^2+a+1=0), (b=0 and a=0) or (b=1 and a=1)]";
        $newarg['debuglist']  = "[ASSUMEREALVARS,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR]";
        $newarg['outcome']    = true;
        $newarg['assumereal'] = true;
        $samplearguments[]    = $newarg;

        $newarg = array();
        $newarg['title']      = "Substitute in a value for a variable and keep working.";
        $newarg['narrative']  = '';
        $newarg['casstring']  = "[2*x^3-9*x^2+10*x-3,stacklet(x,1),2*1^3-9*1^2+10*1-3,stackeq(0),\"So\"," .
                "2*x^3-9*x^2+10*x-3,stackeq((x-1)*(2*x^2-7*x+3)),stackeq((x-1)*(2*x-1)*(x-3))]";
        $newarg['debuglist']  = "[EMPTYCHAR,EMPTYCHAR,EQUIVCHAR,CHECKMARK,EMPTYCHAR,EMPTYCHAR,CHECKMARK,CHECKMARK]";
        $newarg['outcome']    = 'unknown';
        $samplearguments[]    = $newarg;

        $newarg = array();
        $newarg['title']      = "Cardano's solution of the cubic equation";
        $newarg['narrative']  = 'Not the method in Cardano\'s work, but a better approach using complex numbers.';
        $newarg['casstring']  = "[(x+a+b)*(x+w*a+w^2*b)*(x+w^2*a+w*b)=x^3-3*p*x+2*q, stacklet(w,(-1+i*sqrt(3))/2)," .
                "x^3-3*a*b*x+(a^3+b^3)=x^3-3*p*x+2*q,3*a*b=3*p and a^3+b^3=2*q,a^3*b^3=p^3 and a^3+b^3=2*q," .
                "a^3*b^3=p^3 and (a^3)^2+b^3=2*q*a^3,stacklet(a^3,u),u*b^3=p^3 and u^2-2*q*u+b^3=0," .
                "u*b^3=p^3 and (u-q)^2-q^2+b^3=0,u*b^3=p^3 and u = q#pm#sqrt(q^2-b^3),stacklet(u,a^3)," .
                "a^3*b^3=p^3 and a^3 = q#pm#sqrt(q^2-b^3),b^3= q-(#pm#sqrt(q^2-b^3)) and a^3 = q#pm#sqrt(q^2-b^3)," .
                '"And so",' .
                "x= (q-sqrt(q^2-b^3))^(1/3)+(q+sqrt(q^2-b^3))^(1/3)]";
        $newarg['debuglist']  = "[EMPTYCHAR,EMPTYCHAR,EQUIVCHAR,EQUATECOEFFLOSS(x),EQUIVCHAR,EQUIVCHAR,EMPTYCHAR,EQUIVCHAR," .
                "EQUIVCHAR,EQUIVCHAR,EMPTYCHAR,EQUIVCHAR,EQUIVCHAR,EMPTYCHAR,EMPTYCHAR]";
        $newarg['outcome']   = 'unsupported';
        $samplearguments[]    = $newarg;

        /* ....................................... */

        $newarg = array();
        $newarg['section'] = 'Inequalities';
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Solving a quadratic inequality";
        $newarg['narrative'] = 'Solving quadratic inequalities using reasoning by equivalence.';
        $newarg['casstring'] = "[2*x^2+x>=6, 2*x^2+x-6>=0, (2*x-3)*(x+2)>= 0,".
                "((2*x-3)>=0 and (x+2)>=0) or ((2*x-3)<=0 and (x+2)<=0), ".
                "(x>=3/2 and x>=-2) or (x<=3/2 and x<=-2), x>=3/2 or x <=-2]";
        $newarg['debuglist'] = "[EMPTYCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR]";
        $newarg['outcome']   = true;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Solving a quadratic inequality";
        $newarg['narrative'] = 'Solving quadratic inequalities using reasoning by equivalence.';
        $newarg['casstring'] = "[2*x^2+x>=6, 2*x^2+x-6>=0, (2*x-3)*(x+2)>= 0,".
                "((2*x-3)>=0 and (x+2)>=0) or ((2*x-3)<=0 and (x+2)<=0), ".
                "(x>=3/2 and x>=-2) or (x<=3/2 and x<=-2), x>=3/2 or x <=-2]";
        $newarg['debuglist'] = "[EMPTYCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR]";
        $newarg['outcome']   = true;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Solving a quadratic inequality";
        $newarg['narrative'] = 'Failing to solving quadratic inequalities';
        $newarg['casstring'] = "[2*x^2+x>=6, 2*x^2+x-6>=0, (2*x-3)*(x+2)>= 0,".
                "((2*x-3)>=0 and (x+2)>=0) or ((2*x-3)<=0 and (x+2)<=0), ".
                "(x>=3/2 and x>=-2) or (x<=3/2 and x<=-2), x>=3/2 or x <=2]";
        $newarg['debuglist'] = "[EMPTYCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR,QMCHAR]";
        $newarg['outcome']   = false;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Solving an inequality (remove redundant inequalities)";
        $newarg['narrative'] = '';
        $newarg['casstring'] = "[x^2>=9 and x>3, x^2-9>=0 and x>3, (x>=3 or x<=-3) and x>3, x>3]";
        $newarg['debuglist'] = "[EMPTYCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR]";
        $newarg['outcome']   = true;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Find the values of a which satisfy this inequality for all x.";
        $newarg['narrative'] = 'This argument contains a comment and hence cannot be automatically assessed.  '.
                'Each internal bit is fine, but the logic uses a universal quantifier which we cannot assess. '.
                'This argument will need semi-automatic marking.';
        $newarg['casstring'] = "[-x^2+a*x+a-3<0, a-3<x^2-a*x, a-3<(x-a/2)^2-a^2/4, a^2/4+a-3<(x-a/2)^2, a^2+4*a-12<4*(x-a/2)^2, ".
                "(a-2)*(a+6)<4*(x-a/2)^2, \"This inequality is required to be true for all x.\", \"So it must be true " .
                "when the right hand side takes its minimum value.\", \"This happens for x=a/2.\", ".
                "(a-2)*(a+6)<0, ((a-2)<0 and (a+6)>0) or ((a-2)>0 and (a+6)<0), (a<2 and a>-6) or (a>2 and a<-6), ".
                "(-6<a and a<2) or false, (-6<a and a<2)]";
        $newarg['debuglist'] = "[EMPTYCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR,EMPTYCHAR,EMPTYCHAR,EMPTYCHAR,".
                "EMPTYCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR]";
        $newarg['outcome']   = 'unknown';
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Rectangle question from NH_2016_8";
        $newarg['narrative'] = '';
        $newarg['casstring'] = "[x-2>0 and x*(x-2)<15,x>2 and x^2-2*x-15<0,x>2 and (x-5)*(x+3)<0,".
                "x>2 and ((x<5 and x>-3) or (x>5 and x<-3)),x>2 and (x<5 and x>-3),x>2 and x<5]";
        $newarg['debuglist'] = "[EMPTYCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR]";
        $newarg['outcome']   = true;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Rectangle question from NH_2016_8 (slip)";
        $newarg['narrative'] = 'With a wrong line.';
        $newarg['casstring'] = "[x-2>0 and x*(x-2)<15,x>2 and x^2-2*x-15<0,x>2 and (x-5)*(x+3)<0,".
                "x>2 and ((x<5 and x>-3) or (x>5 and x<-3)),x>7 and (x<5 and x>-3),x>2 and x<5]";
        $newarg['debuglist'] = "[EMPTYCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR,QMCHAR,QMCHAR]";
        $newarg['outcome']   = false;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Semi-automatic marking of a multi-stage problem.";
        $newarg['narrative'] = "Taken from Finnish national exam, Q9 of 22 March 1971." .
            "(http://matemaattinenyhdistys.fi/yo/?download=1970-1998.pdf)" .
            "This question illustrates the practical steps needed in problem solving at this level.\n\n".
            "Find the minimum number of the positive integer \(a\), ".
            "for which the equation \(x^2 + (a-2)x + a = 0\) has real roots.";
        $newarg['casstring'] = '[x^2 + (a-2)*x + a = 0,(x + (a-2)/2)^2 -((a-2)/2)^2 + a = 0,(x + (a-2)/2)^2 =(a-2)^2/4 - a,'.
                '"This has real roots iff",(a-2)^2/4-a >=0,a^2-4*a+4-4*a >=0,a^2-8*a+4>=0,(a-4)^2-16+4>=0,'.
                '(a-4)^2>=12,a-4>=sqrt(12) or a-4<= -sqrt(12),"Ignoring the negative solution.",'.
                'a>=sqrt(12)+4,"Using external domain information that a is an integer.",a>=8]';
        $newarg['debuglist'] = "[EMPTYCHAR,EQUIVCHAR,EQUIVCHAR,EMPTYCHAR,EMPTYCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR,".
                "EQUIVCHAR,EMPTYCHAR,EMPTYCHAR,EMPTYCHAR,EMPTYCHAR]";
        $newarg['outcome']   = false;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Solving inequalities with the absolute value function";
        $newarg['narrative'] = '';
        $newarg['casstring'] = "[2*x/abs(x-1)<1, 2*x<abs(x-1),".
                "(x>=1 and 2*x<x-1) or (x<1 and 2*x<-x+1),(x>=1 and x<-1) or (x<1 and 3*x<1),x<1/3]";
        $newarg['debuglist'] = "[EMPTYCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR]";
        $newarg['outcome']   = 'unsupported';
        $samplearguments[] = $newarg;

        /* ....................................... */

        $newarg = array();
        $newarg['section'] = 'Induction steps';
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Proof by induction";
        $newarg['narrative'] = "";
        $newarg['casstring'] = '["Set P(n) be the statement that",sum(k^2,k,1,n) = n*(n+1)*(2*n+1)/6, '.
                '"Then P(1) is the statement", 1^2 = 1*(1+1)*(2*1+1)/6, 1 = 1, '.
                '"So P(1) holds.  Now assume P(n) is true.",sum(k^2,k,1,n) = n*(n+1)*(2*n+1)/6,'.
                'sum(k^2,k,1,n) +(n+1)^2= n*(n+1)*(2*n+1)/6 +(n+1)^2,sum(k^2,k,1,n+1)= (n+1)*(n*(2*n+1) +6*(n+1))/6,'.
                'sum(k^2,k,1,n+1)= (n+1)*(2*n^2+7*n+6)/6,sum(k^2,k,1,n+1)= (n+1)*(n+1+1)*(2*(n+1)+1)/6]';
        $newarg['debuglist'] = "[EMPTYCHAR,EMPTYCHAR,EMPTYCHAR,EMPTYCHAR,EQUIVCHAR,EMPTYCHAR,EMPTYCHAR," .
                "EQUIVCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR]";
        $newarg['outcome']   = 'unknown';
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Proof by induction (2)";
        $newarg['narrative'] = "";
        $newarg['casstring'] = '[(n+1)^2+sum(k^2,k,1,n) = (n+1)^2+(n*(n+1)*(2*n+1))/6, '.
                'sum(k^2,k,1,n+1) = ((n+1)*(n*(2*n+1)+6*(n+1)))/6, sum(k^2,k,1,n+1) = ((n+1)*(2*n^2+7*n+6))/6, '.
                'sum(k^2,k,1,n+1) = ((n+1)*(n+2)*(2*(n+1)+1))/6]';
        $newarg['debuglist'] = "[EMPTYCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR]";
        $newarg['outcome']   = true;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Definition chasing proof";
        $newarg['narrative'] = "";
        $newarg['casstring'] = '[conjugate(a)*conjugate(b),stacklet(a,x+i*y),stacklet(b,r+i*s),' .
            'stackeq(conjugate(x+i*y)*conjugate(r+i*s)),'.
            'stackeq((x-i*y)*(r-i*s)),stackeq((x*r-y*s)-i*(y*r+x*s)),stackeq(conjugate((x*r-y*s)+i*(y*r+x*s))),' .
            'stackeq(conjugate((x+i*y)*(r+i*s))),'.
            'stacklet(x+i*y,a),stacklet(r+i*s,b),stackeq(conjugate(a*b))]';
        $newarg['debuglist'] = "[EMPTYCHAR,EMPTYCHAR,EMPTYCHAR,CHECKMARK,CHECKMARK,CHECKMARK,CHECKMARK,CHECKMARK," .
            "EMPTYCHAR,EMPTYCHAR,CHECKMARK]";
        $newarg['outcome']   = true;
        $samplearguments[] = $newarg;

        /* ....................................... */

        $newarg = array();
        $newarg['section'] = 'Limits';
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Indefinite integration";
        $newarg['narrative'] = "";
        $newarg['casstring'] = '[nounint(x*e^x,x,-inf,0),nounlimit(nounint(x*e^x,x,t,0),t,-inf),'.
            'nounlimit(e^t-t*e^t-1,t,-inf),nounlimit(e^t,t,-inf)+nounlimit(-t*e^t,t,-inf)+nounlimit(-1,t,-inf),-1]';
        $newarg['debuglist'] = "[EMPTYCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR]";
        $newarg['outcome']   = true;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Calculus from first principles";
        $newarg['narrative'] = "";
        $newarg['casstring'] = '[noundiff(x^2,x),stackeq(nounlimit(((x+h)^2-x^2)/h,h,0)),'.
            'stackeq(nounlimit(2*x+h,h,0)),stackeq(2*x)]';
        $newarg['debuglist'] = "[EMPTYCHAR,CHECKMARK,CHECKMARK,CHECKMARK]";
        $newarg['outcome']   = true;
        $samplearguments[] = $newarg;

        /* ....................................... */

        $newarg = array();
        $newarg['section'] = 'Calculus';
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Differential quotient as the unknown";
        $newarg['narrative'] = 'Just solving for dy/dx here.';
        $newarg['casstring'] = "[-12+3*noundiff(y(x),x)+8-8*noundiff(y(x),x)=0,-5*noundiff(y(x),x)=4,noundiff(y(x),x)=-4/5]";
        $newarg['debuglist'] = "[EMPTYCHAR,EQUIVCHAR,EQUIVCHAR]";
        $newarg['calculus']  = true;
        $newarg['outcome']   = true;
        $samplearguments[]   = $newarg;

        $newarg = array();
        $newarg['title']     = "Various calculus cases";
        $newarg['narrative'] = 'Calculus, without explicit operations.  With and without the constant.';
        $newarg['casstring'] = "[x^2+1,x^3/3+x,x^2+1,x^3/3+x+c]";
        $newarg['debuglist'] = "[EMPTYCHAR,INTCHAR(x),DIFFCHAR(x),INTCHAR(x)]";
        $newarg['calculus']  = true;
        $newarg['outcome']   = true;
        $samplearguments[]   = $newarg;

        $newarg = array();
        $newarg['title']     = "Further implicit calculus cases";
        $newarg['narrative'] = '';
        $newarg['casstring'] = "[3*x^(3/2)-2/x,(9*sqrt(x))/2+2/x^2,3*x^(3/2)-2/x+c]";
        $newarg['debuglist'] = "[EMPTYCHAR,DIFFCHAR(x),INTCHAR(x)]";
        $newarg['calculus']  = true;
        $newarg['outcome']   = true;
        $samplearguments[]   = $newarg;

        $newarg = array();
        $newarg['title']     = "Implicit calculus";
        $newarg['narrative'] = 'Calculus cases, with an equals sign, not equivalent.';
        $newarg['casstring'] = "[x^2+1,stackeq(x^3/3+x),stackeq(x^2+1),stackeq(x^3/3+x+c)]";
        $newarg['debuglist'] = "[EMPTYCHAR,QMCHAR,QMCHAR,QMCHAR]";
        $newarg['calculus']  = true;
        $newarg['outcome']   = false;
        $samplearguments[]   = $newarg;

        $newarg = array();
        $newarg['title']     = "Explicit differentiation";
        $newarg['narrative'] = 'Calculus with an equals sign, are equivalent with noun operators.';
        $newarg['casstring'] = "[diff(x^2*sin(x),x),stackeq(x^2*diff(sin(x),x)+diff(x^2,x)*sin(x))," .
            "stackeq(x^2*cos(x)+2*x*sin(x))]";
        $newarg['debuglist'] = "[EMPTYCHAR,CHECKMARK,CHECKMARK]";
        $newarg['calculus']  = true;
        $newarg['outcome']   = true;
        $samplearguments[]   = $newarg;

        $newarg = array();
        $newarg['title']     = "Implicit differentiation";
        $newarg['narrative'] = '';
        $newarg['casstring'] = "[y(x)*cos(x)+y(x)^2 = 6*x,cos(x)*diff(y(x),x)+2*y(x)*diff(y(x),x)-y(x)*sin(x) = 6," .
                "(cos(x)+2*y(x))*diff(y(x),x) = y(x)*sin(x)+6,diff(y(x),x) = (y(x)*sin(x)+6)/(cos(x)+2*y(x))]";
        $newarg['debuglist'] = "[EMPTYCHAR,DIFFCHAR(x),EQUIVCHAR,EQUIVCHAR]";
        $newarg['calculus']  = true;
        $newarg['outcome']   = true;
        $samplearguments[]   = $newarg;

        $newarg = array();
        $newarg['title']     = "Logarithmic differentiation";
        $newarg['narrative'] = '';
        $newarg['casstring'] = "[y=e^(5*x)/(7*x+1),ln(y)=5*x-ln(abs(7*x+1))," .
                "1/y*\'diff(y,x) = 5-7/(7*x+1),diff(y,x)=y*(5-7/(7*x+1)),diff(y,x)=e^(5*x)/(7*x+1)*(5-7/(7*x+1))]";
        $newarg['debuglist'] = "[EMPTYCHAR,EQUIVLOG,DIFFCHAR(x),EQUIVCHAR,EQUIVCHAR]";
        $newarg['calculus']  = true;
        $newarg['outcome']   = 'unsupported';
        $samplearguments[]   = $newarg;

        $newarg = array();
        $newarg['title']     = "Explicit integation";
        $newarg['narrative'] = 'Calculus with an equals sign, are equivalent with noun operators.';
        $newarg['casstring'] = "[nounint(s^2+1,s),stackeq(s^3/3+s+c)]";
        $newarg['debuglist'] = "[EMPTYCHAR,INTCHAR(s)]";
        $newarg['calculus']  = true;
        $newarg['outcome']   = true;
        $samplearguments[]   = $newarg;

        $newarg = array();
        $newarg['title']     = "Integration by parts";
        $newarg['narrative'] = 'This has a missing constant of integration.';
        $newarg['casstring'] = "[nounint(x^3*log(x),x),x^4/4*log(x)-1/4*nounint(x^3,x),x^4/4*log(x)-x^4/16]";
        $newarg['debuglist'] = "[EMPTYCHAR,EQUIVCHAR,PLUSC]";
        $newarg['calculus']  = true;
        $newarg['outcome']   = false;
        $samplearguments[]   = $newarg;

        $newarg = array();
        $newarg['title']     = "Integration by parts +c";
        $newarg['narrative'] = '';
        $newarg['casstring'] = "[nounint(x^3*log(x),x),x^4/4*log(x)-1/4*nounint(x^3,x),x^4/4*log(x)-x^4/16+c]";
        $newarg['debuglist'] = "[EMPTYCHAR,EQUIVCHAR,INTCHAR(x)]";
        $newarg['calculus']  = true;
        $newarg['outcome']   = true;
        $samplearguments[]   = $newarg;

        $newarg = array();
        $newarg['title']     = "Integration by parts +c";
        $newarg['narrative'] = '';
        $newarg['casstring'] = "[noundiff(y,x)-2/x*y=x^3*sin(3*x),1/x^2*noundiff(y,x)-2/x^3*y=x*sin(3*x),".
            "noundiff(y/x^2,x)=x*sin(3*x),y/x^2 = nounint(x*sin(3*x),x),y/x^2=(sin(3*x)-3*x*cos(3*x))/9+c]";
        $newarg['debuglist'] = "[EMPTYCHAR,EQUIVCHAR,EQUIVCHAR,INTCHAR(x),INTCHAR(x)]";
        $newarg['calculus']  = true;
        $newarg['outcome']   = true;
        $samplearguments[]   = $newarg;

        $newarg = array();
        $newarg['title']     = "";
        $newarg['narrative'] = 'This argument is aspirational.';
        $newarg['casstring'] = '[y=int(1/(x^2+1),x),y=int(1/((1+%i*x)*(1-%i*x)),x),'.
             'y=1/2*int(1/(1+%i*x)+1/(1-%i*x),x),"Perform the integral",'.
             'y=1/(2*%i)*(log(1+%i*x)-log(1-%i*x)),y=1/(2*%i)*log((1+%i*x)/(1-%i*x)),'.
             '2*%i*y=log((1+%i*x)/(1-%i*x)),(1+%i*x)/(1-%i*x)=e^(2*%i*y),'.
             '1+%i*x=e^(2*%i*y)*(1-%i*x),%i*x*(1+e^(2*%i*y))=e^(2*%i*y)-1,'.
             'x=1/%i*(e^(2*%i*y)-1)/(e^(2*%i*y)+1),x=1/%i*(e^(%i*y)-e^(-%i*y))/(e^(%i*y)+e^(-%i*y)),'.
             'x=(e^(%i*y)-e^(-%i*y))/(2*%i)*(2/(e^(%i*y)+e^(-%i*y))),x=sin(y)/cos(y),x=tan(y)]';
        $newarg['debuglist'] = "[EMPTYCHAR,EQUIVCHAR,EQUIVCHAR,EMPTYCHAR,EMPTYCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR,".
             "EQUIVCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR]";
        $newarg['calculus']  = true;
        $newarg['outcome']   = 'unsupported';
        $samplearguments[]   = $newarg;

        /* ....................................... */

        $newarg = array();
        $newarg['section'] = 'Other cases';
        $samplearguments[] = $newarg;

        $this->rawdata = $samplearguments;
    }

    public function get_answertestfixtures() {
        // Reformulate the data into answer test fixtures.
        $answertestfixtures = array();
        $validoptions = array('assumepos', 'assumereal', 'calculus');

        foreach ($this->rawdata as $equivarg) {
            if (!array_key_exists('section', $equivarg)) {
                $options = array();
                $score = 0;
                if ($equivarg['outcome'] === true) {
                    $score = 1;
                }

                foreach ($validoptions as $opt) {
                    if (array_key_exists($opt, $equivarg)) {
                        if ($equivarg[$opt]) {
                            $options[] = $opt;
                        }
                    }
                }
                $options = implode(',', $options);
                if ('' !== $options) {
                    $options = '[' . $options . ']';
                }

                // TODO: add in CAS code to support all these arguments!
                if ('unsupported' !== $equivarg['outcome']) {
                    $answertestfixtures[] = array('Equiv', $options, $equivarg['casstring'], '[]',
                            $score, $equivarg['debuglist'], '');
                }
            }
        }

        return $answertestfixtures;
    }

}

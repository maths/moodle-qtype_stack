<?php
// This file is part of Stack - http://stack.bham.ac.uk/
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

        /******************************************************************************/
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

        /******************************************************************************/

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
        $newarg['casstring'] = "[a^2=b^2,a= +-b,a= b or a=-b]";
        $newarg['debuglist'] = "[EMPTYCHAR,EQUIVCHAR,EQUIVCHAR]";
        $newarg['outcome']   = true;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Absolute value";
        $newarg['narrative'] = '';
        $newarg['casstring'] = "[a=b,abs(a)=abs(b)]";
        $newarg['debuglist'] = "[EMPTYCHAR,IMPLIESCHAR]";
        $newarg['outcome']   = false;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Absolute value";
        $newarg['narrative'] = '';
        $newarg['casstring'] = "[abs(a)=abs(b),a=b]";
        $newarg['debuglist'] = "[EMPTYCHAR,IMPLIEDCHAR]";
        $newarg['outcome']   = false;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Absolute value";
        $newarg['narrative'] = '';
        $newarg['casstring'] = "[abs(a)=abs(b),a=b or a=-b]";
        $newarg['debuglist'] = "[EMPTYCHAR,EQUIVCHAR]";
        $newarg['outcome']   = false;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Absolute value";
        $newarg['narrative'] = '';
        $newarg['casstring'] = "[abs(a)=abs(b),a^2=b^2]";
        $newarg['debuglist'] = "[EMPTYCHAR,EQUIVCHAR]";
        $newarg['outcome']   = false;
        $samplearguments[] = $newarg;

        /******************************************************************************/
        $newarg = array();
        $newarg['section'] = 'Assume positive values, to condone squaring.';
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Make g the subject";
        $newarg['narrative'] = 'In this example we need to assume all variables are positive.';
        $newarg['casstring'] = "[T=2*pi*sqrt(L/g),T^2=4*pi^2*L/g,g=4*pi^2*L/T^2]";
        $newarg['debuglist'] = "[EMPTYCHAR,EQUIVCHAR,EQUIVCHAR]";
        $newarg['outcome']   = true;
        $newarg['assumepos'] = true;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Squaring both sides";
        $newarg['narrative'] = 'Squaring both sides does not give equivalence.';
        $newarg['casstring'] = "[a=b,a^2=b^2]";
        $newarg['debuglist'] = "[EMPTYCHAR,EQUIVCHAR]";
        $newarg['outcome']   = true;
        $newarg['assumepos'] = true;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Rooting both sides (1)";
        $newarg['narrative'] = 'Taking the square root of both sides does not give equivalence.';
        $newarg['casstring'] = "[a=b,sqrt(a)=sqrt(b)]";
        $newarg['debuglist'] = "[EMPTYCHAR,EQUIVCHAR]";
        $newarg['outcome']   = true;
        $newarg['assumepos'] = true;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Rooting both sides (2)";
        $newarg['narrative'] = 'Taking the square root of both sides does not give equivalence.';
        $newarg['casstring'] = "[a^2=b^2,a=b]";
        $newarg['debuglist'] = "[EMPTYCHAR,EQUIVCHAR]";
        $newarg['outcome']   = true;
        $newarg['assumepos'] = true;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Rooting both sides (2)";
        $newarg['narrative'] = 'Taking the square root of both sides does not give equivalence, we need two values.  '.
                'This is still ok when we assume positive values';
        $newarg['casstring'] = "[a^2=b^2,a=b or a=-b]";
        $newarg['debuglist'] = "[EMPTYCHAR,EQUIVCHAR]";
        $newarg['outcome']   = true;
        $newarg['assumepos'] = true;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Absolute value";
        $newarg['narrative'] = '';
        $newarg['casstring'] = "[a=b,abs(a)=abs(b)]";
        $newarg['debuglist'] = "[EMPTYCHAR,EQUIVCHAR]";
        $newarg['outcome']   = false;
        $newarg['assumepos'] = true;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Absolute value";
        $newarg['narrative'] = '';
        $newarg['casstring'] = "[abs(a)=abs(b),a=b]";
        $newarg['debuglist'] = "[EMPTYCHAR,EQUIVCHAR]";
        $newarg['outcome']   = false;
        $newarg['assumepos'] = true;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Absolute value";
        $newarg['narrative'] = '';
        $newarg['casstring'] = "[abs(a)=abs(b),a=b or a=-b]";
        $newarg['debuglist'] = "[EMPTYCHAR,EQUIVCHAR]";
        $newarg['outcome']   = false;
        $newarg['assumepos'] = true;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Absolute value";
        $newarg['narrative'] = '';
        $newarg['casstring'] = "[abs(a)=abs(b),a^2=b^2]";
        $newarg['debuglist'] = "[EMPTYCHAR,EQUIVCHAR]";
        $newarg['outcome']   = false;
        $newarg['assumepos'] = true;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Solving quadratic equations 2 (pm) and assume pos";
        $newarg['narrative']  = '';
        $newarg['casstring'] = "[x^2=2,x=+-sqrt(2),x=sqrt(2) or x=-sqrt(2)]";
        $newarg['debuglist'] = "[EMPTYCHAR,EQUIVCHAR,EQUIVCHAR]";
        $newarg['outcome']   = true;
        $newarg['assumepos'] = true;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Solving quadratic equations 2 (pm) and assume pos";
        $newarg['narrative']  = '';
        $newarg['casstring'] = "[x^2=2,x=sqrt(2)]";
        $newarg['debuglist'] = "[EMPTYCHAR,EQUIVCHAR]";
        $newarg['outcome']   = true;
        $newarg['assumepos'] = true;
        $samplearguments[] = $newarg;

        /******************************************************************************/
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
        $newarg['debuglist'] = "[EMPTYCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR]";
        $newarg['outcome']   = true;
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
        $newarg['narrative']  = '';
        $newarg['casstring'] = "[x^2=2,x=+-sqrt(2),x=sqrt(2) or x=-sqrt(2)]";
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
        $newarg['debuglist'] = "[EMPTYCHAR,IMPLIEDCHAR,IMPLIEDCHAR,EQUIVCHAR]";
        $newarg['outcome']   = false;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Solving quadratic equations 6 (specific with pm)";
        $newarg['narrative'] = 'Uses the \(\pm\) operator to capture both roots.';
        $newarg['casstring'] = "[x^2-10*x+9 = 0, (x-5)^2-16 = 0, (x-5)^2 =16, x-5 =+-4, x-5 =4 or x-5=-4, x = 1 or x = 9]";
        $newarg['debuglist'] = "[EMPTYCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR]";
        $newarg['outcome']   = true;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Solving quadratic equations 7 (general with pm)";
        $newarg['narrative'] = '';
        $newarg['casstring'] = "[x^2-2*p*x-q=0,x^2-2*p*x=q,x^2-2*p*x+p^2=q+p^2,(x-p)^2=q+p^2,x-p=+-sqrt(q+p^2),".
                "x-p=sqrt(q+p^2) or x-p=-sqrt(q+p^2),x=p+sqrt(q+p^2) or x=p-sqrt(q+p^2)]";
        $newarg['debuglist'] = "[EMPTYCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR]";
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
        $newarg['debuglist'] = "[EMPTYCHAR,EQUIVCHAR,EQUIVCHAR]";
        $newarg['outcome']   = true;
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
                "a*x+b/2= +-sqrt(b^2/4-a*c),a*x=-b/2+sqrt(b^2/4-a*c) or a*x=-b/2-sqrt(b^2/4-a*c), ".
                "(a=0 or x=(-b+sqrt(b^2-4*a*c))/(2*a)) or (a=0 or x=(-b-sqrt(b^2-4*a*c))/(2*a)), ".
                "a=0 or x=(-b+sqrt(b^2-4*a*c))/(2*a) or x=(-b-sqrt(b^2-4*a*c))/(2*a)]";
        $newarg['debuglist'] = "[EMPTYCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR,SAMEROOTS]";
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
                "x^2-2*x+1 = 4*x+8,x^2-6*x-7 = 0,(x-7)*(x+1) = 0,x=7 or x=-1]";
        $newarg['debuglist'] = "[EMPTYCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR]";
        $newarg['outcome']   = true;
        $newarg['assumepos'] = true;
        $samplearguments[] = $newarg;

        /******************************************************************************/
        $newarg = array();
        $newarg['section'] = 'Difficult cases and nonsense arguments';
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Solving linear equations (nonsense)";
        $newarg['narrative'] = 'In this argument we "move over" one term, but end up at the right answer.'.
                'Actually, the term we "moved over" is zero, which is why we don\'t pick up the problem';
        $newarg['casstring'] = "[6*x-12=3*(x-2),6*x-12+3*(x-2)=0,12*x-24=0,x=2]";
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
        $newarg['outcome']   = false;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Solving quadratic equations (Maxwell's nonsense)";
        $newarg['narrative'] = 'Maxwell pg 88.  The wrong method leads to a correct solution.  '.
                'This argument is false in general, but in this special case leads to the correct solution.  '.
                'We can not spot this kind of thing.';
        $newarg['casstring'] = "[(x+3)*(2-x)=4,x+3=4 or (2-x)=4,x=1 or x=-2]";
        $newarg['debuglist'] = "[EMPTYCHAR,EQUIVCHAR,EQUIVCHAR]";
        $newarg['outcome']   = false;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Solving quadratic equations (Maxwell's nonsense)";
        $newarg['narrative'] = 'Maxwell pg 89.  A general version of the previous argument.';
        $newarg['casstring'] = "[(x-p)*(x-q)=0,x^2-p*x-q*x+p*q=0,1+q-x-p-p*q+p*x+x+q*x-x^2=1-p+q,(1+q-x)*(1-p+x)=1-p+q,".
                "(1+q-x)=1-p+q or (1-p+x)=1-p+q,x=p or x=q]";
        $newarg['debuglist'] = "[EMPTYCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR]";
        $newarg['outcome']   = false;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Classic nonsense 1.";
        $newarg['narrative'] = 'Here we create a problem by dividing by a term which is actually zero.';
        $newarg['casstring'] = "[a=b, a^2=a*b, a^2-b^2=a*b-b^2, (a-b)*(a+b)=b*(a-b), a+b=b, 2*a=a, 1=2]";
        $newarg['debuglist'] = "[EMPTYCHAR,QMCHAR,EQUIVCHAR,EQUIVCHAR,QMCHAR,EQUIVCHAR,QMCHAR]";
        $newarg['outcome']   = false;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Classic nonsense 1 (with auditing).";
        $newarg['narrative'] = 'Here we create a problem by dividing by a term which is actually zero.';
        $newarg['casstring'] = "[a=b, a^2=a*b or a=0, a^2-b^2=a*b-b^2 or a=0, (a-b)*(a+b)=b*(a-b) or a=0, ".
                "a+b=b or a=0 or a-b=0, 2*a=a or a=0 or a=b, 2=1 or a=0 or a=b, a=0 or a=b]";
        $newarg['debuglist'] = "[EMPTYCHAR,QMCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR,QMCHAR,EQUIVCHAR]";
        $newarg['outcome']   = false;
        $samplearguments[] = $newarg;

        /******************************************************************************/
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
        $newarg['debuglist'] = "[EMPTYCHAR,QMCHAR,EQUIVCHAR,EQUIVCHAR]";
        $newarg['outcome']   = false;
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
        $newarg['narrative'] = 'This is similar to the previous argument, but there is a problem with ATEquation.';
        $newarg['casstring'] = "[(x+5)/(x-7)-5= (4*x-40)/(13-x),(x+5-5*(x-7))/(x-7)= (4*x-40)/(13-x), ".
                "(4*x-40)/(7-x)= (4*x-40)/(13-x),7-x= 13-x,7= 13]";
        $newarg['debuglist'] = "[EMPTYCHAR,EQUIVCHAR,EQUIVCHAR,QMCHAR,EQUIVCHAR]";
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

        /******************************************************************************/
        $newarg = array();
        $newarg['section'] = 'Equational reasoning';
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

        /******************************************************************************/
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
        $newarg['title']     = "Solving an inequality (remove redundant inequalities)";
        $newarg['narrative'] = '';
        $newarg['casstring'] = "[x^2>=9 and x>3, x^2-9>=0 and x>3, (x>=3 or x<=-3) and x>3, x>3]";
        $newarg['debuglist'] = "[EMPTYCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR]";
        $newarg['outcome']   = true;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Solving inequalities with the absolute value function";
        $newarg['narrative'] = '';
        $newarg['casstring'] = "[2*x/abs(x-1)<1, 2*x<abs(x-1),".
                "(x>=1 and 2*x<x-1) or (x<1 and 2*x<-x+1),(x>=1 and x<-1) or (x<1 and 3*x<1),x<1/3]";
        $newarg['debuglist'] = "[EMPTYCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR]";
        $newarg['outcome']   = true;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Find the values of a which satisfy this inequality for all x.";
        $newarg['narrative'] = 'This argument contains a comment and hence cannot be automatically assessed.  '.
                'Each internal bit is fine, but the logic uses a universal quantifier which we cannot assess. '.
                'This argument will need semi-automatic marking.';
        $newarg['casstring'] = "[-x^2+a*x+a-3<0, a-3<x^2-a*x, a-3<(x-a/2)^2-a^2/4, a^2/4+a-3<(x-a/2)^2, a^2+4*a-12<4*(x-a/2)^2, ".
                "(a-2)*(a+6)<4*(x-a/2)^2, \"This inequality is required to be true for all x, it must be true when the right hand side takes its minimum value.  This happens for x=a/2\", ".
                "(a-2)*(a+6)<0, ((a-2)<0 and (a+6)>0) or ((a-2)>0 and (a+6)<0), (a<2 and a>-6) or (a>2 and a<-6), ".
                "(-6<a and a<2) or false, (-6<a and a<2)]";
        $newarg['debuglist'] = "[EMPTYCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR,EMPTYCHAR,EMPTYCHAR,EQUIVCHAR,".
                "EQUIVCHAR,EQUIVCHAR,EQUIVCHAR]";
        $newarg['outcome']   = 'unknown';
        $samplearguments[] = $newarg;

        /******************************************************************************/
        $newarg = array();
        $newarg['section'] = 'Simultaneous equations and substitution';
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Simultaneous equations (must use and to join them)";
        $newarg['narrative'] = '';
        $newarg['casstring'] = "[x^2+y^2=8 and x=y, 2*x^2=8 and y=x, x^2=4 and y=x, x= +-2 and y=x, ".
                "(x= 2 and y=x) or (x=-2 and y=x), (x=2 and y=2) or (x=-2 and y=-2)]";
        $newarg['debuglist'] = "[EMPTYCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR]";
        $newarg['outcome']   = true;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Simultaneous equations (without using square roots or substitution)";
        $newarg['narrative'] = '';
        $newarg['casstring'] = "[x^2+y^2=5 and x*y=2, x^2+y^2-5=0 and x*y-2=0, x^2-2*x*y+y^2-1=0 and x^2+2*x*y+y^2-9=0, ".
                "(x+y)^2-1=0 and (x-y)^2-3^2=0, ".
                "(x+y=1 and x-y=3) or (x+y=-1 and x-y=3) or (x+y=1 and x-y=-3) or (x+y=-1 and x-y=-3), ".
                "(x=1 and y=2) or (x=2 and y=1) or (x=-2 and y=-1) or (x=-1 and y=-2)]";
        $newarg['debuglist'] = "[EMPTYCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR]";
        $newarg['outcome']   = true;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Simultaneous equations (without using square roots or substitution) 2";
        $newarg['narrative'] = '';
        $newarg['casstring'] = "[4*x^2+7*x*y+4*y^2=0 and y=x-4, 4*x^2+7*x*(x-4)+4*(x-4)^2=0 and y=x-4, ".
                "15*x^2-60*x+60=0 and y=x-4, (x-2)^2=0 and y=x-4, x=2 and y=x-4, x=2 and y=-2]";
        $newarg['debuglist'] = "[EMPTYCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR]";
        $newarg['outcome']   = true;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Substitution";
        $newarg['narrative'] = '';
        $newarg['casstring'] = "[a^2=b and a^2=1, b=a^2 and (a=1 or a=-1), (b=1 and a=1) or (b=1 and a=-1)]";
        $newarg['debuglist'] = "[EMPTYCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR]";
        $newarg['outcome']   = true;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Substitution";
        $newarg['narrative'] = '';
        $newarg['casstring'] = "[a^2=b and x=1, b=a^2 and x=1]";
        $newarg['debuglist'] = "[EMPTYCHAR,EQUIVCHAR]";
        $newarg['outcome']   = true;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Find two distinct numbers which are the square of each other (needs substitution, not equals)";
        $newarg['narrative'] = '';
        $newarg['casstring'] = "[a^2=b and b^2=a, b=a^2 and a^4=a, b=a^2 and a^4-a=0, b=a^2 and a*(a-1)*(a^2+a+1)=0, ".
                "b=a^2 and (a=0 or a=1 or a^2+a+1=0), (b=0 and a=0) or (b=1 and a=1)]";
        $newarg['debuglist'] = "[EMPTYCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR]";
        $newarg['outcome']   = true;
        $samplearguments[] = $newarg;

        /******************************************************************************/
        $newarg = array();
        $newarg['section'] = 'Other cases';
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Differential quotient as the unknown";
        $newarg['narrative'] = '';
        $newarg['casstring'] = "[-12+3*diff(y(x),x)+8-8*diff(y(x),x)=0,-5*diff(y(x),x)=4,diff(y(x),x)=-4/5]";
        $newarg['debuglist'] = "[EMPTYCHAR,EQUIVCHAR,EQUIVCHAR]";
        $newarg['outcome']   = true;
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
        $newarg['outcome']   = true;
        $samplearguments[] = $newarg;

        $newarg = array();
        $newarg['title']     = "Proof by induction";
        $newarg['narrative'] = "";
        $newarg['casstring'] = '["Set P(n) be the statement that",sum(k^2,k,1,n) = n*(n+1)*(2*n+1)/6, '.
                '"Then P(1) is the statement", 1^2 = 1*(1+1)*(2*1+1)/6, 1 = 1, '.
                '"So P(1) holds.  Now assume P(n) is true.",sum(k^2,k,1,n) = n*(n+1)*(2*n+1)/6,'.
                'sum(k^2,k,1,n) +(n+1)^2= n*(n+1)*(2*n+1)/6 +(n+1)^2,sum(k^2,k,1,n+1)= (n+1)*(n*(2*n+1) +6*(n+1))/6,'.
                'sum(k^2,k,1,n+1)= (n+1)*(2*n^2+7*n+6)/6,sum(k^2,k,1,n+1)= (n+1)*(n+1+1)*(2*(n+1)+1)/6]';
        $newarg['debuglist'] = "[EMPTYCHAR,EMPTYCHAR,EMPTYCHAR,EQUIVCHAR,EMPTYCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR]";
        $newarg['outcome']   = true;

        $this->rawdata = $samplearguments;
    }

    public function get_answertestfixtures() {
        // Reformulate the data into answer test fixtures.
        $answertestfixtures = array();
        foreach ($this->rawdata as $equivarg) {
            if (!array_key_exists('section', $equivarg)) {
                $score = 0;
                if ($equivarg['outcome']) {
                    $score = 1;
                }
                $answertestfixtures[] = array('Equiv', '', $equivarg['casstring'], '[]', $score, $equivarg['debuglist'], '');
            }
        }

        return $answertestfixtures;
    }

}

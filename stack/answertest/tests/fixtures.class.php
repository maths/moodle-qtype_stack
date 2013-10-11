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
 * This script runs the answers tests and verifies the results.
 *
 * This serves two purposes. First, it verifies that the answer tests are working
 * correctly, and second it serves to document the expected behaviour of answer
 * tests, which is useful for learning how they work.
 *
 * @copyright  2012 University of Birmingham
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


class stack_answertest_test_data {
    const NAME    = 0;
    const SANS    = 1;
    const TANS    = 2;
    const SCORE   = 3;
    const OPTIONS = 4;
    const NOTES   = 5;

    /* Raw data should be in the following form.
     * Name of test
     * Student's answer
     * Teacher's answer
     * Expected result
     *    0 = different
     *    1 = same
     *    -1 = "Test fails", but this is trapped.
     * Test options
     * Header row in the table.
     */
    protected static $rawdata = array(
        // AlgEquiv Answer tests.
        array('AlgEquiv', '1/0', '1', -1, '', ''),
        array('AlgEquiv', '1', '1/0', -1, '', ''),
        array('AlgEquiv', 'integerp(3)', 'true', 1, '', 'Predicates'),
        array('AlgEquiv', 'integerp(3.1)', 'true', 0, '', ''),
        array('AlgEquiv', 'X', 'x', 0, '', 'Case sensitivity'),
        array('AlgEquiv', 'Y=1+X', 'y=1+x', 0, '', 'Permutations of variables (To do: a dedicated answer test with feedback)'),
        array('AlgEquiv', 'v+w+x+y+z', 'a+b+c+A+B', 0, '', ''),
        array('AlgEquiv', '4^(-1/2)', '1/2', 1, '', 'Numbers'),
        array('AlgEquiv', '4^(1/2)', 'sqrt(4)', 1, '', ''),
        array('AlgEquiv', '0.5', '1/2', 1, '', 'Mix of floats and rational numbers'),
        array('AlgEquiv', '0.33', '1/3', 0, '', ''),
        array('AlgEquiv', '0.333333333333333', '1/3', 1, '', ''),

        array('AlgEquiv', 'sqrt(-1)', '%i', 1, '', 'Complex numbers'),
        array('AlgEquiv', '%i', 'e^(i*pi/2)', 1, '', ''),
        array('AlgEquiv', '(4*sqrt(3)*%i+4)^(1/5)', '8^(1/5)*(cos(%pi/15)+%i*sin(%pi/15))', 1, '', ''),
        array('AlgEquiv', '(4*sqrt(3)*%i+4)^(1/5)', 'rectform((4*sqrt(3)*%i+4)^(1/5))', 1, '', ''),
        array('AlgEquiv', '(4*sqrt(3)*%i+4)^(1/5)', 'polarform((4*sqrt(3)*%i+4)^(1/5))', 1, '', ''),

        array('AlgEquiv', 'inf', 'inf', 1, '', 'Infinity'),
        array('AlgEquiv', 'inf', '-inf', 0, '', ''),
        array('AlgEquiv', '2*inf', 'inf', 0, '', ''),
        array('AlgEquiv', '0*inf', '0', 1, '', ''),

        array('AlgEquiv', 'x^(1/2)', 'sqrt(x)', 1, '', 'Powers and roots'),
        array('AlgEquiv', 'x', 'sqrt(x^2)', 0, '', ''),
        array('AlgEquiv', 'abs(x)', 'sqrt(x^2)', 1, '', ''),
        array('AlgEquiv', 'sqrt((x-3)*(x-5))', 'sqrt(x-3)*sqrt(x-5)', 0, '', ''),
        array('AlgEquiv', '1/sqrt(x)', 'sqrt(1/x)', 1, '', ''),
        array('AlgEquiv', 'x-1', '(x^2-1)/(x+1)', 1, '', ''),
        array('AlgEquiv', 'a^b*a^c', 'a^(b+c)', 1, '', ''),
        array('AlgEquiv', '(4*sqrt(3)*%i+4)^(1/5)', '6^(1/5)*cos(%pi/15)-6^(1/5)*%i*sin(%pi/15)', 0, '', ''),

        array('AlgEquiv', '(x-1)^2', 'x^2-2*x+1', 1, '', 'Polynomials and rational function'),
        array('AlgEquiv', '(x-1)*(x^2+x+1)', 'x^3-1', 1, '', ''),
        array('AlgEquiv', '(x-1)^(-2)', '1/(x^2-2*x+1)', 1, '', ''),
        array('AlgEquiv', '1/n-1/(n+1)', '1/(n*(n+1))', 1, '', ''),
        array('AlgEquiv', 'cos(x)', 'cos(-x)', 1, '', 'Trig functions'),
        array('AlgEquiv', 'cos(x)^2+sin(x)^2', '1', 1, '', ''),
        array('AlgEquiv', '2*cos(x)^2-1', 'cos(2*x)', 1, '', ''),
        array('AlgEquiv', 'exp(%i*%pi)', '-1', 1, '', ''),
        array('AlgEquiv', '2*cos(2*x)+x+1', '-sin(x)^2+3*cos(x)^2+x', 1, '', ''),
        array('AlgEquiv', '(2*sec(2*t)^2-2)/2',
            '-(sin(4*t)^2-2*sin(4*t)+cos(4*t)^2-1)*(sin(4*t)^2+2*sin(4*t)+cos(4*t)^2-1)/(sin(4*t)^2+cos(4*t)^2+2*cos(4*t)+1)^2',
            1, '', ''),
        array('AlgEquiv', '(-1)^n*cos(x)^n', '(-cos(x))^n', 1, '', ''),
        array('AlgEquiv', 'log(a^2*b)', '2*log(a)+log(b)', 1, '', 'Logarithms'),
        array('AlgEquiv', 'lg(10^x)', 'x', 1, '', ''),
        array('AlgEquiv', '(2*log(2*x)+x)/(2*x)', '(log(2*x)+2)/(2*sqrt(x))', 0, '', ''),
        array('AlgEquiv', 'e^1-e^(-1)', '2*sinh(1)', 1, '', 'Hyperbolic trig'),
        array('AlgEquiv', 'x', '[1,2,3]', 0, '', 'Lists'),
        array('AlgEquiv', '[1,2]', '[1,2,3]', 0, '', ''),
        array('AlgEquiv', '[1,2,4]', '[1,2,3]', 0, '', ''),
        array('AlgEquiv', '[1,x>2]', '[1,2<x]', 1, '', ''),
        array('AlgEquiv', '[1,2,[2-x<0,{1,2,2,2, 1,3}]]', '[1,2,[2-x<0,{1,2}]]', 0, '', ''),

        array('AlgEquiv', 'x', '{1,2,3}', 0, '', 'Sets'),
        array('AlgEquiv', '{1,2}', '{1,2,3}', 0, '', ''),
        array('AlgEquiv', '{2/4, 1/3}', '{1/2, 1/3}', 1, '', ''),
        array('AlgEquiv', '{1,2,4}', '{1,2,3}', 0, '', ''),
        array('AlgEquiv', '{1,x>4}', '{4<x, 1}', 1, '', ''),
        array('AlgEquiv', '{x-1=0,x>1 and 5>x}', '{x>1 and x<5,x=1}', 1, '', ''),
        array('AlgEquiv', '{x-1=0,x>1 and 5>x}', '{x>1 and x<5,x=2}', 0, '', ''),
        array('AlgEquiv', '{x-1=0,x>1 and 5>x}', '{x>1 and x<3,x=1}', 0, '', ''),

        array('AlgEquiv', 'matrix([1,2],[2,3])', 'matrix([1,2],[2,3])', 1, '', 'Matrices'),
        array('AlgEquiv', 'matrix([1,2],[2,3])', 'matrix([1,2,3],[2,3,3])', 0, '', ''),
        array('AlgEquiv', 'matrix([1,2],[2,3])', 'matrix([1,2],[2,5])', 0, '', ''),
        array('AlgEquiv', 'matrix([x>4,{1,x^2}],[[1,2],[1,3]])', 'matrix([4-x<0,{x^2, 1}],[[1,2],[1,3]])', 1, '', ''),
        array('AlgEquiv', 'matrix([x>4,{1,x^2}],[[1,2],[1,3]])', 'matrix([4-x<0,{x^2, 1}],[[1,2],[1,4]])', 0, '', ''),

        array('AlgEquiv', '1', 'x=1', 0, '', 'Equations'),
        array('AlgEquiv', 'x=1', 'x=1', 1, '', ''),
        array('AlgEquiv', '1=x', 'x=1', 1, '', ''),
        array('AlgEquiv', 'x=2', 'x=1', 0, '', ''),
        array('AlgEquiv', 'x=x', 'y=y', 1, '', ''),
        array('AlgEquiv', 'x+y=1', 'y=1-x', 1, '', ''),
        array('AlgEquiv', '2*x+2*y=1', 'y=0.5-x', 1, '', ''),
        array('AlgEquiv', '(x-y)*(x+y)=0', 'y^2=x^2', 1, '', ''),
        array('AlgEquiv', '(x-1)=0', '(x-1)^2=0', 0, '', ''),
        array('AlgEquiv', '(x-1)*(x+1)*(y-1)*(y+1)=0', 'y^2+x^2=1+x^2*y^2', 1, '', ''),
        array('AlgEquiv', '1/x+1/y=2', 'y = x/(2*x-1)', 1, '', ''),
        array('AlgEquiv', 'y=sin(2*x)', 'y/2=cos(x)*sin(x)', 1, '', ''),
        array('AlgEquiv', 'x+y=i', 'y=i-x', 1, '', ''),
        array('AlgEquiv', '(1+i)*(x+y)=0', 'y=-x', 1, '', ''),
        array('AlgEquiv', 'x^2+y^2=1', 'y=sqrt(x^2-1)', 0, '', ''),
        array('AlgEquiv', 's^2*%e^(s*t)=0', 's^2=0', 0, '', ''),

        array('AlgEquiv', 'f(x):=1/0', 'f(x):=x^2', -1, '', 'Functions'),
        array('AlgEquiv', '1', 'f(x):=x^2', 0, '', ''),
        array('AlgEquiv', 'f(x)=x^2', 'f(x):=x^2', 0, '', ''),
        array('AlgEquiv', 'f(x):=x^2', 'f(x,y):=x^2+y^2', 0, '', ''),
        array('AlgEquiv', 'f(x):=x^2', 'f(x)=x^2', 0, '', ''),
        array('AlgEquiv', 'f(x):=x^2', 'f(x):=x^2', 1, '', ''),
        array('AlgEquiv', 'f(x):=x^2', 'f(x):=sin(x)', 0, '', ''),
        array('AlgEquiv', 'g(x):=x^2', 'f(x):=x^2', 0, '', ''),
        array('AlgEquiv', 'f(y):=y^2', 'f(x):=x^2', 1, '', ''),
        array('AlgEquiv', 'f(a,b):=a^2+b^2', 'f(x,y):=x^2+y^2', 1, '', ''),

        array('AlgEquiv', '1', 'x>1', 0, '', 'Inequalities'),
        array('AlgEquiv', 'x=1', 'x>1 and x<5', 0, '', ''),
        array('AlgEquiv', 'x<1', 'x>1', 0, '', ''),
        array('AlgEquiv', 'x>1', 'x<=1', 0, '', ''),
        array('AlgEquiv', 'x>=2', 'x<2', 0, '', ''),
        array('AlgEquiv', 'x>=1', 'x>2', 0, '', ''),
        array('AlgEquiv', 'x>1', 'x>1', 1, '', ''),
        array('AlgEquiv', 'x>=1', 'x>=1', 1, '', ''),
        array('AlgEquiv', 'x>2', 'x>1', 0, '', ''),
        array('AlgEquiv', '1<x', 'x>1', 1, '', ''),
        array('AlgEquiv', '2*x>=x^2', 'x^2<=2*x', 1, '', ''),
        array('AlgEquiv', 'x^2+x+4<(3*x-1)*(2*x+1)', 'x^2-1>0', 1, '', ''),
        array('AlgEquiv', 'x>1 and x<5', 'x>1 and x<5', 1, '', ''),
        array('AlgEquiv', 'x>1 and x<5', '5>x and 1<x', 1, '', ''),
        array('AlgEquiv', 'not (x<=2 and -2<=x)', 'x>2 or -2>x', 1, '', ''),
        array('AlgEquiv', 'x>=1 or 1<=x', 'x>=1', 1, '', ''),
        array('AlgEquiv', '(x>4 and x<5) or (x<-4 and x>-5) or (x+5>0 and x<-4)', '(x>-5 and x<-4) or (x>4 and x<5)', 1, '', ''),
        array('AlgEquiv', '(x>4 and x<5) or (x<-4 and x>-5) or (x+5>0 and x<-4)', '(x>-5 and x<-4) or (x>8 and x<5)', 0, '', ''),

        array('AlgEquiv', '2*x^2+x>=6', 'x<=-2 or x>=3/2', 0, '',
                'Inequalities - not currently considered equivalent, but maybe in the future'),

        array('AlgEquiv', 'sqrt(12)', '2*sqrt(3)', 1, '', 'Surds'),
        array('AlgEquiv', 'sqrt(11+6*sqrt(2))', '3+sqrt(2)', 1, '', ''),
        array('AlgEquiv', '(19601-13860*sqrt(2))^(7/4)', '(5*sqrt(2)-7)^7', 1, '', ''),
        array('AlgEquiv', '(19601-13861*sqrt(2))^(7/4)', '(5*sqrt(2)-7)^7', 0, '', ''),
        array('AlgEquiv', '(x-a)*(x+a)/sqrt(x^2-a^2)', 'sqrt(x^2-a^2)', 1, '', ''),
        array('AlgEquiv', '(n+1)*n!', '(n+1)!', 1, '', 'Factorials'),
        array('AlgEquiv', 'n/n!', '1/(n-1)!', 1, '', ''),
        array('AlgEquiv', '2/%i*ln(sqrt((1+z)/2)+%i*sqrt((1-z)/2))', '-%i*ln(z+i*sqrt(1-z^2))', 1, '', 'These currently fail'),
        array('AlgEquiv', '-%i/sqrt(x)', 'sqrt(-1/x)', 1, '', ''),
        array('AlgEquiv', 'x^2>4', 'x>2 and x<-2', 1, '', ''),
        array('AlgEquiv', 'x^4>=0', 'x^2>=0', 1, '', ''),
        array('AlgEquiv', '-inf', 'minf', 1, '', ''),
        array('AlgEquiv', '(sqrt(108)+10)^(1/3)-(sqrt(108)-10)^(1/3)', '2', 1, '', ''), // Cardano's example!

        // SubstEquiv Answer tests.
        array('SubstEquiv', '1/0', 'x^2-2*x+1', -1, '', ''),
        array('SubstEquiv', 'x^2+1', 'x^2+1', 1, '', ''),
        array('SubstEquiv', 'x^2+1', 'x^3+1', 0, '', ''),
        array('SubstEquiv', 'X^2+1', 'x^2+1', 1, '', ''),
        array('SubstEquiv', 'x^2+y', 'a^2+b', 1, '', ''),
        array('SubstEquiv', 'x^2+y/z', 'a^2+c/b', 1, '', ''),
        array('SubstEquiv', 'y=x^2', 'a^2=b', 1, '', ''),
        array('SubstEquiv', '{x=1,y=2}', '{x=2,y=1}', 1, '', ''),

        array('EqualComAss', '1/0', '0', -1, '', ''),
        array('EqualComAss', '0', '1/0', -1, '', ''),
        array('EqualComAss', '1+2*x', 'x*2+1', 1, '', 'Simple polynomials'),
        array('EqualComAss', '1+x', '2*x+1', 0, '', ''),
        array('EqualComAss', '1+x+x', '2*x+1', 0, '', ''),
        array('EqualComAss', '(x+y)+z', 'z+x+y', 1, '', ''),
        array('EqualComAss', 'x*x', 'x^2', 0, '', ''),
        array('EqualComAss', '(1-x)^2', '(x-1)^2', 0, '', ''),
        array('EqualComAss', '-1+2', '2-1', 1, '', 'Unary minus'),
        array('EqualComAss', '-1*2+3*4', '3*4-1*2', 1, '', ''),
        array('EqualComAss', '(-1*2)+3*4', '10', 0, '', ''),
        array('EqualComAss', '-1*2+3*4', '3*4-1*2', 1, '', ''),
        array('EqualComAss', 'x*(-y)', '-x*y', 1, '', ''),
        array('EqualComAss', 'x*(-y)', '-(x*y)', 1, '', ''),
        array('EqualComAss', '(-x)*(-x)', 'x*x', 0, '', ''),
        array('EqualComAss', '(-x)*(-x)', 'x^2', 0, '', ''),
        array('EqualComAss', '1/2', '3/6', 0, '', 'Rational expressions'),
        array('EqualComAss', '1/(1+2*x)', '1/(2*x+1)', 1, '', ''),
        array('EqualComAss', '2/(4+2*x)', '1/(x+2)', 0, '', ''),
        array('EqualComAss', '(a*b)/c', 'a*(b/c)', 1, '', ''),
        array('EqualComAss', '(-x)/y', '-(x/y)', 1, '', ''),
        array('EqualComAss', 'x/(-y)', '-(x/y)', 0, '', ''),
        array('EqualComAss', '-1/(1-x)', '1/(x-1)', 0, '', ''),
        array('EqualComAss', '1/2*1/x', '1/(2*x)', 0, '', ''),
        array('EqualComAss', '%i', 'e^(i*pi/2)', 0, '', 'Complex numbers'),
        array('EqualComAss', '(4*sqrt(3)*%i+4)^(1/5)', 'rectform((4*sqrt(3)*%i+4)^(1/5))', 0, '', ''),
        array('EqualComAss', '(4*sqrt(3)*%i+4)^(1/5)', '8^(1/5)*(cos(%pi/15)+%i*sin(%pi/15))', 0, '', ''),
        array('EqualComAss', '(4*sqrt(3)*%i+4)^(1/5)', 'polarform((4*sqrt(3)*%i+4)^(1/5))', 0, '', ''),

        array('EqualComAss', 'y=x', 'x=y', 0, '', 'Equality is not included as commutative....'),
        array('EqualComAss', 'x+1', 'y=2*x+1', 0, '', 'Equations'),
        array('EqualComAss', 'y=1+2*x', 'y=2*x+1', 1, '', ''),
        array('EqualComAss', 'y=x+x+1', 'y=1+2*x', 0, '', ''),
        array('EqualComAss', '{2*x+1,2}', '{2, 1+x*2}', 1, '', 'Sets'),
        array('EqualComAss', '{2*x+1, 1+1}', '{2, 1+x*2}', 0, '', ''),
        array('EqualComAss', '[2*x+1,2]', '[1+x*2,2]', 1, '', 'Lists'),
        array('EqualComAss', '[x+x+1, 1+1]', '[1+x*2,2]', 0, '', ''),
        array('EqualComAss', 'matrix([1,2],[2,3])', 'matrix([1,2],[2,3])', 1, '', 'Matrices'),
        array('EqualComAss', 'matrix([1,2],[2,3])', 'matrix([1,2,3],[2,3,3])', 0, '', ''),
        array('EqualComAss', 'matrix([1,2],[2,3])', 'matrix([1,2],[2,5])', 0, '', ''),
        array('EqualComAss', 'matrix([1,2],[2,2+1])', 'matrix([1,2],[2,3])', 0, '', ''),
        array('EqualComAss', 'matrix([x+x, 1],[1, 1])', 'matrix([2*x, 1],[1, 1])', 0, '', ''),

        array('CasEqual', '1/0', 'x^2-2*x+1', -1, '', ''),
        array('CasEqual', 'x', '1/0', -1, '', ''),
        array('CasEqual', 'a', 'A', 0, '', 'Case sensitivity'),
        array('CasEqual', '4^(-1/2)', '1/2', 1, '', 'Numbers'),
        array('CasEqual', '0.5', '1/2', 0, '', 'Mix of floats and rational numbers'),
        array('CasEqual', 'x^(1/2)', 'sqrt(x)', 1, '', ''),
        array('CasEqual', 'abs(x)', 'sqrt(x^2)', 1, '', ''),
        array('CasEqual', 'x-1', '(x^2-1)/(x+1)', 0, '', ''),
        array('CasEqual', 'x+x', '2*x', 1, '', 'Polynomials and rational function'),
        array('CasEqual', 'x+x^2', 'x^2+x', 1, '', ''),
        array('CasEqual', '(x-1)^2', 'x^2-2*x+1', 0, '', ''),
        array('CasEqual', '(x-1)^(-2)', '1/(x^2-2*x+1)', 0, '', ''),
        array('CasEqual', '1/n-1/(n+1)', '1/(n*(n+1))', 0, 'x', ''),
        array('CasEqual', 'cos(x)', 'cos(-x)', 1, '', 'Trig functions'),
        array('CasEqual', 'cos(x)^2+sin(x)^2', '1', 0, '', ''),
        array('CasEqual', '2*cos(x)^2-1', 'cos(2*x)', 0, '', ''),

        array('SameType', '1/0', '1', -1, '', ''),
        array('SameType', '1', '1/0', -1, '', ''),
        array('SameType', '4^(-1/2)', '1/2', 1, '', 'Numbers'),
        array('SameType', 'x', '[1,2,3]', 0, '', 'Lists'),
        array('SameType', '[1,2]', '[1,2,3]', 1, '', ''),
        array('SameType', '[1,x>2]', '[1,2<x]', 1, '', ''),
        array('SameType', '[1,x,3]', '[1,2<x,4]', 0, '', ''),
        array('SameType', 'x', '{1,2,3}', 0, '', 'Sets'),
        array('SameType', '{1,2}', '{1,2,3}', 1, '', ''),
        array('SameType', 'matrix([1,2],[2,3])', 'matrix([1,2],[2,3])', 1, '', 'Matrices'),
        array('SameType', '[[1,2],[2,3]]', 'matrix([1,2],[2,3])', 0, '', ''),
        array('SameType', 'matrix([1,2],[2,3])', 'matrix([1,2,3],[2,3,3])', 1, '', ''),
        array('SameType', 'matrix([x>4,{1,x^2}],[[1,2],[1,3]])', 'matrix([4-x<0,{x^2, 1}],[[1,2],[1,3]])', 1, '', ''),
        array('SameType', 'matrix([x>4,[1,x^2]],[[1,2],[1,3]])', 'matrix([4-x<0,{x^2, 1}],[[1,2],[1,4]])', 0, '', ''),
        array('SameType', '1', 'x=1', 0, '', 'Equations'),
        array('SameType', 'x=1', 'x=1', 1, '', ''),
        array('SameType', '1', 'x>1', 0, '', 'Inequalities'),
        array('SameType', 'x>2', 'x>1', 1, '', ''),
        array('SameType', 'x>1', 'x>=1', 1, '', ''),
        array('SameType', 'x>1 and x<3', 'x>=1', 1, '', ''),
        array('SameType', '{x>1,x<3}', 'x>=1', 0, '', ''),

        array('SysEquiv', '[90=v*t,90=(v+5)*(t-1/4)]', '[90=v*t,90=(v+5)*(t-1/4)]', 1, '', ''),
        array('SysEquiv', '[d=90,d=v*t,d=(v+5)*(t-1/4)]', '[90=v*t,90=(v+5)*(t-1/4)]', 1, '', ''),
        array('SysEquiv', '1', '[90=v*t,90=(v+5)*(t-1/4)]', 0, '', ''),
        array('SysEquiv', '[90=v*t,90=(v+5)*(t-1/4)]', '1', 0, '', ''),
        array('SysEquiv', '[1]', '[90=v*t,90=(v+5)*(t-1/4)]', 0, '', ''),
        array('SysEquiv', '[90=v*t,90=(v+5)*(t-1/4)]', '[1]', 0, '', ''),
        array('SysEquiv', '[90=v*t^t,90=(v+5)*(t-1/4)]', '[90=v*t,90=(v+5)*(t-1/4)]', 0, '', ''),
        array('SysEquiv', '[90=v*t,90=(v+5)*(t-1/4)]', '[90=v*t^t,90=(v+5)*(t-1/4)]', 0, '', ''),
        array('SysEquiv', '[90=v*t,90=(v+5)*(t*x-1/4)]', '[90=v*t,90=(v+5)*(t-1/4)]', 0, '', ''),
        array('SysEquiv', '[90=v*t,90=(v+5)*(t-1/4)]', '[90=v*t,90=(v+5)*(t*x-1/4)]', 0, '', ''),
        array('SysEquiv', '[90=v*t]', '[90=v*t,90=(v+5)*(t-1/4)]', 0, '', ''),
        array('SysEquiv', '[90=v*t,90=(v+5)*(t-1/4),90=(v+6)*(t-1/5)]', '[90=v*t,90=(v+5)*(t-1/4)]', 0, '', ''),
        array('SysEquiv', '[90=v*t,90=(v+5)*(t-1/4),90=(v+6)*(t-1/5),90=(v+7)*(t-1/4),90=(v+8)*(t-1/3)]',
            '[90=v*t,90=(v+5)*(t-1/4)]', 0, '', ''),

        array('Expanded', '1/0', '0', -1, '', ''),
        array('Expanded', 'x>2', 'x^2-2*x+1', 0, '', ''),
        array('Expanded', 'x^2-1', '0', 1, '', ''),
        array('Expanded', '2*(x-1)', '0', 0, '', ''),
        array('Expanded', '(x-1)*(x+1)', '0', 0, '', ''),
        array('Expanded', '(x-a)*(x-b)', '0', 0, '', ''),
        array('Expanded', 'x^2-(a+b)*x+a*b', '0', 0, '', ''),
        array('Expanded', 'x^2-a*x-b*x+a*b', '0', 1, '', ''),
        array('Expanded', 'cos(2*x)', '0', 1, '', ''),

        // Factored form.
        array('FacForm', '1/0', '0', -1, 'x', ''),
        array('FacForm', '0', '1/0', -1, 'x', ''),
        array('FacForm', '0', '0', -1, '1/0', ''),
        array('FacForm', '2', '2', 1, 'x', 'Trivial cases'),
        array('FacForm', '1/3', '1/3', 1, 'x', ''),
        array('FacForm', '3*x^2', '3*x^2', 1, 'x', ''),
        array('FacForm', '4*x^2', '4*x^2', 1, 'x', ''),
        array('FacForm', '2*(x-1)', '2*x-2', 1, 'x', 'Linear integer factors'),
        array('FacForm', '2*x-2', '2*x-2', 0, 'x', ''),
        array('FacForm', '2*(x+1)', '2*x-2', 0, 'x', ''),
        array('FacForm', '2*x+2', '2*x-2', 0, 'x', ''),
        array('FacForm', '2*(x+0.5)', '2*x+1', 1, 'x', ''),
        array('FacForm', 't*(2*x+1)', 't*(2*x+1)', 1, 'x', 'Linear factors'),
        array('FacForm', 't*x+t', 't*(x+1)', 0, 'x', ''),
        array('FacForm', '2*x*(x-3)', '2*x^2-6*x', 1, 'x', 'Quadratic, with no const'),
        array('FacForm', '2*(x^2-3*x)', '2*x*(x-3)', 0, 'x', ''),
        array('FacForm', 'x*(2*x-6)', '2*x*(x-3)', 0, 'x', ''),
        array('FacForm', '(x+2)*(x+3)', '(x+2)*(x+3)', 1, 'x', 'Quadratic'),
        array('FacForm', '(x+2)*(2*x+6)', '2*(x+2)*(x+3)', 0, 'x', ''),
        array('FacForm', '(z*x+z)*(2*x+6)', '2*z*(x+1)*(x+3)', 0, 'x', ''),
        array('FacForm', '(x+t)*(x-t)', 'x^2-t^2', 1, 'x', ''),
        array('FacForm', 't^2-1', '(t-1)*(t+1)', 0, 't', ''),
        array('FacForm', '(2-x)*(3-x)', '(x-2)*(x-3)', 1, 'x', 'These are delicate cases!'),
        array('FacForm', '(1-x)^2', '(x-1)^2', 1, 'x', ''),
        array('FacForm', '-(1-x)^2', '-(x-1)^2', 1, 'x', ''),
        array('FacForm', '4*(1-x/2)^2', '(x-2)^2', 1, 'x', ''),
        array('FacForm', '(x-1)*(x^2+x+1)', 'x^3-1', 1, 'x', 'Cubics'),
        array('FacForm', 'x^3-x+1', 'x^3-x+1', 1, 'x', ''),
        array('FacForm', '7*x^3-7*x+7', '7*(x^3-x+1)', 0, 'x', ''),
        array('FacForm', '(1-x)*(2-x)*(3-x)', '-x^3+6*x^2-11*x+6', 1, 'x', ''),
        array('FacForm', '(2-x)*(2-x)*(3-x)', '-x^3+7*x^2-16*x+12', 1, 'x', ''),
        array('FacForm', '(2-x)^2*(3-x)', '-x^3+7*x^2-16*x+12', 1, 'x', ''),
        array('FacForm', '(x^2-4*x+4)*(3-x)', '-x^3+7*x^2-16*x+12', 0, 'x', ''),
        array('FacForm', '(x^2-3*x+2)*(3-x)', '-x^3+6*x^2-11*x+6', 0, 'x', ''),
        array('FacForm', '3*y^3-6*y^2-24*y', '3*(y-4)*y*(y+2)', 0, 'y', ''),
        array('FacForm', '3*(y^3-2*y^2-8*y)', '3*(y-4)*y*(y+2)', 0, 'y', ''),
        array('FacForm', '3*y*(y^2-2*y-8)', '3*(y-4)*y*(y+2)', 0, 'y', ''),
        array('FacForm', '3*(y^2-4*y)*(y+2)', '3*(y-4)*y*(y+2)', 0, 'y', ''),
        array('FacForm', '(y-4)*y*(3*y+6)', '3*(y-4)*y*(y+2)', 0, 'y', ''),
        array('FacForm', '(sin(x)+1)*(sin(x)-1)', 'sin(x)^2-1', 1, 'sin(x)', 'Not polynomials in a variable'),
        array('FacForm', '(cos(t)-sqrt(2))^2', 'cos(t)^2-2*sqrt(2)*cos(t)+2', 1, 'cos(t)', ''),
        array('FacForm', '7', '7', 1, 'x', ''),
        array('FacForm', '24*(x-1/4)', '24*x-6', 1, 'x', 'Factors over other fields'),
        array('FacForm', '(x-sqrt(2))*(x+sqrt(2))', 'x^2-2', 1, 'x', ''),
        array('FacForm', '(%i*x-2*%i)', '%i*(x-2)', 0, 'x', ''),
        array('FacForm', '%i*(x-2)', '(%i*x-2*%i)', 1, 'x', ''),
        array('FacForm', '(x-%i)*(x+%i)', 'x^2+1', 1, 'x', ''),
        array('FacForm', '(x-1)*(x+(1+sqrt(3)*%i)/2)*(x+(1-sqrt(3)*%i)/2)', 'x^3-1', 1, 'x', ''),

        array('CompSquare', '1/0', '0', -1, '', ''),
        array('CompSquare', '1/0', '0', -1, 'x', ''),
        array('CompSquare', '0', '1/0', -1, 'x', ''),
        array('CompSquare', '0', '0', -1, '1/0', ''),
        array('CompSquare', '1', '(x-1)^2+1', 0, 'x', 'Category errors.'),
        array('CompSquare', '(t-1)^2+1', '(x-1)^2+1', 0, 'x', ''),
        array('CompSquare', '(x-1)^2+1=0', '(x-1)^2+1', 0, 'x', ''),
        array('CompSquare', 'sin(x-1)+a-1', '(x-1)^2+1', 0, 'x', ''),
        array('CompSquare', '1', '1', 1, 'x', 'Trivial cases'),
        array('CompSquare', 'x-a', 'x-a', 1, 'x', ''),
        array('CompSquare', 'x^2', 'x^2', 1, 'x', ''),
        array('CompSquare', 'x^2-1', '(x-1)*(x+1)', 1, 'x', ''),
        array('CompSquare', '(x-1)^2*k', '(x-1)^2*k', 1, 'x', ''),
        array('CompSquare', '(x-1)^2/k', '(x-1)^2/k', 1, 'x', ''),
        array('CompSquare', '(x-1)^2+1', '(x-1)^2+1', 1, 'x', 'Normal cases'),
        array('CompSquare', '(X-1)^2+1', '(x-1)^2+1', 0, 'x', ''),
        array('CompSquare', '9*(x-1)^2+1', '(3*x-3)^2+1', 1, 'x', ''),
        array('CompSquare', '(x-1)^2+1', '(x+1)^2+1', 0, 'x', ''),
        array('CompSquare', '(x-a^2)^2+1+b', '(x-a^2)^2+1+b', 1, 'x', ''),
        array('CompSquare', 'x^2-2*x+2', '(x-1)^2+1', 0, 'x', ''),
        array('CompSquare', 'x+1', '(x-1)^2+1', 0, 'x', ''),
        array('CompSquare', 'a*(x-1)^2+1', 'a*(x-1)^2+1', 1, 'x', ''),
        array('CompSquare', '(sin(x)-1)^2+1', '(sin(x)-1)^2+1', 1, 'sin(x)', 'Not simple variable'),
        array('CompSquare', '(x^2-1)^2+1', '(x^2-1)^2+1', 1, 'x^2', ''),
        array('CompSquare', '(y-1)^2+1', '(y-1)^2+1', 1, 'y', ''),
        array('CompSquare', '(y+1)^2+1', '(y-1)^2+1', 0, 'y', ''),
        array('CompSquare', '(x-1)^2+1', '(sin(x)-1)^2+1', 0, 'sin(x)', ''),

        // Single Fraction Test.
        array('SingleFrac', '1/0', '1/n', -1, '', ''),
        array('SingleFrac', '0', '1/0', -1, '', ''),
        array('SingleFrac', 'x=3', '2', 0, '', ''),
        array('SingleFrac', '3', '3', 1, '', ''),
        array('SingleFrac', '3', '2', 0, '', ''),
        array('SingleFrac', '1/m', '1/n', 0, '', ''),
        array('SingleFrac', '1/n', '1/n', 1, '', ''),
        array('SingleFrac', 'a+1/2', '(2*a+1)/2', 0, '', ''),
        array('SingleFrac', '1/n +2/(n+1)', '(3*n+1)/(n*(n+1))', 0, '', ''),
        array('SingleFrac', '2*(1/n)', '2/n', 0, '', '2 subtly different answers for the same question'),
        array('SingleFrac', '2/n', '2/n', 1, '', ''),
        array('SingleFrac', '2/(n+1)', '1/(n+1)', 0, '', 'Simple Mistakes'),
        array('SingleFrac', '(2*n+1)/(n+2)', '1/n', 0, '', ''),
        array('SingleFrac', '(2*n)/(n*(n+2))', '(2*n)/(n*(n+3))', 0, '', ''),
        array('SingleFrac', '(x-1)/(x^2-1)', '1/(x+1)', 1, '', ''),
        array('SingleFrac', '(1/2)/(3/4)', '2/3', 0, '', 'Fractions within fractions'),
        array('SingleFrac', '(x-2)/4/(2/x^2)', '(x-2)*x^2/8', 0, '', ''),
        array('SingleFrac', '1/(1-1/x)', 'x/(x-1)', 0, '', ''),

        array('PartFrac', '1/0', '3*x^2', -1, '', ''),
        array('PartFrac', '1/0', '3*x^2', -1, 'x', ''),
        array('PartFrac', '0', '0', -1, '1/0', ''),
        array('PartFrac', '0', '1/0', -1, 'x', ''),
        array('PartFrac', '1/m', '1/n', 0, 'n', 'Basic tests'),
        array('PartFrac', '1/n', '1/n', 1, 'n', ''),
        array('PartFrac', '1/(n+1)-1/n', '1/(n+1)-1/n', 1, 'n', 'A simple cases, linear factors in denominator'),
        array('PartFrac', '1/(n+1)+1/(1-n)', '1/(n+1)-1/(n-1)', 1, 'n', ''),
        array('PartFrac', '1/(2*(n-1))-1/(2*(n+1))', '1/((n-1)*(n+1))', 1, 'n', ''),
        array('PartFrac', '1/(2*(n+1))-1/(2*(n-1))', '1/((n-1)*(n+1))', 0, 'n', ''),
        array('PartFrac', '1/(x-1)-(x+1)/(x^2+1)', '2/((x-1)*(x^2+1))', 1, 'x', 'Irreducible quadratic in denominator'),
        array('PartFrac', '1/(2*x-2)-(x+1)/(2*(x^2+1))', '1/((x-1)*(x^2+1))', 1, 'x', ''),
        array('PartFrac', '1/(2*(x-1))+x/(2*(x^2+1))', '1/((x-1)*(x^2+1))', 0, 'x', ''),
        array('PartFrac', '3/(x+1) + 3/(x+2)', '3*(2*x+3)/((x+1)*(x+2))', 1, 'x', '2 answers to the same question'),
        array('PartFrac', '3*(1/(x+1) + 1/(x+2))', '3*(2*x+3)/((x+1)*(x+2))', 1, 'x', ''),
        array('PartFrac', '3*x*(1/(x+1) + 2/(x+2))', '-12/(x+2)-3/(x+1)+9', 0, 'x',
            'Algebraically equivalent, but numerators of same order than denominator, ie not in partial fraction form.'),
        array('PartFrac', '(3*x+3)*(1/(x+1) + 2/(x+2))', '9-6/(x+2)', 0, 'x', ''),
        array('PartFrac', 'n/(2*n-1)-(n+1)/(2*n+1)', '1/(4*n-2)-1/(4*n+2)', 0, 'n', ''),
        array('PartFrac', '10/(x+3) - 2/(x+2) + x -2', '(x^3 + 3*x^2 + 4*x +2)/((x+2)*(x+3))',
            1, 'x', 'Correct Answer, Numerator > Denominator'),
        array('PartFrac', '2*x+1/(x+1)+1/(x-1)', '2*x^3/(x^2-1)', 1, 'x', ''),
        array('PartFrac', '1/(n*(n-1))', '1/(n*(n-1))', 0, 'n', 'Simple mistakes'),
        array('PartFrac', '1/(n-1)-1/n^2', '1/((n+1)*n)', 0, 'n', ''),
        array('PartFrac', '1/(n-1)-1/n', '1/(n-1)+1/n', 0, 'n', ''),
        array('PartFrac', '1/(x+1)-1/x', '1/(x-1)+1/x', 0, 'x', ''),
        array('PartFrac', '1/(n*(n+1))+1/n', '2/n-1/(n+1)', 0, 'n', ''),
        array('PartFrac', '2/(x+1)-1/(x+2)', 's/((s+1)*(s+2))', 0, 's', 'Different Variables'),
        array('PartFrac', 's/((s+1)^2) + s/(s+2) - 1/(s+1)', 's/((s+1)*(s+2))', 0, 's', 'Too many parts in the partial fraction'),
        array('PartFrac', 's/(s+2) - 1/(s+1)', 's/((s+1)*(s+2)*(s+3))', 0, 's', 'Too few parts in the partial fraction'),
        array('PartFrac', '1/(x+1) + 1/(x+2)', '2/(x+1) + 1/(x+2)', 0, 'x', 'Addition and Subtraction errors'),
        array('PartFrac', '1/(x+1) + 1/(x+2)', '1/(x+1) + 2/(x+2)', 0, 'x', ''),
        array('PartFrac', '1/(x+1) + 1/(x+2)', '1/(x+3) + 1/(x+2)', 0, 'x', 'Denominator Error'),
        array('PartFrac', '(2*x+1)/(x^2+1)-2/(x-1)', '(2*x+1)/(x^2+1)-2/(x-1)', 1, 'x', ''),
        array('PartFrac', '(-5/(x+3))+(16/(x+3)^2)-(2/(x+2))+4', '(-5/(x+3))+(16/(x+3)^2)-(2/(x+2))+4', 1, 'x', ''),
        array('PartFrac', '(3*x^2-5)/((x-4)^2*x)', '(3*x^2-5)/((x-4)^2*x)', 0, 'x', 'Cubic in the denominator'),
        array('PartFrac', '-4/(16*x)+53/(16*(x-4))+43/(4*(x-4)^2)', '(3*x^2-5)/((x-4)^2*x)', 0, 'x', ''),
        array('PartFrac', '-5/(16*x)+53/(16*(x-4))+43/(4*(x-4)^2)', '(3*x^2-5)/((x-4)^2*x)', 1, 'x', ''),
        array('PartFrac', '(5*x+6)/((x+1)*(x+5)^2)', '(5*x+6)/((x+1)*(x+5)^2)', 0, 'x', ''),
        array('PartFrac', '-1/(16*(x+5))+19/(4*(x+5)^2)+1/(16*(x+1))', '(5*x+6)/((x+1)*(x+5)^2)', 1, 'x', ''),
        array('PartFrac', '5/(x*(x+3)*(5*x-2))', '5/(x*(x+3)*(5*x-2))', 0, 'x', ''),
        array('PartFrac', '125/(34*(5*x-2))+5/(51*(x+3))-5/(6*x)', '5/(x*(x+3)*(5*x-2))', 1, 'x', ''),
        array('PartFrac', '(3*x^2-5)/((4*x-4)^2*x)', '(3*x^2-5)/((4*x-4)^2*x)', 0, 'x', ''),
        array('PartFrac', '-4/(16*x)+1/(2*(x-1))-1/(8*(x-1)^2)', '(3*x^2-5)/((4*x-4)^2*x)', 0, 'x', ''),
        array('PartFrac', '-5/(16*x)+1/(2*(x-1))-1/(8*(x-1)^2)', '(3*x^2-5)/((4*x-4)^2*x)', 1, 'x', ''),

            // Differentiation test.
        array('Diff', '1/0', '3*x^2', -1, '', ''),
        array('Diff', '0', '1/0', -1, '(x', ''),
        array('Diff', '1/0', '3*x^2', -1, 'x', ''),
        array('Diff', '0', '1/0', -1, 'x', ''),
        array('Diff', '0', '0', -1, '1/0', ''),
        array('Diff', '3*x^2', '3*x^2', 1, 'x', 'Basic tests'),
        array('Diff', '3*X^2', '3*x^2', 0, 'x', ''),
        array('Diff', 'x^4/4', '3*x^2', 0, 'x', ''),
        array('Diff', 'x^4/4+1', '3*x^2', 0, 'x', ''),
        array('Diff', 'x^4/4+c', '3*x^2', 0, 'x', ''),
        array('Diff', 'y=x^4/4', 'x^4/4', 0, 'x', ''),
        array('Diff', 'x^4/4', 'y=x^4/4', 0, 'x', ''),
        array('Diff', 'y=x^4/4', 'y=x^4/4', 0, 'x', ''),
        array('Diff', 'y^2-2*y+1', 'x^2-2*x+1', 0, 'x', 'Variable mismatch tests'),
        array('Diff', 'x^2-2*x+1', 'y^2-2*y+1', 0, 'x', ''),
        array('Diff', 'y^2+2*y+1', 'x^2-2*x+1', 0, 'z', ''),
        array('Diff', 'x^4/4', '3*x^2', 0, 'y', ''),

        // Integration test.
        array('Int', '1/0', '1', -1, '', ''),
        array('Int', '1/0', '1', -1, 'x', ''),
        array('Int', '1', '1/0', -1, 'x', ''),
        array('Int', '0', '0', -1, '1/0', ''),
        array('Int', 'x^3/3', 'x^3/3', 0, 'x', 'Basic tests'),
        array('Int', 'x^3/3+1', 'x^3/3', 0, 'x', ''),
        array('Int', 'x^3/3+c', 'x^3/3', 1, 'x', ''),
        array('Int', 'x^3/3-c', 'x^3/3', 1, 'x', ''),
        array('Int', 'x^3/3+c+k', 'x^3/3', 0, 'x', ''),
        array('Int', 'x^3/3+c^2', 'x^3/3', 0, 'x', ''),
        array('Int', 'x^3/3*c', 'x^3/3', 0, 'x', ''),
        array('Int', 'X^3/3+c', 'x^3/3', 0, 'x', ''),
        array('Int', 'sin(2*x)', 'x^3/3', 0, 'x', ''),
        array('Int', 'x^2/2-2*x+2+c', '(x-2)^2/2', 1, 'x', ''),
        array('Int', '(t-1)^5/5+c', '(t-1)^5/5', 1, 't', ''),
        array('Int', 'cos(2*x)/2+1+c', 'cos(2*x)/2', 1, 'x', ''),
        array('Int', 'x^3/3+c', 'x^3/3+c', 1, 'x', 'The teacher adds a constant'),
        array('Int', 'x^2/2-2*x+2+c', '(x-2)^2/2+k', 1, 'x', ''),
        array('Int', 'exp(x)+c', 'exp(x)', 1, 'x', 'Special case'),
        array('Int', 'exp(x)', 'exp(x)', 0, 'x', ''),
        array('Int', '2*x', 'x^3/3', 0, 'x', 'Student differentiates by mistake'),
        array('Int', '2*x+c', 'x^3/3', 0, 'x', ''),
        array('Int', 'ln(x)', 'ln(x)', 0, 'x', 'Sloppy logs (teacher ignores abs(x) )'),
        array('Int', 'ln(x)+c', 'ln(x)+c', 1, 'x', ''),
        array('Int', 'ln(k*x)', 'ln(x)+c', 1, 'x', ''),
        array('Int', 'ln(x)', 'ln(abs(x))+c', 0, 'x', 'Fussy logs (teacher uses abs(x) )'),
        array('Int', 'ln(x)+c', 'ln(abs(x))+c', 0, 'x', ''),
        array('Int', 'ln(abs(x))', 'ln(abs(x))+c', 0, 'x', ''),
        array('Int', 'ln(abs(x))+c', 'ln(abs(x))+c', 1, 'x', ''),
        array('Int', 'ln(k*x)', 'ln(abs(x))+c', 0, 'x', ''),
        array('Int', 'ln(k*abs(x))', 'ln(abs(x))+c', 1, 'x', ''),
        array('Int', 'ln(abs(k*x))', 'ln(abs(x))+c', 1, 'x', ''),
        array('Int', 'ln(x)', 'ln(k*abs(x))', 0, 'x', 'Teacher uses ln(k*abs(x))'),
        array('Int', 'ln(x)+c', 'ln(k*abs(x))', 0, 'x', ''),
        array('Int', 'ln(abs(x))', 'ln(k*abs(x))', 0, 'x', ''),
        array('Int', 'ln(abs(x))+c', 'ln(k*abs(x))', 1, 'x', ''),
        array('Int', 'ln(k*x)', 'ln(k*abs(x))', 0, 'x', ''),
        array('Int', 'ln(k*abs(x))', 'ln(k*abs(x))', 1, 'x', ''),
        array('Int', 'ln(x)+ln(a)', 'ln(k*abs(x+a))', 0, 'x', 'Other logs'),
        array('Int', 'log(x)^2-2*log(c)*log(x)+k', 'ln(c/x)^2', 0, 'x', ''),
        array('Int', 'log(x)^2-2*log(c)*log(x)+k', 'ln(abs(c/x))^2', 0, 'x', ''),
        array('Int', '2*sin(x)*cos(x)', 'sin(2*x)+c', 0, 'x', 'Trig'),
        array('Int', '2*sin(x)*cos(x)+k', 'sin(2*x)+c', 1, 'x', ''),
        array('Int', '-2*cos(3*x)/3-3*cos(2*x)/2', '-2*cos(3*x)/3-3*cos(2*x)/2+c', 0, 'x', ''),
        array('Int', '-2*cos(3*x)/3-3*cos(2*x)/2+1', '-2*cos(3*x)/3-3*cos(2*x)/2+c', 0, 'x', ''),
        array('Int', '-2*cos(3*x)/3-3*cos(2*x)/2+c', '-2*cos(3*x)/3-3*cos(2*x)/2+c', 1, 'x', ''),
        array('Int', '(tan(2*t)-2*t)/2',
            '-(t*sin(4*t)^2-sin(4*t)+t*cos(4*t)^2+2*t*cos(4*t)+t)/(sin(4*t)^2+cos(4*t)^2+2*cos(4*t)+1)', 0, 't', ''),
        array('Int', '(tan(2*t)-2*t)/2+1',
            '-(t*sin(4*t)^2-sin(4*t)+t*cos(4*t)^2+2*t*cos(4*t)+t)/(sin(4*t)^2+cos(4*t)^2+2*cos(4*t)+1)', 0, 't', ''),
        array('Int', '(tan(2*t)-2*t)/2+c',
            '-(t*sin(4*t)^2-sin(4*t)+t*cos(4*t)^2+2*t*cos(4*t)+t)/(sin(4*t)^2+cos(4*t)^2+2*cos(4*t)+1)', 1, 't', ''),
        array('Int', 'tan(x)-x+c', 'tan(x)-x', 1, 'x', ''),
        array('Int', '2/3*sqrt(3)*(atan(sin(x)/(sqrt(3)*(cos(x)+1)))-(atan(sin(x)/(cos(x)+1))))+x/sqrt(3)',
            '2*atan(sin(x)/(sqrt(3)*(cos(x)+1)))/sqrt(3)', 0, 'x', 'Stoutemyer'),

        array('GT', '1/0', '1', -1, '', ''),
        array('GT', '1', '1/0', -1, '', ''),
        array('GT', '1', '1', 0, '', ''),
        array('GT', '2', '1', 1, '', ''),
        array('GT', '1', '2.1', 0, '', ''),
        array('GT', 'pi', '3', 1, '', ''),
        array('GT', 'pi+2', '5', 1, '', ''),
        array('GT', '-inf', '0', 0, '', 'Infinity'),
        array('GT', 'inf', '0', 0, '', ''),

        array('GTE', '1/0', '1', -1, '', ''),
        array('GTE', '1', '1/0', -1, '', ''),
        array('GTE', '1', '1', 1, '', ''),
        array('GTE', '2', '1', 1, '', ''),
        array('GTE', '1', '2.1', 0, '', ''),
        array('GTE', 'pi', '3', 1, '', ''),
        array('GTE', 'pi+2', '5', 1, '', ''),

        array('NumRelative', '1/0', '0', -1, '', 'Basic tests'),
        array('NumRelative', '0', '1/0', -1, '', ''),
        array('NumRelative', '0', '0', -1, '1/0', ''),
        array('NumRelative', '0', '(x', -1, '', ''),
        array('NumRelative', '0', '0', -1, '(x', ''),
        array('NumRelative', '1.1', '1', 0, '', 'No option, so 5%'),
        array('NumRelative', '1.05', '1', 1, '', ''),
        array('NumRelative', '1.05', '1', 1, '0.1', 'Options passed'),
        array('NumRelative', '1.05', '3', 0, '0.1', ''),
        array('NumRelative', '3.14', 'pi', 1, '0.001', ''),
        array('NumRelative', 'inf', '0', 0, '', 'Infinity'),


        array('NumAbsolute', '1/0', '0', -1, '', 'Basic tests'),
        array('NumAbsolute', '0', '1/0', -1, '', ''),
        array('NumAbsolute', '0', '0', -1, '1/0', ''),
        array('NumAbsolute', '0', '(x', -1, '', ''),
        array('NumAbsolute', '0', '0', -1, '(x', ''),
        array('NumAbsolute', '1.1', '1', 0, '', 'No option, so 5%'),
        array('NumAbsolute', '1.05', '1', 1, '', ''),
        array('NumAbsolute', '1.05', '1', 1, '0.1', 'Options passed'),
        array('NumAbsolute', '1.05', '3', 0, '0.1', ''),
        array('NumAbsolute', '3.14', 'pi', 0, '0.001', ''),

        array('NumSigFigs', '3.141', '3.1415927', -1, '', 'Basic tests'),
        array('NumSigFigs', '1/0', '3', -1, '0', ''),
        array('NumSigFigs', '0', '1/0', -1, '0', ''),
        array('NumSigFigs', '0', '0', -1, '1/0', ''),
        array('NumSigFigs', '0', '1', -1, '(', ''),
        array('NumSigFigs', '(', '1', -1, '1', ''),
        array('NumSigFigs', '1', '3', -1, 'pi', ''),
        array('NumSigFigs', '1', '3', -1, '[3,x]', ''),
        array('NumSigFigs', '1', '3', -1, '[1,2,3]', ''),
        array('NumSigFigs', '1', '3', -1, '', ''),
        array('NumSigFigs', '1.234', '4', 0, '1', 'Option is a number'),
        array('NumSigFigs', '3.141', '3.1415927', 0, '3', ''),
        array('NumSigFigs', '3.141', '3.1415927', 0, '4', ''),
        array('NumSigFigs', '3.142', '3.1415927', 1, '4', ''),
        array('NumSigFigs', '3.142', 'pi', 1, '4', ''),
        array('NumSigFigs', 'pi', 'pi', 0, '4', ''),
        array('NumSigFigs', '3141', '3.1415927', 0, '4', ''),
        array('NumSigFigs', '0.00123', '0.001234567', 1, '3', ''),
        array('NumSigFigs', '0.001235', '0.001234567', 1, '4', ''),
        array('NumSigFigs', '0.150', '0.14951', 1, '3', ''),
        array('NumSigFigs', '1000', '999', 1, '2', ''),
        array('NumSigFigs', '-100', '-149', 1, '1', ''),
        array('NumSigFigs', '-0.05', '-0.0499', 1, '1', ''),
        array('NumSigFigs', '3.142', '3.1415927', 1, '[4,3]', 'Mixed options'),
        array('NumSigFigs', '3.143', '3.1415927', 1, '[4,3]', ''),
        array('NumSigFigs', '3.150', '3.1415927', 0, '[4,3]', ''),
        array('NumSigFigs', '3.1416', '3.1415927', 0, '[4,3]', ''),

        array('NumDecPlaces', '3.141', '3.1415927', -1, '', 'Basic tests'),
        array('NumDecPlaces', '1/0', '3', -1, '2', ''),
        array('NumDecPlaces', '0', '1/0', -1, '2', ''),
        array('NumDecPlaces', '0', '0', -1, '1/0', ''),
        array('NumDecPlaces', '0', '1', -1, 'x', ''),
        array('NumDecPlaces', '0', '1', -1, '-1', ''),
        array('NumDecPlaces', '0', '1', -1, '0', ''),
        array('NumDecPlaces', '0', '1', -1, '(', ''),
        array('NumDecPlaces', '(', '1', -1, '1', ''),
        array('NumDecPlaces', 'x', '3.143', 0, '2', "Student's answer not a floating point number"),
        array('NumDecPlaces', '3', '3.000', 0, '3', ''),
        array('NumDecPlaces', '3.14', '3.143', 1, '2', 'Right number of places'),
        array('NumDecPlaces', '3.14', '3.14', 1, '2', ''),
        array('NumDecPlaces', '3.140', '3.140', 1, '3', ''),
        array('NumDecPlaces', '3141.5972', '3141.5972', 1, '4', ''),
        array('NumDecPlaces', '4.14', '3.14', 0, '2', ''),
        array('NumDecPlaces', '3.1416', 'pi', 1, '4', ''),
        array('NumDecPlaces', '-7.3', '-7.3', 1, '1', ''),
        array('NumDecPlaces', '3.14', '3.143', 0, '1', 'Wrong number of places'),
        array('NumDecPlaces', '3.14', '3.143', 0, '1', ''),
        array('NumDecPlaces', '3.14', '3.140', 0, '3', ''),
        array('NumDecPlaces', '7.000', '7', 0, '4', ''),

        array('String', 'Hello', 'hello', 0, '', ''),
        array('String', 'hello', 'hello', 1, '', ''),
        array('String', 'hello', 'heloo', 0, '', ''),

        array('StringSloppy', 'hello', 'Hello', 1, '', ''),
        array('StringSloppy', 'hel lo', 'Hello', 1, '', ''),
        array('StringSloppy', 'hello', 'heloo', 0, '', ''),

        array('RegExp', '3.1415927', '3.1415927', -1, '', ''),
        array('RegExp', '3.1415927', '3.1415927', 1, '{[0-9]*\.[0-9]*}', ''),
        array('RegExp', 'cxcxcz', '3.1415927', 0, '{[0-9]*\.[0-9]*}', ''),

        array('LowestTerms', '1/0', '0', -1, '', ''),
        array('LowestTerms', '0.5', '0', 1, '', 'Mix of floats and rational numbers'),
        array('LowestTerms', '0.33', '0', 1, '', ''),
        array('LowestTerms', '2/4', '0', 0, '', ''),
        array('LowestTerms', '-1/3', '0', 1, '', 'Negative numbers'),
        array('LowestTerms', '1/-3', '0', 1, '', ''),
        array('LowestTerms', '-2/4', '0', 0, '', ''),
        array('LowestTerms', '2/-4', '0', 0, '', ''),
        array('LowestTerms', 'x+1/3', '0', 1, '', 'Polynomials'),
        array('LowestTerms', 'x+2/6', '0', 0, '', ''),
        array('LowestTerms', '2*x/4+2/6', '0', 0, '', ''),
        array('LowestTerms', '2/4*x+2/6', '0', 0, '', ''),
        array('LowestTerms', 'cos(x)', '0', 1, '', 'Trig functions'),
        array('LowestTerms', 'cos(3/6*x)', '0', 0, '', ''),
        array('LowestTerms', 'matrix([1,2/4],[2,3])', '0', 0, '', 'Matrices'),

        array('LowestTerms', 'x=1/2', '0', 1, '', 'Equations'),
        array('LowestTerms', '3/9=x', '0', 0, '', ''),
    );

    public static function get_raw_test_data() {
        return self::$rawdata;
    }

    public static function get_available_tests() {
        $availabletests = array();
        foreach (self::$rawdata as $test) {
            $availabletests[$test[self::NAME]] = $test[self::NAME];
        }
        return $availabletests;
    }

    public static function test_from_raw($data) {
        $test = new stdClass();
        $test->name          = $data[self::NAME];
        $test->studentanswer = $data[self::SANS];
        $test->teacheranswer = $data[self::TANS];
        $test->expectedscore = $data[self::SCORE];
        $test->options       = $data[self::OPTIONS];
        $test->notes         = $data[self::NOTES];
        return $test;
    }

    public static function get_all() {
        $tests = array();
        foreach (self::$rawdata as $data) {
            $tests[] = self::test_from_raw($data);
        }
        return $tests;
    }

    public static function get_tests_for($anstest) {
        $tests = array();
        foreach (self::$rawdata as $data) {
            if ($data[self::NAME] == $anstest) {
                $tests[] = self::test_from_raw($data);
            }
        }
        return $tests;
    }

    public static function run_test($test) {
        $anst = new stack_ans_test_controller($test->name, $test->studentanswer,
                $test->teacheranswer, new stack_options(), $test->options);

        $result   = $anst->do_test(); // This actually executes the answer test in the CAS.
        $errors   = $anst->get_at_errors();
        $rawmark  = $anst->get_at_mark();
        $feedback = $anst->get_at_feedback();
        $ansnote  = $anst->get_at_answernote();

        $passed = false;
        if ($rawmark === $test->expectedscore) {
            $passed = true;
        }

        // The test failed, and we expected it to fail.
        if ($errors === 'TEST_FAILED') {
            if (-1 === $test->expectedscore) {
                $passed = true;
            } else {
                $passed = false;
            }
        }

            return array($passed, $errors, $rawmark, $feedback, $ansnote);
    }
}
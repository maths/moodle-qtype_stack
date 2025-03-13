<?php
// This file is part of Stack - http://stack.maths.ed.ac.uk/
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

defined('MOODLE_INTERNAL') || die();

require_once('equivfixtures.class.php');

class stack_answertest_test_data {
    const NAME    = 0;
    const OPTIONS = 1;
    const SANS    = 2;
    const TANS    = 3;
    const SCORE   = 4;
    const ANSNOTE = 5;
    const NOTES   = 6;

    /**
     * Raw data should be in the following form (matching the constants above).
     * Name of test
     * Options
     * Student's answer
     * Teacher's answer
     * Expected result
     *    0 = different
     *    1 = same
     *    -1 = "Test fails", but this is trapped.
     *    -2 = Expected maths failure, where test should return 0.
     *    -3 = Expected maths failure, where test should return 1.
     * Answer note(s)
     * Comments on this test.
     * Header row in the table (optional).
     */
    protected static $rawdata = [

        ['AlgEquiv', '', '1/0', '1', -1, 'ATAlgEquiv_STACKERROR_SAns.', ''],
        ['AlgEquiv', '', '1', '1/0', -1, 'ATAlgEquiv_STACKERROR_TAns.', ''],
        ['AlgEquiv', '', '', '(x-1)^2', -1, 'ATAlgEquivTEST_FAILED-Empty SA.', ''],
        ['AlgEquiv', '', 'x^2', '', -1, 'ATAlgEquivTEST_FAILED-Empty TA.', ''],
        ['AlgEquiv', '', 'x-1)^2', '(x-1)^2', -1, 'ATAlgEquivTEST_FAILED-Empty SA.', ''],

        // Make this behaviour explict.
        ['AlgEquiv', '', 'x1', 'x_1', 0, '', 'See docs on subscripts and different atoms.'],
        ['AlgEquiv', '', 'x_1', 'x[1]', 0, '', ''],
        ['AlgEquiv', '', 'x[1]', 'x1', 0, '', ''],
        ['AlgEquiv', '', 'integerp(3)', 'true', 1, 'ATLogic_True.', 'Predicates'],
        ['AlgEquiv', '', 'integerp(3.1)', 'true', 0, '', ''],
        ['AlgEquiv', '', 'integerp(3)', 'false', 0, '', ''],
        ['AlgEquiv', '', 'integerp(3)', 'true', 1, 'ATLogic_True.', ''],
        // Note that AlgEquiv simplifies its arguments.  Use a non-simplifying test instead.
        ['AlgEquiv', '', 'lowesttermsp(x^2/x)', 'true', 1, 'ATLogic_True.', ''],
        ['AlgEquiv', '', 'lowesttermsp(-y/-x)', 'true', 1, 'ATLogic_True.', ''],
        ['AlgEquiv', '', 'lowesttermsp((x^2-1)/(x-1))', 'true', 0, '', ''],
        ['AlgEquiv', '', 'lowesttermsp((x^2-1)/(x+2))', 'true', 1, 'ATLogic_True.', ''],
        ['AlgEquiv', '', 'scientific_notationp(4.1561*10^16)', 'true', 0, '', ''],
        ['AlgEquiv', '', 'X', 'x', 0, 'ATAlgEquiv_WrongCase.', 'Case sensitivity'],
        ['AlgEquiv', '', '1/(R-r)', '1', 0, '', ''],
        ['AlgEquiv', '', 'exdowncase(X)', 'x', 1, '', ''],
        ['AlgEquiv', '', 'exdowncase((X-1)^2)', 'x^2-2*x+1', 1, '', ''],
        [
            'AlgEquiv', '', 'Y=1+X', 'y=1+x', 0, 'ATEquation_default',
            'Permutations of variables (To do: a dedicated answer test with feedback)',
        ],
        ['AlgEquiv', '', 'v+w+x+y+z', 'a+b+c+A+B', 0, '', ''],
        ['AlgEquiv', '', '4^(-1/2)', '1/2', 1, '', 'Numbers'],
        ['AlgEquiv', '', '4^(1/2)', 'sqrt(4)', 1, '', ''],
        ['AlgEquiv', '', '0.5', '1/2', 1, '', 'Mix of floats and rational numbers'],
        ['AlgEquiv', '', '0.33', '1/3', 0, '', ''],
        // It will be interesting to find out on how many versions of Maxima the test below fails!
        ['AlgEquiv', '', '452', '4.52*10^2', 0, '', ''],
        ['AlgEquiv', '', '5.1e-2', '51/1000', 1, '', ''],
        ['AlgEquiv', '', '0.333333333333333', '1/3', 0, '', ''],
        ['AlgEquiv', '', '(0.5+x)*2', '2*x+1', 1, '', ''],
        ['AlgEquiv', '', '0.333333333333333*x^2', 'x^2/3', 0, '', ''],
        ['AlgEquiv', '', '0.1*(2.0*s^2+6.0*s-25.0)/s', '(2*s^2+6*s-25)/(10*s)', 1, '', ''],
        ['AlgEquiv', '', '0.1*(2.0*s^2+6.0*s-25.00001)/s', '(2*s^2+6*s-25)/(10*s)', 0, '', ''],
        // Interesting rounding error.
        ['AlgEquiv', '', '100.4-80.0', '20.4', 0, '', ''],

        ['AlgEquiv', '', 'sqrt(-1)', '%i', 1, '', 'Complex numbers'],
        ['AlgEquiv', '', '%i', 'e^(i*pi/2)', 1, '', ''],
        ['AlgEquiv', '', '(4*sqrt(3)*%i+4)^(1/5)', '8^(1/5)*(cos(%pi/15)+%i*sin(%pi/15))', 1, '', ''],
        ['AlgEquiv', '', '(4*sqrt(3)*%i+4)^(1/5)', 'rectform((4*sqrt(3)*%i+4)^(1/5))', 1, '', ''],
        ['AlgEquiv', '', '(4*sqrt(3)*%i+4)^(1/5)', 'polarform((4*sqrt(3)*%i+4)^(1/5))', 1, '', ''],
        ['AlgEquiv', '', '5/4*%e^(%i*%pi/6)', '5*sqrt(3)/8+5/8*%i', 1, '', ''],
        ['AlgEquiv', '', '%i/sqrt(x)', 'sqrt(-1/x)', 1, '', ''],

        ['AlgEquiv', '', 'inf', 'inf', 1, '', 'Infinity'],
        ['AlgEquiv', '', 'inf', '-inf', 0, '', ''],
        ['AlgEquiv', '', '2*inf', 'inf', 0, '', ''],
        ['AlgEquiv', '', '0*inf', '0', 1, '', ''],
        ['AlgEquiv', '', 'exp(-%i)', 'inf', 0, '', ''],

        ['AlgEquiv', '', 'x^(1/2)', 'sqrt(x)', 1, '', 'Powers and roots'],
        ['AlgEquiv', '', 'x', 'sqrt(x^2)', 0, '', ''],
        ['AlgEquiv', '', '\'root(x)', 'x^(1/2)', 1, '', ''],
        ['AlgEquiv', '', '\'root(x,m)', 'x^(1/m)', 1, '', ''],
        ['AlgEquiv', '', 'x', '\'root(x^2)', 0, '', ''],
        ['AlgEquiv', '', 'abs(x)', 'sqrt(x^2)', 1, '', ''],
        ['AlgEquiv', '', '1/abs(x)^(1/3)', '(abs(x)^(1/3)/abs(x))^(1/2)', 1, '', ''],
        ['AlgEquiv', '', 'sqrt((x-3)*(x-5))', 'sqrt(x-3)*sqrt(x-5)', 0, '', ''],
        ['AlgEquiv', '', '1/sqrt(x)', 'sqrt(1/x)', 1, '', ''],
        ['AlgEquiv', '', 'x-1', '(x^2-1)/(x+1)', 1, '', ''],
        ['AlgEquiv', '', '2^((1/5.1)*t)', '2^((1/5.1)*t)', 1, '', ''],
        ['AlgEquiv', '', '2^((1/5.1)*t)', '2^(0.196078431373*t)', 0, '', ''],
        ['AlgEquiv', '', '1-root(2)', '1-2^(1/2)', 1, '', ''],
        ['AlgEquiv', '', '1-root(2)', '1-sqrt(2)', 1, '', ''],
        ['AlgEquiv', '', 'root(2,2)+1', '1+sqrt(2)', 1, '', ''],
        ['AlgEquiv', '', 'a^b*a^c', 'a^(b+c)', 1, '', ''],
        ['AlgEquiv', '', '(a^b)^c', 'a^(b*c)', 0, '', ''],
        ['AlgEquiv', '', '(assume(a>0),(a^b)^c)', 'a^(b*c)', 1, '', ''],
        ['AlgEquiv', '', '(assume(x>2),6*((x-2)^2)^k)', '6*(x-2)^(2*k)', 1, '', ''],
        ['AlgEquiv', '', 'signum(-3)', '-1', 1, '', ''],
        ['AlgEquiv', '', '6*((x-2)^3)^k', '6*(x-2)^(3*k)', 1, '', ''],
        ['AlgEquiv', '', '(4*sqrt(3)*%i+4)^(1/5)', '6^(1/5)*cos(%pi/15)-6^(1/5)*%i*sin(%pi/15)', 0, '', ''],
        ['AlgEquiv', '', '2+2*sqrt(3+x)', '2+sqrt(12+4*x)', 1, '', ''],
        [
            'AlgEquiv', '', '6*e^(6*(y^2+x^2))+72*x^2*e^(6*(y^2+x^2))',
            '(72*x^2+6)*e^(6*(y^2+x^2))', 1, '', '', '',
        ],
        ['AlgEquiv', '', 'a1', 'a_1', 0, '', 'Expressions with subscripts'],
        ['AlgEquiv', '', 'rho*z*V/(4*pi*epsilon[0]*(R^2+z^2)^(3/2))', 'rho*z*V/(4*pi*epsilon[0]*(R^2+z^2)^(3/2))', 1, '', ''],
        ['AlgEquiv', '', 'rho*z*V/(4*pi*epsilon[1]*(R^2+z^2)^(3/2))', 'rho*z*V/(4*pi*epsilon[0]*(R^2+z^2)^(3/2))', 0, '', ''],
        ['AlgEquiv', '', 'sqrt(k/m)*sqrt(m/k)', '1', 1, '', ''],
        ['AlgEquiv', '', '(2*pi)/(k/m)^(1/2)', '(2*pi)/(k/m)^(1/2)', 1, '', ''],
        ['AlgEquiv', '', '(2*pi)*(m/k)^(1/2)', '(2*pi)/(k/m)^(1/2)', 1, '', ''],
        ['AlgEquiv', '', 'sqrt(2*x/10+1)', 'sqrt((2*x+10)/10)', 1, '', ''],
        ['AlgEquiv', '', '((x+3)^2*(x+3))^(1/3)', '((x+3)*(x^2+6*x+9))^(1/3)', 1, '', ''],
        ['AlgEquiv', '', '((x+3)^2*(x+3))^(1/3)', '((x+3)*(x^2+6*x+9))^(1/3)', 1, '', 'Need to factor internally.'],

        ['AlgEquiv', '', '(x-1)^2', 'x^2-2*x+1', 1, '', 'Polynomials and rational function'],
        ['AlgEquiv', '', '(x-1)*(x^2+x+1)', 'x^3-1', 1, '', ''],
        ['AlgEquiv', '', '(x-1)^(-2)', '1/(x^2-2*x+1)', 1, '', ''],
        ['AlgEquiv', '', '1/(4*x-(%pi+sqrt(2)))', '1/(x+1)', 0, '', ''],
        ['AlgEquiv', '', '(x-a)^6000', '(x-a)^6000', 1, '', ''],
        ['AlgEquiv', '', '(a-x)^6000', '(x-a)^6000', 1, '', ''],
        ['AlgEquiv', '', '(4*a-x)^6000', '(x-4*a)^6000', 1, '', ''],
        ['AlgEquiv', '', '(x-a)^6000', '(x-a)^5999', 0, '', ''],
        ['AlgEquiv', '', '(k+8)/(k^2+4*k-12)', '(k+8)/(k^2+4*k-12)', 1, '', ''],
        ['AlgEquiv', '', '(k+7)/(k^2+4*k-12)', '(k+8)/(k^2+4*k-12)', 0, '', ''],
        ['AlgEquiv', '', '-(2*k+6)/(k^2+4*k-12)', '-(2*k+6)/(k^2+4*k-12)', 1, '', ''],
        ['AlgEquiv', '', '1/n-1/(n+1)', '1/(n*(n+1))', 1, '', ''],
        ['AlgEquiv', '', '1/(a-b)-1/(b-a)', '1/(a-b)+1/(b-a)', 0, '', ''],
        ['AlgEquiv', '', '0.5*x^2+3*x-1', 'x^2/2+3*x-1', 1, '', ''],
        [
            'AlgEquiv', '', '14336000000*x^13+250265600000*x^12+1862860800000*x^11+7623925760000*x^10+' .
            '18290677760000*x^9+24744757985280*x^8+14567212351488*x^7-3267871272960*x^6-6408053107200*x^5+' .
            '670406720000*x^4+1179708800000*x^3-429244800000*x^2+56696000000*x-2680000000',
            '512*(2*x+5)^7*(5*x-1)^5*(70*x+67)', 1, '', '',
        ],
        [
            'AlgEquiv', '', '14336000000*x^13+250265600000*x^12+1862860800000*x^11+7623925760000*x^10+' .
                '18290677760000*x^9+24744757985280*x^8+14567212351488*x^7-3267871272960*x^6-6408053107200*x^5+' .
                '670406720000*x^4+1179708800000*x^3-429244800000*x^2+56696000000*x-2680000001',
            '512*(2*x+5)^7*(5*x-1)^5*(70*x+67)', 0, '', '',
        ],
        ['AlgEquiv', '', '14336000000*x^13', '512*(2*x+5)^7*(5*x-1)^5*(70*x+67)', 0, '', ''],

        ['AlgEquiv', '', 'cos(x)', 'cos(-x)', 1, '', 'Trig functions'],
        ['AlgEquiv', '', 'cos(x)^2+sin(x)^2', '1', 1, '', ''],
        ['AlgEquiv', '', 'cos(x+y)', 'cos(x)*cos(y)-sin(x)*sin(y)', 1, '', ''],
        ['AlgEquiv', '', 'cos(x+y)', 'cos(x)*cos(y)+sin(x)*sin(y)', 0, '', ''],
        ['AlgEquiv', '', 'cos(x#pm#y)', 'cos(x)*cos(y)-(#pm#sin(x)*sin(y))', 1, 'ATLogic_True.', ''],
        ['AlgEquiv', '', 'sin(x#pm#y)', 'sin(x)*cos(y)#pm#cos(x)*sin(y)', 1, 'ATLogic_True.', ''],
        ['AlgEquiv', '', 'sin(x#pm#y)', 'cos(x)*sin(y)#pm#sin(x)*cos(y)', 0, '', ''],
        ['AlgEquiv', '', '2*cos(x)^2-1', 'cos(2*x)', 1, '', ''],
        ['AlgEquiv', '', '1.0*cos(1200*%pi*x)', 'cos(1200*%pi*x)', 1, '', ''],
        ['AlgEquiv', '', 'diff(tan(10*x)^2,x)', 'cos(6*x)', 0, '', ''],
        ['AlgEquiv', '', 'exp(%i*%pi)', '-1', 1, '', ''],
        ['AlgEquiv', '', '2*cos(2*x)+x+1', '-sin(x)^2+3*cos(x)^2+x', 1, '', ''],
        // This caused a trigexpand (for some reason), which led to timeouts in issue #1073.
        ['AlgEquiv', '', '4*x*cos(x^12/%pi)', 'x*cos(x^12/%pi)', 0, '', ''],

        [
            'AlgEquiv', '', '(2*sec(2*t)^2-2)/2',
            '-(sin(4*t)^2-2*sin(4*t)+cos(4*t)^2-1)*(sin(4*t)^2+2*sin(4*t)+cos(4*t)^2-1)/(sin(4*t)^2+cos(4*t)^2+2*cos(4*t)+1)^2',
            1, '', '',
        ],
        ['AlgEquiv', '', '1+cosec(3*x)', '1+csc(3*x)', 1, '', ''],
        ['AlgEquiv', '', '1/(1+exp(-2*x))', 'tanh(x)/2+1/2', 1, '', ''],
        ['AlgEquiv', '', '1+cosech(3*x)', '1+csch(3*x)', 1, '', ''],
        [
            'AlgEquiv', '', '-4*sec(4*z)^2*sin(6*z)-6*tan(4*z)*cos(6*z)',
            '-4*sec(4*z)^2*sin(6*z)-6*tan(4*z)*cos(6*z)', 1, '', '',
        ],
        [
            'AlgEquiv', '', '-4*sec(4*z)^2*sin(6*z)-6*tan(4*z)*cos(6*z)',
            '4*sec(4*z)^2*sin(6*z)+6*tan(4*z)*cos(6*z)', 0, '', '',
        ],
        // The following test is here because we can't factor with trigsimp:true.
        [
            'AlgEquiv', '', 'csc(6*x)^2*(7*sin(6*x)*cos(7*x)-6*cos(6*x)*sin(7*x))',
            '-(6*cos(6*x)*sin(7*x)-7*sin(6*x)*cos(7*x))/sin(6*x)^2', 1, '', '',
        ],
        [
            'AlgEquiv', '', 'csc(6*x)^2*(7*sin(6*x)*cos(7*x)-6*cos(6*x)*sin(7*x))',
            '(6*cos(6*x)*sin(7*x)-7*sin(6*x)*cos(7*x))/sin(6*x)^2', 0, '', '',
        ],
        [
            'AlgEquiv', '', '-(7*x^6+4*x^3)/sin(7*y+x^7+x^4+1)^2',
            '-(7*x^6+4*x^3)*csc(7*y+x^7+x^4+1)^2', 1, '', '',
        ],
        ['AlgEquiv', '', 'sin((2*%pi*n-%pi)/2)', '-cos(n*%pi)', 1, '', ''],
        ['AlgEquiv', '', 'sin(x/2)/(1+tan(x)*tan(x/2))', 'sin(x/2)*cos(x)', 1, '', ''],
        ['AlgEquiv', '', '(declare(n,integer),trigrat(sin((2*%pi*n-%pi)/2)))', '-(-1)^n', 1, '', ''],
        // According to Twitter!  Not sure this is even true, but just for fun!
        [
            'AlgEquiv', '', 'cot(%pi/20)+cot(%pi/24)-cot(%pi/10)', 'sqrt(1)+sqrt(2)+sqrt(3)+sqrt(4)+sqrt(5)+sqrt(6)',
            -3, '', '', '',
        ],
        [
            'AlgEquiv', '', 'trigeval(cot(%pi/20)+cot(%pi/24)-cot(%pi/10))', 'sqrt(1)+sqrt(2)+sqrt(3)+sqrt(4)+sqrt(5)+sqrt(6)',
            1, '', '', '',
        ],
        [
            'AlgEquiv', '', 'sin([1/8,1/6, 1/4, 1/3, 1/2, 1]*%pi)', '[sqrt(2-sqrt(2))/2,1/2,1/sqrt(2),sqrt(3)/2,1,0]',
            -3, '(ATList_wrongentries 1).', '', '',
        ],
        [
            'AlgEquiv', '', 'trigeval(sin([1/8,1/6, 1/4, 1/3, 1/2, 1]*%pi))', '[sqrt(2-sqrt(2))/2,1/2,1/sqrt(2),sqrt(3)/2,1,0]',
            1, '', '', '',
        ],
        ['AlgEquiv', '', '1+x', 'taylor(1/(1-x),x,0,1)', 1, '', ''],
        ['AlgEquiv', '', '1', 'taylor(1/(1-x),x,0,1)', 0, '', ''],

        ['AlgEquiv', '', 'log(a^2*b)', '2*log(a)+log(b)', 1, '', 'Logarithms'],
        ['AlgEquiv', '', '(2*log(2*x)+x)/(2*x)', '(log(2*x)+2)/(2*sqrt(x))', 0, '', ''],
        ['AlgEquiv', '', 'log(abs((x^2-9)))', 'log(abs(x-3))+log(abs(x+3))', 0, '', ''],
        ['AlgEquiv', '', 'lg(10^x)', 'x', 1, '', ''],
        ['AlgEquiv', '', 'lg(3^x,3)', 'x', 1, '', ''],
        ['AlgEquiv', '', 'lg(a^x,a)', 'x', 1, '', ''],
        ['AlgEquiv', '', '1+lg(27,3)', '4', 1, '', ''],
        ['AlgEquiv', '', '1+lg(27,3)', '3', 0, '', ''],
        ['AlgEquiv', '', 'lg(1/8,2)', '-3', 1, '', ''],
        ['AlgEquiv', '', 'lg(root(x,n))', 'lg(x,10)/n', 1, '', ''],
        // The log(x) function is base e.
        ['AlgEquiv', '', 'log(root(x,n))', 'lg(x,10)/n', 0, '', ''],
        ['AlgEquiv', '', 'x^log(y)', 'y^log(x)', 1, '', ''],
        // Example where some pre-processing is needed.
        ['AlgEquiv', '', 'log((x+1)/(1-x))', '-log((1-x)/(x+1))', 0, '', ''],
        ['AlgEquiv', '', 'ratsimp(logcontract(log((x+1)/(1-x))))',
            'ratsimp(logcontract(-log((1-x)/(x+1))))', 1, '', '', ],

        ['AlgEquiv', '', 'e^1-e^(-1)', '2*sinh(1)', 1, '', 'Hyperbolic trig'],
        ['AlgEquiv', '', 'x', '[1,2,3]', 0, 'ATAlgEquiv_SA_not_list.', 'Lists'],
        ['AlgEquiv', '', '[1,2]', '[1,2,3]', 0, 'ATList_wronglen.', ''],
        ['AlgEquiv', '', '[1,2,4]', '[1,2,3]', 0, '(ATList_wrongentries 3).', ''],
        ['AlgEquiv', '', '[1,x>2]', '[1,2<x]', 1, '', ''],
        [
            'AlgEquiv', '', '[1,2,[2-x<0,{1,2,2,2, 1,3}]]', '[1,2,[2-x<0,{1,2}]]', 0,
            '(ATList_wrongentries 3: (ATList_wrongentries 2: ATSet_wrongsz)).', '',
        ],
        [
            'AlgEquiv', '', '[(k+8)/(k^2+4*k-12),-(2*k+6)/(k^2+4*k-12)]', '[(k+8)/(k^2+4*k-12),-(2*k+6)/(k^2+4*k-12)]',
            1, '', '',
        ],
        ['AlgEquiv', '', '[1,2]', 'ntuple(1,2)', 0, 'ATAlgEquiv_SA_not_expression.', ''],

        // Note to self: Maxima's round() command uses Bankers' rounding, but significantfigures does not.
        ['AlgEquiv', '', 'round(0.5)', '0.0', 1, '', 'Rounding of floats'],
        ['AlgEquiv', '', 'round(1.5)', '2.0', 1, '', ''],
        ['AlgEquiv', '', 'round(2.5)', '2.0', 1, '', ''],
        ['AlgEquiv', '', 'round(12.5)', '12.0', 1, '', ''],
        ['AlgEquiv', '', 'significantfigures(0.5,1)', '0.5', 1, '', ''],
        ['AlgEquiv', '', 'significantfigures(1.5,1)', '2.0', 1, '', ''],
        ['AlgEquiv', '', 'significantfigures(2.5,1)', '3.0', 1, '', ''],
        ['AlgEquiv', '', 'significantfigures(3.5,1)', '4.0', 1, '', ''],
        ['AlgEquiv', '', 'significantfigures(11.5,2)', '12.0', 1, '', ''],
        ['AlgEquiv', '', '1500', 'scientific_notation(1500,3)', 1, '', ''],
        ['AlgEquiv', '', '1500', 'displaysci(1.5,2,3)', 1, '', ''],
        [
            'AlgEquiv', '', '[3,3.1,3.14,3.142,3.1416,3.14159,3.141593,3.1415927]',
            'makelist(significantfigures(%pi,i),i,8)', 1, '', '',
        ],
        ['AlgEquiv', '', 'x', '{1,2,3}', 0, 'ATAlgEquiv_SA_not_set.', 'Sets'],
        ['AlgEquiv', '', 'co(1,2)', '{1,2,3}', 0, 'ATAlgEquiv_SA_not_set.', ''],
        ['AlgEquiv', '', '{1,2}', '{1,2,3}', 0, 'ATSet_wrongsz.', ''],
        ['AlgEquiv', '', '{2/4, 1/3}', '{1/2, 1/3}', 1, '', ''],
        ['AlgEquiv', '', '{A[1],A[2],A[4]}', '{A[1],A[2],A[3]}', 0, 'ATSet_wrongentries.', ''],
        ['AlgEquiv', '', '{A[1],A[2],A[3]}', '{A[1],A[2],A[3]}', 1, '', ''],
        ['AlgEquiv', '', '{1,2,4}', '{1,2,3}', 0, 'ATSet_wrongentries.', ''],
        ['AlgEquiv', '', '{1,x>4}', '{4<x, 1}', 1, '', ''],
        ['AlgEquiv', '', '{x-1=0,x>1 and 5>x}', '{x>1 and x<5,x=1}', 1, '', ''],
        ['AlgEquiv', '', '{x-1=0,x>1 and 5>x}', '{x>1 and x<5,x=2}', 0, 'ATSet_wrongentries.', ''],
        ['AlgEquiv', '', '{x-1=0,x>1 and 5>x}', '{x>1 and x<3,x=1}', 0, 'ATSet_wrongentries.', ''],
        [
            'AlgEquiv', '', '{-sqrt(2)/sqrt(3)}', '{-2/sqrt(6)}', -3, 'ATSet_wrongentries.',
            'Equivalence for elements of sets is different from expressions: see docs.',
        ],
        [
            'AlgEquiv', '', '{[-sqrt(2)/sqrt(3),0],[2/sqrt(6),0]}', '{[2/sqrt(6),0],[-2/sqrt(6),0]}', -3,
            'ATSet_wrongentries.', '',
        ],
        // Without sets the following examples should be equal.
        ['AlgEquiv', '', '{5/4*%e^(%i*%pi/6)}', '{5*sqrt(3)/8+5/8*%i}', -3, 'ATSet_wrongentries.', ''],
        ['AlgEquiv', '', 'map(expand,{5/4*%e^(%i*%pi/6)})', '{5*sqrt(3)/8+5/8*%i}', 1, '', ''],
        ['AlgEquiv', '', 'ratsimp({5/4*%e^(%i*%pi/6)})', 'ratsimp({5*sqrt(3)/8+5/8*%i})', 1, '', ''],

        ['AlgEquiv', '', 'ev(radcan({-sqrt(2)/sqrt(3)}),simp)', 'ev(radcan({-2/sqrt(6)}),simp)', 1, '', ''],
        [
            'AlgEquiv', '', 'ev(radcan(ratsimp({(-sqrt(10)/2)-2,sqrt(10)/2-2},algebraic:true)),simp)',
            'ev(radcan(ratsimp({(-sqrt(5)/sqrt(2))-2,sqrt(5)/sqrt(2)-2},algebraic:true)),simp)', 1, '', '',
        ],
        ['AlgEquiv', '', '(a^b)^c', 'a^(b*c)', 0, '', ''],
        ['AlgEquiv', '', 'ev(radcan((a^b)^c),radexpand:all,simp)', 'a^(b*c)', 1, '', ''],
        ['AlgEquiv', '', '(n+1)^((n+2)/(n+1))/(n+2)', '1/(n+2)*((n+1)^(1/(n+1)))^(n+2)', 0, '', ''],
        [
            'AlgEquiv', '', 'ev(radcan((n+1)^((n+2)/(n+1))/(n+2)),radexpand:all,simp)',
            'ev(radcan(1/(n+2)*((n+1)^(1/(n+1)))^(n+2)),radexpand:all,simp)', 1, '', '',
        ],
        // We don't simplify here.
        ['AlgEquiv', '', '{(2-2^(5/2))/2,(2^(5/2)+2)/2}', '{1-2^(3/2),2^(3/2)+1}', 0, 'ATSet_wrongentries.', ''],
        ['AlgEquiv', '', 'ev(radcan({(2-2^(5/2))/2,(2^(5/2)+2)/2}),simp)', '{1-2^(3/2),2^(3/2)+1}', 1, '', ''],
        ['AlgEquiv', '', '{(x-a)^6000}', '{(a-x)^6000}', 0, 'ATSet_wrongentries.', ''],
        [
            'AlgEquiv', '', '{(k+8)/(k^2+4*k-12),-(2*k+6)/(k^2+4*k-12)}', '{(k+8)/(k^2+4*k-12),-(2*k+6)/(k^2+4*k-12)}',
            1, '', '',
        ],

        ['AlgEquiv', '', 'matrix([1,2],[2,3])', 'matrix([1,2],[2,3])', 1, '', 'Matrices'],
        ['AlgEquiv', '', 'matrix([1,2],[2,3])', 'matrix([1,2,3],[2,3,3])', 0, 'ATMatrix_wrongsz_columns.', ''],
        ['AlgEquiv', '', 'matrix([1,2],[2,3])', 'matrix([1,2],[2,5])', 0, 'ATMatrix_wrongentries.', ''],
        ['AlgEquiv', '', 'matrix([0.33,1],[1,1])', 'matrix([0.333,1],[1,1])', 0, 'ATMatrix_wrongentries.', ''],
        ['AlgEquiv', '', 'matrix([x+x,2],[2,x*x])', 'matrix([2*x,2],[2,x^2])', 1, '', ''],
        ['AlgEquiv', '', 'matrix([epsilon[0],2],[2,x^2])', 'matrix([epsilon[0],2],[2,x^2])', 1, '', ''],
        ['AlgEquiv', '', 'matrix([epsilon[2],2],[2,x^2])', 'matrix([epsilon[0],2],[2,x^3])', 0, 'ATMatrix_wrongentries.', ''],
        ['AlgEquiv', '', 'matrix([x>4,{1,x^2}],[[1,2],[1,3]])', 'matrix([4-x<0,{x^2, 1}],[[1,2],[1,3]])', 1, '', ''],
        [
            'AlgEquiv', '', 'matrix([x>4,{1,x^2}],[[1,2],[1,3]])', 'matrix([4-x<0,{x^2, 1}],[[1,2],[1,4]])', 0,
            'ATMatrix_wrongentries.', '',
        ],

        // A vector and a scalar are not the same.
        ['AlgEquiv', '', 'a', 'stackvector(a)', 0, '', 'Vectors'],

        ['AlgEquiv', '', '1', 'x=1', 0, 'ATAlgEquiv_SA_not_equation.', 'Equations'],
        ['AlgEquiv', '', 'x=1', 'x=1', 1, 'ATEquation_sides', ''],
        ['AlgEquiv', '', '1=x', '1=x', 1, 'ATEquation_sides', ''],
        ['AlgEquiv', '', '1=x', 'x=1', 1, 'ATEquation_sides_op', ''],
        ['AlgEquiv', '', '1=1', '1=x', 0, 'ATEquation_default', ''],
        ['AlgEquiv', '', '1=1', 'x=1', 0, 'ATEquation_default', ''],
        ['AlgEquiv', '', 'x=2', 'x=1', 0, 'ATEquation_lhs_notrhs', ''],
        ['AlgEquiv', '', '2=x', 'x=1', 0, 'ATEquation_default', ''],
        ['AlgEquiv', '', 'x=x', 'y=y', 1, 'ATEquation_zero', ''],
        ['AlgEquiv', '', 'x+y=1', 'y=1-x', 1, '', ''],
        ['AlgEquiv', '', '2*x+2*y=1', 'y=0.5-x', 1, 'ATEquation_ratio', ''],
        ['AlgEquiv', '', '1/x+1/y=2', 'y = x/(2*x-1)', 1, 'ATEquation_ratio', ''],
        ['AlgEquiv', '', 'y=sin(2*x)', 'y/2=cos(x)*sin(x)', 1, 'ATEquation_ratio', ''],
        ['AlgEquiv', '', 'y=(x-a)^6000', 'y=(x-a)^6000', 1, 'ATEquation_sides', ''],
        ['AlgEquiv', '', 'y=(x-a)^5999', 'y=(x-a)^6000', 0, 'ATEquation_lhs_notrhs', ''],
        ['AlgEquiv', '', 'y=(a-x)^6000', 'y=(x-a)^6000', 1, 'ATEquation_sides', ''],
        ['AlgEquiv', '', 'y=(a-x)^5999', 'y=(x-a)^5999', 0, 'ATEquation_lhs_notrhs', ''],
        ['AlgEquiv', '', 'y=(a-x)^59999', 'y=(x-a)^5999', 0, 'ATEquation_lhs_notrhs', ''],
        ['AlgEquiv', '', 'x+y=i', 'y=i-x', 1, '', ''],
        ['AlgEquiv', '', '(1+%i)*(x+y)=0', 'y=-x', 1, '', ''],
        ['AlgEquiv', '', 's^2*%e^(s*t)=0', 's^2=0', 0, 'ATEquation_default', ''],
        ['AlgEquiv', '', '0=-x+y/A+(y-z)/B', '0=x-y/A-(y-z)/B', 1, '', ''],
        ['AlgEquiv', '', 'x^6000-x^6001=x^5999', 'x^5999*(1-x+x^2)=0', 1, 'ATEquation_ratio', ''],
        ['AlgEquiv', '', 'x^6000-x^6001=x^5999', 'x^5999*(1-x+x^3)=0', 0, 'ATEquation_default', ''],
        ['AlgEquiv', '', '258552*x^7*(81*x^8+1)^398', 'x^3*(x^4+1)^399', 0, '', ''],
        ['AlgEquiv', '', 'Ia*(R1+R2+R3)-Ib*R3=0', '-Ia*(R1+R2+R3)+Ib*R3=0', 1, '', ''],
        ['AlgEquiv', '', 'a=0 or b=0', 'a*b=0', 1, 'ATEquation_sides', ''],
        ['AlgEquiv', '', 'a*b=0', 'a=0 or b=0', 1, 'ATEquation_sides', ''],
        // Notice here that Maxima does not know anything about a, so you can't cancel it!
        ['AlgEquiv', '', 'a*x=a*y', 'x=y', 0, 'ATEquation_default', ''],
        ['AlgEquiv', '', 'a*x=a*y', 'a=0 or x=y', 1, 'ATEquation_ratio', ''],

        ['AlgEquiv', '', '1', 'stackeq(1)', 1, '', 'Unary Equations'],
        ['AlgEquiv', '', 'stackeq(1)', '1', 1, '', ''],
        ['AlgEquiv', '', 'stackeq(1)', '0', 0, '', ''],

        [
            'AlgEquiv', '', 'x=y', 'x^2=y^2', 0, 'ATEquation_default',
            'Equations: Loose/gain roots with nth powers of each side.',
        ],
        // Note that algebraic equivalence does check multiplicity of roots.
        ['AlgEquiv', '', '(x-2)^2=0', 'x=2', 0, 'ATEquation_default', ''],
        [
            'AlgEquiv', '', '4*x^2-71*x+220 = 0 or 14*x^2-91*x+140 = 0',
            'x = 5/2 or x = 4 or x = 55/4', 0, 'ATEquation_default', '',
        ],
        [
            'AlgEquiv', '', '4*x^2-71*x+220 = 0 or 14*x^2-91*x+140 = 0',
            'x = 5/2 or x = 4 or x=4 or x = 55/4', 1, 'ATEquation_sides', '',
        ],
        ['AlgEquiv', '', 'x^2=4', 'x=2 or x=-2', 1, 'ATEquation_ratio', ''],
        ['AlgEquiv', '', 'x^2=4', 'x=2 nounor x=-2', 1, 'ATEquation_ratio', ''],
        ['AlgEquiv', '', 'x^2-5*x+6=0', 'x=2 nounor x=3', 1, 'ATEquation_sides', ''],
        ['AlgEquiv', '', 'x^2-5*x+6=0', 'x=(5 #pm# sqrt(25-24))/2', 1, 'ATEquation_sides', ''],
        ['AlgEquiv', '', 'x^2-5*x+6=0', 'x=(5 #pm# sqrt(25-23))/2', 0, 'ATEquation_default', ''],
        ['AlgEquiv', '', 'a^3*b^3=0', 'a=0 or b=0', 0, 'ATEquation_default', ''],
        ['AlgEquiv', '', 'a^3*b^3=0', 'a*b=0', 0, 'ATEquation_default', ''],
        ['AlgEquiv', '', '(x-y)*(x+y)=0', 'x^2=y^2', 1, 'ATEquation_ratio', ''],
        ['AlgEquiv', '', 'x=1', '(x-1)^3=0', 0, 'ATEquation_default', ''],
        ['AlgEquiv', '', 'sqrt(x)=sqrt(y)', 'x=y', 0, 'ATEquation_default', ''],
        ['AlgEquiv', '', 'x=sqrt(a)', 'x^2=a', 0, 'ATEquation_default', ''],
        ['AlgEquiv', '', '(x-sqrt(a))*(x+sqrt(a))=0', 'x^2=a', 1, 'ATEquation_ratio', ''],
        ['AlgEquiv', '', '(x-%i*sqrt(a))*(x+%i*sqrt(a))=0', 'x^2=-a', 1, 'ATEquation_ratio', ''],
        ['AlgEquiv', '', '(x-%i*sqrt(abs(a)))*(x+%i*sqrt(abs(a)))=0', 'x^2=-abs(a)', 1, 'ATEquation_ratio', ''],
        ['AlgEquiv', '', 'y=sqrt(1-x^2)', 'x^2+y^2=1', 0, 'ATEquation_default', ''],
        ['AlgEquiv', '', '(y-sqrt(1-x^2))*(y+sqrt(1-x^2))=0', 'x^2+y^2=1', 1, 'ATEquation_ratio', ''],
        ['AlgEquiv', '', '(y-sqrt((1-x)*(1+x)))*(y+sqrt((1-x)*(1+x)))=0', 'x^2+y^2=1', 1, 'ATEquation_ratio', ''],
        ['AlgEquiv', '', '(x-1)*(x+1)*(y-1)*(y+1)=0', 'y^2+x^2=1+x^2*y^2', 1, 'ATEquation_ratio', ''],

        [
            'AlgEquiv', '', 'all', 'x=x', 1, 'ATEquation_zero',
            'Equations: edge cases. Teacher must enter an equation, all or none here.',
        ],
        ['AlgEquiv', '', 'true', 'x=x', 1, 'ATEquation_zero', ''],
        ['AlgEquiv', '', 'x=x', 'all', 1, 'ATEquation_zero', ''],
        ['AlgEquiv', '', 'all', 'all', 1, 'ATEquation_zero', ''],
        ['AlgEquiv', '', 'true', 'all', 1, 'ATEquation_zero', ''],
        ['AlgEquiv', '', 'a=a', 'x=x', 1, 'ATEquation_zero', ''],
        ['AlgEquiv', '', 'false', 'x=x', 0, 'ATEquation_zero_fail', ''],
        ['AlgEquiv', '', 'false', 'all', 0, 'ATEquation_zero_fail', ''],
        ['AlgEquiv', '', 'none', 'all', 0, 'ATEquation_zero_fail', ''],
        ['AlgEquiv', '', 'all', 'none', 0, 'ATEquation_empty_fail', ''],
        ['AlgEquiv', '', '2=3', '1=4', 1, 'ATEquation_empty', ''],
        ['AlgEquiv', '', '2=3', '2=4', 1, 'ATEquation_empty', ''],
        ['AlgEquiv', '', 'none', '1=2', 1, 'ATEquation_empty', ''],
        ['AlgEquiv', '', 'false', '1=2', 1, 'ATEquation_empty', ''],
        ['AlgEquiv', '', 'none', 'none', 1, 'ATEquation_empty', ''],
        ['AlgEquiv', '', 'false', 'none', 1, 'ATEquation_empty', ''],
        ['AlgEquiv', '', '3=0', 'none', 1, 'ATEquation_empty', ''],
        ['AlgEquiv', '', '0=3', 'none', 1, 'ATEquation_empty', ''],
        ['AlgEquiv', '', 'all', '1=2', 0, 'ATEquation_empty_fail', ''],
        ['AlgEquiv', '', 'true', '1=2', 0, 'ATEquation_empty_fail', ''],
        ['AlgEquiv', '', '{}', '1=2', 0, 'ATAlgEquiv_SA_not_equation.', ''],
        ['AlgEquiv', '', '[]', '1=2', 0, 'ATAlgEquiv_SA_not_equation.', ''],
        ['AlgEquiv', '', '{}', 'none', 0, 'ATAlgEquiv_SA_not_logic.', ''],

        ['AlgEquiv', '', 'x^2', 'cc(1,3)', 0, 'ATAlgEquiv_SA_not_realset.', 'Sets of real numbers'],
        ['AlgEquiv', '', '%union(oo(1,2),oo(3,4))', '%union(oo(1,2),oo(3,4))', 1, 'ATRealSet_true.', ''],
        ['AlgEquiv', '', '%union(oc(1,2),co(2,3))', 'oo(1,3)', 1, 'ATRealSet_true.', ''],
        ['AlgEquiv', '', '%union(oc(1,2),co(2,3))', 'cc(1,3)', 0, 'ATRealSet_false.', ''],
        // Because we have bumped the "type" of the teacher's answer to realset, this test goes to ATRealSet, not sets.
        ['AlgEquiv', '', '{-1,1}', '%union({-1,1})', 1, 'ATRealSet_true.', ''],
        ['AlgEquiv', '', '{1,3}', 'cc(1,3)', 0, 'ATRealSet_false.', ''],
        ['AlgEquiv', '', '%intersection(oc(-1,1),co(1,2))', '%union({1})', 1, 'ATRealSet_true.', ''],
        ['AlgEquiv', '', 'oo(-inf,1)', 'oo(-inf,1)', 1, 'ATRealSet_true.', ''],
        ['AlgEquiv', '', 'oo(-1,inf)', 'oo(0,inf)', 0, 'ATRealSet_false.', ''],
        ['AlgEquiv', '', '%union(oc(-inf,0),oo(-1,4))', 'oo(-inf,4)', 1, 'ATRealSet_true.', ''],
        ['AlgEquiv', '', '%union(oo(-inf,1),oo(-1,inf))', 'oo(-inf,inf)', 1, 'ATRealSet_true.', ''],
        ['AlgEquiv', '', 'all', 'oo(-inf,inf)', 1, 'ATRealSet_true.', ''],

        // TO-DO: fix type checking to allow these examples.
        ['AlgEquiv', '', 'co(1,2)', '1 <= x nounand x<2', 0, 'ATAlgEquiv_SA_not_logic.', ''],
        ['AlgEquiv', '', '1 <= x nounand x<2', 'co(1,2)', 0, 'ATAlgEquiv_SA_not_realset.', ''],
        ['AlgEquiv', '', 'minf <= x', 'co(minf,inf)', 0, 'ATAlgEquiv_SA_not_realset.', ''],
        ['AlgEquiv', '', '-inf <= x', 'co(minf,inf)', 0, 'ATAlgEquiv_SA_not_realset.', ''],
        ['AlgEquiv', '', 'x <= inf', 'oc(minf,inf)', 0, 'ATAlgEquiv_SA_not_realset.', ''],
        ['AlgEquiv', '', 'minf <= x', 'oo(minf,inf)', 0, 'ATAlgEquiv_SA_not_realset.', ''],
        // So for now.
        [
            'AlgEquiv', '', 'stack_single_variable_solver(minf <= x)',
            'co(minf,inf)', 1, 'ATRealSet_true.', '',
        ],
        [
            'AlgEquiv', '', 'stack_single_variable_solver(-inf <= x)',
            'co(minf,inf)', 1, 'ATRealSet_true.', '',
        ],
        [
            'AlgEquiv', '', 'stack_single_variable_solver(x <= inf)',
            'oc(minf,inf)', 1, 'ATRealSet_true.', '',
        ],
        [
            'AlgEquiv', '', 'stack_single_variable_solver(minf <= x)',
            'oo(minf,inf)', 0, 'ATRealSet_false.', '',
        ],

        ['AlgEquiv', '', 'a=b/%i', '%i*a=b', 1, 'ATEquation_num_i', 'Complex numbers'],
        ['AlgEquiv', '', 'b/%i=a', '%i*a=b', 1, 'ATEquation_num_i', ''],
        ['AlgEquiv', '', 'b=a/%i', '%i*a=b', 0, 'ATEquation_lhs_notrhs_op', ''],
        ['AlgEquiv', '', 'a*(2+%i)=b', 'a=b/(2+%i)', 1, 'ATEquation_ratio', ''],
        ['AlgEquiv', '', 'a*(2+%i)=b', 'a=b*(2-%i)/5', 1, 'ATEquation_num_i', ''],
        ['AlgEquiv', '', 'a*(2+%i)=b', 'a=b*(2-%i)/4', 0, 'ATEquation_default', ''],
        // For now, teachers using these display functions must remove them manually.
        ['AlgEquiv', '', 'i', 'disp_complex(0,1)', 0, '', ''],

        ['AlgEquiv', '', 'abs(x)=abs(y)', 'x=y', 0, 'ATEquation_default', 'Absolute value in equations'],
        ['AlgEquiv', '', 'abs(x)=abs(y)', 'x=y or x=-y', 1, '', ''],
        ['AlgEquiv', '', 'abs(x)=abs(y)', '(x-y)*(x+y)=0', 1, '', ''],

        ['AlgEquiv', '', 'f(x):=1/0', 'f(x):=x^2', -1, 'TEST_FAILED', 'Functions'],
        ['AlgEquiv', '', '1', 'f(x):=x^2', 0, 'ATAlgEquiv_SA_not_function.', ''],
        ['AlgEquiv', '', 'f(x)=x^2', 'f(x):=x^2', 0, 'ATAlgEquiv_SA_not_function.', ''],
        ['AlgEquiv', '', 'f(x):=x^2', 'f(x,y):=x^2+y^2', 0, 'ATFunction_length_args. ATFunction_false.', ''],
        ['AlgEquiv', '', 'f(x):=x^2', 'f(x)=x^2', 0, 'ATAlgEquiv_SA_not_equation.', ''],
        ['AlgEquiv', '', 'f(x):=x^2', 'f(x):=x^2', 1, 'ATFunction_true.', ''],
        ['AlgEquiv', '', 'f(x):=x^2', 'f(x):=sin(x)', 0, 'ATFunction_false.', ''],
        ['AlgEquiv', '', 'g(x):=x^2', 'f(x):=x^2', 0, 'ATFunction_wrongname. ATFunction_true.', ''],
        ['AlgEquiv', '', 'f(y):=y^2', 'f(x):=x^2', 1, 'ATFunction_arguments_different. ATFunction_true.', ''],
        ['AlgEquiv', '', 'f(a,b):=a^2+b^2', 'f(x,y):=x^2+y^2', 1, 'ATFunction_arguments_different. ATFunction_true.', ''],

        ['AlgEquiv', '', '1', 'x>1', 0, 'ATAlgEquiv_SA_not_inequality.', 'Inequalities'],
        ['AlgEquiv', '', 'x=1', 'x>1 and x<5', 0, 'ATAlgEquiv_TA_not_equation.', ''],
        ['AlgEquiv', '', 'x<1', 'x>1', 0, 'ATInequality_backwards.', ''],
        ['AlgEquiv', '', '1<x', 'x>1', 1, '', ''],
        ['AlgEquiv', '', 'a<b', 'b>a', 1, '', ''],
        ['AlgEquiv', '', '2<2*x', 'x>1', 1, '', ''],
        ['AlgEquiv', '', '-2>-2*x', 'x>1', 1, '', ''],
        ['AlgEquiv', '', 'x>1', 'x<=1', 0, 'ATInequality_strict. ATInequality_backwards.', ''],
        ['AlgEquiv', '', 'x>=2', 'x<2', 0, 'ATInequality_nonstrict. ATInequality_backwards.', ''],
        ['AlgEquiv', '', 'x>=1', 'x>2', 0, 'ATInequality_nonstrict.', ''],
        ['AlgEquiv', '', 'x>1', 'x>1', 1, '', ''],
        ['AlgEquiv', '', 'x>=1', 'x>=1', 1, '', ''],
        ['AlgEquiv', '', 'x>2', 'x>1', 0, '', ''],
        ['AlgEquiv', '', '1<x', 'x>1', 1, '', ''],
        ['AlgEquiv', '', '2*x>=x^2', 'x^2<=2*x', 1, '', ''],
        ['AlgEquiv', '', '2*x>=x^2', 'x^2<=2*x', 1, '', ''],
        ['AlgEquiv', '', '3*x^2<9*a', 'x^2-3*a<0', 1, '', ''],
        ['AlgEquiv', '', 'x^2>4', 'x>2 or x<-2', 1, 'ATLogic_True.', ''],
        ['AlgEquiv', '', '1<x or x<-3', 'x<-3 or 1<x', 1, 'ATLogic_True.', ''],
        ['AlgEquiv', '', '1<x or x<-3', 'x<-1 or 3<x', 0, '', ''],
        ['AlgEquiv', '', 'x>1 and x<5', 'x>1 and x<5', 1, 'ATLogic_True.', ''],
        ['AlgEquiv', '', 'x>1 and x<5', '5>x and 1<x', 1, 'ATLogic_True.', ''],
        ['AlgEquiv', '', 'not (x<=2 and -2<=x)', 'x>2 or -2>x', 1, 'ATLogic_True.', ''],

        // This is the expected bevaviour as we are representing a set of numbers.
        ['AlgEquiv', '', 'sigma>1', 'x>1', 1, 'ATInequality_solver.', ''],
        ['AlgEquiv', '', 'a>1', 'x>1', 1, 'ATInequality_solver.', ''],
        ['AlgEquiv', '', 'sigma>1', 'x>2', 0, '', ''],
        ['AlgEquiv', '', 'x>2 or -2>x', 'not (x<=2 and -2<=x)', 1, 'ATLogic_True.', ''],
        ['AlgEquiv', '', 'x>=1 or 1<=x', 'x>=1', 1, '', ''],
        ['AlgEquiv', '', 'x>=1 and x<=1', 'x=1', 1, 'ATInequality_solver.', ''],
        [
            'AlgEquiv', '', '(x>4 and x<5) or (x<-4 and x>-5) or (x+5>0 and x<-4)',
            '(x>-5 and x<-4) or (x>4 and x<5)', 1, 'ATLogic_True.', '',
        ],
        [
            'AlgEquiv', '', '(x>4 and x<5) or (x<-4 and x>-5) or (x+5>0 and x<-4)',
            '(x>-5 and x<-4) or (x>8 and x<5)', 0, '', '',
        ],
        ['AlgEquiv', '', '(x < 0 nounor x >= 1) nounand x <= 3', 'x < 0 or (x >= 1 and x <= 3)', 1, 'ATLogic_True.', ''],
        ['AlgEquiv', '', '(x < 0 nounor x >= 1) nounand x <= 3', 'x < 0 or x >= 1 and x <= 3', 1, 'ATLogic_True.', ''],
        ['AlgEquiv', '', '(x < 0 nounor x >= 1) nounand x <= 3', 'x < 0 or (x >= 1 and x <= 3)', 1, 'ATLogic_True.', ''],
        [
            'AlgEquiv', '', '(x < 0 nounor x >= 1) nounand x <= 3', '(x < 0 or x >= 1) and x <= 3', 1,
            'ATLogic_True.', '',
        ],
        ['AlgEquiv', '', '(x < 0 nounor x >= 1) nounand x <= 3', 'x < 0 or (x >= 1 and x <= 3)', 1, 'ATLogic_True.', ''],
        ['AlgEquiv', '', 'natural_domain(1/x^2)', 'natural_domain(1/x)', 1, 'ATRealSet_true.', ''],
        ['AlgEquiv', '', 'x^4>=0', 'x^2>=0', 1, '', ''],
        ['AlgEquiv', '', 'x^4>=16', 'x^2>=4', 1, '', ''],
        ['AlgEquiv', '', 'x^4>=16', 'x^2>=4', 1, '', ''],
        ['AlgEquiv', '', '-3<=x', '-3<=x nounand x<=3', 0, '', ''],
        ['AlgEquiv', '', '{2,-2}', 'x>2 nounor -2>x', 0, 'ATAlgEquiv_SA_not_logic.', ''],
        ['AlgEquiv', '', 'x^2<4', 'x<2 nounand x>-2', 1, 'ATLogic_Solver_True.', ''],
        ['AlgEquiv', '', 'x^2<6', 'x<2 nounand x>-2', 0, '', ''],
        ['AlgEquiv', '', 'x>1 nounand x<-1', 'false', 1, 'ATLogic_Solver_True.', ''],
        ['AlgEquiv', '', 'x>1 nounand x<3', 'true', 0, '', ''],
        ['AlgEquiv', '', 'x>1 nounor x<3', 'true', 1, 'ATLogic_Solver_True.', ''],
        ['AlgEquiv', '', 'x>1 nounor x<3', 'all', 1, 'ATLogic_Solver_True.', ''],
        ['AlgEquiv', '', 'abs(x)<1', 'abs(x)<1', 1, '', ''],
        ['AlgEquiv', '', 'abs(x)<1', 'abs(x)<2', 0, '', ''],
        ['AlgEquiv', '', 'abs(x)<1', 'abs(x)>1', 0, 'ATInequality_backwards.', ''],
        ['AlgEquiv', '', 'abs(x)<2', '-2<x and x<2', -3, '', ''],
        ['AlgEquiv', '', '-2<x and x<2', 'abs(x)<2', -3, '', ''],
        ['AlgEquiv', '', 'abs(x)<2', '-1<x and x<1', 0, '', ''],
        ['AlgEquiv', '', 'x^2<=9', 'abs(x)<3', 0, '', ''],
        ['AlgEquiv', '', 'x^2<=9', 'abs(x)<=3', -3, '', ''],
        ['AlgEquiv', '', 'x^6<1', 'abs(x)<1', -3, '', ''],
        ['AlgEquiv', '', 'abs(x)>1', 'x<-1 or x>1', -3, '', ''],

        ['AlgEquiv', '', 'minf < x', 'minf <= x', 0, 'ATInequality_strict.', ''],
        ['AlgEquiv', '', 'x>minf', 'minf < x', 1, '', ''],
        ['AlgEquiv', '', 'x>-inf', 'minf < x', 1, '', ''],
        // Because Maxima does not simplify expressions like 2*inf->inf!
        ['AlgEquiv', '', 'x<2*inf', 'x<inf', 0, '', ''],
        ['AlgEquiv', '', 'minf < x nounand x <1', 'x<1', 1, '', ''],
        ['AlgEquiv', '', 'minf < x nounand x <1', 'x<2', 0, '', ''],

        // Please note the following, which are a Maxima design issue!
        ['AlgEquiv', '', '2*inf', 'inf', 0, '', 'Maxima and infinity'],
        ['AlgEquiv', '', '-inf', 'minf', 0, '', ''],

        ['AlgEquiv', '', 'x#1', 'x#1', 1, 'ATLogic_True.', 'Not equal to'],
        ['AlgEquiv', '', 'x#(1+1)', 'x#2', 1, 'ATLogic_True.', ''],
        ['AlgEquiv', '', '1#x', 'x#1', 1, 'ATLogic_True.', ''],
        ['AlgEquiv', '', 'a#b', 'b#a', 1, '', ''],
        ['AlgEquiv', '', 'x#2', 'x-2#0', 1, 'ATLogic_True.', ''],
        ['AlgEquiv', '', '[x#2]', '[x-2#0]', 1, '', ''],
        ['AlgEquiv', '', 'x-3#0', 'x#2', 0, '', ''],
        ['AlgEquiv', '', 'x#2', 'x<2 nounor x>2', 1, 'ATLogic_Solver_True.', ''],
        ['AlgEquiv', '', 'x^2-3#1', 'x<-2 nounor (x<-2 and x<2) nounor 2<x', 0, '', ''],
        ['AlgEquiv', '', 'x^2-3#1', 'x<-2 nounor (-2<x and x<2) nounor 2<x', 1, 'ATLogic_Solver_True.', ''],
        // We probably need to think about what this really means...
        ['AlgEquiv', '', 'x#1', 'x#0', 0, '', ''],

        ['AlgEquiv', '', 'sqrt(12)', '2*sqrt(3)', 1, '', 'Surds'],
        ['AlgEquiv', '', 'sqrt(11+6*sqrt(2))', '3+sqrt(2)', 1, '', ''],
        ['AlgEquiv', '', '(19601-13860*sqrt(2))^(7/4)', '(5*sqrt(2)-7)^7', 1, '', ''],
        ['AlgEquiv', '', '(19601-13861*sqrt(2))^(7/4)', '(5*sqrt(2)-7)^7', 0, '', ''],
        ['AlgEquiv', '', '(19601-13861*sqrt(2))^(7/4)', '(5*sqrt(2)-7)^7', 0, '', ''],
        ['AlgEquiv', '', 'sqrt(2*log(26)+4-2*log(2))', 'sqrt(2*log(13)+4)', 1, '', ''],
        [
            'AlgEquiv', '', 'sqrt(2)*sqrt(3)+2*(sqrt(2/3))*x-(2/3)*(sqrt(2/3))*x^2+(4/9)*(sqrt(2/3))*x^3',
            '4*sqrt(6)*x^3/27-(2*sqrt(6)*x^2)/9+(2*sqrt(6)*x)/3+sqrt(6)', 1, '', '',
        ],
        ['AlgEquiv', '', '(n+1)*n!', '(n+1)!', 1, '', 'Factorials and binomials'],
        ['AlgEquiv', '', 'n/n!', '1/(n-1)!', 1, '', ''],
        ['AlgEquiv', '', 'n/n!', '1/(n+1)!', 0, '', ''],
        ['AlgEquiv', '', 'n!/((k-1)!*(n-k+1)!)', 'n!*k/(k!*(n-k+1)!)', 1, '', ''],
        ['AlgEquiv', '', 'n!/(k!*(n-k)!)', 'n!*(n-k+1)/(k!*(n-k+1)!)', 1, '', ''],
        ['AlgEquiv', '', 'n!/(k!*(n-k)!)', 'binomial(n,k)', 1, '', ''],
        ['AlgEquiv', '', 'binomial(n,k)+binomial(n,k+1)', 'binomial(n+1,k+1)', 1, '', ''],
        [
            'AlgEquiv', '', 'n!/((k-1)!*(n-k+1)!)+n!/(k!*(n-k)!)',
            'n!*k/(k!*(n-k+1)!)+n!*(n-k+1)/(k!*(n-k+1)!)', 1, '', '',
        ],
        ['AlgEquiv', '', 'binomial(n,k)+binomial(n,k+1)', 'binomial(n+1,k)', 0, '', ''],
        ['AlgEquiv', '', 'binomial(n,k)', 'binomial(n,n-k)', 1, '', ''],
        ['AlgEquiv', '', '175!*56!/(55!*176!)', '17556/55176', 1, '', ''],
        ['AlgEquiv', '', 'binomial(58,[9,15,20,14])', 'binomial(58,[15,9,20,14])', 1, '', ''],
        ['AlgEquiv', '', 'binomial(x,[a,b,c])', 'binomial(x,[b,c,a])', 1, '', ''],
        ['AlgEquiv', '', '3*s*diff(q(s),s)', '3*s*diff(q(s),s)', 1, '', 'Unevaluated derviatives'],
        ['AlgEquiv', '', '3*t*diff(q(s),s)', '3*diff(t*q(s),s)', 1, '', ''],
        ['AlgEquiv', '', 'diff(diff(q(s),s),s)', 'diff(q(s),s,2)', 1, '', ''],
        ['AlgEquiv', '', 'sum(k^n,n,0,3)', 'sum(k^n,n,0,3)', 1, '', 'Sums and products'],
        ['AlgEquiv', '', '1+k+k^2+k^3', 'sum(k^n,n,0,3)', 1, '', ''],
        ['AlgEquiv', '', '1+k+k^2', 'sum(k^n,n,0,3)', 0, '', ''],
        ['AlgEquiv', '', 'n*(n+1)*(2*n+1)/6', 'sum(k^2,k,1,n)', 1, '', ''],
        ['AlgEquiv', '', 'sum((k+1)^2,k,0,n-1)', 'sum(k^2,k,1,n)', 1, '', ''],
        ['AlgEquiv', '', 'product(cos(k*x),k,1,3)', 'product(cos(k*x),k,1,3)', 1, '', ''],
        ['AlgEquiv', '', 'cos(x)*cos(2*x)*cos(3*x)', 'product(cos(k*x),k,1,3)', 1, '', ''],
        ['AlgEquiv', '', 'cos(x)*cos(2*x)', 'product(cos(k*x),k,1,3)', 0, '', ''],
        ['AlgEquiv', '', '9.81*m/s^2', 'stackunits(9.81,m/s^2)', 1, '', 'Scientific units are ignored'],
        ['AlgEquiv', '', '6*stackunits(1,m)', 'stackunits(6,m)', 1, '', ''],
        ['AlgEquiv', '', 'stackunits(2,m)^2', 'stackunits(4,m^2)', 1, '', ''],
        ['AlgEquiv', '', 'stackunits(2,s)^2', 'stackunits(4,m^2)', 0, '', ''],
        ['AlgEquiv', '', 'stack_units_nums(stackunits_make(m/s))', '1', 0, '', ''],
        ['AlgEquiv', '', 'stack_units_nums(stackunits_make(m/s))', 'NULLNUM', 1, '', ''],
        ['AlgEquiv', '', 'ev(stack_units_nums(stackunits_make(m/s)),NULLNUM=1)', '1', 1, '', ''],
        ['AlgEquiv', '', '-inf', 'minf', 0, '', 'Maxima does not simplify -inf (I agree!)'],
        [
            'AlgEquiv', '', '2/%i*ln(sqrt((1+z)/2)+%i*sqrt((1-z)/2))', '-%i*ln(z+%i*sqrt(1-z^2))', -3,
            '', 'These currently fail',
        ],
        ['AlgEquiv', '', 'abs(x^2-4)/(abs(x-2)*abs(x+2))', '1', -3, '', ''],
        ['AlgEquiv', '', 'abs(x^2-4)', 'abs(x-2)*abs(x+2)', -3, '', ''],
        ['AlgEquiv', '', '(-1)^n*cos(x)^n', '(-cos(x))^n', -3, '', ''],
        ['AlgEquiv', '', '(sqrt(108)+10)^(1/3)-(sqrt(108)-10)^(1/3)', '2', -3, '', ''],
        ['AlgEquiv', '', '(sqrt(2+sqrt(2))+sqrt(2-sqrt(2)))/(2*sqrt(2))', 'sqrt(sqrt(2)+2)/2', -3, '', ''],
        ['AlgEquiv', '', 'sqrt(2*x*sqrt(x^2+1)+2*x^2+1)-sqrt(x^2+1)-x', '0', -3, '', ''],
        ['AlgEquiv', '', '(77+20*sqrt(13))^(1/6)-(77-20*sqrt(13))^(1/6)', '1', -3, '', ''],
        ['AlgEquiv', '', '(930249+416020*sqrt(5))^(1/30)-(930249-416020*sqrt(5))^(1/30)', '1', -3, '', ''],
        // An example due to Gauss.  Just for fun!
        [
            'AlgEquiv', '', 'cos(2*%pi/17)', '(-1+sqrt(17)+sqrt(34-2*sqrt(17)))/16+' .
            '(2*sqrt(17+3*sqrt(17)-sqrt(34-2*sqrt(17))-2*sqrt(34+2*sqrt(17))))/16', -3, '', '', '',
        ],
        [
            'AlgEquiv', '', '(41-sqrt(511))/2', '(sqrt((4*(cos((1/2*(acos((61/1040*sqrt(130)))-atan(11/3)))))^(2))+21)' .
            '-(2*cos((1/2*(acos((61/1040*sqrt(130)))-atan(11 / 3))))))^(2)', -3, '', '', '',
        ],

        ['AlgEquiv', '', 'a*(1+sqrt(2))=b', 'a=b*(sqrt(2)-1)/3', -3, 'ATEquation_default', ''],
        ['AlgEquiv', '', 'atan(1/2)', '%pi/2-atan(2)', -3, '', 'This is only equivalent for x>=0...', ''],
        ['AlgEquiv', '', 'asinh(x)', 'ln(x+sqrt(x^2+1))', -3, '', 'This is true for all x...', ''],

        ['AlgEquiv', '', 'true and false', 'false', 1, 'ATLogic_True.', 'Logical expressions'],
        ['AlgEquiv', '', 'true or false', 'false', 0, '', ''],
        ['AlgEquiv', '', 'A and B', 'B and A', 1, 'ATLogic_True.', ''],
        ['AlgEquiv', '', 'A and B', 'C and A', 0, '', ''],
        ['AlgEquiv', '', 'A and B=C', 'C=B and A', 1, 'ATLogic_True.', ''],
        ['AlgEquiv', '', 'A and (B and C)', 'A and B and C', 1, 'ATLogic_True.', ''],
        ['AlgEquiv', '', 'A and (B or C)', 'A and (B or C)', 1, 'ATLogic_True.', ''],
        ['AlgEquiv', '', '(A and B) or (A and C)', 'A and (B or C)', 1, 'ATLogic_True.', ''],
        ['AlgEquiv', '', '-(b#pm#sqrt(b^2-4*a*c))', '-b#pm#sqrt(b^2-4*a*c)', 1, 'ATLogic_True.', ''],
        ['AlgEquiv', '', 'x=-b#pm#c^2', 'x=c^2-b or x=-c^2-b', 1, 'ATEquation_sides', ''],
        ['AlgEquiv', '', 'x=b#pm#c^2', 'x=c^2-b or x=-c^2-b', 0, 'ATEquation_default', ''],
        ['AlgEquiv', '', 'x#pm#a = y#pm#b', 'x#pm#a = y#pm#b', 1, 'ATEquation_sides', ''],
        ['AlgEquiv', '', 'x#pm#a = y#pm#b', 'x#pm#a = y#pm#c', 0, 'ATEquation_lhs_notrhs', ''],
        ['AlgEquiv', '', 'not(A) and not(B)', 'not(A or B)', 1, 'ATLogic_True.', ''],
        ['AlgEquiv', '', 'not(A) and not(B)', 'not(A and B)', 0, '', ''],
        ['AlgEquiv', '', 'not(A) or B', 'boolean_form(A implies B)', 1, '', ''],
        ['AlgEquiv', '', 'not(A) or B', 'A implies B', 1, 'ATLogic_True.', ''],
        ['AlgEquiv', '', 'not(A) and B', 'A implies B', 0, '', ''],
        ['AlgEquiv', '', '(not A and B) or (not B and A)', 'A xor B', 1, 'ATLogic_True.', ''],
        ['AlgEquiv', '', '(A and B) or (not A and not B)', 'A xnor B', 1, 'ATLogic_True.', ''],
        // We can't apply this simplification to sets, as it breaks sets of inequalities.
        ['AlgEquiv', '', '{not(A) or B,A and B}', '{A implies B,A and B}', 0, 'ATSet_wrongentries.', ''],
        ['AlgEquiv', '', '{A implies B,A and B}', '{not(A) and B,A and B}', 0, 'ATSet_wrongentries.', ''],

        // Algebraic equivalence evaluates nouns.
        ['AlgEquiv', '', 'diff(x^2,x)', '2*x', 1, '', 'Differential equations'],
        ['AlgEquiv', '', 'diff(x^2,x)', '\'diff(x^2,x)', 1, '', ''],
        ['AlgEquiv', '', 'noundiff(x^2,x)', '2*x', 1, '', ''],
        ['AlgEquiv', '', 'diff(y,x)', '0', 1, '', ''],
        ['AlgEquiv', '', 'noundiff(y,x)', '0', 1, '', ''],
        // Note evaluated functions.
        ['AlgEquiv', '', 'diff(y(x),x)', '0', 0, '', ''],
        ['AlgEquiv', '', 'diff(y(x),x)', 'diff(y,x)', 0, '', ''],
        // Both get evaluated to zero.
        ['AlgEquiv', '', 'diff(y,x)', 'diff(y,x,2)', 1, '', ''],

        ['AlgEquiv', '', '"Hello"', '"Hello"', 1, 'ATAlgEquiv_String', 'Basic support for strings'],
        ['AlgEquiv', '', '"hello"', '"Hello"', 0, 'ATAlgEquiv_String', ''],
        ['AlgEquiv', '', 'W', '"Hello"', 0, 'ATAlgEquiv_SA_not_string.', ''],
        ['AlgEquiv', '', '"Hello"', 'x^2', 0, 'ATAlgEquiv_SA_not_expression.', ''],

        ['AlgEquivNouns', '', '1/0', '1', -1, 'ATAlgEquivNouns_STACKERROR_SAns.', ''],
        ['AlgEquivNouns', '', '1', '1/0', -1, 'ATAlgEquivNouns_STACKERROR_TAns.', ''],
        ['AlgEquivNouns', '', '', '(x-1)^2', -1, 'ATAlgEquivNounsTEST_FAILED-Empty SA.', ''],
        ['AlgEquivNouns', '', 'x^2', '', -1, 'ATAlgEquivNounsTEST_FAILED-Empty TA.', ''],
        ['AlgEquivNouns', '', 'x-1)^2', '(x-1)^2', -1, 'ATAlgEquivNounsTEST_FAILED-Empty SA.', ''],

        ['AlgEquivNouns', '', 'diff(x^2,x)', '2*x', 1, '', ''],
        ['AlgEquivNouns', '', 'diff(x^2,x)', '\'diff(x^2,x)', 0, '', ''],
        ['AlgEquivNouns', '', 'diff(x^2,x)', '\'diff(x^2,x)', 0, '', ''],
        ['AlgEquivNouns', '', '\'diff(y,x)', 'noundiff(y,x)', 1, '', ''],
        ['AlgEquivNouns', '', 'diff(y,x)', '0', 1, '', ''],
        ['AlgEquivNouns', '', '\'diff(y,x)', '0', 0, '', ''],
        ['AlgEquivNouns', '', 'noundiff(y,x)', '0', 0, '', ''],
        ['AlgEquivNouns', '', 'diff(y(x),x)', '0', 0, '', ''],
        ['AlgEquivNouns', '', '\'diff(y,x,1)', '\'diff(y,x,2)', 0, '', ''],
        // A function and a function evaluated at a point are not the same thing.
        ['AlgEquivNouns', '', '\'diff(y(x),x)', '\'diff(y,x)', 0, '', ''],
        // Use subst here to move y(x)->y.
        ['AlgEquivNouns', '', 'subst(y,y(x),\'diff(y,x)+y=1)', '\'diff(y,x)+y=1', 1, 'ATEquation_sides', ''],
        ['AlgEquivNouns', '', 'subst(y,y(x),\'diff(y(x),x)+y(x)=1)', '\'diff(y,x)+y=1', 1, 'ATEquation_sides', ''],
        ['AlgEquivNouns', '', 'subst(y(x),y,\'diff(y,x)+y=1)', '\'diff(y(x),x)+y(x)=1', 1, 'ATEquation_sides', ''],
        ['AlgEquivNouns', '', 'subst(y(x),y,\'diff(y,x)+y=1)', '\'diff(y,x)+y=1', 0, 'ATEquation_default', ''],
        [
            'AlgEquivNouns', '', 'subst(y(x),y,\'diff(y(x),x)+y(x)=1)', '\'diff(y,x)+y=1', -1,
            'ATAlgEquivNouns_STACKERROR_SAns.', '',
        ],
        // The Maxima atom y_x means we can't have subscripts as a derivative operator.
        ['AlgEquivNouns', '', 'y_x', '\'diff(y,x)', 0, '', ''],
        ['AlgEquivNouns', '', 'noundiff(f,x,1,y,1)', 'noundiff(noundiff(f,x),y)', 1, '', 'Partials'],
        ['AlgEquivNouns', '', 'noundiff(noundiff(f,y),x)', 'noundiff(noundiff(f,x),y)', 1, '', ''],
        ['AlgEquivNouns', '', 'noundiff(noundiff(f,x),x)', 'noundiff(f,x,2)', 1, '', ''],
        [
            'AlgEquivNouns', '', 'noundiff(H,x,2) = -R/T', 'noundiff(H,x,2) + R/T = 0', 1, 'ATEquation_ratio',
            'Differential equations',
        ],
        ['AlgEquivNouns', '', '\'diff(H,x,2) = -R/T', 'noundiff(H,x,2) + R/T = 0', 1, 'ATEquation_ratio', ''],
        ['AlgEquivNouns', '', 'y(t)=int(s^2,s,0,t)', 'y(t)=t^3/3', 1, 'ATEquation_sides', ''],
        ['AlgEquivNouns', '', 'y(t)=\'int(s^2,s,0,t)', 'y(t)=t^3/3', 0, 'ATEquation_lhs_notrhs', ''],
        ['AlgEquivNouns', '', 'y(t)=\'int(s^2,s,0,t)', 'y(t)=nounint(s^2,s,0,t)', 1, 'ATEquation_sides', ''],
        [
            'AlgEquivNouns', '', 'true nounand false', 'false', 1, 'ATLogic_True.',
            'Logic nouns are still evaluated',
        ],

        ['SubstEquiv', '', '1/0', 'x^2-2*x+1', -1, 'ATSubstEquiv_STACKERROR_SAns.', ''],
        ['SubstEquiv', '[1/0]', 'x^2', 'x^2-2*x+1', -1, 'ATSubstEquiv_STACKERROR_Opt.', ''],
        ['SubstEquiv', 'x', 'x^2', 'x^2-2*x+1', -1, 'ATSubstEquiv_Opt_List.', ''],
        ['SubstEquiv', '', 'x^2+1', 'x^2+1', 1, '', ''],
        ['SubstEquiv', '', 'x^2+1', 'x^3+1', 0, '', ''],
        ['SubstEquiv', '', 'x^2+1', 'x^3+1', 0, '', ''],
        ['SubstEquiv', '', 'X^2+1', 'x^2+1', 1, 'ATSubstEquiv_Subst [X = x].', ''],
        ['SubstEquiv', '', 'x^2+y', 'a^2+b', 1, 'ATSubstEquiv_Subst [x = a,y = b].', ''],
        ['SubstEquiv', '', 'x^2+y/z', 'a^2+c/b', 1, 'ATSubstEquiv_Subst [x = a,y = c,z = b].', ''],
        ['SubstEquiv', '', 'y=x^2', 'a^2=b', 1, 'ATSubstEquiv_Subst [x = a,y = b].', ''],
        ['SubstEquiv', '', '{x=1,y=2}', '{x=2,y=1}', 1, 'ATSubstEquiv_Subst [x = y,y = x].', ''],
        [
            'SubstEquiv', '', 'cos(a*x)/(x*(ln(x)))', 'cos(a*y)/(y*(ln(y)))', 1, 'ATSubstEquiv_Subst [a = a,x = y].',
            'Where a variable is also a function name.',
        ],
        ['SubstEquiv', '', 'cos(a*x)/(x*(ln(x)))', 'cos(x*a)/(a*(ln(a)))', 1, 'ATSubstEquiv_Subst [a = x,x = a].', ''],
        ['SubstEquiv', '', 'cos(a*x)/(x*(ln(x)))', 'cos(a*x)/(x(ln(x)))', 0, '', ''],
        ['SubstEquiv', '', 'cos(a*x)/(x*(ln(x)))', 'cos(a*y)/(y(ln(y)))', 0, '', ''],
        ['SubstEquiv', '', 'x+1>y', 'y+1>x', 1, 'ATSubstEquiv_Subst [x = y,y = x].', ''],
        ['SubstEquiv', '', 'x+1>y', 'x<y+1', 1, 'ATSubstEquiv_Subst [x = y,y = x].', ''],
        [
            'SubstEquiv', '', 'matrix([1,A^2+A+1],[2,0])', 'matrix([1,x^2+x+1],[2,0])', 1,
            'ATSubstEquiv_Subst [A = x].', 'Matrices',
        ],
        [
            'SubstEquiv', '', 'matrix([B,A^2+A+1],[2,C])', 'matrix([y,x^2+x+1],[2,z])', 1,
            'ATSubstEquiv_Subst [A = x,B = y,C = z].', '',
        ],
        ['SubstEquiv', '', 'matrix([B,A^2+A+1],[2,C])', 'matrix([y,x^2+x+1],[2,x])', 0, 'ATMatrix_wrongentries.', ''],
        ['SubstEquiv', '', '[x^2+1,x^2]', '[A^2+1,A^2]', 1, 'ATSubstEquiv_Subst [x = A].', 'Lists'],
        ['SubstEquiv', '', '[x^2-1,x^2]', '[A^2+1,A^2]', 0, '(ATList_wrongentries 1, 2).', ''],
        ['SubstEquiv', '', '[A,B,C]', '[B,C,A]', 1, 'ATSubstEquiv_Subst [A = B,B = C,C = A].', ''],
        ['SubstEquiv', '', '[A,B,C]', '[B,B,A]', 0, '(ATList_wrongentries 1, 3).', ''],
        ['SubstEquiv', '', '[1,[A,B],C]', '[1,[a,b],C]', 1, 'ATSubstEquiv_Subst [A = a,B = b,C = C].', ''],
        ['SubstEquiv', '', '{x^2+1,x^2}', '{A^2+1,A^2}', 1, 'ATSubstEquiv_Subst [x = A].', 'Sets'],
        ['SubstEquiv', '', '{x^2-1,x^2}', '{A^2+1,A^2}', 0, 'ATSet_wrongentries.', ''],
        ['SubstEquiv', '', '{A+1,B^2,C}', '{B,C+1,A^2}', 1, 'ATSubstEquiv_Subst [A = C,B = A,C = B].', ''],
        ['SubstEquiv', '', '{1,{A,B},C}', '{1,{a,b},C}', 1, 'ATSubstEquiv_Subst [A = a,B = b,C = C].', ''],
        // Will not match since x in the teacher's answer is fixed here.
        ['SubstEquiv', '[x]', 'y=A+B', 'x=a+b', 0, 'ATEquation_default', ''],
        ['SubstEquiv', '[z]', 'y=A+B', 'x=a+b', 1, 'ATSubstEquiv_Subst [A = a,B = b,y = x].', ''],
        // Optional argument to fix some variables within an expression.
        ['SubstEquiv', '', 'A*cos(t)+B*sin(t)', 'P*cos(t)+Q*sin(t)', 1, 'ATSubstEquiv_Subst [A = P,B = Q,t = t].', ''],
        ['SubstEquiv', '', 'A*cos(t)+B*sin(t)', 'P*cos(x)+Q*sin(x)', 1, 'ATSubstEquiv_Subst [A = P,B = Q,t = x].', ''],
        // Fixes variables.
        [
            'SubstEquiv', '[x]', 'A*cos(x)+B*sin(x)', 'P*cos(x)+Q*sin(x)', 1, 'ATSubstEquiv_Subst [A = P,B = Q].',
            'Fix some variables.',
        ],
        ['SubstEquiv', '[x]', 'A*cos(t)+B*sin(t)', 'P*cos(x)+Q*sin(x)', 0, '', ''],
        ['SubstEquiv', '[t]', 'A*cos(t)+B*sin(t)', 'P*cos(x)+Q*sin(x)', 0, '', ''],
        ['SubstEquiv', '[z]', 'A*cos(t)+B*sin(t)', 'P*cos(x)+Q*sin(x)', 1, 'ATSubstEquiv_Subst [A = P,B = Q,t = x].', ''],
        [
            'SubstEquiv', '[x,t]', 'A*cos(t)*e^x+B*sin(t)*e^x+C*sin(2*x)+D*cos(2*x)',
            'P*cos(t)*e^x+Q*sin(t)*e^x+R*sin(2*x)+S*cos(2*x)', 1, 'ATSubstEquiv_Subst [A = P,B = Q,C = R,D = S].', '',
        ],
        // Fix one.
        ['SubstEquiv', '', 'sqrt(2*g*y)', 'sqrt(2*g*x)', 1, 'ATSubstEquiv_Subst [g = g,y = x].', ''],
        ['SubstEquiv', '[g]', 'sqrt(2*g*y)', 'sqrt(2*g*x)', 1, 'ATSubstEquiv_Subst [y = x].', ''],
        [
            'SubstEquiv', '[x]', 'C1*%e^x*sin(4*x)+C2*%e^x*cos(4*x)+C4*x*%e^-x+C3*%e^-x',
            'e^(x)*A*cos(4*x)+B*e^(x)*sin(4*x)+C*e^(-x)+D*x*e^(-x)', 1,
            'ATSubstEquiv_Subst [C1 = B,C2 = A,C3 = C,C4 = D].', '',
        ],
        [
            'SubstEquiv', '[x]', 'C1*%e^x*sin(4*x)+C2*%e^x*cos(4*x)+C4*x*%e^-x+C3*%e^-x',
            'C4*x*e^(-x)+e^(x)*C1*cos(4*x)+C2*e^(x)*sin(4*x)+C3*e^(-x)', 1,
            'ATSubstEquiv_Subst [C1 = C2,C2 = C1,C3 = C3,C4 = C4].', '',
        ],
        [
            'SubstEquiv', '[x]', 'C1*%e^x*sin(4*x)+C2*%e^x*cos(4*x)+C4*x*%e^-x+C3*%e^-x',
            'A*x*e^(-x)+e^(x)*B*cos(4*x)+C*e^(x)*sin(4*x)+D*e^(-x)', 1,
            'ATSubstEquiv_Subst [C1 = C,C2 = B,C3 = D,C4 = A].', '',
        ],
        [
            'SubstEquiv', '[x]', 'C1*%e^x*sin(4*x)+C2*%e^x*cos(4*x)+C4*x*%e^-x+C3*%e^-x',
            'e^(x)*C1*cos(4*x)+C2*e^(x)*sin(4*x)+C3*e^(-x)+C4*x*e^(-x)', 1,
            'ATSubstEquiv_Subst [C1 = C2,C2 = C1,C3 = C3,C4 = C4].', '',
        ],

        ['EqualComAss', '', '1/0', '0', -1, 'ATEqualComAss_STACKERROR_SAns.', ''],
        ['EqualComAss', '', '0', '1/0', -1, 'ATEqualComAss_STACKERROR_TAns.', ''],
        ['EqualComAss', '', '2/4', '1/2', 0, 'ATEqualComAss (AlgEquiv-true).', 'Numbers'],
        ['EqualComAss', '', '3^2', '8', 0, 'ATEqualComAss (AlgEquiv-false).', ''],
        ['EqualComAss', '', '3^2', '9', 0, 'ATEqualComAss (AlgEquiv-true).', ''],
        ['EqualComAss', '', 'cos(0)', '1', 0, 'ATEqualComAss (AlgEquiv-true).', ''],
        ['EqualComAss', '', '4^(1/2)', '2', 0, 'ATEqualComAss (AlgEquiv-true).', ''],
        ['EqualComAss', '', '1/3^(1/2)', '(1/3)^(1/2)', 0, 'ATEqualComAss (AlgEquiv-true).', ''],
        ['EqualComAss', '', 'sqrt(3)/3', '(1/3)^(1/2)', 0, 'ATEqualComAss (AlgEquiv-true).', ''],
        ['EqualComAss', '', 'sqrt(3)', '3^(1/2)', 0, 'ATEqualComAss (AlgEquiv-true).', ''],
        ['EqualComAss', '', '2*sqrt(2)', 'sqrt(8)', 0, 'ATEqualComAss (AlgEquiv-true).', ''],
        ['EqualComAss', '', '2*2^(1/2)', 'sqrt(8)', 0, 'ATEqualComAss (AlgEquiv-true).', ''],
        ['EqualComAss', '', 'sqrt(2)/4', '1/sqrt(8)', 0, 'ATEqualComAss (AlgEquiv-true).', ''],
        ['EqualComAss', '', '1/sqrt(2)', '2^(1/2)/2', 0, 'ATEqualComAss (AlgEquiv-true).', ''],
        ['EqualComAss', '', '4.0', '4', 0, 'ATEqualComAss (AlgEquiv-true).', ''],
        ['EqualComAss', '', 'X', 'x', 0, 'ATEqualComAss (AlgEquiv-false)ATAlgEquiv_WrongCase.', 'Case sensitivity'],
        ['EqualComAss', '', '1/(R-r)', '1', 0, 'ATEqualComAss (AlgEquiv-false).', ''],
        ['EqualComAss', '', 'exdowncase(X)', 'x', 1, '', ''],
        ['EqualComAss', '', 'exdowncase((X-1)^2)', 'x^2-2*x+1', 0, 'ATEqualComAss (AlgEquiv-true).', ''],
        ['EqualComAss', '', 'exdowncase(X^2-2*X+1)', 'x^2-2*x+1', 1, '', ''],
        ['EqualComAss', '', 'a^2/b^3', 'a^2*b^(-3)', 0, 'ATEqualComAss (AlgEquiv-true).', 'Powers'],
        [
            'EqualComAss', '', 'lg(a^x,a)', 'x', 0,
            'ATEqualComAss (AlgEquiv-true).', '',
        ],
        [
            'EqualComAss', '', 'x^(2/4)', 'x^(1/2)', 0,
            'ATEqualComAss (AlgEquiv-true).', '',
        ],
        ['EqualComAss', '', '1+2*x', 'x*2+1', 1, '', 'Simple polynomials'],
        ['EqualComAss', '', '1+x', '2*x+1', 0, 'ATEqualComAss (AlgEquiv-false).', ''],
        ['EqualComAss', '', '1+x+x', '2*x+1', 0, 'ATEqualComAss (AlgEquiv-true).', ''],
        ['EqualComAss', '', '(x+y)+z', 'z+x+y', 1, '', ''],
        ['EqualComAss', '', 'x*x', 'x^2', 0, 'ATEqualComAss (AlgEquiv-true).', ''],
        ['EqualComAss', '', '(x+5)*x', 'x*(5+x)', 1, '', ''],
        ['EqualComAss', '', 'x*(x+5)', '5*x+x^2', 0, 'ATEqualComAss (AlgEquiv-true).', ''],
        ['EqualComAss', '', '(1-x)^2', '(x-1)^2', 0, 'ATEqualComAss (AlgEquiv-true).', ''],
        ['EqualComAss', '', '(a-x)^6000', '(x-a)^6000', 0, 'ATEqualComAss (AlgEquiv-true).', ''],
        [
            'EqualComAss', '', 'rho*z*V/(4*pi*epsilon[0]*(R^2+z^2)^(3/2))', 'rho*z*V/(4*pi*epsilon[0]*(R^2+z^2)^(3/2))', 1, '',
            'Expressions with subscripts',
        ],
        [
            'EqualComAss', '', 'rho*z*V/(4*pi*epsilon[1]*(R^2+z^2)^(3/2))', 'rho*z*V/(4*pi*epsilon[0]*(R^2+z^2)^(3/2))', 0,
            'ATEqualComAss (AlgEquiv-false).', '',
        ],
        ['EqualComAss', '', '+1-2', '1-2', 1, '', 'Unary plus'],
        ['EqualComAss', '', '-1+2', '2-1', 1, '', 'Unary minus'],
        ['EqualComAss', '', '-1*2+3*4', '3*4-1*2', 1, '', ''],
        ['EqualComAss', '', '(-1*2)+3*4', '10', 0, 'ATEqualComAss (AlgEquiv-true).', ''],
        ['EqualComAss', '', '-1*2+3*4', '3*4-1*2', 1, '', ''],
        ['EqualComAss', '', 'x*(-y)', '-x*y', 1, '', ''],
        ['EqualComAss', '', 'x*(-y)', '-(x*y)', 1, '', ''],
        ['EqualComAss', '', '(-x)*(-x)', 'x*x', 0, 'ATEqualComAss (AlgEquiv-true).', ''],
        ['EqualComAss', '', '(-x)*(-x)', 'x^2', 0, 'ATEqualComAss (AlgEquiv-true).', ''],
        ['EqualComAss', '', '-1/4*%pi*i', '-(%i*%pi)/4', 0, 'ATEqualComAss (AlgEquiv-true).', ''],
        ['EqualComAss', '', '1/2', '3/6', 0, 'ATEqualComAss (AlgEquiv-true).', 'Rational expressions'],
        ['EqualComAss', '', '1/(1+2*x)', '1/(2*x+1)', 1, '', ''],
        ['EqualComAss', '', '2/(4+2*x)', '1/(x+2)', 0, 'ATEqualComAss (AlgEquiv-true).', ''],
        ['EqualComAss', '', '(a*b)/c', 'a*(b/c)', 1, '', ''],
        // Non-trivial example of the above case.
        ['EqualComAss', '', '((x+1)/(x*(x-1)))*(x-1)', '((x+1)*(x-1))/(x*(x-1))', 1, '', ''],
        ['EqualComAss', '', '(-x)/y', '-(x/y)', 1, '', ''],
        ['EqualComAss', '', 'x/(-y)', '-(x/y)', 0, 'ATEqualComAss (AlgEquiv-true).', ''],
        ['EqualComAss', '', '-1/(1-x)', '1/(x-1)', 0, 'ATEqualComAss (AlgEquiv-true).', ''],
        ['EqualComAss', '', '1/2*1/x', '1/(2*x)', 0, 'ATEqualComAss (AlgEquiv-true).', ''],
        ['EqualComAss', '', '(k+8)/(k^2+4*k-12)', '(k+8)/(k^2+4*k-12)', 1, '', ''],
        ['EqualComAss', '', '(k+8)/(k^2+4*k-12)', '(k+8)/((k-2)*(k+6))', 0, 'ATEqualComAss (AlgEquiv-true).', ''],
        ['EqualComAss', '', '(k+7)/(k^2+4*k-12)', '(k+8)/(k^2+4*k-12)', 0, 'ATEqualComAss (AlgEquiv-false).', ''],
        ['EqualComAss', '', '-(2*k+6)/(k^2+4*k-12)', '-(2*k+6)/(k^2+4*k-12)', 1, '', ''],
        ['EqualComAss', '', '(a+b)/1', '(b+a)/1', 1, '', ''],
        ['EqualComAss', '', '1*x', 'x', 0, 'ATEqualComAss (AlgEquiv-true).', 'No simplicifcation here'],
        ['EqualComAss', '', '23+0*x', '23', 0, 'ATEqualComAss (AlgEquiv-true).', ''],
        ['EqualComAss', '', 'x+0', 'x', 0, 'ATEqualComAss (AlgEquiv-true).', ''],
        ['EqualComAss', '', 'x^1', 'x', 0, 'ATEqualComAss (AlgEquiv-true).', ''],
        ['EqualComAss', '', '(1/2)*(a+b)', '(a+b)/2', 0, 'ATEqualComAss (AlgEquiv-true).', ''],
        ['EqualComAss', '', '1/3*logbase(27,6)', 'logbase(27,6)/3', 0, 'ATEqualComAss (AlgEquiv-true).', ''],
        ['EqualComAss', '', '1/3*lg(27,6)', 'lg(27,6)/3', 0, 'ATEqualComAss (AlgEquiv-true).', ''],
        ['EqualComAss', '', 'lg(root(x, n))', 'lg(x, 10)/n', 0, 'ATEqualComAss (AlgEquiv-true).', ''],
        ['EqualComAss', '', 'exp(x)', '%e^x', 1, '', ''],
        ['EqualComAss', '', 'exp(x)^2', '%e^(2*x)', 0, 'ATEqualComAss (AlgEquiv-true).', ''],
        ['EqualComAss', '', 'exp(x)^2', '(%e^(x))^2', 1, '', ''],
        ['EqualComAss', '', '1/3*i', 'i/3', 0, 'ATEqualComAss (AlgEquiv-true).', ''],
        ['EqualComAss', '', '%i', 'e^(i*pi/2)', 0, 'ATEqualComAss (AlgEquiv-true).', 'Complex numbers'],
        [
            'EqualComAss', '', '(4*sqrt(3)*%i+4)^(1/5)', 'rectform((4*sqrt(3)*%i+4)^(1/5))', 0,
            'ATEqualComAss (AlgEquiv-true).', '',
        ],
        [
            'EqualComAss', '', '(4*sqrt(3)*%i+4)^(1/5)', '8^(1/5)*(cos(%pi/15)+%i*sin(%pi/15))', 0,
            'ATEqualComAss (AlgEquiv-true).', '',
        ],
        [
            'EqualComAss', '', '(4*sqrt(3)*%i+4)^(1/5)', 'polarform((4*sqrt(3)*%i+4)^(1/5))', 0,
            'ATEqualComAss (AlgEquiv-true).', '',
        ],
        ['EqualComAss', '', 'y=x', 'x=y', 1, '', 'Equations'],
        ['EqualComAss', '', 'x+1', 'y=2*x+1', 0, 'ATEqualComAss ATAlgEquiv_SA_not_equation.', ''],
        ['EqualComAss', '', 'y=1+2*x', 'y=2*x+1', 1, '', ''],
        ['EqualComAss', '', 'y=x+x+1', 'y=1+2*x', 0, 'ATEqualComAss (AlgEquiv-true).', ''],
        ['EqualComAss', '', 'A and B', 'B and A', 1, '', 'Logic'],
        ['EqualComAss', '', 'A or B', 'B or A', 1, '', ''],
        ['EqualComAss', '', 'A or B', 'B and A', 0, 'ATEqualComAss (AlgEquiv-false).', ''],
        ['EqualComAss', '', 'not(true)', 'false', 0, 'ATEqualComAss (AlgEquiv-true).', ''],
        ['EqualComAss', '', '{2*x+1,2}', '{2, 1+x*2}', 1, '', 'Sets'],
        ['EqualComAss', '', '2', '{2}', 0, 'ATEqualComAss ATAlgEquiv_SA_not_set.', ''],
        ['EqualComAss', '', '{2*x+1, 1+1}', '{2, 1+x*2}', 0, 'ATEqualComAss (AlgEquiv-true).', ''],
        ['EqualComAss', '', '{1,2}', '{1,{2}}', 0, 'ATEqualComAss (AlgEquiv-false)ATSet_wrongentries.', ''],
        ['EqualComAss', '', '{4,3}', '{3,4}', 1, '', ''],
        ['EqualComAss', '', '{4,4}', '{4}', 0, 'ATEqualComAss (AlgEquiv-true).', ''],
        ['EqualComAss', '', '{-1,1,-1}', '{-1,-1,1}', 1, '', ''],
        ['EqualComAss', '', '{-1,1,-1}', '{-1,1}', 0, 'ATEqualComAss (AlgEquiv-true).', ''],
        ['EqualComAss', '', '[2*x+1,2]', '[1+x*2,2]', 1, '', 'Lists'],
        ['EqualComAss', '', '[x+x+1, 1+1]', '[1+x*2,2]', 0, 'ATEqualComAss (AlgEquiv-true).', ''],
        ['EqualComAss', '', 'matrix([1,2],[2,3])', 'matrix([1,2],[2,3])', 1, '', 'Matrices'],
        [
            'EqualComAss', '', 'matrix([1,2],[2,3])', 'matrix([1,2,3],[2,3,3])', 0,
            'ATEqualComAss (AlgEquiv-false)ATMatrix_wrongsz_columns.', '',
        ],
        [
            'EqualComAss', '', 'matrix([1,2],[2,3])', 'matrix([1,2],[2,5])', 0,
            'ATEqualComAss (AlgEquiv-false)ATMatrix_wrongentries.', '',
        ],
        ['EqualComAss', '', 'matrix([1,2],[2,2+1])', 'matrix([1,2],[2,3])', 0, 'ATEqualComAss (AlgEquiv-true).', ''],
        ['EqualComAss', '', 'matrix([x+x, 1],[1, 1])', 'matrix([2*x, 1],[1, 1])', 0, 'ATEqualComAss (AlgEquiv-true).', ''],
        ['EqualComAss', '', 'sum(k^n,n,0,3)', 'sum(k^n,n,0,3)', 1, '', 'Sums and products'],
        ['EqualComAss', '', '1+k+k^2+k^3', 'sum(k^n,n,0,3)', 0, 'ATEqualComAss (AlgEquiv-true).', ''],
        ['EqualComAss', '', 'sum(k,k,0,1+n)', 'sum(k,k,0,n+1)', 1, '', ''],
        ['EqualComAss', '', '(n+1)*(n+2)/2', 'sum(k,k,0,n+1)', 0, 'ATEqualComAss (AlgEquiv-true).', ''],
        ['EqualComAss', '', 'product(cos(k*x),k,1,3)', 'product(cos(k*x),k,1,3)', 1, '', ''],
        ['EqualComAss', '', 'cos(x)*cos(2*x)*cos(3*x)', 'product(cos(k*x),k,1,3)', 0, 'ATEqualComAss (AlgEquiv-true).', ''],
        [
            'EqualComAss', '', '-6/5 > x', 'x < -6/5', 0, 'ATEqualComAss (AlgEquiv-true).',
            'Inequalities are not commutative under this test',
        ],
        ['EqualComAss', '', 'x<1 and -3<x', '-3<x and x<1', 1, '', ''],
        ['EqualComAss', '', '1>x and -3<x', '-3<x and x<1', 0, 'ATEqualComAss (AlgEquiv-true).', ''],
        ['EqualComAss', '', 'make_less_ineq(-6/5 > x)', 'x < -6/5', 1, '', ''],
        ['EqualComAss', '', 'make_less_ineq(1>x and -3<x)', '-3<x and x<1', 1, '', ''],
        ['EqualComAss', '', 'make_less_ineq(6/3 > x)', 'x < 2', 0, 'ATEqualComAss (AlgEquiv-true).', ''],
        ['EqualComAss', '', '1', 'stackeq(1)', 1, '', 'Unary Equations'],
        ['EqualComAss', '', 'stackeq(1)', '1', 1, '', ''],
        ['EqualComAss', '', 'stackeq(1+1)', '2', 0, 'ATEqualComAss (AlgEquiv-true).', ''],
        ['EqualComAss', '', 'stackeq(1)', '0', 0, 'ATEqualComAss (AlgEquiv-false).', ''],

        ['EqualComAss', '', 'lowesttermsp(1/3)', 'true', 1, '', ''],
        ['EqualComAss', '', 'lowesttermsp(2/6)', 'true', 0, 'ATEqualComAss (AlgEquiv-false).', ''],
        ['EqualComAss', '', 'lowesttermsp(x^2/x)', 'true', 0, 'ATEqualComAss (AlgEquiv-false).', ''],
        ['EqualComAss', '', 'lowesttermsp(-y/-x)', 'true', 0, 'ATEqualComAss (AlgEquiv-false).', ''],
        ['EqualComAss', '', 'lowesttermsp((x^2-1)/(x-1))', 'true', 0, 'ATEqualComAss (AlgEquiv-false).', ''],
        ['EqualComAss', '', 'lowesttermsp((x^2-1)/(x+2))', 'true', 1, '', ''],

        ['EqualComAss', '', 'scientific_notationp(1/3)', 'true', 0, 'ATEqualComAss (AlgEquiv-false).', ''],
        ['EqualComAss', '', 'scientific_notationp(4.1561*10^16)', 'true', 1, '', ''],

        // We can't use ATAlgEquiv with rationalized as Maxima simplified sqrt(3)/3 to 1/sqrt(3).
        ['EqualComAss', '', 'rationalized(1+sqrt(3)/3)', 'true', 1, '', 'Bad things in denominators'],
        ['EqualComAss', '', 'rationalized(1+1/sqrt(3))', '[sqrt(3)]', 1, '', ''],
        ['EqualComAss', '', 'rationalized(1/sqrt(3))', '[sqrt(3)]', 1, '', ''],
        ['EqualComAss', '', 'rationalized(1/sqrt(2)+i/sqrt(2))', '[sqrt(2),sqrt(2)]', 1, '', ''],
        ['EqualComAss', '', 'rationalized(sqrt(2)/2+1/sqrt(3))', '[sqrt(3)]', 1, '', ''],
        ['EqualComAss', '', 'rationalized(1/sqrt(2)+1/sqrt(3))', '[sqrt(2),sqrt(3)]', 1, '', ''],
        ['EqualComAss', '', 'rationalized(1/(1+i))', '[i]', 1, '', ''],
        ['EqualComAss', '', 'rationalized(1/(1+1/root(3,2)))', '[root(3,2)]', 1, '', ''],

        ['EqualComAss', '', 'B nounand A', 'A nounand B', 1, '', 'Logic'],
        ['EqualComAss', '', 'A nounand A', 'A', 0, 'ATEqualComAss ATAlgEquiv_SA_not_expression.', ''],
        ['EqualComAss', '', 'subst(["*"="nounand", "+"="nounor","!"="nounnot"], A*B)', 'A nounand B', 1, '', ''],

        // Differential equations.
        // Functions are evaluated with simp:false.
        ['EqualComAss', '', 'diff(y,x)', '0', 1, '', 'Differential Equations'],
        ['EqualComAss', '', 'diff(x^2,x)', '2*x', 1, '', ''],
        ['EqualComAss', '', 'noundiff(x^2,x)', '2*x', 0, 'ATEqualComAss (AlgEquiv-true).', ''],
        ['EqualComAss', '', 'diff(y,x)', '\'diff(y,x)', 0, 'ATEqualComAss (AlgEquiv-true).', ''],
        ['EqualComAss', '', 'noundiff(y,x)', '\'diff(y,x)', 1, '', ''],
        ['EqualComAss', '', '\'diff(y(x),x)', '\'diff(y(x),x,1)', 1, '', ''],
        // Test case to illustrate why we need a new answer test.
        // These equations are not the same up to commutativity and associativity, because the algebra needed.
        ['EqualComAss', '', 'noundiff(y(x),x)=-x/4', '4*noundiff(y(x),x)+x=0', 0, 'ATEqualComAss (AlgEquiv-true).', ''],

        ['EqualComAssRules', '[]', '1/0', '0', -1, 'ATEqualComAssRules_STACKERROR_SAns.', ''],
        ['EqualComAssRules', '[]', '0', '1/0', -1, 'ATEqualComAssRules_STACKERROR_TAns.', ''],
        ['EqualComAssRules', '', '0+a', 'a', -1, 'STACKERROR_OPTION.', ''],
        ['EqualComAssRules', 'x', '0+a', 'a', -1, 'ATEqualComAssRules_Opt_List.', ''],
        ['EqualComAssRules', '[x]', '0+a', 'a', -1, 'ATEqualComAssRules_Opt_Wrong.', ''],
        ['EqualComAssRules', '[intMul,intFac]', '0+a', 'a', -1, 'ATEqualComAssRules_Opt_Incompatible.', ''],
        ['EqualComAssRules', '[zeroAdd]', '1+1', '3', 0, 'ATEqualComAssRules (AlgEquiv-false).', 'Basic cases'],
        ['EqualComAssRules', '[zeroAdd]', '1+1', '2', 0, '', ''],
        ['EqualComAssRules', '[testdebug,zeroAdd]', '1+1', '2', 0, 'ATEqualComAssRules: [1 nounadd 1,2].', ''],
        ['EqualComAssRules', '[zeroAdd]', '0+a', 'a', 1, '', ''],
        ['EqualComAssRules', '[zeroAdd]', 'a+0', 'a', 1, '', ''],
        ['EqualComAssRules', '[testdebug,zeroAdd]', '1*a', 'a', 0, 'ATEqualComAssRules: [1 nounmul a,a].', ''],
        // This is a common example where EqualComAss is not adequate.
        ['EqualComAssRules', '[zeroAdd]', '1/2*sin(3*x)', 'sin(3*x)/2', 0, '', ''],
        ['EqualComAssRules', '[oneMul]', '1/2*sin(3*x)', 'sin(3*x)/2', 1, '', ''],
        ['EqualComAssRules', '[oneMul]', '1*a', 'a', 1, '', ''],
        ['EqualComAssRules', 'ID_TRANS', '1*a', 'a', 1, '', ''],
        ['EqualComAssRules', 'ID_TRANS', 'a/1', 'a', 1, '', ''],
        ['EqualComAssRules', 'ID_TRANS', '0*a', '0', 1, '', ''],
        ['EqualComAssRules', 'ID_TRANS', '0-1*i', '-i', 1, '', ''],
        ['EqualComAssRules', 'ID_TRANS', '0-i', '-i', 1, '', ''],
        ['EqualComAssRules', 'ID_TRANS', '2+1*i', '2+i', 1, '', ''],
        ['EqualComAssRules', 'ID_TRANS', 'x^0+x^1/1+x^2/2+x^3/3!+x^4/4!', '1+x+x^2/2+x^3/3!+x^4/4!', 1, '', ''],
        // Illustrate the difference between exp and e^x.
        [
            'EqualComAssRules', '[testdebug,ID_TRANS]', '%e^x', 'exp(x)', 1,
            'ATEqualComAssRules: [%e nounpow x,%e nounpow x].', '',
        ],
        ['EqualComAssRules', 'ID_TRANS', '12*%e^((2*(%pi/2)*%i)/2)', '12*exp(%i*(%pi/2))', 0, '', ''],
        [
            'EqualComAssRules', '[ID_TRANS,[negNeg,negDiv,negOrd],' .
            '[recipMul,divDiv,divCancel],[intAdd,intMul,intPow]]', '12*%e^((2*(%pi/2)*%i)/2)', '12*exp(%i*(%pi/2))', 1, '', '',
        ],
        // This one is tricky.  1-1 is not literally zero here, so the rule zeroPow applies.
        // Try transl(0^(1-1),ID_TRANS); in the Maxima sandbox.
        // The answer test avoids this by throwing a Maxima error before the rules generate a problem.
        ['EqualComAssRules', 'ID_TRANS', '0^(1-1)', '0', 0, 'ATEqualComAssRules_STACKERROR_SAns.', ''],
        ['EqualComAssRules', 'delete(zeroMul, ID_TRANS)', '0*a', '0', 0, '', ''],
        ['EqualComAssRules', '[negNeg]', '-(-a)', 'a', 1, '', ''],
        ['EqualComAssRules', '[negNeg]', '-(-(-a))', '-a', 1, '', ''],
        ['EqualComAssRules', '[testdebug,negNeg]', '-(-(-a))', 'a', 0, 'ATEqualComAssRules (AlgEquiv-false).', ''],
        ['EqualComAssRules', 'ID_TRANS', '3/(-x)', '-3/x', 0, '', ''],
        [
            'EqualComAssRules', '[testdebug,ID_TRANS]', '3/(-x)', '-3/x', 0,
            'ATEqualComAssRules: [3 nounmul UNARY_RECIP UNARY_MINUS nounmul x,UNARY_MINUS nounmul 3 nounmul UNARY_RECIP x].', '',
        ],
        ['EqualComAssRules', '[negDist]', '-x*(x+1)', 'x*(-x-1)', 1, '', ''],
        ['EqualComAssRules', 'NEG_TRANS', '-x*(x-1)', 'x*(1-x)', 1, '', ''],
        ['EqualComAssRules', 'NEG_TRANS', '-x*(x-1)', 'x*(1-x)', 1, '', ''],
        ['EqualComAssRules', 'NEG_TRANS', '-5*x*(3-x)', '5*x*(x-3)', 1, '', ''],
        ['EqualComAssRules', 'NEG_TRANS', '-x*(x-1)*(x+1)', 'x*(x-1)*(-x-1)', 1, '', ''],
        ['EqualComAssRules', 'NEG_TRANS', '-x*(x-1)*(x+1)', 'x*(1-x)*(x+1)', 1, '', ''],
        ['EqualComAssRules', 'NEG_TRANS', '-x*(y-1)*(x-1)', 'x*(1-x)*(y-1)', 1, '', ''],
        ['EqualComAssRules', 'NEG_TRANS', '-x*(y-1)*(x-1)', 'x*(x-1)*(1-y)', 1, '', ''],
        ['EqualComAssRules', 'NEG_TRANS', '(x-y)*(y-x)', '-(x-y)*(x-y)', 1, '', ''],
        [
            'EqualComAssRules', '[testdebug,NEG_TRANS]', '(x-y)*(y-x)', '-(x-y)^2', 0,
            'ATEqualComAssRules: [UNARY_MINUS nounmul (x nounadd UNARY_MINUS nounmul y) nounmul ' .
            '(x nounadd UNARY_MINUS nounmul y),UNARY_MINUS nounmul (x nounadd UNARY_MINUS nounmul y) nounpow 2].', '',
        ],
        // These examples illustrate the problem with distribution (which is not confluent) and factoring (which is)!
        [
            'EqualComAssRules', '[testdebug,negDist,negNeg]', '-x*(x-1)*(x+1)', 'x*(1-x)*(x+1)', 0,
            'ATEqualComAssRules: [x nounmul (UNARY_MINUS nounmul 1 nounadd UNARY_MINUS nounmul x) nounmul ' .
            '(x nounadd UNARY_MINUS nounmul 1),x nounmul (1 nounadd UNARY_MINUS nounmul x) nounmul (1 nounadd x)].', '',
        ],
        [
            'EqualComAssRules', '[testdebug,negDist,negNeg]', '-x*(y-1)*(x-1)', 'x*(x-1)*(1-y)', 0,
            'ATEqualComAssRules: [x nounmul (1 nounadd UNARY_MINUS nounmul x) nounmul (y nounadd UNARY_MINUS nounmul 1),' .
            'x nounmul (1 nounadd UNARY_MINUS nounmul y) nounmul (x nounadd UNARY_MINUS nounmul 1)].', '',
        ],
        ['EqualComAssRules', '[negDiv]', '3/(-x)', '-3/x', 1, '', ''],
        // When an expression comes from a previously simplified expression.
        ['EqualComAssRules', '[negDiv]', '3/(-x)', 'ev(-3,simp)/x', 1, '', ''],
        [
            'EqualComAssRules', '[testdebug,ID_TRANS]', '(-a)/(-x)', '-(-a/x)', 0,
            'ATEqualComAssRules: [UNARY_MINUS nounmul a nounmul UNARY_RECIP UNARY_MINUS nounmul x,' .
                                 'UNARY_MINUS nounmul UNARY_MINUS nounmul a nounmul UNARY_RECIP x].', '',
        ],
        ['EqualComAssRules', '[negDiv]', '(-a)/(-x)', '-(-a/x)', 1, '', ''],
        [
            'EqualComAssRules', '[testdebug,negDiv]', '(-a)/(-x)', 'a/x', 0,
            'ATEqualComAssRules: [UNARY_MINUS nounmul UNARY_MINUS nounmul a nounmul UNARY_RECIP x,a nounmul UNARY_RECIP x].', '',
        ],
        ['EqualComAssRules', '[negDiv,negNeg]', '(-a)/(-x)', 'a/x', 1, '', ''],
        // The following passes because the (-1) is parsed as ,1 nounmul UNARY_MINUS.
        ['EqualComAssRules', '[negDiv]', '1/(-x)', '(-1)/x', 1, '', ''],
        ['EqualComAssRules', '[negDiv]', '1/(-x)', 'ev(-1,simp)/x', 1, '', ''],
        ['EqualComAssRules', '[negDiv]', '(2/-3)*(x-y)', '-(2/3)*(x-y)', 1, '', ''],
        ['EqualComAssRules', '[negDiv]', '(2/-3)*(x-y)', '(2/3)*(y-x)', 0, '', ''],
        ['EqualComAssRules', '[negDiv,negOrd]', '(2/-3)*(x-y)', '(2/3)*(y-x)', 1, '', ''],
        [
            'EqualComAssRules', '[testdebug,negDiv]', '-2/(1-x)', '2/(x-1)', 0,
            'ATEqualComAssRules: [UNARY_MINUS nounmul 2 nounmul UNARY_RECIP (1 nounadd UNARY_MINUS nounmul x),' .
                                 '2 nounmul UNARY_RECIP (x nounadd UNARY_MINUS nounmul 1)].', '',
        ],
        [
            'EqualComAssRules', '[testdebug,ID_TRANS]', '1/2*3/x', '3/(2*x)', 0,
            'ATEqualComAssRules: [3 nounmul (UNARY_RECIP 2) nounmul UNARY_RECIP x,3 nounmul UNARY_RECIP 2 nounmul x].', '',
        ],
        ['EqualComAssRules', '[ID_TRANS,recipMul]', '1/2*3/x', '3/(2*x)', 1, '', ''],
        [
            'EqualComAssRules', '[testdebug,ID_TRANS,recipMul]', '5/2*3/x', '15/(2*x)', 0,
            'ATEqualComAssRules: [3 nounmul 5 nounmul UNARY_RECIP 2 nounmul x,15 nounmul UNARY_RECIP 2 nounmul x].', '',
        ],
        ['EqualComAssRules', '[negOrd]', '-(x-y)', 'y-x', 1, '', ''],
        ['EqualComAssRules', '[ID_TRANS,recipMul,intMul]', '5/2*3/x', '15/(2*x)', 1, '', ''],
        ['EqualComAssRules', '[ID_TRANS,intAdd]', '(3+2)*x+x', '5*x+x', 1, '', ''],
        ['EqualComAssRules', '[ID_TRANS,intAdd]', '(3-5)*x+x', '-2*x+x', 1, '', ''],
        ['EqualComAssRules', '[ID_TRANS,intMul]', '7*x*(-3*x)', '-21*x*x', 1, '', ''],
        [
            'EqualComAssRules', '[testdebug,ID_TRANS,intMul]', '(-7*x)*(-3*x)', '21*x*x', 0,
            'ATEqualComAssRules: [UNARY_MINUS nounmul UNARY_MINUS nounmul 21 nounmul x nounmul x,21 nounmul x nounmul x].', '',
        ],
        ['EqualComAssRules', '[ID_TRANS,intMul,negNeg]', '(-7*x)*(-3*x)', '21*x*x', 1, '', ''],
        // This next example is parsing rules.  In Maxima ev(a/b/c, simp)=a/(b*c).
        [
            'EqualComAssRules', '[testdebug,ID_TRANS]', 'a/b/c', 'a/(b*c)', 0,
            'ATEqualComAssRules: [a nounmul (UNARY_RECIP b) nounmul UNARY_RECIP c,a nounmul UNARY_RECIP b nounmul c].',
            'ev(a/b/c, simp)=a/(b*c)',
        ],
        ['EqualComAssRules', '[ID_TRANS,recipMul]', 'a/b/c', 'a/(b*c)', 1, '', ''],
        ['EqualComAssRules', '[ID_TRANS,recipMul]', '(a/b)/c', 'a/(b*c)', 1, '', ''],
        // This next example is parsing rules.  In Maxima ev(a/(b/c), simp)=(a*c)/b.
        [
            'EqualComAssRules', '[testdebug,ID_TRANS]', 'a/(b/c)', '(a*c)/b', 0,
            'ATEqualComAssRules: [a nounmul UNARY_RECIP b nounmul UNARY_RECIP c,a nounmul c nounmul UNARY_RECIP b].',
            'ev(a/(b/c), simp)=(a*c)/b',
        ],
        [
            'EqualComAssRules', '[testdebug,ID_TRANS,recipMul]', 'a/(b/c)', '(a*c)/b', 0,
            'ATEqualComAssRules: [a nounmul UNARY_RECIP b nounmul UNARY_RECIP c,a nounmul c nounmul UNARY_RECIP b].', '',
        ],
        ['EqualComAssRules', '[ID_TRANS,divDiv]', 'a/(b/c)', '(a*c)/b', 1, '', ''],
        ['EqualComAssRules', '[ID_TRANS,divDiv]', 'A*a/(B*b/c)', 'A*(a*c)/(B*b)', 1, '', ''],
        ['EqualComAssRules', '[ID_TRANS,divDiv]', 'A*a/(B*b/c)*1/d', 'A*(a*c)/(B*b)*1/d', 1, '', ''],
        ['EqualComAssRules', '[ID_TRANS,divDiv]', 'D*A*a/(B*b/c)*1/d', 'A*(a*c)/(B*b)*D/d', 1, '', ''],
        [
            'EqualComAssRules', '[testdebug,ID_TRANS,divDiv]', 'A*a/(B*b/c)*1/d', 'A*(a*c)/(B*b*d)', 0,
            'ATEqualComAssRules: [A nounmul a nounmul c nounmul (UNARY_RECIP B nounmul b) nounmul UNARY_RECIP d,' .
                                 'A nounmul a nounmul c nounmul UNARY_RECIP B nounmul b nounmul d].', '',
        ],
        ['EqualComAssRules', '[ID_TRANS,divDiv,recipMul]', 'A*a/(B*b/c)*1/d', 'A*(a*c)/(B*b*d)', 1, '', ''],
        [
            'EqualComAssRules', '[testdebug,ID_TRANS,divDiv]', 'A/(B/(C/D))', 'A*C/(B*D)', 0,
            'ATEqualComAssRules: [A nounmul C nounmul (UNARY_RECIP B) nounmul UNARY_RECIP D,' .
                                 'A nounmul C nounmul UNARY_RECIP B nounmul D].', '',
        ],
        ['EqualComAssRules', '[ID_TRANS,divDiv,recipMul]', 'A/(B/(C/D))', 'A*C/(B*D)', 1, '', ''],
        ['EqualComAssRules', '[intFac]', '18', '2*3^2', 1, '', ''],
        [
            'EqualComAssRules',
            '[[zeroAdd,zeroMul,oneMul,onePow,idPow,zeroPow,zPow,oneDiv],' .
            '[negNeg,negDiv,negOrd],[recipMul,divDiv,divCancel],[intAdd,intMul,intPow]]',
            '0+%i*(-(1/27))', '-(%i/27)', 1, '', '',
        ],
        ['EqualComAssRules', '[ID_TRANS,sqrtRem]', 'x=sqrt(3)+2', 'x=3^(1/2)+2', 1, '', ''],
        [
            'EqualComAssRules', 'ID_TRANS', 'x=sqrt(3)+2 nounor x=-sqrt(3)-2', 'x=3^(1/2)+2 nounor x=-3^(1/2)-2', 0,
            '', '',
        ],
        [
            'EqualComAssRules', '[ID_TRANS,sqrtRem]', 'x=sqrt(3)+2 nounor x=-sqrt(3)-2', 'x=3^(1/2)+2 nounor x=-3^(1/2)-2', 1,
            '', '',
        ],
        [
            'EqualComAssRules', '[ID_TRANS,sqrtRem]', 'x=sqrt(3)+2 nounor x=-sqrt(3)+7', 'x=3^(1/2)+2 nounor x=-3^(1/2)-2', 0,
            'ATEqualComAssRules (AlgEquiv-false)ATEquation_default.', '',
        ],
        ['EqualComAssRules', '[ID_TRANS,sqrtRem]', '1/sqrt(3)', '1/3^(1/2)', 1, '', ''],
        ['EqualComAssRules', '[ID_TRANS,sqrtRem]', '1/sqrt(3)', '3^(-1/2)', 0, '', ''],

        ['CasEqual', '', '1/0', 'x^2-2*x+1', -1, 'ATCASEqual_STACKERROR_SAns.', ''],
        ['CasEqual', '', 'x', '1/0', -1, 'ATCASEqual_STACKERROR_TAns.', ''],
        ['CasEqual', 'x', '0.5', '1/2', 0, 'ATCASEqual (AlgEquiv-true).', ''],
        ['CasEqual', '', 'x=1', '1', 0, 'ATCASEqual ATAlgEquiv_TA_not_equation.', ''],
        ['CasEqual', '', 'a', 'A', 0, 'ATCASEqual_false.', 'Case sensitivity'],
        ['CasEqual', '', 'exdowncase(X^2-2*X+1)', 'x^2-2*x+1', 1, 'ATCASEqual_true.', ''],
        ['CasEqual', '', '4^(-1/2)', '1/2', 0, 'ATCASEqual (AlgEquiv-true).', 'Numbers'],
        ['CasEqual', '', 'ev(4^(-1/2),simp)', 'ev(1/2,simp)', 1, 'ATCASEqual_true.', ''],
        ['CasEqual', '', '2^2', '4', 0, 'ATCASEqual (AlgEquiv-true).', ''],
        // Below is the intended behaviour: these trees are not equal.
        ['CasEqual', '', '+1-2', '1-2', 0, 'ATCASEqual (AlgEquiv-true).', 'Unary plus'],
        ['CasEqual', '', 'a^2/b^3', 'a^2*b^(-3)', 0, 'ATCASEqual (AlgEquiv-true).', 'Powers'],
        [
            'CasEqual', '', 'rho*z*V/(4*pi*epsilon[0]*(R^2+z^2)^(3/2))', 'rho*z*V/(4*pi*epsilon[0]*(R^2+z^2)^(3/2))', 1,
            'ATCASEqual_true.', 'Expressions with subscripts',
        ],
        [
            'CasEqual', '', 'rho*z*V/(4*pi*epsilon[1]*(R^2+z^2)^(3/2))', 'rho*z*V/(4*pi*epsilon[0]*(R^2+z^2)^(3/2))', 0,
            'ATCASEqual_false.', '',
        ],
        ['CasEqual', '', '0.5', '1/2', 0, 'ATCASEqual (AlgEquiv-true).', 'Mix of floats and rational numbers'],
        ['CasEqual', '', 'x^(1/2)', 'sqrt(x)', 0, 'ATCASEqual (AlgEquiv-true).', ''],
        ['CasEqual', '', 'ev(x^(1/2),simp)', 'ev(sqrt(x),simp)', 1, 'ATCASEqual_true.', ''],
        ['CasEqual', '', 'abs(x)', 'sqrt(x^2)', 0, 'ATCASEqual (AlgEquiv-true).', ''],
        ['CasEqual', '', 'ev(abs(x),simp)', 'ev(sqrt(x^2),simp)', 1, 'ATCASEqual_true.', ''],
        ['CasEqual', '', 'x-1', '(x^2-1)/(x+1)', 0, 'ATCASEqual (AlgEquiv-true).', ''],
        ['CasEqual', '', 'x+x', '2*x', 0, 'ATCASEqual (AlgEquiv-true).', 'Polynomials and rational function'],
        ['CasEqual', '', 'ev(x+x,simp)', 'ev(2*x,simp)', 1, 'ATCASEqual_true.', ''],
        ['CasEqual', '', 'x+x^2', 'x^2+x', 0, 'ATCASEqual (AlgEquiv-true).', ''],
        ['CasEqual', '', 'ev(x+x^2,simp)', 'ev(x^2+x,simp)', 1, 'ATCASEqual_true.', ''],
        ['CasEqual', '', '(x-1)^2', 'x^2-2*x+1', 0, 'ATCASEqual (AlgEquiv-true).', ''],
        ['CasEqual', '', '(x-1)^(-2)', '1/(x^2-2*x+1)', 0, 'ATCASEqual (AlgEquiv-true).', ''],
        ['CasEqual', '', '1/n-1/(n+1)', '1/(n*(n+1))', 0, 'ATCASEqual (AlgEquiv-true).', ''],
        ['CasEqual', '', 'cos(x)', 'cos(-x)', 0, 'ATCASEqual (AlgEquiv-true).', 'Trig functions'],
        ['CasEqual', '', 'ev(cos(x),simp)', 'ev(cos(-x),simp)', 1, 'ATCASEqual_true.', ''],
        ['CasEqual', '', 'cos(x)^2+sin(x)^2', '1', 0, 'ATCASEqual (AlgEquiv-true).', ''],
        ['CasEqual', '', '2*cos(x)^2-1', 'cos(2*x)', 0, 'ATCASEqual (AlgEquiv-true).', ''],
        ['CasEqual', '', 'imag_numberp(2*%i)', 'true', 1, 'ATCASEqual_true.', 'Predicate function wrapper'],
        ['CasEqual', '', 'imag_numberp(%e^(%i*%pi/2))', 'true', 1, 'ATCASEqual_true.', ''],
        ['CasEqual', '', 'imag_numberp(2)', 'false', 1, 'ATCASEqual_true.', ''],
        ['CasEqual', '', 'imag_numberp(%e^(%pi/2))', 'false', 1, 'ATCASEqual_true.', ''],
        ['CasEqual', '', 'complex_exponentialp(3*%e^(%i*%pi/6))', 'true', 1, 'ATCASEqual_true.', ''],
        ['CasEqual', '', 'complex_exponentialp(3)', 'true', 1, 'ATCASEqual_true.', ''],
        ['CasEqual', '', 'complex_exponentialp(-3)', 'false', 1, 'ATCASEqual_true.', ''],
        ['CasEqual', '', 'complex_exponentialp(%e^(%i*%pi/6))', 'true', 1, 'ATCASEqual_true.', ''],
        ['CasEqual', '', 'complex_exponentialp(%e^%i)', 'true', 1, 'ATCASEqual_true.', ''],
        ['CasEqual', '', 'complex_exponentialp(%e^(%pi/6))', 'true', 1, 'ATCASEqual_true.', ''],
        ['CasEqual', '', 'complex_exponentialp(3+%i)', 'false', 1, 'ATCASEqual_true.', ''],
        ['CasEqual', '', 'complex_exponentialp(%e^(%i)/4)', 'true', 1, 'ATCASEqual_true.', ''],
        ['CasEqual', '', 'complex_exponentialp(3*exp(%i*%pi/6))', 'true', 1, 'ATCASEqual_true.', ''],
        ['CasEqual', '', 'complex_exponentialp(3*exp(-%i*%pi/6))', 'true', 1, 'ATCASEqual_true.', ''],
        // We must have -p1<theta<=pi.
        ['CasEqual', '', 'complex_exponentialp(3*%e^(-7*%i*%pi/3))', 'false', 1, 'ATCASEqual_true.', ''],
        ['CasEqual', '', 'complex_exponentialp(7*%e^(3*%i*%pi))', 'false', 1, 'ATCASEqual_true.', ''],
        // We must have r>0.
        ['CasEqual', '', 'complex_exponentialp(-3*exp(%i*%pi/6))', 'false', 1, 'ATCASEqual_true.', ''],
        ['CasEqual', '', 'complex_exponentialp(-(3*exp(%i*%pi/6)))', 'false', 1, 'ATCASEqual_true.', ''],
        // The below test case is 0 because this is a general expression with variables.
        ['CasEqual', '', 'complex_exponentialp(-(r*exp(i*atan(bb/aa))))', 'true', 0, 'ATCASEqual_false.', ''],
        // The below test is 0 because with simp:false, -1 is ((mminus) 1) so not an integer.
        ['CasEqual', '', 'integerp(-1)', 'true', 0, 'ATCASEqual_false.', ''],
        ['CasEqual', '', 'integerp(ev(-1,simp))', 'true', 1, 'ATCASEqual_true.', ''],

        ['SameType', '', '1/0', '1', -1, 'ATSameType_STACKERROR_SAns.', ''],
        ['SameType', '', '1', '1/0', -1, 'ATSameType_STACKERROR_TAns.', ''],
        ['SameType', '', '4^(-1/2)', '1/2', 1, '', 'Numbers'],
        ['SameType', '', 'x', '[1,2,3]', 0, '', 'Lists'],
        ['SameType', '', '[1,2]', '[1,2,3]', 1, '', ''],
        ['SameType', '', '[1,x>2]', '[1,2<x]', 1, '', ''],
        ['SameType', '', '[1,x,3]', '[1,2<x,4]', 0, '', ''],
        ['SameType', '', 'x', '{1,2,3}', 0, '', 'Sets'],
        ['SameType', '', '{1,2}', '{1,2,3}', 1, '', ''],
        ['SameType', '', 'matrix([1,2],[2,3])', 'matrix([1,2],[2,3])', 1, '', 'Matrices'],
        ['SameType', '', '[[1,2],[2,3]]', 'matrix([1,2],[2,3])', 0, '', ''],
        ['SameType', '', 'matrix([1,2],[2,3])', 'matrix([1,2,3],[2,3,3])', 1, '', ''],
        ['SameType', '', 'matrix([x>4,{1,x^2}],[[1,2],[1,3]])', 'matrix([4-x<0,{x^2, 1}],[[1,2],[1,3]])', 1, '', ''],
        ['SameType', '', 'matrix([x>4,[1,x^2]],[[1,2],[1,3]])', 'matrix([4-x<0,{x^2, 1}],[[1,2],[1,4]])', 0, '', ''],
        ['SameType', '', '1', 'x=1', 0, '', 'Equations'],
        ['SameType', '', 'x=1', 'x=1', 1, '', ''],
        ['SameType', '', '1', 'x>1', 0, '', 'Inequalities'],
        ['SameType', '', 'x>2', 'x>1', 1, '', ''],
        ['SameType', '', 'x>1', 'x>=1', 1, '', ''],
        ['SameType', '', 'x>1 and x<3', 'x>=1', 1, '', ''],
        ['SameType', '', '{x>1,x<3}', 'x>=1', 0, '', ''],
        [
            'SameType', '', 'sqrt(2)*sqrt(3)+2*(sqrt(2/3))*x-(2/3)*(sqrt(2/3))*x^2+(4/9)*(sqrt(2/3))*x^3',
            '4*sqrt(6)*x^3/27-(2*sqrt(6)*x^2)/9+(2*sqrt(6)*x)/3+sqrt(6)', 1, '', '',
        ],

        [
            'SysEquiv', '', '1/0', '[(x-1)*(x+1)=0]', -1, 'ATSysEquiv_STACKERROR_SAns.',
            'Basic tests',
        ],
        ['SysEquiv', '', '[(x-1)*(x+1)=0]', '1/0', -1, 'ATSysEquiv_STACKERROR_TAns.', ''],
        ['SysEquiv', '', '1', '[(x-1)*(x+1)=0]', 0, 'ATSysEquiv_SA_not_list.', ''],
        ['SysEquiv', '', '[(x-1)*(x+1)=0]', '1', 0, 'ATSysEquiv_SB_not_list.', ''],
        ['SysEquiv', '', '[1]', '[90=v*t,90=(v+5)*(t-1/4)]', 0, 'ATSysEquiv_SA_not_eq_list.', ''],
        ['SysEquiv', '', '[(x-1)*(x+1)=0]', '[1]', 0, 'ATSysEquiv_SB_not_eq_list.', ''],
        ['SysEquiv', '', '[x^2]', '[(x-1)*(x+1)=0]', 0, 'ATSysEquiv_SA_not_eq_list.', ''],
        [
            'SysEquiv', '', '[90=v*t^t,90=(v+5)*(t-1/4)]', '[90=v*t,90=(v+5)*(t-1/4)]', 0,
            'ATSysEquiv_SA_not_poly_eq_list.', '',
        ],
        [
            'SysEquiv', '', '[90=v*t,90=(v+5)*(t-1/4)]', '[90=v*t^t,90=(v+5)*(t-1/4)]', 0,
            'ATSysEquiv_SB_not_poly_eq_list.', '',
        ],
        ['SysEquiv', '', '[x^2=1]', '[(x-1)*(x+1)=0]', 1, '', 'Tests of equivalence'],
        ['SysEquiv', '', '[x^2+y^2=4,y=x]', '[y=x,y^2=2]', 1, '', ''],
        ['SysEquiv', '', '[x^2+y^2=2,y=x]', '[y=x,y^2=2]', 0, 'ATSysEquiv_SA_system_overdetermined.', ''],
        ['SysEquiv', '', '[x=1]', '[(x-1)*(x+1)=0,(x-1)*(x-3)=0]', 1, 'ATSysEquiv_SA_Completely_solved.', ''],
        ['SysEquiv', '', '[3*a+b-c=2, a-b+2*c=5,b+c=5]', '[a=1,b=2,c=3]', 1, '', ''],
        ['SysEquiv', '', '[a=1,b=2,c=3]', '[3*a+b-c=2, a-b+2*c=5,b+c=5]', 1, 'ATSysEquiv_SA_Completely_solved.', ''],
        ['SysEquiv', '', '[x^2=1]', '[(x-1)*(x+1)*(x-2)=0]', 0, 'ATSysEquiv_SA_system_overdetermined.', ''],
        // The solution to the next one is x=1 or y=-1, not x=1 and y=-1!
        ['SysEquiv', '', '[x=1,y=-1]', '[(x-1)*(y+1)=0]', 0, 'ATSysEquiv_SA_Not_completely_solved.', ''],
        ['SysEquiv', '', '[x=1]', '[(x-1)*(x+1)=0]', 0, 'ATSysEquiv_SA_Not_completely_solved.', ''],
        ['SysEquiv', '', '[x=1]', '[(x-1)*(x+1)*y=0]', 0, 'ATSysEquiv_SA_Not_completely_solved.', ''],
        ['SysEquiv', '', '[90=v*t,90=(v+5)*(t-1/4)]', '[90=v*t,90=(v+5)*(t-1/4)]', 1, '', ''],
        ['SysEquiv', '', '[90=v*t,90=(v+5)*(t*x-1/4)]', '[90=v*t,90=(v+5)*(t-1/4)]', 0, 'ATSysEquiv_SA_extra_variables.', ''],
        [
            'SysEquiv', '', '[90=v*t,90=(v+5)*(t-1/4)]', '[90=v*t,90=(v+5)*(t*x-1/4)]', 0,
            'ATSysEquiv_SA_missing_variables.', '',
        ],
        ['SysEquiv', '', '[90=v*t]', '[90=v*t,90=(v+5)*(t-1/4)]', 0, 'ATSysEquiv_SA_system_underdetermined.', ''],
        [
            'SysEquiv', '', '[90=v*t,90=(v+5)*(t-1/4),90=(v+6)*(t-1/5)]', '[90=v*t,90=(v+5)*(t-1/4)]', 0,
            'ATSysEquiv_SA_system_overdetermined.', '',
        ],
        [
            'SysEquiv', '', '[90=v*t,90=(v+5)*(t-1/4),90=(v+6)*(t-1/5),90=(v+7)*(t-1/4),90=(v+8)*(t-1/3)]',
            '[90=v*t,90=(v+5)*(t-1/4)]', 0, 'ATSysEquiv_SA_system_overdetermined.', '',
        ],
        ['SysEquiv', '', '[b^2=a,a=9]', '[x^2=y,y=9]', 0, 'ATSysEquiv_SA_wrong_variables.', 'Wrong variables'],
        ['SysEquiv', '', '[x^2=4]', '[x^2=4,y=9]', 0, 'ATSysEquiv_SA_missing_variables.', ''],
        ['SysEquiv', '', '[d=90,d=v*t,d=(v+5)*(t-1/4)]', '[90=v*t,90=(v+5)*(t-1/4)]', 0, 'ATSysEquiv_SA_extra_variables.', ''],
        // If we want to condone extra explicit variables in an answer we can do so.
        ['SysEquiv', '', 'stack_eval_assignments([d=90,d=v*t,d=(v+5)*(t-1/4)])', '[90=v*t,90=(v+5)*(t-1/4)]', 1, '', ''],

        ['Sets', '', '{1/0}', '{0}', -1, 'ATSets_STACKERROR_SAns.', ''],
        ['Sets', '', '{0}', '{1/0}', -1, 'ATSets_STACKERROR_TAns.', ''],
        ['Sets', '', 'x', '{1,2,3}', 0, 'ATSets_SA_not_set.', ''],
        ['Sets', '', '{1,2}', 'x', 0, 'ATSets_SB_not_set.', ''],
        ['Sets', '', '{1,2}', '{1,2,3}', 0, 'ATSets_missingentries.', ''],
        ['Sets', '', '{1,2,4}', '{1,2}', 0, 'ATSets_wrongentries.', ''],
        // Note, in the example below the wrong entry "4" displayed in the feedback does not occur in the set.
        // The set contains only the equivalent entry 2+2.  This might be confusing.
        ['Sets', '', '{1,2,2+2}', '{1,2}', 0, 'ATSets_wrongentries.', ''],
        ['Sets', '', '{5,1,2,4}', '{1,2,3}', 0, 'ATSets_wrongentries. ATSets_missingentries.', ''],
        ['Sets', '', '{2/4, 1/3}', '{1/2, 1/3}', 1, '', ''],
        ['Sets', '', '{1,2,1}', '{1,2}', 1, 'ATSets_duplicates.', 'Duplicate entries'],
        ['Sets', '', '{1,2,1+1}', '{1,2}', 1, 'ATSets_duplicates.', ''],
        ['Sets', '', '{1,2,1+1}', '{1,2,3}', 0, 'ATSets_duplicates. ATSets_missingentries.', ''],
        // We accept these are "different" as we can't simplify this without expanding.
        ['Sets', '', '{(x-a)^6000}', '{(a-x)^6000}', 0, 'ATSets_wrongentries. ATSets_missingentries.', ''],

        ['Expanded', '', '1/0', '0', -1, 'ATExpanded_STACKERROR_SAns.', ''],
        ['Expanded', '', 'x>2', 'x^2-2*x+1', 0, 'ATExpanded_SA_not_expression.', ''],
        ['Expanded', '', 'x^2-1', '0', 1, 'ATExpanded_TRUE.', ''],
        ['Expanded', '', '2*(x-1)', '0', 0, 'ATExpanded_FALSE.', ''],
        ['Expanded', '', '(x-1)*(x+1)', '0', 0, 'ATExpanded_FALSE.', ''],
        ['Expanded', '', '(x-a)*(x-b)', '0', 0, 'ATExpanded_FALSE.', ''],
        ['Expanded', '', 'x^2-(a+b)*x+a*b', '0', 0, 'ATExpanded_FALSE.', ''],
        ['Expanded', '', 'x^2-a*x-b*x+a*b', '0', 1, 'ATExpanded_TRUE.', ''],
        ['Expanded', '', 'cos(2*x)', '0', 1, 'ATExpanded_TRUE.', ''],
        ['Expanded', '', 'p+1', '0', 1, 'ATExpanded_TRUE.', ''],
        ['Expanded', '', '(p+1)*(p-1)', '0', 0, 'ATExpanded_FALSE.', ''],
        ['Expanded', '', '3+2*sqrt(3)', '0', 1, 'ATExpanded_TRUE.', ''],
        ['Expanded', '', '3+sqrt(12)', '0', 1, 'ATExpanded_TRUE.', ''],
        ['Expanded', '', '(1+sqrt(5))*(1-sqrt(3))', '0', 0, 'ATExpanded_FALSE.', ''],
        [
            'Expanded', '', '(a-x)^6000', '0', -2, 'ATExpanded_TRUE.',
            'This fails, but you are never going to ask students to do this anyway...',
        ],

        ['FacForm', 'x', '1/0', '0', -1, 'ATFacForm_STACKERROR_SAns.', ''],
        ['FacForm', 'x', '0', '1/0', -1, 'ATFacForm_STACKERROR_TAns.', ''],
        ['FacForm', '1/0', '0', '0', -1, 'ATFacForm_STACKERROR_Opt.', ''],
        ['FacForm', 'x', '2', '2', 1, 'ATFacForm_int_true.', 'Trivial cases'],
        ['FacForm', 'x', '6', '6', 1, 'ATFacForm_int_true.', ''],
        ['FacForm', 'x', '1/3', '1/3', 1, 'ATFacForm_true.', ''],
        ['FacForm', 'x', '3*x^2', '3*x^2', 1, 'ATFacForm_true.', ''],
        ['FacForm', 'x', '4*x^2', '4*x^2', 1, 'ATFacForm_true.', ''],
        ['FacForm', 'x', '2*(x-1)', '2*x-2', 1, 'ATFacForm_true.', 'Linear integer factors'],
        ['FacForm', 'x', '2*x-2', '2*x-2', 0, 'ATFacForm_notfactored.', ''],
        ['FacForm', 'x', '2*(x+1)', '2*x-2', 0, 'ATFacForm_isfactored. ATFacForm_notalgequiv.', ''],
        ['FacForm', 'x', '2*x+2', '2*x-2', 0, 'ATFacForm_notfactored. ATFacForm_notalgequiv.', ''],
        ['FacForm', 'x', '2*(x+0.5)', '2*x+1', 1, 'ATFacForm_default_true.', ''],
        ['FacForm', 'x', 't*(2*x+1)', 't*(2*x+1)', 1, 'ATFacForm_true.', 'Linear factors'],
        ['FacForm', 'x', 't*x+t', 't*(x+1)', 0, 'ATFacForm_notfactored.', ''],
        ['FacForm', 't', '6*s*t+10*s', '2*s*(3*t+5)', 0, 'ATFacForm_notfactored.', ''],
        ['FacForm', 'x', '2*x*(x-3)', '2*x^2-6*x', 1, 'ATFacForm_true.', 'Quadratic, with no const'],
        ['FacForm', 'x', '2*(x^2-3*x)', '2*x*(x-3)', 0, 'ATFacForm_notfactored.', ''],
        ['FacForm', 'x', 'x*(2*x-6)', '2*x*(x-3)', 0, 'ATFacForm_notfactored.', ''],
        ['FacForm', 'x', '(x+2)*(x+3)', '(x+2)*(x+3)', 1, 'ATFacForm_true.', 'Quadratic'],
        ['FacForm', 'x', '(x+2)*(2*x+6)', '2*(x+2)*(x+3)', 0, 'ATFacForm_notfactored.', ''],
        ['FacForm', 'x', '(z*x+z)*(2*x+6)', '2*z*(x+1)*(x+3)', 0, 'ATFacForm_notfactored.', ''],
        ['FacForm', 'x', '(x+t)*(x-t)', 'x^2-t^2', 1, 'ATFacForm_true.', ''],
        ['FacForm', 't', 't^2-1', '(t-1)*(t+1)', 0, 'ATFacForm_notfactored.', ''],
        ['FacForm', 't', 't^2+1', 't^2+1', 1, 'ATFacForm_true.', ''],
        ['FacForm', 'v', 'v^2+1', 'v^2+1', 1, 'ATFacForm_true.', ''],
        ['FacForm', 'v', 'v^2-1', 'v^2-1', 0, 'ATFacForm_notfactored.', ''],
        ['FacForm', 'v', '-(3*w-4*v+9*u)*(3*w+4*v-u)', '-(3*w-4*v+9*u)*(3*w+4*v-u)', 1, 'ATFacForm_true.', ''],
        ['FacForm', 'k', '-6*k*(4*b-k-1)', '6*k*(1+k-4*b)', 1, 'ATFacForm_default_true.', ''],
        ['FacForm', 'k', '-2*3*k*(4*b-k-1)', '6*k*(1+k-4*b)', 1, 'ATFacForm_true.', ''],
        ['FacForm', 'k', '-(6*k*(4*b-k-1))', '6*k*(1+k-4*b)', 1, 'ATFacForm_default_true.', ''],
        ['FacForm', 'a', '-(6*a*(4*b-a-1))', '6*a*(1+a-4*b)', 1, 'ATFacForm_true.', ''],
        ['FacForm', 'a', '-(6*a*(4*b-a-1))', '6*a*(-(4*b)+a+1)', 1, 'ATFacForm_true.', ''],
        ['FacForm', 'x', 'x*(x-4+4/x)', 'x^2-4*x+4', 0, 'ATFacForm_notfactored.', ''],
        ['FacForm', 'x', '(2-x)*(3-x)', '(x-2)*(x-3)', 1, 'ATFacForm_true.', 'These are delicate cases!'],
        ['FacForm', 'x', '(1-x)^2', '(x-1)^2', 1, 'ATFacForm_true.', ''],
        ['FacForm', 'x', '(1-x)*(1-x)', '(x-1)^2', 1, 'ATFacForm_true.', ''],
        ['FacForm', 'x', '-(1-x)^2', '-(x-1)^2', 1, 'ATFacForm_true.', ''],
        ['FacForm', 'x', '(1-x)^2', '(x-1)^2', 1, 'ATFacForm_true.', ''],
        ['FacForm', 'x', '4*(1-x/2)^2', '(x-2)^2', 1, 'ATFacForm_default_true.', ''],
        ['FacForm', 'x', '-3*(x-4)*(x+1)', '-3*x^2+9*x+12', 1, 'ATFacForm_true.', ''],
        ['FacForm', 'x', '3*(-x+4)*(x+1)', '-3*x^2+9*x+12', 1, 'ATFacForm_true.', ''],
        ['FacForm', 'x', '3*(4-x)*(x+1)', '-3*x^2+9*x+12', 1, 'ATFacForm_true.', ''],
        ['FacForm', 'x', '(x-1)*(x^2+x+1)', 'x^3-1', 1, 'ATFacForm_true.', 'Cubics'],
        ['FacForm', 'x', 'x^3-x+1', 'x^3-x+1', 1, 'ATFacForm_true.', ''],
        ['FacForm', 'x', '7*x^3-7*x+7', '7*(x^3-x+1)', 0, 'ATFacForm_notfactored.', ''],
        ['FacForm', 'x', '(1-x)*(2-x)*(3-x)', '-x^3+6*x^2-11*x+6', 1, 'ATFacForm_true.', ''],
        ['FacForm', 'x', '(2-x)*(2-x)*(3-x)', '-x^3+7*x^2-16*x+12', 1, 'ATFacForm_true.', ''],
        ['FacForm', 'x', '(2-x)^2*(3-x)', '-x^3+7*x^2-16*x+12', 1, 'ATFacForm_true.', ''],
        ['FacForm', 'x', '(x^2-4*x+4)*(3-x)', '-x^3+7*x^2-16*x+12', 0, 'ATFacForm_notfactored.', ''],
        ['FacForm', 'x', '(x^2-3*x+2)*(3-x)', '-x^3+6*x^2-11*x+6', 0, 'ATFacForm_notfactored.', ''],
        ['FacForm', 'y', '3*y^3-6*y^2-24*y', '3*(y-4)*y*(y+2)', 0, 'ATFacForm_notfactored.', ''],
        ['FacForm', 'y', '3*(y^3-2*y^2-8*y)', '3*(y-4)*y*(y+2)', 0, 'ATFacForm_notfactored.', ''],
        ['FacForm', 'y', '3*y*(y^2-2*y-8)', '3*(y-4)*y*(y+2)', 0, 'ATFacForm_notfactored.', ''],
        ['FacForm', 'y', '3*(y^2-4*y)*(y+2)', '3*(y-4)*y*(y+2)', 0, 'ATFacForm_notfactored.', ''],
        ['FacForm', 'y', '(y-4)*y*(3*y+6)', '3*(y-4)*y*(y+2)', 0, 'ATFacForm_notfactored.', ''],
        ['FacForm', 'x', '(a-x)^6000', '(a-x)^6000', 1, 'ATFacForm_true.', ''],
        ['FacForm', 'x', '(x-a)^6000', '(a-x)^6000', 1, 'ATFacForm_true.', ''],
        ['FacForm', 'a', '2*a*(a*b-1)', '2*a*(a*b-1)', 1, 'ATFacForm_true.', 'Needs flattening'],
        ['FacForm', 'a', '(2*a)*(a*b-1)', '2*a*(a*b-1)', 1, 'ATFacForm_true.', ''],
        ['FacForm', 'x', '3*x*(7*y-3)*(7*y+3)', '3*x*(7*y-3)*(7*y+3)', 1, 'ATFacForm_true.', ''],
        ['FacForm', 'y', '3*x*(7*y-3)*(7*y+3)', '3*x*(7*y-3)*(7*y+3)', 1, 'ATFacForm_true.', ''],
        ['FacForm', 'sin(x)', '(sin(x)+1)*(sin(x)-1)', 'sin(x)^2-1', 1, 'ATFacForm_true.', 'Not polynomials in a variable'],
        ['FacForm', 'cos(t)', '(cos(t)-sqrt(2))^2', 'cos(t)^2-2*sqrt(2)*cos(t)+2', 1, 'ATFacForm_true.', ''],
        ['FacForm', 'x', '7', '7', 1, 'ATFacForm_int_true.', ''],
        ['FacForm', 'x', '24*(x-1/4)', '24*x-6', 1, 'ATFacForm_default_true.', 'Factors over other fields'],
        ['FacForm', 'x', '(x-sqrt(2))*(x+sqrt(2))', 'x^2-2', 1, 'ATFacForm_true.', ''],
        ['FacForm', 'x', 'x^2-2', 'x^2-2', 1, 'ATFacForm_true.', ''],
        ['FacForm', 'x', '(%i*x-2*%i)', '%i*(x-2)', 0, 'ATFacForm_notfactored.', ''],
        ['FacForm', 'x', '%i*(x-2)', '(%i*x-2*%i)', 1, 'ATFacForm_true.', ''],
        ['FacForm', 'x', '(x-%i)*(x+%i)', 'x^2+1', 1, 'ATFacForm_true.', ''],
        ['FacForm', 'x', '(x-1)*(x+(1+sqrt(3)*%i)/2)*(x+(1-sqrt(3)*%i)/2)', 'x^3-1', 1, 'ATFacForm_default_true.', ''],

        ['CompSquare', '', '1/0', '0', -1, 'STACKERROR_OPTION.', ''],
        ['CompSquare', 'x', '1/0', '0', -1, 'ATCompSquare_STACKERROR_SAns.', ''],
        ['CompSquare', 'x', '0', '1/0', -1, 'ATCompSquare_STACKERROR_TAns.', ''],
        ['CompSquare', '1/0', '0', '0', -1, 'ATCompSquare_STACKERROR_Opt.', ''],
        ['CompSquare', 'x', '1', '(x-1)^2+1', 0, 'ATCompSquare_SA_not_depend_var.', 'Category errors.'],
        ['CompSquare', 'x', '(t-1)^2+1', '(x-1)^2+1', 0, 'ATCompSquare_SA_not_depend_var.', ''],
        ['CompSquare', 'x', '(x-1)^2+1=0', '(x-1)^2+1', 0, 'ATCompSquare_STACKERROR_LIST.', ''],
        ['CompSquare', 'x', 'sin(x-1)+a-1', '(x-1)^2+1', 0, 'ATCompSquare_false_not_AlgEquiv.', ''],
        ['CompSquare', 'x', '1', '1', 1, 'ATCompSquare_true_trivial.', 'Trivial cases'],
        ['CompSquare', 'x', 'x-a', 'x-a', 1, 'ATCompSquare_true_trivial.', ''],
        ['CompSquare', 'x', 'x^2', 'x^2', 1, 'ATCompSquare_true.', ''],
        ['CompSquare', 'x', 'x^2-1', '(x-1)*(x+1)', 1, 'ATCompSquare_true.', ''],
        ['CompSquare', 'x', '(x-1)^2*k', '(x-1)^2*k', 1, 'ATCompSquare_true.', ''],
        ['CompSquare', 'x', '(x-1)^2/k', '(x-1)^2/k', 1, 'ATCompSquare_true.', ''],
        ['CompSquare', 'x', '(x-1)^2+1', '(x-1)^2+1', 1, 'ATCompSquare_true.', 'Normal cases'],
        ['CompSquare', 'x', '(1-x)^2+1', '(x-1)^2+1', 1, 'ATCompSquare_true.', ''],
        ['CompSquare', 'x', '(X-1)^2+1', '(x-1)^2+1', 0, 'ATCompSquare_SA_not_depend_var.', ''],
        ['CompSquare', 'x', '9*(x-1)^2+1', '(3*x-3)^2+1', 1, 'ATCompSquare_true.', ''],
        ['CompSquare', 'x', '-(x-1)^2', '-(x-1)^2', 1, 'ATCompSquare_true.', ''],
        ['CompSquare', 'x', '-(1-x)^2', '-(x-1)^2', 1, 'ATCompSquare_true.', ''],
        ['CompSquare', 'x', '-(x-1)^2+3', '-(x-1)^2+3', 1, 'ATCompSquare_true.', ''],
        ['CompSquare', 'x', '-(1-x)^2+3', '-(x-1)^2+3', 1, 'ATCompSquare_true.', ''],
        ['CompSquare', 'x', '-4*(x-1)^2+3', '-4*(x-1)^2+3', 1, 'ATCompSquare_true.', ''],
        ['CompSquare', 'x', '-4*(x-1)^2+3', '-(2*x-2)^2+3', 1, 'ATCompSquare_true.', ''],
        ['CompSquare', 'x', '3-4*(x-1)^2', '-(2*x-2)^2+3', 1, 'ATCompSquare_true.', ''],
        ['CompSquare', 'x', '(x-1)^2+1', '(x+1)^2+1', 0, 'ATCompSquare_true_not_AlgEquiv.', ''],
        ['CompSquare', 'x', '(x-a^2)^2+1+b', '(x-a^2)^2+1+b', 1, 'ATCompSquare_true.', ''],
        ['CompSquare', 'x', 'x^2-2*x+2', '(x-1)^2+1', 0, 'ATCompSquare_false_no_summands.', ''],
        ['CompSquare', 'x', 'x+1', '(x-1)^2+1', 0, 'ATCompSquare_false_not_AlgEquiv.', ''],
        ['CompSquare', 'x', 'a*(x-1)^2+1', 'a*(x-1)^2+1', 1, 'ATCompSquare_true.', ''],
        ['CompSquare', 'x', '-a*(x-1)^2+1', '1-a*(x-1)^2', 1, 'ATCompSquare_true.', ''],
        ['CompSquare', 'sin(x)', '(sin(x)-1)^2+1', '(sin(x)-1)^2+1', 1, 'ATCompSquare_true.', 'Not simple variable'],
        ['CompSquare', 'x^2', '(x^2-1)^2+1', '(x^2-1)^2+1', 1, 'ATCompSquare_true.', ''],
        ['CompSquare', 'y', '(y-1)^2+1', '(y-1)^2+1', 1, 'ATCompSquare_true.', ''],
        ['CompSquare', 'y', '(y+1)^2+1', '(y-1)^2+1', 0, 'ATCompSquare_true_not_AlgEquiv.', ''],
        ['CompSquare', 'sin(x)', '(x-1)^2+1', '(sin(x)-1)^2+1', 0, 'ATCompSquare_SA_not_depend_var.', ''],

        ['PropLogic', '', '1/0', '0', -1, 'ATPropLogic_STACKERROR_SAns.', ''],
        ['PropLogic', '', '0', '1/0', -1, 'ATPropLogic_STACKERROR_TAns.', ''],
        ['PropLogic', '', 'true', 'true', 1, '', ''],
        ['PropLogic', '', 'true', 'false', 0, '', ''],
        ['PropLogic', '', 'A implies B', 'not(A) or B', 1, '', ''],
        ['PropLogic', '', '(a and b and c) xor (a and b) xor (a and c) xor a xor true', '(a implies b) or c', 1, '', ''],

        ['Equiv', '', 'x', '[x^2=4,x=2 or x=-2]', -1, 'ATEquiv_SA_not_list.', ''],
        ['Equiv', '', '[x^2=4,x=2 or x=-2]', 'x',  -1, 'ATEquiv_SB_not_list.', ''],
        [
            'Equiv', '', '[1/0]', '[x^2=4,x=2 or x=-2]', -1,
            'ATEquiv_STACKERROR_SAns.', '',
        ],
        [
            'Equiv', '', '[x^2=4,x=2 or x=-2]', '[1/0]',  -1,
            'ATEquiv_STACKERROR_TAns.', '',
        ],
        ['Equiv', '', '[x^2=4,x=2 or x=-2]', '[x^2=4,x=2 or x=-2]', 1, '(EMPTYCHAR,EQUIVCHAR)', ''],
        ['Equiv', '', '[x^2=4,x=#pm#2,x=2 and x=-2]', '[x^2=4,x=2 or x=-2]', 0, '(EMPTYCHAR,EQUIVCHAR,ANDOR)', ''],
        ['Equiv', '', '[x^2=4,x=2]', '[x^2=4,x=2 or x=-2]', 0, '(EMPTYCHAR,IMPLIEDCHAR)', ''],
        ['Equiv', '[assumepos]', '[x^2=4,x=2]', '[x^2=4,x=2]', 1, '(ASSUMEPOSVARS,EQUIVCHAR)', ''],
        [
            'Equiv', '', '[x^2=4,x^2-4=0,(x-2)*(x+2)=0,x=2 or x=-2]', '[x^2=4,x=2 or x=-2]', 1,
            '(EMPTYCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR)', '',
        ],
        [
            'Equiv', '', '[x^2=4,x= #pm#2, x=2 or x=-2]', '[x^2=4,x=2 or x=-2]', 1,
            '(EMPTYCHAR,EQUIVCHAR,EQUIVCHAR)', '',
        ],
        ['Equiv', '', '[x^2-6*x+9=0,x=3]', '[x^2-6*x+9=0,x=3]', 1, '(EMPTYCHAR,SAMEROOTS)', ''],

        ['EquivFirst', '', 'x', '[x^2=4,x=2 or x=-2]', -1, 'ATEquivFirst_SA_not_list.', ''],
        ['EquivFirst', '', '[x^2=4,x=2 or x=-2]', 'x',  -1, 'ATEquivFirst_SB_not_list.', ''],
        [
            'EquivFirst', '', '[1/0]', '[x^2=4,x=2 or x=-2]', -1,
            'ATEquivFirst_STACKERROR_SAns.', '',
        ],
        [
            'EquivFirst', '', '[x^2=4,x=2 or x=-2]', '[1/0]',  -1,
            'ATEquivFirst_STACKERROR_TAns.', '',
        ],
        ['EquivFirst', '', '[x^2=4,x=2 or x=-2]', '[x^2=4,x=2 or x=-2]', 1, '(EMPTYCHAR,EQUIVCHAR)', ''],
        ['EquivFirst', '', '[x^2=9,x=3 or x=-3]', '[x^2=4,x=2 or x=-2]', 0, 'ATEquivFirst_SA_wrong_start', ''],
        ['EquivFirst', '', '[x^2=4,x=2]', '[x^2=4,x=2 or x=-2]', 0, '(EMPTYCHAR,IMPLIEDCHAR)', ''],
        [
            'EquivFirst', '', '[x^2=4,x^2-4=0,(x-2)*(x+2)=0,x=2 or x=-2]', '[x^2=4,x=2 or x=-2]', 1,
            '(EMPTYCHAR,EQUIVCHAR,EQUIVCHAR,EQUIVCHAR)', '',
        ],
        [
            'EquivFirst', '', '[x^2=4,x= #pm#2, x=2 or x=-2]', '[x^2=4,x=2 or x=-2]', 1,
            '(EMPTYCHAR,EQUIVCHAR,EQUIVCHAR)', '',
        ],
        ['EquivFirst', '', '[x^2-6*x+9=0,x=3]', '[x^2-6*x+9=0,x=3]', 1, '(EMPTYCHAR,SAMEROOTS)', ''],
        ['EquivFirst', '[assumepos]', '[x^2=4,x=2]', '[x^2=4,x=2]', 1, '(ASSUMEPOSVARS,EQUIVCHAR)', ''],

        ['SingleFrac', '', '1/0', '1/n', -1, 'ATSingleFrac_STACKERROR_SAns.', ''],
        ['SingleFrac', '', '0', '1/0', -1, 'ATSingleFrac_STACKERROR_TAns.', ''],
        ['SingleFrac', '', 'x=3', '2', 0, 'ATSingleFrac_SA_not_expression.', ''],
        ['SingleFrac', '', '3', '3', 1, '', ''],
        ['SingleFrac', '', '3', '2', 0, 'ATSingleFrac_ret_exp.', ''],
        ['SingleFrac', '', '1/m', '1/n', 0, 'ATSingleFrac_true. ATSingleFrac_ret_exp.', ''],
        ['SingleFrac', '', '1/n', '1/n', 1, 'ATSingleFrac_true.', ''],
        ['SingleFrac', '', 'a+1/2', '(2*a+1)/2', 0, 'ATSingleFrac_part.', ''],
        ['SingleFrac', '', 'a+1/2', '(2*a+1)/2', 0, 'ATSingleFrac_part.', ''],
        ['SingleFrac', '', '4/(x^2+2*x-24)+2/(x^2+4*x-12)', '(6*x-16)/(x^3-28*x+48)', 0, 'ATSingleFrac_part.', ''],
        ['SingleFrac', '', '2*(1/n)', '2/n', 0, 'ATSingleFrac_part.', '2 subtly different answers for the same question'],
        ['SingleFrac', '', '2/n', '2/n', 1, 'ATSingleFrac_true.', ''],
        ['SingleFrac', '', '2/(n+1)', '1/(n+1)', 0, 'ATSingleFrac_true. ATSingleFrac_ret_exp.', 'Simple Mistakes'],
        ['SingleFrac', '', '(2*n+1)/(n+2)', '1/n', 0, 'ATSingleFrac_true. ATSingleFrac_ret_exp.', ''],
        ['SingleFrac', '', '(2*n)/(n*(n+2))', '(2*n)/(n*(n+3))', 0, 'ATSingleFrac_true. ATSingleFrac_ret_exp.', ''],
        ['SingleFrac', '', '(x-1)/(x^2-1)', '1/(x+1)', 1, 'ATSingleFrac_true.', ''],
        ['SingleFrac', '', '(1/2)/(3/4)', '2/3', 0, 'ATSingleFrac_div.', 'Fractions within fractions'],
        ['SingleFrac', '', '(x-2)/4/(2/x^2)', '(x-2)*x^2/8', 0, 'ATSingleFrac_div.', ''],
        ['SingleFrac', '', '1/(1-1/x)', 'x/(x-1)', 0, 'ATSingleFrac_div.', ''],
        ['SingleFrac', '', '(1+1/a)/a', '(1+a)/a^2', 0, 'ATSingleFrac_div.', ''],
        ['SingleFrac', '', 'a/(1+1/a)', 'a^2/(1+a)', 0, 'ATSingleFrac_div.', ''],
        ['SingleFrac', '', '(1+2*b/a)/c', '(a+2*b)/(a*c)', 0, 'ATSingleFrac_div.', ''],
        ['SingleFrac', '', 'c/(1+2*b/a)', 'a*c/(a+2*b)', 0, 'ATSingleFrac_div.', ''],
        ['SingleFrac', '', 'a*c/(a+2*b)', 'a*c/(a+2*b)', 1, 'ATSingleFrac_true.', ''],
        ['SingleFrac', '', '-1/2', '-1/2', 1, 'ATSingleFrac_true.', 'Negative cases'],
        ['SingleFrac', '', '-1/2', '-1/3', 0, 'ATSingleFrac_true. ATSingleFrac_ret_exp.', ''],
        ['SingleFrac', '', '-(1/2)', '-1/2', 1, 'ATSingleFrac_true.', ''],
        ['SingleFrac', '', '-a/b', '-a/b', 1, 'ATSingleFrac_true.', ''],
        ['SingleFrac', '', '(-a)/b', '-a/b', 1, 'ATSingleFrac_true.', ''],
        ['SingleFrac', '', 'a/(-b)', '-a/b', 1, 'ATSingleFrac_true.', ''],
        ['SingleFrac', '', '-(a/b)', '-a/b', 1, 'ATSingleFrac_true.', ''],
        ['SingleFrac', '', '-(1/(n-1))', '1/(1-n)', 1, 'ATSingleFrac_true.', ''],
        ['SingleFrac', '', 'a/(-1-1/a)', '-a^2/(1+a)', 0, 'ATSingleFrac_div.', ''],
        ['SingleFrac', '', '((sqrt(5))^3 +6)/15', '((sqrt(5))^3 +6)/15', 1, 'ATSingleFrac_true.', 'Surds in answers'],
        // Use the LowestTerms test for this distinction.
        ['SingleFrac', '', '1/(1-sqrt(2))', '1/(1-sqrt(2))', 1, 'ATSingleFrac_true.', ''],
        ['SingleFrac', '', '((sqrt(5))^3+6)/15', '((sqrt(5))^3+6)/15', 1, 'ATSingleFrac_true.', ''],
        ['SingleFrac', '', '(5^(3/2)+6)/15', '((sqrt(5))^3+6)/15', 1, 'ATSingleFrac_true.', ''],

        ['PartFrac', '', '1/0', '3*x^2', -1, 'STACKERROR_OPTION.', ''],
        ['PartFrac', 'x', '1/0', '3*x^2', -1, 'ATPartFrac_STACKERROR_SAns.', ''],
        ['PartFrac', '1/0', '0', '0', -1, 'ATPartFrac_STACKERROR_Opt.', ''],
        ['PartFrac', 'x', '0', '1/0', -1, 'ATPartFrac_STACKERROR_TAns.', ''],
        ['PartFrac', 'n', '1/n=0', '1/n', 0, 'ATPartFrac_SA_not_expression.', ''],
        ['PartFrac', 'n', '1/n', '{1/n}', 0, 'ATPartFrac_TA_not_expression.', ''],
        ['PartFrac', 'n', '1/m', '1/n', 0, 'ATPartFrac_diff_variables.', 'Basic tests'],
        ['PartFrac', 's', '2/(x+1)-1/(x+2)', 's/((s+1)*(s+2))', 0, 'ATPartFrac_diff_variables.', ''],
        ['PartFrac', 'n', '1/n', '1/n', 1, 'ATPartFrac_true.', ''],
        ['PartFrac', 'n', 'n^3/(n-1)', 'n^3/(n-1)', 0, 'ATPartFrac_false_factor.', ''],
        ['PartFrac', 'n', '1+n+n^2+1/(n-1)', 'n^3/(n-1)', 1, 'ATPartFrac_true.', ''],
        // 1/(1-n) vs -1/(n-1).
        ['PartFrac', 'n', '1+n+n^2-1/(1-n)', 'n^3/(n-1)', 1, 'ATPartFrac_true.', ''],
        [
            'PartFrac', 'n', '1/(n+1)-1/n', '1/(n+1)-1/n', 1, 'ATPartFrac_true.',
            'Distinct linear factors in denominator',
        ],
        ['PartFrac', 'n', '1/(n+1)+1/(1-n)', '1/(n+1)-1/(n-1)', 1, 'ATPartFrac_true.', ''],
        ['PartFrac', 'n', '1/(2*(n-1))-1/(2*(n+1))', '1/((n-1)*(n+1))', 1, 'ATPartFrac_true.', ''],
        ['PartFrac', 'n', '1/(2*(n+1))-1/(2*(n-1))', '1/((n-1)*(n+1))', 0, 'ATPartFrac_ret_expression.', ''],
        ['PartFrac', 'x', '-9/(x-2) + -9/(x+1)', '-9/(x-2) + -9/(x+1)', 1, 'ATPartFrac_true.', ''],
        [
            'PartFrac', 'x', '1/(x+1) + 1/(x+2)', '2/(x+1) + 1/(x+2)', 0,
            'ATPartFrac_ret_expression.', 'Addition and Subtraction errors',
        ],
        [
            'PartFrac', 'x', '1/(x+1) + 1/(x+2)', '1/(x+1) + 2/(x+2)', 0,
            'ATPartFrac_ret_expression.', '',
        ],
        [
            'PartFrac', 'x', '1/(x+1) + 1/(x+2)', '1/(x+3) + 1/(x+2)', 0,
            'ATPartFrac_ret_expression.', 'Denominator Error',
        ],
        [
            'PartFrac', 'y', '(9*y-8)/(y-4)^2', '(9*y-8)/(y-4)^2', 0, 'ATPartFrac_false_factor.',
            'Repeated linear factors in denominator',
        ],
        ['PartFrac', 'y', '9/(y-4)+28/(y-4)^2', '(9*y-8)/(y-4)^2', 1, 'ATPartFrac_true.', ''],
        [
            'PartFrac', 'x', '(-5/(x+3))+(16/(x+3)^2)-(2/(x+2))+4', '(-5/(x+3))+(16/(x+3)^2)-(2/(x+2))+4', 1,
            'ATPartFrac_true.', '',
        ],
        [
            'PartFrac', 'x', '(3*x^2-5)/((x-4)^2*x)', '(3*x^2-5)/((x-4)^2*x)', 0,
            'ATPartFrac_false_factor.', '',
        ],
        [
            'PartFrac', 'x', '-4/(16*x)+53/(16*(x-4))+43/(4*(x-4)^2)', '(3*x^2-5)/((x-4)^2*x)', 0,
            'ATPartFrac_ret_expression.', '',
        ],
        [
            'PartFrac', 'x', '-5/(16*x)+53/(16*(x-4))+43/(4*(x-4)^2)', '(3*x^2-5)/((x-4)^2*x)', 1,
            'ATPartFrac_true.', '',
        ],
        [
            'PartFrac', 'x', '(5*x+6)/((x+1)*(x+5)^2)', '(5*x+6)/((x+1)*(x+5)^2)', 0,
            'ATPartFrac_false_factor.', '',
        ],
        [
            'PartFrac', 'x', '-1/(16*(x+5))+19/(4*(x+5)^2)+1/(16*(x+1))', '(5*x+6)/((x+1)*(x+5)^2)', 1,
            'ATPartFrac_true.', '',
        ],
        ['PartFrac', 'x', '5/(x*(x+3)*(5*x-2))', '5/(x*(x+3)*(5*x-2))', 0, 'ATPartFrac_false_factor.', ''],
        ['PartFrac', 'x', '125/(34*(5*x-2))+5/(51*(x+3))-5/(6*x)', '5/(x*(x+3)*(5*x-2))', 1, 'ATPartFrac_true.', ''],
        [
            'PartFrac', 'x', '-4/(16*x)+1/(2*(x-1))-1/(8*(x-1)^2)', '(3*x^2-5)/((4*x-4)^2*x)', 0,
            'ATPartFrac_ret_expression.', '',
        ],
        ['PartFrac', 'x', '-5/(16*x)+1/(2*(x-1))-1/(8*(x-1)^2)', '(3*x^2-5)/((4*x-4)^2*x)', 1, 'ATPartFrac_true.', ''],
        [
            'PartFrac', 'x', '1/(x-1)-(x+1)/(x^2+1)', '2/((x-1)*(x^2+1))', 1, 'ATPartFrac_true.',
            'Irreducible quadratic in denominator',
        ],
        ['PartFrac', 'x', '1/(2*x-2)-(x+1)/(2*(x^2+1))', '1/((x-1)*(x^2+1))', 1, 'ATPartFrac_true.', ''],
        ['PartFrac', 'x', '1/(2*(x-1))+x/(2*(x^2+1))', '1/((x-1)*(x^2+1))', 0, 'ATPartFrac_ret_expression.', ''],
        ['PartFrac', 'x', '(2*x+1)/(x^2+1)-2/(x-1)', '(2*x+1)/(x^2+1)-2/(x-1)', 1, 'ATPartFrac_true.', ''],
        [
            'PartFrac', 'x', '3/(x+1) + 3/(x+2)', '3*(2*x+3)/((x+1)*(x+2))', 1, 'ATPartFrac_true.',
            '2 answers to the same question',
        ],
        ['PartFrac', 'x', '3*(1/(x+1) + 1/(x+2))', '3*(2*x+3)/((x+1)*(x+2))', 1, 'ATPartFrac_true.', ''],
        [
            'PartFrac', 'x', '3*x*(1/(x+1) + 2/(x+2))', '-12/(x+2)-3/(x+1)+9', 0, 'ATPartFrac_false_factor.',
            'Algebraically equivalent, but numerators of same order than denominator, i.e. not in partial fraction form.',
        ],
        ['PartFrac', 'x', '(3*x+3)*(1/(x+1) + 2/(x+2))', '9-6/(x+2)', 0, 'ATPartFrac_false_factor.', ''],
        ['PartFrac', 'n', 'n/(2*n-1)-(n+1)/(2*n+1)', '1/(4*n-2)-1/(4*n+2)', 0, 'ATPartFrac_false_factor.', ''],
        [
            'PartFrac', 'x', '10/(x+3) - 2/(x+2) + x -2', '(x^3 + 3*x^2 + 4*x +2)/((x+2)*(x+3))', 1, 'ATPartFrac_true.',
            'Correct Answer, Numerator > Denominator',
        ],
        ['PartFrac', 'x', '2*x+1/(x+1)+1/(x-1)', '2*x^3/(x^2-1)', 1, 'ATPartFrac_true.', ''],
        ['PartFrac', 'n', '1/(n*(n-1))', '1/(n*(n-1))', 0, 'ATPartFrac_false_factor.', 'Simple mistakes'],
        ['PartFrac', 'x', '((1-x)^4*x^4)/(x^2+1)', '((1-x)^4*x^4)/(x^2+1)', 0, 'ATPartFrac_false_factor.', ''],
        ['PartFrac', 'n', '1/(n-1)-1/n^2', '1/((n+1)*n)', 0, 'ATPartFrac_denom_ret.', ''],
        ['PartFrac', 'n', '1/(n-1)-1/n', '1/(n-1)+1/n', 0, 'ATPartFrac_ret_expression.', ''],
        ['PartFrac', 'x', '1/(x+1)-1/x', '1/(x-1)+1/x', 0, 'ATPartFrac_ret_expression.', ''],
        ['PartFrac', 'n', '1/(n*(n+1))+1/n', '2/n-1/(n+1)', 0, 'ATPartFrac_false_factor.', ''],
        [
            'PartFrac', 's', 's/((s+1)^2) + s/(s+2) - 1/(s+1)', 's/((s+1)*(s+2))', 0,
            'ATPartFrac_denom_ret.', 'Too many parts in the partial fraction',
        ],
        [
            'PartFrac', 's', 's/(s+2) - 1/(s+1)', 's/((s+1)*(s+2)*(s+3))', 0,
            'ATPartFrac_denom_ret.', 'Too few parts in the partial fraction',
        ],

        ['PartFrac', 'x', '(3*x^2-5)/((4*x-4)^2*x)', '(3*x^2-5)/((4*x-4)^2*x)', 0, 'ATPartFrac_false_factor.', ''],

        ['Diff', '', '1/0', '3*x^2', -1, 'STACKERROR_OPTION.', ''],
        ['Diff', '(x', '0', '1/0', -1, 'STACKERROR_OPTION.', ''],
        ['Diff', 'x', '1/0', '3*x^2', -1, 'ATDiff_STACKERROR_SAns.', ''],
        ['Diff', 'x', '0', '1/0', -1, 'ATDiff_STACKERROR_TAns.', ''],
        ['Diff', '1/0', '0', '0', -1, 'ATDiff_STACKERROR_Opt.', ''],
        ['Diff', 'x', '3*x^2', '3*x^2', 1, 'ATDiff_true.', 'Basic tests'],
        ['Diff', 'x', '3*X^2', '3*x^2', 0, 'ATDiff_var_SB_notSA.', ''],
        ['Diff', 'x', 'x^4/4', '3*x^2', 0, 'ATDiff_int.', ''],
        ['Diff', 'x', 'x^4/4+1', '3*x^2', 0, 'ATDiff_int.', ''],
        ['Diff', 'x', 'x^4/4+c', '3*x^2', 0, 'ATDiff_int.', ''],
        ['Diff', 'x', 'y=x^4/4', 'x^4/4', 0, 'ATDiff_SA_not_expression.', ''],
        ['Diff', 'x', 'x^4/4', 'y=x^4/4', 0, '', ''],
        ['Diff', 'x', 'y=x^4/4', 'y=x^4/4', 0, 'ATDiff_SA_not_expression.', ''],
        ['Diff', 'x', '6000*(x-a)^5999', '6000*(x-a)^5999', 1, 'ATDiff_true.', ''],
        ['Diff', 'x', '5999*(x-a)^5999', '6000*(x-a)^5999', 0, '', ''],
        ['Diff', 'x', 'y^2-2*y+1', 'x^2-2*x+1', 0, 'ATDiff_var_SB_notSA.', 'Variable mismatch tests'],
        ['Diff', 'x', 'x^2-2*x+1', 'y^2-2*y+1', 0, 'ATDiff_var_SA_notSB.', ''],
        ['Diff', 'z', 'y^2+2*y+1', 'x^2-2*x+1', 0, 'ATDiff_var_notSASB_SAnceSB.', ''],
        ['Diff', 'y', 'x^4/4', '3*x^2', 0, '', ''],
        ['Diff', 'x', 'e^x+c', 'e^x', 0, 'ATDiff_int.', 'Edge cases'],
        ['Diff', 'x', 'e^x+2', 'e^x', 0, 'ATDiff_int.', ''],
        ['Diff', 'x', 'n*x^n', 'n*x^(n-1)', -1, 'ATDiff_STACKERROR_SAns.', ''],
        ['Diff', 'x', 'n*x^n', '(assume(n>0), n*x^(n-1))', 0, '', ''],

        ['Int', '', '1/0', '1', -1, 'STACKERROR_OPTION.', ''],
        ['Int', 'x', '1/0', '1', -1, 'ATInt_STACKERROR_SAns.', ''],
        ['Int', 'x', '1', '1/0', -1, 'ATInt_STACKERROR_TAns.', ''],
        ['Int', '1/0', '0', '0', -1, 'ATInt_STACKERROR_Opt.', ''],
        ['Int', '[x,1/0]', '0', '0', -1, 'ATInt_STACKERROR_Opt.', ''],
        ['Int', '[x,NOCONST,1/0]', '0', '0', -1, 'ATInt_STACKERROR_Opt.', ''],
        ['Int', 'x', 'x^3/3', 'x^3/3', 0, 'ATInt_const.', 'Basic tests'],
        ['Int', 'x', 'x^3/3+1', 'x^3/3', 0, 'ATInt_const_int.', ''],
        ['Int', 'x', 'x^3/3+c', 'x^3/3', 1, 'ATInt_true.', ''],
        ['Int', 'x', 'x^3/3-c', 'x^3/3', 1, 'ATInt_true.', ''],
        ['Int', 'x', 'x^3/3+c+1', 'x^3/3', 1, 'ATInt_true.', ''],
        ['Int', 'x', 'x^3/3+3*c', 'x^3/3', 1, 'ATInt_true.', ''],
        ['Int', 'x', '(x^3+c)/3', 'x^3/3', 1, 'ATInt_true.', ''],
        // These are integration with a parameter: integrate(x^k,x), and we have to distinguish parameters from constants.
        ['Int', 'x', 'x^(k+1)/(k+1)', 'x^(k+1)/(k+1)', 0, 'ATInt_const.', ''],
        ['Int', 'x', 'x^(k+1)/(k+1)+c', 'x^(k+1)/(k+1)', 1, 'ATInt_true.', ''],
        ['Int', 'x', '(x^(k+1)-1)/(k+1)', 'x^(k+1)/(k+1)', -2, 'ATInt_true.', ''],
        ['Int', 'x', '(x^(k+1)-1)/(k+1)+c', 'x^(k+1)/(k+1)+c', -3, 'ATInt_weirdconst.', ''],
        ['Int', 'x', 'x^3/3+c+k', 'x^3/3', 0, 'ATInt_weirdconst.', ''],
        ['Int', 'x', 'x^3/3+c^2', 'x^3/3', 0, 'ATInt_weirdconst.', ''],
        // This next one should probably be accepted.
        ['Int', 'x', 'x^3/3+c^3', 'x^3/3', 0, 'ATInt_weirdconst.', ''],
        ['Int', 'x', 'x^3/3*c', 'x^3/3', 0, 'ATInt_generic.', ''],
        ['Int', 'x', 'X^3/3+c', 'x^3/3', 0, 'ATInt_generic. ATInt_var_SB_notSA.', ''],
        ['Int', 'x', 'sin(2*x)', 'x^3/3', 0, 'ATInt_generic.', ''],
        ['Int', 'x', 'x^2/2-2*x+2+c', '(x-2)^2/2', 1, 'ATInt_true.', ''],
        ['Int', 't', '(t-1)^5/5+c', '(t-1)^5/5', 1, 'ATInt_true.', ''],
        ['Int', 'v', '(v-1)^5/5+c', '(v-1)^5/5', 1, 'ATInt_true.', ''],
        ['Int', 'x', 'cos(2*x)/2+1+c', 'cos(2*x)/2', 1, 'ATInt_true.', ''],
        ['Int', 'x', '(x-a)^6001/6001+c', '(x-a)^6001/6001', 1, 'ATInt_true.', ''],
        ['Int', 'x', '(x-a)^6001/6001', '(x-a)^6001/6001', 0, 'ATInt_const.', ''],
        ['Int', 'x', '6000*(x-a)^5999', '(x-a)^6001/6001', 0, 'ATInt_diff.', ''],
        ['Int', 'x', '4*%e^(4*x)/(%e^(4*x)+1)', 'log(%e^(4*x)+1)+c', 0, 'ATInt_generic.', ''],
        ['Int', 'x', 'x^3/3+c', 'x^3/3+c', 1, 'ATInt_true.', 'The teacher adds a constant'],
        ['Int', 'x', 'x^2/2-2*x+2+c', '(x-2)^2/2+k', 1, 'ATInt_true.', ''],
        [
            'Int', '[x,NOCONST]', 'x^3/3', 'x^3/3', 1, 'ATInt_const_condone.',
            'The teacher condones lack of constant, or numerical constant',
        ],
        ['Int', '[x,NOCONST]', 'x^3/3+c', 'x^3/3', 1, 'ATInt_true.', ''],
        ['Int', '[x,NOCONST]', 'x^2/2-2*x+2', '(x-2)^2/2+k', 1, 'ATInt_const_condone.', ''],
        ['Int', '[x,NOCONST]', 'x^3/3+1', 'x^3/3', 1, 'ATInt_const_int_condone.', ''],
        ['Int', '[x,NOCONST]', 'x^3/3+c^2', 'x^3/3', 0, 'ATInt_weirdconst.', ''],
        ['Int', 'x', 'n*x^n', 'n*x^(n-1)', 0, 'ATInt_generic.', ''],
        ['Int', 'x', 'n*x^n', '(assume(n>0), n*x^(n-1))', 0, 'ATInt_generic.', ''],
        ['Int', 'x', 'exp(x)+c', 'exp(x)', 1, 'ATInt_true.', 'Special case'],
        ['Int', 'x', 'exp(x)', 'exp(x)', 0, 'ATInt_const.', ''],
        ['Int', '[x,NOCONST]', 'exp(x)', 'exp(x)', 1, 'ATInt_const_condone.', ''],
        ['Int', 'x', '2*x', 'x^3/3', 0, 'ATInt_diff.', 'Student differentiates by mistake'],
        ['Int', 'x', '2*x+c', 'x^3/3', 0, 'ATInt_diff.', ''],
        ['Int', 'x', 'ln(x)', 'ln(x)', 0, 'ATInt_const.', 'Sloppy logs (teacher ignores abs(x) )'],
        ['Int', '[x,NOCONST]', 'ln(x)', 'ln(x)', 1, 'ATInt_const_condone.', ''],
        ['Int', 'x', 'ln(x)+c', 'ln(x)+c', 1, 'ATInt_true_equiv.', ''],
        ['Int', 'x', 'ln(k*x)', 'ln(x)+c', 1, 'ATInt_true_equiv.', ''],
        ['Int', 'x', 'ln(x)', 'ln(abs(x))+c', 0, 'ATInt_EqFormalDiff. ATInt_logabs.', 'Fussy logs (teacher uses abs(x) )'],
        ['Int', 'x', 'ln(x)+c', 'ln(abs(x))+c', 0, 'ATInt_EqFormalDiff. ATInt_logabs.', ''],
        ['Int', '[x, NOCONST]', 'ln(x)', 'ln(abs(x))+c', 0, 'ATInt_EqFormalDiff. ATInt_logabs.', ''],
        ['Int', 'x', 'ln(abs(x))', 'ln(abs(x))+c', 0, 'ATInt_const.', ''],
        ['Int', 'x', 'ln(abs(x))+c', 'ln(abs(x))+c', 1, 'ATInt_true_equiv.', ''],
        ['Int', 'x', 'ln(k*x)', 'ln(abs(x))+c', 0, 'ATInt_EqFormalDiff. ATInt_logabs.', ''],
        ['Int', 'x', 'ln(k*abs(x))', 'ln(abs(x))+c', 1, 'ATInt_true_equiv.', ''],
        ['Int', 'x', 'ln(abs(k*x))', 'ln(abs(x))+c', 1, 'ATInt_true_equiv.', ''],
        ['Int', 'x', 'ln(x)', 'ln(k*abs(x))', 0, 'ATInt_EqFormalDiff. ATInt_logabs.', 'Teacher uses ln(k*abs(x))'],
        ['Int', 'x', 'ln(x)+c', 'ln(k*abs(x))', 0, 'ATInt_EqFormalDiff. ATInt_logabs.', ''],
        ['Int', 'x', 'ln(abs(x))', 'ln(k*abs(x))', 0, 'ATInt_const.', ''],
        ['Int', 'x', 'ln(abs(x))+c', 'ln(k*abs(x))', 1, 'ATInt_true_equiv.', ''],
        ['Int', 'x', 'ln(k*x)', 'ln(k*abs(x))', 0, 'ATInt_EqFormalDiff. ATInt_logabs.', ''],
        ['Int', 'x', 'ln(k*abs(x))', 'ln(k*abs(x))', 1, 'ATInt_true_equiv.', ''],
        ['Int', 'x', 'ln(x)+ln(a)', 'ln(k*abs(x+a))', 0, 'ATInt_generic. ATInt_logabs.', 'Other logs'],
        ['Int', 'x', 'log(x)^2-2*log(c)*log(x)+k', 'ln(c/x)^2', 0, 'ATInt_EqFormalDiff.', ''],
        ['Int', 'x', 'log(x)^2-2*log(c)*log(x)+k', 'ln(abs(c/x))^2', 0, 'ATInt_generic.', ''],
        ['Int', 'x', 'c-(log(2)-log(x))^2/2', '-1/2*log(2/x)^2', 1, 'ATInt_true_equiv.', ''],
        ['Int', 'x', 'ln(abs(x+3))/2+c', 'ln(abs(2*x+6))/2+c', 0, 'ATInt_EqFormalDiff.', ''],
        ['Int', '[x, FORMAL]', 'ln(abs(x+3))/2+c', 'ln(abs(2*x+6))/2+c', 1, 'ATInt_EqFormalDiff.', ''],
        // Note, the FORMAL option does not pick up missing constants of integration!
        ['Int', '[x, FORMAL]', 'ln(abs(x+3))/2', 'ln(abs(2*x+6))/2+c', 1, 'ATInt_EqFormalDiff.', ''],
        ['Int', '[x, FORMAL, NOCONST]', 'ln(abs(x+3))/2', 'ln(abs(2*x+6))/2+c', 1, 'ATInt_EqFormalDiff.', ''],
        ['Int', '[x, NOCONST, FORMAL]', 'ln(abs(x+3))/2', 'ln(abs(2*x+6))/2+c', 1, 'ATInt_EqFormalDiff.', ''],
        // This one still fails.
        ['Int', '[x, NOCONST]', 'ln(abs(x+3))/2', 'ln(abs(2*x+6))/2+c', -3, 'ATInt_EqFormalDiff.', ''],
        [
            'Int', 'x', '-log(sqrt(x^2-4*x+3)+x-2)/2+(x*sqrt(x^2-4*x+3))/2-sqrt(x^2-4*x+3)+c',
            'integrate(sqrt(x^2-4*x+3),x)', 0, 'ATInt_EqFormalDiff.', '',
        ],
        [
            'Int', '[x, FORMAL]', '-log(sqrt(x^2-4*x+3)+x-2)/2+(x*sqrt(x^2-4*x+3))/2-sqrt(x^2-4*x+3)+c',
            'integrate(sqrt(x^2-4*x+3),x)', 1, 'ATInt_EqFormalDiff.', '',
        ],
        // These examples have an irreducible quadratic: x^2+7*x+7.
        ['Int', '[x,NOCONST]', 'ln(x^2+7*x+7)', 'ln(x^2+7*x+7)', 1, 'ATInt_const_condone.', 'Irreducible quadratic'],
        ['Int', '[x,NOCONST]', 'ln(x^2+7*x+7)', 'ln(abs(x^2+7*x+7))', 0, 'ATInt_EqFormalDiff. ATInt_logabs.', ''],
        ['Int', 'x', 'ln(x^2+7*x+7)+c', 'ln(x^2+7*x+7)+c', 1, 'ATInt_true_equiv.', ''],
        ['Int', 'x', 'ln(k*(x^2+7*x+7))', 'ln(x^2+7*x+7)+c', 1, 'ATInt_true_equiv.', ''],
        ['Int', 'x', 'ln(x^2+7*x+7)', 'ln(abs(x^2+7*x+7))+c', 0, 'ATInt_EqFormalDiff. ATInt_logabs.', ''],
        ['Int', 'x', 'ln(x^2+7*x+7)+c', 'ln(abs(x^2+7*x+7))+c', 0, 'ATInt_EqFormalDiff. ATInt_logabs.', ''],
        [
            'Int', 'x', '-2*log(x)-(10*x^6)/3+x^3/3+5*log(x^4)+c',
            '-2*log(abs(x))+(10*x^6)/3-x^3/3-5/x^3+c', 0, 'ATInt_generic. ATInt_logabs.', '',
        ],
        ['Int', 'x', 'ln(abs(x^2+7*x+7))+c', 'ln(abs(x^2+7*x+7))+c', 1, 'ATInt_true_equiv.', ''],
        ['Int', 'x', 'ln(k*abs(x^2+7*x+7))', 'ln(abs(x^2+7*x+7))+c', 1, 'ATInt_true_equiv.', ''],
        // In these examples there are two logarithms.  The student should be *consistent*
        // in their use, or not, of absolute value.
        ['Int', 'x', 'log(abs(x-3))+log(abs(x+3))', 'log(abs(x-3))+log(abs(x+3))', 0, 'ATInt_const.', 'Two logs'],
        ['Int', 'x', 'log(abs(x-3))+log(abs(x+3))+c', 'log(abs(x-3))+log(abs(x+3))', 1, 'ATInt_true_equiv.', ''],
        ['Int', 'x', 'log(abs(x-3))+log(abs(x+3))', 'log(x-3)+log(x+3)', 0, 'ATInt_const.', ''],
        ['Int', 'x', 'log(abs(x-3))+log(abs(x+3))+c', 'log(x-3)+log(x+3)', 1, 'ATInt_true_equiv.', ''],
        ['Int', 'x', 'log(x-3)+log(x+3)', 'log(x-3)+log(x+3)', 0, 'ATInt_const.', ''],
        ['Int', 'x', 'log(x-3)+log(x+3)+c', 'log(x-3)+log(x+3)', 1, 'ATInt_true_equiv.', ''],
        ['Int', 'x', 'log(x-3)+log(x+3)', 'log(abs(x-3))+log(abs(x+3))', 0, 'ATInt_EqFormalDiff. ATInt_logabs.', ''],
        ['Int', 'x', 'log(x-3)+log(x+3)+c', 'log(abs(x-3))+log(abs(x+3))', 0, 'ATInt_EqFormalDiff. ATInt_logabs.', ''],
        ['Int', 'x', 'log(abs((x-3)*(x+3)))+c', 'log(abs(x-3))+log(abs(x+3))', 1, 'ATInt_true_equiv.', ''],
        ['Int', 'x', 'log(abs((x^2-9)))+c', 'log(abs(x-3))+log(abs(x+3))', 0, 'ATInt_EqFormalDiff.', ''],
        // This comes from the integral of x^3+2*x^2-3*x-2)/(x^2-4).
        [
            'Int', 'x', '2*log(abs(x-2))-log(abs(x+2))+(x^2+4*x)/2',
            '-log(abs(x+2))+2*log(abs(x-2))+(x^2+4*x)/2+c', 0, 'ATInt_const.', '',
        ],
        [
            'Int', 'x', '-log(abs(x+2))+2*log(abs(x-2))+(x^2+4*x)/2+c',
            '-log(abs(x+2))+2*log(abs(x-2))+(x^2+4*x)/2+c', 1, 'ATInt_true_equiv.', '',
        ],
        [
            'Int', 'x', '-log(abs(x+2))+2*log(abs(x-2))+(x^2+4*x)/2+c',
            '-log((x+2))+2*log((x-2))+(x^2+4*x)/2', 1, 'ATInt_true_equiv.', '',
        ],
        // Inconsistent cases. (Teacher doesn't use abs).
        [
            'Int', 'x', 'log(abs(x-3))+log((x+3))+c', 'log(x-3)+log(x+3)', 0,
            'ATInt_true_equiv. ATInt_logabs_inconsistent.', 'Inconsistent log(abs())',
        ],
        [
            'Int', 'v', 'log((v-3))+log(abs(v+3))+c', 'log(v-3)+log(v+3)', 0,
            'ATInt_true_equiv. ATInt_logabs_inconsistent.', '',
        ],
        [
            'Int', 'x', 'log((x-3))+log(abs(x+3))', 'log(x-3)+log(x+3)', 0,
            'ATInt_const. ATInt_logabs_inconsistent.', '',
        ],
        [
            'Int', 'x', '2*log((x-2))-log(abs(x+2))+(x^2+4*x)/2',
            '-log(abs(x+2))+2*log(abs(x-2))+(x^2+4*x)/2', 0, 'ATInt_EqFormalDiff. ATInt_logabs. ATInt_logabs_inconsistent.', '',
        ],
        [
            'Int', 't', '2*(sqrt(t)-5)-10*log((sqrt(t)-5))+c',
            '2*(sqrt(t)-5)-10*log((sqrt(t)-5))+c', 1, 'ATInt_true_equiv.', 'Significant integration constant differences',
        ],
        [
            'Int', 't', '2*(sqrt(t))-10*log((sqrt(t)-5))+c',
            '2*(sqrt(t)-5)-10*log((sqrt(t)-5))+c', 1, 'ATInt_true_differentconst.', '',
        ],
        [
            'Int', 't', '2*(sqrt(t)-5)-10*log((sqrt(t)-5))+c',
            '2*(sqrt(t)-5)-10*log(abs(sqrt(t)-5))+c', 0, 'ATInt_EqFormalDiff. ATInt_logabs.', '',
        ],
        [
            'Int', 't', '2*(sqrt(t))-10*log(abs(sqrt(t)-5))+c',
            '2*(sqrt(t)-5)-10*log(abs(sqrt(t)-5))+c', 1, 'ATInt_true_differentconst.', '',
        ],
        ['Int', 'x', '2*sin(x)*cos(x)', 'sin(2*x)+c', 0, 'ATInt_const.', 'Trig'],
        ['Int', 'x', '2*sin(x)*cos(x)+k', 'sin(2*x)+c', 1, 'ATInt_true.', ''],
        ['Int', 'x', '-2*cos(3*x)/3-3*cos(2*x)/2', '-2*cos(3*x)/3-3*cos(2*x)/2+c', 0, 'ATInt_const.', ''],
        ['Int', 'x', '-2*cos(3*x)/3-3*cos(2*x)/2+1', '-2*cos(3*x)/3-3*cos(2*x)/2+c', 0, 'ATInt_const_int.', ''],
        ['Int', 'x', '-2*cos(3*x)/3-3*cos(2*x)/2+c', '-2*cos(3*x)/3-3*cos(2*x)/2+c', 1, 'ATInt_true.', ''],
        [
            'Int', 't', '(tan(2*t)-2*t)/2',
            '-(t*sin(4*t)^2-sin(4*t)+t*cos(4*t)^2+2*t*cos(4*t)+t)/(sin(4*t)^2+cos(4*t)^2+2*cos(4*t)+1)', 0, 'ATInt_const.', '',
        ],
        [
            'Int', 't', '(tan(2*t)-2*t)/2+1',
            '-(t*sin(4*t)^2-sin(4*t)+t*cos(4*t)^2+2*t*cos(4*t)+t)/(sin(4*t)^2+cos(4*t)^2+2*cos(4*t)+1)', 0, 'ATInt_const_int.', '',
        ],
        [
            'Int', 't', '(tan(2*t)-2*t)/2+c',
            '-(t*sin(4*t)^2-sin(4*t)+t*cos(4*t)^2+2*t*cos(4*t)+t)/(sin(4*t)^2+cos(4*t)^2+2*cos(4*t)+1)', 1, 'ATInt_true.', '',
        ],
        ['Int', 'x', 'tan(x)-x+c', 'tan(x)-x', 1, 'ATInt_true.', ''],
        ['Int', 'x', '4*x*cos(x^12/%pi)+c', 'x*cos(x^12/%pi)+c', 0, 'ATInt_generic.', ''],
        ['Int', 'x', '4*x*cos(x^50/%pi)+c', 'x*cos(x^12/%pi)+c', 0, 'ATInt_generic.', ''],
        [
            'Int', 'x', '((5*%e^7*x-%e^7)*%e^(5*x))', '((5*%e^7*x-%e^7)*%e^(5*x))/25+c', 0,
            'ATInt_generic.', 'Note the difference in feedback here, generated by the options.',
        ],
        ['Int', '[x,x*%e^(5*x+7)]', '((5*%e^7*x-%e^7)*%e^(5*x))', '((5*%e^7*x-%e^7)*%e^(5*x))/25+c', 0, 'ATInt_generic.', ''],
        // Various forms of inverse hyperbolic forms of the integrals.  Consider int(1/(x^2-a^2),x).
        [
            'Int', 'x', 'log(x-3)/6-log(x+3)/6+c', 'log(x-3)/6-log(x+3)/6', 1, 'ATInt_true_equiv.',
            'Inverse hyperbolic integrals',
        ],
        ['Int', 'x', 'asinh(x)', 'ln(x+sqrt(x^2+1))', 0, 'ATInt_const.', ''],
        ['Int', 'x', 'asinh(x)+c', 'ln(x+sqrt(x^2+1))', 1, 'ATInt_true.', ''],
        ['Int', 'x', '-acoth(x/3)/3', 'log(x-3)/6-log(x+3)/6', 0, 'ATInt_const.', ''],
        ['Int', '[x, NOCONST]', '-acoth(x/3)/3', 'log(x-3)/6-log(x+3)/6', 1, 'ATInt_true.', ''],
        ['Int', 'x', '-acoth(x/3)/3+c', 'log(x-3)/6-log(x+3)/6', 1, 'ATInt_true.', ''],
        ['Int', 'x', '-acoth(x/3)/3+c', 'log(abs(x-3))/6-log(abs(x+3))/6', 1, 'ATInt_true.', ''],
        ['Int', 'x', 'log(x-a)/(2*a)-log(x+a)/(2*a)+c', 'log(x-a)/(2*a)-log(x+a)/(2*a)', 1, 'ATInt_true_equiv.', ''],
        ['Int', 'x', '-acoth(x/a)/a+c', 'log(x-a)/(2*a)-log(x+a)/(2*a)', 1, 'ATInt_true.', ''],
        ['Int', 'x', '-acoth(x/a)/a+c', 'log(abs(x-a))/(2*a)-log(abs(x+a))/(2*a)', 1, 'ATInt_true.', ''],
        [
            'Int', 'x', 'log(x-a)/(2*a)-log(x+a)/(2*a)+c', 'log(abs(x-a))/(2*a)-log(abs(x+a))/(2*a)', 0,
            'ATInt_EqFormalDiff. ATInt_logabs.', '',
        ],

        ['Int', 'x', 'log(x-3)/6-log(x+3)/6+c', '-acoth(x/3)/3', 1, 'ATInt_true.', ''],
        ['Int', 'x', 'log(abs(x-3))/6-log(abs(x+3))/6+c', '-acoth(x/3)/3', 1, 'ATInt_true.', ''],
        ['Int', 'x', 'log(x-3)/6-log(x+3)/6', '-acoth(x/3)/3', 0, 'ATInt_const.', ''],
        // Non-trivial example from JHD, July 2017.
        ['Int', 'x', 'atan(2*x-3)+c', 'atan(2*x-3)', 1, 'ATInt_true.', ''],
        ['Int', 'x', 'atan((x-2)/(x-1))+c', 'atan(2*x-3)', 1, 'ATInt_true.', ''],
        ['Int', 'x', 'atan((x-2)/(x-1))', 'atan(2*x-3)', 0, 'ATInt_const.', ''],
        ['Int', 'x', 'atan((x-1)/(x-2))', 'atan(2*x-3)', 0, 'ATInt_generic.', ''],
        ['Int', 'x', 'atan((x-1)/(x+1))+c', 'atan(x)', 1, 'ATInt_true.', ''],
        // This really does have an odd constant of integration!
        ['Int', 'x', 'atan((a*x+1)/(a-x))', 'atan(x)', 1, 'ATInt_true.', ''],
        // These ones currently fail for mathematical reasons.
        [
            'Int', 'x', '2/3*sqrt(3)*(atan(sin(x)/(sqrt(3)*(cos(x)+1)))-(atan(sin(x)/(cos(x)+1))))+x/sqrt(3)',
            '2*atan(sin(x)/(sqrt(3)*(cos(x)+1)))/sqrt(3)', -3, 'ATInt_const.', 'Stoutemyer (currently fails)',
        ],

        ['GT', '', '1/0', '1', -1, 'ATGT_STACKERROR_SAns.', ''],
        ['GT', '', '1', '1/0', -1, 'ATGT_STACKERROR_TAns.', ''],
        ['GT', '', '1', '1', 0, 'ATGT_false.', ''],
        ['GT', '', '2', '1', 1, 'ATGT_true.', ''],
        ['GT', '', '1', '2.1', 0, 'ATGT_false.', ''],
        ['GT', '', 'pi', '3', 1, 'ATGT_true.', ''],
        ['GT', '', 'pi+2', '5', 1, 'ATGT_true.', ''],
        ['GT', '', '-inf', '0', 0, 'Not number', 'Infinity'],
        ['GT', '', 'inf', '0', 0, 'Not number', ''],

        ['GTE', '', '1/0', '1', -1, 'ATGTE_STACKERROR_SAns.', ''],
        ['GTE', '', '1', '1/0', -1, 'ATGTE_STACKERROR_TAns.', ''],
        ['GTE', '', '1', '1', 1, 'ATGTE_true.', ''],
        ['GTE', '', '2', '1', 1, 'ATGTE_true.', ''],
        ['GTE', '', '1', '2.1', 0, 'ATGTE_false.', ''],
        ['GTE', '', 'pi', '3', 1, 'ATGTE_true.', ''],
        ['GTE', '', 'pi+2', '5', 1, 'ATGTE_true.', ''],

        ['NumRelative', '', '1/0', '0', -1, 'ATNumRelative_STACKERROR_SAns.', 'Basic tests'],
        ['NumRelative', '', '0', '1/0', -1, 'ATNumRelative_STACKERROR_TAns.', ''],
        ['NumRelative', '1/0', '0', '0', -1, 'ATNumRelative_STACKERROR_Opt.', ''],
        ['NumRelative', '', '0', '(x', -1, 'ATNumRelativeTEST_FAILED-Empty TA.', ''],
        ['NumRelative', 'x', '1.5', '1.5', -1, 'ATNumerical_STACKERROR_tol.', ''],
        // Invalid options should be caught at edit time.  If they get this far it will be taken as the default 0.05.
        ['NumRelative', '(x', '1', '0', 0, '', ''],
        ['NumRelative', '', 'x=1.5', '1.5', 0, 'ATNumerical_SA_not_number.', ''],
        ['NumRelative', '', '1.5', 'x=1.5', 0, 'ATNumerical_SB_not_number.', ''],
        ['NumRelative', '', '1.1', '1', 0, '', 'No option, so 5%'],
        ['NumRelative', '', '1.05', '1', 1, '', ''],
        ['NumRelative', '', '0.95', '1', 1, '', ''],
        ['NumRelative', '', '0.949', '1', 0, '', ''],
        ['NumRelative', '', '1.05e33', '1e33', 1, '', ''],
        ['NumRelative', '', '1.06e33', '1e33', 0, '', ''],
        ['NumRelative', '', '0.95e33', '1e33', 1, '', ''],
        ['NumRelative', '', '0.949e33', '1e33', 0, '', ''],
        ['NumRelative', '', '1.05e-33', '1e-33', 1, '', ''],
        ['NumRelative', '', '1.06e-33', '1e-33', 0, '', ''],
        ['NumRelative', '', '0.95e-33', '1e-33', 1, '', ''],
        ['NumRelative', '', '0.949e-33', '1e-33', 0, '', ''],
        ['NumRelative', '0.1', '1', 'displaydp(1.05,2)', 1, '', 'Remove display dp etc.'],
        ['NumRelative', '0.1', '1000', 'displaysci(1.05,2,3)', 1, '', ''],
        ['NumRelative', '0.1', '1.05', '1', 1, '', 'Options passed'],
        ['NumRelative', '0.1', '1.05', '3', 0, '', ''],
        ['NumRelative', '0.001', '3.14', 'pi', 1, '', ''],
        ['NumRelative', '', 'inf', '0', 0, 'ATNumerical_SA_not_number.', 'Infinity'],
        ['NumRelative', '', '1', '[1,2]', 0, 'ATNumerical_SA_not_list.', 'Lists'],
        ['NumRelative', '', '[1,2]', '[1,2,3]', 0, 'ATNumerical_wronglen.', ''],
        ['NumRelative', '', '[1,2]', '[1,2]', 1, '', ''],
        ['NumRelative', '', '[3.141,1.414]', '[pi,sqrt(2)]', 1, '', ''],
        ['NumRelative', '0.01', '[3,1.414]', '[pi,sqrt(2)]', 0, 'ATNumerical_wrongentries SA/TA=[3.0].', ''],
        ['NumRelative', '0.01', '[3,1.414]', '{pi,sqrt(2)}', 0, 'ATNumerical_SA_not_set.', ''],
        [
            'NumRelative', '0.01', '{1.414,3.1}', '{significantfigures(pi,6),sqrt(2)}', 0,
            'ATNumerical_wrongentries: TA/SA=[3.14159], SA/TA=[3.1].', '',
        ],
        ['NumRelative', '0.1', '{1.414,3.1}', '{pi,sqrt(2)}', 1, '', ''],
        ['NumRelative', '0.1', '{0,1,2}', '{0,1,2}', 1, '', ''],
        // What happens with floating point complex numbers?
        // This is rejected as not a real number.
        ['NumRelative', '0.1', '0.99*%i', '%i', 0, 'ATNumerical_SA_not_number.', 'Complex numbers'],

        ['NumAbsolute', '', '1/0', '0', -1, 'ATNumAbsolute_STACKERROR_SAns.', 'Basic tests'],
        ['NumAbsolute', '', '0', '1/0', -1, 'ATNumAbsolute_STACKERROR_TAns.', ''],
        ['NumAbsolute', '1/0', '0', '0', -1, 'ATNumAbsolute_STACKERROR_Opt.', ''],
        ['NumAbsolute', '', '0', '(x', -1, 'ATNumAbsoluteTEST_FAILED-Empty TA.', ''],
        // Invalid options should be caught at edit time.  If they get this far it will be taken as the default 0.05.
        ['NumAbsolute', '(x', '1', '0', 0, '', ''],
        ['NumAbsolute', '', '1.1', '1', 0, '', 'No option, so 5%'],
        ['NumAbsolute', '', '1.05', '1', 1, '', ''],
        ['NumAbsolute', '0.1', '1.05', '1', 1, '', 'Options passed'],
        ['NumAbsolute', '0.1', '1.05', '3', 0, '', ''],
        ['NumAbsolute', '0.001', '3.14', 'pi', 0, '', ''],
        ['NumAbsolute', '0.0001', '1.41e-2', '1.41e-2', 1, '', ''],
        ['NumAbsolute', '0.0001', '0.0141', '1.41e-2', 1, '', ''],
        ['NumAbsolute', '0.0001', '0.00141', '0.00141', 1, '', ''],
        ['NumAbsolute', '0.0001', '0.00141', '1.41*10^-3', 1, '', ''],
        ['NumAbsolute', '0.0001', '1.41*10^-3', '1.41*10^-3', 1, '', ''],
        ['NumAbsolute', '0.01', '[3.141,1.414]', '[pi,sqrt(2)]', 1, '', ''],
        ['NumAbsolute', '0.01', '[3,1.414]', '[pi,sqrt(2)]', 0, 'ATNumerical_wrongentries SA/TA=[3.0].', ''],
        ['NumAbsolute', '0.01', '[3,1.414]', '{pi,sqrt(2)}', 0, 'ATNumerical_SA_not_set.', ''],
        [
            'NumAbsolute', '0.01', '{1.414,3.1}', '{significantfigures(pi,6),sqrt(2)}', 0,
            'ATNumerical_wrongentries: TA/SA=[3.14159], SA/TA=[3.1].', '',
        ],
        ['NumAbsolute', '0.1', '{1,1.414,3.1,2}', '{1,2,pi,sqrt(2)}', 1, '', ''],

        ['NumSigFigs', '', '3.141', '3.1415927', -1, 'STACKERROR_OPTION.', 'Basic tests'],
        ['NumSigFigs', '3', '1/0', '3', -1, 'ATNumSigFigs_STACKERROR_SAns.', ''],
        ['NumSigFigs', '3', '0', '1/0', -1, 'ATNumSigFigs_STACKERROR_TAns.', ''],
        ['NumSigFigs', '1/0', '0', '0', -1, 'ATNumSigFigs_STACKERROR_Opt.', ''],
        ['NumSigFigs', '(', '0', '1', -1, 'STACKERROR_OPTION.', ''],
        ['NumSigFigs', '1', '(', '1', -1, 'ATNumSigFigsTEST_FAILED-Empty SA.', ''],
        ['NumSigFigs', 'pi', '1', '3', -1, 'ATNumSigFigs_STACKERROR_not_integer.', ''],
        ['NumSigFigs', '[3,x]', '1', '3', -1, 'ATNumSigFigs_STACKERROR_not_integer.', ''],
        ['NumSigFigs', '[1,2,3]', '1', '3', -1, 'ATNumSigFigs_STACKERROR_list_wrong_length.', ''],
        ['NumSigFigs', '', '1', '3', -1, 'STACKERROR_OPTION.', ''],
        ['NumSigFigs', '4', 'pi', 'pi', 0, 'ATNumSigFigs_NotDecimal.', ''],
        ['NumSigFigs', '2', '0', '0', 0, 'ATNumSigFigs_WrongDigits.', 'Edge cases'],
        ['NumSigFigs', '1', '0', '0', 1, '', ''],
        ['NumSigFigs', '1', '0.0', '0', 1, '', ''],
        ['NumSigFigs', '2', '0.0', '0', 0, 'ATNumSigFigs_WrongDigits.', ''],
        ['NumSigFigs', '2', '0', '0.0', 0, 'ATNumSigFigs_WrongDigits.', ''],
        ['NumSigFigs', '2', '0.0', '0.0', 0, 'ATNumSigFigs_WrongDigits.', ''],
        ['NumSigFigs', '2', '0.00', '0.00', 1, '', ''],
        ['NumSigFigs', '2', '5.4e21', '5.3e21', 0, 'ATNumSigFigs_Inaccurate.', 'Large numbers'],
        ['NumSigFigs', '2', '5.3e21', '5.3e21', 1, '', ''],
        ['NumSigFigs', '2', '5.3e22', '5.3e22', 1, '', ''],
        ['NumSigFigs', '2', '5.3e20', '5.3e22', 0, 'ATNumSigFigs_VeryInaccurate.', ''],
        // The next test cases were raised in issue #1108, but it's not a bug.
        ['NumSigFigs', '2', '9.8', '10', 1, '', ''],
        ['NumSigFigs', '2', '9.5', '10', 0, 'ATNumSigFigs_Inaccurate.', ''],
        ['NumSigFigs', '2', '10.0', '10', 0, 'ATNumSigFigs_WrongDigits.', ''],
        ['NumSigFigs', '9', '6.02214086e23', '6.02214086e23', 1, '', ''],
        ['NumSigFigs', '9', '6.0221409e23', '6.02214086e23', 0, 'ATNumSigFigs_WrongDigits. ATNumSigFigs_Inaccurate.', ''],
        ['NumSigFigs', '9', '6.02214087e23', '6.02214086e23', 0, 'ATNumSigFigs_Inaccurate.', ''],
        ['NumSigFigs', '9', '6.02214085e23', '6.02214086e23', 0, 'ATNumSigFigs_Inaccurate.', ''],
        ['NumSigFigs', '8', '5.3910632e-44', '5.3910632e-44', 1, '', ''],
        ['NumSigFigs', '8', '5.391063e-44', '5.3910632e-44', 0, 'ATNumSigFigs_WrongDigits. ATNumSigFigs_Inaccurate.', ''],
        ['NumSigFigs', '8', '5.3910631e-44', '5.3910632e-44', 0, 'ATNumSigFigs_Inaccurate.', ''],
        ['NumSigFigs', '8', '5.3910633e-44', '5.3910632e-44', 0, 'ATNumSigFigs_Inaccurate.', ''],
        ['NumSigFigs', '9', '1.61622938e-35', '1.61622938e-35', 1, '', ''],
        ['NumSigFigs', '9', '1.6162294e-35', '1.61622938e-35', 0, 'ATNumSigFigs_WrongDigits. ATNumSigFigs_Inaccurate.', ''],
        ['NumSigFigs', '9', '1.61622939e-35', '1.61622938e-35', 0, 'ATNumSigFigs_Inaccurate.', ''],
        ['NumSigFigs', '9', '1.61622937e-35', '1.61622938e-35', 0, 'ATNumSigFigs_Inaccurate.', ''],
        ['NumSigFigs', '5', '1.2345e82', '1.2345e82', 1, '', ''],
        ['NumSigFigs', '5', '1.2346e82', '1.2345e82', 0, 'ATNumSigFigs_Inaccurate.', ''],
        ['NumSigFigs', '5', '1.2344e82', '1.2345e82', 0, 'ATNumSigFigs_Inaccurate.', ''],
        [
            'NumSigFigs', '1', '1.234', '4', 0,
            'ATNumSigFigs_WrongDigits. ATNumSigFigs_Inaccurate.', 'No trailing zeros.',
        ],
        ['NumSigFigs', '3', '3.141', '3.1415927', 0, 'ATNumSigFigs_WrongDigits.', ''],
        ['NumSigFigs', '4', '3.141', '3.1415927', 0, 'ATNumSigFigs_Inaccurate.', ''],
        ['NumSigFigs', '4', '3.146', '3.1415927', 0, 'ATNumSigFigs_Inaccurate.', ''],
        ['NumSigFigs', '4', '3.147', '3.1415927', 0, 'ATNumSigFigs_VeryInaccurate.', ''],
        ['NumSigFigs', '4', '3.142', '3.1415927', 1, '', ''],
        ['NumSigFigs', '4', '3.142', 'pi', 1, '', ''],
        ['NumSigFigs', '4', '3141', '3.1415927', 0, 'ATNumSigFigs_VeryInaccurate.', ''],
        ['NumSigFigs', '3', '0.00123', '0.001234567', 1, '', ''],
        ['NumSigFigs', '3', '1.23e-3', '0.001234567', 1, '', ''],
        ['NumSigFigs', '3', '138*10^-3', '138*10^-3', 1, '', ''],
        ['NumSigFigs', '3', '-138*10^-3', '-138*10^-3', 1, '', ''],
        ['NumSigFigs', '3', '138*10^-3', '-138*10^-3', 0, 'ATNumSigFigs_WrongSign.', ''],
        ['NumSigFigs', '3', '1.38*10^-1', '138*10^-3', 1, '', ''],
        ['NumSigFigs', '3', '1.24e-3', '0.001234567', 0, 'ATNumSigFigs_Inaccurate.', ''],
        ['NumSigFigs', '4', '1.235e-3', '0.001234567', 1, '', ''],
        ['NumSigFigs', '2', '1000', '999', 1, 'ATNumSigFigs_WithinRange.', ''],
        ['NumSigFigs', '2', '1E3', '999', 0, 'ATNumSigFigs_WrongDigits.', ''],
        ['NumSigFigs', '1', '-100', '-149', 1, '', ''],
        ['NumSigFigs', '1', '-0.05', '-0.0499', 1, '', ''],
        ['NumSigFigs', '1', '-(0.05)', '-0.0499', 1, '', ''],
        ['NumSigFigs', '3', '1170', '1174.34', 1, '', ''],
        ['NumSigFigs', '3', '61300', '61250', 1, '', ''],
        ['NumSigFigs', '4', '0.1667', '0.1667', 1, '', 'Previous tricky case'],
        ['NumSigFigs', '4', '0.1666', '0.1667', 0, 'ATNumSigFigs_Inaccurate.', ''],
        ['NumSigFigs', '4', '0.1663', '0.1667', 0, 'ATNumSigFigs_Inaccurate.', ''],
        ['NumSigFigs', '4', '0.1662', '0.1667', 0, 'ATNumSigFigs_VeryInaccurate.', ''],
        ['NumSigFigs', '4', '0.166', '0.1667', 0, 'ATNumSigFigs_WrongDigits. ATNumSigFigs_VeryInaccurate.', ''],
        ['NumSigFigs', '4', '0.16667', '0.1667', 0, 'ATNumSigFigs_WrongDigits.', ''],
        ['NumSigFigs', '4', '-3.141', '-3.1415927', 0, 'ATNumSigFigs_Inaccurate.', 'Negative numbers'],
        ['NumSigFigs', '3', '-3.141', '-3.1415927', 0, 'ATNumSigFigs_WrongDigits.', ''],
        ['NumSigFigs', '4', '-3.141', '-3.1415927', 0, 'ATNumSigFigs_Inaccurate.', ''],
        ['NumSigFigs', '4', '-3.142', '-3.1415927', 1, '', ''],
        ['NumSigFigs', '4', '3.142', '-3.1415927', 0, 'ATNumSigFigs_WrongSign.', ''],
        ['NumSigFigs', '4', '-3.142', '3.1415927', 0, 'ATNumSigFigs_WrongSign.', ''],
        ['NumSigFigs', '4', '-3.149', '3.1415927', 0, 'ATNumSigFigs_WrongSign. ATNumSigFigs_VeryInaccurate.', ''],
        // Note that 75701719/35227192=2.148956947803276, so this tests rounding in the teacher's answer.
        ['NumSigFigs', '3', '2.15', '75701719/35227192', 1, '', ''],
        // Maxima's round() command uses Bankers' rounding, but STACK does not.
        // We actually round the teacher's answer to the specified number of SFs.
        ['NumSigFigs', '3', '0.0499', '0.04985', 1, '', 'Round teacher answer'],
        ['NumSigFigs', '3', '0.0498', '0.04985', 0, 'ATNumSigFigs_Inaccurate.', ''],
        ['NumSigFigs', '3', '0.0498', '0.04975', 1, '', ''],
        ['NumSigFigs', '3', '0.0497', '0.04975', 0, 'ATNumSigFigs_Inaccurate.', ''],
        ['NumSigFigs', '3', '0.0499', '0.0498', 0, 'ATNumSigFigs_Inaccurate.', ''],
        [
            'NumSigFigs', '3', '1.5', '1.500', 0, 'ATNumSigFigs_WrongDigits.',
            'Final zeros after the decimal are significant.',
        ],
        ['NumSigFigs', '3', '1.50', '1.500', 1, '', ''],
        ['NumSigFigs', '3', '1.500', '1.500', 0, 'ATNumSigFigs_WrongDigits.', ''],
        ['NumSigFigs', '3', '245.0', '245', 0, 'ATNumSigFigs_WrongDigits.', ''],
        // The test DOES NOT recognize too few significant figures being entered.
        ['NumSigFigs', '3', '180', '178.35', 0, 'ATNumSigFigs_WithinRange. ATNumSigFigs_Inaccurate.', 'Too few digits'],
        ['NumSigFigs', '3', '33', '33.1558', 0, 'ATNumSigFigs_WrongDigits. ATNumSigFigs_Inaccurate.', ''],
        // 0.0010 has exactly 2 significant digits.
        // With mixed options [n,m] we check for n significant figures, and that the student's answer
        // matches the teacher's answer to m of them.
        ['NumSigFigs', '[4,3]', '3.142', '3.1415927', 1, '', 'Mixed options'],
        // In this test case there are 4 sig figs, only 3 of which are accurate. Should generate feedback.
        ['NumSigFigs', '[4,3]', '3.143', '3.1415927', 1, '', ''],
        // In this test case there are 4 sig figs, only 2 of which are accurate. Should generate feedback.
        ['NumSigFigs', '[4,3]', '3.150', '3.1415927', 0, 'ATNumSigFigs_Inaccurate.', ''],
        // In this test case there are 4 sig figs, only 1 of which are accurate. Should not generate feedback.
        ['NumSigFigs', '[4,3]', '3.211', '3.1415927', 0, 'ATNumSigFigs_VeryInaccurate.', ''],
        // In this test case there are 5 sig figs, which is the wrong number.
        ['NumSigFigs', '[4,3]', '3.1416', '3.1415927', 0, 'ATNumSigFigs_WrongDigits.', ''],
        ['NumSigFigs', '[4,3]', '0.1666', '0.1667', 1, '', ''],
        // Range of sigfigs of 180 contains 3, accurate to 1!
        ['NumSigFigs', '[3,1]', '180', '178.35', 1, 'ATNumSigFigs_WithinRange.', ''],
        ['NumSigFigs', '[3,1]', '33', '33.1558', 0, 'ATNumSigFigs_WrongDigits.', ''], // Too few sigfigs.
        ['NumSigFigs', '[3,1]', '1.500', '1.5', 0, 'ATNumSigFigs_WrongDigits.', ''], // Too many sigfigs.
        ['NumSigFigs', '[3,1]', '245.0', '245', 0, 'ATNumSigFigs_WrongDigits.', ''], // Too many sigfigs.
        // This example has rounding.
        ['NumSigFigs', '[6,6]', '12345.7', '12345.654321', 1, '', ''],
        ['NumSigFigs', '[6,3]', '12345.7', '12345.654321', 1, '', ''],
        ['NumSigFigs', '[6,3]', '12300.0', '12345.654321', 1, '', ''],
        ['NumSigFigs', '[6,3]', '12400.0', '12345.654321', 0, 'ATNumSigFigs_Inaccurate.', ''],
        ['NumSigFigs', '[6,3]', '13500.0', '12345.654321', 0, 'ATNumSigFigs_VeryInaccurate.', ''],
        ['NumSigFigs', '[6,2]', '12000.0', '12345.654321', 1, '', ''],
        ['NumSigFigs', '[6,2]', '13000.0', '12345.654321', 0, 'ATNumSigFigs_Inaccurate.', ''],
        ['NumSigFigs', '[6,2]', '11000.0', '12345.654321', 0, 'ATNumSigFigs_Inaccurate.', ''],
        ['NumSigFigs', '[1,0]', '0.0010', '0', 0, 'ATNumSigFigs_WrongDigits.', 'Zero option and trailing zeros'],
        ['NumSigFigs', '[2,0]', '0.0010', '0', 1, '', ''],
        ['NumSigFigs', '[3,0]', '0.0010', '0', 0, 'ATNumSigFigs_WrongDigits.', ''],
        // 0.001 has exactly 1 significant digits.
        ['NumSigFigs', '[1,0]', '0.001', '0', 1, '', ''],
        ['NumSigFigs', '[2,0]', '0.001', '0', 0, 'ATNumSigFigs_WrongDigits.', ''],
        // 0.00100 has exactly 3 significant digits.
        ['NumSigFigs', '[2,0]', '0.00100', 'null', 0, 'ATNumSigFigs_WrongDigits.', ''],
        ['NumSigFigs', '[3,0]', '0.00100', 'null', 1, '', ''],
        ['NumSigFigs', '[4,0]', '0.00100', 'null', 0, 'ATNumSigFigs_WrongDigits.', ''],
        // 5.00 has exactly 3 significant digits.
        ['NumSigFigs', '[2,0]', '5.00', 'null', 0, 'ATNumSigFigs_WrongDigits.', ''],
        ['NumSigFigs', '[3,0]', '5.00', 'null', 1, '', ''],
        ['NumSigFigs', '[4,0]', '5.00', 'null', 0, 'ATNumSigFigs_WrongDigits.', ''],
        // 100 has at least 1 and maybe even 3 significant digits.
        ['NumSigFigs', '[1,0]', '100', '0', 1, '', ''],
        ['NumSigFigs', '[2,0]', '100', '0', 1, 'ATNumSigFigs_WithinRange.', ''],
        ['NumSigFigs', '[3,0]', '100', '0', 1, 'ATNumSigFigs_WithinRange.', ''],
        ['NumSigFigs', '[4,0]', '100', '0', 0, 'ATNumSigFigs_WrongDigits.', ''],
        // 10.0 has exactly 3 significant digits.
        ['NumSigFigs', '[2,0]', '10.0', '0', 0, 'ATNumSigFigs_WrongDigits.', ''],
        ['NumSigFigs', '[3,0]', '10.0', '0', 1, '', ''],
        ['NumSigFigs', '[4,0]', '10.0', '0', 0, 'ATNumSigFigs_WrongDigits.', ''],
        // 0 has exactly 1 significant digits.
        ['NumSigFigs', '[1,0]', '0', '0', 1, '', ''],
        ['NumSigFigs', '[2,0]', '0', '0', 0, 'ATNumSigFigs_WrongDigits.', ''],
        // 0.00 has at exactly 2 significant digits.
        ['NumSigFigs', '[1,0]', '0.00', '0', 0, 'ATNumSigFigs_WrongDigits.', ''],
        ['NumSigFigs', '[2,0]', '0.00', '0', 1, '', ''],
        ['NumSigFigs', '[3,0]', '0.00', '0', 0, 'ATNumSigFigs_WrongDigits.', ''],
        ['NumSigFigs', '[4,0]', '0.00', '0', 0, 'ATNumSigFigs_WrongDigits.', ''],
        // Condone too many significant figures.
        ['NumSigFigs', '[4,-1]', '8.250', '8.250', 1, '', 'Condone too many sfs.'],
        ['NumSigFigs', '[4,-1]', '8.25', '8.250', 0, 'ATNumSigFigs_WrongDigits.', ''],
        ['NumSigFigs', '[4,-1]', '8.250000', '8.250', 1, '', ''],
        ['NumSigFigs', '[4,-1]', '8.250434', '8.250', 1, '', ''],
        ['NumSigFigs', '[2,-1]', '82.4', '82', 1, '', ''],
        ['NumSigFigs', '[2,-1]', '82.5', '82', 0, 'ATNumSigFigs_Inaccurate.', ''],
        ['NumSigFigs', '[2,-1]', '83', '82', 0, 'ATNumSigFigs_Inaccurate.', ''],
        // Check where teacher uses a rational number 1/7 = 0.142857142857...
        ['NumSigFigs', '[4,-1]', '0.1430', '1/7', 0, 'ATNumSigFigs_Inaccurate.', '1/7 = 0.142857142857...'],
        ['NumSigFigs', '[4,-1]', '0.1429', '1/7', 1, '', ''],
        ['NumSigFigs', '[4,-1]', '0.1428', '1/7', 0, 'ATNumSigFigs_Inaccurate.', ''],
        ['NumSigFigs', '[4,-1]', '0.143', '1/7', 0, 'ATNumSigFigs_WrongDigits. ATNumSigFigs_Inaccurate.', ''],
        // Too many sig figs, which is condoned.
        ['NumSigFigs', '[4,-1]', '0.14284', '1/7', 0, 'ATNumSigFigs_Inaccurate.', ''],
        ['NumSigFigs', '[4,-1]', '0.14285', '1/7', 1, '', ''],
        ['NumSigFigs', '[4,-1]', '0.14286', '1/7', 1, '', ''],
        ['NumSigFigs', '[4,-1]', '0.14291', '1/7', 1, '', ''],
        ['NumSigFigs', '[4,-1]', '0.14294', '1/7', 1, '', ''],
        // Incorrectly rounded means to 4 s.f. this is too large.
        ['NumSigFigs', '[4,-1]', '0.14295', '1/7', 0, 'ATNumSigFigs_Inaccurate.', ''],
        ['NumSigFigs', '[2,-1]', '0.142', '1/7', 1, '', ''],
        ['NumSigFigs', '[2,-1]', '0.14290907676', '1/7', 1, '', ''],
        ['NumSigFigs', '[2,-1]', '0.143', '1/7', 1, '', ''],
        ['NumSigFigs', '[2,-1]', '0.1433333', '1/7', 1, '', ''],
        ['NumSigFigs', '[2,-1]', '0.144', '1/7', 1, '', ''],
        ['NumSigFigs', '[2,-1]', '0.145', '1/7', 1, '', ''],
        ['NumSigFigs', '[2,-1]', '0.146', '1/7', 0, 'ATNumSigFigs_Inaccurate.', ''],
        // Teacher does not give a float.
        ['NumSigFigs', '4', '1.279', 'ev(lg(19),lg=logbasesimp)', 1, '', 'Logarithms, numbers and surds'],
        ['NumSigFigs', '3', '3.14', 'pi', 1, '', ''],
        ['NumSigFigs', '3', '3.15', 'pi', 0, 'ATNumSigFigs_Inaccurate.', ''],
        ['NumSigFigs', '6', '1.73205', 'sqrt(3)', 1, '', ''],
        [
            'NumSigFigs', '2', 'matrix([0.33,1],[1,1])', 'matrix([0.333,1],[1,1])', -1, 'ATNumSigFigs_NotDecimal.',
            'No support for matrices!',
        ],
        ['NumSigFigs', '2', '3.1415', 'matrix([0.333,1],[1,1])', -1, 'TEST_FAILED', ''],
        ['NumSigFigs', '3', '1.50', 'dispsf(1.500,3)', 1, '', 'Teacher uses dispsf'],
        ['NumSigFigs', '3', '1.50', 'dispdp(1.500,3)', 1, '', ''],

        ['NumDecPlaces', '2', '1/0', '3', -1, 'ATNumDecPlaces_STACKERROR_SAns.', 'Basic tests'],
        ['NumDecPlaces', '2', '0.1', '1/0', -1, 'ATNumDecPlaces_STACKERROR_TAns.', ''],
        ['NumDecPlaces', '1/0', '0.1', '0', -1, 'ATNumDecPlaces_STACKERROR_Opt.', ''],
        ['NumDecPlaces', 'x', '0.1', '1', -1, 'ATNumDecPlaces_OptNotInt.', ''],
        ['NumDecPlaces', '-1', '0.1', '1', -1, 'ATNumDecPlaces_OptNotInt.', ''],
        ['NumDecPlaces', '0', '0.1', '1', -1, 'ATNumDecPlaces_OptNotInt.', ''],
        ['NumDecPlaces', '(', '0.1', '1', -1, 'STACKERROR_OPTION.', ''],
        ['NumDecPlaces', '1', '(', '1', -1, 'ATNumDecPlacesTEST_FAILED-Empty SA.', ''],
        [
            'NumDecPlaces', '2', 'x', '3.143', 0,
            'ATNumDecPlaces_SA_Not_num.', 'Student\'s answer not a floating point number',
        ],
        ['NumDecPlaces', '3', 'pi', '3.000', 0, 'ATNumDecPlaces_SA_Not_num.', ''],
        [
            'NumDecPlaces', '2', '3.14', '3.143', 1,
            'ATNumDecPlaces_Correct. ATNumDecPlaces_Equiv.', 'Right number of places',
        ],
        ['NumDecPlaces', '2', '3.14', '3.14', 1, 'ATNumDecPlaces_Correct. ATNumDecPlaces_Equiv.', ''],
        ['NumDecPlaces', '3', '3.140', '3.140', 1, 'ATNumDecPlaces_Correct. ATNumDecPlaces_Equiv.', ''],
        ['NumDecPlaces', '4', '3141.5972', '3141.5972', 1, 'ATNumDecPlaces_Correct. ATNumDecPlaces_Equiv.', ''],
        ['NumDecPlaces', '2', '4.14', '3.14', 0, 'ATNumDecPlaces_Correct. ATNumDecPlaces_Not_equiv.', ''],
        ['NumDecPlaces', '4', '3.1416', 'pi', 1, 'ATNumDecPlaces_Correct. ATNumDecPlaces_Equiv.', ''],
        ['NumDecPlaces', '1', '-7.3', '-7.3', 1, 'ATNumDecPlaces_Correct. ATNumDecPlaces_Equiv.', ''],
        [
            'NumDecPlaces', '1', '3.14', '3.143', 0,
            'ATNumDecPlaces_Wrong_DPs. ATNumDecPlaces_Equiv.', 'Wrong number of places',
        ],
        ['NumDecPlaces', '1', '3.14', '3.143', 0, 'ATNumDecPlaces_Wrong_DPs. ATNumDecPlaces_Equiv.', ''],
        ['NumDecPlaces', '3', '3.14', '3.140', 0, 'ATNumDecPlaces_Wrong_DPs. ATNumDecPlaces_Equiv.', ''],
        ['NumDecPlaces', '4', '7.000', '7', 0, 'ATNumDecPlaces_Wrong_DPs. ATNumDecPlaces_Equiv.', ''],
        ['NumDecPlaces', '4', '7.0000', '7', 1, 'ATNumDecPlaces_Correct. ATNumDecPlaces_Equiv.', ''],
        [
            'NumDecPlaces', '3', '8.0000', '7', 0, 'ATNumDecPlaces_Wrong_DPs. ATNumDecPlaces_Not_equiv.',
            'Both wrong DPs and inaccurate.',
        ],
        [
            'NumDecPlaces', '3', '4.000', '3.99999', 1, 'ATNumDecPlaces_Correct. ATNumDecPlaces_Equiv.',
            'Teacher needs to round their answer.',
        ],
        [
            'NumDecPlaces', '2', '0.10', 'displaydp(0.1,2)', 1, 'ATNumDecPlaces_Correct. ATNumDecPlaces_Equiv.',
            'Teacher uses displaydp',
        ],

        ['NumDecPlacesWrong', '2', '1/0', '3', -1, 'ATNumDecPlacesWrong_STACKERROR_SAns.', 'Basic tests'],
        ['NumDecPlacesWrong', '2', '0.1', '1/0', -1, 'ATNumDecPlacesWrong_STACKERROR_TAns.', ''],
        ['NumDecPlacesWrong', '1/0', '0.1', '0', -1, 'ATNumDecPlacesWrong_STACKERROR_Opt.', ''],
        ['NumDecPlacesWrong', 'x', '0.1', '0', -1, 'ATNumDecPlacesWrong_OptNotInt.', ''],
        ['NumDecPlacesWrong', '4', 'x^2', '1234', 0, 'ATNumDecPlacesWrong_SA_Not_num.', ''],
        ['NumDecPlacesWrong', '4', '1234.5', 'x^2', 0, 'ATNumDecPlacesWrong_Tans_Not_Num.', ''],
        ['NumDecPlacesWrong', '4', '3.141', '31.41', 1, 'ATNumDecPlacesWrong_Correct.', ''],
        ['NumDecPlacesWrong', '4', '3.141', '31.14', 0, 'ATNumDecPlacesWrong_Wrong.', ''],
        ['NumDecPlacesWrong', '4', 'pi', '31.14', 0, 'ATNumDecPlacesWrong_SA_Not_num.', ''],
        ['NumDecPlacesWrong', '4', '0.1234', '1234', 1, 'ATNumDecPlacesWrong_Correct.', ''],
        ['NumDecPlacesWrong', '4', '0.1235', '1234', 0, 'ATNumDecPlacesWrong_Wrong.', ''],
        ['NumDecPlacesWrong', '4', '0.0001234', '1234', 1, 'ATNumDecPlacesWrong_Correct.', ''],
        ['NumDecPlacesWrong', '4', '0.0001235', '1234', 0, 'ATNumDecPlacesWrong_Wrong.', ''],
        ['NumDecPlacesWrong', '3', '0.1233', '1234', 1, 'ATNumDecPlacesWrong_Correct.', ''],
        ['NumDecPlacesWrong', '3', '0.1243', '1234', 0, 'ATNumDecPlacesWrong_Wrong.', ''],
        ['NumDecPlacesWrong', '3', '0.1230', '1239', 1, 'ATNumDecPlacesWrong_Correct.', ''],
        ['NumDecPlacesWrong', '3', '0.1240', '1239', 0, 'ATNumDecPlacesWrong_Wrong.', ''],
        ['NumDecPlacesWrong', '3', '1230', '1239', 1, 'ATNumDecPlacesWrong_Correct.', ''],
        ['NumDecPlacesWrong', '3', '2230', '1239', 0, 'ATNumDecPlacesWrong_Wrong.', ''],
        ['NumDecPlacesWrong', '3', '0.100', '1.00', 1, 'ATNumDecPlacesWrong_Correct.', ''],
        ['NumDecPlacesWrong', '3', '0.1000', '1.00', 1, 'ATNumDecPlacesWrong_Correct.', ''],
        ['NumDecPlacesWrong', '3', '0.1001', '1.001', 1, 'ATNumDecPlacesWrong_Correct.', ''],
        ['NumDecPlacesWrong', '4', '0.100', '1.0', 1, 'ATNumDecPlacesWrong_Correct.', 'Condone lack of trailing zeros'],
        ['NumDecPlacesWrong', '4', '1', '1.00', 1, 'ATNumDecPlacesWrong_Correct.', ''],
        [
            'NumDecPlacesWrong', '3', '0.101', 'displaydp(101,3)', 1, 'ATNumDecPlacesWrong_Correct.',
            'Teacher uses displaydp',
        ],

        ['SigFigsStrict', '', '3.141', 'null', -1, 'STACKERROR_OPTION.', 'Basic tests'],
        ['SigFigsStrict', 'x^2', '3.141', 'null', -1, 'STACKERROR_OPTION.', ''],
        ['SigFigsStrict', '-2', '3.141', 'null', -1, 'STACKERROR_OPTION.', ''],
        ['SigFigsStrict', '0', '3.141', 'null', -1, 'STACKERROR_OPTION.', ''],
        // 0.0010 has exactly 2 significant digits.
        ['SigFigsStrict', '1', '0.0010', 'null', 0, '', ''],
        ['SigFigsStrict', '2', '0.0010', 'null', 1, '', ''],
        ['SigFigsStrict', '3', '0.0010', 'null', 0, '', ''],
        // 0.00100 has exactly 3 significant digits.
        ['SigFigsStrict', '2', '0.00100', 'null', 0, '', ''],
        ['SigFigsStrict', '3', '0.00100', 'null', 1, '', ''],
        ['SigFigsStrict', '4', '0.00100', 'null', 0, '', ''],
        // 0.001 has exactly 1 significant digits.
        ['SigFigsStrict', '1', '0.001', 'null', 1, '', ''],
        ['SigFigsStrict', '2', '0.001', 'null', 0, '', ''],
        // 100 has exactly 1 significant digit.
        ['SigFigsStrict', '1', '100', 'null', 1, '', ''],
        ['SigFigsStrict', '2', '100', 'null', 0, 'ATSigFigsStrict_WithinRange.', ''],
        ['SigFigsStrict', '3', '100', 'null', 0, 'ATSigFigsStrict_WithinRange.', ''],
        ['SigFigsStrict', '4', '100', 'null', 0, '', ''],
        // 100. has exactly 3 significant digit.
        ['SigFigsStrict', '1', '100.', 'null', 0, '', ''],
        ['SigFigsStrict', '2', '100.', 'null', 0, '', ''],
        ['SigFigsStrict', '3', '100.', 'null', 1, '', ''],
        ['SigFigsStrict', '4', '100.', 'null', 0, '', ''],
        // 123. has exactly 3 significant digit.
        ['SigFigsStrict', '1', '123.', 'null', 0, '', ''],
        ['SigFigsStrict', '2', '123.', 'null', 0, '', ''],
        ['SigFigsStrict', '3', '123.', 'null', 1, '', ''],
        ['SigFigsStrict', '4', '123.', 'null', 0, '', ''],
        // 1.00e2 has exactly 3 significant digit2.
        ['SigFigsStrict', '1', '1.00e2', 'null', 0, '', ''],
        ['SigFigsStrict', '2', '1.00e2', 'null', 0, '', ''],
        ['SigFigsStrict', '3', '1.00e2', 'null', 1, '', ''],
        ['SigFigsStrict', '4', '1.00e2', 'null', 0, '', ''],
        // 10.0 has exactly 3 significant digits.
        ['SigFigsStrict', '2', '10.0', 'null', 0, '', ''],
        ['SigFigsStrict', '3', '10.0', 'null', 1, '', ''],
        ['SigFigsStrict', '4', '10.0', 'null', 0, '', ''],
        // 0 has exactly 1 significant digits.
        ['SigFigsStrict', '1', '0', 'null', 1, '', ''],
        ['SigFigsStrict', '2', '0', 'null', 0, '', ''],
        // 0.0 has exactly 1 significant digits.
        ['SigFigsStrict', '1', '0.0', 'null', 1, '', ''],
        ['SigFigsStrict', '2', '0.0', 'null', 0, '', ''],
        // Accept that .0 has exactly 1 significant digits.
        ['SigFigsStrict', '1', '.0', 'null', 1, '', ''],
        ['SigFigsStrict', '2', '.0', 'null', 0, '', ''],
        // Accept that .001030 has exactly 4 significant digits.
        ['SigFigsStrict', '4', '.001030', 'null', 1, '', ''],
        // 0.00 has exactly 2 significant digits.
        ['SigFigsStrict', '1', '0.00', 'null', 0, '', ''],
        ['SigFigsStrict', '2', '0.00', 'null', 1, '', ''],
        ['SigFigsStrict', '3', '0.00', 'null', 0, '', ''],
        // Mix of notations.
        ['SigFigsStrict', '1', '25.00e1', 'null', 0, '', ''],
        ['SigFigsStrict', '3', '25.00e1', 'null', 0, '', ''],
        ['SigFigsStrict', '4', '25.00e1', 'null', 1, '', ''],
        ['SigFigsStrict', '5', '25.00e1', 'null', 0, '', ''],
        // Too many zeros.
        ['SigFigsStrict', '3', '15.1', '15.1', 1, '', ''],
        ['SigFigsStrict', '3', '15.10', '15.1', 0, '', ''],
        ['SigFigsStrict', '3', '15.100', '15.1', 0, '', ''],
        ['SigFigsStrict', '3', '9.81*m/s^2', 'null', 1, '', 'Units are ignored'],

        ['Units', '2', '1/0', '1', -1, 'ATUnits_STACKERROR_SAns.', ''],
        ['Units', '2', '1', '1/0', -1, 'ATUnits_STACKERROR_TAns.', ''],
        ['Units', '1/0', '1', '1', -1, 'ATUnits_STACKERROR_Opt.', ''],
        ['Units', '2', 'x-1)^2', '(x-1)^2', -1, 'ATUnitsTEST_FAILED-Empty SA.', ''],
        ['Units', '[3,x]', '12.3*m*s^(-1)', '3*m', -1, 'ATNumSigFigs_STACKERROR_not_integer.', ''],
        [
            'Units', '[1,2,3]', '3*m*s^(-1)', '3*m', -1,
            'ATNumSigFigs_STACKERROR_list_wrong_length.', '',
        ],
        ['Units', '3', '12.3*m*s^(-1)', '{12.3*m*s^(-1)}', -1, 'ATUnits_TA_not_expression.', ''],
        ['Units', '3', 'x=12.3*m*s^(-1)', '12.3*m*s^(-1)', 0, 'ATUnits_SA_not_expression.', ''],
        ['Units', '3', '12.3', '12.3*m', 0, 'ATUnits_SA_no_units.', 'Missing units'],
        ['Units', '3', '12', '12.3*m', 0, 'ATUnits_SA_no_units.', ''],
        ['Units', '3', '1/2', '12.3*m', 0, 'ATUnits_SA_no_units.', ''],
        ['Units', '3', 'e^(1/2)', '12.3*m', 0, 'ATUnits_SA_no_units.', ''],
        ['Units', '3', '9.81*m', '12.3', -1, 'ATUnits_SB_no_units.', ''],
        ['Units', '3', 'm/s', '12.3*m/s', 0, 'ATUnits_SA_only_units.', 'Only units'],
        ['Units', '3', 'm', '12.3*m/s', 0, 'ATUnits_SA_only_units.', ''],
        ['Units', '3', '9.81+m/s', '9.81*m/s', 0, 'ATUnits_SA_bad_units.', 'Bad units'],
        ['Units', '3', '12.3*m/s', '12.3*m/s', 1, 'ATUnits_units_match.', 'Basic tests'],
        ['Units', '3', '12.4*m/s', '12.3*m/s', 0, 'ATNumSigFigs_Inaccurate. ATUnits_units_match.', ''],
        ['Units', '[3,2]', '12.4*m/s', '12.3*m/s', 1, 'ATUnits_units_match.', ''],
        ['Units', '[3,2]', '12.45*m/s', '12.3*m/s', 0, 'ATNumSigFigs_WrongDigits. ATUnits_units_match.', ''],
        [
            'Units', '[3,2]', '13.45*m/s', '12.3*m/s', 0,
            'ATNumSigFigs_WrongDigits. ATNumSigFigs_Inaccurate. ATUnits_units_match.', '',
        ],
        [
            'Units', '[3,2]', '7.54E-5*(s*M)^-1', '5.625E-5*s^-1', 0,
            'ATNumSigFigs_VeryInaccurate. ATUnits_incompatible_units.', '',
        ],
        [
            'Units', '[3,2]', '7.54E-5*(s*M)^-1', 'stackunits(5.625E-5,1/s)', 0,
            'ATNumSigFigs_VeryInaccurate. ATUnits_incompatible_units.', '',
        ],
        [
            'Units', '3', '12*m/s', '12.3*m/s', 0,
            'ATNumSigFigs_WrongDigits. ATNumSigFigs_Inaccurate. ATUnits_units_match.', '',
        ],
        ['Units', '3', '-9.81*m/s^2', '-9.81*m/s^2', 1, 'ATUnits_units_match.', ''],
        ['Units', '3', '-9.82*m/s^2', '-9.815*m/s^2', 1, 'ATUnits_units_match.', ''],
        ['Units', '3', '-9.81*m/s^2', '-9.815*m/s^2', 0, 'ATNumSigFigs_Inaccurate. ATUnits_units_match.', ''],
        ['Units', '3', '-9.81*m*s^(-2)', '-9.81*m/s^2', 1, 'ATUnits_units_match.', ''],
        ['Units', '3', '-9.82*m/s^2', '-9.81*m/s^2', 0, 'ATNumSigFigs_Inaccurate. ATUnits_units_match.', ''],
        ['Units', '3', '-9.81*m*s^(-2)', '-9.81*m/s^2', 1, 'ATUnits_units_match.', ''],
        ['Units', '3', '-9.81*m/s/s', '-9.81*m/s^2', 1, 'ATUnits_units_match.', ''],
        ['Units', '3', '-9.81*m/s', '-9.81*m/s^2', 0, 'ATUnits_incompatible_units. ATUnits_correct_numerical.', ''],
        ['Units', '3', '-9.81*m/s', '-9.81*m/s^2', 0, 'ATUnits_incompatible_units. ATUnits_correct_numerical.', ''],
        // In the following test case the student uses brackets which are not needed, but broke the test.
        ['Units', '3', '(-9.81)*m/s^2', '-9.81*m/s^2', 1, 'ATUnits_units_match.', ''],
        ['Units', '3', '520*amu', '520*amu', 1, 'ATNumSigFigs_WithinRange. ATUnits_units_match.', ''],
        ['Units', '3', '520*amu', '521*amu', 0, 'ATNumSigFigs_WithinRange. ATNumSigFigs_Inaccurate. ATUnits_units_match.', ''],
        ['Units', '3', '(-9.81)', '-9.81*m/s^2', 0, 'ATUnits_SA_no_units.', 'Missing units'],
        ['Units', '3', '9.81*m/s', '-9.81*m/s^2', 0, 'ATNumSigFigs_WrongSign. ATUnits_incompatible_units.', ''],
        [
            'Units', '3', '8.81*m/s', '-9.81*m/s^2', 0,
            'ATNumSigFigs_WrongSign. ATNumSigFigs_VeryInaccurate. ATUnits_incompatible_units.', '',
        ],
        [
            'Units', '3', '8.1*m/s', '-9.81*m/s^2', 0,
            'ATNumSigFigs_WrongDigits. ATNumSigFigs_WrongSign. ATNumSigFigs_VeryInaccurate. ATUnits_incompatible_units.', '',
        ],
        ['Units', '3', 'm/4', '0.25*m', 0, 'ATNumSigFigs_WrongDigits. ATUnits_units_match.', ''],
        ['Units', '3', 'pi*s', '3.14*s', 0, 'ATNumSigFigs_WrongDigits. ATUnits_units_match.', 'Student is too exact'],
        ['Units', '3', 'sqrt(2)*m', '1.41*m', 0, 'ATNumSigFigs_WrongDigits. ATUnits_units_match.', ''],
        ['Units', '2', '25*g', '0.025*kg', 1, 'ATUnits_compatible_units kg.', 'Different units'],
        ['Units', '2', '26*g', '0.025*kg', 0, 'ATNumSigFigs_Inaccurate. ATUnits_compatible_units kg.', ''],
        [
            'Units', '2', '100*g', '10*kg', 0,
            'ATNumSigFigs_WithinRange. ATNumSigFigs_VeryInaccurate. ATUnits_compatible_units kg.', '',
        ],
        ['Units', '2', '0.025*g', '0.025*kg', 0, 'ATUnits_compatible_units kg. ATUnits_correct_numerical.', ''],
        ['Units', '2', '1000*m', '1*km', 1, 'ATNumSigFigs_WithinRange. ATUnits_compatible_units m.', ''],
        ['Units', '1', '1*Mg/10^6', '1*N*s^2/(km)', 1, 'ATUnits_compatible_units kg.', ''],
        ['Units', '1', '1*Mg/10^6', '1*kN*ns/(mm*Hz)', 1, 'ATUnits_compatible_units kg.', ''],
        ['Units', '3', '3.14*Mg/10^6', '%pi*kN*ns/(mm*Hz)', 1, 'ATUnits_compatible_units kg.', ''],
        ['Units', '3', '3.141*Mg/10^6', '%pi*kN*ns/(mm*Hz)', 0, 'ATNumSigFigs_WrongDigits. ATUnits_compatible_units kg.', ''],
        [
            'Units', '3', '4.141*Mg/10^6', '%pi*kN*ns/(mm*Hz)', 0,
            'ATNumSigFigs_WrongDigits. ATNumSigFigs_VeryInaccurate. ATUnits_compatible_units kg.', '',
        ],
        ['Units', '2', '400*cc', '0.4*l', 1, 'ATNumSigFigs_WithinRange. ATUnits_compatible_units m^3.', ''],
        ['Units', '2', '400*cm^3', '0.4*l', 1, 'ATNumSigFigs_WithinRange. ATUnits_compatible_units m^3.', ''],
        ['Units', '2', '400*ml', '0.4*l', 1, 'ATNumSigFigs_WithinRange. ATUnits_compatible_units m^3.', ''],
        ['Units', '2', '18*kJ', '18000.0*J', 1, 'ATUnits_compatible_units (kg*m^2)/s^2.', ''],
        ['Units', '2', '18.1*kJ', '18000.0*J', 0, 'ATNumSigFigs_WrongDigits. ATUnits_compatible_units (kg*m^2)/s^2.', ''],
        ['Units', '2', '120*kWh', '0.12*MWh', 1, 'ATUnits_compatible_units (kg*m^2)/s^2.', ''],
        ['Units', '2', '2.0*hh', '720000*s', 1, 'ATUnits_compatible_units s.', ''],
        ['Units', '3', '723*kVA', '0.723*MVA', 1, 'ATUnits_compatible_units VA.', ''],
        ['Units', '1', '0*m/s', '0*m/s', 1, 'ATUnits_units_match.', 'Edge case'],
        ['Units', '1', '0.0*m/s', '0*m/s', 1, 'ATUnits_units_match.', ''],
        ['Units', '1', '0*m/s', '0.0*m/s', 1, 'ATUnits_units_match.', ''],
        ['Units', '2', '0.00*m/s', '0.0*m/s', 1, 'ATUnits_units_match.', ''],
        ['Units', '1', '0.0*km/s', '0.0*m/s', 1, 'ATUnits_compatible_units m/s.', ''],
        ['Units', '1', '0.0*m', '0.0*m/s', 0, 'ATUnits_incompatible_units. ATUnits_correct_numerical.', ''],
        ['Units', '1', '0.0', '0.0*m/s', 0, 'ATUnits_SA_no_units.', ''],
        ['Units', '1', '7*in', '7*in', 1, 'ATUnits_units_match.', 'Imperial'],
        ['Units', '1', '6*in', '0.5*ft', 1, 'ATUnits_compatible_units in.', ''],
        ['Units', '4', '2640*ft', '0.5*mi', 1, 'ATNumSigFigs_WithinRange. ATUnits_compatible_units in.', ''],
        [
            'Units', '4', '2650*ft', '0.5*mi', 0,
            'ATNumSigFigs_WithinRange. ATNumSigFigs_VeryInaccurate. ATUnits_compatible_units in.', '',
        ],
        ['Units', '4', '142.8*C', '415.9*K', -3, 'ATNumSigFigs_VeryInaccurate. ATUnits_incompatible_units.', 'TO-DO'],
        // Atomic mass unit: numbers out of range.
        ['Units', '3', '520*mamu', '520*mamu', -3, 'ATUnits_SB_no_units.', ''],
        // Teacher uses stackunits in the answer, and displays nunmerical accuracy.
        ['Units', '3', '-9.82*m/s^2', 'stackunits(-9.815,m/s^2)', 1, 'ATUnits_units_match.', ''],
        ['Units', '3', '-9.81*m/s^2', 'stackunits(-9.815,m/s^2)', 0, 'ATNumSigFigs_Inaccurate. ATUnits_units_match.', ''],
        ['Units', '3', '-9.82*m/s^2', 'stackunits(displaydp(-9.815,3),m/s^2)', 1, 'ATUnits_units_match.', ''],
        ['Units', '3', '-9.82*m/s^2', 'stackunits(displaysf(-9.815,4),m/s^2)', 1, 'ATUnits_units_match.', ''],

        ['UnitsStrict', '2', '25*g', '0.025*kg', 0, 'ATUnits_compatible_units kg.', 'Differences from the Units test only'],
        ['UnitsStrict', '1', '1*Mg/10^6', '1*N*s^2/(km)', 0, 'ATUnits_compatible_units kg.', ''],
        ['UnitsStrict', '1', '1*Mg/10^6', '1*kN*ns/(mm*Hz)', 0, 'ATUnits_compatible_units kg.', ''],
        ['UnitsStrict', '3', '3.14*Mg/10^6', '%pi*kN*ns/(mm*Hz)', 0, 'ATUnits_compatible_units kg.', ''],
        ['UnitsStrict', '2', '400*cc', '0.4*l', 0, 'ATNumSigFigs_WithinRange. ATUnits_compatible_units m^3.', ''],
        ['UnitsStrict', '2', '400*cm^3', '0.4*l', 0, 'ATNumSigFigs_WithinRange. ATUnits_compatible_units m^3.', ''],
        ['UnitsStrict', '2', '400*ml', '0.4*l', 0, 'ATNumSigFigs_WithinRange. ATUnits_compatible_units m^3.', ''],
        ['UnitsStrict', '2', '400*mL', '0.4*l', 0, 'ATNumSigFigs_WithinRange. ATUnits_compatible_units m^3.', ''],
        ['UnitsStrict', '4', '142.8*C', '415.9*K', 0, 'ATNumSigFigs_VeryInaccurate. ATUnits_incompatible_units.', ''],
        ['UnitsStrict', '3', '-9.81*m/s/s', '-9.81*m/s^2', 1, 'ATUnits_units_match.', 'We are not *that* strict!'],
        ['UnitsStrict', '1', '0*m/s', '0*m/s', 1, 'ATUnits_units_match.', 'Edge case'],
        ['UnitsStrict', '1', '0.0*m/s', '0*m/s', 1, 'ATUnits_units_match.', ''],
        ['UnitsStrict', '1', '0*m/s', '0.0*m/s', 1, 'ATUnits_units_match.', ''],
        ['UnitsStrict', '1', '0.0*m/s', '0.0*m/s', 1, 'ATUnits_units_match.', ''],
        ['UnitsStrict', '1', '0.0*km/s', '0.0*m/s', 0, 'ATUnits_compatible_units m/s.', ''],
        ['UnitsStrict', '1', '0.0*m', '0.0*m/s', 0, 'ATUnits_incompatible_units. ATUnits_correct_numerical.', ''],
        ['UnitsStrict', '1', '0.0', '0.0*m/s', 0, 'ATUnits_SA_no_units.', ''],
        ['UnitsStrict', '[3,2]', '2.33e-15*kg', '2.33e-15*kg', 1, 'ATUnits_units_match.', ''],
        ['UnitsStrict', '[3,2]', '7.03e-3*ng', '7.03e-3*ng', 1, 'ATUnits_units_match.', ''],
        ['UnitsStrict', '[3,2]', '2.35e-6*ug', '2.35e-6*ug', 1, 'ATUnits_units_match.', ''],
        ['UnitsStrict', '[3,2]', '9.83e-10*cg', '9.83e-10*cg', 1, 'ATUnits_units_match.', ''],
        ['UnitsStrict', '[3,2]', '9.73e-21*Gg', '9.73e-21*Gg', 1, 'ATUnits_units_match.', ''],
        ['UnitsStrict', '[3,2]', '7.19e-15*kg', '7.19e-15*kg', 1, 'ATUnits_units_match.', ''],
        ['UnitsStrict', '[3,2]', '8.12e-12*g', '8.12e-12*g', 1, 'ATUnits_units_match.', ''],
        ['UnitsStrict', '[3,2]', '9.34e-12*g', '9.34e-12*g', 1, 'ATUnits_units_match.', ''],
        ['UnitsStrict', '[3,2]', '1.07e-21*Gg', '1.07e-21*Gg', 1, 'ATUnits_units_match.', ''],
        ['UnitsStrict', '[3,2]', '1.91e-10*cg', '1.91e-10*cg', 1, 'ATUnits_units_match.', ''],
        ['UnitsStrict', '[3,2]', '5.67e-18*Mg', '5.67e-18*Mg', 1, 'ATUnits_units_match.', ''],
        ['UnitsStrict', '[3,2]', '2.04e-9*mg', '2.04e-9*mg', 1, 'ATUnits_units_match.', ''],
        ['UnitsStrict', '[3,2]', '6.75e-6*ug', '6.75e-6*ug', 1, 'ATUnits_units_match.', ''],
        ['UnitsStrict', '[3,2]', '6.58e-6*ug', '6.58e-6*ug', 1, 'ATUnits_units_match.', ''],
        ['UnitsStrict', '[3,2]', '3.58e-9*mg', '3.58e-9*mg', 1, 'ATUnits_units_match.', ''],
        ['UnitsStrict', '[3,2]', '9.99e-15*kg', '9.99e-15*kg', 1, 'ATUnits_units_match.', ''],
        [
            'UnitsStrict', '[3,2]', '9.8e-9*mg', '9.8e-9*mg', 0,
            'ATNumSigFigs_WrongDigits. ATUnits_units_match.', '',
        ],
        ['UnitsStrict', '[3,2]', '9.80e-9*mg', '9.8e-9*mg', 1, 'ATUnits_units_match.', ''],
        ['UnitsStrict', '[3,2]', '9.83e-9*mg', '9.8e-9*mg', 1, 'ATUnits_units_match.', ''],
        ['UnitsStrict', '[3,2]', '9.78e-9*mg', '9.8e-9*mg', 1, 'ATUnits_units_match.', ''],
        ['UnitsStrict', '2', '36*Kj/mol', '36*Kj/mol', 1, 'ATUnits_units_match.', ''],
        ['UnitsStrict', '2', '-36*Kj/mol', '-36*Kj/mol', 1, 'ATUnits_units_match.', ''],
        ['UnitsStrict', '2', '(-36)*Kj/mol', '-36*Kj/mol', 1, 'ATUnits_units_match.', ''],
        ['UnitsStrict', '2', '(-36*Kj)/mol', '-36*Kj/mol', 1, 'ATUnits_units_match.', ''],
        ['UnitsStrict', '2', '-(36*Kj)/mol', '-36*Kj/mol', 1, 'ATUnits_units_match.', ''],
        [
            'UnitsStrict', '2', '-(36.2*Kj)/mol', '-36.3*Kj/mol', 0,
            'ATNumSigFigs_WrongDigits. ATUnits_units_match.', '',
        ],
        ['UnitsStrict', '[3,2]', '3.58e-9*mg', 'displaydp(3.58e-9,2)*mg', 1, 'ATUnits_units_match.', ''],
        ['UnitsStrict', '[3,2]', '3.58e-9*mg', 'displaysf(3.58e-9,3)*mg', 1, 'ATUnits_units_match.', ''],
        ['UnitsStrict', '3', '-9.82*m/s^2', 'stackunits(displaydp(-9.815,3),m/s^2)', 1, 'ATUnits_units_match.', ''],
        ['UnitsStrict', '3', '-9.82*m/s^2', 'stackunits(displaysf(-9.815,4),m/s^2)', 1, 'ATUnits_units_match.', ''],

        ['UnitsRelative', '0.01', '12.3*m/s', '12.3*m/s', 1, 'ATUnits_units_match.', ''],
        ['UnitsRelative', '0.01', '12*m/s', '12.3*m/s', 0, 'ATUnits_units_match.', ''],
        ['UnitsRelative', '0.15', '1.1*Mg/10^6', '1.2*kN*ns/(mm*Hz)', 1, 'ATUnits_compatible_units kg.', ''],
        ['UnitsRelative', '0.05', '1.1*Mg/10^6', '1.2*kN*ns/(mm*Hz)', 0, 'ATUnits_compatible_units kg.', ''],
        ['UnitsRelative', '0.01', '0*m/s', '0*m/s', 1, 'ATUnits_units_match.', 'Edge case'],
        ['UnitsRelative', '0.01', '0.0*m/s', '0*m/s', 1, 'ATUnits_units_match.', ''],
        ['UnitsRelative', '0.01', '0*m/s', '0.0*m/s', 1, 'ATUnits_units_match.', ''],
        ['UnitsRelative', '0.01', '0.0*m/s', '0.0*m/s', 1, 'ATUnits_units_match.', ''],
        ['UnitsRelative', '0.01', '0.0*km/s', '0.0*m/s', 1, 'ATUnits_compatible_units m/s.', ''],
        ['UnitsRelative', '0.01', '0.0*m', '0.0*m/s', 0, 'ATUnits_incompatible_units. ATUnits_correct_numerical.', ''],
        ['UnitsRelative', '0.01', '0.0', '0.0*m/s', 0, 'ATUnits_SA_no_units.', ''],
        ['UnitsRelative', '0.002', '0.0*kVA', '0.0*kVA', 1, 'ATUnits_units_match.', ''],

        ['UnitsStrictRelative', '0.01', '12.3*m/s', '12.3*m/s', 1, 'ATUnits_units_match.', ''],
        ['UnitsStrictRelative', '0.01', '12.3*m/s', 'stackunits(12.3,m/s)', 1, 'ATUnits_units_match.', ''],
        ['UnitsStrictRelative', '0.01', '12.3*m/s', 'stackunits(displaydp(12.3,1),m/s)', 1, 'ATUnits_units_match.', ''],
        ['UnitsStrictRelative', '0.01', '12*m/s', '12.3*m/s', 0, 'ATUnits_units_match.', ''],
        ['UnitsStrictRelative', '0.15', '1.1*Mg/10^6', '1.2*kN*ns/(mm*Hz)', 0, 'ATUnits_compatible_units kg.', ''],
        ['UnitsStrictRelative', '0.05', '1.1*Mg/10^6', '1.2*kN*ns/(mm*Hz)', 0, 'ATUnits_compatible_units kg.', ''],
        ['UnitsStrictRelative', '0.01', '0*m/s', '0*m/s', 1, 'ATUnits_units_match.', 'Edge case'],
        ['UnitsStrictRelative', '0.01', '0.0*m/s', '0*m/s', 1, 'ATUnits_units_match.', ''],
        ['UnitsStrictRelative', '0.01', '0*m/s', '0.0*m/s', 1, 'ATUnits_units_match.', ''],
        ['UnitsStrictRelative', '0.01', '0.0*m/s', '0.0*m/s', 1, 'ATUnits_units_match.', ''],
        ['UnitsStrictRelative', '0.01', '0.0*km/s', '0.0*m/s', 0, 'ATUnits_compatible_units m/s.', ''],
        ['UnitsStrictRelative', '0.01', '0.0*m', '0.0*m/s', 0, 'ATUnits_incompatible_units. ATUnits_correct_numerical.', ''],
        ['UnitsStrictRelative', '0.01', '0.0', '0.0*m/s', 0, 'ATUnits_SA_no_units.', ''],
        ['UnitsStrictRelative', '0.01', '0*J', '0.0*J', 1, 'ATUnits_units_match.', ''],

        ['UnitsAbsolute', '5*J', '-123000*J', '-123*kJ', 0, 'ATUnits_SO_wrong_units.', ''],
        ['UnitsAbsolute', '0.01', '12.3*m/s', '12.3*m/s', 1, 'ATUnits_units_match.', ''],
        ['UnitsAbsolute', '0.01', '12*m/s', '12.3*m/s', 0, 'ATUnits_units_match.', ''],
        ['UnitsAbsolute', '0.15', '1.1*Mg/10^6', '1.2*kN*ns/(mm*Hz)', 1, 'ATUnits_compatible_units kg.', ''],
        [
            'UnitsAbsolute', '0.1', '1.1*Mg/10^6', '1.2*kN*ns/(mm*Hz)', 1, 'ATUnits_compatible_units kg.',
            'The following illustrates that we convert to base units to compare.',
        ],
        ['UnitsAbsolute', '0.09', '1.1*Mg/10^6', '1.2*kN*ns/(mm*Hz)', 0, 'ATUnits_compatible_units kg.', ''],
        ['UnitsAbsolute', '5*kJ', '-123000*J', '-123*kJ', 1, 'ATUnits_compatible_units (kg*m^2)/s^2.', 'Units in the options'],
        ['UnitsAbsolute', '5*kJ', '-123006*J', '-123*kJ', 1, 'ATUnits_compatible_units (kg*m^2)/s^2.', ''],
        ['UnitsAbsolute', '5*kJ', '-123006*J', 'stackunits(-123,kJ)', 1, 'ATUnits_compatible_units (kg*m^2)/s^2.', ''],
        ['UnitsAbsolute', '5*kJ', '-129006*J', '-123*kJ', 0, 'ATUnits_compatible_units (kg*m^2)/s^2.', ''],
        ['UnitsAbsolute', '0.1*kN*ns/(mm*Hz)', '1.1*Mg/10^6', '1.2*kN*ns/(mm*Hz)', 1, 'ATUnits_compatible_units kg.', ''],
        ['UnitsAbsolute', '0.09*kN*ns/(mm*Hz)', '1.1*Mg/10^6', '1.2*kN*ns/(mm*Hz)', 0, 'ATUnits_compatible_units kg.', ''],
        ['UnitsAbsolute', '0.01', '0*m/s', '0*m/s', 1, 'ATUnits_units_match.', 'Edge case'],
        ['UnitsAbsolute', '0.01', '0.0*m/s', '0*m/s', 1, 'ATUnits_units_match.', ''],
        ['UnitsAbsolute', '0.01', '0*m/s', '0.0*m/s', 1, 'ATUnits_units_match.', ''],
        ['UnitsAbsolute', '0.01', '0.0*m/s', '0.0*m/s', 1, 'ATUnits_units_match.', ''],
        ['UnitsAbsolute', '0.01', '0.0*km/s', '0.0*m/s', 1, 'ATUnits_compatible_units m/s.', ''],
        ['UnitsAbsolute', '0.01', '0.0*m', '0.0*m/s', 0, 'ATUnits_incompatible_units. ATUnits_correct_numerical.', ''],
        ['UnitsAbsolute', '0.01', '0.0', '0.0*m/s', 0, 'ATUnits_SA_no_units.', ''],
        // The teacher's answer here is likely to be the result of internal simplification.
        ['UnitsAbsolute', '0.01', '1.0*m/s', 'm/s', 1, 'ATUnits_units_match.', ''],
        ['UnitsAbsolute', '0.01', '15/pi*kN/mm^2', '15/pi*kN/mm^2', 1, 'ATUnits_units_match.', ''],
        ['UnitsAbsolute', '0.01', '(15*kN)/(pi*mm^2)', '(15*kN)/(pi*mm^2)', 1, 'ATUnits_units_match.', ''],
        ['UnitsAbsolute', '0.01', '(15/pi)*(kN/mm^2)', '(15/pi)*(kN/mm^2)', 1, 'ATUnits_units_match.', ''],
        ['UnitsAbsolute', '0.01', '(600*N)/(%pi*mm^2)', '(600*N)/(%pi*mm^2)', 1, 'ATUnits_units_match.', ''],
        ['UnitsAbsolute', '0.01', '(600/pi)*kN/m^2', '(600/pi)*kN/m^2', 1, 'ATUnits_units_match.', ''],
        ['UnitsAbsolute', '0.01', '(600/pi)*kN/mm^2', '(600/pi)*kN/mm^2', 1, 'ATUnits_units_match.', ''],

        ['UnitsStrictAbsolute', '10.0', '2301.0*mm', '2300.0*mm', 1, 'ATUnits_units_match.', ''],
        ['UnitsStrictAbsolute', '10.0', '2321.0*mm', '2300.0*mm', 0, 'ATUnits_units_match.', ''],
        ['UnitsStrictAbsolute', '10.0', '2.301*m', '2300.0*mm', 0, 'ATUnits_compatible_units m.', ''],
        ['UnitsStrictAbsolute', '10.0', '2.321*m', '2300.0*mm', 0, 'ATUnits_compatible_units m.', ''],
        ['UnitsStrictAbsolute', '10.0', '2.301*kg', '2300.0*mm', 0, 'ATUnits_incompatible_units.', ''],

        ['String', '', '"Hello"', '"hello"', 0, '', ''],
        ['String', '', '"hello"', '"hello"', 1, '', ''],
        ['String', '', '"hello"', '"heloo"', 0, '', ''],
        ['String', '', '"With spaces"', '"With spaces"', 1, '', ''],
        ['String', '', '"Without spaces"', '"Withoutspaces"', 0, '', ''],
        ['String', '', '" Hello "', '"Hello"', 0, '', 'Whitespace not trimmed off inside strings'],
        ['String', '', ' Hello ', 'Hello', 1, '', 'Whitespace is trimmed off around atoms'],
        ['String', '', 'sin(x^2)', '"sin(x^2)"', 1, '', 'This test works on expressions as well as strings'],
        ['String', '', 'pi^2/6', '"pi^2/6"', 1, '', ''],
        ['String', '', 'pi^2/6', '"%pi^2/6"', 0, '', ''],

        ['StringSloppy', '', '"hello"', '"hello"', 1, '', ''],
        ['StringSloppy', '', '"hello"', '"heloo"', 0, '', ''],
        ['StringSloppy', '', '"hel lo"', '"hello"', 1, '', ''],
        ['StringSloppy', '', '"hel lo"', '"Hel*lo"', 0, '', ''],
        ['StringSloppy', '', '" hel   lo    "', '"hello"', 1, '', ''],

        ['Levenshtein', '', '"Hello"', '"Hello"', 0, 'STACKERROR_OPTION.', ''],
        ['Levenshtein', '0.9', '1/0', '"Hello"', -1, 'ATLevenshtein_STACKERROR_SAns.', ''],
        ['Levenshtein', '0.9', 'x^2', '"Hello"', 0, 'ATLevenshtein_SA_not_string.', ''],
        ['Levenshtein', '0.9', '"Hello"', '"Hello"', 0, 'ATLevenshtein_SB_malformed.', ''],
        ['Levenshtein', '0.9', '"Hello"', '["Hello"]', 0, 'ATLevenshtein_SB_malformed.', ''],
        ['Levenshtein', '0.9', '"Hello"', '[["Hello"]]', 0, 'ATLevenshtein_SB_malformed.', ''],
        ['Levenshtein', '0.9', '"Hello"', '[["Hello"], x^2]', 0, 'ATLevenshtein_SB_malformed.', ''],
        ['Levenshtein', '0.9', '"Hello"', '[["Hello"], [x^2]]', 0, 'ATLevenshtein_SB_malformed.', ''],
        [
            'Levenshtein', '0.9', '"Hello"', '[["Hello"], ["Goodbye"], ["Excess"]]', 0,
            'ATLevenshtein_SB_malformed.', '',
        ],
        ['Levenshtein', '0.9', '"Hello"', '[[], ["Goodbye"]]', 0, 'ATLevenshtein_SB_malformed.', ''],
        ['Levenshtein', 'z', '"Hello"', '[["Hello"], ["Goodbye"]]', 0, 'ATLevenshtein_tol_not_number.', ''],
        ['Levenshtein', '[z]', '"Hello"', '[["Hello"], ["Goodbye"]]', 0, 'ATLevenshtein_tol_not_number.', ''],
        // Functionality test.
        [
            'Levenshtein', '0.9', '"Hello"', '[["Hello"], ["Goodbye"]]', 1,
            'ATLevenshtein_true: [[1.0,"Hello"],[0.0,"Goodbye"]].', 'Usage tests',
        ],
        [
            'Levenshtein', '[0.9]', '"hello"', '[["Hello"], ["Goodbye"]]', 1,
            'ATLevenshtein_true: [[1.0,"Hello"],[0.0,"Goodbye"]].', '',
        ],
        // Also tests <= in comparisons, using a fine error.
        [
            'Levenshtein', '[0.8, CASE]', '"hello"', '[["Hello", "Good day", "Hi"], ["Goodbye", "Bye", "Fairwell"]]', 1,
            'ATLevenshtein_match: [[0.8,"Hello"],[0.25,"Fairwell"]].', '',
        ],
        [
            'Levenshtein', '0.8', '"goodday"', '[["Hello", "Good day", "Hi"], ["Goodbye", "Bye", "Fairwell"]]', 1,
            'ATLevenshtein_match: [[0.875,"Good day"],[0.57143,"Goodbye"]].', '',
        ],
        [
            'Levenshtein', '[0.8, CASE]', '"goodday"', '[["Hello", "Good day", "Hi"], ["Goodbye", "Bye", "Fairwell"]]', 0,
            'ATLevenshtein_far: [[0.75,"Good day"],[0.42857,"Goodbye"]].', '',
        ],
        [
            'Levenshtein', '0.9', '"Jello"', '[["Hello", "Good day", "Hi"], ["Goodbye", "Bye", "Fairwell"]]', 0,
            'ATLevenshtein_far: [[0.8,"Hello"],[0.25,"Fairwell"]].', '',
        ],
        [
            'Levenshtein', '0.75', '"Jello"', '[["Hello", "Good day", "Hi"], ["Goodbye", "Bye", "Fairwell"]]',
            1, 'ATLevenshtein_match: [[0.8,"Hello"],[0.25,"Fairwell"]].', '',
        ],
        [
            'Levenshtein', '0.75', '"Jello"', '[["Hello", "Good day", "Hi"], []]',
            1, 'ATLevenshtein_match: [[0.8,"Hello"],[0,[]]].', '',
        ],
        [
            'Levenshtein', '0.75', '"Good bye"', '[["Hello", "Good day", "Hi"], ["Goodbye", "Bye", "Fairwell"]]',
            0, 'ATLevenshtein_deny: [[0.625,"Good day"],[0.875,"Goodbye"]].', '',
        ],
        [
            'Levenshtein', '0.75', '"Good, day!"', '[["Hello", "Good day", "Hi"], ["Goodbye", "Bye", "Fairwell"]]',
            1, 'ATLevenshtein_match: [[0.8,"Good day"],[0.5,"Goodbye"]].', '',
        ],
        [
            'Levenshtein', '0.75', 'sremove_chars(".,!?", "Good, day!")',
            '[["Hello", "Good day", "Hi"], ["Goodbye", "Bye", "Fairwell"]]',
            1, 'ATLevenshtein_true: [[1.0,"Good day"],[0.5,"Goodbye"]].', '',
        ],
        [
            'Levenshtein', '0.75', '"   good     day  "',
            '[["Hello", "Good day", "Hi"], ["Goodbye", "Bye", "Fairwell"]]',
            1, 'ATLevenshtein_true: [[1.0,"Good day"],[0.5,"Goodbye"]].', '',
        ],
        [
            'Levenshtein', '[0.75, WHITESPACE]', '"   good     day  "',
            '[["Hello", "Good day", "Hi"], ["Goodbye", "Bye", "Fairwell"]]',
            0, 'ATLevenshtein_far: [[0.47059,"Good day"],[0.29412,"Goodbye"]].', '',
        ],

        ['SRegExp', '', '1/0', '"hello"', -1, 'ATSRegExp_STACKERROR_SAns.', ''],
        ['SRegExp', '', '"1/0"', '1/0', -1, 'ATSRegExp_STACKERROR_TAns.', ''],
        ['SRegExp', '', 'Hello', 'hello', -1, 'ATSRegExp_SB_not_string.', ''],
        ['SRegExp', '', 'Hello', '"hello"', -1, 'ATSRegExp_SA_not_string.', ''],
        ['SRegExp', '', '"aaaaabbb"', '"(aaa)*b"', 1, 'ATSRegExp: ["aaab","aaa"].', ''],
        ['SRegExp', '', '"aab"', '"(aaa)*b"', 1, 'ATSRegExp: ["b",false].', ''],
        ['SRegExp', '', '"aaac"', '"(aaa)*b"', 0, '', ''],
        [
            'SRegExp', '', '"aab"', '"^[aA]*b$"', 1, 'ATSRegExp: ["aab"].',
            'Anchor pattern to the start and the end of the string',
        ],
        ['SRegExp', '', '"aab"', '"^(aaa)*b$"', 0, '', ''],
        ['SRegExp', '', '"aAb"', '"^[aA]*b$"', 1, 'ATSRegExp: ["aAb"].', ''],
        ['SRegExp', '', '" aAb"', '"^[aA]*b$"', 0, '', ''],
        ['SRegExp', '', '"caAb"', '"(?i:a*b)"', 1, 'ATSRegExp: ["aAb"].', 'Case insensitive'],
        [
            'SRegExp', '', '"Alice went to the market"', '"(Alice|Bob) went to the (bank|market)"', 1,
            'ATSRegExp: ["Alice went to the market","Alice","market"].', 'Options',
        ],
        ['SRegExp', '', '"Malice went to the shop"', '"(Alice|Bob) went to the (bank|market)"', 0, '', ''],
        [
            'SRegExp', '', '"Alice   went  to      the market"', '"(Alice|Bob)\\\\s+went\\\\s+to\\\\s+the\\\\s+(bank|market)"',
            1, 'ATSRegExp: ["Alice   went  to      the market","Alice","market"].', 'Whitespace, ' .
            'note test rendering issue, the test string has additional spaces and tabs as does the result',
        ],
        [
            'SRegExp', '', '"Alice   went  to      themarket"',
            '"(Alice|Bob)\\\\s+went\\\\s+to\\\\s+the\\\\s+(bank|market)"', 0, '', '',
        ],
        [
            'SRegExp', '', '"x^2.2"', '"x\\\\^2\\\\.2"', 1, 'ATSRegExp: ["x^2.2"].',
            'Escaping patterns, note the function that does it',
        ],
        ['SRegExp', '', '"x^2+sin(x)"', 'sconcat(string_to_regex("sin(x)"),"$")', 1, 'ATSRegExp: ["sin(x)"].', ''],
        ['SRegExp', '', '"sin(x)+x^2"', 'sconcat(string_to_regex("sin(x)"),"$")', 0, '', ''],

        ['LowestTerms', '', '1/0', '0', -1, 'ATLowestTerms_STACKERROR_SAns.', ''],
        ['LowestTerms', '', '0.5', '0', 1, '', 'Mix of floats and rational numbers'],
        ['LowestTerms', '', '0.33', '0', 1, '', ''],
        ['LowestTerms', '', '2/4', '0', 0, 'ATLowestTerms_entries.', ''],
        ['LowestTerms', '', '-1/3', '0', 1, '', 'Negative numbers'],
        ['LowestTerms', '', '1/-3', '0', 1, '', ''],
        ['LowestTerms', '', '-2/4', '0', 0, 'ATLowestTerms_entries.', ''],
        ['LowestTerms', '', '2/-4', '0', 0, 'ATLowestTerms_entries.', ''],
        ['LowestTerms', '', '-1/-3', '0', 0, 'ATLowestTerms_entries.', ''],
        ['LowestTerms', '', '-2/-4', '0', 0, 'ATLowestTerms_entries.', ''],
        ['LowestTerms', '', 'x+1/3', '0', 1, '', 'Polynomials'],
        ['LowestTerms', '', 'x+2/6', '0', 0, 'ATLowestTerms_entries.', ''],
        ['LowestTerms', '', '2*x/4+2/6', '0', 0, 'ATLowestTerms_entries.', ''],
        ['LowestTerms', '', '2/4*x+2/6', '0', 0, 'ATLowestTerms_entries.', ''],
        ['LowestTerms', '', 'x-1/-4', '0', 0, 'ATLowestTerms_entries.', ''],
        ['LowestTerms', '', 'cos(x)', '0', 1, '', 'Trig functions'],
        ['LowestTerms', '', 'cos(3/6*x)', '0', 0, 'ATLowestTerms_entries.', ''],
        ['LowestTerms', '', 'matrix([1,2/4],[2,3])', '0', 0, 'ATLowestTerms_entries.', 'Matrices'],
        ['LowestTerms', '', 'x=1/2', '0', 1, '', 'Equations'],
        ['LowestTerms', '', '3/9=x', '0', 0, 'ATLowestTerms_entries.', ''],
        ['LowestTerms', '', 'x^2/x', '0', 1, '', 'Use predicate lowesttermsp'],
        ['LowestTerms', '', '(2*x)/(4*t)', '0', 1, '', ''],
        ['LowestTerms', '', '(2/4)*(x^2/t)', '0', 0, 'ATLowestTerms_entries.', ''],
        ['LowestTerms', '', 'x^(2/4)', '0', 0, 'ATLowestTerms_entries.', ''],
        ['LowestTerms', '', 'sqrt(3)/3', 'sqrt(3)/3', 1, '', 'Need to rationalize demoninator'],
        ['LowestTerms', '', '1/sqrt(3)', 'sqrt(3)/3', 0, 'ATLowestTerms_not_rat.', ''],
        ['LowestTerms', '', '1/(1-sqrt(2))', '1/(1-sqrt(2))', 0, 'ATLowestTerms_not_rat.', ''],
        ['LowestTerms', '', '1/(1+i)', '(1-i)/2', 0, 'ATLowestTerms_not_rat.', ''],
        ['LowestTerms', '', '1+2/sqrt(3)', '(2*sqrt(3)+3)/3', 0, 'ATLowestTerms_not_rat.', ''],
        ['LowestTerms', '', '1/(1+1/root(3,2))', 'sqrt(3)/(sqrt(3)+1)', 0, 'ATLowestTerms_not_rat.', ''],
        ['LowestTerms', '', '1/(1+1/root(2,3))', '1/(1+1/root(2,3))', 0, 'ATLowestTerms_not_rat.', ''],

        ['Validator', 'validate_nofunctions', '1/0', '0', -1, 'ATValidator_STACKERROR_SAns.', ''],
        ['Validator', '1/0', 'x', '0', -1, 'ATValidator_STACKERROR_Opt.', ''],
        ['Validator', 'op', 'x', 'null', -1, 'ATValidator_STACKERROR_ev.', ''],
        ['Validator', '[validate_nofunctions]', 'x^2+sin(1)', 'null', 0, 'ATValidator_not_fun.', ''],
        ['Validator', 'validate_nodef', 'f(x)', 'null', 0, 'ATValidator_not_fun.', ''],
        ['Validator', 'sin', 'x', 'null', 0, 'ATValidator_not_fun.', ''],
        ['Validator', 'first', '[1,2,3]', 'null', 0, 'ATValidator_res_not_string.', ''],
        ['Validator', 'validate_nofunctions', 'x^2+sin(1)', 'null', 1, '', ''],
        ['Validator', 'validate_nofunctions', 'f(x)', 'null', 0, '', ''],

        ];

    public static function get_raw_test_data() {
        $equiv = new stack_equiv_test_data();
        return array_merge(self::$rawdata, $equiv->get_answertestfixtures());
    }

    public static function get_available_tests() {
        $availabletests = [];
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
        $test->ansnote       = $data[self::ANSNOTE];
        $test->notes         = $data[self::NOTES];
        return $test;
    }

    public static function get_all() {
        $tests = [];
        $rawdata = self::get_raw_test_data();
        foreach ($rawdata as $data) {
            $tests[] = self::test_from_raw($data);
        }
        return $tests;
    }

    public static function get_tests_for($anstest) {
        $tests = [];
        $rawdata = self::get_raw_test_data();

        foreach ($rawdata as $data) {
            if ($data[self::NAME] == $anstest) {
                $tests[] = self::test_from_raw($data);
            }
        }
        return $tests;
    }

    public static function run_test($test) {
        $sans = stack_ast_container::make_from_teacher_source($test->studentanswer, '', new stack_cas_security());
        $tans = stack_ast_container::make_from_teacher_source($test->teacheranswer, '', new stack_cas_security());
        $topt = stack_ast_container::make_from_teacher_source($test->options, '', new stack_cas_security());

        $anst = new stack_ans_test_controller($test->name, $sans, $tans, $topt, new stack_options());

        // The false clause is useful for developers to track down which test case is breaking Maxima.
        if (true) {
            $result   = $anst->do_test(); // This actually executes the answer test in the CAS.
            $errors   = $anst->get_at_errors();
            $rawmark  = $anst->get_at_mark();
            $feedback = $anst->get_at_feedback();
            $ansnote  = $anst->get_at_answernote();
        } else {
            $feedback = 'AT'.$test->name.'('.$test->studentanswer.','.$test->teacheranswer.');';
            $result   = true; // This actually executes the answer test in the CAS.
            $errors   = '';
            $rawmark  = 0;
            $ansnote  = '';
        }

        $trace = $anst->get_trace(false);
        $anomalynote = [];
        $passed = true;
        if ($test->expectedscore >= 0) {
            if ($rawmark !== $test->expectedscore) {
                $passed = false;
                $anomalynote[] = '[SCORE]';
            }
        } else {
            // We expect the test to fail.
            switch ($test->expectedscore) {
                case -1:
                    if ($errors !== '') {
                        $passed = true;
                    } else {
                        $passed = false;
                        $anomalynote[] = '[Expected test to fail!]';
                    }
                    break;

                case -2:
                    if ($rawmark) {
                        $passed = true;
                    } else {
                        $passed = false;
                        $anomalynote[] = '[Expected maths failure: got 0, expected 1.]';
                    }
                    break;

                case -3:
                    if ($rawmark) {
                        $passed = false;
                        $anomalynote[] = '[Expected maths failure: got 1, expected 0.]';
                    } else {
                        $passed = true;
                    }
                    break;

                default:
                    $passed = false;
                    $anomalynote[] = '[General failure.]';
            }
        }

        if (!($ansnote === $test->ansnote)) {
            $passed = false;
            $anomalynote[] = '[NOTE expected: ' . $test->ansnote . ']';
        }

        $anomalynote = implode(' | ', $anomalynote);
        return [$passed, $errors, $rawmark, $feedback, $ansnote, $anomalynote, $trace];
    }
}

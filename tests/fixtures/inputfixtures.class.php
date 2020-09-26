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
 * This script runs the student input tests and verifies the results.
 *
 * This helps us verify how STACK "validates" strings supplied by the student.
 *
 * @copyright  2012 University of Birmingham
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../../stack/cas/cassession2.class.php');

class stack_inputvalidation_test_data {

    const RAWSTRING     = 0;
    const PHPVALID      = 1;
    const PHPCASSTRING  = 2;
    const CASVALID      = 3;
    const DISPLAY       = 4;
    const ANSNOTES      = 5;
    const NOTES         = 6;

    protected static $rawdata = array(

        array('123', 'php_true', '123', 'cas_true', '123', '', ""),
        array('x', 'php_true', 'x', 'cas_true', 'x', '', ""),
        array('xy', 'php_true', 'xy', 'cas_true', '{\it xy}', '', "This is a single variable name, not a product."),
        array('x+1', 'php_true', 'x+1', 'cas_true', 'x+1', '', ""),
        array('x+ 1', 'php_true', 'x+1', 'cas_true', 'x+1', '', ""),
        array('x + 1', 'php_true', 'x+1', 'cas_true', 'x+1', '', "Ok to have some spaces between these operators."),
        array('sin x', 'php_false', '', '', '', 'spaces | trigspace',
            "Maxima does not allow spaces to denote function application."),
        array('x y', 'php_false', '', '', '', 'spaces', "We don't allow spaces to denote implicit multiplication."),
        array('1 x', 'php_false', '', '', '', 'spaces', ""),
        array('12 3', 'php_false', '', '', '', 'spaces', ""),
        array('12 3.7', 'php_false', '', '', '', 'spaces', ""),
        array('12.5 3', 'php_false', '', '', '', 'spaces', ""),

        array('1x', 'php_true', '1*x', 'cas_true', '1\cdot x', 'missing_stars', ""),
        array('x1', 'php_true', 'x*1', 'cas_true', 'x\cdot 1', 'missing_stars', ""),
        array('1', 'php_true', '1', 'cas_true', '1', '', "Numbers"),
        array('.1', 'php_true', '.1', 'cas_true', '0.1', '', ""),
        array('1/2', 'php_true', '1/2', 'cas_true', '\frac{1}{2}', '', ""),
        array('2/4', 'php_true', '2/4', 'cas_true', '\frac{2}{4}', 'Lowest_Terms',
            "Rejecting this as 'invalid' not 'wrong' is a question option."),
        array('-10/-1', 'php_true', '-10/-1', 'cas_true', '\frac{-10}{-1}', 'Lowest_Terms', ""),
        array('1/0', 'php_true', '1/0', 'cas_false', '', 'CASError: Division by zero.', ""),
        array('-a/b', 'php_true', '-a/b', 'cas_true', '\frac{-a}{b}', '', ""),
        array('-(a/b)', 'php_true', '-(a/b)', 'cas_true', '-\frac{a}{b}', '', ""),
        array('pi', 'php_true', 'pi', 'cas_true', '\pi', '', ""),
        array('e', 'php_true', 'e', 'cas_true', 'e', '', "Cannot easily make \(e\) a variable name."),
        array('i', 'php_true', 'i', 'cas_true', '\mathrm{i}', '',
            "Options to make i a variable, or a vector unit.  Note this is not italic."),
        array('j', 'php_true', 'j', 'cas_true', 'j', '',
            "Can define \(j^2=-1\) as an option, or a vector unit.  By default a variable, so italic."),
        array('inf', 'php_true', 'inf', 'cas_true', '\infty', '', ""),

        // Different version of Maxima (LISP?) use 1E3 or 1e3.
        array('1E+3', 'php_true', '1E+3', 'cas_true', '1.0E+3', '', "Scientific notation"),
        array('3E2', 'php_true', '3E2', 'cas_true', '3.0E+2', '', ""),
        array('3e2', 'php_true', '3e2', 'cas_true', '3.0E+2', '', ""),
        array('3e-2', 'php_true', '3e-2', 'cas_true', '3.0E-2', '', ""),
        array('3.67x10^2', 'php_true', '3.67*x*10^2', 'cas_true', '3.67\cdot x\cdot 10^2', 'missing_stars', ""),
        array('1+i', 'php_true', '1+i', 'cas_true', '1+\mathrm{i}', '', ""),
        array('3-i', 'php_true', '3-i', 'cas_true', '3-\mathrm{i}', '', ""),
        array('-3+i', 'php_true', '-3+i', 'cas_true', '-3+\mathrm{i}', '', ""),
        array('1+2i', 'php_true', '1+2*i', 'cas_true', '1+2\cdot \mathrm{i}', 'missing_stars', ""),
        array('-(1/512) + i(sqrt(3)/512)', 'php_true', '-(1/512)+i*(sqrt(3)/512)', 'cas_true',
                '-\frac{1}{512}+\mathrm{i}\cdot \left(\frac{\sqrt{3}}{512}\right)', 'missing_stars', ""),
        array('true', 'php_true', 'true', 'cas_true', '\mathbf{True}', '', "Booleans"),
        array('false', 'php_true', 'false', 'cas_true', '\mathbf{False}', '', ""),
        array('"1+1"', 'php_true', '"1+1"', 'cas_true', '\mbox{1+1}', '',
        "Strings - generally discouraged in STACK.  Note, this is a string within a mathematical expression, not literally 1+1."),
        array('"Hello world"', 'php_true', '"Hello world"', 'cas_true', '\mbox{Hello world}', '', ''),
        array('x', 'php_true', 'x', 'cas_true', 'x', '', "Names for variables etc."),

        array('a1', 'php_true', 'a*1', 'cas_true', 'a\cdot 1', 'missing_stars', ""),
        array('a12', 'php_true', 'a*12', 'cas_true', 'a\cdot 12', 'missing_stars', ""),
        array('ab123', 'php_true', 'ab*123', 'cas_true', '{\it ab}\cdot 123', 'missing_stars', ""),
        array('a9b', 'php_true', 'a*9*b', 'cas_true', 'a\cdot 9\cdot b',
                'missing_stars', "Note the subscripting and the implied multiplication."),
        array('ab98cd', 'php_true', 'ab*98*cd', 'cas_true', '{\it ab}\cdot 98\cdot {\it cd}', 'missing_stars', ''),
        array("a'", 'php_false', '', '', '', 'apostrophe', ""),
        array('X', 'php_true', 'X', 'cas_true', 'X', '', ""),
        array('aXy1', 'php_false', 'aXy*1', 'cas_true', '', 'missing_stars | forbiddenVariable', ""),
        // In STACK 4.3, the parser accepts these as functions.
        array('f(x)', 'php_true', 'f(x)', 'cas_true', 'f\left(x\right)', '', "Functions"),
        array('f(x)^2', 'php_true', 'f(x)^2', 'cas_true', 'f^2\left(x\right)', '', ""),
        array('a(x)', 'php_true', 'a(x)', 'cas_true', 'a\left(x\right)', '', ""),
        array('x(t+1)', 'php_true', 'x(t+1)', 'cas_true', 'x\left(t+1\right)', '', ""),
        // Because we are using x as a variable, we do insert a * here!
        array('x(x+1)', 'php_true', 'x*(x+1)', 'cas_true', 'x\cdot \left(x+1\right)', 'missing_stars | Variable_function', ""),
        array('x(sin(t)+1)', 'php_true', 'x(sin(t)+1)', 'cas_true', 'x\left(\sin \left( t \right)+1\right)', '', ""),
        array('b/a(x)', 'php_true', 'b/a(x)', 'cas_true', '\frac{b}{a\left(x\right)}', '', ""),
        array('3b+5/a(x)', 'php_true', '3*b+5/a(x)', 'cas_true', '3\cdot b+\frac{5}{a\left(x\right)}', 'missing_stars', ""),
        array('a/(a(x+1)+2)', 'php_true', 'a/(a*(x+1)+2)', 'cas_true', '\frac{a}{a\cdot \left(x+1\right)+2}',
            'missing_stars | Variable_function', ""),
        array("f''(x)", 'php_false', '' , '', '', 'apostrophe', "Apostrophies again..."),
        array('dosomething(x,y,z)', 'php_false', '', '', '', 'forbiddenFunction',
        "Students have a restricted list of function names.  Teachers are less restricted."),
        array('[]', 'php_true', '[]', 'cas_true', '\left[ \right]', '', "Lists"),
        array('[1]', 'php_true', '[1]', 'cas_true', '\left[ 1 \right]', '', ""),
        array('[1,2,3.4]', 'php_true', '[1,2,3.4]', 'cas_true', '\left[ 1 , 2 , 3.4 \right]', '', ""),
        array('[x, y, z ]', 'php_true', '[x,y,z]', 'cas_true', '\left[ x , y , z \right]', '', ""),
        array('["a"]', 'php_true', '["a"]', 'cas_true', '\left[ \mbox{a} \right]', '', ""),
        array('[1,true,"a"]', 'php_true', '[1,true,"a"]', 'cas_true', '\left[ 1 , \mathbf{True} , \mbox{a} \right]', '', ""),
        array('[[1,2],[3,4]]', 'php_true', '[[1,2],[3,4]]', 'cas_true',
                '\left[ \left[ 1 , 2 \right] , \left[ 3 , 4 \right] \right]', '', ""),
        array('{}', 'php_true', '{}', 'cas_true', '\left \{ \right \}', '', "Sets"),
        array('{1}', 'php_true', '{1}', 'cas_true', '\left \{1 \right \}', '', ""),
        array('{1,2,3.4}', 'php_true', '{1,2,3.4}', 'cas_true', '\left \{1 , 2 , 3.4 \right \}', '', ""),
        array('{x, y, z }', 'php_true', '{x,y,z}', 'cas_true', '\left \{x , y , z \right \}', '', ""),
        array('set(x, y, z)', 'php_false', '', '', '', 'forbiddenFunction', ""),
        array('union(oo(2,3),oo(4,inf))', 'php_true', 'union(oo(2,3),oo(4,inf))', 'cas_true',
            '\left( 2,\, 3\right) \cup \left( 4,\, \infty \right)', '', ""),
        array('union(oc(2,3),co(4,inf),cc(-1,1))', 'php_true', 'union(oc(2,3),co(4,inf),cc(-1,1))', 'cas_true',
            '\left( 2,\, 3\right] \cup \left[ 4,\, \infty \right) \cup \left[ -1,\, 1\right]', '', ""),
        array('union({3,7})', 'php_true', 'union({3,7})', 'cas_true',
            '\left \{3 , 7 \right \}', '', ""),
        array('intersection(oo(2,3),oo(4,inf))', 'php_true', 'intersection(oo(2,3),oo(4,inf))', 'cas_true',
            '\left( 2,\, 3\right) \cap \left( 4,\, \infty \right)', '', ""),
        array('matrix([a,b],[c,d])', 'php_true', 'matrix([a,b],[c,d])', 'cas_true',
            '\left[\begin{array}{cc} a & b \\\\ c & d \end{array}\right]', '', 'Matrices'),
        array('stackvector(a)', 'php_true', 'stackvector(a)', 'cas_true', '{\bf a}', '', 'Vectors'),
        array('a[2]', 'php_true', 'a[2]', 'cas_true', 'a_{2}', '', "Maxima arrays"),
        array('a[n+1]', 'php_true', 'a[n+1]', 'cas_true', 'a_{n+1}', '', ""),
        array('a[1,2]', 'php_true', 'a[1,2]', 'cas_true', 'a_{1,2}', '', ""),
        array('(a,b,c)', 'php_true', '(a,b,c)', 'cas_true', 'c', '',
        "In Maxima this syntax is a programme block which returns its last element."),
        array('0..1', 'php_false', '', '', '', 'spuriousop',
        "Ranges and logical operations are currently not supported by Maxima or STACK
        - this is on our wish list. It will result in the ability to deal with systems of inequalities, e.g. \(x<1\ and\ x>-4\)."),
        array('0.1..1.2', 'php_false', '', '', '', 'spuriousop', ""),
        array('not x', 'php_true', 'not x', 'cas_true', '{\rm not}\left( x \right)', '', ""),
        array('x and y', 'php_true', 'x and y', 'cas_true', 'x\,{\mbox{ and }}\, y', '', ""),
        array('true and false', 'php_true', 'true and false', 'cas_true', '\mathbf{True}\,{\mbox{ and }}\, \mathbf{False}', '', ""),
        array('x or y', 'php_true', 'x or y', 'cas_true', 'x\,{\mbox{ or }}\, y', '', ""),
        array('x xor y', 'php_true', 'x xor y', 'cas_true', 'x\,{\mbox{ xor }}\, y', '', ""),
        array('x nor y', 'php_true', 'x nor y', 'cas_true', 'x\,{\mbox{ nor }}\, y', '', ""),
        array('x nand y', 'php_true', 'x nand y', 'cas_true', 'x\,{\mbox{ nand }}\, y', '', ""),
        array('x implies y', 'php_true', 'x implies y', 'cas_true', 'x\,{\mbox{ implies }}\, y', '', ""),
        array('not false', 'php_true', 'not false', 'cas_true', '{\rm not}\left( \mathbf{False} \right)', '', ""),
        array('not(false)', 'php_true', 'not(false)', 'cas_true', '{\rm not}\left( \mathbf{False} \right)', '', ""),
        array('x isa "number"', 'php_false', '', '', '', 'spaces', ""),
        array('x && y', 'php_false', '', '', '', 'spuriousop', ""),
        array('x || y', 'php_false', '', '', '', 'spuriousop', ""),
        array('x * y', 'php_true', 'x*y', 'cas_true', 'x\cdot y', '',
            "Operations: there are options on how this is displayed, either as \(x\cdot y\), \(x\\times y\), or as \(x\, y\)."),
        array('x + y', 'php_true', 'x+y', 'cas_true', 'x+y', '', ""),
        array('x - y', 'php_true', 'x-y', 'cas_true', 'x-y', '', ""),
        array('x / y', 'php_true', 'x/y', 'cas_true', '\frac{x}{y}', '', ""),
        array('x ^ y', 'php_true', 'x^y', 'cas_true', 'x^{y}', '', ""),
        array('x < y', 'php_true', 'x < y', 'cas_true', 'x < y', '', ""),
        array('x > y', 'php_true', 'x > y', 'cas_true', 'x > y', '', ""),
        array('x = y', 'php_true', 'x = y', 'cas_true', 'x=y', '', ""),
        array('x!', 'php_true', 'x!', 'cas_true', 'x!', '', ""),
        array('!x', 'php_false', '!x', 'cas_false', '', 'badpostfixop', ""),
        array('x_1', 'php_true', 'x_1', 'cas_true', '{x}_{1}', '', ""),
        array('ab_12', 'php_true', 'ab_12', 'cas_true', '{{\it ab}}_{12}', '', ""),
        array('x_y', 'php_true', 'x_y', 'cas_true', '{x}_{y}', '', ""),
        array('x <= y', 'php_true', 'x <= y', 'cas_true', 'x\leq y', '',
        "Inequalities in various forms."),
        array('x >= y', 'php_true', 'x >= y', 'cas_true', 'x\geq y', '', ""),
        array('x => y', 'php_false', 'x=>y', '', '', 'backward_inequalities', ""),
        array('x => and x<1', 'php_false', 'x => and x<1', '', '', 'backward_inequalities', ""),
        array('x<1 and x>1', 'php_true', 'x < 1 and x > 1', 'cas_true', 'x < 1\,{\mbox{ and }}\, x > 1', '', ""),
        array('x>1 or (x<1 and t<sin(x))', 'php_true', 'x > 1 or (x < 1 and t < sin(x))', 'cas_true',
                'x > 1\,{\mbox{ or }}\, x < 1\,{\mbox{ and }}\, t < \sin \left( x \right)', '', ""),
        array('x>1 and (x<1 or t<sin(x))', 'php_true', 'x > 1 and (x < 1 or t < sin(x))', 'cas_true',
                'x > 1\,{\mbox{ and }}\, \left(x < 1\,{\mbox{ or }}\, t < \sin \left( x \right)\right)', '', ""),
        array('1<x<3', 'php_false', '', '', '', 'chained_inequalities', ""),
        array('1<=x<y^2', 'php_false', '', '', '', 'chained_inequalities', ""),
        array('1=<x<3', 'php_false', '', '', '', 'backward_inequalities', ""),
        array('x=1 or 2', 'php_true', 'x = 1 or 2', 'cas_true', 'x=1\,{\mbox{ or }}\, 2', 'Bad_assignment', ""),
        array('x=1 or x=2', 'php_true', 'x = 1 or x = 2', 'cas_true', 'x=1\,{\mbox{ or }}\, x=2', '', ""),
        array('x=1 or 2 or 3', 'php_true', 'x = 1 or 2 or 3', 'cas_true',
                'x=1\,{\mbox{ or }}\, 2\,{\mbox{ or }}\, 3', 'Bad_assignment', ""),
        array('[1<x,x<3]', 'php_true', '[1 < x,x < 3]', 'cas_true', '\left[ 1 < x , x < 3 \right]', '', ""),
        array('[1 < x,y < 1 or y > 7]', 'php_true', '[1 < x,y < 1 or y > 7]', 'cas_true',
            '\left[ 1 < x , y < 1\,{\mbox{ or }}\, y > 7 \right]', '', ""),
        array('[1<x,1<y<3]', 'php_false', '', '', '', 'chained_inequalities', ""),
        array('x <> y', 'php_false', '', '', '', 'spuriousop', "This isn't permitted in Maxima"),
        array('x+', 'php_false', 'x+', '', '', 'finalChar', "Not enough arguments for op error"),
        array('y*', 'php_false', 'y*', '', '', 'finalChar', ""),
        array('x^', 'php_flase', 'x^', '', '', 'finalChar', ""),
        array('x.', 'php_flase', 'x.', '', '', 'finalChar', ""),
        array('x and', 'php_false', '', '', '', 'spaces', ""),
        array('!', 'php_false', '!', 'badpostfixop', '', 'badpostfixop', ""),
        array('sin', 'php_false', 'sin', 'cas_true', '', 'forbiddenVariable',
        "This names the operator sine, which is a valid expression on its own.
        The classic difference between the function \(f\) and the value of the
        function at a point \(f(x)\).  Maybe a 'gocha' for the question author...."),
        array('(x+y)^z', 'php_true', '(x+y)^z', 'cas_true', '{\left(x+y\right)}^{z}', '',
        "Check display: brackets only go round operands when strictly necessary, but student validation respects the input."),
        array('x+(y^z)', 'php_true', 'x+(y^z)', 'cas_true', 'x+y^{z}', '', ""),
        array('x-(y+z)', 'php_true', 'x-(y+z)', 'cas_true', 'x-\left(y+z\right)', '', ""),
        array('(x-y)+z', 'php_true', '(x-y)+z', 'cas_true', 'x-y+z', '', ""),
        array('x^(-(y+z))', 'php_true', 'x^(-(y+z))', 'cas_true', 'x^ {- \left(y+z\right) }', '', ""),
        array('x^(-y)', 'php_true', 'x^(-y)', 'cas_true', 'x^ {- y }', '', ""),
        array('x^-y', 'php_true', 'x^-y', 'cas_true', 'x^ {- y }', '', ""),
        array('x^(y+z)', 'php_true', 'x^(y+z)', 'cas_true', 'x^{y+z}', '', ""),
        array('(1+i)*x', 'php_true', '(1+i)*x', 'cas_true', '\left(1+\mathrm{i}\right)\cdot x', '', ""),
        array('(1+i)+x', 'php_true', '(1+i)+x', 'cas_true', '1+\mathrm{i}+x', '', ""),
        array('y^3-2*y^2-8*y', 'php_true', 'y^3-2*y^2-8*y', 'cas_true', 'y^3-2\cdot y^2-8\cdot y', '', ""),
        array('y^2-2*y-8', 'php_true', 'y^2-2*y-8', 'cas_true', 'y^2-2\cdot y-8', '', ""),
        array('y^2-2*y-0.5', 'php_true', 'y^2-2*y-0.5', 'cas_true', 'y^2-2\cdot y-0.5', '', ""),
        array('(x)', 'php_true', '(x)', 'cas_true', 'x', '', "Brackets"),
        array('((x))', 'php_true', '((x))', 'cas_true', 'x', '', ""),
        array('(()x)', 'php_false', '(()*x)', 'cas_false', '', 'missing_stars | emptyParens', ""),
        array('()x', 'php_false', '()*x', 'cas_false', '', 'missing_stars | emptyParens', ""),
        array('x()', 'php_false', 'x*()', 'cas_false', '', 'emptyParens', ""),
        array('([x)]', 'php_false', '([x)]', '', '', 'ParseError', ""),
        array('(', 'php_false', '', '', '', 'missingRightBracket', "Brackets"),
        array(')', 'php_false', '', '', '', 'missingLeftBracket', ""),
        array('[', 'php_false', '', '', '', 'missingRightBracket', ""),
        array(']', 'php_false', '', '', '', 'missingLeftBracket', ""),
        array('{', 'php_false', '', '', '', 'missingRightBracket', ""),
        array('}', 'php_false', '', '', '', 'missingLeftBracket', ""),
        array('x)', 'php_false', '', '', '', 'missingLeftBracket', ""),
        array('(x', 'php_false', '', '', '', 'missingRightBracket', ""),
        array('(x+(y)', 'php_false', '', '', '', 'missingRightBracket', ""),
        array('x-1)^2', 'php_false', '', '', '', 'missingLeftBracket', ""),
        array('x+(y))', 'php_false', '', '', '', 'missingLeftBracket', ""),
        array('f(x))', 'php_false', '', '', '', 'missingLeftBracket', ""),
        array('[x', 'php_false', '', '', '', 'missingRightBracket', ""),
        array('x]', 'php_false', '', '', '', 'missingLeftBracket', ""),
        array('{x', 'php_false', '', '', '', 'missingRightBracket', ""),
        array('alpha', 'php_true', 'alpha', 'cas_true', '\alpha', '',
        "Greek letters - quite a few have meanings in Maxima already."),
        array('alpha(x)', 'php_true', 'alpha(x)', 'cas_true', '\alpha\left(x\right)', '', ""),
        array('beta', 'php_true', 'beta', 'cas_true', '\beta', '',
        "The beta function is defined as \(\gamma(a) \gamma(b)/\gamma(a+b)\)."),
        array('gamma', 'php_true', 'gamma', 'cas_true', '\gamma', '', "This is the gamma function."),
        array('delta', 'php_true', 'delta', 'cas_true', '\delta', '', "This is the Dirac Delta function."),
        array('epsilon', 'php_true', 'epsilon', 'cas_true', '\varepsilon', '', ""),
        array('zeta', 'php_true', 'zeta', 'cas_true', '\zeta', '', "This is the Riemann zeta function."),
        array('eta', 'php_true', 'eta', 'cas_true', '\eta', '', ""),
        array('theta', 'php_true', 'theta', 'cas_true', '\theta', '', ""),
        array('theta(s)', 'php_true', 'theta(s)', 'cas_true', '\theta\left(s\right)', '', ""),
        array('iota', 'php_true', 'iota', 'cas_true', '\iota', '', ""),
        array('kappa', 'php_true', 'kappa', 'cas_true', '\kappa', '', ""),
        array('lambda', 'php_true', 'lambda', 'cas_true', '\lambda', '', "Defines and returns a lambda expression."),
        array('mu', 'php_true', 'mu', 'cas_true', '\mu', '', ""),
        array('nu', 'php_true', 'nu', 'cas_true', '\nu', '', ""),
        array('xi', 'php_true', 'xi', 'cas_true', '\xi', '', ""),
        array('omicron', 'php_true', 'omicron', 'cas_true', 'o', '', ""),
        array('pi', 'php_true', 'pi', 'cas_true', '\pi', '', "This is a numeric constant."),
        array('rho', 'php_true', 'rho', 'cas_true', '\rho', '', ""),
        array('sigma', 'php_true', 'sigma', 'cas_true', '\sigma', '', ""),
        array('tau', 'php_true', 'tau', 'cas_true', '\tau', '', ""),
        array('upsilon', 'php_true', 'upsilon', 'cas_true', '\upsilon', '', ""),
        array('phi', 'php_true', 'phi', 'cas_true', '\varphi', '',
                "Constant, represents the so-called golden mean, \((1 + \sqrt{5})/2\)."),
        array('phi(n)+Phi(n)', 'php_true', 'phi(n)+Phi(n)', 'cas_true', '\varphi\left(n\right)+\Phi\left(n\right)', '', ""),
        array('chi', 'php_true', 'chi', 'cas_true', '\chi', '', ""),
        array('psi', 'php_true', 'psi', 'cas_true', '\psi', '', "The derivative of \(\log (\gamma (x))\) of order \(n+1\)."),
        array('omega', 'php_true', 'omega', 'cas_true', '\omega', '', ""),
        array('p=?*s', 'php_true', 'p = ?*s', 'cas_true', 'p=\color{red}{?}\cdot s', '', "Question marks"),
        array('(x+2)3', 'php_true', '(x+2)*3', 'cas_true', '\left(x+2\right)\cdot 3', 'missing_stars', "Implicit multiplication"),
        array('(x+2)y', 'php_true', '(x+2)*y', 'cas_true', '\left(x+2\right)\cdot y', 'missing_stars', ""),
        array('3(x+1)', 'php_true', '3*(x+1)', 'cas_true', '3\cdot \left(x+1\right)', 'missing_stars', ""),
        array('-3(x+1)', 'php_true', '-3*(x+1)', 'cas_true', '-3\cdot \left(x+1\right)', 'missing_stars', ""),
        array('2+3(x+1)', 'php_true', '2+3*(x+1)', 'cas_true', '2+3\cdot \left(x+1\right)', 'missing_stars', ""),
        array('x(2+1)', 'php_true', 'x(2+1)', 'cas_true', 'x\left(2+1\right)', '', ""),
        array('7x(2+1)', 'php_true', '7*x(2+1)', 'cas_true', '7\cdot x\left(2+1\right)', 'missing_stars', ""),
        array('(x+2)(x+3)', 'php_true', '(x+2)*(x+3)', 'cas_true', '\left(x+2\right)\cdot \left(x+3\right)', 'missing_stars', ""),
        array('cos(2x)(x+1)', 'php_true', 'cos(2*x)*(x+1)', 'cas_true', '\cos \left( 2\cdot x \right)\cdot \left(x+1\right)',
                'missing_stars', ""),
        array('b(b+1)', 'php_true', 'b*(b+1)', 'cas_true', 'b\cdot \left(b+1\right)', 'missing_stars | Variable_function', ""),
        array('-b(5-b)', 'php_true', '-b*(5-b)', 'cas_true', '\left(-b\right)\cdot \left(5-b\right)',
            'missing_stars | Variable_function', ""),
        array('-x(1+x)', 'php_true', '-x*(1+x)', 'cas_true', '\left(-x\right)\cdot \left(1+x\right)',
            'missing_stars | Variable_function', ""),
        array('1-x(1+x)', 'php_true', '1-x*(1+x)', 'cas_true', '1-x\cdot \left(1+x\right)',
            'missing_stars | Variable_function', ""),
        array('-3x(1+x)', 'php_true', '-3*x*(1+x)', 'cas_true', '-3\cdot x\cdot \left(1+x\right)',
            'missing_stars | Variable_function', ""),
        array('i(1+i)', 'php_true', 'i*(1+i)', 'cas_true', '\mathrm{i}\cdot \left(1+\mathrm{i}\right)', 'missing_stars', ""),
        array('i(4)', 'php_true', 'i*(4)', 'cas_true', '\mathrm{i}\cdot 4', 'missing_stars', ""),
        // The next case is important: please don't call the result of a function f(x), with an argument (2).
        array('f(x)(2)', 'php_true', 'f(x)*(2)', 'cas_true', 'f\left(x\right)\cdot 2', 'missing_stars', ""),
        array('xsin(1)', 'php_true', 'x*sin(1)', 'cas_true', 'x\cdot \sin \left( 1 \right)', 'missing_stars',
        "single-letter variable name followed by known function is an implicit multiplication"),
        array('ycos(2)', 'php_true', 'y*cos(2)', 'cas_true', 'y\cdot \cos \left( 2 \right)', 'missing_stars', ""),
        array('Bgcd(3,2)', 'php_true', 'B*gcd(3,2)', 'cas_true', 'B\cdot 1', 'missing_stars', ""),
        array('+1', 'php_true', '+1', 'cas_true', '1', '', "Unary plus"),
        array('+0.2', 'php_true', '+0.2', 'cas_true', '0.2', '', ""),
        array('+e', 'php_true', '+e', 'cas_true', 'e', '', ""),
        array('+pi', 'php_true', '+pi', 'cas_true', '\pi', '', ""),
        array('+i', 'php_true', '+i', 'cas_true', '\mathrm{i}', '', ""),
        array('+x', 'php_true', '+x', 'cas_true', 'x', '', ""),
        array('sqrt(+x)', 'php_true', 'sqrt(+x)', 'cas_true', '\sqrt{x}', '', ""),
        array('1/sin(+x)', 'php_true', '1/sin(+x)', 'cas_true', '\frac{1}{\sin \left( x \right)}', '', ""),
        array('"+"(a,b)', 'php_true', '"+"(a,b)', 'cas_true', 'a+b', '', "This is Maxima specific syntax."),
        array('(+1)', 'php_true', '(+1)', 'cas_true', '1', '', ""),
        array('[1,+2]', 'php_true', '[1,+2]', 'cas_true', '\left[ 1 , 2 \right]', '', ""),
        array('[+1,+2]', 'php_true', '[+1,+2]', 'cas_true', '\left[ 1 , 2 \right]', '', ""),
        array('-1', 'php_true', '-1', 'cas_true', '-1', '', "Unary minus"),
        array('-0.2', 'php_true', '-0.2', 'cas_true', '-0.2', '', ""),
        array('-e', 'php_true', '-e', 'cas_true', '-e', '', ""),
        array('-pi', 'php_true', '-pi', 'cas_true', '-\pi', '', ""),
        array('-i', 'php_true', '-i', 'cas_true', '-\mathrm{i}', '', ""),
        array('-x', 'php_true', '-x', 'cas_true', '-x', '', ""),
        array('-x[3]', 'php_true', '-x[3]', 'cas_true', '-x_{3}', '', ""),
        array('(-1)', 'php_true', '(-1)', 'cas_true', '-1', '', ""),
        array('[-1,-2]', 'php_true', '[-1,-2]', 'cas_true', '\left[ -1 , -2 \right]', '', ""),
        array('[1,-2]', 'php_true', '[1,-2]', 'cas_true', '\left[ 1 , -2 \right]', '', ""),
        array('y^3-2*y^2-8*y', 'php_true', 'y^3-2*y^2-8*y', 'cas_true', 'y^3-2\cdot y^2-8\cdot y', '', ""),
        array('x^7/7-2*x^6/3-4*x^3/3', 'php_true', 'x^7/7-2*x^6/3-4*x^3/3', 'cas_true',
                '\frac{x^7}{7}-\frac{2\cdot x^6}{3}-\frac{4\cdot x^3}{3}', '', ""),
        array('+-1', 'php_true', '+-1', 'cas_true', '\pm 1', '', "Plus and minus"),
        array('x=+-sqrt(2)', 'php_true', 'x = +-sqrt(2)', 'cas_true', 'x= \pm \sqrt{2}', '', ""),
        array('(-b+-sqrt(b^2))/(2*a)', 'php_true', '(-b+-sqrt(b^2))/(2*a)', 'cas_true',
                '\frac{{-b \pm \sqrt{b^2}}}{2\cdot a}', '', ""),
        array('a+-b', 'php_true', 'a+-b', 'cas_true', '{a \pm b}', '', ""),
        array('a-+b', 'php_true', 'a-+b', 'cas_true', 'a-b', '', ""),
        array('x & y', 'php_false', 'x & y', '', '', 'spuriousop', "Synonyms"),
        array('x && y', 'php_false', 'x && y', '', '', 'spuriousop', ""),
        array('x and y', 'php_true', 'x and y', 'cas_true', 'x\,{\mbox{ and }}\, y', '', ""),
        array('x divides y', 'php_false', '', '', '', 'spaces', ""),
        array('x*divides*y', 'php_false', '', '', '', 'forbiddenVariable', ""),
        array('x | y', 'php_false', 'x|y', '', '', 'spuriousop', ""),
        array('x or y', 'php_true', 'x or y', 'cas_true', 'x\,{\mbox{ or }}\, y', '', ""),
        array('x || y', 'php_false', 'x || y', 'cas_true', '', 'spuriousop', ""),
        array('sqr(x)', 'php_false', '', '', '', 'forbiddenFunction', ""),
        array('sqrt(x)', 'php_true', 'sqrt(x)', 'cas_true', '\sqrt{x}', '',
                "There is an option to display this as \(x^{1/2}|\)."),
        array('root(x)', 'php_true', 'root(x)', 'cas_true', '\sqrt{x}', '', ''),
        array('root(x,3)', 'php_true', 'root(x,3)', 'cas_true', 'x^{\frac{1}{3}}', '', ''),
        array('root(2,-3)', 'php_true', 'root(2,-3)', 'cas_true', '2^{\frac{1}{-3}}', '', ''),
        array('conjugate(x)', 'php_true', 'conjugate(x)', 'cas_true', 'x^\star', '', ''),
        array('conjugate(x)^2', 'php_true', 'conjugate(x)^2', 'cas_true', '{x^\star}^2', '', ''),
        // Parser rules in 4.3, identify cases where known functions (cf) are prefixed with single letter variables.
        array('bsin(t)', 'php_true', 'b*sin(t)', 'cas_true', 'b\cdot \sin \left( t \right)', 'missing_stars', ""),
        // So we have added gcf as a function so it is not g*cf...
        array('gcf(x,y)', 'php_true', 'gcf(x,y)', 'cas_true', '{\it gcf}\left(x , y\right)', '', ""),
        array('gcd(x,y)', 'php_true', 'gcd(x,y)', 'cas_true', '1', '',
                "Don't understand why this is evaluated by Maxima..."),
        array('sgn(x)', 'php_false', '', '', '', 'forbiddenFunction', ""),
        array('sign(x)', 'php_true', 'sign(x)', 'cas_true', '{\it pnz}', '', ""),
        array('len(x)', 'php_false', '', '', '', 'forbiddenFunction', ""),
        array('abs(x)', 'php_true', 'abs(x)', 'cas_true', '\left| x\right|', '', ""),
        array('|x|', 'php_true', 'abs(x)', 'cas_true', '\left| x\right|', '', ""),
        array('length(x)', 'php_true', 'length(x)', 'cas_false', '',
                'CASError: length: argument cannot be a symbol; found x', ""),
        array('x^y^z', 'php_true', 'x^y^z', 'cas_true', 'x^{y^{z}}', '', "Associativity"),
        array('a/b/c', 'php_true', 'a/b/c', 'cas_true', '\frac{\frac{a}{b}}{c}', '', ""),
        array('a-(b-c)', 'php_true', 'a-(b-c)', 'cas_true', 'a-\left(b-c\right)', '', ""),
        array('(a-b)-c', 'php_true', '(a-b)-c', 'cas_true', 'a-b-c', '', ""),
        array('x*y*z', 'php_true', 'x*y*z', 'cas_true', 'x\cdot y\cdot z', '', "Commutativity"),
        array('(x*y)*z', 'php_true', '(x*y)*z', 'cas_true', 'x\cdot y\cdot z', '', ""),
        array('x*(y*z)', 'php_true', 'x*(y*z)', 'cas_true', 'x\cdot \left(y\cdot z\right)', '', ""),
        array('x+y+z', 'php_true', 'x+y+z', 'cas_true', 'x+y+z', '', ""),
        array('(x+y)+z', 'php_true', '(x+y)+z', 'cas_true', 'x+y+z', '', ""),
        array('x+(y+z)', 'php_true', 'x+(y+z)', 'cas_true', 'x+\left(y+z\right)', '', ""),
        array('x/y/z', 'php_true', 'x/y/z', 'cas_true', '\frac{\frac{x}{y}}{z}', '', ""),
        array('(x/y)/z', 'php_true', '(x/y)/z', 'cas_true', '\frac{\frac{x}{y}}{z}', '', ""),
        array('x/(y/z)', 'php_true', 'x/(y/z)', 'cas_true', '\frac{x}{\frac{y}{z}}', '', ""),
        array('x^y', 'php_true', 'x^y', 'cas_true', 'x^{y}', '', "Operations and functions with special TeX"),
        array('x^(y+z)', 'php_true', 'x^(y+z)', 'cas_true', 'x^{y+z}', '', ""),
        array('x^(y/z)', 'php_true', 'x^(y/z)', 'cas_true', 'x^{\frac{y}{z}}', '', ""),
        array('x^f(x)', 'php_true', 'x^f(x)', 'cas_true', 'x^{f\left(x\right)}', '', ""),
        array('x*y^z', 'php_true', 'x*y^z', 'cas_true', 'x\cdot y^{z}', '', ""),
        array('y^z * x', 'php_true', 'y^z*x', 'cas_true', 'y^{z}\cdot x', '', ""),
        array('x*2^y', 'php_true', 'x*2^y', 'cas_true', 'x\cdot 2^{y}', '', ""),
        array('2^y*x', 'php_true', '2^y*x', 'cas_true', '2^{y}\cdot x', '', ""),
        array('2*pi', 'php_true', '2*pi', 'cas_true', '2\cdot \pi', '', ""),
        array('2*e', 'php_true', '2*e', 'cas_true', '2\cdot e', '', ""),
        array('e*2', 'php_true', 'e*2', 'cas_true', 'e\cdot 2', '', ""),
        array('pi*2', 'php_true', 'pi*2', 'cas_true', '\pi\cdot 2', '', ""),
        array('i*2', 'php_true', 'i*2', 'cas_true', '\mathrm{i}\cdot 2', '', ""),
        array('2*i', 'php_true', '2*i', 'cas_true', '2\cdot \mathrm{i}', '', ""),
        array('2*i^3', 'php_true', '2*i^3', 'cas_true', '2\cdot \mathrm{i}^3', '', ""),
        array('x*i^3', 'php_true', 'x*i^3', 'cas_true', 'x\cdot \mathrm{i}^3', '', ""),
        array('x*(-y)', 'php_true', 'x*(-y)', 'cas_true', 'x\cdot \left(-y\right)', '', ""),
        array('(-x)*y', 'php_true', '(-x)*y', 'cas_true', '\left(-x\right)\cdot y', '', ""),
        array('abs(13)', 'php_true', 'abs(13)', 'cas_true', '\left| 13\right|', '', ""),
        array('fact(13)', 'php_false', '', '', '', 'forbiddenFunction', ""),
        array('ceiling(x)', 'php_true', 'ceiling(x)', 'cas_true', '\left \lceil x \right \rceil', '', ""),
        array('floor(x)', 'php_true', 'floor(x)', 'cas_true', '\left \lfloor x \right \rfloor', '', ""),
        array('int(x,y)', 'php_true', 'int(x,y)', 'cas_true', '\int {x}{\;\mathrm{d}y}', '', ""),
        array('int(sin(x))', 'php_true', 'int(sin(x))', 'cas_false', '',
            'CASError: int must have at least two arguments.', ""),
        array('diff(x,y)', 'php_true', 'diff(x,y)', 'cas_true', '\frac{\mathrm{d} x}{\mathrm{d} y}', '', ""),
        array('diff(sin(x),x)', 'php_true', 'diff(sin(x),x)', 'cas_true',
            '\frac{\mathrm{d}}{\mathrm{d} x} \sin \left( x \right)', '', ""),
        array('diff(sin(x))', 'php_true', 'diff(sin(x))', 'cas_false', '',
            'CASError: diff must have at least two arguments.', ""),
        array('y\'+y', 'php_false', '', 'cas_true', '', 'apostrophe', ""),
        array('y\'(x)+y(x)=0', 'php_false', '', 'cas_true', '', 'apostrophe', ""),
        array("'int(x,y)", 'php_false', '', 'cas_true', '', 'apostrophe',
            "Note the use of the apostrophe here to make an inert function."),
        array("'diff(x,y)", 'php_false', '', 'cas_true', '', 'apostrophe', "Not ideal...arises because we don't 'simplify'."),
        array('partialdiff(x,y,1)', 'php_false', '', '', '', 'missing_stars | forbiddenVariable', ""),
        array('limit(y,x,3)', 'php_true', 'limit(y,x,3)', 'cas_true', '\lim_{x\rightarrow 3}{y}', '', ""),
        array('mod(x,y)', 'php_true', 'mod(x,y)', 'cas_true', 'x \rm{mod} y', '', ""),
        array('perm(x,y)', 'php_false', '', '', '', 'forbiddenFunction', ""),
        array('comb(x,y)', 'php_false', '', '', '', 'forbiddenFunction', ""),
        array('switch(x,a,y,b,c)', 'php_false', '', '', '', 'forbiddenFunction', ""),
        array('sin(x)', 'php_true', 'sin(x)', 'cas_true', '\sin \left( x \right)', '', "Trig functions"),
        array('cos(x)', 'php_true', 'cos(x)', 'cas_true', '\cos \left( x \right)', '', ""),
        array('cos(x)^2', 'php_true', 'cos(x)^2', 'cas_true', '\cos ^2x', '', ""),
        array('cos(x+1)^2', 'php_true', 'cos(x+1)^2', 'cas_true', '\cos ^2\left(x+1\right)', '', ""),
        array('tan(x)', 'php_true', 'tan(x)', 'cas_true', '\tan \left( x \right)', '', ""),
        array('sec(x)', 'php_true', 'sec(x)', 'cas_true', '\sec \left( x \right)', '', ""),
        array('cot(x)', 'php_true', 'cot(x)', 'cas_true', '\cot \left( x \right)', '', ""),
        array('csc(x)', 'php_true', 'csc(x)', 'cas_true', '\csc \left( x \right)', '', ""),
        // This is now a Maxima alias.
        array('cosec(x)', 'php_true', 'cosec(x)', 'cas_true', '\csc \left( x \right)', '', ""),
        array('csc(6*x)^2*(7*sin(6*x)*cos(7*x)-6*cos(6*x)*sin(7*x))', 'php_true',
                'csc(6*x)^2*(7*sin(6*x)*cos(7*x)-6*cos(6*x)*sin(7*x))', 'cas_true',
                '\csc ^2\left(6\cdot x\right)\cdot \left(7\cdot \sin ' .
                '\left( 6\cdot x \right)\cdot \cos \left( 7\cdot x \right)-6\cdot ' .
                '\cos \left( 6\cdot x \right)\cdot \sin \left( 7\cdot x \right)\right)', '', ""),
        array('Sin(x)', 'php_false', '', '', '', 'unknownFunctionCase', ""),
        array('sim(x)', 'php_false', '', '', '', 'forbiddenFunction', ""),
        array('asin(x)', 'php_true', 'asin(x)', 'cas_true', '\sin^{-1}\left( x \right)', '',
            "Maxima uses the asin pattern"),
        array('arcsin(x)', 'php_false', 'arcsin(x)', 'cas_true', '', 'triginv', "Not the arcsin"),
        array('sin^-1(x)', 'php_false', 'sin^-1(x)', 'cas_false', '', 'missing_stars | trigexp', ""),
        array('cos^2(x)', 'php_false', 'cos^2(x)', 'cas_false', '', 'missing_stars | trigexp', ""),
        array('sin*2*x', 'php_false', 'sin*2*x', 'cas_false', '', 'forbiddenVariable', ""),
        array('sin[2*x]', 'php_false', 'sin[2*x]', 'cas_false', '', 'trigparens', ""),
        array('cosh(x)', 'php_true', 'cosh(x)', 'cas_true', '\cosh \left( x \right)', '', ""),
        array('sinh(x)', 'php_true', 'sinh(x)', 'cas_true', '\sinh \left( x \right)', '', ""),
        array('tanh(x)', 'php_true', 'tanh(x)', 'cas_true', '\tanh \left( x \right)', '', ""),
        array('coth(x)', 'php_true', 'coth(x)', 'cas_true', '\coth \left( x \right)', '', ""),
        array('cosech(x)', 'php_true', 'cosech(x)', 'cas_true', '{\rm csch}\left( x \right)', '', ""),
        array('sech(x)', 'php_true', 'sech(x)', 'cas_true', '{\rm sech}\left( x \right)', '', ""),
        array('asinh(x)', 'php_true', 'asinh(x)', 'cas_true', '{\rm sinh}^{-1}\left( x \right)', '', "Etc..."),
        array('a^b', 'php_true', 'a^b', 'cas_true', 'a^{b}', '', "Exponentials and logarithms"),
        array('a ** b', 'php_true', 'a**b', 'cas_true', 'a^{b}', '', ""),
        array('x^-1', 'php_true', 'x^-1', 'cas_true', 'x^ {- 1 }', '', ""),
        array('a^-b', 'php_true', 'a^-b', 'cas_true', 'a^ {- b }', '', ""),
        array('e^x', 'php_true', 'e^x', 'cas_true', 'e^{x}', '', ""),
        array('%e^x', 'php_true', '%e^x', 'cas_true', 'e^{x}', '', ""),
        array('exp(x)', 'php_true', 'exp(x)', 'cas_true', '\exp \left( x \right)', '', ""),
        array('log(x)', 'php_true', 'log(x)', 'cas_true', '\ln \left( x \right)', '', "Natural logarithm."),
        array('ln(x)', 'php_true', 'ln(x)', 'cas_true', '\ln \left( x \right)', '', "Natural logarithm, STACK alias."),
        array('ln*x', 'php_true', 'ln*x', 'cas_true', '\ln \cdot x', '', ""),
        array('In(x)', 'php_false', '', '', '', 'stackCas_badLogIn', ""),
        array('log10(x)', 'php_true', 'lg(x,10)', 'cas_true', '\log_{10}\left(x\right)', 'logsubs', ""),
        array('log_10(x)', 'php_true', 'lg(x,10)', 'cas_true', '\log_{10}\left(x\right)', 'logsubs', ""),
        array('log_2(a)', 'php_true', 'lg(a,2)', 'cas_true', '\log_{2}\left(a\right)', 'logsubs', ""),
        array('log_x(1/(x+b))', 'php_true', 'lg(1/(x+b),x)', 'cas_true', '\log_{x}\left(\frac{1}{x+b}\right)', 'logsubs', ""),
        array('log_2(a)^3', 'php_true', 'lg(a,2)^3', 'cas_true', '{\log_{2}\left(a\right)}^3', 'logsubs', ""),
        array('2+log_x(1/(x+b))*x^2', 'php_true', '2+lg(1/(x+b),x)*x^2', 'cas_true',
            '2+\log_{x}\left(\frac{1}{x+b}\right)\cdot x^2', 'logsubs', ""),
        array('log_a(b)*log_b(c)', 'php_true', 'lg(b,a)*lg(c,b)', 'cas_true',
            '\log_{a}\left(b\right)\cdot \log_{b}\left(c\right)', 'logsubs', ""),
        array('lg(x)', 'php_true', 'lg(x)', 'cas_true', '\log_{10}\left(x\right)', '', "Logarithm to the base \(10\)."),
        array('lg(10^3)', 'php_true', 'lg(10^3)', 'cas_true', '\log_{10}\left(10^3\right)', '', ""),
        array('lg(x,a)', 'php_true', 'lg(x,a)', 'cas_true', '\log_{a}\left(x\right)', '', ""),
        array('lg(x,2)+lg(x,2)^3', 'php_true', 'lg(x,2)+lg(x,2)^3', 'cas_true',
            '\log_{2}\left(x\right)+{\log_{2}\left(x\right)}^3', '', ""),
        array('log(2x)/x+1/2', 'php_true', 'log(2*x)/x+1/2', 'cas_true',
                '\frac{\ln \left( 2\cdot x \right)}{x}+\frac{1}{2}', 'missing_stars', ""),
        array('a++b', 'php_true', 'a++b', 'cas_true', 'a+b', '',
                "The extra plusses or minuses are interpreted as unary operators on b"),
        array('a +++ b', 'php_true', 'a+++b', 'cas_true', 'a+b', '', ""),
        array('a --- b', 'php_true', 'a---b', 'cas_true', 'a-\left(-\left(-b\right)\right)', '', ""),
        array('rho*z*V/(4*pi*epsilon[0]*(R^2+z^2)^(3/2))', 'php_true', 'rho*z*V/(4*pi*epsilon[0]*(R^2+z^2)^(3/2))', 'cas_true',
                '\frac{\rho\cdot z\cdot V}{4\cdot \pi\cdot \varepsilon_{0}\cdot {\left(R^2+z^2\right)}^{\frac{3}{2}}}',
                '', "Subscripts"),
        array('a_b', 'php_true', 'a_b', 'cas_true', '{a}_{b}', '', ""),
        array('beta_47', 'php_true', 'beta_47', 'cas_true', '{\beta}_{47}', '', ""),
        array('3beta_47', 'php_true', '3*beta_47', 'cas_true', '3\cdot {\beta}_{47}', 'missing_stars', ""),
        array('a,b,c', 'php_false', 'a,b,c', 'cas_true', '', 'unencapsulated_comma', "Unencapsulated commas"),
        array('3,14159', 'php_false', '3,14159', 'cas_true', '', 'unencapsulated_comma', ""),
        array('0,5*x^2+3', 'php_false', '0,5*x^2+3', 'cas_true', '', 'unencapsulated_comma', ""),
        array('\sqrt{2+x}', 'php_false', '\sqrt{2+x}', 'cas_false', '', 'illegalcaschars', "Student uses LaTeX"),
        array('sin(x),cos(y)', 'php_false', 'sin(x),cos(y)', 'cas_true', '',
                'unencapsulated_comma', ""),
        array('sum(k^n,n,0,3)', 'php_true', 'sum(k^n,n,0,3)', 'cas_true', '\sum_{n=0}^{3}{k^{n}}', '', "Sums and products"),
        array('product(cos(k*x),k,1,3)', 'php_true', 'product(cos(k*x),k,1,3)', 'cas_true',
            '\prod_{k=1}^{3}{\cos \left( k\cdot x \right)}', '', '')
    );

    protected static $rawdataunits = array(
        array('123', 'php_true', '123', 'cas_true', '123\,', 'Units_SA_no_units', "Units"),
        array('9.81*m/s^2', 'php_true', '9.81*m/s^2', 'cas_true', '9.81\, {\mathrm{m}}/{\mathrm{s}^2}', '', ""),
        array('9.81*m*s^-2', 'php_true', '9.81*m*s^-2', 'cas_true', '9.81\, {\mathrm{m}}/{\mathrm{s}^2}', '', ""),
        array('2*(pi+1)', 'php_true', '2*(pi+1)', 'cas_true', '2\, \left(\pi+1\right)\,', 'Units_SA_no_units', ""),
        array('(2*(pi+1))*mm', 'php_true', '(2*(pi+1))*mm', 'cas_true',
            '2\, \left(\pi+1\right)\, \mathrm{m}\mathrm{m}', '', ""),
        array('2*(pi+1)*mm', 'php_true', '2*(pi+1)*mm', 'cas_true',
            '2\, \left(\pi+1\right)\, \mathrm{m}\mathrm{m}', '', ""),
        array('2*mm*(pi+1)', 'php_true', '2*mm*(pi+1)', 'cas_true',
            '2\, \left(\pi+1\right)\, \mathrm{m}\mathrm{m}', '', ""),
        array('(2*mm)*(pi+1)', 'php_true', '(2*mm)*(pi+1)', 'cas_true',
            '2\, \left(\pi+1\right)\, \mathrm{m}\mathrm{m}', '', ""),
        array('mm*2*(pi+1)', 'php_true', 'mm*2*(pi+1)', 'cas_true', '2\, \left(\pi+1\right)\, \mathrm{m}\mathrm{m}', '', ""),
        array('(pi+1)*2*mm', 'php_true', '(pi+1)*2*mm', 'cas_true', '\left(\pi+1\right)\, 2\, \mathrm{m}\mathrm{m}', '', ""),
        array('(pi+1)*mm*2', 'php_true', '(pi+1)*mm*2', 'cas_true', '\left(\pi+1\right)\, 2\, \mathrm{m}\mathrm{m}', '', ""),
        array('(2*1*(pi+1))*mm', 'php_true', '(2*1*(pi+1))*mm', 'cas_true',
            '2\, 1\, \left(\pi+1\right)\, \mathrm{m}\mathrm{m}', '', ""),
        array('(1*2*(pi+1))*mm', 'php_true', '(1*2*(pi+1))*mm', 'cas_true',
            '1\, 2\, \left(\pi+1\right)\, \mathrm{m}\mathrm{m}', '', ""),
        array('(2*pi+2)*mm', 'php_true', '(2*pi+2)*mm', 'cas_true',
            '\left( 2\, \pi+2\right)\, \mathrm{m}\mathrm{m}', '', ""),
        array('(2+0)*(pi+1)*mm', 'php_true', '(2+0)*(pi+1)*mm', 'cas_true',
            '\left(2+0\right)\, \left(\pi+1\right)\, \mathrm{m}\mathrm{m}', '', ""),
        array('(pi+1)*(2*mm)', 'php_true', '(pi+1)*(2*mm)', 'cas_true',
            '\left(\pi+1\right)\, 2\, \mathrm{m}\mathrm{m}', '', ""),
    );

    public static function get_raw_test_data() {
        return self::$rawdata;
    }

    public static function get_raw_test_data_units() {
        return self::$rawdataunits;
    }

    public static function test_from_raw($data, $validationmethod) {

        $test = new stdClass();
        $test->rawstring        = $data[self::RAWSTRING];
        $test->phpvalid         = $data[self::PHPVALID];
        $test->phpcasstring     = $data[self::PHPCASSTRING];
        $test->casvalid         = $data[self::CASVALID];
        $test->display          = $data[self::DISPLAY];
        $test->notes            = $data[self::NOTES];
        $test->ansnotes         = $data[self::ANSNOTES];
        $test->validationmethod = $validationmethod;

        $test->passed           = null;
        $test->errors           = null;
        $test->caserrors        = null;
        $test->casdisplay       = null;
        $test->casvalue         = null;
        $test->casnotes         = null;
        return $test;
    }

    public static function get_all() {
        $tests = array();
        foreach (self::$rawdata as $data) {
            $tests[] = self::test_from_raw($data, 'typeless');
        }
        foreach (self::$rawdataunits as $data) {
            $tests[] = self::test_from_raw($data, 'units');
        }
        return $tests;
    }

    public static function run_test($test) {
        // @codingStandardsIgnoreStart

        // Note: What we would really like to do is the following.
        // $el = stack_input_factory::make('algebraic', 'sans1', 'x');
        // $el->set_parameter('insertStars', 1);
        // $el->set_parameter('sameType', false);
        // $cs = $el->validate_student_response($test->rawstring);
        // However, we want to pull apart the bits to expose where the various errors occur.

        // @codingStandardsIgnoreEnd

        // We need to duplicate certain insert stars logic from input base.
        $filterstoapply = array();

        // The common insert stars rules, that will be forced
        // and if you do not allow inserttion of stars then it is invalid.
        $filterstoapply[] = '402_split_prefix_from_common_function_name';
        $filterstoapply[] = '404_split_at_number_letter_number_boundary';
        $filterstoapply[] = '406_split_implied_variable_names';

        $filterstoapply[] = '502_replace_pm';

        // We want to apply this as our "insert stars" but not spaces...
        $filterstoapply[] = '990_no_fixing_spaces';

        $cs = stack_ast_container::make_from_student_source($test->rawstring, '', new stack_cas_security(), $filterstoapply);
        $cs->set_cas_validation_context('ans1', true, '', $test->validationmethod, false);

        $phpvalid     = $cs->get_valid();
        $phpcasstring = $cs->get_inputform();
        $errors       = $cs->get_errors();

        $passed = true;
        if ('php_true' === $test->phpvalid) {
            $expected = true;
        } else {
            $expected = false;
        }

        if ($phpvalid != $expected) {
            $passed = false;
            $errors .= ' '.stack_string('phpvalidatemismatch');
        }
        if ($phpvalid && $phpcasstring != $test->phpcasstring) {
            $passed = false;
            $errors .= ' ' . stack_maxima_format_casstring($phpcasstring) .
                    ' \(\neq \) '.stack_maxima_format_casstring($test->phpcasstring);
        }

        $casvalid = '';
        $caserrors = '';
        $casvalue = '';
        $casdisplay = '';

        if ($cs->get_valid()) {
            $options = new stack_options();
            $options->set_option('simplify', false);

            $session = new stack_cas_session2(array($cs), $options, 0);
            $session->instantiate();
            $caserrors = stack_maxima_translate($cs->get_errors());
            if ($cs->get_errors() === '') {
                // If it has errors it could not be evaluated and you may
                // not ask the value of something not evaluated.
                $casvalue = stack_maxima_format_casstring($cs->get_value());
            }
            if ('cas_true' == $test->casvalid) {
                $casexpected = true;
            } else {
                $casexpected = false;
            }
            $casvalid = $cs->get_valid();

            if ($casexpected != $casvalid) {
                $passed = false;
                $caserrors .= ' '.stack_string('casvalidatemismatch');
            }
            $casdisplay = '';
            if ($cs->is_correctly_evaluated()) {
                $casdisplay = trim($cs->get_display());
            }
            if ($casdisplay != $test->display) {
                $passed = false;
                $errors .= ' ' . stack_string('displaymismatch') . html_writer::tag('pre', s($test->display)) . html_writer::tag('pre', s($casdisplay));
            }
        }

        $answernote = $cs->get_answernote();
        if ($answernote != $test->ansnotes) {
            $passed = false;
            $errors .= ' '.stack_string('ansnotemismatch');
            $errors .= html_writer::tag('pre', s($test->ansnotes)).html_writer::tag('pre', s($answernote));
        }

        $test->passed     = $passed;
        $test->errors     = $errors;
        $test->caserrors  = $caserrors;
        $test->casdisplay = $casdisplay;
        $test->casvalue   = $casvalue;
        $test->casnotes   = $answernote;
        return $test;
    }
}

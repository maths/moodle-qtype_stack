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

    const BRITISH       = 1;
    const CONTINENTIAL  = 2;


    protected static $rawdata = [

        ['123', 'php_true', '123', 'cas_true', '123', '', ""],
        ['x', 'php_true', 'x', 'cas_true', 'x', '', ""],
        // We map \ud835\udc00 to \u1D400 by hand because PHP!
        ["\u{1D400}", 'php_true', 'A', 'cas_true', 'A', '', ""],
        ["\u{1D435}", 'php_true', 'B', 'cas_true', 'B', '', ""],
        ["\u{03c0}\u{00d7}r^2", 'php_true', 'pi*r^2', 'cas_true', '\pi\cdot r^2', '', ""],
        ["\u{1F497}", 'php_false', '', '', '', 'forbiddenChar', ""],
        ['xy', 'php_true', 'xy', 'cas_true', '{\it xy}', '', "This is a single variable name, not a product."],
        ['x+1', 'php_true', 'x+1', 'cas_true', 'x+1', '', ""],
        ['x+ 1', 'php_true', 'x+1', 'cas_true', 'x+1', '', ""],
        ['x + 1', 'php_true', 'x+1', 'cas_true', 'x+1', '', "Ok to have some spaces between these operators."],
        [
            'sin x', 'php_false', '', '', '', 'spaces | trigspace',
            "Maxima does not allow spaces to denote function application.",
        ],
        ['x y', 'php_false', '', '', '', 'spaces', "We don't allow spaces to denote implicit multiplication."],
        ['1 x', 'php_false', '', '', '', 'spaces', ""],
        ['12 3', 'php_false', '', '', '', 'spaces', ""],
        ['12 3.7', 'php_false', '', '', '', 'spaces', ""],
        ['12.5 3', 'php_false', '', '', '', 'spaces', ""],

        ['1x', 'php_true', '1*x', 'cas_true', '1\cdot x', 'missing_stars', ""],
        // Default input base class filters don't insert *s here.
        ['x1', 'php_true', 'x1', 'cas_true', 'x_{1}', '', ""],
        ['1', 'php_true', '1', 'cas_true', '1', '', "Numbers"],
        ['.1', 'php_true', 'dispdp(.1,1)', 'cas_true', '0.1', '', ""],
        ['0.2000', 'php_true', 'dispdp(0.2000,4)', 'cas_true', '0.2000', '', ""],
        ['0.2000/0.030', 'php_true', 'dispdp(0.2000,4)/dispdp(0.030,3)', 'cas_true', '\frac{0.2000}{0.030}', '', ""],
        ['1/2', 'php_true', '1/2', 'cas_true', '\frac{1}{2}', '', ""],
        [
            '2/4', 'php_true', '2/4', 'cas_true', '\frac{2}{4}', 'Lowest_Terms',
            "Rejecting this as 'invalid' not 'wrong' is a question option.",
        ],
        ['-10/-1', 'php_true', '-10/-1', 'cas_true', '\frac{-10}{-1}', 'Lowest_Terms', ""],
        ['1/0', 'php_true', '1/0', 'cas_false', '', 'CASError: Division by zero.', ""],
        ['-a/b', 'php_true', '-a/b', 'cas_true', '\frac{-a}{b}', '', ""],
        ['-(a/b)', 'php_true', '-(a/b)', 'cas_true', '-\frac{a}{b}', '', ""],
        ['pi', 'php_true', 'pi', 'cas_true', '\pi', '', ""],
        ['e', 'php_true', 'e', 'cas_true', 'e', '', "Cannot easily make \(e\) a variable name."],
        [
            'i', 'php_true', 'i', 'cas_true', '\mathrm{i}', '',
            "Options to make i a variable, or a vector unit.  Note this is not italic.",
        ],
        [
            'j', 'php_true', 'j', 'cas_true', 'j', '',
            "Can define \(j^2=-1\) as an option, or a vector unit.  By default a variable, so italic.",
        ],
        ['inf', 'php_true', 'inf', 'cas_true', '\infty', '', ""],
        ["\u{221e}", 'php_true', 'inf', 'cas_true', '\infty', '', ""],

        // Different version of Maxima (LISP?) use 1E3 or 1e3.
        ['1E+3', 'php_true', 'displaysci(1,0,3)', 'cas_true', '1 \times 10^{3}', '', "Scientific notation"],
        ['3E2', 'php_true', 'displaysci(3,0,2)', 'cas_true', '3 \times 10^{2}', '', ""],
        ['3e2', 'php_true', 'displaysci(3,0,2)', 'cas_true', '3 \times 10^{2}', '', ""],
        ['3e-2', 'php_true', 'displaysci(3,0,-2)', 'cas_true', '3 \times 10^{-2}', '', ""],
        ['52%', 'php_false', '52%', '', '', 'finalChar', ""],
        ['5.20%', 'php_false', '5.20%', '', '', 'finalChar', ""],
        ['3.67x10^2', 'php_true', 'dispdp(3.67,2)*x10^2', 'cas_true', '3.67\cdot x_{10}^2', 'missing_stars', ""],
        ['3.67*x 10^2', 'php_false', 'dispdp(3.67,2)*x*10^2', 'cas_true', '', 'spaces', ""],
        ['1+i', 'php_true', '1+i', 'cas_true', '1+\mathrm{i}', '', ""],
        ['3-i', 'php_true', '3-i', 'cas_true', '3-\mathrm{i}', '', ""],
        ['-3+i', 'php_true', '-3+i', 'cas_true', '-3+\mathrm{i}', '', ""],
        ['1+2i', 'php_true', '1+2*i', 'cas_true', '1+2\cdot \mathrm{i}', 'missing_stars', ""],
        [
            '-(1/512) + i(sqrt(3)/512)', 'php_true', '-(1/512)+i*(sqrt(3)/512)', 'cas_true',
            '-\frac{1}{512}+\mathrm{i}\cdot \left(\frac{\sqrt{3}}{512}\right)', 'missing_stars', "",
        ],
        ['true', 'php_true', 'true', 'cas_true', '\mathbf{True}', '', "Booleans"],
        ['false', 'php_true', 'false', 'cas_true', '\mathbf{False}', '', ""],
        [
            '"1+1"', 'php_true', '"1+1"', 'cas_true', '\text{1+1}', '',
            "Strings - generally discouraged.  Note, this is a string within a mathematical expression, not literally 1+1.",
        ],
        ['"Hello world"', 'php_true', '"Hello world"', 'cas_true', '\text{Hello world}', '', ''],
        // In the continuous integration, "\"We \u{1F497} STACK!\" works with GCL but not with SBCL.
        ['x', 'php_true', 'x', 'cas_true', 'x', '', "Names for variables etc."],
        // Cases below represent a difference between 403 (used by default) and 404 (not used) adding * for a1.
        // That is for letter then number.
        ['a1', 'php_true', 'a1', 'cas_true', 'a_{1}', '', ""],
        ['a12', 'php_true', 'a12', 'cas_true', 'a_{12}', '', ""],
        ['ab123', 'php_true', 'ab123', 'cas_true', '{\it ab}_{123}', '', ""],
        [
            'a9b', 'php_true', 'a9*b', 'cas_true', 'a_{9}\cdot b',
            'missing_stars | (403)', "Note the subscripting and the implied multiplication.",
        ],
        ['ab98cd', 'php_true', 'ab98*c*d', 'cas_true', '{\it ab}_{98}\cdot c\cdot d', 'missing_stars | (403)', ''],
        ["a'", 'php_false', '', '', '', 'apostrophe', ""],
        ['X', 'php_true', 'X', 'cas_true', 'X', '', ""],
        ['aXy1', 'php_true', 'aXy1', 'cas_true', '{\it aXy}_{1}', '', ""],
        // In STACK 4.3, the parser accepts these as functions.
        ['f(x)', 'php_true', 'f(x)', 'cas_true', 'f\left(x\right)', '', "Functions"],
        ['f(x)^2', 'php_true', 'f(x)^2', 'cas_true', 'f^2\left(x\right)', '', ""],
        ['a(x)', 'php_true', 'a(x)', 'cas_true', 'a\left(x\right)', '', ""],
        ['x(t+1)', 'php_true', 'x(t+1)', 'cas_true', 'x\left(t+1\right)', '', ""],
        // Because we are using x as a variable, we do insert a * here!
        ['x(x+1)', 'php_true', 'x*(x+1)', 'cas_true', 'x\cdot \left(x+1\right)', 'missing_stars | Variable_function', ""],
        ['f(x(x+f(1)))', 'php_true', 'f(x*(x+f(1)))', 'cas_true', 'f\left(x\cdot \left(x+f\left(1\right)\right)\right)',
            'missing_stars | Variable_function', "", ],
        ['x(sin(t)+1)', 'php_true', 'x(sin(t)+1)', 'cas_true', 'x\left(\sin \left( t \right)+1\right)', '', ""],
        ['b/a(x)', 'php_true', 'b/a(x)', 'cas_true', '\frac{b}{a\left(x\right)}', '', ""],
        ['3b+5/a(x)', 'php_true', '3*b+5/a(x)', 'cas_true', '3\cdot b+\frac{5}{a\left(x\right)}', 'missing_stars', ""],
        [
            'a/(a(x+1)+2)', 'php_true', 'a/(a*(x+1)+2)', 'cas_true', '\frac{a}{a\cdot \left(x+1\right)+2}',
            'missing_stars | Variable_function', "",
        ],
        ["f''(x)", 'php_false', '' , '', '', 'apostrophe', "Apostrophies again..."],
        ["''diff(f,x)", 'php_false', '' , '', '', 'Illegal_extraevaluation', ""],
        [
            'dosomething(x,y,z)', 'php_false', '', '', '', 'forbiddenFunction',
            "Students have a restricted list of function names.  Teachers are less restricted.",
        ],
        ['[]', 'php_true', '[]', 'cas_true', '\left[ \right]', '', "Lists"],
        ['[1]', 'php_true', '[1]', 'cas_true', '\left[ 1 \right]', '', ""],
        ['[1,2,3.4]', 'php_true', '[1,2,dispdp(3.4,1)]', 'cas_true', '\left[ 1 , 2 , 3.4 \right]', '', ""],
        [
            '[1.000,2,3.40]', 'php_true', '[dispdp(1.000,3),2,dispdp(3.40,2)]', 'cas_true',
            '\left[ 1.000 , 2 , 3.40 \right]', '', "",
        ],
        ['[x, y, z ]', 'php_true', '[x,y,z]', 'cas_true', '\left[ x , y , z \right]', '', ""],
        ['["a"]', 'php_true', '["a"]', 'cas_true', '\left[ \text{a} \right]', '', ""],
        ['[1,true,"a"]', 'php_true', '[1,true,"a"]', 'cas_true', '\left[ 1 , \mathbf{True} , \text{a} \right]', '', ""],
        [
            '[[1,2],[3,4]]', 'php_true', '[[1,2],[3,4]]', 'cas_true',
            '\left[ \left[ 1 , 2 \right] , \left[ 3 , 4 \right] \right]', '', "",
        ],
        ['{}', 'php_true', '{}', 'cas_true', '\left \{ \right \}', '', "Sets"],
        ["\u{2205}", 'php_true', '{}', 'cas_true', '\left \{ \right \}', '', ""],
        ["\u{29b0}", 'php_true', '{}', 'cas_true', '\left \{ \right \}', '', ""],
        ["{\u{2205}}", 'php_true', '{{}}', 'cas_true', '\left \{\left \{ \right \} \right \}', '', ""],
        ['{1}', 'php_true', '{1}', 'cas_true', '\left \{1 \right \}', '', ""],
        ['{1,2,3.4}', 'php_true', '{1,2,dispdp(3.4,1)}', 'cas_true', '\left \{1 , 2 , 3.4 \right \}', '', ""],
        [
            '{1.000,2,3.40}', 'php_true', '{dispdp(1.000,3),2,dispdp(3.40,2)}', 'cas_true',
            '\left \{1.000 , 2 , 3.40 \right \}', '', "",
        ],
        ['{x, y, z }', 'php_true', '{x,y,z}', 'cas_true', '\left \{x , y , z \right \}', '', ""],
        ['set(x, y, z)', 'php_false', '', '', '', 'forbiddenFunction', ""],
        [
            'union(oo(2,3),oo(4,inf))', 'php_true', 'union(oo(2,3),oo(4,inf))', 'cas_true',
            '\left( 2,\, 3\right) \cup \left( 4,\, \infty \right)', '', "",
        ],
        [
            'union(oc(2,3),co(4,inf),cc(-1,1))', 'php_true', 'union(oc(2,3),co(4,inf),cc(-1,1))', 'cas_true',
            '\left( 2,\, 3\right] \cup \left[ 4,\, \infty \right) \cup \left[ -1,\, 1\right]', '', "",
        ],
        [
            'union({3,7})', 'php_true', 'union({3,7})', 'cas_true',
            '\left \{3 , 7 \right \}', '', "",
        ],
        [
            'intersection(oo(2,3),oo(4,inf))', 'php_true', 'intersection(oo(2,3),oo(4,inf))', 'cas_true',
            '\left( 2,\, 3\right) \cap \left( 4,\, \infty \right)', '', "",
        ],
        [
            'matrix([a,b],[c,d])', 'php_true', 'matrix([a,b],[c,d])', 'cas_true',
            '\left[\begin{array}{cc} a & b \\\\ c & d \end{array}\right]', '', 'Matrices',
        ],
        ['A.B', 'php_true', 'A . B', 'cas_true', 'A\cdot B', '', ""],
        ['stackvector(a)', 'php_true', 'stackvector(a)', 'cas_true', '{\bf a}', '', 'Vectors'],
        ['a[2]', 'php_true', 'a[2]', 'cas_true', 'a_{2}', '', "Maxima arrays"],
        ['a[n+1]', 'php_true', 'a[n+1]', 'cas_true', 'a_{n+1}', '', ""],
        ['a[1,2]', 'php_true', 'a[1,2]', 'cas_true', 'a_{1,2}', '', ""],
        [
            '(a,b,c)', 'php_true', 'ntuple(a,b,c)', 'cas_true', '\left(a, b, c\right)', '',
            "In Maxima this syntax is a programme block which we turn into an inert function for student's input.",
        ],
        [
            '{(x,y),(b,c)}', 'php_true', '{ntuple(x,y),ntuple(b,c)}', 'cas_true',
            '\left \{\left(x, y\right) , \left(b, c\right) \right \}', '', "",
        ],
        ['((x,y),a)', 'php_true', 'ntuple(ntuple(x,y),a)', 'cas_true', '\left(\left(x, y\right), a\right)', '', ""],
        ['((x,y)/2,a)', 'php_false', 'ntuple((x,y)/2,a)', 'cas_true', '', 'Illegal_groups', ""],
        ['(x,y)+3', 'php_false', 'ntuple(x,y)+3', 'cas_true', '', 'Illegal_groups', ""],
        ['f((x,y),2)', 'php_true', 'f(ntuple(x,y),2)', 'cas_true', 'f\left(\left(x, y\right) , 2\right)', '', ""],
        [
            '0..1', 'php_false', '', '', '', 'spuriousop',
            "Ranges and logical operations are currently not supported by Maxima or STACK
        - this is on our wish list. It will result in the ability to deal with systems of inequalities, e.g. \(x<1\ and\ x>-4\).",
        ],
        ['0.1..1.2', 'php_false', '', '', '', 'spuriousop', ""],
        ['not x', 'php_true', 'not x', 'cas_true', '{\rm not}\left( x \right)', '', ""],
        ['x and y', 'php_true', 'x and y', 'cas_true', 'x\,{\text{ and }}\, y', '', ""],
        ['true and false', 'php_true', 'true and false', 'cas_true', '\mathbf{True}\,{\text{ and }}\, \mathbf{False}', '', ""],
        ['x or y', 'php_true', 'x or y', 'cas_true', 'x\,{\text{ or }}\, y', '', ""],
        ['x xor y', 'php_true', 'x xor y', 'cas_true', 'x\,{\text{ xor }}\, y', '', ""],
        ['x nor y', 'php_true', 'x nor y', 'cas_true', 'x\,{\text{ nor }}\, y', '', ""],
        ['x nand y', 'php_true', 'x nand y', 'cas_true', 'x\,{\text{ nand }}\, y', '', ""],
        ['x implies y', 'php_true', 'x implies y', 'cas_true', 'x\,{\text{ implies }}\, y', '', ""],
        ['not false', 'php_true', 'not false', 'cas_true', '{\rm not}\left( \mathbf{False} \right)', '', ""],
        ['not(false)', 'php_true', 'not(false)', 'cas_true', '{\rm not}\left( \mathbf{False} \right)', '', ""],
        ['x isa "number"', 'php_false', '', '', '', 'spaces', ""],
        ['x && y', 'php_false', '', '', '', 'spuriousop', ""],
        ['x || y', 'php_false', '', '', '', 'spuriousop', ""],
        [
            'x * y', 'php_true', 'x*y', 'cas_true', 'x\cdot y', '',
            "Operations: there are options on how this is displayed, either as \(x\cdot y\), \(x\\times y\), or as \(x\, y\).",
        ],
        ['x + y', 'php_true', 'x+y', 'cas_true', 'x+y', '', ""],
        ['x - y', 'php_true', 'x-y', 'cas_true', 'x-y', '', ""],
        ["x \u{2052} y", 'php_true', 'x-y', 'cas_true', 'x-y', '', ""],
        ['x / y', 'php_true', 'x/y', 'cas_true', '\frac{x}{y}', '', ""],
        ['x ^ y', 'php_true', 'x^y', 'cas_true', 'x^{y}', '', ""],
        ['x < y', 'php_true', 'x < y', 'cas_true', 'x < y', '', ""],
        ['x > y', 'php_true', 'x > y', 'cas_true', 'x > y', '', ""],
        ['x = y', 'php_true', 'x = y', 'cas_true', 'x=y', '', ""],
        ['x # y', 'php_true', 'x#y', 'cas_true', 'x\neq y', '', ""],
        ['x!', 'php_true', 'x!', 'cas_true', 'x!', '', ""],
        ['!x', 'php_false', '!x', 'cas_false', '', 'badpostfixop', ""],
        ['x_1', 'php_true', 'x_1', 'cas_true', '{x}_{1}', '', ""],
        ['ab_12', 'php_true', 'ab_12', 'cas_true', '{{\it ab}}_{12}', '', ""],
        ['x_y', 'php_true', 'x_y', 'cas_true', '{x}_{y}', '', ""],
        [
            'x <= y', 'php_true', 'x <= y', 'cas_true', 'x\leq y', '',
            "Inequalities in various forms.",
        ],
        ['x >= y', 'php_true', 'x >= y', 'cas_true', 'x\geq y', '', ""],
        ["x \u{2265} y", 'php_true', 'x >= y', 'cas_true', 'x\geq y', '', ""],
        ['x => y', 'php_false', 'x=>y', '', '', 'backward_inequalities', ""],
        ['x => and x<1', 'php_false', 'x => and x<1', '', '', 'backward_inequalities', ""],
        ['x<1 and x>1', 'php_true', 'x < 1 and x > 1', 'cas_true', 'x < 1\,{\text{ and }}\, x > 1', '', ""],
        ["A<1 \u{22c1} B>1", 'php_false', 'A < 1 or B > 1', 'cas_true', '', 'forbiddenChar', ""],
        [
            'x>1 or (x<1 and t<sin(x))', 'php_true', 'x > 1 or (x < 1 and t < sin(x))', 'cas_true',
            'x > 1\,{\text{ or }}\, x < 1\,{\text{ and }}\, t < \sin \left( x \right)', '', "",
        ],
        [
            'x>1 and (x<1 or t<sin(x))', 'php_true', 'x > 1 and (x < 1 or t < sin(x))', 'cas_true',
            'x > 1\,{\text{ and }}\, \left(x < 1\,{\text{ or }}\, t < \sin \left( x \right)\right)', '', "",
        ],
        ['1<x<3', 'php_false', '', '', '', 'chained_inequalities', ""],
        ['1<=x<y^2', 'php_false', '', '', '', 'chained_inequalities', ""],
        ['1=<x<3', 'php_false', '', '', '', 'backward_inequalities', ""],
        ['x=1 or 2', 'php_true', 'x = 1 or 2', 'cas_true', 'x=1\,{\text{ or }}\, 2', 'Bad_assignment', ""],
        ['x=1 or x=2', 'php_true', 'x = 1 or x = 2', 'cas_true', 'x=1\,{\text{ or }}\, x=2', '', ""],
        [
            'x=1 or 2 or 3', 'php_true', 'x = 1 or 2 or 3', 'cas_true',
            'x=1\,{\text{ or }}\, 2\,{\text{ or }}\, 3', 'Bad_assignment', "",
        ],
        ['[1<x,x<3]', 'php_true', '[1 < x,x < 3]', 'cas_true', '\left[ 1 < x , x < 3 \right]', '', ""],
        [
            '[1 < x,y < 1 or y > 7]', 'php_true', '[1 < x,y < 1 or y > 7]', 'cas_true',
            '\left[ 1 < x , y < 1\,{\text{ or }}\, y > 7 \right]', '', "",
        ],
        ['[1<x,1<y<3]', 'php_false', '', '', '', 'chained_inequalities', ""],
        ['x <> y', 'php_false', '', '', '', 'spuriousop', "This isn't permitted in Maxima"],
        ['x+', 'php_false', 'x+', '', '', 'finalChar', "Not enough arguments for op error"],
        ['y*', 'php_false', 'y*', '', '', 'finalChar', ""],
        ['x^', 'php_flase', 'x^', '', '', 'finalChar', ""],
        ['x.', 'php_flase', 'x.', '', '', 'finalChar', ""],
        ['x and', 'php_false', '', '', '', 'spaces', ""],
        ['!', 'php_false', '!', 'badpostfixop', '', 'badpostfixop', ""],
        [
            'sin', 'php_false', 'sin', 'cas_true', '', 'forbiddenVariable',
            "This names the operator sine, which is a valid expression on its own.
        The classic difference between the function \(f\) and the value of the
        function at a point \(f(x)\).  Maybe a 'gocha' for the question author....",
        ],
        [
            '(x+y)^z', 'php_true', '(x+y)^z', 'cas_true', '{\left(x+y\right)}^{z}', '',
            "Check display: brackets only go round operands when strictly necessary, but student validation respects the input.",
        ],
        ['x+(y^z)', 'php_true', 'x+(y^z)', 'cas_true', 'x+y^{z}', '', ""],
        ['x-(y+z)', 'php_true', 'x-(y+z)', 'cas_true', 'x-\left(y+z\right)', '', ""],
        ['(x-y)+z', 'php_true', '(x-y)+z', 'cas_true', 'x-y+z', '', ""],
        ['3*(x-2)^2+1', 'php_true', '3*(x-2)^2+1', 'cas_true', '3\cdot {\left(x-2\right)}^2+1', '', ""],
        ["3*\u{FF08}x-2\u{FF09}^2+1", 'php_true', '3*(x-2)^2+1', 'cas_true', '3\cdot {\left(x-2\right)}^2+1', '', ""],
        ['x^(-(y+z))', 'php_true', 'x^(-(y+z))', 'cas_true', 'x^ {- \left(y+z\right) }', '', ""],
        ['x^(-y)', 'php_true', 'x^(-y)', 'cas_true', 'x^ {- y }', '', ""],
        ['x^-y', 'php_true', 'x^-y', 'cas_true', 'x^ {- y }', '', ""],
        ['x^(y+z)', 'php_true', 'x^(y+z)', 'cas_true', 'x^{y+z}', '', ""],
        ['(1+i)*x', 'php_true', '(1+i)*x', 'cas_true', '\left(1+\mathrm{i}\right)\cdot x', '', ""],
        ['(1+i)+x', 'php_true', '(1+i)+x', 'cas_true', '1+\mathrm{i}+x', '', ""],
        ['y^3-2*y^2-8*y', 'php_true', 'y^3-2*y^2-8*y', 'cas_true', 'y^3-2\cdot y^2-8\cdot y', '', ""],
        ['y^2-2*y-8', 'php_true', 'y^2-2*y-8', 'cas_true', 'y^2-2\cdot y-8', '', ""],
        ['y^2-2*y-0.50', 'php_true', 'y^2-2*y-dispdp(0.50,2)', 'cas_true', 'y^2-2\cdot y-0.50', '', ""],
        ['(x)', 'php_true', '(x)', 'cas_true', 'x', '', "Brackets"],
        ['((x))', 'php_true', '((x))', 'cas_true', 'x', '', ""],
        ['(()x)', 'php_false', '(()*x)', 'cas_false', '', 'missing_stars | emptyParens', ""],
        ['()x', 'php_false', '()*x', 'cas_false', '', 'missing_stars | emptyParens', ""],
        ['x()', 'php_false', 'x*()', 'cas_false', '', 'emptyParens', ""],
        ['([x)]', 'php_false', '([x)]', '', '', 'ParseError', ""],
        ['(', 'php_false', '', '', '', 'missingRightBracket', "Brackets"],
        [')', 'php_false', '', '', '', 'missingLeftBracket', ""],
        ['[', 'php_false', '', '', '', 'missingRightBracket', ""],
        [']', 'php_false', '', '', '', 'missingLeftBracket', ""],
        ['{', 'php_false', '', '', '', 'missingRightBracket', ""],
        ['}', 'php_false', '', '', '', 'missingLeftBracket', ""],
        ['x)', 'php_false', '', '', '', 'missingLeftBracket', ""],
        ['(x', 'php_false', '', '', '', 'missingRightBracket', ""],
        ['(x+(y)', 'php_false', '', '', '', 'missingRightBracket', ""],
        ['x-1)^2', 'php_false', '', '', '', 'missingLeftBracket', ""],
        ['x+(y))', 'php_false', '', '', '', 'missingLeftBracket', ""],
        ['f(x))', 'php_false', '', '', '', 'missingLeftBracket', ""],
        ['[x', 'php_false', '', '', '', 'missingRightBracket', ""],
        ['x]', 'php_false', '', '', '', 'missingLeftBracket', ""],
        ['{x', 'php_false', '', '', '', 'missingRightBracket', ""],
        [
            'alpha', 'php_true', 'alpha', 'cas_true', '\alpha', '',
            "Greek letters - quite a few have meanings in Maxima already.",
        ],
        ['alpha(x)', 'php_true', 'alpha(x)', 'cas_true', '\alpha\left(x\right)', '', ""],
        [
            'beta', 'php_true', 'beta', 'cas_true', '\beta', '',
            "The beta function is defined as \(\gamma(a) \gamma(b)/\gamma(a+b)\).",
        ],
        ['gamma', 'php_true', 'gamma', 'cas_true', '\gamma', '', "This is the gamma function."],
        ['delta', 'php_true', 'delta', 'cas_true', '\delta', '', "This is the Dirac Delta function."],
        ['epsilon', 'php_true', 'epsilon', 'cas_true', '\varepsilon', '', ""],
        ['zeta', 'php_true', 'zeta', 'cas_true', '\zeta', '', "This is the Riemann zeta function."],
        ['eta', 'php_true', 'eta', 'cas_true', '\eta', '', ""],
        ['theta', 'php_true', 'theta', 'cas_true', '\theta', '', ""],
        ['theta(s)', 'php_true', 'theta(s)', 'cas_true', '\theta\left(s\right)', '', ""],
        ['iota', 'php_true', 'iota', 'cas_true', '\iota', '', ""],
        ['kappa', 'php_true', 'kappa', 'cas_true', '\kappa', '', ""],
        ['lambda', 'php_true', 'lambda', 'cas_true', '\lambda', '', "Defines and returns a lambda expression."],
        ['mu', 'php_true', 'mu', 'cas_true', '\mu', '', ""],
        ['nu', 'php_true', 'nu', 'cas_true', '\nu', '', ""],
        ['xi', 'php_true', 'xi', 'cas_true', '\xi', '', ""],
        ['omicron', 'php_true', 'omicron', 'cas_true', 'o', '', ""],
        ['pi', 'php_true', 'pi', 'cas_true', '\pi', '', "This is a numeric constant."],
        ['rho', 'php_true', 'rho', 'cas_true', '\rho', '', ""],
        ['sigma', 'php_true', 'sigma', 'cas_true', '\sigma', '', ""],
        ['tau', 'php_true', 'tau', 'cas_true', '\tau', '', ""],
        ['upsilon', 'php_true', 'upsilon', 'cas_true', '\upsilon', '', ""],
        [
            'phi', 'php_true', 'phi', 'cas_true', '\varphi', '',
            "Constant, represents the so-called golden mean, \((1 + \sqrt{5})/2\).",
        ],
        ['phi(n)+Phi(n)', 'php_true', 'phi(n)+Phi(n)', 'cas_true', '\varphi\left(n\right)+\Phi\left(n\right)', '', ""],
        ['chi', 'php_true', 'chi', 'cas_true', '\chi', '', ""],
        ['psi', 'php_true', 'psi', 'cas_true', '\psi', '', "The derivative of \(\log (\gamma (x))\) of order \(n+1\)."],
        ['omega', 'php_true', 'omega', 'cas_true', '\omega', '', ""],
        ['p=?*s', 'php_true', 'p = ?*s', 'cas_true', 'p=\color{red}{?}\cdot s', '', "Question marks"],
        ['"WA?@AAA@AA"', 'php_true', '"WA?@AAA@AA"', 'cas_true', '\text{WA?@AAA@AA}', '', ""],
        ['(x+2)3', 'php_true', '(x+2)*3', 'cas_true', '\left(x+2\right)\cdot 3', 'missing_stars', "Implicit multiplication"],
        ['(x+2)y', 'php_true', '(x+2)*y', 'cas_true', '\left(x+2\right)\cdot y', 'missing_stars', ""],
        ['3(x+1)', 'php_true', '3*(x+1)', 'cas_true', '3\cdot \left(x+1\right)', 'missing_stars', ""],
        ['-3(x+1)', 'php_true', '-3*(x+1)', 'cas_true', '-3\cdot \left(x+1\right)', 'missing_stars', ""],
        ['2+3(x+1)', 'php_true', '2+3*(x+1)', 'cas_true', '2+3\cdot \left(x+1\right)', 'missing_stars', ""],
        ['x(2+1)', 'php_true', 'x(2+1)', 'cas_true', 'x\left(2+1\right)', '', ""],
        ['7x(2+1)', 'php_true', '7*x(2+1)', 'cas_true', '7\cdot x\left(2+1\right)', 'missing_stars', ""],
        ['(x+2)(x+3)', 'php_true', '(x+2)*(x+3)', 'cas_true', '\left(x+2\right)\cdot \left(x+3\right)', 'missing_stars', ""],
        [
            'cos(2x)(x+1)', 'php_true', 'cos(2*x)*(x+1)', 'cas_true', '\cos \left( 2\cdot x \right)\cdot \left(x+1\right)',
            'missing_stars', "",
        ],
        ['b(b+1)', 'php_true', 'b*(b+1)', 'cas_true', 'b\cdot \left(b+1\right)', 'missing_stars | Variable_function', ""],
        [
            '-b(5-b)', 'php_true', '-b*(5-b)', 'cas_true', '\left(-b\right)\cdot \left(5-b\right)',
            'missing_stars | Variable_function', "",
        ],
        [
            '-x(1+x)', 'php_true', '-x*(1+x)', 'cas_true', '\left(-x\right)\cdot \left(1+x\right)',
            'missing_stars | Variable_function', "",
        ],
        [
            '1-x(1+x)', 'php_true', '1-x*(1+x)', 'cas_true', '1-x\cdot \left(1+x\right)',
            'missing_stars | Variable_function', "",
        ],
        [
            '-3x(1+x)', 'php_true', '-3*x*(1+x)', 'cas_true', '-3\cdot x\cdot \left(1+x\right)',
            'missing_stars | Variable_function', "",
        ],
        ['i(1+i)', 'php_true', 'i*(1+i)', 'cas_true', '\mathrm{i}\cdot \left(1+\mathrm{i}\right)', 'missing_stars', ""],
        ['i(4)', 'php_true', 'i*(4)', 'cas_true', '\mathrm{i}\cdot 4', 'missing_stars', ""],
        // The next case is important: please don't call the result of a function f(x), with an argument (2).
        ['f(x)(2)', 'php_true', 'f(x)*(2)', 'cas_true', 'f\left(x\right)\cdot 2', 'missing_stars', ""],
        [
            'xsin(1)', 'php_true', 'x*sin(1)', 'cas_true', 'x\cdot \sin \left( 1 \right)', 'missing_stars | (402)',
            "single-letter variable name followed by known function is an implicit multiplication",
        ],
        ['ycos(2)', 'php_true', 'y*cos(2)', 'cas_true', 'y\cdot \cos \left( 2 \right)', 'missing_stars | (402)', ""],
        ['Bgcd(3,2)', 'php_true', 'B*gcd(3,2)', 'cas_true', 'B\cdot 1', 'missing_stars | (402)', ""],
        ['+1', 'php_true', '+1', 'cas_true', '+1', '', "Unary plus"],
        // Note: no + in front of the LaTeX below.
        ['+0.200', 'php_true', '+dispdp(0.200,3)', 'cas_true', '+0.200', '', ""],
        ['+e', 'php_true', '+e', 'cas_true', '+e', '', ""],
        ['+pi', 'php_true', '+pi', 'cas_true', '+\pi', '', ""],
        ['+i', 'php_true', '+i', 'cas_true', '+\mathrm{i}', '', ""],
        ['+x', 'php_true', '+x', 'cas_true', '+x', '', ""],
        // The example below is an "odd" output from Maxima.
        ['sqrt(+x)', 'php_true', 'sqrt(+x)', 'cas_true', '+\sqrt{x}', '', ""],
        ['sqrt(x)^3', 'php_true', 'sqrt(x)^3', 'cas_true', '{\sqrt{x}}^3', '', ""],
        // This was raised as issue #1281.
        ['x^+5', 'php_true', 'x^+5', 'cas_true', 'x+^{5}', '', ""],
        // The example below is an "odd" output from Maxima. I'm not planning to fix this!
        ['1/sin(+x)', 'php_true', '1/sin(+x)', 'cas_true', '\frac{1+}{\sin \left( x \right)}', '', ""],
        ['"+"(a,b)', 'php_true', '"+"(a,b)', 'cas_true', 'a+b', '', "This is Maxima specific syntax."],
        ['(+1)', 'php_true', '(+1)', 'cas_true', '+1', '', ""],
        ['[1,+2]', 'php_true', '[1,+2]', 'cas_true', '\left[ 1 , +2 \right]', '', ""],
        ['[+1,+2]', 'php_true', '[+1,+2]', 'cas_true', '\left[ +1 , +2 \right]', '', ""],
        ['-1', 'php_true', '-1', 'cas_true', '-1', '', "Unary minus"],
        ['-0.200', 'php_true', '-dispdp(0.200,3)', 'cas_true', '-0.200', '', ""],
        ['-e', 'php_true', '-e', 'cas_true', '-e', '', ""],
        ['-pi', 'php_true', '-pi', 'cas_true', '-\pi', '', ""],
        ['-i', 'php_true', '-i', 'cas_true', '-\mathrm{i}', '', ""],
        ['-x', 'php_true', '-x', 'cas_true', '-x', '', ""],
        ["\u{2212}x", 'php_true', '-x', 'cas_true', '-x', '', ""],
        ['-x[3]', 'php_true', '-x[3]', 'cas_true', '-x_{3}', '', ""],
        ['(-1)', 'php_true', '(-1)', 'cas_true', '-1', '', ""],
        ['[-1,-2]', 'php_true', '[-1,-2]', 'cas_true', '\left[ -1 , -2 \right]', '', ""],
        ['[1,-2]', 'php_true', '[1,-2]', 'cas_true', '\left[ 1 , -2 \right]', '', ""],
        ['y^3-2*y^2-8*y', 'php_true', 'y^3-2*y^2-8*y', 'cas_true', 'y^3-2\cdot y^2-8\cdot y', '', ""],
        [
            'x^7/7-2*x^6/3-4*x^3/3', 'php_true', 'x^7/7-2*x^6/3-4*x^3/3', 'cas_true',
            '\frac{x^7}{7}-\frac{2\cdot x^6}{3}-\frac{4\cdot x^3}{3}', '', "",
        ],
        ['+-1', 'php_true', '+-1', 'cas_true', '\pm 1', '', "Plus and minus"],
        ['x=+-sqrt(2)', 'php_true', 'x = +-sqrt(2)', 'cas_true', 'x= \pm \sqrt{2}', '', ""],
        [
            '(-b+-sqrt(b^2))/(2*a)', 'php_true', '(-b+-sqrt(b^2))/(2*a)', 'cas_true',
            '\frac{{-b \pm \sqrt{b^2}}}{2\cdot a}', '', "",
        ],
        ['a+-b', 'php_true', 'a+-b', 'cas_true', '{a \pm b}', '', ""],
        ['a-+b', 'php_true', 'a-+b', 'cas_true', 'a+-\left(b\right)', '', ""],
        ['x & y', 'php_false', 'x & y', '', '', 'spuriousop', "Synonyms"],
        ['x && y', 'php_false', 'x && y', '', '', 'spuriousop', ""],
        ['x and y', 'php_true', 'x and y', 'cas_true', 'x\,{\text{ and }}\, y', '', ""],
        ['x divides y', 'php_false', '', '', '', 'spaces', ""],
        ['x*divides*y', 'php_false', '', '', '', 'forbiddenVariable', ""],
        ['x | y', 'php_false', 'x|y', '', '', 'spuriousop', ""],
        ['x or y', 'php_true', 'x or y', 'cas_true', 'x\,{\text{ or }}\, y', '', ""],
        ['x || y', 'php_false', 'x || y', 'cas_true', '', 'spuriousop', ""],
        ['sqr(x)', 'php_false', '', '', '', 'forbiddenFunction', ""],
        [
            'sqrt(x)', 'php_true', 'sqrt(x)', 'cas_true', '\sqrt{x}', '',
            "There is an option to display this as \(x^{1/2}|\).",
        ],
        ['root(x)', 'php_true', 'root(x)', 'cas_true', '\sqrt{x}', '', ''],
        ['root(x,3)', 'php_true', 'root(x,3)', 'cas_true', '\sqrt[3]{x}', '', ''],
        ['root(2,-3)', 'php_true', 'root(2,-3)', 'cas_true', '\sqrt[-3]{2}', '', ''],
        // Parser rules in 4.3, identify cases where known functions (cf) are prefixed with single letter variables.
        ['bsin(t)', 'php_true', 'b*sin(t)', 'cas_true', 'b\cdot \sin \left( t \right)', 'missing_stars | (402)', ""],
        // So we have added gcf as a function so it is not g*cf...
        ['gcf(x,y)', 'php_true', 'gcf(x,y)', 'cas_true', '{\it gcf}\left(x , y\right)', '', ""],
        [
            'gcd(x,y)', 'php_true', 'gcd(x,y)', 'cas_true', '1', '',
            "Don't understand why this is evaluated by Maxima...",
        ],
        ['sign(x)', 'php_true', 'sign(x)', 'cas_true', '{\it pnz}', '', ""],
        ['len(x)', 'php_false', '', '', '', 'forbiddenFunction', ""],
        ['abs(x)', 'php_true', 'abs(x)', 'cas_true', '\left| x\right|', '', ""],
        ['signum(x)', 'php_true', 'signum(x)', 'cas_true', '\mathrm{sgn}\left(x\right)', '', ""],
        ['sgn(x)', 'php_true', 'sgn(x)', 'cas_true', '\mathrm{sgn}\left(x\right)', '', ""],
        ['|x|', 'php_true', 'abs(x)', 'cas_true', '\left| x\right|', '', ""],
        [
            'length(x)', 'php_true', 'length(x)', 'cas_false', '',
            'CASError: length: argument cannot be a symbol; found x', "",
        ],
        ['x^y^z', 'php_true', 'x^y^z', 'cas_true', 'x^{y^{z}}', '', "Associativity"],
        ['a/b/c', 'php_true', 'a/b/c', 'cas_true', '\frac{\frac{a}{b}}{c}', '', ""],
        ['a-(b-c)', 'php_true', 'a-(b-c)', 'cas_true', 'a-\left(b-c\right)', '', ""],
        ['(a-b)-c', 'php_true', '(a-b)-c', 'cas_true', 'a-b-c', '', ""],
        ['x*y*z', 'php_true', 'x*y*z', 'cas_true', 'x\cdot y\cdot z', '', "Commutativity"],
        ['(x*y)*z', 'php_true', '(x*y)*z', 'cas_true', 'x\cdot y\cdot z', '', ""],
        ['x*(y*z)', 'php_true', 'x*(y*z)', 'cas_true', 'x\cdot \left(y\cdot z\right)', '', ""],
        ['x+y+z', 'php_true', 'x+y+z', 'cas_true', 'x+y+z', '', ""],
        ['(x+y)+z', 'php_true', '(x+y)+z', 'cas_true', 'x+y+z', '', ""],
        ['x+(y+z)', 'php_true', 'x+(y+z)', 'cas_true', 'x+\left(y+z\right)', '', ""],
        ['x/y/z', 'php_true', 'x/y/z', 'cas_true', '\frac{\frac{x}{y}}{z}', '', ""],
        ['(x/y)/z', 'php_true', '(x/y)/z', 'cas_true', '\frac{\frac{x}{y}}{z}', '', ""],
        ['x/(y/z)', 'php_true', 'x/(y/z)', 'cas_true', '\frac{x}{\frac{y}{z}}', '', ""],
        ['x^y', 'php_true', 'x^y', 'cas_true', 'x^{y}', '', "Operations and functions with special TeX"],
        ["x\u{00b2}", 'php_false', '', 'cas_false', '', 'forbiddenChar', ""],
        ["x\u{00b2}*x\u{00b2}", 'php_false', '', 'cas_false', '', 'forbiddenChar', ""],
        ['x^(y+z)', 'php_true', 'x^(y+z)', 'cas_true', 'x^{y+z}', '', ""],
        ['x^(y/z)', 'php_true', 'x^(y/z)', 'cas_true', 'x^{\frac{y}{z}}', '', ""],
        ['x^f(x)', 'php_true', 'x^f(x)', 'cas_true', 'x^{f\left(x\right)}', '', ""],
        ['x*y^z', 'php_true', 'x*y^z', 'cas_true', 'x\cdot y^{z}', '', ""],
        ['y^z * x', 'php_true', 'y^z*x', 'cas_true', 'y^{z}\cdot x', '', ""],
        ['x*2^y', 'php_true', 'x*2^y', 'cas_true', 'x\cdot 2^{y}', '', ""],
        ['2^y*x', 'php_true', '2^y*x', 'cas_true', '2^{y}\cdot x', '', ""],
        ['2*pi', 'php_true', '2*pi', 'cas_true', '2\cdot \pi', '', ""],
        // Example of unicode letter replacement.
        ["\u{213c}", 'php_true', 'pi', 'cas_true', '\pi', '', ""],
        ["2*\u{213c}", 'php_true', '2*pi', 'cas_true', '2\cdot \pi', '', ""],
        ["2*\u{213c}*n", 'php_true', '2*pi*n', 'cas_true', '2\cdot \pi\cdot n', '', ""],
        ["2\u{213c}", 'php_true', '2*pi', 'cas_true', '2\cdot \pi', 'missing_stars', ""],
        // We've chosen to replace the unicode pi with the litteral pi to create the variable name "pin" here.
        ["2\u{213c}n", 'php_false', '2*pin', 'cas_true', '', 'missing_stars | forbiddenVariable', ""],
        ['2*e', 'php_true', '2*e', 'cas_true', '2\cdot e', '', ""],
        ['e*2', 'php_true', 'e*2', 'cas_true', 'e\cdot 2', '', ""],
        ['pi*2', 'php_true', 'pi*2', 'cas_true', '\pi\cdot 2', '', ""],
        ['i*2', 'php_true', 'i*2', 'cas_true', '\mathrm{i}\cdot 2', '', ""],
        ['2*i', 'php_true', '2*i', 'cas_true', '2\cdot \mathrm{i}', '', ""],
        ['2*i^3', 'php_true', '2*i^3', 'cas_true', '2\cdot \mathrm{i}^3', '', ""],
        ['x*i^3', 'php_true', 'x*i^3', 'cas_true', 'x\cdot \mathrm{i}^3', '', ""],
        ['x*(-y)', 'php_true', 'x*(-y)', 'cas_true', 'x\cdot \left(-y\right)', '', ""],
        ['(-x)*y', 'php_true', '(-x)*y', 'cas_true', '\left(-x\right)\cdot y', '', ""],
        ['abs(13)', 'php_true', 'abs(13)', 'cas_true', '\left| 13\right|', '', ""],
        ['fact(13)', 'php_false', '', '', '', 'forbiddenFunction', ""],
        ['ceiling(x)', 'php_true', 'ceiling(x)', 'cas_true', '\left \lceil x \right \rceil', '', ""],
        ['floor(x)', 'php_true', 'floor(x)', 'cas_true', '\left \lfloor x \right \rfloor', '', ""],
        ['int(x,y)', 'php_true', 'int(x,y)', 'cas_true', '\int {x}{\;\mathrm{d}y}', '', ""],
        [
            'int(sin(x))', 'php_true', 'int(sin(x))', 'cas_false', '',
            'CASError: int must have at least two arguments.', "",
        ],
        ['diff(x,y)', 'php_true', 'diff(x,y)', 'cas_true', '\frac{\mathrm{d} x}{\mathrm{d} y}', '', ""],
        [
            'diff(sin(x),x)', 'php_true', 'diff(sin(x),x)', 'cas_true',
            '\frac{\mathrm{d}}{\mathrm{d} x} \sin \left( x \right)', '', "",
        ],
        [
            'diff(sin(x))', 'php_true', 'diff(sin(x))', 'cas_false', '',
            'CASError: diff must have at least two arguments.', "",
        ],
        [
            'diff(x(t),t)', 'php_true', 'diff(x(t),t)', 'cas_true',
            '\frac{\mathrm{d}}{\mathrm{d} t} x\left(t\right)', '', "",
        ],
        [
            'diff(x,t,2)', 'php_true', 'diff(x,t,2)', 'cas_true',
            '\frac{\mathrm{d}^2 x}{\mathrm{d} t^2}', '', "",
        ],
        [
            'diff(x(t),t,2)', 'php_true', 'diff(x(t),t,2)', 'cas_true',
            '\frac{\mathrm{d}^2}{\mathrm{d} t^2} x\left(t\right)', '', "",
        ],
        // I think the chances of a student typing this in are low...
        [
            'diff(f,x,1,y,2)', 'php_true', 'diff(f,x,1,y,2)', 'cas_true',
            '\frac{\mathrm{d}^3 f}{\mathrm{d} x \mathrm{d} y^2}', '', "",
        ],
        ['y\'+y', 'php_false', '', 'cas_true', '', 'apostrophe', ""],
        ['y\'(x)+y(x)=0', 'php_false', '', 'cas_true', '', 'apostrophe', ""],
        [
            "'int(x,y)", 'php_false', '', 'cas_true', '', 'apostrophe',
            "Note the use of the apostrophe here to make an inert function.",
        ],
        ["'diff(x,y)", 'php_false', '', 'cas_true', '', 'apostrophe', "Not ideal...arises because we don't 'simplify'."],
        ['partialdiff(x,y,1)', 'php_false', '', '', '', 'missing_stars | (402) | forbiddenVariable', ""],
        ['limit(y,x,3)', 'php_true', 'limit(y,x,3)', 'cas_true', '\lim_{x\rightarrow 3}{y}', '', ""],
        ['mod(x,y)', 'php_true', 'mod(x,y)', 'cas_true', 'x \rm{mod} y', '', ""],
        ['binomial(n,m)', 'php_true', 'binomial(n,m)', 'cas_true', '{{n}\choose{m}}', '', ""],
        ['binomial(8,4)', 'php_true', 'binomial(8,4)', 'cas_true', '{{8}\choose{4}}', '', ""],
        ['binomial(n,[a,b,c])', 'php_true', 'binomial(n,[a,b,c])', 'cas_true', '{{n}\choose{a, b, c}}', '', ""],
        ['binomial(n,[a])', 'php_true', 'binomial(n,[a])', 'cas_true', '{{n}\choose{a}}', '', ""],
        ['perm(x,y)', 'php_false', '', '', '', 'forbiddenFunction', ""],
        ['comb(x,y)', 'php_false', '', '', '', 'forbiddenFunction', ""],
        ['switch(x,a,y,b,c)', 'php_false', '', '', '', 'forbiddenFunction', ""],
        ['sin(x)', 'php_true', 'sin(x)', 'cas_true', '\sin \left( x \right)', '', "Trig functions"],
        ['cos(x)', 'php_true', 'cos(x)', 'cas_true', '\cos \left( x \right)', '', ""],
        ['cos(x)^2', 'php_true', 'cos(x)^2', 'cas_true', '\cos ^2\left(x\right)', '', ""],
        ['cos(x+1)^2', 'php_true', 'cos(x+1)^2', 'cas_true', '\cos ^2\left(x+1\right)', '', ""],
        ['tan(x)', 'php_true', 'tan(x)', 'cas_true', '\tan \left( x \right)', '', ""],
        ['sec(x)', 'php_true', 'sec(x)', 'cas_true', '\sec \left( x \right)', '', ""],
        ['cot(x)', 'php_true', 'cot(x)', 'cas_true', '\cot \left( x \right)', '', ""],
        ['csc(x)', 'php_true', 'csc(x)', 'cas_true', '\csc \left( x \right)', '', ""],
        // This is now a Maxima alias.
        ['cosec(x)', 'php_true', 'cosec(x)', 'cas_true', '\csc \left( x \right)', '', ""],
        [
            'csc(6*x)^2*(7*sin(6*x)*cos(7*x)-6*cos(6*x)*sin(7*x))', 'php_true',
            'csc(6*x)^2*(7*sin(6*x)*cos(7*x)-6*cos(6*x)*sin(7*x))', 'cas_true',
            '\csc ^2\left(6\cdot x\right)\cdot \left(7\cdot \sin ' .
                '\left( 6\cdot x \right)\cdot \cos \left( 7\cdot x \right)-6\cdot ' .
                '\cos \left( 6\cdot x \right)\cdot \sin \left( 7\cdot x \right)\right)', '', "",
        ],
        ['Sin(x)', 'php_false', '', '', '', 'unknownFunctionCase', ""],
        ['sim(x)', 'php_false', '', '', '', 'forbiddenFunction', ""],
        [
            'asin(x)', 'php_true', 'asin(x)', 'cas_true', '\sin^{-1}\left( x \right)', '',
            "Maxima uses the asin pattern",
        ],
        ['arcsin(x)', 'php_true', 'asin(x)', 'cas_true', '\sin^{-1}\left( x \right)', 'triginv', "Not the arcsin"],
        ['arsinh(x)', 'php_true', 'asinh(x)', 'cas_true', '{\rm sinh}^{-1}\left( x \right)', 'triginv', ""],
        ['sin^-1(x)', 'php_false', 'sin^-1(x)', 'cas_false', '', 'missing_stars | trigexp', ""],
        ['cos^2(x)', 'php_false', 'cos^2(x)', 'cas_false', '', 'missing_stars | trigexp', ""],
        ["sin\u{00b2}(x)", 'php_false', 'sin^2(x)', 'cas_false', '', 'forbiddenChar', ""],
        ['sin*2*x', 'php_false', 'sin*2*x', 'cas_false', '', 'forbiddenVariable', ""],
        ['sin[2*x]', 'php_false', 'sin[2*x]', 'cas_false', '', 'trigparens', ""],
        ['cosh(x)', 'php_true', 'cosh(x)', 'cas_true', '\cosh \left( x \right)', '', ""],
        ['sinh(x)', 'php_true', 'sinh(x)', 'cas_true', '\sinh \left( x \right)', '', ""],
        ['tanh(x)', 'php_true', 'tanh(x)', 'cas_true', '\tanh \left( x \right)', '', ""],
        ['coth(x)', 'php_true', 'coth(x)', 'cas_true', '\coth \left( x \right)', '', ""],
        ['cosech(x)', 'php_true', 'cosech(x)', 'cas_true', '{\rm csch}\left( x \right)', '', ""],
        ['sech(x)', 'php_true', 'sech(x)', 'cas_true', '{\rm sech}\left( x \right)', '', ""],
        ['asinh(x)', 'php_true', 'asinh(x)', 'cas_true', '{\rm sinh}^{-1}\left( x \right)', '', "Etc..."],
        ['a^b', 'php_true', 'a^b', 'cas_true', 'a^{b}', '', "Exponentials and logarithms"],
        ['a ** b', 'php_true', 'a**b', 'cas_true', 'a^{b}', '', ""],
        ['x^-1', 'php_true', 'x^-1', 'cas_true', 'x^ {- 1 }', '', ""],
        ['a^-b', 'php_true', 'a^-b', 'cas_true', 'a^ {- b }', '', ""],
        ['e^x', 'php_true', 'e^x', 'cas_true', 'e^{x}', '', ""],
        ['%e^x', 'php_true', '%e^x', 'cas_true', 'e^{x}', '', ""],
        ["\u{212F}^x", 'php_true', 'e^x', 'cas_true', 'e^{x}', '', ""],
        ['exp(x)', 'php_true', 'exp(x)', 'cas_true', '\exp \left( x \right)', '', ""],
        ['log(x)', 'php_true', 'log(x)', 'cas_true', '\ln \left( x \right)', '', "Natural logarithm."],
        ['ln(x)', 'php_true', 'ln(x)', 'cas_true', '\ln \left( x \right)', '', "Natural logarithm, STACK alias."],
        ['ln*x', 'php_false', 'ln*x', '', '', 'forbiddenVariable', ""],
        ['In(x)', 'php_false', '', '', '', 'stackCas_badLogIn', ""],
        ['log10(x)', 'php_true', 'lg(x,10)', 'cas_true', '\log_{10}\left(x\right)', 'logsubs', ""],
        ['log_10(x)', 'php_true', 'lg(x,10)', 'cas_true', '\log_{10}\left(x\right)', 'logsubs', ""],
        ['log_2(a)', 'php_true', 'lg(a,2)', 'cas_true', '\log_{2}\left(a\right)', 'logsubs', ""],
        ['log_x(1/(x+b))', 'php_true', 'lg(1/(x+b),x)', 'cas_true', '\log_{x}\left(\frac{1}{x+b}\right)', 'logsubs', ""],
        ['log_2(a)^3', 'php_true', 'lg(a,2)^3', 'cas_true', '{\log_{2}\left(a\right)}^3', 'logsubs', ""],
        [
            '2+log_x(1/(x+b))*x^2', 'php_true', '2+lg(1/(x+b),x)*x^2', 'cas_true',
            '2+\log_{x}\left(\frac{1}{x+b}\right)\cdot x^2', 'logsubs', "",
        ],
        [
            'log_a(b)*log_b(c)', 'php_true', 'lg(b,a)*lg(c,b)', 'cas_true',
            '\log_{a}\left(b\right)\cdot \log_{b}\left(c\right)', 'logsubs', "",
        ],
        ['lg(x)', 'php_true', 'lg(x)', 'cas_true', '\log_{10}\left(x\right)', '', "Logarithm to the base \(10\)."],
        ['lg(10^3)', 'php_true', 'lg(10^3)', 'cas_true', '\log_{10}\left(10^3\right)', '', ""],
        ['lg(x,a)', 'php_true', 'lg(x,a)', 'cas_true', '\log_{a}\left(x\right)', '', ""],
        [
            'lg(x,2)+lg(x,2)^3', 'php_true', 'lg(x,2)+lg(x,2)^3', 'cas_true',
            '\log_{2}\left(x\right)+{\log_{2}\left(x\right)}^3', '', "",
        ],
        [
            'log(2x)/x+1/2', 'php_true', 'log(2*x)/x+1/2', 'cas_true',
            '\frac{\ln \left( 2\cdot x \right)}{x}+\frac{1}{2}', 'missing_stars', "",
        ],
        [
            'a++b', 'php_true', 'a++b', 'cas_true', 'a++\left(b\right)', '',
            "The extra plusses or minuses are interpreted as unary operators on b",
        ],
        ['a +++ b', 'php_true', 'a+++b', 'cas_true', 'a+++\left(\left(b\right)\right)', '', ""],
        ['a --- b', 'php_true', 'a---b', 'cas_true', 'a-\left(-\left(-b\right)\right)', '', ""],
        [
            'rho*z*V/(4*pi*epsilon[0]*(R^2+z^2)^(3/2))', 'php_true', 'rho*z*V/(4*pi*epsilon[0]*(R^2+z^2)^(3/2))', 'cas_true',
            '\frac{\rho\cdot z\cdot V}{4\cdot \pi\cdot \varepsilon_{0}\cdot {\left(R^2+z^2\right)}^{\frac{3}{2}}}',
            '', "Subscripts",
        ],
        ['a_b', 'php_true', 'a_b', 'cas_true', '{a}_{b}', '', ""],
        ['M_1', 'php_true', 'M_1', 'cas_true', '{M}_{1}', '', ""],
        ['xYz_123', 'php_true', 'xYz_123', 'cas_true', '{{\it xYz}}_{123}', '', ""],
        ['beta_47', 'php_true', 'beta_47', 'cas_true', '{\beta}_{47}', '', ""],
        ['3beta_47', 'php_true', '3*beta_47', 'cas_true', '3\cdot {\beta}_{47}', 'missing_stars', ""],
        // Subscripts in function names.
        ['a_b(x)', 'php_false', 'a_b(x)', 'cas_true', '', 'forbiddenFunction', ""],
        ['inverse_erf(x)', 'php_false', 'inverse_erf(x)', 'cas_true', '', 'missing_stars | (402) | forbiddenVariable', ""],

        ['a,b,c', 'php_false', 'a,b,c', 'cas_true', '', 'unencapsulated_comma', "Unencapsulated commas"],
        ['3,14159', 'php_false', '3,14159', 'cas_true', '', 'unencapsulated_comma', ""],
        ['0,5*x^2+3', 'php_false', '0,5*x^2+3', 'cas_true', '', 'unencapsulated_comma', ""],
        ['\sqrt{2+x}', 'php_false', '\sqrt{2+x}', 'cas_false', '', 'illegalcaschars', "Student uses LaTeX"],
        [
            'sin(x),cos(y)', 'php_false', 'sin(x),cos(y)', 'cas_true', '',
            'ParseError', "",
        ],
        ['sum(k^n,n,0,3)', 'php_true', 'sum(k^n,n,0,3)', 'cas_true', '\sum_{n=0}^{3}{k^{n}}', '', "Sums and products"],
        [
            'product(cos(k*x),k,1,3)', 'php_true', 'product(cos(k*x),k,1,3)', 'cas_true',
            '\prod_{k=1}^{3}{\cos \left( k\cdot x \right)}', '', '',
        ],
    ];

    protected static $rawdataunits = [
        ['123', 'php_true', '123', 'cas_true', '123', 'Units_SA_no_units', "Units"],
        ['9.81*m/s^2', 'php_true', 'dispdp(9.81,2)*m/s^2', 'cas_true', '9.81\, {\mathrm{m}}/{\mathrm{s}^2}', '', ""],
        ['9.81*m*s^-2', 'php_true', 'dispdp(9.81,2)*m*s^-2', 'cas_true', '9.81\, {\mathrm{m}}/{\mathrm{s}^2}', '', ""],
        ['2*(pi+1)', 'php_true', '2*(pi+1)', 'cas_true', '2\, \left(\pi+1\right)', 'Units_SA_no_units', ""],
        [
            '(2*(pi+1))*mm', 'php_true', '(2*(pi+1))*mm', 'cas_true',
            '2\, \left(\pi+1\right)\, \mathrm{m}\mathrm{m}', '', "",
        ],
        [
            '2*(pi+1)*mm', 'php_true', '2*(pi+1)*mm', 'cas_true',
            '2\, \left(\pi+1\right)\, \mathrm{m}\mathrm{m}', '', "",
        ],
        [
            '2*mm*(pi+1)', 'php_true', '2*mm*(pi+1)', 'cas_true',
            '2\, \left(\pi+1\right)\, \mathrm{m}\mathrm{m}', '', "",
        ],
        [
            '(2*mm)*(pi+1)', 'php_true', '(2*mm)*(pi+1)', 'cas_true',
            '2\, \left(\pi+1\right)\, \mathrm{m}\mathrm{m}', '', "",
        ],
        ['mm*2*(pi+1)', 'php_true', 'mm*2*(pi+1)', 'cas_true', '2\, \left(\pi+1\right)\, \mathrm{m}\mathrm{m}', '', ""],
        ['(pi+1)*2*mm', 'php_true', '(pi+1)*2*mm', 'cas_true', '\left(\pi+1\right)\, 2\, \mathrm{m}\mathrm{m}', '', ""],
        ['(pi+1)*mm*2', 'php_true', '(pi+1)*mm*2', 'cas_true', '\left(\pi+1\right)\, 2\, \mathrm{m}\mathrm{m}', '', ""],
        [
            '(2*1*(pi+1))*mm', 'php_true', '(2*1*(pi+1))*mm', 'cas_true',
            '2\, 1\, \left(\pi+1\right)\, \mathrm{m}\mathrm{m}', '', "",
        ],
        [
            '(1*2*(pi+1))*mm', 'php_true', '(1*2*(pi+1))*mm', 'cas_true',
            '1\, 2\, \left(\pi+1\right)\, \mathrm{m}\mathrm{m}', '', "",
        ],
        [
            '(2*pi+2)*mm', 'php_true', '(2*pi+2)*mm', 'cas_true',
            '\left( 2\, \pi+2\right)\, \mathrm{m}\mathrm{m}', '', "",
        ],
        [
            '(2+0)*(pi+1)*mm', 'php_true', '(2+0)*(pi+1)*mm', 'cas_true',
            '\left(2+0\right)\, \left(\pi+1\right)\, \mathrm{m}\mathrm{m}', '', "",
        ],
        [
            '(pi+1)*(2*mm)', 'php_true', '(pi+1)*(2*mm)', 'cas_true',
            '\left(\pi+1\right)\, 2\, \mathrm{m}\mathrm{m}', '', "",
        ],
    ];

    protected static $rawdatadecimals = [
        [
            0 => '123',
            '.' => [null, 'php_true', '123', 'cas_true', '123', '', ""],
            ',' => [null, 'php_true', '123', 'cas_true', '123', '', ""],
        ],
        [
            0 => '1.23',
            '.' => [null, 'php_true', 'dispdp(1.23,2)', 'cas_true', '1.23', '', ""],
            ',' => [null, 'php_false', '', '', '', 'forbiddenCharDecimal', ""],
        ],
        [
            0 => '-1.27',
            '.' => [null, 'php_true', '-dispdp(1.27,2)', 'cas_true', '-1.27', '', ""],
            ',' => [null, 'php_false', '', '', '', 'forbiddenCharDecimal', ""],
        ],
        [
            0 => '2.78e-3',
            '.' => [null, 'php_true', 'displaysci(2.78,2,-3)', 'cas_true', '2.78 \times 10^{-3}', '', ""],
            ',' => [null, 'php_false', '', '', '', 'forbiddenCharDecimal', ""],
        ],
        [
            0 => '1,23',
            '.' => [null, 'php_false', '', '', '', 'unencapsulated_comma', ""],
            ',' => [null, 'php_true', 'dispdp(1.23,2)', 'cas_true', '1.23', '', ""],
        ],
        [
            0 => '-1,29',
            '.' => [null, 'php_false', '', '', '', 'unencapsulated_comma', ""],
            ',' => [null, 'php_true', '-dispdp(1.29,2)', 'cas_true', '-1.29', '', ""],
        ],
        [
            0 => '2,79e-5',
            '.' => [null, 'php_false', '', '', '', 'unencapsulated_comma', ""],
            ',' => [null, 'php_true', 'displaysci(2.79,2,-5)', 'cas_true', '2.79 \times 10^{-5}', '', ""],
        ],
        // For students' input the character ; is forbidden, but not in this test.
        [
            0 => '1;23',
            '.' => [null, 'php_true', '1', 'cas_true', '1', '', ""],
            ',' => [null, 'php_false', '1', '', '', 'unencapsulated_comma', ""],
        ],
        // With strict interpretation both the following are invalid.
        [
            0 => '1.2+2,3*x',
            '.' => [null, 'php_false', '', '', '', 'unencapsulated_comma', ""],
            ',' => [null, 'php_false', '', '', '', 'forbiddenCharDecimal', ""],
        ],
        [
            0 => '{1,23}',
            '.' => [null, 'php_true', '{1,23}', 'cas_true', '\left \{1 , 23 \right \}', '', ""],
            ',' => [null, 'php_true', '{dispdp(1.23,2)}', 'cas_true', '\left \{1.23 \right \}', '', ""],
        ],
        [
            0 => '{1.23}',
            '.' => [null, 'php_true', '{dispdp(1.23,2)}', 'cas_true', '\left \{1.23 \right \}', '', ""],
            ',' => [null, 'php_false', '', '', '', 'forbiddenCharDecimal', ""],
        ],
        [
            0 => '{1;23}',
            '.' => [null, 'php_false', '', '', '', 'forbiddenChar_parserError', ""],
            ',' => [null, 'php_true', '{1,23}', 'cas_true', '\left \{1 , 23 \right \}', '', ""],
        ],
        [
            0 => '{1.2,3}',
            '.' => [null, 'php_true', '{dispdp(1.2,1),3}', 'cas_true', '\left \{1.2 , 3 \right \}', '', ""],
            ',' => [null, 'php_false', '', '', '', 'forbiddenCharDecimal', ""],
        ],
        [
            0 => '{1,2;3}',
            '.' => [null, 'php_false', '', '', '', 'forbiddenChar_parserError', ""],
            ',' => [null, 'php_true', '{dispdp(1.2,1),3}', 'cas_true', '\left \{1.2 , 3 \right \}', '', ""],
        ],
        [
            0 => '{1,2;3;4.1}',
            '.' => [null, 'php_false', '', '', '', 'forbiddenChar_parserError', ""],
            ',' => [null, 'php_false', '', '', '', 'forbiddenCharDecimal', ""],
        ],
    ];

    public static function get_raw_test_data() {
        return self::$rawdata;
    }

    public static function get_raw_test_data_units() {
        return self::$rawdataunits;
    }

    public static function get_raw_test_data_decimals() {
        return self::$rawdatadecimals;
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
        $test->decimals         = '.';

        $test->passed           = null;
        $test->errors           = null;
        $test->caserrors        = null;
        $test->casdisplay       = null;
        $test->casvalue         = null;
        $test->casnotes         = null;
        return $test;
    }

    public static function test_decimals_from_raw($data, $decimals) {

        $test = new stdClass();
        $test->rawstring        = $data[self::RAWSTRING];
        $test->phpvalid         = $data[$decimals][self::PHPVALID];
        $test->phpcasstring     = $data[$decimals][self::PHPCASSTRING];
        $test->casvalid         = $data[$decimals][self::CASVALID];
        $test->display          = $data[$decimals][self::DISPLAY];
        $test->notes            = $data[$decimals][self::NOTES];
        $test->ansnotes         = $data[$decimals][self::ANSNOTES];
        $test->validationmethod = 'typeless';
        $test->decimals         = '.';
        if ($decimals === self::CONTINENTIAL) {
            $test->decimals         = ',';
        }

        $test->passed           = null;
        $test->errors           = null;
        $test->caserrors        = null;
        $test->casdisplay       = null;
        $test->casvalue         = null;
        $test->casnotes         = null;
        return $test;
    }

    public static function get_all() {
        $tests = [];
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
        $filterstoapply = [];

        // The common insert stars rules, that will be forced
        // and if you do not allow inserttion of stars then it is invalid.
        $filterstoapply[] = '180_char_based_superscripts';

        $filterstoapply[] = '402_split_prefix_from_common_function_name';
        $filterstoapply[] = '403_split_at_number_letter_boundary';
        // Filter '404_split_at_number_letter_number_boundary' is not used by input base class.
        $filterstoapply[] = '406_split_implied_variable_names';

        $filterstoapply[] = '502_replace_pm';
        $filterstoapply[] = '504_insert_tuples_for_groups';
        $filterstoapply[] = '505_no_evaluation_groups';

        $filterstoapply[] = '910_inert_float_for_display';

        // We want to apply this as our "insert stars" but not spaces...
        $filterstoapply[] = '990_no_fixing_spaces';

        $secrules = new stack_cas_security();
        $secrules->set_allowedwords('dispdp,displaysci');
        $cs = stack_ast_container::make_from_student_source($test->rawstring, '', $secrules,
            $filterstoapply, [], 'Root', $test->decimals);
        $cs->set_cas_validation_context('ans1', true, '', $test->validationmethod, false, 0, '.');

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

            $session = new stack_cas_session2([$cs], $options, 0);
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
                $errors .= ' ' . stack_string('displaymismatch') . html_writer::tag('pre', s($test->display)) .
                    html_writer::tag('pre', s($casdisplay));
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

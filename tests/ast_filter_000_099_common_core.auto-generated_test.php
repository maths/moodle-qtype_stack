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

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../tests/fixtures/ast_filter_test_base.php');

// Auto-generated unit tests for AST-filter DO NOT EDIT!
/**
 * @group qtype_stack
 * @group qtype_stack_ast_filters
 */

class stack_ast_filter_auto_gen_000_099_common_core_testcase extends qtype_stack_ast_testcase {

    public function test_affected_no_units() {
        $this->security = new stack_cas_security(false);
        $this->filter = stack_parsing_rule_factory::get_filter_pipeline(array(), array(), true);

        $this->expect('(x+2)(x+3)',
                      '(x+2)*(x+3)',
                      array('missing_stars'),
                      true, false);

        $this->expect('-(1/512) + i(sqrt(3)/512)',
                      '-(1/512)+i*(sqrt(3)/512)',
                      array('missing_stars'),
                      true, false);

        $this->expect('0..1',
                      '0. . 1',
                      array('spuriousop'),
                      false, true);

        $this->expect('0.1..1.2',
                      '0.1 . .1 . 2',
                      array('spuriousop'),
                      false, true);

        $this->expect('0.1.1.2',
                      '0.1 . 1.2',
                      array('MatrixMultWithFloat'),
                      false, true);

        $this->expect('1<=x<y^2',
                      '1 <= x < y^2',
                      array('chained_inequalities'),
                      false, true);

        $this->expect('1<x<3',
                      '1 < x < 3',
                      array('chained_inequalities'),
                      false, true);

        $this->expect('2+log_x(1/(x+b))*x^2',
                      '2+lg(1/(x+b),x)*x^2',
                      array('logsubs'),
                      true, false);

        $this->expect('[1<x,1<y<3]',
                      '[1 < x,1 < y < 3]',
                      array('chained_inequalities'),
                      false, true);

        $this->expect('arcsin(x)',
                      'arcsin(x)',
                      array('triginv'),
                      false, true);

        $this->expect('cos(2x)(x+1)',
                      'cos(2*x)*(x+1)',
                      array('missing_stars'),
                      true, false);

        $this->expect('cos^2(x)',
                      'cos^2*(x)',
                      array('trigexp'),
                      false, true);

        $this->expect('f(x)(2)',
                      'f(x)*(2)',
                      array('missing_stars'),
                      true, false);

        $this->expect('i(1+i)',
                      'i*(1+i)',
                      array('missing_stars'),
                      true, false);

        $this->expect('i(4)',
                      'i*(4)',
                      array('missing_stars'),
                      true, false);

        $this->expect('log10(x)',
                      'lg(x,10)',
                      array('logsubs'),
                      true, false);

        $this->expect('log_10(x)',
                      'lg(x,10)',
                      array('logsubs'),
                      true, false);

        $this->expect('log_2(a)',
                      'lg(a,2)',
                      array('logsubs'),
                      true, false);

        $this->expect('log_a(b)*log_b(c)',
                      'lg(b,a)*lg(c,b)',
                      array('logsubs'),
                      true, false);

        $this->expect('log_x(1/(x+b))',
                      'lg(1/(x+b),x)',
                      array('logsubs'),
                      true, false);

        $this->expect('log_x:log_x(a)',
                      'log_x:lg(a,x)',
                      array('logsubs'),
                      true, false);

        $this->expect('sin x',
                      'sin*x',
                      array('trigspace'),
                      false, true);

        $this->expect('sin[2*x]',
                      'sin[2*x]',
                      array('trigparens'),
                      false, true);

        $this->expect('sin^-1(x)',
                      'sin^-1*(x)',
                      array('trigexp'),
                      false, true);

        $this->expect('rand(["+","-"])(x,y)',
                      'rand(["+","-"])*(x,y)',
                      array('missing_stars'),
                      true, false);

        $this->expect('rand(["sin","cos","system"])(x)',
                      'rand(["sin","cos","system"])*(x)',
                      array('missing_stars'),
                      true, false);

    }

    public function test_affected_units() {
        $this->security = new stack_cas_security(true);
        $this->filter = stack_parsing_rule_factory::get_filter_pipeline(array(), array(), true);

        $this->expect('(x+2)(x+3)',
                      '(x+2)*(x+3)',
                      array('missing_stars'),
                      true, false);

        $this->expect('-(1/512) + i(sqrt(3)/512)',
                      '-(1/512)+i*(sqrt(3)/512)',
                      array('missing_stars'),
                      true, false);

        $this->expect('0..1',
                      '0. . 1',
                      array('spuriousop'),
                      false, true);

        $this->expect('0.1..1.2',
                      '0.1 . .1 . 2',
                      array('spuriousop'),
                      false, true);

        $this->expect('0.1.1.2',
                      '0.1 . 1.2',
                      array('MatrixMultWithFloat'),
                      false, true);

        $this->expect('1<=x<y^2',
                      '1 <= x < y^2',
                      array('chained_inequalities'),
                      false, true);

        $this->expect('1<x<3',
                      '1 < x < 3',
                      array('chained_inequalities'),
                      false, true);

        $this->expect('2+log_x(1/(x+b))*x^2',
                      '2+lg(1/(x+b),x)*x^2',
                      array('logsubs'),
                      true, false);

        $this->expect('[1<x,1<y<3]',
                      '[1 < x,1 < y < 3]',
                      array('chained_inequalities'),
                      false, true);

        $this->expect('arcsin(x)',
                      'arcsin(x)',
                      array('triginv'),
                      false, true);

        $this->expect('cos(2x)(x+1)',
                      'cos(2*x)*(x+1)',
                      array('missing_stars'),
                      true, false);

        $this->expect('cos^2(x)',
                      'cos^2*(x)',
                      array('trigexp'),
                      false, true);

        $this->expect('f(x)(2)',
                      'f(x)*(2)',
                      array('missing_stars'),
                      true, false);

        $this->expect('i(1+i)',
                      'i*(1+i)',
                      array('missing_stars'),
                      true, false);

        $this->expect('i(4)',
                      'i*(4)',
                      array('missing_stars'),
                      true, false);

        $this->expect('log10(x)',
                      'lg(x,10)',
                      array('logsubs'),
                      true, false);

        $this->expect('log_10(x)',
                      'lg(x,10)',
                      array('logsubs'),
                      true, false);

        $this->expect('log_2(a)',
                      'lg(a,2)',
                      array('logsubs'),
                      true, false);

        $this->expect('log_a(b)*log_b(c)',
                      'lg(b,a)*lg(c,b)',
                      array('logsubs'),
                      true, false);

        $this->expect('log_x(1/(x+b))',
                      'lg(1/(x+b),x)',
                      array('logsubs'),
                      true, false);

        $this->expect('log_x:log_x(a)',
                      'log_x:lg(a,x)',
                      array('logsubs'),
                      true, false);

        $this->expect('sin x',
                      'sin*x',
                      array('trigspace'),
                      false, true);

        $this->expect('sin[2*x]',
                      'sin[2*x]',
                      array('trigparens'),
                      false, true);

        $this->expect('sin^-1(x)',
                      'sin^-1*(x)',
                      array('trigexp'),
                      false, true);

        $this->expect('rand(["+","-"])(x,y)',
                      'rand(["+","-"])*(x,y)',
                      array('missing_stars'),
                      true, false);

        $this->expect('rand(["sin","cos","system"])(x)',
                      'rand(["sin","cos","system"])*(x)',
                      array('missing_stars'),
                      true, false);

    }

    public function test_non_affected_units() {
        $this->security = new stack_cas_security(true);
        $this->filter = stack_parsing_rule_factory::get_filter_pipeline(array(), array(), true);

        $this->expect('"+"(a,b)',
                      '"+"(a,b)',
                      array(),
                      true, false);

        $this->expect('"1+1"',
                      '"1+1"',
                      array(),
                      true, false);

        $this->expect('"Hello world"',
                      '"Hello world"',
                      array(),
                      true, false);

        $this->expect('%e^x',
                      '%e^x',
                      array(),
                      true, false);

        $this->expect('2pi r^2',
                      '2*pi*r^2',
                      array(),
                      true, false);

        $this->expect('2pir^2',
                      '2*pir^2',
                      array(),
                      true, false);

        $this->expect("'diff(x,y)",
                      "'diff(x,y)",
                      array(),
                      true, false);

        $this->expect("'int(x,y)",
                      "'int(x,y)",
                      array(),
                      true, false);

        $this->expect('(()x)',
                      '(()*x)',
                      array(),
                      true, false);

        $this->expect('((x))',
                      '((x))',
                      array(),
                      true, false);

        $this->expect('()x',
                      '()*x',
                      array(),
                      true, false);

        $this->expect('(+1)',
                      '(+1)',
                      array(),
                      true, false);

        $this->expect('(-1)',
                      '(-1)',
                      array(),
                      true, false);

        $this->expect('(-b+-sqrt(b^2))/(2*a)',
                      '(-b+-sqrt(b^2))/(2*a)',
                      array(),
                      true, false);

        $this->expect('(-x)*y',
                      '(-x)*y',
                      array(),
                      true, false);

        $this->expect('(1+i)*x',
                      '(1+i)*x',
                      array(),
                      true, false);

        $this->expect('(1+i)+x',
                      '(1+i)+x',
                      array(),
                      true, false);

        $this->expect('(a,b,c)',
                      '(a,b,c)',
                      array(),
                      true, false);

        $this->expect('(a-b)-c',
                      '(a-b)-c',
                      array(),
                      true, false);

        $this->expect('(x)',
                      '(x)',
                      array(),
                      true, false);

        $this->expect('(x*y)*z',
                      '(x*y)*z',
                      array(),
                      true, false);

        $this->expect('(x+2)3',
                      '(x+2)*3',
                      array(),
                      true, false);

        $this->expect('(x+2)y',
                      '(x+2)*y',
                      array(),
                      true, false);

        $this->expect('(x+y)+z',
                      '(x+y)+z',
                      array(),
                      true, false);

        $this->expect('(x+y)^z',
                      '(x+y)^z',
                      array(),
                      true, false);

        $this->expect('(x-y)+z',
                      '(x-y)+z',
                      array(),
                      true, false);

        $this->expect('(x/y)/z',
                      '(x/y)/z',
                      array(),
                      true, false);

        $this->expect('+-1',
                      '+-1',
                      array(),
                      true, false);

        $this->expect('+0.2',
                      '+0.2',
                      array(),
                      true, false);

        $this->expect('+1',
                      '+1',
                      array(),
                      true, false);

        $this->expect('+e',
                      '+e',
                      array(),
                      true, false);

        $this->expect('+i',
                      '+i',
                      array(),
                      true, false);

        $this->expect('+pi',
                      '+pi',
                      array(),
                      true, false);

        $this->expect('+x',
                      '+x',
                      array(),
                      true, false);

        $this->expect('-1',
                      '-1',
                      array(),
                      true, false);

        $this->expect('-1234',
                      '-1234',
                      array(),
                      true, false);

        $this->expect('-0.2',
                      '-0.2',
                      array(),
                      true, false);

        $this->expect('-10/-1',
                      '-10/-1',
                      array(),
                      true, false);

        $this->expect('-3(x+1)',
                      '-3*(x+1)',
                      array(),
                      true, false);

        $this->expect('-3+i',
                      '-3+i',
                      array(),
                      true, false);

        $this->expect('-3x(1+x)',
                      '-3*x(1+x)',
                      array(),
                      true, false);

        $this->expect('-b(5-b)',
                      '-b(5-b)',
                      array(),
                      true, false);

        $this->expect('-e',
                      '-e',
                      array(),
                      true, false);

        $this->expect('-i',
                      '-i',
                      array(),
                      true, false);

        $this->expect('-pi',
                      '-pi',
                      array(),
                      true, false);

        $this->expect('-x',
                      '-x',
                      array(),
                      true, false);

        $this->expect('-x(1+x)',
                      '-x(1+x)',
                      array(),
                      true, false);

        $this->expect('-x[3]',
                      '-x[3]',
                      array(),
                      true, false);

        $this->expect('.1',
                      '.1',
                      array(),
                      true, false);

        $this->expect('-0.2433 + 0.1111',
                      '-0.2433+0.1111',
                      array(),
                      true, false);

        $this->expect('-0.2433e23 + 0.1111e-45 * 0.23e12 / -0.11e-11',
                      '-0.2433E23+0.1111E-45*0.23E12/-0.11E-11',
                      array(),
                      true, false);

        $this->expect('-35.3 * 10^23',
                      '-35.3*10^23',
                      array(),
                      true, false);

        $this->expect('0.1. 1.2',
                      '0.1 . 1.2',
                      array(),
                      true, false);

        $this->expect('1',
                      '1',
                      array(),
                      true, false);

        $this->expect('1234',
                      '1234',
                      array(),
                      true, false);

        $this->expect('1 x',
                      '1*x',
                      array(),
                      true, false);

        $this->expect('1+2i',
                      '1+2*i',
                      array(),
                      true, false);

        $this->expect('1+i',
                      '1+i',
                      array(),
                      true, false);

        $this->expect('1-x(1+x)',
                      '1-x(1+x)',
                      array(),
                      true, false);

        $this->expect('1/0',
                      '1/0',
                      array(),
                      true, false);

        $this->expect('1/2',
                      '1/2',
                      array(),
                      true, false);

        $this->expect('1/sin(+x)',
                      '1/sin(+x)',
                      array(),
                      true, false);

        $this->expect('1E+3',
                      '1E+3',
                      array(),
                      true, false);

        $this->expect('1E3',
                      '1E3',
                      array(),
                      true, false);

        $this->expect('1 E 3',
                      '1*E*3',
                      array(),
                      true, false);

        $this->expect('23.2*x*10^5',
                      '23.2*x*10^5',
                      array(),
                      true, false);

        $this->expect('23.2 x 10^5',
                      '23.2*x*10^5',
                      array(),
                      true, false);

        $this->expect('23.2x10^5',
                      '23.2*x10^5',
                      array(),
                      true, false);

        $this->expect('23.2x 10^5',
                      '23.2*x*10^5',
                      array(),
                      true, false);

        $this->expect('23.2 x10^5',
                      '23.2*x10^5',
                      array(),
                      true, false);

        $this->expect('9.81x10^2*m/s',
                      '9.81*x10^2*m/s',
                      array(),
                      true, false);

        $this->expect('9.81x*10^2*m/s',
                      '9.81*x*10^2*m/s',
                      array(),
                      true, false);

        $this->expect('1x',
                      '1*x',
                      array(),
                      true, false);

        $this->expect('2*e',
                      '2*e',
                      array(),
                      true, false);

        $this->expect('2*i',
                      '2*i',
                      array(),
                      true, false);

        $this->expect('2*i^3',
                      '2*i^3',
                      array(),
                      true, false);

        $this->expect('2*pi',
                      '2*pi',
                      array(),
                      true, false);

        $this->expect('2+3(x+1)',
                      '2+3*(x+1)',
                      array(),
                      true, false);

        $this->expect('2/4',
                      '2/4',
                      array(),
                      true, false);

        $this->expect('2^y*x',
                      '2^y*x',
                      array(),
                      true, false);

        $this->expect('3(x+1)',
                      '3*(x+1)',
                      array(),
                      true, false);

        $this->expect('3-i',
                      '3-i',
                      array(),
                      true, false);

        $this->expect('3 5',
                      '3*5',
                      array(),
                      true, false);

        $this->expect('3.14 5',
                      '3.14*5',
                      array(),
                      true, false);

        $this->expect('3 5.2789',
                      '3*5.2789',
                      array(),
                      true, false);

        $this->expect('3.14 5.2789',
                      '3.14*5.2789',
                      array(),
                      true, false);

        $this->expect('33 578 32',
                      '33*578*32',
                      array(),
                      true, false);

        $this->expect('9 8 7.6',
                      '9*8*7.6',
                      array(),
                      true, false);

        $this->expect('9 8.5 7.6',
                      '9*8.5*7.6',
                      array(),
                      true, false);

        $this->expect('3b+5/a(x)',
                      '3*b+5/a(x)',
                      array(),
                      true, false);

        $this->expect('3beta_47',
                      '3*beta_47',
                      array(),
                      true, false);

        $this->expect('3e-2',
                      '3E-2',
                      array(),
                      true, false);

        $this->expect('3e2',
                      '3E2',
                      array(),
                      true, false);

        $this->expect('3E2',
                      '3E2',
                      array(),
                      true, false);

        $this->expect('7x(2+1)',
                      '7*x(2+1)',
                      array(),
                      true, false);

        $this->expect('Bgcd(3,2)',
                      'Bgcd(3,2)',
                      array(),
                      true, false);

        $this->expect('In(x)',
                      'In(x)',
                      array(),
                      true, false);

        $this->expect('Sin(x)',
                      'Sin(x)',
                      array(),
                      true, false);

        $this->expect('3.75*Btu',
                      '3.75*Btu',
                      array(),
                      true, false);

        $this->expect('X',
                      'X',
                      array(),
                      true, false);

        $this->expect('["a"]',
                      '["a"]',
                      array(),
                      true, false);

        $this->expect('[+1,+2]',
                      '[+1,+2]',
                      array(),
                      true, false);

        $this->expect('[-1,-2]',
                      '[-1,-2]',
                      array(),
                      true, false);

        $this->expect('[1 < x,y < 1 or y > 7]',
                      '[1 < x,y < 1 or y > 7]',
                      array(),
                      true, false);

        $this->expect('[1,+2]',
                      '[1,+2]',
                      array(),
                      true, false);

        $this->expect('[1,-2]',
                      '[1,-2]',
                      array(),
                      true, false);

        $this->expect('[1,2,3.4]',
                      '[1,2,3.4]',
                      array(),
                      true, false);

        $this->expect('[1,true,"a"]',
                      '[1,true,"a"]',
                      array(),
                      true, false);

        $this->expect('[1<x,x<3]',
                      '[1 < x,x < 3]',
                      array(),
                      true, false);

        $this->expect('[1]',
                      '[1]',
                      array(),
                      true, false);

        $this->expect('[[1,2],[3,4]]',
                      '[[1,2],[3,4]]',
                      array(),
                      true, false);

        $this->expect('[]',
                      '[]',
                      array(),
                      true, false);

        $this->expect('[x, y, z ]',
                      '[x,y,z]',
                      array(),
                      true, false);

        $this->expect('a ** b',
                      'a**b',
                      array(),
                      true, false);

        $this->expect('a +++ b',
                      'a+++b',
                      array(),
                      true, false);

        $this->expect('a --- b',
                      'a---b',
                      array(),
                      true, false);

        $this->expect('a(x)',
                      'a(x)',
                      array(),
                      true, false);

        $this->expect('a++b',
                      'a++b',
                      array(),
                      true, false);

        $this->expect('a+-b',
                      'a+-b',
                      array(),
                      true, false);

        $this->expect('a,b,c',
                      'a,b=true,c=true',
                      array(),
                      true, false);

        $this->expect('a-(b-c)',
                      'a-(b-c)',
                      array(),
                      true, false);

        $this->expect('a-+b',
                      'a-+b',
                      array(),
                      true, false);

        $this->expect('a/(a(x+1)+2)',
                      'a/(a(x+1)+2)',
                      array(),
                      true, false);

        $this->expect('a/b/c',
                      'a/b/c',
                      array(),
                      true, false);

        $this->expect('a1',
                      'a1',
                      array(),
                      true, false);

        $this->expect('a9b',
                      'a9b',
                      array(),
                      true, false);

        $this->expect('ab98cd',
                      'ab98cd',
                      array(),
                      true, false);

        $this->expect('aXy1',
                      'aXy1',
                      array(),
                      true, false);

        $this->expect('a[1,2]',
                      'a[1,2]',
                      array(),
                      true, false);

        $this->expect('a[2]',
                      'a[2]',
                      array(),
                      true, false);

        $this->expect('a[n+1]',
                      'a[n+1]',
                      array(),
                      true, false);

        $this->expect('a^-b',
                      'a^-b',
                      array(),
                      true, false);

        $this->expect('a^b',
                      'a^b',
                      array(),
                      true, false);

        $this->expect('a_b',
                      'a_b',
                      array(),
                      true, false);

        $this->expect('abs(13)',
                      'abs(13)',
                      array(),
                      true, false);

        $this->expect('abs(x)',
                      'abs(x)',
                      array(),
                      true, false);

        $this->expect('alpha',
                      'alpha',
                      array(),
                      true, false);

        $this->expect('asin(x)',
                      'asin(x)',
                      array(),
                      true, false);

        $this->expect('asinh(x)',
                      'asinh(x)',
                      array(),
                      true, false);

        $this->expect('b(b+1)',
                      'b(b+1)',
                      array(),
                      true, false);

        $this->expect('b/a(x)',
                      'b/a(x)',
                      array(),
                      true, false);

        $this->expect('beta',
                      'beta',
                      array(),
                      true, false);

        $this->expect('beta_47',
                      'beta_47',
                      array(),
                      true, false);

        $this->expect('bsin(t)',
                      'bsin(t)',
                      array(),
                      true, false);

        $this->expect('ceiling(x)',
                      'ceiling(x)',
                      array(),
                      true, false);

        $this->expect('chi',
                      'chi',
                      array(),
                      true, false);

        $this->expect('comb(x,y)',
                      'comb(x,y)',
                      array(),
                      true, false);

        $this->expect('cos(x)',
                      'cos(x)',
                      array(),
                      true, false);

        $this->expect('cosec(x)',
                      'cosec(x)',
                      array(),
                      true, false);

        $this->expect('cosech(x)',
                      'cosech(x)',
                      array(),
                      true, false);

        $this->expect('cosh(x)',
                      'cosh(x)',
                      array(),
                      true, false);

        $this->expect('cot(x)',
                      'cot(x)',
                      array(),
                      true, false);

        $this->expect('coth(x)',
                      'coth(x)',
                      array(),
                      true, false);

        $this->expect('csc(6*x)^2*(7*sin(6*x)*cos(7*x)-6*cos(6*x)*sin(7*x))',
                      'csc(6*x)^2*(7*sin(6*x)*cos(7*x)-6*cos(6*x)*sin(7*x))',
                      array(),
                      true, false);

        $this->expect('csc(x)',
                      'csc(x)',
                      array(),
                      true, false);

        $this->expect('delta',
                      'delta',
                      array(),
                      true, false);

        $this->expect('diff(sin(x))',
                      'diff(sin(x))',
                      array(),
                      true, false);

        $this->expect('diff(sin(x),x)',
                      'diff(sin(x),x)',
                      array(),
                      true, false);

        $this->expect('diff(x,y)',
                      'diff(x,y)',
                      array(),
                      true, false);

        $this->expect('dosomething(x,y,z)',
                      'dosomething(x,y,z)',
                      array(),
                      true, false);

        $this->expect('e',
                      'e',
                      array(),
                      true, false);

        $this->expect('e*2',
                      'e*2',
                      array(),
                      true, false);

        $this->expect('e^x',
                      'e^x',
                      array(),
                      true, false);

        $this->expect('epsilon',
                      'epsilon',
                      array(),
                      true, false);

        $this->expect('eta',
                      'eta',
                      array(),
                      true, false);

        $this->expect('exp(x)',
                      'exp(x)',
                      array(),
                      true, false);

        $this->expect('f(x)',
                      'f(x)',
                      array(),
                      true, false);

        $this->expect('fact(13)',
                      'fact(13)',
                      array(),
                      true, false);

        $this->expect('false',
                      'false',
                      array(),
                      true, false);

        $this->expect('floor(x)',
                      'floor(x)',
                      array(),
                      true, false);

        $this->expect('gamma',
                      'gamma',
                      array(),
                      true, false);

        $this->expect('gcd(x,y)',
                      'gcd(x,y)',
                      array(),
                      true, false);

        $this->expect('gcf(x,y)',
                      'gcf(x,y)',
                      array(),
                      true, false);

        $this->expect('i',
                      'i',
                      array(),
                      true, false);

        $this->expect('i*2',
                      'i*2',
                      array(),
                      true, false);

        $this->expect('inf',
                      'inf',
                      array(),
                      true, false);

        $this->expect('int(sin(x))',
                      'int(sin(x))',
                      array(),
                      true, false);

        $this->expect('int(x,y)',
                      'int(x,y)',
                      array(),
                      true, false);

        $this->expect('iota',
                      'iota',
                      array(),
                      true, false);

        $this->expect('j',
                      'j',
                      array(),
                      true, false);

        $this->expect('kappa',
                      'kappa',
                      array(),
                      true, false);

        $this->expect('lambda',
                      'lambda',
                      array(),
                      true, false);

        $this->expect('len(x)',
                      'len(x)',
                      array(),
                      true, false);

        $this->expect('length(x)',
                      'length(x)',
                      array(),
                      true, false);

        $this->expect('lg(10^3)',
                      'lg(10^3)',
                      array(),
                      true, false);

        $this->expect('lg(x)',
                      'lg(x)',
                      array(),
                      true, false);

        $this->expect('lg(x,a)',
                      'lg(x,a)',
                      array(),
                      true, false);

        $this->expect('limit(y,x,3)',
                      'limit(y,x,3)',
                      array(),
                      true, false);

        $this->expect('ln(x)',
                      'ln(x)',
                      array(),
                      true, false);

        $this->expect('ln*x',
                      'ln*x',
                      array(),
                      true, false);

        $this->expect('log(2x)/x+1/2',
                      'log(2*x)/x+1/2',
                      array(),
                      true, false);

        $this->expect('log(x)',
                      'log(x)',
                      array(),
                      true, false);

        $this->expect('matrix([a,b],[c,d])',
                      'matrix([a,b],[c,d])',
                      array(),
                      true, false);

        $this->expect('mod(x,y)',
                      'mod(x,y)',
                      array(),
                      true, false);

        $this->expect('mu',
                      'mu',
                      array(),
                      true, false);

        $this->expect('not x',
                      'not x',
                      array(),
                      true, false);

        $this->expect('nu',
                      'nu',
                      array(),
                      true, false);

        $this->expect('omega',
                      'omega',
                      array(),
                      true, false);

        $this->expect('omicron',
                      'omicron',
                      array(),
                      true, false);

        $this->expect('p=?*s',
                      'p = QMCHAR*s',
                      array(),
                      true, false);

        $this->expect('partialdiff(x,y,1)',
                      'partialdiff(x,y,1)',
                      array(),
                      true, false);

        $this->expect('perm(x,y)',
                      'perm(x,y)',
                      array(),
                      true, false);

        $this->expect('phi',
                      'phi',
                      array(),
                      true, false);

        $this->expect('pi',
                      'pi',
                      array(),
                      true, false);

        $this->expect('pi*2',
                      'pi*2',
                      array(),
                      true, false);

        $this->expect('plot(x^2,[x,-1,1])',
                      'plot(x^2,[x,-1,1])',
                      array(),
                      true, false);

        $this->expect('plot2d(x^2,[x,-1,1])',
                      'plot2d(x^2,[x,-1,1])',
                      array(),
                      true, false);

        $this->expect('product(cos(k*x),k,1,3)',
                      'product(cos(k*x),k,1,3)',
                      array(),
                      true, false);

        $this->expect('psi',
                      'psi',
                      array(),
                      true, false);

        $this->expect('rho',
                      'rho',
                      array(),
                      true, false);

        $this->expect('rho*z*V/(4*pi*epsilon[0]*(R^2+z^2)^(3/2))',
                      'rho*z*V/(4*pi*epsilon[0]*(R^2+z^2)^(3/2))',
                      array(),
                      true, false);

        $this->expect('root(2,-3)',
                      'root(2,-3)',
                      array(),
                      true, false);

        $this->expect('root(x)',
                      'root(x)',
                      array(),
                      true, false);

        $this->expect('root(x,3)',
                      'root(x,3)',
                      array(),
                      true, false);

        $this->expect('sec(x)',
                      'sec(x)',
                      array(),
                      true, false);

        $this->expect('sech(x)',
                      'sech(x)',
                      array(),
                      true, false);

        $this->expect('set(x, y, z)',
                      'set(x,y,z)',
                      array(),
                      true, false);

        $this->expect('sgn(x)',
                      'sgn(x)',
                      array(),
                      true, false);

        $this->expect('sigma',
                      'sigma',
                      array(),
                      true, false);

        $this->expect('sign(x)',
                      'sign(x)',
                      array(),
                      true, false);

        $this->expect('sim(x)',
                      'sim(x)',
                      array(),
                      true, false);

        $this->expect('sin',
                      'sin',
                      array(),
                      true, false);

        $this->expect('sin(x)',
                      'sin(x)',
                      array(),
                      true, false);

        $this->expect('sin*2*x',
                      'sin*2*x',
                      array(),
                      true, false);

        $this->expect('sinh(x)',
                      'sinh(x)',
                      array(),
                      true, false);

        $this->expect('sqr(x)',
                      'sqr(x)',
                      array(),
                      true, false);

        $this->expect('sqrt(+x)',
                      'sqrt(+x)',
                      array(),
                      true, false);

        $this->expect('sqrt(x)',
                      'sqrt(x)',
                      array(),
                      true, false);

        $this->expect('stackvector(a)',
                      'stackvector(a)',
                      array(),
                      true, false);

        $this->expect('sum(k^n,n,0,3)',
                      'sum(k^n,n,0,3)',
                      array(),
                      true, false);

        $this->expect('switch(x,a,y,b,c)',
                      'switch(x,a,y,b,c)',
                      array(),
                      true, false);

        $this->expect('tan(x)',
                      'tan(x)',
                      array(),
                      true, false);

        $this->expect('tanh(x)',
                      'tanh(x)',
                      array(),
                      true, false);

        $this->expect('tau',
                      'tau',
                      array(),
                      true, false);

        $this->expect('theta',
                      'theta',
                      array(),
                      true, false);

        $this->expect('true',
                      'true',
                      array(),
                      true, false);

        $this->expect('upsilon',
                      'upsilon',
                      array(),
                      true, false);

        $this->expect('x',
                      'x',
                      array(),
                      true, false);

        $this->expect('x * y',
                      'x*y',
                      array(),
                      true, false);

        $this->expect('x + 1',
                      'x+1',
                      array(),
                      true, false);

        $this->expect('x + y',
                      'x+y',
                      array(),
                      true, false);

        $this->expect('x - y',
                      'x-y',
                      array(),
                      true, false);

        $this->expect('x / y',
                      'x/y',
                      array(),
                      true, false);

        $this->expect('x < y',
                      'x < y',
                      array(),
                      true, false);

        $this->expect('x <= y',
                      'x <= y',
                      array(),
                      true, false);

        $this->expect('x = y',
                      'x = y',
                      array(),
                      true, false);

        $this->expect('x > y',
                      'x > y',
                      array(),
                      true, false);

        $this->expect('x >= y',
                      'x >= y',
                      array(),
                      true, false);

        $this->expect('x ^ y',
                      'x^y',
                      array(),
                      true, false);

        $this->expect('x and',
                      'x*and',
                      array(),
                      true, false);

        $this->expect('x and y',
                      'x and y',
                      array(),
                      true, false);

        $this->expect('x divides y',
                      'x*divides*y',
                      array(),
                      true, false);

        $this->expect('x or y',
                      'x or y',
                      array(),
                      true, false);

        $this->expect('x xor y',
                      'x xor y',
                      array(),
                      true, false);

        $this->expect('x y',
                      'x*y',
                      array(),
                      true, false);

        $this->expect('x!',
                      'x!',
                      array(),
                      true, false);

        $this->expect('x()',
                      'x()',
                      array(),
                      true, false);

        $this->expect('x(2+1)',
                      'x(2+1)',
                      array(),
                      true, false);

        $this->expect('x(sin(t)+1)',
                      'x(sin(t)+1)',
                      array(),
                      true, false);

        $this->expect('x(t+1)',
                      'x(t+1)',
                      array(),
                      true, false);

        $this->expect('x(x+1)',
                      'x(x+1)',
                      array(),
                      true, false);

        $this->expect('x*(-y)',
                      'x*(-y)',
                      array(),
                      true, false);

        $this->expect('x*(y*z)',
                      'x*(y*z)',
                      array(),
                      true, false);

        $this->expect('x*2^y',
                      'x*2^y',
                      array(),
                      true, false);

        $this->expect('x*divides*y',
                      'x*divides*y',
                      array(),
                      true, false);

        $this->expect('x*i^3',
                      'x*i^3',
                      array(),
                      true, false);

        $this->expect('x*y*z',
                      'x*y*z',
                      array(),
                      true, false);

        $this->expect('x*y^z',
                      'x*y^z',
                      array(),
                      true, false);

        $this->expect('x+ 1',
                      'x+1',
                      array(),
                      true, false);

        $this->expect('x+(y+z)',
                      'x+(y+z)',
                      array(),
                      true, false);

        $this->expect('x+(y^z)',
                      'x+(y^z)',
                      array(),
                      true, false);

        $this->expect('x+1',
                      'x+1',
                      array(),
                      true, false);

        $this->expect('x+y+z',
                      'x+y+z',
                      array(),
                      true, false);

        $this->expect('x-(y+z)',
                      'x-(y+z)',
                      array(),
                      true, false);

        $this->expect('x/(y/z)',
                      'x/(y/z)',
                      array(),
                      true, false);

        $this->expect('x/y/z',
                      'x/y/z',
                      array(),
                      true, false);

        $this->expect('x1',
                      'x1',
                      array(),
                      true, false);

        $this->expect('x<1 and x>1',
                      'x < 1 and x > 1',
                      array(),
                      true, false);

        $this->expect('x=+-sqrt(2)',
                      'x = +-sqrt(2)',
                      array(),
                      true, false);

        $this->expect('x=1 or 2',
                      'x = 1 or 2',
                      array(),
                      true, false);

        $this->expect('x=1 or 2 or 3',
                      'x = 1 or 2 or 3',
                      array(),
                      true, false);

        $this->expect('x=1 or x=2',
                      'x = 1 or x = 2',
                      array(),
                      true, false);

        $this->expect('x>1 or (x<1 and t<sin(x))',
                      'x > 1 or (x < 1 and t < sin(x))',
                      array(),
                      true, false);

        $this->expect('x^(-(y+z))',
                      'x^(-(y+z))',
                      array(),
                      true, false);

        $this->expect('x^(-y)',
                      'x^(-y)',
                      array(),
                      true, false);

        $this->expect('x^(y+z)',
                      'x^(y+z)',
                      array(),
                      true, false);

        $this->expect('x^(y/z)',
                      'x^(y/z)',
                      array(),
                      true, false);

        $this->expect('x^-1',
                      'x^-1',
                      array(),
                      true, false);

        $this->expect('x^-y',
                      'x^-y',
                      array(),
                      true, false);

        $this->expect('x^7/7-2*x^6/3-4*x^3/3',
                      'x^7/7-2*x^6/3-4*x^3/3',
                      array(),
                      true, false);

        $this->expect('x^f(x)',
                      'x^f(x)',
                      array(),
                      true, false);

        $this->expect('x^y',
                      'x^y',
                      array(),
                      true, false);

        $this->expect('x^y^z',
                      'x^y^z',
                      array(),
                      true, false);

        $this->expect('x_1',
                      'x_1',
                      array(),
                      true, false);

        $this->expect('x_y',
                      'x_y',
                      array(),
                      true, false);

        $this->expect('xy_zw',
                      'xy_zw',
                      array(),
                      true, false);

        $this->expect('xy_12',
                      'xy_12',
                      array(),
                      true, false);

        $this->expect('xi',
                      'xi',
                      array(),
                      true, false);

        $this->expect('xsin(1)',
                      'xsin(1)',
                      array(),
                      true, false);

        $this->expect('xy',
                      'xy',
                      array(),
                      true, false);

        $this->expect('y^2-2*y-0.5',
                      'y^2-2*y-0.5',
                      array(),
                      true, false);

        $this->expect('y^2-2*y-8',
                      'y^2-2*y-8',
                      array(),
                      true, false);

        $this->expect('y^3-2*y^2-8*y',
                      'y^3-2*y^2-8*y',
                      array(),
                      true, false);

        $this->expect('y^z * x',
                      'y^z*x',
                      array(),
                      true, false);

        $this->expect('ycos(2)',
                      'ycos(2)',
                      array(),
                      true, false);

        $this->expect('zeta',
                      'zeta',
                      array(),
                      true, false);

        $this->expect('{1,2,3.4}',
                      '{1,2,3.4}',
                      array(),
                      true, false);

        $this->expect('{1}',
                      '{1}',
                      array(),
                      true, false);

        $this->expect('{x, y, z }',
                      '{x,y,z}',
                      array(),
                      true, false);

        $this->expect('{}',
                      '{}',
                      array(),
                      true, false);

        $this->expect('|x|',
                      'abs(x)',
                      array(),
                      true, false);

        $this->expect('1.2*m**2',
                      '1.2*m**2',
                      array(),
                      true, false);

        $this->expect('/* Comment */x+1',
                      '/* Comment */x+1',
                      array(),
                      true, false);

        $this->expect('/* Comment **/x+1',
                      '/* Comment **/x+1',
                      array(),
                      true, false);

        $this->expect('/** Comment */x+1',
                      '/** Comment */x+1',
                      array(),
                      true, false);

        $this->expect('/** Comment **/x+1',
                      '/** Comment **/x+1',
                      array(),
                      true, false);

        $this->expect('/*@ Comment @*/x+1',
                      '/*@ Comment @*/x+1',
                      array(),
                      true, false);

        $this->expect('"A string that needs sanitising <script>bad stuff</script>."',
                      '"A string that needs sanitising <script>bad stuff</script>."',
                      array(),
                      true, false);

    }

    public function test_non_affected_no_units() {
        $this->security = new stack_cas_security(false);
        $this->filter = stack_parsing_rule_factory::get_filter_pipeline(array(), array(), true);

        $this->expect('"+"(a,b)',
                      '"+"(a,b)',
                      array(),
                      true, false);

        $this->expect('"1+1"',
                      '"1+1"',
                      array(),
                      true, false);

        $this->expect('"Hello world"',
                      '"Hello world"',
                      array(),
                      true, false);

        $this->expect('%e^x',
                      '%e^x',
                      array(),
                      true, false);

        $this->expect('2pi r^2',
                      '2*pi*r^2',
                      array(),
                      true, false);

        $this->expect('2pir^2',
                      '2*pir^2',
                      array(),
                      true, false);

        $this->expect("'diff(x,y)",
                      "'diff(x,y)",
                      array(),
                      true, false);

        $this->expect("'int(x,y)",
                      "'int(x,y)",
                      array(),
                      true, false);

        $this->expect('(()x)',
                      '(()*x)',
                      array(),
                      true, false);

        $this->expect('((x))',
                      '((x))',
                      array(),
                      true, false);

        $this->expect('()x',
                      '()*x',
                      array(),
                      true, false);

        $this->expect('(+1)',
                      '(+1)',
                      array(),
                      true, false);

        $this->expect('(-1)',
                      '(-1)',
                      array(),
                      true, false);

        $this->expect('(-b+-sqrt(b^2))/(2*a)',
                      '(-b+-sqrt(b^2))/(2*a)',
                      array(),
                      true, false);

        $this->expect('(-x)*y',
                      '(-x)*y',
                      array(),
                      true, false);

        $this->expect('(1+i)*x',
                      '(1+i)*x',
                      array(),
                      true, false);

        $this->expect('(1+i)+x',
                      '(1+i)+x',
                      array(),
                      true, false);

        $this->expect('(a,b,c)',
                      '(a,b,c)',
                      array(),
                      true, false);

        $this->expect('(a-b)-c',
                      '(a-b)-c',
                      array(),
                      true, false);

        $this->expect('(x)',
                      '(x)',
                      array(),
                      true, false);

        $this->expect('(x*y)*z',
                      '(x*y)*z',
                      array(),
                      true, false);

        $this->expect('(x+2)3',
                      '(x+2)*3',
                      array(),
                      true, false);

        $this->expect('(x+2)y',
                      '(x+2)*y',
                      array(),
                      true, false);

        $this->expect('(x+y)+z',
                      '(x+y)+z',
                      array(),
                      true, false);

        $this->expect('(x+y)^z',
                      '(x+y)^z',
                      array(),
                      true, false);

        $this->expect('(x-y)+z',
                      '(x-y)+z',
                      array(),
                      true, false);

        $this->expect('(x/y)/z',
                      '(x/y)/z',
                      array(),
                      true, false);

        $this->expect('+-1',
                      '+-1',
                      array(),
                      true, false);

        $this->expect('+0.2',
                      '+0.2',
                      array(),
                      true, false);

        $this->expect('+1',
                      '+1',
                      array(),
                      true, false);

        $this->expect('+e',
                      '+e',
                      array(),
                      true, false);

        $this->expect('+i',
                      '+i',
                      array(),
                      true, false);

        $this->expect('+pi',
                      '+pi',
                      array(),
                      true, false);

        $this->expect('+x',
                      '+x',
                      array(),
                      true, false);

        $this->expect('-1',
                      '-1',
                      array(),
                      true, false);

        $this->expect('-1234',
                      '-1234',
                      array(),
                      true, false);

        $this->expect('-0.2',
                      '-0.2',
                      array(),
                      true, false);

        $this->expect('-10/-1',
                      '-10/-1',
                      array(),
                      true, false);

        $this->expect('-3(x+1)',
                      '-3*(x+1)',
                      array(),
                      true, false);

        $this->expect('-3+i',
                      '-3+i',
                      array(),
                      true, false);

        $this->expect('-3x(1+x)',
                      '-3*x(1+x)',
                      array(),
                      true, false);

        $this->expect('-b(5-b)',
                      '-b(5-b)',
                      array(),
                      true, false);

        $this->expect('-e',
                      '-e',
                      array(),
                      true, false);

        $this->expect('-i',
                      '-i',
                      array(),
                      true, false);

        $this->expect('-pi',
                      '-pi',
                      array(),
                      true, false);

        $this->expect('-x',
                      '-x',
                      array(),
                      true, false);

        $this->expect('-x(1+x)',
                      '-x(1+x)',
                      array(),
                      true, false);

        $this->expect('-x[3]',
                      '-x[3]',
                      array(),
                      true, false);

        $this->expect('.1',
                      '.1',
                      array(),
                      true, false);

        $this->expect('-0.2433 + 0.1111',
                      '-0.2433+0.1111',
                      array(),
                      true, false);

        $this->expect('-0.2433e23 + 0.1111e-45 * 0.23e12 / -0.11e-11',
                      '-0.2433E23+0.1111E-45*0.23E12/-0.11E-11',
                      array(),
                      true, false);

        $this->expect('-35.3 * 10^23',
                      '-35.3*10^23',
                      array(),
                      true, false);

        $this->expect('0.1. 1.2',
                      '0.1 . 1.2',
                      array(),
                      true, false);

        $this->expect('1',
                      '1',
                      array(),
                      true, false);

        $this->expect('1234',
                      '1234',
                      array(),
                      true, false);

        $this->expect('1 x',
                      '1*x',
                      array(),
                      true, false);

        $this->expect('1+2i',
                      '1+2*i',
                      array(),
                      true, false);

        $this->expect('1+i',
                      '1+i',
                      array(),
                      true, false);

        $this->expect('1-x(1+x)',
                      '1-x(1+x)',
                      array(),
                      true, false);

        $this->expect('1/0',
                      '1/0',
                      array(),
                      true, false);

        $this->expect('1/2',
                      '1/2',
                      array(),
                      true, false);

        $this->expect('1/sin(+x)',
                      '1/sin(+x)',
                      array(),
                      true, false);

        $this->expect('1E+3',
                      '1E+3',
                      array(),
                      true, false);

        $this->expect('1E3',
                      '1E3',
                      array(),
                      true, false);

        $this->expect('1 E 3',
                      '1*E*3',
                      array(),
                      true, false);

        $this->expect('23.2*x*10^5',
                      '23.2*x*10^5',
                      array(),
                      true, false);

        $this->expect('23.2 x 10^5',
                      '23.2*x*10^5',
                      array(),
                      true, false);

        $this->expect('23.2x10^5',
                      '23.2*x10^5',
                      array(),
                      true, false);

        $this->expect('23.2x 10^5',
                      '23.2*x*10^5',
                      array(),
                      true, false);

        $this->expect('23.2 x10^5',
                      '23.2*x10^5',
                      array(),
                      true, false);

        $this->expect('9.81x10^2*m/s',
                      '9.81*x10^2*m/s',
                      array(),
                      true, false);

        $this->expect('9.81x*10^2*m/s',
                      '9.81*x*10^2*m/s',
                      array(),
                      true, false);

        $this->expect('1x',
                      '1*x',
                      array(),
                      true, false);

        $this->expect('2*e',
                      '2*e',
                      array(),
                      true, false);

        $this->expect('2*i',
                      '2*i',
                      array(),
                      true, false);

        $this->expect('2*i^3',
                      '2*i^3',
                      array(),
                      true, false);

        $this->expect('2*pi',
                      '2*pi',
                      array(),
                      true, false);

        $this->expect('2+3(x+1)',
                      '2+3*(x+1)',
                      array(),
                      true, false);

        $this->expect('2/4',
                      '2/4',
                      array(),
                      true, false);

        $this->expect('2^y*x',
                      '2^y*x',
                      array(),
                      true, false);

        $this->expect('3(x+1)',
                      '3*(x+1)',
                      array(),
                      true, false);

        $this->expect('3-i',
                      '3-i',
                      array(),
                      true, false);

        $this->expect('3 5',
                      '3*5',
                      array(),
                      true, false);

        $this->expect('3.14 5',
                      '3.14*5',
                      array(),
                      true, false);

        $this->expect('3 5.2789',
                      '3*5.2789',
                      array(),
                      true, false);

        $this->expect('3.14 5.2789',
                      '3.14*5.2789',
                      array(),
                      true, false);

        $this->expect('33 578 32',
                      '33*578*32',
                      array(),
                      true, false);

        $this->expect('9 8 7.6',
                      '9*8*7.6',
                      array(),
                      true, false);

        $this->expect('9 8.5 7.6',
                      '9*8.5*7.6',
                      array(),
                      true, false);

        $this->expect('3b+5/a(x)',
                      '3*b+5/a(x)',
                      array(),
                      true, false);

        $this->expect('3beta_47',
                      '3*beta_47',
                      array(),
                      true, false);

        $this->expect('3e-2',
                      '3E-2',
                      array(),
                      true, false);

        $this->expect('3e2',
                      '3E2',
                      array(),
                      true, false);

        $this->expect('3E2',
                      '3E2',
                      array(),
                      true, false);

        $this->expect('7x(2+1)',
                      '7*x(2+1)',
                      array(),
                      true, false);

        $this->expect('Bgcd(3,2)',
                      'Bgcd(3,2)',
                      array(),
                      true, false);

        $this->expect('In(x)',
                      'In(x)',
                      array(),
                      true, false);

        $this->expect('Sin(x)',
                      'Sin(x)',
                      array(),
                      true, false);

        $this->expect('3.75*Btu',
                      '3.75*Btu',
                      array(),
                      true, false);

        $this->expect('X',
                      'X',
                      array(),
                      true, false);

        $this->expect('["a"]',
                      '["a"]',
                      array(),
                      true, false);

        $this->expect('[+1,+2]',
                      '[+1,+2]',
                      array(),
                      true, false);

        $this->expect('[-1,-2]',
                      '[-1,-2]',
                      array(),
                      true, false);

        $this->expect('[1 < x,y < 1 or y > 7]',
                      '[1 < x,y < 1 or y > 7]',
                      array(),
                      true, false);

        $this->expect('[1,+2]',
                      '[1,+2]',
                      array(),
                      true, false);

        $this->expect('[1,-2]',
                      '[1,-2]',
                      array(),
                      true, false);

        $this->expect('[1,2,3.4]',
                      '[1,2,3.4]',
                      array(),
                      true, false);

        $this->expect('[1,true,"a"]',
                      '[1,true,"a"]',
                      array(),
                      true, false);

        $this->expect('[1<x,x<3]',
                      '[1 < x,x < 3]',
                      array(),
                      true, false);

        $this->expect('[1]',
                      '[1]',
                      array(),
                      true, false);

        $this->expect('[[1,2],[3,4]]',
                      '[[1,2],[3,4]]',
                      array(),
                      true, false);

        $this->expect('[]',
                      '[]',
                      array(),
                      true, false);

        $this->expect('[x, y, z ]',
                      '[x,y,z]',
                      array(),
                      true, false);

        $this->expect('a ** b',
                      'a**b',
                      array(),
                      true, false);

        $this->expect('a +++ b',
                      'a+++b',
                      array(),
                      true, false);

        $this->expect('a --- b',
                      'a---b',
                      array(),
                      true, false);

        $this->expect('a(x)',
                      'a(x)',
                      array(),
                      true, false);

        $this->expect('a++b',
                      'a++b',
                      array(),
                      true, false);

        $this->expect('a+-b',
                      'a+-b',
                      array(),
                      true, false);

        $this->expect('a,b,c',
                      'a,b=true,c=true',
                      array(),
                      true, false);

        $this->expect('a-(b-c)',
                      'a-(b-c)',
                      array(),
                      true, false);

        $this->expect('a-+b',
                      'a-+b',
                      array(),
                      true, false);

        $this->expect('a/(a(x+1)+2)',
                      'a/(a(x+1)+2)',
                      array(),
                      true, false);

        $this->expect('a/b/c',
                      'a/b/c',
                      array(),
                      true, false);

        $this->expect('a1',
                      'a1',
                      array(),
                      true, false);

        $this->expect('a9b',
                      'a9b',
                      array(),
                      true, false);

        $this->expect('ab98cd',
                      'ab98cd',
                      array(),
                      true, false);

        $this->expect('aXy1',
                      'aXy1',
                      array(),
                      true, false);

        $this->expect('a[1,2]',
                      'a[1,2]',
                      array(),
                      true, false);

        $this->expect('a[2]',
                      'a[2]',
                      array(),
                      true, false);

        $this->expect('a[n+1]',
                      'a[n+1]',
                      array(),
                      true, false);

        $this->expect('a^-b',
                      'a^-b',
                      array(),
                      true, false);

        $this->expect('a^b',
                      'a^b',
                      array(),
                      true, false);

        $this->expect('a_b',
                      'a_b',
                      array(),
                      true, false);

        $this->expect('abs(13)',
                      'abs(13)',
                      array(),
                      true, false);

        $this->expect('abs(x)',
                      'abs(x)',
                      array(),
                      true, false);

        $this->expect('alpha',
                      'alpha',
                      array(),
                      true, false);

        $this->expect('asin(x)',
                      'asin(x)',
                      array(),
                      true, false);

        $this->expect('asinh(x)',
                      'asinh(x)',
                      array(),
                      true, false);

        $this->expect('b(b+1)',
                      'b(b+1)',
                      array(),
                      true, false);

        $this->expect('b/a(x)',
                      'b/a(x)',
                      array(),
                      true, false);

        $this->expect('beta',
                      'beta',
                      array(),
                      true, false);

        $this->expect('beta_47',
                      'beta_47',
                      array(),
                      true, false);

        $this->expect('bsin(t)',
                      'bsin(t)',
                      array(),
                      true, false);

        $this->expect('ceiling(x)',
                      'ceiling(x)',
                      array(),
                      true, false);

        $this->expect('chi',
                      'chi',
                      array(),
                      true, false);

        $this->expect('comb(x,y)',
                      'comb(x,y)',
                      array(),
                      true, false);

        $this->expect('cos(x)',
                      'cos(x)',
                      array(),
                      true, false);

        $this->expect('cosec(x)',
                      'cosec(x)',
                      array(),
                      true, false);

        $this->expect('cosech(x)',
                      'cosech(x)',
                      array(),
                      true, false);

        $this->expect('cosh(x)',
                      'cosh(x)',
                      array(),
                      true, false);

        $this->expect('cot(x)',
                      'cot(x)',
                      array(),
                      true, false);

        $this->expect('coth(x)',
                      'coth(x)',
                      array(),
                      true, false);

        $this->expect('csc(6*x)^2*(7*sin(6*x)*cos(7*x)-6*cos(6*x)*sin(7*x))',
                      'csc(6*x)^2*(7*sin(6*x)*cos(7*x)-6*cos(6*x)*sin(7*x))',
                      array(),
                      true, false);

        $this->expect('csc(x)',
                      'csc(x)',
                      array(),
                      true, false);

        $this->expect('delta',
                      'delta',
                      array(),
                      true, false);

        $this->expect('diff(sin(x))',
                      'diff(sin(x))',
                      array(),
                      true, false);

        $this->expect('diff(sin(x),x)',
                      'diff(sin(x),x)',
                      array(),
                      true, false);

        $this->expect('diff(x,y)',
                      'diff(x,y)',
                      array(),
                      true, false);

        $this->expect('dosomething(x,y,z)',
                      'dosomething(x,y,z)',
                      array(),
                      true, false);

        $this->expect('e',
                      'e',
                      array(),
                      true, false);

        $this->expect('e*2',
                      'e*2',
                      array(),
                      true, false);

        $this->expect('e^x',
                      'e^x',
                      array(),
                      true, false);

        $this->expect('epsilon',
                      'epsilon',
                      array(),
                      true, false);

        $this->expect('eta',
                      'eta',
                      array(),
                      true, false);

        $this->expect('exp(x)',
                      'exp(x)',
                      array(),
                      true, false);

        $this->expect('f(x)',
                      'f(x)',
                      array(),
                      true, false);

        $this->expect('fact(13)',
                      'fact(13)',
                      array(),
                      true, false);

        $this->expect('false',
                      'false',
                      array(),
                      true, false);

        $this->expect('floor(x)',
                      'floor(x)',
                      array(),
                      true, false);

        $this->expect('gamma',
                      'gamma',
                      array(),
                      true, false);

        $this->expect('gcd(x,y)',
                      'gcd(x,y)',
                      array(),
                      true, false);

        $this->expect('gcf(x,y)',
                      'gcf(x,y)',
                      array(),
                      true, false);

        $this->expect('i',
                      'i',
                      array(),
                      true, false);

        $this->expect('i*2',
                      'i*2',
                      array(),
                      true, false);

        $this->expect('inf',
                      'inf',
                      array(),
                      true, false);

        $this->expect('int(sin(x))',
                      'int(sin(x))',
                      array(),
                      true, false);

        $this->expect('int(x,y)',
                      'int(x,y)',
                      array(),
                      true, false);

        $this->expect('iota',
                      'iota',
                      array(),
                      true, false);

        $this->expect('j',
                      'j',
                      array(),
                      true, false);

        $this->expect('kappa',
                      'kappa',
                      array(),
                      true, false);

        $this->expect('lambda',
                      'lambda',
                      array(),
                      true, false);

        $this->expect('len(x)',
                      'len(x)',
                      array(),
                      true, false);

        $this->expect('length(x)',
                      'length(x)',
                      array(),
                      true, false);

        $this->expect('lg(10^3)',
                      'lg(10^3)',
                      array(),
                      true, false);

        $this->expect('lg(x)',
                      'lg(x)',
                      array(),
                      true, false);

        $this->expect('lg(x,a)',
                      'lg(x,a)',
                      array(),
                      true, false);

        $this->expect('limit(y,x,3)',
                      'limit(y,x,3)',
                      array(),
                      true, false);

        $this->expect('ln(x)',
                      'ln(x)',
                      array(),
                      true, false);

        $this->expect('ln*x',
                      'ln*x',
                      array(),
                      true, false);

        $this->expect('log(2x)/x+1/2',
                      'log(2*x)/x+1/2',
                      array(),
                      true, false);

        $this->expect('log(x)',
                      'log(x)',
                      array(),
                      true, false);

        $this->expect('matrix([a,b],[c,d])',
                      'matrix([a,b],[c,d])',
                      array(),
                      true, false);

        $this->expect('mod(x,y)',
                      'mod(x,y)',
                      array(),
                      true, false);

        $this->expect('mu',
                      'mu',
                      array(),
                      true, false);

        $this->expect('not x',
                      'not x',
                      array(),
                      true, false);

        $this->expect('nu',
                      'nu',
                      array(),
                      true, false);

        $this->expect('omega',
                      'omega',
                      array(),
                      true, false);

        $this->expect('omicron',
                      'omicron',
                      array(),
                      true, false);

        $this->expect('p=?*s',
                      'p = QMCHAR*s',
                      array(),
                      true, false);

        $this->expect('partialdiff(x,y,1)',
                      'partialdiff(x,y,1)',
                      array(),
                      true, false);

        $this->expect('perm(x,y)',
                      'perm(x,y)',
                      array(),
                      true, false);

        $this->expect('phi',
                      'phi',
                      array(),
                      true, false);

        $this->expect('pi',
                      'pi',
                      array(),
                      true, false);

        $this->expect('pi*2',
                      'pi*2',
                      array(),
                      true, false);

        $this->expect('plot(x^2,[x,-1,1])',
                      'plot(x^2,[x,-1,1])',
                      array(),
                      true, false);

        $this->expect('plot2d(x^2,[x,-1,1])',
                      'plot2d(x^2,[x,-1,1])',
                      array(),
                      true, false);

        $this->expect('product(cos(k*x),k,1,3)',
                      'product(cos(k*x),k,1,3)',
                      array(),
                      true, false);

        $this->expect('psi',
                      'psi',
                      array(),
                      true, false);

        $this->expect('rho',
                      'rho',
                      array(),
                      true, false);

        $this->expect('rho*z*V/(4*pi*epsilon[0]*(R^2+z^2)^(3/2))',
                      'rho*z*V/(4*pi*epsilon[0]*(R^2+z^2)^(3/2))',
                      array(),
                      true, false);

        $this->expect('root(2,-3)',
                      'root(2,-3)',
                      array(),
                      true, false);

        $this->expect('root(x)',
                      'root(x)',
                      array(),
                      true, false);

        $this->expect('root(x,3)',
                      'root(x,3)',
                      array(),
                      true, false);

        $this->expect('sec(x)',
                      'sec(x)',
                      array(),
                      true, false);

        $this->expect('sech(x)',
                      'sech(x)',
                      array(),
                      true, false);

        $this->expect('set(x, y, z)',
                      'set(x,y,z)',
                      array(),
                      true, false);

        $this->expect('sgn(x)',
                      'sgn(x)',
                      array(),
                      true, false);

        $this->expect('sigma',
                      'sigma',
                      array(),
                      true, false);

        $this->expect('sign(x)',
                      'sign(x)',
                      array(),
                      true, false);

        $this->expect('sim(x)',
                      'sim(x)',
                      array(),
                      true, false);

        $this->expect('sin',
                      'sin',
                      array(),
                      true, false);

        $this->expect('sin(x)',
                      'sin(x)',
                      array(),
                      true, false);

        $this->expect('sin*2*x',
                      'sin*2*x',
                      array(),
                      true, false);

        $this->expect('sinh(x)',
                      'sinh(x)',
                      array(),
                      true, false);

        $this->expect('sqr(x)',
                      'sqr(x)',
                      array(),
                      true, false);

        $this->expect('sqrt(+x)',
                      'sqrt(+x)',
                      array(),
                      true, false);

        $this->expect('sqrt(x)',
                      'sqrt(x)',
                      array(),
                      true, false);

        $this->expect('stackvector(a)',
                      'stackvector(a)',
                      array(),
                      true, false);

        $this->expect('sum(k^n,n,0,3)',
                      'sum(k^n,n,0,3)',
                      array(),
                      true, false);

        $this->expect('switch(x,a,y,b,c)',
                      'switch(x,a,y,b,c)',
                      array(),
                      true, false);

        $this->expect('tan(x)',
                      'tan(x)',
                      array(),
                      true, false);

        $this->expect('tanh(x)',
                      'tanh(x)',
                      array(),
                      true, false);

        $this->expect('tau',
                      'tau',
                      array(),
                      true, false);

        $this->expect('theta',
                      'theta',
                      array(),
                      true, false);

        $this->expect('true',
                      'true',
                      array(),
                      true, false);

        $this->expect('upsilon',
                      'upsilon',
                      array(),
                      true, false);

        $this->expect('x',
                      'x',
                      array(),
                      true, false);

        $this->expect('x * y',
                      'x*y',
                      array(),
                      true, false);

        $this->expect('x + 1',
                      'x+1',
                      array(),
                      true, false);

        $this->expect('x + y',
                      'x+y',
                      array(),
                      true, false);

        $this->expect('x - y',
                      'x-y',
                      array(),
                      true, false);

        $this->expect('x / y',
                      'x/y',
                      array(),
                      true, false);

        $this->expect('x < y',
                      'x < y',
                      array(),
                      true, false);

        $this->expect('x <= y',
                      'x <= y',
                      array(),
                      true, false);

        $this->expect('x = y',
                      'x = y',
                      array(),
                      true, false);

        $this->expect('x > y',
                      'x > y',
                      array(),
                      true, false);

        $this->expect('x >= y',
                      'x >= y',
                      array(),
                      true, false);

        $this->expect('x ^ y',
                      'x^y',
                      array(),
                      true, false);

        $this->expect('x and',
                      'x*and',
                      array(),
                      true, false);

        $this->expect('x and y',
                      'x and y',
                      array(),
                      true, false);

        $this->expect('x divides y',
                      'x*divides*y',
                      array(),
                      true, false);

        $this->expect('x or y',
                      'x or y',
                      array(),
                      true, false);

        $this->expect('x xor y',
                      'x xor y',
                      array(),
                      true, false);

        $this->expect('x y',
                      'x*y',
                      array(),
                      true, false);

        $this->expect('x!',
                      'x!',
                      array(),
                      true, false);

        $this->expect('x()',
                      'x()',
                      array(),
                      true, false);

        $this->expect('x(2+1)',
                      'x(2+1)',
                      array(),
                      true, false);

        $this->expect('x(sin(t)+1)',
                      'x(sin(t)+1)',
                      array(),
                      true, false);

        $this->expect('x(t+1)',
                      'x(t+1)',
                      array(),
                      true, false);

        $this->expect('x(x+1)',
                      'x(x+1)',
                      array(),
                      true, false);

        $this->expect('x*(-y)',
                      'x*(-y)',
                      array(),
                      true, false);

        $this->expect('x*(y*z)',
                      'x*(y*z)',
                      array(),
                      true, false);

        $this->expect('x*2^y',
                      'x*2^y',
                      array(),
                      true, false);

        $this->expect('x*divides*y',
                      'x*divides*y',
                      array(),
                      true, false);

        $this->expect('x*i^3',
                      'x*i^3',
                      array(),
                      true, false);

        $this->expect('x*y*z',
                      'x*y*z',
                      array(),
                      true, false);

        $this->expect('x*y^z',
                      'x*y^z',
                      array(),
                      true, false);

        $this->expect('x+ 1',
                      'x+1',
                      array(),
                      true, false);

        $this->expect('x+(y+z)',
                      'x+(y+z)',
                      array(),
                      true, false);

        $this->expect('x+(y^z)',
                      'x+(y^z)',
                      array(),
                      true, false);

        $this->expect('x+1',
                      'x+1',
                      array(),
                      true, false);

        $this->expect('x+y+z',
                      'x+y+z',
                      array(),
                      true, false);

        $this->expect('x-(y+z)',
                      'x-(y+z)',
                      array(),
                      true, false);

        $this->expect('x/(y/z)',
                      'x/(y/z)',
                      array(),
                      true, false);

        $this->expect('x/y/z',
                      'x/y/z',
                      array(),
                      true, false);

        $this->expect('x1',
                      'x1',
                      array(),
                      true, false);

        $this->expect('x<1 and x>1',
                      'x < 1 and x > 1',
                      array(),
                      true, false);

        $this->expect('x=+-sqrt(2)',
                      'x = +-sqrt(2)',
                      array(),
                      true, false);

        $this->expect('x=1 or 2',
                      'x = 1 or 2',
                      array(),
                      true, false);

        $this->expect('x=1 or 2 or 3',
                      'x = 1 or 2 or 3',
                      array(),
                      true, false);

        $this->expect('x=1 or x=2',
                      'x = 1 or x = 2',
                      array(),
                      true, false);

        $this->expect('x>1 or (x<1 and t<sin(x))',
                      'x > 1 or (x < 1 and t < sin(x))',
                      array(),
                      true, false);

        $this->expect('x^(-(y+z))',
                      'x^(-(y+z))',
                      array(),
                      true, false);

        $this->expect('x^(-y)',
                      'x^(-y)',
                      array(),
                      true, false);

        $this->expect('x^(y+z)',
                      'x^(y+z)',
                      array(),
                      true, false);

        $this->expect('x^(y/z)',
                      'x^(y/z)',
                      array(),
                      true, false);

        $this->expect('x^-1',
                      'x^-1',
                      array(),
                      true, false);

        $this->expect('x^-y',
                      'x^-y',
                      array(),
                      true, false);

        $this->expect('x^7/7-2*x^6/3-4*x^3/3',
                      'x^7/7-2*x^6/3-4*x^3/3',
                      array(),
                      true, false);

        $this->expect('x^f(x)',
                      'x^f(x)',
                      array(),
                      true, false);

        $this->expect('x^y',
                      'x^y',
                      array(),
                      true, false);

        $this->expect('x^y^z',
                      'x^y^z',
                      array(),
                      true, false);

        $this->expect('x_1',
                      'x_1',
                      array(),
                      true, false);

        $this->expect('x_y',
                      'x_y',
                      array(),
                      true, false);

        $this->expect('xy_zw',
                      'xy_zw',
                      array(),
                      true, false);

        $this->expect('xy_12',
                      'xy_12',
                      array(),
                      true, false);

        $this->expect('xi',
                      'xi',
                      array(),
                      true, false);

        $this->expect('xsin(1)',
                      'xsin(1)',
                      array(),
                      true, false);

        $this->expect('xy',
                      'xy',
                      array(),
                      true, false);

        $this->expect('y^2-2*y-0.5',
                      'y^2-2*y-0.5',
                      array(),
                      true, false);

        $this->expect('y^2-2*y-8',
                      'y^2-2*y-8',
                      array(),
                      true, false);

        $this->expect('y^3-2*y^2-8*y',
                      'y^3-2*y^2-8*y',
                      array(),
                      true, false);

        $this->expect('y^z * x',
                      'y^z*x',
                      array(),
                      true, false);

        $this->expect('ycos(2)',
                      'ycos(2)',
                      array(),
                      true, false);

        $this->expect('zeta',
                      'zeta',
                      array(),
                      true, false);

        $this->expect('{1,2,3.4}',
                      '{1,2,3.4}',
                      array(),
                      true, false);

        $this->expect('{1}',
                      '{1}',
                      array(),
                      true, false);

        $this->expect('{x, y, z }',
                      '{x,y,z}',
                      array(),
                      true, false);

        $this->expect('{}',
                      '{}',
                      array(),
                      true, false);

        $this->expect('|x|',
                      'abs(x)',
                      array(),
                      true, false);

        $this->expect('1.2*m**2',
                      '1.2*m**2',
                      array(),
                      true, false);

        $this->expect('/* Comment */x+1',
                      '/* Comment */x+1',
                      array(),
                      true, false);

        $this->expect('/* Comment **/x+1',
                      '/* Comment **/x+1',
                      array(),
                      true, false);

        $this->expect('/** Comment */x+1',
                      '/** Comment */x+1',
                      array(),
                      true, false);

        $this->expect('/** Comment **/x+1',
                      '/** Comment **/x+1',
                      array(),
                      true, false);

        $this->expect('/*@ Comment @*/x+1',
                      '/*@ Comment @*/x+1',
                      array(),
                      true, false);

        $this->expect('"A string that needs sanitising <script>bad stuff</script>."',
                      '"A string that needs sanitising <script>bad stuff</script>."',
                      array(),
                      true, false);

    }
}

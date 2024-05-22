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

namespace qtype_stack;

use qtype_stack_ast_testcase;
use stack_cas_security;
use stack_parsing_rule_factory;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../tests/fixtures/ast_filter_test_base.php');

// Auto-generated unit tests for AST-filter DO NOT EDIT!
/**
 * @group qtype_stack
 * @group qtype_stack_ast_filters
 * @covers \ast_filter_105_no_grouppings
 */

class ast_filter_105_no_grouppings_auto_generated_test extends qtype_stack_ast_testcase {

    public function test_affected_no_units() {
        $this->security = new stack_cas_security(false);
        $this->filter = stack_parsing_rule_factory::get_by_common_name('105_no_grouppings');

        $this->expect('(()x)',
                      '(()*x)',
                      ['Illegal_groupping'],
                      false, true);

        $this->expect('((x))',
                      '((x))',
                      ['Illegal_groupping'],
                      false, true);

        $this->expect('(+1)',
                      '(+1)',
                      ['Illegal_groupping'],
                      false, true);

        $this->expect('(-1)',
                      '(-1)',
                      ['Illegal_groupping'],
                      false, true);

        $this->expect('(-b+-sqrt(b^2))/(2*a)',
                      '(-b+-sqrt(b^2))/(2*a)',
                      ['Illegal_groupping'],
                      false, true);

        $this->expect('(-x)*y',
                      '(-x)*y',
                      ['Illegal_groupping'],
                      false, true);

        $this->expect('(1+i)*x',
                      '(1+i)*x',
                      ['Illegal_groupping'],
                      false, true);

        $this->expect('(1+i)+x',
                      '(1+i)+x',
                      ['Illegal_groupping'],
                      false, true);

        $this->expect('(a-b)-c',
                      '(a-b)-c',
                      ['Illegal_groupping'],
                      false, true);

        $this->expect('(x)',
                      '(x)',
                      ['Illegal_groupping'],
                      false, true);

        $this->expect('(x*y)*z',
                      '(x*y)*z',
                      ['Illegal_groupping'],
                      false, true);

        $this->expect('(x+2)(x+3)',
                      '(x+2)(x+3)',
                      ['Illegal_groupping'],
                      false, true);

        $this->expect('(x+2)3',
                      '(x+2)*3',
                      ['Illegal_groupping'],
                      false, true);

        $this->expect('(x+2)y',
                      '(x+2)*y',
                      ['Illegal_groupping'],
                      false, true);

        $this->expect('(x+y)+z',
                      '(x+y)+z',
                      ['Illegal_groupping'],
                      false, true);

        $this->expect('(x+y)^z',
                      '(x+y)^z',
                      ['Illegal_groupping'],
                      false, true);

        $this->expect('(x-y)+z',
                      '(x-y)+z',
                      ['Illegal_groupping'],
                      false, true);

        $this->expect('(x/y)/z',
                      '(x/y)/z',
                      ['Illegal_groupping'],
                      false, true);

        $this->expect('-(1/512) + i(sqrt(3)/512)',
                      '-(1/512)+i(sqrt(3)/512)',
                      ['Illegal_groupping'],
                      false, true);

        $this->expect('-3(x+1)',
                      '-3*(x+1)',
                      ['Illegal_groupping'],
                      false, true);

        $this->expect('2+3(x+1)',
                      '2+3*(x+1)',
                      ['Illegal_groupping'],
                      false, true);

        $this->expect('2+log_x(1/(x+b))*x^2',
                      '2+log_x(1/(x+b))*x^2',
                      ['Illegal_groupping'],
                      false, true);

        $this->expect('3(x+1)',
                      '3*(x+1)',
                      ['Illegal_groupping'],
                      false, true);

        $this->expect('a-(b-c)',
                      'a-(b-c)',
                      ['Illegal_groupping'],
                      false, true);

        $this->expect('a/(a(x+1)+2)',
                      'a/(a(x+1)+2)',
                      ['Illegal_groupping'],
                      false, true);

        $this->expect('cos^2(x)',
                      'cos^2*(x)',
                      ['Illegal_groupping'],
                      false, true);

        $this->expect('csc(6*x)^2*(7*sin(6*x)*cos(7*x)-6*cos(6*x)*sin(7*x))',
                      'csc(6*x)^2*(7*sin(6*x)*cos(7*x)-6*cos(6*x)*sin(7*x))',
                      ['Illegal_groupping'],
                      false, true);

        $this->expect('log_x(1/(x+b))',
                      'log_x(1/(x+b))',
                      ['Illegal_groupping'],
                      false, true);

        $this->expect('rho*z*V/(4*pi*epsilon[0]*(R^2+z^2)^(3/2))',
                      'rho*z*V/(4*pi*epsilon[0]*(R^2+z^2)^(3/2))',
                      ['Illegal_groupping'],
                      false, true);

        $this->expect('sin^-1(x)',
                      'sin^-1*(x)',
                      ['Illegal_groupping'],
                      false, true);

        $this->expect('x*(-y)',
                      'x*(-y)',
                      ['Illegal_groupping'],
                      false, true);

        $this->expect('x*(y*z)',
                      'x*(y*z)',
                      ['Illegal_groupping'],
                      false, true);

        $this->expect('x+(y+z)',
                      'x+(y+z)',
                      ['Illegal_groupping'],
                      false, true);

        $this->expect('x+(y^z)',
                      'x+(y^z)',
                      ['Illegal_groupping'],
                      false, true);

        $this->expect('x-(y+z)',
                      'x-(y+z)',
                      ['Illegal_groupping'],
                      false, true);

        $this->expect('x/(y+z)',
                      'x/(y+z)',
                      ['Illegal_groupping'],
                      false, true);

        $this->expect('x/(y/z)',
                      'x/(y/z)',
                      ['Illegal_groupping'],
                      false, true);

        $this->expect('x>1 or (x<1 and t<sin(x))',
                      'x > 1 or (x < 1 and t < sin(x))',
                      ['Illegal_groupping'],
                      false, true);

        $this->expect('x^(-(y+z))',
                      'x^(-(y+z))',
                      ['Illegal_groupping'],
                      false, true);

        $this->expect('x^(-y)',
                      'x^(-y)',
                      ['Illegal_groupping'],
                      false, true);

        $this->expect('x^(y+z)',
                      'x^(y+z)',
                      ['Illegal_groupping'],
                      false, true);

        $this->expect('x^(y/z)',
                      'x^(y/z)',
                      ['Illegal_groupping'],
                      false, true);

    }

    public function test_affected_units() {
        $this->security = new stack_cas_security(true);
        $this->filter = stack_parsing_rule_factory::get_by_common_name('105_no_grouppings');

        $this->expect('(()x)',
                      '(()*x)',
                      ['Illegal_groupping'],
                      false, true);

        $this->expect('((x))',
                      '((x))',
                      ['Illegal_groupping'],
                      false, true);

        $this->expect('(+1)',
                      '(+1)',
                      ['Illegal_groupping'],
                      false, true);

        $this->expect('(-1)',
                      '(-1)',
                      ['Illegal_groupping'],
                      false, true);

        $this->expect('(-b+-sqrt(b^2))/(2*a)',
                      '(-b+-sqrt(b^2))/(2*a)',
                      ['Illegal_groupping'],
                      false, true);

        $this->expect('(-x)*y',
                      '(-x)*y',
                      ['Illegal_groupping'],
                      false, true);

        $this->expect('(1+i)*x',
                      '(1+i)*x',
                      ['Illegal_groupping'],
                      false, true);

        $this->expect('(1+i)+x',
                      '(1+i)+x',
                      ['Illegal_groupping'],
                      false, true);

        $this->expect('(a-b)-c',
                      '(a-b)-c',
                      ['Illegal_groupping'],
                      false, true);

        $this->expect('(x)',
                      '(x)',
                      ['Illegal_groupping'],
                      false, true);

        $this->expect('(x*y)*z',
                      '(x*y)*z',
                      ['Illegal_groupping'],
                      false, true);

        $this->expect('(x+2)(x+3)',
                      '(x+2)(x+3)',
                      ['Illegal_groupping'],
                      false, true);

        $this->expect('(x+2)3',
                      '(x+2)*3',
                      ['Illegal_groupping'],
                      false, true);

        $this->expect('(x+2)y',
                      '(x+2)*y',
                      ['Illegal_groupping'],
                      false, true);

        $this->expect('(x+y)+z',
                      '(x+y)+z',
                      ['Illegal_groupping'],
                      false, true);

        $this->expect('(x+y)^z',
                      '(x+y)^z',
                      ['Illegal_groupping'],
                      false, true);

        $this->expect('(x-y)+z',
                      '(x-y)+z',
                      ['Illegal_groupping'],
                      false, true);

        $this->expect('(x/y)/z',
                      '(x/y)/z',
                      ['Illegal_groupping'],
                      false, true);

        $this->expect('-(1/512) + i(sqrt(3)/512)',
                      '-(1/512)+i(sqrt(3)/512)',
                      ['Illegal_groupping'],
                      false, true);

        $this->expect('-3(x+1)',
                      '-3*(x+1)',
                      ['Illegal_groupping'],
                      false, true);

        $this->expect('2+3(x+1)',
                      '2+3*(x+1)',
                      ['Illegal_groupping'],
                      false, true);

        $this->expect('2+log_x(1/(x+b))*x^2',
                      '2+log_x(1/(x+b))*x^2',
                      ['Illegal_groupping'],
                      false, true);

        $this->expect('3(x+1)',
                      '3*(x+1)',
                      ['Illegal_groupping'],
                      false, true);

        $this->expect('a-(b-c)',
                      'a-(b-c)',
                      ['Illegal_groupping'],
                      false, true);

        $this->expect('a/(a(x+1)+2)',
                      'a/(a(x+1)+2)',
                      ['Illegal_groupping'],
                      false, true);

        $this->expect('cos^2(x)',
                      'cos^2*(x)',
                      ['Illegal_groupping'],
                      false, true);

        $this->expect('csc(6*x)^2*(7*sin(6*x)*cos(7*x)-6*cos(6*x)*sin(7*x))',
                      'csc(6*x)^2*(7*sin(6*x)*cos(7*x)-6*cos(6*x)*sin(7*x))',
                      ['Illegal_groupping'],
                      false, true);

        $this->expect('log_x(1/(x+b))',
                      'log_x(1/(x+b))',
                      ['Illegal_groupping'],
                      false, true);

        $this->expect('rho*z*V/(4*pi*epsilon[0]*(R^2+z^2)^(3/2))',
                      'rho*z*V/(4*pi*epsilon[0]*(R^2+z^2)^(3/2))',
                      ['Illegal_groupping'],
                      false, true);

        $this->expect('sin^-1(x)',
                      'sin^-1*(x)',
                      ['Illegal_groupping'],
                      false, true);

        $this->expect('x*(-y)',
                      'x*(-y)',
                      ['Illegal_groupping'],
                      false, true);

        $this->expect('x*(y*z)',
                      'x*(y*z)',
                      ['Illegal_groupping'],
                      false, true);

        $this->expect('x+(y+z)',
                      'x+(y+z)',
                      ['Illegal_groupping'],
                      false, true);

        $this->expect('x+(y^z)',
                      'x+(y^z)',
                      ['Illegal_groupping'],
                      false, true);

        $this->expect('x-(y+z)',
                      'x-(y+z)',
                      ['Illegal_groupping'],
                      false, true);

        $this->expect('x/(y+z)',
                      'x/(y+z)',
                      ['Illegal_groupping'],
                      false, true);

        $this->expect('x/(y/z)',
                      'x/(y/z)',
                      ['Illegal_groupping'],
                      false, true);

        $this->expect('x>1 or (x<1 and t<sin(x))',
                      'x > 1 or (x < 1 and t < sin(x))',
                      ['Illegal_groupping'],
                      false, true);

        $this->expect('x^(-(y+z))',
                      'x^(-(y+z))',
                      ['Illegal_groupping'],
                      false, true);

        $this->expect('x^(-y)',
                      'x^(-y)',
                      ['Illegal_groupping'],
                      false, true);

        $this->expect('x^(y+z)',
                      'x^(y+z)',
                      ['Illegal_groupping'],
                      false, true);

        $this->expect('x^(y/z)',
                      'x^(y/z)',
                      ['Illegal_groupping'],
                      false, true);

    }

    public function test_non_affected_units() {
        $this->security = new stack_cas_security(true);
        $this->filter = stack_parsing_rule_factory::get_by_common_name('105_no_grouppings');

        $this->expect('"+"(a,b)',
                      '"+"(a,b)',
                      [],
                      true, false);

        $this->expect('"1+1"',
                      '"1+1"',
                      [],
                      true, false);

        $this->expect('"Hello world"',
                      '"Hello world"',
                      [],
                      true, false);

        $this->expect('%e^x',
                      '%e^x',
                      [],
                      true, false);

        $this->expect('2pi r^2',
                      '2*pi*r^2',
                      [],
                      true, false);

        $this->expect('2pir^2',
                      '2*pir^2',
                      [],
                      true, false);

        $this->expect("'diff(x,y)",
                      "'diff(x,y)",
                      [],
                      true, false);

        $this->expect("'int(x,y)",
                      "'int(x,y)",
                      [],
                      true, false);

        $this->expect('()x',
                      '()*x',
                      [],
                      true, false);

        $this->expect('(a,b,c)',
                      '(a,b,c)',
                      [],
                      true, false);

        $this->expect('((a,b),c)',
                      '((a,b),c)',
                      [],
                      true, false);

        $this->expect('(a,(b,c))',
                      '(a,(b,c))',
                      [],
                      true, false);

        $this->expect('{(a,b),(x,y)}',
                      '{(a,b),(x,y)}',
                      [],
                      true, false);

        $this->expect('+-1',
                      '+-1',
                      [],
                      true, false);

        $this->expect('+0.2',
                      '+0.2',
                      [],
                      true, false);

        $this->expect('+1',
                      '+1',
                      [],
                      true, false);

        $this->expect('+e',
                      '+e',
                      [],
                      true, false);

        $this->expect('+i',
                      '+i',
                      [],
                      true, false);

        $this->expect('+pi',
                      '+pi',
                      [],
                      true, false);

        $this->expect('+x',
                      '+x',
                      [],
                      true, false);

        $this->expect('-1',
                      '-1',
                      [],
                      true, false);

        $this->expect('-1234',
                      '-1234',
                      [],
                      true, false);

        $this->expect('-0.2',
                      '-0.2',
                      [],
                      true, false);

        $this->expect('-10/-1',
                      '-10/-1',
                      [],
                      true, false);

        $this->expect('-3+i',
                      '-3+i',
                      [],
                      true, false);

        $this->expect('-3x(1+x)',
                      '-3*x(1+x)',
                      [],
                      true, false);

        $this->expect('-b(5-b)',
                      '-b(5-b)',
                      [],
                      true, false);

        $this->expect('-e',
                      '-e',
                      [],
                      true, false);

        $this->expect('-i',
                      '-i',
                      [],
                      true, false);

        $this->expect('-pi',
                      '-pi',
                      [],
                      true, false);

        $this->expect('-x',
                      '-x',
                      [],
                      true, false);

        $this->expect('-x(1+x)',
                      '-x(1+x)',
                      [],
                      true, false);

        $this->expect('-x[3]',
                      '-x[3]',
                      [],
                      true, false);

        $this->expect('.1',
                      '.1',
                      [],
                      true, false);

        $this->expect('-0.2433 + 0.1111',
                      '-0.2433+0.1111',
                      [],
                      true, false);

        $this->expect('-0.2433e23 + 0.1111e-45 * 0.23e12 / -0.11e-11',
                      '-0.2433E23+0.1111E-45*0.23E12/-0.11E-11',
                      [],
                      true, false);

        $this->expect('-35.3 * 10^23',
                      '-35.3*10^23',
                      [],
                      true, false);

        $this->expect('0..1',
                      '0. . 1',
                      [],
                      true, false);

        $this->expect('0.1..1.2',
                      '0.1 . .1 . 2',
                      [],
                      true, false);

        $this->expect('0.1.1.2',
                      '0.1 . 1.2',
                      [],
                      true, false);

        $this->expect('0.1. 1.2',
                      '0.1 . 1.2',
                      [],
                      true, false);

        $this->expect('1',
                      '1',
                      [],
                      true, false);

        $this->expect('1234',
                      '1234',
                      [],
                      true, false);

        $this->expect('1 x',
                      '1*x',
                      [],
                      true, false);

        $this->expect('1+2i',
                      '1+2*i',
                      [],
                      true, false);

        $this->expect('1+i',
                      '1+i',
                      [],
                      true, false);

        $this->expect('1-x(1+x)',
                      '1-x(1+x)',
                      [],
                      true, false);

        $this->expect('1/0',
                      '1/0',
                      [],
                      true, false);

        $this->expect('1/2',
                      '1/2',
                      [],
                      true, false);

        $this->expect('1/sin(+x)',
                      '1/sin(+x)',
                      [],
                      true, false);

        $this->expect('1<=x<y^2',
                      '1 <= x < y^2',
                      [],
                      true, false);

        $this->expect('1<x<3',
                      '1 < x < 3',
                      [],
                      true, false);

        $this->expect('1E+3',
                      '1E+3',
                      [],
                      true, false);

        $this->expect('1E3',
                      '1E3',
                      [],
                      true, false);

        $this->expect('1 E 3',
                      '1*E*3',
                      [],
                      true, false);

        $this->expect('23.2*x*10^5',
                      '23.2*x*10^5',
                      [],
                      true, false);

        $this->expect('23.2 x 10^5',
                      '23.2*x*10^5',
                      [],
                      true, false);

        $this->expect('23.2x10^5',
                      '23.2*x10^5',
                      [],
                      true, false);

        $this->expect('23.2x 10^5',
                      '23.2*x*10^5',
                      [],
                      true, false);

        $this->expect('23.2 x10^5',
                      '23.2*x10^5',
                      [],
                      true, false);

        $this->expect('1E23*10^45',
                      '1E23*10^45',
                      [],
                      true, false);

        $this->expect('9.81x10^2*m/s',
                      '9.81*x10^2*m/s',
                      [],
                      true, false);

        $this->expect('9.81x*10^2*m/s',
                      '9.81*x*10^2*m/s',
                      [],
                      true, false);

        $this->expect('1x',
                      '1*x',
                      [],
                      true, false);

        $this->expect('2*e',
                      '2*e',
                      [],
                      true, false);

        $this->expect('2*i',
                      '2*i',
                      [],
                      true, false);

        $this->expect('2*i^3',
                      '2*i^3',
                      [],
                      true, false);

        $this->expect('2*pi',
                      '2*pi',
                      [],
                      true, false);

        $this->expect('2/4',
                      '2/4',
                      [],
                      true, false);

        $this->expect('2^y*x',
                      '2^y*x',
                      [],
                      true, false);

        $this->expect('3-i',
                      '3-i',
                      [],
                      true, false);

        $this->expect('3 5',
                      '3*5',
                      [],
                      true, false);

        $this->expect('3.14 5',
                      '3.14*5',
                      [],
                      true, false);

        $this->expect('3 5.2789',
                      '3*5.2789',
                      [],
                      true, false);

        $this->expect('3.14 5.2789',
                      '3.14*5.2789',
                      [],
                      true, false);

        $this->expect('33 578 32',
                      '33*578*32',
                      [],
                      true, false);

        $this->expect('9 8 7.6',
                      '9*8*7.6',
                      [],
                      true, false);

        $this->expect('9 8.5 7.6',
                      '9*8.5*7.6',
                      [],
                      true, false);

        $this->expect('3b+5/a(x)',
                      '3*b+5/a(x)',
                      [],
                      true, false);

        $this->expect('3beta_47',
                      '3*beta_47',
                      [],
                      true, false);

        $this->expect('3e-2',
                      '3E-2',
                      [],
                      true, false);

        $this->expect('3e2',
                      '3E2',
                      [],
                      true, false);

        $this->expect('3E2',
                      '3E2',
                      [],
                      true, false);

        $this->expect('7x(2+1)',
                      '7*x(2+1)',
                      [],
                      true, false);

        $this->expect('Bgcd(3,2)',
                      'Bgcd(3,2)',
                      [],
                      true, false);

        $this->expect('In(x)',
                      'In(x)',
                      [],
                      true, false);

        $this->expect('Sin(x)',
                      'Sin(x)',
                      [],
                      true, false);

        $this->expect('3.75*Btu',
                      '3.75*Btu',
                      [],
                      true, false);

        $this->expect('X',
                      'X',
                      [],
                      true, false);

        $this->expect('["a"]',
                      '["a"]',
                      [],
                      true, false);

        $this->expect('[+1,+2]',
                      '[+1,+2]',
                      [],
                      true, false);

        $this->expect('[-1,-2]',
                      '[-1,-2]',
                      [],
                      true, false);

        $this->expect('[1 < x,y < 1 or y > 7]',
                      '[1 < x,y < 1 or y > 7]',
                      [],
                      true, false);

        $this->expect('[1,+2]',
                      '[1,+2]',
                      [],
                      true, false);

        $this->expect('[1,-2]',
                      '[1,-2]',
                      [],
                      true, false);

        $this->expect('[1,2,3.4]',
                      '[1,2,3.4]',
                      [],
                      true, false);

        $this->expect('[1,true,"a"]',
                      '[1,true,"a"]',
                      [],
                      true, false);

        $this->expect('[1<x,1<y<3]',
                      '[1 < x,1 < y < 3]',
                      [],
                      true, false);

        $this->expect('[1<x,x<3]',
                      '[1 < x,x < 3]',
                      [],
                      true, false);

        $this->expect('[1]',
                      '[1]',
                      [],
                      true, false);

        $this->expect('[[1,2],[3,4]]',
                      '[[1,2],[3,4]]',
                      [],
                      true, false);

        $this->expect('[]',
                      '[]',
                      [],
                      true, false);

        $this->expect('[x, y, z ]',
                      '[x,y,z]',
                      [],
                      true, false);

        $this->expect('a ** b',
                      'a**b',
                      [],
                      true, false);

        $this->expect('a +++ b',
                      'a+++b',
                      [],
                      true, false);

        $this->expect('a --- b',
                      'a---b',
                      [],
                      true, false);

        $this->expect('a(x)',
                      'a(x)',
                      [],
                      true, false);

        $this->expect('a++b',
                      'a++b',
                      [],
                      true, false);

        $this->expect('a+-b',
                      'a+-b',
                      [],
                      true, false);

        $this->expect('a,b,c',
                      'a,b=true,c=true',
                      [],
                      true, false);

        $this->expect('a-+b',
                      'a-+b',
                      [],
                      true, false);

        $this->expect('a/b/c',
                      'a/b/c',
                      [],
                      true, false);

        $this->expect('a1',
                      'a1',
                      [],
                      true, false);

        $this->expect('a9b',
                      'a9b',
                      [],
                      true, false);

        $this->expect('ab98cd',
                      'ab98cd',
                      [],
                      true, false);

        $this->expect('aXy1',
                      'aXy1',
                      [],
                      true, false);

        $this->expect('a[1,2]',
                      'a[1,2]',
                      [],
                      true, false);

        $this->expect('a[2]',
                      'a[2]',
                      [],
                      true, false);

        $this->expect('a[n+1]',
                      'a[n+1]',
                      [],
                      true, false);

        $this->expect('a^-b',
                      'a^-b',
                      [],
                      true, false);

        $this->expect('a^b',
                      'a^b',
                      [],
                      true, false);

        $this->expect('a_b',
                      'a_b',
                      [],
                      true, false);

        $this->expect('abs(13)',
                      'abs(13)',
                      [],
                      true, false);

        $this->expect('abs(x)',
                      'abs(x)',
                      [],
                      true, false);

        $this->expect('alpha',
                      'alpha',
                      [],
                      true, false);

        $this->expect('arcsin(x)',
                      'arcsin(x)',
                      [],
                      true, false);

        $this->expect('asin(x)',
                      'asin(x)',
                      [],
                      true, false);

        $this->expect('asinh(x)',
                      'asinh(x)',
                      [],
                      true, false);

        $this->expect('b(b+1)',
                      'b(b+1)',
                      [],
                      true, false);

        $this->expect('b/a(x)',
                      'b/a(x)',
                      [],
                      true, false);

        $this->expect('beta',
                      'beta',
                      [],
                      true, false);

        $this->expect('beta_47',
                      'beta_47',
                      [],
                      true, false);

        $this->expect('bsin(t)',
                      'bsin(t)',
                      [],
                      true, false);

        $this->expect('ceiling(x)',
                      'ceiling(x)',
                      [],
                      true, false);

        $this->expect('chi',
                      'chi',
                      [],
                      true, false);

        $this->expect('comb(x,y)',
                      'comb(x,y)',
                      [],
                      true, false);

        $this->expect('cos(2x)(x+1)',
                      'cos(2*x)(x+1)',
                      [],
                      true, false);

        $this->expect('cos(x)',
                      'cos(x)',
                      [],
                      true, false);

        $this->expect('cosec(x)',
                      'cosec(x)',
                      [],
                      true, false);

        $this->expect('cosech(x)',
                      'cosech(x)',
                      [],
                      true, false);

        $this->expect('cosh(x)',
                      'cosh(x)',
                      [],
                      true, false);

        $this->expect('cot(x)',
                      'cot(x)',
                      [],
                      true, false);

        $this->expect('coth(x)',
                      'coth(x)',
                      [],
                      true, false);

        $this->expect('csc(x)',
                      'csc(x)',
                      [],
                      true, false);

        $this->expect('delta',
                      'delta',
                      [],
                      true, false);

        $this->expect('diff(sin(x))',
                      'diff(sin(x))',
                      [],
                      true, false);

        $this->expect('diff(sin(x),x)',
                      'diff(sin(x),x)',
                      [],
                      true, false);

        $this->expect('diff(x,y)',
                      'diff(x,y)',
                      [],
                      true, false);

        $this->expect('dosomething(x,y,z)',
                      'dosomething(x,y,z)',
                      [],
                      true, false);

        $this->expect('e',
                      'e',
                      [],
                      true, false);

        $this->expect('e*2',
                      'e*2',
                      [],
                      true, false);

        $this->expect('e^x',
                      'e^x',
                      [],
                      true, false);

        $this->expect('epsilon',
                      'epsilon',
                      [],
                      true, false);

        $this->expect('eta',
                      'eta',
                      [],
                      true, false);

        $this->expect('exp(x)',
                      'exp(x)',
                      [],
                      true, false);

        $this->expect('f(x)',
                      'f(x)',
                      [],
                      true, false);

        $this->expect('f(x)(2)',
                      'f(x)(2)',
                      [],
                      true, false);

        $this->expect('fact(13)',
                      'fact(13)',
                      [],
                      true, false);

        $this->expect('false',
                      'false',
                      [],
                      true, false);

        $this->expect('floor(x)',
                      'floor(x)',
                      [],
                      true, false);

        $this->expect('gamma',
                      'gamma',
                      [],
                      true, false);

        $this->expect('gcd(x,y)',
                      'gcd(x,y)',
                      [],
                      true, false);

        $this->expect('gcf(x,y)',
                      'gcf(x,y)',
                      [],
                      true, false);

        $this->expect('i',
                      'i',
                      [],
                      true, false);

        $this->expect('i(1+i)',
                      'i(1+i)',
                      [],
                      true, false);

        $this->expect('i(4)',
                      'i(4)',
                      [],
                      true, false);

        $this->expect('i*2',
                      'i*2',
                      [],
                      true, false);

        $this->expect('inf',
                      'inf',
                      [],
                      true, false);

        $this->expect('int(sin(x))',
                      'int(sin(x))',
                      [],
                      true, false);

        $this->expect('int(x,y)',
                      'int(x,y)',
                      [],
                      true, false);

        $this->expect('iota',
                      'iota',
                      [],
                      true, false);

        $this->expect('j',
                      'j',
                      [],
                      true, false);

        $this->expect('kappa',
                      'kappa',
                      [],
                      true, false);

        $this->expect('lambda',
                      'lambda',
                      [],
                      true, false);

        $this->expect('len(x)',
                      'len(x)',
                      [],
                      true, false);

        $this->expect('length(x)',
                      'length(x)',
                      [],
                      true, false);

        $this->expect('lg(10^3)',
                      'lg(10^3)',
                      [],
                      true, false);

        $this->expect('lg(x)',
                      'lg(x)',
                      [],
                      true, false);

        $this->expect('lg(x,a)',
                      'lg(x,a)',
                      [],
                      true, false);

        $this->expect('limit(y,x,3)',
                      'limit(y,x,3)',
                      [],
                      true, false);

        $this->expect('ln(x)',
                      'ln(x)',
                      [],
                      true, false);

        $this->expect('ln*x',
                      'ln*x',
                      [],
                      true, false);

        $this->expect('log(2x)/x+1/2',
                      'log(2*x)/x+1/2',
                      [],
                      true, false);

        $this->expect('log(x)',
                      'log(x)',
                      [],
                      true, false);

        $this->expect('log10(x)',
                      'log10(x)',
                      [],
                      true, false);

        $this->expect('log_10(x)',
                      'log_10(x)',
                      [],
                      true, false);

        $this->expect('log_2(a)',
                      'log_2(a)',
                      [],
                      true, false);

        $this->expect('log_a(b)*log_b(c)',
                      'log_a(b)*log_b(c)',
                      [],
                      true, false);

        $this->expect('log_x:log_x(a)',
                      'log_x:log_x(a)',
                      [],
                      true, false);

        $this->expect('matrix([a,b],[c,d])',
                      'matrix([a,b],[c,d])',
                      [],
                      true, false);

        $this->expect('mod(x,y)',
                      'mod(x,y)',
                      [],
                      true, false);

        $this->expect('mu',
                      'mu',
                      [],
                      true, false);

        $this->expect('not x',
                      'not x',
                      [],
                      true, false);

        $this->expect('nu',
                      'nu',
                      [],
                      true, false);

        $this->expect('omega',
                      'omega',
                      [],
                      true, false);

        $this->expect('omicron',
                      'omicron',
                      [],
                      true, false);

        $this->expect('p=?*s',
                      'p = QMCHAR*s',
                      [],
                      true, false);

        $this->expect('partialdiff(x,y,1)',
                      'partialdiff(x,y,1)',
                      [],
                      true, false);

        $this->expect('perm(x,y)',
                      'perm(x,y)',
                      [],
                      true, false);

        $this->expect('phi',
                      'phi',
                      [],
                      true, false);

        $this->expect('pi',
                      'pi',
                      [],
                      true, false);

        $this->expect('pi*2',
                      'pi*2',
                      [],
                      true, false);

        $this->expect('plot(x^2,[x,-1,1])',
                      'plot(x^2,[x,-1,1])',
                      [],
                      true, false);

        $this->expect('plot2d(x^2,[x,-1,1])',
                      'plot2d(x^2,[x,-1,1])',
                      [],
                      true, false);

        $this->expect('product(cos(k*x),k,1,3)',
                      'product(cos(k*x),k,1,3)',
                      [],
                      true, false);

        $this->expect('psi',
                      'psi',
                      [],
                      true, false);

        $this->expect('rho',
                      'rho',
                      [],
                      true, false);

        $this->expect('root(2,-3)',
                      'root(2,-3)',
                      [],
                      true, false);

        $this->expect('root(x)',
                      'root(x)',
                      [],
                      true, false);

        $this->expect('root(x,3)',
                      'root(x,3)',
                      [],
                      true, false);

        $this->expect('sec(x)',
                      'sec(x)',
                      [],
                      true, false);

        $this->expect('sech(x)',
                      'sech(x)',
                      [],
                      true, false);

        $this->expect('set(x, y, z)',
                      'set(x,y,z)',
                      [],
                      true, false);

        $this->expect('sgn(x)',
                      'sgn(x)',
                      [],
                      true, false);

        $this->expect('sigma',
                      'sigma',
                      [],
                      true, false);

        $this->expect('sign(x)',
                      'sign(x)',
                      [],
                      true, false);

        $this->expect('sim(x)',
                      'sim(x)',
                      [],
                      true, false);

        $this->expect('sin',
                      'sin',
                      [],
                      true, false);

        $this->expect('sin x',
                      'sin*x',
                      [],
                      true, false);

        $this->expect('sin(x)',
                      'sin(x)',
                      [],
                      true, false);

        $this->expect('sin*2*x',
                      'sin*2*x',
                      [],
                      true, false);

        $this->expect('sin[2*x]',
                      'sin[2*x]',
                      [],
                      true, false);

        $this->expect('sinh(x)',
                      'sinh(x)',
                      [],
                      true, false);

        $this->expect('sqr(x)',
                      'sqr(x)',
                      [],
                      true, false);

        $this->expect('sqrt(+x)',
                      'sqrt(+x)',
                      [],
                      true, false);

        $this->expect('sqrt(x)',
                      'sqrt(x)',
                      [],
                      true, false);

        $this->expect('stackvector(a)',
                      'stackvector(a)',
                      [],
                      true, false);

        $this->expect('sum(k^n,n,0,3)',
                      'sum(k^n,n,0,3)',
                      [],
                      true, false);

        $this->expect('switch(x,a,y,b,c)',
                      'switch(x,a,y,b,c)',
                      [],
                      true, false);

        $this->expect('tan(x)',
                      'tan(x)',
                      [],
                      true, false);

        $this->expect('tanh(x)',
                      'tanh(x)',
                      [],
                      true, false);

        $this->expect('tau',
                      'tau',
                      [],
                      true, false);

        $this->expect('theta',
                      'theta',
                      [],
                      true, false);

        $this->expect('true',
                      'true',
                      [],
                      true, false);

        $this->expect('upsilon',
                      'upsilon',
                      [],
                      true, false);

        $this->expect('x',
                      'x',
                      [],
                      true, false);

        $this->expect('x * y',
                      'x*y',
                      [],
                      true, false);

        $this->expect('x + 1',
                      'x+1',
                      [],
                      true, false);

        $this->expect('x + y',
                      'x+y',
                      [],
                      true, false);

        $this->expect('x - y',
                      'x-y',
                      [],
                      true, false);

        $this->expect('x / y',
                      'x/y',
                      [],
                      true, false);

        $this->expect('x < y',
                      'x < y',
                      [],
                      true, false);

        $this->expect('x <= y',
                      'x <= y',
                      [],
                      true, false);

        $this->expect('x = y',
                      'x = y',
                      [],
                      true, false);

        $this->expect('x > y',
                      'x > y',
                      [],
                      true, false);

        $this->expect('x >= y',
                      'x >= y',
                      [],
                      true, false);

        $this->expect('x ^ y',
                      'x^y',
                      [],
                      true, false);

        $this->expect('x and',
                      'x*and',
                      [],
                      true, false);

        $this->expect('x and y',
                      'x and y',
                      [],
                      true, false);

        $this->expect('x divides y',
                      'x*divides*y',
                      [],
                      true, false);

        $this->expect('x or y',
                      'x or y',
                      [],
                      true, false);

        $this->expect('x xor y',
                      'x xor y',
                      [],
                      true, false);

        $this->expect('x y',
                      'x*y',
                      [],
                      true, false);

        $this->expect('x!',
                      'x!',
                      [],
                      true, false);

        $this->expect('x()',
                      'x()',
                      [],
                      true, false);

        $this->expect('x(2+1)',
                      'x(2+1)',
                      [],
                      true, false);

        $this->expect('x(sin(t)+1)',
                      'x(sin(t)+1)',
                      [],
                      true, false);

        $this->expect('x(t+1)',
                      'x(t+1)',
                      [],
                      true, false);

        $this->expect('x(x+1)',
                      'x(x+1)',
                      [],
                      true, false);

        $this->expect('x*2^y',
                      'x*2^y',
                      [],
                      true, false);

        $this->expect('x*divides*y',
                      'x*divides*y',
                      [],
                      true, false);

        $this->expect('x*i^3',
                      'x*i^3',
                      [],
                      true, false);

        $this->expect('x*y*z',
                      'x*y*z',
                      [],
                      true, false);

        $this->expect('x*y^z',
                      'x*y^z',
                      [],
                      true, false);

        $this->expect('x+ 1',
                      'x+1',
                      [],
                      true, false);

        $this->expect('x+1',
                      'x+1',
                      [],
                      true, false);

        $this->expect('x+y+z',
                      'x+y+z',
                      [],
                      true, false);

        $this->expect('x/y/z',
                      'x/y/z',
                      [],
                      true, false);

        $this->expect('x1',
                      'x1',
                      [],
                      true, false);

        $this->expect('x<1 and x>1',
                      'x < 1 and x > 1',
                      [],
                      true, false);

        $this->expect('x=+-sqrt(2)',
                      'x = +-sqrt(2)',
                      [],
                      true, false);

        $this->expect('x=1 or 2',
                      'x = 1 or 2',
                      [],
                      true, false);

        $this->expect('x=1 or 2 or 3',
                      'x = 1 or 2 or 3',
                      [],
                      true, false);

        $this->expect('x=1 or x=2',
                      'x = 1 or x = 2',
                      [],
                      true, false);

        $this->expect('x^-1',
                      'x^-1',
                      [],
                      true, false);

        $this->expect('x^-y',
                      'x^-y',
                      [],
                      true, false);

        $this->expect('x^7/7-2*x^6/3-4*x^3/3',
                      'x^7/7-2*x^6/3-4*x^3/3',
                      [],
                      true, false);

        $this->expect('x^f(x)',
                      'x^f(x)',
                      [],
                      true, false);

        $this->expect('x^y',
                      'x^y',
                      [],
                      true, false);

        $this->expect('x^y^z',
                      'x^y^z',
                      [],
                      true, false);

        $this->expect('x_1',
                      'x_1',
                      [],
                      true, false);

        $this->expect('Xy_12',
                      'Xy_12',
                      [],
                      true, false);

        $this->expect('x_y',
                      'x_y',
                      [],
                      true, false);

        $this->expect('x_y_z',
                      'x_y_z',
                      [],
                      true, false);

        $this->expect('x_y_1',
                      'x_y_1',
                      [],
                      true, false);

        $this->expect('x_12_z',
                      'x_12_z',
                      [],
                      true, false);

        $this->expect('xy_zw',
                      'xy_zw',
                      [],
                      true, false);

        $this->expect('xy_12',
                      'xy_12',
                      [],
                      true, false);

        $this->expect('M_2*x^2+M_1*x+M_0',
                      'M_2*x^2+M_1*x+M_0',
                      [],
                      true, false);

        $this->expect('xi',
                      'xi',
                      [],
                      true, false);

        $this->expect('xsin(1)',
                      'xsin(1)',
                      [],
                      true, false);

        $this->expect('xy',
                      'xy',
                      [],
                      true, false);

        $this->expect('y^2-2*y-0.5',
                      'y^2-2*y-0.5',
                      [],
                      true, false);

        $this->expect('y^2-2*y-8',
                      'y^2-2*y-8',
                      [],
                      true, false);

        $this->expect('y^3-2*y^2-8*y',
                      'y^3-2*y^2-8*y',
                      [],
                      true, false);

        $this->expect('y^z * x',
                      'y^z*x',
                      [],
                      true, false);

        $this->expect('ycos(2)',
                      'ycos(2)',
                      [],
                      true, false);

        $this->expect('zeta',
                      'zeta',
                      [],
                      true, false);

        $this->expect('{1,2,3.4}',
                      '{1,2,3.4}',
                      [],
                      true, false);

        $this->expect('{1}',
                      '{1}',
                      [],
                      true, false);

        $this->expect('{x, y, z }',
                      '{x,y,z}',
                      [],
                      true, false);

        $this->expect('{}',
                      '{}',
                      [],
                      true, false);

        $this->expect('|x|',
                      'abs(x)',
                      [],
                      true, false);

        $this->expect('rand(["+","-"])(x,y)',
                      'rand(["+","-"])(x,y)',
                      [],
                      true, false);

        $this->expect('rand(["sin","cos","system"])(x)',
                      'rand(["sin","cos","system"])(x)',
                      [],
                      true, false);

        $this->expect('1.2*m**2',
                      '1.2*m**2',
                      [],
                      true, false);

        $this->expect('1.2*mˆ2',
                      '1.2*m^2',
                      [],
                      true, false);

        $this->expect('/* Comment */x+1',
                      '/* Comment */x+1',
                      [],
                      true, false);

        $this->expect('/* Comment **/x+1',
                      '/* Comment **/x+1',
                      [],
                      true, false);

        $this->expect('/** Comment */x+1',
                      '/** Comment */x+1',
                      [],
                      true, false);

        $this->expect('/** Comment **/x+1',
                      '/** Comment **/x+1',
                      [],
                      true, false);

        $this->expect('/*@ Comment @*/x+1',
                      '/*@ Comment @*/x+1',
                      [],
                      true, false);

        $this->expect('"A string that needs sanitising <script>bad stuff</script>."',
                      '"A string that needs sanitising <script>bad stuff</script>."',
                      [],
                      true, false);

    }

    public function test_non_affected_no_units() {
        $this->security = new stack_cas_security(false);
        $this->filter = stack_parsing_rule_factory::get_by_common_name('105_no_grouppings');

        $this->expect('"+"(a,b)',
                      '"+"(a,b)',
                      [],
                      true, false);

        $this->expect('"1+1"',
                      '"1+1"',
                      [],
                      true, false);

        $this->expect('"Hello world"',
                      '"Hello world"',
                      [],
                      true, false);

        $this->expect('%e^x',
                      '%e^x',
                      [],
                      true, false);

        $this->expect('2pi r^2',
                      '2*pi*r^2',
                      [],
                      true, false);

        $this->expect('2pir^2',
                      '2*pir^2',
                      [],
                      true, false);

        $this->expect("'diff(x,y)",
                      "'diff(x,y)",
                      [],
                      true, false);

        $this->expect("'int(x,y)",
                      "'int(x,y)",
                      [],
                      true, false);

        $this->expect('()x',
                      '()*x',
                      [],
                      true, false);

        $this->expect('(a,b,c)',
                      '(a,b,c)',
                      [],
                      true, false);

        $this->expect('((a,b),c)',
                      '((a,b),c)',
                      [],
                      true, false);

        $this->expect('(a,(b,c))',
                      '(a,(b,c))',
                      [],
                      true, false);

        $this->expect('{(a,b),(x,y)}',
                      '{(a,b),(x,y)}',
                      [],
                      true, false);

        $this->expect('+-1',
                      '+-1',
                      [],
                      true, false);

        $this->expect('+0.2',
                      '+0.2',
                      [],
                      true, false);

        $this->expect('+1',
                      '+1',
                      [],
                      true, false);

        $this->expect('+e',
                      '+e',
                      [],
                      true, false);

        $this->expect('+i',
                      '+i',
                      [],
                      true, false);

        $this->expect('+pi',
                      '+pi',
                      [],
                      true, false);

        $this->expect('+x',
                      '+x',
                      [],
                      true, false);

        $this->expect('-1',
                      '-1',
                      [],
                      true, false);

        $this->expect('-1234',
                      '-1234',
                      [],
                      true, false);

        $this->expect('-0.2',
                      '-0.2',
                      [],
                      true, false);

        $this->expect('-10/-1',
                      '-10/-1',
                      [],
                      true, false);

        $this->expect('-3+i',
                      '-3+i',
                      [],
                      true, false);

        $this->expect('-3x(1+x)',
                      '-3*x(1+x)',
                      [],
                      true, false);

        $this->expect('-b(5-b)',
                      '-b(5-b)',
                      [],
                      true, false);

        $this->expect('-e',
                      '-e',
                      [],
                      true, false);

        $this->expect('-i',
                      '-i',
                      [],
                      true, false);

        $this->expect('-pi',
                      '-pi',
                      [],
                      true, false);

        $this->expect('-x',
                      '-x',
                      [],
                      true, false);

        $this->expect('-x(1+x)',
                      '-x(1+x)',
                      [],
                      true, false);

        $this->expect('-x[3]',
                      '-x[3]',
                      [],
                      true, false);

        $this->expect('.1',
                      '.1',
                      [],
                      true, false);

        $this->expect('-0.2433 + 0.1111',
                      '-0.2433+0.1111',
                      [],
                      true, false);

        $this->expect('-0.2433e23 + 0.1111e-45 * 0.23e12 / -0.11e-11',
                      '-0.2433E23+0.1111E-45*0.23E12/-0.11E-11',
                      [],
                      true, false);

        $this->expect('-35.3 * 10^23',
                      '-35.3*10^23',
                      [],
                      true, false);

        $this->expect('0..1',
                      '0. . 1',
                      [],
                      true, false);

        $this->expect('0.1..1.2',
                      '0.1 . .1 . 2',
                      [],
                      true, false);

        $this->expect('0.1.1.2',
                      '0.1 . 1.2',
                      [],
                      true, false);

        $this->expect('0.1. 1.2',
                      '0.1 . 1.2',
                      [],
                      true, false);

        $this->expect('1',
                      '1',
                      [],
                      true, false);

        $this->expect('1234',
                      '1234',
                      [],
                      true, false);

        $this->expect('1 x',
                      '1*x',
                      [],
                      true, false);

        $this->expect('1+2i',
                      '1+2*i',
                      [],
                      true, false);

        $this->expect('1+i',
                      '1+i',
                      [],
                      true, false);

        $this->expect('1-x(1+x)',
                      '1-x(1+x)',
                      [],
                      true, false);

        $this->expect('1/0',
                      '1/0',
                      [],
                      true, false);

        $this->expect('1/2',
                      '1/2',
                      [],
                      true, false);

        $this->expect('1/sin(+x)',
                      '1/sin(+x)',
                      [],
                      true, false);

        $this->expect('1<=x<y^2',
                      '1 <= x < y^2',
                      [],
                      true, false);

        $this->expect('1<x<3',
                      '1 < x < 3',
                      [],
                      true, false);

        $this->expect('1E+3',
                      '1E+3',
                      [],
                      true, false);

        $this->expect('1E3',
                      '1E3',
                      [],
                      true, false);

        $this->expect('1 E 3',
                      '1*E*3',
                      [],
                      true, false);

        $this->expect('23.2*x*10^5',
                      '23.2*x*10^5',
                      [],
                      true, false);

        $this->expect('23.2 x 10^5',
                      '23.2*x*10^5',
                      [],
                      true, false);

        $this->expect('23.2x10^5',
                      '23.2*x10^5',
                      [],
                      true, false);

        $this->expect('23.2x 10^5',
                      '23.2*x*10^5',
                      [],
                      true, false);

        $this->expect('23.2 x10^5',
                      '23.2*x10^5',
                      [],
                      true, false);

        $this->expect('1E23*10^45',
                      '1E23*10^45',
                      [],
                      true, false);

        $this->expect('9.81x10^2*m/s',
                      '9.81*x10^2*m/s',
                      [],
                      true, false);

        $this->expect('9.81x*10^2*m/s',
                      '9.81*x*10^2*m/s',
                      [],
                      true, false);

        $this->expect('1x',
                      '1*x',
                      [],
                      true, false);

        $this->expect('2*e',
                      '2*e',
                      [],
                      true, false);

        $this->expect('2*i',
                      '2*i',
                      [],
                      true, false);

        $this->expect('2*i^3',
                      '2*i^3',
                      [],
                      true, false);

        $this->expect('2*pi',
                      '2*pi',
                      [],
                      true, false);

        $this->expect('2/4',
                      '2/4',
                      [],
                      true, false);

        $this->expect('2^y*x',
                      '2^y*x',
                      [],
                      true, false);

        $this->expect('3-i',
                      '3-i',
                      [],
                      true, false);

        $this->expect('3 5',
                      '3*5',
                      [],
                      true, false);

        $this->expect('3.14 5',
                      '3.14*5',
                      [],
                      true, false);

        $this->expect('3 5.2789',
                      '3*5.2789',
                      [],
                      true, false);

        $this->expect('3.14 5.2789',
                      '3.14*5.2789',
                      [],
                      true, false);

        $this->expect('33 578 32',
                      '33*578*32',
                      [],
                      true, false);

        $this->expect('9 8 7.6',
                      '9*8*7.6',
                      [],
                      true, false);

        $this->expect('9 8.5 7.6',
                      '9*8.5*7.6',
                      [],
                      true, false);

        $this->expect('3b+5/a(x)',
                      '3*b+5/a(x)',
                      [],
                      true, false);

        $this->expect('3beta_47',
                      '3*beta_47',
                      [],
                      true, false);

        $this->expect('3e-2',
                      '3E-2',
                      [],
                      true, false);

        $this->expect('3e2',
                      '3E2',
                      [],
                      true, false);

        $this->expect('3E2',
                      '3E2',
                      [],
                      true, false);

        $this->expect('7x(2+1)',
                      '7*x(2+1)',
                      [],
                      true, false);

        $this->expect('Bgcd(3,2)',
                      'Bgcd(3,2)',
                      [],
                      true, false);

        $this->expect('In(x)',
                      'In(x)',
                      [],
                      true, false);

        $this->expect('Sin(x)',
                      'Sin(x)',
                      [],
                      true, false);

        $this->expect('3.75*Btu',
                      '3.75*Btu',
                      [],
                      true, false);

        $this->expect('X',
                      'X',
                      [],
                      true, false);

        $this->expect('["a"]',
                      '["a"]',
                      [],
                      true, false);

        $this->expect('[+1,+2]',
                      '[+1,+2]',
                      [],
                      true, false);

        $this->expect('[-1,-2]',
                      '[-1,-2]',
                      [],
                      true, false);

        $this->expect('[1 < x,y < 1 or y > 7]',
                      '[1 < x,y < 1 or y > 7]',
                      [],
                      true, false);

        $this->expect('[1,+2]',
                      '[1,+2]',
                      [],
                      true, false);

        $this->expect('[1,-2]',
                      '[1,-2]',
                      [],
                      true, false);

        $this->expect('[1,2,3.4]',
                      '[1,2,3.4]',
                      [],
                      true, false);

        $this->expect('[1,true,"a"]',
                      '[1,true,"a"]',
                      [],
                      true, false);

        $this->expect('[1<x,1<y<3]',
                      '[1 < x,1 < y < 3]',
                      [],
                      true, false);

        $this->expect('[1<x,x<3]',
                      '[1 < x,x < 3]',
                      [],
                      true, false);

        $this->expect('[1]',
                      '[1]',
                      [],
                      true, false);

        $this->expect('[[1,2],[3,4]]',
                      '[[1,2],[3,4]]',
                      [],
                      true, false);

        $this->expect('[]',
                      '[]',
                      [],
                      true, false);

        $this->expect('[x, y, z ]',
                      '[x,y,z]',
                      [],
                      true, false);

        $this->expect('a ** b',
                      'a**b',
                      [],
                      true, false);

        $this->expect('a +++ b',
                      'a+++b',
                      [],
                      true, false);

        $this->expect('a --- b',
                      'a---b',
                      [],
                      true, false);

        $this->expect('a(x)',
                      'a(x)',
                      [],
                      true, false);

        $this->expect('a++b',
                      'a++b',
                      [],
                      true, false);

        $this->expect('a+-b',
                      'a+-b',
                      [],
                      true, false);

        $this->expect('a,b,c',
                      'a,b=true,c=true',
                      [],
                      true, false);

        $this->expect('a-+b',
                      'a-+b',
                      [],
                      true, false);

        $this->expect('a/b/c',
                      'a/b/c',
                      [],
                      true, false);

        $this->expect('a1',
                      'a1',
                      [],
                      true, false);

        $this->expect('a9b',
                      'a9b',
                      [],
                      true, false);

        $this->expect('ab98cd',
                      'ab98cd',
                      [],
                      true, false);

        $this->expect('aXy1',
                      'aXy1',
                      [],
                      true, false);

        $this->expect('a[1,2]',
                      'a[1,2]',
                      [],
                      true, false);

        $this->expect('a[2]',
                      'a[2]',
                      [],
                      true, false);

        $this->expect('a[n+1]',
                      'a[n+1]',
                      [],
                      true, false);

        $this->expect('a^-b',
                      'a^-b',
                      [],
                      true, false);

        $this->expect('a^b',
                      'a^b',
                      [],
                      true, false);

        $this->expect('a_b',
                      'a_b',
                      [],
                      true, false);

        $this->expect('abs(13)',
                      'abs(13)',
                      [],
                      true, false);

        $this->expect('abs(x)',
                      'abs(x)',
                      [],
                      true, false);

        $this->expect('alpha',
                      'alpha',
                      [],
                      true, false);

        $this->expect('arcsin(x)',
                      'arcsin(x)',
                      [],
                      true, false);

        $this->expect('asin(x)',
                      'asin(x)',
                      [],
                      true, false);

        $this->expect('asinh(x)',
                      'asinh(x)',
                      [],
                      true, false);

        $this->expect('b(b+1)',
                      'b(b+1)',
                      [],
                      true, false);

        $this->expect('b/a(x)',
                      'b/a(x)',
                      [],
                      true, false);

        $this->expect('beta',
                      'beta',
                      [],
                      true, false);

        $this->expect('beta_47',
                      'beta_47',
                      [],
                      true, false);

        $this->expect('bsin(t)',
                      'bsin(t)',
                      [],
                      true, false);

        $this->expect('ceiling(x)',
                      'ceiling(x)',
                      [],
                      true, false);

        $this->expect('chi',
                      'chi',
                      [],
                      true, false);

        $this->expect('comb(x,y)',
                      'comb(x,y)',
                      [],
                      true, false);

        $this->expect('cos(2x)(x+1)',
                      'cos(2*x)(x+1)',
                      [],
                      true, false);

        $this->expect('cos(x)',
                      'cos(x)',
                      [],
                      true, false);

        $this->expect('cosec(x)',
                      'cosec(x)',
                      [],
                      true, false);

        $this->expect('cosech(x)',
                      'cosech(x)',
                      [],
                      true, false);

        $this->expect('cosh(x)',
                      'cosh(x)',
                      [],
                      true, false);

        $this->expect('cot(x)',
                      'cot(x)',
                      [],
                      true, false);

        $this->expect('coth(x)',
                      'coth(x)',
                      [],
                      true, false);

        $this->expect('csc(x)',
                      'csc(x)',
                      [],
                      true, false);

        $this->expect('delta',
                      'delta',
                      [],
                      true, false);

        $this->expect('diff(sin(x))',
                      'diff(sin(x))',
                      [],
                      true, false);

        $this->expect('diff(sin(x),x)',
                      'diff(sin(x),x)',
                      [],
                      true, false);

        $this->expect('diff(x,y)',
                      'diff(x,y)',
                      [],
                      true, false);

        $this->expect('dosomething(x,y,z)',
                      'dosomething(x,y,z)',
                      [],
                      true, false);

        $this->expect('e',
                      'e',
                      [],
                      true, false);

        $this->expect('e*2',
                      'e*2',
                      [],
                      true, false);

        $this->expect('e^x',
                      'e^x',
                      [],
                      true, false);

        $this->expect('epsilon',
                      'epsilon',
                      [],
                      true, false);

        $this->expect('eta',
                      'eta',
                      [],
                      true, false);

        $this->expect('exp(x)',
                      'exp(x)',
                      [],
                      true, false);

        $this->expect('f(x)',
                      'f(x)',
                      [],
                      true, false);

        $this->expect('f(x)(2)',
                      'f(x)(2)',
                      [],
                      true, false);

        $this->expect('fact(13)',
                      'fact(13)',
                      [],
                      true, false);

        $this->expect('false',
                      'false',
                      [],
                      true, false);

        $this->expect('floor(x)',
                      'floor(x)',
                      [],
                      true, false);

        $this->expect('gamma',
                      'gamma',
                      [],
                      true, false);

        $this->expect('gcd(x,y)',
                      'gcd(x,y)',
                      [],
                      true, false);

        $this->expect('gcf(x,y)',
                      'gcf(x,y)',
                      [],
                      true, false);

        $this->expect('i',
                      'i',
                      [],
                      true, false);

        $this->expect('i(1+i)',
                      'i(1+i)',
                      [],
                      true, false);

        $this->expect('i(4)',
                      'i(4)',
                      [],
                      true, false);

        $this->expect('i*2',
                      'i*2',
                      [],
                      true, false);

        $this->expect('inf',
                      'inf',
                      [],
                      true, false);

        $this->expect('int(sin(x))',
                      'int(sin(x))',
                      [],
                      true, false);

        $this->expect('int(x,y)',
                      'int(x,y)',
                      [],
                      true, false);

        $this->expect('iota',
                      'iota',
                      [],
                      true, false);

        $this->expect('j',
                      'j',
                      [],
                      true, false);

        $this->expect('kappa',
                      'kappa',
                      [],
                      true, false);

        $this->expect('lambda',
                      'lambda',
                      [],
                      true, false);

        $this->expect('len(x)',
                      'len(x)',
                      [],
                      true, false);

        $this->expect('length(x)',
                      'length(x)',
                      [],
                      true, false);

        $this->expect('lg(10^3)',
                      'lg(10^3)',
                      [],
                      true, false);

        $this->expect('lg(x)',
                      'lg(x)',
                      [],
                      true, false);

        $this->expect('lg(x,a)',
                      'lg(x,a)',
                      [],
                      true, false);

        $this->expect('limit(y,x,3)',
                      'limit(y,x,3)',
                      [],
                      true, false);

        $this->expect('ln(x)',
                      'ln(x)',
                      [],
                      true, false);

        $this->expect('ln*x',
                      'ln*x',
                      [],
                      true, false);

        $this->expect('log(2x)/x+1/2',
                      'log(2*x)/x+1/2',
                      [],
                      true, false);

        $this->expect('log(x)',
                      'log(x)',
                      [],
                      true, false);

        $this->expect('log10(x)',
                      'log10(x)',
                      [],
                      true, false);

        $this->expect('log_10(x)',
                      'log_10(x)',
                      [],
                      true, false);

        $this->expect('log_2(a)',
                      'log_2(a)',
                      [],
                      true, false);

        $this->expect('log_a(b)*log_b(c)',
                      'log_a(b)*log_b(c)',
                      [],
                      true, false);

        $this->expect('log_x:log_x(a)',
                      'log_x:log_x(a)',
                      [],
                      true, false);

        $this->expect('matrix([a,b],[c,d])',
                      'matrix([a,b],[c,d])',
                      [],
                      true, false);

        $this->expect('mod(x,y)',
                      'mod(x,y)',
                      [],
                      true, false);

        $this->expect('mu',
                      'mu',
                      [],
                      true, false);

        $this->expect('not x',
                      'not x',
                      [],
                      true, false);

        $this->expect('nu',
                      'nu',
                      [],
                      true, false);

        $this->expect('omega',
                      'omega',
                      [],
                      true, false);

        $this->expect('omicron',
                      'omicron',
                      [],
                      true, false);

        $this->expect('p=?*s',
                      'p = QMCHAR*s',
                      [],
                      true, false);

        $this->expect('partialdiff(x,y,1)',
                      'partialdiff(x,y,1)',
                      [],
                      true, false);

        $this->expect('perm(x,y)',
                      'perm(x,y)',
                      [],
                      true, false);

        $this->expect('phi',
                      'phi',
                      [],
                      true, false);

        $this->expect('pi',
                      'pi',
                      [],
                      true, false);

        $this->expect('pi*2',
                      'pi*2',
                      [],
                      true, false);

        $this->expect('plot(x^2,[x,-1,1])',
                      'plot(x^2,[x,-1,1])',
                      [],
                      true, false);

        $this->expect('plot2d(x^2,[x,-1,1])',
                      'plot2d(x^2,[x,-1,1])',
                      [],
                      true, false);

        $this->expect('product(cos(k*x),k,1,3)',
                      'product(cos(k*x),k,1,3)',
                      [],
                      true, false);

        $this->expect('psi',
                      'psi',
                      [],
                      true, false);

        $this->expect('rho',
                      'rho',
                      [],
                      true, false);

        $this->expect('root(2,-3)',
                      'root(2,-3)',
                      [],
                      true, false);

        $this->expect('root(x)',
                      'root(x)',
                      [],
                      true, false);

        $this->expect('root(x,3)',
                      'root(x,3)',
                      [],
                      true, false);

        $this->expect('sec(x)',
                      'sec(x)',
                      [],
                      true, false);

        $this->expect('sech(x)',
                      'sech(x)',
                      [],
                      true, false);

        $this->expect('set(x, y, z)',
                      'set(x,y,z)',
                      [],
                      true, false);

        $this->expect('sgn(x)',
                      'sgn(x)',
                      [],
                      true, false);

        $this->expect('sigma',
                      'sigma',
                      [],
                      true, false);

        $this->expect('sign(x)',
                      'sign(x)',
                      [],
                      true, false);

        $this->expect('sim(x)',
                      'sim(x)',
                      [],
                      true, false);

        $this->expect('sin',
                      'sin',
                      [],
                      true, false);

        $this->expect('sin x',
                      'sin*x',
                      [],
                      true, false);

        $this->expect('sin(x)',
                      'sin(x)',
                      [],
                      true, false);

        $this->expect('sin*2*x',
                      'sin*2*x',
                      [],
                      true, false);

        $this->expect('sin[2*x]',
                      'sin[2*x]',
                      [],
                      true, false);

        $this->expect('sinh(x)',
                      'sinh(x)',
                      [],
                      true, false);

        $this->expect('sqr(x)',
                      'sqr(x)',
                      [],
                      true, false);

        $this->expect('sqrt(+x)',
                      'sqrt(+x)',
                      [],
                      true, false);

        $this->expect('sqrt(x)',
                      'sqrt(x)',
                      [],
                      true, false);

        $this->expect('stackvector(a)',
                      'stackvector(a)',
                      [],
                      true, false);

        $this->expect('sum(k^n,n,0,3)',
                      'sum(k^n,n,0,3)',
                      [],
                      true, false);

        $this->expect('switch(x,a,y,b,c)',
                      'switch(x,a,y,b,c)',
                      [],
                      true, false);

        $this->expect('tan(x)',
                      'tan(x)',
                      [],
                      true, false);

        $this->expect('tanh(x)',
                      'tanh(x)',
                      [],
                      true, false);

        $this->expect('tau',
                      'tau',
                      [],
                      true, false);

        $this->expect('theta',
                      'theta',
                      [],
                      true, false);

        $this->expect('true',
                      'true',
                      [],
                      true, false);

        $this->expect('upsilon',
                      'upsilon',
                      [],
                      true, false);

        $this->expect('x',
                      'x',
                      [],
                      true, false);

        $this->expect('x * y',
                      'x*y',
                      [],
                      true, false);

        $this->expect('x + 1',
                      'x+1',
                      [],
                      true, false);

        $this->expect('x + y',
                      'x+y',
                      [],
                      true, false);

        $this->expect('x - y',
                      'x-y',
                      [],
                      true, false);

        $this->expect('x / y',
                      'x/y',
                      [],
                      true, false);

        $this->expect('x < y',
                      'x < y',
                      [],
                      true, false);

        $this->expect('x <= y',
                      'x <= y',
                      [],
                      true, false);

        $this->expect('x = y',
                      'x = y',
                      [],
                      true, false);

        $this->expect('x > y',
                      'x > y',
                      [],
                      true, false);

        $this->expect('x >= y',
                      'x >= y',
                      [],
                      true, false);

        $this->expect('x ^ y',
                      'x^y',
                      [],
                      true, false);

        $this->expect('x and',
                      'x*and',
                      [],
                      true, false);

        $this->expect('x and y',
                      'x and y',
                      [],
                      true, false);

        $this->expect('x divides y',
                      'x*divides*y',
                      [],
                      true, false);

        $this->expect('x or y',
                      'x or y',
                      [],
                      true, false);

        $this->expect('x xor y',
                      'x xor y',
                      [],
                      true, false);

        $this->expect('x y',
                      'x*y',
                      [],
                      true, false);

        $this->expect('x!',
                      'x!',
                      [],
                      true, false);

        $this->expect('x()',
                      'x()',
                      [],
                      true, false);

        $this->expect('x(2+1)',
                      'x(2+1)',
                      [],
                      true, false);

        $this->expect('x(sin(t)+1)',
                      'x(sin(t)+1)',
                      [],
                      true, false);

        $this->expect('x(t+1)',
                      'x(t+1)',
                      [],
                      true, false);

        $this->expect('x(x+1)',
                      'x(x+1)',
                      [],
                      true, false);

        $this->expect('x*2^y',
                      'x*2^y',
                      [],
                      true, false);

        $this->expect('x*divides*y',
                      'x*divides*y',
                      [],
                      true, false);

        $this->expect('x*i^3',
                      'x*i^3',
                      [],
                      true, false);

        $this->expect('x*y*z',
                      'x*y*z',
                      [],
                      true, false);

        $this->expect('x*y^z',
                      'x*y^z',
                      [],
                      true, false);

        $this->expect('x+ 1',
                      'x+1',
                      [],
                      true, false);

        $this->expect('x+1',
                      'x+1',
                      [],
                      true, false);

        $this->expect('x+y+z',
                      'x+y+z',
                      [],
                      true, false);

        $this->expect('x/y/z',
                      'x/y/z',
                      [],
                      true, false);

        $this->expect('x1',
                      'x1',
                      [],
                      true, false);

        $this->expect('x<1 and x>1',
                      'x < 1 and x > 1',
                      [],
                      true, false);

        $this->expect('x=+-sqrt(2)',
                      'x = +-sqrt(2)',
                      [],
                      true, false);

        $this->expect('x=1 or 2',
                      'x = 1 or 2',
                      [],
                      true, false);

        $this->expect('x=1 or 2 or 3',
                      'x = 1 or 2 or 3',
                      [],
                      true, false);

        $this->expect('x=1 or x=2',
                      'x = 1 or x = 2',
                      [],
                      true, false);

        $this->expect('x^-1',
                      'x^-1',
                      [],
                      true, false);

        $this->expect('x^-y',
                      'x^-y',
                      [],
                      true, false);

        $this->expect('x^7/7-2*x^6/3-4*x^3/3',
                      'x^7/7-2*x^6/3-4*x^3/3',
                      [],
                      true, false);

        $this->expect('x^f(x)',
                      'x^f(x)',
                      [],
                      true, false);

        $this->expect('x^y',
                      'x^y',
                      [],
                      true, false);

        $this->expect('x^y^z',
                      'x^y^z',
                      [],
                      true, false);

        $this->expect('x_1',
                      'x_1',
                      [],
                      true, false);

        $this->expect('Xy_12',
                      'Xy_12',
                      [],
                      true, false);

        $this->expect('x_y',
                      'x_y',
                      [],
                      true, false);

        $this->expect('x_y_z',
                      'x_y_z',
                      [],
                      true, false);

        $this->expect('x_y_1',
                      'x_y_1',
                      [],
                      true, false);

        $this->expect('x_12_z',
                      'x_12_z',
                      [],
                      true, false);

        $this->expect('xy_zw',
                      'xy_zw',
                      [],
                      true, false);

        $this->expect('xy_12',
                      'xy_12',
                      [],
                      true, false);

        $this->expect('M_2*x^2+M_1*x+M_0',
                      'M_2*x^2+M_1*x+M_0',
                      [],
                      true, false);

        $this->expect('xi',
                      'xi',
                      [],
                      true, false);

        $this->expect('xsin(1)',
                      'xsin(1)',
                      [],
                      true, false);

        $this->expect('xy',
                      'xy',
                      [],
                      true, false);

        $this->expect('y^2-2*y-0.5',
                      'y^2-2*y-0.5',
                      [],
                      true, false);

        $this->expect('y^2-2*y-8',
                      'y^2-2*y-8',
                      [],
                      true, false);

        $this->expect('y^3-2*y^2-8*y',
                      'y^3-2*y^2-8*y',
                      [],
                      true, false);

        $this->expect('y^z * x',
                      'y^z*x',
                      [],
                      true, false);

        $this->expect('ycos(2)',
                      'ycos(2)',
                      [],
                      true, false);

        $this->expect('zeta',
                      'zeta',
                      [],
                      true, false);

        $this->expect('{1,2,3.4}',
                      '{1,2,3.4}',
                      [],
                      true, false);

        $this->expect('{1}',
                      '{1}',
                      [],
                      true, false);

        $this->expect('{x, y, z }',
                      '{x,y,z}',
                      [],
                      true, false);

        $this->expect('{}',
                      '{}',
                      [],
                      true, false);

        $this->expect('|x|',
                      'abs(x)',
                      [],
                      true, false);

        $this->expect('rand(["+","-"])(x,y)',
                      'rand(["+","-"])(x,y)',
                      [],
                      true, false);

        $this->expect('rand(["sin","cos","system"])(x)',
                      'rand(["sin","cos","system"])(x)',
                      [],
                      true, false);

        $this->expect('1.2*m**2',
                      '1.2*m**2',
                      [],
                      true, false);

        $this->expect('1.2*mˆ2',
                      '1.2*m^2',
                      [],
                      true, false);

        $this->expect('/* Comment */x+1',
                      '/* Comment */x+1',
                      [],
                      true, false);

        $this->expect('/* Comment **/x+1',
                      '/* Comment **/x+1',
                      [],
                      true, false);

        $this->expect('/** Comment */x+1',
                      '/** Comment */x+1',
                      [],
                      true, false);

        $this->expect('/** Comment **/x+1',
                      '/** Comment **/x+1',
                      [],
                      true, false);

        $this->expect('/*@ Comment @*/x+1',
                      '/*@ Comment @*/x+1',
                      [],
                      true, false);

        $this->expect('"A string that needs sanitising <script>bad stuff</script>."',
                      '"A string that needs sanitising <script>bad stuff</script>."',
                      [],
                      true, false);

    }
}

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
 * @covers \ast_filter_996_call_modification
 */

class ast_filter_996_call_modification_auto_generated_test extends qtype_stack_ast_testcase {

    public function test_affected_no_units() {
        $this->security = new stack_cas_security(false);
        $this->filter = stack_parsing_rule_factory::get_by_common_name('996_call_modification');

        $this->expect('"+"(a,b)',
                      '(%_C("+"),"+"(a,b))',
                      array(),
                      true, false);

        $this->expect('(-b+-sqrt(b^2))/(2*a)',
                      '(-b+-(%_C(sqrt),sqrt(b^2)))/(2*a)',
                      array(),
                      true, false);

        $this->expect('(x+2)(x+3)',
                      '(%_C((x+2)),(x+2)(x+3))',
                      array(),
                      true, false);

        $this->expect('-(1/512) + i(sqrt(3)/512)',
                      '-(1/512)+(%_C(i),i((%_C(sqrt),sqrt(3))/512))',
                      array(),
                      true, false);

        $this->expect('-3x(1+x)',
                      '-3*(%_C(x),x(1+x))',
                      array(),
                      true, false);

        $this->expect('-b(5-b)',
                      '(%_C(b),-b(5-b))',
                      array(),
                      true, false);

        $this->expect('-x(1+x)',
                      '(%_C(x),-x(1+x))',
                      array(),
                      true, false);

        $this->expect('1-x(1+x)',
                      '1-(%_C(x),x(1+x))',
                      array(),
                      true, false);

        $this->expect('1/sin(+x)',
                      '1/(%_C(sin),sin(+x))',
                      array(),
                      true, false);

        $this->expect('2+log_x(1/(x+b))*x^2',
                      '2+(%_C(log_x),log_x(1/(x+b)))*x^2',
                      array(),
                      true, false);

        $this->expect('3b+5/a(x)',
                      '3*b+5/(%_C(a),a(x))',
                      array(),
                      true, false);

        $this->expect('7x(2+1)',
                      '7*(%_C(x),x(2+1))',
                      array(),
                      true, false);

        $this->expect('Bgcd(3,2)',
                      '(%_C(Bgcd),Bgcd(3,2))',
                      array(),
                      true, false);

        $this->expect('In(x)',
                      '(%_C(In),In(x))',
                      array(),
                      true, false);

        $this->expect('Sin(x)',
                      '(%_C(Sin),Sin(x))',
                      array(),
                      true, false);

        $this->expect('a(x)',
                      '(%_C(a),a(x))',
                      array(),
                      true, false);

        $this->expect('a/(a(x+1)+2)',
                      'a/((%_C(a),a(x+1))+2)',
                      array(),
                      true, false);

        $this->expect('abs(13)',
                      '(%_C(abs),abs(13))',
                      array(),
                      true, false);

        $this->expect('abs(x)',
                      '(%_C(abs),abs(x))',
                      array(),
                      true, false);

        $this->expect('arcsin(x)',
                      '(%_C(arcsin),arcsin(x))',
                      array(),
                      true, false);

        $this->expect('asin(x)',
                      '(%_C(asin),asin(x))',
                      array(),
                      true, false);

        $this->expect('asinh(x)',
                      '(%_C(asinh),asinh(x))',
                      array(),
                      true, false);

        $this->expect('b(b+1)',
                      '(%_C(b),b(b+1))',
                      array(),
                      true, false);

        $this->expect('b/a(x)',
                      'b/(%_C(a),a(x))',
                      array(),
                      true, false);

        $this->expect('bsin(t)',
                      '(%_C(bsin),bsin(t))',
                      array(),
                      true, false);

        $this->expect('ceiling(x)',
                      '(%_C(ceiling),ceiling(x))',
                      array(),
                      true, false);

        $this->expect('comb(x,y)',
                      '(%_C(comb),comb(x,y))',
                      array(),
                      true, false);

        $this->expect('cos(2x)(x+1)',
                      '(%_C(cos),%_C(cos(2*x)),cos(2*x)(x+1))',
                      array(),
                      true, false);

        $this->expect('cos(x)',
                      '(%_C(cos),cos(x))',
                      array(),
                      true, false);

        $this->expect('cosec(x)',
                      '(%_C(cosec),cosec(x))',
                      array(),
                      true, false);

        $this->expect('cosech(x)',
                      '(%_C(cosech),cosech(x))',
                      array(),
                      true, false);

        $this->expect('cosh(x)',
                      '(%_C(cosh),cosh(x))',
                      array(),
                      true, false);

        $this->expect('cot(x)',
                      '(%_C(cot),cot(x))',
                      array(),
                      true, false);

        $this->expect('coth(x)',
                      '(%_C(coth),coth(x))',
                      array(),
                      true, false);

        $this->expect('csc(6*x)^2*(7*sin(6*x)*cos(7*x)-6*cos(6*x)*sin(7*x))',
                      '(%_C(csc),csc(6*x))^2*(7*(%_C(sin),sin(6*x))*(%_C(cos),cos(7*x))-6*(%_C(cos),cos(6*x))*(%_C(sin),sin(7*x)))',
                      array(),
                      true, false);

        $this->expect('csc(x)',
                      '(%_C(csc),csc(x))',
                      array(),
                      true, false);

        $this->expect('diff(sin(x))',
                      '(%_C(diff),diff((%_C(sin),sin(x))))',
                      array(),
                      true, false);

        $this->expect('diff(sin(x),x)',
                      '(%_C(diff),diff((%_C(sin),sin(x)),x))',
                      array(),
                      true, false);

        $this->expect('diff(x,y)',
                      '(%_C(diff),diff(x,y))',
                      array(),
                      true, false);

        $this->expect('dosomething(x,y,z)',
                      '(%_C(dosomething),dosomething(x,y,z))',
                      array(),
                      true, false);

        $this->expect('exp(x)',
                      '(%_C(exp),exp(x))',
                      array(),
                      true, false);

        $this->expect('f(x)',
                      '(%_C(f),f(x))',
                      array(),
                      true, false);

        $this->expect('f(x)(2)',
                      '(%_C(f),%_C(f(x)),f(x)(2))',
                      array(),
                      true, false);

        $this->expect('fact(13)',
                      '(%_C(fact),fact(13))',
                      array(),
                      true, false);

        $this->expect('floor(x)',
                      '(%_C(floor),floor(x))',
                      array(),
                      true, false);

        $this->expect('gcd(x,y)',
                      '(%_C(gcd),gcd(x,y))',
                      array(),
                      true, false);

        $this->expect('gcf(x,y)',
                      '(%_C(gcf),gcf(x,y))',
                      array(),
                      true, false);

        $this->expect('i(1+i)',
                      '(%_C(i),i(1+i))',
                      array(),
                      true, false);

        $this->expect('i(4)',
                      '(%_C(i),i(4))',
                      array(),
                      true, false);

        $this->expect('int(sin(x))',
                      '(%_C(int),int((%_C(sin),sin(x))))',
                      array(),
                      true, false);

        $this->expect('int(x,y)',
                      '(%_C(int),int(x,y))',
                      array(),
                      true, false);

        $this->expect('len(x)',
                      '(%_C(len),len(x))',
                      array(),
                      true, false);

        $this->expect('length(x)',
                      '(%_C(length),length(x))',
                      array(),
                      true, false);

        $this->expect('lg(10^3)',
                      '(%_C(lg),lg(10^3))',
                      array(),
                      true, false);

        $this->expect('lg(x)',
                      '(%_C(lg),lg(x))',
                      array(),
                      true, false);

        $this->expect('lg(x,a)',
                      '(%_C(lg),lg(x,a))',
                      array(),
                      true, false);

        $this->expect('limit(y,x,3)',
                      '(%_C(limit),limit(y,x,3))',
                      array(),
                      true, false);

        $this->expect('ln(x)',
                      '(%_C(ln),ln(x))',
                      array(),
                      true, false);

        $this->expect('log(2x)/x+1/2',
                      '(%_C(log),log(2*x))/x+1/2',
                      array(),
                      true, false);

        $this->expect('log(x)',
                      '(%_C(log),log(x))',
                      array(),
                      true, false);

        $this->expect('log10(x)',
                      '(%_C(log10),log10(x))',
                      array(),
                      true, false);

        $this->expect('log_10(x)',
                      '(%_C(log_10),log_10(x))',
                      array(),
                      true, false);

        $this->expect('log_2(a)',
                      '(%_C(log_2),log_2(a))',
                      array(),
                      true, false);

        $this->expect('log_a(b)*log_b(c)',
                      '(%_C(log_a),log_a(b))*(%_C(log_b),log_b(c))',
                      array(),
                      true, false);

        $this->expect('log_x(1/(x+b))',
                      '(%_C(log_x),log_x(1/(x+b)))',
                      array(),
                      true, false);

        $this->expect('log_x:log_x(a)',
                      'log_x:(%_C(log_x),log_x(a))',
                      array(),
                      true, false);

        $this->expect('matrix([a,b],[c,d])',
                      '(%_C(matrix),matrix([a,b],[c,d]))',
                      array(),
                      true, false);

        $this->expect('mod(x,y)',
                      '(%_C(mod),mod(x,y))',
                      array(),
                      true, false);

        $this->expect('partialdiff(x,y,1)',
                      '(%_C(partialdiff),partialdiff(x,y,1))',
                      array(),
                      true, false);

        $this->expect('perm(x,y)',
                      '(%_C(perm),perm(x,y))',
                      array(),
                      true, false);

        $this->expect('plot(x^2,[x,-1,1])',
                      '(%_C(plot),plot(x^2,[x,-1,1]))',
                      array(),
                      true, false);

        $this->expect('plot2d(x^2,[x,-1,1])',
                      '(%_C(plot2d),plot2d(x^2,[x,-1,1]))',
                      array(),
                      true, false);

        $this->expect('product(cos(k*x),k,1,3)',
                      '(%_C(product),product((%_C(cos),cos(k*x)),k,1,3))',
                      array(),
                      true, false);

        $this->expect('root(2,-3)',
                      '(%_C(root),root(2,-3))',
                      array(),
                      true, false);

        $this->expect('root(x)',
                      '(%_C(root),root(x))',
                      array(),
                      true, false);

        $this->expect('root(x,3)',
                      '(%_C(root),root(x,3))',
                      array(),
                      true, false);

        $this->expect('sec(x)',
                      '(%_C(sec),sec(x))',
                      array(),
                      true, false);

        $this->expect('sech(x)',
                      '(%_C(sech),sech(x))',
                      array(),
                      true, false);

        $this->expect('set(x, y, z)',
                      '(%_C(set),set(x,y,z))',
                      array(),
                      true, false);

        $this->expect('sgn(x)',
                      '(%_C(sgn),sgn(x))',
                      array(),
                      true, false);

        $this->expect('sign(x)',
                      '(%_C(sign),sign(x))',
                      array(),
                      true, false);

        $this->expect('sim(x)',
                      '(%_C(sim),sim(x))',
                      array(),
                      true, false);

        $this->expect('sin(x)',
                      '(%_C(sin),sin(x))',
                      array(),
                      true, false);

        $this->expect('sinh(x)',
                      '(%_C(sinh),sinh(x))',
                      array(),
                      true, false);

        $this->expect('sqr(x)',
                      '(%_C(sqr),sqr(x))',
                      array(),
                      true, false);

        $this->expect('sqrt(+x)',
                      '(%_C(sqrt),sqrt(+x))',
                      array(),
                      true, false);

        $this->expect('sqrt(x)',
                      '(%_C(sqrt),sqrt(x))',
                      array(),
                      true, false);

        $this->expect('stackvector(a)',
                      '(%_C(stackvector),stackvector(a))',
                      array(),
                      true, false);

        $this->expect('sum(k^n,n,0,3)',
                      '(%_C(sum),sum(k^n,n,0,3))',
                      array(),
                      true, false);

        $this->expect('switch(x,a,y,b,c)',
                      '(%_C(switch),switch(x,a,y,b,c))',
                      array(),
                      true, false);

        $this->expect('tan(x)',
                      '(%_C(tan),tan(x))',
                      array(),
                      true, false);

        $this->expect('tanh(x)',
                      '(%_C(tanh),tanh(x))',
                      array(),
                      true, false);

        $this->expect('x()',
                      '(%_C(x),x())',
                      array(),
                      true, false);

        $this->expect('x(2+1)',
                      '(%_C(x),x(2+1))',
                      array(),
                      true, false);

        $this->expect('x(sin(t)+1)',
                      '(%_C(x),x((%_C(sin),sin(t))+1))',
                      array(),
                      true, false);

        $this->expect('x(t+1)',
                      '(%_C(x),x(t+1))',
                      array(),
                      true, false);

        $this->expect('x(x+1)',
                      '(%_C(x),x(x+1))',
                      array(),
                      true, false);

        $this->expect('x=+-sqrt(2)',
                      'x = (%_C(sqrt),+-sqrt(2))',
                      array(),
                      true, false);

        $this->expect('x>1 or (x<1 and t<sin(x))',
                      'x > 1 or (x < 1 and t < (%_C(sin),sin(x)))',
                      array(),
                      true, false);

        $this->expect('x^f(x)',
                      'x^(%_C(f),f(x))',
                      array(),
                      true, false);

        $this->expect('xsin(1)',
                      '(%_C(xsin),xsin(1))',
                      array(),
                      true, false);

        $this->expect('ycos(2)',
                      '(%_C(ycos),ycos(2))',
                      array(),
                      true, false);

        $this->expect('|x|',
                      '(%_C(abs),abs(x))',
                      array(),
                      true, false);

        $this->expect('rand(["+","-"])(x,y)',
                      '(%_C(rand),%_C(rand(["+","-"])),rand(["+","-"])(x,y))',
                      array(),
                      true, false);

        $this->expect('rand(["sin","cos","system"])(x)',
                      '(%_C(rand),%_C(rand(["sin","cos","system"])),rand(["sin","cos","system"])(x))',
                      array(),
                      true, false);

    }

    public function test_affected_units() {
        $this->security = new stack_cas_security(true);
        $this->filter = stack_parsing_rule_factory::get_by_common_name('996_call_modification');

        $this->expect('"+"(a,b)',
                      '(%_C("+"),"+"(a,b))',
                      array(),
                      true, false);

        $this->expect('(-b+-sqrt(b^2))/(2*a)',
                      '(-b+-(%_C(sqrt),sqrt(b^2)))/(2*a)',
                      array(),
                      true, false);

        $this->expect('(x+2)(x+3)',
                      '(%_C((x+2)),(x+2)(x+3))',
                      array(),
                      true, false);

        $this->expect('-(1/512) + i(sqrt(3)/512)',
                      '-(1/512)+(%_C(i),i((%_C(sqrt),sqrt(3))/512))',
                      array(),
                      true, false);

        $this->expect('-3x(1+x)',
                      '-3*(%_C(x),x(1+x))',
                      array(),
                      true, false);

        $this->expect('-b(5-b)',
                      '(%_C(b),-b(5-b))',
                      array(),
                      true, false);

        $this->expect('-x(1+x)',
                      '(%_C(x),-x(1+x))',
                      array(),
                      true, false);

        $this->expect('1-x(1+x)',
                      '1-(%_C(x),x(1+x))',
                      array(),
                      true, false);

        $this->expect('1/sin(+x)',
                      '1/(%_C(sin),sin(+x))',
                      array(),
                      true, false);

        $this->expect('2+log_x(1/(x+b))*x^2',
                      '2+(%_C(log_x),log_x(1/(x+b)))*x^2',
                      array(),
                      true, false);

        $this->expect('3b+5/a(x)',
                      '3*b+5/(%_C(a),a(x))',
                      array(),
                      true, false);

        $this->expect('7x(2+1)',
                      '7*(%_C(x),x(2+1))',
                      array(),
                      true, false);

        $this->expect('Bgcd(3,2)',
                      '(%_C(Bgcd),Bgcd(3,2))',
                      array(),
                      true, false);

        $this->expect('In(x)',
                      '(%_C(In),In(x))',
                      array(),
                      true, false);

        $this->expect('Sin(x)',
                      '(%_C(Sin),Sin(x))',
                      array(),
                      true, false);

        $this->expect('a(x)',
                      '(%_C(a),a(x))',
                      array(),
                      true, false);

        $this->expect('a/(a(x+1)+2)',
                      'a/((%_C(a),a(x+1))+2)',
                      array(),
                      true, false);

        $this->expect('abs(13)',
                      '(%_C(abs),abs(13))',
                      array(),
                      true, false);

        $this->expect('abs(x)',
                      '(%_C(abs),abs(x))',
                      array(),
                      true, false);

        $this->expect('arcsin(x)',
                      '(%_C(arcsin),arcsin(x))',
                      array(),
                      true, false);

        $this->expect('asin(x)',
                      '(%_C(asin),asin(x))',
                      array(),
                      true, false);

        $this->expect('asinh(x)',
                      '(%_C(asinh),asinh(x))',
                      array(),
                      true, false);

        $this->expect('b(b+1)',
                      '(%_C(b),b(b+1))',
                      array(),
                      true, false);

        $this->expect('b/a(x)',
                      'b/(%_C(a),a(x))',
                      array(),
                      true, false);

        $this->expect('bsin(t)',
                      '(%_C(bsin),bsin(t))',
                      array(),
                      true, false);

        $this->expect('ceiling(x)',
                      '(%_C(ceiling),ceiling(x))',
                      array(),
                      true, false);

        $this->expect('comb(x,y)',
                      '(%_C(comb),comb(x,y))',
                      array(),
                      true, false);

        $this->expect('cos(2x)(x+1)',
                      '(%_C(cos),%_C(cos(2*x)),cos(2*x)(x+1))',
                      array(),
                      true, false);

        $this->expect('cos(x)',
                      '(%_C(cos),cos(x))',
                      array(),
                      true, false);

        $this->expect('cosec(x)',
                      '(%_C(cosec),cosec(x))',
                      array(),
                      true, false);

        $this->expect('cosech(x)',
                      '(%_C(cosech),cosech(x))',
                      array(),
                      true, false);

        $this->expect('cosh(x)',
                      '(%_C(cosh),cosh(x))',
                      array(),
                      true, false);

        $this->expect('cot(x)',
                      '(%_C(cot),cot(x))',
                      array(),
                      true, false);

        $this->expect('coth(x)',
                      '(%_C(coth),coth(x))',
                      array(),
                      true, false);

        $this->expect('csc(6*x)^2*(7*sin(6*x)*cos(7*x)-6*cos(6*x)*sin(7*x))',
                      '(%_C(csc),csc(6*x))^2*(7*(%_C(sin),sin(6*x))*(%_C(cos),cos(7*x))-6*(%_C(cos),cos(6*x))*(%_C(sin),sin(7*x)))',
                      array(),
                      true, false);

        $this->expect('csc(x)',
                      '(%_C(csc),csc(x))',
                      array(),
                      true, false);

        $this->expect('diff(sin(x))',
                      '(%_C(diff),diff((%_C(sin),sin(x))))',
                      array(),
                      true, false);

        $this->expect('diff(sin(x),x)',
                      '(%_C(diff),diff((%_C(sin),sin(x)),x))',
                      array(),
                      true, false);

        $this->expect('diff(x,y)',
                      '(%_C(diff),diff(x,y))',
                      array(),
                      true, false);

        $this->expect('dosomething(x,y,z)',
                      '(%_C(dosomething),dosomething(x,y,z))',
                      array(),
                      true, false);

        $this->expect('exp(x)',
                      '(%_C(exp),exp(x))',
                      array(),
                      true, false);

        $this->expect('f(x)',
                      '(%_C(f),f(x))',
                      array(),
                      true, false);

        $this->expect('f(x)(2)',
                      '(%_C(f),%_C(f(x)),f(x)(2))',
                      array(),
                      true, false);

        $this->expect('fact(13)',
                      '(%_C(fact),fact(13))',
                      array(),
                      true, false);

        $this->expect('floor(x)',
                      '(%_C(floor),floor(x))',
                      array(),
                      true, false);

        $this->expect('gcd(x,y)',
                      '(%_C(gcd),gcd(x,y))',
                      array(),
                      true, false);

        $this->expect('gcf(x,y)',
                      '(%_C(gcf),gcf(x,y))',
                      array(),
                      true, false);

        $this->expect('i(1+i)',
                      '(%_C(i),i(1+i))',
                      array(),
                      true, false);

        $this->expect('i(4)',
                      '(%_C(i),i(4))',
                      array(),
                      true, false);

        $this->expect('int(sin(x))',
                      '(%_C(int),int((%_C(sin),sin(x))))',
                      array(),
                      true, false);

        $this->expect('int(x,y)',
                      '(%_C(int),int(x,y))',
                      array(),
                      true, false);

        $this->expect('len(x)',
                      '(%_C(len),len(x))',
                      array(),
                      true, false);

        $this->expect('length(x)',
                      '(%_C(length),length(x))',
                      array(),
                      true, false);

        $this->expect('lg(10^3)',
                      '(%_C(lg),lg(10^3))',
                      array(),
                      true, false);

        $this->expect('lg(x)',
                      '(%_C(lg),lg(x))',
                      array(),
                      true, false);

        $this->expect('lg(x,a)',
                      '(%_C(lg),lg(x,a))',
                      array(),
                      true, false);

        $this->expect('limit(y,x,3)',
                      '(%_C(limit),limit(y,x,3))',
                      array(),
                      true, false);

        $this->expect('ln(x)',
                      '(%_C(ln),ln(x))',
                      array(),
                      true, false);

        $this->expect('log(2x)/x+1/2',
                      '(%_C(log),log(2*x))/x+1/2',
                      array(),
                      true, false);

        $this->expect('log(x)',
                      '(%_C(log),log(x))',
                      array(),
                      true, false);

        $this->expect('log10(x)',
                      '(%_C(log10),log10(x))',
                      array(),
                      true, false);

        $this->expect('log_10(x)',
                      '(%_C(log_10),log_10(x))',
                      array(),
                      true, false);

        $this->expect('log_2(a)',
                      '(%_C(log_2),log_2(a))',
                      array(),
                      true, false);

        $this->expect('log_a(b)*log_b(c)',
                      '(%_C(log_a),log_a(b))*(%_C(log_b),log_b(c))',
                      array(),
                      true, false);

        $this->expect('log_x(1/(x+b))',
                      '(%_C(log_x),log_x(1/(x+b)))',
                      array(),
                      true, false);

        $this->expect('log_x:log_x(a)',
                      'log_x:(%_C(log_x),log_x(a))',
                      array(),
                      true, false);

        $this->expect('matrix([a,b],[c,d])',
                      '(%_C(matrix),matrix([a,b],[c,d]))',
                      array(),
                      true, false);

        $this->expect('mod(x,y)',
                      '(%_C(mod),mod(x,y))',
                      array(),
                      true, false);

        $this->expect('partialdiff(x,y,1)',
                      '(%_C(partialdiff),partialdiff(x,y,1))',
                      array(),
                      true, false);

        $this->expect('perm(x,y)',
                      '(%_C(perm),perm(x,y))',
                      array(),
                      true, false);

        $this->expect('plot(x^2,[x,-1,1])',
                      '(%_C(plot),plot(x^2,[x,-1,1]))',
                      array(),
                      true, false);

        $this->expect('plot2d(x^2,[x,-1,1])',
                      '(%_C(plot2d),plot2d(x^2,[x,-1,1]))',
                      array(),
                      true, false);

        $this->expect('product(cos(k*x),k,1,3)',
                      '(%_C(product),product((%_C(cos),cos(k*x)),k,1,3))',
                      array(),
                      true, false);

        $this->expect('root(2,-3)',
                      '(%_C(root),root(2,-3))',
                      array(),
                      true, false);

        $this->expect('root(x)',
                      '(%_C(root),root(x))',
                      array(),
                      true, false);

        $this->expect('root(x,3)',
                      '(%_C(root),root(x,3))',
                      array(),
                      true, false);

        $this->expect('sec(x)',
                      '(%_C(sec),sec(x))',
                      array(),
                      true, false);

        $this->expect('sech(x)',
                      '(%_C(sech),sech(x))',
                      array(),
                      true, false);

        $this->expect('set(x, y, z)',
                      '(%_C(set),set(x,y,z))',
                      array(),
                      true, false);

        $this->expect('sgn(x)',
                      '(%_C(sgn),sgn(x))',
                      array(),
                      true, false);

        $this->expect('sign(x)',
                      '(%_C(sign),sign(x))',
                      array(),
                      true, false);

        $this->expect('sim(x)',
                      '(%_C(sim),sim(x))',
                      array(),
                      true, false);

        $this->expect('sin(x)',
                      '(%_C(sin),sin(x))',
                      array(),
                      true, false);

        $this->expect('sinh(x)',
                      '(%_C(sinh),sinh(x))',
                      array(),
                      true, false);

        $this->expect('sqr(x)',
                      '(%_C(sqr),sqr(x))',
                      array(),
                      true, false);

        $this->expect('sqrt(+x)',
                      '(%_C(sqrt),sqrt(+x))',
                      array(),
                      true, false);

        $this->expect('sqrt(x)',
                      '(%_C(sqrt),sqrt(x))',
                      array(),
                      true, false);

        $this->expect('stackvector(a)',
                      '(%_C(stackvector),stackvector(a))',
                      array(),
                      true, false);

        $this->expect('sum(k^n,n,0,3)',
                      '(%_C(sum),sum(k^n,n,0,3))',
                      array(),
                      true, false);

        $this->expect('switch(x,a,y,b,c)',
                      '(%_C(switch),switch(x,a,y,b,c))',
                      array(),
                      true, false);

        $this->expect('tan(x)',
                      '(%_C(tan),tan(x))',
                      array(),
                      true, false);

        $this->expect('tanh(x)',
                      '(%_C(tanh),tanh(x))',
                      array(),
                      true, false);

        $this->expect('x()',
                      '(%_C(x),x())',
                      array(),
                      true, false);

        $this->expect('x(2+1)',
                      '(%_C(x),x(2+1))',
                      array(),
                      true, false);

        $this->expect('x(sin(t)+1)',
                      '(%_C(x),x((%_C(sin),sin(t))+1))',
                      array(),
                      true, false);

        $this->expect('x(t+1)',
                      '(%_C(x),x(t+1))',
                      array(),
                      true, false);

        $this->expect('x(x+1)',
                      '(%_C(x),x(x+1))',
                      array(),
                      true, false);

        $this->expect('x=+-sqrt(2)',
                      'x = (%_C(sqrt),+-sqrt(2))',
                      array(),
                      true, false);

        $this->expect('x>1 or (x<1 and t<sin(x))',
                      'x > 1 or (x < 1 and t < (%_C(sin),sin(x)))',
                      array(),
                      true, false);

        $this->expect('x^f(x)',
                      'x^(%_C(f),f(x))',
                      array(),
                      true, false);

        $this->expect('xsin(1)',
                      '(%_C(xsin),xsin(1))',
                      array(),
                      true, false);

        $this->expect('ycos(2)',
                      '(%_C(ycos),ycos(2))',
                      array(),
                      true, false);

        $this->expect('|x|',
                      '(%_C(abs),abs(x))',
                      array(),
                      true, false);

        $this->expect('rand(["+","-"])(x,y)',
                      '(%_C(rand),%_C(rand(["+","-"])),rand(["+","-"])(x,y))',
                      array(),
                      true, false);

        $this->expect('rand(["sin","cos","system"])(x)',
                      '(%_C(rand),%_C(rand(["sin","cos","system"])),rand(["sin","cos","system"])(x))',
                      array(),
                      true, false);

    }

    public function test_non_affected_units() {
        $this->security = new stack_cas_security(true);
        $this->filter = stack_parsing_rule_factory::get_by_common_name('996_call_modification');

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

        $this->expect('((a,b),c)',
                      '((a,b),c)',
                      array(),
                      true, false);

        $this->expect('(a,(b,c))',
                      '(a,(b,c))',
                      array(),
                      true, false);

        $this->expect('{(a,b),(x,y)}',
                      '{(a,b),(x,y)}',
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

        $this->expect('0..1',
                      '0. . 1',
                      array(),
                      true, false);

        $this->expect('0.1..1.2',
                      '0.1 . .1 . 2',
                      array(),
                      true, false);

        $this->expect('0.1.1.2',
                      '0.1 . 1.2',
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

        $this->expect('1/0',
                      '1/0',
                      array(),
                      true, false);

        $this->expect('1/2',
                      '1/2',
                      array(),
                      true, false);

        $this->expect('1<=x<y^2',
                      '1 <= x < y^2',
                      array(),
                      true, false);

        $this->expect('1<x<3',
                      '1 < x < 3',
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

        $this->expect('[1<x,1<y<3]',
                      '[1 < x,1 < y < 3]',
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

        $this->expect('alpha',
                      'alpha',
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

        $this->expect('chi',
                      'chi',
                      array(),
                      true, false);

        $this->expect('cos^2(x)',
                      'cos^2*(x)',
                      array(),
                      true, false);

        $this->expect('delta',
                      'delta',
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

        $this->expect('false',
                      'false',
                      array(),
                      true, false);

        $this->expect('gamma',
                      'gamma',
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

        $this->expect('ln*x',
                      'ln*x',
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

        $this->expect('sigma',
                      'sigma',
                      array(),
                      true, false);

        $this->expect('sin',
                      'sin',
                      array(),
                      true, false);

        $this->expect('sin x',
                      'sin*x',
                      array(),
                      true, false);

        $this->expect('sin*2*x',
                      'sin*2*x',
                      array(),
                      true, false);

        $this->expect('sin[2*x]',
                      'sin[2*x]',
                      array(),
                      true, false);

        $this->expect('sin^-1(x)',
                      'sin^-1*(x)',
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

        $this->expect('x/(y+z)',
                      'x/(y+z)',
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

        $this->expect('Xy_12',
                      'Xy_12',
                      array(),
                      true, false);

        $this->expect('x_y',
                      'x_y',
                      array(),
                      true, false);

        $this->expect('x_y_z',
                      'x_y_z',
                      array(),
                      true, false);

        $this->expect('x_y_1',
                      'x_y_1',
                      array(),
                      true, false);

        $this->expect('x_12_z',
                      'x_12_z',
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

        $this->expect('M_2*x^2+M_1*x+M_0',
                      'M_2*x^2+M_1*x+M_0',
                      array(),
                      true, false);

        $this->expect('xi',
                      'xi',
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
        $this->filter = stack_parsing_rule_factory::get_by_common_name('996_call_modification');

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

        $this->expect('((a,b),c)',
                      '((a,b),c)',
                      array(),
                      true, false);

        $this->expect('(a,(b,c))',
                      '(a,(b,c))',
                      array(),
                      true, false);

        $this->expect('{(a,b),(x,y)}',
                      '{(a,b),(x,y)}',
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

        $this->expect('0..1',
                      '0. . 1',
                      array(),
                      true, false);

        $this->expect('0.1..1.2',
                      '0.1 . .1 . 2',
                      array(),
                      true, false);

        $this->expect('0.1.1.2',
                      '0.1 . 1.2',
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

        $this->expect('1/0',
                      '1/0',
                      array(),
                      true, false);

        $this->expect('1/2',
                      '1/2',
                      array(),
                      true, false);

        $this->expect('1<=x<y^2',
                      '1 <= x < y^2',
                      array(),
                      true, false);

        $this->expect('1<x<3',
                      '1 < x < 3',
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

        $this->expect('[1<x,1<y<3]',
                      '[1 < x,1 < y < 3]',
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

        $this->expect('alpha',
                      'alpha',
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

        $this->expect('chi',
                      'chi',
                      array(),
                      true, false);

        $this->expect('cos^2(x)',
                      'cos^2*(x)',
                      array(),
                      true, false);

        $this->expect('delta',
                      'delta',
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

        $this->expect('false',
                      'false',
                      array(),
                      true, false);

        $this->expect('gamma',
                      'gamma',
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

        $this->expect('ln*x',
                      'ln*x',
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

        $this->expect('sigma',
                      'sigma',
                      array(),
                      true, false);

        $this->expect('sin',
                      'sin',
                      array(),
                      true, false);

        $this->expect('sin x',
                      'sin*x',
                      array(),
                      true, false);

        $this->expect('sin*2*x',
                      'sin*2*x',
                      array(),
                      true, false);

        $this->expect('sin[2*x]',
                      'sin[2*x]',
                      array(),
                      true, false);

        $this->expect('sin^-1(x)',
                      'sin^-1*(x)',
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

        $this->expect('x/(y+z)',
                      'x/(y+z)',
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

        $this->expect('Xy_12',
                      'Xy_12',
                      array(),
                      true, false);

        $this->expect('x_y',
                      'x_y',
                      array(),
                      true, false);

        $this->expect('x_y_z',
                      'x_y_z',
                      array(),
                      true, false);

        $this->expect('x_y_1',
                      'x_y_1',
                      array(),
                      true, false);

        $this->expect('x_12_z',
                      'x_12_z',
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

        $this->expect('M_2*x^2+M_1*x+M_0',
                      'M_2*x^2+M_1*x+M_0',
                      array(),
                      true, false);

        $this->expect('xi',
                      'xi',
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

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

use qtype_stack_testcase;
use stack_ast_container;
use stack_cas_keyval;
use stack_cas_security;
use stack_cas_session2;
use castext2_evaluatable;
use stack_secure_loader;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../locallib.php');
require_once(__DIR__ . '/fixtures/test_base.php');
require_once(__DIR__ . '/../stack/cas/cassession2.class.php');
require_once(__DIR__ . '/../stack/cas/keyval.class.php');
require_once(__DIR__ . '/../stack/cas/secure_loader.class.php');
require_once(__DIR__ . '/../stack/cas/castext2/castext2_evaluatable.class.php');

/**
 * Tests confirming that what happens in raw maxima will also happen in STACK
 * when STACK does its own security and other rewriting.
 *
 * @group qtype_stack
 * @covers \stack_cas_keyval
 */
class maxima_replication_test extends qtype_stack_testcase {

    /**
     * All tests in this set of tests share the form of having
     * a section of code which will be taken as a keyval and which
     * contains a variable named `RESULT` that will end up containing
     * something that will be compared to the expected form. The result
     * will be outputted as `string(RESULT)`. In this case, we do that
     * outputting through a CASText-evaluatable.
     */
    public function check($code, $result) {
        // Do a full compile of the keyval.
        $keyval = new stack_cas_keyval($code, null, 123);
        $keyval->get_valid();
        $keyval = new stack_secure_loader($keyval->compile('test')['statement']);
        $output = castext2_evaluatable::make_from_source('{#RESULT#}', 'test');
        $output->get_valid();
        $session = new stack_cas_session2([$keyval, $output], null, 123);

        // Execute.
        $session->instantiate();

        $this->assertEquals($result, $output->get_rendered());
    }

    public function test_matrix_mult() {
        $code = 'simp:true;';
        $code .= 'a:matrix([1,2],[3,4]);';
        $code .= 'RESULT:a.a;';

        $result = 'matrix([7,10],[15,22])';

        $this->check($code, $result);
    }


    public function test_ev_flag_1() {
        $code = 'simp:true;';
        $code .= 'a:1+t;';
        $code .= 'RESULT:a,t=1;';

        $result = '2';

        $this->check($code, $result);
    }

    public function test_ev_flag_2() {
        $code = 'simp:true;';
        $code .= 'a:sqrt(t);';
        $code .= 'RESULT:a,t=4;';

        $result = '2';

        $this->check($code, $result);
    }

    public function test_ev_flag_3() {
        $code = 'simp:false;';
        $code .= 'a:1+1;';
        $code .= 'RESULT:a;';

        $result = '1+1';

        $this->check($code, $result);

        $code = 'simp:false;';
        $code .= 'a:1+1,simp;';
        $code .= 'RESULT:a;';

        $result = '2';

        $this->check($code, $result);
    }

    public function test_ev_flag_4() {
        $code = 'simp:true;';
        $code .= 'foo(x):=0+sqrt(x);';
        $code .= 'RESULT:foo(t),t=4;';

        $result = '2';

        $this->check($code, $result);
    }

    public function test_ev_1() {
        $code = 'simp:true;';
        $code .= 'a:1+t;';
        $code .= 'RESULT:ev(a,t=1);';

        $result = '2';

        $this->check($code, $result);
    }

    public function test_ev_2() {
        $code = 'simp:true;';
        $code .= 'a:sqrt(t);';
        $code .= 'RESULT:ev(a,t=4);';

        $result = '2';

        $this->check($code, $result);
    }

    public function test_ev_3() {
        $code = 'simp:false;';
        $code .= 'a:ev(1+1);';
        $code .= 'RESULT:a;';

        $result = '1+1';

        $this->check($code, $result);

        $code = 'simp:false;';
        $code .= 'a:ev(1+1,simp);';
        $code .= 'RESULT:a;';

        $result = '2';

        $this->check($code, $result);
    }

    public function test_ev_4() {
        $code = 'simp:true;';
        $code .= 'foo(x):=0+sqrt(x);';
        $code .= 'RESULT:ev(foo(t),t=4);';

        $result = '2';

        $this->check($code, $result);
    }

    public function test_iss824() {
        $code = 'simp:true;';
        $code .= 'a:2;';
        $code .= 'u:matrix([t,-1,a*t]);';
        $code .= 'v:matrix([-1,a+1,5]);';
        $code .= 'myvar1:ev(t,solve(u.u=1,t));';
        $code .= 'RESULT:ev(sqrt(u.v), t=myvar1);';

        $result = 'sqrt(3)*%i';

        $this->check($code, $result);
    }

    public function test_diff() {
        $code = 'simp:false;';
        $code .= 'a:x;';
        $code .= 'b:x^2;';
        $code .= 'RESULT:ev(diff(a-b,x),simp);';

        $result = '1-2*x';

        $this->check($code, $result);
    }

    public function test_taylor() {
        $code = 'simp:false;';
        $code .= 'RESULT:ev(taylor(10*cos(2*x),x,%pi/4,2),simp);';

        $result = '(20*%pi-80*x)/4';

        $this->check($code, $result);
    }

    public function test_iss844_mapping() {
        $code = 'simp:true;';
        $code .= 'ids:[cos,sin,tan,sqrt];';
        $code .= 'foo(x):=ids[x];';
        $code .= 'RESULT:[];';
        $code .= 'RESULT:append(RESULT,[apply(rand([cos]),[x])]);';
        $code .= 'RESULT:append(RESULT,[apply(ids[2],[x])]);';
        $code .= 'RESULT:append(RESULT,map(ids[3],[x]));';
        $code .= 'RESULT:append(RESULT,[apply(foo(4),[x])]);';

        $result = '[cos(x),sin(x),tan(x),sqrt(x)]';

        $this->check($code, $result);
    }

}

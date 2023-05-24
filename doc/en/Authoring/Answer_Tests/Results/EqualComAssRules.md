# EqualComAssRules: Answer test results

This page exposes the results of running answer tests on STACK test cases.  This page is automatically generated from the STACK unit tests and is designed to show question authors what answer tests actually do.  This includes cases where answer tests currentl fail, which gives a negative expected mark.  Comments and further test cases are very welcome.



<h2>EqualComAssRules</h2><div class="no-overflow"><table class="flexible table table-striped table-hover generaltable generalbox stacktestsuite"><thead><tr><th class="header c0" scope="col">Test<div class="commands"></div></th><th class="header c1" scope="col">?<div class="commands"></div></th><th class="header c2" scope="col">Student response<div class="commands"></div></th><th class="header c3" scope="col">Teacher answer<div class="commands"></div></th><th class="header c4" scope="col">Opt<div class="commands"></div></th><th class="header c5" scope="col">Mark<div class="commands"></div></th><th class="header c6" scope="col">Answer note<div class="commands"></div></th>
</tr></thead><tbody>
<tr class="expectedfail">
  <td class="cell c0">EqualComAssRules</td>
  <td class="cell c1"><span style="color:orange;"><i class="fa fa-adjust"></i></span></td>
  <td class="cell c2"><pre>1/0</pre></td>
  <td class="cell c3"><pre>0</pre></td>
  <td class="cell c4"><pre>[]</pre></td>
  <td class="cell c5">-1</td>
  <td class="cell c6">ATEqualComAssRules_STACKERROR_SAns.</td>
</tr>
<tr class="expectedfail">
  <td class="cell c0">EqualComAssRules</td>
  <td class="cell c1"><span style="color:orange;"><i class="fa fa-adjust"></i></span></td>
  <td class="cell c2"><pre>0</pre></td>
  <td class="cell c3"><pre>1/0</pre></td>
  <td class="cell c4"><pre>[]</pre></td>
  <td class="cell c5">-1</td>
  <td class="cell c6">ATEqualComAssRules_STACKERROR_TAns.</td>
</tr>
<tr class="expectedfail">
  <td class="cell c0">EqualComAssRules</td>
  <td class="cell c1"><span style="color:orange;"><i class="fa fa-adjust"></i></span></td>
  <td class="cell c2"><pre>0+a</pre></td>
  <td class="cell c3"><pre>a</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">-1</td>
  <td class="cell c6">STACKERROR_OPTION.</td>
</tr>
<tr class="expectedfail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">TEST_FAILED</td></td>
</tr>
<tr class="expectedfail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The answer test failed to execute correctly: please alert your teacher. Missing option when executing the test. </td></td>
</tr>
<tr class="expectedfail">
  <td class="cell c0">EqualComAssRules</td>
  <td class="cell c1"><span style="color:orange;"><i class="fa fa-adjust"></i></span></td>
  <td class="cell c2"><pre>0+a</pre></td>
  <td class="cell c3"><pre>a</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">-1</td>
  <td class="cell c6">ATEqualComAssRules_Opt_List.</td>
</tr>
<tr class="expectedfail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The option to this answer test must be a non-empty list of supported rules. This is an error. Please contact your teacher.</td></td>
</tr>
<tr class="expectedfail">
  <td class="cell c0">EqualComAssRules</td>
  <td class="cell c1"><span style="color:orange;"><i class="fa fa-adjust"></i></span></td>
  <td class="cell c2"><pre>0+a</pre></td>
  <td class="cell c3"><pre>a</pre></td>
  <td class="cell c4"><pre>[x]</pre></td>
  <td class="cell c5">-1</td>
  <td class="cell c6">ATEqualComAssRules_Opt_Wrong.</td>
</tr>
<tr class="expectedfail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The option to this answer test must be a non-empty list of supported rules. This is an error. Please contact your teacher.</td></td>
</tr>
<tr class="expectedfail">
  <td class="cell c0">EqualComAssRules</td>
  <td class="cell c1"><span style="color:orange;"><i class="fa fa-adjust"></i></span></td>
  <td class="cell c2"><pre>0+a</pre></td>
  <td class="cell c3"><pre>a</pre></td>
  <td class="cell c4"><pre>[intMul,intFac]</pre></td>
  <td class="cell c5">-1</td>
  <td class="cell c6">ATEqualComAssRules_Opt_Incompatible.</td>
</tr>
<tr class="expectedfail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The option to this answer test contains incompatible rules. This is an error. Please contact your teacher.</td></td>
</tr>
<tr class="notes">
  <td class="cell c0"><td colspan="6">Basic cases</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">EqualComAssRules</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>1+1</pre></td>
  <td class="cell c3"><pre>3</pre></td>
  <td class="cell c4"><pre>[zeroAdd]</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATEqualComAssRules (AlgEquiv-false).</td>
</tr>
<tr class="pass">
  <td class="cell c0">EqualComAssRules</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>1+1</pre></td>
  <td class="cell c3"><pre>2</pre></td>
  <td class="cell c4"><pre>[zeroAdd]</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">EqualComAssRules</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>1+1</pre></td>
  <td class="cell c3"><pre>2</pre></td>
  <td class="cell c4"><pre>[testdebug,zero
Add]</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATEqualComAssRules: [1 nounadd 1,2].</td>
</tr>
<tr class="pass">
  <td class="cell c0">EqualComAssRules</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>0+a</pre></td>
  <td class="cell c3"><pre>a</pre></td>
  <td class="cell c4"><pre>[zeroAdd]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">EqualComAssRules</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>a+0</pre></td>
  <td class="cell c3"><pre>a</pre></td>
  <td class="cell c4"><pre>[zeroAdd]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">EqualComAssRules</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>1*a</pre></td>
  <td class="cell c3"><pre>a</pre></td>
  <td class="cell c4"><pre>[testdebug,zero
Add]</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATEqualComAssRules: [1 nounmul a,a].</td>
</tr>
<tr class="pass">
  <td class="cell c0">EqualComAssRules</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>1*a</pre></td>
  <td class="cell c3"><pre>a</pre></td>
  <td class="cell c4"><pre>[oneMul]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">EqualComAssRules</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>1*a</pre></td>
  <td class="cell c3"><pre>a</pre></td>
  <td class="cell c4"><pre>ID_TRANS</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">EqualComAssRules</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>a/1</pre></td>
  <td class="cell c3"><pre>a</pre></td>
  <td class="cell c4"><pre>ID_TRANS</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">EqualComAssRules</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>0*a</pre></td>
  <td class="cell c3"><pre>0</pre></td>
  <td class="cell c4"><pre>ID_TRANS</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">EqualComAssRules</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>0-1*i</pre></td>
  <td class="cell c3"><pre>-i</pre></td>
  <td class="cell c4"><pre>ID_TRANS</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">EqualComAssRules</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>0-i</pre></td>
  <td class="cell c3"><pre>-i</pre></td>
  <td class="cell c4"><pre>ID_TRANS</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">EqualComAssRules</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>2+1*i</pre></td>
  <td class="cell c3"><pre>2+i</pre></td>
  <td class="cell c4"><pre>ID_TRANS</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">EqualComAssRules</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>x^0+x^1/1+x^2/2+x^3/3!+x^4/4!</pre></td>
  <td class="cell c3"><pre>1+x+x^2/2+x^3/3!+x^4/4!</pre></td>
  <td class="cell c4"><pre>ID_TRANS</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">EqualComAssRules</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>%e^x</pre></td>
  <td class="cell c3"><pre>exp(x)</pre></td>
  <td class="cell c4"><pre>[testdebug,ID_T
RANS]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATEqualComAssRules: [%e nounpow x,%e nounpow x].</td>
</tr>
<tr class="pass">
  <td class="cell c0">EqualComAssRules</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>12*%e^((2*(%pi/2)*%i)/2)</pre></td>
  <td class="cell c3"><pre>12*exp(%i*(%pi/2))</pre></td>
  <td class="cell c4"><pre>ID_TRANS</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">EqualComAssRules</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>12*%e^((2*(%pi/2)*%i)/2)</pre></td>
  <td class="cell c3"><pre>12*exp(%i*(%pi/2))</pre></td>
  <td class="cell c4"><pre>[ID_TRANS,[negN
eg,negDiv,negOr
d],[recipMul,di
vDiv,divCancel]
,[intAdd,intMul
,intPow]]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">EqualComAssRules</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>0^(1-1)</pre></td>
  <td class="cell c3"><pre>0</pre></td>
  <td class="cell c4"><pre>ID_TRANS</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATEqualComAssRules_STACKERROR_SAns.</td>
</tr>
<tr class="pass">
  <td class="cell c0">EqualComAssRules</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>0*a</pre></td>
  <td class="cell c3"><pre>0</pre></td>
  <td class="cell c4"><pre>delete(zeroMul,
 ID_TRANS)</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">EqualComAssRules</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>-(-a)</pre></td>
  <td class="cell c3"><pre>a</pre></td>
  <td class="cell c4"><pre>[negNeg]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">EqualComAssRules</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>-(-(-a))</pre></td>
  <td class="cell c3"><pre>-a</pre></td>
  <td class="cell c4"><pre>[negNeg]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">EqualComAssRules</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>-(-(-a))</pre></td>
  <td class="cell c3"><pre>a</pre></td>
  <td class="cell c4"><pre>[testdebug,negN
eg]</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATEqualComAssRules (AlgEquiv-false).</td>
</tr>
<tr class="pass">
  <td class="cell c0">EqualComAssRules</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>3/(-x)</pre></td>
  <td class="cell c3"><pre>-3/x</pre></td>
  <td class="cell c4"><pre>ID_TRANS</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">EqualComAssRules</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>3/(-x)</pre></td>
  <td class="cell c3"><pre>-3/x</pre></td>
  <td class="cell c4"><pre>[testdebug,ID_T
RANS]</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATEqualComAssRules: [3 nounmul UNARY_RECIP UNARY_MINUS nounmul x,UNARY_MINUS nounmul 3 nounmul UNARY_RECIP x].</td>
</tr>
<tr class="pass">
  <td class="cell c0">EqualComAssRules</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>-x*(x+1)</pre></td>
  <td class="cell c3"><pre>x*(-x-1)</pre></td>
  <td class="cell c4"><pre>[negDist]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">EqualComAssRules</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>-x*(x-1)</pre></td>
  <td class="cell c3"><pre>x*(1-x)</pre></td>
  <td class="cell c4"><pre>NEG_TRANS</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">EqualComAssRules</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>-x*(x-1)</pre></td>
  <td class="cell c3"><pre>x*(1-x)</pre></td>
  <td class="cell c4"><pre>NEG_TRANS</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">EqualComAssRules</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>-5*x*(3-x)</pre></td>
  <td class="cell c3"><pre>5*x*(x-3)</pre></td>
  <td class="cell c4"><pre>NEG_TRANS</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">EqualComAssRules</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>-x*(x-1)*(x+1)</pre></td>
  <td class="cell c3"><pre>x*(x-1)*(-x-1)</pre></td>
  <td class="cell c4"><pre>NEG_TRANS</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">EqualComAssRules</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>-x*(x-1)*(x+1)</pre></td>
  <td class="cell c3"><pre>x*(1-x)*(x+1)</pre></td>
  <td class="cell c4"><pre>NEG_TRANS</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">EqualComAssRules</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>-x*(y-1)*(x-1)</pre></td>
  <td class="cell c3"><pre>x*(1-x)*(y-1)</pre></td>
  <td class="cell c4"><pre>NEG_TRANS</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">EqualComAssRules</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>-x*(y-1)*(x-1)</pre></td>
  <td class="cell c3"><pre>x*(x-1)*(1-y)</pre></td>
  <td class="cell c4"><pre>NEG_TRANS</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">EqualComAssRules</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>(x-y)*(y-x)</pre></td>
  <td class="cell c3"><pre>-(x-y)*(x-y)</pre></td>
  <td class="cell c4"><pre>NEG_TRANS</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">EqualComAssRules</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>(x-y)*(y-x)</pre></td>
  <td class="cell c3"><pre>-(x-y)^2</pre></td>
  <td class="cell c4"><pre>[testdebug,NEG_
TRANS]</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATEqualComAssRules: [UNARY_MINUS nounmul (x nounadd UNARY_MINUS nounmul y) nounmul (x nounadd UNARY_MINUS nounmul y),UNARY_MINUS nounmul (x nounadd UNARY_MINUS nounmul y) nounpow 2].</td>
</tr>
<tr class="pass">
  <td class="cell c0">EqualComAssRules</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>-x*(x-1)*(x+1)</pre></td>
  <td class="cell c3"><pre>x*(1-x)*(x+1)</pre></td>
  <td class="cell c4"><pre>[testdebug,negD
ist,negNeg]</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATEqualComAssRules: [x nounmul (UNARY_MINUS nounmul 1 nounadd UNARY_MINUS nounmul x) nounmul (x nounadd UNARY_MINUS nounmul 1),x nounmul (1 nounadd UNARY_MINUS nounmul x) nounmul (1 nounadd x)].</td>
</tr>
<tr class="pass">
  <td class="cell c0">EqualComAssRules</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>-x*(y-1)*(x-1)</pre></td>
  <td class="cell c3"><pre>x*(x-1)*(1-y)</pre></td>
  <td class="cell c4"><pre>[testdebug,negD
ist,negNeg]</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATEqualComAssRules: [x nounmul (1 nounadd UNARY_MINUS nounmul x) nounmul (y nounadd UNARY_MINUS nounmul 1),x nounmul (1 nounadd UNARY_MINUS nounmul y) nounmul (x nounadd UNARY_MINUS nounmul 1)].</td>
</tr>
<tr class="pass">
  <td class="cell c0">EqualComAssRules</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>3/(-x)</pre></td>
  <td class="cell c3"><pre>-3/x</pre></td>
  <td class="cell c4"><pre>[negDiv]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">EqualComAssRules</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>3/(-x)</pre></td>
  <td class="cell c3"><pre>ev(-3,simp)/x</pre></td>
  <td class="cell c4"><pre>[negDiv]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">EqualComAssRules</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>(-a)/(-x)</pre></td>
  <td class="cell c3"><pre>-(-a/x)</pre></td>
  <td class="cell c4"><pre>[testdebug,ID_T
RANS]</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATEqualComAssRules: [UNARY_MINUS nounmul a nounmul UNARY_RECIP UNARY_MINUS nounmul x,UNARY_MINUS nounmul UNARY_MINUS nounmul a nounmul UNARY_RECIP x].</td>
</tr>
<tr class="pass">
  <td class="cell c0">EqualComAssRules</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>(-a)/(-x)</pre></td>
  <td class="cell c3"><pre>-(-a/x)</pre></td>
  <td class="cell c4"><pre>[negDiv]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">EqualComAssRules</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>(-a)/(-x)</pre></td>
  <td class="cell c3"><pre>a/x</pre></td>
  <td class="cell c4"><pre>[testdebug,negD
iv]</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATEqualComAssRules: [UNARY_MINUS nounmul UNARY_MINUS nounmul a nounmul UNARY_RECIP x,a nounmul UNARY_RECIP x].</td>
</tr>
<tr class="pass">
  <td class="cell c0">EqualComAssRules</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>(-a)/(-x)</pre></td>
  <td class="cell c3"><pre>a/x</pre></td>
  <td class="cell c4"><pre>[negDiv,negNeg]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">EqualComAssRules</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>1/(-x)</pre></td>
  <td class="cell c3"><pre>(-1)/x</pre></td>
  <td class="cell c4"><pre>[negDiv]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">EqualComAssRules</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>1/(-x)</pre></td>
  <td class="cell c3"><pre>ev(-1,simp)/x</pre></td>
  <td class="cell c4"><pre>[negDiv]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">EqualComAssRules</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>(2/-3)*(x-y)</pre></td>
  <td class="cell c3"><pre>-(2/3)*(x-y)</pre></td>
  <td class="cell c4"><pre>[negDiv]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">EqualComAssRules</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>(2/-3)*(x-y)</pre></td>
  <td class="cell c3"><pre>(2/3)*(y-x)</pre></td>
  <td class="cell c4"><pre>[negDiv]</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">EqualComAssRules</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>(2/-3)*(x-y)</pre></td>
  <td class="cell c3"><pre>(2/3)*(y-x)</pre></td>
  <td class="cell c4"><pre>[negDiv,negOrd]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">EqualComAssRules</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>-2/(1-x)</pre></td>
  <td class="cell c3"><pre>2/(x-1)</pre></td>
  <td class="cell c4"><pre>[testdebug,negD
iv]</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATEqualComAssRules: [UNARY_MINUS nounmul 2 nounmul UNARY_RECIP (1 nounadd UNARY_MINUS nounmul x),2 nounmul UNARY_RECIP (x nounadd UNARY_MINUS nounmul 1)].</td>
</tr>
<tr class="pass">
  <td class="cell c0">EqualComAssRules</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>1/2*3/x</pre></td>
  <td class="cell c3"><pre>3/(2*x)</pre></td>
  <td class="cell c4"><pre>[testdebug,ID_T
RANS]</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATEqualComAssRules: [3 nounmul (UNARY_RECIP 2) nounmul UNARY_RECIP x,3 nounmul UNARY_RECIP 2 nounmul x].</td>
</tr>
<tr class="pass">
  <td class="cell c0">EqualComAssRules</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>1/2*3/x</pre></td>
  <td class="cell c3"><pre>3/(2*x)</pre></td>
  <td class="cell c4"><pre>[ID_TRANS,recip
Mul]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">EqualComAssRules</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>5/2*3/x</pre></td>
  <td class="cell c3"><pre>15/(2*x)</pre></td>
  <td class="cell c4"><pre>[testdebug,ID_T
RANS,recipMul]</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATEqualComAssRules: [3 nounmul 5 nounmul UNARY_RECIP 2 nounmul x,15 nounmul UNARY_RECIP 2 nounmul x].</td>
</tr>
<tr class="pass">
  <td class="cell c0">EqualComAssRules</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>-(x-y)</pre></td>
  <td class="cell c3"><pre>y-x</pre></td>
  <td class="cell c4"><pre>[negOrd]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">EqualComAssRules</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>5/2*3/x</pre></td>
  <td class="cell c3"><pre>15/(2*x)</pre></td>
  <td class="cell c4"><pre>[ID_TRANS,recip
Mul,intMul]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">EqualComAssRules</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>(3+2)*x+x</pre></td>
  <td class="cell c3"><pre>5*x+x</pre></td>
  <td class="cell c4"><pre>[ID_TRANS,intAd
d]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">EqualComAssRules</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>(3-5)*x+x</pre></td>
  <td class="cell c3"><pre>-2*x+x</pre></td>
  <td class="cell c4"><pre>[ID_TRANS,intAd
d]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">EqualComAssRules</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>7*x*(-3*x)</pre></td>
  <td class="cell c3"><pre>-21*x*x</pre></td>
  <td class="cell c4"><pre>[ID_TRANS,intMu
l]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">EqualComAssRules</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>(-7*x)*(-3*x)</pre></td>
  <td class="cell c3"><pre>21*x*x</pre></td>
  <td class="cell c4"><pre>[testdebug,ID_T
RANS,intMul]</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATEqualComAssRules: [UNARY_MINUS nounmul UNARY_MINUS nounmul 21 nounmul x nounmul x,21 nounmul x nounmul x].</td>
</tr>
<tr class="pass">
  <td class="cell c0">EqualComAssRules</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>(-7*x)*(-3*x)</pre></td>
  <td class="cell c3"><pre>21*x*x</pre></td>
  <td class="cell c4"><pre>[ID_TRANS,intMu
l,negNeg]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="notes">
  <td class="cell c0"><td colspan="6">ev(a/b/c, simp)=a/(b*c)</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">EqualComAssRules</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>a/b/c</pre></td>
  <td class="cell c3"><pre>a/(b*c)</pre></td>
  <td class="cell c4"><pre>[testdebug,ID_T
RANS]</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATEqualComAssRules: [a nounmul (UNARY_RECIP b) nounmul UNARY_RECIP c,a nounmul UNARY_RECIP b nounmul c].</td>
</tr>
<tr class="pass">
  <td class="cell c0">EqualComAssRules</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>a/b/c</pre></td>
  <td class="cell c3"><pre>a/(b*c)</pre></td>
  <td class="cell c4"><pre>[ID_TRANS,recip
Mul]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">EqualComAssRules</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>(a/b)/c</pre></td>
  <td class="cell c3"><pre>a/(b*c)</pre></td>
  <td class="cell c4"><pre>[ID_TRANS,recip
Mul]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="notes">
  <td class="cell c0"><td colspan="6">ev(a/(b/c), simp)=(a*c)/b</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">EqualComAssRules</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>a/(b/c)</pre></td>
  <td class="cell c3"><pre>(a*c)/b</pre></td>
  <td class="cell c4"><pre>[testdebug,ID_T
RANS]</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATEqualComAssRules: [a nounmul UNARY_RECIP b nounmul UNARY_RECIP c,a nounmul c nounmul UNARY_RECIP b].</td>
</tr>
<tr class="pass">
  <td class="cell c0">EqualComAssRules</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>a/(b/c)</pre></td>
  <td class="cell c3"><pre>(a*c)/b</pre></td>
  <td class="cell c4"><pre>[testdebug,ID_T
RANS,recipMul]</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATEqualComAssRules: [a nounmul UNARY_RECIP b nounmul UNARY_RECIP c,a nounmul c nounmul UNARY_RECIP b].</td>
</tr>
<tr class="pass">
  <td class="cell c0">EqualComAssRules</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>a/(b/c)</pre></td>
  <td class="cell c3"><pre>(a*c)/b</pre></td>
  <td class="cell c4"><pre>[ID_TRANS,divDi
v]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">EqualComAssRules</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>A*a/(B*b/c)</pre></td>
  <td class="cell c3"><pre>A*(a*c)/(B*b)</pre></td>
  <td class="cell c4"><pre>[ID_TRANS,divDi
v]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">EqualComAssRules</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>A*a/(B*b/c)*1/d</pre></td>
  <td class="cell c3"><pre>A*(a*c)/(B*b)*1/d</pre></td>
  <td class="cell c4"><pre>[ID_TRANS,divDi
v]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">EqualComAssRules</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>D*A*a/(B*b/c)*1/d</pre></td>
  <td class="cell c3"><pre>A*(a*c)/(B*b)*D/d</pre></td>
  <td class="cell c4"><pre>[ID_TRANS,divDi
v]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">EqualComAssRules</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>A*a/(B*b/c)*1/d</pre></td>
  <td class="cell c3"><pre>A*(a*c)/(B*b*d)</pre></td>
  <td class="cell c4"><pre>[testdebug,ID_T
RANS,divDiv]</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATEqualComAssRules: [A nounmul a nounmul c nounmul (UNARY_RECIP B nounmul b) nounmul UNARY_RECIP d,A nounmul a nounmul c nounmul UNARY_RECIP B nounmul b nounmul d].</td>
</tr>
<tr class="pass">
  <td class="cell c0">EqualComAssRules</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>A*a/(B*b/c)*1/d</pre></td>
  <td class="cell c3"><pre>A*(a*c)/(B*b*d)</pre></td>
  <td class="cell c4"><pre>[ID_TRANS,divDi
v,recipMul]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">EqualComAssRules</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>A/(B/(C/D))</pre></td>
  <td class="cell c3"><pre>A*C/(B*D)</pre></td>
  <td class="cell c4"><pre>[testdebug,ID_T
RANS,divDiv]</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATEqualComAssRules: [A nounmul C nounmul (UNARY_RECIP B) nounmul UNARY_RECIP D,A nounmul C nounmul UNARY_RECIP B nounmul D].</td>
</tr>
<tr class="pass">
  <td class="cell c0">EqualComAssRules</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>A/(B/(C/D))</pre></td>
  <td class="cell c3"><pre>A*C/(B*D)</pre></td>
  <td class="cell c4"><pre>[ID_TRANS,divDi
v,recipMul]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">EqualComAssRules</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>18</pre></td>
  <td class="cell c3"><pre>2*3^2</pre></td>
  <td class="cell c4"><pre>[intFac]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">EqualComAssRules</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>0+%i*(-(1/27))</pre></td>
  <td class="cell c3"><pre>-(%i/27)</pre></td>
  <td class="cell c4"><pre>[[zeroAdd,zeroM
ul,oneMul,onePo
w,idPow,zeroPow
,zPow,oneDiv],[
negNeg,negDiv,n
egOrd],[recipMu
l,divDiv,divCan
cel],[intAdd,in
tMul,intPow]]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">EqualComAssRules</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>x=sqrt(3)+2</pre></td>
  <td class="cell c3"><pre>x=3^(1/2)+2</pre></td>
  <td class="cell c4"><pre>[ID_TRANS,sqrtR
em]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">EqualComAssRules</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>x=sqrt(3)+2 nounor x=-sqrt(3)-
2</pre></td>
  <td class="cell c3"><pre>x=3^(1/2)+2 nounor x=-3^(1/2)-
2</pre></td>
  <td class="cell c4"><pre>ID_TRANS</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">EqualComAssRules</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>x=sqrt(3)+2 nounor x=-sqrt(3)-
2</pre></td>
  <td class="cell c3"><pre>x=3^(1/2)+2 nounor x=-3^(1/2)-
2</pre></td>
  <td class="cell c4"><pre>[ID_TRANS,sqrtR
em]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">EqualComAssRules</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>x=sqrt(3)+2 nounor x=-sqrt(3)+
7</pre></td>
  <td class="cell c3"><pre>x=3^(1/2)+2 nounor x=-3^(1/2)-
2</pre></td>
  <td class="cell c4"><pre>[ID_TRANS,sqrtR
em]</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATEqualComAssRules (AlgEquiv-false)ATEquation_default.</td>
</tr>
<tr class="pass">
  <td class="cell c0">EqualComAssRules</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>1/sqrt(3)</pre></td>
  <td class="cell c3"><pre>1/3^(1/2)</pre></td>
  <td class="cell c4"><pre>[ID_TRANS,sqrtR
em]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">EqualComAssRules</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>1/sqrt(3)</pre></td>
  <td class="cell c3"><pre>3^(-1/2)</pre></td>
  <td class="cell c4"><pre>[ID_TRANS,sqrtR
em]</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6"></td>
</tr></tbody></table></div>
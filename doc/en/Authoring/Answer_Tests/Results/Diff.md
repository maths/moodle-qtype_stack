# Diff: Answer test results

This page exposes the results of running answer tests on STACK test cases.  This page is automatically generated from the STACK unit tests and is designed to show question authors what answer tests actually do.  This includes cases where answer tests currentl fail, which gives a negative expected mark.  Comments and further test cases are very welcome.



<h2>Diff</h2><div class="no-overflow"><table class="flexible table table-striped table-hover generaltable generalbox stacktestsuite"><thead><tr><th class="header c0" scope="col">Test<div class="commands"></div></th><th class="header c1" scope="col">?<div class="commands"></div></th><th class="header c2" scope="col">Student response<div class="commands"></div></th><th class="header c3" scope="col">Teacher answer<div class="commands"></div></th><th class="header c4" scope="col">Opt<div class="commands"></div></th><th class="header c5" scope="col">Mark<div class="commands"></div></th><th class="header c6" scope="col">Answer note<div class="commands"></div></th>
</tr></thead><tbody>
<tr class="expectedfail">
  <td class="cell c0">Diff</td>
  <td class="cell c1"><span style="color:orange;"><i class="fa fa-adjust"></i></span></td>
  <td class="cell c2"><pre>1/0</pre></td>
  <td class="cell c3"><pre>3*x^2</pre></td>
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
  <td class="cell c0">Diff</td>
  <td class="cell c1"><span style="color:orange;"><i class="fa fa-adjust"></i></span></td>
  <td class="cell c2"><pre>0</pre></td>
  <td class="cell c3"><pre>1/0</pre></td>
  <td class="cell c4"><pre>(x</pre></td>
  <td class="cell c5">-1</td>
  <td class="cell c6">STACKERROR_OPTION.</td>
</tr>
<tr class="expectedfail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">TEST_FAILED</td></td>
</tr>
<tr class="expectedfail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The answer test failed to execute correctly: please alert your teacher. Option field is invalid. You have a missing right bracket <span class="stacksyntaxexample">)</span> in the expression: <span class="stacksyntaxexample">(x</span>.</td></td>
</tr>
<tr class="expectedfail">
  <td class="cell c0">Diff</td>
  <td class="cell c1"><span style="color:orange;"><i class="fa fa-adjust"></i></span></td>
  <td class="cell c2"><pre>1/0</pre></td>
  <td class="cell c3"><pre>3*x^2</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">-1</td>
  <td class="cell c6">ATDiff_STACKERROR_SAns.</td>
</tr>
<tr class="expectedfail">
  <td class="cell c0">Diff</td>
  <td class="cell c1"><span style="color:orange;"><i class="fa fa-adjust"></i></span></td>
  <td class="cell c2"><pre>0</pre></td>
  <td class="cell c3"><pre>1/0</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">-1</td>
  <td class="cell c6">ATDiff_STACKERROR_TAns.</td>
</tr>
<tr class="expectedfail">
  <td class="cell c0">Diff</td>
  <td class="cell c1"><span style="color:orange;"><i class="fa fa-adjust"></i></span></td>
  <td class="cell c2"><pre>0</pre></td>
  <td class="cell c3"><pre>0</pre></td>
  <td class="cell c4"><pre>1/0</pre></td>
  <td class="cell c5">-1</td>
  <td class="cell c6">ATDiff_STACKERROR_Opt.</td>
</tr>
<tr class="notes">
  <td class="cell c0"><td colspan="6">Basic tests</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Diff</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>3*x^2</pre></td>
  <td class="cell c3"><pre>3*x^2</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATDiff_true.</td>
</tr>
<tr class="pass">
  <td class="cell c0">Diff</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>3*X^2</pre></td>
  <td class="cell c3"><pre>3*x^2</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATDiff_var_SB_notSA.</td>
</tr>
<tr class="pass">
  <td class="cell c0">Diff</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>x^4/4</pre></td>
  <td class="cell c3"><pre>3*x^2</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATDiff_int.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">It looks like you have integrated instead!</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Diff</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>x^4/4+1</pre></td>
  <td class="cell c3"><pre>3*x^2</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATDiff_int.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">It looks like you have integrated instead!</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Diff</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>x^4/4+c</pre></td>
  <td class="cell c3"><pre>3*x^2</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATDiff_int.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">It looks like you have integrated instead!</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Diff</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>y=x^4/4</pre></td>
  <td class="cell c3"><pre>x^4/4</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATDiff_SA_not_expression.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer should be an expression, not an equation, inequality, list, set or matrix.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Diff</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>x^4/4</pre></td>
  <td class="cell c3"><pre>y=x^4/4</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">Diff</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>y=x^4/4</pre></td>
  <td class="cell c3"><pre>y=x^4/4</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATDiff_SA_not_expression.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer should be an expression, not an equation, inequality, list, set or matrix.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Diff</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>6000*(x-a)^5999</pre></td>
  <td class="cell c3"><pre>6000*(x-a)^5999</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATDiff_true.</td>
</tr>
<tr class="pass">
  <td class="cell c0">Diff</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>5999*(x-a)^5999</pre></td>
  <td class="cell c3"><pre>6000*(x-a)^5999</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6"></td>
</tr>
<tr class="notes">
  <td class="cell c0"><td colspan="6">Variable mismatch tests</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Diff</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>y^2-2*y+1</pre></td>
  <td class="cell c3"><pre>x^2-2*x+1</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATDiff_var_SB_notSA.</td>
</tr>
<tr class="pass">
  <td class="cell c0">Diff</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>x^2-2*x+1</pre></td>
  <td class="cell c3"><pre>y^2-2*y+1</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATDiff_var_SA_notSB.</td>
</tr>
<tr class="pass">
  <td class="cell c0">Diff</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>y^2+2*y+1</pre></td>
  <td class="cell c3"><pre>x^2-2*x+1</pre></td>
  <td class="cell c4"><pre>z</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATDiff_var_notSASB_SAnceSB.</td>
</tr>
<tr class="pass">
  <td class="cell c0">Diff</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>x^4/4</pre></td>
  <td class="cell c3"><pre>3*x^2</pre></td>
  <td class="cell c4"><pre>y</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6"></td>
</tr>
<tr class="notes">
  <td class="cell c0"><td colspan="6">Edge cases</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Diff</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>e^x+c</pre></td>
  <td class="cell c3"><pre>e^x</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATDiff_int.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">It looks like you have integrated instead!</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Diff</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>e^x+2</pre></td>
  <td class="cell c3"><pre>e^x</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATDiff_int.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">It looks like you have integrated instead!</td></td>
</tr>
<tr class="expectedfail">
  <td class="cell c0">Diff</td>
  <td class="cell c1"><span style="color:orange;"><i class="fa fa-adjust"></i></span></td>
  <td class="cell c2"><pre>n*x^n</pre></td>
  <td class="cell c3"><pre>n*x^(n-1)</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">-1</td>
  <td class="cell c6">ATDiff_STACKERROR_SAns.</td>
</tr>
<tr class="expectedfail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">TEST_FAILED</td></td>
</tr>
<tr class="expectedfail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The answer test failed to execute correctly: please alert your teacher. TIMEDOUT</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Diff</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>n*x^n</pre></td>
  <td class="cell c3"><pre>(assume(n&gt;0), n*x^(n-1))</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6"></td>
</tr></tbody></table></div>
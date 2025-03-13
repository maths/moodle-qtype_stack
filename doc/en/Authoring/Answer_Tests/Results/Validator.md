# Validator: Answer test results

This page exposes the results of running answer tests on STACK test cases.  This page is automatically generated from the STACK unit tests and is designed to show question authors what answer tests actually do.  This includes cases where answer tests currentl fail, which gives a negative expected mark.  Comments and further test cases are very welcome.



<h2>Validator</h2><div class="no-overflow"><table class="flexible table table-striped table-hover generaltable generalbox stacktestsuite"><thead><tr><th class="header c0" scope="col">Test<div class="commands"></div></th><th class="header c1" scope="col">?<div class="commands"></div></th><th class="header c2" scope="col">Student response<div class="commands"></div></th><th class="header c3" scope="col">Teacher answer<div class="commands"></div></th><th class="header c4" scope="col">Opt<div class="commands"></div></th><th class="header c5" scope="col">Mark<div class="commands"></div></th><th class="header c6" scope="col">Answer note<div class="commands"></div></th>
</tr></thead><tbody>
<tr class="expectedfail">
  <td class="cell c0">Validator</td>
  <td class="cell c1"><span style="color:orange;"><i class="fa fa-adjust"></i></span></td>
  <td class="cell c2"><pre>1/0</pre></td>
  <td class="cell c3"><pre>0</pre></td>
  <td class="cell c4"><pre>validate_nofunc
tions</pre></td>
  <td class="cell c5">-1</td>
  <td class="cell c6">ATValidator_STACKERROR_SAns.</td>
</tr>
<tr class="expectedfail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">TEST_FAILED</td></td>
</tr>
<tr class="expectedfail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The answer test failed to execute correctly: please alert your teacher. Division by zero.</td></td>
</tr>
<tr class="expectedfail">
  <td class="cell c0">Validator</td>
  <td class="cell c1"><span style="color:orange;"><i class="fa fa-adjust"></i></span></td>
  <td class="cell c2"><pre>x</pre></td>
  <td class="cell c3"><pre>0</pre></td>
  <td class="cell c4"><pre>1/0</pre></td>
  <td class="cell c5">-1</td>
  <td class="cell c6">ATValidator_STACKERROR_Opt.</td>
</tr>
<tr class="expectedfail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">TEST_FAILED</td></td>
</tr>
<tr class="expectedfail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The answer test failed to execute correctly: please alert your teacher. Division by zero.</td></td>
</tr>
<tr class="expectedfail">
  <td class="cell c0">Validator</td>
  <td class="cell c1"><span style="color:orange;"><i class="fa fa-adjust"></i></span></td>
  <td class="cell c2"><pre>x</pre></td>
  <td class="cell c3"><pre>null</pre></td>
  <td class="cell c4"><pre>op</pre></td>
  <td class="cell c5">-1</td>
  <td class="cell c6">ATValidator_STACKERROR_ev.</td>
</tr>
<tr class="expectedfail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">TEST_FAILED</td></td>
</tr>
<tr class="expectedfail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The validator threw an error when evaluated. This is an error in the test, please contact your teacher.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Validator</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>x^2+sin(1)</pre></td>
  <td class="cell c3"><pre>null</pre></td>
  <td class="cell c4"><pre>[validate_nofun
ctions]</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATValidator_not_fun.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The validator failed to evaluate. Did you give the correct validator function name? This is an error in the test, please contact your teacher.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Validator</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>f(x)</pre></td>
  <td class="cell c3"><pre>null</pre></td>
  <td class="cell c4"><pre>validate_nodef</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATValidator_not_fun.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The validator failed to evaluate. Did you give the correct validator function name? This is an error in the test, please contact your teacher.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Validator</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>x</pre></td>
  <td class="cell c3"><pre>null</pre></td>
  <td class="cell c4"><pre>sin</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATValidator_not_fun.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The validator failed to evaluate. Did you give the correct validator function name? This is an error in the test, please contact your teacher.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Validator</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[1,2,3]</pre></td>
  <td class="cell c3"><pre>null</pre></td>
  <td class="cell c4"><pre>first</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATValidator_res_not_string.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The result of your validator must be a string, but is not. This is an error in the test, please contact your teacher.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Validator</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>x^2+sin(1)</pre></td>
  <td class="cell c3"><pre>null</pre></td>
  <td class="cell c4"><pre>validate_nofunc
tions</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">Validator</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>f(x)</pre></td>
  <td class="cell c3"><pre>null</pre></td>
  <td class="cell c4"><pre>validate_nofunc
tions</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">User-defined functions are not permitted, however \(f\) appears to be used as a function.</td></td>
</tr>
<tr class="emptyrow">
  <td class="cell c0"></td>
  <td class="cell c1"></td>
  <td class="cell c2"></td>
  <td class="cell c3"></td>
  <td class="cell c4"></td>
  <td class="cell c5"></td>
  <td class="cell c6"></td>
</tr>
<tr class="emptyrow">
  <td class="cell c0"></td>
  <td class="cell c1"></td>
  <td class="cell c2"></td>
  <td class="cell c3"></td>
  <td class="cell c4"></td>
  <td class="cell c5"></td>
  <td class="cell c6"></td>
</tr>
<tr class="emptyrow">
  <td class="cell c0"></td>
  <td class="cell c1"></td>
  <td class="cell c2"></td>
  <td class="cell c3"></td>
  <td class="cell c4"></td>
  <td class="cell c5"></td>
  <td class="cell c6"></td>
</tr>
<tr class="emptyrow">
  <td class="cell c0"></td>
  <td class="cell c1"></td>
  <td class="cell c2"></td>
  <td class="cell c3"></td>
  <td class="cell c4"></td>
  <td class="cell c5"></td>
  <td class="cell c6"></td>
</tr>
<tr class="emptyrow">
  <td class="cell c0"></td>
  <td class="cell c1"></td>
  <td class="cell c2"></td>
  <td class="cell c3"></td>
  <td class="cell c4"></td>
  <td class="cell c5"></td>
  <td class="cell c6"></td>
</tr>
<tr class="emptyrow">
  <td class="cell c0"></td>
  <td class="cell c1"></td>
  <td class="cell c2"></td>
  <td class="cell c3"></td>
  <td class="cell c4"></td>
  <td class="cell c5"></td>
  <td class="cell c6"></td>
</tr>
<tr class="emptyrow">
  <td class="cell c0"></td>
  <td class="cell c1"></td>
  <td class="cell c2"></td>
  <td class="cell c3"></td>
  <td class="cell c4"></td>
  <td class="cell c5"></td>
  <td class="cell c6"></td>
</tr>
<tr class="emptyrow">
  <td class="cell c0"></td>
  <td class="cell c1"></td>
  <td class="cell c2"></td>
  <td class="cell c3"></td>
  <td class="cell c4"></td>
  <td class="cell c5"></td>
  <td class="cell c6"></td>
</tr>
<tr class="emptyrow">
  <td class="cell c0"></td>
  <td class="cell c1"></td>
  <td class="cell c2"></td>
  <td class="cell c3"></td>
  <td class="cell c4"></td>
  <td class="cell c5"></td>
  <td class="cell c6"></td>
</tr>
<tr class="emptyrow">
  <td class="cell c0"></td>
  <td class="cell c1"></td>
  <td class="cell c2"></td>
  <td class="cell c3"></td>
  <td class="cell c4"></td>
  <td class="cell c5"></td>
  <td class="cell c6"></td>
</tr></tbody></table></div>
# SubstEquiv: Answer test results

This page exposes the results of running answer tests on STACK test cases.  This page is automatically generated from the STACK unit tests and is designed to show question authors what answer tests actually do.  This includes cases where answer tests currentl fail, which gives a negative expected mark.  Comments and further test cases are very welcome.



<h2>SubstEquiv</h2><div class="no-overflow"><table class="flexible table table-striped table-hover generaltable generalbox stacktestsuite"><thead><tr><th class="header c0" scope="col">Test<div class="commands"></div></th><th class="header c1" scope="col">?<div class="commands"></div></th><th class="header c2" scope="col">Student response<div class="commands"></div></th><th class="header c3" scope="col">Teacher answer<div class="commands"></div></th><th class="header c4" scope="col">Opt<div class="commands"></div></th><th class="header c5" scope="col">Mark<div class="commands"></div></th><th class="header c6" scope="col">Answer note<div class="commands"></div></th>
</tr></thead><tbody>
<tr class="expectedfail">
  <td class="cell c0">SubstEquiv</td>
  <td class="cell c1"><span style="color:orange;"><i class="fa fa-adjust"></i></span></td>
  <td class="cell c2"><pre>1/0</pre></td>
  <td class="cell c3"><pre>x^2-2*x+1</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">-1</td>
  <td class="cell c6">ATSubstEquiv_STACKERROR_SAns.</td>
</tr>
<tr class="expectedfail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">TEST_FAILED</td></td>
</tr>
<tr class="expectedfail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The answer test failed to execute correctly: please alert your teacher. Division by zero. <i class="icon fa fa-exclamation-circle text-danger fa-fw " title="The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. " aria-label="The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. "></i>The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. </td></td>
</tr>
<tr class="fail">
  <td class="cell c0">SubstEquiv</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>x^2</pre></td>
  <td class="cell c3"><pre>x^2-2*x+1</pre></td>
  <td class="cell c4"><pre>[1/0]</pre></td>
  <td class="cell c5">-1</td>
  <td class="cell c6">ATSubstEquiv_STACKERROR_SAns.<pre>ATSubstEquiv(x^2, x^2-2*x+1, [1/0]);</pre></td>
</tr>
<tr class="fail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">TEST_FAILED</td></td>
</tr>
<tr class="fail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The answer test failed to execute correctly: please alert your teacher. <i class="icon fa fa-exclamation-circle text-danger fa-fw " title="The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. " aria-label="The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. "></i>The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. </td></td>
</tr>
<tr class="fail">
  <td class="cell c0">SubstEquiv</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>x^2</pre></td>
  <td class="cell c3"><pre>x^2-2*x+1</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">-1</td>
  <td class="cell c6">ATSubstEquiv_STACKERROR_SAns.<pre>ATSubstEquiv(x^2, x^2-2*x+1, x);</pre></td>
</tr>
<tr class="fail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">TEST_FAILED</td></td>
</tr>
<tr class="fail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The answer test failed to execute correctly: please alert your teacher. <i class="icon fa fa-exclamation-circle text-danger fa-fw " title="The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. " aria-label="The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. "></i>The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. </td></td>
</tr>
<tr class="fail">
  <td class="cell c0">SubstEquiv</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>x^2+1</pre></td>
  <td class="cell c3"><pre>x^2+1</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0 <> 1</td>
  <td class="cell c6">ATSubstEquiv_STACKERROR_SAns.<pre>ATSubstEquiv(x^2+1, x^2+1, []);</pre></td>
</tr>
<tr class="fail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">TEST_FAILED</td></td>
</tr>
<tr class="fail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The answer test failed to execute correctly: please alert your teacher. <i class="icon fa fa-exclamation-circle text-danger fa-fw " title="The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. " aria-label="The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. "></i>The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. </td></td>
</tr>
<tr class="fail">
  <td class="cell c0">SubstEquiv</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>x^2+1</pre></td>
  <td class="cell c3"><pre>x^3+1</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATSubstEquiv_STACKERROR_SAns.<pre>ATSubstEquiv(x^2+1, x^3+1, []);</pre></td>
</tr>
<tr class="fail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">TEST_FAILED</td></td>
</tr>
<tr class="fail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The answer test failed to execute correctly: please alert your teacher. <i class="icon fa fa-exclamation-circle text-danger fa-fw " title="The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. " aria-label="The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. "></i>The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. </td></td>
</tr>
<tr class="fail">
  <td class="cell c0">SubstEquiv</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>x^2+1</pre></td>
  <td class="cell c3"><pre>x^3+1</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATSubstEquiv_STACKERROR_SAns.<pre>ATSubstEquiv(x^2+1, x^3+1, []);</pre></td>
</tr>
<tr class="fail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">TEST_FAILED</td></td>
</tr>
<tr class="fail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The answer test failed to execute correctly: please alert your teacher. <i class="icon fa fa-exclamation-circle text-danger fa-fw " title="The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. " aria-label="The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. "></i>The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. </td></td>
</tr>
<tr class="fail">
  <td class="cell c0">SubstEquiv</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>X^2+1</pre></td>
  <td class="cell c3"><pre>x^2+1</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0 <> 1</td>
  <td class="cell c6">ATSubstEquiv_STACKERROR_SAns.<pre>ATSubstEquiv(X^2+1, x^2+1, []);</pre></td>
</tr>
<tr class="fail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">TEST_FAILED</td></td>
</tr>
<tr class="fail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The answer test failed to execute correctly: please alert your teacher. <i class="icon fa fa-exclamation-circle text-danger fa-fw " title="The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. " aria-label="The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. "></i>The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. </td></td>
</tr>
<tr class="fail">
  <td class="cell c0">SubstEquiv</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>x^2+y</pre></td>
  <td class="cell c3"><pre>a^2+b</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0 <> 1</td>
  <td class="cell c6">ATSubstEquiv_STACKERROR_SAns.<pre>ATSubstEquiv(x^2+y, a^2+b, []);</pre></td>
</tr>
<tr class="fail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">TEST_FAILED</td></td>
</tr>
<tr class="fail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The answer test failed to execute correctly: please alert your teacher. <i class="icon fa fa-exclamation-circle text-danger fa-fw " title="The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. " aria-label="The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. "></i>The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. </td></td>
</tr>
<tr class="fail">
  <td class="cell c0">SubstEquiv</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>x^2+y/z</pre></td>
  <td class="cell c3"><pre>a^2+c/b</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0 <> 1</td>
  <td class="cell c6">ATSubstEquiv_STACKERROR_SAns.<pre>ATSubstEquiv(x^2+y/z, a^2+c/b, []);</pre></td>
</tr>
<tr class="fail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">TEST_FAILED</td></td>
</tr>
<tr class="fail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The answer test failed to execute correctly: please alert your teacher. <i class="icon fa fa-exclamation-circle text-danger fa-fw " title="The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. " aria-label="The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. "></i>The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. </td></td>
</tr>
<tr class="fail">
  <td class="cell c0">SubstEquiv</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>y=x^2</pre></td>
  <td class="cell c3"><pre>a^2=b</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0 <> 1</td>
  <td class="cell c6">ATSubstEquiv_STACKERROR_SAns.<pre>ATSubstEquiv(y = x^2, a^2 = b, []);</pre></td>
</tr>
<tr class="fail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">TEST_FAILED</td></td>
</tr>
<tr class="fail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The answer test failed to execute correctly: please alert your teacher. <i class="icon fa fa-exclamation-circle text-danger fa-fw " title="The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. " aria-label="The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. "></i>The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. </td></td>
</tr>
<tr class="fail">
  <td class="cell c0">SubstEquiv</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>{x=1,y=2}</pre></td>
  <td class="cell c3"><pre>{x=2,y=1}</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0 <> 1</td>
  <td class="cell c6">ATSubstEquiv_STACKERROR_SAns.<pre>ATSubstEquiv({x = 1,y = 2}, {x = 2,y = 1}, []);</pre></td>
</tr>
<tr class="fail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">TEST_FAILED</td></td>
</tr>
<tr class="fail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The answer test failed to execute correctly: please alert your teacher. <i class="icon fa fa-exclamation-circle text-danger fa-fw " title="The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. " aria-label="The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. "></i>The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. </td></td>
</tr>
<tr class="notes">
  <td class="cell c0"><td colspan="6">Where a variable is also a function name.</td></td>
</tr>
<tr class="fail">
  <td class="cell c0">SubstEquiv</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>cos(a*x)/(x*(ln(x)))</pre></td>
  <td class="cell c3"><pre>cos(a*y)/(y*(ln(y)))</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0 <> 1</td>
  <td class="cell c6">ATSubstEquiv_STACKERROR_SAns.<pre>ATSubstEquiv(cos(a*x)/(x*(ln(x))), cos(a*y)/(y*(ln(y))), []);</pre></td>
</tr>
<tr class="fail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">TEST_FAILED</td></td>
</tr>
<tr class="fail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The answer test failed to execute correctly: please alert your teacher. <i class="icon fa fa-exclamation-circle text-danger fa-fw " title="The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. " aria-label="The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. "></i>The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. </td></td>
</tr>
<tr class="fail">
  <td class="cell c0">SubstEquiv</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>cos(a*x)/(x*(ln(x)))</pre></td>
  <td class="cell c3"><pre>cos(x*a)/(a*(ln(a)))</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0 <> 1</td>
  <td class="cell c6">ATSubstEquiv_STACKERROR_SAns.<pre>ATSubstEquiv(cos(a*x)/(x*(ln(x))), cos(x*a)/(a*(ln(a))), []);</pre></td>
</tr>
<tr class="fail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">TEST_FAILED</td></td>
</tr>
<tr class="fail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The answer test failed to execute correctly: please alert your teacher. <i class="icon fa fa-exclamation-circle text-danger fa-fw " title="The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. " aria-label="The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. "></i>The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. </td></td>
</tr>
<tr class="fail">
  <td class="cell c0">SubstEquiv</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>cos(a*x)/(x*(ln(x)))</pre></td>
  <td class="cell c3"><pre>cos(a*x)/(x(ln(x)))</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATSubstEquiv_STACKERROR_SAns.<pre>ATSubstEquiv(cos(a*x)/(x*(ln(x))), cos(a*x)/(x(ln(x))), []);</pre></td>
</tr>
<tr class="fail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">TEST_FAILED</td></td>
</tr>
<tr class="fail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The answer test failed to execute correctly: please alert your teacher. <i class="icon fa fa-exclamation-circle text-danger fa-fw " title="The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. " aria-label="The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. "></i>The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. </td></td>
</tr>
<tr class="fail">
  <td class="cell c0">SubstEquiv</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>cos(a*x)/(x*(ln(x)))</pre></td>
  <td class="cell c3"><pre>cos(a*y)/(y(ln(y)))</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATSubstEquiv_STACKERROR_SAns.<pre>ATSubstEquiv(cos(a*x)/(x*(ln(x))), cos(a*y)/(y(ln(y))), []);</pre></td>
</tr>
<tr class="fail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">TEST_FAILED</td></td>
</tr>
<tr class="fail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The answer test failed to execute correctly: please alert your teacher. <i class="icon fa fa-exclamation-circle text-danger fa-fw " title="The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. " aria-label="The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. "></i>The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. </td></td>
</tr>
<tr class="fail">
  <td class="cell c0">SubstEquiv</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>x+1&gt;y</pre></td>
  <td class="cell c3"><pre>y+1&gt;x</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0 <> 1</td>
  <td class="cell c6">ATSubstEquiv_STACKERROR_SAns.<pre>ATSubstEquiv(x+1 > y, y+1 > x, []);</pre></td>
</tr>
<tr class="fail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">TEST_FAILED</td></td>
</tr>
<tr class="fail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The answer test failed to execute correctly: please alert your teacher. <i class="icon fa fa-exclamation-circle text-danger fa-fw " title="The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. " aria-label="The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. "></i>The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. </td></td>
</tr>
<tr class="fail">
  <td class="cell c0">SubstEquiv</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>x+1&gt;y</pre></td>
  <td class="cell c3"><pre>x&lt;y+1</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0 <> 1</td>
  <td class="cell c6">ATSubstEquiv_STACKERROR_SAns.<pre>ATSubstEquiv(x+1 > y, x < y+1, []);</pre></td>
</tr>
<tr class="fail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">TEST_FAILED</td></td>
</tr>
<tr class="fail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The answer test failed to execute correctly: please alert your teacher. <i class="icon fa fa-exclamation-circle text-danger fa-fw " title="The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. " aria-label="The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. "></i>The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. </td></td>
</tr>
<tr class="notes">
  <td class="cell c0"><td colspan="6">Matrices</td></td>
</tr>
<tr class="fail">
  <td class="cell c0">SubstEquiv</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>matrix([1,A^2+A+1],[2,0])</pre></td>
  <td class="cell c3"><pre>matrix([1,x^2+x+1],[2,0])</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0 <> 1</td>
  <td class="cell c6">ATSubstEquiv_STACKERROR_SAns.<pre>ATSubstEquiv(matrix([1,A^2+A+1],[2,0]), matrix([1,x^2+x+1],[2,0]), []);</pre></td>
</tr>
<tr class="fail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">TEST_FAILED</td></td>
</tr>
<tr class="fail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The answer test failed to execute correctly: please alert your teacher. <i class="icon fa fa-exclamation-circle text-danger fa-fw " title="The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. " aria-label="The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. "></i>The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. </td></td>
</tr>
<tr class="fail">
  <td class="cell c0">SubstEquiv</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>matrix([B,A^2+A+1],[2,C])</pre></td>
  <td class="cell c3"><pre>matrix([y,x^2+x+1],[2,z])</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0 <> 1</td>
  <td class="cell c6">ATSubstEquiv_STACKERROR_SAns.<pre>ATSubstEquiv(matrix([B,A^2+A+1],[2,C]), matrix([y,x^2+x+1],[2,z]), []);</pre></td>
</tr>
<tr class="fail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">TEST_FAILED</td></td>
</tr>
<tr class="fail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The answer test failed to execute correctly: please alert your teacher. <i class="icon fa fa-exclamation-circle text-danger fa-fw " title="The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. " aria-label="The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. "></i>The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. </td></td>
</tr>
<tr class="fail">
  <td class="cell c0">SubstEquiv</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>matrix([B,A^2+A+1],[2,C])</pre></td>
  <td class="cell c3"><pre>matrix([y,x^2+x+1],[2,x])</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATSubstEquiv_STACKERROR_SAns.<pre>ATSubstEquiv(matrix([B,A^2+A+1],[2,C]), matrix([y,x^2+x+1],[2,x]), []);</pre></td>
</tr>
<tr class="fail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">TEST_FAILED</td></td>
</tr>
<tr class="fail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The answer test failed to execute correctly: please alert your teacher. <i class="icon fa fa-exclamation-circle text-danger fa-fw " title="The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. " aria-label="The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. "></i>The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. </td></td>
</tr>
<tr class="notes">
  <td class="cell c0"><td colspan="6">Lists</td></td>
</tr>
<tr class="fail">
  <td class="cell c0">SubstEquiv</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>[x^2+1,x^2]</pre></td>
  <td class="cell c3"><pre>[A^2+1,A^2]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0 <> 1</td>
  <td class="cell c6">ATSubstEquiv_STACKERROR_SAns.<pre>ATSubstEquiv([x^2+1,x^2], [A^2+1,A^2], []);</pre></td>
</tr>
<tr class="fail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">TEST_FAILED</td></td>
</tr>
<tr class="fail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The answer test failed to execute correctly: please alert your teacher. <i class="icon fa fa-exclamation-circle text-danger fa-fw " title="The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. " aria-label="The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. "></i>The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. </td></td>
</tr>
<tr class="fail">
  <td class="cell c0">SubstEquiv</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>[x^2-1,x^2]</pre></td>
  <td class="cell c3"><pre>[A^2+1,A^2]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATSubstEquiv_STACKERROR_SAns.<pre>ATSubstEquiv([x^2-1,x^2], [A^2+1,A^2], []);</pre></td>
</tr>
<tr class="fail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">TEST_FAILED</td></td>
</tr>
<tr class="fail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The answer test failed to execute correctly: please alert your teacher. <i class="icon fa fa-exclamation-circle text-danger fa-fw " title="The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. " aria-label="The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. "></i>The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. </td></td>
</tr>
<tr class="fail">
  <td class="cell c0">SubstEquiv</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>[A,B,C]</pre></td>
  <td class="cell c3"><pre>[B,C,A]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0 <> 1</td>
  <td class="cell c6">ATSubstEquiv_STACKERROR_SAns.<pre>ATSubstEquiv([A,B,C], [B,C,A], []);</pre></td>
</tr>
<tr class="fail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">TEST_FAILED</td></td>
</tr>
<tr class="fail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The answer test failed to execute correctly: please alert your teacher. <i class="icon fa fa-exclamation-circle text-danger fa-fw " title="The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. " aria-label="The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. "></i>The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. </td></td>
</tr>
<tr class="fail">
  <td class="cell c0">SubstEquiv</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>[A,B,C]</pre></td>
  <td class="cell c3"><pre>[B,B,A]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATSubstEquiv_STACKERROR_SAns.<pre>ATSubstEquiv([A,B,C], [B,B,A], []);</pre></td>
</tr>
<tr class="fail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">TEST_FAILED</td></td>
</tr>
<tr class="fail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The answer test failed to execute correctly: please alert your teacher. <i class="icon fa fa-exclamation-circle text-danger fa-fw " title="The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. " aria-label="The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. "></i>The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. </td></td>
</tr>
<tr class="fail">
  <td class="cell c0">SubstEquiv</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>[1,[A,B],C]</pre></td>
  <td class="cell c3"><pre>[1,[a,b],C]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0 <> 1</td>
  <td class="cell c6">ATSubstEquiv_STACKERROR_SAns.<pre>ATSubstEquiv([1,[A,B],C], [1,[a,b],C], []);</pre></td>
</tr>
<tr class="fail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">TEST_FAILED</td></td>
</tr>
<tr class="fail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The answer test failed to execute correctly: please alert your teacher. <i class="icon fa fa-exclamation-circle text-danger fa-fw " title="The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. " aria-label="The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. "></i>The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. </td></td>
</tr>
<tr class="notes">
  <td class="cell c0"><td colspan="6">Sets</td></td>
</tr>
<tr class="fail">
  <td class="cell c0">SubstEquiv</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>{x^2+1,x^2}</pre></td>
  <td class="cell c3"><pre>{A^2+1,A^2}</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0 <> 1</td>
  <td class="cell c6">ATSubstEquiv_STACKERROR_SAns.<pre>ATSubstEquiv({x^2+1,x^2}, {A^2+1,A^2}, []);</pre></td>
</tr>
<tr class="fail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">TEST_FAILED</td></td>
</tr>
<tr class="fail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The answer test failed to execute correctly: please alert your teacher. <i class="icon fa fa-exclamation-circle text-danger fa-fw " title="The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. " aria-label="The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. "></i>The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. </td></td>
</tr>
<tr class="fail">
  <td class="cell c0">SubstEquiv</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>{x^2-1,x^2}</pre></td>
  <td class="cell c3"><pre>{A^2+1,A^2}</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATSubstEquiv_STACKERROR_SAns.<pre>ATSubstEquiv({x^2-1,x^2}, {A^2+1,A^2}, []);</pre></td>
</tr>
<tr class="fail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">TEST_FAILED</td></td>
</tr>
<tr class="fail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The answer test failed to execute correctly: please alert your teacher. <i class="icon fa fa-exclamation-circle text-danger fa-fw " title="The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. " aria-label="The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. "></i>The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. </td></td>
</tr>
<tr class="fail">
  <td class="cell c0">SubstEquiv</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>{A+1,B^2,C}</pre></td>
  <td class="cell c3"><pre>{B,C+1,A^2}</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0 <> 1</td>
  <td class="cell c6">ATSubstEquiv_STACKERROR_SAns.<pre>ATSubstEquiv({A+1,B^2,C}, {B,C+1,A^2}, []);</pre></td>
</tr>
<tr class="fail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">TEST_FAILED</td></td>
</tr>
<tr class="fail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The answer test failed to execute correctly: please alert your teacher. <i class="icon fa fa-exclamation-circle text-danger fa-fw " title="The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. " aria-label="The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. "></i>The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. </td></td>
</tr>
<tr class="fail">
  <td class="cell c0">SubstEquiv</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>{1,{A,B},C}</pre></td>
  <td class="cell c3"><pre>{1,{a,b},C}</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0 <> 1</td>
  <td class="cell c6">ATSubstEquiv_STACKERROR_SAns.<pre>ATSubstEquiv({1,{A,B},C}, {1,{a,b},C}, []);</pre></td>
</tr>
<tr class="fail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">TEST_FAILED</td></td>
</tr>
<tr class="fail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The answer test failed to execute correctly: please alert your teacher. <i class="icon fa fa-exclamation-circle text-danger fa-fw " title="The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. " aria-label="The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. "></i>The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. </td></td>
</tr>
<tr class="fail">
  <td class="cell c0">SubstEquiv</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>y=A+B</pre></td>
  <td class="cell c3"><pre>x=a+b</pre></td>
  <td class="cell c4"><pre>[x]</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATSubstEquiv_STACKERROR_SAns.<pre>ATSubstEquiv(y = A+B, x = a+b, [x]);</pre></td>
</tr>
<tr class="fail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">TEST_FAILED</td></td>
</tr>
<tr class="fail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The answer test failed to execute correctly: please alert your teacher. <i class="icon fa fa-exclamation-circle text-danger fa-fw " title="The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. " aria-label="The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. "></i>The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. </td></td>
</tr>
<tr class="fail">
  <td class="cell c0">SubstEquiv</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>y=A+B</pre></td>
  <td class="cell c3"><pre>x=a+b</pre></td>
  <td class="cell c4"><pre>[z]</pre></td>
  <td class="cell c5">0 <> 1</td>
  <td class="cell c6">ATSubstEquiv_STACKERROR_SAns.<pre>ATSubstEquiv(y = A+B, x = a+b, [z]);</pre></td>
</tr>
<tr class="fail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">TEST_FAILED</td></td>
</tr>
<tr class="fail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The answer test failed to execute correctly: please alert your teacher. <i class="icon fa fa-exclamation-circle text-danger fa-fw " title="The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. " aria-label="The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. "></i>The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. </td></td>
</tr>
<tr class="fail">
  <td class="cell c0">SubstEquiv</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>A*cos(t)+B*sin(t)</pre></td>
  <td class="cell c3"><pre>P*cos(t)+Q*sin(t)</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0 <> 1</td>
  <td class="cell c6">ATSubstEquiv_STACKERROR_SAns.<pre>ATSubstEquiv(A*cos(t)+B*sin(t), P*cos(t)+Q*sin(t), []);</pre></td>
</tr>
<tr class="fail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">TEST_FAILED</td></td>
</tr>
<tr class="fail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The answer test failed to execute correctly: please alert your teacher. <i class="icon fa fa-exclamation-circle text-danger fa-fw " title="The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. " aria-label="The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. "></i>The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. </td></td>
</tr>
<tr class="fail">
  <td class="cell c0">SubstEquiv</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>A*cos(t)+B*sin(t)</pre></td>
  <td class="cell c3"><pre>P*cos(x)+Q*sin(x)</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0 <> 1</td>
  <td class="cell c6">ATSubstEquiv_STACKERROR_SAns.<pre>ATSubstEquiv(A*cos(t)+B*sin(t), P*cos(x)+Q*sin(x), []);</pre></td>
</tr>
<tr class="fail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">TEST_FAILED</td></td>
</tr>
<tr class="fail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The answer test failed to execute correctly: please alert your teacher. <i class="icon fa fa-exclamation-circle text-danger fa-fw " title="The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. " aria-label="The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. "></i>The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. </td></td>
</tr>
<tr class="notes">
  <td class="cell c0"><td colspan="6">Fix some variables.</td></td>
</tr>
<tr class="fail">
  <td class="cell c0">SubstEquiv</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>A*cos(x)+B*sin(x)</pre></td>
  <td class="cell c3"><pre>P*cos(x)+Q*sin(x)</pre></td>
  <td class="cell c4"><pre>[x]</pre></td>
  <td class="cell c5">0 <> 1</td>
  <td class="cell c6">ATSubstEquiv_STACKERROR_SAns.<pre>ATSubstEquiv(A*cos(x)+B*sin(x), P*cos(x)+Q*sin(x), [x]);</pre></td>
</tr>
<tr class="fail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">TEST_FAILED</td></td>
</tr>
<tr class="fail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The answer test failed to execute correctly: please alert your teacher. <i class="icon fa fa-exclamation-circle text-danger fa-fw " title="The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. " aria-label="The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. "></i>The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. </td></td>
</tr>
<tr class="fail">
  <td class="cell c0">SubstEquiv</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>A*cos(t)+B*sin(t)</pre></td>
  <td class="cell c3"><pre>P*cos(x)+Q*sin(x)</pre></td>
  <td class="cell c4"><pre>[x]</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATSubstEquiv_STACKERROR_SAns.<pre>ATSubstEquiv(A*cos(t)+B*sin(t), P*cos(x)+Q*sin(x), [x]);</pre></td>
</tr>
<tr class="fail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">TEST_FAILED</td></td>
</tr>
<tr class="fail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The answer test failed to execute correctly: please alert your teacher. <i class="icon fa fa-exclamation-circle text-danger fa-fw " title="The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. " aria-label="The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. "></i>The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. </td></td>
</tr>
<tr class="fail">
  <td class="cell c0">SubstEquiv</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>A*cos(t)+B*sin(t)</pre></td>
  <td class="cell c3"><pre>P*cos(x)+Q*sin(x)</pre></td>
  <td class="cell c4"><pre>[t]</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATSubstEquiv_STACKERROR_SAns.<pre>ATSubstEquiv(A*cos(t)+B*sin(t), P*cos(x)+Q*sin(x), [t]);</pre></td>
</tr>
<tr class="fail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">TEST_FAILED</td></td>
</tr>
<tr class="fail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The answer test failed to execute correctly: please alert your teacher. <i class="icon fa fa-exclamation-circle text-danger fa-fw " title="The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. " aria-label="The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. "></i>The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. </td></td>
</tr>
<tr class="fail">
  <td class="cell c0">SubstEquiv</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>A*cos(t)+B*sin(t)</pre></td>
  <td class="cell c3"><pre>P*cos(x)+Q*sin(x)</pre></td>
  <td class="cell c4"><pre>[z]</pre></td>
  <td class="cell c5">0 <> 1</td>
  <td class="cell c6">ATSubstEquiv_STACKERROR_SAns.<pre>ATSubstEquiv(A*cos(t)+B*sin(t), P*cos(x)+Q*sin(x), [z]);</pre></td>
</tr>
<tr class="fail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">TEST_FAILED</td></td>
</tr>
<tr class="fail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The answer test failed to execute correctly: please alert your teacher. <i class="icon fa fa-exclamation-circle text-danger fa-fw " title="The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. " aria-label="The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. "></i>The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. </td></td>
</tr>
<tr class="fail">
  <td class="cell c0">SubstEquiv</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>A*cos(t)*e^x+B*sin(t)*e^x+C*si
n(2*x)+D*cos(2*x)</pre></td>
  <td class="cell c3"><pre>P*cos(t)*e^x+Q*sin(t)*e^x+R*si
n(2*x)+S*cos(2*x)</pre></td>
  <td class="cell c4"><pre>[x,t]</pre></td>
  <td class="cell c5">0 <> 1</td>
  <td class="cell c6">ATSubstEquiv_STACKERROR_SAns.<pre>ATSubstEquiv(A*cos(t)*e^x+B*sin(t)*e^x+C*sin(2*x)+D*cos(2*x), P*cos(t)*e^x+Q*sin(t)*e^x+R*sin(2*x)+S*cos(2*x), [x,t]);</pre></td>
</tr>
<tr class="fail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">TEST_FAILED</td></td>
</tr>
<tr class="fail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The answer test failed to execute correctly: please alert your teacher. <i class="icon fa fa-exclamation-circle text-danger fa-fw " title="The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. " aria-label="The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. "></i>The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. </td></td>
</tr>
<tr class="fail">
  <td class="cell c0">SubstEquiv</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>sqrt(2*g*y)</pre></td>
  <td class="cell c3"><pre>sqrt(2*g*x)</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0 <> 1</td>
  <td class="cell c6">ATSubstEquiv_STACKERROR_SAns.<pre>ATSubstEquiv(sqrt(2*g*y), sqrt(2*g*x), []);</pre></td>
</tr>
<tr class="fail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">TEST_FAILED</td></td>
</tr>
<tr class="fail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The answer test failed to execute correctly: please alert your teacher. <i class="icon fa fa-exclamation-circle text-danger fa-fw " title="The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. " aria-label="The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. "></i>The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. </td></td>
</tr>
<tr class="fail">
  <td class="cell c0">SubstEquiv</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>sqrt(2*g*y)</pre></td>
  <td class="cell c3"><pre>sqrt(2*g*x)</pre></td>
  <td class="cell c4"><pre>[g]</pre></td>
  <td class="cell c5">0 <> 1</td>
  <td class="cell c6">ATSubstEquiv_STACKERROR_SAns.<pre>ATSubstEquiv(sqrt(2*g*y), sqrt(2*g*x), [g]);</pre></td>
</tr>
<tr class="fail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">TEST_FAILED</td></td>
</tr>
<tr class="fail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The answer test failed to execute correctly: please alert your teacher. <i class="icon fa fa-exclamation-circle text-danger fa-fw " title="The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. " aria-label="The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. "></i>The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. </td></td>
</tr>
<tr class="fail">
  <td class="cell c0">SubstEquiv</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>C1*%e^x*sin(4*x)+C2*%e^x*cos(4
*x)+C4*x*%e^-x+C3*%e^-x</pre></td>
  <td class="cell c3"><pre>e^(x)*A*cos(4*x)+B*e^(x)*sin(4
*x)+C*e^(-x)+D*x*e^(-x)</pre></td>
  <td class="cell c4"><pre>[x]</pre></td>
  <td class="cell c5">0 <> 1</td>
  <td class="cell c6">ATSubstEquiv_STACKERROR_SAns.<pre>ATSubstEquiv(C1*%e^x*sin(4*x)+C2*%e^x*cos(4*x)+C4*x*%e^-x+C3*%e^-x, e^(x)*A*cos(4*x)+B*e^(x)*sin(4*x)+C*e^(-x)+D*x*e^(-x), [x]);</pre></td>
</tr>
<tr class="fail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">TEST_FAILED</td></td>
</tr>
<tr class="fail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The answer test failed to execute correctly: please alert your teacher. <i class="icon fa fa-exclamation-circle text-danger fa-fw " title="The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. " aria-label="The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. "></i>The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. </td></td>
</tr>
<tr class="fail">
  <td class="cell c0">SubstEquiv</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>C1*%e^x*sin(4*x)+C2*%e^x*cos(4
*x)+C4*x*%e^-x+C3*%e^-x</pre></td>
  <td class="cell c3"><pre>C4*x*e^(-x)+e^(x)*C1*cos(4*x)+
C2*e^(x)*sin(4*x)+C3*e^(-x)</pre></td>
  <td class="cell c4"><pre>[x]</pre></td>
  <td class="cell c5">0 <> 1</td>
  <td class="cell c6">ATSubstEquiv_STACKERROR_SAns.<pre>ATSubstEquiv(C1*%e^x*sin(4*x)+C2*%e^x*cos(4*x)+C4*x*%e^-x+C3*%e^-x, C4*x*e^(-x)+e^(x)*C1*cos(4*x)+C2*e^(x)*sin(4*x)+C3*e^(-x), [x]);</pre></td>
</tr>
<tr class="fail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">TEST_FAILED</td></td>
</tr>
<tr class="fail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The answer test failed to execute correctly: please alert your teacher. <i class="icon fa fa-exclamation-circle text-danger fa-fw " title="The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. " aria-label="The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. "></i>The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. </td></td>
</tr>
<tr class="fail">
  <td class="cell c0">SubstEquiv</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>C1*%e^x*sin(4*x)+C2*%e^x*cos(4
*x)+C4*x*%e^-x+C3*%e^-x</pre></td>
  <td class="cell c3"><pre>A*x*e^(-x)+e^(x)*B*cos(4*x)+C*
e^(x)*sin(4*x)+D*e^(-x)</pre></td>
  <td class="cell c4"><pre>[x]</pre></td>
  <td class="cell c5">0 <> 1</td>
  <td class="cell c6">ATSubstEquiv_STACKERROR_SAns.<pre>ATSubstEquiv(C1*%e^x*sin(4*x)+C2*%e^x*cos(4*x)+C4*x*%e^-x+C3*%e^-x, A*x*e^(-x)+e^(x)*B*cos(4*x)+C*e^(x)*sin(4*x)+D*e^(-x), [x]);</pre></td>
</tr>
<tr class="fail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">TEST_FAILED</td></td>
</tr>
<tr class="fail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The answer test failed to execute correctly: please alert your teacher. <i class="icon fa fa-exclamation-circle text-danger fa-fw " title="The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. " aria-label="The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. "></i>The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. </td></td>
</tr>
<tr class="fail">
  <td class="cell c0">SubstEquiv</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>C1*%e^x*sin(4*x)+C2*%e^x*cos(4
*x)+C4*x*%e^-x+C3*%e^-x</pre></td>
  <td class="cell c3"><pre>e^(x)*C1*cos(4*x)+C2*e^(x)*sin
(4*x)+C3*e^(-x)+C4*x*e^(-x)</pre></td>
  <td class="cell c4"><pre>[x]</pre></td>
  <td class="cell c5">0 <> 1</td>
  <td class="cell c6">ATSubstEquiv_STACKERROR_SAns.<pre>ATSubstEquiv(C1*%e^x*sin(4*x)+C2*%e^x*cos(4*x)+C4*x*%e^-x+C3*%e^-x, e^(x)*C1*cos(4*x)+C2*e^(x)*sin(4*x)+C3*e^(-x)+C4*x*e^(-x), [x]);</pre></td>
</tr>
<tr class="fail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">TEST_FAILED</td></td>
</tr>
<tr class="fail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The answer test failed to execute correctly: please alert your teacher. <i class="icon fa fa-exclamation-circle text-danger fa-fw " title="The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. " aria-label="The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. "></i>The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. </td></td>
</tr></tbody></table></div>
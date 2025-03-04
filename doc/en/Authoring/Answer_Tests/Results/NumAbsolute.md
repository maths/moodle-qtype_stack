# NumAbsolute: Answer test results

This page exposes the results of running answer tests on STACK test cases.  This page is automatically generated from the STACK unit tests and is designed to show question authors what answer tests actually do.  This includes cases where answer tests currentl fail, which gives a negative expected mark.  Comments and further test cases are very welcome.



<h2>NumAbsolute</h2><div class="no-overflow"><table class="flexible table table-striped table-hover generaltable generalbox stacktestsuite"><thead><tr><th class="header c0" scope="col">Test<div class="commands"></div></th><th class="header c1" scope="col">?<div class="commands"></div></th><th class="header c2" scope="col">Student response<div class="commands"></div></th><th class="header c3" scope="col">Teacher answer<div class="commands"></div></th><th class="header c4" scope="col">Opt<div class="commands"></div></th><th class="header c5" scope="col">Mark<div class="commands"></div></th><th class="header c6" scope="col">Answer note<div class="commands"></div></th>
</tr></thead><tbody>
<tr class="notes">
  <td class="cell c0"><td colspan="6">Basic tests</td></td>
</tr>
<tr class="expectedfail">
  <td class="cell c0">NumAbsolute</td>
  <td class="cell c1"><span style="color:orange;"><i class="fa fa-adjust"></i></span></td>
  <td class="cell c2"><pre>1/0</pre></td>
  <td class="cell c3"><pre>0</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">-1</td>
  <td class="cell c6">ATNumAbsolute_STACKERROR_SAns.</td>
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
  <td class="cell c0">NumAbsolute</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>0</pre></td>
  <td class="cell c3"><pre>1/0</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">-1</td>
  <td class="cell c6">ATNumAbsolute_STACKERROR_SAns.<pre>ATNumAbsolute(0, 1/0, 0.05);</pre></td>
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
  <td class="cell c0">NumAbsolute</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>0</pre></td>
  <td class="cell c3"><pre>0</pre></td>
  <td class="cell c4"><pre>1/0</pre></td>
  <td class="cell c5">-1</td>
  <td class="cell c6">ATNumAbsolute_STACKERROR_SAns.<pre>ATNumAbsolute(0, 0, 1/0);</pre></td>
</tr>
<tr class="fail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">TEST_FAILED</td></td>
</tr>
<tr class="fail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The answer test failed to execute correctly: please alert your teacher. <i class="icon fa fa-exclamation-circle text-danger fa-fw " title="The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. " aria-label="The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. "></i>The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. </td></td>
</tr>
<tr class="expectedfail">
  <td class="cell c0">NumAbsolute</td>
  <td class="cell c1"><span style="color:orange;"><i class="fa fa-adjust"></i></span></td>
  <td class="cell c2"><pre>0</pre></td>
  <td class="cell c3"><pre>(x</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">-1</td>
  <td class="cell c6">ATNumAbsoluteTEST_FAILED-Empty TA.</td>
</tr>
<tr class="expectedfail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The answer test failed to execute correctly: please alert your teacher. Attempted to execute an answer test with an empty teacher answer, probably a CAS validation problem when authoring the question.</td></td>
</tr>
<tr class="fail">
  <td class="cell c0">NumAbsolute</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>1</pre></td>
  <td class="cell c3"><pre>0</pre></td>
  <td class="cell c4"><pre>(x</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumAbsolute_STACKERROR_SAns.<pre>ATNumAbsolute(1, 0, 0.05);</pre></td>
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
  <td class="cell c0"><td colspan="6">No option, so 5%</td></td>
</tr>
<tr class="fail">
  <td class="cell c0">NumAbsolute</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>1.1</pre></td>
  <td class="cell c3"><pre>1</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumAbsolute_STACKERROR_SAns.<pre>ATNumAbsolute(1.1, 1, 0.05);</pre></td>
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
  <td class="cell c0">NumAbsolute</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>1.05</pre></td>
  <td class="cell c3"><pre>1</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0 <> 1</td>
  <td class="cell c6">ATNumAbsolute_STACKERROR_SAns.<pre>ATNumAbsolute(1.05, 1, 0.05);</pre></td>
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
  <td class="cell c0"><td colspan="6">Options passed</td></td>
</tr>
<tr class="fail">
  <td class="cell c0">NumAbsolute</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>1.05</pre></td>
  <td class="cell c3"><pre>1</pre></td>
  <td class="cell c4"><pre>0.1</pre></td>
  <td class="cell c5">0 <> 1</td>
  <td class="cell c6">ATNumAbsolute_STACKERROR_SAns.<pre>ATNumAbsolute(1.05, 1, 0.1);</pre></td>
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
  <td class="cell c0">NumAbsolute</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>1.05</pre></td>
  <td class="cell c3"><pre>3</pre></td>
  <td class="cell c4"><pre>0.1</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumAbsolute_STACKERROR_SAns.<pre>ATNumAbsolute(1.05, 3, 0.1);</pre></td>
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
  <td class="cell c0">NumAbsolute</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>3.14</pre></td>
  <td class="cell c3"><pre>pi</pre></td>
  <td class="cell c4"><pre>0.001</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumAbsolute_STACKERROR_SAns.<pre>ATNumAbsolute(3.14, %pi, 0.001);</pre></td>
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
  <td class="cell c0">NumAbsolute</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>1.41e-2</pre></td>
  <td class="cell c3"><pre>1.41e-2</pre></td>
  <td class="cell c4"><pre>0.0001</pre></td>
  <td class="cell c5">0 <> 1</td>
  <td class="cell c6">ATNumAbsolute_STACKERROR_SAns.<pre>ATNumAbsolute(1.41E-2, 1.41E-2, 0.0001);</pre></td>
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
  <td class="cell c0">NumAbsolute</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>0.0141</pre></td>
  <td class="cell c3"><pre>1.41e-2</pre></td>
  <td class="cell c4"><pre>0.0001</pre></td>
  <td class="cell c5">0 <> 1</td>
  <td class="cell c6">ATNumAbsolute_STACKERROR_SAns.<pre>ATNumAbsolute(0.0141, 1.41E-2, 0.0001);</pre></td>
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
  <td class="cell c0">NumAbsolute</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>0.00141</pre></td>
  <td class="cell c3"><pre>0.00141</pre></td>
  <td class="cell c4"><pre>0.0001</pre></td>
  <td class="cell c5">0 <> 1</td>
  <td class="cell c6">ATNumAbsolute_STACKERROR_SAns.<pre>ATNumAbsolute(0.00141, 0.00141, 0.0001);</pre></td>
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
  <td class="cell c0">NumAbsolute</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>0.00141</pre></td>
  <td class="cell c3"><pre>1.41*10^-3</pre></td>
  <td class="cell c4"><pre>0.0001</pre></td>
  <td class="cell c5">0 <> 1</td>
  <td class="cell c6">ATNumAbsolute_STACKERROR_SAns.<pre>ATNumAbsolute(0.00141, 1.41*10^-3, 0.0001);</pre></td>
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
  <td class="cell c0">NumAbsolute</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>1.41*10^-3</pre></td>
  <td class="cell c3"><pre>1.41*10^-3</pre></td>
  <td class="cell c4"><pre>0.0001</pre></td>
  <td class="cell c5">0 <> 1</td>
  <td class="cell c6">ATNumAbsolute_STACKERROR_SAns.<pre>ATNumAbsolute(1.41*10^-3, 1.41*10^-3, 0.0001);</pre></td>
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
  <td class="cell c0">NumAbsolute</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>[3.141,1.414]</pre></td>
  <td class="cell c3"><pre>[pi,sqrt(2)]</pre></td>
  <td class="cell c4"><pre>0.01</pre></td>
  <td class="cell c5">0 <> 1</td>
  <td class="cell c6">ATNumAbsolute_STACKERROR_SAns.<pre>ATNumAbsolute([3.141,1.414], [%pi,sqrt(2)], 0.01);</pre></td>
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
  <td class="cell c0">NumAbsolute</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>[3,1.414]</pre></td>
  <td class="cell c3"><pre>[pi,sqrt(2)]</pre></td>
  <td class="cell c4"><pre>0.01</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumAbsolute_STACKERROR_SAns.<pre>ATNumAbsolute([3,1.414], [%pi,sqrt(2)], 0.01);</pre></td>
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
  <td class="cell c0">NumAbsolute</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>[3,1.414]</pre></td>
  <td class="cell c3"><pre>{pi,sqrt(2)}</pre></td>
  <td class="cell c4"><pre>0.01</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumAbsolute_STACKERROR_SAns.<pre>ATNumAbsolute([3,1.414], {%pi,sqrt(2)}, 0.01);</pre></td>
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
  <td class="cell c0">NumAbsolute</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>{1.414,3.1}</pre></td>
  <td class="cell c3"><pre>{significantfigures(pi,6),sqrt
(2)}</pre></td>
  <td class="cell c4"><pre>0.01</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumAbsolute_STACKERROR_SAns.<pre>ATNumAbsolute({1.414,3.1}, {significantfigures(%pi,6),sqrt(2)}, 0.01);</pre></td>
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
  <td class="cell c0">NumAbsolute</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>{1,1.414,3.1,2}</pre></td>
  <td class="cell c3"><pre>{1,2,pi,sqrt(2)}</pre></td>
  <td class="cell c4"><pre>0.1</pre></td>
  <td class="cell c5">0 <> 1</td>
  <td class="cell c6">ATNumAbsolute_STACKERROR_SAns.<pre>ATNumAbsolute({1,1.414,3.1,2}, {1,2,%pi,sqrt(2)}, 0.1);</pre></td>
</tr>
<tr class="fail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">TEST_FAILED</td></td>
</tr>
<tr class="fail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The answer test failed to execute correctly: please alert your teacher. <i class="icon fa fa-exclamation-circle text-danger fa-fw " title="The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. " aria-label="The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. "></i>The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. </td></td>
</tr></tbody></table></div>
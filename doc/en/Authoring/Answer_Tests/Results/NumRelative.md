# NumRelative: Answer test results

This page exposes the results of running answer tests on STACK test cases.  This page is automatically generated from the STACK unit tests and is designed to show question authors what answer tests actually do.  This includes cases where answer tests currentl fail, which gives a negative expected mark.  Comments and further test cases are very welcome.



<h2>NumRelative</h2><div class="no-overflow"><table class="flexible table table-striped table-hover generaltable generalbox stacktestsuite"><thead><tr><th class="header c0" scope="col">Test<div class="commands"></div></th><th class="header c1" scope="col">?<div class="commands"></div></th><th class="header c2" scope="col">Student response<div class="commands"></div></th><th class="header c3" scope="col">Teacher answer<div class="commands"></div></th><th class="header c4" scope="col">Opt<div class="commands"></div></th><th class="header c5" scope="col">Mark<div class="commands"></div></th><th class="header c6" scope="col">Answer note<div class="commands"></div></th>
</tr></thead><tbody>
<tr class="notes">
  <td class="cell c0"><td colspan="6">Basic tests</td></td>
</tr>
<tr class="expectedfail">
  <td class="cell c0">NumRelative</td>
  <td class="cell c1"><span style="color:orange;"><i class="fa fa-adjust"></i></span></td>
  <td class="cell c2"><pre>1/0</pre></td>
  <td class="cell c3"><pre>0</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">-1</td>
  <td class="cell c6">ATNumRelative_STACKERROR_SAns.</td>
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
  <td class="cell c0">NumRelative</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>0</pre></td>
  <td class="cell c3"><pre>1/0</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">-1</td>
  <td class="cell c6">ATNumRelative_STACKERROR_SAns.<pre>ATNumRelative(0, 1/0, 0.05);</pre></td>
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
  <td class="cell c0">NumRelative</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>0</pre></td>
  <td class="cell c3"><pre>0</pre></td>
  <td class="cell c4"><pre>1/0</pre></td>
  <td class="cell c5">-1</td>
  <td class="cell c6">ATNumRelative_STACKERROR_SAns.<pre>ATNumRelative(0, 0, 1/0);</pre></td>
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
  <td class="cell c0">NumRelative</td>
  <td class="cell c1"><span style="color:orange;"><i class="fa fa-adjust"></i></span></td>
  <td class="cell c2"><pre>0</pre></td>
  <td class="cell c3"><pre>(x</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">-1</td>
  <td class="cell c6">ATNumRelativeTEST_FAILED-Empty TA.</td>
</tr>
<tr class="expectedfail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The answer test failed to execute correctly: please alert your teacher. Attempted to execute an answer test with an empty teacher answer, probably a CAS validation problem when authoring the question.</td></td>
</tr>
<tr class="fail">
  <td class="cell c0">NumRelative</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>1.5</pre></td>
  <td class="cell c3"><pre>1.5</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">-1</td>
  <td class="cell c6">ATNumRelative_STACKERROR_SAns.<pre>ATNumRelative(1.5, 1.5, x);</pre></td>
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
  <td class="cell c0">NumRelative</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>1</pre></td>
  <td class="cell c3"><pre>0</pre></td>
  <td class="cell c4"><pre>(x</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumRelative_STACKERROR_SAns.<pre>ATNumRelative(1, 0, 0.05);</pre></td>
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
  <td class="cell c0">NumRelative</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>x=1.5</pre></td>
  <td class="cell c3"><pre>1.5</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumRelative_STACKERROR_SAns.<pre>ATNumRelative(x = 1.5, 1.5, 0.05);</pre></td>
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
  <td class="cell c0">NumRelative</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>1.5</pre></td>
  <td class="cell c3"><pre>x=1.5</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumRelative_STACKERROR_SAns.<pre>ATNumRelative(1.5, x = 1.5, 0.05);</pre></td>
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
  <td class="cell c0">NumRelative</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>1.1</pre></td>
  <td class="cell c3"><pre>1</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumRelative_STACKERROR_SAns.<pre>ATNumRelative(1.1, 1, 0.05);</pre></td>
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
  <td class="cell c0">NumRelative</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>1.05</pre></td>
  <td class="cell c3"><pre>1</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0 <> 1</td>
  <td class="cell c6">ATNumRelative_STACKERROR_SAns.<pre>ATNumRelative(1.05, 1, 0.05);</pre></td>
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
  <td class="cell c0">NumRelative</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>0.95</pre></td>
  <td class="cell c3"><pre>1</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0 <> 1</td>
  <td class="cell c6">ATNumRelative_STACKERROR_SAns.<pre>ATNumRelative(0.95, 1, 0.05);</pre></td>
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
  <td class="cell c0">NumRelative</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>0.949</pre></td>
  <td class="cell c3"><pre>1</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumRelative_STACKERROR_SAns.<pre>ATNumRelative(0.949, 1, 0.05);</pre></td>
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
  <td class="cell c0">NumRelative</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>1.05e33</pre></td>
  <td class="cell c3"><pre>1e33</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0 <> 1</td>
  <td class="cell c6">ATNumRelative_STACKERROR_SAns.<pre>ATNumRelative(1.05E33, 1E33, 0.05);</pre></td>
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
  <td class="cell c0">NumRelative</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>1.06e33</pre></td>
  <td class="cell c3"><pre>1e33</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumRelative_STACKERROR_SAns.<pre>ATNumRelative(1.06E33, 1E33, 0.05);</pre></td>
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
  <td class="cell c0">NumRelative</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>0.95e33</pre></td>
  <td class="cell c3"><pre>1e33</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0 <> 1</td>
  <td class="cell c6">ATNumRelative_STACKERROR_SAns.<pre>ATNumRelative(0.95E33, 1E33, 0.05);</pre></td>
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
  <td class="cell c0">NumRelative</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>0.949e33</pre></td>
  <td class="cell c3"><pre>1e33</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumRelative_STACKERROR_SAns.<pre>ATNumRelative(0.949E33, 1E33, 0.05);</pre></td>
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
  <td class="cell c0">NumRelative</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>1.05e-33</pre></td>
  <td class="cell c3"><pre>1e-33</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0 <> 1</td>
  <td class="cell c6">ATNumRelative_STACKERROR_SAns.<pre>ATNumRelative(1.05E-33, 1E-33, 0.05);</pre></td>
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
  <td class="cell c0">NumRelative</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>1.06e-33</pre></td>
  <td class="cell c3"><pre>1e-33</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumRelative_STACKERROR_SAns.<pre>ATNumRelative(1.06E-33, 1E-33, 0.05);</pre></td>
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
  <td class="cell c0">NumRelative</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>0.95e-33</pre></td>
  <td class="cell c3"><pre>1e-33</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0 <> 1</td>
  <td class="cell c6">ATNumRelative_STACKERROR_SAns.<pre>ATNumRelative(0.95E-33, 1E-33, 0.05);</pre></td>
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
  <td class="cell c0">NumRelative</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>0.949e-33</pre></td>
  <td class="cell c3"><pre>1e-33</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumRelative_STACKERROR_SAns.<pre>ATNumRelative(0.949E-33, 1E-33, 0.05);</pre></td>
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
  <td class="cell c0"><td colspan="6">Remove display dp etc.</td></td>
</tr>
<tr class="fail">
  <td class="cell c0">NumRelative</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>1</pre></td>
  <td class="cell c3"><pre>displaydp(1.05,2)</pre></td>
  <td class="cell c4"><pre>0.1</pre></td>
  <td class="cell c5">0 <> 1</td>
  <td class="cell c6">ATNumRelative_STACKERROR_SAns.<pre>ATNumRelative(1, displaydp(1.05,2), 0.1);</pre></td>
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
  <td class="cell c0">NumRelative</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>1000</pre></td>
  <td class="cell c3"><pre>displaysci(1.05,2,3)</pre></td>
  <td class="cell c4"><pre>0.1</pre></td>
  <td class="cell c5">0 <> 1</td>
  <td class="cell c6">ATNumRelative_STACKERROR_SAns.<pre>ATNumRelative(1000, displaysci(1.05,2,3), 0.1);</pre></td>
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
  <td class="cell c0">NumRelative</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>1.05</pre></td>
  <td class="cell c3"><pre>1</pre></td>
  <td class="cell c4"><pre>0.1</pre></td>
  <td class="cell c5">0 <> 1</td>
  <td class="cell c6">ATNumRelative_STACKERROR_SAns.<pre>ATNumRelative(1.05, 1, 0.1);</pre></td>
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
  <td class="cell c0">NumRelative</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>1.05</pre></td>
  <td class="cell c3"><pre>3</pre></td>
  <td class="cell c4"><pre>0.1</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumRelative_STACKERROR_SAns.<pre>ATNumRelative(1.05, 3, 0.1);</pre></td>
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
  <td class="cell c0">NumRelative</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>3.14</pre></td>
  <td class="cell c3"><pre>pi</pre></td>
  <td class="cell c4"><pre>0.001</pre></td>
  <td class="cell c5">0 <> 1</td>
  <td class="cell c6">ATNumRelative_STACKERROR_SAns.<pre>ATNumRelative(3.14, %pi, 0.001);</pre></td>
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
  <td class="cell c0"><td colspan="6">Infinity</td></td>
</tr>
<tr class="fail">
  <td class="cell c0">NumRelative</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>inf</pre></td>
  <td class="cell c3"><pre>0</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumRelative_STACKERROR_SAns.<pre>ATNumRelative(inf, 0, 0.05);</pre></td>
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
  <td class="cell c0">NumRelative</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>1</pre></td>
  <td class="cell c3"><pre>[1,2]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumRelative_STACKERROR_SAns.<pre>ATNumRelative(1, [1,2], 0.05);</pre></td>
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
  <td class="cell c0">NumRelative</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>[1,2]</pre></td>
  <td class="cell c3"><pre>[1,2,3]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumRelative_STACKERROR_SAns.<pre>ATNumRelative([1,2], [1,2,3], 0.05);</pre></td>
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
  <td class="cell c0">NumRelative</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>[1,2]</pre></td>
  <td class="cell c3"><pre>[1,2]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0 <> 1</td>
  <td class="cell c6">ATNumRelative_STACKERROR_SAns.<pre>ATNumRelative([1,2], [1,2], 0.05);</pre></td>
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
  <td class="cell c0">NumRelative</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>[3.141,1.414]</pre></td>
  <td class="cell c3"><pre>[pi,sqrt(2)]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0 <> 1</td>
  <td class="cell c6">ATNumRelative_STACKERROR_SAns.<pre>ATNumRelative([3.141,1.414], [%pi,sqrt(2)], 0.05);</pre></td>
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
  <td class="cell c0">NumRelative</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>[3,1.414]</pre></td>
  <td class="cell c3"><pre>[pi,sqrt(2)]</pre></td>
  <td class="cell c4"><pre>0.01</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumRelative_STACKERROR_SAns.<pre>ATNumRelative([3,1.414], [%pi,sqrt(2)], 0.01);</pre></td>
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
  <td class="cell c0">NumRelative</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>[3,1.414]</pre></td>
  <td class="cell c3"><pre>{pi,sqrt(2)}</pre></td>
  <td class="cell c4"><pre>0.01</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumRelative_STACKERROR_SAns.<pre>ATNumRelative([3,1.414], {%pi,sqrt(2)}, 0.01);</pre></td>
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
  <td class="cell c0">NumRelative</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>{1.414,3.1}</pre></td>
  <td class="cell c3"><pre>{significantfigures(pi,6),sqrt
(2)}</pre></td>
  <td class="cell c4"><pre>0.01</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumRelative_STACKERROR_SAns.<pre>ATNumRelative({1.414,3.1}, {significantfigures(%pi,6),sqrt(2)}, 0.01);</pre></td>
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
  <td class="cell c0">NumRelative</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>{1.414,3.1}</pre></td>
  <td class="cell c3"><pre>{pi,sqrt(2)}</pre></td>
  <td class="cell c4"><pre>0.1</pre></td>
  <td class="cell c5">0 <> 1</td>
  <td class="cell c6">ATNumRelative_STACKERROR_SAns.<pre>ATNumRelative({1.414,3.1}, {%pi,sqrt(2)}, 0.1);</pre></td>
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
  <td class="cell c0">NumRelative</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>{0,1,2}</pre></td>
  <td class="cell c3"><pre>{0,1,2}</pre></td>
  <td class="cell c4"><pre>0.1</pre></td>
  <td class="cell c5">0 <> 1</td>
  <td class="cell c6">ATNumRelative_STACKERROR_SAns.<pre>ATNumRelative({0,1,2}, {0,1,2}, 0.1);</pre></td>
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
  <td class="cell c0"><td colspan="6">Complex numbers</td></td>
</tr>
<tr class="fail">
  <td class="cell c0">NumRelative</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>0.99*%i</pre></td>
  <td class="cell c3"><pre>%i</pre></td>
  <td class="cell c4"><pre>0.1</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumRelative_STACKERROR_SAns.<pre>ATNumRelative(0.99*%i, %i, 0.1);</pre></td>
</tr>
<tr class="fail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">TEST_FAILED</td></td>
</tr>
<tr class="fail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The answer test failed to execute correctly: please alert your teacher. <i class="icon fa fa-exclamation-circle text-danger fa-fw " title="The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. " aria-label="The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. "></i>The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. </td></td>
</tr></tbody></table></div>
# SysEquiv: Answer test results

This page exposes the results of running answer tests on STACK test cases.  This page is automatically generated from the STACK unit tests and is designed to show question authors what answer tests actually do.  This includes cases where answer tests currentl fail, which gives a negative expected mark.  Comments and further test cases are very welcome.



<h2>SysEquiv</h2><div class="no-overflow"><table class="flexible table table-striped table-hover generaltable generalbox stacktestsuite"><thead><tr><th class="header c0" scope="col">Test<div class="commands"></div></th><th class="header c1" scope="col">?<div class="commands"></div></th><th class="header c2" scope="col">Student response<div class="commands"></div></th><th class="header c3" scope="col">Teacher answer<div class="commands"></div></th><th class="header c4" scope="col">Opt<div class="commands"></div></th><th class="header c5" scope="col">Mark<div class="commands"></div></th><th class="header c6" scope="col">Answer note<div class="commands"></div></th>
</tr></thead><tbody>
<tr class="notes">
  <td class="cell c0"><td colspan="6">Basic tests</td></td>
</tr>
<tr class="expectedfail">
  <td class="cell c0">SysEquiv</td>
  <td class="cell c1"><span style="color:orange;"><i class="fa fa-adjust"></i></span></td>
  <td class="cell c2"><pre>1/0</pre></td>
  <td class="cell c3"><pre>[(x-1)*(x+1)=0]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">-1</td>
  <td class="cell c6">ATSysEquiv_STACKERROR_SAns.</td>
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
  <td class="cell c0">SysEquiv</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>[(x-1)*(x+1)=0]</pre></td>
  <td class="cell c3"><pre>1/0</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">-1</td>
  <td class="cell c6">ATSysEquiv_STACKERROR_SAns.<pre>ATSysEquiv([(x-1)*(x+1) = 0], 1/0);</pre></td>
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
  <td class="cell c0">SysEquiv</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>1</pre></td>
  <td class="cell c3"><pre>[(x-1)*(x+1)=0]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATSysEquiv_STACKERROR_SAns.<pre>ATSysEquiv(1, [(x-1)*(x+1) = 0]);</pre></td>
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
  <td class="cell c0">SysEquiv</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>[(x-1)*(x+1)=0]</pre></td>
  <td class="cell c3"><pre>1</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATSysEquiv_STACKERROR_SAns.<pre>ATSysEquiv([(x-1)*(x+1) = 0], 1);</pre></td>
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
  <td class="cell c0">SysEquiv</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>[1]</pre></td>
  <td class="cell c3"><pre>[90=v*t,90=(v+5)*(t-1/4)]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATSysEquiv_STACKERROR_SAns.<pre>ATSysEquiv([1], [90 = v*t,90 = (v+5)*(t-1/4)]);</pre></td>
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
  <td class="cell c0">SysEquiv</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>[(x-1)*(x+1)=0]</pre></td>
  <td class="cell c3"><pre>[1]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATSysEquiv_STACKERROR_SAns.<pre>ATSysEquiv([(x-1)*(x+1) = 0], [1]);</pre></td>
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
  <td class="cell c0">SysEquiv</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>[x^2]</pre></td>
  <td class="cell c3"><pre>[(x-1)*(x+1)=0]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATSysEquiv_STACKERROR_SAns.<pre>ATSysEquiv([x^2], [(x-1)*(x+1) = 0]);</pre></td>
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
  <td class="cell c0">SysEquiv</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>[90=v*t^t,90=(v+5)*(t-1/4)]</pre></td>
  <td class="cell c3"><pre>[90=v*t,90=(v+5)*(t-1/4)]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATSysEquiv_STACKERROR_SAns.<pre>ATSysEquiv([90 = v*t^t,90 = (v+5)*(t-1/4)], [90 = v*t,90 = (v+5)*(t-1/4)]);</pre></td>
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
  <td class="cell c0">SysEquiv</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>[90=v*t,90=(v+5)*(t-1/4)]</pre></td>
  <td class="cell c3"><pre>[90=v*t^t,90=(v+5)*(t-1/4)]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATSysEquiv_STACKERROR_SAns.<pre>ATSysEquiv([90 = v*t,90 = (v+5)*(t-1/4)], [90 = v*t^t,90 = (v+5)*(t-1/4)]);</pre></td>
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
  <td class="cell c0"><td colspan="6">Tests of equivalence</td></td>
</tr>
<tr class="fail">
  <td class="cell c0">SysEquiv</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>[x^2=1]</pre></td>
  <td class="cell c3"><pre>[(x-1)*(x+1)=0]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0 <> 1</td>
  <td class="cell c6">ATSysEquiv_STACKERROR_SAns.<pre>ATSysEquiv([x^2 = 1], [(x-1)*(x+1) = 0]);</pre></td>
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
  <td class="cell c0">SysEquiv</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>[x^2+y^2=4,y=x]</pre></td>
  <td class="cell c3"><pre>[y=x,y^2=2]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0 <> 1</td>
  <td class="cell c6">ATSysEquiv_STACKERROR_SAns.<pre>ATSysEquiv([x^2+y^2 = 4,y = x], [y = x,y^2 = 2]);</pre></td>
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
  <td class="cell c0">SysEquiv</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>[x^2+y^2=2,y=x]</pre></td>
  <td class="cell c3"><pre>[y=x,y^2=2]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATSysEquiv_STACKERROR_SAns.<pre>ATSysEquiv([x^2+y^2 = 2,y = x], [y = x,y^2 = 2]);</pre></td>
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
  <td class="cell c0">SysEquiv</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>[x=1]</pre></td>
  <td class="cell c3"><pre>[(x-1)*(x+1)=0,(x-1)*(x-3)=0]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0 <> 1</td>
  <td class="cell c6">ATSysEquiv_STACKERROR_SAns.<pre>ATSysEquiv([x = 1], [(x-1)*(x+1) = 0,(x-1)*(x-3) = 0]);</pre></td>
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
  <td class="cell c0">SysEquiv</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>[3*a+b-c=2, a-b+2*c=5,b+c=5]</pre></td>
  <td class="cell c3"><pre>[a=1,b=2,c=3]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0 <> 1</td>
  <td class="cell c6">ATSysEquiv_STACKERROR_SAns.<pre>ATSysEquiv([3*a+b-c = 2,a-b+2*c = 5,b+c = 5], [a = 1,b = 2,c = 3]);</pre></td>
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
  <td class="cell c0">SysEquiv</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>[a=1,b=2,c=3]</pre></td>
  <td class="cell c3"><pre>[3*a+b-c=2, a-b+2*c=5,b+c=5]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0 <> 1</td>
  <td class="cell c6">ATSysEquiv_STACKERROR_SAns.<pre>ATSysEquiv([a = 1,b = 2,c = 3], [3*a+b-c = 2,a-b+2*c = 5,b+c = 5]);</pre></td>
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
  <td class="cell c0">SysEquiv</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>[x^2=1]</pre></td>
  <td class="cell c3"><pre>[(x-1)*(x+1)*(x-2)=0]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATSysEquiv_STACKERROR_SAns.<pre>ATSysEquiv([x^2 = 1], [(x-1)*(x+1)*(x-2) = 0]);</pre></td>
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
  <td class="cell c0">SysEquiv</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>[x=1,y=-1]</pre></td>
  <td class="cell c3"><pre>[(x-1)*(y+1)=0]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATSysEquiv_STACKERROR_SAns.<pre>ATSysEquiv([x = 1,y = -1], [(x-1)*(y+1) = 0]);</pre></td>
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
  <td class="cell c0">SysEquiv</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>[x=1]</pre></td>
  <td class="cell c3"><pre>[(x-1)*(x+1)=0]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATSysEquiv_STACKERROR_SAns.<pre>ATSysEquiv([x = 1], [(x-1)*(x+1) = 0]);</pre></td>
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
  <td class="cell c0">SysEquiv</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>[x=1]</pre></td>
  <td class="cell c3"><pre>[(x-1)*(x+1)*y=0]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATSysEquiv_STACKERROR_SAns.<pre>ATSysEquiv([x = 1], [(x-1)*(x+1)*y = 0]);</pre></td>
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
  <td class="cell c0">SysEquiv</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>[90=v*t,90=(v+5)*(t-1/4)]</pre></td>
  <td class="cell c3"><pre>[90=v*t,90=(v+5)*(t-1/4)]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0 <> 1</td>
  <td class="cell c6">ATSysEquiv_STACKERROR_SAns.<pre>ATSysEquiv([90 = v*t,90 = (v+5)*(t-1/4)], [90 = v*t,90 = (v+5)*(t-1/4)]);</pre></td>
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
  <td class="cell c0">SysEquiv</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>[90=v*t,90=(v+5)*(t*x-1/4)]</pre></td>
  <td class="cell c3"><pre>[90=v*t,90=(v+5)*(t-1/4)]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATSysEquiv_STACKERROR_SAns.<pre>ATSysEquiv([90 = v*t,90 = (v+5)*(t*x-1/4)], [90 = v*t,90 = (v+5)*(t-1/4)]);</pre></td>
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
  <td class="cell c0">SysEquiv</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>[90=v*t,90=(v+5)*(t-1/4)]</pre></td>
  <td class="cell c3"><pre>[90=v*t,90=(v+5)*(t*x-1/4)]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATSysEquiv_STACKERROR_SAns.<pre>ATSysEquiv([90 = v*t,90 = (v+5)*(t-1/4)], [90 = v*t,90 = (v+5)*(t*x-1/4)]);</pre></td>
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
  <td class="cell c0">SysEquiv</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>[90=v*t]</pre></td>
  <td class="cell c3"><pre>[90=v*t,90=(v+5)*(t-1/4)]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATSysEquiv_STACKERROR_SAns.<pre>ATSysEquiv([90 = v*t], [90 = v*t,90 = (v+5)*(t-1/4)]);</pre></td>
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
  <td class="cell c0">SysEquiv</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>[90=v*t,90=(v+5)*(t-1/4),90=(v
+6)*(t-1/5)]</pre></td>
  <td class="cell c3"><pre>[90=v*t,90=(v+5)*(t-1/4)]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATSysEquiv_STACKERROR_SAns.<pre>ATSysEquiv([90 = v*t,90 = (v+5)*(t-1/4),90 = (v+6)*(t-1/5)], [90 = v*t,90 = (v+5)*(t-1/4)]);</pre></td>
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
  <td class="cell c0">SysEquiv</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>[90=v*t,90=(v+5)*(t-1/4),90=(v
+6)*(t-1/5),90=(v+7)*(t-1/4),9
0=(v+8)*(t-1/3)]</pre></td>
  <td class="cell c3"><pre>[90=v*t,90=(v+5)*(t-1/4)]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATSysEquiv_STACKERROR_SAns.<pre>ATSysEquiv([90 = v*t,90 = (v+5)*(t-1/4),90 = (v+6)*(t-1/5),90 = (v+7)*(t-1/4),90 = (v+8)*(t-1/3)], [90 = v*t,90 = (v+5)*(t-1/4)]);</pre></td>
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
  <td class="cell c0"><td colspan="6">Wrong variables</td></td>
</tr>
<tr class="fail">
  <td class="cell c0">SysEquiv</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>[b^2=a,a=9]</pre></td>
  <td class="cell c3"><pre>[x^2=y,y=9]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATSysEquiv_STACKERROR_SAns.<pre>ATSysEquiv([b^2 = a,a = 9], [x^2 = y,y = 9]);</pre></td>
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
  <td class="cell c0">SysEquiv</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>[x^2=4]</pre></td>
  <td class="cell c3"><pre>[x^2=4,y=9]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATSysEquiv_STACKERROR_SAns.<pre>ATSysEquiv([x^2 = 4], [x^2 = 4,y = 9]);</pre></td>
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
  <td class="cell c0">SysEquiv</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>[d=90,d=v*t,d=(v+5)*(t-1/4)]</pre></td>
  <td class="cell c3"><pre>[90=v*t,90=(v+5)*(t-1/4)]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATSysEquiv_STACKERROR_SAns.<pre>ATSysEquiv([d = 90,d = v*t,d = (v+5)*(t-1/4)], [90 = v*t,90 = (v+5)*(t-1/4)]);</pre></td>
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
  <td class="cell c0">SysEquiv</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>stack_eval_assignments([d=90,d
=v*t,d=(v+5)*(t-1/4)])</pre></td>
  <td class="cell c3"><pre>[90=v*t,90=(v+5)*(t-1/4)]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0 <> 1</td>
  <td class="cell c6">ATSysEquiv_STACKERROR_SAns.<pre>ATSysEquiv(stack_eval_assignments([d = 90,d = v*t,d = (v+5)*(t-1/4)]), [90 = v*t,90 = (v+5)*(t-1/4)]);</pre></td>
</tr>
<tr class="fail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">TEST_FAILED</td></td>
</tr>
<tr class="fail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The answer test failed to execute correctly: please alert your teacher. <i class="icon fa fa-exclamation-circle text-danger fa-fw " title="The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. " aria-label="The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. "></i>The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. </td></td>
</tr></tbody></table></div>
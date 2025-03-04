# Levenshtein: Answer test results

This page exposes the results of running answer tests on STACK test cases.  This page is automatically generated from the STACK unit tests and is designed to show question authors what answer tests actually do.  This includes cases where answer tests currentl fail, which gives a negative expected mark.  Comments and further test cases are very welcome.



<h2>Levenshtein</h2><div class="no-overflow"><table class="flexible table table-striped table-hover generaltable generalbox stacktestsuite"><thead><tr><th class="header c0" scope="col">Test<div class="commands"></div></th><th class="header c1" scope="col">?<div class="commands"></div></th><th class="header c2" scope="col">Student response<div class="commands"></div></th><th class="header c3" scope="col">Teacher answer<div class="commands"></div></th><th class="header c4" scope="col">Opt<div class="commands"></div></th><th class="header c5" scope="col">Mark<div class="commands"></div></th><th class="header c6" scope="col">Answer note<div class="commands"></div></th>
</tr></thead><tbody>
<tr class="pass">
  <td class="cell c0">Levenshtein</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>&quot;Hello&quot;</pre></td>
  <td class="cell c3"><pre>&quot;Hello&quot;</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">STACKERROR_OPTION.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">TEST_FAILED</td></td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The answer test failed to execute correctly: please alert your teacher. Missing option when executing the test. </td></td>
</tr>
<tr class="expectedfail">
  <td class="cell c0">Levenshtein</td>
  <td class="cell c1"><span style="color:orange;"><i class="fa fa-adjust"></i></span></td>
  <td class="cell c2"><pre>1/0</pre></td>
  <td class="cell c3"><pre>&quot;Hello&quot;</pre></td>
  <td class="cell c4"><pre>0.9</pre></td>
  <td class="cell c5">-1</td>
  <td class="cell c6">ATLevenshtein_STACKERROR_SAns.</td>
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
  <td class="cell c0">Levenshtein</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>x^2</pre></td>
  <td class="cell c3"><pre>&quot;Hello&quot;</pre></td>
  <td class="cell c4"><pre>0.9</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATLevenshtein_STACKERROR_SAns.<pre>ATLevenshtein(x^2, "Hello", 0.9);</pre></td>
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
  <td class="cell c0">Levenshtein</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>&quot;Hello&quot;</pre></td>
  <td class="cell c3"><pre>&quot;Hello&quot;</pre></td>
  <td class="cell c4"><pre>0.9</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATLevenshtein_STACKERROR_SAns.<pre>ATLevenshtein("Hello", "Hello", 0.9);</pre></td>
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
  <td class="cell c0">Levenshtein</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>&quot;Hello&quot;</pre></td>
  <td class="cell c3"><pre>[&quot;Hello&quot;]</pre></td>
  <td class="cell c4"><pre>0.9</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATLevenshtein_STACKERROR_SAns.<pre>ATLevenshtein("Hello", ["Hello"], 0.9);</pre></td>
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
  <td class="cell c0">Levenshtein</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>&quot;Hello&quot;</pre></td>
  <td class="cell c3"><pre>[[&quot;Hello&quot;]]</pre></td>
  <td class="cell c4"><pre>0.9</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATLevenshtein_STACKERROR_SAns.<pre>ATLevenshtein("Hello", [["Hello"]], 0.9);</pre></td>
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
  <td class="cell c0">Levenshtein</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>&quot;Hello&quot;</pre></td>
  <td class="cell c3"><pre>[[&quot;Hello&quot;], x^2]</pre></td>
  <td class="cell c4"><pre>0.9</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATLevenshtein_STACKERROR_SAns.<pre>ATLevenshtein("Hello", [["Hello"],x^2], 0.9);</pre></td>
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
  <td class="cell c0">Levenshtein</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>&quot;Hello&quot;</pre></td>
  <td class="cell c3"><pre>[[&quot;Hello&quot;], [x^2]]</pre></td>
  <td class="cell c4"><pre>0.9</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATLevenshtein_STACKERROR_SAns.<pre>ATLevenshtein("Hello", [["Hello"],[x^2]], 0.9);</pre></td>
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
  <td class="cell c0">Levenshtein</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>&quot;Hello&quot;</pre></td>
  <td class="cell c3"><pre>[[&quot;Hello&quot;], [&quot;G
oodbye&quot;], [&quot;Excess&q
uot;]]</pre></td>
  <td class="cell c4"><pre>0.9</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATLevenshtein_STACKERROR_SAns.<pre>ATLevenshtein("Hello", [["Hello"],["Goodbye"],["Excess"]], 0.9);</pre></td>
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
  <td class="cell c0">Levenshtein</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>&quot;Hello&quot;</pre></td>
  <td class="cell c3"><pre>[[], [&quot;Goodbye&quot;]]</pre></td>
  <td class="cell c4"><pre>0.9</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATLevenshtein_STACKERROR_SAns.<pre>ATLevenshtein("Hello", [[],["Goodbye"]], 0.9);</pre></td>
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
  <td class="cell c0">Levenshtein</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>&quot;Hello&quot;</pre></td>
  <td class="cell c3"><pre>[[&quot;Hello&quot;], [&quot;G
oodbye&quot;]]</pre></td>
  <td class="cell c4"><pre>z</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATLevenshtein_STACKERROR_SAns.<pre>ATLevenshtein("Hello", [["Hello"],["Goodbye"]], z);</pre></td>
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
  <td class="cell c0">Levenshtein</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>&quot;Hello&quot;</pre></td>
  <td class="cell c3"><pre>[[&quot;Hello&quot;], [&quot;G
oodbye&quot;]]</pre></td>
  <td class="cell c4"><pre>[z]</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATLevenshtein_STACKERROR_SAns.<pre>ATLevenshtein("Hello", [["Hello"],["Goodbye"]], [z]);</pre></td>
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
  <td class="cell c0"><td colspan="6">Usage tests</td></td>
</tr>
<tr class="fail">
  <td class="cell c0">Levenshtein</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>&quot;Hello&quot;</pre></td>
  <td class="cell c3"><pre>[[&quot;Hello&quot;], [&quot;G
oodbye&quot;]]</pre></td>
  <td class="cell c4"><pre>0.9</pre></td>
  <td class="cell c5">0 <> 1</td>
  <td class="cell c6">ATLevenshtein_STACKERROR_SAns.<pre>ATLevenshtein("Hello", [["Hello"],["Goodbye"]], 0.9);</pre></td>
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
  <td class="cell c0">Levenshtein</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>&quot;hello&quot;</pre></td>
  <td class="cell c3"><pre>[[&quot;Hello&quot;], [&quot;G
oodbye&quot;]]</pre></td>
  <td class="cell c4"><pre>[0.9]</pre></td>
  <td class="cell c5">0 <> 1</td>
  <td class="cell c6">ATLevenshtein_STACKERROR_SAns.<pre>ATLevenshtein("hello", [["Hello"],["Goodbye"]], [0.9]);</pre></td>
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
  <td class="cell c0">Levenshtein</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>&quot;hello&quot;</pre></td>
  <td class="cell c3"><pre>[[&quot;Hello&quot;, &quot;Goo
d day&quot;, &quot;Hi&quot;], 
[&quot;Goodbye&quot;, &quot;By
e&quot;, &quot;Fairwell&quot;]
]</pre></td>
  <td class="cell c4"><pre>[0.8, CASE]</pre></td>
  <td class="cell c5">0 <> 1</td>
  <td class="cell c6">ATLevenshtein_STACKERROR_SAns.<pre>ATLevenshtein("hello", [["Hello","Good day","Hi"],["Goodbye","Bye","Fairwell"]], [0.8,CASE]);</pre></td>
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
  <td class="cell c0">Levenshtein</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>&quot;goodday&quot;</pre></td>
  <td class="cell c3"><pre>[[&quot;Hello&quot;, &quot;Goo
d day&quot;, &quot;Hi&quot;], 
[&quot;Goodbye&quot;, &quot;By
e&quot;, &quot;Fairwell&quot;]
]</pre></td>
  <td class="cell c4"><pre>0.8</pre></td>
  <td class="cell c5">0 <> 1</td>
  <td class="cell c6">ATLevenshtein_STACKERROR_SAns.<pre>ATLevenshtein("goodday", [["Hello","Good day","Hi"],["Goodbye","Bye","Fairwell"]], 0.8);</pre></td>
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
  <td class="cell c0">Levenshtein</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>&quot;goodday&quot;</pre></td>
  <td class="cell c3"><pre>[[&quot;Hello&quot;, &quot;Goo
d day&quot;, &quot;Hi&quot;], 
[&quot;Goodbye&quot;, &quot;By
e&quot;, &quot;Fairwell&quot;]
]</pre></td>
  <td class="cell c4"><pre>[0.8, CASE]</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATLevenshtein_STACKERROR_SAns.<pre>ATLevenshtein("goodday", [["Hello","Good day","Hi"],["Goodbye","Bye","Fairwell"]], [0.8,CASE]);</pre></td>
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
  <td class="cell c0">Levenshtein</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>&quot;Jello&quot;</pre></td>
  <td class="cell c3"><pre>[[&quot;Hello&quot;, &quot;Goo
d day&quot;, &quot;Hi&quot;], 
[&quot;Goodbye&quot;, &quot;By
e&quot;, &quot;Fairwell&quot;]
]</pre></td>
  <td class="cell c4"><pre>0.9</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATLevenshtein_STACKERROR_SAns.<pre>ATLevenshtein("Jello", [["Hello","Good day","Hi"],["Goodbye","Bye","Fairwell"]], 0.9);</pre></td>
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
  <td class="cell c0">Levenshtein</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>&quot;Jello&quot;</pre></td>
  <td class="cell c3"><pre>[[&quot;Hello&quot;, &quot;Goo
d day&quot;, &quot;Hi&quot;], 
[&quot;Goodbye&quot;, &quot;By
e&quot;, &quot;Fairwell&quot;]
]</pre></td>
  <td class="cell c4"><pre>0.75</pre></td>
  <td class="cell c5">0 <> 1</td>
  <td class="cell c6">ATLevenshtein_STACKERROR_SAns.<pre>ATLevenshtein("Jello", [["Hello","Good day","Hi"],["Goodbye","Bye","Fairwell"]], 0.75);</pre></td>
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
  <td class="cell c0">Levenshtein</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>&quot;Jello&quot;</pre></td>
  <td class="cell c3"><pre>[[&quot;Hello&quot;, &quot;Goo
d day&quot;, &quot;Hi&quot;], 
[]]</pre></td>
  <td class="cell c4"><pre>0.75</pre></td>
  <td class="cell c5">0 <> 1</td>
  <td class="cell c6">ATLevenshtein_STACKERROR_SAns.<pre>ATLevenshtein("Jello", [["Hello","Good day","Hi"],[]], 0.75);</pre></td>
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
  <td class="cell c0">Levenshtein</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>&quot;Good bye&quot;</pre></td>
  <td class="cell c3"><pre>[[&quot;Hello&quot;, &quot;Goo
d day&quot;, &quot;Hi&quot;], 
[&quot;Goodbye&quot;, &quot;By
e&quot;, &quot;Fairwell&quot;]
]</pre></td>
  <td class="cell c4"><pre>0.75</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATLevenshtein_STACKERROR_SAns.<pre>ATLevenshtein("Good bye", [["Hello","Good day","Hi"],["Goodbye","Bye","Fairwell"]], 0.75);</pre></td>
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
  <td class="cell c0">Levenshtein</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>&quot;Good, day!&quot;</pre></td>
  <td class="cell c3"><pre>[[&quot;Hello&quot;, &quot;Goo
d day&quot;, &quot;Hi&quot;], 
[&quot;Goodbye&quot;, &quot;By
e&quot;, &quot;Fairwell&quot;]
]</pre></td>
  <td class="cell c4"><pre>0.75</pre></td>
  <td class="cell c5">0 <> 1</td>
  <td class="cell c6">ATLevenshtein_STACKERROR_SAns.<pre>ATLevenshtein("Good, day!", [["Hello","Good day","Hi"],["Goodbye","Bye","Fairwell"]], 0.75);</pre></td>
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
  <td class="cell c0">Levenshtein</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>sremove_chars(&quot;.,!?&quot;
, &quot;Good, day!&quot;)</pre></td>
  <td class="cell c3"><pre>[[&quot;Hello&quot;, &quot;Goo
d day&quot;, &quot;Hi&quot;], 
[&quot;Goodbye&quot;, &quot;By
e&quot;, &quot;Fairwell&quot;]
]</pre></td>
  <td class="cell c4"><pre>0.75</pre></td>
  <td class="cell c5">0 <> 1</td>
  <td class="cell c6">ATLevenshtein_STACKERROR_SAns.<pre>ATLevenshtein(sremove_chars(".,!?","Good, day!"), [["Hello","Good day","Hi"],["Goodbye","Bye","Fairwell"]], 0.75);</pre></td>
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
  <td class="cell c0">Levenshtein</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>&quot;   good     day  &quot;</pre></td>
  <td class="cell c3"><pre>[[&quot;Hello&quot;, &quot;Goo
d day&quot;, &quot;Hi&quot;], 
[&quot;Goodbye&quot;, &quot;By
e&quot;, &quot;Fairwell&quot;]
]</pre></td>
  <td class="cell c4"><pre>0.75</pre></td>
  <td class="cell c5">0 <> 1</td>
  <td class="cell c6">ATLevenshtein_STACKERROR_SAns.<pre>ATLevenshtein("   good     day  ", [["Hello","Good day","Hi"],["Goodbye","Bye","Fairwell"]], 0.75);</pre></td>
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
  <td class="cell c0">Levenshtein</td>
  <td class="cell c1"><span style="color:red;"><i class="fa fa-times"></i></span></td>
  <td class="cell c2"><pre>&quot;   good     day  &quot;</pre></td>
  <td class="cell c3"><pre>[[&quot;Hello&quot;, &quot;Goo
d day&quot;, &quot;Hi&quot;], 
[&quot;Goodbye&quot;, &quot;By
e&quot;, &quot;Fairwell&quot;]
]</pre></td>
  <td class="cell c4"><pre>[0.75, WHITESPA
CE]</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATLevenshtein_STACKERROR_SAns.<pre>ATLevenshtein("   good     day  ", [["Hello","Good day","Hi"],["Goodbye","Bye","Fairwell"]], [0.75,WHITESPACE]);</pre></td>
</tr>
<tr class="fail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">TEST_FAILED</td></td>
</tr>
<tr class="fail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The answer test failed to execute correctly: please alert your teacher. <i class="icon fa fa-exclamation-circle text-danger fa-fw " title="The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. " aria-label="The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. "></i>The version of the STACK-Maxima libraries being used (2025012200) does not match what is expected (2025012100) by this version of the STACK question type. </td></td>
</tr></tbody></table></div>
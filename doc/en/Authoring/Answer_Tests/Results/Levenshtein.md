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
  <td class="cell c1"><td colspan="4">The answer test failed to execute correctly: please alert your teacher. Division by zero.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Levenshtein</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>x^2</pre></td>
  <td class="cell c3"><pre>&quot;Hello&quot;</pre></td>
  <td class="cell c4"><pre>0.9</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATLevenshtein_SA_not_string.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The first argument to the Levenshtein answer test must be a string. The test failed. Please contact your teacher.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Levenshtein</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>&quot;Hello&quot;</pre></td>
  <td class="cell c3"><pre>&quot;Hello&quot;</pre></td>
  <td class="cell c4"><pre>0.9</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATLevenshtein_SB_malformed.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The second argument to the Levenshtein answer test must be in the form [allow, deny] where each item is a list of strings. This argument is malformed and so the test failed. Please contact your teacher.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Levenshtein</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>&quot;Hello&quot;</pre></td>
  <td class="cell c3"><pre>[&quot;Hello&quot;]</pre></td>
  <td class="cell c4"><pre>0.9</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATLevenshtein_SB_malformed.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The second argument to the Levenshtein answer test must be in the form [allow, deny] where each item is a list of strings. This argument is malformed and so the test failed. Please contact your teacher.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Levenshtein</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>&quot;Hello&quot;</pre></td>
  <td class="cell c3"><pre>[[&quot;Hello&quot;]]</pre></td>
  <td class="cell c4"><pre>0.9</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATLevenshtein_SB_malformed.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The second argument to the Levenshtein answer test must be in the form [allow, deny] where each item is a list of strings. This argument is malformed and so the test failed. Please contact your teacher.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Levenshtein</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>&quot;Hello&quot;</pre></td>
  <td class="cell c3"><pre>[[&quot;Hello&quot;], x^2]</pre></td>
  <td class="cell c4"><pre>0.9</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATLevenshtein_SB_malformed.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The second argument to the Levenshtein answer test must be in the form [allow, deny] where each item is a list of strings. This argument is malformed and so the test failed. Please contact your teacher.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Levenshtein</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>&quot;Hello&quot;</pre></td>
  <td class="cell c3"><pre>[[&quot;Hello&quot;], [x^2]]</pre></td>
  <td class="cell c4"><pre>0.9</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATLevenshtein_SB_malformed.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The second argument to the Levenshtein answer test must be in the form [allow, deny] where each item is a list of strings. This argument is malformed and so the test failed. Please contact your teacher.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Levenshtein</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>&quot;Hello&quot;</pre></td>
  <td class="cell c3"><pre>[[&quot;Hello&quot;], [&quot;G
oodbye&quot;], [&quot;Excess&q
uot;]]</pre></td>
  <td class="cell c4"><pre>0.9</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATLevenshtein_SB_malformed.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The second argument to the Levenshtein answer test must be in the form [allow, deny] where each item is a list of strings. This argument is malformed and so the test failed. Please contact your teacher.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Levenshtein</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>&quot;Hello&quot;</pre></td>
  <td class="cell c3"><pre>[[], [&quot;Goodbye&quot;]]</pre></td>
  <td class="cell c4"><pre>0.9</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATLevenshtein_SB_malformed.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The second argument to the Levenshtein answer test must be in the form [allow, deny] where each item is a list of strings. This argument is malformed and so the test failed. Please contact your teacher.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Levenshtein</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>&quot;Hello&quot;</pre></td>
  <td class="cell c3"><pre>[[&quot;Hello&quot;], [&quot;G
oodbye&quot;]]</pre></td>
  <td class="cell c4"><pre>z</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATLevenshtein_tol_not_number.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The tolerance in the Levenshtein answer test must be a number, but is not. The test failed. Please contact your teacher.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Levenshtein</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>&quot;Hello&quot;</pre></td>
  <td class="cell c3"><pre>[[&quot;Hello&quot;], [&quot;G
oodbye&quot;]]</pre></td>
  <td class="cell c4"><pre>[z]</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATLevenshtein_tol_not_number.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The tolerance in the Levenshtein answer test must be a number, but is not. The test failed. Please contact your teacher.</td></td>
</tr>
<tr class="notes">
  <td class="cell c0"><td colspan="6">Usage tests</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Levenshtein</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>&quot;Hello&quot;</pre></td>
  <td class="cell c3"><pre>[[&quot;Hello&quot;], [&quot;G
oodbye&quot;]]</pre></td>
  <td class="cell c4"><pre>0.9</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATLevenshtein_true: [[1.0,"Hello"],[0.0,"Goodbye"]].</td>
</tr>
<tr class="pass">
  <td class="cell c0">Levenshtein</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>&quot;hello&quot;</pre></td>
  <td class="cell c3"><pre>[[&quot;Hello&quot;], [&quot;G
oodbye&quot;]]</pre></td>
  <td class="cell c4"><pre>[0.9]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATLevenshtein_true: [[1.0,"Hello"],[0.0,"Goodbye"]].</td>
</tr>
<tr class="pass">
  <td class="cell c0">Levenshtein</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>&quot;hello&quot;</pre></td>
  <td class="cell c3"><pre>[[&quot;Hello&quot;, &quot;Goo
d day&quot;, &quot;Hi&quot;], 
[&quot;Goodbye&quot;, &quot;By
e&quot;, &quot;Fairwell&quot;]
]</pre></td>
  <td class="cell c4"><pre>[0.8, CASE]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATLevenshtein_match: [[0.8,"Hello"],[0.25,"Fairwell"]].</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The closest match was "<span class="filter_mathjaxloader_equation"><span class="nolink">\(\mbox{Hello}\)</span></span>".</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Levenshtein</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>&quot;goodday&quot;</pre></td>
  <td class="cell c3"><pre>[[&quot;Hello&quot;, &quot;Goo
d day&quot;, &quot;Hi&quot;], 
[&quot;Goodbye&quot;, &quot;By
e&quot;, &quot;Fairwell&quot;]
]</pre></td>
  <td class="cell c4"><pre>0.8</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATLevenshtein_match: [[0.875,"Good day"],[0.57143,"Goodbye"]].</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The closest match was "<span class="filter_mathjaxloader_equation"><span class="nolink">\(\mbox{Good day}\)</span></span>".</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Levenshtein</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>&quot;goodday&quot;</pre></td>
  <td class="cell c3"><pre>[[&quot;Hello&quot;, &quot;Goo
d day&quot;, &quot;Hi&quot;], 
[&quot;Goodbye&quot;, &quot;By
e&quot;, &quot;Fairwell&quot;]
]</pre></td>
  <td class="cell c4"><pre>[0.8, CASE]</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATLevenshtein_far: [[0.75,"Good day"],[0.42857,"Goodbye"]].</td>
</tr>
<tr class="pass">
  <td class="cell c0">Levenshtein</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>&quot;Jello&quot;</pre></td>
  <td class="cell c3"><pre>[[&quot;Hello&quot;, &quot;Goo
d day&quot;, &quot;Hi&quot;], 
[&quot;Goodbye&quot;, &quot;By
e&quot;, &quot;Fairwell&quot;]
]</pre></td>
  <td class="cell c4"><pre>0.9</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATLevenshtein_far: [[0.8,"Hello"],[0.25,"Fairwell"]].</td>
</tr>
<tr class="pass">
  <td class="cell c0">Levenshtein</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>&quot;Jello&quot;</pre></td>
  <td class="cell c3"><pre>[[&quot;Hello&quot;, &quot;Goo
d day&quot;, &quot;Hi&quot;], 
[&quot;Goodbye&quot;, &quot;By
e&quot;, &quot;Fairwell&quot;]
]</pre></td>
  <td class="cell c4"><pre>0.75</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATLevenshtein_match: [[0.8,"Hello"],[0.25,"Fairwell"]].</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The closest match was "<span class="filter_mathjaxloader_equation"><span class="nolink">\(\mbox{Hello}\)</span></span>".</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Levenshtein</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>&quot;Jello&quot;</pre></td>
  <td class="cell c3"><pre>[[&quot;Hello&quot;, &quot;Goo
d day&quot;, &quot;Hi&quot;], 
[]]</pre></td>
  <td class="cell c4"><pre>0.75</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATLevenshtein_match: [[0.8,"Hello"],[0,[]]].</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The closest match was "<span class="filter_mathjaxloader_equation"><span class="nolink">\(\mbox{Hello}\)</span></span>".</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Levenshtein</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>&quot;Good bye&quot;</pre></td>
  <td class="cell c3"><pre>[[&quot;Hello&quot;, &quot;Goo
d day&quot;, &quot;Hi&quot;], 
[&quot;Goodbye&quot;, &quot;By
e&quot;, &quot;Fairwell&quot;]
]</pre></td>
  <td class="cell c4"><pre>0.75</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATLevenshtein_deny: [[0.625,"Good day"],[0.875,"Goodbye"]].</td>
</tr>
<tr class="pass">
  <td class="cell c0">Levenshtein</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>&quot;Good, day!&quot;</pre></td>
  <td class="cell c3"><pre>[[&quot;Hello&quot;, &quot;Goo
d day&quot;, &quot;Hi&quot;], 
[&quot;Goodbye&quot;, &quot;By
e&quot;, &quot;Fairwell&quot;]
]</pre></td>
  <td class="cell c4"><pre>0.75</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATLevenshtein_match: [[0.8,"Good day"],[0.5,"Goodbye"]].</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The closest match was "<span class="filter_mathjaxloader_equation"><span class="nolink">\(\mbox{Good day}\)</span></span>".</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Levenshtein</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>sremove_chars(&quot;.,!?&quot;
, &quot;Good, day!&quot;)</pre></td>
  <td class="cell c3"><pre>[[&quot;Hello&quot;, &quot;Goo
d day&quot;, &quot;Hi&quot;], 
[&quot;Goodbye&quot;, &quot;By
e&quot;, &quot;Fairwell&quot;]
]</pre></td>
  <td class="cell c4"><pre>0.75</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATLevenshtein_true: [[1.0,"Good day"],[0.5,"Goodbye"]].</td>
</tr>
<tr class="pass">
  <td class="cell c0">Levenshtein</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>&quot;   good     day  &quot;</pre></td>
  <td class="cell c3"><pre>[[&quot;Hello&quot;, &quot;Goo
d day&quot;, &quot;Hi&quot;], 
[&quot;Goodbye&quot;, &quot;By
e&quot;, &quot;Fairwell&quot;]
]</pre></td>
  <td class="cell c4"><pre>0.75</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATLevenshtein_true: [[1.0,"Good day"],[0.5,"Goodbye"]].</td>
</tr>
<tr class="pass">
  <td class="cell c0">Levenshtein</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>&quot;   good     day  &quot;</pre></td>
  <td class="cell c3"><pre>[[&quot;Hello&quot;, &quot;Goo
d day&quot;, &quot;Hi&quot;], 
[&quot;Goodbye&quot;, &quot;By
e&quot;, &quot;Fairwell&quot;]
]</pre></td>
  <td class="cell c4"><pre>[0.75, WHITESPA
CE]</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATLevenshtein_far: [[0.47059,"Good day"],[0.29412,"Goodbye"]].</td>
</tr></tbody></table></div>
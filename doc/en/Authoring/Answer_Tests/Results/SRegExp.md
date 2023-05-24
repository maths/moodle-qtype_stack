# SRegExp: Answer test results

This page exposes the results of running answer tests on STACK test cases.  This page is automatically generated from the STACK unit tests and is designed to show question authors what answer tests actually do.  This includes cases where answer tests currentl fail, which gives a negative expected mark.  Comments and further test cases are very welcome.



<h2>SRegExp</h2><div class="no-overflow"><table class="flexible table table-striped table-hover generaltable generalbox stacktestsuite"><thead><tr><th class="header c0" scope="col">Test<div class="commands"></div></th><th class="header c1" scope="col">?<div class="commands"></div></th><th class="header c2" scope="col">Student response<div class="commands"></div></th><th class="header c3" scope="col">Teacher answer<div class="commands"></div></th><th class="header c4" scope="col">Opt<div class="commands"></div></th><th class="header c5" scope="col">Mark<div class="commands"></div></th><th class="header c6" scope="col">Answer note<div class="commands"></div></th>
</tr></thead><tbody>
<tr class="expectedfail">
  <td class="cell c0">SRegExp</td>
  <td class="cell c1"><span style="color:orange;"><i class="fa fa-adjust"></i></span></td>
  <td class="cell c2"><pre>1/0</pre></td>
  <td class="cell c3"><pre>&quot;hello&quot;</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">-1</td>
  <td class="cell c6">ATSRegExp_STACKERROR_SAns.</td>
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
  <td class="cell c0">SRegExp</td>
  <td class="cell c1"><span style="color:orange;"><i class="fa fa-adjust"></i></span></td>
  <td class="cell c2"><pre>&quot;1/0&quot;</pre></td>
  <td class="cell c3"><pre>1/0</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">-1</td>
  <td class="cell c6">ATSRegExp_STACKERROR_TAns.</td>
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
  <td class="cell c0">SRegExp</td>
  <td class="cell c1"><span style="color:orange;"><i class="fa fa-adjust"></i></span></td>
  <td class="cell c2"><pre>Hello</pre></td>
  <td class="cell c3"><pre>hello</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">-1</td>
  <td class="cell c6">ATSRegExp_SB_not_string.</td>
</tr>
<tr class="expectedfail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The second argument to the SRegExp answer test must be a string. The test failed. Please contact your teacher.</td></td>
</tr>
<tr class="expectedfail">
  <td class="cell c0">SRegExp</td>
  <td class="cell c1"><span style="color:orange;"><i class="fa fa-adjust"></i></span></td>
  <td class="cell c2"><pre>Hello</pre></td>
  <td class="cell c3"><pre>&quot;hello&quot;</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">-1</td>
  <td class="cell c6">ATSRegExp_SA_not_string.</td>
</tr>
<tr class="expectedfail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The first argument to the SRegExp answer test must be a string. The test failed. Please contact your teacher.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">SRegExp</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>&quot;aaaaabbb&quot;</pre></td>
  <td class="cell c3"><pre>&quot;(aaa)*b&quot;</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATSRegExp: ["aaab","aaa"].</td>
</tr>
<tr class="pass">
  <td class="cell c0">SRegExp</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>&quot;aab&quot;</pre></td>
  <td class="cell c3"><pre>&quot;(aaa)*b&quot;</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATSRegExp: ["b",false].</td>
</tr>
<tr class="pass">
  <td class="cell c0">SRegExp</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>&quot;aaac&quot;</pre></td>
  <td class="cell c3"><pre>&quot;(aaa)*b&quot;</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6"></td>
</tr>
<tr class="notes">
  <td class="cell c0"><td colspan="6">Anchor pattern to the start and the end of the string</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">SRegExp</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>&quot;aab&quot;</pre></td>
  <td class="cell c3"><pre>&quot;^[aA]*b$&quot;</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATSRegExp: ["aab"].</td>
</tr>
<tr class="pass">
  <td class="cell c0">SRegExp</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>&quot;aab&quot;</pre></td>
  <td class="cell c3"><pre>&quot;^(aaa)*b$&quot;</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">SRegExp</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>&quot;aAb&quot;</pre></td>
  <td class="cell c3"><pre>&quot;^[aA]*b$&quot;</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATSRegExp: ["aAb"].</td>
</tr>
<tr class="pass">
  <td class="cell c0">SRegExp</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>&quot; aAb&quot;</pre></td>
  <td class="cell c3"><pre>&quot;^[aA]*b$&quot;</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6"></td>
</tr>
<tr class="notes">
  <td class="cell c0"><td colspan="6">Case insensitive</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">SRegExp</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>&quot;caAb&quot;</pre></td>
  <td class="cell c3"><pre>&quot;(?i:a*b)&quot;</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATSRegExp: ["aAb"].</td>
</tr>
<tr class="notes">
  <td class="cell c0"><td colspan="6">Options</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">SRegExp</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>&quot;Alice went to the market
&quot;</pre></td>
  <td class="cell c3"><pre>&quot;(Alice|Bob) went to the 
(bank|market)&quot;</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATSRegExp: ["Alice went to the market","Alice","market"].</td>
</tr>
<tr class="pass">
  <td class="cell c0">SRegExp</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>&quot;Malice went to the shop&
quot;</pre></td>
  <td class="cell c3"><pre>&quot;(Alice|Bob) went to the 
(bank|market)&quot;</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6"></td>
</tr>
<tr class="notes">
  <td class="cell c0"><td colspan="6">Whitespace, note test rendering issue, the test string has additional spaces and tabs as does the result</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">SRegExp</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>&quot;Alice   went  to      th
e market&quot;</pre></td>
  <td class="cell c3"><pre>&quot;(Alice|Bob)\\s+went\\s+t
o\\s+the\\s+(bank|market)&quot
;</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATSRegExp: ["Alice   went  to      the market","Alice","market"].</td>
</tr>
<tr class="pass">
  <td class="cell c0">SRegExp</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>&quot;Alice   went  to      th
emarket&quot;</pre></td>
  <td class="cell c3"><pre>&quot;(Alice|Bob)\\s+went\\s+t
o\\s+the\\s+(bank|market)&quot
;</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6"></td>
</tr>
<tr class="notes">
  <td class="cell c0"><td colspan="6">Escaping patterns, note the function that does it</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">SRegExp</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>&quot;x^2.2&quot;</pre></td>
  <td class="cell c3"><pre>&quot;x\\^2\\.2&quot;</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATSRegExp: ["x^2.2"].</td>
</tr>
<tr class="pass">
  <td class="cell c0">SRegExp</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>&quot;x^2+sin(x)&quot;</pre></td>
  <td class="cell c3"><pre>sconcat(string_to_regex(&quot;
sin(x)&quot;),&quot;$&quot;)</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATSRegExp: ["sin(x)"].</td>
</tr>
<tr class="pass">
  <td class="cell c0">SRegExp</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>&quot;sin(x)+x^2&quot;</pre></td>
  <td class="cell c3"><pre>sconcat(string_to_regex(&quot;
sin(x)&quot;),&quot;$&quot;)</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6"></td>
</tr></tbody></table></div>
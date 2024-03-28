# Expanded: Answer test results

This page exposes the results of running answer tests on STACK test cases.  This page is automatically generated from the STACK unit tests and is designed to show question authors what answer tests actually do.  This includes cases where answer tests currentl fail, which gives a negative expected mark.  Comments and further test cases are very welcome.



<h2>Expanded</h2><div class="no-overflow"><table class="flexible table table-striped table-hover generaltable generalbox stacktestsuite"><thead><tr><th class="header c0" scope="col">Test<div class="commands"></div></th><th class="header c1" scope="col">?<div class="commands"></div></th><th class="header c2" scope="col">Student response<div class="commands"></div></th><th class="header c3" scope="col">Teacher answer<div class="commands"></div></th><th class="header c4" scope="col">Opt<div class="commands"></div></th><th class="header c5" scope="col">Mark<div class="commands"></div></th><th class="header c6" scope="col">Answer note<div class="commands"></div></th>
</tr></thead><tbody>
<tr class="expectedfail">
  <td class="cell c0">Expanded</td>
  <td class="cell c1"><span style="color:orange;"><i class="fa fa-adjust"></i></span></td>
  <td class="cell c2"><pre>1/0</pre></td>
  <td class="cell c3"><pre>0</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">-1</td>
  <td class="cell c6">ATExpanded_STACKERROR_SAns.</td>
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
  <td class="cell c0">Expanded</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>x&gt;2</pre></td>
  <td class="cell c3"><pre>x^2-2*x+1</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATExpanded_SA_not_expression.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer should be an expression, not an equation, inequality, list, set or matrix.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Expanded</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>x^2-1</pre></td>
  <td class="cell c3"><pre>0</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATExpanded_TRUE.</td>
</tr>
<tr class="pass">
  <td class="cell c0">Expanded</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>2*(x-1)</pre></td>
  <td class="cell c3"><pre>0</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATExpanded_FALSE.</td>
</tr>
<tr class="pass">
  <td class="cell c0">Expanded</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>(x-1)*(x+1)</pre></td>
  <td class="cell c3"><pre>0</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATExpanded_FALSE.</td>
</tr>
<tr class="pass">
  <td class="cell c0">Expanded</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>(x-a)*(x-b)</pre></td>
  <td class="cell c3"><pre>0</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATExpanded_FALSE.</td>
</tr>
<tr class="pass">
  <td class="cell c0">Expanded</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>x^2-(a+b)*x+a*b</pre></td>
  <td class="cell c3"><pre>0</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATExpanded_FALSE.</td>
</tr>
<tr class="pass">
  <td class="cell c0">Expanded</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>x^2-a*x-b*x+a*b</pre></td>
  <td class="cell c3"><pre>0</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATExpanded_TRUE.</td>
</tr>
<tr class="pass">
  <td class="cell c0">Expanded</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>cos(2*x)</pre></td>
  <td class="cell c3"><pre>0</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATExpanded_TRUE.</td>
</tr>
<tr class="pass">
  <td class="cell c0">Expanded</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>p+1</pre></td>
  <td class="cell c3"><pre>0</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATExpanded_TRUE.</td>
</tr>
<tr class="pass">
  <td class="cell c0">Expanded</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>(p+1)*(p-1)</pre></td>
  <td class="cell c3"><pre>0</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATExpanded_FALSE.</td>
</tr>
<tr class="pass">
  <td class="cell c0">Expanded</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>3+2*sqrt(3)</pre></td>
  <td class="cell c3"><pre>0</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATExpanded_TRUE.</td>
</tr>
<tr class="pass">
  <td class="cell c0">Expanded</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>3+sqrt(12)</pre></td>
  <td class="cell c3"><pre>0</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATExpanded_TRUE.</td>
</tr>
<tr class="pass">
  <td class="cell c0">Expanded</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>(1+sqrt(5))*(1-sqrt(3))</pre></td>
  <td class="cell c3"><pre>0</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATExpanded_FALSE.</td>
</tr>
<tr class="notes">
  <td class="cell c0"><td colspan="6">This fails, but you are never going to ask students to do this anyway...</td></td>
</tr>
<tr class="expectedfail">
  <td class="cell c0">Expanded</td>
  <td class="cell c1"><span style="color:orange;"><i class="fa fa-adjust"></i>!</span></td>
  <td class="cell c2"><pre>(a-x)^6000</pre></td>
  <td class="cell c3"><pre>0</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">-2</td>
  <td class="cell c6">ATExpanded_TRUE.</td>
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
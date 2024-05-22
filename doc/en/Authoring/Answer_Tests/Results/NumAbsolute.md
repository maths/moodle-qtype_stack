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
  <td class="cell c1"><td colspan="4">The answer test failed to execute correctly: please alert your teacher. Division by zero.</td></td>
</tr>
<tr class="expectedfail">
  <td class="cell c0">NumAbsolute</td>
  <td class="cell c1"><span style="color:orange;"><i class="fa fa-adjust"></i></span></td>
  <td class="cell c2"><pre>0</pre></td>
  <td class="cell c3"><pre>1/0</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">-1</td>
  <td class="cell c6">ATNumAbsolute_STACKERROR_TAns.</td>
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
  <td class="cell c0">NumAbsolute</td>
  <td class="cell c1"><span style="color:orange;"><i class="fa fa-adjust"></i></span></td>
  <td class="cell c2"><pre>0</pre></td>
  <td class="cell c3"><pre>0</pre></td>
  <td class="cell c4"><pre>1/0</pre></td>
  <td class="cell c5">-1</td>
  <td class="cell c6">ATNumAbsolute_STACKERROR_Opt.</td>
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
<tr class="pass">
  <td class="cell c0">NumAbsolute</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>1</pre></td>
  <td class="cell c3"><pre>0</pre></td>
  <td class="cell c4"><pre>(x</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6"></td>
</tr>
<tr class="notes">
  <td class="cell c0"><td colspan="6">No option, so 5%</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumAbsolute</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>1.1</pre></td>
  <td class="cell c3"><pre>1</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumAbsolute</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>1.05</pre></td>
  <td class="cell c3"><pre>1</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="notes">
  <td class="cell c0"><td colspan="6">Options passed</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumAbsolute</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>1.05</pre></td>
  <td class="cell c3"><pre>1</pre></td>
  <td class="cell c4"><pre>0.1</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumAbsolute</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>1.05</pre></td>
  <td class="cell c3"><pre>3</pre></td>
  <td class="cell c4"><pre>0.1</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumAbsolute</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>3.14</pre></td>
  <td class="cell c3"><pre>pi</pre></td>
  <td class="cell c4"><pre>0.001</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumAbsolute</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>1.41e-2</pre></td>
  <td class="cell c3"><pre>1.41e-2</pre></td>
  <td class="cell c4"><pre>0.0001</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumAbsolute</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>0.0141</pre></td>
  <td class="cell c3"><pre>1.41e-2</pre></td>
  <td class="cell c4"><pre>0.0001</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumAbsolute</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>0.00141</pre></td>
  <td class="cell c3"><pre>0.00141</pre></td>
  <td class="cell c4"><pre>0.0001</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumAbsolute</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>0.00141</pre></td>
  <td class="cell c3"><pre>1.41*10^-3</pre></td>
  <td class="cell c4"><pre>0.0001</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumAbsolute</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>1.41*10^-3</pre></td>
  <td class="cell c3"><pre>1.41*10^-3</pre></td>
  <td class="cell c4"><pre>0.0001</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumAbsolute</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[3.141,1.414]</pre></td>
  <td class="cell c3"><pre>[pi,sqrt(2)]</pre></td>
  <td class="cell c4"><pre>0.01</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumAbsolute</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[3,1.414]</pre></td>
  <td class="cell c3"><pre>[pi,sqrt(2)]</pre></td>
  <td class="cell c4"><pre>0.01</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumerical_wrongentries SA/TA=[3.0].</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The entries underlined in red below are those that are incorrect. <span class="filter_mathjaxloader_equation"><span class="nolink">\[\left[ {\color{red}{\underline{3.0}}} , 1.414 \right] \]</span></span></td></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumAbsolute</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[3,1.414]</pre></td>
  <td class="cell c3"><pre>{pi,sqrt(2)}</pre></td>
  <td class="cell c4"><pre>0.01</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumerical_SA_not_set.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer should be a set, but is not. Note that the syntax to enter a set is to enclose the comma separated values with curly brackets.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumAbsolute</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>{1.414,3.1}</pre></td>
  <td class="cell c3"><pre>{significantfigures(pi,6),sqrt
(2)}</pre></td>
  <td class="cell c4"><pre>0.01</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumerical_wrongentries: TA/SA=[3.14159], SA/TA=[3.1].</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The entries underlined in red below are those that are incorrect. <span class="filter_mathjaxloader_equation"><span class="nolink">\[\left \{{\color{red}{\underline{3.1}}} \right \}\]</span></span></td></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumAbsolute</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>{1,1.414,3.1,2}</pre></td>
  <td class="cell c3"><pre>{1,2,pi,sqrt(2)}</pre></td>
  <td class="cell c4"><pre>0.1</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr></tbody></table></div>
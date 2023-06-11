# PartFrac: Answer test results

This page exposes the results of running answer tests on STACK test cases.  This page is automatically generated from the STACK unit tests and is designed to show question authors what answer tests actually do.  This includes cases where answer tests currentl fail, which gives a negative expected mark.  Comments and further test cases are very welcome.



<h2>PartFrac</h2><div class="no-overflow"><table class="flexible table table-striped table-hover generaltable generalbox stacktestsuite"><thead><tr><th class="header c0" scope="col">Test<div class="commands"></div></th><th class="header c1" scope="col">?<div class="commands"></div></th><th class="header c2" scope="col">Student response<div class="commands"></div></th><th class="header c3" scope="col">Teacher answer<div class="commands"></div></th><th class="header c4" scope="col">Opt<div class="commands"></div></th><th class="header c5" scope="col">Mark<div class="commands"></div></th><th class="header c6" scope="col">Answer note<div class="commands"></div></th>
</tr></thead><tbody>
<tr class="expectedfail">
  <td class="cell c0">PartFrac</td>
  <td class="cell c1"><span style="color:orange;"><i class="fa fa-adjust"></i></span></td>
  <td class="cell c2"><pre>1/0</pre></td>
  <td class="cell c3"><pre>3*x^2</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">-1</td>
  <td class="cell c6">STACKERROR_OPTION.</td>
</tr>
<tr class="expectedfail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">TEST_FAILED</td></td>
</tr>
<tr class="expectedfail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The answer test failed to execute correctly: please alert your teacher. Missing option when executing the test. </td></td>
</tr>
<tr class="expectedfail">
  <td class="cell c0">PartFrac</td>
  <td class="cell c1"><span style="color:orange;"><i class="fa fa-adjust"></i></span></td>
  <td class="cell c2"><pre>1/0</pre></td>
  <td class="cell c3"><pre>3*x^2</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">-1</td>
  <td class="cell c6">ATPartFrac_STACKERROR_SAns.</td>
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
  <td class="cell c0">PartFrac</td>
  <td class="cell c1"><span style="color:orange;"><i class="fa fa-adjust"></i></span></td>
  <td class="cell c2"><pre>0</pre></td>
  <td class="cell c3"><pre>0</pre></td>
  <td class="cell c4"><pre>1/0</pre></td>
  <td class="cell c5">-1</td>
  <td class="cell c6">ATPartFrac_STACKERROR_Opt.</td>
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
  <td class="cell c0">PartFrac</td>
  <td class="cell c1"><span style="color:orange;"><i class="fa fa-adjust"></i></span></td>
  <td class="cell c2"><pre>0</pre></td>
  <td class="cell c3"><pre>1/0</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">-1</td>
  <td class="cell c6">ATPartFrac_STACKERROR_TAns.</td>
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
  <td class="cell c0">PartFrac</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>1/n=0</pre></td>
  <td class="cell c3"><pre>1/n</pre></td>
  <td class="cell c4"><pre>n</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATPartFrac_SA_not_expression.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer should be an expression, not an equation, inequality, list, set or matrix.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">PartFrac</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>1/n</pre></td>
  <td class="cell c3"><pre>{1/n}</pre></td>
  <td class="cell c4"><pre>n</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATPartFrac_TA_not_expression.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The answer test failed. Please contact your systems administrator</td></td>
</tr>
<tr class="notes">
  <td class="cell c0"><td colspan="6">Basic tests</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">PartFrac</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>1/m</pre></td>
  <td class="cell c3"><pre>1/n</pre></td>
  <td class="cell c4"><pre>n</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATPartFrac_diff_variables.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The variables in your answer are different to those of the question, please check them.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">PartFrac</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>2/(x+1)-1/(x+2)</pre></td>
  <td class="cell c3"><pre>s/((s+1)*(s+2))</pre></td>
  <td class="cell c4"><pre>s</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATPartFrac_diff_variables.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The variables in your answer are different to those of the question, please check them.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">PartFrac</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>1/n</pre></td>
  <td class="cell c3"><pre>1/n</pre></td>
  <td class="cell c4"><pre>n</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATPartFrac_true.</td>
</tr>
<tr class="pass">
  <td class="cell c0">PartFrac</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>n^3/(n-1)</pre></td>
  <td class="cell c3"><pre>n^3/(n-1)</pre></td>
  <td class="cell c4"><pre>n</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATPartFrac_false_factor.</td>
</tr>
<tr class="pass">
  <td class="cell c0">PartFrac</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>1+n+n^2+1/(n-1)</pre></td>
  <td class="cell c3"><pre>n^3/(n-1)</pre></td>
  <td class="cell c4"><pre>n</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATPartFrac_true.</td>
</tr>
<tr class="pass">
  <td class="cell c0">PartFrac</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>1+n+n^2-1/(1-n)</pre></td>
  <td class="cell c3"><pre>n^3/(n-1)</pre></td>
  <td class="cell c4"><pre>n</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATPartFrac_true.</td>
</tr>
<tr class="notes">
  <td class="cell c0"><td colspan="6">Distinct linear factors in denominator</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">PartFrac</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>1/(n+1)-1/n</pre></td>
  <td class="cell c3"><pre>1/(n+1)-1/n</pre></td>
  <td class="cell c4"><pre>n</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATPartFrac_true.</td>
</tr>
<tr class="pass">
  <td class="cell c0">PartFrac</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>1/(n+1)+1/(1-n)</pre></td>
  <td class="cell c3"><pre>1/(n+1)-1/(n-1)</pre></td>
  <td class="cell c4"><pre>n</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATPartFrac_true.</td>
</tr>
<tr class="pass">
  <td class="cell c0">PartFrac</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>1/(2*(n-1))-1/(2*(n+1))</pre></td>
  <td class="cell c3"><pre>1/((n-1)*(n+1))</pre></td>
  <td class="cell c4"><pre>n</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATPartFrac_true.</td>
</tr>
<tr class="pass">
  <td class="cell c0">PartFrac</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>1/(2*(n+1))-1/(2*(n-1))</pre></td>
  <td class="cell c3"><pre>1/((n-1)*(n+1))</pre></td>
  <td class="cell c4"><pre>n</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATPartFrac_ret_expression.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer as a single fraction is <span class="filter_mathjaxloader_equation"><span class="nolink">\(-\frac{1}{\left(n-1\right)\cdot \left(n+1\right)}\)</span></span></td></td>
</tr>
<tr class="pass">
  <td class="cell c0">PartFrac</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>-9/(x-2) + -9/(x+1)</pre></td>
  <td class="cell c3"><pre>-9/(x-2) + -9/(x+1)</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATPartFrac_true.</td>
</tr>
<tr class="notes">
  <td class="cell c0"><td colspan="6">Addition and Subtraction errors</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">PartFrac</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>1/(x+1) + 1/(x+2)</pre></td>
  <td class="cell c3"><pre>2/(x+1) + 1/(x+2)</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATPartFrac_ret_expression.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer as a single fraction is <span class="filter_mathjaxloader_equation"><span class="nolink">\(\frac{2\cdot x+3}{\left(x+1\right)\cdot \left(x+2\right)}\)</span></span></td></td>
</tr>
<tr class="pass">
  <td class="cell c0">PartFrac</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>1/(x+1) + 1/(x+2)</pre></td>
  <td class="cell c3"><pre>1/(x+1) + 2/(x+2)</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATPartFrac_ret_expression.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer as a single fraction is <span class="filter_mathjaxloader_equation"><span class="nolink">\(\frac{2\cdot x+3}{\left(x+1\right)\cdot \left(x+2\right)}\)</span></span></td></td>
</tr>
<tr class="notes">
  <td class="cell c0"><td colspan="6">Denominator Error</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">PartFrac</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>1/(x+1) + 1/(x+2)</pre></td>
  <td class="cell c3"><pre>1/(x+3) + 1/(x+2)</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATPartFrac_ret_expression.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer as a single fraction is <span class="filter_mathjaxloader_equation"><span class="nolink">\(\frac{2\cdot x+3}{\left(x+1\right)\cdot \left(x+2\right)}\)</span></span></td></td>
</tr>
<tr class="notes">
  <td class="cell c0"><td colspan="6">Repeated linear factors in denominator</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">PartFrac</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>(9*y-8)/(y-4)^2</pre></td>
  <td class="cell c3"><pre>(9*y-8)/(y-4)^2</pre></td>
  <td class="cell c4"><pre>y</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATPartFrac_false_factor.</td>
</tr>
<tr class="pass">
  <td class="cell c0">PartFrac</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>9/(y-4)+28/(y-4)^2</pre></td>
  <td class="cell c3"><pre>(9*y-8)/(y-4)^2</pre></td>
  <td class="cell c4"><pre>y</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATPartFrac_true.</td>
</tr>
<tr class="pass">
  <td class="cell c0">PartFrac</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>(-5/(x+3))+(16/(x+3)^2)-(2/(x+
2))+4</pre></td>
  <td class="cell c3"><pre>(-5/(x+3))+(16/(x+3)^2)-(2/(x+
2))+4</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATPartFrac_true.</td>
</tr>
<tr class="pass">
  <td class="cell c0">PartFrac</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>(3*x^2-5)/((x-4)^2*x)</pre></td>
  <td class="cell c3"><pre>(3*x^2-5)/((x-4)^2*x)</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATPartFrac_false_factor.</td>
</tr>
<tr class="pass">
  <td class="cell c0">PartFrac</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>-4/(16*x)+53/(16*(x-4))+43/(4*
(x-4)^2)</pre></td>
  <td class="cell c3"><pre>(3*x^2-5)/((x-4)^2*x)</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATPartFrac_ret_expression.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer as a single fraction is <span class="filter_mathjaxloader_equation"><span class="nolink">\(\frac{49\cdot x^2-8\cdot x-64}{16\cdot {\left(x-4\right)}^2\cdot x}\)</span></span></td></td>
</tr>
<tr class="pass">
  <td class="cell c0">PartFrac</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>-5/(16*x)+53/(16*(x-4))+43/(4*
(x-4)^2)</pre></td>
  <td class="cell c3"><pre>(3*x^2-5)/((x-4)^2*x)</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATPartFrac_true.</td>
</tr>
<tr class="pass">
  <td class="cell c0">PartFrac</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>(5*x+6)/((x+1)*(x+5)^2)</pre></td>
  <td class="cell c3"><pre>(5*x+6)/((x+1)*(x+5)^2)</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATPartFrac_false_factor.</td>
</tr>
<tr class="pass">
  <td class="cell c0">PartFrac</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>-1/(16*(x+5))+19/(4*(x+5)^2)+1
/(16*(x+1))</pre></td>
  <td class="cell c3"><pre>(5*x+6)/((x+1)*(x+5)^2)</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATPartFrac_true.</td>
</tr>
<tr class="pass">
  <td class="cell c0">PartFrac</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>5/(x*(x+3)*(5*x-2))</pre></td>
  <td class="cell c3"><pre>5/(x*(x+3)*(5*x-2))</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATPartFrac_false_factor.</td>
</tr>
<tr class="pass">
  <td class="cell c0">PartFrac</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>125/(34*(5*x-2))+5/(51*(x+3))-
5/(6*x)</pre></td>
  <td class="cell c3"><pre>5/(x*(x+3)*(5*x-2))</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATPartFrac_true.</td>
</tr>
<tr class="pass">
  <td class="cell c0">PartFrac</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>-4/(16*x)+1/(2*(x-1))-1/(8*(x-
1)^2)</pre></td>
  <td class="cell c3"><pre>(3*x^2-5)/((4*x-4)^2*x)</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATPartFrac_ret_expression.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer as a single fraction is <span class="filter_mathjaxloader_equation"><span class="nolink">\(\frac{2\cdot x^2-x-2}{8\cdot {\left(x-1\right)}^2\cdot x}\)</span></span></td></td>
</tr>
<tr class="pass">
  <td class="cell c0">PartFrac</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>-5/(16*x)+1/(2*(x-1))-1/(8*(x-
1)^2)</pre></td>
  <td class="cell c3"><pre>(3*x^2-5)/((4*x-4)^2*x)</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATPartFrac_true.</td>
</tr>
<tr class="notes">
  <td class="cell c0"><td colspan="6">Irreducible quadratic in denominator</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">PartFrac</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>1/(x-1)-(x+1)/(x^2+1)</pre></td>
  <td class="cell c3"><pre>2/((x-1)*(x^2+1))</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATPartFrac_true.</td>
</tr>
<tr class="pass">
  <td class="cell c0">PartFrac</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>1/(2*x-2)-(x+1)/(2*(x^2+1))</pre></td>
  <td class="cell c3"><pre>1/((x-1)*(x^2+1))</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATPartFrac_true.</td>
</tr>
<tr class="pass">
  <td class="cell c0">PartFrac</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>1/(2*(x-1))+x/(2*(x^2+1))</pre></td>
  <td class="cell c3"><pre>1/((x-1)*(x^2+1))</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATPartFrac_ret_expression.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer as a single fraction is <span class="filter_mathjaxloader_equation"><span class="nolink">\(\frac{2\cdot x^2-x+1}{2\cdot \left(x-1\right)\cdot \left(x^2+1 \right)}\)</span></span></td></td>
</tr>
<tr class="pass">
  <td class="cell c0">PartFrac</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>(2*x+1)/(x^2+1)-2/(x-1)</pre></td>
  <td class="cell c3"><pre>(2*x+1)/(x^2+1)-2/(x-1)</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATPartFrac_true.</td>
</tr>
<tr class="notes">
  <td class="cell c0"><td colspan="6">2 answers to the same question</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">PartFrac</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>3/(x+1) + 3/(x+2)</pre></td>
  <td class="cell c3"><pre>3*(2*x+3)/((x+1)*(x+2))</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATPartFrac_true.</td>
</tr>
<tr class="pass">
  <td class="cell c0">PartFrac</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>3*(1/(x+1) + 1/(x+2))</pre></td>
  <td class="cell c3"><pre>3*(2*x+3)/((x+1)*(x+2))</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATPartFrac_true.</td>
</tr>
<tr class="notes">
  <td class="cell c0"><td colspan="6">Algebraically equivalent, but numerators of same order than denominator, i.e. not in partial fraction form.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">PartFrac</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>3*x*(1/(x+1) + 2/(x+2))</pre></td>
  <td class="cell c3"><pre>-12/(x+2)-3/(x+1)+9</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATPartFrac_false_factor.</td>
</tr>
<tr class="pass">
  <td class="cell c0">PartFrac</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>(3*x+3)*(1/(x+1) + 2/(x+2))</pre></td>
  <td class="cell c3"><pre>9-6/(x+2)</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATPartFrac_false_factor.</td>
</tr>
<tr class="pass">
  <td class="cell c0">PartFrac</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>n/(2*n-1)-(n+1)/(2*n+1)</pre></td>
  <td class="cell c3"><pre>1/(4*n-2)-1/(4*n+2)</pre></td>
  <td class="cell c4"><pre>n</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATPartFrac_false_factor.</td>
</tr>
<tr class="notes">
  <td class="cell c0"><td colspan="6">Correct Answer, Numerator > Denominator</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">PartFrac</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>10/(x+3) - 2/(x+2) + x -2</pre></td>
  <td class="cell c3"><pre>(x^3 + 3*x^2 + 4*x +2)/((x+2)*
(x+3))</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATPartFrac_true.</td>
</tr>
<tr class="pass">
  <td class="cell c0">PartFrac</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>2*x+1/(x+1)+1/(x-1)</pre></td>
  <td class="cell c3"><pre>2*x^3/(x^2-1)</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATPartFrac_true.</td>
</tr>
<tr class="notes">
  <td class="cell c0"><td colspan="6">Simple mistakes</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">PartFrac</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>1/(n*(n-1))</pre></td>
  <td class="cell c3"><pre>1/(n*(n-1))</pre></td>
  <td class="cell c4"><pre>n</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATPartFrac_false_factor.</td>
</tr>
<tr class="pass">
  <td class="cell c0">PartFrac</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>((1-x)^4*x^4)/(x^2+1)</pre></td>
  <td class="cell c3"><pre>((1-x)^4*x^4)/(x^2+1)</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATPartFrac_false_factor.</td>
</tr>
<tr class="pass">
  <td class="cell c0">PartFrac</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>1/(n-1)-1/n^2</pre></td>
  <td class="cell c3"><pre>1/((n+1)*n)</pre></td>
  <td class="cell c4"><pre>n</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATPartFrac_denom_ret.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">If your answer is written as a single fraction then the denominator would be <span class="filter_mathjaxloader_equation"><span class="nolink">\(\left(n-1\right)\cdot n^2\)</span></span>. In fact, it should be <span class="filter_mathjaxloader_equation"><span class="nolink">\(n\cdot \left(n+1\right)\)</span></span>.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">PartFrac</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>1/(n-1)-1/n</pre></td>
  <td class="cell c3"><pre>1/(n-1)+1/n</pre></td>
  <td class="cell c4"><pre>n</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATPartFrac_ret_expression.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer as a single fraction is <span class="filter_mathjaxloader_equation"><span class="nolink">\(\frac{1}{\left(n-1\right)\cdot n}\)</span></span></td></td>
</tr>
<tr class="pass">
  <td class="cell c0">PartFrac</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>1/(x+1)-1/x</pre></td>
  <td class="cell c3"><pre>1/(x-1)+1/x</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATPartFrac_ret_expression.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer as a single fraction is <span class="filter_mathjaxloader_equation"><span class="nolink">\(-\frac{1}{x\cdot \left(x+1\right)}\)</span></span></td></td>
</tr>
<tr class="pass">
  <td class="cell c0">PartFrac</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>1/(n*(n+1))+1/n</pre></td>
  <td class="cell c3"><pre>2/n-1/(n+1)</pre></td>
  <td class="cell c4"><pre>n</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATPartFrac_false_factor.</td>
</tr>
<tr class="notes">
  <td class="cell c0"><td colspan="6">Too many parts in the partial fraction</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">PartFrac</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>s/((s+1)^2) + s/(s+2) - 1/(s+1
)</pre></td>
  <td class="cell c3"><pre>s/((s+1)*(s+2))</pre></td>
  <td class="cell c4"><pre>s</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATPartFrac_denom_ret.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">If your answer is written as a single fraction then the denominator would be <span class="filter_mathjaxloader_equation"><span class="nolink">\({\left(s+1\right)}^2\cdot \left(s+2\right)\)</span></span>. In fact, it should be <span class="filter_mathjaxloader_equation"><span class="nolink">\(\left(s+1\right)\cdot \left(s+2\right)\)</span></span>.</td></td>
</tr>
<tr class="notes">
  <td class="cell c0"><td colspan="6">Too few parts in the partial fraction</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">PartFrac</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>s/(s+2) - 1/(s+1)</pre></td>
  <td class="cell c3"><pre>s/((s+1)*(s+2)*(s+3))</pre></td>
  <td class="cell c4"><pre>s</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATPartFrac_denom_ret.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">If your answer is written as a single fraction then the denominator would be <span class="filter_mathjaxloader_equation"><span class="nolink">\(\left(s+1\right)\cdot \left(s+2\right)\)</span></span>. In fact, it should be <span class="filter_mathjaxloader_equation"><span class="nolink">\(\left(s+1\right)\cdot \left(s+2\right)\cdot \left(s+3\right)\)</span></span>.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">PartFrac</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>(3*x^2-5)/((4*x-4)^2*x)</pre></td>
  <td class="cell c3"><pre>(3*x^2-5)/((4*x-4)^2*x)</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATPartFrac_false_factor.</td>
</tr></tbody></table></div>
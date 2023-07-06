# CasEqual: Answer test results

This page exposes the results of running answer tests on STACK test cases.  This page is automatically generated from the STACK unit tests and is designed to show question authors what answer tests actually do.  This includes cases where answer tests currentl fail, which gives a negative expected mark.  Comments and further test cases are very welcome.



<h2>CasEqual</h2><div class="no-overflow"><table class="flexible table table-striped table-hover generaltable generalbox stacktestsuite"><thead><tr><th class="header c0" scope="col">Test<div class="commands"></div></th><th class="header c1" scope="col">?<div class="commands"></div></th><th class="header c2" scope="col">Student response<div class="commands"></div></th><th class="header c3" scope="col">Teacher answer<div class="commands"></div></th><th class="header c4" scope="col">Opt<div class="commands"></div></th><th class="header c5" scope="col">Mark<div class="commands"></div></th><th class="header c6" scope="col">Answer note<div class="commands"></div></th>
</tr></thead><tbody>
<tr class="expectedfail">
  <td class="cell c0">CasEqual</td>
  <td class="cell c1"><span style="color:orange;"><i class="fa fa-adjust"></i></span></td>
  <td class="cell c2"><pre>1/0</pre></td>
  <td class="cell c3"><pre>x^2-2*x+1</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">-1</td>
  <td class="cell c6">ATCASEqual_STACKERROR_SAns.</td>
</tr>
<tr class="expectedfail">
  <td class="cell c0">CasEqual</td>
  <td class="cell c1"><span style="color:orange;"><i class="fa fa-adjust"></i></span></td>
  <td class="cell c2"><pre>x</pre></td>
  <td class="cell c3"><pre>1/0</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">-1</td>
  <td class="cell c6">ATCASEqual_STACKERROR_TAns.</td>
</tr>
<tr class="pass">
  <td class="cell c0">CasEqual</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>0.5</pre></td>
  <td class="cell c3"><pre>1/2</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATCASEqual (AlgEquiv-true).</td>
</tr>
<tr class="pass">
  <td class="cell c0">CasEqual</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>x=1</pre></td>
  <td class="cell c3"><pre>1</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATCASEqual ATAlgEquiv_TA_not_equation.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">You have entered an equation, but an equation is not expected here. You may have typed something like "y=2*x+1" when you only needed to type "2*x+1".</td></td>
</tr>
<tr class="notes">
  <td class="cell c0"><td colspan="6">Case sensitivity</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">CasEqual</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>a</pre></td>
  <td class="cell c3"><pre>A</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATCASEqual_false.</td>
</tr>
<tr class="pass">
  <td class="cell c0">CasEqual</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>exdowncase(X^2-2*X+1)</pre></td>
  <td class="cell c3"><pre>x^2-2*x+1</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATCASEqual_true.</td>
</tr>
<tr class="notes">
  <td class="cell c0"><td colspan="6">Numbers</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">CasEqual</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>4^(-1/2)</pre></td>
  <td class="cell c3"><pre>1/2</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATCASEqual (AlgEquiv-true).</td>
</tr>
<tr class="pass">
  <td class="cell c0">CasEqual</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>ev(4^(-1/2),simp)</pre></td>
  <td class="cell c3"><pre>ev(1/2,simp)</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATCASEqual_true.</td>
</tr>
<tr class="pass">
  <td class="cell c0">CasEqual</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>2^2</pre></td>
  <td class="cell c3"><pre>4</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATCASEqual (AlgEquiv-true).</td>
</tr>
<tr class="notes">
  <td class="cell c0"><td colspan="6">Powers</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">CasEqual</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>a^2/b^3</pre></td>
  <td class="cell c3"><pre>a^2*b^(-3)</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATCASEqual (AlgEquiv-true).</td>
</tr>
<tr class="notes">
  <td class="cell c0"><td colspan="6">Expressions with subscripts</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">CasEqual</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>rho*z*V/(4*pi*epsilon[0]*(R^2+
z^2)^(3/2))</pre></td>
  <td class="cell c3"><pre>rho*z*V/(4*pi*epsilon[0]*(R^2+
z^2)^(3/2))</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATCASEqual_true.</td>
</tr>
<tr class="pass">
  <td class="cell c0">CasEqual</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>rho*z*V/(4*pi*epsilon[1]*(R^2+
z^2)^(3/2))</pre></td>
  <td class="cell c3"><pre>rho*z*V/(4*pi*epsilon[0]*(R^2+
z^2)^(3/2))</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATCASEqual_false.</td>
</tr>
<tr class="notes">
  <td class="cell c0"><td colspan="6">Mix of floats and rational numbers</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">CasEqual</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>0.5</pre></td>
  <td class="cell c3"><pre>1/2</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATCASEqual (AlgEquiv-true).</td>
</tr>
<tr class="pass">
  <td class="cell c0">CasEqual</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>x^(1/2)</pre></td>
  <td class="cell c3"><pre>sqrt(x)</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATCASEqual (AlgEquiv-true).</td>
</tr>
<tr class="pass">
  <td class="cell c0">CasEqual</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>ev(x^(1/2),simp)</pre></td>
  <td class="cell c3"><pre>ev(sqrt(x),simp)</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATCASEqual_true.</td>
</tr>
<tr class="pass">
  <td class="cell c0">CasEqual</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>abs(x)</pre></td>
  <td class="cell c3"><pre>sqrt(x^2)</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATCASEqual (AlgEquiv-true).</td>
</tr>
<tr class="pass">
  <td class="cell c0">CasEqual</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>ev(abs(x),simp)</pre></td>
  <td class="cell c3"><pre>ev(sqrt(x^2),simp)</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATCASEqual_true.</td>
</tr>
<tr class="pass">
  <td class="cell c0">CasEqual</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>x-1</pre></td>
  <td class="cell c3"><pre>(x^2-1)/(x+1)</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATCASEqual (AlgEquiv-true).</td>
</tr>
<tr class="notes">
  <td class="cell c0"><td colspan="6">Polynomials and rational function</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">CasEqual</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>x+x</pre></td>
  <td class="cell c3"><pre>2*x</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATCASEqual (AlgEquiv-true).</td>
</tr>
<tr class="pass">
  <td class="cell c0">CasEqual</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>ev(x+x,simp)</pre></td>
  <td class="cell c3"><pre>ev(2*x,simp)</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATCASEqual_true.</td>
</tr>
<tr class="pass">
  <td class="cell c0">CasEqual</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>x+x^2</pre></td>
  <td class="cell c3"><pre>x^2+x</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATCASEqual (AlgEquiv-true).</td>
</tr>
<tr class="pass">
  <td class="cell c0">CasEqual</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>ev(x+x^2,simp)</pre></td>
  <td class="cell c3"><pre>ev(x^2+x,simp)</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATCASEqual_true.</td>
</tr>
<tr class="pass">
  <td class="cell c0">CasEqual</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>(x-1)^2</pre></td>
  <td class="cell c3"><pre>x^2-2*x+1</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATCASEqual (AlgEquiv-true).</td>
</tr>
<tr class="pass">
  <td class="cell c0">CasEqual</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>(x-1)^(-2)</pre></td>
  <td class="cell c3"><pre>1/(x^2-2*x+1)</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATCASEqual (AlgEquiv-true).</td>
</tr>
<tr class="pass">
  <td class="cell c0">CasEqual</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>1/n-1/(n+1)</pre></td>
  <td class="cell c3"><pre>1/(n*(n+1))</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATCASEqual (AlgEquiv-true).</td>
</tr>
<tr class="notes">
  <td class="cell c0"><td colspan="6">Trig functions</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">CasEqual</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>cos(x)</pre></td>
  <td class="cell c3"><pre>cos(-x)</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATCASEqual (AlgEquiv-true).</td>
</tr>
<tr class="pass">
  <td class="cell c0">CasEqual</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>ev(cos(x),simp)</pre></td>
  <td class="cell c3"><pre>ev(cos(-x),simp)</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATCASEqual_true.</td>
</tr>
<tr class="pass">
  <td class="cell c0">CasEqual</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>cos(x)^2+sin(x)^2</pre></td>
  <td class="cell c3"><pre>1</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATCASEqual (AlgEquiv-true).</td>
</tr>
<tr class="pass">
  <td class="cell c0">CasEqual</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>2*cos(x)^2-1</pre></td>
  <td class="cell c3"><pre>cos(2*x)</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATCASEqual (AlgEquiv-true).</td>
</tr>
<tr class="notes">
  <td class="cell c0"><td colspan="6">Predicate function wrapper</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">CasEqual</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>imag_numberp(2*%i)</pre></td>
  <td class="cell c3"><pre>true</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATCASEqual_true.</td>
</tr>
<tr class="pass">
  <td class="cell c0">CasEqual</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>imag_numberp(%e^(%i*%pi/2))</pre></td>
  <td class="cell c3"><pre>true</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATCASEqual_true.</td>
</tr>
<tr class="pass">
  <td class="cell c0">CasEqual</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>imag_numberp(2)</pre></td>
  <td class="cell c3"><pre>false</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATCASEqual_true.</td>
</tr>
<tr class="pass">
  <td class="cell c0">CasEqual</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>imag_numberp(%e^(%pi/2))</pre></td>
  <td class="cell c3"><pre>false</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATCASEqual_true.</td>
</tr>
<tr class="pass">
  <td class="cell c0">CasEqual</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>complex_exponentialp(3*%e^(%i*
%pi/6))</pre></td>
  <td class="cell c3"><pre>true</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATCASEqual_true.</td>
</tr>
<tr class="pass">
  <td class="cell c0">CasEqual</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>complex_exponentialp(3)</pre></td>
  <td class="cell c3"><pre>true</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATCASEqual_true.</td>
</tr>
<tr class="pass">
  <td class="cell c0">CasEqual</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>complex_exponentialp(%e^(%i*%p
i/6))</pre></td>
  <td class="cell c3"><pre>true</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATCASEqual_true.</td>
</tr>
<tr class="pass">
  <td class="cell c0">CasEqual</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>complex_exponentialp(%e^%i)</pre></td>
  <td class="cell c3"><pre>true</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATCASEqual_true.</td>
</tr>
<tr class="pass">
  <td class="cell c0">CasEqual</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>complex_exponentialp(%e^(%pi/6
))</pre></td>
  <td class="cell c3"><pre>true</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATCASEqual_true.</td>
</tr>
<tr class="pass">
  <td class="cell c0">CasEqual</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>complex_exponentialp(3+%i)</pre></td>
  <td class="cell c3"><pre>false</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATCASEqual_true.</td>
</tr>
<tr class="pass">
  <td class="cell c0">CasEqual</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>complex_exponentialp(%e^(%i)/4
)</pre></td>
  <td class="cell c3"><pre>true</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATCASEqual_true.</td>
</tr>
<tr class="pass">
  <td class="cell c0">CasEqual</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>complex_exponentialp(3*exp(%i*
%pi/6))</pre></td>
  <td class="cell c3"><pre>true</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATCASEqual_true.</td>
</tr>
<tr class="pass">
  <td class="cell c0">CasEqual</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>integerp(-1)</pre></td>
  <td class="cell c3"><pre>true</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATCASEqual_false.</td>
</tr>
<tr class="pass">
  <td class="cell c0">CasEqual</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>integerp(ev(-1,simp))</pre></td>
  <td class="cell c3"><pre>true</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATCASEqual_true.</td>
</tr></tbody></table></div>
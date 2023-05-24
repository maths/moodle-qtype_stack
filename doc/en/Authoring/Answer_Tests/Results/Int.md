# Int: Answer test results

This page exposes the results of running answer tests on STACK test cases.  This page is automatically generated from the STACK unit tests and is designed to show question authors what answer tests actually do.  This includes cases where answer tests currentl fail, which gives a negative expected mark.  Comments and further test cases are very welcome.



<h2>Int</h2><div class="no-overflow"><table class="flexible table table-striped table-hover generaltable generalbox stacktestsuite"><thead><tr><th class="header c0" scope="col">Test<div class="commands"></div></th><th class="header c1" scope="col">?<div class="commands"></div></th><th class="header c2" scope="col">Student response<div class="commands"></div></th><th class="header c3" scope="col">Teacher answer<div class="commands"></div></th><th class="header c4" scope="col">Opt<div class="commands"></div></th><th class="header c5" scope="col">Mark<div class="commands"></div></th><th class="header c6" scope="col">Answer note<div class="commands"></div></th>
</tr></thead><tbody>
<tr class="expectedfail">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:orange;"><i class="fa fa-adjust"></i></span></td>
  <td class="cell c2"><pre>1/0</pre></td>
  <td class="cell c3"><pre>1</pre></td>
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
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:orange;"><i class="fa fa-adjust"></i></span></td>
  <td class="cell c2"><pre>1/0</pre></td>
  <td class="cell c3"><pre>1</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">-1</td>
  <td class="cell c6">ATInt_STACKERROR_SAns.</td>
</tr>
<tr class="expectedfail">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:orange;"><i class="fa fa-adjust"></i></span></td>
  <td class="cell c2"><pre>1</pre></td>
  <td class="cell c3"><pre>1/0</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">-1</td>
  <td class="cell c6">ATInt_STACKERROR_TAns.</td>
</tr>
<tr class="expectedfail">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:orange;"><i class="fa fa-adjust"></i></span></td>
  <td class="cell c2"><pre>0</pre></td>
  <td class="cell c3"><pre>0</pre></td>
  <td class="cell c4"><pre>1/0</pre></td>
  <td class="cell c5">-1</td>
  <td class="cell c6">ATInt_STACKERROR_Opt.</td>
</tr>
<tr class="expectedfail">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:orange;"><i class="fa fa-adjust"></i></span></td>
  <td class="cell c2"><pre>0</pre></td>
  <td class="cell c3"><pre>0</pre></td>
  <td class="cell c4"><pre>[x,1/0]</pre></td>
  <td class="cell c5">-1</td>
  <td class="cell c6">ATInt_STACKERROR_Opt.</td>
</tr>
<tr class="expectedfail">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:orange;"><i class="fa fa-adjust"></i></span></td>
  <td class="cell c2"><pre>0</pre></td>
  <td class="cell c3"><pre>0</pre></td>
  <td class="cell c4"><pre>[x,NOCONST,1/0]</pre></td>
  <td class="cell c5">-1</td>
  <td class="cell c6">ATInt_STACKERROR_Opt.</td>
</tr>
<tr class="notes">
  <td class="cell c0"><td colspan="6">Basic tests</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>x^3/3</pre></td>
  <td class="cell c3"><pre>x^3/3</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATInt_const.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">You need to add a constant of integration, otherwise this appears to be correct. Well done.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>x^3/3+1</pre></td>
  <td class="cell c3"><pre>x^3/3</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATInt_const_int.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">You need to add a constant of integration. This should be an arbitrary constant, not a number.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>x^3/3+c</pre></td>
  <td class="cell c3"><pre>x^3/3</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATInt_true.</td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>x^3/3-c</pre></td>
  <td class="cell c3"><pre>x^3/3</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATInt_true.</td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>x^3/3+c+1</pre></td>
  <td class="cell c3"><pre>x^3/3</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATInt_true.</td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>x^3/3+3*c</pre></td>
  <td class="cell c3"><pre>x^3/3</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATInt_true.</td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>(x^3+c)/3</pre></td>
  <td class="cell c3"><pre>x^3/3</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATInt_true.</td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>x^(k+1)/(k+1)</pre></td>
  <td class="cell c3"><pre>x^(k+1)/(k+1)</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATInt_const.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">You need to add a constant of integration, otherwise this appears to be correct. Well done.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>x^(k+1)/(k+1)+c</pre></td>
  <td class="cell c3"><pre>x^(k+1)/(k+1)</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATInt_true.</td>
</tr>
<tr class="expectedfail">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:orange;"><i class="fa fa-adjust"></i>!</span></td>
  <td class="cell c2"><pre>(x^(k+1)-1)/(k+1)</pre></td>
  <td class="cell c3"><pre>x^(k+1)/(k+1)</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">-2</td>
  <td class="cell c6">ATInt_true.</td>
</tr>
<tr class="expectedfail">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:orange;"><i class="fa fa-adjust"></i>!</span></td>
  <td class="cell c2"><pre>(x^(k+1)-1)/(k+1)+c</pre></td>
  <td class="cell c3"><pre>x^(k+1)/(k+1)+c</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">-3</td>
  <td class="cell c6">ATInt_weirdconst.</td>
</tr>
<tr class="expectedfail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The formal derivative of your answer does equal the expression that you were asked to integrate. However, you have a strange constant of integration. Please ask your teacher about this.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>x^3/3+c+k</pre></td>
  <td class="cell c3"><pre>x^3/3</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATInt_weirdconst.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The formal derivative of your answer does equal the expression that you were asked to integrate. However, you have a strange constant of integration. Please ask your teacher about this.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>x^3/3+c^2</pre></td>
  <td class="cell c3"><pre>x^3/3</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATInt_weirdconst.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The formal derivative of your answer does equal the expression that you were asked to integrate. However, you have a strange constant of integration. Please ask your teacher about this.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>x^3/3+c^3</pre></td>
  <td class="cell c3"><pre>x^3/3</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATInt_weirdconst.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The formal derivative of your answer does equal the expression that you were asked to integrate. However, you have a strange constant of integration. Please ask your teacher about this.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>x^3/3*c</pre></td>
  <td class="cell c3"><pre>x^3/3</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATInt_generic.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The derivative of your answer should be equal to the expression that you were asked to integrate, that was: <span class="filter_mathjaxloader_equation"><span class="nolink">\[x^2\]</span></span> In fact, the derivative of your answer, with respect to <span class="filter_mathjaxloader_equation"><span class="nolink">\(x\)</span></span> is: <span class="filter_mathjaxloader_equation"><span class="nolink">\[c\cdot x^2\]</span></span> so you must have done something wrong!</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>X^3/3+c</pre></td>
  <td class="cell c3"><pre>x^3/3</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATInt_generic. ATInt_var_SB_notSA.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The derivative of your answer should be equal to the expression that you were asked to integrate, that was: <span class="filter_mathjaxloader_equation"><span class="nolink">\[x^2\]</span></span> In fact, the derivative of your answer, with respect to <span class="filter_mathjaxloader_equation"><span class="nolink">\(x\)</span></span> is: <span class="filter_mathjaxloader_equation"><span class="nolink">\[0\]</span></span> so you must have done something wrong!</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>sin(2*x)</pre></td>
  <td class="cell c3"><pre>x^3/3</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATInt_generic.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The derivative of your answer should be equal to the expression that you were asked to integrate, that was: <span class="filter_mathjaxloader_equation"><span class="nolink">\[x^2\]</span></span> In fact, the derivative of your answer, with respect to <span class="filter_mathjaxloader_equation"><span class="nolink">\(x\)</span></span> is: <span class="filter_mathjaxloader_equation"><span class="nolink">\[2\cdot \cos \left( 2\cdot x \right)\]</span></span> so you must have done something wrong!</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>x^2/2-2*x+2+c</pre></td>
  <td class="cell c3"><pre>(x-2)^2/2</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATInt_true.</td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>(t-1)^5/5+c</pre></td>
  <td class="cell c3"><pre>(t-1)^5/5</pre></td>
  <td class="cell c4"><pre>t</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATInt_true.</td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>(v-1)^5/5+c</pre></td>
  <td class="cell c3"><pre>(v-1)^5/5</pre></td>
  <td class="cell c4"><pre>v</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATInt_true.</td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>cos(2*x)/2+1+c</pre></td>
  <td class="cell c3"><pre>cos(2*x)/2</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATInt_true.</td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>(x-a)^6001/6001+c</pre></td>
  <td class="cell c3"><pre>(x-a)^6001/6001</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATInt_true.</td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>(x-a)^6001/6001</pre></td>
  <td class="cell c3"><pre>(x-a)^6001/6001</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATInt_const.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">You need to add a constant of integration, otherwise this appears to be correct. Well done.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>6000*(x-a)^5999</pre></td>
  <td class="cell c3"><pre>(x-a)^6001/6001</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATInt_diff.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">It looks like you have differentiated instead!</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>4*%e^(4*x)/(%e^(4*x)+1)</pre></td>
  <td class="cell c3"><pre>log(%e^(4*x)+1)+c</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATInt_generic.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The derivative of your answer should be equal to the expression that you were asked to integrate, that was: <span class="filter_mathjaxloader_equation"><span class="nolink">\[\frac{4\cdot e^{4\cdot x}}{e^{4\cdot x}+1}\]</span></span> In fact, the derivative of your answer, with respect to <span class="filter_mathjaxloader_equation"><span class="nolink">\(x\)</span></span> is: <span class="filter_mathjaxloader_equation"><span class="nolink">\[\frac{16\cdot e^{4\cdot x}}{e^{4\cdot x}+1}-\frac{16\cdot e^{8 \cdot x}}{{\left(e^{4\cdot x}+1\right)}^2}\]</span></span> so you must have done something wrong!</td></td>
</tr>
<tr class="notes">
  <td class="cell c0"><td colspan="6">The teacher adds a constant</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>x^3/3+c</pre></td>
  <td class="cell c3"><pre>x^3/3+c</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATInt_true.</td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>x^2/2-2*x+2+c</pre></td>
  <td class="cell c3"><pre>(x-2)^2/2+k</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATInt_true.</td>
</tr>
<tr class="notes">
  <td class="cell c0"><td colspan="6">The teacher condones lack of constant, or numerical constant</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>x^3/3</pre></td>
  <td class="cell c3"><pre>x^3/3</pre></td>
  <td class="cell c4"><pre>[x,NOCONST]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATInt_const_condone.</td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>x^3/3+c</pre></td>
  <td class="cell c3"><pre>x^3/3</pre></td>
  <td class="cell c4"><pre>[x,NOCONST]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATInt_true.</td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>x^2/2-2*x+2</pre></td>
  <td class="cell c3"><pre>(x-2)^2/2+k</pre></td>
  <td class="cell c4"><pre>[x,NOCONST]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATInt_const_condone.</td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>x^3/3+1</pre></td>
  <td class="cell c3"><pre>x^3/3</pre></td>
  <td class="cell c4"><pre>[x,NOCONST]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATInt_const_int_condone.</td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>x^3/3+c^2</pre></td>
  <td class="cell c3"><pre>x^3/3</pre></td>
  <td class="cell c4"><pre>[x,NOCONST]</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATInt_weirdconst.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The formal derivative of your answer does equal the expression that you were asked to integrate. However, you have a strange constant of integration. Please ask your teacher about this.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>n*x^n</pre></td>
  <td class="cell c3"><pre>n*x^(n-1)</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATInt_generic.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The derivative of your answer should be equal to the expression that you were asked to integrate, that was: <span class="filter_mathjaxloader_equation"><span class="nolink">\[\left(n-1\right)\cdot n\cdot x^{n-2}\]</span></span> In fact, the derivative of your answer, with respect to <span class="filter_mathjaxloader_equation"><span class="nolink">\(x\)</span></span> is: <span class="filter_mathjaxloader_equation"><span class="nolink">\[n^2\cdot x^{n-1}\]</span></span> so you must have done something wrong!</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>n*x^n</pre></td>
  <td class="cell c3"><pre>(assume(n&gt;0), n*x^(n-1))</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATInt_generic.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The derivative of your answer should be equal to the expression that you were asked to integrate, that was: <span class="filter_mathjaxloader_equation"><span class="nolink">\[\left(n-1\right)\cdot n\cdot x^{n-2}\]</span></span> In fact, the derivative of your answer, with respect to <span class="filter_mathjaxloader_equation"><span class="nolink">\(x\)</span></span> is: <span class="filter_mathjaxloader_equation"><span class="nolink">\[n^2\cdot x^{n-1}\]</span></span> so you must have done something wrong!</td></td>
</tr>
<tr class="notes">
  <td class="cell c0"><td colspan="6">Special case</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>exp(x)+c</pre></td>
  <td class="cell c3"><pre>exp(x)</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATInt_true.</td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>exp(x)</pre></td>
  <td class="cell c3"><pre>exp(x)</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATInt_const.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">You need to add a constant of integration, otherwise this appears to be correct. Well done.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>exp(x)</pre></td>
  <td class="cell c3"><pre>exp(x)</pre></td>
  <td class="cell c4"><pre>[x,NOCONST]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATInt_const_condone.</td>
</tr>
<tr class="notes">
  <td class="cell c0"><td colspan="6">Student differentiates by mistake</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>2*x</pre></td>
  <td class="cell c3"><pre>x^3/3</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATInt_diff.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">It looks like you have differentiated instead!</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>2*x+c</pre></td>
  <td class="cell c3"><pre>x^3/3</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATInt_diff.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">It looks like you have differentiated instead!</td></td>
</tr>
<tr class="notes">
  <td class="cell c0"><td colspan="6">Sloppy logs (teacher ignores abs(x) )</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>ln(x)</pre></td>
  <td class="cell c3"><pre>ln(x)</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATInt_const.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">You need to add a constant of integration, otherwise this appears to be correct. Well done.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>ln(x)</pre></td>
  <td class="cell c3"><pre>ln(x)</pre></td>
  <td class="cell c4"><pre>[x,NOCONST]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATInt_const_condone.</td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>ln(x)+c</pre></td>
  <td class="cell c3"><pre>ln(x)+c</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATInt_true_equiv.</td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>ln(k*x)</pre></td>
  <td class="cell c3"><pre>ln(x)+c</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATInt_true_equiv.</td>
</tr>
<tr class="notes">
  <td class="cell c0"><td colspan="6">Fussy logs (teacher uses abs(x) )</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>ln(x)</pre></td>
  <td class="cell c3"><pre>ln(abs(x))+c</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATInt_EqFormalDiff. ATInt_logabs.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The formal derivative of your answer does equal the expression that you were asked to integrate. However, your answer differs from the correct answer in a significant way, that is to say not just, e.g., a constant of integration. Your teacher may expect you to use the result <span class="filter_mathjaxloader_equation"><span class="nolink">\(\int\frac{1}{x} dx = \log(|x|)+c\)</span></span>, rather than <span class="filter_mathjaxloader_equation"><span class="nolink">\(\int\frac{1}{x} dx = \log(x)+c\)</span></span>. Please ask your teacher about this.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>ln(x)+c</pre></td>
  <td class="cell c3"><pre>ln(abs(x))+c</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATInt_EqFormalDiff. ATInt_logabs.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The formal derivative of your answer does equal the expression that you were asked to integrate. However, your answer differs from the correct answer in a significant way, that is to say not just, e.g., a constant of integration. Your teacher may expect you to use the result <span class="filter_mathjaxloader_equation"><span class="nolink">\(\int\frac{1}{x} dx = \log(|x|)+c\)</span></span>, rather than <span class="filter_mathjaxloader_equation"><span class="nolink">\(\int\frac{1}{x} dx = \log(x)+c\)</span></span>. Please ask your teacher about this.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>ln(x)</pre></td>
  <td class="cell c3"><pre>ln(abs(x))+c</pre></td>
  <td class="cell c4"><pre>[x, NOCONST]</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATInt_EqFormalDiff. ATInt_logabs.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The formal derivative of your answer does equal the expression that you were asked to integrate. However, your answer differs from the correct answer in a significant way, that is to say not just, e.g., a constant of integration. Your teacher may expect you to use the result <span class="filter_mathjaxloader_equation"><span class="nolink">\(\int\frac{1}{x} dx = \log(|x|)+c\)</span></span>, rather than <span class="filter_mathjaxloader_equation"><span class="nolink">\(\int\frac{1}{x} dx = \log(x)+c\)</span></span>. Please ask your teacher about this.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>ln(abs(x))</pre></td>
  <td class="cell c3"><pre>ln(abs(x))+c</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATInt_const.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">You need to add a constant of integration, otherwise this appears to be correct. Well done.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>ln(abs(x))+c</pre></td>
  <td class="cell c3"><pre>ln(abs(x))+c</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATInt_true_equiv.</td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>ln(k*x)</pre></td>
  <td class="cell c3"><pre>ln(abs(x))+c</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATInt_EqFormalDiff. ATInt_logabs.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The formal derivative of your answer does equal the expression that you were asked to integrate. However, your answer differs from the correct answer in a significant way, that is to say not just, e.g., a constant of integration. Your teacher may expect you to use the result <span class="filter_mathjaxloader_equation"><span class="nolink">\(\int\frac{1}{x} dx = \log(|x|)+c\)</span></span>, rather than <span class="filter_mathjaxloader_equation"><span class="nolink">\(\int\frac{1}{x} dx = \log(x)+c\)</span></span>. Please ask your teacher about this.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>ln(k*abs(x))</pre></td>
  <td class="cell c3"><pre>ln(abs(x))+c</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATInt_true_equiv.</td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>ln(abs(k*x))</pre></td>
  <td class="cell c3"><pre>ln(abs(x))+c</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATInt_true_equiv.</td>
</tr>
<tr class="notes">
  <td class="cell c0"><td colspan="6">Teacher uses ln(k*abs(x))</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>ln(x)</pre></td>
  <td class="cell c3"><pre>ln(k*abs(x))</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATInt_EqFormalDiff. ATInt_logabs.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The formal derivative of your answer does equal the expression that you were asked to integrate. However, your answer differs from the correct answer in a significant way, that is to say not just, e.g., a constant of integration. Your teacher may expect you to use the result <span class="filter_mathjaxloader_equation"><span class="nolink">\(\int\frac{1}{x} dx = \log(|x|)+c\)</span></span>, rather than <span class="filter_mathjaxloader_equation"><span class="nolink">\(\int\frac{1}{x} dx = \log(x)+c\)</span></span>. Please ask your teacher about this.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>ln(x)+c</pre></td>
  <td class="cell c3"><pre>ln(k*abs(x))</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATInt_EqFormalDiff. ATInt_logabs.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The formal derivative of your answer does equal the expression that you were asked to integrate. However, your answer differs from the correct answer in a significant way, that is to say not just, e.g., a constant of integration. Your teacher may expect you to use the result <span class="filter_mathjaxloader_equation"><span class="nolink">\(\int\frac{1}{x} dx = \log(|x|)+c\)</span></span>, rather than <span class="filter_mathjaxloader_equation"><span class="nolink">\(\int\frac{1}{x} dx = \log(x)+c\)</span></span>. Please ask your teacher about this.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>ln(abs(x))</pre></td>
  <td class="cell c3"><pre>ln(k*abs(x))</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATInt_const.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">You need to add a constant of integration, otherwise this appears to be correct. Well done.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>ln(abs(x))+c</pre></td>
  <td class="cell c3"><pre>ln(k*abs(x))</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATInt_true_equiv.</td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>ln(k*x)</pre></td>
  <td class="cell c3"><pre>ln(k*abs(x))</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATInt_EqFormalDiff. ATInt_logabs.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The formal derivative of your answer does equal the expression that you were asked to integrate. However, your answer differs from the correct answer in a significant way, that is to say not just, e.g., a constant of integration. Your teacher may expect you to use the result <span class="filter_mathjaxloader_equation"><span class="nolink">\(\int\frac{1}{x} dx = \log(|x|)+c\)</span></span>, rather than <span class="filter_mathjaxloader_equation"><span class="nolink">\(\int\frac{1}{x} dx = \log(x)+c\)</span></span>. Please ask your teacher about this.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>ln(k*abs(x))</pre></td>
  <td class="cell c3"><pre>ln(k*abs(x))</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATInt_true_equiv.</td>
</tr>
<tr class="notes">
  <td class="cell c0"><td colspan="6">Other logs</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>ln(x)+ln(a)</pre></td>
  <td class="cell c3"><pre>ln(k*abs(x+a))</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATInt_generic. ATInt_logabs.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The formal derivative of your answer does equal the expression that you were asked to integrate. However, your answer differs from the correct answer in a significant way, that is to say not just, e.g., a constant of integration. Your teacher may expect you to use the result <span class="filter_mathjaxloader_equation"><span class="nolink">\(\int\frac{1}{x} dx = \log(|x|)+c\)</span></span>, rather than <span class="filter_mathjaxloader_equation"><span class="nolink">\(\int\frac{1}{x} dx = \log(x)+c\)</span></span>. Please ask your teacher about this.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>log(x)^2-2*log(c)*log(x)+k</pre></td>
  <td class="cell c3"><pre>ln(c/x)^2</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATInt_EqFormalDiff.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The formal derivative of your answer does equal the expression that you were asked to integrate. However, your answer differs from the correct answer in a significant way, that is to say not just, e.g., a constant of integration. Please ask your teacher about this.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>log(x)^2-2*log(c)*log(x)+k</pre></td>
  <td class="cell c3"><pre>ln(abs(c/x))^2</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATInt_generic.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The derivative of your answer should be equal to the expression that you were asked to integrate, that was: <span class="filter_mathjaxloader_equation"><span class="nolink">\[-\frac{2\cdot \ln \left( \frac{\left| c\right| }{\left| x\right| } \right)}{x}\]</span></span> In fact, the derivative of your answer, with respect to <span class="filter_mathjaxloader_equation"><span class="nolink">\(x\)</span></span> is: <span class="filter_mathjaxloader_equation"><span class="nolink">\[\frac{2\cdot \ln \left( x \right)}{x}-\frac{2\cdot \ln \left( c \right)}{x}\]</span></span> so you must have done something wrong!</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>c-(log(2)-log(x))^2/2</pre></td>
  <td class="cell c3"><pre>-1/2*log(2/x)^2</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATInt_true_equiv.</td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>ln(abs(x+3))/2+c</pre></td>
  <td class="cell c3"><pre>ln(abs(2*x+6))/2+c</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATInt_EqFormalDiff.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The formal derivative of your answer does equal the expression that you were asked to integrate. However, your answer differs from the correct answer in a significant way, that is to say not just, e.g., a constant of integration. Please ask your teacher about this.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>ln(abs(x+3))/2+c</pre></td>
  <td class="cell c3"><pre>ln(abs(2*x+6))/2+c</pre></td>
  <td class="cell c4"><pre>[x, FORMAL]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATInt_EqFormalDiff.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The formal derivative of your answer does equal the expression that you were asked to integrate. However, your answer differs from the correct answer in a significant way, that is to say not just, e.g., a constant of integration. Please ask your teacher about this.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>ln(abs(x+3))/2</pre></td>
  <td class="cell c3"><pre>ln(abs(2*x+6))/2+c</pre></td>
  <td class="cell c4"><pre>[x, FORMAL]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATInt_EqFormalDiff.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The formal derivative of your answer does equal the expression that you were asked to integrate. However, your answer differs from the correct answer in a significant way, that is to say not just, e.g., a constant of integration. Please ask your teacher about this.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>ln(abs(x+3))/2</pre></td>
  <td class="cell c3"><pre>ln(abs(2*x+6))/2+c</pre></td>
  <td class="cell c4"><pre>[x, FORMAL, NOC
ONST]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATInt_EqFormalDiff.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The formal derivative of your answer does equal the expression that you were asked to integrate. However, your answer differs from the correct answer in a significant way, that is to say not just, e.g., a constant of integration. Please ask your teacher about this.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>ln(abs(x+3))/2</pre></td>
  <td class="cell c3"><pre>ln(abs(2*x+6))/2+c</pre></td>
  <td class="cell c4"><pre>[x, NOCONST, FO
RMAL]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATInt_EqFormalDiff.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The formal derivative of your answer does equal the expression that you were asked to integrate. However, your answer differs from the correct answer in a significant way, that is to say not just, e.g., a constant of integration. Please ask your teacher about this.</td></td>
</tr>
<tr class="expectedfail">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:orange;"><i class="fa fa-adjust"></i>!</span></td>
  <td class="cell c2"><pre>ln(abs(x+3))/2</pre></td>
  <td class="cell c3"><pre>ln(abs(2*x+6))/2+c</pre></td>
  <td class="cell c4"><pre>[x, NOCONST]</pre></td>
  <td class="cell c5">-3</td>
  <td class="cell c6">ATInt_EqFormalDiff.</td>
</tr>
<tr class="expectedfail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The formal derivative of your answer does equal the expression that you were asked to integrate. However, your answer differs from the correct answer in a significant way, that is to say not just, e.g., a constant of integration. Please ask your teacher about this.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>-log(sqrt(x^2-4*x+3)+x-2)/2+(x
*sqrt(x^2-4*x+3))/2-sqrt(x^2-4
*x+3)+c</pre></td>
  <td class="cell c3"><pre>integrate(sqrt(x^2-4*x+3),x)</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATInt_EqFormalDiff.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The formal derivative of your answer does equal the expression that you were asked to integrate. However, your answer differs from the correct answer in a significant way, that is to say not just, e.g., a constant of integration. Please ask your teacher about this.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>-log(sqrt(x^2-4*x+3)+x-2)/2+(x
*sqrt(x^2-4*x+3))/2-sqrt(x^2-4
*x+3)+c</pre></td>
  <td class="cell c3"><pre>integrate(sqrt(x^2-4*x+3),x)</pre></td>
  <td class="cell c4"><pre>[x, FORMAL]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATInt_EqFormalDiff.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The formal derivative of your answer does equal the expression that you were asked to integrate. However, your answer differs from the correct answer in a significant way, that is to say not just, e.g., a constant of integration. Please ask your teacher about this.</td></td>
</tr>
<tr class="notes">
  <td class="cell c0"><td colspan="6">Irreducible quadratic</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>ln(x^2+7*x+7)</pre></td>
  <td class="cell c3"><pre>ln(x^2+7*x+7)</pre></td>
  <td class="cell c4"><pre>[x,NOCONST]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATInt_const_condone.</td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>ln(x^2+7*x+7)</pre></td>
  <td class="cell c3"><pre>ln(abs(x^2+7*x+7))</pre></td>
  <td class="cell c4"><pre>[x,NOCONST]</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATInt_EqFormalDiff. ATInt_logabs.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The formal derivative of your answer does equal the expression that you were asked to integrate. However, your answer differs from the correct answer in a significant way, that is to say not just, e.g., a constant of integration. Your teacher may expect you to use the result <span class="filter_mathjaxloader_equation"><span class="nolink">\(\int\frac{1}{x} dx = \log(|x|)+c\)</span></span>, rather than <span class="filter_mathjaxloader_equation"><span class="nolink">\(\int\frac{1}{x} dx = \log(x)+c\)</span></span>. Please ask your teacher about this.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>ln(x^2+7*x+7)+c</pre></td>
  <td class="cell c3"><pre>ln(x^2+7*x+7)+c</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATInt_true_equiv.</td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>ln(k*(x^2+7*x+7))</pre></td>
  <td class="cell c3"><pre>ln(x^2+7*x+7)+c</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATInt_true_equiv.</td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>ln(x^2+7*x+7)</pre></td>
  <td class="cell c3"><pre>ln(abs(x^2+7*x+7))+c</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATInt_EqFormalDiff. ATInt_logabs.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The formal derivative of your answer does equal the expression that you were asked to integrate. However, your answer differs from the correct answer in a significant way, that is to say not just, e.g., a constant of integration. Your teacher may expect you to use the result <span class="filter_mathjaxloader_equation"><span class="nolink">\(\int\frac{1}{x} dx = \log(|x|)+c\)</span></span>, rather than <span class="filter_mathjaxloader_equation"><span class="nolink">\(\int\frac{1}{x} dx = \log(x)+c\)</span></span>. Please ask your teacher about this.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>ln(x^2+7*x+7)+c</pre></td>
  <td class="cell c3"><pre>ln(abs(x^2+7*x+7))+c</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATInt_EqFormalDiff. ATInt_logabs.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The formal derivative of your answer does equal the expression that you were asked to integrate. However, your answer differs from the correct answer in a significant way, that is to say not just, e.g., a constant of integration. Your teacher may expect you to use the result <span class="filter_mathjaxloader_equation"><span class="nolink">\(\int\frac{1}{x} dx = \log(|x|)+c\)</span></span>, rather than <span class="filter_mathjaxloader_equation"><span class="nolink">\(\int\frac{1}{x} dx = \log(x)+c\)</span></span>. Please ask your teacher about this.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>ln(abs(x^2+7*x+7))+c</pre></td>
  <td class="cell c3"><pre>ln(abs(x^2+7*x+7))+c</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATInt_true_equiv.</td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>ln(k*abs(x^2+7*x+7))</pre></td>
  <td class="cell c3"><pre>ln(abs(x^2+7*x+7))+c</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATInt_true_equiv.</td>
</tr>
<tr class="notes">
  <td class="cell c0"><td colspan="6">Two logs</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>log(abs(x-3))+log(abs(x+3))</pre></td>
  <td class="cell c3"><pre>log(abs(x-3))+log(abs(x+3))</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATInt_const.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">You need to add a constant of integration, otherwise this appears to be correct. Well done.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>log(abs(x-3))+log(abs(x+3))+c</pre></td>
  <td class="cell c3"><pre>log(abs(x-3))+log(abs(x+3))</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATInt_true_equiv.</td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>log(abs(x-3))+log(abs(x+3))</pre></td>
  <td class="cell c3"><pre>log(x-3)+log(x+3)</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATInt_const.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">You need to add a constant of integration, otherwise this appears to be correct. Well done.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>log(abs(x-3))+log(abs(x+3))+c</pre></td>
  <td class="cell c3"><pre>log(x-3)+log(x+3)</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATInt_true_equiv.</td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>log(x-3)+log(x+3)</pre></td>
  <td class="cell c3"><pre>log(x-3)+log(x+3)</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATInt_const.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">You need to add a constant of integration, otherwise this appears to be correct. Well done.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>log(x-3)+log(x+3)+c</pre></td>
  <td class="cell c3"><pre>log(x-3)+log(x+3)</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATInt_true_equiv.</td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>log(x-3)+log(x+3)</pre></td>
  <td class="cell c3"><pre>log(abs(x-3))+log(abs(x+3))</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATInt_EqFormalDiff. ATInt_logabs.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The formal derivative of your answer does equal the expression that you were asked to integrate. However, your answer differs from the correct answer in a significant way, that is to say not just, e.g., a constant of integration. Your teacher may expect you to use the result <span class="filter_mathjaxloader_equation"><span class="nolink">\(\int\frac{1}{x} dx = \log(|x|)+c\)</span></span>, rather than <span class="filter_mathjaxloader_equation"><span class="nolink">\(\int\frac{1}{x} dx = \log(x)+c\)</span></span>. Please ask your teacher about this.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>log(x-3)+log(x+3)+c</pre></td>
  <td class="cell c3"><pre>log(abs(x-3))+log(abs(x+3))</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATInt_EqFormalDiff. ATInt_logabs.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The formal derivative of your answer does equal the expression that you were asked to integrate. However, your answer differs from the correct answer in a significant way, that is to say not just, e.g., a constant of integration. Your teacher may expect you to use the result <span class="filter_mathjaxloader_equation"><span class="nolink">\(\int\frac{1}{x} dx = \log(|x|)+c\)</span></span>, rather than <span class="filter_mathjaxloader_equation"><span class="nolink">\(\int\frac{1}{x} dx = \log(x)+c\)</span></span>. Please ask your teacher about this.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>log(abs((x-3)*(x+3)))+c</pre></td>
  <td class="cell c3"><pre>log(abs(x-3))+log(abs(x+3))</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATInt_true_equiv.</td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>log(abs((x^2-9)))+c</pre></td>
  <td class="cell c3"><pre>log(abs(x-3))+log(abs(x+3))</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATInt_EqFormalDiff.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The formal derivative of your answer does equal the expression that you were asked to integrate. However, your answer differs from the correct answer in a significant way, that is to say not just, e.g., a constant of integration. Please ask your teacher about this.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>2*log(abs(x-2))-log(abs(x+2))+
(x^2+4*x)/2</pre></td>
  <td class="cell c3"><pre>-log(abs(x+2))+2*log(abs(x-2))
+(x^2+4*x)/2+c</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATInt_const.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">You need to add a constant of integration, otherwise this appears to be correct. Well done.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>-log(abs(x+2))+2*log(abs(x-2))
+(x^2+4*x)/2+c</pre></td>
  <td class="cell c3"><pre>-log(abs(x+2))+2*log(abs(x-2))
+(x^2+4*x)/2+c</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATInt_true_equiv.</td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>-log(abs(x+2))+2*log(abs(x-2))
+(x^2+4*x)/2+c</pre></td>
  <td class="cell c3"><pre>-log((x+2))+2*log((x-2))+(x^2+
4*x)/2</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATInt_true_equiv.</td>
</tr>
<tr class="notes">
  <td class="cell c0"><td colspan="6">Inconsistent log(abs())</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>log(abs(x-3))+log((x+3))+c</pre></td>
  <td class="cell c3"><pre>log(x-3)+log(x+3)</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATInt_true_equiv. ATInt_logabs_inconsistent.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">There appear to be strange inconsistencies between your use of <span class="filter_mathjaxloader_equation"><span class="nolink">\(\log(...)\)</span></span> and <span class="filter_mathjaxloader_equation"><span class="nolink">\(\log(|...|)\)</span></span>. Please ask your teacher about this.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>log((v-3))+log(abs(v+3))+c</pre></td>
  <td class="cell c3"><pre>log(v-3)+log(v+3)</pre></td>
  <td class="cell c4"><pre>v</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATInt_true_equiv. ATInt_logabs_inconsistent.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">There appear to be strange inconsistencies between your use of <span class="filter_mathjaxloader_equation"><span class="nolink">\(\log(...)\)</span></span> and <span class="filter_mathjaxloader_equation"><span class="nolink">\(\log(|...|)\)</span></span>. Please ask your teacher about this.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>log((x-3))+log(abs(x+3))</pre></td>
  <td class="cell c3"><pre>log(x-3)+log(x+3)</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATInt_const. ATInt_logabs_inconsistent.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">There appear to be strange inconsistencies between your use of <span class="filter_mathjaxloader_equation"><span class="nolink">\(\log(...)\)</span></span> and <span class="filter_mathjaxloader_equation"><span class="nolink">\(\log(|...|)\)</span></span>. Please ask your teacher about this.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>2*log((x-2))-log(abs(x+2))+(x^
2+4*x)/2</pre></td>
  <td class="cell c3"><pre>-log(abs(x+2))+2*log(abs(x-2))
+(x^2+4*x)/2</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATInt_EqFormalDiff. ATInt_logabs. ATInt_logabs_inconsistent.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">There appear to be strange inconsistencies between your use of <span class="filter_mathjaxloader_equation"><span class="nolink">\(\log(...)\)</span></span> and <span class="filter_mathjaxloader_equation"><span class="nolink">\(\log(|...|)\)</span></span>. Please ask your teacher about this.</td></td>
</tr>
<tr class="notes">
  <td class="cell c0"><td colspan="6">Significant integration constant differences</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>2*(sqrt(t)-5)-10*log((sqrt(t)-
5))+c</pre></td>
  <td class="cell c3"><pre>2*(sqrt(t)-5)-10*log((sqrt(t)-
5))+c</pre></td>
  <td class="cell c4"><pre>t</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATInt_true_equiv.</td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>2*(sqrt(t))-10*log((sqrt(t)-5)
)+c</pre></td>
  <td class="cell c3"><pre>2*(sqrt(t)-5)-10*log((sqrt(t)-
5))+c</pre></td>
  <td class="cell c4"><pre>t</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATInt_true_differentconst.</td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>2*(sqrt(t)-5)-10*log((sqrt(t)-
5))+c</pre></td>
  <td class="cell c3"><pre>2*(sqrt(t)-5)-10*log(abs(sqrt(
t)-5))+c</pre></td>
  <td class="cell c4"><pre>t</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATInt_EqFormalDiff. ATInt_logabs.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The formal derivative of your answer does equal the expression that you were asked to integrate. However, your answer differs from the correct answer in a significant way, that is to say not just, e.g., a constant of integration. Your teacher may expect you to use the result <span class="filter_mathjaxloader_equation"><span class="nolink">\(\int\frac{1}{x} dx = \log(|x|)+c\)</span></span>, rather than <span class="filter_mathjaxloader_equation"><span class="nolink">\(\int\frac{1}{x} dx = \log(x)+c\)</span></span>. Please ask your teacher about this.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>2*(sqrt(t))-10*log(abs(sqrt(t)
-5))+c</pre></td>
  <td class="cell c3"><pre>2*(sqrt(t)-5)-10*log(abs(sqrt(
t)-5))+c</pre></td>
  <td class="cell c4"><pre>t</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATInt_true_differentconst.</td>
</tr>
<tr class="notes">
  <td class="cell c0"><td colspan="6">Trig</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>2*sin(x)*cos(x)</pre></td>
  <td class="cell c3"><pre>sin(2*x)+c</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATInt_const.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">You need to add a constant of integration, otherwise this appears to be correct. Well done.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>2*sin(x)*cos(x)+k</pre></td>
  <td class="cell c3"><pre>sin(2*x)+c</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATInt_true.</td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>-2*cos(3*x)/3-3*cos(2*x)/2</pre></td>
  <td class="cell c3"><pre>-2*cos(3*x)/3-3*cos(2*x)/2+c</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATInt_const.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">You need to add a constant of integration, otherwise this appears to be correct. Well done.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>-2*cos(3*x)/3-3*cos(2*x)/2+1</pre></td>
  <td class="cell c3"><pre>-2*cos(3*x)/3-3*cos(2*x)/2+c</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATInt_const_int.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">You need to add a constant of integration. This should be an arbitrary constant, not a number.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>-2*cos(3*x)/3-3*cos(2*x)/2+c</pre></td>
  <td class="cell c3"><pre>-2*cos(3*x)/3-3*cos(2*x)/2+c</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATInt_true.</td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>(tan(2*t)-2*t)/2</pre></td>
  <td class="cell c3"><pre>-(t*sin(4*t)^2-sin(4*t)+t*cos(
4*t)^2+2*t*cos(4*t)+t)/(sin(4*
t)^2+cos(4*t)^2+2*cos(4*t)+1)</pre></td>
  <td class="cell c4"><pre>t</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATInt_const.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">You need to add a constant of integration, otherwise this appears to be correct. Well done.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>(tan(2*t)-2*t)/2+1</pre></td>
  <td class="cell c3"><pre>-(t*sin(4*t)^2-sin(4*t)+t*cos(
4*t)^2+2*t*cos(4*t)+t)/(sin(4*
t)^2+cos(4*t)^2+2*cos(4*t)+1)</pre></td>
  <td class="cell c4"><pre>t</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATInt_const_int.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">You need to add a constant of integration. This should be an arbitrary constant, not a number.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>(tan(2*t)-2*t)/2+c</pre></td>
  <td class="cell c3"><pre>-(t*sin(4*t)^2-sin(4*t)+t*cos(
4*t)^2+2*t*cos(4*t)+t)/(sin(4*
t)^2+cos(4*t)^2+2*cos(4*t)+1)</pre></td>
  <td class="cell c4"><pre>t</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATInt_true.</td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>tan(x)-x+c</pre></td>
  <td class="cell c3"><pre>tan(x)-x</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATInt_true.</td>
</tr>
<tr class="notes">
  <td class="cell c0"><td colspan="6">Note the difference in feedback here, generated by the options.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>((5*%e^7*x-%e^7)*%e^(5*x))</pre></td>
  <td class="cell c3"><pre>((5*%e^7*x-%e^7)*%e^(5*x))/25+
c</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATInt_generic.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The derivative of your answer should be equal to the expression that you were asked to integrate, that was: <span class="filter_mathjaxloader_equation"><span class="nolink">\[\frac{e^{5\cdot x+7}}{5}+\frac{\left(5\cdot e^7\cdot x-e^7\right) \cdot e^{5\cdot x}}{5}\]</span></span> In fact, the derivative of your answer, with respect to <span class="filter_mathjaxloader_equation"><span class="nolink">\(x\)</span></span> is: <span class="filter_mathjaxloader_equation"><span class="nolink">\[5\cdot e^{5\cdot x+7}+5\cdot \left(5\cdot e^7\cdot x-e^7\right) \cdot e^{5\cdot x}\]</span></span> so you must have done something wrong!</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>((5*%e^7*x-%e^7)*%e^(5*x))</pre></td>
  <td class="cell c3"><pre>((5*%e^7*x-%e^7)*%e^(5*x))/25+
c</pre></td>
  <td class="cell c4"><pre>[x,x*%e^(5*x+7)
]</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATInt_generic.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The derivative of your answer should be equal to the expression that you were asked to integrate, that was: <span class="filter_mathjaxloader_equation"><span class="nolink">\[x\cdot e^{5\cdot x+7}\]</span></span> In fact, the derivative of your answer, with respect to <span class="filter_mathjaxloader_equation"><span class="nolink">\(x\)</span></span> is: <span class="filter_mathjaxloader_equation"><span class="nolink">\[5\cdot e^{5\cdot x+7}+5\cdot \left(5\cdot e^7\cdot x-e^7\right) \cdot e^{5\cdot x}\]</span></span> so you must have done something wrong!</td></td>
</tr>
<tr class="notes">
  <td class="cell c0"><td colspan="6">Inverse hyperbolic integrals</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>log(x-3)/6-log(x+3)/6+c</pre></td>
  <td class="cell c3"><pre>log(x-3)/6-log(x+3)/6</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATInt_true_equiv.</td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>asinh(x)</pre></td>
  <td class="cell c3"><pre>ln(x+sqrt(x^2+1))</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATInt_const.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">You need to add a constant of integration, otherwise this appears to be correct. Well done.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>asinh(x)+c</pre></td>
  <td class="cell c3"><pre>ln(x+sqrt(x^2+1))</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATInt_true.</td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>-acoth(x/3)/3</pre></td>
  <td class="cell c3"><pre>log(x-3)/6-log(x+3)/6</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATInt_const.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">You need to add a constant of integration, otherwise this appears to be correct. Well done.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>-acoth(x/3)/3</pre></td>
  <td class="cell c3"><pre>log(x-3)/6-log(x+3)/6</pre></td>
  <td class="cell c4"><pre>[x, NOCONST]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATInt_true.</td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>-acoth(x/3)/3+c</pre></td>
  <td class="cell c3"><pre>log(x-3)/6-log(x+3)/6</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATInt_true.</td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>-acoth(x/3)/3+c</pre></td>
  <td class="cell c3"><pre>log(abs(x-3))/6-log(abs(x+3))/
6</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATInt_true.</td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>log(x-a)/(2*a)-log(x+a)/(2*a)+
c</pre></td>
  <td class="cell c3"><pre>log(x-a)/(2*a)-log(x+a)/(2*a)</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATInt_true_equiv.</td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>-acoth(x/a)/a+c</pre></td>
  <td class="cell c3"><pre>log(x-a)/(2*a)-log(x+a)/(2*a)</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATInt_true.</td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>-acoth(x/a)/a+c</pre></td>
  <td class="cell c3"><pre>log(abs(x-a))/(2*a)-log(abs(x+
a))/(2*a)</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATInt_true.</td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>log(x-a)/(2*a)-log(x+a)/(2*a)+
c</pre></td>
  <td class="cell c3"><pre>log(abs(x-a))/(2*a)-log(abs(x+
a))/(2*a)</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATInt_EqFormalDiff. ATInt_logabs.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The formal derivative of your answer does equal the expression that you were asked to integrate. However, your answer differs from the correct answer in a significant way, that is to say not just, e.g., a constant of integration. Your teacher may expect you to use the result <span class="filter_mathjaxloader_equation"><span class="nolink">\(\int\frac{1}{x} dx = \log(|x|)+c\)</span></span>, rather than <span class="filter_mathjaxloader_equation"><span class="nolink">\(\int\frac{1}{x} dx = \log(x)+c\)</span></span>. Please ask your teacher about this.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>log(x-3)/6-log(x+3)/6+c</pre></td>
  <td class="cell c3"><pre>-acoth(x/3)/3</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATInt_true.</td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>log(abs(x-3))/6-log(abs(x+3))/
6+c</pre></td>
  <td class="cell c3"><pre>-acoth(x/3)/3</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATInt_true.</td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>log(x-3)/6-log(x+3)/6</pre></td>
  <td class="cell c3"><pre>-acoth(x/3)/3</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATInt_const.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">You need to add a constant of integration, otherwise this appears to be correct. Well done.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>atan(2*x-3)+c</pre></td>
  <td class="cell c3"><pre>atan(2*x-3)</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATInt_true.</td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>atan((x-2)/(x-1))+c</pre></td>
  <td class="cell c3"><pre>atan(2*x-3)</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATInt_true.</td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>atan((x-2)/(x-1))</pre></td>
  <td class="cell c3"><pre>atan(2*x-3)</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATInt_const.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">You need to add a constant of integration, otherwise this appears to be correct. Well done.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>atan((x-1)/(x-2))</pre></td>
  <td class="cell c3"><pre>atan(2*x-3)</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATInt_generic.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The derivative of your answer should be equal to the expression that you were asked to integrate, that was: <span class="filter_mathjaxloader_equation"><span class="nolink">\[\frac{2}{{\left(2\cdot x-3\right)}^2+1}\]</span></span> In fact, the derivative of your answer, with respect to <span class="filter_mathjaxloader_equation"><span class="nolink">\(x\)</span></span> is: <span class="filter_mathjaxloader_equation"><span class="nolink">\[\frac{\frac{1}{x-2}-\frac{x-1}{{\left(x-2\right)}^2}}{\frac{{\left( x-1\right)}^2}{{\left(x-2\right)}^2}+1}\]</span></span> so you must have done something wrong!</td></td>
</tr>
<tr class="notes">
  <td class="cell c0"><td colspan="6">Stoutemyer (currently fails)</td></td>
</tr>
<tr class="expectedfail">
  <td class="cell c0">Int</td>
  <td class="cell c1"><span style="color:orange;"><i class="fa fa-adjust"></i>!</span></td>
  <td class="cell c2"><pre>2/3*sqrt(3)*(atan(sin(x)/(sqrt
(3)*(cos(x)+1)))-(atan(sin(x)/
(cos(x)+1))))+x/sqrt(3)</pre></td>
  <td class="cell c3"><pre>2*atan(sin(x)/(sqrt(3)*(cos(x)
+1)))/sqrt(3)</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">-3</td>
  <td class="cell c6">ATInt_const.</td>
</tr>
<tr class="expectedfail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">You need to add a constant of integration, otherwise this appears to be correct. Well done.</td></td>
</tr></tbody></table></div>
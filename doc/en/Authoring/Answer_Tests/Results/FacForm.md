# FacForm: Answer test results

This page exposes the results of running answer tests on STACK test cases.  This page is automatically generated from the STACK unit tests and is designed to show question authors what answer tests actually do.  This includes cases where answer tests currentl fail, which gives a negative expected mark.  Comments and further test cases are very welcome.



<h2>FacForm</h2><div class="no-overflow"><table class="flexible table table-striped table-hover generaltable generalbox stacktestsuite"><thead><tr><th class="header c0" scope="col">Test<div class="commands"></div></th><th class="header c1" scope="col">?<div class="commands"></div></th><th class="header c2" scope="col">Student response<div class="commands"></div></th><th class="header c3" scope="col">Teacher answer<div class="commands"></div></th><th class="header c4" scope="col">Opt<div class="commands"></div></th><th class="header c5" scope="col">Mark<div class="commands"></div></th><th class="header c6" scope="col">Answer note<div class="commands"></div></th>
</tr></thead><tbody>
<tr class="expectedfail">
  <td class="cell c0">FacForm</td>
  <td class="cell c1"><span style="color:orange;"><i class="fa fa-adjust"></i></span></td>
  <td class="cell c2"><pre>1/0</pre></td>
  <td class="cell c3"><pre>0</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">-1</td>
  <td class="cell c6">ATFacForm_STACKERROR_SAns.</td>
</tr>
<tr class="expectedfail">
  <td class="cell c0">FacForm</td>
  <td class="cell c1"><span style="color:orange;"><i class="fa fa-adjust"></i></span></td>
  <td class="cell c2"><pre>0</pre></td>
  <td class="cell c3"><pre>1/0</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">-1</td>
  <td class="cell c6">ATFacForm_STACKERROR_TAns.</td>
</tr>
<tr class="expectedfail">
  <td class="cell c0">FacForm</td>
  <td class="cell c1"><span style="color:orange;"><i class="fa fa-adjust"></i></span></td>
  <td class="cell c2"><pre>0</pre></td>
  <td class="cell c3"><pre>0</pre></td>
  <td class="cell c4"><pre>1/0</pre></td>
  <td class="cell c5">-1</td>
  <td class="cell c6">ATFacForm_STACKERROR_Opt.</td>
</tr>
<tr class="notes">
  <td class="cell c0"><td colspan="6">Trivial cases</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">FacForm</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>2</pre></td>
  <td class="cell c3"><pre>2</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATFacForm_int_true.</td>
</tr>
<tr class="pass">
  <td class="cell c0">FacForm</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>6</pre></td>
  <td class="cell c3"><pre>6</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATFacForm_int_true.</td>
</tr>
<tr class="pass">
  <td class="cell c0">FacForm</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>1/3</pre></td>
  <td class="cell c3"><pre>1/3</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATFacForm_true.</td>
</tr>
<tr class="pass">
  <td class="cell c0">FacForm</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>3*x^2</pre></td>
  <td class="cell c3"><pre>3*x^2</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATFacForm_true.</td>
</tr>
<tr class="pass">
  <td class="cell c0">FacForm</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>4*x^2</pre></td>
  <td class="cell c3"><pre>4*x^2</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATFacForm_true.</td>
</tr>
<tr class="notes">
  <td class="cell c0"><td colspan="6">Linear integer factors</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">FacForm</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>2*(x-1)</pre></td>
  <td class="cell c3"><pre>2*x-2</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATFacForm_true.</td>
</tr>
<tr class="pass">
  <td class="cell c0">FacForm</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>2*x-2</pre></td>
  <td class="cell c3"><pre>2*x-2</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATFacForm_notfactored.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer is not factored. You need to take out a common factor.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">FacForm</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>2*(x+1)</pre></td>
  <td class="cell c3"><pre>2*x-2</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATFacForm_isfactored. ATFacForm_notalgequiv.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer is factored, well done. Note that your answer is not algebraically equivalent to the correct answer. You must have done something wrong.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">FacForm</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>2*x+2</pre></td>
  <td class="cell c3"><pre>2*x-2</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATFacForm_notfactored. ATFacForm_notalgequiv.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer is not factored. You need to take out a common factor. Note that your answer is not algebraically equivalent to the correct answer. You must have done something wrong.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">FacForm</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>2*(x+0.5)</pre></td>
  <td class="cell c3"><pre>2*x+1</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATFacForm_default_true.</td>
</tr>
<tr class="notes">
  <td class="cell c0"><td colspan="6">Linear factors</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">FacForm</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>t*(2*x+1)</pre></td>
  <td class="cell c3"><pre>t*(2*x+1)</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATFacForm_true.</td>
</tr>
<tr class="pass">
  <td class="cell c0">FacForm</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>t*x+t</pre></td>
  <td class="cell c3"><pre>t*(x+1)</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATFacForm_notfactored.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer is not factored.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">FacForm</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>6*s*t+10*s</pre></td>
  <td class="cell c3"><pre>2*s*(3*t+5)</pre></td>
  <td class="cell c4"><pre>t</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATFacForm_notfactored.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer is not factored.</td></td>
</tr>
<tr class="notes">
  <td class="cell c0"><td colspan="6">Quadratic, with no const</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">FacForm</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>2*x*(x-3)</pre></td>
  <td class="cell c3"><pre>2*x^2-6*x</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATFacForm_true.</td>
</tr>
<tr class="pass">
  <td class="cell c0">FacForm</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>2*(x^2-3*x)</pre></td>
  <td class="cell c3"><pre>2*x*(x-3)</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATFacForm_notfactored.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer is not factored. You could still do some more work on the term <span class="filter_mathjaxloader_equation"><span class="nolink">\(x^2-3\cdot x\)</span></span>.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">FacForm</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>x*(2*x-6)</pre></td>
  <td class="cell c3"><pre>2*x*(x-3)</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATFacForm_notfactored.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer is not factored. You could still do some more work on the term <span class="filter_mathjaxloader_equation"><span class="nolink">\(2\cdot x-6\)</span></span>. You need to take out a common factor.</td></td>
</tr>
<tr class="notes">
  <td class="cell c0"><td colspan="6">Quadratic</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">FacForm</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>(x+2)*(x+3)</pre></td>
  <td class="cell c3"><pre>(x+2)*(x+3)</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATFacForm_true.</td>
</tr>
<tr class="pass">
  <td class="cell c0">FacForm</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>(x+2)*(2*x+6)</pre></td>
  <td class="cell c3"><pre>2*(x+2)*(x+3)</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATFacForm_notfactored.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer is not factored. You could still do some more work on the term <span class="filter_mathjaxloader_equation"><span class="nolink">\(2\cdot x+6\)</span></span>. You need to take out a common factor.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">FacForm</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>(z*x+z)*(2*x+6)</pre></td>
  <td class="cell c3"><pre>2*z*(x+1)*(x+3)</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATFacForm_notfactored.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer is not factored. You could still do some more work on the term <span class="filter_mathjaxloader_equation"><span class="nolink">\(z\cdot x+z\)</span></span>. You could still do some more work on the term <span class="filter_mathjaxloader_equation"><span class="nolink">\(2\cdot x+6\)</span></span>. You need to take out a common factor.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">FacForm</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>(x+t)*(x-t)</pre></td>
  <td class="cell c3"><pre>x^2-t^2</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATFacForm_true.</td>
</tr>
<tr class="pass">
  <td class="cell c0">FacForm</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>t^2-1</pre></td>
  <td class="cell c3"><pre>(t-1)*(t+1)</pre></td>
  <td class="cell c4"><pre>t</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATFacForm_notfactored.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer is not factored.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">FacForm</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>t^2+1</pre></td>
  <td class="cell c3"><pre>t^2+1</pre></td>
  <td class="cell c4"><pre>t</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATFacForm_true.</td>
</tr>
<tr class="pass">
  <td class="cell c0">FacForm</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>v^2+1</pre></td>
  <td class="cell c3"><pre>v^2+1</pre></td>
  <td class="cell c4"><pre>v</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATFacForm_true.</td>
</tr>
<tr class="pass">
  <td class="cell c0">FacForm</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>v^2-1</pre></td>
  <td class="cell c3"><pre>v^2-1</pre></td>
  <td class="cell c4"><pre>v</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATFacForm_notfactored.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer is not factored.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">FacForm</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>-(3*w-4*v+9*u)*(3*w+4*v-u)</pre></td>
  <td class="cell c3"><pre>-(3*w-4*v+9*u)*(3*w+4*v-u)</pre></td>
  <td class="cell c4"><pre>v</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATFacForm_true.</td>
</tr>
<tr class="pass">
  <td class="cell c0">FacForm</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>-6*k*(4*b-k-1)</pre></td>
  <td class="cell c3"><pre>6*k*(1+k-4*b)</pre></td>
  <td class="cell c4"><pre>k</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATFacForm_default_true.</td>
</tr>
<tr class="pass">
  <td class="cell c0">FacForm</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>-2*3*k*(4*b-k-1)</pre></td>
  <td class="cell c3"><pre>6*k*(1+k-4*b)</pre></td>
  <td class="cell c4"><pre>k</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATFacForm_true.</td>
</tr>
<tr class="pass">
  <td class="cell c0">FacForm</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>-(6*k*(4*b-k-1))</pre></td>
  <td class="cell c3"><pre>6*k*(1+k-4*b)</pre></td>
  <td class="cell c4"><pre>k</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATFacForm_default_true.</td>
</tr>
<tr class="pass">
  <td class="cell c0">FacForm</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>-(6*a*(4*b-a-1))</pre></td>
  <td class="cell c3"><pre>6*a*(1+a-4*b)</pre></td>
  <td class="cell c4"><pre>a</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATFacForm_true.</td>
</tr>
<tr class="pass">
  <td class="cell c0">FacForm</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>-(6*a*(4*b-a-1))</pre></td>
  <td class="cell c3"><pre>6*a*(-(4*b)+a+1)</pre></td>
  <td class="cell c4"><pre>a</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATFacForm_true.</td>
</tr>
<tr class="pass">
  <td class="cell c0">FacForm</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>x*(x-4+4/x)</pre></td>
  <td class="cell c3"><pre>x^2-4*x+4</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATFacForm_notfactored.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer is not factored. You could still do some more work on the term <span class="filter_mathjaxloader_equation"><span class="nolink">\(x-4+\frac{4}{x}\)</span></span>. This term is expected to be a polynomial, but is not.</td></td>
</tr>
<tr class="notes">
  <td class="cell c0"><td colspan="6">These are delicate cases!</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">FacForm</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>(2-x)*(3-x)</pre></td>
  <td class="cell c3"><pre>(x-2)*(x-3)</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATFacForm_true.</td>
</tr>
<tr class="pass">
  <td class="cell c0">FacForm</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>(1-x)^2</pre></td>
  <td class="cell c3"><pre>(x-1)^2</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATFacForm_true.</td>
</tr>
<tr class="pass">
  <td class="cell c0">FacForm</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>(1-x)*(1-x)</pre></td>
  <td class="cell c3"><pre>(x-1)^2</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATFacForm_true.</td>
</tr>
<tr class="pass">
  <td class="cell c0">FacForm</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>-(1-x)^2</pre></td>
  <td class="cell c3"><pre>-(x-1)^2</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATFacForm_true.</td>
</tr>
<tr class="pass">
  <td class="cell c0">FacForm</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>(1-x)^2</pre></td>
  <td class="cell c3"><pre>(x-1)^2</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATFacForm_true.</td>
</tr>
<tr class="pass">
  <td class="cell c0">FacForm</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>4*(1-x/2)^2</pre></td>
  <td class="cell c3"><pre>(x-2)^2</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATFacForm_default_true.</td>
</tr>
<tr class="pass">
  <td class="cell c0">FacForm</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>-3*(x-4)*(x+1)</pre></td>
  <td class="cell c3"><pre>-3*x^2+9*x+12</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATFacForm_true.</td>
</tr>
<tr class="pass">
  <td class="cell c0">FacForm</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>3*(-x+4)*(x+1)</pre></td>
  <td class="cell c3"><pre>-3*x^2+9*x+12</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATFacForm_true.</td>
</tr>
<tr class="pass">
  <td class="cell c0">FacForm</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>3*(4-x)*(x+1)</pre></td>
  <td class="cell c3"><pre>-3*x^2+9*x+12</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATFacForm_true.</td>
</tr>
<tr class="notes">
  <td class="cell c0"><td colspan="6">Cubics</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">FacForm</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>(x-1)*(x^2+x+1)</pre></td>
  <td class="cell c3"><pre>x^3-1</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATFacForm_true.</td>
</tr>
<tr class="pass">
  <td class="cell c0">FacForm</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>x^3-x+1</pre></td>
  <td class="cell c3"><pre>x^3-x+1</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATFacForm_true.</td>
</tr>
<tr class="pass">
  <td class="cell c0">FacForm</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>7*x^3-7*x+7</pre></td>
  <td class="cell c3"><pre>7*(x^3-x+1)</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATFacForm_notfactored.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer is not factored. You need to take out a common factor.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">FacForm</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>(1-x)*(2-x)*(3-x)</pre></td>
  <td class="cell c3"><pre>-x^3+6*x^2-11*x+6</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATFacForm_true.</td>
</tr>
<tr class="pass">
  <td class="cell c0">FacForm</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>(2-x)*(2-x)*(3-x)</pre></td>
  <td class="cell c3"><pre>-x^3+7*x^2-16*x+12</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATFacForm_true.</td>
</tr>
<tr class="pass">
  <td class="cell c0">FacForm</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>(2-x)^2*(3-x)</pre></td>
  <td class="cell c3"><pre>-x^3+7*x^2-16*x+12</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATFacForm_true.</td>
</tr>
<tr class="pass">
  <td class="cell c0">FacForm</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>(x^2-4*x+4)*(3-x)</pre></td>
  <td class="cell c3"><pre>-x^3+7*x^2-16*x+12</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATFacForm_notfactored.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer is not factored. You could still do some more work on the term <span class="filter_mathjaxloader_equation"><span class="nolink">\(x^2-4\cdot x+4\)</span></span>.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">FacForm</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>(x^2-3*x+2)*(3-x)</pre></td>
  <td class="cell c3"><pre>-x^3+6*x^2-11*x+6</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATFacForm_notfactored.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer is not factored. You could still do some more work on the term <span class="filter_mathjaxloader_equation"><span class="nolink">\(x^2-3\cdot x+2\)</span></span>.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">FacForm</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>3*y^3-6*y^2-24*y</pre></td>
  <td class="cell c3"><pre>3*(y-4)*y*(y+2)</pre></td>
  <td class="cell c4"><pre>y</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATFacForm_notfactored.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer is not factored. You need to take out a common factor.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">FacForm</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>3*(y^3-2*y^2-8*y)</pre></td>
  <td class="cell c3"><pre>3*(y-4)*y*(y+2)</pre></td>
  <td class="cell c4"><pre>y</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATFacForm_notfactored.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer is not factored. You could still do some more work on the term <span class="filter_mathjaxloader_equation"><span class="nolink">\(y^3-2\cdot y^2-8\cdot y\)</span></span>.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">FacForm</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>3*y*(y^2-2*y-8)</pre></td>
  <td class="cell c3"><pre>3*(y-4)*y*(y+2)</pre></td>
  <td class="cell c4"><pre>y</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATFacForm_notfactored.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer is not factored. You could still do some more work on the term <span class="filter_mathjaxloader_equation"><span class="nolink">\(y^2-2\cdot y-8\)</span></span>.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">FacForm</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>3*(y^2-4*y)*(y+2)</pre></td>
  <td class="cell c3"><pre>3*(y-4)*y*(y+2)</pre></td>
  <td class="cell c4"><pre>y</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATFacForm_notfactored.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer is not factored. You could still do some more work on the term <span class="filter_mathjaxloader_equation"><span class="nolink">\(y^2-4\cdot y\)</span></span>.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">FacForm</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>(y-4)*y*(3*y+6)</pre></td>
  <td class="cell c3"><pre>3*(y-4)*y*(y+2)</pre></td>
  <td class="cell c4"><pre>y</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATFacForm_notfactored.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer is not factored. You could still do some more work on the term <span class="filter_mathjaxloader_equation"><span class="nolink">\(3\cdot y+6\)</span></span>. You need to take out a common factor.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">FacForm</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>(a-x)^6000</pre></td>
  <td class="cell c3"><pre>(a-x)^6000</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATFacForm_true.</td>
</tr>
<tr class="pass">
  <td class="cell c0">FacForm</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>(x-a)^6000</pre></td>
  <td class="cell c3"><pre>(a-x)^6000</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATFacForm_true.</td>
</tr>
<tr class="notes">
  <td class="cell c0"><td colspan="6">Needs flattening</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">FacForm</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>2*a*(a*b-1)</pre></td>
  <td class="cell c3"><pre>2*a*(a*b-1)</pre></td>
  <td class="cell c4"><pre>a</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATFacForm_true.</td>
</tr>
<tr class="pass">
  <td class="cell c0">FacForm</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>(2*a)*(a*b-1)</pre></td>
  <td class="cell c3"><pre>2*a*(a*b-1)</pre></td>
  <td class="cell c4"><pre>a</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATFacForm_true.</td>
</tr>
<tr class="pass">
  <td class="cell c0">FacForm</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>3*x*(7*y-3)*(7*y+3)</pre></td>
  <td class="cell c3"><pre>3*x*(7*y-3)*(7*y+3)</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATFacForm_true.</td>
</tr>
<tr class="pass">
  <td class="cell c0">FacForm</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>3*x*(7*y-3)*(7*y+3)</pre></td>
  <td class="cell c3"><pre>3*x*(7*y-3)*(7*y+3)</pre></td>
  <td class="cell c4"><pre>y</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATFacForm_true.</td>
</tr>
<tr class="notes">
  <td class="cell c0"><td colspan="6">Not polynomials in a variable</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">FacForm</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>(sin(x)+1)*(sin(x)-1)</pre></td>
  <td class="cell c3"><pre>sin(x)^2-1</pre></td>
  <td class="cell c4"><pre>sin(x)</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATFacForm_true.</td>
</tr>
<tr class="pass">
  <td class="cell c0">FacForm</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>(cos(t)-sqrt(2))^2</pre></td>
  <td class="cell c3"><pre>cos(t)^2-2*sqrt(2)*cos(t)+2</pre></td>
  <td class="cell c4"><pre>cos(t)</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATFacForm_true.</td>
</tr>
<tr class="pass">
  <td class="cell c0">FacForm</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>7</pre></td>
  <td class="cell c3"><pre>7</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATFacForm_int_true.</td>
</tr>
<tr class="notes">
  <td class="cell c0"><td colspan="6">Factors over other fields</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">FacForm</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>24*(x-1/4)</pre></td>
  <td class="cell c3"><pre>24*x-6</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATFacForm_default_true.</td>
</tr>
<tr class="pass">
  <td class="cell c0">FacForm</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>(x-sqrt(2))*(x+sqrt(2))</pre></td>
  <td class="cell c3"><pre>x^2-2</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATFacForm_true.</td>
</tr>
<tr class="pass">
  <td class="cell c0">FacForm</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>x^2-2</pre></td>
  <td class="cell c3"><pre>x^2-2</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATFacForm_true.</td>
</tr>
<tr class="pass">
  <td class="cell c0">FacForm</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>(%i*x-2*%i)</pre></td>
  <td class="cell c3"><pre>%i*(x-2)</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATFacForm_notfactored.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer is not factored.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">FacForm</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>%i*(x-2)</pre></td>
  <td class="cell c3"><pre>(%i*x-2*%i)</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATFacForm_true.</td>
</tr>
<tr class="pass">
  <td class="cell c0">FacForm</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>(x-%i)*(x+%i)</pre></td>
  <td class="cell c3"><pre>x^2+1</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATFacForm_true.</td>
</tr>
<tr class="pass">
  <td class="cell c0">FacForm</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>(x-1)*(x+(1+sqrt(3)*%i)/2)*(x+
(1-sqrt(3)*%i)/2)</pre></td>
  <td class="cell c3"><pre>x^3-1</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATFacForm_default_true.</td>
</tr></tbody></table></div>
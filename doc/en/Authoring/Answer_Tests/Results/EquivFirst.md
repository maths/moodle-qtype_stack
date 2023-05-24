# EquivFirst: Answer test results

This page exposes the results of running answer tests on STACK test cases.  This page is automatically generated from the STACK unit tests and is designed to show question authors what answer tests actually do.  This includes cases where answer tests currentl fail, which gives a negative expected mark.  Comments and further test cases are very welcome.



<h2>EquivFirst</h2><div class="no-overflow"><table class="flexible table table-striped table-hover generaltable generalbox stacktestsuite"><thead><tr><th class="header c0" scope="col">Test<div class="commands"></div></th><th class="header c1" scope="col">?<div class="commands"></div></th><th class="header c2" scope="col">Student response<div class="commands"></div></th><th class="header c3" scope="col">Teacher answer<div class="commands"></div></th><th class="header c4" scope="col">Opt<div class="commands"></div></th><th class="header c5" scope="col">Mark<div class="commands"></div></th><th class="header c6" scope="col">Answer note<div class="commands"></div></th>
</tr></thead><tbody>
<tr class="expectedfail">
  <td class="cell c0">EquivFirst</td>
  <td class="cell c1"><span style="color:orange;"><i class="fa fa-adjust"></i></span></td>
  <td class="cell c2"><pre>x</pre></td>
  <td class="cell c3"><pre>[x^2=4,x=2 or x=-2]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">-1</td>
  <td class="cell c6">ATEquivFirst_SA_not_list.</td>
</tr>
<tr class="expectedfail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The first argument to the Equiv answer test should be a list, but the test failed. Please contact your teacher.</td></td>
</tr>
<tr class="expectedfail">
  <td class="cell c0">EquivFirst</td>
  <td class="cell c1"><span style="color:orange;"><i class="fa fa-adjust"></i></span></td>
  <td class="cell c2"><pre>[x^2=4,x=2 or x=-2]</pre></td>
  <td class="cell c3"><pre>x</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">-1</td>
  <td class="cell c6">ATEquivFirst_SB_not_list.</td>
</tr>
<tr class="expectedfail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The second argument to the Equiv answer test should be a list, but the test failed. Please contact your teacher.</td></td>
</tr>
<tr class="expectedfail">
  <td class="cell c0">EquivFirst</td>
  <td class="cell c1"><span style="color:orange;"><i class="fa fa-adjust"></i></span></td>
  <td class="cell c2"><pre>[1/0]</pre></td>
  <td class="cell c3"><pre>[x^2=4,x=2 or x=-2]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">-1</td>
  <td class="cell c6">ATEquivFirst_STACKERROR_SAns.</td>
</tr>
<tr class="expectedfail">
  <td class="cell c0">EquivFirst</td>
  <td class="cell c1"><span style="color:orange;"><i class="fa fa-adjust"></i></span></td>
  <td class="cell c2"><pre>[x^2=4,x=2 or x=-2]</pre></td>
  <td class="cell c3"><pre>[1/0]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">-1</td>
  <td class="cell c6">ATEquivFirst_STACKERROR_TAns.</td>
</tr>
<tr class="pass">
  <td class="cell c0">EquivFirst</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[x^2=4,x=2 or x=-2]</pre></td>
  <td class="cell c3"><pre>[x^2=4,x=2 or x=-2]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(EMPTYCHAR, EQUIVCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &x^2=4& \cr \color{green}{\Leftrightarrow}&x=2\,{\mbox{ or }}\, x=-2& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">EquivFirst</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[x^2=9,x=3 or x=-3]</pre></td>
  <td class="cell c3"><pre>[x^2=4,x=2 or x=-2]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATEquivFirst_SA_wrong_start</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The first line in your argument must be "<span class="filter_mathjaxloader_equation"><span class="nolink">\(x^2=4\)</span></span>".</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">EquivFirst</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[x^2=4,x=2]</pre></td>
  <td class="cell c3"><pre>[x^2=4,x=2 or x=-2]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">(EMPTYCHAR,IMPLIEDCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &x^2=4& \cr \color{red}{\Leftarrow}&x=2& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">EquivFirst</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[x^2=4,x^2-4=0,(x-2)*(x+2)=0,x
=2 or x=-2]</pre></td>
  <td class="cell c3"><pre>[x^2=4,x=2 or x=-2]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(EMPTYCHAR, EQUIVCHAR, EQUIVCHAR, EQUIVCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &x^2=4& \cr \color{green}{\Leftrightarrow}&x^2-4=0& \cr \color{green}{\Leftrightarrow}&\left(x-2\right)\cdot \left(x+2\right)=0& \cr \color{green}{\Leftrightarrow}&x=2\,{\mbox{ or }}\, x=-2& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">EquivFirst</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[x^2=4,x= #pm#2, x=2 or x=-2]</pre></td>
  <td class="cell c3"><pre>[x^2=4,x=2 or x=-2]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(EMPTYCHAR, EQUIVCHAR, EQUIVCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &x^2=4& \cr \color{green}{\Leftrightarrow}&x= \pm 2& \cr \color{green}{\Leftrightarrow}&x=2\,{\mbox{ or }}\, x=-2& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">EquivFirst</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[x^2-6*x+9=0,x=3]</pre></td>
  <td class="cell c3"><pre>[x^2-6*x+9=0,x=3]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(EMPTYCHAR,SAMEROOTS)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &x^2-6\cdot x+9=0& \cr \color{green}{\mbox{(Same roots)}}&x=3& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">EquivFirst</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[x^2=4,x=2]</pre></td>
  <td class="cell c3"><pre>[x^2=4,x=2]</pre></td>
  <td class="cell c4"><pre>[assumepos]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(ASSUMEPOSVARS, EQUIVCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll}\color{blue}{\mbox{Assume +ve vars}}&x^2=4& \cr \color{green}{\Leftrightarrow}&x=2& \cr \end{array}\]</td></td>
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
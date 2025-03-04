# SubstEquiv: Answer test results

This page exposes the results of running answer tests on STACK test cases.  This page is automatically generated from the STACK unit tests and is designed to show question authors what answer tests actually do.  This includes cases where answer tests currentl fail, which gives a negative expected mark.  Comments and further test cases are very welcome.



<h2>SubstEquiv</h2><div class="no-overflow"><table class="flexible table table-striped table-hover generaltable generalbox stacktestsuite"><thead><tr><th class="header c0" scope="col">Test<div class="commands"></div></th><th class="header c1" scope="col">?<div class="commands"></div></th><th class="header c2" scope="col">Student response<div class="commands"></div></th><th class="header c3" scope="col">Teacher answer<div class="commands"></div></th><th class="header c4" scope="col">Opt<div class="commands"></div></th><th class="header c5" scope="col">Mark<div class="commands"></div></th><th class="header c6" scope="col">Answer note<div class="commands"></div></th>
</tr></thead><tbody>
<tr class="expectedfail">
  <td class="cell c0">SubstEquiv</td>
  <td class="cell c1"><span style="color:orange;"><i class="fa fa-adjust"></i></span></td>
  <td class="cell c2"><pre>1/0</pre></td>
  <td class="cell c3"><pre>x^2-2*x+1</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">-1</td>
  <td class="cell c6">ATSubstEquiv_STACKERROR_SAns.</td>
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
  <td class="cell c0">SubstEquiv</td>
  <td class="cell c1"><span style="color:orange;"><i class="fa fa-adjust"></i></span></td>
  <td class="cell c2"><pre>x^2</pre></td>
  <td class="cell c3"><pre>x^2-2*x+1</pre></td>
  <td class="cell c4"><pre>[1/0]</pre></td>
  <td class="cell c5">-1</td>
  <td class="cell c6">ATSubstEquiv_STACKERROR_Opt.</td>
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
  <td class="cell c0">SubstEquiv</td>
  <td class="cell c1"><span style="color:orange;"><i class="fa fa-adjust"></i></span></td>
  <td class="cell c2"><pre>x^2</pre></td>
  <td class="cell c3"><pre>x^2-2*x+1</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">-1</td>
  <td class="cell c6">ATSubstEquiv_Opt_List.</td>
</tr>
<tr class="expectedfail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The option to this answer test must be a list. This is an error. Please contact your teacher.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">SubstEquiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>x^2+1</pre></td>
  <td class="cell c3"><pre>x^2+1</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">SubstEquiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>x^2+1</pre></td>
  <td class="cell c3"><pre>x^3+1</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">SubstEquiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>x^2+1</pre></td>
  <td class="cell c3"><pre>x^3+1</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">SubstEquiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>X^2+1</pre></td>
  <td class="cell c3"><pre>x^2+1</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATSubstEquiv_Subst [X = x].</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer would be correct if you used the following substitution of variables. <span class="filter_mathjaxloader_equation"><span class="nolink">\[\left[ X=x \right] \]</span></span></td></td>
</tr>
<tr class="pass">
  <td class="cell c0">SubstEquiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>x^2+y</pre></td>
  <td class="cell c3"><pre>a^2+b</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATSubstEquiv_Subst [x = a,y = b].</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer would be correct if you used the following substitution of variables. <span class="filter_mathjaxloader_equation"><span class="nolink">\[\left[ x=a , y=b \right] \]</span></span></td></td>
</tr>
<tr class="pass">
  <td class="cell c0">SubstEquiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>x^2+y/z</pre></td>
  <td class="cell c3"><pre>a^2+c/b</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATSubstEquiv_Subst [x = a,y = c,z = b].</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer would be correct if you used the following substitution of variables. <span class="filter_mathjaxloader_equation"><span class="nolink">\[\left[ x=a , y=c , z=b \right] \]</span></span></td></td>
</tr>
<tr class="pass">
  <td class="cell c0">SubstEquiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>y=x^2</pre></td>
  <td class="cell c3"><pre>a^2=b</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATSubstEquiv_Subst [x = a,y = b].</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer would be correct if you used the following substitution of variables. <span class="filter_mathjaxloader_equation"><span class="nolink">\[\left[ x=a , y=b \right] \]</span></span></td></td>
</tr>
<tr class="pass">
  <td class="cell c0">SubstEquiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>{x=1,y=2}</pre></td>
  <td class="cell c3"><pre>{x=2,y=1}</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATSubstEquiv_Subst [x = y,y = x].</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer would be correct if you used the following substitution of variables. <span class="filter_mathjaxloader_equation"><span class="nolink">\[\left[ x=y , y=x \right] \]</span></span></td></td>
</tr>
<tr class="notes">
  <td class="cell c0"><td colspan="6">Where a variable is also a function name.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">SubstEquiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>cos(a*x)/(x*(ln(x)))</pre></td>
  <td class="cell c3"><pre>cos(a*y)/(y*(ln(y)))</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATSubstEquiv_Subst [a = a,x = y].</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer would be correct if you used the following substitution of variables. <span class="filter_mathjaxloader_equation"><span class="nolink">\[\left[ a=a , x=y \right] \]</span></span></td></td>
</tr>
<tr class="pass">
  <td class="cell c0">SubstEquiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>cos(a*x)/(x*(ln(x)))</pre></td>
  <td class="cell c3"><pre>cos(x*a)/(a*(ln(a)))</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATSubstEquiv_Subst [a = x,x = a].</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer would be correct if you used the following substitution of variables. <span class="filter_mathjaxloader_equation"><span class="nolink">\[\left[ a=x , x=a \right] \]</span></span></td></td>
</tr>
<tr class="pass">
  <td class="cell c0">SubstEquiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>cos(a*x)/(x*(ln(x)))</pre></td>
  <td class="cell c3"><pre>cos(a*x)/(x(ln(x)))</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">SubstEquiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>cos(a*x)/(x*(ln(x)))</pre></td>
  <td class="cell c3"><pre>cos(a*y)/(y(ln(y)))</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">SubstEquiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>x+1&gt;y</pre></td>
  <td class="cell c3"><pre>y+1&gt;x</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATSubstEquiv_Subst [x = y,y = x].</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer would be correct if you used the following substitution of variables. <span class="filter_mathjaxloader_equation"><span class="nolink">\[\left[ x=y , y=x \right] \]</span></span></td></td>
</tr>
<tr class="pass">
  <td class="cell c0">SubstEquiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>x+1&gt;y</pre></td>
  <td class="cell c3"><pre>x&lt;y+1</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATSubstEquiv_Subst [x = y,y = x].</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer would be correct if you used the following substitution of variables. <span class="filter_mathjaxloader_equation"><span class="nolink">\[\left[ x=y , y=x \right] \]</span></span></td></td>
</tr>
<tr class="notes">
  <td class="cell c0"><td colspan="6">Matrices</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">SubstEquiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>matrix([1,A^2+A+1],[2,0])</pre></td>
  <td class="cell c3"><pre>matrix([1,x^2+x+1],[2,0])</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATSubstEquiv_Subst [A = x].</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer would be correct if you used the following substitution of variables. <span class="filter_mathjaxloader_equation"><span class="nolink">\[\left[ A=x \right] \]</span></span></td></td>
</tr>
<tr class="pass">
  <td class="cell c0">SubstEquiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>matrix([B,A^2+A+1],[2,C])</pre></td>
  <td class="cell c3"><pre>matrix([y,x^2+x+1],[2,z])</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATSubstEquiv_Subst [A = x,B = y,C = z].</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer would be correct if you used the following substitution of variables. <span class="filter_mathjaxloader_equation"><span class="nolink">\[\left[ A=x , B=y , C=z \right] \]</span></span></td></td>
</tr>
<tr class="pass">
  <td class="cell c0">SubstEquiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>matrix([B,A^2+A+1],[2,C])</pre></td>
  <td class="cell c3"><pre>matrix([y,x^2+x+1],[2,x])</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATMatrix_wrongentries.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The entries underlined in red below are those that are incorrect. <span class="filter_mathjaxloader_equation"><span class="nolink">\[ \left[\begin{array}{cc} {\color{red}{\underline{B}}} & {\color{red}{\underline{A^2+A+1}}} \\ 2 & {\color{red}{\underline{C}}} \end{array}\right]\]</span></span></td></td>
</tr>
<tr class="notes">
  <td class="cell c0"><td colspan="6">Lists</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">SubstEquiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[x^2+1,x^2]</pre></td>
  <td class="cell c3"><pre>[A^2+1,A^2]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATSubstEquiv_Subst [x = A].</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer would be correct if you used the following substitution of variables. <span class="filter_mathjaxloader_equation"><span class="nolink">\[\left[ x=A \right] \]</span></span></td></td>
</tr>
<tr class="pass">
  <td class="cell c0">SubstEquiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[x^2-1,x^2]</pre></td>
  <td class="cell c3"><pre>[A^2+1,A^2]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">(ATList_wrongentries 1, 2).</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The entries underlined in red below are those that are incorrect. <span class="filter_mathjaxloader_equation"><span class="nolink">\[\left[ {\color{red}{\underline{x^2-1}}} , {\color{red}{\underline{x ^2}}} \right] \]</span></span></td></td>
</tr>
<tr class="pass">
  <td class="cell c0">SubstEquiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[A,B,C]</pre></td>
  <td class="cell c3"><pre>[B,C,A]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATSubstEquiv_Subst [A = B,B = C,C = A].</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer would be correct if you used the following substitution of variables. <span class="filter_mathjaxloader_equation"><span class="nolink">\[\left[ A=B , B=C , C=A \right] \]</span></span></td></td>
</tr>
<tr class="pass">
  <td class="cell c0">SubstEquiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[A,B,C]</pre></td>
  <td class="cell c3"><pre>[B,B,A]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">(ATList_wrongentries 1, 3).</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The entries underlined in red below are those that are incorrect. <span class="filter_mathjaxloader_equation"><span class="nolink">\[\left[ {\color{red}{\underline{A}}} , B , {\color{red}{\underline{C }}} \right] \]</span></span></td></td>
</tr>
<tr class="pass">
  <td class="cell c0">SubstEquiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[1,[A,B],C]</pre></td>
  <td class="cell c3"><pre>[1,[a,b],C]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATSubstEquiv_Subst [A = a,B = b,C = C].</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer would be correct if you used the following substitution of variables. <span class="filter_mathjaxloader_equation"><span class="nolink">\[\left[ A=a , B=b , C=C \right] \]</span></span></td></td>
</tr>
<tr class="notes">
  <td class="cell c0"><td colspan="6">Sets</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">SubstEquiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>{x^2+1,x^2}</pre></td>
  <td class="cell c3"><pre>{A^2+1,A^2}</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATSubstEquiv_Subst [x = A].</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer would be correct if you used the following substitution of variables. <span class="filter_mathjaxloader_equation"><span class="nolink">\[\left[ x=A \right] \]</span></span></td></td>
</tr>
<tr class="pass">
  <td class="cell c0">SubstEquiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>{x^2-1,x^2}</pre></td>
  <td class="cell c3"><pre>{A^2+1,A^2}</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATSet_wrongentries.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The following entries are incorrect, although they may appear in a simplified form from that which you actually entered. <span class="filter_mathjaxloader_equation"><span class="nolink">\[\left \{x^2-1 , x^2 \right \}\]</span></span></td></td>
</tr>
<tr class="pass">
  <td class="cell c0">SubstEquiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>{A+1,B^2,C}</pre></td>
  <td class="cell c3"><pre>{B,C+1,A^2}</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATSubstEquiv_Subst [A = C,B = A,C = B].</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer would be correct if you used the following substitution of variables. <span class="filter_mathjaxloader_equation"><span class="nolink">\[\left[ A=C , B=A , C=B \right] \]</span></span></td></td>
</tr>
<tr class="pass">
  <td class="cell c0">SubstEquiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>{1,{A,B},C}</pre></td>
  <td class="cell c3"><pre>{1,{a,b},C}</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATSubstEquiv_Subst [A = a,B = b,C = C].</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer would be correct if you used the following substitution of variables. <span class="filter_mathjaxloader_equation"><span class="nolink">\[\left[ A=a , B=b , C=C \right] \]</span></span></td></td>
</tr>
<tr class="pass">
  <td class="cell c0">SubstEquiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>y=A+B</pre></td>
  <td class="cell c3"><pre>x=a+b</pre></td>
  <td class="cell c4"><pre>[x]</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATEquation_default</td>
</tr>
<tr class="pass">
  <td class="cell c0">SubstEquiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>y=A+B</pre></td>
  <td class="cell c3"><pre>x=a+b</pre></td>
  <td class="cell c4"><pre>[z]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATSubstEquiv_Subst [A = a,B = b,y = x].</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer would be correct if you used the following substitution of variables. <span class="filter_mathjaxloader_equation"><span class="nolink">\[\left[ A=a , B=b , y=x \right] \]</span></span></td></td>
</tr>
<tr class="pass">
  <td class="cell c0">SubstEquiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>A*cos(t)+B*sin(t)</pre></td>
  <td class="cell c3"><pre>P*cos(t)+Q*sin(t)</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATSubstEquiv_Subst [A = P,B = Q,t = t].</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer would be correct if you used the following substitution of variables. <span class="filter_mathjaxloader_equation"><span class="nolink">\[\left[ A=P , B=Q , t=t \right] \]</span></span></td></td>
</tr>
<tr class="pass">
  <td class="cell c0">SubstEquiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>A*cos(t)+B*sin(t)</pre></td>
  <td class="cell c3"><pre>P*cos(x)+Q*sin(x)</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATSubstEquiv_Subst [A = P,B = Q,t = x].</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer would be correct if you used the following substitution of variables. <span class="filter_mathjaxloader_equation"><span class="nolink">\[\left[ A=P , B=Q , t=x \right] \]</span></span></td></td>
</tr>
<tr class="notes">
  <td class="cell c0"><td colspan="6">Fix some variables.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">SubstEquiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>A*cos(x)+B*sin(x)</pre></td>
  <td class="cell c3"><pre>P*cos(x)+Q*sin(x)</pre></td>
  <td class="cell c4"><pre>[x]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATSubstEquiv_Subst [A = P,B = Q].</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer would be correct if you used the following substitution of variables. <span class="filter_mathjaxloader_equation"><span class="nolink">\[\left[ A=P , B=Q \right] \]</span></span></td></td>
</tr>
<tr class="pass">
  <td class="cell c0">SubstEquiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>A*cos(t)+B*sin(t)</pre></td>
  <td class="cell c3"><pre>P*cos(x)+Q*sin(x)</pre></td>
  <td class="cell c4"><pre>[x]</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">SubstEquiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>A*cos(t)+B*sin(t)</pre></td>
  <td class="cell c3"><pre>P*cos(x)+Q*sin(x)</pre></td>
  <td class="cell c4"><pre>[t]</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">SubstEquiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>A*cos(t)+B*sin(t)</pre></td>
  <td class="cell c3"><pre>P*cos(x)+Q*sin(x)</pre></td>
  <td class="cell c4"><pre>[z]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATSubstEquiv_Subst [A = P,B = Q,t = x].</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer would be correct if you used the following substitution of variables. <span class="filter_mathjaxloader_equation"><span class="nolink">\[\left[ A=P , B=Q , t=x \right] \]</span></span></td></td>
</tr>
<tr class="pass">
  <td class="cell c0">SubstEquiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>A*cos(t)*e^x+B*sin(t)*e^x+C*si
n(2*x)+D*cos(2*x)</pre></td>
  <td class="cell c3"><pre>P*cos(t)*e^x+Q*sin(t)*e^x+R*si
n(2*x)+S*cos(2*x)</pre></td>
  <td class="cell c4"><pre>[x,t]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATSubstEquiv_Subst [A = P,B = Q,C = R,D = S].</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer would be correct if you used the following substitution of variables. <span class="filter_mathjaxloader_equation"><span class="nolink">\[\left[ A=P , B=Q , C=R , D=S \right] \]</span></span></td></td>
</tr>
<tr class="pass">
  <td class="cell c0">SubstEquiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>sqrt(2*g*y)</pre></td>
  <td class="cell c3"><pre>sqrt(2*g*x)</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATSubstEquiv_Subst [g = g,y = x].</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer would be correct if you used the following substitution of variables. <span class="filter_mathjaxloader_equation"><span class="nolink">\[\left[ g=g , y=x \right] \]</span></span></td></td>
</tr>
<tr class="pass">
  <td class="cell c0">SubstEquiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>sqrt(2*g*y)</pre></td>
  <td class="cell c3"><pre>sqrt(2*g*x)</pre></td>
  <td class="cell c4"><pre>[g]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATSubstEquiv_Subst [y = x].</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer would be correct if you used the following substitution of variables. <span class="filter_mathjaxloader_equation"><span class="nolink">\[\left[ y=x \right] \]</span></span></td></td>
</tr>
<tr class="pass">
  <td class="cell c0">SubstEquiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>C1*%e^x*sin(4*x)+C2*%e^x*cos(4
*x)+C4*x*%e^-x+C3*%e^-x</pre></td>
  <td class="cell c3"><pre>e^(x)*A*cos(4*x)+B*e^(x)*sin(4
*x)+C*e^(-x)+D*x*e^(-x)</pre></td>
  <td class="cell c4"><pre>[x]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATSubstEquiv_Subst [C1 = B,C2 = A,C3 = C,C4 = D].</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer would be correct if you used the following substitution of variables. <span class="filter_mathjaxloader_equation"><span class="nolink">\[\left[ C_{1}=B , C_{2}=A , C_{3}=C , C_{4}=D \right] \]</span></span></td></td>
</tr>
<tr class="pass">
  <td class="cell c0">SubstEquiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>C1*%e^x*sin(4*x)+C2*%e^x*cos(4
*x)+C4*x*%e^-x+C3*%e^-x</pre></td>
  <td class="cell c3"><pre>C4*x*e^(-x)+e^(x)*C1*cos(4*x)+
C2*e^(x)*sin(4*x)+C3*e^(-x)</pre></td>
  <td class="cell c4"><pre>[x]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATSubstEquiv_Subst [C1 = C2,C2 = C1,C3 = C3,C4 = C4].</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer would be correct if you used the following substitution of variables. <span class="filter_mathjaxloader_equation"><span class="nolink">\[\left[ C_{1}=C_{2} , C_{2}=C_{1} , C_{3}=C_{3} , C_{4}=C_{4} \right] \]</span></span></td></td>
</tr>
<tr class="pass">
  <td class="cell c0">SubstEquiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>C1*%e^x*sin(4*x)+C2*%e^x*cos(4
*x)+C4*x*%e^-x+C3*%e^-x</pre></td>
  <td class="cell c3"><pre>A*x*e^(-x)+e^(x)*B*cos(4*x)+C*
e^(x)*sin(4*x)+D*e^(-x)</pre></td>
  <td class="cell c4"><pre>[x]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATSubstEquiv_Subst [C1 = C,C2 = B,C3 = D,C4 = A].</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer would be correct if you used the following substitution of variables. <span class="filter_mathjaxloader_equation"><span class="nolink">\[\left[ C_{1}=C , C_{2}=B , C_{3}=D , C_{4}=A \right] \]</span></span></td></td>
</tr>
<tr class="pass">
  <td class="cell c0">SubstEquiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>C1*%e^x*sin(4*x)+C2*%e^x*cos(4
*x)+C4*x*%e^-x+C3*%e^-x</pre></td>
  <td class="cell c3"><pre>e^(x)*C1*cos(4*x)+C2*e^(x)*sin
(4*x)+C3*e^(-x)+C4*x*e^(-x)</pre></td>
  <td class="cell c4"><pre>[x]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATSubstEquiv_Subst [C1 = C2,C2 = C1,C3 = C3,C4 = C4].</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer would be correct if you used the following substitution of variables. <span class="filter_mathjaxloader_equation"><span class="nolink">\[\left[ C_{1}=C_{2} , C_{2}=C_{1} , C_{3}=C_{3} , C_{4}=C_{4} \right] \]</span></span></td></td>
</tr></tbody></table></div>
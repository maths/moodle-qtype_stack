# Equiv: Answer test results

This page exposes the results of running answer tests on STACK test cases.  This page is automatically generated from the STACK unit tests and is designed to show question authors what answer tests actually do.  This includes cases where answer tests currentl fail, which gives a negative expected mark.  Comments and further test cases are very welcome.



<h2>Equiv</h2><div class="no-overflow"><table class="flexible table table-striped table-hover generaltable generalbox stacktestsuite"><thead><tr><th class="header c0" scope="col">Test<div class="commands"></div></th><th class="header c1" scope="col">?<div class="commands"></div></th><th class="header c2" scope="col">Student response<div class="commands"></div></th><th class="header c3" scope="col">Teacher answer<div class="commands"></div></th><th class="header c4" scope="col">Opt<div class="commands"></div></th><th class="header c5" scope="col">Mark<div class="commands"></div></th><th class="header c6" scope="col">Answer note<div class="commands"></div></th>
</tr></thead><tbody>
<tr class="expectedfail">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:orange;"><i class="fa fa-adjust"></i></span></td>
  <td class="cell c2"><pre>x</pre></td>
  <td class="cell c3"><pre>[x^2=4,x=2 or x=-2]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">-1</td>
  <td class="cell c6">ATEquiv_SA_not_list.</td>
</tr>
<tr class="expectedfail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The first argument to the Equiv answer test should be a list, but the test failed. Please contact your teacher.</td></td>
</tr>
<tr class="expectedfail">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:orange;"><i class="fa fa-adjust"></i></span></td>
  <td class="cell c2"><pre>[x^2=4,x=2 or x=-2]</pre></td>
  <td class="cell c3"><pre>x</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">-1</td>
  <td class="cell c6">ATEquiv_SB_not_list.</td>
</tr>
<tr class="expectedfail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The second argument to the Equiv answer test should be a list, but the test failed. Please contact your teacher.</td></td>
</tr>
<tr class="expectedfail">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:orange;"><i class="fa fa-adjust"></i></span></td>
  <td class="cell c2"><pre>[1/0]</pre></td>
  <td class="cell c3"><pre>[x^2=4,x=2 or x=-2]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">-1</td>
  <td class="cell c6">ATEquiv_STACKERROR_SAns.</td>
</tr>
<tr class="expectedfail">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:orange;"><i class="fa fa-adjust"></i></span></td>
  <td class="cell c2"><pre>[x^2=4,x=2 or x=-2]</pre></td>
  <td class="cell c3"><pre>[1/0]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">-1</td>
  <td class="cell c6">ATEquiv_STACKERROR_TAns.</td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
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
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[x^2=4,x=#pm#2,x=2 and x=-2]</pre></td>
  <td class="cell c3"><pre>[x^2=4,x=2 or x=-2]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">(EMPTYCHAR, EQUIVCHAR,ANDOR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &x^2=4& \cr \color{green}{\Leftrightarrow}&x= \pm 2& \cr \color{red}{\mbox{and/or confusion!}}&\left\{\begin{array}{l}x=2\cr x=-2\cr \end{array}\right.& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
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
  <td class="cell c0">Equiv</td>
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
<tr class="pass">
  <td class="cell c0">Equiv</td>
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
  <td class="cell c0">Equiv</td>
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
  <td class="cell c0">Equiv</td>
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
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(EMPTYCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &\left[ \right] & \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[x^2=-1]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(EMPTYCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &x^2=-1& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[x=x,all]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(EMPTYCHAR, EQUIVCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &x=x& \cr \color{green}{\Leftrightarrow}&\mathbb{R}& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[x=x,true]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(EMPTYCHAR, EQUIVCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &x=x& \cr \color{green}{\Leftrightarrow}&\mathbf{True}& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[x=x,false]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">(EMPTYCHAR,QMCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &x=x& \cr \color{red}{?}&\mathbf{False}& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[1=1,all]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(EMPTYCHAR, EQUIVCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &1=1& \cr \color{green}{\Leftrightarrow}&\mathbb{R}& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[1=1,true]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(EMPTYCHAR, EQUIVCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &1=1& \cr \color{green}{\Leftrightarrow}&\mathbf{True}& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[0=0,all]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(EMPTYCHAR, EQUIVCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &0=0& \cr \color{green}{\Leftrightarrow}&\mathbb{R}& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[0=0,true]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(EMPTYCHAR, EQUIVCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &0=0& \cr \color{green}{\Leftrightarrow}&\mathbf{True}& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[1=2,false]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(EMPTYCHAR, EQUIVCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &1=2& \cr \color{green}{\Leftrightarrow}&\mathbf{False}& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[1=2,none]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(EMPTYCHAR, EQUIVCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &1=2& \cr \color{green}{\Leftrightarrow}&\emptyset& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[1=2,{}]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(EMPTYCHAR, EQUIVCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &1=2& \cr \color{green}{\Leftrightarrow}&\left \{ \right \}& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[1=2,[]]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(EMPTYCHAR, EQUIVCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &1=2& \cr \color{green}{\Leftrightarrow}&\left[ \right] & \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[x=1,X=1]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">(EMPTYCHAR,QMCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &x=1& \cr \color{red}{?}&X=1& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[1/(x^2+1)=1/((x+%i)*(x-%i)),t
rue]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(EMPTYCHAR, EQUIVCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &\frac{1}{x^2+1}=\frac{1}{\left(x+\mathrm{i}\right)\cdot \left(x-\mathrm{i}\right)}& \cr \color{green}{\Leftrightarrow}&\mathbf{True}& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[2^2,stackeq(4)]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(EMPTYCHAR, CHECKMARK)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &2^2& \cr \color{green}{\checkmark}&=4& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[2^2,stackeq(3)]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">(EMPTYCHAR,IMPLIESCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &2^2& \cr \color{red}{\Rightarrow}&=3& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[2^2,4]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(EMPTYCHAR, EQUIVCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &2^2& \cr \color{green}{\Leftrightarrow}&4& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[2^2,3]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">(EMPTYCHAR,IMPLIESCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &2^2& \cr \color{red}{\Rightarrow}&3& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[lg(64,4),lg(4^3,4),3*lg(4,4),
3]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(EMPTYCHAR, EQUIVCHAR, EQUIVCHAR, EQUIVCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &\log_{4}\left(64\right)& \cr \color{green}{\Leftrightarrow}&\log_{4}\left(4^3\right)& \cr \color{green}{\Leftrightarrow}&3\cdot \log_{4}\left(4\right)& \cr \color{green}{\Leftrightarrow}&3& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[lg(64,4),stackeq(lg(4^3,4)),s
tackeq(3*lg(4,4)),stackeq(3)]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(EMPTYCHAR, CHECKMARK, CHECKMARK, CHECKMARK)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &\log_{4}\left(64\right)& \cr \color{green}{\checkmark}&=\log_{4}\left(4^3\right)& \cr \color{green}{\checkmark}&=3\cdot \log_{4}\left(4\right)& \cr \color{green}{\checkmark}&=3& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[x=1 or x=2,x=1 or 2]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">(EMPTYCHAR,MISSINGVAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &x=1\,{\mbox{ or }}\, x=2& \cr \color{red}{\mbox{Missing assignments}}&x=1\,{\mbox{ or }}\, 2& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[x=1 or x=2,x=1 and x=2]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">(EMPTYCHAR,ANDOR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &x=1\,{\mbox{ or }}\, x=2& \cr \color{red}{\mbox{and/or confusion!}}&\left\{\begin{array}{l}x=1\cr x=2\cr \end{array}\right.& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[x=1 and y=2,x=1 or y=2]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">(EMPTYCHAR,ANDOR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &\left\{\begin{array}{l}x=1\cr y=2\cr \end{array}\right.& \cr \color{red}{\mbox{and/or confusion!}}&x=1\,{\mbox{ or }}\, y=2& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[a=b,a^2=b^2]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">(EMPTYCHAR,IMPLIESCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &a=b& \cr \color{red}{\Rightarrow}&a^2=b^2& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[a=b,sqrt(a)=sqrt(b)]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">(EMPTYCHAR,IMPLIEDCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &a=b& \cr \color{red}{\Leftarrow}&\sqrt{a}=\sqrt{b}& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[a^2=b^2,a=b]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">(EMPTYCHAR,IMPLIEDCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &a^2=b^2& \cr \color{red}{\Leftarrow}&a=b& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[a^2=b^2,a=b or a=-b]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(EMPTYCHAR, EQUIVCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &a^2=b^2& \cr \color{green}{\Leftrightarrow}&a=b\,{\mbox{ or }}\, a=-b& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[a^2=b^2,a= #pm#b,a= b or a=-b
]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(EMPTYCHAR, EQUIVCHAR, EQUIVCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &a^2=b^2& \cr \color{green}{\Leftrightarrow}&a= \pm b& \cr \color{green}{\Leftrightarrow}&a=b\,{\mbox{ or }}\, a=-b& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[9*x^2/2-81*x/2+90=5*x^2/2-5*x
-20 nounor 9*x^2/2-81*x/2+90=-
(5*x^2/2-5*x-20),9*x^2-81*x+18
0=5*x^2-10*x-40 nounor 9*x^2-8
1*x+180=-5*x^2+10*x+40,4*x^2-7
1*x+220=0 nounor 14*x^2-91*x+1
40=0,x=(71 #pm# sqrt(71^2-4*4*
220))/(2*4) nounor x=(91 #pm# 
sqrt(91^2-4*14*140))/(2*14),x=
55/4 nounor x=4 nounor x=5/2]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(EMPTYCHAR, EQUIVCHAR, EQUIVCHAR, EQUIVCHAR,SAMEROOTS)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &\frac{9\cdot x^2}{2}-\frac{81\cdot x}{2}+90=\frac{5\cdot x^2}{2}-5\cdot x-20\,{\mbox{ or }}\, \frac{9\cdot x^2}{2}-\frac{81\cdot x}{2}+90=-\left(\frac{5\cdot x^2}{2}-5\cdot x-20\right)& \cr \color{green}{\Leftrightarrow}&9\cdot x^2-81\cdot x+180=5\cdot x^2-10\cdot x-40\,{\mbox{ or }}\, 9\cdot x^2-81\cdot x+180=-5\cdot x^2+10\cdot x+40& \cr \color{green}{\Leftrightarrow}&4\cdot x^2-71\cdot x+220=0\,{\mbox{ or }}\, 14\cdot x^2-91\cdot x+140=0& \cr \color{green}{\Leftrightarrow}&x=\frac{{71 \pm \sqrt{71^2-4\cdot 4\cdot 220}}}{2\cdot 4}\,{\mbox{ or }}\, x=\frac{{91 \pm \sqrt{91^2-4\cdot 14\cdot 140}}}{2\cdot 14}& \cr \color{green}{\mbox{(Same roots)}}&x=\frac{55}{4}\,{\mbox{ or }}\, x=4\,{\mbox{ or }}\, x=\frac{5}{2}& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[a=b,abs(a)=abs(b),a=b]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">(EMPTYCHAR,IMPLIESCHAR,IMPLIEDCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &a=b& \cr \color{red}{\Rightarrow}&\left| a\right| =\left| b\right| & \cr \color{red}{\Leftarrow}&a=b& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[abs(a)=abs(b),a=b or a=-b]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(EMPTYCHAR, EQUIVCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &\left| a\right| =\left| b\right| & \cr \color{green}{\Leftrightarrow}&a=b\,{\mbox{ or }}\, a=-b& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[abs(a)=abs(b),a^2=b^2]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(EMPTYCHAR, EQUIVCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &\left| a\right| =\left| b\right| & \cr \color{green}{\Leftrightarrow}&a^2=b^2& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[x^3=8,x=2]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">(EMPTYCHAR,IMPLIEDCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &x^3=8& \cr \color{red}{\Leftarrow}&x=2& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[x^3=8,x=2]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"><pre>[assumereal]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(ASSUMEREALVARS, EQUIVCHARREAL)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll}\color{blue}{(\mathbb{R})}&x^3=8& \cr \color{green}{\Leftrightarrow}\, \color{blue}{(\mathbb{R})}&x=2& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[abs(x-1/2)+abs(x+1/2)=2,abs(x
)=1]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(EMPTYCHAR, EQUIVCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &\left| x-\frac{1}{2}\right| +\left| x+\frac{1}{2}\right| =2& \cr \color{green}{\Leftrightarrow}&\left| x\right| =1& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[a^2=9 and a&gt;0,a=3]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(EMPTYCHAR, EQUIVCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &\left\{\begin{array}{l}a^2=9\cr a > 0\cr \end{array}\right.& \cr \color{green}{\Leftrightarrow}&a=3& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[T=2*pi*sqrt(L/g),T^2=4*pi^2*L
/g,g=4*pi^2*L/T^2]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"><pre>[assumepos]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(ASSUMEPOSVARS, EQUIVCHAR, EQUIVCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll}\color{blue}{\mbox{Assume +ve vars}}&T=2\cdot \pi\cdot \sqrt{\frac{L}{g}}& \cr \color{green}{\Leftrightarrow}&T^2=\frac{4\cdot \pi^2\cdot L}{g}& \cr \color{green}{\Leftrightarrow}&g=\frac{4\cdot \pi^2\cdot L}{T^2}& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[a=b,a^2=b^2]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"><pre>[assumepos]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(ASSUMEPOSVARS, EQUIVCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll}\color{blue}{\mbox{Assume +ve vars}}&a=b& \cr \color{green}{\Leftrightarrow}&a^2=b^2& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[a=b,sqrt(a)=sqrt(b)]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"><pre>[assumepos]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(ASSUMEPOSVARS, EQUIVCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll}\color{blue}{\mbox{Assume +ve vars}}&a=b& \cr \color{green}{\Leftrightarrow}&\sqrt{a}=\sqrt{b}& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[a^2=b^2,a=b]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"><pre>[assumepos]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(ASSUMEPOSVARS, EQUIVCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll}\color{blue}{\mbox{Assume +ve vars}}&a^2=b^2& \cr \color{green}{\Leftrightarrow}&a=b& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[a^2=b^2,a=b or a=-b]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"><pre>[assumepos]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(ASSUMEPOSVARS, EQUIVCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll}\color{blue}{\mbox{Assume +ve vars}}&a^2=b^2& \cr \color{green}{\Leftrightarrow}&a=b\,{\mbox{ or }}\, a=-b& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[a=b,abs(a)=abs(b)]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"><pre>[assumepos]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(ASSUMEPOSVARS, EQUIVCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll}\color{blue}{\mbox{Assume +ve vars}}&a=b& \cr \color{green}{\Leftrightarrow}&\left| a\right| =\left| b\right| & \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[abs(a)=abs(b),a=b]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"><pre>[assumepos]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(ASSUMEPOSVARS, EQUIVCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll}\color{blue}{\mbox{Assume +ve vars}}&\left| a\right| =\left| b\right| & \cr \color{green}{\Leftrightarrow}&a=b& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[abs(a)=abs(b),a=-b]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"><pre>[assumepos]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(ASSUMEPOSVARS, EQUIVCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll}\color{blue}{\mbox{Assume +ve vars}}&\left| a\right| =\left| b\right| & \cr \color{green}{\Leftrightarrow}&a=-b& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[abs(a)=abs(b),a=b or a=-b]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"><pre>[assumepos]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(ASSUMEPOSVARS, EQUIVCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll}\color{blue}{\mbox{Assume +ve vars}}&\left| a\right| =\left| b\right| & \cr \color{green}{\Leftrightarrow}&a=b\,{\mbox{ or }}\, a=-b& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[x=abs(-2),x=2]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"><pre>[assumepos]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(ASSUMEPOSVARS, EQUIVCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll}\color{blue}{\mbox{Assume +ve vars}}&x=\left| -2\right| & \cr \color{green}{\Leftrightarrow}&x=2& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[abs(a)=abs(b),a^2=b^2]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"><pre>[assumepos]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(ASSUMEPOSVARS, EQUIVCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll}\color{blue}{\mbox{Assume +ve vars}}&\left| a\right| =\left| b\right| & \cr \color{green}{\Leftrightarrow}&a^2=b^2& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[x^2=9,x=#pm#3,x=3 or x=-3,x=3
]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"><pre>[assumepos]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(ASSUMEPOSVARS, EQUIVCHAR, EQUIVCHAR, EQUIVCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll}\color{blue}{\mbox{Assume +ve vars}}&x^2=9& \cr \color{green}{\Leftrightarrow}&x= \pm 3& \cr \color{green}{\Leftrightarrow}&x=3\,{\mbox{ or }}\, x=-3& \cr \color{green}{\Leftrightarrow}&x=3& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[x^2=9,x=3]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"><pre>[assumepos]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(ASSUMEPOSVARS, EQUIVCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll}\color{blue}{\mbox{Assume +ve vars}}&x^2=9& \cr \color{green}{\Leftrightarrow}&x=3& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[x^2=2,x=#pm#sqrt(2),x=sqrt(2)
 or x=-sqrt(2)]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"><pre>[assumepos]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(ASSUMEPOSVARS, EQUIVCHAR, EQUIVCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll}\color{blue}{\mbox{Assume +ve vars}}&x^2=2& \cr \color{green}{\Leftrightarrow}&x= \pm \sqrt{2}& \cr \color{green}{\Leftrightarrow}&x=\sqrt{2}\,{\mbox{ or }}\, x=-\sqrt{2}& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[x^2=2,x=sqrt(2)]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"><pre>[assumepos]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(ASSUMEPOSVARS, EQUIVCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll}\color{blue}{\mbox{Assume +ve vars}}&x^2=2& \cr \color{green}{\Leftrightarrow}&x=\sqrt{2}& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[x^2 = a^2-b,x = sqrt(a^2-b)]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"><pre>[assumepos]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(ASSUMEPOSVARS, EQUIVCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll}\color{blue}{\mbox{Assume +ve vars}}&x^2=a^2-b& \cr \color{green}{\Leftrightarrow}&x=\sqrt{a^2-b}& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[2*(x-3) = 4*x-3*(x+2),2*x-6=x
-6,x=0]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(EMPTYCHAR, EQUIVCHAR, EQUIVCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &2\cdot \left(x-3\right)=4\cdot x-3\cdot \left(x+2\right)& \cr \color{green}{\Leftrightarrow}&2\cdot x-6=x-6& \cr \color{green}{\Leftrightarrow}&x=0& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[2*(x-3) = 5*x-3*(x+2),2*x-6=2
*x-6,0=0,all]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(EMPTYCHAR, EQUIVCHAR, EQUIVCHAR, EQUIVCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &2\cdot \left(x-3\right)=5\cdot x-3\cdot \left(x+2\right)& \cr \color{green}{\Leftrightarrow}&2\cdot x-6=2\cdot x-6& \cr \color{green}{\Leftrightarrow}&0=0& \cr \color{green}{\Leftrightarrow}&\mathbb{R}& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[2*(x-3) = 5*x-3*(x+1),2*x-6=2
*x-3,0=3,{}]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(EMPTYCHAR, EQUIVCHAR, EQUIVCHAR, EQUIVCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &2\cdot \left(x-3\right)=5\cdot x-3\cdot \left(x+1\right)& \cr \color{green}{\Leftrightarrow}&2\cdot x-6=2\cdot x-3& \cr \color{green}{\Leftrightarrow}&0=3& \cr \color{green}{\Leftrightarrow}&\left \{ \right \}& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[a^2=b^2,a^2-b^2=0,(a-b)*(a+b)
=0,a=b or a=-b]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(EMPTYCHAR, EQUIVCHAR, EQUIVCHAR, EQUIVCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &a^2=b^2& \cr \color{green}{\Leftrightarrow}&a^2-b^2=0& \cr \color{green}{\Leftrightarrow}&\left(a-b\right)\cdot \left(a+b\right)=0& \cr \color{green}{\Leftrightarrow}&a=b\,{\mbox{ or }}\, a=-b& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[a^3=b^3,a^3-b^3=0,(a-b)*(a^2+
a*b+b^2)=0,(a-b)=0,a=b]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">(EMPTYCHAR, EQUIVCHAR, EQUIVCHAR,IMPLIEDCHAR, EQUIVCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &a^3=b^3& \cr \color{green}{\Leftrightarrow}&a^3-b^3=0& \cr \color{green}{\Leftrightarrow}&\left(a-b\right)\cdot \left(a^2+a\cdot b+b^2\right)=0& \cr \color{red}{\Leftarrow}&a-b=0& \cr \color{green}{\Leftrightarrow}&a=b& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[a^3=b^3,a^3-b^3=0,(a-b)*(a^2+
a*b+b^2)=0,(a-b)=0 or (a^2+a*b
+b^2)=0, a=b or (a+(1+%i*sqrt(
3))/2*b)*(a+(1-%i*sqrt(3))/2*b
)=0, a=b or a=-(1+%i*sqrt(3))/
2*b or a=-(1-%i*sqrt(3))/2*b]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(EMPTYCHAR, EQUIVCHAR, EQUIVCHAR, EQUIVCHAR, EQUIVCHAR, EQUIVCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &a^3=b^3& \cr \color{green}{\Leftrightarrow}&a^3-b^3=0& \cr \color{green}{\Leftrightarrow}&\left(a-b\right)\cdot \left(a^2+a\cdot b+b^2\right)=0& \cr \color{green}{\Leftrightarrow}&a-b=0\,{\mbox{ or }}\, a^2+a\cdot b+b^2=0& \cr \color{green}{\Leftrightarrow}&a=b\,{\mbox{ or }}\, \left(a+\frac{1+\mathrm{i}\cdot \sqrt{3}}{2}\cdot b\right)\cdot \left(a+\frac{1-\mathrm{i}\cdot \sqrt{3}}{2}\cdot b\right)=0& \cr \color{green}{\Leftrightarrow}&a=b\,{\mbox{ or }}\, a=\frac{-\left(1+\mathrm{i}\cdot \sqrt{3}\right)}{2}\cdot b\,{\mbox{ or }}\, a=\frac{-\left(1-\mathrm{i}\cdot \sqrt{3}\right)}{2}\cdot b& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[x^2-x=30,x^2-x-30=0,(x-6)*(x+
5)=0,x-6=0 or x+5=0,x=6 or x=-
5]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(EMPTYCHAR, EQUIVCHAR, EQUIVCHAR, EQUIVCHAR, EQUIVCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &x^2-x=30& \cr \color{green}{\Leftrightarrow}&x^2-x-30=0& \cr \color{green}{\Leftrightarrow}&\left(x-6\right)\cdot \left(x+5\right)=0& \cr \color{green}{\Leftrightarrow}&x-6=0\,{\mbox{ or }}\, x+5=0& \cr \color{green}{\Leftrightarrow}&x=6\,{\mbox{ or }}\, x=-5& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[x^2=2,x^2-2=0,(x-sqrt(2))*(x+
sqrt(2))=0,x=sqrt(2) or x=-sqr
t(2)]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(EMPTYCHAR, EQUIVCHAR, EQUIVCHAR, EQUIVCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &x^2=2& \cr \color{green}{\Leftrightarrow}&x^2-2=0& \cr \color{green}{\Leftrightarrow}&\left(x-\sqrt{2}\right)\cdot \left(x+\sqrt{2}\right)=0& \cr \color{green}{\Leftrightarrow}&x=\sqrt{2}\,{\mbox{ or }}\, x=-\sqrt{2}& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[x^2=2,x=#pm#sqrt(2),x=sqrt(2)
 or x=-sqrt(2)]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(EMPTYCHAR, EQUIVCHAR, EQUIVCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &x^2=2& \cr \color{green}{\Leftrightarrow}&x= \pm \sqrt{2}& \cr \color{green}{\Leftrightarrow}&x=\sqrt{2}\,{\mbox{ or }}\, x=-\sqrt{2}& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[(2*x-7)^2=(x+1)^2,(2*x-7)^2 -
(x+1)^2=0,(2*x-7+x+1)*(2*x-7-x
-1)=0,(3*x-6)*(x-8)=0,x=2 or x
=8]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(EMPTYCHAR, EQUIVCHAR, EQUIVCHAR, EQUIVCHAR, EQUIVCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &{\left(2\cdot x-7\right)}^2={\left(x+1\right)}^2& \cr \color{green}{\Leftrightarrow}&{\left(2\cdot x-7\right)}^2-{\left(x+1\right)}^2=0& \cr \color{green}{\Leftrightarrow}&\left(2\cdot x-7+x+1\right)\cdot \left(2\cdot x-7-x-1\right)=0& \cr \color{green}{\Leftrightarrow}&\left(3\cdot x-6\right)\cdot \left(x-8\right)=0& \cr \color{green}{\Leftrightarrow}&x=2\,{\mbox{ or }}\, x=8& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[x^2-6*x=-9,(x-3)^2=0,x-3=0,x=
3]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(EMPTYCHAR, EQUIVCHAR,SAMEROOTS, EQUIVCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &x^2-6\cdot x=-9& \cr \color{green}{\Leftrightarrow}&{\left(x-3\right)}^2=0& \cr \color{green}{\mbox{(Same roots)}}&x-3=0& \cr \color{green}{\Leftrightarrow}&x=3& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[(2*x-7)^2=(x+1)^2,sqrt((2*x-7
)^2)=sqrt((x+1)^2),2*x-7=x+1,x
=8]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">(EMPTYCHAR, EQUIVCHAR,IMPLIEDCHAR, EQUIVCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &{\left(2\cdot x-7\right)}^2={\left(x+1\right)}^2& \cr \color{green}{\Leftrightarrow}&\sqrt{{\left(2\cdot x-7\right)}^2}=\sqrt{{\left(x+1\right)}^2}& \cr \color{red}{\Leftarrow}&2\cdot x-7=x+1& \cr \color{green}{\Leftrightarrow}&x=8& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[x^2-10*x+9 = 0, (x-5)^2-16 = 
0, (x-5)^2 =16, x-5 =#pm#4, x-
5 =4 or x-5=-4, x = 1 or x = 9
]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(EMPTYCHAR, EQUIVCHAR, EQUIVCHAR, EQUIVCHAR, EQUIVCHAR, EQUIVCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &x^2-10\cdot x+9=0& \cr \color{green}{\Leftrightarrow}&{\left(x-5\right)}^2-16=0& \cr \color{green}{\Leftrightarrow}&{\left(x-5\right)}^2=16& \cr \color{green}{\Leftrightarrow}&x-5= \pm 4& \cr \color{green}{\Leftrightarrow}&x-5=4\,{\mbox{ or }}\, x-5=-4& \cr \color{green}{\Leftrightarrow}&x=1\,{\mbox{ or }}\, x=9& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[x^2-2*p*x-q=0,x^2-2*p*x=q,x^2
-2*p*x+p^2=q+p^2,(x-p)^2=q+p^2
,x-p=#pm#sqrt(q+p^2),x-p=sqrt(
q+p^2) or x-p=-sqrt(q+p^2),x=p
+sqrt(q+p^2) or x=p-sqrt(q+p^2
)]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(EMPTYCHAR, EQUIVCHAR, EQUIVCHAR, EQUIVCHAR, EQUIVCHAR, EQUIVCHAR, EQUIVCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &x^2-2\cdot p\cdot x-q=0& \cr \color{green}{\Leftrightarrow}&x^2-2\cdot p\cdot x=q& \cr \color{green}{\Leftrightarrow}&x^2-2\cdot p\cdot x+p^2=q+p^2& \cr \color{green}{\Leftrightarrow}&{\left(x-p\right)}^2=q+p^2& \cr \color{green}{\Leftrightarrow}&x-p= \pm \sqrt{q+p^2}& \cr \color{green}{\Leftrightarrow}&x-p=\sqrt{q+p^2}\,{\mbox{ or }}\, x-p=-\sqrt{q+p^2}& \cr \color{green}{\Leftrightarrow}&x=p+\sqrt{q+p^2}\,{\mbox{ or }}\, x=p-\sqrt{q+p^2}& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[x^2-10*x+7=0,(x-5)^2-18=0,(x-
5)^2=sqrt(18)^2,(x-5)^2-sqrt(1
8)^2=0,(x-5-sqrt(18))*(x-5+sqr
t(18))=0,x=5-sqrt(18) or x=5+s
qrt(18)]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(EMPTYCHAR, EQUIVCHAR, EQUIVCHAR, EQUIVCHAR, EQUIVCHAR, EQUIVCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &x^2-10\cdot x+7=0& \cr \color{green}{\Leftrightarrow}&{\left(x-5\right)}^2-18=0& \cr \color{green}{\Leftrightarrow}&{\left(x-5\right)}^2={\sqrt{18}}^2& \cr \color{green}{\Leftrightarrow}&{\left(x-5\right)}^2-{\sqrt{18}}^2=0& \cr \color{green}{\Leftrightarrow}&\left(x-5-\sqrt{18}\right)\cdot \left(x-5+\sqrt{18}\right)=0& \cr \color{green}{\Leftrightarrow}&x=5-\sqrt{18}\,{\mbox{ or }}\, x=5+\sqrt{18}& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[9*x^2/2-81*x/2+90=5*x^2/2-5*x
-20,4*x^2-71*x+220 = 0,x = (71
 #pm# 39)/8,x=55/4 nounor x=4]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(EMPTYCHAR, EQUIVCHAR, EQUIVCHAR, EQUIVCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &\frac{9\cdot x^2}{2}-\frac{81\cdot x}{2}+90=\frac{5\cdot x^2}{2}-5\cdot x-20& \cr \color{green}{\Leftrightarrow}&4\cdot x^2-71\cdot x+220=0& \cr \color{green}{\Leftrightarrow}&x=\frac{{71 \pm 39}}{8}& \cr \color{green}{\Leftrightarrow}&x=\frac{55}{4}\,{\mbox{ or }}\, x=4& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[x^2+2*a*x = 0, x*(x+2*a)=0, (
x+a-a)*(x+a+a)=0, (x+a)^2-a^2=
0]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(EMPTYCHAR, EQUIVCHAR, EQUIVCHAR, EQUIVCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &x^2+2\cdot a\cdot x=0& \cr \color{green}{\Leftrightarrow}&x\cdot \left(x+2\cdot a\right)=0& \cr \color{green}{\Leftrightarrow}&\left(x+a-a\right)\cdot \left(x+a+a\right)=0& \cr \color{green}{\Leftrightarrow}&{\left(x+a\right)}^2-a^2=0& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[x^3-1=0,(x-1)*(x^2+x+1)=0,x=1
]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">(EMPTYCHAR, EQUIVCHAR,IMPLIEDCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &x^3-1=0& \cr \color{green}{\Leftrightarrow}&\left(x-1\right)\cdot \left(x^2+x+1\right)=0& \cr \color{red}{\Leftarrow}&x=1& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[x^3-1=0,(x-1)*(x^2+x+1)=0,x=1
 or x^2+x+1=0,x=1 or x = -(sqr
t(3)*%i+1)/2 or x=(sqrt(3)*%i-
1)/2]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(EMPTYCHAR, EQUIVCHAR, EQUIVCHAR, EQUIVCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &x^3-1=0& \cr \color{green}{\Leftrightarrow}&\left(x-1\right)\cdot \left(x^2+x+1\right)=0& \cr \color{green}{\Leftrightarrow}&x=1\,{\mbox{ or }}\, x^2+x+1=0& \cr \color{green}{\Leftrightarrow}&x=1\,{\mbox{ or }}\, x=\frac{-\left(\sqrt{3}\cdot \mathrm{i}+1\right)}{2}\,{\mbox{ or }}\, x=\frac{\sqrt{3}\cdot \mathrm{i}-1}{2}& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[a*x^2+b*x+c=0 or a=0,a^2*x^2+
a*b*x+a*c=0,(a*x)^2+b*(a*x)+a*
c=0, (a*x)^2+b*(a*x)+b^2/4-b^2
/4+a*c=0,(a*x+b/2)^2-b^2/4+a*c
=0,(a*x+b/2)^2=b^2/4-a*c, a*x+
b/2= #pm#sqrt(b^2/4-a*c),a*x=-
b/2+sqrt(b^2/4-a*c) or a*x=-b/
2-sqrt(b^2/4-a*c), (a=0 or x=(
-b+sqrt(b^2-4*a*c))/(2*a)) or 
(a=0 or x=(-b-sqrt(b^2-4*a*c))
/(2*a)), a^2=0 or x=(-b+sqrt(b
^2-4*a*c))/(2*a) or x=(-b-sqrt
(b^2-4*a*c))/(2*a)]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(EMPTYCHAR, EQUIVCHAR, EQUIVCHAR, EQUIVCHAR, EQUIVCHAR, EQUIVCHAR, EQUIVCHAR, EQUIVCHAR, EQUIVCHAR, EQUIVCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &a\cdot x^2+b\cdot x+c=0\,{\mbox{ or }}\, a=0& \cr \color{green}{\Leftrightarrow}&a^2\cdot x^2+a\cdot b\cdot x+a\cdot c=0& \cr \color{green}{\Leftrightarrow}&{\left(a\cdot x\right)}^2+b\cdot \left(a\cdot x\right)+a\cdot c=0& \cr \color{green}{\Leftrightarrow}&{\left(a\cdot x\right)}^2+b\cdot \left(a\cdot x\right)+\frac{b^2}{4}-\frac{b^2}{4}+a\cdot c=0& \cr \color{green}{\Leftrightarrow}&{\left(a\cdot x+\frac{b}{2}\right)}^2-\frac{b^2}{4}+a\cdot c=0& \cr \color{green}{\Leftrightarrow}&{\left(a\cdot x+\frac{b}{2}\right)}^2=\frac{b^2}{4}-a\cdot c& \cr \color{green}{\Leftrightarrow}&a\cdot x+\frac{b}{2}= \pm \sqrt{\frac{b^2}{4}-a\cdot c}& \cr \color{green}{\Leftrightarrow}&a\cdot x=-\frac{b}{2}+\sqrt{\frac{b^2}{4}-a\cdot c}\,{\mbox{ or }}\, a\cdot x=-\frac{b}{2}-\sqrt{\frac{b^2}{4}-a\cdot c}& \cr \color{green}{\Leftrightarrow}&a=0\,{\mbox{ or }}\, x=\frac{-b+\sqrt{b^2-4\cdot a\cdot c}}{2\cdot a}\,{\mbox{ or }}\, \left(a=0\,{\mbox{ or }}\, x=\frac{-b-\sqrt{b^2-4\cdot a\cdot c}}{2\cdot a}\right)& \cr \color{green}{\Leftrightarrow}&a^2=0\,{\mbox{ or }}\, x=\frac{-b+\sqrt{b^2-4\cdot a\cdot c}}{2\cdot a}\,{\mbox{ or }}\, x=\frac{-b-\sqrt{b^2-4\cdot a\cdot c}}{2\cdot a}& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[a*x^2+b*x=-c,4*a^2*x^2+4*a*b*
x+b^2=b^2-4*a*c,(2*a*x+b)^2=b^
2-4*a*c,2*a*x+b=#pm#sqrt(b^2-4
*a*c),2*a*x=-b#pm#sqrt(b^2-4*a
*c),x=(-b#pm#sqrt(b^2-4*a*c))/
(2*a)]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">(EMPTYCHAR,IMPLIESCHAR, EQUIVCHAR, EQUIVCHAR, EQUIVCHAR,QMCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &a\cdot x^2+b\cdot x=-c& \cr \color{red}{\Rightarrow}&4\cdot a^2\cdot x^2+4\cdot a\cdot b\cdot x+b^2=b^2-4\cdot a\cdot c& \cr \color{green}{\Leftrightarrow}&{\left(2\cdot a\cdot x+b\right)}^2=b^2-4\cdot a\cdot c& \cr \color{green}{\Leftrightarrow}&2\cdot a\cdot x+b= \pm \sqrt{b^2-4\cdot a\cdot c}& \cr \color{green}{\Leftrightarrow}&2\cdot a\cdot x={-b \pm \sqrt{b^2-4\cdot a\cdot c}}& \cr \color{red}{?}&x=\frac{{-b \pm \sqrt{b^2-4\cdot a\cdot c}}}{2\cdot a}& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[a*x^2+b*x=-c or a=0,4*a^2*x^2
+4*a*b*x+b^2=b^2-4*a*c,(2*a*x+
b)^2=b^2-4*a*c,2*a*x+b=#pm#sqr
t(b^2-4*a*c),2*a*x=-b#pm#sqrt(
b^2-4*a*c),x=(-b#pm#sqrt(b^2-4
*a*c))/(2*a) or a=0]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(EMPTYCHAR, EQUIVCHAR, EQUIVCHAR, EQUIVCHAR, EQUIVCHAR, EQUIVCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &a\cdot x^2+b\cdot x=-c\,{\mbox{ or }}\, a=0& \cr \color{green}{\Leftrightarrow}&4\cdot a^2\cdot x^2+4\cdot a\cdot b\cdot x+b^2=b^2-4\cdot a\cdot c& \cr \color{green}{\Leftrightarrow}&{\left(2\cdot a\cdot x+b\right)}^2=b^2-4\cdot a\cdot c& \cr \color{green}{\Leftrightarrow}&2\cdot a\cdot x+b= \pm \sqrt{b^2-4\cdot a\cdot c}& \cr \color{green}{\Leftrightarrow}&2\cdot a\cdot x={-b \pm \sqrt{b^2-4\cdot a\cdot c}}& \cr \color{green}{\Leftrightarrow}&x=\frac{{-b \pm \sqrt{b^2-4\cdot a\cdot c}}}{2\cdot a}\,{\mbox{ or }}\, a=0& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[sqrt(3*x+4) = 2+sqrt(x+2), 3*
x+4=4+4*sqrt(x+2)+(x+2),x-1=2*
sqrt(x+2),x^2-2*x+1 = 4*x+8,x^
2-6*x-7 = 0,(x-7)*(x+1) = 0,x=
7 or x=-1]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">(EMPTYCHAR,IMPLIESCHAR, EQUIVCHAR,IMPLIESCHAR, EQUIVCHAR, EQUIVCHAR, EQUIVCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &\sqrt{3\cdot x+4}=2+\sqrt{x+2}&{\color{blue}{{x \in {\left[ -\frac{4}{3},\, \infty \right)}}}}\cr \color{red}{\Rightarrow}&3\cdot x+4=4+4\cdot \sqrt{x+2}+\left(x+2\right)&{\color{blue}{{x \in {\left[ -2,\, \infty \right)}}}}\cr \color{green}{\Leftrightarrow}&x-1=2\cdot \sqrt{x+2}&{\color{blue}{{x \in {\left[ -2,\, \infty \right)}}}}\cr \color{red}{\Rightarrow}&x^2-2\cdot x+1=4\cdot x+8& \cr \color{green}{\Leftrightarrow}&x^2-6\cdot x-7=0& \cr \color{green}{\Leftrightarrow}&\left(x-7\right)\cdot \left(x+1\right)=0& \cr \color{green}{\Leftrightarrow}&x=7\,{\mbox{ or }}\, x=-1& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[sqrt(3*x+4) = 2+sqrt(x+2), 3*
x+4=4+4*sqrt(x+2)+(x+2),x-1=2*
sqrt(x+2),x^2-2*x+1 = 4*x+8,x^
2-6*x-7 = 0,(x-7)*(x+1) = 0,x=
7 or x=-1,x=7]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"><pre>[assumepos]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(ASSUMEPOSVARS, EQUIVCHAR, EQUIVCHAR, EQUIVCHAR, EQUIVCHAR, EQUIVCHAR, EQUIVCHAR, EQUIVCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll}\color{blue}{\mbox{Assume +ve vars}}&\sqrt{3\cdot x+4}=2+\sqrt{x+2}&{\color{blue}{{x \in {\left[ 0,\, \infty \right)}}}}\cr \color{green}{\Leftrightarrow}&3\cdot x+4=4+4\cdot \sqrt{x+2}+\left(x+2\right)&{\color{blue}{{x \in {\left[ 0,\, \infty \right)}}}}\cr \color{green}{\Leftrightarrow}&x-1=2\cdot \sqrt{x+2}&{\color{blue}{{x \in {\left[ 0,\, \infty \right)}}}}\cr \color{green}{\Leftrightarrow}&x^2-2\cdot x+1=4\cdot x+8& \cr \color{green}{\Leftrightarrow}&x^2-6\cdot x-7=0& \cr \color{green}{\Leftrightarrow}&\left(x-7\right)\cdot \left(x+1\right)=0& \cr \color{green}{\Leftrightarrow}&x=7\,{\mbox{ or }}\, x=-1& \cr \color{green}{\Leftrightarrow}&x=7& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[x*(x-1)*(x-2)=0,x*(x-1)=0,x*(
x-1)*(x-2)=0,x*(x^2-2)=0]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">(EMPTYCHAR,IMPLIEDCHAR,IMPLIESCHAR,QMCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &x\cdot \left(x-1\right)\cdot \left(x-2\right)=0& \cr \color{red}{\Leftarrow}&x\cdot \left(x-1\right)=0& \cr \color{red}{\Rightarrow}&x\cdot \left(x-1\right)\cdot \left(x-2\right)=0& \cr \color{red}{?}&x\cdot \left(x^2-2\right)=0& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[x^2-6*x=-9,x=3]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(EMPTYCHAR,SAMEROOTS)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &x^2-6\cdot x=-9& \cr \color{green}{\mbox{(Same roots)}}&x=3& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[x=1 nounor x=-2 nounor x=1,x^
3-3*x=-2,x=1 nounor x=-2]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(EMPTYCHAR, EQUIVCHAR,SAMEROOTS)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &x=1\,{\mbox{ or }}\, x=-2\,{\mbox{ or }}\, x=1& \cr \color{green}{\Leftrightarrow}&x^3-3\cdot x=-2& \cr \color{green}{\mbox{(Same roots)}}&x=1\,{\mbox{ or }}\, x=-2& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[9*x^3-24*x^2+13*x=2,x=1/3 nou
nor x=2]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(EMPTYCHAR,SAMEROOTS)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &9\cdot x^3-24\cdot x^2+13\cdot x=2& \cr \color{green}{\mbox{(Same roots)}}&x=\frac{1}{3}\,{\mbox{ or }}\, x=2& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[(x-2)^43*(x+1/3)^60=0,(3*x+1)
^4*(x-2)^2=0,x=-1/3 nounor x=2
]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(EMPTYCHAR,SAMEROOTS,SAMEROOTS)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &{\left(x-2\right)}^{43}\cdot {\left(x+\frac{1}{3}\right)}^{60}=0& \cr \color{green}{\mbox{(Same roots)}}&{\left(3\cdot x+1\right)}^4\cdot {\left(x-2\right)}^2=0& \cr \color{green}{\mbox{(Same roots)}}&x=\frac{-1}{3}\,{\mbox{ or }}\, x=2& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[2^x=4,x*log(2)=log(4),x=log(2
^2)/log(2),x=2*log(2)/log(2),x
=2]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(EMPTYCHAR, EQUIVCHAR, EQUIVCHAR, EQUIVCHAR, EQUIVCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &2^{x}=4& \cr \color{green}{\Leftrightarrow}&x\cdot \ln \left( 2 \right)=\ln \left( 4 \right)& \cr \color{green}{\Leftrightarrow}&x=\frac{\ln \left( 2^2 \right)}{\ln \left( 2 \right)}& \cr \color{green}{\Leftrightarrow}&x=\frac{2\cdot \ln \left( 2 \right)}{\ln \left( 2 \right)}& \cr \color{green}{\Leftrightarrow}&x=2& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[x^log(y),stackeq(e^(log(x)*lo
g(y))),stackeq(e^(log(y)*log(x
))),stackeq(y^log(x))]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(EMPTYCHAR, CHECKMARK, CHECKMARK, CHECKMARK)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &x^{\ln \left( y \right)}& \cr \color{green}{\checkmark}&=e^{\ln \left( x \right)\cdot \ln \left( y \right)}& \cr \color{green}{\checkmark}&=e^{\ln \left( y \right)\cdot \ln \left( x \right)}& \cr \color{green}{\checkmark}&=y^{\ln \left( x \right)}& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[lg(x+17,3)-2=lg(2*x,3),lg(x+1
7,3)-lg(2*x,3)=2,lg((x+17)/(2*
x),3)=2,(x+17)/(2*x)=3^2,(x+17
)=18*x,17*x=17,x=1]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(EMPTYCHAR, EQUIVCHAR, EQUIVCHAR,EQUIVLOG, EQUIVCHAR, EQUIVCHAR, EQUIVCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &\log_{3}\left(x+17\right)-2=\log_{3}\left(2\cdot x\right)&{\color{blue}{{x \in {\left( 0,\, \infty \right)}}}}\cr \color{green}{\Leftrightarrow}&\log_{3}\left(x+17\right)-\log_{3}\left(2\cdot x\right)=2&{\color{blue}{{x \in {\left( 0,\, \infty \right)}}}}\cr \color{green}{\Leftrightarrow}&\log_{3}\left(\frac{x+17}{2\cdot x}\right)=2& \cr \color{green}{\log(?)}&\frac{x+17}{2\cdot x}=3^2&{\color{blue}{{x \not\in {\left \{0 \right \}}}}}\cr \color{green}{\Leftrightarrow}&x+17=18\cdot x& \cr \color{green}{\Leftrightarrow}&17\cdot x=17& \cr \color{green}{\Leftrightarrow}&x=1& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[a=logbase(9,3),3^a=9,3^a=3^2,
a=2]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(EMPTYCHAR, EQUIVCHAR, EQUIVCHAR, EQUIVCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &a=\log_{3}\left(9\right)& \cr \color{green}{\Leftrightarrow}&3^{a}=9& \cr \color{green}{\Leftrightarrow}&3^{a}=3^2& \cr \color{green}{\Leftrightarrow}&a=2& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[x=(1+y/n)^n,x^(1/n)=(1+y/n),y
/n=x^(1/n)-1,y=n*(x^(1/n)-1)]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">(EMPTYCHAR,QMCHAR, EQUIVCHAR, EQUIVCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &x={\left(1+\frac{y}{n}\right)}^{n}& \cr \color{red}{?}&x^{\frac{1}{n}}=1+\frac{y}{n}& \cr \color{green}{\Leftrightarrow}&\frac{y}{n}=x^{\frac{1}{n}}-1& \cr \color{green}{\Leftrightarrow}&y=n\cdot \left(x^{\frac{1}{n}}-1\right)& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[a^3=b^3,a^3-b^3=0,(a-b)*(a^2+
a*b+b^2)=0,(a-b)=0,a=b]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"><pre>[assumereal]</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">(ASSUMEREALVARS, EQUIVCHAR, EQUIVCHAR,IMPLIEDCHAR, EQUIVCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll}\color{blue}{(\mathbb{R})}&a^3=b^3& \cr \color{green}{\Leftrightarrow}&a^3-b^3=0& \cr \color{green}{\Leftrightarrow}&\left(a-b\right)\cdot \left(a^2+a\cdot b+b^2\right)=0& \cr \color{red}{\Leftarrow}&a-b=0& \cr \color{green}{\Leftrightarrow}&a=b& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[x^3-1=0,(x-1)*(x^2+x+1)=0,x=1
]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"><pre>[assumereal]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(ASSUMEREALVARS, EQUIVCHAR, EQUIVCHARREAL)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll}\color{blue}{(\mathbb{R})}&x^3-1=0& \cr \color{green}{\Leftrightarrow}&\left(x-1\right)\cdot \left(x^2+x+1\right)=0& \cr \color{green}{\Leftrightarrow}\, \color{blue}{(\mathbb{R})}&x=1& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[x^4=2,x^4-2=0,(x^2-sqrt(2))*(
x^2+sqrt(2))=0,x^2=sqrt(2),x=#
pm# 2^(1/4)]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"><pre>[assumereal]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(ASSUMEREALVARS, EQUIVCHAR, EQUIVCHAR, EQUIVCHARREAL, EQUIVCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll}\color{blue}{(\mathbb{R})}&x^4=2& \cr \color{green}{\Leftrightarrow}&x^4-2=0& \cr \color{green}{\Leftrightarrow}&\left(x^2-\sqrt{2}\right)\cdot \left(x^2+\sqrt{2}\right)=0& \cr \color{green}{\Leftrightarrow}\, \color{blue}{(\mathbb{R})}&x^2=\sqrt{2}& \cr \color{green}{\Leftrightarrow}&x= \pm 2^{\frac{1}{4}}& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[6*x-12=3*(x-2),6*x-12+3*(x-2)
=0,9*x-18=0,x=2]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(EMPTYCHAR, EQUIVCHAR, EQUIVCHAR, EQUIVCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &6\cdot x-12=3\cdot \left(x-2\right)& \cr \color{green}{\Leftrightarrow}&6\cdot x-12+3\cdot \left(x-2\right)=0& \cr \color{green}{\Leftrightarrow}&9\cdot x-18=0& \cr \color{green}{\Leftrightarrow}&x=2& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[x^2-6*x+9=0,x^2-6*x=-9,x*(x-6
)=3*-3,x=3 or x-6=-3,x=3]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(EMPTYCHAR, EQUIVCHAR, EQUIVCHAR, EQUIVCHAR,SAMEROOTS)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &x^2-6\cdot x+9=0& \cr \color{green}{\Leftrightarrow}&x^2-6\cdot x=-9& \cr \color{green}{\Leftrightarrow}&x\cdot \left(x-6\right)=3\cdot \left(-3\right)& \cr \color{green}{\Leftrightarrow}&x=3\,{\mbox{ or }}\, x-6=-3& \cr \color{green}{\mbox{(Same roots)}}&x=3& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[(x+3)*(2-x)=4,x+3=4 or (2-x)=
4,x=1 or x=-2]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(EMPTYCHAR, EQUIVCHAR, EQUIVCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &\left(x+3\right)\cdot \left(2-x\right)=4& \cr \color{green}{\Leftrightarrow}&x+3=4\,{\mbox{ or }}\, 2-x=4& \cr \color{green}{\Leftrightarrow}&x=1\,{\mbox{ or }}\, x=-2& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[(x-p)*(x-q)=0,x^2-p*x-q*x+p*q
=0,1+q-x-p-p*q+p*x+x+q*x-x^2=1
-p+q,(1+q-x)*(1-p+x)=1-p+q,(1+
q-x)=1-p+q or (1-p+x)=1-p+q,x=
p or x=q]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(EMPTYCHAR, EQUIVCHAR, EQUIVCHAR, EQUIVCHAR, EQUIVCHAR, EQUIVCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &\left(x-p\right)\cdot \left(x-q\right)=0& \cr \color{green}{\Leftrightarrow}&x^2-p\cdot x+\left(-q\right)\cdot x+p\cdot q=0& \cr \color{green}{\Leftrightarrow}&1+q-x-p+\left(-p\right)\cdot q+p\cdot x+x+q\cdot x-x^2=1-p+q& \cr \color{green}{\Leftrightarrow}&\left(1+q-x\right)\cdot \left(1-p+x\right)=1-p+q& \cr \color{green}{\Leftrightarrow}&1+q-x=1-p+q\,{\mbox{ or }}\, 1-p+x=1-p+q& \cr \color{green}{\Leftrightarrow}&x=p\,{\mbox{ or }}\, x=q& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[a=b, a^2=a*b, a^2-b^2=a*b-b^2
, (a-b)*(a+b)=b*(a-b), a+b=b, 
2*a=a, 1=2]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">(EMPTYCHAR,IMPLIESCHAR, EQUIVCHAR, EQUIVCHAR,IMPLIEDCHAR, EQUIVCHAR,IMPLIEDCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &a=b& \cr \color{red}{\Rightarrow}&a^2=a\cdot b& \cr \color{green}{\Leftrightarrow}&a^2-b^2=a\cdot b-b^2& \cr \color{green}{\Leftrightarrow}&\left(a-b\right)\cdot \left(a+b\right)=b\cdot \left(a-b\right)& \cr \color{red}{\Leftarrow}&a+b=b& \cr \color{green}{\Leftrightarrow}&2\cdot a=a& \cr \color{red}{\Leftarrow}&1=2& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[a=b or a=0, a^2=a*b, a^2-b^2=
a*b-b^2, (a-b)*(a+b)=b*(a-b), 
a+b=b or a-b=0, 2*a=a or a=b, 
2=1 or a=0 or a=b, a=0 or a=b]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(EMPTYCHAR, EQUIVCHAR, EQUIVCHAR, EQUIVCHAR, EQUIVCHAR, EQUIVCHAR, EQUIVCHAR, EQUIVCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &a=b\,{\mbox{ or }}\, a=0& \cr \color{green}{\Leftrightarrow}&a^2=a\cdot b& \cr \color{green}{\Leftrightarrow}&a^2-b^2=a\cdot b-b^2& \cr \color{green}{\Leftrightarrow}&\left(a-b\right)\cdot \left(a+b\right)=b\cdot \left(a-b\right)& \cr \color{green}{\Leftrightarrow}&a+b=b\,{\mbox{ or }}\, a-b=0& \cr \color{green}{\Leftrightarrow}&2\cdot a=a\,{\mbox{ or }}\, a=b& \cr \color{green}{\Leftrightarrow}&2=1\,{\mbox{ or }}\, a=0\,{\mbox{ or }}\, a=b& \cr \color{green}{\Leftrightarrow}&a=0\,{\mbox{ or }}\, a=b& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[(x^2-4)/(x-2)=0,(x-2)*(x+2)/(
x-2)=0,x+2=0,x=-2]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(EMPTYCHAR, EQUIVCHAR, EQUIVCHAR, EQUIVCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &\frac{x^2-4}{x-2}=0&{\color{blue}{{x \not\in {\left \{2 \right \}}}}}\cr \color{green}{\Leftrightarrow}&\frac{\left(x-2\right)\cdot \left(x+2\right)}{x-2}=0& \cr \color{green}{\Leftrightarrow}&x+2=0& \cr \color{green}{\Leftrightarrow}&x=-2& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[(x^2-4)/(x-2)=0,(x^2-4)=0,(x-
2)*(x+2)=0,x=-2 or x=2]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">(EMPTYCHAR,IMPLIESCHAR, EQUIVCHAR, EQUIVCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &\frac{x^2-4}{x-2}=0&{\color{blue}{{x \not\in {\left \{2 \right \}}}}}\cr \color{red}{\Rightarrow}&x^2-4=0& \cr \color{green}{\Leftrightarrow}&\left(x-2\right)\cdot \left(x+2\right)=0& \cr \color{green}{\Leftrightarrow}&x=-2\,{\mbox{ or }}\, x=2& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[5*x/(2*x+1)-3/(x+1) = 1,5*x*(
x+1)-3*(2*x+1)=(x+1)*(2*x+1),5
*x^2+5*x-6*x-3=2*x^2+3*x+1,3*x
^2-4*x-4=0,(x-2)*(3*x+2)=0,x=2
 or x=-2/3]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(EMPTYCHAR, EQUIVCHAR, EQUIVCHAR, EQUIVCHAR, EQUIVCHAR, EQUIVCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &\frac{5\cdot x}{2\cdot x+1}-\frac{3}{x+1}=1&{\color{blue}{{x \not\in {\left \{-1 , -\frac{1}{2} \right \}}}}}\cr \color{green}{\Leftrightarrow}&5\cdot x\cdot \left(x+1\right)-3\cdot \left(2\cdot x+1\right)=\left(x+1\right)\cdot \left(2\cdot x+1\right)& \cr \color{green}{\Leftrightarrow}&5\cdot x^2+5\cdot x-6\cdot x-3=2\cdot x^2+3\cdot x+1& \cr \color{green}{\Leftrightarrow}&3\cdot x^2-4\cdot x-4=0& \cr \color{green}{\Leftrightarrow}&\left(x-2\right)\cdot \left(3\cdot x+2\right)=0& \cr \color{green}{\Leftrightarrow}&x=2\,{\mbox{ or }}\, x=\frac{-2}{3}& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[(x+10)/(x-6)-5= (4*x-40)/(13-
x),(x+10-5*(x-6))/(x-6)= (4*x-
40)/(13-x), (4*x-40)/(6-x)= (4
*x-40)/(13-x),6-x= 13-x,6= 13]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">(EMPTYCHAR, EQUIVCHAR, EQUIVCHAR,QMCHAR, EQUIVCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &\frac{x+10}{x-6}-5=\frac{4\cdot x-40}{13-x}&{\color{blue}{{x \not\in {\left \{6 , 13 \right \}}}}}\cr \color{green}{\Leftrightarrow}&\frac{x+10-5\cdot \left(x-6\right)}{x-6}=\frac{4\cdot x-40}{13-x}&{\color{blue}{{x \not\in {\left \{6 , 13 \right \}}}}}\cr \color{green}{\Leftrightarrow}&\frac{4\cdot x-40}{6-x}=\frac{4\cdot x-40}{13-x}&{\color{blue}{{x \not\in {\left \{6 , 13 \right \}}}}}\cr \color{red}{?}&6-x=13-x& \cr \color{green}{\Leftrightarrow}&6=13& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[(x+5)/(x-7)-5= (4*x-40)/(13-x
),(x+5-5*(x-7))/(x-7)= (4*x-40
)/(13-x), (4*x-40)/(7-x)= (4*x
-40)/(13-x),7-x= 13-x,7= 13]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">(EMPTYCHAR, EQUIVCHAR, EQUIVCHAR,IMPLIEDCHAR, EQUIVCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &\frac{x+5}{x-7}-5=\frac{4\cdot x-40}{13-x}&{\color{blue}{{x \not\in {\left \{7 , 13 \right \}}}}}\cr \color{green}{\Leftrightarrow}&\frac{x+5-5\cdot \left(x-7\right)}{x-7}=\frac{4\cdot x-40}{13-x}&{\color{blue}{{x \not\in {\left \{7 , 13 \right \}}}}}\cr \color{green}{\Leftrightarrow}&\frac{4\cdot x-40}{7-x}=\frac{4\cdot x-40}{13-x}&{\color{blue}{{x \not\in {\left \{7 , 13 \right \}}}}}\cr \color{red}{\Leftarrow}&7-x=13-x& \cr \color{green}{\Leftrightarrow}&7=13& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[(x+5)/(x-7)-5= (4*x-40)/(13-x
),(x+5-5*(x-7))/(x-7)= (4*x-40
)/(13-x), (4*x-40)/(7-x)= (4*x
-40)/(13-x),7-x= 13-x or 4*x-4
0=0,7= 13 or 4*x=40,x=10]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(EMPTYCHAR, EQUIVCHAR, EQUIVCHAR, EQUIVCHAR, EQUIVCHAR, EQUIVCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &\frac{x+5}{x-7}-5=\frac{4\cdot x-40}{13-x}&{\color{blue}{{x \not\in {\left \{7 , 13 \right \}}}}}\cr \color{green}{\Leftrightarrow}&\frac{x+5-5\cdot \left(x-7\right)}{x-7}=\frac{4\cdot x-40}{13-x}&{\color{blue}{{x \not\in {\left \{7 , 13 \right \}}}}}\cr \color{green}{\Leftrightarrow}&\frac{4\cdot x-40}{7-x}=\frac{4\cdot x-40}{13-x}&{\color{blue}{{x \not\in {\left \{7 , 13 \right \}}}}}\cr \color{green}{\Leftrightarrow}&7-x=13-x\,{\mbox{ or }}\, 4\cdot x-40=0& \cr \color{green}{\Leftrightarrow}&7=13\,{\mbox{ or }}\, 4\cdot x=40& \cr \color{green}{\Leftrightarrow}&x=10& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[a*x^2+b*x+c=0,a=0 nounand b=0
 nounand c=0,a*x^2+b*x+c=0]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(EMPTYCHAR,EQUATECOEFFLOSS(x),EQUATECOEFFGAIN(x))</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &a\cdot x^2+b\cdot x+c=0& \cr \color{green}{\equiv (\cdots ? x)}&\left\{\begin{array}{l}a=0\cr b=0\cr c=0\cr \end{array}\right.& \cr \color{green}{(\cdots ? x)\equiv}&a\cdot x^2+b\cdot x+c=0& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[a*x^2+b*x+c=A*x^2+B*x+C,a=A n
ounand b=B nounand c=C,a*x^2+b
*x+c=A*x^2+B*x+C]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(EMPTYCHAR,EQUATECOEFFLOSS(x),EQUATECOEFFGAIN(x))</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &a\cdot x^2+b\cdot x+c=A\cdot x^2+B\cdot x+C& \cr \color{green}{\equiv (\cdots ? x)}&\left\{\begin{array}{l}a=A\cr b=B\cr c=C\cr \end{array}\right.& \cr \color{green}{(\cdots ? x)\equiv}&a\cdot x^2+b\cdot x+c=A\cdot x^2+B\cdot x+C& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[(x-1)*(x+4), stackeq(x^2-x+4*
x-4),stackeq(x^2+3*x-4)]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(EMPTYCHAR, CHECKMARK, CHECKMARK)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &\left(x-1\right)\cdot \left(x+4\right)& \cr \color{green}{\checkmark}&=x^2-x+4\cdot x-4& \cr \color{green}{\checkmark}&=x^2+3\cdot x-4& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[(x-1)*(x+4), stackeq(x^2-x+4*
x-4),stackeq(x^2+3*x-4)]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(EMPTYCHAR, CHECKMARK, CHECKMARK)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &\left(x-1\right)\cdot \left(x+4\right)& \cr \color{green}{\checkmark}&=x^2-x+4\cdot x-4& \cr \color{green}{\checkmark}&=x^2+3\cdot x-4& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[x^2-2,stackeq((x-sqrt(2))*(x+
sqrt(2)))]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(EMPTYCHAR, CHECKMARK)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &x^2-2& \cr \color{green}{\checkmark}&=\left(x-\sqrt{2}\right)\cdot \left(x+\sqrt{2}\right)& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[x^2+4,stackeq((x-2*i)*(x+2*i)
)]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(EMPTYCHAR, CHECKMARK)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &x^2+4& \cr \color{green}{\checkmark}&=\left(x-2\cdot \mathrm{i}\right)\cdot \left(x+2\cdot \mathrm{i}\right)& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[x^2+2*a*x,x^2+2*a*x+a^2-a^2,(
x+a)^2-a^2]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(EMPTYCHAR, EQUIVCHAR, EQUIVCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &x^2+2\cdot a\cdot x& \cr \color{green}{\Leftrightarrow}&x^2+2\cdot a\cdot x+a^2-a^2& \cr \color{green}{\Leftrightarrow}&{\left(x+a\right)}^2-a^2& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[x^2+2*a*x,stackeq(x^2+2*a*x+a
^2-a^2),stackeq((x+a)^2-a^2)]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(EMPTYCHAR, CHECKMARK, CHECKMARK)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &x^2+2\cdot a\cdot x& \cr \color{green}{\checkmark}&=x^2+2\cdot a\cdot x+a^2-a^2& \cr \color{green}{\checkmark}&={\left(x+a\right)}^2-a^2& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[(y-z)/(y*z)+(z-x)/(z*x)+(x-y)
/(x*y),(x*(y-z)+y*(z-x)+z*(x-y
))/(x*y*z),0]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(EMPTYCHAR, EQUIVCHAR, EQUIVCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &\frac{y-z}{y\cdot z}+\frac{z-x}{z\cdot x}+\frac{x-y}{x\cdot y}& \cr \color{green}{\Leftrightarrow}&\frac{x\cdot \left(y-z\right)+y\cdot \left(z-x\right)+z\cdot \left(x-y\right)}{x\cdot y\cdot z}& \cr \color{green}{\Leftrightarrow}&0& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[(y-z)/(y*z)+(z-x)/(z*x)+(x-y)
/(x*y),stackeq((x*(y-z)+y*(z-x
)+z*(x-y))/(x*y*z)),stackeq(0)
]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(EMPTYCHAR, CHECKMARK, CHECKMARK)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &\frac{y-z}{y\cdot z}+\frac{z-x}{z\cdot x}+\frac{x-y}{x\cdot y}& \cr \color{green}{\checkmark}&=\frac{x\cdot \left(y-z\right)+y\cdot \left(z-x\right)+z\cdot \left(x-y\right)}{x\cdot y\cdot z}& \cr \color{green}{\checkmark}&=0& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[2*(a^2*b^2+b^2*c^2+c^2*a^2)-(
a^4+b^4+c^4),stackeq(4*a^2*b^2
-(a^4+b^4+c^4+2*a^2*b^2-2*b^2*
c^2-2*c^2*a^2)),stackeq((2*a*b
)^2-(b^2+a^2-c^2)^2,(2*a*b+b^2
+a^2-c^2)*(2*a*b-b^2-a^2+c^2))
,stackeq(((a+b)^2-c^2)*(c^2-(a
-b)^2)),stackeq((a+b+c)*(a+b-c
)*(c+a-b)*(c-a+b))]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(EMPTYCHAR, CHECKMARK, CHECKMARK, CHECKMARK, CHECKMARK)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &2\cdot \left(a^2\cdot b^2+b^2\cdot c^2+c^2\cdot a^2\right)-\left(a^4+b^4+c^4\right)& \cr \color{green}{\checkmark}&=4\cdot a^2\cdot b^2-\left(a^4+b^4+c^4+2\cdot a^2\cdot b^2-2\cdot b^2\cdot c^2-2\cdot c^2\cdot a^2\right)& \cr \color{green}{\checkmark}&={\left(2\cdot a\cdot b\right)}^2-{\left(b^2+a^2-c^2\right)}^2& \cr \color{green}{\checkmark}&=\left({\left(a+b\right)}^2-c^2\right)\cdot \left(c^2-{\left(a-b\right)}^2\right)& \cr \color{green}{\checkmark}&=\left(a+b+c\right)\cdot \left(a+b-c\right)\cdot \left(c+a-b\right)\cdot \left(c-a+b\right)& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[abs(x-1/2)+abs(x+1/2)-2,stack
eq(abs(x)-1)]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">(EMPTYCHAR,QMCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &\left| x-\frac{1}{2}\right| +\left| x+\frac{1}{2}\right| -2& \cr \color{red}{?}&=\left| x\right| -1& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[11*sqrt(abs(x)+1)=25-x,11^2*(
abs(x)+1)=(25-x)^2,11^2*abs(x)
=(25-x)^2-11^2,11^4*x^2=((25-x
)^2-11^2)^2, ((25-x)^2-11^2)^2
-11^4*x^2=0,((25-x)^2-11^2-11^
2*x)*((25-x)^2-11^2+11^2*x)=0,
(x^2-50*x+504-121*x)*(x^2-50*x
+504+121*x)=0, (x-168)*(x-3)*(
x+8)*(x+63)=0]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">(EMPTYCHAR,QMCHAR, EQUIVCHAR, EQUIVCHAR, EQUIVCHAR, EQUIVCHAR, EQUIVCHAR, EQUIVCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &11\cdot \sqrt{\left| x\right| +1}=25-x& \cr \color{red}{?}&11^2\cdot \left(\left| x\right| +1\right)={\left(25-x\right)}^2& \cr \color{green}{\Leftrightarrow}&11^2\cdot \left| x\right| ={\left(25-x\right)}^2-11^2& \cr \color{green}{\Leftrightarrow}&11^4\cdot x^2={\left({\left(25-x\right)}^2-11^2\right)}^2& \cr \color{green}{\Leftrightarrow}&{\left({\left(25-x\right)}^2-11^2\right)}^2-11^4\cdot x^2=0& \cr \color{green}{\Leftrightarrow}&\left({\left(25-x\right)}^2-11^2+\left(-11^2\right)\cdot x\right)\cdot \left({\left(25-x\right)}^2-11^2+11^2\cdot x\right)=0& \cr \color{green}{\Leftrightarrow}&\left(x^2-50\cdot x+504-121\cdot x\right)\cdot \left(x^2-50\cdot x+504+121\cdot x\right)=0& \cr \color{green}{\Leftrightarrow}&\left(x-168\right)\cdot \left(x-3\right)\cdot \left(x+8\right)\cdot \left(x+63\right)=0& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[1/(x^2+1)=1/((x+%i)*(x-%i)), 
stackeq(1/(2*%i)*(1/(x-%i)-1/(
x+%i)))]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(CHECKMARK, CHECKMARK)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll}\color{green}{\checkmark}&\frac{1}{x^2+1}=\frac{1}{\left(x+\mathrm{i}\right)\cdot \left(x-\mathrm{i}\right)}& \cr \color{green}{\checkmark}&=\frac{1}{2\cdot \mathrm{i}}\cdot \left(\frac{1}{x-\mathrm{i}}-\frac{1}{x+\mathrm{i}}\right)& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[((a-b)/(a^2+a*b))/((a^2-2*a*b
+b^2)/(a^4-b^4)),stackeq(((a-b
)*(a-b)*(a+b)*(a^2+b^2))/(a*(a
+b)*(a-b)^2)),stackeq((a^2+b^2
)/a),stackeq(a+b^2/a)]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(EMPTYCHAR, CHECKMARK, CHECKMARK, CHECKMARK)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &\frac{\frac{a-b}{a^2+a\cdot b}}{\frac{a^2-2\cdot a\cdot b+b^2}{a^4-b^4}}& \cr \color{green}{\checkmark}&=\frac{\left(a-b\right)\cdot \left(a-b\right)\cdot \left(a+b\right)\cdot \left(a^2+b^2\right)}{a\cdot \left(a+b\right)\cdot {\left(a-b\right)}^2}& \cr \color{green}{\checkmark}&=\frac{a^2+b^2}{a}& \cr \color{green}{\checkmark}&=a+\frac{b^2}{a}& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[a^4+4*b^4,stackeq((a^2)^2+4*a
^2*b^2+(2*b^2)^2-4*a^2*b^2),st
ackeq((a^2+2*b^2)^2-(2*a*b)^2)
,stackeq((2*b^2-2*a*b+a^2)*(2*
b^2+2*a*b+a^2))]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(EMPTYCHAR, CHECKMARK, CHECKMARK, CHECKMARK)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &a^4+4\cdot b^4& \cr \color{green}{\checkmark}&={\left(a^2\right)}^2+4\cdot a^2\cdot b^2+{\left(2\cdot b^2\right)}^2-4\cdot a^2\cdot b^2& \cr \color{green}{\checkmark}&={\left(a^2+2\cdot b^2\right)}^2-{\left(2\cdot a\cdot b\right)}^2& \cr \color{green}{\checkmark}&=\left(2\cdot b^2-2\cdot a\cdot b+a^2\right)\cdot \left(2\cdot b^2+2\cdot a\cdot b+a^2\right)& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[sum(k,k,1,n+1),stackeq(sum(k,
k,1,n)+(n+1)),stackeq(n*(n+1)/
2 +n+1),stackeq((n+1)*(n+1+1)/
2),stackeq((n+1)*(n+2)/2)]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(EMPTYCHAR, CHECKMARK, CHECKMARK, CHECKMARK, CHECKMARK)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &\sum_{k=1}^{n+1}{k}& \cr \color{green}{\checkmark}&=\sum_{k=1}^{n}{k}+\left(n+1\right)& \cr \color{green}{\checkmark}&=\frac{n\cdot \left(n+1\right)}{2}+n+1& \cr \color{green}{\checkmark}&=\frac{\left(n+1\right)\cdot \left(n+1+1\right)}{2}& \cr \color{green}{\checkmark}&=\frac{\left(n+1\right)\cdot \left(n+2\right)}{2}& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[log((a-1)^n*product(x_i^(-a),
i,1,n)),stackeq(n*log(a-1)-a*s
um(log(x_i),i,1,n))]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(EMPTYCHAR, CHECKMARK)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &\ln \left( {\left(a-1\right)}^{n}\cdot \prod_{i=1}^{n}{\frac{1}{{{x}_{i}}^{a}}} \right)& \cr \color{green}{\checkmark}&=n\cdot \ln \left( a-1 \right)-a\cdot \sum_{i=1}^{n}{\ln \left( {x}_{i} \right)}& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[binomial(n,k)+binomial(n,k+1)
,stackeq(n!/(k!*(n-k)!)+n!/((k
+1)!*(n-k-1)!)),stackeq(n!/(k!
*(n-k)*(n-k-1)!)+n!/((k+1)!*(n
-k-1)!)),stackeq(n!/(k!*(n-k-1
)!)*(1/(n-k)+1/(k+1))),stackeq
(n!/(k!*(n-k-1)!)*((n+1)/((n-k
)*(k+1)))),stackeq((n+1)*n!/(k
!*(n-k-1)!)*(1/((k+1)*(n-k))))
,stackeq((n+1)*n!/((k+1)*k!*(n
-k)*(n-k-1)!)),stackeq(((n+1)!
/((k+1)!)*(1/((n-k)*(n-k-1)!))
)),stackeq((n+1)!/((k+1)!*(n-k
)!)),stackeq(binomial(n+1,k+1)
)]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(EMPTYCHAR, CHECKMARK, CHECKMARK, CHECKMARK, CHECKMARK, CHECKMARK, CHECKMARK, CHECKMARK, CHECKMARK, CHECKMARK)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &{{n}\choose{k}}+{{n}\choose{k+1}}& \cr \color{green}{\checkmark}&=\frac{n!}{k!\cdot \left(n-k\right)!}+\frac{n!}{\left(k+1\right)!\cdot \left(n-k-1\right)!}& \cr \color{green}{\checkmark}&=\frac{n!}{k!\cdot \left(n-k\right)\cdot \left(n-k-1\right)!}+\frac{n!}{\left(k+1\right)!\cdot \left(n-k-1\right)!}& \cr \color{green}{\checkmark}&=\frac{n!}{k!\cdot \left(n-k-1\right)!}\cdot \left(\frac{1}{n-k}+\frac{1}{k+1}\right)& \cr \color{green}{\checkmark}&=\frac{n!}{k!\cdot \left(n-k-1\right)!}\cdot \left(\frac{n+1}{\left(n-k\right)\cdot \left(k+1\right)}\right)& \cr \color{green}{\checkmark}&=\frac{\left(n+1\right)\cdot n!}{k!\cdot \left(n-k-1\right)!}\cdot \left(\frac{1}{\left(k+1\right)\cdot \left(n-k\right)}\right)& \cr \color{green}{\checkmark}&=\frac{\left(n+1\right)\cdot n!}{\left(k+1\right)\cdot k!\cdot \left(n-k\right)\cdot \left(n-k-1\right)!}& \cr \color{green}{\checkmark}&=\frac{\left(n+1\right)!}{\left(k+1\right)!}\cdot \left(\frac{1}{\left(n-k\right)\cdot \left(n-k-1\right)!}\right)& \cr \color{green}{\checkmark}&=\frac{\left(n+1\right)!}{\left(k+1\right)!\cdot \left(n-k\right)!}& \cr \color{green}{\checkmark}&={{n+1}\choose{k+1}}& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[binomial(n,k)+binomial(n,k-1)
,stackeq(n!/((k-1)!*(n-k+1)!)+
n!/(k!*(n-k)!)),stackeq(n!*k/(
k!*(n-k+1)!)+n!*(n-k+1)/(k!*(n
-k+1)!)),stackeq(n!*k/(k!*(n-k
+1)!)+n!/(k!*(n-k)!)),stackeq(
((n-k+1)*n!+k*n!)/(k!*(n-k+1)!
)),stackeq(((n+1)*n!)/(k!*(n-k
+1)!))]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(EMPTYCHAR, CHECKMARK, CHECKMARK, CHECKMARK, CHECKMARK, CHECKMARK)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &{{n}\choose{k}}+{{n}\choose{k-1}}& \cr \color{green}{\checkmark}&=\frac{n!}{\left(k-1\right)!\cdot \left(n-k+1\right)!}+\frac{n!}{k!\cdot \left(n-k\right)!}& \cr \color{green}{\checkmark}&=\frac{n!\cdot k}{k!\cdot \left(n-k+1\right)!}+\frac{n!\cdot \left(n-k+1\right)}{k!\cdot \left(n-k+1\right)!}& \cr \color{green}{\checkmark}&=\frac{n!\cdot k}{k!\cdot \left(n-k+1\right)!}+\frac{n!}{k!\cdot \left(n-k\right)!}& \cr \color{green}{\checkmark}&=\frac{\left(n-k+1\right)\cdot n!+k\cdot n!}{k!\cdot \left(n-k+1\right)!}& \cr \color{green}{\checkmark}&=\frac{\left(n+1\right)\cdot n!}{k!\cdot \left(n-k+1\right)!}& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[(x-1)^2=(x-1)*(x-1), stackeq(
x^2-2*x+1)]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(CHECKMARK, CHECKMARK)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll}\color{green}{\checkmark}&{\left(x-1\right)}^2=\left(x-1\right)\cdot \left(x-1\right)& \cr \color{green}{\checkmark}&=x^2-2\cdot x+1& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[(x-1)^2=(x-1)*(x-1), stackeq(
x^2-2*x+2)]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">(CHECKMARK,QMCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll}\color{green}{\checkmark}&{\left(x-1\right)}^2=\left(x-1\right)\cdot \left(x-1\right)& \cr \color{red}{?}&=x^2-2\cdot x+2& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[(x-2)^2=(x-1)*(x-1), stackeq(
x^2-2*x+1)]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">(QMCHAR, CHECKMARK)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll}\color{red}{?}&{\left(x-2\right)}^2=\left(x-1\right)\cdot \left(x-1\right)& \cr \color{green}{\checkmark}&=x^2-2\cdot x+1& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[4^((n+1)+1)-1= 4*4^(n+1)-1,st
ackeq(4*(4^(n+1)-1)+3)]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(CHECKMARK, CHECKMARK)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll}\color{green}{\checkmark}&4^{n+1+1}-1=4\cdot 4^{n+1}-1& \cr \color{green}{\checkmark}&=4\cdot \left(4^{n+1}-1\right)+3& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[2*x+3*y=6 and 4*x+9*y=15,2*x+
3*y=6 and -2*x=-3,3+3*y=6 and 
2*x=3,y=1 and x=3/2]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(EMPTYCHAR, EQUIVCHAR, EQUIVCHAR, EQUIVCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &\left\{\begin{array}{l}2\cdot x+3\cdot y=6\cr 4\cdot x+9\cdot y=15\cr \end{array}\right.& \cr \color{green}{\Leftrightarrow}&\left\{\begin{array}{l}2\cdot x+3\cdot y=6\cr -2\cdot x=-3\cr \end{array}\right.& \cr \color{green}{\Leftrightarrow}&\left\{\begin{array}{l}3+3\cdot y=6\cr 2\cdot x=3\cr \end{array}\right.& \cr \color{green}{\Leftrightarrow}&\left\{\begin{array}{l}y=1\cr x=\frac{3}{2}\cr \end{array}\right.& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[2*x+3*y=6 and 4*x+9*y=15,2*x+
3*y=6 and -2*x=-3,3+3*y=6 and 
2*x=3,y=1 and x=3]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">(EMPTYCHAR, EQUIVCHAR, EQUIVCHAR,QMCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &\left\{\begin{array}{l}2\cdot x+3\cdot y=6\cr 4\cdot x+9\cdot y=15\cr \end{array}\right.& \cr \color{green}{\Leftrightarrow}&\left\{\begin{array}{l}2\cdot x+3\cdot y=6\cr -2\cdot x=-3\cr \end{array}\right.& \cr \color{green}{\Leftrightarrow}&\left\{\begin{array}{l}3+3\cdot y=6\cr 2\cdot x=3\cr \end{array}\right.& \cr \color{red}{?}&\left\{\begin{array}{l}y=1\cr x=3\cr \end{array}\right.& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[x^2+y^2=8 and x=y, 2*x^2=8 an
d y=x, x^2=4 and y=x, x= #pm#2
 and y=x, (x= 2 and y=x) or (x
=-2 and y=x), (x=2 and y=2) or
 (x=-2 and y=-2)]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(EMPTYCHAR, EQUIVCHAR, EQUIVCHAR, EQUIVCHAR, EQUIVCHAR, EQUIVCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &\left\{\begin{array}{l}x^2+y^2=8\cr x=y\cr \end{array}\right.& \cr \color{green}{\Leftrightarrow}&\left\{\begin{array}{l}2\cdot x^2=8\cr y=x\cr \end{array}\right.& \cr \color{green}{\Leftrightarrow}&\left\{\begin{array}{l}x^2=4\cr y=x\cr \end{array}\right.& \cr \color{green}{\Leftrightarrow}&\left\{\begin{array}{l}x= \pm 2\cr y=x\cr \end{array}\right.& \cr \color{green}{\Leftrightarrow}&x=2\,{\mbox{ and }}\, y=x\,{\mbox{ or }}\, x=-2\,{\mbox{ and }}\, y=x& \cr \color{green}{\Leftrightarrow}&x=2\,{\mbox{ and }}\, y=2\,{\mbox{ or }}\, x=-2\,{\mbox{ and }}\, y=-2& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[x^2+y^2=5 and x*y=2, x^2+y^2-
5=0 and x*y-2=0, x^2-2*x*y+y^2
-1=0 and x^2+2*x*y+y^2-9=0, (x
-y)^2-1=0 and (x+y)^2-3^2=0, (
x-y=1 and x+y=3) or (x-y=-1 an
d x+y=3) or (x-y=1 and x+y=-3)
 or (x-y=-1 and x+y=-3), (x=1 
and y=2) or (x=2 and y=1) or (
x=-2 and y=-1) or (x=-1 and y=
-2)]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(EMPTYCHAR, EQUIVCHAR, EQUIVCHAR, EQUIVCHAR, EQUIVCHAR, EQUIVCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &\left\{\begin{array}{l}x^2+y^2=5\cr x\cdot y=2\cr \end{array}\right.& \cr \color{green}{\Leftrightarrow}&\left\{\begin{array}{l}x^2+y^2-5=0\cr x\cdot y-2=0\cr \end{array}\right.& \cr \color{green}{\Leftrightarrow}&\left\{\begin{array}{l}x^2-2\cdot x\cdot y+y^2-1=0\cr x^2+2\cdot x\cdot y+y^2-9=0\cr \end{array}\right.& \cr \color{green}{\Leftrightarrow}&\left\{\begin{array}{l}{\left(x-y\right)}^2-1=0\cr {\left(x+y\right)}^2-3^2=0\cr \end{array}\right.& \cr \color{green}{\Leftrightarrow}&x-y=1\,{\mbox{ and }}\, x+y=3\,{\mbox{ or }}\, x-y=-1\,{\mbox{ and }}\, x+y=3\,{\mbox{ or }}\, x-y=1\,{\mbox{ and }}\, x+y=-3\,{\mbox{ or }}\, x-y=-1\,{\mbox{ and }}\, x+y=-3& \cr \color{green}{\Leftrightarrow}&x=1\,{\mbox{ and }}\, y=2\,{\mbox{ or }}\, x=2\,{\mbox{ and }}\, y=1\,{\mbox{ or }}\, x=-2\,{\mbox{ and }}\, y=-1\,{\mbox{ or }}\, x=-1\,{\mbox{ and }}\, y=-2& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[4*x^2+7*x*y+4*y^2=4 and y=x-4
, 4*x^2+7*x*(x-4)+4*(x-4)^2-4=
0 and y=x-4, 15*x^2-60*x+60=0 
and y=x-4, (x-2)^2=0 and y=x-4
, x=2 and y=x-4, x=2 and y=-2]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(EMPTYCHAR, EQUIVCHAR, EQUIVCHAR, EQUIVCHAR, EQUIVCHAR, EQUIVCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &\left\{\begin{array}{l}4\cdot x^2+7\cdot x\cdot y+4\cdot y^2=4\cr y=x-4\cr \end{array}\right.& \cr \color{green}{\Leftrightarrow}&\left\{\begin{array}{l}4\cdot x^2+7\cdot x\cdot \left(x-4\right)+4\cdot {\left(x-4\right)}^2-4=0\cr y=x-4\cr \end{array}\right.& \cr \color{green}{\Leftrightarrow}&\left\{\begin{array}{l}15\cdot x^2-60\cdot x+60=0\cr y=x-4\cr \end{array}\right.& \cr \color{green}{\Leftrightarrow}&\left\{\begin{array}{l}{\left(x-2\right)}^2=0\cr y=x-4\cr \end{array}\right.& \cr \color{green}{\Leftrightarrow}&\left\{\begin{array}{l}x=2\cr y=x-4\cr \end{array}\right.& \cr \color{green}{\Leftrightarrow}&\left\{\begin{array}{l}x=2\cr y=-2\cr \end{array}\right.& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[a^2=b and a^2=1, b=a^2 and (a
=1 or a=-1), (b=1 and a=1) or 
(b=1 and a=-1)]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(EMPTYCHAR, EQUIVCHAR, EQUIVCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &\left\{\begin{array}{l}a^2=b\cr a^2=1\cr \end{array}\right.& \cr \color{green}{\Leftrightarrow}&\left\{\begin{array}{l}b=a^2\cr a=1\,{\mbox{ or }}\, a=-1\cr \end{array}\right.& \cr \color{green}{\Leftrightarrow}&b=1\,{\mbox{ and }}\, a=1\,{\mbox{ or }}\, b=1\,{\mbox{ and }}\, a=-1& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[a^2=b and x=1, b=a^2 and x=1]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(EMPTYCHAR, EQUIVCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &\left\{\begin{array}{l}a^2=b\cr x=1\cr \end{array}\right.& \cr \color{green}{\Leftrightarrow}&\left\{\begin{array}{l}b=a^2\cr x=1\cr \end{array}\right.& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[a^2=b and b^2=a, b=a^2 and a^
4=a, b=a^2 and a^4-a=0, b=a^2 
and a*(a-1)*(a^2+a+1)=0, b=a^2
 and (a=0 or a=1 or a^2+a+1=0)
, (b=0 and a=0) or (b=1 and a=
1)]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"><pre>[assumereal]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(ASSUMEREALVARS, EQUIVCHAR, EQUIVCHAR, EQUIVCHAR, EQUIVCHAR, EQUIVCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll}\color{blue}{(\mathbb{R})}&\left\{\begin{array}{l}a^2=b\cr b^2=a\cr \end{array}\right.& \cr \color{green}{\Leftrightarrow}&\left\{\begin{array}{l}b=a^2\cr a^4=a\cr \end{array}\right.& \cr \color{green}{\Leftrightarrow}&\left\{\begin{array}{l}b=a^2\cr a^4-a=0\cr \end{array}\right.& \cr \color{green}{\Leftrightarrow}&\left\{\begin{array}{l}b=a^2\cr a\cdot \left(a-1\right)\cdot \left(a^2+a+1\right)=0\cr \end{array}\right.& \cr \color{green}{\Leftrightarrow}&\left\{\begin{array}{l}b=a^2\cr a=0\,{\mbox{ or }}\, a=1\,{\mbox{ or }}\, a^2+a+1=0\cr \end{array}\right.& \cr \color{green}{\Leftrightarrow}&b=0\,{\mbox{ and }}\, a=0\,{\mbox{ or }}\, b=1\,{\mbox{ and }}\, a=1& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[2*x^3-9*x^2+10*x-3,stacklet(x
,1),2*1^3-9*1^2+10*1-3,stackeq
(0),&quot;So&quot;,2*x^3-9*x^2
+10*x-3,stackeq((x-1)*(2*x^2-7
*x+3)),stackeq((x-1)*(2*x-1)*(
x-3))]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">(EMPTYCHAR, EMPTYCHAR, EQUIVCHAR, CHECKMARK, EMPTYCHAR, EMPTYCHAR, CHECKMARK, CHECKMARK)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &2\cdot x^3-9\cdot x^2+10\cdot x-3& \cr &\mbox{Let }x = 1& \cr \color{green}{\Leftrightarrow}&2\cdot 1^3-9\cdot 1^2+10\cdot 1-3& \cr \color{green}{\checkmark}&=0& \cr &\mbox{So}& \cr &2\cdot x^3-9\cdot x^2+10\cdot x-3& \cr \color{green}{\checkmark}&=\left(x-1\right)\cdot \left(2\cdot x^2-7\cdot x+3\right)& \cr \color{green}{\checkmark}&=\left(x-1\right)\cdot \left(2\cdot x-1\right)\cdot \left(x-3\right)& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[2*x^2+x&gt;=6, 2*x^2+x-6&gt;=
0, (2*x-3)*(x+2)&gt;= 0,((2*x-
3)&gt;=0 and (x+2)&gt;=0) or (
(2*x-3)&lt;=0 and (x+2)&lt;=0)
, (x&gt;=3/2 and x&gt;=-2) or 
(x&lt;=3/2 and x&lt;=-2), x&gt
;=3/2 or x &lt;=-2]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(EMPTYCHAR, EQUIVCHAR, EQUIVCHAR, EQUIVCHAR, EQUIVCHAR, EQUIVCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &2\cdot x^2+x\geq 6& \cr \color{green}{\Leftrightarrow}&2\cdot x^2+x-6\geq 0& \cr \color{green}{\Leftrightarrow}&\left(2\cdot x-3\right)\cdot \left(x+2\right)\geq 0& \cr \color{green}{\Leftrightarrow}&2\cdot x-3\geq 0\,{\mbox{ and }}\, x+2\geq 0\,{\mbox{ or }}\, 2\cdot x-3\leq 0\,{\mbox{ and }}\, x+2\leq 0& \cr \color{green}{\Leftrightarrow}&x\geq \frac{3}{2}\,{\mbox{ and }}\, x\geq -2\,{\mbox{ or }}\, x\leq \frac{3}{2}\,{\mbox{ and }}\, x\leq -2& \cr \color{green}{\Leftrightarrow}&x\geq \frac{3}{2}\,{\mbox{ or }}\, x\leq -2& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[2*x^2+x&gt;=6, 2*x^2+x-6&gt;=
0, (2*x-3)*(x+2)&gt;= 0,((2*x-
3)&gt;=0 and (x+2)&gt;=0) or (
(2*x-3)&lt;=0 and (x+2)&lt;=0)
, (x&gt;=3/2 and x&gt;=-2) or 
(x&lt;=3/2 and x&lt;=-2), x&gt
;=3/2 or x &lt;=-2]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(EMPTYCHAR, EQUIVCHAR, EQUIVCHAR, EQUIVCHAR, EQUIVCHAR, EQUIVCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &2\cdot x^2+x\geq 6& \cr \color{green}{\Leftrightarrow}&2\cdot x^2+x-6\geq 0& \cr \color{green}{\Leftrightarrow}&\left(2\cdot x-3\right)\cdot \left(x+2\right)\geq 0& \cr \color{green}{\Leftrightarrow}&2\cdot x-3\geq 0\,{\mbox{ and }}\, x+2\geq 0\,{\mbox{ or }}\, 2\cdot x-3\leq 0\,{\mbox{ and }}\, x+2\leq 0& \cr \color{green}{\Leftrightarrow}&x\geq \frac{3}{2}\,{\mbox{ and }}\, x\geq -2\,{\mbox{ or }}\, x\leq \frac{3}{2}\,{\mbox{ and }}\, x\leq -2& \cr \color{green}{\Leftrightarrow}&x\geq \frac{3}{2}\,{\mbox{ or }}\, x\leq -2& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[2*x^2+x&gt;=6, 2*x^2+x-6&gt;=
0, (2*x-3)*(x+2)&gt;= 0,((2*x-
3)&gt;=0 and (x+2)&gt;=0) or (
(2*x-3)&lt;=0 and (x+2)&lt;=0)
, (x&gt;=3/2 and x&gt;=-2) or 
(x&lt;=3/2 and x&lt;=-2), x&gt
;=3/2 or x &lt;=2]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">(EMPTYCHAR, EQUIVCHAR, EQUIVCHAR, EQUIVCHAR, EQUIVCHAR,QMCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &2\cdot x^2+x\geq 6& \cr \color{green}{\Leftrightarrow}&2\cdot x^2+x-6\geq 0& \cr \color{green}{\Leftrightarrow}&\left(2\cdot x-3\right)\cdot \left(x+2\right)\geq 0& \cr \color{green}{\Leftrightarrow}&2\cdot x-3\geq 0\,{\mbox{ and }}\, x+2\geq 0\,{\mbox{ or }}\, 2\cdot x-3\leq 0\,{\mbox{ and }}\, x+2\leq 0& \cr \color{green}{\Leftrightarrow}&x\geq \frac{3}{2}\,{\mbox{ and }}\, x\geq -2\,{\mbox{ or }}\, x\leq \frac{3}{2}\,{\mbox{ and }}\, x\leq -2& \cr \color{red}{?}&x\geq \frac{3}{2}\,{\mbox{ or }}\, x\leq 2& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[x^2&gt;=9 and x&gt;3, x^2-9&g
t;=0 and x&gt;3, (x&gt;=3 or x
&lt;=-3) and x&gt;3, x&gt;3]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(EMPTYCHAR, EQUIVCHAR, EQUIVCHAR, EQUIVCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &\left\{\begin{array}{l}x^2\geq 9\cr x > 3\cr \end{array}\right.& \cr \color{green}{\Leftrightarrow}&\left\{\begin{array}{l}x^2-9\geq 0\cr x > 3\cr \end{array}\right.& \cr \color{green}{\Leftrightarrow}&\left\{\begin{array}{l}x\geq 3\,{\mbox{ or }}\, x\leq -3\cr x > 3\cr \end{array}\right.& \cr \color{green}{\Leftrightarrow}&x > 3& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[-x^2+a*x+a-3&lt;0, a-3&lt;x^2
-a*x, a-3&lt;(x-a/2)^2-a^2/4, 
a^2/4+a-3&lt;(x-a/2)^2, a^2+4*
a-12&lt;4*(x-a/2)^2, (a-2)*(a+
6)&lt;4*(x-a/2)^2, &quot;This 
inequality is required to be t
rue for all x.&quot;, &quot;So
 it must be true when the righ
t hand side takes its minimum 
value.&quot;, &quot;This happe
ns for x=a/2.&quot;, (a-2)*(a+
6)&lt;0, ((a-2)&lt;0 and (a+6)
&gt;0) or ((a-2)&gt;0 and (a+6
)&lt;0), (a&lt;2 and a&gt;-6) 
or (a&gt;2 and a&lt;-6), (-6&l
t;a and a&lt;2) or false, (-6&
lt;a and a&lt;2)]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">(EMPTYCHAR, EQUIVCHAR, EQUIVCHAR, EQUIVCHAR, EQUIVCHAR, EQUIVCHAR, EMPTYCHAR, EMPTYCHAR, EMPTYCHAR, EMPTYCHAR, EQUIVCHAR, EQUIVCHAR, EQUIVCHAR, EQUIVCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &-x^2+a\cdot x+a-3 < 0& \cr \color{green}{\Leftrightarrow}&a-3 < x^2-a\cdot x& \cr \color{green}{\Leftrightarrow}&a-3 < {\left(x-\frac{a}{2}\right)}^2-\frac{a^2}{4}& \cr \color{green}{\Leftrightarrow}&\frac{a^2}{4}+a-3 < {\left(x-\frac{a}{2}\right)}^2& \cr \color{green}{\Leftrightarrow}&a^2+4\cdot a-12 < 4\cdot {\left(x-\frac{a}{2}\right)}^2& \cr \color{green}{\Leftrightarrow}&\left(a-2\right)\cdot \left(a+6\right) < 4\cdot {\left(x-\frac{a}{2}\right)}^2& \cr &\mbox{This inequality is required to be true for all x.}& \cr &\mbox{So it must be true when the right hand side takes its minimum value.}& \cr &\mbox{This happens for x=a/2.}& \cr &\left(a-2\right)\cdot \left(a+6\right) < 0& \cr \color{green}{\Leftrightarrow}&a-2 < 0\,{\mbox{ and }}\, a+6 > 0\,{\mbox{ or }}\, a-2 > 0\,{\mbox{ and }}\, a+6 < 0& \cr \color{green}{\Leftrightarrow}&a < 2\,{\mbox{ and }}\, a > -6\,{\mbox{ or }}\, a > 2\,{\mbox{ and }}\, a < -6& \cr \color{green}{\Leftrightarrow}&-6 < a\,{\mbox{ and }}\, a < 2\,{\mbox{ or }}\, \mathbf{False}& \cr \color{green}{\Leftrightarrow}&\left\{\begin{array}{l}-6 < a\cr a < 2\cr \end{array}\right.& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[x-2&gt;0 and x*(x-2)&lt;15,x&
gt;2 and x^2-2*x-15&lt;0,x&gt;
2 and (x-5)*(x+3)&lt;0,x&gt;2 
and ((x&lt;5 and x&gt;-3) or (
x&gt;5 and x&lt;-3)),x&gt;2 an
d (x&lt;5 and x&gt;-3),x&gt;2 
and x&lt;5]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(EMPTYCHAR, EQUIVCHAR, EQUIVCHAR, EQUIVCHAR, EQUIVCHAR, EQUIVCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &\left\{\begin{array}{l}x-2 > 0\cr x\cdot \left(x-2\right) < 15\cr \end{array}\right.& \cr \color{green}{\Leftrightarrow}&\left\{\begin{array}{l}x > 2\cr x^2-2\cdot x-15 < 0\cr \end{array}\right.& \cr \color{green}{\Leftrightarrow}&\left\{\begin{array}{l}x > 2\cr \left(x-5\right)\cdot \left(x+3\right) < 0\cr \end{array}\right.& \cr \color{green}{\Leftrightarrow}&\left\{\begin{array}{l}x > 2\cr x < 5\,{\mbox{ and }}\, x > -3\,{\mbox{ or }}\, x > 5\,{\mbox{ and }}\, x < -3\cr \end{array}\right.& \cr \color{green}{\Leftrightarrow}&\left\{\begin{array}{l}x > 2\cr x < 5\,{\mbox{ and }}\, x > -3\cr \end{array}\right.& \cr \color{green}{\Leftrightarrow}&\left\{\begin{array}{l}x > 2\cr x < 5\cr \end{array}\right.& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[x-2&gt;0 and x*(x-2)&lt;15,x&
gt;2 and x^2-2*x-15&lt;0,x&gt;
2 and (x-5)*(x+3)&lt;0,x&gt;2 
and ((x&lt;5 and x&gt;-3) or (
x&gt;5 and x&lt;-3)),x&gt;7 an
d (x&lt;5 and x&gt;-3),x&gt;2 
and x&lt;5]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">(EMPTYCHAR, EQUIVCHAR, EQUIVCHAR, EQUIVCHAR,QMCHAR,QMCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &\left\{\begin{array}{l}x-2 > 0\cr x\cdot \left(x-2\right) < 15\cr \end{array}\right.& \cr \color{green}{\Leftrightarrow}&\left\{\begin{array}{l}x > 2\cr x^2-2\cdot x-15 < 0\cr \end{array}\right.& \cr \color{green}{\Leftrightarrow}&\left\{\begin{array}{l}x > 2\cr \left(x-5\right)\cdot \left(x+3\right) < 0\cr \end{array}\right.& \cr \color{green}{\Leftrightarrow}&\left\{\begin{array}{l}x > 2\cr x < 5\,{\mbox{ and }}\, x > -3\,{\mbox{ or }}\, x > 5\,{\mbox{ and }}\, x < -3\cr \end{array}\right.& \cr \color{red}{?}&\left\{\begin{array}{l}x > 7\cr x < 5\,{\mbox{ and }}\, x > -3\cr \end{array}\right.& \cr \color{red}{?}&\left\{\begin{array}{l}x > 2\cr x < 5\cr \end{array}\right.& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[x^2 + (a-2)*x + a = 0,(x + (a
-2)/2)^2 -((a-2)/2)^2 + a = 0,
(x + (a-2)/2)^2 =(a-2)^2/4 - a
,&quot;This has real roots iff
&quot;,(a-2)^2/4-a &gt;=0,a^2-
4*a+4-4*a &gt;=0,a^2-8*a+4&gt;
=0,(a-4)^2-16+4&gt;=0,(a-4)^2&
gt;=12,a-4&gt;=sqrt(12) or a-4
&lt;= -sqrt(12),&quot;Ignoring
 the negative solution.&quot;,
a&gt;=sqrt(12)+4,&quot;Using e
xternal domain information tha
t a is an integer.&quot;,a&gt;
=8]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">(EMPTYCHAR, EQUIVCHAR, EQUIVCHAR, EMPTYCHAR, EMPTYCHAR, EQUIVCHAR, EQUIVCHAR, EQUIVCHAR, EQUIVCHAR, EQUIVCHAR, EMPTYCHAR, EMPTYCHAR, EMPTYCHAR, EMPTYCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &x^2+\left(a-2\right)\cdot x+a=0& \cr \color{green}{\Leftrightarrow}&{\left(x+\frac{a-2}{2}\right)}^2-{\left(\frac{a-2}{2}\right)}^2+a=0& \cr \color{green}{\Leftrightarrow}&{\left(x+\frac{a-2}{2}\right)}^2=\frac{{\left(a-2\right)}^2}{4}-a& \cr &\mbox{This has real roots iff}& \cr &\frac{{\left(a-2\right)}^2}{4}-a\geq 0& \cr \color{green}{\Leftrightarrow}&a^2-4\cdot a+4-4\cdot a\geq 0& \cr \color{green}{\Leftrightarrow}&a^2-8\cdot a+4\geq 0& \cr \color{green}{\Leftrightarrow}&{\left(a-4\right)}^2-16+4\geq 0& \cr \color{green}{\Leftrightarrow}&{\left(a-4\right)}^2\geq 12& \cr \color{green}{\Leftrightarrow}&a-4\geq \sqrt{12}\,{\mbox{ or }}\, a-4\leq -\sqrt{12}& \cr &\mbox{Ignoring the negative solution.}& \cr &a\geq \sqrt{12}+4& \cr &\mbox{Using external domain information that a is an integer.}& \cr &a\geq 8& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[x^2#1,x^2-1#0,(x-1)*(x+1)#0,x
&lt;-1 nounor (-1&lt;x nounand
 x&lt;1) nounor x&gt;1]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(EMPTYCHAR, EQUIVCHAR, EQUIVCHAR, EQUIVCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &x^2\neq 1& \cr \color{green}{\Leftrightarrow}&x^2-1\neq 0& \cr \color{green}{\Leftrightarrow}&\left(x-1\right)\cdot \left(x+1\right)\neq 0& \cr \color{green}{\Leftrightarrow}&x < -1\,{\mbox{ or }}\, -1 < x\,{\mbox{ and }}\, x < 1\,{\mbox{ or }}\, x > 1& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[&quot;Set P(n) be the stateme
nt that&quot;,sum(k^2,k,1,n) =
 n*(n+1)*(2*n+1)/6, &quot;Then
 P(1) is the statement&quot;, 
1^2 = 1*(1+1)*(2*1+1)/6, 1 = 1
, &quot;So P(1) holds.  Now as
sume P(n) is true.&quot;,sum(k
^2,k,1,n) = n*(n+1)*(2*n+1)/6,
sum(k^2,k,1,n) +(n+1)^2= n*(n+
1)*(2*n+1)/6 +(n+1)^2,sum(k^2,
k,1,n+1)= (n+1)*(n*(2*n+1) +6*
(n+1))/6,sum(k^2,k,1,n+1)= (n+
1)*(2*n^2+7*n+6)/6,sum(k^2,k,1
,n+1)= (n+1)*(n+1+1)*(2*(n+1)+
1)/6]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">(EMPTYCHAR, EMPTYCHAR, EMPTYCHAR, EMPTYCHAR, EQUIVCHAR, EMPTYCHAR, EMPTYCHAR, EQUIVCHAR, EQUIVCHAR, EQUIVCHAR, EQUIVCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &\mbox{Set P(n) be the statement that}& \cr &\sum_{k=1}^{n}{k^2}=\frac{n\cdot \left(n+1\right)\cdot \left(2\cdot n+1\right)}{6}& \cr &\mbox{Then P(1) is the statement}& \cr &1^2=\frac{1\cdot \left(1+1\right)\cdot \left(2\cdot 1+1\right)}{6}& \cr \color{green}{\Leftrightarrow}&1=1& \cr &\mbox{So P(1) holds. Now assume P(n) is true.}& \cr &\sum_{k=1}^{n}{k^2}=\frac{n\cdot \left(n+1\right)\cdot \left(2\cdot n+1\right)}{6}& \cr \color{green}{\Leftrightarrow}&\sum_{k=1}^{n}{k^2}+{\left(n+1\right)}^2=\frac{n\cdot \left(n+1\right)\cdot \left(2\cdot n+1\right)}{6}+{\left(n+1\right)}^2& \cr \color{green}{\Leftrightarrow}&\sum_{k=1}^{n+1}{k^2}=\frac{\left(n+1\right)\cdot \left(n\cdot \left(2\cdot n+1\right)+6\cdot \left(n+1\right)\right)}{6}& \cr \color{green}{\Leftrightarrow}&\sum_{k=1}^{n+1}{k^2}=\frac{\left(n+1\right)\cdot \left(2\cdot n^2+7\cdot n+6\right)}{6}& \cr \color{green}{\Leftrightarrow}&\sum_{k=1}^{n+1}{k^2}=\frac{\left(n+1\right)\cdot \left(n+1+1\right)\cdot \left(2\cdot \left(n+1\right)+1\right)}{6}& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[(n+1)^2+sum(k^2,k,1,n) = (n+1
)^2+(n*(n+1)*(2*n+1))/6, sum(k
^2,k,1,n+1) = ((n+1)*(n*(2*n+1
)+6*(n+1)))/6, sum(k^2,k,1,n+1
) = ((n+1)*(2*n^2+7*n+6))/6, s
um(k^2,k,1,n+1) = ((n+1)*(n+2)
*(2*(n+1)+1))/6]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(EMPTYCHAR, EQUIVCHAR, EQUIVCHAR, EQUIVCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &{\left(n+1\right)}^2+\sum_{k=1}^{n}{k^2}={\left(n+1\right)}^2+\frac{n\cdot \left(n+1\right)\cdot \left(2\cdot n+1\right)}{6}& \cr \color{green}{\Leftrightarrow}&\sum_{k=1}^{n+1}{k^2}=\frac{\left(n+1\right)\cdot \left(n\cdot \left(2\cdot n+1\right)+6\cdot \left(n+1\right)\right)}{6}& \cr \color{green}{\Leftrightarrow}&\sum_{k=1}^{n+1}{k^2}=\frac{\left(n+1\right)\cdot \left(2\cdot n^2+7\cdot n+6\right)}{6}& \cr \color{green}{\Leftrightarrow}&\sum_{k=1}^{n+1}{k^2}=\frac{\left(n+1\right)\cdot \left(n+2\right)\cdot \left(2\cdot \left(n+1\right)+1\right)}{6}& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[conjugate(a)*conjugate(b),sta
cklet(a,x+i*y),stacklet(b,r+i*
s),stackeq(conjugate(x+i*y)*co
njugate(r+i*s)),stackeq((x-i*y
)*(r-i*s)),stackeq((x*r-y*s)-i
*(y*r+x*s)),stackeq(conjugate(
(x*r-y*s)+i*(y*r+x*s))),stacke
q(conjugate((x+i*y)*(r+i*s))),
stacklet(x+i*y,a),stacklet(r+i
*s,b),stackeq(conjugate(a*b))]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(EMPTYCHAR, EMPTYCHAR, EMPTYCHAR, CHECKMARK, CHECKMARK, CHECKMARK, CHECKMARK, CHECKMARK, EMPTYCHAR, EMPTYCHAR, CHECKMARK)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &a^\star\cdot b^\star& \cr &\mbox{Let }a = x+\mathrm{i}\cdot y& \cr &\mbox{Let }b = r+\mathrm{i}\cdot s& \cr \color{green}{\checkmark}&=\left(x+\mathrm{i}\cdot y\right)^\star\cdot \left(r+\mathrm{i}\cdot s\right)^\star& \cr \color{green}{\checkmark}&=\left(x-\mathrm{i}\cdot y\right)\cdot \left(r-\mathrm{i}\cdot s\right)& \cr \color{green}{\checkmark}&=x\cdot r-y\cdot s-\mathrm{i}\cdot \left(y\cdot r+x\cdot s\right)& \cr \color{green}{\checkmark}&=\left(x\cdot r-y\cdot s+\mathrm{i}\cdot \left(y\cdot r+x\cdot s\right)\right)^\star& \cr \color{green}{\checkmark}&=\left(\left(x+\mathrm{i}\cdot y\right)\cdot \left(r+\mathrm{i}\cdot s\right)\right)^\star& \cr &\mbox{Let }x+\mathrm{i}\cdot y = a& \cr &\mbox{Let }r+\mathrm{i}\cdot s = b& \cr \color{green}{\checkmark}&=\left(a\cdot b\right)^\star& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[nounint(x*e^x,x,-inf,0),nounl
imit(nounint(x*e^x,x,t,0),t,-i
nf),nounlimit(e^t-t*e^t-1,t,-i
nf),nounlimit(e^t,t,-inf)+noun
limit(-t*e^t,t,-inf)+nounlimit
(-1,t,-inf),-1]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(EMPTYCHAR, EQUIVCHAR, EQUIVCHAR, EQUIVCHAR, EQUIVCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &\int_{-\infty }^{0}{x\cdot e^{x}\;\mathrm{d}x}& \cr \color{green}{\Leftrightarrow}&\lim_{t\rightarrow -\infty }{\int_{t}^{0}{x\cdot e^{x}\;\mathrm{d}x}}& \cr \color{green}{\Leftrightarrow}&\lim_{t\rightarrow -\infty }{e^{t}-t\cdot e^{t}-1}& \cr \color{green}{\Leftrightarrow}&\lim_{t\rightarrow -\infty }{e^{t}}+\lim_{t\rightarrow -\infty }{\left(-t\right)\cdot e^{t}}+\lim_{t\rightarrow -\infty }{-1}& \cr \color{green}{\Leftrightarrow}&-1& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[noundiff(x^2,x),stackeq(nounl
imit(((x+h)^2-x^2)/h,h,0)),sta
ckeq(nounlimit(2*x+h,h,0)),sta
ckeq(2*x)]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(EMPTYCHAR, CHECKMARK, CHECKMARK, CHECKMARK)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &\frac{\mathrm{d}}{\mathrm{d} x} x^2& \cr \color{green}{\checkmark}&=\lim_{h\rightarrow 0}{\frac{{\left(x+h\right)}^2-x^2}{h}}& \cr \color{green}{\checkmark}&=\lim_{h\rightarrow 0}{2\cdot x+h}& \cr \color{green}{\checkmark}&=2\cdot x& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[-12+3*noundiff(y(x),x)+8-8*no
undiff(y(x),x)=0,-5*noundiff(y
(x),x)=4,noundiff(y(x),x)=-4/5
]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"><pre>[calculus]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(EMPTYCHAR, EQUIVCHAR, EQUIVCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &-12+3\cdot \left(\frac{\mathrm{d}}{\mathrm{d} x} y\left(x\right)\right)+8-8\cdot \left(\frac{\mathrm{d}}{\mathrm{d} x} y\left(x\right)\right)=0& \cr \color{green}{\Leftrightarrow}&-5\cdot \left(\frac{\mathrm{d}}{\mathrm{d} x} y\left(x\right)\right)=4& \cr \color{green}{\Leftrightarrow}&\left(\frac{\mathrm{d}}{\mathrm{d} x} y\left(x\right)\right)=\frac{-4}{5}& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[x^2+1,x^3/3+x,x^2+1,x^3/3+x+c
]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"><pre>[calculus]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(EMPTYCHAR,INTCHAR(x),DIFFCHAR(x),INTCHAR(x))</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &x^2+1& \cr \color{blue}{\int\ldots\mathrm{d}x}&\frac{x^3}{3}+x& \cr \color{blue}{\frac{\mathrm{d}}{\mathrm{d}x}\ldots}&x^2+1& \cr \color{blue}{\int\ldots\mathrm{d}x}&\frac{x^3}{3}+x+c& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[3*x^(3/2)-2/x,(9*sqrt(x))/2+2
/x^2,3*x^(3/2)-2/x+c]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"><pre>[calculus]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(EMPTYCHAR,DIFFCHAR(x),INTCHAR(x))</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &3\cdot x^{\frac{3}{2}}-\frac{2}{x}&{\color{blue}{{x \not\in {\left \{0 \right \}}}}}\cr \color{blue}{\frac{\mathrm{d}}{\mathrm{d}x}\ldots}&\frac{9\cdot \sqrt{x}}{2}+\frac{2}{x^2}&{\color{blue}{{x \in {\left( 0,\, \infty \right)}}}}\cr \color{blue}{\int\ldots\mathrm{d}x}&3\cdot x^{\frac{3}{2}}-\frac{2}{x}+c& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[x^2+1,stackeq(x^3/3+x),stacke
q(x^2+1),stackeq(x^3/3+x+c)]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"><pre>[calculus]</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">(EMPTYCHAR,QMCHAR,QMCHAR,QMCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &x^2+1& \cr \color{red}{?}&=\frac{x^3}{3}+x& \cr \color{red}{?}&=x^2+1& \cr \color{red}{?}&=\frac{x^3}{3}+x+c& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[diff(x^2*sin(x),x),stackeq(x^
2*diff(sin(x),x)+diff(x^2,x)*s
in(x)),stackeq(x^2*cos(x)+2*x*
sin(x))]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"><pre>[calculus]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(EMPTYCHAR, CHECKMARK, CHECKMARK)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &\cos \left( x \right)\cdot x^2+2\cdot x\cdot \sin \left( x \right)& \cr \color{green}{\checkmark}&=x^2\cdot \cos \left( x \right)+2\cdot x\cdot \sin \left( x \right)& \cr \color{green}{\checkmark}&=x^2\cdot \cos \left( x \right)+2\cdot x\cdot \sin \left( x \right)& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[y(x)*cos(x)+y(x)^2 = 6*x,cos(
x)*diff(y(x),x)+2*y(x)*diff(y(
x),x)-y(x)*sin(x) = 6,(cos(x)+
2*y(x))*diff(y(x),x) = y(x)*si
n(x)+6,diff(y(x),x) = (y(x)*si
n(x)+6)/(cos(x)+2*y(x))]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"><pre>[calculus]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(EMPTYCHAR,DIFFCHAR(x), EQUIVCHAR, EQUIVCHAR)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &y\left(x\right)\cdot \cos \left( x \right)+y^2\left(x\right)=6\cdot x& \cr \color{blue}{\frac{\mathrm{d}}{\mathrm{d}x}\ldots}&\cos \left( x \right)\cdot \left(\frac{\mathrm{d}}{\mathrm{d} x} y\left(x\right)\right)+2\cdot y\left(x\right)\cdot \left(\frac{\mathrm{d}}{\mathrm{d} x} y\left(x\right)\right)+\left(-y\left(x\right)\right)\cdot \sin \left( x \right)=6& \cr \color{green}{\Leftrightarrow}&\left(\cos \left( x \right)+2\cdot y\left(x\right)\right)\cdot \left(\frac{\mathrm{d}}{\mathrm{d} x} y\left(x\right)\right)=y\left(x\right)\cdot \sin \left( x \right)+6& \cr \color{green}{\Leftrightarrow}&\left(\frac{\mathrm{d}}{\mathrm{d} x} y\left(x\right)\right)=\frac{y\left(x\right)\cdot \sin \left( x \right)+6}{\cos \left( x \right)+2\cdot y\left(x\right)}& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[nounint(s^2+1,s),stackeq(s^3/
3+s+c)]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"><pre>[calculus]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(EMPTYCHAR,INTCHAR(s))</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &\int {s^2+1}{\;\mathrm{d}s}& \cr \color{blue}{\int\ldots\mathrm{d}s}&=\frac{s^3}{3}+s+c& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[nounint(x^3*log(x),x),x^4/4*l
og(x)-1/4*nounint(x^3,x),x^4/4
*log(x)-x^4/16]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"><pre>[calculus]</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">(EMPTYCHAR, EQUIVCHAR,PLUSC)</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &\int {x^3\cdot \ln \left( x \right)}{\;\mathrm{d}x}& \cr \color{green}{\Leftrightarrow}&\frac{x^4}{4}\cdot \ln \left( x \right)-\frac{1}{4}\cdot \int {x^3}{\;\mathrm{d}x}& \cr \color{red}{\cdots +c\quad ?}&\frac{x^4}{4}\cdot \ln \left( x \right)-\frac{x^4}{16}&{\color{blue}{{x \in {\left( 0,\, \infty \right)}}}}\cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[nounint(x^3*log(x),x),x^4/4*l
og(x)-1/4*nounint(x^3,x),x^4/4
*log(x)-x^4/16+c]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"><pre>[calculus]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(EMPTYCHAR, EQUIVCHAR,INTCHAR(x))</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &\int {x^3\cdot \ln \left( x \right)}{\;\mathrm{d}x}& \cr \color{green}{\Leftrightarrow}&\frac{x^4}{4}\cdot \ln \left( x \right)-\frac{1}{4}\cdot \int {x^3}{\;\mathrm{d}x}& \cr \color{blue}{\int\ldots\mathrm{d}x}&\frac{x^4}{4}\cdot \ln \left( x \right)-\frac{x^4}{16}+c& \cr \end{array}\]</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Equiv</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>[noundiff(y,x)-2/x*y=x^3*sin(3
*x),1/x^2*noundiff(y,x)-2/x^3*
y=x*sin(3*x),noundiff(y/x^2,x)
=x*sin(3*x),y/x^2 = nounint(x*
sin(3*x),x),y/x^2=(sin(3*x)-3*
x*cos(3*x))/9+c]</pre></td>
  <td class="cell c3"><pre>[]</pre></td>
  <td class="cell c4"><pre>[calculus]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">(EMPTYCHAR, EQUIVCHAR, EQUIVCHAR,INTCHAR(x),INTCHAR(x))</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">\[\begin{array}{lll} &\frac{\mathrm{d} y}{\mathrm{d} x}-\frac{2}{x}\cdot y=x^3\cdot \sin \left( 3\cdot x \right)& \cr \color{green}{\Leftrightarrow}&\frac{1}{x^2}\cdot \left(\frac{\mathrm{d} y}{\mathrm{d} x}\right)-\frac{2}{x^3}\cdot y=x\cdot \sin \left( 3\cdot x \right)& \cr \color{green}{\Leftrightarrow}&\left(\frac{\mathrm{d}}{\mathrm{d} x} \frac{y}{x^2}\right)=x\cdot \sin \left( 3\cdot x \right)& \cr \color{blue}{\int\ldots\mathrm{d}x}&\frac{y}{x^2}=\int {x\cdot \sin \left( 3\cdot x \right)}{\;\mathrm{d}x}& \cr \color{blue}{\int\ldots\mathrm{d}x}&\frac{y}{x^2}=\frac{\sin \left( 3\cdot x \right)-3\cdot x\cdot \cos \left( 3\cdot x \right)}{9}+c& \cr \end{array}\]</td></td>
</tr></tbody></table></div>
# Sets: Answer test results

This page exposes the results of running answer tests on STACK test cases.  This page is automatically generated from the STACK unit tests and is designed to show question authors what answer tests actually do.  This includes cases where answer tests currentl fail, which gives a negative expected mark.  Comments and further test cases are very welcome.



<h2>Sets</h2><div class="no-overflow"><table class="flexible table table-striped table-hover generaltable generalbox stacktestsuite"><thead><tr><th class="header c0" scope="col">Test<div class="commands"></div></th><th class="header c1" scope="col">?<div class="commands"></div></th><th class="header c2" scope="col">Student response<div class="commands"></div></th><th class="header c3" scope="col">Teacher answer<div class="commands"></div></th><th class="header c4" scope="col">Opt<div class="commands"></div></th><th class="header c5" scope="col">Mark<div class="commands"></div></th><th class="header c6" scope="col">Answer note<div class="commands"></div></th>
</tr></thead><tbody>
<tr class="expectedfail">
  <td class="cell c0">Sets</td>
  <td class="cell c1"><span style="color:orange;"><i class="fa fa-adjust"></i></span></td>
  <td class="cell c2"><pre>{1/0}</pre></td>
  <td class="cell c3"><pre>{0}</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">-1</td>
  <td class="cell c6">ATSets_STACKERROR_SAns.</td>
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
  <td class="cell c0">Sets</td>
  <td class="cell c1"><span style="color:orange;"><i class="fa fa-adjust"></i></span></td>
  <td class="cell c2"><pre>{0}</pre></td>
  <td class="cell c3"><pre>{1/0}</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">-1</td>
  <td class="cell c6">ATSets_STACKERROR_TAns.</td>
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
  <td class="cell c0">Sets</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>x</pre></td>
  <td class="cell c3"><pre>{1,2,3}</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATSets_SA_not_set.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer should be a set, but is not. Note that the syntax to enter a set is to enclose the comma separated values with curly brackets.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Sets</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>{1,2}</pre></td>
  <td class="cell c3"><pre>x</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATSets_SB_not_set.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The "Sets" answer test expects its second argument to be a set. This is an error. Please contact your teacher.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Sets</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>{1,2}</pre></td>
  <td class="cell c3"><pre>{1,2,3}</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATSets_missingentries.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The following are missing from your set. <span class="filter_mathjaxloader_equation"><span class="nolink">\[\left \{3 \right \}\]</span></span></td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Sets</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>{1,2,4}</pre></td>
  <td class="cell c3"><pre>{1,2}</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATSets_wrongentries.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">These entries should not be elements of your set. <span class="filter_mathjaxloader_equation"><span class="nolink">\[\left \{4 \right \}\]</span></span></td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Sets</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>{1,2,2+2}</pre></td>
  <td class="cell c3"><pre>{1,2}</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATSets_wrongentries.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">These entries should not be elements of your set. <span class="filter_mathjaxloader_equation"><span class="nolink">\[\left \{4 \right \}\]</span></span></td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Sets</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>{5,1,2,4}</pre></td>
  <td class="cell c3"><pre>{1,2,3}</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATSets_wrongentries. ATSets_missingentries.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">These entries should not be elements of your set. <span class="filter_mathjaxloader_equation"><span class="nolink">\[\left \{4 , 5 \right \}\]</span></span> The following are missing from your set. <span class="filter_mathjaxloader_equation"><span class="nolink">\[\left \{3 \right \}\]</span></span></td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Sets</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>{2/4, 1/3}</pre></td>
  <td class="cell c3"><pre>{1/2, 1/3}</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="notes">
  <td class="cell c0"><td colspan="6">Duplicate entries</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Sets</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>{1,2,1}</pre></td>
  <td class="cell c3"><pre>{1,2}</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATSets_duplicates.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your set appears to contain duplicate entries!</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Sets</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>{1,2,1+1}</pre></td>
  <td class="cell c3"><pre>{1,2}</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATSets_duplicates.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your set appears to contain duplicate entries!</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Sets</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>{1,2,1+1}</pre></td>
  <td class="cell c3"><pre>{1,2,3}</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATSets_duplicates. ATSets_missingentries.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your set appears to contain duplicate entries! The following are missing from your set. <span class="filter_mathjaxloader_equation"><span class="nolink">\[\left \{3 \right \}\]</span></span></td></td>
</tr>
<tr class="pass">
  <td class="cell c0">Sets</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>{(x-a)^6000}</pre></td>
  <td class="cell c3"><pre>{(a-x)^6000}</pre></td>
  <td class="cell c4"></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATSets_wrongentries. ATSets_missingentries.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">These entries should not be elements of your set. <span class="filter_mathjaxloader_equation"><span class="nolink">\[\left \{{\left(x-a\right)}^{6000} \right \}\]</span></span> The following are missing from your set. <span class="filter_mathjaxloader_equation"><span class="nolink">\[\left \{{\left(a-x\right)}^{6000} \right \}\]</span></span></td></td>
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
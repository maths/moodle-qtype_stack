# UnitsRelative: Answer test results

This page exposes the results of running answer tests on STACK test cases.  This page is automatically generated from the STACK unit tests and is designed to show question authors what answer tests actually do.  This includes cases where answer tests currentl fail, which gives a negative expected mark.  Comments and further test cases are very welcome.



<h2>UnitsRelative</h2><div class="no-overflow"><table class="flexible table table-striped table-hover generaltable generalbox stacktestsuite"><thead><tr><th class="header c0" scope="col">Test<div class="commands"></div></th><th class="header c1" scope="col">?<div class="commands"></div></th><th class="header c2" scope="col">Student response<div class="commands"></div></th><th class="header c3" scope="col">Teacher answer<div class="commands"></div></th><th class="header c4" scope="col">Opt<div class="commands"></div></th><th class="header c5" scope="col">Mark<div class="commands"></div></th><th class="header c6" scope="col">Answer note<div class="commands"></div></th>
</tr></thead><tbody>
<tr class="pass">
  <td class="cell c0">UnitsRelative</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>12.3*m/s</pre></td>
  <td class="cell c3"><pre>12.3*m/s</pre></td>
  <td class="cell c4"><pre>0.01</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATUnits_units_match.</td>
</tr>
<tr class="pass">
  <td class="cell c0">UnitsRelative</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>12*m/s</pre></td>
  <td class="cell c3"><pre>12.3*m/s</pre></td>
  <td class="cell c4"><pre>0.01</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATUnits_units_match.</td>
</tr>
<tr class="pass">
  <td class="cell c0">UnitsRelative</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>1.1*Mg/10^6</pre></td>
  <td class="cell c3"><pre>1.2*kN*ns/(mm*Hz)</pre></td>
  <td class="cell c4"><pre>0.15</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATUnits_compatible_units kg.</td>
</tr>
<tr class="pass">
  <td class="cell c0">UnitsRelative</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>1.1*Mg/10^6</pre></td>
  <td class="cell c3"><pre>1.2*kN*ns/(mm*Hz)</pre></td>
  <td class="cell c4"><pre>0.05</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATUnits_compatible_units kg.</td>
</tr>
<tr class="notes">
  <td class="cell c0"><td colspan="6">Edge case</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">UnitsRelative</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>0*m/s</pre></td>
  <td class="cell c3"><pre>0*m/s</pre></td>
  <td class="cell c4"><pre>0.01</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATUnits_units_match.</td>
</tr>
<tr class="pass">
  <td class="cell c0">UnitsRelative</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>0.0*m/s</pre></td>
  <td class="cell c3"><pre>0*m/s</pre></td>
  <td class="cell c4"><pre>0.01</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATUnits_units_match.</td>
</tr>
<tr class="pass">
  <td class="cell c0">UnitsRelative</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>0*m/s</pre></td>
  <td class="cell c3"><pre>0.0*m/s</pre></td>
  <td class="cell c4"><pre>0.01</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATUnits_units_match.</td>
</tr>
<tr class="pass">
  <td class="cell c0">UnitsRelative</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>0.0*m/s</pre></td>
  <td class="cell c3"><pre>0.0*m/s</pre></td>
  <td class="cell c4"><pre>0.01</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATUnits_units_match.</td>
</tr>
<tr class="pass">
  <td class="cell c0">UnitsRelative</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>0.0*km/s</pre></td>
  <td class="cell c3"><pre>0.0*m/s</pre></td>
  <td class="cell c4"><pre>0.01</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATUnits_compatible_units m/s.</td>
</tr>
<tr class="pass">
  <td class="cell c0">UnitsRelative</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>0.0*m</pre></td>
  <td class="cell c3"><pre>0.0*m/s</pre></td>
  <td class="cell c4"><pre>0.01</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATUnits_incompatible_units. ATUnits_correct_numerical.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your units are incompatible with those used by the teacher. Please check your units carefully.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">UnitsRelative</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>0.0</pre></td>
  <td class="cell c3"><pre>0.0*m/s</pre></td>
  <td class="cell c4"><pre>0.01</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATUnits_SA_no_units.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer must have units.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">UnitsRelative</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>0.0*kVA</pre></td>
  <td class="cell c3"><pre>0.0*kVA</pre></td>
  <td class="cell c4"><pre>0.002</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATUnits_units_match.</td>
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
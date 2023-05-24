# UnitsStrict: Answer test results

This page exposes the results of running answer tests on STACK test cases.  This page is automatically generated from the STACK unit tests and is designed to show question authors what answer tests actually do.  This includes cases where answer tests currentl fail, which gives a negative expected mark.  Comments and further test cases are very welcome.



<h2>UnitsStrict</h2><div class="no-overflow"><table class="flexible table table-striped table-hover generaltable generalbox stacktestsuite"><thead><tr><th class="header c0" scope="col">Test<div class="commands"></div></th><th class="header c1" scope="col">?<div class="commands"></div></th><th class="header c2" scope="col">Student response<div class="commands"></div></th><th class="header c3" scope="col">Teacher answer<div class="commands"></div></th><th class="header c4" scope="col">Opt<div class="commands"></div></th><th class="header c5" scope="col">Mark<div class="commands"></div></th><th class="header c6" scope="col">Answer note<div class="commands"></div></th>
</tr></thead><tbody>
<tr class="notes">
  <td class="cell c0"><td colspan="6">Differences from the Units test only</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">UnitsStrict</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>25*g</pre></td>
  <td class="cell c3"><pre>0.025*kg</pre></td>
  <td class="cell c4"><pre>2</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATUnits_compatible_units kg.</td>
</tr>
<tr class="pass">
  <td class="cell c0">UnitsStrict</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>1*Mg/10^6</pre></td>
  <td class="cell c3"><pre>1*N*s^2/(km)</pre></td>
  <td class="cell c4"><pre>1</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATUnits_compatible_units kg.</td>
</tr>
<tr class="pass">
  <td class="cell c0">UnitsStrict</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>1*Mg/10^6</pre></td>
  <td class="cell c3"><pre>1*kN*ns/(mm*Hz)</pre></td>
  <td class="cell c4"><pre>1</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATUnits_compatible_units kg.</td>
</tr>
<tr class="pass">
  <td class="cell c0">UnitsStrict</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>3.14*Mg/10^6</pre></td>
  <td class="cell c3"><pre>%pi*kN*ns/(mm*Hz)</pre></td>
  <td class="cell c4"><pre>3</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATUnits_compatible_units kg.</td>
</tr>
<tr class="pass">
  <td class="cell c0">UnitsStrict</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>400*cc</pre></td>
  <td class="cell c3"><pre>0.4*l</pre></td>
  <td class="cell c4"><pre>2</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumSigFigs_WithinRange. ATUnits_compatible_units m^3.</td>
</tr>
<tr class="pass">
  <td class="cell c0">UnitsStrict</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>400*cm^3</pre></td>
  <td class="cell c3"><pre>0.4*l</pre></td>
  <td class="cell c4"><pre>2</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumSigFigs_WithinRange. ATUnits_compatible_units m^3.</td>
</tr>
<tr class="pass">
  <td class="cell c0">UnitsStrict</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>400*ml</pre></td>
  <td class="cell c3"><pre>0.4*l</pre></td>
  <td class="cell c4"><pre>2</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumSigFigs_WithinRange. ATUnits_compatible_units m^3.</td>
</tr>
<tr class="pass">
  <td class="cell c0">UnitsStrict</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>400*mL</pre></td>
  <td class="cell c3"><pre>0.4*l</pre></td>
  <td class="cell c4"><pre>2</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumSigFigs_WithinRange. ATUnits_compatible_units m^3.</td>
</tr>
<tr class="pass">
  <td class="cell c0">UnitsStrict</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>142.8*C</pre></td>
  <td class="cell c3"><pre>415.9*K</pre></td>
  <td class="cell c4"><pre>4</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumSigFigs_VeryInaccurate. ATUnits_incompatible_units.</td>
</tr>
<tr class="notes">
  <td class="cell c0"><td colspan="6">We are not *that* strict!</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">UnitsStrict</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>-9.81*m/s/s</pre></td>
  <td class="cell c3"><pre>-9.81*m/s^2</pre></td>
  <td class="cell c4"><pre>3</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATUnits_units_match.</td>
</tr>
<tr class="notes">
  <td class="cell c0"><td colspan="6">Edge case</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">UnitsStrict</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>0*m/s</pre></td>
  <td class="cell c3"><pre>0*m/s</pre></td>
  <td class="cell c4"><pre>1</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATUnits_units_match.</td>
</tr>
<tr class="pass">
  <td class="cell c0">UnitsStrict</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>0.0*m/s</pre></td>
  <td class="cell c3"><pre>0*m/s</pre></td>
  <td class="cell c4"><pre>1</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATUnits_units_match.</td>
</tr>
<tr class="pass">
  <td class="cell c0">UnitsStrict</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>0*m/s</pre></td>
  <td class="cell c3"><pre>0.0*m/s</pre></td>
  <td class="cell c4"><pre>1</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATUnits_units_match.</td>
</tr>
<tr class="pass">
  <td class="cell c0">UnitsStrict</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>0.0*m/s</pre></td>
  <td class="cell c3"><pre>0.0*m/s</pre></td>
  <td class="cell c4"><pre>1</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATUnits_units_match.</td>
</tr>
<tr class="pass">
  <td class="cell c0">UnitsStrict</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>0.0*km/s</pre></td>
  <td class="cell c3"><pre>0.0*m/s</pre></td>
  <td class="cell c4"><pre>1</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATUnits_compatible_units m/s.</td>
</tr>
<tr class="pass">
  <td class="cell c0">UnitsStrict</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>0.0*m</pre></td>
  <td class="cell c3"><pre>0.0*m/s</pre></td>
  <td class="cell c4"><pre>1</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATUnits_incompatible_units. ATUnits_correct_numerical.</td>
</tr>
<tr class="pass">
  <td class="cell c0">UnitsStrict</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>0.0</pre></td>
  <td class="cell c3"><pre>0.0*m/s</pre></td>
  <td class="cell c4"><pre>1</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATUnits_SA_no_units.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer must have units.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">UnitsStrict</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>2.33e-15*kg</pre></td>
  <td class="cell c3"><pre>2.33e-15*kg</pre></td>
  <td class="cell c4"><pre>[3,2]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATUnits_units_match.</td>
</tr>
<tr class="pass">
  <td class="cell c0">UnitsStrict</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>7.03e-3*ng</pre></td>
  <td class="cell c3"><pre>7.03e-3*ng</pre></td>
  <td class="cell c4"><pre>[3,2]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATUnits_units_match.</td>
</tr>
<tr class="pass">
  <td class="cell c0">UnitsStrict</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>2.35e-6*ug</pre></td>
  <td class="cell c3"><pre>2.35e-6*ug</pre></td>
  <td class="cell c4"><pre>[3,2]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATUnits_units_match.</td>
</tr>
<tr class="pass">
  <td class="cell c0">UnitsStrict</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>9.83e-10*cg</pre></td>
  <td class="cell c3"><pre>9.83e-10*cg</pre></td>
  <td class="cell c4"><pre>[3,2]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATUnits_units_match.</td>
</tr>
<tr class="pass">
  <td class="cell c0">UnitsStrict</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>9.73e-21*Gg</pre></td>
  <td class="cell c3"><pre>9.73e-21*Gg</pre></td>
  <td class="cell c4"><pre>[3,2]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATUnits_units_match.</td>
</tr>
<tr class="pass">
  <td class="cell c0">UnitsStrict</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>7.19e-15*kg</pre></td>
  <td class="cell c3"><pre>7.19e-15*kg</pre></td>
  <td class="cell c4"><pre>[3,2]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATUnits_units_match.</td>
</tr>
<tr class="pass">
  <td class="cell c0">UnitsStrict</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>8.12e-12*g</pre></td>
  <td class="cell c3"><pre>8.12e-12*g</pre></td>
  <td class="cell c4"><pre>[3,2]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATUnits_units_match.</td>
</tr>
<tr class="pass">
  <td class="cell c0">UnitsStrict</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>9.34e-12*g</pre></td>
  <td class="cell c3"><pre>9.34e-12*g</pre></td>
  <td class="cell c4"><pre>[3,2]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATUnits_units_match.</td>
</tr>
<tr class="pass">
  <td class="cell c0">UnitsStrict</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>1.07e-21*Gg</pre></td>
  <td class="cell c3"><pre>1.07e-21*Gg</pre></td>
  <td class="cell c4"><pre>[3,2]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATUnits_units_match.</td>
</tr>
<tr class="pass">
  <td class="cell c0">UnitsStrict</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>1.91e-10*cg</pre></td>
  <td class="cell c3"><pre>1.91e-10*cg</pre></td>
  <td class="cell c4"><pre>[3,2]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATUnits_units_match.</td>
</tr>
<tr class="pass">
  <td class="cell c0">UnitsStrict</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>5.67e-18*Mg</pre></td>
  <td class="cell c3"><pre>5.67e-18*Mg</pre></td>
  <td class="cell c4"><pre>[3,2]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATUnits_units_match.</td>
</tr>
<tr class="pass">
  <td class="cell c0">UnitsStrict</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>2.04e-9*mg</pre></td>
  <td class="cell c3"><pre>2.04e-9*mg</pre></td>
  <td class="cell c4"><pre>[3,2]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATUnits_units_match.</td>
</tr>
<tr class="pass">
  <td class="cell c0">UnitsStrict</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>6.75e-6*ug</pre></td>
  <td class="cell c3"><pre>6.75e-6*ug</pre></td>
  <td class="cell c4"><pre>[3,2]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATUnits_units_match.</td>
</tr>
<tr class="pass">
  <td class="cell c0">UnitsStrict</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>6.58e-6*ug</pre></td>
  <td class="cell c3"><pre>6.58e-6*ug</pre></td>
  <td class="cell c4"><pre>[3,2]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATUnits_units_match.</td>
</tr>
<tr class="pass">
  <td class="cell c0">UnitsStrict</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>3.58e-9*mg</pre></td>
  <td class="cell c3"><pre>3.58e-9*mg</pre></td>
  <td class="cell c4"><pre>[3,2]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATUnits_units_match.</td>
</tr>
<tr class="pass">
  <td class="cell c0">UnitsStrict</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>9.99e-15*kg</pre></td>
  <td class="cell c3"><pre>9.99e-15*kg</pre></td>
  <td class="cell c4"><pre>[3,2]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATUnits_units_match.</td>
</tr>
<tr class="pass">
  <td class="cell c0">UnitsStrict</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>9.8e-9*mg</pre></td>
  <td class="cell c3"><pre>9.8e-9*mg</pre></td>
  <td class="cell c4"><pre>[3,2]</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumSigFigs_WrongDigits. ATUnits_units_match.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer contains the wrong number of significant digits.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">UnitsStrict</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>9.80e-9*mg</pre></td>
  <td class="cell c3"><pre>9.8e-9*mg</pre></td>
  <td class="cell c4"><pre>[3,2]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATUnits_units_match.</td>
</tr>
<tr class="pass">
  <td class="cell c0">UnitsStrict</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>9.83e-9*mg</pre></td>
  <td class="cell c3"><pre>9.8e-9*mg</pre></td>
  <td class="cell c4"><pre>[3,2]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATUnits_units_match.</td>
</tr>
<tr class="pass">
  <td class="cell c0">UnitsStrict</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>9.78e-9*mg</pre></td>
  <td class="cell c3"><pre>9.8e-9*mg</pre></td>
  <td class="cell c4"><pre>[3,2]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATUnits_units_match.</td>
</tr>
<tr class="pass">
  <td class="cell c0">UnitsStrict</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>36*Kj/mol</pre></td>
  <td class="cell c3"><pre>36*Kj/mol</pre></td>
  <td class="cell c4"><pre>2</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATUnits_units_match.</td>
</tr>
<tr class="pass">
  <td class="cell c0">UnitsStrict</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>-36*Kj/mol</pre></td>
  <td class="cell c3"><pre>-36*Kj/mol</pre></td>
  <td class="cell c4"><pre>2</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATUnits_units_match.</td>
</tr>
<tr class="pass">
  <td class="cell c0">UnitsStrict</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>(-36)*Kj/mol</pre></td>
  <td class="cell c3"><pre>-36*Kj/mol</pre></td>
  <td class="cell c4"><pre>2</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATUnits_units_match.</td>
</tr>
<tr class="pass">
  <td class="cell c0">UnitsStrict</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>(-36*Kj)/mol</pre></td>
  <td class="cell c3"><pre>-36*Kj/mol</pre></td>
  <td class="cell c4"><pre>2</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATUnits_units_match.</td>
</tr>
<tr class="pass">
  <td class="cell c0">UnitsStrict</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>-(36*Kj)/mol</pre></td>
  <td class="cell c3"><pre>-36*Kj/mol</pre></td>
  <td class="cell c4"><pre>2</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATUnits_units_match.</td>
</tr>
<tr class="pass">
  <td class="cell c0">UnitsStrict</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>-(36.2*Kj)/mol</pre></td>
  <td class="cell c3"><pre>-36.3*Kj/mol</pre></td>
  <td class="cell c4"><pre>2</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumSigFigs_WrongDigits. ATUnits_units_match.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer contains the wrong number of significant digits.</td></td>
</tr></tbody></table></div>
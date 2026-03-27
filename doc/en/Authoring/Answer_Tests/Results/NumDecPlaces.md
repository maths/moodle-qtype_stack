# NumDecPlaces: Answer test results

This page exposes the results of running answer tests on STACK test cases.  This page is automatically generated from the STACK unit tests and is designed to show question authors what answer tests actually do.  This includes cases where answer tests currentl fail, which gives a negative expected mark.  Comments and further test cases are very welcome.



<h2>NumDecPlaces</h2><div class="no-overflow"><table class="flexible table table-striped table-hover generaltable generalbox stacktestsuite"><thead><tr><th class="header c0" scope="col">Test<div class="commands"></div></th><th class="header c1" scope="col">?<div class="commands"></div></th><th class="header c2" scope="col">Student response<div class="commands"></div></th><th class="header c3" scope="col">Teacher answer<div class="commands"></div></th><th class="header c4" scope="col">Opt<div class="commands"></div></th><th class="header c5" scope="col">Mark<div class="commands"></div></th><th class="header c6" scope="col">Answer note<div class="commands"></div></th>
</tr></thead><tbody>
<tr class="notes">
  <td class="cell c0"><td colspan="6">Basic tests</td></td>
</tr>
<tr class="expectedfail">
  <td class="cell c0">NumDecPlaces</td>
  <td class="cell c1"><span style="color:orange;"><i class="fa fa-adjust"></i></span></td>
  <td class="cell c2"><pre>1/0</pre></td>
  <td class="cell c3"><pre>3</pre></td>
  <td class="cell c4"><pre>2</pre></td>
  <td class="cell c5">-1</td>
  <td class="cell c6">ATNumDecPlaces_STACKERROR_SAns.</td>
</tr>
<tr class="expectedfail">
  <td class="cell c0">NumDecPlaces</td>
  <td class="cell c1"><span style="color:orange;"><i class="fa fa-adjust"></i></span></td>
  <td class="cell c2"><pre>0.1</pre></td>
  <td class="cell c3"><pre>1/0</pre></td>
  <td class="cell c4"><pre>2</pre></td>
  <td class="cell c5">-1</td>
  <td class="cell c6">ATNumDecPlaces_STACKERROR_TAns.</td>
</tr>
<tr class="expectedfail">
  <td class="cell c0">NumDecPlaces</td>
  <td class="cell c1"><span style="color:orange;"><i class="fa fa-adjust"></i></span></td>
  <td class="cell c2"><pre>0.1</pre></td>
  <td class="cell c3"><pre>0</pre></td>
  <td class="cell c4"><pre>1/0</pre></td>
  <td class="cell c5">-1</td>
  <td class="cell c6">ATNumDecPlaces_STACKERROR_Opt.</td>
</tr>
<tr class="expectedfail">
  <td class="cell c0">NumDecPlaces</td>
  <td class="cell c1"><span style="color:orange;"><i class="fa fa-adjust"></i></span></td>
  <td class="cell c2"><pre>0.1</pre></td>
  <td class="cell c3"><pre>1</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">-1</td>
  <td class="cell c6">ATNumDecPlaces_OptNotInt.</td>
</tr>
<tr class="expectedfail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">For ATNumDecPlaces the test option must be a positive integer, in fact "<span class="filter_mathjaxloader_equation"><span class="nolink">\(x\)</span></span>" was received.</td></td>
</tr>
<tr class="expectedfail">
  <td class="cell c0">NumDecPlaces</td>
  <td class="cell c1"><span style="color:orange;"><i class="fa fa-adjust"></i></span></td>
  <td class="cell c2"><pre>0.1</pre></td>
  <td class="cell c3"><pre>1</pre></td>
  <td class="cell c4"><pre>-1</pre></td>
  <td class="cell c5">-1</td>
  <td class="cell c6">ATNumDecPlaces_OptNotInt.</td>
</tr>
<tr class="expectedfail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">For ATNumDecPlaces the test option must be a positive integer, in fact "<span class="filter_mathjaxloader_equation"><span class="nolink">\(-1\)</span></span>" was received.</td></td>
</tr>
<tr class="expectedfail">
  <td class="cell c0">NumDecPlaces</td>
  <td class="cell c1"><span style="color:orange;"><i class="fa fa-adjust"></i></span></td>
  <td class="cell c2"><pre>0.1</pre></td>
  <td class="cell c3"><pre>1</pre></td>
  <td class="cell c4"><pre>0</pre></td>
  <td class="cell c5">-1</td>
  <td class="cell c6">ATNumDecPlaces_OptNotInt.</td>
</tr>
<tr class="expectedfail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">For ATNumDecPlaces the test option must be a positive integer, in fact "<span class="filter_mathjaxloader_equation"><span class="nolink">\(0\)</span></span>" was received.</td></td>
</tr>
<tr class="expectedfail">
  <td class="cell c0">NumDecPlaces</td>
  <td class="cell c1"><span style="color:orange;"><i class="fa fa-adjust"></i></span></td>
  <td class="cell c2"><pre>0.1</pre></td>
  <td class="cell c3"><pre>1</pre></td>
  <td class="cell c4"><pre>(</pre></td>
  <td class="cell c5">-1</td>
  <td class="cell c6">STACKERROR_OPTION.</td>
</tr>
<tr class="expectedfail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">TEST_FAILED</td></td>
</tr>
<tr class="expectedfail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The answer test failed to execute correctly: please alert your teacher. Option field is invalid. You have a missing right bracket <span class="stacksyntaxexample">)</span> in the expression: <span class="stacksyntaxexample">(</span>.</td></td>
</tr>
<tr class="expectedfail">
  <td class="cell c0">NumDecPlaces</td>
  <td class="cell c1"><span style="color:orange;"><i class="fa fa-adjust"></i></span></td>
  <td class="cell c2"><pre>(</pre></td>
  <td class="cell c3"><pre>1</pre></td>
  <td class="cell c4"><pre>1</pre></td>
  <td class="cell c5">-1</td>
  <td class="cell c6">ATNumDecPlacesTEST_FAILED-Empty SA.</td>
</tr>
<tr class="expectedfail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The answer test failed to execute correctly: please alert your teacher. Attempted to execute an answer test with an empty student answer, probably a CAS validation problem when authoring the question.</td></td>
</tr>
<tr class="notes">
  <td class="cell c0"><td colspan="6">Student's answer not a floating point number</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumDecPlaces</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>x</pre></td>
  <td class="cell c3"><pre>3.143</pre></td>
  <td class="cell c4"><pre>2</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumDecPlaces_SA_Not_num.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer must be a floating point number, but is not.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumDecPlaces</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>pi</pre></td>
  <td class="cell c3"><pre>3.000</pre></td>
  <td class="cell c4"><pre>3</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumDecPlaces_SA_Not_num.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer must be a floating point number, but is not.</td></td>
</tr>
<tr class="notes">
  <td class="cell c0"><td colspan="6">Right number of places</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumDecPlaces</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>3.14</pre></td>
  <td class="cell c3"><pre>3.143</pre></td>
  <td class="cell c4"><pre>2</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATNumDecPlaces_Correct. ATNumDecPlaces_Equiv.</td>
</tr>
<tr class="pass">
  <td class="cell c0">NumDecPlaces</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>3.14</pre></td>
  <td class="cell c3"><pre>3.14</pre></td>
  <td class="cell c4"><pre>2</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATNumDecPlaces_Correct. ATNumDecPlaces_Equiv.</td>
</tr>
<tr class="pass">
  <td class="cell c0">NumDecPlaces</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>3.140</pre></td>
  <td class="cell c3"><pre>3.140</pre></td>
  <td class="cell c4"><pre>3</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATNumDecPlaces_Correct. ATNumDecPlaces_Equiv.</td>
</tr>
<tr class="pass">
  <td class="cell c0">NumDecPlaces</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>3141.5972</pre></td>
  <td class="cell c3"><pre>3141.5972</pre></td>
  <td class="cell c4"><pre>4</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATNumDecPlaces_Correct. ATNumDecPlaces_Equiv.</td>
</tr>
<tr class="pass">
  <td class="cell c0">NumDecPlaces</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>4.14</pre></td>
  <td class="cell c3"><pre>3.14</pre></td>
  <td class="cell c4"><pre>2</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumDecPlaces_Correct. ATNumDecPlaces_Not_equiv.</td>
</tr>
<tr class="pass">
  <td class="cell c0">NumDecPlaces</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>3.1416</pre></td>
  <td class="cell c3"><pre>pi</pre></td>
  <td class="cell c4"><pre>4</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATNumDecPlaces_Correct. ATNumDecPlaces_Equiv.</td>
</tr>
<tr class="pass">
  <td class="cell c0">NumDecPlaces</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>-7.3</pre></td>
  <td class="cell c3"><pre>-7.3</pre></td>
  <td class="cell c4"><pre>1</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATNumDecPlaces_Correct. ATNumDecPlaces_Equiv.</td>
</tr>
<tr class="notes">
  <td class="cell c0"><td colspan="6">Wrong number of places</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumDecPlaces</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>3.14</pre></td>
  <td class="cell c3"><pre>3.143</pre></td>
  <td class="cell c4"><pre>1</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumDecPlaces_Wrong_DPs. ATNumDecPlaces_Equiv.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer has been given to the wrong number of decimal places.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumDecPlaces</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>3.14</pre></td>
  <td class="cell c3"><pre>3.143</pre></td>
  <td class="cell c4"><pre>1</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumDecPlaces_Wrong_DPs. ATNumDecPlaces_Equiv.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer has been given to the wrong number of decimal places.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumDecPlaces</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>3.14</pre></td>
  <td class="cell c3"><pre>3.140</pre></td>
  <td class="cell c4"><pre>3</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumDecPlaces_Wrong_DPs. ATNumDecPlaces_Equiv.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer has been given to the wrong number of decimal places.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumDecPlaces</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>7.000</pre></td>
  <td class="cell c3"><pre>7</pre></td>
  <td class="cell c4"><pre>4</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumDecPlaces_Wrong_DPs. ATNumDecPlaces_Equiv.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer has been given to the wrong number of decimal places.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumDecPlaces</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>7.0000</pre></td>
  <td class="cell c3"><pre>7</pre></td>
  <td class="cell c4"><pre>4</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATNumDecPlaces_Correct. ATNumDecPlaces_Equiv.</td>
</tr>
<tr class="notes">
  <td class="cell c0"><td colspan="6">Both wrong DPs and inaccurate.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumDecPlaces</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>8.0000</pre></td>
  <td class="cell c3"><pre>7</pre></td>
  <td class="cell c4"><pre>3</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumDecPlaces_Wrong_DPs. ATNumDecPlaces_Not_equiv.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer has been given to the wrong number of decimal places.</td></td>
</tr>
<tr class="notes">
  <td class="cell c0"><td colspan="6">Teacher needs to round their answer.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumDecPlaces</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>4.000</pre></td>
  <td class="cell c3"><pre>3.99999</pre></td>
  <td class="cell c4"><pre>3</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATNumDecPlaces_Correct. ATNumDecPlaces_Equiv.</td>
</tr>
<tr class="notes">
  <td class="cell c0"><td colspan="6">Teacher uses displaydp</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumDecPlaces</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>0.10</pre></td>
  <td class="cell c3"><pre>displaydp(0.1,2)</pre></td>
  <td class="cell c4"><pre>2</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATNumDecPlaces_Correct. ATNumDecPlaces_Equiv.</td>
</tr></tbody></table></div>
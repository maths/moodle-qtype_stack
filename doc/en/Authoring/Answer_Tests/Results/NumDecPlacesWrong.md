# NumDecPlacesWrong: Answer test results

This page exposes the results of running answer tests on STACK test cases.  This page is automatically generated from the STACK unit tests and is designed to show question authors what answer tests actually do.  This includes cases where answer tests currentl fail, which gives a negative expected mark.  Comments and further test cases are very welcome.



<h2>NumDecPlacesWrong</h2><div class="no-overflow"><table class="flexible table table-striped table-hover generaltable generalbox stacktestsuite"><thead><tr><th class="header c0" scope="col">Test<div class="commands"></div></th><th class="header c1" scope="col">?<div class="commands"></div></th><th class="header c2" scope="col">Student response<div class="commands"></div></th><th class="header c3" scope="col">Teacher answer<div class="commands"></div></th><th class="header c4" scope="col">Opt<div class="commands"></div></th><th class="header c5" scope="col">Mark<div class="commands"></div></th><th class="header c6" scope="col">Answer note<div class="commands"></div></th>
</tr></thead><tbody>
<tr class="notes">
  <td class="cell c0"><td colspan="6">Basic tests</td></td>
</tr>
<tr class="expectedfail">
  <td class="cell c0">NumDecPlacesWrong</td>
  <td class="cell c1"><span style="color:orange;"><i class="fa fa-adjust"></i></span></td>
  <td class="cell c2"><pre>1/0</pre></td>
  <td class="cell c3"><pre>3</pre></td>
  <td class="cell c4"><pre>2</pre></td>
  <td class="cell c5">-1</td>
  <td class="cell c6">ATNumDecPlacesWrong_STACKERROR_SAns.</td>
</tr>
<tr class="expectedfail">
  <td class="cell c0">NumDecPlacesWrong</td>
  <td class="cell c1"><span style="color:orange;"><i class="fa fa-adjust"></i></span></td>
  <td class="cell c2"><pre>0.1</pre></td>
  <td class="cell c3"><pre>1/0</pre></td>
  <td class="cell c4"><pre>2</pre></td>
  <td class="cell c5">-1</td>
  <td class="cell c6">ATNumDecPlacesWrong_STACKERROR_TAns.</td>
</tr>
<tr class="expectedfail">
  <td class="cell c0">NumDecPlacesWrong</td>
  <td class="cell c1"><span style="color:orange;"><i class="fa fa-adjust"></i></span></td>
  <td class="cell c2"><pre>0.1</pre></td>
  <td class="cell c3"><pre>0</pre></td>
  <td class="cell c4"><pre>1/0</pre></td>
  <td class="cell c5">-1</td>
  <td class="cell c6">ATNumDecPlacesWrong_STACKERROR_Opt.</td>
</tr>
<tr class="expectedfail">
  <td class="cell c0">NumDecPlacesWrong</td>
  <td class="cell c1"><span style="color:orange;"><i class="fa fa-adjust"></i></span></td>
  <td class="cell c2"><pre>0.1</pre></td>
  <td class="cell c3"><pre>0</pre></td>
  <td class="cell c4"><pre>x</pre></td>
  <td class="cell c5">-1</td>
  <td class="cell c6">ATNumDecPlacesWrong_OptNotInt.</td>
</tr>
<tr class="expectedfail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">For ATNumDecPlacesWrong the test option must be a positive integer, in fact "<span class="filter_mathjaxloader_equation"><span class="nolink">\(x\)</span></span>" was received.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumDecPlacesWrong</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>x^2</pre></td>
  <td class="cell c3"><pre>1234</pre></td>
  <td class="cell c4"><pre>4</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumDecPlacesWrong_SA_Not_num.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer must be a floating point number, but is not.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumDecPlacesWrong</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>1234.5</pre></td>
  <td class="cell c3"><pre>x^2</pre></td>
  <td class="cell c4"><pre>4</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumDecPlacesWrong_Tans_Not_Num.</td>
</tr>
<tr class="pass">
  <td class="cell c0">NumDecPlacesWrong</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>3.141</pre></td>
  <td class="cell c3"><pre>31.41</pre></td>
  <td class="cell c4"><pre>4</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATNumDecPlacesWrong_Correct.</td>
</tr>
<tr class="pass">
  <td class="cell c0">NumDecPlacesWrong</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>3.141</pre></td>
  <td class="cell c3"><pre>31.14</pre></td>
  <td class="cell c4"><pre>4</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumDecPlacesWrong_Wrong.</td>
</tr>
<tr class="pass">
  <td class="cell c0">NumDecPlacesWrong</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>pi</pre></td>
  <td class="cell c3"><pre>31.14</pre></td>
  <td class="cell c4"><pre>4</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumDecPlacesWrong_SA_Not_num.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer must be a floating point number, but is not.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumDecPlacesWrong</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>0.1234</pre></td>
  <td class="cell c3"><pre>1234</pre></td>
  <td class="cell c4"><pre>4</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATNumDecPlacesWrong_Correct.</td>
</tr>
<tr class="pass">
  <td class="cell c0">NumDecPlacesWrong</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>0.1235</pre></td>
  <td class="cell c3"><pre>1234</pre></td>
  <td class="cell c4"><pre>4</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumDecPlacesWrong_Wrong.</td>
</tr>
<tr class="pass">
  <td class="cell c0">NumDecPlacesWrong</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>0.0001234</pre></td>
  <td class="cell c3"><pre>1234</pre></td>
  <td class="cell c4"><pre>4</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATNumDecPlacesWrong_Correct.</td>
</tr>
<tr class="pass">
  <td class="cell c0">NumDecPlacesWrong</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>0.0001235</pre></td>
  <td class="cell c3"><pre>1234</pre></td>
  <td class="cell c4"><pre>4</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumDecPlacesWrong_Wrong.</td>
</tr>
<tr class="pass">
  <td class="cell c0">NumDecPlacesWrong</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>0.1233</pre></td>
  <td class="cell c3"><pre>1234</pre></td>
  <td class="cell c4"><pre>3</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATNumDecPlacesWrong_Correct.</td>
</tr>
<tr class="pass">
  <td class="cell c0">NumDecPlacesWrong</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>0.1243</pre></td>
  <td class="cell c3"><pre>1234</pre></td>
  <td class="cell c4"><pre>3</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumDecPlacesWrong_Wrong.</td>
</tr>
<tr class="pass">
  <td class="cell c0">NumDecPlacesWrong</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>0.1230</pre></td>
  <td class="cell c3"><pre>1239</pre></td>
  <td class="cell c4"><pre>3</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATNumDecPlacesWrong_Correct.</td>
</tr>
<tr class="pass">
  <td class="cell c0">NumDecPlacesWrong</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>0.1240</pre></td>
  <td class="cell c3"><pre>1239</pre></td>
  <td class="cell c4"><pre>3</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumDecPlacesWrong_Wrong.</td>
</tr>
<tr class="pass">
  <td class="cell c0">NumDecPlacesWrong</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>1230</pre></td>
  <td class="cell c3"><pre>1239</pre></td>
  <td class="cell c4"><pre>3</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATNumDecPlacesWrong_Correct.</td>
</tr>
<tr class="pass">
  <td class="cell c0">NumDecPlacesWrong</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>2230</pre></td>
  <td class="cell c3"><pre>1239</pre></td>
  <td class="cell c4"><pre>3</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumDecPlacesWrong_Wrong.</td>
</tr>
<tr class="pass">
  <td class="cell c0">NumDecPlacesWrong</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>0.100</pre></td>
  <td class="cell c3"><pre>1.00</pre></td>
  <td class="cell c4"><pre>3</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATNumDecPlacesWrong_Correct.</td>
</tr>
<tr class="pass">
  <td class="cell c0">NumDecPlacesWrong</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>0.1000</pre></td>
  <td class="cell c3"><pre>1.00</pre></td>
  <td class="cell c4"><pre>3</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATNumDecPlacesWrong_Correct.</td>
</tr>
<tr class="pass">
  <td class="cell c0">NumDecPlacesWrong</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>0.1001</pre></td>
  <td class="cell c3"><pre>1.001</pre></td>
  <td class="cell c4"><pre>3</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATNumDecPlacesWrong_Correct.</td>
</tr>
<tr class="notes">
  <td class="cell c0"><td colspan="6">Condone lack of trailing zeros</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumDecPlacesWrong</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>0.100</pre></td>
  <td class="cell c3"><pre>1.0</pre></td>
  <td class="cell c4"><pre>4</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATNumDecPlacesWrong_Correct.</td>
</tr>
<tr class="pass">
  <td class="cell c0">NumDecPlacesWrong</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>1</pre></td>
  <td class="cell c3"><pre>1.00</pre></td>
  <td class="cell c4"><pre>4</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATNumDecPlacesWrong_Correct.</td>
</tr>
<tr class="notes">
  <td class="cell c0"><td colspan="6">Teacher uses displaydp</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumDecPlacesWrong</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>0.101</pre></td>
  <td class="cell c3"><pre>displaydp(101,3)</pre></td>
  <td class="cell c4"><pre>3</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATNumDecPlacesWrong_Correct.</td>
</tr></tbody></table></div>
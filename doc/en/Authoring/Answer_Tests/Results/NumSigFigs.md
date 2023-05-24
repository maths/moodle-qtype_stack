# NumSigFigs: Answer test results

This page exposes the results of running answer tests on STACK test cases.  This page is automatically generated from the STACK unit tests and is designed to show question authors what answer tests actually do.  This includes cases where answer tests currentl fail, which gives a negative expected mark.  Comments and further test cases are very welcome.



<h2>NumSigFigs</h2><div class="no-overflow"><table class="flexible table table-striped table-hover generaltable generalbox stacktestsuite"><thead><tr><th class="header c0" scope="col">Test<div class="commands"></div></th><th class="header c1" scope="col">?<div class="commands"></div></th><th class="header c2" scope="col">Student response<div class="commands"></div></th><th class="header c3" scope="col">Teacher answer<div class="commands"></div></th><th class="header c4" scope="col">Opt<div class="commands"></div></th><th class="header c5" scope="col">Mark<div class="commands"></div></th><th class="header c6" scope="col">Answer note<div class="commands"></div></th>
</tr></thead><tbody>
<tr class="notes">
  <td class="cell c0"><td colspan="6">Basic tests</td></td>
</tr>
<tr class="expectedfail">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:orange;"><i class="fa fa-adjust"></i></span></td>
  <td class="cell c2"><pre>3.141</pre></td>
  <td class="cell c3"><pre>3.1415927</pre></td>
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
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:orange;"><i class="fa fa-adjust"></i></span></td>
  <td class="cell c2"><pre>1/0</pre></td>
  <td class="cell c3"><pre>3</pre></td>
  <td class="cell c4"><pre>3</pre></td>
  <td class="cell c5">-1</td>
  <td class="cell c6">ATNumSigFigs_STACKERROR_SAns.</td>
</tr>
<tr class="expectedfail">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:orange;"><i class="fa fa-adjust"></i></span></td>
  <td class="cell c2"><pre>0</pre></td>
  <td class="cell c3"><pre>1/0</pre></td>
  <td class="cell c4"><pre>3</pre></td>
  <td class="cell c5">-1</td>
  <td class="cell c6">ATNumSigFigs_STACKERROR_TAns.</td>
</tr>
<tr class="expectedfail">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:orange;"><i class="fa fa-adjust"></i></span></td>
  <td class="cell c2"><pre>0</pre></td>
  <td class="cell c3"><pre>0</pre></td>
  <td class="cell c4"><pre>1/0</pre></td>
  <td class="cell c5">-1</td>
  <td class="cell c6">ATNumSigFigs_STACKERROR_Opt.</td>
</tr>
<tr class="expectedfail">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:orange;"><i class="fa fa-adjust"></i></span></td>
  <td class="cell c2"><pre>0</pre></td>
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
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:orange;"><i class="fa fa-adjust"></i></span></td>
  <td class="cell c2"><pre>(</pre></td>
  <td class="cell c3"><pre>1</pre></td>
  <td class="cell c4"><pre>1</pre></td>
  <td class="cell c5">-1</td>
  <td class="cell c6">ATNumSigFigsTEST_FAILED-Empty SA.</td>
</tr>
<tr class="expectedfail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The answer test failed to execute correctly: please alert your teacher. Attempted to execute an answer test with an empty student answer, probably a CAS validation problem when authoring the question.</td></td>
</tr>
<tr class="expectedfail">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:orange;"><i class="fa fa-adjust"></i></span></td>
  <td class="cell c2"><pre>1</pre></td>
  <td class="cell c3"><pre>3</pre></td>
  <td class="cell c4"><pre>pi</pre></td>
  <td class="cell c5">-1</td>
  <td class="cell c6">ATNumSigFigs_STACKERROR_not_integer.</td>
</tr>
<tr class="expectedfail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The answer test failed to execute correctly: please alert your teacher.</td></td>
</tr>
<tr class="expectedfail">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:orange;"><i class="fa fa-adjust"></i></span></td>
  <td class="cell c2"><pre>1</pre></td>
  <td class="cell c3"><pre>3</pre></td>
  <td class="cell c4"><pre>[3,x]</pre></td>
  <td class="cell c5">-1</td>
  <td class="cell c6">ATNumSigFigs_STACKERROR_not_integer.</td>
</tr>
<tr class="expectedfail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The answer test failed to execute correctly: please alert your teacher.</td></td>
</tr>
<tr class="expectedfail">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:orange;"><i class="fa fa-adjust"></i></span></td>
  <td class="cell c2"><pre>1</pre></td>
  <td class="cell c3"><pre>3</pre></td>
  <td class="cell c4"><pre>[1,2,3]</pre></td>
  <td class="cell c5">-1</td>
  <td class="cell c6">ATNumSigFigs_STACKERROR_list_wrong_length.</td>
</tr>
<tr class="expectedfail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The answer test failed to execute correctly: please alert your teacher.</td></td>
</tr>
<tr class="expectedfail">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:orange;"><i class="fa fa-adjust"></i></span></td>
  <td class="cell c2"><pre>1</pre></td>
  <td class="cell c3"><pre>3</pre></td>
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
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>pi</pre></td>
  <td class="cell c3"><pre>pi</pre></td>
  <td class="cell c4"><pre>4</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumSigFigs_NotDecimal.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer should be a decimal number, but is not!</td></td>
</tr>
<tr class="notes">
  <td class="cell c0"><td colspan="6">Edge cases</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>0</pre></td>
  <td class="cell c3"><pre>0</pre></td>
  <td class="cell c4"><pre>2</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumSigFigs_WrongDigits.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer contains the wrong number of significant digits.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>0</pre></td>
  <td class="cell c3"><pre>0</pre></td>
  <td class="cell c4"><pre>1</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>0.0</pre></td>
  <td class="cell c3"><pre>0</pre></td>
  <td class="cell c4"><pre>1</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>0.0</pre></td>
  <td class="cell c3"><pre>0</pre></td>
  <td class="cell c4"><pre>2</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumSigFigs_WrongDigits.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer contains the wrong number of significant digits.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>0</pre></td>
  <td class="cell c3"><pre>0.0</pre></td>
  <td class="cell c4"><pre>2</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumSigFigs_WrongDigits.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer contains the wrong number of significant digits.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>0.0</pre></td>
  <td class="cell c3"><pre>0.0</pre></td>
  <td class="cell c4"><pre>2</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumSigFigs_WrongDigits.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer contains the wrong number of significant digits.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>0.00</pre></td>
  <td class="cell c3"><pre>0.00</pre></td>
  <td class="cell c4"><pre>2</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="notes">
  <td class="cell c0"><td colspan="6">Large numbers</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>5.4e21</pre></td>
  <td class="cell c3"><pre>5.3e21</pre></td>
  <td class="cell c4"><pre>2</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumSigFigs_Inaccurate.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The accuracy of your answer is not correct. Either you have not rounded correctly, or you have rounded an intermediate answer which propagates an error.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>5.3e21</pre></td>
  <td class="cell c3"><pre>5.3e21</pre></td>
  <td class="cell c4"><pre>2</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>5.3e22</pre></td>
  <td class="cell c3"><pre>5.3e22</pre></td>
  <td class="cell c4"><pre>2</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>5.3e20</pre></td>
  <td class="cell c3"><pre>5.3e22</pre></td>
  <td class="cell c4"><pre>2</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumSigFigs_VeryInaccurate.</td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>6.02214086e23</pre></td>
  <td class="cell c3"><pre>6.02214086e23</pre></td>
  <td class="cell c4"><pre>9</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>6.0221409e23</pre></td>
  <td class="cell c3"><pre>6.02214086e23</pre></td>
  <td class="cell c4"><pre>9</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumSigFigs_WrongDigits. ATNumSigFigs_Inaccurate.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer contains the wrong number of significant digits. The accuracy of your answer is not correct. Either you have not rounded correctly, or you have rounded an intermediate answer which propagates an error.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>6.02214087e23</pre></td>
  <td class="cell c3"><pre>6.02214086e23</pre></td>
  <td class="cell c4"><pre>9</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumSigFigs_Inaccurate.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The accuracy of your answer is not correct. Either you have not rounded correctly, or you have rounded an intermediate answer which propagates an error.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>6.02214085e23</pre></td>
  <td class="cell c3"><pre>6.02214086e23</pre></td>
  <td class="cell c4"><pre>9</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumSigFigs_Inaccurate.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The accuracy of your answer is not correct. Either you have not rounded correctly, or you have rounded an intermediate answer which propagates an error.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>5.3910632e-44</pre></td>
  <td class="cell c3"><pre>5.3910632e-44</pre></td>
  <td class="cell c4"><pre>8</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>5.391063e-44</pre></td>
  <td class="cell c3"><pre>5.3910632e-44</pre></td>
  <td class="cell c4"><pre>8</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumSigFigs_WrongDigits. ATNumSigFigs_Inaccurate.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer contains the wrong number of significant digits. The accuracy of your answer is not correct. Either you have not rounded correctly, or you have rounded an intermediate answer which propagates an error.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>5.3910631e-44</pre></td>
  <td class="cell c3"><pre>5.3910632e-44</pre></td>
  <td class="cell c4"><pre>8</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumSigFigs_Inaccurate.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The accuracy of your answer is not correct. Either you have not rounded correctly, or you have rounded an intermediate answer which propagates an error.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>5.3910633e-44</pre></td>
  <td class="cell c3"><pre>5.3910632e-44</pre></td>
  <td class="cell c4"><pre>8</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumSigFigs_Inaccurate.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The accuracy of your answer is not correct. Either you have not rounded correctly, or you have rounded an intermediate answer which propagates an error.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>1.61622938e-35</pre></td>
  <td class="cell c3"><pre>1.61622938e-35</pre></td>
  <td class="cell c4"><pre>9</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>1.6162294e-35</pre></td>
  <td class="cell c3"><pre>1.61622938e-35</pre></td>
  <td class="cell c4"><pre>9</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumSigFigs_WrongDigits. ATNumSigFigs_Inaccurate.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer contains the wrong number of significant digits. The accuracy of your answer is not correct. Either you have not rounded correctly, or you have rounded an intermediate answer which propagates an error.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>1.61622939e-35</pre></td>
  <td class="cell c3"><pre>1.61622938e-35</pre></td>
  <td class="cell c4"><pre>9</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumSigFigs_Inaccurate.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The accuracy of your answer is not correct. Either you have not rounded correctly, or you have rounded an intermediate answer which propagates an error.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>1.61622937e-35</pre></td>
  <td class="cell c3"><pre>1.61622938e-35</pre></td>
  <td class="cell c4"><pre>9</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumSigFigs_Inaccurate.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The accuracy of your answer is not correct. Either you have not rounded correctly, or you have rounded an intermediate answer which propagates an error.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>1.2345e82</pre></td>
  <td class="cell c3"><pre>1.2345e82</pre></td>
  <td class="cell c4"><pre>5</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>1.2346e82</pre></td>
  <td class="cell c3"><pre>1.2345e82</pre></td>
  <td class="cell c4"><pre>5</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumSigFigs_Inaccurate.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The accuracy of your answer is not correct. Either you have not rounded correctly, or you have rounded an intermediate answer which propagates an error.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>1.2344e82</pre></td>
  <td class="cell c3"><pre>1.2345e82</pre></td>
  <td class="cell c4"><pre>5</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumSigFigs_Inaccurate.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The accuracy of your answer is not correct. Either you have not rounded correctly, or you have rounded an intermediate answer which propagates an error.</td></td>
</tr>
<tr class="notes">
  <td class="cell c0"><td colspan="6">No trailing zeros.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>1.234</pre></td>
  <td class="cell c3"><pre>4</pre></td>
  <td class="cell c4"><pre>1</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumSigFigs_WrongDigits. ATNumSigFigs_Inaccurate.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer contains the wrong number of significant digits. The accuracy of your answer is not correct. Either you have not rounded correctly, or you have rounded an intermediate answer which propagates an error.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>3.141</pre></td>
  <td class="cell c3"><pre>3.1415927</pre></td>
  <td class="cell c4"><pre>3</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumSigFigs_WrongDigits.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer contains the wrong number of significant digits.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>3.141</pre></td>
  <td class="cell c3"><pre>3.1415927</pre></td>
  <td class="cell c4"><pre>4</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumSigFigs_Inaccurate.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The accuracy of your answer is not correct. Either you have not rounded correctly, or you have rounded an intermediate answer which propagates an error.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>3.146</pre></td>
  <td class="cell c3"><pre>3.1415927</pre></td>
  <td class="cell c4"><pre>4</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumSigFigs_Inaccurate.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The accuracy of your answer is not correct. Either you have not rounded correctly, or you have rounded an intermediate answer which propagates an error.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>3.147</pre></td>
  <td class="cell c3"><pre>3.1415927</pre></td>
  <td class="cell c4"><pre>4</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumSigFigs_VeryInaccurate.</td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>3.142</pre></td>
  <td class="cell c3"><pre>3.1415927</pre></td>
  <td class="cell c4"><pre>4</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>3.142</pre></td>
  <td class="cell c3"><pre>pi</pre></td>
  <td class="cell c4"><pre>4</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>3141</pre></td>
  <td class="cell c3"><pre>3.1415927</pre></td>
  <td class="cell c4"><pre>4</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumSigFigs_VeryInaccurate.</td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>0.00123</pre></td>
  <td class="cell c3"><pre>0.001234567</pre></td>
  <td class="cell c4"><pre>3</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>1.23e-3</pre></td>
  <td class="cell c3"><pre>0.001234567</pre></td>
  <td class="cell c4"><pre>3</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>138*10^-3</pre></td>
  <td class="cell c3"><pre>138*10^-3</pre></td>
  <td class="cell c4"><pre>3</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>-138*10^-3</pre></td>
  <td class="cell c3"><pre>-138*10^-3</pre></td>
  <td class="cell c4"><pre>3</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>138*10^-3</pre></td>
  <td class="cell c3"><pre>-138*10^-3</pre></td>
  <td class="cell c4"><pre>3</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumSigFigs_WrongSign.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer has the wrong algebraic sign.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>1.38*10^-1</pre></td>
  <td class="cell c3"><pre>138*10^-3</pre></td>
  <td class="cell c4"><pre>3</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>1.24e-3</pre></td>
  <td class="cell c3"><pre>0.001234567</pre></td>
  <td class="cell c4"><pre>3</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumSigFigs_Inaccurate.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The accuracy of your answer is not correct. Either you have not rounded correctly, or you have rounded an intermediate answer which propagates an error.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>1.235e-3</pre></td>
  <td class="cell c3"><pre>0.001234567</pre></td>
  <td class="cell c4"><pre>4</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>1000</pre></td>
  <td class="cell c3"><pre>999</pre></td>
  <td class="cell c4"><pre>2</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATNumSigFigs_WithinRange.</td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>1E3</pre></td>
  <td class="cell c3"><pre>999</pre></td>
  <td class="cell c4"><pre>2</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumSigFigs_WrongDigits.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer contains the wrong number of significant digits.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>-100</pre></td>
  <td class="cell c3"><pre>-149</pre></td>
  <td class="cell c4"><pre>1</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>-0.05</pre></td>
  <td class="cell c3"><pre>-0.0499</pre></td>
  <td class="cell c4"><pre>1</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>-(0.05)</pre></td>
  <td class="cell c3"><pre>-0.0499</pre></td>
  <td class="cell c4"><pre>1</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>1170</pre></td>
  <td class="cell c3"><pre>1174.34</pre></td>
  <td class="cell c4"><pre>3</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>61300</pre></td>
  <td class="cell c3"><pre>61250</pre></td>
  <td class="cell c4"><pre>3</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="notes">
  <td class="cell c0"><td colspan="6">Previous tricky case</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>0.1667</pre></td>
  <td class="cell c3"><pre>0.1667</pre></td>
  <td class="cell c4"><pre>4</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>0.1666</pre></td>
  <td class="cell c3"><pre>0.1667</pre></td>
  <td class="cell c4"><pre>4</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumSigFigs_Inaccurate.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The accuracy of your answer is not correct. Either you have not rounded correctly, or you have rounded an intermediate answer which propagates an error.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>0.1663</pre></td>
  <td class="cell c3"><pre>0.1667</pre></td>
  <td class="cell c4"><pre>4</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumSigFigs_Inaccurate.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The accuracy of your answer is not correct. Either you have not rounded correctly, or you have rounded an intermediate answer which propagates an error.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>0.1662</pre></td>
  <td class="cell c3"><pre>0.1667</pre></td>
  <td class="cell c4"><pre>4</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumSigFigs_VeryInaccurate.</td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>0.166</pre></td>
  <td class="cell c3"><pre>0.1667</pre></td>
  <td class="cell c4"><pre>4</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumSigFigs_WrongDigits. ATNumSigFigs_VeryInaccurate.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer contains the wrong number of significant digits.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>0.16667</pre></td>
  <td class="cell c3"><pre>0.1667</pre></td>
  <td class="cell c4"><pre>4</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumSigFigs_WrongDigits.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer contains the wrong number of significant digits.</td></td>
</tr>
<tr class="notes">
  <td class="cell c0"><td colspan="6">Negative numbers</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>-3.141</pre></td>
  <td class="cell c3"><pre>-3.1415927</pre></td>
  <td class="cell c4"><pre>4</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumSigFigs_Inaccurate.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The accuracy of your answer is not correct. Either you have not rounded correctly, or you have rounded an intermediate answer which propagates an error.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>-3.141</pre></td>
  <td class="cell c3"><pre>-3.1415927</pre></td>
  <td class="cell c4"><pre>3</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumSigFigs_WrongDigits.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer contains the wrong number of significant digits.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>-3.141</pre></td>
  <td class="cell c3"><pre>-3.1415927</pre></td>
  <td class="cell c4"><pre>4</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumSigFigs_Inaccurate.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The accuracy of your answer is not correct. Either you have not rounded correctly, or you have rounded an intermediate answer which propagates an error.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>-3.142</pre></td>
  <td class="cell c3"><pre>-3.1415927</pre></td>
  <td class="cell c4"><pre>4</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>3.142</pre></td>
  <td class="cell c3"><pre>-3.1415927</pre></td>
  <td class="cell c4"><pre>4</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumSigFigs_WrongSign.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer has the wrong algebraic sign.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>-3.142</pre></td>
  <td class="cell c3"><pre>3.1415927</pre></td>
  <td class="cell c4"><pre>4</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumSigFigs_WrongSign.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer has the wrong algebraic sign.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>-3.149</pre></td>
  <td class="cell c3"><pre>3.1415927</pre></td>
  <td class="cell c4"><pre>4</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumSigFigs_WrongSign. ATNumSigFigs_VeryInaccurate.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer has the wrong algebraic sign.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>2.15</pre></td>
  <td class="cell c3"><pre>75701719/35227192</pre></td>
  <td class="cell c4"><pre>3</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="notes">
  <td class="cell c0"><td colspan="6">Round teacher answer</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>0.0499</pre></td>
  <td class="cell c3"><pre>0.04985</pre></td>
  <td class="cell c4"><pre>3</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>0.0498</pre></td>
  <td class="cell c3"><pre>0.04985</pre></td>
  <td class="cell c4"><pre>3</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumSigFigs_Inaccurate.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The accuracy of your answer is not correct. Either you have not rounded correctly, or you have rounded an intermediate answer which propagates an error.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>0.0498</pre></td>
  <td class="cell c3"><pre>0.04975</pre></td>
  <td class="cell c4"><pre>3</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>0.0497</pre></td>
  <td class="cell c3"><pre>0.04975</pre></td>
  <td class="cell c4"><pre>3</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumSigFigs_Inaccurate.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The accuracy of your answer is not correct. Either you have not rounded correctly, or you have rounded an intermediate answer which propagates an error.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>0.0499</pre></td>
  <td class="cell c3"><pre>0.0498</pre></td>
  <td class="cell c4"><pre>3</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumSigFigs_Inaccurate.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The accuracy of your answer is not correct. Either you have not rounded correctly, or you have rounded an intermediate answer which propagates an error.</td></td>
</tr>
<tr class="notes">
  <td class="cell c0"><td colspan="6">Final zeros after the decimal are significant.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>1.5</pre></td>
  <td class="cell c3"><pre>1.500</pre></td>
  <td class="cell c4"><pre>3</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumSigFigs_WrongDigits.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer contains the wrong number of significant digits.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>1.50</pre></td>
  <td class="cell c3"><pre>1.500</pre></td>
  <td class="cell c4"><pre>3</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>1.500</pre></td>
  <td class="cell c3"><pre>1.500</pre></td>
  <td class="cell c4"><pre>3</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumSigFigs_WrongDigits.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer contains the wrong number of significant digits.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>245.0</pre></td>
  <td class="cell c3"><pre>245</pre></td>
  <td class="cell c4"><pre>3</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumSigFigs_WrongDigits.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer contains the wrong number of significant digits.</td></td>
</tr>
<tr class="notes">
  <td class="cell c0"><td colspan="6">Too few digits</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>180</pre></td>
  <td class="cell c3"><pre>178.35</pre></td>
  <td class="cell c4"><pre>3</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumSigFigs_WithinRange. ATNumSigFigs_Inaccurate.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The accuracy of your answer is not correct. Either you have not rounded correctly, or you have rounded an intermediate answer which propagates an error.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>33</pre></td>
  <td class="cell c3"><pre>33.1558</pre></td>
  <td class="cell c4"><pre>3</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumSigFigs_WrongDigits. ATNumSigFigs_Inaccurate.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer contains the wrong number of significant digits. The accuracy of your answer is not correct. Either you have not rounded correctly, or you have rounded an intermediate answer which propagates an error.</td></td>
</tr>
<tr class="notes">
  <td class="cell c0"><td colspan="6">Mixed options</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>3.142</pre></td>
  <td class="cell c3"><pre>3.1415927</pre></td>
  <td class="cell c4"><pre>[4,3]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>3.143</pre></td>
  <td class="cell c3"><pre>3.1415927</pre></td>
  <td class="cell c4"><pre>[4,3]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>3.150</pre></td>
  <td class="cell c3"><pre>3.1415927</pre></td>
  <td class="cell c4"><pre>[4,3]</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumSigFigs_Inaccurate.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The accuracy of your answer is not correct. Either you have not rounded correctly, or you have rounded an intermediate answer which propagates an error.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>3.211</pre></td>
  <td class="cell c3"><pre>3.1415927</pre></td>
  <td class="cell c4"><pre>[4,3]</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumSigFigs_VeryInaccurate.</td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>3.1416</pre></td>
  <td class="cell c3"><pre>3.1415927</pre></td>
  <td class="cell c4"><pre>[4,3]</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumSigFigs_WrongDigits.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer contains the wrong number of significant digits.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>0.1666</pre></td>
  <td class="cell c3"><pre>0.1667</pre></td>
  <td class="cell c4"><pre>[4,3]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>180</pre></td>
  <td class="cell c3"><pre>178.35</pre></td>
  <td class="cell c4"><pre>[3,1]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATNumSigFigs_WithinRange.</td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>33</pre></td>
  <td class="cell c3"><pre>33.1558</pre></td>
  <td class="cell c4"><pre>[3,1]</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumSigFigs_WrongDigits.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer contains the wrong number of significant digits.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>1.500</pre></td>
  <td class="cell c3"><pre>1.5</pre></td>
  <td class="cell c4"><pre>[3,1]</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumSigFigs_WrongDigits.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer contains the wrong number of significant digits.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>245.0</pre></td>
  <td class="cell c3"><pre>245</pre></td>
  <td class="cell c4"><pre>[3,1]</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumSigFigs_WrongDigits.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer contains the wrong number of significant digits.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>12345.7</pre></td>
  <td class="cell c3"><pre>12345.654321</pre></td>
  <td class="cell c4"><pre>[6,6]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>12345.7</pre></td>
  <td class="cell c3"><pre>12345.654321</pre></td>
  <td class="cell c4"><pre>[6,3]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>12300.0</pre></td>
  <td class="cell c3"><pre>12345.654321</pre></td>
  <td class="cell c4"><pre>[6,3]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>12400.0</pre></td>
  <td class="cell c3"><pre>12345.654321</pre></td>
  <td class="cell c4"><pre>[6,3]</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumSigFigs_Inaccurate.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The accuracy of your answer is not correct. Either you have not rounded correctly, or you have rounded an intermediate answer which propagates an error.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>13500.0</pre></td>
  <td class="cell c3"><pre>12345.654321</pre></td>
  <td class="cell c4"><pre>[6,3]</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumSigFigs_VeryInaccurate.</td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>12000.0</pre></td>
  <td class="cell c3"><pre>12345.654321</pre></td>
  <td class="cell c4"><pre>[6,2]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>13000.0</pre></td>
  <td class="cell c3"><pre>12345.654321</pre></td>
  <td class="cell c4"><pre>[6,2]</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumSigFigs_Inaccurate.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The accuracy of your answer is not correct. Either you have not rounded correctly, or you have rounded an intermediate answer which propagates an error.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>11000.0</pre></td>
  <td class="cell c3"><pre>12345.654321</pre></td>
  <td class="cell c4"><pre>[6,2]</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumSigFigs_Inaccurate.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The accuracy of your answer is not correct. Either you have not rounded correctly, or you have rounded an intermediate answer which propagates an error.</td></td>
</tr>
<tr class="notes">
  <td class="cell c0"><td colspan="6">Zero option and trailing zeros</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>0.0010</pre></td>
  <td class="cell c3"><pre>0</pre></td>
  <td class="cell c4"><pre>[1,0]</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumSigFigs_WrongDigits.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer contains the wrong number of significant digits.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>0.0010</pre></td>
  <td class="cell c3"><pre>0</pre></td>
  <td class="cell c4"><pre>[2,0]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>0.0010</pre></td>
  <td class="cell c3"><pre>0</pre></td>
  <td class="cell c4"><pre>[3,0]</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumSigFigs_WrongDigits.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer contains the wrong number of significant digits.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>0.001</pre></td>
  <td class="cell c3"><pre>0</pre></td>
  <td class="cell c4"><pre>[1,0]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>0.001</pre></td>
  <td class="cell c3"><pre>0</pre></td>
  <td class="cell c4"><pre>[2,0]</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumSigFigs_WrongDigits.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer contains the wrong number of significant digits.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>0.00100</pre></td>
  <td class="cell c3"><pre>null</pre></td>
  <td class="cell c4"><pre>[2,0]</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumSigFigs_WrongDigits.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer contains the wrong number of significant digits.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>0.00100</pre></td>
  <td class="cell c3"><pre>null</pre></td>
  <td class="cell c4"><pre>[3,0]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>0.00100</pre></td>
  <td class="cell c3"><pre>null</pre></td>
  <td class="cell c4"><pre>[4,0]</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumSigFigs_WrongDigits.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer contains the wrong number of significant digits.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>5.00</pre></td>
  <td class="cell c3"><pre>null</pre></td>
  <td class="cell c4"><pre>[2,0]</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumSigFigs_WrongDigits.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer contains the wrong number of significant digits.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>5.00</pre></td>
  <td class="cell c3"><pre>null</pre></td>
  <td class="cell c4"><pre>[3,0]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>5.00</pre></td>
  <td class="cell c3"><pre>null</pre></td>
  <td class="cell c4"><pre>[4,0]</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumSigFigs_WrongDigits.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer contains the wrong number of significant digits.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>100</pre></td>
  <td class="cell c3"><pre>0</pre></td>
  <td class="cell c4"><pre>[1,0]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>100</pre></td>
  <td class="cell c3"><pre>0</pre></td>
  <td class="cell c4"><pre>[2,0]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATNumSigFigs_WithinRange.</td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>100</pre></td>
  <td class="cell c3"><pre>0</pre></td>
  <td class="cell c4"><pre>[3,0]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6">ATNumSigFigs_WithinRange.</td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>100</pre></td>
  <td class="cell c3"><pre>0</pre></td>
  <td class="cell c4"><pre>[4,0]</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumSigFigs_WrongDigits.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer contains the wrong number of significant digits.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>10.0</pre></td>
  <td class="cell c3"><pre>0</pre></td>
  <td class="cell c4"><pre>[2,0]</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumSigFigs_WrongDigits.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer contains the wrong number of significant digits.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>10.0</pre></td>
  <td class="cell c3"><pre>0</pre></td>
  <td class="cell c4"><pre>[3,0]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>10.0</pre></td>
  <td class="cell c3"><pre>0</pre></td>
  <td class="cell c4"><pre>[4,0]</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumSigFigs_WrongDigits.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer contains the wrong number of significant digits.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>0</pre></td>
  <td class="cell c3"><pre>0</pre></td>
  <td class="cell c4"><pre>[1,0]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>0</pre></td>
  <td class="cell c3"><pre>0</pre></td>
  <td class="cell c4"><pre>[2,0]</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumSigFigs_WrongDigits.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer contains the wrong number of significant digits.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>0.00</pre></td>
  <td class="cell c3"><pre>0</pre></td>
  <td class="cell c4"><pre>[1,0]</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumSigFigs_WrongDigits.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer contains the wrong number of significant digits.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>0.00</pre></td>
  <td class="cell c3"><pre>0</pre></td>
  <td class="cell c4"><pre>[2,0]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>0.00</pre></td>
  <td class="cell c3"><pre>0</pre></td>
  <td class="cell c4"><pre>[3,0]</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumSigFigs_WrongDigits.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer contains the wrong number of significant digits.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>0.00</pre></td>
  <td class="cell c3"><pre>0</pre></td>
  <td class="cell c4"><pre>[4,0]</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumSigFigs_WrongDigits.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer contains the wrong number of significant digits.</td></td>
</tr>
<tr class="notes">
  <td class="cell c0"><td colspan="6">Condone too many sfs.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>8.250</pre></td>
  <td class="cell c3"><pre>8.250</pre></td>
  <td class="cell c4"><pre>[4,-1]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>8.25</pre></td>
  <td class="cell c3"><pre>8.250</pre></td>
  <td class="cell c4"><pre>[4,-1]</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumSigFigs_WrongDigits.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer contains the wrong number of significant digits.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>8.250000</pre></td>
  <td class="cell c3"><pre>8.250</pre></td>
  <td class="cell c4"><pre>[4,-1]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>8.250434</pre></td>
  <td class="cell c3"><pre>8.250</pre></td>
  <td class="cell c4"><pre>[4,-1]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>82.4</pre></td>
  <td class="cell c3"><pre>82</pre></td>
  <td class="cell c4"><pre>[2,-1]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>82.5</pre></td>
  <td class="cell c3"><pre>82</pre></td>
  <td class="cell c4"><pre>[2,-1]</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumSigFigs_Inaccurate.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The accuracy of your answer is not correct. Either you have not rounded correctly, or you have rounded an intermediate answer which propagates an error.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>83</pre></td>
  <td class="cell c3"><pre>82</pre></td>
  <td class="cell c4"><pre>[2,-1]</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumSigFigs_Inaccurate.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The accuracy of your answer is not correct. Either you have not rounded correctly, or you have rounded an intermediate answer which propagates an error.</td></td>
</tr>
<tr class="notes">
  <td class="cell c0"><td colspan="6">1/7 = 0.142857142857...</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>0.1430</pre></td>
  <td class="cell c3"><pre>1/7</pre></td>
  <td class="cell c4"><pre>[4,-1]</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumSigFigs_Inaccurate.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The accuracy of your answer is not correct. Either you have not rounded correctly, or you have rounded an intermediate answer which propagates an error.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>0.1429</pre></td>
  <td class="cell c3"><pre>1/7</pre></td>
  <td class="cell c4"><pre>[4,-1]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>0.1428</pre></td>
  <td class="cell c3"><pre>1/7</pre></td>
  <td class="cell c4"><pre>[4,-1]</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumSigFigs_Inaccurate.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The accuracy of your answer is not correct. Either you have not rounded correctly, or you have rounded an intermediate answer which propagates an error.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>0.143</pre></td>
  <td class="cell c3"><pre>1/7</pre></td>
  <td class="cell c4"><pre>[4,-1]</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumSigFigs_WrongDigits. ATNumSigFigs_Inaccurate.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer contains the wrong number of significant digits. The accuracy of your answer is not correct. Either you have not rounded correctly, or you have rounded an intermediate answer which propagates an error.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>0.14284</pre></td>
  <td class="cell c3"><pre>1/7</pre></td>
  <td class="cell c4"><pre>[4,-1]</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumSigFigs_Inaccurate.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The accuracy of your answer is not correct. Either you have not rounded correctly, or you have rounded an intermediate answer which propagates an error.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>0.14285</pre></td>
  <td class="cell c3"><pre>1/7</pre></td>
  <td class="cell c4"><pre>[4,-1]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>0.14286</pre></td>
  <td class="cell c3"><pre>1/7</pre></td>
  <td class="cell c4"><pre>[4,-1]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>0.14291</pre></td>
  <td class="cell c3"><pre>1/7</pre></td>
  <td class="cell c4"><pre>[4,-1]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>0.14294</pre></td>
  <td class="cell c3"><pre>1/7</pre></td>
  <td class="cell c4"><pre>[4,-1]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>0.14295</pre></td>
  <td class="cell c3"><pre>1/7</pre></td>
  <td class="cell c4"><pre>[4,-1]</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumSigFigs_Inaccurate.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The accuracy of your answer is not correct. Either you have not rounded correctly, or you have rounded an intermediate answer which propagates an error.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>0.142</pre></td>
  <td class="cell c3"><pre>1/7</pre></td>
  <td class="cell c4"><pre>[2,-1]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>0.14290907676</pre></td>
  <td class="cell c3"><pre>1/7</pre></td>
  <td class="cell c4"><pre>[2,-1]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>0.143</pre></td>
  <td class="cell c3"><pre>1/7</pre></td>
  <td class="cell c4"><pre>[2,-1]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>0.1433333</pre></td>
  <td class="cell c3"><pre>1/7</pre></td>
  <td class="cell c4"><pre>[2,-1]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>0.144</pre></td>
  <td class="cell c3"><pre>1/7</pre></td>
  <td class="cell c4"><pre>[2,-1]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>0.145</pre></td>
  <td class="cell c3"><pre>1/7</pre></td>
  <td class="cell c4"><pre>[2,-1]</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>0.146</pre></td>
  <td class="cell c3"><pre>1/7</pre></td>
  <td class="cell c4"><pre>[2,-1]</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumSigFigs_Inaccurate.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The accuracy of your answer is not correct. Either you have not rounded correctly, or you have rounded an intermediate answer which propagates an error.</td></td>
</tr>
<tr class="notes">
  <td class="cell c0"><td colspan="6">Logarithms, numbers and surds</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>1.279</pre></td>
  <td class="cell c3"><pre>ev(lg(19),lg=logbasesimp)</pre></td>
  <td class="cell c4"><pre>4</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>3.14</pre></td>
  <td class="cell c3"><pre>pi</pre></td>
  <td class="cell c4"><pre>3</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>3.15</pre></td>
  <td class="cell c3"><pre>pi</pre></td>
  <td class="cell c4"><pre>3</pre></td>
  <td class="cell c5">0</td>
  <td class="cell c6">ATNumSigFigs_Inaccurate.</td>
</tr>
<tr class="pass">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The accuracy of your answer is not correct. Either you have not rounded correctly, or you have rounded an intermediate answer which propagates an error.</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>1.73205</pre></td>
  <td class="cell c3"><pre>sqrt(3)</pre></td>
  <td class="cell c4"><pre>6</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="notes">
  <td class="cell c0"><td colspan="6">No support for matrices!</td></td>
</tr>
<tr class="expectedfail">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:orange;"><i class="fa fa-adjust"></i></span></td>
  <td class="cell c2"><pre>matrix([0.33,1],[1,1])</pre></td>
  <td class="cell c3"><pre>matrix([0.333,1],[1,1])</pre></td>
  <td class="cell c4"><pre>2</pre></td>
  <td class="cell c5">-1</td>
  <td class="cell c6">ATNumSigFigs_NotDecimal.</td>
</tr>
<tr class="expectedfail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">Your answer should be a decimal number, but is not!</td></td>
</tr>
<tr class="expectedfail">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:orange;"><i class="fa fa-adjust"></i></span></td>
  <td class="cell c2"><pre>3.1415</pre></td>
  <td class="cell c3"><pre>matrix([0.333,1],[1,1])</pre></td>
  <td class="cell c4"><pre>2</pre></td>
  <td class="cell c5">-1</td>
  <td class="cell c6">TEST_FAILED</td>
</tr>
<tr class="expectedfail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">TEST_FAILED</td></td>
</tr>
<tr class="expectedfail">
  <td class="cell c0"><td colspan="2"></td></td>
  <td class="cell c1"><td colspan="4">The answer test failed to execute correctly: please alert your teacher. sigfigsfun(x,n,d) requires a real number, or a list of real numbers, as a first argument.  Received:  matrix([0.333,1],[1,1])</td></td>
</tr>
<tr class="notes">
  <td class="cell c0"><td colspan="6">Teacher uses dispsf</td></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>1.50</pre></td>
  <td class="cell c3"><pre>dispsf(1.500,3)</pre></td>
  <td class="cell c4"><pre>3</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr>
<tr class="pass">
  <td class="cell c0">NumSigFigs</td>
  <td class="cell c1"><span style="color:green;"><i class="fa fa-check"></i></span></td>
  <td class="cell c2"><pre>1.50</pre></td>
  <td class="cell c3"><pre>dispdp(1.500,3)</pre></td>
  <td class="cell c4"><pre>3</pre></td>
  <td class="cell c5">1</td>
  <td class="cell c6"></td>
</tr></tbody></table></div>
<?php
// This file is part of Stack - http://stack.bham.ac.uk//
//
// Stack is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Stack is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Stack.  If not, see <http://www.gnu.org/licenses/>.


/**
 * Language strings for the Stack question type.
 *
 * @package    qtype_stack
 * @copyright  2012 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Stack';
$string['pluginname_help'] = 'Stack is a maths assessment system ...';
$string['pluginnameadding'] = 'Adding a stack question';
$string['pluginnameediting'] = 'Editing a stack question';
$string['pluginnamesummary'] = 'Stack questions use a computer algebra system to mark the students work. ...';

// general strings
$string['errors'] = 'Errors';
$string['debuginfo'] = 'Debug info';

// admin settings
$string['settingcasdebugging'] = 'CAS debugging';
$string['settingcasdebugging_desc'] = 'Whether to store debugging information about the CAS connection.';
$string['settingcasmaximaversion'] = 'Maxima version';
$string['settingcasmaximaversion_desc'] = 'The version of Maxima being used.';
$string['settingcastimeout'] = 'CAS connection timeout';
$string['settingcastimeout_desc'] = 'The timout to use when trying to connect to Maxima.';
$string['settingplatformtype'] = 'Platform type';
$string['settingplatformtype_desc'] = 'Stack needs to know what sort of operating system it is running on.';
$string['settingplatformtypeunix'] = 'Linux';
$string['settingplatformtypewin']  = 'Windows';
$string['settingplatformtypeserver'] = 'Server';
$string['settingplatformmaximacommand'] = 'Maxima command';
$string['settingplatformmaximacommand_desc'] = 'Stack needs to know the shell command to start Maxima.  If this is blank, Stack will make an educated guess.';
$string['settingplatformplotcommand'] = 'Plot command';
$string['settingplatformplotcommand_desc'] = 'Stack needs to know the gnuplot command.  If this is blank, Stack will make an educated guess.';

// Strings used by interation elements
$string['false'] = 'False';
$string['notanswered'] = 'Not answered';
$string['true'] = 'True';
$string['ddl_empty'] = 'No choices were provided for this drop-down. Please input a set of values link a,b,c,d';

// Support scripts (CAS chat, healthcheck, etc.)
$string['all'] = 'All';
$string['chat'] = 'Send to the CAS';
$string['chat_desc'] = 'The <a href="{$a->link}">CAS chat script</a> lets you test the connection to the CAS, and try out Maxima syntax.';
$string['chatintro'] = 'This page enables CAS text to be evaluated directly. It is a simple script which is a useful minimal example, and a handy way to check if the CAS is working, and to test various inputs.';
$string['chattitle'] = 'Test the connection to the CAS';
$string['healthcheck'] = 'STACK healthcheck';
$string['healthcheck_desc'] = 'The <a href="{$a->link}">healthcheck script</a> helps you verify that all aspects of Stack are working properly.';
$string['healthcheckconfig'] = 'Maxima configuration file';
$string['healthcheckconfigintro'] = 'Trying to automatically write the Maxima configuration file.';
$string['healthcheckconnect'] = 'Trying to connect to the CAS';
$string['healthcheckconnectintro'] = 'We are trying to evaluate the following cas text:';
$string['healthchecklatex'] = 'Check LaTeX is being converted correctly';
$string['healthchecklatexintro'] = 'STACK generates LaTeX on the fly, and enables teachers to write LaTeX in questions. It assumes that LaTeX will be converted by a moodle filter.  Below are samples of displayed and inline expressions in LaTeX which should be appear correctly in your browser.  Problems here indicate incorrect moodle filter settings, not faults with STACK itself. Stack only uses the single and double dollar notation itself, but some question authors may be relying on the other forms.';
$string['healthchecklatexmathjax'] = 'One way to get equiation rendering to work is to copy the following code into the <b>Within HEAD</b> setting on <a href="{$a}">Additional HTML</a>.';
$string['healthcheckmaximabat'] = 'The maxima.bat file is missing';
$string['healthcheckmaximabatinfo'] = 'This script tried to automatically copy the maxima.bat script from inside "C:\Program files\Maxima-1.xx.y\bin" into "{$a}\stack". However, this seems not to have worked. Please copy this file manually.';
$string['healthcheckplots'] = 'Graph plotting';
$string['healthcheckplotsintro'] = 'There should be two different plots.  If two identical plots are seen then this is an error in naming the plot files. If no errors are returned, but a plot is not displayed then one of the following may help.  (i) check read permissions on the two temporary directories. (ii) change the options used by GNUPlot to create the plot. Currently there is no web interface to these options.';
$string['stackInstall_testsuite_title'] = 'A test suite for STACK Answer tests';
$string['stackInstall_testsuite_title_desc'] = 'The <a href="{$a->link}">answer-tests script</a> verifies that the answer tests are performing correctly. They are also useful to learn by example how each answer-test can be used.';
$string['stackInstall_testsuite_intro'] = 'This page allows you to test that the STACK answer tests are functioning correctly.  Note that only answer tests can be checked through the web interface.  Other Maxima commands need to be checked from the command line: see unittests.mac.';
$string['stackInstall_testsuite_choose'] = 'Please choose an answer test.';
$string['stackInstall_testsuite_pass'] = 'All tests passed!';
$string['stackInstall_testsuite_fail'] = 'Not all tests passed!';
$string['answertest'] = 'Answer test';
$string['testsuitecolpassed'] = 'Passed?';
$string['studentanswer'] = 'Student response';
$string['teacheranswer'] = 'Teacher answer';
$string['options'] = 'Options';
$string['testsuitecolerror'] = 'CAS errors';
$string['testsuitecolrawmark'] = 'Raw mark';
$string['testsuitecolexpectedscore'] = 'Expected mark';
$string['testsuitepass'] = 'Pass';
$string['testsuitefail'] = 'Fail';
$string['feedback'] = 'Feedback';
$string['answernote'] = 'Answer note';

$string['texdisplayedbracket'] = 'Displayed bracket';
$string['texinlinebracket'] = 'Inline bracket';
$string['texdoubledollar'] = 'Double dollar';
$string['texsingledollar'] = 'Single dollar';

// casstring.class.php
$string['stackCas_spaces']                  = 'Spaces found in expression {$a->expr}.';
$string['stackCas_percent']                 = '&#037; found in expression {$a->expr}.';
$string['stackCas_missingLeftBracket']      = 'You have a missing left bracket <span class="SyntaxExample2">{$a->bracket}</span> in the expression: {$a->cmd}.';
$string['stackCas_missingRightBracket']     = 'You have a missing right bracket <span class="SyntaxExample2">{$a->bracket}</span> in the expression: {$a->cmd}.';
$string['stackCas_apostrophe']              = 'Apostrophes are not permitted in responses.';
$string['stackCas_forbiddenChar']           = 'CAS commands may not contain the following characters: {$a->char}.';
$string['stackCas_finalChar']               = '\'{$a->char}\' is an invalid final character in {$a->cmd}';
$string['stackCas_MissingStars']            = 'You seem to be missing *\'s. Perhaps you meant to type {$a->cmd}.';
$string['stackCas_unknownFunction']         = 'Unknown function: {$a->forbid}.';
$string['stackCas_unsupportedKeyword']      = 'Unsupported keyword: {$a->forbid}.';
$string['stackCas_forbiddenWord']           = 'The expression {$a->forbid} is forbidden.';

// cassession.class.php
$string['stackCas_CASError']                = 'The CAS returned the following error(s):';
$string['stackCas_allFailed']               = 'CAS failed to return any evaluated expressions.  Please check your connection with the CAS.';
$string['stackCas_failedReturn']            = 'CAS failed to return any data.';

// castext.class.php
$string['stackCas_tooLong']                 = 'CASText statement is too long.';
$string['stackCas_MissingAt']               = 'You are missing a @ sign.';
$string['stackCas_MissingDollar']           = 'You are missing a $ sign';
$string['stackCas_MissingOpenHint']         = 'Missing opening hint';
$string['stackCas_MissingClosingHint']      = 'Missing closing /hint';
$string['stackCas_MissingOpenDisplay']      = 'Missing \[';
$string['stackCas_MissingCloseDisplay']     = 'Missing \]';
$string['stackCas_MissingOpenInline']       = 'Missing \(';
$string['stackCas_MissingCloseInline']      = 'Missing \)';
$string['stackCas_MissingOpenHTML']         = 'Missing opening html tag';
$string['stackCas_MissingCloseHTML']        = 'Missing closing html tag';
$string['stackCas_failedValidation']        = 'CASText failed validation. ';
$string['stackCas_invalidCommand']          = 'CAS commands not valid. ';
$string['stackCas_CASErrorCaused']          = 'caused the following error:';

$string['Maxima_DivisionZero']  = 'Division by zero.';

// Answer tests
$string['stackOptions_AnsTest_values_AlgEquiv']           =  "AlgEquiv";
$string['stackOptions_AnsTest_values_EqualComAss']        =  "EqualComAss";
$string['stackOptions_AnsTest_values_CASEqual']           =  "CasEqual";
$string['stackOptions_AnsTest_values_SameType']           =  "SameType";
$string['stackOptions_AnsTest_values_SubstEquiv']         =  "SubstEquiv";
$string['stackOptions_AnsTest_values_SysEquiv']           =  "SysEquiv";
$string['stackOptions_AnsTest_values_Expanded']           =  "Expanded";
$string['stackOptions_AnsTest_values_FacForm']            =  "FacForm";
$string['stackOptions_AnsTest_values_SingleFrac']         =  "SingleFrac";
$string['stackOptions_AnsTest_values_PartFrac']           =  "PartFrac";
$string['stackOptions_AnsTest_values_CompSquare']         =  "CompletedSquare";
$string['stackOptions_AnsTest_values_Num_tol_relative']   =  "NumRelative";
$string['stackOptions_AnsTest_values_Num_tol_absolute']   =  "NumAbsolute";
$string['stackOptions_AnsTest_values_Num_sig_figs']       =  "NumSigFigs";
$string['stackOptions_AnsTest_values_GT']                 =  "Num-GT";
$string['stackOptions_AnsTest_values_GTE']                =  "Num-GTE";
$string['stackOptions_AnsTest_values_LowestTerms']        =  "LowestTerms";
$string['stackOptions_AnsTest_values_Diff']               =  "Diff";
$string['stackOptions_AnsTest_values_Int']                =  "Int";
$string['stackOptions_AnsTest_values_String']             =  "String";
$string['stackOptions_AnsTest_values_StringSloppy']       =  "StringSloppy";
$string['stackOptions_AnsTest_values_RegExp']             =  "RegExp";

$string['AT_NOTIMPLEMENTED']        = 'This answer test has not been implemented. ';
$string['TEST_FAILED']              = 'The answer test failed to execute correctly: please alert your teacher. ';
$string['AT_MissingOptions']        = 'Missing variable in CAS Option field. ';
$string['AT_InvalidOptions']        = 'Option field is invalid. {$a->errors}';

$string['ATAlgEquiv_SA_not_expression'] = 'Your answer should be an expression, not an equation, inequality, list, set or matrix. ';
$string['ATAlgEquiv_SA_not_matrix']     = 'Your answer should be a matrix, but is not. ';
$string['ATAlgEquiv_SA_not_list']       = 'Your answer should be a list, but is not.  Note that the syntax to enter a list is to enclose the comma separated values with square brackets. ';
$string['ATAlgEquiv_SA_not_set']        = 'Your answer should be a set, but is not.  Note that the syntax to enter a set is to enclose the comma separated values with curly brackets. ';
$string['ATAlgEquiv_SA_not_equation']   = 'Your answer should be an equation, but is not. ';
$string['ATAlgEquiv_TA_not_equation']   = 'Your answer is an equation, but the expression to which it is being compared is not.  You may have typed something like y=2 when you only needed to type 2. ';
$string['ATAlgEquiv_SA_not_inequality'] = 'Your answer should be an inequality, but is not. ';
$string['Subst']                        = 'Your answer would be correct if you used the following substitution of variables. {$a->m0} ';


$string['ATInequality_nonstrict']       = 'Your inequality should be strict, but is not! ';
$string['ATInequality_strict']          = 'Your inequality should not be strict! ';
$string['ATInequality_backwards']       = 'Your inequality appears to be backwards. ';

$string['ATLowestTerms_wrong']          = 'You need to cancel fractions within your answer. ';
$string['ATLowestTerms_entries']        = 'The following terms in your answer are not in lowest terms.  {$a->m0} Please try again.  ';


$string['ATList_wronglen']          = 'Your list should have {$a->m0} elements, but it actually has {$a->m1}. ';
$string['ATList_wrongentries']      = 'The entries in red below are those that are incorrect. {$a->m0} ';

$string['ATMatrix_wrongsz']         = 'Your matrix should be {$a->m0} by {$a->m1}, but it is actually {$a->m2} by {$a->m3}. ';
$string['ATMatrix_wrongentries']    = 'The entries in red below are those that are incorrect. {$a->m0} ';

$string['ATSet_wrongsz']            = 'Your set should have {$a->m0} different elements, but it is actually has {$a->m1}. ';
$string['ATSet_wrongentries']       = 'The following entries are incorrect, although they may appear in a simplified form from that which you actually entered. {$a->m0} ';

$string['irred_Q_factored']         = 'The term {$a->m0} should be unfactored, but is not. ';
$string['irred_Q_commonint']        = 'You need to take out a common factor. ';  // needs a space at the end.
$string['irred_Q_optional_fac']     = 'You could do more work, since {$a->m0} can be further factored.  However, you don\'t need to. ';

$string['FacForm_UnPick_morework']  = 'You could still do some more work on the term {$a->m0}. ';
$string['FacForm_UnPick_intfac']    = $string['irred_Q_commonint'];

$string['ATFacForm_error_list']     = 'The answer test failed.  Please contact your systems administrator';
$string['ATFacForm_error_degreeSA'] = 'The CAS could not establish the algebraic degree of your answer.';
$string['ATFacForm_isfactored']     = 'Your answer is factored, well done. ';  // needs a space at the end.
$string['ATFacForm_notfactored']    = 'Your answer is not factored. '; // needs a space at the end.
$string['ATFacForm_notalgequiv']    = 'Note that your answer is not algebraically equivalent to the correct answer.  You must have done something wrong. '; // needs a space at the end.

$string['ATPartFrac_error_list']        = $string['ATFacForm_error_list'];
$string['ATPartFrac_true']              = '';
$string['ATPartFrac_single_fraction']   ='Your answer seems to be a single fraction, it needs to be in a partial fraction form. ';
$string['ATPartFrac_diff_variables']    ='The variables in your answer are different to those of the question, please check them. ';
$string['ATPartFrac_denom_ret']         ='If your answer is written as a single fraction then the denominator would be {$a->m0}. In fact, it should be {$a->m1}. ';
$string['ATPartFrac_ret_expression']    ='Your answer as a single fraction is {$a->m0} ';

$string['ATSingleFrac_error_list']     = $string['ATFacForm_error_list'];
$string['ATSingleFrac_true']           = '';
$string['ATSingleFrac_part']           = 'Your answer needs to be a single fraction of the form \( {a}\over{b} \). ';
$string['ATSingleFrac_var']            = 'The variables in your answer are different to the those of the question, please check them. ';
$string['ATSingleFrac_ret_exp']        = 'Your answer is not algebraically equivalent to the correct answer. You must have done something wrong. ';
$string['ATSingleFrac_div']            = 'Your answer contains fractions within fractions.  You need to clear these and write your answer as a single fraction.';

$string['ATCompSquare_true']            = '';
$string['ATCompSquare_false']           = '';
$string['ATCompSquare_not_AlgEquiv']    = 'Your answer appears to be in the correct form, but is not equivalent to the correct answer.';
$string['ATCompSquare_false_no_summands']     = 'The completed square is of the form \( a(\cdots\cdots)^2 + b\) where \(a\) and \(b\) do not depend on your variable.  More than one of your summands appears to depend on the variable in your answer.';


$string['ATInt_error_list']         = $string['ATFacForm_error_list'];
$string['ATInt_const_int']          = 'You need to add a constant of integration. This should be an arbitrary constant, not a number.';
$string['ATInt_const']              = 'You need to add a constant of integration, otherwise this appears to be correct.  Well done.';
$string['ATInt_EqFormalDiff']       = 'The formal derivative of your answer does equal the expression that you were asked to integrate.  However, your answer differs from the correct answer in a significant way, that is to say not just, eg, a constant of integration.  Please ask your teacher about this.';
$string['ATInt_wierdconst']         = 'The formal derivative of your answer does equal the expression that you were asked to integrate.  However, you have a strange constant of integration.  Please ask your teacher about this.';
$string['ATInt_diff']               = 'It looks like you have differentiated instead!';
$string['ATInt_generic']            = 'The derivative of your answer should be equal to the expression that you were asked to integrate, that was: {$a->m0}  In fact, the derivative of your answer, with respect to {$a->m1} is: {$a->m2} so you must have done something wrong!';

$string['ATDiff_error_list']        = $string['ATFacForm_error_list'];
$string['ATDiff_int']               = 'It looks like you have integrated instead!';

$string['ATNumSigFigs_error_list']        = $string['ATFacForm_error_list'];
$string['ATNumSigFigs_NotDecimal']  = 'Your answer should be a decimal number, but is not! ';
$string['ATNumSigFigs_Inaccurate']  = 'The accuracy of your answer is not correct.  Either you have not rounded correctly, or you have rounded an intermediate answer which propagates an error.';
$string['ATNumSigFigs_WrongDigits'] = 'Your answer contains the wrong number of significant digits. ';

$string['ATSysEquiv_SA_not_list']               = 'Your answer should be a list, but it is not!';
$string['ATSysEquiv_SB_not_list']               = 'The Teacher\'s answer is not a list.  Please contact your teacher.';
$string['ATSysEquiv_SA_not_eq_list']            = 'Your answer should be a list of equations, but it is not!';
$string['ATSysEquiv_SB_not_eq_list']            = 'Teacher answer is not a list of equations';
$string['ATSysEquiv_SA_not_poly_eq_list']       = 'One or more of your equations is not a polynomial!';
$string['ATSysEquiv_SB_not_poly_eq_list']       = 'The Teacher\'s answer should be a list of polynomial equations, but is not.  Please contact your teacher.';
$string['ATSysEquiv_SA_missing_variables']      = 'Your answer is missing one or more variables!';
$string['ATSysEquiv_SA_extra_variables']        = 'Your answer includes too many variables!';
$string['ATSysEquiv_SA_system_underdetermined']	= 'The equations in your system appear to be correct, but you need others besides.';
$string['ATSysEquiv_SA_system_overdetermined'] 	= 'The entries in red below are those that are incorrect. {$a->m0} ';



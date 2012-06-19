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

$string['pluginname'] = 'STACK';
$string['pluginname_help'] = 'STACK is an assessment system for mathematics.';
$string['pluginnameadding'] = 'Adding a STACK question';
$string['pluginnameediting'] = 'Editing a STACK question';
$string['pluginnamesummary'] = 'STACK provides mathematical questions for the moodle quiz.  These use a computer algebra system to establish the mathematical properties of the student\'s responses.';

// General strings.
$string['errors'] = 'Errors';
$string['debuginfo'] = 'Debug info';
$string['exceptionmessage'] = '{$a}';

// Strings used on the editing form.
$string['addanothernode'] = 'Add another node';
$string['answernote'] = 'Answer note';
$string['answernote_err'] = 'Answer notes may not contain the character |.  This character is inserted by STACK and is later used to split answer notes automatically.';
$string['answernote_help'] = 'This is a tag which is key for reporting purposes.  It is designed to record the unique path through the tree, and the outcome of each answer test.  This is automatically generated, but can be changed to something meaningful.';
$string['answernote_link'] = 'question/type/stack/doc/doc.php/Authoring/Potential_response_trees.md#Answer_note';
$string['assumepositive'] = 'Assume postitive';
$string['assumepositive_help'] = 'This option sets the value of Maxima\'s assume_pos variable.';
$string['assumepositive_link'] = 'question/type/stack/doc/doc.php/Authoring/Options.md#Assume_Positive';
$string['autosimplify'] = 'Auto-simplify';
$string['autosimplify_help'] = 'Sets the variable "simp" within Maxima for this potential response tree.';
$string['autosimplify_link'] = 'question/type/stack/doc/doc.php/CAS/Maxima.md#Simplification';
$string['boxsize'] = 'Input box size';
$string['boxsize_help'] = 'Width of the html formfield.';
$string['boxsize_link'] = 'question/type/stack/doc/doc.php/Authoring/Inputs.md#Box_Size';
$string['checkanswertype'] = 'Check the type of the response';
$string['checkanswertype_help'] = 'If yes, answers which are of a different "type" (e.g. expression, equation, matrix, list, set) are rejected as invalid.';
$string['checkanswertype_link'] = '/question/type/stack/doc/doc.php/Authoring/Inputs.md#Check_Type';
$string['complexno'] = 'Meaning and display of sqrt(-1)';
$string['complexno_help'] = 'Controls the meaning and display of the symbol i and sqrt(-1)';
$string['complexno_link'] = 'question/type/stack/doc/doc.php/Authoring/Options.md#sqrt_minus_one.';
$string['defaultprtcorrectfeedback'] = 'Correct answer, well done.';
$string['defaultprtincorrectfeedback'] = 'Incorrect answer.';
$string['defaultprtpartiallycorrectfeedback'] = 'Your answer is partially correct.';
$string['feedback'] = 'Feedback';
$string['feedback_help'] = 'This is CASText which may depend on any of the question variables, input elements or the feedback variables. This is evaluated and displayed to the student if they pass down this branch.';
$string['inputtest'] ='Input test';
$string['feedbackvariables'] = 'Feedback variables';
$string['feedbackvariables_help'] = 'The feedback variables enable you to manipulate any of the inputs, together with the question variables, prior to traversing the tree.  Variables defined here may be used anywhere else in this tree.';
$string['feedbackvariables_link'] = 'question/type/stack/doc/doc.php/Authoring/KeyVals.md#Feedback_variables';
$string['forbidfloat'] = 'Forbid float';
$string['forbidfloat_help'] = 'If set to yes, then any answer of the student which has a floating point number will be rejected as invalid.';
$string['forbidfloat_link'] = '/question/type/stack/doc/doc.php/Authoring/Inputs.md#Forbid_Floats';
$string['forbidwords'] = 'Forbidden words ';
$string['forbidwords_help'] = 'This is a comma separated list of text strings which are forbidden in a student\'s answer.';
$string['forbidwords_link'] = 'question/type/stack/doc/doc.php/Authoring/CASText.md#Forbidden_Words';
$string['generalfeedback'] = 'General feedback';
$string['generalfeedback_help'] = 'General feedback is CASText. General feedback, also known as a "worked solution", is shown to the student after they have attempted the question. Unlike feedback, which depends on what response the student gave, the same general feedback text is shown to all students.  It may depend on the question variables used in the version of the question.';
$string['generalfeedback_link'] = 'question/type/stack/doc/doc.php/Authoring/CASText.md#general_feedback';
$string['generalfeedbacktags'] = 'General feedback must not contain the token(s) \'{$a}\'.';
$string['showvalidation'] = 'Show the validation';
$string['showvalidation_help'] = 'Setting this option displays any validation feedback from this input, including echoing back their expression in traditional two dimensional notation.';
$string['showvalidation_link'] = 'Show the validation';
$string['inputheading'] = 'Input: {$a}';
$string['inputtype'] = 'Input type';
$string['inputtype_help'] = 'This determines the type of the input element, e.g. form field, true/false, text area.';
$string['inputtype_link'] = 'question/type/stack/doc/doc.php/Authoring/Inputs.md';
$string['inputtypealgebraic'] = 'Algebraic input';
$string['inputtypeboolean'] = 'True/False';
$string['inputtypedropdown'] = 'Drop down list';
$string['inputtypesinglechar'] = 'Single character';
$string['inputtypetextarea'] = 'Text area';
$string['inputtypematrix'] = 'Matrix';
$string['insertstars'] = 'Insert stars';
$string['insertstars_help'] = 'If set to yes then the system will automatically insert *s into any patterns identified by Strict Syntax.  Otherwise, it thows an error.';
$string['insertstars_link'] = '/question/type/stack/doc/doc.php/Authoring/Inputs.md#Insert_Stars';
$string['multiplicationsign'] = 'Multiplication sign';
$string['multiplicationsign_help'] = 'Controls how multiplication signs are displayed.';
$string['multiplicationsign_link'] = 'question/type/stack/doc/doc.php/Authoring/Options.md#multiplication';
$string['multcross'] = 'Cross';
$string['multdot'] = 'Dot';
$string['mustverify'] = 'Student must verify';
$string['mustverify_help'] = 'Specifies whether the student\'s input is presented back to them before scoring.';
$string['mustverify_link'] = 'question/type/stack/doc/doc.php/Authoring/Inputs.md#Student_must_verify';
$string['next'] = 'Next';
$string['nodexfalsefeedback'] = 'Node {no} false feedback';
$string['nodextruefeedback'] = 'Node {no} true feedback';
$string['nodexwhenfalse'] = 'Node {no} when false';
$string['nodexwhentrue'] = 'Node {no} when true';
$string['nodex'] = 'Node {$a}';
$string['nonempty'] = 'This must not be empty.';
$string['penalty'] = 'Penalty';
$string['penalty_help'] = 'The penalty scheme deducts this value from the result of each PRT for each different and valid attempt which is not completely correct.';
$string['penalty_link'] = 'question/type/stack/doc/doc.php/Authoring/Feedback.md';
$string['penaltyerror'] = 'The penalty must be a numeric value between 0 and 1.';
$string['penaltyerror2'] = 'The penalty must empty, or be a numeric value between 0 and 1.';
$string['prtcorrectfeedback'] = 'Standard feedback for correct';
$string['prtheading'] = 'Potential response tree: {$a}';
$string['prtincorrectfeedback'] = 'Standard feedback for incorrect';
$string['prtpartiallycorrectfeedback'] = 'Standard feedback for partially correct';
$string['prtwillbecomeactivewhen'] = 'This potential response tree will become active when the student has answered: {$a}';
$string['questionnote'] = 'Question note';
$string['questionnote_help'] = 'The question note is CASText.  The purpose of a question note is to distinguish between random versions of a question. Two question versions are equal if and only if the question notes are equal.  In later analysis it is very helpful to leave a meaningful question note.';
$string['questionnote_link'] = 'question/type/stack/doc/doc.php/Authoring/Question_note.md';
$string['questionnotetags'] = 'The question note must not contain the token(s) \'{$a}\'.';
$string['questionnotempty'] = 'The question note cannot be empty when rand() appears in the question variables.  The question note is used to distinguish between different random versions of the question.';
$string['questionsimplify'] = 'Question-level simplify';
$string['questionsimplify_help'] = 'Sets the global variable "simp" within Maxima for the whole question.';
$string['questionsimplify_link'] = 'question/type/stack/doc/doc.php/CAS/Maxima.md#Simplification';
$string['questiontext'] = 'Question text';
$string['questiontext_help'] = 'The question text is CASText.  This is the "question" which the student actually sees.  You must put input elements, and the validation strings, in this field, and only in this field.  For example, using `[[input:ans1]] [[validation:ans1]]`.';
$string['questiontext_link'] = 'question/type/stack/doc/doc.php/Authoring/CASText.md#question_text';
$string['questiontextnonempty'] = 'The question text must be non-empty.';
$string['questiontextmustcontain'] = 'The question text must contain the token(s) \'{$a}\'.';
$string['questiontextonlycontain'] = 'The question text contains too many copies of the token(s) \'{$a}\'.  This/these must appear once and only once.';
$string['questiontextfeedbackmustcontain'] = 'The question text combined with the specific feedback, must contain the token(s) \'{$a}\'.';
$string['questiontextfeedbackonlycontain'] = 'The question text combined with the specific feedback, contains too many copies of the token(s) \'{$a}\'.  This/these must appear once and only once.';
$string['questionvalue'] = 'Question value';
$string['questionvaluepostive'] = 'Question value must be positive';
$string['questionvariables'] = 'Question variables';
$string['questionvariables_help'] = 'This field allows you to define and manipulate CAS variables, e.g. to create random versions.  These are available to all other parts of the question.';
$string['questionvariables_link'] = 'question/type/stack/doc/doc.php/Authoring/KeyVals.md';
$string['quiet'] = 'Quiet';
$string['quiet_help'] = 'When set to yes any feedback automatically generated by the answer tests is surpressed, and not displayed to the student.  The feedback fields in the branches are unaffected by this option.';
$string['requiredfield'] = 'This field is required!';
$string['requirelowestterms'] = 'Require lowest terms';
$string['requirelowestterms_help'] = 'When this option is set to yes, any coefficients or other rational numbers in an expression, must be written in lowest terms.  Otherwise the answer is rejected as invalid.';
$string['requirelowestterms_link'] = '/question/type/stack/doc/doc.php/Authoring/Inputs.md#Require_lowest_terms';
$string['sans'] = 'SAns';
$string['sans_help'] = 'This is the first argument to the answer test function.  In asymetrical tests this is considered to be the "student\'s answer" although it may be any valid CAS expression, and may depend on the question variables or the feedback variables.';
$string['sans_link'] = 'question/type/stack/doc/doc.php/Authoring/Answer_tests.md';
$string['stop'] = '[stop]';
$string['score'] = 'Score';
$string['scoreerror'] = 'The score must be a numeric value between 0 and 1.';
$string['scoremode'] = 'Mod';
$string['specificfeedback'] = 'Specific feedback';
$string['specificfeedback_help'] = 'By default, feedback for each potential response tree will be shown in this block.  It can be moved to the question text, in which case Moodle will have less control over when it is displayed by various behaviours.  Note, this block is not CASText.';
$string['specificfeedbacktags'] = 'Specific feedback must not contain the token(s) \'{$a}\'.';
$string['sqrtsign'] = 'Surd for square root';
$string['sqrtsign_help'] = 'Controls how surds are displayed.';
$string['sqrtsign_link'] = 'question/type/stack/doc/doc.php/Authoring/Options.md#surd';
$string['strictsyntax'] = 'Strict syntax';
$string['strictsyntax_help'] = 'Does the input have to be done using strict Maxima syntax?  If not, this increases the range of patterns which indicate missing *s on input, including any function application and scientific notation.';
$string['strictsyntax_link'] = '/question/type/stack/doc/doc.php/Authoring/Inputs.md#Strict_Syntax';
$string['syntaxhint'] = 'Syntax hint';
$string['syntaxhint_help'] = 'The syntax hint will appear in the answer box whenever this is left blank by the student.';
$string['syntaxhint_link'] = '/question/type/stack/doc/doc.php/Authoring/Inputs.md#Syntax_Hint';
$string['tans'] = 'TAns';
$string['tans_help'] = 'This is the second argument to the answer test function.  In asymetrical tests this is considered to be the "teacher\'s answer" although it may be any valid CAS expression, and may depend on the question variables or the feedback variables.';
$string['tans_link'] = 'question/type/stack/doc/doc.php/Authoring/Answer_tests.md';
$string['teachersanswer'] = 'Model answer';
$string['teachersanswer_help'] = 'The teacher must specify a model answer for each input.  This must be a valid Maxima string, and may be formed from the question variables.';
$string['teachersanswer_link'] = 'question/type/stack/doc/doc.php/Authoring/Inputs.md#model_answer';
$string['testoptions'] = 'Test options';
$string['testoptions_help'] = 'This field enables answer tests to accept an option, e.g. a variable or a numerical precision.';
$string['testoptions_link'] = 'question/type/stack/doc/doc.php/Authoring/Answer_tests.md';
$string['testoptionsrequired'] = 'Test options are required for this test.';
$string['variantsselectionseed'] = 'Random group';
$string['variantsselectionseed_help'] = 'Normally you can leave this box blank. If, however, you want two different questions in a quiz to use the same random seed, then type the same string in this box for the two questions (and deploy the same set of random seeds, if you are using deployed versions) and the random seeds for the two questions will be synchronised.';
$string['verifyquestionandupdate'] = 'Verify the question text and update the form';

// Error reporting in the feedback form.
$string['edit_form_error'] = 'Node {$a->no} {$a->field} has the following error: ';

// Admin settings.
$string['settingcasdebugging'] = 'CAS debugging';
$string['settingcasdebugging_desc'] = 'Whether to store debugging information about the CAS connection.';
$string['settingcasmaximaversion'] = 'Maxima version';
$string['settingcasmaximaversion_desc'] = 'The version of Maxima being used.';
$string['settingcasresultscache'] = 'CAS result caching';
$string['settingcasresultscache_db'] = 'Cache in the database';
$string['settingcasresultscache_desc'] = 'This setting determines whether calls the to CAS are cached. This setting should be turned on unless you are doing development that involves changing the Maxima code. The current state of the cache is shown on the healthcheck page.  If you change your settings, e.g. the gnuplot command, you will need to clear the cache before you can see the effects of these changes.';
$string['settingcasresultscache_none'] = 'Do not cache';
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

// Strings used by interaction elements.
$string['false'] = 'False';
$string['notanswered'] = 'Not answered';
$string['true'] = 'True';
$string['ddl_empty'] = 'No choices were provided for this drop-down. Please input a set of values link a,b,c,d';

// Strings used by the question test script.
$string['addanothertestcase'] = 'Add another test case...';
$string['addatestcase'] = 'Add a test case...';
$string['addingatestcase'] = 'Adding a test case to question {$a}';
$string['createtestcase'] = 'Create test case';
$string['currentlyselectedvariant'] = 'This is the variant shown below';
$string['deletetestcase'] = 'Delete test case {$a->no} for question {$a->question}';
$string['deletetestcaseareyousure'] = 'Are you sure you want to delete test case {$a->no} for question {$a->question}?';
$string['deletethistestcase'] = 'Delete this test case...';
$string['deploy'] = 'Deploy';
$string['deployedvariantoptions'] = 'The following variants have been deployed:';
$string['deployedvariants'] = 'Deployed variants';
$string['editingtestcase'] = 'Editing test case {$a->no} for question {$a->question}';
$string['editthistestcase'] = 'Edit this test case...';
$string['expectedanswernote'] = 'Expected answer note';
$string['expectedoutcomes'] = 'Expected outcomes';
$string['expectedpenalty'] = 'Expected penalty';
$string['expectedscore'] = 'Expected score';
$string['inputdisplayed'] = 'Displayed as';
$string['inputentered'] = 'Value entered';
$string['inputexpression'] = 'Test input';
$string['inputname'] = 'Input name';
$string['inputstatus'] = 'Status';
$string['inputstatusname'] = 'Blank';
$string['inputstatusnameinvalid'] = 'Invalid';
$string['inputstatusnamevalid'] = 'Valid';
$string['inputstatusnamescore'] = 'Score';
$string['notestcasesyet'] = 'No test cases have been added yet.';
$string['penalty'] = 'Penalty';
$string['prtname'] = 'PRT name';
$string['questiondoesnotuserandomisation'] = 'This question does not use randomisation.';
$string['questionnotdeployedyet'] = 'No variants of this question have been deployed yet.';
$string['questionpreview'] = 'Question preview';
$string['questiontests'] = 'Question tests';
$string['runquestiontests'] = 'Run the question tests...';
$string['showingundeployedvariant'] = 'Showing undeployed variant: {$a}';
$string['switchtovariant'] = 'Switch to arbitrary variant';
$string['testcasexresult'] = 'Test case {$a->no} {$a->result}';
$string['testingquestion'] = 'Testing question {$a}';
$string['testinputs'] = 'Test inputs';
$string['testthisvariant'] = 'Switch to test this variant';
$string['undeploy'] = 'Un-deploy';

// Support scripts (CAS chat, healthcheck, etc.)
$string['all'] = 'All';
$string['chat'] = 'Send to the CAS';
$string['chat_desc'] = 'The <a href="{$a->link}">CAS chat script</a> lets you test the connection to the CAS, and try out Maxima syntax.';
$string['chatintro'] = 'This page enables CAS text to be evaluated directly. It is a simple script which is a useful minimal example, and a handy way to check if the CAS is working, and to test various inputs.';
$string['chattitle'] = 'Test the connection to the CAS';
$string['clearthecache'] = 'Clear the cache';
$string['healthcheck'] = 'STACK healthcheck';
$string['healthcheck_desc'] = 'The <a href="{$a->link}">healthcheck script</a> helps you verify that all aspects of Stack are working properly.';
$string['healthcheckcache_db'] = 'CAS results are being cached in the database.';
$string['healthcheckcache_none'] = 'CAS results are not being cached.';
$string['healthcheckcachestatus'] = 'The cache currently contains {$a} entries.';
$string['healthcheckconfig'] = 'Maxima configuration file';
$string['healthcheckconfigintro1'] = 'Found, and using, Maxima in the following directory:';
$string['healthcheckconfigintro2'] = 'Trying to automatically write the Maxima configuration file.';
$string['healthcheckconnect'] = 'Trying to connect to the CAS';
$string['healthcheckconnectintro'] = 'We are trying to evaluate the following CAS text:';
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
$string['answertest_help'] = 'An answer test is used to compare two expressions to establish whether they satisfy some mathematical criteria.';
$string['answertest_link'] = 'question/type/stack/doc/doc.php/Authoring/Answer_tests.md';
$string['testsuitecolpassed'] = 'Passed?';
$string['studentanswer'] = 'Student response';
$string['teacheranswer'] = 'Teacher answer';
$string['options'] = 'Options';
$string['testsuitecolerror'] = 'CAS errors';
$string['testsuitecolrawmark'] = 'Raw mark';
$string['testsuitecolexpectedscore'] = 'Expected mark';
$string['testsuitepass'] = 'Pass';
$string['testsuitefail'] = 'Fail';
$string['stackInstall_input_title'] = "A test suite for validation of student's input";
$string['stackInstall_input_title_desc'] = 'The <a href="{$a->link}">input-tests script</a> provides test cases of how STACK interprests mathematical expressions.  They are also useful to learn by example.';
$string['stackInstall_input_intro'] = "This page allows you to test how STACK interprets various inputs from a student.  This currently only checks with the most liberal settings, trying to adopt an informal syntax and insert stars.  <br />'V' columns record validity as judged by PHP and the CAS.  V1 = PHP valid, V2 = CAS valid.";
$string['phpvalid'] = 'V1';
$string['phpcasstring'] = 'PHP output';
$string['phpsuitecolerror'] = 'PHP errors';
$string['phpvalidatemismatch'] = '[PHP validate mismatch]';
$string['casvalidatemismatch'] = '[CAS validate mismatch]';
$string['casvalid'] = 'V2';
$string['casvalue'] = 'CAS value';
$string['casdisplay'] = 'CAS display';
$string['cassuitecolerrors'] = 'CAS errors';

$string['texdisplayedbracket'] = 'Displayed bracket';
$string['texinlinebracket'] = 'Inline bracket';
$string['texdoubledollar'] = 'Double dollar';
$string['texsingledollar'] = 'Single dollar';

// Used in casstring.class.php.
$string['stackCas_spaces']                  = 'Spaces found in expression {$a->expr}.';
$string['stackCas_percent']                 = '&#037; found in expression {$a->expr}.';
$string['stackCas_missingLeftBracket']      = 'You have a missing left bracket <span class="stacksyntaxexample">{$a->bracket}</span> in the expression: {$a->cmd}.';
$string['stackCas_missingRightBracket']     = 'You have a missing right bracket <span class="stacksyntaxexample">{$a->bracket}</span> in the expression: {$a->cmd}.';
$string['stackCas_apostrophe']              = 'Apostrophes are not permitted in responses.';
$string['stackCas_newline']                 = 'Newline characters are not permitted in responses.';
$string['stackCas_forbiddenChar']           = 'CAS commands may not contain the following characters: {$a->char}.';
$string['stackCas_finalChar']               = '\'{$a->char}\' is an invalid final character in {$a->cmd}';
$string['stackCas_MissingStars']            = 'You seem to be missing *\'s. Perhaps you meant to type {$a->cmd}.';
$string['stackCas_unknownFunction']         = 'Unknown function: {$a->forbid}.';
$string['stackCas_unsupportedKeyword']      = 'Unsupported keyword: {$a->forbid}.';
$string['stackCas_forbiddenWord']           = 'The expression {$a->forbid} is forbidden.';

// Used in cassession.class.php.
$string['stackCas_CASError']                = 'The CAS returned the following error(s):';
$string['stackCas_allFailed']               = 'CAS failed to return any evaluated expressions.  Please check your connection with the CAS.';
$string['stackCas_failedReturn']            = 'CAS failed to return any data.';

// Used in castext.class.php.
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
$string['Lowest_Terms']   = 'Your answer contains fractions that are not written in lowest terms.  Please cancel factors and try again.';
$string['Illegal_floats'] = 'Your answer contains floating point numbers, that are not allowed in this question.  You need to type in numbers as fractions.  For example, you should type 1/3 not 0.3333, which is after all only an approximation to one third.';
$string['qm_error'] = 'Your answer contains question mark characters, ?, which are not permitted in answers.  You should replace these with a specfic value.';
// TODO add this to STACK...
// $string['CommaError']     = 'Your answer contains commas which are not part of a list, set or matrix.  <ul><li>If you meant to type in a list, please use <tt>{$a[0]}</tt>,</li><li>If you meant to type in a set, please use <tt>{$a[1]}</tt>.</li></ul>';

// Answer tests.
$string['stackOptions_AnsTest_values_AlgEquiv']           =  "AlgEquiv";
$string['stackOptions_AnsTest_values_EqualComAss']        =  "EqualComAss";
$string['stackOptions_AnsTest_values_CasEqual']           =  "CasEqual";
$string['stackOptions_AnsTest_values_SameType']           =  "SameType";
$string['stackOptions_AnsTest_values_SubstEquiv']         =  "SubstEquiv";
$string['stackOptions_AnsTest_values_SysEquiv']           =  "SysEquiv";
$string['stackOptions_AnsTest_values_Expanded']           =  "Expanded";
$string['stackOptions_AnsTest_values_FacForm']            =  "FacForm";
$string['stackOptions_AnsTest_values_SingleFrac']         =  "SingleFrac";
$string['stackOptions_AnsTest_values_PartFrac']           =  "PartFrac";
$string['stackOptions_AnsTest_values_CompSquare']         =  "CompletedSquare";
$string['stackOptions_AnsTest_values_NumRelative']        =  "NumRelative";
$string['stackOptions_AnsTest_values_NumAbsolute']        =  "NumAbsolute";
$string['stackOptions_AnsTest_values_NumSigFigs']         =  "NumSigFigs";
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
$string['ATAlgEquiv_TA_not_equation']   = 'Your answer is an equation, but the expression to which it is being compared is not.  You may have typed something like "y=2*x+1" when you only needed to type "2*x+1". ';
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
$string['irred_Q_commonint']        = 'You need to take out a common factor. ';  // Needs a space at the end.
$string['irred_Q_optional_fac']     = 'You could do more work, since {$a->m0} can be further factored.  However, you don\'t need to. ';

$string['FacForm_UnPick_morework']  = 'You could still do some more work on the term {$a->m0}. ';
$string['FacForm_UnPick_intfac']    = $string['irred_Q_commonint'];

$string['ATFacForm_error_list']     = 'The answer test failed.  Please contact your systems administrator';
$string['ATFacForm_error_degreeSA'] = 'The CAS could not establish the algebraic degree of your answer.';
$string['ATFacForm_isfactored']     = 'Your answer is factored, well done. ';  // Needs a space at the end.
$string['ATFacForm_notfactored']    = 'Your answer is not factored. '; // Needs a space at the end.
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
$string['ATSysEquiv_SA_system_underdetermined'] = 'The equations in your system appear to be correct, but you need others besides.';
$string['ATSysEquiv_SA_system_overdetermined']  = 'The entries in red below are those that are incorrect. {$a->m0} ';

$string['studentValidation_yourLastAnswer']  = 'Your last answer was interpreted as follows:';
$string['studentValidation_invalidAnswer']   = 'This answer is invalid. ';
$string['stackQuestion_noQuestionParts']        = 'This item has no question parts for you to answer.';

// Documentation strings.
$string['stackDoc_404']                 = 'Error 404';
$string['stackDoc_docs']                = 'STACK Documentation';
$string['stackDoc_docs_desc']           = '<a href="{$a->link}">Documentation for STACK</a>: a local static wiki.';
$string['stackDoc_home']                = 'Documentation home';
$string['stackDoc_index']               = 'Category index';
$string['stackDoc_parent']              = 'Parent';
$string['stackDoc_siteMap']             = 'Site map';
$string['stackDoc_404message']          = 'File not found.';
$string['stackDoc_directoryStructure']  = 'Directory structure';

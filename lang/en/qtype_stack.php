<?php
// This file is part of Stack - http://stack.maths.ed.ac.uk//
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
 * Language strings for the STACK question type.
 *
 * @package    qtype_stack
 * @copyright  2012 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname']        = 'STACK';
$string['pluginname_help']   = 'STACK is an assessment system for mathematics.';
$string['pluginnameadding']  = 'Adding a STACK question';
$string['pluginnameediting'] = 'Editing a STACK question';
$string['pluginnamesummary'] = 'STACK provides mathematical questions for the Moodle quiz.  These use a computer algebra system to establish the mathematical properties of the student\'s responses.';

$string['privacy:metadata']  = 'The STACK question type plugin does not store any personal data.';
$string['cachedef_parsercache'] = 'STACK parsed Maxima expressions';

$string['mbstringrequired'] = 'Installing the MBSTRING library is required for STACK.';
$string['yamlrecommended']  = 'Installing the YAML library is recommended for STACK.';

// General strings.
$string['errors']            = 'Errors';
$string['debuginfo']         = 'Debug info';
$string['exceptionmessage']  = '{$a}';
$string['runtimeerror']      = 'This question generated an unexpected internal error.  Please seek advice, e.g. from a teacher.';
$string['runtimefielderr']   = 'The field ""{$a->field}"" generated the following error: {$a->err}';

// Capability names.
$string['stack:usediagnostictools'] = 'Use the STACK tools';

// Versions of STACK.
$string['stackversionedited']   = 'This question was authored with STACK version {$a}.';
$string['stackversionnow']      = 'The current version of STACK is {$a}.';
$string['stackversionnone']     = 'This question has not been edited since question variant numbering was introduced in STACK 4.2.  Please review your question carefully.';
$string['stackversionerror']    = 'This question uses {$a->pat} in the {$a->qfield}, which changed in STACK version {$a->ver} and is no longer supported.';
$string['stackversionerroralt'] = 'An alternative is {$a}.';
$string['stackversionmulerror'] = 'This question has an input which uses the "mul" option, which is not suppored after STACK version 4.2.  Please edit this question.';
$string['stackversionregexp']   = 'The RegExp answer test is not supported after STACK version 4.3.  Please use the new SRegExp instead.';

// Strings used on the editing form.
$string['addanothernode'] = 'Add another node';
$string['allnodefeedbackmustusethesameformat'] = 'All the feedback for all the nodes in a PRT must use the same text format.';
$string['answernote'] = 'Answer note';
$string['answernote_err'] = 'Answer notes may not contain the character |.  This character is inserted by STACK and is later used to split answer notes automatically.';
$string['answernote_err2'] = 'Answer notes may not contain ; or : characters.  These characters are used to split question attempt summary strings in offline reporting tools.';
$string['answernote_help'] = 'This is a tag which is key for reporting purposes.  It is designed to record the unique path through the tree, and the outcome of each answer test.  This is automatically generated, but can be changed to something meaningful.';
$string['answernote_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Potential_response_trees.md#Answer_note';
$string['answernotedefaultfalse'] = '{$a->prtname}-{$a->nodename}-F';
$string['answernotedefaulttrue'] = '{$a->prtname}-{$a->nodename}-T';
$string['answernoterequired'] = 'Answer note must not be empty.';
$string['answernoteunique'] = 'Duplicate answer notes detected in this potential response tree.';
$string['assumepositive'] = 'Assume positive';
$string['assumepositive_help'] = 'This option sets the value of Maxima\'s assume_pos variable.';
$string['assumepositive_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Options.md#Assume_Positive';
$string['assumereal'] = 'Assume real';
$string['assumereal_help'] = 'This option sets the assume_real variable.';
$string['assumereal_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Options.md#Assume_Real';
$string['autosimplify'] = 'Auto-simplify';
$string['autosimplify_help'] = 'Sets the variable "simp" within Maxima for this question.  E.g. question variables, question text etc.  The value set in each potential response tree will over ride this for any expressions subsequently defined within the tree.';
$string['autosimplify_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/CAS/Maxima.md#Simplification';
$string['autosimplifyprt'] = 'Auto-simplify';
$string['autosimplifyprt_help'] = 'Sets the variable "simp" within Maxima for the feedback variables defined in this potential response tree. Note that whether expressions in PRT notes are simplified before use depends on the answer test. For example, arguments to AlgEquiv are simplified, while those for EqualComAss are not.';
$string['autosimplifyprt_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/CAS/Maxima.md#Simplification';
$string['boxsize'] = 'Input box size';
$string['boxsize_help'] = 'Width of the html formfield.';
$string['boxsize_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Inputs.md#Box_Size';
$string['bulktestindexintro_desc'] = 'The <a href="{$a->link}">run the question tests in bulk script</a> lets you easily run all the STACK questions in a given context. Not only does this test the questions. It is also a good way to re-populate the CAS cache after it has been cleared.';
$string['checkanswertype'] = 'Check the type of the response';
$string['checkanswertype_help'] = 'If yes, answers which are of a different "type" (e.g. expression, equation, matrix, list, set) are rejected as invalid.';
$string['checkanswertype_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Inputs.md#Check_Type';
$string['complexno'] = 'Meaning and display of sqrt(-1)';
$string['complexno_help'] = 'Controls the meaning and display of the symbol i and sqrt(-1)';
$string['complexno_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Options.md#sqrt_minus_one.';
$string['defaultmarkzeroifnoprts'] = 'The default mark must be 0 if this question has no PRTs.';
$string['defaultprtcorrectfeedback'] = 'Correct answer, well done.';
$string['defaultprtincorrectfeedback'] = 'Incorrect answer.';
$string['defaultprtpartiallycorrectfeedback'] = 'Your answer is partially correct.';
$string['symbolicprtcorrectfeedback'] = '<span style="font-size: 1.5em; color:green;"><i class="fa fa-check"></i></span>';
$string['symbolicprtincorrectfeedback'] = '<span style="font-size: 1.5em; color:red;"><i class="fa fa-times"></i></span>';
$string['symbolicprtpartiallycorrectfeedback'] = '<span style="font-size: 1.5em; color:orange;"><i class="fa fa-adjust"></i></span>';
$string['branchfeedback'] = 'Node branch feedback';
$string['branchfeedback_help'] = 'This is CASText which may depend on any of the question variables, input elements or the feedback variables. This is evaluated and displayed to the student if they pass down this branch.';
$string['inputtest'] = 'Input test';
$string['inversetrig'] = 'Inverse trigonometric functions';
$string['inversetrig_help'] = 'Controls how inverse trigonometric functions are displayed in CAS output.';
$string['inversetrig_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Options.md#inverse_trig';
$string['logicsymbol'] = 'Logic symbols';
$string['logicsymbol_help'] = 'Controls how logical symbols should be displayed in CAS output.';
$string['logicsymbol_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Options.md#logicsymbol';
$string['logicsymbollang'] = 'Language';
$string['logicsymbolsymbol'] = 'Symbolic';
$string['matrixparens'] = 'Default shape of matrix parentheses';
$string['matrixparens_help'] = 'Controls the default shape of matrix parentheses when displayed in CAS output.';
$string['matrixparens_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/CAS/Matrix.md#matrixparens';
$string['falsebranch'] = 'False branch';
$string['falsebranch_help'] = 'These fields control what happens when the answer test does not pass
### Mod and score
How the score is adjusted. = means set the score to a particular values, +/- means add or subtract the given score from the current total.

### Penalty
In adaptive or interactive mode, accumulate that much penalty.

### Next
Whether to go to another node, and if so which, or stop.

### Answer note
This is a tag which is key for reporting purposes.  It is designed to record the unique path through the tree, and the outcome of each answer test.  This is automatically generated, but can be changed to something meaningful.
';
$string['feedbackfromprtx'] = '[ Feedback from {$a}. ]';
$string['feedbackvariables'] = 'Feedback variables';
$string['feedbackvariables_help'] = 'The feedback variables enable you to manipulate any of the inputs, together with the question variables, prior to traversing the tree.  Variables defined here may be used anywhere else in this tree.';
$string['feedbackvariables_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Variables.md#Feedback_variables';
$string['fieldshouldnotcontainplaceholder'] = '{$a->field} should not contain any [[{$a->type}:...]] placeholders.';
$string['firstnodemusthavelowestnumber'] = 'The first node must have the lowest number.';
$string['fixdollars'] = 'Fix dollars';
$string['fixdollarslabel'] = 'Replace <code>$...$</code> with <code>\(...\)</code>, <code>$$...$$</code> with <code>\[...\]</code> and <code>@...@</code> with <code>{@...@}</code> on save.';
$string['fixdollars_help'] = 'This option is useful if are copying and pasting (or typing) TeX with <code>$...$</code> and <code>$$...$$</code> delimiters. Those delimiters will be replaced by the recommended delimiters during the save process.';
$string['forbiddendoubledollars'] = 'Please use the delimiters <code>\(...\)</code> for inline maths and <code>\[...\]</code> for display maths. <code>$...$</code> and <code>$$...$$</code> are not permitted. There is an option at the end of the form to fix this automatically.';
$string['forbidfloat'] = 'Forbid float';
$string['forbidfloat_help'] = 'If set to yes, then any answer of the student which has a floating point number will be rejected as invalid.';
$string['forbidfloat_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Inputs.md#Forbid_Floats';
$string['forbidwords'] = 'Forbidden words ';
$string['forbidwords_help'] = 'This is a comma separated list of text strings which are forbidden in a student\'s answer.';
$string['forbidwords_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Inputs.md#Forbidden_Words';
$string['allowwords'] = 'Allowed words ';
$string['allowwords_help'] = 'By default, arbitrary function or variable names of more than two characters in length are not permitted.  This is a comma separated list of function or variable names which are permitted in a student\'s answer.';
$string['allowwords_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Inputs.md#Allow_Words';
$string['generalfeedback'] = 'General feedback';
$string['generalfeedback_help'] = 'General feedback is CASText. General feedback, also known as a "worked solution", is shown to the student after they have attempted the question. Unlike feedback, which depends on what response the student gave, the same general feedback text is shown to all students.  It may depend on the question variables used in the variant of the question.';
$string['generalfeedback_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/CASText.md#general_feedback';
$string['showvalidation'] = 'Show the validation';
$string['showvalidation_help'] = 'Displays any validation feedback from this input, including echoing back their expression in traditional two dimensional notation.   Syntax errors are always reported back.';
$string['showvalidation_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Inputs.md#Show_validation';
$string['showvalidationno'] = 'No';
$string['showvalidationyes'] = 'Yes, with variable list';
$string['showvalidationyesnovars'] = 'Yes, without variable list';
$string['showvalidationcompact'] = 'Yes, compact';
$string['inputinvalidparamater'] = 'Invalid parameter';
$string['mustverifyshowvalidation'] = 'You cannot choose to require two step validation but not show the results of validation to the student after the first step.  This puts the student in an impossible position.';
$string['htmlfragment'] = 'You appear to have some HTML elements in your expression.';
$string['illegalcaschars'] = 'The characters @, $ and \\ are not allowed in CAS input.';
$string['inputextraoptions'] = 'Extra options';
$string['inputextraoptions_help'] = 'Some input types require extra options in order to work. You can enter them here. This value is a CAS expression.';
$string['inputextraoptions_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Inputs.md#Extra options';
$string['inputoptionunknown'] = 'This input does not support the option \'{$a}\'.';
$string['inputheading'] = 'Input: {$a}';
$string['inputnamelength'] = 'Input names cannot be longer than 18 characters. \'{$a}\' is too long.';
$string['inputnameform'] = 'Input names must only be letters followed (optionally) by numbers. \'{$a}\' is illegal.';
$string['inputremovedconfirmbelow'] = 'Input \'{$a}\' has been removed. Please confirm this below.';
$string['inputremovedconfirm'] = 'I confirm that I want to remove this input from this question.';
$string['inputlanguageproblems'] = 'There are inconsistencies in the input and validation tags between languages.';
$string['inputs'] = 'Inputs';
$string['inputtype'] = 'Input type';
$string['inputtype_help'] = 'This determines the type of the input element, e.g. form field, true/false, text area.';
$string['inputtype_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Inputs.md';
$string['inputtypealgebraic'] = 'Algebraic input';
$string['inputtypeboolean'] = 'True/False';
$string['inputtypedropdown'] = 'Drop down list';
$string['inputtypecheckbox'] = 'Checkbox';
$string['inputtyperadio'] = 'Radio';
$string['inputtypesinglechar'] = 'Single character';
$string['inputtypetextarea'] = 'Text area';
$string['inputtypematrix'] = 'Matrix';
$string['inputtypevarmatrix'] = 'Matrix of variable size';
$string['inputtypenotes'] = 'Notes';
$string['inputtypeunits'] = 'Units';
$string['inputtypeequiv'] = 'Equivalence reasoning';
$string['inputtypestring'] = 'String';
$string['inputtypenumerical'] = 'Numerical';
$string['numericalinputmustnumber'] = 'This input expects a number.';
$string['numericalinputvarsforbidden'] = 'This input expects a number, and so may not contain variables.';
$string['numericalinputmustfloat'] = 'This input expects a floating point number.';
$string['numericalinputmustint'] = 'This input expects an explicit integer.';
$string['numericalinputmustrational'] = 'This input expects a fraction or rational number.';
$string['numericalinputdp'] = 'You must supply exactly \( {$a} \) decimal places.';
$string['numericalinputsf'] = 'You must supply exactly \( {$a} \) significant figures.';
$string['numericalinputmindp'] = 'You must supply at least \( {$a} \) decimal places.';
$string['numericalinputmaxdp'] = 'You must supply at most \( {$a} \) decimal places.';
$string['numericalinputminsf'] = 'You must supply at least \( {$a} \) significant figures.';
$string['numericalinputmaxsf'] = 'You must supply at most \( {$a} \) significant figures.';
$string['numericalinputminmaxerr'] = 'The required minimum number of numerical places exceeds the maximum number of places!';
$string['numericalinputminsfmaxdperr'] = 'Do not specify requirements for both decimal places and significant figures in the same input.';
$string['numericalinputoptinterr'] = 'The value of the option <code>{$a->opt}</code> should be an integer, but in fact it is <code>{$a->val}</code>.';
$string['numericalinputoptboolerr'] = 'The value of the option <code>{$a->opt}</code> should be boolean, but in fact it is <code>{$a->val}</code>.';
$string['inputopterr'] = 'The value of the option <code>{$a->opt}</code> cannot be given as <code>{$a->val}</code>.';
$string['inputwillberemoved'] = 'This input is no longer referred to in the question text. If you save the question now, the data about this input will be lost. Please confirm that you want to do this. Alternatively edit the question text to put back the \'[[input:{$a}]]\' and \'[[validation:{$a}]]\' placeholders.';
$string['insertstars'] = 'Insert stars';
$string['insertstars_help'] = 'This option provides a number of different options for inserting stars where multiplication is implied.  Please read the more detailed documentation.';
$string['insertstars_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Inputs.md#Insert_Stars';
$string['insertstarsno'] = 'Don\'t insert stars ';
$string['insertstarsyes'] = 'Insert stars for implied multiplication only';
$string['insertstarsassumesinglechar'] = 'Insert stars assuming single-character variable names';
$string['insertspaces'] = 'Insert stars for spaces only';
$string['insertstarsspaces'] = 'Insert stars for implied multiplication and for spaces';
$string['insertstarsspacessinglechar'] = 'Insert stars assuming single-character variables, implied and for spaces';
$string['multiplicationsign'] = 'Multiplication sign';
$string['multiplicationsign_help'] = 'Controls how multiplication signs are displayed.';
$string['multiplicationsign_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Options.md#multiplication';
$string['multcross'] = 'Cross';
$string['multdot'] = 'Dot';
$string['mustverify'] = 'Student must verify';
$string['mustverify_help'] = 'Specifies whether the student\'s input is presented back to them as a forced two step process before this input is made available to the scoring mechanism.  Syntax errors are always reported back.';
$string['mustverify_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Inputs.md#Student_must_verify';
$string['namealreadyused'] = 'You have already used this name.';
$string['newnameforx'] = 'New name for \'{$a}\'';
$string['next'] = 'Next';
$string['nextcannotbeself'] = 'A node cannot link to itself as the next node.';
$string['nodehelp'] = 'Response tree node';
$string['nodehelp_help'] = '### Answer test
An answer test is used to compare two expressions to establish whether they satisfy some mathematical criteria.

### SAns
This is the first argument to the answer test function.  In asymmetrical tests this is considered to be the "student\'s answer" although it may be any valid CAS expression, and may depend on the question variables or the feedback variables.

### TAns
This is the second argument to the answer test function.  In asymmetrical tests this is considered to be the "teacher\'s answer" although it may be any valid CAS expression, and may depend on the question variables or the feedback variables.

### Test options
This field enables answer tests to accept an option, e.g. a variable or a numerical precision.

### Quiet
When set to yes any feedback automatically generated by the answer tests is suppressed, and not displayed to the student.  The feedback fields in the branches are unaffected by this option.

';
$string['nodeloopdetected'] = 'This link creates a cycle in this PRT.';
$string['nodenotused'] = 'No other nodes in the PRT link to this node.';
$string['nodex'] = 'Node {$a}';
$string['nodexdelete'] = 'Delete node {$a}';
$string['nodexfalsefeedback'] = 'Node {$a} false feedback';
$string['nodextruefeedback'] = 'Node {$a} true feedback';
$string['nodexwhenfalse'] = 'Node {$a} when false';
$string['nodexwhentrue'] = 'Node {$a} when true';
$string['nonempty'] = 'This must not be empty.';
$string['noprtsifnoinputs'] = 'A question with no inputs cannot have any PRTs.';
$string['notavalidname'] = 'Not a valid name';
$string['optionsnotrequired'] = 'This input type does not require any options.';
$string['penalty'] = 'Penalty';
$string['penalty_help'] = 'The penalty scheme deducts this value from the result of each PRT for each different and valid attempt which is not completely correct.';
$string['penalty_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Feedback.md';
$string['penaltyerror'] = 'The penalty must be a numeric value between 0 and 1.';
$string['penaltyerror2'] = 'The penalty must empty, or be a numeric value between 0 and 1.';
$string['prtcorrectfeedback'] = 'Standard feedback for correct';
$string['prtheading'] = 'Potential response tree: {$a}';
$string['prtmustbesetup'] = 'This PRT must be set up before the question can be saved.';
$string['prtnamelength'] = 'PRT names cannot be longer than 18 characters. \'{$a}\' is too long.';
$string['prtnodesheading'] = 'Potential response tree nodes ({$a})';
$string['prtincorrectfeedback'] = 'Standard feedback for incorrect';
$string['prtpartiallycorrectfeedback'] = 'Standard feedback for partially correct';
$string['prtremovedconfirmbelow'] = 'Potential response tree \'{$a}\' has been removed. Please confirm this below.';
$string['prtremovedconfirm'] = 'I confirm that I want to remove this potential response tree from this question.';
$string['prts'] = 'Potential response trees';
$string['prtwillbecomeactivewhen'] = 'This potential response tree will become active when the student has answered: {$a}';
$string['prtruntimeerror'] = 'Node {$a->node} generated the following runtime error: {$a->error}';
$string['prtwillberemoved'] = 'This potential response tree is no longer referred to in the question text or specific feedback. If you save the question now, the data about this potential response tree will be lost. Please confirm that you want to do this. Alternatively edit the question text or specific feedback to put back the \'[[feedback:{$a}]]\' placeholder.';
$string['feedbackstyle'] = 'PRT feedback style';
$string['feedbackstyle_help'] = 'Controls how PRT feedback is displayed.';
$string['feedbackstyle_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Potential_response_trees.md';
$string['feedbackstyle0'] = 'Formative';
$string['feedbackstyle1'] = 'Standard';
$string['feedbackstyle2'] = 'Compact';
$string['feedbackstyle3'] = 'Symbol only';
$string['questionnote'] = 'Question note';
$string['questionnote_help'] = 'The question note is CASText.  The purpose of a question note is to distinguish between random variants of a question. Two question variants are equal if and only if the question notes are equal.  In later analysis it is very helpful to leave a meaningful question note.';
$string['questionnote_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Question_note.md';
$string['questionnotempty'] = 'The question note cannot be empty when rand() appears in the question variables.  The question note is used to distinguish between different random variants of the question.';
$string['questionsimplify'] = 'Question-level simplify';
$string['questionsimplify_help'] = 'Sets the global variable "simp" within Maxima for the whole question.';
$string['questionsimplify_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/CAS/Maxima.md#Simplification';
$string['questiontext'] = 'Question text';
// @codingStandardsIgnoreStart
$string['questiontext_help'] = 'The question text is CASText.  This is the "question" which the student actually sees.  You must put input elements, and the validation strings, in this field, and only in this field.  For example, using `[[input:ans1]] [[validation:ans1]]`.';
// @codingStandardsIgnoreEnd
$string['questiontext_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/CASText.md#question_text';
$string['questiontextnonempty'] = 'The question text must be non-empty.';
$string['questiontextmustcontain'] = 'The question text must contain the token \'{$a}\'.';
$string['questiontextonlycontain'] = 'The question text should only contain the token \'{$a}\' once.';
$string['questiontextplaceholderswhitespace'] = 'Placeholders may not contain whitespace.  This one appears to do so: \'{$a}\'.';
$string['questiontextfeedbackonlycontain'] = 'The question text combined with the specific feedback should only contain the token \'{$a}\' once.';
$string['questiontextfeedbacklanguageproblems'] = 'There are inconsistencies in the feedback tags between languages.';
$string['questionvalue'] = 'Question value';
$string['questionvaluepostive'] = 'Question value must be non-negative.';
$string['questionvariables'] = 'Question variables';
$string['questionvariables_help'] = 'This field allows you to define and manipulate CAS variables, e.g. to create random variants.  These are available to all other parts of the question.';
$string['questionvariables_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Variables.md#Question_variables';
$string['questionvariablevalues'] = 'Question variable values';
$string['quiet'] = 'Quiet';
$string['quiet_help'] = 'When set to yes any feedback automatically generated by the answer tests is suppressed, and not displayed to the student.  The feedback fields in the branches are unaffected by this option.';
$string['renamequestionparts'] = 'Rename parts of the question';
$string['requiredfield'] = 'This field is required!';
$string['requirelowestterms'] = 'Require lowest terms';
$string['requirelowestterms_help'] = 'When this option is set to yes, any coefficients or other rational numbers in an expression, must be written in lowest terms.  Otherwise the answer is rejected as invalid.';
$string['requirelowestterms_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Inputs.md#Require_lowest_terms';
$string['sans'] = 'SAns';
$string['sans_help'] = 'This is the first argument to the answer test function.  In asymmetrical tests this is considered to be the "student\'s answer" although it may be any valid CAS expression, and may depend on the question variables or the feedback variables.';
$string['sans_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Answer_tests.md';
$string['sansinvalid'] = 'SAns is invalid: {$a}';
$string['sansrequired'] = 'SAns must not be empty.';
$string['stop'] = '[stop]';
$string['score'] = 'Score';
$string['scoreerror'] = 'The score must be a numeric value between 0 and 1.';
$string['scoremode'] = 'Mod';
$string['specificfeedback'] = 'Specific feedback';
$string['specificfeedback_help'] = 'By default, feedback for each potential response tree will be shown in this block.  It can be moved to the question text, in which case Moodle will have less control over when it is displayed by various behaviours.  Note, this block is not CASText.';
$string['specificfeedbacktags'] = 'Specific feedback must not contain the token(s) \'{$a}\'.';
$string['sqrtsign'] = 'Surd for square root';
$string['sqrtsign_help'] = 'Controls how surds are displayed.';
$string['sqrtsign_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Options.md#surd';
$string['strictsyntax'] = 'Strict syntax';
$string['strictsyntax_help'] = 'This option is no longer used and will be removed.';
$string['strictsyntax_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/';
$string['strlengtherror'] = 'This string may not exceed 255 characters in length.';
$string['syntaxhint'] = 'Syntax hint';
$string['syntaxhint_help'] = 'The syntax hint will appear in the answer box whenever this is left blank by the student.';
$string['syntaxhint_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Inputs.md#Syntax_Hint';
$string['syntaxattribute'] = 'Hint attribute';
$string['syntaxattribute_help'] = 'The syntax hint will appear as an editable *value* or a non-editable *placeholder*.';
$string['syntaxattribute_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Inputs.md#Syntax_Hint';
$string['syntaxattributevalue'] = 'Value';
$string['syntaxattributeplaceholder'] = 'Placeholder';
$string['tans'] = 'TAns';
$string['tans_help'] = 'This is the second argument to the answer test function.  In asymmetrical tests this is considered to be the "teacher\'s answer" although it may be any valid CAS expression, and may depend on the question variables or the feedback variables.';
$string['tans_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Answer_tests.md';
$string['tansinvalid'] = 'TAns is invalid: {$a}';
$string['tansrequired'] = 'TAns must not be empty.';
$string['teachersanswer'] = 'Model answer';
$string['teachersanswer_help'] = 'The teacher must specify a model answer for each input.  This must be a valid Maxima string, and may be formed from the question variables.';
$string['teachersanswer_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Inputs.md#model_answer';
$string['testoptions'] = 'Test options';
$string['testoptions_help'] = 'This field enables answer tests to accept an option, e.g. a variable or a numerical precision.';
$string['testoptions_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Answer_tests.md';
$string['testoptionsinvalid'] = 'The test options are invalid: {$a}';
$string['testoptionsrequired'] = 'Test options are required for this test.';
$string['truebranch'] = 'True branch';
$string['truebranch_help'] = 'These fields control what happens when the answer test passes
### Mod and score
How the score is adjusted. = means set the score to a particular values, +/- means add or subtract the given score from the current total.

### Penalty
In adaptive or interactive mode, accumulate that much penalty.

### Next
Whether to go to another node, and if so which, or stop.

### Answer note
This is a tag which is key for reporting purposes.  It is designed to record the unique path through the tree, and the outcome of each answer test.  This is automatically generated, but can be changed to something meaningful.
';
$string['variantsselectionseed'] = 'Random group';
$string['variantsselectionseed_help'] = 'Normally you can leave this box blank. If, however, you want two different questions in a quiz to use the same random seed, then type the same string in this box for the two questions (and deploy the same set of random seeds, if you are using deployed variants) and the random seeds for the two questions will be synchronised.';
$string['verifyquestionandupdate'] = 'Verify the question text and update the form';
$string['youmustconfirm'] = 'You must confirm here.';

// Strings used by input elements.
$string['booleangotunrecognisedvalue'] = 'Invalid input.';
$string['dropdowngotunrecognisedvalue'] = 'Invalid input.';
$string['pleaseananswerallparts'] = 'Please answer all parts of the question.';
$string['pleasecheckyourinputs'] = 'Please verify that what you entered was interpreted as expected.';
$string['singlechargotmorethanone'] = 'You can only enter a single character here.';

$string['true'] = 'True';
$string['false'] = 'False';
$string['notanswered'] = '(No answer given)';
$string['ddl_runtime'] = 'The input has generated the following runtime error which prevents you from answering. Please contact your teacher.';
$string['ddl_empty'] = 'No choices were provided for this drop-down.';
$string['ddl_nocorrectanswersupplied'] = 'The teacher did not indicate at least one correct answer. ';
$string['ddl_duplicates'] = 'Duplicate values have been found when generating the input options. ';
$string['ddl_badanswer'] = 'The model answer field for this input is malformed: <code>{$a}</code>. ';
$string['ddl_unknown'] = 'STACK received <code>{$a}</code> but this is not listed by the teacher as an option. ';

$string['teacheranswershow']      = 'A correct answer is \( {$a->display} \), which can be typed in as follows: {$a->value}';
$string['teacheranswershow_disp'] = 'A correct answer is {$a->display}.';
$string['teacheranswershow_mcq']  = 'A correct answer is: {$a->display}';
$string['teacheranswershownotes'] = 'A correct answer is not provided for this input.';
$string['teacheranswerempty']     = 'This input can be left blank.';


// Admin settings.
$string['settingajaxvalidation'] = 'Instant validation';
$string['settingajaxvalidation_desc'] = 'With this setting turned on, the students current input will be validated whenever they pause in their typing. This gives a better user experience, but is likely to increase the server load.';
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
$string['settingdefaultinputoptions'] = 'Default input options';
$string['settingdefaultinputoptions_desc'] = 'Used when creating a new question, or adding a new input to an existing question.';
$string['settingdefaultquestionoptions'] = 'Default input options';
$string['settingdefaultquestionoptions_desc'] = 'Used when creating a new question.';
$string['settingmathsdisplay'] = 'Maths filter';
$string['settingmathsdisplay_mathjax'] = 'MathJax';
$string['settingmathsdisplay_tex'] = 'Moodle TeX filter';
$string['settingmathsdisplay_maths'] = 'Old OU maths filter';
$string['settingmathsdisplay_oumaths'] = 'New OU maths filter';
$string['settingmathsdisplay_desc'] = 'The method used to display maths. If you select MathJax, then you will need to follow the instructions on the Healthcheck page to set it up. If you select a filter, then you must ensure that filter is enabled on the Manage filters configuration page.';
$string['settingsmathsdisplayheading'] = 'Maths display options';
$string['settingsmaximasettings'] = 'Connecting to Maxima';
$string['settingparsercacheinputlength'] = 'Cache parsed expressions longer than';
$string['settingparsercacheinputlength_desc'] = 'The expression parser gets quite slow on long expressions (for example complicated question variables). Therefore we cache the result of parsing expressions longer than a this limit. Ideally, this setting should be set to a value where doign the cache lookup takes about as long as doing the parsing. 50 characters is an educated guess at this. If set to 0, the cache is disabled.';
$string['settingplatformtype'] = 'Platform type';
$string['settingplatformtype_desc'] = 'STACK needs to know what sort of operating system it is running on. The "Server" option gives better performance at the cost of having to set up an additional server. The option "Linux (optimised)" is explained on the Optimising Maxima page in the documentation.';
$string['settingplatformtypelinux'] = 'Linux';
$string['settingplatformtypelinuxoptimised'] = 'Linux (optimised)';
$string['settingplatformtypewin']  = 'Windows';
$string['settingplatformtypeserver'] = 'Server';
$string['settingplatformmaximacommand'] = 'Maxima command';
$string['settingplatformmaximacommand_desc'] = 'If this is blank, STACK will make an educated guess as to where to find Maxima. If that fails, this should be set to the full path of the maxima or maxima-optimised executable.  Use for development and debugging only. Do not use on a production system: use optimised, or better, the Maxima Pool option.';
$string['settingplatformmaximacommandopt'] = 'Optimised Maxima command';
$string['settingplatformmaximacommandopt_desc'] = 'This should be set to the full path of the maxima-optimised executable.  Consider using the timeout command on linux based systems. E.g. timeout --kill-after=10s 10s maxima';
$string['settingplatformmaximacommandserver'] = 'URL of the Maxima Pool';
$string['settingplatformmaximacommandserver_desc'] = 'For Platform type: Server, this is must be set to the URL of the Maxima Pool servlet.';
$string['settingplatformplotcommand'] = 'Plot command';
$string['settingplatformplotcommand_desc'] = 'Normally this can be left blank, but if graph plotting is not working, you may need to supply the full path to the gnuplot command here.';
$string['settingreplacedollars'] = 'Replace <code>$</code> and <code>$$</code>';
$string['settingreplacedollars_desc'] = 'Replace <code>$...$</code> and <code>$$...$$</code> delimiters in question text, in addition to <code>\\\\[...\\\\]</code> and <code>\\\\(...\\\\)</code>. A better option is to use the \'Fix maths delimiters\' script which is referred to below.';
$string['settingserveruserpass'] = 'Server username:password';
$string['settingserveruserpass_desc'] = 'If you are using Platform type: Server, and if you have set up your Maxima pool server with HTTP authentication, then you can put the username and password here. That is slighly safer than putting them in the URL. The format is username:password.';
$string['settingusefullinks'] = 'Useful links';
$string['settingmaximalibraries'] = 'Load optional Maxima libraries:';
$string['settingmaximalibraries_desc'] = 'This is a comma separated list of Maxima library names which will be automatically loaded into Maxima.  Only supported library names can be used: "stats, distrib, descriptive, simplex". These libraries will not be loaded if you have saved a maxima image to optimise performance.';
$string['settingmaximalibraries_error'] = 'The following package is not supported: {$a}';
$string['settingmaximalibraries_failed'] = 'It appears as if some of the Maxima packages you have asked for have failed to load.  Please refer to the installation instructions for notes about this error.';

// Strings used by replace dollars script.
$string['replacedollarscount'] = 'This category contains {$a} STACK questions.';
$string['replacedollarsin'] = 'Fixed maths delimiters in field {$a}';
$string['replacedollarsindex'] = 'Contexts with STACK questions';
$string['replacedollarsindexintro'] = 'Clicking on any of the links will take you to a page where you can review the questions for old-style maths delimiters, and automatically fix them. If you have too many questions (thousands) in one context, the amount of output will probably overwhelm your web browser, in which case add a preview=0 parameter to the URL and try again.';
$string['replacedollarsindextitle'] = 'Replace $s in question texts';
$string['replacedollarsnoproblems'] = 'No problem delimiters found.';
$string['replacedollarstitle'] = 'Replace $s in question texts in {$a}';
$string['replacedollarserrors'] = 'The following questions generated errors.';

// Strings used by the bulk run question tests script.
$string['bulktestcontinuefromhere'] = 'Run again or resume, starting from here';
$string['bulktestindexintro'] = 'Clicking on any of the links will run all the question tests in all the STACK questions in that context';
$string['bulktestindextitle'] = 'Run the question tests in bulk';
$string['bulktestnotests'] = 'This question does not have any tests.';
$string['bulktestnogeneralfeedback'] = 'This question does not have any general feedback.';
$string['bulktestnodeployedseeds'] = 'This question does have random variants, but has no deployed seeds.';
$string['bulktestrun'] = 'Run all the question tests for all the questions in the system (slow, admin only)';
$string['bulktesttitle'] = 'Running all the question tests in {$a}';
$string['overallresult'] = 'Overall result';
$string['seedx'] = 'Seed {$a}';
$string['testpassesandfails'] = '{$a->passes} passes and {$a->fails} failures.';

// Strings used by the question test script.
$string['addanothertestcase'] = 'Add another test case...';
$string['addatestcase'] = 'Add a test case...';
$string['addingatestcase'] = 'Adding a test case to question {$a}';
$string['alreadydeployed'] = ' A variant matching this Question note is already deployed.';
$string['completetestcase'] = 'Fill in the rest of the form to make a passing test-case';
$string['teacheranswercase'] = 'Use the teacher\'s answers as test-case';
$string['createtestcase'] = 'Create test case';
$string['currentlyselectedvariant'] = 'This is the variant shown below';
$string['deletetestcase'] = 'Delete test case {$a->no} for question {$a->question}';
$string['deletetestcaseareyousure'] = 'Are you sure you want to delete test case {$a->no} for question {$a->question}?';
$string['deletethistestcase'] = 'Delete this test case...';
$string['deploy'] = 'Deploy';
$string['deployedprogress'] = 'Deploying variants';
$string['deployedvariants'] = 'Deployed variants';
$string['deployedvariantsn'] = 'Deployed variants ({$a})';
$string['deploymany'] = 'Attempt to automatically deploy the following number of variants:';
$string['deploymanyerror'] = 'Error in user input: cannot deploy "{$a->err}" variants.';
$string['deploytoomanyerror'] = 'STACK will try to deploy up to at most 100 new variants in any one request.  No new variants deployed.';
$string['deploymanynonew'] = 'Too many repeated existing question notes were generated.';
$string['deploymanynotes'] = 'Note, STACK will give up if there are 10 failed attempts to generate a new question note, or when one question test fails.';
$string['deploymanysuccess'] = 'Number of new variants successfully created, tested and deployed: {$a->no}.';
$string['deployoutoftime'] = 'Time limit exceeded by using approx {$a->time} seconds.  Please try again to deploy more.';
$string['deployremoveall'] = 'Undeploy all variants: ';
$string['deploytestall'] = 'Run all tests on all deployed variants (slow): ';
$string['editingtestcase'] = 'Editing test case {$a->no} for question {$a->question}';
$string['editthistestcase'] = 'Edit this test case...';
$string['expectedanswernote'] = 'Expected answer note';
$string['expectedoutcomes'] = 'Expected PRT outcomes: [inputs used]';
$string['expectedpenalty'] = 'Expected penalty';
$string['expectedscore'] = 'Expected score';
$string['exportthisquestion'] = 'Export this question';
$string['exportthisquestion_help'] = 'This will create a Moodle XML export file containing just this one question. One example of when this is useful if you think this question demonstrates a bug in STACK that you would like to report to the developers.';
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
$string['questiontestsfor'] = 'Question tests for seed {$a}';
$string['questiontestspass'] = 'All question tests passed.';
$string['questiontestsdefault'] = '(Default)';
$string['runquestiontests'] = 'Question tests & deployed variants';
$string['runquestiontests_help'] = 'The question tests unit-test the questions to ensure the behaviour matches expectations expressed by the teacher, and deployed variants ensure random versions seen by a student are pre-tested against the question tests. These are tools to help you create and test reliable questions and should be used in all cases a question will be used by students.';
$string['seethisquestioninthequestionbank'] = 'See this question in the question bank';
$string['showingundeployedvariant'] = 'Showing undeployed variant: {$a}';
$string['switchtovariant'] = 'Switch to arbitrary variant';
$string['testcasexresult'] = 'Test case {$a->no} {$a->result}';
$string['testingquestion'] = 'Testing question {$a}';
$string['testingquestionvariants'] = 'Preparing question variants';
$string['testinputs'] = 'Test inputs';
$string['testinputsimpwarning'] = 'Please note that test inputs are always <em>unsimplified</em> regardless of the question or PRT option setting.  Please use <tt>ev(...,simp)</tt> to simplify part or all of the test input expressions.';
$string['testthisvariant'] = 'Switch to test this variant';
$string['tidyquestion'] = 'Tidy STACK question tool';
$string['tidyquestionx'] = 'Rename the parts of question {$a}';
$string['undeploy'] = 'Un-deploy';
$string['variant'] = 'Variant';

$string['basicquestionreport'] = 'Basic question use report';
$string['basicquestionreport_help'] = 'Generates a very basic report on attempts at this question on the server. Useful for deciding which PRT test can be added to improve feedback in the light of what the student actually does.  (Most questions are only used in one place)';
$string['basicreportraw'] = 'Raw data';
$string['basicreportnotes'] = 'Frequency of answer notes, for each PRT, regardless of which variant was used.';
$string['basicreportnotessplit'] = 'Frequency of answer notes, for each PRT, split by |, regardless of which variant was used.';
$string['basicreportvariants'] = 'Raw inputs and PRT answer notes by variant.';
$string['basicreportinputsummary'] = 'Raw inputs, regardless of which variant was used.';

// Equiv input specific string.
$string['equivnocomments'] = 'You are not permitted to use comments in this input type.  Please just work line by line.';
$string['equivfirstline'] = 'You have used the wrong first line in your argument!';

// Support scripts: CAS chat, healthcheck, etc.
$string['all'] = 'All';
$string['chat'] = 'Send to the CAS';
$string['castext'] = 'CAS text';
$string['chat_desc'] = 'The <a href="{$a->link}">CAS chat script</a> lets you test the connection to the CAS, and try out Maxima syntax.';
$string['chatintro'] = 'This page enables CAS text to be evaluated directly. It is a simple script which is a useful minimal example, and a handy way to check if the CAS is working, and to test various inputs.  The first text box enables variables to be defined, the second is for the CAS text itself.';
$string['chattitle'] = 'Test the connection to the CAS';
$string['clearedthecache'] = 'CAS cached has been cleared.';
$string['clearingcachefiles'] = 'Clearing cached STACK plot files {$a->done}/{$a->total}';
$string['clearingthecache'] = 'Clearing the cache';
$string['clearthecache'] = 'Clear the cache';
$string['healthcheck'] = 'STACK healthcheck';
$string['healthcheck_desc'] = 'The <a href="{$a->link}">healthcheck script</a> helps you verify that all aspects of STACK are working properly.';
$string['healthcheckcache_db'] = 'CAS results are being cached in the database.';
$string['healthcheckcache_none'] = 'CAS results are not being cached.';
$string['healthcheckcache_otherdb'] = 'CAS results are being cached in another database.';
$string['healthcheckcachestatus'] = 'The cache currently contains {$a} entries.';
$string['healthcheckconfig'] = 'Maxima configuration file';
$string['healthcheckconfigintro1'] = 'Found, and using, Maxima in the following directory:';
$string['healthcheckconfigintro2'] = 'Trying to automatically write the Maxima configuration file.';
$string['healthcheckconnect'] = 'Trying to connect to the CAS';
$string['healthcheckconnectintro'] = 'We are trying to evaluate the following CAS text:';
$string['healthcheckfilters'] = 'Please ensure that the {$a->filter} is enabled on the <a href="{$a->url}">Manage filters</a> page.';
$string['healthchecknombstring'] = 'STACK v4.3 and later requires the PHP module mbstring, which is missing.  Please read the installation docs.';
$string['healthchecklatex'] = 'Check LaTeX is being converted correctly';
$string['healthchecklatexintro'] = 'STACK generates LaTeX on the fly, and enables teachers to write LaTeX in questions. It assumes that LaTeX will be converted by a moodle filter.  Below are samples of displayed and inline expressions in LaTeX which should be appear correctly in your browser.  Problems here indicate incorrect moodle filter settings, not faults with STACK itself. STACK only uses the single and double dollar notation itself, but some question authors may be relying on the other forms.';
$string['healthchecklatexmathjax'] = 'STACK relies on the Moodle MathJax filter.  An alternative is to add javascript code to Moodle\'s additional HTML.  See the STACK installation docs for more details of this option.';
$string['healthcheckmathsdisplaymethod'] = 'Maths display method being used: {$a}.';
$string['healthcheckmaximabat'] = 'The maxima.bat file is missing';
$string['healthcheckmaximabatinfo'] = 'This script tried to automatically copy the maxima.bat script from inside "C:\Program files\Maxima-1.xx.y\bin" into "{$a}\stack". However, this seems not to have worked. Please copy this file manually.';
$string['healthchecksamplecas'] = 'The derivative of {@ x^4/(1+x^4) @} is \[ \frac{d}{dx} \frac{x^4}{1+x^4} = {@ diff(x^4/(1+x^4),x) @}. \]  Confirm if unicode is supported: \(\forall\) should be displayed {@unicode(8704)@}.';
$string['healthchecksampledisplaytex'] = '\[\sum_{n=1}^\infty \frac{1}{n^2} = \frac{\pi^2}{6}.\]';
$string['healthchecksampleinlinetex'] = '\(\sum_{n=1}^\infty \frac{1}{n^2} = \frac{\pi^2}{6}\).';
$string['healthcheckplots'] = 'Graph plotting';
$string['healthcheckplotsintro'] = 'There should be two different plots.  If two identical plots are seen then this is an error in naming the plot files. If no errors are returned, but a plot is not displayed then one of the following may help.  (i) check read permissions on the two temporary directories. (ii) change the options used by GNUPlot to create the plot. Currently there is no web interface to these options.';
$string['healthchecksampleplots'] = 'Two example plots below.  {@plot([x^4/(1+x^4),diff(x^4/(1+x^4),x)],[x,-3,3])@} {@plot([sin(x),x,x^2,x^3],[x,-3,3],[y,-3,3],grid2d)@}  A third, smaller, plot may be displayed here with traditional axes.  (Newer versions of Maxima only.) {@plot([sin(x),x,x^2,x^3],[x,-3,3],[y,-3,3],[box, false],[yx_ratio, 1],[axes, solid],[xtics, -3, 1, 3],[ytics, -3, 1, 3],[size,250,250])@}';
$string['healthchecksstackmaximaversion'] = 'Maxima version';
$string['healthchecksstackmaximaversionfixoptimised'] = 'Please <a href="{$a->url}">rebuild your optimised Maxima executable</a>.';
$string['healthchecksstackmaximaversionfixserver'] = 'Please rebuild the Maxima code on your MaximaPool server.';
$string['healthchecksstackmaximaversionfixunknown'] = 'It is not really clear how that happened. You will need to debug this problem yourself.  Start by clearing the CAS cache.';
$string['healthchecksstackmaximanotupdated'] = 'It seems that STACK has not been properly update. Please visit the <a href="{$a}">System administration -> Notifications page</a>.';
$string['healthchecksstackmaximatooold'] = 'So old the version is unknown!';
$string['healthchecksstackmaximaversionmismatch'] = 'The version of the STACK-Maxima libraries being used ({$a->usedversion}) does not match what is expected ({$a->expectedversion}) by this version of the STACK question type. {$a->fix}';
$string['healthchecksstackmaximaversionok'] = 'Correct and expected STACK-Maxima library version being used ({$a->usedversion}).';
$string['healthuncached'] = 'Uncached CAS call';
$string['healthuncachedintro'] = 'This section always sends a genuine call to the CAS, regardless of the current cache settings.  This is needed to ensure the connection to the CAS is really currently working.';
$string['healthuncachedstack_CAS_ok'] = 'CAS returned data as expected.  You have a live connection to the CAS.';
$string['healthuncachedstack_CAS_not'] = 'CAS returned some data as expected, but there were errors.';
$string['healthuncachedstack_CAS_version'] = 'Expected Maxima version : "{$a->expected}".  Actual Maxima version: {$a->actual}.';
$string['healthuncachedstack_CAS_versionnotchecked'] = 'You have chosen the "default" version of Maxima, so no Maxima version checking is being done.  Your raw connection is actually using version {$a->actual}.';
$string['healthuncachedstack_CAS_calculation'] = 'Expected CAS calculation : {$a->expected}.  Actual CAS calculation: {$a->actual}.';
$string['healthunabletolistavail'] = 'Platform type not currently set to "linux", so unable to list available versions of Maxima.';
$string['healthautomaxopt'] = 'Automatically create an optimised Maxima image';
$string['healthautomaxoptintro'] = 'For best performance we need to optimize maxima on a linux machine.  Use the plugin "healthcheck" page and see the documentation on this issue.';
$string['healthautomaxopt_succeeded'] = 'Create Optimised Maxima Image SUCCEEDED';
$string['healthautomaxopt_failed'] = 'Create Optimised Maxima Image FAILED : [{$a->errmsg}]';
$string['healthautomaxopt_ok'] = 'Maxima image created at: <tt>{$a->command}</tt>';
$string['healthautomaxopt_notok'] = 'Maxima image not created automatically.';
$string['healthautomaxopt_nolisp'] = 'Unable to determine LISP version, so Maxima image not created automatically.';
$string['healthautomaxopt_nolisprun'] = 'Unable to automatically locate lisp.run.  Try "sudo updatedb" from the command line and refer to the optimization docs.';
$string['healthcheckcreateimage'] = 'Create Maxima image';
$string['stackInstall_replace_dollars_desc'] = 'The <a href="{$a->link}">fix maths delimiters script</a> can be used to replace old-style delimiters like <code>@...@</code>, <code>$...$</code> and <code>$$...$$</code> in your questions with the new recommended <code>{@...@}</code>, <code>\(...\)</code> and <code>\[...\]</code>.';
$string['stackInstall_testsuite_title'] = 'A test suite for STACK Answer tests';
$string['stackInstall_testsuite_title_desc'] = 'The <a href="{$a->link}">answer-tests script</a> verifies that the answer tests are performing correctly. They are also useful to learn by example how each answer-test can be used.';
$string['stackInstall_testsuite_intro'] = 'This page allows you to test that the STACK answer tests are functioning correctly.  Note that only answer tests can be checked through the web interface.  Other Maxima commands need to be checked from the command line: see unittests.mac.';
$string['stackInstall_testsuite_choose'] = 'Please choose an answer test.';
$string['stackInstall_testsuite_pass'] = 'All tests passed!';
$string['stackInstall_testsuite_fail'] = 'Not all tests passed!';
$string['stackInstall_testsuite_failingtests'] = 'Tests that failed';
$string['stackInstall_testsuite_failingupgrades'] = 'Questions which failed on upgrade.';
$string['stackInstall_testsuite_notests'] = 'Questions with no tests: please add some!';
$string['stackInstall_testsuite_nogeneralfeedback'] = 'Questions with no general feedback: students really appreciate worked solutions!';
$string['stackInstall_testsuite_nodeployedseeds'] = 'Questions with random variants, but no deployed seeds';
$string['stackInstall_testsuite_errors'] = 'This question generated the following errors at runtime.';
$string['answertest'] = 'Answer test';
$string['answertest_help'] = 'An answer test is used to compare two expressions to establish whether they satisfy some mathematical criteria.';
$string['answertest_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Answer_tests.md';
$string['testsuitecolpassed'] = 'Passed?';
$string['studentanswer'] = 'Student response';
$string['teacheranswer'] = 'Teacher answer';
$string['options'] = 'Options';
$string['testsuitefeedback'] = 'Feedback';
$string['testsuitecolerror'] = 'CAS errors';
$string['testsuitecolrawmark'] = 'Raw mark';
$string['testsuitecolexpectedscore'] = 'Expected mark';
$string['testsuitepass'] = 'Pass';
$string['testsuiteknownfail'] = 'Expected failure';
$string['testsuiteknownfailmaths'] = 'Expected failure (maths)';
$string['testsuitefail'] = 'Fail';
$string['testsuitenotests']       = 'Number of tests: {$a->no}. ';
$string['testsuiteteststook']     = 'Tests took {$a->time} seconds. ';
$string['testsuiteteststookeach'] = 'Average per test: {$a->time} seconds. ';
$string['stackInstall_input_title'] = "A test suite for validation of student's input";
$string['stackInstall_input_title_desc'] = 'The <a href="{$a->link}">input-tests script</a> provides test cases of how STACK interprets mathematical expressions.  They are also useful to learn by example.';
$string['stackInstall_input_intro'] = "This page allows you to test how STACK interprets various inputs from a student.  This currently only checks with the most liberal settings, trying to adopt an informal syntax and insert stars.  <br />'V' columns record validity as judged by PHP and the CAS.  V1 = PHP valid, V2 = CAS valid.";
$string['phpvalid'] = 'V1';
$string['phpcasstring'] = 'PHP output';
$string['phpsuitecolerror'] = 'PHP errors';
$string['phpvalidatemismatch'] = '[PHP validate mismatch]';
$string['casvalidatemismatch'] = '[CAS validate mismatch]';
$string['ansnotemismatch']     = '[Answernote mismatch]';
$string['displaymismatch']     = '[LaTeX mismatch]';
$string['casvalid'] = 'V2';
$string['casvalue'] = 'CAS value';
$string['casdisplay'] = 'CAS display';
$string['cassuitecolerrors'] = 'CAS errors';

$string['texdisplaystyle'] = 'Display-style equation';
$string['texinlinestyle'] = 'Inline-style equation';

// Used in CAS statement validation.
$string['stackCas_spaces']                  = 'Illegal spaces found in expression {$a->expr}.';
$string['stackCas_underscores']             = 'The following use of underscore characters is not permitted: {$a}.';
$string['stackCas_percent']                 = '&#037; found in expression {$a->expr}.';
$string['stackCas_missingLeftBracket']      = 'You have a missing left bracket <span class="stacksyntaxexample">{$a->bracket}</span> in the expression: {$a->cmd}.';
$string['stackCas_missingRightBracket']     = 'You have a missing right bracket <span class="stacksyntaxexample">{$a->bracket}</span> in the expression: {$a->cmd}.';
$string['stackCas_qmarkoperators']          = 'Question marks are not permitted in responses.';
$string['stackCas_apostrophe']              = 'Apostrophes are not permitted in responses.';
$string['stackCas_newline']                 = 'Newline characters are not permitted in responses.';
$string['stackCas_forbiddenChar']           = 'CAS commands may not contain the following characters: {$a->char}.';
$string['stackCas_useinsteadChar']          = 'Please replace <span class="stacksyntaxexample">{$a->bad}</span> with \'<span class="stacksyntaxexample">{$a->char}</span>\'.';
$string['stackCas_finalChar']               = '\'{$a->char}\' is an invalid final character in {$a->cmd}';
$string['stackCas_MissingStars']            = 'You seem to be missing * characters. Perhaps you meant to type {$a->cmd}.';
$string['stackCas_unknownFunction']         = 'Unknown function: {$a->forbid} in the term {$a->term}.';
$string['stackCas_noFunction']              = 'The use of the function {$a->forbid} in the term {$a->term} is not permitted in this context.';
$string['stackCas_forbiddenFunction']       = 'Forbidden function: {$a->forbid}.';
$string['stackCas_spuriousop']              = 'Unknown operator: {$a->cmd}.';
$string['stackCas_forbiddenOperator']       = 'Forbidden operator: {$a->forbid}.';
$string['stackCas_forbiddenVariable']       = 'Forbidden variable or constant: {$a->forbid}.';
$string['stackCas_operatorAsVariable']      = 'Operator {$a->op} interpreted as variable, check syntax.';
$string['stackCas_redefinitionOfConstant']  = 'Redefinition of key constants is forbidden: {$a->constant}.';
$string['stackCas_unknownFunctionCase']     = 'Input is case sensitive: {$a->forbid} is an unknown function. Did you mean {$a->lower}?';
// TODO: the message should say that while these are valid names for variables as long as this case combination is not implicitely allowed we assume that you have typoed the known different case.
$string['stackCas_unknownVariableCase']     = 'Input is case sensitive: {$a->forbid} is an unknown variable. Did you mean {$a->lower}?';
$string['stackCas_unsupportedKeyword']      = 'Unsupported keyword: {$a->forbid}.';
$string['stackCas_forbiddenWord']           = 'The expression {$a->forbid} is forbidden.';
$string['stackCas_bracketsdontmatch']       = 'The brackets are incorrectly nested in the expression: {$a->cmd}.';
$string['stackCas_chained_inequalities']    = 'You appear to have "chained inequalities" e.g. \(a &lt b &lt c\).  You need to connect individual inequalities with logical operations such as \(and\) or \(or\).';
$string['stackCas_backward_inequalities']   = 'Non-strict inequalities e.g. \( \leq \) or \( \geq \) must be entered as <= or >=.  You have {$a->cmd} in your expression, which is backwards.';
$string['stackCas_unencpsulated_comma']     = 'A comma in your expression appears in a strange way.  Commas are used to separate items in lists, sets etc.  You need to use a decimal point, not a comma, in floating point numbers.';
$string['stackCas_trigspace']               = 'To apply a trig function to its arguments you must use brackets, not spaces.  For example use {$a->trig} instead.';
$string['stackCas_trigop']                  = 'You must apply {$a->trig} to an argument.  You seem to have {$a->forbid}, which looks like you have tried to use {$a->trig} as a variable name.';
$string['stackCas_trigexp']                 = 'You cannot take a power of a trig function by writing {$a->forbid}. The square of the value of \(\{$a->identifier}(x)\) is typed in as <tt>{$a->identifier}(x)^2</tt>.  The inverse of \(\{$a->identifier}(x)\) is written <tt>a{$a->identifier}(x)</tt> and not \(\{$a->identifier}^{-1}(x)\) .';
$string['stackCas_trigparens']              = 'When you apply a trig function to its arguments you must use round parentheses not square brackets.  E.g {$a->forbid}.';
$string['stackCas_triginv']                 = 'Inverse trig functions are written {$a->goodinv} not {$a->badinv}.';
$string['stackCas_badLogIn']                = 'You have typed in the expression <tt>In</tt>.  The natural logarithm is entered as <tt>ln</tt> in lower case.  ("Lima November" not "India November")';
$string['stackCas_unitssynonym']            = 'You appear to have units {$a->forbid}.  Did you mean {$a->unit}?';
$string['stackCas_unknownUnitsCase']        = 'Input of units is case sensitive:  {$a->forbid} is an unknown unit. Did you mean one from the following list {$a->unit}?';
$string['stackCas_applyingnonobviousfunction'] = 'This function call {$a->problem} does not appear to have an easily visible function name. Due to security reasons you may need to simplify the call so that the validator can see the function name.';
$string['stackCas_callingasfunction']       = 'Calling the result of a function call is forbidden {$a->problem}, lambdas are still allowed.';
$string['stackCas_badpostfixop']            = 'You have a bad "postfix" operator in your expression.';

// Used in cassession.class.php.
$string['stackCas_CASError']                = 'The CAS returned the following error(s):';
$string['stackCas_allFailed']               = 'CAS failed to return any evaluated expressions.  Please check your connection with the CAS.';
$string['stackCas_failedReturn']            = 'CAS failed to return any data.';
$string['stackCas_failedReturnOne']         = 'CAS failed to return some data.';

// Used in keyval.class.php.
$string['stackCas_inputsdefined']           = 'You may not use input names as variables.  You have tried to define <code>{$a}</code>';

// Used in castext.class.php.
$string['stackCas_MissingAt']               = 'You are missing a <code>@</code> sign. ';
$string['stackCas_MissingDollar']           = 'You are missing a <code>$</code> sign. ';
$string['stackCas_MissingString']           = 'You are missing a quotation sign <code>"</code>. ';
$string['stackCas_StringOperation']         = 'A string appears to be in the wrong place. This is the issue: <code>{$a->issue}</code>. ';
$string['stackCas_MissingOpenTeXCAS']       = 'Missing <code>{@</code>. ';
$string['stackCas_MissingClosingTeXCAS']    = 'Missing <code>@}</code>. ';
$string['stackCas_MissingOpenRawCAS']       = 'Missing <code>{#</code>. ';
$string['stackCas_MissingClosingRawCAS']    = 'Missing <code>#}</code>. ';
$string['stackCas_MissingOpenDisplay']      = 'Missing <code>\[</code>. ';
$string['stackCas_MissingCloseDisplay']     = 'Missing <code>\]</code>. ';
$string['stackCas_MissingOpenInline']       = 'Missing <code>\(</code>. ';
$string['stackCas_MissingCloseInline']      = 'Missing <code>\)</code>. ';
$string['stackCas_MissingOpenHTML']         = 'Missing opening html tag. ';
$string['stackCas_MissingCloseHTML']        = 'Missing closing html tag. ';
$string['stackCas_failedValidation']        = 'CASText failed validation. ';
$string['stackCas_invalidCommand']          = 'CAS commands not valid. ';
$string['stackCas_CASErrorCaused']          = 'caused the following error:';
$string['stackCas_errorpos']                = 'At about line {$a->line} character {$a->col}.';

// Used in blocks.
$string['stackBlock_ifNeedsCondition']       = 'If-block needs a test attribute. ';
$string['stackBlock_escapeNeedsValue']       = 'Escape-block needs a value attribute. ';
$string['stackBlock_unknownBlock']           = 'The following block is unknown: ';
$string['stackBlock_missmatch']              = 'has no match. ';
$string['stackBlock_else_out_of_an_if']      = '"else" cannot exist outside an if block.';
$string['stackBlock_elif_out_of_an_if']      = '"elif" cannot exist outside an if block.';
$string['stackBlock_multiple_else']          = 'Multiple else branches in an if block.';
$string['stackBlock_elif_after_else']        = '"elif" after an "else" in an if block.';
$string['unrecognisedfactstags']             = 'The following facts tag(s) are not recognized: {$a->tags}.';
$string['stackHintOld']                      = 'The CASText has old-style hint tags. These should now be in the form <pre>[[facts:tag]]</pre>';

$string['Maxima_DivisionZero']  = 'Division by zero.';
$string['Maxima_Args']  = 'args: argument must be a non-atomic expression. ';
$string['Variable_function']   = 'The following appear in your expression as both a variable and a function: {$a->m0}.  Please clarify your input.  Either insert <code>*</code> symbols to remove functions, or make all occurances functions.';
$string['Lowest_Terms']   = 'Your answer contains fractions that are not written in lowest terms.  Please cancel factors and try again.';
$string['Illegal_floats'] = 'Your answer contains floating point numbers, that are not allowed here.  You need to type in numbers as fractions.  For example, you should type 1/3 not 0.3333, which is after all only an approximation to one third.';
$string['Illegal_strings'] = 'Your answer contains "strings" these are not allowed here.';
$string['Illegal_lists'] = 'Your answer contains lists "[a,b,c]" these are not allowed here.';
$string['Illegal_sets'] = 'Your answer contains sets "{a,b,c}" these are not allowed here.';
$string['Illegal_groups'] = 'Your answer contains evaluation groups "(a,b,c)" these are not allowed here.';
$string['Illegal_groupping'] = 'Your answer contains parenthesis used to group operations, these are forbidden here. You should probably manipulate the expression to eliminate them.';
$string['Illegal_control_flow'] = 'Your answer contains control-flow statements like the <code>if</code>-conditional or the <code>do</code>-loop, these are forbidden here, you should probably provide the result of these statements as the answer.';
$string['qm_error'] = 'Your answer contains question mark characters, ?, which are not permitted in answers.  You should replace these with a specific value.';
$string['Equiv_Illegal_set']  = 'Sets are not allowed when reasoning by equivalence.';
$string['Equiv_Illegal_list']  = 'Lists are not allowed when reasoning by equivalence.';
$string['Equiv_Illegal_matrix']  = 'Matrices are not allowed when reasoning by equivalence.';
$string['CommaError']     = 'Your answer contains commas which are not part of a list, set or matrix.  <ul><li>If you meant to type in a list, please use <tt>{...}</tt>,</li><li>If you meant to type in a set, please use <tt>{...}</tt>.</li></ul>';
$string['Bad_assignment']   = 'When listing the values of a variable you should do so in the following way: {$a->m0}.  Please modify your input.';
$string['Illegal_identifiers_in_units']           = 'The input contains a variable name when just units were expected.';
$string['Illegal_illegal_operation_in_units']     = 'The operator <code>{$a}</code> is not allowed in this input.';
$string['Illegal_illegal_power_of_ten_in_units']  = 'The value may not contain non integer powers of ten.';
$string['Illegal_input_form_units']               = 'This input expects a numerical value followed or multiplied by an expression defining an unit, e.g. <code>1.23*W/m^2</code>. Note that the unit required here may be something else.';
$string['Illegal_x10'] = 'Your answer appears to use the character "x" as a multiplication sign.  Please use <code>*</code> for multiplication.';


$string['stackBlock_jsxgraph_width']       = 'The width of a JSXGraph must use a known CSS-length unit.';
$string['stackBlock_jsxgraph_height']      = 'The height of a JSXGraph must use a known CSS-length unit.';
$string['stackBlock_jsxgraph_width_num']   = 'The numeric portion of the width of a JSXGraph must be a raw number and must not contain any extra chars.';
$string['stackBlock_jsxgraph_height_num']  = 'The numeric portion of the height of a JSXGraph must be a raw number and must not contain any extra chars.';
$string['stackBlock_jsxgraph_ref']         = 'The jsxgraph-block only supports referencing inputs present in the same CASText section \'{$a->var}\' does not exist here.';
$string['stackBlock_jsxgraph_param']       = 'The jsxgraph-block supports only these parameters in this context: {$a->param}.';

// Answer tests.
$string['stackOptions_AnsTest_values_AlgEquiv']            = "AlgEquiv";
$string['stackOptions_AnsTest_values_AlgEquivNouns']       = "AlgEquivNouns";
$string['stackOptions_AnsTest_values_EqualComAss']         = "EqualComAss";
$string['stackOptions_AnsTest_values_CasEqual']            = "CasEqual";
$string['stackOptions_AnsTest_values_SameType']            = "SameType";
$string['stackOptions_AnsTest_values_SubstEquiv']          = "SubstEquiv";
$string['stackOptions_AnsTest_values_SysEquiv']            = "SysEquiv";
$string['stackOptions_AnsTest_values_Sets']                = "Sets";
$string['stackOptions_AnsTest_values_Expanded']            = "Expanded";
$string['stackOptions_AnsTest_values_FacForm']             = "FacForm";
$string['stackOptions_AnsTest_values_SingleFrac']          = "SingleFrac";
$string['stackOptions_AnsTest_values_PartFrac']            = "PartFrac";
$string['stackOptions_AnsTest_values_CompSquare']          = "CompletedSquare";
$string['stackOptions_AnsTest_values_PropLogic']           = "PropositionalLogic";
$string['stackOptions_AnsTest_values_Equiv']               = "EquivReasoning";
$string['stackOptions_AnsTest_values_EquivFirst']          = "EquivFirst";
$string['stackOptions_AnsTest_values_SigFigsStrict']       = "SigFigsStrict";
$string['stackOptions_AnsTest_values_NumRelative']         = "NumRelative";
$string['stackOptions_AnsTest_values_NumAbsolute']         = "NumAbsolute";
$string['stackOptions_AnsTest_values_NumSigFigs']          = "NumSigFigs";
$string['stackOptions_AnsTest_values_NumDecPlaces']        = "NumDecPlaces";
$string['stackOptions_AnsTest_values_NumDecPlacesWrong']   = "NumDecPlacesWrong";
$string['stackOptions_AnsTest_values_UnitsSigFigs']        = "UnitsSigFigs";
$string['stackOptions_AnsTest_values_UnitsStrictSigFigs']  = "UnitsStrictSigFigs";
$string['stackOptions_AnsTest_values_UnitsRelative']       = "UnitsRelative";
$string['stackOptions_AnsTest_values_UnitsStrictRelative'] = "UnitsStrictRelative";
$string['stackOptions_AnsTest_values_UnitsAbsolute']       = "UnitsAbsolute";
$string['stackOptions_AnsTest_values_UnitsStrictAbsolute'] = "UnitsStrictAbsolute";
$string['stackOptions_AnsTest_values_GT']                  = "Num-GT";
$string['stackOptions_AnsTest_values_GTE']                 = "Num-GTE";
$string['stackOptions_AnsTest_values_LowestTerms']         = "LowestTerms";
$string['stackOptions_AnsTest_values_Diff']                = "Diff";
$string['stackOptions_AnsTest_values_Int']                 = "Int";
$string['stackOptions_AnsTest_values_String']              = "String";
$string['stackOptions_AnsTest_values_StringSloppy']        = "StringSloppy";
$string['stackOptions_AnsTest_values_SRegExp']             = "SRegExp";

$string['AT_NOTIMPLEMENTED']        = 'This answer test has not been implemented. ';
$string['TEST_FAILED']              = 'The answer test failed to execute correctly: please alert your teacher. {$a->errors}';
$string['TEST_FAILED_Q']            = 'The answer test failed to execute correctly: please alert your teacher. ';
$string['AT_MissingOptions']        = 'Missing option when executing the test. ';
$string['AT_InvalidOptions']        = 'Option field is invalid. {$a->errors}';
$string['AT_EmptySA']               = 'Attempted to execute an answer test with an empty student answer, probably a CAS validation problem when authoring the question.';
$string['AT_EmptyTA']               = 'Attempted to execute an answer test with an empty teacher answer, probably a CAS validation problem when authoring the question.';


$string['ATAlgEquiv_SA_not_expression'] = 'Your answer should be an expression, not an equation, inequality, list, set or matrix. ';
$string['ATAlgEquiv_SA_not_matrix']     = 'Your answer should be a matrix, but is not. ';
$string['ATAlgEquiv_SA_not_list']       = 'Your answer should be a list, but is not.  Note that the syntax to enter a list is to enclose the comma separated values with square brackets. ';
$string['ATAlgEquiv_SA_not_set']        = 'Your answer should be a set, but is not.  Note that the syntax to enter a set is to enclose the comma separated values with curly brackets. ';
$string['ATAlgEquiv_SA_not_realset']    = 'Your answer should be a subset of the real numbers.  This could be a set of numbers, or a collection of intervals.';
$string['ATAlgEquiv_SA_not_equation']   = 'Your answer should be an equation, but is not. ';
$string['ATAlgEquiv_SA_not_logic']      = 'Your answer should be an equation, inequality or a logical combination of many of these, but is not. ';
$string['ATAlgEquiv_TA_not_equation']   = 'You have entered an equation, but an equation is not expected here. You may have typed something like "y=2*x+1" when you only needed to type "2*x+1". ';
$string['ATAlgEquiv_SA_not_inequality'] = 'Your answer should be an inequality, but is not. ';
$string['ATAlgEquiv_SA_not_function']   = 'Your answer should be a function, defined using the operator <tt>:=</tt>, but is not. ';
$string['ATAlgEquiv_SA_not_string']     = 'Your answer should be a string, but is not. ';
$string['Subst']                        = 'Your answer would be correct if you used the following substitution of variables. {$a->m0} ';

$string['ATSubstEquiv_Opt_List']        = 'The option to this answer test must be a list.  This is an error.  Please contact your teacher. ';

$string['ATSets_SA_not_set']            = 'Your answer should be a set, but is not.  Note that the syntax to enter a set is to enclose the comma separated values with curly brackets. ';
$string['ATSets_SB_not_set']            = 'The "Sets" answer test expects its second argument to be a set.  This is an error.  Please contact your teacher.';
$string['ATSets_wrongentries']          = 'These entries should not be elements of your set. {$a->m0} ';
$string['ATSets_missingentries']        = 'The following are missing from your set. {$a->m0} ';
$string['ATSets_duplicates']            = 'Your set appears to contain duplicate entries!';

$string['ATInequality_nonstrict']       = 'Your inequality should be strict, but is not! ';
$string['ATInequality_strict']          = 'Your inequality should not be strict! ';
$string['ATInequality_backwards']       = 'Your inequality appears to be backwards. ';

$string['ATLowestTerms_wrong']          = 'You need to cancel fractions within your answer. ';
$string['ATLowestTerms_entries']        = 'The following terms in your answer are not in lowest terms.  {$a->m0} Please try again.  ';
$string['ATLowestTerms_not_rat']        = 'You must clear the following from the denominator of your fraction: {$a->m0}';

$string['ATList_wronglen']              = 'Your list should have {$a->m0} elements, but it actually has {$a->m1}. ';
$string['ATList_wrongentries']          = 'The entries underlined in red below are those that are incorrect. {$a->m0} ';

$string['ATMatrix_wrongsz']             = 'Your matrix should be {$a->m0} by {$a->m1}, but it is actually {$a->m2} by {$a->m3}. ';
$string['ATMatrix_wrongentries']        = 'The entries underlined in red below are those that are incorrect. {$a->m0} ';

$string['ATSet_wrongsz']                = 'Your set should have {$a->m0} different elements, but it actually has {$a->m1}. ';
$string['ATSet_wrongentries']           = 'The following entries are incorrect, although they may appear in a simplified form from that which you actually entered. {$a->m0} ';

$string['irred_Q_factored']             = 'The term {$a->m0} should be unfactored, but is not. ';
$string['irred_Q_commonint']            = 'You need to take out a common factor. ';  // Needs a space at the end.
$string['irred_Q_optional_fac']         = 'You could do more work, since {$a->m0} can be further factored.  However, you don\'t need to. ';

$string['FacForm_UnPick_morework']      = 'You could still do some more work on the term {$a->m0}. ';
$string['FacForm_UnPick_intfac']        = 'You need to take out a common factor. ';

$string['ATFacForm_error_list']         = 'The answer test failed.  Please contact your systems administrator';
$string['ATFacForm_error_degreeSA']     = 'The CAS could not establish the algebraic degree of your answer.';
$string['ATFacForm_isfactored']         = 'Your answer is factored, well done. ';  // Needs a space at the end.
$string['ATFacForm_notfactored']        = 'Your answer is not factored. '; // Needs a space at the end.
$string['ATFacForm_notpoly']            = 'This term is expected to be a polynomial, but is not.';
$string['ATFacForm_notalgequiv']        = 'Note that your answer is not algebraically equivalent to the correct answer.  You must have done something wrong. '; // Needs a space at the end.

$string['ATPartFrac_error_list']        = 'The answer test failed.  Please contact your systems administrator';
$string['ATPartFrac_true']              = '';
$string['ATPartFrac_single_fraction']   = 'Your answer seems to be a single fraction, it needs to be in a partial fraction form. ';
$string['ATPartFrac_diff_variables']    = 'The variables in your answer are different to those of the question, please check them. ';
$string['ATPartFrac_denom_ret']         = 'If your answer is written as a single fraction then the denominator would be {$a->m0}. In fact, it should be {$a->m1}. ';
$string['ATPartFrac_ret_expression']    = 'Your answer as a single fraction is {$a->m0} ';

$string['ATSingleFrac_error_list']      = 'The answer test failed.  Please contact your systems administrator';
$string['ATSingleFrac_true']            = '';
$string['ATSingleFrac_part']            = 'Your answer needs to be a single fraction of the form \( {a}\over{b} \). ';
$string['ATSingleFrac_var']             = 'The variables in your answer are different to the those of the question, please check them. ';
$string['ATSingleFrac_ret_exp']         = 'Your answer is not algebraically equivalent to the correct answer. You must have done something wrong. ';
$string['ATSingleFrac_div']             = 'Your answer contains fractions within fractions.  You need to clear these and write your answer as a single fraction.';

$string['ATCompSquare_true']            = '';
$string['ATCompSquare_false']           = '';
$string['ATCompSquare_not_AlgEquiv']    = 'Your answer appears to be in the correct form, but is not equivalent to the correct answer.';
$string['ATCompSquare_false_no_summands']  = 'The completed square is of the form \( a(\cdots\cdots)^2 + b\) where \(a\) and \(b\) do not depend on your variable.  More than one of your summands appears to depend on the variable in your answer.';
$string['ATCompSquare_SA_not_depend_var']  = 'Your answer should depend on the variable {$a->m0} but it does not!';

$string['ATInt_error_list']          = 'The answer test failed.  Please contact your systems administrator';
$string['ATInt_const_int']           = 'You need to add a constant of integration. This should be an arbitrary constant, not a number.';
$string['ATInt_const']               = 'You need to add a constant of integration, otherwise this appears to be correct.  Well done.';
$string['ATInt_EqFormalDiff']        = 'The formal derivative of your answer does equal the expression that you were asked to integrate.  However, your answer differs from the correct answer in a significant way, that is to say not just, e.g., a constant of integration.  Please ask your teacher about this.';
$string['ATInt_logabs']              = 'The formal derivative of your answer does equal the expression that you were asked to integrate.  However, your answer differs from the correct answer in a significant way, that is to say not just, e.g., a constant of integration.  Your teacher may expect you to use the result \(\int\frac{1}{x} dx = \log(|x|)+c\), rather than \(\int\frac{1}{x} dx = \log(x)+c\).  Please ask your teacher about this.';
$string['ATInt_weirdconst']          = 'The formal derivative of your answer does equal the expression that you were asked to integrate.  However, you have a strange constant of integration.  Please ask your teacher about this.';
$string['ATInt_logabs_inconsistent'] = 'There appear to be strange inconsistencies between your use of \(\log(...)\) and \(\log(|...|)\).  Please ask your teacher about this.  ';
$string['ATInt_diff']                = 'It looks like you have differentiated instead!';
$string['ATInt_generic']             = 'The derivative of your answer should be equal to the expression that you were asked to integrate, that was: {$a->m0}  In fact, the derivative of your answer, with respect to {$a->m1} is: {$a->m2} so you must have done something wrong!';
$string['ATInt_STACKERROR_OptList']  = 'The answer test failed to execute correctly: please alert your teacher. When the option to ATInt is a list it must have exactly two elements, but does not.';

$string['ATDiff_error_list']        = 'The answer test failed.  Please contact your systems administrator';
$string['ATDiff_int']               = 'It looks like you have integrated instead!';

$string['ATNumerical_SA_not_list']       = 'Your answer should be a list, but is not.  Note that the syntax to enter a list is to enclose the comma separated values with square brackets. ';
$string['ATNumerical_SA_not_set']        = 'Your answer should be a set, but is not.  Note that the syntax to enter a set is to enclose the comma separated values with curly brackets. ';
$string['ATNumerical_SA_not_number']     = 'Your answer should be a floating point number, but is not. ';
$string['ATNumerical_FAILED']            = 'Your answer should be a floating point number, or a list or set of numbers.  It is not. ';
$string['ATNumerical_STACKERROR_tol']    = 'The numerical tolerance for ATNumerical should be a floating point number, but is not.  This is an internal error with the test.  Please ask your teacher about this. ';

$string['ATNum_OutofRange']         = 'A numerical expression is outside the supported range.  Please contact your teacher. ';

$string['ATNumSigFigs_error_list']  = 'The answer test failed.  Please contact your systems administrator';
$string['ATNumSigFigs_NotDecimal']  = 'Your answer should be a decimal number, but is not! ';
$string['ATNumSigFigs_WrongSign']   = 'Your answer has the wrong algebraic sign. ';
$string['ATNumSigFigs_Inaccurate']  = 'The accuracy of your answer is not correct.  Either you have not rounded correctly, or you have rounded an intermediate answer which propagates an error.';
$string['ATNumSigFigs_WrongDigits'] = 'Your answer contains the wrong number of significant digits. ';

$string['ATUnits_SA_not_expression']      = 'Your answer needs to be a number together with units. Do not use sets, lists, equations or matrices. ';
$string['ATUnits_SA_no_units']            = 'Your answer must have units. ';
$string['ATUnits_SA_excess_units']        = 'Your answer has used units (or variables), but should not. ';
$string['ATUnits_SA_only_units']          = 'Your answer needs to be a number together with units. Your answer only has units. ';
$string['ATUnits_SA_bad_units']           = 'Your answer must have units, and you must use multiplication to attach the units to a value, e.g. <code>3.2*m/s</code>. ';
$string['ATUnits_SA_errorbounds_invalid'] = 'Your answer has error bounds.  In this case do not indicate error bounds, instead use just the quantity and units. ';
$string['ATUnits_SO_wrong_units']         = 'The units specified for the numerical tolerance must match the units used for the teacher\'s answer.  This is an internal error with the test.  Please ask your teacher about this. ';
$string['ATUnits_incompatible_units']     = 'Your units are incompatible with those used by the teacher. ';
$string['ATUnits_compatible_units']       = 'Your units are different from those used by the teacher, but are compatible with them.  Numerical values are being converted to SI base units for comparison. ';
$string['ATUnits_correct_numerical']      = 'Please check your units carefully. ';

$string['ATNumDecPlaces_OptNotInt']    = 'For ATNumDecPlaces the test option must be a positive integer, in fact "{$a->m0}" was received. ';
$string['ATNumDecPlaces_NoDP']         = 'Your answer must be a decimal number, including a decimal point. ';
$string['ATNumDecPlaces_Wrong_DPs']    = 'Your answer has been given to the wrong number of decimal places.';
$string['ATNumDecPlaces_Float']        = 'Your answer must be a floating point number, but is not.';

$string['ATNumDecPlacesWrong_OptNotInt']    = 'For ATNumDecPlacesWrong the test option must be a positive integer, in fact "{$a->m0}" was received. ';

$string['ATSysEquiv_SA_not_list']               = 'Your answer should be a list, but it is not!';
$string['ATSysEquiv_SB_not_list']               = 'The teacher\'s answer is not a list.  Please contact your teacher.';
$string['ATSysEquiv_SA_not_eq_list']            = 'Your answer should be a list of equations, but it is not!';
$string['ATSysEquiv_SB_not_eq_list']            = 'The teacher\'s answer is not a list of equations, but should be.';
$string['ATSysEquiv_SA_not_poly_eq_list']       = 'One or more of your equations is not a polynomial!';
$string['ATSysEquiv_SB_not_poly_eq_list']       = 'The Teacher\'s answer should be a list of polynomial equations, but is not.  Please contact your teacher.';
$string['ATSysEquiv_SA_missing_variables']      = 'Your answer is missing one or more variables!';
$string['ATSysEquiv_SA_extra_variables']        = 'Your answer includes too many variables!';
$string['ATSysEquiv_SA_wrong_variables']        = 'Your answer uses the wrong variables!';
$string['ATSysEquiv_SA_system_underdetermined'] = 'The equations in your system appear to be correct, but you need others besides.';
$string['ATSysEquiv_SA_system_overdetermined']  = 'The entries underlined in red below are those that are incorrect. {$a->m0} ';

$string['ATRegEx_missing_option']               = 'Missing regular expression in CAS Option field.';


$string['ATSRegExp_SB_not_string']              = 'The second argument to the SRegExp answer test must be a string. The test failed. Please contact your teacher.';
$string['ATSRegExp_SA_not_string']              = 'The first argument to the SRegExp answer test must be a string. The test failed. Please contact your teacher.';

$string['ATEquiv_SA_not_list']               = 'The first argument to the Equiv answer test should be a list, but the test failed. Please contact your teacher.';
$string['ATEquiv_SB_not_list']               = 'The second argument to the Equiv answer test should be a list, but the test failed. Please contact your teacher.';
$string['ATEquivFirst_SA_not_list']      = 'The first argument to the Equiv answer test should be a list, but the test failed. Please contact your teacher.';
$string['ATEquivFirst_SB_not_list']      = 'The second argument to the Equiv answer test should be a list, but the test failed. Please contact your teacher.';
$string['ATEquivFirst_SA_wrong_start']   = 'The first line in your argument must be "{$a->m0}". ';
$string['ATEquivFirst_SA_wrong_end']     = 'Your final answer is not in the correct form. ';
$string['ATEquivFirst_SA_wrong_end']     = 'Your final answer is not in the correct form. ';
$string['equiv_SAMEROOTS']               = '(Same roots)';
$string['equiv_ANDOR']                   = 'and/or confusion!';
$string['equiv_MISSINGVAR']              = 'Missing assignments';
$string['equiv_ASSUMEPOSVARS']           = 'Assume +ve vars';
$string['equiv_ASSUMEPOSREALVARS']       = 'Assume +ve real vars';
$string['equiv_LET']                     = 'Let';
// We could localise the strings below using \vee, \wedge etc.
$string['equiv_AND']                     = 'and';
$string['equiv_OR']                      = 'or';
$string['equiv_NOT']                     = 'not';
$string['equiv_NAND']                    = 'nand';
$string['equiv_NOR']                     = 'nor';
$string['equiv_XOR']                     = 'xor';
$string['equiv_XNOR']                    = 'xnor';
$string['equiv_IMPLIES']                 = 'implies';

$string['studentValidation_yourLastAnswer']     = 'Your last answer was interpreted as follows: {$a}';
$string['studentValidation_listofvariables']    = 'The variables found in your answer were: {$a}';
$string['studentValidation_listofunits']        = 'The units found in your answer were: {$a}';
$string['studentValidation_invalidAnswer']      = 'This answer is invalid. ';
$string['studentValidation_notes']              = '(This input is not assessed automatically by STACK.)';
$string['stackQuestion_noQuestionParts']        = 'This item has no question parts for you to answer.';

$string['Interval_notinterval']                 = 'An interval was expected, but instead we have {$a->m0}.';
$string['Interval_wrongnumargs']                = 'Interval construction must have exactly two arguments, so this must be an error: {$a->m0}.';
$string['Interval_backwards']                   = 'When constructing a real interval the end points must be ordered. {$a->m0} should be {$a->m1}.';
$string['Interval_illegal_entries']             = 'The following should not appear during construction of real sets: {$a->m0}';

// Documentation strings.
$string['stackDoc_404']                 = 'Error 404';
$string['stackDoc_docs']                = 'STACK Documentation';
$string['stackDoc_docs_desc']           = '<a href="{$a->link}">Documentation for STACK</a>: a local static wiki.';
$string['stackDoc_home']                = 'Documentation home';
$string['stackDoc_index']               = 'Category index';
$string['stackDoc_siteMap']             = 'Site map';
$string['stackDoc_siteMap_en']          = 'English site map';
$string['stackDoc_404message']          = 'File not found.';
$string['stackDoc_directoryStructure']  = 'Directory structure';
$string['stackDoc_version']             = 'Your site is running STACK version {$a}.';

// Fact sheets.
$string['greek_alphabet_name'] = 'The Greek Alphabet';
$string['greek_alphabet_fact'] = '
<center>
<table>
<tr><td>
 Upper case, \(\quad\) </td><td>  lower case, \(\quad\) </td><td>  name </td> </tr>   <tr> <td>
 \(A\)  </td><td>  \(\alpha\)  </td><td>  alpha  </td> </tr>   <tr> <td>
 \(B\)  </td><td>  \(\beta\)  </td><td>  beta </td> </tr>   <tr> <td>
 \(\Gamma\)  </td><td>  \(\gamma\)  </td><td>  gamma </td> </tr>   <tr> <td>
 \(\Delta\)  </td><td>  \(\delta\)  </td><td>  delta </td> </tr>   <tr> <td>
 \(E\)  </td><td>  \(\epsilon\)  </td><td>  epsilon </td> </tr>   <tr> <td>
 \(Z\)  </td><td>  \(\zeta\)  </td><td>  zeta </td> </tr>   <tr> <td>
 \(H\)  </td><td>  \(\eta\)  </td><td>  eta </td> </tr>   <tr> <td>
 \(\Theta\)  </td><td>  \(\theta\)  </td><td>  theta </td> </tr>   <tr> <td>
 \(K\)  </td><td>  \(\kappa\)  </td><td>  kappa </td> </tr>   <tr> <td>
 \(M\)  </td><td>  \(\mu\)  </td><td>  mu </td> </tr>   <tr> <td>
 \(N\)  </td><td>  \( u\)  </td><td>  nu </td> </tr>   <tr> <td>
 \(\Xi\)  </td><td>  \(\xi\)  </td><td>  xi </td> </tr>   <tr> <td>
 \(O\)  </td><td>  \(o\)  </td><td>  omicron </td> </tr>   <tr> <td>
 \(\Pi\)  </td><td>  \(\pi\)  </td><td>  pi </td> </tr>   <tr> <td>
 \(I\)  </td><td>  \(\iota\)  </td><td>  iota </td> </tr>   <tr> <td>
 \(P\)  </td><td>  \(\rho\) </td><td>  rho </td> </tr>   <tr> <td>
 \(\Sigma\)  </td><td>  \(\sigma\)  </td><td>  sigma </td> </tr>   <tr> <td>
 \(\Lambda\)  </td><td>  \(\lambda\)  </td><td>  lambda </td> </tr>   <tr> <td>
 \(T\)  </td><td>  \(\tau\)  </td><td>  tau </td> </tr>   <tr> <td>
 \(\Upsilon\)  </td><td>  \(\upsilon\)  </td><td>  upsilon </td> </tr>   <tr> <td>
 \(\Phi\)  </td><td>  \(\phi\)  </td><td>  phi </td> </tr>   <tr> <td>
 \(X\)  </td><td>  \(\chi\)  </td><td>  chi </td> </tr>   <tr> <td>
 \(\Psi\)  </td><td>  \(\psi\)  </td><td> psi </td> </tr>   <tr> <td>
 \(\Omega\)  </td><td>  \(\omega\)  </td><td>  omega </td></tr>
</table></center>';

$string['alg_inequalities_name'] = 'Inequalities';
$string['alg_inequalities_fact'] = '\[a>b \hbox{ means } a \hbox{ is greater than } b.\]
\[ a < b \hbox{ means } a \hbox{ is less than } b.\]
\[a\geq b \hbox{ means } a \hbox{ is greater than or equal to } b.\]
\[a\leq b \hbox{ means } a \hbox{ is less than or equal to } b.\]';

$string['alg_indices_name'] = 'The Laws of Indices';
$string['alg_indices_fact'] = 'The following laws govern index manipulation:
\[a^ma^n = a^{m+n}\]
\[\frac{a^m}{a^n} = a^{m-n}\]
\[(a^m)^n = a^{mn}\]
\[a^0 = 1\]
\[a^{-m} = \frac{1}{a^m}\]
\[a^{\frac{1}{n}} = \sqrt[n]{a}\]
\[a^{\frac{m}{n}} = \left(\sqrt[n]{a}\right)^m\]';

$string['alg_logarithms_name'] = 'The Laws of Logarithms';
$string['alg_logarithms_fact'] = 'For any base \(c>0\) with \(c \neq 1\):
\[\log_c(a) = b \mbox{, means } a = c^b\]
\[\log_c(a) + \log_c(b) = \log_c(ab)\]
\[\log_c(a) - \log_c(b) = \log_c\left(\frac{a}{c}\right)\]
\[n\log_c(a) = \log_c\left(a^n\right)\]
\[\log_c(1) = 0\]
\[\log_c(b) = 1\]
The formula for a change of base is:
\[\log_a(x) = \frac{\log_b(x)}{\log_b(a)}\]
Logarithms to base \(e\), denoted \(\log_e\) or alternatively \(\ln\) are called natural logarithms.  The letter \(e\) represents the exponential constant which is approximately \(2.718\).';

$string['alg_quadratic_formula_name'] = 'The Quadratic Formula';
$string['alg_quadratic_formula_fact'] = 'If we have a quadratic equation of the form:
\[ax^2 + bx + c = 0,\]
then the solution(s) to that equation given by the quadratic formula are:
\[x = \frac{-b \pm \sqrt{b^2 - 4ac}}{2a}.\]';


$string['alg_partial_fractions_name'] = 'Partial Fractions';
$string['alg_partial_fractions_fact'] = 'Proper fractions occur with \[{\frac{P(x)}{Q(x)}}\]
when \(P\) and \(Q\) are polynomials with the degree of \(P\) less than the degree of \(Q\).  This this case, we proceed
as follows: write \(Q(x)\) in factored form,
<ul>
<li>
a <em>linear factor</em> \(ax+b\) in the denominator produces a partial fraction of the form \[{\frac{A}{ax+b}}.\]
</li>
<li>
a <em>repeated linear factors</em> \((ax+b)^2\) in the denominator
produce partial fractions of the form \[{A\over ax+b}+{B\over (ax+b)^2}.\]
</li>
<li>
a <em>quadratic factor</em> \(ax^2+bx+c\)
in the denominator produces a partial fraction of
the form \[{Ax+B\over ax^2+bx+c}\]
</li>
<li>
<em>Improper fractions}</em> require an additional
term which is a polynomial of degree \(n-d\) where \(n\) is
the degree of the numerator (i.e. \(P(x)\)) and \(d\) is the degree of
the denominator (i.e. \(Q(x)\)).
</li></ul>';

$string['trig_degrees_radians_name'] = 'Degrees and Radians';
$string['trig_degrees_radians_fact'] = '\[
360^\circ= 2\pi \hbox{ radians},\quad
1^\circ={2\pi\over 360}={\pi\over 180}\hbox{ radians}
\]
\[
1 \hbox{ radian} = {180\over \pi} \hbox{ degrees}
\approx 57.3^\circ
\]';

$string['trig_standard_values_name'] = 'Standard Trigonometric Values';
$string['trig_standard_values_fact'] = '
\[\sin(45^\circ)={1\over \sqrt{2}}, \qquad \cos(45^\circ) = {1\over \sqrt{2}},\qquad
\tan( 45^\circ)=1
\]
\[
\sin (30^\circ)={1\over 2}, \qquad \cos (30^\circ)={\sqrt{3}\over 2},\qquad
\tan (30^\circ)={1\over \sqrt{3}}
\]
\[
\sin (60^\circ)={\sqrt{3}\over 2}, \qquad \cos (60^\circ)={1\over 2},\qquad
\tan (60^\circ)={ \sqrt{3}}
\]';

$string['trig_standard_identities_name'] = 'Standard Trigonometric Identities';
$string['trig_standard_identities_fact'] = '\[\sin(a\pm b)\ = \  \sin(a)\cos(b)\ \pm\  \cos(a)\sin(b)\]
 \[\cos(a\ \pm\ b)\ = \  \cos(a)\cos(b)\ \mp \\sin(a)\sin(b)\]
 \[\tan (a\ \pm\ b)\ = \  {\tan (a)\ \pm\ \tan (b)\over1\ \mp\ \tan (a)\tan (b)}\]
 \[ 2\sin(a)\cos(b)\ = \  \sin(a+b)\ +\ \sin(a-b)\]
 \[ 2\cos(a)\cos(b)\ = \  \cos(a-b)\ +\ \cos(a+b)\]
 \[ 2\sin(a)\sin(b) \ = \  \cos(a-b)\ -\ \cos(a+b)\]
 \[ \sin^2(a)+\cos^2(a)\ = \  1\]
 \[ 1+{\rm cot}^2(a)\ = \  {\rm cosec}^2(a),\quad \tan^2(a) +1 \ = \  \sec^2(a)\]
 \[ \cos(2a)\ = \  \cos^2(a)-\sin^2(a)\ = \  2\cos^2(a)-1\ = \  1-2\sin^2(a)\]
 \[ \sin(2a)\ = \  2\sin(a)\cos(a)\]
 \[ \sin^2(a) \ = \  {1-\cos (2a)\over 2}, \qquad \cos^2(a)\ = \  {1+\cos(2a)\over 2}\]';

$string['hyp_functions_name'] = 'Hyperbolic Functions';
$string['hyp_functions_fact'] = 'Hyperbolic functions have similar properties to trigonometric functions but can be represented in exponential form as follows:
 \[ \cosh(x)      = \frac{e^x+e^{-x}}{2}, \qquad \sinh(x)=\frac{e^x-e^{-x}}{2} \]
 \[ \tanh(x)      = \frac{\sinh(x)}{\cosh(x)} = \frac{{e^x-e^{-x}}}{e^x+e^{-x}} \]
 \[ {\rm sech}(x) ={1\over \cosh(x)}={2\over {\rm e}^x+{\rm e}^{-x}}, \qquad  {\rm cosech}(x)= {1\over \sinh(x)}={2\over {\rm e}^x-{\rm e}^{-x}} \]
 \[ {\rm coth}(x) ={\cosh(x)\over \sinh(x)} = {1\over {\rm tanh}(x)} ={{\rm e}^x+{\rm e}^{-x}\over {\rm e}^x-{\rm e}^{-x}}\]';

$string['hyp_identities_name'] = 'Hyperbolic Identities';
$string['hyp_identities_fact'] = 'The similarity between the way hyperbolic and trigonometric functions behave is apparent when observing some basic hyperbolic identities:
  \[{\rm e}^x=\cosh(x)+\sinh(x), \quad {\rm e}^{-x}=\cosh(x)-\sinh(x)\]
  \[\cosh^2(x) -\sinh^2(x) = 1\]
  \[1-{\rm tanh}^2(x)={\rm sech}^2(x)\]
  \[{\rm coth}^2(x)-1={\rm cosech}^2(x)\]
  \[\sinh(x\pm y)=\sinh(x)\ \cosh(y)\ \pm\ \cosh(x)\ \sinh(y)\]
  \[\cosh(x\pm y)=\cosh(x)\ \cosh(y)\ \pm\ \sinh(x)\ \sinh(y)\]
  \[\sinh(2x)=2\,\sinh(x)\cosh(x)\]
  \[\cosh(2x)=\cosh^2(x)+\sinh^2(x)\]
  \[\cosh^2(x)={\cosh(2x)+1\over 2}\]
  \[\sinh^2(x)={\cosh(2x)-1\over 2}\]';

$string['hyp_inverse_functions_name'] = 'Inverse Hyperbolic Functions';
$string['hyp_inverse_functions_fact'] = '\[\cosh^{-1}(x)=\ln\left(x+\sqrt{x^2-1}\right) \quad \mbox{ for } x\geq 1\]
 \[\sinh^{-1}(x)=\ln\left(x+\sqrt{x^2+1}\right)\]
 \[\tanh^{-1}(x) = \frac{1}{2}\ln\left({1+x\over 1-x}\right) \quad \mbox{ for } -1< x < 1\]';


$string['calc_diff_standard_derivatives_name'] = 'Standard Derivatives';
$string['calc_diff_standard_derivatives_fact'] = 'The following table displays the derivatives of some standard functions.  It is useful to learn these standard derivatives as they are used frequently in calculus.
<center>
<table>
<tr><th>\(f(x)\)               </th><th> \(f\'(x)\)</th></tr>
<tr>
<td>\(k\), constant           </td> <td> \(0\) </td> </tr> <tr> <td>
\(x^n\), any constant \(n\) </td> <td> \(nx^{n-1}\)</td> </tr> <tr> <td>
\(e^x\)                   </td> <td> \(e^x\)</td> </tr> <tr> <td>
\(\ln(x)=\log_{\rm e}(x)\)              </td> <td> \(\frac{1}{x}\)                </td> </tr> <tr> <td>
\(\sin(x)\)                             </td> <td> \(\cos(x)\)                    </td> </tr> <tr> <td>
\(\cos(x)\)                             </td> <td> \(-\sin(x)\)                   </td> </tr> <tr> <td>
\(\tan(x) = \frac{\sin(x)}{\cos(x)}\)   </td> <td>   \(\sec^2(x)\)                </td> </tr> <tr> <td>
\(cosec(x)=\frac{1}{\sin(x)}\)         </td> <td>   \(-cosec(x)\cot(x)\)        </td> </tr> <tr> <td>
\(\sec(x)=\frac{1}{\cos(x)}\)           </td> <td>   \(\sec(x)\tan(x)\)           </td> </tr> <tr> <td>
\(\cot(x)=\frac{\cos(x)}{\sin(x)}\)     </td> <td>   \(-cosec^2(x)\)             </td> </tr> <tr> <td>
\(\cosh(x)\)                            </td> <td>   \(\sinh(x)\)                 </td> </tr> <tr> <td>
\(\sinh(x)\)                            </td> <td>   \(\cosh(x)\)                 </td> </tr> <tr> <td>
\(\tanh(x)\)                            </td> <td>   \(sech^2(x)\)               </td> </tr> <tr> <td>
\(sech(x)\)                            </td> <td>   \(-sech(x)\tanh(x)\)        </td> </tr> <tr> <td>
\(cosech(x)\)                          </td> <td>   \(-cosech(x)\coth(x)\)      </td> </tr> <tr> <td>
\(coth(x)\)                            </td> <td>   \(-cosech^2(x)\)            </td> </tr>
</table>
</center>

 \[ \frac{d}{dx}\left(\sin^{-1}(x)\right) =  \frac{1}{\sqrt{1-x^2}}\]
 \[ \frac{d}{dx}\left(\cos^{-1}(x)\right) =  \frac{-1}{\sqrt{1-x^2}}\]
 \[ \frac{d}{dx}\left(\tan^{-1}(x)\right) =  \frac{1}{1+x^2}\]
 \[ \frac{d}{dx}\left(\cosh^{-1}(x)\right) =  \frac{1}{\sqrt{x^2-1}}\]
 \[ \frac{d}{dx}\left(\sinh^{-1}(x)\right) =  \frac{1}{\sqrt{x^2+1}}\]
 \[ \frac{d}{dx}\left(\tanh^{-1}(x)\right) =  \frac{1}{1-x^2}\]
';



$string['calc_diff_linearity_rule_name'] = 'The Linearity Rule for Differentiation';
$string['calc_diff_linearity_rule_fact'] = '\[{{\rm d}\,\over {\rm d}x}\big(af(x)+bg(x)\big)=a{{\rm d}f(x)\over {\rm d}x}+b{{\rm d}g(x)\over {\rm d}x}\quad a,b {\rm\  constant.}\]';

$string['calc_product_rule_name'] = 'The Product Rule';
$string['calc_product_rule_fact'] = 'The following rule allows one to differentiate functions which are
multiplied together.  Assume that we wish to differentiate \(f(x)g(x)\) with respect to \(x\).
\[ \frac{\mathrm{d}}{\mathrm{d}{x}} \big(f(x)g(x)\big) = f(x) \cdot \frac{\mathrm{d} g(x)}{\mathrm{d}{x}}  + g(x)\cdot \frac{\mathrm{d} f(x)}{\mathrm{d}{x}},\] or, using alternative notation, \[ (f(x)g(x))\' = f\'(x)g(x)+f(x)g\'(x). \]';

$string['calc_quotient_rule_name'] = 'The Quotient Rule';
$string['calc_quotient_rule_fact'] = 'The quotient rule for differentiation states that for any two differentiable functions \(f(x)\) and \(g(x)\),
 \[\frac{d}{dx}\left(\frac{f(x)}{g(x)}\right)=\frac{g(x)\cdot\frac{df(x)}{dx}\ \ - \ \ f(x)\cdot \frac{dg(x)}{dx}}{g(x)^2}. \]';

$string['calc_chain_rule_name'] = 'The Chain Rule';
$string['calc_chain_rule_fact'] = 'The following rule allows one to find the derivative of a composition of two functions.
Assume we have a function \(f(g(x))\), then defining \(u=g(x)\), the derivative with respect to \(x\) is given by:
\[\frac{df(g(x))}{dx} = \frac{dg(x)}{dx}\cdot\frac{df(u)}{du}.\]
Alternatively, we can write:
\[\frac{df(x)}{dx} = f\'(g(x))\cdot g\'(x).\]
';

$string['calc_rules_name'] = 'Calculus rules';
$string['calc_rules_fact']  = '<b>The Product Rule</b><br />The following rule allows one to differentiate functions which are
multiplied together.  Assume that we wish to differentiate \(f(x)g(x)\) with respect to \(x\).
\[ \frac{\mathrm{d}}{\mathrm{d}{x}} \big(f(x)g(x)\big) = f(x) \cdot \frac{\mathrm{d} g(x)}{\mathrm{d}{x}}  + g(x)\cdot \frac{\mathrm{d} f(x)}{\mathrm{d}{x}},\] or, using alternative notation, \[ (f(x)g(x))\' = f\'(x)g(x)+f(x)g\'(x). \]
<b>The Quotient Rule</b><br />The quotient rule for differentiation states that for any two differentiable functions \(f(x)\) and \(g(x)\),
\[\frac{d}{dx}\left(\frac{f(x)}{g(x)}\right)=\frac{g(x)\cdot\frac{df(x)}{dx}\ \ - \ \ f(x)\cdot \frac{dg(x)}{dx}}{g(x)^2}. \]
<b>The Chain Rule</b><br />The following rule allows one to find the derivative of a composition of two functions.
Assume we have a function \(f(g(x))\), then defining \(u=g(x)\), the derivative with respect to \(x\) is given by:
\[\frac{df(g(x))}{dx} = \frac{dg(x)}{dx}\cdot\frac{df(u)}{du}.\]
Alternatively, we can write:
\[\frac{df(x)}{dx} = f\'(g(x))\cdot g\'(x).\]
';

$string['calc_int_standard_integrals_name'] = 'Standard Integrals';
$string['calc_int_standard_integrals_fact'] = '

\[\int k\ dx = kx +c, \mbox{ where k is constant.}\]
\[\int x^n\ dx  = \frac{x^{n+1}}{n+1}+c, \quad (n\ne -1)\]
\[\int x^{-1}\ dx = \int {\frac{1}{x}}\ dx = \ln(|x|)+c = \ln(k*|x|) = \left\{\matrix{\ln(x)+c & x>0\cr
\ln(-x)+c & x<0\cr}\right.\]

<center>
<table>
<tr><th>\(f(x)\)</th><th> \(\int f(x)\ dx\)</th></tr>
<tr><td>\(e^x\) </td> <td>  \(e^x+c\)</td> <td> </td> </tr>
<tr><td>\(\cos(x)\) </td> <td>  \(\sin(x)+c\)   </td> <td> </td> </tr>
<tr><td>\(\sin(x)\) </td> <td>  \(-\cos(x)+c\)  </td> <td> </td> </tr>
<tr><td>\(\tan(x)\) </td> <td>  \(\ln(\sec(x))+c\) </td> <td>\(-\frac{\pi}{2} < x < \frac{\pi}{2}\)</td> </tr>
<tr><td>\(\sec x\)  </td> <td>  \(\ln (\sec(x)+\tan(x))+c\) </td> <td> \( -{\pi\over 2}< x < {\pi\over 2}\)</td> </tr>
<tr><td>cosec\(\, x\) </td> <td>  \(\ln ($cosec$(x)-\cot(x))+c\) </td> <td>\(0 < x < \pi\)</td> </tr>
<tr><td>cot\(\,x\) </td> <td>  \(\ln(\sin(x))+c\) </td> <td>  \(0< x< \pi\) </td> </tr>
<tr><td>\(\cosh(x)\) </td> <td>  \(\sinh(x)+c\)</td> <td></td> </tr>
<tr><td>\(\sinh(x)\) </td> <td>  \(\cosh(x) + c\) </td> <td> </td> </tr>
<tr><td>\(\tanh(x)\) </td> <td>  \(\ln(\cosh(x))+c\)</td> <td> </td> </tr>
<tr><td>coth\((x)\) </td> <td>  \(\ln(\sinh(x))+c \)</td> <td>   \(x>0\)</td> </tr>
<tr><td>\({1\over x^2+a^2}\) </td> <td>  \({1\over a}\tan^{-1}{x\over a}+c\)</td> <td> \(a>0\)</td> </tr>
<tr><td>\({1\over x^2-a^2}\) </td> <td>  \({1\over 2a}\ln{x-a\over x+a}+c\) </td> <td>  \(|x|>a>0\)</td> </tr>
<tr><td>\({1\over a^2-x^2}\) </td> <td>  \({1\over 2a}\ln{a+x\over a-x}+c\) </td> <td>   \(|x|<a\)</td> </tr>
<tr><td>\({1\over \sqrt{x^2+a^2}}\) </td> <td>  \(\sinh^{-1}\left(\frac{x}{a}\right) + c\) </td> <td> \(a>0\) </td> </tr>
<tr><td>\({1\over \sqrt{x^2-a^2}}\) </td> <td>  \(\cosh^{-1}\left(\frac{x}{a}\right) + c\) </td> <td>  \(x\geq a > 0\) </td> </tr>
<tr><td>\({1\over \sqrt{x^2+k}}\) </td> <td>  \(\ln (x+\sqrt{x^2+k})+c\)</td> <td> </td> </tr>
<tr><td>\({1\over \sqrt{a^2-x^2}}\) </td> <td>  \(\sin^{-1}\left(\frac{x}{a}\right)+c\)</td> <td>  \(-a\leq x\leq a\)  </td> </tr>
</table></center>';

$string['calc_int_linearity_rule_name'] = 'The Linearity Rule for Integration';
$string['calc_int_linearity_rule_fact'] = '\[\int \left(af(x)+bg(x)\right){\rm d}x = a\int\!\!f(x)\,{\rm d}x
\,+\,b\int \!\!g(x)\,{\rm d}x, \quad (a,b \, \, {\rm constant.})
\]';

$string['calc_int_methods_substitution_name'] = 'Integration by Substitution';
$string['calc_int_methods_substitution_fact'] = '\[
\int f(u){{\rm d}u\over {\rm d}x}{\rm d}x=\int f(u){\rm d}u
\quad\hbox{and}\quad \int_a^bf(u){{\rm d}u\over {\rm d}x}\,{\rm
d}x = \int_{u(a)}^{u(b)}f(u){\rm d}u.
\]';

$string['calc_int_methods_parts_name'] = 'Integration by Parts';
$string['calc_int_methods_parts_fact'] = '\[
\int_a^b u{{\rm d}v\over {\rm d}x}{\rm d}x=\left[uv\right]_a^b-
\int_a^b{{\rm d}u\over {\rm d}x}v\,{\rm d}x\]
or alternatively: \[\int_a^bf(x)g(x)\,{\rm d}x=\left[f(x)\,\int
g(x){\rm d}x\right]_a^b -\int_a^b{{\rm d}f\over {\rm
d}x}\left\{\int g(x){\rm d}x\right\}{\rm d}x.\]';

$string['Illegal_singleton_power'] = 'This input requires a numeric value presented in one of the following forms: <code>{$a->forms}</code>';
$string['Illegal_singleton_floats'] = 'This input does not accept decimal numbers in the given form. This input requires a numeric value presented in one of the following forms: <code>{$a->forms}</code>';
$string['Illegal_singleton_integer'] = 'This input does not accept integer values. This input requires a numeric value presented in one of the following forms: <code>{$a->forms}</code>';

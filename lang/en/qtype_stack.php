<?php

/*
This file is part of Stack - http://stack.bham.ac.uk//

Stack is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

Stack is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Stack.  If not, see <http://www.gnu.org/licenses/>.
*/

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

// casstring.class.php
$string['stackCas_spaces']                  = 'Spaces found in expression: ';
$string['stackCas_percent']                 = '&#037; found in expression: ';
$string['stackCas_missingLeftBracket']      = 'You have a missing left bracket $a in the expression: ';
$string['stackCas_missingRightBracket']     = 'You have a missing right bracket $a in the expression: ';
$string['stackCas_apostrophe']              = 'Apostrophes are not permitted in responses: ';
$string['stackCas_forbiddenChar']           = 'CAS commands may not contain the following characters: {$a[0]}.';
$string['stackCas_finalChar']               = '\'{$a[0]}\' is an invalid final character in {$a[1]}';
$string['stackCas_MissingStars']            = 'You seem to be missing *\'s.<br /> Perhaps you meant to type ';
$string['stackCas_unknownFunction']         = 'Unknown function:';
$string['stackCas_unsupportedKeyword']      = 'Unsupported keyword: ';
$string['stackCas_forbiddenWord']           = 'Forbidden Word: ';

// cassession.class.php
$string['stackCas_CASError']                = 'The CAS returned the following error(s):';
$string['stackCas_allFailed']               = 'CAS failed to return any evaluated expressions.  Please check your connection with the CAS.';

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

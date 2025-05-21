<?php
// This file is part of Stack - http://stack.maths.ed.ac.uk/
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
 * This script searches to make sure language strings are really in use in the plugin.
 *
 * @package    qtype_stack
 * @subpackage cli
 * @copyright  2025 The University of Edinburgh
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);
require(__DIR__ . '/../../../../config.php');
require_once($CFG->libdir . '/clilib.php');
require_once(__DIR__ . '/../lang/en/qtype_stack.php');
$keys = array_keys($string);

/**
 * This function finds all the occurances of $str.
 * @param string $str
 * @return string
 */
function grep_for($str) {
    $command = "cd ..\n grep -rn \"{$str}\" *\n";
    $env = ['PATH' => getenv('PATH')];
    $descriptors = [
        0 => ['pipe', 'r'],
        1 => ['pipe', 'w'],
    ];
    $grepprocess = proc_open($command . ' 2>&1', $descriptors, $pipes, null, $env);
    if (!is_resource($grepprocess)) {
        throw new exception('Could not open a process: ' . $command);
    }

    $ret = '';
    while (!feof($pipes[1])) {
        $out = fread($pipes[1], 1024);
        if ('' == $out) {
            // Pause.
            usleep(1000);
        }
        $ret .= $out;
    }

    fclose($pipes[0]);
    fclose($pipes[1]);

    return trim($ret);
}

$known = [];
$known['exceptionmessage'] = true;
$known['autosimplify'] = true;
$known['autosimplifyprt'] = true;
$known['ValidateVarsSpurious'] = true;
$known['ValidateVarsMissing'] = true;
$known['privacy:metadata'] = true;
$known['stack:usediagnostictools'] = true;
$known['answernotedefaulttrue'] = true;
$known['answernotedefaultfalse'] = true;
$known['bulktestindexintro'] = true;
$known['healthuncached'] = true;

$ffound = 0;
$nfound = 0;
foreach ($keys as $key) {
    if (substr($key, -5) === '_help' || substr($key, -5) === '_link') {
        $key = substr($key, 0, strlen($key) - 5);
    }
    if (substr($key, -5) === '_name' || substr($key, -5) === '_fact') {
        break;
    }

    // The following are legitimate ways language strings might appear in the code.
    $strs = [];
    $strs[] = "stack_string('" . $key . "'";
    $strs[] = "stack_string_error('" . $key . "'";
    $strs[] = "get_string('" . $key . "'";
    $strs[] = "output_cas_text('" . $key . "'";
    $strs[] = "{{#str}} " . $key;
    $strs[] = 'message=\\"' . $key;
    $strs[] = 'return(\\[true, \\"' . $key;
    $strs[] = 'return(\\[false, \\"' . $key;
    $strs[] = 'StackBasicReturn(false, false, \\"' . $key . '\\"';
    $strs[] = 'StackBasicReturn(true, false, \\"' . $key . '\\"';
    $strs[] = 'StackAddFeedback(\\"\\", \\"' . $key . '\\"';
    $strs[] = 'StackAddFeedback(FeedBack, \\"' . $key . '\\"';
    $strs[] = 'StackAddFeedback(fb, \\"' . $key . '\\"';
    $strs[] = 'StackAddNote(\\"\\", \\"' . $key . '\\"';
    $strs[] = 'StackAddNote(AnswerNote, \\"' . $key . '\\"';
    $strs[] = 'StackAddNote(ansnote, \\"' . $key . '\\"';

    $found = false;
    if (array_key_exists($key, $known)) {
        $found = true;
    } else if (substr($key, 0, 28) === 'stackOptions_AnsTest_values_') {
        $found = true;
    } else if (substr($key, 0, 10) === 'pluginname') {
        $found = true;
    } else if (substr($key, 0, 9) === 'inputtype') {
        $found = true;
    } else if (substr($key, 0, 11) === 'healthcheck') {
        $found = true;
    } else {
        foreach ($strs as $str) {
            $ret = grep_for($str);
            if ($ret !== '') {
                $found = true;
                break;
            }
        }
    }
    if (!$found) {
        echo "Not found: " . $key . "\n";
        $nfound += 1;
    } else {
        $ffound += 1;
    }
}

echo "\n\nFound: " . $ffound . "\n";
echo "Not found: " . $nfound . "\n\n";

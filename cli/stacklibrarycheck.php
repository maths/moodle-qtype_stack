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
 * Add description here!
 * @package    qtype_stack
 * @copyright  2025 University of Edinburgh.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

define('CLI_SCRIPT', true);

// This file allows developers to check filenames in the stacklibrary only
// contain basic characters, - and _.

require(__DIR__ . '/../../../../config.php');
require_once(__DIR__ . '/../stack/utils.class.php');
require_once($CFG->libdir . '/clilib.php');

function report($d) {
    global $CFG;
    $a = [];

    if (is_dir($d)) {
        if ($dh = opendir($d)) {
            while (($f = readdir($dh)) !== false) {
                $fpath = "$d/$f";

                $allowedcharsregex = '~[^' . preg_quote(
                    '0123456789.qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM-_', '~'
                    ) . ']~u';

                $matches = [];
                // Check for permitted characters.
                if (preg_match_all($allowedcharsregex, $f, $matches)) {
                    $invalidchars = [];
                    foreach ($matches as $match) {
                        $badchar = $match[0];
                        if (!array_key_exists($badchar, $invalidchars)) {
                            switch ($badchar) {
                                case " ":
                                    $invalidchars[$badchar] = "[SPACE]";
                                    break;
                                case "\n":
                                    $invalidchars[$badchar] = "\\n";
                                    break;
                                case "\r":
                                    $invalidchars[$badchar] = "\\r";
                                    break;
                                case "\t":
                                    $invalidchars[$badchar] = "\\t";
                                    break;
                                case "\v":
                                    $invalidchars[$badchar] = "\\v";
                                    break;
                                case "\e":
                                    $invalidchars[$badchar] = "\\e";
                                    break;
                                case "\f":
                                    $invalidchars[$badchar] = "\\f";
                                    break;
                                default:
                                    $invalidchars[$badchar] = $badchar;
                                }
                            }
                        }
                        $a[] = [$fpath, 'E', 'Forbidden characters: ' . implode($invalidchars)];
                    }
                if (substr($f, 0, 1) != '.') {
                    if (filetype($fpath) == 'dir') {
                        $a = array_merge($a, report($fpath));
                    } else {
                        $a[] = [$fpath, 'F', 'Found file ' . $f];
                    }
                }
            }
            closedir($dh);
        }
    }
    return $a;
}

$lib = stack_utils::convert_slash_paths($CFG->dirroot .
    '/question/type/stack/samplequestions');
$a = report($lib);

$c = 0;
foreach ($a as $data) {
    if ('F' != $data[1]) {
        echo $data[0].": ";
        echo $data[2]."\n";
    } else {
        $c += 1;
    }
}

echo "\n\nNumber of questions: " . $c . "\n\n";

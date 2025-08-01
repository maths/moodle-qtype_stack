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

/** @var int the Maximum allowable length of a filename. */
const MAX_FILENAME_LENGTH = 100;
/** @var int the Maximum allowable length of a directory name. */
const MAX_DIRNAME_LENGTH = 100;

/**
 * This function cretes a report starting in a particular directory.
 * @param string $d
 * @return array
 */
function report($d) {
    $a = [];

    // Check for maximum directory name length.
    $ds = explode('/', $d);
    $ds = end($ds);
    if (strlen($ds) > MAX_DIRNAME_LENGTH) {
        $a[] = [$ds, 'D', 'Directory name exceeds limit of ' . MAX_DIRNAME_LENGTH . ' characters.'];
    }

    if (is_dir($d)) {
        if ($dh = opendir($d)) {
            while (($f = readdir($dh)) !== false) {
                $fpath = "$d/$f";

                // Check for maximum filename length.
                if (strlen($f) > MAX_FILENAME_LENGTH) {
                    $a[] = [$fpath, 'L', 'Filename exceeds limit of ' . MAX_FILENAME_LENGTH . ' characters.'];
                }

                $badcharacters = '/[\/\\\?\%\'*:|"<> $!\`&]/'; // phpcs:ignore moodle.Strings.ForbiddenStrings.Found

                $matches = [];
                // Check for permitted characters.
                if (preg_match_all($badcharacters, $f, $matches)) {
                    $invalidchars = [];
                    foreach ($matches[0] as $match) {
                        $badchar = $match[0];
                        if (!array_key_exists($badchar, $invalidchars)) {
                            switch ($badchar) {
                                case " ":
                                    $invalidchars[$badchar] = "[SPACE]";
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
$p = 0;
foreach ($a as $data) {
    if ('F' != $data[1]) {
        echo $data[0].": ";
        echo $data[2]."\n";
        $p += 1;
    } else {
        $c += 1;
    }
}

echo "\nNumber of questions: " . $c;
echo "\nNumber of Problems: " . $p . "\n\n";

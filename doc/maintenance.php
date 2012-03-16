<?php
// This file is part of Stack - http://stack.bham.ac.uk/
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
 * Documentation system as a static wiki of markdown.
 *
 * @package stackDoc
 * @author Ben Holmes
 * @copyright  2012 The University of Birmingham
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../../../config.php');
require_once(dirname(__FILE__) . '/phpMarkdown/markdown.php');
// TODO: remove all this and use the library provided as a core part of Moodle?
require_once(dirname(__FILE__) . '/../locallib.php');
require_once(dirname(__FILE__) . '/../stack/utils.class.php');

/*
 *  This file serves the contents of a local directory and renders markup to html
 *  A file is requested by appending it's path (from the doc root) to doc.php
 *  e.g. for /CAS/Maxima.md    request    /doc.php/CAS/Maxima.md
 *  Language selection is done automatically.
 */

require_login();

function report($d) {
    global $CFG;
    $root = $CFG->wwwroot;
    $host = $root.'/question/type/stack/doc/doc.php';
    $docs = $CFG->dirroot;
    $webDocs = '/question/type/stack/doc/en';
    $a = array();

    if (is_dir($d)) {
        if ($dh = opendir($d)) {
            while (($f = readdir($dh)) !== false) {
                if (substr($f, 0, 1) != '.'){
                    $fPath = "$d/$f";
                    if(filetype($fPath) == 'dir') {
                        $a = array_merge($a, report($fPath));
                    } else {
                        $fName  = pathinfo($fPath, PATHINFO_FILENAME);
                        $fExt   = pathinfo($fPath, PATHINFO_EXTENSION);
                        $fSize  = filesize($fPath);
                        $relDir = str_replace($docs, '', $d);

                        $a[] = array($fPath, 'F', 'Found file ' . "$fPath");

                        if ($fSize >= 7000) {
                            $a[] = array($fPath, 'W', "Large file ($fSize bytes)");
                        }
                        if ($fExt != 'md') {
                            $a[] = array($fPath, 'W', "Not a markdown file ($fExt)");
                        }

                        // Let's do some link checking, step one: scrape the links off the document's web page
                        $links = strip_tags(file_get_contents($fPath), "<a>");
                        preg_match_all("/<a(?:[^>]*)href=\"([^\"]*)\"(?:[^>]*)>(?:[^<]*)<\/a>/is", $links, $found);

                        //found[0] will have the full a tags, found[1] contains their href properties
                        // Step two, visit these links and check for 404s
                        foreach($found[1] as $i => $link) {
                            if (strpos($link, 'mailto:') !== 0
                                and strpos($link, 'docMaintenance.php') === false
                                and ($_GET['ext'] or strpos($link, 'http') !== 0)) {
                                // Don't check mailto:, this file (ARGH!)
                                // Also if ?ext not true then better not be an external link

                                if (strpos($link, 'http') !== 0) {
                                // If a local link, do some preparation

                                    if (strpos($link, '/') === 0) {
                                        $link = $host . $link; // Not a relative link
                                    } else {
                                        $link = $webDocs . rtrim($relDir, '/') . '/' . $link;
                                    }

                                    $segs = explode('/', $link); // it looks like get_headers isn't evaluating these so lets do it manually

                                    while(($pos = array_search('.', $segs)) !== false) {
                                        unset($segs[$pos]);
                                    }

                                    while(($pos = array_search('..', $segs)) !== false) {
                                        unset($segs[$pos], $segs[$pos - 1]);
                                    }

                                    $link = implode('/', $segs);

                                    // finally it looks like #--- are getting parsed in the request, let's ommit them
                                    if (strpos($link, '#') !== false) {
                                        $link = substr($link, 0, strpos($link, '#'));
                                    }
                                }
                                $hs = get_headers($link);

                                if (strpos($hs[0], '404') !== false) {
                                    $a[] = array($fPath, 'E', 'Error 404 [' . $found[0][$i] . '] appears to be a dead link');
                                }
                            }
                        }
                    }
                }
            }
        closedir($dh);
        }
    }

    return $a;
}

?>

<h2>STACK Documentation Maintenance</h2>

<p><a href="doc.php">STACK menu</a></p>
<p>Key: <i>F - Found file, W - Warning, E - Error</i></p>
<p>This script crawls the entire documentation and checks for dead links and other issues.
Currently the script is crawling locally for speed, to check external links as well
<a href="maintenance.php?ext=1">click here</a></p>
<pre><?php

//TODO make this a nice table!
$docs = stack_utils::convert_slash_paths($CFG->dirroot.'/question/type/stack/doc/en');
$a = report($docs);
print_r($a);

?></pre>


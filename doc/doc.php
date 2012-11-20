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
require_once($CFG->libdir . '/markdown.php');

require_once(dirname(__FILE__) . '/../locallib.php');
require_once(dirname(__FILE__) . '/../stack/utils.class.php');

/*
 *  This file serves the contents of a local directory and renders markup to html
 *  A file is requested by appending it's path (from the doc root) to doc.php
 *  e.g. for /CAS/Maxima.md    request    /doc.php/CAS/Maxima.md
 *  Language selection is done automatically.
 */

if (substr($_SERVER['REQUEST_URI'], -7) == 'doc.php') {
    // Don't access doc.php directly, treat it like a directory instead.
    header('Location: ' . $_SERVER['REQUEST_URI'] . '/');
    exit();
}

//require_login();

$moodleroot = $CFG->dirroot;
$webroot = $CFG->wwwroot;
$docs    = '/question/type/stack/doc/' . $CFG->lang;    // Docs local location.
$webdocs = $webroot.'/question/type/stack/doc/doc.php';  // Doc.php web address.

$webpix  = $CFG->wwwroot.'/question/type/stack/pix/logo-sm.png';

$doclog  = stack_utils::convert_slash_paths($CFG->dataroot . '/stack/logs');

// The URL to the directory for static content to be served by the docs
// access this string in the docs with %CONTENT.
$docscontent = $webroot.'/question/type/stack/doc/content';

$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url('/question/type/stack/doc/doc.php');
$PAGE->set_title(stack_string('stackDoc_docs'));

if (substr($_SERVER['REQUEST_URI'], -7) == 'doc.php') {
    // Don't access doc.php directly, treat it like a directory instead.
    $uri     = '/';  // The uri string.
} else {

    $uri     = get_file_argument();  // The uri string.
}

$segs    = explode('/', $uri);
$lastseg = $segs[count($segs) - 1];

$body = '';
$header = '';

// Links for the end of the page.

if ($uri == '/') {
    // I.e. at doc.php/ the docs front page.
    $links = array($webdocs.'/Site_map' => stack_string('stackDoc_siteMap'));
} else if ('/Site_map' == $uri) {
    $links = array($webdocs   =>  stack_string('stackDoc_home'));
} else {
    $links = array($webdocs   =>  stack_string('stackDoc_home'),
                       './'       =>   stack_string('stackDoc_index'),
                       '../'      =>  stack_string('stackDoc_parent'),
             $webdocs.'/Site_map' =>  stack_string('stackDoc_siteMap'));
}

$linkstrs = array();
foreach ($links as $url => $link) {
    $linkstrs[] = "<a href=\"$url\">$link</a>";
}
$linkstr  = implode(' | ', $linkstrs);


if ('Site_map' == $lastseg) {
    $body .= $linkstr;
    $body .= '<h3>' .  stack_string('stackDoc_directoryStructure', 'stack') . '</h3>';
    $body .= index($moodleroot . $docs, $webdocs); // Assumes at a file in /.
} else {
    if ('' == $lastseg) {
        $doc = 'index.md';
    } else {
        $doc = '';
    }

    $file = $moodleroot . $docs . $uri . $doc;

    if (!file_exists($file)) {
        $header= 'HTTP/1.0 404 Not Found';
    }

    if (file_exists($file)) {

        $handle = fopen($file, 'r');
        $page   = fread($handle, filesize($file));
        fclose($handle);

        $page =  preg_replace('/\\\%CONTENT/', '$$$PARSE_ERROR', $page); // Escaped \%CONTENT won't get processed.
        $page =  preg_replace('/\%CONTENT/', $docscontent, $page);
        $page =  preg_replace('/\$\$\$PARSE_ERROR/', '%CONTENT', $page);

        $body .= $linkstr;
        $body .= "\n<hr/>\n";
        if (pathinfo($file, PATHINFO_EXTENSION) == 'md') {
            $page = str_replace("\\", "\\\\", $page);
            $options = new stdClass();
            $options->noclean = true;
            $body .= format_text(Markdown($page), FORMAT_HTML, $options); // Render it, in this case in Markdown.
        } else {
            $body .= $page;
        }
        $body .= "\n<hr/>\n";
        $body .= $linkstr;

    } else {

        $body .= html_writer::tag('h1', stack_string('stackDoc_404'));
        $body .= html_writer::tag('p', stack_string('stackDoc_404message'));
        $body .= $linkstr;

    }

}


echo $OUTPUT->header($header);
$pagetitle = "<img src=\"{$webpix}\" style=\"margin-right: 10px;\" />".stack_string('stackDoc_docs');
echo $OUTPUT->heading($pagetitle);

echo $body;

echo $OUTPUT->footer();


function index($d, $relpath = '') {
    // Write a list describing the directory structure, recursive, discriminates for .md files.

    $i = '<ul class="dir">';
    if (is_dir($d)) {
        if ($dh = opendir($d)) {
            while (($f = readdir($dh)) !== false) {
                if (substr($f, 0, 1) != '.') {
                    $fpath = "$d/$f";
                    if (filetype($fpath) == 'dir') {
                        $i .= "<li><a href=\"$relpath/$f/\">" . str_replace('_', ' ', $f)
                        .  "</a>" . index($fpath, "$relpath/$f") . '</li>';
                    } else {
                        if ($f != 'index.md' && '.md' == substr($f, -3) && 'Site_map.md' != $f) {
                            $fname = pathinfo($fpath, PATHINFO_FILENAME);
                            $i .= "<li><a href=\"$relpath/$fname.md\">" . str_replace('_', ' ', $fname) . "</a></li>";
                        }
                    }
                }
            }
            closedir($dh);
        }
    }
    $i .= '</ul>';
    return $i;
}


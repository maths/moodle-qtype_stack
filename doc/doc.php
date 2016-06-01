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
 * @package qtype_stack
 * @author Ben Holmes
 * @copyright  2012 The University of Birmingham
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../../config.php');
require_once(__DIR__ . '/docslib.php');

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

$docsroot = $CFG->dirroot . '/question/type/stack/doc/' . current_language();
// Default to English when docs are missing.
if (!file_exists($docsroot.'/index.md')) {
    $docsroot = $CFG->dirroot . '/question/type/stack/doc/en';
}


$docsurl = $CFG->wwwroot . '/question/type/stack/doc/doc.php';

// The URL to the directory for static content to be served by the docs
// access this string in the docs with %CONTENT.
$docscontent = $CFG->wwwroot . '/question/type/stack/doc/content';

$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url('/question/type/stack/doc/doc.php');
$PAGE->set_title(stack_string('stackDoc_docs'));

if (substr($_SERVER['REQUEST_URI'], -7) == 'doc.php') {
    // Don't access doc.php directly, treat it like a directory instead.
    $uri = '/';
} else {
    $uri = get_file_argument();
}
$segs    = explode('/', $uri);
$lastseg = $segs[count($segs) - 1];

// Links for the end of the page.
if ($uri == '/') {
    // The docs front page at .../doc.php/.
    $linkurls = array(
        $docsurl . '/Site_map' => stack_string('stackDoc_siteMap')
    );

} else if ('/Site_map' == $uri) {
    $linkurls = array(
        $docsurl               => stack_string('stackDoc_home')
    );

} else {
    $linkurls = array(
        $docsurl               => stack_string('stackDoc_home'),
        './'                   => stack_string('stackDoc_index'),
        '../'                  => stack_string('stackDoc_parent'),
        $docsurl . '/Site_map' => stack_string('stackDoc_siteMap')
    );
}


$links = array();
foreach ($linkurls as $url => $link) {
    $links[] = '<a href="' . $url . '">' . $link . '</a>';
}
$links = implode(' | ', $links);

if ('Site_map' == $lastseg) {
    $body = stack_docs_site_map($links, $docsroot, $docsurl);

} else {
    if ('' == $lastseg) {
        $file = $docsroot . $uri . 'index.md';
    } else {
        $file = $docsroot . $uri;
    }

    if (file_exists($file)) {
        $body = stack_docs_page($links, $file, $docscontent);

    } else {
        $body = stack_docs_no_found($links);
    }
}

/* Add the version number to the front page.  */
if ($uri == '/') {
    $settings = get_config('qtype_stack');
    $body .= '<br/>'.stack_string('stackDoc_version', $settings->version);
}


$webpix  = $CFG->wwwroot . '/question/type/stack/pix/logo-sm.png';
$pagetitle = '<img src="' . $CFG->wwwroot . '/question/type/stack/pix/logo-sm.png" style="margin-right: 10px;" />' .
        stack_string('stackDoc_docs');

echo $OUTPUT->header();
echo $OUTPUT->heading($pagetitle);
echo $body;
echo $OUTPUT->footer();

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
 * Documentation system as a static wiki of markdown.
 *
 * @package qtype_stack
 * @author Ben Holmes
 * @copyright  2012 The University of Birmingham
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// The docs should be public and not require a login, so we ignore the fact we load config here without a login check.
// @codingStandardsIgnoreStart
require_once(__DIR__ . '/../../../../config.php');
// @codingStandardsIgnoreEnd
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
$docsrooten = $CFG->dirroot . '/question/type/stack/doc/en';
if (!file_exists($docsroot.'/index.md')) {
    $docsroot = $CFG->dirroot . '/question/type/stack/doc/en';
}

$docsurl = $CFG->wwwroot . '/question/type/stack/doc/doc.php';

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
    $linkurls = array();
    $sitemapurl = '<a href = "' . $docsurl . '/Site_map' .'">'
        . stack_string('stackDoc_siteMap') . '</a>';

} else if ('/Site_map' == $uri) {
    $linkurls = array(
        $docsurl               => stack_string('stackDoc_home')
    );

} else {
    $linkurls = array(
        $docsurl               => stack_string('stackDoc_home'),
        './'                   => stack_string('stackDoc_index'),
        $docsurl . '/Site_map' => stack_string('stackDoc_siteMap')
    );
    if (current_language() != 'en') {
        $linkurls[$docsurl . '/Site_map_en'] = stack_string('stackDoc_siteMap_en');
    }
}

$links = array();
foreach ($linkurls as $url => $link) {
    $links[] = '<a href="' . $url . '">' . $link . '</a>';
}
$links = implode(' | ', $links);

if ('Site_map' == $lastseg) {
    $body = stack_docs_site_map($links, $docsroot, $docsurl);
    $meta = stack_docs_page_metadata('Site_map.md');
} else if ('Site_map_en' == $lastseg) {
        $body = stack_docs_site_map($links, $docsrooten, $docsurl);
        $meta = stack_docs_page_metadata('Site_map.md');
} else {
    if ('' == $lastseg) {
        $file = $docsroot . $uri . 'index.md';
        $fileen = $docsrooten . $uri . 'index.md';
    } else {
        $file = $docsroot . $uri;
        $fileen = $docsrooten . $uri;
    }

    if (file_exists($file)) {
        $body = stack_docs_page($links, $file);
        $meta = stack_docs_page_metadata($uri);
    } else if (file_exists($fileen)) {
        // Default to English.
        $body = stack_docs_page($links, $fileen);
        $meta = stack_docs_page_metadata($uri);
    } else {
        $body = stack_docs_no_found($links);
        $meta = array();
    }
}

if (array_key_exists('title', $meta)) {
    $PAGE->set_title($meta['title']);
}

/* Add the version number and logos to the front page.  */
if ($uri == '/') {
    $webpix1  = $CFG->wwwroot . '/question/type/stack/doc/content/logo.png';
    $webpix2  = $CFG->wwwroot . '/question/type/stack/doc/content/CATE.png';
    $body = $sitemapurl . '<br />'
        . '<img src="' . $webpix1 . '" width=200 />'
        . '<img src="' . $webpix2 . '" width=140 style="margin-left: 45px;"/>' . $body;

    $settings = get_config('qtype_stack');
    $libs = array_map('trim', explode(',', $settings->maximalibraries));
    asort($libs);
    $libs = implode(', ', $libs);
    $vstr = $settings->version . ' (' . $libs . ')';
    $body .= '<br/>'.stack_string('stackDoc_version', $vstr);
}

$webpix  = $CFG->wwwroot . '/question/type/stack/pix/logo-sm.png';
$pagetitle = '<img src="' . $webpix . '" style="margin-right: 15px;" />' .
        stack_string('stackDoc_docs');

$header = $OUTPUT->header() . $OUTPUT->heading($pagetitle);
if (array_key_exists('description', $meta)) {
    // Splice in the description at the end of the header.
    $description = $meta['description'];
    $description = '<meta name="description" content="' . $description . '"/>' . "\n";
    $cut = strpos($header, '</head>');
    $header = substr($header, 0, $cut) . $description . substr($header, $cut);
}
echo $header;
echo $body;
echo $OUTPUT->footer();

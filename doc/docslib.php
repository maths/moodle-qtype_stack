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

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once(__DIR__ . '/../locallib.php');
require_once(__DIR__ . '/../stack/utils.class.php');
require_once(__DIR__ . '/../stack/mathsoutput/fact_sheets.class.php');


/**
 * Display the STACK documentation index for a particular path.
 * @param string $dir directory.
 * @param string $relpath relative path to that directory.
 */
function stack_docs_index($dir, $relpath = '') {
    // Write a list describing the directory structure, recursive, discriminates for .md files.
    $exclude = ['index.md', 'Site_map.md'];
    $details = ['AbInitio',
                'Results',
                'Developer',
                'Reference',
                'Installation',
                'Proof',
                'Drag and drop',
                'Equivalence reasoning',
                'GeoGebra',
                'JSXGraph',
    ];

    if (!is_dir($dir)) {
        return '';
    }

    $items = [];
    foreach (glob($dir . '/*') as $filepath) {
        $filename = basename($filepath);

        if (substr($filename, 0, 1) === '.' || in_array($filename, $exclude)) {
            continue;
        }

        $title = stack_docs_title_from_filename($filename);
        if (is_dir($filepath)) {
            if (in_array($title, $details)) {
                // I'd like to make more of the details/summary tag but behat testing breaks as it can't find links.
                $items[$title] = "<li><details>" .
                    "<summary><a id=\"" . $title . "\" href=\"$relpath/$filename/\">" . $title . "</a></summary>" .
                    stack_docs_index($filepath, "$relpath/$filename") . "</details></li>";
            } else {
                $items[$title] = "<li>\n" .
                    "<a id=\"" . $title . "\" href=\"$relpath/$filename/\">" . $title . "</a>\n" .
                    stack_docs_index($filepath, "$relpath/$filename") . "</li>";
            }
        } else {
            if (substr($filename, -2) === 'md') {
                $items[$title] = "<li><a href=\"$relpath/$filename\">" . $title . '</a></li>';
            }
        }
    }

    if (empty($items)) {
        return '';
    }
    stack_utils::sort_array_by_key($items);
    return '<ul class="dir">' . implode('', $items) . '</ul>';
}

/**
 * Convert a file-name to a nice title.
 * @param string $filename a filename.
 * @return string the corresponding title.
 */
function stack_docs_title_from_filename($filename) {
    return str_replace(['_', '.md'], [' ', ''], $filename);
}

/**
 * Generate the documentation site-map.
 * @param string $links menu of links to show.
 * @param string $docsroot file path of the root of the docs.
 * @param string $docsurl base URL for the docs.
 * @return string HTML page body.
 */
function stack_docs_site_map($links, $docsroot, $docsurl) {
    $body = '';
    $body .= $links;
    $body .= '<h3>' .  stack_string('stackDoc_directoryStructure', 'stack') . '</h3>';
    $body .= stack_docs_index($docsroot, $docsurl);
    return $body;
}

/**
 * Generate an error page when missing docs are referred to.
 * @param string $links menu of links to show.
 * @return string HTML page body.
 */
function stack_docs_no_found($links) {
    $body = '';
    $body .= html_writer::tag('h1', stack_string('stackDoc_404'));
    $body .= html_writer::tag('p', stack_string('stackDoc_404message'));
    $body .= $links;
    return $body;
}

/**
 * Generate a page of the documentation from the source in a file.
 * @param string $links menu of links to show.
 * @param string $file path to the file to display.
 * @return string HTML page body.
 */
function stack_docs_page($links, $file) {
    $preprocess = true;
    // This auto-generated file does not need maths processing.
    if (strpos($file, 'Answer_Tests/Results') !== false) {
        $preprocess = false;
    }
    $body = '';
    $body .= $links;
    $body .= "\n<hr/>\n";
    $body .= stack_docs_render_markdown(file_get_contents($file), $preprocess);
    $body .= "\n<hr/>\n";
    $body .= $links;
    return $body;
}

/**
 * Process the markdown into html, respecting \ for maths, code in markdown (with \), and links to video.
 * @param string $page countent in Markdown format.
 * @param boolean $preprocess Do we need to process the maths in this page?.
 * @return string HTML content.
 */
function stack_docs_render_markdown($page, $preprocess = true) {

    if ($preprocess) {
        // Don't process the auto-generated answer test output.
        $page = stack_maths::pre_process_docs_page($page);
    }

    // Note the 'noclean' option is normally not permitted, however, this call to format_text is
    // only applied to fixed content stored in the STACK git repository as code, and not user-generated content.
    $page = format_text($page, FORMAT_MARKDOWN, ['filter' => false, 'noclean' => true]);
    $page = stack_maths::post_process_docs_page($page);
    // The 'filter' => true is to ensure we activate the mathjax filter once the markdown has changed to html.
    $page = format_text($page, FORMAT_HTML, ['filter' => true, 'noclean' => true]);
    return $page;
}

/**
 * @param string $url Docs page being considered.
 * @return array Metadata content.
 */
function stack_docs_page_metadata($uri) {

    $metafile = file_get_contents("meta_en.json");
    $meta = json_decode($metafile, true);
    if ($meta == []) {
        throw new stack_exception('STACK docs: the metadata json file is broken!');
    }
    // TO-DO: langauges.
    $meta = $meta['en'];

    // Sort out what we are looking for.
    $file = explode('/', substr(trim($uri), 0));
    $endfile = 'index.md';
    $pathtofile = [];
    foreach ($file as $f) {
        $f = trim($f);
        if ($f !== '') {
            if (substr($f, -3) == '.md') {
                $endfile = $f;
            } else {
                $pathtofile[] = $f;
            }
        }
    }

    foreach ($pathtofile as $key) {
        foreach ($meta as $ml) {
            if (array_key_exists($key, $ml)) {
                // We are at the directory level, so go down the tree.
                $meta = $ml[$key];
            }
        }
    }

    $metadata = [];
    foreach ($meta as $dat) {
        if (array_key_exists('file', $dat) && trim($dat['file']) == $endfile) {
            $metadata = $dat;
        }
    }
    return $metadata;
}

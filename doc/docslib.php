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

    // Admonitions first, so that their bodies are processed exactly as any other text.
    $page = stack_docs_admonitions($page);

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
 * Convert mkdocs admonitions into HTML which Markdown Extra will process.
 *
 * The docs site (mkdocs) supports admonitions: a line such as `!!! warning "Title"`
 * followed by a body indented by four spaces.  `???` gives a block which starts
 * collapsed, and `???+` one which starts open.  Markdown Extra does not know this
 * syntax, so the copy of the docs inside Moodle would show the body as code.  Here
 * each admonition becomes a Bootstrap alert with `markdown="1"`, so its body is
 * still Markdown.  As in mkdocs, a missing title gives the type as the title, and
 * an empty title `""` gives no title.  Fenced code blocks are left alone.
 *
 * @param string $page content in Markdown format.
 * @return string the same content, with admonitions replaced by HTML.
 */
function stack_docs_admonitions($page) {
    $alerts = [
        'note' => 'alert-info', 'info' => 'alert-info', 'abstract' => 'alert-info', 'question' => 'alert-info',
        'tip' => 'alert-success', 'success' => 'alert-success', 'hint' => 'alert-success',
        'warning' => 'alert-warning', 'caution' => 'alert-warning', 'attention' => 'alert-warning',
        'danger' => 'alert-danger', 'error' => 'alert-danger', 'failure' => 'alert-danger', 'bug' => 'alert-danger',
    ];
    $header = '~^(!!!|\?\?\?\+?)[ \t]+([A-Za-z][\w-]*)((?:[ \t]+[A-Za-z][\w-]*)*)(?:[ \t]+"([^"]*)")?[ \t]*$~';

    $lines = explode("\n", str_replace("\r\n", "\n", $page));
    $out = [];
    $fence = '';
    $n = count($lines);
    for ($i = 0; $i < $n; $i++) {
        $line = $lines[$i];
        // Track fenced code blocks, so that an example of the syntax inside one is not converted.
        // Note \x60 is a backtick, written this way because Moodle's code checker warns about backticks in strings.
        if (preg_match('/^[ \t]*(\x60{3,}|~{3,})/', $line, $m)) {
            if ($fence === '') {
                $fence = $m[1];
            } else if (strpos($m[1], $fence) === 0) {
                $fence = '';
            }
        }
        if ($fence !== '' || !preg_match($header, $line, $m)) {
            $out[] = $line;
            continue;
        }

        // The body is every following line which is blank or indented, less any trailing blank lines.
        $body = [];
        while ($i + 1 < $n && preg_match('~^(?:[ \t]*$|    |\t)~', $lines[$i + 1])) {
            $body[] = preg_replace('~^(?:    |\t)~', '', $lines[++$i]);
        }
        while ($body && trim(end($body)) === '') {
            array_pop($body);
            $i--;
        }
        // Admonitions may be nested.
        $body = stack_docs_admonitions(implode("\n", $body));

        // The type is not used as a bare class name: Moodle styles some, e.g. `.warning` is bold.
        // Any further classes after the type (mkdocs layout options such as `inline`) are ignored.
        $type = strtolower($m[2]);
        $classes = 'stack-docs-admonition admonition admonition-' . $type . ' alert ' . ($alerts[$type] ?? 'alert-secondary');
        $title = isset($m[4]) ? $m[4] : ucfirst($type);

        $out[] = '';
        if ($m[1] === '!!!') {
            $out[] = '<div class="' . $classes . '" markdown="1">';
            if ($title !== '') {
                $out[] = '<p class="admonition-title" markdown="span"><strong>' . $title . '</strong></p>';
            }
            $closing = '</div>';
        } else {
            $open = ($m[1] === '???+') ? ' open' : '';
            $title = ($title === '') ? ucfirst($type) : $title;
            $out[] = '<details class="' . $classes . '"' . $open . ' markdown="1">';
            $out[] = '<summary markdown="span"><strong>' . $title . '</strong></summary>';
            $closing = '</details>';
        }
        $out[] = '';
        $out[] = $body;
        $out[] = '';
        $out[] = $closing;
        $out[] = '';
    }
    return implode("\n", $out);
}

/**
 * Look up the metadata for a docs page.
 * @param string $uri Docs page being considered, relative to the docs root, e.g. "/CAS/Matrix.md".
 * @return array Metadata content, which is empty if there is no metadata for this page.
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
        $found = false;
        foreach ($meta as $ml) {
            if (array_key_exists($key, $ml)) {
                // We are at the directory level, so go down the tree.
                $meta = $ml[$key];
                $found = true;
                break;
            }
        }
        if (!$found) {
            // No metadata for this directory. Don't fall back to a page of the same name in a parent directory.
            return [];
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

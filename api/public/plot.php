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
 * This script serves plot files that have been saved in the moodledata folder.
 *
 * @package    qtype_stack
 * @copyright  2012 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// @codingStandardsIgnoreStart
// ISS1211 - Removed login requirement for App compatibility.
require_once('../config.php');
// @codingStandardsIgnoreEnd

header('Access-Control-Allow-Origin: ' . ($CFG->corsorigin ?? '*'));
header('Access-Control-Allow-Headers: *');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('HTTP/1.0 204 OK');
    header('Access-Control-Allow-Methods: GET');
    die();
}

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$filename = urldecode(basename($path));
$plot = $CFG->dataroot . '/stack/plots/' . $filename;

if (!is_readable($plot)) {
    header('HTTP/1.0 404 Not Found');
    header('Content-Type: text/plain;charset=UTF-8');
    echo 'File not found';
    die();
}

// Handle If-Modified-Since.
$filedate = filemtime($plot);
if (
    isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) &&
        strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) >= $filedate
) {
    header('HTTP/1.0 304 Not Modified');
    die();
}
header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $filedate) . ' GMT');

// Type.
header('Content-Type: ' . get_plot_mimetype($plot));
header('Content-Length: ' . filesize($plot));

// Output file.
readfile($plot);

/**
 * Returns a MIME type for a served plot or static asset.
 *
 * The API may serve generated plots and static question assets, so do not
 * restrict by extension here.
 *
 * @param string $path Path to the readable file.
 * @return string MIME type for the response.
 */
function get_plot_mimetype($path) {
    $filetype = strtolower(pathinfo($path, PATHINFO_EXTENSION));
    $mimetypes = [
        'css' => 'text/css',
        'gif' => 'image/gif',
        'html' => 'text/html',
        'jpeg' => 'image/jpeg',
        'jpg' => 'image/jpeg',
        'js' => 'text/javascript',
        'json' => 'application/json',
        'pdf' => 'application/pdf',
        'png' => 'image/png',
        'svg' => 'image/svg+xml',
        'txt' => 'text/plain',
        'webp' => 'image/webp',
        'xml' => 'text/xml',
    ];

    if (array_key_exists($filetype, $mimetypes)) {
        return $mimetypes[$filetype];
    }

    if (function_exists('mime_content_type')) {
        $mimetype = mime_content_type($path);
        if ($mimetype !== false) {
            return $mimetype;
        }
    }

    return 'application/octet-stream';
}

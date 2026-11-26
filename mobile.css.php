<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Serves the STACK mobile stylesheet with absolute image URLs.
 *
 * @package qtype_stack
 * @copyright 2026 The STACK Authors
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('NO_MOODLE_COOKIES', true);

require_once(__DIR__ . '/../../../config.php');

header('Content-Type: text/css; charset=utf-8');

$css = file_get_contents(__DIR__ . '/mobile.css');
if ($css === false) {
    http_response_code(500);
    exit;
}

$pixurl = (new moodle_url('/question/type/stack/pix/'))->out(false);
echo str_replace('../pix/', $pixurl, $css);

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

// This script handles the various deploy/undeploy actions from questiontestrun.php.
//
// @copyright  2023 RWTH Aachen
// @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.

// Copied from moodle source code.

// Constants.

// Define text formatting types ... eventually we can add Wiki, BBcode etc.

/**
 * Does all sorts of transformations and filtering.
 */
define('FORMAT_MOODLE',   '0');

/**
 * Plain HTML (with some tags stripped).
 */
define('FORMAT_HTML',     '1');

/**
 * Plain text (even tags are printed in full).
 */
define('FORMAT_PLAIN',    '2');

/**
 * Wiki-formatted text.
 * Deprecated: left here just to note that '3' is not used (at the moment)
 * and to catch any latent wiki-like text (which generates an error)
 * @deprecated since 2005!
 */
define('FORMAT_WIKI',     '3');

/**
 * Markdown-formatted text http://daringfireball.net/projects/markdown/
 */
define('FORMAT_MARKDOWN', '4');

/**
 * A moodle_url comparison using this flag will return true if the base URLs match, params are ignored.
 */
define('URL_MATCH_BASE', 0);

/**
 * A moodle_url comparison using this flag will return true if the base URLs match and the params of url1 are part of url2.
 */
define('URL_MATCH_PARAMS', 1);

/**
 * A moodle_url comparison using this flag will return true if the two URLs are identical, except for the order of the params.
 */
define('URL_MATCH_EXACT', 2);

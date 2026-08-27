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
 * Install language packs during Docker build.
 *
 * @package    qtype_stack
 * @copyright  2026 University of Edinburgh
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

define('CLI_SCRIPT', true);
require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/../../emulation/Language.php');

global $CFG;
$supportedlanguages = explode(',', $CFG->supportedlanguages);
foreach ($supportedlanguages as $variant) {
    if (!in_array($variant, ['*', 'en'])) {
        ApiLanguage::install_language($variant);
        $region = ApiLanguage::get_next_parent_language($variant);
        if ($region !== $variant && !in_array($region, $supportedlanguages)) {
            ApiLanguage::install_language($region);
        }
        $language = ApiLanguage::get_next_parent_language($region);
        if ($language !== $region && !in_array($language, $supportedlanguages)) {
            ApiLanguage::install_language($language);
        }
    }
}

// Update German translations if required and not done already.
if (in_array('*', $supportedlanguages) && !in_array('de', $supportedlanguages)) {
    ApiLanguage::install_language('de');
}

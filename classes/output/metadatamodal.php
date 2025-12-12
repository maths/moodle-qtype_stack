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
 * Contains class mod_nosferatu\output\reportlink
 *
 * @package   mod_nosferatu
 * @copyright 2020 Ferran Recio
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace qtype_stack\output;

defined('MOODLE_INTERNAL') || die();

use qtype_stack\metadatamanager;
use renderable;
use templatable;
use renderer_base;
use stdClass;

/**
 * Class to help display report link in mod_nosferatu.
 *
 * @copyright 2020 Ferran Recio
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class metadatamodal implements renderable, templatable {


    /**
     * Constructor.
     *
     */
    public function __construct() {
        global $PAGE;
        $PAGE->requires->js_call_amd('qtype_stack/metadata/metadatamodal', 'init');
    }

}
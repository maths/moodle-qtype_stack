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

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../../maximaparser/utils.php');

/**
 * This is a simple key value store that will simply store some sensitive strings
 * and place them back into the evaluated castext.
 *
 * This is used to mark such generated HTML or other sensitive content that must not
 * be allowed to go through VLE (Moodle)-filters and in particular not through
 * the forceclean logic of issue #1252
 *
 * Unlike `castext2_static_replacer` the map inside of this is not something we store,
 * it is something that comes from other compilation results during execution and
 * exists only over the filtering phase.
 */
class castext2_placeholder_holder {
    private $map;

    public function __construct() {
        $this->map = [];
    }

    /**
     * Returns the protected values on top of the placeholders.
     */
    public function replace(string $in): string {
        $out = $in;
        foreach ($this->map as $key => $value) {
            if (mb_strpos($out, $key) !== false) {
                $out = str_replace($key, $value, $out);
            }
        }
        return $out;
    }

    /**
     * Adds a string to the map and returns the replacement placeholder.
     */
    public function add_to_map(string $value): string {
        $key = array_search($value, $this->map);
        if ($key === false) {
            // Note that the count is unique for only the matching segment of CASText.
            // One must never merge postprocessed CASText before the values have been
            // returned in place. Basically, `format_text` immediately after
            // postprocessing and replacement immediately after `format_text`.
            $k = count($this->map) + 1;
            $key = "[[placeholder:$k]]"; // Assume that this is never present in normal content.
            // For this placeholder we match the input, validation and feedback syntax
            // as that syntax seems to survive `format_text` if it ever breaks this too needs
            // to be fixed.
            $this->map[$key] = $value;
        }
        return $key;
    }
}

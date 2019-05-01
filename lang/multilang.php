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
 * Class for dealing with mult-language support within the Stack question type.
 * This is now needed here so that the internal validation, and the API classes can deal with languages.
 * Much of this code is copied from Moodle's filter/multilang.php class.
 * @package    qtype_stack
 * @copyright  2018 The University of Edinburgh
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class stack_multilang {

    /*
     * Variable to hold the current language.
     */
    private $lang = 'en';

    // Note, we only support the new style language tags.  For more information see Moodle's filter/multilang.php class.
    private $search = '/(<span(\s+lang="[a-zA-Z0-9_-]+"|\s+class="multilang"){2}\s*>.*?<\/span>)' .
            '(\s*<span(\s+lang="[a-zA-Z0-9_-]+"|\s+class="multilang"){2}\s*>.*?<\/span>)+/is';

    private $searchtosplit = '/<(?:lang|span)[^>]+lang="([a-zA-Z0-9_-]+)"[^>]*>(.*?)<\/(?:lang|span)>/is';

    /*
     * Filter text for the specified language.
     */
    public function filter($text, $lang) {
        if (empty($text) or is_numeric($text)) {
            return $text;
        }

        $this->lang = $lang;

        $result = preg_replace_callback($this->search, array($this, 'filter_multilang_impl'), $text);

        if (is_null($result)) {
            return $text; // Error during regex processing: too many nested spans?
        } else {
            return $result;
        }
    }

    private function filter_multilang_impl($langblock) {

        $mylang = $this->lang;
        $parentlang = 'en';

        if (!preg_match_all($this->searchtosplit, $langblock[0], $rawlanglist)) {
            // Skip malformed blocks.
            return $langblock[0];
        }

        $langlist = array();
        foreach ($rawlanglist[1] as $index => $lang) {
            $lang = str_replace('-', '_', strtolower($lang)); // Normalize languages.
            $langlist[$lang] = $rawlanglist[2][$index];
        }

        if (array_key_exists($mylang, $langlist)) {
            return $langlist[$mylang];
        } else if (array_key_exists($parentlang, $langlist)) {
            return $langlist[$parentlang];
        } else {
            $first = array_shift($langlist);
            return $first;
        }
    }

    /*
     * Return those languages _explicitly_ found in the text.
     */
    public function languages_used($langblock) {

        if (!preg_match_all($this->searchtosplit, $langblock, $rawlanglist)) {
            // Skip malformed blocks.
            return array();
        }

        $langlist = array();
        foreach ($rawlanglist[1] as $index => $lang) {
            $lang = str_replace('-', '_', strtolower($lang)); // Normalize languages.
            $langlist[$lang] = $rawlanglist[2][$index];
        }

        return array_keys($langlist);
    }


    /*
     * Consolidate all the text inside langage blocks.
     */
    public function consolidate_languages($text) {
        $langs = $this->languages_used($text);
        if ($langs == array()) {
            return $text;
        }
        $filtered = array();
        foreach ($langs as $lang) {
            $filtered[$lang] = '<span lang="' . $lang . '" class="multilang">' . $this->filter($text, $lang) . '</span>';
        }
        return implode("\n", $filtered);
    }
}

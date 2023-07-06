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
//
defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../block.interface.php');
require_once(__DIR__ . '/../utils.php');

/**
 * This is a block allows one to share content between questions, it
 * allows one to include CASText2 fragments into CASText2. For example,
 * one could have a finely tuned generic JSXGraph plotting logic stored
 * somewhere and simply include it with WYSIWYG-safe block notation:
 *
 *  [[include src="http://example.com/fragments/myplot.txt"/]]
 *
 * Note that this inclusion does not update automatically when the source
 * updates, the value is included only at the time the question gets
 * compiled i.e. during the first use after saving or cache clear. Make
 * sure that the source is accesible then, if it is not things will break.
 * Cache will always get cleared when one updates STACK.
 *
 * We expect that the server serves the file out as text and that it is
 * encoded in UTF-8 if it has anything interesting, if one needs other
 * encodings one can tune the default encoding of the server executing
 * this code to expect something else.
 */
class stack_cas_castext2_include extends stack_cas_castext2_block {

    // Avoid retrieving the same file multiple times during the same request.
    private static $extcache = [];

    private static function file_get_contents($url) {
        if (isset(self::$extcache[$url])) {
            return self::$extcache[$url];
        }
        self::$extcache[$url] = file_get_contents($url);
        return self::$extcache[$url];
    }

    public function compile($format, $options): ?MP_Node {
        $src = self::file_get_contents($this->params['src']);
        if (isset($options['in include'])) {
            // We will need to rethink the validate_extract_attributes()-logic
            // to extract casstrings from nested inclusions. Also loops...
            throw new stack_exception('CASText2 inclusions within inclusions are not currently supportted, ' .
                'due to security validation logic: ' . $this->params['src']);
        }
        if ($src === false) {
            throw new stack_exception('Include block source not accessible: ' . $this->params['src']);
        }
        // Ok we have the source, we will simply compile it.
        // And finally return it as the content matching this block.
        $opt2 = [];
        if ($options !== null) {
            $opt2 = array_merge([], $options);
        }
        $opt2['in include'] = true;
        return castext2_parser_utils::compile($src, $format, $opt2);
    }

    public function is_flat(): bool {
        return false;
    }

    public function validate_extract_attributes(): array {
        // This is tricky, we need to validate the attributes of the included content.
        // To do that we need to retrieve it and process it again, luckily this gets cached.
        $src = self::file_get_contents($this->params['src']);
        if ($src === false) {
            throw new stack_exception('Include block source not accessible: ' . $this->params['src']);
        }
        // Ok we have the source, we will simply compile it, again.
        return castext2_parser_utils::get_casstrings($src);
    }

    public function validate(&$errors=[], $options=[]): bool {
        if (!array_key_exists('src', $this->params)) {
            $errors[] = new $options['errclass']('Include block requires a src parameter.', $options['context'] . '/' .
                $this->position['start'] . '-' . $this->position['end']);
            return false;
        }

        return true;
    }
}

<?php
// This file is part of Stateful
//
// Stateful is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Stateful is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Stateful.  If not, see <http://www.gnu.org/licenses/>.

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../../maximaparser/utils.php');

/**
 * This is a simple key value store that will simply store the shared static strings
 * and place them back into the evalauted castext.
 *
 * The reason for this things existence is pretty much MecLib, when it has that large
 * portion of code that does not directly have a role in CAS we have no reason to send
 * that portion to CAS. So we pick it out of the compiled castext and replace it into
 * it after it returns from the CAS.
 */
class castext2_static_replacer {
    private $map;

    public function __construct(array $map) {
        $this->map = $map;
    }

    // One might need to gain access to this after extraction.
    public function get_map(): array {
        return $this->map;
    }

    public function replace($in) {
        // This might be called with a complete string.
        if (is_string($in)) {
            $out = $in;
            foreach ($this->map as $key => $value) {
                if (mb_strpos($out, $key) !== false) {
                    $out = str_replace($key, $value, $out);
                }
            }
            return $out;
        }
        // Or a parsed array form.
        foreach ($in as $k => $v) {
            if (is_array($v)) {
                $in[$k] = self::replace($v);
            } else if (is_string($v)) {
                $out = $v;
                foreach ($this->map as $key => $value) {
                    if (mb_strpos($out, $key) !== false) {
                        $out = str_replace($key, $value, $out);
                    }
                }
                $in[$k] = $out;
            }
        }
        return $in;
    }

    public function extract(string $in): string {
        // If the castext is already a static string don't do anything.
        if (mb_substr($in, 0, 1) === '"') {
            return $in;
        }

        // Note that the compiler has already done string concatenation for us
        // so we do not need to do that.

        // We need to again parse the compiled CASText, not ideal but wiring this
        // all the way to the compiler would be difficult. And this is something
        // that will be cached so who cares.
        $ast = maxima_parser_utils::parse($in);

        $map = $this->map;
        $collector = function($node) use(&$map) {
            // Yes that array_search = false case is something but it does not apply here.
            if ($node instanceof MP_String && $node->parentnode instanceof MP_List &&
                    array_search($node, $node->parentnode->items) > 0 && mb_strlen($node->value) > 10) {
                // Ensure that the list is a CASText2 thing.
                if ($node->parentnode->items[0] instanceof MP_String && (
                    $node->parentnode->items[0]->value === '%root' ||
                    $node->parentnode->items[0]->value === '%cs' ||
                    $node->parentnode->items[0]->value === 'demarkdown' ||
                    $node->parentnode->items[0]->value === 'demoodle' ||
                    ($node->parentnode->items[0]->value === 'jsxgraph' &&
                            array_search($node, $node->parentnode->items) > 1)
                    )) {
                    // Do we already have this string value?
                    $key = array_search($node->value, $map);
                    if ($key === false) {
                        $k = count($map);
                        $key = "//CT2S$k//"; // Assume that this is never present in normal content.
                        $map[$key] = $node->value;
                    }
                    $node->value = $key;
                }
            }
            return true;
        };
        $ast->callbackRecurse($collector);
        $this->map = $map;
        return $ast->toString(['nosemicolon' => true]);
    }

    /**
     * Adds a string to the map and returns the replacement placeholder.
     */
    public function add_to_map(string $value): string {
        $key = array_search($value, $this->map);
        if ($key === false) {
            $k = count($this->map);
            $key = "//CT2S$k//"; // Assume that this is never present in normal content.
            $this->map[$key] = $value;
        }
        return $key;
    }
}

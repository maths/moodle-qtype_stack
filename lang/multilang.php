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

require_once(__DIR__ . '/../stack/cas/castext2/utils.php');

class stack_multilang {

    /*
     * Variable to hold the current language.
     */
    private $lang = 'en';

    /**
     * @var array Cache of parent language(s) of a given language
     */
    protected static $parentcache = [];

    // Note, we only support the new style language tags.  For more information see Moodle's filter/multilang.php class.
    private $search = '/(<span(\s+lang="[a-zA-Z0-9_-]+"|\s+class="multilang"){2}\s*>.*?<\/span>)+/is';

    private $searchtosplit = '/<(?:lang|span)[^>]+lang="([a-zA-Z0-9_-]+)"[^>]*>(.*?)<\/(?:lang|span)>/is';

    // The search pattern as seen in filter/multilang2.
    private $search2 =
                  '/{\s*mlang\s+(                               # Look for the leading {mlang
                                    (?:[a-z0-9_-]+)             # At least one language must be present
                                                                # (but dont capture it individually).
                                    (?:\s*,\s*[a-z0-9_-]+\s*)*  # More can follow, separated by commas
                                                                # (again dont capture them individually).
                                )\s*}                           # Capture the language list as a single capture.
                   (.*?)                                        # Now capture the text to be filtered.
                   {\s*mlang\s*}                                # And look for the trailing {mlang}.
                   /isx';

    /*
     * Filter text for the specified language.
     */
    public function filter($text, $lang) {
        if ($text === null || empty($text) || is_numeric($text)) {
            return $text;
        }

        // Figure out what we are filtering and with what.
        // Note that we are doing repeated parsing, but that is not a problem as this only
        // happens during compiling and validation.
        $mode = $this->identify_tool($text)[0];

        $this->lang = $lang;

        if ($mode === 1) {
            $result = preg_replace_callback($this->search, array($this, 'filter_multilang_impl'), $text);

            if (is_null($result)) {
                return $text; // Error during regex processing: too many nested spans?
            } else {
                return $result;
            }
        } else if ($mode === 2) {
            $this->replacementdone = false;
            if (!array_key_exists($lang, self::$parentcache)) {
                $parentlangs = get_string_manager()->get_language_dependencies($lang);
                self::$parentcache[$lang] = $parentlangs;
            }
            $result = preg_replace_callback($this->search2,
                function ($matches) {
                    return $this->filter_multilang2_impl($matches);
                }, $text);
            if ($this->replacementdone) {
                return $result;
            }
            $this->lang = 'other';
            $result = preg_replace_callback($this->search2,
                function ($matches) {
                    return $this->filter_multilang2_impl($matches);
                }, $text);

            if (is_null($result)) {
                return $text;
            }
            return $result;
        } else if ($mode === 3) {
            $parsed = castext2_parser_utils::parse($text, castext2_parser_utils::RAWFORMAT);
            $search = function ($node) use (&$parsed) {
                if ($node instanceof CTP_Block && $node->name === 'lang' && isset($node->parameters['code'])) {
                    $codes = explode(',', $node->parameters['code']->value);
                    $good = false;
                    foreach ($codes as $code) {
                        // Normalise codes like the others.
                        $c = str_replace('-', '_', strtolower(trim($code)));
                        if ($c === $this->lang) {
                            $good = true;
                            break;
                        }
                    }
                    if ($good) {
                        foreach ($node->getChildren() as $child) {
                            $node->parent->insertChild($child, $node);
                        }
                    }
                    $node->parent->removeChild($node);
                    return false;
                }
                return true;
            };
            // @codingStandardsIgnoreStart
            while ($parsed->callbackRecurse($search) !== true) {}
            // @codingStandardsIgnoreEnd
            return $parsed->toString();
        }
        return $text;
    }

    private function filter_multilang_impl($langblock) {

        $mylang = $this->lang;
        $parentlang = 'en';

        // If nto lang is defined like in some tests assume lang to be $parentlang.
        if ($mylang === null) {
            $mylang = $parentlang;
        }

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

    private function filter_multilang2_impl($langblock) {
        // Note that this is pretty much perfect clone of the original.
        $blocklangs = explode(',', str_replace(' ', '', str_replace('-', '_', strtolower($langblock[1]))));
        $blocktext = $langblock[2];
        $parentlangs = self::$parentcache[$this->lang];
        foreach ($blocklangs as $blocklang) {
            if (($blocklang === $this->lang) || in_array($blocklang, $parentlangs)) {
                $this->replacementdone = true;
                return $blocktext;
            }
        }

        return '';
    }

    /*
     * Return those languages _explicitly_ found in the text.
     */
    public function languages_used($langblock) {
        $tmp = $this->identify_tool($langblock);
        return array_keys($tmp[1]);
    }


    /*
     * Consolidate all the text inside langage blocks.
     */
    public function consolidate_languages($text) {
        $tmp = $this->identify_tool($text);
        $langs = $tmp[1];
        $mode = $tmp[0];
        if (count($langs) == 0) {
            return $text;
        }
        $filtered = [];
        foreach ($langs as $lang => $duh) {
            if ($mode === 1) {
                $filtered[$lang] = '<span lang="' . $lang . '" class="multilang">' . $this->filter($text, $lang) . '</span>';
            } else if ($mode === 2) {
                $filtered[$lang] = '{mlang ' . $lang . '}' . $this->filter($text, $lang) . '{mlang}';
            } else if ($mode === 3) {
                $filtered[$lang] = '[[lang code="' . $lang . '"]]' . $this->filter($text, $lang) . '[[/lang]]';
            }
        }
        return implode("\n", $filtered);
    }

    /*
     * Check for non-trivial content.
     */
    public function non_trivial_content_for_check($text) {
        $text = trim(strip_tags($text));
        // TODO: remove all equations.
        if ('' == $text) {
            return false;
        }
        return true;
    }

    /*
     * Identify used filter. We know of a few tools that are used for this.
     * Will return the first matching in this list, we do not deal with mixed ones.
     *  0 for no match
     *  1 for filter/multilang, the one that comes with Moodle
     *  2 for filter/multilang2, the other one that does not come with Moodle
     *  3 for CASText2/lang, the one that is built in to STACK
     *
     * Also returns the languages that were seen as an array where the key is the language.
     * The keys are in order of definition.
     */
    public function identify_tool($text) {
        if ($text === null || trim($text) === '') {
            return [0, []];
        }

        if ((mb_strpos($text, 'span') !== false) && (mb_strpos($text, 'multilang') !== false)) {
            $gotlangs = [];
            $duh = preg_replace_callback($this->search,
                function ($matches) use (&$gotlangs) {
                    $rawlanglist = [];
                    if (!preg_match_all($this->searchtosplit, $matches[0], $rawlanglist)) {
                        return '';
                    }
                    foreach ($rawlanglist[1] as $index => $lang) {
                        $lang = str_replace('-', '_', strtolower(trim($lang)));
                        $gotlangs[$lang] = true;
                    }
                    return '';
                }, $text);

            if (count($gotlangs) > 0) {
                return [1, $gotlangs];
            }
        }
        if ((mb_strpos($text, '{') !== false) && (mb_strpos($text, 'mlang') !== false) && (mb_strpos($text, '}') !== false)) {
            $gotlangs = [];
            $duh = preg_replace_callback($this->search2,
                function ($matches) use (&$gotlangs) {
                    $blocklangs = explode(',', str_replace(' ', '', str_replace('-', '_', strtolower($matches[1]))));
                    foreach ($blocklangs as $code) {
                        $gotlangs[$code] = true;
                    }
                    return '';
                }, $text);

            if (count($gotlangs) > 0) {
                return [2, $gotlangs];
            }
        }
        if ((mb_strpos($text, '[[') !== false) && (mb_strpos($text, 'lang') !== false) && (mb_strpos($text, 'code') !== false)) {
            // Try to parse and check for language blocks.
            $parsed = castext2_parser_utils::parse($text, castext2_parser_utils::RAWFORMAT);
            $gotlangs = [];
            $search = function ($node) use (&$gotlangs) {
                if ($node instanceof CTP_Block && $node->name === 'lang' && isset($node->parameters['code'])) {
                    $codes = explode(',', $node->parameters['code']->value);
                    foreach ($codes as $code) {
                        // Normalise codes like the others.
                        $c = str_replace('-', '_', strtolower(trim($code)));
                        $gotlangs[trim($c)] = true;
                    }
                }
                return true;
            };
            $parsed->callbackRecurse($search);
            if (count($gotlangs) > 0) {
                return [3, $gotlangs];
            }
        }

        return [0, []];
    }

    /*
     * Select the best language match from a list.
     * Note this is one of those functions that will need to be tuned in othe VLEs.
     */
    public function pick_lang(array $langs): string {
        $currlang = current_language();
        if (!array_key_exists($currlang, self::$parentcache)) {
            $parentlangs = get_string_manager()->get_language_dependencies($currlang);
            self::$parentcache[$currlang] = $parentlangs;
        }
        $parentlangs = self::$parentcache[$currlang];

        if (array_search($currlang, $langs) !== false) {
            // If we have that then we use it.
            return $currlang;
        }

        // Do we have a parentlanguage that we could try?
        foreach ($parentlangs as $lang) {
            if (array_search($lang, $langs) !== false) {
                return $currlang;
            }
        }

        // If not then maybe we have a multipart locale name with no parentlang config.
        if (strlen($currlang) == 5 && substr($currlang, 2, 1) === '_') {
            $currlang = substr($currlang, 0, 2);
            if (array_search($currlang, $langs) !== false) {
                return $currlang;
            }
        }

        if (array_search('other', $langs) !== false) {
            // If 'other' is defined we use it.
            return 'other';
        }
        // If all else fails use the first one.
        return $langs[0];
    }
}

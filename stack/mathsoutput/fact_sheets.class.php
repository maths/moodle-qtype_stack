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
 * The fact sheets class for STACK.
 *
 * @copyright  2014 Loughborough University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class stack_fact_sheets {

    /**
     * This is the list of allowable facts tags. Each of these needs to have
     * two corresponding lines in the language file.
     * E.g. greek_alphabet_name and greek_alphabet_fact
     */
    protected static $factsheets = ['greek_alphabet', 'alg_inequalities',
                    'alg_indices', 'alg_logarithms', 'alg_quadratic_formula',
                    'alg_partial_fractions', 'trig_degrees_radians', 'trig_standard_values',
                    'trig_standard_identities', 'hyp_functions', 'hyp_identities',
                    'hyp_inverse_functions', 'calc_diff_standard_derivatives',
                    'calc_diff_linearity_rule', 'calc_product_rule', 'calc_quotient_rule',
                    'calc_chain_rule', 'calc_rules', 'calc_int_standard_integrals',
                    'calc_int_linearity_rule', 'calc_int_methods_substitution',
                    'calc_int_methods_parts', 'calc_int_methods_parts_indefinite'];

    /**
     * Check each facts tag actually corresponds to a valid fact sheet.
     * @param string $text the text to validate.
     * @return array any tags in the input that are not recognised.
     */
    public static function get_unrecognised_tags($text) {
        $tags = self::get_fact_sheet_tags($text);
        $errors = [];
        foreach ($tags as $val) {
            if (!in_array($val, self::$factsheets)) {
                $errors[] = $val;
            }
        }
        return $errors;
    }

    /**
     * Get all the tags present in a string.
     * @return array tags, if there are any. Empty array if none.
     */
    protected static function get_fact_sheet_tags($text) {
        if (preg_match_all('|\[\[facts:(\w*)\]\]|U', $text, $matches)) {
            return $matches[1];
        }
        return [];
    }

    /**
     * This function replaces tags with the HTML value.
     * Note, that at this point we assume we have already validated the text.
     * @param string $text the text in which to expand fact sheet tags.
     * @param qtype_stack_renderer $renderer (options) the STACK renderer, if you have one.
     */
    public static function display($text, qtype_stack_renderer $renderer = null) {

        // Convert any old hints tags into the new format.
        $text = self::convert_legacy_tags($text);

        $tags = self::get_fact_sheet_tags($text);
        if (!$tags) {
            return $text;
        }

        if ($renderer === null) {
            global $PAGE;
            $renderer = $PAGE->get_renderer('qtype_stack');
        }

        foreach ($tags as $tag) {
            if (!in_array($tag, self::$factsheets)) {
                throw new stack_exception('stack_fact_sheets: the following facts tag does not exist: ' . $tag);
            }

            $text = str_replace('[[facts:' . $tag . ']]',
                    $renderer->fact_sheet(stack_string($tag . '_name'), stack_string($tag . '_fact')),
                    $text);
        }

        return $text;
    }

    /**
     * This function converts the old style html tags to the new fact sheets
     * system using square brackets.
     */
    public static function convert_legacy_tags($text) {
        if (strpos($text, '<hint>') === false) {
            return $text;
        }

        preg_match_all('|<hint>(.*)</hint>|U', $text, $matches);
        foreach ($matches[1] as $key => $val) {
            $old = $matches[0][$key];
            $new = '[[facts:' . trim($val) . ']]';
            $text = str_replace($old, $new, $text);
        }
        return $text;
    }

    /**
     * This function returns the html to insert into the documentaion.
     * It ensures that all/only the current tags are included in the docs.
     * Note, docs are usually in markdown, but we have html here because
     * fact sheets are part of castext.
     *
     * @return string HTML to insert into the docs page.
     */
    public static function generate_docs() {
        $doc = '';
        foreach (self::$factsheets as $tag) {
            $doc .= '### ' . stack_string($tag . '_name') . "\n\n<code>[[facts:" . $tag . "]]</code>\n\n";
            // Unusually we don't use stack_string here to make sure mathematics is not processed (yet).
            $doc .= get_string($tag . '_fact', 'qtype_stack') . "\n\n\n";
        }
        return $doc;
    }
}

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

// Public API for other parts of STACK to call in order to process equations.

require_once(__DIR__ . '/mathsoutputbase.class.php');

/**
 * Public API to the maths rendering system.
 *
 * @copyright  2012 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class stack_maths {
    /** @var array output name => instance. */
    protected static $outputs = [];

    /**
     * Do the necessary processing on equations in a language string, before it
     * is output. Rather than calling this method directly, you should probably
     * use the stack_string method in locallib.php.
     * @param string $string the language string, as loaded by get_string.
     * @return string the string, with equations rendered to HTML.
     */
    public static function process_lang_string($string) {
        return self::get_output()->process_lang_string($string);
    }

    /**
     * Do the necessary processing on content that came from the user, for example
     * the question text or general feedback. The result of calling this method is
     * then passed to Moodle's {@link format_text()} function.
     * @param string $text the content to process.
     * @param qtype_stack_renderer $renderer (options) the STACK renderer, if you have one.
     * @return string the content ready to pass to format_text.
     */
    public static function process_display_castext($text, qtype_stack_renderer $renderer = null) {
        return self::get_output()->process_display_castext($text,
                stack_utils::get_config()->replacedollars, $renderer);
    }

    /**
     * Do the necessary processing on documentation page before the content is
     * passed to Markdown.
     * @param string $docs content of the documentation file.
     * @return string the documentation content ready to pass to Markdown.
     */
    public static function pre_process_docs_page($docs) {
        return self::get_output()->pre_process_docs_page($docs);
    }

    /**
     * Do the necessary processing on documentation page after the content is
     * has been rendered by Markdown.
     * @param string $html rendered version of the documentation page.
     * @return string rendered version of the documentation page with equations inserted.
     */
    public static function post_process_docs_page($html) {
        return self::get_output()->post_process_docs_page($html);
    }

    /**
     * Replace dollar delimiters ($...$ and $$...$$) in text with the safer
     * \(...\) and \[...\].
     * @param string $text the original text.
     * @param bool $markup surround the change with <ins></ins> tags.
     * @return string the text with delimiters replaced.
     */
    public static function replace_dollars($text, $markup = false) {
        return self::get_output()->replace_dollars($text, $markup);
    }

    /**
     * @return string the name of the currently configured output method.
     */
    public static function configured_output_name() {
        return stack_string('settingmathsdisplay_' . stack_utils::get_config()->mathsdisplay);
    }

    /**
     * @return stack_maths_output the output method that has been set in the
     *      configuration options.
     */
    protected static function get_output() {
        if (!(property_exists(stack_utils::get_config(), 'mathsdisplay'))
                || '' == trim(stack_utils::get_config()->mathsdisplay)) {
            return self::get_output_instance('mathjax');
        }
        return self::get_output_instance(stack_utils::get_config()->mathsdisplay);
    }

    /**
     * @param string $type the output method name.
     * @return stack_maths_output instance of the output class for this method.
     */
    protected static function get_output_instance($method) {
        if (!array_key_exists($method, self::$outputs)) {
            $class = self::class_for_type($method);
            self::$outputs[$method] = new $class();
        }
        return self::$outputs[$method];
    }

    /**
     * The class name corresponding to an output method.
     * @param string $type the output method name.
     * @return string the corresponding class name.
     */
    protected static function class_for_type($type) {
        global $CFG;
        $file = __DIR__ . "/mathsoutput{$type}.class.php";
        $class = "stack_maths_output_{$type}";

        if (!is_readable($file)) {
            throw new stack_exception('stack_maths: unknown output method ' . $type);
        }
        include_once($file);

        if (!class_exists($class)) {
            throw new stack_exception('stack_maths: output method ' . $type .
                    ' does not define the expected class ' . $class);
        }
        return $class;
    }
}

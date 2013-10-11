<?php
// This file is part of Stack - http://stack.bham.ac.uk/
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
 * The base class for STACK maths output methods.
 *
 * @copyright  2012 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class stack_maths_output {
    /**
     * Do the necessary processing on equations in a language string, before it is output.
     * @param string $string the language string, as loaded by get_string.
     * @return string the string, with equations rendered to HTML.
     */
    public function process_lang_string($string) {
        return $string;
    }

    /**
     * Do the necessary processing on documentation page before the content is
     * passed to Markdown.
     * @param string $docs content of the documentation file.
     * @return string the documentation content ready to pass to Markdown.
     */
    public function pre_process_docs_page($docs) {
        // Double all the \ characters, since Markdown uses it as an escape char,
        // but we use it for maths.
        $docs = str_replace('\\', '\\\\', $docs);

        // Re-double \ characters inside text areas, because we don't want maths
        // renderered there.
        return preg_replace_callback('~(<textarea[^>]*>)(.*?)(</textarea>)~s',
                function ($match) {
                    return $match[1] . str_replace('\\', '\\\\', $match[2]) . $match[3];
                }, $docs);
        $docs = str_replace('\\', '\\\\', $docs);

        return $docs;
    }

    /**
     * Do the necessary processing on documentation page after the content is
     * has been rendered by Markdown.
     * @param string $html rendered version of the documentation page.
     * @return string rendered version of the documentation page with equations inserted.
     */
    public function post_process_docs_page($html) {
        // Now, undo the doubling of the \\ characters inside <code> and <textarea> regions.
        return preg_replace_callback('~(<code>|<textarea[^>]*>)(.*?)(</code>|</textarea>)~s',
                function ($match) {
                    return $match[1] . str_replace('\\\\', '\\', $match[2]) . $match[3];
                }, $html);

        return $html;
    }

    /**
     * Do the necessary processing on content that came from the user, for example
     * the question text or general feedback. The result of calling this method is
     * then passed to Moodle's {@link format_text()} function.
     * @param string $text the content to process.
     * @return string the content ready to pass to format_text.
     */
    public function process_display_castext($text, $replacedollars) {
        if ($replacedollars) {
            $text = $this->replace_dollars($text);
        }
        $text = str_replace('!ploturl!',
                moodle_url::make_file_url('/question/type/stack/plot.php', '/'), $text);
        return $text;
    }

    /**
     * Replace dollar delimiters ($...$ and $$...$$) in text with the safer
     * \(...\) and \[...\].
     * @param string $text the original text.
     * @param bool $markup surround the change with <ins></ins> tags.
     * @return string the text with delimiters replaced.
     */
    public function replace_dollars($text, $markup = false) {
        if ($markup) {
            $displaystart = '<ins>\[</ins>';
            $displayend   = '<ins>\]</ins>';
            $inlinestart  = '<ins>\(</ins>';
            $inlineend    = '<ins>\)</ins>';
        } else {
            $displaystart = '\[';
            $displayend   = '\]';
            $inlinestart  = '\(';
            $inlineend    = '\)';
        }
        $text = preg_replace('~(?<!\\\\)\$\$(.*?)(?<!\\\\)\$\$~', $displaystart . '$1' . $displayend, $text);
        $text = preg_replace('~(?<!\\\\)\$(.*?)(?<!\\\\)\$~', $inlinestart . '$1' . $inlineend, $text);
        return $text;
    }
}

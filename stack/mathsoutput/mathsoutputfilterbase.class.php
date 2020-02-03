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

require_once($CFG->libdir . '/filterlib.php');
require_once($CFG->dirroot . '/filter/tex/filter.php');

/**
 * Base class for STACK maths output methods that use a Moodle text filter to do the work.
 *
 * @copyright  2012 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class stack_maths_output_filter_base extends stack_maths_output {
    protected $filter = null;

    protected $displaywrapstart = '<span class="displayequation">';
    protected $displaywrapend = '</span>';
    protected $displaystart;
    protected $displayend;
    protected $inlinestart;
    protected $inlineend;

    /**
     * Constructor.
     */
    public function __construct() {
        $this->initialise_delimiters();
    }

    public function process_lang_string($string) {
        $string = $this->find_and_render_equations($string);
        $string = str_replace('!ploturl!',
                moodle_url::make_file_url('/question/type/stack/plot.php', '/'), $string);
        return $string;
    }

    public function post_process_docs_page($html) {
        $html = str_replace('&#92;', '\\', $html);
        $html = $this->find_and_render_equations($html);
        $html = parent::post_process_docs_page($html);
        return $html;
    }

    public function process_display_castext($text, $replacedollars, qtype_stack_renderer $renderer = null) {
        $text = parent::process_display_castext($text, $replacedollars, $renderer);
        $text = $this->find_equations_and_replace_delimiters($text);
        return $text;
    }

    /**
     * Find all the equations in some content, and use the filter to render any
     * maths.
     * @param string $html the input HTML.
     * @return string the updated HTML.
     */
    protected function find_and_render_equations($html) {
        return $this->find_and_process_equations($html, 'render_equation_callback');
    }

    /**
     * Callback used by {@link find_and_render_equations()}.
     * @param array $match what was matched by the regular expression.
     * @return string what the match should be replaced by.
     */
    protected function render_equation_callback($match) {
        return $this->render_equation($match[1], $match[2] == ']');
    }

    /**
     * Helper used by {@link find_and_render_equations()}.
     * @param string $tex the LaTeX code to render.
     * @param bool $displaystyle if true this is a displya-style equation, else
     *       an inline-style one.
     */
    protected function render_equation($tex, $displaystyle) {
        if ($displaystyle) {
            return $this->displaywrapstart .
                    $this->get_filter()->filter($this->displaystart . $tex .
                            $this->displayend) . $this->displaywrapend;
        } else {
            return $this->get_filter()->filter($this->inlinestart . $tex . $this->inlineend);
        }
    }

    /**
     * Find all the equations in some content and replace the standard \(...\)
     * and \[...\] delimiters with the ones this filter expects.
     * @param string $html the input HTML.
     * @return string the updated HTML.
     */
    protected function find_equations_and_replace_delimiters($html) {
        return $this->find_and_process_equations($html, 'replace_delimiters_callback');
    }

    /**
     * Callback used by {@link find_equations_and_replace_delimiters()}.
     * @param array $match what was matched by the regular expression.
     * @return string what the match should be replaced by.
     */
    protected function replace_delimiters_callback($match) {
        return $this->replace_delimiters($match[1], $match[2] == ']');
    }

    /**
     * Helper used by {@link find_and_render_equations()}.
     * @param string $tex the LaTeX code to render.
     * @param bool $displaystyle if true this is a displya-style equation, else
     *       an inline-style one.
     */
    protected function replace_delimiters($tex, $displaystyle) {
        if ($displaystyle) {
            return $this->displaywrapstart . $this->displaystart . $tex .
                    $this->displayend . $this->displaywrapend;
        } else {
            return $this->inlinestart . $tex . $this->inlineend;
        }
    }

    /**
     * Helper used by {@link find_and_render_equations()} and
     * {@link find_and_render_equations()}.
     * @param string $html the input HTML.
     * @param string $callback the name of the callback method to use.
     * @return string the updated HTML.
     */
    protected function find_and_process_equations($html, $callback) {
        return preg_replace_callback('~(?<!\\\\)(?<!<code>)\\\\[([](.*?)(?<!\\\\)\\\\([])])(?!</code>)~s',
                array($this, $callback), $html);
    }

    /**
     * @return moodle_text_filter an instance of the text filter to use to
     * render equations.
     */
    protected function get_filter() {
        if (is_null($this->filter)) {
            $this->filter = $this->make_filter();
        }
        return $this->filter;
    }

    /**
     * Initialise the fields of this class that contin the delimiters to use.
     */
    protected abstract function initialise_delimiters();

    /**
     * @return moodle_text_filter an newly created instance of the text filter
     * to use to render equations.
     */
    protected abstract function make_filter();
}

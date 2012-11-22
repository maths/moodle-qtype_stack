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


require_once($CFG->libdir . '/filterlib.php');
require_once($CFG->dirroot . '/filter/tex/filter.php');


/**
 * STACK maths output methods for using Moodle's TeX filter.
 *
 * @copyright  2012 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class stack_maths_output_tex extends stack_maths_output {
    protected static $filter = null;

    public function process_lang_string($string) {
        return $this->find_and_render_equations($string);
    }

    public function post_process_docs_page($html) {
        $html = str_replace('&#92;', '\\', $html);
        $html = $this->find_and_render_equations($html);
        $html = parent::post_process_docs_page($html);
        return $html;
    }

    public function pre_process_user_input($text, $replacedollars) {
        $text = parent::pre_process_user_input($text, $replacedollars);
        $text = $this->find_equations_and_replace_delimiters($text);
        return $text;
    }

    protected function find_and_render_equations($html) {
        return $this->find_and_process_equations($html, 'render_equation_callback');
    }

    protected function render_equation_callback($match) {
        return $this->render_equation($match[1], $match[2] == ']');
    }

    protected function render_equation($tex, $displaystyle) {
        if ($displaystyle) {
            return '<span class="displayequation">' .
                    $this->get_filter()->filter('\[\displaystyle ' . $tex . '\]') . '</span>';
        } else {
            return $this->get_filter()->filter('\[' . $tex . '\]');
        }
    }

    protected function find_equations_and_replace_delimiters($html) {
        return $this->find_and_process_equations($html, 'replace_delimiters_callback');
    }

    protected function replace_delimiters_callback($match) {
        return $this->replace_delimiters($match[1], $match[2] == ']');
    }

    protected function replace_delimiters($tex, $displaystyle) {
        if ($displaystyle) {
            return '<span class="displayequation">\[\displaystyle ' . $tex . '\]</span>';
        } else {
            return '\[' . $tex . '\]';
        }
    }

    protected function find_and_process_equations($html, $callback) {
        return preg_replace_callback('~(?<!\\\\)(?<!<code>)\\\\[([](.*?)(?<!\\\\)\\\\([])])(?!</code>)~s',
                array($this, $callback), $html);
    }

    /**
     * @return filter_tex and instance of the Moodle TeX filter.
     */
    protected function get_filter() {
        if (is_null(self::$filter)) {
            self::$filter = new filter_tex(context_system::instance(), array());
        }
        return self::$filter;
    }
}

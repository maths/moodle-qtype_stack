<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * STACK question renderer class.
 *
 * @package    qtype
 * @subpackage stack
 * @author     André Storhaug <andr3.storhaug@gmail.com>
 * @copyright  2018 NTNU
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace qtype_stack\output;

use stack_exception;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../../locallib.php');

class debug_renderer {
    /**
     * Static class. You cannot create instances.
     * @throws stack_exception
     */
    private function __construct() {
        throw new stack_exception('debug_view: you cannot create instances of this class.');
    }

    public static function render_debug_header() {
        $result = "";

        $result .= \html_writer::start_tag('h5');
        $result .= \html_writer::span('!', 'stack-debug-nb-star');
        $result .= \html_writer::span('Debugging info:');
        $result .= \html_writer::end_tag('h5');
        return $result;
    }

    public static function render_debug_stack_span($inputid, $value) {
        $result = "";

        $attributes = array(
            'id' => $inputid . '_debug'
        );

        $result .= \html_writer::span("Transpiled Maximxa code from TeX2Max: ");
        $result .= \html_writer::div($value, 'stack-debug-value', $attributes);

        return $result;
    }

    public static function render_debug_latex_span($inputid, $value) {
        $result = "";

        $attributes = array(
            'id' => $inputid . '_debug'
        );

        $result .= \html_writer::span("LaTeX from MathQuill: ");
        $result .= \html_writer::div($value, 'stack-debug-value', $attributes);


        return $result;
    }


    public static function render_debug_view(array $stackinputs,  $stackinputstring, array $latexinputs, array $latexinputstring) {

        $result = "";
        $result .= \html_writer::start_div('stack-debug-wrapper');

        $result .= self::render_debug_header();


        for ($i = 0; $i < count($stackinputs); $i++) {
            $result .= \html_writer::start_div('stack-debug-question-wrapper');
            $result .= self::render_debug_stack_span($stackinputs[$i], $stackinputstring);
            $result .= self::render_debug_latex_span($latexinputs[$i], $latexinputstring[$i]);
            $result .= \html_writer::end_div();
        }

        $result .= \html_writer::end_div();

        return $result;
    }
}
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
require_once(__DIR__ . '/mathsoutputfilterbase.class.php');

/**
 * STACK maths output methods for using MathJax.
 *
 * @package    qtype_stack
 * @copyright  2012 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class stack_maths_output_mathjax extends stack_maths_output_filter_base {
    /**
     * Register MathJax compatibility required by semantic STACK output.
     */
    protected function require_providecommand(): void {
        global $PAGE;
        if ($PAGE->requires->should_create_one_time_item_now('qtype_stack-mathjax-providecommand')) {
            $PAGE->requires->js_call_amd('qtype_stack/mathjax_providecommand', 'init');
        }
    }

    /**
     * Process CASText and load fallback-definition support when the TeX needs it.
     *
     * @param string $text the CASText output.
     * @param bool $replacedollars whether dollar delimiters should be replaced.
     * @param qtype_stack_renderer|null $renderer optional question renderer.
     * @return string processed CASText.
     */
    public function process_display_castext($text, $replacedollars, ?qtype_stack_renderer $renderer = null) {
        if (str_contains($text, '\\providecommand')) {
            $this->require_providecommand();
        }
        return parent::process_display_castext($text, $replacedollars, $renderer);
    }

    // phpcs:ignore moodle.Commenting.MissingDocblock.Function
    protected function initialise_delimiters() {
        $this->displaywrapstart = '';
        $this->displaywrapend = '';
        $this->displaystart = '\[';
        $this->displayend = '\]';
        $this->inlinestart = '\(';
        $this->inlineend = '\)';
    }

    // phpcs:ignore moodle.Commenting.MissingDocblock.Function
    protected function make_filter() {
        global $CFG, $PAGE;
        if (class_exists('\filter_mathjaxloader\text_filter')) {
            $filter = new \filter_mathjaxloader\text_filter($PAGE->context, []);
        } else {
            // Once Moodle 4.5 is the lowest supported version of Moodle.
            require_once($CFG->libdir . '/filterlib.php');
            require_once($CFG->dirroot . '/filter/mathjaxloader/filter.php');
            return new filter_mathjaxloader($PAGE->context, []);
        }

        $filter->setup($PAGE, $PAGE->context);
        return $filter;
    }
}

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
 * Displays a prt graph using an html table with text.
 *
 * @package   qtype_stack
 * @copyright 2023 The University of Edinburgh
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Displays a {@link stack_abstract_graph} as text.
 *
 * @copyright 2023 The University of Edinburgh
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class stack_prt_graph_text_renderer {

    /*
     * Hold the graph itself.
     */
    protected $g = null;

    /**
     * Output a graph as SVG.
     * @param stack_abstract_graph $g the graph to display.
     * @param string $id an id to add to the SVG node in the HTML.
     */
    public static function render(stack_abstract_graph $g) {
        $renderer = new self($g);
        return $renderer->to_html();
    }

    /**
     * Constructor.
     * @param stack_abstract_graph $g the graph to display.
     */
    protected function __construct(stack_abstract_graph $g) {
        $this->g = $g;
    }

    /**
     * Actually generate the HTML for the nodes in the graph.
     * @return string HTML code. Can be embedded straight into a HTML page.
     */
    protected function to_html() {

        $table = [];
        foreach ($this->g->get_nodes() as $node) {
            $quiet = stack_string('quiet_icon_false');
            if ($node->quiet) {
                $quiet = stack_string('quiet_icon_true');
            }
            // Put the name and description in one cell. It looks better.
            $table[] = [$node->name . '. ' . $node->description,
                    html_writer::tag('code', s($node->casstatement)), $quiet,
                            $node->truenote, $node->falsenote];
        }

        $html = '';
        foreach ($table as $tablerow) {
            $row = '';
            foreach ($tablerow as $td) {
                $row .= html_writer::tag('td', $td);
            }
            $html .= html_writer::tag('tr', $row) . "\n";
        }
        // TODO: style the table with more padding.
        $html = html_writer::start_tag('table', ['class' => 'prttexttable']) . $html .
                html_writer::end_tag('table');

        return $html;
    }
}

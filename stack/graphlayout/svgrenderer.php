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
 * Displays an abstract graph using SVG.
 *
 * @package   qtype_stack
 * @copyright 2013 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Displays a {@link stack_abstract_graph} as SVG.
 *
 * @copyright 2013 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class stack_abstract_graph_svg_renderer {
    /**
     * @var int overall scale factor for the image. 1 unit in the depth direction
     * or two units in the x direction correspond to this many pixels.
     */
    const SCALE = 70;

    /** @var int radius of the circle representing a node in pixels. */
    const NODE_RADIUS = 15;

    /** @var int radius the circle at the end of a stop edge. */
    const END_RADIUS = 4;

    /** @var int size of the error cross at the end of broken links. */
    const CROSS_SIZE = 6;

    /** @var float distance of the edge labels down the edge, as a multiple of NODE_RADIUS. */
    const LABEL_POS = 1.5;

    /** @var float length of stop edges, as a multiple of NODE_RADIUS. */
    const STUB_LENGTH = 2.5;

    /** @var stack_abstract_graph the graph to display. */
    public $g;

    /** @var float */
    public $dx;

    /**
     * @var array of SVG fragments. We build up the output as an array of bits,
     * and then join them together at the end.
     */
    public $svg = [];

    /**
     * Output a graph as SVG.
     * @param stack_abstract_graph $g the graph to display.
     * @param string $id an id to add to the SVG node in the HTML.
     */
    public static function render(stack_abstract_graph $g, $id) {
        $renderer = new self($g);
        list($minx, $maxx) = $g->x_range();
        $width = ceil((5 + $maxx - $minx) * self::SCALE / 2);
        $height = ceil((0.3 + $g->max_depth()) * self::SCALE);

        $output = '';
        $output .= html_writer::start_tag('svg', ['id' => $id, 'class' => 'stack_abstract_graph',
                'width' => $width . 'px', 'height' => $height . 'px', 'version' => '1.1',
                'xmlns' => 'http://www.w3.org/2000/svg']);
        $output .= $renderer->to_svg();
        $output .= html_writer::end_tag('svg');
        return $output;
    }

    /**
     * Constructor.
     * @param stack_abstract_graph $g the graph to display.
     */
    protected function __construct(stack_abstract_graph $g) {
        $this->g = $g;
    }

    /**
     * Actually generate the SVG for the graph.
     * @return string SVG code. Can be embedded straight into a HTML page.
     */
    protected function to_svg() {
        list($minx) = $this->g->x_range();
        $this->dx = self::SCALE * (2.5 - $minx) / 2;

        foreach ($this->g->get_nodes() as $node) {
            if (!is_null($node->right)) {
                $this->edge($node, stack_abstract_graph::RIGHT);
            } else {
                $this->stub_edge($node, stack_abstract_graph::RIGHT);
            }
            if (!is_null($node->left)) {
                $this->edge($node, stack_abstract_graph::LEFT);
            } else {
                $this->stub_edge($node, stack_abstract_graph::LEFT);
            }
        }

        foreach ($this->g->get_nodes() as $node) {
            $this->node($node);
        }

        return implode("\n", $this->svg);
    }

    /**
     * Generate the SVG code for an edge, with its label.
     * @param stack_abstract_graph_node $parent
     * @param int $direction one of stack_abstract_graph::LEFT or stack_abstract_graph::RIGHT.
     */
    protected function edge(stack_abstract_graph_node $parent, $direction) {
        if ($direction == stack_abstract_graph::LEFT) {
            $label = $parent->leftlabel;
            $class = 'left';
            $child = $this->g->get($parent->left);
        } else {
            $label = $parent->rightlabel;
            $class = 'right';
            $child = $this->g->get($parent->right);
        }

        list($px, $py) = $this->position($parent);
        list($cx, $cy) = $this->position($child);
        $initialdirection = $direction / 2;

        $midx = null;
        if (($cx - $px) / ($cy - $py) / $initialdirection < 1) {
            // A bit narrow, use a curve.
            $midy = $py + 3 * (1 - pow(5 / 6, $child->depth - $parent->depth)) * self::SCALE;
            $midx = $px + $initialdirection * ($midy - $py);

        } else if ($this->g->edge_hits_another_node($parent, $child)) {
            // This line goes straight through another node, bend it.
            $midy = $py + self::SCALE;
            $midx = ($px * ($cy - $py - self::SCALE) + $cx * self::SCALE) / ($cy - $py) + $initialdirection * self::SCALE;
        }

        if (is_null($midx)) {
            $path = "M $px $py L $cx $cy";
            $labelx = $px + self::LABEL_POS * (($cx - $px) / ($cy - $py) - $initialdirection / 2) * self::NODE_RADIUS;
        } else {
            $path = "M $px $py Q $midx $midy $cx $cy";
            $labelx = $px + self::LABEL_POS * $initialdirection / 2 * self::NODE_RADIUS;
        }

        $this->svg[] = html_writer::empty_tag('path', ['d' => $path, 'class' => $class]);
        if ($label) {
            $this->svg[] = html_writer::tag('text', s($label), [
                    'x' => $labelx, 'y' => $py + self::LABEL_POS * self::NODE_RADIUS,
                    'class' => 'edgelabel ' . $class]);
        }
    }

    /**
     * Generate the SVG code for a terminal edge, with its label.
     * @param stack_abstract_graph_node $parent
     * @param int $direction one of stack_abstract_graph::LEFT or stack_abstract_graph::RIGHT.
     */
    protected function stub_edge(stack_abstract_graph_node $parent, $direction) {
        if ($direction == stack_abstract_graph::LEFT) {
            $label = $parent->leftlabel;
            $class = 'left';
        } else {
            $label = $parent->rightlabel;
            $class = 'right';
        }

        list($px, $py) = $this->position($parent);
        $initialdirection = $direction / 2;
        $cx = $px + self::STUB_LENGTH * $initialdirection * self::NODE_RADIUS;
        $cy = $py + self::STUB_LENGTH * self::NODE_RADIUS;
        $labelx = $px + self::LABEL_POS * $initialdirection / 2 * self::NODE_RADIUS;

        $this->svg[] = html_writer::empty_tag('path', ['d' => "M $px $py L $cx $cy", 'class' => $class]);

        if ($this->g->is_broken_edge($parent, $direction)) {
            $cross = 'M ' . ($cx - self::CROSS_SIZE) . ' ' . ($cy - self::CROSS_SIZE) .
                    ' L ' . ($cx + self::CROSS_SIZE) . ' ' . ($cy + self::CROSS_SIZE) .
                    ' M ' . ($cx - self::CROSS_SIZE) . ' ' . ($cy + self::CROSS_SIZE) .
                    ' L ' . ($cx + self::CROSS_SIZE) . ' ' . ($cy - self::CROSS_SIZE);
            $this->svg[] = html_writer::empty_tag('path', ['d' => $cross, 'class' => 'cross ' . $class]);
        } else {
            $this->svg[] = html_writer::empty_tag('circle', ['r' => self::END_RADIUS,
                    'cx' => $cx, 'cy' => $cy, 'class' => $class]);
        }

        if ($label) {
            $this->svg[] = html_writer::tag('text', s($label), [
                    'x' => $labelx, 'y' => $py + self::LABEL_POS * self::NODE_RADIUS,
                    'class' => 'edgelabel ' . $class]);
        }
    }

    /**
     * Generate the SVG code for an node, with its label.
     * @param stack_abstract_graph_node $node
     */
    protected function node(stack_abstract_graph_node $node) {
        list($x, $y) = $this->position($node);
        if ($node->url) {
            $this->svg[] = html_writer::start_tag('a', ['xlink:href' => $node->url]);
        }
        $this->svg[] = html_writer::empty_tag('circle', ['r' => self::NODE_RADIUS,
                'cx' => $x, 'cy' => $y, 'class' => 'node']);
        $this->svg[] = html_writer::tag('text', s($node->name), ['x' => $x, 'y' => $y,
                'class' => 'nodelabel']);
        if ($node->url) {
            $this->svg[] = html_writer::end_tag('a');
        }
    }

    /**
     * Covert a node's position to a pixel position on the image.
     * @param stack_abstract_graph_node $node
     * @return array of two floats, the x and y coordinate of the node in pixels.
     */
    protected function position(stack_abstract_graph_node $node) {
        return [$this->dx + $node->x / 2 * self::SCALE, ($node->depth - 0.5) * self::SCALE];
    }
}

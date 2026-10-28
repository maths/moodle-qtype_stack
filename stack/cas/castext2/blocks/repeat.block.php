<?php
// This file is part of STACK
//
// STACK is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// STACK is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Stateful.  If not, see <http://www.gnu.org/licenses/>.

/**
 * This class adds in the interactive repeat blocks to castext.
 * @package    qtype_stack
 * @copyright  2025 University of Edinburgh.
 * @copyright  2025 Ruhr University Bochum.
 * @copyright  2025 ETH Zürich.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../block.interface.php');

/**
 * This class adds in the interactive repeat blocks to castext.
 */
class stack_cas_castext2_repeat extends stack_cas_castext2_block {

    /**
     * Placeholder for the <repeatindex> tag, replaced client side by the number of the copy.
     * This must match REPEATINDEX in the JS of the repeat button.
     */
    const REPEATINDEX = '%%REPEATINDEX%%';

    /**
     * Set for a repeat block inside another one, whose copies are made by the outer block's button.
     * @var bool
     */
    public $nested = false;

    // phpcs:ignore moodle.Commenting.MissingDocblock.Function
    public function compile($format, $options): ?MP_Node {

        $repeatid = $this->params['id'];
        // Repeat blocks and buttons inside this block are compiled for nesting.
        $this->mark_nested();
        $body = new MP_List([new MP_String('%root')]);
        // This <div> only holds the template which the client-side JS clones into
        // the repeatcontainer below; it is never shown to the student directly.
        // The template must not be typeset by MathJax: the copies are typeset once
        // any <repeatindex> has been replaced.
        // The JS of the repeat button finds a nested repeat block inside a template by data-stack-repeat.
        $body->items[] = new MP_String('<div style="display:none;" class="mathjax_ignore tex2jax_ignore" data-stack-repeat="' .
            htmlspecialchars($repeatid) . '" id="');
        // We use the quid block to make the ids unique.
        $body->items[] = new MP_List([new MP_String('quid'), new MP_String("repeat_" . $repeatid)]);
        $body->items[] = new MP_String('">');

        foreach ($this->children as $item) {
            $c = $item->compile($format, $options);
            if ($c !== null) {
                $this->replace_repeatindex($c);
                $body->items[] = $c;
            }
        }
        $body->items[] = new MP_String('</div>');
        
        $body->items[] = new MP_String('<div data-stack-repeat-container="' . htmlspecialchars($repeatid) . '" id="');
        // We use the quid block to make the ids unique.
        $body->items[] = new MP_List([new MP_String('quid'), new MP_String("repeatcontainer_" . $repeatid)]);
        $body->items[] = new MP_String('"></div>');

        return $body;
    }

    /**
     * Mark the repeat blocks and repeat buttons inside this block as nested.
     */
    private function mark_nested() {
        $this->callbackRecurse(function ($node) {
            if ($node instanceof stack_cas_castext2_repeat || $node instanceof stack_cas_castext2_repeatbutton) {
                $node->nested = true;
            }
        });
    }

    /**
     * Replace the <repeatindex> tag in the static content of the block with a placeholder,
     * which survives the processing of the question text.
     * @param MP_Node $node compiled castext.
     */
    private function replace_repeatindex(MP_Node $node) {
        if ($node instanceof MP_String) {
            $node->value = preg_replace('~<\s*repeatindex\s*/?\s*>|<\s*/\s*repeatindex\s*>~i',
                self::REPEATINDEX, $node->value);
        } else if ($node instanceof MP_List) {
            foreach ($node->items as $item) {
                $this->replace_repeatindex($item);
            }
        }
    }

    // phpcs:ignore moodle.Commenting.MissingDocblock.Function
    public function is_flat(): bool {
        return true;
    }

    // phpcs:ignore moodle.Commenting.MissingDocblock.Function
    public function validate_extract_attributes(): array {
        $r = [];
        if (!isset($this->params['id'])) {
            return $r;
        }
        return $r;
    }

    // phpcs:ignore moodle.Commenting.MissingDocblock.Function
    public function validate(&$errors=[], $options=[]): bool {
        if (!array_key_exists('id', $this->params)) {
            $errors[] = new $options['errclass']('Repeat block requires a id parameter.', $options['context'] . '/' .
                $this->position['start'] . '-' . $this->position['end']);
            return false;
        }
        // Children are validated after this block, so let [[repeatindex/]] and nested repeat blocks
        // know where they are.
        // The copies of the block are made client side, so interactive blocks are not allowed
        // anywhere inside it, e.g. also not inside an [[if]].  The exception is one level of nested
        // repeat blocks with their buttons, whose copies are made by the button of this block.
        $interactive = false;
        $toodeep = false;
        $nested = $this->nested;
        $this->callbackRecurse(function ($node) use (&$interactive, &$toodeep, $nested) {
            if ($node instanceof stack_cas_castext2_repeatindex) {
                $node->insiderepeat = true;
            } else if ($node instanceof stack_cas_castext2_repeat || $node instanceof stack_cas_castext2_repeatbutton) {
                $node->nested = true;
                $toodeep = $toodeep || $nested;
            } else if ($node->is_interactive()) {
                $interactive = true;
            }
        });
        if ($toodeep) {
            $errors[] = new $options['errclass']('Repeat blocks may only be nested one level deep.', $options['context'] . '/' .
                $this->position['start'] . '-' . $this->position['end']);
            return false;
        }
        if ($interactive) {
            $errors[] = new $options['errclass']('Repeat blocks may not contain interactive children.', $options['context'] . '/' .
                $this->position['start'] . '-' . $this->position['end']);
            return false;
        }
        return true;
    }

    /**
     * Is this an interactive block?
     * If true, we can't generate a static version.
     * @return bool
     */
    public function is_interactive(): bool {
        return true;
    }
}

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
// along with STACK.  If not, see <http://www.gnu.org/licenses/>.
defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../block.interface.php');
require_once(__DIR__ . '/../../../utils.class.php');

// Register a counter.
require_once(__DIR__ . '/iframe.block.php');
stack_cas_castext2_iframe::register_counter('///REVEAL_COUNT///');

/**
 * A dynamic JavaScript backed that toggles the visibility of its contents.
 * Based on the value of a given input. Will only do singular direct string
 * match.
 */
class stack_cas_castext2_reveal extends stack_cas_castext2_block {

    public function compile($format, $options): ?MP_Node {
        static $count = 0;
        /*
         * This block compiles into multiple things.
         *  1. There is the default hidden div containing the contents.
         *  2. There is the [[iframe]] that binds to the input and
         *     triggers visibility toggling.
         *
         * Basically we change the value of $format for this subtree.
         * Note that the jsxgraph block does this automatically.
         */
        $body = new MP_List([new MP_String('%root')]);

        // This should have enough randomness to avoid collisions.
        $uid = '' . rand(100, 999) . time() . '_' . $count;
        $count = $count + 1;

        // Name and hide the contents.
        $body->items[] = new MP_String('<div style="display:none;" id="stack-reveal-' . $uid . '">');

        foreach ($this->children as $item) {
            $c = $item->compile($format, $options);
            if ($c !== null) {
                $body->items[] = $c;
            }
        }
        $body->items[] = new MP_String('</div>');

        $code = 'import {stack_js} from "' . stack_cors_link('stackjsiframe.min.js') . '";';
        $code .= 'stack_js.request_access_to_input("' . $this->params['input'] . '", true).then((id) => {';
        // So that should give us access to the input.
        // Once we get the access immediately bind a listener to it.
        $code .= 'const input = document.getElementById(id);';
        $code .= 'input.addEventListener("change",(e)=>{';
        $code .= 'stack_js.toggle_visibility("stack-reveal-' . $uid . '",input.value===' .
                json_encode($this->params['value']) . ');});';

        // Finally check whether the value was already matching, or
        // if it changed during the previous steps.
        $code .= 'stack_js.toggle_visibility("stack-reveal-' . $uid . '",input.value===' .
                json_encode($this->params['value']) . ');';
        $code .= '});';

        // Now add a hidden [[iframe]] with suitable scripts.
        $body->items[] = new MP_List([
            new MP_String('iframe'),
            new MP_String(json_encode(['hidden' => true,
                'title' => 'Logic container for a revealing portion ///REVEAL_COUNT///.'])),
            new MP_List([
                new MP_String('script'),
                new MP_String(json_encode(['type' => 'module'])),
                new MP_String($code)
            ])
        ]);

        return $body;
    }

    public function is_flat(): bool {
        // Never flat, the [[iframe]] portion needs extra processing.
        return false;
    }

    public function postprocess(array $params, castext2_processor $processor=null): string {
        return 'Post processing of reveal blocks never happens, this block is handled through [[iframe]].';
    }

    public function validate_extract_attributes(): array {
        return [];
    }
}

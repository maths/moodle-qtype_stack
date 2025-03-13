<?php
// This file is part of Stateful
//
// Stateful is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Stateful is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Stateful.  If not, see <http://www.gnu.org/licenses/>.
defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/questionlib.php');
require_once(__DIR__ . '/../block.interface.php');
// Register a counter.
require_once(__DIR__ . '/iframe.block.php');
stack_cas_castext2_iframe::register_counter('///ADAPTBUTTON_COUNT///');

class stack_cas_castext2_adaptbutton extends stack_cas_castext2_block {

    // All reveals need unique (at request level) identifiers,
    // we use running numbering.

    public function compile($format, $options): ?MP_Node {

        static $count = 0;

        $body = new MP_List([new MP_String('%root')]);

        // This should have enough randomness to avoid collisions.
        $uid = '' . rand(100, 999) . time() . '_' . $count;
        $count = $count + 1;

        $body->items[] = new MP_String('<button type="button" class="btn btn-secondary" id="stack-adaptbutton-' . 
        $uid . '">' . $this->params['title'] . '</button>');

        $code = "\nimport {stack_js} from '" . stack_cors_link('stackjsiframe.min.js') . "';\n";

        //TODO: Add a counter, amount of button clicks
        $code .= "stack_js.request_access_to_input('" . $this->params['save_state'] . "', true).then((id) => {\n";
        $code .= "const input = document.getElementById(id);\n";
        $code .= "if (input.value=='true'){ hide_and_show(); }\n";
        $code .= "stack_js.register_external_button_listener('stack-adaptbutton-". $uid . "', function() {";
        $code .= 'input.value="true";';
        $code .= 'input.dispatchEvent(new Event("change"));';
        $code .= "hide_and_show();";
        $code .= "});\n";
        $code .= "});\n"; // end of input request

        $code .= "function hide_and_show(){";
            if (isset($this->params['show_ids'])) {
                $split_show_id = preg_split ("/[\ \n\;]+/", $this->params['show_ids']); 
                foreach ($split_show_id as &$id )
                {
                    $code .= "stack_js.toggle_visibility('stack-adapt-" . $id . "',true);";
                }
            }   
            if (isset($this->params['hide_ids'])) {
                $split_hide_id = preg_split ("/[\ \n\;]+/", $this->params['hide_ids']); 
                foreach ($split_hide_id as &$id )
                {
                    $code .= "stack_js.toggle_visibility('stack-adapt-" . $id . "',false);";
                }
            }
        $code .= "}";

        //Now add a hidden [[iframe]] with suitable scripts.
        $body->items[] = new MP_List([
            new MP_String('iframe'),
            new MP_String(json_encode(['hidden' => true, 'title' => 'Logic container for a adaptbutton  ///ADAPTBUTTON_COUNT///.'])),
            new MP_List([
                new MP_String('script'),
                new MP_String(json_encode(['type' => 'module'])),
                new MP_String($code)
            ])
        ]);

        return $body;
    }

    public function is_flat(): bool {
        return true;
    }

    public function postprocess(array $params, castext2_processor $processor=null): string {
        return 'Post processing of adaptbutton blocks never happens, this block is handled through [[iframe]].';
    }

    public function validate_extract_attributes(): array {
        $r = [];
        if (!isset($this->params['title'])) {
            return $r;
        }
        if (!isset($this->params['show_ids']) && !isset($this->params['hide_ids'])) {
            return $r;
        }
        if (!isset($this->params['save_state'])) {
            return $r;
        }
        return $r;
    }

    public function validate(&$errors=[], $options=[]): bool {
        if (!array_key_exists('title', $this->params)) {
            $errors[] = new $options['errclass']('Adaptbutton block requires a title parameter.', $options['context'] . '/' .
                $this->position['start'] . '-' . $this->position['end']);
            return false;
        }

        if (!array_key_exists('show_ids', $this->params) && !array_key_exists('hide_ids', $this->params)) {
            $errors[] = new $options['errclass']('Adaptbutton block requires a show_ids or a hide_ids parameter.', $options['context'] . '/' .
                $this->position['start'] . '-' . $this->position['end']);
            return false;
        }
        if (!array_key_exists('save_state', $this->params)) {
            $errors[] = new $options['errclass']('Adaptbutton block requires a save_state parameter.', $options['context'] . '/' .
                $this->position['start'] . '-' . $this->position['end']);
            return false;
        }
        return true;
    }
}
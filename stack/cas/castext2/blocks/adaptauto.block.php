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


require_once(__DIR__ . '/../block.interface.php');

class stack_cas_castext2_adaptauto extends stack_cas_castext2_block {

    public function compile($format, $options): ?MP_Node {

        $body = new MP_List([new MP_String('%root')]);

        $code = 'import {stack_js} from "' . stack_cors_link('stackjsiframe.min.js') . '";';
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

        // $body->items[] = new MP_String('<script>document.addEventListener("DOMContentLoaded", function(){');
        // $body->items[] = new MP_String($code);
        // $body->items[] = new MP_String('});</script>');
    
        $body->items[] = new MP_List([
                new MP_String('script'),
                new MP_String(json_encode(['type' => 'module'])),
                new MP_String($code)
        ]);

        // Now add a hidden [[iframe]] with suitable scripts.
        // $body->items[] = new MP_List([
        //     new MP_String('iframe'),
        //     new MP_String(json_encode(['hidden' => true, 'title' => 'Logic container for a adaptauto.'])),
        //     new MP_List([
        //         new MP_String('script'),
        //         new MP_String(json_encode(['type' => 'module'])),
        //         new MP_String($code)
        //     ])
        // ]);

        return $body;
    }

    public function is_flat(): bool {
        return true;
    }

    public function validate_extract_attributes(): array {
        $r = array();
        if (!isset($this->params['show_ids']) && !isset($this->params['hide_ids'])) {
            return $r;
        }
        return $r;
    }

    public function validate(&$errors=[], $options=[]): bool {
        if (!array_key_exists('show_ids', $this->params) && !array_key_exists('hide_ids', $this->params)) {
            $errors[] = new $options['errclass']('Adaptbutton block requires a show_ids or a hide_ids parameter.', $options['context'] . '/' .
                $this->position['start'] . '-' . $this->position['end']);
            return false;
        }
        return true;
    }
}

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

class stack_cas_castext2_adaptbutton extends stack_cas_castext2_block {

    // All reveals need unique (at request level) identifiers,
    // we use running numbering.
    private static $countadaptbuttons = 1;

    public function compile($format, $options): ?MP_Node {

        $body = new MP_List([new MP_String('%root')]);

        $onclick = "";
        if (isset($this->params['show_ids'])) {
            $split_show_id = preg_split ("/(?<=\d)(;|;\s)/", $this->params['show_ids']); 
            foreach ($split_show_id as &$id )
            {
                $onclick .= "document.getElementById('stack-adapt-" . $id . "').style.display='block';";
            }
        }   
        if (isset($this->params['hide_ids'])) {
            //changed
            $split_hide_id = preg_split ("/(?<=\d)(;|;\s)/", $this->params['hide_ids']); 
            foreach ($split_hide_id as &$id )
            {
                $onclick .= "document.getElementById('stack-adapt-" . $id . "').style.display='none';";
            }
        }

        $body->items[] = new MP_String('<button type="button" class="btn btn-secondary" id="stack-adaptbutton-' . 
            self::$countadaptbuttons . '" onclick="' . $onclick . '" >' . $this->params['title'] . '</button>');


        // $code = 'import {stack_js} from "' . stack_cors_link('stackjsiframe.min.js') . '";';
        // //$code .= 'stack_js.request_access_to_input("' . $this->params['id'] . '", true).then((id) => {';
        // // So that should give us access to the input.
        // // Once we get the access immediately bind a listener to it.
        // //test kommentar
        // $code .= 'const butt = document.getElementById("stack-adaptbutton-'.self::$countadaptbuttons.'");';
        // $code .= 'butt.addEventListener("click",(e)=>{';
        // if (isset($this->params['show_id'])) {
        //     $code .= 'stack_js.toggle_visibility("stack-adapt-' . $this->params['show_id'] . '",true);});';
        // }   
        // if (isset($this->params['hide_id'])) {
        //     $code .= 'stack_js.toggle_visibility("stack-adapt-' . $this->params['hide_id'] . '",false);});';
        // }         

        //$code .= '});';
        // Now add a hidden [[iframe]] with suitable scripts.
        // $body->items[] = new MP_List([
        //     new MP_String('iframe'),
        //     new MP_String(json_encode(['hidden' => true, 'title' => 'Logic container for a adaptbutton ' .
        //             self::$countadaptbuttons . '.'])),
        //     new MP_List([
        //         new MP_String('script'),
        //         new MP_String(json_encode(['type' => 'module'])),
        //         new MP_String($code)
        //     ])
        // ]);



        // Update count.
        self::$countadaptbuttons = self::$countadaptbuttons + 1;

        return $body;
    }

    public function is_flat(): bool {
        return true;
    }

    public function validate_extract_attributes(): array {
        $r = array();
        if (!isset($this->params['title'])) {
            return $r;
        }
        if (!isset($this->params['show_ids']) && !isset($this->params['hide_ids'])) {
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
        return true;
    }
}

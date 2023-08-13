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
            $split_show_id = preg_split ("/[\ \n\;]+/", $this->params['show_ids']); 
            foreach ($split_show_id as &$id )
            {
                $onclick .= "document.getElementById('stack-adapt-" . $id . "').style.display='block';";
            }
        }   
        if (isset($this->params['hide_ids'])) {
            //changed
            $split_hide_id = preg_split ("/[\ \n\;]+/", $this->params['hide_ids']); 
            foreach ($split_hide_id as &$id )
            {
                $onclick .= "document.getElementById('stack-adapt-" . $id . "').style.display='none';";
            }
        }

        //Input boolean for state of adaptbutton
        // $onclick .= "document.getElementById('checkbox-adaptbutton-".self::$countadaptbuttons."').checked=true;";
        // $onclick .= "console.log(document.getElementById('checkbox-adaptbutton-".self::$countadaptbuttons."').checked);";

        // $body->items[] = new MP_String('<input type="checkbox" id="checkbox-adaptbutton-'.self::$countadaptbuttons.
        // '" style="display: none; visibility: hidden;">');

        //Input algebraic
        // $onclick .= "document.getElementById('number-adaptbutton-".self::$countadaptbuttons."').value++;";
        // $onclick .= "console.log(document.getElementById('number-adaptbutton-".self::$countadaptbuttons."').value);";

        // $body->items[] = new MP_String('<input type="number" id="number-adaptbutton-'.self::$countadaptbuttons.
        // '" value="0" style="display: none; visibility: hidden;">');

        $onclick.="const input = document.getElementById('".$this->params['save_state']."');";
        $onclick .= "input.value='true';";

        $body->items[] = new MP_String('<button type="button" class="btn btn-secondary" id="stack-adaptbutton-' . 
        self::$countadaptbuttons . '" onclick="' . $onclick . '" >' . $this->params['title'] . '</button>');

        
        $code = "\nimport {stack_js} from '" . stack_cors_link('stackjsiframe.min.js') . "';\n";
        $code .= "import {stack_jxg} from '" . stack_cors_link('stackjsxgraph.min.js') . "';\n";
        $code .= "stack_js.request_access_to_input('" . $this->params['save_state'] . "', true).then((id) => {\n";
        $code .= "const input = document.getElementById(id);\n";
        //zum$code .= "input.addEventListener('change',(e)=>{";
        $code .= "input.value='Das ist ein test input';";
        $code .= '});';
            
        // $body->items[] = new MP_String('<script type=module>'.$code.'</script>');

        //Now add a hidden [[iframe]] with suitable scripts.
        $body->items[] = new MP_List([
            new MP_String('iframe'),
            new MP_String(json_encode(['hidden' => true, 'title' => 'Logic container for a adaptbutton ' .
                    self::$countadaptbuttons . '.'])),
            new MP_List([
                new MP_String('script'),
                new MP_String(json_encode(['type' => 'module'])),
                new MP_String($code)
            ])
        ]);

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

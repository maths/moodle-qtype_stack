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
//
defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../block.interface.php');
require_once(__DIR__ . '/../block.factory.php');

require_once(__DIR__ . '/root.specialblock.php');
require_once(__DIR__ . '/stack_translate.specialblock.php');
require_once(__DIR__ . '/../../../../vle_specific.php');

require_once(__DIR__ . '/iframe.block.php');
stack_cas_castext2_iframe::register_counter('///JAVASCRIPT_COUNT///');

/**
 * A convenience block for creation of iframes with input-references and
 * stack_js pre-loaded. Use when you want to build some logic connected
 * to the input values.
 *
 * The script will run in `<script type="module">` so feel free to 'import'
 * things.
 *
 * Uses the same input-references declaration logic as [[jsxgraph]].
 */
class stack_cas_castext2_javascript extends stack_cas_castext2_block {

    public function compile($format, $options):  ? MP_Node {
        $r = new MP_List([new MP_String('iframe')]);

        $inputs = []; // From inputname to variable name.
        foreach ($this->params as $key => $value) {
            if (substr($key, 0, 10) === 'input-ref-') {
                $inputname = substr($key, 10);
                $inputs[$inputname] = $value;
            }
        }

        // These will be hidden.
        $pars = ['hidden' => true];
        // Set a title.
        $pars['title'] = 'STACK javascript ///JAVASCRIPT_COUNT///';

        $r->items[] = new MP_String(json_encode($pars));

        // Start the script. We will always have type="module".
        $r->items[] = new MP_String('&nbsp;<script type="module">');

        // For binding and other use we need to import the stack_js library.
        $r->items[] = new MP_String("\nimport {stack_js} from '" . stack_cors_link('stackjsiframe.min.js') . "';\n");

        // Do we need to bind anything?
        if (count($inputs) > 0) {
            // Then we need to link up to the inputs.
            $promises = [];
            $vars = [];
            foreach ($inputs as $key => $value) {
                // That true there makes us sync input-events as well, like we did before.
                $promises[] = 'stack_js.request_access_to_input("' . $key . '",true)';
                $vars[] = $value;
            }
            $linkcode = 'Promise.all([' . implode(',', $promises) . '])';
            $linkcode .= '.then(([' . implode(',', $vars) . ']) => {' . "\n";
            $r->items[] = new MP_String($linkcode);
        }

        $opt2 = [];
        if ($options !== null) {
            $opt2 = array_merge([], $options);
        }
        $opt2['in iframe'] = true;

        foreach ($this->children as $item) {
            // Assume that all code inside is JavaScript and that we do not
            // want to do the markdown escaping or any other in it.
            $c = $item->compile(castext2_parser_utils::RAWFORMAT, $opt2);
            if ($c !== null) {
                $r->items[] = $c;
            }
        }

        if (count($inputs) > 0) {
            // Close the `then(`.
            $r->items[] = new MP_String("\n});");
        }

        // In the end close the script tag.
        $r->items[] = new MP_String('</script>');

        return $r;
    }

    public function is_flat() : bool {
        // Even when the content were flat we need to evaluate this during postprocessing.
        return false;
    }


    public function postprocess(array $params, castext2_processor $processor): string {
        return 'This is never happening! The logic goes to [[iframe]].';
    }

    public function validate_extract_attributes(): array {
        return [];
    }

    public function validate(
        &$errors = [],
        $options = []
    ): bool {
        $valid  = true;
        $err = [];
        $valids = null;
        foreach ($this->params as $key => $value) {
            if (substr($key, 0, 10) === 'input-ref-') {
                $varname = substr($key, 10);
                if (isset($options['inputs']) && !isset($options['inputs'][$varname])) {
                    $err[] = stack_string('stackBlock_javascript_input_missing',
                        ['var' => $varname]);
                }
            }
        }

        // Wrap the old string errors with the context details.
        foreach ($err as $er) {
            $errors[] = new $options['errclass']($er, $options['context'] . '/' . $this->position['start'] . '-' .
                $this->position['end']);
        }

        return $valid;
    }
}

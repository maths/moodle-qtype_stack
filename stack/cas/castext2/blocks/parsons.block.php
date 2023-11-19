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
require_once(__DIR__ . '/../block.factory.php');

require_once(__DIR__ . '/root.specialblock.php');
require_once(__DIR__ . '/stack_translate.specialblock.php');
require_once(__DIR__ . '/../../../../vle_specific.php');

require_once(__DIR__ . '/iframe.block.php');
stack_cas_castext2_iframe::register_counter('///PARSONS_COUNT///');

class stack_cas_castext2_parsons extends stack_cas_castext2_block {

    /* This is not something we want people to edit in general. */
    public static $namedversions = [
        'cdn' => [
            'js' => 'https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js',
        ],
        'local' => [
            'css' => 'cors://sortable.min.css',
            'js' => 'cors://sortable.min.js'
        ]
    ];

    public function compile($format, $options):  ? MP_Node {
        $r = new MP_List([new MP_String('iframe')]);

        // Define iframe params.
        $xpars = [];
        $inputs = []; // From inputname to variable name.
        $clone = "false"; // Whether to have all keys in available list cloned.
        foreach ($this->params as $key => $value) {
            if ($key === 'clone') {
                $clone = $value;
            } else if ($key !== 'input') {
                $xpars[$key] = $value;
            } else {
                $inputs[$key] = $value;
            }
        }
        // These are some of the other parameters we do not need to push forward.
        if (isset($xpars['version'])) {
            unset($xpars['version']);
        }
        if (isset($xpars['overridecss'])) {
            unset($xpars['overridecss']);
        }
        if (isset($xpars['overridejs'])) {
            unset($xpars['overridejs']);
        }
        if (isset($xpars['orientation'])) {
            unset($xpars['orientation']);
        }

        // Set default width and height here. 
        // We want to push forward to overwrite the iframe defaults if they are not provided in the block parameters.
        $width = array_key_exists('width', $xpars) ? $xpars['width'] : "100%";
        // TODO: set default based on number of proof steps
        /*$opt2 = [];
        if ($options !== null) {
            $opt2 = array_merge([], $options);
        }
        $opt2['in iframe'] = true;
        /*foreach ($this->children as $item) {
            // Assume that all code inside is JavaScript and that we do not
            // want to do the markdown escaping or any other in it.
            $c = $item->compile(castext2_parser_utils::RAWFORMAT, $opt2);
            if ($c !== null) {
                $num_steps = $c->;
            };
        };
        $num_steps = $this->children[1]->compile(castext2_parser_utils::RAWFORMAT, $opt2)->arguments[2]->arguments[1];*/
        //$num_steps = ($this->children[0]->compile(castext2_parser_utils::RAWFORMAT, $opt2))->value;
        $height = array_key_exists('height', $xpars) ? $xpars['height'] : "400px";
        $xpars['width'] = $width;
        $xpars['height'] = $height;

        // Set a title.
        $xpars['title'] = 'STACK Parsons ///PARSONS_COUNT///';

        // Figure out what scripts we serve.
        $css = self::$namedversions['local']['css'];
        $js = self::$namedversions['local']['js'];
        if (isset($this->params['version']) &&
            isset(self::$namedversions[$this->params['version']])) {
            $css = self::$namedversions['local']['css'];
            $js = self::$namedversions[$this->params['version']]['js'];
        }
        if (isset($this->params['overridecss'])) {
            $css = $this->params['overridecss'];
        }
        if (isset($this->params['overridejs'])) {
            $js = $this->params['overridejs'];
        }

        $r->items[] = new MP_String(json_encode($xpars));

        // Plug in some style and scripts.
        $mathjax = stack_get_mathjax_url();
        $r->items[] = new MP_List([
            new MP_String('script'),
            new MP_String(json_encode(['type' => 'text/javascript', 'src' => $mathjax]))]);
        //$r->items[] = new MP_String('<script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>');
        $r->items[] = new MP_List([
            new MP_String('style'),
            new MP_String(json_encode(['href' => $css]))
        ]);
        $r->items[] = new MP_List([
            new MP_String('script'),
            new MP_String(json_encode(['type' => 'text/javascript', 'src' => $js]))
        ]);

        // We need to define a size for the inner content.
        $aspectratio = false;
        $innerwidth = substr($width, -1) === 'px' ? "$width - 3px" : "100% - 3px";
        $innerheight = substr($width, -1) === 'px' ? "$height - 3px" : "100% - 3px";
        $astyle = "width:calc($innerwidth);height:calc($innerheight);";

        if (array_key_exists('aspect-ratio', $xpars)) {
            $aspectratio = $xpars['aspect-ratio'];
            // Unset the undefined dimension, if both are defined then we have a problem.
            if (array_key_exists('height', $xpars)) {
                $astyle = "height:calc($innerheight);aspect-ratio:$aspectratio;";
            } else if (array_key_exists('width', $xpars)) {
                $astyle = "width:calc($innerwidth);aspect-ratio:$aspectratio;";
            }
        }

        // Add correctly oriented container divs for the proof lists to be accessed by sortable.

        $orientation = isset($this->params['orientation']) ? $this->params['orientation'] : 'horizontal';
        $outer = $orientation === 'horizontal' ? 'row' : 'col';
        $inner = $orientation === 'horizontal' ? 'col' : 'row';
        $innerui = '<ul class="list-group ' . $inner . '" id="usedList"></ul>
                        <ul class="list-group ' . $inner . '" id="availableList"></ul>';
        if ($clone === 'true') {
            $innerui .= '<ul class="list-group ' . $inner . '" id="bin"></ul>';
        }
        $r->items[] = new MP_String('<button> Orientation </button>');
        $r->items[] = new MP_String('<div class="container" style="' . $astyle . '">
            <div class=row>' . $innerui . '
            </div>
        </div>');

        // JS script.
        $r->items[] = new MP_String('<script type="module">');

        $importcode = "\nimport {stack_js} from '" . stack_cors_link('stackjsiframe.min.js') . "';\n";
        $importcode .= "import {Sortable} from '" . stack_cors_link('sortable.min.js') . "';\n";
        $importcode .= "import {preprocess_steps, stack_sortable, add_orientation_listener} from '" .
            stack_cors_link('stacksortable.min.js') . "';\n";
        $r->items[] = new MP_String($importcode);
        // TODO :automatically set orientation based on device.
        $r->items[] = new MP_String('add_orientation_listener();' . "\n");
        // Extract the proof steps from the inner content.
        $r->items[] = new MP_String('var proofSteps = ');

        $opt2 = [];
        if ($options !== null) {
            $opt2 = array_merge([], $options);
        }
        $opt2['in iframe'] = true;

        foreach ($this->children as $item) {
            // Assume that all code inside is JavaScript.
            // Assume we do not want to do the markdown escaping or any other in it.
            $c = $item->compile(castext2_parser_utils::RAWFORMAT, $opt2);
            if ($c !== null) {
                $r->items[] = $c;
            }
        }

        // Parse steps and Sortable options separately if they exist.
        $code = 'var userOpts;' . "\n";
        $code .= '[proofSteps, userOpts] = preprocess_steps(proofSteps, userOpts);' . "\n";

        // Link up to STACK inputs.
        if (count($inputs) > 0) {
            $code .= 'var inputPromise = stack_js.request_access_to_input("' . $this->params['input'] . '", true);';
            $code .= "\n";
            $code .= 'inputPromise.then((id) => {' . "\n";
        } else {
            $code .= 'var id;' . "\n";
        };

        // Instantiate STACK sortable helper class.
        $code .= 'const stackSortable = new stack_sortable(proofSteps, "availableList", "usedList", id, userOpts, "' .
                $clone .'");' . "\n";
        // Generate the two lists in HTML.
        $code .= 'stackSortable.generate_used();' . "\n";
        $code .= 'stackSortable.generate_available();' . "\n";

        // Create the Sortable objects.
        $code .= 'var usedOpts = {...stackSortable.options.used, ...{onSort: () => ' .
                '{stackSortable.update_state(sortableUsed, sortableAvailable);}}}' . "\n";
        $code .= 'var availableOpts = {...stackSortable.options.available, ' .
                '...{onSort: () => {stackSortable.update_state(sortableUsed, sortableAvailable);}}}' . "\n";
        $code .= 'var sortableUsed = Sortable.create(usedList, usedOpts);' . "\n";
        $code .= 'var sortableAvailable = Sortable.create(availableList, availableOpts);' . "\n";

        // Create bin for clone mode.
        if ($clone === "true") {
            $code .= 'var sortableBin = Sortable.create(bin, {group: {name: "sortableBin", pull: false, put: ' .
                '"sortableUsed"}, onAdd: (e) => {document.getElementById("bin").removeChild(e.item);}});' . "\n";
        }

        // Add double-click events.
        $code .= 'stackSortable.update_state_dblclick(sortableUsed, sortableAvailable);' . "\n";

        // Typeset MathJax.
        $code .= 'MathJax.Hub.Queue(["Typeset", MathJax.Hub]);' . "\n";

        if (count($inputs) > 0) {
            $code .= "\n});";
        };

        $r->items[] = new MP_String($code);
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

    public function validate_JSON_contents($contents) : bool {
        // TODO : check steps are reasonable.
        $val_types = array_unique(array_map('gettype', array_values($contents)));
        return array_keys($contents) === ["steps", "options"] || (count($val_types) == 1 && $val_types[0] == "string");
    }

    public function validate (
        &$errors = [],
        $options = []
    ): bool {
        // Basically, check that the dimensions have units we know.
        // Also that the references make sense.
        $valid  = true;
        $width  = array_key_exists('width', $this->params) ? $this->params['width'] : '100%';
        $height = array_key_exists('height', $this->params) ? $this->params['height'] : '400px';

        // NOTE! List ordered by length. For the trimming logic.
        $validunits = ['vmin', 'vmax', 'rem', 'em', 'ex', 'px', 'cm', 'mm',
            'in', 'pt', 'pc', 'ch', 'vh', 'vw', '%'];

        $widthend   = false;
        $heightend  = false;
        $widthtrim  = $width;
        $heighttrim = $height;

        foreach ($validunits as $suffix) {
            if (!$widthend && strlen($width) > strlen($suffix) &&
                substr($width, -strlen($suffix)) === $suffix) {
                $widthend  = true;
                $widthtrim = substr($width, 0, -strlen($suffix));
            }
            if (!$heightend && strlen($height) > strlen($suffix) &&
                substr($height, -strlen($suffix)) === $suffix) {
                $heightend  = true;
                $heighttrim = substr($height, 0, -strlen($suffix));
            }
            if ($widthend && $heightend) {
                break;
            }
        }
        $err = [];

        if (!$widthend) {
            $valid    = false;
            $err[] = stack_string('stackBlock_parsons_width');
        }
        if (!$heightend) {
            $valid    = false;
            $err[] = stack_string('stackBlock_parsons_height');
        }
        if (!preg_match('/^[0-9]*[\.]?[0-9]+$/', $widthtrim)) {
            $valid    = false;
            $err[] = stack_string('stackBlock_parsons_width_num');
        }
        if (!preg_match('/^[0-9]*[\.]?[0-9]+$/', $heighttrim)) {
            $valid    = false;
            $err[] = stack_string('stackBlock_parsons_height_num');
        }

        if (array_key_exists('width', $this->params) &&
            array_key_exists('height', $this->params) &&
            array_key_exists('aspect-ratio', $this->params)) {
            $valid    = false;
            $err[] = stack_string('stackBlock_parsons_overdefined_dimension');
        }
        if (!(array_key_exists('width', $this->params) ||
            array_key_exists('height', $this->params)) &&
            array_key_exists('aspect-ratio', $this->params)) {
            $valid    = false;
            $err[] = stack_string('stackBlock_parsons_underdefined_dimension');
        }

        // Check version is only one of valid options.
        if (array_key_exists('version', $this->params) && !array_key_exists($this->params['version'],
                self::$namedversions)) {
            $valid    = false;
            $validversions = ['cdn', 'local'];
            $err[] = stack_string('stackBlock_parsons_unknown_named_version', ['version' => implode(', ',
                $validversions)]);
        }

        // Check that only valid parameters are passed to block header.
        $valids = null;
        foreach ($this->params as $key => $value) {
            if ($key !== 'width' && $key !== 'height' && $key !== 'aspect-ratio' &&
                    $key !== 'version' && $key !== 'overridecss' && $key !== 'input' &&
                    $key !== 'orientation' && $key !== 'clone') {
                $err[] = "Unknown parameter '$key' for Parson's block.";
                $valid    = false;
                if ($valids === null) {
                    $valids = ['width', 'height', 'aspect-ratio', 'version', 'overridecss',
                        'overridejs', 'input', 'orientation', 'clone'];
                    $err[] = stack_string('stackBlock_parsons_param', [
                        'param' => implode(', ', $valids)]);
                }
            }
        }

        // Check the JSON contents are of the right format.
        // Either the depth is 1 or the depth is 2 and the keys are ['steps', 'options'].
        $contents = json_decode(($this->children[0]->compile(castext2_parser_utils::RAWFORMAT, []))->value, true);
        // Either this is a string (when using Maxima and stackjson_stringify) or it's a JSON.
        // The former case we sanitise on the JS side so we can ignore this here.
        if (!gettype($contents) == "string") {
            if (!self::validate_JSON_contents(json_decode(($this->children[0]->compile(castext2_parser_utils::RAWFORMAT,
                    []))->value, true))) {
                $err[] = stack_string('stackBlock_parsons_contents');
            }
        };

        // Wrap the old string errors with the context details.
        foreach ($err as $er) {
            $errors[] = new $options['errclass']($er, $options['context'] . '/' . $this->position['start'] . '-' .
                $this->position['end']);
        }

        return $valid;
    }
}

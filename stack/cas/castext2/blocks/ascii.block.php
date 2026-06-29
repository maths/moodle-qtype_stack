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

/**
 * An ASCIIMaths display block - e.g. [[ascii input="ans1"]][[/ascii]]
 * @package    qtype_stack
 * @copyright  2024 University of Edinburgh.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../block.interface.php');
require_once(__DIR__ . '/../block.factory.php');

require_once(__DIR__ . '/root.specialblock.php');
require_once(__DIR__ . '/stack_translate.specialblock.php');
require_once(__DIR__ . '/../../../../vle_specific.php');

require_once(__DIR__ . '/iframe.block.php');
stack_cas_castext2_iframe::register_counter('///ASCII_COUNT///');

// phpcs:ignore moodle.Commenting.MissingDocblock.Class
class stack_cas_castext2_ascii extends stack_cas_castext2_block {
    // phpcs:ignore moodle.Commenting.MissingDocblock.Function
    public function compile($format, $options): ?MP_Node {
        $r = new MP_List([new MP_String('iframe')]);

        // Define iframe params.
        $xpars = [];
        $inputs = [];

        foreach ($this->params as $key => $value) {
            if ($key === 'input') {
                $inputs[] = $value;
            } else if ($key === 'hidden') {
                $xpars[$key] = ($value === 'true');
            } else {
                $xpars[$key] = $value;
            }
        }

        // Get the details of filter and extractor blocks.
        $operations = [];
        // Is the markdown (maths) default filter needed?
        $filterneeded = true;
        $suppliedtext = [];
        $opt2 = [];
        if ($options !== null) {
            $opt2 = array_merge([], $options);
        }
        $opt2['in iframe'] = true;
        foreach ($this->children as $child) {
            if (is_a($child, 'stack_cas_castext2_extractor')) {
                $options = $child->params;
                $options['operation'] = 'extractor';
                $inputs[] = $options['targetinput'];
                $operations[] = $options;
            } else if (is_a($child, 'stack_cas_castext2_filter')) {
                $filterneeded = false;
                $options = $child->params;
                $options['operation'] = 'filter';
                if ($options['type'] == 'markdown-math') {
                    $options['type'] = 'markdown';
                    $transforms = '';
                    if (array_key_exists('transforms', $options)) {
                        $transforms = $options['transforms'];
                    }
                    $options['transforms'] = $this->set_markdown_filter_defaults($transforms);
                }
                $operations[] = $options;
            } else {
                $c = $child->compile(castext2_parser_utils::RAWFORMAT, $opt2);
                if ($c !== null) {
                    $suppliedtext[] = $c;
                }
            }
        }

        // Is the markdown (maths) default filter needed?
        if ($filterneeded) {
            $defaultmarkdown = [
                'operation'  => 'filter',
                'type'       => 'markdown',
                'transforms' => 'asciimath,aligneq,minwrap',
            ];
            array_unshift($operations, $defaultmarkdown);
        }

        // Set default width and height here.
        // We want to push forward to overwrite the iframe defaults if they are not provided in the block parameters.
        $existsuserwidth = array_key_exists('width', $xpars);
        $existsuserheight = array_key_exists('height', $xpars);
        $width = $existsuserwidth ? $xpars['width'] : "100%";
        $height = $existsuserheight ? $xpars['height'] : "400px";
        $xpars['width'] = $width;
        $xpars['height'] = $height;

        // Set a title.
        $xpars['title'] = 'STACK ASCII ///ASCII_COUNT///';

        $r->items[] = new MP_String(json_encode($xpars));

        // Plug in some style and scripts.
        $mathjax = stack_get_mathjax_url();
        $r->items[] = new MP_List([
            new MP_String('script'),
            new MP_String(json_encode(['type' => 'text/javascript', 'src' => $mathjax])),
        ]);
        // ASCIIMathTeXImg must load as a plain script (not bundled) because it uses
        // undeclared variables that are incompatible with ESM strict mode.
        $r->items[] = new MP_List([
            new MP_String('script'),
            new MP_String(json_encode(['type' => 'text/javascript', 'src' => 'cors://ascii/ASCIIMathTeXImg.min.js'])),
        ]);
        $r->items[] = new MP_List([
            new MP_String('style'),
            new MP_String(json_encode(['href' => 'cors://ascii/stackascii.css'])),
        ]);

        // Size from the iframe viewport so the display area tracks frame resizing.
        // Keep a configured minimum so default 400px frames start at 370px content height.
        $astyle = "width:calc({$xpars['width']} - 20px);height:calc(100vh - 30px);";
        $astyle .= "min-height:calc({$xpars['height']} - 30px);";
        if (array_key_exists('aspect-ratio', $xpars)) {
            $aspectratio = $xpars['aspect-ratio'];
            // Unset the undefined dimension, if both are defined then we have a problem.
            if ($existsuserheight) {
                $astyle = "height:calc(100vh - 30px);aspect-ratio:$aspectratio;";
                $astyle .= "min-height:calc({$xpars['height']} - 30px);";
            } else if ($existsuserwidth) {
                $astyle = "width:calc({$xpars['width']} - 20px);aspect-ratio:$aspectratio;";
            }
        }
        $r->items[] = new MP_String('<script type="module">');
        $r->items[] = new MP_String("\nimport stack_js from '" . stack_cors_link('stackjsiframe.min.js') . "';\n");
        $r->items[] = new MP_String("\nimport init from '" . stack_cors_link('ascii/stackascii.bundle.js') . "';\n");

        $answercalls = implode(',', array_map(function ($item, $index) {
            $extra = $index === 0 ? ',true' : '';
            return 'stack_js.request_access_to_input("' . $item . '"' . $extra . ')';
        }, $inputs, array_keys($inputs)));
        $linkcode = 'Promise.all([' . $answercalls . '])';
        $linkcode .= ".then((inputIds) => {init(inputIds," . json_encode($operations) . ");});";

        $r->items[] = new MP_String($linkcode);
        $r->items[] = new MP_String("\n</script>");
        $r->items[] = new MP_String('<textarea id="asciiSuppliedText" style="display:none;">');
        $r->items = array_merge($r->items, $suppliedtext);
        $r->items[] = new MP_String('</textarea>');

        $r->items[] = new MP_String('<div class="container row asciimath" id="asciiContainerRow" style="' . $astyle . '"></div>');

        return $r;
    }

    /**
     * Ensure the markdown filter transforms have the correct defaults.
     * @param mixed $transforms
     * @return string
     */
    private function set_markdown_filter_defaults($transforms) {
        // Sort out the default transforms.
        $asccimathneeded = true;
        $minwrapneeded = true;
        $transforms = array_fill_keys(array_map('trim', explode(',', $transforms)), null);
        if (array_key_exists('asciimath', $transforms)) {
            $asccimathneeded = false;
        }
        if (array_key_exists('minwrap', $transforms)) {
            $minwrapneeded = false;
        }
        if ($asccimathneeded) {
            $transforms = array_merge(['asciimath' => null], $transforms);
        }
        if ($minwrapneeded) {
            $transforms = array_merge($transforms, ['minwrap' => null]);
        }
        return(implode(',', array_keys($transforms)));
    }

    // phpcs:ignore moodle.Commenting.MissingDocblock.Function
    public function is_flat(): bool {
        // Even when the content were flat we need to evaluate this during postprocessing.
        return false;
    }

    // phpcs:ignore moodle.Commenting.MissingDocblock.Function
    public function postprocess(
        array $params,
        castext2_processor $processor,
        castext2_placeholder_holder $holder
    ): string {
        return 'This is never happening! The logic goes to [[iframe]].';
    }

    // phpcs:ignore moodle.Commenting.MissingDocblock.Function
    public function validate_extract_attributes(): array {
        return [];
    }

    // phpcs:ignore moodle.Commenting.MissingDocblock.Function
    public function validate(
        &$errors = [],
        $options = []
    ): bool {
        global $CFG;
        // Basically, check that the dimensions have units we know.
        // Also that the references make sense.
        $valid  = true;
        $width  = array_key_exists('width', $this->params) ? $this->params['width'] : '100%';
        $height = array_key_exists('height', $this->params) ? $this->params['height'] : '400px';

        // NOTE! List ordered by length. For the trimming logic.
        $validunits = [
            'vmin', 'vmax', 'rem', 'em', 'ex', 'px', 'cm', 'mm',
            'in', 'pt', 'pc', 'ch', 'vh', 'vw', '%',
        ];

        $widthend   = false;
        $heightend  = false;
        $widthtrim  = $width;
        $heighttrim = $height;

        foreach ($validunits as $suffix) {
            if (
                !$widthend && strlen($width) >= strlen($suffix) &&
                substr($width, -strlen($suffix)) === $suffix
            ) {
                $widthend  = true;
                $widthtrim = substr($width, 0, -strlen($suffix));
            }
            if (
                !$heightend && strlen($height) >= strlen($suffix) &&
                substr($height, -strlen($suffix)) === $suffix
            ) {
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
            $err[] = stack_string('stackBlock_ascii_width');
        } else if (!preg_match('/^[0-9]*[\.]?[0-9]+$/', $widthtrim)) {
            $valid    = false;
            $err[] = stack_string('stackBlock_ascii_width_num');
        }
        if (!$heightend) {
            $valid    = false;
            $err[] = stack_string('stackBlock_ascii_height');
        } else if (!preg_match('/^[0-9]*[\.]?[0-9]+$/', $heighttrim)) {
            $valid    = false;
            $err[] = stack_string('stackBlock_ascii_height_num');
        }

        if (
            array_key_exists('width', $this->params) &&
            array_key_exists('height', $this->params) &&
            array_key_exists('aspect-ratio', $this->params)
        ) {
            $valid    = false;
            $err[] = stack_string('stackBlock_ascii_overdefined_dimension');
        }
        if (
            !(array_key_exists('width', $this->params) ||
            array_key_exists('height', $this->params)) &&
            array_key_exists('aspect-ratio', $this->params)
        ) {
            $valid    = false;
            $err[] = stack_string('stackBlock_ascii_underdefined_dimension');
        }

        // Check that only valid parameters are passed to block header.
        $valids = null;
        foreach ($this->params as $key => $value) {
            if (
                $key !== 'width' &&
                $key !== 'height' &&
                $key !== 'aspect-ratio' &
                $key !== 'input' &&
                $key !== 'hidden'
            ) {
                $err[] = stack_string('stackBlock_ascii_unknown_param', $key);
                $valid    = false;
                if ($valids === null) {
                    $valids = [
                        'width', 'height', 'aspect-ratio', 'input', 'hidden',
                    ];
                    $err[] = stack_string('stackBlock_ascii_param', [
                        'param' => implode(', ', $valids),
                    ]);
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

    /**
     * Is this an interactive block?
     * If true, we can't generate a static version.
     * @return bool
     */
    public function is_interactive(): bool {
        return true;
    }
}

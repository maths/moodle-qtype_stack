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
require_once(__DIR__ . '/../../../utils.class.php');

/**
 * A block for providing means for creating IFRAMES.
 *
 * Is the basis for a family of blocks that need to do things
 * inside IFRAMEs for security reasons. All JavaScript logic
 * goes into these.
 *
 * Also related to [[style]], [[body]], [[script]] blocks
 * that allow targetted content within this block.
 */
class stack_cas_castext2_iframe extends stack_cas_castext2_block {

    // All frames need unique (at request level) identifiers,
    // we use running numbering.
    private static $counters = ['///IFRAME_COUNT///' => 1];

    // Add separate running numbering for different block types to
    // ease debugging, so that one does not need to know which all affect
    // the numbers. This numbering applies only to the titles.
    public static function register_counter(string $name): void {
        self::$counters[$name] = 1;
    }

    public function compile($format, $options): ?MP_Node {
        $r = new MP_List([
            new MP_String('iframe'),
            new MP_String(json_encode($this->params))
        ]);

        // All formatting assumed to be raw HTML here.
        $frmt = castext2_parser_utils::RAWFORMAT;

        $opt2 = [];
        if ($options !== null) {
            $opt2 = array_merge([], $options);
        }
        $opt2['in iframe'] = true;

        // Note that [[style]], [[body]], [[script]] blocks will be separated during post-processing.
        foreach ($this->children as $child) {
            $c = $child->compile(castext2_parser_utils::RAWFORMAT, $opt2);
            if ($c !== null) {
                $r->items[] = $c;
            }
        }

        return $r;
    }

    public function is_flat(): bool {
        // These are never flat.
        return false;
    }

    public function validate_extract_attributes(): array {
        // No CAS arguments.
        return [];
    }

    public function postprocess(array $params, castext2_processor $processor): string {
        global $PAGE;

        if (count($params) < 3) {
            // Nothing at all.
            return '';
        }

        $divid  = 'stack-iframe-holder-' . self::$counters['///IFRAME_COUNT///'];
        $frameid  = 'stack-iframe-' . self::$counters['///IFRAME_COUNT///'];

        $parameters = json_decode($params[1], true);
        $content    = '';
        $style      = '';
        $scripts    = '<script>const FRAME_ID = "' . $frameid . '";</script>';
        for ($i = 2; $i < count($params); $i++) {
            if (is_array($params[$i])) {
                if ($params[$i][0] === 'style') {
                    $style .= $processor->process($params[$i][0], $params[$i]);
                } else if ($params[$i][0] === 'script') {
                    $scripts .= $processor->process($params[$i][0], $params[$i]);
                } else {
                    $content .= $processor->process($params[$i][0], $params[$i]);
                }
            } else {
                $content .= $params[$i];
            }
        }

        $width  = '500px';
        $height = '400px';
        $aspectratio = false;
        if (array_key_exists('width', $parameters)) {
            $width = $parameters['width'];
        }
        if (array_key_exists('height', $parameters)) {
            $height = $parameters['height'];
        }

        $astyle = "width:$width;height:$height;";

        if (array_key_exists('aspect-ratio', $parameters)) {
            $aspectratio = $parameters['aspect-ratio'];
            // Unset the undefined dimension, if both are defined then we have a problem.
            if (array_key_exists('height', $parameters)) {
                $astyle = "height:$height;aspect-ratio:$aspectratio;";
            } else if (array_key_exists('width', $parameters)) {
                $astyle = "width:$width;aspect-ratio:$aspectratio;";
            }
        }

        // Special option for scripting only style frames.
        if (isset($parameters['hidden']) && $parameters['hidden']) {
            $astyle .= 'display:none;';
        }

        $attributes = ['style' => $astyle, 'id' => $divid];

        if ($content === '') {
            // For now we ensure that the created document will always have some content.
            $content = '&nbsp;';
        }

        // Some form of title for debug and accessibility.
        $title = 'STACK IFRAME ' . self::$counters['///IFRAME_COUNT///'];
        if (isset($parameters['title'])) {
            $title = $parameters['title'];
            // Counter updates.
            foreach (self::$counters as $key => $value) {
                if (strpos($title, $key) !== false) {
                    $title = str_replace($key, '' . $value, $title);
                    self::$counters[$key] = $value + 1;
                }
            }
        }
        $scrolling = true;
        if (isset($parameters['scrolling'])) {
            $scrolling = $parameters['scrolling'];
        }

        // Construct the contents of the IFRAME.
        $code = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $code .= '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"' .
            ' "http://www.w3.org/TR/xhtml1/DTD/strict.dtd">' . "\n";
        $code .= '<html xmlns="http://www.w3.org/TR/xhtml1/strict">';
        // Include a title to help JS debugging.
        $code .= '<head><title>' . $title . '</title>';
        $code .= $style;
        $code .= $scripts;
        $code .= '</head><body style="margin:0px;">' . $content . '</body></html>';

        // Escape some JavaScript strings.
        $args = [
            json_encode($frameid),
            json_encode($code),
            json_encode($divid),
            json_encode($title),
            $scrolling ? 'true' : 'false'
        ];

        // As the content is large we cannot simply use the js_amd_call.
        $PAGE->requires->js_amd_inline(
            'require(["qtype_stack/stackjsvle"], '
            . 'function(stackjsvle,){stackjsvle.create_iframe(' . implode(',', $args). ');});');

        self::$counters['///IFRAME_COUNT///'] = self::$counters['///IFRAME_COUNT///'] + 1;

        // Output the placeholder for this frame.
        return html_writer::tag('div', '', $attributes);
    }

    public function validate(&$errors=[], $options=[]): bool {
        // Basically, check that the dimensions have units we know.
        // Also that the references make sense.
        $valid  = true;
        $width  = '500px';
        $height = '400px';
        if (array_key_exists('width', $this->params)) {
            $width = $this->params['width'];
        }
        if (array_key_exists('height', $this->params)) {
            $height = $this->params['height'];
        }

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
            $err[] = stack_string('stackBlock_iframe_width');
        }
        if (!$heightend) {
            $valid    = false;
            $err[] = stack_string('stackBlock_iframe_height');
        }
        if (!preg_match('/^[0-9]*[\.]?[0-9]+$/', $widthtrim)) {
            $valid    = false;
            $err[] = stack_string('stackBlock_iframe_width_num');
        }
        if (!preg_match('/^[0-9]*[\.]?[0-9]+$/', $heighttrim)) {
            $valid    = false;
            $err[] = stack_string('stackBlock_iframe_height_num');
        }

        if (array_key_exists('width', $this->params) &&
            array_key_exists('height', $this->params) &&
            array_key_exists('aspect-ratio', $this->params)) {
            $valid    = false;
            $err[] = stack_string('stackBlock_iframe_overdefined_dimension');
        }
        if (!(array_key_exists('width', $this->params) ||
            array_key_exists('height', $this->params)) &&
            array_key_exists('aspect-ratio', $this->params)) {
            $valid    = false;
            $err[] = stack_string('stackBlock_iframe_underdefined_dimension');
        }

        return $valid;
    }
}

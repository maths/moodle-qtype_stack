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

/**
 * GeoGebra block for STACK derived by jsxGraph STACK implementation
 *
 * The creation of these resources has been (partially) funded by the ERASMUS+ grant
 * program of the European Union under grant No. 2021-1-DE01-KA220-HED-000032031.
 * Neither the European Commission nor the project's national funding agency DAAD
 * are responsible for the content or liable for any losses or damage resulting
 * of the use of these resources.
 *
 * @copyright  2022-2023 University of Edinburgh
 * @author     Tim Lutz
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../block.interface.php');
require_once(__DIR__ . '/../block.factory.php');

require_once(__DIR__ . '/root.specialblock.php');
require_once(__DIR__ . '/stack_translate.specialblock.php');

class stack_cas_castext2_geogebra extends stack_cas_castext2_block {
    private static $countgraphs = 1;

    // Compatibility with php 7.4: Defining "str_ends_with" if not in existence, delete this function when
    // dropping support for php 7.4, replace all occurences of this->str_ends_with(args) by str_ends_with(args).
    private function str_ends_with($word, $searchstring) {
        $searchstringlen = mb_strlen($searchstring);
        if (mb_substr($word, -$searchstringlen, $searchstringlen) == $searchstring) {
            return true;
        }
        return false;
    }

    public function compile($format, $options): ?MP_Node {
        // We are outputting as [[iframe]], so we will generate some parameters for it on the side.
        $r = new MP_List([new MP_String('iframe')]);
        $iparams = ['scrolling' => false];
        // For now we run without the sandbox, THIS NEES TO BE FIXED WITH GEOGEBRA SIDES HELP..
        $iparams['no sandbox'] = true;
        // This is the only place where we count graphs.
        // And even this can be removed once the fix for #969 comes in.
        $iparams['title'] = 'STACK GeoGebra ' . self::$countgraphs;
        self::$countgraphs = self::$countgraphs + 1;

        // TO-DO:
        // 1. Do we need to load some CSS as well?

        // The bits of code we construct. We could simply output these into
        // the same output variable as these are not coming in mixed but let's
        // keep the code clean.
        $setcode = new MP_FunctionCall(new MP_Identifier('sconcat'),
            [new MP_String("function initialgeogebraset(){};\n")]);
        $remembercode = new MP_FunctionCall(new MP_Identifier('sconcat'),
            [new MP_String("function rememberGeoGebraObjects(){};\n")]);
        $watchcode = new MP_FunctionCall(new MP_Identifier('sconcat'),
            [new MP_String("function watchGeoGebraObjects(){};\n")]);

        // Start by identifying the inputs we deal with.
        $inputmapping = [];
        foreach ($this->params as $key => $value) {
            // TO-DO: are these actually a thing?
            if (substr($key, 0, 10) === 'input-ref-') {
                $inputname = substr($key, 10);
                $inputmapping[$value] = $inputname;
            }
        }
        foreach (['watch', 'remember'] as $param) {
            if (isset($this->params[$param])) {
                // If need be fill in the necessary bits of these functions.
                if ($param === 'watch') {
                    $watchcode->arguments[0]->value =
                        "function watchGeoGebraObjects(){\n var appletObject = applet.getAppletObject();";
                } else if ($param === 'remember') {
                    $remembercode->arguments[0]->value =
                        "function rememberGeoGebraObjects(){\n var appletObject = applet.getAppletObject();";
                }

                $ids = explode(',', $this->params[$param]);
                foreach ($ids as $geogebraname) {
                    $geogebraname = trim($geogebraname);
                    if ($param === 'watch') {
                        // Points and values/angles are different.
                        if (ctype_upper(substr($geogebraname, 0, 1))) {
                            $watchcode->arguments[] = new MP_String(
                                    "\n stack_geogebra.bind_point(watchvar" .
                            $geogebraname . ",appletObject,\"" .
                            $geogebraname .
                            "\");");
                        } else {
                            $watchcode->arguments[] = new MP_String(
                                    "\n stack_geogebra.bind_value(watchvar" .
                            $geogebraname . ",appletObject,\"" .
                            $geogebraname .
                            "\");");
                        }
                        $inputmapping['watchvar' . $geogebraname] = $geogebraname;
                    } else if ($param === 'remember') {
                        // Points and values/angles are different.
                        if (ctype_upper(substr($geogebraname, 0, 1))) {
                            $remembercode->arguments[] = new MP_String(
                                "\n stack_geogebra.bind_point_to_remember_JSON(remembervar" .
                            $geogebraname .
                            ",appletObject,\"" .
                            $geogebraname .
                            "\");");
                        } else {
                            $remembercode->arguments[] = new MP_String(
                                "\n stack_geogebra.bind_value_to_remember_JSON(remembervar" .
                            $geogebraname .
                            ",appletObject,\"" .
                            $geogebraname .
                            "\");");
                        }
                        $inputmapping['remembervar' . $geogebraname] = 'remember';
                    }
                }

                // Remember to close the function.
                if ($param === 'watch') {
                    $watchcode->arguments[] = new MP_String("\n};\n");
                } else if ($param === 'remember') {
                    $remembercode->arguments[] = new MP_String("\n};\n");
                }
            }
        }
        // TO-DO: as there was no dynamic content inside that loop might as well
        // directly generate as a singular MP_String, on the other hand
        // the simplifier during compilation will turn that to a string and
        // writing it like this makes it simpler to add any dynamic bits needed
        // in the future.

        // Here we include some CAS variables into the output so this will
        // be a mixture of static string segments and `string`-calls.
        if (isset($this->params['set'])) {
            $setcode->arguments[0]->value =
                "function initialgeogebraset(){\n var appletObject = applet.getAppletObject();";
            $setvars = explode(',', $this->params['set']);
            foreach ($setvars as $geogebraname) {
                $geogebraname = trim($geogebraname);
                $setfixed = false;
                $setpreserve = false;
                $sethide = false;
                $setshow = false;
                $setnovalue = false;

                // Identify suffixes.
                while (
                    $this->str_ends_with($geogebraname, '__fixed') ||
                    $this->str_ends_with($geogebraname, '__preserve') ||
                    $this->str_ends_with($geogebraname, '__hide') ||
                    $this->str_ends_with($geogebraname, '__show') ||
                    $this->str_ends_with($geogebraname, '__novalue')
                ) {
                    if ($this->str_ends_with($geogebraname, '__fixed')) {
                        // Assuming: point must not be interactable by the user.
                        $geogebraname = substr($geogebraname, 0, -7);
                        $setfixed = true;
                    }
                    if ($this->str_ends_with($geogebraname, '__preserve')) {
                        // Assuming: object should preserve its defintion, e.g. to be a point on an object.
                        $geogebraname = substr($geogebraname, 0, -10);
                        $setpreserve = true;
                    }
                    if ($this->str_ends_with($geogebraname, '__hide')) {
                        // Assuming: object should be hidden at startup.
                        $geogebraname = substr($geogebraname, 0, -6);
                        $sethide = true;
                    }
                    if ($this->str_ends_with($geogebraname, '__show')) {
                        // Assuming: object should be shown at startup.
                        $geogebraname = substr($geogebraname, 0, -6);
                        $setshow = true;
                    }
                    if ($this->str_ends_with($geogebraname, '__novalue')) {
                        // Assuming: object should not be set to a given value,
                        // keyword is useful in combination with __hide or __show.
                        $geogebraname = substr($geogebraname, 0, -9);
                        $setnovalue = true;
                    }
                }
                // Note at this point the name has no suffixes. No know ones that is, or non typoed ones...
                if (ctype_upper(substr($geogebraname, 0, 1))) {
                    // Assuming geogebraname is the (therefore uppercased) name of an object of type: point.
                    $xcoord = new MP_FunctionCall(new MP_Identifier('string'),
                        [new MP_Indexing(new MP_Identifier($geogebraname), [new MP_List ([new MP_Integer(1)])])]);
                    $ycoord = new MP_FunctionCall(new MP_Identifier('string'),
                        [new MP_Indexing(new MP_Identifier($geogebraname), [new MP_List ([new MP_Integer(2)])])]);
                    if ($setfixed) {
                        // Assuming point must not be interactable by the user.
                        // @codingStandardsIgnoreStart
                        // appletObject.evalCommand('POINTNAME= Point({XCOORD,YCOORD})
                        // @codingStandardsIgnoreEnd
                        // Removing __fixed (7 characters).
                        $setcode->arguments[] = new MP_String(
                            "\n appletObject.evalCommand('" .
                            $geogebraname . ' = Point({');
                        $setcode->arguments[] = $xcoord;
                        $setcode->arguments[] = new MP_String(',');
                        $setcode->arguments[] = $ycoord;
                        $setcode->arguments[] = new MP_String("})');\n");
                        // @codingStandardsIgnoreStart
                        // appletObject.evalCommand('G= Point({{#fx#},4})');
                        // @codingStandardsIgnoreEnd
                    } else if ($setpreserve) {
                        // Assuming point definition should be preserved while setting object:
                        // ATTENTION GGB Applet language must be English for this to work!
                        $setcode->arguments[] = new MP_String("\n appletObject.evalCommand('SetCoords(" .
                            $geogebraname .
                            ',');
                        $setcode->arguments[] = $xcoord;
                        $setcode->arguments[] = new MP_String(',');
                        $setcode->arguments[] = $ycoord;
                        $setcode->arguments[] = new MP_String(")');\n");
                    } else if ($setnovalue) {
                        // Assuming point value should not be set, useful when using __show/ __hide.
                        // NOOP.
                        $setnovalue = true;
                    } else {
                        // Assuming point is interactable by the user.
                        // @codingStandardsIgnoreStart
                        // appletObject.evalCommand('POINTNAME=(XCOORD,YCOORD)')
                        // @codingStandardsIgnoreEnd
                        $setcode->arguments[] = new MP_String("\n appletObject.evalCommand('" .
                            $geogebraname .
                            ' = (');
                        $setcode->arguments[] = $xcoord;
                        $setcode->arguments[] = new MP_String(',');
                        $setcode->arguments[] = $ycoord;
                        $setcode->arguments[] = new MP_String(")');\n");
                    }
                } else {
                    // Assuming geogebraname is the name of an object of type: value or angle (therefore latin lowercase)
                    // setting angle by size not supported.
                    // Please set angle by setting defining points.
                    if ($setpreserve) {
                        $setcode->arguments[] = new MP_String("\n appletObject.evalCommand('SetValue(" .
                            $geogebraname . ',');
                        $setcode->arguments[] = new MP_FunctionCall(new MP_Identifier('string'),
                            [new MP_Identifier($geogebraname)]);
                        $setcode->arguments[] = new MP_String(")');\n");
                    } else if ($setnovalue) {
                        // Assuming value should not be set, useful when using __show/ __hide.
                        // NOOP.
                        $setnovalue = true;
                    } else {
                        $setcode->arguments[] = new MP_String("\n appletObject.evalCommand('" .
                            $geogebraname . ' = ');
                        $setcode->arguments[] = new MP_FunctionCall(new MP_Identifier('string'),
                            [new MP_Identifier($geogebraname)]);
                        $setcode->arguments[] = new MP_String("');\n");
                    }
                }
                // End of section for setting values according to suffix.

                // Section to show or hide objects if there are according suffix readings.
                if ($setshow) {
                    // Assuming: object should be shown in view 1 (use case: if object is hidden by default).
                    $setcode->arguments[] = new MP_String(
                        "\n appletObject.evalCommand('SetVisibleInView(" .
                        $geogebraname .
                        ",1,true)');\n");
                } else if ($sethide) {
                    // Assuming: object should be hidden (use case: if object is shown by default).
                    $setcode->arguments[] = new MP_String(
                        "\n appletObject.evalCommand('SetVisibleInView(" .
                        $geogebraname .
                        ",1,false)');\n");
                }
            }
            // Remember to close the function.
            $setcode->arguments[] = new MP_String("\n};\n");
        }

        // Extract some basics.
        $divid = 'stack-geogebra';
        $width = '500px';
        $height = '400px';
        $aspectratio = false;
        if (array_key_exists('width', $this->params)) {
            $width = $this->params['width'];
            $iparams['width'] = $width;
        }
        if (array_key_exists('height', $this->params)) {
            $height = $this->params['height'];
            $iparams['height'] = $height;
        }

        $style = "width:$width;height:$height;";

        if (array_key_exists('aspect-ratio', $this->params)) {
            $aspectratio = $this->params['aspect-ratio'];
            $iparams['aspect-ratio'] = $aspectratio;
            // Unset the undefined dimension, if both are defined then we have a problem.
            if (array_key_exists('height', $this->params)) {
                $style = "height:$height;aspect-ratio:$aspectratio;";
            } else if (array_key_exists('width', $this->params)) {
                $style = "width:$width;aspect-ratio:$aspectratio;";
            }
        }

        // All the IFRAME related parameters are no known we can add them to the output.
        $r->items[] = new MP_String(json_encode($iparams));

        // Then let's add some script tags to the head to load some stuff.
        $mathjax = stack_get_mathjax_url();
        // Silence the MathJax message that blinks on top of every graph.
        $r->items[] = new MP_List([
            new MP_String('script'),
            new MP_String(json_encode(['type' => 'text/x-mathjax-config'])),
            new MP_String('MathJax.Hub.Config({messageStyle: "none"});'),
        ]);
        $r->items[] = new MP_List([
            new MP_String('script'),
            new MP_String(json_encode(['type' => 'text/javascript', 'src' => $mathjax])),
        ]);
        // Naturally having GeoGebra loaded is important, we load it from our CORS source.
        $r->items[] = new MP_List([
            new MP_String('script'),
            new MP_String(json_encode(['type' => 'text/javascript', 'src' => 'cors://geogebracore.js'])),
        ]);

        // Then lets start building up the contents of the body.
        $r->items[] = new MP_String('<div style="' . $style .
            '"><div class="geogebrabox" id="geogebrabox" style="width:100%;height:100%;"></div></div><script type="module">');
        // For binding we need to import the binding libraries.
        $r->items[] = new MP_String("\nimport {stack_js} from '" . stack_cors_link('stackjsiframe.min.js') . "';\n");
        // TO-DO: minify.
        $r->items[] = new MP_String("import {stack_geogebra} from '" . stack_cors_link('stackgeogebra.js') . "';\n");

        // Lets define the common bits of code.
        $commonprecode = 'var presetparams = {"id":"applet","appName":"classic","width":800,"height": 600,' .
            '"showToolBar": false,"showAlgebraInput": false,"showMenuBar": false,' .
            'material_id:"x3tzeapm"};';
        $commonprecode .= "\nvar params = presetparams;";

        // NOTE the way input refs works here is somewhat tied to the STACK-JS
        // implementation and the way the set/watch/remember functions get defined.
        // Basically, we just declare these variables with values they will be
        // having after the input gets "connected" but we will only use these
        // after the inputs are ready.

        // Add the inputrefs, in STACK-JS the id is the name.
        // We could consider a prefix if there is a real risk of collision.
        foreach ($inputmapping as $varname => $inputname) {
            $commonprecode .= "\nvar $varname = '$inputname';";
        }

        // There is no more commonprecode, so we will dump that.
        $r->items[] = new MP_String($commonprecode);

        // Then the functions.
        $r->items[] = $setcode;
        $r->items[] = $watchcode;
        $r->items[] = $remembercode;

        // Then whatever we contain.
        $opt2 = [];
        if ($options !== null) {
            $opt2 = array_merge([], $options);
        }
        // Let the contents know where they are.
        $opt2['in iframe'] = true;
        foreach ($this->children as $item) {
            // Assume that all code inside is JavaScript and that we do not
            // want to do the markdown escaping or any other in it.
            $c = $item->compile(castext2_parser_utils::RAWFORMAT, $opt2);
            if ($c !== null) {
                $r->items[] = $c;
            }
        }

        // In the code after the block contents we make sure we have
        // 'appletOnLoad' logic and if we have that we modify it.
        $commonpostcode = 'if("appletOnLoad" in params){';
        $commonpostcode .= "\n var _tmp = params['appletOnLoad'];";
        $commonpostcode .= "\n params['appletOnLoad'] = () => {";
        $commonpostcode .= "\n  var appletObject = applet.getAppletObject();";
        $commonpostcode .= "\n  initialgeogebraset();";
        $commonpostcode .= "\n  watchGeoGebraObjects();";
        $commonpostcode .= "\n  rememberGeoGebraObjects();";
        $commonpostcode .= "\n  _tmp();";
        $commonpostcode .= "\n };";
        $commonpostcode .= "\n}else{";
        $commonpostcode .= "\n params['appletOnLoad'] = () => {";
        $commonpostcode .= "\n  initialgeogebraset();";
        $commonpostcode .= "\n  watchGeoGebraObjects();";
        $commonpostcode .= "\n  rememberGeoGebraObjects();";
        $commonpostcode .= "\n };";
        $commonpostcode .= "\n}";

        $commonpostcode .= "\nvar applet = new GGBApplet(params, true);";
        $customgeogebrabaseurl = stack_utils::get_config()->geogebrabaseurl;
        if (isset($customgeogebrabaseurl) && trim($customgeogebrabaseurl) != '') {
            // Use JSON-encode to ensure that should the URL have something fancy
            // in it we can still survive.
            $commonpostcode .=
                "\napplet.setHTML5Codebase(" . json_encode(
                $customgeogebrabaseurl) .
                ');';
        }

        // Then add logic to bind to the inputs or wait for "load". Then inject.
        if (count($inputmapping) > 0) {
            $promises = [];
            $vars = [];
            // NOTE that not all varaibles will be mapped here to avoid
            // calling `request_access_to_input` for the same input multiple
            // times. As the value the promise resolves to is know we actually
            // already know the results and have set them, we just want to
            // wait for them to be ready.
            $handledinputs = [];
            foreach ($inputmapping as $value => $key) {
                // That true there makes us sync input-events as well, like we did before.
                if (!isset($handledinputs[$key])) {
                    $promises[] = 'stack_js.request_access_to_input("' . $key . '",true)';
                    $vars[] = $value;
                    $handledinputs[$key] = true;
                }
            }
            $commonpostcode .= "\nPromise.all([" . implode(',', $promises) . '])';
            $commonpostcode .= "\n.then(([" . implode(',', $vars) . ']) => {';
            $commonpostcode .= "applet.inject('geogebrabox');});";
        } else {
            $commonpostcode .= "\nwindow.addEventListener('load', function() {applet.inject('geogebrabox');});";
        }
        // Remember to close the script tag.
        $commonpostcode .= "\n</script>";

        // There is no more commonpostcode, so we will dump that.
        $r->items[] = new MP_String($commonpostcode);

        return $r;
    }



    public function is_flat(): bool {
        return false;
    }

    public function postprocess(array $params, castext2_processor $processor,
        castext2_placeholder_holder $holder): string {
        return 'This is never happening! The logic goes to [[iframe]].';
    }

    public function validate_extract_attributes(): array {
        // Note that all the "set" variables are actually CAS variables.
        // So we should return the nosuffix versions here for checking.
        // Not a major issue as the security system will stop any calls and
        // I really do not consider the reads possible through this as serious enough.
        // TO-DO: not bothering now.
        return [];
    }

    public function validate(&$errors = [], $options = []): bool {
        // Basically, check that the dimensions have units we know.
        // Also that the references make sense.
        $valid = true;
        $width = '500px';
        $height = '400px';
        if (array_key_exists('width', $this->params)) {
            $width = $this->params['width'];
        }
        if (array_key_exists('height', $this->params)) {
            $height = $this->params['height'];
        }
        if (array_key_exists('watch', $this->params)) {
            $watch = $this->params['watch'];
        }
        if (array_key_exists('remember', $this->params)) {
            $remember = $this->params['remember'];
        }
        if (array_key_exists('set', $this->params)) {
            $set = $this->params['set'];
        }
        if (array_key_exists('input', $this->params)) {
            $input = $this->params['input'];
        }

        // NOTE! List ordered by length. For the trimming logic.
        $validunits = [
            'vmin',
            'vmax',
            'rem',
            'em',
            'ex',
            'px',
            'cm',
            'mm',
            'in',
            'pt',
            'pc',
            'ch',
            'vh',
            'vw',
            '%',
        ];

        $widthend = false;
        $heightend = false;
        $widthtrim = $width;
        $heighttrim = $height;

        foreach ($validunits as $suffix) {
            if (
                !$widthend &&
                strlen($width) > strlen($suffix) &&
                substr($width, -strlen($suffix)) === $suffix
            ) {
                $widthend = true;
                $widthtrim = substr($width, 0, -strlen($suffix));
            }
            if (
                !$heightend &&
                strlen($height) > strlen($suffix) &&
                substr($height, -strlen($suffix)) === $suffix
            ) {
                $heightend = true;
                $heighttrim = substr($height, 0, -strlen($suffix));
            }
            if ($widthend && $heightend) {
                break;
            }
        }
        $err = [];

        if (!$widthend) {
            $valid = false;
            $err[] = stack_string('stackBlock_geogebra_width');
        }
        if (!$heightend) {
            $valid = false;
            $err[] = stack_string('stackBlock_geogebra_height');
        }
        if (!preg_match('/^[0-9]*[\.]?[0-9]+$/', $widthtrim)) {
            $valid = false;
            $err[] = stack_string('stackBlock_geogebra_width_num');
        }
        if (!preg_match('/^[0-9]*[\.]?[0-9]+$/', $heighttrim)) {
            $valid = false;
            $err[] = stack_string('stackBlock_geogebra_height_num');
        }

        if (
            array_key_exists('width', $this->params) &&
            array_key_exists('height', $this->params) &&
            array_key_exists('aspect-ratio', $this->params)
        ) {
            $valid = false;
            $err[] = stack_string('stackBlock_geogebra_overdefined_dimension');
        }
        if (
            !(
                array_key_exists('width', $this->params) ||
                array_key_exists('height', $this->params)
            ) &&
            array_key_exists('aspect-ratio', $this->params)
        ) {
            $valid = false;
            $err[] = stack_string('stackBlock_geogebra_underdefined_dimension');
        }

        $valids = null;
        foreach ($this->params as $key => $value) {
            if (substr($key, 0, 10) === 'input-ref-') {
                $varname = substr($key, 10);
                if (
                    isset($options['inputs']) &&
                    !isset($options['inputs'][$varname])
                ) {
                    $err[] = stack_string('stackBlock_geogebra_input_missing', [
                        'var' => $varname,
                    ]);
                }
            } else if (
                $key !== 'width' &&
                $key !== 'height' &&
                $key !== 'aspect-ratio' &&
                $key !== 'watch' &&
                $key !== 'set' &&
                $key !== 'remember'
            ) {
                $err[] = "Unknown parameter '$key' for geogebra-block.";
                $valid = false;
                if ($valids === null) {
                    $valids = [
                        'width',
                        'height',
                        'aspect-ratio',
                        'watch',
                        'set',
                        'remember',
                    ];
                    // The variable $inputdefinitions is not defined!
                    if ($inputdefinitions !== null) {
                        $tmp = $root->get_parameter('ioblocks');
                        $inputs = [];
                        foreach ($inputdefinitions->get_names() as $key) {
                            $inputs[] = "input-ref-$key";
                        }
                        $valids = array_merge($valids, $inputs);
                    }
                    $err[] = stack_string('stackBlock_geogebra_param', [
                        'param' => implode(', ', $valids),
                    ]);
                }
            }
        }

        // Wrap the old string errors with the context details.
        foreach ($err as $er) {
            $errors[] = new $options['errclass'](
                $er,
                $options['context'] .
                    '/' .
                    $this->position['start'] .
                    '-' .
                    $this->position['end']
            );
        }

        return $valid;
    }
}

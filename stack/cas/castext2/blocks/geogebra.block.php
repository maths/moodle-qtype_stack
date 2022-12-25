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
/**
 * GeoGebra block for STACK
 * derived by jsxGraph STACK implementation
 * @copyright  2022 University of Edinburgh
 * @author     Tim Lutz
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../block.interface.php');
require_once(__DIR__ . '/../block.factory.php');

require_once(__DIR__ . '/root.specialblock.php');
require_once(__DIR__ . '/stack_translate.specialblock.php');

class stack_cas_castext2_geogebra extends stack_cas_castext2_block {

    private static $countgraphs = 1;

    public function compile($format, $options):  ? string {
        $r = '["geogebra"';

        $jsonparams = json_encode($this
                  ->params);
        // We need to transfer the parameters of the applet forward like jsx graph implementation.
        $r .= ',[' . stack_utils::php_string_to_maxima_string($jsonparams);

        // Section geogebraset.
        if (isset($this->params['set'])) {
            // Opening the parameter area geogebraset.
            $r .= ',["geogebraset"';
            // Validate below needs to be adjusted.
            $setvars = explode(',', $this->params['set']);

            foreach ($setvars as $geogebraname) {
                if (str_ends_with($geogebraname, '__fixed')) {
                    // Assuming point must not be interactable by the user.
                    $geogebraname = substr($geogebraname, 0, -7);
                }
                $r .= ',[' . stack_utils::php_string_to_maxima_string($geogebraname) . ", string($geogebraname)]";
            }
            // Closing the geogebraset parameter area.
            $r .= ']';
        }
        // Section geogebraset end.

        // We could add more sections like the one above here.

        // Closing parameter area with index 1.
        $r .= ']';

        foreach ($this->children as $item) {
            // Assume that all code inside is JavaScript and that we do not
            // want to do the markdown escaping or any other in it.
            $c = $item->compile(castext2_parser_utils::RAWFORMAT, $options);
            if ($c !== null) {
                $r .= ',' . $c;
            }
        }

        // Closing geogebra.
        $r .= ']';
        return $r;
    }

    public function is_flat() : bool {
        return false;
    }

    public function postprocess(array $params, castext2_processor $processor): string {
        global $PAGE;

        if (count($params) < 3) {
            // Nothing at all.
            return '';
        }

        $parameters = json_decode($params[1][0], true);

        $content    = '';
        for ($i = 2; $i < count($params); $i++) {
            if (is_array($params[$i])) {
                $content .= $processor->process($params[$i][0], $params[$i]);
            } else {
                $content .= $params[$i];
            }
        }

        $divid  = 'stack-geogebra-' . self::$countgraphs;
        $width  = '500px';
        $height = '400px';
        $aspectratio = false;
        if (array_key_exists('width', $parameters)) {
            $width = $parameters['width'];
        }
        if (array_key_exists('height', $parameters)) {
            $height = $parameters['height'];
        }

        $style = "width:$width;height:$height;";

        if (array_key_exists('aspect-ratio', $parameters)) {
            $aspectratio = $parameters['aspect-ratio'];
            // Unset the undefined dimension, if both are defined then we have a problem.
            if (array_key_exists('height', $parameters)) {
                $style = "height:$height;aspect-ratio:$aspectratio;";
            } else if (array_key_exists('width', $parameters)) {
                $style = "width:$width;aspect-ratio:$aspectratio;";
            }
        }

        $code = $content;

        // Input ref prefixes.
        // We could simply expose the prefix Moodle uses and let the author work
        // with that but suppose there exists another VLE which does not use
        // the same prefix for all inputs in the question, by keepping the id
        // mapping here the materials need not care about that and only this
        // needs to be fixed to match the environment.
        // And in any case in Moodle at the time we render this we do not know.
        // The prefix.
        // NOTE! We should validate that we never have input-refs-outside
        // question-text, but for now lets use the old seek code to work with
        // them out if that happens. We now can directly access the identtifier.

        // Deprecated input-ref definition in geogebra.block.
        foreach ($parameters as $key => $value) {
            if (substr($key, 0, 10) === 'input-ref-') {
                $inputname  = substr($key, 10);
                if (property_exists($processor, 'qa') && $processor->qa !== null) {
                    // For contents useing the new renderer we have access to
                    // the identifier at this phase.
                    $namecode = "var $value='" . $processor->qa->get_qt_field_name($inputname) . "';\n";
                    $code = "$namecode\n$code";
                } else {
                    $seekcode = "var $value=stack_geogebra.find_input_id(divid,'$inputname');";
                    $code = "$seekcode\n$code";
                }
            }
        }
        // Deprecated input-ref definition end.

        // Set exists if one of the keys was "set" otherwise no key was set.
        // Set is provided inside block-tag.
        // The value of set is used to set initial values of GeoGebra objects
        // (assumes: set is a string of (unique) comma-separated GeoGebra-objects with latin names.
        // Set could look like: set = "A,B,C,D,a2,E__fixed")
        // (assumes: names are equal in STACK and GeoGebra)
        // (assumes: values are int or float STACK variables)
        // (assumes: points are represented as array in STACK like: [2,3] means point with x=2, y=3)
        // maxima object would be for point D e.g. D:[2,3]
        // (assumes: value-names are starting with lower case letters (angles are used like values,
        // must be named lowercase Latin-Alphabet, values are radian values))
        // (assumes: Point-names are starting with upper case letters).
        // (assumes: Points will be set free to manipulate, unless you add '__fixed' to the Point-name).
        if (array_key_exists('set', $parameters)) {
            $set = $parameters['set'];

            $setcode = "function initialgeogebraset". self::$countgraphs . "(){
                var appletObject = applet.getAppletObject();\n";
            /*  $setcode .= "alert('".addslashes($params[0])."');";
                $setcode .= "alert('".addslashes($params[1][0])."');";
                $setcode .= "alert('".addslashes($params[1][1][0])."');";//hier sollte geogebraset stehen//Yabadabadoo.
                $setcode .= "alert('".addslashes($params[1][1][1][0])."');";//hier weiter//liefert Afixed
                $setcode .= "alert('".addslashes($params[1][1][1][1])."');";//hier weiter//liefert Afixed den Value
            */
            // In params $params[1][0] befindet sich der json String der
            // wie in jsx Graph für allgemeine Graphikoptionen erzeugt wird
            // in $params[1][1] befindet sich der Bereich geogebraset.
            // Das erkennt man am ersten Eintrag: $params[1][1][0]. Dort findet sich der string geogebraset
            // in $params[1][1][1] befindet sich das erste GeoGebra Objekt im geogebraset Bereich
            // in $params[1][1][1][0] der Name. in $params[1][1][1][1] der Wert.
            // nun kann man im folgenden auf Basis der Namenskonventionen an den Namen den Objekttyp erkennen und das Setzen zur Initialisierung einleiten
            // für weitere bereiche wie remeber oder watch definiert man oben einen weiteren Bereich, gibt diesem einen Namen und fertig. :)
            // which names are used in set.
            $geogebranamesetcode = "";
            $setarray = str_getcsv($set);
            foreach ($setarray as $geogebraname) {
                // Get value of params array in section geogebraset ->params[1][1].
                if (str_ends_with($geogebraname, '__fixed')) {
                    for ($i = 0; $i < count($params[1][1]); $i++) {
                        // Param section of "geogebraset".
                        if (substr($geogebraname, 0, -7) == $params[1][1][$i][0]) {
                            $geogebravalue = $params[1][1][$i][1];
                            break;
                        }
                    }
                } else {
                    for ($i = 0; $i < count($params[1][1]); $i++) {
                        // Param section of "geogebraset".
                        if ($geogebraname == $params[1][1][$i][0]) {
                            $geogebravalue = $params[1][1][$i][1];
                            break;
                        }
                    }
                }
                // Decoding values in params for $geogebraname object.
                $ggbcoords = json_decode($geogebravalue, true);

                if (ctype_upper(substr($geogebraname, 0, 1))) {
                    // Assuming geogebraname is the (therefore uppercased) name of an object of type: point.

                    if (str_ends_with($geogebraname, '__fixed')) {
                        // Assuming point must not be interactable by the user.
                        // appletObject.evalCommand('POINTNAME= Point({XCOORD,YCOORD})

                        $geogebraname = substr($geogebraname, 0, -7);
                        // Removing __fixed (7 characters).
                        $geogebranamesetcode .= "appletObject.evalCommand('"
                            . $geogebraname . " = Point({".$ggbcoords[0].",".$ggbcoords[1]."})');";
                        // appletObject.evalCommand('G= Point({{#fx#},4})');
                    } else {
                        // Assuming point is interactable by the user.
                        // appletObject.evalCommand('POINTNAME=(XCOORD,YCOORD)')
                        $geogebranamesetcode .= "appletObject.evalCommand('"
                            . $geogebraname . " = (".$ggbcoords[0].",".$ggbcoords[1].")');";
                    }
                } else {
                    $geogebranamesetcode .= "appletObject.evalCommand('" . $geogebraname . " = ".$ggbcoords."');";
                    // Assuming geogebraname is the name of an object of type: value or angle (therefore latin lowercase)
                    // setting angle by size not supported.
                    // Please set angle by setting defining points.
                }
                $setcode = "$setcode\n$geogebranamesetcode";
            }
            $setcode = "$setcode\n};";
            $code = "$setcode\n$code";
        } else {
            $setcode = "function initialgeogebraset". self::$countgraphs ."(){};";
            $code = "$setcode\n$code";
        }
        // Section GeoGebra watch.

        if (array_key_exists('watch', $parameters)) {
            $watch = $parameters['watch'];

            $watchcode = "function watchGeoGebraObjects". self::$countgraphs . "(){
              var appletObject = applet.getAppletObject();\n";

            // Geogebra objects must not be named with numbers within a name. numbers at the end are allowed.

            $geogebranamewatchcode = ""; // XXXToDo delete this.
            $watcharray = str_getcsv($watch);
            foreach ($watcharray as $geogebraname) {
                $geogebranamewatchcode = "";
                // XXXToDo test this.
                if (ctype_upper(substr($geogebraname, 0, 1))) {
                    // stack_geogebra.bind_point(stateRef,appletObject,"A");
                    // Assuming geogebraname is the (therefore uppercase) name of an object of type: point
                    // in js code variables are called watchvar__.
                    $geogebranamewatchcode .= "stack_geogebra.bind_point(watchvar" . $geogebraname .
                        self::$countgraphs . ",appletObject,\"" . $geogebraname . "\");";
                    // appletObject.evalCommand('G= Point({{#fx#},4})');
                } else {
                    // Assuming geogebraname is a value or angle (therefore lowercase).
                    $geogebranamewatchcode .= "stack_geogebra.bind_value(watchvar" . $geogebraname.
                        self::$countgraphs . ",appletObject,\"" . $geogebraname . "\");";
                }
                $watchcode = "$watchcode\n$geogebranamewatchcode";
            }
            $watchcode = "$watchcode\n};";
            $code = "$watchcode\n$code";
            foreach ($watcharray as $geogebraname) {
                if (property_exists($processor, 'qa') && $processor->qa !== null) {
                    // For contents useing the new renderer we have access to the identifier at this phase.
                    $namecode = "var watchvar$geogebraname". self::$countgraphs ."='" .
                        $processor->qa->get_qt_field_name($geogebraname) . "';\n";
                    $code = "$namecode\n$code";
                } else {
                    $seekcode = "var watchvar$geogebraname". self::$countgraphs .
                        "=stack_geogebra.find_input_id(divid,'$geogebraname');";
                    $code = "$seekcode\n$code";
                }
            }
        } else {
            $watchcode = "function watchGeoGebraObjects" . self::$countgraphs ."(){};";
            $code = "$watchcode\n$code";
        }
        // Section GeoGebra watch end.

        // Section GeoGebra remember.
        if (array_key_exists('remember', $parameters)) {
            $remember = $parameters['remember'];

            $remembercode = "function rememberGeoGebraObjects". self::$countgraphs . "(){
              var appletObject = applet.getAppletObject();\n";

            // Geogebra objects must not be named with numbers within a name. numbers at the end are allowed.
            $geogebranameremembercode = ""; // XXXToDo delete this.
            $rememberarray = str_getcsv($remember);
            foreach ($rememberarray as $geogebraname) {
                $geogebranameremembercode = ""; // XXXToDo delete this.
                if (ctype_upper(substr($geogebraname, 0, 1))) {
                    // stack_geogebra.bind_point(stateRef,appletObject,"A");
                    // Assuming geogebraname is the (therefore uppercase) name of an object of type: point
                    // in js code variables are called remembervar__.
                    $geogebranameremembercode .= "stack_geogebra.bind_point_to_remember_JSON(remembervar" .
                        $geogebraname. self::$countgraphs .",appletObject,\"" . $geogebraname."\");";
                    // appletObject.evalCommand('G= Point({{#fx#}, 4})');
                } else {
                    // Assuming geogebraname is a value or angle (therefore lowercase).
                    $geogebranameremembercode .= "stack_geogebra.bind_value_to_remember_JSON(remembervar" .
                        $geogebraname . self::$countgraphs .",appletObject,\"" . $geogebraname."\");";
                }
                $remembercode = "$remembercode\n$geogebranameremembercode";
            }
            $remembercode = "$remembercode\n};";
            $code = "$remembercode\n$code";
            foreach ($rememberarray as $geogebraname) {
                if (property_exists($processor, 'qa') && $processor->qa !== null) {
                    // For contents useing the new renderer we have access to the identifier at this phase.
                    $remembernamecode = "var remembervar$geogebraname" . self::$countgraphs ."='" .
                        $processor->qa->get_qt_field_name("remember") . "';\n";
                    $code = "$remembernamecode\n$code";
                } else {
                    // Remember is a reserved name, while using geogebra and the remember tag,
                    // Stack input "remember" should be reserved and not used elsewhere
                    // if remember tag is used: remember must be a stack input of type string (to be able to store JSON).
                    $rememberseekcode = "var remembervar$geogebraname" . self::$countgraphs .
                        "=stack_geogebra.find_input_id(divid,'remember');";
                    $code = "$rememberseekcode\n$code";
                }
            }
        } else {
            $remembercode = "function rememberGeoGebraObjects" . self::$countgraphs . "(){};";
            $code = "$remembercode\n$code";
        }
        // Section GeoGebra remember end.

        // Prefix the code with the id of the div.
        // Build geogebra js elements via php.
        $code = "var divid = '$divid';\nvar BOARDID = divid;\n$code";

        $code = "$code\n var applet" . self::$countgraphs . "= new GGBApplet(params" . self::$countgraphs . ", true);";

        // Check global options for self-hosted geogebra url link to self-hosted GeoGebra/HTML5/5.0/web3d/.
        $customgeogebrabaseurl = stack_utils::get_config()->geogebrabaseurl;
        if (isset($customgeogebrabaseurl) && trim($customgeogebrabaseurl) != "") {
            $code = "$code\n applet" . self::$countgraphs . ".setHTML5Codebase('" . $customgeogebrabaseurl . "');";
        }
        // Inject applet.
        $code = "$code\n window.addEventListener('load', function() {applet"
            . self::$countgraphs . ".inject(divid);});";

        // Setting params preset: TODO this could be managed in block related STACK global settings.
        $code = "\n var presetparams" . self::$countgraphs .
            "= {\"id\":\"applet\",\"appName\":\"classic\",\"width\":800,\"height\": 600," .
            "\"showToolBar\": false,\"showAlgebraInput\": false,\"showMenuBar\": false," .
            "material_id:\"x3tzeapm\"," .
            " appletOnLoad:function(){initialgeogebraset" . self::$countgraphs . "();\n" . "watchGeoGebraObjects" .
            self::$countgraphs . "();\n" .
            "rememberGeoGebraObjects" . self::$countgraphs . "();\n" ."}};" .
            "\n var params". self::$countgraphs . "= presetparams". self::$countgraphs . ";\n$code";

        // Replace standard names and ids. TODO use regex for more robust recognition see preg_replace below.
        $code = str_replace('"id":"applet"', '"id":"applet' . self::$countgraphs . '"', $code);
        $code = str_replace("applet.", "applet" . self::$countgraphs . ".", $code);
        $code = str_replace("appletObject", "appletObject" . self::$countgraphs, $code);
        // $code = str_replace("var params", "var params" . self::$countgraphs, $code);//deprecated see presetparams, params is predefined in newest version see line above.

        // Prepare execution of initial value set (inside geogebra block) tag called "set =".
        // $code = str_replace('"appletOnLoad": function(){', '"appletOnLoad": function(){initialgeogebraset' . self::$countgraphs . "();\n", $code);//deprecated
        $code = preg_replace('/params\["appletOnLoad"\]\s*=\s*function\s*\(\s*\)\s*\{/', 'params["appletOnLoad"] = function(){' .
            'var appletObject'. self::$countgraphs .' = applet'. self::$countgraphs .
            // Define appletObject, this can be used e.g. appletObject.evalCommand() added by user in manual script part.
            // TODO Add to documentation.
            ' .getAppletObject(); ' .
            'initialgeogebraset' . self::$countgraphs . "();" .
            "watchGeoGebraObjects" . self::$countgraphs ."();\n" .
            "rememberGeoGebraObjects" . self::$countgraphs ."();\n", $code);
        // $code = str_replace('params["appletOnLoad"] = function(){', 'params["appletOnLoad"] = function(){'
        // .'var appletObject'. self::$countgraphs .' = applet'. self::$countgraphs .'
        // . getAppletObject(); '//define appletObject, this can be used e.g. appletObject.evalCommand() added by user in manual script part.
        // TODO Add to documentation.
        // .'initialgeogebraset' . self::$countgraphs . "();"
        // . "watchGeoGebraObjects" . self::$countgraphs ."();\n", $code);

        // New version.
        $code = str_replace("params[", "params" . self::$countgraphs . "[", $code);
        // We restrict the actions of the block code a bit by stopping it from
        // rewriting some things in the surrounding scopes.
        // Also catch errors inside the code and try to provide console logging
        // of them for the author.
        // We could calculate the actual offset but I'll leave that for
        // someone else. 1+2*n probably, or we could just write all the preamble
        // on the same line and make the offset always be the same?
        $code = '"use strict"; try{if(document.getElementById("' . $divid
            . '")){' . $code . '}} '
            . 'catch(err) {console.log("STACK GeoGebra error in \"' . $divid
            . '\", (note a slight varying offset in the error position due to possible input references):");'
            . 'console.log(err);}';

        // What is this?
        $attributes = ['class' => 'geogebrabox', 'style' => $style, 'id' => $divid];

        $PAGE->requires->js_amd_inline(
            'require(["qtype_stack/geogebra","qtype_stack/geogebracore-lazy","core/yui"], '
            . 'function(stack_geogebra, GEOGEBRA, Y){Y.use("mathjax",function(){' . $code
            . '});});');

        self::$countgraphs = self::$countgraphs + 1;

        return html_writer::tag('div', '', $attributes);
    }

    public function validate_extract_attributes(): array {
        return [];
    }

    public function validate(&$errors = [], $options = []): bool {
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
            $err[] = stack_string('stackBlock_geogebra_width');
        }
        if (!$heightend) {
            $valid    = false;
            $err[] = stack_string('stackBlock_geogebra_height');
        }
        if (!preg_match('/^[0-9]*[\.]?[0-9]+$/', $widthtrim)) {
            $valid    = false;
            $err[] = stack_string('stackBlock_geogebra_width_num');
        }
        if (!preg_match('/^[0-9]*[\.]?[0-9]+$/', $heighttrim)) {
            $valid    = false;
            $err[] = stack_string('stackBlock_geogebra_height_num');
        }

        if (array_key_exists('width', $this->params) &&
            array_key_exists('height', $this->params) &&
            array_key_exists('aspect-ratio', $this->params)) {
            $valid    = false;
            $err[] = stack_string('stackBlock_geogebra_overdefined_dimension');
        }
        if (!(array_key_exists('width', $this->params) ||
            array_key_exists('height', $this->params)) &&
            array_key_exists('aspect-ratio', $this->params)) {
            $valid    = false;
            $err[] = stack_string('stackBlock_geogebra_underdefined_dimension');
        }

        $valids = null;
        foreach ($this->params as $key => $value) {
            if (substr($key, 0, 10) === 'input-ref-') {
                $varname = substr($key, 10);
                if (isset($options['inputs']) && !isset($options['inputs'][$varname])) {
                    $err[] = stack_string('stackBlock_geogebra_input_missing',
                        ['var' => $varname]);
                }
            } else if ($key !== 'width' && $key !== 'height' && $key !== 'aspect-ratio' &&
                    $key !== 'watch' && $key !== 'set' && $key !== 'remember') {
                $err[] = "Unknown parameter '$key' for geogebra-block.";
                $valid    = false;
                if ($valids === null) {
                    $valids = ['width', 'height', 'aspect-ratio', 'watch', 'set', 'remember'];
                    // The variable $inputdefinitions is not defined!
                    if ($inputdefinitions !== null) {
                        $tmp    = $root->get_parameter('ioblocks');
                        $inputs = [];
                        foreach ($inputdefinitions->get_names() as $key) {
                            $inputs[] = "input-ref-$key";
                        }
                        $valids = array_merge($valids, $inputs);
                    }
                    $err[] = stack_string('stackBlock_geogebra_param', [
                        'param' => implode(', ', $valids)]);
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

<?php
// This file is part of Stack - http://stack.bham.ac.uk/
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

// defined('MOODLE_INTERNAL') || die();

// @copyright  2018 vesal
// @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.

require_once(__DIR__ . '/../casstring.class.php');
require_once("block.interface.php");
if (!defined('MINIMAL_API')) {
    // Then we are in Moodle.
    require_once(__DIR__ . '/../../../../../../lib/pagelib.php');
}

class stack_cas_castext_jsxgraphapi extends stack_cas_castext_block {

    private static $countgraphs = 1;

    public function extract_attributes(&$tobeevaluatedcassession, $conditionstack = null) {
        // There are currently no CAS evaluated attributes.
        // Only reasonable such would be dynamic size parameters.
    }

    public function content_evaluation_context($conditionstack = array()) {
        // Nothing changes, we want the contents to be evaluated as they are.
        return $conditionstack;
    }

    public function process_content($evaluatedcassession, $conditionstack = null) {
        // There is nothing to do before the full CASText has been evaluated.
        return false;
    }

    public function clear() {
        global $PAGE, $CFG, $OPTIONS;
        // Now is the time to replace the block with the div and the code.

/*
We try to do to the "server" side an html like:

-------------------------------------------------------------------------------------------------
<iframe id="jsxFrame-stack-jsxgraph-1-div1"
        style="width:calc(700px + 2px);height:calc(700px + 2px);border: none;"
        sandbox="allow-scripts"
        class="jsxFrame"
        src="data:text/html;base64,...">
</iframe>
<script>
  var quizdiv = document.getElementById('div1');
  if ( !quizdiv ) quizdiv = document;
  var sv = new ServerSyncValues(quizdiv, '#jsxFrame-stack-jsxgraph-1-div1', 'stackapi_', '', 'S1');
</script>
-------------------------------------------------------------------------------------------------

and inside the iframe the "client" code
where the base64 coded data url is like (in debug caes):

<!DOCTYPE html>
<head>
<title>JSXGraph</title>
<script type='text/javascript' charset='UTF-8' src='https://cdnjs.cloudflare.com/ajax/libs/jsxgraph/0.99.7/jsxgraphcore.js'></script>
<link rel='stylesheet' type='text/css' href='https://cdnjs.cloudflare.com/ajax/libs/jsxgraph/0.99.7/jsxgraph.css'>
<script type='text/javascript' charset='UTF-8' src='http://192.168.59.9/cs/stack/JSXClientSync.js'></script>
</head>
<body style='margin: 0px;'>
<div id='jxgbox' class='jxgbox' style='width:700px;height:700px;'></div>
</body>
<script type='text/javascript'>
    var divid = 'jxgbox';
    var stack_jxg = null;
    window.onmessage = function(e){
        stack_jxg = new JSXClientSync(e.ports[0],'c1');
        graph = new Graph(e.data);
    };

    function Graph(vars)
    {
      try {
        var ans1Ref=stack_jxg.find_input_id(divid,'ans1');
        // user code
        board.update();
      } catch (err) {
        console.error('STACK JSXGraph error in stack-jsxgraph-1\n');
        console.log(err);
      }
    }
</script>
</html>

*/

        $code = "";
        $iter = $this->get_node()->firstchild;
        while ($iter !== null) {
            $code .= $iter->to_string();
            $iter = $iter->nextsibling;
        }

        $clientNr = self::$countgraphs;
        $divid  = "stack-jsxgraph-" . self::$countgraphs;

        // Input ref prefixes.
        // We could simply expose the prefix Moodle uses and let the author work
        // with that but suppose there exists another VLE which does not use
        // the same prefix for all inputs in the question, by keepping the id
        // mapping here the materials need not care about that and only this
        // needs to be fixed to match the environment.
        // And in any case in Moodle at the time we render this we do not know.
        // The prefix.
        foreach ($this->get_node()->get_parameters() as $key => $value) {
            if (substr($key, 0, 10) === "input-ref-") {
                $varname = substr($key, 10);
                $seekcode = "var $value=stack_jxg.find_input_id(divid,'$varname');";
                $code = "$seekcode\n$code";
            }
        }

        // Prefix the code with the id of the div.

        $width  = $this->get_node()->get_parameter('width', '500px');
        $height = $this->get_node()->get_parameter('height', '500px');
        $debug = (int)$this->get_node()->get_parameter('debug', '0');
        $divSelector = $this->get_node()->get_parameter('div', '#div1');
        $parentSelector = $this->get_node()->get_parameter('parent', "$CFG->jsxparentselector");
        $sendInputs = $this->get_node()->get_parameter('inputs', ''); // TODO: sanitize ALL values
        $initObject = $this->get_node()->get_parameter('init', '{}'); // TODO: sanitize ALL values
        $taskDebug = $OPTIONS->debug;
        if ( $taskDebug >= 0 ) $debug = $taskDebug;

        $communicate = ( strpos($code, "stack_jxg") !== false);

        // $code = str_replace(";", ";\n", $code); // TODO: Hack until code is not on one line

        $inputPrefix = 'stackapi_';

        $communicate += $debug;
        if ( $sendInputs ) $communicate = true;
        if ( $initObject != '{}' && $initObject != '' ) $communicate = true;

        $iframename = "jsxFrame-$divid-" . str_replace('#','',$divSelector);

        $jxStyle  = "width:$width;height:$height;";

        $iframeStyle  = "width:calc($width + 2px);height:calc($height + 2px);border: none;";
        $debugHtmlClient = "";
        $debugHtmlServer = "";
        $debugFindClient = "";
        $debugSelector = "";
        if ( $debug ) {
            $debugName = 'debug-' . $divid;
            $debugSelector = "#$debugName";
            $debugHtmlClient = "\n<p class='debug'>c$clientNr: <input type='text' id='$debugName' size='$debug' value=''></input></p>";
            $debugHtmlServer = "\n<p class='debug'>s$clientNr: <input type='text' id='$debugName' size='$debug' value=''></input></p>";
            $debugFindClient = "debug = document.querySelector('$debugSelector');";
            $iframeStyle  = "width:calc($width + 30px);height:calc($height + 60px);border: none;";
        }

        $htmlCodeClient = "<!DOCTYPE html>
<head>
<title>JSXGraph</title>
<script type='text/javascript' charset='UTF-8' src='https://cdnjs.cloudflare.com/ajax/libs/jsxgraph/0.99.7/jsxgraphcore.js'></script>
<link rel='stylesheet' type='text/css' href='https://cdnjs.cloudflare.com/ajax/libs/jsxgraph/0.99.7/jsxgraph.css'>";
if ( $communicate )
    $htmlCodeClient .= "<script type='text/javascript' charset='UTF-8' src='$CFG->jsxgraphjs/JSXClientSync.js'></script>";
$htmlCodeClient .= "
</head>
<body style='margin: 0px;'>$debugHtmlClient
<div id='jxgbox' class='jxgbox' style='$jxStyle'></div>
</body>             
<script type='text/javascript'>
$debugFindClient
var divid = 'jxgbox';";
if ( $communicate )
$htmlCodeClient .= "
var stack_jxg = null;
window.onmessage = function(e){
 stack_jxg = new JSXClientSync(e.ports[0],'c$clientNr', e.data.values);
 graph = new Graph(e.data);
};

function Graph(initVars)";

        // Also catch errors inside the code and try to provide console logging
        // of them for the author.

$htmlCodeClient .= "
{
 try {
$code 
  // board.update();
 } catch (err) {
   console.error('STACK JSXGraph error in $divid\\n'); 
   console.log(err); 
 }        
}
</script>
</html>";

        $datasrc = base64_encode($htmlCodeClient);
        $data64 = "data:text/html;base64," . $datasrc;

        $iframeAttributes = array('id' => "$iframename", 'style' => $iframeStyle, 'sandbox' => "allow-scripts",
                                  'class' => 'jsxFrame', 'src' => $data64 );

        $html = html_writer::tag('iframe', '', $iframeAttributes);

        // **************************** Serverside code ************************************************

        $script = "";
        if ( $communicate ) {
            $scriptId = uniqid();
            $script = "     
<script id='$scriptId'>

// var quizdiv = document.getElementById('$divSelector');
// if ( !quizdiv ) quizdiv = document;
new ServerSyncValues(findParentElementFromScript('$scriptId', '$parentSelector', '$divSelector'),
 '#$iframename', '$inputPrefix', '$debugSelector', 'S$clientNr', 
 {sendInputs:'$sendInputs',initObject:$initObject}
);
</script>";
        }

        $this->get_node()->convert_to_text("$debugHtmlServer$html \n$script" );

        if (!defined('MINIMAL_API')) {
            $PAGE->requires->js_amd_inline('require(["qtype_stack/jsxgraph",'
                    . '"qtype_stack/jsxgraphcore-lazy","core/yui"], '
                    . 'function(stack_jxg, JXG, Y){Y.use("mathjax",function(){'.$code.'});});');
        }

        // Up the graph number to generate unique names.
        self::$countgraphs = self::$countgraphs + 1;
    }

    public function validate_extract_attributes() {
        // There are currently no CAS evaluated attributes.
        return array();
    }

    public function validate(&$errors=array()) {
        // Basically, check that the dimensions have units we know.
        // Also that the references make sense.
        $valid      = true;
        $width      = $this->get_node()->get_parameter('width', '500px');
        $height     = $this->get_node()->get_parameter('height', '400px');

        // NOTE! List ordered by length. For the trimming logic.
        $validunits = array("vmin", "vmax", "rem", "em", "ex", "px", "cm", "mm",
                            "in", "pt", "pc", "ch", "vh", "vw", "%");

        $widthend   = false;
        $heightend  = false;
        $widthtrim  = $width;
        $heighttrim = $height;

        foreach ($validunits as $suffix) {
            if (!$widthend && strlen($width) > strlen($suffix) &&
                    substr($width, -strlen($suffix)) === $suffix) {
                $widthend = true;
                $widthtrim = substr($width, 0, -strlen($suffix));
            }
            if (!$heightend && strlen($height) > strlen($suffix) &&
                    substr($height, -strlen($suffix)) === $suffix) {
                $heightend = true;
                $heighttrim = substr($height, 0, -strlen($suffix));
            }
            if ($widthend && $heightend) {
                break;
            }
        }

        if (!$widthend) {
            $valid = false;
            $errors[] = stack_string('stackBlock_jsxgraph_width');
        }
        if (!$heightend) {
            $valid = false;
            $errors[] = stack_string('stackBlock_jsxgraph_height');
        }
        if (!preg_match('/^[0-9]*[\.]?[0-9]+$/', $widthtrim)) {
            $valid = false;
            $errors[] = stack_string('stackBlock_jsxgraph_width_num');
        }
        if (!preg_match('/^[0-9]*[\.]?[0-9]+$/', $heighttrim)) {
            $valid = false;
            $errors[] = stack_string('stackBlock_jsxgraph_height_num');
        }

        // To check if the input references are ok we need to check the parsers
        // stats about inputs. Those are stored in the root node.
        $root = $this->get_node();
        while ($root->parent !== null) {
            $root = $root->parent;
        }
        $valids = null;
        foreach ($this->get_node()->get_parameters() as $key => $value) {
            if (substr($key, 0, 10) === "input-ref-") {
                $varname = substr($key, 10);
                if (!array_key_exists('input', $root->get_parameter('ioblocks', array())) 
                    || !array_key_exists($varname, $root->get_parameter('ioblocks')['input'])) {
                    $errors[] = stack_string('stackBlock_jsxgraph_height_num', array('var' => $varname));
                }
            } else if ($key !== 'width' && $key !== 'height') {
                $errors[] = "Unknown parameter '$key' for jsxgraph-block.";
                if ($valids == null) {
                    $valids = array('width', 'height');
                    if (array_key_exists('input', $root->get_parameter('ioblocks', array()))) {
                        $tmp = $root->get_parameter('ioblocks');
                        $inputs = array();
                        foreach ($tmp['input'] as $key => $value) {
                            $inputs[] = "input-ref-$key";
                        }
                        $valids = array_merge($valids, $inputs);
                    }
                    $errors[] = stack_string('stackBlock_jsxgraph_param', array('param' => implode(', ', $valids)));
                }
            }
        }

        // Finally check parent for other issues, should be none.
        if ($valid) {
            $valid = parent::validate($errors);
        }

        return $valid;
    }
}

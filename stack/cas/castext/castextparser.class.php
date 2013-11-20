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

/**
 * CAS text parser
 *
 * @copyright  2013 Aalto University
 * @copyright  2012 University of Birmingham
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 ** WARNING! if the file you are reading has .php-extension do not edit it! It has been generated from castext.peg.inc.
 **/
/**
 ** Howto generate the .php file: run the following command, in the directory of this file:
 ** php ../../../thirdparty/php-peg/cli.php castext.peg.inc > castextparser.class.php
 ** And do remove that PHP ending the question mark greater than thing after generation.
 **/
require_once(dirname(__FILE__) . '/../../../thirdparty/php-peg/autoloader.php');
use hafriedlander\Peg\Parser;
/**
 * Defines the text parser for identifying STACK specific parts from CAStext, does not work with XML, 
 * intended to parse text-fragments and attribute values.
 * Pointless to use if your text does not include the following strings "{@" or "{#"
 */
class stack_cas_castext_castextparser extends Parser\Basic {

    /**
     * A list of TeX environments that act as math-mode.
     */
    private static $math_mode_envs = array('align','align*','alignat','alignat*','eqnarray','eqnarray*','equation','equation*','gather','gather*','multline','multline*');

    /**
     * A function to test a string for necessary features related to castextparser.
     * returns true if the string should be passed trough the parser
     */
    public static function castext_parsing_required($test) {
        return (strpos($test, "{@")!==false || strpos($test, "{#")!==false || strpos($test, "[[")!==false);
    }

    /**
     * Takes a parse tree and concatenates the text-elements of its leafs. 
     * Intentionally skips the text-element of the root as modifications made 
     * to the leafs might not have been done there. 
     */
    public static function to_string($parse_tree) {
        $r = "";
        switch ($parse_tree['_matchrule']) {
            case "castext":
                if (array_key_exists('_matchrule', $parse_tree['item'])) {
                    $r .= stack_cas_castext_castextparser::to_string($parse_tree['item']);
                } else {
                    foreach ($parse_tree['item'] as $sub_tree) {
                        $r .= stack_cas_castext_castextparser::to_string($sub_tree);
                    }
                }
                break;
            case "block":
                $r .= "[[ " . $parse_tree['name'];

                if (count($parse_tree['params']) > 0) {
                    foreach ($parse_tree['params'] as $key => $value) {
                        $r .= " $key=";
                        if (strpos($value, '"') === false) {
                            $r .= '"' . $value . '"';
                        } else {
                            $r .= "'$value'";
                        }
                    }
                }

                $r .= " ]]";

                if (array_key_exists('_matchrule', $parse_tree['item'])) {
                    $r .= stack_cas_castext_castextparser::to_string($parse_tree['item']);
                } else {
                    foreach ($parse_tree['item'] as $sub_tree) {
                        $r .= stack_cas_castext_castextparser::to_string($sub_tree);
                    }
                }
                $r .= "[[/ " . $parse_tree['name'] . " ]]";

                break;
            case "ioblock":
            case "rawcasblock":
            case "texcasblock":
            case "mathmodeopen":
            case "mathmodeclose":
            case "bagintexenv":
            case "endtexenv":
            case "text":
            case "ws":
            case "misc":
            case "break":
            case "blockopen":
            case "blockempty":
            case "blockclose":
                $r .= $parse_tree['text'];
                break;
        }
        return $r;
    }

    /**
     * This function searches the tree for adjacent text nodes and joins them together.
     * Not unlike similar functions in DOM-parsers.
     * returns an array that has been normalized
     */
    public static function normalize($parse_tree) {
        // start by paintting the mathmode if not paintted elsewhere
        if (!array_key_exists('mathmode', $parse_tree)) {
            $mathmode = false;
            $parse_tree['mathmode'] = false;
            if (array_key_exists('item', $parse_tree) && is_array($parse_tree['item']) && count($parse_tree['item']) > 1 && !array_key_exists('_matchrule', $parse_tree['item'])) {
                foreach ($parse_tree['item'] as $key => $value) {
                    if ($value['_matchrule'] == 'mathmodeclose') {
                        $mathmode = false;
                    } else if ($value['_matchrule'] == 'mathmodeopen') {
                        $mathmode = true;
                    } else if ($value['_matchrule'] == 'begintexenv' && array_search($value['value']['text'],self::$math_mode_envs)!==FALSE) {
                        $mathmode = true;
                    } else if ($value['_matchrule'] == 'endtexenv' && array_search($value['value']['text'],self::$math_mode_envs)!==FALSE) {
                        $mathmode = false;
                    }
                    
                    $parse_tree['item'][$key]['mathmode'] = $mathmode;
                }
            }
        }

        if (array_key_exists('item', $parse_tree) && is_array($parse_tree['item']) && !array_key_exists('_matchrule', $parse_tree['item']) && count($parse_tree['item']) > 1) {
            // Key listing maybe not continuous...
            $keys = array_keys($parse_tree['item']);
            for ($i=0; $i<count($keys)-1; $i++) {
                $now = $keys[$i];
                $next = $keys[$i+1];
                if ($parse_tree['item'][$now]['_matchrule'] == 'ioblock' ||
                    $parse_tree['item'][$now]['_matchrule'] == 'ws' ||
                    $parse_tree['item'][$now]['_matchrule'] == 'misc' ||
                    $parse_tree['item'][$now]['_matchrule'] == 'breaks' ||
                    $parse_tree['item'][$now]['_matchrule'] == 'text' ||
                    $parse_tree['item'][$now]['_matchrule'] == 'mathmodeopen' ||
                    $parse_tree['item'][$now]['_matchrule'] == 'mathmodeclose' ||
                    $parse_tree['item'][$now]['_matchrule'] == 'begintexenv' ||
                    $parse_tree['item'][$now]['_matchrule'] == 'endtexenv' ) {
                    if ($parse_tree['item'][$next]['_matchrule'] == 'ioblock' ||
                        $parse_tree['item'][$next]['_matchrule'] == 'ws' ||
                        $parse_tree['item'][$next]['_matchrule'] == 'misc' ||
                        $parse_tree['item'][$next]['_matchrule'] == 'breaks' ||
                        $parse_tree['item'][$next]['_matchrule'] == 'mathmodeopen' ||
                        $parse_tree['item'][$next]['_matchrule'] == 'mathmodeclose' ||
                        $parse_tree['item'][$next]['_matchrule'] == 'begintexenv' ||
                        $parse_tree['item'][$next]['_matchrule'] == 'endtexenv') {
                        $parse_tree['item'][$next]['text'] = $parse_tree['item'][$now]['text'].$parse_tree['item'][$next]['text'];
                        $parse_tree['item'][$next]['_matchrule'] = 'text';
                        unset($parse_tree['item'][$now]);
                    } else {
                        $parse_tree['item'][$now]['_matchrule'] = 'text';
                    }
                } else {
                    $parse_tree['item'][$now] = stack_cas_castext_castextparser::normalize($parse_tree['item'][$now]);
                    if ($parse_tree['item'][$next]['_matchrule'] == 'ioblock' ||
                        $parse_tree['item'][$next]['_matchrule'] == 'ws' ||
                        $parse_tree['item'][$next]['_matchrule'] == 'misc' ||
                        $parse_tree['item'][$next]['_matchrule'] == 'breaks' ||
                        $parse_tree['item'][$next]['_matchrule'] == 'mathmodeopen' ||
                        $parse_tree['item'][$next]['_matchrule'] == 'mathmodeclose' ||
                        $parse_tree['item'][$next]['_matchrule'] == 'begintexenv' ||
                        $parse_tree['item'][$next]['_matchrule'] == 'endtexenv' ) {
                        $parse_tree['item'][$next]['_matchrule'] = 'text';
                    }
                }
            }
        }
        return $parse_tree;
    }

    /**
     * This function searches a flat tree for matching block-ends and converts them to a better structure. 
     * It will also remap any parameters to a simpler form. And paint the mathmode bit on the blocks.
     * returns an array that has been remapped in that way.
     */
    public static function block_conversion($parse_tree) {
        // start by paintting the mathmode if not paintted in previous normalise or elsewhere
        if (!array_key_exists('mathmode', $parse_tree)) {
            $mathmode = false;
            $parse_tree['mathmode'] = false;
            if (array_key_exists('item', $parse_tree) && is_array($parse_tree['item']) && count($parse_tree['item']) > 1 && !array_key_exists('_matchrule', $parse_tree['item'])) {
                foreach ($parse_tree['item'] as $key => $value) {
                    if ($value['_matchrule'] == 'mathmodeclose') {
                        $mathmode = false;
                    } else if ($value['_matchrule'] == 'mathmodeopen') {
                        $mathmode = true;
                    } else if ($value['_matchrule'] == 'begintexenv' && array_search($value['value']['text'],self::$math_mode_envs)!==FALSE) {
                        $mathmode = true;
                    } else if ($value['_matchrule'] == 'endtexenv' && array_search($value['value']['text'],self::$math_mode_envs)!==FALSE) {
                        $mathmode = false;
                    }
                    $parse_tree['item'][$key]['mathmode'] = $mathmode;
                }
            }
        }

        $something_changed = true;
        while ($something_changed) {
            $something_changed = false;
            if (array_key_exists('item', $parse_tree) && is_array($parse_tree['item']) && count($parse_tree['item']) > 1 && !array_key_exists('_matchrule', $parse_tree['item'])) {
                $end_blocks = array();
                $start_blocks = array();
                foreach ($parse_tree['item'] as $key => $value) {
                    if ($value['_matchrule'] == 'blockclose') {
                        $end_blocks[] = $key;
                    } else if ($value['_matchrule'] == 'blockopen') {
                        $start_blocks[] = $key;
                    } else if ($value['_matchrule'] == 'blockempty') {
                        $parse_tree['item'][$key]['_matchrule'] = "block";
                        $parse_tree['item'][$key]['name'] = $parse_tree['item'][$key]['name'][1]['text'];
                        $params = array();

                        if (array_key_exists('params', $parse_tree['item'][$key])) {
                            if (array_key_exists('_matchrule', $parse_tree['item'][$key]['params'])) {
                                $params[$parse_tree['item'][$key]['params']['key']['text']] = $parse_tree['item'][$key]['params']['value']['text'];
                            } else {
                                foreach ($parse_tree['item'][$key]['params'] as $param) {
                                    $params[$param['key']['text']] = $param['value']['text'];
                                }
                            }
                        }
                        $parse_tree['item'][$key]['params'] = $params;
                        $parse_tree['item'][$key]['item'] = array();
                    }
                }

                $i = 0;
                while ($i < count($end_blocks)) {
                    $end_candidate_index = $end_blocks[$i];
                    $closest_start_candidate = -1;
                    foreach ($start_blocks as $cand) {
                        if ($cand < $end_candidate_index && $cand > $closest_start_candidate) {
                            $closest_start_candidate = $cand;
                        }
                    }
                    if ($i > 0 && $end_blocks[$i-1] > $closest_start_candidate) {
                        // There is a missmatch of open-close tags,
                        // generic error handling handles that
                        $i++;
                        break;
                    }

                    $i++;

                    if ($closest_start_candidate !== null && $parse_tree['item'][$end_candidate_index]['name'][1]['text'] == $parse_tree['item'][$closest_start_candidate]['name'][1]['text']) {
                        $parse_tree['item'][$closest_start_candidate]['_matchrule'] = "block";

                        $parse_tree['item'][$closest_start_candidate]['name'] = $parse_tree['item'][$closest_start_candidate]['name'][1]['text'];

                        $params = array();

                        if (array_key_exists('params', $parse_tree['item'][$closest_start_candidate])) {
                            if (array_key_exists('_matchrule', $parse_tree['item'][$closest_start_candidate]['params'])) {
                                $params[$parse_tree['item'][$closest_start_candidate]['params']['key']['text']] = $parse_tree['item'][$closest_start_candidate]['params']['value']['text'];
                            } else {
                                foreach ($parse_tree['item'][$closest_start_candidate]['params'] as $param) {
                                    $params[$param['key']['text']] = $param['value']['text'];
                                }
                            }
                        }
                        $parse_tree['item'][$closest_start_candidate]['params'] = $params;
                        $parse_tree['item'][$closest_start_candidate]['item'] = array();

                        foreach ($parse_tree['item'] as $key => $value) {
                            if ($key > $closest_start_candidate && $key < $end_candidate_index) {
                                $parse_tree['item'][$closest_start_candidate]['item'][] = $value;
                                $parse_tree['item'][$closest_start_candidate]['text'] .= $value['text'];
                                unset($parse_tree['item'][$key]);
                            }
                        }

                        $parse_tree['item'][$closest_start_candidate]['text'] .= $parse_tree['item'][$end_candidate_index]['text'];
                        unset($parse_tree['item'][$end_candidate_index]);

                        $something_changed = true;
                        break;
                    }
                }
            }
        }
   
        $err = stack_cas_castext_castextparser::extract_block_missmatch($parse_tree);
        if (count($err) > 0) {
            if (array_key_exists('errors', $parse_tree)) {
                $parse_tree['errors'] .= '<br/>' . implode('<br/>', $err);
            } else {
                $parse_tree['errors'] = implode('<br/>', $err);
            }
        }

        return $parse_tree;
    }

    private static function extract_block_missmatch($parse_tree) {
        $err = array();
        switch ($parse_tree['_matchrule']) {
            case "castext":
            case "block":
                if (array_key_exists('_matchrule', $parse_tree['item'])) {
                    $err = stack_cas_castext_castextparser::extract_block_missmatch($parse_tree['item']);
                } else {
                    $err = array();
                    foreach ($parse_tree['item'] as $sub_tree) {
                        $err = array_merge($err, stack_cas_castext_castextparser::extract_block_missmatch($sub_tree));
                    }
                }
                break;
            case "blockopen":
                $err[] = "'[[ " . $parse_tree['name'][1]['text'] . " ]]' " . stack_string('stackBlock_missmatch');
                break;
            case "blockclose":
                $err[] = "'[[/ " . $parse_tree['name'][1]['text'] . " ]]' " . stack_string('stackBlock_missmatch');
                break;
        }

        return $err;
    }


    /* texcasblock: "{@" cascontent:/[^@]+/ "@}" */
    protected $match_texcasblock_typestack = array('texcasblock');
    function match_texcasblock ($stack = array()) {
    	$matchrule = "texcasblock"; $result = $this->construct($matchrule, $matchrule, null);
    	$_3 = NULL;
    	do {
    		if (( $subres = $this->literal( '{@' ) ) !== FALSE) { $result["text"] .= $subres; }
    		else { $_3 = FALSE; break; }
    		$stack[] = $result; $result = $this->construct( $matchrule, "cascontent" ); 
    		if (( $subres = $this->rx( '/[^@]+/' ) ) !== FALSE) {
    			$result["text"] .= $subres;
    			$subres = $result; $result = array_pop($stack);
    			$this->store( $result, $subres, 'cascontent' );
    		}
    		else {
    			$result = array_pop($stack);
    			$_3 = FALSE; break;
    		}
    		if (( $subres = $this->literal( '@}' ) ) !== FALSE) { $result["text"] .= $subres; }
    		else { $_3 = FALSE; break; }
    		$_3 = TRUE; break;
    	}
    	while(0);
    	if( $_3 === TRUE ) { return $this->finalise($result); }
    	if( $_3 === FALSE) { return FALSE; }
    }


    /* rawcasblock: "{#" cascontent:/[^#]+/ "#}" */
    protected $match_rawcasblock_typestack = array('rawcasblock');
    function match_rawcasblock ($stack = array()) {
    	$matchrule = "rawcasblock"; $result = $this->construct($matchrule, $matchrule, null);
    	$_8 = NULL;
    	do {
    		if (( $subres = $this->literal( '{#' ) ) !== FALSE) { $result["text"] .= $subres; }
    		else { $_8 = FALSE; break; }
    		$stack[] = $result; $result = $this->construct( $matchrule, "cascontent" ); 
    		if (( $subres = $this->rx( '/[^#]+/' ) ) !== FALSE) {
    			$result["text"] .= $subres;
    			$subres = $result; $result = array_pop($stack);
    			$this->store( $result, $subres, 'cascontent' );
    		}
    		else {
    			$result = array_pop($stack);
    			$_8 = FALSE; break;
    		}
    		if (( $subres = $this->literal( '#}' ) ) !== FALSE) { $result["text"] .= $subres; }
    		else { $_8 = FALSE; break; }
    		$_8 = TRUE; break;
    	}
    	while(0);
    	if( $_8 === TRUE ) { return $this->finalise($result); }
    	if( $_8 === FALSE) { return FALSE; }
    }


    /* mathmodeopen: ( '\(' | '\[' ) */
    protected $match_mathmodeopen_typestack = array('mathmodeopen');
    function match_mathmodeopen ($stack = array()) {
    	$matchrule = "mathmodeopen"; $result = $this->construct($matchrule, $matchrule, null);
    	$_15 = NULL;
    	do {
    		$_13 = NULL;
    		do {
    			$res_10 = $result;
    			$pos_10 = $this->pos;
    			if (( $subres = $this->literal( '\(' ) ) !== FALSE) {
    				$result["text"] .= $subres;
    				$_13 = TRUE; break;
    			}
    			$result = $res_10;
    			$this->pos = $pos_10;
    			if (( $subres = $this->literal( '\[' ) ) !== FALSE) {
    				$result["text"] .= $subres;
    				$_13 = TRUE; break;
    			}
    			$result = $res_10;
    			$this->pos = $pos_10;
    			$_13 = FALSE; break;
    		}
    		while(0);
    		if( $_13 === FALSE) { $_15 = FALSE; break; }
    		$_15 = TRUE; break;
    	}
    	while(0);
    	if( $_15 === TRUE ) { return $this->finalise($result); }
    	if( $_15 === FALSE) { return FALSE; }
    }


    /* mathmodeclose: ( '\)' | '\]' ) */
    protected $match_mathmodeclose_typestack = array('mathmodeclose');
    function match_mathmodeclose ($stack = array()) {
    	$matchrule = "mathmodeclose"; $result = $this->construct($matchrule, $matchrule, null);
    	$_22 = NULL;
    	do {
    		$_20 = NULL;
    		do {
    			$res_17 = $result;
    			$pos_17 = $this->pos;
    			if (( $subres = $this->literal( '\)' ) ) !== FALSE) {
    				$result["text"] .= $subres;
    				$_20 = TRUE; break;
    			}
    			$result = $res_17;
    			$this->pos = $pos_17;
    			if (( $subres = $this->literal( '\]' ) ) !== FALSE) {
    				$result["text"] .= $subres;
    				$_20 = TRUE; break;
    			}
    			$result = $res_17;
    			$this->pos = $pos_17;
    			$_20 = FALSE; break;
    		}
    		while(0);
    		if( $_20 === FALSE) { $_22 = FALSE; break; }
    		$_22 = TRUE; break;
    	}
    	while(0);
    	if( $_22 === TRUE ) { return $this->finalise($result); }
    	if( $_22 === FALSE) { return FALSE; }
    }


    /* begintexenv: "\begin{" value:/[a-zA-Z0-9\*]+/ "}" */
    protected $match_begintexenv_typestack = array('begintexenv');
    function match_begintexenv ($stack = array()) {
    	$matchrule = "begintexenv"; $result = $this->construct($matchrule, $matchrule, null);
    	$_27 = NULL;
    	do {
    		if (( $subres = $this->literal( '\begin{' ) ) !== FALSE) { $result["text"] .= $subres; }
    		else { $_27 = FALSE; break; }
    		$stack[] = $result; $result = $this->construct( $matchrule, "value" ); 
    		if (( $subres = $this->rx( '/[a-zA-Z0-9\*]+/' ) ) !== FALSE) {
    			$result["text"] .= $subres;
    			$subres = $result; $result = array_pop($stack);
    			$this->store( $result, $subres, 'value' );
    		}
    		else {
    			$result = array_pop($stack);
    			$_27 = FALSE; break;
    		}
    		if (substr($this->string,$this->pos,1) == '}') {
    			$this->pos += 1;
    			$result["text"] .= '}';
    		}
    		else { $_27 = FALSE; break; }
    		$_27 = TRUE; break;
    	}
    	while(0);
    	if( $_27 === TRUE ) { return $this->finalise($result); }
    	if( $_27 === FALSE) { return FALSE; }
    }


    /* endtexenv: "\end{" value:/[a-zA-Z0-9\*]+/ "}" */
    protected $match_endtexenv_typestack = array('endtexenv');
    function match_endtexenv ($stack = array()) {
    	$matchrule = "endtexenv"; $result = $this->construct($matchrule, $matchrule, null);
    	$_32 = NULL;
    	do {
    		if (( $subres = $this->literal( '\end{' ) ) !== FALSE) { $result["text"] .= $subres; }
    		else { $_32 = FALSE; break; }
    		$stack[] = $result; $result = $this->construct( $matchrule, "value" ); 
    		if (( $subres = $this->rx( '/[a-zA-Z0-9\*]+/' ) ) !== FALSE) {
    			$result["text"] .= $subres;
    			$subres = $result; $result = array_pop($stack);
    			$this->store( $result, $subres, 'value' );
    		}
    		else {
    			$result = array_pop($stack);
    			$_32 = FALSE; break;
    		}
    		if (substr($this->string,$this->pos,1) == '}') {
    			$this->pos += 1;
    			$result["text"] .= '}';
    		}
    		else { $_32 = FALSE; break; }
    		$_32 = TRUE; break;
    	}
    	while(0);
    	if( $_32 === TRUE ) { return $this->finalise($result); }
    	if( $_32 === FALSE) { return FALSE; }
    }


    /* blockid: /[a-zA-Z0-9\-_]+/  */
    protected $match_blockid_typestack = array('blockid');
    function match_blockid ($stack = array()) {
    	$matchrule = "blockid"; $result = $this->construct($matchrule, $matchrule, null);
    	if (( $subres = $this->rx( '/[a-zA-Z0-9\-_]+/' ) ) !== FALSE) {
    		$result["text"] .= $subres;
    		return $this->finalise($result);
    	}
    	else { return FALSE; }
    }


    /* ws: (' ' | /[\n\t\r]/ )+ */
    protected $match_ws_typestack = array('ws');
    function match_ws ($stack = array()) {
    	$matchrule = "ws"; $result = $this->construct($matchrule, $matchrule, null);
    	$count = 0;
    	while (true) {
    		$res_41 = $result;
    		$pos_41 = $this->pos;
    		$_40 = NULL;
    		do {
    			$_38 = NULL;
    			do {
    				$res_35 = $result;
    				$pos_35 = $this->pos;
    				if (substr($this->string,$this->pos,1) == ' ') {
    					$this->pos += 1;
    					$result["text"] .= ' ';
    					$_38 = TRUE; break;
    				}
    				$result = $res_35;
    				$this->pos = $pos_35;
    				if (( $subres = $this->rx( '/[\n\t\r]/' ) ) !== FALSE) {
    					$result["text"] .= $subres;
    					$_38 = TRUE; break;
    				}
    				$result = $res_35;
    				$this->pos = $pos_35;
    				$_38 = FALSE; break;
    			}
    			while(0);
    			if( $_38 === FALSE) { $_40 = FALSE; break; }
    			$_40 = TRUE; break;
    		}
    		while(0);
    		if( $_40 === FALSE) {
    			$result = $res_41;
    			$this->pos = $pos_41;
    			unset( $res_41 );
    			unset( $pos_41 );
    			break;
    		}
    		$count++;
    	}
    	if ($count >= 1) { return $this->finalise($result); }
    	else { return FALSE; }
    }


    /* misc:  /[^\{\[\\]+/  */
    protected $match_misc_typestack = array('misc');
    function match_misc ($stack = array()) {
    	$matchrule = "misc"; $result = $this->construct($matchrule, $matchrule, null);
    	if (( $subres = $this->rx( '/[^\{\[\\\\]+/' ) ) !== FALSE) {
    		$result["text"] .= $subres;
    		return $this->finalise($result);
    	}
    	else { return FALSE; }
    }


    /* breaks:  ( '{' | '[' | '\\' ) */
    protected $match_breaks_typestack = array('breaks');
    function match_breaks ($stack = array()) {
    	$matchrule = "breaks"; $result = $this->construct($matchrule, $matchrule, null);
    	$_52 = NULL;
    	do {
    		$_50 = NULL;
    		do {
    			$res_43 = $result;
    			$pos_43 = $this->pos;
    			if (substr($this->string,$this->pos,1) == '{') {
    				$this->pos += 1;
    				$result["text"] .= '{';
    				$_50 = TRUE; break;
    			}
    			$result = $res_43;
    			$this->pos = $pos_43;
    			$_48 = NULL;
    			do {
    				$res_45 = $result;
    				$pos_45 = $this->pos;
    				if (substr($this->string,$this->pos,1) == '[') {
    					$this->pos += 1;
    					$result["text"] .= '[';
    					$_48 = TRUE; break;
    				}
    				$result = $res_45;
    				$this->pos = $pos_45;
    				if (substr($this->string,$this->pos,1) == '\\') {
    					$this->pos += 1;
    					$result["text"] .= '\\';
    					$_48 = TRUE; break;
    				}
    				$result = $res_45;
    				$this->pos = $pos_45;
    				$_48 = FALSE; break;
    			}
    			while(0);
    			if( $_48 === TRUE ) { $_50 = TRUE; break; }
    			$result = $res_43;
    			$this->pos = $pos_43;
    			$_50 = FALSE; break;
    		}
    		while(0);
    		if( $_50 === FALSE) { $_52 = FALSE; break; }
    		$_52 = TRUE; break;
    	}
    	while(0);
    	if( $_52 === TRUE ) { return $this->finalise($result); }
    	if( $_52 === FALSE) { return FALSE; }
    }


    /* param: ws key:blockid '=' q:/["']/ value:/[^$q]+/ "$q"  */
    protected $match_param_typestack = array('param');
    function match_param ($stack = array()) {
    	$matchrule = "param"; $result = $this->construct($matchrule, $matchrule, null);
    	$_60 = NULL;
    	do {
    		$matcher = 'match_'.'ws'; $key = $matcher; $pos = $this->pos;
    		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    		if ($subres !== FALSE) {
    			$this->store( $result, $subres );
    		}
    		else { $_60 = FALSE; break; }
    		$matcher = 'match_'.'blockid'; $key = $matcher; $pos = $this->pos;
    		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    		if ($subres !== FALSE) {
    			$this->store( $result, $subres, "key" );
    		}
    		else { $_60 = FALSE; break; }
    		if (substr($this->string,$this->pos,1) == '=') {
    			$this->pos += 1;
    			$result["text"] .= '=';
    		}
    		else { $_60 = FALSE; break; }
    		$stack[] = $result; $result = $this->construct( $matchrule, "q" ); 
    		if (( $subres = $this->rx( '/["\']/' ) ) !== FALSE) {
    			$result["text"] .= $subres;
    			$subres = $result; $result = array_pop($stack);
    			$this->store( $result, $subres, 'q' );
    		}
    		else {
    			$result = array_pop($stack);
    			$_60 = FALSE; break;
    		}
    		$stack[] = $result; $result = $this->construct( $matchrule, "value" ); 
    		if (( $subres = $this->rx( '/[^'.$this->expression($result, $stack, 'q').']+/' ) ) !== FALSE) {
    			$result["text"] .= $subres;
    			$subres = $result; $result = array_pop($stack);
    			$this->store( $result, $subres, 'value' );
    		}
    		else {
    			$result = array_pop($stack);
    			$_60 = FALSE; break;
    		}
    		if (( $subres = $this->literal( ''.$this->expression($result, $stack, 'q').'' ) ) !== FALSE) { $result["text"] .= $subres; }
    		else { $_60 = FALSE; break; }
    		$_60 = TRUE; break;
    	}
    	while(0);
    	if( $_60 === TRUE ) { return $this->finalise($result); }
    	if( $_60 === FALSE) { return FALSE; }
    }


    /* ioblock: '[[' ws? channel:blockid ws? ':' ws? var:blockid ws? ']]' */
    protected $match_ioblock_typestack = array('ioblock');
    function match_ioblock ($stack = array()) {
    	$matchrule = "ioblock"; $result = $this->construct($matchrule, $matchrule, null);
    	$_71 = NULL;
    	do {
    		if (( $subres = $this->literal( '[[' ) ) !== FALSE) { $result["text"] .= $subres; }
    		else { $_71 = FALSE; break; }
    		$res_63 = $result;
    		$pos_63 = $this->pos;
    		$matcher = 'match_'.'ws'; $key = $matcher; $pos = $this->pos;
    		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    		if ($subres !== FALSE) {
    			$this->store( $result, $subres );
    		}
    		else {
    			$result = $res_63;
    			$this->pos = $pos_63;
    			unset( $res_63 );
    			unset( $pos_63 );
    		}
    		$matcher = 'match_'.'blockid'; $key = $matcher; $pos = $this->pos;
    		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    		if ($subres !== FALSE) {
    			$this->store( $result, $subres, "channel" );
    		}
    		else { $_71 = FALSE; break; }
    		$res_65 = $result;
    		$pos_65 = $this->pos;
    		$matcher = 'match_'.'ws'; $key = $matcher; $pos = $this->pos;
    		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    		if ($subres !== FALSE) {
    			$this->store( $result, $subres );
    		}
    		else {
    			$result = $res_65;
    			$this->pos = $pos_65;
    			unset( $res_65 );
    			unset( $pos_65 );
    		}
    		if (substr($this->string,$this->pos,1) == ':') {
    			$this->pos += 1;
    			$result["text"] .= ':';
    		}
    		else { $_71 = FALSE; break; }
    		$res_67 = $result;
    		$pos_67 = $this->pos;
    		$matcher = 'match_'.'ws'; $key = $matcher; $pos = $this->pos;
    		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    		if ($subres !== FALSE) {
    			$this->store( $result, $subres );
    		}
    		else {
    			$result = $res_67;
    			$this->pos = $pos_67;
    			unset( $res_67 );
    			unset( $pos_67 );
    		}
    		$matcher = 'match_'.'blockid'; $key = $matcher; $pos = $this->pos;
    		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    		if ($subres !== FALSE) {
    			$this->store( $result, $subres, "var" );
    		}
    		else { $_71 = FALSE; break; }
    		$res_69 = $result;
    		$pos_69 = $this->pos;
    		$matcher = 'match_'.'ws'; $key = $matcher; $pos = $this->pos;
    		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    		if ($subres !== FALSE) {
    			$this->store( $result, $subres );
    		}
    		else {
    			$result = $res_69;
    			$this->pos = $pos_69;
    			unset( $res_69 );
    			unset( $pos_69 );
    		}
    		if (( $subres = $this->literal( ']]' ) ) !== FALSE) { $result["text"] .= $subres; }
    		else { $_71 = FALSE; break; }
    		$_71 = TRUE; break;
    	}
    	while(0);
    	if( $_71 === TRUE ) { return $this->finalise($result); }
    	if( $_71 === FALSE) { return FALSE; }
    }


    /* blockempty: '[[' ws? name:blockid (params:param)* ws? '/]]' */
    protected $match_blockempty_typestack = array('blockempty');
    function match_blockempty ($stack = array()) {
    	$matchrule = "blockempty"; $result = $this->construct($matchrule, $matchrule, null);
    	$_81 = NULL;
    	do {
    		if (( $subres = $this->literal( '[[' ) ) !== FALSE) { $result["text"] .= $subres; }
    		else { $_81 = FALSE; break; }
    		$res_74 = $result;
    		$pos_74 = $this->pos;
    		$matcher = 'match_'.'ws'; $key = $matcher; $pos = $this->pos;
    		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    		if ($subres !== FALSE) {
    			$this->store( $result, $subres );
    		}
    		else {
    			$result = $res_74;
    			$this->pos = $pos_74;
    			unset( $res_74 );
    			unset( $pos_74 );
    		}
    		$matcher = 'match_'.'blockid'; $key = $matcher; $pos = $this->pos;
    		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    		if ($subres !== FALSE) {
    			$this->store( $result, $subres, "name" );
    		}
    		else { $_81 = FALSE; break; }
    		while (true) {
    			$res_78 = $result;
    			$pos_78 = $this->pos;
    			$_77 = NULL;
    			do {
    				$matcher = 'match_'.'param'; $key = $matcher; $pos = $this->pos;
    				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    				if ($subres !== FALSE) {
    					$this->store( $result, $subres, "params" );
    				}
    				else { $_77 = FALSE; break; }
    				$_77 = TRUE; break;
    			}
    			while(0);
    			if( $_77 === FALSE) {
    				$result = $res_78;
    				$this->pos = $pos_78;
    				unset( $res_78 );
    				unset( $pos_78 );
    				break;
    			}
    		}
    		$res_79 = $result;
    		$pos_79 = $this->pos;
    		$matcher = 'match_'.'ws'; $key = $matcher; $pos = $this->pos;
    		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    		if ($subres !== FALSE) {
    			$this->store( $result, $subres );
    		}
    		else {
    			$result = $res_79;
    			$this->pos = $pos_79;
    			unset( $res_79 );
    			unset( $pos_79 );
    		}
    		if (( $subres = $this->literal( '/]]' ) ) !== FALSE) { $result["text"] .= $subres; }
    		else { $_81 = FALSE; break; }
    		$_81 = TRUE; break;
    	}
    	while(0);
    	if( $_81 === TRUE ) { return $this->finalise($result); }
    	if( $_81 === FALSE) { return FALSE; }
    }


    /* blockopen: '[[' ws? name:blockid (params:param)* ws? ']]' */
    protected $match_blockopen_typestack = array('blockopen');
    function match_blockopen ($stack = array()) {
    	$matchrule = "blockopen"; $result = $this->construct($matchrule, $matchrule, null);
    	$_91 = NULL;
    	do {
    		if (( $subres = $this->literal( '[[' ) ) !== FALSE) { $result["text"] .= $subres; }
    		else { $_91 = FALSE; break; }
    		$res_84 = $result;
    		$pos_84 = $this->pos;
    		$matcher = 'match_'.'ws'; $key = $matcher; $pos = $this->pos;
    		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    		if ($subres !== FALSE) {
    			$this->store( $result, $subres );
    		}
    		else {
    			$result = $res_84;
    			$this->pos = $pos_84;
    			unset( $res_84 );
    			unset( $pos_84 );
    		}
    		$matcher = 'match_'.'blockid'; $key = $matcher; $pos = $this->pos;
    		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    		if ($subres !== FALSE) {
    			$this->store( $result, $subres, "name" );
    		}
    		else { $_91 = FALSE; break; }
    		while (true) {
    			$res_88 = $result;
    			$pos_88 = $this->pos;
    			$_87 = NULL;
    			do {
    				$matcher = 'match_'.'param'; $key = $matcher; $pos = $this->pos;
    				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    				if ($subres !== FALSE) {
    					$this->store( $result, $subres, "params" );
    				}
    				else { $_87 = FALSE; break; }
    				$_87 = TRUE; break;
    			}
    			while(0);
    			if( $_87 === FALSE) {
    				$result = $res_88;
    				$this->pos = $pos_88;
    				unset( $res_88 );
    				unset( $pos_88 );
    				break;
    			}
    		}
    		$res_89 = $result;
    		$pos_89 = $this->pos;
    		$matcher = 'match_'.'ws'; $key = $matcher; $pos = $this->pos;
    		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    		if ($subres !== FALSE) {
    			$this->store( $result, $subres );
    		}
    		else {
    			$result = $res_89;
    			$this->pos = $pos_89;
    			unset( $res_89 );
    			unset( $pos_89 );
    		}
    		if (( $subres = $this->literal( ']]' ) ) !== FALSE) { $result["text"] .= $subres; }
    		else { $_91 = FALSE; break; }
    		$_91 = TRUE; break;
    	}
    	while(0);
    	if( $_91 === TRUE ) { return $this->finalise($result); }
    	if( $_91 === FALSE) { return FALSE; }
    }


    /* blockclose: '[[/' ws? name:blockid ws? ']]' */
    protected $match_blockclose_typestack = array('blockclose');
    function match_blockclose ($stack = array()) {
    	$matchrule = "blockclose"; $result = $this->construct($matchrule, $matchrule, null);
    	$_98 = NULL;
    	do {
    		if (( $subres = $this->literal( '[[/' ) ) !== FALSE) { $result["text"] .= $subres; }
    		else { $_98 = FALSE; break; }
    		$res_94 = $result;
    		$pos_94 = $this->pos;
    		$matcher = 'match_'.'ws'; $key = $matcher; $pos = $this->pos;
    		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    		if ($subres !== FALSE) {
    			$this->store( $result, $subres );
    		}
    		else {
    			$result = $res_94;
    			$this->pos = $pos_94;
    			unset( $res_94 );
    			unset( $pos_94 );
    		}
    		$matcher = 'match_'.'blockid'; $key = $matcher; $pos = $this->pos;
    		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    		if ($subres !== FALSE) {
    			$this->store( $result, $subres, "name" );
    		}
    		else { $_98 = FALSE; break; }
    		$res_96 = $result;
    		$pos_96 = $this->pos;
    		$matcher = 'match_'.'ws'; $key = $matcher; $pos = $this->pos;
    		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    		if ($subres !== FALSE) {
    			$this->store( $result, $subres );
    		}
    		else {
    			$result = $res_96;
    			$this->pos = $pos_96;
    			unset( $res_96 );
    			unset( $pos_96 );
    		}
    		if (( $subres = $this->literal( ']]' ) ) !== FALSE) { $result["text"] .= $subres; }
    		else { $_98 = FALSE; break; }
    		$_98 = TRUE; break;
    	}
    	while(0);
    	if( $_98 === TRUE ) { return $this->finalise($result); }
    	if( $_98 === FALSE) { return FALSE; }
    }


    /* castext: ( item:ioblock | item:texcasblock | item:rawcasblock | item:mathmodeopen | item:mathmodeclose | item:misc | item:ws | item:blockclose | item:blockopen | item:blockempty | item:begintexenv | item:endtexenv | item:breaks)* */
    protected $match_castext_typestack = array('castext');
    function match_castext ($stack = array()) {
    	$matchrule = "castext"; $result = $this->construct($matchrule, $matchrule, null);
    	while (true) {
    		$res_150 = $result;
    		$pos_150 = $this->pos;
    		$_149 = NULL;
    		do {
    			$_147 = NULL;
    			do {
    				$res_100 = $result;
    				$pos_100 = $this->pos;
    				$matcher = 'match_'.'ioblock'; $key = $matcher; $pos = $this->pos;
    				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    				if ($subres !== FALSE) {
    					$this->store( $result, $subres, "item" );
    					$_147 = TRUE; break;
    				}
    				$result = $res_100;
    				$this->pos = $pos_100;
    				$_145 = NULL;
    				do {
    					$res_102 = $result;
    					$pos_102 = $this->pos;
    					$matcher = 'match_'.'texcasblock'; $key = $matcher; $pos = $this->pos;
    					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    					if ($subres !== FALSE) {
    						$this->store( $result, $subres, "item" );
    						$_145 = TRUE; break;
    					}
    					$result = $res_102;
    					$this->pos = $pos_102;
    					$_143 = NULL;
    					do {
    						$res_104 = $result;
    						$pos_104 = $this->pos;
    						$matcher = 'match_'.'rawcasblock'; $key = $matcher; $pos = $this->pos;
    						$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    						if ($subres !== FALSE) {
    							$this->store( $result, $subres, "item" );
    							$_143 = TRUE; break;
    						}
    						$result = $res_104;
    						$this->pos = $pos_104;
    						$_141 = NULL;
    						do {
    							$res_106 = $result;
    							$pos_106 = $this->pos;
    							$matcher = 'match_'.'mathmodeopen'; $key = $matcher; $pos = $this->pos;
    							$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    							if ($subres !== FALSE) {
    								$this->store( $result, $subres, "item" );
    								$_141 = TRUE; break;
    							}
    							$result = $res_106;
    							$this->pos = $pos_106;
    							$_139 = NULL;
    							do {
    								$res_108 = $result;
    								$pos_108 = $this->pos;
    								$matcher = 'match_'.'mathmodeclose'; $key = $matcher; $pos = $this->pos;
    								$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    								if ($subres !== FALSE) {
    									$this->store( $result, $subres, "item" );
    									$_139 = TRUE; break;
    								}
    								$result = $res_108;
    								$this->pos = $pos_108;
    								$_137 = NULL;
    								do {
    									$res_110 = $result;
    									$pos_110 = $this->pos;
    									$matcher = 'match_'.'misc'; $key = $matcher; $pos = $this->pos;
    									$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    									if ($subres !== FALSE) {
    										$this->store( $result, $subres, "item" );
    										$_137 = TRUE; break;
    									}
    									$result = $res_110;
    									$this->pos = $pos_110;
    									$_135 = NULL;
    									do {
    										$res_112 = $result;
    										$pos_112 = $this->pos;
    										$matcher = 'match_'.'ws'; $key = $matcher; $pos = $this->pos;
    										$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    										if ($subres !== FALSE) {
    											$this->store( $result, $subres, "item" );
    											$_135 = TRUE; break;
    										}
    										$result = $res_112;
    										$this->pos = $pos_112;
    										$_133 = NULL;
    										do {
    											$res_114 = $result;
    											$pos_114 = $this->pos;
    											$matcher = 'match_'.'blockclose'; $key = $matcher; $pos = $this->pos;
    											$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    											if ($subres !== FALSE) {
    												$this->store( $result, $subres, "item" );
    												$_133 = TRUE; break;
    											}
    											$result = $res_114;
    											$this->pos = $pos_114;
    											$_131 = NULL;
    											do {
    												$res_116 = $result;
    												$pos_116 = $this->pos;
    												$matcher = 'match_'.'blockopen'; $key = $matcher; $pos = $this->pos;
    												$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    												if ($subres !== FALSE) {
    													$this->store( $result, $subres, "item" );
    													$_131 = TRUE; break;
    												}
    												$result = $res_116;
    												$this->pos = $pos_116;
    												$_129 = NULL;
    												do {
    													$res_118 = $result;
    													$pos_118 = $this->pos;
    													$matcher = 'match_'.'blockempty'; $key = $matcher; $pos = $this->pos;
    													$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    													if ($subres !== FALSE) {
    														$this->store( $result, $subres, "item" );
    														$_129 = TRUE; break;
    													}
    													$result = $res_118;
    													$this->pos = $pos_118;
    													$_127 = NULL;
    													do {
    														$res_120 = $result;
    														$pos_120 = $this->pos;
    														$matcher = 'match_'.'begintexenv'; $key = $matcher; $pos = $this->pos;
    														$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    														if ($subres !== FALSE) {
    															$this->store( $result, $subres, "item" );
    															$_127 = TRUE; break;
    														}
    														$result = $res_120;
    														$this->pos = $pos_120;
    														$_125 = NULL;
    														do {
    															$res_122 = $result;
    															$pos_122 = $this->pos;
    															$matcher = 'match_'.'endtexenv'; $key = $matcher; $pos = $this->pos;
    															$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    															if ($subres !== FALSE) {
    																$this->store( $result, $subres, "item" );
    																$_125 = TRUE; break;
    															}
    															$result = $res_122;
    															$this->pos = $pos_122;
    															$matcher = 'match_'.'breaks'; $key = $matcher; $pos = $this->pos;
    															$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    															if ($subres !== FALSE) {
    																$this->store( $result, $subres, "item" );
    																$_125 = TRUE; break;
    															}
    															$result = $res_122;
    															$this->pos = $pos_122;
    															$_125 = FALSE; break;
    														}
    														while(0);
    														if( $_125 === TRUE ) { $_127 = TRUE; break; }
    														$result = $res_120;
    														$this->pos = $pos_120;
    														$_127 = FALSE; break;
    													}
    													while(0);
    													if( $_127 === TRUE ) { $_129 = TRUE; break; }
    													$result = $res_118;
    													$this->pos = $pos_118;
    													$_129 = FALSE; break;
    												}
    												while(0);
    												if( $_129 === TRUE ) { $_131 = TRUE; break; }
    												$result = $res_116;
    												$this->pos = $pos_116;
    												$_131 = FALSE; break;
    											}
    											while(0);
    											if( $_131 === TRUE ) { $_133 = TRUE; break; }
    											$result = $res_114;
    											$this->pos = $pos_114;
    											$_133 = FALSE; break;
    										}
    										while(0);
    										if( $_133 === TRUE ) { $_135 = TRUE; break; }
    										$result = $res_112;
    										$this->pos = $pos_112;
    										$_135 = FALSE; break;
    									}
    									while(0);
    									if( $_135 === TRUE ) { $_137 = TRUE; break; }
    									$result = $res_110;
    									$this->pos = $pos_110;
    									$_137 = FALSE; break;
    								}
    								while(0);
    								if( $_137 === TRUE ) { $_139 = TRUE; break; }
    								$result = $res_108;
    								$this->pos = $pos_108;
    								$_139 = FALSE; break;
    							}
    							while(0);
    							if( $_139 === TRUE ) { $_141 = TRUE; break; }
    							$result = $res_106;
    							$this->pos = $pos_106;
    							$_141 = FALSE; break;
    						}
    						while(0);
    						if( $_141 === TRUE ) { $_143 = TRUE; break; }
    						$result = $res_104;
    						$this->pos = $pos_104;
    						$_143 = FALSE; break;
    					}
    					while(0);
    					if( $_143 === TRUE ) { $_145 = TRUE; break; }
    					$result = $res_102;
    					$this->pos = $pos_102;
    					$_145 = FALSE; break;
    				}
    				while(0);
    				if( $_145 === TRUE ) { $_147 = TRUE; break; }
    				$result = $res_100;
    				$this->pos = $pos_100;
    				$_147 = FALSE; break;
    			}
    			while(0);
    			if( $_147 === FALSE) { $_149 = FALSE; break; }
    			$_149 = TRUE; break;
    		}
    		while(0);
    		if( $_149 === FALSE) {
    			$result = $res_150;
    			$this->pos = $pos_150;
    			unset( $res_150 );
    			unset( $pos_150 );
    			break;
    		}
    	}
    	return $this->finalise($result);
    }




    // SO WOULD HAVE WANTED THIS BUT COULD NOT UNDERSTAND HOWTO... SO NOW WE HAVE THE NESTED PARSING DONE AFTERWARDS
    // block: '[[' ws? name:blockid (params:param)* ws? ']]' content:castext '[[/' ws? "$name" ws? ']]'

}


/**
 * A custom datastructure for skipping the annoying task of working with references to arrays. The only array in this structure is something we do not modify.
 */
class stack_cas_castext_parsetreenode {

    public $parent = null;
    public $next_sibling = null;
    public $previous_sibling = null;
    public $first_child = null;
    // There are five types, castext is the root, blocks are containers and text, rawcasblock and texcasblock are root nodes.
    public $type = "castext";
    private $params = null;
    private $content = "";
    public $mathmode = false;

    /**
     * Converts the nested array form tree to parsetreenode-tree
     */
    public static function build_from_nested($parse_tree, $parent=null) {
        $node = new stack_cas_castext_parsetreenode();
        $node->parent = $parent;
        if (array_key_exists('mathmode', $parse_tree)) {
            $node->mathmode = $parse_tree['mathmode'];
        }
        switch ($parse_tree['_matchrule']) {
            case "block":
                $node->params = $parse_tree['params'];
                $node->content = $parse_tree['name'];
            case "castext":
                if (array_key_exists('_matchrule', $parse_tree['item'])) {
                    $node->first_child = stack_cas_castext_parsetreenode::build_from_nested($parse_tree['item'], $node);
                } else {
                    $prev = null;
                    foreach ($parse_tree['item'] as $sub_tree) {
                        $n = stack_cas_castext_parsetreenode::build_from_nested($sub_tree, $node);
                        if ($prev !== null) {
                            $n->previous_sibling = $prev;
                            $prev->next_sibling = $n;
                        } else {
                            $node->first_child = $n;
                        }
                        $prev = $n;
                    }
                }
                $node->type = $parse_tree['_matchrule'];
                break;
            case "rawcasblock":
            case "texcasblock":
                $node->type = $parse_tree['_matchrule'];
                $node->content = $parse_tree['cascontent']['text'];
                break;
            default:
                $node->type = 'text';
                $node->content = $parse_tree['text'];
        }
        $node->normalize();
        return $node;
    }

    /**
     * Combines adjacent text-nodes.
     */
    public function normalize() {
        while ($this->type == 'text' && $this->next_sibling !== null && $this->next_sibling->type == 'text') {
            $extra = $this->next_sibling;
            $this->content .= $extra->content;
            $this->next_sibling = $extra->next_sibling;
            if ($this->next_sibling !== null) {
                $this->next_sibling->previous_sibling = $this;
            }
        }
        if ($this->next_sibling !== null) {
            $this->next_sibling->normalize();
        }
        if ($this->is_container() && $this->first_child !== null) {
            $this->first_child->normalize();
        }
    }

    /**
     * Returns true if there could be somekind of a substructure.
     */
    public function is_container() {
        if ($this->type == 'castext' || $this->type == 'block') {
            return true;
        }
        return false;
    }

    /**
     * Converts the node to a text node with the given content.
     */
    public function convert_to_text($new_content) {
        $this->type = "text";
        $this->content = $new_content;
        // Clear other details just in case, makes dumping the var cleaner when debuging
        $this->first_child = null;
        $this->params = array();
    }

    /**
     * Gets the name of this block, the content of this text-node or the cascontent of this casblock
     */
    public function get_content() {
        return $this->content;
    }

    /**
     * Gets the mathmode
     */
    public function get_mathmode() {
        return $this->mathmode;
    }

    /**
     * Returns the value of a parameter, usefull for nodes of the block-type. You can also set the default value returned should such a parameter be missing.
     */
    public function get_parameter($key, $default=null) {
        if (@array_key_exists($key, $this->params)) {
            return $this->params[$key];
        }
        return $default;
    }

    /**
     * Use this if you care if a parameter actually exists.
     */
    public function parameter_exists($key) {
        if ($this->params !== null) {
            return array_key_exists($key, $this->params);
        }
        return false;
    }

    /**
     * Returns an array containing all the parameters.
     */
    public function get_parameters() {
        if ($this->params === null) {
            return array();
        }
        return $this->params;
    }

    /**
     * Destroys this node (and its children) and removes it from its parent. Should you wish to access the parent the parent-link of this node will work even after destruction.
     */
    public function destroy_node() {
        if ($this->parent->first_child === $this) {
            $this->parent->first_child = $this->next_sibling;
        }
        if ($this->next_sibling !== null) {
            $this->next_sibling->previous_sibling = $this->previous_sibling;
        }
        if ($this->previous_sibling !== null) {
            $this->previous_sibling->next_sibling = $this->next_sibling;
        }
    }

    /**
     * Destroys this node but promotes its children to its place. Perfect for removing if-blocks and other wrappers.
     */
    public function destroy_node_promote_children() {
        if ($this->first_child !== null) {
            $next = $this->next_sibling;
            $iter = $this->first_child;
            if ($this->parent->first_child === $this) {
                $this->parent->first_child = $iter;
            }
            if ($this->previous_sibling !== null) {
                $this->previous_sibling->next_sibling = $iter;
            }
            $iter->previous_sibling = $this->previous_sibling;
            $iter->parent = $this->parent;
            while ($iter->next_sibling !== null) {
                $iter->parent = $this->parent;
                $iter = $iter->next_sibling;
            }
            $iter->parent = $this->parent;
            $iter->next_sibling = $next;
            if ($next !== null) {
                $next->previous_sibling = $iter;
            }
        } else {
            if ($this->next_sibling !== null && $this->previous_sibling !== null) {
                $this->previous_sibling->next_sibling = $this->next_sibling;
                $this->next_sibling->previous_sibling = $this->previous_sibling;
            } else if ($this->previous_sibling !== null) {
                $this->previous_sibling->next_sibling = null;
            } else {
                $this->parent->first_child = null;
            }
        }
    }

    /**
     * Presents the node in string form, might not match perfectly to the original content as quotes and whitespace may have changed.
     */
    public function to_string() {
        $r = "";
        switch ($this->type) {
            case "block":
                $r .= "[[ " . $this->content;
                if (count($this->params) > 0) {
                    foreach ($this->params as $key => $value) {
                        $r .= " $key=";
                        if (strpos($value, '"') === false) {
                            $r .= '"' . $value . '"';
                        } else {
                            $r .= "'$value'";
                        }
                    }
                }
                $r .= " ]]";

                $iterator = $this->first_child;
                while ($iterator !== null) {
                    $r .= $iterator->to_string();
                    $iterator = $iterator->next_sibling;
                }


                $r .= "[[/ " . $this->content . " ]]";
                break;
            case "castext":
                $iterator = $this->first_child;
                while ($iterator !== null) {
                    $r .= $iterator->to_string();
                    $iterator = $iterator->next_sibling;
                }
                break;  
            case "text":
                return $this->content;
            case "texcasblock":
                return "{@" . $this->content . "@}";
            case "rawcasblock":
                return "{#" . $this->content . "#}";
        }

        return $r;
    }
}

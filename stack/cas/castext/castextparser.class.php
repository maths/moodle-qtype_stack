<?php
error_reporting(E_ALL);
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
 ** php ../../../lib/php-peg/cli.php castext.peg.inc > castextparser.class.php
 **/
require_once(dirname(__FILE__) . '/../../../lib/php-peg/autoloader.php');
use hafriedlander\Peg\Parser;
/**
 * Defines the text parser for identifying STACK specific parts from CAStext, does not work with XML, 
 * intended to parse text-fragments and attribute values.
 * Pointless to use if your text does not include the following strings "{@" or "{#"
 */
class stack_cas_castext_castextparser extends Parser\Basic {

    /**
     * A function to test a string for necessary features related to castextparser.
     * returns true if the string should be passed trough the parser
     */
    public static function castext_parsing_required($test) {
        return (strpos($test,"{@")!==FALSE || strpos($test,"{#")!==FALSE || strpos($test,"[[")!==FALSE);
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
                if (array_key_exists('_matchrule',$parse_tree['item'])) {
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
                        if (strpos($value,'"') === FALSE) {
                            $r .= '"' . $value . '"';
                        } else {
                            $r .= "'$value'";
                        }
                    }
                }

                $r .= " ]]";

                if (array_key_exists('_matchrule',$parse_tree['item'])) {
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
        if (array_key_exists('item',$parse_tree) && is_array($parse_tree['item']) && !array_key_exists('_matchrule',$parse_tree['item']) && count($parse_tree['item']) > 1) {
            // Key listing maybe not continuous...
            $keys = array_keys($parse_tree['item']); 
            for ($i=0; $i<count($keys)-1; $i++) {
                $now = $keys[$i];
                $next = $keys[$i+1];
                if ($parse_tree['item'][$now]['_matchrule'] == 'ioblock' || $parse_tree['item'][$now]['_matchrule'] == 'ws' || $parse_tree['item'][$now]['_matchrule'] == 'misc' || $parse_tree['item'][$now]['_matchrule'] == 'breaks' || $parse_tree['item'][$now]['_matchrule'] == 'text') {
                    if ($parse_tree['item'][$next]['_matchrule'] == 'ioblock' || $parse_tree['item'][$next]['_matchrule'] == 'ws' || $parse_tree['item'][$next]['_matchrule'] == 'misc' || $parse_tree['item'][$next]['_matchrule'] == 'breaks') {
                        $parse_tree['item'][$next]['text'] = $parse_tree['item'][$now]['text'].$parse_tree['item'][$next]['text'];
                        $parse_tree['item'][$next]['_matchrule'] = 'text';
                        unset($parse_tree['item'][$now]);
                    } else {
                        $parse_tree['item'][$now]['_matchrule'] = 'text';
                    }
                } else {
                    $parse_tree['item'][$now] = stack_cas_castext_castextparser::normalize($parse_tree['item'][$now]);
                    if ($parse_tree['item'][$next]['_matchrule'] == 'ioblock' || $parse_tree['item'][$next]['_matchrule'] == 'ws' || $parse_tree['item'][$next]['_matchrule'] == 'misc' || $parse_tree['item'][$next]['_matchrule'] == 'breaks') {
                        $parse_tree['item'][$next]['_matchrule'] = 'text';
                    }
                }
            }
        }
        return $parse_tree;
    }

    /**
     * This function searches a flat tree for matching block-ends and converts them to a better structure. 
     * It will also remap any parameters to a simpler form.
     * returns an array that has been remapped in that way.
     */
    public static function block_conversion($parse_tree) {
        $something_changed = TRUE;
        while ($something_changed) {
            $something_changed = FALSE;
            if (array_key_exists('item',$parse_tree) && is_array($parse_tree['item']) && count($parse_tree['item']) > 1 && !array_key_exists('_matchrule',$parse_tree['item'])) {
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

                        if (array_key_exists('params',$parse_tree['item'][$key])) {
                            if (array_key_exists('_matchrule',$parse_tree['item'][$key]['params'])) {
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
                        // There is a missmatch of open-close-tags
                        $i++;
                        break;
                    }

                    $i++;

                    if ($closest_start_candidate !== NULL && $parse_tree['item'][$end_candidate_index]['name'][1]['text'] == $parse_tree['item'][$closest_start_candidate]['name'][1]['text']) {
                        $parse_tree['item'][$closest_start_candidate]['_matchrule'] = "block";
 
                        $parse_tree['item'][$closest_start_candidate]['name'] = $parse_tree['item'][$closest_start_candidate]['name'][1]['text'];
 
                        $params = array();

                        if (array_key_exists('params',$parse_tree['item'][$closest_start_candidate])) {
                            if (array_key_exists('_matchrule',$parse_tree['item'][$closest_start_candidate]['params'])) {
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

                        $something_changed = TRUE;
                        break;
                    }
                }
            }
        }
        return $parse_tree;
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
    		$res_17 = $result;
    		$pos_17 = $this->pos;
    		$_16 = NULL;
    		do {
    			$_14 = NULL;
    			do {
    				$res_11 = $result;
    				$pos_11 = $this->pos;
    				if (substr($this->string,$this->pos,1) == ' ') {
    					$this->pos += 1;
    					$result["text"] .= ' ';
    					$_14 = TRUE; break;
    				}
    				$result = $res_11;
    				$this->pos = $pos_11;
    				if (( $subres = $this->rx( '/[\n\t\r]/' ) ) !== FALSE) {
    					$result["text"] .= $subres;
    					$_14 = TRUE; break;
    				}
    				$result = $res_11;
    				$this->pos = $pos_11;
    				$_14 = FALSE; break;
    			}
    			while(0);
    			if( $_14 === FALSE) { $_16 = FALSE; break; }
    			$_16 = TRUE; break;
    		}
    		while(0);
    		if( $_16 === FALSE) {
    			$result = $res_17;
    			$this->pos = $pos_17;
    			unset( $res_17 );
    			unset( $pos_17 );
    			break;
    		}
    		$count++;
    	}
    	if ($count >= 1) { return $this->finalise($result); }
    	else { return FALSE; }
    }


    /* misc:  /[^\{\[]+/  */
    protected $match_misc_typestack = array('misc');
    function match_misc ($stack = array()) {
    	$matchrule = "misc"; $result = $this->construct($matchrule, $matchrule, null);
    	if (( $subres = $this->rx( '/[^\{\[]+/' ) ) !== FALSE) {
    		$result["text"] .= $subres;
    		return $this->finalise($result);
    	}
    	else { return FALSE; }
    }


    /* breaks:  ( '{' |  '[' ) */
    protected $match_breaks_typestack = array('breaks');
    function match_breaks ($stack = array()) {
    	$matchrule = "breaks"; $result = $this->construct($matchrule, $matchrule, null);
    	$_24 = NULL;
    	do {
    		$_22 = NULL;
    		do {
    			$res_19 = $result;
    			$pos_19 = $this->pos;
    			if (substr($this->string,$this->pos,1) == '{') {
    				$this->pos += 1;
    				$result["text"] .= '{';
    				$_22 = TRUE; break;
    			}
    			$result = $res_19;
    			$this->pos = $pos_19;
    			if (substr($this->string,$this->pos,1) == '[') {
    				$this->pos += 1;
    				$result["text"] .= '[';
    				$_22 = TRUE; break;
    			}
    			$result = $res_19;
    			$this->pos = $pos_19;
    			$_22 = FALSE; break;
    		}
    		while(0);
    		if( $_22 === FALSE) { $_24 = FALSE; break; }
    		$_24 = TRUE; break;
    	}
    	while(0);
    	if( $_24 === TRUE ) { return $this->finalise($result); }
    	if( $_24 === FALSE) { return FALSE; }
    }


    /* param: ws key:blockid '=' q:/["']/ value:/[^$q]+/ "$q"  */
    protected $match_param_typestack = array('param');
    function match_param ($stack = array()) {
    	$matchrule = "param"; $result = $this->construct($matchrule, $matchrule, null);
    	$_32 = NULL;
    	do {
    		$matcher = 'match_'.'ws'; $key = $matcher; $pos = $this->pos;
    		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    		if ($subres !== FALSE) {
    			$this->store( $result, $subres );
    		}
    		else { $_32 = FALSE; break; }
    		$matcher = 'match_'.'blockid'; $key = $matcher; $pos = $this->pos;
    		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    		if ($subres !== FALSE) {
    			$this->store( $result, $subres, "key" );
    		}
    		else { $_32 = FALSE; break; }
    		if (substr($this->string,$this->pos,1) == '=') {
    			$this->pos += 1;
    			$result["text"] .= '=';
    		}
    		else { $_32 = FALSE; break; }
    		$stack[] = $result; $result = $this->construct( $matchrule, "q" ); 
    		if (( $subres = $this->rx( '/["\']/' ) ) !== FALSE) {
    			$result["text"] .= $subres;
    			$subres = $result; $result = array_pop($stack);
    			$this->store( $result, $subres, 'q' );
    		}
    		else {
    			$result = array_pop($stack);
    			$_32 = FALSE; break;
    		}
    		$stack[] = $result; $result = $this->construct( $matchrule, "value" ); 
    		if (( $subres = $this->rx( '/[^'.$this->expression($result, $stack, 'q').']+/' ) ) !== FALSE) {
    			$result["text"] .= $subres;
    			$subres = $result; $result = array_pop($stack);
    			$this->store( $result, $subres, 'value' );
    		}
    		else {
    			$result = array_pop($stack);
    			$_32 = FALSE; break;
    		}
    		if (( $subres = $this->literal( ''.$this->expression($result, $stack, 'q').'' ) ) !== FALSE) { $result["text"] .= $subres; }
    		else { $_32 = FALSE; break; }
    		$_32 = TRUE; break;
    	}
    	while(0);
    	if( $_32 === TRUE ) { return $this->finalise($result); }
    	if( $_32 === FALSE) { return FALSE; }
    }


    /* ioblock: '[[' ws? channel:blockid ws? ':' ws? var:blockid ws? ']]' */
    protected $match_ioblock_typestack = array('ioblock');
    function match_ioblock ($stack = array()) {
    	$matchrule = "ioblock"; $result = $this->construct($matchrule, $matchrule, null);
    	$_43 = NULL;
    	do {
    		if (( $subres = $this->literal( '[[' ) ) !== FALSE) { $result["text"] .= $subres; }
    		else { $_43 = FALSE; break; }
    		$res_35 = $result;
    		$pos_35 = $this->pos;
    		$matcher = 'match_'.'ws'; $key = $matcher; $pos = $this->pos;
    		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    		if ($subres !== FALSE) {
    			$this->store( $result, $subres );
    		}
    		else {
    			$result = $res_35;
    			$this->pos = $pos_35;
    			unset( $res_35 );
    			unset( $pos_35 );
    		}
    		$matcher = 'match_'.'blockid'; $key = $matcher; $pos = $this->pos;
    		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    		if ($subres !== FALSE) {
    			$this->store( $result, $subres, "channel" );
    		}
    		else { $_43 = FALSE; break; }
    		$res_37 = $result;
    		$pos_37 = $this->pos;
    		$matcher = 'match_'.'ws'; $key = $matcher; $pos = $this->pos;
    		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    		if ($subres !== FALSE) {
    			$this->store( $result, $subres );
    		}
    		else {
    			$result = $res_37;
    			$this->pos = $pos_37;
    			unset( $res_37 );
    			unset( $pos_37 );
    		}
    		if (substr($this->string,$this->pos,1) == ':') {
    			$this->pos += 1;
    			$result["text"] .= ':';
    		}
    		else { $_43 = FALSE; break; }
    		$res_39 = $result;
    		$pos_39 = $this->pos;
    		$matcher = 'match_'.'ws'; $key = $matcher; $pos = $this->pos;
    		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    		if ($subres !== FALSE) {
    			$this->store( $result, $subres );
    		}
    		else {
    			$result = $res_39;
    			$this->pos = $pos_39;
    			unset( $res_39 );
    			unset( $pos_39 );
    		}
    		$matcher = 'match_'.'blockid'; $key = $matcher; $pos = $this->pos;
    		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    		if ($subres !== FALSE) {
    			$this->store( $result, $subres, "var" );
    		}
    		else { $_43 = FALSE; break; }
    		$res_41 = $result;
    		$pos_41 = $this->pos;
    		$matcher = 'match_'.'ws'; $key = $matcher; $pos = $this->pos;
    		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    		if ($subres !== FALSE) {
    			$this->store( $result, $subres );
    		}
    		else {
    			$result = $res_41;
    			$this->pos = $pos_41;
    			unset( $res_41 );
    			unset( $pos_41 );
    		}
    		if (( $subres = $this->literal( ']]' ) ) !== FALSE) { $result["text"] .= $subres; }
    		else { $_43 = FALSE; break; }
    		$_43 = TRUE; break;
    	}
    	while(0);
    	if( $_43 === TRUE ) { return $this->finalise($result); }
    	if( $_43 === FALSE) { return FALSE; }
    }


    /* blockempty: '[[' ws? name:blockid (params:param)* ws? '/]]' */
    protected $match_blockempty_typestack = array('blockempty');
    function match_blockempty ($stack = array()) {
    	$matchrule = "blockempty"; $result = $this->construct($matchrule, $matchrule, null);
    	$_53 = NULL;
    	do {
    		if (( $subres = $this->literal( '[[' ) ) !== FALSE) { $result["text"] .= $subres; }
    		else { $_53 = FALSE; break; }
    		$res_46 = $result;
    		$pos_46 = $this->pos;
    		$matcher = 'match_'.'ws'; $key = $matcher; $pos = $this->pos;
    		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    		if ($subres !== FALSE) {
    			$this->store( $result, $subres );
    		}
    		else {
    			$result = $res_46;
    			$this->pos = $pos_46;
    			unset( $res_46 );
    			unset( $pos_46 );
    		}
    		$matcher = 'match_'.'blockid'; $key = $matcher; $pos = $this->pos;
    		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    		if ($subres !== FALSE) {
    			$this->store( $result, $subres, "name" );
    		}
    		else { $_53 = FALSE; break; }
    		while (true) {
    			$res_50 = $result;
    			$pos_50 = $this->pos;
    			$_49 = NULL;
    			do {
    				$matcher = 'match_'.'param'; $key = $matcher; $pos = $this->pos;
    				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    				if ($subres !== FALSE) {
    					$this->store( $result, $subres, "params" );
    				}
    				else { $_49 = FALSE; break; }
    				$_49 = TRUE; break;
    			}
    			while(0);
    			if( $_49 === FALSE) {
    				$result = $res_50;
    				$this->pos = $pos_50;
    				unset( $res_50 );
    				unset( $pos_50 );
    				break;
    			}
    		}
    		$res_51 = $result;
    		$pos_51 = $this->pos;
    		$matcher = 'match_'.'ws'; $key = $matcher; $pos = $this->pos;
    		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    		if ($subres !== FALSE) {
    			$this->store( $result, $subres );
    		}
    		else {
    			$result = $res_51;
    			$this->pos = $pos_51;
    			unset( $res_51 );
    			unset( $pos_51 );
    		}
    		if (( $subres = $this->literal( '/]]' ) ) !== FALSE) { $result["text"] .= $subres; }
    		else { $_53 = FALSE; break; }
    		$_53 = TRUE; break;
    	}
    	while(0);
    	if( $_53 === TRUE ) { return $this->finalise($result); }
    	if( $_53 === FALSE) { return FALSE; }
    }


    /* blockopen: '[[' ws? name:blockid (params:param)* ws? ']]' */
    protected $match_blockopen_typestack = array('blockopen');
    function match_blockopen ($stack = array()) {
    	$matchrule = "blockopen"; $result = $this->construct($matchrule, $matchrule, null);
    	$_63 = NULL;
    	do {
    		if (( $subres = $this->literal( '[[' ) ) !== FALSE) { $result["text"] .= $subres; }
    		else { $_63 = FALSE; break; }
    		$res_56 = $result;
    		$pos_56 = $this->pos;
    		$matcher = 'match_'.'ws'; $key = $matcher; $pos = $this->pos;
    		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    		if ($subres !== FALSE) {
    			$this->store( $result, $subres );
    		}
    		else {
    			$result = $res_56;
    			$this->pos = $pos_56;
    			unset( $res_56 );
    			unset( $pos_56 );
    		}
    		$matcher = 'match_'.'blockid'; $key = $matcher; $pos = $this->pos;
    		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    		if ($subres !== FALSE) {
    			$this->store( $result, $subres, "name" );
    		}
    		else { $_63 = FALSE; break; }
    		while (true) {
    			$res_60 = $result;
    			$pos_60 = $this->pos;
    			$_59 = NULL;
    			do {
    				$matcher = 'match_'.'param'; $key = $matcher; $pos = $this->pos;
    				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    				if ($subres !== FALSE) {
    					$this->store( $result, $subres, "params" );
    				}
    				else { $_59 = FALSE; break; }
    				$_59 = TRUE; break;
    			}
    			while(0);
    			if( $_59 === FALSE) {
    				$result = $res_60;
    				$this->pos = $pos_60;
    				unset( $res_60 );
    				unset( $pos_60 );
    				break;
    			}
    		}
    		$res_61 = $result;
    		$pos_61 = $this->pos;
    		$matcher = 'match_'.'ws'; $key = $matcher; $pos = $this->pos;
    		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    		if ($subres !== FALSE) {
    			$this->store( $result, $subres );
    		}
    		else {
    			$result = $res_61;
    			$this->pos = $pos_61;
    			unset( $res_61 );
    			unset( $pos_61 );
    		}
    		if (( $subres = $this->literal( ']]' ) ) !== FALSE) { $result["text"] .= $subres; }
    		else { $_63 = FALSE; break; }
    		$_63 = TRUE; break;
    	}
    	while(0);
    	if( $_63 === TRUE ) { return $this->finalise($result); }
    	if( $_63 === FALSE) { return FALSE; }
    }


    /* blockclose: '[[/' ws? name:blockid ws? ']]' */
    protected $match_blockclose_typestack = array('blockclose');
    function match_blockclose ($stack = array()) {
    	$matchrule = "blockclose"; $result = $this->construct($matchrule, $matchrule, null);
    	$_70 = NULL;
    	do {
    		if (( $subres = $this->literal( '[[/' ) ) !== FALSE) { $result["text"] .= $subres; }
    		else { $_70 = FALSE; break; }
    		$res_66 = $result;
    		$pos_66 = $this->pos;
    		$matcher = 'match_'.'ws'; $key = $matcher; $pos = $this->pos;
    		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    		if ($subres !== FALSE) {
    			$this->store( $result, $subres );
    		}
    		else {
    			$result = $res_66;
    			$this->pos = $pos_66;
    			unset( $res_66 );
    			unset( $pos_66 );
    		}
    		$matcher = 'match_'.'blockid'; $key = $matcher; $pos = $this->pos;
    		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    		if ($subres !== FALSE) {
    			$this->store( $result, $subres, "name" );
    		}
    		else { $_70 = FALSE; break; }
    		$res_68 = $result;
    		$pos_68 = $this->pos;
    		$matcher = 'match_'.'ws'; $key = $matcher; $pos = $this->pos;
    		$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    		if ($subres !== FALSE) {
    			$this->store( $result, $subres );
    		}
    		else {
    			$result = $res_68;
    			$this->pos = $pos_68;
    			unset( $res_68 );
    			unset( $pos_68 );
    		}
    		if (( $subres = $this->literal( ']]' ) ) !== FALSE) { $result["text"] .= $subres; }
    		else { $_70 = FALSE; break; }
    		$_70 = TRUE; break;
    	}
    	while(0);
    	if( $_70 === TRUE ) { return $this->finalise($result); }
    	if( $_70 === FALSE) { return FALSE; }
    }


    /* castext: ( item:ioblock | item:texcasblock | item:rawcasblock | item:misc | item:ws | item:blockclose | item:blockopen | item:blockempty | item:breaks)* */
    protected $match_castext_typestack = array('castext');
    function match_castext ($stack = array()) {
    	$matchrule = "castext"; $result = $this->construct($matchrule, $matchrule, null);
    	while (true) {
    		$res_106 = $result;
    		$pos_106 = $this->pos;
    		$_105 = NULL;
    		do {
    			$_103 = NULL;
    			do {
    				$res_72 = $result;
    				$pos_72 = $this->pos;
    				$matcher = 'match_'.'ioblock'; $key = $matcher; $pos = $this->pos;
    				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    				if ($subres !== FALSE) {
    					$this->store( $result, $subres, "item" );
    					$_103 = TRUE; break;
    				}
    				$result = $res_72;
    				$this->pos = $pos_72;
    				$_101 = NULL;
    				do {
    					$res_74 = $result;
    					$pos_74 = $this->pos;
    					$matcher = 'match_'.'texcasblock'; $key = $matcher; $pos = $this->pos;
    					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    					if ($subres !== FALSE) {
    						$this->store( $result, $subres, "item" );
    						$_101 = TRUE; break;
    					}
    					$result = $res_74;
    					$this->pos = $pos_74;
    					$_99 = NULL;
    					do {
    						$res_76 = $result;
    						$pos_76 = $this->pos;
    						$matcher = 'match_'.'rawcasblock'; $key = $matcher; $pos = $this->pos;
    						$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    						if ($subres !== FALSE) {
    							$this->store( $result, $subres, "item" );
    							$_99 = TRUE; break;
    						}
    						$result = $res_76;
    						$this->pos = $pos_76;
    						$_97 = NULL;
    						do {
    							$res_78 = $result;
    							$pos_78 = $this->pos;
    							$matcher = 'match_'.'misc'; $key = $matcher; $pos = $this->pos;
    							$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    							if ($subres !== FALSE) {
    								$this->store( $result, $subres, "item" );
    								$_97 = TRUE; break;
    							}
    							$result = $res_78;
    							$this->pos = $pos_78;
    							$_95 = NULL;
    							do {
    								$res_80 = $result;
    								$pos_80 = $this->pos;
    								$matcher = 'match_'.'ws'; $key = $matcher; $pos = $this->pos;
    								$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    								if ($subres !== FALSE) {
    									$this->store( $result, $subres, "item" );
    									$_95 = TRUE; break;
    								}
    								$result = $res_80;
    								$this->pos = $pos_80;
    								$_93 = NULL;
    								do {
    									$res_82 = $result;
    									$pos_82 = $this->pos;
    									$matcher = 'match_'.'blockclose'; $key = $matcher; $pos = $this->pos;
    									$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    									if ($subres !== FALSE) {
    										$this->store( $result, $subres, "item" );
    										$_93 = TRUE; break;
    									}
    									$result = $res_82;
    									$this->pos = $pos_82;
    									$_91 = NULL;
    									do {
    										$res_84 = $result;
    										$pos_84 = $this->pos;
    										$matcher = 'match_'.'blockopen'; $key = $matcher; $pos = $this->pos;
    										$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    										if ($subres !== FALSE) {
    											$this->store( $result, $subres, "item" );
    											$_91 = TRUE; break;
    										}
    										$result = $res_84;
    										$this->pos = $pos_84;
    										$_89 = NULL;
    										do {
    											$res_86 = $result;
    											$pos_86 = $this->pos;
    											$matcher = 'match_'.'blockempty'; $key = $matcher; $pos = $this->pos;
    											$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    											if ($subres !== FALSE) {
    												$this->store( $result, $subres, "item" );
    												$_89 = TRUE; break;
    											}
    											$result = $res_86;
    											$this->pos = $pos_86;
    											$matcher = 'match_'.'breaks'; $key = $matcher; $pos = $this->pos;
    											$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
    											if ($subres !== FALSE) {
    												$this->store( $result, $subres, "item" );
    												$_89 = TRUE; break;
    											}
    											$result = $res_86;
    											$this->pos = $pos_86;
    											$_89 = FALSE; break;
    										}
    										while(0);
    										if( $_89 === TRUE ) { $_91 = TRUE; break; }
    										$result = $res_84;
    										$this->pos = $pos_84;
    										$_91 = FALSE; break;
    									}
    									while(0);
    									if( $_91 === TRUE ) { $_93 = TRUE; break; }
    									$result = $res_82;
    									$this->pos = $pos_82;
    									$_93 = FALSE; break;
    								}
    								while(0);
    								if( $_93 === TRUE ) { $_95 = TRUE; break; }
    								$result = $res_80;
    								$this->pos = $pos_80;
    								$_95 = FALSE; break;
    							}
    							while(0);
    							if( $_95 === TRUE ) { $_97 = TRUE; break; }
    							$result = $res_78;
    							$this->pos = $pos_78;
    							$_97 = FALSE; break;
    						}
    						while(0);
    						if( $_97 === TRUE ) { $_99 = TRUE; break; }
    						$result = $res_76;
    						$this->pos = $pos_76;
    						$_99 = FALSE; break;
    					}
    					while(0);
    					if( $_99 === TRUE ) { $_101 = TRUE; break; }
    					$result = $res_74;
    					$this->pos = $pos_74;
    					$_101 = FALSE; break;
    				}
    				while(0);
    				if( $_101 === TRUE ) { $_103 = TRUE; break; }
    				$result = $res_72;
    				$this->pos = $pos_72;
    				$_103 = FALSE; break;
    			}
    			while(0);
    			if( $_103 === FALSE) { $_105 = FALSE; break; }
    			$_105 = TRUE; break;
    		}
    		while(0);
    		if( $_105 === FALSE) {
    			$result = $res_106;
    			$this->pos = $pos_106;
    			unset( $res_106 );
    			unset( $pos_106 );
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
    
    public $parent = NULL;
    public $next_sibling = NULL;
    public $previous_sibling = NULL;
    public $first_child = NULL;
    // There are five types, castext is the root, blocks are containers and text, rawcasblock and texcasblock are root nodes.
    public $type = "castext";
    private $params = NULL;
    private $content = "";

    /**
     * Converts the nested array form tree to parsetreenode-tree
     */
    public static function build_from_nested($parse_tree,$parent=NULL) {
        $node = new stack_cas_castext_parsetreenode();
        $node->parent = $parent;

        switch ($parse_tree['_matchrule']) {
            case "block":
                $node->params = $parse_tree['params'];
                $node->content = $parse_tree['name'];
            case "castext":
                if (array_key_exists('_matchrule',$parse_tree['item'])) {
                    $node->first_child = stack_cas_castext_parsetreenode::build_from_nested($parse_tree['item'],$node);
                } else {
                    $prev = NULL;
                    foreach ($parse_tree['item'] as $sub_tree) {
                        $n = stack_cas_castext_parsetreenode::build_from_nested($sub_tree,$node); 
                        if ($prev !== NULL) {
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
        while ($this->type == 'text' && $this->next_sibling !== NULL && $this->next_sibling->type == 'text') {
            $extra = $this->next_sibling;
            $this->content .= $extra->content;
            $this->next_sibling = $extra->next_sibling;
            if ($this->next_sibling !== NULL) {
                $this->next_sibling->previous_sibling = $this;
            }
        }
        if ($this->next_sibling !== NULL) {
            $this->next_sibling->normalize();
        }
        if ($this->is_container() && $this->first_child !== NULL) {
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
        $this->first_child = NULL;
        $this->params = array();
    }

    /**
     * Gets the name of this block, the content of this text-node or the cascontent of this casblock
     */
    public function get_content() {
        return $this->content;
    }

    /**
     * Returns the value of a parameter, usefull for nodes of the block-type. You can also set the default value returned should such a parameter be missing.
     */
    public function get_parameter($key,$default=NULL) {
        if (@array_key_exists($key,$this->params)) {
            return $this->params[$key];
        }
        return $default;
    }

    /**
     * Use this if you care if a parameter actually exists.
     */
    public function parameter_exists($key) {
        if ($this->params !== NULL) {
            return array_key_exists($key,$this->params);
        }
        return false;
    }

    /**
     * Returns an array containing all the parameters.
     */
    public function get_parameters() {
        if ($this->params === NULL) {
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
        if ($this->next_sibling !== NULL) {
            $this->next_sibling->previous_sibling = $this->previous_sibling;
        }
        if ($this->previous_sibling !== NULL) {
            $this->previous_sibling->next_sibling = $this->next_sibling;
        }
    }

    /**
     * Destroys this node but promotes its children to its place. Perfect for removing if-blocks and other wrappers.
     */
    public function destroy_node_promote_children() {
        if ($this->first_child !== NULL) {
            $next = $this->next_sibling;
            $iter = $this->first_child;
            if ($this->parent->first_child === $this) {
                $this->parent->first_child = $iter;
            }
            if ($this->previous_sibling !== NULL) {
                $this->previous_sibling->next_sibling = $iter;
            }
            $iter->previous_sibling = $this->previous_sibling;
            $iter->parent = $this->parent;
            while ($iter->next_sibling !== NULL) {
                $iter->parent = $this->parent;
                $iter = $iter->next_sibling;
            }
            $iter->parent = $this->parent;
            $iter->next_sibling = $next;
            if ($next !== NULL) {
                $next->previous_sibling = $iter;
            }
        } else {
            if ($this->next_sibling !== NULL && $this->previous_sibling !== NULL) {
                $this->previous_sibling->next_sibling = $this->next_sibling;
                $this->next_sibling->previous_sibling = $this->previous_sibling;
            } else if ($this->previous_sibling !== NULL) {
                $this->previous_sibling->next_sibling = NULL;
            } else {
                $this->parent->first_child = NULL;
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
                        if (strpos($value,'"') === FALSE) {
                            $r .= '"' . $value . '"';
                        } else {
                            $r .= "'$value'";
                        }
                    }
                }
                $r .= " ]]";
             
                $iterator = $this->first_child;
                while ($iterator !== NULL) {
                    $r .= $iterator->to_string();
                    $iterator = $iterator->next_sibling;
                }

                $r .= "[[/ " . $this->content . " ]]";
                break;
            case "castext":
                $iterator = $this->first_child;
                while ($iterator !== NULL) {
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

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
 ** php ../../../lib/php-peg/cli.php castext.peg.inc > castextparser.class.php
 ** And do remove that PHP ending the question mark greater than thing after generation.
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
                    $parse_tree['item'][$now]['_matchrule'] == 'mathmodeclose' ) {
                    if ($parse_tree['item'][$next]['_matchrule'] == 'ioblock' ||
                        $parse_tree['item'][$next]['_matchrule'] == 'ws' ||
                        $parse_tree['item'][$next]['_matchrule'] == 'misc' ||
                        $parse_tree['item'][$next]['_matchrule'] == 'breaks' ||
                        $parse_tree['item'][$next]['_matchrule'] == 'mathmodeopen' ||
                        $parse_tree['item'][$next]['_matchrule'] == 'mathmodeclose') {
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
                        $parse_tree['item'][$next]['_matchrule'] == 'mathmodeclose' ) {
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

                    if ($closest_start_candidate !== null && $parse_tree['item'][$end_candidate_index]['name'][1]['text']
                            == $parse_tree['item'][$closest_start_candidate]['name'][1]['text']) {
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
        $_3 = null;
        do {
            if (( $subres = $this->literal( '{@' ) ) !== false) {
                $result["text"] .= $subres;
            } else {
                $_3 = false;
                break;
            }
            $stack[] = $result; $result = $this->construct( $matchrule, "cascontent" );
            if (( $subres = $this->rx( '/[^@]+/' ) ) !== false) {
                $result["text"] .= $subres;
                $subres = $result; $result = array_pop($stack);
                $this->store( $result, $subres, 'cascontent' );
            } else {
                $result = array_pop($stack);
                $_3 = false;
                break;
            }
            if (( $subres = $this->literal( '@}' ) ) !== false) {
                $result["text"] .= $subres;
            } else {
                $_3 = false;
                break;
            }
            $_3 = true;
            break;
        } while (0);
        if ( $_3 === true ) {
            return $this->finalise($result);
        }
        if ( $_3 === false) {
            return false;
        }
    }


    /* rawcasblock: "{#" cascontent:/[^#]+/ "#}" */
    protected $match_rawcasblock_typestack = array('rawcasblock');
    function match_rawcasblock ($stack = array()) {
        $matchrule = "rawcasblock";
        $result = $this->construct($matchrule, $matchrule, null);
        $_8 = null;
        do {
            if (( $subres = $this->literal( '{#' ) ) !== false) {
                $result["text"] .= $subres;
            } else {
                $_8 = false;
                break;
            }
            $stack[] = $result; $result = $this->construct( $matchrule, "cascontent" );
            if (( $subres = $this->rx( '/[^#]+/' ) ) !== false) {
                $result["text"] .= $subres;
                $subres = $result; $result = array_pop($stack);
                $this->store( $result, $subres, 'cascontent' );
            } else {
                $result = array_pop($stack);
                $_8 = false;
                break;
            }
            if (( $subres = $this->literal( '#}' ) ) !== false) {
                $result["text"] .= $subres;
            } else {
                $_8 = false;
                break;
            }
            $_8 = true;
            break;
        } while (0);
        if ( $_8 === true ) {
            return $this->finalise($result);
        }
        if ( $_8 === false) {
            return false;
        }
    }


    /* mathmodeopen: ( '\(' | '\[' ) */
    protected $match_mathmodeopen_typestack = array('mathmodeopen');
    function match_mathmodeopen ($stack = array()) {
        $matchrule = "mathmodeopen";
        $result = $this->construct($matchrule, $matchrule, null);
        $_15 = null;
        do {
            $_13 = null;
            do {
                $res_10 = $result;
                $pos_10 = $this->pos;
                if (( $subres = $this->literal( '\(' ) ) !== false) {
                    $result["text"] .= $subres;
                    $_13 = true;
                    break;
                }
                $result = $res_10;
                $this->pos = $pos_10;
                if (( $subres = $this->literal( '\[' ) ) !== false) {
                    $result["text"] .= $subres;
                    $_13 = true; break;
                }
                $result = $res_10;
                $this->pos = $pos_10;
                $_13 = false;
                break;
            } while (0);
            if ( $_13 === false) {
                $_15 = false;
                break;
            }
            $_15 = true;
            break;
        } while (0);
        if ( $_15 === true ) {
            return $this->finalise($result);
        }
        if ( $_15 === false) {
            return false;
        }
    }


    /* mathmodeclose: ( '\)' | '\]' ) */
    protected $match_mathmodeclose_typestack = array('mathmodeclose');
    function match_mathmodeclose ($stack = array()) {
        $matchrule = "mathmodeclose";
        $result = $this->construct($matchrule, $matchrule, null);
        $_22 = null;
        do {
            $_20 = null;
            do {
                $res_17 = $result;
                $pos_17 = $this->pos;
                if (( $subres = $this->literal( '\)' ) ) !== false) {
                    $result["text"] .= $subres;
                    $_20 = true; break;
                }
                $result = $res_17;
                $this->pos = $pos_17;
                if (( $subres = $this->literal( '\]' ) ) !== false) {
                    $result["text"] .= $subres;
                    $_20 = true;
                    break;
                }
                $result = $res_17;
                $this->pos = $pos_17;
                $_20 = false;
                break;
            } while (0);
            if ( $_20 === false) {
                $_22 = false;
                break;
            }
            $_22 = true;
            break;
        } while (0);
        if ( $_22 === true ) {
            return $this->finalise($result);
        }
        if ( $_22 === false) {
            return false;
        }
    }


    /* blockid: /[a-zA-Z0-9\-_]+/  */
    protected $match_blockid_typestack = array('blockid');
    function match_blockid ($stack = array()) {
        $matchrule = "blockid"; $result = $this->construct($matchrule, $matchrule, null);
        if (( $subres = $this->rx( '/[a-zA-Z0-9\-_]+/' ) ) !== false) {
            $result["text"] .= $subres;
            return $this->finalise($result);
        } else {
            return false;
        }
    }

    /* ws: (' ' | /[\n\t\r]/ )+ */
    protected $match_ws_typestack = array('ws');
    function match_ws ($stack = array()) {
        $matchrule = "ws";
        $result = $this->construct($matchrule, $matchrule, null);
        $count = 0;
        while (true) {
            $res_31 = $result;
            $pos_31 = $this->pos;
            $_30 = null;
            do {
                $_28 = null;
                do {
                    $res_25 = $result;
                    $pos_25 = $this->pos;
                    if (substr($this->string, $this->pos, 1) == ' ') {
                        $this->pos += 1;
                        $result["text"] .= ' ';
                        $_28 = true;
                        break;
                    }
                    $result = $res_25;
                    $this->pos = $pos_25;
                    if (( $subres = $this->rx( '/[\n\t\r]/' ) ) !== false) {
                        $result["text"] .= $subres;
                        $_28 = true;
                        break;
                    }
                    $result = $res_25;
                    $this->pos = $pos_25;
                    $_28 = false;
                    break;
                } while (0);
                if ($_28 === false) {
                    $_30 = false;
                    break;
                }
                $_30 = true;
                break;
            } while (0);
            if ($_30 === false) {
                $result = $res_31;
                $this->pos = $pos_31;
                unset( $res_31 );
                unset( $pos_31 );
                break;
            }
            $count++;
        }
        if ($count >= 1) {
            return $this->finalise($result);
        } else {
            return false;
        }
    }


    /* misc:  /[^\{\[\\]+/  */
    protected $match_misc_typestack = array('misc');
    function match_misc ($stack = array()) {
        $matchrule = "misc"; $result = $this->construct($matchrule, $matchrule, null);
        if (( $subres = $this->rx( '/[^\{\[\\\\]+/' ) ) !== false) {
            $result["text"] .= $subres;
            return $this->finalise($result);
        } else {
            return false;
        }
    }


    /* breaks:  ( '{' | '[' | '\\' ) */
    protected $match_breaks_typestack = array('breaks');
    function match_breaks ($stack = array()) {
        $matchrule = "breaks"; $result = $this->construct($matchrule, $matchrule, null);
        $_42 = null;
        do {
            $_40 = null;
            do {
                $res_33 = $result;
                $pos_33 = $this->pos;
                if (substr($this->string, $this->pos, 1) == '{') {
                    $this->pos += 1;
                    $result["text"] .= '{';
                    $_40 = true;
                    break;
                }
                $result = $res_33;
                $this->pos = $pos_33;
                $_38 = null;
                do {
                    $res_35 = $result;
                    $pos_35 = $this->pos;
                    if (substr($this->string, $this->pos, 1) == '[') {
                        $this->pos += 1;
                        $result["text"] .= '[';
                        $_38 = true;
                        break;
                    }
                    $result = $res_35;
                    $this->pos = $pos_35;
                    if (substr($this->string, $this->pos, 1) == '\\') {
                        $this->pos += 1;
                        $result["text"] .= '\\';
                        $_38 = true;
                        break;
                    }
                    $result = $res_35;
                    $this->pos = $pos_35;
                    $_38 = false;
                    break;
                } while (0);
                if ( $_38 === true ) {
                    $_40 = true;
                    break;
                }
                $result = $res_33;
                $this->pos = $pos_33;
                $_40 = false;
                break;
            } while (0);
            if ( $_40 === false) {
                $_42 = false;
                break;
            }
            $_42 = true;
            break;
        } while (0);
        if ( $_42 === true ) {
            return $this->finalise($result);
        }
        if ( $_42 === false) {
            return false;
        }
    }


    /* param: ws key:blockid '=' q:/["']/ value:/[^$q]+/ "$q"  */
    protected $match_param_typestack = array('param');
    function match_param ($stack = array()) {
        $matchrule = "param"; $result = $this->construct($matchrule, $matchrule, null);
        $_50 = null;
        do {
            $matcher = 'match_'.'ws'; $key = $matcher; $pos = $this->pos;
            $subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
            if ($subres !== false) {
                $this->store( $result, $subres );
            } else {
                $_50 = false;
                break;
            }
            $matcher = 'match_'.'blockid';
            $key = $matcher; $pos = $this->pos;
            $subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
            if ($subres !== false) {
                $this->store( $result, $subres, "key" );
            } else {
                $_50 = false;
                break;
            }
            if (substr($this->string, $this->pos, 1) == '=') {
                $this->pos += 1;
                $result["text"] .= '=';
            } else {
                $_50 = false;
                break;
            }
            $stack[] = $result; $result = $this->construct( $matchrule, "q" );
            if (( $subres = $this->rx( '/["\']/' ) ) !== false) {
                $result["text"] .= $subres;
                $subres = $result; $result = array_pop($stack);
                $this->store( $result, $subres, 'q' );
            } else {
                $result = array_pop($stack);
                $_50 = false;
                break;
            }
            $stack[] = $result;
            $result = $this->construct( $matchrule, "value" );
            if (( $subres = $this->rx( '/[^'.$this->expression($result, $stack, 'q').']+/' ) ) !== false) {
                $result["text"] .= $subres;
                $subres = $result;
                $result = array_pop($stack);
                $this->store( $result, $subres, 'value' );
            } else {
                $result = array_pop($stack);
                $_50 = false;
                break;
            }
            if (( $subres = $this->literal( ''.$this->expression($result, $stack, 'q').'' ) ) !== false) {
                $result["text"] .= $subres;
            } else {
                $_50 = false;
                break;
            }
            $_50 = true;
            break;
        } while (0);
        if ( $_50 === true ) {
            return $this->finalise($result);
        }
        if ( $_50 === false) {
            return false;
        }
    }


    /* ioblock: '[[' ws? channel:blockid ws? ':' ws? var:blockid ws? ']]' */
    protected $match_ioblock_typestack = array('ioblock');
    function match_ioblock ($stack = array()) {
        $matchrule = "ioblock";
        $result = $this->construct($matchrule, $matchrule, null);
        $_61 = null;
        do {
            if (( $subres = $this->literal( '[[' ) ) !== false) {
                $result["text"] .= $subres;
            } else {
                $_61 = false;
                break;
            }
            $res_53 = $result;
            $pos_53 = $this->pos;
            $matcher = 'match_'.'ws'; $key = $matcher; $pos = $this->pos;
            $subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
            if ($subres !== false) {
                $this->store( $result, $subres );
            } else {
                $result = $res_53;
                $this->pos = $pos_53;
                unset( $res_53 );
                unset( $pos_53 );
            }
            $matcher = 'match_'.'blockid'; $key = $matcher;
            $pos = $this->pos;
            $subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
            if ($subres !== false) {
                $this->store( $result, $subres, "channel" );
            } else {
                $_61 = false;
                break;
            }
            $res_55 = $result;
            $pos_55 = $this->pos;
            $matcher = 'match_'.'ws';
            $key = $matcher; $pos = $this->pos;
            $subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
            if ($subres !== false) {
                $this->store( $result, $subres );
            } else {
                $result = $res_55;
                $this->pos = $pos_55;
                unset( $res_55 );
                unset( $pos_55 );
            }
            if (substr($this->string, $this->pos, 1) == ':') {
                $this->pos += 1;
                $result["text"] .= ':';
            } else {
                $_61 = false;
                break;
            }
            $res_57 = $result;
            $pos_57 = $this->pos;
            $matcher = 'match_'.'ws';
            $key = $matcher;
            $pos = $this->pos;
            $subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
            if ($subres !== false) {
                $this->store( $result, $subres );
            } else {
                $result = $res_57;
                $this->pos = $pos_57;
                unset( $res_57 );
                unset( $pos_57 );
            }
            $matcher = 'match_'.'blockid';
            $key = $matcher;
            $pos = $this->pos;
            $subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
            if ($subres !== false) {
                $this->store( $result, $subres, "var" );
            } else {
                $_61 = false;
                break;
            }
            $res_59 = $result;
            $pos_59 = $this->pos;
            $matcher = 'match_'.'ws';
            $key = $matcher;
            $pos = $this->pos;
            $subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
            if ($subres !== false) {
                $this->store( $result, $subres );
            } else {
                $result = $res_59;
                $this->pos = $pos_59;
                unset( $res_59 );
                unset( $pos_59 );
            }
            if (( $subres = $this->literal( ']]' ) ) !== false) {
                $result["text"] .= $subres;
            } else {
                $_61 = false;
                break;
            }
            $_61 = true;
            break;
        } while (0);
        if ( $_61 === true ) {
            return $this->finalise($result);
        }
        if ( $_61 === false) {
            return false;
        }
    }


    /* blockempty: '[[' ws? name:blockid (params:param)* ws? '/]]' */
    protected $match_blockempty_typestack = array('blockempty');
    function match_blockempty ($stack = array()) {
        $matchrule = "blockempty";
        $result = $this->construct($matchrule, $matchrule, null);
        $_71 = null;
        do {
            if (( $subres = $this->literal( '[[' ) ) !== false) {
                $result["text"] .= $subres;
            } else {
                $_71 = false;
                break;
            }
            $res_64 = $result;
            $pos_64 = $this->pos;
            $matcher = 'match_'.'ws';
            $key = $matcher;
            $pos = $this->pos;
            $subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
            if ($subres !== false) {
                $this->store( $result, $subres );
            } else {
                $result = $res_64;
                $this->pos = $pos_64;
                unset( $res_64 );
                unset( $pos_64 );
            }
            $matcher = 'match_'.'blockid'; $key = $matcher; $pos = $this->pos;
            $subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
            if ($subres !== false) {
                $this->store( $result, $subres, "name" );
            } else {
                $_71 = false;
                break;
            } while (true) {
                $res_68 = $result;
                $pos_68 = $this->pos;
                $_67 = null;
                do {
                    $matcher = 'match_'.'param'; $key = $matcher; $pos = $this->pos;
                    $subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
                    if ($subres !== false) {
                        $this->store( $result, $subres, "params" );
                    } else {
                        $_67 = false;
                        break;
                    }
                    $_67 = true;
                    break;
                } while (0);
                if ( $_67 === false) {
                    $result = $res_68;
                    $this->pos = $pos_68;
                    unset( $res_68 );
                    unset( $pos_68 );
                    break;
                }
            }
            $res_69 = $result;
            $pos_69 = $this->pos;
            $matcher = 'match_'.'ws';
            $key = $matcher;
            $pos = $this->pos;
            $subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
            if ($subres !== false) {
                $this->store( $result, $subres );
            } else {
                $result = $res_69;
                $this->pos = $pos_69;
                unset( $res_69 );
                unset( $pos_69 );
            }
            if (( $subres = $this->literal( '/]]' ) ) !== false) {
                $result["text"] .= $subres;
            } else {
                $_71 = false;
                break;
            }
            $_71 = true;
            break;
        } while (0);
        if ( $_71 === true ) {
            return $this->finalise($result);
        }
        if ( $_71 === false) {
            return false;
        }
    }


    /* blockopen: '[[' ws? name:blockid (params:param)* ws? ']]' */
    protected $match_blockopen_typestack = array('blockopen');
    function match_blockopen ($stack = array()) {
        $matchrule = "blockopen"; $result = $this->construct($matchrule, $matchrule, null);
        $_81 = null;
        do {
            if (( $subres = $this->literal( '[[' ) ) !== false) {
                $result["text"] .= $subres;
            } else {
                $_81 = false;
                break;
            }
            $res_74 = $result;
            $pos_74 = $this->pos;
            $matcher = 'match_'.'ws';
            $key = $matcher;
            $pos = $this->pos;
            $subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
            if ($subres !== false) {
                $this->store( $result, $subres );
            } else {
                $result = $res_74;
                $this->pos = $pos_74;
                unset( $res_74 );
                unset( $pos_74 );
            }
            $matcher = 'match_'.'blockid';
            $key = $matcher;
            $pos = $this->pos;
            $subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
            if ($subres !== false) {
                $this->store( $result, $subres, "name" );
            } else {
                $_81 = false;
                break;
            }
            while (true) {
                $res_78 = $result;
                $pos_78 = $this->pos;
                $_77 = null;
                do {
                    $matcher = 'match_'.'param'; $key = $matcher; $pos = $this->pos;
                    $subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
                    if ($subres !== false) {
                        $this->store($result, $subres, "params");
                    } else {
                        $_77 = false;
                        break;
                    }
                    $_77 = true;
                    break;
                } while (0);
                if ( $_77 === false) {
                    $result = $res_78;
                    $this->pos = $pos_78;
                    unset( $res_78 );
                    unset( $pos_78 );
                    break;
                }
            }
            $res_79 = $result;
            $pos_79 = $this->pos;
            $matcher = 'match_'.'ws';
            $key = $matcher;
            $pos = $this->pos;
            $subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
            if ($subres !== false) {
                $this->store( $result, $subres );
            } else {
                $result = $res_79;
                $this->pos = $pos_79;
                unset( $res_79 );
                unset( $pos_79 );
            }
            if (( $subres = $this->literal( ']]' ) ) !== false) {
                $result["text"] .= $subres;
            } else {
                $_81 = false;
                break;
            }
            $_81 = true;
            break;
        } while (0);
        if ( $_81 === true ) {
            return $this->finalise($result);
        }
        if ( $_81 === false) {
            return false;
        }
    }


    /* blockclose: '[[/' ws? name:blockid ws? ']]' */
    protected $match_blockclose_typestack = array('blockclose');
    function match_blockclose ($stack = array()) {
        $matchrule = "blockclose";
        $result = $this->construct($matchrule, $matchrule, null);
        $_88 = null;
        do {
            if (( $subres = $this->literal( '[[/' ) ) !== false) {
                $result["text"] .= $subres;
            } else {
                $_88 = false;
                break;
            }
            $res_84 = $result;
            $pos_84 = $this->pos;
            $matcher = 'match_'.'ws';
            $key = $matcher;
            $pos = $this->pos;
            $subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
            if ($subres !== false) {
                $this->store( $result, $subres );
            } else {
                $result = $res_84;
                $this->pos = $pos_84;
                unset( $res_84 );
                unset( $pos_84 );
            }
            $matcher = 'match_'.'blockid';
            $key = $matcher;
            $pos = $this->pos;
            $subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
            if ($subres !== false) {
                $this->store( $result, $subres, "name" );
            } else {
                $_88 = false;
                break;
            }
            $res_86 = $result;
            $pos_86 = $this->pos;
            $matcher = 'match_'.'ws';
            $key = $matcher;
            $pos = $this->pos;
            $subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
            if ($subres !== false) {
                $this->store( $result, $subres );
            } else {
                $result = $res_86;
                $this->pos = $pos_86;
                unset( $res_86 );
                unset( $pos_86 );
            }
            if (( $subres = $this->literal( ']]' ) ) !== false) {
                $result["text"] .= $subres;
            } else {
                $_88 = false;
                break;
            }
            $_88 = true;
            break;
        } while (0);
        if ( $_88 === true ) {
            return $this->finalise($result);
        }
        if ( $_88 === false) {
            return false;
        }
    }


    /* castext: ( item:ioblock | item:texcasblock | item:rawcasblock | item:mathmodeopen | item:mathmodeclose | item:misc | item:ws | item:blockclose | item:blockopen | item:blockempty | item:breaks)* */
    protected $match_castext_typestack = array('castext');
    function match_castext ($stack = array()) {
        $matchrule = "castext";
        $result = $this->construct($matchrule, $matchrule, null);
        while (true) {
            $res_132 = $result;
            $pos_132 = $this->pos;
            $_131 = null;
            do {
                $_129 = null;
                do {
                    $res_90 = $result;
                    $pos_90 = $this->pos;
                    $matcher = 'match_'.'ioblock'; $key = $matcher; $pos = $this->pos;
                    $subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
                    if ($subres !== false) {
                        $this->store( $result, $subres, "item" );
                        $_129 = true;
                        break;
                    }
                    $result = $res_90;
                    $this->pos = $pos_90;
                    $_127 = null;
                    do {
                        $res_92 = $result;
                        $pos_92 = $this->pos;
                        $matcher = 'match_'.'texcasblock'; $key = $matcher; $pos = $this->pos;
                        $subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
                        if ($subres !== false) {
                            $this->store( $result, $subres, "item" );
                            $_127 = true;
                            break;
                        }
                        $result = $res_92;
                        $this->pos = $pos_92;
                        $_125 = null;
                        do {
                            $res_94 = $result;
                            $pos_94 = $this->pos;
                            $matcher = 'match_'.'rawcasblock'; $key = $matcher; $pos = $this->pos;
                            $subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
                            if ($subres !== false) {
                                $this->store( $result, $subres, "item" );
                                $_125 = true;
                                break;
                            }
                            $result = $res_94;
                            $this->pos = $pos_94;
                            $_123 = null;
                            do {
                                $res_96 = $result;
                                $pos_96 = $this->pos;
                                $matcher = 'match_'.'mathmodeopen'; $key = $matcher; $pos = $this->pos;
                                $subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
                                if ($subres !== false) {
                                    $this->store( $result, $subres, "item" );
                                    $_123 = true;
                                    break;
                                }
                                $result = $res_96;
                                $this->pos = $pos_96;
                                $_121 = null;
                                do {
                                    $res_98 = $result;
                                    $pos_98 = $this->pos;
                                    $matcher = 'match_'.'mathmodeclose'; $key = $matcher; $pos = $this->pos;
                                    $subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
                                    if ($subres !== false) {
                                        $this->store( $result, $subres, "item" );
                                        $_121 = true;
                                        break;
                                    }
                                    $result = $res_98;
                                    $this->pos = $pos_98;
                                    $_119 = null;
                                    do {
                                        $res_100 = $result;
                                        $pos_100 = $this->pos;
                                        $matcher = 'match_'.'misc'; $key = $matcher; $pos = $this->pos;
                                        $subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
                                        if ($subres !== false) {
                                            $this->store( $result, $subres, "item" );
                                            $_119 = true;
                                            break;
                                        }
                                        $result = $res_100;
                                        $this->pos = $pos_100;
                                        $_117 = null;
                                        do {
                                            $res_102 = $result;
                                            $pos_102 = $this->pos;
                                            $matcher = 'match_'.'ws'; $key = $matcher; $pos = $this->pos;
                                            $subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
                                            if ($subres !== false) {
                                                $this->store( $result, $subres, "item" );
                                                $_117 = true;
                                                break;
                                            }
                                            $result = $res_102;
                                            $this->pos = $pos_102;
                                            $_115 = null;
                                            do {
                                                $res_104 = $result;
                                                $pos_104 = $this->pos;
                                                $matcher = 'match_'.'blockclose'; $key = $matcher; $pos = $this->pos;
                                                $subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
                                                if ($subres !== false) {
                                                    $this->store( $result, $subres, "item" );
                                                    $_115 = true;
                                                    break;
                                                }
                                                $result = $res_104;
                                                $this->pos = $pos_104;
                                                $_113 = null;
                                                do {
                                                    $res_106 = $result;
                                                    $pos_106 = $this->pos;
                                                    $matcher = 'match_'.'blockopen'; $key = $matcher; $pos = $this->pos;
                                                    $subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
                                                    if ($subres !== false) {
                                                        $this->store( $result, $subres, "item" );
                                                        $_113 = true;
                                                        break;
                                                    }
                                                    $result = $res_106;
                                                    $this->pos = $pos_106;
                                                    $_111 = null;
                                                    do {
                                                        $res_108 = $result;
                                                        $pos_108 = $this->pos;
                                                        $matcher = 'match_'.'blockempty'; $key = $matcher; $pos = $this->pos;
                                                        $subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
                                                        if ($subres !== false) {
                                                            $this->store( $result, $subres, "item" );
                                                            $_111 = true;
                                                            break;
                                                        }
                                                        $result = $res_108;
                                                        $this->pos = $pos_108;
                                                        $matcher = 'match_'.'breaks'; $key = $matcher; $pos = $this->pos;
                                                        $subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
                                                        if ($subres !== false) {
                                                            $this->store($result, $subres, "item");
                                                            $_111 = true;
                                                            break;
                                                        }
                                                        $result = $res_108;
                                                        $this->pos = $pos_108;
                                                        $_111 = false;
                                                        break;
                                                    } while (0);
                                                    if ( $_111 === true ) {
                                                        $_113 = true;
                                                        break;
                                                    }
                                                    $result = $res_106;
                                                    $this->pos = $pos_106;
                                                    $_113 = false;
                                                    break;
                                                } while (0);
                                                if ( $_113 === true ) {
                                                    $_115 = true;
                                                    break;
                                                }
                                                $result = $res_104;
                                                $this->pos = $pos_104;
                                                $_115 = false;
                                                break;
                                            } while (0);
                                            if ( $_115 === true ) {
                                                $_117 = true;
                                                break;
                                            }
                                            $result = $res_102;
                                            $this->pos = $pos_102;
                                            $_117 = false;
                                            break;
                                        } while (0);
                                        if ( $_117 === true ) {
                                            $_119 = true;
                                            break;
                                        }
                                        $result = $res_100;
                                        $this->pos = $pos_100;
                                        $_119 = false;
                                        break;
                                    } while (0);
                                    if ( $_119 === true ) {
                                        $_121 = true;
                                        break;
                                    }
                                    $result = $res_98;
                                    $this->pos = $pos_98;
                                    $_121 = false;
                                    break;
                                } while (0);
                                if ( $_121 === true ) {
                                    $_123 = true;
                                    break;
                                }
                                $result = $res_96;
                                $this->pos = $pos_96;
                                $_123 = false;
                                break;
                            } while (0);
                            if ( $_123 === true ) {
                                $_125 = true;
                                break;
                            }
                            $result = $res_94;
                            $this->pos = $pos_94;
                            $_125 = false;
                            break;
                        } while (0);
                        if ( $_125 === true ) {
                            $_127 = true;
                            break;
                        }
                        $result = $res_92;
                        $this->pos = $pos_92;
                        $_127 = false;
                        break;
                    } while (0);
                    if ( $_127 === true ) {
                        $_129 = true;
                        break;
                    }
                    $result = $res_90;
                    $this->pos = $pos_90;
                    $_129 = false;
                    break;
                } while (0);
                if ( $_129 === false) {
                    $_131 = false;
                    break;
                }
                $_131 = true;
                break;
            } while (0);
            if ( $_131 === false) {
                $result = $res_132;
                $this->pos = $pos_132;
                unset( $res_132 );
                unset( $pos_132 );
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

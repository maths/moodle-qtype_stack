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
            $mathmode = FALSE;
            $parse_tree['mathmode'] = FALSE;
            if (array_key_exists('item', $parse_tree) && is_array($parse_tree['item']) && count($parse_tree['item']) > 1 && !array_key_exists('_matchrule',$parse_tree['item'])) {
                foreach ($parse_tree['item'] as $key => $value) {
                    if ($value['_matchrule'] == 'mathmodeclose') {
                        $mathmode = FALSE;
                    } else if ($value['_matchrule'] == 'mathmodeopen') {
                        $mathmode = TRUE;
                    }
                    $parse_tree['item'][$key]['mathmode'] = $mathmode;
                }
            }
        }

        if (array_key_exists('item',$parse_tree) && is_array($parse_tree['item']) && !array_key_exists('_matchrule',$parse_tree['item']) && count($parse_tree['item']) > 1) {
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
                        $parse_tree['item'][$next]['_matchrule'] == 'mathmodeclose' ){
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
        if (!array_key_exists('mathmode',$parse_tree)) {
            $mathmode = FALSE;
            $parse_tree['mathmode'] = FALSE;
            if (array_key_exists('item',$parse_tree) && is_array($parse_tree['item']) && count($parse_tree['item']) > 1 && !array_key_exists('_matchrule',$parse_tree['item'])) {
                foreach ($parse_tree['item'] as $key => $value) {
                    if ($value['_matchrule'] == 'mathmodeclose') {
                        $mathmode = FALSE;
                    } else if ($value['_matchrule'] == 'mathmodeopen') {
                        $mathmode = TRUE;
                    }        
                    $parse_tree['item'][$key]['mathmode'] = $mathmode;
                }
            }
        }

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
                        // There is a missmatch of open-close tags, 
                        // generic error handling handles that
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
   
        $err = stack_cas_castext_castextparser::extract_block_missmatch($parse_tree);
        if (count($err) > 0) {
            if (array_key_exists('errors',$parse_tree)) {
                $parse_tree['errors'] .= '<br/>' . implode('<br/>',$err);
            } else {
                $parse_tree['errors'] = implode('<br/>',$err);
            }
        }

        return $parse_tree;
    }

    private static function extract_block_missmatch($parse_tree) {
        $err = array();
        switch ($parse_tree['_matchrule']) {
            case "castext":
            case "block":
                if (array_key_exists('_matchrule',$parse_tree['item'])) {
                    $err = stack_cas_castext_castextparser::extract_block_missmatch($parse_tree['item']);
                } else {
                    $err = array();
                    foreach ($parse_tree['item'] as $sub_tree) {
                        $err = array_merge($err,stack_cas_castext_castextparser::extract_block_missmatch($sub_tree));
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


    /*!*
    texcasblock: "{@" cascontent:/[^@]+/ "@}"
    rawcasblock: "{#" cascontent:/[^#]+/ "#}"
    mathmodeopen: ( '\(' | '\[' )
    mathmodeclose: ( '\)' | '\]' )
    blockid: /[a-zA-Z0-9\-_]+/ 
    ws: (' ' | /[\n\t\r]/ )+
    misc:  /[^\{\[\\]+/ 
    breaks:  ( '{' | '[' | '\\' )
    param: ws key:blockid '=' q:/["']/ value:/[^$q]+/ "$q" 
    ioblock: '[[' ws? channel:blockid ws? ':' ws? var:blockid ws? ']]'
    blockempty: '[[' ws? name:blockid (params:param)* ws? '/]]'
    blockopen: '[[' ws? name:blockid (params:param)* ws? ']]'
    blockclose: '[[/' ws? name:blockid ws? ']]'
    castext : ( item:ioblock | item:texcasblock | item:rawcasblock | item:mathmodeopen | item:mathmodeclose | item:misc | item:ws | item:blockclose | item:blockopen | item:blockempty | item:breaks)*
    */

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
    public $mathmode = false;

    /**
     * Converts the nested array form tree to parsetreenode-tree
     */
    public static function build_from_nested($parse_tree,$parent=NULL) {
        $node = new stack_cas_castext_parsetreenode();
        $node->parent = $parent;
        if (array_key_exists('mathmode',$parse_tree)) {
            $node->mathmode = $parse_tree['mathmode'];
        }
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
     * Gets the mathmode
     */
    public function get_mathmode() {
        return $this->mathmode;
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
?>
<?php
// This file is part of Stack - https://stack.maths.ed.ac.uk
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

defined('MOODLE_INTERNAL') || die();

// CAS text parser
//
// @copyright  2013 Aalto University
// @copyright  2012 University of Birmingham
// @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.

// WARNING! if the file you are reading has .php-extension do not edit it! It has been generated from castext.peg.inc.

// Howto generate the .php file: run the following command, in the directory of this file:
// php ../../../thirdparty/php-peg/cli.php castext.peg.inc > autogen/castextparser.class.php
// And do remove that PHP ending the question mark greater than thing after generation. If generated.

require_once(__DIR__ . '/../../../../thirdparty/php-peg/autoloader.php');
use hafriedlander\Peg\Parser;
/**
 * Defines the text parser for identifying STACK specific parts from CAStext, does not work with XML,
 * intended to parse text-fragments and attribute values.
 * Pointless to use if your text does not include the following strings "{@", "{#" or "[["
 */
class stack_cas_castext_castextparser extends Parser\Basic {

    /**
     * A list of TeX environments that act as math-mode.
     */
    private static $mathmodeenvs = array('align', 'align*', 'alignat', 'alignat*', 'eqnarray', 'eqnarray*', 'equation',
            'equation*', 'gather', 'gather*', 'multline', 'multline*');

    /**
     * A function to test a string for necessary features related to castextparser.
     * returns true if the string should be passed trough the parser
     */
    public static function castext_parsing_required($test) {
        return (strpos($test, "{@") !== false || strpos($test, "{#") !== false || strpos($test, "[[") !== false);
    }

    /**
     * Takes a parse tree and concatenates the text-elements of its leafs.
     * Intentionally skips the text-element of the root as modifications made
     * to the leafs might not have been done there.
     */
    public static function to_string($parsetree) {
        $r = "";
        switch ($parsetree['_matchrule']) {
            case "castext":
                if (array_key_exists('_matchrule', $parsetree['item'])) {
                    $r .= self::to_string($parsetree['item']);
                } else {
                    foreach ($parsetree['item'] as $subtree) {
                        $r .= self::to_string($subtree);
                    }
                }
                break;
            case "block":
                $r .= "[[ " . $parsetree['name'];

                if (count($parsetree['params']) > 0) {
                    foreach ($parsetree['params'] as $key => $value) {
                        $r .= " $key=";
                        if (strpos($value, '"') === false) {
                            $r .= '"' . $value . '"';
                        } else {
                            $r .= "'$value'";
                        }
                    }
                }

                $r .= " ]]";

                if (array_key_exists('_matchrule', $parsetree['item'])) {
                    $r .= self::to_string($parsetree['item']);
                } else {
                    foreach ($parsetree['item'] as $subtree) {
                        $r .= self::to_string($subtree);
                    }
                }
                $r .= "[[/ " . $parsetree['name'] . " ]]";

                break;
            case "pseudoblock":
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
                $r .= $parsetree['text'];
                break;
        }
        return $r;
    }

    /**
     * This function searches the tree for adjacent text nodes and joins them together.
     * Not unlike similar functions in DOM-parsers.
     * returns an array that has been normalized
     */
    public static function normalize($parsetree) {
        // Start by paintting the mathmode if not paintted elsewhere.
        if (!array_key_exists('mathmode', $parsetree)) {
            $mathmode = false;
            $parsetree['mathmode'] = false;
            if (array_key_exists('item', $parsetree) && is_array($parsetree['item']) && count($parsetree['item']) > 1 &&
                    !array_key_exists('_matchrule', $parsetree['item'])) {
                foreach ($parsetree['item'] as $key => $value) {
                    if ($value['_matchrule'] == 'mathmodeclose') {
                        $mathmode = false;
                    } else if ($value['_matchrule'] == 'mathmodeopen') {
                        $mathmode = true;
                    } else if ($value['_matchrule'] == 'begintexenv' &&
                            array_search($value['value']['text'], self::$mathmodeenvs) !== false) {
                        $mathmode = true;
                    } else if ($value['_matchrule'] == 'endtexenv' &&
                            array_search($value['value']['text'], self::$mathmodeenvs) !== false) {
                        $mathmode = false;
                    }

                    $parsetree['item'][$key]['mathmode'] = $mathmode;
                }
            }
        }

        if (array_key_exists('item', $parsetree) && is_array($parsetree['item']) && !array_key_exists('_matchrule',
                $parsetree['item']) && count($parsetree['item']) > 1) {
            // Key listing maybe not continuous...
            $keys = array_keys($parsetree['item']);
            for ($i = 0; $i < count($keys) - 1; $i++) {
                $now = $keys[$i];
                $next = $keys[$i + 1];
                if ($parsetree['item'][$now]['_matchrule'] == 'ws' ||
                    $parsetree['item'][$now]['_matchrule'] == 'misc' ||
                    $parsetree['item'][$now]['_matchrule'] == 'breaks' ||
                    $parsetree['item'][$now]['_matchrule'] == 'text' ||
                    $parsetree['item'][$now]['_matchrule'] == 'mathmodeopen' ||
                    $parsetree['item'][$now]['_matchrule'] == 'mathmodeclose' ||
                    $parsetree['item'][$now]['_matchrule'] == 'begintexenv' ||
                    $parsetree['item'][$now]['_matchrule'] == 'endtexenv' ) {
                    if ($parsetree['item'][$next]['_matchrule'] == 'ws' ||
                        $parsetree['item'][$next]['_matchrule'] == 'misc' ||
                        $parsetree['item'][$next]['_matchrule'] == 'breaks' ||
                        $parsetree['item'][$next]['_matchrule'] == 'mathmodeopen' ||
                        $parsetree['item'][$next]['_matchrule'] == 'mathmodeclose' ||
                        $parsetree['item'][$next]['_matchrule'] == 'begintexenv' ||
                        $parsetree['item'][$next]['_matchrule'] == 'endtexenv') {
                        $parsetree['item'][$next]['text'] = $parsetree['item'][$now]['text'].$parsetree['item'][$next]['text'];
                        $parsetree['item'][$next]['_matchrule'] = 'text';
                        unset($parsetree['item'][$now]);
                    } else {
                        $parsetree['item'][$now]['_matchrule'] = 'text';
                    }
                } else {
                    $parsetree['item'][$now] = self::normalize($parsetree['item'][$now]);
                    if ($parsetree['item'][$next]['_matchrule'] == 'ws' ||
                        $parsetree['item'][$next]['_matchrule'] == 'misc' ||
                        $parsetree['item'][$next]['_matchrule'] == 'breaks' ||
                        $parsetree['item'][$next]['_matchrule'] == 'mathmodeopen' ||
                        $parsetree['item'][$next]['_matchrule'] == 'mathmodeclose' ||
                        $parsetree['item'][$next]['_matchrule'] == 'begintexenv' ||
                        $parsetree['item'][$next]['_matchrule'] == 'endtexenv' ) {
                        $parsetree['item'][$next]['_matchrule'] = 'text';
                    }
                }
            }
        }
        return $parsetree;
    }

    /**
     * This function searches a flat tree for matching block-ends and converts them to a better structure.
     * It will also remap any parameters to a simpler form. And paint the mathmode bit on the blocks.
     * returns an array that has been remapped in that way.
     */
    public static function block_conversion($parsetree) {
        // Start by paintting the mathmode if not paintted in previous normalise or elsewhere.
        if (!array_key_exists('mathmode', $parsetree)) {
            $mathmode = false;
            $parsetree['mathmode'] = false;
            if (array_key_exists('item', $parsetree) && is_array($parsetree['item']) && count($parsetree['item']) > 1 &&
                    !array_key_exists('_matchrule', $parsetree['item'])) {
                foreach ($parsetree['item'] as $key => $value) {
                    if ($value['_matchrule'] == 'mathmodeclose') {
                        $mathmode = false;
                    } else if ($value['_matchrule'] == 'mathmodeopen') {
                        $mathmode = true;
                    } else if ($value['_matchrule'] == 'begintexenv' &&
                            array_search($value['value']['text'], self::$mathmodeenvs) !== false) {
                        $mathmode = true;
                    } else if ($value['_matchrule'] == 'endtexenv' &&
                            array_search($value['value']['text'], self::$mathmodeenvs) !== false) {
                        $mathmode = false;
                    }
                    $parsetree['item'][$key]['mathmode'] = $mathmode;
                }
            }
        }

        $somethingchanged = true;
        while ($somethingchanged) {
            $somethingchanged = false;
            if (array_key_exists('item', $parsetree) && is_array($parsetree['item']) && count($parsetree['item']) > 1 &&
                    !array_key_exists('_matchrule', $parsetree['item'])) {
                $endblocks = array();
                $startblocks = array();
                foreach ($parsetree['item'] as $key => $value) {
                    if ($value['_matchrule'] == 'blockclose') {
                        $endblocks[] = $key;
                    } else if ($value['_matchrule'] == 'blockopen') {
                        $startblocks[] = $key;
                    } else if ($value['_matchrule'] == 'blockempty') {
                        $parsetree['item'][$key]['_matchrule'] = "block";
                        $parsetree['item'][$key]['name'] = $parsetree['item'][$key]['name'][1]['text'];
                        $params = array();

                        if (array_key_exists('params', $parsetree['item'][$key])) {
                            if (array_key_exists('_matchrule', $parsetree['item'][$key]['params'])) {
                                $params[$parsetree['item'][$key]['params']['key']['text']
                                        ] = $parsetree['item'][$key]['params']['value']['text'];
                            } else {
                                foreach ($parsetree['item'][$key]['params'] as $param) {
                                    $params[$param['key']['text']] = $param['value']['text'];
                                }
                            }
                        }
                        $parsetree['item'][$key]['params'] = $params;
                        $parsetree['item'][$key]['item'] = array();
                    }
                }

                // Special pseudo blocks 'else' and 'elif' need to be taken from the flow.
                $filteredstartblocks = array();
                foreach ($startblocks as $start) {
                    if ($parsetree['item'][$start]['name'][1]['text'] == 'else' ||
                            $parsetree['item'][$start]['name'][1]['text'] == 'elif') {
                        $parsetree['item'][$start]['_matchrule'] = "pseudoblock";
                        $parsetree['item'][$start]['name'] = $parsetree['item'][$start]['name'][1]['text'];

                        $params = array();

                        if (array_key_exists('params', $parsetree['item'][$start])) {
                            if (array_key_exists('_matchrule', $parsetree['item'][$start]['params'])) {
                                $params[$parsetree['item'][$start]['params']['key']['text']
                                        ] = $parsetree['item'][$start]['params']['value']['text'];
                            } else {
                                foreach ($parsetree['item'][$start]['params'] as $param) {
                                    $params[$param['key']['text']] = $param['value']['text'];
                                }
                            }
                        }
                        $parsetree['item'][$start]['params'] = $params;
                        $parsetree['item'][$start]['item'] = array();
                    } else {
                        $filteredstartblocks[] = $start;
                    }
                }
                $startblocks = $filteredstartblocks;

                $i = 0;
                while ($i < count($endblocks)) {
                    $endcandidateindex = $endblocks[$i];
                    $closeststartcandidate = -1;
                    foreach ($startblocks as $cand) {
                        if ($cand < $endcandidateindex && $cand > $closeststartcandidate) {
                            $closeststartcandidate = $cand;
                        }
                    }
                    if ($i > 0 && $endblocks[$i - 1] > $closeststartcandidate) {
                        // There is a missmatch of open-close tags, generic error handling handles that.
                        $i++;
                        break;
                    }

                    $i++;

                    if ($closeststartcandidate !== null && $parsetree['item'][$endcandidateindex]['name'][1]['text'
                            ] == $parsetree['item'][$closeststartcandidate]['name'][1]['text']) {
                        $parsetree['item'][$closeststartcandidate]['_matchrule'] = "block";

                        $parsetree['item'][$closeststartcandidate]['name'
                                ] = $parsetree['item'][$closeststartcandidate]['name'][1]['text'];

                        $params = array();

                        if (array_key_exists('params', $parsetree['item'][$closeststartcandidate])) {
                            if (array_key_exists('_matchrule', $parsetree['item'][$closeststartcandidate]['params'])) {
                                $params[$parsetree['item'][$closeststartcandidate]['params']['key']['text']
                                        ] = $parsetree['item'][$closeststartcandidate]['params']['value']['text'];
                            } else {
                                foreach ($parsetree['item'][$closeststartcandidate]['params'] as $param) {
                                    $params[$param['key']['text']] = $param['value']['text'];
                                }
                            }
                        }
                        $parsetree['item'][$closeststartcandidate]['params'] = $params;
                        $parsetree['item'][$closeststartcandidate]['item'] = array();

                        foreach ($parsetree['item'] as $key => $value) {
                            if ($key > $closeststartcandidate && $key < $endcandidateindex) {
                                $parsetree['item'][$closeststartcandidate]['item'][] = $value;
                                $parsetree['item'][$closeststartcandidate]['text'] .= $value['text'];
                                unset($parsetree['item'][$key]);
                            }
                        }

                        $parsetree['item'][$closeststartcandidate]['text'] .= $parsetree['item'][$endcandidateindex]['text'];
                        unset($parsetree['item'][$endcandidateindex]);

                        $somethingchanged = true;
                        break;
                    }
                }
            }
        }

        $err = self::extract_block_missmatch($parsetree);
        if (count($err) > 0) {
            if (array_key_exists('errors', $parsetree)) {
                $parsetree['errors'] .= '<br/>' . implode('<br/>', $err);
            } else {
                $parsetree['errors'] = implode('<br/>', $err);
            }
        }

        return $parsetree;
    }

    private static function extract_block_missmatch($parsetree, $parent = null) {
        $err = array();
        switch ($parsetree['_matchrule']) {
            case "castext":
            case "block":
                if (array_key_exists('_matchrule', $parsetree['item'])) {
                    $err = self::extract_block_missmatch($parsetree['item'], $parsetree['name']);
                } else {
                    $err = array();
                    $pseudos = array();
                    foreach ($parsetree['item'] as $subtree) {
                        if ($subtree['_matchrule'] == 'pseudoblock') {
                            $pseudos[] = $subtree['name'];
                        }
                        $err = array_merge($err, self::extract_block_missmatch($subtree, $parsetree['name']));
                    }
                    if ($parsetree['name'] == 'if') {
                        $elsefound = false;
                        $elseifafterelse = false;
                        $multipleelse = false;
                        foreach ($pseudos as $pseudo) {
                            if ($pseudo == 'else') {
                                if ($elsefound) {
                                    $multipleelse = true;
                                } else {
                                    $elsefound = true;
                                }
                            }
                            if ($pseudo == 'elif') {
                                if ($elsefound) {
                                    $elseifafterelse = true;
                                }
                            }
                        }
                        if ($multipleelse) {
                            $err[] = stack_string('stackBlock_multiple_else');
                        }
                        if ($elseifafterelse) {
                            $err[] = stack_string('stackBlock_elif_after_else');
                        }
                    }
                }
                break;
            case "pseudoblock":
                if ($parsetree['name'] == 'else' && $parent !== 'if') {
                    $err[] = stack_string('stackBlock_else_out_of_an_if');
                } else if ($parsetree['name'] == 'elif' && $parent !== 'if') {
                    $err[] = stack_string('stackBlock_elif_out_of_an_if');
                }
                break;
            case "blockopen":
                $err[] = "'[[ " . $parsetree['name'][1]['text'] . " ]]' " . stack_string('stackBlock_missmatch');
                break;
            case "blockclose":
                $err[] = "'[[/ " . $parsetree['name'][1]['text'] . " ]]' " . stack_string('stackBlock_missmatch');
                break;
        }

        return $err;
    }

// @codingStandardsIgnoreStart
    /*!*
    texcasblock: "{@" cascontent:/[^@]+/ "@}"
    rawcasblock: "{#" cascontent:/[^#]+/ "#}"
    mathmodeopen: ( '\(' | '\[' )
    mathmodeclose: ( '\)' | '\]' )
    begintexenv: "\begin{" value:/[a-zA-Z0-9\*]+/ "}"
    endtexenv: "\end{" value:/[a-zA-Z0-9\*]+/ "}"
    blockid: /[a-zA-Z0-9\-_]+/
    ws: (' ' | /[\n\t\r]/ )+
    misc:  /[^\{\[\\]+/
    breaks:  ( '{' | '[' | '\\' )
    param: ws key:blockid '=' q:/["']/ value:/[^$q]+/ "$q"
    ioblock: '[[' ws? channel:blockid ws? ':' ws? var:blockid ws? ']]'
    blockempty: '[[' ws? name:blockid (params:param)* ws? '/]]'
    blockopen: '[[' ws? name:blockid (params:param)* ws? ']]'
    blockclose: '[[/' ws? name:blockid ws? ']]'
    castext : ( item:ioblock | item:texcasblock | item:rawcasblock | item:mathmodeopen | item:mathmodeclose | item:misc | item:ws | item:blockclose | item:blockopen | item:blockempty | item:begintexenv | item:endtexenv | item:breaks)*
    */

    // SO WOULD HAVE WANTED THIS BUT COULD NOT UNDERSTAND HOWTO... SO NOW WE HAVE THE NESTED PARSING DONE AFTERWARDS.
    // block: '[[' ws? name:blockid (params:param)* ws? ']]' content:castext '[[/' ws? "$name" ws? ']]'
// @codingStandardsIgnoreEnd
}


/**
 * A custom datastructure for skipping the annoying task of working with references to arrays. The only array in this structure is
 * something we do not modify.
 */
class stack_cas_castext_parsetreenode {

    public $parent = null;
    public $nextsibling = null;
    public $previoussibling = null;
    public $firstchild = null;
    // There are six types, castext is the root, blocks are containers and text,
    // rawcasblock, texcasblock and ioblock are root nodes.
    public $type = "castext";
    private $params = null;
    private $content = "";
    public $mathmode = false;

    private static $reconditioncount = 1;

    /**
     * Converts the nested array form tree to parsetreenode-tree.
     */
    public static function build_from_nested($parsetree, $parent = null, $first = true) {
        $node = new stack_cas_castext_parsetreenode();
        $node->parent = $parent;
        if (array_key_exists('mathmode', $parsetree)) {
            $node->mathmode = $parsetree['mathmode'];
        }
        // We need the root node to attach certain special statistics in it.
        $root = $node;
        while ($root->parent !== null) {
            $root = $root->parent;
        }

        switch ($parsetree['_matchrule']) {
            case "block":
                $node->params = $parsetree['params'];
                $node->content = $parsetree['name'];
            case "castext":
                if (array_key_exists('_matchrule', $parsetree['item'])) {
                    $node->firstchild = self::build_from_nested($parsetree['item'], $node, false);
                } else {
                    $prev = null;
                    foreach ($parsetree['item'] as $subtree) {
                        $n = self::build_from_nested($subtree, $node, false);
                        if ($prev !== null) {
                            $n->previoussibling = $prev;
                            $prev->nextsibling = $n;
                        } else {
                            $node->firstchild = $n;
                        }
                        $prev = $n;
                    }
                }
                $node->type = $parsetree['_matchrule'];
                break;
            case "pseudoblock":
                $node->params = $parsetree['params'];
                $node->content = $parsetree['name'];
                $node->type = $parsetree['_matchrule'];
                break;
            case "rawcasblock":
            case "texcasblock":
                $node->type = $parsetree['_matchrule'];
                $node->content = $parsetree['cascontent']['text'];
                break;
            case "ioblock":
                $node->type = 'ioblock';
                $node->content = $parsetree['text'];
                $node->params = array('channel' => $parsetree['channel']['text'], 'id' => $parsetree['var']['text']);
                if ($root->params === null) {
                    $root->params = array('ioblocks');
                }
                if (!array_key_exists('ioblocks', $root->params)) {
                    $root->params['ioblocks'] = array($node->params['channel'] => array());
                }
                // With ioblocks we track how many of them are present.
                // Used in jsxgraph block to identify valid references to inputs.
                if (!array_key_exists($node->params['channel'], $root->params['ioblocks'])) {
                    $root->params['ioblocks'][$node->params['channel']] = array($node->params['id'] => 1);
                } else if (!array_key_exists($node->params['id'], $root->params['ioblocks'][$node->params['channel']])) {
                    $root->params['ioblocks'][$node->params['channel']][$node->params['id']] = 1;
                } else {
                    $root->params['ioblocks'][$node->params['channel']][$node->params['id']]++;
                }
                break;
            default:
                $node->type = 'text';
                $node->content = $parsetree['text'];
        }
        $node->normalize();
        if ($first) {
            $node->fix_pseudo_blocks();
        }
        return $node;
    }

    /**
     * Rewrites the tree so that we can get rid of the 'else' and 'elif' blocks.
     */
    private function fix_pseudo_blocks() {
        $nots = array();
        $pseudos = $this->find_nodes('type=pseudoblock');
        $iter = null;
        $c = 0;
        if (count($pseudos) > 0) {
            $iter = $pseudos[$c]->parent;
        }

        while ($iter !== null) {
            if ($iter->type == 'block' && $iter->content == 'if') {
                $reconds = array($iter);
                $needrecond = false;
                $i = $iter->firstchild;
                while ($i !== null) {
                    if ($i->type == 'pseudoblock' && $i->content == 'else') {
                        $reconds[] = $i;
                        if ($i->previoussibling !== null) {
                            $i->previoussibling->nextsibling = null;
                        }
                        $i->params['test'] = 'else';
                        $i->type = 'block';
                        $i->content = 'if';
                        $i->parent = $iter->parent;
                        $i->firstchild = $i->nextsibling;
                        $i->firstchild->previoussibling = null;
                        $i->nextsibling = $iter->nextsibling;
                        if ($iter->nextsibling !== null) {
                            $iter->nextsibling->previoussibling = $i;
                        }
                        $i->previoussibling = $iter;
                        $iter->nextsibling = $i;
                        $iter = $i;
                        $ii = $i->firstchild;
                        while ($ii !== null) {
                            $ii->parent = $i;
                            $ii = $ii->nextsibling;
                        }
                        $needrecond = true;
                        $i = null;
                    } else if ($i->type == 'pseudoblock' && $i->content == 'elif') {
                        $reconds[] = $i;
                        if ($i->previoussibling !== null) {
                            $i->previoussibling->nextsibling = null;
                        }
                        $i->type = 'block';
                        $i->content = 'if';
                        $i->parent = $iter->parent;
                        $i->firstchild = $i->nextsibling;
                        $i->firstchild->previoussibling = null;
                        $i->nextsibling = $iter->nextsibling;
                        if ($iter->nextsibling !== null) {
                            $iter->nextsibling->previoussibling = $i;
                        }
                        $i->previoussibling = $iter;
                        $iter->nextsibling = $i;
                        $iter = $i;
                        $ii = $i->firstchild;
                        while ($ii !== null) {
                            if ($ii->type == 'pseudoblock' && ($ii->content == 'else' || $ii->content == 'elif')) {
                                $i = $ii;
                                break;
                            }
                            $ii->parent = $i;
                            $ii = $ii->nextsibling;
                        }
                        $needrecond = true;
                    } else {
                        $i = $i->nextsibling;
                    }
                }
                // As we map a if-elif-else to new ifs we need to handle the case where the activating ifs contents would change the
                // value of the following ifs conditions and therefore allow multiple branches to activate. To do this we need to
                // evaluate all the conditions for all the branches before entering any active one. To do this we transfer
                // the conditions to a new define block to be evaluated before the ifs. As this means we need to generate variables
                // we need to do some tricks on the variable-names, we'll use very long names to ensure that there are no
                // collisions. This needs to be noted in unit testing as the naming of the vars can/will change.
                if ($needrecond) {
                    $newdef = new stack_cas_castext_parsetreenode();
                    $newdef->type = 'block';
                    $newdef->content = 'define';
                    $newdef->parent = $reconds[0]->parent;
                    $newdef->nextsibling = $reconds[0];
                    $newdef->previoussibling = $reconds[0]->previoussibling;
                    if ($newdef->previoussibling !== null) {
                        $newdef->previoussibling->nextsibling = $newdef;
                    } else {
                        $newdef->parent->firstchild = $newdef;
                    }
                    $reconds[0]->previoussibling = $newdef;
                    $newdef->params = array();
                    $cc = 0;
                    foreach ($reconds as $n) {
                        $key = 'stackparsecond' . self::$reconditioncount;
                        self::$reconditioncount++;
                        if ($cc == 0) {
                            $newdef->params[$key] = $n->get_parameter('test', 'false');
                        } else if ($n->get_parameter('test', 'false') == 'else') {
                            $keys = array();
                            foreach (array_slice($reconds, 0, -1) as $b) {
                                $keys[] = $b->get_parameter('test', 'false');
                            }
                            $newdef->params[$key] = 'not (' . implode(' or ', $keys). ')';
                        } else {
                            $newdef->params[$key] = 'not (' . $reconds[$cc - 1]->get_parameter('test', 'false') . ') and (' .
                                    $n->get_parameter('test', 'false') . ')';
                        }
                        $n->params['test'] = $key;
                        $cc++;
                    }
                }
            }
            $c++;
            $iter = null;
            while ($c < count($pseudos)) {
                if ($pseudos[$c]->type == 'pseudoblock') {
                    $iter = $pseudos[$c]->parent;
                    break;
                } else {
                    $c++;
                }
            }
        }
    }

    /**
     * A function for searching of something under the tree startting from this node.
     */
    private function find_nodes($search) {
        $r = array();
        if ($search == 'type=pseudoblock') {
            $iter = $this->firstchild;
            while ($iter !== null) {
                if ($iter->type == 'pseudoblock') {
                    $r[] = $iter;
                } else if ($iter->is_container()) {
                    $tmp = $iter->find_nodes($search);
                    $r = array_merge($r, $tmp);
                }
                $iter = $iter->nextsibling;
            }
        }
        return $r;
    }

    /**
     * Combines adjacent text-nodes.
     */
    public function normalize() {
        if ($this->is_container() && $this->firstchild !== null) {
            $this->firstchild->normalize();
        }
        $iter = $this;
        while ($iter->type == 'text' && $iter->nextsibling !== null && $iter->nextsibling->type == 'text') {
            $extra = $iter->nextsibling;
            $iter->content .= $extra->content;
            $iter->nextsibling = $extra->nextsibling;
            if ($iter->nextsibling !== null) {
                $iter->nextsibling->previoussibling = $this;
            }
            while ($iter->nextsibling !== null && !($iter->nextsibling->type == 'text' && $iter->type == 'text')) {
                $iter = $iter->nextsibling;
                if ($iter->is_container() && $iter->firstchild !== null) {
                    $iter->firstchild->normalize();
                }
            }
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
    public function convert_to_text($newcontent) {
        $this->type = "text";
        $this->content = $newcontent;
        // Clear other details just in case, makes dumping the var cleaner when debuging.
        $this->firstchild = null;
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
     * Returns the value of a parameter, usefull for nodes of the block-type. You can also set the default value returned should
     * such a parameter be missing.
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
     * Destroys this node (and its children) and removes it from its parent. Should you wish to access the parent the parent-link
     * of this node will work even after destruction.
     */
    public function destroy_node() {
        if ($this->parent->firstchild === $this) {
            $this->parent->firstchild = $this->nextsibling;
        }
        if ($this->nextsibling !== null) {
            $this->nextsibling->previoussibling = $this->previoussibling;
        }
        if ($this->previoussibling !== null) {
            $this->previoussibling->nextsibling = $this->nextsibling;
        }
    }

    /**
     * Destroys this node but promotes its children to its place. Perfect for removing if-blocks and other wrappers.
     */
    public function destroy_node_promote_children() {
        if ($this->firstchild !== null) {
            $next = $this->nextsibling;
            $iter = $this->firstchild;
            if ($this->parent->firstchild === $this) {
                $this->parent->firstchild = $iter;
            }
            if ($this->previoussibling !== null) {
                $this->previoussibling->nextsibling = $iter;
            }
            $iter->previoussibling = $this->previoussibling;
            $iter->parent = $this->parent;
            while ($iter->nextsibling !== null) {
                $iter->parent = $this->parent;
                $iter = $iter->nextsibling;
            }
            $iter->parent = $this->parent;
            $iter->nextsibling = $next;
            if ($next !== null) {
                $next->previoussibling = $iter;
            }
        } else {
            if ($this->nextsibling !== null && $this->previoussibling !== null) {
                $this->previoussibling->nextsibling = $this->nextsibling;
                $this->nextsibling->previoussibling = $this->previoussibling;
            } else if ($this->previoussibling !== null) {
                $this->previoussibling->nextsibling = null;
            } else {
                $this->parent->firstchild = null;
            }
        }
    }

    /**
     * Presents the node in string form, might not match perfectly to the original content as quotes and whitespace may have
     * changed.
     */
    public function to_string($format = 'normal', $indent = '') {
        $r = "";
        if ($format == 'normal') {
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
                    if ($this->firstchild !== null) {
                        $r .= " ]]";

                        $iterator = $this->firstchild;
                        while ($iterator !== null) {
                            $r .= $iterator->to_string($format);
                            $iterator = $iterator->nextsibling;
                        }

                        $r .= "[[/ " . $this->content . " ]]";
                    } else {
                        $r .= " /]]";
                    }
                    break;
                case "castext":
                    $iterator = $this->firstchild;
                    while ($iterator !== null) {
                        $r .= $iterator->to_string($format);
                        $iterator = $iterator->nextsibling;
                    }
                    break;
                case "pseudoblock": // This branch should newer be called, unless you skip certain steps.
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
                    break;
                case "ioblock":
                    return $this->content;
                case "text":
                    return $this->content;
                case "texcasblock":
                    return "{@" . $this->content . "@}";
                case "rawcasblock":
                    return "{#" . $this->content . "#}";
            }
        } else if ($format == 'condensed') {
            switch ($this->type) {
                case "block":
                    $r .= "[[" . $this->content;
                    if ($this->firstchild !== null) {
                        $r .= "]]";

                        $iterator = $this->firstchild;
                        while ($iterator !== null) {
                            $r .= $iterator->to_string($format);
                            $iterator = $iterator->nextsibling;
                        }

                        $r .= "[[/" . $this->content . "]]";
                    } else {
                        $r .= "/]]";
                    }
                    break;
                case "castext":
                    $iterator = $this->firstchild;
                    while ($iterator !== null) {
                        $r .= $iterator->to_string($format);
                        $iterator = $iterator->nextsibling;
                    }
                    break;
                case "pseudoblock": // This branch should newer be called, unless you skip certain steps.
                    $r .= "[[" . $this->content . "]]";
                    break;
                case "ioblock":
                    return $this->content;
                case "text":
                    return $this->content;
                case "texcasblock":
                    return "{@" . $this->content . "@}";
                case "rawcasblock":
                    return "{#" . $this->content . "#}";
            }
        } else if ($format == 'debug') {
            switch ($this->type) {
                case "block":
                    $r .= "\n$indent" . "[[ " . $this->content;
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
                    if ($this->firstchild !== null) {
                        $r .= " ]]";

                        $iterator = $this->firstchild;
                        while ($iterator !== null) {
                            $r .= $iterator->to_string($format, $indent . "  ");
                            $iterator = $iterator->nextsibling;
                        }

                        $r .= "\n$indent" . "[[/ " . $this->content . " ]]";
                    } else {
                        $r .= " /]]";
                    }
                    break;
                case "castext":
                    $iterator = $this->firstchild;
                    while ($iterator !== null) {
                        $r .= $iterator->to_string($format);
                        $iterator = $iterator->nextsibling;
                    }
                    break;
                case "pseudoblock": // This branch should never be called, unless you skip certain steps.
                    $r .= "\n$indent" . "[[ " . $this->content;
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
                    $r .= " ]]\n";
                    break;
                case "ioblock":
                    return $this->content;
                case "text":
                    return $this->content;
                case "texcasblock":
                    return "{@" . $this->content . "@}";
                case "rawcasblock":
                    return "{#" . $this->content . "#}";
            }
        }

        return $r;
    }
}

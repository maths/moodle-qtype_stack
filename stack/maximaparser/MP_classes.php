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
// Stateful by Matti Harjula 2017

/*
 * Class defintions for the PHP version of the PEGJS parser.
 * toString functions are mainly to document what the objects parts mean. But
 * you can do some debugging with them.
 * end of the file contains functions the parser uses...
 */

class MP_Node {
    public $parentnode = null;
    public $position = null;
    protected $children = null;

    public function __construct() {
        $this->parentnode = null;
        $this->position = null;
        $this->children = array();
    }

    public function &getChildren() {
        return $this->children;
    }

    public function regenChildren() {
        // For those users that must do direct manipulation that bypasses children updating.
    }

    public function hasChildren() {
        return count($this->children)> 0;
    }

    public function toString($params = null) {
        return "[NO TOSTRING FOR " . get_class($this). "]";
    }
    // Calls a function for all this nodes children.
    // Callback needs to take a node and return true if it changes nothing or does no structural changes
    // if it does structural changes it must return false so that the recursion may be repeated on
    // the changed structure
    public function callbackRecurse($function) {
        for ($i = 0; $i < count($this->children); $i++) {
            // Not a foreach as the list may change.
            $this->children[$i]->parentnode = $this;
            if ($function($this->children[$i]) !== true) {
                return false;
            }
            if ($this->children[$i]->callbackRecurse($function) !== true) {
                return false;
            }
        }
        return true;
    }

    public function &asAList() {
        // This one recursively goes through the whole tree and returns a list of
        // all the nodes found, it also populates the parent details as those might
        // be handy. You can act more efficiently with that list if you need to go
        // through it multiple times than if you were to recurse the tree multiple
        // times. Especially, when the tree is deep.
        $r = array($this);

        foreach ($this->getChildren() as $child) {
            $child->parentnode = $this;
            $r = array_merge($r, $child->asAList());
        }
        return $r;
    }
    // Replace a child of this now with other...
    public function replace($node, $with) {
        // Noop for most.
    }

    public function debugPrint($originalcode) {
        $r = array($originalcode);
        if(!is_array($this->position)|| !isset($this->position['start'])|| !is_int($this->position['start'])) {
            return 'Not possible to debug print without position data for the root node.';
        }
        $ofset = $this->position['start'];

        foreach($this->asAList()as $node) {
            $i = $node;

            while(!is_array($i->position)|| !isset($i->position['start'])) {
                $i = $i->parentnode;
            }
            $line = str_pad('', $i->position['start'] - $ofset);
            if($i === $node) {
                $line .= str_pad('', $i->position['end'] - $i->position['start'], '-');
            } else {
                $line .= str_pad('', $i->position['end'] - $i->position['start'], '?');
            }
            $line = str_pad($line, strlen($originalcode)+ 1);
            if(is_a($node, 'MP_EvaluationFlag')) {
                $line .= get_class($node);
            } else {
                $line .= get_class($node). ' ' . @ $node->op . @ $node->value . @ $node->mode;
            }
            $r[] = rtrim($line);
        }
        return implode("\n", $r);
    }

    // Quick check if we are part of an operation.
    public function is_in_operation() {
        if ($this->parentnode === null) {
            return false;
        }
        if (is_a($this->parentnode, 'MP_Operation')) {
            return true;
        }
        return is_a($this->parentnode, 'MP_PrefixOp') || is_a($this->parentnode, 'MP_PostfixOp');
    }

    // Extraction of terms in operations without caring about the references.
    // Returns null if none present or we are not part of an operation.
    /* TODO: bugs with '-a*b^c-d+e!+f/g(x+y)+z' for d.
    public function get_operand_on_right() {
        if ($this->parentnode === null) {
            return null;
        }

        if (is_a($this->parentnode, 'MP_Operation') && $this->parentnode->lhs === $this) {
            return $this->parentnode->leftmostofright();
        }

        if (is_a($this->parentnode, 'MP_Operation') || is_a($this->parentnode, 'MP_PrefixOp') || is_a($this->parentnode, 'MP_PostfixOp')) {
            return $this->parentnode->get_operand_on_right();
        }

        return null;
    }
    */

    public function get_operand_on_left() {
        // This will not unpack postfix terms so 5!+this => '5!'
        if ($this->parentnode === null) {
            return null;
        }

        if (is_a($this->parentnode, 'MP_Operation') && $this->parentnode->rhs === $this) {
            return $this->parentnode->rightmostofleft();
        }

        if (is_a($this->parentnode, 'MP_Operation') || is_a($this->parentnode, 'MP_PrefixOp') || is_a($this->parentnode, 'MP_PostfixOp')) {
            return $this->parentnode->get_operand_on_left();
        }

        return null;
    }

    public function get_operator_on_left() {
        // This will not unpack postfix terms so 5!+this => '+'
        if ($this->parentnode === null) {
            return null;
        }
        if (is_a($this->parentnode, 'MP_PrefixOp')) {
            return $this->parentnode->op;
        }
        if (is_a($this->parentnode, 'MP_Operation') && $this->parentnode->rhs === $this) {
            return $this->parentnode->op;
        }
        if (is_a($this->parentnode, 'MP_Operation') || is_a($this->parentnode, 'MP_PrefixOp') || is_a($this->parentnode, 'MP_PostfixOp')) {
            return $this->parentnode->get_operator_on_left();
        }
        return null;
    }

    /* TODO: bugs with '-a*b^c-d+e!+f/g(x+y)+z' for d.
    public function get_operator_on_right() {
        if ($this->parentnode === null) {
            return null;
        }
        if (is_a($this->parentnode, 'MP_PostfixOp')) {
            return $this->parentnode->op;
        }
        if (is_a($this->parentnode, 'MP_Operation') && $this->parentnode->lhs === $this) {
            return $this->parentnode->op;
        }
        if (is_a($this->parentnode, 'MP_Operation') || is_a($this->parentnode, 'MP_PrefixOp') || is_a($this->parentnode, 'MP_PostfixOp')) {
            return $this->parentnode->get_operator_on_right();
        }
        return null;
    }
    */
}

class MP_Operation extends MP_Node {
    public $op = '+';
    public $rhs = null;
    public $lhs = null;

    public function __construct($op, $lhs, $rhs) {
        parent::__construct();
        $this->op = $op;
        $this->lhs = $lhs;
        $this->rhs = $rhs;
        $this->children[] = &$lhs;
        $this->children[] = &$rhs;
    }

    public function toString($params = null) {
        if($params !== null && isset($params['red_null_position_stars'])&& $this->op === '*' && $this->position === null) {
            // This is a special rendering rule that colors all multiplications as red if they have no position.
            // i.e. if they have been added after parsing...
            return $this->lhs->toString($params). '<font color="red">' . $this->op . '</font>' . $this->rhs->toString($params);
        }
        if($params !== null && isset($params['red_false_position_stars_as_spaces'])&& $this->op === '*' && $this->position === false) {
            // This is a special rendering rule that colors all multiplications as red if they have no position.
            // i.e. if they have been added after parsing...
            return $this->lhs->toString($params). '<font color="red">_</font>' . $this->rhs->toString($params);
        }
        switch($this->op) {
            case 'and' : case 'or' : case 'nounand' : case 'nounor' : return $this->lhs->toString($params). ' ' . $this->op . ' ' . $this->rhs->toString($params);
        }
        return $this->lhs->toString($params). $this->op . $this->rhs->toString($params);
    }
    // Replace a child of this now with other...
    public function replace($node, $with) {
        $with->parentnode = $this;
        if($this->lhs === $node) {
            $this->lhs = $with;
        } else if($this->rhs === $node) {
            $this->rhs = $with;
        }
        $this->children = array(& $this->lhs,
                                & $this->rhs);
    }
    // Goes up the tree to identify if there is any op on the right of this.
    public function operationOnRight() {
        if($this->parentnode === null || !($this->parentnode instanceof MP_Operation || $this->parentnode instanceof MP_PostfixOp)) {
            return null;
        }
        if($this->parentnode->lhs === $this) {
            return $this->parentnode->op;
        } else {
            return $this->parentnode->operationOnRight();
        }
    }

    public function operationOnLeft() {
        if($this->parentnode === null || !($this->parentnode instanceof MP_Operation || $this->parentnode instanceof MP_PrefixOp)) {
            return null;
        }
        if($this->parentnode->rhs === $this) {
            return $this->parentnode->op;
        } else {
            return $this->parentnode->operationOnLeft();
        }
    }
    // Goes up the tree and back again to find the operand next to this operation.
    public function operandOnRight() {
        if($this->parentnode === null || !($this->parentnode instanceof MP_Operation || $this->parentnode instanceof MP_PostfixOp)) {
            return null;
        }
        $i = $this->parentnode;
        $last = $this;

        while($i->lhs === $last) {
            $last = $i;
            $i = $i->parentnode;
            if($i === null ||($i instanceof MP_Operation || $i instanceof MP_PostfixOp)) {
                return null;
            }
        }
        // $i is now the top of the branch and we go down the rhs sides left edge.
        return $i->leftmostofright();
    }

    public function operandOnLeft() {
        if($this->parentnode === null || !($this->parentnode instanceof MP_Operation || $this->parentnode instanceof MP_PrefixOp)) {
            return null;
        }
        $i = $this->parentnode;
        $last = $this;

        while($i->rhs === $last) {
            $last = $i;
            $i = $i->parentnode;
            if($i === null ||($i instanceof MP_Operation || $i instanceof MP_PostfixOp)) {
                return null;
            }
        }
        // $i is now the top of the branch and we go down the lhs sides right edge.
        return $i->rightmostofleft();
    }

    public function leftmostofright() {
        $i = $this->rhs;

        while($i instanceof MP_Operation || $i instanceof MP_PostfixOp) {
            $i = $i->lhs;
        }
        return $i;
    }

    public function rightmostofleft() {
        $i = $this->lhs;

        while($i instanceof MP_Operation || $i instanceof MP_PrefixOp) {
            $i = $i->rhs;
        }
        return $i;
    }
}

class MP_Atom extends MP_Node {
    public $value = null;

    public function __construct($value) {
        parent::__construct();
        $this->value = $value;
    }

    public function toString($params = null) {
        return "" . $this->value;
    }
}

class MP_Integer extends MP_Atom {
}

class MP_Float extends MP_Atom {
    // In certain cases we want to see the original form of the float.
    public $raw = null;

    public function __construct($value, $raw) {
        parent::__construct($value);
        $this->raw = $raw;
    }

    public function toString($params = null) {
        if($this->raw !== null)
            return $this->raw;
        return "" . $this->value;
    }
}

class MP_String extends MP_Atom {

    public function toString($params = null) {
        return '"' . str_replace('"', '\\"', $this->value). '"';
    }
}

class MP_Boolean extends MP_Atom {

    public function toString($params = null) {
        return $this->value ? 'true' : 'false';
    }
}

class MP_Identifier extends MP_Atom {
    // Covenience functions that work only after $parentnode has been filled in.
    public function is_function_name(): bool {
        return $this->parentnode != null && $this->parentnode instanceof MP_FunctionCall && $this->parentnode->name === $this;
    }

    public function is_variable_name(): bool {
        return !$this->is_function_name();
    }

    public function is_being_written_to(): bool {
        if($this->is_function_name()) {
            return $this->parentnode->is_definition();
        } else {
            // Direct assignment
            if($this->parentnode != null && $this->parentnode instanceof MP_Operation && $this->parentnode->op === ':' && $this->parentnode->lhs === $this) {
                return true;
            } else if($this->parentnode != null && $this->parentnode instanceof MP_List) {
                // multi assignment
                if($this->parentnode->parentnode != null && $this->parentnode->parentnode instanceof MP_Operation && $this->parentnode->parentnode->lhs === $this->parentnode) {
                    return $this->parentnode->parentnode->op === ':';
                }
            }
            return false;
        }
    }

    public function is_global(): bool {
        // This is expensive as we need to travel the whole parent-chain and do some paraller checks.
        $i = $this->parentnode;

        while($i != null) {
            if($i instanceof MP_FunctionCall) {
                if($i->is_definition()) {
                    return false;
                    // the arguments of a function definition are scoped to that function.
                } else if($i->name->value === 'block' || $i->name->value === 'lambda') {
                    // If this is wrapped to a block/lambda then we check the first arguments contents.
                    if($i->arguments[0] instanceof MP_List) {

                        foreach($i->arguments[0]->getChildren()as $v) {
                            if($v instanceof MP_Identifier && $v->value === $this->value) {
                                return false;
                            }
                        }
                    }
                }
            } else if($i instanceof MP_Operation &&($i->op === ':=' || $i->op === '::=')) {
                // The case where we exist on the rhs of function definition.
                if($i->lhs instanceof MP_FunctionCall) {

                    foreach($i->lhs->arguments as $v) {
                        if($v instanceof MP_Identifier && $v->value === $this->value) {
                            return false;
                        }
                    }
                }
            }
            $i = $i->parentnode;
        }
        return true;
    }
}

class MP_Annotation extends MP_Node {
    public $annotationType = null;
    public $params = null;

    public function __constructor($annotationType, $params) {
        parent::__construct();
        $this->annotationType = $annotationType;
        $this->params = $params;
        $this->children = $params;
    }

    public function toString($params = null) {
        $params = array();

        foreach($this->params as $value) {
            $params[] = ' ' . $value->toString($params);
        }
        if($this->annotationType === 'function') {
            return '@function' . $params[0] . ' =>' . $params[1] . ";";
        }
        return '@' . $this->annotationType . implode('', $params). ";";
    }
}

class MP_Comment extends MP_Node {
    public $value = null;
    public $annotations = null;

    public function __construct($value, $annotations) {
        parent::__construct();
        $this->value = $value;
        $this->annotations = $annotations;
        $this->children = $annotations;
    }

    public function toString($params = null) {
        $annotations = array();

        foreach($this->annotations as $value) {
            $annotations[] = $value->toString($params);
        }
        return '/*' . $this->value . implode("\n", $annotations). '*/';
    }
}

class MP_FunctionCall extends MP_Node {
    public $name = null;
    public $arguments = null;

    public function __construct($name, $arguments) {
        parent::__construct();
        $this->name = $name;
        $this->arguments = $arguments;
        $this->children = array_merge(array(&$name), $arguments);
    }

    public function toString($params = null) {
        $r = $this->name->toString($params). "(";
        $ar = array();

        foreach($this->arguments as $value)
            $ar[] = $value->toString($params);
        return $r . implode(',', $ar). ')';
    }
    // Covenience functions that work only after $parentnode has been filled in.
    public function is_definition(): bool {
        return $this->parentnode != null && $this->parentnode instanceof MP_Operation &&($this->parentnode->op === ':=' || $this->parentnode->op === '::=')&& $this->parentnode->lhs === $this;
    }

    public function is_call(): bool {
        return !$this->is_definition();
    }

    public function replace($node, $with) {
        $with->parentnode = $this;
        if($this->name === $node) {
            $this->name = $with;
        } else if($node === - 1) {
            // special case. append a node to arguments.
            $this->arguments[] = $with;
        } else {

            foreach($this->arguments as $key => $value) {
                if($value === $node) {
                    $this->arguments[$key] = $with;
                }
            }
        }
        $this->children = array_merge(array(&$this->name), $this->arguments);
    }
}

class MP_Group extends MP_Node {
    public $items = null;

    public function __construct($items) {
        parent::__construct();
        $this->items = $items;
        $this->children = $items;
    }

    public function toString($params = null) {
        $ar = array();

        foreach ($this->items as $value)
            $ar[] = $value->toString($params);
        return '(' . implode(',', $ar). ')';
    }

    public function replace($node, $with) {
        $with->parentnode = $this;
        if($node === - 1) {
            // special case. append a node to items.
            $this->items[] = $with;
        } else {

            foreach($this->items as $key => $value) {
                if($value === $node) {
                    $this->items[$key] = $with;
                }
            }
        }
        $this->children = $this->items;
    }
}

class MP_Set extends MP_Node {
    public $items = null;

    public function __construct($items) {
        parent::__construct();
        $this->items = $items;
        $this->children = $items;
    }

    public function toString($params = null) {
        $ar = array();

        foreach($this->items as $value)
            $ar[] = $value->toString($params);
        return '{' . implode(',', $ar). '}';
    }

    public function replace($node, $with) {
        $with->parentnode = $this;
        if($node === - 1) {
            // special case. append a node to items.
            $this->items[] = $with;
        } else {

            foreach($this->items as $key => $value) {
                if($value === $node) {
                    $this->items[$key] = $with;
                }
            }
        }
        $this->children = $this->items;
    }
}

class MP_List extends MP_Node {
    public $items = null;

    public function __construct($items) {
        parent::__construct();
        $this->items = $items;
        $this->children = $items;
    }

    public function toString($params = null) {
        $ar = array();

        foreach($this->items as $value)
            $ar[] = $value->toString($params);
        return '[' . implode(',', $ar). ']';
    }

    public function replace($node, $with) {
        $with->parentnode = $this;
        if($node === - 1) {
            // special case. append a node to items.
            $this->items[] = $with;
        } else {

            foreach($this->items as $key => $value) {
                if($value === $node) {
                    $this->items[$key] = $with;
                }
            }
        }
        $this->children = $this->items;
    }
}

class MP_PrefixOp extends MP_Node {
    public $op = '-';
    public $rhs = null;

    public function __construct($op, $rhs) {
        parent::__construct();
        $this->op = $op;
        $this->rhs = $rhs;
        $this->children[] = &$rhs;
    }

    public function toString($params = null) {
        if($this->op === 'not') {
            return $this->op . ' ' . $this->rhs->toString($params);
        }
        return $this->op . $this->rhs->toString($params);
    }

    public function replace($node, $with) {
        $with->parentnode = $this;
        if($this->rhs === $node) {
            $this->rhs = $with;
            $this->children = array(& $this->rhs);
        }
    }
}

class MP_PostfixOp extends MP_Node {
    public $op = '!';
    public $lhs = null;

    public function __construct($op, $lhs) {
        parent::__construct();
        $this->op = $op;
        $this->lhs = $lhs;
        $this->children[] = &$lhs;
    }

    public function toString($params = null) {
        return $this->lhs->toString($params). $this->op;
    }

    public function replace($node, $with) {
        $with->parentnode = $this;
        if($this->lhs === $node) {
            $this->lhs = $with;
            $this->children = array(& $this->lhs);
        }
    }
}

class MP_Indexing extends MP_Node {
    public $target = null;
    // This is and identifier of a function call
    public $indices = null;
    // These are MP_List objects.
    public function __construct($target, $indices) {
        parent::__construct();
        $this->target = $target;
        $this->indices = $indices;
        $this->children = array_merge(array(&$target), $indices);
    }

    public function toString($params = null) {
        $r = $this->target->toString($params);

        foreach($this->indices as $ind)
            $r .= $ind->toString($params);
        return $r;
    }

    public function replace($node, $with) {
        $with->parentnode = $this;
        if($this->target === $node) {
            $this->target = $with;
        } else {

            foreach($this->indices as $key => $value) {
                if($value === $node) {
                    $this->indices[$key] = $with;
                }
            }
        }
        $this->children = array_merge(array(&$this->target), $this->indices);
    }
}

class MP_If extends MP_Node {
    public $conditions = null;
    public $branches = null;

    public function __construct($conditions, $branches) {
        parent::__construct();
        $this->conditions = $conditions;
        $this->branches = $branches;
        $this->children = array_merge($conditions, $branches);
    }

    public function toString($params = null) {
        $r = 'if ' . $this->conditions[0]->toString($params). ' then ' . $this->branches[0]->toString($params);
        if(count($this->conditions)> 1) {
            for($i = 1;
            $i < count ($this->conditions );
            $i ++ ) {
                $r .= ' elseif ' . $this->conditions[$i]->toString($params). ' then ' . $this->branches[$i]->toString($params);
            }
        }
        if(count($this->branches)> count($this->conditions))
            $r .= ' else ' . $this->branches[count($this->conditions)]->toString($params);
        return $r;
    }

    public function replace($node, $with) {
        $with->parentnode = $this;

        foreach($this->conditions as $key => $value) {
            if($value === $node) {
                $this->conditions[$key] = $with;
            }
        }

        foreach($this->branches as $key => $value) {
            if($value === $node) {
                $this->branches[$key] = $with;
            }
        }
        $this->children = array_merge($this->conditions, $this->branches);
    }
}

class MP_Loop extends MP_Node {
    public $body = null;
    public $conf = null;

    public function __construct($body, $conf) {
        parent::__construct();
        $this->body = $body;
        $this->conf = $conf;
        $this->children = array_merge($conf, array(&$body));
    }

    public function replace($node, $with) {
        $with->parentnode = $this;

        foreach($this->conf as $key => $value) {
            if($value === $node) {
                $this->conf[$key] = $with;
            }
        }
        if($this->body === $node) {
            $this->body = $with;
        }
        $this->children = array_merge($this->conf, array(&$this->body));
    }

    public function toString($params = null) {
        $bits = array();
        foreach ($this->conf as $bit) {
            $bits[] = $bit->toString($params);
        }

        return implode(' ', $bits) . ' do ' . $this->body->toString($params);
    }
}

class MP_LoopBit extends MP_Node {
    public $mode = null;
    public $param = null;

    public function __construct($mode, $param) {
        parent::__construct();
        $this->mode = $mode;
        $this->param = $param;
        $this->children[] = &$param;
    }

    public function replace($node, $with) {
        $with->parentnode = $this;
        if($this->param === $node) {
            $this->param = $with;
        }
        $this->children = array(& $this->param);
    }

    public function toString($params = null) {
        return $this->mode . ' ' . $this->param->toString($params);
    }
}

class MP_EvaluationFlag extends MP_Node {
    public $name = null;
    public $value = null;

    public function __construct($name, $value) {
        parent::__construct();
        $this->name = $name;
        $this->value = $value;
        $this->children[] = &$name;
        $this->children[] = &$value;
    }

    public function toString($params = null) {
        return ',' . $this->name->toString($params). '=' . $this->value->toString($params);
    }

    public function replace($node, $with) {
        $with->parentnode = $this;
        if($this->name === $node) {
            $this->name = $with;
        } else if($this->value === $node) {
            $this->value = $with;
        }
        $this->children = array(& $this->name,
                                & $this->value);
    }
}

class MP_Statement extends MP_Node {
    public $statement = null;
    public $flags = null;

    public function __construct($statement, $flags) {
        parent::__construct();
        $this->statement = $statement;
        $this->flags = $flags;
        $this->children = array_merge(array(&$this->statement), $this->flags);
    }

    public function toString($params = null) {
        $r = $this->statement->toString($params);

        foreach($this->flags as $flag)
            $r .= $flag->toString($params);
        return $r;
    }

    public function replace($node, $with) {
        $with->parentnode = $this;

        foreach($this->flags as $key => $value) {
            if($value === $node) {
                $this->flags[$key] = $with;
            }
        }
        if($this->statement === $node) {
            $this->statement = $with;
        }
        $this->children = array_merge(array(&$this->statement), $this->flags);
    }
}

class MP_Root extends MP_Node {
    public $items = null;

    public function __construct($items) {
        parent::__construct();
        $this->items = $items;
        $this->children = $items;
    }

    public function toString($params = null) {
        $r = '';

        foreach($this->items as $item)
            $r .= $item->toString($params). ";\n";
        return $r;
    }

    public function replace($node, $with) {
        $with->parentnode = $this;

        foreach($this->items as $key => $value) {
            if($value === $node) {
                $this->items[$key] = $with;
            }
        }
        $this->children = $this->items;
    }
}
// These are required by the parser. They are defined here instead of the parser
// to avoid redeclaration. Basically, problem with the PHP target generator.
function opLBind($op) {
    switch($op) {
        case ':' : case '::' : case ':=' : case '::=' : return 180;
        case '!' : case '!!' : return 160;
        case '^' : return 140;
        case '.' : return 130;
        case '*' : case '/' : return 120;
        case '+' : case '-' : return 100;
        case '=' : case '*' : case '#' : case '>' : case '>=' : case '<' : case '<=' : return 80;
        case 'and' : case 'nounand' : return 65;
        case 'or' : case 'nounor' : return 60;
    }
    return 0;
}

function opRBind($op) {
    switch($op) {
        case ':' : case '::' : case ':=' : case '::=' : return 20;
        case '^' : return 139;
        case '.' : return 129;
        case '*' : case '/' : return 120;
        case '+' : return 100;
        case '-' : return 134;
        case '=' : case '#' : case '>' : case '>=' : case '<' : case '<=' : return 80;
        case 'not' : return 70;
    }
    return 0;
}

function opBind($op) {
    if(!($op instanceof MP_Operation)) {
        return $op;
    }
    // This one is not done with STACK.

    /*
     if ($op->op == '-') {
     $op->op='+';
     $pos = $op->rhs->position;
     $op->rhs=new MP_PrefixOp('-',$op->rhs);
     $op->rhs->position = $pos;
     $op->rhs->rhs->parentnode = $op->rhs;
     $op->rhs->parentnode = $op;
     }
     */
    $op->lhs = opBind($op->lhs);
    $op->rhs = opBind($op->rhs);
    if($op->lhs instanceof MP_Operation &&(opLBind($op->op)> opRBind($op->lhs->op))) {
        $posA = mergePosition($op->lhs->position, $op->position);
        $posB = mergePosition($op->lhs->rhs->position, $op->rhs->position);
        $nop = new MP_Operation($op->lhs->op, $op->lhs->lhs, new MP_Operation($op->op, $op->lhs->rhs, $op->rhs));
        $nop->position = $posA;
        $nop->rhs->position = $posB;
        $nop->parentnode = $op->parentnode;
        $nop->lhs->parentnode = $nop;
        $nop->rhs->parentnode = $nop;
        $nop->rhs->lhs->parentnode = $nop->rhs;
        $nop->rhs->rhs->parentnode = $nop->rhs;
        $op = $nop;
        $op = opBind($op);
    }
    if($op->rhs instanceof MP_Operation &&(opRBind($op->op)> opLBind($op->rhs->op))) {
        $posA = mergePosition($op->rhs->position, $op->position);
        $posB = mergePosition($op->lhs->position, $op->rhs->lhs->position);
        $nop = new MP_Operation($op->rhs->op, new MP_Operation($op->op, $op->lhs, $op->rhs->lhs), $op->rhs->rhs);
        $nop->position = $posA;
        $nop->lhs->position = $posB;
        $nop->parentnode = $op->parentnode;
        $nop->lhs->parentnode = $nop;
        $nop->rhs->parentnode = $nop;
        $nop->lhs->lhs->parentnode = $nop->lhs;
        $nop->lhs->rhs->parentnode = $nop->lhs;
        $op = $nop;
        $op = opBind($op);
    }
    if($op->lhs instanceof MP_PrefixOp &&(opLBind($op->op)> opRBind($op->lhs->op))) {
        $posA = mergePosition($op->lhs->position, $op->position);
        $posB = mergePosition($op->lhs->rhs->position, $op->rhs->position);
        $nop = new MP_PrefixOp($op->lhs->op, new MP_Operation($op->op, $op->lhs->rhs, $op->rhs));
        $nop->position = $posA;
        $nop->rhs->position = $posB;
        $nop->parentnode = $op->parentnode;
        $nop->rhs->parentnode = $nop;
        $nop->rhs->lhs->parentnode = $nop->rhs;
        $nop->rhs->rhs->parentnode = $nop->rhs;
        $op = $nop;
        $op = opBind($op);
    }
    if($op->rhs instanceof MP_PostfixOp &&(opRBind($op->op)> opLBind($op->rhs->op))) {
        $posA = mergePosition($op->rhs->position, $op->position);
        $posB = mergePosition($op->lhs->position, $op->rhs->lhs->position);
        $nop = new MP_PostfixOp($op->rhs->op, new MP_Operation($op->op, $op->lhs, $op->rhs->lhs));
        $nop->position = $posA;
        $nop->lhs->position = $posB;
        $nop->parentnode = $op->parentnode;
        $nop->lhs->parentnode = $nop;
        $nop->lhs->lhs->parentnode = $nop->lhs;
        $nop->lhs->rhs->parentnode = $nop->lhs;
        $op = $nop;
        $op = opBind($op);
    }
    return $op;
}

function mergePosition($posA, $posB) {
    // The position detail is a bit less verbose on the PHP parser as it costs to evaluate and the library does not have it.
    $R = array('start' =>$posA['start'],
               'end' =>$posA['end']);
    if($posB['start'] < $R['start'])
        $R['start'] = $posB['start'];
    if($posB['end'] > $R['end'])
        $R['end'] = $posB['end'];
    return $R;
}

<?php


require_once(__DIR__ . '/filter.interface.php');
require_once(__DIR__ . '/../cassecurity.class.php');

/**
 * AST filter that identifies cases like 'xsin(x)' and splits them
 * 'x*sin(x)'. Applies to all possible globally known functions and
 * tries to find the longest possible suffix. Probably causes issues
 * with self defined functions.
 *
 * Tags the stars and adds 'missing_stars' answernote.
 */
class stack_ast_common_function_name_multiplier_040 implements stack_cas_astfilter {
    public function filter(MP_Node $ast, array &$errors, array &$answernotes): MP_Node {
        $known = stack_cas_security::get_protected_identifiers('function');

        $process = function($node) use (&$answernotes, $known) {
            if ($node instanceof MP_Functioncall && $node->name instanceof MP_Identifier) {
                // Is it known?
                if (array_key_exists($node->name->value, $known)) {
                    return true;
                }

                // Find if there are any suffixes.
                $longest = false;
                $value = $node->name->value;
                $len = core_text::strlen($value);
                foreach ($known as $key => $other) {
                    if ($len > core_text::strlen($key)) {
                        if (core_text::substr($value, -core_text::strlen($key)) === $key) {
                            $longest = $key;
                            break;
                        }
                    }
                }

                // Split.
                if ($longest !== false) {
                    $prefix = core_text::substr($value, 0, -core_text::strlen($longest));
                    $node->name->value = $longest;
                    $nop = new MP_Operation('*', new MP_Identifier($prefix), $node);
                    $nop->position['insertstars'] = true;
                    $answernotes[] = 'missing_stars';
                    $node->parentnode->replace($node, $nop);
                    return false;
                }
            }
            return true;
        };
        // @codingStandardsIgnoreStart
        while ($ast->callbackRecurse($process) !== true) {
        }
        // @codingStandardsIgnoreEnd
        return $ast;
    }
}
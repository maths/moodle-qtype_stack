<?php

require_once(__DIR__ . '/../../maximaparser/MP_classes.php');

interface stack_cas_astfilter {
    
    /**
     * Does whatever it needs to the AST and may append to the errors or notes
     * migth receive stack_cas_casstring directly, but better to keep these 
     * separate.
     */
    public function filter(MP_Node $ast, array &$errors, array &$answernotes): MP_Node;

}
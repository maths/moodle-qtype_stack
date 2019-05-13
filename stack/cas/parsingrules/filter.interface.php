<?php

require_once(__DIR__ . '/../../maximaparser/MP_classes.php');
require_once(__DIR__ . '/../cassecurity.class.php');

interface stack_cas_astfilter {
    
    /**
     * Does whatever it needs to the AST and may append to the errors or notes
     * migth receive stack_cas_casstring directly, but better to keep these 
     * separate. The security object will tell about identifiers allowed and 
     * includes the knowledge of status of units mode.
     *
     * Any errors mean invalidity, but the process may continue.
     */
    public function filter(MP_Node $ast, array &$errors, array &$answernotes, stack_cas_security $identifierrules): MP_Node;

}
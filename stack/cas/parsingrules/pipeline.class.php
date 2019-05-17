<?php

require_once(__DIR__ . '/filter.interface.php');

/**
 * A chain of filters represeneted as singular filter and returned by 
 * the filter factory if asked for a set of filters.
 */
class stack_ast_filter_pipeline implements stack_cas_astfilter {
	private $filters = array();

	public function __construct($filters_in_order) {
		$this->filters = $filters_in_order;
	}

    public function filter(MP_Node $ast, array &$errors, array &$answernotes, stack_cas_security $identifierrules): MP_Node {
    	$filtered = $ast;
    	foreach ($this->filters as $filter) {
    		$filtered = $filter->filter($ast, $errors, $answernotes, $identifierrules);
    	}
    	return $filtered;
    }
}



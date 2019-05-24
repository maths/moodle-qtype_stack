<?php

// Part of cassession2 process, a description of what cassessions would take
// in and fill with values if need be.


// Things that go in the CAS.
interface cas_evaluatable {

	/**
	 * Is it valid for evaluation.
	 */
	public function get_valid(): bool;

	/**
	 * Returns a CAS code fragment that can be placed anywhere and modifies 
	 * the state of the CAS session. By placing anywhere we mean that it needs 
	 * to fit into an expression of the form "(something,$it,something)"
	 */
	public function get_evaluationform(): string;

	/**
	 * Receives errors that may have happened in CAS evaluation. Or an empty 
	 * list signaling that everything went well. Also returns wverything that 
	 * has gone through the StackAddFeedback() function as answernotes.
	 */
	public function set_cas_status(array $errors, array $answernotes);

	/**
	 * For error tracking puposes provides a name or reference to the area
	 * of logic from which this item came from. e.g. '/questionvariables'
	 * or '/prt/0/node/4/tans'.
	 */
	public function get_source_context(): string;

	/**
	 * If this is something with a specific key return it otherwise ''.
	 * A key is typically the left hand side of the topmost assignment 
	 * operation. Not all things have those and most do not need them,
	 * but old style systems tend to rely on them. Focus on removing any and
	 * all code relying on keys.
	 */
    public function get_key(): string;
}


// Things that also come out. In the value form.
interface cas_value_extractor extends cas_evaluatable {

	/**
	 * Receives the value that CAS returned when evaluating the session.
	 * note that the value is the value of the key given at the end of 
	 * the session not the value at the point this cas_evaluatable was 
	 * evaluated.
	 */
	public function set_cas_evaluated_value(MP_Node $ast);

}

// Things that also come out. In the latex form.
interface cas_latex_extractor extends cas_evaluatable {

	/**
	 * Receives the value that CAS returned when evaluating the session.
	 * note that the value is the value of the key given at the end of 
	 * the session not the value at the point this cas_evaluatable was 
	 * evaluated.
	 */
	public function set_cas_latex_value(string $latex);

}
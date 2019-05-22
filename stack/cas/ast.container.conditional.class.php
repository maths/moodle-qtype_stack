<?php

require_once(__DIR__ . '/parsingrules/parsingrule.factory.php');
require_once(__DIR__ . '/cassecurity.class.php');
require_once(__DIR__ . '/ast.container.silent.class.php');
require_once(__DIR__ . '/evaluatable_object.interfaces.php');
require_once(__DIR__ . '/../../locallib.php');
require_once(__DIR__ . '/../utils.class.php');
require_once(__DIR__ . '/../maximaparser/utils.php');
require_once(__DIR__ . '/../maximaparser/corrective_parser.php');
require_once(__DIR__ . '/../maximaparser/MP_classes.php');


class stack_ast_container_conditional extends stack_ast_container{

	private $conditions;

	public function set_conditions(array $conditions) {
		$this->conditions = $conditions;
	}


	public function get_valid(): bool {
		$valid = parent::get_valid();
		foreach ($this->conditions as $cond) {
			$valid = $valid && $cond->get_valid();
		}
		return $valid;
	}

	public function get_evaluationform(): string {
		if ($this->conditions === null || count($this->conditions) === 0) {
			return parent::get_evaluationform();
		}
		$content = parent::get_evaluationform();
		$conds = array();
		foreach ($this->conditions as $cond) {
			$conds[] = '(' . $cond->get_evaluationform() .')';
		}
		$r = 'if ' . implode(' and ', $conds) . ' then (' . $content . ') else ';
		if ($this->get_key() !== '') {
			$r .= $this->get_key() . ':false';
		} else {
			$r .= 'false';
		}
		return $r;
	}
}
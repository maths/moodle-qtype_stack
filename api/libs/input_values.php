<?php
require_once(__DIR__ . '/../../thirdparty/Parsedown/Parsedown.php');

class qtype_stack_api_input_values {
    const MAP = array(
        'matrix_parens' => array(
            'square' => '[',
            'round' => '(',
            'curly' => '{',
            'bar' => '['
        ),
        'insert_stars' => array(
            'none' => 0,
            'multiply' => 1,
            'sing_char_vars' => 2,
            'spaces' => 3,
            'multiply_spaces' => 4,
            'all' => 5
        ),
        'syntax_attribute' => array(
            'value' => 0,
            'placeholder' => 1
        ),
        'show_validations' => array(
            'none' => 0,
            'with_varlist' => 1,
            'without_varlist' => 2
        ),
        'score_mode' => array(
            'subtract' => '-',
            'add' => '+',
            'equals' => '='
        )
    );

    const BOOL_FIELDS = array(
        'strict_syntax', 'forbid_float', 'require_lowest_terms', 'check_answer_type', 'must_verify', 'quiet',
        'auto_simplify'
    );

    const TRUE_VALUES = array(
        'True', 'true', true, 1, '1','Yes', 'yes'
    );

    const FALSE_VALUES = array(
        'False', 'false', false, 0, '0', 'no', 'no'
    );

    const CONTENT_FIELDS = array(
        'specific_feedback', 'question', 'worked_solution', 'prt_correct', 'prt_partially_correct', 'prt_incorrect',
        'feedback'
    );

    public static function get_yaml_value(string $key, $value) {
        if (!array_key_exists($key, self::MAP)) {
            return $value;
        }
        $item = self::MAP[$key];
        return array_search ($value, $item);
    }

    private static function bool_field($value) {
            return in_array($value, self::TRUE_VALUES);
    }

    private static function process_markdown(string $value) {
        $parsedown = new Parsedown();
        return $parsedown->text($value);
    }

    private static function set_content(&$node, $field, $value) {
        if (!array_key_exists($field . '_html', $node)) {
            $node[$field . '_html'] = self::process_markdown($value);
        }
    }

    public static function get_stack_value(&$node, string $key, string $value) {

        if (in_array($key, self::BOOL_FIELDS)) {
            return self::bool_field($value);
        }
        if (in_array($key, self::CONTENT_FIELDS)) {
            self::set_content($node, $key, $value);
            return $value;
        }
        if (!array_key_exists($key, self::MAP) || !array_key_exists($value, self::MAP[$key])) {
            return $value;
        }

        return self::MAP[$key][$value];
    }
}
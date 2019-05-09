<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * External API for AJAX calls.
 *
 * @package qtype_stack
 * @copyright 2018 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace qtype_stack;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/externallib.php');

/**
 * External API for AJAX calls.
 */
class external extends \external_api {
    /**
     * Returns parameter types for validate_input function.
     *
     * @return \external_function_parameters Parameters
     */
    public static function validate_input_parameters() {
        return new \external_function_parameters([
            'qaid' => new \external_value(PARAM_INT, 'Question attempt id'),
            'name' => new \external_value(PARAM_ALPHANUMEXT, 'Input name'),
            'input' => new \external_value(PARAM_RAW, 'Input value')
        ]);
    }

    /**
     * Returns result type for validate_input function.
     *
     * @return \external_description Result type
     */
    public static function validate_input_returns() {
        return new \external_single_structure([
            'input' => new \external_value(PARAM_RAW, 'Input value'),
            'status' => new \external_value(PARAM_ALPHA, 'One of stack_input::BLANK, stack_input::VALID, ...'),
            'message' => new \external_value(PARAM_RAW, 'The answer message after validation, includes svg')
        ]);
    }

    /**
     * Validates STACK question type input data.
     *
     * @param int $qaid Question attempt id
     * @param string $name Input name
     * @param mixed $input Input value
     * @return array Array of input value, status and message.
     */
    public static function validate_input($qaid, $name, $input) {
        global $CFG;
        require_once($CFG->libdir . '/questionlib.php');
        require_once($CFG->dirroot . '/question/type/stack/stack/options.class.php');
        require_once($CFG->dirroot . '/question/type/stack/stack/input/inputbase.class.php');

        $params = self::validate_parameters(
                self::validate_input_parameters(),
                ['qaid' => $qaid, 'name' => $name, 'input' => $input]);
        self::validate_context(\context_system::instance());

        $dm = new \question_engine_data_mapper();
        $qa = $dm->load_question_attempt($params['qaid']);
        $question = $qa->get_question();

        $input = $question->inputs[$name];
        $state = $question->get_input_state($params['name'], $params['input'], true);

        return [
            'input'   => $params['input'],
            'status'  => $state->status,
            'message' => $input->render_validation($state, $qa->get_qt_field_name($params['name']))
        ];
    }
}

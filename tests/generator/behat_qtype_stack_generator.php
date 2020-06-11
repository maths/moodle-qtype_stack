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
 * Behat data generator for qtype_stack.
 *
 * @package   qtype_stack
 * @category  test
 * @copyright 2020 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


/**
 * Behat data generator for qtype_stack.
 */
class behat_qtype_stack_generator extends behat_generator_base {

    protected function get_creatable_entities(): array {
        return [
            'Deployed variants' => [
                'datagenerator' => 'deployed_variant',
                'required' => ['question', 'seed'],
                'switchids' => ['question' => 'questionid'],
            ],
            'Question tests' => [
                'datagenerator' => 'question_test',
                'required' => ['question'],
                'switchids' => ['question' => 'questionid'],
            ],
        ];
    }

    /**
     * Look up the id of a question from its name.
     *
     * @param string $questionname the question name, for example 'Question 1'.
     * @return int corresponding id.
     */
    protected function get_question_id(string $questionname): int {
        global $DB;

        if (!$id = $DB->get_field('question', 'id', ['name' => $questionname])) {
            throw new Exception('There is no question with name "' . $questionname . '".');
        }
        return $id;
    }
}

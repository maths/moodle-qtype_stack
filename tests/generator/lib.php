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

defined('MOODLE_INTERNAL') || die();

/**
 * STACK question type test data generator class
 *
 * @package   qtype_stack
 * @copyright  2020 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_stack_generator extends component_generator_base {

    /**
     * Create a deployed variant.
     *
     * @param array $data must have questionid and seed.
     */
    public function create_deployed_variant(array $data): void {
        /** @var qtype_stack_question $question */
        $question = question_bank::load_question($data['questionid']);
        $question->deploy_variant($data['seed']);
    }

    /**
     * Create a question test.
     *
     * The $data passed in must contain:
     *   questionid     - which question to add the test to.
     *   ans...         - the test input for each question input.
     *   prt..._grade   - the expected grade.
     *   prt..._penalty - the expected penalty.
     *   prt..._note    - the expected answer note.
     * The input and expected data must be given for all inputs and all PRTs.
     *
     * @param array $data as above.
     */
    public function create_question_test(array $data): void {
        /** @var qtype_stack_question $question */
        $question = question_bank::load_question($data['questionid']);

        $inputs = [];
        foreach ($question->inputs as $inputname => $notused) {
            $inputs[$inputname] = $data[$inputname];
        }
        $qtest = new stack_question_test($inputs);

        foreach ($question->prts as $prtname => $notused) {
            $qtest->add_expected_result($prtname,
                    new stack_potentialresponse_tree_state(
                            1, true, $data[$prtname . ' score'],
                            $data[$prtname . ' penalty'],
                            '', [$data[$prtname . ' note']]));
        }
        question_bank::get_qtype('stack')->save_question_test($question->id, $qtest);
    }
}

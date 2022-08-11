<?php
// This file is part of Stack - http://stack.maths.ed.ac.uk/
//
// Stack is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Stack is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Stack.  If not, see <http://www.gnu.org/licenses/>.

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../../vle_specific.php');

/**
 * Encapsulates the location of an error happening in CAS with the actual error.
 * Allows us to decide the level of error message specificity at the point of output.
 *
 * This class also defines the syntax for those context/location paths.
 *
 * @copyright  2022 Aalto University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class stack_cas_error {

    /**
     * @var string The location where things went wrong.
     */
    private $context;

    /**
     * @var string How things went wrong.
     */
    private $error;

    public function __construct(string $error , string $context = '') {
        $this->error   = $error;
        $this->context = $context;
    }

    /**
     * Takes a path and turns it into an array that is usable for addressing.
     * Note that we need to have access to the question for this to work.
     * Note that we do not define the type of a question in the function declaration
     * this makes it simpler for other systems (i.e. Stateful) to extend this class.
     */
    public static function interpret_context(string $context, $question) {
        /* NOTE! Only CAS valued items have paths, for now.
         * In the future various meta fields might also have paths, but now
         * we deal with the things that can error out in CAS.
         */

        // Short names for the root level items.
        static $qlevelfields = [
            // This is the only keyval field.
            'qv' => 'questionvariables',
            // Ones below are CASText.
            'gf' => 'generalfeedback',
            'qt' => 'questiontext',
            'sf' => 'specificfeedback',
            'qn' => 'questionnote',
            'pc' => 'prtcorrect',
            'pp' => 'prtpartiallycorrect',
            'pi' => 'prtincorrect',
            'td' => 'textdownload'
        ];

        // Short names for the PRT-node level items.
        static $nodefields = [
            // CASStrings.
            's' => 'sans',
            't' => 'tans',
            'o' => 'options',
            'at' => 'answertest',
            'st' => 'truescore',
            'pt' => 'truepenalty',
            'sf' => 'falsescore',
            'pf' => 'falsepenalty',
            // CASText.
            'ft' => 'truefeedback',
            'ff' => 'falsefeedback'
        ];

        $interpreted = [];

        // If we have no context or it does not follow the format and start with '/'.
        if ($context === '' || strpos($context, '/') !== 0) {
            // We mark this as a general error and just push on.
            $interpreted['general'] = true;
            return $interpreted;
        }

        $parts = explode('/', $context);

        // Note that we are checking for location data even for single statement fields,
        // it is not impossible that in the future we compile even them so that there are
        // substatement errors in them as well.

        // For indexing we go for the definition order and do it zero-based.
        switch ($parts[1]) {
            case 'p':
                $prt = $question->prts[array_keys($question->prts)[intval($parts[2])]];
                $interpreted['prt'] = $prt->get_name();
                if (count($parts) > 2) {
                    if ($parts[3] === 'fv') {
                        $interpreted['field'] = 'feedbackvariables';
                        if (count($parts) > 3) {
                            // If we have line:char level location data.
                            $interpreted['detail'] = $parts[4];
                        }
                    } else if ($parts[3] === 'n') {
                        // Note that once we have names for the nodes we don't need these
                        // offsets and prefixes.
                        $interpreted['node'] = 'node ' .
                            (array_keys($prt->get_nodes_summary())[intval($parts[4])]->nodename + 1);
                        if (count($parts) > 5 && isset($nodefields[$parts[5]])) {
                            $interpreted['field'] = $nodefields[$parts[5]];
                            if (count($parts) > 6) {
                                $interpreted['detail'] = $parts[6];
                            }
                        }
                    }
                }
                break;
            case 'i':
                $input = $question->inputs[array_keys($question->prts)[intval($parts[2])]];
                $interpreted['input'] = $input->get_name();
                // The input options in the old world are limited to tans.
                // All others are input2 and will get their names written out in full.
                if (count($parts) > 2) {
                    if ($parts[3] === 'a') {
                        $interpreted['field'] = 'tans';
                    } else {
                        $interpreted['field'] = $parts[3];
                    }
                    if (count($parts) > 3) {
                        // If we have line:char level location data.
                        $interpreted['detail'] = $parts[4];
                    }
                }
                break;
            case 't':
                // As the question tests are not in the question mode we need to fetch them.
                $tests = $question->qtype->load_question_tests();
                $test = $tests[array_keys($tests)[intval($parts[2])]];
                $interpreted['questiontest'] = $test->testcase;

                // For question tests instead of indexing we use the input names directly.
                if (count($parts) > 2) {
                    $interpreted['testinput'] = $parts[3];
                    if (count($parts) > 3) {
                        // If we have line:char level location data.
                        $interpreted['detail'] = $parts[4];
                    }
                }
                break;
            default:
                // All the root fields, vars and texts.
                $interpreted['field'] = $qlevelfields[$parts[1]];
                if (count($parts) > 2) {
                    // If we have line:char level location data.
                    $interpreted['detail'] = $parts[2];
                }
        }
        return $interpreted;
    }

    /**
     * For use in old tests, validation and editors, whereever the context is
     * clear from the placement of the error message.
     * @return string raw error without context.
     */
    public function get_legacy_error(): string {
        // Note we might want to check if the path ends with a detail-element.
        // Such a thing might make this better when working with CASText or keyvals.
        return $this->error;
    }

    public function get_context(): string {
        return $this->context;
    }

    public function get_interpreted_context($question): array {
        // Maybe that should be cached, on the other hand errors are slow anyway.
        return self::interpret_context($this->context);
    }

    /**
     * Gives an error message customised to the user's role.
     *
     * Note that we do not define the type of a question in the function declaration
     * this makes it simpler for other systems (i.e. Stateful) to extend this class.
     *
     * @param $question A question that can be used to interpret the path and that can
     * tell us if the user can edit it and should therefore see more descriptive errors.
     * @return string
     */
    public function get_error($question): string {
        // NOTES:
        // (1) this code is not currently "plumbed in" to the rest of the code base (TODO).
        // (2) the lang strings have not been created, the idea is to have something like:
        // 'errorinfeedbackvarswithdetail' = '{$a->err} in feedback-variables of {$a->prt} specifically at {$a->detail}.'
        // 'errorinfeedbackvars' = '{$a->err} in feedback-variables of {$a->prt}.'
        // Order as you want and there are other vars available.

        $ctx = $this->get_interpreted_context($question);
        if (stack_user_can_edit_question($question)) {
            // Only editing people can have the error itself in the template.
            $ctx['err'] = $this->error;
            if (isset($ctx['prt'])) {
                if (isset($ctx['node'])) {
                    // In node it is either in the test, the points or in feedback.
                    if (isset($ctx['detail'])) {
                        return stack_string('errorinprtnodewithdetail', $ctx);
                    }
                    return stack_string('errorinprtnode', $ctx);
                } else {
                    // If not in a node it is a general issue or in feedback vars.
                    if (isset($ctx['field']) && $ctx['field'] === 'feedbackvariables') {
                        if (isset($ctx['detail'])) {
                            // Here use 'err', 'prt' and 'detail' in the string to describe more.
                            return stack_string('errorinfeedbackvarswithdetail', $ctx);
                        }
                        // With no detail use only 'err' and 'prt'.
                        return stack_string('errorinfeedbackvars', $ctx);
                    }
                }
                return stack_string('generalerrorinprt', $ctx);
            } else if (isset($ctx['input'])) {
                // TODO errors in inputs, tans, options, validation.
                return stack_string('errorininput', $ctx);
            } else if (isset($ctx['questiontest'])) {
                // TODO errors in evalution of specific inputs to tests.
                return stack_string('errorinquestiontest', $ctx);
            }
        } else {
            if (isset($ctx['prt'])) {
                if (isset($ctx['field'])
                    && ($ctx['field'] === 'truefeedback' || $ctx['field'] === 'falsefeedback')) {
                    return stack_string('errorinfeedback');
                }
                return stack_string('erroringrading');
            } else if (isset($ctx['input']) && isset($ctx['field'])
                && $ctx['field'] === 'validation') { // This is a special field-name.
                return stack_string('errorininputvalidation');
            } else if (isset($ctx['input'])) {
                return stack_string('errorininitialisingquestion');
            }
            // Everyhing else is a general error.
            return stack_string('generalerrorhappened');
        }

        if (isset($ctx['field'])) {
            // General unspecific error or error in a top-level field.
            if (isset($ctx['detail'])) {
                // Here use 'field' and 'detail' in the string to describe more.
                return stack_string('generalfielderrorwithdetail', $ctx);
            }
            // With no detail use only 'field'.
            return stack_string('generalfielderror', $ctx);
        }

        // Everything else is a general error.
        return stack_string('generalerrorhappened');
    }
}

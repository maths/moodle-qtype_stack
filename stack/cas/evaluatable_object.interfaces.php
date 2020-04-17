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

/**
 * Part of cassession2 process. A description of what cassessions would take
 * in and fill with values if need be.
 *
 * @package    qtype_stack
 * @copyright  2019 Aalto University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

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
     * list signaling that everything went well. Also returns everything that
     * has gone through the StackAddFeedback() function as feedback and
     * StackAddNote() as answernotes.
     */
    public function set_cas_status(array $errors, array $answernotes, array $feedback);

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

// Things that also come out. In the value form. Next two interfaces
// are mutually exclusive. The intention of the first is to parse the response
// with the MaximaParser while the second one does not, the second one is meant
// for cases where the full parser is overkill. For example CASText2 where
// the response is just nested lists of strings which can be parsed much
// cheaper using traditional means.
interface cas_value_extractor extends cas_evaluatable {

    /**
     * Receives the value that CAS returned when evaluating the session.
     * note that the value is the value of the key given at the end of
     * the session not the value at the point this cas_evaluatable was
     * evaluated. If there is no key then collects the value at the point
     * of evaluation.
     */
    public function set_cas_evaluated_value(MP_Node $ast);

}

interface cas_raw_value_extractor extends cas_evaluatable {

    /**
     * Receives the value that CAS returned when evaluating the session.
     * note that the value is the value of the key given at the end of
     * the session not the value at the point this cas_evaluatable was
     * evaluated. If there is no key then collects the value at the point
     * of evaluation.
     */
    public function set_cas_evaluated_value(string $value);

}

// Things that also come out. In the latex form.
interface cas_latex_extractor extends cas_evaluatable {

    /**
     * Receives the value of `stack_disp()` that CAS returned when
     * evaluating the session. Note that the value is the value of the key
     * given at the end of the session not the value at the point this
     * cas_evaluatable was evaluated. If there is no key then collects
     * the value at the point of evaluation.
     */
    public function set_cas_latex_value(string $latex);

}

// Things that also come out. In the old display form.
interface cas_display_value_extractor extends cas_evaluatable {

    /**
     * Receives the value of `stack_dispvalue()` that CAS returned when
     * evaluating the session. Note that the value is the value of the key
     * given at the end of the session not the value at the point this
     * cas_evaluatable was evaluated. If there is no key then collects
     * the value at the point of evaluation.
     */
    public function set_cas_display_value(string $displayvalue);

}
<?php
// This file is part of Stack - http://stack.bham.ac.uk/
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

/**
 * Stack question renderer class.
 *
 * @package   qtype_stack
 * @copyright 2012 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * Generates the output for Stack questions.
 *
 * @copyright 2012 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_stack_renderer extends qtype_renderer {
    public function formulation_and_controls(question_attempt $qa, question_display_options $options) {
        $question = $qa->get_question();

        $questiontext = $question->questiontext;
        foreach ($question->interactions as $name => $interaction) {
            // TODO: get the value of the current answer to put into the html.
            $currentanswer = ''; //$qa->get_last_qt_var('answer');
             $questiontext = str_replace("#{$name}#",
                 $interaction->get_xhtml($currentanswer, $options->readonly), $questiontext);

            // TODO see stack_old/lib/ui/DisplayItem.php.
            $questiontext = str_replace("<IEfeedback>{$name}</IEfeedback>", '', $questiontext);
        }

        foreach ($question->prts as $index => $prt) {
            // TODO see stack_old/lib/ui/DisplayItem.php.
            $questiontext = str_replace("<PRTfeedback>{$index}</PRTfeedback>", '', $questiontext);
        }

        return $question->format_text($questiontext, $question->questiontextformat,
                $qa, 'question', 'questiontext', $question->id);
    }
}

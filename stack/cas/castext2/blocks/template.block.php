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
//
defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../block.interface.php');
require_once(__DIR__ . '/../../../utils.class.php');

/**
 * A block for providing means for repetition with the option for
 * overridable content.
 *
 * Generates functions with the prefix 'ctt_'
 */
class stack_cas_castext2_template extends stack_cas_castext2_block {

    public function compile($format, $options): ?MP_Node {
        if (count($this->children) === 0) {
            // If we are applying a template then we need to decide how we
            // react to the template not existing.
            $result = new MP_String('');
            if (!(array_key_exists('mode', $this->params) && $this->params['mode'] === 'ignore missing')) {
                $result->value = 'Warning no template defined with name "' . $this->params['name'] . '"';
            }

            $r = new MP_If([
                new MP_FunctionCall(new MP_Identifier('fboundp'), [new MP_Identifier('ctt_' . $this->params['name'])])
                ], [
                    new MP_FunctionCall(new MP_Identifier('ctt_' . $this->params['name']), [new MP_Integer(0)]),
                    $result
                ]);

            return $r;
        }

        $body = new MP_List([new MP_String('%root')]);
        foreach ($this->children as $item) {
            $c = $item->compile($format, $options);
            if ($c !== null) {
                $body->items[] = $c;
            }
        }

        // Either use the body or override by something else.
        if (array_key_exists('mode', $this->params) && $this->params['mode'] === 'default') {
            $r = new MP_If([
                new MP_FunctionCall(new MP_Identifier('fboundp'), [new MP_Identifier('ctt_' . $this->params['name'])])
                ], [
                    new MP_FunctionCall(new MP_Identifier('ctt_' . $this->params['name']), [new MP_Integer(0)]),
                    $body
                ]);
            return $r;
        }

        // Define a template and render an empty string.
        return new MP_Group([
            new MP_Operation(':=', new MP_FunctionCall(new MP_Identifier('ctt_' . $this->params['name']),
                [new MP_Identifier('%dummyvariable')]), $body),
            new MP_String('')
        ]);
    }

    public function is_flat(): bool {
        if (count($this->children) === 0 && !array_key_exists('mode', $this->params)) {
            // When declaring a template the result will always be an empty string.
            return true;
        }
        // We cannot know if the overriding template is flat when using the template.
        return false;
    }

    public function validate_extract_attributes(): array {
        return [];
    }

    public function validate(&$errors=[], $options=[]): bool {
        if (!array_key_exists('name', $this->params)) {
            $errors[] = new $options['errclass']('The "template"-block needs a name.',
                $options['context'] . '/' . $this->position['start'] . '-' . $this->position['end']);
            return false;
        } else {
            $ev = stack_ast_container::make_from_teacher_source($this->params['name']);
            $ast = $ev->get_commentles_primary_statement();
            if (!($ast instanceof MP_Identifier)) {
                $errors[] = new $options['errclass']('The "template"-block needs a name that is suitable to be a function name.',
                    $options['context'] . '/' . $this->position['start'] . '-' .
                    $this->position['end']);
                return false;
            }
        }
        if (array_key_exists('mode', $this->params) && !($this->params['mode'] === 'default' ||
            $this->params['mode'] === 'ignore missing')) {
            $errors[] = new $options['errclass']('The "template"-blocks mode paramter can only have the values ' .
                '"default" or "ignore missing".', $options['context'] . '/' . $this->position['start'] . '-' .
                $this->position['end']);
            return false;
        }

        return true;
    }
}

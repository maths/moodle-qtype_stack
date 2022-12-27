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
require_once(__DIR__ . '/../utils.php');

/**
 * This is a block allowing one to construct a text-file using CASText
 * inside the block and generates an URL for a download link for that file.
 *
 * This is primarily meant for delivering random data for use in questions
 * that require tool use.
 *
 * Like JSXGraph always forces HTML formating for the contents.
 *
 * The current implementation only works in question-text and it is not
 * expected that one could ever include current input values or other values
 * built from them in the content. Though in Stateful this would be possible
 * using past stored values.
 */
class stack_cas_castext2_textdownload extends stack_cas_castext2_block {

    public static $countfiles = 1;

    public function compile($format, $options): ?MP_Node {
        if (!isset($options['in main content']) || !$options['in main content']) {
            throw new stack_exception('CASText2 textdownload currently only supportted in question-text / scene-text.');
        }

        $format = castext2_parser_utils::RAWFORMAT;

        $code = new MP_List([
            new MP_String('textdownload'),
            new MP_String($this->params['name']),
            new MP_String('' . self::$countfiles)
        ]);

        if (isset($options['stateful']) && $options['stateful'] === true) {
            $code->items[] = new MP_String('stateful');
        }

        // Collect the content for future.
        $content = '["%root",""';
        // Think about making this handled as AST as well.
        foreach ($this->children as $child) {
            $content .= ',' . $child->compile($format, $options)->toString();
        }
        $content .= ']';

        // Store it for pickup elsewhere.
        $this->params['text-download-content'] = [self::$countfiles => $content];

        // Remember to increment the count.
        self::$countfiles = self::$countfiles + 1;
        return $code;
    }

    public function is_flat(): bool {
        return false;
    }

    public function validate_extract_attributes(): array {
        return [];
    }

    public function postprocess(array $params, castext2_processor $processor): string {
        // Note different systems serve out through different logic.
        if (count($params) > 3 && $params[3] === 'stateful') {
            return (new moodle_url(
                '/question/type/stateful/textdownload.php', ['qaid' => $processor->qa->get_database_id(),
                'id' => $params[2], 'name' => $params[1]]))->out(false);
        }

        // Simply form the URL for getting the content out.
        return (new moodle_url(
            '/question/type/stack/textdownload.php', ['qaid' => $processor->qa->get_database_id(),
            'id' => $params[2], 'name' => $params[1]]))->out(false);
    }


    public function validate(&$errors=[], $options=[]): bool {
        if (!array_key_exists('name', $this->params)) {
            $errors[] = new $options['errclass']('The textdownload-block requires one to declare a name for the file.',
                $options['context'] . '/' . $this->position['start'] . '-' . $this->position['end']);
            return false;
        }

        return true;
    }
}

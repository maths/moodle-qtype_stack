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

/**
 * Validate input data
 * @param array $data
 * @return array
 */
function validatedata(array $data) {
    if (!array_key_exists('question', $data)) {
        printError('No question');
    }
    if (!array_key_exists('seed', $data)) {
        printError('Seed is required');
    }

    // Default values.
    $data['defaults'] = (array_key_exists('defaults', $data)) ? $data['defaults'] : null;
    $data['prefix'] = (array_key_exists('prefix', $data)) ? $data['prefix'] : '';
    $data['readOnly'] = (array_key_exists('readOnly', $data)) ? $data['readOnly'] : false;
    $data['feedback'] = (array_key_exists('feedback', $data)) ? $data['feedback'] : false;
    $data['score'] = (array_key_exists('score', $data)) ? $data['score'] : false;
    $data['answer'] = (array_key_exists('answer', $data)) ? $data['answer'] : [];
    $data['plots_protocol'] = (array_key_exists('plots_protocol', $data)) ? $data['plots_protocol'] : 'https';

    $GLOBALS['DOMAIN'] = $data['plots_protocol'] . '://' . $_SERVER['HTTP_HOST'];

    $answers = [];
    $prefixlength = strlen($data['prefix']);
    if ($prefixlength > 0) {
        foreach ($data['answer'] as $key => $value) {
            $noprefixkey = substr($key, $prefixlength);
            $answers[$noprefixkey] = $value;
        }
        $data['answer'] = $answers;
    }
    return $data;
}

<?php
// This file is part of Stack - http://stack.bham.ac.uk//
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
 * Interaction element that is a text area. Each line input becomes one element of a list.
 *
 * The value is stored as a string maxima list. For example [1,hello,x + y].
 *
 * @copyright  2012 University of Birmingham
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class STACK_Input_TextArea extends STACK_Input_Answer {

    public function __construct($name, $width = NULL, $default = NULL, $maxLength = NULL,
            $height = NULL, $param = NULL) {
        if (is_null($width)) {
            $width = 5;
        }
        if (!$default == NULL) {
            $default = '';
        }
        parent::__construct($name, $width, $default, $maxLength, $height, $param);
    }

    public function getXHTML($readonly) {
        // Note that at the moment, $this->boxHeight and $this->boxWidth are only
        // used as minimums. If the current input is bigger, the box is expanded.
        $rows = $this->tokenize_list($this->default);

        $height = max($this->boxHeight, count($rows) + 1);

        $value = '';
        $width = $this->boxWidth;
        foreach ($rows as $row) {
            $value .= $row . "\n";
            $width = max($width, strlen($row) + 5);
        }

        $disabled = '';
        if ($readonly) {
            $disabled = ' readonly="readonly"';
        }

        return '<textarea name="' . $this->name . '" rows="' . $height .
                '" cols="' . $width . '"' . $disabled . '>' . htmlspecialchars($value) . '</textarea>';
    }

    /**
     * Converts the inputs passed in into a textareas into a Maxima list
     *
     * TODO worry about lines of input that contain ','.
     *
     * @param string|array $in
     * @return string
     * @access public
     */
    public function transform($in) {
        if (!trim($in)) {
             return '';
        }

        $rowsin = explode("\n", $in);
        $rowsout = array();
        foreach ($rowsin as $key => $row) {
            $cleanrow = trim($row);
            if ($cleanrow) {
                $rowsout[] = $cleanrow;
            }
        }

        return '[' . implode(',', $rowsout) . ']';
    }

    /**
     * Take a list in maxima syntax, and split it into its component elements.
     *
     * @param string $in a list in Maxima syntax.
     * @return array the list elements.
     */
    protected function tokenize_list($in) {
        $su = new STACK_StringUtil($in);
        return $su->listToArray(false);
    }

    public function getTestPostData($value) {
        // This looks wrong to me. Or, at least, if it is right, it makes no sense yet.
        // It looks like it is expecting $value to be like "[a,b]", but if so, stripping
        // the delimiters is not really enough. We should tokenise, and then join with \n.
        return array($this->name => substr($value, 1, strlen($value) - 2));
    }

    /**
     * Return the default values for the options. Using this is optional, in this
     * base class implementation, no default options are set.
     * @return array option => default value.
     */
    public static function getOptionDefaults() {
        return array(
            'boxSize'       => 25,
            'studentVerify' => 'true',
            'hideFeedback'  => 'false'
        );
    }
}

<?php // TO BE DELETED
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

require_once(__DIR__ . '/insertstars0.class.php');
require_once(__DIR__ . '/insertstars1.class.php');
require_once(__DIR__ . '/insertstars2.class.php');
require_once(__DIR__ . '/insertstars3.class.php');
require_once(__DIR__ . '/insertstars4.class.php');
require_once(__DIR__ . '/insertstars5.class.php');

// @copyright  2018 University of Edinburgh.
// @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.

class stack_parsingrule_factory {

    public static function get_parsingrule($insertstars): stack_parser_logic {

        static $rules = null;
        if ($rules === null) {
            $rules = array(
                0 => new stack_parser_logic_insertstars0(),
                1 => new stack_parser_logic_insertstars1(),
                2 => new stack_parser_logic_insertstars2(),
                3 => new stack_parser_logic_insertstars3(),
                4 => new stack_parser_logic_insertstars4(),
                5 => new stack_parser_logic_insertstars5()
                );
        }
        return $rules[$insertstars];
    }

}
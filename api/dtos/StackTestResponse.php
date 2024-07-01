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
 * Response to a question test request
 *
 * @copyright  2024 Uuniversity of Edinburgh
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

namespace api\dtos;

class StackTestResponse {
    /** @var string */
    public $name = '';
    /** @var string */
    public $messages = '';
    /** @var bool */
    public $isupgradeerror = false;
    /** @var bool */
    public $isgeneralfeedback = false;
    /** @var bool */
    public $isdeployedseeds = false;
    /** @var bool */
    public $israndomvariants = false;
    /** @var bool */
    public $istests = false;
    /** @var array
     * Array keyed by seed (or 'noseed'). Subarrays have 'passes' , 'fails', 'messages[]', 'outcomes'.
     */
    public $results = [];
}

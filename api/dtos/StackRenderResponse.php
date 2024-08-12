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

// This script handles the various deploy/undeploy actions from questiontestrun.php.
//
// @copyright  2023 RWTH Aachen
// @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.

namespace api\dtos;
defined('MOODLE_INTERNAL') || die();

class StackRenderResponse {
    /** @var string */
    public $questionrender;
    /** @var string */
    public $questionsamplesolutiontext;
    /** @var StackRenderInput[]  */
    public $questioninputs;
    public $questionassets;
    /** @var int */
    public $questionseed;
    /** @var int[]  */
    public $questionvariants;
    /** @var array */
    public $iframes;
}

class StackRenderInput {
    /** @var int */
    public $validationtype;
    public $samplesolution;
    /** @var string */
    public $samplesolutionrender;
    /** @var array */
    public $configuration;
    /** @var string */
    public $render;
}

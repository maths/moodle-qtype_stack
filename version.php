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
 * Version information for the STACK question type.
 *
 * @package   qtype_stack
 * @copyright 2012 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$plugin->version   = 2020120600;
$plugin->requires  = 2018051700;
$plugin->cron      = 0;
$plugin->component = 'qtype_stack';
$plugin->maturity  = MATURITY_STABLE;
$plugin->release   = '4.3.8 for Moodle 3.5+';

$plugin->dependencies = array(
    'qbehaviour_adaptivemultipart'     => 2018080600,
    'qbehaviour_dfexplicitvaildate'    => 2018080600,
    'qbehaviour_dfcbmexplicitvaildate' => 2018080600,
);

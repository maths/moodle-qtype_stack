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

require_once(__DIR__ . '/../config.php');

error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', 1);
ini_set('html_errors', 1);

require_once(__DIR__ . '/../api/api.php');
require_once(__DIR__ . '/../question.php');

$api = new qtype_stack_api();

// Run this command once at install time to compile Maxima on your machine.
$api->install();

// Test an *uncached* call to the CAS.  I.e. a genuine call to the process.
list($message, $genuinedebug, $result) = stack_connection_helper::stackmaxima_genuine_connect();
$summary[] = array($result, $message);
echo html_writer::tag('p', $message);
echo html_writer::tag('p', $genuinedebug);
echo html_writer::tag('p', $result);

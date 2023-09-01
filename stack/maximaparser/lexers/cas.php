<?php
// This file is part of Stack - https://stack.maths.ed.ac.uk
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

require_once(__DIR__ . '/../parser.options.class.php');
require_once(__DIR__ . '/../lexer.base.php');


/**
 * This is a conveniently consistently named lexer that parses
 * CAS-syntax with no decimal grouping and US/UK style decimal 
 * and list separators.
 */
class stack_maxima_lexer_cas extends stack_maxima_base_lexer {
	// We do nothing to modify the base lexer.
}
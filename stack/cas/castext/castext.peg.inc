<?php
// This file is part of Stack - http://stack.bham.ac.uk/
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
 * CAS text parser
 *
 * @copyright  2013 Aalto University
 * @copyright  2012 University of Birmingham
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 ** WARNING! if the file you are reading has .php-extension do not edit it! It has been generated from castext.peg.inc.
 **/
/**
 ** Howto to generate the .php file, run the following command, in the directory of this file:
 ** php ../../../lib/php-peg/cli.php castext.peg.inc > castextparser.class.php
 **/
require_once(dirname(__FILE__) . '/../../../lib/php-peg/autoloader.php');
use hafriedlander\Peg\Parser;
/**
 * Defines the text parser for identifying STACK specific parts from CAStext, does not work with XML, 
 * intended to parse text-fragments and attribute values.
 * Pointless to use if your text does not include the following strings "{@" or "{#"
 */
class stack_cas_castext_castextparser extends Parser\Basic {

/**
 * A function to test a string for necessary features related to castextparser.
 * returns true if the string should be passed trough the parser
 */
public static function castext_parsing_required($test){
 if(strpos($test,"{@")!==FALSE)
  return true;
 if(strpos($test,"{#")!==FALSE)
  return true;

 return false;
}


/*!*

texcasblock: "{@" cascontent:/[^@]+/ "@}"

rawcasblock: "{#" cascontent:/[^#]+/ "#}"

misc:  /[^\{\[]+/

breaks: /[\{\[]+/

ws: (' ' | /[\n\t\r]/ )

castext : ( item:texcasblock | item:rawcasblock | item:misc | item:ws | item:breaks )*

*/

}
?>

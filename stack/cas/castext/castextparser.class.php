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


/* texcasblock: "{@" cascontent:/[^@]+/ "@}" */
protected $match_texcasblock_typestack = array('texcasblock');
function match_texcasblock ($stack = array()) {
	$matchrule = "texcasblock"; $result = $this->construct($matchrule, $matchrule, null);
	$_3 = NULL;
	do {
		if (( $subres = $this->literal( '{@' ) ) !== FALSE) { $result["text"] .= $subres; }
		else { $_3 = FALSE; break; }
		$stack[] = $result; $result = $this->construct( $matchrule, "cascontent" ); 
		if (( $subres = $this->rx( '/[^@]+/' ) ) !== FALSE) {
			$result["text"] .= $subres;
			$subres = $result; $result = array_pop($stack);
			$this->store( $result, $subres, 'cascontent' );
		}
		else {
			$result = array_pop($stack);
			$_3 = FALSE; break;
		}
		if (( $subres = $this->literal( '@}' ) ) !== FALSE) { $result["text"] .= $subres; }
		else { $_3 = FALSE; break; }
		$_3 = TRUE; break;
	}
	while(0);
	if( $_3 === TRUE ) { return $this->finalise($result); }
	if( $_3 === FALSE) { return FALSE; }
}


/* rawcasblock: "{#" cascontent:/[^#]+/ "#}" */
protected $match_rawcasblock_typestack = array('rawcasblock');
function match_rawcasblock ($stack = array()) {
	$matchrule = "rawcasblock"; $result = $this->construct($matchrule, $matchrule, null);
	$_8 = NULL;
	do {
		if (( $subres = $this->literal( '{#' ) ) !== FALSE) { $result["text"] .= $subres; }
		else { $_8 = FALSE; break; }
		$stack[] = $result; $result = $this->construct( $matchrule, "cascontent" ); 
		if (( $subres = $this->rx( '/[^#]+/' ) ) !== FALSE) {
			$result["text"] .= $subres;
			$subres = $result; $result = array_pop($stack);
			$this->store( $result, $subres, 'cascontent' );
		}
		else {
			$result = array_pop($stack);
			$_8 = FALSE; break;
		}
		if (( $subres = $this->literal( '#}' ) ) !== FALSE) { $result["text"] .= $subres; }
		else { $_8 = FALSE; break; }
		$_8 = TRUE; break;
	}
	while(0);
	if( $_8 === TRUE ) { return $this->finalise($result); }
	if( $_8 === FALSE) { return FALSE; }
}


/* misc:  /[^\{\[]+/ */
protected $match_misc_typestack = array('misc');
function match_misc ($stack = array()) {
	$matchrule = "misc"; $result = $this->construct($matchrule, $matchrule, null);
	if (( $subres = $this->rx( '/[^\{\[]+/' ) ) !== FALSE) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return FALSE; }
}


/* breaks: /[\{\[]+/ */
protected $match_breaks_typestack = array('breaks');
function match_breaks ($stack = array()) {
	$matchrule = "breaks"; $result = $this->construct($matchrule, $matchrule, null);
	if (( $subres = $this->rx( '/[\{\[]+/' ) ) !== FALSE) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return FALSE; }
}


/* ws: (' ' | /[\n\t\r]/ ) */
protected $match_ws_typestack = array('ws');
function match_ws ($stack = array()) {
	$matchrule = "ws"; $result = $this->construct($matchrule, $matchrule, null);
	$_17 = NULL;
	do {
		$_15 = NULL;
		do {
			$res_12 = $result;
			$pos_12 = $this->pos;
			if (substr($this->string,$this->pos,1) == ' ') {
				$this->pos += 1;
				$result["text"] .= ' ';
				$_15 = TRUE; break;
			}
			$result = $res_12;
			$this->pos = $pos_12;
			if (( $subres = $this->rx( '/[\n\t\r]/' ) ) !== FALSE) {
				$result["text"] .= $subres;
				$_15 = TRUE; break;
			}
			$result = $res_12;
			$this->pos = $pos_12;
			$_15 = FALSE; break;
		}
		while(0);
		if( $_15 === FALSE) { $_17 = FALSE; break; }
		$_17 = TRUE; break;
	}
	while(0);
	if( $_17 === TRUE ) { return $this->finalise($result); }
	if( $_17 === FALSE) { return FALSE; }
}


/* castext: ( item:texcasblock | item:rawcasblock | item:misc | item:ws | item:breaks )* */
protected $match_castext_typestack = array('castext');
function match_castext ($stack = array()) {
	$matchrule = "castext"; $result = $this->construct($matchrule, $matchrule, null);
	while (true) {
		$res_37 = $result;
		$pos_37 = $this->pos;
		$_36 = NULL;
		do {
			$_34 = NULL;
			do {
				$res_19 = $result;
				$pos_19 = $this->pos;
				$matcher = 'match_'.'texcasblock'; $key = $matcher; $pos = $this->pos;
				$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
				if ($subres !== FALSE) {
					$this->store( $result, $subres, "item" );
					$_34 = TRUE; break;
				}
				$result = $res_19;
				$this->pos = $pos_19;
				$_32 = NULL;
				do {
					$res_21 = $result;
					$pos_21 = $this->pos;
					$matcher = 'match_'.'rawcasblock'; $key = $matcher; $pos = $this->pos;
					$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
					if ($subres !== FALSE) {
						$this->store( $result, $subres, "item" );
						$_32 = TRUE; break;
					}
					$result = $res_21;
					$this->pos = $pos_21;
					$_30 = NULL;
					do {
						$res_23 = $result;
						$pos_23 = $this->pos;
						$matcher = 'match_'.'misc'; $key = $matcher; $pos = $this->pos;
						$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
						if ($subres !== FALSE) {
							$this->store( $result, $subres, "item" );
							$_30 = TRUE; break;
						}
						$result = $res_23;
						$this->pos = $pos_23;
						$_28 = NULL;
						do {
							$res_25 = $result;
							$pos_25 = $this->pos;
							$matcher = 'match_'.'ws'; $key = $matcher; $pos = $this->pos;
							$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
							if ($subres !== FALSE) {
								$this->store( $result, $subres, "item" );
								$_28 = TRUE; break;
							}
							$result = $res_25;
							$this->pos = $pos_25;
							$matcher = 'match_'.'breaks'; $key = $matcher; $pos = $this->pos;
							$subres = ( $this->packhas( $key, $pos ) ? $this->packread( $key, $pos ) : $this->packwrite( $key, $pos, $this->$matcher(array_merge($stack, array($result))) ) );
							if ($subres !== FALSE) {
								$this->store( $result, $subres, "item" );
								$_28 = TRUE; break;
							}
							$result = $res_25;
							$this->pos = $pos_25;
							$_28 = FALSE; break;
						}
						while(0);
						if( $_28 === TRUE ) { $_30 = TRUE; break; }
						$result = $res_23;
						$this->pos = $pos_23;
						$_30 = FALSE; break;
					}
					while(0);
					if( $_30 === TRUE ) { $_32 = TRUE; break; }
					$result = $res_21;
					$this->pos = $pos_21;
					$_32 = FALSE; break;
				}
				while(0);
				if( $_32 === TRUE ) { $_34 = TRUE; break; }
				$result = $res_19;
				$this->pos = $pos_19;
				$_34 = FALSE; break;
			}
			while(0);
			if( $_34 === FALSE) { $_36 = FALSE; break; }
			$_36 = TRUE; break;
		}
		while(0);
		if( $_36 === FALSE) {
			$result = $res_37;
			$this->pos = $pos_37;
			unset( $res_37 );
			unset( $pos_37 );
			break;
		}
	}
	return $this->finalise($result);
}




}
?>

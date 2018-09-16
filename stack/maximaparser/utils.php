<?php

require_once("maximaParser.php");

class maxima_parser_utils {

    // Parses a string of Maxima code to an AST tree for use elsewhere.
    static function parse(string $code): MP_Root {
            $parser = new MP_Parser();
            return $parser->parse($code);
    }

  // takes a raw tree and the matching source code and remaps the positions from char to line:linechar
  // use when you need to have pretty printted position data.
  static function position_remap(MP_Node $ast, string $code, array $limits = null) {
      if ($limits === null) {
          $limits = array();
          foreach (explode("\n", $code) as $line) {
              $limits[] = strlen($line) + 1;
          }
      }

      $c = $ast->position['start'];
      $l = 1;
      foreach ($limits as $ll) {
          if ($c < $ll) {
              break;
          } else {
              $c -= $ll;
              $l += 1;
          }
      }
      $c += 1;
      $ast->position['start'] = "$l:$c";
      $c = $ast->position['end'];
      $l = 1;
      foreach ($limits as $ll) {
          if ($c < $ll) {
              break;
          } else {
              $c -= $ll;
              $l += 1;
          }
      }
      $c += 1;
      $ast->position['end'] = "$l:$c";
      foreach ($ast->getChildren() as $node) {
          maxima_parser_utils::position_remap($node, $code, $limits);
      }

      return $ast;
  }

  // takes a raw tree and drops the comments sections from it.
  static function strip_comments(MP_Root $ast) {
      // for now comments exist only at the top level and there are no "inline"
      // comments within statements, hopefully at some point we can go further
      $nitems = array();
      foreach ($ast->items as $node) {
          if ($node instanceof MP_Comment) {
              continue;
          } else {
              $nitems[] = $node;
          }
      }
      if (count($nitems) !== count($ast->items)) {
          $ast->items = $nitems;
      }

      return $ast;
  }
}


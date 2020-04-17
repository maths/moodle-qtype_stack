<?php
// @codingStandardsIgnoreFile

require 'autoloader.php';

use hafriedlander\Peg\Compiler;

// Just in case this matters.
/// https://github.com/hafriedlander/php-peg/issues/39
if (php_sapi_name() != "cli") {
  die("Maybe bad...");
}

Compiler::cli( $_SERVER['argv'] ) ;

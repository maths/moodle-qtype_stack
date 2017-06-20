<?php
error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', 1);
ini_set('html_errors', 1);

require_once(__DIR__ . '/../config.php');

require_once(__DIR__ . '/../api/api.php');
require_once(__DIR__ . '/../question.php');

$api = new qtype_stack_api();

// Run this command once at install time to compile Maxima on your machine.
$api->install();


<?php

define('CLI_SCRIPT', true);
require(__DIR__ . '/../../../../config.php');
require_once($CFG->libdir . '/clilib.php');
require_once(__DIR__ . '/../stack/cas/casstring.class.php');

require_once(__DIR__ . '/../stack/utils.class.php');

// This is for testing phase...
$compare_mode = true;
if ($compare_mode) {
  require_once(__DIR__ . '/../stack/cas/casstring.class.old.php');
}

$CFG->debug = (E_ALL | E_STRICT);


// Now get cli options.
list($options, $unrecognized) = cli_get_params(array('help'=>false, 'string' => '1+2x'),
    array('h'=>'help'));
if ($unrecognized) {
    $unrecognized = implode("\n  ", $unrecognized);
    cli_error(get_string('cliunknowoption', 'admin', $unrecognized));
}
if ($options['help']) {
    $help =
        "Test the casstring validation with a given string. Goes through most options.

          --string=\"1+2x\"
        ";
    echo $help;
    die;
}

$teststring = '1+2x';

if (isset($options['string'])) {
    $teststring = $options['string'];
}

cli_heading('= testing = ' . $teststring . '=');

$results = array();

$difference = false;
$keydifference = false;

// Generate...
for ($insertstars = 0; $insertstars < 6; $insertstars++) {
    $heading1 = 'insertstars = ' . $insertstars;
    foreach (array('s', 't') as $security) {
        $heading2 = $heading1 . ' security = ' . $security;
        foreach (array(true, false) as $syntax) {
            $heading3 = $heading2 . ' syntax = ' . ($syntax?'true':'false');
            foreach (array(true, false) as $units) {
                $str = $teststring;
                if ($security === 's') {
                  $str = stack_utils::logic_nouns_sort($str, 'add');
                }
                $heading = $heading3 . ' units = ' . ($units?'true':'false');
                $output = array();
                $cs = new stack_cas_casstring($str);
                $cs->set_units($units);
                $cs2 = false;
                if ($compare_mode) {
                  $cs2 = new stack_cas_casstring_old($str);
                  $cs2->set_units($units);
                }
                $valid = $cs->get_valid($security, $syntax, $insertstars);
                $output[] = 'valid      : ' . ($valid?'true':'false');
                if ($compare_mode && $valid !== $cs2->get_valid($security, $syntax, $insertstars)) {
                    $output[count($output) - 1] .= ' (MISSMATCH WITH OLD)';
                    $difference = true;
                    $keydifference = true;
                }
                if ($cs->get_errors() !== '') {
                    $output[] = 'errors     : ' . trim($cs->get_errors());
                }
                if ($compare_mode && (trim($cs2->get_errors()) !== trim($cs->get_errors()))) {
                    $output[] = 'errors(OLD): ' . trim($cs2->get_errors());
                    $difference = true;
                }
                if ($cs->get_answernote() !== '') {
                    $output[] = 'note       : ' . $cs->get_answernote();
                }
                if ($compare_mode && (trim($cs2->get_answernote()) !== trim($cs->get_answernote()))) {
                    $output[] = 'note (OLD) : ' . trim($cs2->get_answernote());
                    $difference = true;
                }
                $output[] = 'key        : ' . $cs->get_key();
                if ($compare_mode && (trim($cs2->get_key()) !== trim($cs->get_key()))) {
                    $output[] = 'key (OLD)  : ' . trim($cs2->get_key());
                    $difference = true;
                }
                $output[] = 'value      : ' . $cs->get_casstring();
                if ($compare_mode && (trim($cs2->get_casstring()) !== trim($cs->get_casstring()))) {
                    $output[] = 'value (OLD): ' . trim($cs2->get_casstring());
                    $difference = true;
                }
                if ($cs->ast !== null) {
                    $output[] = $cs->ast->debugPrint($teststring);
                }

                $results[$heading] = implode("\n", $output);
            }
        }
    }
}

// Group.
$distinct = array_unique($results);
foreach ($distinct as $val) {
    foreach ($results as $heading => $result) {
        if ($result == $val) {
            cli_heading($heading);
        }
    }
    cli_writeln($val);
}
if ($compare_mode) {
  if ($difference) {
    cli_writeln('NOTE! the old and new validations provided different output.');
  }
  if ($keydifference) {
    cli_writeln('NOTE! A difference in validity between old and new was seen.');
  }
}

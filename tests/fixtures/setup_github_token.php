<?php
// This file is part of Moodle - http://moodle.org/
// Moodle is free software under the GNU GPL v3 or later.

/**
 * Encrypt the disposable CI token in PHPUnit's Moodle instance.
 * @package qtype_stack
 * @copyright 2026 Oleksandr Kulkov
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
if (PHP_SAPI !== 'cli' || $argc !== 3) {
    exit(1);
}
$projectroot = realpath($argv[1]);
$moodleroot = $projectroot;
require_once($moodleroot . '/vendor/autoload.php');
if (is_file($moodleroot . '/public/lib/phpunit/bootstrap.php')) {
    $moodleroot .= '/public';
}
require_once($moodleroot . '/lib/phpunit/bootstrap.php');
require_once($CFG->libdir . '/adminlib.php');
// Keep the disposable key outside the directories PHPUnit clears.
$CFG->secretdataroot = dirname($argv[2]) . '/stack-github-key';
umask(0077);
if (!is_dir($CFG->secretdataroot) && !mkdir($CFG->secretdataroot, 0700, true)) {
    throw new RuntimeException('Could not create the disposable encryption-key directory.');
}
chmod($CFG->secretdataroot, 0700);
$token = trim(stream_get_contents(STDIN));
if ($token === '' || preg_match('/[\r\n]/', $token)) {
    throw new RuntimeException('A nonempty single-line CI token is required on stdin.');
}
$setting = new admin_setting_encryptedpassword('qtype_stack/githubtoken', 'GitHub token', '');
if ($setting->write_setting($token) !== '') {
    throw new RuntimeException('Could not store the encrypted CI credential.');
}
$encrypted = $DB->get_field('config_plugins', 'value', ['plugin' => 'qtype_stack', 'name' => 'githubtoken'], MUST_EXIST);
// Load the encrypted configuration after core bootstrap, then capture it in the reset baseline.
$config = "<?php\nrequire_once " . var_export($moodleroot . '/lib/phpunit/bootstrap.php', true) . ";\n"
    . '$CFG->secretdataroot = ' . var_export($CFG->secretdataroot, true) . ";\n"
    . "\$CFG->forced_plugin_settings['qtype_stack']['githubtoken'] = " . var_export($encrypted, true) . ";\n"
    . "phpunit_util::bootstrap_init();\n";
umask(0077);
if (file_put_contents($argv[2], $config, LOCK_EX) === false) {
    throw new RuntimeException('Could not write the CI credential bootstrap.');
}
// Keep the generated XML beside Moodle's original so all relative paths retain their meaning.
$xml = new DOMDocument();
$xml->load($projectroot . '/phpunit.xml');
$xml->documentElement->setAttribute('bootstrap', realpath($argv[2]));
if ($xml->save($projectroot . '/phpunit-stack-github.xml') === false) {
    throw new RuntimeException('Could not write the CI PHPUnit configuration.');
}
unset($token);

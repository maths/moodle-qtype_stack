<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

namespace qtype_stack;

defined('MOODLE_INTERNAL') || die();
global $CFG;
require_once($CFG->libdir . '/adminlib.php');
require_once(__DIR__ . '/../stack/questionlibrary.class.php');

/**
 * Encrypted credentials shared by CLI and web requests.
 * @package qtype_stack
 * @copyright 2026 Oleksandr Kulkov
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \stack_question_library
 */
final class github_admin_token_test extends \advanced_testcase {
    public function test_setting_storage_rotation_and_removal(): void {
        global $DB, $CFG;
        $this->resetAfterTest();
        unset($CFG->forced_plugin_settings['qtype_stack']['githubtoken']);
        $this->setAdminUser();
        $settings = new \admin_settingpage('stacktokenfixture', 'STACK');
        require(__DIR__ . '/../settings.php');
        $setting = $settings->settings->qtype_stackgithubtoken;
        $cache = \cache::make('qtype_stack', 'librarycache');
        foreach (['first-pat-secret', 'second-pat-secret', ''] as $token) {
            $previous = $setting->get_setting();
            $cache->set('private-list', 'cached');
            $this->assertSame('', $setting->write_setting($token));
            $this->assertTrue($setting->post_write_settings($previous));
            $this->assertFalse($cache->get('private-list'));
            $stored = get_config('qtype_stack', 'githubtoken');
            if ($token !== '') {
                $this->assertNotSame($token, $stored);
                $this->assertSame($token, \core\encryption::decrypt($stored));
                $this->assertStringNotContainsString($token, $setting->output_html($stored, ''));
                foreach ($DB->get_records('config_log', ['plugin' => 'qtype_stack', 'name' => 'githubtoken']) as $log) {
                    $this->assertStringNotContainsString($token, $log->value . $log->oldvalue);
                }
            } else {
                $this->assertSame('', $stored);
            }
            $headers = $this->web_headers($stored);
            $this->assertSame(
                $token === '' ? [] : ['Authorization: Bearer ' . $token],
                array_values(array_filter($headers, static fn($h) => str_starts_with($h, 'Authorization:')))
            );
        }
    }

    public function test_cli_uses_encrypted_setting(): void {
        global $CFG;
        $this->resetAfterTest();
        unset($CFG->forced_plugin_settings['qtype_stack']['githubtoken']);
        set_config('githubtoken', \core\encryption::encrypt('site-pat-secret'), 'qtype_stack');
        $method = new \ReflectionMethod(\stack_question_library::class, 'github_headers');
        $this->assertContains('Authorization: Bearer site-pat-secret', $method->invoke(null));
    }

    public function test_library_setting_and_catalogue_changes_clear_cache(): void {
        global $CFG;
        $this->resetAfterTest();
        $this->setAdminUser();
        $catalogue = ['bank' => ['name' => 'Bank', 'url' => 'https://github.com/example/questions']];
        set_config('libraries', json_encode($catalogue), 'qtype_stack');
        $settings = new \admin_settingpage('stacktokenfixture', 'STACK');
        require(__DIR__ . '/../settings.php');
        $name = \stack_question_library::github_token_setting('bank');
        $setting = $settings->settings->{'qtype_stack' . $name};
        $cache = \cache::make('qtype_stack', 'librarycache');
        foreach (['library-secret', 'replacement-secret', ''] as $token) {
            $previous = $setting->get_setting();
            $cache->set('cached-private-file', 'content');
            $this->assertSame('', $setting->write_setting($token));
            $this->assertTrue($setting->post_write_settings($previous));
            $this->assertFalse($cache->get('cached-private-file'));
            $stored = get_config('qtype_stack', $name);
            $this->assertSame($token, $stored === '' ? '' : \core\encryption::decrypt($stored));
        }
        $setting = $settings->settings->qtype_stacklibraries;
        $previous = $setting->get_setting();
        $catalogue['bank']['name'] = 'Renamed bank';
        $cache->set('cached-private-file', 'content');
        $this->assertSame('', $setting->write_setting(json_encode($catalogue)));
        $this->assertTrue($setting->post_write_settings($previous));
        $this->assertFalse($cache->get('cached-private-file'));
    }

    /**
     * The CI bootstrap includes ciphertext in PHPUnit's reset baseline.
     */
    public function test_ci_credential_survives_resets(): void {
        global $CFG;
        $baseline = $CFG->forced_plugin_settings['qtype_stack']['githubtoken'] ?? '';
        if ($baseline === '') {
            $this->markTestSkipped('Run with the CI encrypted credential fixture to check reset persistence.');
        }
        $this->resetAfterTest();
        $method = new \ReflectionMethod(\stack_question_library::class, 'github_headers');
        $before = $method->invoke(null);
        $this->assertCount(3, $before);
        $this->assertTrue(\core\encryption::decrypt($baseline) !== $baseline);
        for ($i = 0; $i < 2; $i++) {
            unset($CFG->forced_plugin_settings['qtype_stack']['githubtoken']);
            set_config('githubtoken', '', 'qtype_stack');
            $this->assertCount(2, $method->invoke(null));
            \phpunit_util::reset_all_data();
            // Avoid including real credentials in assertion failure output.
            $this->assertTrue($baseline === get_config('qtype_stack', 'githubtoken'));
            $this->assertTrue($before === $method->invoke(null));
        }
    }

    public function test_encrypted_header_injection_is_not_sent(): void {
        $this->resetAfterTest();
        $this->assertCount(2, $this->web_headers(\core\encryption::encrypt("bad\r\nheader")));
    }

    /**
     * The GitHub headers a web request would send, computed in a child PHP process.
     *
     * Real encryption/decryption; the child only supplies configuration without bootstrapping a web session.
     *
     * @param string $encrypted the encrypted token the child reads as the githubtoken setting.
     * @return array the headers.
     */
    private function web_headers(string $encrypted): array {
        global $CFG;
        $script = 'define("MOODLE_INTERNAL", true); define("CLI_SCRIPT", false); define("PHPUNIT_TEST", true);'
            . '$CFG = (object)' . var_export(['dataroot' => $CFG->dataroot,
                'secretdataroot' => $CFG->secretdataroot ?? $CFG->dataroot . '/secret'], true) . ';'
            . 'function get_config($component, $name) { return ' . var_export($encrypted, true) . '; }'
            . 'require ' . var_export($CFG->libdir . '/classes/encryption.php', true) . ';'
            . 'require ' . var_export(__DIR__ . '/../stack/questionlibrary.class.php', true) . ';'
            . 'putenv("STACK_GITHUB_TOKEN=ignored-server-secret");'
            . '$method = new ReflectionMethod("stack_question_library", "github_headers");'
            . 'echo json_encode($method->invoke(null));';
        $process = proc_open([PHP_BINARY, '-r', $script], [1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes);
        $this->assertIsResource($process);
        $output = stream_get_contents($pipes[1]);
        $errors = stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        $this->assertSame(0, proc_close($process), $errors);
        return json_decode($output, true, 512, JSON_THROW_ON_ERROR);
    }
}

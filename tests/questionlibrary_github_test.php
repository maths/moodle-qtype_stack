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

/**
 * Tests for GitHub authentication and failure reporting.
 *
 * @package qtype_stack
 * @copyright 2026 Oleksandr Kulkov
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace qtype_stack;

defined('MOODLE_INTERNAL') || die();
require_once(__DIR__ . '/../stack/questionlibrary.class.php');
require_once(__DIR__ . '/../locallib.php');
require_once(__DIR__ . '/../stack/utils.class.php');

/**
 * GitHub request tests without external network access.
 *
 * @covers \stack_question_library
 */
final class questionlibrary_github_test extends \advanced_testcase {
    /**
     * Authentication is optional and rejects invalid header values.
     */
    public function test_optional_token(): void {
        global $CFG;
        $this->resetAfterTest();
        unset($CFG->forced_plugin_settings['qtype_stack']['githubtoken']);
        $method = new \ReflectionMethod(\stack_question_library::class, 'github_headers');
        foreach (['test-token', '', "invalid\r\nheader"] as $token) {
            set_config('githubtoken', $token === '' ? '' : \core\encryption::encrypt($token), 'qtype_stack');
            $headers = $method->invoke(null);
            if ($token === 'test-token') {
                $this->assertContains('Authorization: Bearer test-token', $headers);
            } else {
                $this->assertCount(2, $headers);
            }
        }
    }

    /**
     * A web entry point must ignore even a valid environment credential.
     */
    public function test_web_requests_ignore_environment_token(): void {
        $script = 'define("MOODLE_INTERNAL", true); define("CLI_SCRIPT", false);'
            . 'require ' . var_export(__DIR__ . '/../stack/questionlibrary.class.php', true) . ';'
            . 'function get_config($component, $name) { return false; }'
            . 'putenv("STACK_GITHUB_TOKEN=server-secret");'
            . '$method = new \ReflectionMethod(\stack_question_library::class, "github_headers");'
            . 'echo json_encode($method->invoke(null));';
        $pipes = [];
        $process = proc_open([PHP_BINARY, '-r', $script], [1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes);
        $this->assertIsResource($process);
        $output = stream_get_contents($pipes[1]);
        $errors = stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        $this->assertSame(0, proc_close($process), $errors);
        $this->assertSame(['User-Agent: Moodle-STACK', 'Accept: application/vnd.github.v3+json'],
            json_decode($output, true));
    }

    /**
     * Unsupported origins are rejected before executing any request, with or without a token.
     */
    public function test_invalid_urls_are_rejected(): void {
        global $CFG;
        $this->resetAfterTest();
        unset($CFG->forced_plugin_settings['qtype_stack']['githubtoken']);
        $library = new class extends \stack_question_library {
            /**
             * A rejected URL must never create an HTTP client.
             *
             * @return \curl HTTP client
             */
            protected static function github_client(): \curl {
                throw new \LogicException('Unexpected network request.');
            }
        };
        $method = new \ReflectionMethod($library, 'request_github');
        foreach (['', 'test-token'] as $token) {
            set_config('githubtoken', $token === '' ? '' : \core\encryption::encrypt($token), 'qtype_stack');
            $urls = [
                'http://api.github.com/repos/example',
                'https://api.github.com.example.org/repos/example',
                'https://example.org/api.github.com',
                'https://api.github.com:8443/repos/example',
                'https://user:password@api.github.com/repos/example',
                'not a URL',
            ];
            foreach ($urls as $url) {
                try {
                    $method->invoke(null, $url);
                    $this->fail('Unsupported URL was accepted.');
                } catch (\stack_exception $exception) {
                    $this->assertStringContainsString('HTTPS api.github.com URL', $exception->getMessage());
                    $this->assertStringNotContainsString($url, $exception->getMessage());
                }
            }
        }
    }

    /**
     * Failed listings retain safe diagnostics rather than the remote response body.
     */
    public function test_listing_failure_diagnostics(): void {
        $library = $this->failed_library();
        [$result, $files] = $library::list_github_repo('https://github.com/maths/moodle-qtype_stack/tree/master');
        $this->assertSame([], $files);
        $this->assertStringContainsString('HTTP 403, cURL error 0, x-ratelimit-remaining=0', $result->error);
        $this->assertStringNotContainsString('sensitive response', $result->error);
    }

    /**
     * Failed downloads include the same diagnostics.
     */
    public function test_download_failure_diagnostics(): void {
        $library = $this->failed_library();
        $this->expectException(\stack_exception::class);
        $this->expectExceptionMessage('File unavailable (HTTP 403, cURL error 0, x-ratelimit-remaining=0).');
        $library::get_external_github_file('https://api.github.com/repos/example/git/blobs/example');
    }

    /**
     * Create a library with a deterministic failed response.
     *
     * @return \stack_question_library Library stub
     */
    private function failed_library(): \stack_question_library {
        return new class extends \stack_question_library {
            /**
             * Return a failed response without making a network request.
             *
             * @param string $url Request URL
             * @param int $timeout Request timeout in seconds
             * @return array Response body, status and diagnostics
             */
            protected static function request_github(string $url, int $timeout = 0, string $setting = 'githubtoken'): array {
                return ['sensitive response', 403, 'HTTP 403, cURL error 0, x-ratelimit-remaining=0',
                    ['x-ratelimit-remaining' => '0']];
            }
        };
    }
}

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
final class questionlibrary_github_test extends \basic_testcase {
    /**
     * Authentication is optional and rejects invalid header values.
     */
    public function test_optional_token(): void {
        $previous = getenv('STACK_GITHUB_TOKEN');
        try {
            putenv('STACK_GITHUB_TOKEN=test-token');
            $method = new \ReflectionMethod(\stack_question_library::class, 'github_headers');
            $headers = $method->invoke(null);
            $this->assertContains('Authorization: Bearer test-token', $headers);
            putenv('STACK_GITHUB_TOKEN');
            $this->assertCount(2, $method->invoke(null));
            putenv("STACK_GITHUB_TOKEN=invalid\r\nheader");
            $this->assertCount(2, $method->invoke(null));
        } finally {
            putenv($previous === false ? 'STACK_GITHUB_TOKEN' : 'STACK_GITHUB_TOKEN=' . $previous);
        }
    }

    /**
     * Unsupported origins are rejected before executing any request, with or without a token.
     */
    public function test_invalid_urls_are_rejected(): void {
        $previous = getenv('STACK_GITHUB_TOKEN');
        $library = new class extends \stack_question_library {
            /**
             * A rejected URL must never reach the transport.
             *
             * @param resource $ch cURL handle
             * @return string Response body
             */
            protected static function execute_curl_request($ch) {
                throw new \LogicException('Unexpected network request.');
            }
        };
        $method = new \ReflectionMethod($library, 'request_github');
        try {
            foreach (['', 'test-token'] as $token) {
                putenv('STACK_GITHUB_TOKEN=' . $token);
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
        } finally {
            putenv($previous === false ? 'STACK_GITHUB_TOKEN' : 'STACK_GITHUB_TOKEN=' . $previous);
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
            protected static function request_github(string $url, int $timeout = 0): array {
                return ['sensitive response', 403, 'HTTP 403, cURL error 0, x-ratelimit-remaining=0'];
            }
        };
    }
}

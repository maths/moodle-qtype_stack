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
     * Authentication is restricted to the GitHub API origin.
     */
    public function test_token_is_scoped(): void {
        $previous = getenv('STACK_GITHUB_TOKEN');
        try {
            putenv('STACK_GITHUB_TOKEN=test-token');
            $method = new \ReflectionMethod(\stack_question_library::class, 'github_headers');
            $headers = $method->invoke(null, 'https://api.github.com/repos/maths/moodle-qtype_stack');
            $this->assertContains('Authorization: Bearer test-token', $headers);
            foreach (
                [
                'http://api.github.com/repos/example',
                'https://api.github.com.example.org/repos/example',
                'https://example.org/api.github.com',
                'https://api.github.com:8443/repos/example',
                'https://user@api.github.com/repos/example',
                ] as $url
            ) {
                $this->assertCount(2, $method->invoke(null, $url), $url);
            }
            putenv('STACK_GITHUB_TOKEN');
            $this->assertCount(2, $method->invoke(null, 'https://api.github.com/repos/example'));
            putenv("STACK_GITHUB_TOKEN=invalid\r\nheader");
            $this->assertCount(2, $method->invoke(null, 'https://api.github.com/repos/example'));
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

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
require_once($CFG->libdir . '/filelib.php');
require_once(__DIR__ . '/../stack/questionlibrary.class.php');

/**
 * Moodle HTTP wrapper integration without external requests.
 * @package qtype_stack
 * @copyright 2026 Oleksandr Kulkov
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \stack_question_library
 */
final class github_http_client_test extends \advanced_testcase {
    public function test_headers_and_request_options(): void {
        global $CFG;
        $this->resetAfterTest();
        unset($CFG->forced_plugin_settings['qtype_stack']['githubtoken']);
        set_config('githubtoken', \core\encryption::encrypt('dummy-token'), 'qtype_stack');
        $client = new class extends \curl {
            /** @var array headers set on the client. */
            public array $sent = [];
            /** @var array options passed to get(). */
            public array $receivedoptions = [];
            /** @var int HTTP status to report. */
            public int $status = 403;
            /** @var int cURL error number to report. */
            public int $errornumber = 0;
            /** @var array raw response header lines fed to Moodle's header parser. */
            public array $lines = ["HTTP/1.1 403 Forbidden\r\n", "X-RateLimit-Remaining: 0\r\n",
                "x-ratelimit-reset: 12345\r\n", "Retry-After: 60\r\n", "Set-Cookie: secret\r\n", "\r\n"];
            /**
             * Record the headers instead of setting them.
             *
             * @param array $header the headers.
             */
            public function setHeader($header) { // phpcs:ignore moodle.NamingConventions.ValidFunctionName.LowercaseMethod
                $this->sent = $header;
            }
            /**
             * Record the options and parse the canned headers, without a network request.
             *
             * @param string $url URL to fetch.
             * @param array $params request parameters.
             * @param array $options request options.
             * @return string the canned response body.
             */
            public function get($url, $params = [], $options = []) {
                $this->receivedoptions = $options;
                // Exercise Moodle's own header parser, including duplicate and mixed-case headers.
                $parser = new \ReflectionMethod(\curl::class, 'formatHeader');
                $parser->setAccessible(true);
                foreach ($this->lines as $line) {
                    $parser->invoke($this, null, $line);
                }
                return 'response';
            }
            /**
             * The canned request information.
             *
             * @return array
             */
            public function get_info() {
                return ['http_code' => $this->status];
            }
            /**
             * The canned cURL error number.
             *
             * @return int
             */
            public function get_errno() {
                return $this->errornumber;
            }
        };
        $library = new class extends \stack_question_library {
            /** @var \curl the client to return. */
            public static \curl $client;
            /**
             * The test client.
             *
             * @return \curl
             */
            protected static function github_client(): \curl {
                return self::$client;
            }
        };
        $library::$client = $client;
        $request = new \ReflectionMethod($library, 'request_github');
        [$body, $status, $details, $headers] = $request->invoke(null, 'https://api.github.com/repos/a/b', 10);
        $this->assertSame('response', $body);
        $this->assertSame(403, $status);
        $this->assertSame(['x-ratelimit-remaining' => '0', 'x-ratelimit-reset' => '12345', 'retry-after' => '60'], $headers);
        $this->assertStringNotContainsString('secret', $details);
        $this->assertContains('Authorization: Bearer dummy-token', $client->sent);
        $this->assertSame(false, $client->receivedoptions['CURLOPT_FOLLOWLOCATION']);
        $this->assertSame(true, $client->receivedoptions['CURLOPT_SSL_VERIFYPEER']);
        $this->assertSame(2, $client->receivedoptions['CURLOPT_SSL_VERIFYHOST']);
        $this->assertSame(10, $client->receivedoptions['CURLOPT_TIMEOUT']);
        $this->assertSame(300, $client->receivedoptions['CURLOPT_CONNECTTIMEOUT']);
        $client->lines = ["HTTP/1.1 403 Forbidden\r\n", "X-RateLimit-Remaining: invalid\r\n",
            "Retry-After: 60\r\n", "Retry-After: 120\r\n", "\r\n"];
        $result = $request->invoke(null, 'https://api.github.com/repos/a/b');
        $this->assertSame([], $result[3]);
        $this->assertSame('HTTP 403, cURL error 0', $result[2]);
        foreach ([[0, 0], [0, 28]] as [$client->status, $client->errornumber]) {
            $result = $request->invoke(null, 'https://api.github.com/repos/a/b');
            $this->assertFalse($result[0]);
        }
    }

    public function test_moodle_blocklist_is_respected(): void {
        global $CFG;
        $this->resetAfterTest();
        $CFG->curlsecurityblockedhosts = 'api.github.com';
        $request = new \ReflectionMethod(\stack_question_library::class, 'request_github');
        $result = $request->invoke(null, 'https://api.github.com/repos/a/b');
        $this->assertDebuggingCalled('Blocked https://api.github.com/repos/a/b: The URL is blocked. [user 0]');
        $this->assertFalse($result[0]);
        $this->assertSame(0, $result[1]);
    }
}

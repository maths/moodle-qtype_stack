<?php
// This file is part of Moodle - http://moodle.org/
// Moodle is free software under the GNU GPL v3 or later.
namespace qtype_stack;

defined('MOODLE_INTERNAL') || die();
require_once(__DIR__ . '/../stack/questionlibrary.class.php');
require_once(__DIR__ . '/../locallib.php');
require_once(__DIR__ . '/../stack/utils.class.php');

/**
 * Library credential selection, fallback and download consistency.
 * @package qtype_stack
 * @copyright 2026 Oleksandr Kulkov
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \stack_question_library
 */
final class github_library_token_test extends \advanced_testcase {
    private const URL = 'https://github.com/example/questions/tree/main';
    private const BLOB = 'https://api.github.com/repos/example/questions/git/blobs/abc';

    protected function setUp(): void {
        parent::setUp();
        global $CFG;
        $this->resetAfterTest();
        unset($CFG->forced_plugin_settings['qtype_stack']['githubtoken']);
        set_config('libraries', json_encode(['bank' => ['name' => 'Bank', 'url' => self::URL]]), 'qtype_stack');
    }

    private function token(string $setting, string $value): void {
        set_config($setting, $value === '' ? '' : \core\encryption::encrypt($value), 'qtype_stack');
    }

    private function transport(): \stack_question_library {
        return new class extends \stack_question_library {
            public static array $responses = [];
            public static array $headers = [];
            protected static function request_github(string $url, int $timeout = 0, string $setting = 'githubtoken'): array {
                self::$headers[] = parent::github_headers($setting);
                if (!self::$responses) {
                    throw new \LogicException('Unexpected request.');
                }
                return array_shift(self::$responses);
            }
        };
    }

    private function listing(): array {
        return [json_encode(['tree' => [['path' => 'q.xml', 'type' => 'blob', 'url' => self::BLOB]]]), 200, '', []];
    }

    public function test_access_failures_retry_default_and_pin_downloads(): void {
        $specific = \stack_question_library::github_token_setting('bank');
        $this->token($specific, 'bank-token');
        $this->token('githubtoken', 'default-token');
        foreach ([401, 403, 404] as $status) {
            $library = $this->transport();
            $library::$headers = [];
            $library::$responses = [['access denied', $status, 'HTTP ' . $status, []], $this->listing()];
            [, $files] = $library::list_github_repo(self::URL, 'bank');
            $this->assertContains('Authorization: Bearer bank-token', $library::$headers[0]);
            $this->assertContains('Authorization: Bearer default-token', $library::$headers[1]);
            $this->assertSame('githubtoken', $files['q.xml']->githubcredential['setting']);
            $library::$responses = [[json_encode(['content' => base64_encode('<quiz/>'), 'encoding' => 'base64']), 200, '']];
            $this->assertSame('<quiz/>', $library::get_external_github_file(self::BLOB, $files['q.xml']->githubcredential));
            $this->assertContains('Authorization: Bearer default-token', $library::$headers[2]);
            $this->assertStringNotContainsString('default-token', json_encode($files));
        }
    }

    public function test_non_access_failures_never_retry(): void {
        $this->token(\stack_question_library::github_token_setting('bank'), 'bank-token');
        $this->token('githubtoken', 'default-token');
        foreach ([
            [false, 0, 'timeout', []],
            ['error', 500, 'HTTP 500', []],
            ['error', 429, 'HTTP 429', []],
            ['error', 403, 'HTTP 403', ['x-ratelimit-remaining' => '0']],
            ['error', 403, 'HTTP 403', ['retry-after' => '60']],
            ['You have exceeded a secondary rate limit', 403, 'HTTP 403', []],
        ] as $response) {
            $library = $this->transport();
            $library::$headers = [];
            $library::$responses = [$response];
            [$error, $files] = $library::list_github_repo(self::URL, 'bank');
            $this->assertCount(1, $library::$headers);
            $this->assertSame([], $files);
            $this->assertStringContainsString($response[2], $error->error);
        }
    }

    public function test_optional_tokens_and_no_duplicate_retry(): void {
        $specific = \stack_question_library::github_token_setting('bank');
        foreach ([['', '', null], ['', 'general', 'general'], ['special', '', 'special'],
                ['same', 'same', 'same'], ['special', 'general', 'special']] as [$bank, $default, $expected]) {
            $this->token($specific, $bank);
            $this->token('githubtoken', $default);
            $library = $this->transport();
            $library::$headers = [];
            $library::$responses = [$this->listing()];
            [, $files] = $library::list_github_repo(self::URL, 'bank');
            $this->assertCount(1, $library::$headers);
            $this->assertArrayHasKey('q.xml', $files);
            $this->assertSame($expected ? ['Authorization: Bearer ' . $expected] : [],
                array_values(array_filter($library::$headers[0], static fn($h) => str_starts_with($h, 'Authorization:'))));
            if ($bank === $default || $default === '') {
                $library::$headers = [];
                $library::$responses = [['denied', 401, 'HTTP 401', []]];
                [, $files] = $library::list_github_repo(self::URL, 'bank');
                $this->assertCount(1, $library::$headers);
                $this->assertSame([], $files);
            }
        }
    }

    public function test_default_failure_stops_after_second_request(): void {
        $this->token(\stack_question_library::github_token_setting('bank'), 'bank');
        $this->token('githubtoken', 'default');
        $library = $this->transport();
        $library::$headers = [];
        $library::$responses = [['denied', 401, 'HTTP 401', []], ['denied', 404, 'HTTP 404', []]];
        [$error, $files] = $library::list_github_repo(self::URL, 'bank');
        $this->assertCount(2, $library::$headers);
        $this->assertSame([], $files);
        $this->assertStringContainsString('HTTP 404', $error->error);
    }

    public function test_download_cannot_use_another_repository(): void {
        $library = $this->transport();
        $library::$responses = [];
        $this->expectException(\stack_exception::class);
        $library::get_external_github_file('https://api.github.com/repos/other/private/git/blobs/abc',
            ['repository' => 'https://api.github.com/repos/example/questions', 'setting' => 'githubtoken']);
    }

    public function test_library_secret_is_not_selected_for_another_url(): void {
        $library = $this->transport();
        $library::$responses = [];
        $this->expectException(\stack_exception::class);
        $library::list_github_repo('https://github.com/other/private', 'bank');
    }

    public function test_download_failure_does_not_switch_listing_credential(): void {
        $specific = \stack_question_library::github_token_setting('bank');
        $this->token($specific, 'bank-token');
        $this->token('githubtoken', 'default-token');
        $library = $this->transport();
        $library::$headers = [];
        $library::$responses = [$this->listing()];
        [, $files] = $library::list_github_repo(self::URL, 'bank');
        $this->assertSame($specific, $files['q.xml']->githubcredential['setting']);
        $library::$responses = [['denied', 403, 'HTTP 403', []]];
        try {
            $library::get_external_github_file(self::BLOB, $files['q.xml']->githubcredential);
            $this->fail('Expected download failure.');
        } catch (\stack_exception $exception) {
            $this->assertCount(2, $library::$headers);
            $this->assertContains('Authorization: Bearer bank-token', $library::$headers[1]);
            $this->assertStringContainsString('HTTP 403', $exception->getMessage());
        }
    }
}

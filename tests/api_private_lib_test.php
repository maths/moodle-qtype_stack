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

/**
 * Unit tests for private demo helpers.
 *
 * @package    qtype_stack
 * @copyright  2026 University of Edinburgh
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 * phpcs:disable PSR1.Classes.ClassDeclaration.MultipleClasses
 */

namespace qtype_stack;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../api/private-demo/lib.php');

/**
 * Minimal response body double.
 */
class api_private_demo_test_body {
    /** @var string Written body content. */
    public $contents = '';

    // phpcs:ignore moodle.Commenting.MissingDocblock.MissingTestcaseMethodDescription
    public function write($text): void {
        $this->contents .= $text;
    }
}

/**
 * Minimal response double.
 */
class api_private_demo_test_response {
    /** @var int HTTP status. */
    public $status = 200;

    /** @var array HTTP headers. */
    public $headers = [];

    /** @var api_private_demo_test_body Response body. */
    private $body;

    // phpcs:ignore moodle.Commenting.MissingDocblock.MissingTestcaseMethodDescription
    public function __construct() {
        $this->body = new api_private_demo_test_body();
    }

    // phpcs:ignore moodle.Commenting.MissingDocblock.MissingTestcaseMethodDescription,moodle.NamingConventions.ValidFunctionName.LowercaseMethod
    public function getBody() {
        return $this->body;
    }

    // phpcs:ignore moodle.Commenting.MissingDocblock.MissingTestcaseMethodDescription,moodle.NamingConventions.ValidFunctionName.LowercaseMethod
    public function withStatus($status) {
        $clone = clone $this;
        $clone->status = $status;
        return $clone;
    }

    // phpcs:ignore moodle.Commenting.MissingDocblock.MissingTestcaseMethodDescription,moodle.NamingConventions.ValidFunctionName.LowercaseMethod
    public function withHeader($name, $value) {
        $clone = clone $this;
        $clone->headers[$name] = $value;
        return $clone;
    }
}

/**
 * Minimal request double.
 */
class api_private_demo_test_request {
    /** @var string Request body. */
    private $body;

    // phpcs:ignore moodle.Commenting.MissingDocblock.MissingTestcaseMethodDescription
    public function __construct($body) {
        $this->body = $body;
    }

    // phpcs:ignore moodle.Commenting.MissingDocblock.MissingTestcaseMethodDescription,moodle.NamingConventions.ValidFunctionName.LowercaseMethod
    public function getBody() {
        return $this->body;
    }
}

/**
 * Unit tests for private demo helpers.
 *
 * @group qtype_stack
 * @covers \qtype_stack
 */
final class api_private_lib_test extends \advanced_testcase {
    public function test_json_and_text_responses_write_body_status_and_content_type(): void {
        $jsonresponse = \stack_private_demo_json_response(new api_private_demo_test_response(), ['ok' => true], 201);
        $this->assertSame(201, $jsonresponse->status);
        $this->assertSame('application/json;charset=UTF-8', $jsonresponse->headers['Content-Type']);
        $this->assertSame('{"ok":true}', $jsonresponse->getBody()->contents);

        $textresponse = \stack_private_demo_text_response(new api_private_demo_test_response(), 'plain text', 202);
        $this->assertSame(202, $textresponse->status);
        $this->assertSame('text/plain;charset=UTF-8', $textresponse->headers['Content-Type']);
        $this->assertSame('plain text', $textresponse->getBody()->contents);
    }

    public function test_resolve_file_accepts_only_files_beneath_root(): void {
        $parent = __DIR__;

        $this->assertSame(
            realpath($parent . '/api_private_lib_test.php'),
            \stack_private_demo_resolve_file($parent, '/api_private_lib_test.php')
        );
        $this->assertFalse(\stack_private_demo_resolve_file($parent, '../version.php'));
        $this->assertFalse(\stack_private_demo_resolve_file($parent, 'sub'));
        $this->assertFalse(\stack_private_demo_resolve_file(false, '/api_private_lib_test.php'));
    }

    public function test_question_reference_requires_exactly_one_identifier(): void {
        $this->assertSame(['questionId' => 'q_123'], \stack_private_demo_question_reference([
            'questionId' => ' q_123 ',
        ]));
        $this->assertSame(['questionPath' => 'topic/question.xml'], \stack_private_demo_question_reference([
            'questionPath' => ' topic/question.xml ',
        ]));

        try {
            \stack_private_demo_question_reference([]);
            $this->fail('Expected an exception for a missing question reference.');
        } catch (\stack_private_demo_http_exception $exception) {
            $this->assertSame(400, $exception->get_status());
            $this->assertSame('Exactly one of questionId or questionPath is required.', $exception->getMessage());
        }

        try {
            \stack_private_demo_question_reference(['questionId' => 'q_123', 'questionPath' => 'topic/question.xml']);
            $this->fail('Expected an exception for two question references.');
        } catch (\stack_private_demo_http_exception $exception) {
            $this->assertSame(400, $exception->get_status());
        }
    }

    public function test_request_json_rejects_invalid_json_and_question_definition(): void {
        $this->assertSame(['questionId' => 'q_123'], \stack_private_demo_request_json(
            new api_private_demo_test_request('{"questionId":"q_123"}')
        ));

        try {
            \stack_private_demo_request_json(new api_private_demo_test_request('not json'));
            $this->fail('Expected an exception for invalid JSON.');
        } catch (\stack_private_demo_http_exception $exception) {
            $this->assertSame('Expected a JSON object.', $exception->getMessage());
        }

        try {
            \stack_private_demo_request_json(new api_private_demo_test_request('{"questionDefinition":"<quiz></quiz>"}'));
            $this->fail('Expected an exception for questionDefinition.');
        } catch (\stack_private_demo_http_exception $exception) {
            $this->assertSame('questionDefinition is not accepted by this demo.', $exception->getMessage());
        }
    }

    public function test_embed_seed_sequence_uses_all_deployed_seeds_or_comma_list(): void {
        $quiz = simplexml_load_string(
            '<quiz><question type="stack"><deployedseed>11</deployedseed><deployedseed>22</deployedseed></question></quiz>'
        );

        $this->assertNull(\stack_private_demo_embed_seed_sequence([], $quiz));
        $this->assertSame([11, 22], \stack_private_demo_embed_seed_sequence(['seeds' => 'all'], $quiz));
        $this->assertSame([3, 5, 8], \stack_private_demo_embed_seed_sequence(['seeds' => '3, 5,8'], $quiz));
    }

    public function test_question_definition_from_path_rejects_invalid_and_missing_paths(): void {
        try {
            \stack_private_demo_question_definition_from_path('/absolute.xml');
            $this->fail('Expected an exception for an absolute path.');
        } catch (\stack_private_demo_http_exception $exception) {
            $this->assertSame(400, $exception->get_status());
            $this->assertSame('Invalid question path.', $exception->getMessage());
        }

        try {
            \stack_private_demo_question_definition_from_path('missing.xml');
            $this->fail('Expected an exception for a missing path.');
        } catch (\stack_private_demo_http_exception $exception) {
            $this->assertSame(404, $exception->get_status());
            $this->assertSame('Question file is not available.', $exception->getMessage());
        }
    }
}

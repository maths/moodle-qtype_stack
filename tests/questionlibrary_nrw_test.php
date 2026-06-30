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
 * Unit tests for the STACK NRW library integration.
 *
 * @package    qtype_stack
 * @copyright  2026 The University of Edinburgh
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * phpcs:disable PSR1.Classes.ClassDeclaration.MultipleClasses
 */

namespace qtype_stack;

use advanced_testcase;
use stack_question_library;

defined('MOODLE_INTERNAL') || die();
global $CFG;
require_once($CFG->dirroot . '/question/type/stack/stack/utils.class.php');
require_once($CFG->dirroot . '/question/type/stack/locallib.php');
require_once($CFG->dirroot . '/question/type/stack/stack/questionlibrary.class.php');

/**
 * Test double for stack_question_library NRW API calls.
 */
class stack_question_library_nrw_testable extends \stack_question_library {
    /** @var array mocked curl_exec responses */
    private static array $mockresponses = [];
    /** @var array mocked CURLINFO_HTTP_CODE values */
    private static array $mockhttpcodes = [];

    /**
     * Prime mock values consumed by wrapped curl methods.
     *
     * @param array $responses Values returned by execute_curl_request.
     * @param array $httpcodes Values returned by get_curl_info.
     * @return void
     */
    public static function set_mock_curl_responses(array $responses, array $httpcodes): void {
        self::$mockresponses = $responses;
        self::$mockhttpcodes = $httpcodes;
    }

    /**
     * Wrapper for curl_exec used by class under test.
     *
     * @param resource $ch cURL handle
     * @return string|bool
     */
    protected static function execute_curl_request($ch) {
        if (!self::$mockresponses) {
            throw new \RuntimeException('Missing mocked curl_exec response.');
        }
        return array_shift(self::$mockresponses);
    }

    /**
     * Wrapper for curl_getinfo used by class under test.
     *
     * @param resource $ch cURL handle
     * @param int $opt cURL info option
     * @return mixed
     */
    protected static function get_curl_info($ch, int $opt) {
        if ($opt !== CURLINFO_HTTP_CODE) {
            return parent::get_curl_info($ch, $opt);
        }
        if (!self::$mockhttpcodes) {
            throw new \RuntimeException('Missing mocked curl_getinfo HTTP code.');
        }
        return array_shift(self::$mockhttpcodes);
    }
}

/**
 * Tests of NRW search/download/upload behavior.
 *
 * @group qtype_stack
 * @covers \stack_question_library::list_nrw_search
 * @covers \stack_question_library::get_external_nrw_file
 * @covers \stack_question_library::upload_nrw_question
 */
final class questionlibrary_nrw_test extends advanced_testcase {
    public function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
        stack_question_library_nrw_testable::set_mock_curl_responses([], []);
    }

    public function test_list_nrw_search_returns_results(): void {
        $payload = [
            'results' => [
                [
                    'question' => [
                        'id' => 'atlas-1',
                        'data' => [
                            'title' => 'Question 1',
                            'description' => [['', 'Description 1']],
                            'license' => 'CC-BY',
                            'source' => 'Source 1',
                            'subject' => ['Algebra', 'Calculus'],
                        ],
                    ],
                ],
                [
                    'question' => [
                        'id' => 'atlas-2',
                        'data' => [
                            'title' => 'Question 2',
                            'description' => [['', 'Description 2']],
                            'license' => 'CC0',
                            'source' => 'Source 2',
                            'subject' => 'Geometry',
                        ],
                    ],
                ],
            ],
        ];
        stack_question_library_nrw_testable::set_mock_curl_responses([json_encode($payload)], [200]);

        [$files, $flat] = stack_question_library_nrw_testable::list_nrw_search([
            'search' => 'integration',
            'apikey' => 'secret',
        ]);

        $this->assertEquals('.', $files->label);
        $this->assertEquals(1, $files->isdirectory);
        $this->assertCount(2, $files->children);
        $this->assertEquals('Question 1', $files->children[0]->label);
        $this->assertEquals('Description 1', $files->children[0]->description);
        $this->assertEquals('CC-BY', $files->children[0]->license);
        $this->assertEquals('Source 1', $files->children[0]->source);
        $this->assertEquals('Algebra, Calculus', $files->children[0]->subject);
        $this->assertEquals('atlas-1', $files->children[0]->path);
        $this->assertEquals('Geometry', $files->children[1]->subject);
        $this->assertFalse(property_exists($files, 'error'));
        $this->assertEquals([], $flat);
    }

    public function test_get_file_list_from_repo_routes_to_nrw_search(): void {
        stack_question_library_nrw_testable::set_mock_curl_responses([
            json_encode([
                'results' => [[
                    'question' => [
                        'id' => 'atlas-1',
                        'data' => [
                            'title' => 'Question 1',
                            'description' => 'Description 1',
                            'license' => 'CC-BY',
                            'source' => 'Source 1',
                            'subject' => 'Algebra',
                        ],
                    ],
                ]],
            ]),
        ], [200]);

        [$files, $flat] = stack_question_library_nrw_testable::get_file_list_from_repo(
            ['search' => 'algebra', 'apikey' => 'secret'],
            stack_question_library::NRWSEARCH
        );

        $this->assertCount(1, $files->children);
        $this->assertEquals('atlas-1', $files->children[0]->path);
        $this->assertEquals([], $flat);
    }

    public function test_list_nrw_search_sets_error_for_empty_results(): void {
        stack_question_library_nrw_testable::set_mock_curl_responses([json_encode(['results' => []])], [200]);

        [$files, $flat] = stack_question_library_nrw_testable::list_nrw_search([
            'search' => 'none',
            'apikey' => 'secret',
        ]);

        $this->assertEquals(stack_string('stack_library_nothing'), $files->error);
        $this->assertEquals([], $files->children);
        $this->assertEquals([], $flat);
    }

    public function test_list_nrw_search_sets_error_for_invalid_payload(): void {
        stack_question_library_nrw_testable::set_mock_curl_responses([json_encode(['foo' => 'bar'])], [200]);

        [$files, $flat] = stack_question_library_nrw_testable::list_nrw_search([
            'search' => 'invalid',
            'apikey' => 'secret',
        ]);

        $this->assertEquals(stack_string('stack_library_connection_error'), $files->error);
        $this->assertEquals([], $files->children);
        $this->assertEquals([], $flat);
    }

    public function test_list_nrw_search_sets_error_for_http_failure(): void {
        stack_question_library_nrw_testable::set_mock_curl_responses(['upstream error'], [500]);

        [$files, $flat] = stack_question_library_nrw_testable::list_nrw_search([
            'search' => 'fail',
            'apikey' => 'secret',
        ]);

        $expected = stack_string('stack_library_connection_error') . ': upstream error';
        $this->assertEquals($expected, $files->error);
        $this->assertEquals([], $files->children);
        $this->assertEquals([], $flat);
    }

    public function test_list_nrw_search_sets_error_for_curl_false_response(): void {
        stack_question_library_nrw_testable::set_mock_curl_responses([false], [0]);

        [$files] = stack_question_library_nrw_testable::list_nrw_search([
            'search' => 'timeout',
            'apikey' => 'secret',
        ]);

        $this->assertEquals(stack_string('stack_library_connection_error'), $files->error);
    }

    public function test_get_external_nrw_file_returns_xml(): void {
        stack_question_library_nrw_testable::set_mock_curl_responses([
            json_encode(['xml' => '<question id="1"></question>']),
        ], [200]);

        $result = stack_question_library_nrw_testable::get_external_nrw_file('atlas-1', 'secret');
        $this->assertEquals('<question id="1"></question>', $result);
    }

    public function test_get_external_nrw_file_throws_for_http_error(): void {
        stack_question_library_nrw_testable::set_mock_curl_responses(['server error'], [500]);

        $this->expectException(\stack_exception::class);
        $this->expectExceptionMessage('File unavailable.');
        stack_question_library_nrw_testable::get_external_nrw_file('atlas-1', 'secret');
    }

    public function test_get_external_nrw_file_throws_for_invalid_json(): void {
        stack_question_library_nrw_testable::set_mock_curl_responses(['not json'], [200]);

        $this->expectException(\stack_exception::class);
        $this->expectExceptionMessage('Invalid JSON.');
        stack_question_library_nrw_testable::get_external_nrw_file('atlas-1', 'secret');
    }

    public function test_upload_nrw_question_requires_api_key(): void {
        $result = stack_question_library_nrw_testable::upload_nrw_question(new \stdClass(), '');

        $this->assertTrue($result['iserror']);
        $this->assertEquals(stack_string('nrwuploadapikeymissing'), $result['message']);
    }

    public function test_upload_nrw_question_handles_curl_failure(): void {
        $questiondata = $this->create_stack_question_data();
        stack_question_library_nrw_testable::set_mock_curl_responses([false], [0]);

        $result = stack_question_library_nrw_testable::upload_nrw_question($questiondata, 'secret');

        $this->assertTrue($result['iserror']);
        $this->assertStringStartsWith(stack_string('nrwuploadfailed') . ':', $result['message']);
    }

    public function test_upload_nrw_question_handles_non_json_response(): void {
        $questiondata = $this->create_stack_question_data();
        stack_question_library_nrw_testable::set_mock_curl_responses(['not json'], [502]);

        $result = stack_question_library_nrw_testable::upload_nrw_question($questiondata, 'secret');

        $this->assertTrue($result['iserror']);
        $this->assertEquals(
            stack_string('nrwuploadfailed') . ' HTTP 502: not json',
            $result['message']
        );
    }

    public function test_upload_nrw_question_returns_success_for_created_question(): void {
        $questiondata = $this->create_stack_question_data();
        stack_question_library_nrw_testable::set_mock_curl_responses([
            json_encode(['status' => 'created', 'id' => 'ATLAS-123']),
        ], [201]);

        $result = stack_question_library_nrw_testable::upload_nrw_question($questiondata, 'secret');

        $this->assertTrue($result['issuccess']);
        $this->assertEquals(stack_string('nrwuploadcreated', 'ATLAS-123'), $result['message']);
    }

    public function test_upload_nrw_question_returns_warning_for_duplicate(): void {
        $questiondata = $this->create_stack_question_data();
        stack_question_library_nrw_testable::set_mock_curl_responses([
            json_encode(['status' => 'duplicate', 'id' => 'ATLAS-999']),
        ], [201]);

        $result = stack_question_library_nrw_testable::upload_nrw_question($questiondata, 'secret');

        $this->assertTrue($result['iswarning']);
        $this->assertEquals(stack_string('nrwuploadduplicate', 'ATLAS-999'), $result['message']);
    }

    public function test_upload_nrw_question_returns_validation_error_for_http_400(): void {
        $questiondata = $this->create_stack_question_data();
        stack_question_library_nrw_testable::set_mock_curl_responses([
            json_encode(['detail' => ['error_message' => 'Unexpected tag <foo>.']]),
        ], [400]);

        $result = stack_question_library_nrw_testable::upload_nrw_question($questiondata, 'secret');

        $this->assertTrue($result['iserror']);
        $this->assertEquals(
            stack_string('nrwuploadvalidationerror') . ' Unexpected tag <foo>.',
            $result['message']
        );
    }

    public function test_upload_nrw_question_returns_generic_error_for_non_201_non_400_json_response(): void {
        $questiondata = $this->create_stack_question_data();
        $response = json_encode(['status' => 'forbidden']);
        stack_question_library_nrw_testable::set_mock_curl_responses([$response], [403]);

        $result = stack_question_library_nrw_testable::upload_nrw_question($questiondata, 'secret');

        $this->assertTrue($result['iserror']);
        $this->assertEquals(
            stack_string('nrwuploadfailed') . ' HTTP 403: ' . $response,
            $result['message']
        );
    }

    /**
     * Create realistic question data suitable for qformat_xml export.
     *
     * @return object
     */
    private function create_stack_question_data(): object {
        $this->setAdminUser();
        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $category = $generator->create_question_category();
        $question = $generator->create_question('stack', 'test3', ['category' => $category->id]);
        return \question_bank::load_question_data($question->id);
    }
}

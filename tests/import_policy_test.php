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
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle. If not, see <http://www.gnu.org/licenses/>.

namespace qtype_stack;

defined('MOODLE_INTERNAL') || die();
global $CFG;
require_once($CFG->dirroot . '/question/format/xml/format.php');
require_once(__DIR__ . '/fixtures/test_base.php');

/**
 * Initial XML import must reject authoring errors unless explicitly imported for repair.
 *
 * @package qtype_stack
 * @copyright 2026 Oleksandr Kulkov
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \qtype_stack::import_from_xml
 * @covers \qtype_stack::save_question_options
 */
final class import_policy_test extends \qtype_stack_testcase {
    /**
     * Exercise the actual initial-upload pipeline, including the write stage.
     *
     * @dataProvider initial_import_cases
     * @param bool $stoponerror Whether authoring errors must abort the upload.
     * @param bool $valid Whether the XML contains its required validation placeholder.
     * @param bool $structural Whether the XML has duplicate input definitions.
     * @param bool $mixed Prepend a valid question to verify the whole upload is rejected.
     */
    public function test_initial_upload_policy(bool $stoponerror, bool $valid, bool $structural = false, bool $mixed = false): void {
        global $DB, $PAGE;
        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course();
        $quiz = $this->getDataGenerator()->create_module('quiz', ['course' => $course->id]);
        $context = \context_module::instance($quiz->cmid);
        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $category = $generator->create_question_category(['contextid' => $context->id]);
        $PAGE->set_context($context);
        $PAGE->set_pagetype('question-bank-importquestions-import');
        $xml = '<quiz><question type="stack"><name><text>Initial upload</text></name>
            <questiontext format="html"><text>[[input:ans1]]' .
            ($valid ? ' [[validation:ans1]]' : '') . '</text></questiontext>
            <questionvariables><text>ta1:[[1,true],[2,false]];</text></questionvariables>
            <specificfeedback format="html"><text></text></specificfeedback>
            <questionnote><text>Dropdown choice</text></questionnote>
            <input><name>ans1</name><type>dropdown</type><tans>ta1</tans>
            <mustverify>0</mustverify><showvalidation>0</showvalidation></input>
            </question></quiz>';
        if ($mixed) {
            $validquestion = str_replace('[[input:ans1]]', '[[input:ans1]] [[validation:ans1]]',
                substr($xml, strlen('<quiz>'), -strlen('</quiz>')));
            $xml = str_replace('<quiz>', '<quiz>' . $validquestion, $xml);
        }
        if ($structural) {
            preg_match('~<input>.*?</input>~s', $xml, $match);
            $xml = str_replace('</question>', $match[0] . '</question>', $xml);
        }
        $file = make_request_directory() . '/initial.xml';
        file_put_contents($file, $xml);
        $format = new \qformat_xml();
        $format->displayprogress = true;
        $format->setCategory($category);
        $format->setCourse($course);
        $format->setContexts([$context]);
        $format->setFilename($file);
        $format->setStoponerror($stoponerror);
        $before = $DB->count_records('question');
        $versions = $DB->count_records('question_versions');
        ob_start();
        try {
            try {
                $result = $format->importprocess();
                $exception = null;
            } catch (\stack_exception $error) {
                $result = false;
                $exception = $error;
            }
            $output = ob_get_contents();
        } finally {
            ob_end_clean();
        }
        if ($structural) {
            $this->assertNotNull($exception);
            $this->assertSame($before, $DB->count_records('question'));
            $this->assertSame($versions, $DB->count_records('question_versions'));
            return;
        }
        $this->assertNull($exception);
        if (!$valid && $stoponerror) {
            $this->assertFalse($result);
            $this->assertSame($before, $DB->count_records('question'));
            $this->assertSame($versions, $DB->count_records('question_versions'));
            $this->assertEmpty($format->questionids);
        } else {
            $this->assertTrue($result);
            $this->assertCount(1, $format->questionids);
            $id = reset($format->questionids);
            $this->assertEquals($valid ? 'ready' : 'draft',
                $DB->get_field('question_versions', 'status', ['questionid' => $id]));
            $this->assertEquals($valid ? 0 : 1,
                $DB->get_field('qtype_stack_options', 'isbroken', ['questionid' => $id]));
        }
        if (!$valid) {
            $this->assertGreaterThan(0, $format->importerrors);
            $this->assertStringContainsString('[[validation:ans1]]', $output);
        }
    }

    /** @return array Upload policy and validity combinations. */
    public static function initial_import_cases(): array {
        return [
            'invalid default' => [true, false],
            'invalid explicit repair import' => [false, false],
            'valid default' => [true, true],
            'valid with override' => [false, true],
            'structural errors cannot be forced' => [false, true, true],
            'mixed upload rejected before first write' => [true, false, false, true],
        ];
    }
}

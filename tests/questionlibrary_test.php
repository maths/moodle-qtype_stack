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
 * Unit tests for create repo command line script for gitsync
 *
 * @package    qtype_stack
 * @copyright  2023 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace qtype_stack;

defined('MOODLE_INTERNAL') || die();
global $CFG;
require_once(__DIR__ . '/fixtures/test_base.php');
require_once($CFG->dirroot . '/question/type/stack/stack/questionlibrary.class.php');
require_once($CFG->dirroot . '/question/type/stack/api/util/StackQuestionLoader.php');
use qtype_stack_testcase;
use stack_question_library;
use api\util\StackQuestionLoader;

/**
 * Test the class for the question library page.
 * @group qtype_stack
 *
 * @covers \qtype_stack\stack_question_library::class
 */
final class questionlibrary_test extends qtype_stack_testcase {
    /** @var \stdClass generated question object */
    protected string $filepath = 'Calculus-Refresher/CR_Diff_01/CR-Diff-01-basic-1-e.xml';
    /**
     * Test question render.
     */
    public function test_render_question(): void {
        global $CFG;
        $qcontents = file_get_contents($CFG->dirroot . '/question/type/stack/samplequestions/stacklibrary/' . $this->filepath);
        $question = StackQuestionLoader::loadxml($qcontents)['question'];
        $qrender = stack_question_library::render_question($question);
        $this->assertStringContainsString('<div class="formulation"><span class="filter_mathjaxloader_equation">' .
            'Differentiate <span class="nolink">\({x}^{-7}\)</span>', $qrender);
        $this->assertStringContainsString('<input type="text" name="stack_temp_R" id="stack_temp_R" size="16.5" ' .
            'style="width: 13.6em" autocapitalize="none" spellcheck="false" class="algebraic" value="" ' .
            'data-stack-input-type="algebraic" data-stack-input-decimal-separator="." data-stack-input-list-separator="," />',
            $qrender);
    }
    /**
     * Test get file list.
     */
    public function test_get_file_list(): void {
        global $CFG;
        $files = stack_question_library::get_file_list($CFG->dirroot . '/question/type/stack/samplequestions/stacklibrary/*');
        $folder = null;
        foreach ($files->children as $currentfolder) {
            if ($currentfolder->label === 'Calculus-Refresher') {
                $folder = $currentfolder;
                break;
            }
        }
        $this->assertEquals(1, $folder->isdirectory);
        $this->assertEquals(17, count($folder->children));
        $subfolder = null;
        foreach ($folder->children as $currentfolder) {
            if ($currentfolder->label === 'CR_Diff_02') {
                $subfolder = $currentfolder;
                break;
            }
        }
        $this->assertEquals(1, $subfolder->isdirectory);
        $this->assertEquals(17, count($subfolder->children));

        $file = null;
        foreach ($subfolder->children as $currentfile) {
            if ($currentfile->label === 'CR-Diff-02-linearity-1-e.xml') {
                $file = $currentfile;
                break;
            }
        }
        $this->assertEquals(0, $file->isdirectory);
        $this->assertStringContainsString('stacklibrary/Calculus-Refresher' .
            '/CR_Diff_02/CR-Diff-02-linearity-1-e.xml', $file->path);
    }
}

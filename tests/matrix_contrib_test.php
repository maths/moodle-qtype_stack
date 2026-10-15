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
 * Compatibility of the matrix package with cores which do not define its functions.
 *
 * @package qtype_stack
 * @copyright 2026 Oleksandr Kulkov
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

namespace qtype_stack;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/fixtures/test_base.php');
require_once(__DIR__ . '/../stack/cas/keyval.class.php');
require_once(__DIR__ . '/../stack/cas/secure_loader.class.php');

/**
 * The downloadable package must work without relying on the new core definitions.
 *
 * @group qtype_stack
 * @covers \stack_cas_keyval
 */
final class matrix_contrib_test extends \qtype_stack_testcase {
    /**
     * Compile the include and restore its functions after removing the core copies.
     */
    public function test_package_without_core_definitions(): void {
        $functions = [
            'aug', 'de_aug', 'triu', 'tril', 'diagonal', 'diag_entries',
            'setrowmx', 'setcolmx', 'setdiagmx', 'triup', 'trilp', 'REFp',
            'diagonalizablep', 'symp', 'invertiblep', '_orthogonal_check_matrix',
            'orthogonal_columnsp', 'orthonormal_columnsp', 'orth_matrixp',
            'diagmatrix_like', 'rowscale', 'columnscale', 'format_as_coeff', 'disp_eqns', 'mat_disp_eqns',
        ];
        $available = 'map(lambda([fn],member(fn,map(safe_op,functions))),["' .
            implode('","', $functions) . '"])';
        foreach ([true, false] as $simplify) {
            $options = new \stack_options();
            $options->set_option('simplify', $simplify);
            $keyval = new \stack_cas_keyval('stack_include("contribl://matrix.mac");', $options, 123);
            $this->assertTrue($keyval->get_valid(), implode('; ', $keyval->get_errors()));
            $compiled = $keyval->compile('matrix compatibility');
            $remove = new \stack_secure_loader(
                'kill(' . implode(',', $functions) . ',aug_matrix)',
                'remove new core definitions'
            );
            $before = new \stack_secure_loader_value($available, 'before include');
            $include = new \stack_secure_loader($compiled['statement'], 'compiled matrix include');
            $after = new \stack_secure_loader_value($available, 'after include');
            $operations = new \stack_secure_loader_value(
                '[triup(ident(2)),is(setrowmx([9,8],1,ident(2))=matrix([9,8],[0,1])),' .
                'orth_matrixp(ident(2)),' .
                'is(de_aug(aug(ident(2),c(1,2)))=matrix([1,0,1],[0,1,2])),' .
                'stringp(mat_disp_eqns(matrix([1,2]),matrix([3]),[x,y]))]',
                'matrix operations'
            );
            $display = new \stack_secure_loader_value('tex1(aug(ident(2),c(1,2)))', 'augmented display');
            $session = new \stack_cas_session2(
                [$remove, $before, $include, $after, $operations, $display],
                $options,
                123
            );
            $session->instantiate();
            $this->assertEquals(
                '[' . implode(',', array_fill(0, count($functions), 'false')) . ']',
                $before->get_value()->toString()
            );
            $this->assertEquals(
                '[' . implode(',', array_fill(0, count($functions), 'true')) . ']',
                $after->get_value()->toString()
            );
            $this->assertEquals('[true,true,true,true,true]', $operations->get_value()->toString());
            $this->assertStringContainsString('\\\\left|', $display->get_value()->toString());
            $this->assertEmpty($session->get_errors());
        }
    }

    /**
     * Included functions must survive question compilation, validation and PRT sessions.
     */
    public function test_package_in_question(): void {
        $question = \test_question_maker::make_question('stack', 'test0');
        $question->questionvariables = 'stack_include("contribl://matrix.mac");' .
            'a:de_aug(aug(ident(2),matrix([3],[4])));';
        $question->questiontext = '{@aug(a)@} [[input:ans1]] [[validation:ans1]]';
        $question->inputs['ans1'] = \stack_input_factory::make('matrix', 'ans1', 'a', null, ['mustVerify' => false]);
        $nodes = $question->prts['firsttree']->get_nodes();
        $nodes[0]->sans = 'diag_entries(ans1)';
        $nodes[0]->tans = '[1,1]';
        $nodes[0]->answertest = 'AlgEquiv';
        $question->prts['firsttree']->set_nodes($nodes);
        $this->assertEquals('', $question->validate_against_stackversion(\context_system::instance()));
        $question->start_attempt(new \question_attempt_step(), 1);
        foreach (['matrix([1,0,3],[0,1,4])' => 1, 'matrix([1,0,3],[0,2,4])' => 0] as $answer => $score) {
            $response = $question->inputs['ans1']->maxima_to_response_array($answer);
            $this->assertTrue($question->is_gradable_response($response));
            $this->assertEquals($score, $question->grade_response($response)[0]);
        }
    }
}

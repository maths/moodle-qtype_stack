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
 * Add description here!
 * @package    qtype_stack
 * @copyright  2024 University of Edinburgh.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

namespace qtype_stack;

use castext2_evaluatable;
use qtype_stack_testcase;
use stack_ast_container;
use stack_cas_keyval;
use stack_cas_session2;
use stack_options;
use stack_secure_loader;
use stack_cas_castext2_latex;

defined('MOODLE_INTERNAL') || die();

// We run tests using STACKs CAS-sessions.
require_once(__DIR__ . '/fixtures/test_base.php');

// Current requirements are these, if changed update the mapping function.
require_once(__DIR__ . '/../stack/cas/castext2/castext2_evaluatable.class.php');
require_once(__DIR__ . '/../stack/cas/cassession2.class.php');
require_once(__DIR__ . '/../stack/cas/keyval.class.php');
require_once(__DIR__ . '/../stack/cas/secure_loader.class.php');
require_once(__DIR__ . '/../stack/cas/ast.container.class.php');
require_once(__DIR__ . '/../stack/options.class.php');

/**
 * This set of tests tests the behaviour of a CASText implementation.
 *
 * It can also be interpreted as the functional declaration of CASText.
 *
 * @group qtype_stack
 * @group qtype_stack_castext_module
 * @covers \qtype_stack
 */
final class castext2_test extends qtype_stack_testcase {

    // This function maps a given set of CASText code, CASString
    // style preamble statements and STACK options to the current
    // implementation and generates the end result.
    // Validation is not being tested, here.
    // phpcs:ignore moodle.Commenting.MissingDocblock.MissingTestcaseMethodDescription
    private function evaluate(string $code, array $preamble=[], ?stack_options $options=null,
            $context='castext-test-case'): string {
        $statements = [];
        foreach ($preamble as $statement) {
            $statements[] = stack_ast_container::make_from_teacher_source($statement, 'castext-test-case');
        }
        $result = castext2_evaluatable::make_from_source($code, $context);
        $statements[] = $result;
        $session = new stack_cas_session2($statements, $options);

        $session->instantiate();

        $this->assertTrue($session->get_valid());
        $this->assertEquals('', $session->get_errors());

        return $result->get_rendered();
    }

    /**
     *
     * LaTeX-injection "{@value@}" functional requirements:
     *  1. Must result in LaTeX code representing the value given.
     *     Note! Finer details of this need additional tests but are not
     *     really CASText related issues. e.g. extra parentheses.
     *  2. Must allow references to previous code. Both within CASText
     *     and outside.
     *  3. Must support statement level overriding of simplification.
     *  4. Must follow global simplification otherwise.
     *  5. If injected outside LaTeX math-mode must wrap the generated
     *     code to inline math delimiters, otherwise no wrapping.
     *  6. When injecting within math-mode wraps result in extra braces.
     *  7. "string" values are outputted as they are.
     *
     * @covers \qtype_stack\stack_cas_castext2_latex
     */
    public function test_latex_injection_1(): void {

        $input = '{@1+2@}, \[{@sqrt(2)@}\]';
        $output = '\({3}\), \[{\sqrt{2}}\]';
        $this->assertEquals($output, $this->evaluate($input));
    }

    /**
     * Add description here.
     * @covers \qtype_stack\stack_cas_castext2_latex
     */
    public function test_latex_injection_2(): void {

        $input = '{@a@}, {@c:b@}, {@3/9,simp=false@}, {@c@}, {@d@}';
        // Note that last one if we are in global simp:true we just cannot know
        // whether that needs to be protected.
        $preamble = ['a:3/9', 'b:sqrt(2)', 'd:3/9'];
        $output = '\({\frac{1}{3}}\), \({\sqrt{2}}\), \({\frac{3}{9}}\), \({\sqrt{2}}\), \({\frac{1}{3}}\)';
        $this->assertEquals($output, $this->evaluate($input, $preamble));
    }

    /**
     * Add description here.
     * @covers \qtype_stack\stack_cas_castext2_latex
     */
    public function test_latex_injection_3(): void {

        $input = '{@a@}, {@3/9@}, {@3/9,simp@}, {@a,simp=false@}, {@a,simp@}';
        $preamble = ['a:3/9'];
        $output = '\({\frac{3}{9}}\), \({\frac{3}{9}}\), \({\frac{1}{3}}\), \({\frac{3}{9}}\), \({\frac{1}{3}}\)';
        $options = new stack_options(['simplify' => false]);
        $this->assertEquals($output, $this->evaluate($input, $preamble, $options));
    }

    /**
     * Add description here.
     * @covers \qtype_stack\stack_cas_castext2_latex
     */
    public function test_latex_injection_4(): void {

        $input = ' {@"test string"@} ';
        $output = ' test string ';
        $this->assertEquals($output, $this->evaluate($input));
    }

    /**
     * Add description here.
     * @covers \qtype_stack\stack_cas_castext2_latex
     */
    public function test_latex_injection_5(): void {

        // Issue #849, let the simplification state stay after injection if
        // modified globally.
        // While this is a bad way to do things and is essenttialy authoring with
        // side effects that might be hard to spot it has been done in the past
        // and we need to support it still.
        // Note that we will not support conditionally changing the `simp` inside
        // an injection. If that needs to be done use the `[[if]]` block combined
        // to `[[define]]`.
        $input = '[[define simp="true"/]]{@a@}, {@(simp:false,a)@}, {@a@}';
        $preamble = ['a:3/9'];
        $output = '\({\frac{1}{3}}\), \({\frac{3}{9}}\), \({\frac{3}{9}}\)';
        $options = new stack_options(['simplify' => false]);
        $this->assertEquals($output, $this->evaluate($input, $preamble, $options));
    }

    /**
     * Add description here.
     * @covers \qtype_stack\stack_cas_castext2_latex
     * @covers \qtype_stack\stack_cas_castext2_markdownformat
     * @covers \qtype_stack\castext2_parser_utils::math_paint
     */
    public function test_latex_injection_mixed_formats_1(): void {

        // The default format is raw HTML.
        // The actual injection is not visible here as the markdown gets rendered, but
        // the math-mode detection should be.
        // Note that Markdown escape rules change if the line is a HTML-block.
        $input = '[[markdownformat]]\\\\\\({@sqrt(x)@}\\\\\\) {@sqrt(x)@}[[/markdownformat]] {@sqrt(x)@} ' .
            '[[markdownformat]]<p>\({@sqrt(y)@}\) {@sqrt(y)@}</p>[[/markdownformat]]';
        $output = '<p>\({\sqrt{x}}\) \({\sqrt{x}}\)</p>' . "\n " .
            '\({\sqrt{x}}\) <p>\({\sqrt{y}}\) \({\sqrt{y}}\)</p>' . "\n";
        $this->assertEquals($output, $this->evaluate($input));
    }

    /**
     * Value-injection "{#value#}" functional requirements:
     *  1. Must result in Maxima code representing the value given.
     *     Equivalent to calling string() in Maxima.
     *  2. Must allow references to previous code. Both within CASText
     *     and outside.
     *  3. Must support statement level overriding of simplification.
     *  4. Must follow global simplification otherwise.
     *
     * @covers \qtype_stack\stack_cas_castext2_raw
     */
    public function test_value_injection_1(): void {

        $input = '{#1+2#}, {#sqrt(2)#}';
        $output = '3, sqrt(2)';
        $this->assertEquals($output, $this->evaluate($input));
    }

    /**
     * Add description here.
     * @covers \qtype_stack\stack_cas_castext2_raw
     */
    public function test_value_injection_2(): void {

        $input = '{#a#}, {#c:b#}, {#3/9,simp=false#}, {#c#}, {#d#}';
        // Note that last one if we are in global simp:true we just cannot know
        // whether that needs to be protected.
        $preamble = ['a:3/9', 'b:sqrt(2)', 'd:3/9'];
        $output = '1/3, sqrt(2), 3/9, sqrt(2), 1/3';
        $this->assertEquals($output, $this->evaluate($input, $preamble));
    }

    /**
     * Add description here.
     * @covers \qtype_stack\stack_cas_castext2_raw
     */
    public function test_value_injection_3(): void {

        $input = '{#a#}, {#3/9#}, {#3/9,simp#}, {#a,simp=false#}, {#a,simp#}';
        $preamble = ['a:3/9'];
        $output = '3/9, 3/9, 1/3, 3/9, 1/3';
        $options = new stack_options(['simplify' => false]);
        $this->assertEquals($output, $this->evaluate($input, $preamble, $options));
    }

    // STACK-options level requirements 1/3:
    // Tuning LaTeX-injection multiplication sign.
    /**
     * Add description here.
     * @covers \qtype_stack\stack_cas_castext2_latex
     * @covers \qtype_stack\stack_options
     */
    public function test_latex_injection_multiplicationsign_dot(): void {

        $input = '{@a@}, {@pi*x^2@}';
        $preamble = ['a:x*y*z'];
        $output = '\({x\cdot y\cdot z}\), \({\pi\cdot x^2}\)';
        $options = new stack_options(['multiplicationsign' => 'dot']);
        $this->assertEquals($output, $this->evaluate($input, $preamble, $options));
    }

    /**
     * Add description here.
     * @covers \qtype_stack\stack_cas_castext2_latex
     * @covers \qtype_stack\stack_options
     */
    public function test_latex_injection_multiplicationsign_cross(): void {

        $input = '{@a@}, {@pi*x^2@}';
        $preamble = ['a:x*y*z'];
        $output = '\({x\times y\times z}\), \({\pi\times x^2}\)';
        $options = new stack_options(['multiplicationsign' => 'cross']);
        $this->assertEquals($output, $this->evaluate($input, $preamble, $options));
    }

    /**
     * Add description here.
     * @covers \qtype_stack\stack_cas_castext2_latex
     * @covers \qtype_stack\stack_options
     */
    public function test_latex_injection_multiplicationsign_none(): void {

        $input = '{@a@}, {@pi*x^2@}';
        $preamble = ['a:x*y*z'];
        $output = '\({x\,y\,z}\), \({\pi\,x^2}\)';
        $options = new stack_options(['multiplicationsign' => 'none']);
        $this->assertEquals($output, $this->evaluate($input, $preamble, $options));
    }

    // STACK-option level requirements 2/3:
    // Tuning LaTeX-injection inverse trigonometric functions.
    /**
     * Add description here.
     * @covers \qtype_stack\stack_cas_castext2_latex
     * @covers \qtype_stack\stack_options
     */
    public function test_latex_injection_inversetrig_acos(): void {

        $input = '\({@acos(alpha)@}, {@asin(alpha)@}, {@a@}\)';
        $preamble = ['a:asech(alpha)'];
        $output = '\({{\rm acos}\left( \alpha \right)}, {{\rm asin}\left( \alpha \right)}, {{\rm asech}\left( \alpha \right)}\)';
        $options = new stack_options(['inversetrig' => 'acos']);
        $this->assertEquals($output, $this->evaluate($input, $preamble, $options));
    }

    /**
     * Add description here.
     * @covers \qtype_stack\stack_cas_castext2_latex
     * @covers \qtype_stack\stack_options
     */
    public function test_latex_injection_inversetrig_cos_1(): void {

        $input = '\({@acos(alpha)@}, {@asin(alpha)@}, {@a@}\)';
        $preamble = ['a:asech(alpha)'];
        $output = '\({\cos^{-1}\left( \alpha \right)}, {\sin^{-1}\left( \alpha \right)}, {{\rm sech}^{-1}\left( \alpha \right)}\)';
        $options = new stack_options(['inversetrig' => 'cos-1']);
        $this->assertEquals($output, $this->evaluate($input, $preamble, $options));
    }

    /**
     * Add description here.
     * @covers \qtype_stack\stack_cas_castext2_latex
     * @covers \qtype_stack\stack_options
     */
    public function test_latex_injection_inversetrig_arccos(): void {

        $input = '\({@acos(alpha)@}, {@asin(alpha)@}, {@a@}\)';
        $preamble = ['a:asech(alpha)'];
        $output = '\({\arccos \left( \alpha \right)}, {\arcsin \left( \alpha \right)}, {{\rm arcsech}\left( \alpha \right)}\)';
        $options = new stack_options(['inversetrig' => 'arccos']);
        $this->assertEquals($output, $this->evaluate($input, $preamble, $options));
    }

    // STACK-option level requirements 3/3:
    // Tuning LaTeX-injection matrix parenthesis.
    /**
     * Add description here.
     * @covers \qtype_stack\stack_cas_castext2_latex
     * @covers \qtype_stack\stack_options
     */
    public function test_latex_injection_matrixparens_brackets(): void {

        $input = '{@matrix([1,0],[0,1])@}';
        $preamble = [];
        $output = '\({\left[\begin{array}{cc} 1 & 0 \\\\ 0 & 1 \end{array}\right]}\)';
        $options = new stack_options(['matrixparens' => '[']);
        $this->assertEquals($output, $this->evaluate($input, $preamble, $options));
    }

    /**
     * Add description here.
     * @covers \qtype_stack\stack_cas_castext2_latex
     * @covers \qtype_stack\stack_options
     */
    public function test_latex_injection_matrixparens_parens(): void {

        $input = '{@matrix([1,0],[0,1])@}';
        $preamble = [];
        $output = '\({\left(\begin{array}{cc} 1 & 0 \\\\ 0 & 1 \end{array}\right)}\)';
        $options = new stack_options(['matrixparens' => '(']);
        $this->assertEquals($output, $this->evaluate($input, $preamble, $options));
    }

    /**
     * Add description here.
     * @covers \qtype_stack\stack_cas_castext2_latex
     * @covers \qtype_stack\stack_options
     */
    public function test_latex_injection_matrixparens_braces(): void {

        $input = '{@matrix([1,0],[0,1])@}';
        $preamble = [];
        $output = '\({\left\{\begin{array}{cc} 1 & 0 \\\\ 0 & 1 \end{array}\right\}}\)';
        $options = new stack_options(['matrixparens' => '{']);
        $this->assertEquals($output, $this->evaluate($input, $preamble, $options));
    }

    /**
     * Add description here.
     * @covers \qtype_stack\stack_cas_castext2_latex
     * @covers \qtype_stack\stack_options
     */
    public function test_latex_injection_matrixparens_none(): void {

        $input = '{@matrix([1,0],[0,1])@}';
        $preamble = [];
        $output = '\({\begin{array}{cc} 1 & 0 \\\\ 0 & 1 \end{array}}\)';
        $options = new stack_options(['matrixparens' => '']);
        $this->assertEquals($output, $this->evaluate($input, $preamble, $options));
    }

    /**
     * Add description here.
     * @covers \qtype_stack\stack_cas_castext2_latex
     * @covers \qtype_stack\stack_options
     */
    public function test_latex_injection_matrixparens_pipe(): void {

        $input = '{@matrix([1,0],[0,1])@}';
        $preamble = [];
        $output = '\({\left|\begin{array}{cc} 1 & 0 \\\\ 0 & 1 \end{array}\right|}\)';
        $options = new stack_options(['matrixparens' => '|']);
        $this->assertEquals($output, $this->evaluate($input, $preamble, $options));
    }

    /**
     * Add description here.
     * @covers \qtype_stack\stack_cas_castext2_latex
     * @covers \qtype_stack\stack_options
     */
    public function test_latex_matrixparens_indirect_lmxchar(): void {

        $input = '\[ f(x) := \left\{ {@(lmxchar:"", f)@} \right. \]';
        $preamble = ['f:matrix([4*x+4, x<1],[-x^2-4*x-8, x>=1])'];
        $output = '\[ f(x) := \left\{ {\begin{array}{cc} 4\cdot x+4 & x < 1 \\\\ ' .
            '-x^2-4\cdot x-8 & x\geq 1 \end{array}} \right. \]';
        $options = new stack_options();
        $this->assertEquals($output, $this->evaluate($input, $preamble, $options));
    }

    /**
     * Block-system "define"-block, functional requirements:
     *  1. Allow inline changes to any value.
     *  2. Handle simplification.
     *  3. Single block may redefine same value, needs to respect
     *     declaration order.
     *
     * @covers \qtype_stack\stack_cas_castext2_define
     */
    public function test_blocks_define(): void {

        $input = '{#a#}, [[ define a="a+1" a="a*a" b="3/9" c="3/9,simp"/]] {#a#} {#b#} {#b,simp#} {#c#}';
        $preamble = ['a:x'];
        $output = 'x,  (x+1)*(x+1) 3/9 1/3 1/3';
        $options = new stack_options(['simplify' => false]);
        $this->assertEquals($output, $this->evaluate($input, $preamble, $options));
    }

    /**
     * Block-system "if"-block, functional requirements:
     *  1. Conditional evaluation and display of contents.
     *  2. Else and else if behaviour.
     *  3. Maxima if equivalent conditions.
     *
     * @covers \qtype_stack\stack_cas_castext2_if
     */
    public function test_blocks_if_1(): void {

        $input = '{#a#}, [[ if test="a=x" ]]yes[[ else ]]no[[define a="3"/]][[/if]], [[ if test="a=3"]]maybe[[/ if ]]';
        $preamble = ['a:x'];
        $output = 'x, yes, ';
        $options = new stack_options(['simplify' => false]);
        $this->assertEquals($output, $this->evaluate($input, $preamble, $options));
    }

    /**
     * Add description here.
     * @covers \qtype_stack\stack_cas_castext2_if
     */
    public function test_blocks_if_2(): void {

        $input = '{#a#}, [[ if test="a=x" ]]yes[[define a="3"/]][[ else ]]no[[/if]], [[ if test="a=3"]]maybe[[/ if ]]';
        $preamble = ['a:x'];
        $output = 'x, yes, maybe';
        $options = new stack_options(['simplify' => true]);
        $this->assertEquals($output, $this->evaluate($input, $preamble, $options));
    }

    /**
     * Add description here.
     * @covers \qtype_stack\stack_cas_castext2_if
     */
    public function test_blocks_if_3(): void {

        $input = '{#a#}, [[ if test="a=x" ]]yes[[define a="3"/]][[ else ]]no[[/if]], ' .
            '[[ if test="a=x"]]no[[elif test="a=3"]]maybe[[/ if ]]';
        $preamble = ['a:x'];
        $output = 'x, yes, maybe';
        $options = new stack_options(['simplify' => true]);
        $this->assertEquals($output, $this->evaluate($input, $preamble, $options));
    }

    /**
     * Add description here.
     * @covers \qtype_stack\stack_cas_castext2_if
     */
    public function test_blocks_if_4(): void {

        $input = '{#a#}, [[ if test="a=x" ]]yes[[if test="b=y"]][[define b="x"/]][[/if]][[ else ]]no[[/if]], {#b#}';
        $preamble = ['a:x', 'b:y'];
        $output = 'x, yes, x';
        $options = new stack_options(['simplify' => true]);
        $this->assertEquals($output, $this->evaluate($input, $preamble, $options));
    }

    /**
     * Block-system "foreach"-block, functional requirements:
     *  1. Iterates over elements of a list or a set assigning the values
     *     to the defined variable.
     *  2. Can iterate over multiple such things simultaneously, but limits
     *     to the length of the shortest one.
     *  3. Simplification is not perfectly maintained as indefinite depth is
     *     not reasonably maintainable. Applying simplification even when its
     *     off globaly is supportted but not disabling simplification.
     *  4. In the case of sets the ordering is not well defined.
     *
     * @covers \qtype_stack\stack_cas_castext2_foreach
     */
    public function test_blocks_foreach_1(): void {

        $input = '[[ foreach foo="a"]][[ foreach bar="foo"]]{#bar#}, [[/foreach]] - [[/foreach]]';
        $preamble = ['a:[[1,1+1,1+1+1],[1,2,3]]'];
        $output = '1, 1+1, 1+1+1,  - 1, 2, 3,  - ';
        $options = new stack_options(['simplify' => false]);
        $this->assertEquals($output, $this->evaluate($input, $preamble, $options));
    }

    /**
     * Add description here.
     * @covers \qtype_stack\stack_cas_castext2_foreach
     */
    public function test_blocks_foreach_2(): void {

        $input = '[[ foreach foo="a"]][[ foreach bar="foo"]]{#bar#}, [[/foreach]] - [[/foreach]]';
        $preamble = ['a:[{1,1+1,1+1+1},{3,2,1}]'];
        $output = '1, 2, 3,  - 1, 2, 3,  - ';
        $options = new stack_options(['simplify' => true]);
        $this->assertEquals($output, $this->evaluate($input, $preamble, $options));
    }

    /**
     * Add description here.
     * @covers \qtype_stack\stack_cas_castext2_foreach
     */
    public function test_blocks_foreach_3(): void {

        $input = '[[ foreach foo="a"]][[ foreach bar="foo,simp"]]{#bar#}, [[/foreach]] - [[/foreach]]';
        $preamble = ['a:[[1,1+1,1+1+1],[1,2,3]]'];
        $output = '1, 2, 3,  - 1, 2, 3,  - ';
        $options = new stack_options(['simplify' => false]);
        $this->assertEquals($output, $this->evaluate($input, $preamble, $options));
    }

    /**
     * Add description here.
     * @covers \qtype_stack\stack_cas_castext2_foreach
     */
    public function test_blocks_foreach_4(): void {

        $input = '[[ foreach foo="a" bar="b"]]{@foo^bar@}, [[/foreach]]';
        $preamble = ['a:[1,2,3,4]', 'b:[x,y,z]'];
        $output = '\({1^{x}}\), \({2^{y}}\), \({3^{z}}\), ';
        $options = new stack_options(['simplify' => false]);
        $this->assertEquals($output, $this->evaluate($input, $preamble, $options));
    }

    /**
     * Block-system "comment"-block, functional requirements:
     *  1. Comments out itself and contents.
     *  2. Even if contents are invalid or incomplete.
     *
     * @covers \qtype_stack\stack_cas_castext2_comment
     */
    public function test_blocks_comment(): void {

        $input = '1[[ comment]] [[ foreach bar="foo"]] {#y@} [[/comment]]2';
        $output = '12';
        $this->assertEquals($output, $this->evaluate($input));
    }

    /**
     * Block-system "todo"-block, functional requirements:
     *  1. Comments out itself and contents.
     *  2. Even if contents are invalid or incomplete.
     *
     * @covers \qtype_stack\stack_cas_castext2_todo
     */
    public function test_blocks_todo(): void {

        $input = '1[[ todo]] [[ foreach bar="foo"]] {#y@} [[/todo]]2';
        $output = '1<!--- stack_todo --->2';
        $this->assertEquals($output, $this->evaluate($input));
    }

    /**
     * Add description here.
     * @covers \qtype_stack\stack_cas_castext2_hint
     */
    public function test_blocks_hint(): void {

        $input = "1[[hint title=\"Show solution\"]][[if test='is(1>0)']]Solution[[/if]][[/hint]]2";
        $output = '1<details class="stack-hint"><summary class="btn btn-secondary" >Show solution</summary>' .
                  '<div class="stack-hint-content">Solution</div></details>2';
        $this->assertEquals($output, $this->evaluate($input));
    }

    /**
     * Add description here.
     * @covers \qtype_stack\stack_cas_castext2_hint
     */
    public function test_blocks_hint_hint(): void {

        $input = "[[hint title=\"Show solution\"]][[hint title=\"Go on....\"]]Solution[[/hint]][[/hint]]";
        $output = '<details class="stack-hint"><summary class="btn btn-secondary" >Show solution</summary>' .
                  '<div class="stack-hint-content">' .
                  '<details class="stack-hint"><summary class="btn btn-secondary" >Go on....</summary>' .
                  '<div class="stack-hint-content">Solution</div></details></div></details>';
        $this->assertEquals($output, $this->evaluate($input));
    }

    /**
     * Block-system "escape"-block, functional requirements:
     *  1. Escapes the contents so that they will not be processed.
     *  2. Outputs contents as they are.
     *
     * @covers \qtype_stack\stack_cas_castext2_escape
     */
    public function test_blocks_escape(): void {

        $input = '1[[ escape]] [[ foreach bar="foo"]] {#y@} [[/escape]]2';
        $output = '1 [[ foreach bar="foo"]] {#y@} 2';
        $this->assertEquals($output, $this->evaluate($input));
    }

    /**
     * Should we condone a space at the end of the block name?
     *
     * @covers \qtype_stack\CTP_Parser
     * @covers \qtype_stack\stack_cas_castext2_escape
     */
    public function test_blocks_escape_space_end(): void {

        $input = '1[[ escape ]] [[ foreach bar="foo"]] {#y@} [[/escape]]2';
        $output = '1 [[ foreach bar="foo"]] {#y@} 2';
        $this->assertEquals($output, $this->evaluate($input));
    }

    /**
     * Should we condone lack of any spaces in the block name?
     *
     * @covers \qtype_stack\CTP_Parser
     * @covers \qtype_stack\stack_cas_castext2_escape
     */
    public function test_blocks_escape_space_none(): void {

        $input = '1[[escape]] [[ foreach bar="foo"]] {#y@} [[/escape]]2';
        $output = '1 [[ foreach bar="foo"]] {#y@} 2';
        $this->assertEquals($output, $this->evaluate($input));
    }

    // Low level tuning, features that are not strictly CASText:
    // Use of texput().
    /**
     * Add description here.
     * @covers \qtype_stack\stack_cas_castext2_latex
     * @covers \qtype_stack\stack_keyval
     */
    public function test_texput_1(): void {

        $input = '\({@foo@}\)';
        $preamble = ['texput(foo, "\\\\frac{foo}{bar}")'];
        $output = '\({\frac{foo}{bar}}\)';
        $this->assertEquals($output, $this->evaluate($input, $preamble));
    }

    /**
     * Add description here.
     * @covers \qtype_stack\stack_cas_castext2_latex
     * @covers \qtype_stack\stack_cas_keyval
     */
    public function test_texput_2(): void {

        $input = '{@x^2+foo(a,sqrt(b))@}';
        $preamble = [
            'footex(e):=block([a,b],[a,b]:args(e),sconcat(tex1(a)," \\\\rightarrow ",tex1(b)))',
            'texput(foo, footex)',
        ];
        $output = '\({x^2+a \rightarrow \sqrt{b}}\)';
        $this->assertEquals($output, $this->evaluate($input, $preamble));
    }

    // Check stackfltfmt for presentation of floating point numbers.
    /**
     * Add description here.
     * @covers \qtype_stack\stack_cas_castext2_latex
     */
    public function test_stackfltfmt(): void {

        $input = '{@a@}, {@(stackfltfmt:"~f",a)@}';
        // Note that 0.000012 has rounding in clisp which is not the point of this test.
        // And 0.000013 has rounding in SBCL/GCL.
        // And 0.000016 has rounding in SBCL!
        $preamble = ['stackfltfmt:"~e"', 'a:0.000025'];
        $output = '\({2.5e-5}\), \({0.000025}\)';
        $this->assertEquals($output, strtolower($this->evaluate($input, $preamble)));
    }

    // Check stackintfmt for presentation of integers.
    /**
     * Add description here.
     * @covers \qtype_stack\stack_cas_castext2_latex
     */
    public function test_stackintfmt(): void {

        $input = '{@(stackintfmt:"~:r",a)@}, {@(stackintfmt:"~@R",a)@}';
        $preamble = ['a:1998'];
        $output = '\({\text{one thousand nine hundred ninety-eighth}}\), \({MCMXCVIII}\)';
        if ($this->adapt_to_new_maxima('5.46.0')) {
            $output = '\({\text{one thousand, nine hundred ninety-eighth}}\), \({MCMXCVIII}\)';
        }
        $this->assertEquals($output, $this->evaluate($input, $preamble));
    }

    // Inline fractions using stack_disp_fractions("i").
    /**
     * Add description here.
     * @covers \qtype_stack\stack_cas_castext2_latex
     */
    public function test_stack_disp_fractions(): void {

        $input = '{@(stack_disp_fractions("i"),a/b)@}, {@(stack_disp_fractions("d"),a/b)@}';
        $output = '\({{a}/{b}}\), \({\frac{a}{b}}\)';
        $this->assertEquals($output, $this->evaluate($input));
    }

    // JavaScript string generation.
    /**
     * Add description here.
     * @covers \qtype_stack\stack_cas_castext2_jsstring
     */
    public function test_jsstring(): void {

        $input = 'var feedback = [[jsstring]]Something \({@sqrt(2)@}\) {@sqrt(2)@}.[[/jsstring]];';
        $output = 'var feedback = "Something \\\\({\\\\sqrt{2}}\\\\) \\\\({\\\\sqrt{2}}\\\\).";';
        $this->assertEquals($output, $this->evaluate($input));

        // Separate for completely static no CAS evaluatable content.
        $input = 'var feedback = [[jsstring]]Something "static".[[/jsstring]];';
        $output = 'var feedback = "Something \"static\".";';
        $this->assertEquals($output, $this->evaluate($input));
    }

    // Inline castext.
    /**
     * Add description here.
     * @covers \qtype_stack\stack_cas_keyval
     * @covers \qtype_stack\stack_cas_castext2_castext
     */
    public function test_inline_castext(): void {

        $keyval = 'B:castext("B");sq:castext("{@sqrt(2)@}");';
        // The inline castext compilation currently only happens for keyvals, not for
        // singular statements so we need to do something special to get this done.
        $kv = new stack_cas_keyval($keyval);
        $kv->get_valid();
        $kvcode = $kv->compile('test')['statement'];
        $statements = [new stack_secure_loader($kvcode, 'test-kv')];

        $input = 'A [[castext evaluated="B"/]] C, [[castext evaluated="sq"/]]';
        $output = 'A B C, \\({\\sqrt{2}}\\)';

        $result = castext2_evaluatable::make_from_source($input, 'castext-test-case');
        $statements[] = $result;
        $session = new stack_cas_session2($statements);
        $session->instantiate();

        $this->assertEquals($output, $result->get_rendered());
    }

    /**
     * Add description here.
     * @covers \qtype_stack\stack_cas_keyval
     * @covers \qtype_stack\stack_cas_castext2_latex
     */
    public function test_inline_castext_normal_injection(): void {

        $keyval = 'B:castext("B");sq:castext("{@sqrt(2)@}");';
        // The inline castext compilation currently only happens for keyvals, not for
        // singular statements so we need to do something special to get this done.
        $kv = new stack_cas_keyval($keyval);
        $kv->get_valid();
        $kvcode = $kv->compile('test')['statement'];
        $statements = [new stack_secure_loader($kvcode, 'test-kv')];

        $input = 'A {@B@} C, {@sq@}';
        $output = 'A B C, \\({\\sqrt{2}}\\)';

        $result = castext2_evaluatable::make_from_source($input, 'castext-test-case');
        $statements[] = $result;
        $session = new stack_cas_session2($statements);
        $session->instantiate();

        $this->assertEquals($output, $result->get_rendered());
    }

    // Inline castext inline castext, not something you see in many places.
    /**
     * Add description here.
     * @covers \qtype_stack\stack_cas_castext2_special_root
     */
    public function test_inline_castext_inline(): void {

        $input = 'A [[castext evaluated="castext(\\"B\\")"/]] C, [[castext evaluated="castext(\\"{@sqrt(2)@}\\")"/]]';
        $output = 'A B C, \\({\\sqrt{2}}\\)';
        $this->assertEquals($output, $this->evaluate($input));
    }

    /**
     * Add description here.
     * @covers \qtype_stack\stack_cas_keyval
     * @covers \qtype_stack\stack_cas_castext2_latex
     */
    public function test_inline_castext_error(): void {

        $keyval = 'S:1;T:castext(A_11);';
        // The inline castext compilation currently only happens for keyvals, not for
        // singular statements so we need to do something special to get this done.
        $kv = new stack_cas_keyval($keyval);
        $kv->get_valid();
        $kvcode = $kv->compile('test')['statement'];
        $statements = [new stack_secure_loader($kvcode, 'test-kv')];

        $input = '{@castext(A_12)@}';
        $output = '<h3>Rendering of text content failed.</h3><ul><li>This text content was never evaluated.</li></ul>';

        $result = castext2_evaluatable::make_from_source($input, 'castext-test-case');
        $this->assertFalse($result->get_valid());

        $statements[] = $result;
        $session = new stack_cas_session2($statements);
        // We know this session is invalid, so don't try to instantiate it now!

        $this->assertEquals('castext()-compiler, wrong argument. Only works with one direct raw string. ' .
            'And possibly a format descriptor.', $session->get_errors());
        $this->assertEquals($output, $result->get_rendered());
    }

    // Test common string population.
    /**
     * Add description here.
     * @covers \qtype_stack\stack_cas_castext2_commonstring
     */
    public function test_commonsstring(): void {

        $preamble = ['simp:false', 'a:52+x-x', 'b:"text"', 'c:sqrt(5)', 'simp:true'];
        // The string "stackversionerror" just happens to have multiple named parameters so we use it,
        // feel free to use any other if things change.
        $input = '[[commonstring key="stackversionerror" nosimp_pat="a" qfield="b" raw_ver="c"/]]';
        $output = stack_string('stackversionerror', ['qfield' => 'text', 'ver' => 'sqrt(5)', 'pat' => '\({52+x-x}\)']);

        $this->assertEquals($output, $this->evaluate($input, $preamble));
    }

    public function test_plot_if(): void {

        // This test case caused an error in Maxima 5.45.0.
        // The fix to this error is the use of ex:%_ce_expedite(ex) in the plot function to remove %_C.
        // However, we need to actively evaluate the %_C functions at the point we remove them.
        // When expressions occur within the "then" clause they are not actually evaluated and in Maxima 5.45.0
        // this happens _after_ the list of variables has been created.  So at that point, %_C(sin) contributes an extra
        // variable "sin" to the picture, and so plot2d throws a (needless) error.  Hence, the fix is to
        // expedite the security checks before we send the cleaned-up expression to plot2d.
        $input = '{@plot(if x<=0 then x^2+1 else sin(x)/x, [x,-4,20], [y,-1,6])@}';

        $this->assertTrue(strpos($this->evaluate($input), '!ploturl!stackplot') > 0);
    }

    public function test_templates_1(): void {

        $input = '[[template name="foobar"/]]';
        $output = 'Warning no template defined with name "foobar"';
        $this->assertEquals($output, $this->evaluate($input));
    }

    public function test_templates_2(): void {

        $input = '[[template name="foobar" mode="ignore missing"/]]';
        $output = '';
        $this->assertEquals($output, $this->evaluate($input));
    }

    public function test_templates_3(): void {

        $preamble = ['a:1'];
        $input = '[[template name="foobar"]]FOOBAR{#a#}[[/template]][[template name="foobar"/]]' .
            '[[define a="2"/]] [[template name="foobar"/]]';
        $output = 'FOOBAR1 FOOBAR2';
        $this->assertEquals($output, $this->evaluate($input, $preamble));
    }

    public function test_templates_4(): void {

        $input = '[[template name="foobar" mode="default"]]default[[/template]]';
        $output = 'default';
        $this->assertEquals($output, $this->evaluate($input));
    }

    public function test_templates_5(): void {

        $input = '[[template name="foobar"]]override[[/template]]X[[template name="foobar" mode="default"]]default[[/template]]';
        $output = 'Xoverride';
        $this->assertEquals($output, $this->evaluate($input));
    }

    public function test_maplist_labda(): void {

        $input = '{@maplist(lambda([ex], x^ex), [1,2,3,4])@}';
        $output = '\({\left[ x , x^2 , x^3 , x^4 \right]}\)';
        $this->assertEquals($output, $this->evaluate($input));
    }
}

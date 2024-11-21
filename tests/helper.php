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

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '../../stack/potentialresponsetreestate.class.php');

// Test helper code for the Stack question type.
//
// @package   qtype_stack.
// @copyright 2012 The Open University.
// @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.

/**
 * @group qtype_stack
 */
class qtype_stack_test_helper extends question_test_helper {
    const DEFAULT_CORRECT_FEEDBACK          = '<p>Correct answer, well done.</p>';
    const DEFAULT_PARTIALLYCORRECT_FEEDBACK = '<p>Your answer is partially correct.</p>';
    const DEFAULT_INCORRECT_FEEDBACK        = '<p>Incorrect answer.</p>';

    public function get_test_questions() {
        return [
            'test0', // One input, one PRT, not randomised. 1 + 1 = 2.
            'test1', // One input, one PRT, randomised. Integrate (v - a) ^ n, a, n small random ints.
            'test2', // Two inputs, one PRT, not randomises. Expand (x - 2)(x - 3).
            'test3', // Four inputs, four PRTs, not randomised. Even and odd functions.
            'test3_penalty0_1', // Four inputs, four PRTs, not randomised. Even and odd functions.
            'test4', // One input, one PRT, not randomised, has a plot. What is the equation of this graph? x^2.
            'test6', // Test of the matrix input type.
            'test8', // 1 input, 1 PRT with 3 nodes. Roots of unity. Input has a syntax hint.
            'test9', // 2 inputs, 1 PRT, randomised, worked solution with CAS & plot. Make function continuous.
            'test_boolean', // 2 inputs, 1 PRT, randomised, worked solution with CAS & plot. Make function continuous.
            'divide',       // One input, one PRT, tests 1 / ans1 - useful for testing CAS errors like divide by 0.
            'numsigfigs',   // One input, one PRT, tests 1 / ans1 - uses the NumSigFigs test.
            'numsigfigszeros',  // One input, one PRT, tests 1 / ans1 - uses the NumSigFigs test with trailing zeros.
            'numdpsfeedbackvars',   // Two numerical inputs, one PRT, uses ATNumDPs and feedback variables (illustrates problem).
            '1input2prts',  // Contrived example with one input, 2 prts, all feedback in the specific feedback area.
            'information',  // Neither inputs nor PRTs.
            'variable_grade', // Variables in grade.
            'survey',       // Inputs, but no PRTs.
            'single_char_vars',   // Tests the insertion of * symbols between letter names.
            'runtime_prt_err',    // This generates an error in the PRT at runtime.  With and without guard clause.
            'runtime_ses_err',    // This generates an invalid session.
            'runtime_cas_err',    // This generates a 1/0 in the CAS at run time.
            'units',              // This question has units inputs, and a numerical test.
            'unitsoptions',       // This question has units inputs, and a numerical test with the accuracy in a variable.
            'unitsmulti',         // This question has units inputs, and an algebraic input.
            'equiv_quad',         // This question uses equivalence reasoning to solve a quadratic equation.
            'checkbox_all_empty', // Creates a checkbox input with none checked as the correct answer: edge case.
            'checkbox_union',     // Creates a checkbox input with %union functions: noun edge case.
            'checkbox_noun_diff', // Creates a checkbox input with noun  diff ('diff) functions.
            'addrow',             // This question has addrows, in an older version.
            'mul',                // This question has mul in the options which is no longer permitted.
            'contextvars',        // This question makes use of the context variables.
            'stringsloppy',       // Uses the StringSloppy answer test, and string input.
            'sregexp',            // Uses the SRegExp answer test, and string input.
            'feedbackstyle',      // Test the various feedbackstyle options.
            'multilang',          // Check for mismatching languages.
            'lang_blocks',        // Check for mismatching languages using STACK's [[lang...]] block mechanism.
            'block_locals',       // Make sure local variables within a block are still permitted student input.
            'validator',          // Test teacher-defined input validators and language.
            'feedback',           // Test teacher-defined input feedback and complex numbers.
            'ordergreat',         // Test the ordergreat function at the question level, e.g. keyvals.
            'exdowncase',         // Test the ordergreat function with exdowncase.
            'bailout',            // Test the ability to bail out of a PRT using %stack_prt_stop_p.
            // Test questions for all the various input types.
            'algebraic_input',
            'algebraic_input_right',
            'algebraic_input_size',
            'algebraic_input_compact',
            'algebraic_input_empty',
            'algebraic_input_simpl',
            'checkbox_input',
            'checkbox_input_no_latex',
            'checkbox_input_plots',
            'checkbox_show_tans',
            'dropdown_input',
            'equiv_input_compact',
            'equiv_input',
            'matrix_input',
            'varmatrix_input',
            'matrix_multi_input',
            'notes_input',
            'numerical_input',
            'radio_input',
            'radio_input_compact',
            'single_char_input',
            'string_input',
            'textarea_input',
            'textarea_input_compact',
            'true_false_input',
            'units_input',
            'jsx_graph_input',
        ];
    }

    /**
     * Does the basic initialisation of a new STACK question that all the test
     * questions will need.
     * @return qtype_stack_question the new question.
     */
    protected static function make_a_stack_question() {
        question_bank::load_question_definition_classes('stack');

        $q = new qtype_stack_question();
        test_question_maker::initialise_a_question($q);
        $q->qtype = question_bank::get_qtype('stack');
        $q->contextid = context_system::instance()->id;

        $q->stackversion = get_config('qtype_stack', 'version');
        $q->questionvariables = '';
        $q->specificfeedback = '';
        $q->specificfeedbackformat = FORMAT_HTML;
        $q->questiondescription = '';
        $q->questiondescriptionformat = FORMAT_HTML;
        $q->penalty = 0.1; // The default.

        $q->prtcorrect = self::DEFAULT_CORRECT_FEEDBACK;;
        $q->prtcorrectformat = FORMAT_HTML;
        $q->prtpartiallycorrect = self::DEFAULT_PARTIALLYCORRECT_FEEDBACK;
        $q->prtpartiallycorrectformat = FORMAT_HTML;
        $q->prtincorrect = self::DEFAULT_INCORRECT_FEEDBACK;
        $q->prtincorrectformat = FORMAT_HTML;
        $q->generalfeedback = '';
        $q->variantsselectionseed = '';
        $q->compiledcache = [];

        $q->inputs = [];
        $q->prts = [];

        $q->options = new stack_options();
        $q->questionnote = '';
        $q->questionnoteformat = FORMAT_HTML;

        return $q;
    }

    /**
     * @return qtype_stack_question a very elementary question.
     */
    public static function make_stack_question_test0() {
        $q = self::make_a_stack_question();

        $q->stackversion = '2019072900';
        $q->name = 'test-0';
        $q->questionvariables = "stack_reset_vars(true);\na:1+1;";
        $q->questiontext = 'What is {@a@}? [[input:ans1]]
                           [[validation:ans1]]';
        $q->questiondescription = 'This is a great and wonderful question!';

        $q->specificfeedback = '[[feedback:firsttree]]';
        $q->penalty = 0.3; // Non-zero and not the default.

        $q->inputs['ans1'] = stack_input_factory::make(
                'algebraic', 'ans1', '2', null, ['boxWidth' => 5]);

        $q->options->set_option('simplify', false);

        $prt = new stdClass;
        $prt->name              = 'firsttree';
        $prt->id                = 0;
        $prt->value             = 1;
        $prt->feedbackstyle     = 1;
        $prt->feedbackvariables = '';
        $prt->firstnodename     = '0';
        $prt->nodes             = [];
        $prt->autosimplify      = true;

        $newnode = new stdClass;
        $newnode->id                  = '0';
        $newnode->nodename            = '0';
        $newnode->description         = '';
        $newnode->sans                = 'ans1';
        $newnode->tans                = '2';
        $newnode->answertest          = 'EqualComAss';
        $newnode->testoptions         = '';
        $newnode->quiet               = false;
        $newnode->falsescore          = '0';
        $newnode->falsescoremode      = '=';
        $newnode->falsepenalty        = $q->penalty;
        $newnode->falsefeedback       = '';
        $newnode->falsefeedbackformat = '1';
        $newnode->falseanswernote     = 'firsttree-1-F';
        $newnode->falsenextnode       = '-1';
        $newnode->truescore           = '1';
        $newnode->truescoremode       = '=';
        $newnode->truepenalty         = $q->penalty;
        $newnode->truefeedback        = '';
        $newnode->truefeedbackformat  = '1';
        $newnode->trueanswernote      = 'firsttree-1-T';
        $newnode->truenextnode        = '-1';
        $prt->nodes[] = $newnode;

        $q->prts[$prt->name] = new stack_potentialresponse_tree_lite($prt, $prt->value, $q);

        return $q;
    }

    /**
     * @return qtype_stack_question the question from the test1.xml file.
     */
    public static function make_stack_question_test1() {
        $q = self::make_a_stack_question();

        // We don't explicitly set stackversion here because we test the default.
        $q->name = 'test-1';
        // Fix the actual variable ta, to avoid differening rand values.
        $q->questionvariables = 'n : rand(5)+3; a : rand(5)+3; v : x; p : (v-a)^n; ta : (x-7)^4/4; ta1 : ta';
        $q->questiontext = 'Find
                            \[ \int {@p@} d{@v@}\]
                            [[input:ans1]]
                            [[validation:ans1]]';
        $q->generalfeedback = 'We can either do this question by inspection (i.e. spot the answer)
                               or in a more formal manner by using the substitution
                               \[ u = ({@v@}-{@a@}).\]
                               Then, since $\frac{d}{d{@v@}}u=1$ we have
                               \[ \int {@p@} d{@v@} = \int u^{@n@} du = \frac{u^{@n+1@}}{@n+1@}+c = {@ta@}+c.\]';

        $q->questionnote = '{@p@}, {@ta@}.';

        $q->specificfeedback = '[[feedback:PotResTree_1]]';
        $q->penalty = 0.25; // Non-zero and not the default.

        $q->inputs['ans1'] = stack_input_factory::make(
                        'algebraic', 'ans1', 'ta+c', null,
                ['boxWidth' => 20, 'forbidWords' => 'int, [[BASIC-ALGEBRA]]', 'allowWords' => 'popup, boo, Sin']);

        // By making the input to the answer test differ from ans1 in a trivial way, we use the "value" of this variable
        // and not the raw student input.  This is to make sure the student's answer is evaluated in the context of
        // question variables.  Normally we don't want the student's answer to be evaluated in this way,
        // but in this question we do to ensure the random values are used.
        $prt = json_decode('{"name":"PotResTree_1","id":"0","value":1,"feedbackstyle":1,"autosimplify":true,
            "feedbackvariables":"",
            "firstnodename":"",
            "nodes":[
                {"id":0,"nodename":"0","quiet":false,
                 "sans":"ans1+0","tans":"ta","answertest":"Int","testoptions":"x",
                 "falsenextnode":"-1","falseanswernote":"PotResTree_1-0-0",
                 "falsescore":0,"falsescoremode":"=",
                 "falsepenalty":0.25,"falsefeedback":"","falsefeedbackformat":"1",
                 "truenextnode":"-1","trueanswernote":"PotResTree_1-0-1",
                 "truescore":1,"truescoremode":"=",
                 "truepenalty":0.25,"truefeedback":"","truefeedbackformat":"1",
                 "description": ""
                }
              ]}');
        $q->prts[$prt->name] = new stack_potentialresponse_tree_lite($prt, $prt->value, $q);

        return $q;
    }

    public function get_stack_question_form_data_test1() {
        $formform = new stdClass();

        $formform->name = 'test-1';
        $formform->stackversion = get_config('qtype_stack', 'version');
        $formform->questionvariables = 'n : rand(5)+3; a : rand(5)+3; v : x; p : (v-a)^n; ta : (x-7)^4/4; ta1 : ta';
        $formform->variantsselectionseed = '';
        $formform->questiontext = [
            'text' => 'Find
                       \[ \int {@p@} d{@v@}\]
                       [[input:ans1]]
                       [[validation:ans1]]',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->defaultmark = 4;
        $formform->specificfeedback = [
            'text' => '[[feedback:PotResTree_1]]',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->penalty = 0.40000000000000002;
        $formform->generalfeedback = [
            'text' => 'We can either do this question by inspection (i.e. spot the answer)
                               or in a more formal manner by using the substitution
                               \[ u = ({@v@}-{@a@}).\]
                               Then, since $\frac{d}{d{@v@}}u=1$ we have
                               \[ \int {@p@} d{@v@} = \int u^{@n@} du = \frac{u^{@n+1@}}{@n+1@}+c = {@ta@}+c.\]',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->questionnote = [
            'text' => '{@p@}, {@ta@}.',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->questiondescription = [
            'text' => 'This is a basic test question.',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->ans1type = 'algebraic';
        $formform->ans1modelans = 'ta+c';
        $formform->ans1boxsize = 20;
        $formform->ans1strictsyntax = '1';
        $formform->ans1insertstars = '0';
        $formform->ans1syntaxhint = '';
        $formform->ans1syntaxattribute = '0';
        $formform->ans1forbidwords = 'int, [[BASIC-ALGEBRA]]';
        $formform->ans1allowwords = 'popup, boo, Sin';
        $formform->ans1forbidfloat = '1';
        $formform->ans1requirelowestterms = '0';
        $formform->ans1checkanswertype = '0';
        $formform->ans1mustverify = '1';
        $formform->ans1showvalidation = '1';
        $formform->ans1options = '';

        $formform->PotResTree_1value = 1;
        $formform->PotResTree_1autosimplify = '1';
        $formform->PotResTree_1feedbackstyle     = 1;
        $formform->PotResTree_1feedbackvariables = 'sa:subst(x=-x,ans1)+ans1';
        $formform->PotResTree_1answertest = [0 => 'Int'];
        $formform->PotResTree_1description = [0 => 'Anti-derivative test'];
        $formform->PotResTree_1sans = [0 => 'ans1+0'];
        $formform->PotResTree_1tans = [0 => 'ta'];
        $formform->PotResTree_1testoptions = [0 => 'x'];
        $formform->PotResTree_1quiet = [0 => '0'];
        $formform->PotResTree_1truescoremode = [0 => '='];
        $formform->PotResTree_1truescore = [0 => '1'];
        $formform->PotResTree_1truepenalty = [0 => ''];
        $formform->PotResTree_1truenextnode = [0 => '-1'];
        $formform->PotResTree_1trueanswernote = [0 => 'PotResTree_1-1-T'];
        $formform->PotResTree_1truefeedback = [0 => ['text' => '', 'format' => '1', 'itemid' => 0]];
        $formform->PotResTree_1falsescoremode = [0 => '='];
        $formform->PotResTree_1falsescore = [0 => '0'];
        $formform->PotResTree_1falsepenalty = [0 => ''];
        $formform->PotResTree_1falsenextnode = [0 => '-1'];
        $formform->PotResTree_1falseanswernote = [0 => 'PotResTree_1-1-F'];
        $formform->PotResTree_1falsefeedback = [0 => ['text' => '', 'format' => '1', 'itemid' => 0]];

        $formform->questionsimplify = '1';
        $formform->assumepositive = '0';
        $formform->assumereal = '0';
        $formform->prtcorrect = ['text' => 'Correct answer, well done!', 'format' => '1', 'itemid' => 0];
        $formform->prtpartiallycorrect = ['text' => 'Your answer is partially correct!', 'format' => '1', 'itemid' => 0];
        $formform->prtincorrect = ['text' => 'Incorrect answer :-(', 'format' => '1', 'itemid' => 0];
        $formform->decimals = '.';
        $formform->scientificnotation = '*10';
        $formform->multiplicationsign = 'dot';
        $formform->sqrtsign = '1';
        $formform->complexno = 'i';
        $formform->inversetrig = 'cos-1';
        $formform->logicsymbol = 'lang';
        $formform->matrixparens = '[';
        $formform->numhints = 2;
        $formform->hint = [
            0 => ['text' => 'Hint 1<br>', 'format' => '1', 'itemid' => '0'],
            1 => ['text' => '<p>Hint 2<br></p>', 'format' => '1', 'itemid' => '0'],
        ];
        $formform->qtype = 'stack';

        return $formform;
    }

    /**
     * @return qtype_stack_question the question from the test2.xml file.
     */
    public static function make_stack_question_test2() {
        $q = self::make_a_stack_question();

        $q->stackversion = '2019072900';
        $q->name = 'test-2';
        $q->questionvariables = 'orderless(y,x); a:3; f(x):=x^2; b:f(a); ta:y+x';
        $q->questiontext = 'Expand
                            \[ (x-2)(x-3) = x^2-[[input:ans1]] x+[[input:ans2]]. \]
                            [[validation:ans1</IEfeedback>
                            [[validation:ans2</IEfeedback>';

        $q->specificfeedback = '[[feedback:PotResTree_1]]';

        $q->inputs['ans1'] = stack_input_factory::make(
                    'algebraic', 'ans1', '5', null, ['boxWidth' => 3]);
        $q->inputs['ans2'] = stack_input_factory::make(
                    'algebraic', 'ans2', '6', null, ['boxWidth' => 3]);

        $prt = json_decode('{"name":"PotResTree_1","id":"0","value":1,"feedbackstyle":1,"autosimplify":true,
            "feedbackvariables":"",
            "firstnodename":"",
            "nodes":[
                {"id":0,"nodename":"0","quiet":false,
                 "sans":"x^2-ans1*x+ans2","tans":"(x-2)*(x-3)","answertest":"AlgEquiv","testoptions":"",
                 "falsenextnode":"-1","falseanswernote":"PotResTree_1-0-0",
                 "falsescore":0,"falsescoremode":"=",
                 "falsepenalty":0.1,"falsefeedback":"","falsefeedbackformat":"1",
                 "truenextnode":"-1","trueanswernote":"PotResTree_1-0-1",
                 "truescore":1,"truescoremode":"=",
                 "truepenalty":0.1,"truefeedback":"","truefeedbackformat":"1"
                }
              ]}');
        $q->prts[$prt->name] = new stack_potentialresponse_tree_lite($prt, $prt->value, $q);

        return $q;
    }

    /**
     * @return qtype_stack_question the question from the test3.xml file.
     */
    public static function make_stack_question_test3() {
        $q = self::make_a_stack_question();

        $q->name = 'test-3';
        $q->questiontext = '<p>1. Give an example of an odd function by typing
                                      an expression which represents it.
                                      $f_1(x)=$ [[input:ans1]].
                                      [[validation:ans1]]
                                      [[feedback:odd]]</p>
                                <p>2. Give an example of an even function.
                                      $f_2(x)=$ [[input:ans2]].
                                      [[validation:ans2]]
                                      [[feedback:even]]</p>
                                <p>3. Give an example of a function which is odd and even.
                                      $f_3(x)=$ [[input:ans3]].
                                      [[validation:ans3]]
                                      [[feedback:oddeven]]</p>
                                <p>4. Is the answer to 3. unique? [[input:ans4]]
                                      (Or are there many different possibilities.)
                                      [[validation:ans4]]
                                      [[feedback:unique]]</p>';
        $q->specificfeedback = '';
        $q->penalty = 0.4;

        $options = new stack_options();
        $q->inputs['ans1'] = stack_input_factory::make('algebraic', 'ans1', 'x^3', $options,
                        ['boxWidth' => 15, 'lowestTerms' => false, 'sameType' => false]);
        $q->inputs['ans2'] = stack_input_factory::make('algebraic', 'ans2', 'x^4', $options,
                        ['boxWidth' => 15, 'lowestTerms' => false, 'sameType' => false]);
        $q->inputs['ans3'] = stack_input_factory::make('algebraic', 'ans3', '0', $options,
                        ['boxWidth' => 15, 'lowestTerms' => false, 'sameType' => false]);
        $q->inputs['ans4'] = stack_input_factory::make('boolean', 'ans4', 'true', $options);
        $q->prts = [];

        $prt = new stdClass;
        $prt->name              = 'odd';
        $prt->id                = 0;
        $prt->value             = 0.25;
        $prt->feedbackstyle     = 1;
        $prt->feedbackvariables = 'sa:subst(x=-x,ans1)+ans1;';
        $prt->firstnodename     = '0';
        $prt->nodes             = [];
        $prt->autosimplify      = true;

        $newnode = new stdClass;
        $newnode->id                  = '0';
        $newnode->nodename            = '0';
        $newnode->description         = '';
        $newnode->sans                = 'sa';
        $newnode->tans                = '0';
        $newnode->answertest          = 'AlgEquiv';
        $newnode->testoptions         = '';
        $newnode->quiet               = false;
        $newnode->falsescore          = '0';
        $newnode->falsescoremode      = '=';
        $newnode->falsepenalty        = $q->penalty;
        $newnode->falsefeedback       = 'Your answer is not an odd function. Look, \[ f(x)+f(-x)={@sa@} \neq 0.\]';
        $newnode->falsefeedbackformat = '1';
        $newnode->falseanswernote     = 'odd-0-0';
        $newnode->falsenextnode       = '-1';
        $newnode->truescore           = '1';
        $newnode->truescoremode       = '=';
        $newnode->truepenalty         = $q->penalty;
        $newnode->truefeedback        = '';
        $newnode->truefeedbackformat  = '1';
        $newnode->trueanswernote      = 'odd-0-1';
        $newnode->truenextnode        = '-1';
        $prt->nodes[] = $newnode;

        $q->prts[$prt->name] = new stack_potentialresponse_tree_lite($prt, $prt->value, $q);

        $prt = new stdClass;
        $prt->name              = 'even';
        $prt->id                = 1;
        $prt->value             = 0.25;
        $prt->feedbackstyle     = 1;
        $prt->feedbackvariables = 'sa:subst(x=-x,ans2)-ans2;';
        $prt->firstnodename     = '0';
        $prt->nodes             = [];
        $prt->autosimplify      = true;

        $newnode = new stdClass;
        $newnode->id                  = '0';
        $newnode->nodename            = '0';
        $newnode->description         = '';
        $newnode->sans                = 'sa';
        $newnode->tans                = '0';
        $newnode->answertest          = 'AlgEquiv';
        $newnode->testoptions         = '';
        $newnode->quiet               = false;
        $newnode->falsescore          = '0';
        $newnode->falsescoremode      = '=';
        $newnode->falsepenalty        = $q->penalty;
        $newnode->falsefeedback       = 'Your answer is not an even function. Look, \[ f(x)-f(-x)={@sa@} \neq 0.\]';
        $newnode->falsefeedbackformat = '1';
        $newnode->falseanswernote     = 'even-0-0';
        $newnode->falsenextnode       = '-1';
        $newnode->truescore           = '1';
        $newnode->truescoremode       = '=';
        $newnode->truepenalty         = $q->penalty;
        $newnode->truefeedback        = '';
        $newnode->truefeedbackformat  = '1';
        $newnode->trueanswernote      = 'even-0-1';
        $newnode->truenextnode        = '-1';
        $prt->nodes[] = $newnode;

        $q->prts[$prt->name] = new stack_potentialresponse_tree_lite($prt, $prt->value, $q);

        $prt = new stdClass;
        $prt->name              = 'oddeven';
        $prt->id                = 2;
        $prt->value             = 0.25;
        $prt->feedbackstyle     = 1;
        $prt->feedbackvariables = 'sa1:ans3+subst(x=-x,ans3); sa2:ans3-subst(x=-x,ans3);';
        $prt->firstnodename     = '0';
        $prt->nodes             = [];
        $prt->autosimplify      = true;

        $newnode = new stdClass;
        $newnode->id                  = '0';
        $newnode->nodename            = '0';
        $newnode->description         = '';
        $newnode->sans                = 'sa1';
        $newnode->tans                = '0';
        $newnode->answertest          = 'AlgEquiv';
        $newnode->testoptions         = '';
        $newnode->quiet               = false;
        $newnode->truescore           = '0.5';
        $newnode->truescoremode       = '=';
        $newnode->truepenalty         = $q->penalty;
        $newnode->truefeedback        = '';
        $newnode->truefeedbackformat  = '1';
        $newnode->trueanswernote      = 'oddeven-0-1';
        $newnode->truenextnode        = '1';
        $newnode->falsescore          = '0';
        $newnode->falsescoremode      = '=';
        $newnode->falsepenalty        = $q->penalty;
        $newnode->falsefeedback       = 'Your answer is not an odd function. Look, \[ f(x)+f(-x)={@sa1@} \neq 0.\]';
        $newnode->falsefeedbackformat = '1';
        $newnode->falseanswernote     = 'oddeven-0-0';
        $newnode->falsenextnode       = '1';
        $prt->nodes[] = $newnode;
        $newnode = new stdClass;
        $newnode->id                  = '1';
        $newnode->nodename            = '1';
        $newnode->description         = 'Descript of node 1';
        $newnode->sans                = 'sa2';
        $newnode->tans                = '0';
        $newnode->answertest          = 'AlgEquiv';
        $newnode->testoptions         = '';
        $newnode->quiet               = false;
        $newnode->falsescore          = '0';
        $newnode->falsescoremode      = '+';
        $newnode->falsepenalty        = $q->penalty;
        $newnode->falsefeedback       = 'Your answer is not an even function. Look, \[ f(x)-f(-x)={@sa2@} \neq 0.\]';
        $newnode->falsefeedbackformat = '1';
        $newnode->falseanswernote     = 'oddeven-1-0';
        $newnode->falsenextnode       = '-1';
        $newnode->truescore           = '0.5';
        $newnode->truescoremode       = '+';
        $newnode->truepenalty         = $q->penalty;
        $newnode->truefeedback        = '';
        $newnode->truefeedbackformat  = '1';
        $newnode->trueanswernote      = 'oddeven-1-1';
        $newnode->truenextnode        = '-1';
        $prt->nodes[] = $newnode;

        $q->prts[$prt->name] = new stack_potentialresponse_tree_lite($prt, $prt->value, $q);

        $prt = new stdClass;
        $prt->name              = 'unique';
        $prt->id                = 3;
        $prt->value             = 0.25;
        $prt->feedbackstyle     = '1';
        $prt->feedbackvariables = '';
        $prt->firstnodename     = '0';
        $prt->nodes             = [];
        $prt->autosimplify      = true;

        $newnode = new stdClass;
        $newnode->id                  = '0';
        $newnode->nodename            = '0';
        $newnode->description         = '';
        $newnode->sans                = 'ans4';
        $newnode->tans                = 'true';
        $newnode->answertest          = 'AlgEquiv';
        $newnode->testoptions         = '';
        $newnode->quiet               = false;
        $newnode->falsescore          = '0';
        $newnode->falsescoremode      = '=';
        $newnode->falsepenalty        = '1';
        $newnode->falsefeedback       = '';
        $newnode->falsefeedbackformat = '1';
        $newnode->falseanswernote     = 'unique-0-0';
        $newnode->falsenextnode       = '-1';
        $newnode->truescore           = '1';
        $newnode->truescoremode       = '=';
        $newnode->truepenalty         = '1';
        $newnode->truefeedback        = '';
        $newnode->truefeedbackformat  = '1';
        $newnode->trueanswernote      = 'unique-0-1';
        $newnode->truenextnode        = '-1';
        $prt->nodes[] = $newnode;

        $q->prts[$prt->name] = new stack_potentialresponse_tree_lite($prt, $prt->value, $q);

        $q->hints = [
            new question_hint(1, 'Hint 1', FORMAT_HTML),
            new question_hint(2, 'Hint 2', FORMAT_HTML),
        ];

        $q->deployedseeds = [];

        return $q;
    }

    /**
     * @return qtype_stack_question the question from the test3.xml file.
     */
    public static function make_stack_question_test3_penalty0_1() {
        $q = self::make_a_stack_question();

        $q->stackversion = '2019072900';
        $q->name = 'test-3,penalty-0.1';
        $q->questiontext = '<p>1. Give an example of an odd function by typing
                                  an expression which represents it.
                                  $f_1(x)=$ [[input:ans1]].
                                  [[validation:ans1]]
                                  [[feedback:odd]]</p>
                            <p>2. Give an example of an even function.
                                  $f_2(x)=$ [[input:ans2]].
                                  [[validation:ans2]]
                                  [[feedback:even]]</p>
                            <p>3. Give an example of a function which is odd and even.
                                  $f_3(x)=$ [[input:ans3]].
                                  [[validation:ans3]]
                                  [[feedback:oddeven]]</p>
                            <p>4. Is the answer to 3. unique? [[input:ans4]]
                                  (Or are there many different possibilities.)
                                  [[validation:ans4]]
                                  [[feedback:unique]]</p>';

        $q->inputs['ans1'] = stack_input_factory::make(
                        'algebraic', 'ans1', 'x^3', null, ['boxWidth' => 15]);
        $q->inputs['ans2'] = stack_input_factory::make(
                        'algebraic', 'ans2', 'x^4', null, ['boxWidth' => 15]);
        $q->inputs['ans3'] = stack_input_factory::make(
                        'algebraic', 'ans3', '0', null, ['boxWidth' => 15]);
        $q->inputs['ans4'] = stack_input_factory::make(
                        'boolean',   'ans4', 'true');

        $q->penalty = 0.1;

        $q->prts = [];

        $prt = new stdClass;
        $prt->name              = 'odd';
        $prt->id                = 0;
        $prt->value             = 0.25;
        $prt->feedbackstyle     = 1;
        $prt->feedbackvariables = 'sa:subst(x=-x,ans1)+ans1;';
        $prt->firstnodename     = '0';
        $prt->nodes             = [];
        $prt->autosimplify      = true;

        $newnode = new stdClass;
        $newnode->id                  = '0';
        $newnode->nodename            = '0';
        $newnode->description         = '';
        $newnode->sans                = 'sa';
        $newnode->tans                = '0';
        $newnode->answertest          = 'AlgEquiv';
        $newnode->testoptions         = '';
        $newnode->quiet               = false;
        $newnode->falsescore          = '0';
        $newnode->falsescoremode      = '=';
        $newnode->falsepenalty        = '0.3';
        $newnode->falsefeedback       = 'Your answer is not an odd function. Look, \[ f(x)+f(-x)={@sa@} \neq 0.\]';
        $newnode->falsefeedbackformat = '1';
        $newnode->falseanswernote     = 'odd-0-0';
        $newnode->falsenextnode       = '-1';
        $newnode->truescore           = '1';
        $newnode->truescoremode       = '=';
        $newnode->truepenalty         = '0.3';
        $newnode->truefeedback        = '';
        $newnode->truefeedbackformat  = '1';
        $newnode->trueanswernote      = 'odd-0-1';
        $newnode->truenextnode        = '-1';
        $prt->nodes[] = $newnode;

        $q->prts[$prt->name] = new stack_potentialresponse_tree_lite($prt, $prt->value, $q);

        $prt = new stdClass;
        $prt->name              = 'even';
        $prt->id                = 1;
        $prt->value             = 0.25;
        $prt->feedbackstyle     = 1;
        $prt->feedbackvariables = 'sa:subst(x=-x,ans2)-ans2;';
        $prt->firstnodename     = '0';
        $prt->nodes             = [];
        $prt->autosimplify      = true;

        $newnode = new stdClass;
        $newnode->id                  = '0';
        $newnode->nodename            = '0';
        $newnode->description         = '';
        $newnode->sans                = 'sa';
        $newnode->tans                = '0';
        $newnode->answertest          = 'AlgEquiv';
        $newnode->testoptions         = '';
        $newnode->quiet               = false;
        $newnode->falsescore          = '0';
        $newnode->falsescoremode      = '=';
        $newnode->falsepenalty        = '0.3';
        $newnode->falsefeedback       = 'Your answer is not an even function. Look, \[ f(x)-f(-x)={@sa@} \neq 0.\]';
        $newnode->falsefeedbackformat = '1';
        $newnode->falseanswernote     = 'even-0-0';
        $newnode->falsenextnode       = '-1';
        $newnode->truescore           = '1';
        $newnode->truescoremode       = '=';
        $newnode->truepenalty         = '0.3';
        $newnode->truefeedback        = '';
        $newnode->truefeedbackformat  = '1';
        $newnode->trueanswernote      = 'even-0-1';
        $newnode->truenextnode        = '-1';
        $prt->nodes[] = $newnode;

        $q->prts[$prt->name] = new stack_potentialresponse_tree_lite($prt, $prt->value, $q);

        $prt = new stdClass;
        $prt->name              = 'oddeven';
        $prt->id                = 2;
        $prt->value             = 0.25;
        $prt->feedbackstyle     = 1;
        $prt->feedbackvariables = 'sa1:ans3+subst(x=-x,ans3); sa2:ans3-subst(x=-x,ans3);';
        $prt->firstnodename     = '0';
        $prt->nodes             = [];
        $prt->autosimplify      = true;

        $newnode = new stdClass;
        $newnode->id                  = '0';
        $newnode->nodename            = '0';
        $newnode->description         = '';
        $newnode->sans                = 'sa1';
        $newnode->tans                = '0';
        $newnode->answertest          = 'AlgEquiv';
        $newnode->testoptions         = '';
        $newnode->quiet               = false;
        $newnode->falsescore          = '0';
        $newnode->falsescoremode      = '=';
        $newnode->falsepenalty        = $q->penalty;
        $newnode->falsefeedback       = 'Your answer is not an odd function. Look, \[ f(x)+f(-x)={@sa1@} \neq 0.\]';
        $newnode->falsefeedbackformat = '1';
        $newnode->falseanswernote     = 'oddeven-0-0';
        $newnode->falsenextnode       = '1';
        $newnode->truescore           = '0.5';
        $newnode->truescoremode       = '=';
        $newnode->truepenalty         = $q->penalty;
        $newnode->truefeedback        = '';
        $newnode->truefeedbackformat  = '1';
        $newnode->trueanswernote      = 'oddeven-0-1';
        $newnode->truenextnode        = '1';
        $prt->nodes[] = $newnode;
        $newnode = new stdClass;
        $newnode->id                  = '1';
        $newnode->nodename            = '1';
        $newnode->description         = '';
        $newnode->sans                = 'sa2';
        $newnode->tans                = '0';
        $newnode->answertest          = 'AlgEquiv';
        $newnode->testoptions         = '';
        $newnode->quiet               = false;
        $newnode->falsescore          = '0';
        $newnode->falsescoremode      = '+';
        $newnode->falsepenalty        = $q->penalty;
        $newnode->falsefeedback       = 'Your answer is not an even function. Look, \[ f(x)-f(-x)={@sa2@} \neq 0.\]';
        $newnode->falsefeedbackformat = '1';
        $newnode->falseanswernote     = 'oddeven-1-0';
        $newnode->falsenextnode       = '-1';
        $newnode->truescore           = '0.5';
        $newnode->truescoremode       = '+';
        $newnode->truepenalty         = $q->penalty;
        $newnode->truefeedback        = '';
        $newnode->truefeedbackformat  = '1';
        $newnode->trueanswernote      = 'oddeven-1-1';
        $newnode->truenextnode        = '-1';
        $prt->nodes[] = $newnode;

        $q->prts[$prt->name] = new stack_potentialresponse_tree_lite($prt, $prt->value, $q);

        $prt = new stdClass;
        $prt->name              = 'unique';
        $prt->id                = 3;
        $prt->value             = 0.25;
        $prt->feedbackstyle     = '1';
        $prt->feedbackvariables = '';
        $prt->firstnodename     = '0';
        $prt->nodes             = [];
        $prt->autosimplify      = true;

        $newnode = new stdClass;
        $newnode->id                  = '0';
        $newnode->nodename            = '0';
        $newnode->description         = '';
        $newnode->sans                = 'ans4';
        $newnode->tans                = 'true';
        $newnode->answertest          = 'AlgEquiv';
        $newnode->testoptions         = '';
        $newnode->quiet               = false;
        $newnode->falsescore          = '0';
        $newnode->falsescoremode      = '=';
        $newnode->falsepenalty        = $q->penalty;
        $newnode->falsefeedback       = '';
        $newnode->falsefeedbackformat = '1';
        $newnode->falseanswernote     = 'unique-0-0';
        $newnode->falsenextnode       = '-1';
        $newnode->truescore           = '1';
        $newnode->truescoremode       = '=';
        $newnode->truepenalty         = $q->penalty;
        $newnode->truefeedback        = '';
        $newnode->truefeedbackformat  = '1';
        $newnode->trueanswernote      = 'unique-0-1';
        $newnode->truenextnode        = '-1';
        $prt->nodes[] = $newnode;

        $q->prts[$prt->name] = new stack_potentialresponse_tree_lite($prt, $prt->value, $q);

        $q->hints = [
            new question_hint(1, 'Hint 1', FORMAT_HTML),
            new question_hint(2, 'Hint 2', FORMAT_HTML),
        ];

        $q->deployedseeds = [];

        return $q;
    }

    /**
     * @return qtype_stack_question the question from the test4.xml file.
     */
    public static function make_stack_question_test4() {
        $q = self::make_a_stack_question();

        $q->stackversion = '2019072900';
        $q->name = 'test-4';
        $q->questionvariables = 'p : x^2';
        $q->questiontext = 'Below is a sketch of a graph. Find an algebraic expression which represents it.
                            {@plot(p,[x,-2,2])@}
                            $f(x)=$ [[input:ans1]].
                            [[validation:ans1]]';
        $q->specificfeedback = '[[feedback:plots]]';
        $q->generalfeedback = 'The graph {@plot(p,[x,-2,2])@} has algebraic expression \[ f(x)={@p@}. \]';
        $q->qtype = question_bank::get_qtype('stack');

        $q->inputs['ans1'] = stack_input_factory::make(
                        'algebraic', 'ans1', 'x^2', null, ['boxWidth' => 15]);

        $prt = new stdClass;
        $prt->name              = 'plots';
        $prt->id                = 0;
        $prt->value             = 1;
        $prt->feedbackstyle     = 1;
        $prt->feedbackvariables = '';
        $prt->firstnodename     = '0';
        $prt->nodes             = [];
        $prt->autosimplify      = true;
        $newnode = new stdClass;
        $newnode->id                  = '0';
        $newnode->nodename            = '0';
        $newnode->description         = '';
        $newnode->sans                = 'ans1';
        $newnode->tans                = 'x^2';
        $newnode->answertest          = 'AlgEquiv';
        $newnode->testoptions         = '';
        $newnode->quiet               = false;
        $newnode->truescore           = '1';
        $newnode->truescoremode       = '=';
        $newnode->truepenalty         = $q->penalty;
        $newnode->truefeedback        = '';
        $newnode->truefeedbackformat  = '1';
        $newnode->trueanswernote      = 'plots-0-1';
        $newnode->truenextnode        = '-1';
        $newnode->falsescore          = '0';
        $newnode->falsescoremode      = '=';
        $newnode->falsepenalty        = $q->penalty;
        $newnode->falsefeedback       =
            'Your answer and my answer are plotted below. Look they are different! {@plot([p,ans1],[x,-2,2])@}';
        $newnode->falsefeedbackformat = '1';
        $newnode->falseanswernote     = 'plots-0-0';
        $newnode->falsenextnode       = '-1';
        $prt->nodes[] = $newnode;
        $q->prts[$prt->name] = new stack_potentialresponse_tree_lite($prt, $prt->value, $q);

        return $q;
    }

    /**
     * @return qtype_stack_question in which the expected answer in the true/false input is generated from the question variables...
     *                              and the question variables define the scores in the PRT.
     */
    public static function make_stack_question_test_boolean() {
        $q = self::make_a_stack_question();

        $q->stackversion = '2019072900';
        $q->name = 'test-boolean';
        $q->questionvariables = 'ta:true;sc1:0.2;sc2:0.9;';
        $q->questiontext = 'What is {@ta@}? [[input:ans1]]
                           [[validation:ans1]]';

        $q->specificfeedback = '[[feedback:firsttree]]';
        $q->penalty = 0.3; // Non-zero and not the default.

        $q->inputs['ans1'] = stack_input_factory::make('boolean', 'ans1', 'ta');

        $q->options->set_option('simplify', true);

        $prt = new stdClass;
        $prt->name              = 'firsttree';
        $prt->id                = 0;
        $prt->value             = 1;
        $prt->feedbackstyle     = 1;
        $prt->feedbackvariables = '';
        $prt->firstnodename     = '0';
        $prt->nodes             = [];
        $prt->autosimplify      = true;
        $newnode = new stdClass;
        $newnode->id                  = '0';
        $newnode->nodename            = '0';
        $newnode->description         = '';
        $newnode->sans                = 'ans1';
        $newnode->tans                = 'ta';
        $newnode->answertest          = 'AlgEquiv';
        $newnode->testoptions         = '';
        $newnode->quiet               = false;
        $newnode->falsescore          = 'sc1';
        $newnode->falsescoremode      = '=';
        $newnode->falsepenalty        = $q->penalty;
        $newnode->falsefeedback       = '';
        $newnode->falsefeedbackformat = '1';
        $newnode->falseanswernote     = 'firsttree-1-F';
        $newnode->falsenextnode       = '-1';
        $newnode->truescore           = 'sc2';
        $newnode->truescoremode       = '=';
        $newnode->truepenalty         = $q->penalty;
        $newnode->truefeedback        = '';
        $newnode->truefeedbackformat  = '1';
        $newnode->trueanswernote      = 'firsttree-1-T';
        $newnode->truenextnode        = '-1';
        $prt->nodes[] = $newnode;
        $q->prts[$prt->name] = new stack_potentialresponse_tree_lite($prt, $prt->value, $q);

        return $q;
    }

    /**
     * @return qtype_stack_question the question from the test8.xml file.
     */
    public static function make_stack_question_test8() {
        $q = self::make_a_stack_question();

        $q->stackversion = '2019072900';
        $q->name = 'test-8';
        $q->questionvariables = "n : 3; " .
                                "p : 4; " .
                                "ta : setify(makelist(p*%e^(2*%pi*%i*k/n),k,1,n));";
        $q->questiontext = '<p>Find all the complex solutions of the equation \[ z^{@n@}={@p^n@}.\]
                            Enter your answer as a set of numbers.
                            [[input:ans1]]</p>
                            [[validation:ans1]]';

        $q->specificfeedback = '[[feedback:ans]]';
        $q->questionnote = '{@ta@}';

        $q->inputs['ans1'] = stack_input_factory::make(
                        'algebraic', 'ans1', 'ta', null,
                        ['boxWidth' => 20, 'syntaxHint' => '{?,?,...,?}']);

        $feedbackvars = 'a1 : listify(ans1);' .
                        'a1 : maplist(lambda([x],x^n-p^n),a1);' .
                        'a1 : setify(a1)';

        $prt = new stdClass;
        $prt->name              = 'ans';
        $prt->id                = 0;
        $prt->value             = 1;
        $prt->feedbackstyle     = 1;
        $prt->feedbackvariables = $feedbackvars;
        $prt->firstnodename     = '0';
        $prt->nodes             = [];
        $prt->autosimplify      = true;
        $newnode = new stdClass;
        $newnode->id                  = '0';
        $newnode->nodename            = '0';
        $newnode->description         = '';
        $newnode->sans                = 'ans1';
        $newnode->tans                = 'ta';
        $newnode->answertest          = 'AlgEquiv';
        $newnode->testoptions         = '';
        $newnode->quiet               = false;
        $newnode->falsescore          = '0';
        $newnode->falsescoremode      = '=';
        $newnode->falsepenalty        = $q->penalty;
        $newnode->falsefeedback       = '';
        $newnode->falsefeedbackformat = '1';
        $newnode->falseanswernote     = 'ans-0-F';
        $newnode->falsenextnode       = '1';
        $newnode->truescore           = 'sc2';
        $newnode->truescoremode       = '=';
        $newnode->truepenalty         = $q->penalty;
        $newnode->truefeedback        = '';
        $newnode->truefeedbackformat  = '1';
        $newnode->trueanswernote      = 'ans-0-T';
        $newnode->truenextnode        = '-1';
        $prt->nodes[] = $newnode;
        $newnode = new stdClass;
        $newnode->id                  = '1';
        $newnode->nodename            = '1';
        $newnode->description         = '';
        $newnode->sans                = 'ans1';
        $newnode->tans                = '{p}';
        $newnode->answertest          = 'AlgEquiv';
        $newnode->testoptions         = '';
        $newnode->quiet               = false;
        $newnode->falsescore          = '0';
        $newnode->falsescoremode      = '=';
        $newnode->falsepenalty        = $q->penalty;
        $newnode->falsefeedback       = '';
        $newnode->falsefeedbackformat = '1';
        $newnode->falseanswernote     = 'ans-1-F';
        $newnode->falsenextnode       = '2';
        $newnode->truescore           = '0';
        $newnode->truescoremode       = '=';
        $newnode->truepenalty         = $q->penalty;
        $newnode->truefeedback        = '<p>There are more answers that just the single real number.
                 Please consider complex solutions to this problem!</p>';
        $newnode->truefeedbackformat  = '1';
        $newnode->trueanswernote      = 'ans-1-T';
        $newnode->truenextnode        = '-1';
        $prt->nodes[] = $newnode;
        $newnode = new stdClass;
        $newnode->id                  = '2';
        $newnode->nodename            = '2';
        $newnode->description         = '';
        $newnode->sans                = 'a1';
        $newnode->tans                = '{0}';
        $newnode->answertest          = 'AlgEquiv';
        $newnode->testoptions         = '';
        $newnode->quiet               = true;
        $newnode->falsescore          = '0';
        $newnode->falsescoremode      = '=';
        $newnode->falsepenalty        = $q->penalty;
        $newnode->falsefeedback       = '';
        $newnode->falsefeedbackformat = '1';
        $newnode->falseanswernote     = 'ans-2-F';
        $newnode->falsenextnode       = '-1';
        $newnode->truescore           = 'sc2';
        $newnode->truescoremode       = '=';
        $newnode->truepenalty         = $q->penalty;
        $newnode->truefeedback        =
            'All your answers satisfy the equation. But, you have missed some of the solutions.';
        $newnode->truefeedbackformat  = '1';
        $newnode->trueanswernote      = 'ans-2-T';
        $newnode->truenextnode        = '-1';
        $prt->nodes[] = $newnode;
        $q->prts[$prt->name] = new stack_potentialresponse_tree_lite($prt, $prt->value, $q);

        return $q;
    }

    /**
     * @return qtype_stack_question the question from the test9.xml file.
     */
    public static function make_stack_question_test9() {
        $q = self::make_a_stack_question();

        $q->stackversion = '2019072900';
        $q->name = 'test-9';
        $q->questionvariables = 'b : rand([-2,-3,-4,2,3,4]); ta1 : b; ta2 : rand([-2,-3,-4,2,3,4]); ' .
                'a : b*ta2; p : a*x+b; f : lambda([x],if (x<0) then p else ta1*exp(ta2*x))';
        $q->questiontext = '<p>Let $f(x)$ be a real function defined on the interval $[-1,1]$ by the following formula.</p>
                            \[
                            f(x) = \left\{ \begin{array}{ll}
                            {@p@} & \text{if }x<0, \\
                            a_1 e^{a_2\ x} & \text{if }x\geq 0.
                            \end{array}
                            \right.
                            \]
                            <p>Find the values of the parameters so that $f$ is continuous and differentiable of order $1$.
                            $a_1=[[input:ans1]]
                            $a_2=[[input:ans2]]</p>
                            [[validation:ans1]][[validation:ans2]]';

        $q->specificfeedback = '[[feedback:prt1]]';

        $q->generalfeedback  = '<p>We would like the function to be continuous at $x=0$, so we would like
                                \[ {@p@}=a_1e^{a_2\ x}\]
                                when evaluated at $x=0$. That is to say we want
                                \[ {@b@} = a_1 \]
                                If we differentiate both sides of the above equation with respect to $x$ we
                                may match up the gradients. This gives us
                                \[ {@a@}=a_1a_2.\]
                                Solving this gives $a_2 = {@a/b@}$.</p>
                                <p>Hence the full answer is
                                \[
                                f(x) = \left\{ \begin{array}{ll}
                                {@p@} & \text{if }x<0, \\
                                {@ta1@} e^{{@ta2@} x} & \text{if }x\geq 0.
                                \end{array}
                                \right.
                                \]</p>
                                <p>We can sketch the graph of this function as follows.
                                {@plot(f(x),[x,-1,1])@}</p>';

        $q->questionnote = '\[ a_1={@ta1@},\ a_2={@ta2@}.\]';

        $q->inputs['ans1'] = stack_input_factory::make(
                                    'algebraic', 'ans1', 'ta1', null, ['boxWidth' => 4]);
        $q->inputs['ans2'] = stack_input_factory::make(
                                    'algebraic', 'ans2', 'ta2', null, ['boxWidth' => 4]);

        $prt = new stdClass;
        $prt->name              = 'prt1';
        $prt->id                = 0;
        $prt->value             = 1;
        $prt->feedbackstyle     = 1;
        $prt->feedbackvariables = 'g:lambda([x],if (x<0) then p else ans1*exp(ans2*x));';
        $prt->firstnodename     = '0';
        $prt->nodes             = [];
        $prt->autosimplify      = true;
        $newnode = new stdClass;
        $newnode->id                  = '0';
        $newnode->nodename            = '0';
        $newnode->description         = '';
        $newnode->sans                = '[ans1,ans2]';
        $newnode->tans                = '[ta1,ta2]';
        $newnode->answertest          = 'Int';
        $newnode->testoptions         = 'x';
        $newnode->quiet               = false;
        $newnode->falsescore          = '0';
        $newnode->falsescoremode      = '=';
        $newnode->falsepenalty        = $q->penalty;
        $newnode->falsefeedback       = 'Compare your answer with the correct one {@plot([f(x),g(x)],[x,-1,1])@}';
        $newnode->falsefeedbackformat = '1';
        $newnode->falseanswernote     = 'prt1-1-F';
        $newnode->falsenextnode       = '-1';
        $newnode->truescore           = '1';
        $newnode->truescoremode       = '=';
        $newnode->truepenalty         = $q->penalty;
        $newnode->truefeedback        = '';
        $newnode->truefeedbackformat  = '1';
        $newnode->trueanswernote      = 'prt1-1-T';
        $newnode->truenextnode        = '-1';
        $prt->nodes[] = $newnode;
        $q->prts[$prt->name] = new stack_potentialresponse_tree_lite($prt, $prt->value, $q);

        return $q;
    }

    /**
     * @return qtype_stack_question.
     */
    public static function make_stack_question_divide() {
        $q = self::make_a_stack_question();

        $q->stackversion = '2019072900';
        $q->name = 'divide';
        $q->questiontext = '<p>Give me \(x\) such that \[1/x = 2\]</p>
                            <p>\(x = \) [[input:ans1]]</p>
                            [[validation:ans1]]';

        $q->specificfeedback = '[[feedback:prt1]]';
        $q->penalty = 0.3333333;

        $q->inputs['ans1'] = stack_input_factory::make(
                'algebraic', 'ans1', '1/2', null, ['boxWidth' => 5]);

        $prt = new stdClass;
        $prt->name              = 'prt1';
        $prt->id                = 0;
        $prt->value             = 1;
        $prt->feedbackstyle     = 1;
        // Question authors are now able to use errcatch.
        $prt->feedbackvariables = 'S1:errcatch(1/(ans1-7));';
        // Without the errcatch we would get [RUNTIME_FV_ERROR].
        $prt->firstnodename     = '0';
        $prt->nodes             = [];
        $prt->autosimplify      = true;
        $newnode = new stdClass;
        $newnode->id                  = '0';
        $newnode->nodename            = '0';
        $newnode->description         = '';
        $newnode->sans                = '1/ans1';
        $newnode->tans                = '2';
        $newnode->answertest          = 'AlgEquiv';
        $newnode->testoptions         = '';
        $newnode->quiet               = false;
        $newnode->falsescore          = '0';
        $newnode->falsescoremode      = '=';
        $newnode->falsepenalty        = $q->penalty;
        $newnode->falsefeedback       = '';
        $newnode->falsefeedbackformat = '1';
        $newnode->falseanswernote     = 'prt1-1-F';
        $newnode->falsenextnode       = '-1';
        $newnode->truescore           = '1';
        $newnode->truescoremode       = '=';
        $newnode->truepenalty         = $q->penalty;
        $newnode->truefeedback        = '';
        $newnode->truefeedbackformat  = '1';
        $newnode->trueanswernote      = 'prt1-1-T';
        $newnode->truenextnode        = '-1';
        $prt->nodes[] = $newnode;
        $q->prts[$prt->name] = new stack_potentialresponse_tree_lite($prt, $prt->value, $q);

        return $q;
    }

    /**
     * @return qtype_stack_question a question using a numerical precision answertest.
     */
    public static function make_stack_question_numsigfigs() {
        $q = self::make_a_stack_question();

        $q->stackversion = '2019072900';
        $q->name = 'test-numsigfigs';
        $q->questionvariables = '';
        $q->questiontext = 'Please round $\pi$ to three significant figures. [[input:ans1]]
                           [[validation:ans1]]';

        $q->specificfeedback = '[[feedback:firsttree]]';
        $q->penalty = 0.1;

        $q->inputs['ans1'] = stack_input_factory::make(
                'algebraic', 'ans1', '3.14', null, ['boxWidth' => 5, 'forbidFloats' => false]);

        $q->options->set_option('simplify', false);

        $prt = new stdClass;
        $prt->name              = 'firsttree';
        $prt->id                = 0;
        $prt->value             = 1;
        $prt->feedbackstyle     = 1;
        $prt->feedbackvariables = '';
        $prt->firstnodename     = '0';
        $prt->nodes             = [];
        $prt->autosimplify      = false;
        $newnode = new stdClass;
        $newnode->id                  = '0';
        $newnode->nodename            = '0';
        $newnode->description         = '';
        $newnode->sans                = 'ans1';
        $newnode->tans                = '3.14';
        $newnode->answertest          = 'NumSigFigs';
        $newnode->testoptions         = '3';
        $newnode->quiet               = false;
        $newnode->falsescore          = '0';
        $newnode->falsescoremode      = '=';
        $newnode->falsepenalty        = $q->penalty;
        $newnode->falsefeedback       = '';
        $newnode->falsefeedbackformat = '1';
        $newnode->falseanswernote     = 'firsttree-1-F';
        $newnode->falsenextnode       = '-1';
        $newnode->truescore           = '1';
        $newnode->truescoremode       = '=';
        $newnode->truepenalty         = $q->penalty;
        $newnode->truefeedback        = '';
        $newnode->truefeedbackformat  = '1';
        $newnode->trueanswernote      = 'firsttree-1-T';
        $newnode->truenextnode        = '-1';
        $prt->nodes[] = $newnode;
        $q->prts[$prt->name] = new stack_potentialresponse_tree_lite($prt, $prt->value, $q);

        return $q;
    }

    /**
     * @return qtype_stack_question a question using a numerical precision answertest, with trailing zeros.
     */
    public static function make_stack_question_numsigfigszeros() {
        $q = self::make_a_stack_question();

        $q->stackversion = '2019072900';
        $q->name = 'test-numsigfigszeros';
        $q->questionvariables = '';
        $q->questiontext = 'Please type in four hundredths to three significant figures. [[input:ans1]]
                           [[validation:ans1]]';

        $q->specificfeedback = '[[feedback:firsttree]]';
        $q->penalty = 0.2;

        $q->inputs['ans1'] = stack_input_factory::make(
                'numerical', 'ans1', '0.040', null, ['boxWidth' => 5, 'forbidFloats' => false]);

        $q->options->set_option('simplify', false);

        $prt = new stdClass;
        $prt->name              = 'firsttree';
        $prt->id                = 0;
        $prt->value             = 1;
        $prt->feedbackstyle     = 1;
        $prt->feedbackvariables = '';
        $prt->firstnodename     = '0';
        $prt->nodes             = [];
        $prt->autosimplify      = false;
        $newnode = new stdClass;
        $newnode->id                  = '0';
        $newnode->nodename            = '0';
        $newnode->description         = '';
        $newnode->sans                = 'ans1';
        $newnode->tans                = '0.040';
        $newnode->answertest          = 'NumSigFigs';
        $newnode->testoptions         = '2';
        $newnode->quiet               = false;
        $newnode->falsescore          = '0';
        $newnode->falsescoremode      = '=';
        $newnode->falsepenalty        = $q->penalty;
        $newnode->falsefeedback       = '';
        $newnode->falsefeedbackformat = '1';
        $newnode->falseanswernote     = 'firsttree-1-F';
        $newnode->falsenextnode       = '-1';
        $newnode->truescore           = '1';
        $newnode->truescoremode       = '=';
        $newnode->truepenalty         = $q->penalty;
        $newnode->truefeedback        = '';
        $newnode->truefeedbackformat  = '1';
        $newnode->trueanswernote      = 'firsttree-1-T';
        $newnode->truenextnode        = '-1';
        $prt->nodes[] = $newnode;
        $q->prts[$prt->name] = new stack_potentialresponse_tree_lite($prt, $prt->value, $q);

        return $q;
    }

    /**
     * @return qtype_stack_question the question which uses numerical precision feedback variables.
     */
    public static function make_stack_question_numdpsfeedbackvars() {
        $q = self::make_a_stack_question();

        $q->stackversion = '2021052100';
        $q->name = 'numdpsfeedbackvars';
        $q->questiontext = '<p>Give me two random numbers to 3 decimal places.</p>
                            <p>[[input:ans1]] [[validation:ans1]]</p>
                            <p>[[input:ans2]] [[validation:ans2]]</p>';

        $q->specificfeedback = '[[feedback:prt1]]';
        $q->penalty = 0.3;

        $q->inputs['ans1'] = stack_input_factory::make(
            'numerical', 'ans1', '0.356', null, ['boxWidth' => 5]);
        $q->inputs['ans2'] = stack_input_factory::make(
            'numerical', 'ans2', '3.14', null, ['boxWidth' => 5]);

        $feedbackvars = new stack_cas_keyval('sa:min(ans1,ans2);', null, null);

        // Check if the smallest of the two random numbers is within 3dps of pi.
        $prt = new stdClass;
        $prt->name              = 'prt1';
        $prt->id                = 0;
        $prt->value             = 1;
        $prt->feedbackstyle     = 1;
        $prt->feedbackvariables = 'sa:min(ans1,ans2);';
        $prt->firstnodename     = '0';
        $prt->nodes             = [];
        $prt->autosimplify      = true;
        $newnode = new stdClass;
        $newnode->id                  = '0';
        $newnode->nodename            = '0';
        $newnode->description         = '';
        $newnode->sans                = 'sa';
        $newnode->tans                = '3.14';
        $newnode->answertest          = 'NumDecPlaces';
        $newnode->testoptions         = '3';
        $newnode->quiet               = false;
        $newnode->falsescore          = '0';
        $newnode->falsescoremode      = '=';
        $newnode->falsepenalty        = $q->penalty;
        $newnode->falsefeedback       = 'Your answer was received as {@sa@}.';
        $newnode->falsefeedbackformat = '1';
        $newnode->falseanswernote     = 'prt1-1-F';
        $newnode->falsenextnode       = '-1';
        $newnode->truescore           = '1';
        $newnode->truescoremode       = '=';
        $newnode->truepenalty         = $q->penalty;
        $newnode->truefeedback        = 'You are within 3 dps of pi! Was that random?!';
        $newnode->truefeedbackformat  = '1';
        $newnode->trueanswernote      = 'prt1-1-T';
        $newnode->truenextnode        = '-1';
        $prt->nodes[] = $newnode;
        $q->prts[$prt->name] = new stack_potentialresponse_tree_lite($prt, $prt->value, $q);

        return $q;
    }

    /**
     * @return qtype_stack_question a question using a numerical precision answertest.
     */
    public static function make_stack_question_units() {
        $q = self::make_a_stack_question();

        $q->stackversion = '2019072900';
        $q->name = 'test-units';
        $q->questionvariables = '';
        $q->questiontext = 'Please round type in gravity to three significant figures. [[input:ans1]]
        [[validation:ans1]]';

        $q->specificfeedback = '[[feedback:firsttree]]';
        $q->penalty = 0.2; // Non-zero and not the default.

        $q->inputs['ans1'] = stack_input_factory::make(
                'units', 'ans1', '9.81*m/s^2', null, ['boxWidth' => 5, 'forbidFloats' => false]);

        $q->options->set_option('simplify', false);

        $prt = new stdClass;
        $prt->name              = 'firsttree';
        $prt->id                = 0;
        $prt->value             = 1;
        $prt->feedbackstyle     = 1;
        $prt->feedbackvariables = '';
        $prt->firstnodename     = '0';
        $prt->nodes             = [];
        $prt->autosimplify      = true;

        $newnode = new stdClass;
        $newnode->id                  = '0';
        $newnode->nodename            = '0';
        $newnode->description         = '';
        $newnode->sans                = 'ans1';
        $newnode->tans                = '9.81*m/s^2';
        $newnode->answertest          = 'Units';
        $newnode->testoptions         = '3';
        $newnode->quiet               = false;
        $newnode->falsescore          = '0';
        $newnode->falsescoremode      = '=';
        $newnode->falsepenalty        = $q->penalty;
        $newnode->falsefeedback       = '';
        $newnode->falsefeedbackformat = '1';
        $newnode->falseanswernote     = 'firsttree-1-F';
        $newnode->falsenextnode       = '-1';
        $newnode->truescore           = '1';
        $newnode->truescoremode       = '=';
        $newnode->truepenalty         = $q->penalty;
        $newnode->truefeedback        = '';
        $newnode->truefeedbackformat  = '1';
        $newnode->trueanswernote      = 'firsttree-1-T';
        $newnode->truenextnode        = '-1';
        $prt->nodes[] = $newnode;

        $q->prts[$prt->name] = new stack_potentialresponse_tree_lite($prt, $prt->value, $q);

        return $q;
    }

    /**
     * @return qtype_stack_question a question using a numerical precision answertest.
     */
    public static function make_stack_question_unitsoptions() {
        $q = self::make_a_stack_question();

        $q->stackversion = '2019072900';
        $q->name = 'test-units-options';
        $q->questionvariables = 'n0:3';
        $q->questiontext = 'Please round type in gravity to {@n0@} significant figures. [[input:ans1]]
        [[validation:ans1]]';

        $q->specificfeedback = '[[feedback:firsttree]]';
        $q->penalty = 0.2; // Non-zero and not the default.

        $q->inputs['ans1'] = stack_input_factory::make(
                'units', 'ans1', '9.81*m/s^2', null, ['boxWidth' => 5, 'forbidFloats' => false]);

        $q->options->set_option('simplify', false);

        $prt = new stdClass;
        $prt->name              = 'firsttree';
        $prt->id                = 0;
        $prt->value             = 1;
        $prt->feedbackstyle     = 1;
        $prt->feedbackvariables = '';
        $prt->firstnodename     = '0';
        $prt->nodes             = [];
        $prt->autosimplify      = true;

        $newnode = new stdClass;
        $newnode->id                  = '0';
        $newnode->nodename            = '0';
        $newnode->description         = '';
        $newnode->sans                = 'ans1';
        $newnode->tans                = '9.81*m/s^2';
        $newnode->answertest          = 'Units';
        $newnode->testoptions         = '[n0,n0-1]';
        $newnode->quiet               = false;
        $newnode->falsescore          = '0';
        $newnode->falsescoremode      = '=';
        $newnode->falsepenalty        = $q->penalty;
        $newnode->falsefeedback       = '';
        $newnode->falsefeedbackformat = '1';
        $newnode->falseanswernote     = 'firsttree-1-F';
        $newnode->falsenextnode       = '-1';
        $newnode->truescore           = '1';
        $newnode->truescoremode       = '=';
        $newnode->truepenalty         = $q->penalty;
        $newnode->truefeedback        = '';
        $newnode->truefeedbackformat  = '1';
        $newnode->trueanswernote      = 'firsttree-1-T';
        $newnode->truenextnode        = '-1';
        $prt->nodes[] = $newnode;

        $q->prts[$prt->name] = new stack_potentialresponse_tree_lite($prt, $prt->value, $q);

        return $q;
    }

    /**
     * @return qtype_stack_question a question using a units and algebraic input.
     */
    public static function make_stack_question_unitsmulti() {
        $q = self::make_a_stack_question();

        $q->stackversion = '2024092500';
        $q->name = 'test-units-multi';
        $q->questionvariables = '';
        $q->questiontext = 'What is the rate law for the reaction? [[input:ans1]] [[validation:ans1]] ' .
           'What is the rate constant for this reaction? [[input:ans2]] [[validation:ans2]]';

        $q->specificfeedback = '[[feedback:firsttree]]';
        $q->penalty = 0.2; // Non-zero and not the default.

        // This example illsutrated the extra "nounits" option.
        $q->inputs['ans1'] = stack_input_factory::make(
            'algebraic', 'ans1', 'A*k', null, ['boxWidth' => 5, 'options' => 'nounits']);
        $q->inputs['ans2'] = stack_input_factory::make(
            'units', 'ans2', '0.0061/s', null, ['boxWidth' => 5, 'forbidFloats' => false]);

        $q->options->set_option('simplify', false);

        $prt = new stdClass;
        $prt->name              = 'firsttree';
        $prt->id                = 0;
        $prt->value             = 1;
        $prt->feedbackstyle     = 1;
        $prt->feedbackvariables = '';
        $prt->firstnodename     = '0';
        $prt->nodes             = [];
        $prt->autosimplify      = true;

        $newnode = new stdClass;
        $newnode->id                  = '0';
        $newnode->nodename            = '0';
        $newnode->description         = '';
        $newnode->sans                = 'ans1';
        $newnode->tans                = 'A*k';
        $newnode->answertest          = 'AlgEquiv';
        $newnode->testoptions         = '';
        $newnode->quiet               = false;
        $newnode->falsescore          = '0';
        $newnode->falsescoremode      = '=';
        $newnode->falsepenalty        = $q->penalty;
        $newnode->falsefeedback       = '';
        $newnode->falsefeedbackformat = '1';
        $newnode->falseanswernote     = 'firsttree-1-F';
        $newnode->falsenextnode       = '-1';
        $newnode->truescore           = '1';
        $newnode->truescoremode       = '=';
        $newnode->truepenalty         = $q->penalty;
        $newnode->truefeedback        = '';
        $newnode->truefeedbackformat  = '1';
        $newnode->trueanswernote      = 'firsttree-1-T';
        $newnode->truenextnode        = '-1';
        $prt->nodes[] = $newnode;

        $q->prts[$prt->name] = new stack_potentialresponse_tree_lite($prt, $prt->value, $q);

        return $q;
    }

    /**
     * @return qtype_stack_question a question using equivalence reasoning to solve a quadratic equation.
     */
    public static function make_stack_question_equiv_quad() {
        $q = self::make_a_stack_question();

        $q->stackversion = '2019072900';
        $q->name = 'test-equiv-quad';
        $q->questionvariables = 'ta:[x^2-3*x+2=0,(x-2)*(x-1)=0,x=2 or x=1]; p:first(ta)';
        $q->questiontext = 'Solve the following equation: {@p@}. [[input:ans1]]
        [[validation:ans1]]';

        $q->specificfeedback = '[[feedback:firsttree]]';
        $q->penalty = 0.2; // Non-zero and not the default.

        $q->inputs['ans1'] = stack_input_factory::make(
                'equiv', 'ans1', 'ta', null, ['boxWidth' => 20, 'forbidFloats' => false]);

        $q->options->set_option('simplify', false);

        $prt = new stdClass;
        $prt->name              = 'firsttree';
        $prt->id                = 0;
        $prt->value             = 1;
        $prt->feedbackstyle     = 1;
        $prt->feedbackvariables = '';
        $prt->firstnodename     = '0';
        $prt->nodes             = [];
        $prt->autosimplify      = false;

        $newnode = new stdClass;
        $newnode->id                  = '0';
        $newnode->nodename            = '0';
        $newnode->description         = '';
        $newnode->sans                = 'ans1';
        $newnode->tans                = 'ta';
        $newnode->answertest          = 'Equiv';
        $newnode->testoptions         = '';
        $newnode->quiet               = false;
        $newnode->falsescore          = '0';
        $newnode->falsescoremode      = '=';
        $newnode->falsepenalty        = $q->penalty;
        $newnode->falsefeedback       = '';
        $newnode->falsefeedbackformat = '1';
        $newnode->falseanswernote     = 'firsttree-1-F';
        $newnode->falsenextnode       = '-1';
        $newnode->truescore           = '1';
        $newnode->truescoremode       = '=';
        $newnode->truepenalty         = $q->penalty;
        $newnode->truefeedback        = '';
        $newnode->truefeedbackformat  = '1';
        $newnode->trueanswernote      = 'firsttree-1-T';
        $newnode->truenextnode        = '-1';
        $prt->nodes[] = $newnode;

        $q->prts[$prt->name] = new stack_potentialresponse_tree_lite($prt, $prt->value, $q);

        return $q;
    }

    /**
     * @return qtype_stack_question with two PRTs with different values.
     */
    public static function make_stack_question_1input2prts() {
        $q = self::make_a_stack_question();

        $q->stackversion = '2019072900';
        $q->name = '1input2prts';
        $q->questionvariables = '';
        $q->questiontext = 'Enter a multiple of 6: [[input:ans1]]
                            [[validation:ans1]]';
        $q->generalfeedback = '';

        $q->specificfeedback = '[[feedback:prt1]] [[feedback:prt2]]';
        $q->penalty = 0.25;

        $q->inputs['ans1'] = stack_input_factory::make(
                        'algebraic', 'ans1', '6', null, ['boxWidth' => 15]);

        $prt = new stdClass;
        $prt->name              = 'prt1';
        $prt->id                = 0;
        $prt->value             = 0.2;
        $prt->feedbackstyle     = 1;
        $prt->feedbackvariables = '';
        $prt->firstnodename     = '0';
        $prt->nodes             = [];
        $prt->autosimplify      = true;
        $newnode = new stdClass;
        $newnode->id                  = '0';
        $newnode->nodename            = '0';
        $newnode->description         = '';
        $newnode->sans                = 'mod(ans1,2)';
        $newnode->tans                = '0';
        $newnode->answertest          = 'AlgEquiv';
        $newnode->testoptions         = '';
        $newnode->quiet               = false;
        $newnode->falsescore          = '0';
        $newnode->falsescoremode      = '=';
        $newnode->falsepenalty        = $q->penalty;
        $newnode->falsefeedback       = 'Your answer is not even.';
        $newnode->falsefeedbackformat = '1';
        $newnode->falseanswernote     = 'prt1-0-0';
        $newnode->falsenextnode       = '-1';
        $newnode->truescore           = '1';
        $newnode->truescoremode       = '=';
        $newnode->truepenalty         = $q->penalty;
        $newnode->truefeedback        = '';
        $newnode->truefeedbackformat  = '1';
        $newnode->trueanswernote      = 'prt1-0-1';
        $newnode->truenextnode        = '-1';
        $prt->nodes[] = $newnode;
        $q->prts[$prt->name] = new stack_potentialresponse_tree_lite($prt, $prt->value, $q);

        $prt = new stdClass;
        $prt->name              = 'prt2';
        $prt->id                = 1;
        $prt->value             = 0.8;
        $prt->feedbackstyle     = 1;
        $prt->feedbackvariables = '';
        $prt->firstnodename     = '0';
        $prt->nodes             = [];
        $prt->autosimplify      = true;
        $newnode = new stdClass;
        $newnode->id                  = '0';
        $newnode->nodename            = '0';
        $newnode->description         = '';
        $newnode->sans                = 'mod(ans1,3)';
        $newnode->tans                = '0';
        $newnode->answertest          = 'AlgEquiv';
        $newnode->testoptions         = '';
        $newnode->quiet               = false;
        $newnode->falsescore          = '0';
        $newnode->falsescoremode      = '=';
        $newnode->falsepenalty        = $q->penalty;
        $newnode->falsefeedback       = 'Your answer is not divisible by three.';
        $newnode->falsefeedbackformat = '1';
        $newnode->falseanswernote     = 'prt2-0-0';
        $newnode->falsenextnode       = '-1';
        $newnode->truescore           = '1';
        $newnode->truescoremode       = '=';
        $newnode->truepenalty         = $q->penalty;
        $newnode->truefeedback        = '';
        $newnode->truefeedbackformat  = '1';
        $newnode->trueanswernote      = 'prt2-0-1';
        $newnode->truenextnode        = '-1';
        $prt->nodes[] = $newnode;
        $q->prts[$prt->name] = new stack_potentialresponse_tree_lite($prt, $prt->value, $q);

        return $q;
    }

    /**
     * @return qtype_stack_question a information item, rather than a question.
     */
    public static function make_stack_question_information() {
        $q = self::make_a_stack_question();

        $q->stackversion = '2019072900';
        $q->name = 'Information item';
        $q->questionvariables = 'a:7;';
        $q->questiontext = '\[a = {@a@}\].';
        $q->generalfeedback = 'The end.';

        $q->specificfeedback = '';
        $q->defaultmark = 0;
        $q->length = 0;

        return $q;
    }

    /**
     * @return qtype_stack_question with variable grades
     */
    public static function get_stack_question_data_variable_grade() {
        question_bank::load_question_definition_classes('stack');
        $qdata = new stdClass();
        test_question_maker::initialise_question_data($qdata);

        $qdata->qtype = 'stack';
        $qdata->name = 'test-0';
        $qdata->questiontext = 'What is $1+1$? [[input:ans1]]
                                [[validation:ans1]]';
        $qdata->generalfeedback = '';

        $qdata->options = new stdClass();
        $qdata->options->id                        = 0;
        $qdata->options->stackversion              = get_config('qtype_stack', 'version');
        $qdata->options->questionvariables         = '';
        $qdata->options->specificfeedback          = '[[feedback:firsttree]]';
        $qdata->options->specificfeedbackformat    = FORMAT_HTML;
        $qdata->options->questionnote              = '';
        $qdata->options->questionnoteformat        = FORMAT_HTML;
        $qdata->options->questiondescription       = 'This is a rather wonderful question!';
        $qdata->options->questiondescriptionformat = FORMAT_HTML;
        $qdata->options->questionsimplify          = 1;
        $qdata->options->assumepositive            = 0;
        $qdata->options->assumereal                = 0;
        $qdata->options->prtcorrect                = self::DEFAULT_CORRECT_FEEDBACK;
        $qdata->options->prtcorrectformat          = FORMAT_HTML;
        $qdata->options->prtpartiallycorrect       = self::DEFAULT_PARTIALLYCORRECT_FEEDBACK;
        $qdata->options->prtpartiallycorrectformat = FORMAT_HTML;
        $qdata->options->prtincorrect              = self::DEFAULT_INCORRECT_FEEDBACK;
        $qdata->options->prtincorrectformat        = FORMAT_HTML;
        $qdata->options->decimals                  = '.';
        $qdata->options->scientificnotation        = '*10';
        $qdata->options->multiplicationsign        = 'dot';
        $qdata->options->sqrtsign                  = 1;
        $qdata->options->complexno                 = 'i';
        $qdata->options->inversetrig               = 'cos-1';
        $qdata->options->logicsymbol               = 'lang';
        $qdata->options->matrixparens              = '[';
        $qdata->options->variantsselectionseed     = '';
        $qdata->options->compiledcache             = null;

        $input = new stdClass();
        $input->name               = 'ans1';
        $input->id                 = 0;
        $input->questionid         = 0;
        $input->type               = 'algebraic';
        $input->tans               = '2';
        $input->boxsize            = 5;
        $input->strictsyntax       = 1;
        $input->insertstars        = 0;
        $input->syntaxhint         = '';
        $input->syntaxattribute    = 0;
        $input->forbidwords        = '';
        $input->allowwords         = '';
        $input->forbidfloat        = 1;
        $input->requirelowestterms = 0;
        $input->checkanswertype    = 0;
        $input->mustverify         = 1;
        $input->showvalidation     = 1;
        $input->options            = '';
        $qdata->inputs['ans1'] = $input;

        $prt = new stdClass();
        $prt->name              = 'firsttree';
        $prt->id                = 0;
        $prt->id                = '0';
        $prt->questionid        = '0';
        $prt->value             = 1;
        $prt->autosimplify      = 1;
        $prt->feedbackstyle     = 1;
        $prt->feedbackvariables = 'grade: 1 nograde: 0';
        $prt->firstnodename     = '0';

        $node = new stdClass();
        $node->id                  = 0;
        $node->questionid          = 0;
        $node->prtname             = 'firsttree';
        $node->nodename            = '0';
        $node->description         = '';
        $node->answertest          = 'EqualComAss';
        $node->sans                = 'ans1';
        $node->tans                = '2';
        $node->testoptions         = '';
        $node->quiet               = 0;
        $node->truescoremode       = '=';
        $node->truescore           = 'grade';
        $node->truepenalty         = 0;
        $node->truenextnode        = -1;
        $node->trueanswernote      = 'firsttree-1-T';
        $node->truefeedback        = '';
        $node->truefeedbackformat  = FORMAT_HTML;
        $node->falsescoremode      = '=';
        $node->falsescore          = 'nograde';
        $node->falsepenalty        = 0;
        $node->falsenextnode       = -1;
        $node->falseanswernote     = 'firsttree-1-F';
        $node->falsefeedback       = '';
        $node->falsefeedbackformat = FORMAT_HTML;
        $prt->nodes['0'] = $node;
        $qdata->prts['firsttree'] = $prt;

        $qdata->deployedseeds = ['12345'];

        $qtest = new stack_question_test('Basic test of question', ['ans1' => '2']);
        $qtest->add_expected_result('firsttree', new stack_potentialresponse_tree_state(
                1, true, 1, 0, '', ['firsttree-1-T']));
        $qdata->testcases[1] = $qtest;

        return $qdata;
    }

    /**
     * @return qtype_stack_question a 'survey' item. Inputs, but no grading.
     */
    public static function make_stack_question_survey() {
        $q = self::make_a_stack_question();

        $q->stackversion = '2019072900';
        $q->name = 'Survey';
        $q->questionvariables = '';
        $q->questiontext = 'What is your favourite equation? [[input:ans1]]
                           [[validation:ans1]]';

        $q->specificfeedback = '';
        $q->defaultmark = 0;

        $q->inputs['ans1'] = stack_input_factory::make(
                'algebraic', 'ans1', '2', null, ['boxWidth' => 15, 'sameType' => false]);

        return $q;
    }

    /**
     * @return qtype_stack_question a very elementary question assuming single letter variables.
     */
    public static function make_stack_question_single_char_vars() {
        $q = self::make_a_stack_question();

        $q->stackversion = '2019072900';
        $q->name = 'single_char_vars';
        $q->questionvariables = 'a:sin(x*y);';
        $q->questiontext = 'What is {@a@}? [[input:ans1]]
                               [[validation:ans1]]';

        $q->specificfeedback = '[[feedback:firsttree]]';
        $q->penalty = 0.3; // Non-zero and not the default.

        $q->inputs['ans1'] = stack_input_factory::make(
                    'algebraic', 'ans1', '2', null, ['boxWidth' => 5, 'insertStars' => 2]);

        $q->options->set_option('simplify', false);

        $prt = new stdClass;
        $prt->name              = 'firsttree';
        $prt->id                = 0;
        $prt->value             = 1;
        $prt->feedbackstyle     = 1;
        $prt->feedbackvariables = '';
        $prt->firstnodename     = '0';
        $prt->nodes             = [];
        $prt->autosimplify      = false;
        $newnode = new stdClass;
        $newnode->id                  = '0';
        $newnode->nodename            = '0';
        $newnode->description         = '';
        $newnode->sans                = 'ans1+0';
        $newnode->tans                = 'sin(x*y)';
        $newnode->answertest          = 'AlgEquiv';
        $newnode->testoptions         = '';
        $newnode->quiet               = false;
        $newnode->falsescore          = '0';
        $newnode->falsescoremode      = '=';
        $newnode->falsepenalty        = $q->penalty;
        $newnode->falsefeedback       = '';
        $newnode->falsefeedbackformat = '1';
        $newnode->falseanswernote     = 'firsttree-1-F';
        $newnode->falsenextnode       = '-1';
        $newnode->truescore           = '1';
        $newnode->truescoremode       = '=';
        $newnode->truepenalty         = $q->penalty;
        $newnode->truefeedback        = '';
        $newnode->truefeedbackformat  = '1';
        $newnode->trueanswernote      = 'firsttree-1-T';
        $newnode->truenextnode        = '-1';
        $prt->nodes[] = $newnode;
        $q->prts[$prt->name] = new stack_potentialresponse_tree_lite($prt, $prt->value, $q);

        return $q;
    }

    public static function make_stack_question_runtime_prt_err() {
        $q = self::make_a_stack_question();

        $q->stackversion = '2019072900';
        $q->name = 'runtime_prt_err';
        $q->questionvariables = "";
        $q->questiontext = '<p>Give an example of a system of equations with a unique solution.</p>' .
            '<p>[[input:ans1]] [[validation:ans1]]</p>';

        $q->specificfeedback = '[[feedback:Result]]';
        $q->questionnote = '';

        $q->inputs['ans1'] = stack_input_factory::make(
                'algebraic', 'ans1', '[x+y=1,x-y=1]', null, ['boxWidth' => 25]);

        // This will generate a runtime error in the feedback variables.
        $feedbackvars = new stack_cas_keyval('');

        $prt = new stdClass;
        $prt->name              = 'Result';
        $prt->id                = 0;
        $prt->value             = 1;
        $prt->feedbackstyle     = 1;
        $prt->feedbackvariables = 'S1:1/(7-rhs(first(ans1)));';
        $prt->firstnodename     = '0';
        $prt->nodes             = [];
        $prt->autosimplify      = true;
        $newnode = new stdClass;
        $newnode->id                  = '0';
        $newnode->nodename            = '0';
        $newnode->description         = '';
        $newnode->sans                = 'all_listp(equationp,ans1)';
        $newnode->tans                = 'true';
        $newnode->answertest          = 'AlgEquiv';
        $newnode->testoptions         = '';
        $newnode->quiet               = true;
        $newnode->falsescore          = '0';
        $newnode->falsescoremode      = '=';
        $newnode->falsepenalty        = 0;
        $newnode->falsefeedback       = 'Your answer should be a list of equations!';
        $newnode->falsefeedbackformat = '1';
        $newnode->falseanswernote     = 'Result-0-F';
        $newnode->falsenextnode       = '-1';
        $newnode->truescore           = '0';
        $newnode->truescoremode       = '=';
        $newnode->truepenalty         = $q->penalty;
        $newnode->truefeedback        = 'Your answer is a list of equations.';
        $newnode->truefeedbackformat  = '1';
        $newnode->trueanswernote      = 'Result-0-T';
        $newnode->truenextnode        = '1';
        $prt->nodes[] = $newnode;
        $newnode = new stdClass;
        $newnode->id                  = '1';
        $newnode->nodename            = '1';
        $newnode->description         = '';
        $newnode->sans                = 'solve(ans1,listofvars(ans1))';
        $newnode->tans                = '[]';
        $newnode->answertest          = 'AlgEquiv';
        $newnode->testoptions         = '';
        $newnode->quiet               = true;
        $newnode->falsescore          = '0';
        $newnode->falsescoremode      = '=';
        $newnode->falsepenalty        = $q->penalty;
        $newnode->falsefeedback       = 'You have some solutions!';
        $newnode->falsefeedbackformat = '1';
        $newnode->falseanswernote     = 'Result-1-F';
        $newnode->falsenextnode       = '2';
        $newnode->truescore           = '0';
        $newnode->truescoremode       = '=';
        $newnode->truepenalty         = $q->penalty;
        $newnode->truefeedback        = 'Your equations have no solution!';
        $newnode->truefeedbackformat  = '1';
        $newnode->trueanswernote      = 'Result-1-T';
        $newnode->truenextnode        = '-2';
        $prt->nodes[] = $newnode;
        $newnode = new stdClass;
        $newnode->id                  = '2';
        $newnode->nodename            = '2';
        $newnode->description         = '';
        $newnode->sans                = 'length(solve(ans1,listofvars(ans1)))';
        $newnode->tans                = '1';
        $newnode->answertest          = 'AlgEquiv';
        $newnode->testoptions         = '';
        $newnode->quiet               = true;
        $newnode->falsescore          = '0';
        $newnode->falsescoremode      = '=';
        $newnode->falsepenalty        = $q->penalty;
        $newnode->falsefeedback       = 'You should have only one solution.';
        $newnode->falsefeedbackformat = '1';
        $newnode->falseanswernote     = 'Result-2-F';
        $newnode->falsenextnode       = '-1';
        $newnode->truescore           = '1';
        $newnode->truescoremode       = '=';
        $newnode->truepenalty         = $q->penalty;
        $newnode->truefeedback        = 'Good, you have one solution.';
        $newnode->truefeedbackformat  = '1';
        $newnode->trueanswernote      = 'Result-2-T';
        $newnode->truenextnode        = '-2';
        $prt->nodes[] = $newnode;

        $q->prts[$prt->name] = new stack_potentialresponse_tree_lite($prt, $prt->value, $q);

        return $q;
    }

    public static function make_stack_question_runtime_ses_err() {
        $q = self::make_a_stack_question();

        $q->stackversion = '2019072900';
        $q->name = 'runtime_ses_err';
        $q->questionvariables = "p:1/1+x^2);ta:diff(p,x);";
        $q->questiontext = '<p>Give an example of a system of equations with a unique solution.</p>' .
            '<p>[[input:ans1]] [[validation:ans1]]</p>';

        $q->specificfeedback = '[[feedback:Result]]';
        $q->questionnote = '';

        $q->inputs['ans1'] = stack_input_factory::make(
                'algebraic', 'ans1', 'ta', null, ['boxWidth' => 25]);

        $prt = new stdClass;
        $prt->name              = 'Result';
        $prt->id                = 0;
        $prt->value             = 1;
        $prt->feedbackstyle     = 1;
        $prt->feedbackvariables = '';
        $prt->firstnodename     = '0';
        $prt->nodes             = [];
        $prt->autosimplify      = true;
        $newnode = new stdClass;
        $newnode->id                  = '0';
        $newnode->nodename            = '0';
        $newnode->description         = '';
        $newnode->sans                = 'ans1';
        $newnode->tans                = 'ta';
        $newnode->answertest          = 'AlgEquiv';
        $newnode->testoptions         = '';
        $newnode->quiet               = true;
        $newnode->falsescore          = '0';
        $newnode->falsescoremode      = '=';
        $newnode->falsepenalty        = $q->penalty;
        $newnode->falsefeedback       = 'Not quite.';
        $newnode->falsefeedbackformat = '1';
        $newnode->falseanswernote     = 'Result-0-F';
        $newnode->falsenextnode       = '-1';
        $newnode->truescore           = '1';
        $newnode->truescoremode       = '=';
        $newnode->truepenalty         = $q->penalty;
        $newnode->truefeedback        = 'Correct.';
        $newnode->truefeedbackformat  = '1';
        $newnode->trueanswernote      = 'Result-0-T';
        $newnode->truenextnode        = '-1';
        $prt->nodes[] = $newnode;
        $q->prts[$prt->name] = new stack_potentialresponse_tree_lite($prt, $prt->value, $q);

        return $q;
    }

    public static function make_stack_question_runtime_cas_err() {
        $q = self::make_a_stack_question();

        $q->stackversion = '2019072900';
        $q->name = 'runtime_cas_err';
        $q->questionvariables = "p:3;q:3;ta:1;";
        $q->questiontext = '<p>Caculate {@1/(p-q)@}.</p>' .
            '<p>[[input:ans1]] [[validation:ans1]]</p>';

        $q->specificfeedback = '[[feedback:Result]]';
        $q->questionnote = '';

        $q->inputs['ans1'] = stack_input_factory::make(
                'algebraic', 'ans1', 'ta', null, ['boxWidth' => 25]);

        $prt = new stdClass;
        $prt->name              = 'Result';
        $prt->id                = 0;
        $prt->value             = 1;
        $prt->feedbackstyle     = 1;
        $prt->feedbackvariables = '';
        $prt->firstnodename     = '0';
        $prt->nodes             = [];
        $prt->autosimplify      = true;
        $newnode = new stdClass;
        $newnode->id                  = '0';
        $newnode->nodename            = '0';
        $newnode->description         = '';
        $newnode->sans                = 'ans1';
        $newnode->tans                = 'ta';
        $newnode->answertest          = 'AlgEquiv';
        $newnode->testoptions         = '';
        $newnode->quiet               = true;
        $newnode->falsescore          = '0';
        $newnode->falsescoremode      = '=';
        $newnode->falsepenalty        = $q->penalty;
        $newnode->falsefeedback       = 'Not quite.';
        $newnode->falsefeedbackformat = '1';
        $newnode->falseanswernote     = 'Result-0-F';
        $newnode->falsenextnode       = '-1';
        $newnode->truescore           = '1';
        $newnode->truescoremode       = '=';
        $newnode->truepenalty         = $q->penalty;
        $newnode->truefeedback        = 'Correct.';
        $newnode->truefeedbackformat  = '1';
        $newnode->trueanswernote      = 'Result-0-T';
        $newnode->truenextnode        = '-1';
        $prt->nodes[] = $newnode;
        $q->prts[$prt->name] = new stack_potentialresponse_tree_lite($prt, $prt->value, $q);

        return $q;
    }

    /**
     * @return stdClass the question from the test0.xml file.
     */
    public static function get_stack_question_data_test0() {
        question_bank::load_question_definition_classes('stack');
        $qdata = new stdClass();
        test_question_maker::initialise_question_data($qdata);

        $qdata->qtype = 'stack';
        $qdata->name = 'test-0';
        $qdata->questiontext = 'What is $1+1$? [[input:ans1]]
                                [[validation:ans1]]';
        $qdata->generalfeedback = '';

        $qdata->options = new stdClass();
        $qdata->options->id                        = 0;
        $qdata->options->stackversion              = get_config('qtype_stack', 'version');
        $qdata->options->questionvariables         = '';
        $qdata->options->specificfeedback          = '[[feedback:firsttree]]';
        $qdata->options->specificfeedbackformat    = FORMAT_HTML;
        $qdata->options->questionnote              = '';
        $qdata->options->questionnoteformat        = FORMAT_HTML;
        $qdata->options->questiondescription       = 'This is a rather wonderful question!';
        $qdata->options->questiondescriptionformat = FORMAT_HTML;
        $qdata->options->questionsimplify          = 1;
        $qdata->options->assumepositive            = 0;
        $qdata->options->assumereal                = 0;
        $qdata->options->prtcorrect                = self::DEFAULT_CORRECT_FEEDBACK;
        $qdata->options->prtcorrectformat          = FORMAT_HTML;
        $qdata->options->prtpartiallycorrect       = self::DEFAULT_PARTIALLYCORRECT_FEEDBACK;
        $qdata->options->prtpartiallycorrectformat = FORMAT_HTML;
        $qdata->options->prtincorrect              = self::DEFAULT_INCORRECT_FEEDBACK;
        $qdata->options->prtincorrectformat        = FORMAT_HTML;
        $qdata->options->decimals                  = '.';
        $qdata->options->scientificnotation        = '*10';
        $qdata->options->multiplicationsign        = 'dot';
        $qdata->options->sqrtsign                  = 1;
        $qdata->options->complexno                 = 'i';
        $qdata->options->inversetrig               = 'cos-1';
        $qdata->options->logicsymbol               = 'lang';
        $qdata->options->matrixparens              = '[';
        $qdata->options->variantsselectionseed     = '';
        $qdata->options->compiledcache             = null;

        $input = new stdClass();
        $input->name               = 'ans1';
        $input->id                 = 0;
        $input->questionid         = 0;
        $input->type               = 'algebraic';
        $input->tans               = '2';
        $input->boxsize            = 5;
        $input->strictsyntax       = 1;
        $input->insertstars        = 0;
        $input->syntaxhint         = '';
        $input->syntaxattribute    = 0;
        $input->forbidwords        = '';
        $input->allowwords         = '';
        $input->forbidfloat        = 1;
        $input->requirelowestterms = 0;
        $input->checkanswertype    = 0;
        $input->mustverify         = 1;
        $input->showvalidation     = 1;
        $input->options            = '';
        $qdata->inputs['ans1'] = $input;

        $prt = new stdClass();
        $prt->name              = 'firsttree';
        $prt->id                = 0;
        $prt->id                = '0';
        $prt->questionid        = '0';
        $prt->value             = 1;
        $prt->autosimplify      = 1;
        $prt->feedbackstyle     = 1;
        $prt->feedbackvariables = '';
        $prt->firstnodename     = '0';

        $node = new stdClass();
        $node->id                  = 0;
        $node->questionid          = 0;
        $node->prtname             = 'firsttree';
        $node->nodename            = '0';
        $node->description         = '';
        $node->answertest          = 'EqualComAss';
        $node->sans                = 'ans1';
        $node->tans                = '2';
        $node->testoptions         = '';
        $node->quiet               = 0;
        $node->truescoremode       = '=';
        $node->truescore           = 1;
        $node->truepenalty         = 0;
        $node->truenextnode        = -1;
        $node->trueanswernote      = 'firsttree-1-T';
        $node->truefeedback        = '';
        $node->truefeedbackformat  = FORMAT_HTML;
        $node->falsescoremode      = '=';
        $node->falsescore          = 0;
        $node->falsepenalty        = 0;
        $node->falsenextnode       = -1;
        $node->falseanswernote     = 'firsttree-1-F';
        $node->falsefeedback       = '';
        $node->falsefeedbackformat = FORMAT_HTML;
        $prt->nodes['0'] = $node;
        $qdata->prts['firsttree'] = $prt;

        $qdata->deployedseeds = ['12345'];

        $qtest = new stack_question_test('Basic test of question', ['ans1' => '2']);
        $qtest->add_expected_result('firsttree', new stack_potentialresponse_tree_state(
                1, true, 1, 0, '', ['firsttree-1-T']));
        $qdata->testcases[1] = $qtest;

        return $qdata;
    }

    /**
     * @return stdClass the question from the test3.xml file.
     */
    public static function get_stack_question_data_test3() {
        question_bank::load_question_definition_classes('stack');
        $qdata = new stdClass();
        test_question_maker::initialise_question_data($qdata);

        $qdata->contextid = context_system::instance()->id;
        $qdata->qtype = 'stack';
        $qdata->name = 'test-3';
        $qdata->questiontext = '<p>1. Give an example of an odd function by typing
                                      an expression which represents it.
                                      $f_1(x)=$ [[input:ans1]].
                                      [[validation:ans1]]
                                      [[feedback:odd]]</p>
                                <p>2. Give an example of an even function.
                                      $f_2(x)=$ [[input:ans2]].
                                      [[validation:ans2]]
                                      [[feedback:even]]</p>
                                <p>3. Give an example of a function which is odd and even.
                                      $f_3(x)=$ [[input:ans3]].
                                      [[validation:ans3]]
                                      [[feedback:oddeven]]</p>
                                <p>4. Is the answer to 3. unique? [[input:ans4]]
                                      (Or are there many different possibilities.)
                                      [[validation:ans4]]
                                      [[feedback:unique]]</p>';
        $qdata->generalfeedback = '';
        $qdata->penalty = 0.4;

        $qdata->options = new stdClass();
        $qdata->options->id                        = 0;
        $qdata->options->stackversion              = get_config('qtype_stack', 'version');
        $qdata->options->questionvariables         = '';
        $qdata->options->specificfeedback          = '';
        $qdata->options->specificfeedbackformat    = FORMAT_HTML;
        $qdata->options->questiondescription       = '';
        $qdata->options->questiondescriptionformat = FORMAT_HTML;
        $qdata->options->questionnote              = '';
        $qdata->options->questionnoteformat        = FORMAT_HTML;
        $qdata->options->questionsimplify          = 1;
        $qdata->options->assumepositive            = 0;
        $qdata->options->assumereal                = 0;
        $qdata->options->markmode                  = 'penalty';
        $qdata->options->prtcorrect                = self::DEFAULT_CORRECT_FEEDBACK;
        $qdata->options->prtcorrectformat          = FORMAT_HTML;
        $qdata->options->prtpartiallycorrect       = self::DEFAULT_PARTIALLYCORRECT_FEEDBACK;
        $qdata->options->prtpartiallycorrectformat = FORMAT_HTML;
        $qdata->options->prtincorrect              = self::DEFAULT_INCORRECT_FEEDBACK;
        $qdata->options->prtincorrectformat        = FORMAT_HTML;
        $qdata->options->decimals                  = '.';
        $qdata->options->scientificnotation        = '*10';
        $qdata->options->multiplicationsign        = 'dot';
        $qdata->options->sqrtsign                  = 1;
        $qdata->options->complexno                 = 'i';
        $qdata->options->inversetrig               = 'cos-1';
        $qdata->options->logicsymbol               = 'lang';
        $qdata->options->matrixparens              = '[';
        $qdata->options->variantsselectionseed     = '';
        $qdata->options->compiledcache             = null;

        $input = new stdClass();
        $input->name               = 'ans1';
        $input->id                 = 0;
        $input->questionid         = 0;
        $input->type               = 'algebraic';
        $input->tans               = 'x^3';
        $input->boxsize            = 15;
        $input->strictsyntax       = 1;
        $input->insertstars        = 0;
        $input->syntaxhint         = '';
        $input->syntaxattribute    = 0;
        $input->forbidwords        = '';
        $input->allowwords         = '';
        $input->forbidfloat        = 1;
        $input->requirelowestterms = 0;
        $input->checkanswertype    = 0;
        $input->mustverify         = 1;
        $input->showvalidation     = 1;
        $input->options            = '';
        $qdata->inputs['ans1'] = $input;

        $input = new stdClass();
        $input->name               = 'ans2';
        $input->id                 = 0;
        $input->questionid         = 0;
        $input->type               = 'algebraic';
        $input->tans               = 'x^4';
        $input->boxsize            = 15;
        $input->strictsyntax       = 1;
        $input->insertstars        = 0;
        $input->syntaxhint         = '';
        $input->syntaxattribute    = 0;
        $input->forbidwords        = '';
        $input->allowwords         = '';
        $input->forbidfloat        = 1;
        $input->requirelowestterms = 0;
        $input->checkanswertype    = 0;
        $input->mustverify         = 1;
        $input->showvalidation     = 1;
        $input->options            = '';
        $qdata->inputs['ans2'] = $input;

        $input = new stdClass();
        $input->name               = 'ans3';
        $input->id                 = 0;
        $input->questionid         = 0;
        $input->type               = 'algebraic';
        $input->tans               = '0';
        $input->boxsize            = 15;
        $input->strictsyntax       = 1;
        $input->insertstars        = 0;
        $input->syntaxhint         = '';
        $input->syntaxattribute    = 0;
        $input->forbidwords        = '';
        $input->allowwords         = '';
        $input->forbidfloat        = 1;
        $input->requirelowestterms = 0;
        $input->checkanswertype    = 0;
        $input->mustverify         = 1;
        $input->showvalidation     = 1;
        $input->options            = '';
        $qdata->inputs['ans3'] = $input;

        $input = new stdClass();
        $input->name               = 'ans4';
        $input->id                 = 0;
        $input->questionid         = 0;
        $input->type               = 'boolean';
        $input->tans               = 'true';
        $input->boxsize            = 15;
        $input->strictsyntax       = 1;
        $input->insertstars        = 0;
        $input->syntaxhint         = '';
        $input->syntaxattribute    = 0;
        $input->forbidwords        = '';
        $input->allowwords         = '';
        $input->forbidfloat        = 1;
        $input->requirelowestterms = 0;
        $input->checkanswertype    = 0;
        $input->mustverify         = 0;
        $input->showvalidation     = 0;
        $input->options            = '';
        $qdata->inputs['ans4'] = $input;

        $prt = new stdClass();
        $prt->name              = 'odd';
        $prt->id                = '0';
        $prt->questionid        = '0';
        $prt->value             = 1;
        $prt->autosimplify      = 1;
        $prt->feedbackstyle     = 1;
        $prt->feedbackvariables = 'sa:subst(x=-x,ans1)+ans1';
        $prt->firstnodename     = '0';

        $node = new stdClass();
        $node->id                  = 0;
        $node->questionid          = 0;
        $node->prtname             = 'odd';
        $node->nodename            = '0';
        $node->description         = 'Check for oddness';
        $node->answertest          = 'AlgEquiv';
        $node->sans                = 'sa';
        $node->tans                = '0';
        $node->testoptions         = null;
        $node->quiet               = 0;
        $node->truescoremode       = '=';
        $node->truescore           = 1;
        $node->truepenalty         = 0.4;
        $node->truenextnode        = -1;
        $node->trueanswernote      = 'odd-0-1';
        $node->truefeedback        = '';
        $node->truefeedbackformat  = FORMAT_HTML;
        $node->falsescoremode      = '=';
        $node->falsescore          = 0;
        $node->falsepenalty        = 0.4;
        $node->falsenextnode       = -1;
        $node->falseanswernote     = 'odd-0-0';
        $node->falsefeedback       = 'Your answer is not an odd function. Look, \[ f(x)+f(-x)={@sa@} \neq 0.\]';
        $node->falsefeedbackformat = FORMAT_HTML;
        $prt->nodes['0'] = $node;
        $qdata->prts['odd'] = $prt;

        $prt = new stdClass();
        $prt->name              = 'even';
        $prt->id                = 1;
        $prt->id                = '0';
        $prt->questionid        = '0';
        $prt->value             = 1;
        $prt->autosimplify      = 1;
        $prt->feedbackstyle     = 1;
        $prt->feedbackvariables = 'sa:subst(x=-x,ans2)-ans2';
        $prt->firstnodename     = '0';

        $node = new stdClass();
        $node->id                  = 0;
        $node->questionid          = 0;
        $node->prtname             = 'even';
        $node->nodename            = '0';
        $node->description         = 'Check for evenness';
        $node->answertest          = 'AlgEquiv';
        $node->sans                = 'sa';
        $node->tans                = '0';
        $node->testoptions         = null;
        $node->quiet               = 0;
        $node->truescoremode       = '=';
        $node->truescore           = 1;
        $node->truepenalty         = 0.4;
        $node->truenextnode        = -1;
        $node->trueanswernote      = 'even-0-1';
        $node->truefeedback        = '';
        $node->truefeedbackformat  = FORMAT_HTML;
        $node->falsescoremode      = '=';
        $node->falsescore          = 0;
        $node->falsepenalty        = 0.4;
        $node->falsenextnode       = -1;
        $node->falseanswernote     = 'even-0-0';
        $node->falsefeedback       = 'Your answer is not an even function. Look, \[ f(x)-f(-x)={@sa@} \neq 0.\]';
        $node->falsefeedbackformat = FORMAT_HTML;
        $prt->nodes['0'] = $node;
        $qdata->prts['even'] = $prt;

        $prt = new stdClass();
        $prt->name              = 'oddeven';
        $prt->id                = 2;
        $prt->id                = '0';
        $prt->questionid        = '0';
        $prt->value             = 1;
        $prt->autosimplify      = 1;
        $prt->feedbackstyle     = 1;
        $prt->feedbackvariables = 'sa1:ans3+subst(x=-x,ans3); sa2:ans3-subst(x=-x,ans3)';
        $prt->firstnodename     = '0';

        $node = new stdClass();
        $node->id                  = 0;
        $node->questionid          = 0;
        $node->prtname             = 'oddeven';
        $node->nodename            = '0';
        $node->description         = 'Check for odd again';
        $node->answertest          = 'AlgEquiv';
        $node->sans                = 'sa1';
        $node->tans                = '0';
        $node->testoptions         = null;
        $node->quiet               = 0;
        $node->truescoremode       = '=';
        $node->truescore           = 0.5;
        $node->truepenalty         = 0.4;
        $node->truenextnode        = 1;
        $node->trueanswernote      = 'oddeven-0-1';
        $node->truefeedback        = '';
        $node->truefeedbackformat  = FORMAT_HTML;
        $node->falsescoremode      = '=';
        $node->falsescore          = 0;
        $node->falsepenalty        = 0.4;
        $node->falsenextnode       = 1;
        $node->falseanswernote     = 'oddeven-0-0';
        $node->falsefeedback       = 'Your answer is not an odd function. Look, \[ f(x)+f(-x)={@sa1@} \neq 0.\]';
        $node->falsefeedbackformat = FORMAT_HTML;
        $prt->nodes['0'] = $node;

        $node = new stdClass();
        $node->id                  = 0;
        $node->questionid          = 0;
        $node->prtname             = 'oddeven';
        $node->nodename            = '1';
        $node->description         = 'Check for even again';
        $node->answertest          = 'AlgEquiv';
        $node->sans                = 'sa2';
        $node->tans                = '0';
        $node->testoptions         = null;
        $node->quiet               = 0;
        $node->truescoremode       = '+';
        $node->truescore           = 0.5;
        $node->truepenalty         = 0.4;
        $node->truenextnode        = -1;
        $node->trueanswernote      = 'oddeven-1-1';
        $node->truefeedback        = '';
        $node->truefeedbackformat  = FORMAT_HTML;
        $node->falsescoremode      = '+';
        $node->falsescore          = 0;
        $node->falsepenalty        = 0.4;
        $node->falsenextnode       = -1;
        $node->falseanswernote     = 'oddeven-1-0';
        $node->falsefeedback       = 'Your answer is not an even function. Look, \[ f(x)-f(-x)={@sa2@} \neq 0.\]';
        $node->falsefeedbackformat = FORMAT_HTML;
        $prt->nodes['1'] = $node;
        $qdata->prts['oddeven'] = $prt;

        $prt = new stdClass();
        $prt->name              = 'unique';
        $prt->id                = 3;
        $prt->id                = '0';
        $prt->questionid        = '0';
        $prt->value             = 1;
        $prt->autosimplify      = 1;
        $prt->feedbackstyle     = 1;
        $prt->feedbackvariables = null;
        $prt->firstnodename     = '0';

        $node = new stdClass();
        $node->id                  = 0;
        $node->questionid          = 0;
        $node->prtname             = 'unique';
        $node->nodename            = '0';
        $node->description         = 'Check unique';
        $node->answertest          = 'AlgEquiv';
        $node->sans                = 'ans4';
        $node->tans                = 'true';
        $node->testoptions         = null;
        $node->quiet               = 0;
        $node->truescoremode       = '=';
        $node->truescore           = 1;
        $node->truepenalty         = 1;
        $node->truenextnode        = -1;
        $node->trueanswernote      = 'unique-0-1';
        $node->truefeedback        = '';
        $node->truefeedbackformat  = FORMAT_HTML;
        $node->falsescoremode      = '=';
        $node->falsescore          = 0;
        $node->falsepenalty        = 1;
        $node->falsenextnode       = -1;
        $node->falseanswernote     = 'unique-0-0';
        $node->falsefeedback       = '';
        $node->falsefeedbackformat = FORMAT_HTML;
        $prt->nodes['0'] = $node;
        $qdata->prts['unique'] = $prt;

        $qdata->deployedseeds = [];
        $qdata->testcases = [];

        $qdata->hints = [
            1 => new question_hint(1, 'Hint 1', FORMAT_HTML),
            2 => new question_hint(2, 'Hint 2', FORMAT_HTML),
        ];

        return $qdata;
    }

    public function get_stack_question_form_data_test3() {
        $formform = new stdClass();

        $formform->category = '2,14';
        $formform->usecurrentcat = '1';
        $formform->categorymoveto = '2,14';
        $formform->name = 'test-3';
        $formform->stackversion = get_config('qtype_stack', 'version');
        $formform->questionvariables = '';
        $formform->variantsselectionseed = '';
        $formform->questiontext = [
            'text' => '<p>1. Give an example of an odd function by typing
                                 an expression which represents it.
                                 $f_1(x)=$ [[input:ans1]].
                                 [[validation:ans1]]
                                 [[feedback:odd]]</p>
                           <p>2. Give an example of an even function.
                                 $f_2(x)=$ [[input:ans2]].
                                 [[validation:ans2]]
                                 [[feedback:even]]</p>
                           <p>3. Give an example of a function which is odd and even.
                                 $f_3(x)=$ [[input:ans3]].
                                 [[validation:ans3]]
                                 [[feedback:oddeven]]</p>
                           <p>4. Is the answer to 3. unique? [[input:ans4]]
                                 (Or are there many different possibilities.)
                                 [[validation:ans4]]
                                 [[feedback:unique]]</p>',
            'format' => '1',
            'itemid' => 815759888,
        ];
        $formform->defaultmark = 4;
        $formform->specificfeedback = [
            'text' => '',
            'format' => '1',
            'itemid' => 137873291,
        ];
        $formform->penalty = 0.40000000000000002;
        $formform->generalfeedback = [
            'text' => '',
            'format' => '1',
            'itemid' => 250226104,
        ];
        $formform->questionnote = [
            'text' => '',
            'format' => '1',
            'itemid' => 12346789,
        ];
        $formform->questiondescription = [
            'text' => '',
            'format' => '1',
            'itemid' => 25022610,
        ];
        $formform->ans1type = 'algebraic';
        $formform->ans1modelans = 'x^3';
        $formform->ans1boxsize = 15;
        $formform->ans1strictsyntax = '1';
        $formform->ans1insertstars = '0';
        $formform->ans1syntaxhint = '';
        $formform->ans1syntaxattribute = '0';
        $formform->ans1forbidwords = '';
        $formform->ans1allowwords = '';
        $formform->ans1forbidfloat = '1';
        $formform->ans1requirelowestterms = '0';
        $formform->ans1checkanswertype = '0';
        $formform->ans1mustverify = '1';
        $formform->ans1showvalidation = '1';
        $formform->ans1options = '';

        $formform->ans2type = 'algebraic';
        $formform->ans2modelans = 'x^4';
        $formform->ans2boxsize = 15;
        $formform->ans2strictsyntax = '1';
        $formform->ans2insertstars = '0';
        $formform->ans2syntaxhint = '';
        $formform->ans2syntaxattribute = '0';
        $formform->ans2forbidwords = '';
        $formform->ans2allowwords = '';
        $formform->ans2forbidfloat = '1';
        $formform->ans2requirelowestterms = '0';
        $formform->ans2checkanswertype = '0';
        $formform->ans2mustverify = '1';
        $formform->ans2showvalidation = '1';
        $formform->ans2options = '';

        $formform->ans3type = 'algebraic';
        $formform->ans3modelans = '0';
        $formform->ans3boxsize = 15;
        $formform->ans3strictsyntax = '1';
        $formform->ans3insertstars = '0';
        $formform->ans3syntaxhint = '';
        $formform->ans3syntaxattribute = '0';
        $formform->ans3forbidwords = '';
        $formform->ans3allowwords = '';
        $formform->ans3forbidfloat = '1';
        $formform->ans3requirelowestterms = '0';
        $formform->ans3checkanswertype = '0';
        $formform->ans3mustverify = '1';
        $formform->ans3showvalidation = '1';
        $formform->ans3options = '';

        $formform->ans4type = 'boolean';
        $formform->ans4modelans = 'true';
        $formform->ans4boxsize = 15;
        $formform->ans4strictsyntax = '1';
        $formform->ans4insertstars = '0';
        $formform->ans4syntaxhint = '';
        $formform->ans4syntaxattribute = '0';
        $formform->ans4forbidwords = '';
        $formform->ans4allowwords = '';
        $formform->ans4forbidfloat = '1';
        $formform->ans4requirelowestterms = '0';
        $formform->ans4checkanswertype = '0';
        $formform->ans4mustverify = '0';
        $formform->ans4showvalidation = '0';
        $formform->ans4options = '';

        $formform->oddvalue = 1;
        $formform->odddescription = [
            0 => '',
        ];
        $formform->oddautosimplify = '1';
        $formform->oddfeedbackstyle     = 1;
        $formform->oddfeedbackvariables = 'sa:subst(x=-x,ans1)+ans1';
        $formform->oddanswertest = [
            0 => 'AlgEquiv',
        ];
        $formform->oddsans = [
            0 => 'sa',
        ];
        $formform->oddtans = [
            0 => '0',
        ];
        $formform->oddtestoptions = [
            0 => '',
        ];
        $formform->oddquiet = [
            0 => '0',
        ];
        $formform->oddtruescoremode = [
            0 => '=',
        ];
        $formform->oddtruescore = [
            0 => '1',
        ];
        $formform->oddtruepenalty = [
            0 => '',
        ];
        $formform->oddtruenextnode = [
            0 => '-1',
        ];
        $formform->oddtrueanswernote = [
            0 => 'odd-1-T',
        ];
        $formform->oddtruefeedback = [
            0 => [
                'text' => '',
                'format' => '1',
                'itemid' => 251659256,
            ],
        ];
        $formform->oddfalsescoremode = [
            0 => '=',
        ];
        $formform->oddfalsescore = [
            0 => '0',
        ];
        $formform->oddfalsepenalty = [
            0 => '',
        ];
        $formform->oddfalsenextnode = [
            0 => '-1',
        ];
        $formform->oddfalseanswernote = [
            0 => 'odd-1-F',
        ];
        $formform->oddfalsefeedback = [
            0 => [
                'text' => 'Your answer is not an odd function. Look, \\[ f(x)+f(-x)={@sa@} \\neq 0.\\]<br>',
                'format' => '1',
                'itemid' => 352216298,
            ],
        ];

        $formform->evenvalue = 1;
        $formform->evendescription = [
            0 => '',
        ];
        $formform->evenautosimplify = '1';
        $formform->evenfeedbackstyle     = 1;
        $formform->evenfeedbackvariables = 'sa:subst(x=-x,ans2)-ans2';
        $formform->evenanswertest = [
            0 => 'AlgEquiv',
        ];
        $formform->evensans = [
            0 => 'sa',
        ];
        $formform->eventans = [
            0 => '0',
        ];
        $formform->eventestoptions = [
            0 => '',
        ];
        $formform->evenquiet = [
            0 => '0',
        ];
        $formform->eventruescoremode = [
            0 => '=',
        ];
        $formform->eventruescore = [
            0 => '1',
        ];
        $formform->eventruepenalty = [
            0 => '',
        ];
        $formform->eventruenextnode = [
            0 => '-1',
        ];
        $formform->eventrueanswernote = [
            0 => 'even-1-T',
        ];
        $formform->eventruefeedback = [
            0 => [
                'text' => '',
                'format' => '1',
                'itemid' => 374097881,
            ],
        ];
        $formform->evenfalsescoremode = [
            0 => '=',
        ];
        $formform->evenfalsescore = [
            0 => '0',
        ];
        $formform->evenfalsepenalty = [
            0 => '',
        ];
        $formform->evenfalsenextnode = [
            0 => '-1',
        ];
        $formform->evenfalseanswernote = [
            0 => 'even-1-F',
        ];
        $formform->evenfalsefeedback = [
            0 => [
                'text' => '<p>Your answer is not an even function. Look, \\[ f(x)-f(-x)={@sa@} \\neq 0.\\]<br></p>',
                'format' => '1',
                'itemid' => 880424514,
            ],
        ];

        $formform->oddevenvalue = 1;
        $formform->oddevendescription = [
            0 => '', 1 => '',
        ];
        $formform->oddevenautosimplify = '1';
        $formform->oddevenfeedbackstyle     = 1;
        $formform->oddevenfeedbackvariables = 'sa1:ans3+subst(x=-x,ans3); sa2:ans3-subst(x=-x,ans3)';
        $formform->oddevenanswertest = [
            0 => 'AlgEquiv',
            1 => 'AlgEquiv',
        ];
        $formform->oddevensans = [
            0 => 'sa1',
            1 => 'sa2',
        ];
        $formform->oddeventans = [
            0 => '0',
            1 => '0',
        ];
        $formform->oddeventestoptions = [
            0 => '',
            1 => '',
        ];
        $formform->oddevenquiet = [
            0 => '0',
            1 => '0',
        ];
        $formform->oddeventruescoremode = [
            0 => '=',
            1 => '+',
        ];
        $formform->oddeventruescore = [
            0 => '0.5',
            1 => '0.5',
        ];
        $formform->oddeventruepenalty = [
            0 => '',
            1 => '',
        ];
        $formform->oddeventruenextnode = [
            0 => '1',
            1 => '-1',
        ];
        $formform->oddeventrueanswernote = [
            0 => 'oddeven-1-T',
            1 => 'oddeven-2-T',
        ];
        $formform->oddeventruefeedback = [
            0 => [
                'text' => '',
                'format' => '1',
                'itemid' => 90882068,
            ],
            1 => [
                'text' => '',
                'format' => '1',
                'itemid' => 201325868,
            ],
        ];
        $formform->oddevenfalsescoremode = [
            0 => '=',
            1 => '+',
        ];
        $formform->oddevenfalsescore = [
            0 => '0',
            1 => '0',
        ];
        $formform->oddevenfalsepenalty = [
            0 => '',
            1 => '',
        ];
        $formform->oddevenfalsenextnode = [
            0 => '1',
            1 => '-1',
        ];
        $formform->oddevenfalseanswernote = [
            0 => 'oddeven-1-F',
            1 => 'oddeven-2-F',
        ];
        $formform->oddevenfalsefeedback = [
            0 => [
                'text' => '<p>Your answer is not an odd function. Look, \\[ f(x)+f(-x)={@sa1@} \\neq 0.\\]<br></p>',
                'format' => '1',
                'itemid' => 387904086,
            ],
            1 => [
                'text' => '<p>Your answer is not an even function. Look, \\[ f(x)-f(-x)={@sa2@} \\neq 0.\\]<br></p>',
                'format' => '1',
                'itemid' => 212217540,
            ],
        ];

        $formform->uniquevalue = 1;
        $formform->uniquedescription = [
            0 => '',
        ];
        $formform->uniqueautosimplify = '1';
        $formform->uniquefeedbackstyle     = 1;
        $formform->uniquefeedbackvariables = '';
        $formform->uniqueanswertest = [
            0 => 'AlgEquiv',
        ];
        $formform->uniquesans = [
            0 => 'ans4',
        ];
        $formform->uniquetans = [
            0 => 'true',
        ];
        $formform->uniquetestoptions = [
            0 => '',
        ];
        $formform->uniquequiet = [
            0 => '0',
        ];
        $formform->uniquetruescoremode = [
            0 => '=',
        ];
        $formform->uniquetruescore = [
            0 => '1',
        ];
        $formform->uniquetruepenalty = [
            0 => '',
        ];
        $formform->uniquetruenextnode = [
            0 => '-1',
        ];
        $formform->uniquetrueanswernote = [
            0 => 'unique-1-T',
        ];
        $formform->uniquetruefeedback = [
            0 => [
                'text' => '',
                'format' => '1',
                'itemid' => 692993996,
            ],
        ];
        $formform->uniquefalsescoremode = [
            0 => '=',
        ];
        $formform->uniquefalsescore = [
            0 => '0',
        ];
        $formform->uniquefalsepenalty = [
            0 => '',
        ];
        $formform->uniquefalsenextnode = [
            0 => '-1',
        ];
        $formform->uniquefalseanswernote = [
            0 => 'unique-1-F',
        ];
        $formform->uniquefalsefeedback = [
            0 => [
                'text' => '',
                'format' => '1',
                'itemid' => 55631697,
            ],
        ];

        $formform->questionsimplify = '1';
        $formform->assumepositive = '0';
        $formform->assumereal = '0';
        $formform->prtcorrect = [
            'text' => 'Correct answer, well done!',
            'format' => '1',
            'itemid' => 847867102,
        ];
        $formform->prtpartiallycorrect = [
            'text' => 'Your answer is partially correct!',
            'format' => '1',
            'itemid' => 698828552,
        ];
        $formform->prtincorrect = [
            'text' => 'Incorrect answer :-(',
            'format' => '1',
            'itemid' => 56111684,
        ];
        $formform->decimals = '.';
        $formform->scientificnotation = '*10';
        $formform->multiplicationsign = 'dot';
        $formform->sqrtsign = '1';
        $formform->complexno = 'i';
        $formform->inversetrig = 'cos-1';
        $formform->logicsymbol = 'lang';
        $formform->matrixparens = '[';
        $formform->numhints = 2;
        $formform->hint = [
            0 => [
                'text' => 'Hint 1<br>',
                'format' => '1',
                'itemid' => '83894244',
            ],
            1 => [
                'text' => '<p>Hint 2<br></p>',
                'format' => '1',
                'itemid' => '34635511',
            ],
        ];
        $formform->qtype = 'stack';

        return $formform;
    }

    /**
     * @return qtype_stack_question a very elementary question.
     */
    public static function make_stack_question_checkbox_all_empty() {
        $q = self::make_a_stack_question();

        $q->name = 'test-checkbox-empty';
        $q->questionvariables =
            'texput(olor, lambda([z], block([a,b], [a,b]:args(z), sconcat("\\left(",tex1(a),",",tex1(b),"\\right)"))));' .
            // A silly example but brackets mess up the regular expression in the test construction.
            // This is just to make sure the texput gets into the checkbox input mechanism with a function.
            'texput(clcr, lambda([z], block([a,b], [a,b]:args(z), sconcat("{\\diamond}",tex1(a),",",tex1(b)))));';
        $q->questiontext = 'Which of these are true? [[input:ans1]]
                           [[validation:ans1]]';

        $q->specificfeedback = '[[feedback:firsttree]]';
        $q->penalty = 0.3; // Non-zero and not the default.

        $q->inputs['ans1'] = stack_input_factory::make(
                'checkbox', 'ans1', '[[x^2+1<0,false],[A,false,"Generalizations are false"],[clcr(a,b), false]]', null, null);

        $q->options->set_option('simplify', false);

        $prt = new stdClass;
        $prt->name              = 'firsttree';
        $prt->id                = 0;
        $prt->value             = 1;
        $prt->feedbackstyle     = 1;
        $prt->feedbackvariables = '';
        $prt->firstnodename     = '0';
        $prt->nodes             = [];
        $prt->autosimplify      = false;

        $newnode = new stdClass;
        $newnode->id                  = '0';
        $newnode->nodename            = '0';
        $newnode->description         = '';
        $newnode->sans                = 'ans1';
        $newnode->tans                = '[]';
        $newnode->answertest          = 'AlgEquiv';
        $newnode->testoptions         = '';
        $newnode->quiet               = false;
        $newnode->falsescore          = '0';
        $newnode->falsescoremode      = '=';
        $newnode->falsepenalty        = $q->penalty;
        $newnode->falsefeedback       = '';
        $newnode->falsefeedbackformat = '1';
        $newnode->falseanswernote     = 'firsttree-1-F';
        $newnode->falsenextnode       = '-1';
        $newnode->truescore           = '1';
        $newnode->truescoremode       = '=';
        $newnode->truepenalty         = $q->penalty;
        $newnode->truefeedback        = '';
        $newnode->truefeedbackformat  = '1';
        $newnode->trueanswernote      = 'firsttree-1-T';
        $newnode->truenextnode        = '-1';
        $prt->nodes[] = $newnode;

        $q->prts[$prt->name] = new stack_potentialresponse_tree_lite($prt, $prt->value, $q);

        return $q;
    }

    /**
     * @return qtype_stack_question a checkbox question using %union, which was problematic
     */
    public static function make_stack_question_checkbox_union() {
        $q = self::make_a_stack_question();

        $q->name = 'test-checkbox-union';
        $q->questionvariables = 'ta:[[%union(oo(-inf,0),oo(0,inf)),true],[%union({1},{2}),false],' .
            '[union({1},{4}),false],[A,false,%union({1},{3})]];';
        $q->questiontext = 'Which of these are is the domain? [[input:ans1]]
                           [[validation:ans1]]';

        $q->specificfeedback = '[[feedback:firsttree]]';
        $q->penalty = 0.3; // Non-zero and not the default.

        $q->inputs['ans1'] = stack_input_factory::make(
            'checkbox', 'ans1', 'ta', null, null);

        $q->options->set_option('simplify', false);

        $prt = new stdClass;
        $prt->name              = 'firsttree';
        $prt->id                = 0;
        $prt->value             = 1;
        $prt->feedbackstyle     = 1;
        $prt->feedbackvariables = '';
        $prt->firstnodename     = '0';
        $prt->nodes             = [];
        $prt->autosimplify      = false;

        $newnode = new stdClass;
        $newnode->id                  = '0';
        $newnode->nodename            = '0';
        $newnode->description         = '';
        $newnode->sans                = 'ans1';
        $newnode->tans                = '[]';
        $newnode->answertest          = 'AlgEquiv';
        $newnode->testoptions         = '';
        $newnode->quiet               = false;
        $newnode->falsescore          = '0';
        $newnode->falsescoremode      = '=';
        $newnode->falsepenalty        = $q->penalty;
        $newnode->falsefeedback       = '';
        $newnode->falsefeedbackformat = '1';
        $newnode->falseanswernote     = 'firsttree-1-F';
        $newnode->falsenextnode       = '-1';
        $newnode->truescore           = '1';
        $newnode->truescoremode       = '=';
        $newnode->truepenalty         = $q->penalty;
        $newnode->truefeedback        = '';
        $newnode->truefeedbackformat  = '1';
        $newnode->trueanswernote      = 'firsttree-1-T';
        $newnode->truenextnode        = '-1';
        $prt->nodes[] = $newnode;

        $q->prts[$prt->name] = new stack_potentialresponse_tree_lite($prt, $prt->value, $q);

        return $q;
    }

    /**
     * @return qtype_stack_question a checkbox question using %union, which was problematic
     */
    public static function make_stack_question_checkbox_noun_diff() {
        $q = self::make_a_stack_question();

        $q->name = 'test-checkbox-nound-diff';
        $q->questionvariables = 'ta:[[\'diff(f,x),true],[noundiff(g,x),false],' .
            '[\'int(f,x),false],[nounint(g,x),false]];';
        $q->questiontext = 'Which operation should you use? [[input:ans1]]
                           [[validation:ans1]]';

        $q->specificfeedback = '[[feedback:firsttree]]';
        $q->penalty = 0.3; // Non-zero and not the default.

        $q->inputs['ans1'] = stack_input_factory::make(
            'checkbox', 'ans1', 'ta', null, null);

        $q->options->set_option('simplify', false);

        $prt = new stdClass;
        $prt->name              = 'firsttree';
        $prt->id                = 0;
        $prt->value             = 1;
        $prt->feedbackstyle     = 1;
        $prt->feedbackvariables = '';
        $prt->firstnodename     = '0';
        $prt->nodes             = [];
        $prt->autosimplify      = false;

        $newnode = new stdClass;
        $newnode->id                  = '0';
        $newnode->nodename            = '0';
        $newnode->description         = '';
        $newnode->sans                = 'ans1';
        $newnode->tans                = '[\'diff(f,x)]';
        $newnode->answertest          = 'AlgEquiv';
        $newnode->testoptions         = '';
        $newnode->quiet               = false;
        $newnode->falsescore          = '0';
        $newnode->falsescoremode      = '=';
        $newnode->falsepenalty        = $q->penalty;
        $newnode->falsefeedback       = '';
        $newnode->falsefeedbackformat = '1';
        $newnode->falseanswernote     = 'firsttree-1-F';
        $newnode->falsenextnode       = '-1';
        $newnode->truescore           = '1';
        $newnode->truescoremode       = '=';
        $newnode->truepenalty         = $q->penalty;
        $newnode->truefeedback        = '';
        $newnode->truefeedbackformat  = '1';
        $newnode->trueanswernote      = 'firsttree-1-T';
        $newnode->truenextnode        = '-1';
        $prt->nodes[] = $newnode;

        $q->prts[$prt->name] = new stack_potentialresponse_tree_lite($prt, $prt->value, $q);

        return $q;
    }

    /**
     * @return qtype_stack_question a question which tests checking for addrow in an older question.
     */
    public static function make_stack_question_addrow() {
        $q = self::make_a_stack_question();

        // This number needs to be old, to trigger the addrow error.
        $q->stackversion = '2017072900';
        $q->name = 'addrow';
        $q->questionvariables = 'm:matrix([1,2],[3,4]);m:addrow(m,1,1,1);v1:texdecorate("\\bf", v);';
        $q->questiontext = 'What is \({@m@}^2\)? [[input:ans1]]
                           [[validation:ans1]]';

        $q->specificfeedback = '[[feedback:firsttree]]';
        $q->penalty = 0.3; // Non-zero and not the default.

        $q->inputs['ans1'] = stack_input_factory::make(
                'algebraic', 'ans1', '2', null, ['boxWidth' => 5]);

        $q->options->set_option('simplify', false);

        $prt = new stdClass;
        $prt->name              = 'firsttree';
        $prt->id                = 0;
        $prt->value             = 1;
        $prt->feedbackstyle     = 1;
        // This is to check the upgrade process spots addrow in the PRT feedback variables.
        $prt->feedbackvariables = 'sa:addrow(ans1,2,1,1);';
        $prt->firstnodename     = '0';
        $prt->nodes             = [];
        $prt->autosimplify      = false;

        $newnode = new stdClass;
        $newnode->id                  = '0';
        $newnode->nodename            = '0';
        $newnode->description         = '';
        $newnode->sans                = 'ans1';
        $newnode->tans                = '2';
        $newnode->answertest          = 'EqualComAss';
        $newnode->testoptions         = '';
        $newnode->quiet               = false;
        $newnode->falsescore          = '0';
        $newnode->falsescoremode      = '=';
        $newnode->falsepenalty        = $q->penalty;
        $newnode->falsefeedback       = '';
        $newnode->falsefeedbackformat = '1';
        $newnode->falseanswernote     = 'firsttree-1-F';
        $newnode->falsenextnode       = '-1';
        $newnode->truescore           = '1';
        $newnode->truescoremode       = '=';
        $newnode->truepenalty         = $q->penalty;
        $newnode->truefeedback        = '';
        $newnode->truefeedbackformat  = '1';
        $newnode->trueanswernote      = 'firsttree-1-T';
        $newnode->truenextnode        = '-1';
        $prt->nodes[] = $newnode;

        $q->prts[$prt->name] = new stack_potentialresponse_tree_lite($prt, $prt->value, $q);

        return $q;
    }

    /**
     * @return qtype_stack_question.
     */
    public static function make_stack_question_mul() {
        $q = self::make_a_stack_question();

        $q->stackversion = '2019072900';
        $q->name = 'mul';
        $q->questiontext = 'What is the force of gravity? [[input:ans1]]
                           [[validation:ans1]]';

        $q->specificfeedback = '[[feedback:firsttree]]';
        $q->penalty = 0.5; // Non-zero and not the default.

        $q->inputs['ans1'] = stack_input_factory::make(
                'units', 'ans1', 'stackunits(9.81,m*s^-2)', null, ['boxWidth' => 5, 'options' => 'mul']);

        $q->options->set_option('simplify', false);

        $prt = new stdClass;
        $prt->name              = 'firsttree';
        $prt->id                = 0;
        $prt->value             = 1;
        $prt->feedbackstyle     = 1;
        $prt->feedbackvariables = '';
        $prt->firstnodename     = '0';
        $prt->nodes             = [];
        $prt->autosimplify      = true;

        $newnode = new stdClass;
        $newnode->id                  = '0';
        $newnode->nodename            = '0';
        $newnode->description         = '';
        $newnode->sans                = 'ans1';
        $newnode->tans                = '2';
        $newnode->answertest          = 'UnitsStrict';
        $newnode->testoptions         = '3';
        $newnode->quiet               = false;
        $newnode->falsescore          = '0';
        $newnode->falsescoremode      = '=';
        $newnode->falsepenalty        = $q->penalty;
        $newnode->falsefeedback       = '';
        $newnode->falsefeedbackformat = '1';
        $newnode->falseanswernote     = 'firsttree-1-F';
        $newnode->falsenextnode       = '-1';
        $newnode->truescore           = '1';
        $newnode->truescoremode       = '=';
        $newnode->truepenalty         = $q->penalty;
        $newnode->truefeedback        = '';
        $newnode->truefeedbackformat  = '1';
        $newnode->trueanswernote      = 'firsttree-1-T';
        $newnode->truenextnode        = '-1';
        $prt->nodes[] = $newnode;

        $q->prts[$prt->name] = new stack_potentialresponse_tree_lite($prt, $prt->value, $q);

        return $q;
    }

    /**
     * @return qtype_stack_question a question which uses ATStringSloppy.
     */
    public static function make_stack_question_stringsloppy() {
        $q = self::make_a_stack_question();

        $q->stackversion = '2019110900';
        $q->name = 'stringsloppy';
        $q->questionvariables = 'ta1:"Pythagoras\' Theorem";';
        $q->questiontext = 'What relates side lengths of a right angled plane triangle? [[input:ans1]]
                           [[validation:ans1]]';

        $q->specificfeedback = '[[feedback:firsttree]]';
        $q->penalty = 0.4; // Non-zero and not the default.

        $q->inputs['ans1'] = stack_input_factory::make(
                'string', 'ans1', 'ta1', null, ['boxWidth' => 25]);

        $q->options->set_option('simplify', false);

        $prt = new stdClass;
        $prt->name              = 'firsttree';
        $prt->id                = 0;
        $prt->value             = 1;
        $prt->feedbackstyle     = 1;
        $prt->feedbackvariables = '';
        $prt->firstnodename     = '0';
        $prt->nodes             = [];
        $prt->autosimplify      = false;

        $newnode = new stdClass;
        $newnode->id                  = '0';
        $newnode->nodename            = '0';
        $newnode->description         = '';
        $newnode->sans                = 'ans1';
        $newnode->tans                = 'ta1';
        $newnode->answertest          = 'String';
        $newnode->testoptions         = '';
        $newnode->quiet               = false;
        $newnode->falsescore          = '0';
        $newnode->falsescoremode      = '=';
        $newnode->falsepenalty        = $q->penalty;
        $newnode->falsefeedback       = '';
        $newnode->falsefeedbackformat = '1';
        $newnode->falseanswernote     = 'firsttree-1-F';
        $newnode->falsenextnode       = '1';
        $newnode->truescore           = '1';
        $newnode->truescoremode       = '=';
        $newnode->truepenalty         = $q->penalty;
        $newnode->truefeedback        = '';
        $newnode->truefeedbackformat  = '1';
        $newnode->trueanswernote      = 'firsttree-1-T';
        $newnode->truenextnode        = '-1';
        $prt->nodes[] = $newnode;
        $newnode = new stdClass;
        $newnode->id                  = '1';
        $newnode->nodename            = '1';
        $newnode->description         = '';
        $newnode->sans                = 'ans1';
        $newnode->tans                = 'ta1';
        $newnode->answertest          = 'StringSloppy';
        $newnode->testoptions         = '';
        $newnode->quiet               = false;
        $newnode->falsescore          = '0';
        $newnode->falsescoremode      = '=';
        $newnode->falsepenalty        = $q->penalty;
        $newnode->falsefeedback       = '';
        $newnode->falsefeedbackformat = '1';
        $newnode->falseanswernote     = 'firsttree-2-F';
        $newnode->falsenextnode       = '1';
        $newnode->truescore           = '0.75';
        $newnode->truescoremode       = '=';
        $newnode->truepenalty         = $q->penalty;
        $newnode->truefeedback        = '';
        $newnode->truefeedbackformat  = '1';
        $newnode->trueanswernote      = 'firsttree-2-T';
        $newnode->truenextnode        = '-1';
        $prt->nodes[] = $newnode;

        $q->prts[$prt->name] = new stack_potentialresponse_tree_lite($prt, $prt->value, $q);

        return $q;
    }

    /**
     * @return qtype_stack_question a question which uses ATSRegExp.
     */
    public static function make_stack_question_sregexp() {
        $q = self::make_a_stack_question();

        $q->stackversion = '2019110900';
        $q->name = 'sregexp';
        $q->questionvariables = "ta:\"cccggf\";\ntregex:\"(ccc)*b\";";
        $q->questiontext = '<p>Input a word of the language decribed by the regular expression {@tregex@}.</p>' .
                '<p>[[input:ans1]] [[validation:ans1]]</p>' .
                '[[feedback:firsttree]]';

        $q->specificfeedback = '';
        $q->penalty = 0.4; // Non-zero and not the default.

        $q->inputs['ans1'] = stack_input_factory::make(
                'string', 'ans1', 'ta', null, ['boxWidth' => 25]);

        $q->options->set_option('simplify', false);

        $prt = new stdClass;
        $prt->name              = 'firsttree';
        $prt->id                = 0;
        $prt->value             = 1;
        $prt->feedbackstyle     = 1;
        $prt->feedbackvariables = '';
        $prt->firstnodename     = '0';
        $prt->nodes             = [];
        $prt->autosimplify      = false;

        $newnode = new stdClass;
        $newnode->id                  = '0';
        $newnode->nodename            = '0';
        $newnode->description         = '';
        $newnode->sans                = 'ans1';
        $newnode->tans                = 'tregex';
        $newnode->answertest          = 'SRegExp';
        $newnode->testoptions         = '';
        $newnode->quiet               = false;
        $newnode->falsescore          = '0';
        $newnode->falsescoremode      = '=';
        $newnode->falsepenalty        = $q->penalty;
        $newnode->falsefeedback       =
            '<p>The word {@ans1@} is&nbsp; an element&nbsp; of the language described by {@regex@}<br></p>';
        $newnode->falsefeedbackformat = '1';
        $newnode->falseanswernote     = 'firsttree-1-F';
        $newnode->falsenextnode       = '-1';
        $newnode->truescore           = '1';
        $newnode->truescoremode       = '=';
        $newnode->truepenalty         = $q->penalty;
        $newnode->truefeedback        =
            '<p>The word {@ans1@} is not an element of the language described by {@regex@}</p>';
        $newnode->truefeedbackformat  = '1';
        $newnode->trueanswernote      = 'firsttree-1-T';
        $newnode->truenextnode        = '-1';
        $prt->nodes[] = $newnode;

        $q->prts[$prt->name] = new stack_potentialresponse_tree_lite($prt, $prt->value, $q);

        return $q;
    }

    /**
     * @return qtype_stack_question a question which uses ATSRegExp.
     */
    public static function make_stack_question_feedbackstyle() {
        $q = self::make_a_stack_question();

        $q->stackversion = '2020041100';
        $q->name = 'feedbackstyle';
        $q->questionvariables = "";
        $q->questiontext = '<p>Give two examples of odd functions.</p>' .
                '<p>[[input:ans1]] [[validation:ans1]] [[feedback:prt1]]</p>' .
                '<p>[[input:ans2]] [[validation:ans2]] [[feedback:prt2]]</p>' .
                '<p>[[feedback:prt3]]</p>';

        $q->specificfeedback = '';
        $q->penalty = 0.4; // Non-zero and not the default.

        $q->inputs['ans1'] = stack_input_factory::make(
                'algebraic', 'ans1', 'x^3', null, ['boxWidth' => 10, 'showValidation' => 3]);
        $q->inputs['ans2'] = stack_input_factory::make(
                'algebraic', 'ans2', 'sin(x)', null, ['boxWidth' => 10, 'showValidation' => 3]);

        $q->options->set_option('simplify', true);

        $prt = new stdClass;
        $prt->name              = 'prt1';
        $prt->id                = 0;
        $prt->value             = 1;
        // Set feedbackstyle=2 to test compact feedback.
        $prt->feedbackstyle     = 2;
        $prt->feedbackvariables = 'sa:ev(ans1,x=-x)+ans1;';
        $prt->firstnodename     = '0';
        $prt->nodes             = [];
        $prt->autosimplify      = true;

        $newnode = new stdClass;
        $newnode->id                  = '0';
        $newnode->nodename            = '0';
        $newnode->description         = '';
        $newnode->sans                = 'sa';
        $newnode->tans                = '0';
        $newnode->answertest          = 'AlgEquiv';
        $newnode->testoptions         = '';
        $newnode->quiet               = false;
        $newnode->falsescore          = '0';
        $newnode->falsescoremode      = '=';
        $newnode->falsepenalty        = $q->penalty;
        $newnode->falsefeedback       = 'Your first function is not odd:  \[ f(x)+f(-x)={@sa@} \neq 0\]';
        $newnode->falsefeedbackformat = '1';
        $newnode->falseanswernote     = 'prt1-1-F';
        $newnode->falsenextnode       = '-1';
        $newnode->truescore           = '1';
        $newnode->truescoremode       = '=';
        $newnode->truepenalty         = $q->penalty;
        $newnode->truefeedback        = '';
        $newnode->truefeedbackformat  = '1';
        $newnode->trueanswernote      = 'prt1-1-T';
        $newnode->truenextnode        = '-1';
        $prt->nodes[] = $newnode;
        $q->prts[$prt->name] = new stack_potentialresponse_tree_lite($prt, $prt->value, $q);

        $prt = new stdClass;
        $prt->name              = 'prt2';
        $prt->id                = 1;
        $prt->value             = 1;
        // Set feedbackstyle=3 to test symbolic feedback.
        $prt->feedbackstyle     = 3;
        $prt->feedbackvariables = 'sa:ev(ans2,x=-x)+ans2;';
        $prt->firstnodename     = '0';
        $prt->nodes             = [];
        $prt->autosimplify      = true;

        $newnode = new stdClass;
        $newnode->id                  = '0';
        $newnode->nodename            = '0';
        $newnode->description         = '';
        $newnode->sans                = 'sa';
        $newnode->tans                = '0';
        $newnode->answertest          = 'AlgEquiv';
        $newnode->testoptions         = '';
        $newnode->quiet               = false;
        $newnode->falsescore          = '0';
        $newnode->falsescoremode      = '=';
        $newnode->falsepenalty        = $q->penalty;
        $newnode->falsefeedback       = 'Your second function is not odd:  \[ f(x)+f(-x)={@sa@} \neq 0\]';
        $newnode->falsefeedbackformat = '1';
        $newnode->falseanswernote     = 'prt2-1-F';
        $newnode->falsenextnode       = '-1';
        $newnode->truescore           = '1';
        $newnode->truescoremode       = '=';
        $newnode->truepenalty         = $q->penalty;
        $newnode->truefeedback        = '';
        $newnode->truefeedbackformat  = '1';
        $newnode->trueanswernote      = 'prt2-1-T';
        $newnode->truenextnode        = '-1';
        $prt->nodes[] = $newnode;
        $q->prts[$prt->name] = new stack_potentialresponse_tree_lite($prt, $prt->value, $q);

        $prt = new stdClass;
        $prt->name              = 'prt3';
        $prt->id                = 2;
        $prt->value             = 1;
        // Set feedbackstyle=0 to test formative potential response trees.
        $prt->feedbackstyle     = 0;
        $prt->feedbackvariables = 'sa:ev(ans2,x=-x)+ans2;';
        $prt->firstnodename     = '0';
        $prt->nodes             = [];
        $prt->autosimplify      = true;

        $newnode = new stdClass;
        $newnode->id                  = '0';
        $newnode->nodename            = '0';
        $newnode->description         = '';
        $newnode->sans                = 'all_listp(polynomialpsimp,[ans1,ans2])';
        $newnode->tans                = 'true';
        $newnode->answertest          = 'AlgEquiv';
        $newnode->testoptions         = '';
        $newnode->quiet               = false;
        $newnode->falsescore          = '0.4';
        $newnode->falsescoremode      = '=';
        $newnode->falsepenalty        = '0.2';
        $newnode->falsefeedback       = 'Non-polynomials included.';
        $newnode->falsefeedbackformat = '1';
        $newnode->falseanswernote     = 'prt3-1-F';
        $newnode->falsenextnode       = '-1';
        $newnode->truescore           = '0.5';
        $newnode->truescoremode       = '=';
        $newnode->truepenalty         = '0.2';
        $newnode->truefeedback        = 'Try to think of something more imaginative than just polynomials!';
        $newnode->truefeedbackformat  = '1';
        $newnode->trueanswernote      = 'prt3-1-T';
        $newnode->truenextnode        = '-1';
        $prt->nodes[] = $newnode;
        $q->prts[$prt->name] = new stack_potentialresponse_tree_lite($prt, $prt->value, $q);

        return $q;
    }

    /**
     * @return qtype_stack_question a question which tests context variables.
     */
    public static function make_stack_question_contextvars() {
        $q = self::make_a_stack_question();

        $q->stackversion = '2020112300';
        $q->name = 'contextvars';
        $q->questionvariables = "tuptex(z):= block([a,b], [a,b]:args(z), " .
            "sconcat(\"\\\\left[\",tex1(a),\",\",tex1(b),\"\\\\right)\"));texput(tup, tuptex);" .
            "vec(ex):=stackvector(ex);%_stack_preamble_end;" .
            "texput(blob, \"\\\\diamond\");\n assume(x>2);\n texput(log, \"\\\\log \", prefix);";
        $q->questiontext = 'What is {@blob@}? [[input:ans1]] [[validation:ans1]]';
        $q->generalfeedback = 'You should be able to type in {@blob@} as <code>blob</code>.';

        $q->specificfeedback = '[[feedback:firsttree]]';
        $q->penalty = 0.35; // Non-zero and not the default.

        $q->inputs['ans1'] = stack_input_factory::make(
                'algebraic', 'ans1', 'blob', null, ['boxWidth' => 5, 'allowWords' => 'blob,vec,tup']);

        $q->options->set_option('simplify', true);

        $prt = new stdClass;
        $prt->name              = 'firsttree';
        $prt->id                = 0;
        $prt->value             = 1;
        $prt->feedbackstyle     = 1;
        $prt->feedbackvariables = 'assume(a>0);';
        $prt->firstnodename     = '0';
        $prt->nodes             = [];
        $prt->autosimplify      = true;

        $newnode = new stdClass;
        $newnode->id                  = '0';
        $newnode->nodename            = '0';
        $newnode->description         = '';
        $newnode->sans                = 'ans1';
        $newnode->tans                = '6*(x-2)^(2*k)';
        $newnode->answertest          = 'AlgEquiv';
        $newnode->testoptions         = '';
        $newnode->quiet               = false;
        $newnode->falsescore          = '0';
        $newnode->falsescoremode      = '=';
        $newnode->falsepenalty        = $q->penalty;
        $newnode->falsefeedback       = '';
        $newnode->falsefeedbackformat = '1';
        $newnode->falseanswernote     = 'firsttree-1-F';
        $newnode->falsenextnode       = '1';
        $newnode->truescore           = '1';
        $newnode->truescoremode       = '=';
        $newnode->truepenalty         = $q->penalty;
        $newnode->truefeedback        = '';
        $newnode->truefeedbackformat  = '1';
        $newnode->trueanswernote      = 'firsttree-1-T';
        $newnode->truenextnode        = '-1';
        $prt->nodes[] = $newnode;
        $newnode = new stdClass;
        $newnode->id                  = '1';
        $newnode->nodename            = '1';
        $newnode->description         = 'Description of node 1';
        $newnode->sans                = 'ans1';
        $newnode->tans                = 'a^(x*y)';
        $newnode->answertest          = 'AlgEquiv';
        $newnode->testoptions         = '';
        $newnode->quiet               = false;
        $newnode->falsescore          = '0';
        $newnode->falsescoremode      = '=';
        $newnode->falsepenalty        = $q->penalty;
        $newnode->falsefeedback       = '';
        $newnode->falsefeedbackformat = '1';
        $newnode->falseanswernote     = 'firsttree-2-F';
        $newnode->falsenextnode       = '1';
        $newnode->truescore           = '0.6';
        $newnode->truescoremode       = '=';
        $newnode->truepenalty         = $q->penalty;
        $newnode->truefeedback        = '';
        $newnode->truefeedbackformat  = '1';
        $newnode->trueanswernote      = 'firsttree-2-T';
        $newnode->truenextnode        = '-1';
        $prt->nodes[] = $newnode;

        $q->prts[$prt->name] = new stack_potentialresponse_tree_lite($prt, $prt->value, $q);

        return $q;
    }

    /**
     * @return qtype_stack_question a question which tests mismatched languages.
     */
    public static function make_stack_question_multilang() {
        $q = self::make_a_stack_question();

        $q->stackversion = '2020112300';
        $q->name = 'multilang';
        $q->questionvariables = "mat1:matrix([1,2],[3,4]);\nmat2:matrix([-2,0],[5,7]);\nta:mat1+mat2;";
        $en = '<p>Let \[ A = {@mat1@} \quad \textrm{and} \quad B = {@mat2@}. \]</p>'
            . '<p>Compute the sum \(C = A + B\).</p>';
        $fi = '<p>Olkoot \[ A = {@mat1@} \quad \textrm{ja} \quad B = {@mat2@}. \]'
            . '</p><p>Laske summa \(C = A + B\).</p>';
        $enfi = '  <span lang="en" class="multilang">' . $en . '</span>'
            . '<span lang="fi" class="multilang">' . $fi . '</span>'
            . '<p>[[input:ans1]]</p><div>[[validation:ans1]]</div>';

        $q->questiontext = $enfi;

        $q->specificfeedback = '[[feedback:firsttree]]';
        $q->penalty = 0.35; // Non-zero and not the default.

        $q->inputs['ans1'] = stack_input_factory::make(
            'matrix', 'ans1', 'ta', new stack_options(),
            ['boxWidth' => 5, 'allowWords' => 'blob']);

        $q->options->set_option('simplify', false);

        $prt = new stdClass;
        $prt->name              = 'firsttree';
        $prt->id                = 0;
        $prt->value             = 1;
        $prt->feedbackstyle     = 1;
        $prt->feedbackvariables = 'assume(a>0);';
        $prt->firstnodename     = '0';
        $prt->nodes             = [];
        $prt->autosimplify      = true;

        $newnode = new stdClass;
        $newnode->id                  = '0';
        $newnode->nodename            = '0';
        $newnode->description         = '';
        $newnode->sans                = 'ans1';
        $newnode->tans                = 'ta';
        $newnode->answertest          = 'AlgEquiv';
        $newnode->testoptions         = '';
        $newnode->quiet               = false;
        $newnode->falsescore          = '0';
        $newnode->falsescoremode      = '=';
        $newnode->falsepenalty        = $q->penalty;
        $newnode->falsefeedback       = '  <span lang="en" class="multilang">Looks good to me.</span>';
        $newnode->falsefeedbackformat = '1';
        $newnode->falseanswernote     = 'firsttree-1-F';
        $newnode->falsenextnode       = '1';
        $newnode->truescore           = '1';
        $newnode->truescoremode       = '=';
        $newnode->truepenalty         = $q->penalty;
        $newnode->truefeedback        = '';
        $newnode->truefeedbackformat  = '1';
        $newnode->trueanswernote      = 'firsttree-1-T';
        $newnode->truenextnode        = '-1';
        $prt->nodes[] = $newnode;
        $newnode = new stdClass;
        $newnode->id                  = '1';
        $newnode->nodename            = '1';
        $newnode->description         = 'Description of node 1';
        $newnode->sans                = 'ans1';
        $newnode->tans                = 'mat1.mat2';
        $newnode->answertest          = 'AlgEquiv';
        $newnode->testoptions         = '';
        $newnode->quiet               = false;
        $newnode->falsescore          = '0';
        $newnode->falsescoremode      = '=';
        $newnode->falsepenalty        = $q->penalty;
        $newnode->falsefeedback       = '';
        $newnode->falsefeedbackformat = '1';
        $newnode->falseanswernote     = 'firsttree-2-F';
        $newnode->falsenextnode       = '1';
        $newnode->truescore           = '0.6';
        $newnode->truescoremode       = '=';
        $newnode->truepenalty         = $q->penalty;
        $newnode->truefeedback        = '';
        $newnode->truefeedbackformat  = '1';
        $newnode->trueanswernote      = 'firsttree-2-T';
        $newnode->truenextnode        = '-1';
        $prt->nodes[] = $newnode;

        $q->prts[$prt->name] = new stack_potentialresponse_tree_lite($prt, $prt->value, $q);

        return $q;
    }

    /**
     * @return qtype_stack_question a question which tests language blocks.
     */
    public static function make_stack_question_lang_blocks() {
        $q = self::make_a_stack_question();

        $q->stackversion = '2020112300';
        $q->name = 'langblocks';
        $q->questionvariables = "pt:5;ta2:(x-pt)^2";

        $q->questiontext = '[[lang code="en,other"]] Give an example of a function \(f(x)\) with a stationary point ' .
            'at \(x={@pt@}\).[[/lang]][[lang code="da"]] Giv et eksempel på en funktion \(f(x)\) med et stationært ' .
            'punkt ved \(x={@pt@}\). [[/lang]] [[input:ans1]][[validation:ans1]][[feedback:prt1]]';

        $q->specificfeedback = '';
        $q->penalty = 0.35; // Non-zero and not the default.

        $q->inputs['ans1'] = stack_input_factory::make(
            'algebraic', 'ans1', 'ta2', new stack_options(),
            ['boxWidth' => 5, 'allowWords' => '']);

        $q->options->set_option('simplify', true);

        $prt = new stdClass;
        $prt->name              = 'prt1';
        $prt->id                = 0;
        $prt->value             = 1;
        $prt->feedbackstyle     = 1;
        $prt->feedbackvariables = '';
        $prt->firstnodename     = '0';
        $prt->nodes             = [];
        $prt->autosimplify      = true;

        $newnode = new stdClass;
        $newnode->id                  = '0';
        $newnode->nodename            = '0';
        $newnode->description         = '';
        $newnode->sans                = 'subst(x=pt,diff(ans1,x))';
        $newnode->tans                = '0';
        $newnode->answertest          = 'AlgEquiv';
        $newnode->testoptions         = '';
        $newnode->quiet               = false;
        $newnode->falsescore          = '0';
        $newnode->falsescoremode      = '=';
        $newnode->falsepenalty        = $q->penalty;
        $newnode->falsefeedback       = '[[lang code="en,other"]]At a stationary point, \\(f\'(x)\\) ' .
                'should be zero. However, in your answer, \\(f\'({@pt@})={@subst(x=pt,diff(ans1,x))@}\\).[[/lang]]' .
                '[[lang code="da"]]Ved et stationært punkt skal \\(f\'(x)\\) være nul. Men i dit svar er ' .
                '\\(f\'({@pt@})={@subst(x=pt,diff(ans2,x))@}\\).[[/lang]]';
        $newnode->falsefeedbackformat = '1';
        $newnode->falseanswernote     = 'prt1-1-F';
        $newnode->falsenextnode       = '1';
        $newnode->truescore           = '1';
        $newnode->truescoremode       = '=';
        $newnode->truepenalty         = $q->penalty;
        $newnode->truefeedback        = '';
        $newnode->truefeedbackformat  = '1';
        $newnode->trueanswernote      = 'prt1-1-T';
        $newnode->truenextnode        = '-1';
        $prt->nodes[] = $newnode;

        $q->prts[$prt->name] = new stack_potentialresponse_tree_lite($prt, $prt->value, $q);

        return $q;
    }

    /**
     * @return qtype_stack_question.
     */
    public static function make_stack_question_block_locals() {
        $q = self::make_a_stack_question();

        $q->name = 'block_locals';
        // We need to check that local variable names within the block are not invalid for student's input.
        $q->questionvariables = 'tmpf(a):=block([p,q,r],p:a,q:a,r:p+q,return(r)); cans1:p^2+p+1;';
        $q->questiontext = 'Answer {@cans1@} with input p^2+p+1.'
                . '<p>[[input:ans1]]</p><div>[[validation:ans1]]</div>';
        $q->generalfeedback = '';
        $q->questionnote = '';

        $q->specificfeedback = '[[feedback:firsttree]]';
        $q->penalty = 0.25; // Non-zero and not the default.

        $q->inputs['ans1'] = stack_input_factory::make(
                'algebraic', 'ans1', 'p^2+p+1', null,
                ['boxWidth' => 20, 'forbidWords' => '', 'allowWords' => '']);

        $prt = new stdClass;
        $prt->name              = 'firsttree';
        $prt->id                = 0;
        $prt->value             = 1;
        $prt->feedbackstyle     = 1;
        $prt->feedbackvariables = '';
        $prt->firstnodename     = '0';
        $prt->nodes             = [];
        $prt->autosimplify      = true;

        $newnode = new stdClass;
        $newnode->id                  = '0';
        $newnode->nodename            = '0';
        $newnode->description         = '';
        $newnode->sans                = 'ans1';
        $newnode->tans                = 'cans1';
        $newnode->answertest          = 'AlgEquiv';
        $newnode->testoptions         = '';
        $newnode->quiet               = false;
        $newnode->falsescore          = '0';
        $newnode->falsescoremode      = '=';
        $newnode->falsepenalty        = $q->penalty;
        $newnode->falsefeedback       = '';
        $newnode->falsefeedbackformat = '1';
        $newnode->falseanswernote     = 'firsttree-0-0';
        $newnode->falsenextnode       = '-1';
        $newnode->truescore           = '1';
        $newnode->truescoremode       = '=';
        $newnode->truepenalty         = $q->penalty;
        $newnode->truefeedback        = '';
        $newnode->truefeedbackformat  = '1';
        $newnode->trueanswernote      = 'firsttree-0-1';
        $newnode->truenextnode        = '-1';
        $prt->nodes[] = $newnode;

        $q->prts[$prt->name] = new stack_potentialresponse_tree_lite($prt, $prt->value, $q);

        return $q;
    }

    /**
     * @return qtype_stack_question.
     */
    public static function make_stack_question_validator() {
        $q = self::make_a_stack_question();

        $q->name = 'validator';
        // We need to check that local variable names within the block are not invalid for student's input.
        // We need to chack mathematics within the castext is correctly displayed.
        $q->questionvariables = 'texput(foo,lambda([e],[a,b]:args(e), sconcat("\\\\frac{", tex1(a), "}{", tex1(b), "}")));' .
            'ta:phi^2-1;myvalidityidea(ex):=block(if ev(subsetp(setify(listofvars(ex)),' .
            'setify(listofvars(ta))), simp) then return(""),castext("[[lang code=\'fi\']]Vastauksesi sisältää ' .
            'vääriä muuttujia.[[/lang]][[lang code=\'en,other\']]Your answer {@ex@} contains the wrong variables.[[/lang]]"));';
        // This question is also used to test the lang blocks at the top level.
        $q->questiontext = "[[lang code='en,other']] What is {@ta@}? [[/lang]]<br>" .
                           "[[lang code='de']] Was ist {@ta@}? [[/lang]]<br>" .
                           "[[lang code='fi']] Mikä on {@ta@}? [[/lang]]<br>" .
                           "[[input:ans1]] [[validation:ans1]]";
        $q->generalfeedback = '';
        $q->questionnote = '';

        $q->specificfeedback = '[[feedback:firsttree]]';
        $q->penalty = 0.25; // Non-zero and not the default.

        $q->inputs['ans1'] = stack_input_factory::make(
            'algebraic', 'ans1', 'ta', null,
            [
                'boxWidth' => 20, 'forbidWords' => '', 'allowWords' => 'foo',
                'options' => 'validator:myvalidityidea',
            ]);

        $prt = new stdClass;
        $prt->name              = 'firsttree';
        $prt->id                = 0;
        $prt->value             = 1;
        $prt->feedbackstyle     = 1;
        $prt->feedbackvariables = '';
        $prt->firstnodename     = '0';
        $prt->nodes             = [];
        $prt->autosimplify      = true;

        $newnode = new stdClass;
        $newnode->id                  = '0';
        $newnode->nodename            = '0';
        $newnode->description         = '';
        $newnode->sans                = 'ans1';
        $newnode->tans                = 'ta';
        $newnode->answertest          = 'AlgEquiv';
        $newnode->testoptions         = '';
        $newnode->quiet               = false;
        $newnode->falsescore          = '0';
        $newnode->falsescoremode      = '=';
        $newnode->falsepenalty        = $q->penalty;
        $newnode->falsefeedback       = "[[lang code='en,other']] wrong [[/lang]]<br> [[lang code='de']] falsch [[/lang]]" .
            "<br> [[lang code='fi']] väärä [[/lang]]";
        $newnode->falsefeedbackformat = '1';
        $newnode->falseanswernote     = 'firsttree-0-0';
        $newnode->falsenextnode       = '-1';
        $newnode->truescore           = '1';
        $newnode->truescoremode       = '=';
        $newnode->truepenalty         = $q->penalty;
        $newnode->truefeedback        = "[[lang code='en,other']] true answer [[/lang]]<br> [[lang code='de']] richtig [[/lang]]" .
            "<br> [[lang code='fi']] oikea [[/lang]]";
        $newnode->truefeedbackformat  = '1';
        $newnode->trueanswernote      = 'firsttree-0-1';
        $newnode->truenextnode        = '-1';
        $prt->nodes[] = $newnode;

        $q->prts[$prt->name] = new stack_potentialresponse_tree_lite($prt, $prt->value, $q);

        return $q;
    }

    /**
     * @return qtype_stack_question.
     */
    public static function make_stack_question_feedback() {
        $q = self::make_a_stack_question();

        $q->name = 'feedback';
        // We need to check that local variable names within the block are not invalid for student's input.
        // We need to chack mathematics within the castext is correctly displayed.
        $q->questionvariables = 'feedback_fn(ex) := "Remember to enter sets!"' .
            "n : rand(2)+3; " .
            "p : rand(3)+2; " .
            "ta : setify(makelist(p*%e^(2*%pi*%i*k/n),k,1,n))" .
            "sc2:0.3";
        $q->questiontext = '<p>Find all the complex solutions of the equation \[ z^{@n@}={@p^n@}.\]
                            Enter your answer as a set of numbers.
                            [[input:ans1]]</p>
                            [[validation:ans1]]';

        $q->specificfeedback = '[[feedback:ans]]';
        $q->questionnote = '{@ta@}';

        $q->inputs['ans1'] = stack_input_factory::make(
            'algebraic', 'ans1', 'ta', null,
            [
                'boxWidth' => 20, 'syntaxHint' => '{?,?,...,?}',
                'options' => 'feedback:feedback_fn',
            ]
            );

            $feedbackvars = 'a1 : listify(ans1);' .
                'a1 : maplist(lambda([x],x^n-p^n),a1);' .
                'a1 : setify(a1)';

            $prt = new stdClass;
            $prt->name              = 'ans';
            $prt->id                = 0;
            $prt->value             = 1;
            $prt->feedbackstyle     = 1;
            $prt->feedbackvariables = $feedbackvars;
            $prt->firstnodename     = '0';
            $prt->nodes             = [];
            $prt->autosimplify      = true;
            $newnode = new stdClass;
            $newnode->id                  = '0';
            $newnode->nodename            = '0';
            $newnode->description         = '';
            $newnode->sans                = 'ans1';
            $newnode->tans                = 'ta';
            $newnode->answertest          = 'Sets';
            $newnode->testoptions         = '';
            $newnode->quiet               = false;
            $newnode->falsescore          = '0';
            $newnode->falsescoremode      = '=';
            $newnode->falsepenalty        = $q->penalty;
            $newnode->falsefeedback       = '';
            $newnode->falsefeedbackformat = '1';
            $newnode->falseanswernote     = 'ans-0-F';
            $newnode->falsenextnode       = '1';
            $newnode->truescore           = '1';
            $newnode->truescoremode       = '=';
            $newnode->truepenalty         = $q->penalty;
            $newnode->truefeedback        = '';
            $newnode->truefeedbackformat  = '1';
            $newnode->trueanswernote      = 'ans-0-T';
            $newnode->truenextnode        = '-1';
            $prt->nodes[] = $newnode;
            $newnode = new stdClass;
            $newnode->id                  = '1';
            $newnode->nodename            = '1';
            $newnode->description         = '';
            $newnode->sans                = 'ans1';
            $newnode->tans                = '{p}';
            $newnode->answertest          = 'AlgEquiv';
            $newnode->testoptions         = '';
            $newnode->quiet               = false;
            $newnode->falsescore          = '0';
            $newnode->falsescoremode      = '=';
            $newnode->falsepenalty        = $q->penalty;
            $newnode->falsefeedback       = '';
            $newnode->falsefeedbackformat = '1';
            $newnode->falseanswernote     = 'ans-1-F';
            $newnode->falsenextnode       = '2';
            $newnode->truescore           = 'sc2';
            $newnode->truescoremode       = '=';
            $newnode->truepenalty         = $q->penalty;
            $newnode->truefeedback        = '<p>There are more answers that just the single real number.
                 Please consider complex solutions to this problem!</p>';
            $newnode->truefeedbackformat  = '1';
            $newnode->trueanswernote      = 'ans-1-T';
            $newnode->truenextnode        = '-1';
            $prt->nodes[] = $newnode;
            $newnode = new stdClass;
            $newnode->id                  = '2';
            $newnode->nodename            = '2';
            $newnode->description         = '';
            $newnode->sans                = 'a1';
            $newnode->tans                = '{0}';
            $newnode->answertest          = 'AlgEquiv';
            $newnode->testoptions         = '';
            $newnode->quiet               = true;
            $newnode->falsescore          = '0';
            $newnode->falsescoremode      = '=';
            $newnode->falsepenalty        = $q->penalty;
            $newnode->falsefeedback       = '';
            $newnode->falsefeedbackformat = '1';
            $newnode->falseanswernote     = 'ans-2-F';
            $newnode->falsenextnode       = '-1';
            $newnode->truescore           = 'sc2';
            $newnode->truescoremode       = '=';
            $newnode->truepenalty         = $q->penalty;
            $newnode->truefeedback        =
            'All your answers satisfy the equation. But, you have missed some of the solutions.';
            $newnode->truefeedbackformat  = '1';
            $newnode->trueanswernote      = 'ans-2-T';
            $newnode->truenextnode        = '-1';
            $prt->nodes[] = $newnode;
            $q->prts[$prt->name] = new stack_potentialresponse_tree_lite($prt, $prt->value, $q);

            return $q;
    }

    /**
     * @return qtype_stack_question.
     */
    public static function make_stack_question_ordergreat() {
        $q = self::make_a_stack_question();

        $q->name = 'ordergreat';
        $q->questionvariables = "ordergreat(x,y);\nta:5*y+3*x=1";
        $q->questiontext = "What is {@ta@}? [[input:ansq]][[validation:ansq]]" .
                // Below checks this is still a real float.
                '{@dispdp(float(pi),4)@}';
        $q->generalfeedback = '';
        $q->questionnote = '';

        $q->specificfeedback = '[[feedback:firsttree]]';
        $q->penalty = 0.25; // Non-zero and not the default.

        $q->inputs['ansq'] = stack_input_factory::make(
            'algebraic', 'ansq', 'ta', null,
            [
                'boxWidth' => 20, 'forbidWords' => '',
            ]);

        // By setting simp:true (the default) we check the re-ordering really happens.
        $q->options->set_option('simplify', true);

        $prt = new stdClass;
        $prt->name              = 'firsttree';
        $prt->id                = 0;
        $prt->value             = 1;
        $prt->feedbackstyle     = 1;
        $prt->feedbackvariables = '';
        $prt->firstnodename     = '0';
        $prt->nodes             = [];
        $prt->autosimplify      = false;

        $newnode = new stdClass;
        $newnode->id                  = '0';
        $newnode->nodename            = '0';
        $newnode->description         = '';
        $newnode->sans                = 'ansq';
        $newnode->tans                = 'ta';
        $newnode->answertest          = 'CasEqual';
        $newnode->testoptions         = '';
        $newnode->quiet               = false;
        $newnode->falsescore          = '0';
        $newnode->falsescoremode      = '=';
        $newnode->falsepenalty        = $q->penalty;
        $newnode->falsefeedback       = "";
        $newnode->falsefeedbackformat = '1';
        $newnode->falseanswernote     = 'firsttree-0-0';
        $newnode->falsenextnode       = '-1';
        $newnode->truescore           = '1';
        $newnode->truescoremode       = '=';
        $newnode->truepenalty         = $q->penalty;
        $newnode->truefeedback        = "";
        $newnode->truefeedbackformat  = '1';
        $newnode->trueanswernote      = 'firsttree-0-1';
        $newnode->truenextnode        = '-1';
        $prt->nodes[] = $newnode;

        $q->prts[$prt->name] = new stack_potentialresponse_tree_lite($prt, $prt->value, $q);

        return $q;
    }

    /**
     * @return qtype_stack_question.
     */
    public static function make_stack_question_exdowncase() {
        $q = self::make_a_stack_question();

        $q->name = 'exdowncase';
        $q->questionvariables = "ordergreat(x);\nveq: 5*y^2-8*x*y+13*x^2 = 49;";
        $q->questiontext = "What is {@veq@}? [[input:ansq]][[validation:ansq]]";
        $q->generalfeedback = '';
        $q->questionnote = '';

        $q->specificfeedback = '[[feedback:firsttree]]';
        $q->penalty = 0.25; // Non-zero and not the default.

        $q->inputs['ansq'] = stack_input_factory::make(
            'algebraic', 'ansq', 'veq', null,
            [
                'boxWidth' => 20, 'forbidWords' => '',
            ]);

        // By setting simp:true (the default) we check the re-ordering really happens.
        $q->options->set_option('simplify', true);

        $prt = new stdClass;
        $prt->name              = 'firsttree';
        $prt->id                = 0;
        $prt->value             = 1;
        $prt->feedbackstyle     = 1;
        $prt->feedbackvariables = 'loweranseq:exdowncase(ansq);';
        $prt->firstnodename     = '0';
        $prt->nodes             = [];
        $prt->autosimplify      = true;

        $newnode = new stdClass;
        $newnode->id                  = '0';
        $newnode->nodename            = '0';
        $newnode->description         = 'Use CasEqual as the test';
        $newnode->sans                = 'loweranseq';
        $newnode->tans                = 'veq';
        $newnode->answertest          = 'CasEqual';
        $newnode->testoptions         = '';
        $newnode->quiet               = false;
        $newnode->falsescore          = '0';
        $newnode->falsescoremode      = '=';
        $newnode->falsepenalty        = $q->penalty;
        $newnode->falsefeedback       = "";
        $newnode->falsefeedbackformat = '1';
        $newnode->falseanswernote     = 'firsttree-0-0';
        $newnode->falsenextnode       = '1';
        $newnode->truescore           = '1';
        $newnode->truescoremode       = '=';
        $newnode->truepenalty         = $q->penalty;
        $newnode->truefeedback        = "";
        $newnode->truefeedbackformat  = '1';
        $newnode->trueanswernote      = 'firsttree-0-1';
        $newnode->truenextnode        = '1';
        $prt->nodes[] = $newnode;
        $newnode = new stdClass;
        $newnode->id                  = '1';
        $newnode->nodename            = '1';
        $newnode->description         = 'Use AlgEquiv as the test';
        $newnode->sans                = 'loweranseq';
        $newnode->tans                = 'veq';
        $newnode->answertest          = 'AlgEquiv';
        $newnode->testoptions         = '';
        $newnode->quiet               = false;
        $newnode->falsescore          = '0';
        $newnode->falsescoremode      = '=';
        $newnode->falsepenalty        = $q->penalty;
        $newnode->falsefeedback       = "";
        $newnode->falsefeedbackformat = '1';
        $newnode->falseanswernote     = 'firsttree-1-0';
        $newnode->falsenextnode       = '-1';
        $newnode->truescore           = '1';
        $newnode->truescoremode       = '=';
        $newnode->truepenalty         = $q->penalty;
        $newnode->truefeedback        = "";
        $newnode->truefeedbackformat  = '1';
        $newnode->trueanswernote      = 'firsttree-1-1';
        $newnode->truenextnode        = '-1';
        $prt->nodes[] = $newnode;

        $q->prts[$prt->name] = new stack_potentialresponse_tree_lite($prt, $prt->value, $q);

        return $q;
    }

    /**
     * @return qtype_stack_question.
     */
    public static function make_stack_question_bailout() {
        $q = self::make_a_stack_question();

        $q->name = 'bailout';
        $q->questionvariables = "veq:{1,2,3};";
        $q->questiontext = "What is the largest from {@veq@}? [[input:ans1]][[validation:ans1]]";
        $q->generalfeedback = '';
        $q->questionnote = '';

        $q->specificfeedback = '[[feedback:firsttree]]';
        $q->penalty = 0.25; // Non-zero and not the default.

        $q->inputs['ans1'] = stack_input_factory::make(
            'algebraic', 'ans1', '3', null,
            [
                'boxWidth' => 20, 'forbidWords' => '',
            ]);

        // By setting simp:true (the default) we check the re-ordering really happens.
        $q->options->set_option('simplify', true);

        $prt = new stdClass;
        $prt->name              = 'firsttree';
        $prt->id                = 0;
        $prt->value             = 1;
        $prt->feedbackstyle     = 1;
        $prt->feedbackvariables = '%stack_prt_stop_p:if is(ans1=5) then true else false;';
        $prt->feedbackvariables .= 'k:ans1^2;';
        $prt->firstnodename     = '0';
        $prt->nodes             = [];
        $prt->autosimplify      = true;

        $newnode = new stdClass;
        $newnode->id                  = '0';
        $newnode->nodename            = '0';
        $newnode->description         = 'Use the test ability to bail out';
        $newnode->sans                = 'ans1';
        $newnode->tans                = '3';
        $newnode->answertest          = 'AlgEquiv';
        $newnode->testoptions         = '';
        $newnode->quiet               = false;
        $newnode->falsescore          = '0';
        $newnode->falsescoremode      = '=';
        $newnode->falsepenalty        = $q->penalty;
        $newnode->falsefeedback       = "";
        $newnode->falsefeedbackformat = '1';
        $newnode->falseanswernote     = 'firsttree-0-0';
        $newnode->falsenextnode       = '1';
        $newnode->truescore           = '1';
        $newnode->truescoremode       = '=';
        $newnode->truepenalty         = $q->penalty;
        $newnode->truefeedback        = "";
        $newnode->truefeedbackformat  = '1';
        $newnode->trueanswernote      = 'firsttree-0-1';
        $newnode->truenextnode        = '1';
        $prt->nodes[] = $newnode;

        $q->prts[$prt->name] = new stack_potentialresponse_tree_lite($prt, $prt->value, $q);

        return $q;
    }

    /**
     * Make the data what would be received from the editing form for an algebraic input question.
     *
     * @return stdClass the data that would be returned by $form->get_data();
     */
    public static function get_stack_question_form_data_algebraic_input() {
        $formform = new stdClass();
        $formform->stackversion = '2024032401';
        $formform->name = 'Algebraic input';
        $formform->questionvariables = 'ta:a*b';
        $formform->questiontext = [
            'text' => '<p>Type in {@ta@}.</p><p>[[input:ans1]] [[validation:ans1]]</p>
                <p>(Note, this assumes single variable variable names)</p>',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->questiondescription = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->specificfeedback = [
            'text' => '[[feedback:prt1]]',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->generalfeedback = [
            'text' => 'There are various options for typing in multiplication within STACK.',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->questionnote = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->penalty = 0.1;
        $formform->variantsselectionseed = '';
        $formform->defaultmark = '1';
        $formform->ans1type = 'algebraic';
        $formform->ans1modelans = 'ta';
        $formform->ans1boxsize = '15';
        $formform->ans1strictsyntax = '';
        $formform->ans1insertstars = '2';
        $formform->ans1syntaxhint = '';
        $formform->ans1syntaxattribute = '0';
        $formform->ans1forbidwords = 'solve';
        $formform->ans1allowwords = '';
        $formform->ans1forbidfloat = '1';
        $formform->ans1requirelowestterms = '0';
        $formform->ans1checkanswertype = '1';
        $formform->ans1mustverify = '1';
        $formform->ans1showvalidation = '1';
        $formform->ans1options = '';
        $formform->questionsimplify = '1';
        $formform->assumepositive = '';
        $formform->assumereal = '0';
        $formform->prtcorrect = [
            'text' => '<span style="font-size: 1.5em; color:green;"><i class="fa fa-check"></i></span> Correct answer, well done.',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prtpartiallycorrect = [
            'text' => '<span style="font-size: 1.5em; color:orange;"><i class="fa fa-adjust"></i></span>
                Your answer is partially correct.',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prtincorrect = [
            'text' => '<span style="font-size: 1.5em; color:red;"><i class="fa fa-times"></i></span> Incorrect answer.',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->decimals = '.';
        $formform->scientificnotation = '*10';
        $formform->multiplicationsign = 'dot';
        $formform->sqrtsign = '1';
        $formform->complexno = 'i';
        $formform->inversetrig = 'cos-1';
        $formform->logicsymbol = 'lang';
        $formform->matrixparens = '[';
        $formform->qtype = 'stack';
        $formform->numhints = 2;
        $formform->hint = [
            [
                'text' => '',
                'format' => '1',
            ],
            [
                'text' => '',
                'format' => '1',
            ],
        ];
        $formform->prt1value = 1;
        $formform->prt1feedbackstyle = '1';
        $formform->prt1feedbackvariables = '';
        $formform->prt1autosimplify = '1';
        $formform->prt1description[0] = '';
        $formform->prt1sans[0] = 'ans1';
        $formform->prt1tans[0] = 'ta';
        $formform->prt1answertest[0] = 'AlgEquiv';
        $formform->prt1testoptions[0] = '';
        $formform->prt1quiet[0] = '0';
        $formform->prt1falsescore[0] = '0';
        $formform->prt1falsescoremode[0] = '=';
        $formform->prt1falsepenalty[0] = '';
        $formform->prt1falsefeedback[0] = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prt1falseanswernote[0] = 'prt1-1-F';
        $formform->prt1falsenextnode[0] = '-1';
        $formform->prt1truescore[0] = '1';
        $formform->prt1truescoremode[0] = '=';
        $formform->prt1truepenalty[0] = '';
        $formform->prt1truefeedback[0] = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prt1trueanswernote[0] = 'prt1-1-T';
        $formform->prt1truenextnode[0] = '-1';
        return $formform;
    }

    /**
     * Make the data what would be received from the editing form for Algebraic input (align to the right) question.
     *
     * @return stdClass the data that would be returned by $form->get_data();
     */
    public static function get_stack_question_form_data_algebraic_input_right() {
        $formform = new stdClass();
        $formform->stackversion = '2024032401';
        $formform->name = 'Algebraic input (align to the right)';
        $formform->questionvariables = 'ta:sin(x^2)';
        $formform->questiontext = [
            'text' => '<p>Type in {@ta@}.<br></p><p>[[input:ans1]] [[validation:ans1]]</p>
                <p>(Note, this assumes single variable variable names)</p>',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->questiondescription = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->specificfeedback = [
            'text' => '[[feedback:prt1]]',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->generalfeedback = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->questionnote = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->penalty = 0.1;
        $formform->variantsselectionseed = '';
        $formform->defaultmark = '1';
        $formform->ans1type = 'algebraic';
        $formform->ans1modelans = 'ta';
        $formform->ans1boxsize = '15';
        $formform->ans1strictsyntax = '';
        $formform->ans1insertstars = '2';
        $formform->ans1syntaxhint = '';
        $formform->ans1syntaxattribute = '0';
        $formform->ans1forbidwords = 'solve';
        $formform->ans1allowwords = '';
        $formform->ans1forbidfloat = '1';
        $formform->ans1requirelowestterms = '0';
        $formform->ans1checkanswertype = '1';
        $formform->ans1mustverify = '1';
        $formform->ans1showvalidation = '1';
        $formform->ans1options = 'align:right';
        $formform->questionsimplify = '1';
        $formform->assumepositive = '';
        $formform->assumereal = '0';
        $formform->prtcorrect = [
            'text' => '<span style="font-size: 1.5em; color:green;"><i class="fa fa-check"></i></span> Correct answer, well done.',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prtpartiallycorrect = [
            'text' => '<span style="font-size: 1.5em; color:orange;"><i class="fa fa-adjust"></i></span>
                Your answer is partially correct.',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prtincorrect = [
            'text' => '<span style="font-size: 1.5em; color:red;"><i class="fa fa-times"></i></span> Incorrect answer.',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->decimals = '.';
        $formform->scientificnotation = '*10';
        $formform->multiplicationsign = 'dot';
        $formform->sqrtsign = '1';
        $formform->complexno = 'i';
        $formform->inversetrig = 'cos-1';
        $formform->logicsymbol = 'lang';
        $formform->matrixparens = '[';
        $formform->qtype = 'stack';
        $formform->numhints = 2;
        $formform->hint = [
            [
                'text' => '',
                'format' => '1',
            ],
            [
                'text' => '',
                'format' => '1',
            ],
        ];
        $formform->prt1value = 1;
        $formform->prt1feedbackstyle = '1';
        $formform->prt1feedbackvariables = '';
        $formform->prt1autosimplify = '1';
        $formform->prt1description[0] = '';
        $formform->prt1sans[0] = 'ans1';
        $formform->prt1tans[0] = 'ta';
        $formform->prt1answertest[0] = 'AlgEquiv';
        $formform->prt1testoptions[0] = '';
        $formform->prt1quiet[0] = '0';
        $formform->prt1falsescore[0] = '0';
        $formform->prt1falsescoremode[0] = '=';
        $formform->prt1falsepenalty[0] = '';
        $formform->prt1falsefeedback[0] = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prt1falseanswernote[0] = 'prt1-1-F';
        $formform->prt1falsenextnode[0] = '-1';
        $formform->prt1truescore[0] = '1';
        $formform->prt1truescoremode[0] = '=';
        $formform->prt1truepenalty[0] = '';
        $formform->prt1truefeedback[0] = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prt1trueanswernote[0] = 'prt1-1-T';
        $formform->prt1truenextnode[0] = '-1';
        return $formform;
    }

    /**
     * Make the data what would be received from the editing form for Algebraic input (answer box sizes test) question.
     *
     * @return stdClass the data that would be returned by $form->get_data();
     */
    public static function get_stack_question_form_data_algebraic_input_size() {
        $formform = new stdClass();
        $formform->stackversion = '2024032401';
        $formform->name = 'Algebraic input (answer box sizes test)';
        $formform->questionvariables = '';
        $formform->questiontext = [
            'text' => '<p>This question just tests answer boxes of multiple sizes, and styles of input.</p>
                <p>Standard: [[input:ans1]] [[validation:ans1]]</p> <p>No variable list: [[input:ans2]]
                [[validation:ans2]]</p> <p>Compact [[input:ans3]] [[validation:ans3]]
                (all following are compact)</p> <p>[[input:ans4]] [[validation:ans4]]</p>
                <p>[[input:ans5]] [[validation:ans5]]</p> <p>[[input:ans7]] [[validation:ans7]]</p>
                <p>[[input:ans10]] [[validation:ans10]]</p> <p>[[input:ans15]] [[validation:ans15]]</p>
                <p>[[input:ans20]] [[validation:ans20]]</p>',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->questiondescription = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->specificfeedback = [
            'text' => '[[feedback:prt1]]',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->generalfeedback = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->questionnote = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->penalty = 0.1;
        $formform->variantsselectionseed = '';
        $formform->defaultmark = '1';
        $formform->ans1type = 'algebraic';
        $formform->ans1modelans = 'a';
        $formform->ans1boxsize = '1';
        $formform->ans1strictsyntax = '';
        $formform->ans1insertstars = '0';
        $formform->ans1syntaxhint = 'a';
        $formform->ans1syntaxattribute = '0';
        $formform->ans1forbidwords = '';
        $formform->ans1allowwords = '';
        $formform->ans1forbidfloat = '1';
        $formform->ans1requirelowestterms = '0';
        $formform->ans1checkanswertype = '0';
        $formform->ans1mustverify = '1';
        $formform->ans1showvalidation = '1';
        $formform->ans1options = '';
        $formform->ans2type = 'algebraic';
        $formform->ans2modelans = '2*x';
        $formform->ans2type = 'algebraic';
        $formform->ans2modelans = '2*x';
        $formform->ans2boxsize = '2';
        $formform->ans2strictsyntax = '';
        $formform->ans2insertstars = '0';
        $formform->ans2syntaxhint = '2*x';
        $formform->ans2syntaxattribute = '0';
        $formform->ans2forbidwords = '';
        $formform->ans2allowwords = '';
        $formform->ans2forbidfloat = '1';
        $formform->ans2requirelowestterms = '0';
        $formform->ans2checkanswertype = '0';
        $formform->ans2mustverify = '1';
        $formform->ans2showvalidation = '2';
        $formform->ans2options = '';
        $formform->ans3type = 'algebraic';
        $formform->ans3modelans = 'x^2';
        $formform->ans3type = 'algebraic';
        $formform->ans3modelans = 'x^2';
        $formform->ans3boxsize = '3';
        $formform->ans3strictsyntax = '';
        $formform->ans3insertstars = '0';
        $formform->ans3syntaxhint = 'x^2';
        $formform->ans3syntaxattribute = '0';
        $formform->ans3forbidwords = '';
        $formform->ans3allowwords = '';
        $formform->ans3forbidfloat = '1';
        $formform->ans3requirelowestterms = '0';
        $formform->ans3checkanswertype = '0';
        $formform->ans3mustverify = '1';
        $formform->ans3showvalidation = '3';
        $formform->ans3options = '';
        $formform->ans4type = 'algebraic';
        $formform->ans4modelans = '2';
        $formform->ans4type = 'algebraic';
        $formform->ans4modelans = '2';
        $formform->ans4boxsize = '4';
        $formform->ans4strictsyntax = '';
        $formform->ans4insertstars = '0';
        $formform->ans4syntaxhint = '2222';
        $formform->ans4syntaxattribute = '0';
        $formform->ans4forbidwords = '';
        $formform->ans4allowwords = '';
        $formform->ans4forbidfloat = '1';
        $formform->ans4requirelowestterms = '0';
        $formform->ans4checkanswertype = '0';
        $formform->ans4mustverify = '1';
        $formform->ans4showvalidation = '3';
        $formform->ans4options = '';
        $formform->ans5type = 'algebraic';
        $formform->ans5modelans = '2';
        $formform->ans5type = 'algebraic';
        $formform->ans5modelans = '2';
        $formform->ans5boxsize = '5';
        $formform->ans5strictsyntax = '';
        $formform->ans5insertstars = '0';
        $formform->ans5syntaxhint = '22222';
        $formform->ans5syntaxattribute = '0';
        $formform->ans5forbidwords = '';
        $formform->ans5allowwords = '';
        $formform->ans5forbidfloat = '1';
        $formform->ans5requirelowestterms = '0';
        $formform->ans5checkanswertype = '0';
        $formform->ans5mustverify = '1';
        $formform->ans5showvalidation = '3';
        $formform->ans5options = '';
        $formform->ans7type = 'algebraic';
        $formform->ans7modelans = '2';
        $formform->ans7type = 'algebraic';
        $formform->ans7modelans = '2';
        $formform->ans7boxsize = '7';
        $formform->ans7strictsyntax = '';
        $formform->ans7insertstars = '0';
        $formform->ans7syntaxhint = '2222222';
        $formform->ans7syntaxattribute = '0';
        $formform->ans7forbidwords = '';
        $formform->ans7allowwords = '';
        $formform->ans7forbidfloat = '1';
        $formform->ans7requirelowestterms = '0';
        $formform->ans7checkanswertype = '0';
        $formform->ans7mustverify = '1';
        $formform->ans7showvalidation = '3';
        $formform->ans7options = '';
        $formform->ans10type = 'algebraic';
        $formform->ans10modelans = '2';
        $formform->ans10type = 'algebraic';
        $formform->ans10modelans = '2';
        $formform->ans10boxsize = '10';
        $formform->ans10strictsyntax = '';
        $formform->ans10insertstars = '0';
        $formform->ans10syntaxhint = '2222222222';
        $formform->ans10syntaxattribute = '0';
        $formform->ans10forbidwords = '';
        $formform->ans10allowwords = '';
        $formform->ans10forbidfloat = '1';
        $formform->ans10requirelowestterms = '0';
        $formform->ans10checkanswertype = '0';
        $formform->ans10mustverify = '1';
        $formform->ans10showvalidation = '3';
        $formform->ans10options = '';
        $formform->ans15type = 'algebraic';
        $formform->ans15modelans = '2';
        $formform->ans15type = 'algebraic';
        $formform->ans15modelans = '2';
        $formform->ans15boxsize = '15';
        $formform->ans15strictsyntax = '';
        $formform->ans15insertstars = '0';
        $formform->ans15syntaxhint = '222222222222222';
        $formform->ans15syntaxattribute = '0';
        $formform->ans15forbidwords = '';
        $formform->ans15allowwords = '';
        $formform->ans15forbidfloat = '1';
        $formform->ans15requirelowestterms = '0';
        $formform->ans15checkanswertype = '0';
        $formform->ans15mustverify = '1';
        $formform->ans15showvalidation = '3';
        $formform->ans15options = '';
        $formform->ans20type = 'algebraic';
        $formform->ans20modelans = '2';
        $formform->ans20type = 'algebraic';
        $formform->ans20modelans = '2';
        $formform->ans20boxsize = '20';
        $formform->ans20strictsyntax = '';
        $formform->ans20insertstars = '0';
        $formform->ans20syntaxhint = '12345123451234512345';
        $formform->ans20syntaxattribute = '0';
        $formform->ans20forbidwords = '';
        $formform->ans20allowwords = '';
        $formform->ans20forbidfloat = '1';
        $formform->ans20requirelowestterms = '0';
        $formform->ans20checkanswertype = '0';
        $formform->ans20mustverify = '1';
        $formform->ans20showvalidation = '3';
        $formform->ans20options = '';
        $formform->questionsimplify = '1';
        $formform->assumepositive = '';
        $formform->assumereal = '0';
        $formform->prtcorrect = [
            'text' => '<span style="font-size: 1.5em; color:green;"><i class="fa fa-check"></i></span> Correct answer, well done.',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prtpartiallycorrect = [
            'text' => '<span style="font-size: 1.5em; color:orange;"><i class="fa fa-adjust"></i></span>
                Your answer is partially correct.',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prtincorrect = [
            'text' => '<span style="font-size: 1.5em; color:red;"><i class="fa fa-times"></i></span> Incorrect answer.',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->decimals = '.';
        $formform->scientificnotation = '*10';
        $formform->multiplicationsign = 'dot';
        $formform->sqrtsign = '1';
        $formform->complexno = 'i';
        $formform->inversetrig = 'cos-1';
        $formform->logicsymbol = 'lang';
        $formform->matrixparens = '[';
        $formform->qtype = 'stack';
        $formform->numhints = 2;
        $formform->hint = [
            [
                'text' => '',
                'format' => '1',
            ],
            [
                'text' => '',
                'format' => '1',
            ],
        ];
        $formform->prt1value = 1;
        $formform->prt1feedbackstyle = '1';
        $formform->prt1feedbackvariables = '';
        $formform->prt1autosimplify = '1';
        $formform->prt1description[0] = '';
        $formform->prt1sans[0] = 'ans1';
        $formform->prt1tans[0] = 'a';
        $formform->prt1answertest[0] = 'AlgEquiv';
        $formform->prt1testoptions[0] = '';
        $formform->prt1quiet[0] = '0';
        $formform->prt1falsescore[0] = '0';
        $formform->prt1falsescoremode[0] = '=';
        $formform->prt1falsepenalty[0] = '';
        $formform->prt1falsefeedback[0] = [
            'text' => '<p>This just takes account of the first answer box!<br></p>',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prt1falseanswernote[0] = 'prt1-1-F';
        $formform->prt1falsenextnode[0] = '-1';
        $formform->prt1truescore[0] = '1';
        $formform->prt1truescoremode[0] = '=';
        $formform->prt1truepenalty[0] = '';
        $formform->prt1truefeedback[0] = [
            'text' => '<p>This just takes account of the first answer box!</p>',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prt1trueanswernote[0] = 'prt1-1-T';
        $formform->prt1truenextnode[0] = '-1';
        return $formform;
    }

    /**
     * Make the data what would be received from the editing form for Algebraic input (compact) question.
     *
     * @return stdClass the data that would be returned by $form->get_data();
     */
    public static function get_stack_question_form_data_algebraic_input_compact() {
        $formform = new stdClass();
        $formform->stackversion = '2024032401';
        $formform->name = 'Algebraic input (compact)';
        $formform->questionvariables = 'ta:n*(n+1)/2';
        $formform->questiontext = [
            'text' => '<p>What is \(\sum_{k=1}^n k = \) [[validation:ans1]] [[input:ans1]]
                [[feedback:prt1]]</p> <p>(Note, this input has compact validation and PRT.)</p>',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->questiondescription = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->specificfeedback = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->generalfeedback = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->questionnote = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->penalty = 0.1;
        $formform->variantsselectionseed = '';
        $formform->defaultmark = '1';
        $formform->ans1type = 'algebraic';
        $formform->ans1modelans = 'ta';
        $formform->ans1boxsize = '15';
        $formform->ans1strictsyntax = '';
        $formform->ans1insertstars = '2';
        $formform->ans1syntaxhint = '';
        $formform->ans1syntaxattribute = '0';
        $formform->ans1forbidwords = 'solve';
        $formform->ans1allowwords = '';
        $formform->ans1forbidfloat = '1';
        $formform->ans1requirelowestterms = '0';
        $formform->ans1checkanswertype = '1';
        $formform->ans1mustverify = '1';
        $formform->ans1showvalidation = '3';
        $formform->ans1options = '';
        $formform->questionsimplify = '1';
        $formform->assumepositive = '';
        $formform->assumereal = '0';
        $formform->prtcorrect = [
            'text' => '<span style="font-size: 1.5em; color:green;"><i class="fa fa-check"></i></span> Correct answer, well done.',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prtpartiallycorrect = [
            'text' => '<span style="font-size: 1.5em; color:orange;"><i class="fa fa-adjust"></i></span>
                Your answer is partially correct.',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prtincorrect = [
            'text' => '<span style="font-size: 1.5em; color:red;"><i class="fa fa-times"></i></span> Incorrect answer.',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->decimals = '.';
        $formform->scientificnotation = '*10';
        $formform->multiplicationsign = 'dot';
        $formform->sqrtsign = '1';
        $formform->complexno = 'i';
        $formform->inversetrig = 'cos-1';
        $formform->logicsymbol = 'lang';
        $formform->matrixparens = '[';
        $formform->qtype = 'stack';
        $formform->numhints = 2;
        $formform->hint = [
            [
                'text' => '',
                'format' => '1',
            ],
            [
                'text' => '',
                'format' => '1',
            ],
        ];
        $formform->prt1value = 1;
        $formform->prt1feedbackstyle = '2';
        $formform->prt1feedbackvariables = '';
        $formform->prt1autosimplify = '1';
        $formform->prt1description[0] = '';
        $formform->prt1sans[0] = 'ans1';
        $formform->prt1tans[0] = 'ta';
        $formform->prt1answertest[0] = 'AlgEquiv';
        $formform->prt1testoptions[0] = '';
        $formform->prt1quiet[0] = '0';
        $formform->prt1falsescore[0] = '0';
        $formform->prt1falsescoremode[0] = '=';
        $formform->prt1falsepenalty[0] = '';
        $formform->prt1falsefeedback[0] = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prt1falseanswernote[0] = 'prt1-1-F';
        $formform->prt1falsenextnode[0] = '-1';
        $formform->prt1truescore[0] = '1';
        $formform->prt1truescoremode[0] = '=';
        $formform->prt1truepenalty[0] = '';
        $formform->prt1truefeedback[0] = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prt1trueanswernote[0] = 'prt1-1-T';
        $formform->prt1truenextnode[0] = '-1';
        return $formform;
    }

    /**
     * Make the data what would be received from the editing form for Algebraic input (empty answer permitted) question.
     *
     * @return stdClass the data that would be returned by $form->get_data();
     */
    public static function get_stack_question_form_data_algebraic_input_empty() {
        $formform = new stdClass();
        $formform->stackversion = '2024032401';
        $formform->name = 'Algebraic input (empty answer permitted)';
        $formform->questionvariables = '';
        $formform->questiontext = [
            'text' => '<p>Type in \(\sin(x)\), \(\cos(x)\) and leave one input blank.</p>
                <p>[[input:ans1]] [[validation:ans1]]</p> <p>[[input:ans2]] [[validation:ans2]]</p>
                <p>[[input:ans3]] [[validation:ans3]]</p>',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->questiondescription = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->specificfeedback = [
            'text' => '[[feedback:prt1]]',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->generalfeedback = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->questionnote = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->penalty = 0.1;
        $formform->variantsselectionseed = '';
        $formform->defaultmark = '1';
        $formform->ans1type = 'algebraic';
        $formform->ans1modelans = 'sin(x)';
        $formform->ans1boxsize = '15';
        $formform->ans1strictsyntax = '';
        $formform->ans1insertstars = '0';
        $formform->ans1syntaxhint = '';
        $formform->ans1syntaxattribute = '0';
        $formform->ans1forbidwords = '';
        $formform->ans1allowwords = '';
        $formform->ans1forbidfloat = '1';
        $formform->ans1requirelowestterms = '0';
        $formform->ans1checkanswertype = '0';
        $formform->ans1mustverify = '1';
        $formform->ans1showvalidation = '1';
        $formform->ans1options = 'allowempty';
        $formform->ans2type = 'algebraic';
        $formform->ans2modelans = 'cos(x)';
        $formform->ans2type = 'algebraic';
        $formform->ans2modelans = 'cos(x)';
        $formform->ans2boxsize = '15';
        $formform->ans2strictsyntax = '';
        $formform->ans2insertstars = '0';
        $formform->ans2syntaxhint = '';
        $formform->ans2syntaxattribute = '0';
        $formform->ans2forbidwords = '';
        $formform->ans2allowwords = '';
        $formform->ans2forbidfloat = '1';
        $formform->ans2requirelowestterms = '0';
        $formform->ans2checkanswertype = '0';
        $formform->ans2mustverify = '1';
        $formform->ans2showvalidation = '1';
        $formform->ans2options = 'allowempty';
        $formform->ans3type = 'algebraic';
        $formform->ans3modelans = 'EMPTYANSWER';
        $formform->ans3type = 'algebraic';
        $formform->ans3modelans = 'EMPTYANSWER';
        $formform->ans3boxsize = '15';
        $formform->ans3strictsyntax = '';
        $formform->ans3insertstars = '0';
        $formform->ans3syntaxhint = '';
        $formform->ans3syntaxattribute = '0';
        $formform->ans3forbidwords = '';
        $formform->ans3allowwords = '';
        $formform->ans3forbidfloat = '1';
        $formform->ans3requirelowestterms = '0';
        $formform->ans3checkanswertype = '0';
        $formform->ans3mustverify = '1';
        $formform->ans3showvalidation = '1';
        $formform->ans3options = 'allowempty';
        $formform->questionsimplify = '1';
        $formform->assumepositive = '';
        $formform->assumereal = '0';
        $formform->prtcorrect = [
            'text' => '<span style="font-size: 1.5em; color:green;"><i class="fa fa-check"></i></span> Correct answer, well done.',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prtpartiallycorrect = [
            'text' => '<span style="font-size: 1.5em; color:orange;"><i class="fa fa-adjust"></i></span>
                Your answer is partially correct.',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prtincorrect = [
            'text' => '<span style="font-size: 1.5em; color:red;"><i class="fa fa-times"></i></span> Incorrect answer.',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->decimals = '.';
        $formform->scientificnotation = '*10';
        $formform->multiplicationsign = 'dot';
        $formform->sqrtsign = '1';
        $formform->complexno = 'i';
        $formform->inversetrig = 'cos-1';
        $formform->logicsymbol = 'lang';
        $formform->matrixparens = '[';
        $formform->qtype = 'stack';
        $formform->numhints = 2;
        $formform->hint = [
            [
                'text' => '',
                'format' => '1',
            ],
            [
                'text' => '',
                'format' => '1',
            ],
        ];
        $formform->prt1value = 1;
        $formform->prt1feedbackstyle = '1';
        $formform->prt1feedbackvariables = 'sa:setdifference({ans1,ans2,ans3},{EMPTYANSWER})';
        $formform->prt1autosimplify = '1';
        $formform->prt1description[0] = '';
        $formform->prt1sans[0] = 'sa';
        $formform->prt1tans[0] = '{sin(x),cos(x)}';
        $formform->prt1answertest[0] = 'Sets';
        $formform->prt1testoptions[0] = '';
        $formform->prt1quiet[0] = '0';
        $formform->prt1falsescore[0] = '0';
        $formform->prt1falsescoremode[0] = '=';
        $formform->prt1falsepenalty[0] = '';
        $formform->prt1falsefeedback[0] = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prt1falseanswernote[0] = 'prt1-1-F';
        $formform->prt1falsenextnode[0] = '-1';
        $formform->prt1truescore[0] = '1';
        $formform->prt1truescoremode[0] = '=';
        $formform->prt1truepenalty[0] = '';
        $formform->prt1truefeedback[0] = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prt1trueanswernote[0] = 'prt1-1-T';
        $formform->prt1truenextnode[0] = '-1';
        return $formform;
    }

    /**
     * Make the data what would be received from the editing form for Algebraic input (with simplification) question.
     *
     * @return stdClass the data that would be returned by $form->get_data();
     */
    public static function get_stack_question_form_data_algebraic_input_simpl() {
        $formform = new stdClass();
        $formform->stackversion = '2024032401';
        $formform->name = 'Algebraic input (with simplification)';
        $formform->questionvariables = 'ta:makelist(k^2,k,1,8)';
        $formform->questiontext = [
            'text' => '<p>Type in {@ta@}</p> <p>[[input:ans1]] [[validation:ans1]]</p>
                <p>Hint: use <code>makelist(k^2,k,1,8)</code></p>',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->questiondescription = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->specificfeedback = [
            'text' => '[[feedback:prt1]]',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->generalfeedback = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->questionnote = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->penalty = 0.1;
        $formform->variantsselectionseed = '';
        $formform->defaultmark = '1';
        $formform->ans1type = 'algebraic';
        $formform->ans1modelans = 'ta';
        $formform->ans1boxsize = '15';
        $formform->ans1strictsyntax = '';
        $formform->ans1insertstars = '0';
        $formform->ans1syntaxhint = '';
        $formform->ans1syntaxattribute = '0';
        $formform->ans1forbidwords = '';
        $formform->ans1allowwords = '';
        $formform->ans1forbidfloat = '1';
        $formform->ans1requirelowestterms = '0';
        $formform->ans1checkanswertype = '0';
        $formform->ans1mustverify = '1';
        $formform->ans1showvalidation = '1';
        $formform->ans1options = 'simp';
        $formform->questionsimplify = '1';
        $formform->assumepositive = '';
        $formform->assumereal = '0';
        $formform->prtcorrect = [
            'text' => '<span style="font-size: 1.5em; color:green;"><i class="fa fa-check"></i></span> Correct answer, well done.',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prtpartiallycorrect = [
            'text' => '<span style="font-size: 1.5em; color:orange;"><i class="fa fa-adjust"></i></span>
                Your answer is partially correct.',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prtincorrect = [
            'text' => '<span style="font-size: 1.5em; color:red;"><i class="fa fa-times"></i></span> Incorrect answer.',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->decimals = '.';
        $formform->scientificnotation = '*10';
        $formform->multiplicationsign = 'dot';
        $formform->sqrtsign = '1';
        $formform->complexno = 'i';
        $formform->inversetrig = 'cos-1';
        $formform->logicsymbol = 'lang';
        $formform->matrixparens = '[';
        $formform->qtype = 'stack';
        $formform->numhints = 2;
        $formform->hint = [
            [
                'text' => '',
                'format' => '1',
            ],
            [
                'text' => '',
                'format' => '1',
            ],
        ];
        $formform->prt1value = 1;
        $formform->prt1feedbackstyle = '1';
        $formform->prt1feedbackvariables = 'sa:ev(ans1,simp);';
        $formform->prt1autosimplify = '1';
        $formform->prt1description[0] = '';
        $formform->prt1sans[0] = 'sa';
        $formform->prt1tans[0] = 'ta';
        $formform->prt1answertest[0] = 'AlgEquiv';
        $formform->prt1testoptions[0] = '';
        $formform->prt1quiet[0] = '0';
        $formform->prt1falsescore[0] = '0';
        $formform->prt1falsescoremode[0] = '=';
        $formform->prt1falsepenalty[0] = '';
        $formform->prt1falsefeedback[0] = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prt1falseanswernote[0] = 'prt1-1-F';
        $formform->prt1falsenextnode[0] = '-1';
        $formform->prt1truescore[0] = '1';
        $formform->prt1truescoremode[0] = '=';
        $formform->prt1truepenalty[0] = '';
        $formform->prt1truefeedback[0] = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prt1trueanswernote[0] = 'prt1-1-T';
        $formform->prt1truenextnode[0] = '-1';
        return $formform;
    }

    /**
     * Make the data what would be received from the editing form for Checkbox question.
     *
     * @return stdClass the data that would be returned by $form->get_data();
     */
    public static function get_stack_question_form_data_checkbox_input() {
        $formform = new stdClass();
        $formform->stackversion = '2024032401';
        $formform->name = 'Checkbox';
        $formform->questionvariables = '/* Create a list of potential answers. */ p:sin(2*x);
                ta:[[diff(p,x),true],[p,false],[int(p,x),false],[cos(2*x)+c,false]];
                /* The actual correct answer. */ tac:diff(p,x) /* Add in a "None of these" to the end
                of the list. The Maxima value is the atom null. */
                tao:[null, false, "None of these"]; ta:append(ta,[tao]);';
        $formform->questiontext = [
            'text' => '<p>Differentiate {@p@} with respect to \(x\).</p><p>[[input:ans1]]</p><div>[[validation:ans1]]</div>',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->questiondescription = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->specificfeedback = [
            'text' => '[[feedback:prt1]]',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->generalfeedback = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->questionnote = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->penalty = 0.1;
        $formform->variantsselectionseed = '';
        $formform->defaultmark = '1';
        $formform->ans1type = 'checkbox';
        $formform->ans1modelans = 'ta';
        $formform->ans1boxsize = '15';
        $formform->ans1strictsyntax = '';
        $formform->ans1insertstars = '0';
        $formform->ans1syntaxhint = '';
        $formform->ans1syntaxattribute = '0';
        $formform->ans1forbidwords = '';
        $formform->ans1allowwords = '';
        $formform->ans1forbidfloat = '1';
        $formform->ans1requirelowestterms = '0';
        $formform->ans1checkanswertype = '0';
        $formform->ans1mustverify = '1';
        $formform->ans1showvalidation = '2';
        $formform->ans1options = '';
        $formform->questionsimplify = '1';
        $formform->assumepositive = '';
        $formform->assumereal = '0';
        $formform->prtcorrect = [
            'text' => '<span style="font-size: 1.5em; color:green;"><i class="fa fa-check"></i></span> Correct answer, well done.',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prtpartiallycorrect = [
            'text' => '<span style="font-size: 1.5em; color:orange;"><i class="fa fa-adjust"></i></span>
                Your answer is partially correct.',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prtincorrect = [
            'text' => '<span style="font-size: 1.5em; color:red;"><i class="fa fa-times"></i></span> Incorrect answer.',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->decimals = '.';
        $formform->scientificnotation = '*10';
        $formform->multiplicationsign = 'dot';
        $formform->sqrtsign = '1';
        $formform->complexno = 'i';
        $formform->inversetrig = 'cos-1';
        $formform->logicsymbol = 'lang';
        $formform->matrixparens = '[';
        $formform->qtype = 'stack';
        $formform->numhints = 2;
        $formform->hint = [
            [
                'text' => '',
                'format' => '1',
            ],
            [
                'text' => '',
                'format' => '1',
            ],
        ];
        $formform->prt1value = 1;
        $formform->prt1feedbackstyle = '1';
        $formform->prt1feedbackvariables = 'ansmod:apply("and",maplist(lambda([ex],second(ATDiff(ex,diff(p,x),x))),ans1));';
        $formform->prt1autosimplify = '1';
        $formform->prt1description[0] = '';
        $formform->prt1sans[0] = 'ansmod';
        $formform->prt1tans[0] = 'true';
        $formform->prt1answertest[0] = 'AlgEquiv';
        $formform->prt1testoptions[0] = '';
        $formform->prt1quiet[0] = '0';
        $formform->prt1falsescore[0] = '0';
        $formform->prt1falsescoremode[0] = '=';
        $formform->prt1falsepenalty[0] = '';
        $formform->prt1falsefeedback[0] = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prt1falseanswernote[0] = 'prt1-1-F';
        $formform->prt1falsenextnode[0] = '-1';
        $formform->prt1truescore[0] = '1';
        $formform->prt1truescoremode[0] = '=';
        $formform->prt1truepenalty[0] = '';
        $formform->prt1truefeedback[0] = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prt1trueanswernote[0] = 'prt1-1-T';
        $formform->prt1truenextnode[0] = '-1';
        return $formform;
    }

    /**
     * Make the data what would be received from the editing form for Checkbox (no body LaTeX) question.
     *
     * @return stdClass the data that would be returned by $form->get_data();
     */
    public static function get_stack_question_form_data_checkbox_input_no_latex() {
        $formform = new stdClass();
        $formform->stackversion = '2024032401';
        $formform->name = 'Checkbox (no body LaTeX)';
        $formform->questionvariables = '/* Create a list of potential answers.
                */ p:sin(2*x); ta:[[diff(p,x),true],[p,false],[int(p,x),false],[cos(2*x)+c,false]];
                /* The actual correct answer. */ tac:diff(p,x) tao:[null, true, "Something random"]; ta:append(ta,[tao]);';
        $formform->questiontext = [
            'text' => '<p>This question has no LaTeX in the body, to test display of LaTeX only appearing in the input.</p>
                <p>[[input:ans1]]</p><div>[[validation:ans1]]</div>',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->questiondescription = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->specificfeedback = [
            'text' => '[[feedback:prt1]]',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->generalfeedback = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->questionnote = [
            'text' => '{@ta@}',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->penalty = 0.1;
        $formform->variantsselectionseed = '';
        $formform->defaultmark = '1';
        $formform->ans1type = 'checkbox';
        $formform->ans1modelans = 'ta';
        $formform->ans1boxsize = '15';
        $formform->ans1strictsyntax = '';
        $formform->ans1insertstars = '0';
        $formform->ans1syntaxhint = '';
        $formform->ans1syntaxattribute = '0';
        $formform->ans1forbidwords = '';
        $formform->ans1allowwords = '';
        $formform->ans1forbidfloat = '1';
        $formform->ans1requirelowestterms = '0';
        $formform->ans1checkanswertype = '0';
        $formform->ans1mustverify = '1';
        $formform->ans1showvalidation = '2';
        $formform->ans1options = '';
        $formform->questionsimplify = '1';
        $formform->assumepositive = '';
        $formform->assumereal = '0';
        $formform->prtcorrect = [
            'text' => '<span style="font-size: 1.5em; color:green;"><i class="fa fa-check"></i></span> Correct answer, well done.',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prtpartiallycorrect = [
            'text' => '<span style="font-size: 1.5em; color:orange;"><i class="fa fa-adjust"></i></span>
                Your answer is partially correct.',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prtincorrect = [
            'text' => '<span style="font-size: 1.5em; color:red;"><i class="fa fa-times"></i></span> Incorrect answer.',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->decimals = '.';
        $formform->scientificnotation = '*10';
        $formform->multiplicationsign = 'dot';
        $formform->sqrtsign = '1';
        $formform->complexno = 'i';
        $formform->inversetrig = 'cos-1';
        $formform->logicsymbol = 'lang';
        $formform->matrixparens = '[';
        $formform->qtype = 'stack';
        $formform->numhints = 2;
        $formform->hint = [
            [
                'text' => '',
                'format' => '1',
            ],
            [
                'text' => '',
                'format' => '1',
            ],
        ];
        $formform->prt1value = 1;
        $formform->prt1feedbackstyle = '1';
        $formform->prt1feedbackvariables = '';
        $formform->prt1autosimplify = '1';
        $formform->prt1description[0] = '';
        $formform->prt1sans[0] = 'ans1';
        $formform->prt1tans[0] = '[diff(p,x),null]';
        $formform->prt1answertest[0] = 'AlgEquiv';
        $formform->prt1testoptions[0] = '';
        $formform->prt1quiet[0] = '0';
        $formform->prt1falsescore[0] = '0';
        $formform->prt1falsescoremode[0] = '=';
        $formform->prt1falsepenalty[0] = '';
        $formform->prt1falsefeedback[0] = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prt1falseanswernote[0] = 'prt1-1-F';
        $formform->prt1falsenextnode[0] = '-1';
        $formform->prt1truescore[0] = '1';
        $formform->prt1truescoremode[0] = '=';
        $formform->prt1truepenalty[0] = '';
        $formform->prt1truefeedback[0] = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prt1trueanswernote[0] = 'prt1-1-T';
        $formform->prt1truenextnode[0] = '-1';
        return $formform;
    }

    /**
     * Make the data what would be received from the editing form for Checkbox (plots in options)  question.
     *
     * @return stdClass the data that would be returned by $form->get_data();
     */
    public static function get_stack_question_form_data_checkbox_input_plots() {
        $formform = new stdClass();
        $formform->stackversion = '2024032401';
        $formform->name = 'Checkbox (plots in options)';
        $formform->questionvariables = 'cfn1:x^3; cfn2:atan(x); cfn3:(1/2)^x; cfn4:-x^5; wfn1:x^2-1; wfn2:(x-1)*x*(x+1);
                wfn3:1/x^2; wfn4:cos(x); xmax:3;
                ymax:3; cplot1:plot(cfn1,[x,-xmax,xmax],[y,-ymax,ymax],[box, false],[yx_ratio, 1],[axes, solid]);
                cplot2:plot(cfn2,[x,-xmax,xmax],[y,-%pi/2,%pi/2],[box, false],[yx_ratio, 1],[axes, solid]);
                cplot3:plot(cfn3,[x,-xmax,xmax],[y,0,ymax],[box, false],[yx_ratio, 1],[axes, solid]);
                cplot4:plot(cfn4,[x,-xmax,xmax],[y,-ymax,ymax],[box, false],[yx_ratio, 1],[axes, solid]);
                wplot1:plot(wfn1,[x,-xmax,xmax],[y,-1,ymax],[box, false],[yx_ratio, 1],[axes, solid]);
                wplot2:plot(wfn2,[x,-xmax,xmax],[y,-ymax,ymax],[box, false],[yx_ratio, 1],[axes, solid]);
                wplot3:plot(wfn3,[x,-xmax,xmax],[y,0,ymax],[box, false],[yx_ratio, 1],[axes, solid]);
                wplot4:plot(wfn4,[x,-xmax,xmax],[y,-1,1],[box, false],[yx_ratio, 1],[axes, solid]);
                corbase:[cplot1,cplot2,cplot3,cplot4]; wrongbase:[wplot1,wplot2,wplot3,wplot4];
                /* code lifted and adapted from multiselqnalpha */
                sel_cor: maplist(lambda([ex], [ex, true]), rand_selection(corbase, 2));
                sel_incorr: maplist(lambda([ex], [ex, false]), rand_selection(wrongbase, 2));
                opts: random_permutation(append(sel_cor,sel_incorr));
                talab: ev(makelist(sconcat("(",ascii(96+i),")"), i, 1, length(opts)), simp);
                ta1:zip_with(lambda([ex1, ex2], [ex1, ex2[2], sconcat("", ex1, " ", ex2[1])]), talab, opts);
                version: map(first, opts); corr1:mcq_correct(ta1); incorr1:mcq_incorrect(ta1);';
        $formform->questiontext = [
            'text' => '<p class="noindent">Which of the following functions are invertible?<br></p>
                [[input:ans1]] [[validation:ans1]]
                <p>(This question is to test auto-generated images appear in MCQ options.)</p>',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->questiondescription = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->specificfeedback = [
            'text' => '[[feedback:prt1]]',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->generalfeedback = [
            'text' => '<p>A function is invertible if and only if it takes each value in its range precisely once.&nbsp;
                The functions that are not invertible here are not invertible
                because they take some values more than once.<br></p>',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->questionnote = [
            'text' => '{@f@} {#version#}',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->penalty = 0.1;
        $formform->variantsselectionseed = '';
        $formform->defaultmark = '1';
        $formform->ans1type = 'checkbox';
        $formform->ans1modelans = 'ta1';
        $formform->ans1boxsize = '15';
        $formform->ans1strictsyntax = '';
        $formform->ans1insertstars = '0';
        $formform->ans1syntaxhint = '';
        $formform->ans1syntaxattribute = '0';
        $formform->ans1forbidwords = '';
        $formform->ans1allowwords = '';
        $formform->ans1forbidfloat = '1';
        $formform->ans1requirelowestterms = '1';
        $formform->ans1checkanswertype = '0';
        $formform->ans1mustverify = '0';
        $formform->ans1showvalidation = '0';
        $formform->ans1options = 'LaTeX';
        $formform->questionsimplify = '1';
        $formform->assumepositive = '';
        $formform->assumereal = '0';
        $formform->prtcorrect = [
            'text' => '<span style="font-size: 1.5em; color:green;"><i class="fa fa-check"></i></span> Correct answer, well done.',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prtpartiallycorrect = [
            'text' => '<span style="font-size: 1.5em; color:orange;"><i class="fa fa-adjust"></i></span>
                Your answer is partially correct.',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prtincorrect = [
            'text' => '<span style="font-size: 1.5em; color:red;"><i class="fa fa-times"></i></span> Incorrect answer.',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->decimals = '.';
        $formform->scientificnotation = '*10';
        $formform->multiplicationsign = 'none';
        $formform->sqrtsign = '1';
        $formform->complexno = 'i';
        $formform->inversetrig = 'cos-1';
        $formform->logicsymbol = 'lang';
        $formform->matrixparens = '[';
        $formform->qtype = 'stack';
        $formform->numhints = 2;
        $formform->hint = [
            [
                'text' => '',
                'format' => '1',
            ],
            [
                'text' => '',
                'format' => '1',
            ],
        ];
        $formform->prt1value = 1;
        $formform->prt1feedbackstyle = '1';
        $formform->prt1feedbackvariables = '';
        $formform->prt1autosimplify = '0';
        $formform->prt1description[0] = '';
        $formform->prt1sans[0] = 'setify(ans1)';
        $formform->prt1tans[0] = 'setify(corr1)';
        $formform->prt1answertest[0] = 'AlgEquiv';
        $formform->prt1testoptions[0] = '';
        $formform->prt1quiet[0] = '1';
        $formform->prt1falsescore[0] = '0';
        $formform->prt1falsescoremode[0] = '=';
        $formform->prt1falsepenalty[0] = '0.1';
        $formform->prt1falsefeedback[0] = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prt1falseanswernote[0] = 'prt1-0-F';
        $formform->prt1falsenextnode[0] = '1';
        $formform->prt1truescore[0] = '1';
        $formform->prt1truescoremode[0] = '=';
        $formform->prt1truepenalty[0] = '0';
        $formform->prt1truefeedback[0] = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prt1trueanswernote[0] = 'prt1-0-T';
        $formform->prt1truenextnode[0] = '-1';
        $formform->prt1description[1] = '';
        $formform->prt1sans[1] = 'subsetp(setify(ans1),setify(corr1))';
        $formform->prt1tans[1] = 'true';
        $formform->prt1answertest[1] = 'AlgEquiv';
        $formform->prt1testoptions[1] = '';
        $formform->prt1quiet[1] = '0';
        $formform->prt1falsescore[1] = '0';
        $formform->prt1falsescoremode[1] = '-';
        $formform->prt1falsepenalty[1] = '';
        $formform->prt1falsefeedback[1] = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prt1falseanswernote[1] = 'prt1-2-F';
        $formform->prt1falsenextnode[1] = '2';
        $formform->prt1truescore[1] = '0.5';
        $formform->prt1truescoremode[1] = '+';
        $formform->prt1truepenalty[1] = '';
        $formform->prt1truefeedback[1] = [
            'text' => '<p>You have correctly identified one of the correct answers, but missed
                \({@setdifference(setify(corr1),setify(ans1))@}\).<br></p>',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prt1trueanswernote[1] = 'prt1-2-T';
        $formform->prt1truenextnode[1] = '-1';
        $formform->prt1description[2] = '';
        $formform->prt1sans[2] = 'cardinality(ev(intersection(setify(incorr1),setify(ans1)),simp))';
        $formform->prt1tans[2] = '0';
        $formform->prt1answertest[2] = 'CasEqual';
        $formform->prt1testoptions[2] = '';
        $formform->prt1quiet[2] = '0';
        $formform->prt1falsescore[2] = '0';
        $formform->prt1falsescoremode[2] = '-';
        $formform->prt1falsepenalty[2] = '';
        $formform->prt1falsefeedback[2] = [
            'text' => '<p>You incorrectly selected {@(setdifference(setify(ans1),setify(corr1)))@}<br></p>',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prt1falseanswernote[2] = 'prt1-3-F';
        $formform->prt1falsenextnode[2] = '-1';
        $formform->prt1truescore[2] = '0';
        $formform->prt1truescoremode[2] = '+';
        $formform->prt1truepenalty[2] = '';
        $formform->prt1truefeedback[2] = [
            'text' => '<p><br></p><p>{@setify(ans1)@}<br></p><p>{@(intersection(setify(incorr),setify(ans1)))@}
                <br></p><p>{@cardinality(intersection(setify(incorr),setify(ans1)))@}</p>',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prt1trueanswernote[2] = 'prt1-3-T';
        $formform->prt1truenextnode[2] = '-1';
        return $formform;
    }

    /**
     * Make the data what would be received from the editing form for Checkbox (Show teacher's answer) question.
     *
     * @return stdClass the data that would be returned by $form->get_data();
     */
    public static function get_stack_question_form_data_checkbox_show_tans() {
        $formform = new stdClass();
        $formform->stackversion = '2024032401';
        $formform->name = "Checkbox (Show teacher's answer)";
        $formform->questionvariables = 'ta1:[A, true, "Integration by parts"]; ta2:[B, true, "Integration by substitution"];
                ta3:[C, true, "Apply a trig formula to remove product"];
                ta4:[D, true, "Remove trig with complex exponentials, then integrate"];
                ta0:[X, false, "None of the other options"]; ta:[ta1,ta2,ta3,ta4,ta0];';
        $formform->questiontext = [
            'text' => '<p>Which method would you use to find \(\int\sin(x)\cos(x)\mathrm{d} x\)?</p>
                <p>[[input:ans1]][[validation:ans1]]</p> <p>(The purpose of this question is to test the "teacher\'s answer"
                display is the string shown, not the value returned to Maxima).</p>',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->questiondescription = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->specificfeedback = [
            'text' => '[[feedback:prt1]]',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->generalfeedback = [
            'text' => '<p>Indeed, all four methods can be readily used on this integration problem!</p>',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->questionnote = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->penalty = 0.1;
        $formform->variantsselectionseed = '';
        $formform->defaultmark = '1';
        $formform->ans1type = 'checkbox';
        $formform->ans1modelans = 'ta';
        $formform->ans1boxsize = '15';
        $formform->ans1strictsyntax = '';
        $formform->ans1insertstars = '0';
        $formform->ans1syntaxhint = '';
        $formform->ans1syntaxattribute = '0';
        $formform->ans1forbidwords = '';
        $formform->ans1allowwords = '';
        $formform->ans1forbidfloat = '1';
        $formform->ans1requirelowestterms = '0';
        $formform->ans1checkanswertype = '0';
        $formform->ans1mustverify = '0';
        $formform->ans1showvalidation = '0';
        $formform->ans1options = '';
        $formform->questionsimplify = '1';
        $formform->assumepositive = '';
        $formform->assumereal = '0';
        $formform->prtcorrect = [
            'text' => '<span style="font-size: 1.5em; color:green;"><i class="fa fa-check"></i></span> Correct answer, well done.',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prtpartiallycorrect = [
            'text' => '<span style="font-size: 1.5em; color:orange;"><i class="fa fa-adjust"></i></span>
                Your answer is partially correct.',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prtincorrect = [
            'text' => '<span style="font-size: 1.5em; color:red;"><i class="fa fa-times"></i></span> Incorrect answer.',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->decimals = '.';
        $formform->scientificnotation = '*10';
        $formform->multiplicationsign = 'dot';
        $formform->sqrtsign = '1';
        $formform->complexno = 'i';
        $formform->inversetrig = 'cos-1';
        $formform->logicsymbol = 'lang';
        $formform->matrixparens = '[';
        $formform->qtype = 'stack';
        $formform->numhints = 2;
        $formform->hint = [
            [
                'text' => '',
                'format' => '1',
            ],
            [
                'text' => '',
                'format' => '1',
            ],
        ];
        $formform->prt1value = 1;
        $formform->prt1feedbackstyle = '1';
        $formform->prt1feedbackvariables = '';
        $formform->prt1autosimplify = '1';
        $formform->prt1description[0] = '';
        $formform->prt1sans[0] = 'ans1';
        $formform->prt1tans[0] = '[A,B,C,D]';
        $formform->prt1answertest[0] = 'AlgEquiv';
        $formform->prt1testoptions[0] = '';
        $formform->prt1quiet[0] = '0';
        $formform->prt1falsescore[0] = '0';
        $formform->prt1falsescoremode[0] = '=';
        $formform->prt1falsepenalty[0] = '';
        $formform->prt1falsefeedback[0] = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prt1falseanswernote[0] = 'prt1-1-F';
        $formform->prt1falsenextnode[0] = '-1';
        $formform->prt1truescore[0] = '1';
        $formform->prt1truescoremode[0] = '=';
        $formform->prt1truepenalty[0] = '';
        $formform->prt1truefeedback[0] = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prt1trueanswernote[0] = 'prt1-1-T';
        $formform->prt1truenextnode[0] = '-1';
        return $formform;
    }

    /**
     * Make the data what would be received from the editing form for Dropdown (shuffle) question.
     *
     * @return stdClass the data that would be returned by $form->get_data();
     */
    public static function get_stack_question_form_data_dropdown_input() {
        $formform = new stdClass();
        $formform->stackversion = '2024032401';
        $formform->name = 'Dropdown (shuffle)';
        $formform->questionvariables = '/* Create a list of potential answers. */ p:sin(2*x);
                ta:[[diff(p,x),true],[p,false],[int(p,x),false],[cos(2*x)+c,false]];
                /* The actual correct answer. */ tac:diff(p,x) /* Randomly shuffle the list "ta". */
                ta:random_permutation(ta); /* Add in a "None of these" to the end of the list.
                The Maxima value is the atom null. */ tao:[null, false, "None of these"]; ta:append(ta,[tao]);';
        $formform->questiontext = [
            'text' => '<p>Differentiate {@p@} with respect to \(x\).</p><p>[[input:ans1]]</p><div>[[validation:ans1]]</div>',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->questiondescription = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->specificfeedback = [
            'text' => '[[feedback:prt1]]',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->generalfeedback = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->questionnote = [
            'text' => '{@ta@}',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->penalty = 0.1;
        $formform->variantsselectionseed = '';
        $formform->defaultmark = '1';
        $formform->ans1type = 'dropdown';
        $formform->ans1modelans = 'ta';
        $formform->ans1boxsize = '15';
        $formform->ans1strictsyntax = '';
        $formform->ans1insertstars = '0';
        $formform->ans1syntaxhint = '';
        $formform->ans1syntaxattribute = '0';
        $formform->ans1forbidwords = '';
        $formform->ans1allowwords = '';
        $formform->ans1forbidfloat = '1';
        $formform->ans1requirelowestterms = '0';
        $formform->ans1checkanswertype = '0';
        $formform->ans1mustverify = '1';
        $formform->ans1showvalidation = '2';
        $formform->ans1options = '';
        $formform->questionsimplify = '1';
        $formform->assumepositive = '';
        $formform->assumereal = '0';
        $formform->prtcorrect = [
            'text' => '<span style="font-size: 1.5em; color:green;"><i class="fa fa-check"></i></span> Correct answer, well done.',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prtpartiallycorrect = [
            'text' => '<span style="font-size: 1.5em; color:orange;"><i class="fa fa-adjust"></i></span>
                Your answer is partially correct.',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prtincorrect = [
            'text' => '<span style="font-size: 1.5em; color:red;"><i class="fa fa-times"></i></span> Incorrect answer.',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->decimals = '.';
        $formform->scientificnotation = '*10';
        $formform->multiplicationsign = 'dot';
        $formform->sqrtsign = '1';
        $formform->complexno = 'i';
        $formform->inversetrig = 'cos-1';
        $formform->logicsymbol = 'lang';
        $formform->matrixparens = '[';
        $formform->qtype = 'stack';
        $formform->numhints = 2;
        $formform->hint = [
            [
                'text' => '',
                'format' => '1',
            ],
            [
                'text' => '',
                'format' => '1',
            ],
        ];
        $formform->prt1value = 1;
        $formform->prt1feedbackstyle = '1';
        $formform->prt1feedbackvariables = '';
        $formform->prt1autosimplify = '1';
        $formform->prt1description[0] = '';
        $formform->prt1sans[0] = 'ans1';
        $formform->prt1tans[0] = 'tac';
        $formform->prt1answertest[0] = 'Diff';
        $formform->prt1testoptions[0] = 'x';
        $formform->prt1quiet[0] = '1';
        $formform->prt1falsescore[0] = '0';
        $formform->prt1falsescoremode[0] = '=';
        $formform->prt1falsepenalty[0] = '';
        $formform->prt1falsefeedback[0] = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prt1falseanswernote[0] = 'prt1-1-F';
        $formform->prt1falsenextnode[0] = '-1';
        $formform->prt1truescore[0] = '1';
        $formform->prt1truescoremode[0] = '=';
        $formform->prt1truepenalty[0] = '';
        $formform->prt1truefeedback[0] = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prt1trueanswernote[0] = 'prt1-1-T';
        $formform->prt1truenextnode[0] = '-1';
        return $formform;
    }

    /**
     * Make the data what would be received from the editing form for Equiv input test (compact) question.
     *
     * @return stdClass the data that would be returned by $form->get_data();
     */
    public static function get_stack_question_form_data_equiv_input_compact() {
        $formform = new stdClass();
        $formform->stackversion = '2024032401';
        $formform->name = 'Equiv input test (compact)';
        $formform->questionvariables = 'v:x p:3*v+7=4 ta:[p,x=(4-7)/3,x=-1]';
        $formform->questiontext = [
            'text' => '<p>Solve {@p@}.</p><p>[[input:ans1]] [[validation:ans1]]</p>',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->questiondescription = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->specificfeedback = [
            'text' => '[[feedback:prt1]]',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->generalfeedback = [
            'text' => '<p>sangwinc<br></p>',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->questionnote = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->penalty = 0.1;
        $formform->variantsselectionseed = '';
        $formform->defaultmark = '1';
        $formform->ans1type = 'equiv';
        $formform->ans1modelans = 'ta';
        $formform->ans1boxsize = '15';
        $formform->ans1strictsyntax = '';
        $formform->ans1insertstars = '5';
        $formform->ans1syntaxhint = 'firstline';
        $formform->ans1syntaxattribute = '0';
        $formform->ans1forbidwords = '';
        $formform->ans1allowwords = '';
        $formform->ans1forbidfloat = '1';
        $formform->ans1requirelowestterms = '0';
        $formform->ans1checkanswertype = '1';
        $formform->ans1mustverify = '1';
        $formform->ans1showvalidation = '3';
        $formform->ans1options = '';
        $formform->questionsimplify = '0';
        $formform->assumepositive = '';
        $formform->assumereal = '0';
        $formform->prtcorrect = [
            'text' => '<span style="font-size: 1.5em; color:green;"><i class="fa fa-check"></i></span> Correct answer, well done.',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prtpartiallycorrect = [
            'text' => '<span style="font-size: 1.5em; color:orange;"><i class="fa fa-adjust"></i></span>
                Your answer is partially correct.',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prtincorrect = [
            'text' => '<span style="font-size: 1.5em; color:red;"><i class="fa fa-times"></i></span> Incorrect answer.',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->decimals = '.';
        $formform->scientificnotation = '*10';
        $formform->multiplicationsign = 'dot';
        $formform->sqrtsign = '1';
        $formform->complexno = 'i';
        $formform->inversetrig = 'cos-1';
        $formform->logicsymbol = 'lang';
        $formform->matrixparens = '[';
        $formform->qtype = 'stack';
        $formform->numhints = 2;
        $formform->hint = [
            [
                'text' => '',
                'format' => '1',
            ],
            [
                'text' => '',
                'format' => '1',
            ],
        ];
        $formform->prt1value = 1;
        $formform->prt1feedbackstyle = '1';
        $formform->prt1feedbackvariables = '';
        $formform->prt1autosimplify = '1';
        $formform->prt1description[0] = '';
        $formform->prt1sans[0] = 'last(ans1)';
        $formform->prt1tans[0] = 'last(ta)';
        $formform->prt1answertest[0] = 'AlgEquiv';
        $formform->prt1testoptions[0] = '';
        $formform->prt1quiet[0] = '0';
        $formform->prt1falsescore[0] = '0';
        $formform->prt1falsescoremode[0] = '=';
        $formform->prt1falsepenalty[0] = '';
        $formform->prt1falsefeedback[0] = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prt1falseanswernote[0] = 'prt1-1-F';
        $formform->prt1falsenextnode[0] = '-1';
        $formform->prt1truescore[0] = '1';
        $formform->prt1truescoremode[0] = '=';
        $formform->prt1truepenalty[0] = '';
        $formform->prt1truefeedback[0] = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prt1trueanswernote[0] = 'prt1-1-T';
        $formform->prt1truenextnode[0] = '-1';
        return $formform;
    }

    /**
     * Make the data what would be received from the editing form for Equiv input test (let, or +-) question.
     *
     * @return stdClass the data that would be returned by $form->get_data();
     */
    public static function get_stack_question_form_data_equiv_input() {
        $formform = new stdClass();
        $formform->stackversion = '2024032401';
        $formform->name = 'Equiv input test (let, or +-)';
        $formform->questionvariables = 'tal:[(x-a)^2=4,x-a= #pm#2,x=a#pm#2,x=a+2 nounor x=a-2,stacklet(a,1),x=3 nounor x=-1];
                p:first(tal);';
        $formform->questiontext = [
            'text' => '<p>Solve {@p@} and let \(a=1\).</p> <p>[[input:ans1]] [[validation:ans1]]</p>
                <p>(This tests "let", "or" and "+-".)</p>',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->questiondescription = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->specificfeedback = [
            'text' => '[[feedback:prt1]]',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->generalfeedback = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->questionnote = [
            'text' => '{@tal@}',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->penalty = 0.1;
        $formform->variantsselectionseed = '';
        $formform->defaultmark = '1';
        $formform->ans1type = 'equiv';
        $formform->ans1modelans = 'tal';
        $formform->ans1boxsize = '15';
        $formform->ans1strictsyntax = '';
        $formform->ans1insertstars = '5';
        $formform->ans1syntaxhint = 'firstline';
        $formform->ans1syntaxattribute = '0';
        $formform->ans1forbidwords = '';
        $formform->ans1allowwords = '';
        $formform->ans1forbidfloat = '1';
        $formform->ans1requirelowestterms = '0';
        $formform->ans1checkanswertype = '1';
        $formform->ans1mustverify = '1';
        $formform->ans1showvalidation = '1';
        $formform->ans1options = '';
        $formform->questionsimplify = '0';
        $formform->assumepositive = '';
        $formform->assumereal = '0';
        $formform->prtcorrect = [
            'text' => '<span style="font-size: 1.5em; color:green;"><i class="fa fa-check"></i></span> Correct answer, well done.',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prtpartiallycorrect = [
            'text' => '<span style="font-size: 1.5em; color:orange;"><i class="fa fa-adjust"></i></span>
                Your answer is partially correct.',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prtincorrect = [
            'text' => '<span style="font-size: 1.5em; color:red;"><i class="fa fa-times"></i></span> Incorrect answer.',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->decimals = '.';
        $formform->scientificnotation = '*10';
        $formform->multiplicationsign = 'dot';
        $formform->sqrtsign = '1';
        $formform->complexno = 'i';
        $formform->inversetrig = 'cos-1';
        $formform->logicsymbol = 'lang';
        $formform->matrixparens = '[';
        $formform->qtype = 'stack';
        $formform->numhints = 2;
        $formform->hint = [
            [
                'text' => '',
                'format' => '1',
            ],
            [
                'text' => '',
                'format' => '1',
            ],
        ];
        $formform->prt1value = 1;
        $formform->prt1feedbackstyle = '1';
        $formform->prt1feedbackvariables = '';
        $formform->prt1autosimplify = '1';
        $formform->prt1description[0] = '';
        $formform->prt1sans[0] = 'ans1';
        $formform->prt1tans[0] = 'tal';
        $formform->prt1answertest[0] = 'EquivFirst';
        $formform->prt1testoptions[0] = '';
        $formform->prt1quiet[0] = '0';
        $formform->prt1falsescore[0] = '0';
        $formform->prt1falsescoremode[0] = '=';
        $formform->prt1falsepenalty[0] = '';
        $formform->prt1falsefeedback[0] = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prt1falseanswernote[0] = 'prt1-1-F';
        $formform->prt1falsenextnode[0] = '-1';
        $formform->prt1truescore[0] = '1';
        $formform->prt1truescoremode[0] = '=';
        $formform->prt1truepenalty[0] = '';
        $formform->prt1truefeedback[0] = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prt1trueanswernote[0] = 'prt1-1-T';
        $formform->prt1truenextnode[0] = '1';
        $formform->prt1description[1] = '';
        $formform->prt1sans[1] = 'last(ans1)';
        $formform->prt1tans[1] = 'last(tal)';
        $formform->prt1answertest[1] = 'EqualComAss';
        $formform->prt1testoptions[1] = '';
        $formform->prt1quiet[1] = '0';
        $formform->prt1falsescore[1] = '0';
        $formform->prt1falsescoremode[1] = '-';
        $formform->prt1falsepenalty[1] = '';
        $formform->prt1falsefeedback[1] = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prt1falseanswernote[1] = 'prt1-2-F';
        $formform->prt1falsenextnode[1] = '-1';
        $formform->prt1truescore[1] = '0';
        $formform->prt1truescoremode[1] = '+';
        $formform->prt1truepenalty[1] = '';
        $formform->prt1truefeedback[1] = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prt1trueanswernote[1] = 'prt1-2-T';
        $formform->prt1truenextnode[1] = '-1';
        return $formform;
    }

    /**
     * Make the data what would be received from the editing form for Matrix question.
     *
     * @return stdClass the data that would be returned by $form->get_data();
     */
    public static function get_stack_question_form_data_matrix_input() {
        $formform = new stdClass();
        $formform->stackversion = '2024032401';
        $formform->name = 'Matrix';
        $formform->questionvariables = 'M:matrix([1,2],[3,4])';
        $formform->questiontext = [
            'text' => '<p>Type in {@M@}<br></p><p>[[input:ans1]]</p><div>[[validation:ans1]]</div>',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->questiondescription = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->specificfeedback = [
            'text' => '[[feedback:prt1]]',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->generalfeedback = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->questionnote = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->penalty = 0.1;
        $formform->variantsselectionseed = '';
        $formform->defaultmark = '1';
        $formform->ans1type = 'matrix';
        $formform->ans1modelans = 'M';
        $formform->ans1boxsize = '15';
        $formform->ans1strictsyntax = '';
        $formform->ans1insertstars = '0';
        $formform->ans1syntaxhint = '';
        $formform->ans1syntaxattribute = '0';
        $formform->ans1forbidwords = '';
        $formform->ans1allowwords = '';
        $formform->ans1forbidfloat = '1';
        $formform->ans1requirelowestterms = '0';
        $formform->ans1checkanswertype = '1';
        $formform->ans1mustverify = '1';
        $formform->ans1showvalidation = '1';
        $formform->ans1options = '';
        $formform->questionsimplify = '1';
        $formform->assumepositive = '';
        $formform->assumereal = '0';
        $formform->prtcorrect = [
            'text' => '<span style="font-size: 1.5em; color:green;"><i class="fa fa-check"></i></span> Correct answer, well done.',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prtpartiallycorrect = [
            'text' => '<span style="font-size: 1.5em; color:orange;"><i class="fa fa-adjust"></i></span>
                Your answer is partially correct.',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prtincorrect = [
            'text' => '<span style="font-size: 1.5em; color:red;"><i class="fa fa-times"></i></span> Incorrect answer.',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->decimals = '.';
        $formform->scientificnotation = '*10';
        $formform->multiplicationsign = 'dot';
        $formform->sqrtsign = '1';
        $formform->complexno = 'i';
        $formform->inversetrig = 'cos-1';
        $formform->logicsymbol = 'lang';
        $formform->matrixparens = '[';
        $formform->qtype = 'stack';
        $formform->numhints = 2;
        $formform->hint = [
            [
                'text' => '',
                'format' => '1',
            ],
            [
                'text' => '',
                'format' => '1',
            ],
        ];
        $formform->prt1value = 1;
        $formform->prt1feedbackstyle = '1';
        $formform->prt1feedbackvariables = '';
        $formform->prt1autosimplify = '1';
        $formform->prt1description[0] = '';
        $formform->prt1sans[0] = 'ans1';
        $formform->prt1tans[0] = 'M';
        $formform->prt1answertest[0] = 'AlgEquiv';
        $formform->prt1testoptions[0] = '';
        $formform->prt1quiet[0] = '0';
        $formform->prt1falsescore[0] = '0';
        $formform->prt1falsescoremode[0] = '=';
        $formform->prt1falsepenalty[0] = '';
        $formform->prt1falsefeedback[0] = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prt1falseanswernote[0] = 'prt1-1-F';
        $formform->prt1falsenextnode[0] = '-1';
        $formform->prt1truescore[0] = '1';
        $formform->prt1truescoremode[0] = '=';
        $formform->prt1truepenalty[0] = '';
        $formform->prt1truefeedback[0] = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prt1trueanswernote[0] = 'prt1-1-T';
        $formform->prt1truenextnode[0] = '-1';
        return $formform;
    }

    /**
     * Make the data what would be received from the editing form for Matrix (varmatrix) question.
     *
     * @return stdClass the data that would be returned by $form->get_data();
     */
    public static function get_stack_question_form_data_varmatrix_input() {
        $formform = new stdClass();
        $formform->stackversion = '2024032401';
        $formform->name = 'Matrix (varmatrix)';
        $formform->questionvariables = 'M1:matrix([1,0],[0,1]); TA1:matrix([1, 0, 0, 0],[0,1,0,0]);
                TA2:matrix([1,0],[0,1],[0,0],[0,0]);';
        $formform->questiontext = [
            'text' => '<p>Find two non-square matrices which solve the following equation.</p>
                <p>[[input:ans1]] \(\times\) [[input:ans2]] = {@M1@}</p>
                <p> [[validation:ans1]] \(\times\) [[validation:ans2]] \( = ? \) </p>',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->questiondescription = [
            'text' => '',
            'format' => '0',
            'itemid' => 0,
        ];
        $formform->specificfeedback = [
            'text' => '[[feedback:prt1]]',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->generalfeedback = [
            'text' => 'Plenty of ways of adding additional information which is not needed by extending the matrices.',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->questionnote = [
            'text' => '',
            'format' => '0',
            'itemid' => 0,
        ];
        $formform->penalty = 0.1;
        $formform->variantsselectionseed = '';
        $formform->defaultmark = '1';
        $formform->ans1type = 'varmatrix';
        $formform->ans1modelans = 'TA1';
        $formform->ans1boxsize = '3';
        $formform->ans1strictsyntax = '';
        $formform->ans1insertstars = '0';
        $formform->ans1syntaxhint = '';
        $formform->ans1syntaxattribute = '0';
        $formform->ans1forbidwords = '';
        $formform->ans1allowwords = '';
        $formform->ans1forbidfloat = '1';
        $formform->ans1requirelowestterms = '0';
        $formform->ans1checkanswertype = '0';
        $formform->ans1mustverify = '1';
        $formform->ans1showvalidation = '3';
        $formform->ans1options = '';
        $formform->ans2type = 'varmatrix';
        $formform->ans2modelans = 'TA2';
        $formform->ans2type = 'varmatrix';
        $formform->ans2modelans = 'TA2';
        $formform->ans2boxsize = '3';
        $formform->ans2strictsyntax = '';
        $formform->ans2insertstars = '0';
        $formform->ans2syntaxhint = '';
        $formform->ans2syntaxattribute = '0';
        $formform->ans2forbidwords = '';
        $formform->ans2allowwords = '';
        $formform->ans2forbidfloat = '1';
        $formform->ans2requirelowestterms = '0';
        $formform->ans2checkanswertype = '0';
        $formform->ans2mustverify = '1';
        $formform->ans2showvalidation = '3';
        $formform->ans2options = '';
        $formform->questionsimplify = '1';
        $formform->assumepositive = '';
        $formform->assumereal = '0';
        $formform->prtcorrect = [
            'text' => '<span style="font-size: 1.5em; color:green;"><i class="fa fa-check"></i></span> Correct answer, well done.',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prtpartiallycorrect = [
            'text' => '<span style="font-size: 1.5em; color:orange;"><i class="fa fa-adjust"></i></span>
                Your answer is partially correct.',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prtincorrect = [
            'text' => '<span style="font-size: 1.5em; color:red;"><i class="fa fa-times"></i></span> Incorrect answer.',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->decimals = '.';
        $formform->scientificnotation = '*10';
        $formform->multiplicationsign = 'dot';
        $formform->sqrtsign = '1';
        $formform->complexno = 'i';
        $formform->inversetrig = 'cos-1';
        $formform->logicsymbol = 'symbol';
        $formform->matrixparens = '(';
        $formform->qtype = 'stack';
        $formform->numhints = 2;
        $formform->hint = [
            [
                'text' => '',
                'format' => '1',
            ],
            [
                'text' => '',
                'format' => '1',
            ],
        ];
        $formform->prt1value = 1;
        $formform->prt1feedbackstyle = '1';
        $formform->prt1feedbackvariables = 'sz1:matrix_size(ans1); sz2:matrix_size(ans2);';
        $formform->prt1autosimplify = '1';
        $formform->prt1description[0] = '';
        $formform->prt1sans[0] = 'first(sz1)';
        $formform->prt1tans[0] = 'second(sz1)';
        $formform->prt1answertest[0] = 'AlgEquiv';
        $formform->prt1testoptions[0] = '';
        $formform->prt1quiet[0] = '1';
        $formform->prt1falsescore[0] = '0';
        $formform->prt1falsescoremode[0] = '=';
        $formform->prt1falsepenalty[0] = '';
        $formform->prt1falsefeedback[0] = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prt1falseanswernote[0] = 'prt1-1-F';
        $formform->prt1falsenextnode[0] = '1';
        $formform->prt1truescore[0] = '1';
        $formform->prt1truescoremode[0] = '=';
        $formform->prt1truepenalty[0] = '';
        $formform->prt1truefeedback[0] = [
            'text' => 'Your first matrix should not be square!',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prt1trueanswernote[0] = 'prt1-1-T';
        $formform->prt1truenextnode[0] = '1';
        $formform->prt1description[1] = '';
        $formform->prt1sans[1] = 'first(sz2)';
        $formform->prt1tans[1] = 'second(sz2)';
        $formform->prt1answertest[1] = 'AlgEquiv';
        $formform->prt1testoptions[1] = '';
        $formform->prt1quiet[1] = '0';
        $formform->prt1falsescore[1] = '0';
        $formform->prt1falsescoremode[1] = '-';
        $formform->prt1falsepenalty[1] = '';
        $formform->prt1falsefeedback[1] = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prt1falseanswernote[1] = 'prt1-2-F';
        $formform->prt1falsenextnode[1] = '2';
        $formform->prt1truescore[1] = '0';
        $formform->prt1truescoremode[1] = '+';
        $formform->prt1truepenalty[1] = '';
        $formform->prt1truefeedback[1] = [
            'text' => 'Your second matrix should not be square!',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prt1trueanswernote[1] = 'prt1-2-T';
        $formform->prt1truenextnode[1] = '2';
        $formform->prt1description[2] = '';
        $formform->prt1sans[2] = 'second(sz1)';
        $formform->prt1tans[2] = 'first(sz2)';
        $formform->prt1answertest[2] = 'AlgEquiv';
        $formform->prt1testoptions[2] = '';
        $formform->prt1quiet[2] = '0';
        $formform->prt1falsescore[2] = '0';
        $formform->prt1falsescoremode[2] = '=';
        $formform->prt1falsepenalty[2] = '';
        $formform->prt1falsefeedback[2] = [
            'text' => 'It is impossible to multiply {@ans1@} with {@ans2@}!',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prt1falseanswernote[2] = 'prt1-3-F';
        $formform->prt1falsenextnode[2] = '-1';
        $formform->prt1truescore[2] = '0';
        $formform->prt1truescoremode[2] = '+';
        $formform->prt1truepenalty[2] = '';
        $formform->prt1truefeedback[2] = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prt1trueanswernote[2] = 'prt1-3-T';
        $formform->prt1truenextnode[2] = '3';
        $formform->prt1description[3] = '';
        $formform->prt1sans[3] = 'ans1.ans2';
        $formform->prt1tans[3] = 'M1';
        $formform->prt1answertest[3] = 'AlgEquiv';
        $formform->prt1testoptions[3] = '';
        $formform->prt1quiet[3] = '0';
        $formform->prt1falsescore[3] = '0';
        $formform->prt1falsescoremode[3] = '-';
        $formform->prt1falsepenalty[3] = '';
        $formform->prt1falsefeedback[3] = [
            'text' => '\[ {@ans1@}{@ans2@} = {@ans1.ans2@} \neq {@M1@} \]',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prt1falseanswernote[3] = 'prt1-4-F';
        $formform->prt1falsenextnode[3] = '-1';
        $formform->prt1truescore[3] = '1';
        $formform->prt1truescoremode[3] = '=';
        $formform->prt1truepenalty[3] = '';
        $formform->prt1truefeedback[3] = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prt1trueanswernote[3] = 'prt1-4-T';
        $formform->prt1truenextnode[3] = '-1';
        return $formform;
    }

    /**
     * Make the data what would be received from the editing form for Matrix-multi question.
     *
     * @return stdClass the data that would be returned by $form->get_data();
     */
    public static function get_stack_question_form_data_matrix_multi_input() {
        $formform = new stdClass();
        $formform->stackversion = '2024032401';
        $formform->name = 'Matrix-multi';
        $formform->questionvariables = 'M1:matrix([1,2],[3,4]); M2:matrix([a,b],[c,d]);';
        $formform->questiontext = [
            'text' => '<p>Don\'t type in the same matrix twice! Well, this question is to help confirm instant
                validation works with more than one matrix in a given question.</p> <p>[[input:ans1]] \(\neq \)
                [[input:ans2]] </p> <p>[[validation:ans1]] [[validation:ans2]]</p>',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->questiondescription = [
            'text' => '',
            'format' => '0',
            'itemid' => 0,
        ];
        $formform->specificfeedback = [
            'text' => '[[feedback:prt1]]',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->generalfeedback = [
            'text' => 'Just about anything random should do here! I chose \[ {@M1@} \neq {@M2@}.\]',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->questionnote = [
            'text' => '',
            'format' => '0',
            'itemid' => 0,
        ];
        $formform->penalty = 0.1;
        $formform->variantsselectionseed = '';
        $formform->defaultmark = '1';
        $formform->ans1type = 'matrix';
        $formform->ans1modelans = 'M1';
        $formform->ans1boxsize = '3';
        $formform->ans1strictsyntax = '';
        $formform->ans1insertstars = '0';
        $formform->ans1syntaxhint = '';
        $formform->ans1syntaxattribute = '0';
        $formform->ans1forbidwords = '';
        $formform->ans1allowwords = '';
        $formform->ans1forbidfloat = '1';
        $formform->ans1requirelowestterms = '0';
        $formform->ans1checkanswertype = '0';
        $formform->ans1mustverify = '1';
        $formform->ans1showvalidation = '1';
        $formform->ans1options = '';
        $formform->ans2type = 'matrix';
        $formform->ans2modelans = 'M2';
        $formform->ans2type = 'matrix';
        $formform->ans2modelans = 'M2';
        $formform->ans2boxsize = '3';
        $formform->ans2strictsyntax = '';
        $formform->ans2insertstars = '0';
        $formform->ans2syntaxhint = '';
        $formform->ans2syntaxattribute = '0';
        $formform->ans2forbidwords = '';
        $formform->ans2allowwords = '';
        $formform->ans2forbidfloat = '1';
        $formform->ans2requirelowestterms = '0';
        $formform->ans2checkanswertype = '0';
        $formform->ans2mustverify = '1';
        $formform->ans2showvalidation = '1';
        $formform->ans2options = '';
        $formform->questionsimplify = '1';
        $formform->assumepositive = '';
        $formform->assumereal = '0';
        $formform->prtcorrect = [
            'text' => '<span style="font-size: 1.5em; color:green;"><i class="fa fa-check"></i></span> Correct answer, well done.',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prtpartiallycorrect = [
            'text' => '<span style="font-size: 1.5em; color:orange;"><i class="fa fa-adjust"></i></span>
                Your answer is partially correct.',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prtincorrect = [
            'text' => '<span style="font-size: 1.5em; color:red;"><i class="fa fa-times"></i></span> Incorrect answer.',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->decimals = '.';
        $formform->scientificnotation = '*10';
        $formform->multiplicationsign = 'dot';
        $formform->sqrtsign = '1';
        $formform->complexno = 'i';
        $formform->inversetrig = 'cos-1';
        $formform->logicsymbol = 'lang';
        $formform->matrixparens = '[';
        $formform->qtype = 'stack';
        $formform->numhints = 2;
        $formform->hint = [
            [
                'text' => '',
                'format' => '1',
            ],
            [
                'text' => '',
                'format' => '1',
            ],
        ];
        $formform->prt1value = 1;
        $formform->prt1feedbackstyle = '1';
        $formform->prt1feedbackvariables = '';
        $formform->prt1autosimplify = '1';
        $formform->prt1description[0] = '';
        $formform->prt1sans[0] = 'ans1';
        $formform->prt1tans[0] = 'ans2';
        $formform->prt1answertest[0] = 'AlgEquiv';
        $formform->prt1testoptions[0] = '';
        $formform->prt1quiet[0] = '1';
        $formform->prt1falsescore[0] = '1';
        $formform->prt1falsescoremode[0] = '=';
        $formform->prt1falsepenalty[0] = '';
        $formform->prt1falsefeedback[0] = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prt1falseanswernote[0] = 'prt1-1-F';
        $formform->prt1falsenextnode[0] = '-1';
        $formform->prt1truescore[0] = '0';
        $formform->prt1truescoremode[0] = '=';
        $formform->prt1truepenalty[0] = '';
        $formform->prt1truefeedback[0] = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prt1trueanswernote[0] = 'prt1-1-T';
        $formform->prt1truenextnode[0] = '-1';
        return $formform;
    }

    /**
     * Make the data what would be received from the editing form for Notes question.
     *
     * @return stdClass the data that would be returned by $form->get_data();
     */
    public static function get_stack_question_form_data_notes_input() {
        $formform = new stdClass();
        $formform->stackversion = '2024032401';
        $formform->name = 'Notes';
        $formform->questionvariables = '';
        $formform->questiontext = [
            'text' => '<p>Show your working in this box!<br></p><p>[[input:ans1]]</p><div>[[validation:ans1]]</div>',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->questiondescription = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->specificfeedback = [
            'text' => '[[feedback:prt1]]',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->generalfeedback = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->questionnote = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->penalty = 0.1;
        $formform->variantsselectionseed = '';
        $formform->defaultmark = '1';
        $formform->ans1type = 'notes';
        $formform->ans1modelans = 'true';
        $formform->ans1boxsize = '15';
        $formform->ans1strictsyntax = '';
        $formform->ans1insertstars = '0';
        $formform->ans1syntaxhint = '';
        $formform->ans1syntaxattribute = '0';
        $formform->ans1forbidwords = '';
        $formform->ans1allowwords = '';
        $formform->ans1forbidfloat = '1';
        $formform->ans1requirelowestterms = '0';
        $formform->ans1checkanswertype = '0';
        $formform->ans1mustverify = '1';
        $formform->ans1showvalidation = '1';
        $formform->ans1options = '';
        $formform->questionsimplify = '1';
        $formform->assumepositive = '';
        $formform->assumereal = '0';
        $formform->prtcorrect = [
            'text' => '<span style="font-size: 1.5em; color:green;"><i class="fa fa-check"></i></span> Correct answer, well done.',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prtpartiallycorrect = [
            'text' => '<span style="font-size: 1.5em; color:orange;"><i class="fa fa-adjust"></i></span>
                Your answer is partially correct.',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prtincorrect = [
            'text' => '<span style="font-size: 1.5em; color:red;"><i class="fa fa-times"></i></span> Incorrect answer.',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->decimals = '.';
        $formform->scientificnotation = '*10';
        $formform->multiplicationsign = 'dot';
        $formform->sqrtsign = '1';
        $formform->complexno = 'i';
        $formform->inversetrig = 'cos-1';
        $formform->logicsymbol = 'lang';
        $formform->matrixparens = '[';
        $formform->qtype = 'stack';
        $formform->numhints = 2;
        $formform->hint = [
            [
                'text' => '',
                'format' => '1',
            ],
            [
                'text' => '',
                'format' => '1',
            ],
        ];
        $formform->prt1value = 1;
        $formform->prt1feedbackstyle = '1';
        $formform->prt1feedbackvariables = '';
        $formform->prt1autosimplify = '1';
        $formform->prt1description[0] = '';
        $formform->prt1sans[0] = 'ans1';
        $formform->prt1tans[0] = 'true';
        $formform->prt1answertest[0] = 'AlgEquiv';
        $formform->prt1testoptions[0] = '';
        $formform->prt1quiet[0] = '0';
        $formform->prt1falsescore[0] = '0';
        $formform->prt1falsescoremode[0] = '=';
        $formform->prt1falsepenalty[0] = '';
        $formform->prt1falsefeedback[0] = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prt1falseanswernote[0] = 'prt1-1-F';
        $formform->prt1falsenextnode[0] = '-1';
        $formform->prt1truescore[0] = '1';
        $formform->prt1truescoremode[0] = '=';
        $formform->prt1truepenalty[0] = '';
        $formform->prt1truefeedback[0] = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prt1trueanswernote[0] = 'prt1-1-T';
        $formform->prt1truenextnode[0] = '-1';
        return $formform;
    }

    /**
     * Make the data what would be received from the editing form for Numerical input (min sf) question.
     *
     * @return stdClass the data that would be returned by $form->get_data();
     */
    public static function get_stack_question_form_data_numerical_input() {
        $formform = new stdClass();
        $formform->stackversion = '2024032401';
        $formform->name = 'Numerical input (min sf)';
        $formform->questionvariables = '';
        $formform->questiontext = [
            'text' => '<p></p><p>Type in \(\pi\) to at least \(3\) significant
                    figures</p><p>[[input:ans1]] [[validation:ans1]]</p>',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->questiondescription = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->specificfeedback = [
            'text' => '[[feedback:prt1]]',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->generalfeedback = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->questionnote = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->penalty = 0.1;
        $formform->variantsselectionseed = '';
        $formform->defaultmark = '1';
        $formform->ans1type = 'numerical';
        $formform->ans1modelans = 'significantfigures(pi,5)';
        $formform->ans1boxsize = '15';
        $formform->ans1strictsyntax = '';
        $formform->ans1insertstars = '0';
        $formform->ans1syntaxhint = '';
        $formform->ans1syntaxattribute = '0';
        $formform->ans1forbidwords = '';
        $formform->ans1allowwords = '';
        $formform->ans1forbidfloat = '0';
        $formform->ans1requirelowestterms = '0';
        $formform->ans1checkanswertype = '0';
        $formform->ans1mustverify = '1';
        $formform->ans1showvalidation = '2';
        $formform->ans1options = 'minsf:3';
        $formform->questionsimplify = '1';
        $formform->assumepositive = '';
        $formform->assumereal = '0';
        $formform->prtcorrect = [
            'text' => '<span style="font-size: 1.5em; color:green;"><i class="fa fa-check"></i></span> Correct answer, well done.',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prtpartiallycorrect = [
            'text' => '<span style="font-size: 1.5em; color:orange;"><i class="fa fa-adjust"></i></span>
                Your answer is partially correct.',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prtincorrect = [
            'text' => '<span style="font-size: 1.5em; color:red;"><i class="fa fa-times"></i></span> Incorrect answer.',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->decimals = '.';
        $formform->scientificnotation = '*10';
        $formform->multiplicationsign = 'dot';
        $formform->sqrtsign = '1';
        $formform->complexno = 'i';
        $formform->inversetrig = 'cos-1';
        $formform->logicsymbol = 'lang';
        $formform->matrixparens = '[';
        $formform->qtype = 'stack';
        $formform->numhints = 2;
        $formform->hint = [
            [
                'text' => '',
                'format' => '1',
            ],
            [
                'text' => '',
                'format' => '1',
            ],
        ];
        $formform->prt1value = 1;
        $formform->prt1feedbackstyle = '1';
        $formform->prt1feedbackvariables = '';
        $formform->prt1autosimplify = '1';
        $formform->prt1description[0] = '';
        $formform->prt1sans[0] = 'ans1';
        $formform->prt1tans[0] = 'pi';
        $formform->prt1answertest[0] = 'NumAbsolute';
        $formform->prt1testoptions[0] = '0.01';
        $formform->prt1quiet[0] = '0';
        $formform->prt1falsescore[0] = '0';
        $formform->prt1falsescoremode[0] = '=';
        $formform->prt1falsepenalty[0] = '';
        $formform->prt1falsefeedback[0] = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prt1falseanswernote[0] = 'prt1-1-F';
        $formform->prt1falsenextnode[0] = '-1';
        $formform->prt1truescore[0] = '1';
        $formform->prt1truescoremode[0] = '=';
        $formform->prt1truepenalty[0] = '';
        $formform->prt1truefeedback[0] = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prt1trueanswernote[0] = 'prt1-1-T';
        $formform->prt1truenextnode[0] = '-1';
        return $formform;
    }

    /**
     * Make the data what would be received from the editing form for Radio question.
     *
     * @return stdClass the data that would be returned by $form->get_data();
     */
    public static function get_stack_question_form_data_radio_input() {
        $formform = new stdClass();
        $formform->stackversion = '2024032401';
        $formform->name = 'Radio';
        $formform->questionvariables = '/* Create a list of potential answers. */ p:sin(2*x);
                ta:[[diff(p,x),true],[p,false],[int(p,x),false],[cos(2*x)+c,false]];
                /* The actual correct answer. */ tac:diff(p,x) /* Add in a "None of these" to the end of the list.
                The Maxima value is the atom null. */ tao:[null, false, "None of these"]; ta:append(ta,[tao]);';
        $formform->questiontext = [
            'text' => '<p>Differentiate {@p@} with respect to \(x\).</p><p>[[input:ans1]]</p><div>[[validation:ans1]]</div>',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->questiondescription = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->specificfeedback = [
            'text' => '[[feedback:prt1]]',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->generalfeedback = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->questionnote = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->penalty = 0.1;
        $formform->variantsselectionseed = '';
        $formform->defaultmark = '1';
        $formform->ans1type = 'radio';
        $formform->ans1modelans = 'ta';
        $formform->ans1boxsize = '15';
        $formform->ans1strictsyntax = '';
        $formform->ans1insertstars = '0';
        $formform->ans1syntaxhint = '';
        $formform->ans1syntaxattribute = '0';
        $formform->ans1forbidwords = '';
        $formform->ans1allowwords = '';
        $formform->ans1forbidfloat = '1';
        $formform->ans1requirelowestterms = '0';
        $formform->ans1checkanswertype = '0';
        $formform->ans1mustverify = '1';
        $formform->ans1showvalidation = '1';
        $formform->ans1options = '';
        $formform->questionsimplify = '1';
        $formform->assumepositive = '';
        $formform->assumereal = '0';
        $formform->prtcorrect = [
            'text' => '<span style="font-size: 1.5em; color:green;"><i class="fa fa-check"></i></span> Correct answer, well done.',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prtpartiallycorrect = [
            'text' => '<span style="font-size: 1.5em; color:orange;"><i class="fa fa-adjust"></i></span>
                Your answer is partially correct.',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prtincorrect = [
            'text' => '<span style="font-size: 1.5em; color:red;"><i class="fa fa-times"></i></span> Incorrect answer.',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->decimals = '.';
        $formform->scientificnotation = '*10';
        $formform->multiplicationsign = 'dot';
        $formform->sqrtsign = '1';
        $formform->complexno = 'i';
        $formform->inversetrig = 'cos-1';
        $formform->logicsymbol = 'lang';
        $formform->matrixparens = '[';
        $formform->qtype = 'stack';
        $formform->numhints = 2;
        $formform->hint = [
            [
                'text' => '',
                'format' => '1',
            ],
            [
                'text' => '',
                'format' => '1',
            ],
        ];
        $formform->prt1value = 1;
        $formform->prt1feedbackstyle = '1';
        $formform->prt1feedbackvariables = '';
        $formform->prt1autosimplify = '1';
        $formform->prt1description[0] = '';
        $formform->prt1sans[0] = 'ans1';
        $formform->prt1tans[0] = 'diff(p,x)';
        $formform->prt1answertest[0] = 'Diff';
        $formform->prt1testoptions[0] = 'x';
        $formform->prt1quiet[0] = '0';
        $formform->prt1falsescore[0] = '0';
        $formform->prt1falsescoremode[0] = '=';
        $formform->prt1falsepenalty[0] = '';
        $formform->prt1falsefeedback[0] = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prt1falseanswernote[0] = 'prt1-1-F';
        $formform->prt1falsenextnode[0] = '-1';
        $formform->prt1truescore[0] = '1';
        $formform->prt1truescoremode[0] = '=';
        $formform->prt1truepenalty[0] = '';
        $formform->prt1truefeedback[0] = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prt1trueanswernote[0] = 'prt1-1-T';
        $formform->prt1truenextnode[0] = '-1';
        return $formform;
    }

    /**
     * Make the data what would be received from the editing form for Radio (compact) question.
     *
     * @return stdClass the data that would be returned by $form->get_data();
     */
    public static function get_stack_question_form_data_radio_input_compact() {
        $formform = new stdClass();
        $formform->stackversion = '2024032401';
        $formform->name = 'Radio (compact)';
        $formform->questionvariables = '/* Create a list of potential answers. */ p:sin(2*x);
                ta:[[diff(p,x),true],[p,false],[int(p,x),false],[cos(2*x)+c,false]];
                /* The actual correct answer. */ tac:diff(p,x) /* Add in a "None of these" to the end of the list.
                The Maxima value is the atom null. */ tao:[null, false, "None of these"]; ta:append(ta,[tao]);';
        $formform->questiontext = [
            'text' => '<p>Differentiate {@p@} with respect to \(x\).</p><p>[[input:ans1]]</p><div>[[validation:ans1]]</div>',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->questiondescription = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->specificfeedback = [
            'text' => '[[feedback:prt1]]',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->generalfeedback = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->questionnote = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->penalty = 0.1;
        $formform->variantsselectionseed = '';
        $formform->defaultmark = '1';
        $formform->ans1type = 'radio';
        $formform->ans1modelans = 'ta';
        $formform->ans1boxsize = '15';
        $formform->ans1strictsyntax = '';
        $formform->ans1insertstars = '0';
        $formform->ans1syntaxhint = '';
        $formform->ans1syntaxattribute = '0';
        $formform->ans1forbidwords = '';
        $formform->ans1allowwords = '';
        $formform->ans1forbidfloat = '1';
        $formform->ans1requirelowestterms = '0';
        $formform->ans1checkanswertype = '0';
        $formform->ans1mustverify = '1';
        $formform->ans1showvalidation = '3';
        $formform->ans1options = '';
        $formform->questionsimplify = '1';
        $formform->assumepositive = '';
        $formform->assumereal = '0';
        $formform->prtcorrect = [
            'text' => '<span style="font-size: 1.5em; color:green;"><i class="fa fa-check"></i></span> Correct answer, well done.',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prtpartiallycorrect = [
            'text' => '<span style="font-size: 1.5em; color:orange;"><i class="fa fa-adjust"></i></span>
                Your answer is partially correct.',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prtincorrect = [
            'text' => '<span style="font-size: 1.5em; color:red;"><i class="fa fa-times"></i></span> Incorrect answer.',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->decimals = '.';
        $formform->scientificnotation = '*10';
        $formform->multiplicationsign = 'dot';
        $formform->sqrtsign = '1';
        $formform->complexno = 'i';
        $formform->inversetrig = 'cos-1';
        $formform->logicsymbol = 'lang';
        $formform->matrixparens = '[';
        $formform->qtype = 'stack';
        $formform->numhints = 2;
        $formform->hint = [
            [
                'text' => '',
                'format' => '1',
            ],
            [
                'text' => '',
                'format' => '1',
            ],
        ];
        $formform->prt1value = 1;
        $formform->prt1feedbackstyle = '1';
        $formform->prt1feedbackvariables = '';
        $formform->prt1autosimplify = '1';
        $formform->prt1description[0] = '';
        $formform->prt1sans[0] = 'ans1';
        $formform->prt1tans[0] = 'diff(p,x)';
        $formform->prt1answertest[0] = 'Diff';
        $formform->prt1testoptions[0] = 'x';
        $formform->prt1quiet[0] = '0';
        $formform->prt1falsescore[0] = '0';
        $formform->prt1falsescoremode[0] = '=';
        $formform->prt1falsepenalty[0] = '';
        $formform->prt1falsefeedback[0] = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prt1falseanswernote[0] = 'prt1-1-F';
        $formform->prt1falsenextnode[0] = '-1';
        $formform->prt1truescore[0] = '1';
        $formform->prt1truescoremode[0] = '=';
        $formform->prt1truepenalty[0] = '';
        $formform->prt1truefeedback[0] = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prt1trueanswernote[0] = 'prt1-1-T';
        $formform->prt1truenextnode[0] = '-1';
        return $formform;
    }

    /**
     * Make the data what would be received from the editing form for Single char question.
     *
     * @return stdClass the data that would be returned by $form->get_data();
     */
    public static function get_stack_question_form_data_single_char_input() {
        $formform = new stdClass();
        $formform->stackversion = '2024032401';
        $formform->name = 'Single char';
        $formform->questionvariables = '';
        $formform->questiontext = [
            'text' => '<p>Type in \(x\)<br></p><p>[[input:ans1]]</p><div>[[validation:ans1]]</div>',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->questiondescription = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->specificfeedback = [
            'text' => '[[feedback:prt1]]',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->generalfeedback = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->questionnote = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->penalty = 0.1;
        $formform->variantsselectionseed = '';
        $formform->defaultmark = '1';
        $formform->ans1type = 'singlechar';
        $formform->ans1modelans = 'x';
        $formform->ans1boxsize = '15';
        $formform->ans1strictsyntax = '';
        $formform->ans1insertstars = '0';
        $formform->ans1syntaxhint = '';
        $formform->ans1syntaxattribute = '0';
        $formform->ans1forbidwords = '';
        $formform->ans1allowwords = '';
        $formform->ans1forbidfloat = '1';
        $formform->ans1requirelowestterms = '0';
        $formform->ans1checkanswertype = '0';
        $formform->ans1mustverify = '1';
        $formform->ans1showvalidation = '1';
        $formform->ans1options = '';
        $formform->questionsimplify = '1';
        $formform->assumepositive = '';
        $formform->assumereal = '0';
        $formform->prtcorrect = [
            'text' => '<span style="font-size: 1.5em; color:green;"><i class="fa fa-check"></i></span> Correct answer, well done.',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prtpartiallycorrect = [
            'text' => '<span style="font-size: 1.5em; color:orange;"><i class="fa fa-adjust"></i></span>
                Your answer is partially correct.',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prtincorrect = [
            'text' => '<span style="font-size: 1.5em; color:red;"><i class="fa fa-times"></i></span> Incorrect answer.',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->decimals = '.';
        $formform->scientificnotation = '*10';
        $formform->multiplicationsign = 'dot';
        $formform->sqrtsign = '1';
        $formform->complexno = 'i';
        $formform->inversetrig = 'cos-1';
        $formform->logicsymbol = 'lang';
        $formform->matrixparens = '[';
        $formform->qtype = 'stack';
        $formform->numhints = 2;
        $formform->hint = [
            [
                'text' => '',
                'format' => '1',
            ],
            [
                'text' => '',
                'format' => '1',
            ],
        ];
        $formform->prt1value = 1;
        $formform->prt1feedbackstyle = '1';
        $formform->prt1feedbackvariables = '';
        $formform->prt1autosimplify = '1';
        $formform->prt1description[0] = '';
        $formform->prt1sans[0] = 'ans1';
        $formform->prt1tans[0] = 'x';
        $formform->prt1answertest[0] = 'AlgEquiv';
        $formform->prt1testoptions[0] = '';
        $formform->prt1quiet[0] = '0';
        $formform->prt1falsescore[0] = '0';
        $formform->prt1falsescoremode[0] = '=';
        $formform->prt1falsepenalty[0] = '';
        $formform->prt1falsefeedback[0] = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prt1falseanswernote[0] = 'prt1-1-F';
        $formform->prt1falsenextnode[0] = '-1';
        $formform->prt1truescore[0] = '1';
        $formform->prt1truescoremode[0] = '=';
        $formform->prt1truepenalty[0] = '';
        $formform->prt1truefeedback[0] = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prt1trueanswernote[0] = 'prt1-1-T';
        $formform->prt1truenextnode[0] = '-1';
        return $formform;
    }

    /**
     * Make the data what would be received from the editing form for String test question.
     *
     * @return stdClass the data that would be returned by $form->get_data();
     */
    public static function get_stack_question_form_data_string_input() {
        $formform = new stdClass();
        $formform->stackversion = '2024032401';
        $formform->name = 'String input';
        $formform->questionvariables = '';
        $formform->questiontext = [
            'text' => 'This input is sent to the CAS: <p>[[input:ans1]] [[validation:ans1]]</p>
                This input is not, perhaps it is used to store JSXGraph state? or GeoGebra state?
                <p>[[input:ans2]] [[validation:ans2]]</p>',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->questiondescription = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->specificfeedback = [
            'text' => '[[feedback:prt1]]',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->generalfeedback = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->questionnote = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->penalty = 0.1;
        $formform->variantsselectionseed = '';
        $formform->defaultmark = '1';
        $formform->ans1type = 'string';
        $formform->ans1modelans = '"Hello world"';
        $formform->ans1boxsize = '25';
        $formform->ans1strictsyntax = '';
        $formform->ans1insertstars = '0';
        $formform->ans1syntaxhint = '';
        $formform->ans1syntaxattribute = '0';
        $formform->ans1forbidwords = '';
        $formform->ans1allowwords = '';
        $formform->ans1forbidfloat = '1';
        $formform->ans1requirelowestterms = '0';
        $formform->ans1checkanswertype = '0';
        $formform->ans1mustverify = '1';
        $formform->ans1showvalidation = '1';
        $formform->ans1options = '';
        $formform->ans2type = 'string';
        $formform->ans2modelans = '"Some JSON stuff"';
        $formform->ans2type = 'string';
        $formform->ans2modelans = '"Some JSON stuff"';
        $formform->ans2boxsize = '15';
        $formform->ans2strictsyntax = '';
        $formform->ans2insertstars = '0';
        $formform->ans2syntaxhint = '';
        $formform->ans2syntaxattribute = '0';
        $formform->ans2forbidwords = '';
        $formform->ans2allowwords = '';
        $formform->ans2forbidfloat = '1';
        $formform->ans2requirelowestterms = '0';
        $formform->ans2checkanswertype = '0';
        $formform->ans2mustverify = '0';
        $formform->ans2showvalidation = '0';
        $formform->ans2options = 'hideanswer';
        $formform->questionsimplify = '1';
        $formform->assumepositive = '';
        $formform->assumereal = '0';
        $formform->prtcorrect = [
            'text' => '<span style="font-size: 1.5em; color:green;"><i class="fa fa-check"></i></span> Correct answer, well done.',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prtpartiallycorrect = [
            'text' => '<span style="font-size: 1.5em; color:orange;"><i class="fa fa-adjust"></i></span>
                Your answer is partially correct.',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prtincorrect = [
            'text' => '<span style="font-size: 1.5em; color:red;"><i class="fa fa-times"></i></span> Incorrect answer.',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->decimals = '.';
        $formform->scientificnotation = '*10';
        $formform->multiplicationsign = 'dot';
        $formform->sqrtsign = '1';
        $formform->complexno = 'i';
        $formform->inversetrig = 'cos-1';
        $formform->logicsymbol = 'lang';
        $formform->matrixparens = '[';
        $formform->qtype = 'stack';
        $formform->numhints = 2;
        $formform->hint = [
            [
                'text' => '',
                'format' => '1',
            ],
            [
                'text' => '',
                'format' => '1',
            ],
        ];
        $formform->prt1value = 1;
        $formform->prt1feedbackstyle = '1';
        $formform->prt1feedbackvariables = '';
        $formform->prt1autosimplify = '1';
        $formform->prt1description[0] = '';
        $formform->prt1sans[0] = 'ans1';
        $formform->prt1tans[0] = '"Hello world"';
        $formform->prt1answertest[0] = 'StringSloppy';
        $formform->prt1testoptions[0] = '';
        $formform->prt1quiet[0] = '0';
        $formform->prt1falsescore[0] = '0';
        $formform->prt1falsescoremode[0] = '=';
        $formform->prt1falsepenalty[0] = '';
        $formform->prt1falsefeedback[0] = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prt1falseanswernote[0] = 'prt1-1-F';
        $formform->prt1falsenextnode[0] = '-1';
        $formform->prt1truescore[0] = '1';
        $formform->prt1truescoremode[0] = '=';
        $formform->prt1truepenalty[0] = '';
        $formform->prt1truefeedback[0] = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prt1trueanswernote[0] = 'prt1-1-T';
        $formform->prt1truenextnode[0] = '-1';
        return $formform;
    }

    /**
     * Make the data what would be received from the editing form for Textarea test question.
     *
     * @return stdClass the data that would be returned by $form->get_data();
     */
    public static function get_stack_question_form_data_textarea_input() {
        $formform = new stdClass();
        $formform->stackversion = '2024032401';
        $formform->name = 'Textarea test';
        $formform->questionvariables = 'ta:[x=1#pm#a,x=2 nounor x=-2];';
        $formform->questiontext = [
            'text' => 'Dummy maths input:&nbsp; \({@ta@}\).<br>[[input:ans1]] [[validation:ans1]]',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->questiondescription = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->specificfeedback = [
            'text' => '[[feedback:prt1]]',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->generalfeedback = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->questionnote = [
            'text' => 'vendor/bin/phpunit --group qtype_stack',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->penalty = 0.1;
        $formform->variantsselectionseed = '';
        $formform->defaultmark = '1';
        $formform->ans1type = 'textarea';
        $formform->ans1modelans = 'ta';
        $formform->ans1boxsize = '15';
        $formform->ans1strictsyntax = '';
        $formform->ans1insertstars = '1';
        $formform->ans1syntaxhint = '';
        $formform->ans1syntaxattribute = '0';
        $formform->ans1forbidwords = '';
        $formform->ans1allowwords = '';
        $formform->ans1forbidfloat = '1';
        $formform->ans1requirelowestterms = '0';
        $formform->ans1checkanswertype = '0';
        $formform->ans1mustverify = '1';
        $formform->ans1showvalidation = '1';
        $formform->ans1options = '';
        $formform->questionsimplify = '1';
        $formform->assumepositive = '';
        $formform->assumereal = '0';
        $formform->prtcorrect = [
            'text' => '<span style="font-size: 1.5em; color:green;"><i class="fa fa-check"></i></span> Correct answer, well done.',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prtpartiallycorrect = [
            'text' => '<span style="font-size: 1.5em; color:orange;"><i class="fa fa-adjust"></i></span>
                Your answer is partially correct.',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prtincorrect = [
            'text' => '<span style="font-size: 1.5em; color:red;"><i class="fa fa-times"></i></span> Incorrect answer.',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->decimals = '.';
        $formform->scientificnotation = '*10';
        $formform->multiplicationsign = 'dot';
        $formform->sqrtsign = '1';
        $formform->complexno = 'i';
        $formform->inversetrig = 'cos-1';
        $formform->logicsymbol = 'lang';
        $formform->matrixparens = '[';
        $formform->qtype = 'stack';
        $formform->numhints = 2;
        $formform->hint = [
            [
                'text' => '',
                'format' => '1',
            ],
            [
                'text' => '',
                'format' => '1',
            ],
        ];
        $formform->prt1value = 1;
        $formform->prt1feedbackstyle = '1';
        $formform->prt1feedbackvariables = '';
        $formform->prt1autosimplify = '1';
        $formform->prt1description[0] = '';
        $formform->prt1sans[0] = 'ans1';
        $formform->prt1tans[0] = 'ta';
        $formform->prt1answertest[0] = 'AlgEquiv';
        $formform->prt1testoptions[0] = '';
        $formform->prt1quiet[0] = '0';
        $formform->prt1falsescore[0] = '0';
        $formform->prt1falsescoremode[0] = '=';
        $formform->prt1falsepenalty[0] = '';
        $formform->prt1falsefeedback[0] = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prt1falseanswernote[0] = 'prt1-1-F';
        $formform->prt1falsenextnode[0] = '-1';
        $formform->prt1truescore[0] = '1';
        $formform->prt1truescoremode[0] = '=';
        $formform->prt1truepenalty[0] = '';
        $formform->prt1truefeedback[0] = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prt1trueanswernote[0] = 'prt1-1-T';
        $formform->prt1truenextnode[0] = '-1';
        return $formform;
    }

    /**
     * Make the data what would be received from the editing form for Textarea test (compact) question.
     *
     * @return stdClass the data that would be returned by $form->get_data();
     */
    public static function get_stack_question_form_data_textarea_input_compact() {
        $formform = new stdClass();
        $formform->stackversion = '2024032401';
        $formform->name = 'Textarea test (compact)';
        $formform->questionvariables = 'ta:[x=1,x=2]';
        $formform->questiontext = [
            'text' => 'Dummy maths input:&nbsp; \({@ta@}\).<p>[[input:ans1]] [[validation:ans1]]</p>',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->questiondescription = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->specificfeedback = [
            'text' => '[[feedback:prt1]]',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->generalfeedback = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->questionnote = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->penalty = 0.1;
        $formform->variantsselectionseed = '';
        $formform->defaultmark = '1';
        $formform->ans1type = 'textarea';
        $formform->ans1modelans = 'ta';
        $formform->ans1boxsize = '15';
        $formform->ans1strictsyntax = '';
        $formform->ans1insertstars = '1';
        $formform->ans1syntaxhint = '';
        $formform->ans1syntaxattribute = '0';
        $formform->ans1forbidwords = '';
        $formform->ans1allowwords = '';
        $formform->ans1forbidfloat = '1';
        $formform->ans1requirelowestterms = '0';
        $formform->ans1checkanswertype = '0';
        $formform->ans1mustverify = '1';
        $formform->ans1showvalidation = '3';
        $formform->ans1options = 'simp';
        $formform->questionsimplify = '1';
        $formform->assumepositive = '';
        $formform->assumereal = '0';
        $formform->prtcorrect = [
            'text' => '<span style="font-size: 1.5em; color:green;"><i class="fa fa-check"></i></span> Correct answer, well done.',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prtpartiallycorrect = [
            'text' => '<span style="font-size: 1.5em; color:orange;"><i class="fa fa-adjust"></i></span>
                Your answer is partially correct.',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prtincorrect = [
            'text' => '<span style="font-size: 1.5em; color:red;"><i class="fa fa-times"></i></span> Incorrect answer.',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->decimals = '.';
        $formform->scientificnotation = '*10';
        $formform->multiplicationsign = 'dot';
        $formform->sqrtsign = '1';
        $formform->complexno = 'i';
        $formform->inversetrig = 'cos-1';
        $formform->logicsymbol = 'lang';
        $formform->matrixparens = '[';
        $formform->qtype = 'stack';
        $formform->numhints = 2;
        $formform->hint = [
            [
                'text' => '',
                'format' => '1',
            ],
            [
                'text' => '',
                'format' => '1',
            ],
        ];
        $formform->prt1value = 1;
        $formform->prt1feedbackstyle = '1';
        $formform->prt1feedbackvariables = '';
        $formform->prt1autosimplify = '1';
        $formform->prt1description[0] = '';
        $formform->prt1sans[0] = 'ans1';
        $formform->prt1tans[0] = 'ta';
        $formform->prt1answertest[0] = 'AlgEquiv';
        $formform->prt1testoptions[0] = '';
        $formform->prt1quiet[0] = '0';
        $formform->prt1falsescore[0] = '0';
        $formform->prt1falsescoremode[0] = '=';
        $formform->prt1falsepenalty[0] = '';
        $formform->prt1falsefeedback[0] = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prt1falseanswernote[0] = 'prt1-1-F';
        $formform->prt1falsenextnode[0] = '-1';
        $formform->prt1truescore[0] = '1';
        $formform->prt1truescoremode[0] = '=';
        $formform->prt1truepenalty[0] = '';
        $formform->prt1truefeedback[0] = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prt1trueanswernote[0] = 'prt1-1-T';
        $formform->prt1truenextnode[0] = '-1';
        return $formform;
    }

    /**
     * Make the data what would be received from the editing form for True/false question.
     *
     * @return stdClass the data that would be returned by $form->get_data();
     */
    public static function get_stack_question_form_data_true_false_input() {
        $formform = new stdClass();
        $formform->stackversion = '2024032401';
        $formform->name = 'True/false';
        $formform->questionvariables = '';
        $formform->questiontext = [
            'text' => '<p>All generalizations are false: [[input:ans1]] [[validation:ans1]]</p><p><br></p>',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->questiondescription = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->specificfeedback = [
            'text' => '[[feedback:prt1]]',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->generalfeedback = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->questionnote = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->penalty = 0.1;
        $formform->variantsselectionseed = '';
        $formform->defaultmark = '1';
        $formform->ans1type = 'boolean';
        $formform->ans1modelans = 'false';
        $formform->ans1boxsize = '15';
        $formform->ans1strictsyntax = '';
        $formform->ans1insertstars = '0';
        $formform->ans1syntaxhint = '';
        $formform->ans1syntaxattribute = '0';
        $formform->ans1forbidwords = '';
        $formform->ans1allowwords = '';
        $formform->ans1forbidfloat = '1';
        $formform->ans1requirelowestterms = '0';
        $formform->ans1checkanswertype = '0';
        $formform->ans1mustverify = '1';
        $formform->ans1showvalidation = '1';
        $formform->ans1options = '';
        $formform->questionsimplify = '1';
        $formform->assumepositive = '';
        $formform->assumereal = '0';
        $formform->prtcorrect = [
            'text' => '<span style="font-size: 1.5em; color:green;"><i class="fa fa-check"></i></span> Correct answer, well done.',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prtpartiallycorrect = [
            'text' => '<span style="font-size: 1.5em; color:orange;"><i class="fa fa-adjust"></i></span>
                Your answer is partially correct.',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prtincorrect = [
            'text' => '<span style="font-size: 1.5em; color:red;"><i class="fa fa-times"></i></span> Incorrect answer.',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->decimals = '.';
        $formform->scientificnotation = '*10';
        $formform->multiplicationsign = 'dot';
        $formform->sqrtsign = '1';
        $formform->complexno = 'i';
        $formform->inversetrig = 'cos-1';
        $formform->logicsymbol = 'lang';
        $formform->matrixparens = '[';
        $formform->qtype = 'stack';
        $formform->numhints = 2;
        $formform->hint = [
            [
                'text' => '',
                'format' => '1',
            ],
            [
                'text' => '',
                'format' => '1',
            ],
        ];
        $formform->prt1value = 1;
        $formform->prt1feedbackstyle = '1';
        $formform->prt1feedbackvariables = '';
        $formform->prt1autosimplify = '1';
        $formform->prt1description[0] = '';
        $formform->prt1sans[0] = 'ans1';
        $formform->prt1tans[0] = 'false';
        $formform->prt1answertest[0] = 'AlgEquiv';
        $formform->prt1testoptions[0] = '';
        $formform->prt1quiet[0] = '0';
        $formform->prt1falsescore[0] = '0.5';
        $formform->prt1falsescoremode[0] = '=';
        $formform->prt1falsepenalty[0] = '';
        $formform->prt1falsefeedback[0] = [
            'text' => '<p>Who knows!<br></p>',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prt1falseanswernote[0] = 'prt1-1-F';
        $formform->prt1falsenextnode[0] = '-1';
        $formform->prt1truescore[0] = '0.5';
        $formform->prt1truescoremode[0] = '=';
        $formform->prt1truepenalty[0] = '';
        $formform->prt1truefeedback[0] = [
            'text' => '<p>Who knows!</p>',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prt1trueanswernote[0] = 'prt1-1-T';
        $formform->prt1truenextnode[0] = '-1';
        return $formform;
    }

    /**
     * Make the data what would be received from the editing form for Units question.
     *
     * @return stdClass the data that would be returned by $form->get_data();
     */
    public static function get_stack_question_form_data_units_input() {
        $formform = new stdClass();
        $formform->stackversion = '2024032401';
        $formform->name = 'Units';
        $formform->questionvariables = 'ta:9.81*m*s^-2';
        $formform->questiontext = [
            'text' => '<p>What is the force of gravity?</p><p>[[input:ans1]]</p><div>[[validation:ans1]]</div>',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->questiondescription = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->specificfeedback = [
            'text' => '[[feedback:prt1]]',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->generalfeedback = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->questionnote = [
            'text' => 'This question just calls for factual recall, but with scientific units attached!',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->penalty = 0.1;
        $formform->variantsselectionseed = '';
        $formform->defaultmark = '1';
        $formform->ans1type = 'units';
        $formform->ans1modelans = 'ta';
        $formform->ans1boxsize = '15';
        $formform->ans1strictsyntax = '';
        $formform->ans1insertstars = '0';
        $formform->ans1syntaxhint = '';
        $formform->ans1syntaxattribute = '0';
        $formform->ans1forbidwords = '';
        $formform->ans1allowwords = '';
        $formform->ans1forbidfloat = '1';
        $formform->ans1requirelowestterms = '0';
        $formform->ans1checkanswertype = '0';
        $formform->ans1mustverify = '1';
        $formform->ans1showvalidation = '1';
        $formform->ans1options = 'mindp:2';
        $formform->questionsimplify = '1';
        $formform->assumepositive = '';
        $formform->assumereal = '0';
        $formform->prtcorrect = [
            'text' => '<span style="font-size: 1.5em; color:green;"><i class="fa fa-check"></i></span> Correct answer, well done.',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prtpartiallycorrect = [
            'text' => '<span style="font-size: 1.5em; color:orange;"><i class="fa fa-adjust"></i></span>
                Your answer is partially correct.',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prtincorrect = [
            'text' => '<span style="font-size: 1.5em; color:red;"><i class="fa fa-times"></i></span> Incorrect answer.',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->decimals = '.';
        $formform->scientificnotation = '*10';
        $formform->multiplicationsign = 'dot';
        $formform->sqrtsign = '1';
        $formform->complexno = 'i';
        $formform->inversetrig = 'cos-1';
        $formform->logicsymbol = 'lang';
        $formform->matrixparens = '[';
        $formform->qtype = 'stack';
        $formform->numhints = 2;
        $formform->hint = [
            [
                'text' => '',
                'format' => '1',
            ],
            [
                'text' => '',
                'format' => '1',
            ],
        ];
        $formform->prt1value = 1;
        $formform->prt1feedbackstyle = '1';
        $formform->prt1feedbackvariables = '';
        $formform->prt1autosimplify = '1';
        $formform->prt1description[0] = '';
        $formform->prt1sans[0] = 'ans1';
        $formform->prt1tans[0] = 'ta';
        $formform->prt1answertest[0] = 'Units';
        $formform->prt1testoptions[0] = '3';
        $formform->prt1quiet[0] = '0';
        $formform->prt1falsescore[0] = '0';
        $formform->prt1falsescoremode[0] = '=';
        $formform->prt1falsepenalty[0] = '';
        $formform->prt1falsefeedback[0] = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prt1falseanswernote[0] = 'prt1-1-F';
        $formform->prt1falsenextnode[0] = '-1';
        $formform->prt1truescore[0] = '1';
        $formform->prt1truescoremode[0] = '=';
        $formform->prt1truepenalty[0] = '';
        $formform->prt1truefeedback[0] = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prt1trueanswernote[0] = 'prt1-1-T';
        $formform->prt1truenextnode[0] = '-1';
        return $formform;
    }

    /**
     * Make the data what would be received from the editing form for JSX graph question.
     *
     * @return stdClass the data that would be returned by $form->get_data();
     */
    public static function get_stack_question_form_data_jsx_graph_input() {
        $formform = new stdClass();
        $formform->stackversion = '2024032401';
        $formform->name = 'JSX behat test';
        $formform->questionvariables = '';
        $formform->questiontext = [
            'text' => "<table> <tbody><tr> <td>Element1 location: [[input:ans1]] [[validation:ans1]]<br>
                Element 2 location: [[input:ans2]] [[validation:ans2]]<br> Element1 id: [[input:element1]]<br>
                [[validation:element1]]<br> Element2 id: [[input:element2]]<br>[[validation:element2]]<br></td></tr><tr>
                <td> [[jsxgraph width='400px' height='400px' input-ref-ans1='ans1Ref' input-ref-ans2='ans2Ref'
                input-ref-element1='element1Ref' input-ref-element2='element2Ref']]
                var board = JXG.JSXGraph.initBoard(divid, {boundingbox: [-4.5, 4.5, 4.5, -4.5], showNavigation:false, grid:true});
                var p2 = board.create('point', [-2, -2], {size: 8}); var p = board.create('point', [4, 3]);
                var element1Ref = document.getElementById(element1Ref); element1Ref.value = p.id;
                element1Ref.dispatchEvent(new Event('change')); stack_jxg.bind_point(ans1Ref, p);
                var element2Ref = document.getElementById(element2Ref); element2Ref.value = p2.id;
                element2Ref.dispatchEvent(new Event('change')); stack_jxg.bind_point(ans2Ref, p2);
                board.update(); [[/jsxgraph]] </td> </tr> </tbody></table>",
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->questiondescription = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->specificfeedback = [
            'text' => '[[feedback:prt1]]',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->generalfeedback = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->questionnote = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->penalty = 0.1;
        $formform->variantsselectionseed = '';
        $formform->defaultmark = '1';
        $formform->ans1type = 'algebraic';
        $formform->ans1modelans = '[0,0]';
        $formform->ans1boxsize = '15';
        $formform->ans1strictsyntax = '';
        $formform->ans1insertstars = '0';
        $formform->ans1syntaxhint = '';
        $formform->ans1syntaxattribute = '0';
        $formform->ans1forbidwords = '';
        $formform->ans1allowwords = '';
        $formform->ans1forbidfloat = '0';
        $formform->ans1requirelowestterms = '0';
        $formform->ans1checkanswertype = '0';
        $formform->ans1mustverify = '0';
        $formform->ans1showvalidation = '0';
        $formform->ans1options = '';
        $formform->ans2type = 'algebraic';
        $formform->ans2modelans = '[0,0]';
        $formform->ans2type = 'algebraic';
        $formform->ans2modelans = '[0,0]';
        $formform->ans2boxsize = '15';
        $formform->ans2strictsyntax = '';
        $formform->ans2insertstars = '0';
        $formform->ans2syntaxhint = '';
        $formform->ans2syntaxattribute = '0';
        $formform->ans2forbidwords = '';
        $formform->ans2allowwords = '';
        $formform->ans2forbidfloat = '0';
        $formform->ans2requirelowestterms = '0';
        $formform->ans2checkanswertype = '0';
        $formform->ans2mustverify = '0';
        $formform->ans2showvalidation = '0';
        $formform->ans2options = '';
        $formform->element1type = 'string';
        $formform->element1modelans = '""';
        $formform->element1type = 'string';
        $formform->element1modelans = '""';
        $formform->element1boxsize = '10';
        $formform->element1strictsyntax = '';
        $formform->element1insertstars = '0';
        $formform->element1syntaxhint = '';
        $formform->element1syntaxattribute = '0';
        $formform->element1forbidwords = '';
        $formform->element1allowwords = '';
        $formform->element1forbidfloat = '0';
        $formform->element1requirelowestterms = '0';
        $formform->element1checkanswertype = '0';
        $formform->element1mustverify = '0';
        $formform->element1showvalidation = '0';
        $formform->element1options = 'hideanswer';
        $formform->element2type = 'string';
        $formform->element2modelans = '""';
        $formform->element2type = 'string';
        $formform->element2modelans = '""';
        $formform->element2boxsize = '10';
        $formform->element2strictsyntax = '';
        $formform->element2insertstars = '0';
        $formform->element2syntaxhint = '';
        $formform->element2syntaxattribute = '0';
        $formform->element2forbidwords = '';
        $formform->element2allowwords = '';
        $formform->element2forbidfloat = '0';
        $formform->element2requirelowestterms = '0';
        $formform->element2checkanswertype = '0';
        $formform->element2mustverify = '0';
        $formform->element2showvalidation = '0';
        $formform->element2options = 'hideanswer';
        $formform->questionsimplify = '1';
        $formform->assumepositive = '';
        $formform->assumereal = '0';
        $formform->prtcorrect = [
            'text' => '<span style="font-size: 1.5em; color:green;"><i class="fa fa-check"></i></span> Correct answer, well done.',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prtpartiallycorrect = [
            'text' => '<span style="font-size: 1.5em; color:orange;"><i class="fa fa-adjust"></i></span>
                Your answer is partially correct.',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prtincorrect = [
            'text' => '<span style="font-size: 1.5em; color:red;"><i class="fa fa-times"></i></span> Incorrect answer.',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->decimals = '.';
        $formform->scientificnotation = '*10';
        $formform->multiplicationsign = 'dot';
        $formform->sqrtsign = '1';
        $formform->complexno = 'i';
        $formform->inversetrig = 'cos-1';
        $formform->logicsymbol = 'lang';
        $formform->matrixparens = '[';
        $formform->qtype = 'stack';
        $formform->numhints = 2;
        $formform->hint = [
            [
                'text' => '',
                'format' => '1',
            ],
            [
                'text' => '',
                'format' => '1',
            ],
        ];
        $formform->prt1value = 1;
        $formform->prt1feedbackstyle = '1';
        $formform->prt1feedbackvariables = '';
        $formform->prt1autosimplify = '1';
        $formform->prt1description[0] = '';
        $formform->prt1sans[0] = 'ans1';
        $formform->prt1tans[0] = '[0,0]';
        $formform->prt1answertest[0] = 'NumAbsolute';
        $formform->prt1testoptions[0] = '0.05';
        $formform->prt1quiet[0] = '0';
        $formform->prt1falsescore[0] = '0';
        $formform->prt1falsescoremode[0] = '=';
        $formform->prt1falsepenalty[0] = '';
        $formform->prt1falsefeedback[0] = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prt1falseanswernote[0] = 'prt1-1-F';
        $formform->prt1falsenextnode[0] = '-1';
        $formform->prt1truescore[0] = '0';
        $formform->prt1truescoremode[0] = '=';
        $formform->prt1truepenalty[0] = '';
        $formform->prt1truefeedback[0] = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prt1trueanswernote[0] = 'prt1-1-T';
        $formform->prt1truenextnode[0] = '1';
        $formform->prt1description[1] = '';
        $formform->prt1sans[1] = 'ans2';
        $formform->prt1tans[1] = '[0,0]';
        $formform->prt1answertest[1] = 'NumAbsolute';
        $formform->prt1testoptions[1] = '0.05';
        $formform->prt1quiet[1] = '0';
        $formform->prt1falsescore[1] = '0';
        $formform->prt1falsescoremode[1] = '-';
        $formform->prt1falsepenalty[1] = '';
        $formform->prt1falsefeedback[1] = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prt1falseanswernote[1] = 'prt1-2-F';
        $formform->prt1falsenextnode[1] = '-1';
        $formform->prt1truescore[1] = '1';
        $formform->prt1truescoremode[1] = '=';
        $formform->prt1truepenalty[1] = '';
        $formform->prt1truefeedback[1] = [
            'text' => '',
            'format' => '1',
            'itemid' => 0,
        ];
        $formform->prt1trueanswernote[1] = 'prt1-2-T';
        $formform->prt1truenextnode[1] = '-1';
        return $formform;
    }
}

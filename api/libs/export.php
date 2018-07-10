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

require_once('input_values.php');
require_once(__DIR__ . '/../../lang/multilang.php');

class qtype_stack_api_export {

    private $defaults;
    private $question;

    public function __construct(string $questionxml, $defaults) {
        $qob = new SimpleXMLElement($questionxml);
        $question = ($qob->question) ? $qob->question : $qob;
        $this->defaults = $defaults;
        $this->question = $question;
    }

    /**
     * Set yaml property
     * @param array $yaml the resulting yaml array.
     * @param string $propertyname the property name in yaml
     * @param mixed $value value to store in yaml
     * @param string $type property type
     * @param string @section property section
     * @return string HTML ready to output.
     */
    private function property(&$yaml, $propertyname, $value, $type, $section) {
        $value = self::processvalue($value, $type);
        $value = qtype_stack_api_input_values::get_yaml_value($propertyname, $value);
        if (!$this->defaults->isdefault($section, $propertyname, $value)) {
            // For all string values, we try to tanslate them.
            if ($type == 'string') {
                $multilang = new stack_multilang();
                $languages = $multilang->languages_used($value);
                if ($languages == array()) {
                    $yaml[$propertyname] = $value;
                } else {
                    foreach($languages as $lang) {
                        $yaml[$propertyname.'_'.$lang] = $multilang->filter($value, $lang);
                    }
                }
            } else {
                $yaml[$propertyname] = $value;
            }
        }
    }

    /**
     * Type coercion
     * @param $value
     * @param string $type
     * @return bool|float|int|string
     */
    private static function processvalue($value, string $type) {
        switch($type) {
            case 'string':
                return (string) $value;
            case 'int':
                return (int) $value;
            case 'float':
                return (float) $value;
            case 'bool':
                return (bool) ($value == '1');
        }
    }

    /**
     * Exports question as yaml encoded string.
     * @return string
     */
    public function yaml() {

        $yaml = array();
        // General properties.
        $q = $this->question;
        $section = 'main';
        self::property($yaml, 'name', $q->name->text, 'string', $section);
        self::property($yaml, 'default_mark', $q->defaultgrade, 'float', $section);
        self::property($yaml, 'question_html', $q->questiontext->text, 'string', $section);
        self::property($yaml, 'penalty', $q->penalty, 'float', $section);
        if (trim($q->questionvariables->text) != '') {
            self::property($yaml, 'variables', $this->trimcasstrings($q->questionvariables->text),
                'string', $section);
        }
        if (trim($q->specificfeedback->text) != '') {
            self::property($yaml, 'specific_feedback_html', $q->specificfeedback->text, 'string', $section);
        }
        if (trim($q->questionnote->text) != '') {
            self::property($yaml, 'note', $q->questionnote->text, 'string', $section);
        }
        if (trim($q->generalfeedback->text) != '') {
            self::property($yaml, 'worked_solution_html', $q->generalfeedback->text, 'string', $section);
        }
        /* Note, we do not export the following because there are a mess with html tags...
        self::property($yaml, 'prt_correct_html', $q->prtcorrect->text, 'string', $section);
        self::property($yaml, 'prt_partially_correct_html', $q->prtpartiallycorrect->text, 'string', $section);
        self::property($yaml, 'prt_incorrect_html', $q->prtincorrect->text, 'string', $section);
        */

        $section = 'options';
        $yaml['options'] = array();

        // TODO: should these mappings be in qtype_stack_api_input_values?
        $options = array(
            'sqrtsign' => 'sqrt_sign',
            'assumepositive' => 'assume_positive',
            'assumereal' => 'assume_real',
            'questionsimplify' => 'simplify'
        );
        foreach ($options as $key => $value) {
            self::property($yaml['options'], $value, $q->$key, 'bool', $section);
        }

        $options = array(
            'multiplicationsign' => 'multiplication_sign',
            'complexno' => 'complex_no',
            'inversetrig' => 'inverse_trig',
            'matrixparens' => 'matrix_parens'
            );
        foreach ($options as $key => $value) {
            self::property($yaml['options'], $value, $q->$key, 'string', $section);
        }
        // Everything is default?
        if ($yaml['options'] === array()) {
            unset($yaml['options']);
        }

        // Process inputs.
        $this->processinputs($yaml);
        // Process trees.
        $this->processresponsetrees($yaml);
        // Process question tests.
        $this->processresponsetests($yaml);

        // Add in the deployed seeds.
        foreach ($q->deployedseed as $seed) {
            $yaml['deployedseed'][] = self::processvalue((string) $seed, 'int');
        }
        return yaml_emit($yaml, YAML_UTF8_ENCODING);
    }

    /**
     * Process question input and returns it as an array.
     * @param SimpleXMLElement $input question input
     * @return array
     */
    private function getinput($input) {
        $section = 'input';
        $res = array();
        $this->property($res, 'type', $input->type, 'string', $section);
        $this->property($res, 'model_answer', $input->tans, 'string', $section);
        $this->property($res, 'box_size', $input->boxsize, 'int', $section);
        $this->property($res, 'insert_stars', $input->insertstars, 'int', $section);
        $this->property($res, 'strict_syntax', $input->strictsyntax, 'bool', $section);
        $this->property($res, 'syntax_hint', $input->syntaxhint, 'string', $section);
        $this->property($res, 'syntax_attribute', $input->syntaxattribute, 'string', $section);
        $this->property($res, 'forbid_words', $input->forbidwords, 'string', $section);
        $this->property($res, 'allow_words', $input->allowwords, 'string', $section);
        $this->property($res, 'forbid_float', $input->forbidfloat, 'bool', $section);
        $this->property($res, 'require_lowest_terms', $input->requirelowestterms, 'bool', $section);
        $this->property($res, 'check_answer_type', $input->checkanswertype, 'bool', $section);
        $this->property($res, 'must_verify', $input->mustverify, 'bool', $section);
        $this->property($res, 'show_validations', $input->showvalidation, 'string', $section);
        $this->property($res, 'options', $input->options, 'string', $section);
        return $res;
    }

    /**
     * Process all question inputs and store it in yaml array.
     * @param array $yaml
     */
    private function processinputs(array &$yaml) {
        $yaml['inputs'] = array();
        foreach ($this->question->input as $value) {
            $yaml['inputs'][(string)$value->name] = self::getinput($value);
        }
    }

    /**
     * Process question tree node and returns it as array.
     * @param SimpleXMLElement $node question tree node
     * @return array
     */
    private function getresponsetreenode( $node) {
        $section = 'node';
        $res = array();
        $this->property($res, 'answer_test', $node->answertest, 'string', $section);
        $this->property($res, 'quiet', $node->quiet, 'bool', $section);
        $this->property($res, 'answer', $node->sans, 'string', $section);
        $this->property($res, 'model_answer', $node->tans, 'string', $section);
        $this->property($res, 'test_options', $node->testoptions, 'string', $section);

        // True branch.
        $section = 'branch-T';
        $res['T'] = array();
        $this->property($res['T'], 'score_mode', $node->truescoremode, 'string', $section);
        $this->property($res['T'], 'score', $node->truescore, 'float', $section);
        $this->property($res['T'], 'penalty', $node->truepenalty, 'float', $section);
        $nextnode = ($node->truenextnode == -1) ? -1 : 'node_' . (string)$node->truenextnode;
        $this->property($res['T'], 'next_node', $nextnode, 'string', $section);
        $this->property($res['T'], 'answer_note', $node->trueanswernote, 'string', $section);
        if (trim($node->truefeedback->text) != '') {
            $this->property($res['T'], 'feedback_html', $node->truefeedback->text, 'string', $section);
        }

        // False branch.
        $section = 'branch-F';
        $res['F'] = array();
        $this->property($res['F'], 'score_mode', $node->falsescoremode, 'string', $section);
        $this->property($res['F'], 'score', $node->falsescore, 'float', $section);
        $this->property($res['F'], 'penalty', $node->falsepenalty, 'float', $section);
        $nextnode = ($node->falsenextnode == -1) ? -1 : 'node_' . (string)$node->falsenextnode;
        $this->property($res['F'], 'next_node', $nextnode, 'string', $section);
        $this->property($res['F'], 'answer_note', $node->falseanswernote, 'string', $section);
        if (trim($node->falsefeedback->text) != '') {
            $this->property($res['F'], 'feedback_html', $node->falsefeedback->text, 'string', $section);
        }

        return $res;
    }

    /**
     * Process question response tree and returns as array.
     * @param SimpleXMLElement $tree question tree
     * @return array
     */
    private function getresponsetree($tree) {
        $section = 'tree';
        $res = array();
        $this->property($res, 'auto_simplify', $tree->autosimplify, 'bool', $section);
        $this->property($res, 'value', $tree->value, 'float', $section);
        $this->property($res, 'first_node', 'node_' . (int) $tree->firstnodename, 'string', $section);
        if (trim($tree->feedbackvariables->text) != '') {
            $this->property($res, 'feedback_variables',
              $this->trimcasstrings((string) $tree->feedbackvariables->text), 'string', $section);
        }

        $res['nodes'] = array();
        foreach ($tree->node as $node) {
            $res['nodes']["node_" . (string) $node->name] = $this->getresponsetreenode($node);
        }
        return $res;
    }

    /**
     * Process all response trees and store it in yaml array.
     * @param array $yaml
     */
    private function processresponsetrees(array &$yaml) {
        $yaml['response_trees'] = array();
        foreach ($this->question->prt as $tree) {
            $yaml['response_trees'][(string) $tree->name] = $this->getresponsetree($tree);
        }
    }

    /**
     * Process all question tests and store them in yaml array.
     * @param array $yaml
     */
    private function processresponsetests(array &$yaml) {
        foreach ($this->question->qtest as $test) {
            $res = array();
            foreach ($test->testinput as $input) {
                $this->property($res, (string) $input->name, (string) $input->value, 'string', 'input');
            }
            foreach ($test->expected as $prt) {
                $expect['score'] = self::processvalue((string) $prt->expectedscore, 'float');
                $expect['penalty'] = self::processvalue((string) $prt->expectedpenalty, 'float');
                $expect['answer_note'] = self::processvalue((string) $prt->expectedanswernote, 'string');
                $res[(string) $prt->name] = $expect;
            }
            $yaml['tests'][(string) $test->testcase] = $res;
        }

    }

    /**
     * Remove extraneous whitespace round CAS variables.
     * @param array $vars
     */
    private function trimcasstrings($vars) {
        $vars = explode("\n", $vars);
        foreach ($vars as $key => $value) {
            $vars[$key] = trim($value);
        }
        return implode($vars, "\n");
    }
}

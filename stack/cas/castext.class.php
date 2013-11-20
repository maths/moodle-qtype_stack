<?php
// This file is part of Stack - http://stack.bham.ac.uk/
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
 * CAS text and related functions.
 *
 * @copyright  2012 University of Birmingham
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once('cassession.class.php');
require_once('casstring.class.php');
require_once('castext/castextparser.class.php');
require_once('castext/raw.class.php');
require_once('castext/latex.class.php');
require_once('castext/if.class.php');
require_once('castext/define.class.php');
require_once('castext/foreach.class.php');
require_once('castext/external.class.php');

class stack_cas_text {

    /** @var string Exactly the cas_text entered. */
    private $rawcastext;

    /** @var string This is processed gradually. */
    private $trimmedcastext;

    /** @var string The end result. */
    private $castext;

    /**
     * @var stack_cas_session Context in which the castext is evaluated.
     *  Note, this is the place to set any CAS options of STACK_CAS_Maxima_Preferences.
     */
    private $session;

    /** @var bool whether the string is valid. */
    private $valid;

    /** @var bool whether this been sent to the CAS yet? Stops re-sending to the CAS. */
    private $instantiated = null;

    /** @var array any error messages to display to the user. */
    private $errors;

    /** @var string security level, 's' or 't'. */
    private $security;

    /** @var bool whether to insert stars. */
    private $insertstars;

    /** @var bool whether to do strict syntax checks. */
    private $syntax;

    /** @var stack_cas_castext_parsetreenode the root of the parse tree */
    private $parse_tree_root = null;

    /** @var array holds block-handlers for various parse_tree nodes */
    private $blocks = array();


    public function __construct($rawcastext, $session=null, $seed=null, $security='s', $syntax=true, $insertstars=false) {

        if (!is_string($rawcastext)) {
            throw new stack_exception('stack_cas_text: raw_castext must be a STRING.');
        } else {
            $this->rawcastext   = $rawcastext;
        }

        if (is_a($session, 'stack_cas_session') || null===$session) {
            $this->session      = $session;
        } else {
            throw new stack_exception('stack_cas_text constructor expects $session to be a stack_cas_session.');
        }

        if (is_int($seed)) {
            $this->seed = $seed;
        } else if ($seed === null) {
            $this->seed = time();
        } else {
            throw new stack_exception('stack_cas_text: $seed must be a number (or null).');
        }

        if (!('s'===$security || 't'===$security)) {
            throw new stack_exception('stack_cas_text: 4th argument, security level, must be "s" or "t" only.');
        }

        if (!is_bool($syntax)) {
            throw new stack_exception('stack_cas_text: 5th argument, stringSyntax, must be Boolean.');
        }

        if (!is_bool($insertstars)) {
            throw new stack_exception('stack_cas_text: 6th argument, insertStars, must be Boolean.');
        }

        $this->security    = $security;
        $this->syntax      = $syntax;
        $this->insertstars = $insertstars;
    }

    /**
     * Checks the castext syntax is valid, no missing @'s, $'s etc
     *
     * @access public
     * @return bool
     */
    private function validate() {
        $this->errors = array();
        if (strlen(trim($this->rawcastext)) > 64000) {
            // Limit to just less than 64kb. Maximum practical size of a post. (About 14 pages).
            $this->errors[] = stack_string("stackCas_tooLong");
            $this->valid = false;
            return false;
        }

        // Remove any comments from the castext.
        $this->trimmedcastext = stack_utils::remove_comments(str_replace("\n", ' ', $this->rawcastext));

        if (trim($this->trimmedcastext) === '') {
            $this->valid = true;
            return true;
        }

        // Find reasons to invalidate the text...
        $this->valid = true;

        // Check {@...@}'s match.
        $amps = stack_utils::check_bookends($this->trimmedcastext, '{@', '@}');
        if ($amps !== true) {
            if ($amps == 'left') {
                $this->errors[] = stack_string('stackCas_MissingOpenTeXCAS');
            } else {
                $this->errors[] = stack_string('stackCas_MissingClosingTeXCAS');
            }
            $this->valid = false;
        }

        // Check {#...#}'s match.
        $amps = stack_utils::check_bookends($this->trimmedcastext, '{#', '#}');
        if ($amps !== true) {
            if ($amps == 'left') {
                $this->errors[] = stack_string('stackCas_MissingOpenRawCAS');
            } else {
                $this->errors[] = stack_string('stackCas_MissingClosingRawCAS');
            }
            $this->valid = false;
        }

        // Dollars can be protected for use with currency.
        $protected = str_replace('\$', '', $this->trimmedcastext);
        $dollar = stack_utils::check_matching_pairs($protected, '$');
        if ($dollar == false) {
            $this->errors[] = stack_string('stackCas_MissingDollar');
            $this->valid = false;
        }

        $hints = stack_utils::check_bookends($this->trimmedcastext, '<hint>', '</hint>');
        if ($hints !== true) {
            // The method check_bookends does not return false.
            $this->valid = false;
            if ($hints == 'left') {
                $this->errors[] = stack_string('stackCas_MissingOpenHint');
            } else {
                $this->errors[] = stack_string('stackCas_MissingClosingHint');
            }
        }

        $html = stack_utils::check_bookends($this->trimmedcastext, '<html>', '</html>');
        if ($html !== true) {
            // The method check_bookends does not return false.

            $this->valid = false;
            if ($html == 'left') {
                $this->errors[] = stack_string('stackCas_MissingOpenHTML');
            } else {
                $this->errors[] = stack_string('stackCas_MissingCloseHTML');
            }
        }

        $inline = stack_utils::check_bookends($this->trimmedcastext, '\[', '\]');
        if ($inline !== true) {
            // The method check_bookends does not return false.

            $this->valid = false;
            if ($inline == 'left') {
                $this->errors[] = stack_string('stackCas_MissingOpenDisplay');
            } else {
                $this->errors[] = stack_string('stackCas_MissingCloseDisplay');
            }
        }

        $inline = stack_utils::check_bookends($this->trimmedcastext, '\(', '\)');
        if ($inline !== true) {
            // The method check_bookends does not return false.
            $this->valid = false;
            if ($inline == 'left') {
                $this->errors[] = stack_string('stackCas_MissingOpenInline');
            } else {
                $this->errors[] = stack_string('stackCas_MissingCloseInline');
            }
        }

        // Perform validation on the existing session.
        if (null != $this->session) {
            if (!$this->session->get_valid()) {
                $this->valid = false;
            }
        }

        // Perform block and casstring validation
        $parser = new stack_cas_castext_castextparser($this->trimmedcastext);
        $array_form = $parser->match_castext();
        $array_form = stack_cas_castext_castextparser::normalize($array_form);
        $array_form = stack_cas_castext_castextparser::block_conversion($array_form);

        $validation_parse_tree_root = stack_cas_castext_parsetreenode::build_from_nested($array_form);

        $this->valid = $this->valid && $this->validation_recursion($validation_parse_tree_root);

        if (array_key_exists('errors', $array_form)) {
            $this->valid = false;
            $this->errors[] = 'ARRAY-FROM'. $array_form['errors'];
        }

        return $this->valid;
    }


    private function validation_recursion($node) {
        $valid = true;
        switch ($node->type) {
            case 'castext':
                $iter = $node->first_child;
                while ($iter !== null) {
                    $valid = $valid && $this->validation_recursion($iter);
                    $iter = $iter->next_sibling;
                }
                break;
            case 'block':
                $block = null;
                switch ($node->get_content()) {
                    case 'if':
                        $block = new stack_cas_castext_if($node, $this->session, $this->seed, $this->security, $this->syntax, $this->insertstars);
                        break;
                    case 'define':
                        $block = new stack_cas_castext_define($node, $this->session, $this->seed, $this->security, $this->syntax, $this->insertstars);
                        break;
                    case 'foreach':
                        $block = new stack_cas_castext_foreach($node, $this->session, $this->seed, $this->security, $this->syntax, $this->insertstars);
                        break;
                    case 'external':
                        $block = new stack_cas_castext_external($node, $this->session, $this->seed, $this->security, $this->syntax, $this->insertstars);
                        break;
                    default:
                        $this->errors[] = stack_string('stackBlock_unknownBlock') . " '" . $node->get_content() . "'";
                        $valid = false;
                        break;
                }
                if ($valid) {
                    $err = '';
                    $block->validate($err);
                    if ($err != '') {
                        $valid = false;
                        $this->errors[] = $err;
                    } else {
                        $iter = $node->first_child;
                        while ($iter !== null) {
                            $valid = $valid && $this->validation_recursion($iter);
                            $iter = $iter->next_sibling;
                        }
                    }
                }
                break;
            case 'rawcasblock':
                $block = new stack_cas_castext_raw($node, $this->session, $this->seed, $this->security, $this->syntax, $this->insertstars);
                $block->validate($this->errors);
                break;
            case 'texcasblock':
                $block = new stack_cas_castext_latex($node, $this->session, $this->seed, $this->security, $this->syntax, $this->insertstars);
                $block->validate($this->errors);
                break;
        }
        return $valid;
    }


    private function first_pass_recursion(&$node, $condition_stack) {
        $block_child_evaluation = false;
        switch ($node->type) {
            case 'castext':
                $iter = $node->first_child;
                while ($iter !== null) {
                    $this->first_pass_recursion($iter, $condition_stack);
                    $iter = $iter->next_sibling;
                }
                break;
            case 'block':
                $block = null;
                switch ($node->get_content()) {
                    case 'if':
                        $block = new stack_cas_castext_if($node, $this->session, $this->seed, $this->security, $this->syntax, $this->insertstars);
                        break;
                    case 'define':
                        $block = new stack_cas_castext_define($node, $this->session, $this->seed, $this->security, $this->syntax, $this->insertstars);
                        break;
                    case 'foreach':
                        $block = new stack_cas_castext_foreach($node, $this->session, $this->seed, $this->security, $this->syntax, $this->insertstars);
                        break;
                    case 'external':
                        $block = new stack_cas_castext_external($node, $this->session, $this->seed, $this->security, $this->syntax, $this->insertstars);
                        break;
                    default:
                        throw new stack_exception('stack_cas_text: UNKNOWN NODE '.$node->get_content());
                }
                $block->extract_attributes($this->session, $condition_stack);
                $this->blocks[] = $block;
                $new_stack = $block->content_evaluation_context($condition_stack);
                if ($new_stack === false) {
                    $block_child_evaluation = true;
                } else {
                    $condition_stack = $new_stack;
                }
                if (!$block_child_evaluation) {
                    $iter = $node->first_child;
                    while ($iter !== null) {
                        $this->first_pass_recursion($iter, $condition_stack);
                        $iter = $iter->next_sibling;
                    }
                }
                break;
            case 'rawcasblock':
                $block = new stack_cas_castext_raw($node, $this->session, $this->seed, $this->security, $this->syntax, $this->insertstars);
                $block->extract_attributes($this->session, $condition_stack);
                $this->blocks[] = $block;
                break;
            case 'texcasblock':
                $block = new stack_cas_castext_latex($node, $this->session, $this->seed, $this->security, $this->syntax, $this->insertstars);
                $block->extract_attributes($this->session, $condition_stack);
                $this->blocks[] = $block;
                break;
        }
    }


    /**
     * This function actually evaluates the castext.
     */
    private function instantiate() {
        // Initial pass
        if (stack_cas_castext_castextparser::castext_parsing_required($this->trimmedcastext)) {
            if ($this->session == null) {
                $this->session = new stack_cas_session(array(), null, $this->seed);
            }
            $parser = new stack_cas_castext_castextparser($this->trimmedcastext);
            $array_form = $parser->match_castext();
            $array_form = stack_cas_castext_castextparser::normalize($array_form);
            $array_form = stack_cas_castext_castextparser::block_conversion($array_form);
            $this->parse_tree_root = stack_cas_castext_parsetreenode::build_from_nested($array_form);
            $this->first_pass_recursion($this->parse_tree_root, array());
        }

        if (null!=$this->session) {
            if (!$this->session->get_valid()) {
                $this->valid = false;
            }
        }

        if (!$this->valid) {
            return false;
        }

        // Deal with castext without any CAS variables.
        if (null !== $this->session && count($this->session->get_session()) > 0) {
            $this->session->instantiate();
        }

        // Handle blocks
        $requires_rerun = false;
        foreach (array_reverse($this->blocks) as $block) {
            $requires_rerun = $block->process_content($this->session) || $requires_rerun;
        }

        while ($requires_rerun) {
            $this->blocks = array();

            $this->trimmedcastext = $this->parse_tree_root->to_string();

            $parser = new stack_cas_castext_castextparser($this->trimmedcastext);
            $array_form = $parser->match_castext();
            $array_form = stack_cas_castext_castextparser::normalize($array_form);
            $array_form = stack_cas_castext_castextparser::block_conversion($array_form);
            $this->parse_tree_root = stack_cas_castext_parsetreenode::build_from_nested($array_form);
            $this->first_pass_recursion($this->parse_tree_root, array());
            $this->session->instantiate();
            $requires_rerun = false;
            foreach (array_reverse($this->blocks) as $block) {
                $requires_rerun = $block->process_content($this->session) || $requires_rerun;
            }
        }

        foreach ($this->blocks as $block) {
            $block->clear();
        }

        if (trim($this->trimmedcastext) !== '' && $this->parse_tree_root !== null) {
            $this->trimmedcastext = $this->parse_tree_root->to_string();
        }

        // Replaces the old "hints" filter from STACK 2.0.
        // These strings are now part of the regular language files.
        $strin = $this->trimmedcastext;
        preg_match_all('|<hint>(.*)</hint>|U', $strin, $html_match);
        foreach ($html_match[1] as $val) {
            $sr = '<hint>'.$val.'</hint>';
            $rep = '<div class="secondaryFeedback"><h3 class="secondaryFeedback">' .
                    stack_string($val.'_name') . '</h3>' . stack_string($val . '_fact') . '</div>';
            $strin = str_replace($sr, $rep, $strin);
        }
        $this->trimmedcastext = $strin;

        $this->castext = stack_utils::wrap_around($this->trimmedcastext);

        // Another modification. Stops <html> tags from being given $ tags and therefore breaking tth.
        $this->castext = str_replace('\(<html>', '', $this->castext);
        // Bug occurs when maxima returns <html>tags in output, eg plots or div by 0 errors.
        $this->castext = str_replace('</html>\)', '', $this->castext);
        $this->latex_tidy();

        $this->instantiated = true;
    }

    /**
     * Tidy up LaTeX commands used in castext which are not interpreted by JSMath.
     */
    private function latex_tidy() {
        // Need to create line breaks in sensible places.
        $this->castext = str_replace('\begin{itemize}', '<ol>', $this->castext);
        $this->castext = str_replace('\end{itemize}', '</ol>', $this->castext);
        $this->castext = str_replace('\begin{enumerate}', '<ul>', $this->castext);
        $this->castext = str_replace('\end{enumerate}', '</ul>', $this->castext);
        $this->castext = str_replace('\item', '<li>', $this->castext);
    }

    public function get_valid() {
        if (null===$this->valid) {
            $this->validate();
        }
        return $this->valid;
    }

    public function get_errors($casdebug=false) {
        if (null===$this->valid) {
            $this->validate();
        }

        $errmsg = '';
        if ($this->errors != array()) {
            $errmsg .= implode($this->errors, ' ');
        }

        if (null !== $this->session) {
            $errmsg .= $this->session->get_errors();
        }

        if ('' != trim($errmsg)) {
            $errmsg = '<span class="error">'.stack_string("stackCas_failedValidation").'</span>'.$errmsg;
        }

        if ($casdebug && null !== $this->session) {
            $errmsg .= $this->session->get_debuginfo();
        }

        return $errmsg;
    }

    public function get_all_raw_casstrings() {
        if (null===$this->valid) {
            $this->validate();
        }

        // 31/10/2013 
        // This function is only used by the unit tests.  It is essential to
        // look *inside* the session to make sure all variables are grabbed from the
        // text.  However, there is no harm in instantiating it to get the full session.
        $this->instantiate();
        if (null !== $this->session) {
            return $this->session->get_all_raw_casstrings();
        } else {
            return array();
        }
    }

    public function get_display_castext() {
        if (null===$this->valid) {
            $this->validate();
        }
        if (null === $this->instantiated) {
            $this->instantiate();
        } else if (false === $this->instantiated) {
            return false;
        }
        return $this->castext;
    }

    public function get_session() {
        if (null===$this->valid) {
            $this->validate();
        }
        if (null===$this->instantiated) {
            $this->instantiate();
        } else if (false === $this->instantiated) {
            return false;
        }
        return $this->session;
    }

    /* Simply passes the keywords through to session.*/
    public function check_external_forbidden_words($keywords) {
        if (null===$this->valid) {
            $this->validate();
        }
        if (!is_a($this->session, 'stack_cas_session')) {
            return false;
        }
        return $this->session->check_external_forbidden_words($keywords);
    }

    public function get_debuginfo() {
        if (null !== $this->session) {
            return $this->session->get_debuginfo();
        }
        return '';
    }

}
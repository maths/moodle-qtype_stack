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

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/stack/mathsoutput/mathsoutput.class.php');

/**
 * Base class for all the types of exception we throw.
 */
class stack_exception extends moodle_exception {
    public function __construct($error) {
        parent::__construct('exceptionmessage', 'qtype_stack', '', $error);
    }
}

/**
 * You need to call this method on the string you get from
 * $castext->get_display_castext() before you echo it. This ensures that equations
 * are displayed properly.
 * @param string $castext the result of calling $castext->get_display_castext().
 * @return string HTML ready to output.
 */
function stack_ouput_castext($castext) {
    return format_text(stack_maths::process_display_castext($castext),
            FORMAT_HTML, ['noclean' => true, 'allowid' => true]);
}

/**
 * Equivalent to get_string($key, 'qtype_stack', $a), but this method ensure that
 * any equations in the string are displayed properly.
 * @param string $key the string name.
 * @param mixed $a (optional) any values to interpolate into the string.
 * @return string the language string
 */
function stack_string($key, $a = null) {
    return stack_maths::process_lang_string(get_string($key, 'qtype_stack', $a));
}

/**
 * Equivalent to get_string($key, 'qtype_stack', $a), but this method ensure that
 * any equations in the string are displayed properly and that this message is formatted as an error.
 * @param string $key the string name.
 * @param mixed $a (optional) any values to interpolate into the string.
 * @return string the language string
 */
function stack_string_error($key, $a = null) {
    $key = stack_maths::process_lang_string(get_string($key, 'qtype_stack', $a));
    return '<i class="icon fa fa-exclamation-circle text-danger fa-fw " title="' . $key . '" aria-label="' .
            $key . '"></i>' . $key;
}

/**
 * Private helper used by the next function.
 *
 * @return array search => replace strings.
 */
function get_stack_maxima_latex_replacements() {
    // This is an array language code => replacements array.
    static $replacements = [];

    $lang = current_language();
    if (!isset($replacements[$lang])) {
        $replacements[$lang] = [
            'QMCHAR' => '?',
            '!LEFTSQ!' => '\left[',
            '!LEFTR!' => '\left(',
            '!RIGHTSQ!' => '\right]',
            '!RIGHTR!' => '\right)',
            '!ANDOR!' => stack_string('equiv_ANDOR'),
            '!SAMEROOTS!' => stack_string('equiv_SAMEROOTS'),
            '!MISSINGVAR!' => stack_string('equiv_MISSINGVAR'),
            '!ASSUMEPOSVARS!' => stack_string('equiv_ASSUMEPOSVARS'),
            '!ASSUMEPOSREALVARS!' => stack_string('equiv_ASSUMEPOSREALVARS'),
            '!LET!' => stack_string('equiv_LET'),
            '!AND!' => stack_string('equiv_AND'),
            '!OR!' => stack_string('equiv_OR'),
            '!NOT!' => stack_string('equiv_NOT'),
            '!NAND!' => stack_string('equiv_NAND'),
            '!NOR!' => stack_string('equiv_NOR'),
            '!XOR!' => stack_string('equiv_XOR'),
            '!XNOR!' => stack_string('equiv_XNOR'),
            '!IMPLIES!' => stack_string('equiv_IMPLIES'),
            '!BOOLTRUE!' => stack_string('true'),
            '!BOOLFALSE!' => stack_string('false'),
        ];
    }
    return $replacements[$lang];
}

/**
 * This function tidies up LaTeX from Maxima.
 * @param string $rawfeedback
 * @return string
 */
function stack_maxima_latex_tidy($latex) {
    $replacements = get_stack_maxima_latex_replacements();
    $latex = str_replace(array_keys($replacements), array_values($replacements), $latex);

    // Also previously some spaces have been eliminated and line changes dropped.
    // Apparently returning verbatim LaTeX was not a thing.
    $latex = str_replace("\n ", '', $latex);
    $latex = str_replace("\n", '', $latex);
    // Just don't want to use regexp.
    $latex = str_replace('    ', ' ', $latex);
    $latex = str_replace('   ', ' ', $latex);
    $latex = str_replace('  ', ' ', $latex);

    return $latex;
}

/**
 * This function takes a feedback string from Maxima and unpacks and translates it.
 * @param string $rawfeedback
 * @return string
 */
function stack_maxima_translate($rawfeedback) {

    if (strpos($rawfeedback, 'stack_trans') === false) {
        return trim(stack_maxima_latex_tidy($rawfeedback));
    } else {
        $rawfeedback = str_replace('[[', '', $rawfeedback);
        $rawfeedback = str_replace(']]', '', $rawfeedback);
        $rawfeedback = str_replace("\n", '', $rawfeedback);
        $rawfeedback = str_replace('\n', '', $rawfeedback);
        $rawfeedback = str_replace('!quot!', '"', $rawfeedback);

        $translated = [];
        preg_match_all('/stack_trans\(.*?\);/', $rawfeedback, $matches);
        $feedback = $matches[0];
        foreach ($feedback as $fb) {
            $fb = substr($fb, 12, -2);
            if (strstr($fb, "' , \"") === false) {
                // We only have a feedback tag, with no optional arguments.
                $translated[] = trim(stack_string(substr($fb, 1, -1)));
            } else {
                // We have a feedback tag and some optional arguments.
                $tag = substr($fb, 1, strpos($fb, "' , \"") - 1);
                $arg = substr($fb, strpos($fb, "' , \"") + 5, -2);
                $args = explode('"  , "', $arg);

                $a = [];
                for ($i = 0; $i < count($args); $i++) {
                    $a["m{$i}"] = $args[$i];
                }
                $translated[] = trim(stack_string($tag, $a));
            }
        }

        return stack_maxima_latex_tidy(implode(' ', $translated));
    }
}

function stack_maxima_format_casstring($str) {
    // Santise the output, E.g. '>' -> '&gt;'.
    $str = stack_string_sanitise($str);
    $str = str_replace('[[syntaxexamplehighlight]', '<span class="stacksyntaxexamplehighlight">', $str);
    $str = str_replace('[syntaxexamplehighlight]]', '</span>', $str);

    return html_writer::tag('span', $str, ['class' => 'stacksyntaxexample']);
}

function stack_string_sanitise($str) {
    // Students may not input strings containing specific LaTeX
    // i.e. no math-modes due to us being unable to decide if
    // it is safe.
    $str = str_replace('\\[', '\\&#8203;[', $str);
    $str = str_replace('\\]', '\\&#8203;]', $str);
    $str = str_replace('\\(', '\\&#8203;(', $str);
    $str = str_replace('\\)', '\\&#8203;)', $str);
    $str = str_replace('$$', '$&#8203;$', $str);
    // Also any script tags need to be disabled.
    $str = str_ireplace('<script', '&lt;&#8203;script', $str);
    $str = str_ireplace('</script>', '&lt;&#8203;/script&gt;', $str);
    $str = str_ireplace('<iframe', '&lt;&#8203;iframe', $str);
    $str = str_ireplace('</iframe>', '&lt;&#8203;/iframe&gt;', $str);
    $str = str_ireplace('<style', '&lt;&#8203;style', $str);
    $str = str_ireplace('</style>', '&lt;&#8203;/style&gt;', $str);
    $str = str_ireplace('<div', '&lt;&#8203;div', $str);
    $str = str_ireplace('</div>', '&lt;&#8203;/div&gt;', $str);
    $str = str_ireplace('/>', '/&gt;', $str);
    $str = str_ireplace('</', '&lt;/', $str);
    $str = str_ireplace('<!--', '&lt;!--', $str);
    $str = str_ireplace('-->', '--&gt;', $str);

    $pat = ['/(on)([a-z]+[ ]*)(=)/i', '/(href)([ ]*)(=)/i', '/(src)([ ]*)(=)/i'];
    $rep = ['on&#0;$2&#0;&#61;', 'href&#0;$2&#61;', 'src&#0;$2&#61;'];
    $str = preg_replace($pat, $rep, $str);
    return $str;
}

/**
 * Used by the questiontest*.php scripts, and deploy.php, to do some initialisation
 * that is needed on all of them.
 * @return array page context, selected seed (or null), and URL parameters.
 */
function qtype_stack_setup_question_test_page($question) {
    $seed = optional_param('seed', null, PARAM_INT);
    $urlparams = ['questionid' => $question->id];
    if (!is_null($seed) && $question->has_random_variants()) {
        $urlparams['seed'] = $seed;
    }

    // Were we given a particular context to run the question in?
    // This affects things like filter settings, or forced theme or language.
    if ($cmid = optional_param('cmid', 0, PARAM_INT)) {
        $cm = get_coursemodule_from_id(false, $cmid);
        require_login($cm->course, false, $cm);
        $context = context_module::instance($cmid);
        $urlparams['cmid'] = $cmid;

    } else if ($courseid = optional_param('courseid', 0, PARAM_INT)) {
        require_login($courseid);
        $context = context_course::instance($courseid);
        $urlparams['courseid'] = $courseid;

    } else {
        $context = $question->get_context();
        if ($context->contextlevel == CONTEXT_MODULE) {
            $urlparams['cmid'] = $context->instanceid;
        } else if ($context->contextlevel == CONTEXT_COURSE) {
            $urlparams['courseid'] = $context->instanceid;
        } else {
            $urlparams['courseid'] = SITEID;
        }
    }

    return [$context, $seed, $urlparams];
}

/* This class is needed to ignore requests for pluginfile rewrites in the bulk tester
 * and possibly elsewhere, e.g. API.
 */
class stack_outofcontext_process {

    public function __construct() {
    }

    /**
     * Calls {@link question_rewrite_question_urls()} with appropriate parameters
     * for content belonging to this question.
     * @param string $text the content to output.
     * @param string $component the component name (normally 'question' or 'qtype_...')
     * @param string $filearea the name of the file area.
     * @param int $itemid the item id.
     * @return string the content with the URLs rewritten.
     */
    public function rewrite_pluginfile_urls($text, $component, $filearea, $itemid) {
        return $text;
    }
}

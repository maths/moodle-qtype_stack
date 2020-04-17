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
 * Configuration settings declaration information for the Stack question type.
 *
 * @package    qtype_stack
 * @copyright  2012 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/settingslib.php');
require_once(__DIR__ . '/stack/options.class.php');
require_once(__DIR__ . '/stack/potentialresponsetree.class.php');

// Useful links.
$links = array(
    get_string('stackDoc_docs_desc', 'qtype_stack',
            array('link' => (string) new moodle_url('/question/type/stack/doc/doc.php/'))),
    get_string('healthcheck_desc', 'qtype_stack',
            array('link' => (string) new moodle_url('/question/type/stack/healthcheck.php'))),
    get_string('chat_desc', 'qtype_stack',
            array('link' => (string) new moodle_url('/question/type/stack/caschat.php'))),
    get_string('stackInstall_testsuite_title_desc', 'qtype_stack',
            array('link' => (string) new moodle_url('/question/type/stack/answertests.php'))),
    get_string('stackInstall_input_title_desc', 'qtype_stack',
            array('link' => (string) new moodle_url('/question/type/stack/studentinputs.php'))),
    get_string('bulktestindexintro_desc', 'qtype_stack',
            array('link' => (string) new moodle_url('/question/type/stack/bulktestindex.php'))),
    get_string('stackInstall_replace_dollars_desc', 'qtype_stack',
            array('link' => (string) new moodle_url('/question/type/stack/replacedollarsindex.php'))),
);
$settings->add(new admin_setting_heading('docs',
        get_string('settingusefullinks', 'qtype_stack'),
        '* ' . implode("\n* ", $links)));

// Options for connection to Maxima.
// Note that any settings here where we try to set the default
// intelligently in install.php, the default here must be null.
// Otherwise, the default here will overwrite anything set in install.php.
$settings->add(new admin_setting_heading('maixmasettingsheading',
        get_string('settingsmaximasettings', 'qtype_stack'), ''));

$settings->add(new admin_setting_configselect('qtype_stack/platform',
        get_string('settingplatformtype', 'qtype_stack'),
        // Note, install.php tries to auto-detect Windows installs, and set the default appropriately.
        get_string('settingplatformtype_desc', 'qtype_stack'), null, array(
                'unix'             => get_string('settingplatformtypeunix',                'qtype_stack'),
                'unix-optimised'   => get_string('settingplatformtypeunixoptimised',       'qtype_stack'),
                'win'              => get_string('settingplatformtypewin',                 'qtype_stack'),
                'server'           => get_string('settingplatformtypeserver',              'qtype_stack'))));

$settings->add(new admin_setting_configselect('qtype_stack/maximaversion',
        get_string('settingcasmaximaversion', 'qtype_stack'),
        get_string('settingcasmaximaversion_desc', 'qtype_stack'), null,
                array('5.38.1' => '5.38.1', '5.39.0' => '5.39.0',
                      '5.40.0' => '5.40.0', '5.41.0' => '5.41.0', '5.42.0' => '5.42.0',
                      '5.42.1' => '5.42.1', '5.42.2' => '5.42.2',
                      'default' => 'default')));

$settings->add(new admin_setting_configtext('qtype_stack/castimeout',
        get_string('settingcastimeout', 'qtype_stack'),
        get_string('settingcastimeout_desc', 'qtype_stack'), 10, PARAM_INT, 3));

$settings->add(new admin_setting_configselect('qtype_stack/casresultscache',
        get_string('settingcasresultscache', 'qtype_stack'),
        get_string('settingcasresultscache_desc', 'qtype_stack'), 'db', array(
            'none' => get_string('settingcasresultscache_none', 'qtype_stack'),
            'db' => get_string('settingcasresultscache_db', 'qtype_stack'),
        )));

$settings->add(new admin_setting_configtext('qtype_stack/maximacommand',
        get_string('settingplatformmaximacommand', 'qtype_stack'),
        get_string('settingplatformmaximacommand_desc', 'qtype_stack'), null));

$settings->add(new admin_setting_configtext('qtype_stack/serveruserpass',
        get_string('settingserveruserpass', 'qtype_stack'),
        get_string('settingserveruserpass_desc', 'qtype_stack'), ''));

$settings->add(new admin_setting_configtext('qtype_stack/plotcommand',
        get_string('settingplatformplotcommand', 'qtype_stack'),
        get_string('settingplatformplotcommand_desc', 'qtype_stack'), ''));

// The supported libraries are defined by public static $maximalibraries in installhelper.php.
$settings->add(new admin_setting_configtext('qtype_stack/maximalibraries',
        get_string('settingmaximalibraries', 'qtype_stack'),
        get_string('settingmaximalibraries_desc', 'qtype_stack'), null));

$settings->add(new admin_setting_configcheckbox('qtype_stack/casdebugging',
        get_string('settingcasdebugging', 'qtype_stack'),
        get_string('settingcasdebugging_desc', 'qtype_stack'), 0));

// @codingStandardsIgnoreStart
// ILIAS: will need to replicate this cache.
// The Moodle cache API is quite simple, so to replicate it we need only implement
//   $cache = cache::make('qtype_stack', 'parsercache');
//   $ast = $cache->get($cachekey); // Returns null/false or something if key not present.
//   $cache->set($cachekey, $ast);
// Or, it is already possible to disable this, because there is a config variable that can be set to 0 to disable.
// @codingStandardsIgnoreEnd
$settings->add(new admin_setting_configtext('qtype_stack/parsercacheinputlength',
        get_string('settingparsercacheinputlength', 'qtype_stack'),
        get_string('settingparsercacheinputlength_desc', 'qtype_stack'), 50, PARAM_INT, 3));

// Options for maths display.
$settings->add(new admin_setting_heading('mathsdisplayheading',
        get_string('settingsmathsdisplayheading', 'qtype_stack'), ''));

$settings->add(new admin_setting_configcheckbox('qtype_stack/ajaxvalidation',
        get_string('settingajaxvalidation', 'qtype_stack'),
        get_string('settingajaxvalidation_desc', 'qtype_stack'), 1));

$settings->add(new qtype_stack_admin_setting_maths_display_method('qtype_stack/mathsdisplay',
        get_string('settingmathsdisplay', 'qtype_stack'),
        get_string('settingmathsdisplay_desc', 'qtype_stack'), 'mathjax', null));

$settings->add(new admin_setting_configcheckbox('qtype_stack/replacedollars',
        get_string('settingreplacedollars', 'qtype_stack'),
        get_string('settingreplacedollars_desc', 'qtype_stack'), false));

// Options for new inputs.
$settings->add(new admin_setting_heading('inputoptionsheading',
        get_string('settingdefaultinputoptions', 'qtype_stack'),
        get_string('settingdefaultinputoptions_desc', 'qtype_stack')));

$settings->add(new qtype_stack_admin_setting_input_types('qtype_stack/inputtype',
        get_string('inputtype', 'qtype_stack'),
        get_string('inputtype_help', 'qtype_stack'), 'algebraic', null));

$settings->add(new admin_setting_configtext('qtype_stack/inputboxsize',
        get_string('boxsize', 'qtype_stack'),
        get_string('boxsize_help', 'qtype_stack'), '15', PARAM_INT));

$settings->add(new admin_setting_configselect('qtype_stack/inputstrictsyntax',
        get_string('strictsyntax', 'qtype_stack'),
        get_string('strictsyntax_help', 'qtype_stack'), '1',
        stack_options::get_yes_no_options()));

$settings->add(new admin_setting_configselect('qtype_stack/inputinsertstars',
        get_string('insertstars', 'qtype_stack'),
        get_string('insertstars_help', 'qtype_stack'), '0',
        stack_options::get_insert_star_options()));

$settings->add(new admin_setting_configtext('qtype_stack/inputforbidwords',
        get_string('forbidwords', 'qtype_stack'),
        get_string('forbidwords_help', 'qtype_stack'), '', PARAM_RAW));

$settings->add(new admin_setting_configselect('qtype_stack/inputforbidfloat',
        get_string('forbidfloat', 'qtype_stack'),
        get_string('forbidfloat_help', 'qtype_stack'), '1',
        stack_options::get_yes_no_options()));

$settings->add(new admin_setting_configselect('qtype_stack/inputrequirelowestterms',
        get_string('requirelowestterms', 'qtype_stack'),
        get_string('requirelowestterms_help', 'qtype_stack'), '0',
        stack_options::get_yes_no_options()));

$settings->add(new admin_setting_configselect('qtype_stack/inputcheckanswertype',
        get_string('checkanswertype', 'qtype_stack'),
        get_string('checkanswertype_help', 'qtype_stack'), '0',
        stack_options::get_yes_no_options()));

$settings->add(new admin_setting_configselect('qtype_stack/inputmustverify',
        get_string('mustverify', 'qtype_stack'),
        get_string('mustverify_help', 'qtype_stack'), '1',
        stack_options::get_yes_no_options()));

$settings->add(new admin_setting_configselect('qtype_stack/inputshowvalidation',
        get_string('showvalidation', 'qtype_stack'),
        get_string('showvalidation_help', 'qtype_stack'), '1',
        stack_options::get_showvalidation_options()));

// Options for new questions.
$settings->add(new admin_setting_heading('questionoptionsheading',
        get_string('settingdefaultquestionoptions', 'qtype_stack'),
        get_string('settingdefaultquestionoptions_desc', 'qtype_stack')));

$settings->add(new admin_setting_configselect('qtype_stack/questionsimplify',
        get_string('questionsimplify', 'qtype_stack'),
        get_string('autosimplify_help', 'qtype_stack'), '1',
        stack_options::get_yes_no_options()));

$settings->add(new admin_setting_configselect('qtype_stack/assumepositive',
        get_string('assumepositive', 'qtype_stack'),
        get_string('assumepositive_help', 'qtype_stack'), '0',
        stack_options::get_yes_no_options()));

$settings->add(new admin_setting_configselect('qtype_stack/assumereal',
        get_string('assumereal', 'qtype_stack'),
        get_string('assumereal_help', 'qtype_stack'), '0',
        stack_options::get_yes_no_options()));

$settings->add(new admin_setting_configselect('qtype_stack/feedbackstyle',
        get_string('feedbackstyle', 'qtype_stack'),
        get_string('feedbackstyle', 'qtype_stack'), '1',
        stack_potentialresponse_tree::get_feedbackstyle_options()));

$settings->add(new admin_setting_configtextarea('qtype_stack/prtcorrect',
        get_string('prtcorrectfeedback', 'qtype_stack'), '',
        get_string('symbolicprtcorrectfeedback', 'qtype_stack') . ' ' .
            get_string('defaultprtcorrectfeedback', 'qtype_stack'), PARAM_RAW, 60, 3));

$settings->add(new admin_setting_configtextarea('qtype_stack/prtpartiallycorrect',
        get_string('prtpartiallycorrectfeedback', 'qtype_stack'), '',
        get_string('symbolicprtpartiallycorrectfeedback', 'qtype_stack') . ' ' .
            get_string('defaultprtpartiallycorrectfeedback', 'qtype_stack'), PARAM_RAW, 60, 3));

$settings->add(new admin_setting_configtextarea('qtype_stack/prtincorrect',
        get_string('prtincorrectfeedback', 'qtype_stack'), '',
        get_string('symbolicprtincorrectfeedback', 'qtype_stack') . ' ' .
            get_string('defaultprtincorrectfeedback', 'qtype_stack'), PARAM_RAW, 60, 3));

$settings->add(new admin_setting_configselect('qtype_stack/multiplicationsign',
        get_string('multiplicationsign', 'qtype_stack'),
        get_string('multiplicationsign_help', 'qtype_stack'), 'dot',
        stack_options::get_multiplication_sign_options()));

$settings->add(new admin_setting_configselect('qtype_stack/sqrtsign',
        get_string('sqrtsign', 'qtype_stack'),
        get_string('sqrtsign_help', 'qtype_stack'), '1',
        stack_options::get_yes_no_options()));

$settings->add(new admin_setting_configselect('qtype_stack/complexno',
        get_string('complexno', 'qtype_stack'),
        get_string('complexno_help', 'qtype_stack'), 'i',
        stack_options::get_complex_no_options()));

$settings->add(new admin_setting_configselect('qtype_stack/inversetrig',
        get_string('inversetrig', 'qtype_stack'),
        get_string('inversetrig_help', 'qtype_stack'), 'cos-1',
        stack_options::get_inverse_trig_options()));

$settings->add(new admin_setting_configselect('qtype_stack/logicsymbol',
        get_string('logicsymbol', 'qtype_stack'),
        get_string('logicsymbol_help', 'qtype_stack'), 'lang',
        stack_options::get_logic_options()));

$settings->add(new admin_setting_configselect('qtype_stack/matrixparens',
        get_string('matrixparens', 'qtype_stack'),
        get_string('matrixparens_help', 'qtype_stack'), '[',
        stack_options::get_matrix_parens_options()));

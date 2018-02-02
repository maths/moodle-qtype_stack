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

$platform = NULL;
$platformerrors = array(); $platformwarnings = array();
// Only perform configuration / installation checks if the neccessary config settings
// are present, i.e. STACK has been installed installed. We use three key settings...
if(isset(stack_utils::get_config()->platform) && isset(stack_utils::get_config()->maximaversion) && isset(stack_utils::get_config()->maximalibraries)) {
    // Check the current platform configuration; Store the results in $platform_warnings and $platform_errors:
    $platform = stack_platform_base::get_current();
    $checkrv = $platform->check_maxima_install();
    $platformerrors = $checkrv['errors']; $platformwarnings = $checkrv['warnings'];
}

// Which admin settings will be monitored for critical changes?
$monitored = array();

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
$settings->add(new admin_setting_heading('maixmasettingsheading',
        get_string('settingsmaximasettings', 'qtype_stack'),
        get_string('settingsmaximasettings_desc', 'qtype_stack')));

$settings->add(new qtype_stack_admin_messages('settingmaximaconnectionmessages',
        get_string('settingmaximaconnectionmessages', 'qtype_stack'), '', $platformerrors, $platformwarnings));

$settings->add($monitored[] = new admin_setting_configselect('qtype_stack/platform',
        get_string('settingplatformtype', 'qtype_stack'),
        // Note, install.php tries to auto-detect Windows installs, and set the default appropriately.
        // unfortunately, a clean install seems to override / overwrite that, so we try here too.
        get_string('settingplatformtype_desc', 'qtype_stack'),
        strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' ? "win" : "unix",
        stack_platform_base::get_names_and_descs()));

$settings->add($monitored[] = new admin_setting_configselect('qtype_stack/lisp',
        get_string('settinglisp', 'qtype_stack'),
        // Note, install.php tries to auto-detect Windows installs, and set the default appropriately.
        // unfortunately, a clean install seems to override / overwrite that, so we try here too.
        get_string('settinglisp_desc', 'qtype_stack'),
        strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' ? "win" : "unix",
        array(
                'gcl'              => get_string('settinglispgcl',               'qtype_stack'),
                'clisp'            => get_string('settinglispclisp',             'qtype_stack'),
                'sbcl'             => get_string('settinglispsbcl',              'qtype_stack'))));

$settings->add($monitored[] = new admin_setting_configselect('qtype_stack/maximaversion',
        get_string('settingcasmaximaversion', 'qtype_stack'),
        get_string('settingcasmaximaversion_desc', 'qtype_stack'), 'default',
                array('5.23.2' => '5.23.2', '5.25.1' => '5.25.1', '5.26.0' => '5.26.0',
                      '5.27.0' => '5.27.0', '5.28.0' => '5.28.0', '5.30.0' => '5.30.0',
                      '5.31.1' => '5.31.1', '5.31.2' => '5.31.2', '5.31.3' => '5.31.3',
                      '5.32.0' => '5.32.0', '5.32.1' => '5.32.1', '5.33.0' => '5.33.0',
                      '5.34.0' => '5.34.0', '5.34.1' => '5.34.1', '5.35.1' => '5.35.1',
                      '5.35.1.2' => '3.35.1.2', '5.36.0' => '5.36.0', '5.36.1' => '5.36.1',
                      '5.37.3' => '5.37.3', // Recently compiled GCL version for Windows is much faster.
                      '5.38.0' => '5.38.0', '5.38.1' => '5.38.1', '5.39.0' => '5.39.0',
                      '5.40.0' => '5.40.0', '5.41.0' => '5.41.0',
                      'default' => 'default')));

$settings->add($monitored[] = new admin_setting_configtext('qtype_stack/castimeout',
        get_string('settingcastimeout', 'qtype_stack'),
        get_string('settingcastimeout_desc', 'qtype_stack'), "10", PARAM_INT, 3));

$settings->add($monitored[] = new admin_setting_configtext('qtype_stack/exectimeout',
        get_string('settingexectimeout', 'qtype_stack'),
        get_string('settingexectimeout_desc', 'qtype_stack'), "12", PARAM_INT, 3));

$settings->add(new admin_setting_configselect('qtype_stack/casresultscache',
        get_string('settingcasresultscache', 'qtype_stack'),
        get_string('settingcasresultscache_desc', 'qtype_stack'), 'db', array(
            'none' => get_string('settingcasresultscache_none', 'qtype_stack'),
            'db' => get_string('settingcasresultscache_db', 'qtype_stack'),
        )));

$settings->add($monitored[] = new admin_setting_configtext('qtype_stack/maximapreoptcommand',
        get_string('settingplatformmaximapreoptcommand', 'qtype_stack'),
        get_string('settingplatformmaximapreoptcommand_desc', 'qtype_stack'), ''));

$settings->add($monitored[] = new admin_setting_configtext('qtype_stack/maximacommand',
        get_string('settingplatformmaximacommand', 'qtype_stack'),
        get_string('settingplatformmaximacommand_desc', 'qtype_stack'), ''));

$settings->add($monitored[] = new admin_setting_configcheckbox('qtype_stack/bypasslaunchscript',
        get_string('settingplatformbypasslaunchscript', 'qtype_stack'),
        get_string('settingplatformbypasslaunchscript_desc', 'qtype_stack'), '0'));

$settings->add($monitored[] = new admin_setting_configtext('qtype_stack/serveruserpass',
        get_string('settingserveruserpass', 'qtype_stack'),
        get_string('settingserveruserpass_desc', 'qtype_stack'), ''));

$settings->add($monitored[] = new admin_setting_configtext('qtype_stack/plotcommand',
        get_string('settingplatformplotcommand', 'qtype_stack'),
        get_string('settingplatformplotcommand_desc', 'qtype_stack'), ''));

$settings->add($monitored[] = new admin_setting_configtext('qtype_stack/maximalibraries',
        get_string('settingmaximalibraries', 'qtype_stack'),
        get_string('settingmaximalibraries_desc', 'qtype_stack'), 'stats, distrib, descriptive, simplex'));

$settings->add(new qtype_stack_admin_timestamp('qtype_stack/criticalsettingsupdated',
        get_string('settingcriticalsettingsupdated', 'qtype_stack'),
        get_string('settingcriticalsettingsupdated_desc', 'qtype_stack'), $monitored));


if ($platform && count($platformerrors) == 0) {
    if(!$platform->check_launch_script()) {
        $platform->generate_launch_script();
    }
    if(!stack_cas_configuration::check_maximalocal()) {
        stack_cas_configuration::create_maximalocal();
    }
}


$settings->add(new admin_setting_configcheckbox('qtype_stack/casdebugging',
        get_string('settingcasdebugging', 'qtype_stack'),
        get_string('settingcasdebugging_desc', 'qtype_stack'), '0'));


// Options for maths display.
$settings->add(new admin_setting_heading('mathsdisplayheading',
        get_string('settingsmathsdisplayheading', 'qtype_stack'), ''));

$settings->add(new admin_setting_configcheckbox('qtype_stack/ajaxvalidation',
        get_string('settingajaxvalidation', 'qtype_stack'),
        get_string('settingajaxvalidation_desc', 'qtype_stack'), '1'));

$settings->add(new qtype_stack_admin_setting_maths_display_method('qtype_stack/mathsdisplay',
        get_string('settingmathsdisplay', 'qtype_stack'),
        get_string('settingmathsdisplay_desc', 'qtype_stack'), 'mathjax', null));

$settings->add(new admin_setting_configcheckbox('qtype_stack/replacedollars',
        get_string('settingreplacedollars', 'qtype_stack'),
        get_string('settingreplacedollars_desc', 'qtype_stack'), '0'));


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

$settings->add(new admin_setting_configtextarea('qtype_stack/prtcorrect',
        get_string('prtcorrectfeedback', 'qtype_stack'), '',
        get_string('defaultprtcorrectfeedback', 'qtype_stack'), PARAM_RAW, '60', '3'));

$settings->add(new admin_setting_configtextarea('qtype_stack/prtpartiallycorrect',
        get_string('prtpartiallycorrectfeedback', 'qtype_stack'), '',
        get_string('defaultprtpartiallycorrectfeedback', 'qtype_stack'), PARAM_RAW, '60', '3'));

$settings->add(new admin_setting_configtextarea('qtype_stack/prtincorrect',
        get_string('prtincorrectfeedback', 'qtype_stack'), '',
        get_string('defaultprtincorrectfeedback', 'qtype_stack'), PARAM_RAW, '60', '3'));

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

$settings->add(new admin_setting_configselect('qtype_stack/matrixparens',
        get_string('matrixparens', 'qtype_stack'),
        get_string('matrixparens_help', 'qtype_stack'), '[',
        stack_options::get_matrix_parens_options()));

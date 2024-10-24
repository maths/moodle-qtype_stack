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

require_once(__DIR__ . '/../../locallib.php');
require_once(__DIR__ . '/../utils.class.php');
require_once(__DIR__ . '/../options.class.php');
require_once(__DIR__ . '/connectorhelper.class.php');
require_once(__DIR__ . '/cassession2.class.php');
require_once(__DIR__ . '/castext2/castext2_evaluatable.class.php');
require_once(__DIR__ . '/connector.dbcache.class.php');
require_once(__DIR__ . '/installhelper.class.php');

/**
 * This class supports the healthcheck functions..
 *
 * @copyright  2023 The University of Edinburgh
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(__DIR__ . '/ast.container.class.php');
require_once(__DIR__ . '/connectorhelper.class.php');
require_once(__DIR__ . '/cassession2.class.php');


class stack_cas_healthcheck {
    /* This variable holds the state of the healthcheck. */
    protected $ishealthy = true;

    protected $config = null;

    protected $tests = [];

    public function __construct($config) {
        global $CFG;
        $this->config = $config;

        // Record the platform in the summary.
        $test = [];
        $test['tag'] = 'platform';
        $test['result'] = null;
        $test['summary'] = $config->platform;
        $test['details'] = null;
        $this->tests[] = $test;

        // Check if the current options for library packages are permitted (maximalibraries).
        list($result, $message, $livetestcases) = stack_cas_configuration::validate_maximalibraries();
        // The livetestcases are used below, once we have a live maxima or image ready to test.
        if (!$result) {
            $this->ishealthy = false;
            $test = [];
            $test['tag'] = 'settingmaximalibraries';
            $test['result'] = $result;
            $test['summary'] = $message;
            $test['details'] = html_writer::tag('p', $message);
            $test['details'] .= html_writer::tag('p', stack_string('settingmaximalibraries_failed'));
            $test['details'] .= html_writer::tag('p', stack_string('settingmaximalibraries_desc'));
            $this->tests[] = $test;
        }

        // Try to connect to create maxima local.
        stack_cas_configuration::create_maximalocal();

        // Make sure we are in a position to call maxima.
        switch ($config->platform) {
            case 'win':
                $maximalocation = stack_cas_configuration::confirm_maxima_win_location();
                if ('' != $maximalocation) {
                    $test = [];
                    $test['tag'] = 'stackmaximalibraries';
                    $test['result'] = null;
                    $test['summary'] = null;
                    $test['details'] = html_writer::tag('p', stack_string('healthcheckconfigintro1').' '.
                        html_writer::tag('tt', $maximalocation));
                    $this->tests[] = $test;
                } else {
                    $this->ishealthy = false;
                    $test = [];
                    $test['result'] = false;
                    $test['summary'] = "Could not confirm the location of Maxima";
                    $this->tests[] = $test;
                }

                stack_cas_configuration::copy_maxima_bat();

                if (!is_readable($CFG->dataroot . '/stack/maxima.bat')) {
                    $this->ishealthy = false;
                    $test = [];
                    $test['tag'] = 'healthcheckmaximabat';
                    $test['result'] = false;
                    $test['summary'] = stack_string('healthcheckmaximabatinfo', $CFG->dataroot);
                    $test['details'] = html_writer::tag('p', stack_string('healthcheckmaximabatinfo', $CFG->dataroot));
                    $this->tests[] = $test;
                }

                break;
            case 'linux':
                // On a raw linux server list the versions of Maxima available.
                $connection = stack_connection_helper::make();
                $test = [];
                $test['tag'] = 'healthcheckmaximaavailable';
                $test['result'] = null;
                $test['summary'] = null;
                $test['details'] = html_writer::tag('pre', $connection->get_maxima_available());
                $this->tests[] = $test;
                break;
            case 'server':
                if (!empty($CFG->proxyhost) && !is_proxybypass(get_config('qtype_stack', 'maximacommandserver'))) {
                    $test = [];
                    $test['tag'] = 'healthcheckproxysettings';
                    $test['result'] = null;
                    $test['summary'] = stack_string('healthcheckproxysettings');
                    $this->tests[] = $test;
                    break;
                }
            default:
                // Server-proxy/optimised.
                // TO-DO: add in any specific tests for these setups?
                break;
        }

        // Record the contents of the maximalocal file.
        if ($this->ishealthy) {
            $test = [];
            $test['tag'] = 'healthcheckmaximalocal';
            $test['result'] = null;
            $test['summary'] = null;
            $test['details'] = html_writer::tag('textarea', stack_cas_configuration::generate_maximalocal_contents(),
                ['readonly' => 'readonly', 'wrap' => 'virtual', 'rows' => '32', 'cols' => '100']);
            $this->tests[] = $test;
        }

        // Test an *uncached* call to the CAS.  I.e. a genuine call to the process.
        if ($this->ishealthy) {
            list($message, $genuinedebug, $result) = stack_connection_helper::stackmaxima_genuine_connect();
            $this->ishealthy = $result;

            $test = [];
            $test['tag'] = 'healthuncached';
            $test['result'] = $result;
            $test['summary'] = $message;
            $test['details'] = html_writer::tag('p', stack_string('healthuncachedintro')) . $message;
            $test['details'] .= $genuinedebug;
            $this->tests[] = $test;
        }

        // Test Maxima connection.
        if ($this->ishealthy) {
            // Intentionally use get_string for the sample CAS and plots, so we don't render
            // the maths too soon.
            $this->output_cas_text('healthcheckconnect',
                stack_string('healthcheckconnectintro'), get_string('healthchecksamplecas', 'qtype_stack'));
            $this->output_cas_text('healthcheckconnectunicode',
                stack_string('healthcheckconnectintro'), get_string('healthchecksamplecasunicode', 'qtype_stack'));
            $this->output_cas_text('healthcheckplots',
                stack_string('healthcheckplotsintro'), get_string('healthchecksampleplots', 'qtype_stack'));
            $this->output_cas_text('healthcheckjsxgraph',
                stack_string('healthcheckjsxgraphintro'), get_string('healthcheckjsxgraphsample', 'qtype_stack'), true);
            $this->output_cas_text('healthcheckparsons',
                stack_string('healthcheckparsonsintro'), get_string('healthcheckparsonssample', 'qtype_stack'), true);
            $this->output_cas_text('healthcheckgeogebra',
                stack_string('healthcheckgeogebraintro'), get_string('healthcheckgeogebrasample', 'qtype_stack'), true);
        }

        // If we have a linux machine, and we are testing the raw connection then we should
        // attempt to automatically create an optimized maxima image on the system.
        if ($this->ishealthy && $config->platform === 'linux') {
            list($message, $debug, $result, $commandline, $rawcommand)
                = stack_connection_helper::stackmaxima_auto_maxima_optimise($genuinedebug);
            $test = [];
            $test['tag'] = 'healthautomaxopt';
            $test['result'] = $result;
            $test['summary'] = $message;
            $test['details'] = html_writer::tag('p', stack_string('healthautomaxoptintro'));
            $test['details'] .= html_writer::tag('pre', $debug);
            $this->tests[] = $test;
        }

        if ($this->ishealthy) {
            list($message, $details, $result) = stack_connection_helper::stackmaxima_version_healthcheck();
            $test = [];
            $test['tag'] = 'healthchecksstackmaximaversion';
            $test['result'] = $result;
            $test['summary'] = stack_string($message, $details);
            $test['details'] = stack_string($message, $details);
            $this->tests[] = $test;
        }

        // Check that each library really is loaded into the current connection.
        if ($this->ishealthy) {
            // At this point everything _should_ be working so we use a regular session connection.
            $s = [];
            foreach ($livetestcases as $lib => $test) {
                $s[$lib] = stack_ast_container::make_from_teacher_source($test, 'test_library', new stack_cas_security());
            }
            $session = new stack_cas_session2($s);
            if ($session->get_valid()) {
                $session->instantiate();
            }

            $result = true;
            $message = 'healthchecksstacklibrariesworkingok';
            $details = '';
            if ($session->is_instantiated()) {
                $failed = [];
                foreach ($livetestcases as $lib => $test) {
                    // We assume the maxima expression testing each library must return true if and only if it works.
                    if ($s[$lib]->get_value() != 'true') {
                        $failed[] = $lib;
                    }
                }
                if ($failed != []) {
                    $this->ishealthy = false;
                    $result = false;
                    $message = 'healthchecksstacklibrariesworkingfailed';
                    $details = ['err' => implode(', ', $failed)];
                }
            } else {
                $this->ishealthy = false;
                $result = false;
                $message = 'healthchecksstacklibrariesworkingsession';
                $details = ['err' => $session->get_errors(true)];
            }

            $test = [];
            $test['tag'] = 'healthchecksstacklibrariesworking';
            $test['result'] = $result;
            $test['summary'] = stack_string($message, $details);
            $test['details'] = stack_string($message, $details);
            $this->tests[] = $test;
        }
        // List the requested maxima packages in the summary.
        $test = [];
        $test['tag'] = 'settingmaximalibraries';
        $test['result'] = null;
        $test['summary'] = $config->maximalibraries;
        $test['details'] = null;
        $this->tests[] = $test;

        // Record whether caching is taking place in the summary.
        $test = [];
        $test['tag'] = 'settingcasresultscache';
        $test['result'] = null;
        $test['summary'] = stack_string('healthcheckcache_' . $config->casresultscache);
        $test['details'] = null;
        $this->tests[] = $test;
    }

    /*
     * Try and evaluate the raw castext and build a result entry.
     *
     * $hideraw is for those cases where we do not wish to show the raw CASText.
     */
    private function output_cas_text($title, $intro, $castext, $hideraw=false) {
        $ct = castext2_evaluatable::make_from_source($castext, 'healthcheck');
        $session = new stack_cas_session2([$ct]);
        $session->instantiate();

        $test = [];
        $test['tag'] = $title;
        $test['result'] = null;
        $test['summary'] = null;
        if ($hideraw) {
            $test['details'] = html_writer::tag('p', $intro);
        } else {
            $test['details'] = html_writer::tag('p', $intro) . html_writer::tag('pre', s($castext));
        }

        if ($session->get_errors()) {
            $this->ishealthy = false;
            $test['result'] = false;
            $test['summary'] = stack_string('errors') . $ct->get_errors();
            $test['details'] .= stack_string('errors') . $ct->get_errors();
            $test['details'] .= stack_string('debuginfo') . $session->get_debuginfo();
        } else {
            // This content is goind to a secure output where we do not do the two phase
            // handlign the holder would want.
            $test['details'] .= html_writer::tag('p', stack_ouput_castext($ct->apply_placeholder_holder($ct->get_rendered())));
        }
        $this->tests[] = $test;
    }

    /*
     * This function returns a summary of the status of the healthcheck.
     */
    public function get_test_results() {
        return $this->tests;
    }

    /*
     * Return overall results.
     */
    public function get_overall_result() {
        return $this->ishealthy;
    }
}

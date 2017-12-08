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
 * This script tests locale and encoding related environment settings of
 * the Maxima connectors and ensures that the umlauts are correctly mixed
 * with the runes.
 *
 * @copyright  2012 University of Birmingham
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__.'/../../../config.php');
require_once($CFG->dirroot .'/course/lib.php');
require_once($CFG->libdir .'/filelib.php');

require_once(__DIR__ . '/locallib.php');
require_once(__DIR__ . '/stack/utils.class.php');
require_once(__DIR__ . '/stack/options.class.php');
require_once(__DIR__ . '/stack/cas/castext.class.php');
require_once(__DIR__ . '/stack/cas/casstring.class.php');
require_once(__DIR__ . '/stack/cas/cassession.class.php');
require_once(__DIR__ . '/stack/cas/connector.dbcache.class.php');
require_once(__DIR__ . '/stack/cas/installhelper.class.php');


// Check permissions.
require_login();
$context = context_system::instance();
require_capability('moodle/site:config', $context);

// Set up page.
$PAGE->set_context($context);
$PAGE->set_url('/question/type/stack/localecheck.php');
$title = 'Encoding check';
$PAGE->set_title($title);

// Clear the cache if requested. Cache might contain wrongly decoded values.
if (data_submitted() && optional_param('clearcache', false, PARAM_BOOL)) {
    require_sesskey();
    stack_cas_connection_db_cache::clear_cache($DB);
    redirect($PAGE->url);
}

// Create and store Maxima image if requested. When locale/encoding settings change.
if (data_submitted() && optional_param('createmaximaimage', false, PARAM_BOOL)) {
    require_sesskey();
    stack_cas_connection_db_cache::clear_cache($DB);
    stack_cas_configuration::create_auto_maxima_image();
    redirect($PAGE->url);
}

$config = stack_utils::get_config();

// Start output.
echo $OUTPUT->header();
echo $OUTPUT->heading($title);

echo html_writer::tag('p', 'Generally, we assume that the Maxima has been configured to work with UTF-8 encoded strings, but as most environments default to the C-locale things do not often work as expected. To ensure that your Maxima processes are correctly configured this script tries to send various values to Maxima and tries to diagnose possible issues from what it receives.');

echo html_writer::tag('p', 'If you happen to only work with the modern English language and/or do not deal with strings in your STACK questions you can probably ignore the whole issue. But if you need to for example construct custom labels for some MCQ inputs and your language is not a subset of ASCII or LATIN-1 you may need to deal with this.');

echo html_writer::tag('p', 'Typical issues related to incorrect environment and locale settings are for example inability to present certain characters leading to mysterious question marks and incorrect values for slength() calls. These will be tested here.');

echo html_writer::tag('p', 'Note that this testing relies on features not present in older Maxima versions, 5.38 branch seems to be the first with them. Also some older Maxima and LISP combinations may not be able to handle UTF-8 so getting them to work may prove to be impossible.');



// Construct a test string.
$teststring = '"ᚦe úmlãũtŝ are nöt å problem for 堆"';
$cs1 = new stack_cas_casstring('s:' . $teststring);
$cs1->get_valid('s');
// That same string as octets on a working system.
$cs2 = new stack_cas_casstring('so:[225,154,166,101,32,195,186,109,108,195,163,197,169,116,197,157,32,97,114,101,32,110,195,182,116,32,195,165,32,112,114,111,98,108,101,109,32,102,111,114,32,229,160,134]');
$cs2->get_valid('s');
// That string has this many chars on a corectly LC_CTYPE configured system.
$slength = 34;

echo html_writer::tag('p', 'First we test a string with non ASCII and even multibyte characters and its behaviour in round trip transmission. Should encoding/decoding fail expect to see question marks but do not worry if the first or the last characters are presented to you as missing character boxes.');


// Basic tests.
$cs3 = new stack_cas_casstring('a:string_to_octets(s)');
$cs3->get_valid('t');
$cs4 = new stack_cas_casstring('b:octets_to_string(so)');
$cs4->get_valid('t');
$cs5 = new stack_cas_casstring('c:slength(s)');
$cs5->get_valid('t');
$cs6 = new stack_cas_casstring('d:slength(b)');
$cs6->get_valid('t');

// Make it a session.
$cs = new stack_cas_session(array($cs1, $cs2, $cs3, $cs4, $cs5, $cs6), null, 0);
$cs->instantiate();

// Extract the values for direct access.
$evs = $cs->get_value_key('s');
$evso = $cs->get_value_key('so');
$eva = $cs->get_value_key('a');
$evb = $cs->get_value_key('b');
$evc = $cs->get_value_key('c');
$evd = $cs->get_value_key('d');

$score = 4;

$list = '';
if ($evs == $teststring) {
    $list .= html_writer::tag('li', 'The string ' . $teststring . ' returned back from Maxima as it was sent. Which is good but probably not enough.');
} else {
    $score = $score - 1;
    $list .= html_writer::tag('li', 'The string ' . $teststring . ' returned back from Maxima as ' . $evs . ' which is a problem.', array('class' => 'error'));
}

if ($evso == $eva) {
    $list .= html_writer::tag('li', 'Maximas interpretation of the bytes defining the string match expected. Which is good.', array('class' => 'ok'));
} else {
    $score = $score - 1;
    $list .= html_writer::tag('li', 'Maximas interpretation of the bytes defining the string is different from the expected one. Which is bad.', array('class' => 'error'));
}


if ($evs == $evb) {
    $list .= html_writer::tag('li', 'Constructing the same string from byte values on the Maxima side generates the same output, which means that we atleast did not lose any bits when we sent values to Maxima.', array('class' => 'ok'));
} else {
    $score = $score - 1;
    $list .= html_writer::tag('li', 'Constructing the same string from byte values on the Maxima side did not generate the same output, which means that some bits were probably lost when sending values to Maxima or things were interpreted differently and outputting values broke things down.', array('class' => 'error'));
}

if ($evc == $slength) {
    $list .= html_writer::tag('li', 'The strings length was correctly interpreted, this is a good sign.', array('class' => 'ok'));
} else {
    $score = $score - 1;
    $list .= html_writer::tag('li', 'The strings length is ' . $slength . ' characters however Maxima interpreted it to be ' . $evc  . ' characters long. This is probably due to CTYPE not being configured correctly.', array('class' => 'error'));
}

echo html_writer::tag('ul', $list);

if ($score == 4) {
    echo $OUTPUT->heading('Things seem to be good');
} else {
    echo $OUTPUT->heading('Some things are broken, maybe just the tests though');

    echo html_writer::tag('p', 'You may want to copy paste this test code directly to a Maxima to see how things work. Typically, you want to run Maxima directly as an user and try this code and if it works then you want to copy the environment variables used as that user, typically web servers have different environment variables which is the root cause of this problem.');

    $test = 's:' . $teststring . '$' . "\n"
        . $cs2->get_raw_casstring() . '$' . "\n"
        . 'if is(string_to_octets(s) = so) then print("OK   input encoding") else print("FAIL input encoding, or maybe you have an old Maxima")$' . "\n"
        . 'if is(slength(s) = ' . $slength . ') then print("OK   string length") else print("FAIL string length")$' . "\n";

    echo html_writer::tag('textarea', $test,
        array('readonly' => 'readonly', 'wrap' => 'virtual', 'rows' => '6', 'cols' => '100'));

    echo html_writer::tag('p', 'Simplest way to find the environment variables is to run \'system("env")\' (or \'system("set")\' on Windows) inside that Maxima. The variables of interest are typically LANG and LC_* but there may be others defining CHARSETs.');

    if ($config->platform == 'server') {
        echo html_writer::tag('p', 'You have configures STACK to connect to a MaximaPool server, this meas that there is little to be done on STACK side to fix these issues. Everything needs to be configured on the MaximaPool side.');

        $list = '';
        $list .= html_writer::tag('li', 'First ensure that your MaximaPool is current atleast a version from late Novemeber 2017.');
        $list .= html_writer::tag('li', 'Ensure that the Java servlet container is running with "-Dfile.encoding=UTF-8" in the Java options of the command line starting the servlet container (probably Tomcat). Some configurations default to ASCII encoding.');
        $list .= html_writer::tag('li', 'If that does not fix things try to copy environment variables from somewhere where things work to the process.conf of your MaximaPool.');
        $list .= html_writer::tag('li', 'Note that you can run commands including that test inside the MaximaPool Maxima-process easily through the Pools web interface (' . $config->maximacommand . ')');
        $list .= html_writer::tag('li', 'Remember to clear caches and reload the Pool when changing things.');

        echo html_writer::tag('ul', $list);
    } else if ($config->platform == 'unix' || $config->platform == 'unix-optimised') {
        echo html_writer::tag('p', 'You have configures STACK to run Maxima on the same *NIX machine as the Moodle is running. Basically, we just need to setup the environment variables STACK uses to start Maxima, the problem is that normally Moodle runs on a web server connected account that has no default environment that would make sense. Lets do some guessing, if we cannot create a sensible solution please let the developers know the values our guessess generated.');

        echo html_writer::tag('p', 'First lets find out what "locale" and "locale -a" say:');

        $cmd = 'locale';
        if (is_executable('/bin/locale')) {
            $cmd = '/bin/locale';
        } else if (is_executable('/usr/bin/locale')) {
            $cmd = '/usr/bin/locale';
        }

        $cwd = null;
        $newpath = getenv('PATH');
        // Here you probably wonder why we do not read the vars with getenv...
        // Well do we really know what vars exist for each distribution/platform?
        // Maybe the locale command knows better.
        // And someone might add more vars to proc_open through some other way...
        $env = array('PATH' => $newpath);

        $descriptors = array(
            0 => array('pipe', 'r'),
            1 => array('pipe', 'w'),
            2 => array('pipe', 'w'));
        $process = proc_open($cmd, $descriptors, $pipes, $cwd, $env);

        if (!stream_set_blocking($pipes[1], false)) {
            $this->debug->log('', 'Warning: could not stream_set_blocking to be FALSE on the CAS process.');
        }
        $ret = '';
        $starttime = microtime(true);
        $continue = true;
        while ($continue and !feof($pipes[1])) {
            $now = microtime(true);

            if (($now - $starttime) > 1000) {
                $procarray = proc_get_status($process);
                if ($procarray['running']) {
                    proc_terminate($process);
                }
                $continue = false;
            } else {
                $out = fread($pipes[1], 1024);
                if ('' == $out) {
                    // Pause.
                    usleep(1000);
                }
                $ret .= $out;
            }
        }
        if ($continue) {
            fclose($pipes[0]);
            fclose($pipes[1]);
        }
        $locale = $ret;

        $cmd = $cmd . ' -a';
        $process = proc_open($cmd, $descriptors, $pipes, $cwd, $env);

        if (!stream_set_blocking($pipes[1], false)) {
            $this->debug->log('', 'Warning: could not stream_set_blocking to be FALSE on the CAS process.');
        }
        $ret = '';
        $starttime = microtime(true);
        $continue = true;
        while ($continue and !feof($pipes[1])) {
            $now = microtime(true);

            if (($now - $starttime) > 1000) {
                $procarray = proc_get_status($process);
                if ($procarray['running']) {
                    proc_terminate($process);
                }
                $continue = false;
            } else {
                $out = fread($pipes[1], 1024);
                if ('' == $out) {
                    // Pause.
                    usleep(1000);
                }
                $ret .= $out;
            }
        }
        if ($continue) {
            fclose($pipes[0]);
            fclose($pipes[1]);
        }

        $localea = $ret;

        echo html_writer::tag('textarea', '> locale' . "\n" . $locale . "\n > locale -a\n" . $localea,
        array('readonly' => 'readonly', 'wrap' => 'virtual', 'rows' => '20', 'cols' => '100'));

        $list = '';
        echo html_writer::tag('p', 'Assuming that those evaluated we can say the following things:');

        $alocale = explode("\n", $locale);
        $alocalea = explode("\n", $localea);
        $foundctype = false;
        foreach ($alocale as $loc) {
            if (explode('=', $loc)[0] == 'LC_CTYPE') {
                $foundctype  = true;
                if (strpos(strtolower(explode('=', $loc)[1]),'utf8') !== false || strpos(strtolower(explode('=', $loc)[1]),'utf-8') !== false) {
                    $list .= html_writer::tag('li', 'LC_CTYPE has been defined and it seems to have "UTF-8" in its name, hopefully it is functioning.');
                } else {
                    $list .= html_writer::tag('li', 'LC_CTYPE has been defined but it seems to not have "UTF-8" in its name. Maybe it supports it anyway but it could make sense to try some of the ones in the "locale -a" listing.');
                }
            }
        }
        if (!$foundctype) {
            $list .= html_writer::tag('li', 'LC_CTYPE has not been defined, you should select a sensible type with "UTF-8" from the "locale -a" listing, or if there are none find out a way to add such to your system.');
        }
        $localecandidate = false;
        $foundlocale = false;
        foreach ($alocalea as $loc) {
            if (strpos(strtolower($loc),'utf8') !== false || strpos(strtolower($loc),'utf-8') !== false) {
                if (!$foundlocale) {
                    $list .= html_writer::tag('li', 'The "locale -a" listing seems to contain atleast one "UTF-8" locale, hopefully it is fully initialised.');
                }
                $foundlocale = true;
                if ($localecandidate === false) {
                    $localecandidate = $loc;
                } else if (strpos(strtolower($loc),'en_us') === 0 && strpos(strtolower($localecandidate),'en_gb') !== 0) {
                    $localecandidate = $loc;
                } else if (strpos(strtolower($loc),'en_gb') === 0) {
                    $localecandidate = $loc;
                }
                // Maybe we could select the prefered locale by Moodle lang settings?
                // If we can assume that STACK in general works with other than en locales.
            }
        }
        if (!$foundlocale) {
            $list .= html_writer::tag('li', 'The "locale -a" contains no "UTF-8" locales maybe your machine has none or thay have been named in an interesting way. You will probably need to do some work to aquire them.');
        }

        echo html_writer::tag('ul', $list);

        echo html_writer::tag('p', 'Based on those outputs we would probably set the environment as follows (in addition to whatever else there is):');

        $env = 'LC_CTYPE=' . $localecandidate . ',LANG=' . $localecandidate;
        if ($localecandidate === false) {
            $env = 'LC_CTYPE=UTF-8';
        } else if (strpos($localecandidate,',') !== false) {
            $env = 'LC_CTYPE="' . $localecandidate . '",LANG="' . $localecandidate . '"';
        }


        echo html_writer::tag('textarea', $env,
        array('readonly' => 'readonly', 'wrap' => 'virtual', 'rows' => '2', 'cols' => '100'));

        if (strpos($config->maximaenvironment, $env) !== false) {
            echo html_writer::tag('p', 'But as the setting already includes those more will need to be added.');
        }

    }
}

echo '<hr/>';

// State of the cache.
echo $OUTPUT->heading(stack_string('settingcasresultscache'), 3);
$message = stack_string('healthcheckcache_' . $config->casresultscache);
$summary[] = array(null, $message);
echo html_writer::tag('p', $message);
if ('db' == $config->casresultscache) {
    echo html_writer::tag('p', stack_string('healthcheckcachestatus',
            stack_cas_connection_db_cache::entries_count($DB)));
    echo $OUTPUT->single_button(
            new moodle_url($PAGE->url, array('clearcache' => 1, 'sesskey' => sesskey())),
            stack_string('clearthecache'));
}

// Option to auto-create the Maxima image and store the results.
if ($config->platform != 'win') {
    echo $OUTPUT->single_button(
        new moodle_url($PAGE->url, array('createmaximaimage' => 1, 'sesskey' => sesskey())),
        stack_string('healthcheckcreateimage'));
}


echo $OUTPUT->footer();

function output_cas_text($title, $intro, $castext) {
    global $OUTPUT;

    echo $OUTPUT->heading($title, 3);
    echo html_writer::tag('p', $intro);
    echo html_writer::tag('pre', s($castext));

    $ct = new stack_cas_text($castext, null, 0, 't');

    echo html_writer::tag('p', stack_ouput_castext($ct->get_display_castext()));
    echo output_debug(stack_string('errors'), $ct->get_errors());
    echo output_debug(stack_string('debuginfo'), $ct->get_debuginfo());
}


function output_debug($title, $message) {
    global $OUTPUT;

    if (!$message) {
        return;
    }

    return $OUTPUT->box($OUTPUT->heading($title) . $message);
}

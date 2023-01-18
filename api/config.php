<?php
/**
 * Simulating Moodle global configuration variables.
 */

$CFG = new stdClass;
$PAGE = new stdClass;

// This is the directory into which you put the scripts.
$CFG->wwwroot = "/var/www/html";
// The base url of the installation
// The server path of the installation.
$CFG->dirroot = realpath(dirname(__FILE__));
// You must have a data directory into which the webserver can write.  Don't put this in your web directory.
$CFG->dataroot = "/var/data/api";

// URL of your web server, e.g.
$CFG->dataurl = "http://localhost/";

$CFG->maximacommand = 'maxima';
$CFG->maximaversion = '5.44.0';
// Once you have compiled maxima you will need to change this.
$CFG->platform            = 'server';
//$CFG->platform            = 'linux-optimised';
$CFG->maximacommandopt    = 'timeout --kill-after=10s 10s ' . $CFG->dataroot . '/stack/maxima_opt_auto';
$CFG->maximacommandserver = 'http://maxima:8080/maxima';
/*
 * These settings are hard-wired here.  See settings.php for more details.
 * You probably don't need to change many of the following.
 */
$CFG->maximalocalfolder = $CFG->dataroot . 'maxima/';

// Type (int).
$CFG->castimeout = 10;
$CFG->casdebugging = 1;
$CFG->casresultscache = 'none';
$CFG->maximalibraries = '';
$CFG->serveruserpass = '';
//$CFG->

// Do not change this from zero.  The API has no parser cache.
$CFG->parsercacheinputlength = 0;

// Tells the code you are not using this as part of Moodle.
$CFG->useaspartof = 'api';

$CFG->caspreparse = 'true';
$CFG->plotcommand = "gnuplot";
$CFG->ajaxvalidation = 0;
$CFG->replacedollars = false;

$CFG->questionsimplify = 1;
$CFG->assumepositive = 0;
$CFG->assumereal = 0;
$CFG->prtcorrect = '';
$CFG->prtpartiallycorrect = '';
$CFG->prtincorrect = '';
$CFG->multiplicationsign = 'dot';
$CFG->sqrtsign = 1;
$CFG->complexno = 'i;';
$CFG->inversetrig = 'cos-1';
$CFG->matrixparens = "[";

$CFG->inputtype = 'algebraic';
$CFG->inputboxsize = 15;
$CFG->inputstrictsyntax = 1;
$CFG->inputinsertstars = 0;
$CFG->inputforbidwords = '';
$CFG->inputforbidfloat = 0;
$CFG->inputrequirelowestterms = 1;
$CFG->inputcheckanswertype = 1;
$CFG->inputmustverify = 1;
$CFG->inputshowvalidation = 1;

$CFG->stackmaximaversion = "2022060100";
$CFG->version = "2022060100";

// Do not change this setting.
$CFG->mathsdisplay = 'api';

$CFG->libdir = $CFG->dirroot . '/emulation/libdir';

$GLOBALS['CFG'] =& $CFG;

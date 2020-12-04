# Unit tests

Unit testing for STACK comes in the following three parts.

* PHP Unit tests,
* Maxima unit tests,
* Test scripts exposed to the question author.

These three mechanisms aim to provide comprehensive testing of STACK.  The last category are a compromise, and are designed to expose the results of unit tests to question authors in a reasonably attractive manner to inform them of what each answer test is actually supposed to do.  Links to these tests are in the healthcheck page.

STACK uses the Travis continuous integration mechanism so that all unit tests are triggered when a commit is pushed to GitHub.
See [https://travis-ci.org/maths/moodle-qtype_stack](https://travis-ci.org/maths/moodle-qtype_stack).

# PHP Unit tests

Moodle uses PHPUnit for its unit tests. Setting this up and getting it working
is a bit of a pain, but you only have to follow the instructions in
[the Moodle PHPUnit documentation](http://docs.moodle.org/dev/PHPUnit) once to get it working.

**NOTE: do not use linux-optimised when running the unit tests.** The STACK installation must be set to `linux` (or `win` of course).

## STACK-specific set-up steps ##

Once you have executed

    php admin/tool/phpunit/cli/init.php

you need to edit the config.php file to add the following configuration
information near the end, but before the `require_once(dirname(__FILE__) . '/lib/setup.php');`.
Other options for the platform are `linux` and `linux-optimised`.

    define('QTYPE_STACK_TEST_CONFIG_PLATFORM',        'linux');
    /* It is essential that the MAXIMAVERSION and MAXIMACOMMAND match.
       That is, you must check that the command executed here really loads
       the version specified in MAXIMAVERSION.  Some unit tests are version
       dependent.  Do not use default.  */
    define('QTYPE_STACK_TEST_CONFIG_MAXIMAVERSION',   '5.42.0');
    define('QTYPE_STACK_TEST_CONFIG_MAXIMACOMMAND',   'maxima --use-version=5.42.0');
    define('QTYPE_STACK_TEST_CONFIG_MAXIMACOMMANDOPT',   '');
    define('QTYPE_STACK_TEST_CONFIG_MAXIMACOMMANDSERVER',   'http://pool.home:8080/MaximaPool/MaximaPool');
    define('QTYPE_STACK_TEST_CONFIG_CASTIMEOUT',      '5');
    define('QTYPE_STACK_TEST_CONFIG_MAXIMALIBRARIES', 'stats, distrib, descriptive, simplex');
    define('QTYPE_STACK_TEST_CONFIG_CASDEBUGGING',    '0');
    define('QTYPE_STACK_TEST_CONFIG_PLOTCOMMAND',     '');

    define('QTYPE_STACK_TEST_CONFIG_CASRESULTSCACHE', 'db');

You should probably copy the settings from Admin -> Plugins -> Question types -> STACK.
However, you can use the flexibility to have different configurations of STACK
for testing in order to test a new release of Maxima, for example.

If you want to run just the unit tests for STACK, you can use the command

    vendor\bin\phpunit --group qtype_stack
    vendor/bin/phpunit --group qtype_stack

To make sure this keeps working, please annotate all test classes with

    /**
     * @group qtype_stack
     */

## Stop resetting the dataroot directory.


In `[...]/moodle/lib/phpunit/classes/util.php` 

    public static function reset_all_data() {

Comment out the line (currently 253).

    self::reset_dataroot();

This stops the unit tests from deleting the Maxima image files at each step.

## Making the tests faster ##

The tests will be very slow, because the Moodle PHPUnit integration keeps resetting
the database state between each test, so you get no benefit from the cache. To
get around that problem, you can use the option to connect to a different database
server for the cache. Modify the following to suit your system and put this near the end of your config.php file:

Note you need to make sure the `QTYPE_STACK_TEST_CONFIG_CASRESULTSCACHE` variable is only defined once.

    define('QTYPE_STACK_TEST_CONFIG_CASRESULTSCACHE',   'otherdb');
    define('QTYPE_STACK_TEST_CONFIG_CASCACHEDBTYPE',    $CFG->dbtype);
    define('QTYPE_STACK_TEST_CONFIG_CASCACHEDBLIBRARY', $CFG->dblibrary);
    define('QTYPE_STACK_TEST_CONFIG_CASCACHEDBHOST',    $CFG->dbhost);
    define('QTYPE_STACK_TEST_CONFIG_CASCACHEDBNAME',    $CFG->dbname);
    define('QTYPE_STACK_TEST_CONFIG_CASCACHEDBUSER',    $CFG->dbuser);
    define('QTYPE_STACK_TEST_CONFIG_CASCACHEDBPASS',    $CFG->dbpass);
    define('QTYPE_STACK_TEST_CONFIG_CASCACHEDBPREFIX',  $CFG->prefix);

To make sure the CAS cache is cleared after each unit test, revert back to the `db` settings for `QTYPE_STACK_TEST_CONFIG_CASRESULTSCACHE` as described above.  This will be slow...

# Other configuration issues

Moodle overrides the PHP debug message settings.  To see errors and warnings, go to

    Site administration -> Development -> Debugging

and set the Debug messages option.

# Maxima unit tests

Maxima has a unit testing framework called "rtest".  One complication is that we need to run tests with and without [simplification](../CAS/Simplification.md).  To help with this, a batch file is provided to run the unit tests.

    \moodle\question\type\stack\stack\maxima\unittests_load.mac
    
To run this set up the [STACK-maxima-sandbox](../CAS/STACK-Maxima_sandbox.md) and load STACK's libraries.  Then type

    load("unittests_load.mac");

The output from these tests is written to `.ERR` files in `\moodle\question\type\stack\stack\maxima\`.
    
Please note that currently, with simplification false, there are a number of false negative results.  That is tests appear to fail, but do not.  This is because rtest is not designed to run with simp:false, and so does not correctly decide whether things are really the "same" or "different".

# Timing the code.

Maxima has a range of functions for code profiling.  Put the following at the start of the file.

    timer(all)$

This adds all user-defined functions to the timer list.  

To time a single command

    ev(timer_info(abs_replace), simp);

To profile all user-defined commands execute.

    simp:true$
    T:timer_info()$

Find those commands actually called (based on T being the matrix above).

    S:sublist(rest(args(T)),lambda([a], not(is(third(a)=0))));

Sort by functions called most often.

    S:sort(S, lambda([a,b],third(a)>third(b)));

Sort by the time/call

    float_time(a):= if a=0 then 0 else first(args(a))$
    S:sort(S, lambda([a,b],float_time(second(a))>float_time(second(b))));
    
# Testing ajax specific problems.

You need to output values to the file system, as the display can't manage this.  For example,

    file_put_contents("/tmp/log.txt", print_r($result, true));

# Testing the updated parser in STACK 4.3

In the STACK directory

    php cli/casstringtester.php --string="0..1"

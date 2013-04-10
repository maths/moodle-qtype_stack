# Unit tests

Moodle 2.3 uses PHPunit for its unit tests. Setting this up and getting it working
is a bit of a pain, but you only have to follow the instructions in
[the Moodle PHPUnit documentation](http://docs.moodle.org/dev/PHPUnit) once to get it working.

## STACK-specific set-up steps ##

Once you have executed

    php admin/tool/phpunit/cli/init.php

you need to edit the config.php file to add the following configuration
information near the end, but before the require_once(dirname(__FILE__) . '/lib/setup.php');:

    define('QTYPE_STACK_TEST_CONFIG_PLATFORM',        'win');
    define('QTYPE_STACK_TEST_CONFIG_MAXIMAVERSION',   '5.28.0');
    define('QTYPE_STACK_TEST_CONFIG_CASTIMEOUT',      '1');
    define('QTYPE_STACK_TEST_CONFIG_CASRESULTSCACHE', 'db');
    define('QTYPE_STACK_TEST_CONFIG_MAXIMACOMMAND',   '');
    define('QTYPE_STACK_TEST_CONFIG_PLOTCOMMAND',     '');
    define('QTYPE_STACK_TEST_CONFIG_CASDEBUGGING',    '0');

You should probably copy the settings from Admin -> Plugins -> Question types -> STACK.
however, you can use the flexibilty to have different configurations of STACK
for testing in order to test a new release of Maxima, for example.

You also need to edit the line in phpunit.xml that says

        <testsuite name="qtype_stack test suite">
            <directory suffix="_test.php">question/type/stack/tests</directory>
        </testsuite>

to instead say

        <testsuite name="qtype_stack test suite">
            <directory suffix="_test.php">question/type/stack</directory>
        </testsuite>

(That is, delete "/tests".)

If you want to run just the unit tests for STACK, you can use the command

    phpunit --group qtype_stack

To make sure this keeps working, please annotate all test classes with

    /**
     * @group qtype_stack
     */

## Making the tests faster ##

The tests will be very slow, because the Moodle PHPUnit integration keeps resetting
the database state between each test, so you get no benefit from the cache. To
get round that problem, you an use the option to connect to a different database
server for the cache. Put something like this near the end of your config.php file:

    define('QTYPE_STACK_TEST_CONFIG_CASRESULTSCACHE',   'otherdb');
    define('QTYPE_STACK_TEST_CONFIG_CASCACHEDBTYPE',    $CFG->dbtype);
    define('QTYPE_STACK_TEST_CONFIG_CASCACHEDBLIBRARY', $CFG->dblibrary);
    define('QTYPE_STACK_TEST_CONFIG_CASCACHEDBHOST',    $CFG->dbhost);
    define('QTYPE_STACK_TEST_CONFIG_CASCACHEDBNAME',    $CFG->dbname);
    define('QTYPE_STACK_TEST_CONFIG_CASCACHEDBUSER',    $CFG->dbuser);
    define('QTYPE_STACK_TEST_CONFIG_CASCACHEDBPASS',    $CFG->dbpass);
    define('QTYPE_STACK_TEST_CONFIG_CASCACHEDBPREFIX',  $CFG->prefix);

# Other configuration issues

Moodle overides the PHP debug message settings.  To see errors and warnings, go to

    Site administration -> Development -> Debugging

and set the Debug messages option.

# Resetting the database

Subvert one of the functions in `C:\xampp\htdocs\moodle\lib\phpunit\classes\util.php` 

    public static function reset_database() {
    public static function reset_all_data() {

By adding the following line at the beginning of the function.

    return false;

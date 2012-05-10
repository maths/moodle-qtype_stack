# Unit tests

Moodle 2.3 uses PHPunit for its unit tests. Setting this up and getting it working
is a bit of a pain, but you only have to follow the instructions in
[the Moodle PHPUnit documentation](http://docs.moodle.org/dev/PHPUnit) once to get it working.

## STACK-specific set-up steps ##

Once you have executed

    php admin/tool/phpunit/cli/init.php

you need to edit the phpunit.xml file to add the following configuration information:

    <php>
        <const name="QTYPE_STACK_TEST_CONFIG_PLATFORM"        value="win"/>
        <const name="QTYPE_STACK_TEST_CONFIG_MAXIMAVERSION"   value="5.22.1"/>
        <const name="QTYPE_STACK_TEST_CONFIG_CASTIMEOUT"      value="1"/>
        <const name="QTYPE_STACK_TEST_CONFIG_CASRESULTSCACHE" value="db"/>
        <const name="QTYPE_STACK_TEST_CONFIG_MAXIMACOMMAND"   value=""/>
        <const name="QTYPE_STACK_TEST_CONFIG_PLOTCOMMAND"     value=""/>
        <const name="QTYPE_STACK_TEST_CONFIG_CASDEBUGGING"    value="0"/>
    </php>

You should probably copy the settings from Admin -> Plugins -> Question types -> STACK.
however, you can use the flexibilty to have different configurations of STACK
for testing in order to test a new release of Maxima, for example.

If you want to run just the unit tests for STACK, you can use the command

    phpunit --group qtype_stack

To make sure this keeps working, please annotate all test classes with

    /**
     * @group qtype_stack
     */



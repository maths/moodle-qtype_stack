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
 * Behat steps definitions for STACK.
 *
 * @package   qtype_stack
 * @category  test
 * @copyright 2014 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../../../../lib/behat/behat_base.php');

use Moodle\BehatExtension\Exception\SkippedException;

/**
 * Steps definitions related with the question bank management.
 *
 * @package   qtype_stack
 * @category  test
 * @copyright 2014 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_qtype_stack extends behat_base {

    /**
     * This step looks to see if there is information about a Maxima configuration
     * for testing in the config.php file. If there is, it sets STACK up to use
     * that. If not, it skips this scenario.
     *
     * @When /^I set up STACK using the PHPUnit configuration$/
     */
    public function isetupstackusingthephpunitconfiguration() {
        // The require_once is here, this file may be required by behat before including /config.php.
        require_once(__DIR__ . '/../fixtures/test_maxima_configuration.php');

        if (!qtype_stack_test_config::is_test_config_available()) {
            throw new SkippedException('To run the STACK tests, ' .
                    ' you must define a Maxima configuration in config.php.');
        }

        qtype_stack_test_config::setup_test_maxima_connection();
    }

    /**
     * Get the xpath for a given input element.
     * @param string $name the input name, like 'ans1'.
     * @return string the xpath expression.
     */
    protected function input_xpath($name) {
        return "//div[starts-with(@class, 'que stack')]" .
                "//*[(self::select and contains(@id, '_{$name}') or (self::input and contains(@id, '_{$name}')))]";
    }

    /**
     * Set the response for a given input.
     *
     * @param string $identifier the text of the item to drag. E.g. '2:answer'.
     * @param string $value the response to give.
     *
     * @Given /^I set the input "(?P<name>[^"]*)" to "(?P<value>[^"]*)" in the STACK question$/
     */
    public function i_set_the_part_to_in_the_combined_question($name, $value) {
        $formscontext = behat_context_helper::get('behat_forms');
        $formscontext->i_set_the_field_with_xpath_to($this->input_xpath($name), $value);
    }
}

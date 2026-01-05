@qtype @qtype_stack
Feature: Create and edit STACK metadata
  In order catalogue questions effectively
  As an teacher
  I need to create and edit metadata for STACK questions

  Background:
    Given I set up STACK using the PHPUnit configuration
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
    And the following "users" exist:
      | username | firstname |
      | teacher  | Teacher   |
    And the following "course enrolments" exist:
      | user    | course | role           |
      | teacher | C1     | editingteacher |
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype | name                                     | template               |
      | Test questions   | stack | Algebraic input                          | algebraic_input        |

  @javascript
  Scenario: Create and edit STACK metadata in Moodle ≥ 4.3
    Given the site is running Moodle version 4.3 or higher
    # Edit the question, verify the form field contents, then change some.
    When I am on the "Algebraic input" "core_question > edit" page logged in as teacher
    And I click on "View and edit full metadata" "button"
    And I should see "STACK metadata is stored as a JSON object."



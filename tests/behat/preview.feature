@qtype @qtype_stack
Feature: Test how STACK questions work using Preview in the qeustoin bank
  As a teacher
  In order to check my STACK questions will work for students
  I need to preview them

  Background:
    Given I set up STACK using the PHPUnit configuration
    And the following "users" exist:
      | username |
      | teacher  |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user    | course | role           |
      | teacher | C1     | editingteacher |
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype | name                  | template |
      | Test questions   | stack | Simple STACK question | test1    |

  @javascript
  Scenario: Check button should be hidden with deferred feedback.
    When I am on the "Simple STACK question" "core_question > preview" page logged in as teacher
    Then I should see "Find"
    And I should see "Not yet answered"
    And "check" "button" should not be visible

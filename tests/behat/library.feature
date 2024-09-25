@qtype @qtype_stack
Feature: Test STACK library
  As a teacher
  In order to use the STACK library
  I need to preview and import questions

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

  @javascript @current
  Scenario: Import a question.
    When I am on the "Course 1" "core_question > course question bank" page logged in as "teacher"
    And I click on "Create a new question" "button"
    And I click on "STACK" "text"
    And I click on "[name$='submitbutton']" "css_element"
    And I click on "STACK question library" "link"
    Then I should see "Test questions"
    And I should not see "Question variables"
    And I click on "Calculus-Refresher" "button"
    And I click on "CR_Diff_02" "button"
    And I click on "CR-Diff-02-linearity-1-b.xml" "button"
    And I should see "Differentiate \[{@p@}\] with respect to {@v@}. [[input:ans1]]"
    And I click on "Import" "button"
    And I click on "Question bank" "link"
    Then I should see "CR-Diff-02-linearity-1.b"

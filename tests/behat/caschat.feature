@qtype @qtype_stack
Feature: STACK script for interacting with the CAS.
  In order to learn Maxima code
  As an admin
  I need to use the CAS chat script.

  Background:
    Given I log in as "admin"
    And I set up STACK using the PHPUnit configuration

  @javascript
  Scenario: Navigate to the CAS chat script and evaluate something
    When I navigate to "Plugins > Question types > STACK" in site administration
    And I follow "CAS chat script"
    Then I should see "Test the connection to the CAS"

    When I set the field "cas" to "1 + 1 = {@ 1+1 @}."
    And I press "Send to the CAS"
    Then I should see "1 + 1 = 2"

    When I set the field "cas" to "[[facts:calc_int_methods_parts]]"
    And I press "Send to the CAS"
    And I should see "Integration by Parts"
    Then I should see "or alternatively:"

  @javascript
  Scenario: Save changes as new question version
    When the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype | name                  | template |
      | Test questions   | stack | Simple STACK question | test1    |
    When I am on the "Course 1" "core_question > course question bank" page logged in as "admin"

    And I choose "STACK question dashboard" action for "Simple STACK question" in the question bank
    And I follow "Send general feedback to the CAS"

    When I set the field "cas" to "1 + 1 = {@ 1+1 @}."
    And I set the field "maximavars" to "t1:3;"
    And I press "Send to edit form"
    Then I should see "Editing a STACK question"
    Then the following fields match these values:
      | General feedback                  | 1 + 1 = {@ 1+1 @}.  |
      | Question variables                | t1:3;               |

    When I press "Save changes"
    And I am on the "Course 1" "core_question > course question bank" page logged in as "admin"
    And I choose "Edit question" action for "Simple STACK question" in the question bank
    Then the following fields match these values:
      | General feedback                  | 1 + 1 = {@ 1+1 @}.  |
      | Question variables                | t1:3;               |

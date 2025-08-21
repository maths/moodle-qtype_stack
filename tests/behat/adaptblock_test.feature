@qtype @qtype_stack @_file_upload @javascript
Feature: Test running Adapt Block question.
  As a teacher
  In order to check my Adaptblock STACK questions will work for students
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
    And the following "activities" exist:
      | activity   | name   | intro                           | course | idnumber |
      | quiz       | Quiz 1 | Quiz 1 for testing the Add menu | C1     | quiz1    |
    When I am on the "Course 1" "core_question > course question bank" page logged in as "teacher"
    And I click on "Create a new question" "button"
    And I set the field "item_qtype_stack" to "1"
    And I press "submitbutton"
    And I click on "STACK question library" "link"
    Then I should see "Doc-Examples"
    And I should not see "Question variables"
    And I click on "Doc-Examples" "button"
    And I click on "Authoring-Docs" "button"
    And I click on "Question-blocks" "button"
    And I click on "Adapt_button_block.xml" "button"
    And I should see "This text will be shown until the adaptbutton has been clicked."
    And I click on "Import" "button"
    And I click on "Return to question bank" "link"
    Then I should see "Question bank"

  @javascript
  Scenario: Create, preview, test, tidy and edit STACK questions in Moodle ≤ 4.2
    Given the site is running Moodle version 4.2 or lower
    When I am on the "Adapt button block" "core_question > preview" page logged in as teacher
    And I set the following fields to these values:
      | How questions behave | Adaptive          |
      | Marks                | Show mark and max |
    And I press "Start again with these options"
    Then I should see "This text will be shown until the adaptbutton has been clicked."
    And I should see "Adapt block #1"
    And I should not see "Adapt block #2"
    And I should not see "Adapt block #3"
    And I should not see "Adapt block #4"
    And I click on "Click me" "button"
    And I wait "2" seconds
    Then I should see "Adapt block #4"
    And I should not see "Adapt block #1"
    And I should not see "Adapt block #2"
    And I should see "Adapt block #3"
    And I click on "Check" "button"
    And I wait "2" seconds
    Then I should see "Adapt block #4"
    And I should see "Adapt block #2"
    And I should not see "Adapt block #1"
    And I should see "Adapt block #3"
    And I should see "Yes, you clicked the button!"

  @javascript
  Scenario: Create, preview, test, tidy and edit STACK questions in Moodle ≥ 4.3
    Given the site is running Moodle version 4.3 or higher
    When I am on the "Adapt button block" "core_question > preview" page logged in as teacher
    And I set the following fields to these values:
      | How questions behave | Adaptive          |
      | Marks                | Show mark and max |
    And I press "Save preview options and start again"
    Then I should see "This text will be shown until the adaptbutton has been clicked."
    And I should see "Adapt block #1"
    And I should not see "Adapt block #2"
    And I should not see "Adapt block #3"
    And I should not see "Adapt block #4"
    And I click on "Click me" "button"
    And I wait "2" seconds
    Then I should see "Adapt block #4"
    And I should not see "Adapt block #1"
    And I should not see "Adapt block #2"
    And I should see "Adapt block #3"
    And I click on "Check" "button"
    And I wait "2" seconds
    Then I should see "Adapt block #4"
    And I should see "Adapt block #2"
    And I should not see "Adapt block #1"
    And I should see "Adapt block #3"
    And I should see "Yes, you clicked the button!"

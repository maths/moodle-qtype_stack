@qtype @qtype_stack @_file_upload @javascript
Feature: Test input of correct answers on various inputs.
  As a teacher
  In order to check different STACK inputs will work for students
  I need to preview them

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
    And I am on the "Course 1" "core_question > course question import" page logged in as "teacher"
    And I set the field "id_format_xml" to "1"
    And I upload "question/type/stack/samplequestions/input-sample-questions.xml" file to "Import" filemanager
    And I press "id_submitbutton"
    And I press "Continue"

  Scenario: Test algebraic input

    When I am on the "Algebraic input" "core_question > preview" page logged in as teacher
    And I set the following fields to these values:
      | How questions behave | Adaptive          |
    And I press "id_saverestart"
    And I set the input "ans1" to "===" in the STACK question
    And I wait until "Your last answer was interpreted as follows" "text" exists
    Then I should see "This answer is invalid."
    And I set the input "ans1" to "a*b" in the STACK question
    And I wait until "This answer is invalid." "text" does not exist
    And I press "Check"
    And I wait until "Correct answer, well done" "text" exists

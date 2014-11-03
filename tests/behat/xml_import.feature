@qtype @qtype_stack
Feature: Test importing STACK questions from Moodle XML files.
  In order reuse questions
  As an teacher
  I need to be able to import them.

  Background:
    Given the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
    And the following "users" exist:
      | username | firstname |
      | teacher  | Teacher   |
    And the following "course enrolments" exist:
      | user    | course | role           |
      | teacher | C1     | editingteacher |
    And I log in as "teacher"
    And I follow "Course 1"

  @javascript
  Scenario: import a STACK question from a Moodle XML file
    When I navigate to "Import" node in "Course administration > Question bank"
    And I set the field "id_format_xml" to "1"
    And I upload "question/type/stack/samplequestions/odd-even.xml" file to "Import" filemanager
    And I press "id_submitbutton"
    Then I should see "Parsing questions from import file."
    And I should see "Importing 1 questions from file"
    And I should see "Give an example of an odd function by typing"
    And I press "Continue"
    And I should see "Odd and even functions"

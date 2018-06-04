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
    And I am on "Course 1" course homepage

  @javascript
  Scenario: import a STACK question from a Moodle XML file
    When I navigate to "Import" node in "Course administration > Question bank"
    And I set the field "id_format_xml" to "1"
    And I upload "question/type/stack/samplequestions/sample_questions.xml" file to "Import" filemanager
    And I press "id_submitbutton"
    Then I should see "Parsing questions from import file."
    And I should see "Importing 19 questions from file"
    And I should see "This question just tests answer boxes of multiple sizes."
    And I press "Continue"
    And I should see "Algebraic input (answer box sizes test)"

    # Now export again.
    And I navigate to "Export" node in "Course administration > Question bank"
    And I set the field "id_format_xml" to "1"
    And I set the field "Export category" to "Default for Scratch"
    And I press "Export questions to file"
    And following "click here" should download between "100000" and "110000" bytes
    # If the download step is the last in the scenario then we can sometimes run
    # into the situation where the download page causes a http redirect but behat
    # has already conducted its reset (generating an error). By putting a logout
    # step we avoid behat doing the reset until we are off that page.
    And I log out

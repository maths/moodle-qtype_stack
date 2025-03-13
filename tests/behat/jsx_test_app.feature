@qtype @qtype_stack @app @javascript
Feature: Test running JSX Graph question in Moodle App.
  As a student
  In order to check JSX works in the app
  I need to answer a question

  Background:
    Given the site is running Moodle version 4.1 or higher
    And I set up STACK using the PHPUnit configuration
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
    And the following "users" exist:
      | username |
      | student  |
    And the following "course enrolments" exist:
      | user     | course | role    |
      | student  | C1     | student |
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "activities" exist:
      | activity   | name      | course | idnumber | preferredbehaviour |
      | quiz       | Test quiz | C1     | quiz     | adaptive           |
    And the following "questions" exist:
      | questioncategory | qtype | name                                     | template               |
      | Test questions   | stack | JSX behat test                           | jsx_graph_input        |
    And quiz "Test quiz" contains the following questions:
      | JSX behat test                          | 1  |

  Scenario: Test JSX input in Moodle App

    When I entered the "quiz" activity "Test quiz" on course "Course 1" as "student" in the app
    And I press "Attempt quiz now" in the app
    And I set the input "ans2" to "[0,0]" in the STACK app question
    And I drag JSXelement "element1" to JSXelement "element2"
    And I switch to the main frame
    And I press "Check" in the app
    And I press "OK" in the app
    And I wait until "Correct answer, well done" "text" exists

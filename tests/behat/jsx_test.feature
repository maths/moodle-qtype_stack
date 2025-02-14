@qtype @qtype_stack @_file_upload @javascript
Feature: Test running JSX Graph question.
  As a teacher
  In order to check my JSX STACK questions will work for students
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
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype | name                                     | template               |
      | Test questions   | stack | JSX behat test                           | jsx_graph_input        |

  Scenario: Test JSX input

    When I am on the "JSX behat test" "core_question > preview" page logged in as teacher
    And I set the following fields to these values:
      | How questions behave | Adaptive          |
    And I press "id_saverestart"
    And I set the input "ans2" to "[0,0]" in the STACK question
    And I drag JSXelement "element1" to JSXelement "element2"
    And I switch to the main frame
    And I press "Check"
    And I wait until "Correct answer, well done" "text" exists

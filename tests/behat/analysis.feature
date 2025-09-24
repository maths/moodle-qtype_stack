@qtype @qtype_stack
Feature: Test analysis response page
  As a teacher
  In order to analyse student responses
  I need to open the analysis reposnse page

  Background:
    Given I set up STACK using the PHPUnit configuration
    Given the following "users" exist:
      | username |
      | teacher  |
      | student  |
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "course enrolments" exist:
      | user    | course | role           |
      | teacher | C1     | editingteacher |
      | student | C1     | student        |
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype  | name                                     | template               |
      | Test questions   | stack  | Test question 1                          | algebraic_input        |
      | Test questions   | stack  | Test question 2                          | algebraic_input        |
      | Test questions   | stack  | Test question 3                          | algebraic_input        |
      | Test questions   | random | Random (Test questions)                  |                        |
    And the following "activities" exist:
      | activity   | name   | course | idnumber |
      | quiz       | Quiz 1 | C1     | quiz1    |
      | quiz       | Quiz 2 | C1     | quiz2    |
    And quiz "Quiz 1" contains the following questions:
      | question                | page |
      | Random (Test questions) | 1    |
    And quiz "Quiz 2" contains the following questions:
      | question                | page |
      | Test question 3         | 1    |

  @javascript
  Scenario: Analyse a question in Moodle ≥ 4.1
    Given the site is running Moodle version 4.1 or higher
    And I am on the "Quiz 2" "mod_quiz > View" page logged in as "student"
    And I press "Attempt quiz"
    And I set the input "ans1" to "a*b" in the STACK question
    And I wait until "Your last answer was interpreted as follows" "text" exists
    And I follow "Finish attempt ..."
    And I press "Submit all and finish"
    And I click on "Submit all and finish" "button" in the "Submit all your answers and finish?" "dialogue"
    And I follow "Finish review"
    When I am on the "C1 > Test question 3" "qtype_stack > analysis" page logged in as "teacher"
    Then I should see "Type in {@ta@}."
    And I click on "select option:nth-child(2)" "css_element"
    And I should see "ATAlgEquiv(ans1,ta)"
    And I follow "Variants"
    And I should see "## prt1: 1 (100.00%); # = 1 | prt1-1-T"

  @javascript
  Scenario: Check random questions appear in analysis
    Given I log in as "teacher"
    And I am on the "Quiz 1" "mod_quiz > Responses report" page
    When I am on the "C1 > Test question 1" "qtype_stack > analysis" page logged in as "teacher"
    Then I should see "Type in {@ta@}."
    And I click on "select option:nth-child(2)" "css_element"
    And I should see "ATAlgEquiv(ans1,ta)"

  @javascript
  Scenario: Analyse a question in Moodle ≤ 4.0
    Given the site is running Moodle version 4.0 or lower
    And I am on the "Quiz 2" "mod_quiz > View" page logged in as "student"
    And I press "Attempt quiz"
    And I set the input "ans1" to "a*b" in the STACK question
    And I wait until "Your last answer was interpreted as follows" "text" exists
    And I follow "Finish attempt ..."
    And I press "Submit all and finish"
    And I click on "Submit all and finish" "button" in the "Confirmation" "dialogue"
    And I follow "Finish review"
    When I am on the "C1 > Test question 3" "qtype_stack > analysis" page logged in as "teacher"
    Then I should see "Type in {@ta@}."
    And I click on "select option:nth-child(2)" "css_element"
    And I should see "ATAlgEquiv(ans1,ta)"
    And I follow "Variants"
    And I should see "## prt1: 1 (100.00%); # = 1 | prt1-1-T"

@qtype @qtype_stack @_file_upload @javascript @app
Feature: Test input of correct answers on various inputs.
  As a student
  In order to check different STACK inputs work in the app
  I need to answer them

  Background:
    Given I set up STACK using the PHPUnit configuration
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
    And the following "questions" exist:
      | questioncategory | qtype | name                                     | template               |
      | Test questions   | stack | Algebraic input                          | algebraic_input        |
      | Test questions   | stack | Algebraic input (align to the right)     | algebraic_input_right  |
      | Test questions   | stack | Algebraic input (answer box sizes test)  | algebraic_input_size   |
      | Test questions   | stack | Algebraic input (compact)                | algebraic_input_compact|
      | Test questions   | stack | Algebraic input (empty answer permitted) | algebraic_input_empty  |
      | Test questions   | stack | Algebraic input (with simplification)    | algebraic_input_simpl  |
      | Test questions   | stack | Checkbox                                 | checkbox_input         |
      | Test questions   | stack | Checkbox (no body LaTeX)                 | checkbox_input_no_latex|
      | Test questions   | stack | Checkbox (plots in options)              | checkbox_input_plots   |
      | Test questions   | stack | Checkbox (Show teacher's answer)         | checkbox_show_tans     |
      | Test questions   | stack | Dropdown (shuffle)                       | dropdown_input         |
      | Test questions   | stack | Equiv input test (compact)               | equiv_input_compact    |
      | Test questions   | stack | Equiv input test (let, or +-)            | equiv_input            |
      | Test questions   | stack | Matrix                                   | matrix_input           |
      | Test questions   | stack | Matrix (varmatrix)                       | varmatrix_input        |
      | Test questions   | stack | Matrix-multi                             | matrix_multi_input     |
      | Test questions   | stack | Notes                                    | notes_input            |
      | Test questions   | stack | Numerical input (min sf)                 | numerical_input        |
      | Test questions   | stack | Radio                                    | radio_input            |
      | Test questions   | stack | Radio (compact)                          | radio_input_compact    |
      | Test questions   | stack | Single char                              | single_char_input      |
      | Test questions   | stack | String input                             | string_input           |
      | Test questions   | stack | Textarea test                            | textarea_input         |
      | Test questions   | stack | Textarea test (compact)                  | textarea_input_compact |
      | Test questions   | stack | True/false                               | true_false_input       |
      | Test questions   | stack | Units                                    | units_input            |
    And the following "qtype_stack > Deployed variants" exist:
      | question                    | seed       |
      | Checkbox (no body LaTeX)    | 972840190  |
      | Checkbox (plots in options) | 473610050  |
      | Dropdown (shuffle)          | 1859965311 |
    And the following "activities" exist:
      | activity   | name      | course | idnumber | preferredbehaviour |
      | quiz       | Test quiz | C1     | quiz     | adaptive           |
    And quiz "Test quiz" contains the following questions:
      | Algebraic input  | 1 |

  Scenario: Test algebraic input

    When I entered the "quiz" activity "Test quiz" on course "Course 1" as "student" in the app
    And I press "Attempt quiz now" in the app
    And I press "Check" in the app
    And I press "OK" in the app
    And I wait "2" seconds
    Then  ".stackprtfeedback-prt1" "css_element" should not exist
    # Check no simplification
    And I set the input "ans1" to "makelist(k^2,k,1,8)" in the STACK app question
    # Check not compact
    And I wait until "Your last answer was interpreted as follows" "text" exists
    And I should see "This answer is invalid."
    And I set the input "ans1" to "a*c" in the STACK app question
    And I wait until "This answer is invalid." "text" does not exist
    And I press "Check" in the app
    And I press "OK" in the app
    And I wait until "Incorrect answer" "text" exists
    And I set the input "ans1" to "===" in the STACK app question
    And I wait until "This answer is invalid." "text" exists
    And I set the input "ans1" to "a*b" in the STACK app question
    And I wait until "This answer is invalid." "text" does not exist
    And I press "Check" in the app
    And I press "OK" in the app
    # Check not compact
    And I wait until "Correct answer, well done" "text" exists
    # Confirm inverse of empty answer check
    And  ".stackprtfeedback-prt1" "css_element" should exist
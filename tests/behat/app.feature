@qtype @qtype_stack @javascript @app
Feature: Test input of correct answers on various inputs in the Moodle app.
  As a student
  In order to check different STACK inputs work in the app
  I need to answer them

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

  Scenario: App Test algebraic input
    Given the following "questions" exist:
      | questioncategory | qtype | name                                     | template               |
      | Test questions   | stack | Algebraic input                          | algebraic_input        |
    And quiz "Test quiz" contains the following questions:
      | Algebraic input                          | 1  |
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

  Scenario: App Test algebraic input right align
    Given the following "questions" exist:
      | questioncategory | qtype | name                                     | template               |
      | Test questions   | stack | Algebraic input (align to the right)     | algebraic_input_right  |
    And quiz "Test quiz" contains the following questions:
      | Algebraic input (align to the right)                          | 1  |
    When I entered the "quiz" activity "Test quiz" on course "Course 1" as "student" in the app
    And I press "Attempt quiz now" in the app
    Then the "class" attribute of "[id$='ans1']" "css_element" should contain "algebraic-right"
    And I set the input "ans1" to "cos(x^2)" in the STACK app question
    And I wait until "Your last answer was interpreted as follows" "text" exists
    And I press "Check" in the app
    And I press "OK" in the app
    And I wait until "Incorrect answer" "text" exists
    And I set the input "ans1" to "===" in the STACK app question
    And I wait until "This answer is invalid." "text" exists
    And I set the input "ans1" to "sin(x^2)" in the STACK app question
    And I wait until "This answer is invalid." "text" does not exist
    And I press "Check" in the app
    And I press "OK" in the app
    And I wait until "Correct answer, well done" "text" exists

  Scenario: App Test algebraic input size
    Given the following "questions" exist:
      | questioncategory | qtype | name                                     | template               |
      | Test questions   | stack | Algebraic input (answer box sizes test)  | algebraic_input_size   |
    And quiz "Test quiz" contains the following questions:
      | Algebraic input (answer box sizes test)                          | 1  |
    When I entered the "quiz" activity "Test quiz" on course "Course 1" as "student" in the app
    And I press "Attempt quiz now" in the app
    Then the "style" attribute of "[id$='ans1']" "css_element" should contain "width: 1.3em"
    Then the "style" attribute of "[id$='ans2']" "css_element" should contain "width: 2.47em"
    Then the "style" attribute of "[id$='ans3']" "css_element" should contain "width: 3.64em"
    Then the "style" attribute of "[id$='ans4']" "css_element" should contain "width: 4.81em"
    Then the "style" attribute of "[id$='ans5']" "css_element" should contain "width: 5.98em"
    Then the "style" attribute of "[id$='ans7']" "css_element" should contain "width: 8.32em"
    Then the "style" attribute of "[id$='ans10']" "css_element" should contain "width: 11.83em"
    Then the "style" attribute of "[id$='ans15']" "css_element" should contain "width: 17.68em"
    Then the "style" attribute of "[id$='ans20']" "css_element" should contain "width: 23.53em"
    And I set the input "ans1" to "===" in the STACK app question
    And I wait until "Your last answer was interpreted as follows" "text" exists
    And I should see "This answer is invalid."
    And I set the input "ans1" to "a" in the STACK app question
    And I wait until "This answer is invalid." "text" does not exist
    And I press "Check" in the app
    And I press "OK" in the app
    And I wait until "Correct answer, well done" "text" exists

  Scenario: App Test algebraic input compact

    Given the following "questions" exist:
      | questioncategory | qtype | name                                     | template               |
      | Test questions   | stack | Algebraic input (compact)                | algebraic_input_compact|
    And quiz "Test quiz" contains the following questions:
      | Algebraic input (compact)                | 1  |
    When I entered the "quiz" activity "Test quiz" on course "Course 1" as "student" in the app
    And I press "Attempt quiz now" in the app
    And I set the input "ans1" to "===" in the STACK app question
    And I wait until "This answer is invalid." "text" exists
    Then I should not see "Your last answer was interpreted as follows"
    And I set the input "ans1" to "(n*(n+1))/2" in the STACK app question
    And I wait until "This answer is invalid." "text" does not exist
    And I should not see "Your last answer was interpreted as follows"
    And I press "Check" in the app
    And I press "OK" in the app
    And I wait until ".core-question-answer-correct" "css_element" exists
    And I should not see "Correct answer, well done"

  Scenario: App Test algebraic input empty answer permitted

    Given the following "questions" exist:
      | questioncategory | qtype | name                                     | template               |
      | Test questions   | stack | Algebraic input (empty answer permitted) | algebraic_input_empty  |
    And quiz "Test quiz" contains the following questions:
      | Algebraic input (empty answer permitted) | 1  |
    When I entered the "quiz" activity "Test quiz" on course "Course 1" as "student" in the app
    And I press "Attempt quiz now" in the app
    And I press "Check" in the app
    And I press "OK" in the app
    Then I wait until "Incorrect answer." "text" exists

  Scenario: App Test algebraic input with simplification

    Given the following "questions" exist:
      | questioncategory | qtype | name                                     | template               |
      | Test questions   | stack | Algebraic input (with simplification)    | algebraic_input_simpl  |
    And quiz "Test quiz" contains the following questions:
      | Algebraic input (with simplification)    | 1  |
    When I entered the "quiz" activity "Test quiz" on course "Course 1" as "student" in the app
    And I press "Attempt quiz now" in the app
    And I set the input "ans1" to "makelist(k^2,k,1,18)" in the STACK app question
    And I wait until "Your last answer was interpreted as follows" "text" exists
    And I press "Check" in the app
    And I press "OK" in the app
    And I wait until "Incorrect answer" "text" exists
    And I set the input "ans1" to "===" in the STACK app question
    And I wait until "This answer is invalid." "text" exists
    And I set the input "ans1" to "makelist(k^2,k,1,8)" in the STACK app question
    And I wait until "This answer is invalid." "text" does not exist
    And I press "Check" in the app
    And I press "OK" in the app
    And I wait until "Correct answer, well done" "text" exists

  Scenario: App Test checkbox

    Given the following "questions" exist:
      | questioncategory | qtype | name                                     | template               |
      | Test questions   | stack | Checkbox                                 | checkbox_input         |
    And quiz "Test quiz" contains the following questions:
      | Checkbox                                 | 1  |
    # Check can select 2 answers
    When I entered the "quiz" activity "Test quiz" on course "Course 1" as "student" in the app
    And I press "Attempt quiz now" in the app
    And I click on "[name$='ans1_2']" "css_element"
    And I click on "[name$='ans1_1']" "css_element"
    And I wait until "Your last answer was interpreted as follows" "text" exists
    And I press "Check" in the app
    And I press "OK" in the app
    And I wait until "Incorrect answer" "text" exists
    # Unselect one answer to leave correct answer
    And I click on "[name$='ans1_2']" "css_element"
    And I wait "2" seconds
    And I press "Check" in the app
    And I press "OK" in the app
    And I wait until "Correct answer, well done" "text" exists

  Scenario: App Test checkbox - no body latex

    Given the following "questions" exist:
      | questioncategory | qtype | name                                     | template               |
      | Test questions   | stack | Checkbox (no body LaTeX)                 | checkbox_input_no_latex|
    And quiz "Test quiz" contains the following questions:
      | Checkbox (no body LaTeX)                 | 1  |
    And the following "qtype_stack > Deployed variants" exist:
      | question                    | seed       |
      | Checkbox (no body LaTeX)    | 972840190  |
    When I entered the "quiz" activity "Test quiz" on course "Course 1" as "student" in the app
    And I press "Attempt quiz now" in the app
    And I click on "[name$='ans1_1']" "css_element"
    And I wait until "Your last answer was interpreted as follows" "text" exists
    And I press "Check" in the app
    And I press "OK" in the app
    And I wait until "Incorrect answer" "text" exists
    And I click on "[name$='ans1_1']" "css_element"
    And I wait until "Your last answer was interpreted as follows" "text" does not exist
    And I click on "[name$='ans1_1']" "css_element"
    And I click on "[name$='ans1_5']" "css_element"
    And I wait until "Your last answer was interpreted as follows" "text" exists
    And I press "Check" in the app
    And I press "OK" in the app
    And I wait until "Correct answer, well done" "text" exists

  Scenario: App Test checkbox - plots in options

    Given the following "questions" exist:
      | questioncategory | qtype | name                                     | template               |
      | Test questions   | stack | Checkbox (plots in options)              | checkbox_input_plots   |
    And quiz "Test quiz" contains the following questions:
      | Checkbox (plots in options)              | 1  |
    And the following "qtype_stack > Deployed variants" exist:
      | question                    | seed       |
      | Checkbox (plots in options) | 473610050  |
    When I entered the "quiz" activity "Test quiz" on course "Course 1" as "student" in the app
    And I press "Attempt quiz now" in the app
    And I click on "[name$='ans1_2']" "css_element"
    And I press "Check" in the app
    And I press "OK" in the app
    And I wait until "Your answer is partially correct" "text" exists
    And I click on "[name$='ans1_3']" "css_element"
    And I press "Check" in the app
    And I press "OK" in the app
    And I wait until "Correct answer, well done" "text" exists
    # Will not check plots actually loaded unfortunately.

  Scenario: App Test checkbox - teacher answer

    Given the following "questions" exist:
      | questioncategory | qtype | name                                     | template               |
      | Test questions   | stack | Checkbox (Show teacher's answer)         | checkbox_show_tans     |
    And quiz "Test quiz" contains the following questions:
      | Checkbox (Show teacher's answer)         | 1  |
    When I entered the "quiz" activity "Test quiz" on course "Course 1" as "student" in the app
    And I press "Attempt quiz now" in the app
    And I click on "[name$='ans1_1']" "css_element"
    And I click on "[name$='ans1_2']" "css_element"
    And I click on "[name$='ans1_3']" "css_element"
    And I click on "[name$='ans1_4']" "css_element"
    And I press "Check" in the app
    And I press "OK" in the app
    And I wait until "Correct answer, well done" "text" exists
    And I press "Submit" in the app
    And I press "Submit all and finish" in the app
    And I press "Submit" near "Once you submit" in the app
    Then I should find "Review" in the app
    And "Integration by parts" "text" should appear after "correct answer" "text" in the ".rightanswer" "css_element"
    And "Integration by substitution" "text" should appear after "correct answer" "text" in the ".rightanswer" "css_element"
    And "Apply a trig formula to remove product" "text" should appear after "correct answer" "text" in the ".rightanswer" "css_element"
    And "Remove trig with complex exponentials, then integrate" "text" should appear after "correct answer" "text" in the ".rightanswer" "css_element"

  Scenario: App Dropdown (shuffle)

    Given the following "questions" exist:
      | questioncategory | qtype | name                                     | template               |
      | Test questions   | stack | Dropdown (shuffle)                       | dropdown_input         |
    And quiz "Test quiz" contains the following questions:
      | Dropdown (shuffle)                       | 1  |
    And the following "qtype_stack > Deployed variants" exist:
      | question                    | seed       |
      | Dropdown (shuffle)          | 1859965311 |
    When I entered the "quiz" activity "Test quiz" on course "Course 1" as "student" in the app
    And I press "Attempt quiz now" in the app
    And I click on "ion-select[name$='ans1']" "css_element"
    And ".alert-radio-group button:nth-child(3)" "css_element" should be visible
    And I click on ".alert-radio-group button:nth-child(3)" "css_element"
    And I press "OK" in the app
    And I wait until "Your last answer was interpreted as follows" "text" exists
    And I press "Check" in the app
    And I press "OK" in the app
    And I wait until "Incorrect answer" "text" exists
    And I click on "ion-select[name$='ans1']" "css_element"
    And ".alert-radio-group button:nth-child(1)" "css_element" should be visible
    And I click on ".alert-radio-group button:nth-child(1)" "css_element"
    And I press "OK" in the app
    And I wait until "Your last answer was interpreted as follows" "text" does not exist
    And I click on "ion-select[name$='ans1']" "css_element"
    And ".alert-radio-group button:nth-child(2)" "css_element" should be visible
    And I click on ".alert-radio-group button:nth-child(2)" "css_element"
    And I press "OK" in the app
    And I wait until "Your last answer was interpreted as follows" "text" exists
    And I press "Check" in the app
    And I press "OK" in the app
    And I wait until "Correct answer, well done" "text" exists

  Scenario: App Equivalent input test - compact

    Given the following "questions" exist:
      | questioncategory | qtype | name                                     | template               |
      | Test questions   | stack | Equiv input test (compact)               | equiv_input_compact    |
    And quiz "Test quiz" contains the following questions:
      | Equiv input test (compact)               | 1  |
    When I entered the "quiz" activity "Test quiz" on course "Course 1" as "student" in the app
    And I press "Attempt quiz now" in the app
    And I set the input "ans1" to "===" in the STACK app question
    And I wait until "This answer is invalid." "text" exists
    And I set the input "ans1" to "3*x+7 = 5" in the STACK app question
    And I wait until "This answer is invalid." "text" does not exist
    And I should not see "Your last answer was interpreted as follows"
    And I press "Check" in the app
    And I press "OK" in the app
    Then I should see "Incorrect answer."
    And I set the input "ans1" to "===" in the STACK app question
    And I wait until "This answer is invalid." "text" exists
    And I set the input "ans1" to "3*x+7 = 4" in the STACK app question
    And I wait until "This answer is invalid." "text" does not exist
    And I press "Check" in the app
    And I press "OK" in the app
    And I wait until "Correct answer, well done" "text" exists

  Scenario: App Equivalent input test - let or +-

    Given the following "questions" exist:
      | questioncategory | qtype | name                                     | template               |
      | Test questions   | stack | Equiv input test (let, or +-)            | equiv_input            |
    And quiz "Test quiz" contains the following questions:
      | Equiv input test (let, or +-)            | 1  |
    When I entered the "quiz" activity "Test quiz" on course "Course 1" as "student" in the app
    And I press "Attempt quiz now" in the app
    And I set the input "ans1" to "===" in the STACK app question
    And I wait until "This answer is invalid." "text" exists
    And I set the input "ans1" to "(x-a)^2 = 5" in the STACK app question
    And I wait until "This answer is invalid." "text" does not exist
    And I should not see "Your last answer was interpreted as follows"
    And I press "Check" in the app
    And I press "OK" in the app
    Then I should see "Incorrect answer."
    And I set the input "ans1" to "===" in the STACK app question
    And I wait until "This answer is invalid." "text" exists
    And I set the input "ans1" to "(x-a)^2 = 4" in the STACK app question
    And I wait until "This answer is invalid." "text" does not exist
    And I press "Check" in the app
    And I press "OK" in the app
    And I wait until "Correct answer, well done" "text" exists

  Scenario: App Matrix

    Given the following "questions" exist:
      | questioncategory | qtype | name                                     | template               |
      | Test questions   | stack | Matrix                                   | matrix_input           |
    And quiz "Test quiz" contains the following questions:
      | Matrix                                   | 1  |
    When I entered the "quiz" activity "Test quiz" on course "Course 1" as "student" in the app
    And I press "Attempt quiz now" in the app
    And I set the input "ans1_sub_0_0" to "0" in the STACK app question
    And I set the input "ans1_sub_0_1" to "2" in the STACK app question
    And I set the input "ans1_sub_1_0" to "3" in the STACK app question
    And I set the input "ans1_sub_1_1" to "4" in the STACK app question
    And I wait until "Your last answer was interpreted as follows" "text" exists
    And I press "Check" in the app
    And I press "OK" in the app
    And I wait until "Incorrect answer" "text" exists
    And I set the input "ans1_sub_0_0" to "===" in the STACK app question
    And I wait until "This answer is invalid" "text" exists
    And I set the input "ans1_sub_0_0" to "1" in the STACK app question
    And I wait until "This answer is invalid." "text" does not exist
    And I press "Check" in the app
    And I press "OK" in the app
    And I wait until "Correct answer, well done" "text" exists

  Scenario: App Varmatrix

    Given the following "questions" exist:
      | questioncategory | qtype | name                                     | template               |
      | Test questions   | stack | Matrix (varmatrix)                       | varmatrix_input        |
    And quiz "Test quiz" contains the following questions:
      | Matrix (varmatrix)                       | 1  |
    When I entered the "quiz" activity "Test quiz" on course "Course 1" as "student" in the app
    And I press "Attempt quiz now" in the app
    And I set the STACK input "ans1" to multiline in app:
    """
    1 0 0 0
    0 1 0 ===
    """
    And I wait until "This answer is invalid" "text" exists
    And I set the STACK input "ans2" to multiline in app:
    """
    1 0
    0 0
    0 1
    0 3
    """
    And I set the STACK input "ans1" to multiline in app:
    """
    1 0 0 0
    0 1 0 0
    """
    And I wait until "This answer is invalid" "text" does not exist
    And I press "Check" in the app
    And I press "OK" in the app
    And I wait until "Incorrect answer" "text" exists
    And I set the STACK input "ans1" to multiline in app:
    """
    1 0 0 0
    0 1 0 ===
    """
    And I wait until "This answer is invalid" "text" exists
    And I set the STACK input "ans2" to multiline in app:
    """
    1 0
    0 1
    0 0
    0 0
    """
    And I set the STACK input "ans1" to multiline in app:
    """
    1 0 0 0
    0 1 0 0
    """
    And I wait until "This answer is invalid" "text" does not exist
    And I press "Check" in the app
    And I press "OK" in the app
    And I wait until "Correct answer, well done" "text" exists

  Scenario: App Validation of multiple Matrices

    Given the following "questions" exist:
      | questioncategory | qtype | name                                     | template               |
      | Test questions   | stack | Matrix-multi                             | matrix_multi_input     |
    And quiz "Test quiz" contains the following questions:
      | Matrix-multi                             | 1  |
    When I entered the "quiz" activity "Test quiz" on course "Course 1" as "student" in the app
    And I press "Attempt quiz now" in the app
    And I set the input "ans1_sub_0_0" to "*" in the STACK app question
    And I set the input "ans2_sub_0_1" to "+" in the STACK app question
    And I wait until "'+' is an invalid final character" "text" exists
    And I wait until "'*' is an invalid final character" "text" exists
    # Making absolutely sure both appear at the same time while avoiding race condition.
    Then I should see "'+' is an invalid final character"
    And I set the input "ans1_sub_0_1" to "2" in the STACK app question
    And I set the input "ans1_sub_1_0" to "3" in the STACK app question
    And I set the input "ans1_sub_1_1" to "4" in the STACK app question
    And I set the input "ans2_sub_0_0" to "a" in the STACK app question
    And I set the input "ans2_sub_0_1" to "b" in the STACK app question
    And I set the input "ans2_sub_1_0" to "c" in the STACK app question
    And I set the input "ans2_sub_1_1" to "d" in the STACK app question
    And I set the input "ans1_sub_0_0" to "0" in the STACK app question
    And I wait until "This answer is invalid." "text" does not exist
    And I press "Check" in the app
    And I press "OK" in the app
    And I wait until "Correct answer, well done" "text" exists

  Scenario: App Notes

    Given the following "questions" exist:
      | questioncategory | qtype | name                                     | template               |
      | Test questions   | stack | Notes                                    | notes_input            |
    And quiz "Test quiz" contains the following questions:
      | Notes                                    | 1  |
    When I entered the "quiz" activity "Test quiz" on course "Course 1" as "student" in the app
    And I press "Attempt quiz now" in the app
    And I set the input "ans1" to "'@!!!!====" in the STACK app question
    And I wait until "(This input is not assessed automatically by STACK.)" "text" exists
    Then I should not see "This answer is invalid."
    And I press "Check" in the app
    And I press "OK" in the app
    And I wait "2" seconds
    And I should not see "Correct answer, well done"
    And I should not see "Incorrect answer"

  Scenario: App Numerical input - minimum significant figures

    Given the following "questions" exist:
      | questioncategory | qtype | name                                     | template               |
      | Test questions   | stack | Numerical input (min sf)                 | numerical_input        |
    And quiz "Test quiz" contains the following questions:
      | Numerical input (min sf)                 | 1  |
    When I entered the "quiz" activity "Test quiz" on course "Course 1" as "student" in the app
    And I press "Attempt quiz now" in the app
    And I set the input "ans1" to "3.1" in the STACK app question
    And I wait until "This answer is invalid." "text" exists
    And I set the input "ans1" to "3.18" in the STACK app question
    And I wait until "This answer is invalid." "text" does not exist
    And I press "Check" in the app
    And I press "OK" in the app
    Then I should see "Incorrect answer."
    And I set the input "ans1" to "===" in the STACK app question
    And I wait until "This answer is invalid." "text" exists
    And I set the input "ans1" to "3.14" in the STACK app question
    And I wait until "This answer is invalid." "text" does not exist
    And I press "Check" in the app
    And I press "OK" in the app
    And I wait until "Correct answer, well done" "text" exists

  Scenario: App Radio buttons

    Given the following "questions" exist:
      | questioncategory | qtype | name                                     | template               |
      | Test questions   | stack | Radio                                    | radio_input            |
    And quiz "Test quiz" contains the following questions:
      | Radio                                    | 1  |
    When I entered the "quiz" activity "Test quiz" on course "Course 1" as "student" in the app
    And I press "Attempt quiz now" in the app
    And I click on "ion-radio[value='3']" "css_element"
    And I wait until "Your last answer was interpreted as follows" "text" exists
    And I press "Check" in the app
    And I press "OK" in the app
    And I wait until "Incorrect answer" "text" exists
    And I click on "ion-radio[value='']" "css_element"
    And I wait until "Your last answer was interpreted as follows" "text" does not exist
    And I press "Check" in the app
    And I press "OK" in the app
    And I wait "2" seconds
    Then I should not see "Correct answer, well done"
    And I should not see "Incorrect answer"
    And I click on "ion-radio[value='1']" "css_element"
    And I wait until "Your last answer was interpreted as follows" "text" exists
    And I press "Check" in the app
    And I press "OK" in the app
    And I wait until "Correct answer, well done" "text" exists

  Scenario: App Radio buttons - compact

    Given the following "questions" exist:
      | questioncategory | qtype | name                                     | template               |
      | Test questions   | stack | Radio (compact)                          | radio_input_compact    |
    And quiz "Test quiz" contains the following questions:
      | Radio (compact)                          | 1  |
    When I entered the "quiz" activity "Test quiz" on course "Course 1" as "student" in the app
    And I press "Attempt quiz now" in the app
    And I click on "ion-radio[value='3']" "css_element"
    # Cannot enter an invalid answer to force some text to appear
    # so we're just going to have to wait.
    And I wait "2" seconds
    Then I should not see "Your last answer was interpreted as follows"
    And I press "Check" in the app
    And I press "OK" in the app
    And I wait until "Incorrect answer" "text" exists
    And I click on "ion-radio[value='1']" "css_element"
    And I wait "2" seconds
    And I press "Check" in the app
    And I press "OK" in the app
    And I wait until "Correct answer, well done" "text" exists

  Scenario: App Single character

    Given the following "questions" exist:
      | questioncategory | qtype | name                                     | template               |
      | Test questions   | stack | Single char                              | single_char_input      |
    And quiz "Test quiz" contains the following questions:
      | Single char                              | 1  |
    When I entered the "quiz" activity "Test quiz" on course "Course 1" as "student" in the app
    And I press "Attempt quiz now" in the app
    And I set the input "ans1" to "y" in the STACK app question
    And I wait until "Your last answer was interpreted as follows" "text" exists
    And I press "Check" in the app
    And I press "OK" in the app
    And I set the input "ans1" to "=" in the STACK app question
    And I wait until "This answer is invalid." "text" exists
    # Check answer gets truncated
    And I set the input "ans1" to "xyghgh" in the STACK app question
    And I wait until "This answer is invalid." "text" does not exist
    And I press "Check" in the app
    And I press "OK" in the app
    And I wait until "Correct answer, well done" "text" exists

  Scenario: App String input

    Given the following "questions" exist:
      | questioncategory | qtype | name                                     | template               |
      | Test questions   | stack | String input                             | string_input           |
    And quiz "Test quiz" contains the following questions:
      | String input                             | 1  |
    When I entered the "quiz" activity "Test quiz" on course "Course 1" as "student" in the app
    And I press "Attempt quiz now" in the app
    And I set the input "ans1" to "=" in the STACK app question
    And I wait until "Your last answer was interpreted as follows" "text" exists
    And I set the input "ans2" to "Anything at all" in the STACK app question
    And I set the input "ans1" to "" in the STACK app question
    And I wait "2" seconds
    Then I should not see "Your last answer was interpreted as follows"
    And I set the input "ans1" to "Hello world" in the STACK app question
    And I wait until "Your last answer was interpreted as follows" "text" exists
    And I press "Check" in the app
    And I press "OK" in the app
    And I wait until "Correct answer, well done" "text" exists

  Scenario: App Textarea test

    Given the following "questions" exist:
      | questioncategory | qtype | name                                     | template               |
      | Test questions   | stack | Textarea test                            | textarea_input         |
    And quiz "Test quiz" contains the following questions:
      | Textarea test                            | 1  |
    When I entered the "quiz" activity "Test quiz" on course "Course 1" as "student" in the app
    And I press "Attempt quiz now" in the app
    And I set the input "ans1" to "cos(x^2)" in the STACK app question
    And I wait until "Your last answer was interpreted as follows" "text" exists
    And I press "Check" in the app
    And I press "OK" in the app
    And I wait until "Incorrect answer" "text" exists
    And I set the input "ans1" to "===" in the STACK app question
    And I wait until "This answer is invalid." "text" exists
    And I set the STACK input "ans1" to multiline in app:
    """
    x = 1+-a
    x = -2 or x = 2
    """
    And I wait until "This answer is invalid." "text" does not exist
    And I press "Check" in the app
    And I press "OK" in the app
    And I wait until "Correct answer, well done" "text" exists

  Scenario: App Textarea test - compact

    Given the following "questions" exist:
      | questioncategory | qtype | name                                     | template               |
      | Test questions   | stack | Textarea test (compact)                  | textarea_input_compact |
    And quiz "Test quiz" contains the following questions:
      | Textarea test (compact)                  | 1  |
    When I entered the "quiz" activity "Test quiz" on course "Course 1" as "student" in the app
    And I press "Attempt quiz now" in the app
    And I set the input "ans1" to "cos(x^2)" in the STACK app question
    And I wait "2" seconds
    Then I should not see "Your last answer was interpreted as follows"
    And I press "Check" in the app
    And I press "OK" in the app
    And I wait until "Incorrect answer" "text" exists
    And I set the input "ans1" to "===" in the STACK app question
    And I wait until "This answer is invalid." "text" exists
    And I set the STACK input "ans1" to multiline in app:
    """
    x=1
    x=2
    """
    And I wait until "This answer is invalid." "text" does not exist
    And I press "Check" in the app
    And I press "OK" in the app
    And I wait until "Correct answer, well done" "text" exists

  Scenario: App True/false

    Given the following "questions" exist:
      | questioncategory | qtype | name                                     | template               |
      | Test questions   | stack | True/false                               | true_false_input       |
    And quiz "Test quiz" contains the following questions:
      | True/false                               | 1  |
    When I entered the "quiz" activity "Test quiz" on course "Course 1" as "student" in the app
    And I press "Attempt quiz now" in the app
    And I click on "ion-select[name$='ans1']" "css_element"
    And ".alert-radio-group button:nth-child(2)" "css_element" should be visible
    And I click on ".alert-radio-group button:nth-child(2)" "css_element"
    And I press "OK" in the app
    And I wait until "Your last answer was interpreted as follows" "text" exists
    And I press "Check" in the app
    And I press "OK" in the app
    And I wait until "Your answer is partially correct." "text" exists
    And I click on "ion-select[name$='ans1']" "css_element"
    Then ".alert-radio-group button:last-child" "css_element" should be visible
    And I click on ".alert-radio-group button:last-child" "css_element"
    And I press "OK" in the app
    And I wait until "Your last answer was interpreted as follows" "text" does not exist
    And I click on "ion-select[name$='ans1']" "css_element"
    And ".alert-radio-group button:nth-child(1)" "css_element" should be visible
    And I click on ".alert-radio-group button:nth-child(1)" "css_element"
    And I press "OK" in the app
    And I wait until "Your last answer was interpreted as follows" "text" exists
    And I press "Check" in the app
    And I press "OK" in the app
    And I wait until "Your last answer was interpreted as follows" "text" exists

  Scenario: App Units

    Given the following "questions" exist:
      | questioncategory | qtype | name                                     | template               |
      | Test questions   | stack | Units                                    | units_input            |
    And quiz "Test quiz" contains the following questions:
      | Units                                    | 1  |
    When I entered the "quiz" activity "Test quiz" on course "Course 1" as "student" in the app
    And I press "Attempt quiz now" in the app
    And I set the input "ans1" to "9.81" in the STACK app question
    And I wait until "This answer is invalid. Your answer must have units" "text" exists
    And I set the input "ans1" to "9.81*N" in the STACK app question
    And I wait until "This answer is invalid." "text" does not exist
    And I press "Check" in the app
    And I press "OK" in the app
    Then I should see "Incorrect answer."
    And I should see "Your units are incompatible with those used by the teacher."
    And I set the input "ans1" to "===" in the STACK app question
    And I wait until "This answer is invalid." "text" exists
    And I set the input "ans1" to "(9.81*m)/s^2" in the STACK app question
    And I wait until "This answer is invalid." "text" does not exist
    And I press "Check" in the app
    And I press "OK" in the app
    And I wait until "Correct answer, well done" "text" exists

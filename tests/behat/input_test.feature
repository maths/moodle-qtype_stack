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

  Scenario: Test algebraic input

    When I am on the "Algebraic input" "core_question > preview" page logged in as teacher
    And I set the following fields to these values:
      | How questions behave | Adaptive          |
    And I press "id_saverestart"
    # Check can't send empty answers
    And I press "Check"
    And I wait "2" seconds
    Then  ".stackprtfeedback-prt1" "css_element" should not exist
    # Check no simplification
    And I set the input "ans1" to "makelist(k^2,k,1,8)" in the STACK question
    # Check not compact
    And I wait until "Your last answer was interpreted as follows" "text" exists
    And I should see "This answer is invalid."
    And I set the input "ans1" to "a*c" in the STACK question
    And I wait until "This answer is invalid." "text" does not exist
    And I press "Check"
    And I wait until "Incorrect answer" "text" exists
    And I set the input "ans1" to "===" in the STACK question
    And I wait until "This answer is invalid." "text" exists
    And I set the input "ans1" to "a*b" in the STACK question
    And I wait until "This answer is invalid." "text" does not exist
    And I press "Check"
    # Check not compact
    And I wait until "Correct answer, well done" "text" exists
    # Confirm inverse of empty answer check
    And  ".stackprtfeedback-prt1" "css_element" should exist

  Scenario: Test algebraic input right align

    When I am on the "Algebraic input (align to the right)" "core_question > preview" page logged in as teacher
    Then the "class" attribute of "[id$='ans1']" "css_element" should contain "algebraic-right"
    And I set the following fields to these values:
      | How questions behave | Adaptive          |
    And I press "id_saverestart"
    And I set the input "ans1" to "cos(x^2)" in the STACK question
    And I wait until "Your last answer was interpreted as follows" "text" exists
    And I press "Check"
    And I wait until "Incorrect answer" "text" exists
    And I set the input "ans1" to "===" in the STACK question
    And I wait until "This answer is invalid." "text" exists
    And I set the input "ans1" to "sin(x^2)" in the STACK question
    And I wait until "This answer is invalid." "text" does not exist
    And I press "Check"
    And I wait until "Correct answer, well done" "text" exists

  Scenario: Test algebraic input size

    When I am on the "Algebraic input (answer box sizes test)" "core_question > preview" page logged in as teacher
    Then the "style" attribute of "[id$='ans1']" "css_element" should contain "width: 1em"
    Then the "style" attribute of "[id$='ans2']" "css_element" should contain "width: 1.9em"
    Then the "style" attribute of "[id$='ans3']" "css_element" should contain "width: 2.8em"
    Then the "style" attribute of "[id$='ans4']" "css_element" should contain "width: 3.7em"
    Then the "style" attribute of "[id$='ans5']" "css_element" should contain "width: 4.6em"
    Then the "style" attribute of "[id$='ans7']" "css_element" should contain "width: 6.4em"
    Then the "style" attribute of "[id$='ans10']" "css_element" should contain "width: 9.1em"
    Then the "style" attribute of "[id$='ans15']" "css_element" should contain "width: 13.6em"
    Then the "style" attribute of "[id$='ans20']" "css_element" should contain "width: 18.1em"
    And I set the following fields to these values:
      | How questions behave | Adaptive          |
    And I press "id_saverestart"
    And I set the input "ans1" to "===" in the STACK question
    And I wait until "Your last answer was interpreted as follows" "text" exists
    And I should see "This answer is invalid."
    And I set the input "ans1" to "a" in the STACK question
    And I wait until "This answer is invalid." "text" does not exist
    And I press "Check"
    And I wait until "Correct answer, well done" "text" exists

  Scenario: Test algebraic input compact

    When I am on the "Algebraic input (compact)" "core_question > preview" page logged in as teacher
    And I set the following fields to these values:
      | How questions behave | Adaptive          |
    And I press "id_saverestart"
    And I set the input "ans1" to "===" in the STACK question
    And I wait until "This answer is invalid." "text" exists
    Then I should not see "Your last answer was interpreted as follows"
    And I set the input "ans1" to "(n*(n+1))/2" in the STACK question
    And I wait until "This answer is invalid." "text" does not exist
    And I should not see "Your last answer was interpreted as follows"
    And I press "Check"
    And I wait until ".stackprtfeedback-prt1 .correct" "css_element" exists
    And I should not see "Correct answer, well done"

  Scenario: Test algebraic input empty answer permitted

    When I am on the "Algebraic input (empty answer permitted)" "core_question > preview" page logged in as teacher
    And I set the following fields to these values:
      | How questions behave | Adaptive          |
    And I press "id_saverestart"
    And I press "Check"
    Then I wait until "Incorrect answer." "text" exists

  Scenario: Test algebraic input with simplification

    When I am on the "Algebraic input (with simplification)" "core_question > preview" page logged in as teacher
    And I set the following fields to these values:
      | How questions behave | Adaptive          |
    And I press "id_saverestart"
    And I set the input "ans1" to "makelist(k^2,k,1,18)" in the STACK question
    And I wait until "Your last answer was interpreted as follows" "text" exists
    And I press "Check"
    And I wait until "Incorrect answer" "text" exists
    And I set the input "ans1" to "===" in the STACK question
    And I wait until "This answer is invalid." "text" exists
    And I set the input "ans1" to "makelist(k^2,k,1,8)" in the STACK question
    And I wait until "This answer is invalid." "text" does not exist
    And I press "Check"
    And I wait until "Correct answer, well done" "text" exists

  Scenario: Test checkbox

    When I am on the "Checkbox" "core_question > preview" page logged in as teacher
    And I set the following fields to these values:
      | How questions behave | Adaptive          |
    And I press "id_saverestart"
    # Check can select 2 answers
    And I click on "[id$='ans1_2']" "css_element"
    And I click on "[id$='ans1_1']" "css_element"
    And I wait until "Your last answer was interpreted as follows" "text" exists
    And I press "Check"
    And I wait until "Incorrect answer" "text" exists
    # Unselect one answer to leave correct answer
    And I click on "[id$='ans1_2']" "css_element"
    And I wait "2" seconds
    And I press "Check"
    And I wait until "Correct answer, well done" "text" exists

  Scenario: Test checkbox - no body latex

    When I am on the "Checkbox (no body LaTeX)" "core_question > preview" page logged in as teacher
    And I set the following fields to these values:
      | How questions behave | Adaptive          |
    And I press "id_saverestart"
    And I click on "[id$='ans1_1']" "css_element"
    And I wait until "Your last answer was interpreted as follows" "text" exists
    And I press "Check"
    And I wait until "Incorrect answer" "text" exists
    And I click on "[id$='ans1_1']" "css_element"
    And I wait until "Your last answer was interpreted as follows" "text" does not exist
    And I click on "[id$='ans1_1']" "css_element"
    And I click on "[id$='ans1_5']" "css_element"
    And I wait until "Your last answer was interpreted as follows" "text" exists
    And I press "Check"
    And I wait until "Correct answer, well done" "text" exists

  Scenario: Test checkbox - plots in options

    When I am on the "Checkbox (plots in options)" "core_question > preview" page logged in as teacher
    And I set the following fields to these values:
      | How questions behave | Adaptive          |
    And I press "id_saverestart"
    And I click on "[id$='ans1_2']" "css_element"
    And I press "Check"
    And I wait until "Your answer is partially correct" "text" exists
    And I click on "[id$='ans1_3']" "css_element"
    And I press "Check"
    And I wait until "Correct answer, well done" "text" exists
    Then I check "4" "stack_plot" images are loadable in the STACK question

  Scenario: Test checkbox - teacher answer

    When I am on the "Checkbox (Show teacher's answer)" "core_question > preview" page logged in as teacher
    And I set the following fields to these values:
      | How questions behave | Immediate feedback |
    And I press "id_saverestart"
    And I click on "[id$='ans1_1']" "css_element"
    And I click on "[id$='ans1_2']" "css_element"
    And I click on "[id$='ans1_3']" "css_element"
    And I click on "[id$='ans1_4']" "css_element"
    And I press "Check"
    And I wait until "Correct answer, well done" "text" exists
    Then I should see "Integration by parts" in the ".rightanswer ul" "css_element"
    Then I should see "Integration by substitution" in the ".rightanswer ul" "css_element"
    Then I should see "Apply a trig formula to remove product" in the ".rightanswer ul" "css_element"
    Then I should see "Remove trig with complex exponentials, then integrate" in the ".rightanswer ul" "css_element"

  Scenario: Dropdown (shuffle)

    When I am on the "Dropdown (shuffle)" "core_question > preview" page logged in as teacher
    And I set the following fields to these values:
      | How questions behave |  Adaptive |
    And I press "id_saverestart"

    And I click on "select[id$='ans1']" "css_element"
    And "select[id$='ans1'] option:nth-child(3)" "css_element" should be visible
    And I click on "select[id$='ans1'] option:nth-child(3)" "css_element"
    And I press "Check"
    And I wait until "Your last answer was interpreted as follows" "text" exists
    And I press "Check"
    And I wait until "Incorrect answer" "text" exists
    And I click on "select[id$='ans1']" "css_element"
    Then "select[id$='ans1'] option:first-child" "css_element" should be visible
    And I click on "select[id$='ans1'] option:first-child" "css_element"
    And I press "Check"
    And I wait until "Your last answer was interpreted as follows" "text" does not exist
    And I click on "select[id$='ans1']" "css_element"
    And "select[id$='ans1'] option:nth-child(2)" "css_element" should be visible
    And I click on "select[id$='ans1'] option:nth-child(2)" "css_element"
    And I press "Check"
    And I wait until "Your last answer was interpreted as follows" "text" exists
    And I press "Check"
    And I wait until "Correct answer, well done" "text" exists

  Scenario: Equivalent input test - compact

    When I am on the "Equiv input test (compact)" "core_question > preview" page logged in as teacher
    And I set the following fields to these values:
      | How questions behave | Adaptive          |
    And I press "id_saverestart"
    And I set the input "ans1" to "===" in the STACK question
    And I wait until "This answer is invalid." "text" exists
    And I set the input "ans1" to "3*x+7 = 5" in the STACK question
    And I wait until "This answer is invalid." "text" does not exist
    And I should not see "Your last answer was interpreted as follows"
    And I press "Check"
    Then I should see "Incorrect answer."
    And I set the input "ans1" to "===" in the STACK question
    And I wait until "This answer is invalid." "text" exists
    And I set the input "ans1" to "3*x+7 = 4" in the STACK question
    And I wait until "This answer is invalid." "text" does not exist
    And I press "Check"
    And I wait until "Correct answer, well done" "text" exists

  Scenario: Equivalent input test - let or +-

    When I am on the "Equiv input test (let, or +-)" "core_question > preview" page logged in as teacher
    And I set the following fields to these values:
      | How questions behave | Adaptive          |
    And I press "id_saverestart"
    And I set the input "ans1" to "===" in the STACK question
    And I wait until "This answer is invalid." "text" exists
    And I set the input "ans1" to "(x-a)^2 = 5" in the STACK question
    And I wait until "This answer is invalid." "text" does not exist
    And I should not see "Your last answer was interpreted as follows"
    And I press "Check"
    Then I should see "Incorrect answer."
    And I set the input "ans1" to "===" in the STACK question
    And I wait until "This answer is invalid." "text" exists
    And I set the input "ans1" to "(x-a)^2 = 4" in the STACK question
    And I wait until "This answer is invalid." "text" does not exist
    And I press "Check"
    And I wait until "Correct answer, well done" "text" exists

  Scenario: Matrix

    When I am on the "Matrix" "core_question > preview" page logged in as teacher
    And I set the following fields to these values:
      | How questions behave | Adaptive          |
    And I press "id_saverestart"
    And I set the input "ans1_sub_0_0" to "0" in the STACK question
    And I set the input "ans1_sub_0_1" to "2" in the STACK question
    And I set the input "ans1_sub_1_0" to "3" in the STACK question
    And I set the input "ans1_sub_1_1" to "4" in the STACK question
    And I wait until "Your last answer was interpreted as follows" "text" exists
    And I press "Check"
    And I wait until "Incorrect answer" "text" exists
    And I set the input "ans1_sub_0_0" to "===" in the STACK question
    And I wait until "This answer is invalid" "text" exists
    And I set the input "ans1_sub_0_0" to "1" in the STACK question
    And I wait until "This answer is invalid." "text" does not exist
    And I press "Check"
    And I wait until "Correct answer, well done" "text" exists

  Scenario: Varmatrix

    When I am on the "Matrix (varmatrix)" "core_question > preview" page logged in as teacher
    And I set the following fields to these values:
      | How questions behave | Adaptive          |
    And I press "id_saverestart"
    And I set the STACK input "ans1" to multiline:
    """
    1 0 0 0
    0 1 0 ===
    """
    And I wait until "This answer is invalid" "text" exists
    And I set the STACK input "ans2" to multiline:
    """
    1 0
    0 0
    0 1
    0 3
    """
    And I set the STACK input "ans1" to multiline:
    """
    1 0 0 0
    0 1 0 0
    """
    And I wait until "This answer is invalid" "text" does not exist
    And I press "Check"
    And I wait until "Incorrect answer" "text" exists
    And I set the STACK input "ans1" to multiline:
    """
    1 0 0 0
    0 1 0 ===
    """
    And I wait until "This answer is invalid" "text" exists
    And I set the STACK input "ans2" to multiline:
    """
    1 0
    0 1
    0 0
    0 0
    """
    And I set the STACK input "ans1" to multiline:
    """
    1 0 0 0
    0 1 0 0
    """
    And I wait until "This answer is invalid" "text" does not exist
    And I press "Check"
    And I wait until "Correct answer, well done" "text" exists

  Scenario: Validation of multiple Matrices

    When I am on the "Matrix-multi" "core_question > preview" page logged in as teacher
    And I set the following fields to these values:
      | How questions behave | Adaptive          |
    And I press "id_saverestart"
    And I set the input "ans1_sub_0_0" to "*" in the STACK question
    And I set the input "ans2_sub_0_1" to "+" in the STACK question
    And I wait until "'+' is an invalid final character" "text" exists
    And I wait until "'*' is an invalid final character" "text" exists
    # Making absolutely sure both appear at the same time while avoiding race condition.
    Then I should see "'+' is an invalid final character"
    And I set the input "ans1_sub_0_1" to "2" in the STACK question
    And I set the input "ans1_sub_1_0" to "3" in the STACK question
    And I set the input "ans1_sub_1_1" to "4" in the STACK question
    And I set the input "ans2_sub_0_0" to "a" in the STACK question
    And I set the input "ans2_sub_0_1" to "b" in the STACK question
    And I set the input "ans2_sub_1_0" to "c" in the STACK question
    And I set the input "ans2_sub_1_1" to "d" in the STACK question
    And I set the input "ans1_sub_0_0" to "0" in the STACK question
    And I wait until "This answer is invalid." "text" does not exist
    And I press "Check"
    And I wait until "Correct answer, well done" "text" exists

  Scenario: Notes

    When I am on the "Notes" "core_question > preview" page logged in as teacher
    And I set the following fields to these values:
      | How questions behave | Adaptive          |
    And I press "id_saverestart"
    And I set the input "ans1" to "'@!!!!====" in the STACK question
    And I wait until "(This input is not assessed automatically by STACK.)" "text" exists
    Then I should not see "This answer is invalid."
    And I press "Check"
    And I wait "2" seconds
    And I should not see "Correct answer, well done"
    And I should not see "Incorrect answer"

  Scenario: Numerical input - minimum significant figures

    When I am on the "Numerical input (min sf)" "core_question > preview" page logged in as teacher
    And I set the following fields to these values:
      | How questions behave | Adaptive          |
    And I press "id_saverestart"
    And I set the input "ans1" to "3.1" in the STACK question
    And I wait until "This answer is invalid." "text" exists
    And I set the input "ans1" to "3.18" in the STACK question
    And I wait until "This answer is invalid." "text" does not exist
    And I press "Check"
    Then I should see "Incorrect answer."
    And I set the input "ans1" to "===" in the STACK question
    And I wait until "This answer is invalid." "text" exists
    And I set the input "ans1" to "3.14" in the STACK question
    And I wait until "This answer is invalid." "text" does not exist
    And I press "Check"
    And I wait until "Correct answer, well done" "text" exists

  Scenario: Radio buttons

    When I am on the "Radio" "core_question > preview" page logged in as teacher
    And I set the following fields to these values:
      | How questions behave | Adaptive          |
    And I press "id_saverestart"
    And I click on "[id$='ans1_3']" "css_element"
    And I wait until "Your last answer was interpreted as follows" "text" exists
    And I press "Check"
    And I wait until "Incorrect answer" "text" exists
    And I click on "[id$='ans1_']" "css_element"
    And I wait until "Your last answer was interpreted as follows" "text" does not exist
    And I press "Check"
    And I wait "2" seconds
    Then I should not see "Correct answer, well done"
    And I should not see "Incorrect answer"
    And I click on "[id$='ans1_1']" "css_element"
    And I wait until "Your last answer was interpreted as follows" "text" exists
    And I press "Check"
    And I wait until "Correct answer, well done" "text" exists

  Scenario: Radio buttons - compact

    When I am on the "Radio (compact)" "core_question > preview" page logged in as teacher
    And I set the following fields to these values:
      | How questions behave | Adaptive          |
    And I press "id_saverestart"
    And I click on "[id$='ans1_3']" "css_element"
    # Cannot enter an invalid answer to force some text to appear
    # so we're just going to have to wait.
    And I wait "2" seconds
    Then I should not see "Your last answer was interpreted as follows"
    And I press "Check"
    And I wait until "Incorrect answer" "text" exists
    And I click on "[id$='ans1_1']" "css_element"
    And I wait "2" seconds
    And I press "Check"
    And I wait until "Correct answer, well done" "text" exists

  Scenario: Single character

    When I am on the "Single char" "core_question > preview" page logged in as teacher
    And I set the following fields to these values:
      | How questions behave | Adaptive          |
    And I press "id_saverestart"
    And I set the input "ans1" to "y" in the STACK question
    And I wait until "Your last answer was interpreted as follows" "text" exists
    And I press "Check"
    And I set the input "ans1" to "=" in the STACK question
    And I wait until "This answer is invalid." "text" exists
    # Check answer gets truncated
    And I set the input "ans1" to "xyghgh" in the STACK question
    And I wait until "This answer is invalid." "text" does not exist
    And I press "Check"
    And I wait until "Correct answer, well done" "text" exists

  Scenario: String input

    When I am on the "String input" "core_question > preview" page logged in as teacher
    And I set the following fields to these values:
      | How questions behave | Adaptive          |
    And I press "id_saverestart"
    And I set the input "ans1" to "=" in the STACK question
    And I wait until "Your last answer was interpreted as follows" "text" exists
    And I set the input "ans2" to "Anything at all" in the STACK question
    And I set the input "ans1" to "" in the STACK question
    And I wait "2" seconds
    Then I should not see "Your last answer was interpreted as follows"
    And I set the input "ans1" to "Hello world" in the STACK question
    And I wait until "Your last answer was interpreted as follows" "text" exists
    And I press "Check"
    And I wait until "Correct answer, well done" "text" exists

  Scenario: Textarea test

    When I am on the "Textarea test" "core_question > preview" page logged in as teacher
    And I set the following fields to these values:
      | How questions behave | Adaptive          |
    And I press "id_saverestart"
    And I set the input "ans1" to "cos(x^2)" in the STACK question
    And I wait until "Your last answer was interpreted as follows" "text" exists
    And I press "Check"
    And I wait until "Incorrect answer" "text" exists
    And I set the input "ans1" to "===" in the STACK question
    And I wait until "This answer is invalid." "text" exists
    And I set the STACK input "ans1" to multiline:
    """
    x = 1+-a
    x = -2 or x = 2
    """
    And I wait until "This answer is invalid." "text" does not exist
    And I press "Check"
    And I wait until "Correct answer, well done" "text" exists

  Scenario: Textarea test - compact

    When I am on the "Textarea test (compact)" "core_question > preview" page logged in as teacher
    And I set the following fields to these values:
      | How questions behave | Adaptive          |
    And I press "id_saverestart"
    And I set the input "ans1" to "cos(x^2)" in the STACK question
    And I wait "2" seconds
    Then I should not see "Your last answer was interpreted as follows"
    And I press "Check"
    And I wait until "Incorrect answer" "text" exists
    And I set the input "ans1" to "===" in the STACK question
    And I wait until "This answer is invalid." "text" exists
    And I set the STACK input "ans1" to multiline:
    """
    x=1
    x=2
    """
    And I wait until "This answer is invalid." "text" does not exist
    And I press "Check"
    And I wait until "Correct answer, well done" "text" exists

  Scenario: True/false

    When I am on the "True/false" "core_question > preview" page logged in as teacher
    And I set the following fields to these values:
      | How questions behave |  Adaptive |
    And I press "id_saverestart"
    And I click on "select[id$='ans1']" "css_element"
    And "select[id$='ans1'] option:nth-child(2)" "css_element" should be visible
    And I click on "select[id$='ans1'] option:nth-child(2)" "css_element"
    # Need to press check so behat closes dropdown but doesn't submit.
    And I press "Check"
    And I wait until "Your last answer was interpreted as follows" "text" exists
    And I press "Check"
    And I wait until "Your answer is partially correct." "text" exists
    And I click on "select[id$='ans1']" "css_element"
    Then "select[id$='ans1'] option:last-child" "css_element" should be visible
    And I click on "select[id$='ans1'] option:last-child" "css_element"
    And I press "Check"
    And I wait until "Your last answer was interpreted as follows" "text" does not exist
    And I click on "select[id$='ans1']" "css_element"
    And "select[id$='ans1'] option:nth-child(1)" "css_element" should be visible
    And I click on "select[id$='ans1'] option:nth-child(1)" "css_element"
    And I press "Check"
    And I wait until "Your last answer was interpreted as follows" "text" exists
    And I press "Check"
    And I wait until "Your last answer was interpreted as follows" "text" exists

  Scenario: Units

    When I am on the "Units" "core_question > preview" page logged in as teacher
    And I set the following fields to these values:
      | How questions behave | Adaptive          |
    And I press "id_saverestart"
    And I set the input "ans1" to "9.81" in the STACK question
    And I wait until "This answer is invalid. Your answer must have units" "text" exists
    And I set the input "ans1" to "9.81*N" in the STACK question
    And I wait until "This answer is invalid." "text" does not exist
    And I press "Check"
    Then I should see "Incorrect answer."
    And I should see "Your units are incompatible with those used by the teacher."
    And I set the input "ans1" to "===" in the STACK question
    And I wait until "This answer is invalid." "text" exists
    And I set the input "ans1" to "(9.81*m)/s^2" in the STACK question
    And I wait until "This answer is invalid." "text" does not exist
    And I press "Check"
    And I wait until "Correct answer, well done" "text" exists

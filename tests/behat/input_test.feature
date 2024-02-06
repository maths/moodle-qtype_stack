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
    # Check can't send empty answers
    And I press "Check"
    And I wait "2" seconds
    Then  ".stackprtfeedback-prt1" "css_element" should not exist
    # Check no simplification
    And I set the input "ans1" to "makelist(k^2,k,1,8)" in the STACK question
    # Check not compact
    And I wait until "Your last answer was interpreted as follows" "text" exists
    And I should see "This answer is invalid."
    And I set the input "ans1" to "a*b" in the STACK question
    And I wait until "This answer is invalid." "text" does not exist
    # Check not compact
    And I should see "Your last answer was interpreted as follows"
    And I press "Check"
    And I wait until "Correct answer, well done" "text" exists
    # Confirm inverse of empty answer check
    And  ".stackprtfeedback-prt1" "css_element" should exist


  Scenario: Test algebraic input right align

    When I am on the "Algebraic input (align to the right)" "core_question > preview" page logged in as teacher
    Then the "class" attribute of "[id$='ans1']" "css_element" should contain "algebraic-right"
    And I set the following fields to these values:
      | How questions behave | Adaptive          |
    And I press "id_saverestart"
    And I set the input "ans1" to "===" in the STACK question
    And I wait until "Your last answer was interpreted as follows" "text" exists
    And I should see "This answer is invalid."
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
    And I set the input "ans1" to "===" in the STACK question
    And I wait until "Your last answer was interpreted as follows" "text" exists
    And I should see "This answer is invalid."
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

@qtype @qtype_stack
Feature: STACK input vaidation works even if there is no maths in the question
  In order to be sure my input was interpreted correctly
  As a student
  I need to see my input rendered (even if there is no maths in the question)

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
    And I log in as "teacher"
    And I am on "Course 1" course homepage
    And I navigate to "Question bank" in current page administration

  @javascript
  Scenario: Create, preview, test, tidy and edit STACK questions
    # Create a new question.
    When I add a "STACK" question filling the form with:
      | Question name      | Test STACK question                               |
      | Question text      | What is 1 + 1? [[input:ans1]] [[validation:ans1]] |
      | Model answer       | 2                                                 |
      | SAns               | ans1                                              |
      | TAns               | 2                                                 |
    Then I should see "Test STACK question"

    # Preview it.
    When I choose "Preview" action for "Test STACK question" in the question bank
    And I switch to "questionpreview" window
    And I set the following fields to these values:
      | How questions behave | Adaptive          |
      | Marks                | Show mark and max |
    And I press "Start again with these options"
    And I set the input "ans1" to "x-1" in the STACK question
    And I wait "2" seconds
    Then I should see "Your last answer was interpreted as follows"
    And I should not see "\("
    And I switch to the main window

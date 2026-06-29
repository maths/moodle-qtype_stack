@qtype @qtype_stack @javascript
Feature: Test input of correct answers on freetext inputs.
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
      | Test questions   | stack | Freetext                                 | freetext_input         |
      | Test questions   | stack | Freetext (ASCII no input)                | freetext_input_ascii_no_input |

  Scenario: Test Freetext input

    When I am on the "Freetext" "core_question > preview" page logged in as teacher
    And I set the STACK input "ans1" to multiline:
    """
    words
    {@2+3@}
    4+ 4
    `
    f(x) = 4sqrt(2x^2+1)+c
    f(0) = 5 => c = 1
    f(x) = 4sqrt(2x^2+1)+1
    `
    `f(x) = 4sqrt(2x^2+1)+1`
    """
    And I wait until "Your last answer was interpreted as follows" "text" exists
    And I check the input "ans2" is '4sqrt(2x^2+1)+1'
    And I check the input "ans3" is 'f(x) = 4sqrt(2x^2+1)+1'
    And I check the input "ans4" is 'f(0) = 5 => c = 1'
    And I check the input "ans5" is '5 => c = 1'
    And I check the input "ans6" is '5 => c = 1'
    And I check the input "ans7" is '{"matches":["f(x) = 4sqrt(2x^2+1)+c","f(x) = 4sqrt(2x^2+1)+1"]}'
    And I check the input "ans8" is '{"matches":["4sqrt(2x^2+1)+c","4sqrt(2x^2+1)+1"]}'
    And I check the input "ans9" is '5'
    And I check the input "ans10" is 'f(x) = 4sqrt(2x^2+1)+1'
    # MathJax 3 will have rendered, MathJax 2 probably won't. Moodle 5 gives us the flattened plain text. Sigh...
    And I check the value of iframe element "asciiContainerRow" contains one of '\begin{align*}\n& & f(x)  & = 4sqrt(2x^2+1)+c\\\n& & f(0)  & = 5 => c = 1\\\n& & f(x)  & = 4sqrt(2x^2+1)+1\\\n\end{align*}\n' or '𝑓⁡(𝑥)=4⁢𝑠⁢𝑞⁢𝑟⁢𝑡⁢(2⁢𝑥2+1)+1' or 'f(x)=4sqrt(2x2+1)+1'

  Scenario: Test Freetext input with ASCII block teacher answer markdown and no input

    When I am on the "Freetext (ASCII no input)" "core_question > preview" page logged in as teacher
    # MathJax 3 will have rendered, MathJax 2 probably won't. Moodle 5 gives us the flattened plain text. Sigh...
    Then I check the value of iframe element "asciiContainerRow" contains one of '{4}\sqrt{{{2}{x}^{{2}}+{1}}}+{1}' or '42x2+1+1' or '4⁢√2⁢𝑥2+1 +1'

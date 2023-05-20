@qtype @qtype_stack
Feature: Test restoring and testing an individual STACK question from the sample questions
  As an admin
  I need to restore the STACK reveal question.

  Background:
    Given I set up STACK using the PHPUnit configuration
    Given the following "courses" exist:
      | fullname            | shortname |
      | Demonstrating STACK | STACK     |
    And I log in as "admin"
    And I navigate to "Courses > Restore course" in site administration
    And I click on "Manage backup files" "button" in the "//h2[contains(., 'User private backup area')]/following-sibling::div[1]" "xpath_element"
    And I upload "question/type/stack/samplequestions/STACK-reveal-test.mbz" file to "Files" filemanager
    And I press "Save changes"

  @javascript @_file_upload
  Scenario: Restore the STACK demo course on a Moodle ≤ 3.11
    Given the site is running Moodle version 3.11 or lower
    When I restore "STACK-reveal-test" backup into "Demonstrating STACK" course using this options:
    And I am on "Demonstrating STACK" course homepage
    Then I should see "Reveal block test"
    When I follow "Reveal block test"
    # Moodle 3.9 has "Attempt quiz now"
    # Moodle 4.0 has "Preview quiz"
    # At least `And I click on "quiz" "button"` works...
    And I click on "Attempt quiz now" "button"
    Then I should see "made from the straight line through the origin"
    When I set the input "ans1" to "true" in the STACK question
    And I wait "2" seconds
    Then I should see "If true write the subspace in parametric form"
    When I set the input "ans2_sub_0_0" to "-t" in the STACK question
    When I set the input "ans2_sub_1_0" to "3*t" in the STACK question
    When I set the input "ans2_sub_2_0" to "2*t" in the STACK question
    When I set the input "ans3" to "[t]" in the STACK question
    And I wait "2" seconds
    When I press "Check"
    Then I should see "Correct answer, well done."

  @javascript @_file_upload
  Scenario: Restore the STACK demo course on a Moodle ≥ 4.0
    Given the site is running Moodle version 4.0 or higher
    When I restore "STACK-reveal-test" backup into "Demonstrating STACK" course using this options:
    And I am on "Demonstrating STACK" course homepage
    Then I should see "Reveal block test"
    When I follow "Reveal block test"
    And I click on "Preview quiz" "button"
    Then I should see "made from the straight line through the origin"
    When I set the input "ans1" to "true" in the STACK question
    And I wait "2" seconds
    Then I should see "If true write the subspace in parametric form"
    When I set the input "ans2_sub_0_0" to "-t" in the STACK question
    When I set the input "ans2_sub_1_0" to "3*t" in the STACK question
    When I set the input "ans2_sub_2_0" to "2*t" in the STACK question
    When I set the input "ans3" to "[t]" in the STACK question
    And I wait "2" seconds
    When I press "Check"
    Then I should see "Correct answer, well done."

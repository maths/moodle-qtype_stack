@qtype @qtype_stack
Feature: Test restoring the STACK demo course
  In order to reuse all the existing shared STACK questions
  As an admin
  I need to restore the STACK demo course.

  Background:
    Given I log in as "admin"
    And I navigate to "Restore course" node in "Site administration > Courses"
    And I click on "Manage backup files" "button" in the "//h2[contains(., 'User private backup area')]/following-sibling::div[1]" "xpath_element"
    And I upload "question/type/stack/samplequestions/STACK-demo.mbz" file to "Files" filemanager
    And I press "Save changes"

  @javascript
  Scenario: Restore the STACK demo course.
    When I restore "STACK-demo.mbz" backup into a new course using this options:
    Then I should see "Demonstrating STACK"
    Then I should see "Demonstration Quiz"
    When I navigate to "Question bank" node in "Course administration"
    And I set the field "Select a category" to "Example_questions"
    Then I should see "Cart speed analysis"

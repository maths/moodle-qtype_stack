@qtype @qtype_stack
Feature: Test restoring the STACK demo course
  In order to reuse all the existing shared STACK questions
  As an admin
  I need to restore the STACK demo course.

  Background:
    Given I log in as "admin"
    And I navigate to "Courses > Restore course" in site administration
    And I click on "Manage backup files" "button" in the "//h2[contains(., 'User private backup area')]/following-sibling::div[1]" "xpath_element"
    And I upload "question/type/stack/samplequestions/STACK-demo.mbz" file to "Files" filemanager
    And I press "Save changes"

  @javascript @_file_upload
  Scenario: Restore the STACK demo course.
    When I restore "STACK-demo.mbz" backup into a new course using this options:
    And I am on "Demonstrating STACK (v4.3.4)" course homepage
    Then I should see "Demonstration quiz"
    And I navigate to "Question bank" in current page administration
    And I set the field "Select a category" to "Example_questions"
    And I should see "Cart speed analysis"

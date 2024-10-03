@qtype @qtype_stack
Feature: Test STACK library
  As a teacher
  In order to use the STACK library
  I need to preview and import questions

  Background:
    Given I set up STACK using the PHPUnit configuration
    And the following "users" exist:
      | username |
      | teacher  |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user    | course | role           |
      | teacher | C1     | editingteacher |
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "activities" exist:
      | activity   | name   | intro                           | course | idnumber |
      | quiz       | Quiz 1 | Quiz 1 for testing the Add menu | C1     | quiz1    |

  @javascript
  Scenario: Import a question starting from question bank.
    When I am on the "Course 1" "core_question > course question bank" page logged in as "teacher"
    And I click on "Create a new question" "button"
    And I click on "STACK" "text"
    And I click on "[name$='submitbutton']" "css_element"
    And I click on "STACK question library" "link"
    Then I should see "Test questions"
    And I should not see "Question variables"
    And I click on "Calculus-Refresher" "button"
    And I click on "CR_Diff_02" "button"
    And I click on "CR-Diff-02-linearity-1-b.xml" "button"
    And I should see "Differentiate \[{@p@}\] with respect to {@v@}. [[input:ans1]]"
    And I click on "Import" "button"
    And I click on "Return to question bank" "link"
    Then I should see "CR-Diff-02-linearity-1.b"

  @javascript
  Scenario: Import a question starting from quiz in Moodle < 4.3.
    Given the site is running Moodle version 4.2 or lower
    When I am on the "Quiz 1" "mod_quiz > Edit" page logged in as "teacher"
    When I open the "last" add to quiz menu
    And I follow "a new question"
    And I set the field "item_qtype_stack" to "1"
    And I press "submitbutton"
    And I click on "STACK question library" "link"
    Then I should see "Test questions"
    And I should not see "Question variables"
    And I click on "Calculus-Refresher" "button"
    And I click on "CR_Diff_02" "button"
    And I click on "CR-Diff-02-linearity-1-b.xml" "button"
    And I should see "Differentiate \[{@p@}\] with respect to {@v@}. [[input:ans1]]"
    And I click on "Import" "button"
    And I click on "Return to quiz" "link"
    And I open the "last" add to quiz menu
    And I follow "from question bank"
    And I click on "select[id$='id_selectacategory']" "css_element"
    And I click on "select[id$='id_selectacategory'] option:nth-child(2)" "css_element"
    And I should see "Default for Quiz 1"
    And I should see "CR-Diff-02-linearity-1.b"

  @javascript
  Scenario: Import a question starting from quiz in Moodle ≥ 4.3.
    Given the site is running Moodle version 4.3 or higher
    When I am on the "Quiz 1" "mod_quiz > Edit" page logged in as "teacher"
    When I open the "last" add to quiz menu
    And I follow "a new question"
    And I set the field "item_qtype_stack" to "1"
    And I press "submitbutton"
    And I click on "STACK question library" "link"
    Then I should see "Test questions"
    And I should not see "Question variables"
    And I click on "Calculus-Refresher" "button"
    And I click on "CR_Diff_02" "button"
    And I click on "CR-Diff-02-linearity-1-b.xml" "button"
    And I should see "Differentiate \[{@p@}\] with respect to {@v@}. [[input:ans1]]"
    And I click on "Import" "button"
    And I click on "Return to quiz" "link"
    And I open the "last" add to quiz menu
    And I follow "from question bank"
    And I click on "[id^=form_autocomplete_downarrow]" "css_element"
    And I click on "ul[id^=form_autocomplete_suggestions] li:nth-child(3)" "css_element"
    And I click on "Apply filters" "button"
    And I should see "CR-Diff-02-linearity-1.b"

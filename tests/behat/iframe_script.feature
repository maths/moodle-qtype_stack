@qtype @qtype_stack @javascript
Feature: Render iframe scripts in question previews and comments
  As a teacher
  I need embedded scripts to run when inspecting a question
  So that interactive content also works in the comments modal

  Background:
    Given I set up STACK using the PHPUnit configuration
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "users" exist:
      | username | firstname |
      | teacher  | Teacher   |
    And the following "course enrolments" exist:
      | user    | course | role           |
      | teacher | C1     | editingteacher |
    And the following "activities" exist:
      | activity | name      | course | idnumber |
      | quiz     | Test quiz | C1     | quiz1    |
    And the following "question categories" exist:
      | contextlevel    | reference | name           |
      | Activity module | quiz1     | Test questions |
    And the following "question" exists:
      | questioncategory | Test questions |
      | qtype            | stack          |
      | name             | Iframe script  |
      | template         | test1          |
      | questiontext     | [[iframe]]<p id="script-result">Script has not run</p>[[script type="module"]]document.getElementById("script-result").textContent = "Script executed";[[/script]][[/iframe]] |

  Scenario: Run an iframe script in ordinary question preview
    When I am on the "Iframe script" "core_question > preview" page logged in as teacher
    Then I check the value of iframe element "script-result" is 'Script executed'

  Scenario: Run an iframe script in the question comments modal
    When I am on the "Test quiz" "mod_quiz > question bank" page logged in as teacher
    And I select the STACK question category "Test questions"
    And I click on ".commentcount a" "css_element" in the "Iframe script" "table_row"
    Then I check the value of iframe element "script-result" is 'Script executed'

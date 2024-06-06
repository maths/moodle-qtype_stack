@qtype @qtype_stack
Feature: Test duplicating a quiz containing STACK questions
  As a teacher
  In order re-use my courses containing STACK questions
  I need to be able to backup and restore them

  Background:
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype | name                                     | template               |
      | Test questions   | stack | Dropdown (shuffle)                       | dropdown_input         |
    And the following "activities" exist:
      | activity   | name      | course | idnumber |
      | quiz       | Test quiz | C1     | quiz     |
    And quiz "Test quiz" contains the following questions:
      | Dropdown (shuffle)  | 1 |
    And the following config values are set as admin:
    | config | value |
    | enableasyncbackup | true |

  @javascript
  Scenario: Backup and restore a course containing a STACK question
    When I am logged in as admin
    And I navigate to "Courses > Asynchronous backup/restore" in site administration
    And I click on "Save changes" "button"
    And I am on the "Course 1" course page logged in as admin
    And I backup "Course 1" course using this options:
      | Confirmation | Filename | test_backup.mbz |
    And I restore "test_backup.mbz" backup into a new course using this options:
      | Schema | Course name       | Course 2 |
      | Schema | Course short name | C2       |
    And I am on the "Course 2" "core_question > course question bank" page
    And I choose "Edit question" action for "Dropdown (shuffle)" in the question bank
    Then the following fields match these values:
      | Question name                | Dropdown (shuffle)                               |
      | Question text                | <p>Differentiate {@p@} with respect to \(x\).</p><p>[[input:ans1]]</p><div>[[validation:ans1]]</div> |
      | Specific feedback            | [[feedback:prt1]]                                |
    And I press "Cancel"
    And I choose "Edit question" action for "Dropdown (shuffle)" in the question bank
    Then the following fields match these values:
      | Question name             | Dropdown (shuffle)                               |
      | Question text             | <p>Differentiate {@p@} with respect to \(x\).</p><p>[[input:ans1]]</p><div>[[validation:ans1]]</div> |

@qtype @qtype_stack
Feature: Create, edit STACK questions adding in PRT and saving.
  In order create questions
  As an teacher
  I need to create, and edit STACK questions.

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

  @javascript
  Scenario: Create, preview, test, tidy and edit STACK questions in Moodle ≥ 4.0
    Given the site is running Moodle version 4.0 or higher
    When I am on the "Course 1" "core_question > course question bank" page logged in as "teacher"
    # Create a new question.
    And I add a "STACK" question filling the form with:
      | Question name        | Test STACK question                                                           |
      | Question variables   | p : (x-1)^3;                                                                  |
      | Question text        | Differentiate {@p@} with respect to \(x\). [[input:ans1]] [[validation:ans1]] |
      | Question description | This is a very simple test question.                                          |
      | Model answer         | diff(p,x)                                                                     |
      | SAns                 | ans1                                                                          |
      | TAns                 | diff(p,x)                                                                     |
    Then I should see "Test STACK question"

    When I am on the "Test STACK question" "core_question > edit" page
    Then the following fields match these values:
      | Question name        | Test STACK question                                                           |
      | Question variables   | p : (x-1)^3;                                                                  |
      | Question text        | Differentiate {@p@} with respect to \(x\). [[input:ans1]] [[validation:ans1]] |
      | Question description | This is a very simple test question.                                          |
      | Specific feedback    | [[feedback:prt1]]                                                             |
      | Model answer         | diff(p,x)                                                                     |
      | SAns                 | ans1                                                                          |
      | TAns                 | diff(p,x)                                                                     |
    And I set the following fields to these values:
      | Specific feedback    | [[feedback:prt1]] [[feedback:prt2]] |
    And I press "id_updatebutton"
    Then I should see "This PRT must be set up before the question can be saved."
    And I set the following fields to these values:
      | id_prt2sans_0 | int(ans1,x) |
      | id_prt2tans_0 | p |
    And I press "id_updatebutton"
    Then I should see "Potential response tree: prt2"

    And I set the following fields to these values:
      | Specific feedback    | [[feedback:prt1]] |
    And I press "id_updatebutton"
    Then I should see "This potential response tree is no longer referred to in the question text or specific feedback."
    And I set the following fields to these values:
      | id_prt2prtdeleteconfirm    | true |
    And I press "id_updatebutton"
    Then the following fields match these values:
      | Question name        | Test STACK question                                                           |
      | Question variables   | p : (x-1)^3;                                                                  |
      | Question text        | Differentiate {@p@} with respect to \(x\). [[input:ans1]] [[validation:ans1]] |
      | Question description | This is a very simple test question.                                          |
      | Specific feedback    | [[feedback:prt1]]                                                             |
      | Model answer         | diff(p,x)                                                                     |
      | SAns                 | ans1                                                                          |
      | TAns                 | diff(p,x)                                                                     |

    When I press "Add another node"
    And I press "collapseElement-2"
    Then I should see "Node 2"
    And I set the following fields to these values:
      | id_prt1sans_1 | int(ans1,x) |
      | id_prt1tans_1 | p |
    And I press "id_updatebutton"
    Then I should see "No other nodes in the PRT link to this node."
    And I set the following fields to these values:
      | id_prt1falsenextnode_0 | Node 2 |
    And I press "id_updatebutton"
    And I press "collapseElement-2"
    Then I should see "This potential response tree will become active when the student has answered: ans1"
    Then I should see "ATAlgEquiv(int(ans1,x),p)"

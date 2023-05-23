@qtype @qtype_stack
Feature: Create, preview, test, tidy and edit STACK questions
  In order evaluate students mathematical ability
  As an teacher
  I need to create, preview, test, tidy and edit STACK questions.

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
  Scenario: Create, preview, test, tidy and edit STACK questions in Moodle ≤ 4.2
    Given the site is running Moodle version 4.2 or lower
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

    # Preview it.
    When I am on the "Test STACK question" "core_question > preview" page logged in as teacher
    And I set the following fields to these values:
      | How questions behave | Adaptive          |
      | Marks                | Show mark and max |
    # Moodle changed wording: https://github.com/moodle/moodle/commit/c05a290
    # Language tag $string['restartwiththeseoptions'] in lang/en/question.php.
    # "Start again with these options" > "Save preview options and start again".
    And I press "Start again with these options"
    Then I should see "Differentiate"
    And the state of "Differentiate" question is shown as "Not complete"
    When I set the input "ans1" to "x-1" in the STACK question
    And I wait "2" seconds
    Then I should see "Your last answer was interpreted as follows"
    When I press "Check"
    Then I should see "Incorrect answer."
    And the state of "Differentiate" question is shown as "Not complete"
    When I set the input "ans1" to "3(x-1)^2" in the STACK question
    And I wait "2" seconds
    Then I should see "You seem to be missing * characters"
    When I set the input "ans1" to "3*(x-1)^2" in the STACK question
    And I wait "2" seconds
    And I press "Check"
    Then I should see "Correct answer, well done."
    And the state of "Differentiate" question is shown as "Answer saved"
    And I should see "Mark 0.90 out of 1.00"
    And I should see "Marks for this submission: 1.00/1.00. Accounting for previous tries, this gives 0.90/1.00."

    # Create a question test: two methods.
    When I follow "Question is missing tests or variants"
    Then I should see "This question does not use randomisation."
    When I press "Add test case assuming the teacher's input gets full marks."
    Then I should see "Automatically adding one test case assuming the teacher's input gets full marks."
    And I should see "Test case 1"
    And I should see "All tests passed!"
    When I press "Delete this test case."
    Then I should see "Are you sure you want to delete test case 1 for question Test STACK question"
    When I press "Continue"
    Then I should see "Question is missing tests or variants. No test cases have been added yet."
    When I press "Add a test case..."
    And I set the following fields to these values:
      | ans1 | x - 1 |
    And I press "Fill in the rest of the form to make a passing test-case"
    Then the following fields match these values:
      | ans1        | x - 1    |
      | Score       | 0        |
      | Penalty     |          |
      | Answer note | prt1-1-F |
    When I press "Create test case"
    Then I should see "All tests passed!"
    And I should see "Test case 1"
    And following "Export as Moodle XML" should download between "3700" and "4000" bytes

    # Use the tidy question script.
    And I follow "Tidy inputs and PRTs"
    And I set the following fields to these values:
      | New name for 'ans1' | ans |
      | New name for 'prt1' | prt |
      | New name for '1'    | 2   |
    And I press "Rename parts of the question"
    And I follow "STACK question dashboard"
    Then I should see "All tests passed!"
    When I follow "Preview"

    # Edit the question, verify the form field contents, then change some.
    When I am on the "Test STACK question" "core_question > edit" page
    Then the following fields match these values:
      | Question name        | Test STACK question                                                         |
      | Question variables   | p : (x-1)^3;                                                                |
      | Question text        | Differentiate {@p@} with respect to \(x\). [[input:ans]] [[validation:ans]] |
      | Question description | This is a very simple test question.                                        |
      | Specific feedback    | [[feedback:prt]]                                                            |
      | Model answer         | diff(p,x)                                                                   |
      | SAns                 | ans                                                                         |
      | TAns                 | diff(p,x)                                                                   |
    And I set the following fields to these values:
      | Question name | Edited question name |
    And I press "id_submitbutton"
    Then I should see "Edited question name"

  @javascript
  Scenario: Create, preview, test, tidy and edit STACK questions in Moodle ≥ 4.3
    Given the site is running Moodle version 4.3 or higher
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

    # Preview it.
    When I am on the "Test STACK question" "core_question > preview" page logged in as teacher
    And I set the following fields to these values:
      | How questions behave | Adaptive          |
      | Marks                | Show mark and max |
    And I press "Save preview options and start again"
    Then I should see "Differentiate"
    And the state of "Differentiate" question is shown as "Not complete"
    When I set the input "ans1" to "x-1" in the STACK question
    And I wait "2" seconds
    Then I should see "Your last answer was interpreted as follows"
    When I press "Check"
    Then I should see "Incorrect answer."
    And the state of "Differentiate" question is shown as "Not complete"
    When I set the input "ans1" to "3(x-1)^2" in the STACK question
    And I wait "2" seconds
    Then I should see "You seem to be missing * characters"
    When I set the input "ans1" to "3*(x-1)^2" in the STACK question
    And I wait "2" seconds
    And I press "Check"
    Then I should see "Correct answer, well done."
    And the state of "Differentiate" question is shown as "Answer saved"
    And I should see "Mark 0.90 out of 1.00"
    And I should see "Marks for this submission: 1.00/1.00. Accounting for previous tries, this gives 0.90/1.00."

    # Create a question test: two methods.
    When I follow "Question is missing tests or variants"
    Then I should see "This question does not use randomisation."
    When I press "Add test case assuming the teacher's input gets full marks."
    Then I should see "Automatically adding one test case assuming the teacher's input gets full marks."
    And I should see "Test case 1"
    And I should see "All tests passed!"
    When I press "Delete this test case."
    Then I should see "Are you sure you want to delete test case 1 for question Test STACK question"
    When I press "Continue"
    Then I should see "Question is missing tests or variants. No test cases have been added yet."
    When I press "Add a test case..."
    And I set the following fields to these values:
      | ans1 | x - 1 |
    And I press "Fill in the rest of the form to make a passing test-case"
    Then the following fields match these values:
      | ans1        | x - 1    |
      | Score       | 0        |
      | Penalty     |          |
      | Answer note | prt1-1-F |
    When I press "Create test case"
    Then I should see "All tests passed!"
    And I should see "Test case 1"
    And following "Export as Moodle XML" should download between "3700" and "4000" bytes

    # Use the tidy question script.
    And I follow "Tidy inputs and PRTs"
    And I set the following fields to these values:
      | New name for 'ans1' | ans |
      | New name for 'prt1' | prt |
      | New name for '1'    | 2   |
    And I press "Rename parts of the question"
    And I follow "STACK question dashboard"
    Then I should see "All tests passed!"
    When I follow "Preview"

    # Edit the question, verify the form field contents, then change some.
    When I am on the "Test STACK question" "core_question > edit" page
    Then the following fields match these values:
      | Question name        | Test STACK question                                                         |
      | Question variables   | p : (x-1)^3;                                                                |
      | Question text        | Differentiate {@p@} with respect to \(x\). [[input:ans]] [[validation:ans]] |
      | Question description | This is a very simple test question.                                        |
      | Specific feedback    | [[feedback:prt]]                                                            |
      | Model answer         | diff(p,x)                                                                   |
      | SAns                 | ans                                                                         |
      | TAns                 | diff(p,x)                                                                   |
    And I set the following fields to these values:
      | Question name | Edited question name |
    And I press "id_submitbutton"
    Then I should see "Edited question name"

  @javascript
  Scenario: Test duplicating a STACK question keeps the deployed variants and question tests
    Given the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Default for C1 |
    And the following "questions" exist:
      | questioncategory | qtype | name             | template |
      | Default for C1   | stack | Question to copy | test1    |
    And the following "qtype_stack > Deployed variants" exist:
      | question         | seed |
      | Question to copy | 42   |
    And the following "qtype_stack > Question tests" exist:
      | question         | ans1 | PotResTree_1 score | PotResTree_1 penalty | PotResTree_1 note |
      | Question to copy | ta+C | 1                  | 0                    | PotResTree_1-1-T  |
    When I am on the "Course 1" "core_question > course question bank" page logged in as "teacher"
    And I choose "Duplicate" action for "Question to copy" in the question bank
    And I press "id_submitbutton"
    And I choose "STACK question dashboard" action for "Question to copy (copy)" in the question bank
    Then I should see "Deployed variants (1)"
    And I should see "42"
    And I should see "Test case 1"
    And I should see "All tests passed!"

  @javascript
  Scenario: Editing a STACK question (to make a new version) keeps the deployed variants and question tests
    Given the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Default for C1 |
    And the following "questions" exist:
      | questioncategory | qtype | name             | template |
      | Default for C1   | stack | Question to edit | test1    |
    And the following "qtype_stack > Deployed variants" exist:
      | question         | seed |
      | Question to edit | 42   |
    And the following "qtype_stack > Question tests" exist:
      | question         | ans1 | PotResTree_1 score | PotResTree_1 penalty | PotResTree_1 note |
      | Question to edit | ta+C | 1                  | 0                    | PotResTree_1-1-T  |
    When I am on the "Question to edit" "core_question > edit" page logged in as "teacher"
    And I set the field "Question name" to "Edited question"
    And I press "id_submitbutton"
    And I choose "STACK question dashboard" action for "Edited question" in the question bank
    Then I should see "Deployed variants (1)"
    And I should see "42"
    And I should see "Test case 1"
    And I should see "All tests passed!"

  @javascript
  Scenario: Add a second test, and deploy variants from a list.
    When I am on the "Course 1" "core_question > course question bank" page logged in as "teacher"
    # Create a new question.
    And I add a "STACK" question filling the form with:
      | Question name      | Test STACK rand question                                                      |
      | Question variables | p : (x-rand(100))^3;                                                          |
      | Question text      | Differentiate {@p@} with respect to \(x\). [[input:ans1]] [[validation:ans1]] |
      | Question note      | {@p@}                                                                         |
      | Model answer       | diff(p,x)                                                                     |
      | SAns               | ans1                                                                          |
      | TAns               | diff(p,x)                                                                     |
    Then I should see "Test STACK rand question"

    When I am on the "Test STACK rand question" "core_question > preview" page logged in as teacher
    Then I should see "Question is missing tests or variants."
    When I follow "Question is missing tests or variants."
    Then I should see "Question is missing tests or variants"
    When I press "Add test case assuming the teacher's input gets full marks."
    Then I should see "Automatically adding one test case assuming the teacher's input gets full marks."
    And I should see "Test case 1"
    And I should see "All tests passed!"
    And I should see "No variants of this question have been deployed yet."
    And I set the field "seedfield" to "1729"
    And I press "Switch to variant"
    Then I should see "Question is missing tests or variants."
    And I should see "Showing undeployed variant: 1729"
    Then I press "Deploy single variant"
    And I should see "Deployed variants (1)"
    When I set the field "seedfield" to "1730"
    And I press "Switch to variant"
    Then I should see "Showing undeployed variant: 1730"
    Then I press "Deploy single variant"
    And I should see "Deployed variants (2)"
    When I set the field "seedfield" to "1731"
    And I press "Switch to variant"
    Then I should see "Showing undeployed variant: 1731"
    Then I press "Deploy single variant"
    And I should see "Deployed variants (3)"
    And I should see "Question tests for seed 1731: All tests passed!"

    # Add in a second test case.
    When I press "Add another test case..."
    And I set the following fields to these values:
      | ans1 | x - 1 |
    And I press "Fill in the rest of the form to make a passing test-case"
    Then the following fields match these values:
      | ans1        | x - 1    |
      | Score       | 0        |
      | Penalty     |          |
      | Answer note | prt1-1-F |
    When I press "Create test case"
    Then I should see "All tests passed!"
    And I should see "Test case 2"

    # Run all tests on all variants
    When I press "Run all tests on all deployed variants (slow)"
    And I should see "Deployed variants (3)"
    And I should see "2 passes and 0 failures."
    And I should see "Question tests for seed 1731: All tests passed!"

    # Remove all variants and deploy from list
    When I press "Undeploy all variants"
    Then I should see "Question is missing tests or variants"

    When I set the field "deployfromlist" to "10,11,12,13,11"
    Then I press "Remove variants and re-deploy from list"
    And I should see "An error was detected in your list of integers, and so no changes were made to the list of deployed variants."

    When I set the field "deployfromlist" to "10,11,12,13"
    Then I press "Remove variants and re-deploy from list"
    And I should see "Deployed variants (4)"
    And I should see "All tests passed!"
    When I press "Run all tests on all deployed variants (slow)"
    And I should see "Deployed variants (4)"
    And I should see "All tests passed!"
    And I should see "2 passes and 0 failures."

    # Test the edit link in the STACK question dashboard.
    When I follow "Edit question"
    # Edit the question to create duplicate question notes.
    Then I should see "Editing a STACK question"
    When I set the field "questionvariables" to "n1:rand(100); p:(x-1)^4;"
    And I set the field "Question name" to "Test STACK rand question v2"
    And I press "Save changes"

    When I am on the "Test STACK rand question v2" "core_question > preview" page logged in as teacher
    Then I should see "STACK question dashboard"
    When I follow "STACK question dashboard"
    Then I should see "Deployed variants (4)"
    And I should see "duplicate notes"

    # Undeploy and try to create random variants, which won't work.
    When I press "Undeploy all variants"
    Then I should see "Question is missing tests or variants"
    And I set the field "deploymany" to "3"
    And I press "Deploy # of variants:"
    Then I should see "Deployed variants (1)"
    And I should see "Number of new variants successfully created, tested and deployed: 1."
    And I should see "Too many repeated existing question notes were generated."
    And I should see "A variant matching this Question note is already deployed."

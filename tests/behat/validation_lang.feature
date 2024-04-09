@qtype @qtype_stack @javascript
Feature: Test validation language is correct.
  As a teacher
  In order to check answer validation language is correct i need to update language settings
  I need to preview the question

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
      | Test questions   | stack | Algebraic input                          | algebraic_input        |
    And the following "language pack" exists:
      | language | fr | es |

  Scenario: Test validation language
    And I am on the "Algebraic input" "core_question > preview" page logged in as teacher
    And I wait until "Your last answer was interpreted as follows" "text" does not exist
    And I set the input "ans1" to "a*b" in the STACK question
    # Site default language is English.
    And I wait until "Your last answer was interpreted as follows" "text" exists
    When I am on the "Course 1" course page logged in as teacher
    And I should see "Settings"
    And I follow "Preferences" in the user menu
    And I follow "Preferred language"
    # Change preferred language to French.
    And I set the field "Preferred language" to "fr"
    And I press "Save changes"
    And I am on the "Algebraic input" "core_question > preview" page logged in as teacher
    And I wait until "Votre dernière réponse a été interprétée comme suit" "text" does not exist
    And I set the input "ans1" to "a*b" in the STACK question
    And I wait until "Votre dernière réponse a été interprétée comme suit" "text" exists
    And I am on "Course 1" course homepage
    And I should see "Paramètres"
    # Change course language to Spanish.
    And I navigate to "Paramètres" in current page administration
    When I set the following fields to these values:
      | id_lang | es |
    And I press "Enregistrer et afficher"
    And I am on the "Algebraic input" "core_question > preview" page logged in as teacher
    And I wait until "Tu respuesta fue interpretado como" "text" does not exist
    And I set the input "ans1" to "a*b" in the STACK question
    And I wait until "Tu respuesta fue interpretado como" "text" exists

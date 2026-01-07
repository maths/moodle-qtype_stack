@qtype @qtype_stack
Feature: Create and edit STACK metadata
  In order catalogue questions effectively
  As an teacher
  I need to create and edit metadata for STACK questions

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

  @javascript
  Scenario: Create and edit STACK metadata
    When I am on the "Algebraic input" "core_question > edit" page logged in as teacher
    And I click on "View and edit full metadata" "button"
    And I should see "STACK metadata is stored as a JSON object."
    And I set the field "smdi_0_creator_firstName" in the "#qtype-stack-metadata-content" "css_element" to "Edmund"
    And I set the field "smdi_0_creator_lastName" in the "#qtype-stack-metadata-content" "css_element" to "Farrow"
    And I set the field "smdi_0_creator_institution" in the "#qtype-stack-metadata-content" "css_element" to "UoE"
    And I set the field "smdi_0_creator_year" in the "#qtype-stack-metadata-content" "css_element" to "2025"
    And I click on "Add contributor" "button"
    And I wait until "smdi_1_contributor_firstName" "field" exists
    And I set the field "smdi_1_contributor_firstName" in the "#qtype-stack-metadata-content" "css_element" to "Bob"
    And I set the field "smdi_1_contributor_lastName" in the "#qtype-stack-metadata-content" "css_element" to "Smith"
    And I set the field "smdi_1_contributor_institution" in the "#qtype-stack-metadata-content" "css_element" to "MIT"
    And I set the field "smdi_1_contributor_year" in the "#qtype-stack-metadata-content" "css_element" to "2026"
    And I click on "Add language" "button"
    And I wait until "smdi_1_language_value" "field" exists
    And I set the field "smdi_1_language_value" in the "#qtype-stack-metadata-content" "css_element" to "en"
    And I set the field "smdi_0_isPartOf_value" in the "#qtype-stack-metadata-content" "css_element" to "HELM"
    And I open the autocomplete suggestions list in the "#qtype-stack-metadata-content" "css_element"
    Then "[data-value='cc-nc-4.0']" "css_element" should be visible
    And I click on "[data-value='cc-nc-4.0']" "css_element"
    And I click on "Add scope" "button"
    And I wait until "smdi_1_additional_scope" "field" exists
    And I set the field "smdi_1_additional_scope" in the "#qtype-stack-metadata-content" "css_element" to "Added data"
    And I set the field "smdi_1_additional_property" in the "#qtype-stack-metadata-content" "css_element" to "Dog info"
    And I set the field "smdi_1_additional_qualifier" in the "#qtype-stack-metadata-content" "css_element" to "Breed"
    And I set the field "smdi_1_additional_value" in the "#qtype-stack-metadata-content" "css_element" to "Al$%&^"
    And I click on "Validate and close" "button"
    And I check the hidden input "metadata" is '{"creator":{"firstName":"Edmund","lastName":"Farrow","institution":"UoE","year":"2025"},"contributor":[{"firstName":"Bob","lastName":"Smith","institution":"MIT","year":"2026"}],"language":["en"],"isPartOf":"HELM","license":"cc-nc-4.0","additional":[{"scope":"Added data","property":"Dog info","qualifier":"Breed","value":"Al$%&^"}]}'
    And I click on "View and edit full metadata" "button"
    And I should see "STACK metadata is stored as a JSON object."
    And I click on "Add me as a contributor" "button"
    And I click on "Add language" "button"
    And I wait until "smdi_2_language_value" "field" exists
    And I set the field "smdi_2_language_value" in the "#qtype-stack-metadata-content" "css_element" to "fr"
    And I click on "Add property" "button"
    And I wait until "smdi_2_additional_property" "field" exists
    And I set the field "smdi_2_additional_property" in the "#qtype-stack-metadata-content" "css_element" to "Cat info"
    And I set the field "smdi_2_additional_qualifier" in the "#qtype-stack-metadata-content" "css_element" to "Breed"
    And I set the field "smdi_2_additional_value" in the "#qtype-stack-metadata-content" "css_element" to "Tabby"
    And I click on "Add scope" "button"
    And I wait until "smdi_3_additional_scope" "field" exists
    And I set the field "smdi_3_additional_scope" in the "#qtype-stack-metadata-content" "css_element" to "More data"
    And I set the field "smdi_3_additional_property" in the "#qtype-stack-metadata-content" "css_element" to "Question"
    And I set the field "smdi_3_additional_qualifier" in the "#qtype-stack-metadata-content" "css_element" to "Type"
    And I set the field "smdi_3_additional_value" in the "#qtype-stack-metadata-content" "css_element" to "MC"
    And I click on "smd_property_3_add" "button"
    And I wait until "smdi_4_additional_property" "field" exists
    And I set the field "smdi_4_additional_property" in the "#qtype-stack-metadata-content" "css_element" to "More"
    And I set the field "smdi_4_additional_qualifier" in the "#qtype-stack-metadata-content" "css_element" to "Things"
    And I set the field "smdi_4_additional_value" in the "#qtype-stack-metadata-content" "css_element" to "AAA"
    And I click on "Validate and close" "button"
    And I check the hidden input "metadata" is '{"creator":{"firstName":"Edmund","lastName":"Farrow","institution":"UoE","year":"2025"},"contributor":[{"firstName":"Bob","lastName":"Smith","institution":"MIT","year":"2026"},{"firstName":"Teacher","lastName":"Lastname1","institution":"","year":"XXXX"}],"language":["en","fr"],"isPartOf":"HELM","license":"cc-nc-4.0","additional":[{"scope":"Added data","property":"Dog info","qualifier":"Breed","value":"Al$%&^"},{"scope":"Added data","property":"Cat info","qualifier":"Breed","value":"Tabby"},{"scope":"More data","property":"Question","qualifier":"Type","value":"MC"},{"scope":"More data","property":"More","qualifier":"Things","value":"AAA"}]}'
    And I click on "View and edit full metadata" "button"
    And I set the field "smdi_3_additional_scope" in the "#qtype-stack-metadata-content" "css_element" to "Changed"
    And I click on "Validate and close" "button"
    And I check the hidden input "metadata" is '{"creator":{"firstName":"Edmund","lastName":"Farrow","institution":"UoE","year":"2025"},"contributor":[{"firstName":"Bob","lastName":"Smith","institution":"MIT","year":"2026"},{"firstName":"Teacher","lastName":"Lastname1","institution":"","year":"XXXX"}],"language":["en","fr"],"isPartOf":"HELM","license":"cc-nc-4.0","additional":[{"scope":"Added data","property":"Dog info","qualifier":"Breed","value":"Al$%&^"},{"scope":"Added data","property":"Cat info","qualifier":"Breed","value":"Tabby"},{"scope":"Changed","property":"Question","qualifier":"Type","value":"MC"},{"scope":"Changed","property":"More","qualifier":"Things","value":"AAA"}]}'
    And I click on "View and edit full metadata" "button"
    And I should see "STACK metadata is stored as a JSON object."
    And I click on "smd_contributor_1_delete" "button"
    And I should not see "Smith"
    And I set the field "smdi_1_language_value" in the "#qtype-stack-metadata-content" "css_element" to "en-del"
    And I click on "smd_language_1_delete" "button"
    And I should not see "en-del"
    And I click on "smd_additional_2_delete" "button"
    And I should not see "Cat info"
    And I click on "Add language" "button"
    And I wait until "smdi_3_language_value" "field" exists
    And I set the field "smdi_3_language_value" in the "#qtype-stack-metadata-content" "css_element" to "it"
    And I click on "Validate and close" "button"
    And I check the hidden input "metadata" is '{"creator":{"firstName":"Edmund","lastName":"Farrow","institution":"UoE","year":"2025"},"contributor":[{"firstName":"Teacher","lastName":"Lastname1","institution":"","year":"XXXX"}],"language":["fr","it"],"isPartOf":"HELM","license":"cc-nc-4.0","additional":[{"scope":"Added data","property":"Dog info","qualifier":"Breed","value":"Al$%&^"},{"scope":"Changed","property":"Question","qualifier":"Type","value":"MC"},{"scope":"Changed","property":"More","qualifier":"Things","value":"AAA"}]}'
    And I click on "View and edit full metadata" "button"
    And I should see "STACK metadata is stored as a JSON object."
    And I click on "smd_scope_1_delete" "button"
    And I should not see "Added data"
    And I click on "smd_property_3_add" "button"
    And I wait until "smdi_5_additional_property" "field" exists
    And I click on "Validate and close" "button"
    And I should see "Required" in the "#smde_5_additional_property_error" "css_element"
    And I click on "smd_additional_5_delete" "button"
    And I click on "Validate and close" "button"
    And I check the hidden input "metadata" is '{"creator":{"firstName":"Edmund","lastName":"Farrow","institution":"UoE","year":"2025"},"contributor":[{"firstName":"Teacher","lastName":"Lastname1","institution":"","year":"XXXX"}],"language":["fr","it"],"isPartOf":"HELM","license":"cc-nc-4.0","additional":[{"scope":"Changed","property":"Question","qualifier":"Type","value":"MC"},{"scope":"Changed","property":"More","qualifier":"Things","value":"AAA"}]}'
    And I click on "View and edit full metadata" "button"
    And I should see "STACK metadata is stored as a JSON object."
    And I set the field "id_metadata_json" to multiline:
    """
    {
      "creator": {
          "firstName": "Bob",
          "lastName": "Smith",
          "institution": "MIT",
          "year": "2024"
      },
      "contributor": [
          {
              "firstName": "Mike",
              "lastName": "Jones",
              "institution": "Bath",
              "year": "2023"
          }
      ],
      "language": [
          "en"
      ],
      "isPartOf": "Everything",
      "license": "cc-nc-4.1",
      "additional": [
          {
              "scope": "Added",
              "property": "Cat",
              "qualifier": "Breed",
              "value": "Al$%&^"
          },
          {
              "scope": "Added",
              "property": "Dog",
              "qualifier": "Teeth",
              "value": "50"
          },
          {
              "scope": "Added too",
              "property": "Fish",
              "qualifier": "Gills",
              "value": "2"
          }
      ]
    }
    """
    And I click on "Update inputs from JSON" "button"
    And I click on "Validate and close" "button"
    And I check the hidden input "metadata" is '{"creator":{"firstName":"Bob","lastName":"Smith","institution":"MIT","year":"2024"},"contributor":[{"firstName":"Mike","lastName":"Jones","institution":"Bath","year":"2023"}],"language":["en"],"isPartOf":"Everything","license":"cc-nc-4.1","additional":[{"scope":"Added","property":"Cat","qualifier":"Breed","value":"Al$%&^"},{"scope":"Added","property":"Dog","qualifier":"Teeth","value":"50"},{"scope":"Added too","property":"Fish","qualifier":"Gills","value":"2"}]}'
    And I click on "View and edit full metadata" "button"
    And I should see "STACK metadata is stored as a JSON object."
    And I set the field "id_metadata_json" to multiline:
    """
    {
      "creator": {
          "firstName": "Bo1b",
          "lastName": "Smi1th",
          "institution": "MI1T",
          "year": "2024"
      },
      "contributor": [
          {
              "firstName": "Mi1ke",
              "lastName": "Jon1es",
              "institution": "1ath",
              "year": "2023"
          },
          {
              "firstName": "Helen",
              "lastName": "Lowell",
              "institution": "Bath",
              "year": "2023"
          }
      ],
      "language": [
          "edfsedn"
      ],
      "isPartOf": "Eve1rything",
      "license": "public",
      "additional": [
          {
              "scope": "Adfded",
              "property": "Cfat",
              "qualifier": "Bfreed",
              "value": "Al$%f&^"
          },
          {
              "scope": "Adfded",
              "property": "Dfog",
              "qualifier": "Tefeth",
              "value": "5f0"
          },
          {
              "scope": "Addfed too",
              "property": "Fifsh",
              "qualifier": "Giflls",
              "value": "2f"
          }
      ]
    }
    """
    And I click on "Update inputs from JSON" "button"
    And I wait until "smdi_2_contributor_firstName" "field" exists
    And I click on "Revert current changes" "button"
    And I should not see "Lowell"
    And I click on "Validate and close" "button"
    And I check the hidden input "metadata" is '{"creator":{"firstName":"Bob","lastName":"Smith","institution":"MIT","year":"2024"},"contributor":[{"firstName":"Mike","lastName":"Jones","institution":"Bath","year":"2023"}],"language":["en"],"isPartOf":"Everything","license":"cc-nc-4.1","additional":[{"scope":"Added","property":"Cat","qualifier":"Breed","value":"Al$%&^"},{"scope":"Added","property":"Dog","qualifier":"Teeth","value":"50"},{"scope":"Added too","property":"Fish","qualifier":"Gills","value":"2"}]}'
    And I click on "id_updatebutton"
    And I should see "Version 2"
    And I check the hidden input "metadata" is '{"creator":{"firstName":"Bob","lastName":"Smith","institution":"MIT","year":"2024"},"contributor":[{"firstName":"Mike","lastName":"Jones","institution":"Bath","year":"2023"}],"language":["en"],"isPartOf":"Everything","license":"cc-nc-4.1","additional":[{"scope":"Added","property":"Cat","qualifier":"Breed","value":"Al$%&^"},{"scope":"Added","property":"Dog","qualifier":"Teeth","value":"50"},{"scope":"Added too","property":"Fish","qualifier":"Gills","value":"2"}]}'

  @javascript
  Scenario: New question metadata
    When I am on the "Course 1" "core_question > course question bank" page logged in as "teacher"
    And I click on "Create a new question" "button"
    And I set the field "item_qtype_stack" to "1"
    And I press "submitbutton"
    And I click on "View and edit full metadata" "button"
    And I should see "STACK metadata is stored as a JSON object."
    And I click on "Validate and close" "button"
    And I check the hidden input "metadata" is '{"creator":{"firstName":"Teacher","lastName":"Lastname1","institution":"","year":"XXXX"},"contributor":[],"language":["en"],"license":"unknown","isPartOf":"","additional":[]}'


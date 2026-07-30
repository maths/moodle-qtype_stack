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
    And I should see "Freeform metadata"
    And I click on "stack-metadata-edit-switch" "checkbox"
    And I click on "smd_author_0_add" "button"
    And I wait until "smdi_1_author_firstName" "field" exists
    And the focused element should be "#smdi_1_author_firstName" "css_element"
    And I set the field "smdi_1_author_firstName" in the "#qtype-stack-metadata-content" "css_element" to "Edmund"
    And I set the field "smdi_1_author_lastName" in the "#qtype-stack-metadata-content" "css_element" to "Farrow"
    And I set the field "smdi_1_author_institution" in the "#qtype-stack-metadata-content" "css_element" to "UoE"
    And I set the field "smdi_1_author_year" in the "#qtype-stack-metadata-content" "css_element" to "2025"
    And I click on "smd_author_0_add" "button"
    And I wait until "smdi_2_author_firstName" "field" exists
    And the focused element should be "#smdi_2_author_firstName" "css_element"
    And I set the field "smdi_2_author_firstName" in the "#qtype-stack-metadata-content" "css_element" to "Bob"
    And I set the field "smdi_2_author_lastName" in the "#qtype-stack-metadata-content" "css_element" to "Smith"
    And I set the field "smdi_2_author_institution" in the "#qtype-stack-metadata-content" "css_element" to "MIT"
    And I set the field "smdi_2_author_year" in the "#qtype-stack-metadata-content" "css_element" to "2026"
    And I click on "smd_language_0_add" "button"
    And I wait until "smdi_1_language_value" "field" exists
    And the focused element should be "#smdi_1_language_value" "css_element"
    And I set the field "smdi_1_language_value" in the "#qtype-stack-metadata-content" "css_element" to "en"
    And I set the field "smdi_0_isPartOf_value" in the "#qtype-stack-metadata-content" "css_element" to "HELM"
    And I open the autocomplete suggestions list in the "#qtype-stack-metadata-content" "css_element"
    Then "[data-value='cc-nc-4.0']" "css_element" should be visible
    And I click on "[data-value='cc-nc-4.0']" "css_element"
    And I click on "smd_scope_0_add" "button"
    And I wait until "smdi_1_additional_scope" "field" exists
    And the focused element should be "#smdi_1_additional_scope" "css_element"
    And I set the field "smdi_1_additional_scope" in the "#qtype-stack-metadata-content" "css_element" to "Added data"
    And I set the field "smdi_1_additional_property" in the "#qtype-stack-metadata-content" "css_element" to "Dog info"
    And I set the field "smdi_1_additional_qualifier" in the "#qtype-stack-metadata-content" "css_element" to "Breed"
    And I set the field "smdi_1_additional_value" in the "#qtype-stack-metadata-content" "css_element" to "Al$%&^"
    And I click on "Validate and close" "button"
    And I check the hidden input "metadata" is '{"author":[{"firstName":"Edmund","lastName":"Farrow","institution":"UoE","year":"2025"},{"firstName":"Bob","lastName":"Smith","institution":"MIT","year":"2026"}],"language":["en"],"isPartOf":"HELM","license":"cc-nc-4.0","additional":{"Added data":{"Dog info":{"Breed":"Al$%&^"}}},"freeform":{}}'
    And I click on "View and edit full metadata" "button"
    And I should see "Freeform metadata"
    And I click on "stack-metadata-edit-switch" "checkbox"
    And I click on "stack-metadata-make-author" "button"
    And I wait until "smdi_3_author_firstName" "field" exists
    And the focused element should be "#smdi_3_author_firstName" "css_element"
    And I click on "smd_language_0_add" "button"
    And I wait until "smdi_2_language_value" "field" exists
    And the focused element should be "#smdi_2_language_value" "css_element"
    And I set the field "smdi_2_language_value" in the "#qtype-stack-metadata-content" "css_element" to "fr"
    And I click on "smd_property_1_add" "button"
    And I wait until "smdi_2_additional_property" "field" exists
    And the focused element should be "#smdi_2_additional_property" "css_element"
    And I set the field "smdi_2_additional_property" in the "#qtype-stack-metadata-content" "css_element" to "Cat info"
    And I set the field "smdi_2_additional_qualifier" in the "#qtype-stack-metadata-content" "css_element" to "Breed"
    And I set the field "smdi_2_additional_value" in the "#qtype-stack-metadata-content" "css_element" to "Tabby"
    And I click on "smd_scope_0_add" "button"
    And I wait until "smdi_3_additional_scope" "field" exists
    And the focused element should be "#smdi_3_additional_scope" "css_element"
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
    And I check the hidden input "metadata" is '{"author":[{"firstName":"Edmund","lastName":"Farrow","institution":"UoE","year":"2025"},{"firstName":"Bob","lastName":"Smith","institution":"MIT","year":"2026"},{"firstName":"Teacher","lastName":"Lastname1","institution":"","year":"XXXX"}],"language":["en","fr"],"isPartOf":"HELM","license":"cc-nc-4.0","additional":{"Added data":{"Dog info":{"Breed":"Al$%&^"},"Cat info":{"Breed":"Tabby"}},"More data":{"Question":{"Type":"MC"},"More":{"Things":"AAA"}}},"freeform":{}}'
    And I click on "View and edit full metadata" "button"
    And I click on "stack-metadata-edit-switch" "checkbox"
    And I set the field "smdi_3_additional_scope" in the "#qtype-stack-metadata-content" "css_element" to "Changed"
    And I click on "Validate and close" "button"
    And I check the hidden input "metadata" is '{"author":[{"firstName":"Edmund","lastName":"Farrow","institution":"UoE","year":"2025"},{"firstName":"Bob","lastName":"Smith","institution":"MIT","year":"2026"},{"firstName":"Teacher","lastName":"Lastname1","institution":"","year":"XXXX"}],"language":["en","fr"],"isPartOf":"HELM","license":"cc-nc-4.0","additional":{"Added data":{"Dog info":{"Breed":"Al$%&^"},"Cat info":{"Breed":"Tabby"}},"Changed":{"Question":{"Type":"MC"},"More":{"Things":"AAA"}}},"freeform":{}}'
    And I click on "View and edit full metadata" "button"
    And I should see "Freeform metadata"
    And I click on "stack-metadata-edit-switch" "checkbox"
    And I click on "smd_author_2_delete" "button"
    And the focused element should be "#smd_author_0_add" "css_element"
    And I should not see "Smith"
    And I set the field "smdi_1_language_value" in the "#qtype-stack-metadata-content" "css_element" to "en-del"
    And I click on "smd_language_1_delete" "button"
    And the focused element should be "#smd_language_0_add" "css_element"
    And I should not see "en-del"
    And I click on "smd_additional_2_delete" "button"
    And the focused element should be "#smd_property_1_add" "css_element"
    And I should not see "Cat info"
    And I click on "smd_language_0_add" "button"
    And I wait until "smdi_3_language_value" "field" exists
    And the focused element should be "#smdi_3_language_value" "css_element"
    And I set the field "smdi_3_language_value" in the "#qtype-stack-metadata-content" "css_element" to "it"
    And I click on "Validate and close" "button"
    And I check the hidden input "metadata" is '{"author":[{"firstName":"Edmund","lastName":"Farrow","institution":"UoE","year":"2025"},{"firstName":"Teacher","lastName":"Lastname1","institution":"","year":"XXXX"}],"language":["fr","it"],"isPartOf":"HELM","license":"cc-nc-4.0","additional":{"Added data":{"Dog info":{"Breed":"Al$%&^"}},"Changed":{"Question":{"Type":"MC"},"More":{"Things":"AAA"}}},"freeform":{}}'
    And I click on "View and edit full metadata" "button"
    And I should see "Freeform metadata"
    And I click on "stack-metadata-edit-switch" "checkbox"
    And I click on "smd_scope_1_delete" "button"
    And the focused element should be "#smd_scope_0_add" "css_element"
    And I should not see "Added data"
    And I click on "smd_property_3_add" "button"
    And I wait until "smdi_5_additional_property" "field" exists
    And I click on "Validate and close" "button"
    And I should see "Required" in the "#smde_5_additional_property_error" "css_element"
    And I click on "smd_additional_5_delete" "button"
    And I click on "Validate and close" "button"
    And I check the hidden input "metadata" is '{"author":[{"firstName":"Edmund","lastName":"Farrow","institution":"UoE","year":"2025"},{"firstName":"Teacher","lastName":"Lastname1","institution":"","year":"XXXX"}],"language":["fr","it"],"isPartOf":"HELM","license":"cc-nc-4.0","additional":{"Changed":{"Question":{"Type":"MC"},"More":{"Things":"AAA"}}},"freeform":{}}'
    And I click on "View and edit full metadata" "button"
    And I should see "Freeform metadata"
    And I click on "stack-metadata-edit-switch" "checkbox"
    And I click on "smd_scope_0_add" "button"
    And I wait until "smdi_5_additional_scope" "field" exists
    And I set the field "smdi_5_additional_scope" in the "#qtype-stack-metadata-content" "css_element" to "Another Scope"
    And I set the field "smdi_5_additional_property" in the "#qtype-stack-metadata-content" "css_element" to "Question2"
    And I set the field "smdi_5_additional_qualifier" in the "#qtype-stack-metadata-content" "css_element" to "Type2"
    And I set the field "smdi_5_additional_value" in the "#qtype-stack-metadata-content" "css_element" to "MC2"
    And I click on "smd_property_5_add" "button"
    And I wait until "smdi_6_additional_property" "field" exists
    And I set the field "smdi_6_additional_property" in the "#qtype-stack-metadata-content" "css_element" to "More data"
    And I set the field "smdi_6_additional_qualifier" in the "#qtype-stack-metadata-content" "css_element" to "Things2"
    And I set the field "smdi_6_additional_value" in the "#qtype-stack-metadata-content" "css_element" to "AAA2"
    And I click on "smd_property_5_add" "button"
    And I wait until "smdi_7_additional_property" "field" exists
    And I set the field "smdi_7_additional_property" in the "#qtype-stack-metadata-content" "css_element" to "More data"
    And I set the field "smdi_7_additional_qualifier" in the "#qtype-stack-metadata-content" "css_element" to "Type2"
    And I set the field "smdi_7_additional_value" in the "#qtype-stack-metadata-content" "css_element" to "BBB2"
    And I click on "smd_property_5_add" "button"
    And I wait until "smdi_8_additional_property" "field" exists
    And I set the field "smdi_8_additional_property" in the "#qtype-stack-metadata-content" "css_element" to "No obj"
    And I set the field "smdi_8_additional_qualifier" in the "#qtype-stack-metadata-content" "css_element" to ""
    And I set the field "smdi_8_additional_value" in the "#qtype-stack-metadata-content" "css_element" to "BBB3"
    And I click on "Validate and close" "button"
    And I check the hidden input "metadata" is '{"author":[{"firstName":"Edmund","lastName":"Farrow","institution":"UoE","year":"2025"},{"firstName":"Teacher","lastName":"Lastname1","institution":"","year":"XXXX"}],"language":["fr","it"],"isPartOf":"HELM","license":"cc-nc-4.0","additional":{"Changed":{"Question":{"Type":"MC"},"More":{"Things":"AAA"}},"Another Scope":{"Question2":{"Type2":"MC2"},"More data":{"Things2":"AAA2","Type2":"BBB2"},"No obj":"BBB3"}},"freeform":{}}'
    And I click on "View and edit full metadata" "button"
    And I should see "Freeform metadata"
    And I click on "stack-metadata-edit-switch" "checkbox"
    And I click on "#qtype-stack-metadata-json-section > summary" "css_element"
    And I set the field "id_metadata_json" to multiline:
    """
    {
      "author": [
          {
              "firstName": "Bob",
              "lastName": "Smith",
              "institution": "MIT",
              "year": "2024"
          },
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
      "additional":
          {
              "Added": {
                  "Cat": {
                      "Breed": "Al$%&^"
                  },
                  "Horse": "Dobbin",
                  "Dog": {
                      "Teeth": "50",
                      "Tails": "1"
                  }
              },
              "Added too": {
                  "Fish": {
                      "Gills": "2"
                  }
              }
          },
      "freeform": {}
    }
    """
    And I click on "stack-metadata-update-inputs" "button"
    And the focused element should be "#stack-metadata-update-inputs" "css_element"
    And I click on "Validate and close" "button"
    And I check the hidden input "metadata" is '{"author":[{"firstName":"Bob","lastName":"Smith","institution":"MIT","year":"2024"},{"firstName":"Mike","lastName":"Jones","institution":"Bath","year":"2023"}],"language":["en"],"isPartOf":"Everything","license":"cc-nc-4.1","additional":{"Added":{"Cat":{"Breed":"Al$%&^"},"Horse":"Dobbin","Dog":{"Teeth":"50","Tails":"1"}},"Added too":{"Fish":{"Gills":"2"}}},"freeform":{}}'
    And I click on "View and edit full metadata" "button"
    And I should see "Freeform metadata"
    And I click on "stack-metadata-edit-switch" "checkbox"
    And I click on "smd_property_5_add" "button"
    And I wait until "smdi_6_additional_property" "field" exists
    And I set the field "smdi_6_additional_property" in the "#qtype-stack-metadata-content" "css_element" to "Fish"
    And I set the field "smdi_6_additional_qualifier" in the "#qtype-stack-metadata-content" "css_element" to "Gills"
    And I set the field "smdi_6_additional_value" in the "#qtype-stack-metadata-content" "css_element" to "3"
    And I click on "smd_property_1_add" "button"
    And I wait until "smdi_7_additional_property" "field" exists
    And I set the field "smdi_7_additional_property" in the "#qtype-stack-metadata-content" "css_element" to "Horse"
    And I set the field "smdi_7_additional_qualifier" in the "#qtype-stack-metadata-content" "css_element" to ""
    And I set the field "smdi_7_additional_value" in the "#qtype-stack-metadata-content" "css_element" to "Champion"
    And I click on "Validate and close" "button"
    And I check the hidden input "metadata" is '{"author":[{"firstName":"Bob","lastName":"Smith","institution":"MIT","year":"2024"},{"firstName":"Mike","lastName":"Jones","institution":"Bath","year":"2023"}],"language":["en"],"isPartOf":"Everything","license":"cc-nc-4.1","additional":{"Added":{"Cat":{"Breed":"Al$%&^"},"Horse":["Dobbin","Champion"],"Dog":{"Teeth":"50","Tails":"1"}},"Added too":{"Fish":{"Gills":["2","3"]}}},"freeform":{}}'
    And I click on "View and edit full metadata" "button"
    And I should see "Freeform metadata"
    And I click on "stack-metadata-edit-switch" "checkbox"
    And I click on "smd_property_1_add" "button"
    And I wait until "smdi_8_additional_property" "field" exists
    And I set the field "smdi_8_additional_property" in the "#qtype-stack-metadata-content" "css_element" to "Horse"
    And I set the field "smdi_8_additional_qualifier" in the "#qtype-stack-metadata-content" "css_element" to "Name"
    And I set the field "smdi_8_additional_value" in the "#qtype-stack-metadata-content" "css_element" to "Nessie"
    And I click on "Validate and close" "button"
    And I should see "Required" in the "#smde_7_additional_qualifier_error" "css_element"
    And I set the field "id_metadata_json" to multiline:
    """
    {
      "author": {
              "firstName": "Bo1b",
              "lastName": "Smi1th",
              "institution": "MI1T",
              "year": "2024"
          },
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
      "additional": {
            "Adfded": {
                "Cfat": {
                    "Bfreed": "Al$%f&^"
                },
                "Dfog": {
                    "Tefeth": "5f0"
                }
            },
            "Addfed too": {
                "Fifsh": {
                    "Giflls": "2f"
                }
            }
        },
      "freeform": {}
    }
    """
    And I click on "stack-metadata-update-inputs" "button"
    And I wait until "smdi_2_author_firstName" "field" exists
    And I click on "stack-metadata-revert" "button"
    And the focused element should be "#stack-metadata-revert" "css_element"
    And I should not see "Lowell"
    And I click on "Validate and close" "button"
    And I check the hidden input "metadata" is '{"author":[{"firstName":"Bob","lastName":"Smith","institution":"MIT","year":"2024"},{"firstName":"Mike","lastName":"Jones","institution":"Bath","year":"2023"}],"language":["en"],"isPartOf":"Everything","license":"cc-nc-4.1","additional":{"Added":{"Cat":{"Breed":"Al$%&^"},"Horse":["Dobbin","Champion"],"Dog":{"Teeth":"50","Tails":"1"}},"Added too":{"Fish":{"Gills":["2","3"]}}},"freeform":{}}'
    And I press "id_updatebutton"
    Given the site is running Moodle version 4.6 or lower
    Then I should see "Version 2"
    And I check the hidden input "metadata" is '{"author":[{"firstName":"Bob","lastName":"Smith","institution":"MIT","year":"2024"},{"firstName":"Mike","lastName":"Jones","institution":"Bath","year":"2023"}],"language":["en"],"isPartOf":"Everything","license":"cc-nc-4.1","additional":{"Added":{"Cat":{"Breed":"Al$%&^"},"Horse":["Dobbin","Champion"],"Dog":{"Teeth":"50","Tails":"1"}},"Added too":{"Fish":{"Gills":["2","3"]}}},"freeform":{}}'
    Given the site is running Moodle version 5.0 or higher
    Then I should see "v2 (latest)"
    And I check the hidden input "metadata" is '{"author":[{"firstName":"Bob","lastName":"Smith","institution":"MIT","year":"2024"},{"firstName":"Mike","lastName":"Jones","institution":"Bath","year":"2023"}],"language":["en"],"isPartOf":"Everything","license":"cc-nc-4.1","additional":{"Added":{"Cat":{"Breed":"Al$%&^"},"Horse":["Dobbin","Champion"],"Dog":{"Teeth":"50","Tails":"1"}},"Added too":{"Fish":{"Gills":["2","3"]}}},"freeform":{}}'

  @javascript
  Scenario: Create and edit STACK Freeform metadata
    When I am on the "Algebraic input" "core_question > edit" page logged in as teacher
    And I click on "View and edit full metadata" "button"
    And I should see "Freeform metadata"
    And I click on "stack-metadata-edit-switch" "checkbox"
    And I click on "#qtype-stack-metadata-json-section > summary" "css_element"
    And I set the field "id_metadata_json" to multiline:
    """
    {
      "author": [
          {
              "firstName": "Bob",
              "lastName": "Smith",
              "institution": "MIT",
              "year": "2024"
          },
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
      "additional":
          {
              "additional": {
                  "Cat": {
                      "Breed": "Al$%&^"
                  },
                  "Horse": "Dobbin",
                  "Dog": {
                      "Teeth": "50",
                      "Tails": "1"
                  },
                  "Multi": [
                    1,2,3
                  ],
                  "Multi1": {
                    "Multi2": [
                      4,5,6
                    ]
                  }
              },
              "Added too": {
                  "Fish": {
                      "Gills": "2"
                  }
              }
          },
      "freeform":
          {
              "license": {
                  "Cat": {
                      "Breed": "Al$%&^"
                  },
                  "Horse": "Dobbin",
                  "Dog": {
                      "Teeth": "50",
                      "Tails": "1"
                  }
              },
              "Freeform too": {
                  "Fish": {
                      "Gills": "2"
                  }
              }
          }
    }
    """
    And I click on "stack-metadata-update-inputs" "button"
    And the focused element should be "#stack-metadata-update-inputs" "css_element"
    And I should see "{\"license\":{\"Cat\":{\"Breed\":\"Al$%&^\"},\"Horse\":\"Dobbin\",\"Dog\":{\"Teeth\":\"50\",\"Tails\":\"1\"}},\"Freeform too\":{\"Fish\":{\"Gills\":\"2\"}}}"
    And I click on "Validate and close" "button"
    And I check the hidden input "metadata" is '{"author":[{"firstName":"Bob","lastName":"Smith","institution":"MIT","year":"2024"},{"firstName":"Mike","lastName":"Jones","institution":"Bath","year":"2023"}],"language":["en"],"isPartOf":"Everything","license":"cc-nc-4.1","additional":{"additional":{"Cat":{"Breed":"Al$%&^"},"Horse":"Dobbin","Dog":{"Teeth":"50","Tails":"1"},"Multi":["1","2","3"],"Multi1":{"Multi2":["4","5","6"]}},"Added too":{"Fish":{"Gills":"2"}}},"freeform":{"license":{"Cat":{"Breed":"Al$%&^"},"Horse":"Dobbin","Dog":{"Teeth":"50","Tails":"1"}},"Freeform too":{"Fish":{"Gills":"2"}}}}'
    And I click on "View and edit full metadata" "button"
    And I should see "Freeform metadata"
    And I click on "stack-metadata-edit-switch" "checkbox"
    And I set the field "smdi_0_freeform_value" to multiline:
    """
    {"x":[{"additional":"b"},{"license":"d"}]}
    """
    And I click on "Validate and close" "button"
    And I check the hidden input "metadata" is '{"author":[{"firstName":"Bob","lastName":"Smith","institution":"MIT","year":"2024"},{"firstName":"Mike","lastName":"Jones","institution":"Bath","year":"2023"}],"language":["en"],"isPartOf":"Everything","license":"cc-nc-4.1","additional":{"additional":{"Cat":{"Breed":"Al$%&^"},"Horse":"Dobbin","Dog":{"Teeth":"50","Tails":"1"},"Multi":["1","2","3"],"Multi1":{"Multi2":["4","5","6"]}},"Added too":{"Fish":{"Gills":"2"}}},"freeform":{"x":[{"additional":"b"},{"license":"d"}]}}'

  @javascript
  Scenario: New question metadata
    When I am on the "Course 1" "core_question > course question bank" page logged in as "teacher"
    And I click on "Create a new question" "button"
    And I set the field "item_qtype_stack" to "1"
    And I press "submitbutton"
    And I click on "View and edit full metadata" "button"
    And I should see "Freeform metadata"
    And I click on "Validate and close" "button"
    And I check the hidden input "metadata" is '{"author":[{"firstName":"Teacher","lastName":"Lastname1","institution":"","year":"XXXX"}],"language":["en"],"license":"unknown","isPartOf":"","additional":{},"freeform":{}}'

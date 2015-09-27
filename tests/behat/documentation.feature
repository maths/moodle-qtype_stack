@qtype @qtype_stack
Feature: STACK has build in documentation.
  In order to use STACK
  As an admin
  I need to read the documentation.

  Background:
    Given I log in as "admin"
    And I navigate to "STACK" node in "Site administration > Plugins > Question types"

  @javascript
  Scenario: Navigate to the documentation
    When I follow "Documentation for STACK"
    Then I should see "Welcome to the official user documentation for the STACK project."
    When I follow "Site map"
    Then I should see "Directory structure"
    When I follow "Fact sheets"
    Then I should see "[[facts:calc_int_methods_parts]]"

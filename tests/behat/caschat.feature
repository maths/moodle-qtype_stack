@qtype @qtype_stack
Feature: STACK script for interacting with the CAS.
  In order to learn Maxima code
  As an admin
  I need to use the CAS chat script.

  Background:
    Given I log in as "admin"
    And I set up STACK using the PHPUnit configuration
    And I navigate to "Plugins > Question types > STACK" in site administration

  @javascript
  Scenario: Navigate to the CAS chat script and evaluate something
    When I follow "CAS chat script"
    Then I should see "Test the connection to the CAS"

    When I set the field "cas" to "1 + 1 = {@ 1+1 @}."
    And I press "Send to the CAS"
    Then I should see "1 + 1 = 2"

    When I set the field "cas" to "[[facts:calc_int_methods_parts]]"
    And I press "Send to the CAS"
    And I should see "Integration by Parts"
    Then I should see "or alternatively:"

@qtype @qtype_stack
Feature: STACK has a build in self-check.
  In order to verify STACK is installed properly
  As an admin
  I need to run the healthcheck.

  Background:
    Given I log in as "admin"
    And I set up STACK using the PHPUnit configuration
    And I navigate to "Plugins > Question types > STACK" in site administration

  Scenario: Run the STACK healthcheck
    When I follow "healthcheck script"
    Then I should see "STACK healthcheck"
    And I should see "CAS returned data as expected.  You have a live connection to the CAS."
    And I should see "Correct and expected STACK-Maxima library version being used"

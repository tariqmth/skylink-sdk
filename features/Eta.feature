@ccompleted
Feature: ETA

  Scenario: Retrieving an ETA
    Given I am connected to sales channel "1"
     When I find the ETA for "25" products with id "124005"
     Then I should see the ETA is in the future

  Scenario: Retrieving an ETA
    Given I am connected to sales channel "1"
     When I find the ETA for "50" products with id "124006"
     Then I should see there is no ETA

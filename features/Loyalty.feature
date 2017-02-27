@ccompleted
Feature: Loyalty

  Scenario: Retrieve loyalty points balance
    Given I find the loyalty balance for customer with id "300000"
     Then I can see the loyalty balance is "0"

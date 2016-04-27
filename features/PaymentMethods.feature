@completed
Feature: Payment Methods

  Scenario: Retrieving all payment methods
    Given I am connected to sales channel "1"
     When I get all payment methods
     Then I should see there are "25" payment methods
      And payment method "1" is known as "Cash"

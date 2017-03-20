@completed
Feature: Outlets

  Scenario: Retrieving all outlets
    Given I am connected to sales channel "1"
     When I get all outlets
     Then I should see there are "3" outlets
      And outlet "1" is known as "Maroon Outlet"
      And outlet "2" is known as "Blue Outlet"

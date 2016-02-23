@run
Feature: Outlets

  Scenario: Retrieving all outlets
    Given I am connected to sales channel "1"
     When I get all outlets
     Then I should see there are "2" outlets
      And outlet "1" is known as "Outlet A"
      And outlet "2" is known as "Outlet B"

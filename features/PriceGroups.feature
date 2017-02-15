@completed
Feature: Price Groups

  Scenario: Retrieving all price groups
    When I get all price groups
    Then I should see there are "6" price groups
     And I should see that "fixed" price group "1" is "VIP"
     And I should see that "standard" price group "2" is "Bronze"

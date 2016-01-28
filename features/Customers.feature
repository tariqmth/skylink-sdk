Feature: Products

  @run
  Scenario: Retrieving a customer
    When I find the customer with id "300001"
    Then I should see that their first name is "Joe"
     And I should see that their last name is "Bloggs"

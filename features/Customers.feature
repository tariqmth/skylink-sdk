Feature: Products

  @run
  Scenario: Retrieving a customer with lots of information
    When I find the customer with id "300000"
    Then I should see that their first name is "Joe"
     And I should see that their last name is "Bloggs"
     And I should see their email is "joe@testing.com"
     And I should see they work for "Sample Company"
     And I should see their billing address is:
     """
     Sample Company
     Unit 5
     192 Ann St
     Brisbane Queensland 4000
     Australia
     """
     And I should see they can be contacted by calling "(07) 1111 1111"

  Scenario: Retrieving a customer with bare minimum information
    When I find the customer with id "300001"
    Then I should see that their first name is "Sarah"
     And I should see that their last name is "Bloggs"

  Scenario: Registering a new customer
    When I have an email

  # Scenario: Updating a customer
  #   When I find the customer with id "300013"
  #   Then I should see that their first name is "Kelly"
  #    And I should be able to update their first name to "Smelly"
  #    And I should be able to update their first name to "Kelly"

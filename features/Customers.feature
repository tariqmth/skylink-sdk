Feature: Customers

  Scenario: Retrieving a customer with lots of information
    When I find the customer with id "300000"
    Then I should see that their full name is "Joe" "Bloggs"
     And I should see their email is "joe@testing.com"
     And I should see they work for "Sample Company"
     And I should see their billing contact is:
     """
     Joe Bloggs
     Sample Company
     Unit 5
     192 Ann St
     Brisbane Queensland 4000
     Australia
     """
     # And I should see they can be contacted by calling "(07) 1111 1111"

  Scenario: Retrieving a customer with bare minimum information
    When I find the customer with id "300001"
    Then I should see that their full name is "Sarah" "Bloggs"

  @run
  Scenario: Registering a new customer with bare minimum information
    Given I use a unique email based on "hello@example.com" and a password "hello123"
      And I use "Ben" and "Corlett" as the first and last name respectively
     Then I should be able to register a customer based on these details
      And I should be able to add the customer to Retail Express


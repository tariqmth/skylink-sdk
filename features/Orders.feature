@completed
Feature: Orders

  Scenario: Placing an order
    Given I am connected to sales channel "1"
      And I use a unique email based on "hello@example.com" and a password "hello123"
      And I use "Ben" and "Corlett" as the first and last name respectively
      And I want to ship my order to:
      """
      Acme
      1 George Street
      Sydney
      New South Wales
      2000
      Australia
      """
      And I order "1" of the product with id "124005" for "19.95"
      And I am willing to pay "10.00" for shipping with "Australia Post"
     Then I should be able to add a new order for my new customer
      And I should have a new customer id and order id
     Then I can see the order is not fulfilled
      And I can see the order is not paid
     Then I can pay a total of "9.95" towards the order using payment method "1"
      And I can pay a total of "20.00" towards the order using payment method "2"
      And I can see the order is paid

  Scenario: Retrieving an order
    Given I should be able to find the order "16-00000001"
     Then I should be able to see it has "2" payments made
      And that all items have been fulfilled in "1" fulfillments

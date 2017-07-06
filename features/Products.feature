@completed
Feature: Products

  Scenario: Retrieving all brands
     When I get all brands
     Then I should see there are "6" brands

  Scenario: Retrieving product ids
    Given I am connected to sales channel "1"
     When I get all product ids
     Then I can see there are "19" product ids

  Scenario: Retrieving a simple product with minimal information
    Given I am connected to sales channel "1"
     When I find the product with id "124005"
     Then I should see the product exists
      And I should see that its sku is "HB0011OSFA"
      And I should see that its manufacturer sku is "HB0011"

  Scenario: Retrieving a simple product with lots of information
    Given I am connected to sales channel "1"
     When I find the product with id "124006"
     Then I should see the product exists
      And I should see that its sku is "SC0011OSFA"
      And I should see that we have "100" available and "0" on order
      And I should see that outlet "3" has "0" available
      And I should see that outlet "2" has "100" available
      And I should see that it has no stock for outlet "4"

  Scenario: Retrieving a simple product on a sales channel that it doesn't belong to
    Given I am connected to sales channel "2"
     When I find the product with id "124006"
     Then I should see the product does not exist

  Scenario: Retrieving a matrix product
    Given I am connected to sales channel "1"
     When I find the product with id "124007"
     Then I should see the product exists
      And it is a matrix that contains "6" products
      And I should see that its manufacturer sku is "BSS-THAW16"

@completed
Feature: Products

  Scenario: Retrieving all brands
    Given I am connected to sales channel "1"
     When I get all brands
     Then I should see there are "5" brands

  Scenario: Retrieving product ids
    Given I am connected to sales channel "1"
     When I get all product ids
     Then I can see there are "19" product ids

  Scenario: Retrieving a simple product with minimal information
    Given I am connected to sales channel "1"
     When I find the product with id "124005"
     Then I should see the product exists
      And I should see that its sku is "HB0011OSFA"

  Scenario: Retrieving a simple product with lots of information
    Given I am connected to sales channel "1"
     When I find the product with id "124006"
     Then I should see the product exists
      And I should see that its sku is "SC0011OSFA"

  Scenario: Retrieving a simple product on a sales channel that it doesn't belong to
    Given I am connected to sales channel "2"
     When I find the product with id "124006"
     Then I should see the product does not exist

  Scenario: Retrieving a matrix product
    Given I am connected to sales channel "1"
     When I find the product with id "124007"
     Then I should see the product exists
      And it is a matrix that contains "4" products

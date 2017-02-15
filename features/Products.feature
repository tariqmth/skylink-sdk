@completed
Feature: Products

  Scenario: Retrieving all brands
    Given I am connected to sales channel "1"
     When I get all brands
     Then I should see there are "5" brands

  Scenario: Retrieving a simple product with minimal information
    Given I am connected to sales channel "1"
     When I find the product with id "124005"
     Then I should see that its sku is "HB0011OSFA"

  Scenario: Retrieving a configurable product
    Given I am connected to sales channel "1"
     When I find the product with id "124007"

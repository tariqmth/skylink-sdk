Feature: Products

  Scenario: Retrieving a simple product
    Given I am connected to sales channel "1"
     When I find the product with id "124001"
     Then I should see that its sku is "TS004"

  Scenario: Retrieving a grouped product
    Given I am connected to sales channel "1"
     When I find the product with id "124005"
     Then I should see that its sku is "GHORIDLBL"

  Scenario: Retrieving a simple associated product
    Given I am connected to sales channel "1"
     When I find the product with id "124006"
     Then I should see that its sku is "GHORIDSBK"

Feature: Products

  @wip
  Scenario: Retrieving a simple product
    Given I am connected to sales channel "1"
     When I find the product with id "124001"
     Then I should see that its sku is "TS004"


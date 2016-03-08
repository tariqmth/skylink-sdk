Feature: Products

  @completed
  Scenario: Retrieving all brands
    Given I am connected to sales channel "1"
     When I get all brands
     Then I should see there are "4" brands

  @completed
  Scenario: Retrieving a simple product with minimal information
    Given I am connected to sales channel "1"
     When I find the product with id "124001"
     Then I should see that its sku is "01761C684"

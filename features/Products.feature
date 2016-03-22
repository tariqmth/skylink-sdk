Feature: Products

  @completed
  Scenario: Retrieving all brands
    Given I am connected to sales channel "1"
     When I get all brands
     Then I should see there are "5" brands

  @completed
  Scenario: Retrieving a simple product with minimal information
    Given I am connected to sales channel "1"
     When I find the product with id "124001"
     Then I should see that its sku is "01761C684"

  @wip
  Scenario: Retrieving a simple product with lots of information
    Given I am connected to sales channel "1"
     When I find the product with id "124002"
     Then I should see that its sku is "01901D039"
     # @todo many, many more assertions here!

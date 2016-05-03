@completed
Feature: Vouchers

  Scenario: Retrieve a voucher
    When I find the voucher with code "2ZPS40YV"
    Then the balance should be "100.0"

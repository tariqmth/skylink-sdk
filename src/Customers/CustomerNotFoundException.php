<?php

namespace RetailExpress\SkyLink\Sdk\Customers;

use Exception;

class CustomerNotFoundException extends Exception
{
    public static function withCustomerId(CustomerId $customerId)
    {
        return new self("Customer with ID {$customerId} does not exist.");
    }
}

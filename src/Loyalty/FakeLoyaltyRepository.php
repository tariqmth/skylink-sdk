<?php

namespace RetailExpress\SkyLink\Sdk\Loyalty;

use RetailExpress\SkyLink\Sdk\Customers\CustomerId;

class FakeLoyaltyRepository implements LoyaltyRepository
{
    public function find(CustomerId $customerId)
    {
        return new Loyalty(100);
    }
}

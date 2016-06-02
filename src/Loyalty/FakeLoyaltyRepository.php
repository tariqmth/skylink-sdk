<?php

namespace RetailExpress\SkyLink\Loyalty;

use RetailExpress\SkyLink\Customers\CustomerId;

class FakeLoyaltyRepository implements LoyaltyRepository
{
    public function find(CustomerId $customerId)
    {
        return new Loyalty(100);
    }
}

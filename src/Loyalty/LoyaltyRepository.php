<?php

namespace RetailExpress\SkyLink\Loyalty;

use RetailExpress\SkyLink\Customers\CustomerId;

interface LoyaltyRepository
{
    public function find(CustomerId $customerId);
}

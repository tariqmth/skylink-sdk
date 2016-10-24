<?php

namespace RetailExpress\SkyLink\Sdk\Loyalty;

use RetailExpress\SkyLink\Sdk\Customers\CustomerId;

interface LoyaltyRepository
{
    public function find(CustomerId $customerId);
}

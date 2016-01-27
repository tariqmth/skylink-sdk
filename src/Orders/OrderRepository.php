<?php

namespace RetailExpress\SkyLink\Orders;

interface OrderRepository
{
    public function find(CustomerId $customerId);
}

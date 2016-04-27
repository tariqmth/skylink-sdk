<?php

namespace RetailExpress\SkyLink\Sales\Orders;

interface OrderRepository
{
    public function find(CustomerId $customerId);
}

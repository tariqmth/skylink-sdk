<?php

namespace RetailExpress\SkyLink\Sales;

interface OrderRepository
{
    public function find(CustomerId $customerId);
}

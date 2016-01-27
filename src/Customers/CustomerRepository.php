<?php

namespace RetailExpress\SkyLink\Customers;

interface CustomerRepository
{
    public function find(CustomerId $customerId);
}

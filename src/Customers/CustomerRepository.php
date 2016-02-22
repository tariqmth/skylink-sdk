<?php

namespace RetailExpress\SkyLink\Customers;

interface CustomerRepository
{
    public function all();

    public function find(CustomerId $customerId);

    public function add(Customer $customer);
}

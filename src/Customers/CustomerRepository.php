<?php

namespace RetailExpress\SkyLink\Customers;

interface CustomerRepository
{
    /**
     * Finds a Customer with the given Customer ID.
     *
     * @param CustomerId $customerId
     *
     * @return Customer|null
     *
     * @throws CustomerNotFoundException
     */
    public function find(CustomerId $customerId);

    public function add(Customer $customer);
}

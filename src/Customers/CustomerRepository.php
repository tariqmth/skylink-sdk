<?php

namespace RetailExpress\SkyLink\Sdk\Customers;

interface CustomerRepository
{
    public function allIds();

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

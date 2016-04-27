<?php

namespace RetailExpress\SkyLink\Sales\Orders;

use RetailExpress\SkyLink\Customers\BillingContact;
use RetailExpress\SkyLink\Customers\ShippingContact;

class Order
{
    private $billingContact;

    private $shippingContact;

    public function __construct(BillingContact $billingContact, ShippingContact $shippingContact)
    {
        $this->billingContact = $billingContact;
        $this->shippingContact = $shippingContact;
    }
}

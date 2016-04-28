<?php

namespace RetailExpress\SkyLink\Sales\Orders;

use RetailExpress\SkyLink\ValueObjects\TaxRate;
use ValueObjects\Number\Real;

class ShippingCharge
{
    use TaxablePrice;

    private $price;

    private $taxRate;

    public static function fromNative()
    {
        $args = func_get_args();

        if (count($args) < 2) {
            throw new BadMethodCallException('You must provide at least 2 arguments: 1) price, 2) tax rate');
        }

        $price = new Real($args[0]);
        $taxRate = TaxRate::fromNative($args[1]);

        return new self($price, $taxRate);
    }

    public function __construct(Real $price, TaxRate $taxRate)
    {
        $this->price = $price;
        $this->taxRate = $taxRate;
    }
}

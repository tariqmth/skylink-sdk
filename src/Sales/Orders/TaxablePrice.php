<?php

namespace RetailExpress\SkyLink\Sales\Orders;

use ValueObjects\Number\Real;

trait TaxablePrice
{
    public function getPrice()
    {
        return clone $this->price;
    }

    public function getTaxRate()
    {
        return clone $this->taxRate;
    }

    public function getPriceExclTax()
    {
        if (!$this->getTaxRate()->isTaxable()) {
            return $this->getPrice();
        }

        return new Real($this->getPrice()->toNative() / (1 + $this->getTaxRate()->getRate()->toNative()));
    }
}

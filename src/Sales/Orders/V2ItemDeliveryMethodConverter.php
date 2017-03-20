<?php

namespace RetailExpress\SkyLink\Sdk\Sales\Orders;

trait V2ItemDeliveryMethodConverter
{
    private static $mappings = [
        'cash_carry' => null,
    ];

    public function getV2XmlAttribute()
    {
        if (!array_key_exists($this->getValue(), self::$mappings)) {
            return $this->getValue();
        }

        return self::$mappings[$this->getValue()];
    }
}

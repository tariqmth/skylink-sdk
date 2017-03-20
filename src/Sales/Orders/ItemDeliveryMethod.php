<?php

namespace RetailExpress\SkyLink\Sdk\Sales\Orders;

use ValueObjects\Enum\Enum;
use ValueObjects\StringLiteral\StringLiteral;

class ItemDeliveryMethod extends Enum
{
    use V2ItemDeliveryMethodConverter;

    const CASH_CARRY = 'cash_carry';
    const HOME = 'home';
    const WAREHOUSE = 'warehouse';
    const STORE = 'store';

    public static function getDefault()
    {
        return self::get('home');
    }

    public function isPickupLater()
    {
        $pickupLater = [self::WAREHOUSE, self::STORE];

        return in_array($this->getValue(), $pickupLater);
    }

    public function getLabel()
    {
        $labels = [
            self::CASH_CARRY => 'Cash and Carry',
            self::HOME => 'Home Delivery',
            self::WAREHOUSE => 'Warehouse Pickup',
            self::STORE => 'Store Pickup',
        ];

        return new StringLiteral($labels[$this->getValue()]);
    }
}

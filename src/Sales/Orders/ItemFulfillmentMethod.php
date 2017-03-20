<?php

namespace RetailExpress\SkyLink\Sdk\Sales\Orders;

use ValueObjects\Enum\Enum;
use ValueObjects\StringLiteral\StringLiteral;

class ItemFulfillmentMethod extends Enum
{
    use V2ItemFulfillmentMethodConverter;

    const CASH_CARRY = 'cash_carry';
    const HOME = 'home';
    const WAREHOUSE = 'warehouse';
    const STORE = 'store';

    public static function getDefault()
    {
        return self::HOME;
    }

    public static function getNonAutomaticFulfilling()
    {
        return array_values(array_diff(self::getConstants(), [self::CASH_CARRY]));
    }

    public function automaticallyFulfills()
    {
        return !in_array($this->getValue(), self::getNonAutomaticFulfilling());
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

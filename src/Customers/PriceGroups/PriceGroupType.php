<?php

namespace RetailExpress\SkyLink\Sdk\Customers\PriceGroups;

use ValueObjects\Enum\Enum;

class PriceGroupType extends Enum
{
    const STANDARD = 'standard';
    const FIXED = 'fixed';

    /**
     * @return \ValueObjects\StringLiteral\StringLiteral
     */
    public function getPriceGroupTypeName()
    {
        $names = [
            self::STANDARD => 'Standard',
            self::FIXED => 'Fixed',
        ];

        return new \ValueObjects\StringLiteral\StringLiteral($names[$this->getValue()]);
    }
}

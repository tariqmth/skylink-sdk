<?php

namespace RetailExpress\SkyLink\Sdk\Customers\PriceGroups;

use ValueObjects\Enum\Enum;
use ValueObjects\StringLiteral\StringLiteral;

class PriceGroupType extends Enum
{
    const STANDARD = 'standard';
    const FIXED = 'fixed';

    /**
     * @return StringLiteral
     */
    public function getPriceGroupName()
    {
        $names = [
            self::STANDARD => 'Standard',
            self::FIXED => 'Fixed',
        ];

        return new StringLiteral($names[$this->getValue()]);
    }
}

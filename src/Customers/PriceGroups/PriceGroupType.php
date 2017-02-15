<?php

namespace RetailExpress\SkyLink\Sdk\Customers\PriceGroups;

use ValueObjects\Enum\Enum;

class PriceGroupType extends Enum
{
    const STANDARD = 'standard';
    const FIXED = 'fixed';
}

<?php

namespace RetailExpress\SkyLink\Products;

use RetailExpress\SkyLink\ValueObjects\SalesChannelId;
use ValueObjects\Enum\Enum;

class AttributeCode extends Enum
{
    const BRAND = 'brand';
    const COLOUR = 'colour';
    const SEASON = 'season';
    const SIZE = 'size';
    const PRODUCT_TYPE = 'product_type';
}

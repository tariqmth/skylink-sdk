<?php

namespace RetailExpress\SkyLink\Sdk\Catalogue\Products;

use ValueObjects\Enum\Enum;

class ProductPriceAttribute extends Enum
{
    use V2ProductPriceAttributeConverter;

    const RRP = 'rrp';
    const DEFAULT_PRICE = 'default_price';
    const DISCOUNTED_PRICE = 'discounted_price';
    const WEB_PRICE = 'web_price';

    public static function getDefaultForRegularPrice()
    {
        return self::get('default_price');
    }

    public static function getDefaultForSpecialPrice()
    {
        return self::get('discounted_price');
    }
}

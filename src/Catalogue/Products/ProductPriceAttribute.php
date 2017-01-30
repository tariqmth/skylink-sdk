<?php

namespace RetailExpress\SkyLink\Sdk\Catalogue\Products;

use ValueObjects\Enum\Enum;

class ProductPriceAttribute extends Enum
{
    use V2ProductPriceAttributeConverter;

    const DISCOUNTED_PRICE = 'discounted_price';
    const DEFAULT_PRICE = 'default_price';
    const WEB_SELL_PRICE = 'web_sell_price';
    const RRP = 'rrp';

    public static function getDefaultForRegularPrice()
    {
        return self::get('default_price');
    }

    public static function getDefaultForSpecialPrice()
    {
        return self::get('default_price');
    }
}

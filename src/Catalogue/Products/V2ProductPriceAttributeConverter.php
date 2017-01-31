<?php

namespace RetailExpress\SkyLink\Sdk\Catalogue\Products;

use ValueObjects\Enum\Enum;

trait V2ProductPriceAttributeConverter
{
    private static $mappings = [
        'discounted_price' => 'DiscountedPrice',
        'default_price' => 'DefaultPrice',
        'web_price' => 'WebSellPrice',
        'rrp' => 'RRP',
    ];

    public function getV2XmlAttribute()
    {
        return self::$mappings[$this->getValue()];
    }
}

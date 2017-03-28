<?php

namespace RetailExpress\SkyLink\Sdk\Catalogue\Products;

use ValueObjects\Enum\Enum;
use ValueObjects\StringLiteral\StringLiteral;

class ProductPriceAttribute extends Enum
{
    use V2ProductPriceAttributeConverter;

    const RRP = 'rrp';
    const DEFAULT_PRICE = 'default_price';
    const PROMOTIONAL_PRICE = 'promotional_price';
    const WEB_PRICE = 'web_price';

    public static function getDefaultForRegularPrice()
    {
        return self::get('default_price');
    }

    public static function getDefaultForSpecialPrice()
    {
        return self::get('promotional_price');
    }

    public function getLabel()
    {
        $labels = [
            self::RRP => 'RRP',
            self::DEFAULT_PRICE => 'POS Price (Without Discounts)',
            self::PROMOTIONAL_PRICE => 'POS Price (With Discounts)',
            self::WEB_PRICE => 'Web Price',
        ];

        return new StringLiteral($labels[$this->getValue()]);
    }

    public function isTimed()
    {
        return self::PROMOTIONAL_PRICE === $this->getValue();
    }
}

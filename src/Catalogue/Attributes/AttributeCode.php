<?php

namespace RetailExpress\SkyLink\Catalogue\Attributes;

use ValueObjects\Enum\Enum;

class AttributeCode extends Enum
{
    const BRAND = 'brand';
    const COLOUR = 'colour';
    const SEASON = 'season';
    const SIZE = 'size';
    const PRODUCT_TYPE = 'product_type';
    const CUSTOM_1 = 'custom_1';
    const CUSTOM_2 = 'custom_2';
    const CUSTOM_3 = 'custom_3';

    public static function getPredefined()
    {
        return array_filter(self::getConstants(), function ($value) {
            return !str_is('custom_*', $value);
        });
    }

    public static function getAdhoc()
    {
        return array_diff(self::getConstants(), self::getPredefined());
    }

    public function isPredefined()
    {
        return in_array($this->getValue(), self::getPredefined());
    }

    public function isAdhoc()
    {
        return !$this->isPredefined();
    }
}

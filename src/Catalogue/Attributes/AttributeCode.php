<?php

namespace RetailExpress\SkyLink\Sdk\Catalogue\Attributes;

use ValueObjects\Enum\Enum;
use ValueObjects\StringLiteral\StringLiteral;

class AttributeCode extends Enum
{
    const BRAND = 'brand';
    const COLOUR = 'colour';
    const CUSTOM_1 = 'custom_1';
    const CUSTOM_2 = 'custom_2';
    const CUSTOM_3 = 'custom_3';
    const PRODUCT_TYPE = 'product_type';
    const SEASON = 'season';
    const SIZE = 'size';

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

    public function getLabel()
    {
        $labels = [
            self::BRAND => 'Brand',
            self::COLOUR => 'Colour',
            self::CUSTOM_1 => 'Custom 1',
            self::CUSTOM_2 => 'Custom 2',
            self::CUSTOM_3 => 'Custom 3',
            self::PRODUCT_TYPE => 'Product Type',
            self::SEASON => 'Season',
            self::SIZE => 'Size',
        ];

        return new StringLiteral($labels[$this->getValue()]);
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

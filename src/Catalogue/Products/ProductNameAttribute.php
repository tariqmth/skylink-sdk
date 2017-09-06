<?php

namespace RetailExpress\SkyLink\Sdk\Catalogue\Products;

use ValueObjects\Enum\Enum;
use ValueObjects\StringLiteral\StringLiteral;

class ProductNameAttribute extends Enum
{
    use V2ProductNameAttributeConverter;

    const DESCRIPTION = 'description';
    const CUSTOM_1 = 'custom_1';
    const CUSTOM_2 = 'custom_2';
    const CUSTOM_3 = 'custom_3';

    public static function getDefault()
    {
        return self::get('description');
    }

    public function getLabel()
    {
        $labels = [
            self::DESCRIPTION => 'Short Description',
            self::CUSTOM_1 => 'Custom 1',
            self::CUSTOM_2 => 'Custom 2',
            self::CUSTOM_3 => 'Custom 3',
        ];

        return new StringLiteral($labels[$this->getValue()]);
    }
}

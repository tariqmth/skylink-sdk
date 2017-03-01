<?php

namespace RetailExpress\SkyLink\Sdk\Catalogue\Attributes;

trait V2AttributeCodeConverter
{
    public function getV2XmlAttribute()
    {
        $xmlAttributes = [
            self::BRAND => 'Brand',
            self::COLOUR => 'Colour',
            self::CUSTOM_1 => 'Custom1',
            self::CUSTOM_2 => 'Custom2',
            self::CUSTOM_3 => 'Custom3',
            self::PRODUCT_TYPE => 'ProductType',
            self::SEASON => 'Season',
            self::SIZE => 'Size',
        ];

        return $xmlAttributes[$this->getValue()];
    }

    public function getV2XmlAttributeGroup()
    {
        $xmlAttributesGroups = [
            self::BRAND => 'Brands',
            self::COLOUR => 'Colours',
            self::CUSTOM_1 => 'Customs',
            self::CUSTOM_2 => 'Customs',
            self::CUSTOM_3 => 'Customs',
            self::PRODUCT_TYPE => 'ProductTypes',
            self::SEASON => 'Seasons',
            self::SIZE => 'Sizes',
        ];

        return $xmlAttributesGroups[$this->getValue()];
    }
}

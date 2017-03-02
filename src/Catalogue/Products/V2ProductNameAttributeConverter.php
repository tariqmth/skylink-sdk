<?php

namespace RetailExpress\SkyLink\Sdk\Catalogue\Products;

trait V2ProductNameAttributeConverter
{
    private static $mappings = [
        'description' => 'Description',
        'custom_1' => 'Custom1',
        'custom_2' => 'Custom2',
        'custom_3' => 'Custom3',
    ];

    public function getV2XmlAttribute()
    {
        return self::$mappings[$this->getValue()];
    }
}

<?php

namespace RetailExpress\SkyLink\Sdk\Catalogue\Products;

trait V2ProductPriceAttributeConverter
{
    private static $mappings = [
        'rrp' => 'RRP',
        'default_price' => 'DefaultPrice',
        'promotional_price' => 'PromotionalPrice',
        'web_price' => 'WebSellPrice',
        'master_pos_price' => 'MasterPOSPrice'
    ];

    private static $startAndEndDateMappings = [
        'promotional_price' => [
            'start_date' => 'PromotionStartDate',
            'end_date' => 'PromotionEndDate',
        ],
    ];

    public function getV2XmlAttribute()
    {
        return self::$mappings[$this->getValue()];
    }

    public function getV2XmlAttributesForStartDate()
    {
        return $this->extractV2XmlTimedAttribute('start_date');
    }

    public function getV2XmlAttributesForEndDate()
    {
        return $this->extractV2XmlTimedAttribute('end_date');
    }

    private function extractV2XmlTimedAttribute($kind)
    {
        // @todo throw exception? The use should have checked the start and end date
        if (!array_key_exists($this->getValue(), self::$startAndEndDateMappings)) {
            return null;
        }

        return self::$startAndEndDateMappings[$this->getValue()][$kind];
    }
}

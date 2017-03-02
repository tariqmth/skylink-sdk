<?php

namespace RetailExpress\SkyLink\Sdk\Catalogue\Products;

use RetailExpress\SkyLink\Sdk\Catalogue\Attributes\AttributeCode;
use RetailExpress\SkyLink\Sdk\Catalogue\Attributes\AttributeOption;
use Sabre\Xml\Deserializer as XmlDeserializer;
use Sabre\Xml\Reader as XmlReader;
use ValueObjects\StringLiteral\StringLiteral;

class V2ProductDeserializer
{
    private $nameAttribute;

    private $regularPriceAttribute;

    private $specialPriceAttribute;

    public function __construct(
        ProductNameAttribute $nameAttribute = null,
        ProductPriceAttribute $regularPriceAttribute = null,
        ProductPriceAttribute $specialPriceAttribute = null
    ) {
        if (null === $nameAttribute) {
            $nameAttribute = ProductNameAttribute::getDefault();
        }

        if (null === $regularPriceAttribute) {
            $regularPriceAttribute = ProductPriceAttribute::getDefaultForRegularPrice();
        }

        if (null === $specialPriceAttribute) {
            $specialPriceAttribute = ProductPriceAttribute::getDefaultForSpecialPrice();
        }

        $this->nameAttribute = $nameAttribute;
        $this->regularPriceAttribute = $regularPriceAttribute;
        $this->specialPriceAttribute = $specialPriceAttribute;
    }

    /**
     * @todo Validate options from Attribute Repository
     */
    public function xmlDeserialize(XmlReader $xmlReader)
    {
        $payload = XmlDeserializer\keyValue($xmlReader, '');

        $id = new ProductId($payload['ProductId']);
        $sku = new StringLiteral($payload['SKU']);

        $name = new StringLiteral($this->extractProductName($payload));

        $pricingStructure = PricingStructure::fromNative(
            $this->extractRegularPrice($payload),
            $this->extractSpecialPrice($payload),
            $payload['TaxRate']
        );

        array_map(function (PriceGroupPrice $priceGroupPrice) use (&$pricingStructure) {
            $pricingStructure = $pricingStructure->withPriceGroupPrice($priceGroupPrice);
        }, $this->extractPriceGroupPrices($payload));

        $inventoryItem = InventoryItem::fromNative(
            $payload['ManageStock'],
            $payload['StockAvailable'],
            $payload['StockOnOrder']
        );

        $physicalPackage = PhysicalPackage::fromNative(
            array_get_notempty($payload, 'Weight', 0),
            array_get_notempty($payload, 'Length', 0),
            array_get_notempty($payload, 'Breadth', 0),
            array_get_notempty($payload, 'Depth', 0),
            array_get_notempty($payload, 'ShippingCubic')
        );

        $options = self::extractAttributeOptions($payload, ['', 'Id']);

        return new SimpleProduct(
            $id,
            $sku,
            $name,
            $pricingStructure,
            $inventoryItem,
            $physicalPackage,
            $options
        );
    }

    private function extractProductName(array $payload)
    {
        return array_get(
            $payload,
            $this->nameAttribute->getV2XmlAttribute(),
            function () use ($payload) {
                return $payload[ProductNameAttribute::getDefault()->getV2XmlAttribute()];
            }
        );
    }

    private function extractRegularPrice(array $payload)
    {
        return array_get(
            $payload,
            $this->regularPriceAttribute->getV2XmlAttribute(),
            function () use ($payload) {
                return $payload[ProductPriceAttribute::getDefaultForRegularPrice()->getV2XmlAttribute()];
            }
        );
    }

    private function extractSpecialPrice(array $payload)
    {
        return array_get(
            $payload,
            $this->specialPriceAttribute->getV2XmlAttribute(),
            function () use ($payload) {
                return $payload[ProductPriceAttribute::getDefaultForSpecialPrice()->getV2XmlAttribute()];
            }
        );
    }

    private function extractPriceGroupPrices(array $payload)
    {
        $priceGroupPrices = [];

        array_map(function (array $priceGroupPricePayload) use (&$priceGroupPrices) {
            $priceGroupPrices[] = $this->convertPriceGroupPricePayload('standard', $priceGroupPricePayload);
        }, array_get_notempty($payload, 'Standard_Price_Group', []));

        array_map(function (array $priceGroupPricePayload) use (&$priceGroupPrices) {
            $priceGroupPrices[] = $this->convertPriceGroupPricePayload('fixed', $priceGroupPricePayload);
        }, array_get_notempty($payload, 'Fixed_Price_Group', []));

        return $priceGroupPrices;
    }

    private function convertPriceGroupPricePayload($type, array $priceGroupPricePayload)
    {
        return PriceGroupPrice::fromNative(
            $type,
            $priceGroupPricePayload['attributes']['Id'],
            $priceGroupPricePayload['value']
        );
    }

    private static function extractAttributeOptions(array $payload, array $suffixesToCheck)
    {
        $options = [];

        foreach (AttributeCode::getConstants() as $attributeCode) {
            $studlyAttributeCode = studly_case($attributeCode);

            foreach ($suffixesToCheck as $suffixToCheck) {
                $key = $studlyAttributeCode.$suffixToCheck;

                if (!array_key_exists($key, $payload) || empty($payload[$key])) {
                    continue;
                }

                $options[] = AttributeOption::fromNative($attributeCode, $payload[$key]);
            }
        }

        return $options;
    }
}

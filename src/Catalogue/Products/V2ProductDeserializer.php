<?php

namespace RetailExpress\SkyLink\Sdk\Catalogue\Products;

use RetailExpress\SkyLink\Sdk\Catalogue\Attributes\AttributeCode;
use RetailExpress\SkyLink\Sdk\Catalogue\Attributes\AttributeOption;
use Sabre\Xml\Deserializer as XmlDeserializer;
use Sabre\Xml\Reader as XmlReader;
use ValueObjects\StringLiteral\StringLiteral;

trait V2ProductDeserializer
{
    /**
     * @todo Validate options from Attribute Repository
     */
    public static function xmlDeserialize(XmlReader $xmlReader)
    {
        $payload = XmlDeserializer\keyValue($xmlReader, '');

        $id = new ProductId($payload['ProductId']);
        $sku = new StringLiteral($payload['SKU']);
        $name = new StringLiteral($payload['Description']);

        $pricingStructure = PricingStructure::fromNative(
            $payload['DefaultPrice'],
            $payload['DiscountedPrice'],
            $payload['TaxRate']
        );

        $inventoryItem = InventoryItem::fromNative(
            $payload['ManageStock'],
            $payload['StockAvailable']
        );

        $physicalPackage = PhysicalPackage::fromNative(
            array_get_notempty($payload, 'Weight', 0),
            array_get_notempty($payload, 'Length', 0),
            array_get_notempty($payload, 'Breadth', 0),
            array_get_notempty($payload, 'Depth', 0),
            array_get_notempty($payload, 'ShippingCubic')
        );

        $options = self::extractAttributeOptions($payload, ['', 'Id']);

        return new self($id, $sku, $name, $pricingStructure, $inventoryItem, $physicalPackage, $options);
    }

    private static function extractAttributeOptions(array $payload, array $suffixesToCheck)
    {
        $options = [];

        foreach (AttributeCode::getConstants() as $attributeCode) {
            $studlyAttributeCode = studly_case($attributeCode);

            foreach ($suffixesToCheck as $suffixToCheck) {
                $key = $studlyAttributeCode.$suffixToCheck;

                if (!array_key_exists($key, $payload)) {
                    continue;
                }

                $options[] = AttributeOption::fromNative($attributeCode, $payload[$key]);
            }
        }

        return $options;
    }
}

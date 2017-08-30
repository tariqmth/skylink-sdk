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

        // @todo decide if this should be optional, e.g. $product->withManufacturerSku()
        // and just make Matrices use it? IDK...
        $manufacturerSku = new StringLiteral(array_get_notempty($payload, 'Code', ''));

        $name = new StringLiteral($this->extractProductName($payload));

        $description = new Description(array_get_notempty($payload, 'WebstoreDescription', ''));

        $pricingStructure = PricingStructure::fromNative(
            $this->extractRegularPrice($payload),
            $this->extractSpecialPrice($payload),
            $payload['TaxRate']
        );

        array_map(function (PriceGroupPrice $priceGroupPrice) use (&$pricingStructure) {
            $pricingStructure = $pricingStructure->withPriceGroupPrice($priceGroupPrice);
        }, $this->extractPriceGroupPrices($payload));

        $inventoryItem = $this->extractInventoryItem($payload, $id);

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
            $manufacturerSku,
            $name,
            $description,
            $pricingStructure,
            $inventoryItem,
            $physicalPackage,
            $options
        );
    }

    private function extractProductName(array $payload)
    {
        return array_get_notempty(
            $payload,
            $this->nameAttribute->getV2XmlAttribute(),
            function () use ($payload) {
                return $payload[ProductNameAttribute::getDefault()->getV2XmlAttribute()];
            }
        );
    }

    private function extractRegularPrice(array $payload)
    {
        return array_get_notempty(
            $payload,
            $this->regularPriceAttribute->getV2XmlAttribute(),
            function () use ($payload) {
                return $payload[ProductPriceAttribute::getDefaultForRegularPrice()->getV2XmlAttribute()];
            }
        );
    }

    private function extractSpecialPrice(array $payload)
    {
        $priceAttribute = $this->specialPriceAttribute;

        $price = array_get_notempty(
            $payload,
            $priceAttribute->getV2XmlAttribute()
        );

        // If we didn't find a price, we'll check if the default attribute is different.
        // If it is, we'll re-query the paylaod for that and then we can move on
        if (null === $price) {
            $defaultPriceAttribute = ProductPriceAttribute::getDefaultForSpecialPrice();

            // If we're already using the default attribute, move on
            if ($defaultPriceAttribute->sameValueAs($priceAttribute)) {
                return null;
            }

            $priceAttribute = $defaultPriceAttribute;

            $price = array_get_notempty(
                $payload,
                $priceAttribute->getV2XmlAttribute()
            );

            // If we still don't have a price, give up
            if (null === $price) {
                return null;
            }
        }

        if (false === $this->specialPriceAttribute->isTimed()) {
            return $price;
        }

        $startDate = array_get_notempty(
            $payload,
            $this->specialPriceAttribute->getV2XmlAttributesForStartDate()
        );

        $endDate = array_get_notempty(
            $payload,
            $this->specialPriceAttribute->getV2XmlAttributesForEndDate()
        );

        return [
            $price,
            $this->convertOptionalDateToTimestamp($startDate),
            $this->convertOptionalDateToTimestamp($endDate)
        ];
    }

    private function convertOptionalDateToTimestamp($date)
    {
        if (null === $date) {
            return null;
        }

        return from_v2_rex_date_to_timestamp($date);
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

    private function extractInventoryItem(array $payload, ProductId $id)
    {
        $outletQtys = [];

        foreach (array_get_notempty($payload, 'StockAvailablePerOutlet', []) as $outletQtyPayload) {
            $outletId = $outletQtyPayload['attributes']['Id'];
            $qty = (int) $outletQtyPayload['value'];

            if (array_key_exists($outletId, $outletQtys)) {
                continue;
            }

            $outletQtys[$outletId] = [$outletId, $qty];
        }

        $inventoryItem = InventoryItem::fromNative(
            $payload['ManageStock'],
            $payload['StockAvailable'],
            $payload['StockOnOrder'],
            array_values($outletQtys)
        );
        $inventoryItem->setProductId($id);

        return $inventoryItem;
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

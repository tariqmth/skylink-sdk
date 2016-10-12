<?php

namespace RetailExpress\SkyLink\Catalogue\Products;

use RetailExpress\SkyLink\Catalogue\Attributes\AttributeCode;
use RetailExpress\SkyLink\Catalogue\Attributes\AttributeOption;
use Sabre\Xml\XmlDeserializable;
use ValueObjects\StringLiteral\StringLiteral;

class SimpleProduct implements Product, XmlDeserializable
{
    use V2ProductDeserializer;

    private $id;

    private $sku;

    private $name;

    private $pricingStructure;

    private $inventoryItem;

    private $physicalPackage;

    private $attributeOptions = [];

    public function __construct(
        ProductId $id,
        StringLiteral $sku,
        StringLiteral $name,
        PricingStructure $pricingStructure,
        InventoryItem $inventoryItem,
        PhysicalPackage $physicalPackage,
        array $attributeOptions
    ) {
        $this->id = $id;
        $this->sku = $sku;
        $this->name = $name;
        $this->pricingStructure = $pricingStructure;
        $this->inventoryItem = $inventoryItem;
        $this->physicalPackage = $physicalPackage;

        $this->attributeOptions = array_map(function (AttributeOption $attributeOption) {
            return $attributeOption;
        }, $attributeOptions);
    }

    public function getId()
    {
        return clone $this->id;
    }

    public function getSku()
    {
        return clone $this->sku;
    }

    public function getName()
    {
        return clone $this->name;
    }

    public function getPricingStructure()
    {
        return clone $this->pricingStructure;
    }

    public function getInventoryItem()
    {
        return clone $this->inventoryItem;
    }

    public function getPhysicalPackage()
    {
        return clone $this->physicalPackage;
    }

    public function getAttributeOptions()
    {
        return array_map(function (AttributeOption $attributeOption) {
            return clone $attributeOption;
        }, $this->attributeOptions);
    }

    public function getAttributeOption(AttributeCode $attributeCode)
    {
        foreach ($this->getAttributeOptions() as $attributeOption) {
            if ($attributeOption->getAttribute()->getCode()->sameValueAs($attributeCode)) {
                return $attributeOption;
            }
        }
    }

    public function getProductType()
    {
        return $this->getAttributeOption(AttributeCode::fromNative('product_type'));
    }
}

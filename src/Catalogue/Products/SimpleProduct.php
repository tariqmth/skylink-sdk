<?php

namespace RetailExpress\SkyLink\Sdk\Catalogue\Products;

use RetailExpress\SkyLink\Sdk\Catalogue\Attributes\AttributeCode;
use RetailExpress\SkyLink\Sdk\Catalogue\Attributes\AttributeOption;
use ValueObjects\StringLiteral\StringLiteral;

class SimpleProduct implements Product
{
    private $id;

    private $sku;

    private $name;

    private $description;

    private $pricingStructure;

    private $inventoryItem;

    private $physicalPackage;

    private $attributeOptions = [];

    public function __construct(
        ProductId $id,
        StringLiteral $sku,
        StringLiteral $name,
        Description $description,
        PricingStructure $pricingStructure,
        InventoryItem $inventoryItem,
        PhysicalPackage $physicalPackage,
        array $attributeOptions
    ) {
        $this->id = $id;
        $this->sku = $sku;
        $this->name = $name;
        $this->description = $description;
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

    public function getDescription()
    {
        return clone $this->description;
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

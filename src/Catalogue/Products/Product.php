<?php

namespace RetailExpress\SkyLink\Catalogue\Products;

use RetailExpress\SkyLink\Catalogue\AttributeOption;
use Sabre\Xml\XmlDeserializable;
use ValueObjects\StringLiteral\StringLiteral;

class Product implements XmlDeserializable
{
    use V2ProductDeserializer;

    private $id;

    private $sku;

    private $name;

    private $pricingStructure;

    private $inventoryItem;

    private $physicalPackage;

    private $attributeOptions = [];

    /**
     * Returns an Product taking PHP native values as arguments.
     *
     * @return ValueObjectInterface
     */
    public static function fromNative()
    {
        $args = func_get_args();

        $id = new ProductId($args[0]);
        $sku = new StringLiteral($args[1]);
        $name = new StringLiteral($args[2]);
        $pricingStructure = PricingStructure::fromNative($args[3], $args[4]);
        $inventoryItem = InventoryItem::fromNative($args[5], $args[6]);
        $physicalPackage = physicalPackage::fromNative($args[7], $args[8], $args[9], $args[10], $args[11]);

        return new self(
            $id,
            $sku,
            $name,
            $pricingStructure,
            $inventoryItem,
            $physicalPackage,
            []
        );
    }

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
}

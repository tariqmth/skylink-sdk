<?php

namespace RetailExpress\SkyLink\Products;

use ValueObjects\StringLiteral\StringLiteral;

class PendingProduct
{
    use Product;

    private $id;

    private $sku;

    private $name;

    private $pricingStructure;

    private $inventoryItem;

    private $physicalPackage;

    private $pendingConfigurableProductState;

    /**
     * Returns an Inventory Item taking PHP native values as arguments.
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
        $pendingConfigurableProductState = PendingConfigurableProductState::fromNative($args[12]);

        return new self(
            $id,
            $sku,
            $name,
            $pricingStructure,
            $inventoryItem,
            $physicalPackage,
            $pendingConfigurableProductState
        );
    }

    public function __construct(
        ProductId $id,
        StringLiteral $sku,
        StringLiteral $name,
        PricingStructure $pricingStructure,
        InventoryItem $inventoryItem,
        PhysicalPackage $physicalPackage,
        PendingConfigurableProductState $pendingConfigurableProductState
    ) {
        $this->id = $id;
        $this->sku = $sku;
        $this->name = $name;
        $this->pricingStructure = $pricingStructure;
        $this->inventoryItem = $inventoryItem;
        $this->physicalPackage = $physicalPackage;
        $this->pendingConfigurableProductState = $pendingConfigurableProductState;
    }

    public function getPendingConfigurableProductState()
    {
        return $this->pendingConfigurableProductState;
    }
}

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
        $pendingConfigurableProductState = PendingConfigurableProductState::fromNative($args[7]);

        return new self(
            $id,
            $sku,
            $name,
            $pricingStructure,
            $inventoryItem,
            $pendingConfigurableProductState
        );
    }

    public function __construct(
        ProductId $id,
        StringLiteral $sku,
        StringLiteral $name,
        PricingStructure $pricingStructure,
        InventoryItem $inventoryItem,
        PendingConfigurableProductState $pendingConfigurableProductState
    ) {
        $this->id = $id;
        $this->sku = $sku;
        $this->name = $name;
        $this->pricingStructure = $pricingStructure;
        $this->inventoryItem = $inventoryItem;
        $this->pendingConfigurableProductState = $pendingConfigurableProductState;
    }

    public function getPendingConfigurableProductState()
    {
        return $this->pendingConfigurableProductState;
    }
}

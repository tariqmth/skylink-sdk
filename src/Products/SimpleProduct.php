<?php

namespace RetailExpress\SkyLink\Products;

use ValueObjects\StringLiteral\StringLiteral;

/**
 * @todo Create interface for converting from pending product.
 */
class SimpleProduct
{
    use Product;

    private $id;

    private $sku;

    private $name;

    private $pricingStructure;

    private $inventoryItem;

    public static function fromPendingProduct(PendingProduct $pendingProduct)
    {
        return new self(
            $pendingProduct->getId(),
            $pendingProduct->getSku(),
            $pendingProduct->getName(),
            $pendingProduct->getPricingStructure(),
            $pendingProduct->getInventoryItem()
        );
    }

    public function __construct(
        ProductId $id,
        StringLiteral $sku,
        StringLiteral $name,
        PricingStructure $pricingStructure,
        InventoryItem $inventoryItem
    ) {
        $this->id = $id;
        $this->sku = $sku;
        $this->name = $name;
        $this->pricingStructure = $pricingStructure;
        $this->inventoryItem = $inventoryItem;
    }
}

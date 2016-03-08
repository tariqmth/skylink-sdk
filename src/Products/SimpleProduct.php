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

    private $physicalPackage;

    public static function fromPendingProduct(PendingProduct $pendingProduct)
    {
        return new self(
            $pendingProduct->getId(),
            $pendingProduct->getSku(),
            $pendingProduct->getName(),
            $pendingProduct->getPricingStructure(),
            $pendingProduct->getInventoryItem(),
            $pendingProduct->getPhysicalPackage()
        );
    }

    public function __construct(
        ProductId $id,
        StringLiteral $sku,
        StringLiteral $name,
        PricingStructure $pricingStructure,
        InventoryItem $inventoryItem,
        PhysicalPackage $physicalPackage
    ) {
        $this->id = $id;
        $this->sku = $sku;
        $this->name = $name;
        $this->pricingStructure = $pricingStructure;
        $this->inventoryItem = $inventoryItem;
        $this->physicalPackage = $physicalPackage;
    }
}

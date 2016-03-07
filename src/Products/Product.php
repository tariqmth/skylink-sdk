<?php

namespace RetailExpress\SkyLink\Products;

use ValueObjects\StringLiteral\StringLiteral;

class Product
{
    private $id;

    private $sku;

    private $name;

    private $inventoryItem;

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
        $inventoryItem = InventoryItem::fromNative($args[3], $args[4]);

        return new self($id, $sku, $name, $inventoryItem);
    }

    public function __construct(
        ProductId $id,
        StringLiteral $sku,
        StringLiteral $name,
        InventoryItem $inventoryItem
    ) {
        $this->id = $id;
        $this->sku = $sku;
        $this->name = $name;
        $this->inventoryItem = $inventoryItem;
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

    public function getInventoryItem()
    {
        return clone $this->inventoryItem;
    }
}

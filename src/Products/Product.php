<?php

namespace RetailExpress\SkyLink\Products;

trait Product
{
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
}

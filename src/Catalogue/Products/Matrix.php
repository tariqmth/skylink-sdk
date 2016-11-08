<?php

namespace RetailExpress\SkyLink\Sdk\Catalogue\Products;

use RetailExpress\SkyLink\Sdk\Catalogue\Attributes\AttributeCode;

class Matrix implements Product
{
    private $policy;

    private $products = [];

    public function __construct(MatrixPolicy $policy, array $products)
    {
        $this->policy = $policy;

        $this->products = array_map(function (Product $product) {
            return $product;
        }, $products);
    }

    public function getPolicy()
    {
        return clone $this->policy;
    }

    public function getProducts()
    {
        return array_map(function (Product $product) {
            return clone $product;
        }, $this->products);
    }

    public function getId()
    {
        return null;
    }

    public function getSku()
    {
        return $this->getName();
    }

    public function getName()
    {
        return clone $this->getRepresentativeProduct()->getName();
    }

    public function getPricingStructure()
    {
        return clone $this->getRepresentativeProduct()->getPricingStructure();
    }

    public function getInventoryItem()
    {
        return clone $this->getRepresentativeProduct()->getInventoryItem();
    }

    public function getPhysicalPackage()
    {
        return clone $this->getRepresentativeProduct()->getPhysicalPackage();
    }

    public function getAttributeOption(AttributeCode $attributeCode)
    {
        return clone $this->getRepresentativeProduct()->getAttributeOption($attributeCode);
    }

    public function getProductType()
    {
        return clone $this->getRepresentativeProduct()->getProductType();
    }

    private function getRepresentativeProduct()
    {
        $products = $this->getProducts();

        return array_shift($products);
    }
}

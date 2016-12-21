<?php

namespace RetailExpress\SkyLink\Sdk\Catalogue\Products;

use RetailExpress\SkyLink\Sdk\Catalogue\Attributes\Attribute;
use RetailExpress\SkyLink\Sdk\Catalogue\Attributes\AttributeCode;
use ValueObjects\StringLiteral\StringLiteral;

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
        return new StringLiteral(str_slug($this->getName()));
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
        // If our attribute code is listed in our Policy, the Matrix itself cannot suck
        // the options for this attribute code from any products, because presumably
        // they're all different
        $inPolicy = $this->attributeCodeIsInPolicy($attributeCode);

        if (true === $inPolicy) {
            return null;
        }

        $attributeOption = $this->getRepresentativeProduct()->getAttributeOption($attributeCode);

        if (null !== $attributeOption) {
            return clone $attributeOption;
        }
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

    private function attributeCodeIsInPolicy(AttributeCode $attributeCode)
    {
        $matchingAttributes = array_filter(array_map(
            function (Attribute $policyAttribute) use ($attributeCode) {
                return $policyAttribute->getCode()->sameValueAs($attributeCode);
            },
            $this->getPolicy()->getAttributes()
        ));

        return count($matchingAttributes) > 0;
    }
}

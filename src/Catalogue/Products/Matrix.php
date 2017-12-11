<?php

namespace RetailExpress\SkyLink\Sdk\Catalogue\Products;

use InvalidArgumentException;
use RetailExpress\SkyLink\Sdk\Catalogue\Attributes\AttributeCode;
use ValueObjects\StringLiteral\StringLiteral;

class Matrix implements Product, CompositeProduct
{
    private $policy;

    private $products = [];

    public function __construct(MatrixPolicy $policy, array $products)
    {
        $this->policy = $policy;

        $this->products = array_map(function (SimpleProduct $product) {
            return $product;
        }, $products);

        $this->assertProductsAllHaveTheSameManufacturerSku();

        $this->policy->assertProductsAreConfiguredCorrectly($this);
    }

    public function getPolicy()
    {
        return clone $this->policy;
    }

    /**
     * {@inheritdoc}
     */
    public function getProducts()
    {
        return array_map(function (SimpleProduct $product) {
            return clone $product;
        }, $this->products);
    }

    public function getProduct(ProductId $productId)
    {
        return array_first($this->getProducts(), function ($key, SimpleProduct $product) use ($productId) {
            return $product->getId()->sameValueAs($productId);
        });
    }

    public function getId()
    {
        return null;
    }

    public function getSku()
    {
        return clone $this->getManufacturerSku();
    }

    public function getManufacturerSku()
    {
        return clone $this->getRepresentativeProduct()->getManufacturerSku();
    }

    public function getName()
    {
        return clone $this->getRepresentativeProduct()->getName();
    }

    public function getDescription()
    {
        return clone $this->getRepresentativeProduct()->getDescription();
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
            function (AttributeCode $policyAttributeCode) use ($attributeCode) {
                return $policyAttributeCode->sameValueAs($attributeCode);
            },
            $this->getPolicy()->getAttributeCodes()
        ));

        return count($matchingAttributes) > 0;
    }

    private function assertProductsAllHaveTheSameManufacturerSku()
    {
        $manufacturerSkus = array_map(function (SimpleProduct $product) {
            return (string) $product->getManufacturerSku();
        }, $this->getProducts());

        $uniqueManufacturerSkus = array_unique($manufacturerSkus);

        if (count($uniqueManufacturerSkus) > 1) {
            throw new InvalidArgumentException(sprintf(
                'A matrix requires all products have the same manufacturer sku, "%s" used.',
                implode(', ', $uniqueManufacturerSkus)
            ));
        }
    }

    public function getMatrixProduct()
    {
        return null;
    }

    public function setMatrixProduct(Matrix $matrixProduct)
    {
        return null;
    }
}

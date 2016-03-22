<?php

namespace RetailExpress\SkyLink\Catalogue\Products;

use InvalidArgumentException;

class MatrixPolicyMapper
{
    private $matrixPolicies = [];

    public function withPolicyForProductType(MatrixPolicy $policy, AttributeOption $productType)
    {
        $this->validateProductTypeAttributeOption($productType);

        $new = clone $this;
        $new->matrixPolicies[$this->getStorageKey($productType)] = $policy;

        return $new;
    }

    public function getPolicyForProductType(AttributeOption $productType)
    {
        $this->validateProductTypeAttributeOption($productType);

        $key = $this->getStorageKey($productType);

        if (array_key_exists($key, $this->matrixPolicies)) {
            return $matrixPolicies[$key];
        }

        return MatrixPolicy::getDefault();
    }

    private function validateProductTypeAttributeOption(AttributeOption $productType)
    {
        // Techncally any attribute option could be passed here. We enforce a business rule
        // that it is a "product type" attribute option by checking the attribute
        // encapsulated by the attribute option actually is "product type".
        $productTypeCode = AttributeCode::fromNative('product_type');
        $actualCode = $productType->getAttribute()->getCode();

        if (!$actualCode->sameValueAs($productTypeCode)) {
            throw new InvalidArgumentException("Matrix policies work using \"product_type\" attribute options, \"{$productTypeCode}\" given.");
        }
    }

    private function getStorageKey(AttributeOption $productType)
    {
        return $productType->getId()->toNative();
    }
}

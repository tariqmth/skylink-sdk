<?php

namespace RetailExpress\SkyLink\Sdk\Catalogue\Products;

use InvalidArgumentException;
use RetailExpress\SkyLink\Sdk\Catalogue\Attributes\AttributeCode;
use RetailExpress\SkyLink\Sdk\Catalogue\Attributes\AttributeOptionId;
use ValueObjects\StringLiteral\StringLiteral;

class MatrixPolicyProductsNotConfiguredCorrectlyException extends InvalidArgumentException
{
    public static function withNotAllProductsHavingOptionsForAnyAttribute(
        StringLiteral $matrixSku,
        array $attributeCodes
    ) {
        return new self(sprintf(
            'Matrix "%s" requires at least one attribute (%s) has options defined on all of it\'s products.',
            $matrixSku,
            implode(', ', $attributeCodes)
        ));
    }

    public static function withNoProductsHavingOptionsForAttributeWhenAllAreRequired(
        StringLiteral $matrixSku,
        AttributeCode $attributeCode
    ) {
        return new self(sprintf(
            'Matrix "%s" requires all it\'s products have options for all attributes, however no products have options for the "%s" attribute.',
            $matrixSku,
            $attributeCode
        ));
    }

    public static function withNotAllProductsHavingOptionsForAttribute(
        StringLiteral $matrixSku,
        array $productIds,
        AttributeCode $attributeCode
    ) {
        return new self(sprintf(
            'Matrix "%s" requires all it\'s products have values for the "%s" attribute, however products %s do not. This attribute is required on all products because some products have options for it.',
            $matrixSku,
            $attributeCode,
            implode(', ', $productIds)
        ));
    }

    public static function withMultipleProductsUsingTheSameOptionForAttribute(
        StringLiteral $matrixSku,
        array $productIds,
        AttributeOptionId $attributeOptionId,
        AttributeCode $attributeCode
    ) {
        return new self(sprintf(
            'Matrix "%s" requires all it\'s products have unique options, however products %s all use option "%s" for the "%s" attribute.',
            $matrixSku,
            implode(', ', $productIds),
            $attributeOptionId,
            $attributeCode
        ));

        dd('hi');
    }
}

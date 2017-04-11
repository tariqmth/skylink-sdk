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

    public static function withMultipleProductsUsingTheSameCombinationOfOptions(
        StringLiteral $matrixSku,
        array $productIds,
        array $combination
    ) {
        $message = sprintf(
            'Matrix "%s" requires all it\'s products have unique combinations of options, however products %s all use the same combination, ',
            $matrixSku,
            implode(', ', $productIds)
        );

        $optionMessages = array_map(function (array $codeAndOption) {
            return sprintf('"%s" for the "%s" attribute', $codeAndOption['attributeOptionId'], $codeAndOption['attributeCode']);
        }, $combination);

        $message .= sprintf('%s.', implode(', ', $optionMessages));

        return new self($message);
    }
}

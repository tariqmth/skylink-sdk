<?php

namespace RetailExpress\SkyLink\Sdk\Catalogue\Products;

use BadMethodCallException;
use RetailExpress\SkyLink\Sdk\Catalogue\Attributes\Attribute;
use RetailExpress\SkyLink\Sdk\Catalogue\Attributes\AttributeCode;
use RetailExpress\SkyLink\Sdk\Catalogue\Attributes\AttributeOptionId;

class MatrixPolicy
{
    private $attributes = [];

    private $requirement;

    public static function fromNative()
    {
        $args = func_get_args();

        if (count($args) < 1) {
            throw new BadMethodCallException('You must at least provide 1 argument: 1) attribute code names');
        }

        $attributes = array_map(function ($attributeCodeName) {
            return Attribute::fromNative($attributeCodeName);
        }, $args[0]);

        if (isset($args[1])) {
            $requirement = MatrixPolicyRequirement::get($args[1]);
        } else {
            $requirement = MatrixPolicyRequirement::getDefault();
        }

        return new self($attributes, $requirement);
    }

    public static function getDefault()
    {
        return self::fromNative(['size', 'colour']);
    }

    public function __construct(array $attributes, MatrixPolicyRequirement $requirement = null)
    {
        $this->attributes = array_map(function (Attribute $attribute) {
            $this->assertAttributeIsAllowed($attribute);

            return $attribute;
        }, $attributes);

        $this->requirement = $requirement ?: MatrixPolicyRequirement::getDefault();
    }

    public function getAttributes()
    {
        return array_map(function (Attribute $attribute) {
            return clone $attribute;
        }, $this->attributes);
    }

    public function getRequirement()
    {
        return $this->requirement;
    }

    public function assertProductsAreConfiguredCorrectly(Matrix $matrix)
    {
        $products = $matrix->getProducts();

        $productOptionsByCodes = [];

        array_map(function (Attribute $attribute) use ($products, &$productOptionsByCodes) {

            $productOptions = array_map(function (SimpleProduct $product) use ($attribute) {
                return [
                    'option' => (string) $product->getAttributeOption($attribute->getCode()),
                    'id' => $product->getId()->toNative(),
                ];
            }, $products);

            $productOptionsByCodes[(string) $attribute->getCode()] = $productOptions;

        }, $this->getAttributes());

        // We'll build an array of indexes (in the previous array) who don't have a value and we'll group them by
        // code
        $indexesWithoutOptionsByCodes = [];
        array_walk($productOptionsByCodes, function (array $productOptionsByCode, $code) use (&$indexesWithoutOptionsByCodes) {
            array_walk($productOptionsByCode, function (array $productOptionByCode, $index) use (&$indexesWithoutOptionsByCodes, $code) {
                if (null === $productOptionByCode['option']) {
                    $indexesWithoutOptionsByCodes[$code][] = $index;
                }
            });
        });

        // If there are as many indexes without options as there are options, we know none have been configured correctly
        if (count($indexesWithoutOptionsByCodes) === count($productOptionsByCodes)) {
            throw MatrixPolicyProductsNotConfiguredCorrectlyException::withNotAllProductsHavingOptionsForAnyAttribute(
                $matrix->getSku(),
                array_map(function ($code) {
                    return AttributeCode::get($code);
                }, array_keys($productOptionsByCodes))
            );
        }

        // Firstly, we'll go through the indexes without product codes and compare their count against the product count.
        // We know that if the index has less than the product count, then only some of the products are filled out
        // and that means there are some missing
        array_walk($indexesWithoutOptionsByCodes, function ($indexesWithoutOptionsByCode, $code) use ($matrix, $productOptionsByCodes) {

            // If there are no values for this attribute, we'll check if our matrix requirement is that all attributes
            // have values. If so, we'll complain that this attribute is missing all of it's values and it can't
            // be skipped.
            if (count($indexesWithoutOptionsByCode) === count($productOptionsByCodes[$code])) {
                if ($this->getRequirement()->isAny()) {
                    continue;
                }

                throw MatrixPolicyProductsNotConfiguredCorrectlyException::withNoProductsHavingOptionsForAttributeWhenAllAreRequired(
                    $matrix->getSku(),
                    AttributeCode::get($code)
                );
            }

            $missingProducts = array_intersect_key($productOptionsByCodes[$code], array_flip($indexesWithoutOptionsByCode));

            throw MatrixPolicyProductsNotConfiguredCorrectlyException::withNotAllProductsHavingOptionsForAttribute(
                $matrix->getSku(),
                array_map(function (array $missingProduct) {
                    return new ProductId($missingProduct['id']);
                }, $missingProducts),
                AttributeCode::get($code)
            );
        });

        // Now we'll loop through all options and check their values are unique
        array_walk($productOptionsByCodes, function (array $productOptionsByCode, $code) use ($matrix) {
            $productIdsByOptions = [];

            array_walk($productOptionsByCode, function (array $productOptionByCode) use (&$productIdsByOptions) {
                $productIdsByOptions[$productOptionByCode['option']][] = $productOptionByCode['id'];
            });

            array_walk($productIdsByOptions, function (array $productIds, $option) use ($matrix, $code) {
                if (count($productIds) === 1) {
                    return;
                }

                throw MatrixPolicyProductsNotConfiguredCorrectlyException::withMultipleProductsUsingTheSameOptionForAttribute(
                    $matrix->getSku(),
                    array_map(function ($productId) {
                        return new ProductId($productId);
                    }, $productIds),
                    new AttributeOptionId((string) $option),
                    AttributeCode::get($code)
                );
            });

            dd($productIdsByOptions);
        });

        dd($indexesWithoutOptionsByCodes);
    }

    private function assertAttributeIsAllowed(Attribute $attribute)
    {
        $notAllowed = AttributeCode::fromNative('product_type');

        if ($attribute->getCode()->samevalueAs($notAllowed)) {
            throw new \InvalidArgumentException("Attribute \"{$attribute->getCode()}\" is not allowed in a Matrix policy.");
        }
    }
}

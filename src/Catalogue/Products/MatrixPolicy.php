<?php

namespace RetailExpress\SkyLink\Sdk\Catalogue\Products;

use BadMethodCallException;
use RetailExpress\SkyLink\Sdk\Catalogue\Attributes\AttributeCode;
use RetailExpress\SkyLink\Sdk\Catalogue\Attributes\AttributeOptionId;

class MatrixPolicy
{
    private $attributeCodes = [];

    private $requirement;

    public static function fromNative()
    {
        $args = func_get_args();

        if (count($args) < 1) {
            throw new BadMethodCallException('You must at least provide 1 argument: 1) attribute code names');
        }

        $attributeCodes = array_map(function ($attributeCodeName) {
            return AttributeCode::fromNative($attributeCodeName);
        }, $args[0]);

        if (isset($args[1])) {
            $requirement = MatrixPolicyRequirement::get($args[1]);
        } else {
            $requirement = MatrixPolicyRequirement::getDefault();
        }

        return new self($attributeCodes, $requirement);
    }

    public static function getDefault()
    {
        return self::fromNative(['size', 'colour']);
    }

    public function __construct(array $attributeCodes, MatrixPolicyRequirement $requirement = null)
    {
        $this->attributeCodes = array_map(function (AttributeCode $attributeCode) {
            $this->assertAttributeCodeIsAllowed($attributeCode);

            return $attributeCode;
        }, $attributeCodes);

        $this->requirement = $requirement ?: MatrixPolicyRequirement::getDefault();
    }

    public function getAttributeCodes()
    {
        return $this->attributeCodes;
    }

    public function getRequirement()
    {
        return $this->requirement;
    }

    public function assertProductsAreConfiguredCorrectly(Matrix $matrix)
    {
        $products = $matrix->getProducts();

        $productOptionsByCodes = [];

        array_map(function (AttributeCode $attributeCode) use ($products, &$productOptionsByCodes) {

            $productOptions = array_map(function (SimpleProduct $product) use ($attributeCode) {
                return [
                    'option' => (string) $product->getAttributeOption($attributeCode),
                    'id' => $product->getId()->toNative(),
                ];
            }, $products);

            $productOptionsByCodes[(string) $attributeCode] = $productOptions;

        }, $this->getAttributeCodes());

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
                    return;
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

        // Now we'll create an array where the product ID is the key and the value is key/value pair of attribute codes
        // and options. We can then use this to make sure all of the arrays are unique
        $combinationsByIds = [];
        array_walk($productOptionsByCodes, function (array $productOptionsByCode, $code) use ($matrix, &$combinationsByIds) {

            array_walk($productOptionsByCode, function (array $productOptionByCode) use (&$combinationsByIds, $code) {
                $combinationsByIds[$productOptionByCode['id']][$code] = $productOptionByCode['option'];
            });
        });

        $productIdsByOptionHashes = [];
        array_walk($combinationsByIds, function (array $combinationById, $id) use (&$productIdsByOptionHashes) {
            $key = md5(serialize($combinationById));
            $productIdsByOptionHashes[$key][] = $id;
        });

        array_walk($productIdsByOptionHashes, function (array $productIds) use ($matrix, $combinationsByIds) {
            if (count($productIds) === 1) {
                return;
            }

            $combination = [];
            foreach ($combinationsByIds[$productIds[0]] as $code => $option) {
                $combination[] = [
                    'attributeCode' => AttributeCode::get($code),
                    'attributeOptionId' => new AttributeOptionId((string) $option),
                ];
            }

            throw MatrixPolicyProductsNotConfiguredCorrectlyException::withMultipleProductsUsingTheSameCombinationOfOptions(
                $matrix->getSku(),
                array_map(function ($productId) {
                    return new ProductId($productId);
                }, $productIds),
                $combination
            );
        });
    }

    private function assertAttributeCodeIsAllowed(AttributeCode $attributeCode)
    {
        $notAllowed = AttributeCode::fromNative('product_type');

        if ($attributeCode->samevalueAs($notAllowed)) {
            throw new \InvalidArgumentException("Attribute \"{$attributeCode}\" is not allowed in a Matrix policy.");
        }
    }
}

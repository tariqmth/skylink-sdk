<?php

namespace RetailExpress\SkyLink\Sdk\Catalogue\Products;

use BadMethodCallException;
use RetailExpress\SkyLink\Sdk\Catalogue\Attributes\Attribute;
use RetailExpress\SkyLink\Sdk\Catalogue\Attributes\AttributeCode;

class MatrixPolicy
{
    private $attributes = [];

    public static function fromNative()
    {
        $args = func_get_args();

        if (count($args) < 1) {
            throw new BadMethodCallException('You must at least provide 1 argument: 1) attribute code names');
        }

        $attributes = array_map(function ($attributeCodeName) {
            return Attribute::fromNative($attributeCodeName);
        }, $args[0]);

        return new self($attributes);
    }

    public static function getDefault()
    {
        return self::fromNative(['size', 'colour']);
    }

    public function __construct(array $attributes)
    {
        $this->attributes = array_map(function (Attribute $attribute) {
            $this->assertAttributeIsAllowed($attribute);

            return $attribute;
        }, $attributes);
    }

    public function getAttributes()
    {
        return array_map(function (Attribute $attribute) {
            return clone $attribute;
        }, $this->attributes);
    }

    private function assertAttributeIsAllowed(Attribute $attribute)
    {
        $notAllowed = AttributeCode::fromNative('product_type');

        if ($attribute->getCode()->samevalueAs($notAllowed)) {
            throw new \InvalidArgumentException("Attribute \"{$attribute->getCode()}\" is not allowed in a Matrix policy.");
        }
    }
}

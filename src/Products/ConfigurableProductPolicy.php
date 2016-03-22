<?php

namespace RetailExpress\SkyLink\Catalogue\Products;

class ConfigurableProductPolicy
{
    private $attributes = [];

    public static function fromNative()
    {
        $args = func_get_args();

        $attributes = array_map(function ($attributeCodeName) {
            return Attribute::fromNative($attributeCodeName);
        }, $args[0]);

        return new self($attributes);
    }

    public function __construct(array $attributes)
    {
        foreach ($attributes as $attribute) {
            $this->registerAttribute($attribute);
        }
    }

    private function registerAttribute(Attribute $attribute)
    {
        $this->attributes[] = $attribute;
    }
}

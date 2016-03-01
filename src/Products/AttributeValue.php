<?php

namespace RetailExpress\SkyLink\Products;

use ValueObjects\ValueObjectInterface;

class AttributeValue implements ValueObjectInterface
{
    /**
     * Returns an Attribute Value object taking PHP native values as arguments.
     *
     * @return AttributeValue
     */
    public static function fromNative()
    {
        $args = func_get_args();
    }

    /**
     * Compare two ValueObjectInterface and tells whether they can be considered equal
     *
     * @param  AttributeValue $object
     * @return bool
     */
    public function sameValueAs(ValueObjectInterface $attributeValue);

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function __toString();
}

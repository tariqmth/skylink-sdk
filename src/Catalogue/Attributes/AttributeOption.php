<?php

namespace RetailExpress\SkyLink\Sdk\Catalogue\Attributes;

use BadMethodCallException;
use ValueObjects\StringLiteral\StringLiteral;
use ValueObjects\Util\Util;
use ValueObjects\ValueObjectInterface;

class AttributeOption implements ValueObjectInterface
{
    private $attribute;

    private $id;

    private $label;

    /**
     * Returns an Attribute Value object taking PHP native values as arguments.
     *
     * @return AttributeOption
     */
    public static function fromNative()
    {
        $args = func_get_args();

        if (count($args) < 2) {
            throw new BadMethodCallException('You must provide at least 2 arguments: 1) attribute code, 2) id');
        }

        $attribute = new Attribute(AttributeCode::fromNative($args[0]));
        $id = new StringLiteral($args[1]);
        $label = isset($args[2]) ? new StringLiteral($args[2]) : null;

        return new self($attribute, $id, $label);
    }

    public function __construct(Attribute $attribute, StringLiteral $id, StringLiteral $label = null)
    {
        $this->attribute = $attribute;
        $this->id = $id;
        $this->label = $label;
    }

    public function getAttribute()
    {
        return $this->attribute;
    }

    public function getLabel()
    {
        return clone $this->label;
    }

    public function getId()
    {
        return clone $this->id;
    }

    /**
     * Tells whether two Attribute Option instances are equal.
     *
     * @param ValueObjectInterface $attributeOption
     *
     * @return bool
     */
    public function sameValueAs(ValueObjectInterface $attributeOption)
    {
        if (false === Util::classEquals($this, $attributeOption)) {
            return false;
        }

        return $this->getAttribute()->sameValueAs($attributeOption->getAttribute()) &&
            $this->getLabel()->sameValueAs($attributeOption->getLabel()) &&
            $this->getId()->sameValueAs($attributeOption->getId());
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->optionId;
    }
}

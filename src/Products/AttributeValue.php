<?php

namespace RetailExpress\SkyLink\Products;

use BadMethodCallException;
use Sabre\Xml\XmlDeserializable;
use ValueObjects\StringLiteral\StringLiteral;
use ValueObjects\Util\Util;
use ValueObjects\ValueObjectInterface;

class AttributeValue implements ValueObjectInterface, XmlDeserializable
{
    use V2AttributeValueDeserializer;

    private $code;

    private $valueId;

    private $label;

    /**
     * Returns an Attribute Value object taking PHP native values as arguments.
     *
     * @return AttributeValue
     */
    public static function fromNative()
    {
        $args = func_get_args();

        if (count($args) < 3) {
            throw new BadMethodCallException('You must provide at least 3 arguments: 1) attribute code, 2) attribute value id, 3) label');
        }

        $code = AttributeCode::fromNative($args[0]);
        $valueId = new AttributeValueId($args[1]);
        $label = new StringLiteral($args[2]);

        return new self($code, $valueId, $label);
    }

    public function __construct(AttributeCode $code, AttributeValueId $valueId, StringLiteral $label)
    {
        $this->code = $code;
        $this->valueId = $valueId;
        $this->label = $label;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function getValueId()
    {
        return clone $this->valueId;
    }

    public function getLabel()
    {
        return clone $this->label;
    }

    /**
     * Tells whether two Attribute Value instances are equal.
     *
     * @param ValueObjectInterface $object
     *
     * @return bool
     */
    public function sameValueAs(ValueObjectInterface $attributeValue)
    {
        if (false === Util::classEquals($this, $attributeValue)) {
            return false;
        }

        return $this->getCode()->sameValueAs($attributeValue->getCode()) &&
            $this->getValueId()->sameValueAs($attributeValue->getValueId()) &&
            $this->getLabel()->sameValueAs($attributeValue->getLabel());
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->valueId;
    }
}

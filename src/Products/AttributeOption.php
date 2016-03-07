<?php

namespace RetailExpress\SkyLink\Products;

use BadMethodCallException;
use Sabre\Xml\XmlDeserializable;
use ValueObjects\StringLiteral\StringLiteral;
use ValueObjects\Util\Util;
use ValueObjects\ValueObjectInterface;

class AttributeOption implements ValueObjectInterface, XmlDeserializable
{
    use V2AttributeOptionDeserializer;

    private $code;

    private $optionId;

    private $label;

    /**
     * Returns an Attribute Option object taking PHP native options as arguments.
     *
     * @return AttributeOption
     */
    public static function fromNative()
    {
        $args = func_get_args();

        if (count($args) < 3) {
            throw new BadMethodCallException('You must provide at least 3 arguments: 1) attribute code, 2) attribute option id, 3) label');
        }

        $code = AttributeCode::fromNative($args[0]);
        $optionId = new AttributeOptionId($args[1]);
        $label = new StringLiteral($args[2]);

        return new self($code, $optionId, $label);
    }

    public function __construct(AttributeCode $code, AttributeOptionId $optionId, StringLiteral $label)
    {
        $this->code = $code;
        $this->optionId = $optionId;
        $this->label = $label;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function getOptionId()
    {
        return clone $this->optionId;
    }

    public function getLabel()
    {
        return clone $this->label;
    }

    /**
     * Tells whether two Attribute Option instances are equal.
     *
     * @param ValueObjectInterface $object
     *
     * @return bool
     */
    public function sameValueAs(ValueObjectInterface $attributeOption)
    {
        if (false === Util::classEquals($this, $attributeOption)) {
            return false;
        }

        return $this->getCode()->sameOptionAs($attributeOption->getCode()) &&
            $this->getOptionId()->sameOptionAs($attributeOption->getOptionId()) &&
            $this->getLabel()->sameOptionAs($attributeOption->getLabel());
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->optionId;
    }
}

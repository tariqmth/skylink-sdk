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

    private $attribute;

    private $label;

    private $id;

    /**
     * Returns an Attribute Option object taking PHP native options as arguments.
     *
     * @return AttributeOption
     */
    public static function fromNative()
    {
        $args = func_get_args();

        if (count($args) < 2) {
            throw new BadMethodCallException('You must provide at least 3 arguments: 1) attribute code, 2) label');
        }

        $attribute = new Attribute(AttributeCode::fromNative($args[0]));
        $label = new StringLiteral($args[1]);
        $id = new StringLiteral(array_get($args, 2, $args[1]));

        return new self($attribute, $label, $id);
    }

    public function __construct(Attribute $attribute, StringLiteral $label, StringLiteral $id)
    {
        $this->attribute = $attribute;
        $this->label = $label;
        $this->id = $id;
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
     * @param ValueObjectInterface $object
     *
     * @return bool
     */
    public function sameValueAs(ValueObjectInterface $attributeOption)
    {
        if (false === Util::classEquals($this, $attributeOption)) {
            return false;
        }

        return $this->getAttribute()->sameOptionAs($attributeOption->getAttribute()) &&
            $this->getLabel()->sameOptionAs($attributeOption->getLabel()) &&
            $this->getId()->sameOptionAs($attributeOption->getId());
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

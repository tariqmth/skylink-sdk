<?php

namespace RetailExpress\SkyLink\Sdk\Customers\PriceGroups;

use BadMethodCallException;
use Sabre\Xml\XmlDeserializable;
use ValueObjects\StringLiteral\StringLiteral;
use ValueObjects\Util\Util;
use ValueObjects\ValueObjectInterface;

class PriceGroup implements ValueObjectInterface, XmlDeserializable
{
    use V2PriceGroupDeserializer;

    /**
     * @var PriceGroupKey
     */
    private $key;

    /**
     * @var StringLiteral
     */
    private $name;

    /**
     * Returns a Price Group taking PHP native values as arguments.
     *
     * @return ValueObjectInterface
     */
    public static function fromNative()
    {
        $args = func_get_args();

        if (count($args) < 3) {
            $message = 'You must provide at least 3 arguments: 1) type, 2) id, 3) name';
            throw new BadMethodCallException($message);
        }

        $key = PriceGroupKey::fromNative($args[0], $args[1]);
        $name = new StringLiteral($args[2]);

        return new self($key, $name);
    }

    public function __construct(PriceGroupKey $key, StringLiteral $name)
    {
        $this->key = $key;
        $this->name = $name;
    }

    public function getKey()
    {
        return clone $this->key;
    }

    public function getName()
    {
        return clone $this->name;
    }

    /**
     * Tells whether two Price Group instances are equal.
     *
     * @param  ValueObjectInterface $priceGroup
     * @return bool
     */
    public function sameValueAs(ValueObjectInterface $priceGroup)
    {
        if (false === Util::classEquals($this, $priceGroup)) {
            return false;
        }

        return $this->getKey()->sameValueAs($priceGroup->getKey()) &&
            $this->getName()->sameValueAs($priceGroup->getName());
    }

    /**
     * Returns a string representation of the Price Group.
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getId();
    }
}

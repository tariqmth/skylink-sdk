<?php

namespace RetailExpress\SkyLink\Sdk\Customers\PriceGroups;

use BadMethodCallException;
use Sabre\Xml\XmlDeserializable;
use ValueObjects\StringLiteral\StringLiteral;
use ValueObjects\Util\Util;
use ValueObjects\ValueObjectInterface;

class PriceGroupKey implements ValueObjectInterface
{
    /**
     * @var PriceGroupType
     */
    private $type;

    /**
     * @var PriceGroupId
     */
    private $id;

    /**
     * Returns a Price Group taking PHP native values as arguments.
     *
     * @return ValueObjectInterface
     */
    public static function fromNative()
    {
        $args = func_get_args();

        if (count($args) < 2) {
            $message = 'You must provide at least 2 arguments: 1) type, 2) id';
            throw new BadMethodCallException($message);
        }

        $type = PriceGroupType::get($args[0]);
        $id = new PriceGroupId($args[1]);

        return new self($type, $id);
    }

    public function __construct(PriceGroupType $type, PriceGroupId $id)
    {
        $this->type = $type;
        $this->id = $id;
    }

    /**
     * @todo Remove the full @return namespace when Magento stops bitching about the class not existing when using this class.
     *
     * @return \RetailExpress\SkyLink\Sdk\Customers\PriceGroups\PriceGroupType
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @todo see PriceGroupKey::getType()
     *
     * @return \RetailExpress\SkyLink\Sdk\Customers\PriceGroups\PriceGroupId
     */
    public function getId()
    {
        return clone $this->id;
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

        return $this->getType()->sameValueAs($priceGroup->getType()) &&
            $this->getId()->sameValueAs($priceGroup->getId());
    }

    /**
     * Returns a string representation of the Price Group.
     *
     * @return string
     */
    public function __toString()
    {
        return "{$this->getType()}: {$this->getId()}";
    }
}

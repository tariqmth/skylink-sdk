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
     * Match creating from string in the following format (example shown):
     *
     *   "type : id"
     *
     * Where there is optional spacing separating each side of the colon. For example:
     *
     *   "fixed : 1"
     *   "fixed:1"
     *   "fixed: 1"
     */
    const STRING_PATTERN = '/^([a-z]+)(?:\s+)?\:(?:\s+)?(\d+)$/';

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

    public static function fromString($string)
    {
        preg_match(self::STRING_PATTERN, $string, $matches);

        if (count($matches) !== 3) {
            throw new BadMethodCallException('You must provide a string in the format "type : id".');
        }

        return self::fromNative($matches[1], $matches[2]);
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
        return "{$this->getType()}:{$this->getId()}";
    }
}

<?php

namespace RetailExpress\SkyLink\Sdk\Catalogue\Attributes;

use BadMethodCallException;
use ValueObjects\Util\Util;
use ValueObjects\ValueObjectInterface;

class Attribute implements ValueObjectInterface
{
    private $code;

    private $options = [];

    /**
     * Returns a object taking PHP native value(s) as argument(s).
     *
     * @return ValueObjectInterface
     */
    public static function fromNative()
    {
        $args = func_get_args();

        if (count($args) < 1) {
            throw new BadMethodCallException('You must provide at least 1 argument: 1) attribute code');
        }

        $attributeCode = AttributeCode::fromNative($args[0]);

        return new self($attributeCode);
    }

    public function __construct(AttributeCode $code, array $options = null)
    {
        $this->code = $code;

        if (null !== $options) {
            $this->options = array_map(function (AttributeOption $option) {
                return $option;
            }, $options);
        }
    }

    /**
     * Compare two ValueObjectInterface and tells whether they can be considered equal.
     *
     * @param ValueObjectInterface $otherAttribute
     *
     * @return bool
     */
    public function sameValueAs(ValueObjectInterface $otherAttribute)
    {
        if (false === Util::classEquals($this, $otherAttribute)) {
            return false;
        }

        if (!$this->getCode()->sameValueAs($otherAttribute->getCode())) {
            return false;
        }

        foreach ($this->getOptions() as $thisOption) {
            foreach ($otherAttribute->getOptions() as $otherOption) {
                if (!$thisOption->sameValueAs($otherOption)) {
                    return false;
                }
            }
        }

        return true;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function getOptions()
    {
        return array_map(function (AttributeOption $option) {
            return clone $option;
        }, $this->options);
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getCode();
    }
}

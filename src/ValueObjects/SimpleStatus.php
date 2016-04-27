<?php

namespace RetailExpress\SkyLink\ValueObjects;

use ValueObjects\Exception\InvalidNativeArgumentException;
use ValueObjects\Util\Util;
use ValueObjects\ValueObjectInterface;

class SimpleStatus implements ValueObjectInterface
{
    private $enabled;

    /**
     * Returns a new Simple Status object from a PHP native.
     *
     * @return self
     */
    public static function fromNative()
    {
        $enabled = func_get_arg(0);

        return new self($enabled);
    }

    public function __construct($enabled)
    {
        $this->assertEnabledArgument($enabled);
        $this->enabled = boolval($enabled);
    }

    /**
     * Tells whether two Integer are equal by comparing their values.
     *
     * @param ValueObjectInterface $integer
     *
     * @return bool
     */
    public function sameValueAs(ValueObjectInterface $newsletterSubscription)
    {
        if (false === Util::classEquals($this, $newsletterSubscription)) {
            return false;
        }

        return $this->toNative() === $newsletterSubscription->toNative();
    }

    /**
     * Returns the value of the newsletter subscription.
     *
     * @return int
     */
    public function toNative()
    {
        return intval($this->enabled);
    }

    /**
     * Returns a string representation of the newsletter subscription.
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->toNative();
    }

    private function assertEnabledArgument($enabled)
    {
        $enabled = filter_var($enabled, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

        if (null === $enabled) {
            throw new InvalidNativeArgumentException($enabled, array('bool'));
        }
    }
}

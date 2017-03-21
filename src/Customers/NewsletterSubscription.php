<?php

namespace RetailExpress\SkyLink\Sdk\Customers;

use ValueObjects\Exception\InvalidNativeArgumentException;
use ValueObjects\Util\Util;
use ValueObjects\ValueObjectInterface;

class NewsletterSubscription implements ValueObjectInterface
{
    private $subscribed;

    /**
     * Returns a new Newsletter Subscription object from a PHP native.
     *
     * @return self
     */
    public static function fromNative()
    {
        $subscribed = func_get_arg(0);

        return new self($subscribed);
    }

    public function __construct($subscribed)
    {
        $this->assertSubscribedArgument($subscribed);
        $this->subscribed = boolval($subscribed);
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
        return $this->subscribed;
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

    private function assertSubscribedArgument($subscribed)
    {
        $subscribed = filter_var($subscribed, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

        if (null === $subscribed) {
            throw new InvalidNativeArgumentException($subscribed, array('bool'));
        }
    }
}

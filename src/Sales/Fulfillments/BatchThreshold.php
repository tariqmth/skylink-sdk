<?php

namespace RetailExpress\SkyLink\Sdk\Sales\Fulfillments;

use BadMethodCallException;
use ValueObjects\Exception\InvalidNativeArgumentException;
use ValueObjects\Util\Util;
use ValueObjects\ValueObjectInterface;
use ValueObjects\Number\Integer;

class BatchThreshold implements ValueObjectInterface
{
    /**
     * Seconds in the threshold.
     *
     * @var int
     */
    private $seconds;

    /**
     * Returns a Batch Threshold taking PHP native value(s) as argument(s).
     *
     * @return ValueObjectInterface
     */
    public static function fromNative()
    {
        $args = func_get_args();

        if (count($args) < 1) {
            throw new BadMethodCallException('You must provide 1 argument: 1) seconds');
        }

        return new self(new Integer($args[0]));
    }

    /**
     * Creates a default batch threshold.
     *
     * @return BatchThreshold
     */
    public static function createDefault()
    {
        return self::fromNative(300); // 5 Minutes
    }

    public function __construct(Integer $seconds)
    {
        $this->assertValidSeconds($seconds);

        $this->seconds = $seconds;
    }

    public function getSeconds()
    {
        return clone $this->seconds;
    }

    /**
     * Compare two Batch Thresholds and tells whether they can be considered equal.
     *
     * @param ValueObjectInterface $object
     *
     * @return bool
     */
    public function sameValueAs(ValueObjectInterface $batchThreshold)
    {
        if (false === Util::classEquals($this, $batchThreshold)) {
            return false;
        }

        return $this->getSeconds()->sameValueAs($batchThreshold->getSeconds());
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getSeconds();
    }

    private function assertValidSeconds(Integer $seconds)
    {
        if ($seconds->toNative() < 0) {
            throw new InvalidNativeArgumentException($seconds, ['int >= 0']);
        }
    }
}

<?php

namespace RetailExpress\SkyLink\Sdk\Catalogue\Products;

use DateTimeImmutable;
use ValueObjects\Number\Real;
use ValueObjects\Util\Util;
use ValueObjects\ValueObjectInterface;

class SpecialPrice implements ValueObjectInterface
{
    private $price;

    private $startDate;

    private $endDate;

    public static function fromNative()
    {
        $args = func_get_args();

        if (count($args) < 1) {
            throw new BadMethodCallException('You must provide at least 1 argument: 1) price');
        }

        if (isset($args[1])) {
            $startDate = new DateTimeImmutable("@{$args[1]}");
        } else {
            $startDate = null;
        }

        if (isset($args[2])) {
            $endDate = new DateTimeImmutable("@{$args[2]}");
        } else {
            $endDate = null;
        }

        return new self(new Real($args[0]), $startDate, $endDate);
    }

    public function __construct(Real $price, DateTimeImmutable $startDate = null, DateTimeImmutable $endDate = null)
    {
        $this->price = $price;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function getPrice()
    {
        return clone $this->price;
    }

    public function hasStartDate()
    {
        return null !== $this->startDate;
    }

    public function getStartDate()
    {
        if (false === $this->hasStartDate()) {
            return null;
        }

        return clone $this->startDate;
    }

    public function hasEndDate()
    {
        return null !== $this->endDate;
    }

    public function getEndDate()
    {
        if (false === $this->hasEndDate()) {
            return null;
        }

        return clone $this->endDate;
    }

    /**
     * Compare two Special Price instances and tells whether they can be considered equal.
     *
     * @param ValueObjectInterface $specialPrice
     *
     * @return bool
     */
    public function sameValueAs(ValueObjectInterface $specialPrice)
    {
        if (false === Util::classEquals($this, $specialPrice)) {
            return false;
        }

        return $this->getPrice()->sameValueAs($specialPrice->getPrice()) &&
            $this->getStartDate() == $specialPrice->getStartDate() &&
            $this->getEndDate() == $specialPrice->getEndDate();
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getPrice();
    }
}

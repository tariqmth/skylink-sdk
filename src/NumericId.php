<?php

namespace RetailExpress\SkyLink;

use InvalidArgumentException;

abstract class NumericId implements ValueObject
{
    private $id;

    public function __construct($id)
    {
        if (!is_numeric($id)) {
            throw new InvalidArgumentException("ID must be numeric, \"{$id}\" given.");
        }

        $this->id = (int) $id;
    }

    public function toInt()
    {
        return $this->id;
    }

    public function equals(ValueObject $other)
    {
        return $other->id === $this->id;
    }
}

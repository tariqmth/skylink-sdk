<?php

namespace RetailExpress\SkyLink\ValueObjects\Person;

use ValueObjects\Person\Name as BaseName;
use ValueObjects\StringLiteral\StringLiteral;

class Name extends BaseName
{
    /**
     * Returns a Name objects form PHP native values.
     *
     * @param string $firstName
     * @param string $lastName
     *
     * @return Name
     */
    public static function fromNative()
    {
        $args = func_get_args();

        $firstName = new StringLiteral($args[0]);
        $lastName = new StringLiteral($args[1]);

        return new self($firstName, new StringLiteral(''), $lastName);
    }
}

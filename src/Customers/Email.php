<?php

namespace RetailExpress\SkyLink\Customers;

use RetailExpress\SkyLink\ValueObject;
use InvalidArgumentException;

class Email implements ValueObject
{
    private $email;

    public function __construct($email)
    {
        $this->assertValidEmail($email);

        $this->email = $email;
    }

    public function equals(ValueObject $other)
    {
        return $other->email === $this->email;
    }

    private function assertValidEmail($email)
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            throw new InvalidArgumentException("\"{$email}\" is not a valid email addresss.");
        }
    }
}

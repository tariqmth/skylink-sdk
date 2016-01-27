<?php

namespace RetailExpress\SkyLink\Customers;

use RetailExpress\SkyLink\ValueObject;
use InvalidArgumentException;

class Website implements ValueObject
{
    private $url;

    public function __construct($url)
    {
        $this->assertValidUrl($url);

        $this->url = $url;
    }

    public function equals(ValueObject $other)
    {
        return $other->url === $this->url;
    }

    private function assertValidUrl($url)
    {
        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
            throw new InvalidArgumentException("\"{$url}\" is not a valid URL.");
        }
    }
}

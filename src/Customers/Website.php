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

        $this->url = (string) $url;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function equals(ValueObject $other)
    {
        return $other->url === $this->url;
    }

    /**
     * Validate the URL provided is valid.
     *
     * Currently this cannot be used because Retail Express does not validate this on their end.
     *
     * @link https://www.dropbox.com/s/d86cniqgaypjyqw/Screenshot%202016-02-22%2011.33.19.png?dl=0
     *
     * @param string $url
     *
     * @throws InvalidArgumentException
     */
    private function assertValidUrl($url)
    {
        return;

        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
            throw new InvalidArgumentException("\"{$url}\" is not a valid URL.");
        }
    }
}

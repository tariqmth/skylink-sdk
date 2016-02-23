<?php

namespace RetailExpress\SkyLink;

use RetailExpress\SkyLink\ValueObject;

class Company implements ValueObject
{
    private $name;

    private $abn;

    private $website;

    public function __construct($name, Abn $abn = null, Website $website = null)
    {
        $this->name = trim((string) $name);
        $this->abn = $abn;
        $this->website = $website;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getAbn()
    {
        return $this->abn;
    }

    public function getWebsite()
    {
        return $this->website;
    }

    public function equals(ValueObject $other)
    {
        return $other->name === $this->name &&
            $other->abn === $this->abn &&
            $other->website === $this->website;
    }
}

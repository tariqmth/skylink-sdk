<?php

namespace RetailExpress\SkyLink;

interface ValueObject
{
    public function equals(ValueObject $other);
}

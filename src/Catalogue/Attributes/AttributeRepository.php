<?php

namespace RetailExpress\SkyLink\Sdk\Catalogue\Attributes;

use RetailExpress\SkyLink\Sdk\ValueObjects\SalesChannelId;

interface AttributeRepository
{
    public function find(AttributeCode $code);
}

<?php

namespace RetailExpress\SkyLink\Catalogue\Attributes;

use RetailExpress\SkyLink\ValueObjects\SalesChannelId;

interface AttributeRepository
{
    public function find(AttributeCode $code, SalesChannelId $salesChannelId);
}

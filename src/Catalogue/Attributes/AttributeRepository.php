<?php

namespace RetailExpress\SkyLink\Catalogue\Attributes;

use RetailExpress\SkyLink\ValueObjects\SalesChannelId;

interface AttributeRepository
{
    public function get(AttributeCode $code, SalesChannelId $salesChannelId);
}

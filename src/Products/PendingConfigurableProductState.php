<?php

namespace RetailExpress\SkyLink\Products;

use RetailExpress\SkyLink\ValueObjects\SalesChannelId;
use ValueObjects\Enum\Enum;

class PendingConfigurableProductState extends Enum
{
    const _PARENT = 'parent';
    const CHILD = 'child';
    const NONE = 'none';
}

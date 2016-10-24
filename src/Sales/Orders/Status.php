<?php

namespace RetailExpress\SkyLink\Sdk\Sales\Orders;

use ValueObjects\Enum\Enum;

class Status extends Enum
{
    use V2StatusConverter;

    const PENDING = 'pending';
    const PROCESSING = 'processing';
    const COMPLETE = 'complete';
}

<?php

namespace RetailExpress\SkyLink\Sdk\Sales\Orders;

use ValueObjects\Enum\Enum;

class Status extends Enum
{
    use V2StatusConverter;

    const PENDING = 'pending';
    const PENDING_PAYMENT = 'pending_payment';
    const PROCESSING = 'processing';
    const COMPLETE = 'complete';
    const CANCELLED = 'canceled';
    const CLOSED = 'closed';
}

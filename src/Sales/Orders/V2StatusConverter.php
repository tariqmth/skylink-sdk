<?php

namespace RetailExpress\SkyLink\Sales\Orders;

trait V2StatusConverter
{
    private static $mappings = [
        'pending' => 'Quote',
        'processing' => 'Incomplete',
        'complete' => 'Processed',
    ];

    /**
     * Convert the logical order status into the V2 API terminology.
     *
     * @return string
     */
    public function toV2Status()
    {
        return self::$mappings[$this->getValue()];
    }
}

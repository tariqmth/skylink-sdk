<?php

namespace RetailExpress\SkyLink\Sdk\Sales\Orders;

use UnexpectedValueException;

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

    public static function fromV2Status($v2Status)
    {
        $key = array_search($v2Status, self::$mappings);

        if (false == $key) {
            throw new UnexpectedValueException("Invalid V2 Status {$v2Status}.");
        }

        return self::get($key);
    }
}

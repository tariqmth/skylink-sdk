<?php

namespace RetailExpress\SkyLink\Vouchers;

use BadMethodCallException;
use ValueObjects\Number\Real;

class Voucher
{
    private $code;

    private $total;

    public static function fromNative()
    {
        $args = func_get_args();

        if (count($args) < 2) {
            throw new BadMethodCallException('You must provide at least 2 arguments: 1) code, 2) total');
        }

        return new self(new VoucherCode($args[0]), new Real($args[1]));
    }

    public function __construct(VoucherCode $code, Real $total)
    {
        $this->code = $code;
        $this->total = $total;
    }

    public function getCode()
    {
        return clone $this->code;
    }

    public function getTotal()
    {
        return clone $this->total;
    }
}

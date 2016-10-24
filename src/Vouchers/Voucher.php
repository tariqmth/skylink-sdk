<?php

namespace RetailExpress\SkyLink\Sdk\Vouchers;

use BadMethodCallException;
use ValueObjects\Number\Real;

class Voucher
{
    private $code;

    private $balance;

    public static function fromNative()
    {
        $args = func_get_args();

        if (count($args) < 2) {
            throw new BadMethodCallException('You must provide at least 2 arguments: 1) code, 2) balance');
        }

        return new self(new VoucherCode($args[0]), new Real($args[1]));
    }

    public function __construct(VoucherCode $code, Real $balance)
    {
        $this->code = $code;
        $this->balance = $balance;
    }

    public function getCode()
    {
        return clone $this->code;
    }

    public function getBalance()
    {
        return clone $this->balance;
    }
}

<?php

namespace RetailExpress\SkyLink\Vouchers;

interface VoucherRepository
{
    public function find(VoucherCode $voucherCode);
}

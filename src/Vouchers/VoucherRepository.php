<?php

namespace RetailExpress\SkyLink\Sdk\Vouchers;

interface VoucherRepository
{
    public function find(VoucherCode $voucherCode);
}

<?php

namespace RetailExpress\SkyLink\Vouchers;

use RetailExpress\SkyLink\Apis\V2 as V2Api;

use ValueObjects\Number\Real;

class V2VoucherRepository implements VoucherRepository
{
    private $api;

    public function __construct(V2Api $api)
    {
        $this->api = $api;
    }

    public function find(VoucherCode $voucherCode)
    {
        $rawResponse = $this->api->call('VoucherGetBalance', [
            'VoucherCode' => $voucherCode->toNative(),
        ]);

        $xmlService = $this->api->getXmlService();
        $parsedResponse = $xmlService->parse($rawResponse);

        // The value is held directly in a root "Amount" node
        $balance = (float) array_get($parsedResponse, '0.value');

        if ($balance === 0.0) {
            return;
        }

        return new Voucher($voucherCode, new Real($balance));
    }
}

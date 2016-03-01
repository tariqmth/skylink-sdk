<?php

namespace RetailExpress\SkyLink\Products;

use RetailExpress\SkyLink\Apis\V2 as V2Api;
use RetailExpress\SkyLink\ValueObjects\SalesChannelId;

class V2AttributeRepository implements AttributeRepository
{
    private $api;

    public function __construct(V2Api $api)
    {
        $this->api = $api;
    }

    public function allByCode(AttributeCode $code, SalesChannelId $salesChannelId)
    {
        $rawResponse = $this->api->call('ProductsGetBulkDetailsByChannel', [
            'ChannelId' => $salesChannelId->toNative(),
            'LastUpdated' => date('Y-m-d\TH:i:s.000\Z')
        ]);

        dd($rawResponse);
    }
}

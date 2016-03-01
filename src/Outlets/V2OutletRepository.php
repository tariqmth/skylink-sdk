<?php

namespace RetailExpress\SkyLink\Outlets;

use RetailExpress\SkyLink\Abn;
use RetailExpress\SkyLink\Apis\V2 as V2Api;
use RetailExpress\SkyLink\Company;
use RetailExpress\SkyLink\ValueObjects\SalesChannelId;

class V2OutletRepository implements OutletRepository
{
    private $api;

    public function __construct(V2Api $api)
    {
        $this->api = $api;
    }

    public function all(SalesChannelId $salesChannelId)
    {
        $rawResponse = $this->api->call('OutletsGetByChannel', [
            'ChannelId' => $salesChannelId->toNative(),
        ]);

        $xmlService = $this->api->getXmlService();
        $xmlService->elementMap = [
            '{}Outlet' => Outlet::class,
        ];
        $parsedResponse = $xmlService->parse($rawResponse);

        $outlets = [];

        foreach (array_get($parsedResponse, '0.value') as $outletResponse) {
            $outlets[] = $outletResponse['value'];
        }

        return $outlets;
    }
}

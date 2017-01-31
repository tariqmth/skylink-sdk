<?php

namespace RetailExpress\SkyLink\Sdk\Outlets;

use RetailExpress\SkyLink\Sdk\Apis\V2 as V2Api;
use RetailExpress\SkyLink\Sdk\ValueObjects\SalesChannelId;

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

    public function find(OutletId $outletId, SalesChannelId $salesChannelId)
    {
        return array_first($this->all($salesChannelId), function ($key, Outlet $outlet) use ($outletId) {
            return $outlet->getId()->sameValueAs($outletId);
        });
    }
}

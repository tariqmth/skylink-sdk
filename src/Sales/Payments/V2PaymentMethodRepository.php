<?php

namespace RetailExpress\SkyLink\Sdk\Sales\Payments;

use RetailExpress\SkyLink\Sdk\Apis\V2 as V2Api;
use RetailExpress\SkyLink\Sdk\ValueObjects\SalesChannelId;
use ValueObjects\Number\Integer;

class V2PaymentMethodRepository implements PaymentMethodRepository
{
    private $api;

    private $cacheTime;


    public function __construct(V2Api $api, Integer $cacheTime)
    {
        $this->api = $api;
        $this->cacheTime = $cacheTime;
    }

    public function all(SalesChannelId $salesChannelId)
    {
        $rawResponse = $this->api->cachedCall($this->cacheTime, 'ProductsGetBulkDetailsByChannel', [
            'ChannelId' => $salesChannelId->toNative(),
            'LastUpdated' => date('Y-m-d\TH:i:s.000'),
        ], ['LastUpdated']);

        $xmlService = $this->api->getXmlService();
        $xmlService->elementMap = [
            '{}PaymentMethod' => PaymentMethod::class,
        ];

        $parsedResponse = $xmlService->parse($rawResponse);
        $flattenedParsedResponse = array_flatten($parsedResponse);

        $paymentMethods = array_filter($flattenedParsedResponse, function ($payload) {
            return $payload instanceof PaymentMethod;
        });

        return array_values($paymentMethods);
    }
}

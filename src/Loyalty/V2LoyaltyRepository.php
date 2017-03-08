<?php

namespace RetailExpress\SkyLink\Sdk\Loyalty;

use RetailExpress\SkyLink\Sdk\Apis\V2 as V2Api;
use RetailExpress\SkyLink\Sdk\Customers\CustomerId;
use RetailExpress\SkyLink\Sdk\Customers\CustomerNotFoundException;

class V2LoyaltyRepository implements LoyaltyRepository
{
    private $api;

    public function __construct(V2Api $api)
    {
        $this->api = $api;
    }

    public function find(CustomerId $customerId)
    {
        $rawResponse = $this->api->call('GetCustomers', [
            'CustomerIds' => [$customerId->toNative()],
        ]);

        $xmlService = $this->api->getXmlService();
        $xmlService->elementMap = [
            '{}Customer' => Loyalty::class,
        ];
        $parsedResponse = $xmlService->parse($rawResponse);
        $flattenedParsedResponse = array_flatten($parsedResponse);

        return array_get($parsedResponse, '0.value.0.value', function () use ($customerId) {
            throw CustomerNotFoundException::withCustomerId($customerId);
        });
    }
}

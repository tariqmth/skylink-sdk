<?php

namespace RetailExpress\SkyLink\Sdk\Customers\PriceGroups;

use RetailExpress\SkyLink\Sdk\Apis\V2 as V2Api;

class V2PriceGroupRepository implements PriceGroupRepository
{
    private $api;

    public function __construct(V2Api $api)
    {
        $this->api = $api;
    }

    /**
     * {@inheritdoc}
     */
    public function all()
    {
        $rawResponse = $this->api->call('EDSGetAllCustomerPriceGroups', [
***REMOVED***
        ]);

        $xmlService = $this->api->getXmlService();
        $xmlService->elementMap = [
            '{}PriceGroup' => PriceGroup::class,
        ];
        $parsedResponse = $xmlService->parse($rawResponse);
        $flattenedParsedResponse = array_flatten($parsedResponse);

        $priceGroups = array_filter($flattenedParsedResponse, function ($payload) {
            return $payload instanceof PriceGroup;
        });

        return array_values($priceGroups);
    }

    public function find(PriceGroupKey $priceGroupKey)
    {
        return array_first($this->all(), function ($key, PriceGroup $priceGroup) use ($priceGroupKey) {
            return $priceGroup->getKey()->sameValueAs($priceGroupKey);
        });
    }
}

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

    public function allValuesByCode(AttributeCode $attributeCode, SalesChannelId $salesChannelId)
    {
        $rawResponse = $this->api->call('ProductsGetBulkDetailsByChannel', [
            'ChannelId' => $salesChannelId->toNative(),
            'LastUpdated' => date('Y-m-d\TH:i:s.000\Z')
        ]);

        $xmlService = $this->api->getXmlService();
        $xmlService->elementMap = [
            '{}Brand' => AttributeValue::class,
            '{}Colour' => AttributeValue::class,
            '{}Season' => AttributeValue::class,
            '{}Size' => AttributeValue::class,
            '{}ProductType' => AttributeValue::class,
        ];
        $parsedResponse = $xmlService->parse($rawResponse);

        $flattenedParsedResponse = array_flatten($parsedResponse);

        $values = array_filter($flattenedParsedResponse, function ($payload) use ($attributeCode) {
            return $payload instanceof AttributeValue && $payload->getCode()->sameValueAs($attributeCode);
        });

        return array_values($values);
    }
}

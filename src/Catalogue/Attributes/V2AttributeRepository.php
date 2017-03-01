<?php

namespace RetailExpress\SkyLink\Sdk\Catalogue\Attributes;

use RetailExpress\SkyLink\Sdk\Apis\V2 as V2Api;
use RetailExpress\SkyLink\Sdk\ValueObjects\SalesChannelId;

class V2AttributeRepository implements AttributeRepository
{
    private $api;

    public function __construct(V2Api $api)
    {
        $this->api = $api;
    }

    public function find(AttributeCode $attributeCode, SalesChannelId $salesChannelId)
    {
        $rawResponse = $this->api->call('GetAllProductAttributes');

        $xmlService = $this->api->getXmlService();

        // To speed up, we'll just map the relavent attribute
        $xmlService->elementMap = [
            "{}{$attributeCode->getV2XmlAttribute()}" => $attributeCode->isPredefined() ? V2PredefinedAttributeOptionDeserializer::class : V2AdhocAttributeOptionDeserializer::class,
        ];

        $parsedResponse = $xmlService->parse($rawResponse);

        // Grab the attribute options from the parsed response
        $attributeOptions = [];
        array_walk($parsedResponse, function ($value) use ($attributeCode, &$attributeOptions) {
            if ($value['name'] !== "{}{$attributeCode->getV2XmlAttributeGroup()}") {
                return;
            }

            $attributeOptions = array_map(function (array $payload) {
                return $payload['value'];
            }, $value['value']);
        });

        return new Attribute($attributeCode, $attributeOptions);
    }
}

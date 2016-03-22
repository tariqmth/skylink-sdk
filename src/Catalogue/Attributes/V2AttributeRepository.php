<?php

namespace RetailExpress\SkyLink\Catalogue\Attributes;

use RetailExpress\SkyLink\Apis\V2 as V2Api;
use RetailExpress\SkyLink\ValueObjects\SalesChannelId;

/**
 * @todo Rename to V2AttributeOptionRepository
 */
class V2AttributeRepository implements AttributeRepository
{
    private $api;

    public function __construct(V2Api $api)
    {
        $this->api = $api;
    }

    public function get(AttributeCode $attributeCode, SalesChannelId $salesChannelId)
    {
        // We do not require all products if the attribute code is predefined
        $LastUpdated = $attributeCode->isPredefined() ? date('Y-m-d\TH:i:s.000') : '2000-01-01T00:00:00.000';

        $rawResponse = $this->api->call('ProductsGetBulkDetailsByChannel', [
            'ChannelId' => $salesChannelId->toNative(),
            'LastUpdated' => $LastUpdated,
        ]);

        $xmlService = $this->api->getXmlService();
        $xmlService->elementMap = [
            '{}Brand' => V2PredefinedAttributeOptionDeserializer::class,
            '{}Colour' => V2PredefinedAttributeOptionDeserializer::class,
            '{}Season' => V2PredefinedAttributeOptionDeserializer::class,
            '{}Size' => V2PredefinedAttributeOptionDeserializer::class,
            '{}ProductType' => V2PredefinedAttributeOptionDeserializer::class,
            '{}Product' => V2AdhocAttributeOptionDeserializer::class,
        ];

        $parsedResponse = $xmlService->parse($rawResponse);

        $flattenedParsedResponse = array_flatten($parsedResponse);

        $uniqueValues = [];
        $values = array_filter($flattenedParsedResponse, function ($payload) use ($attributeCode, &$uniqueValues) {

            // Check the attribute option is applicable
            if (!$payload instanceof AttributeOption || !$payload->getAttribute()->getCode()->sameValueAs($attributeCode)) {
                return false;
            }

            // Check we haven't already added the attribute (in the case of custom product atttributes)
            if (in_array((string) $payload->getId(), $uniqueValues)) {
                return false;
            }

            $uniqueValues[] = (string) $payload->getId();

            return true;
        });

        return array_values($values);
    }
}

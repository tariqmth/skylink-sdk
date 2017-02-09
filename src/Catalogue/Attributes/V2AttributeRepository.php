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
        // We do not require all products if the attribute code is predefined
        $lastUpdated = date(V2_API_DATE_FORMAT, $attributeCode->isPredefined() ? time() : 0);

        $rawResponse = $this->api->call('GetAllProductAttributes');

        $xmlService = $this->api->getXmlService();
        $xmlService->elementMap = [
            '{}Brand' => V2PredefinedAttributeOptionDeserializer::class,
            '{}Colour' => V2PredefinedAttributeOptionDeserializer::class,
            '{}Season' => V2PredefinedAttributeOptionDeserializer::class,
            '{}Size' => V2PredefinedAttributeOptionDeserializer::class,
            '{}ProductType' => V2PredefinedAttributeOptionDeserializer::class,
            '{}Custom1' => V2AdhocAttributeOptionDeserializer::class,
            '{}Custom2' => V2AdhocAttributeOptionDeserializer::class,
            '{}Custom3' => V2AdhocAttributeOptionDeserializer::class,
        ];

        $parsedResponse = $xmlService->parse($rawResponse);

        $flattenedParsedResponse = array_flatten($parsedResponse);

        $uniqueOptions = [];
        $options = array_filter($flattenedParsedResponse, function ($payload) use ($attributeCode, &$uniqueOptions) {

            // Check the attribute option is applicable
            if (!$payload instanceof AttributeOption) {
                return false;
            }

            if (!$payload->getAttribute()->getCode()->sameValueAs($attributeCode)) {
                return false;
            }

            // Check we haven't already added the attribute (in the case of custom product atttributes)
            if (in_array((string) $payload->getId(), $uniqueOptions)) {
                return false;
            }

            $uniqueOptions[] = (string) $payload->getId();

            return true;
        });

        $attribute = new Attribute($attributeCode);

        foreach ($options as $option) {
            $attribute = $attribute->withOption($option);
        }

        return $attribute;
    }
}

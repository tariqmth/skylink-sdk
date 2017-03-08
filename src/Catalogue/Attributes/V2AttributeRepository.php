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

    /**
     * @todo refactor this, it feels a little messy (particularly finding the attribute options).
     */
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

            // Check for the existence of options
            if (null === $value['value']) {
                return;
            }

            $attributeOptions = array_filter(array_map(function (array $payload) {

                // Becuase the custom attributes are all combined, we'll check that we're dealing with an actual
                // attribute that was transfomed and not one that wasn't.
                $option = $payload['value'];

                if (!$option instanceof AttributeOption) {
                    return false;
                }

                return $option;
            }, $value['value']));
        });

        return new Attribute($attributeCode, $attributeOptions);
    }
}

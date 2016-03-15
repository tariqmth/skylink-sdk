<?php

namespace RetailExpress\SkyLink\Products;

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
        $rawResponse = $this->api->call('ProductsGetBulkDetailsByChannel', [
            'ChannelId' => $salesChannelId->toNative(),
            'LastUpdated' => '2000-01-01T00:00:00.000',
        ]);

        $xmlService = $this->api->getXmlService();
        $xmlService->elementMap = [
            '{}Brand' => AttributeOption::class,
            '{}Colour' => AttributeOption::class,
            '{}Season' => AttributeOption::class,
            '{}Size' => AttributeOption::class,
            '{}ProductType' => AttributeOption::class,
            '{}Product' => AttributeOption::class,
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

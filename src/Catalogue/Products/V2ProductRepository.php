<?php

namespace RetailExpress\SkyLink\Catalogue\Products;

use RetailExpress\SkyLink\Apis\V2 as V2Api;
use RetailExpress\SkyLink\ValueObjects\SalesChannelId;

class V2ProductRepository implements ProductRepository
{
    private $matrixPolicyMapper;

    private $api;

    public function __construct(matrixPolicyMapper $matrixPolicyMapper, V2Api $api)
    {
        $this->matrixPolicyMapper = $matrixPolicyMapper;
        $this->api = $api;
    }

    public function find(
        ProductId $productId,
        SalesChannelId $salesChannelId
    ) {
        $rawResponse = $this->api->call('ProductGetDetailsStockPricingByChannel', [
            'ProductId' => $productId->toNative(),
            'CustomerId' => 0,
            'PriceGroupId' => 0,
            'ChannelId' => $salesChannelId->toNative(),
        ]);

        $xmlService = $this->api->getXmlService();
        $xmlService->elementMap = [
            '{}Product' => Product::class,
        ];
        $parsedResponse = $xmlService->parse($rawResponse);
        $flattenedParsedResponse = array_flatten($parsedResponse);

        $products = array_filter($flattenedParsedResponse, function ($payload) {
            return $payload instanceof Product;
        });

        if (count($products) === 0) {
            return;
        }

        // If there is more than one product, we're dealing with a product matrix
        if (count($products) > 1) {
            dd($products);
        } elseif (count($products) === 1) {
            return current($products);
        }
    }

    /**
     * @todo Dependency inject this!
     */
    private function getPendingProductConverter()
    {
        return new PendingProductConverter();
    }
}

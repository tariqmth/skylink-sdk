<?php

namespace RetailExpress\SkyLink\Catalogue\Products;

use RetailExpress\SkyLink\Apis\V2 as V2Api;
use RetailExpress\SkyLink\ValueObjects\SalesChannelId;

class V2ProductRepository implements ProductRepository
{
    private $matrixPolicyMapper;

    private $api;

    public function __construct(MatrixPolicyMapper $matrixPolicyMapper, V2Api $api)
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
            '{}Product' => SimpleProduct::class,
        ];
        $parsedResponse = $xmlService->parse($rawResponse);
        $flattenedParsedResponse = array_flatten($parsedResponse);

        $products = array_filter($flattenedParsedResponse, function ($payload) {
            return $payload instanceof SimpleProduct;
        });

        // If there is more than one product, we're dealing with a product matrix
        if (count($products) > 1) {
            return $this->buildProductMatrix($products);
        } elseif (count($products) === 1) {
            return current($products);
        }
    }

    private function buildProductMatrix(array $products)
    {
        $firstProduct = current($products);

        $matrixPolicy = $this->matrixPolicyMapper->getPolicyForProductType($firstProduct->getProductType());

        return new Matrix($matrixPolicy, $products);
    }
}

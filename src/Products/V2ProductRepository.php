<?php

namespace RetailExpress\SkyLink\Products;

use RetailExpress\SkyLink\Apis\V2 as V2Api;
use RetailExpress\SkyLink\ValueObjects\SalesChannelId;
use Sabre\Xml\Reader as XmlReader;

class V2ProductRepository implements ProductRepository
{
    private $api;

    public function __construct(V2Api $api)
    {
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
            '{}Product' => V2ProductDeserializer::class,
        ];
        $parsedResponse = $xmlService->parse($rawResponse);
        $flattenedParsedResponse = array_flatten($parsedResponse);

        $pendingProducts = array_filter($flattenedParsedResponse, function ($payload) {
            return $payload instanceof PendingProduct;
        });

        $pendingProducts = array_values($pendingProducts);

        if (count($pendingProducts) === 0) {
            return;
        }

        return $this->getPendingProductConverter()->convert($pendingProducts);
    }

    /**
     * @todo Dependency inject this!
     */
    private function getPendingProductConverter()
    {
        return new PendingProductConverter();
    }
}

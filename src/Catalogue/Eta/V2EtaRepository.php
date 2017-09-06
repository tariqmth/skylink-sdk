<?php

namespace RetailExpress\SkyLink\Sdk\Catalogue\Eta;

use RetailExpress\SkyLink\Sdk\Apis\V2\Api as V2Api;
use RetailExpress\SkyLink\Sdk\Catalogue\Products\ProductId;
use RetailExpress\SkyLink\Sdk\ValueObjects\SalesChannelId;

class V2EtaRepository implements EtaRepository
{
    private $api;

    public function __construct(V2Api $api)
    {
        $this->api = $api;
    }

    /**
     * {@inheritdoc}
     */
    public function find(ProductId $productId, EtaQty $qty, SalesChannelId $salesChannelId)
    {
        $rawResponse = $this->api->call('GetProductEtaDateByChannel', [
            'ChannelId' => $salesChannelId->toNative(),
            'RequestXML' => <<<XML
<Products>
    <Product>
        <ProductID>{$productId}</ProductID>
        <QtyOrdered>{$qty}</QtyOrdered>
    </Product>
</Products>
XML
            ,
        ]);

        $xmlService = $this->api->getXmlService();
        $xmlService->elementMap = [
            'Product' => Eta::class,
        ];
        $parsedResponse = $xmlService->parse($rawResponse);
        $flattenedParsedResponse = array_flatten($parsedResponse);

        return array_first($flattenedParsedResponse, function ($key, $payload) {
            return $payload instanceof Eta;
        });
    }
}

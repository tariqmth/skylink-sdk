<?php

namespace RetailExpress\SkyLink\Products;

use RetailExpress\SkyLink\Apis\V2 as V2Api;
use RetailExpress\SkyLink\Customers\CustomerGroupId;
use RetailExpress\SkyLink\SalesChannelId;
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
        SalesChannelId $salesChannelId,
        CustomerGroupId $customerGroupId = null
    )
    {
        // Grab a response from the Retail Express API
        $rawResponse = $this->api->soapClient->ProductGetDetailsStockPricingByChannel([
            'ProductId' => $productId->toInt(),
            'CustomerId' => $customerGroupId !== null ? $customerGroupId->toInt() : 0,
            'PriceGroupId' => 0,
            'ChannelId' => $salesChannelId->toInt(),
        ]);

        // Boot up the XML reader and
        $xmlReader = new XmlReader();
        $xmlReader->elementMap = [
            '{}Product' => Product::class,
        ];
        $xmlReader->xml($rawResponse->ProductGetDetailsStockPricingByChannelResult->any);
        $parsedResponse = $xmlReader->parse();

        // There is a whole layer of junk that surrounds the actual
        // product, so we'll make use of this handy function's
        // dot-notation to traverse to the actual
        // element that we require.
        return array_get($parsedResponse, 'value.1.value');
    }
}

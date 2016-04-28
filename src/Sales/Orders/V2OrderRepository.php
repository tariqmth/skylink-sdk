<?php

namespace RetailExpress\SkyLink\Sales\Orders;

use RetailExpress\SkyLink\ValueObjects\SalesChannelId;
use RetailExpress\SkyLink\Apis\V2 as V2Api;

class V2OrderRepository implements OrderRepository
{
    private $api;

    public function __construct(V2Api $api)
    {
        $this->api = $api;
    }

    public function add(SalesChannelId $salesChannelId, Order $order)
    {
        $xmlService = $this->api->getXmlService();
        $xml = $xmlService->write('Orders', [
            'Order' => $order,
        ]);

        /**
         * @todo Is this still needed?
         */
        $cdataWrappedXml = $this->wrapXmlInCDataTags($xml);

        (new \Illuminate\Support\Debug\Dumper)->dump($xml);

        $response = $this->api->call('OrderCreateByChannel', [
            'ChannelId' => $salesChannelId->toNative(),
            'OrderXML' => $xml,
        ]);

        dd($response);

        dd($this->wrapXmlInCDataTags($xml));
    }

    /**
     * The Retail Express V2 API never ceases to amaze in it's inconsistencies.
     * Orders will not be created unless part of the SOAP payload is wrapped
     * (in it's entirety) in CData tags, so that's what we'll do here!
     *
     * @param  string $xml
     * @return string
     */
    private function wrapXmlInCDataTags($xml)
    {
        return <<<XML
<![CDATA[
{$xml}
]]>
XML;
    }
}

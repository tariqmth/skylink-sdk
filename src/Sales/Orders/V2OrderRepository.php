<?php

namespace RetailExpress\SkyLink\Sdk\Sales\Orders;

use BadMethodCallException;
use RetailExpress\SkyLink\Sdk\Apis\V2 as V2Api;
use RetailExpress\SkyLink\Sdk\Customers\CustomerId;
use RetailExpress\SkyLink\Sdk\ValueObjects\SalesChannelId;

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

        /*
         * @todo Is this still needed?
         */
        $cdataWrappedXml = $this->wrapXmlInCDataTags($xml);

        $rawResponse = $this->api->call('OrderCreateByChannel', [
            'ChannelId' => $salesChannelId->toNative(),
            'OrderXML' => $xml,
        ]);

        $xmlService = $this->api->getXmlService();
        $xmlService->elementMap = [
            '{}Customer' => 'Sabre\Xml\Deserializer\keyValue',
            '{}Order' => 'Sabre\Xml\Deserializer\keyValue',
        ];

        $parsedResponse = $xmlService->parse($rawResponse);

        // A successful response contains 4 nodes, Customer, Order, OrderItems and OrderPayments
        if (null === $order->getCustomerId()) {
            $customerParsedResponse = array_get($parsedResponse, '0.value.0.value');
            $order->setCustomerId(new CustomerId($customerParsedResponse['{}CustomerId']));
        }

        $orderParsedResponse = array_get($parsedResponse, '0.value.1.value');
        $order->setId(new OrderId($orderParsedResponse['{}OrderId']));
    }

    public function get(OrderId $orderId)
    {
        throw new BadMethodCallException(sprintf(
            '%s::get(%s) is not currently supported by the V2 API, please use retail-express/skylink-sdk-order-shim',
            OrderRepository::class,
            $orderId
        ));
    }

    /**
     * The Retail Express V2 API never ceases to amaze in it's inconsistencies.
     * Orders will not be created unless part of the SOAP payload is wrapped
     * (in it's entirety) in CData tags, so that's what we'll do here!
     *
     * @param string $xml
     *
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

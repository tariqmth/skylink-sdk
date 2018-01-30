<?php

namespace RetailExpress\SkyLink\Sdk\Sales\Orders;

use RetailExpress\SkyLink\Sdk\Apis\V2\Api as V2Api;
use RetailExpress\SkyLink\Sdk\Customers\CustomerId;
use RetailExpress\SkyLink\Sdk\Sales\Fulfillments\Batch;
use RetailExpress\SkyLink\Sdk\Sales\Fulfillments\Fulfillment;
use RetailExpress\SkyLink\Sdk\Sales\Fulfillments\FulfillmentGrouper;
use RetailExpress\SkyLink\Sdk\Sales\Fulfillments\V2FulfillmentDeserializer;
use RetailExpress\SkyLink\Sdk\Sales\Payments\Payment;
use RetailExpress\SkyLink\Sdk\Sales\Payments\V2PaymentDeserializer;
use RetailExpress\SkyLink\Sdk\ValueObjects\SalesChannelId;

class V2OrderRepository implements OrderRepository
{
    private $api;

    private $fulfillmentGrouper;

    public function __construct(
        V2Api $api,
        FulfillmentGrouper $fulfillmentGrouper = null
    ) {
        $this->api = $api;

        if (null === $fulfillmentGrouper) {
            $fulfillmentGrouper = $this->createDefaultFulfillmentGrouper();
        }

        $this->fulfillmentGrouper = $fulfillmentGrouper;
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
        $rawResponse = $this->api->call('GetOrder', [
            'OrderId' => $orderId->toNative(),
        ]);

        // Currently, we don't know how to handle nested nodes that
        // have the same value as their parent, e.g:

        // <Payment>
        //   <MethodId>1</MethodId>
        //   <Payment>10.00</Payment>

        // So we'll just switch out the XML for now.
        $this->switchOutPaymentNodes($rawResponse);

        $xmlService = $this->api->getXmlService();
        $xmlService->elementMap = [
            '{}Order' => V2OrderDeserializer::class,
            '{}OrderItem' => V2OrderItemDeserializer::class,
            '{}Payment' => V2PaymentDeserializer::class,
            '{}Fulfillment' => V2FulfillmentDeserializer::class,
        ];

        $parsedResponse = $xmlService->parse($rawResponse);
        $parsedResponse = array_flatten($parsedResponse);

        // Filter out the matching order
        $order = array_first($parsedResponse, function ($key, $payload) {
            return $payload instanceof Order;
        });

        if (null === $order) {
            return null;
        }

        $this->extractAndAttachOrderItems($parsedResponse, $order);
        $this->extractAndAttachPayments($parsedResponse, $order);

        $fulfillments = $this->extractFulfillments($parsedResponse);
        $groupsOfFullfillments = $this->fulfillmentGrouper->groupForBatching($fulfillments);

        $fulfillmentBatches = array_map(function (array $groupOfFulfillments) {
            return new Batch($groupOfFulfillments);
        }, $groupsOfFullfillments);

        $this->attachFulfillmentBatches($fulfillmentBatches, $order);

        return $order;
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

    private function switchOutPaymentNodes(&$xml)
    {
        $xml = preg_replace(
            '/<Payment>([\d\.-]+)<\/Payment>/',
            '<Total>$1</Total>',
            $xml
        );
    }

    private function extractAndAttachOrderItems(array $parsedResponse, Order &$order)
    {
        $orderItems = array_filter($parsedResponse, function ($payload) {
            return $payload instanceof Item;
        });

        array_walk($orderItems, function (Item $orderItem) use (&$order) {
            $order = $order->withItem($orderItem);
        });
    }

    private function extractAndAttachPayments(array $parsedResponse, Order &$order)
    {
        $payments = array_filter($parsedResponse, function ($payload) {
            return $payload instanceof Payment;
        });

        array_walk($payments, function (Payment $payment) use (&$order) {
            $order = $order->withPayment($payment);
        });
    }

    private function extractFulfillments(array $parsedResponse)
    {
        return array_values(array_filter($parsedResponse, function ($payload) {
            return $payload instanceof Fulfillment;
        }));
    }

    private function attachFulfillmentBatches(array $fulfillmentBatches, Order &$order)
    {
        array_walk($fulfillmentBatches, function (Batch $fulfillmentBatch) use (&$order) {
            $order = $order->withFulfillmentBatch($fulfillmentBatch);
        });
    }

    private function createDefaultFulfillmentGrouper()
    {
        return new FulfillmentGrouper();
    }
}

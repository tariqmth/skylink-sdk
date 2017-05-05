<?php

namespace RetailExpress\SkyLink\Sdk\Sales\Payments;

use RetailExpress\SkyLink\Sdk\Apis\V2\Api as V2Api;
use RetailExpress\SkyLink\Sdk\Apis\V2ApiException;
use RetailExpress\SkyLink\Sdk\Sales\Orders\OrderId;
use RetailExpress\SkyLink\Sdk\Sales\Orders\OrderRepository;

class V2PaymentRepository implements PaymentRepository
{
    private $api;

    private $orderRepository;

    public function __construct(
        V2Api $api,
        OrderRepository $orderRepository
    ) {
        $this->api = $api;
        $this->orderRepository = $orderRepository;
    }

    public function add(Payment $payment)
    {
        $xmlService = $this->api->getXmlService();
        $xml = $xmlService->write('OrderPayments', [
            'OrderPayment' => $payment,
        ]);

        $rawResponse = $this->api->call('OrderAddPayment', [
            'OrderPaymentXML' => $xml,
        ]);

        // Retail Express doens't actually give us the payment ID back at all,
        // so we'll need to re-query the order to retrieve it
        $paymentId = $this->getLatestPaymentId($payment->getOrderId());
        $payment->setId($paymentId);
    }

    private function getLatestPaymentId(OrderId $orderId)
    {
        $order = $this->orderRepository->get($orderId);

        return $order->getLatestPayment()->getId();
    }
}

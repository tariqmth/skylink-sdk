<?php

namespace RetailExpress\SkyLink\Sdk\Sales\Payments;

use RetailExpress\SkyLink\Sdk\Apis\V2 as V2Api;
use RetailExpress\SkyLink\Sdk\Apis\V2ApiException;

class V2PaymentRepository implements PaymentRepository
{
    private $api;

    public function __construct(V2Api $api)
    {
        $this->api = $api;
    }

    public function add(Payment $payment)
    {
        $xmlService = $this->api->getXmlService();
        $xml = $xmlService->write('OrderPayments', [
            'OrderPayment' => $payment,
        ]);

        $rawResponse = $this->api->call('OrderAddPayment', [
            'OrderPaymentXML' => $xml,
        ], function ($response) {

            // Todo, tidy this up, maybe globalise it?
            $xml = simplexml_load_string($response);
            $errors = array_map('trim', $xml->xpath('//Error'));

            if (count($errors) > 0) {
                throw new V2ApiException(implode(' ', $errors));
            }
        });
    }
}

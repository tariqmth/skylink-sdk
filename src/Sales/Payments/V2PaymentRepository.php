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
        // @todo check for existing ID on payment already and throw exception

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

        // Retail Express does not give us an ID for the payment, however we know
        // that the "made at" attribute is stored statically in Retail Express
        // and is not modified from the value we supply. Therefore, we'll
        // take a combination of that, the "method id" and the "total"
        // and that'll certainly give us a unique set of data to
        // build a hash from. We could probably just use the
        // "made at" attribute, but we'll go the extra
        // step to avoid any horrible debugging that
        // might be required and hard to do.
        $idComponents = [
            (string) $payment->getMadeAt()->format(V2_API_DATE_FORMAT),
            (string) $payment->getMethodId(),
            (string) $payment->getTotal(),
        ];

        $idAsString = md5(implode('', $idComponents));
        $payment->setId(new PaymentId($idAsString));
    }
}

<?php

namespace RetailExpress\SkyLink\Sdk\Sales\Payments;

use RetailExpress\SkyLink\Sdk\Apis\V2 as V2Api;

class V2PaymentMethodRepository implements PaymentMethodRepository
{
    private $api;

    public function __construct(V2Api $api)
    {
        $this->api = $api;
    }

    public function all()
    {
        $rawResponse = $this->api->call('GetPaymentMethods');

        $xmlService = $this->api->getXmlService();
        $xmlService->elementMap = [
            '{}PaymentMethod' => PaymentMethod::class,
        ];

        $parsedResponse = $xmlService->parse($rawResponse);
        $flattenedParsedResponse = array_flatten($parsedResponse);

        $paymentMethods = array_filter($flattenedParsedResponse, function ($payload) {
            return $payload instanceof PaymentMethod;
        });

        return array_values($paymentMethods);
    }
}

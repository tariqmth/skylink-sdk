<?php

namespace RetailExpress\SkyLink\Sdk\Customers;

use DateTimeImmutable;
use RetailExpress\SkyLink\Sdk\Apis\V2 as V2Api;
use RetailExpress\SkyLink\Sdk\Apis\V2ApiException;

class V2CustomerRepository implements CustomerRepository
{
    private $api;

    public function __construct(V2Api $api)
    {
        $this->api = $api;
    }

    public function allIds()
    {
        $rawResponse = $this->api->call('EDSGetCustomers', [
        ]);

        $xmlService = $this->api->getXmlService();
        $xmlService->elementMap = [
            '{}Customer' => CustomerId::class,
        ];
        $parsedResponse = $xmlService->parse($rawResponse);
        $flattenedParsedResponse = array_flatten($parsedResponse);

        $customerIds = array_filter($flattenedParsedResponse, function ($payload) {
            return $payload instanceof CustomerId;
        });

        return array_values($customerIds);
    }

    public function find(CustomerId $customerId)
    {
        $rawResponse = $this->api->call('EDSGetCustomers', [
            'CustomerIds' => [$customerId->toNative()],
        ]);

        $xmlService = $this->api->getXmlService();
        $xmlService->elementMap = [
            '{}Customer' => Customer::class,
        ];
        $parsedResponse = $xmlService->parse($rawResponse);

        return array_get($parsedResponse, '0.value.0.value', function () use ($customerId) {
            throw CustomerNotFoundException::withCustomerId($customerId);
        });
    }

    public function add(Customer $customer)
    {
        $xmlService = $this->api->getXmlService();
        $xml = $xmlService->write('Customers', [
            'Customer' => $customer,
        ]);

        $rawResponse = $this->api->call('CustomerCreateUpdate', [
            'CustomerXML' => $xml,
        ]);

        $xmlService = $this->api->getXmlService();
        $xmlService->elementMap = [
            '{}Customer' => 'Sabre\Xml\Deserializer\keyValue',
        ];

        $parsedResponse = $xmlService->parse($rawResponse);

        if (array_get($parsedResponse, '0.value.{}Result') === 'Fail') {
            dd($this->api->getSoapClient()->__getLastRequest());
            throw new V2ApiException("Failed to create a customer based on the given details.");
        }

        if (null === $customer->getId()) {
            $customerId = new CustomerId(array_get($parsedResponse, '0.value.{}CustomerId'));
            $customer->setId($customerId);
        }
    }
}

<?php

namespace RetailExpress\SkyLink\Sdk\Customers;

use RetailExpress\SkyLink\Sdk\Apis\V2\Api as V2Api;
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
        $rawResponse = $this->api->call('GetCustomerIds', [
            'OnlyCustomersForExport' => true,
            'OnlyCustomersWithEmails' => true,
        ]);

        $xmlService = $this->api->getXmlService();
        $xmlService->elementMap = [
            '{}Customer' => CustomerId::class,
        ];
        $parsedResponse = $xmlService->parse($rawResponse);

        $customerIds = array_filter(array_map(function (array $payload) {
            if ($payload['value'] instanceof CustomerId) {
                return $payload['value'];
            }
        }, array_get($parsedResponse, '0.value')));

        return array_values($customerIds);
    }

    public function find(CustomerId $customerId)
    {
        $rawResponse = $this->api->call('GetCustomers', [
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
            throw new V2ApiException("Failed to create a customer based on the given details.");
        }

        if (null === $customer->getId()) {
            $customerId = new CustomerId(array_get($parsedResponse, '0.value.{}CustomerId'));
            $customer->setId($customerId);
        }
    }
}

<?php

namespace RetailExpress\SkyLink\Sdk\Customers;

use DateTimeImmutable;
use RetailExpress\SkyLink\Sdk\Apis\V2 as V2Api;

class V2CustomerRepository implements CustomerRepository
{
    private $api;

    public function __construct(V2Api $api)
    {
        $this->api = $api;
    }

    public function allIds(DateTimeImmutable $updatedSince = null)
    {
        if (null === $updatedSince) {
            $updatedSince = new DateTimeImmutable('@0');
        }

        $rawResponse = $this->api->call('CustomerGetBulkDetails', [
            'LastUpdated' => $updatedSince->format(V2_API_DATE_FORMAT),
            'OnlyCustomersWithEmails' => 1,
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
        $rawResponse = $this->api->call('CustomerGetDetails', [
            'CustomerId' => $customerId->toNative(),
        ]);

        $xmlService = $this->api->getXmlService();
        $xmlService->elementMap = [
            '{}Customer' => Customer::class,
        ];
        $parsedResponse = $xmlService->parse($rawResponse);

        return array_get($parsedResponse, '0.value.1.value', function () use ($customerId) {
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

        if (null === $customer->getId()) {
            $customerId = new CustomerId(array_get($parsedResponse, '0.value.{}CustomerId'));
            $customer->setId($customerId);
        }
    }
}

<?php

namespace RetailExpress\SkyLink\Customers;

use RetailExpress\SkyLink\Apis\V2 as V2Api;
use Sabre\Xml\Service as XmlService;

class V2CustomerRepository implements CustomerRepository
{
    private $api;

    public function __construct(V2Api $api)
    {
        $this->api = $api;
    }

    public function find(CustomerId $customerId)
    {
        $rawResponse = $this->api->call('CustomerGetDetails', [
            'CustomerId' => $customerId->toInt(),
        ]);

        $xmlService = $this->getXmlService();
        $xmlService->elementMap = [
            '{}Customer' => Customer::class,
        ];
        $parsedResponse = $xmlService->parse($rawResponse);

        // Bypass all the schema definition junk
        return array_get($parsedResponse, '0.value.1.value');
    }

    public function add(Customer $customer)
    {
        $xmlService = $this->getXmlService();
        $xml = $xmlService->write('Customers', [
            'Customer' => $customer,
        ]);

        $this->api->call('CustomerCreateUpdate', [
            'CustomerXML' => $xml,
        ]);
    }

    /**
     * @todo Extract this do DI.
     */
    private function getXmlService()
    {
        return new XmlService();
    }
}

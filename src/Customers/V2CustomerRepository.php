<?php

namespace RetailExpress\SkyLink\Customers;

use RetailExpress\SkyLink\Apis\V2 as V2Api;
use Sabre\Xml\Reader as XmlReader;

class V2CustomerRepository implements CustomerRepository
{
    private $api;

    public function __construct(V2Api $api)
    {
        $this->api = $api;
    }

    public function all()
    {
    }

    public function find(CustomerId $customerId)
    {
        $rawResponse = $this->api->call('CustomerGetDetails', [
            'CustomerId' => $customerId->toInt(),
        ]);

        $xmlReader = new XmlReader();
        $xmlReader->elementMap = [
            '{}Customer' => Customer::class,
        ];
        $xmlReader->xml($rawResponse->CustomerGetDetailsResult->any);
        $parsedResponse = $xmlReader->parse()['value'];

        // Bypass all the schema definition junk
        return array_get($parsedResponse, '0.value.1.value');
    }

    public function add(Customer $customer)
    {
    }
}

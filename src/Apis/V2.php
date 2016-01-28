<?php

namespace RetailExpress\SkyLink\Apis;

use Ramsey\Uuid\Uuid;
use SoapClient;
use SoapHeader;

class V2
{
    public $soapClient;

    public function __construct(Uuid $clientId, $database, $username, $password)
    {
        $this->soapClient = $this->createSoapClient(
            $clientId,
            $this->determineSoapUrlFromDatabaseName($database),
            (string) $username,
            (string) $password
        );
    }

    public function call($method, array $arguments = [])
    {
        return $this->soapClient->__soapCall($method, [$arguments]);
    }

    private function determineSoapUrlFromDatabaseName($database)
    {
        return "https://{$database}.retailexpress.com.au/dotnet/admin/webservices/v2/webstore/service.asmx?wsdl";
    }

    private function createSoapClient(Uuid $clientId, $url, $username, $password)
    {
        $client = new SoapClient($url, [
            'soap_version' => SOAP_1_2,
            'trace' => true,
        ]);

        $header = new SoapHeader('http://retailexpress.com.au/', 'ClientHeader', [
            'ClientID' => (string) $clientId,
            'UserName' => $username,
            'Password' => $password,
        ]);

        $client->__setSoapHeaders($header);

        return $client;
    }
}

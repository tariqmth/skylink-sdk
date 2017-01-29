<?php

namespace RetailExpress\SkyLink\Sdk\Apis;

use SoapFault;

class V2ApiException extends ApiException
{
    private $soapFault;

    private $lastSoapRequest;

    public static function withSoapFaultAndRequest(SoapFault $soapFault, $lastSoapRequest, $message)
    {
        $new = new self($message);
        $new->lastSoapRequest = $lastSoapRequest;
        $new->soapFault = $soapFault;

        return $new;
    }

    public function getSoapFault()
    {
        return $this->soapFault;
    }

    public function getLastSoapRequest()
    {
        return $this->lastSoapRequest;
    }
}

<?php

namespace RetailExpress\SkyLink\Sdk\Apis\V2;

use RetailExpress\SkyLink\Sdk\Apis\ApiException as BaseApiException;
use SoapFault;

class ApiException extends BaseApiException
{
    private $soapFault;

    private $lastSoapRequest;

    public static function withSoapFaultAndRequest(SoapFault $soapFault, $lastSoapRequest, $customMessage = null)
    {
        $new = new self($customMessage ?: $soapFault->faultstring);

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

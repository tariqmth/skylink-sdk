<?php

namespace RetailExpress\SkyLink\Sdk\Apis;

use SoapFault;

class V2ApiException extends ApiException
{
    private $soapFault;

    public static function withSoapFault(SoapFault $soapFault, $message)
    {
        $new = new self($message);
        $new->soapFault = $soapFault;

        return $new;
    }

    public function getSoapFault()
    {
        return $this->soapFault;
    }
}

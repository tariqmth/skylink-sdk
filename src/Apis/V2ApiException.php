<?php

namespace RetailExpress\SkyLink\Apis;

use SoapFault;

class V2ApiException extends ApiException
{
    private $soapFault;

    public function setSoapFault(SoapFault $soapFault)
    {
        $this->soapFault = $soapFault;
    }

    public function getSoapFault()
    {
        return $this->soapFault;
    }
}

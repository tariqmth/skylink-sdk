<?php

namespace RetailExpress\SkyLink\Sdk\Sales\Payments;

use Sabre\Xml\Writer as XmlWriter;

trait V2PaymentSerializer
{
    public function xmlSerialize(XmlWriter $xmlWriter)
    {
        $payload = [];

        $payload['OrderId'] = (string) $this->getOrderId();
        $payload['MethodId'] = (string) $this->getMethodId();
        $payload['Amount'] = (string) $this->getTotal();
        $payload['DateCreated'] = $this->getMadeAt()->format(V2_API_DATE_FORMAT);

        if ($this->usesVoucherCode()) {
            $payload['VoucherCode'] = (string) $this->getVoucherCode();
        }

        $xmlWriter->write($payload);
    }
}

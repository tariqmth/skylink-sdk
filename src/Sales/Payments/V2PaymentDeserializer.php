<?php

namespace RetailExpress\SkyLink\Sdk\Sales\Payments;

use RetailExpress\SkyLink\Sdk\Sales\Orders\OrderId;
use Sabre\Xml\Deserializer as XmlDeserializer;
use Sabre\Xml\Reader;
use Sabre\Xml\XmlDeserializable;

class V2PaymentDeserializer implements XmlDeserializable
{
    public static function xmlDeserialize(Reader $xmlReader)
    {
        $payload = XmlDeserializer\keyValue($xmlReader, '');

        $payment = Payment::normalFromNative(
            $payload['OrderId'],
            strtotime($payload['DateCreated']),
            $payload['MethodId'],
            $payload['Total']
        );

        $payment->setId(new PaymentId($payload['OrderPaymentId']));

        return $payment;
    }
}

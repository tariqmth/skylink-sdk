<?php

namespace RetailExpress\SkyLink\Sdk\Sales\Fulfillments;

use Sabre\Xml\Deserializer as XmlDeserializer;
use Sabre\Xml\Reader;
use Sabre\Xml\XmlDeserializable;

class V2FulfillmentDeserializer implements XmlDeserializable
{
    public static function xmlDeserialize(Reader $xmlReader)
    {
        $payload = XmlDeserializer\keyValue($xmlReader, '');

        $fulfillment = Fulfillment::fromNative(
            $payload['OrderId'],
            $payload['OrderItemId'],
            strtotime($payload['DateFulfilled']),
            $payload['QtyFulfilled']
        );

        $idComponents = [
            (string) $fulfillment->getOrderId(),
            (string) $fulfillment->getOrderItemId(),
            (string) $fulfillment->getFulfilledAt()->format(V2_API_DATE_FORMAT),
            (string) $fulfillment->getQty(),
        ];

        $fulfillmentId = new FulfillmentId(md5(implode('', $idComponents)));
        $fulfillment->setId($fulfillmentId);

        return $fulfillment;
    }
}

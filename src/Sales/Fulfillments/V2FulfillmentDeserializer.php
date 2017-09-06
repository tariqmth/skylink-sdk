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
            from_v2_rex_date_to_timestamp($payload['DateFulfilled']),
            $payload['QtyFulfilled']
        );

        $fulfillmentId = new FulfillmentId($payload['FulfillmentID']);
        $fulfillment->setId($fulfillmentId);

        return $fulfillment;
    }
}

<?php

namespace RetailExpress\SkyLink\Sdk\Sales\Orders;

use RetailExpress\SkyLink\Sdk\Sales\Orders\OrderId;
use RetailExpress\SkyLink\Sdk\Sales\Orders\Item;
use RetailExpress\SkyLink\Sdk\Sales\Orders\ItemId;
use Sabre\Xml\Deserializer as XmlDeserializer;
use Sabre\Xml\Reader;
use Sabre\Xml\XmlDeserializable;

class V2OrderItemDeserializer implements XmlDeserializable
{
    public static function xmlDeserialize(Reader $xmlReader)
    {
        $payload = XmlDeserializer\keyValue($xmlReader, '');

        $orderItem = Item::fromNative(
            $payload['ProductId'],
            $payload['QtyOrdered'],
            $payload['QtyFulfilled'],
            $payload['UnitPrice'],
            0 // @todo get tax rate exposed when retrieving
        );
        $orderItem->setId(new ItemId($payload['OrderItemId']));
        $orderItem->setOrderId(new OrderId($payload['OrderId']));

        return $orderItem;
    }
}

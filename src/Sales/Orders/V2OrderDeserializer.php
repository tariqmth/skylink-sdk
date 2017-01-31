<?php

namespace RetailExpress\SkyLink\Sdk\Sales\Orders;

use DateTimeImmutable;
use RetailExpress\SkyLink\Sdk\Customers\BillingContact;
use RetailExpress\SkyLink\Sdk\Customers\CustomerId;
use RetailExpress\SkyLink\Sdk\Customers\ShippingContact;
use Sabre\Xml\Deserializer as XmlDeserializer;
use Sabre\Xml\Reader;
use Sabre\Xml\XmlDeserializable;
use ValueObjects\StringLiteral\StringLiteral;

class V2OrderDeserializer implements XmlDeserializable
{
    public static function xmlDeserialize(Reader $xmlReader)
    {
        $payload = XmlDeserializer\keyValue($xmlReader, '');

        $customerId = new CustomerId($payload['CustomerId']);

        $placedAt = new DateTimeImmutable($payload['DateCreated']);

        $status = Status::fromV2Status($payload['OrderStatus']);

        list($billingFirstName, $billingLastName) = self::splitName('BillName');
        $billingContact = BillingContact::fromNative(
            $billingFirstName,
            $billingLastName,
            array_get_notempty($payload, 'BillEmail', "{$payload['CustomerId']}@example.com"),
            array_get_notempty($payload, 'BillCompany', ''),
            array_get_notempty($payload, 'BillAddress', ''),
            array_get_notempty($payload, 'BillAddress2', ''),
            array_get_notempty($payload, 'BillSuburb', ''),
            array_get_notempty($payload, 'BillState', ''),
            trim(array_get_notempty($payload, 'BillPostCode', '')), // Retail Express pads this to 30 characters
            array_get_notempty($payload, 'BillCountry', ''),
            array_get_notempty($payload, 'BillPhone', ''),
            array_get_notempty($payload, 'BillFax', '')
        );

        list($shippingFirstName, $shippingLastName) = self::splitName('DelName');
        $shippingContact = ShippingContact::fromNative(
            $shippingFirstName,
            $shippingLastName,
            array_get_notempty($payload, 'DelCompany', ''),
            array_get_notempty($payload, 'DelAddress', ''),
            array_get_notempty($payload, 'DelAddress2', ''),
            array_get_notempty($payload, 'DelSuburb', ''),
            array_get_notempty($payload, 'DelState', ''),
            trim(array_get_notempty($payload, 'DelPostCode', '')), // Retail Express pads this to 30 characters
            array_get_notempty($payload, 'DelCountry', ''),
            array_get_notempty($payload, 'DelPhone', '')
        );

        $shippingCharge = ShippingCharge::fromNative(
            $payload['FreightTotal'],
            0 // @todo Ask for API to expose tax rate when retrieving orders
        );

        $order = Order::forCustomerWithId(
            $customerId,
            $placedAt,
            $status,
            $billingContact,
            $shippingContact,
            $shippingCharge
        );

        $order->setId(new OrderId($payload['OrderId']));

        if (null !== $payload['PublicComments']) {
            $order = $order->withPublicComments(
                new StringLiteral($payload['PublicComments'])
            );
        }

        if (null !== $payload['PrivateComments']) {
            $order = $order->withPublicComments(
                new StringLiteral($payload['PrivateComments'])
            );
        }

        return $order;
    }

    private static function splitName($name)
    {
        $split = explode(' ', $name, 2);

        if (count($split) < 2) {
            return [$split[0], ''];
        }

        return $split;
    }
}

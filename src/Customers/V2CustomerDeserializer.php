<?php

namespace RetailExpress\SkyLink\Customers;

use Sabre\Xml\Deserializer as XmlDeserializer;
use Sabre\Xml\Reader as XmlReader;

trait V2CustomerDeserializer
{
    public static function xmlDeserialize(XmlReader $xmlReader)
    {
        $payload = XmlDeserializer\keyValue($xmlReader, '');

        $billingContact = BillingContact::fromNative(
            $payload['BillFirstName'],
            $payload['BillLastName'],
            array_get_notempty($payload, 'BillEmail', "{$payload['CustomerId']}@example.com"),
            array_get($payload, 'BillCompany'),
            array_get($payload, 'BillAddress'),
            array_get($payload, 'BillAddress2'),
            array_get($payload, 'BillSuburb'),
            array_get($payload, 'BillState'),
            trim(array_get($payload, 'BillPostCode')), // Retail Express pads this to 30 characters
            array_get($payload, 'BillCountry')
        );

        // The shipping name comes back as a singular field, so we'll try split it out!
        $shippingFirstName = null;
        $shippingLastName = null;
        if (isset($payload['DelName'])) {
            list($shippingFirstName, $shippingLastName) = self::splitShippingName($payload['DelName'], [$payload['BillFirstName'], $payload['BillLastName']]);
        }

        $shippingContact = ShippingContact::fromNative(
            $shippingFirstName,
            $shippingLastName,
            array_get($payload, 'DelCompany'),
            array_get($payload, 'DelAddress'),
            array_get($payload, 'DelAddress2'),
            array_get($payload, 'DelSuburb'),
            array_get($payload, 'DelState'),
            trim(array_get($payload, 'DelPostCode')),
            array_get($payload, 'DelCountry')
        );

        $customer = static::existing(
            new CustomerId($payload['CustomerId']),
            $billingContact,
            $shippingContact,
            new NewsletterSubscription($payload['ReceivesNews'])
        );

        return $customer;
    }

    /**
     * Takes the given combined shipping name and attempts to see if it matches the billing name.
     * If it matches, this method simply returns the billing name. If not, it will split the
     * first and last name by the first occurance of a space.
     *
     * @param string $shippingName
     * @param array  $billingName
     *
     * @return array
     */
    private static function splitShippingName($shippingName, array $billingName = null)
    {
        if ($billingName !== null) {
            $concatenatedBillingName = implode(' ', $billingName);

            if ($concatenatedBillingName === $shippingName) {
                return $billingName;
            }
        }

        return explode(' ', $shippingName, 2);
    }
}

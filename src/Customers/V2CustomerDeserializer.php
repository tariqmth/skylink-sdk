<?php

namespace RetailExpress\SkyLink\Customers;

use Sabre\Xml\Deserializer as XmlDeserializer;
use Sabre\Xml\Reader as XmlReader;

trait V2CustomerDeserializer
{
    public static function xmlDeserialize(XmlReader $xmlReader)
    {
        $payload = XmlDeserializer\keyValue($xmlReader, '');
        dd($payload);

        if (isset($payload['BillCompany'])) {
            $billingAddress = BillingAddress::forCompany(
                new Company(
                    $payload['BillCompany'],
                    isset($payload['BillABN']) ? new Abn($payload['BillABN']) : null,
                    isset($payload['BillWebsite']) ? new Website($payload['BillWebsite']) : null
                ),
                $payload['BillFirstName'],
                $payload['BillLastName'],
                [
                    array_get($payload, 'BillAddress'),
                    array_get($payload, 'BillAddress2'),
                ],
                array_get($payload, 'BillSuburb'),
                array_get($payload, 'BillPostCode'),
                array_get($payload, 'BillState'),
                array_get($payload, 'BillCountry'),
                [
                    'phone' => array_get($payload, 'BillPhone'),
                    'mobile' => array_get($payload, 'BillMobile'),
                    'fax' => array_get($payload, 'BillFax'),
                ]
            );
        } else {
            $billingAddress = BillingAddress::forIndividual(
                $payload['BillFirstName'],
                $payload['BillLastName'],
                [
                    array_get($payload, 'BillAddress'),
                    array_get($payload, 'BillAddress2'),
                ],
                array_get($payload, 'BillSuburb'),
                array_get($payload, 'BillPostCode'),
                array_get($payload, 'BillState'),
                array_get($payload, 'BillCountry'),
                [
                    'phone' => array_get($payload, 'BillPhone'),
                    'mobile' => array_get($payload, 'BillMobile'),
                    'fax' => array_get($payload, 'BillFax'),
                ]
            );
        }

        if (isset($payload['DelName'])) {
            list($deliveryFirstName, $deliveryLastName) = self::splitDeliveryName($payload['DelName'], [$payload['BillFirstName'], $payload['BillLastName']]);
        } else {
            $deliveryFirstName = null;
            $deliveryLastName = null;
        }

        if (isset($payload['DelCompany'])) {
            $deliveryAddress = DeliveryAddress::forCompany(
                new Company($payload['DelCompany']),
                $deliveryFirstName,
                $deliveryLastName,
                [
                    array_get($payload, 'DelAddress'),
                    array_get($payload, 'DelAddress2'),
                ],
                array_get($payload, 'DelSuburb'),
                array_get($payload, 'DelPostCode'),
                array_get($payload, 'DelState'),
                array_get($payload, 'DelCountry'),
                [
                    'phone' => array_get($payload, 'DelPhone'),
                    'mobile' => array_get($payload, 'DelMobile'),
                ]
            );
        } else {
            $deliveryAddress = DeliveryAddress::forIndividual(
                $deliveryFirstName,
                $deliveryLastName,
                [
                    array_get($payload, 'DelAddress'),
                    array_get($payload, 'DelAddress2'),
                ],
                array_get($payload, 'DelSuburb'),
                array_get($payload, 'DelPostCode'),
                array_get($payload, 'DelState'),
                array_get($payload, 'DelCountry'),
                [
                    'phone' => array_get($payload, 'DelPhone'),
                    'mobile' => array_get($payload, 'DelMobile'),
                ]
            );
        }

        $customer = static::existing(
            new CustomerId($payload['CustomerId']),
            new Email(array_get($payload, 'BillEmail', "{$payload['CustomerId']}@example.com")),
            $billingAddress,
            $deliveryAddress,
            $payload['ReceivesNews']
        );

        return $customer;
    }

    private static function splitDeliveryName($deliveryName, array $billingName = null)
    {
        // If we have a billing name, we'll check if the delivery name is simply that as a concatenated string
        if ($billingName !== null) {
            $concatenatedBillingName = implode(' ', $billingName);

            // If they match, we know it's safe to just return the billing name
            if ($deliveryName === $concatenatedBillingName) {
                return $billingName;
            }
        }

        // Otherwise, we'll just use the first blank space as the placeholder for first and last name
        return explode(' ', $deliveryName, 2);
    }
}

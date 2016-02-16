<?php

namespace RetailExpress\SkyLink\Customers;

use RetailExpress\SkyLink\XmlKeySanitiser;
use Sabre\Xml\Deserializer as XmlDeserializer;
use Sabre\Xml\Reader as XmlReader;

trait V2CustomerDeserializer
{
    /**
     * @todo Implement company logic (delivery companies are just a name, billing include ABN, etc)
     */
    public static function xmlDeserialize(XmlReader $xmlReader)
    {
        $payload = XmlDeserializer\keyValue($xmlReader, '');

        if (isset($payload['BillCompany'])) {
            $billingAddress = BillingAddress::forCompany(
                new Company(
                    $payload['BillCompany'],
                    isset($payload['BillABN']) ? new Abn($payload['BillABN']) : null,
                    $payload['BillWebsite']
                ),
                $payload['BillFirstName'],
                $payload['BillLastName'],
                [
                    $payload['BillAddress'],
                    $payload['BillAddress2'],
                ],
                $payload['BillSuburb'],
                $payload['BillPostCode'],
                $payload['BillState'],
                $payload['BillCountry'],
                [
                    'phone' => $payload['BillPhone'],
                    'mobile' => $payload['BillMobile'],
                    'fax' => $payload['BillFax'],
                ]
            );
        } else {
            $billingAddress = BillingAddress::forIndividual(
                $payload['BillFirstName'],
                $payload['BillLastName'],
                [
                    $payload['BillAddress'],
                    $payload['BillAddress2'],
                ],
                $payload['BillSuburb'],
                $payload['BillPostCode'],
                $payload['BillState'],
                $payload['BillCountry'],
                [
                    'phone' => $payload['BillPhone'],
                    'mobile' => $payload['BillMobile'],
                    'fax' => $payload['BillFax'],
                ]
            );
        }

        list($deliveryFirstName, $deliveryLastName) = self::splitDeliveryName($payload['DelName'], [$payload['BillFirstName'], $payload['BillLastName']]);

        if (isset($payload['DelCompany'])) {
            $deliveryAddress = DeliveryAddress::forCompany(
                new Company($payload['DelCompany']),
                $deliveryFirstName,
                $deliveryLastName,
                [
                    $payload['DelAddress'],
                    $payload['DelAddress2'],
                ],
                $payload['DelSuburb'],
                $payload['DelPostCode'],
                $payload['DelState'],
                $payload['DelCountry'],
                [
                    'phone' => $payload['DelPhone'],
                    'mobile' => $payload['DelMobile'],
                ]
            );
        } else {
            $deliveryAddress = DeliveryAddress::forIndividual(
                $deliveryFirstName,
                $deliveryLastName,
                [
                    $payload['DelAddress'],
                    $payload['DelAddress2'],
                ],
                $payload['DelSuburb'],
                $payload['DelPostCode'],
                $payload['DelState'],
                $payload['DelCountry'],
                [
                    'phone' => $payload['DelPhone'],
                    'mobile' => $payload['DelMobile'],
                ]
            );
        }

        $customer = static::create(
            new CustomerId($payload['CustomerId']),
            new Email($payload['BillEmail']),
            $payload['BillFirstName'],
            $payload['BillLastName'],
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

<?php

namespace RetailExpress\SkyLink\Customers;

use RetailExpress\SkyLink\XmlKeySanitiser;
use Sabre\Xml\Element\KeyValue as KeyValueElement;
use Sabre\Xml\Reader as XmlReader;

trait V2CustomerDeserializer
{
    /**
     * @todo Implement company logic (delivery companies are just a name, billing include ABN, etc)
     */
    public static function xmlDeserialize(XmlReader $xmlReader)
    {
        $payload = XmlKeySanitiser::sanitise(
            KeyValueElement::xmlDeserialize($xmlReader)
        );

        $billingAddress = new BillingAddress(
            $payload['bill_first_name'],
            $payload['bill_last_name'],
            [
                $payload['bill_address'],
                $payload['bill_address2'],
            ],
            $payload['bill_suburb'],
            $payload['bill_post_code'],
            $payload['bill_state'],
            $payload['bill_country'],
            [
                'phone' => $payload['bill_phone'],
                'mobile' => $payload['bill_mobile'],
                'fax' => $payload['bill_fax'],
            ]
        );

        list($deliveryFirstName, $deliveryLastName) = self::splitDeliveryName($payload['del_name'], [$payload['bill_first_name'], $payload['bill_last_name']]);

        $deliveryAddress = new DeliveryAddress(
            $deliveryFirstName,
            $deliveryLastName,
            [
                $payload['del_address'],
                $payload['del_address2'],
            ],
            $payload['del_suburb'],
            $payload['del_post_code'],
            $payload['del_state'],
            $payload['del_country'],
            [
                'phone' => $payload['del_phone'],
                'mobile' => $payload['del_mobile'],
            ]
        );

        $customer = static::create(
            new CustomerId($payload['customer_id']),
            new Email($payload['bill_email']),
            $payload['bill_first_name'],
            $payload['bill_last_name'],
            $billingAddress,
            $deliveryAddress,
            $payload['receives_news']
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

<?php

namespace RetailExpress\SkyLink\Sdk\Customers;

trait V2BillingAndShippingContactSerializer
{
    public function serializeBillingAndShippingContacts(
        BillingContact $billingContact,
        ShippingContact $shippingContact
    ) {
        $payload = [];

        $payload['BillFirstName'] = (string) $billingContact->getName()->getFirstName();
        $payload['BillLastName'] = (string) $billingContact->getName()->getLastName();

        $payload['BillAddress'] = (string) $billingContact->getAddress()->getLine1();
        $payload['BillAddress2'] = (string) $billingContact->getAddress()->getLine2();
        $payload['BillCompany'] = (string) $billingContact->getCompanyName();
        $payload['BillSuburb'] = (string) $billingContact->getAddress()->getCity();
        $payload['BillPostCode'] = (string) $billingContact->getAddress()->getPostcode();
        $payload['BillState'] = (string) $billingContact->getAddress()->getState();
        $payload['BillCountry'] = (string) $billingContact->getAddress()->getCountry();
        $payload['BillPhone'] = (string) $billingContact->getPhoneNumber();

        $payload['BillEmail'] = (string) $billingContact->getEmailAddress();

        $payload['DelName'] = (string) $shippingContact->getName()->getFirstName();
        $shippingLastName = (string) $shippingContact->getName()->getLastName();
        if ($shippingLastName !== '') {
            $payload['DelName'] .= " {$shippingLastName}";
        }

        $payload['DelCompany'] = (string) $shippingContact->getCompanyName();
        $payload['DelAddress'] = (string) $shippingContact->getAddress()->getLine1();
        $payload['DelAddress2'] = (string) $shippingContact->getAddress()->getLine2();
        $payload['DelSuburb'] = (string) $shippingContact->getAddress()->getCity();
        $payload['DelPostCode'] = (string) $shippingContact->getAddress()->getPostcode();
        $payload['DelState'] = (string) $shippingContact->getAddress()->getState();
        $payload['DelCountry'] = (string) $shippingContact->getAddress()->getCountry();
        $payload['DelPhone'] = (string) $shippingContact->getPhoneNumber();

        return $payload;
    }
}

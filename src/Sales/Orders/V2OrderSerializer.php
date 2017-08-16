<?php

namespace RetailExpress\SkyLink\Sdk\Sales\Orders;

use RetailExpress\SkyLink\Sdk\Customers\V2BillingAndShippingContactSerializer;
use Sabre\Xml\Writer as XmlWriter;

trait V2OrderSerializer
{
    use V2BillingAndShippingContactSerializer;

    public function xmlSerialize(XmlWriter $xmlWriter)
    {
        $payload = [];

        $payload['DateCreated'] = to_v2_rex_date($this->getPlacedAt());
        $payload['OrderTotal'] = (string) $this->getTotal();
        $payload['FreightTotal'] = (string) $this->getShippingCharge()->getPrice();
        $payload['OrderStatus'] = $this->getStatus()->toV2Status();

        if (null !== $this->getCustomerId()) {
            $payload['CustomerId'] = (string) $this->getCustomerId();
        } else {
            $payload['Password'] = (string) $this->getNewCustomerPassword();
        }

        $payload = array_merge(
            $payload,
            $this->serializeBillingAndShippingContacts(
                $this->getBillingContact(),
                $this->getShippingContact()
            )
        );

        $payload['ReceivesNews'] = 1;
        $payload['PublicComments'] = (string) $this->getPublicComments();
        $payload['PrivateComments'] = (string) $this->getPrivateComments();

        if ($this->specifiedOutletIdToFulfillFrom()) {
            $payload['FulfilmentOutletID'] = (string) $this->getOutletIdToFulfillFrom();
        }

        $payload['OrderItems'] = [];

        foreach ($this->getItems() as $item) {
            $itemPayload = [];

            $itemPayload['ProductId'] = (string) $item->getProductId();
            $itemPayload['QtyOrdered'] = (string) $item->getQty()->getOrdered();
            $itemPayload['QtyFulfilled'] = (string) $item->getQty()->getFulfilled();
            $itemPayload['UnitPrice'] = (string) $item->getPrice();
            $itemPayload['TaxRateApplied'] = (string) $item->getTaxRate()->getRate();

            // The fulfillment method comes from the order itself (since Rex does not support
            // multiple fulfillment methods, this ensures we're sending sanitised data for them!)
            $itemPayload['DeliveryMethod'] = $this->getItemFulfillmentMethod()->getV2XmlAttribute();

            if ($this->specifiedItemDeliveryDriverName()) {
                $itemPayload['DeliveryDriverName'] = (string) $this->getItemDeliveryDriverName();
            }

            $payload['OrderItems'][]['OrderItem'] = $itemPayload;
        }

        $xmlWriter->write($payload);
    }
}

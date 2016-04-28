<?php

namespace RetailExpress\SkyLink\Sales\Orders;

use RetailExpress\SkyLink\Customers\V2BillingAndShippingContactSerializer;
use Sabre\Xml\Writer as XmlWriter;

trait V2OrderSerializer
{
    use V2BillingAndShippingContactSerializer;

    public function xmlSerialize(XmlWriter $xmlWriter)
    {
        $payload = [];

        $payload['DateCreated'] = date(V2_API_DATE_FORMAT, $this->getPlacedAt()->getTimestamp());
        $payload['OrderTotal'] = (string) $this->getTotal();
        $payload['FreightTotal'] = (string) $this->getShippingCharge()->getPrice();
        $payload['OrderStatus'] = $this->getStatus()->toV2Status();

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
            $payload['FulfilmentOutletId'] = (string) $this->getOutletIdToFulfillFrom();
        }

        $payload['OrderItems'] = [];

        foreach ($this->getItems() as $item) {
            $itemPayload = [];

            $itemPayload['ProductId'] = (string) $item->getProductId();
            $itemPayload['QtyOrdered'] = (string) $item->getQty()->getOrdered();
            $itemPayload['QtyFulfilled'] = (string) $item->getQty()->getFulfilled();
            $itemPayload['UnitPrice'] = (string) $item->getPrice();
            $itemPayload['TaxRateApplied'] = (string) $item->getTaxRate()->getRate();

            $payload['OrderItems'][]['OrderItem'] = $itemPayload;
        }

        $xmlWriter->write($payload);
    }
}

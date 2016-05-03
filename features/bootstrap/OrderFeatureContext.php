<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;

use RetailExpress\SkyLink\Customers\BillingContact;
use RetailExpress\SkyLink\Customers\ShippingContact;
use RetailExpress\SkyLink\Sales\Orders\Item;
use RetailExpress\SkyLink\Sales\Orders\Order;
use RetailExpress\SkyLink\Sales\Orders\ShippingCharge;
use RetailExpress\SkyLink\Sales\Orders\Status;
use ValueObjects\StringLiteral\StringLiteral;

trait OrderFeatureContext
{
    private $pendingOrderInformation = [];

    private $order;

    /**
     * @Given I want to ship my order to:
     */
    public function iWantToShipMyOrderTo(PyStringNode $address)
    {
        $this->pendingOrderInformation['shippingAddress'] = $address->getStrings();
    }

    /**
     * @Given I order :arg1 of the product with id :arg2 for :arg3
     */
    public function iOrderOfTheProductWithIdFor($qtyOrdered, $productId, $price)
    {
        $item = [
            'product_id' => $productId,
            'qty_ordered' => $qtyOrdered,
            'price' => $price,
        ];

        $this->pendingOrderInformation['items'][] = $item;
    }

    /**
     * @Given I am willing to pay :arg1 for shipping
     */
    public function iAmWillingToPayForShipping($shippingCharge)
    {
        $this->pendingOrderInformation['shippingCharge'] = $shippingCharge;
    }

    /**
     * @Then I should be able to add a new order for my new customer
     */
    public function iShouldBeAbleToAddANewOrderForMyNewCustomer()
    {
        extract($this->pendingCustomerInformation);
        extract($this->pendingOrderInformation);

        $placedAt = new DateTimeImmutable();

        $password = new StringLiteral($password);

        $status = Status::fromNative('pending');

        $billingContact = BillingContact::fromNative($firstName, $lastName, $emailAddress);

        $shippingContact = ShippingContact::fromNative(
            $firstName,
            $lastName,
            $shippingAddress[0],
            $shippingAddress[1],
            '',
            $shippingAddress[2],
            $shippingAddress[3],
            $shippingAddress[4],
            $shippingAddress[5]
        );

        $shippingCharge = ShippingCharge::fromNative($shippingCharge, $taxRate = '10%');

        $this->order = Order::forNewCustomerWithPassword(
            $password,
            $placedAt,
            $status,
            $billingContact,
            $shippingContact,
            $shippingCharge
        );

        foreach ($items as $item) {
            $item = Item::fromNative(
                $item['product_id'],
                $item['qty_ordered'],
                $qtyFulfilled = 0,
                $item['price'],
                $taxRate = '10%'
            );

            $this->order = $this->order->withItem($item);
        }

        $this->orderRepository->add($this->salesChannelId, $this->order);
    }

    /**
     * @Then I should have a new customer id and order id
     */
    public function iShouldHaveANewCustomerIdAndOrderId()
    {
        if (null === $this->order->getCustomerId()) {
            throw new Exception('No valid customer ID was present.');
        }

        if (null === $this->order->getId()) {
            throw new Exception('No valid order ID was present.');
        }
    }
}
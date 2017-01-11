<?php

use Behat\Gherkin\Node\PyStringNode;
use RetailExpress\SkyLink\Sdk\Customers\BillingContact;
use RetailExpress\SkyLink\Sdk\Customers\ShippingContact;
use RetailExpress\SkyLink\Sdk\Sales\Orders\Item;
use RetailExpress\SkyLink\Sdk\Sales\Orders\Order;
use RetailExpress\SkyLink\Sdk\Sales\Orders\OrderId;
use RetailExpress\SkyLink\Sdk\Sales\Orders\ShippingCharge;
use RetailExpress\SkyLink\Sdk\Sales\Orders\Status;
use RetailExpress\SkyLink\Sdk\Sales\Payments\Payment;
use ValueObjects\StringLiteral\StringLiteral;

trait OrderFeatureContext
{
    private $orderRepository;

    private $paymentRepository;

    private $pendingOrderInformation = [];

    private $order;

    private $payment;

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

    /**
     * @Then I should be able to find the order :arg1
     */
    public function iShouldBeAbleToFindTheOrder($orderId)
    {
        $orderId = new OrderId($orderId);

        $this->order = $this->orderRepository->get($orderId);

        if (null === $this->order) {
            throw new Exception("Order with ID {$orderId} could not be found.");
        }
    }

    /**
     * @Then I can pay a total of :arg1 towards the order using payment method :arg2
     */
    public function iCanPayATotalOfTowardsTheOrderUsingPaymentMethod($total, $methodId)
    {
        $this->payment = Payment::normalFromNative((string) $this->order->getId(), time(), $methodId, $total);

        $this->paymentRepository->add($this->payment);
    }
}

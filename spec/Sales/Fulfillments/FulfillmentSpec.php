<?php

namespace spec\RetailExpress\SkyLink\Sdk\Sales\Fulfillments;

use DateTimeImmutable;
use LogicException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use RetailExpress\SkyLink\Sdk\Sales\Fulfillments\Fulfillment;
use RetailExpress\SkyLink\Sdk\Sales\Fulfillments\FulfillmentId;
use RetailExpress\SkyLink\Sdk\Sales\Orders\OrderId;
use RetailExpress\SkyLink\Sdk\Sales\Orders\ItemId;
use ValueObjects\Number\Real;

class FulfillmentSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->beConstructedThrough('fromNative', ['1-1', 1, time(), 1]);
        $this->shouldHaveType(Fulfillment::class);
    }

    public function it_exposes_an_order_id()
    {
        $this->beConstructedThrough('fromNative', [$orderIdString = '1-1', 1, time(), 1]);
        $this->getOrderId()->sameValueAs(new OrderId($orderIdString))->shouldBe(true);
    }

    public function it_exposes_an_order_item_id()
    {
        $this->beConstructedThrough('fromNative', ['1-1', $orderItemInteger = 1, time(), 1]);
        $this->getOrderItemId()->sameValueAs(new ItemId($orderItemInteger))->shouldBe(true);
    }

    public function it_exposes_fulfilled_at()
    {
        $this->beConstructedThrough('fromNative', ['1-1', 1, $fulfilledAtTimestamp = time(), 1]);
        $this->getFulfilledAt()->getTimestamp()->shouldReturn($fulfilledAtTimestamp);
    }

    public function it_exposes_its_qty()
    {
        $this->beConstructedThrough('fromNative', ['1-1', 1, time(), $qty = 1]);
        $this->getQty()->sameValueAs(new Real($qty))->shouldBe(true);
    }

    public function it_allows_an_id_to_be_set_and_gotten()
    {
        $this->beConstructedThrough('fromNative', ['1-1', 1, time(), 1]);
        $this->getId()->shouldBe(null);
        $this->setId($id = new FulfillmentId('my id'));
        $this->getId()->sameValueAs($id)->shouldBe(true);
    }

    public function it_doesnt_allow_the_id_to_be_overwritten()
    {
        $this->beConstructedThrough('fromNative', ['1-1', 1, time(), 1]);
        $this->setId(new FulfillmentId('my id'));

        $this
            ->shouldThrow(LogicException::class)
            ->duringSetId(new FulfillmentId('another id'));
    }
}

<?php

namespace spec\RetailExpress\SkyLink\Sdk\Sales\Fulfillments;

use DateTimeImmutable;
use InvalidArgumentException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use RetailExpress\SkyLink\Sdk\Sales\Fulfillments\Batch;
use RetailExpress\SkyLink\Sdk\Sales\Fulfillments\BatchId;
use RetailExpress\SkyLink\Sdk\Sales\Fulfillments\Fulfillment;
use RetailExpress\SkyLink\Sdk\Sales\Fulfillments\FulfillmentId;
use RetailExpress\SkyLink\Sdk\Sales\Orders\OrderId;
use ValueObjects\Number\Integer;

class BatchSpec extends ObjectBehavior
{
    private $fulfillment1;

    private $fulfillment2;

    private $orderId;

    private $fulfilledAt;

    public function let(
        Fulfillment $fulfillment1,
        Fulfillment $fulfillment2
    ) {
        $this->fulfillment1 = $fulfillment1;
        $this->fulfillment2 = $fulfillment2;

        $this->fulfillment1->getOrderId()->willReturn($this->orderId = new OrderId('1-1'));
        $this->fulfillment1->getFulfilledAt()->willReturn(
            $this->fulfilledAt = new DateTimeImmutable()
        );
        $this->fulfillment1->getId()->willReturn(null);
        $this->fulfillment2->getOrderId()->willReturn($this->orderId);
        $this->fulfillment2->getFulfilledAt()->willReturn($this->fulfilledAt);
        $this->fulfillment2->getId()->willReturn(null);


        $this->beConstructedWith([
            $this->fulfillment1,
            $this->fulfillment2,
        ]);
    }

    public function it_is_initializable()
    {
        $this->shouldNotThrow(InvalidArgumentException::class)->duringInstantiation();
        $this->shouldHaveType(Batch::class);
    }

    public function it_compares_all_fulfillments_are_from_the_same_order()
    {
        $this->fulfillment2->getOrderId()->willReturn(new OrderId('1-2'));
        $this->shouldThrow(InvalidArgumentException::class)->duringInstantiation();
    }

    public function it_returns_the_fulfillments()
    {
        $this->getFulfillments()->shouldHaveCount(2);
        $this->getFulfillments()[0]->shouldBeAnInstanceOf($this->fulfillment1);
    }

    public function it_returns_the_order_id()
    {
        $this->getOrderId()->sameValueAs($this->orderId)->shouldBe(true);
    }

    public function it_returns_fulfilled_at()
    {
        $this->getFulfilledAt()->getTimestamp()->shouldReturn($this->fulfilledAt->getTimestamp());
    }

    public function it_returns_no_id_when_fulfillments_have_none()
    {
        $this->getId()->shouldBe(null);
    }

    public function it_returns_a_hash_of_fulfillment_ids()
    {
        $this->fulfillment1->getId()->willReturn(new FulfillmentId($id1 = 'first id'));
        $this->fulfillment2->getId()->willReturn(new FulfillmentId($id2 = 'second id'));

        $batchIdHash = md5(implode('', [$id1, $id2]));
        $this->getId()->sameValueAs(new BatchId($batchIdHash))->shouldBe(true);
    }
}

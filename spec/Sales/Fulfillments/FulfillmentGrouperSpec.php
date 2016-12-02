<?php

namespace spec\RetailExpress\SkyLink\Sdk\Sales\Fulfillments;

use DateTimeImmutable;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use RetailExpress\SkyLink\Sdk\Sales\Fulfillments\BatchThreshold;
use RetailExpress\SkyLink\Sdk\Sales\Fulfillments\Fulfillment;
use RetailExpress\SkyLink\Sdk\Sales\Fulfillments\FulfillmentGrouper;
use ValueObjects\Number\Integer;

class FulfillmentGrouperSpec extends ObjectBehavior
{
    private $batchThreshold;

    private $fulfillment1;

    private $fulfillment2;

    private $fulfillment3;

    public function let(
        BatchThreshold $batchThreshold,
        Fulfillment $fulfillment1,
        Fulfillment $fulfillment2,
        Fulfillment $fulfillment3
    ) {
        $batchThreshold->getSeconds()->willReturn(new Integer(5));
        $this->batchThreshold = $batchThreshold;

        $this->fulfillment1 = $fulfillment1;
        $this->fulfillment2 = $fulfillment2;
        $this->fulfillment3 = $fulfillment3;

        $this->beConstructedWith($this->batchThreshold);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(FulfillmentGrouper::class);
    }

    public function it_can_batch_fulfillments_with_the_same_timestamp()
    {
        $this->fulfillment1->getFulfilledAt()->willReturn($fulfilledAt = new DateTimeImmutable());
        $this->fulfillment2->getFulfilledAt()->willReturn($fulfilledAt);
        $this->fulfillment3->getFulfilledAt()->willReturn($fulfilledAt);

        $groups = $this->groupForBatching([$this->fulfillment1, $this->fulfillment2, $this->fulfillment3]);

        $groups->shouldBeArray();
        $groups->shouldHaveCount(1);
        $groups[0][0]->shouldBe($this->fulfillment1);
        $groups[0][1]->shouldBe($this->fulfillment2);
        $groups[0][2]->shouldBe($this->fulfillment3);
    }

    public function it_can_batch_fulfillments_where_the_latest_fulfillment_is_older_than_the_others()
    {
        $this->fulfillment1->getFulfilledAt()->willReturn($fulfilledAtNow = new DateTimeImmutable());
        $this->fulfillment2->getFulfilledAt()->willReturn($fulfilledAtNow);
        $this->fulfillment3->getFulfilledAt()->willReturn(
            $fulfilledAtOneHourAgo = (new DateTimeImmutable())->modify('-1 hour')
        );

        $groups = $this->groupForBatching([$this->fulfillment1, $this->fulfillment2, $this->fulfillment3]);

        $groups->shouldBeArray();
        $groups->shouldHaveCount(2);
        $groups[0][0]->shouldBe($this->fulfillment3);
        $groups[1][0]->shouldBe($this->fulfillment1);
        $groups[1][1]->shouldBe($this->fulfillment2);
    }
}

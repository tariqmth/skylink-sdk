<?php

namespace spec\RetailExpress\SkyLink\Sdk\Sales\Fulfillments;

use BadMethodCallException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use RetailExpress\SkyLink\Sdk\Sales\Fulfillments\BatchThreshold;
use ValueObjects\Exception\InvalidNativeArgumentException;
use ValueObjects\NullValue\NullValue;
use ValueObjects\Number\Integer;

class BatchThresholdSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->beConstructedThrough('fromNative', [60]);
        $this->shouldHaveType(BatchThreshold::class);
    }

    public function it_validates_an_argument_is_provided()
    {
        $this->beConstructedThrough('fromNative', []);
        $this->shouldThrow(BadMethodCallException::class)->duringInstantiation();
    }

    public function it_validates_seconds_are_not_negative()
    {
        $this->beConstructedThrough('fromNative', [-1]);

        $this
            ->shouldThrow(InvalidNativeArgumentException::class)
            ->duringInstantiation();
    }

    public function it_exposes_seconds()
    {
        $this->beConstructedThrough('fromNative', [60]);
        $this->getSeconds()->sameValueAs(new Integer(60))->shouldBe(true);
    }

    public function its_value_can_be_compared_against_an_equal()
    {
        $this->beConstructedThrough('fromNative', [60]);
        $this->sameValueAs(BatchThreshold::fromNative(60))->shouldBe(true);
    }

    public function its_value_can_be_compared_against_a_non_equal_of_the_same_type()
    {
        $this->beConstructedThrough('fromNative', [60]);
        $this->sameValueAs(BatchThreshold::fromNative(0))->shouldBe(false);
    }

    public function its_value_can_be_compared_against_a_non_equal_of_different_type()
    {
        $this->beConstructedThrough('fromNative', [60]);
        $this->sameValueAs(new NullValue())->shouldBe(false);
    }

    public function it_can_be_represented_as_a_string()
    {
        $this->beConstructedThrough('fromNative', [60]);
        $this->__toString()->shouldBe('60');
    }
}

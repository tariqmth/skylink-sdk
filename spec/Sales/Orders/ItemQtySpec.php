<?php

namespace spec\RetailExpress\SkyLink\Sdk\Sales\Orders;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

use BadMethodCallException;
use InvalidArgumentException;
use RetailExpress\SkyLink\Sdk\Sales\Orders\ItemQty;
use ValueObjects\NullValue\NullValue;

class ItemQtySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->beConstructedThrough('fromNative', [1, 0]);
        $this->shouldHaveType(ItemQty::class);
    }

    function it_forces_a_qty_ordered_to_be_supplied()
    {
        $this->beConstructedThrough('fromNative');
        $this->shouldThrow(BadMethodCallException::class)->duringInstantiation();
    }

    function it_allows_us_to_provide_just_a_qty_ordered()
    {
        $this->beConstructedThrough('fromNative', [1]);
        $this->getOrdered()->toInteger()->toNative()->shouldBe(1);
    }

    function it_allows_to_get_qty_fulfilled()
    {
        $this->beConstructedThrough('fromNative', [1, 0]);
        $this->getFulfilled()->toInteger()->toNative()->shouldBe(0);
    }

    function it_returns_zero_qty_fulfilled_if_no_argument_was_provided()
    {
        $this->beConstructedThrough('fromNative', [1]);
        $this->getFulfilled()->toInteger()->toNative()->shouldBe(0);
    }

    function it_can_be_represented_as_a_string()
    {
        $this->beConstructedThrough('fromNative', [1]);
        $this->__toString()->shouldBe('1');
    }

    function it_wont_allow_us_to_fulfill_more_than_we_order()
    {
        $this->beConstructedThrough('fromNative', [1, 2]);
        $this->shouldThrow(InvalidArgumentException::class)->duringInstantiation();
    }

    function it_wont_allow_us_to_fulfill_more_than_we_order_for_negative_qty()
    {
        $this->beConstructedThrough('fromNative', [-1, -2]);
        $this->shouldThrow(InvalidArgumentException::class)->duringInstantiation();
    }

    function it_wont_allow_us_to_fulfill_more_than_we_order_for_incompatible_qty()
    {
        $this->beConstructedThrough('fromNative', [-1, 2]);
        $this->shouldThrow(InvalidArgumentException::class)->duringInstantiation();
    }

    function its_value_can_be_compared_against_an_equal()
    {
        $this->beConstructedThrough('fromNative', [1]);
        $this->sameValueAs(ItemQty::fromNative(1))->shouldBe(true);
    }

    function its_value_can_be_compared_against_a_non_equal_of_the_same_type()
    {
        $this->beConstructedThrough('fromNative', [1]);
        $this->sameValueAs(ItemQty::fromNative(2))->shouldBe(false);
    }

    function its_value_can_be_compared_against_a_non_equal_of_different_type()
    {
        $this->beConstructedThrough('fromNative', [1]);
        $this->sameValueAs(new NullValue())->shouldBe(false);
    }
}

<?php

namespace spec\RetailExpress\SkyLink\Sdk;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class UtilSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('RetailExpress\SkyLink\Sdk\Util');
    }

    function it_returns_non_empty_values()
    {
        $array = ['foo' => 'bar'];

        $this::arrayGetNotempty($array, 'foo')->shouldReturn('bar');
    }

    function it_doesnt_return_empty_values()
    {
        $array = ['foo' => ''];

        $this::arrayGetNotempty($array, 'foo')->shouldReturn(null);
    }

    function it_should_allow_a_default_for_empty_values()
    {
        $array = ['foo' => ''];

        $this::arrayGetNotempty($array, 'foo', 'bar')->shouldReturn('bar');
    }
}

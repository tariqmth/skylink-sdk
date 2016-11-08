<?php

namespace spec\RetailExpress\SkyLink\Sdk;

use PhpSpec\ObjectBehavior;

class UtilSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('RetailExpress\SkyLink\Sdk\Util');
    }

    public function it_returns_non_empty_values()
    {
        $array = ['foo' => 'bar'];

        $this::arrayGetNotempty($array, 'foo')->shouldReturn('bar');
    }

    public function it_doesnt_return_empty_values()
    {
        $array = ['foo' => ''];

        $this::arrayGetNotempty($array, 'foo')->shouldReturn(null);
    }

    public function it_should_allow_a_default_for_empty_values()
    {
        $array = ['foo' => ''];

        $this::arrayGetNotempty($array, 'foo', 'bar')->shouldReturn('bar');
    }
}

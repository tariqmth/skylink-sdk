<?php

namespace spec\RetailExpress\SkyLink\ValueObjects\Person;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

use RetailExpress\SkyLink\ValueObjects\Person\Name;

class NameSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->beConstructedThrough('fromNative', ['Ben', 'Corlett']);
        $this->shouldHaveType(Name::class);
    }
}

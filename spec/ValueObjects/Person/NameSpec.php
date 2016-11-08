<?php

namespace spec\RetailExpress\SkyLink\Sdk\ValueObjects\Person;

use PhpSpec\ObjectBehavior;
use RetailExpress\SkyLink\Sdk\ValueObjects\Person\Name;

class NameSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->beConstructedThrough('fromNative', ['Ben', 'Corlett']);
        $this->shouldHaveType(Name::class);
    }
}

<?php

namespace spec\Venue;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class EventSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->beConstructedWith('eventname');
        $this->shouldHaveType('Venue\Event');
    }
}

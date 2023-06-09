<?php

namespace spec\Venue;

use PhpSpec\ObjectBehavior;
use Venue\Event;

class EventSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(Event::class);
    }
}

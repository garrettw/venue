<?php

namespace spec\Venue;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class MediatorSpec extends ObjectBehavior
{
    function let() {
        $this->beConstructedWith(new \Venue\Manager);
    }

    function it_is_initializable()
    {
        $this->shouldBeAnInstanceOf('Venue\Mediator');
    }

    function it_will_not_hold_unheard_events_by_default()
    {
        $this->holdUnheardEvents()->shouldBe(false);
    }

    function it_may_hold_unheard_events()
    {
        $this->holdUnheardEvents(true);

        $this->holdUnheardEvents()->shouldBe(true);
    }

    function it_clears_the_pending_list()
    {
        $this->holdUnheardEvents(true);

        $this->publish(new \Venue\Event('randomname'));
        $this->holdUnheardEvents(false);

        $this->held->shouldEqual([]);
    }

    function it_holds_the_pending_event()
    {
        $this->holdUnheardEvents(true);

        $this->publish(new \Venue\Event('randomname'));

        $this->held[0]->shouldBeAnInstanceOf('Venue\Event');
        $this->held[0]->name->shouldEqual('randomname');
    }

    function it_has_no_subscribers()
    {
        $this->shouldNotHaveSubscribers('randomname');
    }

    function it_can_register_basic_handlers()
    {
        $eventname = 'randomname';
        $callback = function() {};

        $this->subscribe([$eventname => [$callback]]);

        $this->shouldHaveSubscribers($eventname);
        $this->isSubscribed($eventname, $callback)->shouldBeInteger();
    }

    function it_can_register_time_handlers()
    {
        $eventname = 'timer';
        $callback = function() {};

        $this->subscribe([$eventname . ':10' => [$callback]]);

        $this->shouldHaveSubscribers($eventname);
        $this->isSubscribed($eventname, $callback)->shouldBeInteger();
    }

    function it_can_unregister_all_handlers()
    {
        $eventname = 'randomname';
        $callback = function() {};
        $this->subscribe([$eventname => [$callback]]);
        $this->subscribe([$eventname => [$callback]]);

        $this->unsubscribe([$eventname => '*']);

        $this->shouldNotHaveSubscribers($eventname);
    }

    function it_can_unregister_specific_handlers()
    {
        $eventname = 'randomname';
        $callback1 = function() { return true; };
        $callback2 = function() { return false; };
        $this->subscribe([$eventname => [$callback1]]);
        $this->subscribe([$eventname => [$callback2]]);

        $this->unsubscribe([$eventname => $callback1]);

        $this->isSubscribed($eventname, $callback2)->shouldBeInteger();
        $this->isSubscribed($eventname, $callback1)->shouldReturn(false);
    }

    function it_can_publish_events()
    {
        $eventname = 'randomname';
        $callback = function() { return 'event handled'; };
        $this->subscribe([$eventname => [$callback]]);

        $this->publish(new \Venue\Event($eventname))->shouldReturn('event handled');
    }

    function it_can_handle_timed_events()
    {
        $eventname = 'timer';
        $callback = function() { return 'timed event handled'; };
        $this->subscribe([$eventname . ':100' => [$callback]]);
        $timer = new \Venue\Event('timer');

        while (($result = $this->getWrappedObject()->publish($timer)) === null) {}

        if ($result != 'timed event handled') throw new \Exception("bad result: $result");
    }
}

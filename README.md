[![Packagist Downloads](https://img.shields.io/packagist/dt/garrettw/noair.svg)](https://packagist.org/packages/garrettw/noair) [![Latest Stable Version](https://img.shields.io/packagist/v/garrettw/noair.svg)](https://packagist.org/packages/garrettw/noair) [![License](https://poser.pugx.org/garrettw/noair/license.svg)](https://packagist.org/packages/garrettw/noair)

[![Build Status](https://travis-ci.org/garrettw/noair.svg?branch=master)](https://travis-ci.org/garrettw/noair) [![Code Climate](https://codeclimate.com/github/garrettw/noair/badges/gpa.svg)](https://codeclimate.com/github/garrettw/noair) [![SensioLabsInsight](https://img.shields.io/sensiolabs/i/fc0bc904-ef77-4ed4-b474-8ce3db9a4cc2.svg)](https://insight.sensiolabs.com/projects/fc0bc904-ef77-4ed4-b474-8ce3db9a4cc2) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/garrettw/noair/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/garrettw/noair/?branch=master)

Venue
======

Venue is a PHP event library implementing the Mediator and Observer patterns.

**"Why should I use Venue instead of all the other event libraries out there?"**

Because Venue:
- Uses standard PSR-14 terminology
- Can optionally hang onto dispatched events for which there is no listener until such time as one starts listening for it
- Supports timer events in addition to normal published events
- Allows listeners to watch any and all events if they want
- Encapsulates event information in an object that gets passed to listeners
- Event objects can hold custom data set by the emitter for listeners to use
- Event objects allow listeners to access the context that published them
- Event objects allow listeners to access previous listener output (for daisy-chaining)
- Event objects can prevent further daisy-chaining by setting stopPropagation
- Observer objects can define handlers explicitly or use on*() method naming, where * is the capitalized event name

Core Principles
-------
- Listeners are callables that watch for, and receive, published events.
- It is recommended (but optional) that listener provider objects be used to contain listeners.

Basic usage
-------
The idea is that you have multiple event listeners watching a single event hub, waiting for events they can handle.

- First, you'll create child classes of Venue's abstract Observer class that contain handlers:
```php
class MyObserver extends \Venue\Observer
{
    public function onThing(\Venue\Event $e)
    {
        return 'do it ' . $e->data;
    }
}
```
- Now, in the main code you're executing, you'll need to create a hub for your events: a Mediator object which serves as a go-between for your handlers and your code that fires/publishes the events.
```php
$hub = new \Venue\Mediator(new \Venue\Manager);
```
- Then, you can create objects of your own Observer classes and subscribe them to the hub.
```php
$obs = (new MyObserver($hub))->subscribe();
```
- You will then use that Mediator object in your code to publish events that the Observer classes may handle.
```php
$hub->publish(new \Venue\Event('thing', 'now'));

// Now if you're an object-oriented fiend like me, you'll probably be calling that
// from within a method, like so:
// $this->mediator->publish(new \Venue\Event('thing', 'now', $this));

// Anyway, either of those will return: 'do it now'
```

Advanced usage
-------
The only "advanced" thing you can do is set up handlers with custom method names,
custom priorities, or forceability (this means that the handler will be run even if
another handler higher up the chain tries to cancel the rest of the chain).
You do this by defining (actually, overriding) the `subscribe()` method as follows:

```php
class OtherObserver extends \Venue\Observer
{
    public function subscribe() {
        $this->handlers = [
            'doWeirdThings' => [ // an event name that this class handles
                [$this, 'doWeirdThingsAlways'], // the callable that the event fires
                \Venue\Mediator::PRIORITY_HIGHEST, // how important this handler is
                true, // this is the forceability setting
            ],
        ];

        return parent::subscribe();
    }

    // This is just a normal handler
    public function onThing(\Venue\Event $e)
    {
        return 'do it ' . $e->data;
    }

    // Wait, this function doesn't start with "on"! How can it work?
    // See subscribe() above.
    public function doWeirdThingsAlways(\Venue\Event $e)
    {
        return 'do ' . $e->data . ' ' . rand() . ' times';
    }
}

$hub = new \Venue\Mediator();
$obs = (new OtherObserver($hub))->subscribe();

$hub->publish(new \Venue\Event('doWeirdThings', 'stuff'));
```

That might return: `do stuff 5623 times`

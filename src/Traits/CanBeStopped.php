<?php

namespace Venue\Traits;

/**
 * Includes CanStopPropagation.
 * Adds a method to stop propagation from outside the event.
 */
trait CanBeStopped
{
    use CanStopPropagation;

    /**
     * Determine whether this event should be sent to further listeners
     *
     * @return bool whether propagation was stopped
     */
    public function stopPropagation(bool $stop = true): bool
    {
        $this->stopPropagation = $stop;
        return $stop;
    }
}

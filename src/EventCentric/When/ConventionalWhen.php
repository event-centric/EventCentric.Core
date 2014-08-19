<?php

namespace EventCentric\When;

use EventCentric\DomainEvents\DomainEvent;
use EventCentric\DomainEvents\DomainEvents;
use Verraes\ClassFunctions\ClassFunctions;

trait ConventionalWhen
{
    use When;

    /**
     * @param DomainEvent $event
     * @return void
     */
    protected function when(DomainEvent $event)
    {
        $method = 'when' . ClassFunctions::short($event);
        if (is_callable([$this, $method])) {
            $this->{$method}($event);
        }
    }

    /**
     * @param DomainEvents $events
     * @return void
     */
    protected function whenAll(DomainEvents $events)
    {
        foreach ($events as $event) {
            $this->when($event);
        }
    }
}

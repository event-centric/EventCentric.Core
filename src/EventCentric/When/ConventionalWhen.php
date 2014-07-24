<?php

namespace EventCentric\When;

use EventCentric\DomainEvents\DomainEvent;
use EventCentric\DomainEvents\DomainEvents;

trait ConventionalWhen
{
    use When;

    /**
     * @param DomainEvent $event
     * @return void
     */
    protected function when(DomainEvent $event)
    {
        $method = 'when' . $this->short($event);
        if(is_callable([$this, $method])) {
            $this->{$method}($event);
        }
    }

    /**
     * @param DomainEvents $events
     * @return void
     */
    protected function whenAll(DomainEvents $events)
    {
        foreach($events as $event) {
            $this->when($event);
        }
    }

    /**
     * The class name of an object, without the namespace
     * @param $object
     * @return string
     */
    private function short($object)
    {
        $parts = explode('\\', trim(get_class($object), '\\'));
        return end($parts);
    }
}

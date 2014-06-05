<?php

namespace EventCentric\Protection\AggregateRoot;

use EventCentric\DomainEvents\DomainEvents;

trait Reconstitution
{
    /**
     * @param DomainEvents $history
     * @return static
     */
    public static function reconstituteFrom(DomainEvents $history)
    {
        /** @var $instance Reconstitution */
        $instance = new static;
        $instance->whenAll($history);
        return $instance;
    }

    abstract protected function whenAll(DomainEvents $events);

} 
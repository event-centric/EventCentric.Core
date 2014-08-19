<?php

namespace EventCentric\AggregateRoot;

use EventCentric\DomainEvents\DomainEvents;

/**
 * Implements ReconstitutesFromHistory
 */
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

    /**
     * React to a series of Domain Events.
     * @param DomainEvents $events
     * @return void
     */
    abstract protected function whenAll(DomainEvents $events);
}

<?php

namespace EventCentric\Protection\AggregateRoot;

use EventCentric\DomainEvents\DomainEvents;
use EventCentric\Protection\AggregateRoot\AggregateRoot;

trait Reconstitution
{
    /**
     * @param DomainEvents $history
     * @return AggregateRoot
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
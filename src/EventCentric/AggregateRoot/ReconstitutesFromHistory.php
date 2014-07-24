<?php

namespace EventCentric\AggregateRoot;

use EventCentric\DomainEvents\DomainEvents;
use EventCentric\AggregateRoot\AggregateRoot;

/**
 * Interface to provide the ability to reconstitute the state of an Aggregate from a history of Domain Events.
 * @package EventCentric\AggregateRoot
 */
interface ReconstitutesFromHistory
{
    /**
     * @param DomainEvents $history
     * @return AggregateRoot
     */
    public static function reconstituteFrom(DomainEvents $history);
}
<?php

namespace EventCentric\AggregateRoot;

use EventCentric\DomainEvents\DomainEvents;
use EventCentric\AggregateRoot\AggregateRoot;

/**
 * Reconstitutes an Aggregate instance from its history of Domain Events
 */
interface ReconstitutesFromHistory
{
    /**
     * @param DomainEvents $history
     * @return AggregateRoot
     */
    public static function reconstituteFrom(DomainEvents $history);
}
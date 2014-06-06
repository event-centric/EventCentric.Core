<?php

namespace EventCentric\Aggregates\AggregateRoot;

use EventCentric\DomainEvents\DomainEvents;
use EventCentric\Aggregates\AggregateRoot\AggregateRoot;

interface ReconstitutesFromHistory
{
    /**
     * @param DomainEvents $history
     * @return AggregateRoot
     */
    public static function reconstituteFrom(DomainEvents $history);
}
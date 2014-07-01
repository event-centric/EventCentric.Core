<?php

namespace EventCentric\AggregateRoot;

use EventCentric\DomainEvents\DomainEvents;
use EventCentric\AggregateRoot\AggregateRoot;

interface ReconstitutesFromHistory
{
    /**
     * @param DomainEvents $history
     * @return AggregateRoot
     */
    public static function reconstituteFrom(DomainEvents $history);
}
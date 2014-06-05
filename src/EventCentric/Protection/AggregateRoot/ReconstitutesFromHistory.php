<?php

namespace EventCentric\Protection\AggregateRoot;

use EventCentric\DomainEvents\DomainEvents;
use EventCentric\Protection\AggregateRoot\AggregateRoot;

interface ReconstitutesFromHistory
{
    /**
     * @param DomainEvents $history
     * @return AggregateRoot
     */
    public static function reconstituteFrom(DomainEvents $history);
}
<?php

namespace EventCentric\UnitOfWork;

use EventCentric\Contracts\Contract;
use EventCentric\DomainEvents\DomainEvents;

/**
 * Reconstitute an AggregateRoot using its history of DomainEvents
 * @package EventCentric\UnitOfWork
 */
interface AggregateRootReconstituter
{
    /**
     * @param Contract $contract
     * @param DomainEvents $domainEvents
     * @return object the AggregateRoot
     */
    public function reconstitute(Contract $contract, DomainEvents $domainEvents);
}
<?php

namespace EventCentric\UnitOfWork;

use EventCentric\Contracts\Contract;
use EventCentric\DomainEvents\DomainEvents;

/**
 * Reconstitute an AggregateRoot using the class name as implemented by the Contract.
 */
final class ClassNameBasedAggregateRootReconstituter implements AggregateRootReconstituter
{
    /**
     * @param Contract $contract
     * @param DomainEvents $domainEvents
     * @return object
     */
    public function reconstitute(Contract $contract, DomainEvents $domainEvents)
    {
        $className = $contract->toClassName();
        // @todo check if class exists and implements ReconstitutesFromHistory
        return $className::reconstituteFrom($domainEvents);
    }
}
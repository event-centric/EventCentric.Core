<?php

namespace EventCentric\UnitOfWork;

use EventCentric\AggregateRoot\ReconstitutesFromHistory;
use EventCentric\AggregateRoot\TracksChanges;
use EventCentric\Contracts\Contract;
use EventCentric\Identifiers\Identifier;

/**
 * A Unit of Work will keep track of one or more Aggregates.
 * When the Unit of Work is committed, the changes will be persisted using a single commit for each Aggregate.
 * A UnitOfWork can also reconstitute an Aggregate from the Event Store.
 */
interface TracksAggregates
{
    /**
     * Track a newly created AggregateRoot
     *
     * @param Contract      $aggregateContract
     * @param Identifier    $aggregateId
     * @param TracksChanges $aggregateRoot
     *
     * @throws AggregateRootIsAlreadyBeingTracked
     */
    public function track(Contract $aggregateContract, Identifier $aggregateId, TracksChanges $aggregateRoot);

    /**
     * @param Contract   $aggregateContract
     * @param Identifier $aggregateId
     *
     * @return ReconstitutesFromHistory
     */
    public function get(Contract $aggregateContract, Identifier $aggregateId);

    /**
     * Persist each tracked Aggregate.
     */
    public function commit();
}

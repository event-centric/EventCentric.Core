<?php

namespace EventCentric\Persistence;

use EventCentric\Contracts\Contract;
use EventCentric\EventStore\CommitId;
use EventCentric\EventStore\EventEnvelope;
use EventCentric\Identifiers\Identifier;

/**
 * A concrete instance of Persistence is required by EventStore; each EventStream will
 * then use this instance to persist new events & to fetch a history of events.
 */
interface Persistence
{
    /**
     * @param Contract $streamContract
     * @param Identifier $streamId
     * @return EventEnvelope[]
     */
    public function fetch(Contract $streamContract, Identifier $streamId);

    /**
     * @param CommitId $commitId
     * @param Contract $streamContract
     * @param Identifier $streamId
     * @param $expectedStreamRevision
     * @param EventEnvelope[] $eventEnvelopes
     * @return void
     */
    public function commit(
        CommitId $commitId,
        Contract $streamContract,
        Identifier $streamId,
        $expectedStreamRevision,
        array $eventEnvelopes
    );
}

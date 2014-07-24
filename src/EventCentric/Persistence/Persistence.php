<?php

namespace EventCentric\Persistence;

use EventCentric\Contracts\Contract;
use EventCentric\EventStore\CommitId;
use EventCentric\EventStore\EventEnvelope;
use EventCentric\Identity\Identity;

/**
 * A concrete instance of Persistence is required by EventStore; each EventStream will
 * then use this instance to persist new events & to fetch a history of events.
 * @package EventCentric\Persistence
 */
interface Persistence
{
    /**
     * @param Contract $streamContract
     * @param Identity $streamId
     * @return EventEnvelope[]
     */
    public function fetch(Contract $streamContract, Identity $streamId);

    /**
     * @param CommitId $commitId
     * @param Contract $streamContract
     * @param Identity $streamId
     * @param $expectedStreamRevision
     * @param EventEnvelope[] $eventEnvelopes
     * @return void
     */
    public function commit(
        CommitId $commitId,
        Contract $streamContract,
        Identity $streamId,
        $expectedStreamRevision,
        array $eventEnvelopes
    );
}
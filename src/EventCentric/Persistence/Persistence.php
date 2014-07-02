<?php

namespace EventCentric\Persistence;

use EventCentric\Contracts\Contract;
use EventCentric\EventStore\CommitId;
use EventCentric\EventStore\EventEnvelope;
use EventCentric\Identity\Identity;

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
     * @param EventEnvelope[] $eventEnvelopes
     * @return void
     */
    public function commit(CommitId $commitId, Contract $streamContract, Identity $streamId, array $eventEnvelopes);
}
<?php

namespace EventCentric\V2Persistence;

use EventCentric\Contracts\Contract;
use EventCentric\EventStore\CommitId;
use EventCentric\Identifiers\Identifier;
use EventCentric\V2EventStore\PendingEvent;

interface V2Persistence
{
    /**
     * @param CommitId $commitId
     * @param PendingEvent $pendingEvent
     * @return void
     */
    public function persist(CommitId $commitId, PendingEvent $pendingEvent);

    public function fetchFromStream(Contract $streamContract, Identifier $streamId);
}
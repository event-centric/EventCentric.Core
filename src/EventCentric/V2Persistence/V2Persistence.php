<?php

namespace EventCentric\V2Persistence;

use EventCentric\Contracts\Contract;
use EventCentric\Identifiers\Identifier;
use EventCentric\V2EventStore\CommittedEvent;
use EventCentric\V2EventStore\PendingEvent;

interface V2Persistence
{
    /**
     * Commit a single event
     * @param PendingEvent $pendingEvent
     * @return void
     */
    public function commit(PendingEvent $pendingEvent);

    /**
     * Commit a set of events in a transaction.
     * @param PendingEvent[] $pendingEvents
     * @return void
     */
    public function commitAll($pendingEvents);

    /**
     * @param Bucket $bucket
     * @param Contract $streamContract
     * @param Identifier $streamId
     * @return CommittedEvent[]
     */
    public function fetchFromStream(Bucket $bucket, Contract $streamContract, Identifier $streamId);

    /**
     * @return CommittedEvent[]
     */
    public function fetchAll();
}
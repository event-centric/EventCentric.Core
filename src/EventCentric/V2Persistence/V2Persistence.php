<?php

namespace EventCentric\V2Persistence;

use EventCentric\Contracts\Contract;
use EventCentric\Identifiers\Identifier;
use EventCentric\V2EventStore\CommittedEvent;
use EventCentric\V2EventStore\PendingEvent;

interface V2Persistence
{
    /**
     * @param PendingEvent $pendingEvent
     * @return void
     */
    public function commit(PendingEvent $pendingEvent);

    /**
     * @param PendingEvent[] $pendingEvents
     * @return CommittedEvent[]
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
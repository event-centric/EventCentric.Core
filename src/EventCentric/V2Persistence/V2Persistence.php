<?php

namespace EventCentric\V2Persistence;

use EventCentric\Contracts\Contract;
use EventCentric\EventStore\EventId;
use EventCentric\Identifiers\Identifier;
use EventCentric\Persistence\OptimisticConcurrencyFailed;
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
     * @throws OptimisticConcurrencyFailed
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

    public function delete(EventId $eventId);

    public function deleteStream(Bucket $bucket, Contract $streamContract, Identifier $streamId);
}
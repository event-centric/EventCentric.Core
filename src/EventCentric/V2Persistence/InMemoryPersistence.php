<?php

namespace EventCentric\V2Persistence;

use DateTimeImmutable;
use EventCentric\Contracts\Contract;
use EventCentric\EventStore\CommitId;
use EventCentric\Identifiers\Identifier;
use EventCentric\V2EventStore\CommittedEvent;
use EventCentric\V2EventStore\PendingEvent;

final class InMemoryPersistence implements V2Persistence
{
    private $storage = [];

    public function commit(PendingEvent $pendingEvent)
    {
        $streamRevision = 0;
        $checkpointNumber = 1;
        $commitSequence = 0;
        $dispatched = false;
        $committedEvent = new CommittedEvent(
            $pendingEvent->getEventId(),
            $pendingEvent->getBucket(),
            $pendingEvent->getStreamContract(),
            $pendingEvent->getStreamId(),
            $pendingEvent->getEventContract(),
            $pendingEvent->getEventPayload(),
            $pendingEvent->getEventMetadata(),
            $pendingEvent->getEventMetadataContract(),
            $pendingEvent->getCausationId(),
            $pendingEvent->getCorrelationId(),
            $streamRevision,
            $checkpointNumber,
            CommitId::generate(),
            $commitSequence,
            new DateTimeImmutable(),
            $dispatched
        );

        $this->storage[] = $committedEvent;
    }

    /**
     * @param Bucket $bucket
     * @param Contract $streamContract
     * @param Identifier $streamId
     * @return CommittedEvent[]
     */
    public function fetchFromStream(Bucket $bucket, Contract $streamContract, Identifier $streamId)
    {
        $callback = function (CommittedEvent $event) use ($bucket, $streamContract, $streamId) {
            return
                   $bucket->equals($event->getBucket())
                && $streamContract->equals($event->getStreamContract())
                && $streamId->equals($event->getStreamId());
        };

        return array_values(
            array_filter(
                $this->storage,
                $callback
            )
        );
    }

}
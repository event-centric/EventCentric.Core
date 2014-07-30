<?php

namespace EventCentric\V2Persistence;

use DateTimeImmutable;
use EventCentric\Contracts\Contract;
use EventCentric\EventStore\CommitId;
use EventCentric\Identifiers\Identifier;
use EventCentric\V2EventStore\CommittedEvent;
use EventCentric\V2EventStore\PendingEvent;
use Assert;

final class InMemoryPersistence implements V2Persistence
{
    private $storage = [];

    public function commit(PendingEvent $pendingEvent)
    {
        $streamRevision = 0;
        $checkpointNumber = 1;
        $commitSequence = 0;
        $dispatched = false;
        $commitId = CommitId::generate();
        $this->commitAs($commitId, $pendingEvent, $streamRevision, $checkpointNumber, $commitSequence, $dispatched);
    }

    /**
     * @param PendingEvent[] $pendingEvents
     * @return CommittedEvent[]
     */
    public function commitAll($pendingEvents)
    {
        Assert\that($pendingEvents)->all()->isInstanceOf(PendingEvent::class);

        $streamRevision = 0;
        $checkpointNumber = 1;
        $commitSequence = 0;
        $dispatched = false;
        $commitId = CommitId::generate();

        foreach($pendingEvents as $pendingEvent) {
            $this->commitAs($commitId, $pendingEvent, $streamRevision, $checkpointNumber, $commitSequence, $dispatched);
        }
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

    /**
     * @return CommittedEvent[]
     */
    public function fetchAll()
    {
        return array_values($this->storage);
    }

    /**
     * @param CommitId $commitId
     * @param PendingEvent $pendingEvent
     * @param $streamRevision
     * @param $checkpointNumber
     * @param $commitSequence
     * @param $dispatched
     */
    private function commitAs(
        CommitId $commitId,
        PendingEvent $pendingEvent,
        $streamRevision,
        $checkpointNumber,
        $commitSequence,
        $dispatched
    ) {
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
            $commitId,
            $commitSequence,
            new DateTimeImmutable(),
            $dispatched
        );

        $this->storage[] = $committedEvent;
    }
}
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

    private $lastCheckPointNumber = 0;

    public function commit(PendingEvent $pendingEvent)
    {
        $this->commitAll([$pendingEvent]);
    }

    /**
     * @param PendingEvent[] $pendingEvents
     * @return void
     */
    public function commitAll($pendingEvents)
    {
        Assert\that($pendingEvents)->all()->isInstanceOf(PendingEvent::class);

        $commitSequence = 1;
        $dispatched = false;
        $commitId = CommitId::generate();

        foreach($pendingEvents as $pendingEvent) {
            $streamRevision = $this->getNextStreamRevisionFor($pendingEvent->getBucket(), $pendingEvent->getStreamContract(), $pendingEvent->getStreamId());
            $this->commitAs($commitId, $pendingEvent, ++$streamRevision, ++$this->lastCheckPointNumber, $commitSequence++, $dispatched);
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
            $pendingEvent->getEventMetadataContract(),
            $pendingEvent->getEventMetadata(),
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

    /**
     * @param Bucket $bucket
     * @param Contract $streamContract
     * @param Identifier $streamId
     * @return int
     */
    private function getNextStreamRevisionFor(Bucket $bucket, Contract $streamContract, Identifier $streamId)
    {
        $committedEvents = $this->fetchFromStream($bucket, $streamContract, $streamId);
        return count($committedEvents);
    }
}
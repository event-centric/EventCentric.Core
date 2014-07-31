<?php

namespace EventCentric\V2EventStore;

use DateTimeImmutable;
use EventCentric\Contracts\Contract;
use EventCentric\EventStore\CommitId;
use EventCentric\EventStore\EventId;
use EventCentric\Identifiers\Identifier;
use Assert;
use EventCentric\V2Persistence\Bucket;

final class CommittedEvent
{
    private $checkpointNumber;
    private $bucket;
    private $streamContract;
    private $eventContract;
    private $eventPayload;
    private $streamId;
    private $streamRevision;
    private $utcCommittedTime;
    private $eventMetadataContract;
    private $eventMetadata;
    private $causationId;
    private $correlationId;
    private $eventId;
    private $commitId;
    private $commitSequence;
    private $dispatched;

    public function __construct(
        EventId $eventId,
        Bucket $bucket,
        Contract $streamContract,
        Identifier $streamId,
        Contract $eventContract,
        $eventPayload,
        $eventMetadata = '',
        Contract $eventMetadataContract = null,
        Identifier $causationId = null,
        Identifier $correlationId = null,
        $streamRevision,
        $checkpointNumber,
        CommitId $commitId,
        $commitSequence,
        DateTimeImmutable $utcCommittedTime,
        $dispatched
    ) {
        Assert\that($eventPayload)->string();
        Assert\that($eventMetadata)->string();
        Assert\that($streamRevision)->integer()->min(0);
        Assert\that($checkpointNumber)->integer()->min(1);
        Assert\that($commitSequence)->integer()->min(0);
        Assert\that($dispatched)->boolean();

        $this->eventId = $eventId;
        $this->streamContract = $streamContract;
        $this->streamId = $streamId;
        $this->eventContract = $eventContract;
        $this->eventPayload = $eventPayload;
        $this->bucket = $bucket;
        $this->eventMetadata = $eventMetadata;
        $this->eventMetadataContract = $eventMetadataContract;
        $this->causationId = $causationId;
        $this->correlationId = $correlationId;
        $this->streamRevision = $streamRevision;
        $this->checkpointNumber = $checkpointNumber;
        $this->commitId = $commitId;
        $this->commitSequence = $commitSequence;
        $this->utcCommittedTime = $utcCommittedTime;
        $this->dispatched = $dispatched;
    }

    /**
     * @return Bucket
     */
    public function getBucket()
    {
        return $this->bucket;
    }

    /**
     * @return Identifier
     */
    public function getCausationId()
    {
        return $this->causationId;
    }

    /**
     * @return bool
     */
    public function hasCausationId()
    {
        return !is_null($this->causationId);
    }

    /**
     * @return int
     */
    public function getCheckpointNumber()
    {
        return $this->checkpointNumber;
    }

    /**
     * @return Identifier
     */
    public function getCommitId()
    {
        return $this->commitId;
    }

    /**
     * @return int
     */
    public function getCommitSequence()
    {
        return $this->commitSequence;
    }

    /**
     * @return Identifier
     */
    public function getCorrelationId()
    {
        return $this->correlationId;
    }

    /**
     * @return bool
     */
    public function hasCorrelationId()
    {
        return !is_null($this->correlationId);
    }

    /**
     * @return bool
     */
    public function isDispatched()
    {
        return $this->dispatched;
    }

    /**
     * @return Contract
     */
    public function getEventContract()
    {
        return $this->eventContract;
    }

    /**
     * @return EventId
     */
    public function getEventId()
    {
        return $this->eventId;
    }

    /**
     * @return string
     */
    public function getEventMetadata()
    {
        return $this->eventMetadata;
    }

    /**
     * @return Contract
     */
    public function getEventMetadataContract()
    {
        return $this->eventMetadataContract;
    }

    /**
     * @return bool
     */
    public function hasEventMetadataContract()
    {
        return !is_null($this->eventMetadataContract);
    }

    /**
     * @return string
     */
    public function getEventPayload()
    {
        return $this->eventPayload;
    }

    /**
     * @return Contract
     */
    public function getStreamContract()
    {
        return $this->streamContract;
    }

    /**
     * @return Identifier
     */
    public function getStreamId()
    {
        return $this->streamId;
    }

    /**
     * @return int
     */
    public function getStreamRevision()
    {
        return $this->streamRevision;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getUtcCommittedTime()
    {
        return $this->utcCommittedTime;
    }
}

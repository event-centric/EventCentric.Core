<?php

namespace EventCentric\V2EventStore;

use EventCentric\Contracts\Contract;
use EventCentric\EventStore\EventId;
use EventCentric\Identifiers\Identifier;
use Assert;
use EventCentric\V2Persistence\Bucket;

final class PendingEvent
{
    /**
     * @var Identifier
     */
    private $eventId;

    /**
     * @var int
     */
    private $expectedStreamRevision;

    /**
     * @var Bucket
     */
    private $bucket;

    /**
     * @var Contract
     */
    private $streamContract;

    /**
     * @var Contract
     */
    private $eventContract;

    /**
     * @var string
     */
    private $eventPayload;

    /**
     * @var Identifier
     */
    private $streamId;

    /**
     * @var Contract|null
     */
    private $eventMetadataContract;

    /**
     * @var string
     */
    private $eventMetadata = '';

    /**
     * @var Identifier|null
     */
    private $causationId;

    /**
     * @var Identifier|null
     */
    private $correlationId;

    /**
     * @param EventId $eventId
     * @param int $expectedStreamRevision
     * @param Bucket $bucket
     * @param Contract $streamContract
     * @param Identifier $streamId
     * @param Contract $eventContract
     * @param string $eventPayload
     */
    public function __construct(
        EventId $eventId,
        $expectedStreamRevision,
        Bucket $bucket,
        Contract $streamContract,
        Identifier $streamId,
        Contract $eventContract,
        $eventPayload
    ) {
        Assert\that($eventPayload)->string();
        Assert\that($expectedStreamRevision)->integer()->min(0);
        $this->eventId = $eventId;
        $this->streamContract = $streamContract;
        $this->streamId = $streamId;
        $this->eventContract = $eventContract;
        $this->eventPayload = $eventPayload;
        $this->bucket = $bucket;
        $this->expectedStreamRevision = $expectedStreamRevision;
    }

    /**
     * @return int
     */
    public function getExpectedStreamRevision()
    {
        return $this->expectedStreamRevision;
    }

    /**
     * @param string $eventMetadata
     */
    public function setEventMetadata($eventMetadata = '')
    {
        Assert\that($eventMetadata)->string();
        $this->eventMetadata = $eventMetadata;
    }

    /**
     * @param Contract|null $eventMetadataContract
     */
    public function setEventMetadataContract(Contract $eventMetadataContract = null)
    {
        $this->eventMetadataContract = $eventMetadataContract;
    }

    /**
     * @param Identifier|null $causationId
     */
    public function setCausationId(Identifier $causationId = null)
    {
        $this->causationId = $causationId;
    }

    /**
     * @param Identifier|null $correlationId
     */
    public function setCorrelationId(Identifier $correlationId = null)
    {
        $this->correlationId = $correlationId;
    }

    /**
     * @return Bucket
     */
    public function getBucket()
    {
        return $this->bucket;
    }

    /**
     * @return Identifier|null
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
     * @return Identifier|null
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
     * @return Contract|null
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
}
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
     * @var Contract
     */
    private $eventMetadataContract;

    /**
     * @var string
     */
    private $eventMetadata;

    /**
     * @var Identifier
     */
    private $causationId;

    /**
     * @var Identifier
     */
    private $correlationId;

    /**
     * @var Identifier
     */
    private $eventId;

    public function __construct(
        EventId $eventId,
        Bucket $bucket,
        Contract $streamContract,
        Identifier $streamId,
        Contract $eventContract,
        $eventPayload,
        Contract $eventMetadataContract = null,
        $eventMetadata = "",
        Identifier $causationId = null,
        Identifier $correlationId = null
    ) {
        Assert\that($eventMetadata)->string();
        Assert\that($eventPayload)->string();

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
}
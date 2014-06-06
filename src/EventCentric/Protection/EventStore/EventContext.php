<?php

namespace EventCentric\Protection\EventStore;

use EventCentric\DomainEvents\DomainEvent;
use EventCentric\Identity\Identity;
use EventCentric\Protection\EventStore\Contract;

final class EventContext
{
    /** @var EventId */
    private $eventId;
    /** @var DomainEvent */
    private $event;
    /** @var Contract */
    private $aggregateContract;
    /** @var Identity */
    private $streamId;

    public function __construct(Contract $aggregateContract, Identity $streamId, EventId $eventId, DomainEvent $event)
    {
        $this->eventId = $eventId;
        $this->aggregateContract = $aggregateContract;
        $this->event = $event;
        $this->streamId = $streamId;
    }

    /** @return DomainEvent */
    public function event()
    {
        return $this->event;
    }

    /** @return EventId */
    public function eventId()
    {
        return $this->eventId;
    }

    /** @return Contract */
    public function contract()
    {
        return $this->aggregateContract;
    }

    /**
     * @return Identity
     */
    public function streamId()
    {
        return $this->streamId;
    }


} 
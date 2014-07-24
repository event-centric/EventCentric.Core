<?php

namespace EventCentric\EventStore;

use EventCentric\Contracts\Contract;
use Assert;
use EventCentric\EventStore\EventId;

/**
 * A wrapper around a DomainEvent, containing metadata about that Event.
 * Comparable to a physical envelope, with an address on the outside, containing a message on the inside.
 */
final class EventEnvelope
{
    /**
     * @var EventId
     */
    private $eventId;

    /**
     * @var Contract
     */
    private $eventContract;

    /**
     * @var string
     */
    private $eventPayload;

    private function __construct(EventId $eventId, Contract $eventContract, $eventPayload)
    {
        Assert\that($eventPayload)->string();
        $this->eventId = $eventId;
        $this->eventContract = $eventContract;
        $this->eventPayload = $eventPayload;
    }

    /**
     * @param EventId $eventId
     * @param Contract $eventContract
     * @param string $eventPayload
     * @return EventEnvelope
     */
    public static function wrap(EventId $eventId, Contract $eventContract, $eventPayload)
    {
        return new EventEnvelope($eventId, $eventContract, $eventPayload);
    }

    /**
     * @todo check whether this method serves the same purpose as self::wrap().
     */
    public static function reconstitute(EventId $eventId, Contract $eventContract, $eventPayload)
    {
        return new EventEnvelope($eventId, $eventContract, $eventPayload);
    }

    /**
     * @return Contract
     */
    public function getEventContract()
    {
        return $this->eventContract;
    }

    /**
     * @return string
     */
    public function getEventPayload()
    {
        return $this->eventPayload;
    }

    /**
     * @return EventId
     */
    public function getEventId()
    {
        return $this->eventId;
    }

    public function equals(EventEnvelope $other)
    {
        return
            $this->eventId->equals($other->eventId)
            && $this->eventContract->equals($other->eventContract)
            && $this->eventPayload == $other->eventPayload;
    }
} 
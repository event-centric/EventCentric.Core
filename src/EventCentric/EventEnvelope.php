<?php

namespace EventCentric;

use EventCentric\Contracts\Contract;
use Assert;

/**
 * An EventEnvelope wraps a payload with a bunch of relevant information, so we can send it around.
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
     * @var $eventPayload
     */
    private $eventPayload;

    private function __construct(){}

    /**
     * @param EventId $eventId
     * @param Contract $eventContract
     * @param string $eventPayload
     * @return EventEnvelope
     */
    public static function wrap(EventId $eventId, Contract $eventContract, $eventPayload)
    {
        Assert\that($eventPayload)->string();
        $eventEnvelope = new EventEnvelope;
        $eventEnvelope->eventId = $eventId;
        $eventEnvelope->eventContract = $eventContract;
        $eventEnvelope->eventPayload = $eventPayload;
        return $eventEnvelope;
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


} 
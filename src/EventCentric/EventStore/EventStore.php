<?php

namespace EventCentric\EventStore;

use EventCentric\Contracts\Contract;
use EventCentric\EventStore\EventStream;
use EventCentric\Identifiers\Identifier;
use EventCentric\Persistence\Persistence;

/**
 * A history of all DomainEvents that have happened in the system.
 */
final class EventStore
{
    /**
     * @var \EventCentric\Persistence\Persistence
     */
    private $persistence;

    public function __construct(Persistence $persistence)
    {
        $this->persistence = $persistence;
    }

    /**
     * Creates a new stream.
     * @param Contract $streamContract
     * @param $streamId
     * @return EventStream
     */
    public function createStream(Contract $streamContract, Identifier $streamId)
    {
        return EventStream::create($this->persistence, $streamContract, $streamId);
    }

    /**
     * @param Contract $streamContract
     * @param Identifier $streamId
     * @return EventStream
     */
    public function openStream(Contract $streamContract, Identifier $streamId)
    {
        return EventStream::open($this->persistence, $streamContract, $streamId);
    }
}

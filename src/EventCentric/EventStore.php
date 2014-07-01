<?php


namespace EventCentric;

use EventCentric\Contracts\Contract;
use EventCentric\Identity\Identity;
use Exception;

final class EventStore
{
    /**
     * @var Persistence
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
    public function createStream(Contract $streamContract, Identity $streamId)
    {
        return EventStream::create($this->persistence, $streamContract, $streamId);

    }

    /**
     * @param Contract $streamContract
     * @param Identity $streamId
     * @return EventStream
     */
    public function openStream(Contract $streamContract, Identity $streamId)
    {
        return EventStream::open($this->persistence, $streamContract, $streamId);
    }
}

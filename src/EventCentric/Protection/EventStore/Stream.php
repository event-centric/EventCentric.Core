<?php


namespace EventCentric\Protection\EventStore;

use EventCentric\DomainEvents\DomainEvents;
use EventCentric\DomainEvents\Implementations\DomainEventsArray;
use EventCentric\Identity\Identity;
use EventCentric\Protection\EventStore\Contract;

final class Stream
{
    private $streamId;
    private $streamContract;
    private $eventContexts;

    public function __construct(Identity $streamId, Contract $streamContract, array $eventContexts)
    {
        $this->streamId = $streamId;
        $this->streamContract = $streamContract;
        $this->eventContexts = $eventContexts;
    }

    /**
     * @return DomainEvents
     */
    public function events()
    {
        return new DomainEventsArray(array_map(
            function(EventContext $eventContext) {return $eventContext->event();},
            $this->eventContexts
        ));
    }
}
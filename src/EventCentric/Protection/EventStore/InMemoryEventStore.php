<?php

namespace EventCentric\Protection\EventStore;

use EventCentric\Identity\Identity;
use EventCentric\Protection\EventStore\Contract;

final class InMemoryEventStore implements EventStore
{
    private $commits = [];
    private $eventContexts = [];

    public function commit(CommitId $commitId, array $eventContexts)
    {
        $this->commits[(string) $commitId] = $eventContexts;
        /** @var EventContext $eventContext */
        foreach($eventContexts as $eventContext) {
            $this->eventContexts[(string) $eventContext->eventId()] = $eventContext;
        }
    }

    /**
     * @param Contract $contract
     * @param Identity $streamId
     * @return Stream
     */
    public function getStreamByContract(Contract $contract, Identity $streamId)
    {
        $matchingContexts = array_filter(
            $this->eventContexts,
            function(EventContext $eventContext) use($contract, $streamId){
                return
                    $eventContext->contract()->equals($contract)
                    && $eventContext->streamId()->equals($streamId);
            }
        );

        return new Stream($streamId, $contract, $matchingContexts);
    }
}
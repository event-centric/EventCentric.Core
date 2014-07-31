<?php

namespace EventCentric\V2EventStore;

use EventCentric\Contracts\Contract;
use EventCentric\EventStore\CommitId;
use EventCentric\EventStore\EventEnvelope;
use EventCentric\Identifiers\Identifier;
use EventCentric\Persistence\Persistence;

/**
 * A logical, cohesive sequence of DomainEvents, ordered chronologically by their
 * recorded date, identified by a stream contract and stream id.
 * Typically, an EventStream represents the history for a single Aggregate instance.
 */
final class EventStream
{
    /**
     * @var Contract
     */
    private $streamContract;

    /**
     * @var Identifier
     */
    private $streamId;

    /**
     * @var Persistence
     */
    private $persistence;
    private $eventEnvelopes;

    private function __construct(Persistence $persistence, Contract $streamContract, Identifier $streamId)
    {
        $this->streamContract = $streamContract;
        $this->streamId = $streamId;
        $this->persistence = $persistence;
    }

    /**
     * @param Persistence $persistence
     * @param Contract $streamContract
     * @param Identifier $streamId
     * @return EventStream
     */
    public static function open(Persistence $persistence, Contract $streamContract, Identifier $streamId)
    {
        $eventStream = new EventStream($persistence, $streamContract, $streamId);
        $eventStream->eventEnvelopes = $persistence->fetch($streamContract, $streamId);
        return $eventStream;
    }

    /**
     * @param PendingEventEnvelope $eventEnvelope
     * @return void
     */
    public function write(PendingEventEnvelope $eventEnvelope)
    {
        $this->persistence->commit(
            CommitId::generate(),
            $this->streamContract,
            $this->streamId,
            count($this->committedEventEnvelopes),
            $this->pendingEventEnvelopes
        );

        $this->committedEventEnvelopes = array_merge($this->committedEventEnvelopes, $this->pendingEventEnvelopes);
        $this->pendingEventEnvelopes = [];
    }

    /**
     * @param EventEnvelope[] $envelopes
     * @return void
     */
    public function appendAll(array $envelopes)
    {
        foreach($envelopes as $envelope) {
            $this->append($envelope);
        }
    }

    /**
     * @return EventEnvelope[]
     */
    public function all()
    {
        return array_merge($this->committedEventEnvelopes, $this->pendingEventEnvelopes);
    }

    public function commit(CommitId $commitId)
    {
        $this->persistence->commit(
            $commitId,
            $this->streamContract,
            $this->streamId,
            count($this->committedEventEnvelopes),
            $this->pendingEventEnvelopes
        );

        $this->committedEventEnvelopes = array_merge($this->committedEventEnvelopes, $this->pendingEventEnvelopes);
        $this->pendingEventEnvelopes = [];
    }
}

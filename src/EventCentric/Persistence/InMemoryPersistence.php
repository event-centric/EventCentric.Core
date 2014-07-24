<?php

namespace EventCentric\Persistence;

use DateTimeImmutable;
use EventCentric\Contracts\Contract;
use EventCentric\EventStore\CommitId;
use EventCentric\EventStore\EventEnvelope;
use EventCentric\Identifiers\Identifier;
use EventCentric\Persistence\Persistence;
use iter as _;
use iter\fn as __;

/**
 * An implementation of Persistence that stores Events in-memory & only at runtime.
 * This is useful to use during testing.
 * @package EventCentric\Persistence
 */
final class InMemoryPersistence implements Persistence
{
    /**
     * @var InMemoryRecord[]
     */
    private $records = [];

    /**
     * @param Contract $streamContract
     * @param Identifier $streamId
     * @return EventEnvelope[]
     */
    public function fetch(Contract $streamContract, Identifier $streamId)
    {
        return
            _\toArray(
                _\map(
                    $this->toEventEnvelope(),
                    _\filter(
                        $this->belongsToStream($streamContract, $streamId),
                        $this->records
                    )
                )
            )
            ;
    }

    /**
     * @param CommitId $commitId
     * @param Contract $streamContract
     * @param Identifier $streamId
     * @param int $expectedStreamRevision
     * @param EventEnvelope[] $eventEnvelopes
     * @throws OptimisticConcurrencyFailed
     */
    public function commit(
        CommitId $commitId,
        Contract $streamContract,
        Identifier $streamId,
        $expectedStreamRevision,
        array $eventEnvelopes
    )
    {
        $actualStreamRevision = $this->revisionFor($streamContract, $streamId);
        if($actualStreamRevision != $expectedStreamRevision) {
            throw OptimisticConcurrencyFailed::revisionDoNotMatch($expectedStreamRevision, $actualStreamRevision);
        }

        foreach ($eventEnvelopes as $eventEnvelope) {
            $record = new InMemoryRecord();
            $record->checkpointNumber = "todo";
            $record->streamContract = $streamContract;
            $record->streamId = $streamId;
            $record->streamRevision = "todo";
            $record->eventContract = $eventEnvelope->getEventContract();
            $record->eventPayload = $eventEnvelope->getEventPayload();
            $record->utcCommittedTime = new DateTimeImmutable();
            $record->eventId = $eventEnvelope->getEventId();
            $record->commitId = $commitId;
            $this->records[]  = $record;
        }
    }

    private function maximum($numbers)
    {
        return _\reduce(
            function($acc, $x) { return $x > $acc ? $x : $acc;},
            $numbers,
            0
        );
    }

    private function revisionFor(Contract $streamContract, Identifier $streamId)
    {
        $pluckStreamRevision = __\property('streamRevision');

        return $this->maximum(
            _\map(
                $pluckStreamRevision,
                _\filter(
                    $this->belongsToStream($streamContract, $streamId),
                    $this->records
                )
            )
        );
    }


    private function toEventEnvelope()
    {
        return function(InMemoryRecord $record) {
            return EventEnvelope::reconstitute($record->eventId, $record->eventContract, $record->eventPayload);
        };
    }


    private function belongsToStream(Contract $streamContract, Identifier $streamId)
    {
        return function (InMemoryRecord $record) use ($streamContract, $streamId) {
            return $record->streamContract->equals($streamContract) && $record->streamId->equals($streamId);
        };
    }
}

final class InMemoryRecord
{
    public $checkpointNumber;
    public $bucket = '@default';
    /** @var Contract */
    public $streamContract;
    public $eventContract;
    public $eventPayload;
    /** @var Identifier */
    public $streamId;
    public $streamRevision;
    public $utcCommittedTime;
    public $eventMetadataContract = '';
    public $eventMetadata = '';
    public $causationId = null;
    public $correlationId = null;
    public $eventId;
    public $commitId;
    public $commitSequence;
    public $dispatched;
}


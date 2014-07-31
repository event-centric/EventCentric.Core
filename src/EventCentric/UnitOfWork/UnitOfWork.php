<?php

namespace EventCentric\UnitOfWork;

use EventCentric\AggregateRoot\ReconstitutesFromHistory;
use EventCentric\AggregateRoot\TracksChanges;
use EventCentric\EventStore\CommitId;
use EventCentric\Contracts\Contract;
use EventCentric\DomainEvents\DomainEvent;
use EventCentric\DomainEvents\DomainEventsArray;
use EventCentric\EventStore\EventEnvelope;
use EventCentric\EventStore\EventId;
use EventCentric\EventStore\EventStore;
use EventCentric\Identifiers\Identifier;
use EventCentric\Serializer\DomainEventSerializer;
use iter as _;
use iter\fn as __;

/**
 * A Unit of Work will keep track of one or more Aggregates.
 * When the Unit of Work is committed, the changes will be persisted using a single commit for each Aggregate.
 * A UnitOfWork can also reconstitute an Aggregate from the Event Store.
 */
final class UnitOfWork
{
    /**
     * @var \EventCentric\EventStore\EventStore
     */
    private $eventStore;

    /**
     * @var DomainEventSerializer
     */
    private $serializer;

    /**
     * @var AggregateRootReconstituter
     */
    private $aggregateRootReconstituter;

    private $trackedAggregates  = [];

    public function __construct(
        EventStore $eventStore,
        DomainEventSerializer $serializer,
        AggregateRootReconstituter $aggregateRootReconstituter
    )
    {
        $this->eventStore = $eventStore;
        $this->serializer = $serializer;
        $this->aggregateRootReconstituter = $aggregateRootReconstituter;
    }

    /**
     * Track a newly created AggregateRoot
     *
     * @param Contract $aggregateContract
     * @param Identifier $aggregateId
     * @param TracksChanges $aggregateRoot
     * @throws AggregateRootIsAlreadyBeingTracked
     */
    public function track(Contract $aggregateContract, Identifier $aggregateId, TracksChanges $aggregateRoot)
    {
        $aggregate = new Aggregate($aggregateContract, $aggregateId, $aggregateRoot);

        $alreadyTracked =
            _\any(
                function(Aggregate $foundAggregate) use($aggregate){ return $aggregate->equals($foundAggregate); },
                $this->trackedAggregates
            );

        if($alreadyTracked) {
            throw AggregateRootIsAlreadyBeingTracked::identifiedBy($aggregateContract, $aggregateId);
        }

        $this->trackedAggregates[] = $aggregate;
    }

    /**
     * @param Contract $aggregateContract
     * @param Identifier $aggregateId
     * @return ReconstitutesFromHistory
     */
    public function get(Contract $aggregateContract, Identifier $aggregateId)
    {
        $aggregate = $this->findTrackedAggregate($aggregateContract, $aggregateId);

        if(!is_null($aggregate)) {
            return $aggregate->getAggregateRoot();
        }

        $aggregateRoot = $this->findPersistedAggregateRoot($aggregateContract, $aggregateId);
        $this->track($aggregateContract, $aggregateId, $aggregateRoot);

        return $aggregateRoot;
    }

    /**
     * Persist each tracked Aggregate.
     */
    public function commit()
    {
        /** @var Aggregate $aggregate */
        foreach($this->trackedAggregates as $aggregate) {
            if($aggregate->hasChanges()) {
                $this->persistAggregate($aggregate);
            }
        }
    }

    /**
     * @param Aggregate $aggregate
     * @todo happens if there are no changes to an Aggregate?
     */
    private function persistAggregate(Aggregate $aggregate)
    {
        $stream = $this->eventStore->createStream($aggregate->getAggregateContract(), $aggregate->getAggregateId());

        $domainEvents = $aggregate->getChanges();

        $wrapInEnvelope = function (DomainEvent $domainEvent) {
            // @todo The unit of work shouldn't know how to get contracts. Move to Serializer
            $eventContract = Contract::canonicalFrom($domainEvent);
            $payload = $this->serializer->serialize($eventContract, $domainEvent);
            return EventEnvelope::wrap(EventId::generate(), $eventContract, $payload);
        };

        $envelopes = $domainEvents->map($wrapInEnvelope);

        $stream->appendAll($envelopes);
        // @todo share commitId across aggregates
        $stream->commit(CommitId::generate());
        $aggregate->clearChanges();
    }

    /**
     * @param Contract $aggregateContract
     * @param Identifier $aggregateId
     * @return Aggregate
     */
    private function findTrackedAggregate(Contract $aggregateContract, Identifier $aggregateId)
    {
        $aggregates = _\toArray(
            _\filter(
                function (Aggregate $aggregate) use ($aggregateContract, $aggregateId) {
                    return $aggregate->isIdentifiedBy($aggregateContract, $aggregateId);
                },
                $this->trackedAggregates
            )
        );

        // ugh I'm missing all kinds of FP shizzle to make this pretty. Maybe later.
        /** @var Aggregate $aggregate */
        $aggregate = count($aggregates)
            ? $aggregates[0]
            : null;
        return $aggregate;
    }

    /**
     * @param Contract $aggregateContract
     * @param Identifier $aggregateId
     * @return mixed
     */
    private function findPersistedAggregateRoot(Contract $aggregateContract, Identifier $aggregateId)
    {
        $streamId = $aggregateId;
        $stream = $this->eventStore->openOrCreateStream($aggregateContract, $streamId);

        $eventEnvelopes = $stream->all();

        $unwrapFromEnvelope = function (EventEnvelope $eventEnvelope) {
            $domainEvent = $this->serializer->unserialize(
                $eventEnvelope->getEventContract(),
                $eventEnvelope->getEventPayload()
            );
            return $domainEvent;
        };

        $domainEvents = new DomainEventsArray(
            array_map($unwrapFromEnvelope, $eventEnvelopes)
        );

        $aggregateRoot = $this->aggregateRootReconstituter->reconstitute($aggregateContract, $domainEvents);
        return $aggregateRoot;
    }
}

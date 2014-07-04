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
use EventCentric\Identity\Identity;
use EventCentric\Serializer\DomainEventSerializer;

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

    private $trackedAggregateRoots  = [];

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
     * @param Identity $aggregateId
     * @param TracksChanges $aggregateRoot
     * @throws AggregateRootIsAlreadyBeingTracked
     */
    public function track(Contract $aggregateContract, Identity $aggregateId, TracksChanges $aggregateRoot)
    {
        $trackingKey = "$aggregateContract::$aggregateId";
        if(array_key_exists($trackingKey, $this->trackedAggregateRoots)) {
            throw AggregateRootIsAlreadyBeingTracked::identifiedBy($aggregateContract, $aggregateId);
        }
        $this->trackedAggregateRoots[$trackingKey] = $aggregateRoot;


        $streamId = $aggregateId;
        $streamContract = $aggregateContract;
        $stream = $this->eventStore->createStream($streamContract, $streamId);

        $domainEvents = $aggregateRoot->getChanges();


        $wrapInEnvelope = function (DomainEvent $domainEvent) {
            $eventContract = Contract::canonicalFrom(get_class($domainEvent));
            $payload = $this->serializer->serialize($eventContract, $domainEvent);
            return EventEnvelope::wrap(EventId::generate(), $eventContract, $payload);
        };


        $envelopes = $domainEvents->map($wrapInEnvelope);

        $stream->appendAll($envelopes);
        $stream->commit(CommitId::generate());

    }

    /**
     * @param Contract $aggregateContract
     * @param Identity $aggregateId
     * @return ReconstitutesFromHistory
     */
    public function get(Contract $aggregateContract, Identity $aggregateId)
    {
        $streamId = $aggregateId;
        $stream = $this->eventStore->openStream($aggregateContract, $streamId);

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
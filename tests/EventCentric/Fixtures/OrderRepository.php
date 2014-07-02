<?php

namespace EventCentric\Fixtures;

use EventCentric\AggregateRoot\AggregateRoot;
use EventCentric\CommitId;
use EventCentric\Contracts\Contract;
use EventCentric\DomainEvents\DomainEvent;
use EventCentric\DomainEvents\DomainEventsArray;
use EventCentric\EventEnvelope;
use EventCentric\EventId;
use EventCentric\EventStore;
use EventCentric\Identity\Identity;
use EventCentric\Serializer\DomainEventSerializer;
use EventCentric\UnitOfWork\AggregateRootReconstituter;

final class OrderRepository
{
    /**
     * @var EventStore
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

    public function __construct(
        EventStore $eventStore,
        DomainEventSerializer $domainEventSerializer,
        AggregateRootReconstituter $aggregateRootReconstituter
    )
    {
        $this->eventStore = $eventStore;
        $this->serializer = $domainEventSerializer;
        $this->aggregateRootReconstituter = $aggregateRootReconstituter;
    }

    public function add(Order $order)
    {
        $streamId = $this->extractAggregateId($order);
        $stream = $this->eventStore->createStream($this->getContract(), $streamId);

        $domainEvents = $order->getChanges();


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
     * @param OrderId $orderId
     * @return Order
     */
    public function get(OrderId $orderId)
    {
        $streamId = $orderId;
        $stream = $this->eventStore->openStream($this->getContract(), $streamId);

        $eventEnvelopes = $stream->all();

        $unwrapFromEnvelope = function (EventEnvelope $eventEnvelope) {
            $domainEvent = $this->serializer->unserialize($eventEnvelope->getEventContract(), $eventEnvelope->getEventPayload());
            return $domainEvent;
        };

        $domainEvents = new DomainEventsArray(
            array_map($unwrapFromEnvelope, $eventEnvelopes)
        );

        return $this->aggregateRootReconstituter->reconstitute($this->getContract(), $domainEvents);
    }

    /**
     * @param AggregateRoot $aggregateRoot
     * @return Identity
     */
    private function extractAggregateId(AggregateRoot $aggregateRoot)
    {
        return $aggregateRoot->getOrderId();
    }

    /**
     * @return Contract
     */
    private function getContract()
    {
        return Contract::canonicalFrom(Order::class);
    }


}
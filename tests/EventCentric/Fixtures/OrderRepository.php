<?php

namespace EventCentric\Fixtures;

use EventCentric\CommitId;
use EventCentric\Contracts\Contract;
use EventCentric\DomainEvents\DomainEvent;
use EventCentric\DomainEvents\DomainEventsArray;
use EventCentric\EventEnvelope;
use EventCentric\EventId;
use EventCentric\EventStore;
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
     * @var Contract
     */
    private $contract;

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
        $this->contract = Contract::canonicalFrom(Order::class);
        $this->aggregateRootReconstituter = $aggregateRootReconstituter;
    }

    public function add(Order $order)
    {
        $streamId = $order->getOrderId();
        $stream = $this->eventStore->createStream($this->contract, $streamId);

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
        $stream = $this->eventStore->openStream($this->contract, $streamId);

        $eventEnvelopes = $stream->all();

        $unwrapFromEnvelope = function (EventEnvelope $eventEnvelope) {
            $domainEvent = $this->serializer->unserialize($eventEnvelope->getEventContract(), $eventEnvelope->getEventPayload());
            return $domainEvent;
        };

        $domainEvents = new DomainEventsArray(
            array_map($unwrapFromEnvelope, $eventEnvelopes)
        );

        return $this->aggregateRootReconstituter->reconstitute($this->contract, $domainEvents);
    }



}
<?php

namespace EventCentric\Fixtures;

use EventCentric\CommitId;
use EventCentric\Contracts\Contract;
use EventCentric\DomainEvents\DomainEvent;
use EventCentric\DomainEvents\Implementations\DomainEventsArray;
use EventCentric\EventEnvelope;
use EventCentric\EventId;
use EventCentric\EventStore;
use EventCentric\Serializer\DomainEventSerializer;

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

    public function __construct(EventStore $eventStore, DomainEventSerializer $domainEventSerializer)
    {
        $this->eventStore = $eventStore;
        $this->serializer = $domainEventSerializer;
        $this->contract = Contract::with(Order::class);
    }

    public function add(Order $order)
    {
        $streamId = $order->getOrderId();
        $stream = $this->eventStore->createStream($this->contract, $streamId);

        $domainEvents = $order->getChanges();


        $wrapInEnvelope = function (DomainEvent $domainEvent) {
            $eventContract = Contract::with(get_class($domainEvent));
            $payload = $this->serializer->serialize($eventContract, $domainEvent);
            return EventEnvelope::wrap(EventId::generate(), $eventContract, $payload);
        };


        $envelopes = $domainEvents->map($wrapInEnvelope);

        $stream->appendAll($envelopes);
        $stream->commit(CommitId::generate());
    }


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

        return Order::reconstituteFrom($domainEvents);
    }



}
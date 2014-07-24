<?php

namespace EventCentric\Serializer;

use EventCentric\Contracts\Contract;
use EventCentric\DomainEvents\DomainEvent;

/**
 * Transforms a DomainEvent into a string, and vice versa using a Contract to decide
 * how the string should be interpreted.
 * Domain Events are serialized & wrapped in an EventEnvelope ready for persisting.
 * @package EventCentric\Serializer
 */
interface DomainEventSerializer
{
    /**
     * @param Contract $contract
     * @param DomainEvent $domainEvent
     * @return string
     */
    public function serialize(Contract $contract, DomainEvent $domainEvent);

    /**
     * @param \EventCentric\Contracts\Contract $contract
     * @param string $data
     * @return DomainEvent
     */
    public function unserialize(Contract $contract, $data);
}
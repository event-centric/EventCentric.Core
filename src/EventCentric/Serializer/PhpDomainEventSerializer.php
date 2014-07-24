<?php

namespace EventCentric\Serializer;

use EventCentric\Contracts\Contract;
use EventCentric\DomainEvents\DomainEvent;

/**
 * Serialize Domain Events using PHP's serialize()/unserialize() functions.
 */
final class PhpDomainEventSerializer implements DomainEventSerializer
{
    public function serialize(Contract $contract, DomainEvent $domainEvent)
    {
        return serialize($domainEvent);
    }

    /**
     * @param \EventCentric\Contracts\Contract $contract
     * @param $data
     * @return DomainEvent
     */
    public function unserialize(Contract $contract, $data)
    {
        return unserialize($data);
    }
} 
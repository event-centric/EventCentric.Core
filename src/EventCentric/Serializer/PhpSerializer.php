<?php

namespace EventCentric\Serializer;

use EventCentric\Contract;
use EventCentric\DomainEvents\DomainEvent;

final class PhpSerializer implements Serializer
{
    public function serialize(DomainEvent $domainEvent)
    {
        return serialize($domainEvent);
    }

    /**
     * @param Contract $contract
     * @param $data
     * @return DomainEvent
     */
    public function unserialize(Contract $contract, $data)
    {
        return unserialize($data);
    }
} 
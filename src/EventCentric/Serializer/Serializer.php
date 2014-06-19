<?php

namespace EventCentric\Serializer;

use EventCentric\Contract;
use EventCentric\DomainEvents\DomainEvent;

interface Serializer
{
    public function serialize(DomainEvent $domainEvent);

    /**
     * @param Contract $contract
     * @param $data
     * @return DomainEvent
     */
    public function unserialize(Contract $contract, $data);
}
<?php

namespace EventCentric\Serializer;

use EventCentric\Contracts\Contract;
use EventCentric\DomainEvents\DomainEvent;

interface Serializer
{
    public function serialize(Contract $contract, DomainEvent $domainEvent);

    /**
     * @param \EventCentric\Contracts\Contract $contract
     * @param $data
     * @return DomainEvent
     */
    public function unserialize(Contract $contract, $data);
}
<?php

namespace EventCentric\UnitOfWork;

use EventCentric\Contracts\Contract;
use EventCentric\DomainEvents\DomainEvents;

interface AggregateRootReconstituter
{
    public function reconstitute(Contract $contract, DomainEvents $domainEvents);
}
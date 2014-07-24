<?php

namespace EventCentric\UnitOfWork;

use EventCentric\Contracts\Contract;
use EventCentric\Identifiers\Identifier;
use Exception;

final class AggregateRootIsAlreadyBeingTracked  extends Exception
{
    public static function identifiedBy(Contract $aggregateContract, Identifier $aggregateId)
    {
        $message = sprintf("The AggregateRoot [%s] with id [%s] was already added to the Unit of Work. You can only track newly created AggregateRoots.", $aggregateContract, $aggregateId);
        return new AggregateRootIsAlreadyBeingTracked($message);
    }
}
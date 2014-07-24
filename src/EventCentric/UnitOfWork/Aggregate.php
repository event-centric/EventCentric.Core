<?php

namespace EventCentric\UnitOfWork;

use EventCentric\AggregateRoot\TracksChanges;
use EventCentric\Contracts\Contract;
use EventCentric\DomainEvents\DomainEvents;
use EventCentric\Identity\Identity;

/**
 * A shell around an AggregateRoot that stores all infrastructural information such as aggregateId and aggregateContract
 * @package EventCentric\UnitOfWork
 */
final class Aggregate
{
    /**
     * @var Contract
     */
    private $aggregateContract;

    /**
     * @var Identity
     */
    private $aggregateId;

    /**
     * @var TracksChanges
     */
    private $aggregateRoot;

    public function __construct(Contract $aggregateContract, Identity $aggregateId, TracksChanges $aggregateRoot)
    {

        $this->aggregateContract = $aggregateContract;
        $this->aggregateId = $aggregateId;
        $this->aggregateRoot = $aggregateRoot;
    }

    /**
     * @return Contract
     */
    public function getAggregateContract()
    {
        return $this->aggregateContract;
    }

    /**
     * @return Identity
     */
    public function getAggregateId()
    {
        return $this->aggregateId;
    }

    /**
     * @return TracksChanges
     */
    public function getAggregateRoot()
    {
        return $this->aggregateRoot;
    }

    /**
     * @param Aggregate $other
     * @return bool
     */
    public function equals(Aggregate $other)
    {
        return $this->isIdentifiedBy($other->aggregateContract, $other->aggregateId);
    }

    /**
     * @return DomainEvents
     */
    public function getChanges()
    {
        return $this->aggregateRoot->getChanges();
    }

    /**
     * @param Contract $aggregateContract
     * @param Identity $aggregateId
     * @return bool
     */
    public function isIdentifiedBy(Contract $aggregateContract, Identity $aggregateId)
    {
        return
            $this->aggregateContract->equals($aggregateContract)
            && $this->aggregateId->equals($aggregateId);
    }


} 
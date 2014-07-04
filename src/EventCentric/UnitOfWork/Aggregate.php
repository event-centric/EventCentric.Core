<?php

namespace EventCentric\UnitOfWork;

use EventCentric\AggregateRoot\TracksChanges;
use EventCentric\Contracts\Contract;
use EventCentric\Identity\Identity;

/**
 * A shell around an AggregateRoot that stores all infrastructural information such as aggregateId and aggregateContract
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
        return
            $this->aggregateContract->equals($other->aggregateContract)
            && $this->aggregateId->equals($other->aggregateId);
    }

    public function getChanges()
    {
        return $this->aggregateRoot->getChanges();
    }


} 
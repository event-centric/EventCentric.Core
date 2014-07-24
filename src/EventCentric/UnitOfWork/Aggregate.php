<?php

namespace EventCentric\UnitOfWork;

use EventCentric\AggregateRoot\TracksChanges;
use EventCentric\Contracts\Contract;
use EventCentric\DomainEvents\DomainEvents;
use EventCentric\Identifiers\Identifier;

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
     * @var Identifier
     */
    private $aggregateId;

    /**
     * @var TracksChanges
     */
    private $aggregateRoot;

    public function __construct(Contract $aggregateContract, Identifier $aggregateId, TracksChanges $aggregateRoot)
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
     * @return Identifier
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

    public function clearChanges()
    {
        $this->aggregateRoot->clearChanges();
    }

    /**
     * @param Contract $aggregateContract
     * @param Identifier $aggregateId
     * @return bool
     */
    public function isIdentifiedBy(Contract $aggregateContract, Identifier $aggregateId)
    {
        return
            $this->aggregateContract->equals($aggregateContract)
            && $this->aggregateId->equals($aggregateId);
    }


} 
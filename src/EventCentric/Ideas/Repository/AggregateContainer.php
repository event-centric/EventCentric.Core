<?php

namespace EventCentric\Aggregates\Repository;

use EventCentric\Aggregates\AggregateRoot\AggregateRoot;

final class AggregateContainer
{
    /**
     * @var AggregateIdentifier
     */
    private $aggregateIdentity;

    /**
     * @var AggregateRoot
     */
    private $aggregateRoot;

    public function __construct(AggregateIdentifier $aggregateIdentity, AggregateRoot $aggregateRoot)
    {
        $this->aggregateIdentity = $aggregateIdentity;
        $this->aggregateRoot = $aggregateRoot;
    }

    /** @return AggregateRoot */
    public function getAggregateRoot()
    {
        return $this->aggregateRoot;
    }

    /**
     * @return \EventCentric\Aggregates\Repository\AggregateIdentifier
     */
    public function getAggregateIdentity()
    {
        return $this->aggregateIdentity;
    }

    /** @return bool */
    public function hasChanges()
    {
        return $this->getAggregateRoot()->hasChanges();
    }

} 
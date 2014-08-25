<?php

namespace EventCentric\Tests\Fixtures;

use EventCentric\AggregateRoot\AggregateRoot;
use EventCentric\Contracts\Contract;
use EventCentric\EventStore\EventStore;
use EventCentric\Identifiers\Identifier;
use EventCentric\UnitOfWork\UnitOfWork;
use EventCentric\UnitOfWork\TracksAggregates;

final class OrderRepository
{
    /**
     * @var TracksAggregates
     */
    private $unitOfWork;

    public function __construct(TracksAggregates $unitOfWork)
    {
        $this->unitOfWork = $unitOfWork;
    }

    public function add(Order $order)
    {
        $this->unitOfWork->track(
            $this->getContract(),
            $this->extractAggregateId($order),
            $order
        );
    }

    /**
     * @param OrderId $orderId
     * @return Order
     */
    public function get(OrderId $orderId)
    {
        return $this->unitOfWork->get($this->getContract(), $orderId);
    }

    /**
     * @param AggregateRoot $aggregateRoot
     * @return Identifier
     */
    private function extractAggregateId(AggregateRoot $aggregateRoot)
    {
        return $aggregateRoot->getOrderId();
    }

    /**
     * @return Contract
     */
    private function getContract()
    {
        return Contract::canonicalFrom(Order::class);
    }
}

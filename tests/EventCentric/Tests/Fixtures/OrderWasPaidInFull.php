<?php

namespace EventCentric\Tests\Fixtures;

use EventCentric\DomainEvents\DomainEvent;

final class OrderWasPaidInFull implements DomainEvent
{
    /**
     * @var OrderId
     */
    private $orderId;

    public function __construct(OrderId $orderId)
    {
        $this->orderId = $orderId;
    }

    /**
     * @return OrderId
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

} 